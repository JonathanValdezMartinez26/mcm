<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\Controller;

require_once dirname(__DIR__) . '/libs/mpdf/mpdf.php';
require_once dirname(__DIR__) . '/libs/phpexcel/Classes/PHPExcel.php';

class Contenedor extends Controller
{


    function __construct()
    {
        parent::__construct();
    }

    public function getUsuario()
    {
        return $this->__usuario;
    }

    public function header($extra = '')
    {
        $usuario = $this->__usuario;
        $nombre = $this->__nombre;
        $sucursal = $this->__cdgco;
        $perfil = $this->__perfil;
        $permiso_ahorro = $this->__ahorro;

        $header = <<<html
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta http-equiv="Expires" content="0">
            <meta http-equiv="Last-Modified" content="0">
            <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
            <meta http-equiv="Pragma" content="no-cache">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta charset="utf-8">
            
            <link rel="shortcut icon" href="/img/logo_ico.png">
            <link rel="stylesheet" type="text/css" href="/css/nprogress.css">
            <link rel="stylesheet" type="text/css" href="/css/loader.css">
            <link rel="stylesheet" type="text/css" href="/css/tabla/sb-admin-2.css">
            <link rel="stylesheet" type="text/css" href="/css/bootstrap/datatables.bootstrap.css">
            <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.css">
            <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap-switch.css">
            <link rel="stylesheet" type="text/css" href="/css/validate/screen.css">
            <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
            <link rel="stylesheet" type="text/css" href="/css/menu/menu5custom.min.css">
            <link rel="stylesheet" type="text/css" href="/css/green.css">
            <link rel="stylesheet" type="text/css" href="/css/custom.min.css">
            $extra
        </head>
        <body class="nav-md">
            <div class="container body" >
                <div class="main_container" style="background: #ffffff">
                    <div class="col-md-3 left_col">
                        <div class="left_col scroll-view">
                            <div class="navbar nav_title" style="border: 0;"> 
                                <a href="/Principal/" class="site_title" style="display: flex; align-items: center; justify-content: center; padding: 0; margin: 0;">
                                    <img src="/img/logo_ico.png" alt="Inicio" width="50px" id="ico_home" style="display: none;">
                                    <img src="/img/logo_nombre.png" alt="Inicio" width="210px" id="img_home">
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="profile clearfix">
                                <div class="profile_pic">
                                    <img src="/img/profile_default.jpg" alt="..." class="img-circle profile_img">
                                </div>
                                <div class="profile_info">
                                    <span><b>USUARIO: </b>{$usuario}</span>
                                    <br>
                                    <span><b>PERFIL: </b><span class="fa fa-key"></span>{$perfil}</span>
                                </div>
                            </div>
                            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                                <div class="menu_section">
html;

        $menu = "";

        if ($permiso_ahorro == '1' || $this->__usuario == 'LGFR' || $this->__usuario == 'PAES' || $this->__usuario == 'PMAB' || $this->__usuario == 'AMGM' || $this->__usuario == 'CAMG' || $this->__usuario == 'GUGJ' || $this->__usuario == 'JUSA' || $this->__usuario == 'HEDC' || $this->__usuario == 'PHEE') {
            $menu .= <<<html
           <hr>
           <h3>General WEB AHORRO</h3>
            <ul class="nav side-menu">     
html;
        }

        if ($permiso_ahorro == '1' || $this->__usuario == 'AMGM') {
            $menu .= <<<html
                <li><a href="/Ahorro/CuentaCorriente/"><i class="glyphicon glyphicon-usd"> </i>&nbsp; Mi espacio </a> </li>   
html;
        }

        if ($this->__usuario == 'AMGM' || $this->__usuario == 'LGFR' || $this->__usuario == 'PAES' || $this->__usuario == 'PMAB' || $this->__usuario == 'GUGJ' || $this->__usuario == 'JUSA' || $this->__usuario == 'HEDC' || $this->__usuario == 'PHEE') {
            $menu .= <<<html
                <li><a href="/AdminSucursales/SaldosDiarios/"><i class="glyphicon glyphicon-paste"> </i>&nbsp; Admin Sucursales </a> </li>
             </ul>
html;
        }

        $menu .= <<<html
              <hr>
              <h3>GENERAL </h3>
              <ul class="nav side-menu">       
html;

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'CAJA' || $this->__perfil == 'GTOCA' || $this->__perfil == 'AMOCA' || $this->__perfil == 'OCOF' || $this->__perfil == 'CPAGO' || $this->__perfil == 'ACALL' || $this->__perfil == 'LAYOU' || $this->__usuario == 'TESP' || $this->__usuario == 'MGJC') {
            $menu .= <<<html
                <li><a><i class="glyphicon	glyphicon glyphicon-usd"> </i>&nbsp; Pagos <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'LGFR' || $this->__usuario == 'MGJC') {
            $menu .= <<<html
            <li><a href="/Pagos/">Administración Pagos</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN') {
            $menu .= <<<html
            <li><a href="/Pagos/CorteEjecutivo/">Recepción Pagos App</a></li> 
            <li><a href="/Pagos/CorteEjecutivoReimprimir/">Reimprimir Recibos App</a></li> 
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'ACALL' || $this->__perfil == 'LAYOU') {
            $menu .= <<<html
            <li><a href="/Pagos/Layout/">Layout Contable</a></li> 
html;
        }

