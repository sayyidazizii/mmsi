<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctDepositoAccountBufferReport extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Connection_model');
        $this->load->model('MainPage_model');
        $this->load->model('AcctDepositoAccountBufferReport_model');
        $this->load->helper('sistem');
        $this->load->helper('url');
        $this->load->database('default');
        $this->load->library('configuration');
        $this->load->library('fungsi');
        $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    }

    public function index(){
        $corebranch                            = create_double_branch($this->AcctDepositoAccountBufferReport_model->getCoreBranch(), 'branch_id', 'branch_name');
        $corebranch[0]                         = 'Semua Cabang';
        ksort($corebranch);
        $data['main_view']['corebranch']       = $corebranch;
        $data['main_view']['content']          = 'AcctDepositoAccountBufferReport/AcctDepositoAccountBufferReport_view';
        $this->load->view('MainPage_view', $data);
    }

    public function viewreport(){
        $sesi = array(
            "start_date"            => tgltodb($this->input->post('start_date', true)),
            "end_date"              => tgltodb($this->input->post('end_date', true)),
            "branch_id"             => $this->input->post('branch_id', true),
            "view"                  => $this->input->post('view', true),
        );

        if ($sesi['view'] == 'pdf') {
            $this->processPrinting($sesi);
        } else {
            $this->export($sesi);
        }
    }

    public function processPrinting($sesi){
        $auth             = $this->session->userdata('auth');
        if ($auth['branch_status'] == 1) {
            if ($sesi['branch_id'] == '' || $sesi['branch_id'] == 0) {
                $branch_id = '';
            } else {
                $branch_id = $sesi['branch_id'];
            }
        } else {
            $branch_id = $auth['branch_id'];
        }

        $preference        = $this->AcctDepositoAccountBufferReport_model->getPreferenceCompany();

        $acctdepositoaccount    = $this->AcctDepositoAccountBufferReport_model->getAcctDepositoAccount($sesi['start_date'], $sesi['end_date'], $branch_id);

        // foreach ($acctsavingstransfermutation as $key => $val) {
        // 	$datatranfser[$val['savings_transfer_mutation_id']][] = array (
        // 	)
        // }

        require_once('tcpdf/config/tcpdf_config.php');
        require_once('tcpdf/tcpdf.php');
        // create new PDF document
        $pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // set document information
        /*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('tcpdf, PDF, example, test, guide');*/

        // set default header data
        /*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

        // set header and footer fonts
        /*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

        // set default monospaced font
        /*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

        // set margins
        /*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
        /*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
        /*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

        // set auto page breaks
        /*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', 'B', 20);

        // add a page
        $pdf->AddPage();

        /*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

        $pdf->SetFont('helvetica', '', 9);

        // -----------------------------------------------------------------------------
        $base_url = base_url();
        $img = "<img src=\"" . $base_url . "assets/layouts/layout/img/" . $preference['logo_koperasi'] . "\" alt=\"\" width=\"500%\" height=\"500%\"/>";

        $tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"10%\">" . $img . "</td>
			    </tr>
			    <tr>
			    </tr>
			</table>
			<br/>
			<br/>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;\">" . $preference['company_name'] . "</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">LAPORAN BUFFER TANGGAL : &nbsp;&nbsp; " . tgltoview($sesi['start_date']) . "&nbsp;&nbsp; S.D &nbsp;&nbsp;" . tgltoview($sesi['end_date']) . "</div></td>		
			       	       
			    </tr>					
			</table>";

        $pdf->writeHTML($tbl, true, false, false, false, '');

        $tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"4%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO.</div></td>
			        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">TANGGAL</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. DEPOSITO</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"28%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JENIS SIMP BERJANGKA</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NILAI PRODUK</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">BUFFER</div></td>
			       
			    </tr>				
			</table>";

        $no = 1;

        $totalnominalfrom = 0;
        $totalsaldofrom = 0;

        $tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
        foreach ($acctdepositoaccount as $key => $val) {
                $tbl3 .= "
						<tr>
					    	<td width=\"4%\"><div style=\"text-align: center;\">" . $no . "</div></td>
					        <td width=\"11%\"><div style=\"text-align: center;\">" . tgltoview($val['deposito_account_date']) . "</div></td>
					        <td width=\"15%\"><div style=\"text-align: center;\">" . $val['deposito_account_no'] . "</div></td>
					        <td width=\"20%\"><div style=\"text-align: left;\">" . $val['member_name'] . "</div></td>
					        <td width=\"28%\"><div style=\"text-align: left;\">" . $val['deposito_name'] . "</div></td>
					        <td width=\"13%\"><div style=\"text-align: right;\">" . number_format($val['deposito_account_amount'], 2) . "</div></td>
					        <td width=\"10%\"><div style=\"text-align: right;\">" . number_format($val['deposito_account_buffer'], 2) . "</div></td>
					    </tr>
					";

            $totalnilaiproduk += $val['deposito_account_amount'];
            $totalbuffer   += $val['deposito_account_buffer'];

                $no++;
            }


        $grandtotalnilaiproduk     += $totalnilaiproduk;
        $grandtotalbuffer          += $totalbuffer;


        $tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : " . date('d-m-Y H:i:s') . "  " . $this->AcctDepositoAccountBufferReport_model->getUserName($auth['user_id']) . "</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">" . number_format($grandtotalnilaiproduk, 2) . "</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">" . number_format($grandtotalbuffer, 2) . "</div></td>
					</tr>			
			</table>";

        $pdf->writeHTML($tbl1 . $tbl2 . $tbl3 . $tbl4, true, false, false, false, '');


        ob_clean();

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $filename = 'Laporan Mutasi Harian Non Tunai Simpanan.pdf';
        $pdf->Output($filename, 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
    }

    public function export($sesi){
        $auth = $this->session->userdata('auth');
        if ($auth['branch_status'] == 1) {
            if ($sesi['branch_id'] == '' || $sesi['branch_id'] == 0) {
                $branch_id = '';
            } else {
                $branch_id = $sesi['branch_id'];
            }
        } else {
            $branch_id = $auth['branch_id'];
        }

        $preference        = $this->AcctDepositoAccountBufferReport_model->getPreferenceCompany();

        $acctdepositoaccount    = $this->AcctDepositoAccountBufferReport_model->getAcctDepositoAccount($sesi['start_date'], $sesi['end_date'], $branch_id);

        // print_r($acctdepositoaccount); exit;

        if (count($acctdepositoaccount) != 0) {
            $this->load->library('Excel');

            $this->excel->getProperties()->setCreator("CST FISRT")
                ->setLastModifiedBy("CST FISRT")
                ->setTitle("Laporan Buffer Simpanan Berjangka")
                ->setSubject("")
                ->setDescription("Laporan Buffer Simpanan Berjangka")
                ->setKeywords("Laporan, Buffer, Simpanan Berjangka")
                ->setCategory("Laporan Buffer Simpanan Berjangka");

            $this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
            $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);


            $this->excel->getActiveSheet()->mergeCells("B1:H1");
            $this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->setCellValue('B1', "LAPORAN BUFFER SIMPANAN BERJANGKA TGL : " . $sesi['start_date'] . " S.D " . $sesi['end_date'] . "");


            $this->excel->getActiveSheet()->setCellValue('B3', "No");
            $this->excel->getActiveSheet()->setCellValue('C3', "Tanggal");
            $this->excel->getActiveSheet()->setCellValue('D3', "No. Deposito");
            $this->excel->getActiveSheet()->setCellValue('E3', "Nama");
            $this->excel->getActiveSheet()->setCellValue('F3', "Jenis Simp Berjangka");
            $this->excel->getActiveSheet()->setCellValue('G3', "Nilai Produk");
            $this->excel->getActiveSheet()->setCellValue('H3', "Buffer");


            $a = 4;
            $no = 0;
            $j = $a;
            $totalnilaiproduk = 0;
            $totalbuffer = 0;
            $grandtotalnilaiproduk = 0;
            $grandtotalbuffer = 0;

            foreach ($acctdepositoaccount as $key => $val) {

                // $no++;

                    $no++;
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->getStyle('B' . $j . ':H' . $j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $this->excel->getActiveSheet()->getStyle('B' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excel->getActiveSheet()->getStyle('C' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->excel->getActiveSheet()->getStyle('D' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->excel->getActiveSheet()->getStyle('E' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->excel->getActiveSheet()->getStyle('F' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    $this->excel->getActiveSheet()->setCellValue('B' . $j, $no);
                    $this->excel->getActiveSheet()->setCellValue('C' . $j, tgltoview($val['deposito_account_date']));
                    $this->excel->getActiveSheet()->setCellValue('D' . $j, $val['deposito_account_no']);
                    $this->excel->getActiveSheet()->setCellValue('E' . $j, $val['member_name']);
                    $this->excel->getActiveSheet()->setCellValue('F' . $j, $val['deposito_name']);
                    $this->excel->getActiveSheet()->setCellValue('G' . $j, number_format($val['deposito_account_amount'], 2));
                    $this->excel->getActiveSheet()->setCellValue('H' . $j, number_format($val['deposito_account_buffer'], 2));

                $totalnilaiproduk += $val['deposito_account_amount'];
                $totalbuffer      += $val['deposito_account_buffer'];

                $j++;
                
            }

            $grandtotalnilaiproduk     += $totalnilaiproduk;
            $grandtotalbuffer          += $totalbuffer;

            $m = $j;

            $this->excel->getActiveSheet()->getStyle('B' . $m . ':H' . $m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
            $this->excel->getActiveSheet()->getStyle('B' . $m . ':H' . $m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $this->excel->getActiveSheet()->mergeCells('B' . $m . ':F' . $m);
            $this->excel->getActiveSheet()->setCellValue('B' . $m, 'Total');
            $this->excel->getActiveSheet()->setCellValue('G' . $m, number_format($grandtotalnilaiproduk, 2));
            $this->excel->getActiveSheet()->setCellValue('H' . $m, number_format($grandtotalbuffer, 2));

            $filename = 'Laporan_Buffer_Simpanan_Berjangka.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $objWriter = IOFactory::createWriter($this->excel, 'Excel5');
            ob_end_clean();
            $objWriter->save('php://output');
        } else {
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}
