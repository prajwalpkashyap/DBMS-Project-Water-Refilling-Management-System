<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<?php 
$type = isset($_GET['type'])? $_GET['type'] : 'all';
$date_start = isset($_GET['date_start'])? $_GET['date_start'] : date("Y-m-d",strtotime(date("Y-m-d")." -5 days"));
$date_end = isset($_GET['date_end'])? $_GET['date_end'] : date("Y-m-d");
?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Sales Report</h3>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<form id="filter-form" method="GET">
				<div class="row align-items-end">
					<div class="col-md-3">
						<div class="form-group">
							<label for="" class="control-label">Type</label>
							<select name="type" id="type" class="custom-select select-2">
								<option value="all" <?php echo $type == 'all' ? "selected" : "" ?>>All</option>
								<option value="1" <?php echo $type == 1 ? "selected" : "" ?>>Walk-In</option>
								<option value="2" <?php echo $type == 2 ? "selected" : "" ?>>Delivered</option>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="" class="control-label">Date Start</label>
							<input type="date" class="form-control" reqiured name="date_start" value="<?php echo date("Y-m-d",strtotime($date_start)) ?>">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="" class="control-label">Date End</label>
							<input type="date" class="form-control" reqiured name="date_end" value="<?php echo date("Y-m-d",strtotime($date_end)) ?>">
						</div>
					</div>
					<div class="col-md-3 ">
						<div class="form-group row w-100">
							<button class="btn btn-flat btn-primary mr-3"> Filter</button>
							<button class="btn btn-flat btn-success" id="print" type="button"> <i class="fa fa-print"></i> Print</button>
						</div>
					</div>
				</div>
			</form>
			<div id="print_out">
				<style>
					.details-tbl tr, .details-tbl td, .details-tbl th{
						border : unset !important;
					}
					.details-tbl td, .details-tbl th{
						padding :3px 5px !important;
					}
				</style>
			<table class="table table-bordered table-stripped" id="indi-list">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="10%">
					<col width="40%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Date</th>
						<th>Customer</th>
						<th>Type</th>
						<th>Details</th>
						<th>Total Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$total = 0 ;
					$where = '';
					$where = " where (date(date_created) BETWEEN '{$date_start}' and '{$date_end}') ";
					if($type > 0 && is_numeric($type)){
						$where .= " and `type` = '{$type}' ";
					}
					$qry = $conn->query("SELECT * from `sales` {$where} order by unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
							$total += $row['amount'];
					?>
					
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['customer_name'] ?></td>
							<td><?php echo ($row['type'] == 1) ? "Wailk-In" : "Delivered" ?></td>
							<td>
								<div class="w-100">
									<table class="w-100 details-tbl" >
										<?php 
										$sqry = $conn->query("SELECT i.*,j.name FROM `sales_items` i inner join jar_types j on j.id = i.jar_type_id where i.sales_id = '{$row['id']}' ");
										while($srow = $sqry->fetch_assoc()):	
										?>
										<tr>
											<td class="text-right"><small><?php echo $srow['quantity'] ?></td>
											<td><small><?php echo $srow['name'] ?></td>
											<td class="text-right"><small><?php echo $srow['total_amount'] ?></small></td>
										</tr>
										<?php endwhile; ?>
									</table>
								</div>
							</td>
							<td class="text-right"><?php echo number_format($row['amount']) ?></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" class="text-center">Total</th>
						<th class="text-right"><?php echo number_format($total) ?></th>
					</tr>
				</tfoot>
			</table>
			</div>

		</div>
	</div>
</div>
<script>
	$(function(){
		$('#filter-form').submit(function(e){
			e.preventDefault();
			location.href = "./?page=reports&type="+$('[name="type"]').val()+"&date_start="+$('[name="date_start"]').val()+"&date_end="+$('[name="date_end"]').val()
		})
		$('#print').click(function(){
			start_loader()
			var _h = $('head').clone()
			var _p = $('#print_out').clone();
			var el = $('<div>')
			el.append("<style>html *, body *{ height:auto !important;min-height:unset !important}</style>")
			el.append(_h)
			el.append("<h2 class='text-center m-0'><?php echo $_settings->info('name') ?></h2>")
			el.append("<h2 class='text-center m-0'>Sales Report</h2>")
			if('<?php echo $date_start ?>' == '<?php echo $date_end ?>')
				el.append("<h3 class='text-center m-0'>as of <?php echo date("F d, Y",strtotime($date_start)) ?></h3>");
			else
				el.append("<h3 class='text-center m-0'>as of <?php echo date("F d, Y",strtotime($date_start)). ' - '. date("F d, Y",strtotime($date_end)) ?></h3>");
			el.append('<hr>')
			el.append(_p)
			var nw = window.open("","_blank",'fullscreen=true')
					 nw.document.write(el.html())
					 nw.document.close()
					 setTimeout(function(){
						nw.print()
						setTimeout(() => {
							nw.close()
							end_loader()
						}, 300);
					 },200)
		})
	})
</script>