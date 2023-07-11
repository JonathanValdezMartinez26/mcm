<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Creditos AS CreditosDao;

class Devengo extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());

    }

    public function getUsuario(){
      return $this->__usuario;
    }


    public function index()
    {
        $extraHeader = <<<html
        <title>Devengar Crédito</title>
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
            
            $("#export_excel").click(function(){
               
              Credito = getParameterByName('Credito');
              
              $('#all').attr('action', '/Creditos/generarExcel/?Credito='+Credito);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
            
        });
        
         
        
        function enviar_add(){
             articulo = document.getElementById("articulo").value;
             marca = document.getElementById("marca").value;
             modelo = document.getElementById("modelo").value;
             serie = document.getElementById("serie").value;
             valor = document.getElementById("valor").value;
             factura = document.getElementById("factura").value;
             
             if(articulo != '')
                 {
                      if(marca != '')
                         {
                              if(modelo != '')
                                 {
                                      if(serie != '')
                                         {
                                              if(valor != '')
                                                 {
                                                      if(factura != '')
                                                         {
                                                             $.ajax({
                                                                type: 'POST',
                                                                url: '/Creditos/InsertGarantia/',
                                                                data: $('#Add').serialize(),
                                                                success: function(respuesta) {
                                                                    if(respuesta != '0'){
                                                                    swal("Registro guardado exitosamente", {
                                                                      icon: "success",
                                                                    });
                                                                    location.reload();
                                                                    }
                                                                    else {
                                                                         swal(respuesta, {
                                                                          icon: "error",
                                                                        });
                                                                    }
                                                                }
                                                                });
                                                         }
                                                      else
                                                          {
                                                               swal("Atención", "Ingrese la serie de la factura", "warning");
                                                          }
                                                 }
                                              else
                                                  {
                                                       swal("Atención", "Ingrese el valor del artículo", "warning");
                                                  }
                                         }
                                      else
                                          {
                                               swal("Atención", "Ingrese el número de serie", "warning");
                                          }
                                 }
                              else
                                  {
                                       swal("Atención", "Ingrese el modelo", "warning");
                                  }
                         }
                      else
                          {
                               swal("Atención", "Ingrese el nombre de la marca", "warning");
                          }
                 }
             else
                 {
                       swal("Atención", "Ingrese el nombre del articulo", "warning");
                 }
             
    }
    
        function enviar_add_update(){
                 credito = getParameterByName('Credito');
                 
                 $('#credito_e').val(credito);
                 articulo = document.getElementById("articulo_e").value;
                 marca = document.getElementById("marca_e").value;
                 modelo = document.getElementById("modelo_e").value;
                 serie = document.getElementById("serie_e").value;
                 valor = document.getElementById("valor_e").value;
                 factura = document.getElementById("factura_e").value;
                 
                 if(articulo != '')
                     {
                          if(marca != '')
                             {
                                  if(modelo != '')
                                     {
                                          if(serie != '')
                                             {
                                                  if(valor != '')
                                                     {
                                                          if(factura != '')
                                                             {
                                                                 $.ajax({
                                                                    type: 'POST',
                                                                    url: '/Creditos/UpdateGarantia/',
                                                                    data: $('#AddUpdate').serialize(),
                                                                    success: function(respuesta) {
                                                                        if(respuesta != '0'){
                                                                        alertify.success('Registro Guardado con Exito');
                                                                        location.reload();
                                                                        }
                                                                        else {
                                                                             swal(respuesta, {
                                                                              icon: "error",
                                                                            });
                                                                        }
                                                                    }
                                                                    });
                                                             }
                                                          else
                                                              {
                                                                   alert("Ingrese la serie de la factura")
                                                              }
                                                     }
                                                  else
                                                      {
                                                           alert("Ingrese el valor del artículo")
                                                      }
                                             }
                                          else
                                              {
                                                   alert("Ingrese el número de serie")
                                              }
                                     }
                                  else
                                      {
                                           alert("Ingrese el modelo")
                                      }
                             }
                          else
                              {
                                   swal("Atención", "Ingrese el nombre de la marca", "warning");
                              }
                     }
                 else
                     {
                         swal("Atención", "Ingrese el nombre del articulo", "warning");
                     }
                 
                 
                 
        }
        
        function Delete_Garantias(secuencia) {
            credito = getParameterByName('Credito');
            secuencias = secuencia;
   
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
                        url: "/Creditos/DeleteGarantia/",
                        data: {"credito" : credito, "secuencia" : secuencias},
                        success: function(response){
                            //alert(response);
                            if(response != '0')
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
                    } else {
                swal("No se pudo eliminar el registro");
              }
            });
    
    
    
        }
        function Edit_Garantias(articulo_p, marca_p, modelo_p, no_serie_p, monto_p, factura_p, secuencia_p) {
    
            $('#articulo_e').val(articulo_p);
            $('#marca_e').val(marca_p);
            $('#modelo_e').val(modelo_p);
            $('#serie_e').val(no_serie_p);
            $('#valor_e').val(monto_p);
            $('#factura_e').val(factura_p);
            $('#secuencia_e').val(secuencia_p);
    
            $('#modal_editar_articulo').modal('show');
    
        }
        function Update_Garantias(secuencia) {
    
            secuencias = secuencia;
    
            alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                if(response){
    
                    $.ajax({
                        type: "POST",
                        url: "/Creditos/DeleteGarantia/",
                        data: {"credito" : credito, "secuencia" : secuencias},
                        success: function(respuesta){
                            //alert(response);
                            if(respuesta != '0')
                            {
                                  swal("Registro fue eliminado correctamente", {
                                      icon: "success",
                                    });
                                    location.reload();
    
                            }
                            else
                            {
                                swal(respuesta, {
                                    icon: "error",
                                });
                            }
                        }
                    });
    
    
                }
            });
    
        }
        
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];

        if($credito != '' || $ciclo != '')
        {
            $Garantias = CreditosDao::ConsultaGarantias($credito);

            if($Garantias[0] != ''){
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('credito',$credito);
                View::render("contrrolgarantias_busqueda_all");

            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('credito',$credito);
                View::render("controlgarantias_busqueda_message");
            }

        }
        else
        {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer',$this->_contenedor->footer($extraFooter));
            View::render("controlgarantias_all");
        }


    }

}
