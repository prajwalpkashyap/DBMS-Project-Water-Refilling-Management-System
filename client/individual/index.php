<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
#selectAll{
	top:0
}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Individual</h3>
		<!-- <div class="card-tools">
			<a href="?page=individual/manage_individual" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<div class="row" style="display:none" id="selected_opt">
				<div class="w-100 d-flex">
					<div class="col-2">
						<label for="" class="controllabel"> With Selected:</label>
					</div>
					<div class="col-3">
						<select id="w_selected" class="custom-select select" >
							<option value="no_show">Mark as No Show</option>
							<option value="delete">Delete</option>
							<option value="done">Mark as Done</option>
						</select>
					</div>
					<div class="col">
						<button class="btn btn-primary" type="button" id="selected_go">Go</button>
					</div>
				</div>
			</div>
			<table class="table table-bordered table-stripped" id="indi-list">
				<colgroup>
					<col width="5%">
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="15%">
					<col width="25%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<td class="text-center"><div class="form-check">
								<input type="checkbox" class="form-check-input" id="selectAll">
							</div></td>
						<th class="text-center">#</th>
						<th>Code</th>
						<th>Name</th>
						<th>Schedule</th>
						<th>Location</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT i.*,s.date_sched,s.status,l.`location` from `individuals` i inner join `schedules` s on i.id = s.individual_id inner join `location` l on l.id = s.location_id order by unix_timestamp(s.date_sched) desc ");
						while($row = $qry->fetch_assoc()):
					?>
					
						<tr>
							<td class="text-center">
							<div class="form-check">
								<input type="checkbox" class="form-check-input invCheck" value="<?php echo $row['id'] ?>">
							</div>
							</td>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['code'] ?></td>
							<td><?php echo $row['name'] ?></td>
							<td><?php echo $row['date_sched'] ?></td>
							<td class=""><p class='m-0'><?php echo $row['location'] ?></p></td>
							<td class="text-center">
								<?php 
								switch($row['status']){ 
									case(0): 
										echo '<span class="badge badge-primary">Scheduled</span>';
									break; 
									case(1): 
									echo '<span class="badge badge-success">Done</span>';
									break; 
									case(2): 
										echo '<span class="badge badge-danger">No Show</span>';
									break; 
									default: 
										echo '<span class="badge badge-secondary">NA</span>';
                                } 
								?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['code'] ?>"> View</a>
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
		$('.view_data').click(function(){
			uni_modal("Indiviual Details","individual/view_details.php?code="+$(this).attr('data-id'))
		})
		$('#selectAll').change(function(){
			// if($(this).is(":checked") == true){
			// 	$('.invCheck').prop("checked",true)
			// }else{
			// 	$('.invCheck').prop("checked",false)
			// }
			var _this = $(this)
			count = indiList.api().rows().data().length
			for($i = 0 ; $i < count; $i++){
				var node = indiList.api().row($i).node()
				console.log($(node).find('.invCheck'))
				if(_this.is(":checked") == true){
					$(node).find('.invCheck').prop("checked",true)
					$('#selected_opt').show('slow')
				}else{
					$(node).find('.invCheck').prop("checked",false)
					$('#selected_opt').hide('slow')
				}
			}
		})
		
	})
	$(function(){
		indiList = $('#indi-list').dataTable({
			columnDefs:[{
				targets:[0,7],
				orderable:false
			}],
			order:[[1,'asc']],
		});
		// console.log(indiList)
		$(indiList.fnGetNodes()).find('.invCheck').change(function(){
			if($(this).is(":checked")==true){
				if($('#selected_opt').is(':visible') == false){
					$('#selected_opt').show('slow')
				}
				
			}else{
				if($(indiList.fnGetNodes()).find('.invCheck:checked').length <= 0){
					if($('#selected_opt').is(':visible') == true){
						$('#selected_opt').hide('slow')
					}
				}
			}
			if($(indiList.fnGetNodes()).find('.invCheck:checked').length == $(indiList.fnGetNodes()).find('.invCheck').length){
				$('#selectAll').prop('checked',true)
			}else if($(indiList.fnGetNodes()).find('.invCheck:checked').length <= 0){
				$('#selectAll').prop('checked',false)
			}else{
				$('#selectAll').prop('checked',false)
			}
		})

		$('#selected_go').click(function(){
			start_loader();
			var ids = [];
			$(indiList.fnGetNodes()).find('.invCheck:checked').each(function(){
				ids.push($(this).val())
			})
			var _action = $('#w_selected').val()
			$.ajax({
				url:_base_url_+'classes/Master.php?f=multiple_action',
				method:"POST",
				data:{ids:ids,_action:_action},
				dataType:'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
						alert_toast(resp.msg,'error');
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})
</script>