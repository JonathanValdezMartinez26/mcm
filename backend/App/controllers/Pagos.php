<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos AS PagosDao;

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

    public function getUsuario()
    {
        return $this->__usuario;
    }

    public function index()
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
        
        function FunDelete_Pago(secuencia, fech, ciclo) {
             credito = getParameterByName('Credito');
             secuencias = secuencia;
             fecha = fech;
             
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                      
                      $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"credito" : credito, "secuencia" : secuencias, "fecha" : fecha},
                        success: function(response){
                            if(response != 1)
                                {
                                    alertify.success("Se ha eliminado correctamente");
                                    location.reload();
                                    
                                }
                            else
                                {
                                     alertify.error("Error, al eliminar.");
                                }
                        }
                    });
                      
                    
                  }
                });
              
             }
        
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
                        document.getElementById("tipo").value = "";
                        alertify.confirm('Registro Guardado con Exito');
                        }
                        else {
                        $('#addnew').modal('hide')
                         alertify.confirm('Registro Guardado con Exito');
                            document.getElementById("monto").value = "";
                            document.getElementById("tipo").value = "";
                        }
                    }
                    });
                }
    }
    
        
      </script>
html;

        //$busqueda = $_POST['busqueda'];
        $credito = $_GET['Credito'];
        $tabla = '';

        $status = PagosDao::ListaEjecutivos();
        $getStatus = '';
        foreach ($status as $key => $val2) {
            $getStatus .= <<<html
                <option value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }
        if ($credito != '') {
            $Administracion = PagosDao::ConsultarPagosAdministracion($credito);
            $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito);

            foreach ($Administracion as $key => $value) {
                $oldDate = strtotime($value['FECHA']);
                $newDate = date('Y-m-d',$oldDate);

                if($value['FIDENTIFICAPP'] ==  NULL)
                {
                    $medio = '<span class="count_top" style="font-size: 25px"><i class="fa fa-female"></i></span>';
                }
                else
                {
                    $medio = '<span class="count_top" style="font-size: 30px"><i class="fa fa-female"></i></span>';
                }

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;" width="45" nowrap>{$medio}</td>
                    <td style="padding: 0px !important;" width="45" nowrap>{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;" class="center" >
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('4');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago({$value['SECUENCIA']}, '{$newDate}', {$value['CICLO']});"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
            }
            if($Administracion[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::render("pagos_admin_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('status', $getStatus);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_admin_busqueda");
            }

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_admin_all");
        }

        if(isset($_POST['agregar'])){
            echo '<script> alert("Data Saved"); </script>';
        }
    }

    public function PagosAdd(){
        $pagos = new \stdClass();
        $monto = MasterDom::getDataAll('monto');
        $pagos->_monto = $monto;
        $tipo = MasterDom::getDataAll('tipo');
        $pagos->_tipo = $tipo;
        $pagos->_ejecutivo = MasterDom::getData('ejecutivo');
        $id = PagosDao::insertProcedure($pagos);

        if($id >= 1){
            return 'ok';
        }else{
            return 'fail';
        }
    }

    public function Delete(){

        $id = $_POST['credito'];
        $secuencia = $_POST['secuencia'];
        $fecha = $_POST['fecha'];
        $id = PagosDao::DeletePago($id, $secuencia, $fecha);

        var_dump($id);
    }

    public function PagosRegistro()
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
        
        function FunDelete_Pago(secuencia, fech, ciclo) {
             credito = getParameterByName('Credito');
             secuencias = secuencia;
             fecha = fech;
             
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                      
                      $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"credito" : credito, "secuencia" : secuencias, "fecha" : fecha},
                        success: function(response){
                            if(response != 1)
                                {
                                    alertify.success("Se ha eliminado correctamente");
                                    location.reload();
                                    
                                }
                            else
                                {
                                     alertify.error("Error, al eliminar.");
                                }
                        }
                    });
                      
                    
                  }
                });
              
             }
        
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
                        document.getElementById("tipo").value = "";
                        alertify.confirm('Registro Guardado con Exito');
                        }
                        else {
                        $('#addnew').modal('hide')
                         alertify.confirm('Registro Guardado con Exito');
                            document.getElementById("monto").value = "";
                            document.getElementById("tipo").value = "";
                        }
                    }
                    });
                }
    }
      </script>
