<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Clientes Cultiva (PLD)</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el , solo tiene permitido un rango 30 días </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Operaciones/IdentificacionClientes/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
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
                                    <th>Id Cliente</th>
                                    <th>Cuenta</th>
                                    <th>Origen</th>
                                    <th>Nombre</th>
                                    <th>Adicional</th>
                                    <th>Apellido P</th>
                                    <th>Apellido M</th>
                                    <th>Tipo P</th>
                                    <th>RFC</th>
                                    <th>CURP</th>
                                    <th>Razon Social</th>
                                    <th>Fec Nac</th>
                                    <th>Nacionalidad</th>
                                    <th>Domicilio</th>
                                    <th>Colonia</th>
                                    <th>Ciudad</th>
                                    <th>Pais</th>
                                    <th>Estado</th>
                                    <th>Telefono</th>
                                    <th>Act Eco</th>
                                    <th>Cal</th>
                                    <th>F.Alta</th>
                                    <th>Sucursal</th>
                                    <th>Genero</th>
                                    <th>C. Elec</th>
                                    <th>Firma E</th>
                                    <th>Profesion</th>
                                    <th>Ocupacion</th>
                                    <th>Pais Nac</th>
                                    <th>Edo.Nac</th>
                                    <th>Lugar Nac</th>
                                    <th>N.Doc</th>
                                    <th>Con.Cl</th>
                                    <th>Inmigran</th>
                                    <th>C.Original</th>
                                    <th>S.Cliente</th>
                                    <th>T.Doc</th>
                                    <th>Indicador Emp</th>
                                    <th>Empresa Lab</th>
                                    <th>Ind.Gob</th>
                                    <th>Puesto</th>
                                    <th>Fecha.Ini</th>
                                    <th>Fecha.Fin</th>
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
