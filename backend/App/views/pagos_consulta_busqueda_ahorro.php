<?php echo $header; ?>

<div class="right_col" style="color: #000;">

    <!-- Panel principal -->
    <div class="panel panel-body" style="margin-bottom: 0px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px;">
       <div class="x_title">
			<a href="/AhorroSimple/EstadoCuenta/" style="text-decoration: none; color: inherit;">
				<label style="font-size: 28px; font-weight: bold; cursor: pointer;">📊 Estado de Cuenta</label>
			</a>
			<div class="clearfix"></div>
		</div>

        <!-- Botón de búsqueda -->
        <div class="card col-md-12 mb-3">
           

            <!-- Información resumida -->
            <div class="row">
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px;">

                    <!-- Cliente -->
                    <div class="col-md-4 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;"><i class="fa fa-user-circle"></i> Cliente</span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">
                            (<?= $ConsultaDatos['NO_CREDITO'] ?>) - <?= $ConsultaDatos['CLIENTE'] ?>
                        </div>
                        <div class="count" style="font-size: 14px; color: #555;">
                            Apertura: <?= $ConsultaDatos['FECHA_APERTURA_AHORRO'] ?>
                        </div>
                    </div>

                    <!-- Saldo -->
                    <div class="col-md-1 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;"><i class="fa fa-wallet"></i> Saldo</span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">$ <?=  number_format($ConsultaDatos['TOTAL']);  ?></div>
                    </div>

                    <!-- Abonos -->
                    <div class="col-md-1 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px; color: green;"><i class="fa fa-arrow-down"></i> Abonos</span>
                        <div class="count" style="font-size: 16px; font-weight: bold; color: green;">
                            $ <?= number_format($ConsultaDatos['PAGOSDIA']); ?>
                        </div>
                    </div>

                    <!-- Retiros -->
                    <div class="col-md-1 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px; color: red;"><i class="fa fa-arrow-up"></i> Retiros</span>
                        <div class="count" style="font-size: 16px; font-weight: bold; color: red;">
                            $ <?= number_format($ConsultaDatos['RETIROS_AHORRO_SIMPLE']); ?>
                        </div>
                    </div>

                    <!-- Tasa -->
                    <div class="col-md-1 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;"><i class="fa fa-percent"></i> Tasa</span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">6% ANUAL</div>
                    </div>

                    <!-- Sucursal -->
                    <div class="col-md-1 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;"><i class="fa fa-building"></i> Sucursal</span>
                        <div class="count" style="font-size: 16px; font-weight: bold;"><?= $ConsultaDatos['SUCURSAL'] ?></div>
                    </div>

                    <!-- Ejecutivo -->
                    <div class="col-md-2 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;"><i class="fa fa-user-tie"></i> Ejecutivo</span>
                        <div class="count" style="font-size: 16px; font-weight: bold;"><?= $ConsultaDatos['EJECUTIVO']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Tabla de pagos -->
            <div class="dataTable_wrapper mt-3">
                <table class="table table-striped table-bordered table-hover" id="pagosRegistrados" style="font-size: 14px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Medio</th>
                            <th>Fecha Aplicación</th>
                            <th>Ciclo</th>
							<th>Operación</th>
                            <th>Monto</th>
                            <th>Tipo de Movimiento</th>
                            <th>Ejecutivo / Adminsitradora</th>
                            <th>Fecha Registro</th>
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

<?php echo $footer; ?>
