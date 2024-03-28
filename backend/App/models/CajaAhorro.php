<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Exception;
use DateTime;

class CajaAhorro
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
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}");
            if ($res['NO_CONTRATOS'] >= 1) return self::Responde(false, "El cliente {$datos['cliente']} ya cuenta con un contrato de ahorro", $res);

            if ($res) return self::Responde(true, "Consulta realizada correctamente", $res);
            return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
        }
    }

    public static function BuscaClienteContrato($datos)
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
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}");
            if ($res['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro");
            return self::Responde(true, "Consulta realizada correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
        }
    }

    public static function AgregaContratoAhorro($datos)
    {
        $queryValidacion = <<<sql
        SELECT
            *
        FROM
            ASIGNA_PROD_AHORRO
        WHERE
            CDGCL = :cliente
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($queryValidacion, ['cliente' => $datos['credito']]);
            if ($res) return self::Responde(false, "El cliente ya cuenta con un contrato de ahorro");

            $noContrato = $datos['credito'] . date('Ymd');

            $query = <<<sql
            INSERT INTO ASIGNA_PROD_AHORRO
                (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO)
            VALUES
                (:contrato, :cliente, :fecha_apertura, '1', 'A', 0)
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
                    'fecha_apertura' => $fecha
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
                if ($res) return self::Responde(true, "Contrato de ahorro registrado correctamente", ['contrato' => $noContrato]);
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro");
            } catch (Exception $e) {
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro", null, $e->getMessage());
            }
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro", null, $e->getMessage());
        }
    }

    public static function AddPagoApertura($datos)
    {
        if ($datos['deposito_inicial'] == 0) return self::Responde(false, "El monto de apertura no puede ser de 0");
        if ($datos['saldo_inicial'] < $datos['sma']) return self::Responde(false, "El saldo inicial no puede ser menor a " . $datos['sma']);

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
                'monto' => $datos['deposito_inicial'],
                'ejecutivo' => $datos['ejecutivo']
            ],
            [
                'tipo_pago' => '1',
                'contrato' => $datos['contrato'],
                'monto' => $datos['deposito'],
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
                return self::Responde(true, "Pago de apertura registrado correctamente", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura");
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el pago de apertura", null, $e->getMessage());
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
                'ejecutivo' => $datos['ejecutivo']
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
                return self::Responde(true, "El " . $tipoMov . " fue registrado correctamente", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el " . $tipoMov, null, $e->getMessage());
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
        $resultado['mensaje'] = "Se detecto diferencia entre el registro del ticket y los movimiento de ahorro";
        return $resultado;
    }

    public static function GetQueryTicket()
    {
        return <<<sql
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, :ejecutivo)
        sql;
    }

    public static function GetQueryMovimientoAhorro()
    {
        return <<<sql
        INSERT INTO MOVIMIENTOS_AHORRO
            (CODIGO, FECHA_MOV, CDG_TIPO_PAGO, CDG_CONTRATO, MONTO, MOVIMIENTO, DESCRIPCION, CDG_TICKET, FECHA_VALOR)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM MOVIMIENTOS_AHORRO) + 1, SYSDATE, :tipo_pago, :contrato, :monto, :movimiento, 'ALGUNA_DESCRIPCION', (SELECT MAX(TO_NUMBER(CODIGO)) AS CODIGO FROM TICKETS_AHORRO WHERE CDG_CONTRATO = :contrato), SYSDATE)
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
                SELECT
                    MOVIMIENTO
                FROM
                    MOVIMIENTOS_AHORRO
                WHERE
                    TO_NUMBER(CDG_TICKET) = TO_NUMBER(T.CODIGO)
                    AND CDG_TIPO_PAGO != 2
            ) AS ES_DEPOSITO,
            (
                SELECT
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE
                FROM
                    PE
                WHERE
                    PE.CODIGO = T.CDGPE
            ) AS NOM_EJECUTIVO,
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
            TO_CHAR(CL.REGISTRO, 'DD-MM-YYYY') AS FECHA_REGISTRO,
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
            if (!$res) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}");
            if ($res['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro", $res);
            if ($res['NO_CONTRATOS'] >= 1 && $res['CONTRATO_COMPLETO'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no ha concluido el proceso de apertura de su cuenta de ahorro", $res);
            return self::Responde(true, "Consulta realizada correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
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
                        return self::Responde(false, "El PQ ya cuenta con una cuenta de ahorro");
                    }
                }
            }

            $noContrato = $datos['credito'] . date_format(date_create($datos['fecha']), 'Ymd') . str_pad((count($res) + 1), 2, '0', STR_PAD_LEFT);

            $queryAPA = <<<sql
            INSERT INTO ASIGNA_PROD_AHORRO
                (CONTRATO, CDGCL, FECHA_APERTURA, CDGPR_PRIORITARIO, ESTATUS, SALDO)
            VALUES
                (:contrato, :cliente, :fecha_apertura, '2', 'A', 0)
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
                    'cliente' => $datos['credito'],
                    'fecha_apertura' => $fecha
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
                if ($res) return self::Responde(true, "Contrato de ahorro registrado correctamente", ['contrato' => $noContrato]);
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro");
            } catch (Exception $e) {
                return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro", null, $e->getMessage());
            }
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al validar si el cliente ya cuenta con un contrato de ahorro", null, $e->getMessage());
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
                if (!$res2) return self::Responde(false, "No se encontraron datos para el cliente {$datos['cliente']}");
                if ($res2['NO_CONTRATOS'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con un contrato de ahorro", $res2);
                if ($res2['NO_CONTRATOS'] >= 1 && $res2['CONTRATO_COMPLETO'] == 0) return self::Responde(false, "El cliente {$datos['cliente']} no ha concluido el proceso de apertura de su cuenta de ahorro", $res2);
                return self::Responde(false, "El cliente {$datos['cliente']} no cuenta con cuentas de ahorro Peques", $res2);
            }
            return self::Responde(true, "Consulta realizada correctamente", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Ocurrió un error al consultar los datos del cliente", null, $e->getMessage());
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////
}
