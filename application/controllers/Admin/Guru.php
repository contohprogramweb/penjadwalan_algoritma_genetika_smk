<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Guru
 * Field sesuai DB: nip, nama_lengkap, jenis_kelamin,
 *                  jam_min_minggu, jam_maks_minggu, status_aktif
 */
require_once APPPATH . 'core/MY_Controller.php';

class Guru extends MY_Controller {

    protected $allowed_roles = ['admin'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Guru_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['page_title'] = 'Data Guru';
        $this->load->view('admin/guru_list', $data);
    }

    private function _json($success, $message, $extra = [])
    {
        $payload = array_merge(['success' => $success, 'message' => $message], $extra);
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    private function _validate_guru()
    {
        $rules = [
            ['field' => 'nip',          'label' => 'NIP',           'rules' => 'required|exact_length[18]|numeric'],
            ['field' => 'nama_lengkap', 'label' => 'Nama Lengkap',  'rules' => 'required|min_length[3]|max_length[100]'],
            ['field' => 'jenis_kelamin','label' => 'Jenis Kelamin', 'rules' => 'required|in_list[L,P]'],
            ['field' => 'tempat_lahir', 'label' => 'Tempat Lahir',  'rules' => 'required|max_length[50]'],
            ['field' => 'tanggal_lahir','label' => 'Tanggal Lahir', 'rules' => 'required|valid_date'],
            ['field' => 'pendidikan_terakhir','label' => 'Pendidikan', 'rules' => 'required|in_list[S1,S2,S3,D3,D4]'],
            ['field' => 'status_kepegawaian','label' => 'Status Kepegawaian', 'rules' => 'required|in_list[pns,honorer,ttk]'],
            ['field' => 'status_aktif', 'label' => 'Status Aktif',  'rules' => 'required|in_list[0,1]'],
        ];
        $this->form_validation->set_rules($rules);
        return $this->form_validation->run();
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') { show_error('Method tidak diizinkan', 405); }

        if ($this->_validate_guru() === FALSE) {
            $this->_json(FALSE, 'Validasi gagal', ['errors' => $this->form_validation->error_array()]);
            return;
        }

        $nip = $this->input->post('nip');
        if ($this->Guru_model->check_nip_exists($nip)) {
            $this->_json(FALSE, 'NIP sudah terdaftar'); return;
        }

        $data = [
            'nip'                => $nip,
            'nama_lengkap'       => $this->input->post('nama_lengkap'),
            'jenis_kelamin'      => $this->input->post('jenis_kelamin'),
            'tempat_lahir'       => $this->input->post('tempat_lahir'),
            'tanggal_lahir'      => $this->input->post('tanggal_lahir'),
            'pendidikan_terakhir'=> $this->input->post('pendidikan_terakhir'),
            'status_kepegawaian' => $this->input->post('status_kepegawaian'),
            'status_aktif'       => intval($this->input->post('status_aktif')),
        ];

        $ok = $this->Guru_model->insert($data);
        $this->_json((bool)$ok, $ok ? 'Data guru berhasil ditambahkan' : 'Gagal menambahkan data guru');
    }

    public function edit($id)
    {
        if ($this->input->method() !== 'post') { show_error('Method tidak diizinkan', 405); }

        if (!$this->Guru_model->get_by_id($id)) {
            $this->_json(FALSE, 'Data guru tidak ditemukan'); return;
        }

        if ($this->_validate_guru() === FALSE) {
            $this->_json(FALSE, 'Validasi gagal', ['errors' => $this->form_validation->error_array()]);
            return;
        }

        $data = [
            'nip'                => $this->input->post('nip'),
            'nama_lengkap'       => $this->input->post('nama_lengkap'),
            'jenis_kelamin'      => $this->input->post('jenis_kelamin'),
            'tempat_lahir'       => $this->input->post('tempat_lahir'),
            'tanggal_lahir'      => $this->input->post('tanggal_lahir'),
            'pendidikan_terakhir'=> $this->input->post('pendidikan_terakhir'),
            'status_kepegawaian' => $this->input->post('status_kepegawaian'),
            'status_aktif'       => intval($this->input->post('status_aktif')),
        ];

        $ok = $this->Guru_model->update($id, $data);
        $this->_json((bool)$ok, $ok ? 'Data guru berhasil diperbarui' : 'Gagal memperbarui data guru');
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') { show_error('Method tidak diizinkan', 405); }

        if (!$this->Guru_model->get_by_id($id)) {
            $this->_json(FALSE, 'Data guru tidak ditemukan'); return;
        }

        $ok = $this->Guru_model->delete($id);
        $this->_json((bool)$ok, $ok ? 'Data guru berhasil dihapus' : 'Gagal menghapus data guru');
    }

    public function get_detail($id)
    {
        $guru = $this->Guru_model->get_by_id($id);
        if ($guru) {
            $this->_json(TRUE, 'OK', ['data' => $guru]);
        } else {
            $this->_json(FALSE, 'Data guru tidak ditemukan');
        }
    }
}
