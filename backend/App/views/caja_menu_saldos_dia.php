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
                <div class="col-md-5" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5833/5833897.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Mis saldos del día </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5833/5833855.png -->
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
                            <a class="navbar-brand">Mi espacio / Saldos del día</a>
                            &nbsp;&nbsp;
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-8" style="height: 50px; display: flex; align-items: center;">
                                <label style="font-size: 16px; font-weight: bold; margin: 0; padding: 0;">Saldos generales del día <?= $fecha; ?>
                                </label>
                            </div>
                            <div class="col-md-4" style="height: 50px; display: flex; align-items: center; justify-content: flex-end;">
                                <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"></i><b> Exportar a Excel</b></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="card col-md-12">
                                        <form id="AddPagoApertura">
                                            <!-- <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                        <p>Para poder depositar a una cuenta de Ahorro, el cliente debe tener una cuenta activa de Ahorro Corriente, si el cliente no tiene una cuenta abierta <a href="/Ahorro/Apertura/" target="_blank">presione aquí</a>.</p>
                                                        <hr>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3" style="border: 1px solid #dfdfdf; border-radius: 30px;display:flex; flex-direction: column;justify-content: center; align-items: center;" data-toggle="modal" data-target="#modal_agregar_pago">
                                                        <br>
                                                        <p style="font-size: 16px"><b><i class="fa fa-sun-o" style="color: #ffdc3a"></i> Apertura:</b></p>
                                                        <br>
                                                        <p style="font-size: 19px"><b> $ <?= $saldoInicial; ?></b></p>
                                                        <br>
                                                    </div>
                                                    <div class="col-md-3" style="border: 1px solid #dfdfdf; border-radius: 30px;display:flex; flex-direction: column;justify-content: center; align-items: center;" data-toggle="modal" data-target="#modal_agregar_pago">
                                                        <br>
                                                        <p style="font-size: 16px"><b><i class="fa fa-arrow-down" style="color: #21ac29"></i> Entradas:</b></p>
                                                        <br>
                                                        <p style="font-size: 19px"><b> $ <?= $entradas; ?></b></p>
                                                        <br>
                                                    </div>
                                                    <div class="col-md-3" style="border: 1px solid #dfdfdf; border-radius: 30px;display:flex; flex-direction: column;justify-content: center; align-items: center;" data-toggle="modal" data-target="#modal_agregar_pago">
                                                        <br>
                                                        <p style="font-size: 16px"><b> <i class="fa fa-arrow-up" style="color: #d1000d"></i> Salidas:</b></p>
                                                        <br>
                                                        <p style="font-size: 19px"><b> $ <?= $salidas; ?></b></p>
                                                        <br>
                                                    </div>
                                                    <div class="col-md-3" style="border: 1px solid #dfdfdf; border-radius: 30px;display:flex; flex-direction: column;justify-content: center; align-items: center;" data-toggle="modal" data-target="#modal_agregar_pago">
                                                        <br>
                                                        <p style="font-size: 16px"><b><i class="fa fa-moon-o" style="color: #094471"></i> Cierre:</b></p>
                                                        <br>
                                                        <p style="font-size: 19px"><b> $ <?= $saldoFinal; ?></b></p>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12" style="text-align:center;">
                                                    <h4>Resumen de movimientos (entradas y salidas de efectivo)</h4>
                                                </div>
                                            </div>
                                            <div class="card col-md-12">
                                                <form name="all" id="all" method="POST">
                                                    <div class="dataTable_wrapper">
                                                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Operación</th>
                                                                    <th>Nombre del cliente</th>
                                                                    <th>Código cliente</th>
                                                                    <th>Fecha de movimiento</th>
                                                                    <th>Monto del movimiento</th>
                                                                    <th>Autorización</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="detMov">
                                                                <?= $tabla; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>
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