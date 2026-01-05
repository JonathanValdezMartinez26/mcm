<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Core\Model;

class AhorroConsulta extends Model
{
    public static function GetRetirosAhorro($datos)
    {
        $qry = <<<SQL
            SELECT 
                ID
                ,CDGNS
                ,CANT_SOLICITADA
                ,NVL(CANT_AUTORIZADA, 0) AS CANT_AUTORIZADA
                ,TO_CHAR(FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(FECHA_ENTREGA_SOLICITADA, 'DD/MM/YYYY') AS FECHA_ENTREGA_SOLICITADA
                ,OBSERVACIONES_ADMINISTRADORA
                ,ESTATUS_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,CDGPE_SOPORTE
                ,TO_CHAR(FECHA_PROCESA_TESORERIA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_PROCESA_TESORERIA 
                ,ESTATUS_TESORERIA
                ,OBSERVACIONES_TESORERIA
                ,CDGPE_TESORERIA
                ,TO_CHAR(FECHA_CALL_CENTER, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CALL_CENTER
                ,ESTATUS_CALL_CENTER
                ,OBSERVACIONES_CALL_CENTER
                ,CDGPE_CALL_CENTER
                ,TO_CHAR(FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,TO_CHAR(FECHA_CANCELACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CANCELACION
            FROM 
                RETIROS_AHORRO
            WHERE
                TRUNC(FECHA_CREACION) >= TO_DATE(:fechaI, 'YYYY-MM-DD')
                AND TRUNC(FECHA_CREACION) <= TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY 
                ID DESC
        SQL;

        $params = [
            ':fechaI' => $datos['fechaI'],
            ':fechaF' => $datos['fechaF']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            return self::Responde(true, "Retiros obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los retiros", null, $e->getMessage());
        }
    }

    public static function getRetiroById($id)
    {
        $qry = <<<SQL
            SELECT 
                ID
                ,CDGNS
                ,CANT_SOLICITADA
                ,NVL(CANT_AUTORIZADA, 0) AS CANT_AUTORIZADA
                ,TO_CHAR(FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(FECHA_ENTREGA_SOLICITADA, 'DD/MM/YYYY') AS FECHA_ENTREGA_SOLICITADA
                ,OBSERVACIONES_ADMINISTRADORA
                ,ESTATUS_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,CDGPE_SOPORTE
                ,TO_CHAR(FECHA_PROCESA_TESORERIA, 'DD/MM/YYYY') AS FECHA_PROCESA_TESORERIA
                ,ESTATUS_TESORERIA
                ,OBSERVACIONES_TESORERIA
                ,CDGPE_TESORERIA
                ,TO_CHAR(FECHA_CALL_CENTER, 'DD/MM/YYYY') AS FECHA_CALL_CENTER
                ,ESTATUS_CALL_CENTER
                ,OBSERVACIONES_CALL_CENTER
                ,CDGPE_CALL_CENTER
                ,TO_CHAR(FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
            FROM 
                RETIROS_AHORRO
            WHERE 
                ID = :id
        SQL;

        $params = [':id' => $id];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            if (!$res) {
                return self::Responde(false, "No se encontró el retiro", null);
            }

            return self::Responde(true, "Retiro obtenido correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el retiro", null, $e->getMessage());
        }
    }

    public static function BuscarSaldo($datos)
    {
        $qry = <<<SQL
            SELECT
                CDGNS,
                MAX(TO_NUMBER(CICLO)) AS CICLO_ACTUAL,
                FN_GET_AHORRO(:cdgns) AS SALDO_ACTUAL
            FROM
                PRN
            WHERE
                CDGNS = :cdgns
            GROUP BY
                CDGNS
        SQL;

        $params = [
            ':cdgns' => $datos['cdgns']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            if (!$res) return self::Responde(false, "No se encontró el ahorro", null);

            return self::Responde(true, "Saldo obtenido correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el saldo", null, $e->getMessage());
        }
    }

    public static function insertRetiro($datos)
    {
        $qry = <<<SQL
            INSERT INTO RETIROS_AHORRO (
                CDGNS
                , CICLO
                ,CANT_SOLICITADA
                ,FECHA_SOLICITUD
                ,FECHA_ENTREGA_SOLICITADA
                ,OBSERVACIONES_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,FOTO
                ,FECHA_CREACION
            ) VALUES (
                :cdgns
                ,:ciclo
                ,:cantidad_solicitada
                ,TO_DATE(:fecha_solicitud, 'YYYY-MM-DD')
                ,TO_DATE(:fecha_entrega_solicitada, 'YYYY-MM-DD')
                ,:observaciones_administradora
                ,:cdgpe_administradora
                , EMPTY_BLOB()
                ,SYSDATE
            )
            RETURNING FOTO INTO :foto
        SQL;

        $params = [
            'cdgns' => $datos['cdgns'],
            'ciclo' => $datos['ciclo'],
            'cantidad_solicitada' => $datos['cantidad_solicitada'],
            'fecha_solicitud' => $datos['fecha_solicitud'],
            'fecha_entrega_solicitada' => $datos['fecha_entrega_solicitada'],
            'observaciones_administradora' => $datos['observaciones_administradora'],
            'cdgpe_administradora' => $datos['cdgpe_administradora'],
            'foto' => $datos['foto']
        ];

        try {
            $db = new Database();
            $db->insertarBlob($qry, $params, ['foto']);
            return self::Responde(true, "Solicitud de retiro creada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al crear la solicitud", null, $e->getMessage());
        }
    }

    public static function getImgSolicitud($datos)
    {
        $qry = <<<SQL
            SELECT 
                FOTO
            FROM 
                RETIROS_AHORRO
            WHERE 
                ID = :id
        SQL;

        $params = [':id' => $datos['id']];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            return self::Responde(true, "Imagen obtenida correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener la imagen", null, $e->getMessage());
        }
    }
}
