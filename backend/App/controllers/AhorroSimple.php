<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\MasterDom;
use Core\Controller;
use App\models\Pagos as PagosDao;
use App\models\CallCenter as CallCenterDao;

class Pagos extends Controller
{

    private $_contenedor;


    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

  
    public function AhorroConsulta()
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

            function Validar() {
                fecha1 = moment((document.getElementById("Inicial").innerHTML = inputValue))
                fecha2 = moment((document.getElementById("Final").innerHTML = inputValue))

                dias = fecha2.diff(fecha1, "days")
                alert(dias)

                if (dias == 1) {
                    alert("si es")
                    return false
                }
                return false
            }

            Inicial.max = new Date().toISOString().split("T")[0]
            Final.max = new Date().toISOString().split("T")[0]

            function InfoAdmin() {
                swal("Info", "Este registro fue capturado por una administradora en caja", "info")
            }
            function InfoPhone() {
                swal(
                    "Info",
                    "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora",
                    "info"
                )
            }

            id_sucursal = getParameterByName("id_sucursal")
            if (id_sucursal != "") {
                const select_e = document.querySelector("#id_sucursal")
                select_e.value = id_sucursal
            }
        </script>
        HTML;

        $fechaActual = date('Y-m-d');
        $id_sucursal = $_GET['id_sucursal'];
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];

        $sucursales = PagosDao::ListaSucursales($this->__usuario);
        $getSucursales = '';
        if (
            $this->__perfil == 'ADMIN'
            || $this->__perfil == 'ACALL'
            || $this->__usuario == 'PMAB'
            || $this->__usuario == 'PAES'
            || $this->__usuario == 'COCS'
            || $this->__usuario == 'LGFR'
			|| $this->__usuario == 'FECR'
			|| $this->__usuario == 'JACJ'
			|| $this->__usuario == 'CILA'
            || $this->__usuario == 'VAMA'
			|| $this->__usuario == 'CRME'
			|| $this->__usuario == 'ZEPG'
			|| $this->__usuario == 'LRAF'
			|| $this->__usuario == 'MAAL'
			|| $this->__usuario == 'REHM'
			|| $this->__usuario == 'JUSA'
			|| $this->__usuario == 'MBAE'
			
        ) {
            $getSucursales .= '<option value="">TODAS</option>';
        }

        foreach ($sucursales as $key => $val2) {
            $getSucursales .= '<option value="' . $val2['ID_SUCURSAL'] . '">' . $val2['SUCURSAL'] . '</option>';
        }

        if ($Inicial != '' && $Final != '') {
            $Consulta = PagosDao::ConsultarPagosFechaSucursal($id_sucursal, $Inicial, $Final);

            $tabla = '';
            foreach ($Consulta as $key => $value) {
                if ($value['FIDENTIFICAPP'] ==  NULL) {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                    $mensaje = 'InfoAdmin();';
                } else {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                    $mensaje = 'InfoPhone();';
                }

                $monto = number_format($value['MONTO'], 2);
                $tabla .= <<<HTML
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                     <td style="padding: 0px !important;">{$value['REGION']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_SUCURSAL']}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
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
                View::render("pagos_consulta_busqueda_message");
            } else {
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::set('getSucursales', $getSucursales);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_consulta_busqueda");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('fechaActual', $fechaActual);
            View::set('getSucursales', $getSucursales);
            View::render("pagos_consulta_all");
        }
    }

    public function PagosAdd()
    {
        $pagos = new \stdClass();
        $credito = MasterDom::getDataAll('cdgns');
        $pagos->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo');
        $pagos->_ciclo = $ciclo;

        $fecha = MasterDom::getDataAll('Fecha');
        $pagos->_fecha = $fecha;

        $monto = MasterDom::getDataAll('monto');
        $pagos->_monto = $monto;

        $tipo = MasterDom::getDataAll('tipo');
        $pagos->_tipo = $tipo;

        $nombre = MasterDom::getDataAll('nombre');
        $pagos->_nombre = $nombre;

        $usuario = $this->__usuario;
        $pagos->_usuario = $usuario;

        $pagos->_ejecutivo = MasterDom::getData('ejecutivo');

        $pagos->_ejecutivo_nombre = MasterDom::getData('ejec');

        $id = PagosDao::insertProcedure($pagos);
        return $id;
    }

    public function HorariosAdd()
    {
        $pagos = new \stdClass();
        $fecha_registro = MasterDom::getDataAll('fecha_registro');
        $pagos->_fecha_registro = $fecha_registro;

        $sucursal = MasterDom::getDataAll('sucursal');
        $pagos->_sucursal = $sucursal;

        $hora = MasterDom::getDataAll('hora');
        $pagos->_hora = $hora;

        $id = PagosDao::insertHorarios($pagos);
        return $id;
    }

    public function HorariosUpdate()
    {
        $horario = new \stdClass();

        $sucursal = MasterDom::getDataAll('sucursal_e');
        $horario->_sucursal = $sucursal;

        $hora = MasterDom::getDataAll('hora_e');
        $horario->_hora = $hora;

        $id = PagosDao::updateHorarios($horario);
        return $id;
    }

    public function ValidaCorrectoPago()
    {
        $update = new \stdClass();

        $estatus = MasterDom::getDataAll('estatus');
        $update->_estatus = $estatus;

        $id_check = MasterDom::getDataAll('id_check');
        $update->_id_check = $id_check;

        $id = PagosDao::updateEstatusValidaPago($update);
        return $id;
    }

    public function PagosEdit()
    {
        $pagos = new \stdClass();


        $secuencia = MasterDom::getDataAll('secuencia_e');
        $pagos->_secuencia = $secuencia;

        $credito = MasterDom::getDataAll('cdgns_e');
        $pagos->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo_e');
        $pagos->_ciclo = $ciclo;

        $fecha = MasterDom::getDataAll('Fecha_e');
        $pagos->_fecha = $fecha;

        $fecha_aux = MasterDom::getDataAll('Fecha_e_r');
        $pagos->_fecha_aux = $fecha_aux;

        $monto = MasterDom::getDataAll('monto_e');
        $pagos->_monto = $monto;

        $tipo = MasterDom::getDataAll('tipo_e');
        $pagos->_tipo = $tipo;

        $nombre = MasterDom::getDataAll('nombre_e');
        $pagos->_nombre = $nombre;

        $usuario = $this->__usuario;
        $pagos->_usuario = $usuario;

        $pagos->_ejecutivo = MasterDom::getData('ejecutivo_e');

        $pagos->_ejecutivo_nombre = MasterDom::getData('ejec_e');

        $id = PagosDao::EditProcedure($pagos);
        return $id;
    }

    public function PagosEditAdmin()
    {
        $bitacora = self::RegistraBitacora($_POST);
        if ($bitacora === false) return "No se pudo recuperar el registro original";

        $post = new \ArrayObject($_POST, \ArrayObject::ARRAY_AS_PROPS);
        $resultado = PagosDao::EditProcedure($post);
        if ($resultado == '1 Proceso realizado exitosamente') {
            unset($_POST['_secuencia']);
            $registro = PagosDao::GetRegistroPagosDia($_POST);
            $_POST['modificado'] = json_encode($registro);
            PagosDao::ActualizaBitacoraAdmin($_POST);
        } else {
            PagosDao::EliminaBitacoraAdmin($_POST);
        }
        return $resultado;
    }

    public function Delete()
    {

        $cdgns = $_POST['cdgns'];
        $fecha = $_POST['fecha'];
        $usuario = $_POST['usuario'];
        $secuencia = $_POST['secuencia'];

        $id = PagosDao::DeleteProcedure($cdgns, $fecha, $usuario, $secuencia);
        return $id;
    }

    public function DeleteAdmin()
    {
        $bitacora = self::RegistraBitacora($_POST);
        if ($bitacora === false) return "No se pudo recuperar el registro original";

        $resultado = PagosDao::DeleteProcedure($_POST['cdgns'], $_POST['fecha'], $_POST['usuario'], $_POST['secuencia']);
        if ($resultado !== '1 Proceso realizado exitosamente') PagosDao::EliminaBitacoraAdmin($_POST);
        return $resultado;
    }

    public function RegistraBitacora(&$datos)
    {
        $registro = PagosDao::GetRegistroPagosDia($datos);
        if (count($registro) == 0) return false;
        $datos['original'] = json_encode($registro);

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $datos['soporte'] = fopen($_FILES['archivo']['tmp_name'], 'rb');
            $datos['nombre_soporte'] = $_FILES['archivo']['name'];
            $datos['tipo_soporte'] = $_FILES['archivo']['type'];
        }

        PagosDao::RegistroBitacoraAdmin($datos);
        if ($datos['soporte']) fclose($datos['soporte']);
        return true;
    }

    public function PagosEditApp()
    {

        $edit = new \stdClass();

        $edit->_id_registro = $_POST['id_registro'];
        $edit->_fecha_registro = $_POST['fecha_registro'];
        $edit->_tipo_pago_detalle = $_POST['tipo_pago_detalle'];
        $edit->_nuevo_monto = $_POST['nuevo_monto'];
        $edit->_comentario_detalle = $_POST['comentario_detalle'];
        $edit->_tipo_pago = $_POST['tipo_pago_detalle'];


        $id = PagosDao::updatePagoApp($edit);
        //return $id;

    }

    public function PagosAddApp()
    {

        $add_app = $_POST['cortecaja_pk'];
        $barcode = $_POST['barcode'];

        $id = PagosDao::AddPagoApp($add_app, $barcode);
    }

    public function PagosRegistro()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->confirmarMovimiento}
                {$this->parseaNumero}

                const Desactivado = () => showWarning("Usted no puede modificar este registro")
                const InfoAdmin = () => showInfo("Este registro fue capturado por una administradora en caja")
                const InfoPhone = () => showInfo("Este registro fue capturado por un ejecutivo en campo y procesado por una administradora")

                const getParameterByName = (name) => {
                    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                    let regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                        results = regex.exec(location.search)
                    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
                }

                const FunDelete_Pago = (secuencia, fecha, usuario) => {
                    credito = getParameterByName("Credito")
                    user = usuario

                    confirmarMovimiento("¿Segúro que desea eliminar el registro seleccionado?").then((continuar) => {
                        if (!continuar) return

                        $.ajax({
                            type: "POST",
                            url: "/Pagos/Delete/",
                            data: { cdgns: credito, fecha: fecha, secuencia: secuencia, usuario: user },
                            success: (response) => {
                                if (response !== "1 Proceso realizado exitosamente") showError(response)
                                else showSuccess("Registro eliminado correctamente")
                                location.reload()
                            }
                        })
                    })
                }

                const enviar_add = () => {
                    monto = $("#monto").val()

                    if (monto == "" || monto == 0) {
                        showWarning("Ingrese un monto mayor a $0.00")
                        $("#monto").focus()
                        return
                    }

                    if ($("#tipo").val() === "M") {
                        const parcialidad = parseaNumero($("#parcialidad").text())
                        const multaEsperada = parcialidad * 0.1

                        if (monto != multaEsperada) {
                            confirmarMovimiento(
                                "Diferencia de Multa",
                                null,
                                getMensajeMultaExcedente(multaEsperada)
                            ).then((continuar) => {
                                if (continuar) agregarPago()
                            })
                            return
                        }
                    }

                    agregarPago()
                }

                const agregarPago = () => {
                    texto = $("#ejecutivo :selected").text()
                    $.ajax({
                        type: "POST",
                        url: "/Pagos/PagosAdd/",
                        data: $("#Add").serialize() + "&ejec=" + texto,
                        success: (respuesta) => {
                            if (respuesta === "1 Proceso realizado exitosamente") {
                                showSuccess("Registro guardado exitosamente")
                                location.reload()
                            } else {
                                $("#modal_agregar_pago").modal("hide")
                                $("#monto").val("")
                                showError(respuesta)
                            }
                        }
                    })
                }

                const enviar_edit = () => {
                    monto = $("#monto_e").val()

                    if (monto == "" || monto == 0) {
                        showWarning("Ingrese un monto mayor a $0.00")
                        $("#monto_e").focus()
                        return
                    }

                    texto = $("#ejecutivo_e :selected").text()
                    $.ajax({
                        type: "POST",
                        url: "/Pagos/PagosEdit/",
                        data: $("#Edit").serialize() + "&ejec_e=" + texto,
                        success: function (respuesta) {
                            if (respuesta === "1 Proceso realizado exitosamente") {
                                showSuccess("Registro guardado exitosamente")
                                location.reload()
                            } else {
                                $("#modal_editar_pago").modal("hide")
                                $("#monto_e").val("")
                                showError(respuesta)
                            }
                        }
                    })
                }

                const BotonPago = (estatus, ciclo) => {
                    if (estatus === "LIQUIDADO") {
                        const ciclo_anterior = (ciclo - 1).toString().padStart(2, "0")

                        select = $("#tipo")
                        select.empty()
                        select.append(
                            $("<option>", {
                                value: "M",
                                text: "MULTA"
                            }),
                            $("<option>", {
                                value: "Z",
                                text: "MULTA GESTORES"
                            }),
                            $("<option>", {
                                value: "L",
                                text: "MULTA ELECTRÓNICA"
                            }),
                            $("<option>", {
                                value: "Y",
                                text: "PAGO EXCEDENTE"
                            })
                        )

                        if (ciclo != "01") {
                            $("#ciclo").empty()
                            $("#ciclo").append($("<option>", { value: ciclo, text: ciclo }))
                            $("#ciclo").append($("<option>", { value: ciclo_anterior, text: ciclo_anterior }))
                        }
                    }
                }

                const EditarPago = (fecha, cdgns, nombre, ciclo, tipo_pago, monto, ejecutivo, secuencia, estatus) => {
                    $("#Fecha_e").val(fecha)
                    $("#Fecha_e_r").val(fecha)
                    $("#cdgns_e").val(cdgns)
                    $("#nombre_e").val(nombre)
                    $("#ciclo_e").val(ciclo)
                    $("#monto_e").val(monto)
                    $("#secuencia_e").val(secuencia)

                    if (estatus == "LIQUIDADO") {
                        select = $("#tipo_e")
                        select.empty()
                        select.append(
                            $("<option>", {
                                value: "Z",
                                text: "MULTA GESTORES"
                            })
                        )
                    }

                    $("#tipo_e").val(tipo_pago)
                    $("#ejecutivo_e").val(ejecutivo)
                    $("#modal_editar_pago").modal("show")
                }

                const CambioOperacion = (operacion, ciclo) => {
                    const ciclo_anterior = (ciclo - 1).toString().padStart(2, "0")

                    $("#monto").prop("readonly", false)
                    $("#infoTipoOp").css("display", "none")
                    $("#monto").val("")

                    if (operacion.value == "M") {
                        if (ciclo != "01") $("#ciclo").append($("<option>", { value: ciclo_anterior, text: ciclo_anterior }))
                        $("#infoTipoOp").css("display", "block")
                    } else if (operacion.value === "S") {
                        const monto = parseaNumero($("#prestamo").text())
                        $("#monto").val(monto < 10001 ? "250.00" : "300.00").prop("readonly", true)
                        $("#infoTipoOp").css("display", "block")
                    } else {
                        $("#ciclo").empty()
                        $("#ciclo").append($("<option>", { value: ciclo, text: ciclo }))
                    }
                }

                const muestraInfoOp = () => {
                    const tipoSel = $("#tipo").val()
                    if (tipoSel === "M") showInfo(infoMulta())
                    if (tipoSel === "S") showInfo(infoSeguro())
                }

                const infoMulta = () => {
                    const div = document.createElement("div")
                    const titulo = document.createElement("h2")
                    const descripcion = document.createElement("p")
                    const politica = document.createElement("p")

                    titulo.innerHTML = "<b>Multa</b>"
                    descripcion.textContent = "Este tipo de pago se aplica cuando el cliente no realizó su pago en la fecha establecida, se le cobra una multa por retraso."
                    politica.innerHTML = "<b>La multa es del 10% sobre el monto de la parcialidad a pagar.</b>"

                    div.appendChild(titulo)
                    div.appendChild(descripcion)
                    div.appendChild(politica)

                    return div
                }

                const infoSeguro = () => {
                    const div = document.createElement("div")
                    const titulo = document.createElement("h2")
                    const descripcion = document.createElement("p")
                    const politicas = document.createElement("ul")

                    titulo.innerHTML = "<b>Seguro</b>"
                    descripcion.textContent = "Pago para el apoyo de protección familiar."
                    politicas.innerHTML = "<li>Si el monto del credito es menor a $10,000.00, el costo del seguro es de $250.00</li>"
                    politicas.innerHTML += "<li>Si el monto del credito es mayor a $10,000.00, el costo del seguro es de $300.00</li>"

                    div.appendChild(titulo)
                    div.appendChild(descripcion)
                    div.appendChild(politicas)

                    return div
                }

                const getMensajeMultaExcedente = (multaEsperada) => {
                    const div = document.createElement("div")
                    const descripcion = document.createElement("p")
                    const confirmacion = document.createElement("p")

                    descripcion.innerHTML = "El monto ingresado es diferente al 10% de la multa por retraso, la multa esperada es de: $" + multaEsperada.toFixed(2)
                    confirmacion.innerHTML = "<br><b>Valide que el monto ingresado corresponde con el monto capturado en la tarjeta del ejecutivo.</b>"

                    div.appendChild(descripcion)
                    div.appendChild(confirmacion)

                    return div
                }

                const validaSeguro = () =>{
                    const cicloActual = $("#cicloActual").text().trim()
                    const filas = $("#pagosRegistrados").DataTable().data()
                    const seguro = filas.filter(fila => fila[4] === cicloActual && fila[6] === "SEGURO").length === 0

                    const muestraS = seguro ? "block" : "none"
                    $("#tipo option[value='S']").css("display", muestraS)
                    $("#tipo_e option[value='S']").css("display", muestraS)
                }

                $(document).ready(() => {
                    configuraTabla("pagosRegistrados")
                    $("#enviaAdd").click(enviar_add)
                    $("#enviaEdit").click(enviar_edit)
                    $("#infoTipoOp").click(muestraInfoOp)
                    validaSeguro()
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Registro de Pagos')));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        $credito = $_GET['Credito'];
        if ($credito == '') return View::render("pagos_registro_all");

        $status = PagosDao::ListaEjecutivosAdmin($credito);
        $getStatus = '';
        foreach ($status[0] as $key => $val2) {
            $select = ($status[1] == $val2['ID_EJECUTIVO']) ? 'selected' : '';
            $getStatus .= '<option value="' . $val2['ID_EJECUTIVO'] . '" ' .  $select  . '>' . $val2['EJECUTIVO'] . '</option>';
        }

        $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);
        if ($AdministracionOne[0]['NO_CREDITO'] == '') {

            View::set('status', $getStatus);
            View::set('credito', $credito);
            View::set('usuario', $this->__usuario);
            return View::render("pagos_registro_busqueda_message");
        }

        $tabla = '';
        $fechaActual = date("Y-m-d");
        $horaActual = date("H:i:s");
        $dia = date("N");
        $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];
        $fue_dia_festivo = $AdministracionOne[2]['TOT'];
        $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'] ?? '10:00:00';
        $fin_f = $fechaActual;
        $inicio_f = $fechaActual;

        if ($horaActual <= $hora_cierre) {
            if ($dia == 1) {
                if ($fue_dia_festivo == 4) {
                    $date_past = strtotime('-6 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else if ($fue_dia_festivo == 3) {
                    $date_past = strtotime('-5 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else if ($fue_dia_festivo == 2) {
                    $date_past = strtotime('-4 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else if ($fue_dia_festivo == 1) {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else {
                    $date_past = strtotime('-3 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
            } else {
                if ($fue_dia_festivo == 1 && $dia == 2) {
                    $date_past = strtotime('-4 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else if ($fue_dia_festivo == 1 && $dia != 2) {
                    $date_past = strtotime('-2 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                } else {
                    $date_past = strtotime('-1 days', strtotime($fechaActual));
                    $date_past = date('Y-m-d', $date_past);
                }
            }

            $inicio_f = $date_past;
        }

        $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);
        foreach ($Administracion as $key => $value) {
            if ($value['FIDENTIFICAPP'] ==  NULL) {
                $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                $mensaje = 'InfoAdmin();';
            } else {
                $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                $mensaje = 'InfoPhone();';
            }

            if ($value['DESIGNATION'] == 'SI') {
                $editar = <<<HTML
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                HTML;
            } else {
                if ($fue_dia_festivo == 4) {
                    $date_past_b = strtotime('-6 days', strtotime($fechaActual));
                    $date_past_b = date('Y-m-d', $date_past_b);
                } else if ($fue_dia_festivo == 3) {
                    $date_past_b = strtotime('-5 days', strtotime($fechaActual));
                    $date_past_b = date('Y-m-d', $date_past_b);
                } else if ($fue_dia_festivo == 2) {
                    $date_past_b = strtotime('-4 days', strtotime($fechaActual));
                    $date_past_b = date('Y-m-d', $date_past_b);
                } else if ($fue_dia_festivo == 1) {
                    $date_past_b = strtotime('-3 days', strtotime($fechaActual));
                    $date_past_b = date('Y-m-d', $date_past_b);
                } else {
                    $date_past_b = strtotime('-3 days', strtotime($fechaActual));
                    $date_past_b = date('Y-m-d', $date_past_b);
                }

                $fecha_base = strtotime($value['FECHA']);
                $fecha_base = date('Y-m-d', $fecha_base);
                $inicio_b = $date_past_b;

                if (($inicio_b == $fecha_base) ||  $fecha_base >= $date_past_b && $AdministracionOne[2]['FECHA_CAPTURA'] <= $AdministracionOne[2]['FECHA_CAPTURA']) // aqui poner el dia en que se estaran capturando
                {
                    if ($horaActual <= $hora_cierre && $value['DESIGNATION'] == 'SI') { // AQUI SE HIZO EL AJUSTE 25/06/2025
                        $editar = <<<HTML
                            <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                        HTML;
                    } else {
                        $editar = <<<HTML
                            <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
                        HTML;
                    }
                } else {
                    $editar = <<<HTML
                        <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
                    HTML;
                }
            }

            $monto = number_format($value['MONTO'], 2);
            $tabla .= <<<HTML
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA_TABLA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
            HTML;
        }

        View::set('tabla', $tabla);
        View::set('Administracion', $AdministracionOne);
        View::set('credito', $credito);
        View::set('inicio_f', $inicio_f);
        View::set('fin_f', $fin_f);
        View::set('fechaActual', $fechaActual);
        View::set('status', $getStatus);
        View::set('usuario', $this->__usuario);
        View::set('cdgco', $this->__cdgco);
        View::render("pagos_registro_busqueda");
    }

    public function PagosConsultaUsuarios()
    {
        $extraHeader = <<<html
        <title>Registro de Pagos</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
        ponerElCursorAlFinal('Credito');
      
        function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    
        $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
        function FunDelete_Pago(secuencia, fecha, usuario) {
             credito = getParameterByName('Credito');
             user = usuario;
             ////////////////////////////
             swal({
              title: "¿Segúro que desea eliminar el registro seleccionado?",
              text: "",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"cdgns" : credito, "fecha" : fecha, "secuencia": secuencia, "usuario" : user},
                        success: function(response){
                            if(response == '1 Proceso realizado exitosamente')
                                {
                                    swal("Registro fue eliminado correctamente", {
                                      icon: "success",
                                    });
                                    location.reload();
                                    
                                }
                            else
                                {
                                    swal(response, {
                                      icon: "error",
                                    });
                                    
                                }
                        }
                    });
                  /////////////////
              } else {
                swal("No se pudo eliminar el registro");
              }
            });
             }
        function enviar_add(){	
             monto = document.getElementById("monto").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             document.getElementById("monto").focus();
                             
                        }
                }
            else
                {
                    texto = $("#ejecutivo :selected").text();
                   
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAdd/',
                    data: $('#Add').serialize()+ "&ejec="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                         location.reload();
                        
                        }
                        else {
                        $('#modal_agregar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                            document.getElementById("monto").value = "";
                        }
                    }
                    });
                }
    }
        function enviar_edit(){	
           
             monto = document.getElementById("monto_e").value; 
             
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             swal("Atención", "Ingrese un monto mayor a $0", "warning");
                             
                        }
                }
            else
                {
                    texto = $("#ejecutivo_e :selected").text(); 
             
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosEdit/',
                    data: $('#Edit').serialize()+ "&ejec_e="+texto,
                    success: function(respuesta) {
                         if(respuesta=='1 Proceso realizado exitosamente'){
                      
                        document.getElementById("monto_e").value = "";
                        
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                        $('#modal_editar_pago').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                        }
                    }
                    });
                }
    }
        function Desactivado()
         {
             swal("Atención", "Usted no puede modificar este registro", "warning");
         }
         function InfoAdmin()
         {
             swal("Info", "Este registro fue capturado por una administradora en caja", "info");
         }
         function InfoPhone()
         {
             swal("Info", "Este registro fue capturado por un ejecutivo en campo y procesado por una administradora", "info");
         }
    
      </script>
