<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Creditos extends Model
{

    public static function ConsultaGarantias($noCredito)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT
    GARPREN.SECUENCIA,
    GARPREN.ARTICULO,
    GARPREN.MARCA,
    GARPREN.MODELO,
    GARPREN.SERIE NO_SERIE,
    GARPREN.MONTO,
    GARPREN.FACTURA,
     TO_CHAR(GARPREN.FECREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FECREGISTRO
FROM
    GARPREN
WHERE 
	GARPREN.CDGEM = 'EMPFIN'
	AND GARPREN.ESTATUS = 'A'
	AND GARPREN.CDGNS = '$noCredito'

sql;

        return $mysqli->queryAll($query);
    }

    public static function ActualizacionCredito($noCredito)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT
    GARPREN.SECUENCIA,
    GARPREN.ARTICULO,
    GARPREN.MARCA,
    GARPREN.MODELO,
    GARPREN.SERIE NO_SERIE,
    GARPREN.MONTO,
    GARPREN.FACTURA
FROM
    GARPREN
WHERE 
	GARPREN.CDGEM = 'EMPFIN'
	AND GARPREN.ESTATUS = 'A'
	AND GARPREN.CDGNS = '$noCredito'

sql;

        return $mysqli->queryAll($query);
    }

    public static function SelectSucursalAllCreditoCambioSuc($noCredito)
    {

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		SC.SITUACION,
		SN.PLAZOSOL PLAZO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
	FROM 
		SN, SC, SC Q2, PRN 
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	  	AND SC.CICLO !='R1' 
		AND SC.CICLO != 'R2'
		AND SC.CICLO != 'R3'
		AND SC.CICLO != 'R4'
		AND SC.CICLO != 'R5'
		AND SC.CICLO != 'R6'
		AND SC.CICLO != 'R7'
		AND PRN.CDGNS = SN.CDGNS
		AND PRN.CICLO = SN.CICLO
		AND PRN.SITUACION != 'T'
		AND SC.CANTSOLIC <> '9999' order by SN.INICIO DESC
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }

    public static function ListaSucursales()
    {
        //////cambiar el parametro CDGPE
        $query = <<<sql
        SELECT DISTINCT 
            RG.CODIGO ID_REGION,
            RG.NOMBRE REGION,
            CO.CODIGO ID_SUCURSAL,
            CO.NOMBRE SUCURSAL
        FROM
            PCO, CO, RG
        WHERE
            PCO.CDGCO = CO.CODIGO
            AND CO.CDGRG = RG.CODIGO 
            AND PCO.CDGEM = 'EMPFIN'
        ORDER BY
	            SUCURSAL ASC
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function UpdateSucursal($sucursal_c)
    {

        $credito = $sucursal_c->_credito;
        $ciclo = $sucursal_c->_ciclo;
        $nueva_sucursal = $sucursal_c->_nueva_sucursal;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaSucursal($credito, $ciclo, $nueva_sucursal);
    }

    public static function ProcedureGarantias($garantias_c)
    {
        $credito = $garantias_c->_credito;
        $articulo = $garantias_c->_articulo;
        $marca = $garantias_c->_marca;
        $modelo = $garantias_c->_modelo;
        $serie = $garantias_c->_serie;
        $factura = $garantias_c->_factura;
        $usuario = $garantias_c->_usuario;
        $valor = $garantias_c->_valor;

        $mysqli = new Database();
        return $mysqli->queryProcedureInsertGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, 1);
    }

    public static function ProcedureGarantiasDelete($id, $secu)
    {

        $credito = $id;
        $secuencia = $secu;

        $mysqli = new Database();
        return $mysqli->queryProcedureDeleteGarantias($credito, $secuencia, 3);
    }

    public static function ProcedureGarantiasUpdate($garantias_c)
    {
        $credito = $garantias_c->_credito;
        $articulo = $garantias_c->_articulo;
        $marca = $garantias_c->_marca;
        $modelo = $garantias_c->_modelo;
        $serie = $garantias_c->_serie;
        $factura = $garantias_c->_factura;
        $usuario = $garantias_c->_usuario;
        $valor = $garantias_c->_valor;
        $secuencia = $garantias_c->_secuencia;



        $mysqli = new Database();
        return $mysqli->queryProcedureUpdatesGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, $secuencia);
    }

    public static function ConsultarPagosAdministracionOne($noCredito)
    {

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
		SN.PLAZOSOL PLAZO,
		SN.PERIODICIDAD,
		SN.TASA,
		DIA_PAGO(SN.NOACUERDO) DIA_PAGO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO,
		SC.CDGPI ID_PROYECTO
	FROM 
		SN, SC, SC Q2, PRN 
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	    AND PRN.CICLO = SC.CICLO 
		AND PRN.CDGNS = SC.CDGNS 
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    public static function UpdateActulizaCredito($credito_c)
    {

        $credito = $credito_c->_credito;
        $credito_n = $credito_c->_credito_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCredito($credito, $credito_n);
    }
    public static function UpdateActulizaCiclo($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoCiclo($credito, $ciclo_nuevo);
    }
    public static function UpdateActulizaSituacion($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;
        $situacion = $credito_c->_situacion;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoSituacion($credito, $ciclo_nuevo, $situacion);
    }

    public static function GetCierreDiario($fecha)
    {
        $qry = <<<SQL
        SELECT
            CO.NOMBRE AS SUCURSAL,
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE_ASESOR,
            PRN.CDGNS AS CODIGO_GRUPO,
            CL.CODIGO AS CODIGO_CLIENTE,
            CL.CURP AS CURP_CLIENTE,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_COMPLETO_CLIENTE,
            CL_AVAL.CODIGO AS CODIGO_AVAL,
            CL_AVAL.CURP AS CURP_AVAL,
            CONCATENA_NOMBRE(CL_AVAL.NOMBRE1, CL_AVAL.NOMBRE2, CL_AVAL.PRIMAPE, CL_AVAL.SEGAPE) AS NOMBRE_COMPLETO_AVAL,
            PRN.CICLO AS CICLO,
            TO_CHAR(PRN.INICIO, 'DD/MM/YYYY') AS FECHA_INICIO,
            CD.SDO_TOTAL AS SALDO_TOTAL,
            CD.MORA_TOTAL AS MORA_TOTAL,
            CD.DIAS_MORA AS DIAS_MORA,
            CASE
                WHEN (SYSDATE > PRN.INICIO + (DURACINI * 7)) THEN 'VENCIDO'
                ELSE 'VIGENTE'
            END AS TIPO_CARTERA
        FROM
            PRN
            INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
            INNER JOIN PE ON PE.CODIGO = PRN.CDGOCPE
            INNER JOIN SC ON SC.CDGNS = PRN.CDGNS
            AND SC.CICLO = PRN.CICLO -- Join para el cliente
            INNER JOIN CL ON CL.CODIGO = SC.CDGCL
            AND SC.CANTSOLIC <> 9999 -- Subquery para el aval
            LEFT JOIN (
                SELECT
                    SC_AUX.CDGNS,
                    SC_AUX.CICLO,
                    CL_AUX.CODIGO,
                    CL_AUX.NOMBRE1,
                    CL_AUX.NOMBRE2,
                    CL_AUX.PRIMAPE,
                    CL_AUX.SEGAPE,
                    CL_AUX.CURP -- Agregado CL_AUX.CURP
                FROM
                    SC SC_AUX
                    INNER JOIN CL CL_AUX ON CL_AUX.CODIGO = SC_AUX.CDGCL
                WHERE
                    SC_AUX.CANTSOLIC = 9999
            ) CL_AVAL ON CL_AVAL.CDGNS = PRN.CDGNS
            AND CL_AVAL.CICLO = PRN.CICLO -- Join adicional para obtener información de TBL_CIERRE_DIA
            LEFT JOIN TBL_CIERRE_DIA CD ON CD.CDGEM = PRN.CDGEM
            AND CD.CDGCLNS = PRN.CDGNS
            AND (
                PRN.CICLO = CD.CICLO
                OR PRN.CICLOD = CD.CICLO
            )
            AND PRN.INICIO = CD.INICIO
        WHERE
            PRN.SITUACION = 'E'
            AND TO_CHAR(CD.FECHA_CALC, 'YYYY-MM-DD') = '$fecha'
            AND CD.CLNS = 'G'
        SQL;

        try {
            $mysqli = new Database();
            $res = $mysqli->queryAll($qry);
            return self::Responde(true, 'Cierre diario generado con éxito.', $res, $qry);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al generar el cierre diario.', null, $e->getMessage());
        }
    }

    public static function GetParametrosCorreos()
    {
        $qry = <<<SQL
            SELECT * FROM (
                SELECT UNIQUE
                    'AREA' AS TIPO,
                    AREA AS VALOR,
                    AREA AS MOSTRAR
                FROM
                    CORREO_DIRECTORIO
                ORDER BY AREA
            )
            UNION
            SELECT * FROM (
                SELECT UNIQUE
                    'SUCURSAL' AS TIPO,
                    CD.SUCURSAL AS VALOR,
                    CD.SUCURSAL || ' - ' || CO.NOMBRE AS MOSTRAR
                FROM
                    CORREO_DIRECTORIO CD
                    JOIN CO ON CO.CODIGO = CD.SUCURSAL
                ORDER BY TO_NUMBER(CD.SUCURSAL)
            )
            UNION
            SELECT * FROM (
                SELECT UNIQUE
                    'GRUPO' AS TIPO,
                    GRUPO AS VALOR,
                    REPLACE(GRUPO, '_', ' ') AS MOSTRAR
                FROM
                    CORREO_DESTINATARIOS
                ORDER BY GRUPO
            )
            UNION
            SELECT * FROM (
                SELECT UNIQUE
                    'SUCURSALES' AS TIPO,
                    CODIGO AS VALOR,
                    CODIGO || ' - ' || NOMBRE AS MOSTRAR
                FROM
                    CO
                ORDER BY TO_NUMBER(CODIGO)
            )
        SQL;

        try {
            $mcm = new Database();
            $res = $mcm->queryAll($qry);
            return self::Responde(true, 'Parámetros de correos encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar parámetros de correos', null, $e->getMessage());
        }
    }

    public static function GetCorreos($datos)
    {
        $qry = <<<SQL
            SELECT
                NOMBRE,
                CORREO,
                AREA,
                SUCURSAL
            FROM
                CORREO_DIRECTORIO
        SQL;

        if (count($datos) > 0) {
            $qry .= ' WHERE ';
            $pTmp = [];
            foreach ($datos as $key => $value) {
                $pTmp[] = "$key = :$key";
            }
            $qry .= implode(' AND ', $pTmp);
        }

        try {
            $mcm = new Database();
            $res = $mcm->queryAll($qry, $datos);
            return self::Responde(true, 'Correos encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar correos', null, $e->getMessage());
        }
    }

    public static function GetCorreosGrupo($datos)
    {
        $qry = <<<SQL
            SELECT
                CORREO,
                EDITABLE
            FROM
                CORREO_DESTINATARIOS
            WHERE
                GRUPO = :grupo 
        SQL;

        try {
            $mcm = new Database();
            $res = $mcm->queryAll($qry, $datos);
            return self::Responde(true, 'Correos de grupo encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar correos de grupo', null, $e->getMessage());
        }
    }

    public static function AgregaCorreoGrupo($datos)
    {
        $qry = <<<SQL
            INSERT INTO CORREO_DESTINATARIOS (GRUPO, CORREO, EDITABLE, REGISTRO)
            VALUES (:grupo,:correo,1,:usuario)
        SQL;

        $qrys = [];
        $parametros = [];
        foreach ($datos['correos'] as $key => $value) {
            $qrys[] = $qry;
            $parametros[] = [
                'grupo' => $datos['grupo'],
                'correo' => $value,
                'usuario' => $datos['usuario']
            ];
        }

        try {
            $mcm = new Database();
            $mcm->insertaMultiple($qrys, $parametros);
            return self::Responde(true, 'Correo agregado a grupo correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al agregar correo a grupo', null, $e->getMessage());
        }
    }

    public static function EliminaCorreoGrupo($datos)
    {
        $qry = <<<SQL
            DELETE FROM CORREO_DESTINATARIOS
            WHERE GRUPO = :grupo
            AND CORREO = :correo
        SQL;

        $qrys = [];
        $parametros = [];

        foreach ($datos['correos'] as $key => $value) {
            $qrys[] = $qry;
            $parametros[] = [
                'grupo' => $datos['grupo'],
                'correo' => $value
            ];
        }

        try {
            $mcm = new Database();
            $mcm->insertaMultiple($qrys, $parametros);
            return self::Responde(true, 'Correo eliminado de grupo correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al eliminar correo de grupo', null, $e->getMessage());
        }
    }

    public static function RegistraCorreo($datos)
    {
        $qry = <<<SQL
            INSERT INTO CORREO_DIRECTORIO (NOMBRE,CORREO,ESTADO,AREA,SUCURSAL,REGISTRO)
            VALUES (:nombre,:correo,'A',:area,:sucursal,:registro)
        SQL;

        $parametros = [
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'area' => $datos['area'],
            'sucursal' => $datos['sucursal'],
            'registro' => $datos['usuario']
        ];

        try {
            $mcm = new Database();
            $mcm->insertar($qry, $parametros);
            return self::Responde(true, 'Correo registrado correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al registrar correo', null, $e->getMessage());
        }
    }
}
