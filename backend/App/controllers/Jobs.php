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
        $esquema = [
            "PRMCDGEM" => "string",
            "PRMCDGCLNS" => "string",
            "PRMCLNS" => "string",
            "PRMCICLO" => "string",
            "PRMT_CDGCL" => "string",
            "PRMT_NOCHEQUE" => "string",
            "PRMFECHA" => "string",
            "PRMUSER" => "string",
            "PRMCDGCB" => "string"
        ];

        self::ValidaEsquema($esquema, $_POST);

        $parametros = [];
        $parametros[":PRMCDGEM"] = $_POST['PRMCDGEM'];
        $parametros[":PRMCDGCLNS"] = $_POST['PRMCDGCLNS'];
        $parametros[":PRMCLNS"] = $_POST['PRMCLNS'];
        $parametros[":PRMCICLO"] = $_POST['PRMCICLO'];
        $parametros[":PRMT_CDGCL"] = $_POST['PRMT_CDGCL'];
        $parametros[":PRMT_NOCHEQUE"] = $_POST['PRMT_NOCHEQUE'];
        $parametros[":PRMFECHA"] = $_POST['PRMFECHA'];
        $parametros[":PRMUSER"] = $_POST['PRMUSER'];
        $parametros[":PRMCDGCB"] = $_POST['PRMCDGCB'];
        $parametros[":VMENSAJE"] = "__RETURN__";

        // ('EMPFIN', '010407', 'G', '09', T_CDGCL('112690''), T_NOCHEQUE('4568''), '2024-04-11 00:00:00.000', 'AMGM', '28', ?)

        echo JobsDao::sp_con_array($parametros);
    }
}
