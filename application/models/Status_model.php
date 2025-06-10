<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        $this->db->order_by('id', 'ASC');
        return $this->db->get('tb_status')->result();
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('tb_status')->row();
    }

    public function get_training_status() {
        $this->db->where_in('id', array(6, 7, 8)); // TRAINING BM, TRAINING SULAM, TRAINING HO
        return $this->db->get('tb_status')->result();
    }

    public function get_for_dropdown() {
        $this->db->select('id, nama_status');
        $this->db->order_by('id', 'ASC');
        return $this->db->get('tb_status')->result();
    }
}