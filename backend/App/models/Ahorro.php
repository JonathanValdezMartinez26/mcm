<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;

class Ahorro{

    public static function ConcultaClientes($cliente){

        $query_valida_es_cliente_ahorro=<<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
sql;

        $query_busca_cliente=<<<sql
        SELECT (NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE) AS NOMBRE, CURP, REGISTRO  FROM CL WHERE CODIGO = '$cliente'
sql;

        $query_tiene_creditos=<<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
sql;

        $query_es_aval=<<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
sql;



        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query_busca_cliente);
        } catch (Exception $e) {
            return "";
        }
    }


}
