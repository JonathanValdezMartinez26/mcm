<?php echo $header; ?>

    <div class="right_col">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

            <div class="col-md-3 panel panel-body" sstyle="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">

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
                    <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2995/2995390.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                        <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                        <! -- https://cdn-icons-png.flaticon.com/512/2995/2995467.png -->
                    </div>
                </a>

                <div class="col-md-5" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/12202/12202918.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Estado de Cuenta </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/12202/12202939.png -->
                </div>
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
                                <a class="navbar-brand">Mi espacio / Estado de Cuenta</a>
                                &nbsp;&nbsp;
                            </div>
                            <div>
                                <ul class="nav navbar-nav" >
                                    <li><a href="/Ahorro/ContratoCuentaCorriente/"><p style="font-size: 16px;"><b>Consulta</b></p></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
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
                                        <div class="col-md-4">
                                            <p><b><span class="fa fa-sticky-note"></span> Identificación del cliente</b></p>
                                            <br>
                                            <div class="card col-md-12">


                                            </div>
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