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
        $cliente = [];
        $cheque = [];
        $creditos = JobsDao::CreditosAutorizados("11/04/2024");
        foreach ($creditos as $key => $credito) {
            $cliente[] = JobsDao::ClientesAutorizados($credito["CDGNS"], $credito["CICLO"]);

            if (empty($cliente)) continue;
            $chequera = JobsDao::GetNoChequera($creditos["CDGCO"]);
            $cheque[] = JobsDao::GetNoCheque($chequera["CDGCB"]);

            $parametros = [];
            $parametros[":PRMCDGEM"] = "EMPFIN";
            $parametros[":PRMCDGCLNS"] = $credito['CDGNS'];
            $parametros[":PRMCLNS"] = 'G';
            $parametros[":PRMCICLO"] = $credito['CICLO'];
            $parametros[":PRMT_CDGCL"] = [$cliente];
            $parametros[":PRMT_NOCHEQUE"] = [$cheque];
            $parametros[":PRMFECHA"] = $credito['INICIO'];
            $parametros[":PRMUSER"] = $_SESSION['USUARIO'] ?? 'AMGM';
            $parametros[":PRMCDGCB"] = $chequera["CDGCB"];
            $parametros[":VMENSAJE"] = "__RETURN__";

            $pDemo[] = [$parametros, JobsDao::sp_con_array($parametros)];
            $creditos[$key]["CHEQUERA"] = $chequera["CDGCB"];
            $creditos[$key]["CHEQUE"] = $cheque["CHQSIG"];
        }
        // CALL ESIACOM.PKG_SPS_CON_ARRAY.SP_INS_CHEQUES_CTE(:PRMCDGEM, :PRMCDGCLNS, :PRMCLNS, :PRMCICLO, :PRMT_CDGCL, :PRMT_NOCHEQUE, :PRMFECHA, :PRMUSER, :PRMCDGCB, ?)
        // CALL ESIACOM.PKG_SPS_CON_ARRAY.SP_INS_CHEQUES_CTE('EMPFIN', '010407', 'G', '09', T_CDGCL('112690''), T_NOCHEQUE('4568''), '2024-04-11 00:00:00.000', 'AMGM', '28', ?)
        echo json_encode($pDemo);
        die();
    }

    public function Prueba()
    {
        echo json_encode(JobsDao::Prueba());
    }
}
