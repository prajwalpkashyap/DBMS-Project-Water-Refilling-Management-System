<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `sales` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
}
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
    <div class="card-header">
    <h3 class="card-title"><?php echo !isset($id) ? "Create New" : "Manage" ?> Sale</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form id="sales-form">
                <input type="hidden" name="id" value='<?php echo isset($id)? $id : '' ?>'>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="control-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" id="customer_name" value="<?php echo isset($customer_name) ?  $customer_name : "Guest" ?>" reqiured>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label">Type</label>
                        <select  id="type" name="type" class="custom-select select2">
                            <option value="1" <?php echo isset($type) && $type == 1 ? "selected" : "" ?>>Walk-In</option>
                            <option value="2" <?php echo isset($type) && $type == 2 ? "selected" : "" ?>>For Delivery</option>
                        </select>

                    </div>
                </div>
                <div class="row" style="display:none" id="da-holder">
                    <div class="form-group col-sm-6">
                        <label class="control-label">Delivery Address</label>
                        <textarea type="text" rows="2" style="resize:none" name="delivery_address" class="form-control" id="delivery_address"><?php echo isset($delivery_address) ?  $delivery_address : "" ?></textarea>
                    </div>
                </div>
                <hr>
                <div class="row align-items-end">
                    <div class="form-group col-sm-4">
                        <label class="control-label">Jar Type</label>
                        <select  id="jar_type_id" class="custom-select select2">
                            <option value=""></option>
                            <?php 
                            $j_qry = $conn->query("SELECT * FROM jar_types order by `name` asc");
                            while($row=$j_qry->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>" data-price="<?php echo $row['pricing'] ?>" ><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Quantity</label>
                        <input type="number" min="1" class="form-control" id="quantity">
                    </div>
                    <div class="col-sm-2 mb-3">
                        <button class="btn btn-primary btn-flat" type="button" id="add_to_list"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
                <hr>
                <table class="table table-bordered table-striped" id="item-list">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="30%">
                        <col width="25%">
                        <col width="25%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th></th>
                            <th>QTY</th>
                            <th>Jar Type</th>
                            <th>Price</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $qry2 = $conn->query("SELECT i.*,j.name FROM `sales_items` i inner join `jar_types`j on j.id = i.jar_type_id where  i.sales_id = '{$id}' order by id asc ");
                        while($row= $qry2->fetch_assoc()):
                        ?>
                        <tr class="s-item">
                            <td class='text-center'><button class='btn btn-default text-danger' type='button' onclick='del_item($(this))'><i class='fa fa-times'></i></button></td>
                            <td class='text-center'><input type='hidden' name='quantity[]' value='<?php echo $row['quantity'] ?>'><?php echo number_format($row['quantity']) ?></td>
                            <td class='text-center'><input type='hidden' name='jar_type_id[]' value='<?php echo $row['jar_type_id'] ?>'><?php echo $row['name'] ?></td>
                            <td class='text-center'><input type='hidden' name='price[]' value='<?php echo $row['price'] ?>'><?php echo number_format($row['price']) ?></td>
                            <td class='text-center'><input type='hidden' name='total_amount[]' value='<?php echo $row['total_amount'] ?>'><?php echo number_format($row['total_amount']) ?></td>
                        </tr>
                        <?php
                            endwhile;
                            endif;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan='4' class="text-center">Total <input type="hidden" name="amount" value="<?php echo isset($amount)? $amount : 0 ?>"> </th>
                            <th class="text-right" id="grand_total"><?php echo isset($amount)? $amount : 0 ?></th>
                        </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="form-group col-sm-4">
                    <label class="control-label">Payment Status</label>
                    <select  id="status" name="status" class="custom-select">
                        <option value="0" <?php echo isset($status) && $status == 0 ? "selected" : "" ?>>Unpaid</option>
                        <option value="1" <?php echo isset($status) && $status == 1 ? "selected" : "" ?>>Paid</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex w-100">
            <button form="sales-form" class="btn btn-primary mr-2">Submit</button>
            <a href="./?page=sales" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
<script>
    function calculate_total(){
        total = 0 ; 
        $('.s-item').each(function(){
            var amount = $(this).find('[name="total_amount[]"]').val()
            total += parseFloat(amount)
        })
        $('#grand_total').text(parseFloat(total).toLocaleString('en-US'))
        $('[name="amount"]').val(total)
    }
    function del_item(_this){
        _this.closest('tr').remove()
        calculate_total();
    }
    $(function(){
        if('<?php echo isset($id) ? 1 : 0 ?>' == 1){
            calculate_total()
            if($('#type').val() == 1){
                $('#da-holder').hide('slow')
                $('#delivery_address').attr('required',false)
            }else{
                $('#da-holder').show('slow')
                $('#delivery_address').attr('required',true)
            }
        }
        $('#type').change(function(){
            if($(this).val() == 1){
                $('#da-holder').hide('slow')
                $('#delivery_address').attr('required',false)
            }else{
                $('#da-holder').show('slow')
                $('#delivery_address').attr('required',true)
            }
        })
       $('.select2').select2();
       $('#add_to_list').click(function(){
            var jar_type_id = $('#jar_type_id').val();
            var quantity = $('#quantity').val();
            if(jar_type_id == ''){
                alert_toast(' Please Select Jar Type first',"warning");
                return false;
            }
            if(quantity <= 0){
                alert_toast(' Please enter valid quantity',"warning");
                return false;
            }
            var jar_type = $('#jar_type_id option[value="'+jar_type_id+'"]').text()
            var price = $('#jar_type_id option[value="'+jar_type_id+'"]').attr('data-price')

            var amount = parseFloat(quantity) * parseFloat(price);
            var tr = $('<tr class="s-item">')
            tr.append("<td class='text-center'><button class='btn btn-default text-danger' type='button' onclick='del_item($(this))'><i class='fa fa-times'></i></button></td>")
            tr.append("<td class='text-center'><input type='hidden' name='quantity[]' value='"+quantity+"'>"+(parseFloat(quantity).toLocaleString("en-US"))+"</td>")
            tr.append("<td class='text-center'><input type='hidden' name='jar_type_id[]' value='"+jar_type_id+"'>"+(jar_type)+"</td>")
            tr.append("<td class='text-center'><input type='hidden' name='price[]' value='"+price+"'>"+(parseFloat(price).toLocaleString("en-US"))+"</td>")
            tr.append("<td class='text-center'><input type='hidden' name='total_amount[]' value='"+amount+"'>"+(parseFloat(amount).toLocaleString("en-US"))+"</td>")
            $('#item-list tbody').append(tr);
            calculate_total();
            $('#quantity').val('');
            $('#jar_type_id').val('').trigger('change');
       })
       
        $('#sales-form').submit(function(e){
            e.preventDefault()
            if($('.s-item').length < 1){
                alert_toast(" Please add atleast 1 Item in the list.","warning");
                return false;
            }
            start_loader()
            if($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url:_base_url_+'classes/Master.php?f=save_sales',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured","error")
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.href = './?page=sales';
                    }else if(!!resp.msg){
                         var msg = $('<div class="err_msg"><div class="alert alert-danger">'+resp.msg+'</div></div>')
                         $('#sales-form').prepend(msg) 
                         msg.show('slow')
                    }else{
                        alert_toast('An error occured',"error")
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
    })
</script>