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
                RA.ID
                ,RA.CDGNS
                ,RA.CANT_SOLICITADA
                ,NVL(RA.CANT_AUTORIZADA, 0) AS CANT_AUTORIZADA
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,RA.OBSERVACIONES_ADMINISTRADORA
                ,RA.ESTATUS
                ,RA.CDGPE_ADMINISTRADORA
                ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,TO_CHAR(RA.FECHA_CANCELACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CANCELACION
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
            FROM 
                RETIROS_AHORRO RA
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                TRUNC(RA.FECHA_CREACION) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY 
                RA.ID DESC
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
                RA.ID
                ,RA.CDGNS
                ,RA.CANT_SOLICITADA
                ,NVL(RA.CANT_AUTORIZADA, 0) AS CANT_AUTORIZADA
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,RA.OBSERVACIONES_ADMINISTRADORA
                ,RA.ESTATUS
                ,RA.MOTIVO_CANCELACION
                ,RA.CDGPE_ADMINISTRADORA
                ,GET_NOMBRE_EMPLEADO(RA.CDGPE_ADMINISTRADORA) AS NOMBRE_ADMINISTRADORA
                ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,CASE RA.ESTATUS
                    WHEN 'V' THEN 'Validado'
                    WHEN 'C' THEN 'Cancelado'
                    WHEN 'R' THEN 'Rechazado'
                    WHEN 'P' THEN 'Pendiente'
                    WHEN 'A' THEN 'Aprobado'
                    WHEN 'E' THEN 'Entregado'
                    WHEN 'D' THEN 'Devuelto'
                    ELSE NULL
                 END AS ESTATUS_ETIQUETA
                ,RAC.ESTATUS AS ESTATUS_CC
                ,CASE RAC.ESTATUS
                    WHEN 'C' THEN 'Completado'
                    WHEN 'I' THEN 'Incompleto'
                    ELSE 'Pendiente'
                 END AS ESTATUS_CC_ETIQUETA
                ,RAC.CDGPE AS CDGPE_CC
                ,RAC.COMENTARIO_EXTERNO
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
            FROM  
                RETIROS_AHORRO RA
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE 
                RA.ID = :id
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
                ,FECHA_ENTREGA
                ,OBSERVACIONES_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,FOTO
                ,FECHA_CREACION
            ) VALUES (
                :cdgns
                ,:ciclo
                ,:cantidad_solicitada
                ,TO_DATE(:fecha_solicitud, 'YYYY-MM-DD')
                ,TO_DATE(:fecha_entrega, 'YYYY-MM-DD')
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
            'fecha_entrega' => $datos['fecha_entrega'],
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
