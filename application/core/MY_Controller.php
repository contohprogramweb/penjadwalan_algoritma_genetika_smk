<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller - Base Controller untuk Autentikasi dan Otorisasi Role
 *
 * Sesuai SRS Bab 16.1:
 * - Cek session 'logged_in'
 * - Properti $allowed_roles untuk kontrol akses per role
 * - Redirect ke login jika belum authenticated
 * - Show error 403 jika role tidak sesuai
 */
class MY_Controller extends CI_Controller
{
    /**
     * Daftar role yang diizinkan mengakses controller ini.
     * Override di child controller.
     *
     * @var array
     */
    protected $allowed_roles = [];

    public function __construct()
    {
        parent::__construct();

        // CSRF verify hanya untuk POST/PUT/DELETE, bukan GET (SRS Bab 16.4)
        // CI3 CSRF protection sudah berjalan otomatis via config csrf_protection = TRUE
        // Jangan panggil csrf_verify() manual di sini karena akan memutus semua GET request.

        // Cek apakah user sudah login
        if ( ! $this->session->userdata('logged_in')) {
            redirect('auth/login', 'refresh');
            return;
        }

        // Validasi role user terhadap allowed_roles
        $user_role = $this->session->userdata('role');

        if ( ! empty($this->allowed_roles) && ! in_array($user_role, $this->allowed_roles)) {
            show_error(
                'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.',
                403,
                '403 Forbidden'
            );
            return;
        }

        // Set data user ke view untuk keperluan display
        $this->load->vars([
            'current_user' => [
                'id'           => $this->session->userdata('user_id'),
                'username'     => $this->session->userdata('username'),
                'nama_lengkap' => $this->session->userdata('nama_lengkap'),
                'role'         => $this->session->userdata('role'),
                'nip'          => $this->session->userdata('nip'),
            ]
        ]);
    }
}
