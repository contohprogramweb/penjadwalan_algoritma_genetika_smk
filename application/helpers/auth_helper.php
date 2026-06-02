<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Helper - Fungsi helper untuk autentikasi
 * 
 * Sesuai SRS Bab 16.3:
 * - redirect_by_role()
 * - is_logged_in()
 * - current_role()
 */

/**
 * Redirect user berdasarkan role mereka
 * Admin → admin/dashboard
 * Waka → waka/dashboard
 */
if (!function_exists('redirect_by_role')) {
    function redirect_by_role($role = NULL)
    {
        $CI =& get_instance();
        
        // Jika role tidak diberikan, ambil dari session
        if ($role === NULL) {
            $role = $CI->session->userdata('role');
        }

        switch ($role) {
            case 'admin':
                redirect('admin/dashboard', 'refresh');
                break;
            case 'waka':
                redirect('waka/dashboard', 'refresh');
                break;
            default:
                redirect('auth/login', 'refresh');
                break;
        }
    }
}

/**
 * Cek apakah user sudah login
 * 
 * @return bool
 */
if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        $CI =& get_instance();
        return (bool) $CI->session->userdata('logged_in');
    }
}

/**
 * Dapatkan role user yang sedang login
 * 
 * @return string|null
 */
if (!function_exists('current_role')) {
    function current_role()
    {
        $CI =& get_instance();
        return $CI->session->userdata('role');
    }
}

/**
 * Dapatkan data user yang sedang login
 * 
 * @return array|null
 */
if (!function_exists('current_user')) {
    function current_user()
    {
        $CI =& get_instance();
        
        if (!is_logged_in()) {
            return NULL;
        }

        return [
            'id' => $CI->session->userdata('user_id'),
            'username' => $CI->session->userdata('username'),
            'nama_lengkap' => $CI->session->userdata('nama_lengkap'),
            'role' => $CI->session->userdata('role'),
            'id_guru' => $CI->session->userdata('id_guru'),
            'id_kelas' => $CI->session->userdata('id_kelas'),
            'email' => $CI->session->userdata('email'),
            'last_login' => $CI->session->userdata('last_login'),
            'nip' => $CI->session->userdata('nip'),
            'login_time' => $CI->session->userdata('login_time')
        ];
    }
}

/**
 * Cek apakah role user sesuai dengan yang diharapkan
 * 
 * @param string|array $roles Role yang diizinkan (string atau array)
 * @return bool
 */
if (!function_exists('has_role')) {
    function has_role($roles)
    {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('role');
        
        if (is_array($roles)) {
            return in_array($user_role, $roles);
        }
        
        return $user_role === $roles;
    }
}

/**
 * Convert validation errors to array format for JSON response
 * Format: ['field_name' => 'error message']
 * 
 * @return array
 */
if (!function_exists('validation_errors_array')) {
    function validation_errors_array()
    {
        $CI =& get_instance();
        $errors = [];
        
        foreach ($_ERROR_ARRAY as $field => $message) {
            $errors[$field] = $message;
        }
        
        return $errors;
    }
}
