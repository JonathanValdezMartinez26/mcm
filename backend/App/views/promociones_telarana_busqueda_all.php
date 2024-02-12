<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">


            <div class="row">
                <div class="col-md-5">
                    <div class="panel panel-body" style="margin-bottom: 7px;">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #787878;color: white" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <strong>
                                                                Identificación del Cliente que Recomienda
                                                            </strong>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px;  !important;" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12" style="padding-top: 11px">
                                                            <b><?php echo $Recomienda['NOMBRE']; ?> (<?php echo $Recomienda['CL_INVITA']; ?>) </b>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="10"><strong>Crédito</strong></td>
                                                <td style="font-size: 16px" colspan="3"><strong>Ciclo Actual</strong></td>
                                                <td style="font-size: 16px" colspan="5"><strong>Sucursal</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 19px" ;="" colspan="10"><?php echo $Recomienda['CDGNS']; ?></td>
                                                <td style="font-size: 19px; font: " colspan="3">
                                                    <?php echo $Recomienda['CICLO']; ?>
                                                </td>
                                                <td style="font-size: 16px;" colspan="5">
                                                    <?php echo $Recomienda['SUCURSAL']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 18px; background: #cccccc;color: #707070" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            El cliente tiene un total de  <strong>
                                                                <span class="label label-warning" style="font-size: 95% !important; border-radius: 50em !important;" align="right">0</span>
                                                            </strong>
                                                            atrasos en sus pagos del ciclo actual.
                                                        </div>

                                                        <div class="col-md-4">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                        <div style="display:none;">
                                            <input class="form-check-input" type="checkbox" value="" id="check_2610" name="check_2610" onclick="check_2610('');">
                                            <label class="form-check-label" for="flexCheckDefault" style="font-size: 15px">
                                                Información Inconsistente
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Fechas del crédito </h4></span>
                    <div class="panel panel-body" style="padding: 0px">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">

                                        <div class="col-md-12 col-sm-12  tile_stats_count">
                                            <div class="count" style="font-size: 17px"> <i class="fa fa-calendar"></i> Fecha de Inicio: <?php echo $Recomienda['INICIO']; ?></div>
                                            <div class="count" style="font-size: 17px"> <i class="fa fa-calendar"></i> Fecha de Fin: <?php echo $Recomienda['FIN']; ?></div>
                                            <div class="count" style="font-size: 17px"> <i class="fa fa-calendar"></i> Plazo: <?php echo $Recomienda['PLAZO']; ?> semanas.</div>
                                            <div class="count" style="font-size: 17px"> <i class="fa fa-calendar"></i> Semanas trancurridas: <?php echo $Semanas ?>.</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Recomienda mas Paga Menos </h4></span>
                    <div class="panel panel-body" style="padding: 0px">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">

                                        <?php echo $Promocion_estatus; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-12">
                    <div class="panel panel-body" style="margin-bottom: 7px;">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #73879C;color: white" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <strong>
                                                                Historial de clientes invitados por <?php echo $Recomienda['NOMBRE']; ?>
                                                            </strong>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="10">
                                                    <div class="dataTable_wrapper">
                                                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                                            <thead>
                                                            <tr>
                                                                <th>Codigo del Cliente</th>
                                                                <th>Nombre Completo del Cliente Invitado</th>
                                                                <th>Total del Prestamo</th>
                                                                <th>Descuento por promoción</th>
                                                                <th>Lo recomendo en</th>
                                                                <th>Estatus de pago</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?= $tabla_clientes; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="font-size: 18px; background: #cccccc;color: #707070" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12" style="color: #707070">
                                                            MCM

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
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
