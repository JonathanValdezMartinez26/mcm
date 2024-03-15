<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\Validaciones as ValidacionesDao;

class Validaciones extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;



        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function RegistroTelarana()
    {
        $fecha = date('Y-m-d');
        $extraHeader = <<<html
            <title>Gestión de Telaraña</title>
            <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
            function getParameterByName(name) {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)")
                results = regex.exec(location.search)
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
            }
            
            const showError = (mensaje) => swal(mensaje, { icon: "error" })
            const showSuccess = (mensaje) => swal(mensaje, { icon: "success" })
            const getCodigoCliente = (cliente) => cliente.split(" - ")[0]
            const getNombreCliente = (cliente) => cliente.split(" - ")[1]
            
            const consumeWS = (url, datos, callback, tipo = "post") => {
                $.ajax({
                    type: tipo,
                    url: url,
                    data: datos,
                    success: callback
                })
            }
            
            const validarYbuscar = (e) => {
                if (e.keyCode === 13) {
                    if (e.target.id === "Cliente") buscaAnfitrion()
                    if (e.target.id === "Invitado") buscaInvitado()
                }
                if (e.target.value.length > 5) e.preventDefault()
                if (e.keyCode < 48 || e.keyCode > 57) e.preventDefault()
            }
            
            const limpiaCampos = (numero, nombre, msg = null) => {
                if (msg) showError(msg)
                if (numero) {
                    numero.value = ""
                    numero.required = true
                }
                if (nombre) nombre.value = ""
                document.querySelector("#btnVincular").disabled = true
                return false
            }
            
            const buscaAnfitrion = () => {
                const xCredito = document.querySelector("#anfiXcred").checked
                const noAnfitrion = document.querySelector("#Cliente")
                const nombreAnfitrion = document.querySelector("#MuestraCliente")
                const noInvitado = document.querySelector("#Invitado")
                const nombreInvitado = document.querySelector("#MuestraInvitado")
                
                if (nombreAnfitrion.value !== "") limpiaCampos(null, nombreInvitado)
                if (noAnfitrion.value === "") return limpiaCampos(noAnfitrion, nombreAnfitrion, "Se debe ingresar un numero de Cliente para buscar anfitrión.")
                if (isNaN(noAnfitrion.value)) return limpiaCampos(noAnfitrion, nombreAnfitrion, "El valor ingresado debe ser un numérico.")
                if (noAnfitrion.value.length != 6) return limpiaCampos(noAnfitrion, nombreAnfitrion, "El valor ingresado debe ser de 6 dígitos.")
                
                const procesaRespuesta = (respuesta) => {
                    const res = JSON.parse(respuesta)
                    if (!res.success) {
                        limpiaCampos(noInvitado, nombreInvitado)
                        document.querySelector("#Invitado").disabled = true
                        document.querySelector("#BuscarInvitado").disabled = true
                        return limpiaCampos(noAnfitrion, nombreAnfitrion, res.mensaje)
                    }
            
                    nombreAnfitrion.value = res.datos.nombre
                    noAnfitrion.required = false
                    noAnfitrion.value = ""
                    document.querySelector("#Invitado").disabled = false
                    document.querySelector("#BuscarInvitado").disabled = false
                }
            
                const datos = { codigo: noAnfitrion.value, xCredito }
                consumeWS("/Validaciones/BuscaCliente", datos, procesaRespuesta)
                
                return false
            }
            
            const buscaInvitado = () => {
                const xCredito = document.querySelector("#invXcred").checked
                const noInvitado = document.querySelector("#Invitado")
                const nombreInvitado = document.querySelector("#MuestraInvitado")
                const anfitrion = document.querySelector("#MuestraCliente")
                const noAnfitrion = getCodigoCliente(anfitrion.value)
                
                if (noInvitado.value === "") return limpiaCampos(noInvitado, nombreInvitado, "Se debe ingresar un código para buscar.")
                if (isNaN(noInvitado.value)) return limpiaCampos(noInvitado, nombreInvitado, "El valor ingresado debe ser numérico.")
                if (noInvitado.value.length != 6) return limpiaCampos(noInvitado, nombreInvitado, "El valor ingresado debe ser de 6 dígitos.")
                
                const procesaRespuesta = (respuesta) => {
                    const res = JSON.parse(respuesta)
                    if (!res.success) return limpiaCampos(noInvitado, nombreInvitado, res.mensaje)
                    if (getCodigoCliente(res.datos.nombre) === noAnfitrion) return limpiaCampos(noInvitado, nombreInvitado, "El cliente invitado no puede ser el mismo que el cliente anfitrión.")
                    
                    nombreInvitado.value = res.datos.nombre
                    noInvitado.value = ""
                    noInvitado.required = false
                    document.querySelector("#btnVincular").disabled = false
                }
            
                const datos = {
                    anfitrion: noAnfitrion,
                    codigo: noInvitado.value,
                    xCredito
                }
                consumeWS("/Validaciones/BuscaCliente", datos, procesaRespuesta)
            
                return false
            }
            
            const vincularInvitado = (e) => {
                e.preventDefault()
                const cliente = document.querySelector("#MuestraCliente")
                const invitado = document.querySelector("#MuestraInvitado")
                const fecha = document.querySelector("#Fecha")
                
                if (isNaN(new Date(fecha.value).getTime())) {
                    showError("El campo fecha no puede estar vacío")
                    return false
                }
                
                const datos = {
                    anfitrion: getCodigoCliente(cliente.value),
                    invitado: getCodigoCliente(invitado.value),
                    fecha: fecha.value
                }
                
                const validaRespuesta = (respuesta) => {
                    const res = JSON.parse(respuesta)
                    if (!res.success) {
                        showError(res.mensaje)
                        return false
                    }
            
                    showSuccess(res.mensaje)
                    cliente.value = ""
                    invitado.value = ""
                    fecha.value = "$fecha"
                }
            
                consumeWS("/Validaciones/VinculaInvitado", datos, validaRespuesta)
                return false
            }
        </script>
html;

        $catalogo = ValidacionesDao::ConsultaClienteInvitado();

        $tabla_clientes = "";
        foreach ($catalogo as $key => $fila) {
            $tabla_clientes .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                if ($key == "FECHA_REGISTRO") $columna = date('d-m-Y H:i:s', strtotime($columna));
                $tabla_clientes .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla_clientes .= '<td style="padding: 0px !important;" class="center">
                <button type="button" class="btn btn-success btn-circle" onclick="Desactivado()" style="background: #E5E5E5"><i class="fa fa-edit"></i></button>
                </td>';
            $tabla_clientes .= "</tr>";
        }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla_clientes);
        View::set('fecha', $fecha);
        View::set('fechaMin', date('Y-m-d', strtotime("-30 day")));
        View::set('fechaMax', date('Y-m-d', strtotime("+7 day")));
        View::render("registro_telarana");
    }

    public function VinculaInvitado()
    {
        $respuesta = ValidacionesDao::VinculaInvitado($_POST);
        echo $respuesta;
        return $respuesta;
    }

    public function BuscaCliente()
    {
        $respuesta = ValidacionesDao::BuscaCliente($_POST);
        echo $respuesta;
        return $respuesta;
    }
}
