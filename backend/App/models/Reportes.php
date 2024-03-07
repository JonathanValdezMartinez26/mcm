<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\Database_cultiva;

class Reportes
{

    public static function ConsultaUsuariosSICAFINMCM()
    {

        $mysqli = Database::getInstance();
        $query = <<<sql
        SELECT
            *
        FROM
            (
                SELECT
                    PE.CODIGO AS COD_USUARIO,
                    CONCATENA_NOMBRE(NOMBRE1, NOMBRE2, PRIMAPE, SEGAPE) AS NOMBRE_COMPLETO,
                    --DESDEUS AS FECHA_ALTA,
                    TO_CHAR(DESDEUS, 'DD/MM/YYYY') AS FECHA_ALTA,
                    CO.CODIGO AS COD_SUCURSAL,
                    CO.NOMBRE AS SUCURSAL,
                    PE.TELEFONO AS NOMINA,
                    PE.CALLE AS NOMINA_JEFE,
                    --ACTIVO,
                    CASE
                        WHEN PE.ACTIVO = 'S' THEN 'SI'
                        WHEN PE.ACTIVO = 'N' THEN 'NO'
                        ELSE PE.ACTIVO
                    END AS ACTIVO,
                    --'' AS PUESTO
                    (
						SELECT
							WM_CONCAT(TUS.NOMBRE)
						FROM
							TUS,
							UT
						WHERE
							TUS.CODIGO = UT.CDGTUS
							AND UT.CDGEM = 'EMPFIN'
							AND TUS.CDGEM = UT.CDGEM
							AND UT.CDGPE = PE.CODIGO
                            AND TUS.NOMBRE != 'SUPER ADMINISTRADOR'
                    ) AS PUESTO
                FROM
                    PE,
                    CO
                WHERE
                    CO.CODIGO = PE.CDGCO
                UNION
                SELECT
                    PE.CODIGO AS COD_USUARIO,
                    CONCATENA_NOMBRE(NOMBRE1, NOMBRE2, PRIMAPE, SEGAPE) AS NOMBRE_COMPLETO,
                    --DESDEUS AS FECHA_ALTA,
                    TO_CHAR(DESDEUS, 'DD/MM/YYYY') AS FECHA_ALTA,
                    '' AS COD_SUCURSAL,
                    '' AS SUCURSAL,
                    PE.TELEFONO AS NOMINA,
                    PE.CALLE AS NOMINA_JEFE,
                    --ACTIVO,
                    CASE
                        WHEN PE.ACTIVO = 'S' THEN 'SI'
                        WHEN PE.ACTIVO = 'N' THEN 'NO'
                        ELSE PE.ACTIVO
                    END AS ACTIVO,
                    --'' AS PUESTO
                    (
						SELECT
							WM_CONCAT(TUS.NOMBRE)
						FROM
							TUS,
							UT
						WHERE
							TUS.CODIGO = UT.CDGTUS
							AND UT.CDGEM = 'EMPFIN'
							AND TUS.CDGEM = UT.CDGEM
							AND UT.CDGPE = PE.CODIGO
                            AND TUS.NOMBRE != 'SUPER ADMINISTRADOR'
                    ) AS PUESTO
                FROM
                    PE
                WHERE
                    PE.CDGCO IS NULL
            )
        ORDER BY
            TO_DATE(FECHA_ALTA, 'DD/MM/YYYY') ASC
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }
    public static function ConsultaUsuariosSICAFINCultiva()
    {

        $mysqli = Database_cultiva::getInstance();
        $query = <<<sql

           SELECT * FROM(
           SELECT PE.CODIGO AS COD_USUARIO, (NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE ) AS NOMBRE_COMPLETO, DESDEUS AS FECHA_ALTA, 
           CO.CODIGO AS COD_SUCURSAL, CO.NOMBRE AS SUCURSAL, PE.TELEFONO AS NOMINA, PE.CALLE AS NOMINA_JEFE, ACTIVO, '' AS PUESTO 
           FROM PE
           INNER JOIN CO ON CO.CODIGO = PE.CDGCO 
           UNION
           SELECT PE.CODIGO AS COD_USUARIO, (NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE ) AS NOMBRE_COMPLETO, DESDEUS AS FECHA_ALTA, 
           '' AS COD_SUCURSAL, '' AS SUCURSAL, PE.TELEFONO AS NOMINA, PE.CALLE AS NOMINA_JEFE, ACTIVO, '' AS PUESTO 
           FROM PE
           WHERE CDGCO IS NULL)
           
           ORDER BY FECHA_ALTA ASC

		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }
}
