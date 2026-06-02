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
    }

    /**
     * Halaman Dashboard Admin
     * TODO: Implementasi sesuai SRS Bab 11.6
     */
    public function index()
    {
        // Placeholder untuk dashboard admin
        // Akan diimplementasikan terpisah
        echo '<h1>Admin Dashboard</h1>';
        echo '<p>Selamat datang, ' . $this->session->userdata('nama_lengkap') . '</p>';
        echo '<p>Role: ' . $this->session->userdata('role') . '</p>';
        echo '<a href="' . site_url('auth/logout') . '">Logout</a>';
    }
}
