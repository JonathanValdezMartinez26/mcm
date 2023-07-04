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
                                                     vMensaje OUT VARCHAR2,
                                                     prmIdentificaAPP IN VARCHAR2:=''  --14ABR2023 REGISTRO DE IDENTIFICADOR PARA PAGOS QUE SE REGISTRAN POR LA APP
                                                     ) IS
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
      valTiposUnicos number;
      vFIdentificApp DATE:=NULL;
BEGIN

     vTipo := prmTIPOMOV;

     IF prmIdentificaAPP IS NULL THEN

        vFIdentificApp := NULL;

ELSE

        vFIdentificApp := TO_DATE(SUBSTR(prmIdentificaAPP,1,2) || '/' || SUBSTR(prmIdentificaAPP,3,2) || '/' || SUBSTR(prmIdentificaAPP,5,4) || ' ' || SUBSTR(prmIdentificaAPP,9,2) || ':' || SUBSTR(prmIdentificaAPP,11,2) || ':' || SUBSTR(prmIdentificaAPP,13,2), 'DD/MM/YYYY HH24:MI:SS');

END IF;


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

IF (vHora < 8 OR vHora > 18) AND prmCDGPE NOT IN ('SORA','AMGM','GASC')THEN
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
  AND TIPO = vTipo;
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


         IF prmTIPOMOV = 'I' THEN
              vCount:= 0;
              vTipo:= 'P';
END IF;


          IF prmIdentificaAPP IS NULL THEN

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
                            TIPO,
                            FIDENTIFICAPP)
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
                        vTipo,
                        vFIdentificApp);

ELSE

SELECT COUNT(*) INTO vCount2
FROM CORTECAJA_PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA;

vCount2 := vCount2 + 1;

INSERT INTO ESIACOM.CORTECAJA_PAGOSDIA
(CORTECAJA_PAGOSDIA_PK, CDGEM, FECHA, CDGNS, NOMBRE, CICLO, CDGOCPE, EJECUTIVO, SECUENCIA, FREGISTRO, CDGPE, ESTATUS, FACTUALIZA, MONTO, TIPO, FIDENTIFICAPP, ESTATUS_CAJA, SECUENCIA2, INCIDENCIA, NUEVO_MONTO, COMENTARIO_INCIDENCIA)
VALUES(id_cortecaja_pagosdia.nextval, prmCDGEM, prmFECHA, prmCDGNS, prmNOMBRE, prmCICLO, prmCDGOCPE, prmEJECUTIVO, vCount, SYSDATE, prmCDGPE, 'A', SYSDATE, prmMONTO, vTipo, vFIdentificApp, '0', vCount2, '0', NULL, NULL);


END IF;

COMMIT;
END IF;

     IF prmTIPO = 2 THEN


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


              IF prmIdentificaAPP IS NULL THEN

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

ELSE

SELECT COUNT(*) INTO vCount2
FROM CORTECAJA_PAGOSDIA
WHERE CDGEM = prmCDGEM
  AND FECHA = prmFECHA;

vCount2 := vCount2 + 1;

INSERT INTO ESIACOM.CORTECAJA_PAGOSDIA
(CORTECAJA_PAGOSDIA_PK, CDGEM, FECHA, CDGNS, NOMBRE, CICLO, CDGOCPE, EJECUTIVO, SECUENCIA, FREGISTRO, CDGPE, ESTATUS, FACTUALIZA, MONTO, TIPO, FIDENTIFICAPP, ESTATUS_CAJA, SECUENCIA2, INCIDENCIA, NUEVO_MONTO, COMENTARIO_INCIDENCIA)
VALUES(id_cortecaja_pagosdia.nextval, prmCDGEM, prmFECHA, prmCDGNS, prmNOMBRE, prmCICLO, prmCDGOCPE, prmEJECUTIVO, vCount, SYSDATE, prmCDGPE, 'A', SYSDATE, prmMONTO, vTipo, vFIdentificApp, '0', vCount2, '0', NULL, NULL);


END IF;


COMMIT;

END IF;

END IF;

     IF prmTIPO = 3 THEN


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
