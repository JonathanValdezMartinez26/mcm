<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <div class="right_col" role="main">
      <div class="col-middle">
        <div class="text-center">
          <h1 class="error-number"><?php echo $error; ?></h1>
          <h2><?php echo $tituloError; ?></h2>
          <p><?php echo $mensajeError; ?></p>
          </p>
          <div class="mid_center">
            <form name="all" id="all" action="/ResumenQuincenal/generarExcel" method="POST" >

            <?php echo $codigo; ?>
<!--
              <div class="form-group" <?= $visualizar?> >
                <input type="hidden" id="periodo_id" name="periodo_id" value="<?= $periodo_id?>" />
                <input type="hidden" id="tipo_periodo" name="tipo_periodo" value="<?= $tipo_periodo?>" />
                <input type="hidden" id="mensaje" name="mensaje" value="<?= $mensaje?>" />

                <div class="col-md-3 col-sm-3 col-xs-3">
                  <button class="btn btn-danger" id="btnCancelarPeriodo" type="button" <?= $visible_admin?>>Cancelar</button>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <button class="btn btn-warning" id="btnRespaldarPeriodo" type="button" <?= $visible_admin?> >Respaldar</button>
                </div>
              </div>
-->

            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


<?php echo $footer; ?>