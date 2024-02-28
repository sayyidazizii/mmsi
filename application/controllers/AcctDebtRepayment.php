<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDebtRepayment extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDebtRepayment_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-acctdebtrepayment');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');				
			}
			$this->session->set_userdata('filter-acctdebtrepayment', $sesi);

			// print_r($sesi);exit();

			$this->session->unset_userdata('addAcctDebtRepayment-'.$unique['unique']);	
			$this->session->unset_userdata('acctdebtrepaymenttoken-'.$unique['unique']);
			$this->session->unset_userdata('acctdebtrepaymenttokenedit-'.$unique['unique']);

			$this->AcctDebtRepayment_model->truncateAcctDataRepaymentItemTemp();

			$data['main_view']['acctdebtrepayment']			= $this->AcctDebtRepayment_model->getAcctDebtRepayment();
			$data['main_view']['content']					= 'AcctDebtRepayment/ListAcctDebtRepayment_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> $this->input->post('start_date',true),
				"end_date" 		=> $this->input->post('end_date',true),
			);

			$this->session->set_userdata('filter-acctdebtrepayment',$data);
			redirect('debt-repayment');
		}

		public function reset_list(){

			$this->session->unset_userdata('filter-acctdebtrepayment');
			redirect('debt-repayment');
		}
		
		public function getAcctDebtRepaymentList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctdebtrepayment');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
			}

			$list = $this->AcctDebtRepayment_model->get_datatables_master($sesi['start_date'], $sesi['end_date']);

	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $debtrepayment) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $debtrepayment->debt_repayment_no;
	            $row[] = $debtrepayment->debt_repayment_date;
	            $row[] = number_format($debtrepayment->debt_repayment_amount, 2);
				$row[] = '<a href="'.base_url().'debt-repayment/detail/'.$debtrepayment->debt_repayment_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-bars"></i> Detail</a>';
	            
	            $data[] = $row;
	        }

	        $output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->AcctDebtRepayment_model->count_all_master($sesi['start_date'], $sesi['end_date']),
						"recordsFiltered" => $this->AcctDebtRepayment_model->count_filtered_master($sesi['start_date'], $sesi['end_date']),
						"data" => $data,
	                );

	        echo json_encode($output);
		}

		public function detailAcctDebtRepayment(){
			$auth 				= $this->session->userdata('auth');
			$debt_repayment_id 	= $this->uri->segment(3);

			$data['main_view']['debtrepaymentdetail']		= $this->AcctDebtRepayment_model->getAcctDebtRepayment_Detail($debt_repayment_id);
			$data['main_view']['debtrepaymentitem']			= $this->AcctDebtRepayment_model->getAcctDebtRepaymentItem($debt_repayment_id);
			$data['main_view']['content']					= 'AcctDebtRepayment/DetailAcctDebtRepayment_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctDebtRepayment(){
			$auth 	= $this->session->userdata('auth');

			$data['main_view']['acctdebtrepaymentitemtemp']	= $this->AcctDebtRepayment_model->getAcctDebtRepaymentItemTemp();
			$data['main_view']['content']					= 'AcctDebtRepayment/FormAddAcctDebtRepayment_view';
			$this->load->view('MainPage_view',$data);
		}

		public function addArrayAcctDebtRepayment(){
			$auth 		= $this->session->userdata('auth');

			$this->AcctDebtRepayment_model->truncateAcctDataRepaymentItemTemp();

			$fileName 	= $_FILES['excel_file']['name'];
			$fileSize 	= $_FILES['excel_file']['size'];
			$fileError 	= $_FILES['excel_file']['error'];
			$fileType 	= $_FILES['excel_file']['type'];

			$config['upload_path'] 		= './assets/';
            $config['file_name'] 		= $fileName;
            $config['allowed_types'] 	= 'xls|xlsx';
            $config['max_size']        	= 10000;

			$this->load->library('upload');
            $this->upload->initialize($config);

			if(! $this->upload->do_upload('excel_file') ){
				$msg = "<div class='alert alert-danger alert-dismissable'>                
					".$this->upload->display_errors('', '')."
					</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('debt-repayment/add');
			}else{
				$media 			= $this->upload->data('excel_file');
				$inputFileName 	= './assets/'.$config['file_name'];

				try {
					$inputFileType 	= IOFactory::identify($inputFileName);
					$objReader 		= IOFactory::createReader($inputFileType);
					$objPHPExcel 	= $objReader->load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}

				$sheet 			= $objPHPExcel->getSheet(0);
				$highestRow 	= $sheet->getHighestRow();
				$highestColumn 	= $sheet->getHighestColumn();

				for ($row = 2; $row <= $highestRow; $row++){ 
					$rowData 	= $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);

					$member_id 	= $this->AcctDebtRepayment_model->getCoreMemberID($rowData[0][0]);

					$data	= array (
						'member_id'							=> $member_id,
						'debt_repayment_item_temp_amount'	=> $rowData[0][1],
					);

					if($data['member_id'] != ''){
						$this->AcctDebtRepayment_model->insertAcctDataRepaymentItemTemp($data);
					}
				}
				unlink($inputFileName);
				$msg = "<div class='alert alert-success'>                
							Import Data Excel
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('debt-repayment/add');
			}
		}
		
		public function processAddAcctDebtRepayment(){
			$auth 		= $this->session->userdata('auth');
			
			$acctdebtrepaymenttemp = $this->AcctDebtRepayment_model->getAcctDebtRepaymentItemTemp();
			$total = 0;
			foreach($acctdebtrepaymenttemp as $key => $val){
				$total += $val['debt_repayment_item_temp_amount'];
			}

			$data = array (
				'debt_repayment_date' 	=> date('Y-m-d'),
				'debt_repayment_amount' => $total,
				'created_id'			=> $auth['user_id']
			);

			if($this->AcctDebtRepayment_model->insertAcctDebtRepayment($data)){
				$debt_repayment_id = $this->AcctDebtRepayment_model->getAcctDebtRepaymentLast($auth['user_id']);

				foreach($acctdebtrepaymenttemp as $key => $val){
					$dataitem = array(
						'debt_repayment_id' 			=> $debt_repayment_id,
						'member_id' 					=> $val['member_id'],
						'debt_repayment_item_amount' 	=> $val['debt_repayment_item_temp_amount'],
						'created_id'					=> $auth['user_id'],
					);

					if($this->AcctDebtRepayment_model->insertAcctDebtRepaymentItem($dataitem)){
						$member_account_receivable_amount = $this->AcctDebtRepayment_model->getCoreMemberAccountReceivableAmount($val['member_id']);

						$datamember = array(
							'member_id' 						=> $val['member_id'],
							'member_account_receivable_amount' 	=> $member_account_receivable_amount - $val['debt_repayment_item_temp_amount'],
						);

						$this->AcctDebtRepayment_model->updateCoreMemberAccountReceivableAmount($datamember);
					}
				}

				$auth = $this->session->userdata('auth');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Pelunasan Piutang Potong Gaji Sukses
						</div> ";
				$this->session->unset_userdata('addAcctDebtRepayment');
				$this->session->set_userdata('message',$msg);
				redirect('debt-repayment');
			}else{
				$this->session->set_userdata('addAcctDebtRepayment',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Pelunasan Piutang Potong Gaji Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('debt-repayment');
			}
		}

		public function exportMemberAccountReceivableAmount(){
			$auth 				= $this->session->userdata('auth');
			$preferencecompany 	= $this->AcctDebtRepayment_model->getPreferenceCompany();
			$coremember			= $this->AcctDebtRepayment_model->getCoreMember();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7);
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			$pdf->SetFont('helvetica', 'B', 20);

			$pdf->AddPage();

			$pdf->SetFont('helvetica', '', 12);

			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"500%\" height=\"500%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">" .$img."</td>
			        <td width=\"50%\"><div style=\"text-align: left; font-size:14px\">DAFTAR PIUTANG POTONG GAJI</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">".date('d M Y')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
			        <td width=\"25%\"><div style=\"text-align: center;\">Nomor Anggota</div></td>
			        <td width=\"35%\"><div style=\"text-align: center;\">Nama Anggota</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;\">Status</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\">Jumlah</div></td>
			    </tr>";

			$no = 1;
			foreach($coremember as $key => $val){
				if($val['member_account_receivable_status'] == 0){
					$status = "Aktif";
				}else{
					$status = "Diblokir";
				}
				$tbl1 .= "
				<tr>
					<td width=\"5%\"><div style=\"text-align: center;\">".$no."</div></td>
					<td width=\"25%\"><div style=\"text-align: left;\">".$val['member_no']."</div></td>
					<td width=\"35%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					<td width=\"15%\"><div style=\"text-align: left;\">".$status."</div></td>
					<td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['member_account_receivable_amount'], 2)."</div></td>
				</tr>
				";
				$no++;
			}

			$tbl1 .="</table>";

			$pdf->writeHTML($tbl1, true, false, false, false, '');
			if (ob_get_length() > 0){
				ob_clean();	
			}
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DataPiutangPotongGajiAnggota.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
	}
?>