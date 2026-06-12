<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Dashboard Controller
 * 
 * Sesuai SRS Bab 16.1:
 * - Extend MY_Controller
 * - Set allowed_roles = ['admin']
 */
require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends MY_Controller
{
    protected $allowed_roles = ['admin'];

    public function __construct()
    {
        parent::__construct();
        
        // Load semua model yang diperlukan untuk dashboard dan layout
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('Ruangan_model');
        $this->load->model('Tahun_ajaran_model');
        $this->load->model('Jam_model');
        
        // Role sudah divalidasi di MY_Controller
        // Tambahan logic khusus admin jika diperlukan
    }

    /**
     * Halaman Dashboard Admin
     * TODO: Implementasi sesuai SRS Bab 11.6
     */
    public function index()
    {
        // Siapkan data statistik untuk dashboard
        $data = [
            'total_guru' => $this->Guru_model->count_all(),
            'total_kelas' => $this->Kelas_model->count_all(),
            'total_mapel' => $this->Mapel_model->count_all(),
            'total_ruangan' => $this->Ruangan_model->count_all(),
            'total_tahun_ajaran' => $this->Tahun_ajaran_model->count_all(),
            'total_jam' => $this->Jam_model->count_all(),
            'page_title' => 'Dashboard Admin'
        ];
        
        // Load view dashboard dengan layout yang sudah lengkap
        $this->load->view('admin/dashboard', $data);
    }
}
