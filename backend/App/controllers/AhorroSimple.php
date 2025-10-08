<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\MasterDom;
use Core\Controller;
use App\models\AhorroSimple as AhorroSimpleDao;
use App\models\CallCenter as CallCenterDao;

class AhorroSimple extends Controller
{

    private $_contenedor;


    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

  
    public function EstadoCuenta()
    {
        $extraHeader = self::GetExtraHeader('Consulta de Pagos');

        $extraFooter = <<<HTML
        <script>
            function getParameterByName(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                    results = regex.exec(location.search)
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
            }

            $(document).ready(function () {
                $("#muestra-cupones").tablesorter()
                var oTable = $("#muestra-cupones").DataTable({
                    lengthMenu: [
                        [13, 50, -1],
                        [132, 50, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false
                })
                // Remove accented character from search input as well
                $("#muestra-cupones input[type=search]").keyup(function () {
                    var table = $("#example").DataTable()
                    table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw()
                })
                var checkAll = 0

                fecha1 = getParameterByName("Inicial")
                fecha2 = getParameterByName("Final")
                sucursal = getParameterByName("id_sucursal")

                $("#export_excel_consulta").click(function () {
                    $("#all").attr(
                        "action",
                        "/Pagos/generarExcelConsulta/?Inicial=" +
                            fecha1 +
                            "&Final=" +
                            fecha2 +
                            "&Sucursal=" +
                            sucursal
                    )
                    $("#all").attr("target", "_blank")
                    $("#all").submit()
                })
            })

        </script>
        HTML;

        $fechaActual = date('Y-m-d');
		$cdgns = $_GET['cdgns'];

     

        if ($cdgns != '') {
            $Consulta = AhorroSimpleDao::ConsultarPagosFechaSucursal($cdgns);
	
			$ConsultaDatos = $Consulta[0];
			
            $tabla = '';
            foreach ($Consulta[1] as $key => $value) {
                if ($value['FIDENTIFICAPP'] ==  NULL) {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                    $mensaje = 'InfoAdmin();';
                } else {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                    $mensaje = 'InfoPhone();';
                }

                $monto = number_format($value['MONTO'], 2);
				
				// Definimos la variable para el icono según tipo de operación
				if($value['TIPO_OPERA'] = 'ABONO') {
					$icono = '<i class="fa fa-arrow-down" style="color: green;"></i>'; // flecha verde para ingresos
				} else {
					$icono = '<i class="fa fa-arrow-up" style="color: red;"></i>'; // flecha roja para cargos/retiros
				}

				
				
                $tabla .= <<<HTML
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                     <td style="padding: 0px !important;">{$value['TIPO_OPERA']}</td>
                     <td style="padding: 0px !important;">$icono $ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;">{$value['FREGISTRO']}</td>
                </tr>
                HTML;
				
            }

            if ($Consulta[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('getSucursales', $getSucursales);
                View::set('fechaActual', $fechaActual);
                View::render("pagos_consulta_busqueda_message_ahorro");
            } else {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
				View::set('ConsultaDatos', $ConsultaDatos);
                View::render("pagos_consulta_busqueda_ahorro");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::render("pagos_consulta_ahorro_all");
        }
    }


	public function Contrato()
	{
		$extraHeader = self::GetExtraHeader('Contratos de Ahorro');
		$extraFooter = <<<HTML
		<script>
		$(document).ready(function () {
			$("#muestra-contratos").DataTable({
				lengthMenu: [[13, 50, -1], [13, 50, "Todos"]],
				order: false
			});
		});

		// Abre el modal
		function abrirModal(cdg, nombre) {
			$("#modal_cdgns").val(cdg);
			$("#modal_nombre").val(nombre);
			$('#modal_contrato').modal('show');
		}

		// Agregar beneficiario dinámico (máx. 3)
		var contadorBeneficiarios = 1;
		function agregarBeneficiario() {
			if (contadorBeneficiarios >= 2) {
				swal("Solo puedes agregar hasta 2 beneficiarios");
				return;
			}
			contadorBeneficiarios++;
			let html = `
			<div class="beneficiario-extra">
				<div class="col-md-12"><hr></div>
				<div class="col-md-4">
					<label>Nombre completo</label>
					<input type="text" class="form-control form-control-sm" name="beneficiario_nombre[]" required>
				</div>
				<div class="col-md-4">
					<label>Parentesco</label>
					<select class="form-control form-control-sm" name="beneficiario_parentesco[]" required>
						<option value="">Seleccionar...</option>
						<option value="Padre">Padre</option>
						<option value="Madre">Madre</option>
						<option value="Hermano">Hermano</option>
						<option value="Hermana">Hermana</option>
						<option value="Esposo(a)">Esposo(a)</option>
						<option value="Hijo(a)">Hijo(a)</option>
						<option value="Abuelo(a)">Abuelo(a)</option>
						<option value="Tío(a)">Tío(a)</option>
						<option value="Primo(a)">Primo(a)</option>
						<option value="Otro">Otro</option>
					</select>
				</div>
				<div class="col-md-4">
					<label>Porcentaje (%)</label>
					<input type="number" class="form-control form-control-sm" name="beneficiario_porcentaje[]" max="100" min="0" step="0.01" required>
				</div>
			</div>`;
			$("#contenedor-beneficiarios").append(html);
		}

		// Guardar contrato y beneficiarios (con soporte para múltiples registros)
		function guardarContrato() {
			const form = document.getElementById('form_contrato');
			const data = new FormData(form);

			$.ajax({
				url: '/AhorroSimple/ContratoAdd/',
				type: 'POST',
				data: data,
				processData: false,
				contentType: false,
				success: function(respuesta) {
					if (respuesta.trim() === '1') {
						swal("Contrato guardado exitosamente", { icon: "success" });
						setTimeout(() => location.reload(), 1000);
					} else {
						swal("Error: " + respuesta, { icon: "error" });
					}
				},
				error: function(xhr, status, error) {
					swal("Error de conexión: " + error, { icon: "error" });
				}
			});
		}
		</script>
		HTML;

		// Consulta de clientes sin contrato
		$Consulta = AhorroSimpleDao::ListarClientesSinContrato();
		$tabla = '';

		foreach ($Consulta as $key => $value) {
			$cdgns = $value['CDGNS'];
			$nombre = $value['NOMBRE'];

			$tabla .= <<<HTML
			<tr>
				<td>{$cdgns}</td>
				<td>{$nombre}</td>
				<td style="text-align:center;">
					<button class="btn btn-primary btn-sm" onclick="abrirModal('{$cdgns}', '{$nombre}')">
						<i class="fa fa-plus"></i>
					</button>
				</td>
			</tr>
			HTML;
		}

		View::set('tabla', $tabla);
		View::set('header', $this->_contenedor->header($extraHeader));
		View::set('footer', $this->_contenedor->footer($extraFooter));
		View::render("contratos_lista_ahorro");
	}
	
	
	public function ContratoAdd()
	{
		$contrato = new \stdClass();

		// Datos del contrato
		$contrato->_cdgns = $_POST['cdgns'] ?? '';
		$contrato->_fecha_registro = date('Y-m-d H:i:s');
		$contrato->_tipo_ahorro = 'AHORRO SIMPLE';
		$contrato->_tasa_anual = 6.00;
		$contrato->_cdgpe_alta = $_POST['cdgpe'] ?? '';
		$contrato->_cdgpe = $_POST['cdgpe'] ?? '';

		// Validar CDGNS
		if (empty($contrato->_cdgns)) {
			echo 'Falta seleccionar un cliente';
			return;
		}

		// Insertar contrato
		$idContrato = AhorroSimpleDao::insertContrato($contrato);
		if (!$idContrato) {
			echo 'Error al registrar el contrato';
			return;
		}

		// Preparar beneficiarios
		$nombres = $_POST['beneficiario_nombre'] ?? [];
		$parentescos = $_POST['beneficiario_parentesco'] ?? [];
		$porcentajes = $_POST['beneficiario_porcentaje'] ?? [];

		$beneficiarios = [];
		$totalPorcentaje = 0;

		for ($i = 0; $i < count($nombres); $i++) {
			$nombre = trim($nombres[$i]);
			$parentesco = trim($parentescos[$i] ?? '');
			$porcentaje = floatval($porcentajes[$i] ?? 0);

			if (empty($nombre)) continue;

			$totalPorcentaje += $porcentaje;
			$beneficiarios[] = [
				'nombre' => $nombre,
				'parentesco' => $parentesco,
				'porcentaje' => $porcentaje
			];
		}

		if ($totalPorcentaje > 100) {
			echo "El porcentaje total de beneficiarios no puede exceder 100%";
			return;
		}
		if ($totalPorcentaje < 100) {
			echo "El porcentaje total de beneficiarios debe ser 100%";
			return;
		}

		// Insertar beneficiarios
		$res = AhorroSimpleDao::insertBeneficiarios($beneficiarios, $idContrato, $contrato->_cdgpe_alta, $contrato->_cdgpe);

		// Si $res es string → error real
		if (is_string($res)) {
			echo $res;
			return;
		}

		// Todo salió bien
		//echo '1';
	}


    public function generarExcel()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('FECHA', 'Fecha'),
            \PHPSpreadsheet::ColumnaExcel('REFERENCIA', 'Referencia'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto'),
            \PHPSpreadsheet::ColumnaExcel('MONEDA', 'Moneda', ['estilo' => \PHPSpreadsheet::GetEstilosExcel('moneda')])
        ];

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];
        $filas = PagosDao::GeneraLayoutContable($fecha_inicio, $fecha_fin);

        \PHPSpreadsheet::DescargaExcel('Layout Pagos', 'Reporte', 'Pagos', $columnas, $filas);
    }

    public function generarExcelConsulta()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('REGION', 'Region'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('SECUENCIA', 'Codigo'),
            \PHPSpreadsheet::ColumnaExcel('FECHA', 'Fecha'),
            \PHPSpreadsheet::ColumnaExcel('CDGNS', 'Cliente'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE', 'Nombre'),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto'),
            \PHPSpreadsheet::ColumnaExcel('TIPO', 'Tipo'),
            \PHPSpreadsheet::ColumnaExcel('EJECUTIVO', 'Ejecutivo'),
            \PHPSpreadsheet::ColumnaExcel('FREGISTRO', 'Registro')
        ];

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];
        $Sucursal = $_GET['Sucursal'];
        $filas = PagosDao::ConsultarPagosFechaSucursal($Sucursal, $fecha_inicio, $fecha_fin);

        \PHPSpreadsheet::DescargaExcel('Consulta Pagos Global', 'Reporte', 'Pagos', $columnas, $filas);
    }

}
