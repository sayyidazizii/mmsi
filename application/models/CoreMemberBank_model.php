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
			$this->db->from('core_member_bank');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
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
		
		public function updateBank($member_bank_id){
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