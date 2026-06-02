<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Jadwal_model
 * Deskripsi: Mengelola operasi database untuk jadwal pelajaran.
 * Referensi SRS: Bab 11.8 (Review & Edit Jadwal)
 */
class Jadwal_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'jadwal';
        $this->table_detail = 'jadwal_detail';
    }

    /**
     * Method: get_jadwal_by_tahun_ajaran($id_tahun_ajaran)
     * Ambil semua detail jadwal untuk tahun ajaran tertentu dengan join lengkap.
     * @param int $id_tahun_ajaran
     * @return array of objects
     */
    public function get_jadwal_by_tahun_ajaran($id_tahun_ajaran)
    {
        $this->db->select('
            jd.id as id_jadwal_detail,
            jd.id_jadwal,
            jd.hari,
            jd.slot,
            jd.id_kelas,
            k.nama as nama_kelas,
            jd.id_penugasan,
            g.nama as nama_guru,
            mp.nama as nama_mapel,
            mp.sifat as mapel_sifat,
            jd.id_ruangan,
            r.nama as nama_ruangan,
            jd.created_at,
            jd.updated_at
        ');
        $this->db->from($this->table_detail . ' jd');
        $this->db->join($this->table_name . ' j', 'jd.id_jadwal = j.id');
        $this->db->join('kelas k', 'jd.id_kelas = k.id');
        $this->db->join('penugasan_guru pg', 'jd.id_penugasan = pg.id');
        $this->db->join('guru g', 'pg.id_guru = g.id');
        $this->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id');
        $this->db->join('ruangan r', 'jd.id_ruangan = r.id');
        $this->db->where('j.id_tahun_ajaran', $id_tahun_ajaran);
        $this->db->order_by('jd.hari, jd.slot, k.nama');
        
        return $this->db->get()->result();
    }

    /**
     * Method: get_meta_jadwal($id_tahun_ajaran)
     * Ambil metadata jadwal (status, tanggal generate, dll) untuk tahun ajaran.
     * @param int $id_tahun_ajaran
     * @return object|null
     */
    public function get_meta_jadwal($id_tahun_ajaran)
    {
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        return $this->db->get($this->table_name)->row();
    }

    /**
     * Method: get_jam_pelajaran_per_hari()
     * Ambil daftar jam pelajaran yang aktif, diurutkan berdasarkan slot.
     * @return array of objects
     */
    public function get_jam_pelajaran_per_hari()
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('slot', 'ASC');
        return $this->db->get('jam_pelajaran')->result();
    }

    /**
     * Method: get_jadwal_detail_by_id($id_jadwal_detail)
     * Ambil satu record detail jadwal berdasarkan ID.
     * @param int $id_jadwal_detail
     * @return object|null
     */
    public function get_jadwal_detail_by_id($id_jadwal_detail)
    {
        $this->db->where('id', $id_jadwal_detail);
        return $this->db->get($this->table_detail)->row();
    }

    /**
     * Method: update_jadwal_detail($id_jadwal_detail, $data)
     * Update satu record detail jadwal.
     * @param int $id_jadwal_detail
     * @param array $data
     * @return bool
     */
    public function update_jadwal_detail($id_jadwal_detail, $data)
    {
        $this->db->where('id', $id_jadwal_detail);
        return $this->db->update($this->table_detail, $data);
    }

    /**
     * Method: update_meta_jadwal($id_tahun_ajaran, $data)
     * Update metadata jadwal (misal: status draft → approved).
     * @param int $id_tahun_ajaran
     * @param array $data
     * @return bool
     */
    public function update_meta_jadwal($id_tahun_ajaran, $data)
    {
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        return $this->db->update($this->table_name, $data);
    }

    /**
     * Method: check_guru_conflict($id_guru, $hari, $slot, $exclude_id_jadwal_detail)
     * Cek apakah guru sudah mengajar di kelas lain pada slot yang sama.
     * HC-01: Guru tidak double-booked.
     * @param int $id_guru
     * @param string $hari
     * @param int $slot
     * @param int $exclude_id_jadwal_detail - ID yang dikecualikan (untuk edit)
     * @return bool true jika ada konflik
     */
    public function check_guru_conflict($id_guru, $hari, $slot, $exclude_id_jadwal_detail = null)
    {
        $this->db->select('jd.id');
        $this->db->from($this->table_detail . ' jd');
        $this->db->join('penugasan_guru pg', 'jd.id_penugasan = pg.id');
        $this->db->where('pg.id_guru', $id_guru);
        $this->db->where('jd.hari', $hari);
        $this->db->where('jd.slot', $slot);
        
        if ($exclude_id_jadwal_detail) {
            $this->db->where('jd.id !=', $exclude_id_jadwal_detail);
        }
        
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Method: check_kelas_conflict($id_kelas, $hari, $slot, $exclude_id_jadwal_detail)
     * Cek apakah kelas sudah memiliki jadwal lain pada slot yang sama.
     * HC-02: Kelas tidak double-booked.
     * @param int $id_kelas
     * @param string $hari
     * @param int $slot
     * @param int $exclude_id_jadwal_detail
     * @return bool true jika ada konflik
     */
    public function check_kelas_conflict($id_kelas, $hari, $slot, $exclude_id_jadwal_detail = null)
    {
        $this->db->select('jd.id');
        $this->db->from($this->table_detail . ' jd');
        $this->db->where('jd.id_kelas', $id_kelas);
        $this->db->where('jd.hari', $hari);
        $this->db->where('jd.slot', $slot);
        
        if ($exclude_id_jadwal_detail) {
            $this->db->where('jd.id !=', $exclude_id_jadwal_detail);
        }
        
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Method: check_ruangan_conflict($id_ruangan, $hari, $slot, $exclude_id_jadwal_detail)
     * Cek apakah ruangan sudah digunakan oleh kelas lain pada slot yang sama.
     * HC-03: Ruangan tidak double-booked.
     * @param int $id_ruangan
     * @param string $hari
     * @param int $slot
     * @param int $exclude_id_jadwal_detail
     * @return bool true jika ada konflik
     */
    public function check_ruangan_conflict($id_ruangan, $hari, $slot, $exclude_id_jadwal_detail = null)
    {
        $this->db->select('jd.id');
        $this->db->from($this->table_detail . ' jd');
        $this->db->where('jd.id_ruangan', $id_ruangan);
        $this->db->where('jd.hari', $hari);
        $this->db->where('jd.slot', $slot);
        
        if ($exclude_id_jadwal_detail) {
            $this->db->where('jd.id !=', $exclude_id_jadwal_detail);
        }
        
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Method: create_jadwal($data)
     * Buat record jadwal baru (header).
     * @param array $data
     * @return int insert_id
     */
    public function create_jadwal($data)
    {
        $this->db->insert($this->table_name, $data);
        return $this->db->insert_id();
    }

    /**
     * Method: create_jadwal_detail($data)
     * Buat record detail jadwal baru.
     * @param array $data
     * @return int insert_id
     */
    public function create_jadwal_detail($data)
    {
        $this->db->insert($this->table_detail, $data);
        return $this->db->insert_id();
    }

    /**
     * Method: delete_jadwal($id_tahun_ajaran)
     * Hapus jadwal beserta detailnya untuk tahun ajaran tertentu.
     * Digunakan saat regenerate jadwal.
     * @param int $id_tahun_ajaran
     * @return bool
     */
    public function delete_jadwal($id_tahun_ajaran)
    {
        // Ambil ID jadwal
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        $jadwal = $this->db->get($this->table_name)->row();
        
        if (!$jadwal) {
            return false;
        }
        
        // Hapus detail dulu
        $this->db->where('id_jadwal', $jadwal->id);
        $this->db->delete($this->table_detail);
        
        // Hapus header
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        return $this->db->delete($this->table_name);
    }

    /**
     * Method: save_generated_schedule($id_tahun_ajaran, $schedule_data)
     * Simpan hasil generate GA ke database.
     * @param int $id_tahun_ajaran
     * @param array $schedule_data - Format: [hari][slot] = id_penugasan
     * @param array $ruangan_assignment - Format: [hari][slot] = id_ruangan
     * @return bool
     */
    public function save_generated_schedule($id_tahun_ajaran, $schedule_data, $ruangan_assignment = [])
    {
        // Mulai transaksi
        $this->db->trans_start();
        
        // Hapus jadwal lama jika ada
        $this->delete_jadwal($id_tahun_ajaran);
        
        // Buat jadwal baru
        $jadwal_data = [
            'id_tahun_ajaran' => $id_tahun_ajaran,
            'status' => 'draft',
            'generated_at' => date('Y-m-d H:i:s'),
            'generated_by' => $this->session->userdata('user_id') ?? 1
        ];
        
        $id_jadwal = $this->create_jadwal($jadwal_data);
        
        // Simpan detail jadwal
        foreach ($schedule_data as $hari => $slots) {
            foreach ($slots as $slot => $id_penugasan) {
                if ($id_penugasan) {
                    // Ambil id_kelas dari penugasan
                    $this->db->where('id', $id_penugasan);
                    $penugasan = $this->db->get('penugasan_guru')->row();
                    
                    if ($penugasan) {
                        // Tentukan ruangan (dari assignment atau default)
                        $id_ruangan = $ruangan_assignment[$hari][$slot] ?? $this->_get_default_ruangan($penugasan->id_kelas);
                        
                        $detail_data = [
                            'id_jadwal' => $id_jadwal,
                            'hari' => $hari,
                            'slot' => $slot,
                            'id_kelas' => $penugasan->id_kelas,
                            'id_penugasan' => $id_penugasan,
                            'id_ruangan' => $id_ruangan
                        ];
                        
                        $this->create_jadwal_detail($detail_data);
                    }
                }
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Internal: _get_default_ruangan($id_kelas)
     * Dapatkan ruangan default untuk kelas (bisa dikembangkan untuk logic lebih kompleks).
     * @param int $id_kelas
     * @return int|null
     */
    private function _get_default_ruangan($id_kelas)
    {
        // Ambil ruangan pertama yang tersedia (bisa disesuaikan dengan logic kapasitas)
        $this->db->where('tipe', 'kelas');
        $this->db->limit(1);
        $ruangan = $this->db->get('ruangan')->row();
        
        return $ruangan ? $ruangan->id : null;
    }

    /**
     * Method: get_konflik_list($id_tahun_ajaran)
     * Dapatkan daftar konflik pada jadwal yang sudah di-generate.
     * @param int $id_tahun_ajaran
     * @return array
     */
    public function get_konflik_list($id_tahun_ajaran)
    {
        $konflik = [];
        
        // Ambil semua jadwal detail
        $jadwal_list = $this->get_jadwal_by_tahun_ajaran($id_tahun_ajaran);
        
        // Group by hari dan slot untuk cek konflik
        $grouped = [];
        foreach ($jadwal_list as $j) {
            $key = $j->hari . '_' . $j->slot;
            $grouped[$key][] = $j;
        }
        
        // Cek konflik per slot
        foreach ($grouped as $key => $items) {
            if (count($items) > 1) {
                // Cek guru conflict
                $guru_ids = array_column($items, 'nama_guru');
                $guru_unique = array_unique($guru_ids);
                if (count($guru_unique) < count($guru_ids)) {
                    $konflik[] = [
                        'type' => 'HC-01',
                        'message' => 'Guru mengajar di multiple kelas pada slot yang sama',
                        'details' => $items
                    ];
                }
                
                // Cek ruangan conflict
                $ruangan_ids = array_column($items, 'id_ruangan');
                $ruangan_unique = array_unique($ruangan_ids);
                if (count($ruangan_unique) < count($ruangan_ids)) {
                    $konflik[] = [
                        'type' => 'HC-03',
                        'message' => 'Ruangan digunakan oleh multiple kelas pada slot yang sama',
                        'details' => $items
                    ];
                }
            }
        }
        
        return $konflik;
    }
}
