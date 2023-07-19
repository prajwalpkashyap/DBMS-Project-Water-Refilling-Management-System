<h1 class="text-dark">Welcome to <?php echo $_settings->info('name') ?></h1>
<?php
?>
<hr>
<div class="container-fluid">
<div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hand-holding-water"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Sales Today</span>
                <span class="info-box-box text-right">
                  <?php 
                    echo number_format($conn->query("SELECT sum(amount) as total FROM sales where date(date_created) = '".(date('Y-m-d'))."' ")->fetch_array()['total']);
                  ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          
</div>
