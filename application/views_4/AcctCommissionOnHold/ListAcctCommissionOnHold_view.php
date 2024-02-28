<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}
	

</style>
<div class="row-fluid">
	

			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
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
			<a href="<?php echo base_url();?>commission-on-hold">
				Daftar Komisi Ditahan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Komisi Ditahan <small>Kelola Komisi Ditahan</small>
</h3>
<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="15%">Nama BO</th>
								<th width="15%">No Rek Tabungan</th>
								<th width="10%">No Simpanan Berjangka</th>
								<th width="15%">Jumlah</th>
								<th width="15%">Tanggal Cair</th>
								<th width="15%">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctcommissiononhold)){
									echo "
										<tr>
											<td colspan='7' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctcommissiononhold as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['office_name']."</td>
												<td>".$val['savings_account_no']."</td>
												<td>".$val['deposito_account_no']."</td>
												<td align='right'>".number_format($val['commission_on_hold_amount'], 2)."</td>
												<td>".$val['commission_on_hold_date']."</td>";
												if($val['commission_on_hold_status'] == 0){
													echo"<td>Ditahan</td>";
												}else{
													echo"<td>Sudah Dicairkan</td>";
												}
											echo"</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
							</table>
						</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
<?php echo form_close(); ?>