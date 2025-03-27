<?php

namespace Jobs\models;

include_once dirname(__DIR__) . "/../Core/Model.php";
include_once dirname(__DIR__) . "/../Core/Database.php";

use Core\Model;
use Core\Database;

class JobsCredito extends Model
{
    public static function GetSolicitudes()
    {
        $qry = <<<SQL
            SELECT
                SN.CDGNS,
                SN.CICLO,
                TO_CHAR(SN.SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS SOLICITUD,
                SCC.CDGPE,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE_PE,
                SCC.ESTATUS AS ESTATUS,
                CASE SCC.DIA_LLAMADA_2_CL
                    WHEN NULL THEN 2
                    ELSE 1
                END AS NO_LLAMADAS,
                TO_CHAR(SCC.DIA_LLAMADA_1_CL, 'DD/MM/YYYY HH24:MI:SS') AS PRIMERA_LLAMADA,
                TO_CHAR(
                    CASE
                        WHEN SCC.DIA_LLAMADA_2_CL IS NULL THEN SCC.DIA_LLAMADA_1_CL
                        ELSE SCC.DIA_LLAMADA_2_CL
                    END
                    , 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA,
                SCC.NUMERO_INTENTOS_CL AS INTENTOS,
                SCC.COMENTARIO_INICIAL,
                SCC.COMENTARIO_FINAL,
                CL.CODIGO AS CL,
                CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_CL,
                CO.CODIGO AS CO,
                CO.NOMBRE AS NOMBRE_CO,
                RG.CODIGO AS RG,
                RG.NOMBRE AS NOMBRE_RG
            FROM
                SN
                RIGHT JOIN SOL_CALL_CENTER SCC ON SN.CDGNS = SCC.CDGNS AND SN.SOLICITUD = SCC.FECHA_SOL
                RIGHT JOIN CO ON SN.CDGCO = CO.CODIGO
                RIGHT JOIN RG ON CO.CDGRG = RG.CODIGO
                RIGHT JOIN SC ON SN.CDGNS = SC.CDGNS AND SN.CICLO = SC.CICLO AND SN.SOLICITUD = SC.SOLICITUD AND SC.CANTSOLIC <> 9999
                RIGHT JOIN CL ON SC.CDGCL = CL.CODIGO
                RIGHT JOIN PE ON SCC.CDGPE = PE.CODIGO
            WHERE
                SN.SITUACION = 'S'
                AND SCC.ESTATUS <> 'PENDIENTE'
                AND SCC.FECHA_TRA_CL > TO_DATE('24/03/2025 00:00:00', 'DD/MM/YYYY HH24:MI:SS')
                AND (SELECT
                    COUNT(*) + CASE WHEN SN.CICLO = '01' THEN 1 ELSE 0 END	
                FROM
                    PRN
                WHERE
                    PRN.CDGNS = SN.CDGNS
                    AND PRN.SITUACION = 'L'
                    AND TO_NUMBER(PRN.CICLO) = TO_NUMBER(SN.CICLO) - 1) = 1
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Se obtuvieron las solicitudes de crédito",  $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las solicitudes de crédito", null, $e->getMessage());
        }
    }

