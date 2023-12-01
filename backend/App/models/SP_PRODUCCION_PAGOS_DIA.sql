CREATE OR REPLACE PROCEDURE ESIACOM.SPACCIONPAGODIA (prmCDGEM IN PAGOSDIA.CDGEM%TYPE,
                                                     prmFECHA IN PAGOSDIA.FECHA%TYPE,
                                                     prmFECHAAUX IN PAGOSDIA.FECHA%TYPE,
                                                     prmCDGNS IN PAGOSDIA.CDGNS%TYPE,
                                                     prmCICLO IN PAGOSDIA.CICLO%TYPE,
                                                     prmSECUENCIA IN PAGOSDIA.SECUENCIA%TYPE,
                                                     prmNOMBRE IN PAGOSDIA.NOMBRE%TYPE,
                                                     prmCDGOCPE IN PAGOSDIA.CDGOCPE%TYPE,
                                                     prmEJECUTIVO IN PAGOSDIA.EJECUTIVO%TYPE,
                                                     prmCDGPE IN PAGOSDIA.CDGOCPE%TYPE,
                                                     prmMONTO IN PAGOSDIA.MONTO%TYPE,
                                                     prmTIPOMOV IN PAGOSDIA.TIPO%TYPE,
                                                     prmTIPO IN NUMBER,
                                                     vMensaje OUT VARCHAR2) IS
/******************************************************************************
   NAME:       SPACCIONPAGODIA
   PURPOSE:

   REVISIONS:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        06/07/2020  ANGEL GUERRERO   1. Created this procedure.

******************************************************************************/

      ecode          NUMBER;
      emesg          VARCHAR2(200);
      vCount         NUMBER;
      vCount2        NUMBER;
      vHora          NUMBER;
      vTipo          VARCHAR2(1);
      valTiposUnicos number:=0;
      vMontoElim     NUMBER(12,2);
BEGIN

     vTipo := prmTIPOMOV;

--     --CONSULTA PARA SABER SI EL CREDITO ES DE UNA AGENCIA QUE TIENE CAJA
--     SELECT COUNT(*) INTO vCount2 FROM PRN WHERE CDGEM = prmCDGEM AND CDGNS = prmCDGNS AND CICLO = prmCICLO
--     AND CDGCO IN ('001',  --TENANCINGO
--                   '002',  --XONACATLAN
--                   '003',  --CHOLULA 1
--                   '004',  --HUAMANTLA
--                   '005',  --CHOLULA 2
--                   '006',  --TENANGO
--                   '007',  --ZINA 1
--                   '008',  --PUEBLA SUR
--                   '009',  --ATIZAPAN
--                   '010',  --SANTA ANA
--                   '011',  --ATLIXCO
--                   '012',  --APIZACO
--                   '013',  --PUEBLA NORTE
--                   '014',  --TOLUCA
--                   '015',  --IXTAPALUCA
--                   '016',  --ZINA 2
--                   '017', --ZACATLAN
--                   '018'); --CHALCO-AMECA

SELECT TO_NUMBER(TO_CHAR(SYSDATE,'HH24')) INTO vHora FROM DUAL;
--
--     --VALIDA SI ES UNA HORA VALIDA PARA LA CAPTURA DE PAGOS POR PARTE DE LAS CAJERAS
--     IF vCount2 = 1 THEN
--
--        IF (vHora < 8 OR vHora > 18) AND prmCDGPE NOT IN ('SORA','AMGM','GASC')THEN
--            vMensaje := '0 No se puede capturar el pago. Caja cerrada.';
--            RETURN;
--        END IF;
--     END IF;

IF (vHora < 7 OR vHora > 18) AND prmCDGPE NOT IN ('SORA','AMGM','GASC')THEN
        vMensaje := '0 No se puede capturar el pago. Caja cerrada.';
        RETURN;
END IF;

     --VALIDA QUE NO SE PUEDAN CAPTURAR PAGOS DEL DIA ANTERIOR DESPUES DE LAS 12 DEL MEDIO DIA
     IF vHora >= 12 AND prmFECHA < TRUNC(SYSDATE) AND prmCDGPE NOT IN ('SORA','AMGM','GASC') THEN
        vMensaje := '0 No se puede registrar/actualizar/eliminar un movimiento con fecha anterior al día de hoy. Hora de captura no permitida';
        RETURN;
