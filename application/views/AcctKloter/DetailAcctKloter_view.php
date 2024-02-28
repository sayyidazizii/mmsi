<script>
base_url = '<?php echo base_url(); ?>';

function ulang() {
    document.getElementById("kloter_name").value = "<?php echo $acctkloter['kloter_name'] ?>";
}
</script>
<?php echo form_open('kloter/process-edit', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
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
        <li>
            <a href="">
                Detail Data Kloter
            </a>
        </li>
    </ul>
</div>
<h3 class=" page-title">
    Form Detail Data Kloter
</h3>
<?php
echo $this->session->userdata('message');
$this->session->unset_userdata('message');
?>
<div class=" row">
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        Form Detail
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
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_name" id="kloter_name"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_name', $acctkloter['kloter_name']); ?>"
                                        readonly />
                                    <label class="control-label">Nama Kloter<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_quota" id="kloter_quota"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_quota', $acctkloter['kloter_quota']); ?>"
                                        readonly />
                                    <label class="control-label">Kuota Kloter<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_period" id="kloter_period"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_period', $acctkloter['kloter_period']); ?>"
                                        readonly />
                                    <label class="control-label">Jangka Waktu (Bulan)<span
                                            class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_amount" id="kloter_amount"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_amount', nominal($acctkloter['kloter_amount'], 2)); ?>"
                                        readonly />
                                    <label class="control-label">Nominal Partisipasi (Rp)<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_prize_amount" id="kloter_prize_amount"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_prize_amount', nominal($acctkloter['kloter_prize_amount'], 2)); ?>"
                                        readonly />
                                    <label class="control-label">Total Hadiah (Rp)<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_prize" id="kloter_prize"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_prize', $acctkloter['kloter_prize']); ?>"
                                        readonly />
                                    <label class="control-label">Hadiah<span class="required">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-md-line-input">
                                    <input type="text" class="form-control" name="kloter_point" id="kloter_point"
                                        autocomplete="off"
                                        value="<?php echo set_value('kloter_point', $acctkloter['kloter_point']); ?>"
                                        readonly />
                                    <label class="control-label">Poin<span class="required">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            Daftar Anggota Partisipan
        </div>
    </div>
    <div class="portlet-body">
        <div class="form-body">
            <table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
                <thead>
                    <tr>
                        <th style="text-align:center" width="5%">No</th>
                        <th style="text-align:center" width=" 15%">Nama Anggota</th>
                        <th style="text-align:center" width="10%">Nomor Anggota</th>
                        <th style="text-align:center" width="15%">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (empty($memberparticipate)) {
                        echo "
                            <tr>
                                <td colspan='11' align='center'>Emty Data</td>
                            </tr>
                            ";
                    } else {
                        foreach ($memberparticipate as $key => $val) {
                            echo "
                            <tr>			
                                <td style='text-align:center'>$no.</td>
                                <td>" . $val['member_name'] . "</td>
                                <td>" . $val['member_no'] . "</td>
                                <td>" . $val['member_address'] . "</td>
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
</div> <input type="hidden" class="form-control" name="kloter_id" id="kloter_id" placeholder="id"
    value="<?php echo set_value('kloter_id', $acctkloter['kloter_id']); ?>" />
<?php echo form_close(); ?>