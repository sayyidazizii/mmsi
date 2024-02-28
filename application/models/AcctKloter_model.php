<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctKloter_model extends CI_Model {
		var $table = "acct_kloter";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getAcctKloter(){
			$this->db->select('acct_kloter.*, core_branch.branch_id');
			$this->db->from('acct_kloter');
			$this->db->join('core_branch', 'acct_kloter.branch_id = core_branch.branch_id');
			$this->db->where('acct_kloter.data_state', 0);
			$this->db->order_by('acct_kloter.kloter_name', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctKloter_Detail($kloter_id){
			$this->db->select('*');
			$this->db->from('acct_kloter');
			$this->db->where('acct_kloter.kloter_id', $kloter_id);
			return $this->db->get()->row_array();
		}

		public function getKloterQuota($kloter_id){
			$this->db->select('kloter_quota');
			$this->db->from('acct_kloter');
			$this->db->where('kloter_id', $kloter_id);
			$result = $this->db->get()->row_array();
			return $result['kloter_quota'];
		}

		public function insertAcctKloter($data){
			return $query = $this->db->insert('acct_kloter',$data);
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

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function closingAcctKloter($data){
			$this->db->where("kloter_id", $data['kloter_id']);
			$query = $this->db->update($this->table, $data);
			if ($query) {
				return true;
			} else {
				return false;
			}
		}

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)) {
				return true;
			} else {
				return false;
			}
		}

		public function getJournalVoucherID($created_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.created_id', $created_id);
			$this->db->order_by('acct_journal_voucher.journal_voucher_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_id'];
		}

		public function getJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
		}

		public function insertAcctJournalVoucherItem($data){
			if ($this->db->insert('acct_journal_voucher_item', $data)) {
				return true;
			} else {
				return false;
			}
		}
		
		public function getMemberParticipate(){
		$hasil = $this->db->query("
						SELECT core_member.member_id, 
						CONCAT(core_member.member_no,' - ', core_member.member_name) as member_no 
						FROM core_member
						WHERE core_member.data_state='0'");
		return $hasil->result_array();
		}

		public function getMemberParticipate_Detail($kloter_id){
			$this->db->select('acct_kloter_member.*, core_branch.branch_id, core_member.member_id, core_member.member_name, core_member.member_id, core_member.member_no, core_member.member_address');
			$this->db->from('acct_kloter_member');
			$this->db->join('core_branch', 'acct_kloter_member.branch_id = core_branch.branch_id');
			$this->db->join('core_member', 'acct_kloter_member.member_id = core_member.member_id');
			$this->db->order_by('core_member.member_name', 'ASC');
			$this->db->where('acct_kloter_member.data_state', 0);
			$this->db->where('acct_kloter_member.kloter_id', $kloter_id);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function insertMemberParticipate($data){
			return $query = $this->db->insert('acct_kloter_member',$data);
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

		public function deleteMemberParticipate($kloter_member_id){
			$this->db->where("kloter_member_id",$kloter_member_id);
			$query = $this->db->update('acct_kloter_member', array('data_state'=>1));
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

		public function getAcctKloterPoint($kloter_id){
			$this->db->select('kloter_point');
			$this->db->from('acct_kloter');
			$this->db->where('kloter_id', $kloter_id);
			$this->db->where('data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['kloter_point'];
		}

		public function getSystemPointSetting(){
			$this->db->select('*');
			$this->db->from('system_point_setting');
			$this->db->where('data_state', 0);
			return $this->db->get()->row_array();
		}
	
		public function insertSystemPoint($data){
			$query = $this->db->insert('system_point', $data);
			if ($query) {
				return true;
			} else {
				return false;
			}
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							FROM acct_account
							WHERE acct_account.data_state='0'");
			return $hasil->result_array();
		}

		public function getCoreMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}

		public function getAccountID($deposito_id){
			$this->db->select('acct_deposito.account_id');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getAcctAccountName($account_id){
			$this->db->select('CONCAT(acct_account.account_code, " - ", acct_account.account_name) as account_name');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}
	}
