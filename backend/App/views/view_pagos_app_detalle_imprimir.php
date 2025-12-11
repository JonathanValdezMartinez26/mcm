<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="card col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="row">
                        <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>
                                <div class="count" style="font-size: 18px"><?= $Ejecutivo ?></div>
                            </div>
                            <div class="col-md-2 col-sm-2  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i>#</i> de Pagos Validados</span>
                                <div class="count" style="font-size: 30px; color: #030303"><span style="font-size: 30px; color: #030303" id="validados_r" name="validados_r"><?= $DetalleGlobal['TOTAL_VALIDADOS']; ?></span> DE <span style="font-size: 30px; color: #030303" id="total_r" name="total_r"><?= $DetalleGlobal['TOTAL_PAGOS']; ?></span></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Monto Validado</span>
                                <div class="count" style="font-size: 35px; color: #368a05">$<?= number_format($DetalleGlobal['TOTAL'], 2); ?></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Terminar Validación</span>
                                <div class="count" style="font-size: 35px; color: #368a05">
                                    <? if ($pagos_efectivo) { ?>
                                        <button type="button" id="recibo_pagos" class="btn btn-primary" style="border: 1px solid #338300; background: #40a200;" data-keyboard="false">
                                            <i class="fa fa-print" style="color: #ffffff"></i> <span style="color: #ffffff"> Imprimir Recibo de Pagos</span>
                                        </button>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dataTable_wrapper">
                        <hr>
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                                <tr>
                                    <th>Secuencia</th>
                                    <th>Cliente</th>
                                    <th>Movimiento</th>
                                    <th>Comentarios</th>
                                    <th>Fecha Captura</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $tabla; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>