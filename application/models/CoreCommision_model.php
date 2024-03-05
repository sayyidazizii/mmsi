<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreCommision_model extends CI_Model {
		var $table = "core_commision";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCommision(){
			$this->db->select('*');
			$this->db->from('core_commision');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		//type agent
		public function getDataCommisionAgent(){
			$this->db->select('*');
			$this->db->from('core_commision');
			$this->db->where('commision_type', 1);
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		//type spv
		public function getDataCommisionSpv(){
			$this->db->select('*');
			$this->db->from('core_commision');
			$this->db->where('commision_type', 2);
			$this->db->where('data_state', 0);
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

		public function getBranchName($branch_parent){
			$this->db->select('core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_parent);
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}
		
		public function insertCommision($data){
			$query = $this->db->insert('core_commision',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCommision_Detail($core_commisison_id){
			$this->db->select('*');
			$this->db->from('core_commision');
			$this->db->where('core_commision_id', $core_commisison_id);
			return $this->db->get()->row_array();
		}

		public function getCommision_percentage($core_commision_id){
			$this->db->select('core_commision_id,commision_percentage,commision_period');
			$this->db->from('core_commision');
			$this->db->where('core_commision_id', $core_commision_id);
			return $this->db->get()->row_array();
		}
		
		public function updateCommision($data){
			$this->db->where("core_commision_id",$data['core_commision_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteCommision($core_commision_id){
			$this->db->where("core_commision_id",$core_commision_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	
	}
?>