html;

        //$busqueda = $_POST['busqueda'];
        $credito = $_GET['Credito'];
        $tabla = '';

        $status = PagosDao::ListaEjecutivos();
        $getStatus = '';
        foreach ($status as $key => $val2) {
            $getStatus .= <<<html
                <option value="{$val2['ID_EJECUTIVO']}">{$val2['EJECUTIVO']}</option>
html;
        }



        if ($credito != '') {
            $Administracion = PagosDao::ConsultarPagosAdministracion($credito);
            $AdministracionOne = PagosDao::ConsultarPagosAdministracionOne($credito);

            foreach ($Administracion as $key => $value) {
                $oldDate = strtotime($value['FECHA']);

                $newDate = date('Y-m-d',$oldDate);



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['SECUENCIA']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['FECHA']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">$ {$value['MONTO']}</td>
                    <td style="padding: 0px !important;">{$value['TIPO']}</td>
                    <td style="padding: 0px !important;" class="center" >
                        <button type="button" class="btn btn-success btn-circle" onclick="FunEdit($credito, {$value['SECUENCIA']}, {$value['CICLO']});"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago({$value['SECUENCIA']}, '{$newDate}', {$value['CICLO']});"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
            }
            if($Administracion[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('status', $getStatus);
                View::set('credito', $credito);
                View::render("pagos_registro_busqueda_message");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('status', $getStatus);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("pagos_registro_busqueda");
            }

        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("pagos_registro_all");
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
        
        function FunDelete_Pago(secuencia, fech, ciclo) {
             credito = getParameterByName('Credito');
             secuencias = secuencia;
             fecha = fech;
             
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                      
                      $.ajax({
                        type: "POST",
                        url: "/Pagos/Delete/",
                        data: {"credito" : credito, "secuencia" : secuencias, "fecha" : fecha},
                        success: function(response){
                            if(response != 1)
                                {
                                    alertify.success("Se ha eliminado correctamente");
                                    location.reload();
                                    
                                }
                            else
                                {
                                     alertify.error("Error, al eliminar.");
                                }
                        }
                    });
                      
                    
                  }
                });
              
             }
        
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
                        document.getElementById("tipo").value = "";
                        alertify.confirm('Registro Guardado con Exito');
                        }
                        else {
                        $('#addnew').modal('hide')
                         alertify.confirm('Registro Guardado con Exito');
                            document.getElementById("monto").value = "";
                            document.getElementById("tipo").value = "";
                        }
                    }
                    });
                }
    }
      </script>
