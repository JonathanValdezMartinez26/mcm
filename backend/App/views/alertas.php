<?php echo $header;?>
<!--/Header-->
<!--Body-->
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">

      <div class="x_title">
        <h2>Alerta: <?php echo $titulo; ?></h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a href="<?php echo $regreso; ?>"><strong style="color: green;">Regresar</strong></a></li>
        </ul>
        <div class="clearfix"></div>
      </div>

      <div class="x_content">
        <br />
        <?php echo $mensaje ?>
      </div>
    </div>
  </div>
</div>
<!--/Body-->
<!--Footer-->
<?php echo $footer;?>
<!--/Footer-->
