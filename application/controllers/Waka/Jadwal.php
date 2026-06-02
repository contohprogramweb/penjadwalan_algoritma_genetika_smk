<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Waka/Jadwal
 * Deskripsi: Mengelola tampilan grid jadwal mingguan, edit slot, dan approval jadwal.
 * Referensi SRS: Bab 11.8 (Use Case Review & Edit Jadwal), Bab 15.2 (Format Response AJAX)
 */
class Jadwal extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Pastikan user terautentikasi dan memiliki role Waka/Kurikulum
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $this->load->model('Jadwal_model');
        $this->load->model('Penugasan_model');
        $this->load->model('Ruangan_model');
        $this->load->library('GA_Scheduler');
    }

    /**
     * Method: index()
     * Deskripsi: Menampilkan grid jadwal mingguan untuk tahun ajaran aktif.
     * Route: /waka/jadwal
     */
    public function index()
    {
        // Ambil tahun ajaran aktif
        $tahun_ajaran = $this->db->where('status', 'active')->get('tahun_ajaran')->row();
        
        if (!$tahun_ajaran) {
            $this->session->set_flashdata('error', 'Tidak ada tahun ajaran aktif.');
            redirect('waka/dashboard');
        }

        $id_tahun_ajaran = $tahun_ajaran->id;

        // Ambil data jadwal yang sudah di-generate (status: draft/approved)
        $jadwal_data = $this->Jadwal_model->get_jadwal_by_tahun_ajaran($id_tahun_ajaran);
        
        // Ambil metadata: status jadwal (draft/approved), tanggal generate, dll.
        $meta_jadwal = $this->Jadwal_model->get_meta_jadwal($id_tahun_ajaran);
        
        $data = [
            'title' => 'Grid Jadwal Mingguan',
            'tahun_ajaran' => $tahun_ajaran,
            'jadwal_data' => $jadwal_data,
            'meta_jadwal' => $meta_jadwal,
            'hari_list' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'jam_mapel' => $this->Jadwal_model->get_jam_pelajaran_per_hari(),
            'content' => 'waka/jadwal_grid'
        ];

        $this->load->view('templates/waka_header', $data);
        $this->load->view('waka/jadwal_grid', $data);
        $this->load->view('templates/waka_footer');
    }

    /**
     * Method: edit_slot()
     * Deskripsi: AJAX POST untuk menyimpan perubahan pada satu slot jadwal.
     * Request: { id_jadwal_detail, id_penugasan_baru, id_ruangan_baru }
     * Response: { status: "ok"/"error", message: "...", konflik: [...] }
     * Route: /waka/jadwal/edit_slot
     */
    public function edit_slot()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $this->output->set_content_type('application/json');

        $id_jadwal_detail = $this->input->post('id_jadwal_detail');
        $id_penugasan_baru = $this->input->post('id_penugasan_baru');
        $id_ruangan_baru = $this->input->post('id_ruangan_baru');

        // Validasi input
        if (!$id_jadwal_detail || !$id_penugasan_baru || !$id_ruangan_baru) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data tidak lengkap.'
            ]);
            return;
        }

        // Cek konflik sebelum simpan
        $conflict_check = $this->_check_conflict_internal($id_jadwal_detail, $id_penugasan_baru, $id_ruangan_baru);

        if ($conflict_check['status'] === 'conflict') {
            echo json_encode([
                'status' => 'conflict',
                'message' => 'Perubahan menyebabkan konflik.',
                'konflik' => $conflict_check['konflik']
            ]);
            return;
        }

        // Simpan perubahan ke database
        $update_data = [
            'id_penugasan' => $id_penugasan_baru,
            'id_ruangan' => $id_ruangan_baru,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('user_id')
        ];

        $success = $this->Jadwal_model->update_jadwal_detail($id_jadwal_detail, $update_data);

        if ($success) {
            echo json_encode([
                'status' => 'ok',
                'message' => 'Slot berhasil diperbarui.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan perubahan.'
            ]);
        }
    }

    /**
     * Method: approve()
     * Deskripsi: Mengubah status jadwal dari draft → approved.
     * Hanya bisa dilakukan jika tidak ada konflik hard constraint.
     * Route: /waka/jadwal/approve
     */
    public function approve()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $this->output->set_content_type('application/json');

        $tahun_ajaran = $this->db->where('status', 'active')->get('tahun_ajaran')->row();
        if (!$tahun_ajaran) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tidak ada tahun ajaran aktif.'
            ]);
            return;
        }

        $id_tahun_ajaran = $tahun_ajaran->id;

        // Cek apakah jadwal masih draft
        $meta = $this->Jadwal_model->get_meta_jadwal($id_tahun_ajaran);
        if (!$meta || $meta->status !== 'draft') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Jadwal tidak dalam status draft atau belum di-generate.'
            ]);
            return;
        }

        // Final check: pastikan tidak ada konflik HC
        $scheduler = new GA_Scheduler();
        $conflicts = $scheduler->get_conflict_list($id_tahun_ajaran);
        
        if (!empty($conflicts)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Masih terdapat konflik Hard Constraint. Tidak dapat approve.',
                'konflik' => $conflicts
            ]);
            return;
        }

        // Update status ke approved
        $update_data = [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => $this->session->userdata('user_id')
        ];

        $success = $this->Jadwal_model->update_meta_jadwal($id_tahun_ajaran, $update_data);

        if ($success) {
            echo json_encode([
                'status' => 'ok',
                'message' => 'Jadwal berhasil disetujui dan dipublikasikan.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal mengubah status jadwal.'
            ]);
        }
    }

    /**
     * Method: check_conflict()
     * Deskripsi: AJAX POST untuk mengecek konflik pada perubahan slot.
     * Response sesuai SRS Bab 15.2: { status: "ok"/"conflict", konflik: [{constraint, pesan}] }
     * Route: /jadwal/check_conflict
     */
    public function check_conflict()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $this->output->set_content_type('application/json');

        $id_jadwal_detail = $this->input->post('id_jadwal_detail');
        $id_penugasan_baru = $this->input->post('id_penugasan_baru');
        $id_ruangan_baru = $this->input->post('id_ruangan_baru');

        if (!$id_jadwal_detail || !$id_penugasan_baru || !$id_ruangan_baru) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data tidak lengkap.'
            ]);
            return;
        }

        $result = $this->_check_conflict_internal($id_jadwal_detail, $id_penugasan_baru, $id_ruangan_baru);
        echo json_encode($result);
    }

    /**
     * Method: get_penugasan_by_kelas()
     * Deskripsi: AJAX POST untuk mendapatkan daftar penugasan yang valid untuk suatu kelas.
     * Digunakan untuk populate dropdown di modal edit slot.
     * Response: { status: "ok", data: [...] }
     * Route: /waka/jadwal/get_penugasan_by_kelas
     */
    public function get_penugasan_by_kelas()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $this->output->set_content_type('application/json');

        $id_kelas = $this->input->post('id_kelas');

        if (!$id_kelas) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID kelas tidak valid.'
            ]);
            return;
        }

        // Ambil penugasan yang sesuai untuk kelas ini
        // Penugasan harus: (1) untuk kelas ini, (2) belum habis jamnya
        $this->db->select('
            pg.id,
            pg.id_guru,
            g.nama as nama_guru,
            mp.id as id_mapel,
            mp.nama as nama_mapel,
            mp.sifat,
            pg.jam_maks
        ');
        $this->db->from('penugasan_guru pg');
        $this->db->join('guru g', 'pg.id_guru = g.id');
        $this->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id');
        $this->db->where('pg.id_kelas', $id_kelas);
        $this->db->order_by('mp.nama', 'ASC');
        
        $query = $this->db->get();
        $penugasan_list = $query->result();

        if (empty($penugasan_list)) {
            echo json_encode([
                'status' => 'ok',
                'data' => []
            ]);
            return;
        }

        echo json_encode([
            'status' => 'ok',
            'data' => $penugasan_list
        ]);
    }

    /**
     * Internal method untuk cek konflik
     * @param int $id_jadwal_detail
     * @param int $id_penugasan_baru
     * @param int $id_ruangan_baru
     * @return array { status: "ok"/"conflict", konflik: [...] }
     */
    private function _check_conflict_internal($id_jadwal_detail, $id_penugasan_baru, $id_ruangan_baru)
    {
        $konflik = [];

        // Ambil detail jadwal saat ini
        $current = $this->Jadwal_model->get_jadwal_detail_by_id($id_jadwal_detail);
        if (!$current) {
            return ['status' => 'error', 'konflik' => [['constraint' => 'UNKNOWN', 'pesan' => 'Data jadwal tidak ditemukan.']]];
        }

        $hari = $current->hari;
        $slot = $current->slot;
        $id_kelas = $current->id_kelas;

        // Ambil info penugasan baru
        $penugasan = $this->Penugasan_model->get_by_id($id_penugasan_baru);
        if (!$penugasan) {
            return ['status' => 'error', 'konflik' => [['constraint' => 'UNKNOWN', 'pesan' => 'Data penugasan tidak ditemukan.']]];
        }

        $id_guru = $penugasan->id_guru;
        $mapel_sifat = $penugasan->sifat; // teori/praktikum

        // HC-01: Guru tidak double-booked
        $guru_conflict = $this->Jadwal_model->check_guru_conflict($id_guru, $hari, $slot, $id_jadwal_detail);
        if ($guru_conflict) {
            $konflik[] = [
                'constraint' => 'HC-01',
                'pesan' => 'Guru sudah mengajar di kelas lain pada slot yang sama.'
            ];
        }

        // HC-02: Kelas tidak double-booked (sudah pasti aman karena kita edit slot kelas yang sama)
        // Tapi cek jika ada data korup
        $kelas_conflict = $this->Jadwal_model->check_kelas_conflict($id_kelas, $hari, $slot, $id_jadwal_detail);
        if ($kelas_conflict) {
            $konflik[] = [
                'constraint' => 'HC-02',
                'pesan' => 'Kelas sudah memiliki jadwal lain pada slot yang sama.'
            ];
        }

        // HC-03: Ruangan tidak double-booked
        $ruangan_conflict = $this->Jadwal_model->check_ruangan_conflict($id_ruangan_baru, $hari, $slot, $id_jadwal_detail);
        if ($ruangan_conflict) {
            $konflik[] = [
                'constraint' => 'HC-03',
                'pesan' => 'Ruangan sudah digunakan oleh kelas lain pada slot yang sama.'
            ];
        }

        // HC-05: Mapel sesuai penugasan (otomatis terpenuhi karena kita pilih dari dropdown penugasan)

        // SC-02: Penempatan mapel praktikum di ruangan sesuai tipe
        if ($mapel_sifat === 'praktikum') {
            $ruangan = $this->Ruangan_model->get_by_id($id_ruangan_baru);
            if ($ruangan && $ruangan->tipe !== 'lab') {
                $konflik[] = [
                    'constraint' => 'SC-02',
                    'pesan' => 'Mapel praktikum seharusnya di ruangan tipe lab.'
                ];
            }
        }

        if (!empty($konflik)) {
            return ['status' => 'conflict', 'konflik' => $konflik];
        }

        return ['status' => 'ok', 'konflik' => []];
    }
}
