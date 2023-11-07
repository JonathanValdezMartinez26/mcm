<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Pagos AS PagosDao;
use \App\models\Operaciones AS OperacionesDao;

class ApiCondusef extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());

    }

    public function UploadFile()
    {
        $extraHeader = <<<html
        <title>Consulta de Pagos Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      </script>
html;

        View::render("upload_file_api_condusef");
    }

    public function GetMyTickets()
    {
        $extraHeader = <<<html
        <title>Consulta de Pagos Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      </script>
html;

        View::render("upload_file_api_condusef");
    }

    public function StatusTicket()
    {
        $extraHeader = <<<html
        <title>Consulta de Pagos Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      </script>
html;

        View::render("upload_file_api_condusef");
    }


}
