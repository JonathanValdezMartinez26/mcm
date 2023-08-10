<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Consulta de Clientes Cultiva (PLD)</h3>
            <div class="clearfix"></div>
        </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tienen permitido hasta 30 días por consulta</h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/IdentificacionClientes/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $fechaActual; ?>">
                                    <span id="availability1" >Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $fechaActual; ?>">
                                    <span id="availability1" >Hasta</span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
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
                            <label style="font-size: 14px; color: black;">Clientes no encontrados:</label> <li style="color: black;">Valide que las fechas sean correctas</li> <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico</li>
                            <br>
                            <a href="/Pagos/" class="alert-link">Regresar</a>.
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
