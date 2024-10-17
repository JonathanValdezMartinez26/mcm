<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Promociones
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = array(
            "success" => $respuesta,
            "mensaje" => $mensaje
        );

        if ($datos != null) $res['datos'] = $datos;
        if ($error != null) $res['error'] = $error;

        return json_encode($res);
    }

    public static function ConsultarClientesInvitados($cdgns)
    {
        $query = <<<sql
        SELECT
            PRC.CDGNS AS CDGNS_INVITADO,
            CL_INVITADO,
            (
                CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE
            ) AS NOMBRE,
            PRN.CANTENTRE AS CANTIDAD_ENTREGADA,
            ABS((MP.CANTIDAD * 0.10)) AS DESCUENTO,
            CPT.CICLO_INVITACION,
            CPT.ESTATUS_PAGADO,
            FNCALDIASATRASO('EMPFIN', PRC.CDGNS, PRN.CICLO, 'G', SYSDATE) AS DIAS_ATRASO
        FROM
            CL_PROMO_TELARANA CPT
            INNER JOIN CL ON CL.CODIGO = CPT.CL_INVITADO
            INNER JOIN PRN ON PRN.CDGNS = CPT.CDGNS_INVITA
            INNER JOIN MP ON MP.CDGNS = CPT.CDGNS_INVITA
            INNER JOIN PRC ON PRC.CDGCL = CL_INVITADO
        WHERE
            CPT.CDGNS_INVITA = '$cdgns'
            AND PRN.CICLO = '01'
            AND MP.TIPO = 'IN'
            AND MP.CICLO = CPT.CICLO_INVITACION
            AND PRC.CDGCL = CL_INVITADO
            AND PRC.CICLO = '01'
        ORDER BY
            CPT.CICLO_INVITACION DESC
        sql;

        try {
            $mysqli = new Database();
            return $mysqli->queryAll($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function ConsultarDatosClienteRecomienda($cdgns)
    {
        $query = <<<sql
        SELECT
            *
        FROM
        (
            SELECT
                CL_INVITA,
                CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
                PRN.CICLO,
                PRN.CDGNS,
                CO.NOMBRE AS SUCURSAL,
                TO_CHAR(TCD.INICIO, 'YYYY/MM/DD') AS INICIO,
                TO_CHAR(TCD.FIN, 'YYYY/MM/DD') AS FIN,
                TCD.PLAZO,
                FNCALDIASATRASO('EMPFIN', '$cdgns', PRN.CICLO, 'G', SYSDATE) AS DIAS_ATRASO,
                MAX(PRN.CICLO) AS ULTIMO_CICLO_REGISTRADO
            FROM
                CL_PROMO_TELARANA CPT
                INNER JOIN CL ON CL.CODIGO = CPT.CL_INVITA
                INNER JOIN PRN ON PRN.CDGNS = CPT.CDGNS_INVITA
                INNER JOIN CO ON PRN.CDGCO = CO.CODIGO
                INNER JOIN TBL_CIERRE_DIA TCD ON TCD.CDGCLNS = CPT.CDGNS_INVITA
            WHERE
                CPT.CDGNS_INVITA = '$cdgns'
                AND PRN.SITUACION = 'E'
                AND TCD.CICLO = PRN.CICLO
            GROUP BY
                CL_INVITA,
                CL.NOMBRE1,
                CL.NOMBRE2,
                CL.PRIMAPE,
                CL.SEGAPE,
                PRN.CICLO,
                PRN.CDGNS,
                CO.NOMBRE,
                TCD.INICIO,
                TCD.FIN,
                TCD.PLAZO
            )
        GROUP BY
            CL_INVITA,
            NOMBRE,
            CICLO,
            CDGNS,
            SUCURSAL,
            INICIO,
            FIN,
            PLAZO,
            DIAS_ATRASO,
            ULTIMO_CICLO_REGISTRADO
        sql;
        //var_dump($query);
        try {
            $mysqli = new Database();
            return $mysqli->queryOne($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function ConsultaPgosPromocion($credito)
    {
        $query = <<<sql
        SELECT
            CL_INVITADO AS CODIGO,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            ABS((MP.CANTIDAD * 0.10)) AS PROMOCION,
            FNCALDIASATRASO('EMPFIN', PRC.CDGNS, PRN.CICLO, 'G', SYSDATE) AS CUMPLE
        FROM
            CL_PROMO_TELARANA CPT
            INNER JOIN CL ON CL.CODIGO = CPT.CL_INVITADO
            INNER JOIN PRN ON PRN.CDGNS = CPT.CDGNS_INVITA
            INNER JOIN MP ON MP.CDGNS = CPT.CDGNS_INVITA
            INNER JOIN PRC ON PRC.CDGCL = CL_INVITADO
        WHERE
            CPT.CDGNS_INVITA = '$credito'
            AND PRN.CICLO = '01'
            AND MP.TIPO = 'IN'
            AND MP.CICLO = CPT.CICLO_INVITACION
            AND PRC.CDGCL = CL_INVITADO
            AND PRC.CICLO = '01'
        ORDER BY
            CPT.CICLO_INVITACION DESC
        sql;
        try {
            $mysqli = new Database();
            return $mysqli->queryAll($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function RegistrarPagosPromocion($datos)
    {
        $pagos = $datos['pagos'];
        $query = "";

        foreach ($pagos as $pago) {
            $query .= <<<sql
            INSERT INTO MP
                (CDGNS, COMENTARIO, DESCUENTO)
            VALUES
                ('{$pago['id']}', '{$pago['comentario']}', '{$pago['descuento']}');
            sql;
        }

        try {
            // $mysqli = new Database();
            // return $mysqli->insert($query);
            return self::Responde(true, "Pago registrado correctamente", $query);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al registrar el pago", null, $e->getMessage());
        }
    }
}