    // Metodos para las solicitudes de crédito aprobadas
    public static function ProcesaSolicitudAprobada($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Inserta_PRN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Inserta_PRC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Limpia_MPC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Limpia_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Limpia_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Inserta_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Inserta_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Inserta_MPC($credito);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Solicitud aprobada procesada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud aprobada", null, $e->getMessage());
        }
    }

    public static function Solicitud_A_Actualiza_SN($datos)
    {
        $qry = <<<SQL
            UPDATE
                SN
            SET
                CANTAUTOR = CANTSOLIC - 9999
                , SITUACION = 'A'
            WHERE
                SITUACION = 'S'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Actualiza_SC($datos)
    {
        $qry = <<<SQL
            UPDATE
                SC
            SET
                CANTAUTOR = CASE
                                WHEN CANTSOLIC = 9999 THEN 0
                                ELSE CANTSOLIC
                            END,
                SITUACION = CASE
                                WHEN CANTSOLIC = 9999 THEN 'R'
                                ELSE 'A'
                            END
            WHERE
                SITUACION = 'S'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Inserta_PRN($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRN (
                    CDGEM,
                    CDGNS,
                    CICLO,
                    CDGCO,
                    CDGOCPE,
                    SOLICITUD,
                    INICIO,
                    PERIODICIDAD,
                    CANTAUTOR,
                    CANTENTRE,
                    DIAJUNTA,
                    HORARIO,
                    DESFASEPAGO,
                    TASAINI,
                    DURACINI,
                    TASAFIN,
                    DURACFIN,
                    PERIGRCAP,
                    PERIGRINT,
                    CDGMCI,
                    CDGFDI,
                    DEPOSITA,
                    SITUACION,
                    REPORTE,
                    CONCILIADO,
                    AUTCARPE,
                    FAUTCAR,
                    AUTTESPE,
                    FAUTTES,
                    PRESIDENTE,
                    TESORERO,
                    SECRETARIO,
                    ACTUALIZAENPE,
                    MODOAPLIRECA,
                    FCOMITE,
                    NOCHEQUE,
                    ACTUALIZACHPE,
                    FEXP,
                    CDGCB,
                    FORMAENTREGA,
                    CDGTPC,
                    CDGPCR,
                    TASA,
                    PLAZO,
                    CDGMO,
                    CDGPRPE,
                    ACTUALIZACPE,
                    NOACUERDO,
                    MULTPER
                )
            SELECT
                SN.CDGEM,
                SN.CDGNS,
                SN.CICLO,
                SN.CDGCO,
                SN.CDGOCPE,
                SN.SOLICITUD,
                SN.INICIO,
                SN.PERIODICIDAD,
                SN.CANTAUTOR,
                SN.CANTAUTOR,
                SN.DIAJUNTA,
                SN.HORARIO,
                SN.DESFASEPAGO,
                SN.TASA,
                SN.DURACION,
                SN.TASA,
                SN.DURACION,
                SN.PERIGRCAP,
                SN.PERIGRINT,
                SN.CDGMCI,
                SN.CDGFDI,
                SN.DEPOSITA,
                'E',
                '   C',
                'C',
                :USUARIO,
                SYSDATE,
                :USUARIO,
                SYSDATE,
                SN.PRESIDENTE,
                SN.TESORERO,
                SN.SECRETARIO,
                :USUARIO,
                SN.MODOAPLIRECA,
                SYSDATE,
                GET_CHQ(SN.CDGCO),
                :USUARIO,
                SYSDATE,
                GET_CDGCB(SN.CDGCO),
                'I',
                SN.CDGTPC,
                SN.CDGPCR,
                SN.TASA,
                SN.DURACION,
                SN.CDGMO,
                SN.CDGPRPE,
                :USUARIO,
                SN.NOACUERDO,
                SN.MULTPER
            FROM
                SN
            WHERE
                SN.SITUACION = 'A'
                AND SN.CDGNS = :CDGNS
                AND SN.CICLO = :CICLO
                AND SN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CDGNS'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD'],
            'USUARIO' => 'AMGM'
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Inserta_PRC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    CDGNS,
                    SOLICITUD,
                    CANTAUTOR,
                    CANTENTRE,
                    CDGORF,
                    CDGLC,
                    REPORTE,
                    NOCHEQUE,
                    FEXPCHEQUE,
                    SITUACION,
                    CONCILIADO,
                    ACTUALIZACHPE,
                    CLNS,
                    FEXP,
                    CDGCB,
                    FORMAENTREGA,
                    CDGCLNS,
                    ENTRREAL,
                    DOMICILIA
                )
            SELECT
                PRN.CDGEM,
                SC.CDGCL,
                PRN.CICLO,
                PRN.CDGNS,
                PRN.SOLICITUD,
                PRN.CANTAUTOR,
                PRN.CANTENTRE,
                '0001',
                '001',
                PRN.REPORTE,
                PRN.NOCHEQUE,
                SYSDATE,
                PRN.SITUACION,
                PRN.CONCILIADO,
                PRN.ACTUALIZACHPE,
                SC.CLNS,
                PRN.FEXP,
                PRN.CDGCB,
                PRN.FORMAENTREGA,
                SC.CDGNS,
                PRN.CANTENTRE,
                SC.DOMICILIA
            FROM
                PRN
                JOIN SC ON PRN.CDGNS = SC.CDGNS AND PRN.CICLO = SC.CICLO AND PRN.SOLICITUD = SC.SOLICITUD AND SC.SITUACION = 'A' AND SC.CANTSOLIC <> 9999
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CDGNS'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD']
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Limpia_MPC($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MPC
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Limpia_JP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                JP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Limpia_MP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Inserta_MP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    SECUENCIA,
                    REFERENCIA,
                    REFCIE,
                    TIPO,
                    FDEPOSITO,
                    FREALDEP,
                    CANTIDAD,
                    MODO,
                    CONCILIADO,
                    ESTATUS,
                    ACTUALIZARPE,
                    PAGADOCAP,
                    PAGADOINT,
                    PAGADOREC
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCLNS,
                PRC.CLNS,
                PRN.CDGNS,
                PRN.CICLO,
                0,
                '01',
                :ETIQUETA,
                :ETIQUETA,
                'IN',
                PRN.INICIO,
                PRN.INICIO,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.TASA,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                'G',
                'D',
                'B',
                'AMGM',
                0,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.TASA,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                0
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"],
            "ETIQUETA" => "Interés total del préstamo",
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Inserta_JP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                JP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    TEXTO,
                    FECHA,
                    PAGOINFORME,
                    PAGOFICHA,
                    AHORRO,
                    RETIRO,
                    CONCBANINF,
                    CONCBANFI,
                    COINCIDEPAG,
                    TIPO,
                    ACTUALIZARPE,
                    CONCILIADO
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCLNS,
                PRC.CLNS,
                PRN.CDGNS,
                PRN.CICLO,
                0,
                'Interés total del préstamo',
                PRN.INICIO,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                0,
                0,
                'S',
                'S',
                'S',
                'IN',
                'AMGM',
                'C'
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_A_Inserta_MPC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MPC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    PERIODO,
                    TIPO,
                    CDGNS,
                    CANTIDAD,
                    CLNS,
                    FECHA,
                    CDGCLNS
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCL,
                PRN.CICLO,
                0,
                'IN',
                PRN.CDGNS,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                PRC.CLNS,
                PRN.INICIO,
                PRC.CDGCLNS
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    // Metodos para las solicitudes de crédito rechazadas
    public static function ProcesaSolicitudRechazada($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_R_Actualiza_SN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_R_Actualiza_SC($credito);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Solicitud rechazada procesada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud rechazada", null, $e->getMessage());
        }
    }

    public static function Solicitud_R_Actualiza_SN($datos)
    {
        $qry = <<<SQL
            UPDATE
                SN
            SET
                CICLO = 'R' || (
                    SELECT
                        COUNT(*) + 1
                    FROM
                        SN SN2
                    WHERE
                        SN2.CICLOR = SN.CICLO
                        AND SN2.CDGNS = SN.CDGNS
                ),
                SITUACION = 'R',
                CANTAUTOR = 0,
                CICLOR = :CICLO,
                RECCARPE = :USUARIOR
            WHERE
                SITUACION = 'S'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"],
            "USUARIOR" => $datos["CDGPE"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function Solicitud_R_Actualiza_SC($datos)
    {
        $qry = <<<SQL
            UPDATE
                SC
            SET
                CICLO = 'R' || (
                    SELECT
                        COUNT(*) + 1
                    FROM
                        SC SC2
                    WHERE
                        SC2.CICLOR = SC.CICLO
                        AND SC2.CDGNS = SC.CDGNS
                ),
                SITUACION = 'R',
                CICLOR = :CICLO
            WHERE
                SITUACION = 'S'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CDGNS"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    // Metodos para los cheques
    public static function GetCreditosAutorizados()
    {
        $qry = <<<SQL
            SELECT
                PRC.CDGCL,
                PRN.CDGNS,
                PRN.CICLO,
                TO_CHAR(PRN.INICIO, 'YYYY-MM-DD') AS INICIO,
                PRN.CDGCO,
                PRN.CANTAUTOR,
                TRUNC(SYSDATE) AS FEXP,
                (
                    APagarInteresPrN(
                        'EMPFIN',
                        PRN.CDGNS,
                        PRN.CICLO,
                        NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                        PRN.Tasa,
                        PRN.PLAZO,
                        PRN.PERIODICIDAD,
                        PRN.CDGMCI,
                        PRN.INICIO,
                        PRN.DIAJUNTA,
                        PRN.MULTPER,
                        PRN.PERIGRCAP,
                        PRN.PERIGRINT,
                        PRN.DESFASEPAGO,
                        PRN.CDGTI
                    ) * -1
                ) AS INTERES,
                (
                    APagarInteresPrN(
                        'EMPFIN',
                        PRN.CDGNS,
                        PRN.CICLO,
                        NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                        PRN.Tasa,
                        PRN.PLAZO,
                        PRN.PERIODICIDAD,
                        PRN.CDGMCI,
                        PRN.INICIO,
                        PRN.DIAJUNTA,
                        PRN.MULTPER,
                        PRN.PERIGRCAP,
                        PRN.PERIGRINT,
                        PRN.DESFASEPAGO,
                        PRN.CDGTI
                    ) * -1
                ) AS PAGADOINT
            FROM
                PRN,
                PRC
            WHERE
                PRN.INICIO > TIMESTAMP '2024-04-11 00:00:00.000000'
                AND PRN.SITUACION = 'T'
                AND (
                    SELECT
                        COUNT(*)
                    FROM
                        PRN PRN2
                    WHERE
                        PRN2.SITUACION = 'E'
                        AND PRN2.CDGNS = PRN.CDGNS
                ) = 0
                AND PRC.CDGNS = PRN.CDGNS
                AND PRC.NOCHEQUE IS NULL
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Se obtuvieron los créditos autorizados",  $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los créditos autorizados", null, $e->getMessage());
        }
    }

    public static function GetNoChequera($cdgco)
    {
        $qry = <<<SQL
            SELECT
                CDGCB
            FROM
                CHEQUERA
            WHERE
                TO_NUMBER(CODIGO) = (
                    SELECT
                        MAX(TO_NUMBER(CODIGO)) AS int_column
                    FROM
                        CHEQUERA
                    WHERE
                        CDGCO = :cdgco
                )
                AND CDGCO = :cdgco
        SQL;

        try {
            $db = new Database();
            $res = $db->queryOne($qry, ["cdgco" => $cdgco]);
            return self::Responde(true, "Se obtuvo el número de chequera", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el número de chequera", null, $e->getMessage());
        }
    }

    public static function GetNoCheque($chequera)
    {
        $qry = <<<SQL
            SELECT
                FNSIGCHEQUE('EMPFIN', :chequera) CHQSIG
            FROM
                DUAL
        SQL;

        try {
            $db = new Database();
            $res = $db->queryOne($qry, ["chequera" => $chequera]);
            return self::Responde(true, "Se obtuvo el número de cheque", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el número de cheque", $e->getMessage());
        }
    }

    public static function GeneraCheques($datos)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::GenCheques_Actualiza_PRC($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Actualiza_PRN($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_MPC($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_JP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_MP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_MP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_JP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_MPC($datos);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Cheque generado correctamente", $datos);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al generar el cheque", null, $e->getMessage());
        }
    }

    public static function GenCheques_Actualiza_PRC($datos)
    {
        $qry = <<<SQL
            UPDATE PRC SET
                NOCHEQUE = LPAD(:cheque,7,'0'),
                FEXP = SYSDATE,
                ACTUALIZACHPE = 'AMGM',
                SITUACION = 'E',
                CDGCB = :cdgcb,
                REPORTE = '   C',
                FEXPCHEQUE = SYSDATE,
                CANTENTRE = :cantautor,
                ENTRREAL = :cantautor
            WHERE
                CDGCL = :cdgcl
                AND CDGCLNS = :cdgns
                AND CICLO = :ciclo
        SQL;

        $parametros = [
            "cheque" => $datos["cheque"],
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgcl" => $datos["cdgcl"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Actualiza_PRN($datos)
    {
        $qry = <<<SQL
            UPDATE PRN SET
                REPORTE = '   C',
                FEXP = SYSDATE,
                ACTUALIZACHPE= 'AMGM',
                SITUACION = 'E',
                CDGCB = :cdgcb,
                CANTENTRE = :cantautor,
                ACTUALIZAENPE = 'AMGM',
                ACTUALIZACPE = 'AMGM',
                FCOMITE = SYSDATE
            WHERE
                CDGNS = :cdgns
                AND CICLO = :ciclo
        SQL;

        $parametros = [
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_MPC($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MPC
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGCLNS = :prmCDGCLNS
                AND CLNS = 'G'
                AND CICLO = :prmCICLO
                AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = '00'
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_JP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                JP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGCLNS = :prmCDGCLNS
                AND CLNS = 'G'
                AND CICLO = :prmCICLO
                AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND PERIODO = '00'
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_MP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MP
            WHERE
                CDGEM = 'EMPFIN'
                AND cdgclns = :prmCDGCLNS
                AND CLNS = 'G'
                AND ciclo = :prmCICLO
                AND frealdep = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_MP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    SECUENCIA,
                    REFERENCIA,
                    REFCIE,
                    TIPO,
                    FREALDEP,
                    FDEPOSITO,
                    CANTIDAD,
                    MODO,
                    CONCILIADO,
                    ESTATUS,
                    ACTUALIZARPE,
                    PAGADOCAP,
                    PAGADOINT,
                    PAGADOREC
                )
            VALUES
                (
                    'EMPFIN',
                    :prmCDGCLNS,
                    'G',
                    :prmCDGNS,
                    :prmCICLO,
                    '0',
                    '01',
                    'Interés total del préstamo',
                    'Interés total del préstamo',
                    'IN',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    :vINTERES,
                    'G',
                    'D',
                    'B',
                    'AMGM',
                    0,
                    :vINTERES,
                    0
                )
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCDGNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_JP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                JP (
                    CDGEM,
                    CDGCLNS,
                    CICLO,
                    CLNS,
                    FECHA,
                    PERIODO,
                    PAGOINFORME,
                    PAGOFICHA,
                    AHORRO,
                    RETIRO,
                    TIPO,
                    CDGNS,
                    TEXTO,
                    CONCILIADO,
                    ACTUALIZARPE,
                    CONCBANINF,
                    CONCBANFI,
                    COINCIDEPAG
                )
            VALUES
                (
                    'EMPFIN',
                    :prmCDGCLNS,
                    :prmCICLO,
                    'G',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    '00',
                    :vINTERES,
                    :vINTERES,
                    0,
                    0,
                    'IN',
                    :prmCDGCLNS,
                    'Interés total del préstamo',
                    'C',
                    'AMGM',
                    'S',
                    'S',
                    'S'
                )
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"],
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_MPC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MPC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    CLNS,
                    FECHA,
                    TIPO,
                    PERIODO,
                    CDGCLNS,
                    CDGNS,
                    CANTIDAD
                )
            VALUES
                (
                    'EMPFIN',
                    :vCLIENTE,
                    :prmCICLO,
                    'G',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    'IN',
                    '00',
                    :prmCDGCLNS,
                    :prmCDGCLNS,
                    :vINTERES
                )
        SQL;

        $parametros = [
            "vCLIENTE" => $datos["vCLIENTE"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "vINTERES" => $datos["vINTERES"]
        ];

        return [
            $qry,
            $parametros
        ];
    }
}
