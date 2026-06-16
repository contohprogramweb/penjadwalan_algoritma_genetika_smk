<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Waka Kurikulum Dashboard Controller
 * 
 * Sesuai SRS Bab 16.1:
 * - Extend MY_Controller
 * - Set allowed_roles = ['waka']
 */
require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends MY_Controller
{
    protected $allowed_roles = ['waka'];

    public function __construct()
    {
        parent::__construct();
        
        // Role sudah divalidasi di MY_Controller
        // Tambahan logic khusus waka jika diperlukan
    }

    /**
     * Halaman Dashboard Waka Kurikulum
     */
    public function index()
    {
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('Penugasan_model');
        $this->load->model('Tahun_ajaran_model');

        $data = [
            'total_guru'        => $this->Guru_model->count_all(),
            'total_kelas'       => $this->Kelas_model->count_all(),
            'total_mapel'       => $this->Mapel_model->count_all(),
            'tahun_ajaran_aktif'=> $this->Tahun_ajaran_model->get_aktif(),
            'page_title'        => 'Dashboard Waka Kurikulum',
        ];

        $this->load->view('waka/dashboard', $data);
    }
}
