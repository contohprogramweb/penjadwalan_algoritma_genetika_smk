<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Guru Dashboard Controller
 *
 * Sesuai SRS:
 * - Extend MY_Controller
 * - Set allowed_roles = ['guru']
 */
require_once APPPATH . 'core/MY_Controller.php';

class Dashboard extends MY_Controller
{
    protected $allowed_roles = ['guru'];

    public function __construct()
    {
        parent::__construct();

        // Role sudah divalidasi di MY_Controller
        // Tambahan logic khusus guru jika diperlukan
    }

    /**
     * Halaman Dashboard Guru
     */
    public function index()
    {
        $this->load->model('Guru_model');
        
        $user_id = $this->session->userdata('user_id');
        $guru_data = $this->Guru_model->get_by_user_id($user_id);
        
        $data = [
            'guru_data'     => $guru_data,
            'page_title'    => 'Dashboard Guru',
        ];

        $this->load->view('guru/dashboard', $data);
    }
}
