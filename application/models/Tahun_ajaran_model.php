<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Tahun Ajaran
 * 
 * Kolom DB: id_tahun_ajaran, tahun_mulai, tahun_selesai, semester, 
 *           is_aktif, tanggal_mulai, tanggal_selesai, status (draft/active/closed)
 */
class Tahun_ajaran_model extends CI_Model {

    private $table = 'tahun_ajaran';
    private $primary_key = 'id_tahun_ajaran';
    private $column_order = ['tahun_mulai', 'semester', 'status', 'tanggal_mulai', 'tanggal_selesai'];
    private $column_search = ['tahun_mulai', 'tahun_selesai', 'semester', 'status'];
    private $order = ['tahun_mulai' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

    private function _get_datatables_query()
    {
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

        if (isset($_POST['order'])) {
            $col_index = intval($_POST['order']['0']['column']);
            $col_name  = isset($this->column_order[$col_index]) ? $this->column_order[$col_index] : 'tahun_mulai';
            $this->db->order_by($col_name, $_POST['order']['0']['dir']);
        } else {
            foreach ($this->order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get active tahun ajaran (is_aktif=1 ATAU status='active')
     */
    public function get_aktif()
    {
        // Coba is_aktif=1 dulu, fallback ke status='active'
        $result = $this->db->where('is_aktif', 1)->get($this->table)->row_array();
        if (!$result) {
            $result = $this->db->where('status', 'active')->get($this->table)->row_array();
        }
        // Normalkan nama tampilan
        if ($result && !isset($result['nama_tahun'])) {
            $result['nama_tahun'] = ($result['tahun_mulai'] ?? '') . '/' . ($result['tahun_selesai'] ?? '') . ' ' . ucfirst($result['semester'] ?? '');
        }
        return $result;
    }

    /** Alias */
    public function get_active()
    {
        return $this->get_aktif();
    }

    /**
     * Check if tahun ajaran with same year and semester exists
     * @param string $tahun_mulai Format: YYYY
     * @param string $semester 'ganjil' or 'genap'
     * @param int|null $exclude_id Exclude this ID (for update)
     * @return bool
     */
    public function check_tahun_exists($tahun_mulai, $semester, $exclude_id = null)
    {
        $this->db->where('tahun_mulai', $tahun_mulai);
        $this->db->where('semester', $semester);
        
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    /**
     * Get all tahun ajaran
     */
    public function get_all()
    {
        $rows = $this->db->order_by('tahun_mulai', 'DESC')->get($this->table)->result_array();
        foreach ($rows as &$r) {
            if (!isset($r['nama_tahun'])) {
                $r['nama_tahun'] = ($r['tahun_mulai'] ?? '') . '/' . ($r['tahun_selesai'] ?? '') . ' ' . ucfirst($r['semester'] ?? '');
            }
        }
        return $rows;
    }

    /**
     * Count active
     */
    public function count_active()
    {
        return $this->db->from($this->table)->where('is_aktif', 1)->count_all_results();
    }

    /**
     * Set all to inactive then set one active
     */
    public function set_all_inactive($exclude_id = null)
    {
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        $this->db->update($this->table, ['is_aktif' => 0, 'status' => 'draft']);
    }

    public function get_all_ordered()
    {
        return $this->db->order_by('tahun_mulai', 'DESC')->get($this->table)->result_array();
    }
}
