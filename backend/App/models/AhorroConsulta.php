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
                ,RA.ESTATUS
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,TO_CHAR(RA.FECHA_ENTREGA_REAL, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ENTREGA_REAL
                ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,TO_CHAR(RA.FECHA_CANCELACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CANCELACION
                ,TO_CHAR(RA.FECHA_DEVOLUCION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_DEVOLUCION
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
            FROM 
                RETIROS_AHORRO RA
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                TRUNC(RA.FECHA_CREACION) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                FILTRO_USUARIO
            ORDER BY 
                RA.ID DESC
        SQL;

        $params = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if ($_SESSION['perfil'] === 'ADMIN') {
            $qry = str_replace('FILTRO_USUARIO', '', $qry);
        } else {
            $qry = str_replace('FILTRO_USUARIO', 'AND RA.CDGPE_ADMINISTRADORA = cdgpe_administradora', $qry);
            $params['cdgpe_administradora'] = $_SESSION['cdgpe'];
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            if ($res === false) return self::Responde(false, "Error al obtener los retiros");
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
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,TO_CHAR(RA.FECHA_ENTREGA_REAL, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ENTREGA_REAL
                ,RA.OBSERVACIONES_ADMINISTRADORA
                ,RA.ESTATUS
                ,RA.MOTIVO_CANCELACION
                ,RA.COMENTARIO_DEVOLUCION
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
            WITH CREDITO_ADICIONAL
            AS (
                SELECT CN.CDGCL
                    ,NVL(CN.CDGNS, 0) AS CDGNS
                    ,(
                        SELECT MAX(TO_NUMBER(PRN.CICLO))
                        FROM PRN
                        WHERE PRN.CDGNS = CN.CDGNS
                            AND PRN.CICLO NOT LIKE 'D%'
                            AND PRN.CICLO NOT LIKE 'R%'
                        GROUP BY PRN.CDGNS
                        ) AS ULTIMO_CICLO
                FROM CN
                WHERE CN.CDGEM = 'EMPFIN'
                    AND CN.ESTATUS = 'A'
                    AND CN.CDGMS IS NULL
                ORDER BY CN.INICIO DESC
                )
            SELECT CA.CDGNS
                ,PRC.CICLO AS ULTIMO_CICLO_TRADICIONAL
                ,CR_AD.CDGNS AS CREDITO_ADICIONAL
                ,CR_AD.ULTIMO_CICLO AS ULTIMO_CICLO_ADICIONAL
                ,0 AS ATRASO_TRADICONAL
                --,FNCALDIASMORA(PRC.CDGEM, PRC.CDGNS, 'G', PRC.CICLO) AS DIAS_MORA_TRADICONAL
                --,5 AS DIAS_MORA_ADICIONAL
                ,CASE WHEN NOT CR_AD.ULTIMO_CICLO IS NULL THEN FNCALDIASMORA(PRC.CDGEM, CR_AD.CDGNS, 'G', LPAD(TO_CHAR(CR_AD.ULTIMO_CICLO), 2, '0')) ELSE NULL END AS DIAS_MORA_ADICIONAL
                ,GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                ,FN_GET_AHORRO(PRC.CDGNS) AS SALDO_ACTUAL
                ,TO_CHAR(ADD_MONTHS(CA.FECHA_REGISTRO, 12), 'YYYY-MM-DD') AS ANIVERSARIO
            FROM CONTRATOS_AHORRO CA
            INNER JOIN PRC ON PRC.CDGNS = CA.CDGNS
            INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
            LEFT JOIN CREDITO_ADICIONAL CR_AD ON CR_AD.CDGCL = CL.CODIGO
                AND CR_AD.CDGNS <> CA.CDGNS
            WHERE CA.CDGNS = :cdgns
                AND PRC.CICLO NOT LIKE 'D%'
                AND PRC.CICLO NOT LIKE 'R%'
            ORDER BY PRC.CICLO DESC
            FETCH FIRST 1 ROWS ONLY
        SQL;

        $params = [
            ':cdgns' => $datos['cdgns']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            if (!$res) return self::Responde(false, "El crédito no tiene un contrato de ahorro.", null);

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

    public static function confirmarEntregaRetiroAhorro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO
            SET ESTATUS = 'E'
                , FECHA_ENTREGA_REAL = SYSDATE
            WHERE ID = :id
        SQL;

        $prms = ['id' => $datos['id']];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, "Entrega confirmada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al confirmar la entrega", null, $e->getMessage());
        }
    }

    public static function devolverRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO
            SET ESTATUS = 'D'
            , FECHA_DEVOLUCION = SYSDATE
            , COMENTARIO_DEVOLUCION = :comentario
            WHERE ID = :id
        SQL;

        $prms = [
            'id' => $datos['id'],
            'comentario' => $datos['comentario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, "Retiro devuelto correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al devolver el retiro", null, $e->getMessage());
        }
    }
}
