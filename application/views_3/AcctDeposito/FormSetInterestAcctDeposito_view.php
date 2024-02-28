<script>
	base_url = '<?php echo base_url();?>';
	mappia = "	<?php 
					$site_url = 'deposito/add/';
					echo site_url($site_url); 
				?>";

	function processAddAcctAccount() {
		var account_code 			= $("#account_code").val();
		var account_name 			= $("#account_name").val();
		var account_status 			= $("#account_status").val();
		var account_group 			= $("#account_group").val();
		var account_type_id 		= $("#account_type_id").val();

		if(account_code == ''){
			alert('Nomor Perkiraan masih kosong');
			$('#account_code').val('');
		} else if(account_name == ''){
			alert('Nama Perkiraan masih kosong');
			$('#account_name').val('');
		} else if(account_group == ''){
			alert('Golongan Perkiraan masih kosong');
			$('#account_group').val('');
		} else  {
			$.ajax({
				type: "POST",
				url : "<?php echo site_url('deposito/process-add-account');?>",
				data: {
						'account_code' 		: account_code,
						'account_name'		: account_name,
						'account_status'	: account_status,
						'account_group'		: account_group,
						'account_type_id'	: account_type_id,
					},

				success: function(msg){
					// alert(msg);
					$('#account_code').val('');
					$('#account_name').val('');
					$('#account_group').val('');
					window.location.replace(mappia);
			}
			});
		}
	}
</script>
<?php echo form_open('deposito/process-set-interest',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
		<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Home
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito">
				List Kode Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito/set-interest/<?php echo $this->uri->segment(3); ?>">
				Set Bunga Simpanan Berjangka 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Set Bunga Simpanan Berjangka 
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
						Form Detail
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>deposito" class="btn btn-default btn-sm">
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
									<input type="hidden" class="form-control" name="deposito_id" id="deposito_id" value="<?php echo set_value('deposito_id',$acctdeposito['deposito_id']);?>">
									<input type="text" class="form-control" name="deposito_name" id="deposito_name" value="<?php echo set_value('deposito_name',$acctdeposito['deposito_name']);?>" readonly>
									<label class="control-label">Nama
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="deposito_period" id="deposito_period" autocomplete="off" value="<?php echo set_value('deposito_period',$acctdeposito['deposito_period']);?>" readonly/>
									<label class="control-label">Jangka Waktu<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('account_id', $depositointerestperiod, set_value('account_id',$acctdeposito['deposito_interest_period']),'id="account_id" class="form-control select2me" disabled');?>
									<label class="control-label">Periode Bunga</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Set Bunga
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body"><table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<?php 
									foreach ($periode as $key => $val){
								?>
										<th style='text-align:center'><?php echo $val?></th>
								<?php 
									} 
								?>
							</tr>
						</thead>
						<tbody>
								<?php 
									$no = 0;
									foreach ($periode as $key => $val){
										$deposito_interest = $this->AcctDeposito_model->getAcctDepositoInterest($val, $acctdeposito['deposito_id']);
										if($deposito_interest){
								?>
											<td>
												<input type="text" class="form-control" name="deposito_interest_percentage_<?php echo $no;?>" id="deposito_interest_percentage_<?php echo $no;?>" value="<?php echo set_value('deposito_interest',$deposito_interest['deposito_interest_percentage']);?>">
												<input type="hidden" class="form-control" name="deposito_interest_date_<?php echo $no;?>" id="deposito_interest_date_<?php echo $no;?>" value="<?php echo set_value('deposito_interest_date',$val);?>">
											</td>
								<?php 
										}else{
								?>
											<td>
												<input type="text" class="form-control" name="deposito_interest_percentage_<?php echo $no;?>" id="deposito_interest_percentage_<?php echo $no;?>" value="<?php echo set_value('deposito_interest',$acctdeposito['deposito_interest_rate']);?>">
												<input type="hidden" class="form-control" name="deposito_interest_date_<?php echo $no;?>" id="deposito_interest_date_<?php echo $no;?>" value="<?php echo set_value('deposito_interest_date',$val);?>">
											</td>
								<?php
										}
										$no++;
									} 
								?>
						</tbody>
							</table>
						<br>
						<div class="row">
							<div class="col-md-12" style='text-align:right'>
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

