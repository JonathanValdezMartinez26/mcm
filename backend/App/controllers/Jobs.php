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
        $creditos = JobsDao::CreditosAutorizados("11/04/2024");
        echo json_encode($creditos);
        die();
        foreach ($creditos as $key => $credito) {
            $pDemo[] = JobsDao::ActualizaCheques($credito);
        }

        echo json_encode($pDemo);
        die();
    }
}
