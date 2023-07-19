<?php 
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `jar_types` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
}
}
?>
<div class="container-fuid">
    <form id="maintenance-form">
        <input type="hidden" name="id" value='<?php echo isset($id)? $id : '' ?>'>
        <div class="form-group">
            <label class="control-label">Name</label>
            <input type="text" class="form-control col-sm-8" name="name" value="<?php echo isset($name)? $name : '' ?>" required>
        </div>
        <div class="form-group">
            <label class="control-label">Description</label>
            <textarea type="text" class="form-control summernote" name="description" required><?php echo isset($description)? stripslashes(html_entity_decode($description)) : '' ?></textarea>
        </div>
        <div class="form-group">
            <label class="control-label">Price</label>
            <input type="number" min="0" class="form-control col-sm-8 text-right" name="pricing" value="<?php echo isset($pricing)? $pricing : '' ?>" required>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#maintenance-form').submit(function(e){
            e.preventDefault()
            start_loader()
            if($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url:_base_url_+'classes/Master.php?f=save_jar_types',
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
                        location.href="./?page=maintenance"
                    }else if(!!resp.msg){
                         var msg = $('<div class="err_msg"><div class="alert alert-danger">'+resp.msg+'</div></div>')
                         $('#maintenance-form').prepend(msg) 
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