<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Control de Garantías</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5" >
            <div class="card-header">
                <h5 class="card-title">Ingrese el número de crédito</h5>
            </div>
            <div class="card-body">
                <form class="" action="/Creditos/ControlGarantias/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            <div class="row" >
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                    <div class="x_content">
                        <br />
                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <label style="font-size: 14px; color: black;">Crédito no encontrado:</label> <li style="color: black;">Valide que el número de crédito sea correcto</li> <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico</li>
                            <br>
                            <a href="/Creditos/ControlGarantias/" class="alert-link">Regresar</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</div>

<?php echo $footer; ?>
