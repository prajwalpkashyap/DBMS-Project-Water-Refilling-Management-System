<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Sales</h3>
		<div class="card-tools">
			<a href="?page=sales/manage_sales" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped" id="indi-list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Date Created</th>
						<th>Customer</th>
						<th>Type</th>
						<th>Status</th>
						<th>Total Items</th>
						<th>Amount</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `sales` t order by unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
							$item_count = $conn->query("SELECT sum(quantity) as total FROM sales_items where sales_id = '{$row['id']}' ")->fetch_array()['total'];
					?>
					
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['date_created'] ?></td>
							<td><?php echo $row['customer_name'] ?></td>
							<td><?php echo ($row['type'] == 1)? "walk-in" : "for delivery" ?>
							<td class="text-center"><?php echo ($row['status'] == 1)? "<span class='badge badge-success'>Paid</span>" : "<span class='badge badge-primary'>Unpaid</span>" ?>
							<td class='text-right'><?php echo number_format($item_count) ?></td>
							<td class='text-right'><?php echo number_format($row['amount'],2) ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="./?page=sales/manage_sales&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>"> Edit</a>
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
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Sales Transaction ?","delete_sales",[$(this).attr('data-id')])
		})
	})
	function delete_sales($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sales",
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
		$('#indi-list').dataTable()
	})
</script>