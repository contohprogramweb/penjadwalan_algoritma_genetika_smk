<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model - Mengelola data user untuk autentikasi
 */
class User_model extends CI_Model
{
    private $table = 'users';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user by username
     * 
     * @param string $username
     * @return object|null
     */
    public function get_by_username($username)
    {
        return $this->db
            ->where('username', $username)
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    /**
     * Get user by ID
     * 
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    /**
     * Create new user (untuk seeding/development)
     * 
     * @param array $data
     * @return int Insert ID
     */
    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update user data
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    /**
     * Verify password
     * 
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify_password($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash password
     * 
     * @param string $password
     * @return string
     */
    public function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
