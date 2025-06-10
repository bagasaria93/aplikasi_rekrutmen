<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Trainer_model');
        $this->load->library('pagination');
    }

    public function index() {
        $config['base_url'] = base_url('trainer/index');
        $config['per_page'] = $this->input->get('per_page') ? $this->input->get('per_page') : 25;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        // Pagination styling
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['attributes'] = array('class' => 'page-link');

        // Get filters
        $search = $this->input->get('search');
        $filters = array(
            'level' => $this->input->get('level'),
            'status_aktif' => $this->input->get('status_aktif')
        );

        $config['total_rows'] = $this->Trainer_model->count_all($search, $filters);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        $data['title'] = 'Data Trainer';
        $data['trainer'] = $this->Trainer_model->get_all($config['per_page'], $page, $search, $filters);
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['search'] = $search;
        $data['filters'] = $filters;
        $data['per_page'] = $config['per_page'];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('trainer/index', $data);
        $this->load->view('templates/footer');
    }

    public function form($id = null) {
        if ($id) {
            $data['trainer'] = $this->Trainer_model->get_by_id($id);
            if (!$data['trainer']) {
                show_404();
            }
            $data['title'] = 'Edit Trainer';
        } else {
            $data['trainer'] = null;
            $data['title'] = 'Tambah Trainer';
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('trainer/form', $data);
        $this->load->view('templates/footer');
    }

    public function save() {
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('jabatan', 'Jabatan', 'required|trim');
        $this->form_validation->set_rules('level', 'Level', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('trainer/form/' . $this->input->post('id'));
        }

        $data = array(
            'nama' => $this->input->post('nama'),
            'jabatan' => $this->input->post('jabatan'),
            'level' => $this->input->post('level'),
            'level_lainnya' => $this->input->post('level_lainnya'),
            'status_aktif' => $this->input->post('status_aktif')
        );

        $id = $this->input->post('id');
        if ($id) {
            $result = $this->Trainer_model->update($id, $data);
            $message = 'Data trainer berhasil diupdate';
        } else {
            $result = $this->Trainer_model->insert($data);
            $message = 'Data trainer berhasil ditambahkan';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data');
        }

        redirect('trainer');
    }

    public function delete($id) {
        $result = $this->Trainer_model->delete($id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Data trainer berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data. Trainer masih digunakan oleh ART.');
        }
        
        redirect('trainer');
    }
}