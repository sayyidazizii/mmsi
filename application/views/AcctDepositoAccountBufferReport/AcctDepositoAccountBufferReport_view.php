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
				<a href="<?php echo base_url(); ?>AcctDepositoAccountBufferReport">
					Laporan Buffer
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
		</ul>
	</div>
	<!-- END PAGE TITLE & BREADCRUMB-->

	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Laporan Buffer Simpanan Berjangka
					</div>
				</div>


				<div class="portlet-body form">
					<div class="form-body">

						<?php echo form_open('AcctDepositoAccountBufferReport/viewreport', array('id' => 'myform', 'class' => ''));
						$auth = $this->session->userdata('auth');
						?>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Mulai Tanggal</label>
									<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo date('d-m-Y'); ?>" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Sampai Tanggal</label>
									<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo date('d-m-Y'); ?>" />
								</div>
							</div>
							<?php if ($auth['branch_status'] == 1) { ?>
								<div class="col-md-4">
									<div class="form-group">
										<label>Cabang</label>
										<?php
										echo form_dropdown('branch_id', $corebranch, set_value('branch_id'), 'id="branch_id" class="form-control select2me" ');
										?>

									</div>
								</div>
							<?php } ?>
						</div>

						<div class="row">
							<div class="form-actions right">
								<button type="submit" class="btn green-jungle" id="view" name="view" value="excel"><i class="fa fa-file-excel-o"></i> Export data</button>
								<button type="submit" class="btn blue" id="view" name="view" value="pdf"><i class="fa fa-file-pdf-o"></i> Laporan Pdf</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END EXAMPLE TABLE PORTLET-->
		</div>
	</div>
	<?php echo form_close(); ?>