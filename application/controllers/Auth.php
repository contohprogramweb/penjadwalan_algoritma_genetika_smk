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
    }

    /**
     * Tampilkan halaman login
     * Sesuai SRS Bab 11.4 - Layout card 420px
     */
    public function login()
    {
        // Setup CSRF token untuk form
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $data['csrf_name']  = $this->security->get_csrf_token_name();

        $this->load->view('auth/login', $data);
    }

    /**
     * Proses login - validasi username/password dan set session
     * Sesuai SRS Bab 16.2 - Payload session
     *
     * CATATAN: Route 'auth/proses_login' sudah di-exclude dari CSRF protection
     * di config/config.php ($config['csrf_exclude_uris']), sehingga CSRF tidak
     * perlu dan tidak boleh di-verify manual di sini.
     */
    public function proses_login()
    {
        // Pastikan response JSON
        $this->output->set_content_type('application/json');

        // Pastikan request adalah POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Method tidak diizinkan.'
                ]));
            return;
        }

        // Ambil input dari POST
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        // Validasi input (server-side per SRS Bab 13)
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors('<li>', '</li>');
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => strip_tags($errors)
                ]));
            return;
        }

        try {
            // Load user model
            $this->load->model('User_model', 'user');
            $user = $this->user->get_by_username($username);

            if ( ! $user) {
                $this->output
                    ->set_status_header(401)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Username atau password salah.'
                    ]));
                return;
            }

            // Verifikasi password
            if ( ! password_verify($password, $user->password)) {
                $this->output
                    ->set_status_header(401)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Username atau password salah.'
                    ]));
                return;
            }

            // Cek apakah user aktif
            if ( ! isset($user->is_active) || $user->is_active != 1) {
                $this->output
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Akun Anda nonaktif. Hubungi administrator.'
                    ]));
                return;
            }

            // Regenerate session ID untuk keamanan (SRS Bab 16.2)
            $this->session->sess_regenerate(TRUE);

            // Set session payload LENGKAP sesuai SRS Bab 16.2
            $session_data = [
                'logged_in'    => TRUE,
                'user_id'      => $user->id,
                'username'     => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role'         => $user->role,
                'id_guru'      => isset($user->id_guru)  ? $user->id_guru  : NULL,
                'id_kelas'     => isset($user->id_kelas) ? $user->id_kelas : NULL,
                'email'        => isset($user->email)    ? $user->email    : NULL,
                'nip'          => isset($user->nip)      ? $user->nip      : NULL,
                'last_login'   => date('Y-m-d H:i:s'),
                'login_time'   => time(),
            ];

            $this->session->set_userdata($session_data);

            // Update kolom last_login di database
            $this->user->update_last_login($user->id);

            // Return success response
            $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'success'      => true,
                    'message'      => 'Login berhasil',
                    'redirect_url' => site_url($this->_get_redirect_url($user->role))
                ]));

        } catch (Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());

            $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server. Silakan hubungi administrator.'
                ]));
        }
    }

    /**
     * Logout - destroy session dan redirect ke login
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login', 'refresh');
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /**
     * Dapatkan URL redirect berdasarkan role
     *
     * @param  string $role
     * @return string
     */
    private function _get_redirect_url($role)
    {
        switch ($role) {
            case 'admin':
                return 'admin/dashboard';
            case 'waka':
                return 'waka/dashboard';
            case 'guru':
                return 'guru/dashboard';
            default:
                return 'auth/login';
        }
    }

    /**
     * Redirect berdasarkan role user yang sedang login
     */
    private function _redirect_by_role()
    {
        $role = $this->session->userdata('role');
        redirect($this->_get_redirect_url($role), 'refresh');
    }
}
