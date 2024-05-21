<?php

namespace App\models;

include 'C:/xampp/htdocs/mcm/backend/Core/Database.php';

use \Core\Database;
use Exception;

class JobsAhorro
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
            return self::Responde(true, "Créditos activos obtenidos correctamente", $res ?? []);
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
            CI.CDG_TASA AS ID_TASA,
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
            return self::Responde(true, "Inversiones obtenidas correctamente", $res ?? []);
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

        $qrys = [
            $qryLiquidacion,
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro(),
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
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
                "contrato" => $datos["contrato"],
                "monto" => $datos["monto"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000'
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["monto"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000',
                "tipo_pago" => 11,
                "movimiento" => 1,
                "cliente" => $datos["cliente"],
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["rendimiento"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000'
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["rendimiento"],
                "ejecutivo" => 'SSTM',
                "sucursal" => '000',
                "tipo_pago" => 12,
                "movimiento" => 1,
                "cliente" => $datos["cliente"],

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
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CODIGO AS CLIENTE,
            SRA.CANTIDAD_SOLICITADA AS MONTO,
            (
                SELECT
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE)
                FROM
                    PE
                WHERE
                    PE.CODIGO = SRA.CDGPE_ASIGNA_ESTATUS
                    AND CDGEM = 'EMPFIN'
            ) AS APROBADO_POR,
            TO_CHAR(SRA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_ESPERADA,
            SRA.CONTRATO,
            SRA.TIPO_RETIRO
        FROM
            SOLICITUD_RETIRO_AHORRO SRA
            INNER JOIN CL ON CL.CODIGO = (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = SRA.CONTRATO)
        WHERE
            SRA.ESTATUS <= 1
            AND TRUNC(SRA.FECHA_SOLICITUD) < TRUNC(SYSDATE)
        sql;

        try {
            $db = Database::getInstance();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Solicitudes de retiro obtenidas correctamente", $res ?? []);
        } catch (Exception $e) {
            return self::Responde(false, "Error al obtener las solicitudes de retiro", null, $e->getMessage());
        }
    }

    public static function CancelaSolicitudRetiro($datos)
    {
        $qry = <<<sql
        UPDATE
            SOLICITUD_RETIRO_AHORRO
        SET
            FECHA_ESTATUS = SYSDATE,
            ESTATUS = '5',
            CDGPE_ASIGNA_ESTATUS = 'SSTM'
        WHERE
            ID_SOL_RETIRO_AHORRO = '{$datos['idSolicitud']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if (!$res) return self::Responde(true, "Solicitud cancelada correctamente.");
            return self::Responde(false, "Ocurrió un error al cancelar la solicitud.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al cancelar la solicitud.", null, $e->getMessage());
        }
    }

    public static function  DevolucionRetiro($datos)
    {
        $query = [
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $datosInsert = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'ejecutivo' => 'SSTM',
                'sucursal' => $datos['sucursal']
            ],
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'ejecutivo' => 'SSTM',
                'sucursal' => $datos['sucursal'],
                'tipo_pago' => $datos['tipo'] == 1 ? '8' : '9',
                'movimiento' => '1',
                'cliente' => $datos['cliente'],
            ]
        ];

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datosInsert);
            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "Se han liberado $ " . number_format($datos['monto'], 2) . " a la cuenta del cliente por el apartado para el retiro " . ($datos['tipo'] == 1 ? "express" : "programado") . ".", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar la devolución.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar la devolución.", null, $e->getMessage());
        }
    }

    public static function GetSucursalesSinArqueo()
    {
        $qry = <<<sql
        SELECT
            SEA.CDG_SUCURSAL,
            NVL(ARQ.CONTEO, 0) AS CONTEO
        FROM
            SUC_ESTADO_AHORRO SEA
        LEFT JOIN (
                SELECT
                    A.CDG_SUCURSAL,
                    TRUNC(A.FECHA) AS FECHA,
                    COUNT(*) AS CONTEO
                FROM
                    ARQUEO A
                WHERE
                    TRUNC(FECHA) = TRUNC(SYSDATE)
                GROUP BY
                    A.CDG_SUCURSAL,
                    TRUNC(A.FECHA)
            ) ARQ ON ARQ.CDG_SUCURSAL = SEA.CDG_SUCURSAL
        WHERE
            ARQ.CONTEO IS NULL
        sql;

        try {
            $db = Database::getInstance();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Sucursales sin arqueo obtenidas correctamente", $res ?? []);
        } catch (Exception $e) {
            return self::Responde(false, "Error al obtener las sucursales sin arqueo", null, $e->getMessage());
        }
    }

    public static function RegistraArqueoPendiente($datos)
    {
        try {

            $qry = <<<sql
            INSERT INTO ARQUEO
            (CDG_ARQUEO, CDG_USUARIO, CDG_SUCURSAL, FECHA, MONTO, B_1000, B_500, B_200, B_100, B_50, B_20, M_10, M_5, M_2, M_1, M_050, M_020, M_010, SALDO_SUCURSAL)
            VALUES
            ((SELECT NVL(MAX(CDG_ARQUEO),0) FROM ARQUEO) + 1, :ejecutivo, :sucursal, SYSDATE, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT
                SALDO
            FROM
                SUC_ESTADO_AHORRO
            WHERE
                CDG_SUCURSAL = :sucursal))
            sql;

            $parametros = [
                'ejecutivo' => $datos['ejecutivo'],
                'sucursal' => $datos['sucursal']
            ];

            $mysqli = Database::getInstance();
            $res = $mysqli->insertar($qry, $parametros);
            return self::Responde(true, "Arqueo registrado correctamente.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el arqueo.", null, $e->getMessage());
        }
    }

    public static function GetQueryTicket()
    {
        return <<<sql
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE, CDG_SUCURSAL)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, :ejecutivo, :sucursal)
        sql;
    }

    public static function GetQueryMovimientoAhorro()
    {
        return <<<sql
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
                'ALGUNA_DESCRIPCION',
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
    }

    public static function RecuperaTicket($contrato)
    {
        $queryTicket = <<<sql
        SELECT
            MAX(TO_NUMBER(CODIGO)) AS CODIGO
        FROM
            TICKETS_AHORRO
        WHERE
            CDG_CONTRATO = '$contrato'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryOne($queryTicket);
        } catch (Exception $e) {
            return 0;
        }
    }
}