<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\Database_cultiva;
use Exception;

class AdminSucursales
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

    public static function GetSucursalesActivas()
    {
        $query = <<<sql
            SELECT
                TO_CHAR(FECHA_REGISTRO, 'DD/MM/YYYY') FECHA_REGISTRO,
                SEA.CDG_SUCURSAL,
                CO.NOMBRE,
                SCA.CDG_USUARIO,
                (
                    SELECT
                        CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE)
                    FROM 
                        PE
                    WHERE
                        PE.CODIGO = SCA.CDG_USUARIO
                        AND PE.CDGEM = 'EMPFIN'
                ) NOMBRE_CAJERA,
                TO_CHAR(TO_DATE(SCA.HORA_APERTURA, 'HH24:MI:SS'), 'HH:MI AM') HORA_APERTURA,
                TO_CHAR(TO_DATE(SCA.HORA_CIERRE, 'HH24:MI:SS'), 'HH:MI AM') HORA_CIERRE,
                TO_CHAR(TO_NUMBER(SEA.SALDO_MINIMO), 'FM$999,999,999.00') SALDO_MINIMO,
                TO_CHAR(TO_NUMBER(SEA.SALDO_MAXIMO), 'FM$999,999,999.00') SALDO_MAXIMO,
                NULL ACCIONES
            FROM
                SUC_ESTADO_AHORRO SEA
            JOIN
                CO ON CO.CODIGO = SEA.CDG_SUCURSAL
            RIGHT JOIN
                SUC_CAJERA_AHORRO SCA ON SCA.CDG_ESTADO_AHORRO = SEA.CODIGO
            WHERE
                SEA.ESTATUS = 'A'
            ORDER BY
               SEA.CDG_SUCURSAL
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function GetSucursales()
    {
        $query = <<<sql
            SELECT
                CO.CODIGO,
                CO.NOMBRE
            FROM
                CO
            WHERE
                CO.CODIGO NOT IN (
                    SELECT
                        CDG_SUCURSAL
                    FROM
                        SUC_ESTADO_AHORRO
                    WHERE
                        ESTATUS = 'A'
                    )
            ORDER BY
                CO.NOMBRE
        sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function GetMontoSucursal($sucursal)
    {
        $query = <<<sql
            SELECT
                SALDO_MINIMO,
                SALDO_MAXIMO
            FROM
                SUC_ESTADO_AHORRO
            WHERE
                CDG_SUCURSAL = '$sucursal'
                AND ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($query);
            if ($res) return self::Responde(true, "Monto de sucursal encontrado", $res);
            return self::Responde(false, "No se encontró monto de sucursal");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar monto de sucursal", null, $e->getMessage());
        }
    }

    public static function GetCajeras($sucursal)
    {
        $qry = <<<sql
        SELECT * FROM (
            SELECT DISTINCT 
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE, PE.CODIGO
            FROM
                PE,
                UT
            WHERE
            PE.CODIGO = UT.CDGPE
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL) 
            AND (PE.CDGCO = '$sucursal' OR (PE.CODIGO = 'LGFR' AND UT.CDGTUS = 'CAJA' ))
            ) 
        sql;


        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            return self::Responde(true, "Cajeras encontradas", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar cajeras", null, $e->getMessage());
        }
    }

    public static function GetHorarioCajera($cajera)
    {
        $qry = <<<sql
        SELECT
            HORA_APERTURA,
            HORA_CIERRE
        FROM
            SUC_CAJERA_AHORRO
        WHERE
            CDG_USUARIO = '$cajera'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if ($res) return self::Responde(true, "Horario de cajera encontrado", $res);
            else return self::Responde(false, "No se encontró horario de cajera");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar horario de cajera", null, $e->getMessage());
        }
    }

    public static function ActivarSucursal($datos)
    {
        $qrySuc = <<<sql
        INSERT INTO SUC_ESTADO_AHORRO
            (CODIGO, CDG_SUCURSAL, FECHA_REGISTRO, ESTATUS, SALDO_MINIMO, SALDO_MAXIMO)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) FROM SUC_ESTADO_AHORRO) + 1,
                :sucursal,
                SYSDATE,
                'A',
                :minimo,
                :maximo
            )
        sql;

        $qryCaj = <<<sql
        INSERT INTO SUC_CAJERA_AHORRO
            (CDG_ESTADO_AHORRO, CDG_USUARIO, HORA_APERTURA, HORA_CIERRE)
        VALUES
            (
                (SELECT MAX(TO_NUMBER(CODIGO)) FROM SUC_ESTADO_AHORRO),
                :cajera,
                :apertura,
                :cierre
            )
        sql;

        $qrys = [
            $qrySuc,
            $qryCaj
        ];

        $params = [
            [
                "sucursal" => $datos['sucursal'],
                "minimo" => $datos['montoMin'],
                "maximo" => $datos['montoMax']
            ],
            [
                "cajera" => $datos['cajera'],
                "apertura" => $datos['horaA'],
                "cierre" => $datos['horaC']
            ]
        ];

        try {
            $ora = Database::getInstance();
            $res = $ora->insertaMultiple($qrys, $params);
            if ($res) return self::Responde(true, "Sucursal activada correctamente");
            else return self::Responde(false, "Error al activar sucursal");
        } catch (Exception $e) {
            return self::Responde(false, "Error al activar sucursal", null, $e->getMessage());
        }
    }

    public static function GetDatosFondeoRetiro($datos)
    {
        $qry = <<<sql
        SELECT
            SEA.CODIGO,
            SEA.CDG_SUCURSAL AS CODIGO_SUCURSAL,
            (
                SELECT
                    NOMBRE
                FROM
                    CO
                WHERE
                    CODIGO = SEA.CDG_SUCURSAL
            ) AS NOMBRE_SUCURSAL,
            SCA.CDG_USUARIO AS CODIGO_CAJERA,
            (
                SELECT
                    CONCATENA_NOMBRE(NOMBRE1, NOMBRE2, PRIMAPE, SEGAPE)
                FROM
                    PE
                WHERE
                    CODIGO = SCA.CDG_USUARIO
            ) AS NOMBRE_CAJERA,
            NULL AS FECHA_CIERRE,
            SEA.SALDO_MINIMO AS MONTO_MIN,
            SEA.SALDO_MAXIMO AS MONTO_MAX,
            NVL(SEA.SALDO, 0) AS SALDO
        FROM
            SUC_ESTADO_AHORRO SEA
        RIGHT JOIN
            SUC_CAJERA_AHORRO SCA ON SCA.CDG_ESTADO_AHORRO = SEA.CODIGO
        WHERE
            SEA.CDG_SUCURSAL = '{$datos["sucursal"]}'
            AND SEA.ESTATUS = 'A'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if ($res) return self::Responde(true, "Datos encontrados.", $res);
            else return self::Responde(false, "No se encontraron datos para la sucursal " . $datos["sucursal"] . ".");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar información de la sucursal.", null, $e->getMessage());
        }
    }

    public static function AplicarFondeo($datos)
    {
        $qry = <<<sql
        INSERT INTO SUC_MOVIMIENTOS_AHORRO
            (CODIGO, CDG_ESTADO_AHORRO, FECHA, MONTO, MOVIMIENTO, CDG_USUARIO)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) FROM SUC_MOVIMIENTOS_AHORRO) + 1,
                :codigo,
                SYSDATE,
                :monto,
                '1',
                :usuario
            )
        sql;

        $params = [
            "codigo" => $datos["codigoSEA"],
            "monto" => $datos["montoOperacion"],
            "usuario" => $datos["usuario"]
        ];

        try {
            $mysqli = Database::getInstance();
            $mysqli->insertar($qry, $params);
            return self::Responde(true, "Fondeo realizado correctamente.");
        } catch (Exception $e) {
            return self::Responde(false, "Error al realizar fondeo.", null, $e->getMessage());
        }
    }

    public static function AplicarRetiro($datos)
    {
        $qry = <<<sql
        INSERT INTO SUC_MOVIMIENTOS_AHORRO
            (CODIGO, CDG_ESTADO_AHORRO, FECHA, MONTO, MOVIMIENTO, CDG_USUARIO)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) FROM SUC_MOVIMIENTOS_AHORRO) + 1,
                :codigo,
                SYSDATE,
                :monto,
                '0',
                :usuario
            )
        sql;

        $params = [
            "codigo" => $datos["codigoSEA"],
            "monto" => $datos["montoOperacion"],
            "usuario" => $datos["usuario"]
        ];

        try {
            $mysqli = Database::getInstance();
            $mysqli->insertar($qry, $params);
            return self::Responde(true, "Retiro realizado correctamente.");
        } catch (Exception $e) {
            return self::Responde(false, "Error al realizar retiro.", null, $e->getMessage());
        }
    }

    public static function GetMovimientos($datos)
    {
        $qry = <<<sql
        SELECT
            TO_CHAR(FECHA, 'DD/MM/YYYY HH24:MI:SS') FECHA,
            MONTO,
            CASE
                WHEN MOVIMIENTO = '1' THEN 'FONDEO'
                WHEN MOVIMIENTO = '2' THEN 'RETIRO'
                ELSE 'DESCONOCIDO'
            END MOVIMIENTO,
            (
                SELECT
                    CONCATENA_NOMBRE(NOMBRE1, NOMBRE2, PRIMAPE, SEGAPE)
                FROM
                    PE
                WHERE
                    CODIGO = CDG_USUARIO
            ) USUARIO
        FROM
            SUC_MOVIMIENTOS_AHORRO
        WHERE
            CDG_ESTADO_AHORRO = '{$datos["codigo"]}'
        ORDER BY
            FECHA DESC
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryAll($qry);
            return self::Responde(true, "Movimientos encontrados.", $res);
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar movimientos.", null, $e->getMessage());
        }
    }

    public static function GetMontosApertura($sucursal)
    {
        $qry = <<<sql
        SELECT
            MONTO_MINIMO,
            MONTO_MAXIMO
        FROM
            PARAMETROS_AHORRO
        WHERE
            CDG_SUCURSAL = '$sucursal'
        sql;

        try {
            $mysqli = Database::getInstance();
            $res = $mysqli->queryOne($qry);
            if ($res) return self::Responde(true, "Montos de apertura encontrados.", $res);
            return self::Responde(false, "No se encontraron montos de apertura.");
        } catch (Exception $e) {
            return self::Responde(false, "Error al buscar montos de apertura.", null, $e->getMessage());
        }
    }

    public static function GuardarMontosApertura($datos)
    {
        $qry = <<<sql
        INSERT INTO PARAMETROS_AHORRO
            (CODIGO, MONTO_MINIMO, MONTO_MAXIMO, CDG_SUCURSAL, FECHA_ALTA, MODIFICACION)
        VALUES
            (
                (SELECT NVL(MAX(TO_NUMBER(CODIGO)), 0) + 1 FROM PARAMETROS_AHORRO),
                :minimo,
                :maximo,
                :sucursal,
                SYSDATE,
                SYSDATE
            )
        sql;

        $params = [
            "sucursal" => $datos["codSucMontos"],
            "minimo" => $datos["minimoApertura"],
            "maximo" => $datos["maximoApertura"]
        ];

        try {
            $mysqli = Database::getInstance();
            $mysqli->insertar($qry, $params);
            return self::Responde(true, "Montos de apertura guardados correctamente.");
        } catch (Exception $e) {
            return self::Responde(false, "Error al guardar montos de apertura.", null, $e->getMessage());
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

    public static function ResumenCuenta($datos)
    {
        return "Resumen de cuenta";

        $contrato = $datos['contrato'] || '003011';
        $a = self::rangoFechas($datos['mes'] || date('m'), $datos['anio'] || date('Y'));
        $fI = $a['primerDia'];
        $fF = $a['ultimoDia'];

        $qry = <<<sql
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
            $res = $mysqli->queryAll($qry);
            if (count($res) === 0) return array();
            return $res;
        } catch (Exception $e) {
            return array();
        }
    }

    public static function rangoFechas($mes, $anio = date("Y"))
    {
        $numeroDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

        $primerDia = date("Y-m-01", strtotime("$anio-$mes-01"));
        $ultimoDia = date("Y-m-$numeroDiasMes", strtotime("$anio-$mes-$numeroDiasMes"));

        return ["primerDia" => $primerDia, "ultimoDia" => $ultimoDia];
    }

    public static function tst()
    {
        return "Hola";
    }
}
