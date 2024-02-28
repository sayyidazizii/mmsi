<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class SystemPoint extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('SystemPoint_model');
			$this->load->model('ValidationProcess_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

        public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$sesi	= $this->session->userdata('filter-systempoint');

			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['branch_id']		= $auth['branch_id'] ;
			}else{
				if(!$sesi['branch_id']){
					$sesi['branch_id']		= $auth['branch_id'];	
				}
			}

			$systempoint =  $this->SystemPoint_model->getSystemPoint($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']);

			$data['main_view']['corebranch']			= create_double($this->SystemPoint_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['systempoint']			= $systempoint;	
			$data['main_view']['content']				= 'SystemPoint/ListSystemPoint_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-systempoint',$data);
			redirect('system-point');
		}

		public function reset_search(){
			$this->session->unset_userdata('filter-systempoint');
			redirect('system-point');
		}

        public function detailSystemPoint(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-systempoint');
			$member_id	= $this->uri->segment(3);

			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['branch_id']		= $auth['branch_id'] ;
			}else{
				if(!$sesi['branch_id']){
					$sesi['branch_id']		= $auth['branch_id'];	
				}
			}

			$systempoint 	=  $this->SystemPoint_model->getSystemPoint_Detail($sesi['start_date'], $sesi['end_date'], $sesi['branch_id'], $member_id);
			$coremember 	=  $this->SystemPoint_model->getCoreMember_Detail($member_id);

			$data['main_view']['sesi']					= $sesi;	
			$data['main_view']['coremember']			= $coremember;	
			$data['main_view']['systempoint']			= $systempoint;	
			$data['main_view']['content']				= 'SystemPoint/FormDetailSystemPoint_view';
			$this->load->view('MainPage_view',$data);
		}

        public function settingSystemPoint(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$systempointsetting 	=  $this->SystemPoint_model->getSystemPointSetting();

			$data['main_view']['systempointsetting']	= $systempointsetting;	
			$data['main_view']['content']				= 'SystemPoint/FormSettingSystemPoint_view';
			$this->load->view('MainPage_view',$data);
		}

        public function processSettingSystemPoint(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'point_setting_id'							=> $this->input->post('point_setting_id', true),
				'point_setting_principal_savings_amount'	=> $this->input->post('point_setting_principal_savings_amount', true),
				'point_setting_mandatory_savings_amount'	=> $this->input->post('point_setting_mandatory_savings_amount', true),
				'point_setting_special_savings_amount'		=> $this->input->post('point_setting_special_savings_amount', true),
				'point_setting_savings_mutation_amount'		=> $this->input->post('point_setting_savings_mutation_amount', true),
				'point_setting_credits_payment_amount'		=> $this->input->post('point_setting_credits_payment_amount', true),
			);

			$this->form_validation->set_rules('point_setting_principal_savings_amount', 'Simpanan Pokok', 'required');
			$this->form_validation->set_rules('point_setting_mandatory_savings_amount', 'Simpanan Wajib', 'required');
			$this->form_validation->set_rules('point_setting_special_savings_amount', 'Simpanan Khusus', 'required');
			$this->form_validation->set_rules('point_setting_savings_mutation_amount', 'Mutasi Tabungan', 'required');
			$this->form_validation->set_rules('point_setting_credits_payment_amount', 'Angsuran Pinjaman', 'required');

			if ($this->form_validation->run() == true) {
				if($this->SystemPoint_model->updateSystemPointSetting($data)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Setting Point Anggota Berhasil
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('system-point');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Setting Point Anggota Tidak Berhasil
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('system-point/setting');
				}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message', $msg);
				redirect('system-point/setting');
			}
		}
    }
?>