<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Custom Helper - Fungsi bantu tambahan untuk validasi dan CSRF
 */

if (!function_exists('validation_errors_array')) {
    /**
     * Mengembalikan array error validasi dari form_validation
     * Format: ['field_name' => 'error message']
     *
     * @return array
     */
    function validation_errors_array()
    {
        $CI =& get_instance();
        $errors = [];
        
        if ($CI->form_validation->run() === FALSE) {
            $fields = $CI->form_validation->_error_array;
            foreach ($fields as $field => $message) {
                $errors[$field] = $message;
            }
        }
        
        return $errors;
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Menghasilkan hidden input field untuk CSRF token
     *
     * @return string HTML hidden input
     */
    function csrf_field()
    {
        $CI =& get_instance();
        $token_name = $CI->security->get_csrf_token_name();
        $token_hash = $CI->security->get_csrf_hash();
        
        return '<input type="hidden" name="' . $token_name . '" value="' . $token_hash . '">';
    }
}

if (!function_exists('csrf_meta_tags')) {
    /**
     * Menghasilkan meta tags untuk CSRF token (untuk AJAX)
     *
     * @return string HTML meta tags
     */
    function csrf_meta_tags()
    {
        $CI =& get_instance();
        $token_name = $CI->security->get_csrf_token_name();
        $token_hash = $CI->security->get_csrf_hash();
        
        return '<meta name="csrf_token_name" content="' . htmlspecialchars($token_name) . '">' .
               '<meta name="csrf_token_hash" content="' . htmlspecialchars($token_hash) . '">';
    }
}
