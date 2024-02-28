<script>
    base_url = '<?php echo base_url(); ?>';

    function ulang() {
        document.getElementById("branch_id").value = "";
        document.getElementById("kloter_name").value = "";
        // document.getElementById("store_name").value = "";
    }
</script>
<?php echo form_open('kloter/process-add', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
$data = $this->session->userdata('addAcctKloter');
$sesi     = $this->session->userdata('unique');
$token     = $this->session->userdata('acctklotertoken-' . $sesi['unique']);
?>
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
                Daftar Data Kloter
            </a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="<?php echo base_url(); ?>kloter/add">
                Tambah Data Kloter
            </a>
        </li>
    </ul>
</div>
<h3 class="page-title">
    Form Tambah data Kloter
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
                        <a href="<?php echo base_url(); ?>kloter" class="btn btn-default btn-sm">
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
                            <input type="hidden" class="form-control" name="kloter_token" id="kloter_token" value="<?php echo $token; ?>" readonly />
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_name" id="kloter_name" autocomplete="off" />
                                    <label class="control-label">Nama Kloter<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_quota" id="kloter_quota" autocomplete="off" />
                                    <label class="control-label">Kuota Kloter<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_period" id="kloter_period" autocomplete="off" />
                                    <label class="control-label">Jangka Waktu (Bulan)<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_amount" id="kloter_amount"
                                        autocomplete="off" />
                                    <label class="control-label">Nominal Partisipasi (Rp)<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_prize_amount" id="kloter_prize_amount"
                                        autocomplete="off" />
                                    <label class="control-label">Total Hadiah (Rp)<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_prize" id="kloter_prize" autocomplete="off" />
                                    <label class="control-label">Hadiah<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_point" id="kloter_point"
                                        autocomplete="off" />
                                    <label class="control-label">Poin<span class="required">*</span></label>
                                </div>
                            </div>
                        </div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('account_kloter_id', $acctaccount, set_value('account_id', $data['account_kloter_id']), 'id="account_kloter_id" class="form-control select2me"');
									?>

									<label class="control-label">Pendapatan Kloter</label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php
									echo form_dropdown('account_prize_id', $acctaccount, set_value('account_id', $data['account_prize_id']), 'id="account_prize_id" class="form-control select2me"');
									?>

									<label class="control-label">Biaya Hadiah</label>
								</div>
							</div>
						</div>

                        <div class="row">
                            <div class="col-md-12" style='text-align:right'>
                                <button type="reset" name="Reset" value="Batal" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
                                <button type="submit" name="Save" value="Simpan" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>