<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pelamar_model');
        $this->load->model('Art_model');
        $this->load->model('Status_model');
        $this->load->model('Trainer_model');
        $this->load->model('Dashboard_model');
    }

    public function pelamar() {
        $data['title'] = 'Laporan Pelamar';
        $data['status_list'] = $this->Status_model->get_all();
        $data['departemen_list'] = $this->get_departemen();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('laporan/pelamar', $data);
        $this->load->view('templates/footer');
    }

    public function art() {
        $data['title'] = 'Laporan ART';
        $data['training_status'] = $this->Status_model->get_training_status();
        $data['trainer_list'] = $this->Trainer_model->get_active();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('laporan/art', $data);
        $this->load->view('templates/footer');
    }

    public function cetak_pelamar() {
        $filters = array(
            'status_id' => $this->input->post('status_id'),
            'departemen_id' => $this->input->post('departemen_id'),
            'tanggal_dari' => $this->input->post('tanggal_dari'),
            'tanggal_sampai' => $this->input->post('tanggal_sampai')
        );

        $data = $this->Pelamar_model->get_for_report($filters);
        
        $this->load->view('laporan/cetak_pelamar', array(
            'data' => $data,
            'filters' => $filters,
            'status_list' => $this->Status_model->get_all(),
            'departemen_list' => $this->get_departemen()
        ));
    }

    public function cetak_art() {
        $filters = array(
            'status_id' => $this->input->post('status_id'),
            'trainer_id' => $this->input->post('trainer_id'),
            'status_kelulusan' => $this->input->post('status_kelulusan'),
            'tanggal_dari' => $this->input->post('tanggal_dari'),
            'tanggal_sampai' => $this->input->post('tanggal_sampai')
        );

        $data = $this->Art_model->get_for_report($filters);
        
        $this->load->view('laporan/cetak_art', array(
            'data' => $data,
            'filters' => $filters,
            'trainer_list' => $this->Trainer_model->get_all()
        ));
    }

    private function get_departemen() {
        $this->db->order_by('nama_departemen', 'ASC');
        return $this->db->get('tb_departemen')->result();
    }
}