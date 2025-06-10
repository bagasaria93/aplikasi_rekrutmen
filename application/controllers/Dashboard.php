<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->model('Status_model');
        $this->load->model('Trainer_model');
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $data['summary'] = $this->Dashboard_model->get_summary();
        $data['chart_data'] = $this->Dashboard_model->get_status_chart_data();
        $data['head_office_departemen'] = $this->Dashboard_model->get_head_office_by_departemen();
        $data['recent_activities'] = $this->Dashboard_model->get_recent_activities(10);
        $data['training_summary'] = $this->Dashboard_model->get_training_summary();
        $data['monthly_trend'] = $this->Dashboard_model->get_monthly_recruitment_trend();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer');
    }

    public function get_chart_data() {
        $chart_data = $this->Dashboard_model->get_status_chart_data();
        
        $result = array();
        foreach ($chart_data as $data) {
            $result[] = array(
                'label' => $data->nama_status,
                'data' => (int)$data->jumlah,
                'backgroundColor' => $data->warna
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function get_monthly_trend() {
        $tahun = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
        $trend_data = $this->Dashboard_model->get_monthly_recruitment_trend($tahun);
        
        $bulan_nama = array(
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        );
        
        $result = array(
            'labels' => array(),
            'pelamar' => array(),
            'diterima' => array()
        );
        
        for ($i = 1; $i <= 12; $i++) {
            $result['labels'][] = $bulan_nama[$i];
            $found = false;
            
            foreach ($trend_data as $data) {
                if ($data->bulan == $i) {
                    $result['pelamar'][] = (int)$data->jumlah_pelamar;
                    $result['diterima'][] = (int)$data->diterima;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $result['pelamar'][] = 0;
                $result['diterima'][] = 0;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}