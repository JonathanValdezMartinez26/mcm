<?php

namespace Core;

defined("APPPATH") or die("Access denied");

use \App\models\General as GeneralDao;

class Controller
{

    public $__usuario = '';
    public $__nombre = '';
    public $__puesto = '';
    public $__cdgco = '';
    public $__perfil = '';

    public function __construct()
    {
        session_start();
        if ($_SESSION['usuario'] == '' || empty($_SESSION['usuario'])) {
            unset($_SESSION);
            session_unset();
            session_destroy();
            header("Location: /Login/");
            exit();
        } else {
            $this->__usuario = $_SESSION['usuario'];
            $this->__nombre = $_SESSION['nombre'];
            $this->__puesto = $_SESSION['puesto'];
            $this->__cdgco = $_SESSION['cdgco'];
            $this->__perfil = $_SESSION['perfil'];
            $this->__ahorro = $_SESSION['ahorro'];
        }
    }

    public function GetExtraHeader($titulo, $elementos = [])
    {
        if (empty($elementos)) {
            return <<<html
            <title>$titulo</title>
            <link rel="shortcut icon" href="/img/logo.png">
            html;
        } else {
            $html = <<<html
            <title>$titulo</title>
            <link rel="shortcut icon" href="/img/logo.png">
            html;
            foreach ($elementos as $elemento) {
                $html .= "\n" . $elemento;
            }
            return $html;
        }
    }
}
