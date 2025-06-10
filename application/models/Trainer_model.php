<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainer_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all($limit = null, $offset = null, $search = '', $filters = array()) {
        $this->db->select('*');
        $this->db->from('tb_trainer');
        
        // Search
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('jabatan', $search);
            $this->db->group_end();
        }
        
        // Filters
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['status_aktif'])) {
            $this->db->where('status_aktif', $filters['status_aktif']);
        }
        
        $this->db->order_by('nama', 'ASC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    public function count_all($search = '', $filters = array()) {
        $this->db->from('tb_trainer');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('jabatan', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['level'])) {
            $this->db->where('level', $filters['level']);
        }
        
        if (!empty($filters['status_aktif'])) {
            $this->db->where('status_aktif', $filters['status_aktif']);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('tb_trainer')->row();
    }

    public function get_active() {
        $this->db->where('status_aktif', 'Aktif');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('tb_trainer')->result();
    }

    public function insert($data) {
        return $this->db->insert('tb_trainer', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tb_trainer', $data);
    }

    public function delete($id) {
        // Cek apakah trainer sedang digunakan
        $this->db->where('trainer_id', $id);
        $used = $this->db->get('tb_art')->num_rows();
        
        if ($used > 0) {
            return false; // Tidak bisa dihapus karena sedang digunakan
        }
        
        $this->db->where('id', $id);
        return $this->db->delete('tb_trainer');
    }

    public function get_for_dropdown() {
        $this->db->select('id, nama, jabatan');
        $this->db->where('status_aktif', 'Aktif');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('tb_trainer')->result();
    }
}