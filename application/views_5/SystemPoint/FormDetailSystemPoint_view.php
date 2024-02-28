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
	td .bold{
		font-size:12px  !important;
		font-weight: bold !important;
	}
	

</style>
<script type="text/javascript">
	base_url = "<?php echo base_url();?>";

	function reset_search(){
		document.location = base_url +"system-point/reset-search";
	}
</script>
<div class="row-fluid">
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
				Daftar Point Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>system-point">
				Detail Point Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>

<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth 	= $this->session->userdata('auth');
	$sesi 	= $this->session->userdata('filter-systempoint');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['branch_id']			= '';
	}
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Detail Point Anggota
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
					<div class="form-body form">
						
						<div class = "row">
							<div class = "col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control form-control-inline input-big date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($sesi['start_date']);?>" readonly/>
									<label class="control-label">Tanggal Awal
									</label>
								</div>
							</div>

							<div class = "col-md-6">
								<div class="form-group form-md-line-input">
									<input class="form-control form-control-inline input-big date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($sesi['end_date']);?>" readonly/>
									<label class="control-label">Tanggal Akhir
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no',$coremember['member_no']);?>" readonly/>
									<label class="control-label">No Anggota</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name',$coremember['member_name']);?>" readonly/>
									<label class="control-label">Nama Anggota</label>
								</div>
							</div>
						</div>
						<hr/>
						<table class="table table-striped table-bordered table-hover table-checkable order-column" id="myDataTable">
							<thead>
								<tr>
									<th width="5%">No</th>
									<th width="25%">Tanggal Perolehan</th>
									<th width="25%">Sumber</th>
									<th width="20%">Inputer</th>
									<th width="25%">Jumlah Point</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$no 	= 1;
									$total 	= 0;
									if(empty($systempoint)){
										echo "
											<tr>
												<td colspan='8' align='center'>Data Kosong</td>
											</tr>
										";
									} else {
										foreach ($systempoint as $key=>$val){	
											echo "
												<tr>
													<td>".$no."</td>
													<td>".$val['point_date']."</td>
													<td>".$val['point_from']."</td>
													<td>".$this->SystemPoint_model->getSystemUserName($val['created_id'])."</td>
													<td align='right'>".number_format($val['point_amount'], 0)."</td>
												</tr>
											";
											$total += $val['point_amount'];
											$no++;
										} 
										echo "
											<tr>
												<th colspan='4'>Total Poin</th>
												<td align='right' class='bold'>".number_format($total, 0)."</td>
											</tr>
										";
									}
								?>
								</tbody>
							</table>							
						</div>
					</div>
				</div>
			</div>
		</div>
<?php echo form_close(); ?>