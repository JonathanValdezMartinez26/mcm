<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class Login{

    public static function getById($usuario){
        $mysqli = Database::getInstance(true);


        $query =<<<sql
       SELECT CODIFICA(:password) as pass FROM DUAL
sql;
        $params = array(
            ':password'=>$usuario->_password
        );

        $pass = $mysqli->queryOne($query,$params);

        //var_dump($pass);

        $query1 =<<<sql
        SELECT * FROM PE WHERE CODIGO LIKE :usuario AND CLAVE LIKE :password 
sql;
        $params1 = array(
            ':usuario'=> $usuario->_usuario,
            ':password'=>$pass['PASS']
        );

        return $mysqli->queryOne($query1,$params1);


    }

    public static function getUser($usuario){
        $mysqli = Database::getInstance(true);
        $query =<<<sql
        SELECT * FROM PE WHERE CODIGO = '$usuario' AND ACTIVO = 'S'
sql;

        return $mysqli->queryAll($query);
    }
}
