<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model untuk CRUD Guru
 */
class Guru_model extends CI_Model {

    private $table = 'guru';
    private $primary_key = 'id_guru';
    private $column_order = ['nip', 'nama', 'email', 'status', 'jam_min', 'jam_maks'];
    private $column_search = ['nip', 'nuptk', 'nama', 'email', 'no_hp', 'status'];
    private $order = ['id_guru' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get datatables data
     */
    public function get_datatables()
    {
        $this->_get_datatables_query();
        
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    /**
     * Count total records
     */
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /**
     * Count filtered records
     */
    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

    /**
     * Build datatables query
     */
    private function _get_datatables_query()
    {
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
     * Get guru by ID
     */
    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row_array();
    }

    /**
     * Insert new guru
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update guru
     */
    public function update($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete guru
     */
    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Check if NIP exists (for validation)
     */
    public function check_nip_exists($nip, $exclude_id = null)
    {
        $this->db->where('nip', $nip);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Check if NUPTK exists (for validation)
     */
    public function check_nuptk_exists($nuptk, $exclude_id = null)
    {
        $this->db->where('nuptk', $nuptk);
        if ($exclude_id) {
            $this->db->where($this->primary_key . ' !=', $exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }
}
