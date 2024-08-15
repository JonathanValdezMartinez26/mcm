<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . "\models\JobsCredito.php";

use Core\Job;
use Jobs\models\JobsCredito as JobsDao;

class JobsCredito extends Job
{
    public function __construct()
    {
        parent::__construct("JobsCredito");
    }

    public function JobCheques()
    {
        self::SaveLog("Iniciando Job Cheques");
        $resumen = [];
        $creditos = JobsDao::CreditosAutorizados();

        foreach ($creditos as $key => $credito) {
            $chequera = JobsDao::GetNoChequera($credito["CDGCO"]);
            $cheque = JobsDao::GetNoCheque($chequera["CDGCB"]);

            $datos = [
                //Datos para actualizar PRC y PRN
                "cheque" => $cheque["CHQSIG"],
                "cdgcb" => $chequera["CDGCB"],
                "cdgcl" => $credito["CDGCL"],
                "cdgns" => $credito["CDGNS"],
                "ciclo" => $credito["CICLO"],
                "cantautor" => $credito["CANTAUTOR"],
                //Datos para MP, JP y MPC
                "prmCDGCLNS" => $credito["CDGNS"],
                "prmCICLO" => $credito["CICLO"],
                "prmINICIO" => $credito["INICIO"],
                "vINTERES" => $credito["INTERES"],
                "vCLIENTE" => $credito["CDGCL"],
            ];


            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
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

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizando Job Cheques");

        echo "Job Cheques finalizado";
    }

    public function ReInserta($archivo)
    {
        self::SaveLog("Iniciando ReInserta");
        $resumen = [];
        $creditos = json_decode(file_get_contents($archivo), true);

        if (!is_array($creditos)) $creditos = [$creditos];

        foreach ($creditos as $key => $credito) {
            $datos = [
                //Datos para MP, JP y MPC
                "prmCDGCLNS" => $credito["datos"]["prmCDGCLNS"],
                "prmCICLO" => $credito["datos"]["prmCICLO"],
                "prmINICIO" => $credito["datos"]["prmINICIO"],
                "vINTERES" => $credito["datos"]["vINTERES"],
                "vCLIENTE" => $credito["datos"]["vCLIENTE"]
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_MP_INSERT" => JobsDao::InsertarMP($datos),
                "RES_JP_INSERT" => JobsDao::InsertarJP($datos),
                "RES_MPC_INSERT" => JobsDao::InsertarMPC($datos),
            ];
        }

        self::SaveLog(json_encode($resumen, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizando ReInserta");

        echo "ReInserta finalizado";
    }
}

$jobs = new JobsCredito();
$jobs->JobCheques();
// $jobs->ReInserta("C:\Users\Alberto\Desktop\prueba.json");
