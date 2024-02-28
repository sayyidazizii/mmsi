<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("point_setting_principal_savings_amount").value 	= "<?php echo $systempointsetting['point_setting_principal_savings_amount']; ?>";
		document.getElementById("point_setting_mandatory_savings_amount").value 	= "<?php echo $systempointsetting['point_setting_mandatory_savings_amount']; ?>";
		document.getElementById("point_setting_special_savings_amount").value 		= "<?php echo $systempointsetting['point_setting_special_savings_amount']; ?>";
		document.getElementById("point_setting_savings_mutation_amount").value 		= "<?php echo $systempointsetting['point_setting_savings_mutation_amount']; ?>";
		document.getElementById("point_setting_credits_payment_amount").value 		= "<?php echo $systempointsetting['point_setting_credits_payment_amount']; ?>";
	}
</script>
<?php echo form_open('system-point/process-setting',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>system-point">
				Daftar Perkiraan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>system-point/setting">
				Setting Point Anggota
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Form Setting Point Anggota 
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
						Form Setting
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>system-point" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" id="point_setting_principal_savings_amount" name="point_setting_principal_savings_amount" autocomplete="off" value="<?php echo $systempointsetting['point_setting_principal_savings_amount']; ?>">
									<input type="hidden" class="form-control" id="point_setting_id" name="point_setting_id" autocomplete="off" value="<?php echo $systempointsetting['point_setting_id']; ?>">
									<label class="control-label">Simpanan Pokok</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="point_setting_mandatory_savings_amount" name="point_setting_mandatory_savings_amount" autocomplete="off" value="<?php echo $systempointsetting['point_setting_mandatory_savings_amount']; ?>">
									<label class="control-label">Simpanan Wajib</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="point_setting_special_savings_amount" name="point_setting_special_savings_amount" autocomplete="off" value="<?php echo $systempointsetting['point_setting_special_savings_amount']; ?>">
									<label class="control-label">Simpanan Khusus</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="point_setting_savings_mutation_amount" name="point_setting_savings_mutation_amount" autocomplete="off" value="<?php echo $systempointsetting['point_setting_savings_mutation_amount']; ?>">
									<label class="control-label">Mutasi Tabungan</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="point_setting_credits_payment_amount" name="point_setting_credits_payment_amount" autocomplete="off" value="<?php echo $systempointsetting['point_setting_credits_payment_amount']; ?>">
									<label class="control-label">Angsuran Pinjaman</label>
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
<input type="hidden" class="form-control" name="account_id" id="account_id" placeholder="id" value="<?php echo set_value('account_id',$systempointsetting['account_id']);?>"/>
<?php echo form_close(); ?>