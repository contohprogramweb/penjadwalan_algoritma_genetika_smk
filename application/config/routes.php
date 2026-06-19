<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|       example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|       https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|       $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|       $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|       $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:     my-controller/index     -> my_controller/index
|               my-controller/my-method -> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Routes untuk Admin
$route['admin/dashboard'] = 'Admin/Dashboard/index';
$route['admin/onboarding'] = 'Admin/Onboarding/index';
$route['admin/guru'] = 'Admin/Guru/index';
$route['admin/guru/tambah'] = 'Admin/Guru/tambah';
$route['admin/guru/edit/(:num)'] = 'Admin/Guru/edit/$1';
$route['admin/guru/hapus/(:num)'] = 'Admin/Guru/hapus/$1';
$route['admin/guru/get_detail/(:num)'] = 'Admin/Guru/get_detail/$1';
$route['admin/mapel'] = 'Admin/Mapel/index';
$route['admin/mapel/tambah'] = 'Admin/Mapel/tambah';
$route['admin/mapel/edit/(:num)'] = 'Admin/Mapel/edit/$1';
$route['admin/mapel/hapus/(:num)'] = 'Admin/Mapel/hapus/$1';
$route['admin/mapel/get_detail/(:num)'] = 'Admin/Mapel/get_detail/$1';
$route['admin/kelas'] = 'Admin/Kelas/index';
$route['admin/kelas/tambah'] = 'Admin/Kelas/tambah';
$route['admin/kelas/edit/(:num)'] = 'Admin/Kelas/edit/$1';
$route['admin/kelas/hapus/(:num)'] = 'Admin/Kelas/hapus/$1';
$route['admin/kelas/get_detail/(:num)'] = 'Admin/Kelas/get_detail/$1';
$route['admin/ruangan'] = 'Admin/Ruangan/index';
$route['admin/ruangan/tambah'] = 'Admin/Ruangan/tambah';
$route['admin/ruangan/update/(:num)'] = 'Admin/Ruangan/update/$1';
$route['admin/ruangan/hapus/(:num)'] = 'Admin/Ruangan/hapus/$1';
$route['admin/ruangan/get_detail/(:num)'] = 'Admin/Ruangan/get_detail/$1';
$route['admin/jam'] = 'Admin/Jam/index';
$route['admin/jam/tambah'] = 'Admin/Jam/tambah';
$route['admin/jam/edit/(:num)'] = 'Admin/Jam/edit/$1';
$route['admin/jam/hapus/(:num)'] = 'Admin/Jam/hapus/$1';
$route['admin/jam/get_detail/(:num)'] = 'Admin/Jam/get_detail/$1';
$route['admin/tahun_ajaran'] = 'Admin/Tahun_ajaran/index';
$route['admin/tahun_ajaran/tambah'] = 'Admin/Tahun_ajaran/tambah';
$route['admin/tahun_ajaran/edit/(:num)'] = 'Admin/Tahun_ajaran/edit/$1';
$route['admin/tahun_ajaran/hapus/(:num)'] = 'Admin/Tahun_ajaran/hapus/$1';
$route['admin/tahun_ajaran/get_detail/(:num)'] = 'Admin/Tahun_ajaran/get_detail/$1';

// Routes untuk Waka
$route['waka/dashboard'] = 'Waka/Dashboard/index';
$route['waka/penugasan'] = 'Waka/Penugasan/index';

// Routes untuk Guru
$route['guru/dashboard'] = 'Guru/Dashboard/index';

$route['waka/penugasan/get_data'] = 'Waka/Penugasan/get_data';
$route['waka/penugasan/tambah'] = 'Waka/Penugasan/tambah';
$route['waka/penugasan/edit/(:num)'] = 'Waka/Penugasan/edit/$1';
$route['waka/penugasan/hapus/(:num)'] = 'Waka/Penugasan/hapus/$1';
$route['waka/penugasan/get_detail/(:num)'] = 'Waka/Penugasan/get_detail/$1';
$route['waka/generate'] = 'Waka/Generate/index';
$route['waka/generate/trigger'] = 'Waka/Generate/trigger';
$route['waka/generate/progress'] = 'Waka/Generate/progress';
$route['waka/generate/reset_progress'] = 'Waka/Generate/reset_progress';
$route['waka/generate/run_process/(:num)'] = 'Waka/Generate/run_process/$1';
$route['waka/jadwal'] = 'Waka/Jadwal/index';
$route['waka/jadwal/edit_slot'] = 'Waka/Jadwal/edit_slot';
$route['waka/jadwal/approve'] = 'Waka/Jadwal/approve';
$route['waka/jadwal/check_conflict'] = 'Waka/Jadwal/check_conflict';
$route['waka/jadwal/get_penugasan_by_kelas'] = 'Waka/Jadwal/get_penugasan_by_kelas';

// Routes untuk Jadwal (publik - semua role)
$route['jadwal'] = 'Jadwal/index';
$route['jadwal/check_conflict'] = 'Jadwal/check_conflict';

// Routes untuk Datatables (Server-side processing)
$route['datatables/guru'] = 'Datatables/guru';
$route['datatables/mapel'] = 'Datatables/mapel';
$route['datatables/kelas'] = 'Datatables/kelas';
$route['datatables/ruangan'] = 'Datatables/ruangan';
$route['datatables/jam'] = 'Datatables/jam';
$route['datatables/tahun_ajaran'] = 'Datatables/tahun_ajaran';

// Routes untuk Laporan
$route['laporan/pdf_jadwal/(:num)'] = 'Laporan/pdf_jadwal/$1';
$route['laporan/pdf_beban_guru/(:num)'] = 'Laporan/pdf_beban_guru/$1';

// Routes untuk Auth
$route['auth/login'] = 'Auth/login';
$route['auth/logout'] = 'Auth/logout';