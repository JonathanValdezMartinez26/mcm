<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Core\Model;

class AhorroConsulta extends Model
{
    public static function getRetirosAhorro()
    {
        $qry = <<<SQL
            SELECT 
                ID_RETIRO
                ,CDGNS
                ,CANTIDAD_SOLICITADA
                ,CANTIDAD_AUTORIZADA
                ,FECHA_SOLICITUD
                ,FECHA_ENTREGA_SOLICITADA
                ,OBSERVACIONES_ADMINISTRADORA
                ,ESTATUS_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,CDGPE_SOPORTE
                ,FECHA_PROCESA_TESORERIA
                ,ESTATUS_TESORERIA
                ,OBSERVACIONES_TESORERIA
                ,CDGPE_TESORERIA
                ,FECHA_PROCESA_CALL_CENTER
                ,ESTATUS_CALL_CENTER
                ,OBSERVACIONES_CALL_CENTER
                ,CDGPE_CALL_CENTER
                ,FOTO
                ,FECHA_CREACION
            FROM 
                RETIROS_AHORRO_SIMPLE
            ORDER BY 
                ID_RETIRO DESC
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Retiros obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los retiros", null, $e->getMessage());
        }
    }
}
