<?php
namespace Core;
defined("APPPATH") OR die("Access denied");
use \App\models\General AS GeneralDao;
class Controller{

    public $__usuario = '';
    public $__nombre = '';

    public function __construct(){
    	session_start();
    	if($_SESSION['usuario'] == '' || empty($_SESSION['usuario'])){
    	    unset($_SESSION);
                session_unset();
                session_destroy();
                header("Location: /Login/");
                exit();
            }else{
    	    $this->__usuario = $_SESSION['usuario'];
    	    $this->__nombre = $_SESSION['nombre'];
    	}
    }

    public static function getPermisosUsuario1($usuario){
        return $usuario;
    }

    public static function getPermisosEmpresas($usuario){
        $permiso = GeneralDao::getPermisos($usuario);
        foreach ($permiso as $key => $value) {
            return $value['seccion_empresas'];
        }
        //return $permiso;
    }

    public static function getPermisosUsuario($usuario, $seccion, $valor){
        $id = GeneralDao::getPermisos($usuario);
        foreach ($id as $key => $value) {
            $secciones = $value[$seccion];
            $valores = explode("-",$secciones);
            $v = 0;

            switch ($valor) {
                case 1:
                    $v = ($valores['0'] == 1) ? 1 : 0;
                    break;
                case 2:
                    $v = ($valores['1'] == 2) ? 1 : 0;
                    break;
                case 3:
                    $v = ($valores['2'] == 3) ? 1 : 0;
                    break;
                case 4:
                    $v = ($valores['3'] == 4) ? 1 : 0;
                    break;
                case 5:
                    $v = ($valores['4'] == 5) ? 1 : 0;
                    break;
                case 6:
                    $v = ($valores['5'] == 6) ? 1 : 0;
                    break;
                case 7:
                    $v = ($secciones == 1) ? 1 : 0;
                    break;
                default:
                    # code...
                    break;
            }
            return $v;
        }

    }

    public static function getPermisoRecursosHumanos($usuario){
        $id = GeneralDao::getUsuario($usuario);

        $permiso = 0;
        if($id['permisos_globales'] == 2)
            $permiso = 2;
        

        return $permiso;

    }


}
