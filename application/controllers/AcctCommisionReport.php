<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCommisionReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('Commision_Report_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('Configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$data['main_view']['coredeposito']	= create_double($this->Commision_Report_model->getAccountDeposito(),'deposito_account_id', 'deposito_account_no');
			$corebranch 						= create_double_branch($this->Commision_Report_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctCommision/ListAcctCommsionReport_view';
				
			$this->load->view('MainPage_view', $data);
		}
		public function viewreport(){
			$sesi = array (
				'deposito_account_id'		=> $this->input->post('deposito_account_id', true),
				'branch_id'					=> $this->input->post('branch_id', true),
				'start_date'				=> tgltodb($this->input->post('start_date', true)),
				'end_date'					=> tgltodb($this->input->post('end_date', true)),
				"view"						=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 

			$preferencecompany 		= $this->Commision_Report_model->getPreferenceCompany();

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			//dump
			$acctcommision 	= $this->Commision_Report_model->getAcctCommission($sesi['deposito_account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);						

			// echo json_encode($acctcommision);
			// $membername = $this->Commision_Report_model->getMemberName(52);
			// echo json_encode($membername);

			// exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

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
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
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
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"500%\" height=\"500%\"/>";

			$tbl0 = "
			<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"10%\">" .$img."</td>
			    </tr>
			    <tr>
			    </tr>
			</table>
			<br/>
			<br/>
			<br/>
			<br/>";
			
			if(!empty($sesi['deposito_account_id'])){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
						<tr>
					        <td colspan=\"2\"><div style=\"text-align: left;font-size:10; font-weight:bold\">".$preferencecompany['company_name']."</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR KOMISI SIMPANAN BERJANGKA</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Deposito</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No.Tabungan/Agent</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Komisi Agent</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No.Tabungan/Supervisor</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Komisi Supervisor</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Status</div></td>
					       
					    </tr>				
					</table>";

					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
					$no = 1;
					foreach ($acctcommision as $val) {
						$status = '';
								if($val['commission_disbursed_status'] == 1){
									$status = 'Dicairkan';
								}else{
									$status = 'Belum Cair';
								}
								
								$tbl3 .= "
									<tr>
								    	<td width=\"5%\"><div style=\"text-align: center;\">".$no."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['deposito_account_no']."</div></td>
								        <td width=\"15%\"><div style=\"text-align: center;\">".$val['commission_date']."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$this->Commision_Report_model->getMemberName($val['savings_account_id_agent'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['commission_disbursed_agent']."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$this->Commision_Report_model->getMemberName($val['savings_account_id_supervisor'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['commission_disbursed_supervisor']."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$status."</div></td>
								    </tr>
								";
								$no++;

								$totalagent += $val['commission_disbursed_agent'];
								$totalsupervisor += $val['commission_disbursed_supervisor'];
					
					}

					$tbl4 = "	
						<tr>
							<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->Commision_Report_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\">".number_format($totalagent, 2)."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\"></div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\">".number_format($totalsupervisor, 2)."</div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\"></div></td>
						</tr>						
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
						<tr>
					        <td colspan=\"2\"><div style=\"text-align: left;font-size:10; font-weight:bold\">".$preferencecompany['company_name']."</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR KOMISI SIMPANAN BERJANGKA</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
						<td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Deposito</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No.Tabungan/Agent</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Komisi Agent</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No.Tabungan/Supervisor</div></td>
						<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Komisi Supervisor</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Status</div></td>
					    </tr>				
					</table>";

					$no = 1;

					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

							$no = 1;
							foreach ($acctcommision as $val) {
								$status = '';
								if($val['commission_disbursed_status'] == 1){
									$status = 'Dicairkan';
								}else{
									$status = 'Belum Cair';
								}
								
								$tbl3 .= "
									<tr>
								    	<td width=\"5%\"><div style=\"text-align: center;\">".$no."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['deposito_account_no']."</div></td>
								        <td width=\"15%\"><div style=\"text-align: center;\">".$val['commission_date']."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$this->Commision_Report_model->getMemberName($val['savings_account_id_agent'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['commission_disbursed_agent']."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$this->Commision_Report_model->getMemberName($val['savings_account_id_supervisor'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$val['commission_disbursed_supervisor']."</div></td>
								        <td width=\"10%\"><div style=\"text-align: center;\">".$status."</div></td>
								    </tr>
								";
								$no++;

								$totalagent += $val['commission_disbursed_agent'];
								$totalsupervisor += $val['commission_disbursed_supervisor'];
					
					}

					$tbl4 = "	
						<tr>
							<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->Commision_Report_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\">".number_format($totalagent, 2)."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\"></div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\">".number_format($totalsupervisor, 2)."</div></td>
							<td colspan =\"1\" style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:center\"></div></td>
						</tr>						
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			}

			if (ob_get_length() > 0){
				ob_clean();
			}
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan_Simpanan_Per_BO.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){	
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			
			$acctsavings 			= $this->AcctSavingsAccountOfficerReport_model->getAcctSavings();
			$preferencecompany 		= $this->AcctSavingsAccountOfficerReport_model->getPreferenceCompany();

			
			if(count($acctsavings) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Nominatif Simpanan")
									 ->setSubject("")
									 ->setDescription("Laporan Nominatif Simpanan")
									 ->setKeywords("Laporan, Nominatif, Simpanan")
									 ->setCategory("Laporan Nominatif Simpanan");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:G1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				
				$this->excel->getActiveSheet()->mergeCells("B2:G2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B3:G3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getFont()->setBold(true);
				if($sesi['office_id'] == 0){
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR SIMPANAN");
				} else {
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR SIMPANAN ".$this->AcctSavingsAccountOfficerReport_model->getOfficeName($sesi['office_id']));
				}
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Rek");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				//$this->excel->getActiveSheet()->setCellValue('F3',"SRH");
				$this->excel->getActiveSheet()->setCellValue('F3',"Basil");
				$this->excel->getActiveSheet()->setCellValue('G3',"Saldo");
				
				
				$no=0;
				$totalbasil = 0;
				$totalsaldo = 0;
				if(empty($sesi['office_id'])){
					$j=4;
					foreach ($acctsavings as $k => $v) {

						$acctsavingsaccount 	= $this->AcctSavingsAccountOfficerReport_model->getAcctSavingsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $v['savings_id'], $branch_id);	

						foreach($acctsavingsaccount as $key=>$val){
							$savings_profit_sharing = $this->AcctSavingsAccountOfficerReport_model->getSavingsProfitSharing($val['savings_account_id']);

							if(is_numeric($key)){
								$no++;
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($savings_profit_sharing,2));
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['savings_account_last_balance'],2));
					
								$totalbasil += $savings_profit_sharing;
								$totalsaldo += $val['savings_account_last_balance'];
								
							}else{
								continue;
							}
							$j++;
							$no++;
						}
					}
				} else {
					$i=4;
					$totalbasil = 0;
					$totalsaldo = 0;
					foreach ($acctsavings as $k => $v) {
						$acctsavingsaccount 	= $this->AcctSavingsAccountOfficerReport_model->getAcctSavingsAccount($sesi['office_id'], $sesi['start_date'], $sesi['end_date'], $v['savings_id'], $branch_id);

						if(!empty($acctsavingsaccount)){
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['savings_name']);

							
							$nov= 0;
							$j=$i+1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;

							foreach($acctsavingsaccount as $key=>$val){
								$savings_profit_sharing = $this->AcctSavingsAccountOfficerReport_model->getSavingsProfitSharing($val['savings_account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

								if(is_numeric($key)){
									$nov++;
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									


									$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, $savings_profit_sharing);
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['savings_account_last_balance'],2));
						
									
								}else{
									continue;
								}
								$j++;

								$subtotalbasil += $savings_profit_sharing;
								$subtotalsaldo += $val['savings_account_last_balance'];
							}							

							$m = $j;

							$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
							$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
							$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');
							$this->excel->getActiveSheet()->setCellValue('F'.$m, number_format($subtotalbasil,2));
							$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($subtotalsaldo,2));

							
						}
						$i = $m+1;
					}

					$totalbasil += $subtotalbasil;
					$totalsaldo += $subtotalsaldo;
					
				}

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totalbasil,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($totalsaldo,2));
				
				$filename='Daftar_Simpanan.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				if(ob_get_length()>0){
					ob_end_clean();
				}
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}


	}
?>