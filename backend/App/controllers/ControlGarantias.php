<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Creditos AS CreditosDao;

class ControlGarantias extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function index() {
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){
            ///
            
        });
      </script>
html;

      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("controlgarantias_all");
    }

    public function Busqueda() {


    }

}
