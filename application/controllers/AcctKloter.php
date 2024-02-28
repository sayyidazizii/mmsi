<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctKloter extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('Connection_model');
		$this->load->model('MainPage_model');
		$this->load->model('AcctKloter_model');
		$this->load->model('CoreMember_model');
		$this->load->helper('sistem');
		$this->load->helper('url');
		$this->load->database('default');
		$this->load->library('configuration');
		$this->load->library('fungsi');
		$this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
	}

	public function index(){
		$sesi 	= $this->session->userdata('unique');

		$this->session->unset_userdata('addAcctKloter-' . $sesi['unique']);
		$this->session->unset_userdata('editAcctKloter-' . $sesi['unique']);

		$data['main_view']['acctkloter'] 		= $this->AcctKloter_model->getAcctKloter();
		$data['main_view']['content']	 		= 'AcctKloter/ListAcctKloter_view';
		$this->load->view('MainPage_view', $data);
	}

	public function addAcctKloter(){
		$data['main_view']['acctaccount']				= create_double($this->AcctKloter_model->getAcctAccount(), 'account_id', 'account_code');
		$data['main_view']['content']					= 'AcctKloter/FormAddAcctKloter_view';
		$this->load->view('MainPage_view', $data);
	}

	public function function_elements_add(){
		$unique 	= $this->session->userdata('unique');
		$name 		= $this->input->post('name', true);
		$value 		= $this->input->post('value', true);
		$sessions	= $this->session->userdata('addAcctKloter-' . $unique['unique']);
		$sessions[$name] = $value;
		$this->session->set_userdata('addAcctKloter-' . $unique['unique'], $sessions);
	}

	public function processAddAcctKloter(){
		$auth 		= $this->session->userdata('auth');
		$sesi 		= $this->session->userdata('unique');

		$data = array(
			'kloter_name'								=> $this->input->post('kloter_name', true),
			'kloter_quota'								=> $this->input->post('kloter_quota', true),
			'branch_id'									=> $auth['branch_id'],
			'kloter_period'								=> $this->input->post('kloter_period', true),
			'kloter_amount'								=> $this->input->post('kloter_amount', true),
			'kloter_prize_amount'						=> $this->input->post('kloter_prize_amount', true),
			'kloter_prize'								=> $this->input->post('kloter_prize', true),
			'account_kloter_id'							=> $this->input->post('account_kloter_id', true),
			'account_prize_id'							=> $this->input->post('account_prize_id', true),
			'kloter_token'								=> $this->input->post('kloter_token', true),
			'kloter_point'								=> $this->input->post('kloter_point', true),
			'created_id'								=> $auth['user_id'],
			'created_on'								=> date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('kloter_period', 'Jangka Waktu', 'required');
		$this->form_validation->set_rules('kloter_amount', 'Nominal Partisipasi', 'required');
		$this->form_validation->set_rules('kloter_prize_amount', 'Total Hadiah', 'required');
		$this->form_validation->set_rules('kloter_prize', 'Hadiah', 'required');
		$this->form_validation->set_rules('kloter_quota', 'Kuota Kloter', 'required');
		$this->form_validation->set_rules('account_kloter_id', 'Kuota Kloter', 'required');
		$this->form_validation->set_rules('account_prize_id', 'Kuota Kloter', 'required');
		$this->form_validation->set_rules('kloter_point', 'Poin', 'required');

		if ($this->input->post('kloter_quota') < 0) {
			$msg = "<div class='alert alert-danger alert-dismissable'> 
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
					Kuota tidak boleh negatif ( - )
				</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter/add/');
		}

		if ($this->form_validation->run() == true) {
			if ($this->AcctKloter_model->insertAcctKloter($data)) {
				$auth = $this->session->userdata('auth');
				$sesi = $this->session->userdata('unique');

				$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Kloter Sukses
								</div> ";
				$this->session->unset_userdata('addAcctKloter-' . $sesi['unique']);
				$this->session->set_userdata('message', $msg);
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Kloter Tidak Berhasil
								</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('kloter/add');
			}
			redirect('kloter/add');
		} else {
			$this->session->set_userdata('addAcctKloter', $data);
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('kloter/add');
		}
	}

	public function editAcctKloter(){
		$unique 									= $this->session->userdata('unique');

		$data['main_view']['acctaccount']			= create_double($this->AcctKloter_model->getAcctAccount(), 'account_id', 'account_code');
		$data['main_view']['acctkloter']			= $this->AcctKloter_model->getAcctKloter_Detail($this->uri->segment(3));
		$data['main_view']['content']				= 'AcctKloter/FormEditAcctKloter_view';
		$this->load->view('MainPage_view', $data);
	}

	public function function_elements_edit(){
		$unique 	= $this->session->userdata('unique');
		$name 		= $this->input->post('name', true);
		$value 		= $this->input->post('value', true);
		$sessions	= $this->session->userdata('editAcctKloter-' . $unique['unique']);
		$sessions[$name] = $value;
		$this->session->set_userdata('editAcctKloter-' . $unique['unique'], $sessions);
	}

	public function processEditAcctKloter(){
		$auth		= $this->session->userdata('auth');
		$sesi 		= $this->session->userdata('unique');

		$data = array(
			'kloter_id'									=> $this->input->post('kloter_id', true),
			'branch_id'									=> $auth['branch_id'],
			'kloter_name'								=> $this->input->post('kloter_name', true),
			'kloter_quota'								=> $this->input->post('kloter_quota', true),
			'kloter_period'								=> $this->input->post('kloter_period', true),
			'kloter_amount'								=> $this->input->post('kloter_amount', true),
			'kloter_prize_amount'						=> $this->input->post('kloter_prize_amount', true),
			'kloter_prize'								=> $this->input->post('kloter_prize', true),
			'kloter_point'								=> $this->input->post('kloter_point', true),
			'account_kloter_id'							=> $this->input->post('account_kloter_id', true),
			'account_prize_id'							=> $this->input->post('account_prize_id', true),
			'created_id'								=> $auth['user_id'],
		);

		$this->form_validation->set_rules('kloter_period', 'Jangka Waktu', 'required');
		$this->form_validation->set_rules('kloter_amount', 'Nominal Partisipasi', 'required');
		$this->form_validation->set_rules('kloter_prize_amount', 'Total Hadiah', 'required');
		$this->form_validation->set_rules('kloter_prize', 'Hadiah', 'required');
		$this->form_validation->set_rules('kloter_quota', 'Kuota Kloter', 'required');
		$this->form_validation->set_rules('kloter_point', 'Poin', 'required');
		$this->form_validation->set_rules('account_kloter_id', 'Pendapatan Kloter', 'required');
		$this->form_validation->set_rules('account_prize_id', 'Biaya Hadiah', 'required');

		if ($this->input->post('kloter_quota') < 0) {
			$msg = "<div class='alert alert-danger alert-dismissable'> 
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
					Kuota tidak boleh negatif ( - )
				</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter/edit/' . $data['kloter_id']);
		}

		if ($this->form_validation->run() == true) {
			if ($this->AcctKloter_model->updateAcctKloter($data)) {
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Edit Data Kloter Sukses
							</div> ";
				$this->session->unset_userdata('editAcctKloter-' . $sesi['unique']);
				$this->session->set_userdata('message', $msg);
				redirect('kloter/edit/' . $data['kloter_id']);
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Data Kloter Tidak Berhasil
							</div> ";
				$this->session->set_userdata('message', $msg);
				redirect('kloter/edit/' . $data['kloter_id']);
			}
		} else {
			$this->session->set_userdata('editmachine', $data);
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('kloter/edit/' . $data['kloter_id']);
		}
	}

	public function listMemberParticipate(){
		$sesi 	= $this->session->userdata('unique');
		$kloter_id = $this->uri->segment(3);

		$this->session->unset_userdata('addMemberParticipate-' . $sesi['unique']);
		$this->session->unset_userdata('editMemberParticipate-' . $sesi['unique']);

		$data['main_view']['getmemberparticipate']		= create_double($this->AcctKloter_model->getMemberParticipate(), 'member_id', 'member_no');
		$data['main_view']['acctkloter']				= $this->AcctKloter_model->getAcctKloter_Detail($this->uri->segment(3));
		$data['main_view']['memberparticipate'] 		= $this->AcctKloter_model->getMemberParticipate_Detail($kloter_id);
		$data['main_view']['content']	 				= 'AcctKloter/ListAcctKloterMemberParticipate_view';
		$this->load->view('MainPage_view', $data);
	}

	public function processAddMemberParticipate(){
		$auth		= $this->session->userdata('auth');
		$sesi 		= $this->session->userdata('unique');
		$member_id  = $this->uri->segment(3);
		$kloter_id  = $this->uri->segment(4);

		$data = array(
			'kloter_id'									=> $kloter_id,
			'branch_id'									=> $auth['branch_id'],
			'member_id'									=> $member_id,
			'created_id'								=> $auth['user_id'],
			'created_on'								=> date('Y-m-d H:i:s'),
		);

		$acctkloter 	= $this->AcctKloter_model->getAcctKloter_Detail($data['kloter_id']);
		$member_name 	= $this->AcctKloter_model->getCoreMemberName($data['member_id']);
		$kloter_point 	= $this->AcctKloter_model->getAcctKloterPoint($data['kloter_id']);

		if ($this->AcctKloter_model->insertMemberParticipate($data)) {
			$kloter_quota		= $this->AcctKloter_model->getKloterQuota($data['kloter_id']);

			$data_kloter		= array(
				'kloter_id'		=> $data['kloter_id'],
				'kloter_quota'	=> $kloter_quota - 1,
			);

			if ($this->AcctKloter_model->updateAcctKloter($data_kloter)) {
				$transaction_module_code 	= "PK";
				$transaction_module_id 		= $this->AcctKloter_model->getTransactionModuleID($transaction_module_code);
				$journal_voucher_period 	= date("Ym");

				$data_journal = array(
					'branch_id'						=> $auth['branch_id'],
					'journal_voucher_period' 		=> $journal_voucher_period,
					'journal_voucher_date'			=> date('Y-m-d'),
					'journal_voucher_title'			=> 'PARTISIPASI KLOTER ' . $member_name,
					'journal_voucher_description'	=> 'PARTISIPASI KLOTER ' . $member_name,
					'journal_voucher_token'			=> md5(rand()),
					'transaction_module_id'			=> $transaction_module_id,
					'transaction_module_code'		=> $transaction_module_code,
					'transaction_journal_id' 		=> $kloter_id,
					'transaction_journal_no' 		=> $kloter_id,
					'created_id' 					=> $data['created_id'],
					'created_on' 					=> date('Y-m-d H:i:s'),
				);

				$journal_voucher_token = $this->AcctKloter_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

				if ($journal_voucher_token->num_rows() == 0) {
				// 	$this->AcctKloter_model->insertAcctJournalVoucher($data_journal);

				// 	$journal_voucher_id 		= $this->AcctKloter_model->getJournalVoucherID($data['created_id']);

				// 	$preferencecompany 			= $this->AcctKloter_model->getPreferenceCompany();

				// 	$account_id_default_status 	= $this->AcctKloter_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

				// 	$data_debet = array(
				// 		'journal_voucher_id'			=> $journal_voucher_id,
				// 		'account_id'					=> $preferencecompany['account_cash_id'],
				// 		'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
				// 		'journal_voucher_amount'		=> $acctkloter['kloter_amount'],
				// 		'journal_voucher_debit_amount'	=> $acctkloter['kloter_amount'],
				// 		'account_id_default_status'		=> $account_id_default_status,
				// 		'account_id_status'				=> 0,
				// 		'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'] . $preferencecompany['account_cash_id'],
				// 		'created_id' 					=> $auth['user_id'],
				// 	);
					
				// 	$journal_voucher_token = $this->AcctKloter_model->getJournalVoucherToken($data_journal['journal_voucher_token']);
					
				// 	if ($journal_voucher_token->num_rows() == 0) {
						$this->AcctKloter_model->insertAcctJournalVoucher($data_journal);
		
						$journal_voucher_id 		= $this->AcctKloter_model->getJournalVoucherID($data['created_id']);
						
						$preferencecompany 			= $this->AcctKloter_model->getPreferenceCompany();
						
						$account_id_default_status 	= $this->AcctKloter_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
					
						$data_debet = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
							'journal_voucher_amount'		=> $acctkloter['kloter_amount'],
							'journal_voucher_debit_amount'	=> $acctkloter['kloter_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'] . $preferencecompany['account_cash_id'],
							'created_id' 					=> $auth['user_id'],
						);
		
						$journal_voucher_item_token = $this->AcctKloter_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);
		
						if ($journal_voucher_item_token->num_rows() == 0) {
							$this->AcctKloter_model->insertAcctJournalVoucherItem($data_debet);
						}
		
						$account_id = $acctkloter['account_kloter_id'];
		
						$account_id_default_status = $this->AcctKloter_model->getAccountIDDefaultStatus($account_id);
		
						$journal_voucher_credit_amount = $acctkloter['kloter_amount'];
		
						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
							'journal_voucher_amount'		=> $journal_voucher_credit_amount,
							'journal_voucher_credit_amount'	=> $journal_voucher_credit_amount,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_journal['journal_voucher_token'] . $account_id,
							'created_id' 					=> $auth['user_id'],
						);
		
						$journal_voucher_item_token = $this->AcctKloter_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
		
						if ($journal_voucher_item_token->num_rows() == 0) {
							$this->AcctKloter_model->insertAcctJournalVoucherItem($data_credit);
						}
				// 	}
				}
			}

			$data_point = array(
				'member_id' 	=> $data['member_id'],
				'point_date' 	=> date('Y-m-d'),
				'point_from' 	=> 'Partisipan Kloter',
				'point_amount' 	=> $kloter_point,
				'branch_id' 	=> $auth['branch_id'],
				'created_id' 	=> $auth['user_id'],
				'created_on' 	=> date('Y-m-d'),
			);
			$this->AcctKloter_model->insertSystemPoint($data_point);

			$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Anggota Partisipan Sukses
						</div> ";
			$this->session->unset_userdata('addMemberParticipate-' . $sesi['unique']);
			$this->session->set_userdata('message', $msg);
			redirect('kloter/member-participate/' . $data['kloter_id']);
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Tambah Anggota Partisipan Tidak Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter/member-participate/' . $data['kloter_id']);
		}
	}

	public function detailAcctKloter(){
		$unique 									= $this->session->userdata('unique');
		$sesi 	= $this->session->userdata('unique');
		$kloter_id = $this->uri->segment(3);

		$data['main_view']['acctkloter']			= $this->AcctKloter_model->getAcctKloter_Detail($this->uri->segment(3));

		$this->session->unset_userdata('addMemberParticipate-' . $sesi['unique']);
		$this->session->unset_userdata('editMemberParticipate-' . $sesi['unique']);

		$data['main_view']['getmemberparticipate']		= create_double($this->AcctKloter_model->getMemberParticipate(), 'member_id', 'member_no');
		$data['main_view']['acctkloter']				= $this->AcctKloter_model->getAcctKloter_Detail($this->uri->segment(3));
		$data['main_view']['memberparticipate'] 		= $this->AcctKloter_model->getMemberParticipate_Detail($kloter_id);
		$data['main_view']['content']				= 'AcctKloter/DetailAcctKloter_view';
		$this->load->view('MainPage_view', $data);
	}

	public function deleteMemberParticipate(){
		if ($this->AcctKloter_model->deleteMemberParticipate($this->uri->segment(3))) {
			$auth = $this->session->userdata('auth');

			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Kloter Sukses
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter/member-participate/' . $this->uri->segment(4));
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Kloter Tidak Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter/member-participate/' . $this->uri->segment(4));
		}
	}

	public function deleteAcctKloter(){
		if ($this->AcctKloter_model->deleteAcctKloter($this->uri->segment(3))) {
			$auth = $this->session->userdata('auth');
			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Kloter Sukses
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter');
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Kloter Tidak Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter');
		}
	}

	public function getListCoreMember(){
		$auth 		= $this->session->userdata('auth');
		$kloter_id  = $this->uri->segment(3);

		$list = $this->CoreMember_model->get_datatables($auth['branch_id']);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $customers) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $customers->member_no;
			$row[] = $customers->member_name;
			$row[] = $customers->member_address;
			$row[] = '<a href="' . base_url() . 'kloter/process-add-member-participate/' . $customers->member_id . '/' . $kloter_id . '" class="btn btn-info" role="button"><span class="fa fa-plus"></span> Tambah</a>';
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->CoreMember_model->count_all($auth['branch_id']),
			"recordsFiltered" => $this->CoreMember_model->count_filtered($auth['branch_id']),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function closingAcctKloter(){
		$auth = $this->session->userdata('auth');
		$kloter_id = $this->uri->segment(3);
		$preferencecompany = $this->AcctKloter_model->getPreferenceCompany();
		$acctkloter	= $this->AcctKloter_model->getAcctKloter_Detail($kloter_id);
		// print_r($acctkloter); exit;

		$data = array(
			'kloter_id'  		=> $kloter_id,
			'kloter_status'		=> 1,
			'created_id'		=> $auth['user_id'],
			'created_on'		=> date('Y-m-d H:i:s'),
		);

		// print_r($data);exit;

		if ($this->AcctKloter_model->closingAcctKloter($data)) {
			$token = md5(rand());
			$transaction_module_code 	= "CK";
			$transaction_module_id 		= $this->AcctKloter_model->getTransactionModuleID($transaction_module_code);
			$journal_voucher_period 	= date("Ym", strtotime($acctkloter['created_on']));

			$data_journal = array(
				'branch_id'						=> $auth['branch_id'],
				'journal_voucher_period' 		=> $journal_voucher_period,
				'journal_voucher_date'			=> date('Y-m-d'),
				'journal_voucher_title'			=> 'PENUTUPAN KLOTER ' . $acctkloter['kloter_name'],
				'journal_voucher_description'	=> 'PENUTUPAN KLOTER ' . $acctkloter['kloter_name'],
				'journal_voucher_token'			=> $token,
				'transaction_module_id'			=> $transaction_module_id,
				'transaction_module_code'		=> $transaction_module_code,
				'transaction_journal_id' 		=> $acctkloter['kloter_id'],
				'transaction_journal_no' 		=> $acctkloter['kloter_id'],
				'created_id' 					=> $acctkloter['created_id'],
				'created_on' 					=> date('Y-m-d H:i:s'),
			);
			// print_r($data_journal);exit;

			$journal_voucher_token = $this->AcctKloter_model->getJournalVoucherToken($token);

			if ($journal_voucher_token->num_rows() == 0) {
				$this->AcctKloter_model->insertAcctJournalVoucher($data_journal);

				$account_id = $acctkloter['account_prize_id'];

				$journal_voucher_id 		= $this->AcctKloter_model->getJournalVoucherID($acctkloter['created_id']);

				$account_id_default_status = $this->AcctKloter_model->getAccountIDDefaultStatus($account_id);

				$data_debet = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($acctkloter['kloter_prize_amount']),
					'journal_voucher_debit_amount'	=> ABS($acctkloter['kloter_prize_amount']),
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 0,
					'journal_voucher_item_token'	=> $token . $account_id,
					'created_id' 					=> $auth['user_id'],
				);
				// print_r($data_debet);
				// exit;

				$journal_voucher_item_token = $this->AcctKloter_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

				if ($journal_voucher_item_token->num_rows() == 0) {
					$this->AcctKloter_model->insertAcctJournalVoucherItem($data_debet);

					$preferencecompany 			= $this->AcctKloter_model->getPreferenceCompany();

					$account_id_default_status 	= $this->AcctKloter_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					// $journal_voucher_credit_amount = $acctkloter['kloter_prize_amount'];

					$data_credit = array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
						'journal_voucher_amount'		=> ABS($acctkloter['kloter_prize_amount']),
						'journal_voucher_credit_amount'	=> ABS($acctkloter['kloter_prize_amount']),
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $token . $preferencecompany['account_cash_id'],
						'created_id' 					=> $auth['user_id'],
					);
					// print_r($data_credit);
					// exit;
					$journal_voucher_item_token = $this->AcctKloter_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if ($journal_voucher_item_token->num_rows() == 0) {
						$this->AcctKloter_model->insertAcctJournalVoucherItem($data_credit);
					}
				}
			}

			$msg = "<div class='alert alert-success alert-dismissable'>  
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
              Penutupan Kloter Sukses
            </div>";
			$this->session->set_userdata('message', $msg);
			redirect('kloter');
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'> 
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
                  Penutupan Kloter Tidak Berhasil
                </div> ";
			$this->session->set_userdata('message', $msg);
			redirect('kloter');
		}
	}
}
