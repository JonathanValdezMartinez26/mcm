<?php echo $header;?>
<!--/Header-->
<!--Body-->
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">

      <div class="x_title">
        <h2>Alerta <?php echo $titulo; ?>:</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a href="<?php echo $regreso; ?>">Regresar</a></li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <br />
        <div class="alert alert-<?php echo $class; ?> alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $mensaje; ?>
          <?php $redireccion ?>
          <?php
          ob_start();
          //header("refresh: 3; url = $regreso");
          header("url = $regreso");
          ob_end_flush();
          ?>
          <a href="<?php echo ($regreso) ? $regreso : '/'?>" class="alert-link">Regreso</a>.
        </div>
      </div>
    </div>
  </div>
</div>
<!--/Body-->
<!--Footer-->
<?php echo $footer;?>
<!--/Footer-->
