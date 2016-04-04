        <div id="planning" class="container_info hide">
          <div class="row divider">
            <div class="col-sm-12">
              <p class="title_section">Planeación</p>
              <h1><?php echo $elcontrato->planning->budget;?></h1>
              <h2><?php echo $elcontrato->tender->id;?></h2>
            </div>
          </div>
          <div class="row divider">
            <div class="col-sm-6">
              <p class="title_section">PRESUPUESTO (<?php echo $elcontrato->planning->currency;?>)</p>
              <h2 class="amount"><span>$</span><?php echo number_format($elcontrato->planning->amount,2,'.',',');?></h2>
            </div>
            <div class="col-sm-6">
              <p class="title_section">Fecha</p>
              <?php $time_planning = strtotime($elcontrato->date);?>
              <p><?php echo date('d/m/Y',$time_planning);?> </p>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <p class="title_section">COMPRADOR</p>
              <p><a href="/dependencia.php"><?php echo $elcontrato->buyer;?></a></p>
            </div>
          </div>
        </div>
