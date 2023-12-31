<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\models\General AS GeneralDao;
use \Core\Controller;

require_once dirname(__DIR__).'/../public/librerias/mpdf/mpdf.php';
require_once dirname(__DIR__).'/../public/librerias/phpexcel/Classes/PHPExcel.php';



class Contenedor extends Controller{


    function __construct(){
      parent::__construct();
    }

    public function getUsuario(){
      return $this->__usuario;
    }

    public function header($extra = ''){
        date_default_timezone_set('America/Mexico_City');
        $usuario = $this->__usuario;
        $nombre = $this->__nombre;
        $sucursal = $this->__cdgco;
        $perfil = $this->__perfil;

        //var_dump($this->__perfil);


     $header =<<<html

        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta http-equiv="Expires" content="0">
            <meta http-equiv="Last-Modified" content="0">
            <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
            <meta http-equiv="Pragma" content="no-cache">

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta charset="utf-8">
            
            <link href="/css/nprogress.css" rel="stylesheet">
            <link href="/css/loader.css" rel="stylesheet">
            <link rel="stylesheet" href="/css/tabla/sb-admin-2.css">
            <link rel="stylesheet" href="/css/bootstrap/datatables.bootstrap.css">
            <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
            <link rel="stylesheet" href="/css/bootstrap/bootstrap-switch.css">
            <link rel="stylesheet" href="/css/validate/screen.css">

            <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">
          	<link href="/css/font-awesome.min.css" rel="stylesheet">
            <link href="/css/menu/menu5custom.min.css" rel="stylesheet">
            <link href="/css/green.css" rel="stylesheet">
            <link href="/css/custom.min.css" rel="stylesheet">

            <link href="/librerias/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="/librerias/vintage_flip_clock/jquery.flipcountdown.css" />
        </head>
html;
$menu =<<<html
<body class="nav-md" >
  <div class="container body" >
    <div class="main_container" style="background: #ffffff">

      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
          <div class="navbar nav_title" style="border: 0;"> 
            <a href="/Principal/" class="site_title"><i class="fa fa-home"></i> <span>MCM</span></a>
          </div>
          <div class="clearfix"></div>
          <div class="profile clearfix">
            <div class="profile_pic">
              <img src="https://static.vecteezy.com/system/resources/previews/013/042/571/large_2x/default-avatar-profile-icon-social-media-user-photo-in-flat-style-vector.jpg" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
              <span>USUARIO: {$usuario}</span>
              <br>
              <span class="fa fa-key">: {$perfil}</span>
html;
$menu.=<<<html
            </div>
          </div>
          <br/>
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
              <h3>General </h3>
              <ul class="nav side-menu">       
html;
        if($this->__perfil== 'ADMIN' || $this->__perfil== 'CAJA' || $this->__perfil== 'GTOCA' || $this->__perfil== 'AMOCA' || $this->__perfil== 'OCOF' || $this->__perfil== 'CPAGO' || $this->__perfil== 'ACALL' || $this->__perfil== 'LAYOU') {

            $menu .= <<<html
                <li><a><i class="glyphicon	glyphicon glyphicon-usd"> </i>&nbsp; Pagos <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
html;
        }
        if($this->__perfil== 'ADMIN')
        {
            $menu.=<<<html
            <li><a href="/Pagos/">Administración Pagos</a></li>
            <li><a href="/Pagos/CorteEjecutivo/">Recepción Pagos App</a></li> 
            <li><a href="/Pagos/CorteEjecutivoReimprimir/">Reimprimir Recibos App</a></li> 
html;
        }
        if($this->__perfil== 'ADMIN' || $this->__perfil== 'ACALL' || $this->__perfil== 'LAYOU')
        {
            $menu.=<<<html
            <li><a href="/Pagos/Layout/">Layout Contable</a></li> 
html;
        }

        if($this->__perfil== 'ADMIN' || $this->__perfil == 'CAJA' || $this->__usuario == 'LGFR' || $this->__usuario == 'PLMV' || $this->__usuario == 'PMAB' || $this->__usuario == 'MGJC'
            || $this->__usuario== 'AVGA' //USUARIO DE ANGELES - TOLUCA
            || $this->__usuario== 'FLCR' //USUARIO DE REBECA - VILLA VICTORIA
            || $this->__usuario== 'COCS' //USUARIO DE SELENE - ESTADO DE MEXICO
            || $this->__usuario== 'GOIY' //USUARIO DE SELENE - Huamantla, Santa Ana, Apizaco y Tlaxcala
            || $this->__usuario== 'DAGC' //DANIELA
            || $this->__usuario== 'COVG' //USUARIO GABRIELA VELAZQUEZ

        )
        {
            $menu.=<<<html
                    <!-- <li><a href="/Pagos/CorteCaja/">Corte Caja Pagos</a></li>-->
                   <li><a href="/Pagos/PagosRegistro/">Registro de Pagos Caja</a></li>
html;
        }

        if($this->__perfil == 'ACALL')
        {
            $menu.=<<<html
                    <!-- <li><a href="/Pagos/CorteCaja/">Corte Caja Pagos</a></li>-->
                   <li><a href="/Pagos/PagosConsultaUsuarios/">Consulta de Pagos Cliente</a></li>
html;
        }

        if($this->__perfil== 'ADMIN' || $this->__perfil == 'CAJA' || $this->__perfil== 'GTOCA' || $this->__perfil== 'AMOCA' || $this->__perfil== 'OCOF' || $this->__perfil== 'CPAGO' || $this->__perfil == 'ACALL' )
        {
            $menu.=<<<html
                    <!-- <li><a href="/Pagos/CorteCaja/">Corte Caja Pagos</a></li>-->
                   <li><a href="/Pagos/PagosConsulta/">Consultar Pagos</a></li>
html;
        }
        $menu.=<<<html
                
                  </ul>
                </li>
html;

        if($this->__perfil== 'ADMIN' || $this->__perfil== 'GARAN' || $this->__perfil== 'CAMAG') {
            $menu .= <<<html
                <li><a><i class="fa fa-users"> </i>&nbsp; Creditos <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
html;
        }
        if($this->__perfil== 'ADMIN' || $this->__perfil== 'GARAN') {
            $menu .= <<<html
                 
                   <li><a href="/Creditos/ControlGarantias/">Control de Garantías</a></li>
html;
        }
            if($this->__perfil== 'ADMIN' ) {
                $menu .= <<<html
                   <li><a href="/Creditos/ActualizaCredito/">Actualización de Créditos</a></li>
                   <li><a href="/Devengo/">Devengo Crédito</a></li>
html;
       }
            if($this->__perfil== 'ADMIN' || $this->__perfil== 'CAMAG') {
                $menu .= <<<html
                   <li><a href="/Creditos/CambioSucursal/">Cambio de Sucursal</a></li>
html;
            }
               $menu .= <<<html
                  </ul>
                </li>
html;

        if($this->__perfil == 'ADMIN' || $this->__perfil == 'CALLC'  || $this->__perfil == 'ACALL') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-phone-alt"> </i>&nbsp; Call Center <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
html;
        }
        $fechaActual = date('Y-m-d');
        if($this->__perfil == 'ADMIN' || $this->__perfil == 'ACALL' || $this->__usuario == 'ESMM') {
            $menu .= <<<html
                    <li><a href="/CallCenter/Administracion/">Asignar Sucursales</a></li>
                    <li><a href="/CallCenter/Prorroga/">Solicitudes de Prorroga</a></li>
                    <li><a href="/CallCenter/Reactivar/">Reactivar Solicitudes</a></li>
                    <li><a href="/CallCenter/Busqueda/">Búsqueda Rápida</a></li>
html;
        }
        if($this->__perfil == 'ADMIN' || $this->__perfil == 'CALLC' || $this->__perfil == 'ACALL') {
            if($this->__perfil == 'ADMIN')
            {
                $titulo = "(Analistas)";
            }
            else{
                $mis = 'Mis';
                if($this->__usuario == 'ESMM')
                {
                    $opcion = '<li><a href="/CallCenter/HistoricoAnalistas/">Histórico Analistas</a></li>';
                }

                $opcion .= '<li><a href="/CallCenter/Global/">Todos los Pendientes</a></li>';


            }
            $menu .= <<<html
                   <li><a href="/CallCenter/Pendientes/">$mis Pendientes $titulo</a></li>
                   <li><a href="/CallCenter/Historico/">$mis Historicos $titulo</a></li>
                   $opcion
                  </ul>
                </li>
              </ul>
html;
        }
if($this->__perfil == 'ADMIN' || $this->__usuario == 'PLD') {
    $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-th-list	
"> </i>&nbsp; Operaciones <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                   <li><a href="/Operaciones/ReportePLDDesembolsos/">PLD Reporte Desembolsos</a></li>
                   <li><a href="/Operaciones/ReportePLDPagos/">PLD Reporte Pagos</a></li>
                   <li><a href="/Operaciones/ReportePLDPagosNacimiento/">PLD R. Pagos Edad</a></li>
                   <li><a href="/Operaciones/IdentificacionClientes/">Identificación (Clientes)</a></li>
                   <li><a href="/Operaciones/CuentasRelacionadas/">Cuentas Relacionadas</a></li>
                   <li><a href="/Operaciones/PerfilTransaccional/">Perfil Transaccional</a></li>
                   <li><a href="/Operaciones/UDIS_DOLAR/">Cargar UDIS y DOLAR</a></li>
                  </ul>
                </li>
              </ul>
html;
}

