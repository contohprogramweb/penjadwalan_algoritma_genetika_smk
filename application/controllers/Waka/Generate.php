<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Waka Kurikulum - Generate Jadwal Controller
 * 
 * Sesuai SRS Bab 11.7, 15.3, 16.5:
 * - index(): Tampilkan halaman generate (ringkasan data + checklist + tombol)
 * - trigger(): AJAX POST: validasi kesiapan data, jalankan GA_Scheduler
 * - progress(): endpoint GET JSON untuk polling progress
 * 
 * @author Waka Kurikulum System
 * @version 1.0
 */
require_once APPPATH . 'core/MY_Controller.php';

class Generate extends MY_Controller
{
    protected $allowed_roles = ['waka'];

    // Key session untuk tracking progress
    private $session_key_generating = 'ga_generating';
    private $session_key_progress = 'ga_progress';

    public function __construct()
    {
        parent::__construct();
        
        // Load model yang diperlukan
        $this->load->model('Penugasan_model');
        $this->load->model('Tahun_ajaran_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Ruangan_model');
        $this->load->model('Mapel_model');
        $this->load->model('Jam_model');
        
        // Load library GA Scheduler
        $this->load->library('GA_Scheduler');
    }

    /**
     * Halaman utama Generate Jadwal
     * State 1: Sebelum generate - tampilkan ringkasan data dan checklist
     * 
     * @return void
     */
    public function index()
    {
        // Dapatkan tahun ajaran aktif
        $tahun_ajaran_aktif = $this->Tahun_ajaran_model->get_active();
        
        if (!$tahun_ajaran_aktif) {
            $data['error_message'] = 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.';
            $data['checklist'] = [
                'tahun_ajaran' => false,
                'penugasan' => false,
                'guru' => false,
                'kelas' => false,
                'ruangan' => false,
                'jam_pelajaran' => false
            ];
            $data['ready'] = false;
        } else {
            $id_tahun_ajaran = $tahun_ajaran_aktif['id_tahun_ajaran'];
            
            // Hitung ringkasan data
            $data['tahun_ajaran'] = $tahun_ajaran_aktif;
            $data['summary'] = [
                'total_penugasan' => $this->Penugasan_model->count_by_tahun($id_tahun_ajaran),
                'total_guru' => $this->Guru_model->count_active(),
                'total_kelas' => $this->Kelas_model->count_all(),
                'total_ruangan' => $this->Ruangan_model->count_all(),
                'total_jam' => $this->Jam_model->count_all()
            ];
            
            // Validasi checklist kesiapan data (SRS Bab 11.6.2)
            $data['checklist'] = $this->_validate_checklist($id_tahun_ajaran);
            $data['ready'] = $this->_is_ready_to_generate($data['checklist']);
            $data['error_message'] = null;
        }
        
        // Set page title
        $data['page_title'] = 'Generate Jadwal Pelajaran';
        
        // Render view
        $this->load->view('layouts/main', [
            'content' => $this->load->view('waka/generate', $data, true),
            'page_title' => $data['page_title']
        ]);
    }

