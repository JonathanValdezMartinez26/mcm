<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Creditos as CreditosDao;

class Creditos extends Controller
{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function getUsuario()
    {
        return $this->__usuario;
    }


    public function ControlGarantias()
    {
        $extraHeader = <<<html
        <title>Control de Garantías</title>
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

        if ($credito != '') {
            $Garantias = CreditosDao::ConsultaGarantias($credito);
            $tabla = '';
            foreach ($Garantias as $key => $value) {
                $monto = number_format($value['MONTO']);
                if ($value['FACTURA'] == '') {
                    $factura = '-';
                } else {
                    $factura = $value['FACTURA'];
                }
                $fecha = strval($value['FECREGISTRO']);
                $tabla .= <<<html
                <tr>
                    <td>{$fecha}</td>
                    <td>{$value['SECUENCIA']}</td>
                    <td>{$value['ARTICULO']}</td>
                    <td>{$value['MARCA']}</td>
                    <td>{$value['MODELO']}</td>
                    <td>{$value['NO_SERIE']}</td>
                    <td>$ {$monto}</td>
                    <td>{$factura}</td>
                    <td class="center" >
                         <button type="button" class="btn btn-success btn-circle" onclick="Edit_Garantias('{$value['ARTICULO']}','{$value['MARCA']}','{$value['MODELO']}','{$value['NO_SERIE']}', '{$value['MONTO']}', '{$value['FACTURA']}', '{$value['SECUENCIA']}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="Delete_Garantias({$value['SECUENCIA']});"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
            }
            if ($Garantias[0] != '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('credito', $credito);
                View::render("contrrolgarantias_busqueda_all");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('credito', $credito);
                View::render("controlgarantias_busqueda_message");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("controlgarantias_all");
        }
    }

    public function ActualizaCredito()
    {
        $extraHeader = <<<html
        <title>Actualizar Crédito</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
    
        //////////////////////////////////////
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        //////////////////////////////////////
        function enviar_edit_credito(){
            
             credito = document.getElementById("credito_nuevo").value;
             
            if(credito != '')
            {
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateCredito/',
                 data: $('#Add').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                             swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                             swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_editar_numero_credito').modal('hide');
                         }
                     
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
                alert("Ingresa el número de credito nuevo");
            }
        }
        function enviar_edit_ciclo(){
            
             ciclo = document.getElementById("ciclo_c_n").value;
             
            if(credito != '')
            {
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateCicloCredito/',
                 data: $('#AddCiclo').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                            swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                            swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_actualizar_ciclo').modal('hide');
                         }
                     
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
                swal("Ingrese el número del nuevo ciclo", {
                                      icon: "warning",
                             });
            }
        }
        function enviar_edit_situacion(){
            
            
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateSituacion/',
                 data: $('#AddSituacion').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                             swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                             swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_actualizar_ciclo').modal('hide');
                         }
                     
                 }
                 else {
                       swal(respuesta, {
                                    icon: "error",
                                });
                 }
                    }
                 });
        }
        
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {
            $tabla = '';
            $AdministracionOne = CreditosDao::ConsultarPagosAdministracionOne($credito);

            if ($AdministracionOne['NO_CREDITO'] != '') {

                /////////////////////////7
                $ComboSucursal = '';
                if ($AdministracionOne['SITUACION'] == 'E') {
                    $ComboSucursal .= <<<html
                    <option selected value="E">ENTREGADO</option>
                    <option value="L">LIQUIDADO</option>
html;
                } else {
                    $ComboSucursal .= <<<html
                     <option value="E">ENTREGADO</option>
                     <option selected value="L">LIQUIDADO</option>
html;
                }

                ////////////////////////
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('credito', $credito);
                View::set('combo', $ComboSucursal);
                View::set('Administracion', $AdministracionOne);
                View::render("actualizacredito_busqueda_all");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('credito', $credito);
                View::render("actualizacredito_busqueda_message");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("actualizacredito_all");
        }
    }

    public function CambioSucursal()
    {
        $extraHeader = <<<html
        <title>Cambio de Sucursal</title>
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
        
        function enviar_add(ciclo_p){
        credito = getParameterByName('Credito');
        sucursal = document.getElementById("sucursal").value;
        ciclo = ciclo_p;
        
        
            $.ajax({
                type: 'POST',
                url: '/Creditos/UpdateSucursal/',
                data: {"credito" : credito, "sucursal" : sucursal, "ciclo" : ciclo},
                success: function(respuesta) {
                    if(respuesta!='0'){
                        swal("Registro actualizado correctamente", {
                                      icon: "success",
                             });
                        location.reload();
                        
                    }
                    else {
                        swal(respuesta, {
                                    icon: "error",
                                });
                        $('#modal_cambio_sucursal').modal('hide')
                        alertify.error("Error en la actualización");
                    }
                }
                
            });
    }
    
     
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {
            $credito_cambio = CreditosDao::SelectSucursalAllCreditoCambioSuc($credito);

            if ($credito_cambio['CLIENTE'] != '') {
                $sucursales = CreditosDao::ListaSucursales();
                $ComboSucursal = '';
                foreach ($sucursales as $key => $val2) {
                    if ($val2['ID_SUCURSAL'] == $credito_cambio['ID_SUCURSAL']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $ComboSucursal .= <<<html
                <option $selected value="{$val2['ID_SUCURSAL']}">{$val2['SUCURSAL']}</option>
html;
                }

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $credito_cambio);
                View::set('sucursal', $ComboSucursal);
                View::set('credito', $credito);
                View::render("cambio_sucursal_busqueda");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $credito_cambio);
                View::set('credito', $credito);
                View::render("cambio_sucursal_busqueda_message");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('credito', $credito);
            View::render("cambio_sucursal_all");
        }
    }
    public function UpdateSucursal()
    {
        $sucursal = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $sucursal->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo');
        $sucursal->_ciclo = $ciclo;

        $nueva_sucursal = MasterDom::getDataAll('sucursal');
        $sucursal->_nueva_sucursal = $nueva_sucursal;

        $id = CreditosDao::UpdateSucursal($sucursal);

        if ($id >= 1) {
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }
    public function InsertGarantia()
    {
        $usuario = $this->__usuario;

        $garantia = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $garantia->_credito = $credito;

        $articulo = MasterDom::getDataAll('articulo');
        $garantia->_articulo = $articulo;

        $marca = MasterDom::getDataAll('marca');
        $garantia->_marca = $marca;

        $modelo = MasterDom::getDataAll('modelo');
        $garantia->_modelo = $modelo;

        $serie = MasterDom::getDataAll('serie');
        $garantia->_serie = $serie;

        $factura = MasterDom::getDataAll('factura');
        $garantia->_factura = $factura;

        $garantia->_usuario = $usuario;

        $valor = MasterDom::getDataAll('valor');
        $garantia->_valor = $valor;

        $id = CreditosDao::ProcedureGarantias($garantia);

        if ($id >= 1) {
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }
    public function UpdateGarantia()
    {
        $usuario = $this->__usuario;

        $garantia = new \stdClass();

        $credito = MasterDom::getDataAll('credito_e');
        $garantia->_credito = $credito;

        $articulo = MasterDom::getDataAll('articulo_e');
        $garantia->_articulo = $articulo;

        $marca = MasterDom::getDataAll('marca_e');
        $garantia->_marca = $marca;

        $modelo = MasterDom::getDataAll('modelo_e');
        $garantia->_modelo = $modelo;

        $serie = MasterDom::getDataAll('serie_e');
        $garantia->_serie = $serie;

        $factura = MasterDom::getDataAll('factura_e');
        $garantia->_factura = $factura;

        $secuencia = MasterDom::getDataAll('secuencia_e');
        $garantia->_secuencia = $secuencia;

        $garantia->_usuario = $usuario;

        $valor = MasterDom::getDataAll('valor_e');
        $garantia->_valor = $valor;

        $id = CreditosDao::ProcedureGarantiasUpdate($garantia);


        if ($id >= 1) {
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }
    public function DeleteGarantia()
    {

        $id = $_POST['credito'];
        $secuencia = $_POST['secuencia'];
        $id = CreditosDao::ProcedureGarantiasDelete($id, $secuencia);

        var_dump($id);
        var_dump($secuencia);

        if ($id >= 1) {
            var_dump($id['VMENSAJE']);
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }
    ////////////////////////////////////////////////////
    public function UpdateCredito()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $up_credito->_credito = $credito;

        $credito_nuevo = MasterDom::getDataAll('credito_nuevo');
        $up_credito->_credito_nuevo = $credito_nuevo;

        $id = CreditosDao::UpdateActulizaCredito($up_credito);

        if ($id >= 1) {
        } else {
            return '0';
        }
    }
    public function UpdateCicloCredito()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_c');
        $up_credito->_credito = $credito;

        $ciclo_nuevo = MasterDom::getDataAll('ciclo_c_n');
        $up_credito->_ciclo_nuevo = $ciclo_nuevo;

        $id = CreditosDao::UpdateActulizaCiclo($up_credito);

        return $id;
    }
    public function UpdateSituacion()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_s');
        $up_credito->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo_s');
        $up_credito->_ciclo_nuevo = $ciclo;

        $situacion = MasterDom::getDataAll('situacion_s');
        $up_credito->_situacion = $situacion;

        $id = CreditosDao::UpdateActulizaSituacion($up_credito);

        if ($id >= 1) {
            //var_dump($id['VMENSAJE']);
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }

    public function generarExcel()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('SECUENCIA', 'Secuencia'),
            \PHPSpreadsheet::ColumnaExcel('ARTICULO', 'Articulo'),
            \PHPSpreadsheet::ColumnaExcel('MARCA', 'Marca'),
            \PHPSpreadsheet::ColumnaExcel('MODELO', 'Modelo'),
            \PHPSpreadsheet::ColumnaExcel('NO_SERIE', 'Numero de Serie'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto'),
            \PHPSpreadsheet::ColumnaExcel('FACTURA', 'Factura'),
            \PHPSpreadsheet::ColumnaExcel('FECREGISTRO', 'Registro')
        ];

        $credito = $_GET['Credito'];
        $filas = CreditosDao::ConsultaGarantias($credito);

        \PHPSpreadsheet::DescargaExcel('Layout Garantías Creditos', 'Reporte', 'Garantías', $columnas, $filas);
    }

    ////////////////////////////////////////////////////

    public function cierreDiario()
    {
        $extraFooter = <<<HTML
        <script>
            {$this->mensajes}
            {$this->descargaExcel}

            const descarga = () => {
                const fecha = document.getElementById('fecha').value
                if (!fecha) return showError("Ingrese una fecha a buscar.")
                
                descargaExcel("/Creditos/excelCierreDiario/?" + $.param({ fecha }))
            }
        </script>
        HTML;

        $ahora = new \DateTime();
        $cierre = new \DateTime('16:00:00');

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Cierres Operativos")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', $ahora > $cierre ? date('Y-m-d') : date('Y-m-d', strtotime('-1 day')));
        View::render('cierre_diario');
    }

    public function GetCierreDiario($f = null)
    {
        $fecha = $_POST['fecha'] ?? $f;
        $datos = CreditosDao::GetCierreDiario($fecha);
        $datos = $datos['success'] ? $datos['datos'] : [];

        $tabla = "";
        foreach ($datos as $key => $dato) {
            $tabla .= "<tr>";
            foreach ($dato as $key2 => $campo) {
                $tabla .= "<td style='vertical-align: middle;'>{$campo}</td>";
            }
            $tabla .= "</tr>";
        }

        if (!$_SERVER['REQUEST_METHOD'] === 'POST') return $tabla;

        echo json_encode([
            "success" => count($datos) > 0,
            "datos" => $tabla,
            "mensaje" => count($datos) > 0 ? "" : "No se encontraron registros."
        ]);
    }

    public function excelCierreDiario()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'SUCURSAL'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_ASESOR', 'NOMBRE ASESOR'),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_GRUPO', 'CODIGO GRUPO', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_CLIENTE', 'CODIGO CLIENTE', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CURP_CLIENTE', 'CURP CLIENTE', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO_CLIENTE', 'NOMBRE CLIENTE'),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_AVAL', 'CODIGO AVAL', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CURP_AVAL', 'CURP AVAL', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO_AVAL', 'NOMBRE AVAL'),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'CICLO', $centrado),
            \PHPSpreadsheet::ColumnaExcel('FECHA_INICIO', 'FECHA INICIO', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('SALDO_TOTAL', 'SALDO TOTAL', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('MORA_TOTAL', 'MORA TOTAL', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('DIAS_MORA', 'DIAS MORA', $centrado),
            \PHPSpreadsheet::ColumnaExcel('TIPO_CARTERA', 'TIPO CARTERA', $centrado)
        ];

        $fecha = $_GET['fecha'];
        $filas = CreditosDao::GetCierreDiario($fecha);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Situación Cartera MCM', 'Reporte', 'Situación Cartera MCM', $columnas, $filas);
    }

    public function AdminCorreos()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->configuraTabla}
                {$this->actualizaDatosTabla}
                {$this->confirmarMovimiento}

                $(document).on("ready", () => {
                    $("#addCorreo").on("click", () => $("#modalCorreo").modal("show"))
                    $("#addGrupo").on("click", () => $("#modalGrupo").modal("show"))


                    $("#areaFiltro").on("change", getCorreos)
                    $("#sucursalFiltro").on("change", getCorreos)
                    // $("#grupoFiltro").on("change", getCorreoGrupo)
                    $("#btnAgregar").on("click", addCorreoGrupo)
                    $("#btnQuitar").on("click", eliminarCorreoGrupo)
                    $("#nombre").on("change", validaCampos)
                    $("#correo").on("keyup", sugerenciasCorreo)
                    $("#correo").on("blur", () => {
                        const correo = $("#correo").val()
                        $("#sugerenciasCorreo").remove()
                        $("#correo").attr("list", "")
                        if (!correo) return showError("Debe ingresar un correo electrónico.")
                        if (!validaCorreo(correo)) return showError("El correo electrónico ingresado no es válido.")
                    })
                    $("#area").on("change", validaCampos)
                    $("#sucursal").on("change", validaCampos)
                    $("#guardarDireccion").on("click", addCorreo)
                    $("#guardarGrupo").on("click", addGrupo)
                    $("#buscarGrupo").on("keyup", buscarGrupos)

                    getCorreos()
                    getCorreoGrupo()
                    configuraTabla("tblCorreos", {noRegXvista: false})
                    configuraTabla("tblGrupo", {noRegXvista: false})
                    
                    $(".dataTables_filter").css("width", "100%")
                    $(".dataTables_filter").css("width", "100%")
                })

                const getCorreos = () => {
                    const parametros = {}

                    if ($("#areaFiltro").val() !== "*") parametros.area = $("#areaFiltro").val()
                    if ($("#sucursalFiltro").val() !== "*") parametros.sucursal = $("#sucursalFiltro").val()

                    consultaServidor("/Creditos/GetCorreos", parametros, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los correos.")
                        if (respuesta.datos.length === 0) return showError("No se encontraron correos registrados.").then(() => actualizaDatosTabla("tblCorreos", null))

                        const correos = respuesta.datos.map((correo) => {
                            const checador = "<input type='checkbox' name='correo' value='" + correo.ID + "'onchange='compruebaCorreoGrupo(event)'>"

                            return [
                                checador,
                                correo.NOMBRE,
                                correo.CORREO,
                                correo.AREA,
                                correo.SUCURSAL
                            ]

                            $("#tblCorreos tbody").append(fila)
                        })

                        actualizaDatosTabla("tblCorreos", correos)
                    })
                }

                const getCorreoGrupo = () => {
                    const grupo = $("#idGrupoSeleccionado").val() 

                    consultaServidor("/Creditos/GetCorreosGrupo", { grupo }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los grupos.")
                        
                        const grupos = respuesta.datos.map((grupo) => {
                            return [
                                grupo.EDITABLE == 1 ? "<input type='checkbox' name='grupo' value='" + grupo.ID_CORREO + "'>" : "",
                                grupo.CORREO
                            ]
                        })

                        actualizaDatosTabla("tblGrupo", grupos)
                    })
                }

                const addCorreoGrupo = () => {
                    const correosNuevos = []
                    $("#tblCorreos tbody input[type='checkbox']:checked").each((index, element) => {
                        if ($("#tblGrupo tbody input[type='checkbox'][value='" + $(element).val() + "']").length === 0)
                            correosNuevos.push($(element).val())
                        else {
                            element.checked = false
                            return showError("El correo " + $(element).val() + " ya está agregado al grupo seleccionado.")
                        }
                    })

                    if (correosNuevos.length === 0) return showError("Seleccione al menos un correo para agregar al grupo.")
                    
                    const grupo = $("#idGrupoSeleccionado").val()
                    if (!grupo) return showError("Selecciones un grupo para agregar los correos.")

                    consultaServidor("/Creditos/AgregaCorreoGrupo", { grupo, correos: correosNuevos, usuario: "{$_SESSION['usuario']}" }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al agregar los correos al grupo.")
                        
                        correosNuevos.forEach((correo) => {
                            $("#tblCorreos tbody input[type='checkbox'][value='" + correo + "']").prop("checked", false)
                        })
                        actualizaListaGrupos($("#grupoSeleccionado").val())
                        showSuccess("Correos agregados al grupo correctamente.")
                    })
                }

                const eliminarCorreoGrupo = () => {
                    const correos = []
                    $("#tblGrupo tbody input[type='checkbox']:checked").each((index, element) => {
                        correos.push($(element).val())
                    })

                    if (correos.length === 0) return showError("Seleccione al menos un correo para quitar del grupo.")
                    
                    const grupo = $("#idGrupoSeleccionado").val()
                    if (!grupo) return showError("Selecciones un grupo para quitar los correos.")

                    consultaServidor("/Creditos/EliminaCorreoGrupo", { grupo, correos }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al quitar los correos del grupo.")
                        
                        correos.forEach((correo) => {
                            $("#tblGrupo tbody input[type='checkbox'][value='" + correo + "']").prop("checked", false)
                        })
                        actualizaListaGrupos($("#grupoSeleccionado").val())
                        showSuccess("Correos quitados del grupo correctamente.")
                    })
                }

                const compruebaCorreoGrupo = (e) => {
                    if (!e.target.checked) return
                    e.target.checked = false

                    if ($("#idGrupoSeleccionado").val() === "") return showError("Debe seleccionar un grupo para agregar correos.")
                    if ($("#tblGrupo tbody input[type='checkbox'][value='" + e.target.value + "']").length > 0)
                        return showError("El correo seleccionado ya está agregado al grupo " + $("#grupoFiltro option:selected").text() + ".")

                    e.target.checked = true
                }

                const validaCampos = (e) => {
                    $("#guardarDireccion").prop("disabled", (!$("#nombre").val() || !$("#correo").val() || !$("#area").val() || !$("#sucursal").val()))
                }

                const addCorreo = () => {
                    if (!$("#nombre").val()) return showError("Ingrese el nombre del usuario.")
                    if (!$("#correo").val()) return showError("Ingrese el correo electrónico.")
                    if (!$("#area").val()) return showError("Seleccione un área.")
                    if (!$("#sucursal").val()) return showError("Seleccione una sucursal.")

                    const registro = {
                        nombre: $("#nombre").val(),
                        correo: $("#correo").val(),
                        area: $("#area").val(),
                        sucursal: $("#sucursal").val(),
                        usuario: "{$_SESSION['usuario']}"
                    }

                    consultaServidor("/Creditos/AgregaCorreo", registro, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al registrar el correo.")
                        
                        showSuccess("Correo registrado correctamente.")
                        getCorreos()
                    })

                    $("#nombre").val("")
                    $("#correo").val("")
                    $("#empresa").val("")
                    $("#sucursal").val("")
                    $("#guardarDireccion").prop("disabled", true)

                    $("#modalCorreo").modal("hide")
                }

                const validaCorreo = (correo) => {
                    const regexCorreo = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/
                    return (!correo || !regexCorreo.test(correo)) ? false : true
                }

                const addGrupo = () => {
                    const grupo = $("#nombreGrupo").val().trim()
                    if (!grupo) return showError("Ingrese un nombre para el nuevo grupo.")

                    const coincidencias = $("#grupoFiltro li .nombreGrupo").filter((index, element) => element.innerText.trim().toLowerCase() === grupo.toLowerCase())
                    if (coincidencias.length > 0) return showError("El grupo " + grupo + " ya existe.")

                    consultaServidor("/Creditos/AgregaGrupo", { grupo, usuario: "{$_SESSION['usuario']}" }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al registrar el grupo.")
                        
                        showSuccess("Grupo registrado correctamente.")
                        actualizaListaGrupos(grupo)
                    })

                    $("#nombreGrupo").val("")
                    $("#modalGrupo").modal("hide")
                }

                const actualizaListaGrupos = (grupo = null) => {
                    consultaServidor("/Creditos/GetParametros", null, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los grupos.")
                        
                        $("#grupoFiltro").empty()
                        $("#grupoFiltro").append(respuesta.datos.grupo)
                        if (grupo) $("#grupoFiltro li .nombreGrupo").filter((index, element) => element.innerText === grupo).click()
                        else {
                            if ($("#grupoSeleccionado").text() === grupo) {
                                $("#grupoSeleccionado").text("Seleccionar grupo")
                                $("#idGrupoSeleccionado").val("")
                            }
                            getCorreoGrupo()
                        }
                    })
                }

                const sugerenciasCorreo = () => {
                    if ($("#correo").val().indexOf("@") !== -1) {
                        const empresas = ["masconmenos.com.mx", "financieracultiva.com"]

                        const correo = $("#correo").val().split("@")[0]
                        const lista = empresas.map((empresa) => {
                            return "<option value='" + correo + "@" + empresa + "'>" + correo + "@" + empresa + "</option>"
                        })

                        const datalist = $("<datalist id='sugerenciasCorreo'>" + lista.join("") + "</datalist>")
                        $("#correo").after(datalist).attr("list", "sugerenciasCorreo")                    
                    } else {
                        $("#sugerenciasCorreo").remove()
                        $("#correo").attr("list", "")
                    }
                    validaCampos()
                }

                const seleccionGrupo = (id, grupo) => {
                    $("#idGrupoSeleccionado").val(id)
                    $("#grupoSeleccionado").text(grupo)
                    getCorreoGrupo()
                }

                const eliminarGrupo = (id, grupo) => {
                    confirmarMovimiento("Administración de correos", "¿Esta seguro de eliminar el grupo " + grupo + "?")
                    .then((continuar) => {
                        if (!continuar) return
                        consultaServidor("/Creditos/EliminaGrupo", { grupo: id }, (respuesta) => {
                            if (!respuesta.success) return showError("Ocurrio un error al eliminar el grupo.")
                            
                            showSuccess("Grupo eliminado correctamente.")
                            actualizaListaGrupos()
                        })
                    })
                }

                const buscarGrupos = () => {
                    $("#sinResultados").hide()
                    const buscar = $("#buscarGrupo").val().toLowerCase()

                    const encontrados = $("#grupoFiltro li").filter((index, element) => {
                        const elemento = $(element).find(".nombreGrupo")
                        const textoOriginal = elemento.text()
                        const textoMinuscula = textoOriginal.toLowerCase()
                        const indexMatch = textoMinuscula.indexOf(buscar)
                        
                        if (indexMatch !== -1) {
                            const parteAntes = textoOriginal.substring(0, indexMatch)
                            const parteCoincidente = textoOriginal.substring(indexMatch, indexMatch + buscar.length)
                            const parteDespues = textoOriginal.substring(indexMatch + buscar.length)

                            if (buscar === "") elemento.text(textoOriginal)
                            else elemento.html(parteAntes + "<mark>" + parteCoincidente + "</mark>" + parteDespues)

                            $(element).show()
                            return true
                        } else {
                            elemento.text(textoOriginal)
                            $(element).hide()
                            return false
                        }
                    })

                    if (encontrados.length === 0) $("#sinResultados").show()
                }

            </script>
        HTML;

        $prm = $this->GetParametros(true);
        $prm = $prm['datos'];

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Administración de correos")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcArea', $prm['area']);
        View::set('opcSucursal', $prm['sucursal']);
        View::set('opcGrupo', $prm['grupo']);
        View::set('opcSucursales', $prm['sucursales']);
        View::render('creditos_adminCorreos');
    }

    public function GetParametros($ret = false)
    {
        $parametros = CreditosDao::GetParametrosCorreos();

        $opcArea = "<option value='*'>Todas</option>";
        $opcSucursal = "<option value='*'>Todas</option>";
        $opcGrupo = "";
        $opcSucursales = "<option value=''>Seleccione una sucursal</option>";

        if ($parametros['success']) {
            foreach ($parametros['datos'] as $parametro) {
                if ($parametro['TIPO'] === 'AREA') $opcArea .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";
                if ($parametro['TIPO'] === 'SUCURSAL') $opcSucursal .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";

                if ($parametro['TIPO'] === 'GRUPO') {
                    $boton = '';

                    if ($parametro['USUARIOS'] == 0) $boton = "<button type='button' class='btn btn-danger btn-sm' onclick='eliminarGrupo(\"{$parametro['VALOR']}\", \"{$parametro['MOSTRAR']}\")' style='grid-column: 2;'>
                        <span class='glyphicon glyphicon-trash'></span>
                    </button>";

                    $opcGrupo .= "<li class='dropdown-item d-flex justify-content-between align-items-center'>
                        <div style='display: grid; grid-template-columns: 1fr auto .3fr; width: 100%; gap: 20px; align-items: center; padding: 5px;'>
                            <span style='color: black; grid-column: 1; cursor: pointer;' class='nombreGrupo' onclick='seleccionGrupo(\"{$parametro['VALOR']}\", \"{$parametro['MOSTRAR']}\")'>{$parametro['MOSTRAR']}</span> 
                            $boton
                            <span style='grid-column: 3; text-align: right;'>{$parametro['USUARIOS']}&nbsp;<span class='glyphicon glyphicon-user'></span></span>
                        </div>
                    </li>";
                }
                if ($parametro['TIPO'] === 'SUCURSALES') $opcSucursales .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";
            }
        }

        $res = [
            'success' => $parametros['success'],
            'mensaje' => $parametros['mensaje'],
            'datos' => [
                'area' => $opcArea,
                'sucursal' => $opcSucursal,
                'grupo' => $opcGrupo,
                'sucursales' => $opcSucursales
            ]
        ];

        if (!$ret) echo json_encode($res);
        else return $res;
    }

    public function GetCorreos()
    {
        echo json_encode(CreditosDao::GetCorreos($_POST));
    }

    public function GetCorreosGrupo()
    {
        if (count($_POST) === 1 && isset($_POST['grupo']) && $_POST['grupo'] !== '')
            echo json_encode(CreditosDao::GetCorreosGrupo($_POST));
        else echo json_encode(["success" => true, "datos" => []]);
    }

    public function AgregaCorreoGrupo()
    {
        echo json_encode(CreditosDao::AgregaCorreoGrupo($_POST));
    }

    public function EliminaCorreoGrupo()
    {
        echo json_encode(CreditosDao::EliminaCorreoGrupo($_POST));
    }

    public function AgregaCorreo()
    {
        echo json_encode(CreditosDao::AgregaCorreo($_POST));
    }

    public function AgregaGrupo()
    {
        echo json_encode(CreditosDao::AgregaGrupo($_POST));
    }

    public function EliminaGrupo()
    {
        echo json_encode(CreditosDao::EliminaGrupo($_POST));
    }
}
