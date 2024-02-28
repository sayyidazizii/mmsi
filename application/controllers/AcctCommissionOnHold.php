<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCommissionOnHold extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCommissionOnHold_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
		// $member_id 			= $this->uri->segment(3);
		// $data_member 	 = $this->AcctCommissionOnHold_model->getCoreMember_Detail($member_id);
		// $data_agent  	 = $this->AcctCommissionOnHold_model->getCoreMember_Detail($data_member['member_reference']);
		// $data_supervisor = $this->AcctCommissionOnHold_model->getCoreMember_Detail($data_agent['member_reference']);

		// $savings_account_agent  	 = $this->AcctCommissionOnHold_model->getAcctSavingsAccountAgentSPV($data_agent['member_id']);
		// $savings_account_supervisor  = $this->AcctCommissionOnHold_model->getAcctSavingsAccountAgentSPV($data_supervisor['member_id']);

			$data['main_view']['acctcommissiononhold']	= $this->AcctCommissionOnHold_model->getAcctCommissionOnHold();
			// $data['main_view']['acctcommissiononholdagent']	= $savings_account_agent;
			// $data['main_view']['acctcommissiononholdspv']	= $savings_account_supervisor;
			$data['main_view']['content']				= 'AcctCommissionOnHold/ListAcctCommissionOnHold_view';
			$this->load->view('MainPage_view',$data);
			// print_r($data['main_view']['acctcommissiononhold']); exit;
		}
		
		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctcredits-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctcredits-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctcredits-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctcredits-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctcredits-'.$unique['unique']);
			redirect('credits/add');
		}
		
	}
?>