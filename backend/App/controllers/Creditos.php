<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Creditos AS CreditosDao;

class Creditos extends Controller{

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

        if($credito != '')
        {
            $Garantias = CreditosDao::ConsultaGarantias($credito);
            $tabla= '';
            foreach ($Garantias as $key => $value) {
                $monto = number_format($value['MONTO']);
                if($value['FACTURA'] == '')
                {
                    $factura = '-';
                }
                else
                {
                    $factura = $value['FACTURA'];
                }
                $fecha = strval($value['FECREGISTRO']);
                $tabla.=<<<html
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
            if($Garantias[0] != ''){
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('tabla',$tabla);
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

    public function ActualizaCredito() {
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

        if($credito != '')
        {
            $tabla= '';
            $AdministracionOne = CreditosDao::ConsultarPagosAdministracionOne($credito);

            if($AdministracionOne['NO_CREDITO'] != ''){

                /////////////////////////7
                $ComboSucursal = '';
                if($AdministracionOne['SITUACION'] == 'E') {
                    $ComboSucursal .= <<<html
                    <option selected value="E">ENTREGADO</option>
                    <option value="L">LIQUIDADO</option>
html;
                }
                else{
                    $ComboSucursal .= <<<html
                     <option value="E">ENTREGADO</option>
                     <option selected value="L">LIQUIDADO</option>
html;
                }

                ////////////////////////
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('tabla',$tabla);
                View::set('credito',$credito);
                View::set('combo',$ComboSucursal);
                View::set('Administracion', $AdministracionOne);
                View::render("actualizacredito_busqueda_all");

            }
            else
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('credito',$credito);
                View::render("actualizacredito_busqueda_message");
            }

        }
        else
        {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer',$this->_contenedor->footer($extraFooter));
            View::render("actualizacredito_all");
        }
    }

    public function CambioSucursal() {
        $extraHeader = <<<html
        <title>Cambio de Sucursal</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter =<<<html
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

        if($credito != '')
        {
            $credito_cambio = CreditosDao::SelectSucursalAllCreditoCambioSuc($credito);

            if($credito_cambio['CLIENTE'] != '')
            {
                $sucursales = CreditosDao::ListaSucursales();
                $ComboSucursal = '';
                foreach ($sucursales as $key => $val2) {
                    if($val2['ID_SUCURSAL'] == $credito_cambio['ID_SUCURSAL'])
                    {
                        $selected = 'selected';
                    }
                    else
                    {
                        $selected = '';
                    }
                    $ComboSucursal .= <<<html
                <option $selected value="{$val2['ID_SUCURSAL']}">{$val2['SUCURSAL']}</option>
html;
                }

                View::set('header',$this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('Administracion', $credito_cambio);
                View::set('sucursal', $ComboSucursal);
                View::set('credito', $credito);
                View::render("cambio_sucursal_busqueda");
            }
            else
            {
                View::set('header',$this->_contenedor->header($extraHeader));
                View::set('footer',$this->_contenedor->footer($extraFooter));
                View::set('Administracion', $credito_cambio);
                View::set('credito', $credito);
                View::render("cambio_sucursal_busqueda_message");

            }

        }
        else
        {
            View::set('header',$this->_contenedor->header($extraHeader));
            View::set('footer',$this->_contenedor->footer($extraFooter));
            View::set('credito', $credito);
            View::render("cambio_sucursal_all");
        }

    }
    public function UpdateSucursal(){
        $sucursal = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $sucursal->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo');
        $sucursal->_ciclo = $ciclo;

        $nueva_sucursal = MasterDom::getDataAll('sucursal');
        $sucursal->_nueva_sucursal = $nueva_sucursal;

        $id = CreditosDao::UpdateSucursal($sucursal);

        if($id >= 1){
            return $id['VMENSAJE'];
        }else{
            return '0';
        }
    }
    public function InsertGarantia(){
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

        if($id >= 1){
            return $id['VMENSAJE'];
        }else{
            return '0';
        }
    }
    public function UpdateGarantia(){
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


        if($id >= 1){
            return $id['VMENSAJE'];
        }else{
            return '0';
        }
    }
    public function DeleteGarantia(){

        $id = $_POST['credito'];
        $secuencia = $_POST['secuencia'];
        $id = CreditosDao::ProcedureGarantiasDelete($id, $secuencia);

        var_dump($id);
        var_dump($secuencia);

        if($id >= 1){
            var_dump($id['VMENSAJE']);
            return $id['VMENSAJE'];
        }else{
            return '0';
        }
    }
    ////////////////////////////////////////////////////
    public function UpdateCredito(){
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $up_credito->_credito = $credito;

        $credito_nuevo = MasterDom::getDataAll('credito_nuevo');
        $up_credito->_credito_nuevo = $credito_nuevo;

        $id = CreditosDao::UpdateActulizaCredito($up_credito);

        if($id >= 1){
        }else{
            return '0';
        }
    }
    public function UpdateCicloCredito(){
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_c');
        $up_credito->_credito = $credito;

        $ciclo_nuevo = MasterDom::getDataAll('ciclo_c_n');
        $up_credito->_ciclo_nuevo = $ciclo_nuevo;

        $id = CreditosDao::UpdateActulizaCiclo($up_credito);

        return $id;
    }
    public function UpdateSituacion(){
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_s');
        $up_credito->_credito = $credito;

        $ciclo= MasterDom::getDataAll('ciclo_s');
        $up_credito->_ciclo_nuevo = $ciclo;

        $situacion = MasterDom::getDataAll('situacion_s');
        $up_credito->_situacion = $situacion;

        $id = CreditosDao::UpdateActulizaSituacion($up_credito);

        if($id >= 1){
            //var_dump($id['VMENSAJE']);
            return $id['VMENSAJE'];
        }else{
            return '0';
        }
    }

    public function generarExcel(){

        $credito = $_GET['Credito'];

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("jma");
        $objPHPExcel->getProperties()->setLastModifiedBy("jma");
        $objPHPExcel->getProperties()->setTitle("Reporte");
        $objPHPExcel->getProperties()->setSubject("Reorte");
        $objPHPExcel->getProperties()->setDescription("Descripcion");
        $objPHPExcel->setActiveSheetIndex(0);



        $estilo_titulo = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_encabezado = array(
            'font' => array('bold' => true,'name'=>'Calibri','size'=>11, 'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID
        );

        $estilo_celda = array(
            'font' => array('bold' => false,'name'=>'Calibri','size'=>11,'color' => array('rgb' => '060606')),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'type' => \PHPExcel_Style_Fill::FILL_SOLID

        );


        $fila = 1;
        $adaptarTexto = true;

        $controlador = "Creditos";
        $columna = array('A','B','C','D', 'E', 'F', 'G', 'H');
        $nombreColumna = array('Secuencia','Articulo','Marca','Modelo', 'Numero de Serie', 'Monto', 'Factura', 'Registro');
        $nombreCampo = array('SECUENCIA','ARTICULO','MARCA','MODELO', 'NO_SERIE', 'MONTO', 'FACTURA', 'FECREGISTRO');



        /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
        foreach ($nombreColumna as $key => $value) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        }
        $fila +=1; //fila donde comenzaran a escribirse los datos

        /* FILAS DEL ARCHIVO EXCEL */

        $Layoutt = CreditosDao::ConsultaGarantias($credito);
        foreach ($Layoutt as $key => $value) {
            foreach ($nombreCampo as $key => $campo) {
                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
            }
            $fila +=1;
        }


        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$columna[count($columna)-1].$fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for ($i=0; $i <$fila ; $i++) {
            $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
        }


        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Layout Garantias '.$controlador.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

    ////////////////////////////////////////////////////


}
