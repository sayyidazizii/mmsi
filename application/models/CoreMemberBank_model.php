<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreMemberBank_model extends CI_Model {
		var $table = "core_member_bank";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getMemberBank($member_id){
			$this->db->select('*');
			$this->db->join('acct_bank_account', 'acct_bank_account.bank_account_id = core_member_bank.bank_account_id');
			$this->db->from('core_member_bank');
			$this->db->where('core_member_bank.member_id', $member_id);
			$this->db->where('core_member_bank.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function insertBank($data){
			$query = $this->db->insert('core_member_bank',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getAcctBankAccount_Detail($member_bank_id){
			$this->db->select('core_member_bank.member_bank_id,core_member_bank.member_id,core_member_bank.bank_account_id,core_member_bank.bank_account_number,core_member_bank.data_state,acct_bank_account.bank_account_id,acct_bank_account.account_id,acct_bank_account.bank_account_code,acct_bank_account.bank_account_name');
			$this->db->from('core_member_bank');
			$this->db->join('acct_bank_account', 'acct_bank_account.bank_account_id = core_member_bank.bank_account_id');
			$this->db->where('core_member_bank.data_state', 0);
			$this->db->where('core_member_bank.member_bank_id', $member_bank_id);
			return $this->db->get()->row_array();
		}
		
		public function updateBank($data){
			$this->db->where("member_bank_id",$data['member_bank_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteBank($member_bank_id){
			$this->db->where("member_bank_id",$member_bank_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	
	}
?>