END IF;


     IF prmTIPO = 1 THEN

        -- SOOA 04/01/2023: Validacion que verifica el numero de movimientos, que no sean pagos, en el ciclo
        -- SOOA 23/01/2023: Se agrega exclusion que permite registrar mas de un movimiento a ciertos usuarios
        IF prmCDGPE NOT IN ('SORA','AMGM','GASC') AND vTipo <> 'P' AND vTipo <> 'M' THEN
SELECT COUNT(*) INTO valTiposUnicos
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND CDGNS = prmCDGNS
  AND CICLO = prmCICLO
  AND TIPO = vTipo
  AND ESTATUS = 'A';
END IF;

        IF valTiposUnicos > 0 THEN
          vMensaje := '0 No se puede guardar la informacion, ya se ha realizado un registro previo de tipo ' || TIPO_OPERACION(vTipo) || ' en el ciclo ' || prmCICLO || '.';
          RETURN;
END IF;
        -- SOOA 04/01/2023: Fin Modificacion

SELECT COUNT(*) INTO vCount
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA;

vCount := vCount + 1;

        IF prmTIPOMOV = 'I' THEN
            vCount:= 0;
            vTipo:= 'P';
END IF;

INSERT INTO PAGOSDIA (CDGEM,
                      FECHA,
                      CDGNS,
                      NOMBRE,
                      CICLO,
                      CDGOCPE,
                      EJECUTIVO,
                      SECUENCIA,
                      FREGISTRO,
                      CDGPE,
                      ESTATUS,
                      MONTO,
                      TIPO)
VALUES (prmCDGEM,
        prmFECHA,
        prmCDGNS,
        prmNOMBRE,
        prmCICLO,
        prmCDGOCPE,
        prmEJECUTIVO,
        vCount,
        SYSDATE,
        prmCDGPE,
        'A',
        prmMONTO,
        vTipo);
COMMIT;
END IF;

     IF prmTIPO = 2 THEN



     ----------------------------------------------------------
	 IF vTipo = 'S' OR vTipo = 'G' THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND CDGNS = prmCDGNS
  AND CICLO = prmCICLO
  AND TIPO = vTipo
  AND ESTATUS = 'A';

IF valTiposUnicos > 0 THEN
	          vMensaje := 'No se puede actualizar, ' || TIPO_OPERACION(vTipo) || ' ya existe en el ciclo ' || prmCICLO || '.';
	          RETURN;
END IF;
END IF;
  ------------------------------------------------------------------



        -- AMGM 28/02/2023: Validacion que NO permite eliminar un pago si ya se aplico en la cartera

        -- Obtenemos el monto del movimiento
        IF prmCDGNS IS NOT NULL THEN

SELECT MONTO INTO vMontoElim
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHAAUX
  AND CDGNS = prmCDGNS
  AND SECUENCIA = prmSECUENCIA;

ELSE

SELECT MONTO INTO vMontoElim
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHAAUX
  AND SECUENCIA = prmSECUENCIA;

END IF;

        --Validamos el tipo de movimiento que se va a eliminar
        IF vTipo NOT IN ('M','S') THEN

              -- Validamos si existe como pago
SELECT COUNT(*) INTO valTiposUnicos
FROM MP
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND TIPO = 'PD'
  AND FREALDEP = prmFECHAAUX
  AND CANTIDAD = vMontoElim;

IF valTiposUnicos = 0 THEN

                  --Validamos si existe como garantía
SELECT COUNT(*) INTO valTiposUnicos
FROM PAG_GAR_SIM
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND FPAGO = prmFECHAAUX
  AND ESTATUS = 'RE'
  AND CANTIDAD = vMontoElim;

IF valTiposUnicos = 0 THEN

                      --Validamos si existe como Descuento
SELECT COUNT(*) INTO valTiposUnicos
FROM MP, MPR
WHERE MP.CDGEM = MPR.CDGEM
  AND MP.CDGCLNS = MPR.CDGNS
  AND MP.CICLO = MPR.CICLO
  AND MP.PERIODO = MPR.PERIODO
  AND MP.SECUENCIA = MPR.SECUENCIA
  AND MP.CDGEM = prmCDGEM
  AND MP.CDGCLNS = prmCDGNS
  AND MP.CICLO = prmCICLO
  AND MP.TIPO IN ('AA','AC','CI','MI')
  AND MP.FREALDEP = prmFECHAAUX
  AND MPR.RAZON = '04';

