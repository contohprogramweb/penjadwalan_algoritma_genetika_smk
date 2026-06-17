<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Mapel
 * Referensi: SRS Bab 11.5, 13.2
 */
class Mapel extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mapel_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('admin/mapel_list');
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $this->form_validation->set_rules([
            'kode_mapel' => [
                'label' => 'Kode Mapel',
                'rules' => 'required|alpha_dash|max_length[10]',
                'errors' => [
                    'required' => 'Kode mapel wajib diisi',
                    'alpha_dash' => 'Kode hanya boleh berisi huruf, angka, dan strip (-)',
                    'max_length' => 'Kode maksimal 10 karakter'
                ]
            ],
            'nama_mapel' => [
                'label' => 'Nama Mapel',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama mapel wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'tipe' => [
                'label' => 'Tipe Mapel',
                'rules' => 'required|in_list[teori,praktikum]',
                'errors' => [
                    'required' => 'Tipe mapel wajib dipilih',
                    'in_list' => 'Tipe harus teori atau praktikum'
                ]
            ],
            'jam_per_minggu' => [
                'label' => 'JP Per Minggu',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]',
                'errors' => [
                    'required' => 'JP per minggu wajib diisi',
                    'integer' => 'JP harus bilangan bulat',
                    'greater_than_equal_to' => 'JP minimal 1',
                    'less_than_equal_to' => 'JP maksimal 48'
                ]
            ],
            'kelompok' => [
                'label' => 'Kelompok Mapel',
                'rules' => 'required|in_list[A,B,C,D]',
                'errors' => [
                    'required' => 'Kelompok mapel wajib dipilih',
                    'in_list' => 'Kelompok harus A, B, C, atau D'
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

        // Cek kode unik
        if ($this->Mapel_model->check_kode_exists($this->input->post('kode_mapel'))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode mapel sudah digunakan',
                    'errors' => ['kode_mapel' => 'Kode mapel sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_mapel' => strtoupper($this->input->post('kode_mapel')),
            'nama_mapel' => $this->input->post('nama_mapel'),
            'tipe' => $this->input->post('tipe'),
            'jam_per_minggu' => intval($this->input->post('jam_per_minggu')),
            'kelompok' => $this->input->post('kelompok')
        ];

        $result = $this->Mapel_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data mapel berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data mapel'
                ]));
        }
    }

    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $mapel = $this->Mapel_model->get_by_id($id);
        if (!$mapel) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data mapel tidak ditemukan'
                ]));
            return;
        }

        $this->form_validation->set_rules([
            'kode_mapel' => [
                'label' => 'Kode Mapel',
                'rules' => 'required|alpha_dash|max_length[10]',
                'errors' => [
                    'required' => 'Kode mapel wajib diisi',
                    'alpha_dash' => 'Kode hanya boleh berisi huruf, angka, dan strip (-)',
                    'max_length' => 'Kode maksimal 10 karakter'
                ]
            ],
            'nama_mapel' => [
                'label' => 'Nama Mapel',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama mapel wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'tipe' => [
                'label' => 'Tipe Mapel',
                'rules' => 'required|in_list[teori,praktikum]',
                'errors' => [
                    'required' => 'Tipe mapel wajib dipilih',
                    'in_list' => 'Tipe harus teori atau praktikum'
                ]
            ],
            'jam_per_minggu' => [
                'label' => 'JP Per Minggu',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[48]',
                'errors' => [
                    'required' => 'JP per minggu wajib diisi',
                    'integer' => 'JP harus bilangan bulat',
                    'greater_than_equal_to' => 'JP minimal 1',
                    'less_than_equal_to' => 'JP maksimal 48'
                ]
            ],
            'kelompok' => [
                'label' => 'Kelompok Mapel',
                'rules' => 'required|in_list[A,B,C,D]',
                'errors' => [
                    'required' => 'Kelompok mapel wajib dipilih',
                    'in_list' => 'Kelompok harus A, B, C, atau D'
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

        // Cek kode unik (kecuali untuk record ini)
        if ($this->Mapel_model->check_kode_exists($this->input->post('kode_mapel'), $id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode mapel sudah digunakan',
                    'errors' => ['kode_mapel' => 'Kode mapel sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_mapel' => strtoupper($this->input->post('kode_mapel')),
            'nama_mapel' => $this->input->post('nama_mapel'),
            'tipe' => $this->input->post('tipe'),
            'jam_per_minggu' => intval($this->input->post('jam_per_minggu')),
            'kelompok' => $this->input->post('kelompok')
        ];

        $result = $this->Mapel_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data mapel berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data mapel'
                ]));
        }
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $mapel = $this->Mapel_model->get_by_id($id);
        if (!$mapel) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data mapel tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Mapel_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data mapel berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data mapel'
                ]));
        }
    }

    public function get_detail($id)
    {
        $mapel = $this->Mapel_model->get_by_id($id);
        
        if ($mapel) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $mapel
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data mapel tidak ditemukan'
                ]));
        }
    }
}
