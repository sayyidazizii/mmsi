<script>
	base_url = '<?php echo base_url(); ?>';
	mappia = "	<?php
					$site_url = 'deposito/add/';
					echo site_url($site_url);
					?>";

	function function_elements_add(name, value) {
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('deposito/elements-add'); ?>",
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
<?php echo form_open('member/list-bank/save', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
$sesi 	= $this->session->userdata('unique');

if (empty($data['bank_account_id'])) {
	$data['bank_account_id'] 					= 0;
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
			<a href="<?php echo base_url(); ?>member">
				Daftar Member
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>member/list-bank/<?php echo $member_id ?>">
				Daftar Bank Member
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="#">
				Tambah Bank Member
			</a>
		</li>
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Bank Member
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
						<a href="<?php echo base_url(); ?>member/list-bank/<?php echo $member_id ?>" class="btn btn-default btn-sm">
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
									<label class="control-label">Bank<span class="required">*</span></label>
									<?php echo form_dropdown('bank_account_id', $acctbank, set_value('bank_account_id', $data['bank_account_id']), 'id="bank_account_id" class="easyui-combobox" style="width:100%"'); ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
                                    <input type="hidden" class="form-control" name="member_id" id="member_id" autocomplete="off" value="<?php echo set_value('member_id', $member_id); ?>" onChange="function_elements_add(this.name, this.value);"/>
                                    <input type="text" class="form-control" name="bank_account_number" id="bank_account_number" autocomplete="off" value="<?php echo set_value('bank_account_number', $data['bank_account_number']); ?>" onChange="function_elements_add(this.name, this.value);"  placeholder="nomor rekening pribadi"/>
									<label class="control-label">No.Rekening<span class="required">*</span></label>
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
