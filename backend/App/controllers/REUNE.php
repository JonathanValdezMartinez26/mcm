<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Reune as ReuneDao;

class Reune extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function ConsultarQuejas()
    {
        $fecha = date('Y-m-d');
        $extraHeader = <<<html
        <title>Registrar Quejas REDECO</title>
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
                                                
            const showError = (mensaje) => swal(mensaje, { icon: "error" })
            const showAviso = (mensaje) => swal(mensaje, { icon: "warning" })
            const showSuccess = (mensaje) => swal(mensaje, { icon: "success" , showConfirmButton: true,}).then((result) => {location.reload();} )
        
            const consumeAPI = (url, callback, datos = null, tipoDatos = 'json', tipo = "get", token = null, msgError = "") => {
                $.ajax({
                    type: tipo,
                    url: url,
                    dataType: tipoDatos,
                    data: JSON.stringify(datos),
                    contentType: "application/json",
                    success: callback,
                    error: (resError) => {
                        console.log(resError.responseJSON, resError.responseText, resError.statusText, resError.status)
                        showError(msgError)
                    },
                    headers: { "Authorization": token }
                })
            }
                
            consumeAPI("https://api.condusef.gob.mx/catalogos/medio-recepcion/", (data) => {
                const medio = document.querySelector("#MedioId")
                const opciones = getOpciones(data.medio, "medioId", "medioDsc")
                insertaOpciones(medio, opciones)
            })
                
            consumeAPI("https://api.condusef.gob.mx/catalogos/niveles-atencion", (data) => {
                const atencion = document.querySelector("#NivelATId")
                const opciones = getOpciones(data.nivelesDeAtencion, "nivelDeAtencionId", "nivelDeAtencionDsc")
                insertaOpciones(atencion, opciones)
            })
                
            const limpiaCampos = (mensaje = "") => {
                if (mensaje !== "") showError(mensaje)
                document.querySelector("#EstadosId").innerHTML = ""
                document.querySelector("#EstadosId").disabled = true
                document.querySelector("#QuejasMunId").innerHTML = ""
                document.querySelector("#QuejasMunId").disabled = true
                document.querySelector("#QuejasLocId").innerHTML = ""
                document.querySelector("#QuejasLocId").disabled = true
                document.querySelector("#QuejasColId").innerHTML = ""
                document.querySelector("#QuejasColId").disabled = true
            }
                
            const soloNumeros = (e) => {
                if (e.keyCode < 48 || e.keyCode > 57) return e.preventDefault()
            }
                
            const validaLargo = (e, largo = 1) => {
                if (e.target.value.length > largo) return e.preventDefault()
            }
                
            const validaEntradaCP = (e) =>{
                if (e.keyCode === 13) {
                    validaCP()
                    return e.preventDefault()
                }
                if (e.keyCode < 48 || e.keyCode > 57) return e.preventDefault()
            }
                
            const formatoFecha = (fecha) => {
                const [anio, mes, dia] = fecha.split("-")
                return dia + "/" + mes + "/" + anio
            }
                
            const validaFechaRecepcion = () => {
                const fechaRegistro = document.querySelector("#QuejasFecRecepcion").value
                const mesRegistro = document.querySelector("#QuejasNoTrim").value
                const [anio, mes, dia] = fechaRegistro.split("-")
                    
                if (parseInt(mes) != parseInt(mesRegistro)) {
                    document.querySelector("#QuejasFecRecepcion").value = "$fecha"
                    return showAviso("El mes de registro no coincide con la fecha de recepción, favor de validar.")
                }
            }
                
            const validaCP = () => {
                    const cp = document.querySelector("#QuejasCP").value
                    if (cp.length !== 5) return limpiaCampos("El código postal debe ser de 5 dígitos.")
                    
                    const url = "https://api.condusef.gob.mx/sepomex/colonias/?cp=" + cp
                    
                    consumeAPI(url, (data) => {
                        if (data.colonias.length === 0) return limpiaCampos("Código postal no encontrado.")
                        
                        validaEstado(data.colonias)
                        validaMunicipio(data.colonias)
                        validaLocalidad(data.colonias)
                        validaColonia(data.colonias)
                    })
            }
                
            const validaEstado = (edo) => {
                const estado = document.querySelector("#EstadosId")
                const estados = getOpciones(edo, "estadoId", "estado")
                insertaOpciones(estado, estados)
            }
                
            const validaMunicipio = (mun) => {
                const municipio = document.querySelector("#QuejasMunId")
                const municipios = getOpciones(mun, "municipioId", "municipio")
                insertaOpciones(municipio, municipios)
            }
                
            const validaLocalidad = (loc) => {
                const localidad = document.querySelector("#QuejasLocId")
                const localidades = getOpciones(loc, "tipoLocalidadId", "tipoLocalidad")
                insertaOpciones(localidad, localidades)
            }
                
            const validaColonia = (col) => {
                const colonia = document.querySelector("#QuejasColId")
                const colonias = getOpciones(col, "coloniaId", "colonia")
                insertaOpciones(colonia, colonias)
            }
                
            const getOpciones = (elementos, key, value) => {
                const opciones = []
                elementos.forEach(elemento => {
                    const opcion = "<option value='" + elemento[key] + "'>" + elemento[value] + "</option>"
                    if (!opciones.includes(opcion)) opciones.push(opcion)
                })
                return opciones
            }
                
            const insertaOpciones = (elemento, opciones = []) => {
                if (opciones.length > 1) opciones.unshift("<option value='' disabled>Seleccione</option>")
                
                elemento.innerHTML = opciones.join("")
                elemento.selectedIndex = 0
                elemento.disabled = !(opciones.length > 1)
            }
            
            const consultarQueja =(e) => {
                e.preventDefault()
                const token = "token_api"
                const datos = [{
                    InstitucionClave: "Cultiva",
                    Sector: "Sociedades Financieras de Objeto Múltiple E.N.R.",
                    ConsultasTrim: Number(document.querySelector("#QuejasNoTrim").value),
                    NumConsultas: 1,
                    ConsultasFolio: document.querySelector("#QuejasFolio").value,
                    ConsultasEstatusCon: Number(document.querySelector("#QuejasEstatus").value),
                    ConsultasFecAten: formatoFecha(document.querySelector("#QuejasFecResolucion").value),
                    EstadosId: Number(document.querySelector("#EstadosId").value),
                    ConsultasFecRecepcion: formatoFecha(document.querySelector("#QuejasFecRecepcion").value),
                    MediosId: Number(document.querySelector("#MedioId").value),
                    Producto: document.querySelector("#product").value,
                    CausaId: document.querySelector("#CausasId").value,
                    ConsultasCP: Number(document.querySelector("#QuejasCP").value),
                    ConsultasMpioId: Number(document.querySelector("#QuejasMunId").value),
                    ConsultasLocId: Number(document.querySelector("#QuejasLocId").value),
                    ConsultasColId: Number(document.querySelector("#QuejasColId").value),
                    ConsultascatnivelatenId: Number(document.querySelector("#NivelATId").value),
                    ConsultasPori: document.querySelector("#QuejasPORI").value,
                }]
                
                console.log(datos)
                    
                const procesaRespuesta = (respuesta) => {
                    if (respuesta.errors.length > 0) {
                        let mensaje = "Ocurrieron los siguientes errores:\\n"
                        respuesta.errors[0].queja.errors.forEach(error => {
                            mensaje += error + ".\\n" 
                        })
                        return showError(mensaje)
                    } else {
                        return showSuccess("Queja registrada exitosamente bajo el folio: " + document.querySelector("#QuejasFolio").value)
                    }
                }
                consumeAPI("api-reune-pruebas.condusef.gob.mx/reune/consultas/general", procesaRespuesta, datos, "json", "post", token, "Ocurrió un error de comunicación con el portal de REUNE.")   
            }
        </script>
html;

        $meses = [
            "1" => "Enero",
            "2" => "Febrero",
            "3" => "Marzo",
            "4" => "Abril",
            "5" => "Mayo",
            "6" => "Junio",
            "7" => "Julio",
            "8" => "Agosto",
            "9" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre"
        ];

        $opcionesMeses = "";
        foreach ($meses as $key => $value) {
            $opcionesMeses .= "<option value='{$key}'>{$value}</option>";
        }

        $productos = ReuneDao::GetProductos();
        $opcionesProductos = "";
        foreach ($productos as $key => $value) {
            $opcionesProductos .= "<option value='{$value['CODIGO']}'>{$value['PRODUCTO']}</option>";
        }

        $causas = ReuneDao::GetCausas();
        $opcionesCausas = "";
        foreach ($causas as $key => $value) {
            $opcionesCausas .= "<option value='{$value['CODIGO']}'>{$value['DESCRIPCION']}</option>";
        }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', $fecha);
        View::set('meses', $opcionesMeses);
        View::set('productos', $opcionesProductos);
        View::set('causas', $opcionesCausas);
        View::render("z_api_consulta_quejas");
    }
}