IF valTiposUnicos = 0 THEN

                        --Validamos si existe como Refinanciamiento
SELECT COUNT(*) INTO valTiposUnicos
FROM MP, MPR
WHERE MP.CDGEM = MPR.CDGEM
  AND MP.CDGCLNS = MPR.CDGNS
  AND MP.CICLO = MPR.CICLO
  AND MP.PERIODO = MPR.PERIODO
  AND MP.SECUENCIA = MPR.SECUENCIA
  AND MP.CDGEM = prmCDGEM
  AND MP.CDGCLNS = prmCDGNS
  AND MP.CICLO = prmCICLO
  AND MP.TIPO IN ('AA','AC','CI','MI')
  AND MP.FREALDEP = prmFECHAAUX
  AND MPR.RAZON = '09';

END IF;

END IF;


END IF;

ELSE

            --Validamos si ya se reaizo el cierre de día para este crédito
            IF prmCDGNS IS NOT NULL THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM TBL_CIERRE_DIA
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND FECHA_CALC = prmFECHAAUX;

ELSE

                valTiposUnicos := 0;

END IF;


END IF;

        IF valTiposUnicos > 0 THEN
          vMensaje := '0 No se puede modificar el movimiento porque ya se aplico en el cierre diario. Favor de solicitar apoyo a Operaciones.';
          RETURN;
END IF;
        -- AMGM 28/02/2023: Fin Modificacion


        IF prmFECHA = prmFECHAAUX THEN  -- Si son iguales quiere decir que solo cambio el monto o el tipo

               vCount:= prmSECUENCIA;

               IF prmTIPOMOV = 'I' THEN
                    vCount:= 0;
                    vTipo:= 'P';
END IF;

               IF prmSECUENCIA = 0 AND prmTIPOMOV <> 'I' THEN

SELECT COUNT(*) INTO vCount
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA;

vCount := vCount + 1;

END IF;


UPDATE PAGOSDIA SET MONTO = prmMONTO,
                    TIPO = vTipo,
                    CDGPE = prmCDGPE,
                    CICLO = prmCICLO,
                    CDGOCPE = prmCDGOCPE,
                    EJECUTIVO = prmEJECUTIVO,
                    FACTUALIZA = SYSDATE,
                    SECUENCIA = vCount
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA
  AND CDGNS = prmCDGNS
  AND SECUENCIA = prmSECUENCIA;
COMMIT;


ELSE --Si entra aqui es porque la fecha del pago cambio

             --1) borrar el registro con la fecha anterior
UPDATE PAGOSDIA SET ESTATUS = 'E',
                    CDGPE = prmCDGPE,
                    FACTUALIZA = SYSDATE
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHAAUX
  AND CDGNS = prmCDGNS
  AND SECUENCIA = prmSECUENCIA;

--2) Registramos el pago como nuevo

--2.1) Se determina la secuencia del pago
SELECT COUNT(*) INTO vCount
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA;

vCount := vCount + 1;

               IF prmTIPOMOV = 'I' THEN
                    vCount:= 0;
                    vTipo:= 'P';
END IF;

               --2.2) Se inserta el nuevo pago
INSERT INTO PAGOSDIA (CDGEM,
                      FECHA,
                      CDGNS,
                      NOMBRE,
                      CICLO,
                      CDGOCPE,
                      EJECUTIVO,
                      SECUENCIA,
                      FREGISTRO,
                      CDGPE,
                      ESTATUS,
                      MONTO,
                      TIPO)
VALUES (prmCDGEM,
        prmFECHA,
        prmCDGNS,
        prmNOMBRE,
        prmCICLO,
        prmCDGOCPE,
        prmEJECUTIVO,
        vCount,
        SYSDATE,
        prmCDGPE,
        'A',
        prmMONTO,
        vTipo);
COMMIT;

END IF;

END IF;

     IF prmTIPO = 3 THEN

        -- AMGM 28/02/2023: Validacion que NO permite eliminar un pago si ya se aplico en la cartera

        -- Obtenemos el monto del movimiento
        IF prmCDGNS IS NOT NULL THEN

