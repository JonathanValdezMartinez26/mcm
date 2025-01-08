<?php

namespace Core;

defined("APPPATH") or die("Access denied");

class Controller
{
    public $socket = '<script src="/libs/socket.io.min.js"></script>';
    public $swal2 = '<script src="/libs/sweetalert2/sweetalert2.all.min.js"></script><link href="/libs/sweetalert2/sweetalert2-tema-bootstrap-4.css" rel="stylesheet" />';
    public $showError = 'const showError = (mensaje) => swal({ text: mensaje, icon: "error" })';
    public $showSuccess = 'const showSuccess = (mensaje) => swal({ text: mensaje, icon: "success" })';
    public $showInfo = 'const showInfo = (mensaje) => swal({ text: mensaje, icon: "info" })';
    public $showWarning = 'const showWarning = (mensaje) => swal({ text: mensaje, icon: "warning" })';
    public $showWait = 'const showWait = (mensaje) => swal({ text: mensaje, icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })';
    public $confirmarMovimiento = <<<JAVASCRIPT
        const confirmarMovimiento = async (titulo, mensaje, html = null) => {
            return await swal({ title: titulo, content: html, text: mensaje, icon: "warning", buttons: ["No", "Si, continuar"], dangerMode: true })
        }
    JAVASCRIPT;
    public $conectaSocket = <<<JAVASCRIPT
        const conectaSocket = (url, modulo, datos = {}) => {
            showWait("Conectando con el servidor...")
            return io(url, {
                query: {
                    servidor: window.location.origin,
                    sesionPHP: "sessionID",
                    modulo: modulo,
                    configuracion: JSON.stringify(datos)
                }
            })
        }
    JAVASCRIPT;
    public $consultaServidor = <<<JAVASCRIPT
        const consultaServidor = (url, datos, fncOK, metodo = "POST", tipo = "JSON", tipoContenido = null) => {
            swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
            const configuracion = {
                type: metodo,
                url: url,
                data: datos,
                success: (res) => {
                    if (tipo === "JSON") {
                        try {
                            res = JSON.parse(res)
                        } catch (error) {
                            console.error(error)
                            res =  {
                                success: false,
                                mensaje: "Ocurrió un error al procesar la respuesta del servidor."
                            }
                        }
                    }
                    if (tipo === "blob") res = new Blob([res], { type: "application/pdf" })

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
    JAVASCRIPT;
    public $parseaNumero = 'const parseaNumero = (numero) => parseFloat(numero.replace(/[^0-9.-]/g, "")) || 0';
    public $formatoMoneda = 'const formatoMoneda = (numero) => parseFloat(numero).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 })';
    public $configuraTabla = <<<JAVASCRIPT
        const configuraTabla = (id, {noRegXvista = true} = {}) => {
            const configuracion = {
                lengthMenu: [
                    [10, 40, -1],
                    [10, 40, "Todos"]
                ],
                order: [],
                language: {
                    emptyTable: "No hay datos disponibles",
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente",
                    },
                    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Sin registros para mostrar",
                    zeroRecords: "No se encontraron registros",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    search: "Buscar:",
                }
            }

            configuracion.lengthChange = noRegXvista

            $("#" + id).DataTable(configuracion)

            $("#"  + id + " input[type=search]").keyup(() => {
                $("#example")
                    .DataTable()
                    .search(jQuery.fn.DataTable.ext.type.search.html(this.value))
                    .draw()
            })
        }
    JAVASCRIPT;
    public $crearFilas = <<<JAVASCRIPT
        const creaFilas = (datos) => {
            const filas = document.createDocumentFragment()
            datos.forEach((dato) => {
                const fila = document.createElement("tr")
                Object.keys(dato).forEach((key) => {
                    const celda = document.createElement("td")
                    celda.style.verticalAlign = "middle"
                    celda.innerText = dato[key]
                    fila.appendChild(celda)
                })
                filas.appendChild(fila)
            })
            return filas
        }
    JAVASCRIPT;
    public $validaFIF = <<<JAVASCRIPT
        const validaFIF = (idI, idF) => {
            const fechaI = document.getElementById(idI).value
            const fechaF = document.getElementById(idF).value
            if (fechaI && fechaF && fechaI > fechaF) {
                document.getElementById(idI).value = fechaF
            }
        }
    JAVASCRIPT;
    public $descargaExcel = <<<JAVASCRIPT
        const descargaExcel = (url, parametros = {}) => {
            const formDescarga = document.createElement("form")
            formDescarga.action = url
            formDescarga.method = "POST"
            formDescarga.target = "_blank"
            formDescarga.style.display = "none"

            Object.entries(parametros).forEach(([clave, valor]) => {
                const input = document.createElement("input")
                input.name = clave
                input.value = valor
                formDescarga.appendChild(input)
            })

            document.body.appendChild(formDescarga)
            formDescarga.submit()

            document.body.removeChild(formDescarga)

            showInfo("Generando el archivo, espere un momento...")
        }
    JAVASCRIPT;

    public $__usuario = '';
    public $__nombre = '';
    public $__puesto = '';
    public $__cdgco = '';
    public $__cdgco_ahorro = '';
    public $__perfil = '';
    public $__ahorro = '';
    public $__hora_inicio_ahorro = '';
    public $__hora_fin_ahorro = '';

    public function __construct()
    {
        session_start();
        $this->conectaSocket = str_replace('sessionID', session_id(), $this->conectaSocket);
        if ($_SESSION['usuario'] == '' || empty($_SESSION['usuario'])) {
            unset($_SESSION);
            session_unset();
            session_destroy();
            header("Location: /Login/");
            exit();
        } else {
            $this->__usuario = $_SESSION['usuario'];
            $this->__nombre = $_SESSION['nombre'];
            $this->__puesto = $_SESSION['puesto'];
            $this->__cdgco = $_SESSION['cdgco'];
            $this->__perfil = $_SESSION['perfil'];
            $this->__ahorro = $_SESSION['ahorro'];
            $this->__cdgco_ahorro = $_SESSION['cdgco_ahorro'];
            $this->__hora_inicio_ahorro = $_SESSION['inicio'];
            $this->__hora_fin_ahorro = $_SESSION['fin'];
        }
    }

    public function GetExtraHeader($titulo, $elementos = [])
    {
        $html = <<<HTML
        <title>$titulo</title>
        HTML;

        if (!empty($elementos)) {
            foreach ($elementos as $elemento) {
                $html .= "\n" . $elemento;
            }
        }

        return $html;
    }
}
