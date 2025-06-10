<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Art_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all($limit = null, $offset = null, $search = '', $filters = array()) {
        $this->db->select('a.*, s.nama_status, t.nama as trainer_nama');
        $this->db->from('tb_art a');
        $this->db->join('tb_status s', 'a.status_id = s.id', 'left');
        $this->db->join('tb_trainer t', 'a.trainer_id = t.id', 'left');
        
        // Search
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.no_ktp', $search);
            $this->db->or_like('a.no_telepon', $search);
            $this->db->group_end();
        }
        
        // Filters
        if (!empty($filters['status_id'])) {
            $this->db->where('a.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['trainer_id'])) {
            $this->db->where('a.trainer_id', $filters['trainer_id']);
        }
        
        if (!empty($filters['status_kelulusan'])) {
            $this->db->where('a.status_kelulusan', $filters['status_kelulusan']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('a.tanggal_mulai >=', $dari);
            $this->db->where('a.tanggal_mulai <=', $sampai);
        }
        
        if (!empty($filters['sisa_hari'])) {
            $today = date('Y-m-d');
            switch ($filters['sisa_hari']) {
                case 'kritis':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") <', 3);
                    $this->db->where('a.tanggal_selesai >=', $today);
                    break;
                case 'perhatian':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") >=', 3);
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") <=', 7);
                    break;
                case 'normal':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") >', 7);
                    break;
            }
        }
        
        $this->db->order_by('a.created_at', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    public function count_all($search = '', $filters = array()) {
        $this->db->from('tb_art a');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.no_ktp', $search);
            $this->db->or_like('a.no_telepon', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['status_id'])) {
            $this->db->where('a.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['trainer_id'])) {
            $this->db->where('a.trainer_id', $filters['trainer_id']);
        }
        
        if (!empty($filters['status_kelulusan'])) {
            $this->db->where('a.status_kelulusan', $filters['status_kelulusan']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('a.tanggal_mulai >=', $dari);
            $this->db->where('a.tanggal_mulai <=', $sampai);
        }
        
        if (!empty($filters['sisa_hari'])) {
            $today = date('Y-m-d');
            switch ($filters['sisa_hari']) {
                case 'kritis':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") <', 3);
                    $this->db->where('a.tanggal_selesai >=', $today);
                    break;
                case 'perhatian':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") >=', 3);
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") <=', 7);
                    break;
                case 'normal':
                    $this->db->where('DATEDIFF(a.tanggal_selesai, "' . $today . '") >', 7);
                    break;
            }
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('a.*, s.nama_status, t.nama as trainer_nama');
        $this->db->from('tb_art a');
        $this->db->join('tb_status s', 'a.status_id = s.id', 'left');
        $this->db->join('tb_trainer t', 'a.trainer_id = t.id', 'left');
        $this->db->where('a.id', $id);
        return $this->db->get()->row();
    }

    public function insert($data) {
        // Format tanggal
        if (isset($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = format_tanggal_database($data['tanggal_lahir']);
        }
        if (isset($data['tanggal_mulai'])) {
            $data['tanggal_mulai'] = format_tanggal_database($data['tanggal_mulai']);
        }
        if (isset($data['tanggal_selesai'])) {
            $data['tanggal_selesai'] = format_tanggal_database($data['tanggal_selesai']);
        }
        
        return $this->db->insert('tb_art', $data);
    }

    public function update($id, $data) {
        // Format tanggal
        if (isset($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = format_tanggal_database($data['tanggal_lahir']);
        }
        if (isset($data['tanggal_mulai'])) {
            $data['tanggal_mulai'] = format_tanggal_database($data['tanggal_mulai']);
        }
        if (isset($data['tanggal_selesai'])) {
            $data['tanggal_selesai'] = format_tanggal_database($data['tanggal_selesai']);
        }
        
        $this->db->where('id', $id);
        return $this->db->update('tb_art', $data);
    }

    public function delete($id) {
        // Hapus file upload terlebih dahulu
        $art = $this->get_by_id($id);
        if ($art) {
            if (!empty($art->upload_cv) && file_exists('./uploads/cv/' . $art->upload_cv)) {
                unlink('./uploads/cv/' . $art->upload_cv);
            }
            if (!empty($art->upload_ktp) && file_exists('./uploads/ktp/' . $art->upload_ktp)) {
                unlink('./uploads/ktp/' . $art->upload_ktp);
            }
            if (!empty($art->upload_kk) && file_exists('./uploads/kk/' . $art->upload_kk)) {
                unlink('./uploads/kk/' . $art->upload_kk);
            }
        }
        
        $this->db->where('id', $id);
        return $this->db->delete('tb_art');
    }

    public function get_for_report($filters = array()) {
        $this->db->select('a.*, s.nama_status, t.nama as trainer_nama');
        $this->db->from('tb_art a');
        $this->db->join('tb_status s', 'a.status_id = s.id', 'left');
        $this->db->join('tb_trainer t', 'a.trainer_id = t.id', 'left');
        
        if (!empty($filters['status_id'])) {
            $this->db->where('a.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['trainer_id'])) {
            $this->db->where('a.trainer_id', $filters['trainer_id']);
        }
        
        if (!empty($filters['status_kelulusan'])) {
            $this->db->where('a.status_kelulusan', $filters['status_kelulusan']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('a.tanggal_mulai >=', $dari);
            $this->db->where('a.tanggal_mulai <=', $sampai);
        }
        
        $this->db->order_by('a.nama', 'ASC');
        
        return $this->db->get()->result();
    }

    public function check_no_ktp($no_ktp, $exclude_id = null) {
        $this->db->where('no_ktp', $no_ktp);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get('tb_art')->num_rows() > 0;
    }
}