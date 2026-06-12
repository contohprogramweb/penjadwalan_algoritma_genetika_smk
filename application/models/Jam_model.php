<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Jam Pelajaran
 */
class Jam_model extends CI_Model {

    private $table = 'jam_pelajaran';
    private $primary_key = 'id_jam';
    private $column_order = ['slot', 'waktu_mulai', 'waktu_selesai', 'durasi_menit'];
    private $column_search = ['slot', 'waktu_mulai', 'waktu_selesai', 'keterangan'];
    private $order = ['slot' => 'ASC'];

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
     * Get all jam pelajaran ordered by slot
     */
    public function get_all_ordered()
    {
        return $this->db->order_by('slot', 'ASC')->get($this->table)->result_array();
    }

    /**
     * Get active jam pelajaran (not break time)
     */
    public function get_active_jam()
    {
        return $this->db->where('is_active', 1)->order_by('slot', 'ASC')->get($this->table)->result_array();
    }
}