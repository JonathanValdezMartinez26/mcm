<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;

class Promociones{

    public static function ConsultarClientesInvitados($cdgns){
        $query=<<<sql

       SELECT CL_INVITADO, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' '|| CL.PRIMAPE || ' '|| CL.SEGAPE ) AS NOMBRE,
       PRN.CANTENTRE AS CANTIDAD_ENTREGADA, ABS((MP.CANTIDAD  * 0.10)) AS DESCUENTO, CPT.CICLO_INVITACION, CPT.ESTATUS_PAGADO  
       FROM CL_PROMO_TELARANA CPT
       INNER JOIN CL ON CL.CODIGO = CPT.CL_INVITADO
       INNER JOIN PRN ON PRN.CDGNS = CPT.CDGNS_INVITA
       INNER JOIN MP ON MP.CDGNS = CPT.CDGNS_INVITA 
       WHERE CPT.CDGNS_INVITA = '$cdgns'
       AND PRN.CICLO = '01'
       AND MP.CICLO = '01' AND MP.TIPO = 'IN'
       ORDER BY CPT.CICLO_INVITACION DESC
  
sql;

        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryAll($query);
        } catch (Exception $e) {
            return "";
        }
    }

    public static function ConsultarDatosClienteRecomienda($cdgns){
        $query=<<<sql
      SELECT * FROM(
	   SELECT CL_INVITA, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' '|| CL.PRIMAPE || ' '|| CL.SEGAPE ) AS NOMBRE, 
	          PRN.CICLO, PRN.CDGNS, CO. NOMBRE AS SUCURSAL,  
	              
	   TO_CHAR(TCD.INICIO ,'YYYY/MM/DD') AS INICIO,
	   TO_CHAR(TCD.FIN ,'YYYY/MM/DD') AS FIN, TCD.PLAZO
	              
	   FROM CL_PROMO_TELARANA CPT
       INNER JOIN CL ON CL.CODIGO = CPT.CL_INVITA
       INNER JOIN PRN ON PRN.CDGNS = CPT.CDGNS_INVITA 
	   INNER JOIN CO ON PRN.CDGCO = CO.CODIGO
	   INNER JOIN TBL_CIERRE_DIA TCD  ON TCD.CDGCLNS = CPT.CDGNS_INVITA 
	   
       WHERE CPT.CDGNS_INVITA = '$cdgns' AND PRN.SITUACION = 'E'
       AND TCD.CICLO = PRN.CICLO 
       )
       GROUP BY CL_INVITA, NOMBRE, CICLO, CDGNS, SUCURSAL, INICIO, FIN, PLAZO
sql;
        try {
            $mysqli = Database::getInstance();
            return $mysqli->queryOne($query);
        } catch (Exception $e) {
            return "";
        }
    }
}
