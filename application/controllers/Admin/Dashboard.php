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
        
        // Role sudah divalidasi di MY_Controller
        // Tambahan logic khusus admin jika diperlukan
		
		 $this->load->model('Guru_model', 'guru_model');
        $this->load->model('Kelas_model', 'kelas_model');
        $this->load->model('Mapel_model', 'mapel_model');
        $this->load->model('Ruangan_model', 'ruangan_model');
        $this->load->model('Tahun_ajaran_model', 'tahun_ajaran_model');
        $this->load->model('Jam_model', 'jam_model');
		
    }

    /**
     * Halaman Dashboard Admin
     * TODO: Implementasi sesuai SRS Bab 11.6
     */
    public function index()
    {
        $data = [
            'total_guru' => $this->guru_model->count_all(),
            'total_kelas' => $this->kelas_model->count_all(),
            'total_mapel' => $this->mapel_model->count_all(),
            'total_ruangan' => $this->ruangan_model->count_all(),
            'total_tahun_ajaran' => $this->tahun_ajaran_model->count_all(),
            'total_jam' => $this->jam_model->count_all(),
            'page_title' => 'Dashboard Admin'
        ];

        // Load view dashboard dengan layout yang sudah lengkap
        $this->load->view('admin/dashboard', $data);
    }
}
