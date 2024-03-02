<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Commision extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('Connection_model');
		$this->load->model('MainPage_model');
		$this->load->model('CoreCommision_model');
		$this->load->model('CoreOffice_model');
		$this->load->model('Library_model');
		$this->load->helper('sistem');
		$this->load->helper('url');
		$this->load->database('default');
		$this->load->library('configuration');
		$this->load->library('fungsi');
		$this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
	}

	public function index(){
		$unique = $this->session->userdata('unique');

		$export_master_data_id 			= $this->Library_model->getIDMenu('deposito-account/get-master-data-list');
		$export_master_data_id_mapping 	= $this->Library_model->getIDMenuOnSystemMapping($export_master_data_id);

		if ($export_master_data_id_mapping == 1) {
			$export_id = 1;
		} else {
			$export_id = 0;
		}

		$this->session->unset_userdata('acctdepositoaccounttoken-' . $unique['unique']);
		$this->session->unset_userdata('member_id');

		$data['main_view']['corecommision']		= $this->CoreCommision_model->getDataCommision();
		$data['main_view']['export_id']			= $export_id;
		$data['main_view']['content']			= 'Commision/ListCommision_view';

		// echo json_encode($data);
		// exit;
		$this->load->view('MainPage_view', $data);
	}

	public function filter(){
		$data = array(
			// "start_date" 	=> tgltodb($this->input->post('start_date',true)),
			// "end_date" 		=> tgltodb($this->input->post('end_date',true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-acctdepositoaccount', $data);
		redirect('deposito-account');
	}

	public function function_elements_add(){
		$unique 	= $this->session->userdata('unique');
		$name 		= $this->input->post('name', true);
		$value 		= $this->input->post('value', true);
		$sessions	= $this->session->userdata('addacctcommision-' . $unique['unique']);
		$sessions[$name] = $value;
		$this->session->set_userdata('addacctcommision-' . $unique['unique'], $sessions);
	}

	public function reset_data(){
		$unique 	= $this->session->userdata('unique');
		$sessions	= $this->session->unset_userdata('addacctcommision-' . $unique['unique']);
		$this->session->unset_userdata('core_commision_id');
		redirect('commision/add');
	}

	public function reset_close(){
		$member_id = $this->uri->segment(3);
		$unique 	= $this->session->userdata('unique');
		$this->session->unset_userdata('addacctdepositoaccount-' . $unique['unique']);
		redirect('deposito-account/add-closed/' . $member_id);
	}

	public function reset_search(){
		$this->session->unset_userdata('filter-acctdepositoaccount');
		redirect('deposito-account');
	}

	public function filtermasterdata(){
		$data = array(
			"start_date" 	=> tgltodb($this->input->post('start_date', true)),
			"end_date" 		=> tgltodb($this->input->post('end_date', true)),
			"deposito_id"	=> $this->input->post('deposito_id', true),
			"branch_id"		=> $this->input->post('branch_id', true),
		);

		$this->session->set_userdata('filter-masterdataacctdepositoaccount', $data);
		redirect('deposito-account/get-master');
	}


	public function addAcctCommision(){
		$commisiontype		= array(
			1 => 'Komisi Agent',
			2 => 'komisi Supervisor'
		);

		$today											= date('Y-m-d');
		$data['main_view']['commisiontype']				= $commisiontype;
		$data['main_view']['content']					= 'Commision/FormAddAcctCommision_view';
		// echo json_encode($data);
		// exit;
		$this->load->view('MainPage_view', $data);
	}

	public function processAddCommision(){
		$auth = $this->session->userdata('auth');
		$data = array(
			'commision_code'									=> $this->input->post('commision_code', true),
			'commision_name'									=> $this->input->post('commision_name', true),
			'commision_type'									=> $this->input->post('commision_type', true),
			'commision_percentage'								=> $this->input->post('commision_percentage', true),
			'commision_period'									=> $this->input->post('commision_period', true),
			'created_id'										=> $auth['user_id'],
			'created_on'										=> date('Y-m-d H:i:s'),
		);

		// echo json_encode($data);
		// exit;

		$this->form_validation->set_rules('commision_code', 'kode Komisi', 'required');
		$this->form_validation->set_rules('commision_name', 'Nama Komisi', 'required');
		$this->form_validation->set_rules('commision_type', 'Jenis Komisi', 'required');
		$this->form_validation->set_rules('commision_percentage', 'Persentase Komisi', 'required');
		$this->form_validation->set_rules('commision_period', 'Jangka Waktu', 'required');

		// echo json_encode($this->form_validation->run()) ;
		// exit;

		if ($this->form_validation->run() == true) {
				if ($this->CoreCommision_model->insertCommision($data)) {
					
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Komisi Sukses
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('commision/add');

				} else {
					$this->session->set_userdata('addacctcommision', $data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Komisi gagal
								</div> ";
					$this->session->set_userdata('message', $msg);
					redirect('commision/add');
				}
		
		} else {
			$this->session->set_userdata('addacctcommision', $data);
			$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			$this->session->set_userdata('message', $msg);
			redirect('commision/add');
		}
	}

	public function editAcctCommision(){
		$commisiontype		= array(
			1 => 'Komisi Agent',
			2 => 'komisi Supervisor'
		);

		$today											= date('Y-m-d');
		$data['main_view']['commisiontype']				= $commisiontype;
		$data['main_view']['acctcommision']				= $this->CoreCommision_model->getCommision_Detail($this->uri->segment(3));
		$data['main_view']['content']					= 'Commision/FormEditAcctCommision_view';
		
		$this->load->view('MainPage_view', $data);
	}

	public function processEditAcctCommission(){
		$auth = $this->session->userdata('auth');

		$data = array(
			'core_commision_id'									=> $this->input->post('core_commision_id', true),
			'commision_code'									=> $this->input->post('commision_code', true),
			'commision_name'									=> $this->input->post('commision_name', true),
			'commision_type'									=> $this->input->post('commision_type', true),
			'commision_percentage'								=> $this->input->post('commision_percentage', true),
			'commision_period'									=> $this->input->post('commision_period', true),
			'created_id'										=> $auth['user_id'],
			'last_update'										=> date('Y-m-d H:i:s'),
		);

		// echo json_encode($data);
		// exit;

		if ($this->CoreCommision_model->updateCommision($data)) {
			$this->fungsi->set_log($auth['user_id'], $auth['username'], '1005', 'Application.Commision.updateAcctCommission', $auth['user_id'], 'Edit Acct Commission');
			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Edit Komisi Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('commision');
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Edit Komisi Gagal
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('commision');
		}
	}

	public function deleteAcctCommision(){
		$auth = $this->session->userdata('auth');

		if ($this->CoreCommision_model->deleteCommision($this->uri->segment(3))) {
			$this->fungsi->set_log($auth['user_id'], $auth['username'], '1005', 'Application.Commision.deleteAcctCommission', $auth['user_id'], 'Delete Acct Commission');
			$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Komisi Berhasil
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('commision');
		} else {
			$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Komisi Gagal
						</div> ";
			$this->session->set_userdata('message', $msg);
			redirect('commision');
		}
	}
}