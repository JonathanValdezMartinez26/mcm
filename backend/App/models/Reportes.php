<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\Database_cultiva;

class Reportes{

    public static function ConsultaUsuariosSICAFINMCM(){

        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT PE.CODIGO AS COD_USUARIO, (NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE ) AS NOMBRE_COMPLETO, DESDEUS AS FECHA_ALTA, 
            CO.CODIGO AS COD_SUCURSAL, CO.NOMBRE AS SUCURSAL, PE.TELEFONO AS NOMINA, PE.CALLE AS NOMINA_JEFE, ACTIVO, '' AS PUESTO 
            FROM PE
            INNER JOIN CO ON CO.CODIGO = PE.CDGCO 
           
           ORDER BY PE.DESDEUS ASC

		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }
    public static function ConsultaUsuariosSICAFINCultiva(){

        $mysqli = Database_cultiva::getInstance();
        $query=<<<sql
           SELECT PE.CODIGO AS COD_USUARIO, (NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE ) AS NOMBRE_COMPLETO, DESDEUS AS FECHA_ALTA, 
            CO.CODIGO AS COD_SUCURSAL, CO.NOMBRE AS SUCURSAL, PE.TELEFONO AS NOMINA, PE.CALLE AS NOMINA_JEFE, ACTIVO, '' AS PUESTO 
            FROM PE
            INNER JOIN CO ON CO.CODIGO = PE.CDGCO 
           
           ORDER BY PE.DESDEUS ASC

		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);

    }

}
