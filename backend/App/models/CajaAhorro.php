<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;
use DateTime;

/**
 * Tablas de registros:
 * - ASIGNA_PROD_AHORRO
 * - BENEFICIARIOS_AHORRO
 * - MOVIMIENTOS_AHORRO
 * - TICKETS_AHORRO
 * - CUENTA_INVERSION
 * - CL_PQS
 *
 * Limpieza de tablas:
 * DELETE FROM ASIGNA_PROD_AHORRO;
 * DELETE FROM BENEFICIARIOS_AHORRO;
 * DELETE FROM MOVIMIENTOS_AHORRO;
 * DELETE FROM TICKETS_AHORRO;
 * DELETE FROM CUENTA_INVERSION;
 * DELETE FROM CL_PQS;
 */

class CajaAhorro
{
    public static function GetEFed()
    {
        $query = <<<sql
        SELECT NOMBRE FROM EF WHERE NOMBRE != 'Desconocido'
        sql;

        $mysqli = Database::getInstance();
        return $mysqli->queryAll($query);
    }

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

    public static function GetCatalogoParentescos()
    {
        $query = <<<sql
        SELECT
            *
        FROM
            CAT_PARENTESCO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSucursalAsignadaCajeraAhorro($usuario)
    {
        if ($usuario == '') {
            $var =  '';
        } else {
            $var = "WHERE SUC_CAJERA_AHORRO.CDG_USUARIO = '" . $usuario . "'";
        }
        $query = <<<sql
        SELECT
            CO.CODIGO, CO.NOMBRE  
        FROM
            SUC_ESTADO_AHORRO 
        INNER JOIN SUC_CAJERA_AHORRO ON SUC_ESTADO_AHORRO.CODIGO = SUC_CAJERA_AHORRO.CDG_ESTADO_AHORRO
        INNER JOIN CO ON CO.CODIGO = SUC_ESTADO_AHORRO.CDG_SUCURSAL 
        $var
        ORDER BY CO.CODIGO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetEjecutivosSucursal($sucursal)
    {
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
            CODIGO ID_EJECUTIVO
        FROM
            PE
        WHERE
            CDGEM = 'EMPFIN' 
            AND CDGCO IN( '$sucursal')
            AND ACTIVO = 'S'
            AND BLOQUEO = 'N'
        ORDER BY 1
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSaldoMinimoInversion()
    {
        $query = <<<sql
        SELECT
            MIN(MONTO_MINIMO) AS MONTO_MINIMO
        FROM
            TASA_INVERSION
        WHERE
            ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if ($res) return $res['MONTO_MINIMO'];
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function GetTasas()
    {
        $query = <<<sql
        SELECT
            TI.CODIGO,
            TI.TASA,
            TI.MONTO_MINIMO,
            TI.CDG_PLAZO,
            CONCAT(
                CONCAT(PI.PLAZO, ' '),
                CASE PI.PERIODICIDAD
                    WHEN 'D' THEN 'Días'
                    WHEN 'S' THEN 'Semanas'
                    WHEN 'M' THEN 'Meses'
                    WHEN 'A' THEN 'Años'
                END
            ) AS PLAZO
        FROM
            TASA_INVERSION TI
        LEFT JOIN
            PLAZO_INVERSION PI
        ON
            TI.CDG_PLAZO = PI.CODIGO
        WHERE
            TI.ESTATUS = 'A'
        ORDER BY 
            TI.MONTO_MINIMO,
            TI.TASA
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetOperacionesLog()
    {
        $qry = <<<sql
        SELECT
            TIPO
        FROM
            LOG_TRANSACCIONES_AHORRO
        GROUP BY
            TIPO
        ORDER BY
            TIPO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetUsuariosLog()
    {
        $qry = <<<sql
        SELECT
            USUARIO
        FROM
            LOG_TRANSACCIONES_AHORRO
        GROUP BY
            USUARIO
        ORDER BY
            USUARIO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSucursalesLog()
    {
        $qry = <<<sql
        SELECT
            SUCURSAL
        FROM
            LOG_TRANSACCIONES_AHORRO
        GROUP BY
            SUCURSAL
        ORDER BY
            SUCURSAL
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetBeneficiarios($contrato)
    {
        $query = <<<sql
        SELECT
            *
        FROM
            BENEFICIARIOS_AHORRO
        WHERE
            CDG_CONTRATO = '$contrato'
            AND ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return self::Responde(true, "Consulta realizada correctamente.", $res);
            return self::Responde(false, "No se encontraron beneficiarios para el contrato {$contrato}.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los beneficiarios del contrato {$contrato}.", null, $e->getMessage());
        }
    }

    public static function ConsultaClientesProducto($cliente)
    {

        $query_valida_es_cliente_ahorro = <<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        $query_busca_cliente = <<<sql
        SELECT (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE) AS NOMBRE, CL.CURP, TO_CHAR(CL.REGISTRO ,'DD-MM-YYYY')AS REGISTRO, 
        TRUNC(MONTHS_BETWEEN(TO_DATE(SYSDATE,'dd-mm-yy'),CL.NACIMIENTO)/12)AS EDAD,  UPPER((CL.CALLE || ', ' || COL.NOMBRE|| ', ' || LO.NOMBRE || ', ' || MU.NOMBRE  || ', ' || EF.NOMBRE)) AS DIRECCION   
        FROM CL, COL, LO, MU,EF 
        WHERE EF.CODIGO = CL.CDGEF
        AND MU.CODIGO = CL.CDGMU
        AND LO.CODIGO = CL.CDGLO 
        AND COL.CODIGO = CL.CDGCOL
        AND EF.CODIGO = MU.CDGEF 
        AND EF.CODIGO = LO.CDGEF
        AND EF.CODIGO = COL.CDGEF
        AND MU.CODIGO = LO.CDGMU 
        AND MU.CODIGO = COL.CDGMU 
        AND LO.CODIGO = COL.CDGLO 
        AND CL.CODIGO = '$cliente'
        sql;


        $query_tiene_creditos = <<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        $query_es_aval = <<<sql
        SELECT * FROM CL WHERE CODIGO = '$cliente'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query_busca_cliente);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function BuscaClienteNvoContrato($datos)
    {
        $queryValidacion = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CURP,
            TO_CHAR(CL.REGISTRO, 'DD-MM-YYYY') AS FECHA_REGISTRO,
            TRUNC(MONTHS_BETWEEN(TO_DATE(SYSDATE, 'dd-mm-yy'), CL.NACIMIENTO)/12)AS EDAD,
            UPPER(DOMICILIO_CLIENTE(CL.CODIGO)) AS DIRECCION,
            (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1) AS CONTRATO,
            NVL((SELECT SALDO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1), 0) AS SALDO,
            CL.CODIGO AS CDGCL,
            (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1) AS CONTRATO,
            NVL(
                (SELECT
                    COUNT(MA.CDG_CONTRATO)
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    MA.CDG_TIPO_PAGO = 2
                    AND MA.CDG_CONTRATO = (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1)
            ),0) AS CONTRATO_COMPLETO,
            (
                SELECT
                    COUNT(APA.CONTRATO)
                FROM
                    ASIGNA_PROD_AHORRO APA
                WHERE
                    APA.CDGCL = CL.CODIGO
                    AND CDGPR_PRIORITARIO = 1
            ) AS NO_CONTRATOS
        FROM
            CL
        WHERE
            CL.CODIGO = '{$datos['cliente']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion);
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}.");
            if ($res['NO_CONTRATOS'] >= 1) return self::Responde(false, "El cliente {$datos['cliente']} ya cuenta con un contrato de ahorro.", $res);
            if ($res) return self::Responde(true, "Consulta realizada correctamente.", $res);
            return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente.", null, $e->getMessage());
        }
    }

    public static function BuscaContratoAhorro($datos)
    {
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CURP,
            (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1) AS CONTRATO,
            NVL((SELECT SALDO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1), 0) AS SALDO,
            CL.CODIGO AS CDGCL,
            NVL(
                (SELECT
                    MA.MONTO
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    MA.CDG_TIPO_PAGO = 2
                    AND MA.CDG_CONTRATO = (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1)
            ),0) AS CONTRATO_COMPLETO,
            (
                SELECT
                    COUNT(APA.CONTRATO)
                FROM
                    ASIGNA_PROD_AHORRO APA
                WHERE
                    APA.CDGCL = CL.CODIGO
                    AND CDGPR_PRIORITARIO = 1
            ) AS NO_CONTRATOS
        FROM
            CL
        WHERE
            CL.CODIGO = '{$datos['cliente']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}.");
            if ($res['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro.", $res);
            if ($res['NO_CONTRATOS'] >= 1 && $res['CONTRATO_COMPLETO'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no ha concluido el proceso de apertura de su cuenta de ahorro.", $res);
            return self::Responde(true, "Consulta realizada correctamente.", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente.", null, $e->getMessage());
        }
    }

    public static function AgregaContratoAhorro($datos)
    {
        $queryValidacion = <<<sql
        SELECT
            *
        FROM
            ASIGNA_PROD_AHORRO APA
        WHERE
            CDGCL = :cliente
            AND (
                SELECT
                    COUNT(MA.CDG_CONTRATO)
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    MA.CDG_TIPO_PAGO = 2
                    AND MA.CDG_CONTRATO = APA.CONTRATO
            ) > 0
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion, ['cliente' => $datos['credito']]);
            if ($res) return self::Responde(false, "El cliente ya cuenta con un contrato de ahorro");

            $noContrato = $datos['credito'] . date('Ymd');

            $query = <<<sql
            INSERT INTO ASIGNA_PROD_AHORRO
                (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO, TASA, CDGCO, CDGPE_COMISIONA, CDGPE_REGISTRO)
            VALUES
                (:contrato, :cliente, :fecha_apertura, '1', 'A', 0, :tasa, :sucursal, :ejecutivo_comisiona, :ejecutivo_registro)
            sql;

            $queryBen = <<<sql
            INSERT INTO BENEFICIARIOS_AHORRO
                (CDG_CONTRATO, NOMBRE, CDGCT_PARENTESCO, ESTATUS, FECHA_MODIFICACION, PORCENTAJE)
            VALUES
                (:contrato, :nombre, :parentesco, 'A', SYSDATE, :porcentaje)
            sql;

            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
            $fecha = $fecha !== false && $fecha->format('Y-m-d') === $datos['fecha'] ? $fecha->format('d-m-Y') : $datos['fecha'];

            $datosInsert = [
                [
                    'contrato' => $noContrato,
                    'cliente' => $datos['credito'],
                    'fecha_apertura' => $fecha,
                    'tasa' => $datos['tasa'],
                    'sucursal' => $datos['sucursal'],
                    'ejecutivo_comisiona' => $datos['ejecutivo_comision'],
                    'ejecutivo_registro' => $datos['ejecutivo'],
                ]
            ];

            $inserts = [
                $query
            ];

            if ($datos['beneficiario_1']) {
                $datosInsert[] = [
                    'contrato' => $noContrato,
                    'nombre' => $datos['beneficiario_1'],
                    'parentesco' => $datos['parentesco_1'],
                    'porcentaje' => $datos['porcentaje_1']
                ];
                $inserts[] = $queryBen;
            }

            if ($datos['beneficiario_2']) {
                $datosInsert[] = [
                    'contrato' => $noContrato,
                    'nombre' => $datos['beneficiario_2'],
                    'parentesco' => $datos['parentesco_2'],
                    'porcentaje' => $datos['porcentaje_2']
                ];
                $inserts[] = $queryBen;
            }

            if ($datos['beneficiario_3']) {
                $datosInsert[] = [
                    'contrato' => $noContrato,
                    'nombre' => $datos['beneficiario_3'],
                    'parentesco' => $datos['parentesco_3'],
                    'porcentaje' => $datos['porcentaje_3']
                ];
                $inserts[] = $queryBen;
            }


            try {
                $mysqli = Database::getInstance();
                $res = $mysqli->insertaMultiple($inserts, $datosInsert);
                if ($res) {
                    LogTransaccionesAhorro::LogTransacciones($inserts, $datosInsert, $_SESSION['cdgco'], $_SESSION['usuario'], $noContrato, "Nuevo contrato ahorro corriente");
                    return self::Responde(true, "Contrato de ahorro registrado correctamente.", ['contrato' => $noContrato]);
                }
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.");
            } catch (Exception $e) {
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.", null, $e->getMessage());
            }
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro.", null, $e->getMessage());
        }
    }

    public static function AddPagoApertura($datos)
    {
        if ($datos['monto'] == 0) return self::Responde(false, "El monto de apertura no puede ser de 0.");
        if ($datos['monto'] < $datos['sma']) return self::Responde(false, "El monto mínimo de apertura no puede ser menor a " . $datos['sma'] . ".");

        $query = [
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro(),
            self::GetQueryMovimientoAhorro()
        ];

        $validacion = [
            'query' => self::GetQueryValidaAhorro(),
            'datos' => ['contrato' => $datos['contrato']],
            'funcion' => [CajaAhorro::class, 'ValidaMovimientoAhorro']
        ];

        $datosInsert = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'ejecutivo' => $datos['ejecutivo'],
                'sucursal' => $datos['sucursal']
            ],
            [
                'tipo_pago' => '1',
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'movimiento' => '1'
            ],
            [
                'tipo_pago' => '2',
                'contrato' => $datos['contrato'],
                'monto' => $datos['inscripcion'],
                'movimiento' => '0'
            ]
        ];

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datosInsert, $validacion);

            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "Pago de apertura registrado correctamente.", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura.", null, $e->getMessage());
        }
    }

    public static function RegistraOperacion($datos)
    {
        $query = [
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $esDeposito = $datos['esDeposito'] === true || $datos['esDeposito'] === 'true';

        $datosInsert = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'ejecutivo' => $datos['ejecutivo'],
                'sucursal' => $datos['sucursal']
            ],
            [
                'tipo_pago' => $esDeposito ? '3' : '4',
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'movimiento' => $esDeposito ? '1' : '0'
            ]
        ];

        $tipoMov = $esDeposito ? "depósito" : "retiro";

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datosInsert);
            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "El " . $tipoMov . " fue registrado correctamente.", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov . ".");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov  . ".", null, $e->getMessage());
        }
    }

    public static function ValidaMovimientoAhorro($validar)
    {
        $resultado = [
            'success' => true,
            'mensaje' => ""
        ];

        if (count($validar) > 0) return $resultado;

        $resultado['success'] = false;
        $resultado['mensaje'] = "Se detecto diferencia entre el registro del ticket y los movimiento de ahorro.";
        return $resultado;
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
        INSERT INTO MOVIMIENTOS_AHORRO
            (CODIGO, FECHA_MOV, CDG_TIPO_PAGO, CDG_CONTRATO, MONTO, MOVIMIENTO, DESCRIPCION, CDG_TICKET, FECHA_VALOR, CDG_RETIRO)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM MOVIMIENTOS_AHORRO) + 1, SYSDATE, :tipo_pago, :contrato, :monto, :movimiento, 'ALGUNA_DESCRIPCION', (SELECT MAX(TO_NUMBER(CODIGO)) AS CODIGO FROM TICKETS_AHORRO WHERE CDG_CONTRATO = :contrato), SYSDATE, (SELECT CASE :tipo_pago WHEN '7' THEN MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO)) ELSE NULL END FROM SOLICITUD_RETIRO_AHORRO WHERE CONTRATO = :contrato))
        sql;
    }

    public static function GetQueryValidaAhorro()
    {
        return <<<sql
        SELECT
            *
        FROM
            (
            SELECT
                T.CODIGO AS TC,
                T.MONTO AS TM,
                MA.CODIGO AS MC,
                MA.MONTO AS MM,
                NVL(T.MONTO,0) - NVL(MA.MONTO,0) AS DIFERENCIA
            FROM
                TICKETS_AHORRO T
            FULL JOIN
                (
                SELECT
                    M.CDG_TICKET AS CODIGO,
                    SUM(CASE M.MOVIMIENTO
                        WHEN '1' THEN M.MONTO
                        ELSE -M.MONTO
                    END) AS MONTO
                FROM
                    MOVIMIENTOS_AHORRO M
                GROUP BY
                    M.CDG_TICKET
                ) MA
            ON T.CODIGO = MA.CODIGO
            WHERE
                T.CDG_CONTRATO = :contrato
            )
        WHERE
            DIFERENCIA != 0
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

    public static function DatosTicket($ticket)
    {
        $query = <<< sql
        SELECT
            TO_CHAR(T.FECHA, 'dd/mm/yyyy HH24:MI:SS') AS FECHA,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_CLIENTE,
            T.CDG_SUCURSAL,
            (
                SELECT
                    NOMBRE
                FROM
                    CO
                WHERE
                    CODIGO = T.CDG_SUCURSAL
            ) AS NOMBRE_SUCURSAL,
            CL.CODIGO,
            APA.CONTRATO,
            T.MONTO,
            (
                SELECT
                    SUM(
                        CASE MA.MOVIMIENTO
                            WHEN '0' THEN -MA.MONTO
                            ELSE MA.MONTO
                        END 
                    )
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    TO_NUMBER(CDG_TICKET) < TO_NUMBER(T.CODIGO)
                    AND T.CDG_CONTRATO = MA.CDG_CONTRATO
            ) AS SALDO_ANTERIOR,
            (
                SELECT
                    SUM(MONTO)
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO = 2
            ) AS COMISION,
            (
                NVL((SELECT
                    SUM(
                        CASE MA.MOVIMIENTO
                            WHEN '0' THEN -MA.MONTO
                            ELSE MA.MONTO
                        END 
                    )
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND T.CDG_CONTRATO = MA.CDG_CONTRATO), 0)
                +
                NVL((SELECT
                    SUM(
                        CASE MA.MOVIMIENTO
                            WHEN '0' THEN -MA.MONTO
                            ELSE MA.MONTO
                        END 
                    )
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    TO_NUMBER(CDG_TICKET) < TO_NUMBER(T.CODIGO)
                    AND T.CDG_CONTRATO = MA.CDG_CONTRATO), 0)
            ) AS SALDO_NUEVO,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'ENVIÓ A INVERSIÓN'
                        ELSE CASE MOVIMIENTO
                            WHEN '0' THEN 'RET. DE CTA. AHORRO'
                            ELSE 'DEP. A CTA. AHORRO'
                        END
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS ES_DEPOSITO,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'TRANSFERENCIA'
                        ELSE 'EFECTIVO'
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS METODO,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'APERTURADO POR'
                        ELSE CASE MOVIMIENTO
                            WHEN '0' THEN 'ENTREGAMOS'
                            ELSE 'RECIBIMOS'
                        END
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS ENTREGA,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'Atendió'
                        ELSE CASE MOVIMIENTO
                            WHEN '0' THEN 'Entrego'
                            ELSE 'Recibió'
                        END
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS RECIBIO,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'INVERSIÓN'
                        ELSE CASE MOVIMIENTO
                            WHEN '0' THEN 'RETIRO'
                            ELSE 'DEPÓSITO'
                        END
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS COMPROBANTE,
            (
                SELECT
                    DISTINCT CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE
                FROM
                    PE
                WHERE
                    PE.CODIGO = T.CDGPE
            ) AS NOM_EJECUTIVO,
            (
                SELECT
                    CASE CDG_TIPO_PAGO
                        WHEN '5' THEN 'APERTURA CUENTA DE INVERSIÓN'
                        ELSE CASE APA.CDGPR_PRIORITARIO
                            WHEN '1' THEN 'CUENTA DE AHORRO CORRIENTE'
                            WHEN '2' THEN 'CUENTA DE AHORRO PEQUE'
                            ELSE 'NO DEFINIDO'
                        END
                    END
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS PRODUCTO,
            T.CDGPE AS COD_EJECUTIVO
        FROM
            TICKETS_AHORRO T,
            ASIGNA_PROD_AHORRO APA,
            CL
        WHERE
            CL.CODIGO = APA.CDGCL
            AND T.CDG_CONTRATO = APA.CONTRATO
            AND T.CODIGO = '$ticket'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryOne($query);
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function BuscaClienteNvoContratoPQ($datos)
    {
        $queryValidacion = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CURP,
            TO_CHAR(CL.REGISTRO, 'DD/MM/YYYY') AS FECHA_REGISTRO,
            UPPER((CL.CALLE
            || ', '
            || COL.NOMBRE
            || ', '
            || LO.NOMBRE
            || ', '
            || MU.NOMBRE
            || ', '
                || EF.NOMBRE)) AS DIRECCION,
            (
                SELECT
                    COUNT(*)
                FROM
                    ASIGNA_PROD_AHORRO
                WHERE
                    CDGCL = CL.CODIGO
                    AND CDGPR_PRIORITARIO = 2
            ) AS HIJAS,
            NVL(
                (SELECT
                    COUNT(MA.CDG_CONTRATO)
                FROM
                    MOVIMIENTOS_AHORRO MA
                WHERE
                    MA.CDG_TIPO_PAGO = 2
                    AND MA.CDG_CONTRATO = (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1)
            ),0) AS CONTRATO_COMPLETO,
            (
                SELECT
                    COUNT(APA.CONTRATO)
                FROM
                    ASIGNA_PROD_AHORRO APA
                WHERE
                    APA.CDGCL = CL.CODIGO
                    AND CDGPR_PRIORITARIO = 1
            ) AS NO_CONTRATOS
        FROM
            CL,
            COL,
            LO,
            MU,
            EF
        WHERE
            EF.CODIGO = CL.CDGEF
            AND MU.CODIGO = CL.CDGMU
            AND LO.CODIGO = CL.CDGLO
            AND COL.CODIGO = CL.CDGCOL
            AND EF.CODIGO = MU.CDGEF
            AND EF.CODIGO = LO.CDGEF
            AND EF.CODIGO = COL.CDGEF
            AND MU.CODIGO = LO.CDGMU
            AND MU.CODIGO = COL.CDGMU
            AND LO.CODIGO = COL.CDGLO
            AND CL.CODIGO = '{$datos['cliente']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion);
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}.");
            if ($res['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro.", $res);
            if ($res['NO_CONTRATOS'] >= 1 && $res['CONTRATO_COMPLETO'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no ha concluido el proceso de apertura de su cuenta de ahorro.", $res);
            return self::Responde(true, "Consulta realizada correctamente.", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente.", null, $e->getMessage());
        }
    }

    public static function AgregaContratoAhorroPQ($datos)
    {
        $queryValidacion = <<<sql
        SELECT
            *
        FROM
            CL_PQS
        WHERE
            CDGCL = :cliente
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($queryValidacion, ['cliente' => $datos['credito']]);
            if ($res) {
                foreach ($res as $key => $value) {
                    if ($value['CURP'] == $datos['curp']) {
                        return self::Responde(false, "El cliente (Peque), ya tiene registrada una cuenta de ahorro con el contrato: " . $value['CDG_CONTRATO'] . ".");
                    }
                }
            }

            $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd') . str_pad((count($res) + 1), 2, '0', STR_PAD_LEFT);

            $queryAPA = <<<sql
            INSERT INTO ASIGNA_PROD_AHORRO
                (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO)
            VALUES
                (:contrato, :cliente, SYSDATE, '2', 'A', 0)
            sql;

            $queryCL_PQ = <<<sql
            INSERT INTO CL_PQS
                (CDGCL,CDG_CONTRATO,NOMBRE1,NOMBRE2,APELLIDO1,APELLIDO2,FECHA_NACIMIENTO,SEXO,CURP,PAIS,ENTIDAD,FECHA_REGISTRO,FECHA_MODIFICACION,ESTATUS)
            VALUES
                (:cliente, :contrato, :nombre1, :nombre2, :apellido1, :apellido2, :fecha_nacimiento, :sexo, :curp, :pais, :entidad, SYSDATE, SYSDATE, 'A')
            sql;

            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_nac']);
            $fecha = $fecha !== false && $fecha->format('Y-m-d') === $datos['fecha_nac'] ? $fecha->format('d-m-Y') : $datos['fecha_nac'];
            $sexo = $datos['sexo'] === true || $datos['sexo'] === 'true';

            $parametros = [
                [
                    'contrato' => $noContrato,
                    'cliente' => $datos['credito']
                ],
                [
                    'cliente' => $datos['credito'],
                    'contrato' => $noContrato,
                    'nombre1' => $datos['nombre1'],
                    'nombre2' => $datos['nombre2'],
                    'apellido1' => $datos['apellido1'],
                    'apellido2' => $datos['apellido2'],
                    'fecha_nacimiento' => $fecha,
                    'sexo' => $sexo ? 'H' : 'M',
                    'curp' => $datos['curp'],
                    'pais' => $datos['pais'],
                    'entidad' => $datos['entidad']
                ]
            ];

            $inserts = [
                $queryAPA,
                $queryCL_PQ
            ];

            try {
                $mysqli = Database::getInstance();
                $res = $mysqli->insertaMultiple($inserts, $parametros);
                if ($res) return self::Responde(true, "Contrato de ahorro registrado correctamente.", ['contrato' => $noContrato]);
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.");
            } catch (Exception $e) {
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.", null, $e->getMessage());
            }
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro.", null, $e->getMessage());
        }
    }

    public static function BuscaClienteContratoPQ($datos)
    {
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(CL_PQS.NOMBRE1, CL_PQS.NOMBRE2, CL_PQS.APELLIDO1, CL_PQS.APELLIDO2) AS NOMBRE,
            CL_PQS.CURP,
            CL_PQS.CDG_CONTRATO,
            CL_PQS.CDGCL,
            NVL((
                SELECT
                    SALDO
                FROM
                    ASIGNA_PROD_AHORRO APA
                WHERE
                    APA.CONTRATO = CL_PQS.CDG_CONTRATO
            ),0) AS SALDO
        FROM
            CL_PQS
        WHERE
            CL_PQS.CDGCL = '{$datos['cliente']}'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if (count($res) === 0) {
                $qryVal = <<<sql
                SELECT
                    CL.CODIGO,
                    NVL(
                        (SELECT
                            COUNT(MA.CDG_CONTRATO)
                        FROM
                            MOVIMIENTOS_AHORRO MA
                        WHERE
                            MA.CDG_TIPO_PAGO = 2
                            AND MA.CDG_CONTRATO = (SELECT CONTRATO FROM ASIGNA_PROD_AHORRO WHERE CDGCL = CL.CODIGO AND CDGPR_PRIORITARIO = 1)
                    ),0) AS CONTRATO_COMPLETO,
                    (
                        SELECT
                            COUNT(APA.CONTRATO)
                        FROM
                            ASIGNA_PROD_AHORRO APA
                        WHERE
                            APA.CDGCL = CL.CODIGO
                            AND CDGPR_PRIORITARIO = 1
                    ) AS NO_CONTRATOS
                FROM
                    CL
                WHERE
                    CL.CODIGO = '{$datos['cliente']}'
                sql;

                $res2 = $mysqli->queryOne($qryVal);
                if (!$res2) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}.");
                if ($res2['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro.", $res2);
                if ($res2['NO_CONTRATOS'] >= 1 && $res2['CONTRATO_COMPLETO'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no ha concluido el proceso de apertura de su cuenta de ahorro.", $res2);
                return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con cuentas de ahorro Peques.", $res2);
            }
            return self::Responde(true, "Consulta realizada correctamente.", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente.", null, $e->getMessage());
        }
    }

    public static function RegistraInversion($datos)
    {
        $qryInversion = <<<sql
        INSERT INTO CUENTA_INVERSION
            (CDG_CONTRATO, CDG_TASA, MONTO_INVERSION, FECHA_APERTURA, ESTATUS, ACCION)
        VALUES
            (:contrato, :tasa, :monto, SYSDATE, 'A', :accion)
        sql;

        $query = [
            $qryInversion,
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $datosInsert = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'tasa' => $datos['tasa'],
                'accion' => $datos['renovacion']
            ],
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'ejecutivo' => $datos['ejecutivo'],
                'sucursal' => $datos['sucursal']
            ],
            [
                'tipo_pago' => '5',
                'contrato' => $datos['contrato'],
                'monto' => $datos['montoOperacion'],
                'movimiento' => '0'
            ]
        ];

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datosInsert);
            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "Inversión registrada correctamente.", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar la inversión.");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar la inversión.", null, $e->getMessage());
        }
    }

    public static function GetInversiones($datos)
    {
        $query = <<<sql
        SELECT
            TO_CHAR(CI.FECHA_APERTURA, 'DD/MM/YYYY') AS APERTURA,
            CI.MONTO_INVERSION AS MONTO,
            (SELECT TASA FROM TASA_INVERSION WHERE CODIGO = CI.CDG_TASA) AS TASA,
            (SELECT PLAZO FROM PLAZO_INVERSION WHERE CODIGO = (SELECT CDG_PLAZO FROM TASA_INVERSION WHERE CODIGO =CI.CDG_TASA)) AS PLAZO,
            (SELECT CASE PERIODICIDAD WHEN 'D' THEN 'Días' WHEN 'S' THEN 'Semanas' WHEN 'M' THEN 'Meses' WHEN 'A' THEN 'Años' ELSE 'No definido' END FROM PLAZO_INVERSION WHERE CODIGO = (SELECT CDG_PLAZO FROM TASA_INVERSION WHERE CODIGO =CI.CDG_TASA)) AS PERIODICIDAD,
            TO_CHAR(CI.FECHA_VENCIMIENTO, 'DD/MM/YYYY') AS VENCIMIENTO,
            NVL(CI.RENDIMIENTO,0) AS RENDIMIENTO,
            TO_CHAR(CI.FECHA_LIQUIDACION, 'DD/MM/YYYY') AS LIQUIDACION,
            CASE CI.ACCION WHEN 'D' THEN 'Depósito' WHEN 'R' THEN 'Renovación' ELSE 'No aplica' END AS ACCION 
        FROM
            CUENTA_INVERSION CI
        WHERE
            CI.CDG_CONTRATO = '{$datos['contrato']}'
        ORDER BY
            CI.FECHA_VENCIMIENTO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if (count($res) === 0) return self::Responde(false, "No se encontraron inversiones para el contrato {$datos['contrato']}.");
            return self::Responde(true, "Consulta realizada correctamente.", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar las inversiones.", null, $e->getMessage());
        }
    }

    public static function DatosContrato($contrato)
    {
        $query = <<<sql
        SELECT
            APA.CONTRATO,
            APA.CDGCL,
            TO_CHAR(APA.FECHA_APERTURA, 'DD/MM/YYYY') AS FECHA_APERTURA,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_CLIENTE,
            (
                SELECT
                    MONTO
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    CDG_TIPO_PAGO = 1
                    AND CDG_CONTRATO = APA.CONTRATO
            ) AS DEP_INICIAL,
            (
                SELECT
                    MONTO
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    CDG_TIPO_PAGO = 2
                    AND CDG_CONTRATO = APA.CONTRATO
            ) AS COMISION,
            (
                SELECT
                    SUM(CASE MOVIMIENTO
                        WHEN '0' THEN -MONTO
                        ELSE MONTO
                    END)
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    (CDG_TIPO_PAGO = 1
                    OR CDG_TIPO_PAGO = 2)
                    AND CDG_CONTRATO = APA.CONTRATO
            ) AS SALDO_INICIAL
        FROM
            ASIGNA_PROD_AHORRO APA
        LEFT JOIN
            CL
        ON
            CL.CODIGO = APA.CDGCL
        WHERE
            APA.CONTRATO = '{$contrato}'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryOne($query);
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function getSucursal($noSuc)
    {
        $query = <<<sql
        SELECT
            CODIGO, 
            NOMBRE
        FROM
            CO
        WHERE
            CODIGO = '$noSuc'
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryOne($query);
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function RegistraSolicitud($datos)
    {
        $qrySolicitud = <<<sql
        INSERT INTO ESIACOM.SOLICITUD_RETIRO_AHORRO
            (ID_SOL_RETIRO_AHORRO, CONTRATO, FECHA_SOLICITUD, CANTIDAD_SOLICITADA, AUTORIZACION_CLIENTE, CDGPE, ESTATUS, FECHA_ESTATUS, PRORROGA, TIPO_RETIRO)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO)),0) FROM SOLICITUD_RETIRO_AHORRO) + 1, :contrato, :fecha_solicitud, :monto, NULL, :ejecutivo, 0, SYSDATE, 0, :tipo_retiro)
        sql;
        $qryTicket = self::GetQueryTicket();
        $qryMovimiento = self::GetQueryMovimientoAhorro();

        $tipoRetiro = $datos['retiroExpress'] === true || $datos['retiroExpress'] === 'true' ? 1 : 2;

        $datosSolicitud = [
            'contrato' => $datos['contrato'],
            'fecha_solicitud' => $datos['fecha_retiro'],
            'monto' => $datos['monto'],
            'ejecutivo' => $datos['ejecutivo'],
            'tipo_retiro' => $tipoRetiro
        ];

        $datosTicket = [
            'contrato' => $datos['contrato'],
            'monto' => $datos['monto'],
            'ejecutivo' => $datos['ejecutivo'],
            'sucursal' => $datos['sucursal']
        ];

        $datosMovimiento = [
            'tipo_pago' => $tipoRetiro === 1 ? '6' : '7',
            'contrato' => $datos['contrato'],
            'monto' => $datos['monto'],
            'movimiento' => '0'
        ];

        $query = [
            $qrySolicitud,
            $qryTicket,
            $qryMovimiento
        ];

        $datosInsert = [
            $datosSolicitud,
            $datosTicket,
            $datosMovimiento
        ];

        $tipoMov = $tipoRetiro === 1 ? "express" : "programado";
        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->insertaMultiple($query, $datosInsert);
            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "El retiro " . $tipoMov . " fue registrado correctamente.", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar el retiro " . $tipoMov . ".");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el retiro " . $tipoMov  . ".", null, $e->getMessage());
        }
    }

    public static function DetalleMovimientosXdia()
    {
        $qry = <<<sql
        SELECT
            MA.MOVIMIENTO,
            TPA.CODIGO AS CODOP,
            TPA.DESCRIPCION AS OPERACION,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CODIGO AS CLIENTE,
            TO_CHAR(MA.FECHA_MOV,'DD/MM/YYYY HH24:MI:SS') AS FECHA,
            MA.MONTO,
            'AUT_CLIENTE' AS AUTORIZACION
        FROM
            MOVIMIENTOS_AHORRO MA
            INNER JOIN CL ON CL.CODIGO = (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = MA.CDG_CONTRATO)
            INNER JOIN TIPO_PAGO_AHORRO TPA ON TPA.CODIGO = MA.CDG_TIPO_PAGO
        WHERE
            MA.FECHA_MOV >= TRUNC(SYSDATE) AND MA.FECHA_MOV < TRUNC(SYSDATE) + 1
        ORDER BY
            MA.CODIGO
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($qry);
        } catch (Exception $e) {
            return array();
        }
    }

    public static function HistoricoSolicitudRetiro()
    {
        $qry = <<<sql
        SELECT
            CASE SR.TIPO_RETIRO
                WHEN 1 THEN 'EXPRESS'
                WHEN 2 THEN 'PROGRAMADO'
                ELSE 'NO DEFINIDO'
            END AS TIPO_RETIRO,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
            CL.CODIGO AS CLIENTE,
            TO_CHAR(SR.FECHA_SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOLICITUD,
            SR.CANTIDAD_SOLICITADA AS MONTO,
            CASE SR.ESTATUS
                WHEN 0 THEN 'SOLICITADO'
                WHEN 1 THEN 'APROBADO'
                WHEN 2 THEN 'APROBADO CON CAMBIOS'
                WHEN 3 THEN 'RECHAZADO'
                ELSE 'NO DEFINIDO'
            END AS ESTATUS
        FROM
            SOLICITUD_RETIRO_AHORRO SR
            INNER JOIN CL ON CL.CODIGO = (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = SR.CONTRATO)
        ORDER BY
            SR.FECHA_ESTATUS DESC
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($qry);
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetLogTransacciones($parametros)
    {
        $qry = <<<sql
        SELECT
            TO_CHAR(LTA.FECHA_TRANSACCION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA,
            LTA.SUCURSAL,
            LTA.USUARIO,
            (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = LTA.CONTRATO) AS CLIENTE,
            LTA.CONTRATO,
            LTA.TIPO
        FROM
            LOG_TRANSACCIONES_AHORRO LTA
        WHERE
            TRUNC(LTA.FECHA_TRANSACCION) BETWEEN TO_DATE(:fecha_inicio, 'YYYY-MM-DD') AND TO_DATE(:fecha_fin, 'YYYY-MM-DD')
        sql;

        $qry .= $parametros["operacion"] ? " AND LTA.TIPO = :operacion" : "";
        $qry .= $parametros["usuario"] ? " AND LTA.USUARIO = :usuario" : "";
        $qry .= $parametros["sucursal"] ? " AND LTA.SUCURSAL = :sucursal" : "";

        try {
            $mysqli = Database::getInstance();
            $resultado = $mysqli->queryAll($qry, $parametros);
            if (count($resultado) === 0) return self::Responde(false, "No se encontraron registros para la consulta.", $qry);
            return self::Responde(true, "Consulta realizada correctamente.", $resultado);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los registros.", null, $e->getMessage());
        }
    }

    public static function GetDatosEdoCta($cliente)
    {
        $qryDatosGenerale = <<<sql
            SELECT
                CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
                CL.CODIGO AS CLIENTE,
                APA.CONTRATO,
                APA.SALDO
            FROM
                ASIGNA_PROD_AHORRO APA
                INNER JOIN CL ON CL.CODIGO = APA.CDGCL
            WHERE
                APA.CDGCL = '$cliente'
                AND APA.CDGPR_PRIORITARIO = 1
                AND APA.ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qryDatosGenerale);
            if (!$res) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetMovimientosAhorro($contrato, $fI, $fF)
    {
        // (
        //     SELECT
        //         DESCRIPCION
        //     FROM
        //         TIPO_PAGO_AHORRO
        //     WHERE
        //         CODIGO = MA.CDG_TIPO_PAGO
        // ) AS DESCRIPCION,
        $qryMovimientos = <<<sql
        SELECT
            *
        FROM (
            SELECT
                TO_CHAR(MA.FECHA_MOV, 'DD/MM/YYYY') AS FECHA,
                CONCAT(
                        (SELECT DESCRIPCION
                        FROM TIPO_PAGO_AHORRO
                        WHERE CODIGO = MA.CDG_TIPO_PAGO),  CASE 
                    WHEN SRA.FECHA_SOLICITUD IS NULL THEN ''
                    ELSE TO_CHAR(SRA.FECHA_SOLICITUD, ' - DD/MM/YYYY')
                    END 
                    )
                AS DESCRIPCION,
                CASE MA.MOVIMIENTO
                    WHEN '0' THEN MA.MONTO
                    ELSE 0
                END AS CARGO,
                CASE MA.MOVIMIENTO
                    WHEN '1' THEN MA.MONTO
                    ELSE 0
                END AS ABONO,
                SUM(
                    CASE MA.MOVIMIENTO
                        WHEN '0' THEN -MA.MONTO
                        WHEN '1' THEN MA.MONTO
                    END
                ) OVER (ORDER BY MA.FECHA_MOV, MA.MOVIMIENTO DESC) AS SALDO
            FROM
                MOVIMIENTOS_AHORRO MA
                INNER JOIN TIPO_PAGO_AHORRO TPA ON TPA.CODIGO = MA.CDG_TIPO_PAGO
                LEFT JOIN SOLICITUD_RETIRO_AHORRO SRA ON SRA.ID_SOL_RETIRO_AHORRO = MA.CDG_RETIRO 
            WHERE
                MA.CDG_CONTRATO = '$contrato'
            ORDER BY
                MA.FECHA_MOV, MA.MOVIMIENTO DESC
        ) WHERE TO_DATE(FECHA, 'DD/MM/YYYY') BETWEEN TO_DATE('$fI', 'DD/MM/YYYY') AND TO_DATE('$fF', 'DD/MM/YYYY')
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qryMovimientos);
            if (count($res) === 0) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetMovimientosInversion($contrato)
    {
        $qryMovimientos = <<<sql
        SELECT
            TO_CHAR(FECHA_APERTURA, 'DD/MM/YYYY') AS FECHA_APERTURA,
            TO_CHAR(FECHA_VENCIMIENTO, 'DD/MM/YYYY') AS FECHA_VENCIMIENTO,
            NVL(MONTO_INVERSION, 0) AS MONTO,
            (
                SELECT
                    CONCAT(
                        CONCAT(PI.PLAZO, ' '),
                        CASE PI.PERIODICIDAD
                            WHEN 'D' THEN 'Días'
                            WHEN 'S' THEN 'Semanas'
                            WHEN 'M' THEN 'Meses'
                            WHEN 'A' THEN 'Años'
                        END
                    )
                FROM
                    PLAZO_INVERSION PI
                WHERE
                    CODIGO = (
                        SELECT
                            TI.CDG_PLAZO
                        FROM
                            TASA_INVERSION TI
                        WHERE
                            CODIGO = CI.CDG_TASA
                    )
            ) AS PLAZO,
            (
                SELECT
                    TASA
                FROM
                    TASA_INVERSION
                WHERE
                    CODIGO = CI.CDG_TASA
            ) AS TASA,
            CASE CI.ESTATUS
                WHEN 'A' THEN 'Activa'
                WHEN 'C' THEN 'Cancelada'
                WHEN 'L' THEN 'Liquidada'
                ELSE 'No definido'
            END AS ESTATUS,
            TO_CHAR(FECHA_LIQUIDACION, 'DD/MM/YYYY') AS FECHA_LIQUIDACION,
            NVL(RENDIMIENTO,0) AS RENDIMIENTO,
            CASE ACCION
                WHEN 'D' THEN 'Cuenta ahorro'
                WHEN 'R' THEN 'Renovación'
                ELSE 'No definido'
            END AS ACCION
        FROM
            CUENTA_INVERSION CI
        WHERE
            CDG_CONTRATO = '$contrato'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qryMovimientos);
            if (count($res) === 0) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetCuentasPeque($contrato)
    {
        $qryCuentas = <<<sql
        SELECT
            APA.CONTRATO,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.APELLIDO1, CL.APELLIDO2) AS NOMBRE,
            TO_CHAR(APA.FECHA_APERTURA, 'DD/MM/YYYY') AS FECHA_APERTURA,
            APA.SALDO
        FROM
            ASIGNA_PROD_AHORRO APA
            RIGHT JOIN CL_PQS CL ON CL.CDG_CONTRATO = APA.CONTRATO
        WHERE
            APA.CDGCL = '$contrato'
            AND APA.CDGPR_PRIORITARIO = 2
            AND APA.ESTATUS = 'A'
        ORDER BY
            APA.CONTRATO
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qryCuentas);
            if (count($res) === 0) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetMovimientosPeque($contrato, $fI, $fF)
    {
        $qryMovimientos = <<<sql
        SELECT * FROM (
            SELECT
                TO_CHAR(MA.FECHA_MOV, 'DD/MM/YYYY') AS FECHA,
                (
                    SELECT
                        DESCRIPCION
                    FROM
                        TIPO_PAGO_AHORRO
                    WHERE
                        CODIGO = MA.CDG_TIPO_PAGO
                ) AS DESCRIPCION,
                CASE MA.MOVIMIENTO
                    WHEN '0' THEN MA.MONTO
                    ELSE 0
                END AS CARGO,
                CASE MA.MOVIMIENTO
                    WHEN '1' THEN MA.MONTO
                    ELSE 0
                END AS ABONO,
                SUM(
                    CASE MA.MOVIMIENTO
                        WHEN '0' THEN -MA.MONTO
                        WHEN '1' THEN MA.MONTO
                    END
                ) OVER (ORDER BY MA.FECHA_MOV, MA.MOVIMIENTO DESC) AS SALDO
            FROM
                MOVIMIENTOS_AHORRO MA
                INNER JOIN TIPO_PAGO_AHORRO TPA ON TPA.CODIGO = MA.CDG_TIPO_PAGO
            WHERE
                MA.CDG_CONTRATO = '$contrato'
            ORDER BY
                MA.FECHA_MOV, MA.MOVIMIENTO DESC
        ) WHERE TO_DATE(FECHA, 'DD/MM/YYYY') BETWEEN TO_DATE('$fI', 'DD/MM/YYYY') AND TO_DATE('$fF', 'DD/MM/YYYY')
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qryMovimientos);
            if (count($res) === 0) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSaldoMinimoApertura($sucursal)
    {
        $qry = <<<sql
        SELECT
            NVL(MONTO_MINIMO, 100),
            NVL(MONTO_MAXIMO, 1000)
        FROM
            PARAMETROS_AHORRO
        WHERE
            CDG_SUCURSAL = '$sucursal'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if (!$res) return ['MONTO_MINIMO' => 300, 'MONTO_MAXIMO' => 10000];
            return $res;
        } catch (Exception $e) {
            return ['MONTO_MINIMO' => 300, 'MONTO_MAXIMO' => 10000];
        }
    }
    public static function GetAllTransacciones($usuario)
    {
        if ($usuario == '') {
            $var =  '';
        } else {
            $var = "WHERE 
            SUC_CAJERA_AHORRO.CDG_USUARIO = '" . $usuario . "' 
            AND OPERAC
            
            
            ";
        }

        $query = <<<sql
        SELECT ma.CDG_CONTRATO, c.CODIGO AS CDGCL, (c.NOMBRE1 || ' ' || c.NOMBRE2 || ' ' || c.PRIMAPE || ' ' || c.SEGAPE) AS TITULAR_CUENTA_EJE, 
        ma.FECHA_MOV, ma.CDG_TICKET, ma.MONTO, tpa.DESCRIPCION AS CONCEPTO, pp.DESCRIPCION AS PRODUCTO, '' AS INCIDENCIA FROM MOVIMIENTOS_AHORRO ma
        INNER JOIN TIPO_PAGO_AHORRO tpa ON tpa.CODIGO = ma.CDG_TIPO_PAGO 
        INNER JOIN ASIGNA_PROD_AHORRO apa ON apa.CONTRATO = ma.CDG_CONTRATO 
        INNER JOIN PR_PRIORITARIO pp ON pp.CODIGO = apa.CDGPR_PRIORITARIO 
        INNER JOIN CL c ON c.CODIGO = apa.CDGCL 
        ORDER BY ma.FECHA_MOV ASC
sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSolicitudesPendientesAdminAll()
    {
        $query = <<<sql
        SELECT tar.CDGTICKET_AHORRO, apa.CONTRATO, (c.NOMBRE1 || ' ' || c.NOMBRE2 || ' ' || c.PRIMAPE || ' ' || c.SEGAPE) AS NOMBRE_CLIENTE,
        tar.MOTIVO, ta.MONTO, tar.DESCRIPCION_MOTIVO, tar.FREGISTRO  FROM TICKETS_AHORRO_REIMPRIME tar 
        INNER JOIN TICKETS_AHORRO ta ON ta.CODIGO = tar.CDGTICKET_AHORRO 
        INNER JOIN ASIGNA_PROD_AHORRO apa ON apa.CONTRATO = ta.CDG_CONTRATO 
        INNER JOIN CL c ON c.CODIGO = apa.CDGCL 
        INNER JOIN PE p ON p.CODIGO = tar.CDGPE_SOLICITA 
        WHERE p.CDGEM = 'EMPFIN'
        AND tar.AUTORIZA = '0'
           
sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    public static function GetSolicitudesHistorialAdminAll()
    {
        $query = <<<sql
        SELECT tar.CDGTICKET_AHORRO, apa.CONTRATO, (c.NOMBRE1 || ' ' || c.NOMBRE2 || ' ' || c.PRIMAPE || ' ' || c.SEGAPE) AS NOMBRE_CLIENTE,
        tar.MOTIVO, ta.MONTO, tar.DESCRIPCION_MOTIVO, tar.FREGISTRO, tar.AUTORIZA  FROM TICKETS_AHORRO_REIMPRIME tar 
        INNER JOIN TICKETS_AHORRO ta ON ta.CODIGO = tar.CDGTICKET_AHORRO 
        INNER JOIN ASIGNA_PROD_AHORRO apa ON apa.CONTRATO = ta.CDG_CONTRATO 
        INNER JOIN CL c ON c.CODIGO = apa.CDGCL 
        INNER JOIN PE p ON p.CODIGO = tar.CDGPE_SOLICITA 
        WHERE p.CDGEM = 'EMPFIN'
        AND tar.AUTORIZA != '0'
        ORDER BY tar.FREGISTRO
           
sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($query);
            if ($res) return $res;
            return array();
        } catch (Exception $e) {
            return array();
        }
    }


}

class LogTransaccionesAhorro
{
    public function BindingQuery($qry, $parametros = null)
    {
        if ($parametros) {
            foreach ($parametros as $parametro => $valor)
                $qry = str_replace(":" . $parametro, "'" . $valor . "'", $qry);
        }

        return $qry;
    }

    public static function LogTransaccion($datos)
    {
        $qry = <<<sql
        INSERT INTO LOG_TRANSACCIONES_AHORRO (
            ID_TRANSACCION,
            FECHA_TRANSACCION,
            QUERY_TRANSACCION,
            SUCURSAL,
            USUARIO,
            CONTRATO,
            MODULO,
            TIPO
        )
        VALUES (
            (SELECT NVL(MAX(TO_NUMBER(ID_TRANSACCION)),0) FROM LOG_TRANSACCIONES_AHORRO) + 1,
            SYSDATE,
            :query,
            :sucursal,
            :usuario,
            :contrato,
            :modulo,
            :tipo_transaccion
        )
        sql;

        $parametros = [
            'query' => self::BindingQuery($datos['query'], $datos['parametros']),
            'sucursal' => $datos['sucursal'],
            'usuario' => $datos['usuario'],
            'contrato' => $datos['contrato'],
            'modulo' => $datos['modulo'],
            'tipo_transaccion' => $datos['tipo']
        ];

        try {
            $db = Database::getInstance();
            $db->insertar($qry, $parametros);
            return [$qry, $parametros];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public static function LogTransacciones($qyrs, $parametros, $sucursal, $usuario, $contrato, $tipo)
    {
        $tmp = [];
        foreach ($qyrs as $qry => $q) {
            $log['query'] = $q;
            $log['parametros'] = $parametros[$qry];
            $log['sucursal'] = $sucursal;
            $log['usuario'] = $usuario;
            $log['contrato'] = $contrato;
            $log['modulo'] = debug_backtrace()[1]['function'];
            $log['tipo'] = $tipo;
            $tmp[] = self::LogTransaccion($log);
        }
        return $tmp;
    }
}
