<?php
class CronAcctDeposito_model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->CI = get_instance();

		// $this->db_api 	= $this->load->database('api', true);
	}

	public function getAcctDepositoAccount($today, $branch_id){
		$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_id, acct_deposito_account.savings_account_id, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_account_date, acct_deposito.deposito_name, acct_deposito.deposito_interest_period, acct_deposito.deposito_interest_rate, acct_savings_account.savings_id, acct_savings_account.member_id, acct_savings_account.savings_account_last_balance, acct_deposito_account.deposito_account_nisbah');
		$this->db->from('acct_deposito_account');
		$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
		$this->db->join('acct_savings_account', 'acct_deposito_account.savings_account_id = acct_savings_account.savings_account_id');
		$this->db->where('acct_deposito_account.deposito_account_status', 0);
		$this->db->where('acct_deposito_account.validation', 1);
		$this->db->where('acct_deposito_account.deposito_account_due_date >=', $today);
		$this->db->where('acct_deposito_account.branch_id', $branch_id);
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function getAcctDepositoProfitSharing($today, $branch_id){
		$this->db->select('acct_deposito_profit_sharing.*, acct_deposito_account.savings_account_id, acct_savings_account.savings_id, acct_savings_account.savings_account_last_balance');
		$this->db->from('acct_deposito_profit_sharing');
		$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
		$this->db->join('acct_savings_account', 'acct_deposito_account.savings_account_id = acct_savings_account.savings_account_id');
		$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_status', 0);
		$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date', $today);
		$this->db->where('acct_deposito_profit_sharing.branch_id', $branch_id);
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function getAcctDepositoInterest($deposito_id, $today){
		$this->db->select('acct_deposito_interest.deposito_interest_percentage');
		$this->db->from('acct_deposito_interest');
		$this->db->where('acct_deposito_interest.deposito_id', $deposito_id);
		$this->db->where('acct_deposito_interest.deposito_interest_date', $today);
		$result = $this->db->get()->row_array();
		return $result['deposito_interest_percentage'];
	}

	public function getPreferenceCompany(){
		$this->db->select('*');
		$this->db->from('preference_company');
		return $this->db->get()->row_array();
	}
	
	public function getCoreBranch(){
		$this->db->select('branch_id, branch_code, branch_name');
		$this->db->from('core_branch');
		return $this->db->get()->result_array();
	}

	public function insertAcctDepositoProfitSharing($data){
		if ($this->db->insert('acct_deposito_profit_sharing', $data)){
			return true;
		}else{
			return false;
		}
	}
}