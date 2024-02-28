<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctKloterMember_model extends CI_Model {
		var $table = "acct_kloter_member";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 

		public function getCoreMember(){
			$this->db->select('core_member.member_id, core_member.member_name, core_member.member_address, core_member.member_no, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_mother');
			$this->db->from('core_member');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			if(!empty($city_id)){
			$this->db->where('core_member.data_state', 0);
			}

			if(!empty($kecamatan_id)){
				$this->db->where('core_member.kecamatan_id', $kecamatan_id);
			}
			$this->db->where('core_member.data_state', 0);
			return $this->db->get()->result_array();
		}
		public function getCoreCity(){
			$this->db->select('city_id, city_name');
			$this->db->from('core_city');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}
		public function getCoreKecamatan($city_id){
			$this->db->select('core_kecamatan.kecamatan_id, core_kecamatan.kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('core_kecamatan.city_id', $city_id);
			$this->db->where('core_kecamatan.data_state', '0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		
		public function updateAcctKloter($data){
			$this->db->where("kloter_id",$data['kloter_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctKloter($kloter_id){
			$this->db->where("kloter_id",$kloter_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getAcctKloter_Last($created_id){
			$this->db->select('acct_kloter.kloter_id, acct_kloter.kloter_name, acct_kloter.kloter_quota');
			$this->db->from('acct_kloter');
			$this->db->where('acct_kloter.created_id', $created_id);
			$this->db->limit(1);
			$this->db->order_by('acct_kloter.kloter_id','DESC');
			$result = $this->db->get()->row_array();
			return $result;
		}
	}
?>