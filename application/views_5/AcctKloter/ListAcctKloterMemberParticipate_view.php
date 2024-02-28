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
<?php echo form_open('kloter/process-add-member-participate', array('id' => 'myform', 'class' => 'horizontal-form')); ?>
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
            <a href="<?php echo base_url(); ?>kloter/member-participate">
                Daftar Kloter
            </a>
            <i class="fa fa-angle-right"></i>
        </li>
    </ul>
</div>
<h3 class="page-title">
    Daftar Partispan Kloter <small><?php echo set_value('kloter_name', $acctkloter['kloter_name']); ?></small>
</h3>
<div class="row-fluid">
    <?php
    echo $this->session->userdata('message');
    $this->session->unset_userdata('message');
    ?>
</div>
<div>
    <label class="col-form-label">Sisa Kuota :</label>
    <input type="text" class="easyui-textbox" name="kloter_quota" id="kloter_quota" autocomplete="off"
        value="<?php echo set_value('kloter_quota', $acctkloter['kloter_quota']); ?>" style="width: 5%" readonly />
        <?php
        if($acctkloter['kloter_quota'] == 0){
            echo "<button class= 'btn btn-info btn-sm' disabled><i class= 'fa fa-plus'></i> Tambah Partisipan</button>";
        }else{
            echo "<a href= '#' role= 'button' class= 'btn btn-info btn-sm' data-toggle= 'modal' data-target= '#memberlist'><i class= 'fa fa-plus'></i> Tambah Partisipan</a>";
        } 
        ?>
    <!-- <a href="#" role="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#memberlist"><i
        class="fa fa-plus"></i> Tambah Partisipan</a> -->
</div><br>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    Daftar Anggota Partisipan
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
                    <table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nama Anggota</th>
                                <th width="10%">Nomor Anggota</th>
                                <th width="15%">Alamat</th>
                                <th width="5%">Action</th>
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
                                            <td>
                                                <a href='" . $this->config->item('base_url') . 'kloter/delete-member-participate/' . $val['kloter_member_id'] . '/' . $acctkloter['kloter_id'] . "'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")' ><i class='fa fa-trash-o'></i> Hapus</a>
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

<div id="memberlist" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Daftar Anggota</h4>
            </div>
            <div class="modal-body">
                <table id="myDataTable" class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Member No</th>
                            <th>Member Nama</th>
                            <th>Alamat</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
 
var table;
 
$(document).ready(function() {
 
    //datatables
    table = $('#myDataTable').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "pageLength": 5,
        "order": [], //Initial no order.
        "ajax": {
            "url": "<?php echo site_url('kloter/get-list-member/'.$acctkloter['kloter_id'])?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
 
    });
 
});
</script>
<?php echo form_close(); ?>