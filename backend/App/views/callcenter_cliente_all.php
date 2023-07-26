<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 7px;">
            <div class="">

                    <div class="box-tools pull-left" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                        <h3> Validación de Cliente y Aval</h3>
                    </div>
                <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <a type="button" href="/CallCenter/Pendientes/" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Regresar a mis pendientes</a>
                    </div>
                </div>
            </div>


        </div>

        <div class="row">

            <div class="col-md-10">
                <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Crédito | <i class="fa fa-user"></i> <?php echo $Administracion[0]['CLIENTE']; ?></h4></span>
                <div class="panel panel-body" style="padding: 0px">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                                    <div class="col-md-2 col-sm-4  tile_stats_count" style="padding-bottom: 1px !important; margin-bottom: 1px !important;">
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
                <div class="panel panel-body" style="margin-bottom: 7px;">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 18px; background: #787878;color: white" colspan="7">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <strong>
                                                            Identificación del Cliente
                                                        </strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>
                                                            <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important;" align="right">Pendiente 1 llamada</span>
                                                        </strong>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="6"><b><?php echo $Administracion[0]['CLIENTE']; ?></b></td>
                                            <td style="font-size: 16px" colspan="1">
                                                <button type="button" class="btn btn-primary" style="border: 1px solid #c4a603; background: #FFFFFF" data-toggle="modal" data-target="#modal_detalle_cliente" data-backdrop="static" data-keyboard="false" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                                                    <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Ver Expediente (CLIENTE)</label>
                                                </button>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="1"><strong>Encuesta *</strong></td>
                                            <td style="font-size: 16px" colspan="1"><strong>Contacto</strong></td>
                                            <td style="font-size: 16px" colspan="5"><strong>Estatus Encuesta</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 19px; font: " colspan="1">
                                                No se ha iniciado
                                            </td>
                                            <td style="font-size: 19px" colspan="1"><i class="fa fa-phone-square"></i> <?php
                                                $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],5,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                                echo $format; ?>
                                            </td>
                                            <td style="font-size: 16px" colspan="5">
                                                <button type="button" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_agregar_pago" data-backdrop="static" data-keyboard="false" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                                                    <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63">Iniciar con la Encuesta (CLIENTE)</label>
                                                </button>
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

            <div class="col-md-6">
                <div class="panel panel-body" style="margin-bottom: 7px;">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 18px; background: #73879C;color: white" colspan="7">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <strong>
                                                            Identificación del Aval
                                                        </strong>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>
                                                            <span class="label label-success" style="font-size: 95% !important; border-radius: 50em !important;" align="right">Validado en 1 llamada</span>
                                                        </strong>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="6"><?php echo $Administracion[0]['AVAL']; ?></td>
                                            <td style="font-size: 16px" colspan="1">
                                                <button type="button" class="btn btn-primary" style="border: 1px solid #c4a603; background: #FFFFFF" data-toggle="modal" data-target="#modal_agregar_pago" data-backdrop="static" data-keyboard="false" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                                                    <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Ver Expediente (AVAL)</label>
                                                </button>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px" colspan="1"><strong>Encuesta *</strong></td>
                                            <td style="font-size: 16px" colspan="1"><strong>Contacto</strong></td>
                                            <td style="font-size: 16px" colspan="5"><strong>Estatus Encuesta</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 19px" colspan="1">
                                                Finalizada
                                            </td>
                                            <td style="font-size: 19px" colspan="1"><i class="fa fa-phone-square"></i> <?php
                                                $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],5,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                                echo $format; ?>
                                            </td>
                                            <td style="font-size: 16px" colspan="5">
                                                <button type="button" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_agregar_pago" data-backdrop="static" data-keyboard="false" onclick="BotonPago('<?php echo $Administracion['SITUACION_NOMBRE']; ?>');">
                                                    <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63">Iniciar con la Encuesta (AVAL)</label>
                                                </button>
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

            <div class="col-md-12">
                <div class="panel panel-body" style="margin-bottom: 7px; margin-top: 15px;">
                    <div class="x_content">
                        <div class="col-sm-12">
                            <div class="card-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover">
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 18px; background: #440101;color: white" colspan="6"><strong>Mi Resumen ejecutivo para Call Center</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label for="Fecha">Comentarios Iniciales *</label>
                                            <textarea name="contenido" id="contenido" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios INICIALES una vez que hayas marcado al número del cliente o aval, por primera vez" style="background-color: white; resize: none"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-3">
                                            <label for="Fecha">Comentarios Finales *</label>
                                            <textarea name="contenido" id="contenido" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none"></textarea>
                                        </div>
                                    </div>

                                    <div  class="col-lg-3">
                                        <div class="form-group">
                                            <div>
                                                <label for="tipo"> Estatus Final de la Solicitud *</label>
                                                <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="SI">CANCELADA, NO LOCALIZADOS</option>
                                                    <option value="NO">CANCELADA POR CLIENTE</option>
                                                    <option value="NO">CANCELADA POR GERENTE</option>
                                                    <option value="NO">CANCELADA POR POLÍTICAS</option>
                                                    <option value="NO">CANCELADA POR GERENTE</option>
                                                    <option value="NO">LISTA CON OBSERVACIÓN</option>
                                                    <option value="NO">LISTA SIN INCIDENCIA</option>
                                                    <option value="NO">PENDIENTE</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div>
                                                <label for="tipo"> VoBo Gerente Regional (Opcional)</label>
                                                <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="SI">SI</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <button type="button" class="btn btn-success btn-lg" style="background: #2da92d; color: #ffffff; border: #1c4e63 4px solid; ">
                                            Terminar proceso de validación<br> y enviar estatus de la solicitud a Sucursal<br>Clic aquí <i class="fa fa-hand-pointer-o" style="color: #ffffff"></i>
                                        </button>
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
<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">CLIENTE</span>
                <center><h4 class="modal-title" id="myModalLabel"><?php echo $Administracion[0]['CLIENTE']; ?></h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Fecha">Fecha de trabajo</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="<?php echo $inicio_f; ?>" max="<?php echo $fin_f; ?>" value="<?php echo $fin_f; ?>">

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movil">Ciclo del Crédito</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="01">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movil">Número de telefono del cliente</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="<?php
                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],5,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                    echo $format; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Tipo de llamada que esta realizando</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="VOZ">VOZ</option>
                                        <option value="WHATSAPP">WHATSAPP</option>
                                        <option value="VIDEO LLAMADA">VIDEO LLAMADA</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <hr>
                            <h5><b>Preguntas de validación</b></h5>
                            <hr>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">1.- ¿Qué edad tiene?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">2.- ¿Cuál es su fecha de nacimiento?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">3.- ¿Cuál es su domicilio completo?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">4.- ¿Tiempo viviendo en este domicilio?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">5.- Actualmente, ¿Cuál es su principal fuente de ingresos?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">6.- Mencione, ¿Cuál es el nombre completo de su aval?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">7.- Mencione, ¿Qué relación directa tiene con su aval?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">8.- ¿ Cuál es la actividad económica de su aval?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">9.- Me proporciona el número telefónico de su aval</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">10.- ¿Firmó su solicitud?, ¿Cuándo firmo la solicitud?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">11.- Me puede indicar ¿para qué utilizará su crédito?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">12.- ¿Compartirá su crédito con alguna otra persona?</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                        <option selected disabled value="">Seleccione una opción</option>
                                        <option value="SI">RESPONDIO CORRECTAMENTE</option>
                                        <option value="NO">NO RESPONDIO</option>
                                    </select>
                                    <p style="color: #007700"><b>R: 18 años</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <hr>
                            <h5><b>Preguntas de validación</b></h5>
                            <hr>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalle_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">CLIENTE</span>
                <center><h4 class="modal-title" id="myModalLabel"><?php echo $Administracion[0]['CLIENTE']; ?></h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Fecha">Fecha de trabajo</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="<?php echo $inicio_f; ?>" max="<?php echo $fin_f; ?>" value="<?php echo $fin_f; ?>">

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movil">Ciclo del Crédito</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="01">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movil">Número de telefono del cliente</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="<?php
                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],5,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                    echo $format; ?>">
                                </div>
                            </div>
                        </div>

                    S
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
