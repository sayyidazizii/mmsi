<script>
	base_url = '<?php echo base_url(); ?>';
	mappia = "	<?php
					$site_url = 'commision/add/';
					echo site_url($site_url);
					?>";

	function function_elements_add(name, value) {
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('commision/elements-add'); ?>",
			data: {
				'name': name,
				'value': value
			},
			success: function(msg) {}
		});
	}

	function function_state_add(value) {
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('deposito/state-add'); ?>",
			data: {
				'value': value
			},
			success: function(msg) {}
		});
	}

	function reset_data() {
		document.location = "<?php echo base_url(); ?>Commision/reset-data";
	}

</script>
<?php echo form_open('commision/process-edit', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
$sesi 	= $this->session->userdata('unique');

if (empty($acctcommision['commision_code'])) {
	$acctcommision['commision_code'] 					= '';
}

if (empty($acctcommision['commision_name'])) {
	$acctcommision['commision_name'] 					= '';
}

if (empty($acctcommision['commision_period'])) {
	$acctcommision['commision_period'] 				= '';
}

if (empty($acctcommision['commision_type'])) {
	$acctcommision['commision_type'] 					= 0;
}

if (empty($acctcommision['account_basil_id'])) {
	$acctcommision['account_basil_id'] 				= 0;
}

if (empty($acctcommision['commision_percentage'])) {
	$acctcommision['commision_percentage'] 		= '';
}

?>

<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url(); ?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>commision">
				Daftar Kode Komisi
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>commision/add">
				Tambah Kode Komisi
			</a>
		</li>
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Kode Komisi
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
						Form Tambah
					</div>
					<div class="actions">
						<a href="<?php echo base_url(); ?>commision" class="btn btn-default btn-sm">
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
									<input type="hidden" class="form-control" name="core_commision_id" id="core_commision_id" autocomplete="off" value="<?php echo set_value('core_commision_id', $acctcommision['core_commision_id']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<input type="text" class="form-control" name="commision_code" id="commision_code" autocomplete="off" value="<?php echo set_value('commision_code', $acctcommision['commision_code']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Kode Komisi<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="commision_name" id="commision_name" value="<?php echo set_value('commision_name', $acctcommision['commision_name']); ?>" onChange="function_elements_add(this.name, this.value);">
									<label class="control-label">Nama Komisi
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<label class="control-label">Jenis Komisi<span class="required">*</span></label>
									<?php echo form_dropdown('commision_type', $commisiontype, set_value('commision_type', $acctcommision['commision_type']), 'id="commision_type" class="easyui-combobox" style="width:100%"'); ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="commision_percentage" id="commision_percentage" autocomplete="off" value="<?php echo set_value('commision_percentage', $acctcommision['commision_percentage']); ?>" onChange="function_elements_add(this.name, this.value);"  placeholder="%"/>
									<label class="control-label">Bunga<span class="required">*</span></label>
								</div>
							</div>
                            <div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="commision_period" id="commision_period" autocomplete="off" value="<?php echo set_value('commision_period', $acctcommision['commision_period']); ?>" onChange="function_elements_add(this.name, this.value);"/>
									<label class="control-label">Jangka Waktu<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" style="padding-top: 10px; text-align:right;">
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
