<?php echo $header; ?>

<div class="right_col">
    <hr>
    <div class="col-md-4">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Identificación del cliente</h3>
            </div>

            <div class="card col-md-12">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="movil">Fecha de registro</label>
                            <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="">
                            <small id="emailHelp" class="form-text text-muted">Fecha de registro en sucursal</small>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="cdgns">Clave de cliente</label>
                            <input type="number" class="form-control" id="cdgns" name="cdgns" readonly="" value="003011">
                            <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">Nombre del cliente</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" readonly="" value="<?php echo $Cliente[0]['NOMBRE']; ?>">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="nombre">CURP</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" readonly="" value="<?php echo $Cliente[0]['CURP']; ?>">
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nombre">Edad</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" readonly="" value="27 Años">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">Dirección</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" readonly="" value="<?php echo $Cliente[0]['']; ?> SAN PEDRO DE LOS PINOS, CDMX">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">Actividad ecónomica</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" readonly="" value="<?php echo $Cliente[0]['']; ?> VENTA - TORTILLAS">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movil">¿Es cliente | crédito MCM?</label>
                            <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="3 CICLOS">
                            <small id="emailHelp" class="form-text text-muted">Estatus: <span style="color: #21ac29">activo | al corriente</span></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movil">Número de crédito MCM</label>
                            <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="003011">
                            <small id="emailHelp" class="form-text text-muted">Estatus: <span style="color: #21ac29">activo | al corriente</span></small>
                        </div>
                    </div>


                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movil">¿Es cliente | crédito MCM?</label>
                            <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="3 CICLOS">
                            <small id="emailHelp" class="form-text text-muted">Estatus: <span style="color: #21ac29">activo | al corriente</span></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movil">Número de crédito MCM</label>
                            <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="003011">
                            <small id="emailHelp" class="form-text text-muted">Estatus: <span style="color: #21ac29">activo | al corriente</span></small>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Apertura de cuenta ahorro corriente</h3>
            </div>
            <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura</b></p>
            <br>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Fecha">Fecha de apertura</label>
                        <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="2024-03-07" max="2024-03-11" value="2024-03-11">
                        <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="tipo">Tasa Anual</label>
                        <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                            <option value="P">5 %</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipo">Monto minimo</label>
                        <input onkeydown="return false" type="text" class="form-control" id="Fecha" name="Fecha" value="$100.00" readonly>
                        <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipo">Monto maximo</label>
                        <input onkeydown="return false" type="text" class="form-control" id="Fecha" name="Fecha" min="2024-03-07" max="2024-03-11" value="NO APLICA" readonly>
                        <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                    </div>
                </div>
                <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo">Beneficiarfio 1 </label>
                                <input onkeydown="return false" type="text" class="form-control" id="Fecha" name="Fecha" min="2024-03-07" max="2024-03-11" value="NO APLICA">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo">Parentesco Beneficiario 1</label>
                                <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                                    <option value="P">Padre/Madre</option>
                                    <option value="P">Esposo/Esposa</option>
                                    <option value="P">Hijo/Hija</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="tipo">Porcentaje Beneficiario 1</label>
                                <input onkeydown="return false" type="text" class="form-control" id="Fecha" name="Fecha" min="2024-03-07" max="2024-03-11">
                            </div>
                        </div>

                 </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="tipo">Beneficiarfio 2 </label>
                            <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                                <option value="P">50 (Cincuenta Pesos)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="tipo">Parentesco Beneficiario 2</label>
                            <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                                <option value="P">Padre/Madre</option>
                                <option value="P">Esposo/Esposa</option>
                                <option value="P">Hijo/Hija</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="tipo">Porcentaje Beneficiario 1</label>
                            <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                                <option value="P">Padre/Madre</option>
                                <option value="P">Esposo/Esposa</option>
                                <option value="P">Hijo/Hija</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Fecha">Sucursal</label>
                        <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                            <option value="P">SUCURSAL DE PRUEBA</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Fecha">Sucursal</label>
                        <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo">
                            <option value="P">SUCURSAL DE PRUEBA</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="Fecha">Manejo de cuenta</label>
                        <select class="form-control mr-sm-3" autofocus="" type="select" id="tipo" name="tipo" readonly="">
                            <option value="P">Aplica</option>
                        </select>
                    </div>
                </div>


                <div class="col-md-5"></div>
                <div class="col-md-5 col-sm-4  tile_stats_count">
                    <div class="count" style="font-size: 35px; color: #368a05">
                        <button type="button" id="procesar_pagos" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago" style="border: 1px solid #c4a603; background: #FFFFFF" data-keyboard="false">
                            <i class="fa fa-spinner" style="color: #1c4e63"></i>  <span style="color: #1E283D"><b>GUARDAR DATOS Y PROCEDER AL COBRO </b></span>
                        </button>
                    </div>
                </div>


            </div>

            </div>
        </div>
    <div class="col-md-2">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Estatus de la cuenta</h3>
            </div>
            <div class="row">
                <p><b> Estatus de la cuenta corriente</b></p>
                <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-success"><span class="fa fa-check"></span></span> Apertura de cuenta</div>
                    <div><span class="label label-success"><span class="fa fa-check"></span></span> Pago de inscripción</div>
                    <div><span class="label label-warning"><span class="fa fa-clock-o"></span></span> Gastos de administración</div>

                </td>
            </div>

        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Registro de pago por apertura y ahorro inicial cuenta corriente</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_pago">Fecha</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="fecha_pago" name="fecha_pago" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil">Medio de registro</label>
                                    <input type="text" class="form-control" id="medio_registro" aria-describedby="medio_registro" disabled placeholder="" value="CAJERA">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codigo_cl">Número de cliente</label>
                                    <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" readonly value="<?php echo $credito; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly value="<?php echo $Cliente[0]['NOMBRE']; ?>">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="curp">CURP</label>
                                    <input type="text" class="form-control" id="curp" name="curp" readonly value="<?php echo $Cliente[0]['CURP']; ?>">
                                </div>
                            </div>


                            <div class="col-sm-12"><table class="table table-striped table-bordered table-hover dataTable no-footer" >
                                    <thead>
                                    <tr role="row">
                                        <th class="header" rowspan="1" colspan="1" aria-label="Cod Sucursal" style="width: 250px;">Concepto del pago</th>
                                        <th class="header sorting" tabindex="0" aria-controls="muestra-cupones" rowspan="1" colspan="1" aria-label="Nombre Sucursal: activate to sort column ascending" style="width: 46px;">Monto</th>
                                        </thead>
                                    <tbody>
                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-12" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="apertura_cuenta" name="apertura_cuenta" autocomplete="off" max="10000" value="APERTURA DE CUENTA - INSCRIPCIÓN" readonly>
                                                    <small id="emailHelp" class="form-text text-muted">Monto fijo.</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-2" style="padding-top: 5px;">
                                                <div class="form-group">
                                                    <h4>$</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-9" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto_apertura" name="monto_apertura" autocomplete="off" max="10000" value="50"readonly>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-12" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto" name="monto" autocomplete="off" max="10000" value="CAPITAL INICIAL - CUENTA CORRIENTE" readonly>
                                                    <small id="emailHelp" class="form-text text-muted">El monto debe ser mayor a $100.00 MN.</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0px !important;">
                                            <div class="col-md-2" style="padding-top: 5px;">
                                                <div class="form-group">
                                                    <h4>$</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-9" style="padding-top: 9px;">
                                                <div class="form-group">
                                                    <input autofocus type="text" class="form-control" id="monto_ahorro" name="monto_ahorro" autocomplete="off" max="10000">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table></div>


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


<style>
    .center {
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 40%;
    }
</style>

<?php echo $footer; ?>