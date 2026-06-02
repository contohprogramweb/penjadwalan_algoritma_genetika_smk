<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk Penugasan Guru (Waka Kurikulum)
 * Sesuai SRS Bab 11.6 - Modul Penugasan
 */
require_once APPPATH . 'core/MY_Controller.php';

class Penugasan extends MY_Controller
{
    protected $allowed_roles = ['admin', 'waka'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Penugasan_model');
        $this->load->model('Guru_model');
        $this->load->model('Mapel_model');
        $this->load->model('Kelas_model');
        $this->load->model('Ruangan_model');
        $this->load->model('Tahun_ajaran_model');
        $this->load->library('form_validation');
    }

    /**
     * Halaman utama penugasan guru
     * Menampilkan checklist kesiapan data dan tabel penugasan
     */
    public function index()
    {
        // Get tahun ajaran aktif
        $tahun_ajaran_aktif = $this->Tahun_ajaran_model->get_active();
        
        if (!$tahun_ajaran_aktif) {
            $this->session->set_flashdata('warning', 'Belum ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
            redirect('admin/tahun_ajaran');
        }

        $data = [
            'page_title' => 'Penugasan Guru',
            'breadcrumbs' => [
                'home' => site_url('dashboard'),
                'penugasan' => site_url('waka/penugasan')
            ],
            'tahun_ajaran_aktif' => $tahun_ajaran_aktif,
            'semester_aktif' => 'ganjil', // Default semester ganjil
        ];

        // Cek kelas tanpa penugasan untuk checklist
        $kelas_tanpa_penugasan = $this->Penugasan_model->get_kelas_without_penugasan(
            $tahun_ajaran_aktif['id_tahun_ajaran'],
            $data['semester_aktif']
        );

        $data['kelas_tanpa_penugasan_count'] = count($kelas_tanpa_penugasan);
        $data['kelas_tanpa_penugasan'] = $kelas_tanpa_penugasan;

        // Hitung total penugasan
        $data['total_penugasan'] = $this->Penugasan_model->count_all(
            $tahun_ajaran_aktif['id_tahun_ajaran'],
            $data['semester_aktif']
        );

        // Load view dengan layout main.php
        $content = $this->load->view('waka/penugasan', $data, TRUE);
        $this->load->view('layouts/main', ['content' => $content]);
    }

    /**
     * Get datatables data untuk penugasan
     */
    public function get_data()
    {
        $tahun_ajaran_id = $this->input->post('id_tahun_ajaran');
        $semester = $this->input->post('semester', 'ganjil');

        $list = $this->Penugasan_model->get_datatables($tahun_ajaran_id, $semester);
        $total = $this->Penugasan_model->count_all($tahun_ajaran_id, $semester);
        $filtered = $this->Penugasan_model->count_filtered($tahun_ajaran_id, $semester);

        $data = [];
        $no = $_POST['start'];

        foreach ($list as $item) {
            $no++;
            $row = [];

            // Nomor urut
            $row[] = $no;

            // Nama Guru
            $row[] = htmlspecialchars($item['nama_guru'] ?? '-');

            // Mata Pelajaran
            $row[] = htmlspecialchars($item['nama_mapel'] ?? '-');

            // Kelas
            $row[] = htmlspecialchars($item['nama_kelas'] ?? '-');

            // Ruangan (jika praktikum)
            if ($item['is_praktikum']) {
                $row[] = '<span class="badge badge-info">Praktikum</span> ' . 
                         htmlspecialchars($item['nama_ruangan'] ?? 'Tidak ditentukan');
            } else {
                $row[] = '<span class="badge badge-secondary">Teori</span>';
            }

            // Semester
            $row[] = '<span class="badge badge-' . ($item['semester'] === 'ganjil' ? 'success' : 'primary') . '">' . 
                     ucfirst($item['semester']) . '</span>';

            // Jam per minggu
            $row[] = $item['jam_per_minggu'] . ' jam/minggu';

            // Aksi
            $actions = '
                <button class="btn btn-sm btn-info btn-edit" data-id="' . $item['id_penugasan'] . '" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="' . $item['id_penugasan'] . '" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            ';
            $row[] = $actions;

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data,
            "csrf_token_name" => $this->security->get_csrf_token_name(),
            "csrf_hash" => $this->security->get_csrf_hash()
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * Tambah penugasan baru
     */
    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $tahun_ajaran_aktif = $this->Tahun_ajaran_model->get_active();
        
        // Validasi form
        $this->form_validation->set_rules([
            'id_guru' => [
                'label' => 'Guru',
                'rules' => 'required|integer',
                'errors' => ['required' => 'Guru wajib dipilih']
            ],
            'id_mapel' => [
                'label' => 'Mata Pelajaran',
                'rules' => 'required|integer',
                'errors' => ['required' => 'Mata pelajaran wajib dipilih']
            ],
            'id_kelas' => [
                'label' => 'Kelas',
                'rules' => 'required|integer',
                'errors' => ['required' => 'Kelas wajib dipilih']
            ],
            'semester' => [
                'label' => 'Semester',
                'rules' => 'required|in_list[ganjil,genap]',
                'errors' => [
                    'required' => 'Semester wajib dipilih',
                    'in_list' => 'Semester tidak valid'
                ]
            ],
            'jam_per_minggu' => [
                'label' => 'Jam Per Minggu',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]',
                'errors' => [
                    'required' => 'Jam per minggu wajib diisi',
                    'integer' => 'Jam per minggu harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam per minggu minimal 1',
                    'less_than_equal_to' => 'Jam per minggu maksimal 48'
                ]
            ]
        ]);

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors_array();
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ]));
            return;
        }

        $id_guru = $this->input->post('id_guru');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $semester = $this->input->post('semester');
        $is_praktikum = $this->input->post('is_praktikum') ? 1 : 0;
        $id_ruangan = $is_praktikum ? $this->input->post('id_ruangan') : null;

        // Validasi ruangan jika praktikum
        if ($is_praktikum && empty($id_ruangan)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Ruangan wajib dipilih untuk mapel praktikum'
                ]));
            return;
        }

        // Cek duplikasi
        if ($this->Penugasan_model->check_duplicate(
            $id_guru, $id_mapel, $id_kelas, 
            $tahun_ajaran_aktif['id_tahun_ajaran'], $semester
        )) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Penugasan sudah ada! Guru ini sudah ditugaskan untuk mapel yang sama di kelas yang sama pada semester ini.'
                ]));
            return;
        }

        // Simpan data
        $data = [
            'id_guru' => $id_guru,
            'id_mapel' => $id_mapel,
            'id_kelas' => $id_kelas,
            'id_tahun_ajaran' => $tahun_ajaran_aktif['id_tahun_ajaran'],
            'semester' => $semester,
            'jam_per_minggu' => $this->input->post('jam_per_minggu'),
            'is_praktikum' => $is_praktikum,
            'id_ruangan' => $id_ruangan
        ];

        $result = $this->Penugasan_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Penugasan berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan penugasan'
                ]));
        }
    }

    /**
     * Edit penugasan
     */
    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $penugasan = $this->Penugasan_model->get_by_id($id);
        if (!$penugasan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data penugasan tidak ditemukan'
                ]));
            return;
        }

        $tahun_ajaran_aktif = $this->Tahun_ajaran_model->get_active();

        // Validasi form (sama seperti tambah)
        $this->form_validation->set_rules([
            'id_guru' => ['label' => 'Guru', 'rules' => 'required|integer'],
            'id_mapel' => ['label' => 'Mata Pelajaran', 'rules' => 'required|integer'],
            'id_kelas' => ['label' => 'Kelas', 'rules' => 'required|integer'],
            'semester' => ['label' => 'Semester', 'rules' => 'required|in_list[ganjil,genap]'],
            'jam_per_minggu' => ['label' => 'Jam Per Minggu', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]']
        ]);

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors_array();
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ]));
            return;
        }

        $id_guru = $this->input->post('id_guru');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $semester = $this->input->post('semester');
        $is_praktikum = $this->input->post('is_praktikum') ? 1 : 0;
        $id_ruangan = $is_praktikum ? $this->input->post('id_ruangan') : null;

        // Validasi ruangan jika praktikum
        if ($is_praktikum && empty($id_ruangan)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Ruangan wajib dipilih untuk mapel praktikum'
                ]));
            return;
        }

        // Cek duplikasi (exclude ID sendiri)
        if ($this->Penugasan_model->check_duplicate(
            $id_guru, $id_mapel, $id_kelas,
            $tahun_ajaran_aktif['id_tahun_ajaran'], $semester,
            $id
        )) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Penugasan sudah ada! Guru ini sudah ditugaskan untuk mapel yang sama di kelas yang sama pada semester ini.'
                ]));
            return;
        }

        // Update data
        $data = [
            'id_guru' => $id_guru,
            'id_mapel' => $id_mapel,
            'id_kelas' => $id_kelas,
            'semester' => $semester,
            'jam_per_minggu' => $this->input->post('jam_per_minggu'),
            'is_praktikum' => $is_praktikum,
            'id_ruangan' => $id_ruangan
        ];

        $result = $this->Penugasan_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Penugasan berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui penugasan'
                ]));
        }
    }

    /**
     * Hapus penugasan
     */
    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $penugasan = $this->Penugasan_model->get_by_id($id);
        if (!$penugasan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data penugasan tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Penugasan_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Penugasan berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus penugasan'
                ]));
        }
    }

    /**
     * Get detail penugasan untuk edit modal
     */
    public function get_detail($id)
    {
        $penugasan = $this->Penugasan_model->get_by_id($id);

        if ($penugasan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $penugasan
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data penugasan tidak ditemukan'
                ]));
        }
    }

    /**
     * Get dropdown data untuk form
     */
    public function get_dropdown_data()
    {
        $type = $this->input->get('type');
        $id_tahun_ajaran = $this->input->get('id_tahun_ajaran');
        $semester = $this->input->get('semester', 'ganjil');

        $data = [];

        switch ($type) {
            case 'guru':
                $data = $this->Guru_model->get_datatables();
                break;
            case 'mapel':
                $data = $this->Mapel_model->get_datatables();
                break;
            case 'kelas':
                $data = $this->Kelas_model->get_datatables();
                break;
            case 'ruangan':
                $data = $this->Ruangan_model->get_datatables();
                break;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => TRUE,
                'data' => $data
            ]));
    }
}
