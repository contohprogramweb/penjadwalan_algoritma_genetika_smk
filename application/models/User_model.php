<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model - Model untuk autentikasi user
 */
class User_model extends CI_Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil user berdasarkan username
     *
     * @param string $username
     * @return object|null
     */
    public function get_by_username($username)
    {
        $query = $this->db
            ->where('username', $username)
            ->where('is_active', 1)
            ->get($this->table);

        return $query->row();
    }

    /**
     * Ambil user berdasarkan ID
     *
     * @param int $user_id
     * @return object|null
     */
    public function get_by_id($user_id)
    {
        $query = $this->db
            ->where('id', $user_id)
            ->get($this->table);

        return $query->row();
    }

    /**
     * Update data user
     *
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function update($user_id, $data)
    {
        return $this->db
            ->where('id', $user_id)
            ->update($this->table, $data);
    }

    /**
     * Update last_login user
     *
     * @param int $user_id
     * @return bool
     */
    public function update_last_login($user_id)
    {
        return $this->db
            ->where('id', $user_id)
            ->update($this->table, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