        if (
            $this->__perfil == 'ADMIN' || $this->__perfil == 'CAJA' || $this->__usuario == 'LGFR'
            || $this->__usuario == 'PLMV'
            || $this->__usuario == 'PMAB' || $this->__usuario == 'MGJC'
            || $this->__usuario == 'AVGA' //USUARIO DE ANGELES - TOLUCA
            || $this->__usuario == 'FLCR' //USUARIO DE REBECA - VILLA VICTORIA
            || $this->__usuario == 'COCS' //USUARIO DE SELENE - ESTADO DE MEXICO
            || $this->__usuario == 'GOIY' //USUARIO DE SELENE - Huamantla, Santa Ana, Apizaco y Tlaxcala
            || $this->__usuario == 'DAGC' //DANIELA
            || $this->__usuario == 'COVG' //USUARIO GABRIELA VELAZQUEZ
			|| $this->__usuario == 'CAMG' //USUARIO REMPLAZO IRENE PUEBLA
		
            || $this->__usuario == 'TESP'
        ) {
            $menu .= <<<html
                   <li><a href="/Pagos/PagosRegistro/">Registro de Pagos Caja</a></li>
html;
        }

        if ($this->__perfil == 'ACALL') {
            $menu .= <<<html
                   <li><a href="/Pagos/PagosConsultaUsuarios/">Consulta de Pagos Cliente</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'CAJA' || $this->__perfil == 'GTOCA' || $this->__perfil == 'AMOCA' || $this->__perfil == 'OCOF' || $this->__perfil == 'CPAGO' || $this->__perfil == 'ACALL') {
            $menu .= <<<html
                   <li><a href="/Pagos/PagosConsulta/">Consultar Pagos</a></li>
html;
        }

        $menu .= <<<html
                  </ul>
                </li>
html;

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'GARAN' || $this->__perfil == 'CAMAG') {
            $menu .= <<<html
                <li><a><i class="fa fa-users"> </i>&nbsp; Creditos <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'GARAN') {
            $menu .= <<<html
                 
                   <li><a href="/Creditos/ControlGarantias/">Control de Garantías</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN') {
            $menu .= <<<html
        <li><a href="/Promociones/Telarana/">Calculo Descuento Telaraña</a></li>
        <li><a href="/Validaciones/RegistroTelarana/">Registro Telaraña</a></li>
        <li><a href="/Creditos/ActualizaCredito/">Actualización de Créditos</a></li>
        <li><a href="/Devengo/">Devengo Crédito</a></li>
       
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'CAMAG') {
            $menu .= <<<html
                   <li><a href="/Creditos/CambioSucursal/">Cambio de Sucursal</a></li>
html;
        }

        $menu .= <<<html
                  </ul>
                </li>
