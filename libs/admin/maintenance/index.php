<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Jar Types & Pricing</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped" id="indi-list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="35%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Date Created</th>
						<th>Name</th>
						<th>Description</th>
						<th>Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `jar_types` order by unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
							$row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])))
					?>
					
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['date_created'] ?></td>
							<td><?php echo $row['name'] ?></td>
							<td><p class="m-o truncate"><?php echo $row['description'] ?></p></td>
							<td class="text-right"><?php echo $row['pricing'] ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"> Edit</a>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var indiList;
	$(document).ready(function(){
		$('#uni_modal').on('show.bs.modal',function(){
			$('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
		})
		$('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i>Add New Jar Type & Pricing",'maintenance/manage_jar_type.php');
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-edit'></i>Edit Jar Type & Pricing",'maintenance/manage_jar_type.php?id='+$(this).attr('data-id'));
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Jar Type & Pricing?","delete_jartype",[$(this).attr('data-id')])
		})
	})
	function delete_jartype($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_jar_type",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	$(function(){
		indiList = $('#indi-list').dataTable({
			columnDefs:[{
				targets:[5],
				orderable:false
			}],
		});
	})
</script>