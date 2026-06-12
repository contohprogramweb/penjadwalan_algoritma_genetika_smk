<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk DataTables Server-Side Processing
 * Referensi: SRS Bab 15.1
 */
require_once APPPATH . 'core/MY_Controller.php';

class Datatables extends MY_Controller {

    protected $allowed_roles = ['admin', 'waka'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Guru_model');
        $this->load->model('Mapel_model');
        $this->load->model('Kelas_model');
        $this->load->model('Ruangan_model');
        $this->load->model('Jam_model');
        $this->load->model('Tahun_ajaran_model');
    }

    /**
     * DataTables untuk Guru
     * Response format sesuai SRS Bab 15.1
     */
    public function guru()
    {
        $list = $this->Guru_model->get_datatables();
        $total = $this->Guru_model->count_all();
        $filtered = $this->Guru_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item['nip'];
            $row[] = $item['nuptk'];
            $row[] = $item['nama'];
            $row[] = $item['email'];
            $row[] = $item['no_hp'];
            $row[] = ucfirst($item['status']);
            $row[] = $item['jam_min'] . ' - ' . $item['jam_maks'];
            
            // Aksi buttons
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_guru'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_guru'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * DataTables untuk Mapel
     */
    public function mapel()
    {
        $list = $this->Mapel_model->get_datatables();
        $total = $this->Mapel_model->count_all();
        $filtered = $this->Mapel_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item['kode'];
            $row[] = $item['nama'];
            $row[] = ucfirst($item['tipe']);
            $row[] = $item['jp_per_minggu'];
            $row[] = 'Semester ' . $item['semester'];
            
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_mapel'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_mapel'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * DataTables untuk Kelas
     */
    public function kelas()
    {
        $list = $this->Kelas_model->get_datatables();
        $total = $this->Kelas_model->count_all();
        $filtered = $this->Kelas_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item['kode'];
            $row[] = $item['nama'];
            $row[] = 'Tingkat ' . $item['tingkat'];
            $row[] = $item['kapasitas'];
            $row[] = $item['jurusan'];
            $row[] = $item['tahun_ajaran'];
            
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_kelas'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_kelas'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * DataTables untuk Ruangan
     */
    public function ruangan()
    {
        $list = $this->Ruangan_model->get_datatables();
        $total = $this->Ruangan_model->count_all();
        $filtered = $this->Ruangan_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item['kode'];
            $row[] = $item['nama'];
            $row[] = $item['kapasitas'];
            $row[] = ucfirst($item['jenis']);
            $row[] = 'Lantai ' . $item['lantai'];
            
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_ruangan'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_ruangan'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * DataTables untuk Jam Pelajaran
     */
    public function jam()
    {
        $list = $this->Jam_model->get_datatables();
        $total = $this->Jam_model->count_all();
        $filtered = $this->Jam_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = 'Slot ke-' . $item['slot'];
            $row[] = $item['waktu_mulai'];
            $row[] = $item['waktu_selesai'];
            $row[] = $item['durasi_menit'] . ' menit';
            $row[] = $item['keterangan'] ?? '-';
            
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_jam'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_jam'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    /**
     * DataTables untuk Tahun Ajaran
     */
    public function tahun_ajaran()
    {
        $list = $this->Tahun_ajaran_model->get_datatables();
        $total = $this->Tahun_ajaran_model->count_all();
        $filtered = $this->Tahun_ajaran_model->count_filtered();

        $data = [];
        $no = $_POST['start'];
        
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item['tahun'];
            $row[] = ucfirst($item['semester']);
            $row[] = $item['tanggal_mulai'];
            $row[] = $item['tanggal_selesai'];
            $row[] = '<span class="badge badge-'.($item['status'] === 'aktif' ? 'success' : 'secondary').'">'.ucfirst(str_replace('_', ' ', $item['status'])).'</span>';
            
            $aksi = '
                <button class="btn btn-sm btn-info btn-edit" data-id="'.$item['id_tahun_ajaran'].'">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="'.$item['id_tahun_ajaran'].'">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            ';
            $row[] = $aksi;

            $data[] = $row;
        }

        $output = [
            'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}
