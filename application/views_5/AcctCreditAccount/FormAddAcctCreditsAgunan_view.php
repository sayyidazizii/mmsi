<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}
	input:read-only {
		background-color: f0f8ff;
	}
</style>
<script>
	base_url = '<?= base_url()?>';
	mappia = "	<?php 
					$id = $this->uri->segment(3);
					$site_url = 'credit-account/add-form/'.$id;
					echo site_url($site_url); 
				?>";

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

	$(document).on('change','#bpkb_taksiran_view',function(event){
		bpkb_taksiran_view				= $('#bpkb_taksiran_view')[0].value;	
		
		document.getElementById('bpkb_taksiran_view').value	= toRp(bpkb_taksiran_view);
		document.getElementById('bpkb_taksiran').value		= bpkb_taksiran_view;
		
	});

	$(document).on('change','#bpkb_gross_view',function(event){
		bpkb_gross_view				= $('#bpkb_gross_view')[0].value;	
		
		document.getElementById('bpkb_gross_view').value	= toRp(bpkb_gross_view);
		document.getElementById('bpkb_gross').value			= bpkb_gross_view;
		
	});

	$(document).on('change','#shm_taksiran_view',function(event){
		shm_taksiran_view				= $('#shm_taksiran_view')[0].value;	
		
		document.getElementById('shm_taksiran_view').value	= toRp(shm_taksiran_view);
		document.getElementById('shm_taksiran').value		= shm_taksiran_view;
		
	});

	function formupdate(data){
		if(data.value != ''){
				if(data.value == "SK Pengangkatan"){
				document.getElementById("sk").style.display 			= "block";
				document.getElementById("manulife").style.display 		= "none";
				document.getElementById("bpjs").style.display 			= "none";
				document.getElementById("other").style.display 			= "none";
				
			}else if(data.value == "Manulife"){
				document.getElementById("sk").style.display 			= "none";
				document.getElementById("manulife").style.display 		= "block";
				document.getElementById("bpjs").style.display 			= "none";
				document.getElementById("other").style.display 			= "none";

			}else if(data.value == "BPJS Ketenagakerjaan"){
				document.getElementById("sk").style.display 			= "none";
				document.getElementById("manulife").style.display 		= "none";
				document.getElementById("bpjs").style.display 			= "block";
				document.getElementById("other").style.display 			= "none";

			}else {
				document.getElementById("sk").style.display 			= "none";
				document.getElementById("manulife").style.display 		= "none";
				document.getElementById("bpjs").style.display 			= "none";
				document.getElementById("other").style.display 			= "block";
			}
		}
	}

	function processAddArrayAgunan(){
		
		var tipe					= document.getElementById("tipe_agunan").value;
		var sk_description			= document.getElementById("sk_description").value;
		var manulife_description	= document.getElementById("manulife_description").value;
		var bpjs_description 		= document.getElementById("bpjs_description").value;
		var other_description 		= document.getElementById("other_description").value;

	

			$('#offspinwarehouse').css('display', 'none');
			$('#onspinspinwarehouse').css('display', 'table-row');
			  $.ajax({
			  type: "POST",
			  url : "<?php echo site_url('credit-account/process-add-array-agunan');?>",
			  data: {
					'tipe' 						: tipe,	
					'sk_description' 			: sk_description,
					'manulife_description' 		: manulife_description,
					'bpjs_description' 			: bpjs_description,
					'other_description'			: other_description,
					'session_name' 				: "addarrayacctcreditsagunan-"
				},
			  success: function(msg){
			   window.location.replace(mappia);
			 }
			});
	}
</script>

		<!-- <?php echo form_open('credit-account/process-add-array-agunan',array('id' => 'myform', 'class' => 'horizontal-form')); ?> -->
		<div class="form-body">
			<table style="width: 100%;" border="0" padding:"0">
				<tbody  id="tipe" style="display:block" >
					<tr>
						<td>Pilih Tipe</td>
						<td> : </td>
						<td> <select name="tipe" id="tipe_agunan" class="form-control" onchange="formupdate(this)">
							<option value="">Select</option>
							<option value="SK Pengangkatan">SK Pengangkatan</option>
							<option value="Manulife">Manulife</option>
							<option value="BPJS Ketenagakerjaan">BPJS Ketenagakerjaan</option>
							<option value="Lain-Lain">Lain-Lain</option>
							</select>
						</td>
					</tr>
				</tbody>
				<tbody  id="sk" style="display:none">
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="sk_description" id="sk_description" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="manulife" style="display:none">
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="manulife_description" id="manulife_description" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="bpjs" style="display:none">
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="bpjs_description" id="bpjs_description" autocomplete="off" />
						</td>
					</tr>
				</tbody>
				<tbody  id="other" style="display:none">
					<tr>
						<td>Keterangan</td>
						<td> : </td>
						<td><input type="text" class="form-control" name="other_description" id="other_description" autocomplete="off" />
						</td>
					</tr>
				</tbody>
			</table>
		<div class="row">
			<div class="col-md-12" style='text-align:left'>
				<input type="button" name="add2" id="buttonAddArrayInvtGoodsReceivedNote" value="Add" class="btn green-jungle" title="Simpan Data" onClick="processAddArrayAgunan();">
			</div>	
		</div>
		<!-- <?php echo form_close(); ?> -->

		<?php 
			$sesi = $this->session->userdata('unique');
			$daftaragunan = $this->session->userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
			// print_r($daftaragunan);
		?>

		<table class="table table-striped table-hover">
			<tr>
				<th>No</th>
				<th>Type</th>
				<th>Keterangan</th>
			</tr>
			<?php 
				$no = 1;
				if(empty($daftaragunan)){

				} else {
					foreach ($daftaragunan as $key => $val) {
						if($val['credits_agunan_type'] == "SK Pengangkatan"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Keterangan : ".$val['credits_agunan_sk_description']."</td>
								</tr>
							";
						} else if($val['credits_agunan_type'] == "Manulife"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Keterangan : ".$val['credits_agunan_manulife_description']."</td>
								</tr>
							";
						}else if($val['credits_agunan_type'] == "BPJS Ketenagakerjaan"){
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Keterangan : ".$val['credits_agunan_bpjs_description']."</td>
								</tr>
							";
						}else{
							echo "
								<tr>
									<td>$no</td>
									<td>".$val['credits_agunan_type']."</td>
									<td>Keterangan : ".$val['credits_agunan_other_description']."</td>
								</tr>
							";
						}
						$no++;
					}
				}
			?>
		</table>
