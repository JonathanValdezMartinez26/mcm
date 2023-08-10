<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reactivar crédito y recalcular intereses devengados</h3>
                <h5> Preguntar si ya se realizo el corte del día para poder realizar un devengo</h5>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito y ciclo</h5>
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
                <button type="button" class="btn btn-primary" onclick="BotonReactivar('<?php echo $Administracion[1]['HOY_ES']; ?>', '<?php echo $Administracion[0]['CDGCLNS']; ?>', '<?php echo $Administracion[0]['CICLO']; ?>', '<?php echo $Administracion[0]['INICIO']; ?>', '<?php echo $Administracion[1]['INTERES_DIARIO']; ?>', '<?php echo $Administracion[1]['DDD_FINAL']; ?>', '<?php echo $Administracion[1]['INT_DEV']; ?>', '<?php echo $Administracion[1]['DEV_DIARIO_SIN_IVA']; ?>', '<?php echo $Administracion[1]['IVA_INT']; ?>', '<?php echo $Administracion[1]['PLAZO']; ?>', '<?php echo $Administracion[1]['PLAZO_DIAS']; ?>', '<?php echo $Administracion[0]['FIN']; ?>');">
                    <i class="fa fa-toggle-on"></i> Reactivar Crédito
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['NOMBRE']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Número del Crédito</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['CDGCLNS']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Ciclo del Crédito</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['CICLO']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['COD_SUCURSAL']; ?> |  <?php echo $Administracion[0]['NOM_SUCURSAL']; ?> </div>
                            <span class="count_top" style="font-size: 15px"> Región</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['REGION']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Periodo</span>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> INICIO: <?php echo $Administracion[0]['INICIO']; ?></div>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> FIN: <?php echo $Administracion[0]['FIN']; ?></div>
                        </div>

                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> FECHA DE CIERRE: <?php echo $Administracion['FECHA_LIQUIDA']; ?></div>
                        </div>

                    </div>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <h4> Resumen del devengo</h4>
                <br>
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-calendar"></i> Fecha de liquidación</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[1]['LIQUIDARON_EL_DIA']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Se van a devengar interes al día</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[1]['HOY_ES']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Total de días</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[1]['DIAS_SIN_DEVENGO']; ?> DÍAS</div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés Global</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['INTERES_GLOBAL']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés Diario</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['INTERES_DIARIO']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Plazo de días</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[1]['PLAZO_DIAS']; ?> DÍAS</div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Dev diario sin IVA</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['DEV_DIARIO_SIN_IVA']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> IVA de intereses</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['IVA_INT']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Debe en total</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['DEBE']; ?> </div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Consecutivo</span>
                            <div class="count" style="font-size: 17px"><?php echo $Administracion[1]['DDD_FINAL']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés Calculado</span>
                            <div class="count" style="font-size: 17px">$<?php echo $Administracion[1]['INT_DEV']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
