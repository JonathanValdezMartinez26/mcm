<?php

namespace App\models;

include 'C:/xampp/htdocs/mcm/backend/Core/Database.php';

use \Core\Database;
use Exception;

class Jobs
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = [
            "success" => $respuesta,
            "mensaje" => $mensaje
        ];

        if ($datos != null) $res['datos'] = $datos;
        if ($error != null) $res['error'] = $error;

        return $res;
    }

    public static function GetCreditosActivos()
    {
        $qry = <<<sql
        SELECT
            APA.CONTRATO,
            APA.SALDO,
            APA.TASA
        FROM
            ASIGNA_PROD_AHORRO APA
        WHERE
            APA.SALDO > 0
            AND APA.ESTATUS = 'A'
        sql;

        try {
            $db = Database::getInstance();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Créditos activos obtenidos correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al obtener los créditos activos", null, $e->getMessage());
        }
    }

    public static function AplicaDevengo($datos)
    {
        $qryDevengo = <<<sql
        INSERT INTO
            DEVENGO_AHORRO (
                CONTRATO,
                SALDO_CIERRE,
                FECHA,
                DEVENGO,
                TASA
            )
        VALUES
            (
                :contrato,
                :saldo,
                SYSDATE,
                :devengo,
                :tasa
            )
        sql;

        $qrySaldo = <<<sql
        UPDATE
            ASIGNA_PROD_AHORRO
        SET
            INTERES = NVL(INTERES,0) + :devengo
        WHERE
            CONTRATO = :contrato
        sql;

        $datosSaldo = [
            "contrato" => $datos["contrato"],
            "devengo" => $datos["devengo"]
        ];

        $qrys = [
            $qryDevengo,
            $qrySaldo
        ];

        $parametros = [
            $datos,
            $datosSaldo
        ];

        try {
            $db = Database::getInstance();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Devengo aplicado correctamente");
        } catch (Exception $e) {
            return self::Responde(false, "Error al aplicar el devengo", null, $e->getMessage());
        }
    }

    public static function GetInversiones()
    {
        $qry = <<<sql
        SELECT
            (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = CI.CDG_CONTRATO) AS CLIENTE,
            CI.CDG_CONTRATO AS CONTRATO,
            CI.FECHA_APERTURA AS APERTURA,
            CI.FECHA_VENCIMIENTO AS VENCIMIENTO,
            CI.MONTO_INVERSION AS MONTO,
            CI.CDG_TASA AS ID_TASA
            TI.TASA,
            PI.DESCRIPCION AS PLAZO
        FROM
            CUENTA_INVERSION CI
        JOIN
            TASA_INVERSION TI ON CI.CDG_TASA = TI.CODIGO
        JOIN
            PLAZO_INVERSION PI ON TI.CDG_PLAZO = PI.CODIGO
        WHERE
            CI.ESTATUS = 'A'
            AND TRUNC(CI.FECHA_VENCIMIENTO) = TRUNC(SYSDATE)
        sql;

        try {
            $db = Database::getInstance();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Inversiones obtenidas correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al obtener las inversiones", null, $e->getMessage());
        }
    }

    public static function LiquidaInversion($datos)
    {
        $qryLiquidacion = <<<sql
        UPDATE
            CUENTA_INVERSION
        SET
            RENDIMIENTO = :rendimiento,
            ESTATUS = 'L',
            FECHA_LIQUIDACION = SYSDATE,
            MODIFICACION = SYSDATE
        WHERE
            CDG_CONTRATO = :contrato,
            AND ESTATUS = 'A',
            AND TRUNC(FECHA_VENCIMIENTO) = TRUNC(:fecha_vencimiento)
            AND TRUNC(FECHA_APERTURA) = TRUNC(:fecha_apertura)
            AND CDG_TASA = :id_tasa,
            AND MONTO_INVERSION = :monto
        sql;

        $qryMovimiento = <<<sql
        INSERT INTO
            MOVIMIENTOS_AHORRO (
                CODIGO,
                FECHA_MOV,
                CDG_TIPO_PAGO,
                CDG_CONTRATO,
                MONTO,
                MOVIMIENTO,
                DESCRIPCION,
                CDG_TICKET,
                FECHA_VALOR,
                CDG_RETIRO,
                CDGCO,
                CDGCL,
                CDGPE
            )
        VALUES
            (
                (
                    SELECT
                        NVL(MAX(TO_NUMBER(CODIGO)), 0)
                    FROM
                        MOVIMIENTOS_AHORRO
                ) + 1,
                SYSDATE,
                :tipo_pago,
                :contrato,
                :monto,
                :movimiento,
                '',
                (
                    SELECT
                        MAX(TO_NUMBER(CODIGO)) AS CODIGO
                    FROM
                        TICKETS_AHORRO
                    WHERE
                        CDG_CONTRATO = :contrato
                ),
                SYSDATE,
                (
                    SELECT
                        CASE
                            :tipo_pago
                            WHEN '6' THEN MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO))
                            WHEN '7' THEN MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO))
                            ELSE NULL
                        END
                    FROM
                        SOLICITUD_RETIRO_AHORRO
                    WHERE
                        CONTRATO = :contrato
                ),
                :sucursal,
                :cliente,
                :ejecutivo
            )
        sql;

        $qryTicket = <<<sql
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE, CDG_SUCURSAL)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, :ejecutivo, :sucursal)
        sql;

        $qrys = [
            $qryLiquidacion,
            $qryMovimiento,
            $qryTicket,
            $qryMovimiento,
            $qryTicket
        ];

        $parametros = [
            [
                "rendimiento" => $datos["rendimiento"],
                "contrato" => $datos["contrato"],
                "fecha_vencimiento" => $datos["fecha_vencimiento"],
                "fecha_apertura" => $datos["fecha_apertura"],
                "id_tasa" => $datos["id_tasa"],
                "monto" => $datos["monto"]
            ],
            [
                "cliente" => $datos["cliente"],
                "monto" => $datos["monto"],
                "contrato" => $datos["contrato"],
                "tipo_pago" => 11,
                "movimiento" => 1,
                "sucursal" => '000',
                "ejecutivo" => 'SSTM'
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["monto"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000'
            ],
            [
                "cliente" => $datos["cliente"],
                "monto" => $datos["rendimiento"],
                "contrato" => $datos["contrato"],
                "tipo_pago" => 12,
                "movimiento" => 1,
                "sucursal" => '000',
                "ejecutivo" => 'SSTM'

            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["rendimiento"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000'
            ]
        ];

        try {
            $db = Database::getInstance();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Inversión liquidada correctamente");
        } catch (Exception $e) {
            return self::Responde(false, "Error al liquidar la inversión", null, $e->getMessage());
        }
    }

    public static function GetSolicitudesRetiro()
    {
        $qry = <<<sql
        SELECT
            SRA.ID_SOL_RETIRO_AHORRO AS ID,
            SRA.CDG_CONTRATO AS CONTRATO,
            SRA.FECHA_SOLICITUD AS FECHA,
            SRA.MONTO_SOLICITADO AS MONTO
        FROM
            SOLICITUD_RETIRO_AHORRO SRA
        WHERE
            SRA.CDG_ESTATUS = 0
            AND TRUNC(SRA.FECHA_SOLICITUD) < TRUNC(SYSDATE)
        sql;

        try {
            $db = Database::getInstance();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Solicitudes de retiro obtenidas correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al obtener las solicitudes de retiro", null, $e->getMessage());
        }
    }
}
