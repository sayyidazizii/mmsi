<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("kloter_name").value 			= "<?php echo $acctkloter['kloter_name'] ?>";
	}

</script>
<?php echo form_open('kloter/process-edit',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
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
			<a href="<?php echo base_url();?>kloter">
				Daftar Kloter
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>kloter/edit/"<?php $this->uri->segment(3); ?>>
				Edit Data Kloter 
			</a>
		</li>
	</ul>
</div>
<h3 class="page-title">
	Form Edit Data Kloter 
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
						<a href="<?php echo base_url();?>kloter" class="btn btn-default btn-sm">
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
						<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_name" id="kloter_name" autocomplete="off" value="<?php echo set_value('kloter_name',$acctkloter['kloter_name']);?>"/>
									<label class="control-label">Nama Kloter<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_quota" id="kloter_quota" autocomplete="off" value="<?php echo set_value('kloter_quota',$acctkloter['kloter_quota']);?>"/>
									<label class="control-label">Kuota Kloter<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_period" id="kloter_period" autocomplete="off" value="<?php echo set_value('kloter_period',$acctkloter['kloter_period']);?>"/>
									<label class="control-label">Jangka Waktu (Bulan)<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_amount" id="kloter_amount" autocomplete="off" value="<?php echo set_value('kloter_amount',$acctkloter['kloter_amount']);?>"/>
									<label class="control-label">Nominal Partisipasi (Rp)<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_prize_amount" id="kloter_prize_amount" autocomplete="off" value="<?php echo set_value('kloter_prize_amount',$acctkloter['kloter_prize_amount']);?>"/>
									<label class="control-label">Total Hadiah (Rp)<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_prize" id="kloter_prize" autocomplete="off" value="<?php echo set_value('kloter_prize',$acctkloter['kloter_prize']);?>"/>
									<label class="control-label">Hadiah<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="kloter_point" id="kloter_point" autocomplete="off" value="<?php echo set_value('kloter_point',$acctkloter['kloter_point']);?>"/>
									<label class="control-label">Poin<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('account_kloter_id', $acctaccount, set_value('account_id', $acctkloter['account_kloter_id']), 'id="account_kloter_id" class="form-control select2me"');
									?>

									<label class="control-label">Pendapatan Kloter</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('account_prize_id', $acctaccount, set_value('account_id', $acctkloter['account_prize_id']), 'id="account_prize_id" class="form-control select2me"');
									?>

									<label class="control-label">Biaya Hadiah</label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan </i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="kloter_id" id="kloter_id" placeholder="id" value="<?php echo set_value('kloter_id',$acctkloter['kloter_id']);?>"/>
<?php echo form_close(); ?>