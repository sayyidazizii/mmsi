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
		document.location = "<?php echo base_url(); ?>deposito/reset-data";
	}

	function processAddAcctAccount() {
		var account_code = $("#account_code").val();
		var account_name = $("#account_name").val();
		var account_status = $("#account_status").val();
		var account_group = $("#account_group").val();
		var account_type_id = $("#account_type_id").val();

		if (account_code == '') {
			alert('Nomor Perkiraan masih kosong');
			$('#account_code').val('');
		} else if (account_name == '') {
			alert('Nama Perkiraan masih kosong');
			$('#account_name').val('');
		} else if (account_group == '') {
			alert('Golongan Perkiraan masih kosong');
			$('#account_group').val('');
		} else {
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('deposito/process-add-account'); ?>",
				data: {
					'account_code': account_code,
					'account_name': account_name,
					'account_status': account_status,
					'account_group': account_group,
					'account_type_id': account_type_id,
				},

				success: function(msg) {
					// alert(msg);
					$('#account_code').val('');
					$('#account_name').val('');
					$('#account_group').val('');
					window.location.replace(mappia);
				}
			});
		}
	}

	// $(document).ready(function() {
	// 	$("#deposito_interest_period_days").change(function(){
			
	// 		var jatuh_tempo = document.getElementById("deposito_interest_period").value;
	// 		var hari = document.getElementById("deposito_interest_period_days").value;
	// 		var jangka_waktu = document.getElementById("deposito_period").value;

	// 		if (jatuh_tempo == 1) {
	// 			7;
	// 		}if (jatuh_tempo == 2) {
	// 			30;
	// 		}if (jatuh_tempo == 3) {
	// 			365;
	// 		}

	// 		if (jatuh_tempo == '') {
	// 			var jangka_waktu = '';
	// 		} else {
	// 			var hari = jangka_waktu * jatuh_tempo; 
	// 		}


			

	// 		document.getElementById("deposito_period").value=jangka_waktu;
	// 		document.getElementById("deposito_interest_period_days").value=hari;
	// 		document.getElementById("deposito_interest_period").value=jatuh_tempo;
	// 	});
	// });

	// $(document).ready(function() {
	// 	$("#deposito_interest_period_days").change(function(){
			
	// 		var komisi_ditahan = document.getElementById("deposito_interest_period").value;
	// 		var komisi_dicairkan = document.getElementById("deposito_period").value;

	// 		if (komisi_ditahan == '') {
	// 			var komisi_dicairkan = '';
	// 		} else {
	// 			var hari = komisi_dicairkan * komisi_ditahan; 
	// 		}
	// 		document.getElementById("deposito_commission_on_hold").value=komisi_ditahan;
	// 		document.getElementById("deposito_commission_disbursed").value=komisi_dicairkan;
	// 		console.log(komisi_ditahan);
	// 		console.log(komisi_dicairkan);
	// 	});
	// });
