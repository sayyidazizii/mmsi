<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class Commision_Report_model extends CI_Model {
		var $table = "acct_commision";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
			$auth 	=	$this->session->userdata('auth'); 


			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctCommission($deposito_account_id, $start_date, $end_date, $branch_id){
			$this->db->select('*');
			$this->db->from('acct_commission');
			$this->db->join('acct_deposito_account', 'acct_deposito_account.deposito_account_id = acct_commission.deposito_account_id');
			$this->db->join('acct_savings_account', 'acct_savings_account.savings_account_id = acct_commission.savings_account_id');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_commission.data_state', 0);
			if(!empty($deposito_account_id)){
				$this->db->where('acct_commission.deposito_account_id', $deposito_account_id);
			}
			$this->db->where('acct_commission.commission_date >=', $start_date);
			$this->db->where('acct_commission.commission_date <=', $end_date);
			if(!empty($branch_id)){
				$this->db->where('acct_commission.branch_id', $branch_id);
			}
			return $this->db->get()->result_array();  

		}


		public function getAccountDeposito(){
			$this->db->select('acct_deposito_account.deposito_account_no,acct_deposito_account.deposito_account_id');
			$this->db->from('acct_deposito_account');
			// $this->db->where('acct_deposito_account.branch_id',$auth['branch_id']);
			$this->db->where('acct_deposito_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

	
	}
?>