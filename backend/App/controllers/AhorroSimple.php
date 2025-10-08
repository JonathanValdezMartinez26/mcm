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
				if ($value['TIPO_OPERA'] = 'ABONO') {
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
			{$this->mensajes}
			{$this->consultaServidor}
			{$this->confirmarMovimiento}
			{$this->configuraTabla}
			{$this->descargaExcel}
			{$this->formatoMoneda}
			{$this->soloNumeros}

			const mostrarBusqueda = () => {
				$("#busqueda_cliente").show()
				limpiaBusqueda()
				limpiarBeneficiarios()
				$("#btnRegistraContrato").show()
				$("#btnActualizaBeneficiarios").hide()
				$('#modal_alta_contrato').modal('show')
			}

			const buscarClienteAlta = () => {
				const credito = $("#alta_cdgns").val().trim()

				if (credito === "") return showError("Debe ingresar un número de cliente")
				if (credito.length < 6) return showError("El número de cliente debe tener 6 dígitos")

				consultaServidor("/AhorroSimple/GetCliente/", { credito }, (respuesta) => {
					if (!respuesta.success) return showError(respuesta.mensaje)

					limpiarBeneficiarios()
					if (!respuesta?.datos || respuesta.datos.length === 0) return showError("No se encontró un cliente con ese número de crédito")

					$("#alta_nombre").val(respuesta.datos.NOMBRE)
					$("#noCredito").val(credito)
					$("#noCliente").val(respuesta.datos.CLIENTE)

					$(".nombreBeneficiario").prop("disabled", false)
					$(".parentescoBeneficiario").prop("disabled", false)
					$(".porcentajeBeneficiario").prop("disabled", false)
				})
			}

			const activarBotonAgregar = (e) => {
				const padre = $(e.target).closest(".beneficiario-row")
				const nombre = padre.find(".nombreBeneficiario").val().trim()
				const parentesco = padre.find(".parentescoBeneficiario").val().trim()
				const porcentaje = Array.from(padre.find(".porcentajeBeneficiario")).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0)

				padre.find(".btnAgregaBeneficiario").prop("disabled", true)
				if (nombre === "") return
				if (parentesco === "") return
				if (parseFloat(porcentaje) >= 100) return
				if (parseFloat(porcentaje) > 0) padre.find(".btnAgregaBeneficiario").prop("disabled", false)
			}

			const agregarBeneficiarioRow = (e) => {
				const padre = $(e.target).closest(".beneficiario-row")
				const abuelo = padre.closest("#contenedor-beneficiarios")
				const totalPorcentaje = Array.from(abuelo.find(".porcentajeBeneficiario")).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0)
				
				if (totalPorcentaje > 100) return showError("El porcentaje total de beneficiarios no puede exceder 100%")
				if ($(".beneficiario-row").length >= 2) return showError("No se pueden agregar más de 2 beneficiarios")

				const clone = padre.clone()
				clone.find("input").val("")
				clone.find("select").val("")
				clone.find(".btnAgregaBeneficiario").prop("disabled", true).hide()
				clone.find(".btnEliminaBeneficiario").prop("disabled", false).closest('div').show()
				abuelo.append(clone)
			}

			const eliminarBeneficiario = (e) => {
				const padre = $(e.target).closest(".beneficiario-row")
				padre.remove()
			}

			const maximo100 = (e) => {
				const valor = $(e.target).val()
				if (parseFloat(valor) > 100) {
					$(e.target).val(100)
					showError("El porcentaje no puede ser mayor a 100")
				}
			}

			const limpiaBusqueda = () => {
				$("#alta_cdgns").val('')
				$("#alta_nombre").val('')
				$("#noCredito").val('')
				$("#noCliente").val('')
			}

			const limpiarBeneficiarios = () => {
				$("#contenedor-beneficiarios").find(".beneficiario-row:not(:first)").remove()
				$("#contenedor-beneficiarios").find("input, select").val('')
				$("#btnAgregaBeneficiario").prop("disabled", true)
				$(".nombreBeneficiario").prop("disabled", true)
				$(".parentescoBeneficiario").prop("disabled", true)
				$(".porcentajeBeneficiario").prop("disabled", true)
			}

			const registraContrato = () => {
				const totalPorcentaje = Array.from($(".porcentajeBeneficiario")).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0)
				if (totalPorcentaje > 100) return showError("El porcentaje total de beneficiarios no puede exceder 100%")
				if (totalPorcentaje < 100) return showError("El porcentaje total de beneficiarios debe ser 100%")

				confirmarMovimiento("Registro de Contrato", "¿Desea continuar con la creación del contrato?")
				.then((continuar) => {
					if (!continuar) return

					let params = $("#form_alta_contrato").serialize()
					params += "&ejecutivo={$_SESSION['usuario']}"

					consultaServidor("/AhorroSimple/RegistraContrato/", params, (respuesta) => {
						if (!respuesta.success) return showError(respuesta.mensaje)
						showSuccess(respuesta.mensaje).then(() => {
							$('#modal_alta_contrato').modal('hide')
							limpiaBusqueda()
							limpiarBeneficiarios()
							location.reload();
						})
					})
				})
			}

			const mostrarBeneficiarios = (credito, nombre) => {
				$("#busqueda_cliente").hide()
				limpiaBusqueda()
				limpiarBeneficiarios()

				$("#alta_nombre").val(nombre)
				$("#noCredito").val(credito)

				$(".nombreBeneficiario").prop("disabled", false)
				$(".parentescoBeneficiario").prop("disabled", false)
				$(".porcentajeBeneficiario").prop("disabled", false)

				consultaServidor("/AhorroSimple/GetBeneficiarios/", { credito }, (respuesta) => {
					if (!respuesta.success) return showError(respuesta.mensaje)
					if (!respuesta?.datos || respuesta.datos.length === 0) return showError("No se encontraron beneficiarios para este contrato")

					respuesta.datos.forEach((b, index) => {
						if (index > 0) $(".btnAgregaBeneficiario").click()
						$(".nombreBeneficiario").eq(index).val(b.NOMBRE_COMPLETO)
						$(".parentescoBeneficiario").eq(index).val(b.PARENTESCO)
						$(".porcentajeBeneficiario").eq(index).val(b.PORCENTAJE)
					})
				})

				$("#btnRegistraContrato").hide()
				$("#btnActualizaBeneficiarios").show()
				$('#modal_alta_contrato').modal('show')
			}

			const actualizaBeneficiarios = () => {
				const totalPorcentaje = Array.from($(".porcentajeBeneficiario")).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0)
				if (totalPorcentaje > 100) return showError("El porcentaje total de beneficiarios no puede exceder 100%")
				if (totalPorcentaje < 100) return showError("El porcentaje total de beneficiarios debe ser 100%")

				confirmarMovimiento("Actualización de Beneficiarios", "¿Desea continuar con la actualización de los beneficiarios?")
				.then((continuar) => {
					if (!continuar) return

					let params = $("#form_alta_contrato").serialize()
					params += "&ejecutivo={$_SESSION['usuario']}"

					consultaServidor("/AhorroSimple/ActualizaBeneficiarios/", params, (respuesta) => {
						if (!respuesta.success) return showError(respuesta.mensaje)
						showSuccess(respuesta.mensaje).then(() => {
							$('#modal_alta_contrato').modal('hide')
							limpiaBusqueda()
							limpiarBeneficiarios()
							location.reload();
						})
					})
				})
			}

			$(document).ready(function () {
				$("#muestra-contratos").DataTable({
					lengthMenu: [[13, 50, -1], [13, 50, "Todos"]],
					order: false
				});

				$("#btnMostrarBusqueda").on("click", mostrarBusqueda)

				$("#alta_cdgns").on("keypress", (e) => soloNumeros(e, buscarClienteAlta))
				$("#btnBuscarNuevo").on("click", buscarClienteAlta)
				$(document).on("keypress", ".porcentajeBeneficiario", (e) => maximo100(e))
				$(document).on("input", ".nombreBeneficiario, .porcentajeBeneficiario", (e) => activarBotonAgregar(e))
				$(document).on("change", ".parentescoBeneficiario", (e) => activarBotonAgregar(e))
				$(document).on("click", ".btnAgregaBeneficiario", agregarBeneficiarioRow)
				$(document).on("click", ".btnEliminaBeneficiario", eliminarBeneficiario)

				$("#btnRegistraContrato").on("click", registraContrato)
				$("#btnActualizaBeneficiarios").on("click", actualizaBeneficiarios)
			});
		</script>
		HTML;

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
					<button class="btn btn-primary btn-sm" onclick="mostrarBeneficiarios('{$cdgns}', '{$nombre}')">
						<i class="fa fa-plus"></i>
					</button>
				</td>
			</tr>
			HTML;
		}

		$parentescos = AhorroSimpleDao::GetCatalogoParentescos();
		$parentescosOptions = '<option value="">Seleccionar...</option>';
		if ($parentescos['success']) {
			foreach ($parentescos['datos'] as $key => $value) {
				$parentescosOptions .= "<option value='{$value['CODIGO']}'>{$value['DESCRIPCION']}</option>";
			}
		}

		View::set('tabla', $tabla);
		View::set('header', $this->_contenedor->header($extraHeader));
		View::set('footer', $this->_contenedor->footer($extraFooter));
		View::set('parentescosOptions', $parentescosOptions);
		View::render("contratos_lista_ahorro");
	}

	public function GetCliente()
	{
		echo json_encode(AhorroSimpleDao::GetCliente($_POST));
	}

	public function RegistraContrato()
	{
		echo json_encode(AhorroSimpleDao::RegistraContrato($_POST));
	}

	public function GetBeneficiarios()
	{
		echo json_encode(AhorroSimpleDao::GetBeneficiarios($_POST));
	}

	public function ActualizaBeneficiarios()
	{
		echo json_encode(AhorroSimpleDao::ActualizaBeneficiarios($_POST));
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
