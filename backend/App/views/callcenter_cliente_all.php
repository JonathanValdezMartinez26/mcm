<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta Información de Clientes (Call Center)</h3>
                <div class="clearfix"></div>
            </div>
                <div class="x_content">

                    <div class="card card-danger col-md-5" >
                        <div class="card-header">
                            <h5 class="card-title">Ingrese el número de crédito y ciclo</h5>
                        </div>
                        <div class="card-body">
                            <form class="" action="/CallCenter/Consultar/" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" style="font-size: 25px;" autofocus type="number" id="Ciclo" name="Ciclo" placeholder="00" aria-label="Search" value="<?php echo $ciclo; ?>">
                                    <span id="availability1"></span>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
        <div class="row">

            <div class="col-md-10">
                <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Crédito</h4></span>
                <div class="panel panel-body" style="padding: 0px">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                                        <div class="col-md-3 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="fa fa-dollar"></i> Monto</span>

                                            <div class="count" style="font-size: 15px"><?php echo $Administracion['MONTO']; ?></div>
                                        </div>
                                        <div class="col-md-3 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="fa fa-clock-o"></i> Plazo</span>
                                            <div class="count" style="font-size: 15px"><?php echo $Administracion['PLAZO']; ?> rer</div>
                                        </div>
                                        <div class="col-md-3 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i></i> Parcialidad</span>
                                            <div class="count" style="font-size: 15px"> $ <?php echo number_format($Administracion['PARCIALIDAD']); ?></div>
                                        </div>
                                        <div class="col-md-3 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Día de Pago</span>
                                            <div class="count" style="font-size: 15px"><?php echo $Administracion['DIA_PAGO']; ?></div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2" style="padding-top: 33px">
                <div class="panel panel-body" style="padding: 0px">
                    <div class="x_content">
                        <div class="col-sm-12 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-12 text-center text-sm-left ">
                                <img src="https://cdn-icons-png.flaticon.com/512/3281/3281312.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <span class="badge" style="background: #73879C"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Cliente</h4></span>
                <div class="panel panel-body">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 18px; background: #787878;color: white" colspan="6"><strong>Identificación</strong></td>
                                        </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Nombre</strong></td>
                                                <td style="font-size: 16px"><strong>Fecha de Nacimiento</strong></td>
                                                <td style="font-size: 16px"><strong>Edad</strong></td>
                                                <td style="font-size: 16px"><strong>Sexo</strong></td>
                                                <td style="font-size: 16px"><strong>Edo. Civil</strong></td>
                                                <td style="font-size: 16px"><strong>Telefono</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px">MA MARGARITA VICTORIA SAMANIEGO RICARDO</td>
                                                <td style="font-size: 16px">23/12/1962</td>
                                                <td style="font-size: 16px">60</td>
                                                <td style="font-size: 16px">F</td>
                                                <td style="font-size: 16px">VIUDO</td>
                                                <td style="font-size: 16px"></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="6"><strong>Actividad Economica</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="6">BAZAR</td>
                                            </tr>
                                        </tbody>
                                </table>
                                </div>

                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Calle</strong></td>
                                                <td style="font-size: 16px"><strong>Colonia</strong></td>
                                                <td style="font-size: 16px"><strong>Localidad</strong></td>
                                                <td style="font-size: 16px"><strong>Municipio</strong></td>
                                                <td style="font-size: 16px"><strong>Estado</strong></td>
                                                <td style="font-size: 16px"><strong>CP</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px">SAN JOAQUIN 16</td>
                                                <td style="font-size: 16px">SN JUAN CUAUTLANCIN</td>
                                                <td style="font-size: 16px">Cuautlancingo</td>
                                                <td style="font-size: 16px">Cuautlancingo</td>
                                                <td style="font-size: 16px">PUEBLA</td>
                                                <td style="font-size: 16px">72764</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <span class="badge" style="background: #73879C"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Aval</h4></span>
                <div class="panel panel-body">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 18px; background: #787878;color: white" colspan="6"><strong>Identificación</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px"><strong>Nombre</strong></td>
                                            <td style="font-size: 16px"><strong>Fecha de Nacimiento</strong></td>
                                            <td style="font-size: 16px"><strong>Edad</strong></td>
                                            <td style="font-size: 16px"><strong>Sexo</strong></td>
                                            <td style="font-size: 16px"><strong>Edo. Civil</strong></td>
                                            <td style="font-size: 16px"><strong>Telefono</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px">MA MARGARITA VICTORIA SAMANIEGO RICARDO</td>
                                            <td style="font-size: 16px">23/12/1962</td>
                                            <td style="font-size: 16px">60</td>
                                            <td style="font-size: 16px">F</td>
                                            <td style="font-size: 16px">VIUDO</td>
                                            <td style="font-size: 16px"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="6"><strong>Actividad Economica</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="6">BAZAR</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 16px"><strong>Calle</strong></td>
                                            <td style="font-size: 16px"><strong>Colonia</strong></td>
                                            <td style="font-size: 16px"><strong>Localidad</strong></td>
                                            <td style="font-size: 16px"><strong>Municipio</strong></td>
                                            <td style="font-size: 16px"><strong>Estado</strong></td>
                                            <td style="font-size: 16px"><strong>CP</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px">SAN JOAQUIN 16</td>
                                            <td style="font-size: 16px">SN JUAN CUAUTLANCIN</td>
                                            <td style="font-size: 16px">Cuautlancingo</td>
                                            <td style="font-size: 16px">Cuautlancingo</td>
                                            <td style="font-size: 16px">PUEBLA</td>
                                            <td style="font-size: 16px">72764</td>
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
