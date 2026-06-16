<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller untuk DataTables Server-Side Processing
 * Semua method mengembalikan object bernama kolom (bukan indexed array)
 * agar cocok dengan konfigurasi DataTables views yang menggunakan `data: 'nama_kolom'`
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

    private function _json($data, $total, $filtered)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'draw'            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
                'recordsTotal'    => intval($total),
                'recordsFiltered' => intval($filtered),
                'data'            => $data,
            ]));
    }

    /** DataTables Guru */
    public function guru()
    {
        $list     = $this->Guru_model->get_datatables();
        $total    = $this->Guru_model->count_all();
        $filtered = $this->Guru_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $status_label = $r['status_aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
            $data[] = [
                'no'          => $no,
                'nip'         => htmlspecialchars($r['nip']),
                'nama'        => htmlspecialchars($r['nama_lengkap']),
                'email'       => '-',
                'no_hp'       => '-',
                'status'      => $status_label,
                'jam_mengajar'=> ($r['jam_min_minggu'] ?? 0) . ' - ' . ($r['jam_maks_minggu'] ?? 0) . ' jam/mgg',
                'aksi'        =>
                    '<button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="'.$r['id_guru'].'"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="'.$r['id_guru'].'"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }

    /** DataTables Mapel */
    public function mapel()
    {
        $list     = $this->Mapel_model->get_datatables();
        $total    = $this->Mapel_model->count_all();
        $filtered = $this->Mapel_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $data[] = [
                'no'           => $no,
                'kode'         => htmlspecialchars($r['kode_mapel']),
                'nama'         => htmlspecialchars($r['nama_mapel']),
                'tipe'         => '<span class="badge badge-' . ($r['tipe'] === 'praktikum' ? 'warning' : 'info') . '">' . ucfirst($r['tipe']) . '</span>',
                'jp_per_minggu'=> $r['jam_per_minggu'] . ' JP',
                'semester'     => 'Kelompok ' . $r['kelompok'],
                'aksi'         =>
                    '<button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="'.$r['id_mapel'].'"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="'.$r['id_mapel'].'"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }

    /** DataTables Kelas */
    public function kelas()
    {
        $list     = $this->Kelas_model->get_datatables();
        $total    = $this->Kelas_model->count_all();
        $filtered = $this->Kelas_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $data[] = [
                'no'           => $no,
                'kode'         => htmlspecialchars($r['kode_kelas']),
                'nama'         => htmlspecialchars($r['nama_kelas']),
                'tingkat'      => 'Kelas ' . $r['tingkat'],
                'jurusan'      => htmlspecialchars($r['jurusan']),
                'kapasitas'    => $r['kapasitas_siswa'] . ' siswa',
                'tahun_ajaran' => '-',
                'aksi'         =>
                    '<button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="'.$r['id_kelas'].'"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="'.$r['id_kelas'].'"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }

    /** DataTables Ruangan */
    public function ruangan()
    {
        $list     = $this->Ruangan_model->get_datatables();
        $total    = $this->Ruangan_model->count_all();
        $filtered = $this->Ruangan_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $tipe_map = ['kelas' => 'info', 'lab' => 'warning', 'bengkel' => 'danger', 'lapangan' => 'success', 'aula' => 'primary'];
            $badge_cls = $tipe_map[$r['tipe']] ?? 'secondary';
            $data[] = [
                'no'       => $no,
                'kode'     => htmlspecialchars($r['kode_ruangan']),
                'nama'     => htmlspecialchars($r['nama_ruangan']),
                'kapasitas'=> $r['kapasitas'] . ' orang',
                'jenis'    => '<span class="badge badge-'.$badge_cls.'">'.ucfirst($r['tipe']).'</span>',
                'lantai'   => $r['lantai'] ? 'Lantai ' . $r['lantai'] : '-',
                'aksi'     =>
                    '<button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="'.$r['id_ruangan'].'"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="'.$r['id_ruangan'].'"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }

    /** DataTables Jam Pelajaran */
    public function jam()
    {
        $list     = $this->Jam_model->get_datatables();
        $total    = $this->Jam_model->count_all();
        $filtered = $this->Jam_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $ket = $r['is_istirahat'] ? '<span class="badge badge-warning">Istirahat</span>' : '<span class="badge badge-success">Reguler</span>';
            $data[] = [
                'no'           => $no,
                'id_jam'       => $r['id_jam'],
                'slot'         => 'Jam ke-' . $r['slot'],
                'waktu_mulai'  => $r['waktu_mulai'],
                'waktu_selesai'=> $r['waktu_selesai'],
                'durasi_menit' => $r['durasi_menit'] . ' menit',
                'keterangan'   => $ket,
                'aksi'         =>
                    '<button class="btn btn-sm btn-warning mr-1" onclick="editData('.$r['id_jam'].')"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger" onclick="konfirmasiHapus('.$r['id_jam'].')"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }

    /** DataTables Tahun Ajaran */
    public function tahun_ajaran()
    {
        $list     = $this->Tahun_ajaran_model->get_datatables();
        $total    = $this->Tahun_ajaran_model->count_all();
        $filtered = $this->Tahun_ajaran_model->count_filtered();

        $no = intval($_POST['start'] ?? 0);
        $data = [];
        foreach ($list as $r) {
            $no++;
            $is_active = ($r['is_aktif'] == 1) || ($r['status'] === 'active');
            $status_html = $is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">'.ucfirst($r['status'] ?? 'Draft').'</span>';
            $nama_tahun = $r['tahun_mulai'] . '/' . $r['tahun_selesai'] . ' ' . ucfirst($r['semester']);
            $data[] = [
                'no'              => $no,
                'tahun'           => $nama_tahun,
                'semester'        => ucfirst($r['semester']),
                'tanggal_mulai'   => $r['tanggal_mulai'] ?? '-',
                'tanggal_selesai' => $r['tanggal_selesai'] ?? '-',
                'status'          => $status_html,
                'aksi'            =>
                    '<button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="'.$r['id_tahun_ajaran'].'"><i class="fas fa-edit"></i></button>' .
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="'.$r['id_tahun_ajaran'].'"><i class="fas fa-trash"></i></button>',
            ];
        }
        $this->_json($data, $total, $filtered);
    }
}
