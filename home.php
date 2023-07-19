 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Welcome <?php echo $_settings->info('name') ?></h1>
        </div>
    </div>
</header>
<!-- Section-->
<?php 
$sched_arr = array();
$max = 0;
?>
<section class="py-5">
    <div class="container d-flex justify-content-center">
       <div class="card col-md-6 p-0">
           <div class="card-header">
               <div class="card-title text-center w-100">Login</div>
           </div>
           <div class="card-body">
               <form action="" id="login-client">
                    <div class="form-group">
                        <label for="email" class='control-label'>Email</label>
                        <input type="text" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class='control-label'>Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button class="btn btn-sm btn-primary btn-flat">Login</button>
                    </div>
               </form>
           </div>
       </div>
    </div>
</section>
<script>
</script>