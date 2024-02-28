<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}
	input:read-only {
		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?php echo base_url();?>';

	// $(document).ready(function() {
	// 	$('#excel_file').filebox({
	// 		onChange: function(value) {
	// 			console.log('tes');
	// 			$.ajax({
	// 				type: "POST",
	// 				url : "<?php echo site_url('debt-repayment/add-array');?>",
	// 				data: {
	// 						'value'			: value,
	// 						'session_name' 	: "addarraydebtrepayment-"
	// 					},
	// 				success: function(msg){
	// 					window.location.reload();
	// 				}
	// 			});
	// 		}
	// 	})
	// });
	
</script>
<?php echo form_open_multipart('debt-repayment/add-array',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctdebtrepayment-'.$sesi['unique']);
	$auth 	= $this->session->userdata('auth');
	$token 	= $this->session->userdata('acctdebtrepaymenttoken-'.$sesi['unique']);

?>

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
			<a href="<?php echo base_url();?>debt-repayment">
				Daftar Pelunasan Piutang Potong Gaji
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>debt-repayment/add">
				Tambah Pelunasan Piutang Potong Gaji 
			</a>
		</li>
	</ul>
</div>

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
						Form Tambah Pelunasan Piutang Potong Gaji
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>debt-repayment" class="btn btn-default btn-sm">
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

							<input type="hidden" class="form-control" name="debt_repayment_token" id="debt_repayment_token" value="<?php echo $token;?>" readonly/>

							<div class="col-md-6">
								<table width="100%">
									<tr>
										<td width="35%">File Excel</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="" accept=".xlsx, .xls, .csv" class="easyui-filebox" name="excel_file" id="excel_file" style="width: 60%"/>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="submit" name="process" value="process" id="process" class="btn green-jungle" title="Proses Data">Proses</i></button>
							</div>	
						</div>
					</div>
			 	</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<?php echo form_open_multipart('debt-repayment/process-add',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar File Excel
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">
						<table class="table table-striped table-bordered table-hover table-full-width" id="myDataTable">
							<thead>
								<tr>
									<th width="5%" style="text-align: center;">No</th>
									<th width="25%" style="text-align: center;">No Anggota</th>
									<th width="35%" style="text-align: center;">Nama Anggota</th>
									<th width="35%" style="text-align: center;">Jumlah Potong Gaji</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$no = 1;
								if(empty($acctdebtrepaymentitemtemp)){
									echo "
										<tr>
											<td colspan='4' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach($acctdebtrepaymentitemtemp as $key => $val){
								?>
										<tr>
											<td width="5%" style="text-align: center;"><?php echo $no; ?></td>
											<td width="25%" style="text-align: left;"><?php echo $this->AcctDebtRepayment_model->getCoreMemberNo($val['member_id'])?></td>	
											<td width="35%" style="text-align: left;"><?php echo $this->AcctDebtRepayment_model->getCoreMemberName($val['member_id'])?></td>
											<td width="35%" style="text-align: right;"><?php echo number_format($val['debt_repayment_item_temp_amount'], 2) ?></td>
										</tr>
								<?php 
										$no++;
									}
							 	} 
								?>
							</tbody>
						</table>
						</div>
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
