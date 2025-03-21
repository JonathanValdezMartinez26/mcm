<?php

namespace Core;

use DateTime;
use DateTimeZone;

class Job
{
    private $logPath;
    private $nombreJob;

    public function __construct($nj)
    {
        $validaHV = new DateTime("now", new DateTimeZone("America/Mexico_City"));
        if ($validaHV->format("I")) date_default_timezone_set("America/Mazatlan");
        else date_default_timezone_set("America/Mexico_City");

        $this->logPath = dirname(__DIR__) . "/Jobs/Logs/";
        $this->nombreJob = $nj;

        $this->ValidaPathLog($this->logPath);
    }

    public function ValidaPathLog($path)
    {
        if (!file_exists($path)) mkdir($path, 0777, true);
    }

    public function SaveLog($tdatos)
    {
        $archivo = $this->logPath . $this->nombreJob . ".log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = $this->logPath  . $this->nombreJob . date("Ymd") . ".log";
            rename($archivo, $nuevoNombre);
        }

        $log = fopen($archivo, "a");

        $infoReg = date("Y-m-d H:i:s") . ": " . debug_backtrace()[1]["function"] . " -> " . $tdatos;

        fwrite($log, $infoReg . PHP_EOL);
        fclose($log);
    }

    public function ReadLog($metodoSolicitado)
    {
        $resultados = [];

        if (!file_exists($this->logPath)) throw new \Exception("No se encontró el archivo de log");

        $archivo = fopen($this->logPath, 'r');
        while (($linea = fgets($archivo)) !== false) {
            if (preg_match('/^(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2}): ([^ ]+) -> (.+)$/', $linea, $matches)) {
                list(, $fecha, $hora, $metodo, $informacion) = $matches;
                $json = $this->esJson($informacion) ? json_decode($informacion, true) : null;

                if ($metodo === $metodoSolicitado) {
                    $resultados[] = [
                        'fecha' => $fecha,
                        'hora' => $hora,
                        'metodo' => $metodo,
                        'resultado' => $json,
                    ];
                }
            }
        }
        fclose($archivo);

        return $resultados;
    }

    private function esJson($texto)
    {
        json_decode($texto);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public function GetDestinatarios($respuestas, $destinatarios = [])
    {
        $respuestas = array_key_exists('success', $respuestas) ? [$respuestas] : $respuestas;

        foreach ($respuestas as $respuesta) {
            if ($respuesta['success'] && count($respuesta['datos']) > 0) {
                $destinatarios = array_merge($destinatarios, array_map(function ($d) {
                    return $d['CORREO'];
                }, $respuesta['datos']));
            }
        }

        sort($destinatarios);
        return array_unique($destinatarios);
    }
}