html;

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'CALLC'  || $this->__perfil == 'ACALL') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-phone-alt"> </i>&nbsp; Call Center <span class="fa fa-chevron-down"></span></a>
                 <ul class="nav child_menu">
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'ACALL' || $this->__usuario == 'ESMM' || $this->__usuario == 'HSEJ') {
            $menu .= <<<html
                    <li><a href="/CallCenter/Administracion/">Asignar Sucursales</a></li>
                    <li><a href="/CallCenter/Prorroga/">Solicitudes de Prorroga</a></li>
                    <li><a href="/CallCenter/Reactivar/">Reactivar Solicitudes</a></li>
                    <li><a href="/CallCenter/Busqueda/">Búsqueda Rápida</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__perfil == 'CALLC' || $this->__perfil == 'ACALL' || $this->__usuario == 'HSEJ') {
            if ($this->__perfil == 'ADMIN' || $this->__usuario == 'HSEJ') {
                $titulo = "(Analistas)";
            } else {
                $mis = 'Mis';
                if ($this->__usuario == 'ESMM' || $this->__usuario == 'MAPH') {
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

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'PLD') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-th-list"> </i>&nbsp; Operaciones <span class="fa fa-chevron-down"></span></a>
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

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'PLD') {
            $menu .= <<<html
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon glyphicon-globe"> 
                </i>&nbsp;Api Condusef<span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                   <li><a href="/ApiCondusef/AddRedeco/">Registrar Quejas REDECO</a></li>
                     <li><a href="/ApiCondusef/AddReune/">Registrar Quejas REUNE</a></li>
                  </ul>
                </li>
              </ul>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'PLMV' || $this->__usuario == 'MCDP' || $this->__usuario == 'LGFR') {
            $menu .= <<<html
        <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon glyphicon-globe"> 
                </i>&nbsp;Cultiva<span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                        <li><a href="/Cultiva/">Consulta Clientes Solicitudes</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN'  || $this->__usuario == 'MCDP') {
            $menu .= <<<html
                        <li><a href="/Cultiva/ReingresarClientesCredito/">Reingresar Clientes a Grupo</a></li>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'PLMV' || $this->__usuario == 'MCDP') {
            $menu .= <<<html
                  </ul>
                </li>
        </ul>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'PLMV' || $this->__usuario == 'PHEE') {
            $menu .= <<<html
        <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-cog"> 
                </i>&nbsp;Incidencias MCM<span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                  <li><a href="/Incidencias/AutorizaRechazaSolicitud/">Error Autorizar y/o Rechazar Solicitud</a></li>
                  <li><a href="/Incidencias/CalculoDevengo/">Calculo de Devengos</a></li>
                  <li><a href="/Incidencias/CancelarRefinanciamiento/">Cancelar Refinanciamiento</a></li>
                  <li><a href="/Incidencias/ActualizarFechaPagosNoConciliados/">Cambio de Fecha para Pagos No conciliados del día</a></li>
                  <li><a href="/Incidencias/ActualizarFechaPagosNoConciliados/">Telaraña agregar referencias</a></li>
                  </ul>
                </li>
        </ul>
html;
        }

        if ($this->__perfil == 'ADMIN' || $this->__usuario == 'MAPH' || $this->__usuario == 'HSEJ' || $this->__usuario == 'ORHM' || $this->__usuario == 'LGFR') {
            $menu .= <<<html
              <ul class="nav side-menu">
html;
            if ($this->__perfil == 'ADMIN' || $this->__usuario == 'LGFR') {
                $menu .= <<<html
                <li><a><i class="glyphicon glyphicon glyphicon-cog"> </i>&nbsp; Administrar Caja <span class="fa fa-chevron-down"></span></a>
html;
            } else {
                $menu .= <<<html
                <li><a><i class="glyphicon glyphicon glyphicon-cog"> </i>&nbsp; Usuarios SICAFIN <span class="fa fa-chevron-down"></span></a>
html;
            }

            $menu .= <<<html
                  <ul class="nav child_menu">
html;

            if ($this->__perfil == 'ADMIN' || $this->__usuario == 'LGFR') {
                $menu .= <<<html
                   <li><a href="/Pagos/AjusteHoraCierre/">Ajustar Hora de Cierre</a></li>
                   <li><a href="/Pagos/DiasFestivos/">Asignación Días Festivos</a></li>
html;
            }

            if ($this->__perfil == 'ADMIN' || $this->__usuario == 'MAPH' || $this->__usuario == 'HSEJ' || $this->__usuario == 'PHEE' || $this->__usuario == 'ORHM') {
                $menu .= <<<html
                    <li><a href="/Reportes/UsuariosMCM/">Reporte Usuarios SICAFIN MCM</a></li>
                    <li><a href="/Reportes/UsuariosCultiva/">Reporte Usuarios SICAFIN Cultiva</a></li>
                     <li><a href="/Creditos/cierreDiario">Situación Cartera</a></li>
html;
            }

            $menu .= <<<html
                  </ul>
                </li>
              </ul>
html;
        }

        $menu .= <<<html
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

        return $header . $menu;
    }

    public function footer($extra = '')
    {
        $footer = <<<HTML
            </div>
            <script src="/js/moment/moment.min.js"></script>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script src="/js/jquery.min.js"></script>
            <script src="/js/bootstrap.min.js"></script>
            <script src="/js/bootstrap/bootstrap-switch.js"></script>
            <script src="/js/nprogress.js"></script>
            <script src="/js/custom.min.js"></script>
            <script src="/js/validate/jquery.validate.js"></script>
            <script src="/js/login.js"></script>
            <script src="/js/tabla/jquery.dataTables.min.js"></script>
            <script src="/js/tabla/dataTables.bootstrap.min.js"></script>
            <script src="/js/tabla/jquery.tablesorter.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js" ></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" ></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js" ></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js" ></script>
            <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js" ></script>
            $extra
        </body>
        </html>
        HTML;
        return $footer;
    }
}
