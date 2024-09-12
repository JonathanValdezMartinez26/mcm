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
    public $__cdgco_ahorro = '';
    public $__perfil = '';
    public $__ahorro = '';
    public $__hora_inicio_ahorro = '';
    public $__hora_fin_ahorro = '';

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
            $this->__cdgco_ahorro = $_SESSION['cdgco_ahorro'];
            $this->__hora_inicio_ahorro = $_SESSION['inicio'];
            $this->__hora_fin_ahorro = $_SESSION['fin'];
        }
    }

    public function GetExtraHeader($titulo, $elementos = [])
    {
        $html = <<<html
        <title>$titulo</title>
        html;

        if (!empty($elementos)) {
            foreach ($elementos as $elemento) {
                $html .= "\n" . $elemento;
            }
        }

        return $html;
    }

    public function ColumnaExcel($letra, $campo, $titulo = '', $estilo = [])
    {
        $titulo = $titulo == '' ? $campo : $titulo;

        return [
            'letra' => $letra,
            'campo' => $campo,
            'estilo' => $estilo,
            'titulo' => $titulo
        ];
    }

    public function GetEstilosExcel()
    {
        return [
            'titulo' => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ],
            'centrado' => [
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER]
            ],
            'moneda' => [
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE]
            ],
            'fecha' => [
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                'numberformat' => ['code' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY]
            ],
            'fecha_hora' => [
                'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                'numberformat' => ['code' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME]
            ],
        ];
    }
}
