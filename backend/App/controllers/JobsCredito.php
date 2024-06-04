<?PHP

namespace App\controllers;

include 'C:/xampp/htdocs/mcm/backend/App/models/JobsCredito.php';

use \App\models\JobsCredito as JobsDao;

date_default_timezone_set('America/Mexico_City');

$jobs = new JobsCredito();
$jobs->JobCheques();

class JobsCredito
{
    public function SaveLog($tdatos)
    {
        $archivo = "C:/xampp/JobsCredito_php.log";

        clearstatcache();
        if (file_exists($archivo) && filesize($archivo) > 10 * 1024 * 1024) { // 10 MB
            $nuevoNombre = "C:/xampp/Jobs_ahorro" . date('Ymd') . ".log";
            rename($archivo, $nuevoNombre);
        }

        $log = fopen($archivo, "a");

        $infoReg = date("Y-m-d H:i:s") . " - job_fnc: " . debug_backtrace()[1]['function'] . " -> " . $tdatos;

        fwrite($log, $infoReg . PHP_EOL);
        fclose($log);
    }

    public function JobCheques_OLD()
    {
        self::SaveLog("Iniciando Job Cheques");
        $resumen = [];
        $creditos = JobsDao::CreditosAutorizados();
        var_dump($creditos);

        foreach ($creditos as $key => $credito) {
            $chequera = JobsDao::GetNoChequera($credito["CDGCO"]);
            $cheque = JobsDao::GetNoCheque($chequera["CDGCB"]);

            $datos = [
                "cheque" => $cheque["CHQSIG"],
                "fexp" => $credito["FEXP"],
                "usuario" => $_SESSION["usuario"] ?? "AMGM",
                "cdgcb" => $chequera["CDGCB"],
                "cdgcl" => $credito["CDGCL"],
                "cdgns" => $credito["CDGNS"],
                "ciclo" => $credito["CICLO"],
                "cantautor" => $credito["CANTAUTOR"]
            ];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_PRC_UPDATE" => JobsDao::ActualizaPRC($datos),
                "RES_PRN_UPDATE" => JobsDao::ActualizaPRN($datos)
            ];
        }

        self::SaveLog(json_encode($resumen, JSON_PRETTY_PRINT));
        self::SaveLog("Finalizando Job Cheques");

        echo "Job Cheques finalizado";
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
                "usuario" => $_SESSION["usuario"] ?? "AMGM",
                "cdgcb" => $chequera["CDGCB"],
                "cdgcl" => $credito["CDGCL"],
                "cdgns" => $credito["CDGNS"],
                "ciclo" => $credito["CICLO"],
                "cantautor" => $credito["CANTAUTOR"],
                //Datos para nuevas querys
                "prmCDGEM" => 'EMPFIN',
                "prmCDGCLNS" => $credito["CDGNS"],
                "prmCLNS" => $credito["CDGCL"],
                "prmCICLO" => $credito["CICLO"],
                "prmINICIO" => $credito["FEXP"],
                "vINTCTE" => 0,
                "vINTERES" => 0
            ];

            // $datos["vINTCTE"] = JobsDao::GET_vINTCTE($datos)["vINTCTE"];
            // $datos["vINTERES"] = JobsDao::GET_vINTERES($datos)["vINTERES"];

            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "INCCTE" => JobsDao::GET_vINTCTE($datos)["vINTCTE"],
                "INTERES" => JobsDao::GET_vINTERES($datos)["vINTERES"],
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
