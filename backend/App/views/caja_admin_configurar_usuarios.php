<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <span class="button__badge">4</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes</b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2972/2972528.png IAMGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/EstadoCuentaCliente/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5864/5864275.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Catalogo de Clientes </b></p>
                    <! -- IMAGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Log/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491361.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Log Transaccional </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Configuracion/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491253.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Configurar Módulo </b></p>
                    <! -- IMAGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Reporteria/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3201/3201495.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Consultar Reportes </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                </div>
            </a>
        </div>
        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales / Configuración de Módulo para Ahorro</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea"><a href="/AdminSucursales/Configuracion/">
                                        <p style="font-size: 15px;">Activar Modulo en Sucursal</p>
                                    </a></li>
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Permisos a Usuarios</b></p>
                                    </a></li>

                                <li class="linea"><a href="/AdminSucursales/ConfiguracionParametros/">
                                        <p style="font-size: 15px;">Parámetros de Operación</p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_horario">
                                    <i class="fa fa-plus"></i> Nueva Activación
                                </button>
                                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                            </div>
                            <div class="row">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="sucursalesActivas">
                                        <thead>
                                            <tr>
                                                <th>Fecha de Registro</th>
                                                <th>Cod Sucursal</th>
                                                <th>Nombre Sucursal</th>
                                                <th>Cod Cajera</th>
                                                <th>Nombre Cajera</th>
                                                <th>Hora Apertura</th>
                                                <th>Hora Cierre</th>
                                                <th>Monto Mínimo</th>
                                                <th>Monto Máximo</th>
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
            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Permisos Modulo Administración Ahorro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="datos" onsubmit=noSUBMIT(event)>
                        <div class="row">
                            <div class="col-md-12">
                                <p>Selecciona las opciones a las que te gustaria dar acceso a sus colaboradores</p>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="sucursal">Colaborador Administrativo MCM *</label>
                                    <select class="form-control" id="cajera" name="cajera" onchange=cambioCajera() disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 19px;">
                                    <label for="sucursal">SALDOS DE SUCURSALES</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Saldos del día por sucursal</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Cierre de día</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Fondear sucursal</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Retiro efectivo</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Historail saldos por sucursal</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 19px;">
                                    <label for="sucursal">SOLICITUDES</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Reimpresión de tickets</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Resumen de movimientos</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Retiros ordinarios</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Retiros express</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Retirar efectivo de caja</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 128px!important;">
                                    <label for="sucursal">CATÁLOGO DE CLIENTES</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Catálogo de clientes</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 128px;">
                                    <label for="sucursal">LOG TRANSACCIONAL</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Log transaccional</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 73px;">
                                    <label for="sucursal">CONFIGURAR MÓDULO</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Activar módulo en sucursal</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Permisos a usuarios</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Parámetros de operación</label>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 73px;">
                                    <label for="sucursal">CONSULTAR REPORTES</label>
                                    <hr>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Hostorial de transacciones</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Historial fondeo sucursal</label>

                                    <br>
                                    <input name="hobby" type="checkbox" value="MoBa"  />
                                    <label for="hobby">Historial retiro sucursal</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button class="btn btn-primary" id="guardar" onclick=activarSucursal() disabled><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modal_configurar_montos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_configurar_montos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Configurar montos de sucursal</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="configMontos" onsubmit=noSUBMIT(event)>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codSucMontos">Código Sucursal</label>
                                    <input name="codSucMontos" id="codSucMontos" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nomSucMontos">Nombre Sucursal</label>
                                    <input name="nomSucMontos" id="nomSucMontos" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="minimoApertura">Monto mínimo apertura</label>
                                    <div class="row">
                                        <div class="col-md-1">
                                            <span style="font-size: x-large;">$</span>
                                        </div>
                                        <div class="col-md-10">
                                            <input class="form-control" id="minimoApertura" name="minimoApertura" placeholder="0.00" style="font-size: 25px;" onkeydown=soloNumeros(event) oninput=validaMontoMinMax(event) />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="maximoApertura">Monto máximo apertura</label>
                                    <div class="row">
                                        <div class="col-md-1">
                                            <span style="font-size: x-large;">$</span>
                                        </div>
                                        <div class="col-md-10">
                                            <input class="form-control" id="maximoApertura" name="maximoApertura" placeholder="0.00" style="font-size: 25px;" onkeydown=soloNumeros(event) oninput=validaMontoMinMax(event) />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button class="btn btn-primary" onclick=guardarMontos()><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<style>
    .imagen {
        transform: scale(var(--escala, 1));
        transition: transform 0.25s;
    }

    .imagen:hover {
        --escala: 1.2;
        cursor: pointer;
    }

    .linea:hover {
        --escala: 1.2;
        cursor: pointer;
        text-decoration: underline;
    }


    /* Make the badge float in the top right corner of the button */
    .button__badge {
        background-color: #fa3e3e;
        border-radius: 50px;
        color: white;
        padding: 2px 10px;
        font-size: 19px;
        position: absolute;
        /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }
</style>


<?= $footer; ?>