<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Ruangan
 */
class Ruangan extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ruangan_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('admin/ruangan_list');
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $this->form_validation->set_rules([
            'kode_ruangan' => [
                'label' => 'Kode Ruangan',
                'rules' => 'required|min_length[2]|max_length[10]',
                'errors' => [
                    'required' => 'Kode ruangan wajib diisi',
                    'min_length' => 'Kode minimal 2 karakter',
                    'max_length' => 'Kode maksimal 10 karakter'
                ]
            ],
            'nama_ruangan' => [
                'label' => 'Nama Ruangan',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama ruangan wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'kapasitas' => [
                'label' => 'Kapasitas',
                'rules' => 'required|integer|greater_than_equal_to[1]',
                'errors' => [
                    'required' => 'Kapasitas wajib diisi',
                    'integer' => 'Kapasitas harus bilangan bulat',
                    'greater_than_equal_to' => 'Kapasitas minimal 1'
                ]
            ],
            'tipe' => [
                'label' => 'Jenis Ruangan',
                'rules' => 'required|in_list[kelas,lab,bengkel,lapangan,aula]',
                'errors' => [
                    'required' => 'Jenis ruangan wajib dipilih',
                    'in_list' => 'Jenis tidak valid'
                ]
            ],
            'lantai' => [
                'label' => 'Lantai',
                'rules' => 'permit_empty|max_length[10]',
                'errors' => [
                    'max_length' => 'Lantai maksimal 10 karakter'
                ]
            ],
            'fasilitas' => [
                'label' => 'Fasilitas',
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Fasilitas maksimal 255 karakter'
                ]
            ]
        ]);

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors_array();
            // Format errors agar key sesuai dengan ID elemen di form (tanpa underscore setelah kata kunci)
            $formatted_errors = [];
            foreach ($errors as $key => $message) {
                $shortKey = str_replace('_ruangan', '', $key);
                $formatted_errors[$shortKey] = $message;
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Validasi gagal',
                    'errors' => $formatted_errors
                ]));
            return;
        }

        // Cek kode unik
        if ($this->Ruangan_model->check_kode_exists($this->input->post('kode_ruangan'))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode ruangan sudah digunakan',
                    'errors' => ['kode' => 'Kode ruangan sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_ruangan' => strtoupper(trim($this->input->post('kode_ruangan'))),
            'nama_ruangan' => trim($this->input->post('nama_ruangan')),
            'kapasitas' => (int) $this->input->post('kapasitas'),
            'tipe' => $this->input->post('tipe'),
            'lantai' => trim($this->input->post('lantai')),
            'fasilitas' => trim($this->input->post('fasilitas')),
            'status_aktif' => 1
        ];

        $result = $this->Ruangan_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data ruangan berhasil ditambahkan'
                ]));
        } else {
            log_message('error', 'Gagal insert ruangan, DB Error: ' . $this->db->_error_message());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data ruangan: ' . $this->db->_error_message()
                ]));
        }
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $ruangan = $this->Ruangan_model->get_by_id($id);
        if (!$ruangan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data ruangan tidak ditemukan'
                ]));
            return;
        }

        $this->form_validation->set_rules([
            'kode_ruangan' => [
                'label' => 'Kode Ruangan',
                'rules' => 'required|min_length[2]|max_length[10]',
                'errors' => [
                    'required' => 'Kode ruangan wajib diisi',
                    'min_length' => 'Kode minimal 2 karakter',
                    'max_length' => 'Kode maksimal 10 karakter'
                ]
            ],
            'nama_ruangan' => [
                'label' => 'Nama Ruangan',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama ruangan wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'kapasitas' => [
                'label' => 'Kapasitas',
                'rules' => 'required|integer|greater_than_equal_to[1]',
                'errors' => [
                    'required' => 'Kapasitas wajib diisi',
                    'integer' => 'Kapasitas harus bilangan bulat',
                    'greater_than_equal_to' => 'Kapasitas minimal 1'
                ]
            ],
            'tipe' => [
                'label' => 'Jenis Ruangan',
                'rules' => 'required|in_list[kelas,lab,bengkel,lapangan,aula]',
                'errors' => [
                    'required' => 'Jenis ruangan wajib dipilih',
                    'in_list' => 'Jenis tidak valid'
                ]
            ],
            'lantai' => [
                'label' => 'Lantai',
                'rules' => 'permit_empty|max_length[10]',
                'errors' => [
                    'max_length' => 'Lantai maksimal 10 karakter'
                ]
            ],
            'fasilitas' => [
                'label' => 'Fasilitas',
                'rules' => 'permit_empty|max_length[255]',
                'errors' => [
                    'max_length' => 'Fasilitas maksimal 255 karakter'
                ]
            ]
        ]);

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors_array();
            $formatted_errors = [];
            foreach ($errors as $key => $message) {
                $shortKey = str_replace('_ruangan', '', $key);
                $formatted_errors[$shortKey] = $message;
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Validasi gagal',
                    'errors' => $formatted_errors
                ]));
            return;
        }

        // Cek kode unik (kecuali untuk record ini)
        if ($this->Ruangan_model->check_kode_exists($this->input->post('kode_ruangan'), $id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode ruangan sudah digunakan',
                    'errors' => ['kode' => 'Kode ruangan sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_ruangan' => strtoupper(trim($this->input->post('kode_ruangan'))),
            'nama_ruangan' => trim($this->input->post('nama_ruangan')),
            'kapasitas' => (int) $this->input->post('kapasitas'),
            'tipe' => $this->input->post('tipe'),
            'lantai' => trim($this->input->post('lantai')),
            'fasilitas' => trim($this->input->post('fasilitas'))
        ];

        $result = $this->Ruangan_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data ruangan berhasil diperbarui'
                ]));
        } else {
            log_message('error', 'Gagal update ruangan ID: ' . $id . ', DB Error: ' . $this->db->_error_message());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data ruangan: ' . $this->db->_error_message()
                ]));
        }
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $ruangan = $this->Ruangan_model->get_by_id($id);
        if (!$ruangan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data ruangan tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Ruangan_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data ruangan berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data ruangan'
                ]));
        }
    }

    public function get_detail($id)
    {
        $ruangan = $this->Ruangan_model->get_by_id($id);
        
        if ($ruangan) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $ruangan
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data ruangan tidak ditemukan'
                ]));
        }
    }
}
