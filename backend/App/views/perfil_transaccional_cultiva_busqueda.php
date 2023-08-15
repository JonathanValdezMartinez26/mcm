<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta del Perfil Transaccional Clientes Cultiva (PLD)</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tiene permitido un rango 30 días </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/PerfilTransaccional/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                                    <span id="availability1" style="font-size:15px">Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                                    <span id="availability1" style="font-size:15px">Hasta</span>
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
                            <th>Cliente</th>
                            <th>Grupo</th>
                            <th>Nombre Comp</th>
                            <th>Instrumento</th>
                            <th>Tipo_Moneda</th>
                            <th>T Cambio</th>
                            <th>Monto Presta</th>
                            <th>Plazo</th>
                            <th>Frecuencia</th>
                            <th>Total Pagos</th>
                            <th>Monto</th>
                            <th>Aut Adelan</th>
                            <th>No.Aporta</th>
                            <th>Monto Aporta</th>
                            <th>Cuota</th>
                            <th>Saldo</th>
                            <th>Sucursal</th>
                            <th>Origen Recu</th>
                            <th>Destino Recu</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Destino</th>
                            <th>Origen</th>
                            <th>Tipo Operacion</th>
                            <th>Instr Mon</th>
                            <th>T Credito</th>
                            <th>Clave  Pro</th>
                            <th>Pais Origen</th>
                            <th>Pais Destino</th>
                            <th>Alta contrato</th>
                            <th>Tipo Contr</th>
                            <th>Tipo Doc</th>
                            <th>Lat/Lon</th>
                            <th>Localizacion</th>
                            <th>CP</th>
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
