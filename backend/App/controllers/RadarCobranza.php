<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\RadarCobranza as RadarCobranzaDao;

class RadarCobranza extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function DashboardDia()
    {
        $extraFooter = <<<HTML
            <script>
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Dashboard Día")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('rc_dashboard_dia');
    }
}
