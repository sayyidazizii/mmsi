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
            <a href="<?php echo base_url(); ?>kloter">
                Daftar Kloter
            </a>
            <i class="fa fa-angle-right"></i>
        </li>
    </ul>
</div>
<h3 class="page-title">
    Daftar Kloter <small>Kelola Data Kloter</small>
</h3>
<div class="row-fluid">
    <?php
        echo $this->session->userdata('message');
        $this->session->unset_userdata('message');
    ?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    Daftar
                </div>
                <div class="actions">
                    <a href="<?php echo base_url(); ?>kloter/add" class="btn btn-default btn-sm">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-480">
                            Tambah Data Kloter Baru
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
                                <th width="10%">No Kloter</th>
                                <th width="10%">Nama Kloter</th>
                                <th width="7%">Kuota <br>Kloter</th>
                                <th width="5%">Jangka Waktu <br>(Bulan)</th>
                                <th width="10%">Nominal Partisipan <br>(Rp)</th>
                                <th width="10%">Total Hadiah <br>(Rp)</th>
                                <th width="10%">Hadiah</th>
                                <th width="10%">COA Pendapatan <br>Partisipasi</th>
                                <th width="10%">COA Hadiah</th>
                                <th width="5%">Poin</th>
                                <th width="10%">Status Kloter</th>
                                <th width="7%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (empty($acctkloter)) {
                                echo "
										<tr>
											<td colspan='11' align='center'>Emty Data</td>
										</tr>
									";
                            } else {
                                foreach ($acctkloter as $key => $val) {
                                    echo "
										<tr>			
											<td style='text-align:center'>$no.</td>
											<td>" . $val['kloter_no'] . "</td>
											<td>" . $val['kloter_name'] . "</td>
											<td>" . $val['kloter_quota'] . "</td>
											<td>" . $val['kloter_period'] . "</td>
											<td style='text-align:right'>" . number_format($val['kloter_amount'], 2) . "</td>
											<td style='text-align:right'>" . number_format($val['kloter_prize_amount'], 2) . "</td>
											<td>" . $val['kloter_prize'] . "</td>
											<td>" . $this->AcctKloter_model->getAcctAccountName($val['account_kloter_id']) . "</td>
											<td>" . $this->AcctKloter_model->getAcctAccountName($val['account_prize_id']) . "</td>
											<td>" . $val['kloter_point'] . "</td>
											<td>" . ($val['kloter_status'] == 0 ? "Aktif" : "Tutup" ). "</td>
											<td>
												<a href='" . $this->config->item('base_url') . 'kloter/edit/' . $val['kloter_id'] . "' class='btn default btn-xs blue'><i class='fa fa-edit'></i> Edit</a>
												" . ($val['kloter_status'] == 0 ? "<a href='" . $this->config->item('base_url') . 'kloter/member-participate/' . $val['kloter_id'] . "' class='btn default btn-xs purple'><i class='fa fa-users'></i> Partisipan</a>" : " " ). "
												<a href='" . $this->config->item('base_url') . 'kloter/detail/' . $val['kloter_id'] . "' class='btn default btn-xs yellow-lemon'><i class='fa fa-bars'></i> Detail</a>
                                                " . ($val['kloter_status'] == 0 ? "<a href='" . $this->config->item('base_url') . 'kloter/closing/' . $val['kloter_id'] . "'class='btn default btn-xs green-jungle', onClick='javascript:return confirm(\"apakah yakin ingin melakukan penutupan Kloter ?\")'><i class='fa fa-exclamation-circle'></i> Penutupan</a>" : " " ). "
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