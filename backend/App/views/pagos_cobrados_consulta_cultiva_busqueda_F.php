<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Pagos de Clientes Cultiva (PLD) con edad</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tiene permitido un rango 7 días </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/ReportePLDPagosNacimiento/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                                    <span id="availability1">Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                                    <span id="availability1">Hasta</span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                                    <th>Localidad</th>
                                    <th>Sucursal</th>
                                    <th>Tipo de Operación</th>
                                    <th>Cliente</th>
                                    <th>N.Cuenta</th>
                                    <th>Instrumento Monetario</th>
                                    <th>Moneda</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Tipo de Receptor</th>
                                    <th>Clave de Receptor</th>
                                    <th>Caja</th>
                                    <th>Id Cajero</th>
                                   <th>Fecha y Hora</th>
                                   <th>N.Tarjeta</th>
                                   <th>Tipo de Tarjeta</th>
                                   <th>Código de Autorización</th>
                                   <th>Atraso</th>
                                   <th>oficina</th>
                                   <th>Fec Nac</th>
                                   <th>Edad</th>
                                   <th>Ciclo</th>
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

<?php echo $footer; ?>
