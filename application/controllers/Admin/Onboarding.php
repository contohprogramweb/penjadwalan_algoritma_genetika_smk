<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Onboarding Controller
 * 
 * Sesuai SRS Bab 11.9 - Onboarding Wizard 6 Langkah
 * - Redirect ke wizard jika users.first_login = TRUE atau data master kosong
 * - 6 langkah: Tahun Ajaran → Guru → Mapel → Kelas & Ruangan → Jam → Penugasan
 */
require_once APPPATH . 'core/MY_Controller.php';

class Onboarding extends MY_Controller
{
    protected $allowed_roles = ['admin'];

    /**
     * Daftar langkah wizard dengan konfigurasi
     */
    private $steps = [
        1 => [
            'id' => 'tahun_ajaran',
            'title' => 'Tahun Ajaran',
            'icon' => 'fa-calendar',
            'description' => 'Setup tahun ajaran aktif untuk periode penjadwalan'
        ],
        2 => [
            'id' => 'guru',
            'title' => 'Data Guru',
            'icon' => 'fa-chalkboard-teacher',
            'description' => 'Input data guru yang akan mengajar'
        ],
        3 => [
            'id' => 'mapel',
            'title' => 'Mata Pelajaran',
            'icon' => 'fa-book',
            'description' => 'Daftar mata pelajaran yang tersedia'
        ],
        4 => [
            'id' => 'kelas_ruangan',
            'title' => 'Kelas & Ruangan',
            'icon' => 'fa-school',
            'description' => 'Setup kelas dan ruangan untuk pembelajaran'
        ],
        5 => [
            'id' => 'jam_pelajaran',
            'title' => 'Jam Pelajaran',
            'icon' => 'fa-clock',
            'description' => 'Konfigurasi jam pelajaran per hari'
        ],
        6 => [
            'id' => 'penugasan',
            'title' => 'Penugasan Guru',
            'icon' => 'fa-clipboard-list',
            'description' => 'Assign guru ke mata pelajaran dan kelas'
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        
        // Load model yang diperlukan (sesuai nama class yang ada)
        $this->load->model('Tahun_ajaran_model', 'tahun_ajaran_model');
        $this->load->model('Guru_model', 'guru_model');
        $this->load->model('Mapel_model', 'mapel_model');
        $this->load->model('Kelas_model', 'kelas_model');
        $this->load->model('Ruangan_model', 'ruangan_model');
        $this->load->model('Jam_model', 'jam_model');
        // Penugasan dan activity log optional - akan dihandle jika ada
    }

    /**
     * Halaman utama wizard onboarding
     */
    public function index($step = 1)
    {
        // Validasi step
        $step = (int) $step;
        if ($step < 1 || $step > 6) {
            $step = 1;
        }

        // Cek apakah boleh skip wizard
        $can_skip = $this->_check_can_skip_wizard();

        // Ambil status kelengkapan setiap step
        $step_status = $this->_get_step_status();

        // Data untuk view
        $data = [
            'page_title' => 'Setup Awal Sistem',
            'current_step' => $step,
            'total_steps' => 6,
            'steps' => $this->steps,
            'step_status' => $step_status,
            'can_skip' => $can_skip,
            'breadcrumbs' => [
                'home' => site_url('admin/dashboard'),
                'setup_awal' => site_url('admin/onboarding'),
                $this->steps[$step]['title'] => ''
            ],
            'css_files' => [],
            'js_files' => ['assets/js/pages/onboarding.js']
        ];

        $this->load->view('layouts/main', $data);
        $this->load->view('admin/onboarding', $data);
    }

    /**
     * API: Cek status kelengkapan data per step
     */
    public function api_step_status()
    {
        $step_status = $this->_get_step_status();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $step_status
            ]));
    }

    /**
     * API: Simpan progress wizard
     */
    public function api_save_progress()
    {
        $this->load->helper('security');
        
        $step = $this->input->post('step');
        $completed = $this->input->post('completed');
        
        if (!$step || !isset($this->steps[$step])) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_code(400)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Step tidak valid'
                ]));
            return;
        }

        // Simpan progress ke session atau database
        $progress = $this->session->userdata('onboarding_progress') ?: [];
        $progress[$step] = [
            'completed' => (bool) $completed,
            'completed_at' => $completed ? date('Y-m-d H:i:s') : null
        ];
        $this->session->set_userdata('onboarding_progress', $progress);

        // Jika semua step selesai, update first_login user
        if ($completed && $this->_all_steps_completed()) {
            $this->_complete_onboarding();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Progress tersimpan',
                'all_completed' => $this->_all_steps_completed()
            ]));
    }

    /**
     * API: Skip wizard dan tandai sebagai selesai
     */
    public function api_skip_wizard()
    {
        $this->_complete_onboarding();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'redirect_url' => site_url('admin/dashboard')
            ]));
    }

    /**
     * Redirect berdasarkan kondisi first_login atau data kosong
     * Dipanggil dari auth controller setelah login
     */
    public static function check_and_redirect($CI)
    {
        // Cek first_login flag
        $first_login = $CI->session->userdata('first_login');
        
        if ($first_login === true || $first_login === '1' || $first_login === 1) {
            redirect('admin/onboarding', 'refresh');
            return true;
        }

        // Cek apakah data master masih kosong
        $CI->load->model('Tahun_ajaran_model', 'tahun_ajaran_model');
        $CI->load->model('Guru_model', 'guru_model');
        $CI->load->model('Mapel_model', 'mapel_model');

        $tahun_ajaran_count = $CI->tahun_ajaran_model->count_all();
        $guru_count = $CI->guru_model->count_all();
        $mapel_count = $CI->mapel_model->count_all();

        // Jika salah satu kosong, wajib onboarding
        if ($tahun_ajaran_count == 0 || $guru_count == 0 || $mapel_count == 0) {
            redirect('admin/onboarding', 'refresh');
            return true;
        }

        return false;
    }

    /**
     * Cek status kelengkapan setiap step
     */
    private function _get_step_status()
    {
        return [
            1 => [
                'completed' => $this->tahun_ajaran_model->count_active() > 0,
                'count' => $this->tahun_ajaran_model->count_all(),
                'required' => true
            ],
            2 => [
                'completed' => $this->guru_model->count_all() >= 1,
                'count' => $this->guru_model->count_all(),
                'required' => true,
                'min_required' => 1
            ],
            3 => [
                'completed' => $this->mapel_model->count_all() >= 1,
                'count' => $this->mapel_model->count_all(),
                'required' => true,
                'min_required' => 1
            ],
            4 => [
                'completed' => ($this->kelas_model->count_all() >= 1) && ($this->ruangan_model->count_all() >= 1),
                'kelas_count' => $this->kelas_model->count_all(),
                'ruangan_count' => $this->ruangan_model->count_all(),
                'required' => true,
                'min_required' => 1
            ],
            5 => [
                'completed' => $this->jam_model->count_all() >= 1,
                'count' => $this->jam_model->count_all(),
                'required' => true,
                'min_required' => 1
            ],
            6 => [
                'completed' => false, // Penugasan optional, selalu false jika model tidak ada
                'count' => 0,
                'required' => false, // Optional, bisa di-skip
                'min_required' => 0
            ]
        ];
    }

    /**
     * Cek apakah wizard boleh di-skip
     */
    private function _check_can_skip_wizard()
    {
        // Hanya boleh skip jika minimal data dasar sudah ada
        $status = $this->_get_step_status();
        
        // Step 1-3 wajib diisi
        for ($i = 1; $i <= 3; $i++) {
            if (!$status[$i]['completed']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah semua step required sudah selesai
     */
    private function _all_steps_completed()
    {
        $status = $this->_get_step_status();
        
        foreach ($status as $step_data) {
            if ($step_data['required'] && !$step_data['completed']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tandai onboarding sebagai selesai
     */
    private function _complete_onboarding()
    {
        // Update user first_login flag
        $user_id = $this->session->userdata('user_id');
        $this->load->model('User_model', 'user_model');
        
        // Cek apakah method update ada parameter yang sesuai
        $this->user_model->update($user_id, ['first_login' => 0]);

        // Clear session flags
        $this->session->set_userdata('first_login', 0);
        $this->session->unset_userdata('onboarding_progress');

        // Log activity (optional - jika model tidak ada, skip)
        if (method_exists($this, 'activity_log_model')) {
            try {
                $this->load->model('Activity_log_model', 'activity_log_model');
                $this->activity_log_model->insert([
                    'user_id' => $user_id,
                    'action' => 'complete_onboarding',
                    'description' => 'Admin menyelesaikan onboarding wizard',
                    'ip_address' => $this->input->ip_address(),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } catch (Exception $e) {
                // Ignore jika activity log tidak ada
                log_message('debug', 'Activity log not available: ' . $e->getMessage());
            }
        }
    }
}
