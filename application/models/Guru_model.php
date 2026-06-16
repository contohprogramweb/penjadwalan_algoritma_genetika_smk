<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Guru
 * Kolom DB: id_guru, nip, nama_lengkap, jenis_kelamin, tempat_lahir,
 *           tanggal_lahir, pendidikan_terakhir, jam_maks_minggu, jam_min_minggu,
 *           status_aktif, created_at, updated_at
 */
class Guru_model extends CI_Model {

    private $table       = 'guru';
    private $primary_key = 'id_guru';
    private $column_order  = ['nip', 'nama_lengkap', 'jenis_kelamin', 'jam_min_minggu', 'jam_maks_minggu', 'status_aktif'];
    private $column_search = ['nip', 'nama_lengkap'];
    private $order = ['id_guru' => 'DESC'];

    public function __construct() { parent::__construct(); }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get($this->table)->result_array();
    }

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->get($this->table)->num_rows();
    }

    private function _get_datatables_query()
    {
        if (!empty($_POST['search']['value'])) {
            $kw = $_POST['search']['value'];
            $this->db->group_start();
            foreach ($this->column_search as $i => $col) {
                $i === 0 ? $this->db->like($col, $kw) : $this->db->or_like($col, $kw);
            }
            $this->db->group_end();
        }
        if (!empty($_POST['order'])) {
            $idx = intval($_POST['order']['0']['column']);
            $col = $this->column_order[$idx] ?? 'id_guru';
            $this->db->order_by($col, $_POST['order']['0']['dir']);
        } else {
            foreach ($this->order as $k => $v) { $this->db->order_by($k, $v); }
        }
    }

    public function get_all()
    {
        return $this->db->order_by('nama_lengkap', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row_array();
    }

    public function insert($data) { return $this->db->insert($this->table, $data); }

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

    public function count_active()
    {
        return $this->db->where('status_aktif', 1)->count_all_results($this->table);
    }

    public function check_nip_exists($nip, $exclude_id = null)
    {
        $this->db->where('nip', $nip);
        if ($exclude_id) $this->db->where($this->primary_key . ' !=', $exclude_id);
        return $this->db->count_all_results($this->table) > 0;
    }
}
