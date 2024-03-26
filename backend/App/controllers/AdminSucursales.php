<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\CajaAhorro as CajaAhorroDao;
use \App\models\Ahorro as AhorroDao;

class AdminSucursales extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
        
    }

    ///////////////////////////////////////////////////
    public function SaldosDiarios()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    public function SolicitudesRetiroPeriodo()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    public function SolicitudesRetiroInmediato()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_solicitudes_ret_inm");
    }


    public function SolicitudesReimpresionTicket()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    public function SolicitudesIncidencias()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }


    public function ClientesAhorro()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_clientes_ahorro");
    }

    public function ClientesInversiones()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    public function ClientesPeques()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_saldos_dia");
    }

    public function LogTransaccional()
    {
        $extraHeader = <<<html
        <title>Saldos Sucursales</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
       
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("caja_admin_log");
    }



}
