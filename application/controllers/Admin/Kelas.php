<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Kelas
 * Referensi: SRS Bab 11.5, 13.3
 */
class Kelas extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kelas_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('admin/kelas_list');
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $this->form_validation->set_rules([
            'kode_kelas' => [
                'label' => 'Kode Kelas',
                'rules' => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'Kode kelas wajib diisi',
                    'min_length' => 'Kode minimal 3 karakter',
                    'max_length' => 'Kode maksimal 20 karakter'
                ]
            ],
            'nama_kelas' => [
                'label' => 'Nama Kelas',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama kelas wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'tingkat' => [
                'label' => 'Tingkat',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[6]',
                'errors' => [
                    'required' => 'Tingkat wajib diisi',
                    'integer' => 'Tingkat harus bilangan bulat',
                    'greater_than_equal_to' => 'Tingkat minimal 1',
                    'less_than_equal_to' => 'Tingkat maksimal 6'
                ]
            ],
            'kapasitas_siswa' => [
                'label' => 'Kapasitas Siswa',
                'rules' => 'required|integer|greater_than_equal_to[10]|less_than_equal_to[50]',
                'errors' => [
                    'required' => 'Kapasitas wajib diisi',
                    'integer' => 'Kapasitas harus bilangan bulat',
                    'greater_than_equal_to' => 'Kapasitas minimal 10 siswa',
                    'less_than_equal_to' => 'Kapasitas maksimal 50 siswa'
                ]
            ],
            'jurusan' => [
                'label' => 'Jurusan',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Jurusan wajib diisi',
                    'max_length' => 'Jurusan maksimal 50 karakter'
                ]
            ],
            'tahun_ajaran' => [
                'label' => 'Tahun Ajaran',
                'rules' => 'required|min_length[9]|max_length[9]',
                'errors' => [
                    'required' => 'Tahun ajaran wajib diisi',
                    'min_length' => 'Format tahun ajaran YYYY/YYYY',
                    'max_length' => 'Format tahun ajaran YYYY/YYYY'
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
        if ($this->Kelas_model->check_kode_exists($this->input->post('kode_kelas'))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode kelas sudah digunakan',
                    'errors' => ['kode_kelas' => 'Kode kelas sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_kelas' => strtoupper($this->input->post('kode_kelas')),
            'nama_kelas' => $this->input->post('nama_kelas'),
            'tingkat' => $this->input->post('tingkat'),
            'kapasitas_siswa' => intval($this->input->post('kapasitas_siswa')),
            'jurusan' => $this->input->post('jurusan'),
            // tahun_ajaran removed - not in DB schema
        ];

        $result = $this->Kelas_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data kelas berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data kelas'
                ]));
        }
    }

    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $kelas = $this->Kelas_model->get_by_id($id);
        if (!$kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data kelas tidak ditemukan'
                ]));
            return;
        }

        $this->form_validation->set_rules([
            'kode_kelas' => [
                'label' => 'Kode Kelas',
                'rules' => 'required|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'Kode kelas wajib diisi',
                    'min_length' => 'Kode minimal 3 karakter',
                    'max_length' => 'Kode maksimal 20 karakter'
                ]
            ],
            'nama_kelas' => [
                'label' => 'Nama Kelas',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama kelas wajib diisi',
                    'min_length' => 'Nama minimal 3 karakter',
                    'max_length' => 'Nama maksimal 100 karakter'
                ]
            ],
            'tingkat' => [
                'label' => 'Tingkat',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[6]',
                'errors' => [
                    'required' => 'Tingkat wajib diisi',
                    'integer' => 'Tingkat harus bilangan bulat',
                    'greater_than_equal_to' => 'Tingkat minimal 1',
                    'less_than_equal_to' => 'Tingkat maksimal 6'
                ]
            ],
            'kapasitas_siswa' => [
                'label' => 'Kapasitas Siswa',
                'rules' => 'required|integer|greater_than_equal_to[10]|less_than_equal_to[50]',
                'errors' => [
                    'required' => 'Kapasitas wajib diisi',
                    'integer' => 'Kapasitas harus bilangan bulat',
                    'greater_than_equal_to' => 'Kapasitas minimal 10 siswa',
                    'less_than_equal_to' => 'Kapasitas maksimal 50 siswa'
                ]
            ],
            'jurusan' => [
                'label' => 'Jurusan',
                'rules' => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Jurusan wajib diisi',
                    'max_length' => 'Jurusan maksimal 50 karakter'
                ]
            ],
            'tahun_ajaran' => [
                'label' => 'Tahun Ajaran',
                'rules' => 'required|min_length[9]|max_length[9]',
                'errors' => [
                    'required' => 'Tahun ajaran wajib diisi',
                    'min_length' => 'Format tahun ajaran YYYY/YYYY',
                    'max_length' => 'Format tahun ajaran YYYY/YYYY'
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
        if ($this->Kelas_model->check_kode_exists($this->input->post('kode_kelas'), $id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Kode kelas sudah digunakan',
                    'errors' => ['kode_kelas' => 'Kode kelas sudah digunakan']
                ]));
            return;
        }

        $data = [
            'kode_kelas' => strtoupper($this->input->post('kode_kelas')),
            'nama_kelas' => $this->input->post('nama_kelas'),
            'tingkat' => $this->input->post('tingkat'),
            'kapasitas_siswa' => intval($this->input->post('kapasitas_siswa')),
            'jurusan' => $this->input->post('jurusan'),
            // tahun_ajaran removed - not in DB schema
        ];

        $result = $this->Kelas_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data kelas berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data kelas'
                ]));
        }
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $kelas = $this->Kelas_model->get_by_id($id);
        if (!$kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data kelas tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Kelas_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data kelas berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data kelas'
                ]));
        }
    }

    public function get_detail($id)
    {
        $kelas = $this->Kelas_model->get_by_id($id);
        
        if ($kelas) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $kelas
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data kelas tidak ditemukan'
                ]));
        }
    }
}
