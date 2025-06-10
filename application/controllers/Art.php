<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Art extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Art_model');
        $this->load->model('Status_model');
        $this->load->model('Trainer_model');
        $this->load->library('pagination');
    }

    public function index() {
        $config['base_url'] = base_url('art/index');
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
            'status_id' => $this->input->get('status_id'),
            'trainer_id' => $this->input->get('trainer_id'),
            'status_kelulusan' => $this->input->get('status_kelulusan'),
            'tanggal_dari' => $this->input->get('tanggal_dari'),
            'tanggal_sampai' => $this->input->get('tanggal_sampai'),
            'sisa_hari' => $this->input->get('sisa_hari')
        );

        $config['total_rows'] = $this->Art_model->count_all($search, $filters);
        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        
        $data['title'] = 'Data ART';
        $data['art'] = $this->Art_model->get_all($config['per_page'], $page, $search, $filters);
        $data['training_status'] = $this->Status_model->get_training_status();
        $data['trainer_list'] = $this->Trainer_model->get_active();
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['search'] = $search;
        $data['filters'] = $filters;
        $data['per_page'] = $config['per_page'];

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('art/index', $data);
        $this->load->view('templates/footer');
    }

    public function form($id = null) {
        if ($id) {
            $data['art'] = $this->Art_model->get_by_id($id);
            if (!$data['art']) {
                show_404();
            }
            $data['title'] = 'Edit ART';
        } else {
            $data['art'] = null;
            $data['title'] = 'Tambah ART';
        }
        
        $data['training_status'] = $this->Status_model->get_training_status();
        $data['trainer_list'] = $this->Trainer_model->get_active();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('art/form', $data);
        $this->load->view('templates/footer');
    }

    public function save() {
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('no_ktp', 'No KTP', 'required|trim|exact_length[16]|numeric');
        $this->form_validation->set_rules('no_telepon', 'No Telepon', 'required|trim');

        $id = $this->input->post('id');
        
        // Cek duplikasi KTP
        if ($this->Art_model->check_no_ktp($this->input->post('no_ktp'), $id)) {
            $this->session->set_flashdata('error', 'No KTP sudah terdaftar');
            redirect('art/form/' . $id);
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('art/form/' . $id);
        }

        $data = array(
            'nama' => $this->input->post('nama'),
            'no_ktp' => $this->input->post('no_ktp'),
            'no_telepon' => $this->input->post('no_telepon'),
            'alamat' => $this->input->post('alamat'),
            'email' => $this->input->post('email'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir'),
            'alamat_ktp' => $this->input->post('alamat_ktp'),
            'alamat_domisili' => $this->input->post('alamat_domisili'),
            'agama' => $this->input->post('agama'),
            'pendidikan' => $this->input->post('pendidikan'),
            'status_perkawinan' => $this->input->post('status_perkawinan'),
            'nama_kontak_darurat' => $this->input->post('nama_kontak_darurat'),
            'telp_kontak_darurat' => $this->input->post('telp_kontak_darurat'),
            'hubungan_kontak_darurat' => $this->input->post('hubungan_kontak_darurat'),
            'alamat_kontak_darurat' => $this->input->post('alamat_kontak_darurat'),
            'jumlah_anak' => $this->input->post('jumlah_anak'),
            'status_id' => $this->input->post('status_id'),
            'trainer_id' => $this->input->post('trainer_id'),
            'level' => $this->input->post('level'),
            'level_lainnya' => $this->input->post('level_lainnya'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'status_kelulusan' => $this->input->post('status_kelulusan'),
            'nilai' => $this->input->post('nilai'),
            'catatan_trainer' => $this->input->post('catatan_trainer')
        );

        // Handle file uploads
        $this->handle_uploads($data, $id);

        if ($id) {
            $result = $this->Art_model->update($id, $data);
            $message = 'Data ART berhasil diupdate';
        } else {
            $result = $this->Art_model->insert($data);
            $message = 'Data ART berhasil ditambahkan';
        }

        if ($result) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data');
        }

        redirect('art');
    }

    private function handle_uploads(&$data, $id = null) {
        $upload_fields = array(
            'upload_cv' => array('path' => './uploads/cv/', 'types' => 'pdf|doc|docx'),
            'upload_ktp' => array('path' => './uploads/ktp/', 'types' => 'jpg|jpeg|png|pdf'),
            'upload_kk' => array('path' => './uploads/kk/', 'types' => 'jpg|jpeg|png|pdf')
        );

        foreach ($upload_fields as $field => $config) {
            if (!empty($_FILES[$field]['name'])) {
                if (!is_dir($config['path'])) {
                    mkdir($config['path'], 0777, true);
                }

                $upload_config = array(
                    'upload_path' => $config['path'],
                    'allowed_types' => $config['types'],
                    'max_size' => 2048, // 2MB
                    'file_name' => time() . '_' . $_FILES[$field]['name']
                );

                $this->upload->initialize($upload_config);

                if ($this->upload->do_upload($field)) {
                    $upload_data = $this->upload->data();
                    $data[$field] = $upload_data['file_name'];

                    // Hapus file lama jika update
                    if ($id) {
                        $old_data = $this->Art_model->get_by_id($id);
                        if ($old_data && !empty($old_data->{$field})) {
                            $old_file = $config['path'] . $old_data->{$field};
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                    }
                }
            }
        }
    }

    public function delete($id) {
        $result = $this->Art_model->delete($id);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Data ART berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data');
        }
        
        redirect('art');
    }

    public function get_trainer() {
        $trainers = $this->Trainer_model->get_for_dropdown();
        echo json_encode($trainers);
    }

    public function export_pdf() {
        $search = $this->input->get('search');
        $filters = array(
            'status_id' => $this->input->get('status_id'),
            'trainer_id' => $this->input->get('trainer_id'),
            'status_kelulusan' => $this->input->get('status_kelulusan'),
            'tanggal_dari' => $this->input->get('tanggal_dari'),
            'tanggal_sampai' => $this->input->get('tanggal_sampai')
        );

        $data = $this->Art_model->get_for_report($filters);
        
        $pdf = init_pdf('L', 'Laporan Data ART');
        pdf_header($pdf, 'Laporan Data ART');
        
        // Table headers
        $headers = array('No', 'Nama', 'No KTP', 'Status', 'Trainer', 'Tgl Mulai', 'Tgl Selesai', 'Sisa Hari');
        $widths = array(10, 40, 35, 25, 35, 25, 25, 20);
        
        create_table_header($pdf, $headers, $widths);
        
        $no = 1;
        foreach ($data as $row) {
            $sisa_hari = hitung_sisa_hari($row->tanggal_selesai);
            $sisa_text = $sisa_hari . ' hari';
            if ($sisa_hari < 0) {
                $sisa_text = 'Terlambat ' . abs($sisa_hari) . ' hari';
            }
            
            $row_data = array(
                $no++,
                $row->nama,
                $row->no_ktp,
                $row->nama_status,
                $row->trainer_nama,
                format_tanggal_indonesia($row->tanggal_mulai),
                format_tanggal_indonesia($row->tanggal_selesai),
                $sisa_text
            );
            
            $fill = ($sisa_hari < 3);
            add_table_row($pdf, $row_data, $widths, $fill);
        }
        
        $pdf->Output('laporan_art_' . date('Y-m-d') . '.pdf', 'D');
    }
}