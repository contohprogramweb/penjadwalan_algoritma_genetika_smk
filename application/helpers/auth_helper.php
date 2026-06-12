<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Helper - Fungsi bantu autentikasi
 */

if (!function_exists('is_logged_in')) {
    /**
     * Cek apakah user sudah login
     *
     * @return bool
     */
    function is_logged_in()
    {
        $CI =& get_instance();
        return (bool) $CI->session->userdata('logged_in');
    }
}

if (!function_exists('current_user_role')) {
    /**
     * Ambil role user yang sedang login
     *
     * @return string|null
     */
    function current_user_role()
    {
        $CI =& get_instance();
        return $CI->session->userdata('role');
    }
}

if (!function_exists('current_user')) {
    /**
     * Ambil data user yang sedang login
     *
     * @return array
     */
    function current_user()
    {
        $CI =& get_instance();
        return [
            'id'           => $CI->session->userdata('user_id'),
            'username'     => $CI->session->userdata('username'),
            'nama_lengkap' => $CI->session->userdata('nama_lengkap'),
            'role'         => $CI->session->userdata('role'),
            'nip'          => $CI->session->userdata('nip'),
            'email'        => $CI->session->userdata('email'),
        ];
    }
}

if (!function_exists('validation_errors_array')) {
    /**
     * Mengembalikan error validasi sebagai array
     *
     * @return array
     */
    function validation_errors_array()
    {
        $CI =& get_instance();
        $errors = [];
        foreach ($CI->form_validation->get_errors() as $field => $error) {
            $errors[] = $error;
        }
        return $errors;
    }
}
