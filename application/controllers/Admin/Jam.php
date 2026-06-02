<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk CRUD Jam Pelajaran
 */
class Jam extends MY_Controller {

    protected $allowed_roles = ['admin'];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jam_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('admin/jam_list');
    }

    public function tambah()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $this->form_validation->set_rules([
            'jam_ke' => [
                'label' => 'Jam Ke-',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[16]',
                'errors' => [
                    'required' => 'Jam ke- wajib diisi',
                    'integer' => 'Jam ke- harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam ke- minimal 1',
                    'less_than_equal_to' => 'Jam ke- maksimal 16'
                ]
            ],
            'waktu_mulai' => [
                'label' => 'Waktu Mulai',
                'rules' => 'required|callback_check_time_format',
                'errors' => [
                    'required' => 'Waktu mulai wajib diisi',
                    'check_time_format' => 'Format waktu harus HH:MM'
                ]
            ],
            'waktu_selesai' => [
                'label' => 'Waktu Selesai',
                'rules' => 'required|callback_check_time_format|callback_check_waktu',
                'errors' => [
                    'required' => 'Waktu selesai wajib diisi',
                    'check_time_format' => 'Format waktu harus HH:MM',
                    'check_waktu' => 'Waktu selesai harus lebih besar dari waktu mulai'
                ]
            ],
            'durasi' => [
                'label' => 'Durasi (menit)',
                'rules' => 'required|integer|greater_than_equal_to[30]|less_than_equal_to[120]',
                'errors' => [
                    'required' => 'Durasi wajib diisi',
                    'integer' => 'Durasi harus bilangan bulat',
                    'greater_than_equal_to' => 'Durasi minimal 30 menit',
                    'less_than_equal_to' => 'Durasi maksimal 120 menit'
                ]
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'max_length[200]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 200 karakter'
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

        $data = [
            'jam_ke' => $this->input->post('jam_ke'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'durasi' => $this->input->post('durasi'),
            'keterangan' => $this->input->post('keterangan')
        ];

        $result = $this->Jam_model->insert($data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data jam pelajaran berhasil ditambahkan'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menambahkan data jam pelajaran'
                ]));
        }
    }

    public function check_time_format($time)
    {
        if (!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $time)) {
            $this->form_validation->set_message('check_time_format', 'Format waktu harus HH:MM (24 jam)');
            return FALSE;
        }
        return TRUE;
    }

    public function check_waktu()
    {
        $mulai = $this->input->post('waktu_mulai');
        $selesai = $this->input->post('waktu_selesai');

        if ($selesai <= $mulai) {
            $this->form_validation->set_message('check_waktu', 'Waktu selesai harus lebih besar dari waktu mulai');
            return FALSE;
        }
        return TRUE;
    }

    public function edit($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $jam = $this->Jam_model->get_by_id($id);
        if (!$jam) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data jam pelajaran tidak ditemukan'
                ]));
            return;
        }

        $this->form_validation->set_rules([
            'jam_ke' => [
                'label' => 'Jam Ke-',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[16]',
                'errors' => [
                    'required' => 'Jam ke- wajib diisi',
                    'integer' => 'Jam ke- harus bilangan bulat',
                    'greater_than_equal_to' => 'Jam ke- minimal 1',
                    'less_than_equal_to' => 'Jam ke- maksimal 16'
                ]
            ],
            'waktu_mulai' => [
                'label' => 'Waktu Mulai',
                'rules' => 'required|callback_check_time_format',
                'errors' => [
                    'required' => 'Waktu mulai wajib diisi',
                    'check_time_format' => 'Format waktu harus HH:MM'
                ]
            ],
            'waktu_selesai' => [
                'label' => 'Waktu Selesai',
                'rules' => 'required|callback_check_time_format|callback_check_waktu',
                'errors' => [
                    'required' => 'Waktu selesai wajib diisi',
                    'check_time_format' => 'Format waktu harus HH:MM',
                    'check_waktu' => 'Waktu selesai harus lebih besar dari waktu mulai'
                ]
            ],
            'durasi' => [
                'label' => 'Durasi (menit)',
                'rules' => 'required|integer|greater_than_equal_to[30]|less_than_equal_to[120]',
                'errors' => [
                    'required' => 'Durasi wajib diisi',
                    'integer' => 'Durasi harus bilangan bulat',
                    'greater_than_equal_to' => 'Durasi minimal 30 menit',
                    'less_than_equal_to' => 'Durasi maksimal 120 menit'
                ]
            ],
            'keterangan' => [
                'label' => 'Keterangan',
                'rules' => 'max_length[200]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 200 karakter'
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

        $data = [
            'jam_ke' => $this->input->post('jam_ke'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'durasi' => $this->input->post('durasi'),
            'keterangan' => $this->input->post('keterangan')
        ];

        $result = $this->Jam_model->update($id, $data);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data jam pelajaran berhasil diperbarui'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal memperbarui data jam pelajaran'
                ]));
        }
    }

    public function hapus($id)
    {
        if ($this->input->method() !== 'post') {
            show_error('Method tidak diizinkan', 405);
        }

        $jam = $this->Jam_model->get_by_id($id);
        if (!$jam) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data jam pelajaran tidak ditemukan'
                ]));
            return;
        }

        $result = $this->Jam_model->delete($id);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'message' => 'Data jam pelajaran berhasil dihapus'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Gagal menghapus data jam pelajaran'
                ]));
        }
    }

    public function get_detail($id)
    {
        $jam = $this->Jam_model->get_by_id($id);
        
        if ($jam) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => TRUE,
                    'data' => $jam
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => FALSE,
                    'message' => 'Data jam pelajaran tidak ditemukan'
                ]));
        }
    }
}
