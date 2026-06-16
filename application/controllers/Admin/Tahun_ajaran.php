<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Tahun Ajaran
 */
class Tahun_ajaran extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tahun_ajaran_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('admin/tahun_ajaran_list');
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $this->form_validation->set_rules([
            'tahun' => [
                'label' => 'Tahun Ajaran',
                'rules' => 'required|min_length[9]|max_length[9]',
                'errors' => [
                    'required' => 'Tahun ajaran wajib diisi',
                    'min_length' => 'Format tahun ajaran YYYY/YYYY',
                    'max_length' => 'Format tahun ajaran YYYY/YYYY'
                ]
            ],
            'semester' => [
                'label' => 'Semester',
                'rules' => 'required|in_list[ganjil,genap]',
                'errors' => [
                    'required' => 'Semester wajib dipilih',
                    'in_list' => 'Semester harus ganjil atau genap'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[aktif,tidak_aktif]',
                'errors' => [
                    'required' => 'Status wajib dipilih',
                    'in_list' => 'Status harus aktif atau tidak_aktif'
                ]
            ],
            'tanggal_mulai' => [
                'label' => 'Tanggal Mulai',
                'rules' => 'required|callback_check_date_format',
                'errors' => [
                    'required' => 'Tanggal mulai wajib diisi',
                    'check_date_format' => 'Format tanggal harus YYYY-MM-DD'
                ]
            ],
            'tanggal_selesai' => [
                'label' => 'Tanggal Selesai',
                'rules' => 'required|callback_check_date_format|callback_check_tanggal',
                'errors' => [
                    'required' => 'Tanggal selesai wajib diisi',
                    'check_date_format' => 'Format tanggal harus YYYY-MM-DD',
                    'check_tanggal' => 'Tanggal selesai harus lebih besar dari tanggal mulai'
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

        // Cek tahun ajaran unik
        if ($this->Tahun_ajaran_model->check_tahun_exists($this->input->post('tahun_mulai'), $this->input->post('semester'))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Tahun ajaran dan semester sudah digunakan',
                    'errors' => ['tahun' => 'Kombinasi tahun ajaran dan semester sudah ada']
                ]));
            return;
        }

        $data = [
            'tahun_mulai' => $this->input->post('tahun_mulai'),
            'tahun_selesai' => $this->input->post('tahun_selesai'),
            'semester' => $this->input->post('semester'),
            'is_aktif' => ($this->input->post('status') === 'aktif') ? 1 : 0,
            'status' => ($this->input->post('status') === 'aktif') ? 'active' : 'draft',
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai')
        ];

        // Jika status aktif, set semua yang lain menjadi tidak aktif
        if ($this->input->post('status') === 'aktif') {
            $this->Tahun_ajaran_model->set_all_inactive();
        }

        $result = $this->Tahun_ajaran_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data tahun ajaran berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data tahun ajaran'
                ]));
        }
    }

    public function check_date_format($date)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->form_validation->set_message('check_date_format', 'Format tanggal harus YYYY-MM-DD');
            return FALSE;
        }
        return TRUE;
    }

    public function check_tanggal()
    {
        $mulai = $this->input->post('tanggal_mulai');
        $selesai = $this->input->post('tanggal_selesai');

        if ($selesai <= $mulai) {
            $this->form_validation->set_message('check_tanggal', 'Tanggal selesai harus lebih besar dari tanggal mulai');
            return FALSE;
        }
        return TRUE;
    }

    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $ta = $this->Tahun_ajaran_model->get_by_id($id);
        if (!$ta) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data tahun ajaran tidak ditemukan'
                ]));
            return;
        }

        $this->form_validation->set_rules([
            'tahun' => [
                'label' => 'Tahun Ajaran',
                'rules' => 'required|min_length[9]|max_length[9]',
                'errors' => [
                    'required' => 'Tahun ajaran wajib diisi',
                    'min_length' => 'Format tahun ajaran YYYY/YYYY',
                    'max_length' => 'Format tahun ajaran YYYY/YYYY'
                ]
            ],
            'semester' => [
                'label' => 'Semester',
                'rules' => 'required|in_list[ganjil,genap]',
                'errors' => [
                    'required' => 'Semester wajib dipilih',
                    'in_list' => 'Semester harus ganjil atau genap'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[aktif,tidak_aktif]',
                'errors' => [
                    'required' => 'Status wajib dipilih',
                    'in_list' => 'Status harus aktif atau tidak_aktif'
                ]
            ],
            'tanggal_mulai' => [
                'label' => 'Tanggal Mulai',
                'rules' => 'required|callback_check_date_format',
                'errors' => [
                    'required' => 'Tanggal mulai wajib diisi',
                    'check_date_format' => 'Format tanggal harus YYYY-MM-DD'
                ]
            ],
            'tanggal_selesai' => [
                'label' => 'Tanggal Selesai',
                'rules' => 'required|callback_check_date_format|callback_check_tanggal',
                'errors' => [
                    'required' => 'Tanggal selesai wajib diisi',
                    'check_date_format' => 'Format tanggal harus YYYY-MM-DD',
                    'check_tanggal' => 'Tanggal selesai harus lebih besar dari tanggal mulai'
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

        // Cek tahun ajaran unik (kecuali untuk record ini)
        if ($this->Tahun_ajaran_model->check_tahun_exists($this->input->post('tahun_mulai'), $this->input->post('semester'), $id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Tahun ajaran dan semester sudah digunakan',
                    'errors' => ['tahun' => 'Kombinasi tahun ajaran dan semester sudah ada']
                ]));
            return;
        }

        $data = [
            'tahun_mulai' => $this->input->post('tahun_mulai'),
            'tahun_selesai' => $this->input->post('tahun_selesai'),
            'semester' => $this->input->post('semester'),
            'is_aktif' => ($this->input->post('status') === 'aktif') ? 1 : 0,
            'status' => ($this->input->post('status') === 'aktif') ? 'active' : 'draft',
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai')
        ];

        // Jika status aktif, set semua yang lain menjadi tidak aktif
        if ($this->input->post('status') === 'aktif') {
            $this->Tahun_ajaran_model->set_all_inactive($id);
        }

        $result = $this->Tahun_ajaran_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data tahun ajaran berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data tahun ajaran'
                ]));
        }
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $ta = $this->Tahun_ajaran_model->get_by_id($id);
        if (!$ta) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data tahun ajaran tidak ditemukan'
                ]));
            return;
        }

        // Tidak boleh hapus jika status aktif
        if (($ta['is_aktif'] ?? 0) == 1 || ($ta['status'] ?? '') === 'active') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Tidak dapat menghapus tahun ajaran yang sedang aktif'
                ]));
            return;
        }

        $result = $this->Tahun_ajaran_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data tahun ajaran berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data tahun ajaran'
                ]));
        }
    }

    public function get_detail($id)
    {
        $ta = $this->Tahun_ajaran_model->get_by_id($id);
        
        if ($ta) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $ta
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data tahun ajaran tidak ditemukan'
                ]));
        }
    }

    /**
     * Get tahun ajaran aktif
     */
    public function get_aktif()
    {
        $ta = $this->Tahun_ajaran_model->get_aktif();
        
        if ($ta) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $ta
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Tidak ada tahun ajaran aktif'
                ]));
        }
    }
}