    /**
     * Trigger proses generate jadwal via AJAX
     * 
     * Validasi kesiapan data, lalu jalankan GA_Scheduler
     * Response JSON dengan status awal untuk memulai polling
     * 
     * @return void JSON response
     */
    public function trigger()
    {
        // Hanya terima POST
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        // Cek apakah sudah ada proses generating
        if ($this->session->userdata($this->session_key_generating)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Proses generate sedang berjalan. Harap tunggu hingga selesai.'
            ]);
            return;
        }

        // Dapatkan tahun ajaran aktif
        $tahun_ajaran = $this->Tahun_ajaran_model->get_active();
        
        if (!$tahun_ajaran) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada tahun ajaran aktif.'
            ]);
            return;
        }

        $id_tahun_ajaran = $tahun_ajaran['id_tahun_ajaran'];

        // Validasi checklist
        $checklist = $this->_validate_checklist($id_tahun_ajaran);
        
        if (!$this->_is_ready_to_generate($checklist)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data belum lengkap untuk generate jadwal.',
                'checklist' => $checklist
            ]);
            return;
        }

        // Set flag generating di session
        $progress_data = [
            'is_generating' => true,
            'persen' => 0,
            'generasi' => 0,
            'generasi_maks' => 500,
            'fitness_terbaik' => 0,
            'waktu_berjalan' => 0,
            'status' => 'initializing',
            'pesan' => 'Menyiapkan data dan memulai algoritma genetika...'
        ];
        
        $this->session->set_userdata($this->session_key_generating, true);
        $this->session->set_userdata($this->session_key_progress, $progress_data);

        // Jalankan GA di background menggunakan exec() atau simple async
        // Untuk deployment production, gunakan queue system (Redis/RabbitMQ)
        // Di sini kita gunakan teknik simple background process
        
        $base_url = site_url('waka/generate/run_process/' . $id_tahun_ajaran);
        
        // Gunakan curl untuk trigger background process
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout pendek agar tidak blocking
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_exec($ch);
        curl_close($ch);

        // Return response awal untuk memulai polling
        echo json_encode([
            'status' => 'started',
            'message' => 'Proses generate dimulai.',
            'progress' => $progress_data
        ]);
    }

    /**
     * Run proses GA di background (dipanggil via internal request)
     * 
     * @param int $id_tahun_ajaran ID tahun ajaran
     * @return void
     */
    public function run_process($id_tahun_ajaran)
    {
        // Ignore user abort untuk background process
        ignore_user_abort(true);
        set_time_limit(0);

        // Update status menjadi running
        $progress_data = $this->session->userdata($this->session_key_progress);
        $progress_data['status'] = 'running';
        $progress_data['pesan'] = 'Algoritma genetika sedang berjalan...';
        $this->session->set_userdata($this->session_key_progress, $progress_data);

        $start_time = microtime(true);

        try {
            // Jalankan GA Scheduler
            $result = $this->ga_scheduler->generate($id_tahun_ajaran, 500);

            $end_time = microtime(true);
            $waktu_total = round($end_time - $start_time, 2);

            if ($result['status'] === 'success') {
                // Simpan hasil ke session/database
                $this->session->set_userdata('last_generated_schedule', [
                    'schedule' => $result['schedule'],
                    'fitness' => $result['best_fitness'],
                    'generasi' => $result['generasi'],
                    'waktu' => $waktu_total,
                    'timestamp' => time()
                ]);

                // Update progress final
                $progress_data = [
                    'is_generating' => false,
                    'persen' => 100,
                    'generasi' => $result['generasi'],
                    'generasi_maks' => 500,
                    'fitness_terbaik' => round($result['best_fitness'], 2),
                    'waktu_berjalan' => $waktu_total,
                    'status' => 'completed',
                    'pesan' => 'Generate berhasil! Jadwal siap untuk direview.',
                    'conflicts_count' => count($result['conflicts'])
                ];
                
                log_message('info', "GA Success - Generasi: {$result['generasi']}, Fitness: {$result['best_fitness']}, Waktu: {$waktu_total}s");
            } else {
                // Gagal
                $progress_data = [
                    'is_generating' => false,
                    'persen' => 0,
                    'generasi' => 0,
                    'generasi_maks' => 500,
                    'fitness_terbaik' => 0,
                    'waktu_berjalan' => $waktu_total,
                    'status' => 'failed',
                    'pesan' => 'Generate gagal: ' . $result['message']
                ];
                
                log_message('error', "GA Failed - " . $result['message']);
            }
        } catch (Exception $e) {
            $progress_data = [
                'is_generating' => false,
                'persen' => 0,
                'generasi' => 0,
                'generasi_maks' => 500,
                'fitness_terbaik' => 0,
                'waktu_berjalan' => 0,
                'status' => 'error',
                'pesan' => 'Error: ' . $e->getMessage()
            ];
            
            log_message('error', "GA Exception - " . $e->getMessage());
        }

        // Clear flag generating dan update progress
        $this->session->unset_userdata($this->session_key_generating);
        $this->session->set_userdata($this->session_key_progress, $progress_data);
    }

    /**
     * Endpoint untuk polling progress (SRS Bab 15.3)
     * 
     * Response JSON:
     * {
     *   is_generating: bool,
     *   persen: int,
     *   generasi: int,
     *   generasi_maks: int,
     *   fitness_terbaik: float,
     *   waktu_berjalan: float,
     *   status: string,
     *   pesan: string
     * }
     * 
     * @return void JSON response
     */
    public function progress()
    {
        // Hanya terima GET
        if ($this->input->method() !== 'get') {
            show_error('Method not allowed', 405);
        }

        $progress = $this->session->userdata($this->session_key_progress);
        
        if (!$progress) {
            // Belum ada proses
            echo json_encode([
                'is_generating' => false,
                'persen' => 0,
                'generasi' => 0,
                'generasi_maks' => 0,
                'fitness_terbaik' => 0,
                'waktu_berjalan' => 0,
                'status' => 'idle',
                'pesan' => 'Belum ada proses generate.'
            ]);
            return;
        }

        // Update waktu berjalan jika masih generating
        if ($progress['is_generating']) {
            if (!isset($progress['start_time'])) {
                $progress['start_time'] = microtime(true);
                $this->session->set_userdata($this->session_key_progress, $progress);
            }
            $progress['waktu_berjalan'] = round(microtime(true) - $progress['start_time'], 1);
        }

        echo json_encode($progress);
    }

    /**
     * Reset progress (untuk cleanup setelah selesai/error)
     * 
     * @return void JSON response
     */
    public function reset_progress()
    {
        $this->session->unset_userdata($this->session_key_generating);
        $this->session->unset_userdata($this->session_key_progress);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Progress telah direset.'
        ]);
    }

    // =========================================================================
    // HELPER METHODS PRIVATE
    // =========================================================================

    /**
     * Validasi checklist kesiapan data (SRS Bab 11.6.2)
     * 
     * @param int $id_tahun_ajaran ID tahun ajaran
     * @return array Status checklist per item
     */
    private function _validate_checklist($id_tahun_ajaran)
    {
        return [
            'tahun_ajaran' => true, // Sudah ada karena aktif
            'penugasan' => $this->Penugasan_model->count_by_tahun($id_tahun_ajaran) > 0,
            'guru' => $this->Guru_model->count_active() > 0,
            'kelas' => $this->Kelas_model->count_all() > 0,
            'ruangan' => $this->Ruangan_model->count_all() > 0,
            'jam_pelajaran' => $this->Jam_model->count_all() > 0
        ];
    }

    /**
     * Cek apakah semua checklist sudah terpenuhi
     * 
     * @param array $checklist Array status checklist
     * @return bool True jika semua terpenuhi
     */
    private function _is_ready_to_generate($checklist)
    {
        foreach ($checklist as $status) {
            if (!$status) {
                return false;
            }
        }
        return true;
    }
}
