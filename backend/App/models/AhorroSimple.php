<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class AhorroSimple extends Model
{

	public static function ConsultarPagosFechaSucursal($cdgns)
	{
		$query_datos = <<<sql
		SELECT
			PD.PAGOSDIA,
			PD.RETIROS_AHORRO_SIMPLE,
			PD.PAGOSDIA - PD.RETIROS_AHORRO_SIMPLE AS TOTAL,
			PD.FECHA_APERTURA_AHORRO,
			C.NO_CREDITO,
			C.CICLO,
			C.CLIENTE,
			C.ID_SUCURSAL,
			C.SUCURSAL,
			C.ID_EJECUTIVO,
			C.EJECUTIVO
		FROM (
			SELECT 
				-- Total de pagos del día
				(SELECT NVL(SUM(MONTO), 0)
				   FROM PAGOSDIA
				  WHERE CDGNS = '$cdgns'
					AND ESTATUS = 'A' AND TIPO IN('B', 'F')) AS PAGOSDIA,
					
				-- Total de retiros de ahorro simple
				(SELECT NVL(SUM(CANTIDAD_AUTORIZADA), 0)
				   FROM RETIROS_AHORRO_SIMPLE
				  WHERE CDGNS = '$cdgns') AS RETIROS_AHORRO_SIMPLE,
				  
				-- Fecha del primer registro tipo B o F (inicio del ahorro)
				(SELECT MIN(FECHA)
				   FROM PAGOSDIA
				  WHERE CDGNS = '$cdgns'
					AND TIPO IN ('B','F')) AS FECHA_APERTURA_AHORRO
			FROM DUAL
		) PD
		CROSS JOIN (
			SELECT *
			FROM (
				SELECT 
					SC.CDGNS NO_CREDITO,
					SC.CICLO,
					GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
					SN.CDGCO ID_SUCURSAL,
					GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
					SN.CDGOCPE ID_EJECUTIVO,
					GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
				FROM 
					SN
					JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO
					JOIN SC Q2 ON SC.CDGNS = Q2.CDGNS AND SC.CICLO = Q2.CICLO AND SC.CDGCL <> Q2.CDGCL
					JOIN PRN ON PRN.CICLO = SC.CICLO AND PRN.CDGNS = SC.CDGNS
				WHERE
					SC.CDGNS = '$cdgns'
					AND SC.CANTSOLIC <> '9999'
				ORDER BY SC.SOLICITUD DESC
			)
			WHERE ROWNUM = 1
		) C
sql;


		$query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
		PAGOSDIA.CICLO,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO,
		'ABONO' AS TIPO_OPERA
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
		AND PAGOSDIA.TIPO IN ('B', 'F')
        AND PAGOSDIA.CDGNS = $cdgns
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;
		$mysqli = new Database();

		$res1 = $mysqli->queryOne($query_datos);
		$res2 = $mysqli->queryAll($query);
		return [$res1, $res2];
	}


    public static function ListarClientesSinContrato($user)
    {

        // 1️⃣ Obtiene las sucursales asignadas al usuario
        $query_obten_sucu = "SELECT CDGCO FROM PCO WHERE CDGPE = '$user'";

        $mysqli = new Database();
        $sucursales = $mysqli->queryAll($query_obten_sucu);

        // 2️⃣ Convierte los resultados en una lista para el IN
        $listaSucursales = [];

        if (!empty($sucursales)) {
            foreach ($sucursales as $row) {
                $listaSucursales[] = "'" . $row['CDGCO'] . "'";
            }
        }

        // Si el usuario no tiene sucursales asignadas, evita error en SQL
        if (empty($listaSucursales)) {
            $listaSucursales[] = "'NULL'";
        }

        $in_sucursales = implode(",", $listaSucursales);

        // 3️⃣ Consulta principal, ahora filtrando por las sucursales del usuario
        $query = <<<SQL
        SELECT 
            P.CDGNS,
            P.NOMBRE,
            PRN.CDGCO,
            CO.NOMBRE AS SUCURSAL
        FROM 
            PAGOSDIA P
            INNER JOIN PRN ON PRN.CDGNS = P.CDGNS AND PRN.CICLO = P.CICLO
            INNER JOIN CO ON CODIGO = PRN.CDGCO
        WHERE 
            P.TIPO IN ('F', 'B')
            AND ESTATUS = 'A'
            AND PRN.CDGCO IN ($in_sucursales)
            AND NOT EXISTS (
                SELECT 1 FROM CONTRATOS_AHORRO C WHERE C.CDGNS = P.CDGNS
            )
        GROUP BY 
            P.CDGNS, P.NOMBRE, PRN.CDGCO, CO.NOMBRE
        ORDER BY 
            P.NOMBRE
    SQL;

        // 4️⃣ Retorna el resultado
        return $mysqli->queryAll($query);
    }



	public static function insertContrato($contrato)
	{
		$mysqli = new Database();

		// 1. Obtenemos el siguiente valor de la secuencia
		$idQuery = "SELECT CONTRATOS_AHORRO_SEQQ.NEXTVAL AS ID_CONTRATO FROM DUAL";
		$idData = $mysqli->queryOne($idQuery);
		$id_contrato = $idData['ID_CONTRATO'];

		// 2. Insertamos el contrato usando ese ID
		$query = <<<sql
			INSERT INTO CONTRATOS_AHORRO
			(ID_CONTRATO, CDGNS, FECHA_REGISTRO, TIPO_AHORRO, TASA_ANUAL, CDGPE)
			VALUES(
				'{$id_contrato}', 
				'{$contrato->_cdgns}', 
				TO_DATE('{$contrato->_fecha_registro}', 'YYYY-MM-DD HH24:MI:SS'),
				'{$contrato->_tipo_ahorro}',
				'{$contrato->_tasa_anual}',
				'AMGM'
			)
		sql;

		$mysqli->insert($query);
		return $id_contrato;
	}

	public static function insertBeneficiarios($beneficiarios, $idContrato, $cdgpe_alta, $cdgpe)
	{
		$mysqli = new Database();
		$insertados = 0;

		foreach ($beneficiarios as $benef) {
			$nombre = substr(str_replace("'", "''", $benef['nombre']), 0, 100);
			$parentesco = substr(str_replace("'", "''", $benef['parentesco']), 0, 30);
			$porcentaje = floatval($benef['porcentaje']);
			$cdgpe_alta = substr(str_replace("'", "''", $cdgpe_alta), 0, 10);
			$cdgpe = substr(str_replace("'", "''", $cdgpe), 0, 10);

			$query = <<<sql
				INSERT INTO CONTRATOS_BENEFICIARIOS
				(ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
				VALUES
				($idContrato, '$nombre', '$parentesco', $porcentaje, '$cdgpe_alta', '$cdgpe')
			sql;

			$mysqli->insert_bene($query);
			$insertados++;
		}

		return $insertados; // solo un número interno, no se hace echo
	}

	public static function GetCliente($datos)
	{
		$query = <<<SQL
			SELECT
				CL.CODIGO AS CLIENTE
				, CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE
			FROM 
				PRC
				LEFT JOIN CL ON PRC.CDGCL = CL.CODIGO
				INNER JOIN PRN ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO
			WHERE 
				PRC.CDGNS = :credito
				AND PRN.SITUACION = 'E'
			FETCH FIRST 1 ROWS ONLY
		SQL;

		$params = ['credito' => $datos['credito']];

		try {
			$db = new Database();
			$res = $db->queryOne($query, $params);
			return self::Responde(true, 'Cliente obtenido', $res);
		} catch (\Exception $e) {
			return  self::Responde(false, 'Error al obtener cliente', null, $e->getMessage());
		}
	}

	public static function RegistraContrato($datos)
	{
		$qryContrato = <<<SQL
			INSERT INTO CONTRATOS_AHORRO
				(CDGNS, TIPO_AHORRO, TASA_ANUAL, CDGPE_ALTA, CDGPE)
			VALUES
				(:credito, :tipo, :tasa, :cdgpe_alta, :cdgpe)
		SQL;

		$qryBeneficiario = <<<SQL
			INSERT INTO CONTRATOS_BENEFICIARIOS
				(ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
			VALUES
				((SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito), :nombre, :parentesco, :porcentaje, :cdgpe_alta, :cdgpe)
		SQL;

		$inserts = [];
		$datosInsert = [];

		$inserts[] = $qryContrato;
		$datosInsert[] = [
			'credito' => $datos['noCredito'],
			'tipo' => $datos['tipo'],
			'tasa' => $datos['tasa'],
			'cdgpe_alta' => $datos['ejecutivo'],
			'cdgpe' => $datos['ejecutivo']
		];

		foreach ($datos['beneficiario_parentesco'] as $index => $p) {
			$inserts[] = $qryBeneficiario;
			$datosInsert[] = [
				'credito' => $datos['noCredito'],
				'nombre' => $datos['beneficiario_nombre'][$index],
				'parentesco' => $datos['beneficiario_parentesco'][$index],
				'porcentaje' => $datos['beneficiario_porcentaje'][$index],
				'cdgpe_alta' => $datos['ejecutivo'],
				'cdgpe' => $datos['ejecutivo']
			];
		}

		try {
			$db = new Database();
			$res = $db->insertaMultiple($inserts, $datosInsert);
			if ($res) return self::Responde(true, "Contrato de ahorro registrado correctamente", $res);
			return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.");
		} catch (\Exception $e) {
			return  self::Responde(false, 'Error al registrar el contrato de ahorro', null, $e->getMessage());
		}
	}

	public static function GetBeneficiarios($datos)
	{
		$query = <<<SQL
			SELECT
				NOMBRE_COMPLETO,
				PARENTESCO,
				PORCENTAJE
			FROM 
				CONTRATOS_BENEFICIARIOS
			WHERE 
				ID_CONTRATO = (SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito)
		SQL;

		$params = ['credito' => $datos['credito']];

		try {
			$db = new Database();
			$res = $db->queryAll($query, $params);
			return self::Responde(true, 'Beneficiarios obtenidos', $res);
		} catch (\Exception $e) {
			return  self::Responde(false, 'Error al obtener beneficiarios', null, $e->getMessage());
		}
	}

	public static function ActualizaBeneficiarios($datos)
	{
		$qryDelete = <<<SQL
			DELETE FROM CONTRATOS_BENEFICIARIOS
			WHERE ID_CONTRATO = (SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito)
		SQL;

		$qryBeneficiario = <<<SQL
			INSERT INTO CONTRATOS_BENEFICIARIOS
				(ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
			VALUES
				((SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito), :nombre, :parentesco, :porcentaje, :cdgpe_alta, :cdgpe)
		SQL;

		$inserts = [];
		$datosInsert = [];

		$inserts[] = $qryDelete;
		$datosInsert[] = [
			'credito' => $datos['noCredito']
		];

		foreach ($datos['beneficiario_parentesco'] as $index => $p) {
			$inserts[] = $qryBeneficiario;
			$datosInsert[] = [
				'credito' => $datos['noCredito'],
				'nombre' => $datos['beneficiario_nombre'][$index],
				'parentesco' => $datos['beneficiario_parentesco'][$index],
				'porcentaje' => $datos['beneficiario_porcentaje'][$index],
				'cdgpe_alta' => $datos['ejecutivo'],
				'cdgpe' => $datos['ejecutivo']
			];
		}

		try {
			$db = new Database();
			$res = $db->insertaMultiple($inserts, $datosInsert);
			if ($res) return self::Responde(true, "Beneficiarios actualizados correctamente", $res);
			return self::Responde(false, "Ocurrió un error al actualizar los beneficiarios.");
		} catch (\Exception $e) {
			return  self::Responde(false, 'Error al actualizar los beneficiarios', null, $e->getMessage());
		}
	}
}