html;

        $consolidado = $_GET['Consolidado'];
        $tabla = '';

        if ($consolidado != '') {
            $CorteCaja = PagosDao::getAllByIdCorteCaja(1);

            foreach ($CorteCaja as $key => $value) {

                //////////////////////////////////////
                if($value['TIPO'] == 'P')
                {
                    $tipo_pago = 'PAGO';
                }
                if($value['TIPO_PAGO'] == 'G')
                {
                    $tipo_pago = 'GARANTÍA';
                }
                if($value['TIPO_PAGO'] == 'M')
                {

                }
                if($value['TIPO_PAGO'] == 'A')
                {

                }
                if($value['TIPO_PAGO'] == 'W')
                {

                }
                if($value['ESTATUS_CAJA'] == '0')
                {
                    if($value['INCIDENCIA'] == 1)
                    {
                        $estatus = 'PENDIENTE, CON MODIFICACION';
                    }
                    else{
                        $estatus = 'PENDIENTE';
                    }


                }
                //////////////////////////////////////
                if($value['INCIDENCIA'] == 1)
                {
                    $incidencia = '<br><span class="count_top" style="font-size: 20px; color: gold"><i class="fa fa-warning"></i></span> <b>Incidencia:</b>'.$value['COMENTARIO_INCIDENCIA'];
                    $monto = '<span class="count_top" style="font-size: 18px; color: #017911">Monto a recibir: $' .number_format($value['NUEVO_MONTO']). '</span>
                              <span class="count_top" style="font-size: 13px; color: #ff0066">Monto registrado: $' .number_format($value['MONTO']).'</span>';

                }else{
                    $incidencia = '';
                    $monto = '$ '.number_format($value['MONTO']);
                }
                /// //////////////////////////////////////
                var_dump($value['COMENTARIO_INCIDENCIA']);
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
                
                <td class="center" >
                <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago({$value['CORTECAJA_PAGOSDIA_PK']});"><i class="fa fa-trash"></i></button>
                    <a href="/Pagos/CorteCaja/?Consolidado={$value['CDGPE']}/" type="submit" name="id_coordinador" class="btn btn-success"><span class="fa fa-edit" style="color:white"></span></a>
                </td>
                </tr>
            
html;
            }

            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if($CorteCaja[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }
            else
            {
                View::set('tabla', $tabla);
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_view");
            }

        }
        else {

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
                    <a href="/Pagos/CorteCaja/?Consolidado={$value['CDGPE']}/" type="submit" name="id_coordinador" class="btn btn-success"><span class="fa fa-product-hunt" style="color:white"></span> Liberar Pagos</a>
                </td>
                </tr>
            
html;
            }
            /////////////////////////////////////////////////////////////////
            /// Sirve para decir que la consulta viene vacia, mandar mernsaje de vacio
            if($CorteCaja[0] == '')
            {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::render("cortecaja_all");////CAmbiar a una en donde diga que no hay registros
            }
            else
            {
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
            $extraFooter = <<<html
      <script>
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
            $("#checkAll").click(function () {
              if(checkAll==0){
                $("input:checkbox").prop('checked', true);
                checkAll = 1;
              }else{
                $("input:checkbox").prop('checked', false);
                checkAll = 0;
              }

            });


            $("#export_pdf").click(function(){
              $('#all').attr('action', '/Empresa/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Empresa/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Empresa/delete');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

        });
      </script>
html;
            $CorteCaja = CorteCajaDao::getAll();
            $usuario = $this->__usuario;
            $tabla = '';
            foreach ($CorteCaja as $key => $value) {
                $tabla .= <<<html
                <tr>
                <td> {$value['NUM_PAG']}</td>
                
                <td><i class="fa fa-user"></i>  {$value['CDGPE']}</td>
                <td>$ {$value['MONTO_TOTAL']}</td>
                <td>$ {$value['MONTO_PAGO']}</td>
                <td>$ {$value['MONTO_GARANTIA']}</td>
                <td>$ {$value['MONTO_DESCUENTO']}</td>
                <td>$ {$value['MONTO_REFINANCIAMIENTO']}</td>
                <td class="center" >
                    <a href="/CorteCaja/Show/{$value['CDGPE']}" type="submit" name="id_coordinador" class="btn btn-success"><span class="fa fa-product-hunt" style="color:white"></span> Liberar Pagos</a>
                </td>
                </tr>
html;
            }

            View::set('tabla', $tabla);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("cortecaja_all");
        }

    public function generarPDF()
        {
            $ids = MasterDom::getDataAll('borrar');
            $mpdf = new \mPDF('c');
            $mpdf->defaultPageNumStyle = 'I';
            $mpdf->h2toc = array('H5' => 0, 'H6' => 1);
 $style = <<<html
      <style>
        .imagen{
          width:100%;
          height: 150px;
          background: url(/img/ag_logo.png) no-repeat center center fixed;
          background-size: cover;
          -moz-background-size: cover;
          -webkit-background-size: cover
          -o-background-size: cover;
        }

        .titulo{
          width:100%;
          margin-top: 30px;
          color: #F5AA3C;
          margin-left:auto;
          margin-right:auto;
        }
      </style>
html;
            $tabla = <<<html
  <img class="imagen" src="/img/ag_logo.png"/>
  <br>
  <div style="page-break-inside: avoid;" align='center'>
  <H1 class="titulo">Empresas</H1>
  <table border="0" style="width:100%;text-align: center">
    <tr style="background-color:#B8B8B8;">
    <th><strong>Id</strong></th>
    <th><strong>Nombre</strong></th>
    <th><strong>Descripción</strong></th>
    <th><strong>Status</strong></th>
    </tr>
html;

            if ($ids != '') {
                foreach ($ids as $key => $value) {
                    $empresa = EmpresaDao::getByIdReporte($value);
                    $tabla .= <<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['catalogo_empresa_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['status']}</td>
              </tr>
html;
                }
            } else {
                foreach (EmpresaDao::getAll() as $key => $empresa) {
                    $tabla .= <<<html
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['catalogo_empresa_id']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['nombre']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['descripcion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['status']}</td>
            </tr>
html;
                }
            }
            $tabla .= <<<html
      </table>
      </div>
html;
            $mpdf->WriteHTML($style, 1);
            $mpdf->WriteHTML($tabla, 2);
            //$nombre_archivo = "MPDF_".uniqid().".pdf";/* se genera un nombre unico para el archivo pdf*/
            print_r($mpdf->Output());/* se genera el pdf en la ruta especificada*/
            //echo $nombre_archivo;/* se imprime el nombre del archivo para poder retornarlo a CrmCatalogo/index */

            exit;
            //$ids = MasterDom::getDataAll('borrar');
            //echo shell_exec('php -f /home/granja/backend/public/librerias/mpdf_apis/Api.php Empresa '.json_encode(MasterDom::getDataAll('borrar')));
        }

    public function generarExcel()
        {
            $ids = MasterDom::getDataAll('borrar');
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()->setCreator("jma");
            $objPHPExcel->getProperties()->setLastModifiedBy("jma");
            $objPHPExcel->getProperties()->setTitle("Reporte");
            $objPHPExcel->getProperties()->setSubject("Reorte");
            $objPHPExcel->getProperties()->setDescription("Descripcion");
            $objPHPExcel->setActiveSheetIndex(0);

            /*AGREGAR IMAGEN AL EXCEL*/
            //$gdImage = imagecreatefromjpeg('http://52.32.114.10:8070/img/ag_logo.jpg');
            $gdImage = imagecreatefrompng('http://52.32.114.10:8070/img/ag_logo.png');
            // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
            $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');
            $objDrawing->setImageResource($gdImage);
            //$objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
            $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
            $objDrawing->setWidth(50);
            $objDrawing->setHeight(125);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

            $estilo_titulo = array(
                'font' => array('bold' => true, 'name' => 'Verdana', 'size' => 16, 'color' => array('rgb' => 'FEAE41')),
                'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'type' => \PHPExcel_Style_Fill::FILL_SOLID
            );

            $estilo_encabezado = array(
                'font' => array('bold' => true, 'name' => 'Verdana', 'size' => 14, 'color' => array('rgb' => 'FEAE41')),
                'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'type' => \PHPExcel_Style_Fill::FILL_SOLID
            );

            $estilo_celda = array(
                'font' => array('bold' => false, 'name' => 'Verdana', 'size' => 12, 'color' => array('rgb' => 'B59B68')),
                'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'type' => \PHPExcel_Style_Fill::FILL_SOLID

            );


            $fila = 9;
            $adaptarTexto = true;

            $controlador = "Empresa";
            $columna = array('A', 'B', 'C', 'D');
            $nombreColumna = array('Id', 'Nombre', 'Descripción', 'Status');
            $nombreCampo = array('catalogo_empresa_id', 'nombre', 'descripcion', 'status');

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $fila, 'Reporte de Empresas');
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $fila . ':' . $columna[count($nombreColumna) - 1] . $fila);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->applyFromArray($estilo_titulo);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila)->getAlignment()->setWrapText($adaptarTexto);

            $fila += 1;

            /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
            foreach ($nombreColumna as $key => $value) {
                $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, $value);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_encabezado);
                $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
                $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
            }
            $fila += 1; //fila donde comenzaran a escribirse los datos

            /* FILAS DEL ARCHIVO EXCEL */
            if ($ids != '') {
                foreach ($ids as $key => $value) {
                    $empresa = EmpresaDao::getByIdReporte($value);
                    foreach ($nombreCampo as $key => $campo) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, html_entity_decode($empresa[$campo], ENT_QUOTES, "UTF-8"));
                        $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_celda);
                        $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
                    }
                    $fila += 1;
                }
            } else {
                foreach (EmpresaDao::getAll() as $key => $value) {
                    foreach ($nombreCampo as $key => $campo) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key] . $fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
                        $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->applyFromArray($estilo_celda);
                        $objPHPExcel->getActiveSheet()->getStyle($columna[$key] . $fila)->getAlignment()->setWrapText($adaptarTexto);
                    }
                    $fila += 1;
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $columna[count($columna) - 1] . $fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            for ($i = 0; $i < $fila; $i++) {
                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
            }

            $objPHPExcel->getActiveSheet()->setTitle('Reporte');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte AG ' . $controlador . '.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }

   public function alerta($id, $parametro)
        {
            $regreso = "/Empresa/";

            if ($parametro == 'add') {
                $mensaje = "Se ha agregado correctamente";
                $class = "success";
            }

            if ($parametro == 'edit') {
                $mensaje = "Se ha modificado correctamente";
                $class = "success";
            }

            if ($parametro == 'delete') {
                $mensaje = "Se ha eliminado la empresa {$id}, ya que cambiaste el estatus a eliminado";
                $class = "success";
            }

            if ($parametro == 'nothing') {
                $mensaje = "Posibles errores: <li>No intentaste actualizar ningún campo</li> <li>Este dato ya esta registrado, comunicate con soporte técnico</li> ";
                $class = "warning";
            }

            if ($parametro == 'no_cambios') {
                $mensaje = "No intentaste actualizar ningún campo";
                $class = "warning";
            }

            if ($parametro == 'union') {
                $mensaje = "Al parecer este campo de está ha sido enlazada con un campo de Catálogo de Colaboradores, ya que esta usuando esta información";
                $class = "info";
            }

            if ($parametro == "error") {
                $mensaje = "Al parecer ha ocurrido un problema";
                $class = "danger";
            }


            View::set('class', $class);
            View::set('regreso', $regreso);
            View::set('mensaje', $mensaje);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("alerta");
        }

        public function alertas($title, $array, $regreso)
        {
            $mensaje = "";
            foreach ($array as $key => $value) {
                if ($value['seccion'] == 2) {
                    $mensaje .= <<<html
            <div class="alert alert-danger" role="alert">
              <h4>El ID <b>{$value['id']}</b>, no se puede eliminar, ya que esta siendo utilizado por el Catálogo de Colaboradores</h4>
            </div>
html;
                }

                if ($value['seccion'] == 1) {
                    $mensaje .= <<<html
            <div class="alert alert-success" role="alert">
              <h4>El ID <b>{$value['id']}</b>, se ha eliminado</h4>
            </div>
html;
                }
            }
            View::set('regreso', $regreso);
            View::set('mensaje', $mensaje);
            View::set('titulo', $title);
            View::render("alertas");
        }


}
