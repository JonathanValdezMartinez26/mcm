<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <span class="button__badge">4</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910306.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2910/2910156.png -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Solicitudes/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes de Sucursales</b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2972/2972528.png IAMGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/EstadoCuentaCliente/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5864/5864275.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Catalogo de Clientes </b></p>
                    <! --https://cdn-icons-png.flaticon.com/512/1605/1605350.png IMAGEN EN COLOR -->
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
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491249.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Configurar Módulo </b></p>
                    <! --https://cdn-icons-png.flaticon.com/512/900/900834.png IMAGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Log/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3201/3201495.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Consultar Reportes </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/3201/3201558.png IMAGEN EN COLOR -->
                </div>
            </a>
        </div>
        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales / Saldos del día</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Saldos del día por sucursal</b></p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/ArqueoSucursal/">
                                        <p style="font-size: 15px;">Cierre de día</p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/FondearSucursal/">
                                        <p style="font-size: 15px;">Fondear sucursal</p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/RetiroSucursal/">
                                        <p style="font-size: 15px;">Retiro efectivo</p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/Historial/">
                                        <p style="font-size: 15px;">Historial saldos por sucursal</p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="card col-md-12">
                                        <form id="AddPagoApertura">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-4">
                                                        <p>Para poder depositar a una cuenta de Ahorro, el cliente debe tener una cuenta activa de Ahorro Corriente, si el cliente no tiene una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>.</p>
                                                        <hr>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="movil">Clave de contrato o código del cliente (SICAFIN)</label>
                                                        <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="clienteBuscado" name="clienteBuscado" placeholder="000000" required>
                                                    </div>

                                                    <div class="col-md-2" style="padding-top: 25px">
                                                        <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <label for="nombre">Nombre del cliente*</label>
                                                            <input type="text" class="form-control" id="nombre" name="nombre" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label for="curp">CURP*</label>
                                                            <input type="text" class="form-control" id="curp" name="curp" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="fecha_pago">Fecha del depósito*</label>
                                                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="<?= $fecha; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="contrato">Número de contrato*</label>
                                                            <input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="contrato" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="cliente">Número cliente SICAFIN*</label>
                                                            <input type="number" class="form-control" id="cliente" name="cliente" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="nombre_ejecutivo">Nombre del ejecutivo*</label>
                                                            <input type="text" class="form-control" id="nombre_ejecutivo" name="nombre_ejecutivo" value="Ejecutivo de Prueba" readonly>
                                                            <input type="hidden" class="form-control" id="ejecutivo" name="ejecutivo" value="SOOA">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3" style="font-size: 18px; padding-top: 5px;">
                                                        <label>Movimiento:</label>
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                                        <input type="radio" name="tipoMovimiento" id="deposito" onchange=cambioMovimiento(event) checked>
                                                        <label for="deposito">Depósito</label>
                                                    </div>
                                                    <div class="col-md-2" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                                        <input type="radio" name="tipoMovimiento" id="retiro" onchange=cambioMovimiento(event)>
                                                        <label for="retiro">Retiro</label>
                                                    </div>
                                                    <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                        <h3>$</h3>
                                                    </div>
                                                    <div class="col-md-4" style="padding-top: 5px;">
                                                        <input type="number" class="form-control" id="monto" name="monto" min="250" max="100000" placeholder="0.00" style="font-size: large; font-size: 25px;" onkeyup=validaDeposito(event)>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control" id="monto_letra" name="monto_letra" style="border: 1px solid #000000; text-align: center; font-size: 25px;" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="text-align:center;">
                                                    <hr>
                                                    <h3>Resumen de movimientos</h3>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                                    <h4>Saldo actual cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="saldoActual" name="saldoActual" value="0.00" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-1">
                                                    <h4 id="simboloOperacion">+</h4>
                                                </div>
                                                <div class="col-md-7" style="display: flex; justify-content: flex-start;">
                                                    <h4 id="descOperacion">Depósito a cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="montoOperacion" name="montoOperacion" value="0.00" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                                    <h4>Saldo final cuenta ahorro corriente</h4>
                                                </div>
                                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                    <h4>$</h4>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" class="form-control" id="saldoFinal" name="saldoFinal" value="0.00" readonly>
                                                </div>
                                                <div class="col-md-12" style="display: flex; justify-content: center; color: red;">
                                                    <label id="tipSaldo" style="opacity:0">El saldo final no puede ser menor a $0.00.</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" id="btnRegistraOperacion" name="agregar" class="btn btn-primary" value="enviar" onclick=registraOperacion(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Procesar Transaccion</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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


<?php echo $footer; ?>