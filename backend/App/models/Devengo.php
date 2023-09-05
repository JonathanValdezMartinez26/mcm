<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Devengo{

    public static function ConsultaExiste($noCredito, $noCiclo){
      $mysqli = Database::getInstance();


        $query=<<<sql
        SELECT COUNT(*) AS EXISTE FROM TBL_CIERRE_DIA WHERE CDGEM = 'EMPFIN' AND CDGCLNS = '$noCredito'
        AND CICLO = '$noCiclo' AND FECHA_LIQUIDA IS NOT NULL
sql;

        $existe = $mysqli->queryOne($query);

        if($existe['EXISTE'] == 1)
        {
            $query1=<<<sql
            SELECT CDGCLNS, NOMBRE, CICLO, COD_SUCURSAL, NOM_SUCURSAL, NOM_ASESOR, TO_CHAR(FECHA_LIQUIDA, 'YYYY-MM-DD' ) AS FECHA_LIQUIDA, REGION, TO_CHAR(INICIO, 'YYYY-MM-DD') AS INICIO ,  TO_CHAR(FIN, 'YYYY-MM-DD') AS FIN, PLAZO, TASA, INTERES_GLOBAL,
            TOTAL_PAGAR, SITUACION  AS RES FROM TBL_CIERRE_DIA 
            WHERE CDGEM = 'EMPFIN'
            AND CDGCLNS = '$noCredito'
            AND CICLO = '$noCiclo'
            AND FECHA_LIQUIDA IS NOT NULL 
sql;

            $d_liq = $mysqli->queryOne($query1);

            $fec_liq = $d_liq['FECHA_LIQUIDA'];

            $query2=<<<sql
            select 
               TO_CHAR(TO_DATE(FECHA_LIQUIDA,'dd-mm-YY'), 'YYYY-MM-DD') AS LIQUIDARON_EL_DIA,
               TO_CHAR(TO_DATE(SYSDATE,'dd-mm-YY'), 'YYYY-MM-DD') AS HOY_ES,
               TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd')) as DIAS_SIN_DEVENGO, 
               INTERES_GLOBAL,
               TO_NUMBER((SELECT DIAS_DEV FROM DEVENGO_DIARIO WHERE CDGEM = 'EMPFIN' AND CDGCLNS = '$noCredito' AND CICLO = '$noCiclo' AND TO_DATE(FECHA_CALC ,'dd-mm-YY') = TO_DATE('$fec_liq','YY-mm-dd')) + (TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd')))) * (TO_NUMBER((SELECT DEV_DIARIO FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' ))) AS INT_DEV, 
               TO_NUMBER((SELECT DEV_DIARIO FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' )) AS INTERES_DIARIO,
               TO_NUMBER((SELECT PLAZO FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' )) AS PLAZO,
               (SELECT PLAZO_DIAS  FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo') AS PLAZO_DIAS, 
               TO_NUMBER((SELECT DEV_DIARIO_SIN_IVA FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' ))  * (TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd'))) AS DEV_DIARIO_SIN_IVA,
               TO_NUMBER((SELECT IVA_INT FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' ))  * (TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd'))) AS IVA_INT,
               TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd')) * TO_NUMBER((SELECT DEV_DIARIO FROM DEVENGO_DIARIO WHERE DIAS_DEV = 1 AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' )) AS DEBE,
               (SELECT DIAS_DEV FROM DEVENGO_DIARIO WHERE CDGEM = 'EMPFIN' AND CDGCLNS = '$noCredito' AND CICLO = '$noCiclo' AND TO_DATE(FECHA_CALC ,'dd-mm-YY') = TO_DATE('$fec_liq','YY-mm-dd')) + (TO_NUMBER(TO_DATE(SYSDATE,'dd-mm-YY') - TO_DATE('$fec_liq','YY-mm-dd'))) AS DDD_FINAL 
               from TBL_CIERRE_DIA WHERE CDGEM='EMPFIN' AND CDGCLNS='$noCredito' AND CICLO='$noCiclo' AND FECHA_LIQUIDA IS NOT NULL
sql;

            //var_dump("*******".$query2);

            $d_datos = $mysqli->queryOne($query2);
            return[$d_liq, $d_datos];
        }
        else
        {
            return false;
        }

    }

    public static function ReactivarCredito($noCredito, $noCiclo){
        $mysqli = Database::getInstance();
        $query=<<<sql
    SELECT * FROM TBL_CIERRE_DIA 
    WHERE CDGEM = 'EMPFIN'
    AND CDGCLNS = '$noCredito'
    AND CICLO = '$noCiclo'
    AND FECHA_LIQUIDA IS NOT NULL 
sql;
        return $mysqli->queryOne($query);

    }

}
