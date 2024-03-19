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

                <a id="link" href="/Ahorro/CuentaPeque/">
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
                                    <li class="linea"><a href="/Ahorro/CuentaPeque/"><p style="font-size: 15px;">Ahorro Cuenta Corriente - Peque</p></a></li>
                                    <li><a onclick=mostrarAhorro() href=""><p style="font-size: 16px;"><b>Nuevo Contrato - Peque </b></p></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <p>Para poder dar de alta un nuevo contrato de una cuenta de Ahorro, el cliente debe estar registrado en SICAFIN, si el cliente no tiene una cuenta abierta solicite el alta a su ADMINISTRADORA.</p><hr>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="movil">Código de cliente (SICAFIN)*</label>
                                                <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Cliente" name="Cliente" value="" placeholder="000000" required>
                                            </div>

                                            <div class="col-md-2" style="padding-top: 25px">
                                                <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                                    <i class="fa fa-search"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <p><b><span class="fa fa-sticky-note"></span> Identificación del cliente</b></p>
                                            <br>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="movil">Fecha de registro</label>
                                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="cdgns">Clave de cliente (Co-Titular)</label>
                                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly="" value="003011">
                                                    <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nombre">Nombre completo del CO-TITULAR*</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="nombre1" >
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="movil">Fecha de registro (Peque)</label>
                                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled="" placeholder="" value="<?php echo $Cliente[0]['REGISTRO']; ?>">
                                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="cdgns">Clave de cliente (Peque)</label>
                                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly="" value="003011">
                                                    <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Primer nombre*</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="nombre1" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Segundo nombre</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="nombre2">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Apellido paterno*</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="apellio_m">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Apellido materno*</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="apellido_p">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="nombre">Sexo*</label>
                                                <div class="form-group">

                                                    <div class="col-md-6">
                                                        <input type="radio" name="sexo" id="cHombre" checked>
                                                        <label for="cHombre">Hombre</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="radio" name="sexo" id="cMujer">
                                                        <label for="cMujer">Mujer</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Fecha de nacimiento*</label>
                                                    <input type="date" class="form-control" id="fec_nacimiento" name="fec_nacimiento">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Pais de nacimiento*</label>
                                                    <input type="text" class="form-control" id="nom_cliente" name="apellio_m" value="MÉXICO" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tipo">Entidad de nacimiento</label>
                                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo" name="tipo">
                                                        <option value="P">CDMX</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="nombre">CURP*</label>
                                                    <input type="text" class="form-control" id="curp_" name="curp_" readonly="" value="<?php echo $Cliente[0]['CURP']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nombre">Edad*</label>
                                                    <input type="text" class="form-control" id="edad" name="edad" readonly="" value="<?php echo $Cliente[0]['EDAD']; ?> Años">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <input type="radio" name="direccion" id="cDireccion" checked>
                                                    <label for="cDireccion">Dirección (Autoriza usar la dirección del acreditado) *</label>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                            <textarea type="text" class="form-control" id="direccion" name="direccion" rows="3" cols="50" readonly>
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="col-md-7">
                                            <form id="registroInicialAhorro" name="registroInicialAhorro">
                                                <div class="col-md-12">
                                                    <div class="panel panel-body" style="margin-bottom: 0px;">
                                                        <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura ahorro cuenta peque</b></p>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="Fecha">Fecha de apertura</label>
                                                                    <input onkeydown="return false" type="date" class="form-control" id="fecha" name="fecha" min="2024-03-07" max="2024-03-11" value="2024-03-11">
                                                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label for="tipo">Tasa Anual</label>
                                                                    <select class="form-control mr-sm-3" autofocus="" type="select" id="tasa" name="tasa">
                                                                        <option value="5">5 %</option>
                                                                    </select>
                                                                    <small id="emailHelp" class="form-text text-muted">Rendimiento.</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="tipo">Monto mínimo</label>
                                                                    <input onkeydown="return false" type="text" class="form-control" id="monto_min" name="monto_min" value="$100.00" readonly>
                                                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="tipo">Monto máximo</label>
                                                                    <input onkeydown="return false" type="text" class="form-control" id="monto_max" name="monto_max" value="NO APLICA" readonly>
                                                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="tipo">Costo de apertura</label>
                                                                    <input onkeydown="return false" type="text" class="form-control" id="monto_max" name="monto_max" value="NO APLICA" readonly>
                                                                    <small id="emailHelp" class="form-text text-muted">Ahorro para cuenta corriente.</small>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
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