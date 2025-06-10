<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelamar extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pelamar_model');
        $this->load->model('Status_model');
        $this->load->model('Trainer_model');
        $this->load->library('pagination');
    }

    public function index() {
        $config['base_url'] = base_url('pelamar/index');
        $config['total_rows'] = $this->Pelamar_model->count_all();
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
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tagl_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tagl_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tagl_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tagl_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        // Get filters
        $search = $this->input->get('search');
        $filters = array(
            'status_id' => $this->input->get('status_id'),
            'departemen_id' => $this->input->get('departemen_id'),
            'tanggal_dari' => $this->input->get('tanggal_dari'),
            'tanggal_sampai' => $this->input->get('tanggal_sampai')
        );

        $config['total_rows'] = $this->Pelamar_model->count_all($search, $filters);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        $data['title'] = 'Data Pelamar';
        $data['pelamar'] = $this->Pelamar_model->get_all($config['per_page'], $page, $search, $filters);
        $data['status_list'] = $this->Status_model->get_all();
        $data['departemen_list'] = $this->get_departemen();
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['search'] = $search;
        $data['filters'] = $filters;
        $data['per_page'] = $config['per_page'];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pelamar/index', $data);
        $this->load->view('templates/footer');
    }

    public function form($id = null) {
        if ($id) {
            $data['pelamar'] = $this->Pelamar_model->get_by_id($id);
            if (!$data['pelamar']) {
                show_404();
            }
            $data['title'] = 'Edit Pelamar';
        } else {
            $data['pelamar'] = null;
            $data['title'] = 'Tambah Pelamar';
        }
        
        $data['status_list'] = $this->Status_model->get_all();
        $data['departemen_list'] = $this->get_departemen();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pelamar/form', $data);
        $this->load->view('templates/footer');
    }

    public function save() {
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('no_telepon', 'No Telepon', 'required|trim');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required|trim');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('pelamar/form/' . $this->input->post('id'));
        }

        $data = array(
            'nama' => $this->input->post('nama'),
            'no_telepon' => $this->input->post('no_telepon'),
            'alamat' => $this->input->post('alamat'),
            'email' => $this->input->post('email'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir'),
            'tanggal_phone_screening' => $this->input->post('tanggal_phone_screening'),
            'status_id' => $this->input->post('status_id'),
            'departemen_id' => $this->input->post('departemen_id') ? $this->input->post('departemen_id') : null,
            'catatan' => $this->input->post('catatan')
        );

        $id = $this->input->post('id');
        if ($id) {
            $result = $this->Pelamar_model->update($id, $data);
            $message = 'Data pelamar berhasil diupdate';
        } else {
            $result = $this->Pelamar_model->insert($data);
            $message = 'Data pelamar berhasil ditambahkan';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data');
        }

        redirect('pelamar');
    }

    public function delete($id) {
        $result = $this->Pelamar_model->delete($id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Data pelamar berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data');
        }
        
        redirect('pelamar');
    }

    public function update_status() {
        $id = $this->input->post('id');
        $status_id = $this->input->post('status_id');
        
        $result = $this->Pelamar_model->update_status($id, $status_id);
        
        if ($result) {
            echo json_encode(array('status' => 'success', 'message' => 'Status berhasil diupdate'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Gagal mengupdate status'));
        }
    }

    public function create_training() {
        $pelamar_id = $this->input->post('pelamar_id');
        $status_id = $this->input->post('status_id');
        $trainer_id = $this->input->post('trainer_id');
        $tanggal_mulai = $this->input->post('tanggal_mulai');

        $training_data = array(
            'status_id' => $status_id,
            'trainer_id' => $trainer_id,
            'tanggal_mulai' => $tanggal_mulai
        );

        $art_id = $this->Pelamar_model->create_art_from_pelamar($pelamar_id, $training_data);

        if ($art_id) {
            echo json_encode(array(
                'status' => 'success', 
                'message' => 'Data ART berhasil dibuat', 
                'art_id' => $art_id
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Gagal membuat data ART'));
        }
    }

    public function get_status($id) {
        $status = $this->Status_model->get_by_id($id);
        echo json_encode($status);
    }

    public function get_departemen() {
        $this->db->order_by('nama_departemen', 'ASC');
        return $this->db->get('tb_departemen')->result();
    }

    public function export_pdf() {
        $search = $this->input->get('search');
        $filters = array(
            'status_id' => $this->input->get('status_id'),
            'departemen_id' => $this->input->get('departemen_id'),
            'tanggal_dari' => $this->input->get('tanggal_dari'),
            'tanggal_sampai' => $this->input->get('tanggal_sampai')
        );

        $data = $this->Pelamar_model->get_for_report($filters);
        
        $pdf = init_pdf('L', 'Laporan Data Pelamar');
        pdf_header($pdf, 'Laporan Data Pelamar');
        
        // Filter info
        $filter_info = 'Filter: ';
        if (!empty($filters['status_id'])) {
            $status = $this->Status_model->get_by_id($filters['status_id']);
            $filter_info .= 'Status: ' . $status->nama_status . ' ';
        }
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $filter_info .= 'Periode: ' . rentang_tanggal($filters['tanggal_dari'], $filters['tanggal_sampai']);
        }
        
        pdf_footer_info($pdf, $filter_info);
        
        // Table headers
        $headers = array('No', 'Nama', 'No Telp', 'Email', 'TTL', 'Alamat', 'Status', 'Tgl Screening');
        $widths = array(10, 35, 25, 35, 30, 50, 25, 25);
        
        create_table_header($pdf, $headers, $widths);
        
        $no = 1;
        foreach ($data as $row) {
            $ttl = $row->tempat_lahir . ', ' . format_tanggal_indonesia($row->tanggal_lahir);
            $screening = format_tanggal_indonesia($row->tanggal_phone_screening);
            
            $row_data = array(
                $no++,
                $row->nama,
                $row->no_telepon,
                $row->email,
                $ttl,
                $row->alamat,
                $row->nama_status,
                $screening
            );
            
            add_table_row($pdf, $row_data, $widths, ($no % 2 == 0));
        }
        
        $pdf->Output('laporan_pelamar_' . date('Y-m-d') . '.pdf', 'D');
    }
}