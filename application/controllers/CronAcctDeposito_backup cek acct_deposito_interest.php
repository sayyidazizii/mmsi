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

	public function getDepositoProfitSharing($password){ //$password
		if($password != "mms!"){
			echo "access is denied";
		} else {
			$tes				= array();
			$today            	= date("Y-m-d");
			$preferencecompany 	= $this->CronAcctDeposito_model->getPreferenceCompany();
            $corebranch  		= $this->CronAcctDeposito_model->getCoreBranch();
			
			foreach($corebranch as $keyy => $vall){
				$depositoaccount  	= $this->CronAcctDeposito_model->getAcctDepositoAccount($today, $vall['branch_id']);
				foreach($depositoaccount as $key => $val){
					$interest_amount 	= 0;
					$tax_amount 	 	= 0;

					$interest_rate 		= $this->CronAcctDeposito_model->getAcctDepositoInterest($val['deposito_id'], $today);
					if(!$interest_rate){
						$interest_rate = $val['deposito_interest_rate'];
					}

					$today_date		= new DateTime(date('Y-m-d'));
					$deposito_date 	= new DateTime($val['deposito_account_date']);
					$datediff  		= $deposito_date->diff($today_date)->format('%a');

					if($val['deposito_interest_period'] == 1){
						if(date('D') == "Mon"){
							if($datediff < 7){
								$interest_amount = $datediff / 7 * $interest_rate * $val['deposito_account_amount'] / 100;
							}else{
								$interest_amount = $interest_rate * $val['deposito_account_amount'] / 100;
							}
						}
					}else if($val['deposito_interest_period'] == 2){
						if(date('d') == 1){
							if(date('Y') == date('Y', strtotime($val['deposito_account_date'])) && date('m') == date('m', strtotime($val['deposito_account_date']))){
								$interest_amount = $datediff / date('t') * $interest_rate * $val['deposito_account_amount'] / 100;
							}else{
								$interest_amount = $interest_rate * $val['deposito_account_amount'] / 100;
							}
						}
					}if($val['deposito_interest_period'] == 3){
						if(date('m') == 1 && date('d') == 1){
							if(date('Y') == date('Y', strtotime($val['deposito_account_date']))){
								$interest_amount = $datediff / 365 * $interest_rate * $val['deposito_account_amount'] / 100;
							}else{
								$interest_amount = $interest_rate * $val['deposito_account_amount'] / 100;
							}
						}
					}

					//!Journal Voucher
					if($interest_amount != 0){
						
						if($interest_amount > $preferencecompany['tax_minimum_amount']){
							$tax_amount	= $interest_amount * $preferencecompany['tax_percentage'] / 100;
						}else{
							$tax_amount 	= 0;
						}
						
						$data = array (
							'deposito_account_id'				=> $val['deposito_account_id'],
							'deposito_id'						=> $val['deposito_id'],
							'deposito_profit_sharing_date'		=> $today,
							'deposito_profit_sharing_due_date'	=> $today,
							'deposito_index_amount'				=> 0,
							'deposito_account_nisbah'			=> $interest_rate,
							'deposito_profit_sharing_amount'	=> $interest_amount,
							'deposito_daily_average_balance'	=> $interest_amount,
							'deposito_account_last_balance'		=> $val['deposito_account_nisbah']+$interest_amount,
							'deposito_profit_sharing_period'	=> date('mY'),
							'savings_account_id'				=> $val['savings_account_id'],
							'deposito_profit_sharing_status'	=> 1,
							'member_id'							=> $val['member_id'],
							'branch_id'							=> $vall['branch_id'],
						);

						if($this->CronAcctDeposito_model->insertAcctDepositoProfitSharing($data)){
							$data_update = array(
								'deposito_account_id'				=> $val['deposito_account_id'],
								'deposito_account_nisbah'			=> $val['deposito_account_nisbah']+$interest_amount,
								'deposito_process_last_date'		=> $today,
							);

							$this->AcctDepositoProfitSharingCheck_model->updateAcctDepositoAccount($data_update);
							$data_transfer = array (
								'branch_id'							=> $vall['branch_id'],
								'savings_transfer_mutation_date'	=> date('Y-m-d'),
								'savings_transfer_mutation_amount'	=> $interest_amount-$tax_amount,
								'operated_name'						=> 'SYS',
								'created_id'						=> 0,
								'created_on'						=> date('Y-m-d H:i:s'),
							);
		
							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfer)){
								$savings_transfer_mutation_id = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfer['created_on']);
		
								$data_transfer_to = array (
									'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
									'savings_account_id'						=> $data['savings_account_id'],
									'savings_id'								=> $val['savings_id'],
									'member_id'									=> $val['member_id'],
									'branch_id'									=> $vall['branch_id'],
									'mutation_id'								=> $preferencecompany['deposito_basil_id'],
									'savings_account_opening_balance'			=> $val['savings_account_last_balance'],
									'savings_transfer_mutation_to_amount'		=> $interest_amount-$tax_amount,
									'savings_account_last_balance'				=> $val['savings_account_last_balance']+$interest_amount-$tax_amount,
								);
		
								if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($data_transfer_to)){

									$acctdepositoprofitsharing_last 	= $this->AcctDepositoProfitSharingCheck_model->getAcctDepositoProfitSharing_Last($data['deposito_profit_sharing_id']);
			
									$journal_voucher_period 	= date("Ym", strtotime($data['deposito_profit_sharing_date']));
									
									$transaction_module_code 	= "BSDEP";

									$transaction_module_id 		= $this->AcctDepositoProfitSharingCheck_model->getTransactionModuleID($transaction_module_code);

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
										'created_id' 					=> 0,
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
										'journal_voucher_amount'		=> $interest_amount-$tax_amount,
										'journal_voucher_debit_amount'	=> $interest_amount-$tax_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_basil_id,	
										'account_id_status'				=> 0,
										'created_id' 					=> 0,
									);
			
									$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debet);
			
									$account_id = $this->AcctDepositoProfitSharingCheck_model->getAccountID($val['savings_id']);
			
									$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_id);
			
									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_id,
										'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
										'journal_voucher_amount'		=> $interest_amount-$tax_amount,
										'journal_voucher_credit_amount'	=> $interest_amount-$tax_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'journal_voucher_item_token'	=> $data['deposito_profit_sharing_token'].$account_id,
										'account_id_status'				=> 1,
										'created_id' 					=> 0,
										
									);
			
									$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_credit);
									
									if($tax_amount > 0){
										$account_savings_tax_id 	= $preferencecompany['account_savings_tax_id'];

										$account_id_default_status = $this->AcctDepositoProfitSharingCheck_model->getAccountIDDefaultStatus($account_savings_tax_id);

										$data_debit = array (
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_savings_tax_id,
											'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
											'journal_voucher_amount'		=> $tax_amount,
											'journal_voucher_debit_amount'	=> $tax_amount,
											'account_id_default_status'		=> $account_id_default_status,
											'journal_voucher_item_token'	=> 'PJ1'.$data['deposito_profit_sharing_token'].$account_savings_tax_id,	
											'account_id_status'				=> 0,
											'created_id' 					=> 0,
										);

										$this->AcctDepositoProfitSharingCheck_model->insertAcctJournalVoucherItem($data_debit);

										$account_id = $this->AcctDepositoAccount_model->getAccountID($data['deposito_id']);
		
										$account_id_default_status = $this->AcctDepositoAccount_model->getAccountIDDefaultStatus($account_id);
			
										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
											'journal_voucher_amount'		=> $tax_amount,
											'journal_voucher_credit_amount'	=> $tax_amount,
											'account_id_default_status'		=> $account_id_default_status,
											'journal_voucher_item_token'	=> 'PJ2'.$data['deposito_account_closed_token'].$account_id,
											'account_id_status'				=> 1,
											'created_id' 					=> 0
										);
		
										$this->AcctDepositoAccount_model->insertAcctJournalVoucherItem($data_credit);
									}
								}
							}
						}
						array_push($tes, $data);
					}
				}
			}
			print_r($tes);exit;
		} 
	}
}