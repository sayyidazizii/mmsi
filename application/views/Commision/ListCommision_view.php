<style>
	th {
		font-size: 14px !important;
		font-weight: bold !important;
		text-align: center !important;
		margin: 0 auto;
		vertical-align: middle !important;
	}

	td {
		font-size: 12px !important;
		font-weight: normal !important;
	}
</style>
<div class="row-fluid">
	<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
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
					Konfigurasi Komisi
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
		</ul>
	</div>
	<!-- END PAGE TITLE & BREADCRUMB-->

	<h3 class="page-title">
		Daftar Konfigurasi Komisi <small>Kelola Komisi</small>
	</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url(); ?>commision/add" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Tipe Komisi Baru
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
									<th width="10%">Kode Komisi</th>
									<th width="15%">Nama</th>
									<th width="10%">Jenis Komisi</th>
									<th width="10%">Bunga komisi</th>
									<th hidden width="10%">Jangka Waktu (bln)</th>
									<th width="15%">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$type = '';
									foreach ($corecommision as $val) {
										if( $val['commision_type'] == 1 ){
											$type = 'Komisi Agent';
										}else{
											$type = 'Komisi Supervisor';
										}
										echo "
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>" . $val['commision_code'] . "</td>
												<td>" . $val['commision_name'] . "</td>
												<td>" . $type . "</td>
												<td>" . $val['commision_percentage'] . " %</td>
												<td hidden>" . $val['commision_period'] . "</td>
												<td>
													<a href='" . $this->config->item('base_url') . 'commision/edit/' . $val['core_commision_id'] . "' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='" . $this->config->item('base_url') . 'commision/delete/' . $val['core_commision_id'] . "'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
														<i class='fa fa-trash-o'></i> Hapus
													</a>
												</td>
											</tr>
										";
										$no++;
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