</script>
<?php echo form_open('deposito/process-add', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
$sesi 	= $this->session->userdata('unique');
$data 	= $this->session->userdata('addacctdeposito-' . $sesi['unique']);

if (empty($data['deposito_code'])) {
	$data['deposito_code'] 					= '';
}

if (empty($data['deposito_name'])) {
	$data['deposito_name'] 					= '';
}

if (empty($data['deposito_period'])) {
	$data['deposito_period'] 				= '';
}

if (empty($data['account_id'])) {
	$data['account_id'] 					= 0;
}

if (empty($data['account_basil_id'])) {
	$data['account_basil_id'] 				= 0;
}

if (empty($data['deposito_interest_rate'])) {
	$data['deposito_interest_rate'] 		= '';
}

if (empty($data['deposito_interest_period_days'])) {
	$data['deposito_interest_period_days'] = '';
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
			<a href="<?php echo base_url(); ?>deposito">
				Daftar Kode Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>deposito/add">
				Tambah Kode Simpanan Berjangka
			</a>
		</li>
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Kode Simpanan Berjangka
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
						<a href="<?php echo base_url(); ?>deposito" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="deposito_code" id="deposito_code" autocomplete="off" value="<?php echo set_value('deposito_code', $data['deposito_code']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Kode Simpanan Berjangka<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_name" id="deposito_name" value="<?php echo set_value('deposito_name', $data['deposito_name']); ?>" onChange="function_elements_add(this.name, this.value);">
									<label class="control-label">Nama
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
									<?php
									echo form_dropdown('account_id', $acctaccount, set_value('account_id', $data['account_id']), 'id="account_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
									?>

									<label class="control-label">Nomor Perkiraan</label>
								</div>
							</div>
							<div class="col-md-6" id='search2' style='display:block;'>
								<div class="form-group form-md-line-input">
									<label class="control-label"></label>
									<a class="btn blue" data-toggle="modal" href="#modalcoabaru"><i class="fa fa-plus"></i> Tambah Nomor Perkiraan Baru</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('deposito_interest_period', $depositointerestperiod, set_value('depositointerestperiod', $data['deposito_interest_period']), 'id="deposito_interest_period" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
									?>

									<label class="control-label">Jatuh Tempo/Periode</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_period" id="deposito_period" autocomplete="off" value="<?php echo set_value('deposito_period', $data['deposito_period']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Jangka Waktu<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('account_basil_id', $acctaccount, set_value('account_id', $data['account_basil_id']), 'id="account_basil_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
									?>

									<label class="control-label">Bunga</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_interest_rate" id="deposito_interest_rate" autocomplete="off" value="<?php echo set_value('deposito_interest_rate', $data['deposito_interest_rate']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Bunga<span class="required">*</span></label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_penalty_percentage" id="deposito_penalty_percentage" autocomplete="off" value="<?php echo set_value('deposito_penalty_percentage', $data['deposito_penalty_percentage']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Persentase Penalty<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_availability" id="deposito_availability" autocomplete="off" value="<?php echo set_value('deposito_availability', $data['deposito_availability']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Ketersediaan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_point" id="deposito_point" autocomplete="off" value="<?php echo set_value('deposito_point', $data['deposito_point']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Poin<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_commission_agent_percentage" id="deposito_commission_agent_percentage" autocomplete="off" value="<?php echo set_value('deposito_commission_agent_percentage', $data['deposito_commission_agent_percentage']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Persentase Komisi Agent<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_commission_supervisor_percentage" id="deposito_commission_supervisor_percentage" autocomplete="off" value="<?php echo set_value('deposito_commission_supervisor_percentage', $data['deposito_commission_supervisor_percentage']); ?>" onChange="function_elements_add(this.name, this.value);"/>
									<label class="control-label">Persentase Komisi Supervisor<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_commission_period" id="deposito_commission_period" autocomplete="off" value="<?php echo set_value('deposito_commission_period', $data['deposito_commission_period']); ?>" onChange="function_elements_add(this.name, this.value);" />
									<label class="control-label">Jangka Waktu Komisi<span class="required">*</span></label>
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

<div class="modal fade bs-modal-lg" id="modalcoabaru" tabindex="-1" role="basic" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Nomor Perkiraan Baru</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_code" name="account_code" autocomplete="off">
							<label class="control-label">Nomor Perkiraan</label>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_name" name="account_name" autocomplete="off">
							<label class="control-label">Nama Perkiraan</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_group" name="account_group" autocomplete="off">
							<label class="control-label">Golongan Perkiraan</label>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<?php echo form_dropdown('account_status', $accountstatus, set_value('account_status'), 'id="account_status" class="form-control select2me"'); ?>
							<label class="control-label"></label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<?php echo form_dropdown('account_type_id', $kelompokperkiraan, set_value('account_type_id'), 'id="account_type_id" class="form-control select2me"'); ?>
							<label class="control-label">Kelompok Perkiraan</label>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn red" data-dismiss="modal"><i class="fa fa-times"> Tutup</i></button>
				<button type="button" class="btn green-jungle" onClick='processAddAcctAccount();'><i class="fa fa-check"> Simpan</i></button>
			</div>
		</div>
	</div>
</div>