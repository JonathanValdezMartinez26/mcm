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
    SELECT * FROM TBL_CIERRE_DIA 
    WHERE CDGEM = 'EMPFIN'
    AND CDGCLNS = '$noCredito'
    AND CICLO = '$noCiclo'
    AND FECHA_LIQUIDA IS NOT NULL 
sql;
      return $mysqli->queryOne($query);

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
