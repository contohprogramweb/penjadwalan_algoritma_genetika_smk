<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller - Menangani autentikasi user
 * 
 * Sesuai SRS Bab 11.4, 16.2:
 * - Method login: tampilkan form login
 * - Method proses_login: validasi & set session
 * - Method logout: destroy session
 */
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Jika sudah login, redirect sesuai role
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
        }
        
        // Load helper auth
        $this->load->helper('auth');
    }

    /**
     * Tampilkan halaman login
     * Sesuai SRS Bab 11.4 - Layout card 420px
     */
    public function login()
    {
        // Setup CSRF token untuk form
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        
        $this->load->view('auth/login', $data);
    }

    /**
     * Proses login - validasi username/password dan set session
     * Sesuai SRS Bab 16.2 - Payload session
     */
    public function proses_login()
    {
        // Validasi CSRF
        if ($this->security->csrf_verify() === FALSE) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Token keamanan kadaluarsa. Silakan refresh halaman.'
                ]));
            return;
        }

        // Ambil input dari POST
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        // Validasi input (client-side + server-side per SRS Bab 13)
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors('<li>', '</li>');
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => strip_tags($errors)
                ]));
            return;
        }

        // TODO: Ganti dengan model user yang sebenarnya
        // Contoh hardcoded untuk development (HAPUS di production)
        $this->load->model('user_model', 'user');
        $user = $this->user->get_by_username($username);

        if (!$user) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Username tidak ditemukan'
                ]));
            return;
        }

        // Verifikasi password
        if (!password_verify($password, $user->password)) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Password salah'
                ]));
            return;
        }

        // Cek apakah user aktif
        if ($user->is_active != 1) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Akun Anda nonaktif. Hubungi administrator.'
                ]));
            return;
        }

        // Regenerate session ID untuk keamanan (SRS Bab 16.2)
        $this->session->sess_regenerate(TRUE);

        // Set session payload LENGKAP sesuai SRS Bab 16.2
        // Payload wajib: logged_in, user_id, username, nama_lengkap, role, id_guru, id_kelas, email, last_login
        $session_data = [
            'logged_in' => TRUE,
            'user_id' => $user->id,
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'role' => $user->role, // 'admin' atau 'waka'
            'id_guru' => $user->id_guru ?? NULL, // Untuk role guru
            'id_kelas' => $user->id_kelas ?? NULL, // Untuk role wali kelas
            'email' => $user->email ?? NULL,
            'last_login' => date('Y-m-d H:i:s'),
            'nip' => $user->nip ?? NULL,
            'login_time' => time()
        ];

        $this->session->set_userdata($session_data);

        // Return success response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect_url' => site_url($this->_get_redirect_url($user->role))
            ]));
    }

    /**
     * Logout - destroy session dan redirect ke login
     */
    public function logout()
    {
        // Destroy semua session data
        $this->session->sess_destroy();
        
        // Redirect ke halaman login
        redirect('auth/login', 'refresh');
    }

    /**
     * Helper: Dapatkan URL redirect berdasarkan role
     * 
     * @param string $role
     * @return string
     */
    private function _get_redirect_url($role)
    {
        switch ($role) {
            case 'admin':
                return 'admin/dashboard';
            case 'waka':
                return 'waka/dashboard';
            default:
                return 'auth/login';
        }
    }

    /**
     * Helper: Redirect berdasarkan role user
     */
    private function _redirect_by_role()
    {
        $role = $this->session->userdata('role');
        redirect($this->_get_redirect_url($role), 'refresh');
    }
}
