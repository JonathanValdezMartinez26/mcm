<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Validación de Cliente</h3>
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                        <div class="btn-group" data-toggle="btn-toggle">
                            <a type="button" href="/CallCenter/Pendientes/" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Regresar a mis pendientes</a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">

                <div class="col-md-10">
                    <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Crédito | <i class="fa fa-user"></i> <?php echo $Administracion[0]['CLIENTE']; ?></h4></span>
                    <div class="panel panel-body" style="padding: 0px">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="">#</i> Crédito</span>

                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['NO_CREDITO']; ?></div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"> Ciclo</span>

                                            <div class="count" style="font-size: 17px"> <?php echo $Administracion[0]['CICLO']; ?></div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="fa fa-dollar"></i> Monto</span>

                                            <div class="count" style="font-size: 17px">$ <?php echo number_format($Administracion[0]['MONTO']); ?></div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="fa fa-clock-o"></i> Plazo</span>
                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['PLAZO']; ?> semanas</div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i></i> Parcialidad</span>
                                            <div class="count" style="font-size: 17px"> $ <?php echo number_format($Administracion[0]['PARCIALIDAD']); ?></div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Día de Pago</span>
                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['DIA_PAGO']; ?></div>
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
                    <span class="badge" style="background: #787878"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Cliente</h4></span>
                    <div class="panel panel-body">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #787878;color: white" colspan="6"><strong> Identificación del Cliente</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Nombre</strong></td>
                                                <td style="font-size: 16px"><strong>Fec. Nac</strong></td>
                                                <td style="font-size: 16px"><strong>Edad</strong></td>
                                                <td style="font-size: 16px"><strong>Sexo</strong></td>
                                                <td style="font-size: 16px"><strong>Edo. Civil</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[0]['CLIENTE']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['NACIMIENTO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['EDAD']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['SEXO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['EDO_CIVIL']; ?></td>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="1"><strong>Contacto</strong></td>
                                                <td style="font-size: 16px" colspan="5"><strong>Actividad Económica</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 19px" colspan="1"><i class="fa fa-phone-square"></i> <?php
                                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],5,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                                    echo $format; ?>
                                                </td>
                                                <td style="font-size: 16px" colspan="5">BAZAR</td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #787878;color: white" colspan="6"><strong>Domicilio del Cliente</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Calle</strong></td>
                                                <td style="font-size: 16px"><strong>Colonia</strong></td>
                                                <td style="font-size: 16px"><strong>Localidad</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['CALLE']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['COLONIA']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['LOCALIDAD']; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Municipio</strong></td>
                                                <td style="font-size: 16px"><strong>Estado</strong></td>
                                                <td style="font-size: 16px"><strong>CP</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['MUNICIPIO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['ESTADO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[1]['CP']; ?></td>
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
                                                <td style="font-size: 18px; background: #73879C;color: white" colspan="6"><strong>Identificación del Aval</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Nombre</strong></td>
                                                <td style="font-size: 16px"><strong>Fec. Nac</strong></td>
                                                <td style="font-size: 16px"><strong>Edad</strong></td>
                                                <td style="font-size: 16px"><strong>Sexo</strong></td>
                                                <td style="font-size: 16px"><strong>Edo. Civil</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[0]['AVAL']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['NACIMIENTO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['EDAD']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['SEXO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['EDO_CIVIL']; ?></td>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="1"><strong>Contacto</strong></td>
                                                <td style="font-size: 16px" colspan="5"><strong>Actividad Económica</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 19px" colspan="1"><i class="fa fa-phone-square"></i> <?php
                                                    $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],5,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                                    echo $format; ?>
                                                </td>
                                                <td style="font-size: 16px" colspan="5">BAZAR</td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #73879C;color: white" colspan="6"><strong>Domicilio del Aval</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Calle</strong></td>
                                                <td style="font-size: 16px"><strong>Colonia</strong></td>
                                                <td style="font-size: 16px"><strong>Localidad</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['CALLE']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['COLONIA']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['LOCALIDAD']; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><strong>Municipio</strong></td>
                                                <td style="font-size: 16px"><strong>Estado</strong></td>
                                                <td style="font-size: 16px"><strong>CP</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['MUNICIPIO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['ESTADO']; ?></td>
                                                <td style="font-size: 16px"><?php echo $Administracion[2]['CP']; ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <span class="badge" style="background: #73879C"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Aval</h4></span>
                    <div class="panel panel-body">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-xs-6">

                                            <div class="small-box bg-green">
                                                <div class="inner">
                                                    <h3>¿Qué edad tiene?<sup style="font-size: 20px">%</sup></h3>
                                                    <p>Bounce Rate</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
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