html;


        $credito = $_GET['Credito'];
        $tabla = '';

        $fechaActual = date("Y-m-d");
        $horaActual = date("H:i:s");
        $dia = date("N");

        $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);

        $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        if ($hora_cierre == '') {
            $hora_cierre = '10:00:00';
        } else {
            $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        }

        if ($horaActual <= $hora_cierre) {
            if ($dia == 1) {
                $date_past = strtotime('-3 days', strtotime($fechaActual));
                $date_past = date('Y-m-d', $date_past);
            } else {
                $date_past = strtotime('-1 days', strtotime($fechaActual));
                $date_past = date('Y-m-d', $date_past);
            }

            $inicio_f = $date_past;
            $fin_f = $fechaActual;
        } else {
            $inicio_f = $fechaActual;
            $fin_f = $fechaActual;
        }


        $status = PagosDao::ListaEjecutivosAdmin($credito);
        $getStatus = '';
        foreach ($status[0] as $key => $val2) {
            if ($status[1] == $val2['ID_EJECUTIVO']) {
                $select = 'selected';
            } else {
                $select = '';
            }

            $getStatus .= <<<html
                <option $select value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {

            if ($AdministracionOne[0]['NO_CREDITO'] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::set('usuario', $this->__usuario);
                View::render("pagos_consulta_p_busqueda_message");
            } else {
                $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);
                foreach ($Administracion as $key => $value) {

                    if ($value['FIDENTIFICAPP'] ==  NULL) {
                        $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                        $mensaje = 'InfoAdmin();';
                    } else {
                        $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                        $mensaje = 'InfoPhone();';
                    }

                    $editar = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle"  onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-trash"></i></button>
html;

                    $monto = number_format($value['MONTO'], 2);
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap onclick="{$mensaje}">{$medio}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;">{$value['EJECUTIVO']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
html;
                }

                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('fechaActual', $fechaActual);
                View::set('status', $getStatus);
                View::set('usuario', $this->__usuario);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_consulta_p_busqueda");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_consulta_p_all");
        }
    }

    public function CorteCaja()
    {
        $extraFooter = <<<html
      <script>
      
          function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
    
      $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
       
        function enviar_add(){	
             monto = document.getElementById("monto").value; 
            if(monto == '')
                {
                    if(monto == 0)
                        {
                             alertify.confirm('Ingresa un monto, mayor a $0');
                        }
                }
            else
                {
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAdd/',
                    data: $('#Add').serialize(),
                    success: function(respuesta) {
                        if(respuesta=='ok'){
                        alert('enviado'); 
                        document.getElementById("monto").value = "";
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                         location.reload();
                         
                        }
                        else {
                        $('#addnew').modal('hide')
                         swal(respuesta, {
                                      icon: "error",
                                    });
                           
                        }
                    }
                    });
                }
    }
        function FunprecesarPagos() {
           alert("procesando...");
           ///////
        
           ///////////
           
        }
      </script>
