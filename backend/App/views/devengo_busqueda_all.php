<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reactivar crédito y recalcular intereses devengados</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito</h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Devengo/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" min="1" aria-label="Search" value="<?php echo $credito; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="Ciclo" name="Ciclo" placeholder="00" aria-label="Search" min="1" value="<?php echo $ciclo; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                    <i class="fa fa-toggle-on"></i> Reactivar Crédito
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion['NOMBRE']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Número del Crédito</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion['CDGCLNS']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Ciclo del Crédito</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion['CICLO']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['COD_SUCURSAL']; ?> |  <?php echo $Administracion['NOM_SUCURSAL']; ?> </div>
                            <span class="count_top" style="font-size: 15px"> Región</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion['REGION']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Periodo</span>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> INICIO: <?php echo $Administracion['INICIO']; ?></div>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> FIN: <?php echo $Administracion['FIN']; ?></div>
                        </div>

                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> FECHA DE CIERRE: <?php echo $Administracion['FECHA_LIQUIDA']; ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