        if($this->__perfil == 'ADMIN' || $this->__usuario == 'PLD') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon glyphicon-globe"> 
                </i>&nbsp;Api Condusef<span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                   <li><a href="/ApiCondusef/UploadFile/">Subir Archivo</a></li>
                   <li><a href="/ApiCondusef/GetMyTickets/">Obtener Tickets</a></li>
                   <li><a href="/ApiCondusef/StatusTicket/">Status de Tickets</a></li>
                   <li><a href="/ApiCondusef/StatusTicket/">Corregir un documento</a></li>
                   <li><a href="/ApiCondusef/StatusTicket/">Eliminar un documento</a></li>
                  </ul>
                </li>
              </ul>
html;
        }

        if($this->__perfil == 'ADMIN') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-cog	
"> </i>&nbsp; Administrar Caja <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                   <li><a href="/Pagos/AjusteHoraCierre/">Ajustar Hora de Cierre</a></li>
                   <li><a href="/Pagos/DiasFestivos/">Asignación Días Festivos</a></li>
                  </ul>
                </li>
              </ul>
html;
        }
            $menu.=<<<html
              </div>
          </div>
        </div>
      </div>

      <div class="top_nav">
        <div class="nav_menu">
          <nav>
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class=" fa fa-user"></span> {$nombre}
                    <span class=" fa fa-angle-down"></span>
                  </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                 
                  <li><a href="/Login/cerrarSession"><i class="fa fa-sign-out pull-right"></i>Cerrar Sesión</a></li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </div>

    </div>

