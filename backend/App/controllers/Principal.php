<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;

class Principal extends Controller
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
        $extraHeader = <<<HTML
        <title>Principal MCM</title>
        <link rel="shortcut icon" href="/img/logo.png">
        HTML;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::render("principal");
    }
}
