<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Application Configuration File
 * 
 * Referensi SRS: Bab 11.10 (PDF Engine) & Best Practices Production
 */

// ------------------------------------------------------------------
// ERROR REPORTING & LOGGING (Production Settings)
// ------------------------------------------------------------------

/*
 |--------------------------------------------------------------------------
 | Error Logging Threshold
 |--------------------------------------------------------------------------
 |
 | Setting untuk production environment:
 | - 0 = Disable logging
 | - 1 = Error Messages (Errors, PHP Errors)
 | - 2 = Debug Messages
 | - 3 = Informational Messages
 | - 4 = All Messages
 |
 | Untuk production: set ke 1 (hanya error penting yang dicatat)
 | Stack trace tidak ditampilkan ke user (lihat views/errors/)
 */
$config['log_threshold'] = 1;

/*
 |--------------------------------------------------------------------------
 | Error Logging Path
 |--------------------------------------------------------------------------
 */
$config['log_path'] = APPPATH . 'logs/';

/*
 |--------------------------------------------------------------------------
 | Date Format for Logs
 |--------------------------------------------------------------------------
 */
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
 |--------------------------------------------------------------------------
 | Error Views Directory
 |--------------------------------------------------------------------------
 | Custom error pages telah dibuat di application/views/errors/
 | - 403.php: Akses Ditolak
 | - 404.php: Halaman Tidak Ditemukan
 | - 500.php: Terjadi Kesalahan Server
 */
$config['error_view_path'] = APPPATH . 'views/errors/';

/*
 |--------------------------------------------------------------------------
 | Show PHP Errors (Production: FALSE)
 |--------------------------------------------------------------------------
 | Di production, error PHP tidak ditampilkan langsung ke user.
 | User akan melihat halaman error yang ramah (views/errors/*.php)
 | Detail error dicatat ke log file untuk debugging admin.
 */
// Note: Ini diatur juga di index.php dengan ini_set()
// $config['show_php_errors'] = FALSE; // Implicit dalam error handler

// ------------------------------------------------------------------
// DOMPDF CONFIGURATION (Bab 11.10 - PDF Engine)
// ------------------------------------------------------------------

/*
 |--------------------------------------------------------------------------
 | Dompdf Options
 |--------------------------------------------------------------------------
 | Konfigurasi untuk Dompdf 1.x
 */
$config['dompdf_options'] = [
    'isRemoteEnabled' => TRUE,      // Izinkan load CSS/image dari URL
    'defaultFont'     => 'Arial',   // Font default untuk PDF
    'defaultPaperSize'=> 'A4',
    'defaultMediaType'=> 'screen',
    'isHtml5ParserEnabled' => TRUE, // Parse HTML5
    'isPhpEnabled'    => FALSE,     // Disable PHP di PDF (security)
];

// ------------------------------------------------------------------
// GENERAL APPLICATION SETTINGS
// ------------------------------------------------------------------

/*
 |--------------------------------------------------------------------------
 | Base Site URL
 |--------------------------------------------------------------------------
 */
$config['base_url'] = ''; // Auto-detect atau set manual

/*
 |--------------------------------------------------------------------------
 | Index File
 |--------------------------------------------------------------------------
 */
$config['index_page'] = '';

/*
 |--------------------------------------------------------------------------
 | URI PROTOCOL
 |--------------------------------------------------------------------------
 */
$config['uri_protocol'] = 'REQUEST_URI';

/*
 |--------------------------------------------------------------------------
 | Default Character Set
 |--------------------------------------------------------------------------
 */
$config['charset'] = 'UTF-8';

/*
 |--------------------------------------------------------------------------
 | Enable Query Strings
 |--------------------------------------------------------------------------
 */
$config['enable_query_strings'] = FALSE;

// ------------------------------------------------------------------
// END OF CONFIGURATION FILE
// ------------------------------------------------------------------
