<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctDepositoAccount extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('Connection_model');
		$this->load->model('MainPage_model');
		$this->load->model('AcctDepositoAccount_model');
		$this->load->model('CoreMember_model');
		$this->load->model('CoreOffice_model');
		$this->load->model('CoreCommision_model');
		$this->load->model('Library_model');
		$this->load->model('AcctSavingsAccount_model');
		$this->load->model('AcctSavingsTransferMutation_model');
		$this->load->model('AcctDepositoProfitSharingCheck_model');
		$this->load->helper('sistem');
		$this->load->helper('url');
		$this->load->database('default');
		$this->load->library('configuration');
		$this->load->library('fungsi');
		$this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
	}

	public function index(){
		$unique = $this->session->userdata('unique');

		$export_master_data_id 			= $this->Library_model->getIDMenu('deposito-account/get-master-data-list');
		$export_master_data_id_mapping 	= $this->Library_model->getIDMenuOnSystemMapping($export_master_data_id);

		if ($export_master_data_id_mapping == 1) {
			$export_id = 1;
		} else {
			$export_id = 0;
		}

		$this->session->unset_userdata('acctdepositoaccounttoken-' . $unique['unique']);
		$this->session->unset_userdata('member_id');

		$data['main_view']['acctdeposito']		= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['corebranch']		= create_double($this->AcctDepositoAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
		$data['main_view']['export_id']			= $export_id;
		$data['main_view']['content']			= 'AcctDepositoAccount/ListAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function filter(){
		$data = array(
			// "start_date" 	=> tgltodb($this->input->post('start_date',true)),
			// "end_date" 		=> tgltodb($this->input->post('end_date',true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-acctdepositoaccount', $data);
		redirect('deposito-account');
	}

	public function function_elements_add(){
		$unique 	= $this->session->userdata('unique');
		$name 		= $this->input->post('name', true);
		$value 		= $this->input->post('value', true);
		$sessions	= $this->session->userdata('addacctdepositoaccount-' . $unique['unique']);
		$sessions[$name] = $value;
		$this->session->set_userdata('addacctdepositoaccount-' . $unique['unique'], $sessions);
	}

	public function reset_data(){
		$unique 	= $this->session->userdata('unique');
		$sessions	= $this->session->unset_userdata('addacctdepositoaccount-' . $unique['unique']);
		$this->session->unset_userdata('member_id');
		redirect('deposito-account/add');
	}

	public function reset_close(){
		$member_id = $this->uri->segment(3);
		$unique 	= $this->session->userdata('unique');
		$this->session->unset_userdata('addacctdepositoaccount-' . $unique['unique']);
		redirect('deposito-account/add-closed/' . $member_id);
	}

	public function reset_search(){
		$this->session->unset_userdata('filter-acctdepositoaccount');
		redirect('deposito-account');
	}

	public function getListAcctDepositoAccount(){
		$auth 	= $this->session->userdata('auth');
		$sesi	= $this->session->userdata('filter-acctdepositoaccount');
		if (!is_array($sesi)) {
			// $sesi['start_date']		= date('Y-m-d');
			// $sesi['end_date']		= date('Y-m-d');
			$sesi['deposito_id']		= '';
			if ($auth['branch_status'] == 0) {
				$sesi['branch_id']		= $auth['branch_id'];
			} else {
				$sesi['branch_id']		= '';
			}
		}

		$print_certificate_id 			= $this->Library_model->getIDMenu('deposito-account/print-certificate-deposito');
		$print_certificate_id_mapping 	= $this->Library_model->getIDMenuOnSystemMapping($print_certificate_id);


		$list = $this->AcctDepositoAccount_model->get_datatables_master($sesi['deposito_id'], $sesi['branch_id']);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $depositoaccount) {
			$deposito_accrual_last_balance = $this->AcctDepositoAccount_model->getAcctDepositoAccrualLastBalance($depositoaccount->deposito_account_id);
			$acctdepositoaccount 		   = $this->AcctDepositoAccount_model->getAcctDepositoAccountDetail($depositoaccount->deposito_account_id);

			$interest_total = $deposito_accrual_last_balance + $acctdepositoaccount['deposito_account_nisbah'];

			$button 					= '';
			$button_commission 			= '<a href="' . base_url() . 'deposito-account/commission/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs purple" role="button"><i class="fa fa-money"></i> Komisi</a>';
			$button_print_certificate 	= '<a href="' . base_url() . 'deposito-account/print-certificate-front/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs yellow" role="button"><i class="fa fa-print"></i> Cetak Depan Sertifikat</a>
				<a href="' . base_url() . 'deposito-account/print-certificate-back/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs green" role="button"><i class="fa fa-print"></i> Cetak Belakang Sertifikat</a>
				<a href="' . base_url() . 'deposito-account/print-bilyet/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs blue-hoki" role="button"><i class="fa fa-print"></i> Cetak Bilyet</a>';

			if ($print_certificate_id_mapping == 1) {
				$button .= $button_print_certificate;
			}
			if ($depositoaccount->deposito_account_extra_type == '1') {
				$type_extra = 'ARO';
			} else {
				$type_extra = 'Manual';
			}
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $depositoaccount->deposito_account_no;
			$row[] = $depositoaccount->member_name;
			$row[] = $depositoaccount->deposito_name;
			$row[] = $type_extra;
			$row[] = $depositoaccount->deposito_account_serial_no;
			$row[] = tgltoview($depositoaccount->deposito_account_date);
			$row[] = tgltoview($depositoaccount->deposito_account_due_date);
			$row[] = number_format($depositoaccount->deposito_account_amount, 2);
			$row[] = $depositoaccount->deposito_account_unique_code;
			$row[] = number_format($interest_total);
			$row[] = number_format($depositoaccount->deposito_account_cashback, 2);
			$row[] = number_format($depositoaccount->deposito_account_commission_on_hold_agent, 2);
			$row[] = number_format($depositoaccount->deposito_account_commission_disbursed_agent, 2);
			$row[] = number_format($depositoaccount->deposito_account_commission_on_hold_supervisor, 2);
			$row[] = number_format($depositoaccount->deposito_account_commission_disbursed_supervisor, 2);
			$row[] = $depositoaccount->deposito_account_condition;
			$row[] = $depositoaccount->deposito_account_reject_remark;
			if ($depositoaccount->validation == 0) {
				$row[] = '
				<a href="'.base_url().'deposito-account/validation/'.$depositoaccount->deposito_account_id.'" class="btn btn-xs green-jungle" role="button" onClick="javascript:return confirm(\'Apakah anda yakin akan memproses Simpanan Berjangka ini ?\')"><i class="fa fa-check"></i> Proses</a>
				<a class="btn btn-xs red" id="open_modal_reject" data-toggle="modal" data-target="#modal_reject" data-id="'.$depositoaccount->deposito_account_id.'"><i class="fa fa-times"></i> Reject</a>'.$button_commission;
			} else if ($depositoaccount->validation == 1) {
				$row[] = '<a href="'.base_url().'deposito-account/print-note/'.$depositoaccount->deposito_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>'.$button.$button_commission;
			} else {
				$row[] = '
				<a class="btn btn-xs red">Rejected</a>
				';
			}
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->AcctDepositoAccount_model->count_all_master($sesi['deposito_id'], $sesi['branch_id']),
			"recordsFiltered" => $this->AcctDepositoAccount_model->count_filtered_master($sesi['deposito_id'], $sesi['branch_id']),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function getMasterDataAcctDepositoAccount(){
		$data['main_view']['acctdeposito']		= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['corebranch']		= create_double($this->AcctDepositoAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
		$data['main_view']['content']	= 'AcctDepositoAccount/ListMasterDataAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function filtermasterdata(){
		$data = array(
			"start_date" 	=> tgltodb($this->input->post('start_date', true)),
			"end_date" 		=> tgltodb($this->input->post('end_date', true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-masterdataacctdepositoaccount', $data);
		redirect('deposito-account/get-master');
	}

	public function getMasterDataAcctDepositoAccountList(){
		$sesi	= 	$this->session->userdata('filter-masterdataacctdepositoaccount');
		$auth 	= $this->session->userdata('auth');
		if (!is_array($sesi)) {
			// $sesi['start_date']		= date('Y-m-d');
			// $sesi['end_date']		= date('Y-m-d');
			$sesi['deposito_id']		= '';
			if ($auth['branch_status'] == 0) {
				$sesi['branch_id']		= $auth['branch_id'];
			} else {
				$sesi['branch_id']		= '';
			}
		}

		$list = $this->AcctDepositoAccount_model->get_datatables_master($sesi['deposito_id'], $sesi['branch_id']);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $depositoaccount) {
			if ($depositoaccount->deposito_account_extra_type == '1') {
				$type_extra = 'ARO';
			} else {
				$type_extra = 'Manual';
			}
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $depositoaccount->deposito_account_no;
			$row[] = $depositoaccount->member_name;
			$row[] = $depositoaccount->deposito_name;
			$row[] = $type_extra;
			$row[] = $depositoaccount->deposito_account_serial_no;
			$row[] = tgltoview($depositoaccount->deposito_account_date);
			$row[] = tgltoview($depositoaccount->deposito_account_due_date);
			$row[] = number_format($depositoaccount->deposito_account_amount, 2);
			$row[] = $depositoaccount->deposito_interest_rate;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->AcctDepositoAccount_model->count_all_master($sesi['deposito_id'], $sesi['branch_id']),
			"recordsFiltered" => $this->AcctDepositoAccount_model->count_filtered_master($sesi['deposito_id'], $sesi['branch_id']),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function getListCoreMember(){
		$auth = $this->session->userdata('auth');
		$data_state = 0;
		$list = $this->CoreMember_model->get_datatables($data_state);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $customers) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $customers->member_no;
			$row[] = $customers->member_name;
			$row[] = $customers->member_address;
			$row[] = '<a href="' . base_url() . 'deposito-account/add/' . $customers->member_id . '" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->CoreMember_model->count_all($data_state),
			"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function getListAcctSavingAccount(){
		$member_id 			= $this->uri->segment(3);
		$auth = $this->session->userdata('auth');
		$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $savingsaccount) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $savingsaccount->savings_account_no;
			$row[] = $savingsaccount->member_name;
			$row[] = $savingsaccount->member_address;
			$row[] = '<a href="' . base_url() . 'deposito-account/add/' . $member_id . '/' . $savingsaccount->savings_account_id . '" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
			$data[] = $row;
		}



		// print_r($list);exit;

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
			"recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function addAcctDepositoAccount(){
		$member_id 			= $this->uri->segment(3);
		$savings_account_id = $this->uri->segment(4);

		$unique = $this->session->userdata('unique');
		$token 	= $this->session->userdata('acctdepositoaccounttoken-' . $unique['unique']);

		if (empty($token)) {
			$token = md5(rand());
			$this->session->set_userdata('acctdepositoaccounttoken-' . $unique['unique'], $token);
		}
		$depositoextratype = array(
			array(
				"deposito_account_extra_type" => 0,
				"deposito_account_extra_type_name" => 'Manual',
			),
			array(
				"deposito_account_extra_type" => 1,
				"deposito_account_extra_type_name" => 'ARO',
			),
		);

		$today				= date('Y-m-d');
		$uniquecode 		= rand(100, 999);
		$uniquecode_check 	= $this->AcctDepositoAccount_model->getAcctDepositoAccountUniqueCode($today, $uniquecode);
		while ($uniquecode_check) {
			$uniquecode = rand(100, 999);
			$uniquecode_check 	= $this->AcctDepositoAccount_model->getAcctDepositoAccountUniqueCode($today, $uniquecode);
		}

		//Get Data member agent & supervisor
		$data_member 	 = $this->AcctDepositoAccount_model->getCoreMember_Detail($member_id);
		$data_agent  	 = $this->AcctDepositoAccount_model->getCoreMember_Detail($data_member['member_reference']);
		$data_supervisor = $this->AcctDepositoAccount_model->getCoreMember_Detail($data_agent['member_reference']);

		$savings_account_agent  	 = $this->AcctDepositoAccount_model->getAcctSavingsAccountAgentSPV($data_agent['member_id']);
		$savings_account_supervisor  = $this->AcctDepositoAccount_model->getAcctSavingsAccountAgentSPV($data_supervisor['member_id']);
		// print_r($data_agent['member_id']); exit;





		$data['main_view']['coremember']				= $this->AcctDepositoAccount_model->getCoreMember_Detail($member_id);
		$data['main_view']['acctdeposito']				= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['depositoextratype']			= create_double($depositoextratype, 'deposito_account_extra_type', 'deposito_account_extra_type_name');
		$data['main_view']['coreoffice']				= create_double($this->AcctDepositoAccount_model->getCoreOffice(), 'office_id', 'office_name', 'savings_account_id');
		$data['main_view']['commisionagent']			= create_double($this->CoreCommision_model->getDataCommisionAgent() ,'core_commision_id', 'commision_name');
		$data['main_view']['commisionspv']				= create_double($this->CoreCommision_model->getDataCommisionSpv() ,'core_commision_id', 'commision_name');
		$data['main_view']['acctsavingsaccount']		= $this->AcctDepositoAccount_model->getAcctSavingsAccount_Detail($savings_account_id);
		$data['main_view']['memberagent']				= $savings_account_agent;
		$data['main_view']['membersupervisor']			= $savings_account_supervisor;
		$data['main_view']['membergender']				= $this->configuration->MemberGender();
		$data['main_view']['depositointerestperiod']	= $this->configuration->DepositoInterestPeriod();
		$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
		$data['main_view']['familyrelationship']		= $this->configuration->FamilyRelationship();
		$data['main_view']['uniquecode']				= $uniquecode;
		$data['main_view']['acctdepositoaccount_last']	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Last(date('Y-m-d'));
		$data['main_view']['content']					= 'AcctDepositoAccount/FormAddAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function generateUniqueCode(){
		$today				= date('Y-m-d');
		$uniquecode 		= rand(100, 999);
		$uniquecode_check 	= $this->AcctDepositoAccount_model->getAcctDepositoAccountUniqueCode($today, $uniquecode);
		while ($uniquecode_check) {
			$uniquecode = rand(100, 999);
			$uniquecode_check 	= $this->AcctDepositoAccount_model->getAcctDepositoAccountUniqueCode($today, $uniquecode);
		}
		echo $uniquecode;
	}

	// public function tesData(){
	// 	$json='[
	// 		{
	// 		"id": "121",
	// 		"amount": 21
	// 		},
	// 		{
	// 		"id": "122",
	// 		"amount": 22
	// 		},
	// 		{"id": "123",
	// 		"amount": 25
	// 		}
	// 		]';
	// 		$arr		= json_decode($json);
	// 		$arraykey 	= array_search('121     ', array_column(json_decode($json, true), 'id'));	
	// 		$arramount	= $arr[$arraykey]->amount;
	// 		if(is_numeric($arraykey)){
	// 			unset($arr[$arraykey]);
	// 			$new		= array(
	// 				'id' 		=> 121,
	// 				'amount' 	=> $arramount+1
	// 			);
	// 			array_push($arr, $new);
	// 		}
	// 		print_r($arr);
	// }

	public function getDepositoAccountNo(){
		$date = date('Y-m-d');
		$auth = $this->session->userdata('auth');

		$deposito_id 	= $this->input->post('deposito_id');
		$branchcode 	= $this->AcctDepositoAccount_model->getBranchCode($auth['branch_id']);

		$depositocode 				= $this->AcctDepositoAccount_model->getDepositoCode($deposito_id);
		$depositoperiod 			= $this->AcctDepositoAccount_model->getDepositoPeriod($deposito_id);
		$depositointerestperiod 	= $this->AcctDepositoAccount_model->getDepositoInterestPeriod($deposito_id);
		$depositonisbah 			= $this->AcctDepositoAccount_model->getDepositoNisbah($deposito_id);
		$lastdepositoaccountno 		= $this->AcctDepositoAccount_model->getLastAccountDepositoNo($auth['branch_id'], $deposito_id);

		if ($lastdepositoaccountno->num_rows() <> 0) {
			//jika kode ternyata sudah ada.      
			$data = $lastdepositoaccountno->row_array();
			$kode = intval($data['last_deposito_account_no']) + 1;
		} else {
			//jika kode belum ada      
			$kode = 1;
		}

		$kodemax 		= str_pad($kode, 5, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
		//$new_deposito_account_no 		= $depositocode.$branchcode.$kodemax;    // hasilnya ODJ-9921-0001 dst.
		$new_deposito_account_no 		= $branchcode . $depositocode . $kodemax;    // hasilnya ODJ-9921-0001 dst.
		$new_deposito_account_serial 	= $depositoperiod . "." . $kodemax;

		// $deposito_date_star = date_create(date('Y-m-d'));
		// $deposito_due_date = date_create(date('d-m-Y', strtotime('+' . $depositoperiod . 'month', strtotime($date))));
		// $diff = date_diff($deposito_date_star, $deposito_date_end);
		// $deposito_due_date = $diff->days; 
		if ($depositointerestperiod == 1) {
			$depositointerestperiod_view = 'Mingguan';
			$deposito_due_date = date('d-m-Y', strtotime('+' . $depositoperiod . 'week', strtotime($date)));
		}
		if ($depositointerestperiod == 2) {
			$depositointerestperiod_view = 'Bulanan';
			$deposito_due_date = date('d-m-Y', strtotime('+' . $depositoperiod . 'month', strtotime($date)));
		}
		if ($depositointerestperiod == 3) {
			$depositointerestperiod_view = 'Tahunan';
			$deposito_due_date = date('d-m-Y', strtotime('+' . $depositoperiod . 'year', strtotime($date)));
		}

		// $result = array();
		$result = array(
			'deposito_period'						=> $depositoperiod,
			'deposito_interest_period'				=> $depositointerestperiod,
			'deposito_interest_period_view'			=> $depositointerestperiod_view,
			'deposito_account_no'					=> $new_deposito_account_no,
			'deposito_account_serial_no'			=> $new_deposito_account_serial,
			'deposito_account_due_date'				=> $deposito_due_date,
			'deposito_account_nisbah'				=> $depositonisbah,
		);


		echo json_encode($result);
		// print_r($result); exit;
	}
	public function add_months($months, DateTime $dateObject){
		$next = new DateTime($dateObject->format('Y-m-d'));
		$next->modify('last day of +' . $months . ' month');

		if ($dateObject->format('d') > $next->format('d')) {
			return $dateObject->diff($next);
		} else {
			return new DateInterval('P' . $months . 'M');
		}
	}

	public function endCycle($d1, $months){
		$date = new DateTime($d1);

		// call second function to add the months
		$newDate = $date->add($this->add_months($months, $date));

		// goes back 1 day from date, remove if you want same day of month
		$newDate->sub(new DateInterval('P1D'));

		//formats final date to Y-m-d form
		$dateReturned = $newDate->format('Y-m-d');

		return $dateReturned;
	}

	public function processAddAcctDepositoAccount(){
		$auth = $this->session->userdata('auth');
		$savings_account_id = $this->uri->segment(4);
		$data = array(
			'member_id'											=> $this->input->post('member_id', true),
			'deposito_id'										=> $this->input->post('deposito_id', true),
			'office_id'											=> $this->input->post('office_id', true),
			'branch_id'											=> $auth['branch_id'],
			'savings_account_id'								=> $this->input->post('savings_account_id', true),
			'deposito_account_date'								=> date('Y-m-d'),
			'deposito_account_due_date'							=> tgltodb($this->input->post('deposito_account_due_date', true)),
			'deposito_account_no'								=> $this->input->post('deposito_account_no', true),
			'deposito_account_serial_no'						=> $this->input->post('deposito_account_serial_no', true),
			'deposito_account_amount'							=> $this->input->post('deposito_account_amount', true),
			'deposito_account_interest'							=> $this->input->post('deposito_account_nisbah', true),
			// 'deposito_account_nisbah'						=> $this->input->post('deposito_account_nisbah', true),
			'deposito_account_period'							=> $this->input->post('deposito_period', true),
			'deposito_account_interest_period'					=> $this->input->post('deposito_interest_period', true),
			'deposito_member_heir'								=> $this->input->post('deposito_member_heir', true),
			'deposito_member_heir_address'						=> $this->input->post('deposito_member_heir_address', true),
			'deposito_member_heir_relationship'					=> $this->input->post('deposito_member_heir_relationship', true),
			'deposito_account_extra_type'						=> $this->input->post('deposito_account_extra_type', true),
			'deposito_account_unique_code'						=> $this->input->post('deposito_account_unique_code', true),
			'deposito_account_cashback'							=> $this->input->post('deposito_account_cashback', true),
			'deposito_account_commission_on_hold_agent'			=> $this->input->post('deposito_account_commission_on_hold_agent', true),
			'deposito_account_commission_disbursed_agent'		=> $this->input->post('deposito_account_commission_disbursed_agent', true),
			'deposito_account_commission_on_hold_supervisor'	=> $this->input->post('deposito_account_commission_on_hold_supervisor', true),
			'deposito_account_commission_disbursed_supervisor'	=> $this->input->post('deposito_account_commission_disbursed_supervisor', true),
			'deposito_account_buffer'							=> $this->input->post('deposito_account_buffer', true),
			'savings_account_id_agent'							=> $this->input->post('savings_account_id_agent', true),
			'savings_account_id_supervisor'						=> $this->input->post('savings_account_id_supervisor', true),
			'deposito_account_condition'						=> $this->input->post('deposito_account_condition', true),
			'deposito_account_token'							=> $this->input->post('deposito_account_token', true),
			'created_id'										=> $auth['user_id'],
			'created_on'										=> date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('member_id', 'Anggota', 'required');
		$this->form_validation->set_rules('deposito_id', 'Jenis Simpanan berjangka', 'required');
		$this->form_validation->set_rules('savings_account_id', 'No. Simpanan', 'required');
		$this->form_validation->set_rules('deposito_account_amount', 'Nominal', 'required');
		$this->form_validation->set_rules('office_id', 'Business Officer (BO)', 'required');
		$this->form_validation->set_rules('deposito_account_cashback', 'Cashback', 'required');
		$this->form_validation->set_rules('deposito_account_commission_disbursed_agent', 'Komisi Agent Cair', 'required');
		$this->form_validation->set_rules('deposito_account_commission_on_hold_agent', 'Komisi Agent Ditahan', 'required');
		$this->form_validation->set_rules('deposito_account_commission_disbursed_supervisor', 'Komisi Supervisor Cair', 'required');
		$this->form_validation->set_rules('deposito_account_commission_on_hold_supervisor', 'Komisi Supervisor Ditahan', 'required');
		$this->form_validation->set_rules('deposito_account_buffer', 'Buffer', 'required');

		$transaction_module_code 	= "DEP";
		$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);

		$deposito_account_token 	= $this->AcctDepositoAccount_model->getDepositoAccountToken($data['deposito_account_token']);

		if ($this->form_validation->run() == true) {
			if ($deposito_account_token->num_rows() == 0) {
				if ($this->AcctDepositoAccount_model->insertAcctDepositoAccount($data)) {
					
					$deposito_availability		= $this->AcctDepositoAccount_model->getDepositoAvailability($data['deposito_id']);
					$data_deposito	= array(
						'deposito_id'			=> $data['deposito_id'],
						'deposito_availability'	=> $deposito_availability - 1,
					);

					// print_r($data_deposito); exit;
					$this->AcctDepositoAccount_model->updateAcctDeposito($data_deposito);


					$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Last($data['created_on']);

					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Berjangka Sukses
								</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
					$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
					$this->session->unset_userdata('member_id');
					$this->session->unset_userdata('savings');
					$this->session->set_userdata('message', $msg);
					redirect('deposito-account/print-note/' . $acctdepositoaccount_last['deposito_account_id']);
					// echo  $acctdepositoaccount_last['deposito_account_id'];

				} else {
					$this->session->set_userdata('addacctdepositoaccount', $data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Berjangka Tidak Berhasil
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('deposito-account/add');
				}
			} else {
				$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Last($data['created_on']);

				$auth = $this->session->userdata('auth');
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Rekening Simpanan Berjangka Sukses
							</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
				$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
				$this->session->unset_userdata('member_id');
				$this->session->unset_userdata('savings');
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account/print-note/' . $acctdepositoaccount_last['deposito_account_id']);
				// redirect('deposito-account');
			}
		} else {
			$this->session->set_userdata('addacctdepositoaccount', $data);
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/add');
		}
	}

	public function printNoteAcctDepositoAccount(){
		$auth = $this->session->userdata('auth');
		$deposito_account_id 	= $this->uri->segment(3);
		$preferencecompany 		= $this->AcctDepositoAccount_model->getPreferenceCompany();
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

		$pdf->SetFont('helvetica', '', 12);

		// -----------------------------------------------------------------------------
		$base_url = base_url();
		$img = "<img src=\"" . $base_url . "assets/layouts/layout/img/" . $preferencecompany['logo_koperasi'] . "\" alt=\"\" width=\"500%\" height=\"500%\"/>";

		$tbl = "
			<table  cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"13%\">" . $img . "</td>
			        <td width=\"50%\";><div class=\"ml-1\"; style=\"text-align: left; font-size:14px\"><br>BUKTI SETORAN SIMPANAN BERJANGKA</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : " . date('H:i:s') . "</div></td>
			    </tr>
			</table>";

		$pdf->writeHTML($tbl, true, false, false, false, '');


		$tbl1 = "
			Telah diterima uang dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: " . $acctdepositoaccount['member_name'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: " . $acctdepositoaccount['deposito_account_no'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: " . $acctdepositoaccount['member_address'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: " . numtotxt($acctdepositoaccount['deposito_account_amount']) . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: SETORAN SIMPANAN BERJANGKA</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;" . number_format($acctdepositoaccount['deposito_account_amount'], 2) . "</div></td>
			    </tr>	
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Kode Unik</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: " . $acctdepositoaccount['deposito_account_unique_code'] . "</div></td>
			    </tr>			
			</table>";

		$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr> 
				<br>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">" . $this->AcctDepositoAccount_model->getBranchCity($auth['branch_id']) . ", " . date('d-m-Y') . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

		$pdf->writeHTML($tbl1 . $tbl2, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Kwitansi.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	public function validationAcctDepositoAccount(){
		$auth = $this->session->userdata('auth');
		$deposito_account_id 		= $this->uri->segment(3);
		$preferencecompany 			= $this->AcctSavingsAccount_model->getPreferenceCompany();
		$acctdepositoaccount		= $this->AcctDepositoAccount_model->getAcctDepositoAccountDetail($deposito_account_id);
		$acctdepositoaccountBO		= $this->AcctDepositoAccount_model->getAcctDepositoAccountBO($deposito_account_id);

		$data = array(
			'deposito_account_id'  	=> $deposito_account_id,
			'validation'			=> 1,
			'validation_id'			=> $auth['user_id'],
			'validation_on'			=> date('Y-m-d H:i:s'),
		);

		if ($this->AcctDepositoAccount_model->validationAcctDepositoAccount($data)) {
			$day 	= date('d', strtotime($acctdepositoaccount['deposito_account_date']));
			$month 	= date('m', strtotime($acctdepositoaccount['deposito_account_date']));
			$year 	= date('Y', strtotime($acctdepositoaccount['deposito_account_date']));
			$date 	= date('Y-m-d', strtotime($acctdepositoaccount['deposito_account_date']));

			// print_r($year); exit;

			$token 	= md5(rand());

			for ($i = 1; $i <= $acctdepositoaccount['deposito_account_period']; $i++) {

				$depositoprofitsharing = array();
				$month = $month + 1;
				if ($month == 13) {
					$month = 01;
					$year = $year + 1;
				}

				$deposito_profit_sharing_due_date = '';
				$check_date = $year . '-' . $month . '-' . "01";
				$lastday = date("t", strtotime($check_date));
				if ($day > $lastday) {
					$deposito_profit_sharing_due_date = $year . '-' . $month . '-' . $lastday;
				} else {
					$deposito_profit_sharing_due_date = $year . '-' . $month . '-' . $day;
				}

				$depositoprofitsharing = array(
					'deposito_account_id'				=> $deposito_account_id,
					'branch_id'							=> $auth['branch_id'],
					'deposito_id'						=> $acctdepositoaccount['deposito_id'],
					'deposito_account_interest'			=> $acctdepositoaccount['deposito_account_interest'],
					'member_id'							=> $acctdepositoaccount['member_id'],
					'deposito_profit_sharing_due_date'	=> $deposito_profit_sharing_due_date,
					'deposito_daily_average_balance'	=> $acctdepositoaccount['deposito_account_amount'],
					'deposito_account_last_balance'		=> $acctdepositoaccount['deposito_account_amount'],
					'savings_account_id'				=> $acctdepositoaccount['savings_account_id'],
					'deposito_profit_sharing_token'		=> $token . $i,
				);

				$this->AcctDepositoAccount_model->insertAcctDepositoProfitSharing($depositoprofitsharing);
			}

			// $day 	= date('d', strtotime($acctdepositoaccount['deposito_account_date']));
			// $month 	= date('m', strtotime($acctdepositoaccount['deposito_account_date']));
			$year 	= date('Y', strtotime($acctdepositoaccount['deposito_account_date']));
			// $date 	= date('Y-m-d', strtotime($acctdepositoaccount['deposito_account_date']));

			if ($acctdepositoaccount['deposito_account_interest_period'] == 1) {
				for ($i = 1; $i <= $acctdepositoaccount['deposito_account_period']; $i++) {
					$data_week = date("Y-m-d", strtotime($i . " week", $date));


					$data_interest_period = array(
						'deposito_account_id'						=> $deposito_account_id,
						'branch_id'									=> $acctdepositoaccount['branch_id'],
						'deposito_id'								=> $acctdepositoaccount['deposito_id'],
						'savings_account_id'						=> $acctdepositoaccount['savings_account_id'],
						'office_id'									=> $acctdepositoaccount['office_id'],
						'commission_date'							=> $data_week,
						'commission_due_date'						=> $acctdepositoaccount['deposito_account_due_date'],
						'deposito_account_last_balance'				=> $acctdepositoaccount['deposito_account_amount'],
						'commission_on_hold_agent'					=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'commission_disbursed_agent' 				=> $acctdepositoaccount['deposito_account_commission_disbursed_agent'],
						'commission_on_hold_supervisor'				=> $acctdepositoaccount['deposito_account_commission_on_hold_supervisor'],
						'commission_disbursed_supervisor' 			=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
					);
					// print_r($data_interest_period); exit;

					$this->AcctDepositoAccount_model->insertAcctCommission($data_interest_period);
				}
			}
			if ($acctdepositoaccount['deposito_account_interest_period'] == 2) {
				for ($i = 1; $i <= $acctdepositoaccount['deposito_account_period']; $i++) {
					// $date = date("Y-m-d", strtotime(" +1 month", strtotime($date)));
					// $date 	= $this->endCycle($date, 1);
					$month = $month + 1;
					if ($month == 13) {
						$month = 01;
						$year = $year + 1;
					}

					$commission_date = '';
					$check_date = $year . '-' . $month . '-' . "01";
					$lastday = date("t", strtotime($check_date));
					if ($day > $lastday) {
						$commission_date = $year . '-' . $month . '-' . $lastday;
					} else {
						$commission_date = $year . '-' . $month . '-' . $day;
					}

					$data_interest_period = array(
						'deposito_account_id'						=> $deposito_account_id,
						'branch_id'									=> $acctdepositoaccount['branch_id'],
						'deposito_id'								=> $acctdepositoaccount['deposito_id'],
						'savings_account_id'						=> $acctdepositoaccount['savings_account_id'],
						'office_id'									=> $acctdepositoaccount['office_id'],
						'commission_date'							=> $commission_date,
						'commission_due_date'						=> $acctdepositoaccount['deposito_account_due_date'],
						'deposito_account_last_balance'				=> $acctdepositoaccount['deposito_account_amount'],
						'commission_on_hold_agent'					=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'commission_disbursed_agent' 				=> $acctdepositoaccount['deposito_account_commission_disbursed_agent'],
						'commission_on_hold_supervisor'				=> $acctdepositoaccount['deposito_account_commission_on_hold_supervisor'],
						'commission_disbursed_supervisor' 			=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
					);
					// print_r($data_interest_period); exit;

					$this->AcctDepositoAccount_model->insertAcctCommission($data_interest_period);
				}
			}
			if ($acctdepositoaccount['deposito_account_interest_period'] == 3) {
				for ($i = 0; $i <= $acctdepositoaccount['deposito_account_period']; $i++) {
					$data_year = date("Y-m-d", strtotime($i . " year", $date));

					// $year = $year + 1;

					// $commission_date = $year . '-' . $month . '-' . $day;

					$data_interest_period = array(
						'deposito_account_id'						=> $deposito_account_id,
						'branch_id'									=> $acctdepositoaccount['branch_id'],
						'deposito_id'								=> $acctdepositoaccount['deposito_id'],
						'savings_account_id'						=> $acctdepositoaccount['savings_account_id'],
						'office_id'									=> $acctdepositoaccount['office_id'],
						'commission_date'							=> $data_year,
						'commission_due_date'						=> $acctdepositoaccount['deposito_account_due_date'],
						'deposito_account_last_balance'				=> $acctdepositoaccount['deposito_account_amount'],
						'commission_on_hold_agent'					=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'commission_disbursed_agent' 				=> $acctdepositoaccount['deposito_account_commission_disbursed_agent'],
						'commission_on_hold_supervisor'				=> $acctdepositoaccount['deposito_account_commission_on_hold_supervisor'],
						'commission_disbursed_supervisor' 			=> $acctdepositoaccount['deposito_account_commission_on_hold_agent'],
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
					);
					// print_r($data_interest_period); exit;

					$this->AcctDepositoAccount_model->insertAcctCommission($data_interest_period);
				}
			}

			$deposito_point = $this->AcctDepositoAccount_model->getAcctDepositoPoint($acctdepositoaccount['deposito_id']);

			$transaction_module_code 	= "DEP";
			$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
			$journal_voucher_period 	= date("Ym", strtotime($acctdepositoaccount['deposito_account_date']));

			$data_journal = array(
				'branch_id'						=> $auth['branch_id'],
				'journal_voucher_period' 		=> $journal_voucher_period,
				'journal_voucher_date'			=> date('Y-m-d'),
				'journal_voucher_title'			=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount['member_name'],
				'journal_voucher_description'	=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount['member_name'],
				'journal_voucher_token'			=> $acctdepositoaccount['deposito_account_token'],
				'transaction_module_id'			=> $transaction_module_id,
				'transaction_module_code'		=> $transaction_module_code,
				'transaction_journal_id' 		=> $acctdepositoaccount['deposito_account_id'],
				'transaction_journal_no' 		=> $acctdepositoaccount['deposito_account_no'],
				'created_id' 					=> $acctdepositoaccount['created_id'],
				'created_on' 					=> date('Y-m-d H:i:s'),
			);

			$journal_voucher_token = $this->AcctDepositoAccount_model->getJournalVoucherToken($acctdepositoaccount['deposito_account_token']);

			if ($journal_voucher_token->num_rows() == 0) {
				$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

				$journal_voucher_id 		= $this->AcctDepositoAccount_model->getJournalVoucherID($acctdepositoaccount['created_id']);

				$preferencecompany 			= $this->AcctDepositoAccount_model->getPreferenceCompany();

				$account_id_default_status 	= $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

				$data_debet = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $preferencecompany['account_cash_id'],
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($acctdepositoaccount['deposito_account_amount'] - $acctdepositoaccount['deposito_account_cashback']),
					'journal_voucher_debit_amount'	=> ABS($acctdepositoaccount['deposito_account_amount'] - $acctdepositoaccount['deposito_account_cashback']),
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 0,
					'journal_voucher_item_token'	=> $acctdepositoaccount['deposito_account_token'] . $preferencecompany['account_cash_id'],
					'created_id' 					=> $auth['user_id'],
				);

				$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

				if ($journal_voucher_item_token->num_rows() == 0) {
					$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
				}

				$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount['deposito_id']);

				$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

				$journal_voucher_credit_amount = $acctdepositoaccount['deposito_account_amount'];

				$data_credit = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($acctdepositoaccount['deposito_account_amount']),
					'journal_voucher_credit_amount'	=> ABS($journal_voucher_credit_amount),
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 1,
					'journal_voucher_item_token'	=> $acctdepositoaccount['deposito_account_token'] . $account_id,
					'created_id' 					=> $auth['user_id'],
				);

				$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

				if ($journal_voucher_item_token->num_rows() == 0) {
					$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
				}
			}
			
			$account_id = $preferencecompany['account_cashback_id'];

			$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

			$journal_voucher_debit_amount = $acctdepositoaccount['deposito_account_cashback'];

			$data_debit = array(
				'journal_voucher_id'			=> $journal_voucher_id,
				'account_id'					=> $account_id,
				'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
				'journal_voucher_amount'		=> ABS($acctdepositoaccount['deposito_account_amount']),
				'journal_voucher_debit_amount'	=> ABS($journal_voucher_debit_amount),
				'account_id_default_status'		=> $account_id_default_status,
				'account_id_status'				=> 0,
				'journal_voucher_item_token'	=> $acctdepositoaccount['deposito_account_token'] . $account_id,
				'created_id' 					=> $auth['user_id'],
			);

			$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

			if ($journal_voucher_item_token->num_rows() == 0) {
				$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debit);
			}


			$data_point = array(
				'member_id' 	=> $acctdepositoaccount['member_id'],
				'point_date' 	=> date('Y-m-d'),
				'point_from' 	=> 'Buka Simpanan Berjangka',
				'point_amount' 	=> $deposito_point,
				'branch_id' 	=> $auth['branch_id'],
				'created_id' 	=> $auth['user_id'],
				'created_on' 	=> date('Y-m-d'),
			);
			$this->AcctDepositoAccount_model->insertSystemPoint($data_point);

			$msg = "<div class='alert alert-success alert-dismissable'>  
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
					Persetujuan Rekening Simpanan Berjangka Sukses
				</div>";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/print-validation/' . $deposito_account_id);
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Persetujuan Rekening Simpanan Berjangka Tidak Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account');
		}
	}
	
	public function rejectAcctDepositoAccount(){
		$auth 					= $this->session->userdata('auth');
		$deposito_account_id	= $this->input->post('deposito_account_id', true);

		$data = array(
			'deposito_account_id'  				=> $deposito_account_id,
			'deposito_account_reject_remark'  	=> $this->input->post('deposito_account_reject_remark', true),
			'validation'						=> 2,
			'validation_id'						=> $auth['user_id'],
			'validation_on'						=> date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('deposito_account_reject_remark', 'Keterangan', 'required');

		if ($this->form_validation->run() == true) {
			if ($this->AcctDepositoAccount_model->validationAcctDepositoAccount($data)) {
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Reject Rekening Simpanan Berjangka Sukses
							</div>";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Reject Rekening Simpanan Berjangka Tidak Berhasil
							</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			}
		} else {
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account');
		}
	}

	public function printValidationAcctDepositoAccount(){
		$deposito_account_id 	= $this->uri->segment(3);
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

		$pdf->SetFont('helveticaI', '', 7);

		// -----------------------------------------------------------------------------

		$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['deposito_account_no'] . "</div></td>
			        <td width=\"40%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['member_name'] . "</div></td>
			        <td width=\"5%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['office_id'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['validation_on'] . "</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">" . $this->AcctDepositoAccount_model->getUsername($acctdepositoaccount['validation_id']) . "</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; " . number_format($acctdepositoaccount['deposito_account_amount'], 2) . "</div></td>
			    </tr>
			</table>";

		$pdf->writeHTML($tbl, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Validasi.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	//-----------------------------------------------------------------------------------------------------------------------------//

	public function voidAcctDepositoAccount(){
		$data['main_view']['membergender']				= $this->configuration->MemberGender();
		$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($this->uri->segment(3));
		$data['main_view']['content']			= 'AcctDepositoAccount/FormVoidAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function processVoidAcctDepositoAccount(){
		$auth	= $this->session->userdata('auth');

		$newdata = array(
			"deposito_account_id"	=> $this->input->post('deposito_account_id', true),
			"voided_on"				=> date('Y-m-d H:i:s'),
			'data_state'			=> 2,
			"voided_remark" 		=> $this->input->post('voided_remark', true),
			"voided_id"				=> $auth['user_id']
		);

		$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

		if ($this->form_validation->run() == true) {
			if ($this->AcctDepositoAccount_model->voidAcctDepositoAccount($newdata)) {
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Berjangka Sukses
							</div>";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Berjangka Tidak Berhasil
							</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			}
		} else {
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account');
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------------//

	public function printCertificateDeposito(){
		$unique = $this->session->userdata('unique');

		$this->session->unset_userdata('acctdepositoaccounttoken-' . $unique['unique']);
		$this->session->unset_userdata('member_id');

		$data['main_view']['content']			= 'AcctDepositoAccount/ListAcctDepositoPrintCertificate_view';
		$this->load->view('MainPage_view', $data);
	}

	public function getListPrintAcctDepositoAccount(){
		$auth 	= $this->session->userdata('auth');


		$list = $this->AcctDepositoAccount_model->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $depositoaccount) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $depositoaccount->deposito_account_no;
			$row[] = $depositoaccount->member_name;
			$row[] = $depositoaccount->deposito_name;
			$row[] = $depositoaccount->deposito_account_serial_no;
			$row[] = tgltoview($depositoaccount->deposito_account_date);
			$row[] = tgltoview($depositoaccount->deposito_account_due_date);
			$row[] = number_format($depositoaccount->deposito_account_amount, 2);
			$row[] = $depositoaccount->deposito_account_nisbah;
			$row[] = '
	            	<a href="' . base_url() . 'deposito-account/print-certificate-front/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Cetak Depan Sertifikat</a>
	            	<br>

	            	<a href="' . base_url() . 'deposito-account/print-certificate-back/' . $depositoaccount->deposito_account_id . '" class="btn btn-xs green" role="button"><i class="fa fa-print"></i> Cetak Belakang Sertifikat</a>';

			$data[] = $row;
		}



		// print_r($list);exit;

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->AcctDepositoAccount_model->count_all(),
			"recordsFiltered" => $this->AcctDepositoAccount_model->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function printCertificateAcctDepositoAccountFront(){
		$deposito_account_id 	= $this->uri->segment(3);
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

		$pdf->SetFont('helveticaI', '', 7);

		// -----------------------------------------------------------------------------

		$tbl = "
			<table cellspacing=\"6\" cellpadding=\"1\" border=\"0\">
				<tr>
			        <td width=\"100%\" colspan=\"4\" height=\"135px\"></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"></td>
			        <td width=\"45%\"><div style=\"text-align: left; font-size:12px\">" . $acctdepositoaccount['member_name'] . "</div></td>
			        <td width=\"10%\"></td>
			        <td width=\"25%\"><div style=\"text-align: right; font-size:12px\">" . number_format($acctdepositoaccount['deposito_account_amount'], 2) . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"></td>
			        <td width=\"45%\"><div style=\"text-align: left; font-size:12px\">" . $acctdepositoaccount['member_address'] . "</div></td>
			        <td width=\"10%\"></td>
			        <td width=\"25%\" rowspan =\"2\"><div style=\"text-align: left; font-size:11px\">" . numtotxt($acctdepositoaccount['deposito_account_amount']) . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"></td>
			        <td width=\"45%\"><div style=\"text-align: left; font-size:12px\">" . $acctdepositoaccount['deposito_account_no'] . "</div></td>
			        <td width=\"10%\"></td>
			    </tr>
			</table>
			<br><br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center; font-size:12px\">" . tgltoview($acctdepositoaccount['deposito_account_date']) . "</div></td>
			        <td width=\"25%\"><div style=\"text-align: center; font-size:12px\">" . tgltoview($acctdepositoaccount['deposito_account_due_date']) . "</div></td>
			        <td width=\"30%\"><div style=\"text-align: center; font-size:12px\">" . $acctdepositoaccount['deposito_account_period'] . "</div></td>
			        <td width=\"25%\"><div style=\"text-align: center; font-size:12px\">" . $acctdepositoaccount['deposito_interest_rate'] . "</div></td>
			    </tr>
			</table>
			<br><br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"28%\"><div style=\"text-align: center; font-size:12px\">" . $acctdepositoaccount['deposito_account_serial_no'] . "</div></td>
			    </tr>
			</table>";


		$pdf->writeHTML($tbl, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Cetak_Sertifikat_Bg_Depan.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	public function printCertificateAcctDepositoAccountBack(){
		$deposito_account_id 	= $this->uri->segment(3);
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

		$pdf->SetFont('helveticaI', '', 7);

		// -----------------------------------------------------------------------------

		$tbl = "
			<table cellspacing=\"6\" cellpadding=\"1\" border=\"0\">
				<tr>
			        <td width=\"100%\" colspan=\"4\" height=\"50px\"></td>
			    </tr>
			    <tr>
			        <td width=\"10%\"></td>
			        <td width=\"20%\"><div style=\"text-align: left; font-size:12px\">" . $acctdepositoaccount['member_no'] . "</div></td>
			        <td width=\"10%\"></td>
			        <td width=\"45%\"><div style=\"text-align: left; font-size:12px\">" . $acctdepositoaccount['member_name'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"100%\" colspan=\"4\"></td>
			    </tr>
			    <tr>
			        <td width=\"10%\"></td>
			        <td width=\"55%\"></td>
			        <td width=\"10%\"></td>
			        <td width=\"25%\" rowspan =\"2\"><div style=\"text-align: left; font-size:11px\">" . number_format($acctdepositoaccount['deposito_account_amount'], 2) . "</div></td>
			    </tr>
			</table>";


		$pdf->writeHTML($tbl, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Cetak_Sertifikat_Bg_Belakang.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	public function printCertificateAcctDepositoAccountBilyet(){
		$deposito_account_id 	= $this->uri->segment(3);
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document

		$pdf = new TCPDF('L', 'mm', array(140, 200), true, 'UTF-8', false);
		// $pdf = new TCPDF('P', 'mm', array(200, 140), true, 'UTF-8', false);
		// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

		// set default header data
		/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

		// set header and footer fonts
		/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

		// set default monospaced font
		/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

		// set margins
		$pdf->SetMargins(25, 52.5, 25, true);
		// set margins
		/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		// $pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
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
		$pdf->SetFont('helvetica', '', 20);

		// add a page
		$pdf->AddPage();
		/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

		$pdf->SetFont('helvetica', '', 7);

		// -----------------------------------------------------------------------------

		$tbl = "
		<table cellspacing=\"0.5\" cellpadding=\"3\" border=\"0\" >
			<tr width=\"15cm\">
				<td height=\"0.7cm\"><div style=\"text-align: center; font-size:17px;\"><b>Rp." . number_format($acctdepositoaccount['deposito_account_amount'], 2) . " (" . numtotxt($acctdepositoaccount['deposito_account_amount']) . ")</b></div></td>
			</tr>
			<tr width=\"15cm\">
				<td  height=\"0.5cm\"><div></div></td>
			</tr>
			<tr width=\"15cm\">
				<td height=\"0.7cm\" width=\"2.8cm\" ><div style=\"vertical-align: bottom; text-align: left; font-size:13px;\">Nomor Bilyet</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_account_no'] . "</b></div></td>
				
				<td height=\"0.7cm\" width=\"0.5cm\" style=\"margin-top: 0.2cm;\"><div style=\"text-align: left; font-size:13px;\"></div></td>

				<td height=\"0.7cm\" width=\"2.8cm\" align=\"center\"><div style=\"align: center; text-align: left; font-size:13px;\">Nomor Anggota</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['member_no'] . "</b></div></td>
			</tr>
			<tr width=\"15cm\">
				<td height=\"0.7cm\" width=\"2.8cm\" ><div style=\"text-align: left; font-size:13px;\">Tanggal Buka</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_account_date'] . "</b></div></td>
				
				<td height=\"0.7cm\" width=\"0.5cm\" style=\"margin-top: 0.2cm;\"><div style=\"text-align: left; font-size:13px;\"></div></td>

				<td height=\"0.7cm\" width=\"2.8cm\" align=\"center\"><div style=\"align: center; text-align: left; font-size:13px;\">Nama Anggota</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['member_name'] . "</b></div></td>
			</tr>
			<tr  width=\"15cm\">
				<td height=\"0.7cm\" width=\"2.8cm\" ><div style=\"text-align: left; font-size:13px;\">Tanggal Jatuh Tempo</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_account_due_date'] . "</b></div></td>
				
				<td height=\"0.7cm\" width=\"0.5cm\" style=\"margin-top: 0.2cm;\"><div style=\"text-align: left; font-size:13px;\"></div></td>

				<td height=\"0.7cm\" width=\"2.8cm\" align=\"center\"><div style=\"align: center; text-align: left; font-size:13px;\">Jangka Waktu</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_account_period'] . "</b></div></td>
			</tr>
			<tr width=\"15cm\">
				<td height=\"0.7cm\" width=\"2.8cm\" ><div style=\"text-align: left; font-size:13px;\">Nama Produk</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_name'] . "</b></div></td>
				
				<td height=\"0.7cm\" width=\"0.5cm\" style=\"margin-top: 0.2cm;\"><div style=\"text-align: left; font-size:13px;\"></div></td>

				<td height=\"0.7cm\" width=\"2.8cm\" align=\"center\"><div style=\"align: center; text-align: left; font-size:13px;\">Kondisi</div></td>
				<td height=\"0.7cm\" width=\"0.5cm\"><div style=\"text-align: centert; font-size:13px;\">:</div></td>
				<td height=\"0.7cm\" width=\"3.89cm\"><div style=\"text-align: left; font-size:13px;\"><b>" . $acctdepositoaccount['deposito_account_condition'] . "</b></div></td>
			</tr>
		</table> 
		";


		$pdf->writeHTML($tbl, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Cetak_Sertifikat_Bg_Belakang.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

	//-----------------------------------------------------------------------------------------------------------------------------//


	public function getAcctDepositoAccountDueDate(){
		$sesi 	= $this->session->userdata('filter-acctdepositoaccountduedate');
		$auth 	= $this->session->userdata('auth');
		if (!is_array($sesi)) {
			// $sesi['start_date']		= date('Y-m-d');
			// $sesi['end_date']		= date('Y-m-d');
			$sesi['deposito_id']		= '';
			if ($auth['branch_status'] == 0) {
				$sesi['branch_id']		= $auth['branch_id'];
			} else {
				$sesi['branch_id']		= '';
			}
		}

		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getAcctDepositoAccountDueDate($sesi['deposito_id'], $sesi['branch_id']);
		$data['main_view']['corebranch']				= create_double($this->AcctDepositoAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
		$data['main_view']['acctdeposito']				= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['content']					= 'AcctDepositoAccount/ListAcctDepositoAccountExtra_view';
		$this->load->view('MainPage_view', $data);
	}

	public function filterAcctDepositoAccountDueDate(){
		$data = array(
			// "start_date"	=> tgltodb($this->input->post('start_date',true)),
			// "end_date"		=> tgltodb($this->input->post('end_date',true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-acctdepositoaccountduedate', $data);
		redirect('deposito-account/get-due-date');
	}

	public function reset_search_duedate(){
		$this->session->unset_userdata('filter-acctdepositoaccountduedate');
		redirect('deposito-account/get-due-date');
	}

	public function addAcctDepositoAccountExtra(){
		$data['main_view']['membergender']				= $this->configuration->MemberGender();
		$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
		$data['main_view']['token']						= md5(date('Y-m-d H:i:s'));
		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($this->uri->segment(3));
		$data['main_view']['content']					= 'AcctDepositoAccount/FormAddAcctDepositoAccountExtra_view';
		$this->load->view('MainPage_view', $data);
	}

	public function processAddAcctDepositoAccountExtra(){
		$auth = $this->session->userdata('auth');
		$amount_administration =  $this->input->post('deposito_account_amount_adm', true);
		$data = array(
			'deposito_account_id'					=> $this->input->post('deposito_account_id', true),
			'deposito_id'							=> $this->input->post('deposito_id', true),
			'member_id'								=> $this->input->post('member_id', true),
			'branch_id'								=> $auth['branch_id'],
			'deposito_account_extra_period'			=> $this->input->post('deposito_account_extra_period', true),
			'deposito_account_extra_date'			=> tgltodb($this->input->post('deposito_account_extra_date', true)),
			'deposito_account_extra_due_date'		=> tgltodb($this->input->post('deposito_account_extra_due_date', true)),
			'deposito_account_extra_token'			=> $this->input->post('deposito_account_extra_token', true),
			'created_id'							=> $auth['user_id'],
			'created_on'							=> date('Y-m-d H:i:s'),
		);

		$data_update = array(
			'deposito_account_period'		=> $this->input->post('deposito_account_period', true) + $this->input->post('deposito_account_extra_period', true),
			'deposito_account_due_date'		=> tgltodb($this->input->post('deposito_account_extra_due_date', true)),
		);

		$data_deposito = array(
			'deposito_account_due_date'		=> tgltodb($this->input->post('deposito_account_due_date', true)),
			'deposito_account_nisbah'		=> $this->input->post('deposito_account_nisbah', true),
			'deposito_account_amount'		=> $this->input->post('deposito_account_amount', true),
			'savings_account_id'			=> $this->input->post('savings_account_id', true),
		);

		$this->form_validation->set_rules('deposito_account_extra_period', 'Jangka Waktu', 'required');

		if ($this->form_validation->run() == true) {
			$deposito_account_extra_token 			= $this->AcctDepositoAccount_model->getDepositoAccountExtraToken($data['deposito_account_extra_token']);

			if ($deposito_account_extra_token->num_rows() == 0) {
				if ($this->AcctDepositoAccount_model->insertAcctDepositoAccountExtra($data, $data_update)) {

					$date 	= date('d', strtotime($data_deposito['deposito_account_due_date']));
					$month 	= date('m', strtotime($data_deposito['deposito_account_due_date']));
					$year 	= date('Y', strtotime($data_deposito['deposito_account_due_date']));


					// for ($i = 1; $i <= $data['deposito_account_extra_period']; $i++) {
					// 	$depositoprofitsharing = array();

					// 	$month = $month + 1;

					// 	if ($month == 13) {
					// 		$month = 01;
					// 		$year = $year + 1;
					// 	}

					// 	$deposito_profit_sharing_due_date = $year . '-' . $month . '-' . $date;

					// 	$depositoprofitsharing = array(
					// 		'deposito_account_id'				=> $data['deposito_account_id'],
					// 		'branch_id'							=> $auth['branch_id'],
					// 		'deposito_id'						=> $data['deposito_id'],
					// 		'deposito_account_nisbah'			=> $data_deposito['deposito_account_nisbah'],
					// 		'member_id'							=> $data['member_id'],
					// 		'deposito_profit_sharing_due_date'	=> $deposito_profit_sharing_due_date,
					// 		'deposito_daily_average_balance'	=> $data_deposito['deposito_account_amount'],
					// 		'deposito_account_last_balance'		=> $data_deposito['deposito_account_amount'],
					// 		'savings_account_id'				=> $data_deposito['savings_account_id'],
					// 		'deposito_profit_sharing_token'		=> 'PST' . $data['deposito_account_extra_token'],
					// 	);

					// 	$depositoprofitsharing_data = $this->AcctDepositoAccount_model->getAcctDepositoProfitSharing($depositoprofitsharing);

					// 	if ($depositoprofitsharing_data->num_rows() == 0) {
					// 		$this->AcctDepositoAccount_model->insertAcctDepositoProfitSharing($depositoprofitsharing);
					// 	}
					// }

					if ($amount_administration > 0) {
						$transaction_module_code = "PDEP";

						$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
						$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($data['deposito_account_id']);

						$journal_voucher_period = date("Ym", strtotime($data['deposito_account_extra_due_date']));

						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'PERPANJANGAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'journal_voucher_description'	=> 'PERPANJANGAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
							'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
							'journal_voucher_token' 		=> 'ED' . $data['deposito_account_extra_token'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data_journal['created_id']);

						$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

						$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);

						$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);



						$data_debet = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $amount_administration,
							'journal_voucher_debit_amount'	=> $amount_administration,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> 'STR1' . $data['deposito_account_extra_token'] . $amount_administration,
							'created_id' 					=> $auth['user_id']
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);

						$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

						$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_mutation_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $amount_administration,
							'journal_voucher_credit_amount'	=> $amount_administration,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> 'STR2' . $data['deposito_account_extra_token'] . $preferencecompany['account_mutation_adm_id'],
							'created_id' 					=> $auth['user_id']
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
					}
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perpanjangan Simpanan Berjangka Sukses
								</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
					$this->session->set_userdata('message', $msg);
					redirect('deposito-account/get-due-date');
				} else {
					$this->session->set_userdata('addacctdepositoaccount', $data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perpanjangan Simpanan Berjangka Tidak Berhasil
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('deposito-account/add-extra/' . $data['deposito_account_id']);
				}
			} else {
				$date 	= date('d', strtotime($data_deposito['deposito_account_due_date']));
				$month 	= date('m', strtotime($data_deposito['deposito_account_due_date']));
				$year 	= date('Y', strtotime($data_deposito['deposito_account_due_date']));

				//-------------------------------DEPOSITO_PROFIT_SHARING---------------------------------------//

				// for ($i = 1; $i <= $data['deposito_account_extra_period']; $i++) {
				// 	$depositoprofitsharing = array();

				// 	$month = $month + 1;

				// 	if ($month == 13) {
				// 		$month = 01;
				// 		$year = $year + 1;
				// 	}

				// 	$deposito_profit_sharing_due_date = $year . '-' . $month . '-' . $date;

				// 	$depositoprofitsharing = array(
				// 		'deposito_account_id'				=> $data['deposito_account_id'],
				// 		'branch_id'							=> $auth['branch_id'],
				// 		'deposito_id'						=> $data['deposito_id'],
				// 		'deposito_account_nisbah'			=> $data_deposito['deposito_account_nisbah'],
				// 		'member_id'							=> $data['member_id'],
				// 		'deposito_profit_sharing_due_date'	=> $deposito_profit_sharing_due_date,
				// 		'deposito_daily_average_balance'	=> $data_deposito['deposito_account_amount'],
				// 		'deposito_account_last_balance'		=> $data_deposito['deposito_account_amount'],
				// 		'savings_account_id'				=> $data_deposito['savings_account_id'],
				// 		'deposito_profit_sharing_token'		=> 'PST' . $data['deposito_account_extra_token'],
				// 	);
				// 	$deposito_profit_sharing_token = $this->AcctDepositoAccount_model->getAcctDepositoProfitSharingToken($depositoprofitsharing['deposito_profit_sharing_token']);
				// 	if ($deposito_profit_sharing_token->num_rows() == 0) {
				// 		$depositoprofitsharing_data = $this->AcctDepositoAccount_model->getAcctDepositoProfitSharing($depositoprofitsharing);

				// 		if ($depositoprofitsharing_data->num_rows() == 0) {
				// 			$this->AcctDepositoAccount_model->insertAcctDepositoProfitSharing($depositoprofitsharing);
				// 		}
				// 	}
				// }
				$token = 'ED' . $data['deposito_account_extra_token'];
				$journal_voucher_token = $this->AcctDepositoAccount_model->getAcctJournalVoucherToken($token);
				if ($journal_voucher_token->num_rows() == 0) {
					if ($amount_administration > 0) {
						$transaction_module_code = "PDEP";

						$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
						$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($data['deposito_account_id']);

						$journal_voucher_period = date("Ym", strtotime($data['deposito_account_extra_due_date']));

						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'PERPANJANGAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'journal_voucher_description'	=> 'PERPANJANGAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
							'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
							'journal_voucher_token' 		=> 'ED' . $data['deposito_account_extra_token'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

						$token = 'STR1' . $data['deposito_account_extra_token'] . $amount_administration;
						$journal_voucher_item_token = $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($token);
						if ($journal_voucher_item_token->num_rows() == 0) {
							$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data_journal['created_id']);
							$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();
							$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);
							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							$data_debet = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $amount_administration,
								'journal_voucher_debit_amount'	=> $amount_administration,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> 'STR1' . $data['deposito_account_extra_token'] . $amount_administration,
								'created_id' 					=> $auth['user_id']
							);


							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
						}
						$token = 'STR2' . $data['deposito_account_extra_token'] . $amount_administration;
						$journal_voucher_item_token = $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($token);
						if ($journal_voucher_item_token->num_rows() == 0) {
							$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

							$data_credit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_mutation_adm_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $amount_administration,
								'journal_voucher_credit_amount'	=> $amount_administration,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> 'STR2' . $data['deposito_account_extra_token'] . $preferencecompany['account_mutation_adm_id'],
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}
				}

				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Perpanjangan Simpanan Berjangka Sukses
							</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account/get-due-date');
			}
		} else {
			$this->session->set_userdata('addacctdepositoaccount', $data);
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/add-extra/' . $data['deposito_account_id']);
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------------//

	public function getClosedAcctDepositoAccount(){
		$sesi = $this->session->userdata('filter-closedacctdepositoaccount');
		$auth 	= $this->session->userdata('auth');
		if (!is_array($sesi)) {
			// $sesi['start_date']		= date('Y-m-d');
			// $sesi['end_date']		= date('Y-m-d');
			$sesi['deposito_id']		= '';
			if ($auth['branch_status'] == 0) {
				$sesi['branch_id']		= $auth['branch_id'];
			} else {
				$sesi['branch_id']		= '';
			}
		}

		$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getClosedAcctDepositoAccount($sesi['deposito_id'], $sesi['branch_id']);
		$data['main_view']['acctdeposito']				= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['corebranch']				= create_double($this->AcctDepositoAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
		$data['main_view']['content']					= 'AcctDepositoAccount/ListAcctDepositoAccountClosed_view';
		$this->load->view('MainPage_view', $data);
	}

	public function filterClosedAcctDepositoAccount(){
		$data = array(
			// "start_date"	=> tgltodb($this->input->post('start_date',true)),
			// "end_date"		=> tgltodb($this->input->post('end_date',true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-closedacctdepositoaccount', $data);
		redirect('deposito-account/get-closed');
	}

	public function reset_search_closed(){
		$this->session->unset_userdata('filter-closedacctdepositoaccount');
		redirect('deposito-account/get-closed');
	}

	public function getAcctSavingsAccountList(){
		$deposito_account_id = $this->uri->segment(3);
		$auth = $this->session->userdata('auth');
		$branch_id = '';
		$list = $this->AcctSavingsAccount_model->get_datatables($branch_id);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $savingsaccount) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $savingsaccount->savings_account_no;
			$row[] = $savingsaccount->member_name;
			$row[] = $savingsaccount->member_address;
			$row[] = '<a href="' . base_url() . 'deposito-account/add-closed/' . $deposito_account_id . '/' . $savingsaccount->savings_account_id . '" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
			$data[] = $row;
		}

		// print_r($list);exit;

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->AcctSavingsAccount_model->count_all($branch_id),
			"recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($branch_id),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function addClosedAcctDepositoAccount(){
		$unique 				= $this->session->userdata('unique');
		$token 					= $this->session->userdata('acctdepositoaccounttoken-' . $unique['unique']);

		$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

		if (empty($token)) {
			$token = md5(date('Y-m-d H:i:s'));
			$this->session->set_userdata('acctdepositoaccounttoken-' . $unique['unique'], $token);
		}

		$acctdepositoaccount 		   = $this->AcctDepositoAccount_model->getAcctDepositoAccountDetail($this->uri->segment(3));

		$interest_total		 		   = $acctdepositoaccount['deposito_account_nisbah'];
		if ($interest_total > $preferencecompany['tax_minimum_amount']) {
			$tax_total	= $interest_total * $preferencecompany['tax_percentage'] / 100;
		} else {
			$tax_total 	= 0;
		}

		$lastprofitsharing = $this->AcctDepositoAccount_model->getAcctDepositoProfitSharingLast($this->uri->segment(3));

		if ($lastprofitsharing['deposito_profit_sharing_due_date'] < $acctdepositoaccount['deposito_account_due_date']) {
			$last_profit_date	= new DateTime($lastprofitsharing['deposito_profit_sharing_due_date']);
			$deposito_due_date 	= new DateTime($acctdepositoaccount['deposito_account_due_date']);
			$datediff  			= $deposito_due_date->diff($last_profit_date)->format('%a');

			if ($acctdepositoaccount['deposito_interest_period'] == 1) {
				$interest_amount = $datediff / 7 * $acctdepositoaccount['deposito_interest_rate'] * $acctdepositoaccount['deposito_account_amount'] / 100;
			} else if ($acctdepositoaccount['deposito_interest_period'] == 2) {
				$interest_amount = $datediff / date('t') * $acctdepositoaccount['deposito_interest_rate'] * $acctdepositoaccount['deposito_account_amount'] / 100;
			}
			if ($acctdepositoaccount['deposito_interest_period'] == 3) {
				$interest_amount = $datediff / 365 * $acctdepositoaccount['deposito_interest_rate'] * $acctdepositoaccount['deposito_account_amount'] / 100;
			}

			if ($interest_amount > $preferencecompany['tax_minimum_amount']) {
				$tax_amount	= $interest_amount * $preferencecompany['tax_percentage'] / 100;
			} else {
				$tax_amount 	= 0;
			}
		} else {
			$interest_amount 	= 0;
			$tax_amount 		= 0;
		}

		$data['main_view']['lastprofitsharing']			= $lastprofitsharing;
		$data['main_view']['membergender']				= $this->configuration->MemberGender();
		$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($this->uri->segment(3));
		$data['main_view']['acctsavingsaccount']		= $this->AcctDepositoAccount_model->getAcctSavingsAccount_Detail($this->uri->segment(4));
		$data['main_view']['interest_total']			= $interest_total;
		$data['main_view']['tax_total']					= $tax_total;
		$data['main_view']['interest_amount']			= $interest_amount;
		$data['main_view']['tax_amount']				= $tax_amount;
		$data['main_view']['content']					= 'AcctDepositoAccount/FormClosedAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function processClosedAcctDepositoAccount(){
		$auth = $this->session->userdata('auth');
		$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

		$data = array(
			'deposito_account_id'				=> $this->input->post('deposito_account_id', true),
			'deposito_account_penalty'			=> $this->input->post('deposito_account_penalty', true),
			'deposito_account_closed_date'		=> date('Y-m-d'),
			'deposito_account_status'			=> 1,
			'savings_account_id'				=> $this->input->post('savings_account_id', true),
			'deposito_account_closed_token'		=> $this->input->post('deposito_account_closed_token', true),
		);

		$amount_administration =  $this->input->post('deposito_account_amount_adm', true);

		$data_savings = array(
			'savings_id'						=> $this->input->post('savings_id', true),
			'member_id'							=> $this->input->post('member_id_savings', true),
			'savings_account_opening_balance'	=> $this->input->post('savings_account_last_balance', true),
			'savings_account_last_balance'		=> $this->input->post('savings_account_last_balance', true) + $this->input->post('deposito_account_amount', true),
		);

		$acctdepositoaccount 		   = $this->AcctDepositoAccount_model->getAcctDepositoAccountDetail($data['deposito_account_id']);
		$interest_amount		 	   = $this->input->post('interest_amount', true);
		$tax_amount					   = $this->input->post('tax_amount', true);
		$total_amount				   = $this->input->post('deposito_account_amount', true);

		$deposito_account_closed_token = $this->AcctDepositoAccount_model->getDepositoAccountClosedToken($data['deposito_account_closed_token']);

		if ($deposito_account_closed_token->num_rows() == 0) {
			if ($this->AcctDepositoAccount_model->closedAcctDepositoAccountExtra($data)) {
				$data_transfer = array(
					'branch_id'							=> $auth['branch_id'],
					'savings_transfer_mutation_date'	=> date('Y-m-d'),
					'savings_transfer_mutation_amount'	=> $total_amount + $interest_amount,
					'operated_name'						=> 'SYS',
					'savings_transfer_mutation_token'	=> $data['deposito_account_closed_token'],
					'created_id'						=> $auth['user_id'],
					'created_on'						=> date('Y-m-d H:i:s'),
				);

				if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)) {
					$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);

					$data_transfer_to = array(
						'savings_transfer_mutation_id'			=> $savings_transfer_mutation_id,
						'savings_account_id'					=> $data['savings_account_id'],
						'savings_id'							=> $data_savings['savings_id'],
						'member_id'								=> $data_savings['member_id'],
						'branch_id'								=> $auth['branch_id'],
						'mutation_id'							=> 10,
						'savings_account_opening_balance'		=> $data_savings['savings_account_opening_balance'],
						'savings_transfer_mutation_to_amount'	=> $total_amount + $interest_amount,
						'savings_account_last_balance'			=> $data_savings['savings_account_last_balance'],
						'savings_transfer_mutation_to_token'	=> $data['deposito_account_closed_token'] . $savings_transfer_mutation_id,
					);

					if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)) {
						$transaction_module_code = "PDEP";

						$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
						$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($data['deposito_account_id']);


						$journal_voucher_period = date("Ym", strtotime($data['deposito_account_closed_date']));

						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'PENUTUPAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'journal_voucher_description'	=> 'PENUTUPAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
							'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
							'journal_voucher_token' 		=> $data['deposito_account_closed_token'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data_journal['created_id']);

						$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

						$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);

						$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

						//Simpanan Berjangka
						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
							'journal_voucher_amount'		=> ABS($total_amount),
							'journal_voucher_debit_amount'	=> ABS($total_amount),
							'journal_voucher_item_token' 	=> $data['deposito_account_closed_token'] . $account_id,
							'account_id_status'				=> 0,
							'created_id' 					=> $auth['user_id'],
						);

						$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debit);

						$account_id = $this->AcctDepositoAccount_model->getAccountSavingsID($data_savings['savings_id']);

						$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

						//Masuk ke Rekening Tabungan
						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
							'journal_voucher_amount'		=> ABS($total_amount),
							'journal_voucher_credit_amount'	=> ABS($total_amount),
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token' 	=> $data['deposito_account_closed_token'] . $account_id,
							'account_id_status'				=> 1,
							'created_id' 					=> $auth['user_id'],
						);
						$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);

						if ($tax_amount > 0) {

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_savings_tax_id']);

							$data_debet = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_savings_tax_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $tax_amount,
								'journal_voucher_debit_amount'	=> $tax_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> 'PJ1' . $data['deposito_account_closed_token'] . $tax_amount,
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							$data_credit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $tax_amount,
								'journal_voucher_credit_amount'	=> $tax_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> 'PJ2' . $data['deposito_account_closed_token'] . $account_id,
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if ($interest_amount > 0) {
							$account_basil_id 	= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($acctdepositoaccount_last['deposito_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);

							$data_debet = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_basil_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $interest_amount,
								'journal_voucher_debit_amount'	=> $interest_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> 'PJ1' . $data['deposito_account_closed_token'] . $interest_amount,
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);

							$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);

							$data_credit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $interest_amount,
								'journal_voucher_credit_amount'	=> $interest_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> 'PJ2' . $data['deposito_account_closed_token'] . $account_id,
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
						}

						if ($amount_administration > 0) {
							$data_debet = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $amount_administration,
								'journal_voucher_debit_amount'	=> $amount_administration,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> 'STR1' . $data['deposito_account_closed_token'] . $amount_administration,
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);

							$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

							$data_credit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_mutation_adm_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $amount_administration,
								'journal_voucher_credit_amount'	=> $amount_administration,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> 'STR2' . $data['deposito_account_closed_token'] . $preferencecompany['account_mutation_adm_id'],
								'created_id' 					=> $auth['user_id']
							);

							$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}
				}
				$auth = $this->session->userdata('auth');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Penutupan Simpanan Berjangka Sukses
						</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
				$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account/print-validation-closed/' . $data['deposito_account_id']);
			} else {
				$this->session->set_userdata('addacctdepositoaccount', $data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Penutupan Simpanan Berjangka Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account/get-closed');
			}
		} else {
			$data_transfer = array(
				'branch_id'							=> $auth['branch_id'],
				'savings_transfer_mutation_date'	=> date('Y-m-d'),
				'savings_transfer_mutation_amount'	=> $total_amount + $interest_amount,
				'operated_name'						=> 'SYS',
				'savings_transfer_mutation_token'	=> $data['deposito_account_closed_token'],
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d H:i:s'),
			);

			$savings_transfer_mutation_token 		= $this->AcctDepositoAccount_model->getAcctSavingsTransferMutationToken($data_transfer['savings_transfer_mutation_token']);

			if ($savings_transfer_mutation_token->num_rows() == 0) {
				if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)) {
					$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);

					$data_transfer_to = array(
						'savings_transfer_mutation_id'			=> $savings_transfer_mutation_id,
						'savings_account_id'					=> $data['savings_account_id'],
						'savings_id'							=> $data_savings['savings_id'],
						'member_id'								=> $data_savings['member_id'],
						'branch_id'								=> $auth['branch_id'],
						'mutation_id'							=> 10,
						'savings_account_opening_balance'		=> $data_savings['savings_account_opening_balance'],
						'savings_transfer_mutation_to_amount'	=> $total_amount + $interest_amount,
						'savings_account_last_balance'			=> $data_savings['savings_account_last_balance'],
						'savings_transfer_mutation_to_token'	=> $data['deposito_account_closed_token'] . $savings_transfer_mutation_id,
					);

					$savings_transfer_mutation_to_token 			= $this->AcctDepositoAccount_model->getAcctSavingsTransferMutationToToken($data_transfer_to['savings_transfer_mutation_to_token']);

					if ($savings_transfer_mutation_to_token->num_rows() == 0) {
						if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)) {
							$transaction_module_code = "PDEP";

							$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
							$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($data['deposito_account_id']);


							$journal_voucher_period = date("Ym", strtotime($data['deposito_account_closed_date']));

							$data_journal = array(
								'branch_id'						=> $auth['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'PENUTUPAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
								'journal_voucher_description'	=> 'PENUTUPAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
								'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
								'journal_voucher_token' 		=> $data['deposito_account_closed_token'],
								'created_id' 					=> $auth['user_id'],
								'created_on' 					=> date('Y-m-d H:i:s'),
							);


							$journal_voucher_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherToken($data_journal['journal_voucher_token']);

							if ($journal_voucher_token->num_rows() == 0) {
								$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);
							}

							$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data_journal['created_id']);

							$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

							$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							//Simpanan Berjangka
							$data_debit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount),
								'journal_voucher_debit_amount'	=> ABS($total_amount),
								'journal_voucher_item_token' 	=> $data['deposito_account_closed_token'] . $account_id,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
							);


							$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if ($journal_voucher_item_token->num_rows() == 0) {
								$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debit);
							}

							$account_id = $this->AcctDepositoAccount_model->getAccountSavingsID($data_savings['savings_id']);

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							//Masuk ke Rekening Tabungan
							$data_credit = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount),
								'journal_voucher_credit_amount'	=> ABS($total_amount),
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token' 	=> $data['deposito_account_closed_token'] . $account_id,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
							);

							$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if ($journal_voucher_item_token->num_rows() == 0) {
								$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
							}

							if ($tax_amount > 0) {

								$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_savings_tax_id']);

								$data_debet = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_savings_tax_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $tax_amount,
									'journal_voucher_debit_amount'	=> $tax_amount,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> 'PJ1' . $data['deposito_account_closed_token'] . $tax_amount,
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
								}

								$account_id = $this->AcctDepositoAccount_model->getAccountID($acctdepositoaccount_last['deposito_id']);

								$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

								$data_credit = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $tax_amount,
									'journal_voucher_credit_amount'	=> $tax_amount,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> 'PJ2' . $data['deposito_account_closed_token'] . $account_id,
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
								}
							}

							if ($interest_amount > 0) {
								$account_basil_id 	= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($acctdepositoaccount_last['deposito_id']);

								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);

								$data_debet = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_basil_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $interest_amount,
									'journal_voucher_debit_amount'	=> $interest_amount,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> 'ITR1' . $data['deposito_account_closed_token'] . $interest_amount,
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
								}

								$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);

								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);

								$data_credit = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $interest_amount,
									'journal_voucher_credit_amount'	=> $interest_amount,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> 'ITR2' . $data['deposito_account_closed_token'] . $account_id,
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
								}
							}

							if ($amount_administration > 0) {
								$data_debet = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $amount_administration,
									'journal_voucher_debit_amount'	=> $amount_administration,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> 'STR1' . $data['deposito_account_closed_token'] . $amount_administration,
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
								}

								$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

								$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_mutation_adm_id']);

								$data_credit = array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_mutation_adm_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $amount_administration,
									'journal_voucher_credit_amount'	=> $amount_administration,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> 'STR2' . $data['deposito_account_closed_token'] . $preferencecompany['account_mutation_adm_id'],
									'created_id' 					=> $auth['user_id']
								);

								$journal_voucher_item_token 	= $this->AcctDepositoAccount_model->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if ($journal_voucher_item_token->num_rows() == 0) {
									$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}
					}
				}
			}
			$auth = $this->session->userdata('auth');
			$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Penutupan Simpanan Berjangka Sukses
					</div> ";
			$sesi = $this->session->userdata('unique');
			$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
			$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/print-validation-closed/' . $data['deposito_account_id']);
		}
	}

	public function printValidationClosedAcctDepositoAccount(){
		$deposito_account_id 	= $this->uri->segment(3);
		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);


		require_once('tcpdf/config/tcpdf_config.php');
		require_once('tcpdf/tcpdf.php');
		// create new PDF document
		$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

		// set document information
		/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

		$pdf->SetFont('helveticaI', '', 7);

		// -----------------------------------------------------------------------------

		$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['deposito_account_no'] . "</div></td>
			        <td width=\"40%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['member_name'] . "</div></td>
			        <td width=\"5%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['office_id'] . "</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">" . $acctdepositoaccount['validation_on'] . "</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">" . $this->AcctDepositoAccount_model->getUsername($acctdepositoaccount['validation_id']) . "</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; " . number_format($acctdepositoaccount['deposito_account_amount'], 2) . "</div></td>
			    </tr>
			</table>";

		$pdf->writeHTML($tbl, true, false, false, false, '');
		if (ob_get_length() > 0) {
			ob_clean();
		}
		// -----------------------------------------------------------------------------

		//Close and output PDF document
		$filename = 'Validasi.pdf';
		$pdf->Output($filename, 'I');

		//============================================================+
		// END OF FILE
		//============================================================+
	}

	//-----------------------------------------------------------------------------------------------------------------------------//

	public function listAcctDepositoAccount(){
		$sesi	= $this->session->userdata('filter-listacctdepositoaccount');
		$auth 	= $this->session->userdata('auth');
		if (!is_array($sesi)) {
			// $sesi['start_date']		= date('Y-m-d');
			// $sesi['end_date']		= date('Y-m-d');
			$sesi['deposito_id']		= '';
			if ($auth['branch_status'] == 0) {
				$sesi['branch_id']		= $auth['branch_id'];
			} else {
				$sesi['branch_id']		= '';
			}
		}

		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getDataAcctDepositoAccount($sesi['deposito_id'], $sesi['branch_id']);
		$data['main_view']['acctdeposito']				= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['corebranch']				= create_double($this->AcctDepositoAccount_model->getCoreBranch(), 'branch_id', 'branch_name');
		$data['main_view']['content']			= 'AcctDepositoAccount/ListAddAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function filterListAcctDepositoAccount(){
		$data = array(
			// "start_date" 	=> tgltodb($this->input->post('start_date',true)),
			// "end_date" 		=> tgltodb($this->input->post('end_date',true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-listacctdepositoaccount', $data);
		redirect('deposito-account/list');
	}

	public function addNewAcctDepositoAccount(){
		$auth 					= $this->session->userdata('auth');
		$unique 				= $this->session->userdata('unique');
		$deposito_account_id 	= $this->uri->segment(3);
		$token 					= $this->session->userdata('acctdepositoaccount-' . $unique['unique']);

		if (empty($token)) {
			$token = md5(date('Y-m-d H:i:s'));
			$this->session->set_userdata('acctdepositoaccount-' . $unique['unique'], $token);
		}

		$data['main_view']['acctdepositoaccount']		= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);
		$data['main_view']['acctdeposito']				= create_double($this->AcctDepositoAccount_model->getAcctDeposito(), 'deposito_id', 'deposito_name');
		$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']), 'savings_account_id', 'savings_account_no');
		$data['main_view']['membergender']				= $this->configuration->MemberGender();
		$data['main_view']['memberidentity'] 			= $this->configuration->MemberIdentity();

		$data['main_view']['content']			= 'AcctDepositoAccount/FormAddNewAcctDepositoAccount_view';
		$this->load->view('MainPage_view', $data);
	}

	public function getAcctDepositoAccount_Detail(){
		$deposito_account_id 	= $this->input->post('deposito_account_id');

		// $member_id = 9;

		$data 			= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Detail($deposito_account_id);
		$membergender	= $this->configuration->MemberGender();
		$memberidentity = $this->configuration->MemberIdentity();
		$city_name 		= $this->AcctDepositoAccount_model->getCityName($data['city_id']);
		$kecamatan_name = $this->AcctDepositoAccount_model->getKecamatanName($data['kecamatan_id']);

		$result = array();
		$result = array(
			"member_id"					=> $data['member_id'],
			"member_no"					=> $data['member_no'],
			"member_date_of_birth" 		=> tgltoview($data['member_date_of_birth']),
			"member_gender"				=> $membergender[$data['member_gender']],
			"member_address"			=> $data['member_address'],
			"city_name"					=> $city_name,
			"kecamatan_name"			=> $kecamatan_name,
			"member_job"				=> $data['member_job'],
			"identity_name"				=> $memberidentity[$data['identity_id']],
			"member_identity_no"		=> $data['member_identity_no'],
			"member_phone"				=> $data['member_phone'],
		);
		echo json_encode($result);
	}

	public function processAddNewAcctDepositoAccount(){
		$auth = $this->session->userdata('auth');

		$data = array(
			'member_id'								=> $this->input->post('member_id', true),
			'deposito_id'							=> $this->input->post('deposito_id', true),
			'office_id'								=> $this->input->post('office_id', true),
			'branch_id'								=> $auth['branch_id'],
			'savings_account_id'					=> $this->input->post('savings_account_id', true),
			'deposito_account_date'					=> date('Y-m-d'),
			'deposito_account_due_date'				=> tgltodb($this->input->post('deposito_account_due_date', true)),
			'deposito_account_no'					=> $this->input->post('deposito_account_no', true),
			'deposito_account_serial_no'			=> $this->input->post('deposito_account_serial_no', true),
			'deposito_account_amount'				=> $this->input->post('deposito_account_amount', true),
			'deposito_account_nisbah'				=> $this->input->post('deposito_account_nisbah', true),
			'deposito_account_period'				=> $this->input->post('deposito_period', true),
			'deposito_account_cashback'				=> $this->input->post('deposito_account_cashback', true),
			'deposito_account_commission_amount'	=> $this->input->post('deposito_account_commission_amount', true),
			'deposito_account_token'				=> $this->input->post('deposito_period', true),
			'created_id'							=> $auth['user_id'],
			'created_on'							=> date('Y-m-d H:i:s'),
		);

		$deposito_account_token 					= $this->AcctDepositoAccount_model->getDepositoAccountToken($data['deposito_account_token']);

		if ($deposito_account_token->num_rows() == 0) {
			if ($this->AcctDepositoAccount_model->insertAcctDepositoAccount($data)) {

				$transaction_module_code = "DEP";

				$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
				$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Last($data['created_on']);


				$journal_voucher_period = date("Ym", strtotime($data['deposito_account_date']));

				$data_journal = array(
					'branch_id'						=> $auth['branch_id'],
					'journal_voucher_period' 		=> $journal_voucher_period,
					'journal_voucher_date'			=> date('Y-m-d'),
					'journal_voucher_title'			=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
					'journal_voucher_description'	=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
					'transaction_module_id'			=> $transaction_module_id,
					'transaction_module_code'		=> $transaction_module_code,
					'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
					'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
					'journal_voucher_token' 		=> $data['deposito_account_token'],
					'created_id' 					=> $data['created_id'],
					'created_on' 					=> date('Y-m-d H:i:s'),
				);

				$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

				$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data['created_id']);

				$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

				$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

				$data_debet = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $preferencecompany['account_cash_id'],
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($data['deposito_account_amount']),
					'journal_voucher_debit_amount'	=> ABS($data['deposito_account_amount']),
					'account_id_default_status'		=> $account_id_default_status,
					'journal_voucher_item_token' 	=> $data['deposito_account_token'] . $preferencecompany['account_cash_id'],
					'account_id_status'				=> 0,
					'created_id' 					=> $auth['user_id'],
				);

				$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);

				$account_id = $this->AcctDepositoAccount_model->getAccountID($data['deposito_id']);

				$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

				$data_credit = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($data['deposito_account_amount']),
					'journal_voucher_credit_amount'	=> ABS($data['deposito_account_amount']),
					'journal_voucher_item_token' 	=> $data['deposito_account_token'] . $account_id,
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 1,
					'created_id' 					=> $auth['user_id'],
				);

				$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Simpanan Berjangka Sukses
							</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
				$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			} else {
				$this->session->set_userdata('addacctdepositoaccount', $data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Simpanan Berjangka Tidak Berhasil
							</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('deposito-account');
			}
		} else {
			$deposito_account_id = $this->AcctDepositoAccount_model->getDepositoAccountID($data['created_on']);

			$date 	= date('d', strtotime($data['deposito_account_date']));
			$month 	= date('m', strtotime($data['deposito_account_date']));
			$year 	= date('Y', strtotime($data['deposito_account_date']));

			$transaction_module_code = "DEP";

			$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
			$acctdepositoaccount_last 	= $this->AcctDepositoAccount_model->getAcctDepositoAccount_Last($data['created_on']);


			$journal_voucher_period = date("Ym", strtotime($data['deposito_account_date']));

			$data_journal = array(
				'branch_id'						=> $auth['branch_id'],
				'journal_voucher_period' 		=> $journal_voucher_period,
				'journal_voucher_date'			=> date('Y-m-d'),
				'journal_voucher_title'			=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
				'journal_voucher_description'	=> 'SETORAN SIMP BERJANGKA ' . $acctdepositoaccount_last['member_name'],
				'transaction_module_id'			=> $transaction_module_id,
				'transaction_module_code'		=> $transaction_module_code,
				'transaction_journal_id' 		=> $acctdepositoaccount_last['deposito_account_id'],
				'transaction_journal_no' 		=> $acctdepositoaccount_last['deposito_account_no'],
				'journal_voucher_token' 		=> $data['deposito_account_token'],
				'created_id' 					=> $data['created_id'],
				'created_on' 					=> date('Y-m-d H:i:s'),
			);

			$journal_voucher_token 					= $this->AcctDepositoAccount->getAcctJournalVoucherToken($data_journal['journal_voucher_token']);

			if ($journal_voucher_token->num_rows() == 0) {
				$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);
			}

			$journal_voucher_id = $this->AcctDepositoAccount_model->getJournalVoucherID($data['created_id']);

			$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();

			$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

			$data_debet = array(
				'journal_voucher_id'			=> $journal_voucher_id,
				'account_id'					=> $preferencecompany['account_cash_id'],
				'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
				'journal_voucher_amount'		=> ABS($data['deposito_account_amount']),
				'journal_voucher_debit_amount'	=> ABS($data['deposito_account_amount']),
				'account_id_default_status'		=> $account_id_default_status,
				'journal_voucher_item_token' 	=> $data['deposito_account_token'] . $preferencecompany['account_cash_id'],
				'account_id_status'				=> 0,
				'created_id' 					=> $auth['user_id'],
			);

			$journal_voucher_item_token 		= $this->AcctDepositoAccount->getAcctJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

			if ($journal_voucher_item_token->num_rows() == 0) {
				$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debet);
			}

			$account_id = $this->AcctDepositoAccount_model->getAccountID($data['deposito_id']);

			$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

			$data_credit = array(
				'journal_voucher_id'			=> $journal_voucher_id,
				'account_id'					=> $account_id,
				'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
				'journal_voucher_amount'		=> ABS($data['deposito_account_amount']),
				'journal_voucher_credit_amount'	=> ABS($data['deposito_account_amount']),
				'journal_voucher_item_token' 	=> $data['deposito_account_token'] . $account_id,
				'account_id_default_status'		=> $account_id_default_status,
				'account_id_status'				=> 1,
				'created_id' 					=> $auth['user_id'],
			);

			$journal_voucher_item_token 		= $this->AcctDepositoAccount->getAcctJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

			if ($journal_voucher_item_token->num_rows() == 0) {
				$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
			}
			$auth = $this->session->userdata('auth');
			// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
			$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Simpanan Berjangka Sukses
						</div> ";
			$sesi = $this->session->userdata('unique');
			$this->session->unset_userdata('addacctdepositoaccount-' . $sesi['unique']);
			$this->session->unset_userdata('acctdepositoaccounttoken-' . $sesi['unique']);
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account');
		}
	}

	public function exportMasterDataAcctDepositoAccount(){
		// $sesi	= 	$this->session->userdata('filter-acctdepositoaccount');
		// if(!is_array($sesi)){
		// 	$sesi['start_date']		= date('Y-m-d');
		// 	$sesi['end_date']		= date('Y-m-d');
		// 	$sesi['deposito_id']		='';
		// }

		// $start_date = tgltodb($sesi['start_date']);
		// $end_date 	= tgltodb($sesi['end_date']);

		$acctdepositoaccount	= $this->AcctDepositoAccount_model->getExport();


		if ($acctdepositoaccount->num_rows() != 0) {
			$this->load->library('Excel');

			$this->excel->getProperties()->setCreator("SIS")
				->setLastModifiedBy("SIS")
				->setTitle("Master Data Simpanan Berjangka")
				->setSubject("")
				->setDescription("Master Data Simpanan Berjangka")
				->setKeywords("Master, Data, Simpanan, Berjangka")
				->setCategory("Master Data Simpanan Berjangka");

			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);


			$this->excel->getActiveSheet()->mergeCells("B1:K1");
			$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
			$this->excel->getActiveSheet()->getStyle('B3:K3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('B3:K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B3:K3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('B1', "Master Data Simpanan Berjangka");

			$this->excel->getActiveSheet()->setCellValue('B3', "No");
			$this->excel->getActiveSheet()->setCellValue('C3', "Nama Anggota");
			$this->excel->getActiveSheet()->setCellValue('D3', "Jenis Simp Berjangka");
			$this->excel->getActiveSheet()->setCellValue('E3', "Jenis Perpanjangan");
			$this->excel->getActiveSheet()->setCellValue('F3', "No. SimKa");
			$this->excel->getActiveSheet()->setCellValue('G3', "No. seri");
			$this->excel->getActiveSheet()->setCellValue('H3', "Tgl Buka");
			$this->excel->getActiveSheet()->setCellValue('I3', "JT Tempo");
			$this->excel->getActiveSheet()->setCellValue('J3', "Nominal");
			$this->excel->getActiveSheet()->setCellValue('K3', "Bagi Hasil");

			$j = 4;
			$no = 0;

			foreach ($acctdepositoaccount->result_array() as $key => $val) {
				if (is_numeric($key)) {
					if ($val['deposito_account_extra_type'] == '1') {
						$type_extra = 'ARO';
					} else {
						$type_extra = 'Manual';
					}
					$no++;
					$this->excel->setActiveSheetIndex(0);
					$this->excel->getActiveSheet()->getStyle('B' . $j . ':K' . $j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->getStyle('B' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('D' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('E' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('F' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('G' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('H' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('I' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('J' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$this->excel->getActiveSheet()->getStyle('K' . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


					$this->excel->getActiveSheet()->setCellValue('B' . $j, $no);
					$this->excel->getActiveSheet()->setCellValue('C' . $j, $val['member_name']);
					$this->excel->getActiveSheet()->setCellValue('D' . $j, $val['deposito_name']);
					$this->excel->getActiveSheet()->setCellValue('E' . $j, $type_extra);
					$this->excel->getActiveSheet()->setCellValueExplicit('F' . $j, $val['deposito_account_no']);
					$this->excel->getActiveSheet()->setCellValue('G' . $j, $val['deposito_account_serial_no']);
					$this->excel->getActiveSheet()->setCellValue('H' . $j, tgltoview($val['deposito_account_date']));
					$this->excel->getActiveSheet()->setCellValue('I' . $j, tgltoview($val['deposito_account_due_date']));
					$this->excel->getActiveSheet()->setCellValue('J' . $j, number_format($val['deposito_account_amount'], 2));
					$this->excel->getActiveSheet()->setCellValue('K' . $j, $val['deposito_account_nisbah']);
				} else {
					continue;
				}
				$j++;
			}
			$filename = 'Master Data Simpanan Berjangka.xls';
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($this->excel, 'Excel5');
			if (ob_get_length() > 0) {
				ob_end_clean();
			}
			$objWriter->save('php://output');
		} else {
			echo "Maaf data yang di eksport tidak ada !";
		}
	}

	public function function_state_add(){
		$unique 	= $this->session->userdata('unique');
		$value 		= $this->input->post('value', true);
		$sessions	= $this->session->userdata('addacctdepositoaccount-' . $unique['unique']);
		$sessions['active_tab'] = $value;
		$this->session->set_userdata('addacctdepositoaccount-' . $unique['unique'], $sessions);
	}

	public function listAcctCommission($deposito_account_id){
		$unique 		= $this->session->userdata('unique');
		$acctcommission = $this->AcctDepositoAccount_model->getListAcctCommission($deposito_account_id);

		$data['main_view']['acctcommission']	= $acctcommission;
		$data['main_view']['content']			= 'AcctDepositoAccount/ListAcctCommission_view';
		$this->load->view('MainPage_view', $data);
	}

	public function editAcctCommission(){
		$unique 		= $this->session->userdata('unique');
		$acctcommission = $this->AcctDepositoAccount_model->getAcctCommissionDetail($this->uri->segment(4));

		$data['main_view']['acctcommission']	= $acctcommission;
		$data['main_view']['content']			= 'AcctDepositoAccount/FormEditAcctCommission_view';
		$this->load->view('MainPage_view', $data);
	}

	public function processEditAcctCommission(){
		$auth = $this->session->userdata('auth');

		$data = array(
			'commission_id' 					=> $this->input->post('commission_id', true),
			'commission_disbursed_agent' 		=> $this->input->post('commission_disbursed_agent', true),
			'commission_on_hold_agent' 			=> $this->input->post('commission_on_hold_agent', true),
			'commission_disbursed_supervisor' 	=> $this->input->post('commission_disbursed_supervisor', true),
			'commission_on_hold_supervisor' 	=> $this->input->post('commission_on_hold_supervisor', true),
		);

		if ($this->AcctDepositoAccount_model->processEditCommission($data)) {
			$this->fungsi->set_log($auth['user_id'], $auth['username'], '1005', 'Application.CoreMember.deleteAcctCommission', $auth['user_id'], 'Delete Acct Commission');
			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Edit Komisi Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/commission/'.$this->input->post('deposito_account_id', true));
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Edit Komisi Gagal
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/edit-commission/'.$this->input->post('deposito_account_id'.$data['commission_id'], true));
		}
	}

	public function processDeleteAcctCommission(){
		$auth = $this->session->userdata('auth');

		if ($this->AcctDepositoAccount_model->deleteAcctCommission($this->uri->segment(4))) {
			$this->fungsi->set_log($auth['user_id'], $auth['username'], '1005', 'Application.CoreMember.deleteAcctCommission', $auth['user_id'], 'Delete Acct Commission');
			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Komisi Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/commission/'.$this->uri->segment(3));
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Komisi Gagal
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('deposito-account/commission/'.$this->uri->segment(3));
		}
	}

	public function getpercentageCommision()
	{
		// $core_commision_id 	= 1;

		$core_commision_id 	= $this->input->post('core_commision_agent_id');

		$commision 	= $this->CoreCommision_model->getCommision_percentage($core_commision_id);

		$result = array(
			'commision'	=> $commision,
		);
		
		echo json_encode($result);
	}

	public function getpercentageCommisionSpv()
	{
		// $core_commision_id 	= 1;

		$core_commision_id 	= $this->input->post('core_commision_supervisor_id');

		$commision 	= $this->CoreCommision_model->getCommision_percentage($core_commision_id);

		$result = array(
			'commision'	=> $commision,
		);
		
		echo json_encode($result);
	}
}