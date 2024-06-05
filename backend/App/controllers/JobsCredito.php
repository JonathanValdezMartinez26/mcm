<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\CajaAhorro as CajaAhorroDao;
use \App\models\JobsCredito as JobsDao;
use DateTime;

//use \App\models\JobsCredito as JobsDao;

date_default_timezone_set('America/Mexico_City');

$jobs = new JobsCredito();
$jobs->JobCheques();

class JobsCredito
{
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/Jobs_php.log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = "C:/xampp/Jobs_php_" . date('Ymd') . ".log";
            rename($archivo, $nuevoNombre);
        }

        $log = fopen($archivo, "a");

        $infoReg = date("Y-m-d H:i:s") . " - job_fnc: " . debug_backtrace()[1]['function'] . " -> " . $tdatos;

        fwrite($log, $infoReg . PHP_EOL);
        fclose($log);
    }

    public function JobCheques()
    {
        self::SaveLog("Iniciando Job Cheques");
        $resumen = [];
        $creditos = JobsDao::CreditosAutorizados();
        var_dump($creditos);

        foreach ($creditos as $key => $credito) {
            $chequera = JobsDao::GetNoChequera($credito["CDGCO"]);
            $cheque = JobsDao::GetNoCheque($chequera["CDGCB"]);

            $datos = [
                //Datos para actualizar PRC y PRN
                "cheque" => $cheque["CHQSIG"],
                "fexp" => $credito["FEXP"],
                "usuario" => "AMGM",
                "cdgcb" => $chequera["CDGCB"],
                "cdgcl" => $credito["CDGCL"],
                "cdgns" => $credito["CDGNS"],
                "ciclo" => $credito["CICLO"],
                "cantautor" => $credito["CANTAUTOR"],
                //Datos para nuevas querys

                "prmCDGCLNS" => $credito["CDGNS"],
                "prmCICLO" => $credito["CICLO"],
                "prmINICIO" => $credito["FEXP"],
                "vINTERES" => $credito["INTERES"]
            ];


            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "INTCTE" => JobsDao::GET_vINTCTE($datos)["vINTCTE"],
                "RES_PRC_UPDATE" => JobsDao::ActualizaPRC($datos),
                "RES_PRN_UPDATE" => JobsDao::ActualizaPRN($datos),
                "RES_MPC_DELETE" => JobsDao::LimpiarMPC($datos),
                "RES_JP_DELETE" => JobsDao::LimpiarJP($datos),
                "RES_MP_DELETE" => JobsDao::LimpiarMP($datos),
                "RES_MP_INSERT" => JobsDao::InsertarMP($datos),
                "RES_JP_INSERT" => JobsDao::InsertarJP($datos),
                "RES_MPC_INSERT" => JobsDao::InsertarMPC($datos),
            ];
        }

        self::SaveLog(json_encode($resumen, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizando Job Cheques");

        echo "Job Cheques finalizado";
    }
}
