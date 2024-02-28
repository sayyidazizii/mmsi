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
<?php	echo form_open('system-point/filter',array('id' => 'myform', 'class' => '')); 

	$start_date					= $sesi['start_date'];
	$end_date					= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Point Anggota
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>system-point/setting" class="btn btn-default btn-sm">
						<i class="fa fa-gear"></i>
						<span class="hidden-480">
							Setting Point Anggota
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
									
								?>
								<label>Cabang</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>

<?php echo form_close(); ?>

				<div class="portlet-body">
					<div class="form-body form">
						<table class="table table-striped table-bordered table-hover table-full-width">
							<thead>
								<tr>
									<th width="5%">No</th>
									<th width="25%">No. Anggota</th>
									<th width="25%">Nama Anggota</th>
									<th width="25%">Jumlah Point</th>
									<th width="20%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$no = 1;
									if(empty($systempoint)){
										echo "
											<tr>
												<td colspan='8' align='center'>Data Kosong</td>
											</tr>
										";
									} else {
										$no = 1;
										foreach ($systempoint as $key=>$val){	
											echo"
												<tr>
													<td align='center'>".$no."</td>
													<td>".$val['member_no']."</td>
													<td>".$val['member_name']."</td>
													<td align='right'>".number_format($val['point_total'], 0)."</td>
													<td align='center'>
														<a href='".$this->config->item('base_url').'system-point/detail/'.$val['member_id']."'class='btn default btn-xs yellow-lemon'>
															<i class='fa fa-bars'></i> Detail
														</a>
													</td>
												</tr>
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
			</div>
		</div>
<?php echo form_close(); ?>