html;


        $consolidado = $_GET['Consolidado'];
        $tabla = '';
        $CorteCajaById = PagosDao::getAllCorteCajaByID($consolidado);


        $extraHeader = '';
        if ($consolidado != '') {
            $CorteCaja = PagosDao::getAllByIdCorteCaja(1);

            foreach ($CorteCaja as $key => $value) {

                //////////////////////////////////////
                if ($value['TIPO'] == 'P') {
                    $tipo_pago = 'PAGO';
                }
                if ($value['TIPO_PAGO'] == 'G') {
                    $tipo_pago = 'GARANTÍA';
                }
                if ($value['TIPO_PAGO'] == 'M') {
                }
                if ($value['TIPO_PAGO'] == 'A') {
                }
                if ($value['TIPO_PAGO'] == 'W') {
                }
                if ($value['ESTATUS_CAJA'] == '0') {
                    if ($value['INCIDENCIA'] == 1) {
                        $estatus = 'PENDIENTE, CON MODIFICACION';
                    } else {
                        $estatus = 'PENDIENTE';
                    }
                }
                //////////////////////////////////////

                if ($value['INCIDENCIA'] == 1) {
                    $incidencia = '<br><span class="count_top" style="font-size: 20px; color: gold"><i class="fa fa-warning"></i></span> <b>Incidencia:</b>' . $value['COMENTARIO_INCIDENCIA'];
                    $monto = '<span class="count_top" style="font-size: 16px; color: #017911">Monto a recibir: $' . number_format($value['NUEVO_MONTO']) . '</span><br>
                              <span class="count_top" style="font-size: 15px; color: #ff0066">Monto registrado: $' . number_format($value['MONTO']) . '</span>';
                    $botones = "";
                } else {
                    $incidencia = '';
                    $monto = '$ ' . number_format($value['MONTO']);

                    $botones =  <<<html
                    
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$tipo_pago}', '{$value['MONTO']}','{$estatus}', '{$value['EJECUTIVO']}', '{$value['SITUACION_NOMBRE']}');"><i class="fa fa-edit"></i></button>
                
html;
                }
                $tabla .= <<<html
                <tr>
                <td><span class="count_top" style="font-size: 25px"><i class="fa fa-mobile"></i></span></td>
                <td> {$value['FECHA']}</td>
                <td> {$value['CDGNS']}</td>
                <td> {$value['NOMBRE']}</td>
                <td> {$value['CICLO']}</td>
                <td> {$tipo_pago}</td>
                <td>{$monto}</td>
                <td>{$estatus}</td>
                <td><i class="fa fa-user"></i>   {$value['EJECUTIVO']} {$incidencia}</td>
                
                <td class="center">
                {$botones}
                </td>
                </tr>
html;
            }
            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if ($CorteCaja[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            } else {
                View::set('tabla', $tabla);
                View::set('CorteCajaById', $CorteCajaById);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }
        } else {

            $CorteCaja = PagosDao::getAllCorteCaja();

            foreach ($CorteCaja as $key => $value) {
                $tabla .= <<<html
                <tr>
                <td><span class="count_top" style="font-size: 30px"><i class="fa fa-mobile"></i></span></td>
                <td> {$value['NUM_PAG']}</td>
                <td><i class="fa fa-user"></i>  {$value['CDGPE']}</td>
                <td>$ {$value['MONTO_TOTAL']}</td>
                <td>$ {$value['MONTO_PAGO']}</td>
                <td>$ {$value['MONTO_GARANTIA']}</td>
                <td>$ {$value['MONTO_DESCUENTO']}</td>
                <td>$ {$value['MONTO_REFINANCIAMIENTO']}</td>
                <td></td>
                <td class="center" >
                    <a href="/Pagos/CorteCaja/?Consolidado={$value['CDGPE']}" type="submit" name="id_coordinador" class="btn btn-success"><span class="fa fa-product-hunt" style="color:white"></span> Liberar Pagos</a>
                </td>
                </tr>
            
html;
            }
            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if ($CorteCaja[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all"); ////CAmbiar a una en donde diga que no hay registros
            } else {
                View::set('tabla', $tabla);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all");
            }
            //////////////////////////////////////////////////////////////////
        }
    }

    public function Layout()
    {

        $extraHeader = <<<html
        <title>Layout Contable</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
          function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [21, 50, -1],
                    [21, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel").click(function(){
              $('#all').attr('action', '/Pagos/generarExcel/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
        });
          
          Inicial.max = new Date().toISOString().split("T")[0];
          Final.max = new Date().toISOString().split("T")[0];
         
    
      </script>
html;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];

        if ($Inicial == '' && $Final == '') {
            View::set('fechaActual', $fechaActual);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_layout_all");
        } else {
            ///////////////////////////////////////////////////////////////////////////////////
            $tabla = '';

            $Layout = PagosDao::GeneraLayoutContable($Inicial, $Final);
            if ($Layout != '') {
                foreach ($Layout as $key => $value) {

                    $monto = number_format($value['MONTO'], 2);
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['REFERENCIA']}</td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['MONEDA']}</td>
                </tr>
html;
                }
                if ($Layout[0] == '') {
                    View::set('header', $this->_contenedor->header($extraHeader));
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::set('fechaActual', $fechaActual);
                    View::render("pagos_layout_busqueda_message");
                } else {
                    View::set('tabla', $tabla);
                    View::set('Inicial', $Inicial);
                    View::set('Final', $Final);
                    View::set('header', $this->_contenedor->header($extraHeader));
                    View::set('footer', $this->_contenedor->footer($extraFooter));
                    View::render("pagos_layout_busqueda");
                }
            } else {
                View::set('fechaActual', $fechaActual);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_layout_all");
            }

            ////////////////////////////////////////////////////
        }
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

    public function CorteEjecutivoReimprimir()
    {
        $extraFooter = <<<HTML
        <script>
            $(document).ready(() => {
                configuraTabla("tbl-historico")
                document.getElementById("fInicio").addEventListener("change", () => validaFIF("fInicio", "fFin"))
                document.getElementById("fFin").addEventListener("change", () => validaFIF("fInicio", "fFin"))
            })

            const validaFIF = (idI, idF) => {
                const fechaI = document.getElementById(idI).valueAsDate
                const fechaF = document.getElementById(idF).valueAsDate
                if (fechaI && fechaF && fechaI > fechaF) {
                    document.getElementById(idI).valueAsDate = fechaF
                }
            }

            const configuraTabla = (id) => {
                $("#" + id).tablesorter()
                $("#" + id).DataTable({
                    lengthMenu: [
                        [10, 40, -1],
                        [10, 40, "Todos"]
                    ],
                    columnDefs: [
                        {
                            orderable: false,
                            targets: 0
                        }
                    ],
                    order: false,
                    language: {
                        emptyTable: "No hay datos disponibles",
                        paginate: {
                            previous: "Anterior",
                            next: "Siguiente",
                        },
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "Sin registros disponibles",
                    }
                })

                $("#"  + id + " input[type=search]").keyup(() => {
                    $("#example")
                        .DataTable()
                        .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                        .draw()
                })
            }

            const consultaServidor = (url, datos, fncOK, metodo = "POST", tipo = "JSON", tipoContenido = null) => {
                swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                const configuracion = {
                    type: metodo,
                    url: url,
                    data: datos,
                    success: (res) => {
                        swal.close()
                        fncOK(res)
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al procesar la solicitud.")
                    }
                }
                if (tipoContenido) configuracion.contentType = tipoContenido 
                $.ajax(configuracion)
            }

            const buscar = () => {
                const fechaI = document.getElementById("fInicio").value
                const fechaF = document.getElementById("fFin").value
                if (new Date(fechaI) > new Date(fechaF)) {
                    swal("Atención", "La fecha de inicio no puede ser mayor a la fecha final", "warning")
                    return
                }

                const datos = {
                    fInicio: fechaI,
                    fFin: fechaF
                }

                consultaServidor("/Pagos/GetPagosAppHistorico/", $.param(datos), (respuesta) => {
                    if (!respuesta) swal({ text: "No se encontraron pagos en el rango de fechas seleccionado.", icon: "error" })
                    
                    $("#tbl-historico").DataTable().destroy()
                    $("#tbl-historico tbody").html(respuesta)
                    configuraTabla("tbl-historico", true)
                })
            }

            const reimprime = (idComprobante) => {
                if (!idComprobante) return
                
                const titulo = 'Comprobante ' + idComprobante
                const ruta = window.location.origin + "/Pagos/Ticket/" + idComprobante
                
                muestraPDF(titulo, ruta)
            }

            const muestraPDF = (titulo, ruta) => {
                let plantilla = '<!DOCTYPE html>'
                plantilla += '<html lang="es">'
                plantilla += '<head>'
                plantilla += '<meta charset="UTF-8">'
                plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                plantilla += '<link rel="shortcut icon" href="' + window.location.origin + '/img/logo_ico.png">'
                plantilla += '<title>' + titulo + '</title>'
                plantilla += '</head>'
                plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
                plantilla += '<iframe src="' + ruta + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += '</body>'
                plantilla += '</html>'
            
                const blob = new Blob([plantilla], { type: 'text/html' })
                const url = URL.createObjectURL(blob)
                window.open(url, '_blank')
            }
        </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Histórico de Pagos App")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fInicio', date('Y-m-d', strtotime('-7 day')));
        View::set('fFin', date('Y-m-d'));
        View::set('tabla', $this->GetPagosAppHistorico());
        View::render("view_pagos_app_historico");
    }

    public function GetPagosAppHistorico()
    {
        $fi = $_POST['fInicio'] ? $_POST['fInicio'] : date('Y-m-d');
        $ff = $_POST['fFin'] ? $_POST['fFin'] : date('Y-m-d', strtotime('-7 day'));

        $pagos = PagosDao::ConsultarPagosAppHistorico($fi, $ff);

        $tabla = '';
        foreach ($pagos as $key => $value) {
            $pago = number_format($value['TOTAL_PAGOS'], 2);
            $multa = number_format($value['TOTAL_MULTA'], 2);
            $refinanciamiento = number_format($value['TOTAL_REFINANCIAMIENTO'], 2);
            $descuento = number_format($value['TOTAL_DESCUENTO'], 2);
            $garantia = number_format($value['GARANTIA'], 2);
            $monto_total = number_format($value['MONTO_TOTAL'], 2);

            $tabla .= <<<HTML
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['BARRAS']}</td>
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['NUM_PAGOS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;"><strong>{$value['FECHA_D']}</strong></td>
                    <td style="padding: 0px !important;">$ {$pago}</td>
                    <td style="padding: 0px !important;">$ {$multa}</td>
                    <td style="padding: 0px !important;">$ {$refinanciamiento}</td>
                    <td style="padding: 0px !important;">$ {$descuento}</td>
                    <td style="padding: 0px !important;">$ {$garantia}</td>
                    <td style="padding: 0px !important;">$ {$monto_total}</td>
                    <td style="padding: 0px !important;">
                        <button class="btn btn-success btn-circle" onclick="reimprime('{$value['BARRAS']}')"><i class="fa fa-edit"></i> Reimprimir recibo</button>
                    </td>
                </tr>
            HTML;
        }

        if ($_POST) echo $tabla;
        else return $tabla;
    }

    public function descargarArchivo()
    {
        $archivo = PagosDao::RecuperaSoporte($_GET);

        if (count($archivo) == 0) {
            echo "No se encontró el archivo solicitado.";
            return;
        }

        // Obtener los datos binarios del archivo correctamente
        $contenido = is_resource($archivo['SOPORTE']) ? stream_get_contents($archivo['SOPORTE']) : $archivo['SOPORTE'];

        // Enviar las cabeceras para la descarga
        header("Content-Type: " . $archivo['TIPO_SOPORTE']);
        header("Content-Disposition: attachment; filename=\"" . $archivo['NOMBRE_SOPORTE'] . "\"");
        header("Content-Length: " . strlen($contenido));

        // Limpiar el búfer de salida antes de imprimir el archivo
        ob_clean();
        flush();

        echo $contenido;
        exit; // Asegurar que el script se detiene después de enviar el archivo
    }
}
