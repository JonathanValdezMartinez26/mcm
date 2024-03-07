<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos as PagosDao;
use \App\models\Operaciones as OperacionesDao;

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

    public function CreateUser()
    {
        $extraHeader = <<<html
            <title>Crear Usuario REDECO</title>
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
            
                function enviar_add_user(){	
                    alert("Hola");
                    
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
            </script>
        html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("z_api_create_user");
    }

    public function QueriesComplaints()
    {
        $extraHeader = <<<html
            <title>Consultar Quejas REDECO</title>
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
            
                function enviar_add_user(){	
                    alert("Hola");
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
            </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("z_api_quejas_consulta");
    }


    public function ComplaintsAdd()
    {
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
                
                function enviar_add_user(){	
                    alert("Hola");
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
                ///////////////////////////////
                //MEDIO DE RECEPCION
                ///////////////////////////////
                $.ajax({
                type: 'GET',
                url: 'https://api.condusef.gob.mx/catalogos/medio-recepcion/',
                dataType: 'json',
                success: function(data_json) {
                    $.each(data_json.medio, function(key, value) {
                        let id = data_json.medio[key].medioId;
                        let desc = data_json.medio[key].medioDsc;
                        $("#medio_recepcion").append('<option name="' + id + '">' + desc + '</option>');
                    });
                }
                });
            
                ///////////////////////////////
                //NIVELES DE ATENCION
                ///////////////////////////////
                $.ajax({
                type: 'GET',
                url: 'https://api.condusef.gob.mx/catalogos/niveles-atencion',
                dataType: 'json',
                success: function(data_json) {
                    $.each(data_json.nivelesDeAtencion, function(key, value) {
                        let id = data_json.nivelesDeAtencion[key].nivelDeAtencionId;
                        let desc = data_json.nivelesDeAtencion[key].nivelDeAtencionDsc;
                        $("#nivel_atencion").append('<option name="' + id + '">' + desc + '</option>');
                    });
                
<<<<<<< HEAD
                  let id = data_json.medio[key].medioId;
                  let desc = data_json.medio[key].medioDsc;
                  
				$("#medio_recepcion").append('<option name="' + id + '">' + desc + '</option>');
			});
           
          }
        });
    
    ///////////////////////////////
    //NIVELES DE ATENCION
    ///////////////////////////////
        $.ajax({
          type: 'GET',
          url: 'https://api.condusef.gob.mx/catalogos/niveles-atencion',
          dataType: 'json',
          success: function(data_json) {
			$.each(data_json.nivelesDeAtencion, function(key, value) {
                
                  let id = data_json.nivelesDeAtencion[key].nivelDeAtencionId;
                  let desc = data_json.nivelesDeAtencion[key].nivelDeAtencionDsc;
                  
				$("#nivel_atencion").append('<option name="' + id + '">' + desc + '</option>');
			});
           
          }
        });
        
    ///////////////////////////////
    //ESTADOS
    ///////////////////////////////
        $.ajax({
          type: 'GET',
          url: 'https://api.condusef.gob.mx/sepomex/estados/',
          dataType: 'json',
          success: function(data_json) {
			$.each(data_json.estados, function(key, value) {
                
                  let id = data_json.estados[key].claveEdo;
                  let desc = data_json.estados[key].estado;
                  
				$("#estado").append('<option name="' + id + '">' + desc + '</option>');
			});
           
          }
        });
        

                ///////////////////////////////
                //NIVELES DE ATENCION
                ///////////////////////////////
                $.ajax({
                type: 'GET',
                url: 'https://api.condusef.gob.mx/sepomex/estados/',
                dataType: 'json',
                success: function(data_json) {
                    $.each(data_json.estados, function(key, value) {
                        let id = data_json.estados[key].claveEdo;
                        let desc = data_json.estados[key].estado;
                        $("#nivel_atencion").append('<option name="' + id + '">' + desc + '</option>');
                    });
                }
                })
                 
                const showError = (mensaje) => swal(mensaje, { icon: "error" })
            
                const consumeAPI = (url, callback, tipoDatos = 'json', tipo = "get") => {
                    $.ajax({
                        type: tipo,
                        url: url,
                        dataType: tipoDatos,
                        success: callback
                    })
                }
                 
                const limpiaCampos = (mensaje = "") => {
                    if (mensaje !== "") showError(mensaje)
                    document.querySelector("#estado").innerHTML = ""
                    document.querySelector("#estado").disabled = true
                    document.querySelector("#municipio").innerHTML = ""
                    document.querySelector("#municipio").disabled = true
                    document.querySelector("#localidad").innerHTML = ""
                    document.querySelector("#localidad").disabled = true
                    document.querySelector("#colonia").innerHTML = ""
                    document.querySelector("#colonia").disabled = true
                }
                 
                const validaCP = () => {
                        const cp = document.querySelector("#cp").value
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
                    const estado = document.querySelector("#estado")
                    const estados = getOpciones(edo, "claveEdo", "estado")
                    insertaOpciones(estado, estados)
                }
                 
                const validaMunicipio = (mun) => {
                    const municipio = document.querySelector("#municipio")
                    const municipios = getOpciones(mun, "municipioId", "municipio")
                    insertaOpciones(municipio, municipios)
                }
                 
                const validaLocalidad = (loc) => {
                    const localidad = document.querySelector("#localidad")
                    const localidades = getOpciones(loc, "tipoLocalidadId", "tipoLocalidad")
                    insertaOpciones(localidad, localidades)
                }
                 
                const validaColonia = (col) => {
                    const colonia = document.querySelector("#colonia")
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
            </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("z_api_agregar_quejas");
    }
}
