<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Jam Pelajaran
 * Kolom DB: id_jam, slot, waktu_mulai, waktu_selesai, durasi_menit,
 *           is_istirahat, is_active, created_at
 */
class Jam_model extends CI_Model {

    private $table       = 'jam_pelajaran';
    private $primary_key = 'id_jam';
    private $column_order  = ['slot', 'waktu_mulai', 'waktu_selesai', 'durasi_menit'];
    private $column_search = ['slot', 'waktu_mulai', 'waktu_selesai'];
    private $order = ['slot' => 'ASC'];

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
            $col = $this->column_order[$idx] ?? 'slot';
            $this->db->order_by($col, $_POST['order']['0']['dir']);
        } else {
            foreach ($this->order as $k => $v) { $this->db->order_by($k, $v); }
        }
    }

    public function get_all_ordered()
    {
        return $this->db->order_by('slot', 'ASC')->get($this->table)->result_array();
    }

    public function get_active_jam()
    {
        return $this->db->where('is_active', 1)->order_by('slot', 'ASC')->get($this->table)->result_array();
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
}
