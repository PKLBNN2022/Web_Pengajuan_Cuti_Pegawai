<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pengajuancutipns extends MY_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Mod_pengajuancutipns'));
    }

    public function index()
    {
        $this->template->load('layoutbackend', 'subbag/pengajuancuti_pns');
    }

    public function ajax_list()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(3600);
        $list = $this->Mod_pengajuancutipns->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $cuti) {
            $form = "
                <form action=".site_url('pengajuancutipns/status')." method='post'>
                    <input type='hidden' name ='id_pns' value =".$cuti->id_pns.">
                    <select name='change' class='form-control pilih-status'>
                        <option ".($cuti->status == "Belum Dikonfirmasi" ? "selected" : "")." value='Belum Dikonfirmasi'>Belum Dikonfirmasi</option>
                        <option ".($cuti->status == "Disetujui" ? "selected" : "")." value='Disetujui'>Disetujui</option>
                        <option ".($cuti->status == "Perubahan" ? "selected" : "")." value='Perubahan'>Perubahan</option>
                        <option ".($cuti->status == "Ditangguhkan" ? "selected" : "")." value='Ditangguhkan'>Ditangguhkan</option>
                        <option ".($cuti->status == "Tidak Disetujui" ? "selected" : "")." value='Tidak Disetujui'>Tidak Disetujui</option>
                    </select>
                    <button type='submit' class='btn btn-primary d-none ubah-status'>Konfirmasi</button>
                </form>";

            // Dengan BUTTON
            // $form = "
            //     <form action=".site_url('pengajuancutipns/status')." method='post'>
            //         <input type='hidden' name ='id_pns' value =".$cuti->id_pns.">
            //         <button type='submit' class='btn btn-primary' name='change' value='Tidak Disetujui'>Setujui</button>
            //         <button type='submit' class='btn btn-primary' name='change' value='Disetujui'>Tolak</button>
            //     </form>";

            $no++;
            $row = array();
            $row[] = $cuti->nrpnip;
            $row[] = $cuti->nama;
            $row[] = $cuti->jabatan;
            $row[] = $cuti->jenis_cuti;
            $row[] = $cuti->tgl_awal;
            $row[] = $cuti->tgl_akhir;
            $row[] = $cuti->jmlh_cuti;
            $row[] = $form;
            $row[] = $cuti->masa_kerja;
            $row[] = $cuti->unit_kerja;
            $row[] = $cuti->alasan;
            $row[] = $cuti->sisa_cuti;
            $row[] = $cuti->alamat_cuti;
            $row[] = $cuti->telp;
            $row[] = $cuti->id_pns;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Mod_pengajuancutipns->count_all(),
            "recordsFiltered" => $this->Mod_pengajuancutipns->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    public function status()
    {
        // 
        $this->db->update("tbl_pengajuan_pns",[
            'status' => $this->input->post('change')
        ],[
            'id_pns' => $this->input->post('id_pns')
        ]);

        redirect(base_url("pengajuancutipns"));
    }

    // public function insert()
    // {
    //     $this->_validate();
    //     $save  = array(
    //         'nrpnip'     => $this->input->post('nrpnip'),
    //         'nama'        => $this->input->post('nama'),
    //         'jabatan'     => $this->input->post('jabatan'),
    //         'masa_kerja'  => $this->input->post('masa_kerja'),
    //         'unit_kerja'  => $this->input->post('unit_kerja'),
    //         'jenis_cuti'  => $this->input->post('jenis_cuti'),
    //         'alasan'      => $this->input->post('alasan'),
    //         'tgl_awal'    => $this->input->post('tgl_awal'),
    //         'tgl_akhir'   => $this->input->post('tgl_akhir'),
    //         'jmlh_cuti'   => $this->input->post('jmlh_cuti'),
    //         'sisa_cuti'   => $this->input->post('sisa_cuti'),
    //         'alamat_cuti' => $this->input->post('alamat_cuti'),
    //         'telp'        => $this->input->post('telp')
    //         // 'status'         => $this->input->post('status')
    //     );
    //     $this->Mod_pengajuancutipns->insert("tbl_pengajuan_pns", $save);
    //     echo json_encode(array("status" => TRUE));
    // }

    public function view()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $data['table'] = $table;
        $data['data_field'] = $this->db->field_data($table);
        $data['data_table'] = $this->Mod_pengajuancutipns->view($id)->result_array();
        $this->load->view('subbag/view', $data);
    }

    // public function edit($id)
    // {
    //     $data = $this->Mod_pengajuancutipns->get_pengajuancutipns($id);
    //     echo json_encode($data);
    // }

    public function update()
    {
        $this->_validate();
        $id        = $this->input->post('id');
        $save  = array(
            'nrpnip'      => $this->input->post('nrpnip'),
            'nama'        => $this->input->post('nama'),
            'jabatan'     => $this->input->post('jabatan'),
            'masa_kerja'  => $this->input->post('masa_kerja'),
            'unit_kerja'  => $this->input->post('unit_kerja'),
            'jenis_cuti'  => $this->input->post('jenis_cuti'),
            'alasan'      => $this->input->post('alasan'),
            'tgl_awal'    => $this->input->post('tgl_awal'),
            'tgl_akhir'   => $this->input->post('tgl_akhir'),
            'jmlh_cuti'   => $this->input->post('jmlh_cuti'),
            'sisa_cuti'   => $this->input->post('sisa_cuti'),
            'alamat_cuti' => $this->input->post('alamat_cuti'),
            'telp'        => $this->input->post('telp'),
            'status'      => $this->input->post('status')
        );
        $this->Mod_pengajuancutipns->update($id, $save);
        echo json_encode(array("status" => TRUE));
    }

    // public function add()
    // {
    //     $this->load->view('pengajuancutipns/add');
    // }
   
    // public function delete()
    // {
    //     $id = $this->input->post('id');
    //     $this->Mod_pengajuancutipns->delete($id, 'tbl_pengajuan_pns');
    //     echo json_encode(array("status" => TRUE));
    // }
    

    public function excel()
    {

        $this->load->model('Mod_pengajuancutipns');
		$spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIP/NRP');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Masa Kerja');
        $sheet->setCellValue('F1', 'Unit Kerja');
        $sheet->setCellValue('G1', 'Jenis Cuti');
        $sheet->setCellValue('H1', 'Alasan');
        $sheet->setCellValue('I1', 'Tgl Awal');
        $sheet->setCellValue('J1', 'Tgl Akhir');
        $sheet->setCellValue('K1', 'Jumlah Cuti');
        $sheet->setCellValue('L1', 'Sisa Cuti');
        $sheet->setCellValue('M1', 'Alamat Selama Cuti');
        $sheet->setCellValue('N1', 'Telepon Tempat Cuti');
        $sheet->setCellValue('O1', 'Status');

        $menu = $this->Mod_pengajuancutipns->getdataAll();
        $no = 1;
        $x = 2;
        foreach ($menu as $row) {
            $sheet->setCellValue('A' . $x, $no++);
            $sheet->setCellValue('B' . $x, $row->nrpnip);
            $sheet->setCellValue('C' . $x, $row->nama);
            $sheet->setCellValue('D' . $x, $row->jabatan);
            $sheet->setCellValue('E' . $x, $row->masa_kerja);
            $sheet->setCellValue('F' . $x, $row->unit_kerja);
            $sheet->setCellValue('G' . $x, $row->jenis_cuti);
            $sheet->setCellValue('H' . $x, $row->alasan);
            $sheet->setCellValue('I' . $x, $row->tgl_awal);
            $sheet->setCellValue('J' . $x, $row->tgl_akhir);
            $sheet->setCellValue('K' . $x, $row->jmlh_cuti);
            $sheet->setCellValue('L' . $x, $row->sisa_cuti);
            $sheet->setCellValue('M' . $x, $row->alamat_cuti);
            $sheet->setCellValue('N' . $x, $row->telp);
            $sheet->setCellValue('O' . $x, $row->status);
            $x++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan Pengajuan Cuti PNS';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function pdf()
    {
        // $menu = $this->Mod_pengajuancutipns->getAll()->result();
        // $this->load->view('pns/pdfpns', $menu);
        if(isset($_GET['id']) ){
            $data['cuti'] = $this->db->get_where("tbl_pengajuan_pns",['id_pns'=>$this->input->get('id')])->row_array();
            $this->load->view('subbag/pdfpns', $data);
        }else{
            $data['cuti'] = $this->Mod_pengajuancutipns->getAll()->result();
            $this->load->view('subbag/pdfpegawaipns', $data);
        }
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($this->input->post('nrpnip') == '')
        {
            $data['inputerror'][] = 'nap_nrp';
            $data['error_string'][] = 'NIP/NRP Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('nama') == '')
        {
            $data['inputerror'][] = 'nama';
            $data['error_string'][] = 'Nama Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('jabatan') == '')
        {
            $data['inputerror'][] = 'jabatan';
            $data['error_string'][] = 'Jabatan Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }
        
        if($this->input->post('masa_kerja') == '')
        {
            $data['inputerror'][] = 'masa_kerja';
            $data['error_string'][] = 'Masa Kerja Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('unit_kerja') == '')
        {
            $data['inputerror'][] = 'unit_kerja';
            $data['error_string'][] = 'Unit Kerja Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('jenis_cuti') == '')
        {
            $data['inputerror'][] = 'jenis_cuti';
            $data['error_string'][] = 'Pilih Jenis Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('alasan') == '')
        {
            $data['inputerror'][] = 'alasan';
            $data['error_string'][] = 'Alasan Tidak Boleh Kosong';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }
    
        if($this->input->post('tgl_awal') == '')
        {
            $data['inputerror'][] = 'tgl_awal';
            $data['error_string'][] = 'Isi Tanggal Awal Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('tgl_akhir') == '')
        {
            $data['inputerror'][] = 'tgl_akhir';
            $data['error_string'][] = 'Isi Tanggal Akhir Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('jmlh_cuti') == '')
        {
            $data['inputerror'][] = 'jmlh_cuti';
            $data['error_string'][] = 'Isi Jumlah Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }
        if($this->input->post('sisa_cuti') == '')
        {
            $data['inputerror'][] = 'sisa_cuti';
            $data['error_string'][] = 'Isi Sisa Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }
        if($this->input->post('alamat_cuti') == '')
        {
            $data['inputerror'][] = 'alamat_cuti';
            $data['error_string'][] = 'Isi Alamat selama Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($this->input->post('telp') == '')
        {
            $data['inputerror'][] = 'telp';
            $data['error_string'][] = 'Isi No Telepon Tempat Cuti';
            $data['minlength'] = '2';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }
}