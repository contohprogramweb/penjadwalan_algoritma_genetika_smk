<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profile Controller - Mengelola profil user
 */
class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Cek apakah user sudah login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        
        $this->load->model('User_model', 'user');
        $this->load->library('form_validation');
    }

    /**
     * Tampilkan halaman profil user
     */
    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->user->get_by_id($user_id);
        $data['page_title'] = 'Profil Saya';
        $data['breadcrumbs'] = ['Profile' => ''];
        
        // Render konten profil ke variabel
        $data['content'] = $this->load->view('profile/index', $data, TRUE);
        
        // Load layout yang sesuai dengan role
        $role = $this->session->userdata('role');
        if ($role === 'admin') {
            $this->load->view('layouts/header', $data);
            $this->load->view('profile/index', $data);
            $this->load->view('layouts/footer');
        } else {
            $this->load->view('layouts/main', $data);
            $this->load->view('layouts/footer');
        }
    }

    /**
     * Update profil user
     */
    public function update()
    {
        // Pastikan hanya request AJAX yang diproses
        if (!$this->input->is_ajax_request()) {
            redirect('profile');
        }

        $this->output->set_content_type('application/json');
        
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan.'
            ]));
            return;
        }

        $user_id = $this->session->userdata('user_id');
        
        // Validasi input
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        
        // Password hanya wajib jika diisi
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'trim|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors('<li>', '</li>');
            $this->output->set_status_header(400)->set_output(json_encode([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => strip_tags($errors)
            ]));
            return;
        }

        try {
            $data = [
                'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
                'email' => $this->input->post('email', TRUE)
            ];

            // Jika password diisi, update password
            $password = $this->input->post('password', TRUE);
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $this->user->update($user_id, $data);

            // Update session username jika berubah
            $this->session->set_userdata('username', $data['nama_lengkap']);

            // Kembalikan token CSRF terbaru untuk rotasi token
            $csrf_data = [
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'csrf_hash' => $this->security->get_csrf_hash()
            ];

            $this->output->set_status_header(200)->set_output(json_encode($csrf_data));

        } catch (Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            $this->output->set_status_header(500)->set_output(json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan hubungi administrator.',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]));
        }
    }
}
