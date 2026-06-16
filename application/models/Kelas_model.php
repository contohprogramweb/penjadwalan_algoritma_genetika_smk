<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Kelas
 * Kolom DB: id_kelas, kode_kelas, nama_kelas, jurusan, tingkat,
 *           kapasitas_siswa, id_kurikulum, created_at, updated_at
 */
class Kelas_model extends CI_Model {

    private $table       = 'kelas';
    private $primary_key = 'id_kelas';
    private $column_order  = ['kode_kelas', 'nama_kelas', 'tingkat', 'jurusan', 'kapasitas_siswa'];
    private $column_search = ['kode_kelas', 'nama_kelas', 'jurusan', 'tingkat'];
    private $order = ['tingkat' => 'ASC', 'nama_kelas' => 'ASC'];

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
            $col = $this->column_order[$idx] ?? 'id_kelas';
            $this->db->order_by($col, $_POST['order']['0']['dir']);
        } else {
            foreach ($this->order as $k => $v) { $this->db->order_by($k, $v); }
        }
    }

    public function get_all()
    {
        return $this->db->order_by('tingkat', 'ASC')->order_by('nama_kelas', 'ASC')->get($this->table)->result();
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

    public function check_kode_exists($kode, $exclude_id = null)
    {
        $this->db->where('kode_kelas', strtoupper($kode));
        if ($exclude_id) $this->db->where($this->primary_key . ' !=', $exclude_id);
        return $this->db->count_all_results($this->table) > 0;
    }
}
