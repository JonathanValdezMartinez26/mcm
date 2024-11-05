<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Operaciones
{

    public static function ConsultarDesembolsos($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                DESEMBOLSOS_VIEW  
            WHERE
                FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                FDEPOSITO ASC
        SQL;

        try {
            $db = new Database();
            return $db->queryAll($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function ConsultarClientes($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                DISTINCT TO_CHAR(' '||CDGCL) AS CDGCL, TO_CHAR(GRUPO) AS GRUPO, ORIGEN, CLIENTES AS NOMBRE, ADICIONAL,
                A_PATERNO, A_MATERNO, TIPO_PERSONA, RFC, CURP, RAZON_SOCIAL, FECHA_NAC, NACIONALIDAD, DOMICILIO,
                COLONIA, CIUDAD, PAIS, SUC_ID_ESTADO, TELEFONO, ID_ACTIVIDAD_ECONO, CALIFICACION, ALTA,
                TO_CHAR(ID_SUCL_SISTEMA) AS ID_SUCURSAL_SISTEMA, GENERO, CORREO_ELECTRONICO, FIRMA_ELECT, PROFESION,
                OCUPACION, PAIS_NAC, EDO_NAC, LUGAR_NAC, NUMERO_DOCUMENTO, CONOCIMIENTO, INMIGRACION,CUENTA_ORIGINAL,
                SITUACION_CREDITO, TIPO_DOCUMENTO, INDICADOR_EMPLEO, EMPRESAS, INDICADOR_GOBIERNO, PUESTO, FECHA_INICIO,
                FEH_FIN, CP, FECHA_ALTA
            FROM
                SUB_CLIENTES_PERFIL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') 
            GROUP BY 
                CDGCL, GRUPO, ORIGEN, CLIENTES, ADICIONAL, A_PATERNO, A_MATERNO, TIPO_PERSONA, RFC, CURP, RAZON_SOCIAL, 
                FECHA_NAC, NACIONALIDAD, DOMICILIO, COLONIA, CIUDAD, PAIS, SUC_ID_ESTADO, TELEFONO, ID_ACTIVIDAD_ECONO, 
                CALIFICACION, ALTA, ID_SUCL_SISTEMA, GENERO, CORREO_ELECTRONICO, FIRMA_ELECT, PROFESION, OCUPACION, 
                PAIS_NAC, EDO_NAC, LUGAR_NAC, NUMERO_DOCUMENTO, CONOCIMIENTO, INMIGRACION,CUENTA_ORIGINAL, SITUACION_CREDITO,
                TIPO_DOCUMENTO, INDICADOR_EMPLEO, EMPRESAS, INDICADOR_GOBIERNO, PUESTO, FECHA_INICIO, FEH_FIN, CP, FECHA_ALTA
            ORDER BY
                FECHA_ALTA DESC
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function CuentasRelacionadas($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                DISTINCT TO_CHAR(' ' || CDGCL) AS CLIENTE,
                TO_CHAR(GRUPO) AS GRUPO,
                ULTIMO_CICLO AS CUENTA_RELACION,
                CLIENTES AS NOMBRE,
                TO_CHAR(ADICIONAL) AS ADICIONAL,
                TO_CHAR(A_PATERNO) AS A_PATERNO,
                TO_CHAR(A_MATERNO) AS A_MATERNO,
                'PRESTAMO ' || ULTIMO_CICLO AS DESCRIPCION_OPERACION,
                CASE
                    WHEN ULTIMO_CICLO = '01' THEN '0'
                    ELSE '1'
                END AS IDENTIFICA_CUENTA,
                '' AS CONSERVA,
                ID_SUCL_SISTEMA AS OFICINA_CLIENTE,
                ALTA AS FECHA_INICIO_OPERACION,
                FECHA_ALTA
            FROM
                SUB_CLIENTES_PERFIL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            GROUP BY
                CDGCL,
                GRUPO,
                ULTIMO_CICLO,
                CLIENTES,
                ADICIONAL,
                A_PATERNO,
                A_MATERNO,
                ID_SUCL_SISTEMA,
                ALTA,
                FECHA_ALTA
            ORDER BY
                FECHA_ALTA DESC
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPagos($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                PRN.CANTENTRE,
                PRC.CDGEM,
                PRN.CICLO,
                EF.NOMBRE AS LOCALIDAD,
                CASE
                    WHEN IB.CODIGO = 13 THEN '001' ------------------------ IMBURSA
                    WHEN IB.CODIGO = 11 THEN '002' ---------------------- PAYCASH
                    WHEN IB.CODIGO = 05 THEN '003' ----------------------- OXXO
                    WHEN IB.CODIGO = 00 THEN '001' ----------------------- ES BANORTE PERO PASA A IMBURSA
                    WHEN IB.CODIGO = 04 THEN '004' ----------------------- SON GARANTIAS
                    ELSE '000'
                END AS SUCURSAL,
                '09' AS TIPO_OPERACION,
                CL.CODIGO AS ID_CLIENTE,
                PRC.CDGNS AS NUM_CUENTA,
                '01' AS INSTRUMENTO_MONETARIO,
                'MXN' AS MONEDA,
                ROUND((MP.CANTIDAD * PRC.CANTENTRE) / PRN.CANTENTRE, 3) AS MONTO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_OPERACION,
                (
                    CASE
                        WHEN (CB.NOMBRE = 'OXXO' || 'PAYCASH') THEN 1
                        ELSE 4
                    END
                ) AS TIPO_RECEPTOR,
                (
                    CASE
                        WHEN (IB.NOMBRE = 'BANORTE') THEN 'INBURSA'
                        ELSE IB.NOMBRE
                    END
                ) AS CLAVE_RECEPTOR,
                '0' AS NUM_CAJA,
                '0' AS ID_CAJERO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_HORA,
                '036180500609569035' AS NOTARJETA_CTA,
                '4' AS TIPOTARJETA,
                '0' AS COD_AUTORIZACION,
                'NO' AS ATRASO,
                PRN.CDGCO AS OFICINA_CLIENTE,
                PRN.SITUACION,
                MP.FDEPOSITO
            FROM
                MP
                INNER JOIN PRN ON PRN.CDGNS = MP.CDGNS
                INNER JOIN PRC ON PRC.CDGNS = PRN.CDGNS
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
                INNER JOIN EF ON CL.CDGEF = EF.CODIGO
                INNER JOIN CB ON CB.CODIGO = MP.CDGCB
                INNER JOIN IB ON IB.CODIGO = CB.CDGIB
            WHERE
                MP.CDGEM = 'EMPFIN'
                AND MP.TIPO = 'PD'
                AND MP.ESTATUS = 'B'
                AND MP.CICLO = PRC.CICLO
                AND MP.CICLO = PRN.CICLO
                AND MP.CDGNS = PRC.CDGNS
                AND MP.CDGNS = PRN.CDGNS
                AND MP.FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                PRN.CICLO DESC  
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPagosNacimiento($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                PRN.CANTENTRE,
                PRC.CDGEM,
                PRN.CICLO,
                EF.NOMBRE AS LOCALIDAD,
                CASE
                    WHEN IB.CODIGO = 13 THEN '001' ------------------------ IMBURSA
                    WHEN IB.CODIGO = 11 THEN '002' ---------------------- PAYCASH
                    WHEN IB.CODIGO = 05 THEN '003' ----------------------- OXXO
                    WHEN IB.CODIGO = 00 THEN '001' ----------------------- ES BANORTE PERO PASA A IMBURSA
                    WHEN IB.CODIGO = 04 THEN '004' ----------------------- SON GARANTIAS
                    ELSE '000'
                END AS SUCURSAL,
                '09' AS TIPO_OPERACION,
                CL.CODIGO AS ID_CLIENTE,
                PRC.CDGNS AS NUM_CUENTA,
                '01' AS INSTRUMENTO_MONETARIO,
                'MXN' AS MONEDA,
                ROUND((MP.CANTIDAD * PRC.CANTENTRE) / PRN.CANTENTRE, 3) AS MONTO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_OPERACION,
                (
                    CASE
                        WHEN (CB.NOMBRE = 'OXXO' || 'PAYCASH') THEN 1
                        ELSE 4
                    END
                ) AS TIPO_RECEPTOR,
                (
                    CASE
                        WHEN (IB.NOMBRE = 'BANORTE') THEN 'INBURSA'
                        ELSE IB.NOMBRE
                    END
                ) AS CLAVE_RECEPTOR,
                '0' AS NUM_CAJA,
                '0' AS ID_CAJERO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_HORA,
                '036180500609569035' AS NOTARJETA_CTA,
                '4' AS TIPOTARJETA,
                '0' AS COD_AUTORIZACION,
                'NO' AS ATRASO,
                PRN.CDGCO AS OFICINA_CLIENTE,
                PRN.SITUACION,
                MP.FDEPOSITO,
                TO_CHAR(CL.NACIMIENTO) AS FEC_NAC,
                TRUNC(
                    MONTHS_BETWEEN(
                        TO_DATE(SYSDATE, 'dd-mm-yy'),
                        CL.NACIMIENTO
                    ) / 12
                ) AS EDAD
            FROM
                MP
                INNER JOIN PRN ON PRN.CDGNS = MP.CDGNS
                INNER JOIN PRC ON PRC.CDGNS = PRN.CDGNS
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
                INNER JOIN EF ON CL.CDGEF = EF.CODIGO
                INNER JOIN CB ON CB.CODIGO = MP.CDGCB
                INNER JOIN IB ON IB.CODIGO = CB.CDGIB
            WHERE
                MP.CDGEM = 'EMPFIN'
                AND MP.TIPO = 'PD'
                AND MP.ESTATUS = 'B'
                AND MP.CICLO = PRC.CICLO
                AND MP.CICLO = PRN.CICLO
                AND MP.CDGNS = PRC.CDGNS
                AND MP.CDGNS = PRN.CDGNS
                AND MP.FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                PRN.CICLO DESC
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPerfilTransaccional($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                CDGCL, GRUPO, NOMBRE, INSTRUMENTO, 'MXN' AS TIPO_MONEDA, T_CAMBIO, MONT_PRESTAMO, PLAZO, 
                FRECUENCIA, TOTAL_PAGOS, MONTO_FIN_PAGO, ADELANTAR_PAGO, NUMERO_APORTACIONES,
                MONTO_APORTACIONES, CUOTA_PAGO, SALDO, ID_SUCURSAL_SISTEMA, ORIGEN_RECURSO,
                DESTINO_RECURSOS, FECHA_INICIO_CREDITO, FECHA_FIN, DESTINO, ORIGEN, TIPO_OPERACION,
                INST_MONETARIO, TIPO_CREDITO, PRODUCTO, PAIS_ORIGEN, PAIS_DESTINO, ALTA_CONTRATO,
                'PREC' AS TIPO_CONTRATO, '' AS TIP_DOC, '' AS LATLON, '' AS LOCALIZACION, CP
            FROM
                PERFIL_TRANSACCIONAL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
                AND ULTIMO_CICLO != 'D1'
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultaGruposCultiva($fecha_inicial, $fecha_final)
    {
        $query = <<<SQL
            SELECT
                CO.NOMBRE AS SUCURSAL,
                SC.CDGNS,
                NS.NOMBRE as NOMBRE_GRUPO,
                TO_CHAR(
                        (
                                CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE
                        )
                ) AS CLIENTE,
                (CL.CALLE) AS DOMICILIO,
                TO_CHAR(SC.SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS SOLICITUD,
                SC.CICLO,
                NS.CODIGO AS CDGNS
            FROM
                SC
                INNER JOIN NS ON NS.CODIGO = SC.CDGNS
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL
                INNER JOIN CO ON CO.CODIGO = NS.CDGCO
            WHERE
                SOLICITUD BETWEEN TIMESTAMP '$fecha_inicial 00:00:00.000000'
                AND TIMESTAMP '$fecha_final 11:59:00.000000'
            ORDER BY
                SC.SOLICITUD ASC
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ReingresarClientesCredito($credito)
    {
        $query = <<<SQL
            SELECT
                CDGNS,
                CDGCL,
                NOMBRE_CLIENTE,
                INICIO,
                FECHA_BAJA,
                FECHA_BAJA_REAL,
                CODIGO_MOTIVO,
                MOTIVO_BAJA
            FROM
                (
                    SELECT
                        CDGNS,
                        CDGCL,
                        (
                            NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE
                        ) AS NOMBRE_CLIENTE,
                        INICIO,
                        TO_CHAR(FIN, 'DD-MM-YYYY') AS FECHA_BAJA,
                        FIN AS FECHA_BAJA_REAL,
                        m.CODIGO AS CODIGO_MOTIVO,
                        UPPER(m.DESCRIPCION) AS MOTIVO_BAJA,
                        ROW_NUMBER() OVER (
                            PARTITION BY CDGCL
                            ORDER BY
                                FIN DESC
                        ) AS RN
                    FROM
                        CN c
                        INNER JOIN MS m ON m.CODIGO = c.CDGMS
                        INNER JOIN CL c2 ON c2.CODIGO = c.CDGCL
                    WHERE
                        CDGNS = '$credito'
                ) sub
            WHERE
                RN = 1
        SQL;

        $query2 = <<<SQL
            SELECT 
                NOMBRE
            FROM
                NS
            WHERE
                CODIGO = '$credito'
        SQL;

        $db = new Database();
        if ($db->db_activa == null) return [];
        return [$db->queryAll($query), $db->queryOne($query2)];
    }
}
