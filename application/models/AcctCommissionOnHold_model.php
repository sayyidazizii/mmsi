<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCommissionOnHold_model extends CI_Model {
		var $table = "acct_commission";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function getAcctCommissionOnHold(){
			$this->db->select('acct_commission.*, acct_deposito_account.deposito_account_no, acct_deposito_account.savings_account_id_agent, acct_deposito_account.savings_account_id_supervisor, acct_deposito_account.deposito_account_id, core_member.member_name, acct_savings_account.savings_account_no');
			$this->db->from('acct_commission');
			$this->db->join('acct_deposito_account', 'acct_commission.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_savings_account', 'acct_commission.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->where('acct_commission.data_state', 0);
			$this->db->where('acct_commission.commission_disbursed_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount($savings_account_id){
		$this->db->select('acct_savings_account.savings_account_no, core_member.member_name');
		$this->db->from('acct_savings_account');
		$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
		$this->db->where('acct_savings_account.data_state', 0);
		// $this->db->where('acct_savings_account.savings_id', 30);
		$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
		return $this->db->get()->row_array();
		}
		
	}
?>