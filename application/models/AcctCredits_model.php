<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCredits_model extends CI_Model {
		var $table = "acct_credits";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getDataAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_code, acct_credits.credits_name, acct_credits.receivable_account_id, acct_credits.income_account_id, acct_credits.credits_fine, acct_credits.credits_point');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							FROM acct_account
							WHERE acct_account.data_state='0'");
			return $hasil->result_array();
		}

		public function getAccountCode($account_id){
			$this->db->select('account_code');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_code'];
		}

		public function getAccountName($account_id){
			$this->db->select('account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}

		public function insertAcctAccount($data){
			return $query = $this->db->insert('acct_account',$data);
		}
		
		public function insertAcctCredits($data){
			return $query = $this->db->insert('acct_credits',$data);
		}
		
		public function getAcctCredits_Detail($credits_id){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_code, acct_credits.credits_name, acct_credits.receivable_account_id, acct_credits.income_account_id, acct_credits.credits_fine, acct_credits.credits_point');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			$this->db->where('acct_credits.credits_id', $credits_id);
			return $this->db->get()->row_array();
		}
		
		public function updateAcctCredits($data){
			$this->db->where("credits_id",$data['credits_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctCredits($credits_id){
			$this->db->where("credits_id",$credits_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>