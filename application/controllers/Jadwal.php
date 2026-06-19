<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Jadwal (Public API)
 * Deskripsi: Endpoint publik untuk cek konflik (bisa dipanggil dari berbagai controller).
 * Referensi SRS: Bab 15.2 (Format Response AJAX)
 */
require_once APPPATH . 'core/MY_Controller.php';

class Jadwal extends MY_Controller {
    
    protected $allowed_roles = ['waka', 'admin', 'guru'];

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Jadwal_model');
        $this->load->model('Penugasan_model');
        $this->load->model('Ruangan_model');
        $this->load->model('Kelas_model');
        $this->load->model('Tahun_ajaran_model');
    }

    /**
     * Method: index()
     * Deskripsi: Halaman lihat jadwal - redirect ke waka/jadwal
     * Route: /jadwal
     */
    public function index()
    {
        redirect('waka/jadwal');
    }

    /**
     * Method: check_conflict()
     * Deskripsi: AJAX POST untuk mengecek konflik pada perubahan slot.
     * Ini adalah endpoint alternatif yang bisa dipanggil dari mana saja.
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

        $konflik = [];

        // Ambil detail jadwal saat ini
        $current = $this->Jadwal_model->get_jadwal_detail_by_id($id_jadwal_detail);
        if (!$current) {
            echo json_encode([
                'status' => 'error',
                'konflik' => [['constraint' => 'UNKNOWN', 'pesan' => 'Data jadwal tidak ditemukan.']]
            ]);
            return;
        }

        $hari = $current->hari;
        $slot = $current->slot;
        $id_kelas = $current->id_kelas;

        // Ambil info penugasan baru
        $penugasan = $this->Penugasan_model->get_by_id($id_penugasan_baru);
        if (!$penugasan) {
            echo json_encode([
                'status' => 'error',
                'konflik' => [['constraint' => 'UNKNOWN', 'pesan' => 'Data penugasan tidak ditemukan.']]
            ]);
            return;
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

        // HC-02: Kelas tidak double-booked
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
            echo json_encode([
                'status' => 'conflict',
                'konflik' => $konflik
            ]);
        } else {
            echo json_encode([
                'status' => 'ok',
                'konflik' => []
            ]);
        }
    }
}
