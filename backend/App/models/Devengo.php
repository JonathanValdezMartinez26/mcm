<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Devengo{

    public static function ConsultaExiste($noCredito, $noCiclo){
      $mysqli = Database::getInstance();


        $query=<<<sql
        SELECT COUNT(*) AS EXISTE FROM TBL_CIERRE_DIA WHERE CDGEM = 'EMPFIN' AND CDGCLNS = '$noCredito'
        AND CICLO = '$noCiclo' AND FECHA_LIQUIDA IS NOT NULL
sql;

        $existe = $mysqli->queryOne($query);

        if($existe['EXISTE'] == 1)
        {
            $query1=<<<sql
            SELECT CDGCLNS, NOMBRE, CICLO, COD_SUCURSAL, NOM_SUCURSAL, NOM_ASESOR, FECHA_LIQUIDA, REGION, INICIO , FIN, PLAZO, TASA, INTERES_GLOBAL,
            TOTAL_PAGAR, SITUACION  AS RES FROM TBL_CIERRE_DIA 
            WHERE CDGEM = 'EMPFIN'
            AND CDGCLNS = '$noCredito'
            AND CICLO = '$noCiclo'
            AND FECHA_LIQUIDA IS NOT NULL 
sql;
            return $mysqli->queryOne($query1);
        }
        else
        {
            return false;
        }

    }

    public static function ReactivarCredito($noCredito, $noCiclo){
        $mysqli = Database::getInstance();
        $query=<<<sql
    SELECT * FROM TBL_CIERRE_DIA 
    WHERE CDGEM = 'EMPFIN'
    AND CDGCLNS = '$noCredito'
    AND CICLO = '$noCiclo'
    AND FECHA_LIQUIDA IS NOT NULL 
sql;
        return $mysqli->queryOne($query);

    }

}
