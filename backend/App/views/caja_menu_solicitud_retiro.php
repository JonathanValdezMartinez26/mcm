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
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2995/2995390.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2995/2995467.png -->
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
                    <div class="col-md-5" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/942/942752.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Solicitud de retiro </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/942/942803.png -->
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
                                <a class="navbar-brand">Mi espacio / Retiros</a>
                                &nbsp;&nbsp;
                            </div>
                            <div>
                                <ul class="nav navbar-nav" >
                                    <li><a onclick=mostrarAhorro() href=""><p style="font-size: 16px;"><b>Solicitud de retiro</b></p></a></li>
                                    <li class="linea"><a href="/Ahorro/SolicitudRetiroHistorial/"><p style="font-size: 15px;">Historial de solicitudes</p></a></li>
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
                                                                <label for="fecha_pago">Fecha de la solicitud*</label>
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
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="fecha_pago">Fecha del retiro*</label>
                                                                <input onkeydown="return false" type="text" class="form-control" id="fecha_pago" name="fecha_pago" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="contrato">Monto del retiro*</label>
                                                                <input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="contrato" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12" style="text-align:center;">
                                                            <hr>
                                                            <h4>Selecciona la cuenta del retiro</h4>
                                                        </div>
                                                    </div>

                                                    <div class="card col-md-12">
                                                        <form name="all" id="all" method="POST">
                                                            <div class="dataTable_wrapper">
                                                                <table class="table table-striped table-bordered table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th>Apertura</th>
                                                                        <th>Contrato</th>
                                                                        <th>Cuenta</th>
                                                                        <th>Saldo Disponible</th>
                                                                        <th>Cliente(s)</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                                                        <th><input type="radio" name="contrato" id="contrato1"></th>
                                                                        <td style="padding: 0px !important;">15/03/2024 12:24:04</td>
                                                                        <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>0030110147895210 &nbsp;</td>
                                                                        <td style="padding: 0px !important;"> AHORRO CUENTA CORRIENTE SIMPLE</td>
                                                                        <td style="padding: 0px !important;">$ 50,000.00</td>
                                                                        <td style="padding: 0px !important;">EJEMPLO EJEMPLO EJEMPLO</td>

                                                                    </tr>
                                                                    <tr style="padding: 0px !important;" role="row" class="odd">
                                                                        <th><input type="radio" name="contrato" id="contrato2"></th>
                                                                        <td style="padding: 0px !important;">15/03/2024 12:24:04</td>
                                                                        <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>0030110147895210 &nbsp;</td>
                                                                        <td style="padding: 0px !important;">AHORRO CUENTA CORRIENTE MANCOMUNADA</td>
                                                                        <td style="padding: 0px !important;">$ 50,000.00</td>
                                                                        <td style="padding: 0px !important;">EJEMPLO EJEMPLO EJEMPLO<br>EJEMPLO EJEMPLO EJEMPLO 2</td>

                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                        <div>

                                                            <center>
                                                                <p style="font-size: 17px;"> El cliente <u>EJEMPLO EJEMPLO EJEMPLO</u>, solicita el retiro de fondos de su cuenta <u>NUMERO_CONTRATO</u>, la cantidad de $0000 <b>(CANTIDAD CON LETRA M.N)</b>, para el día <b>18/03/2024.</b> <input type="checkbox" id="cbox1" value="first_checkbox" /></p>
                                                            </center>

                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" id="registraDepositoInicial" name="agregar" class="btn btn-primary" value="enviar" onclick=pagoApertura(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Enviar Solicitud a Tesorería</button>
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