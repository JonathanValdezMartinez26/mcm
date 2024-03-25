<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <a id="link" href="/Ahorro/CuentaCorriente/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
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

            <a id="link" href="/Ahorro/ReimprimeTicket/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7325/7325275.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Reimprime Ticket </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/942/942752.png -->
                </div>
            </a>

            <a id="link" href="/Ahorro/Calculadora/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5833/5833756.png" style="border-radius: 3px; padding-top: 5px;" width="98" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Calculadora</b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/7325/7325359.png -->
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
                            <ul class="nav navbar-nav">
                                <li class="linea"><a href="/Ahorro/CuentaPeque/">
                                        <p style="font-size: 15px;">Ahorro Cuenta Corriente - Peque</p>
                                    </a></li>
                                <li><a onclick=mostrarAhorro() href="">
                                        <p style="font-size: 16px;"><b>Nuevo Contrato - Peque </b></p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <p>Para poder dar de alta un nuevo contrato de una cuenta de Ahorro, el cliente debe estar registrado en SICAFIN, si el cliente no tiene una cuenta abierta solicite el alta a su ADMINISTRADORA.</p>
                                            <hr>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="movil">Código de cliente (SICAFIN)*</label>
                                            <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="clienteBuscado" placeholder="000000">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fechaRegistro">Fecha de registro</label>
                                                <input type="text" class="form-control" id="fechaRegistro" readonly>
                                                <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="noCliente">Clave de cliente (Co-Titular)</label>
                                                <input type="number" class="form-control" id="noCliente" readonly>
                                                <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="nombre">Nombre completo del CO-TITULAR*</label>
                                                <input type="text" class="form-control" id="nombre" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="fechaRegistroPQ">Fecha de registro (Peque)</label>
                                                <input type="text" class="form-control" id="fechaRegistroPQ" readonly>
                                                <small id="emailHelp" class="form-text text-muted">Fecha de registro.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="noClientePQ">Clave de cliente (Peque)</label>
                                                <input type="number" class="form-control" id="noClientePQ" readonly>
                                                <small id="emailHelp" class="form-text text-muted">Número de acreditado MCM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre1">Primer nombre*</label>
                                                <input type="text" class="form-control" id="nombre1" name="nombre1" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre2">Segundo nombre</label>
                                                <input type="text" class="form-control" id="nombre2" name="nombre2" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="apellido1">Apellido paterno*</label>
                                                <input type="text" class="form-control" id="apellido1" name="apellido1" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="apellido2">Apellido materno*</label>
                                                <input type="text" class="form-control" id="apellido2" name="apellido2" oninput=camposLlenos(event)>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="nombre">Sexo*</label>
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <input type="radio" name="sexo" id="sexoH" checked>
                                                    <label for="sexoH">Hombre</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="radio" name="sexo" id="sexoM">
                                                    <label for="sexoM">Mujer</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_nac">Fecha de nacimiento*</label>
                                                <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" value="<?= $fecha ?>" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="pais">Pais de nacimiento*</label>
                                                <input type="text" class="form-control" id="pais" name="pais" value="MÉXICO" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ciudad">Entidad de nacimiento</label>
                                                <select class="form-control mr-sm-3" autofocus id="ciudad" name="ciudad" onchange=camposLlenos(event)>
                                                    <option value="P">CDMX</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="curp">CURP*</label>
                                                <input type="text" class="form-control" name="curp" id="curp" maxlength="18" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="edad">Edad*</label>
                                                <input type="text" class="form-control" id="edad" oninput=camposLlenos(event)>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="direccion">Dirección (Autoriza usar la dirección del acreditado) *</label>
                                                <div class="form-group">
                                                    <textarea type="text" style="resize: none;" class="form-control" id="direccion" rows="3" cols="50" readonly>
                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>

                                    <div class="col-md-7">
                                        <form id="registroInicialAhorro" name="registroInicialAhorro">
                                            <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura para la cuenta de Ahorro Corriente</b></p>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Fecha">Fecha de apertura</label>
                                                        <input type="date" class="form-control" id="fecha" name="fecha" min="2024-03-07" max="2024-03-11" value="<?= $fecha ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tipo">Tasa Anual</label>
                                                        <select class="form-control mr-sm-3" autofocus="" type="select" id="tasa" name="tasa">
                                                            <option value="5">5 %</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tipo">Monto mínimo</label>
                                                        <input type="text" class="form-control" id="monto_min" name="monto_min" value="$100.00" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Fecha">Sucursal</label>
                                                        <select class="form-control mr-sm-3" id="sucursal" name="sucursal">
                                                            <option value="1514">CORPORATIVO</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Fecha">Ejecutivo</label>
                                                        <select class="form-control mr-sm-3" id="ejecutivo" name="ejecutivo">
                                                            <option value="135">Ejecutivo Prueba</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Fecha">Manejo de cuenta</label>
                                                        <select class="form-control mr-sm-3" id="manejo_cta" name="manejo_cta" readonly>
                                                            <option value="1">Aplica</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="margin-top:40px;">
                                                <button type="button" name="btnGeneraContrato" id="btnGeneraContrato" class="btn btn-primary" onclick="generaContrato(event)" style="border: 1px solid #c4a603; background: #ffffff" data-keyboard="false" disabled>
                                                    <i class="fa fa-spinner" style="color: #1c4e63"></i>
                                                    <span style="color: #1e283d"><b>GUARDAR DATOS Y PROCEDER AL COBRO </b></span>
                                                </button>
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

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Registro de pago por apertura y ahorro inicial cuenta corriente</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="AddPagoApertura">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_pago">Fecha</label>
                                    <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="contrato">Contrato</label>
                                    <input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="contrato" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codigo_cl">Número de cliente</label>
                                    <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_cliente">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="<?php echo $Cliente[0]['NOMBRE']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_ejecutivo">Nombre del Ejecutivo</label>
                                    <input type="text" class="form-control" id="nombre_ejecutivo" name="nombre_ejecutivo" value="Ejecutivo de Prueba" readonly>
                                    <input type="hidden" class="form-control" id="ejecutivo" name="ejecutivo" value="SOOA">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Deposito de Apertura</h3>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h3>$</h3>
                            </div>
                            <div class="col-md-5" style="padding-top: 5px;">
                                <input type="number" class="form-control" id="deposito_inicial" name="deposito_inicial" min=100 max=100000 placeholder="Ingrese el monto" style="font-size: large;" onkeyup=validaDeposito(event) onkeydown=soloNumeros(event)>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="deposito_inicial_letra" name="deposito_inicial_letra" style="border: 1px solid #000000; text-align: center;" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="text-align:center;">
                                <h4>Detalle de movimientos</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <h4>+</h4>
                            </div>
                            <div class="col-md-5">
                                <h4>DEPOSITO</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" id="deposito" name="deposito" value="0.00" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <h4>-</h4>
                            </div>
                            <div class="col-md-5">
                                <h4>INSCRIPCIÓN</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" id="inscripcion" name="inscripcion" value="<?= $saldoMinimoApertura ?>.00" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>SALDO INICIAL DE LA CUENTA</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" id="saldo_inicial" name="saldo_inicial" value="0.00" readonly>
                                <input type="hidden" class="form-control" id="sma" name="sma" value="<?= $saldoMinimoApertura ?>" readonly>
                                <small style="opacity: 0;" id="tipSaldo">El saldo inicial debe ser mínimo de $<?= $saldoMinimoApertura ?>.00</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                            <button type="button" id="registraDepositoInicial" name="agregar" class="btn btn-primary" value="enviar" onclick=pagoApertura(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                        </div>
                    </form>
                </div>
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
</style>


<?php echo $footer; ?>