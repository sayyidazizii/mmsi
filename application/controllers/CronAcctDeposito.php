<?php
class CronAcctDeposito extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('MainPage_model');
		$this->load->model('CronAcctDeposito_model');
		$this->load->model('AcctDepositoAccount_model');
		$this->load->model('AcctSavingsTransferMutation_model');
		$this->load->model('AcctDepositoProfitSharingCheck_model');
		$this->load->helper('sistem');
		$this->load->library('configuration');
		$this->load->library('fungsi');
		$this->load->helper('url');
		$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
	}
	
	public function index(){
		echo "access is denied index";
	}

	public function getDepositoProfitSharing(){ //$password
		$tes				= array();
		$auth 				= $this->session->userdata('auth');
		$unique 			= $this->session->userdata('unique');
		$preferencecompany 	= $this->CronAcctDeposito_model->getPreferenceCompany();
        $corebranch  		= $this->CronAcctDeposito_model->getCoreBranch();
		$token				= md5(rand());
		$today            	= date("Y-m-d");
		$month 				= date('m');
		$year				= date('Y');
		
		if($month == 01 || $month == 1){
			$month = 12;
			$year = $year - 1;
		} else {
			$month = $month - 1;
			$year = $year;
		}
		
		if($month < 10){
			$month = '0'.$month;
		}
		
		foreach($corebranch as $keyy => $vall){
			$depositoprofitsharing  	= $this->CronAcctDeposito_model->getAcctDepositoProfitSharing($today, $vall['branch_id']);
			foreach($depositoprofitsharing as $key => $val){
				$deposito_index_amount 			= ($val['deposito_account_interest'] / 12) / 100;
				$deposito_profit_sharing_amount = $deposito_index_amount*$val['deposito_account_last_balance'];
				$deposito_profit_sharing_period = $month.$year;

				$data = array (
					'deposito_id'						=> $val['deposito_id'],
					'deposito_profit_sharing_id'		=> $val['deposito_profit_sharing_id'],
					'deposito_profit_sharing_date'		=> date('Y-m-d'),
					'deposito_index_amount'				=> $deposito_index_amount,
					'deposito_profit_sharing_amount'	=> $deposito_profit_sharing_amount,
					'deposito_profit_sharing_period'	=> $deposito_profit_sharing_period,
					'savings_account_id'				=> $val['savings_account_id'],
					'deposito_profit_sharing_token'		=> $token.$val['deposito_profit_sharing_id'],
					'deposito_profit_sharing_status'	=> 1,
				);

				$data_savings = array (
					'savings_id'						=> $val['savings_id'],
					'member_id'							=> $val['member_id'],
					'savings_account_opening_balance'	=> $val['savings_account_last_balance'],
					'savings_account_last_balance'		=> $val['savings_account_last_balance'] + $data['deposito_profit_sharing_amount'],
				);

				$transaction_module_code 		= "BSDEP";

				$transaction_module_id 			= $this->AcctDepositoProfitSharingCheck_model->getTransactionModuleID($transaction_module_code);

				$preferencecompany 				= $this->AcctDepositoProfitSharingCheck_model->getPreferenceCompany();
				$depositoaccount				= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoAccountDetail($data['deposito_profit_sharing_id']);

				$deposito_profit_sharing_token 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharingToken($data['deposito_profit_sharing_token']);

				if($deposito_profit_sharing_token->num_rows()==0){
					if($this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoProfitSharing($data)){
						$data_depositoaccount = array (
							'deposito_account_id'			=> $depositoaccount['deposito_account_id'],
							'deposito_account_nisbah'		=> $depositoaccount['deposito_account_nisbah']+$data['deposito_profit_sharing_amount'],
							'deposito_process_last_date'	=> $data['deposito_profit_sharing_date'],
						);
						$this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoAccount($data_depositoaccount);

						$total_amount	= $data['deposito_profit_sharing_amount'];
						$tax_amount		= 0;
						if($total_amount > $preferencecompany['tax_minimum_amount']){
							$tax_amount = $total_amount * $preferencecompany['tax_percentage'] / 100;
						}
						$total_amount_min_tax	= $total_amount - $tax_amount;

						$data_transfer = array (
							'branch_id'							=> $vall['branch_id'],
							'savings_transfer_mutation_date'	=> date('Y-m-d'),
							'savings_transfer_mutation_amount'	=> $total_amount_min_tax,
							'operated_name'						=> 'SYS',
							'created_id'						=> 37,
							'created_on'						=> date('Y-m-d H:i:s'),
						);
	
						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)){
							$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);
	
							$data_transfer_to = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $data['savings_account_id'],
								'savings_id'								=> $data_savings['savings_id'],
								'member_id'									=> $data_savings['member_id'],
								'branch_id'									=> $vall['branch_id'],
								'mutation_id'								=> $preferencecompany['deposito_basil_id'],
								'savings_account_opening_balance'			=> $data_savings['savings_account_opening_balance'],
								'savings_transfer_mutation_to_amount'		=> $total_amount_min_tax,
								'savings_account_last_balance'				=> $data_savings['savings_account_last_balance'],
							);
	
							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)){

								$acctdepositoprofitsharing_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Last($data['deposito_profit_sharing_id']);
		
							
								$journal_voucher_period = date("Ym", strtotime($data['deposito_profit_sharing_date']));
								
								$data_journal = array(
									'branch_id'						=> $vall['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
									'journal_voucher_description'	=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $acctdepositoprofitsharing_last['deposito_profit_sharing_id'],
									'transaction_journal_no' 		=> $acctdepositoprofitsharing_last['deposito_account_no'],
									'journal_voucher_token'			=> $data['deposito_profit_sharing_token'],	
									'created_id' 					=> 37,
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucher($data_journal);
		
								$journal_voucher_id 		= $this->AcctDepositoProfitSharingCheck_model->getJournalVoucherID($data_journal['created_id']);
		
								$account_basil_id 			= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($data['deposito_id']);
		
								$account_id_default_status 	= $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);
		
								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_basil_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($total_amount),
									'journal_voucher_debit_amount'	=> ABS($total_amount),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_basil_id,	
									'account_id_status'				=> 0,
									'created_id' 					=> 37,
								);
		
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debet);
		
								$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);
		
								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);
		
								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($total_amount_min_tax),
									'journal_voucher_credit_amount'	=> ABS($total_amount_min_tax),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_id,
									'account_id_status'				=> 1,
									'created_id' 					=> 37,
									
								);
		
								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								
								if($tax_amount > 0){
									$account_savings_tax_id 	= $preferencecompany['account_savings_tax_id'];

									$account_id_default_status 	= $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_savings_tax_id);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_savings_tax_id,
										'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
										'journal_voucher_amount'		=> ABS($tax_amount),
										'journal_voucher_credit_amount'	=> ABS($tax_amount),
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_savings_tax_id,	
										'account_id_status'				=> 0,
										'created_id' 					=> 37,
									);

									$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}
					}
				}else{
					$data_depositoaccount = array (
						'deposito_account_id'			=> $depositoaccount['deposito_account_id'],
						'deposito_account_nisbah'		=> $depositoaccount['deposito_account_nisbah']+$data['deposito_profit_sharing_amount'],
						'deposito_process_last_date'	=> $data['deposito_profit_sharing_date'],
					);
					$this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoAccount($data_depositoaccount);

					$total_amount	= $data['deposito_profit_sharing_amount'];
					$tax_amount		= 0;
					if($total_amount > $preferencecompany['tax_minimum_amount']){
						$tax_amount = $total_amount * $preferencecompany['tax_percentage'] / 100;
					}
					$total_amount_min_tax	= $total_amount - $tax_amount;

					$data_transfer = array (
						'branch_id'							=> $vall['branch_id'],
						'savings_transfer_mutation_date'	=> date('Y-m-d'),
						'savings_transfer_mutation_amount'	=> $total_amount_min_tax,
						'operated_name'						=> 'SYS',
						'created_id'						=> 37,
						'created_on'						=> date('Y-m-d H:i:s'),
					);

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)){
						$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);

						$data_transfer_to = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $data['savings_account_id'],
							'savings_id'								=> $data_savings['savings_id'],
							'member_id'									=> $data_savings['member_id'],
							'branch_id'									=> $vall['branch_id'],
							'mutation_id'								=> $preferencecompany['deposito_basil_id'],
							'savings_account_opening_balance'			=> $data_savings['savings_account_opening_balance'],
							'savings_transfer_mutation_to_amount'		=> $total_amount_min_tax,
							'savings_account_last_balance'				=> $data_savings['savings_account_last_balance'],
						);

						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)){
							$acctdepositoprofitsharing_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Last($data['deposito_profit_sharing_id']);
							$journal_voucher_period = date("Ym", strtotime($data['deposito_profit_sharing_date']));
							
							$data_journal = array(
								'branch_id'						=> $vall['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
								'journal_voucher_description'	=> 'JASA SIMP BERJANGKA '.$acctdepositoprofitsharing_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctdepositoprofitsharing_last['deposito_profit_sharing_id'],
								'transaction_journal_no' 		=> $acctdepositoprofitsharing_last['deposito_account_no'],
								'journal_voucher_token'			=> $data['deposito_profit_sharing_token'],	
								'created_id' 					=> 37,
								'created_on' 					=> date('Y-m-d H:i:s'),
							);
							
							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctDepositoProfitSharingCheck_model->getJournalVoucherID($data_journal['created_id']);

							$account_basil_id 	= $this->AcctDepositoProfitSharingCheck_model->getAccountBasilID($data['deposito_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_basil_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_basil_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount),
								'journal_voucher_debit_amount'	=> ABS($total_amount),
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_basil_id,	
								'account_id_status'				=> 0,
								'created_id' 					=> 37,
							);

							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debet);

							$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($data_savings['savings_id']);

							$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($total_amount_min_tax),
								'journal_voucher_credit_amount'	=> ABS($total_amount_min_tax),
								'account_id_default_status'		=> $account_id_default_status,
								'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_id,
								'account_id_status'				=> 1,
								'created_id' 					=> 37,
								
							);

							$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
								
							if($tax_amount > 0){
								$account_savings_tax_id 	= $preferencecompany['account_savings_tax_id'];

								$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_savings_tax_id);

								$data_credit = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_savings_tax_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
									'journal_voucher_amount'		=> ABS($tax_amount),
									'journal_voucher_credit_amount'	=> ABS($tax_amount),
									'account_id_default_status'		=> $account_id_default_status,
									'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_savings_tax_id,	
									'account_id_status'				=> 0,
									'created_id' 					=> 37,
								);

								$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
					}
				}
			}
		}
	}

	public function cronJobCommission(){
		$auth = $this->session->userdata('auth');
		$deposito_account_id = $this->uri->segment(3);
		$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();
		$acctcommission = $this->AcctDepositoAccount_model->getAcctCommission();

		foreach ($acctcommission as $key => $val) {
			if($val['commission_disbursed_status'] == 0){
				
					$journal_voucher_id 		= $this->AcctDepositoAccount_model->getJournalVoucherID($val['created_id']);
					$transaction_module_code 	= "DEP";
					$transaction_module_id 		= $this->AcctDepositoAccount_model->getTransactionModuleID($transaction_module_code);
					$journal_voucher_period 	= date("Ym", strtotime($val['deposito_account_date']));
					$token 	= md5(rand());

					$data_journal = array(
						'branch_id'						=> $val['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'PEMBAGIAN KOMISI ' . $val['member_name'],
						'journal_voucher_description'	=> 'PEMBAGIAN KOMISI ' . $val['member_name'],
						'journal_voucher_token'			=> $token.'kms',
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $val['deposito_account_id'],
						'transaction_journal_no' 		=> $val['deposito_account_no'],
						'created_id' 					=> $val['created_id'],
						'created_on' 					=> date('Y-m-d H:i:s'),
					);

				$account_id = $preferencecompany['account_commission_id'];
				$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);
				$commission_agent = $val['commission_on_hold_agent'] + $val['commission_disbursed_agent'];
				$commission_supervisor = $val['commission_on_hold_supervisor'] + $val['commission_disbursed_supervisor'];
				$total_commision_amount = 0;
				
				if($val['savings_account_id_agent']){
					$total_commision_amount += $commission_agent;
				}

				if($val['savings_account_id_supervisor']){
					$total_commision_amount += $commission_supervisor;
				}

				$data_debit = array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
					'journal_voucher_amount'		=> ABS($total_commision_amount),
					'journal_voucher_debit_amount'	=> ABS($total_commision_amount),
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 0,
					'journal_voucher_item_token'	=> $token . $account_id,
					'created_id' 					=> 37,
				);

				$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

				if ($journal_voucher_item_token->num_rows() == 0) {
					$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_debit);
				}
				
				if($val['savings_account_id_agent']){

					$data_agent = $this->AcctDepositoAccount_model->getAcctSavingsAccountLastBalance($val['savings_account_id_agent']);

					// $savings_account_last_balance_agent = implode(" ",$data_last_balance_agent);

					$data_savings_account_agent = array(
						'savings_id'						=> $data_agent['savings_id'],
						'savings_account_id'				=> $data_agent['savings_account_id'],
						'member_id'							=> $data_agent ['member_id'],
						'office_id'                         => $data_agent['office_id'],
						'savings_account_last_balance'		=> $data_agent['savings_account_last_balance'],
					);

					$data_commission_agent = array(
						'branch_id'							=> $val['branch_id'],
						'savings_transfer_mutation_date'	=> date('Y-m-d'),
						'savings_transfer_mutation_amount'	=> $val['commission_on_hold_agent'] + $val['commission_disbursed_agent'],
						'operated_name'						=> 'SYS',
						'created_id'						=> $val['created_id'],
						'created_on'						=> date('Y-m-d H:i:s'),
					);

					if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_commission_agent)) {
						$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_commission_agent['created_on']);
						
						$data_transfer_commission_agent = array(
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $data_savings_account_agent['savings_account_id'],
							'savings_id'								=> $data_savings_account_agent['savings_id'],
							'member_id'									=> $data_savings_account_agent['member_id'],
							'branch_id'									=> $val['branch_id'],
							'mutation_id'								=> $preferencecompany['deposito_basil_id'],
							'savings_transfer_mutation_to_amount'		=> $data_commission_agent['savings_transfer_mutation_amount'],
							'savings_account_last_balance'				=> $data_savings_account_agent['savings_account_last_balance'],
						);

						if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_commission_agent)) {
							$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

							$account_id = $data_agent['account_id'];

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							// $journal_voucher_credit_amount = $commissiononhold['commission_on_hold_amount'];
							$data_credit_agent = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($data_transfer_commission_agent['savings_transfer_mutation_to_amount']),
								'journal_voucher_credit_amount'	=> ABS($data_transfer_commission_agent['savings_transfer_mutation_to_amount']),
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $token . $account_id . 'agent',
								'created_id' 					=> $val['created_id'],
							);
							
							$data= array(
								'savings_account_id' 				=> $data_agent['savings_account_id'],
								'savings_account_blockir_amount'	=> $val['commission_on_hold_agent'],
								'savings_account_blockir_status'	=> 1,
							);
							$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

							$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_credit_agent['journal_voucher_item_token']);

							if ($journal_voucher_item_token->num_rows() == 0) {
								$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit_agent);
							}
						}
					}
				}

				if($val['savings_account_id_supervisor']){

					$data_supervisor	 = $this->AcctDepositoAccount_model->getAcctSavingsAccountLastBalance($val['savings_account_id_supervisor']);
					
					// $savings_account_last_balance_supervisor= implode(" ",$data_last_balance_supervisor);
					$data_savings_account_supervisor = array(
						'savings_id'						=> $data_supervisor['savings_id'],
						'savings_account_id'				=> $data_supervisor['savings_account_id'],
						'member_id'							=> $data_supervisor['member_id'],
						'office_id'                         => $data_supervisor['office_id'],
						'savings_account_last_balance'		=> $data_supervisor['savings_account_last_balance'],
					);

					$data_commission_supervisor = array(
						'branch_id'							=> $val['branch_id'],
						'savings_transfer_mutation_date'	=> date('Y-m-d'),
						'savings_transfer_mutation_amount'	=> $val['commission_on_hold_supervisor'] + $val['commission_disbursed_supervisor'],
						'operated_name'						=> 'SYS',
						'created_id'						=> $val['created_id'],
						'created_on'						=> date('Y-m-d H:i:s'),
					);

					if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_commission_supervisor)) {
						$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_commission_supervisor['created_on']);

						$data_transfer_commission_supervisor= array(
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $data_savings_account_supervisor['savings_account_id'],
							'savings_id'								=> $data_savings_account_supervisor['savings_id'],
							'member_id'									=> $data_savings_account_supervisor['member_id'],
							'branch_id'									=> $val['branch_id'],
							'mutation_id'								=> $preferencecompany['deposito_basil_id'],
							'savings_transfer_mutation_to_amount'		=> $data_commission_supervisor['savings_transfer_mutation_amount'],
							'savings_account_last_balance'				=> $data_savings_account_supervisor['savings_account_last_balance'],
						);

						if ($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_commission_supervisor)) {
							$this->AcctDepositoAccount_model->insertAcctJournalVoucher($data_journal);

							$account_id = $data_supervisor['account_id'];

							$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);

							// $journal_voucher_credit_amount = $commissiononhold['commission_on_hold_amount'];
							$data_credit_supervisor = array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
								'journal_voucher_amount'		=> ABS($data_transfer_commission_supervisor['savings_transfer_mutation_to_amount']),
								'journal_voucher_credit_amount'	=> ABS($data_transfer_commission_supervisor['savings_transfer_mutation_to_amount']),
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $token . $account_id . 'spv',
								'created_id' 					=> $val['created_id'],
							);
										
							$data= array(
								'savings_account_id' 				=> $data_supervisor['savings_account_id'],
								'savings_account_blockir_amount'	=> $data_supervisor['savings_account_blockir_amount'] + $val['commission_on_hold_supervisor'],
								'savings_account_blockir_status'	=> 1,
							);
							$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

							$journal_voucher_item_token = $this->AcctDepositoAccount_model->getJournalVoucherItemToken($data_credit_supervisor['journal_voucher_item_token']);

							if ($journal_voucher_item_token->num_rows() == 0) {
								$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit_supervisor);
							}
						}
					}
				}

				$data_commission = array(
					'commission_id' 				=> $val['commission_id'],
					'commission_disbursed_status'	=> 1,
				);
				$this->AcctDepositoAccount_model->updateAcctCommission($data_commission);
			}
		}
	}

	public function cronJobUpdateCommission(){
		$auth = $this->session->userdata('auth');
		$deposito_account_id = $this->uri->segment(3);
		$preferencecompany = $this->AcctDepositoAccount_model->getPreferenceCompany();
		$acctcommission = $this->AcctDepositoAccount_model->getAcctCommissionUpdate();

		foreach ($acctcommission as $val) {
			if($val['commission_on_hold_status'] == 0){

				$data_agent = $this->AcctDepositoAccount_model->getAcctSavingsAccountLastBalance($val['savings_account_id_agent']);
				$data= array(
					'savings_account_id' 				=> $data_agent['savings_account_id'],
					'savings_account_blockir_amount'	=> $data_agent['savings_account_blockir_amount'] - $val['commission_on_hold_agent'],
					// 'savings_account_blockir_status'	=> 0,
				);
				$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

				if($data['savings_account_blockir_amount'] == 0){
					$data= array(
						'savings_account_blockir_status'	=> 0,
					);
				}
				$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

				$data_supervisor = $this->AcctDepositoAccount_model->getAcctSavingsAccountLastBalance($val['savings_account_id_supervisor']);
				$data= array(
					'savings_account_id' 				=> $data_supervisor['savings_account_id'],
					'savings_account_blockir_amount'	=> $data_supervisor['savings_account_blockir_amount'] - $val['commission_on_hold_supervisor'],
				);
				$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

				if($data['savings_account_blockir_amount'] == 0){
					$data= array(
						'savings_account_blockir_status'	=> 0,
					);
				}
				$this->AcctDepositoAccount_model->updateAcctSavingsAccount($data);

				$data_commission = array(
					'commission_id' 				=> $val['commission_id'],
					'commission_on_hold_status'		=> 1,
				);
				$this->AcctDepositoAccount_model->updateAcctCommission($data_commission);
			}	
		}
	}
	
	public function testCron(){
			$data = array(
			    'branch_id'     => 2,
			    'store_code'    => "V",
			    'store_name'    => "TES CRON",
			    'store_address'  => "TES CRON",
		    );

            $this->AcctDepositoAccount_model->insertTestCron($data);
	}
}