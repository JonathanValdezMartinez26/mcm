<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\ApiCondusef as ApiCondusefDao;

class ApiCondusef extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function AddRedeco()
    {
        $fecha = date('Y-m-d');

        $extraHeader = <<<html
        <title>Registrar Quejas REDECO</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
            <script>                                                  
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
                            console.log(resError.responseJSON)
                            showError(msgError)
                        },
                        headers: { "Authorization": token }
                    })
                }
                 
                consumeAPI("https://api.condusef.gob.mx/catalogos/medio-recepcion/", (data) => {
                    const medio = document.querySelector("#QuejasMedio")
                    const opciones = getOpciones(data.medio, "medioId", "medioDsc")
                    insertaOpciones(medio, opciones)
                })
                 
                consumeAPI("https://api.condusef.gob.mx/catalogos/niveles-atencion", (data) => {
                    const atencion = document.querySelector("#QuejasNivelAT")
                    const opciones = getOpciones(data.nivelesDeAtencion, "nivelDeAtencionId", "nivelDeAtencionDsc")
                    insertaOpciones(atencion, opciones)
                })
                 
                const limpiaCampos = (mensaje = "") => {
                    if (mensaje !== "") showError(mensaje)
                    document.querySelector("#QuejasEstados").innerHTML = ""
                    document.querySelector("#QuejasEstados").disabled = true
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

                const validaEdad = (e) => {
                    if (e.target.value < 18) {
                        return showAviso("La edad mínima para registrar una queja es de 18 años.")
                        .then(() => e.target.focus())
                    }
                }
                 
                const formatoFecha = (fecha) => {
                    const [anio, mes, dia] = fecha.split("-")
                    return dia + "/" + mes + "/" + anio
                }
                 
                const validaFechaRecepcion = () => {
                    const fechaRegistro = document.querySelector("#QuejasFecRecepcion").value
                    const mesRegistro = document.querySelector("#QuejasNoMes").value
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
                    const estado = document.querySelector("#QuejasEstados")
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

                const validaRequeridos = () => {
                    const requeridos = [
                        "#QuejasNoMes",
                        "#QuejasNum",
                        "#QuejasFolio",
                        "#QuejasFecRecepcion",
                        "#QuejasMedio",
                        "#QuejasNivelAT",
                        "#QuejasProducto",
                        "#QuejasCausa",
                        "#QuejasPORI",
                        "#QuejasEstatus",
                        "#QuejasCP",
                        "#QuejasEstados",
                        "#QuejasMunId",
                        "#QuejasLocId",
                        "#QuejasColId",
                        "#QuejasTipoPersona",
                        "#QuejasSexo",
                        "#QuejasEdad",
                        "#QuejasFecNotificacion",
                        "#QuejasNumPenal",
                        "#QuejasPenalizacion"
                    ]
                
                    const elementos = document.querySelectorAll(requeridos.join(","))
                    let validacion = false
                
                    elementos.forEach((elemento) => {
                        if (elemento.value === "" || elemento.value === "Seleccione") {
                            validacion = true
                        }

                        if (elemento.id === "QuejasEdad" && Number(elemento.value) < 18) {
                            validacion = false
                        }
                    })
                
                    document.querySelector("#btnAgregar").disabled = validacion
                }
                
                const registrarQueja =(e) => {
                    e.preventDefault()
                    const token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiJkYmE0MWMyZi1kNzBkLTQ4NmUtYjA0Yi0zZWYxMDc3YTNmNDciLCJ1c2VybmFtZSI6InN5c3RlbU1DTSIsImluc3RpdHVjaW9uaWQiOiJGOUNGMjUzMy03RjRDLTQ3RkYtOTIyNi04MEE4QjA3OCIsImluc3RpdHVjaW9uQ2xhdmUiOjE1NDk0LCJkZW5vbWluYWNpb25fc29jaWFsIjoiRmluYW5jaWVyYSBDdWx0aXZhLCBTLkEuUC5JLiBkZSBDLlYuLCBTT0ZPTSwgRS5OLlIuIiwic2VjdG9yaWQiOjI0LCJzZWN0b3IiOiJTT0NJRURBREVTIEZJTkFOQ0lFUkFTIERFIE9CSkVUTyBNVUxUSVBMRSBFTlIiLCJzeXN0ZW0iOiJSRURFQ08iLCJpYXQiOjE3MTgxNDM2OTcsImV4cCI6MTcyMDczNTY5N30._5nqkX_PuvsWNF6RhNKSi885EEKOi7lSPC4FwcBOObk"
         
                    const datos = [{
                        "QuejasDenominacion": document.querySelector("#QuejasDenominacion").value,
                        "QuejasSector": document.querySelector("#QuejasSector").value,
                        "QuejasNoMes": Number(document.querySelector("#QuejasNoMes").value),
                        "QuejasNum": Number(document.querySelector("#QuejasNum").value),
                        "QuejasFolio": document.querySelector("#QuejasFolio").value,
                        "QuejasFecRecepcion": formatoFecha(document.querySelector("#QuejasFecRecepcion").value),
                        "QuejasMedio": Number(document.querySelector("#QuejasMedio").value),
                        "QuejasNivelAT": Number(document.querySelector("#QuejasNivelAT").value),
                        "QuejasProducto": document.querySelector("#QuejasProducto").value,
                        "QuejasCausa": document.querySelector("#QuejasCausa").value,
                        "QuejasPORI": document.querySelector("#QuejasPORI").value,
                        "QuejasEstatus": Number(document.querySelector("#QuejasEstatus").value),
                        "QuejasEstados": Number(document.querySelector("#QuejasEstados").value),
                        "QuejasMunId": Number(document.querySelector("#QuejasMunId").value),
                        "QuejasLocId": Number(document.querySelector("#QuejasLocId").value),
                        "QuejasColId": Number(document.querySelector("#QuejasColId").value),
                        "QuejasCP": Number(document.querySelector("#QuejasCP").value),
                        "QuejasTipoPersona": Number(document.querySelector("#QuejasTipoPersona").value),
                        "QuejasSexo": document.querySelector("#QuejasSexo").value,
                        "QuejasEdad": Number(document.querySelector("#QuejasEdad").value),
                        "QuejasFecResolucion": null,
                        "QuejasFecNotificacion": formatoFecha(document.querySelector("#QuejasFecNotificacion").value),
                        "QuejasRespuesta": null,
                        "QuejasNumPenal": Number(document.querySelector("#QuejasNumPenal").value),
                        "QuejasPenalizacion": Number(document.querySelector("#QuejasPenalizacion").value),
                    }]
                     
                    const procesaRespuesta = (respuesta) => {
                        if (respuesta["Quejas enviadas"] === 0) 
                            return showError(respuesta.message)
                        
                        return showSuccess("Queja registrada exitosamente con el folio: " + document.querySelector("#QuejasFolio").value)
                    }
                            
                    consumeAPI("https://api.condusef.gob.mx/redeco/quejas", procesaRespuesta, datos, "json", "post", token, "Ocurrió un error de comunicación con el portal de REDECO.")
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
            if ($key == date('m')) $opcionesMeses .= "<option value='{$key}' selected>{$value}</option>";
            else $opcionesMeses .= "<option value='{$key}'>{$value}</option>";
        }

        $productos = ApiCondusefDao::GetProductos();
        $opcionesProductos = "";
        foreach ($productos as $key => $value) {
            $opcionesProductos .= "<option value='{$value['CODIGO']}'>{$value['PRODUCTO']}</option>";
        }

        $causas = ApiCondusefDao::GetCausas();
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
        View::render("z_api_agregar_quejas_REDECO");
    }


    public function AddReune()
    {
        $fecha = date('Y-m-d');
        $extraHeader = <<<html
        <title>Registrar Quejas REUNE</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>                                                
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
                        console.log(resError.responseJSON)
                        showError(msgError)
                    },
                    headers: { "Authorization": token }
                })
            }
                
            consumeAPI("https://api.condusef.gob.mx/catalogos/medio-recepcion/", (data) => {
                const medio = document.querySelector("#MediosId")
                const opciones = getOpciones(data.medio, "medioId", "medioDsc")
                insertaOpciones(medio, opciones)
            })
                
            // consumeAPI("https://api.condusef.gob.mx/catalogos/niveles-atencion", (data) => {
            //     const atencion = document.querySelector("#ConsultascatnivelatenId")
            //     const opciones = getOpciones(data.nivelesDeAtencion, "nivelDeAtencionId", "nivelDeAtencionDsc")
            //     insertaOpciones(atencion, opciones)
            // })
                
            const limpiaCampos = (mensaje = "") => {
                if (mensaje !== "") showError(mensaje)
                document.querySelector("#EstadosId").innerHTML = ""
                document.querySelector("#EstadosId").disabled = true
                document.querySelector("#ConsultasMpioId").innerHTML = ""
                document.querySelector("#ConsultasMpioId").disabled = true
                document.querySelector("#ConsultasLocId").innerHTML = ""
                document.querySelector("#ConsultasLocId").disabled = true
                document.querySelector("#ConsultasColId").innerHTML = ""
                document.querySelector("#ConsultasColId").disabled = true
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
                const fechaRegistro = document.querySelector("#ConsultasFecRecepcion").value
                const mesRegistro = document.querySelector("#ConsultasTrim").value
                const [anio, mes, dia] = fechaRegistro.split("-")
                    
                if (parseInt(mes) != parseInt(mesRegistro)) {
                    document.querySelector("#ConsultasFecRecepcion").value = "$fecha"
                    return showAviso("El mes de registro no coincide con la fecha de recepción, favor de validar.")
                }
            }
                
            const validaCP = () => {
                const cp = document.querySelector("#ConsultasCP").value
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
                const municipio = document.querySelector("#ConsultasMpioId")
                const municipios = getOpciones(mun, "municipioId", "municipio")
                insertaOpciones(municipio, municipios)
            }
                
            const validaLocalidad = (loc) => {
                const localidad = document.querySelector("#ConsultasLocId")
                const localidades = getOpciones(loc, "tipoLocalidadId", "tipoLocalidad")
                insertaOpciones(localidad, localidades)
            }
                
            const validaColonia = (col) => {
                const colonia = document.querySelector("#ConsultasColId")
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
         
            const validaRequeridos = () => {
                const requeridos = [
                    "#ConsultasTrim",
                    "#ConsultasFolio",
                    // "#ConsultasEstatusCon",
                    "#ConsultasFecRecepcion",
                    "#EstadosId",
                    // "#ConsultasFecAten",
                    "#MediosId",
                    "#Producto",
                    "#CausaId",
                    "#ConsultasCP",
                    "#ConsultasMpioId",
                    "#ConsultasLocId",
                    "#ConsultasColId",
                    // "#ConsultascatnivelatenId",
                    "#ConsultasPori",
                ]
            
                const elementos = document.querySelectorAll(requeridos.join(","))
                let validacion = false
            
                elementos.forEach((elemento) => {
                    if (elemento.value === "" || elemento.value === "Seleccione") {
                        validacion = true
                    }
                })
            
                document.querySelector("#btnAgregar").disabled = validacion
            }
            
            const registrarQueja =(e) => {
                e.preventDefault()
                const token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiJkYmE0MWMyZi1kNzBkLTQ4NmUtYjA0Yi0zZWYxMDc3YTNmNDciLCJ1c2VybmFtZSI6InN5c3RlbU1DTSIsImluc3RpdHVjaW9uaWQiOiJGOUNGMjUzMy03RjRDLTQ3RkYtOTIyNi04MEE4QjA3OCIsImluc3RpdHVjaW9uQ2xhdmUiOjE1NDk0LCJkZW5vbWluYWNpb25fc29jaWFsIjoiRmluYW5jaWVyYSBDdWx0aXZhLCBTLkEuUC5JLiBkZSBDLlYuLCBTT0ZPTSwgRS5OLlIuIiwic2VjdG9yaWQiOjI0LCJzZWN0b3IiOiJTT0NJRURBREVTIEZJTkFOQ0lFUkFTIERFIE9CSkVUTyBNVUxUSVBMRSBFTlIiLCJzeXN0ZW0iOiJSRVVORSIsImlhdCI6MTcxODE2ODY3OSwiZXhwIjoxNzIwNzYwNjc5fQ._y9D9eSHh-e3cqGfCr1Az6mDtS0e0Uh1K2fDvAH9QI0"
                const datos = [{
                    InstitucionClave: document.querySelector("#InstitucionClave").value,
                    Sector: document.querySelector("#Sector").value,
                    ConsultasTrim: Number(document.querySelector("#ConsultasTrim").value),
                    NumConsultas: Number(document.querySelector("#NumConsultas").value),
                    ConsultasFolio: document.querySelector("#ConsultasFolio").value,
                    ConsultasEstatusCon: Number(document.querySelector("#ConsultasEstatusCon").value),
                    ConsultasFecAten: null,
                    EstadosId: Number(document.querySelector("#EstadosId").value),
                    ConsultasFecRecepcion: formatoFecha(document.querySelector("#ConsultasFecRecepcion").value),
                    MediosId: Number(document.querySelector("#MediosId").value),
                    Producto: document.querySelector("#Producto").value,
                    CausaId: document.querySelector("#CausaId").value,
                    ConsultasCP: Number(document.querySelector("#ConsultasCP").value),
                    ConsultasMpioId: Number(document.querySelector("#ConsultasMpioId").value),
                    ConsultasLocId: Number(document.querySelector("#ConsultasLocId").value),
                    ConsultasColId: Number(document.querySelector("#ConsultasColId").value),
                    ConsultascatnivelatenId: null,
                    ConsultasPori: document.querySelector("#ConsultasPori").value,
                }]
                    
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
                consumeAPI("https://api-reune-pruebas.condusef.gob.mx/reune/consultas/general", procesaRespuesta, datos, "json", "post", token, "Ocurrió un error de comunicación con el portal de REUNE.")   
            }
        </script>
        html;

        $trimestres = [
            "1" => "Enero - Marzo",
            "2" => "Abril - Junio",
            "3" => "Julio - Septiembre",
            "4" => "Octubre - Diciembre"
        ];

        $opcionesMeses = "";
        foreach ($trimestres as $key => $value) {
            if (intval($key) == ceil(date('m') / 3)) $opcionesMeses .= "<option value='{$key}' selected>{$value}</option>";
            else $opcionesMeses .= "<option value='{$key}'>{$value}</option>";
        }

        $productos = ApiCondusefDao::GetProductos();
        $opcionesProductos = "";
        foreach ($productos as $key => $value) {
            $opcionesProductos .= "<option value='{$value['CODIGO']}'>{$value['PRODUCTO']}</option>";
        }

        $causas = ApiCondusefDao::GetCausas();
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
        View::render("z_api_agregar_quejas_REUNE");
    }
}
