<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

/**
 * Controller untuk CRUD Jam Pelajaran
 * DB: slot, waktu_mulai, waktu_selesai, durasi_menit, is_istirahat, is_active
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
            'slot' => [
                'label' => 'Slot Ke-',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[16]',
                'errors' => [
                    'required' => 'Slot ke- wajib diisi',
                    'integer' => 'Slot ke- harus bilangan bulat',
                    'greater_than_equal_to' => 'Slot ke- minimal 1',
                    'less_than_equal_to' => 'Slot ke- maksimal 16'
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
            'durasi_menit' => [
                'label' => 'Durasi (menit)',
                'rules' => 'required|integer|greater_than_equal_to[15]|less_than_equal_to[120]',
                'errors' => [
                    'required' => 'Durasi wajib diisi',
                    'integer' => 'Durasi harus bilangan bulat',
                    'greater_than_equal_to' => 'Durasi minimal 15 menit',
                    'less_than_equal_to' => 'Durasi maksimal 120 menit'
                ]
            ],
            'is_istirahat' => [
                'label' => 'Istirahat',
                'rules' => 'integer|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Nilai istirahat harus 0 atau 1'
                ]
            ]',
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
            'slot' => $this->input->post('slot'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'durasi_menit' => $this->input->post('durasi_menit'),
            'is_istirahat' => intval($this->input->post('is_istirahat') ?: 0),
            'is_active' => 1,
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
            'slot' => [
                'label' => 'Slot Ke-',
                'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[16]',
                'errors' => [
                    'required' => 'Slot ke- wajib diisi',
                    'integer' => 'Slot ke- harus bilangan bulat',
                    'greater_than_equal_to' => 'Slot ke- minimal 1',
                    'less_than_equal_to' => 'Slot ke- maksimal 16'
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
            'durasi_menit' => [
                'label' => 'Durasi (menit)',
                'rules' => 'required|integer|greater_than_equal_to[15]|less_than_equal_to[120]',
                'errors' => [
                    'required' => 'Durasi wajib diisi',
                    'integer' => 'Durasi harus bilangan bulat',
                    'greater_than_equal_to' => 'Durasi minimal 15 menit',
                    'less_than_equal_to' => 'Durasi maksimal 120 menit'
                ]
            ],
            'is_istirahat' => [
                'label' => 'Istirahat',
                'rules' => 'integer|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Nilai istirahat harus 0 atau 1'
                ]
            ],
            'is_active' => [
                'label' => 'Aktif',
                'rules' => 'integer|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Nilai aktif harus 0 atau 1'
                ]
            ]',
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
            'slot' => $this->input->post('slot'),
            'waktu_mulai' => $this->input->post('waktu_mulai'),
            'waktu_selesai' => $this->input->post('waktu_selesai'),
            'durasi_menit' => $this->input->post('durasi_menit'),
            'is_istirahat' => intval($this->input->post('is_istirahat') ?: 0),
            'is_active' => intval($this->input->post('is_active', 1)),
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