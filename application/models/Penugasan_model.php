<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Penugasan Guru
 * Sesuai SRS Bab 11.6 - Modul Penugasan (Waka Kurikulum)
 */
class Penugasan_model extends CI_Model {

    private $table = 'penugasan_guru';
    private $primary_key = 'id_penugasan';
    private $column_order = ['pg.id_penugasan', 'g.nama_lengkap', 'mp.nama_mapel', 'k.nama_kelas', 'pg.semester', 'pg.jam_per_minggu'];
    private $column_search = ['g.nama_lengkap', 'mp.nama_mapel', 'k.nama_kelas', 'r.nama_ruangan'];
    private $order = ['pg.id_penugasan' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get datatables data dengan join ke tabel terkait
     */
    public function get_datatables($id_tahun_ajaran = null, $semester = null)
    {
        $this->_get_datatables_query($id_tahun_ajaran, $semester);
        
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Count total records
     */
    public function count_all($id_tahun_ajaran = null, $semester = null)
    {
        $this->db->from($this->table . ' pg');
        
        if ($id_tahun_ajaran) {
            $this->db->where('pg.id_tahun_ajaran', $id_tahun_ajaran);
        }
        
        if ($semester) {
            $this->db->where('pg.semester', $semester);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Count filtered records
     */
    public function count_filtered($id_tahun_ajaran = null, $semester = null)
    {
        $this->_get_datatables_query($id_tahun_ajaran, $semester);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Build datatables query dengan join
     */
    private function _get_datatables_query($id_tahun_ajaran = null, $semester = null)
    {
        // Join dengan tabel terkait
        $this->db->from($this->table . ' pg');
        $this->db->join('guru g', 'pg.id_guru = g.id_guru', 'left');
        $this->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id_mapel', 'left');
        $this->db->join('kelas k', 'pg.id_kelas = k.id_kelas', 'left');
        $this->db->join('ruangan r', 'pg.id_ruangan = r.id_ruangan', 'left');
        $this->db->join('tahun_ajaran ta', 'pg.id_tahun_ajaran = ta.id_tahun_ajaran', 'left');
        
        // Filter tahun ajaran dan semester
        if ($id_tahun_ajaran) {
            $this->db->where('pg.id_tahun_ajaran', $id_tahun_ajaran);
        }
        
        if ($semester) {
            $this->db->where('pg.semester', $semester);
        }
        
        // Search
        if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
            $keyword = $_POST['search']['value'];
            $this->db->group_start();
            foreach ($this->column_search as $i => $column) {
                if ($i === 0) {
                    $this->db->like($column, $keyword);
                } else {
                    $this->db->or_like($column, $keyword);
                }
            }
            $this->db->group_end();
        }

        // Order
        if (isset($_POST['order'])) {
            $this->db->order_by(
                $this->column_order[$_POST['order']['0']['column']], 
                $_POST['order']['0']['dir']
            );
        } elseif (isset($this->order)) {
            $order = $this->order;
            foreach ($order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    /**
     * Get penugasan by ID
     */
    public function get_by_id($id)
    {
        $this->db->select("pg.*, g.nama_lengkap as nama_guru, mp.nama_mapel, k.nama_kelas, r.nama_ruangan, CONCAT(ta.tahun_mulai, '/', ta.tahun_selesai) as tahun");
        $this->db->from($this->table . ' pg');
        $this->db->join('guru g', 'pg.id_guru = g.id_guru', 'left');
        $this->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id_mapel', 'left');
        $this->db->join('kelas k', 'pg.id_kelas = k.id_kelas', 'left');
        $this->db->join('ruangan r', 'pg.id_ruangan = r.id_ruangan', 'left');
        $this->db->join('tahun_ajaran ta', 'pg.id_tahun_ajaran = ta.id_tahun_ajaran', 'left');
        $this->db->where('pg.id_penugasan', $id);
        
        return $this->db->get()->row_array();
    }

    /**
     * Insert new penugasan
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update penugasan
     */
    public function update($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete penugasan
     */
    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Check duplikasi penugasan (guru + mapel + kelas + tahun ajaran + semester)
     */
    public function check_duplicate($id_guru, $id_mapel, $id_kelas, $id_tahun_ajaran, $semester, $exclude_id = null)
    {
        $this->db->where('id_guru', $id_guru);
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        $this->db->where('semester', $semester);
        
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Cek kelas yang belum punya penugasan untuk tahun ajaran tertentu
     * Sesuai SRS Bab 11.6.2 - Checklist kesiapan data
     */
    public function get_kelas_without_penugasan($id_tahun_ajaran, $semester = null)
    {
        $this->db->select('k.id_kelas, k.nama_kelas, k.tingkat');
        $this->db->from('kelas k');
        $this->db->join('penugasan_guru pg', 'k.id_kelas = pg.id_kelas AND pg.id_tahun_ajaran = ' . $this->db->escape($id_tahun_ajaran), 'left');
        $this->db->where('pg.id_penugasan IS NULL');
        
        if ($semester) {
            $this->db->where('pg.semester', $semester);
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Get total penugasan per kelas
     */
    public function get_penugasan_per_kelas($id_tahun_ajaran, $semester = null)
    {
        $this->db->select('k.id_kelas, k.nama_kelas, COUNT(pg.id_penugasan) as jumlah_penugasan');
        $this->db->from('kelas k');
        $this->db->join('penugasan_guru pg', 'k.id_kelas = pg.id_kelas', 'left');
        $this->db->where('pg.id_tahun_ajaran', $id_tahun_ajaran);
        
        if ($semester) {
            $this->db->where('pg.semester', $semester);
        }
        
        $this->db->group_by('k.id_kelas, k.nama_kelas');
        return $this->db->get()->result_array();
    }

    /**
     * Get beban mengajar guru (total jam per minggu)
     */
    public function get_beban_guru($id_guru, $id_tahun_ajaran, $semester = null)
    {
        $this->db->select_sum('jam_per_minggu', 'total_jam');
        $this->db->from($this->table);
        $this->db->where('id_guru', $id_guru);
        $this->db->where('id_tahun_ajaran', $id_tahun_ajaran);
        
        if ($semester) {
            $this->db->where('semester', $semester);
        }
        
        return $this->db->get()->row_array();
    }

    /**
     * Get all penugasan untuk dropdown/select2
     */
    public function get_all_for_dropdown($id_tahun_ajaran = null, $semester = null)
    {
        $this->db->select('pg.id_penugasan, g.nama_lengkap as nama_guru, mp.nama_mapel, k.nama_kelas');
        $this->db->from($this->table . ' pg');
        $this->db->join('guru g', 'pg.id_guru = g.id_guru', 'left');
        $this->db->join('mata_pelajaran mp', 'pg.id_mapel = mp.id_mapel', 'left');
        $this->db->join('kelas k', 'pg.id_kelas = k.id_kelas', 'left');
        
        if ($id_tahun_ajaran) {
            $this->db->where('pg.id_tahun_ajaran', $id_tahun_ajaran);
        }
        
        if ($semester) {
            $this->db->where('pg.semester', $semester);
        }
        
        $this->db->order_by('g.nama_lengkap', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Count penugasan by tahun ajaran
     */
    public function count_by_tahun($id_tahun_ajaran)
    {
        return $this->count_all($id_tahun_ajaran);
    }
}