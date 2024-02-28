<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class SystemPoint_model extends CI_Model {
		var $table = "system_end_of_days";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getSystemPoint($start_date, $end_date, $branch_id){
			$this->db->select('SUM(system_point.point_amount) as point_total, system_point.point_id, core_member.member_id, core_member.member_no, core_member.member_name');
			$this->db->from('system_point');
			$this->db->join('core_member','system_point.member_id = core_member.member_id');
			$this->db->where('system_point.point_date >=', $start_date);		
			$this->db->where('system_point.point_date <=', $end_date);		
			$this->db->where('system_point.branch_id', $branch_id);		
			$this->db->group_by('system_point.member_id');		
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getSystemPoint_Detail($start_date, $end_date, $branch_id, $member_id){
			$this->db->select('system_point.*, core_member.member_no, core_member.member_name');
			$this->db->from('system_point');
			$this->db->join('core_member','system_point.member_id = core_member.member_id');
			$this->db->where('system_point.point_date >=', $start_date);		
			$this->db->where('system_point.point_date <=', $end_date);		
			$this->db->where('system_point.branch_id', $branch_id);		
			$this->db->where('system_point.member_id', $member_id);			
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_no, core_member.member_name');
			$this->db->from('core_member');
			$this->db->where('core_member.member_id', $member_id);		
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getSystemPointSetting(){
			$this->db->select('system_point_setting.*');
			$this->db->from('system_point_setting');
			$this->db->where('system_point_setting.data_state', 0);		
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getSystemUserName($user_id){
			$this->db->select('system_user.username');
			$this->db->from('system_user');
			$this->db->where('system_user.user_id', $user_id);		
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSystemPointDate(){
			$this->db->select('system_end_of_days.*');
			$this->db->from('system_end_of_days');
			$this->db->order_by('system_end_of_days.created_at','desc');
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function insertSystemPointDate($data){
			if($this->db->insert('system_end_of_days', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function updateSystemPointSetting($data){
			$this->db->where('point_setting_id',$data['point_setting_id']);
			$query = $this->db->update('system_point_setting', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>