SELECT MONTO INTO vMontoElim
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA
  AND CDGNS = prmCDGNS
  AND SECUENCIA = prmSECUENCIA;

ELSE

SELECT MONTO INTO vMontoElim
FROM PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA
  AND SECUENCIA = prmSECUENCIA;

END IF;

        --Validamos el tipo de movimiento que se va a eliminar
        IF vTipo = 'P' THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM MP
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND TIPO = 'PD'
  AND FREALDEP = prmFECHA
  AND CANTIDAD = vMontoElim;

ELSIF vTIPO = 'G' THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM PAG_GAR_SIM
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND FPAGO = prmFECHA
  AND ESTATUS = 'RE'
  AND CANTIDAD = vMontoElim;

ELSIF vTIPO = 'D' THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM MP, MPR
WHERE MP.CDGEM = MPR.CDGEM
  AND MP.CDGCLNS = MPR.CDGNS
  AND MP.CICLO = MPR.CICLO
  AND MP.PERIODO = MPR.PERIODO
  AND MP.SECUENCIA = MPR.SECUENCIA
  AND MP.CDGEM = prmCDGEM
  AND MP.CDGCLNS = prmCDGNS
  AND MP.CICLO = prmCICLO
  AND MP.TIPO IN ('AA','AC','CI','MI')
  AND MP.FREALDEP = prmFECHA
  AND MPR.RAZON = '04';

ELSIF vTIPO = 'R' THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM MP, MPR
WHERE MP.CDGEM = MPR.CDGEM
  AND MP.CDGCLNS = MPR.CDGNS
  AND MP.CICLO = MPR.CICLO
  AND MP.PERIODO = MPR.PERIODO
  AND MP.SECUENCIA = MPR.SECUENCIA
  AND MP.CDGEM = prmCDGEM
  AND MP.CDGCLNS = prmCDGNS
  AND MP.CICLO = prmCICLO
  AND MP.TIPO IN ('AA','AC','CI','MI')
  AND MP.FREALDEP = prmFECHA
  AND MPR.RAZON = '09';

ELSE

            --Validamos si ya se reaizo el cierre de día para este crédito
            IF prmCDGNS IS NOT NULL THEN

SELECT COUNT(*) INTO valTiposUnicos
FROM TBL_CIERRE_DIA
WHERE CDGEM = prmCDGEM
  AND CDGCLNS = prmCDGNS
  AND CICLO = prmCICLO
  AND FECHA_CALC = prmFECHAAUX;

ELSE

                valTiposUnicos := 0;

END IF;



END IF;

        IF valTiposUnicos > 0 THEN
          vMensaje := '0 No se puede eliminar el movimiento porque ya se aplico en el cierre diario. Favor de solicitar apoyo a Operaciones.';
          RETURN;
END IF;
        -- AMGM 28/02/2023: Fin Modificacion


         IF prmSECUENCIA = 0 THEN --AND prmCDGPE NOT IN ('SORA','AMGM','GASC') THEN

            IF prmCDGNS IS NULL OR prmCDGNS = '' THEN

                vMensaje := '0 No se puede eliminar un primer pago. Comunicate con Operaciones.';
                RETURN;

ELSE

                --vMensaje := '0 No se puede eliminar un primer pago. Comunicate con Operaciones.' || prmCDGNS || prmFECHA || prmSECUENCIA;
                --RETURN;

UPDATE PAGOSDIA SET ESTATUS = 'E',
                    CDGPE = prmCDGPE,
                    FACTUALIZA = SYSDATE
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA
  AND CDGNS = prmCDGNS
  AND SECUENCIA = prmSECUENCIA;
COMMIT;

END IF;

ELSE

UPDATE PAGOSDIA SET ESTATUS = 'E',
                    CDGPE = prmCDGPE,
                    FACTUALIZA = SYSDATE
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA
  AND SECUENCIA = prmSECUENCIA;
COMMIT;

END IF;


END IF;

     vMensaje := '1 Proceso realizado exitosamente';

exception
     when others then
          ecode := SQLCODE;
          emesg := SQLERRM;
rollback;
vMensaje := '0 Fallo el proceso. Descripción: ' || TO_CHAR(ecode) || '-' || emesg;
END SPACCIONPAGODIA;