html;

    return $header.$extra.$menu;
    }

    public function footer($extra = ''){
        $footer =<<<html
             <footer>
              <div class="pull-right">
                <!--a href="#">AG Alimentos de Granja</a-->
              </div>
            mcm
              
            </footer>
            <!-- /footer content -->
          </div>
          
        <script src="/js/moment/moment.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <script src="/js/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/bootstrap/bootstrap-switch.js"></script>
        <script src="/js/nprogress.js"></script>
        <!-- Custom Theme Scripts -->
        <script src="/js/custom.min.js"></script>

        <script src="/js/validate/jquery.validate.js"></script>
        <script src="/js/login.js"></script>

        <script src="/js/tabla/jquery.dataTables.min.js"></script>
        <script src="/js/tabla/dataTables.bootstrap.min.js"></script>
        <script src="/js/tabla/jquery.tablesorter.js"></script>

        <!-- EXTENCIONES DE DATATABLE() PARA EXPORTAR  -->
        <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js" ></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js" ></script>

        <script src="/librerias/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script type="text/javascript" src="/librerias/vintage_flip_clock/jquery.flipcountdown.js"></script>
       <script>
       
       function ponerElCursorAlFinal(id)
        {
            var el = document.getElementById(id);
            el.selectionStart=el.selectionEnd=el.value.length;
            el.focus();
        }
        
        function Delete_Garantias(secuencia) {
            credito = getParameterByName('Credito');
            secuencias = secuencia;
    
            alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                if(response){
    
                    $.ajax({
                        type: "POST",
                        url: "/Creditos/DeleteGarantia/",
                        data: {"credito" : credito, "secuencia" : secuencias},
                        success: function(response){
                            //alert(response);
                            if(response != '0')
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
                        success: function(response){
                            //alert(response);
                            if(response != '0')
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
    
        function check(e) {
            tecla = (document.all) ? e.keyCode : e.which;
    
            //Tecla de retroceso para borrar, siempre la permite
            if (tecla == 8) {
                return true;
            }
    
            // Patrón de entrada, en este caso solo acepta numeros y letras
            patron = /[A-Za-z]/;
            tecla_final = String.fromCharCode(tecla);
            return patron.test(tecla_final);
        }
    
        function check_t(e) {
            tecla = (document.all) ? e.keyCode : e.which;
    
            //Tecla de retroceso para borrar, siempre la permite
            if (tecla == 8) {
                return true;
            }
    
            // Patrón de entrada, en este caso solo acepta numeros y letras
            patron = /[A-Za-z0-9]/;
            tecla_final = String.fromCharCode(tecla);
            return patron.test(tecla_final);
        }
    
        function mayus(e) {
            e.value = e.value.toUpperCase();
        }
        
        $(window).load(function() {
    $(".loader").fadeOut("slow");
});
</script>
  </body>
</html>

html;

    return $footer.$extra;
    }

}
