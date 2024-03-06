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
        $extraHeader = <<<html
        <title>Gestion de Telaraña</title>
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

            const consumeWS = (url, datos, callback, tipo = "post") => {
                $.ajax({
                    type: tipo,
                    url: url,
                    data: datos,
                    success: callback
                })
            }

            const getCodigoCliente = (cliente) => cliente.split(" - ")[0]
            const getNombreCliente = (cliente) => cliente.split(" - ")[1]

            const vincularInvitado = () => {
                const cliente = document.querySelector("#MuestraCliente")
                const invitado = document.querySelector("#MuestraInvitado")
                const fecha = document.querySelector("#Fecha")

                if (cliente.value === "") {
                    showError("El campo cliente no puede estar vacío")
                    return false
                }

                if (invitado.value === "") {
                    showError("El campo invitado no puede estar vacío")
                    return false
                }

                if (cliente.value === invitado.value) {
                    showError("El cliente no puede ser el mismo que el invitado")
                    return false
                }

                if (isNaN(new Date(fecha.value).getTime())) {
                    showError("El campo fecha no puede estar vacío")
                    return false
                }

                const datos = {
                    invita: getCodigoCliente(cliente.value),
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
                    fecha.value = ""
                }

                consumeWS("/Validaciones/VinculaInvitado", datos, validaRespuesta)
            }

            const buscaCliente = (id) => {
                const noCliente = document.querySelector("#" + id)
                const nombreCliente = document.querySelector("#Muestra" + id)

                const limpiaCampos = (msg) => {
                    showError(msg)
                    noCliente.value = ""
                    nombreCliente.value = ""
                    return false
                }

                if (noCliente.value === "") limpiaCampos("El campo no puede estar vacío")
                if (isNaN(noCliente.value)) limpiaCampos("El valor ingresado debe ser un númerico")
                if (noCliente.value.length != 6) limpiaCampos("El valor ingresado debe ser de 6 dígitos")

                const validaRespuesta = (respuesta) => {
                    const res = JSON.parse(respuesta)

                    if (!res.success) limpiaCampos(res.mensaje)

                    nombreCliente.value = res.datos.nombre
                    document.querySelector("#" + id).value = ""
                }

                const datos = { codigo: noCliente.value }

                if (id === "Invitado") {
                    const anfitrion = {
                        codigo: getCodigoCliente(document.querySelector("#MuestraCliente").value),
                        nombre: getNombreCliente(document.querySelector("#MuestraCliente").value)
                    }

                    if (anfitrion.codigo === noCliente.value)
                        limpiaCampos("El cliente no puede ser el mismo que el invitado")

                    if (anfitrion.codigo != "") datos.anfitrion = anfitrion
                }

                consumeWS("/Validaciones/BuscaCliente", datos, validaRespuesta)
            }
        </script>
html;


        $catalogo = ValidacionesDao::ConsultaClienteInvitado();

        $tabla_clientes = "";
        foreach ($catalogo as $key => $fila) {
            $tabla_clientes .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                $tabla_clientes .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla_clientes .= "</tr>";
        }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla_clientes);
        View::set('fecha', date('Y-m-d'));
        View::render("registro_telarana");
    }

    public function VinculaInvitado()
    {
        $respuesta = ValidacionesDao::AddVinculaInvitado($_POST);
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
