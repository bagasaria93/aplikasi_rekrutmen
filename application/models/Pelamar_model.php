<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelamar_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all($limit = null, $offset = null, $search = '', $filters = array()) {
        $this->db->select('p.*, s.nama_status, s.warna, d.nama_departemen');
        $this->db->from('tb_pelamar p');
        $this->db->join('tb_status s', 'p.status_id = s.id', 'left');
        $this->db->join('tb_departemen d', 'p.departemen_id = d.id', 'left');
        
        // Search
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.no_telepon', $search);
            $this->db->or_like('p.email', $search);
            $this->db->group_end();
        }
        
        // Filters
        if (!empty($filters['status_id'])) {
            $this->db->where('p.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['departemen_id'])) {
            $this->db->where('p.departemen_id', $filters['departemen_id']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('p.tanggal_phone_screening >=', $dari);
            $this->db->where('p.tanggal_phone_screening <=', $sampai);
        }
        
        $this->db->order_by('p.created_at', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    public function count_all($search = '', $filters = array()) {
        $this->db->from('tb_pelamar p');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('p.no_telepon', $search);
            $this->db->or_like('p.email', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['status_id'])) {
            $this->db->where('p.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['departemen_id'])) {
            $this->db->where('p.departemen_id', $filters['departemen_id']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('p.tanggal_phone_screening >=', $dari);
            $this->db->where('p.tanggal_phone_screening <=', $sampai);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('p.*, s.nama_status, d.nama_departemen');
        $this->db->from('tb_pelamar p');
        $this->db->join('tb_status s', 'p.status_id = s.id', 'left');
        $this->db->join('tb_departemen d', 'p.departemen_id = d.id', 'left');
        $this->db->where('p.id', $id);
        return $this->db->get()->row();
    }

    public function insert($data) {
        if (isset($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = format_tanggal_database($data['tanggal_lahir']);
        }
        if (isset($data['tanggal_phone_screening'])) {
            $data['tanggal_phone_screening'] = format_tanggal_database($data['tanggal_phone_screening']);
        }
        
        return $this->db->insert('tb_pelamar', $data);
    }

    public function update($id, $data) {
        if (isset($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = format_tanggal_database($data['tanggal_lahir']);
        }
        if (isset($data['tanggal_phone_screening'])) {
            $data['tanggal_phone_screening'] = format_tanggal_database($data['tanggal_phone_screening']);
        }
        
        $this->db->where('id', $id);
        return $this->db->update('tb_pelamar', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_pelamar');
    }

    public function update_status($id, $status_id) {
        $data = array('status_id' => $status_id);
        $this->db->where('id', $id);
        return $this->db->update('tb_pelamar', $data);
    }

    public function create_art_from_pelamar($pelamar_id, $training_data) {
        $pelamar = $this->get_by_id($pelamar_id);
        if (!$pelamar) {
            return false;
        }

        // Tentukan tanggal selesai berdasarkan jenis training
        $tanggal_mulai = format_tanggal_database($training_data['tanggal_mulai']);
        $durasi_hari = 14; // Default untuk TRAINING BM/SULAM
        
        if ($training_data['status_id'] == 8) { // TRAINING HO
            $durasi_hari = 2;
        }
        
        $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' + ' . $durasi_hari . ' days'));

        $art_data = array(
            'nama' => $pelamar->nama,
            'no_ktp' => '', // Akan diisi manual
            'no_telepon' => $pelamar->no_telepon,
            'alamat' => $pelamar->alamat,
            'email' => $pelamar->email,
            'tempat_lahir' => $pelamar->tempat_lahir,
            'tanggal_lahir' => $pelamar->tanggal_lahir,
            'alamat_ktp' => $pelamar->alamat,
            'alamat_domisili' => $pelamar->alamat,
            'agama' => '',
            'pendidikan' => '',
            'status_perkawinan' => 'Belum Menikah',
            'nama_kontak_darurat' => '',
            'telp_kontak_darurat' => '',
            'hubungan_kontak_darurat' => '',
            'alamat_kontak_darurat' => '',
            'jumlah_anak' => 0,
            'status_id' => $training_data['status_id'],
            'trainer_id' => $training_data['trainer_id'],
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai
        );

        $this->db->insert('tb_art', $art_data);
        $art_id = $this->db->insert_id();

        // Update status pelamar
        $this->update_status($pelamar_id, $training_data['status_id']);

        return $art_id;
    }

    public function get_for_report($filters = array()) {
        $this->db->select('p.*, s.nama_status, d.nama_departemen');
        $this->db->from('tb_pelamar p');
        $this->db->join('tb_status s', 'p.status_id = s.id', 'left');
        $this->db->join('tb_departemen d', 'p.departemen_id = d.id', 'left');
        
        if (!empty($filters['status_id'])) {
            $this->db->where('p.status_id', $filters['status_id']);
        }
        
        if (!empty($filters['departemen_id'])) {
            $this->db->where('p.departemen_id', $filters['departemen_id']);
        }
        
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $dari = format_tanggal_database($filters['tanggal_dari']);
            $sampai = format_tanggal_database($filters['tanggal_sampai']);
            $this->db->where('p.tanggal_phone_screening >=', $dari);
            $this->db->where('p.tanggal_phone_screening <=', $sampai);
        }
        
        $this->db->order_by('p.nama', 'ASC');
        
        return $this->db->get()->result();
    }
}