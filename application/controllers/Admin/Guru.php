<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Guru
 * Referensi: SRS Bab 11.5, 13.1
 */
class Guru extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Guru_model');
        $this->load->library('form_validation');
    }

    /**
     * Halaman daftar guru dengan DataTables
     */
    public function index()
    {
        $this->load->view('admin/guru_list');
    }

    /**
     * Tambah guru baru
     */
    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        // Validasi form
        $this->form_validation->set_rules([
            'nip' => [
                'label' => 'NIP',
                'rules' => 'required|exact_length[18]|numeric',
                'errors' => [
                    'required' => 'NIP wajib diisi',
                    'exact_length' => 'NIP harus 18 digit',
                    'numeric' => 'NIP harus berupa angka'
                ]
            ],
            'nuptk' => [
                'label' => 'NUPTK',
                'rules' => 'required|exact_length[16]|numeric',
                'errors' => [
                    'required' => 'NUPTK wajib diisi',
                    'exact_length' => 'NUPTK harus 16 digit',
                    'numeric' => 'NUPTK harus berupa angka'
                ]
            ],
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 100 karakter'
                ]
            ],
            'no_hp' => [
                'label' => 'Nomor HP',
                'rules' => 'required|min_length[10]|max_length[15]|numeric',
                'errors' => [
                    'required' => 'Nomor HP wajib diisi',
                    'min_length' => 'Nomor HP minimal 10 digit',
                    'max_length' => 'Nomor HP maksimal 15 digit',
                    'numeric' => 'Nomor HP harus berupa angka'
                ]
            ],
            'jam_min' => [
                'label' => 'Jam Minimal',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]',
                'errors' => [
                    'required' => 'Jam minimal wajib diisi',
                    'integer' => 'Jam minimal harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam minimal minimal 1',
                    'less_than_equal_to' => 'Jam minimal maksimal 48'
                ]
            ],
            'jam_maks' => [
                'label' => 'Jam Maksimal',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]|callback_check_jam',
                'errors' => [
                    'required' => 'Jam maksimal wajib diisi',
                    'integer' => 'Jam maksimal harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam maksimal minimal 1',
                    'less_than_equal_to' => 'Jam maksimal maksimal 48'
                ]
            ],
            'status' => [
                'label' => 'Status Kepegawaian',
                'rules' => 'required|in_list[pns,honorer,ttk]',
                'errors' => [
                    'required' => 'Status wajib dipilih',
                    'in_list' => 'Status tidak valid'
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

        // Simpan data
        $data = [
            'nip' => $this->input->post('nip'),
            'nuptk' => $this->input->post('nuptk'),
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'no_hp' => $this->input->post('no_hp'),
            'jam_min' => $this->input->post('jam_min'),
            'jam_maks' => $this->input->post('jam_maks'),
            'status' => $this->input->post('status')
        ];

        $result = $this->Guru_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data guru berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data guru'
                ]));
        }
    }

    /**
     * Callback validasi jam min <= jam maks
     */
    public function check_jam()
    {
        $jam_min = $this->input->post('jam_min');
        $jam_maks = $this->input->post('jam_maks');

        if ($jam_maks < $jam_min) {
            $this->form_validation->set_message('check_jam', 'Jam maksimal harus lebih besar atau sama dengan jam minimal');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Edit guru
     */
    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        // Cek apakah guru ada
        $guru = $this->Guru_model->get_by_id($id);
        if (!$guru) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data guru tidak ditemukan'
                ]));
            return;
        }

        // Validasi form (sama seperti tambah, kecuali NIP/NUPTK bisa optional jika tidak diubah)
        $this->form_validation->set_rules([
            'nip' => [
                'label' => 'NIP',
                'rules' => 'required|exact_length[18]|numeric',
                'errors' => [
                    'required' => 'NIP wajib diisi',
                    'exact_length' => 'NIP harus 18 digit',
                    'numeric' => 'NIP harus berupa angka'
                ]
            ],
            'nuptk' => [
                'label' => 'NUPTK',
                'rules' => 'required|exact_length[16]|numeric',
                'errors' => [
                    'required' => 'NUPTK wajib diisi',
                    'exact_length' => 'NUPTK harus 16 digit',
                    'numeric' => 'NUPTK harus berupa angka'
                ]
            ],
            'nama' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 100 karakter'
                ]
            ],
            'no_hp' => [
                'label' => 'Nomor HP',
                'rules' => 'required|min_length[10]|max_length[15]|numeric',
                'errors' => [
                    'required' => 'Nomor HP wajib diisi',
                    'min_length' => 'Nomor HP minimal 10 digit',
                    'max_length' => 'Nomor HP maksimal 15 digit',
                    'numeric' => 'Nomor HP harus berupa angka'
                ]
            ],
            'jam_min' => [
                'label' => 'Jam Minimal',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]',
                'errors' => [
                    'required' => 'Jam minimal wajib diisi',
                    'integer' => 'Jam minimal harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam minimal minimal 1',
                    'less_than_equal_to' => 'Jam minimal maksimal 48'
                ]
            ],
            'jam_maks' => [
                'label' => 'Jam Maksimal',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]|callback_check_jam',
                'errors' => [
                    'required' => 'Jam maksimal wajib diisi',
                    'integer' => 'Jam maksimal harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam maksimal minimal 1',
                    'less_than_equal_to' => 'Jam maksimal maksimal 48'
                ]
            ],
            'status' => [
                'label' => 'Status Kepegawaian',
                'rules' => 'required|in_list[pns,honorer,ttk]',
                'errors' => [
                    'required' => 'Status wajib dipilih',
                    'in_list' => 'Status tidak valid'
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

        // Update data
        $data = [
            'nip' => $this->input->post('nip'),
            'nuptk' => $this->input->post('nuptk'),
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'no_hp' => $this->input->post('no_hp'),
            'jam_min' => $this->input->post('jam_min'),
            'jam_maks' => $this->input->post('jam_maks'),
            'status' => $this->input->post('status')
        ];

        $result = $this->Guru_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data guru berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data guru'
                ]));
        }
    }

    /**
     * Hapus guru
     */
    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        // Cek apakah guru ada
        $guru = $this->Guru_model->get_by_id($id);
        if (!$guru) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data guru tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Guru_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data guru berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data guru'
                ]));
        }
    }

    /**
     * Get detail guru untuk edit modal
     */
    public function get_detail($id)
    {
        $guru = $this->Guru_model->get_by_id($id);
        
        if ($guru) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $guru
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data guru tidak ditemukan'
                ]));
        }
    }
}
