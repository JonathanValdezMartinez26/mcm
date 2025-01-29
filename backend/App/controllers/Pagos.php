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

    public function index()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->confirmarMovimiento}
                {$this->parseaNumero}
                {$this->consultaServidor}

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
                    
                    confirmarMovimiento("¿Segúro que desea eliminar el registro seleccionado?")
                    .then((continuar) => {
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

                const BotonPago = (estatus) => {
                    if (estatus === "LIQUIDADO") {
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
                                value: "Y",
                                text: "PAGO EXCEDENTE"
                            })
                        )
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

                const muestraAdmin = (e) => {
                    const tr = e.target.tagName === "I" ? e.target.parentElement.parentElement.parentElement : e.target.parentElement.parentElement

                    const [, secuencia, cdgns, fecha_tabla, ciclo, monto, tipo, ejecutivo] = tr.children
                    const fecha = new Date(fecha_tabla.innerText.split("/").reverse().join("-"))
                    const fechaMin = new Date(fecha)
                    fechaMin.setDate(fecha.getDate() - 20)
                    
                    $("#nombre_admin").val($("#nombreCliente").text())
                    $("#secuencia_admin").val(secuencia.innerText)
                    $("#cdgns_admin").val(cdgns.innerText)
                    $("#Fecha_admin_r").val(fecha.toISOString().split("T")[0])
                    $("#Fecha_admin").val(fecha.toISOString().split("T")[0])
                    $("#Fecha_admin").attr("max", fecha.toISOString().split("T")[0])
                    $("#Fecha_admin").attr("min", fechaMin.toISOString().split("T")[0])
                    $("#ciclo_admin").val(ciclo.innerText)
                    $("#monto_admin").val(parseaNumero(monto.innerText))
                    $("#tipo_admin").val(tipo.innerText.charAt(0))
                    $("#ejecutivo_admin").val($("#ejecutivo_admin option").filter((i, e) => e.text === ejecutivo.innerText).val())

                    $("#modal_admin").modal("show")
                }

                const justificacion = (tipo) => {
                    $("#tituloJustificacion").text(tipo === 0 ? "Editar Pago" : "Eliminar Pago")
                    $("#tipoMovAdmin").val(tipo)
                    $("#modal_justificacion").modal("show")
                }

                const enviarCambios = () => {
                    const tipo = $("#tipoMovAdmin").val()
                    const datos = new FormData();
                    let url

                    if (tipo == 0) {
                        url = "/Pagos/PagosEditAdmin/"
                        datos.append("_secuencia", $("#secuencia_admin").val())
                        datos.append("_credito", $("#cdgns_admin").val())
                        datos.append("_ciclo", $("#ciclo_admin").val())
                        datos.append("_fecha", $("#Fecha_admin").val())
                        datos.append("_fecha_aux", $("#Fecha_admin_r").val())
                        datos.append("_monto", $("#monto_admin").val())
                        datos.append("_tipo", $("#tipo_admin").val())
                        datos.append("_nombre", $("#nombre_admin").val())
                        datos.append("_usuario", "$this->__usuario")
                        datos.append("_ejecutivo", $("#ejecutivo_admin").val())
                        datos.append("_ejecutivo_nombre", $("#ejecutivo_admin option:selected").text())
                    } else {
                        url = "/Pagos/DeleteAdmin/"
                        datos.append("cdgns", $("#cdgns_admin").val())
                        datos.append("secuencia", $("#secuencia_admin").val())
                        datos.append("fecha", $("#Fecha_admin_r").val())
                        datos.append("usuario", "$this->__usuario")
                    }

                    datos.append("justificacion", $("#justificacion").val())

                    const archivo = $("#archivo")[0].files[0]

                    if (archivo) {
                        const tiposPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
                        const tamPermitido = 2 * 1024 * 1024

                        if (archivo.size > tamPermitido) return showWarning("El archivo excede el tamaño permitido (2MB).")
                        if (!tiposPermitidos.includes(archivo.type)) return showWarning("Tipo de archivo no valido, solo se permiten JPG, PNG o PDF.")
                        datos.append("archivo", archivo)
                    }

                    consultaServidor(url, datos, (respuesta) => {
                        if (respuesta === "1 Proceso realizado exitosamente") {
                            const texTipo = tipo == 0 ? "editado" : "eliminado"
                            showWait("Registro " + texTipo + " exitosamente, espere un momento se recargará la página.")
                            location.reload()
                        } else {
                            showError(respuesta)
                            $("#modal_justificacion").modal("hide")
                        }
                    }, "POST", "TEXT", false, false)
                }

                $(document).ready(() => {
                    configuraTabla("pagosRegistrados")
                    $("#enviaAdd").click(enviar_add)
                    $("#enviaEdit").click(enviar_edit)
                    $(".$this->__usuario").click(muestraAdmin)
                    $("#editAdmin").click(() => justificacion(0))
                    $("#deleteAdmin").click(() => justificacion(1))
                    $("#enviaJustificacion").click(enviarCambios)
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Administración de Pagos')));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        $credito = $_GET['Credito'];
        if ($credito == '') return View::render("pagos_admin_all");

        $getStatus = '';
        $status = PagosDao::ListaEjecutivosAdmin($credito);
        foreach ($status[0] as $key => $val2) {
            $select = $status[1] == $val2['ID_EJECUTIVO'] ? 'selected' : '';
            $getStatus .= '<option value="' . $val2['ID_EJECUTIVO'] . '"' . $select . '>' . $val2['EJECUTIVO'] . '</option>';
        }

        $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito, $this->__perfil, $this->__usuario);
        $hora_cierre = $AdministracionOne[1]['HORA_CIERRE'];
        $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];

        if ($AdministracionOne[0]['NO_CREDITO'] == '') {
            View::set('status', $getStatus);
            View::set('credito', $credito);
            View::set('usuario', $this->__usuario);
            View::render('pagos_admin_busqueda_message');
            return;
        }

        $fechaActual = date("Y-m-d");
        $tabla = '';
        $dias = date("N") == 1 ? '-3 days' : '-4 days';
        $date_past = strtotime($dias, strtotime($fechaActual));
        $date_past = date('Y-m-d', $date_past);
        $inicio_f = $date_past;
        $fin_f = $fechaActual;
        $Administracion = PagosDao::ConsultarPagosAdministracion($credito, $hora_cierre);

        foreach ($Administracion as $key => $value) {
            if ($value['FIDENTIFICAPP'] ==  NULL) {
                $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                $mensaje = 'InfoAdmin();';
            } else {
                $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-phone"></i></span>';
                $mensaje = 'InfoPhone();';
            }

            if ($value['DESIGNATION_ADMIN'] == 'SI') {
                $editar = <<<HTML
                    <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                HTML;
            } else {
                $date_past = strtotime('-4 days', strtotime($fechaActual));
                $date_past = date('Y-m-d', $date_past);
                $fecha_base = strtotime($value['FECHA']);
                $fecha_base = date('Y-m-d', $fecha_base);
                $inicio_f = $date_past;

                if ($inicio_f == $fecha_base) {
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
            }

            $lista_SU = ['MCDP', 'AMGM'];


            if (in_array($this->__usuario, $lista_SU) && $inicio_f != $fecha_base && $value['DESIGNATION_ADMIN'] != 'SI') $editar .= <<<HTML
                <button type="button" class="btn btn-warning btn-circle $this->__usuario"><i class="fa fa-key"></i></button>
            HTML;

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
                    <td style="padding: 0px !important;"><span class="fa fa-key"></span> {$value['NOMBRE_CDGPE']}</td>
                    <td style="padding: 0px !important;" class="center">{$editar}</td>
                </tr>
            HTML;
        }

        View::set('tabla', $tabla);
        View::set('Administracion', $AdministracionOne);
        View::set('credito', $credito);
        View::set('inicio_f', $inicio_f);
        View::set('fin_f', $fin_f);
        View::set('status', $getStatus);
        View::set('usuario', $this->__usuario);
        View::render('pagos_admin_busqueda');
    }

    public function AjusteHoraCierre()
    {
        $extraHeader = <<<html
        <title>Ajuste Cierre Caja</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
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
       
        function enviar_add_horario(){	
             sucursal = document.getElementById("sucursal").value; 
             
            if(sucursal == '')
                {
                    
                      swal("Atención", "Ingrese un monto mayor a $0", "warning");
                      document.getElementById("monto").focus();
                        
                }
            else
                {
                    
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosAdd/',
                    data: $('#Add_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
                }
    }
    
        function enviar_update_horario(){	
          
                    $.ajax({
                    type: 'POST',
                    url: '/Pagos/HorariosUpdate/',
                    data: $('#Update_AHC').serialize(),
                    success: function(respuesta) {
                         if(respuesta=='1'){
                      
                         swal("Registro actualizado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }
                        else {
                         swal(respuesta, {
                                      icon: "error",
                                    });
                         //location.reload();
                         
                        }
                    }
                    });
    }
      
      
      </script>
html;

        $tabla = '';
        $horaActual = date("H:i:s");
        $opciones_suc = '';

        $ComboSucursales = CallCenterDao::getComboSucursalesHorario();


        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
        }

        $Administracion = PagosDao::ConsultarHorarios();


        foreach ($Administracion as $key => $value) {

            if ($value['HORA_PRORROGA'] == 'NULL') {
                $prorroga = 'NO TIENE';
            } else {
                $prorroga = $value['HORA_PRORROGA'];
            }

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CODIGO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE']}</td>
                    <td style="padding: 0px !important;">De (08:00:00 a.m) A (<strong>{$value['HORA_CIERRE']}</strong> a.m)</td>
                    <td style="padding: 0px !important;">$prorroga</td>
                    <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
                     <td style="padding: 0px !important;">
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarHorario('{$value['CDGCO']}', '{$value['NOMBRE']}' , '{$value['HORA_CIERRE']}');"><i class="fa fa-edit"></i></button>
                     </td>
                </tr>
html;
        }

        View::set('tabla', $tabla);
        View::set('usuario', $this->__usuario);
        View::set('opciones_suc', $opciones_suc);

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("horarios_caja_sucursal");
    }

    public function DiasFestivos()
    {
        $extraHeader = <<<html
        <title>Días Festivos</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
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

      </script>
html;

        $tabla = '';
        $opciones_suc = '';


        $Administracion = PagosDao::ConsultarDiasFestivos();


        foreach ($Administracion as $key => $value) {

            if ($value['HORA_PRORROGA'] == 'NULL') {
                $prorroga = 'NO TIENE';
            } else {
                $prorroga = $value['HORA_PRORROGA'];
            }

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['DESCRIPCION']}</td>
                    <td style="padding: 0px !important;"><strong>{$value['FECHA_CAPTURA']}</strong></td>
                     <td style="padding: 0px !important;">
                        <button style="display: none;" type="button" class="btn btn-success btn-circle" onclick="EditarHorario('{$value['CDGCO']}', '{$value['NOMBRE']}' , '{$value['HORA_CIERRE']}');"><i class="fa fa-edit"></i></button>
                     </td>
                </tr>
html;
        }

        View::set('tabla', $tabla);
        View::set('usuario', $this->__usuario);
        View::set('opciones_suc', $opciones_suc);
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("dias_festivos_caja_sucursal");
    }

    public function CorteEjecutivo()
    {
        $extraHeader = <<<html
        <title>Corte de Pagos App</title>
        <link rel="shortcut icon" href="/img/logo.png">
        html;


        $extraFooter = <<<html
        <script>
        $(document).ready(function () {
            $("#muestra-cupones").tablesorter()
            var oTable = $("#muestra-cupones").DataTable({
                lengthMenu: [
                    [30, 50, -1],
                    [30, 50, "Todos"]
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
        })

        function enviar_add_horario() {
            sucursal = document.getElementById("sucursal").value
            if (sucursal == "") {
                swal("Atención", "Ingrese un monto mayor a $0", "warning")
                document.getElementById("monto").focus()
            } else {
                $.ajax({
                    type: "POST",
                    url: "/Pagos/HorariosAdd/",
                    data: $("#Add_AHC").serialize(),
                    success: function (respuesta) {
                        if (respuesta == "1") {
                            swal("Registro guardado exitosamente", {
                                icon: "success"
                            })
                            location.reload()
                        } else {
                            swal(respuesta, {
                                icon: "error"
                            })
                            //location.reload();
                        }
                    }
                })
            }
        }

        function enviar_update_horario() {
            $.ajax({
                type: "POST",
                url: "/Pagos/HorariosUpdate/",
                data: $("#Update_AHC").serialize(),
                success: function (respuesta) {
                    if (respuesta == "1") {
                        swal("Registro actualizado exitosamente", {
                            icon: "success"
                        })
                        location.reload()
                    } else {
                        swal(respuesta, {
                            icon: "error"
                        })
                        //location.reload();
                    }
                }
            })
        }

        function editar_pago(id, comentario, tipo, monto, nuevo_monto, incidencia) {
            let USDollar = new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "USD"
            })

            if (incidencia == 1) {
                document.getElementById("nuevo_monto").value = nuevo_monto
            } else {
                document.getElementById("nuevo_monto").value = monto
            }
            document.getElementById("monto_detalle").value = USDollar.format(monto)
            document.getElementById("comentario_detalle").value = comentario
            document.getElementById("id_registro").value = id

            select = document.querySelector("#tipo_pago_detalle")
            select.value = tipo

            $("#modal_agregar_horario").modal("show")
        }

        function enviar_add_edit_app() {
            nuevo_monto = document.getElementById("nuevo_monto").value

            if (nuevo_monto == "") {
                if (nuevo_monto == 0) {
                    swal("Atención", "Ingrese un monto mayor a $0", "warning")
                    document.getElementById("monto").focus()
                }
            } else {
                $.ajax({
                    type: "POST",
                    url: "/Pagos/PagosEditApp/",
                    data: $("#Add_Edit_Pago").serialize(),
                    success: function (respuesta) {
                        if (respuesta == "1") {
                            document.getElementById("nuevo_monto").value = ""

                            swal("Registro editado exitosamente", {
                                icon: "success"
                            })
                            location.reload()
                        } else {
                            $("#modal_agregar_pago").modal("hide")
                            swal(respuesta, {
                                icon: "error"
                            })
                            document.getElementById("monto").value = ""
                        }
                    }
                })
            }
        }

        function check_pagos(id) {
            var checkbox = document.getElementById(id)
            var isChecked = document.getElementById(id).checked
            if (isChecked == false) {
                var returnVal = confirm("¿Estas seguro de que deseas desactivar esta casilla?")
                if (returnVal == true) {
                    estatus = 0
                    //////////////////////////
                    $.ajax({
                        type: "POST",
                        url: "/Pagos/ValidaCorrectoPago/",
                        data: "estatus=" + estatus + "&id_check=" + id,
                        success: function (respuesta) {
                            if (respuesta == "1") {
                                swal("Registro desactivado exitosamente", {
                                    icon: "success"
                                })
                                location.reload()
                                return false
                            } else {
                                swal(respuesta, {
                                    icon: "error"
                                })
                                location.reload()
                            }
                        }
                    })
                    //////////////////////////
                    return false
                } else {
                    alert("No paso nada")
                    return false
                }
            } else {
                var returnVal = confirm("Estas seguro de Calcular?")
                if (returnVal == true) {
                    estatus = 1
                    //////////////////////////
                    $.ajax({
                        type: "POST",
                        url: "/Pagos/ValidaCorrectoPago/",
                        data: "estatus=" + estatus + "&id_check=" + id,
                        success: function (respuesta) {
                            if (respuesta == "1") {
                                swal("Registro actualizado exitosamente", {
                                    icon: "success"
                                })
                                location.reload()
                                //return false;
                            } else {
                                swal(respuesta, {
                                    icon: "error"
                                })
                                //location.reload();
                            }
                        }
                    })
                    //////////////////////////
                } else {
                    document.getElementById(id).checked = false
                    return false
                }
            }
        }

        function boton_resumen_pago() {
            validados = document.getElementById("validados_r")
            contenido_validados = validados.innerHTML

            total = document.getElementById("total_r")
            contenido_total = total.innerHTML

            operacion = parseInt(contenido_total) - parseInt(contenido_validados)

            if (contenido_validados == contenido_total)
                $("#modal_resumen").modal({ backdrop: "static", keyboard: false }, "show")
            else
                swal(
                    "Atención",
                    "Debe validar todos los pagos (tiene " + operacion + " registros pendientes)",
                    "warning"
                )
        }

        function boton_ticket(barcode) {
            $("#all").attr("action", "/Pagos/Ticket/" + barcode + "/")
            $("#all").attr("target", "_blank")
            $("#all").submit()
        }

        function boton_terminar(barcode) {
            let tabla = document.querySelector("#terminar_resumen").querySelector("tbody")
            let filas = tabla.querySelectorAll("tr")
            
            swal({
                title: "Procesando Pagos",
                text: "Espere por favor...",
                buttons: false,
                onOpen: function() {
                    swal.showLoading()
                }
            })

            Array.from(filas).forEach((fila) => {
                pk = fila.cells[0].innerText
                $.ajax({
                    type: 'POST',
                    url: '/Pagos/PagosAddApp/',
                    data: 'cortecaja_pk='+pk+'&barcode='+barcode
                })
            })

            location.reload()
        }
        </script>
        html;

        $tabla = '';

        $ejecutivo = $_GET['MTYQW'];
        $fecha = $_GET['FEC'];
        $suc = $_GET['SUC'];
        $barcode = $_GET['BCODE'];

        if ($ejecutivo == '' || $fecha == '' || $suc == '' || $barcode == '') {
            $Administracion = PagosDao::ConsultarPagosApp();

            foreach ($Administracion as $key => $value) {
                $pago = number_format($value['TOTAL_PAGOS'], 2);
                $multa = number_format($value['TOTAL_MULTA'], 2);
                $refinanciamiento = number_format($value['TOTAL_REFINANCIAMIENTO'], 2);
                $descuento = number_format($value['TOTAL_DESCUENTO'], 2);
                $garantia = number_format($value['GARANTIA'], 2);
                $monto_total = number_format($value['MONTO_TOTAL'], 2);

                $tabla .= <<<html
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
                        <a href="/Pagos/CorteEjecutivo/?MTYQW={$value['CDGOCPE']}&FEC={$value['FECHA']}&BCODE={$value['BARRAS']}&SUC={$value['COD_SUC']}" type="button" class="btn btn-success btn-circle"><i class="fa fa-edit"></i> Procesar Pagos</a>
                     </td>
                </tr>
html;
            }
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::render("view_pagos_app_ejecutivos");
        } else {
            $Administracion = PagosDao::ConsultarPagosAppDetalle($ejecutivo, $fecha, $suc);
            $validar = $Administracion[0];

            if ($validar == NULL) {
                //////////////////////aqui
                $Administracion = PagosDao::ConsultarPagosAppDetalleImprimir($ejecutivo, $fecha, $suc);
                $Ejec = $Administracion[0][0];
                foreach ($Administracion[0] as $key => $value) {

                    if ($value['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }


                    $monto = number_format($value['MONTO'], 2);
                    $nuevo_monto = number_format($value['NUEVO_MONTO'], 2);
                    $id_check = $value['CORTECAJA_PAGOSDIA_PK'];
                    if ($value['TIPO'] == 'P' || $value['TIPO'] == 'M') {
                        $color_celda = "";
                        $boton_visible = "";
                        $check_visible = '';
                    } else {
                        $color_celda = "background-color: #FFC733 !important;";
                        $boton_visible = "disabled";
                        $check_visible = 'display:none;';
                    }

                    if ($value['ESTATUS_CAJA'] == 1) {
                        $selected = 'checked';
                    } else {
                        $selected = '';
                    }

                    if ($value['INCIDENCIA'] == 1) {
                        $campo = '<div><del>$' . $monto . '</del></div> <div style="font-size: 20px!important;"> $' . $nuevo_monto . '</div>';
                    } else {
                        $campo = '<div style="font-size: 20px!important;">$' . $monto . '</div>';
                    }

                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important; $color_celda">{$value['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td style="padding: 10px !important; text-align: left; $color_celda">
                        <div>#CRÉDITO: <b>{$value['CDGNS']}</b></div>
                        <div>NOMBRE: <b>{$value['NOMBRE']}</b></div>
                        
                        <div>CICLO: <b>{$value['CICLO']}</b></div>
                        <div>FECHA DE PAGO: <b>{$value['FECHA']}</b></div>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$tipo_pago}</td>
                    <td style="padding: 10px !important; $color_celda">
                        {$campo}
                         <input style="{$check_visible}" class="form-check-input" type="checkbox" value="" id="$id_check" name="$id_check" onclick="check_pagos('$id_check');" $selected disabled>
                          <label style="{$check_visible}" class="form-check-label" for="flexCheckDefault">
                            Validado
                         </label>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$value['COMENTARIO_INCIDENCIA']}</td>
                    <td style="padding: 10px !important; $color_celda">{$value['FIDENTIFICAPP']}</td>
                    
                     <td style="padding-top: 30px !important;">
                        <b>Pago Procesado</b>
                     </td>
                </tr>
html;
                }


                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('ejecutivo', $ejecutivo);
                View::set('DetalleGlobal', $Administracion[1]);
                View::set('Ejecutivo', $Ejec['EJECUTIVO']);
                View::set('barcode', $barcode);
                View::render("view_pagos_app_detalle_imprimir");
            } else {
                $AdministracionResumen = PagosDao::ConsultarPagosAppResumen($ejecutivo, $fecha, $suc);
                $Ejec = $Administracion[0][0];
                foreach ($Administracion[0] as $key => $value) {

                    if ($value['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }


                    $monto = number_format($value['MONTO'], 2);
                    $nuevo_monto = number_format($value['NUEVO_MONTO'], 2);
                    $id_check = $value['CORTECAJA_PAGOSDIA_PK'];
                    if ($value['TIPO'] == 'P' || $value['TIPO'] == 'M') {
                        $color_celda = "";
                        $boton_visible = "";
                        $check_visible = '';
                    } else {
                        $color_celda = "background-color: #FFC733 !important;";
                        $boton_visible = "disabled";
                        $check_visible = 'display:none;';
                    }

                    if ($value['ESTATUS_CAJA'] == 1) {
                        $selected = 'checked';
                    } else {
                        $selected = '';
                    }

                    if ($value['INCIDENCIA'] == 1) {
                        $campo = '<div><del>$' . $monto . '</del></div> <div style="font-size: 20px!important;"> $' . $nuevo_monto . '</div>';
                    } else {
                        $campo = '<div style="font-size: 20px!important;">$' . $monto . '</div>';
                    }
                    $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important; $color_celda">{$value['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td style="padding: 10px !important; text-align: left; $color_celda">
                        <div>#CRÉDITO: <b>{$value['CDGNS']}</b></div>   
                        <div>NOMBRE: <b>{$value['NOMBRE']}</b></div>
                        <div>CICLO: <b>{$value['CICLO']}</b></div>
                        <div>FECHA DE PAGO: <b>{$value['FECHA']}</b></div>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$tipo_pago}</td>
                    <td style="padding: 10px !important; $color_celda">
                        {$campo}
                         <input style="{$check_visible}" class="form-check-input" type="checkbox" value="" id="$id_check" name="$id_check" onclick="check_pagos('$id_check');" $selected>
                          <label style="{$check_visible}" class="form-check-label" for="flexCheckDefault">
                            Validado
                         </label>
                    </td>
                    <td style="padding: 10px !important; $color_celda">{$value['COMENTARIO_INCIDENCIA']}</td>
                    <td style="padding: 10px !important; $color_celda">{$value['FIDENTIFICAPP']}</td>
                    
                     <td style="padding-top: 30px !important;">
                        <button type="button" class="btn btn-success btn-circle" onclick="editar_pago('{$value['CORTECAJA_PAGOSDIA_PK']}', '{$value['COMENTARIO_INCIDENCIA']}', '{$value['TIPO']}', '{$value['MONTO']}', '{$value['NUEVO_MONTO']}', '{$value['INCIDENCIA']}');"><i class="fa fa-edit"></i> Editar Pago</button>
                     </td>
                </tr>
html;
                }

                $AdministracionOne = PagosDao::ConsultarCierreCajaCajera($this->__usuario);
                $situacion_credito = $AdministracionOne[0]['SITUACION_NOMBRE'];
                $fechaActual = date("Y-m-d");
                $horaActual = date("H:i:s");
                $dia = date("N");

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

                $tabla_resumen = '';
                foreach ($AdministracionResumen[0] as $key => $value_resumen) {

                    $ejecutivo = $value_resumen['EJECUTIVO'];
                    $cdgpe_ejecutivo = $value_resumen['CDGPE'];


                    if ($value_resumen['TIPO'] == 'P') {
                        $tipo_pago = 'PAGO';
                    } else if ($value_resumen['TIPO'] == 'M') {
                        $tipo_pago = 'MULTA';
                    } else if ($value_resumen['TIPO'] == 'G') {
                        $tipo_pago = 'GARANTIA';
                    } else if ($value_resumen['TIPO'] == 'D') {
                        $tipo_pago = 'MULTA';
                    } else if ($value_resumen['TIPO'] == 'R') {
                        $tipo_pago = 'REFINANCIAMIENTO';
                    }

                    if ($value_resumen['INCIDENCIA'] == 1) {
                        $campo_resumen = '$' . number_format($value_resumen['NUEVO_MONTO'], 2);
                    } else {
                        $campo_resumen = '$' . number_format($value_resumen['MONTO'], 2);
                    }

                    $tabla_resumen .= <<<html
                <tr>
                    <td style="display: none;" id="pk" style="padding: 10px !important; background: #9d9d9d">{$value_resumen['CORTECAJA_PAGOSDIA_PK']}</td>
                    <td id="codigo" style="text-align: left; padding: 3px !important;">
                        <b> {$value_resumen['CDGNS']}</b>
                    </td>
                    <td id="nombre" style="text-align: left; padding: 3px !important;">
                        {$value_resumen['NOMBRE']}
                    </td>
                    <td id="ciclo" style="padding: 3px !important;"><b>{$value_resumen['CICLO']}</b></td>
                    <td id="tipo" style="padding: 3px !important;">{$tipo_pago}</td>
                     
                    <td id="monto" style="background: #173b00; color: #fdfdfd; padding: 3px !important; width:94px !important;"><b>{$campo_resumen}</b></td>
                   
                </tr>
html;
                }
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('ejecutivo', $ejecutivo);
                View::set('cdgpe_ejecutivo', $cdgpe_ejecutivo);
                View::set('tabla_resumen', $tabla_resumen);
                View::set('DetalleGlobal', $Administracion[1]);
                View::set('Ejecutivo', $Ejec['EJECUTIVO']);
                View::set('inicio_f', $inicio_f);
                View::set('fin_f', $fin_f);
                View::set('barcode', $barcode);
                View::set('fechaActual', $fechaActual);
                View::render("view_pagos_app_detalle");
            }
        }
    }

    public function Ticket($barcode)
    {
        $mpdf = new \mPDF([
            'mode' => 'c',
        ]);
        $mpdf->defaultPageNumStyle = 'I';
        $mpdf->h2toc = array('H5' => 0, 'H6' => 1);

        $style = <<<html
      <style>
     
       .titulo{
          width:100%;
          margin-top: 30px;
          color: #b92020;
          margin-left:auto;
          margin-right:auto;
        }
        
        body {
          padding: 50px;
        }
        
        * {
          box-sizing: border-box;
        }
        
        .receipt-main {
          display: inline-block;
          width: 100%;
          padding: 15px;
          font-size: 12px;
          border: 1px solid #000;
        }
        
        .receipt-title {
          text-align: center;
          text-transform: uppercase;
          font-size: 20px;
          font-weight: 600;
          margin: 0;
        }
          
        .receipt-label {
          font-weight: 600;
        }
        
        .text-large {
          font-size: 16px;
        }
        
        .receipt-section {
          margin-top: 10px;
        }
        
        .receipt-footer {
          text-align: center;
          background: #ff0000;
        }
        
        .receipt-signature {
          height: 80px;
          margin: 50px 0;
          padding: 0 50px;
          background: #fff;
          
          .receipt-line {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
          }
          
          p {
            text-align: justify;
            margin: 0;
            font-size: 17px;
          }
        }
      </style>
html;
        $complemento = PagosDao::getByIdReporte($barcode);

        $cant_total = "$" . number_format($complemento[0]['TOTAL'], 2);

        $tabla = <<<html

        <div class="receipt-main">
         <table class="table">
             <tr>
                 <th style="width: 600px;" class="text-right">
                    <p class="receipt-title"><b>Recibo de Pago</b></p>
                 </th>
                 <th style="width: 10px;" class="text-right">
                    <img src="img/logo.png" alt="Esta es una descripcion alternativa de la imagen para cuando no se pueda mostrar" width="60" height="50" align="left"/>
                 </th>
             </tr>
        </table>
         
          <div class="receipt-section pull-left">
            <span class="receipt-label text-large">#FOLIO:</span>
            <span class="text-large"><b>{$barcode}</b></span>
          </div>
          
           <div class="receipt-section pull-left">
            <span class="receipt-label text-large">FECHA DE COBRO:</span>
            <span class="text-large">{$complemento[0]['FECHA_GRUPO']}</span>
          </div>
          
          
          <div class="clearfix"></div>
          
          <div class="receipt-section">
        
            <p>Recibí del ejecutiv(a) <b>{$complemento[0]['EJECUTIVO']}</b>, la cantidad de <b>{$cant_total} M.N</b>, 
            por concepto de recoleccion de <b>pagos varios</b> <u>({$complemento[0]['TOTAL_VALIDADOS']} pagos)</u> de Financiera Más con Menos, sucursal <u>{$complemento[0]['NOMBRE_SUC']}</u>, con aplicación a la fecha: <b>31/10/2023</b>.
             </p>
             <p>Así mismo el ejecutivo firma de conformidad, la entrega a detalle de los siguientes pagos, en donde se especifica número de crédito, ciclo, nombre completo del cliente, tipo de pago y monto:</p>
          </div>
          
          <hr>
          
        
       <div class="table-responsive-sm">
          <table class="table">
              <thead>
                 <tr>
                     <th># Crédito</th>
                     <th>Nombre del Cliente</th>
                     <th>Ciclo</th>
                     <th width="19%" class="text-right">Tipo</th>
                     <th class="text-right">Monto</th>
                 </tr>
              </thead>
                  <tbody>
                     
html;


        foreach ($complemento[1] as $key => $value) {
            if ($value['TIPO'] == 'P') {
                $tipo_pago = 'PAGO';
                if ($value['INCIDENCIA'] == '1') {
                    $procede = "$" . number_format($value['NUEVO_MONTO'], 2);
                } else {
                    $procede = "$" . number_format($value['MONTO'], 2);
                }
            } else if ($value['TIPO'] == 'M') {
                $tipo_pago = 'MULTA';
                if ($value['INCIDENCIA'] == '1') {
                    $procede = "$" . number_format($value['NUEVO_MONTO'], 2);
                } else {
                    $procede = "$" . number_format($value['MONTO'], 2);
                }
            } else if ($value['TIPO'] == 'G') {
                $tipo_pago = 'GARANTIA';
                $procede = '$00.00';
            } else if ($value['TIPO'] == 'D') {
                $tipo_pago = 'MULTA';
                $procede = '$00.00';
            } else if ($value['TIPO'] == 'R') {
                $tipo_pago = 'REFINANCIAMIENTO';
                $procede = '$00.00';
            }

            $tabla .= <<<html
                   
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 80px;background-color:#E4E4E4; text-align: center;">{$value['CDGNS']}</td>
            <td style="height:auto; width: 300px;background-color:#E4E4E4; ">{$value['NOMBRE']}</td>
            <td style="height:auto; width: 60px;background-color:#E4E4E4; text-align: center;">{$value['CICLO']}</td>
            <td style="height:auto; width: 80px;background-color:#E4E4E4; text-align: center;">{$tipo_pago}</td>
            <td class="center" style="height:auto; width: 100px;background-color:#E4E4E4; text-align: center;">{$procede}</td>
            </tr>
html;
        }

        $tabla .= <<<html
      </table>
      </div>
      <hr>
      <br>
      <br>
      <br>
      
        <table class="table">
             <tr>
                 <th style="width: 370px;" class="text-right">
                 <b>Ejecutiv(a)</b>
                 <br>
                 <br>
                 <br>
                 _____________________
                 <br>
                 <b>{$complemento[0]['EJECUTIVO']}</b>
                 <br>
                 <b>Firma de conformidad</b>
                 
                 
                 </th>
                 <th style="width: 270px;" class="text-right">
                 <b>Cajer(a)</b>
                 <br>
                 <br>
                 <br>
                 _____________________
                 <br>
                 <b>$this->__nombre</b>
                 <br>
                 <b>Firma de conformidad</b>
                 
                 
                 </th>
             </tr>
        </table>
      
</div>

html;

        $fechaActual = date('Y-m-d H:i:s');


        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($tabla, 2);
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:opensans;">Este recibo de pago se genero el día ' . $fechaActual . '<br>{PAGENO}</div>');
        print_r($mpdf->Output());
        exit;
    }

    public function PagosConsulta()
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
                        }
                        return
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
                    if ($horaActual <= $hora_cierre) {
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
