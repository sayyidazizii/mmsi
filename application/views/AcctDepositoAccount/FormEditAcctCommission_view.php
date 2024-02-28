<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("commission_disbursed_agent").value 		= "<?php echo $acctcommission['commission_disbursed_agent']; ?>";
		document.getElementById("commission_on_hold_agent").value 			= "<?php echo $acctcommission['commission_on_hold_agent']; ?>";
		document.getElementById("commission_disbursed_supervisor").value 	= "<?php echo $acctcommission['commission_disbursed_supervisor']; ?>";
		document.getElementById("commission_on_hold_supervisor").value 		= "<?php echo $acctcommission['commission_on_hold_supervisor']; ?>";
	}
</script>
<?php echo form_open('deposito-account/process-edit-commission',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account">
				Daftar Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/commission/"<?php $this->uri->segment(3); ?>>
				Daftar Komisi
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/edit-commission/"<?php $this->uri->segment(4); ?>>
				Edit Komisi 
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Form Edit Komisi 
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>deposito-account/commission/<?php echo $this->uri->segment(3); ?>" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="hidden" class="form-control" id="deposito_account_id" name="deposito_account_id" value="<?php echo $this->uri->segment(3); ?>" readonly>
									<input type="hidden" class="form-control" id="commission_id" name="commission_id" value="<?php echo $acctcommission['commission_id']; ?>" readonly>
									<input type="text" class="form-control" id="commission_date" name="commission_date" value="<?php echo $acctcommission['commission_date']; ?>" readonly>
									<label class="control-label">Tanggal Komisi</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="commission_disbursed_agent" name="commission_disbursed_agent" value="<?php echo $acctcommission['commission_disbursed_agent']; ?>">
									<label class="control-label">Komisi Agent Cair</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="commission_on_hold_agent" name="commission_on_hold_agent" value="<?php echo $acctcommission['commission_on_hold_agent']; ?>">
									<label class="control-label">Komisi Agent Ditahan</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="commission_disbursed_supervisor" name="commission_disbursed_supervisor" value="<?php echo $acctcommission['commission_disbursed_supervisor']; ?>">
									<label class="control-label">Komisi Supervisor Cair</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="commission_on_hold_supervisor" name="commission_on_hold_supervisor" value="<?php echo $acctcommission['commission_on_hold_supervisor']; ?>">
									<label class="control-label">Komisi Supervisor Ditahan</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="bank_account_id" id="bank_account_id" placeholder="id" value="<?php echo set_value('bank_account_id',$acctbankaccount['bank_account_id']);?>"/>
<?php echo form_close(); ?>