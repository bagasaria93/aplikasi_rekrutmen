<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_summary() {
        return $this->db->get('v_dashboard_summary')->row();
    }

    public function get_status_chart_data() {
        $this->db->select('s.nama_status, COUNT(p.id) as jumlah, s.warna');
        $this->db->from('tb_status s');
        $this->db->join('tb_pelamar p', 'p.status_id = s.id', 'left');
        $this->db->group_by('s.id, s.nama_status, s.warna');
        $this->db->order_by('s.id', 'ASC');
        return $this->db->get()->result();
    }

    public function get_head_office_by_departemen() {
        return $this->db->get('v_head_office_departemen')->result();
    }

    public function get_recent_activities($limit = 10) {
        $this->db->select('
            l.created_at,
            CASE 
                WHEN l.pelamar_id IS NOT NULL THEN CONCAT("Pelamar ", p.nama, " status diubah ke ", s.nama_status)
                WHEN l.art_id IS NOT NULL THEN CONCAT("ART ", a.nama, " status diubah ke ", s.nama_status)
            END as aktivitas
        ');
        $this->db->from('tb_log_status l');
        $this->db->join('tb_pelamar p', 'l.pelamar_id = p.id', 'left');
        $this->db->join('tb_art a', 'l.art_id = a.id', 'left');
        $this->db->join('tb_status s', 'l.status_baru_id = s.id', 'left');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_training_summary() {
        $result = array();
        
        // Training yang sedang berjalan
        $this->db->select('COUNT(*) as jumlah');
        $this->db->where('status_id IN (6,7,8)'); // Training status
        $this->db->where('status_kelulusan', 'Proses');
        $result['training_berjalan'] = $this->db->get('tb_art')->row()->jumlah;
        
        // Training akan berakhir < 3 hari
        $this->db->select('COUNT(*) as jumlah');
        $this->db->where('status_id IN (6,7,8)');
        $this->db->where('status_kelulusan', 'Proses');
        $this->db->where('DATEDIFF(tanggal_selesai, CURDATE()) <', 3);
        $this->db->where('tanggal_selesai >=', date('Y-m-d'));
        $result['training_kritis'] = $this->db->get('tb_art')->row()->jumlah;
        
        // Training selesai bulan ini
        $this->db->select('COUNT(*) as jumlah');
        $this->db->where('MONTH(tanggal_selesai)', date('m'));
        $this->db->where('YEAR(tanggal_selesai)', date('Y'));
        $this->db->where('status_kelulusan !=', 'Proses');
        $result['training_selesai_bulan_ini'] = $this->db->get('tb_art')->row()->jumlah;
        
        return (object) $result;
    }

    public function get_monthly_recruitment_trend($tahun = null) {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $this->db->select('
            MONTH(created_at) as bulan,
            COUNT(*) as jumlah_pelamar,
            SUM(CASE WHEN status_id IN (9,10) THEN 1 ELSE 0 END) as diterima
        ');
        $this->db->where('YEAR(created_at)', $tahun);
        $this->db->group_by('MONTH(created_at)');
        $this->db->order_by('MONTH(created_at)', 'ASC');
        return $this->db->get('tb_pelamar')->result();
    }
}