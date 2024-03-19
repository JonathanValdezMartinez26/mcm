<?php echo $header; ?>

    <div class="right_col">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

            <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
                <a id="link" href="/Ahorro/CuentaCorriente/">
                    <div class="col-md-5 imagen"  style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5575/5575938.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Ahorro </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5575/5575939.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ContratoInversion/">
                    <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5836/5836503.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Inversión </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5836/5836477.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ContratoCuentaPeque/">
                    <div class="col-md-5" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2995/2995467.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2995/2995390.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/EstadoCuenta/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/12202/12202939.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Estado de Cuenta </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/12202/12202918.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/SaldosDia/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5833/5833855.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Mis saldos del día </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5833/5833897.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/SolicitudRetiro/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/942/942803.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Solicitud de retiro </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/942/942752.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/ReimprimeTicket/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/7325/7325275.png" style="border-radius: 3px; padding-top: 5px;" width="98" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Reimprime Ticket </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/7325/7325359.png -->
                    </div>
                </a>

                <a id="link" href="/Ahorro/Calculadora/">
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/5833/5833756.png" style="border-radius: 3px; padding-top: 5px;" width="98" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Calculadora  </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/5833/5833832.png -->
                    </div>
                </a>
            </div>



            <div class="col-md-9">
                <form id="registroInicialAhorro" name="registroInicialAhorro">
                    <div class="modal-content">
                        <div class="modal-header" style="padding-bottom: 0px">
                            <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                                <a class="navbar-brand">Mi espacio / Cuentas de ahorro corriente</a>
                                &nbsp;&nbsp;
                            </div>
                            <div>
                                <ul class="nav navbar-nav" >
                                    <li><a onclick=mostrarAhorro() href=""><p style="font-size: 16px;"><b>Ahorro Cuenta Corriente</b></p></a></li>
                                    <li class="linea"><a href="/Ahorro/ContratoCuentaCorriente/"><p style="font-size: 15px;">Nuevo Contrato</p></a></li>
                                    <li class="linea"><a href="#"><p style="font-size: 15px;">Estado de Cuenta</p></a></li>
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
                                                            <p>Para poder depositar a una cuenta de Ahorro, el cliente debe tener una cuenta activa de Ahorro Corriente, si el cliente no tiene una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>.</p><hr>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="movil">Clave de contrato o código del cliente (SICAFIN)</label>
                                                            <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Cliente" name="Cliente" value="" placeholder="000000" required>
                                                        </div>

                                                        <div class="col-md-2" style="padding-top: 25px">
                                                            <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                                                <i class="fa fa-search"></i> Buscar
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="fecha_pago">Fecha del depósito*</label>
                                                            <input onkeydown="return false" type="text" class="form-control" id="fecha_pago" name="fecha_pago" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="contrato">Número de contrato*</label>
                                                            <input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="contrato" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="codigo_cl">Número cliente SICAFIN*</label>
                                                            <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" value="<?php echo $credito; ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="nombre">CURP*</label>
                                                            <input type="text" class="form-control" id="curp_" name="curp_" readonly="" value="<?php echo $Cliente[0]['CURP']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label for="nombre_cliente">Nombre del cliente*</label>
                                                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="<?php echo $Cliente[0]['NOMBRE']; ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="nombre_ejecutivo">Nombre del ejecutivo*</label>
                                                            <input type="text" class="form-control" id="nombre_ejecutivo" name="nombre_ejecutivo" value="Ejecutivo de Prueba" readonly>
                                                            <input type="hidden" class="form-control" id="ejecutivo" name="ejecutivo" value="SOOA">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12"><hr></div>
                                                    <div class="col-md-6"><h3></h3></div>

                                                    <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                                        <h3>$</h3>
                                                    </div>
                                                    <div class="col-md-5" style="padding-top: 5px;">
                                                        <input type="number" class="form-control" id="deposito_inicial" name="deposito_inicial" min="250" max="100000" placeholder="Ingrese el monto a depositar" style="font-size: large; font-size: 30px;" onkeyup=validaDeposito(event)>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control" id="deposito_inicial_letra" name="deposito_inicial_letra" style="border: 1px solid #000000; text-align: center; font-size: 28px;" readonly>
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
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-8" style="display: flex; justify-content: flex-end;">
                                                            <h4>Depósito a cuenta ahorro corriente $</h4>
                                                        </div>
                                                        <div class="col-md-4" style="display: flex; justify-content: flex-end;">

                                                            <input type="number" class="form-control" id="deposito" name="deposito" value="0.00" readonly>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-8">
                                                            <h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inscripción ahorro Cuenta Corriente $</h4>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control" id="inscripcion" name="inscripcion" value="<?= $saldoMinimoApertura ?>.00" readonly>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <div class="col-md-8">
                                                            <h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Saldo inicial de la cuenta $</h4>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number" class="form-control" id="saldo_inicial" name="saldo_inicial" value="0.00" readonly>
                                                            <input type="hidden" class="form-control" id="sma" name="sma" value="<?= $saldoMinimoApertura ?>" readonly>
                                                            <small style="opacity: 0;" id="tipSaldo">El saldo inicial debe ser mínimo de $<?= $saldoMinimoApertura ?>.00</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" id="registraDepositoInicial" name="agregar" class="btn btn-primary" value="enviar" onclick=pagoApertura(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Procesar Transaccion</button>
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
    </style>


<?php echo $footer; ?>