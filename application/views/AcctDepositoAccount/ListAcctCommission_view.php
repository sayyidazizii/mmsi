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
			<a href="<?php echo base_url();?>deposito-account">
				Daftar Rekening Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>deposito-account/commission">
				Tambah Rekening Simpanan Berjangka
			</a>
		</li>
	</ul>
</div>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');
	$sesi = $this->session->userdata('filter-listacctdepositoaccount');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Komisi
				</div>
				<div class="actions">
					<a href="<?php echo base_url(); ?>deposito-account" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">No Deposito</th>
							<th width="10%">Tgl Komisi</th>
							<th width="15%">Komisi Agent Cair</th>
							<th width="15%">Komisi Agent Ditahan</th>
							<th width="15%">Komisi Supervisor Cair</th>
							<th width="15%">Komisi Supervisor Ditahan</th>
							<th width="15%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($acctcommission)){
								echo "
									<tr>
										<td colspan='8' align='center'>Data Kosong</td>
									</tr>
								";
							} else {
								foreach ($acctcommission as $key => $val){									
									echo"
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>".$val['deposito_account_no']."</td>
											<td>".tgltoview($val['commission_date'])."</td>
											<td style='text-align:right'>".number_format($val['commission_disbursed_agent'], 2)."</td>
											<td style='text-align:right'>".number_format($val['commission_on_hold_agent'], 2)."</td>
											<td style='text-align:right'>".number_format($val['commission_disbursed_supervisor'], 2)."</td>
											<td style='text-align:right'>".number_format($val['commission_on_hold_supervisor'], 2)."</td>
											<td>";
											if($val['commission_disbursed_status'] == 0 && $val['commission_date'] > date('Y-m-d')){
												echo"
												<a href='".$this->config->item('base_url').'deposito-account/edit-commission/'.$val['deposito_account_id'].'/'.$val['commission_id']."' class='btn default btn-xs yellow-lemon'>
													Edit
												</a>
												<a onClick=\"javascript:return confirm('Apakah anda yakin ingin menghapus data ini ?')\" href='".$this->config->item('base_url').'deposito-account/process-delete-commission/'.$val['deposito_account_id'].'/'.$val['commission_id']."' class='btn default btn-xs red'>
													Hapus
												</a>";
											}else{
												echo"
												<a class='btn default btn-xs gray'>
													Tanggal Komisi Sudah Berlalu
												</a>";
											}
											echo"
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