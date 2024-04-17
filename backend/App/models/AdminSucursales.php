<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\Database_cultiva;
use Exception;

class AdminSucursales
{
    public static function getComboSucursalesHorario(){

        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }
}
