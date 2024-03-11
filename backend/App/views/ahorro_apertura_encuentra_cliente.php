<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Apertura de cuentas</h3>
            </div>

            <div class="card col-md-12">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>
                            <br>
                            <span class="count_top badge" style="padding: 1px 1px; background: #21ac29"><h5><b><i class=""><?php echo $Cliente[0]['NOMBRE']; ?></i></b></h5></span>

                        </div>
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> CURP</span>
                            <br>
                            <span class="count_top badge" style="padding: 1px 1px; background: #21ac29"><h5><b><i class=""><?php echo $Cliente[0]['NOMBRE']; ?></i></b></h5></span>

                        </div>
                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>Medio</th>
                            <th>Consecutivo</th>
                            <th>CDGNS</th>
                            <th>Fecha</th>
                            <th>Ciclo</th>
                            <th>Monto</th>
                            <th>Tipo</th>
                            <th>Ejecutivo</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



<?php echo $footer; ?>
