<?PHP

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \App\models\Jobs as JobsDao;

class Jobs
{
    public function ValidaEsquema($esquema, $datos)
    {
        $res = [
            "errores" => []
        ];
        foreach ($esquema as $key => $value) {
            if (!isset($datos[$key])) {
                $res["errores"][] = "El campo " . $key . " es requerido";
                continue;
            }
            if (gettype($datos[$key]) != $value) {
                $res["errores"][] = "El campo " . $key . " debe ser de tipo " . $value;
                continue;
            }
        }

        if (count($res["errores"]) > 0) {
            echo json_encode($res);
            die();
        }
    }

    public function sp_con_array()
    {
        $pDemo = [];
        $creditos = JobsDao::CreditosAutorizados("12/04/2024");
        foreach ($creditos as $key => $credito) {
            if (empty($cliente)) continue;
            $chequera = JobsDao::GetNoChequera($credito["CDGCO"]);
            $cheque = JobsDao::GetNoCheque($chequera["CDGCB"]);

            $datos = [
                "cheque" => $cheque["CHQSIG"],
                "fexp" => $credito["FEXP"],
                "usuario" => $_SESSION["usuario"] ?? "AMGM",
                "cdgcb" => $chequera["CDGCB"],
                "cdgcl" => $credito["CDGCL"],
                "cdgns" => $credito["CDGNS"],
                "ciclo" => $credito["CICLO"]
            ];

            $pDemo[] = [
                $datos,
                // JobsDao::ActualizaPRC($datos),
                // JobsDao::ActualizaPRC($datos)
            ];
        }

        echo json_encode($pDemo);
    }
}
