<?php

namespace Core;

use PDO;

/**
 * @class Conn
 */

class Database
{
    private $db_mcm;
    private $db_cultiva;
    public $db_activa;

    function __construct()
    {
        $this->DB_CULTIVA();
        $this->DB_MCM();

        // La base por defecto seria MCM
        $this->db_activa = $this->db_mcm;

        // La base por defecto seria CULTIVA
        // $this->db_activa = $this->db_cultiva;
    }

    private function Conecta($s, $u = null, $p = null)
    {
        $host = 'oci:dbname=//' . $s . ':1521/ESIACOM;charset=UTF8';
        $usuario = $u ?? 'ESIACOM';
        $password = $p ?? 'ESIACOM';
        try {
            return new PDO($host, $usuario, $password);
        } catch (\PDOException $e) {
            echo self::muestraError($e);
            return null;
        }
    }

    private function muestraError($e, $sql = null, $parametros = null)
    {
        $error = "Error en DB: " . $e->getMessage();

        if ($sql != null) $error .= "\nSql: " . $sql;
        if ($parametros != null) $error .= "\nDatos: " . print_r($parametros, 1);
        echo $error . "\n";
        return $error;
    }

    private function DB_MCM()
    {
        $servidor = 'DRP';
        // $servidor = 'mcm-server';
        $this->db_mcm = self::Conecta($servidor);
    }

    private function DB_CULTIVA()
    {
        $servidor = '25.95.21.168';
        $this->db_cultiva = self::Conecta($servidor);
    }

    public function SetDB_MCM()
    {
        $this->db_activa = $this->db_mcm;
    }

    public function SetDB_CULTIVA()
    {
        $this->db_activa = $this->db_cultiva;
    }

    public function insert($sql)
    {
        $stmt = $this->db_activa->prepare($sql);
        $result = $stmt->execute();

        if ($result) {
            echo '1';
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function insertar($sql, $datos)
    {
        try {
            if (!$this->db_activa->prepare($sql)->execute($datos)) {
                throw new \Exception("Error en insertar: " . print_r($this->db_activa->errorInfo(), 1) . "\nSql : $sql \nDatos : " . print_r($datos, 1));
            }
        } catch (\PDOException $e) {
            throw new \Exception("Error en insertar: " . $e->getMessage() . "\nSql : $sql \nDatos : " . print_r($datos, 1));
        }
    }

    public function insertCheques($sql, $parametros)
    {
        $stmt = $this->db_activa->prepare($sql);
        $result = $stmt->execute($parametros);

        if ($result) return $result;

        $arr = $stmt->errorInfo();
        return "PDOStatement::errorInfo():\n" . json_encode($arr);
    }

    public function insertaMultiple($sql, $registros, $validacion = null)
    {
        try {
            $this->db_activa->beginTransaction();
            foreach ($registros as $i => $valores) {
                $stmt = $this->db_activa->prepare($sql[$i]);
                $result = $stmt->execute($valores);
                if (!$result) {
                    $err = $stmt->errorInfo();
                    $this->db_activa->rollBack();
                    throw new \Exception("Error: " . print_r($err, 1) . "\nSql : " . $sql[$i] . "\nDatos : " . print_r($valores, 1));
                }
            }

            if ($validacion != null) {
                $stmt = $this->db_activa->prepare($validacion['query']);
                $stmt->execute($validacion['datos']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $resValidacion = $validacion['funcion']($result);
                if ($resValidacion['success'] == false) {
                    $this->db_activa->rollBack();
                    throw new \Exception($resValidacion['mensaje']);
                }
            }

            $this->db_activa->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db_activa->rollBack();
            throw new \Exception("Error en insertaMultiple: " . $e->getMessage());
        }
    }

    public function EjecutaSP($sp, $parametros)
    {
        try {
            $stmt = $this->db_activa->prepare($sp);
            $outParam = 'OK';
            foreach ($parametros as $parametro => $valor) {
                if ($valor === "__RETURN__") {
                    $stmt->bindParam($parametro, $outParam, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                } else {
                    $stmt->bindParam($parametro, $valor);
                }
            }
            $stmt->execute();
            return $outParam;
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function eliminar($sql)
    {
        try {
            return $this->db_activa->prepare($sql)->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error en eliminar: " . $e->getMessage() . "\nSql : $sql");
        }
    }

    public function queryOne($sql, $params = '')
    {

        if ($params == '') {
            try {
                $stmt = $this->db_activa->query($sql);
                return array_shift($stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        } else {
            try {
                $stmt = $this->db_activa->prepare($sql);
                foreach ($params as $values => $val)
                    $stmt->bindParam($values, $val);
                $stmt->execute($params);
                return array_shift($stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        }
    }

    public function queryAll($sql, $params = '')
    {
        if ($params == '') {
            try {
                $stmt = $this->db_activa->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        } else {
            try {
                $stmt = $this->db_activa->prepare($sql);
                foreach ($params as $values => $val)
                    $stmt->bindParam($values, $val);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                self::muestraError($e, $sql, $params);
                return false;
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////7

    public function queryProcedurePago($credito, $ciclo_, $monto_, $tipo_, $nombre_, $user_, $ejecutivo_id,  $ejec_nom_, $tipo_procedure, $fecha_aux, $secuencia, $fecha)
    {

        $newDate = date("d-m-Y", strtotime($fecha));
        $newDateFechaAux = date("d-m-Y", strtotime($fecha_aux));

        $empresa = "EMPFIN";
        $fecha = $newDate;
        $fecha_aux =  $newDateFechaAux;
        $cdgns = $credito;
        $ciclo = $ciclo_;
        $secuencia = $secuencia;
        $nombre = $nombre_;
        $cdgocpe = $ejecutivo_id;
        $ejecutivo = $ejec_nom_;
        $cdgpe = $user_;
        $monto = $monto_;
        $tipo_mov = $tipo_;
        $tipo = $tipo_procedure;
        $resultado = "";
        $identifica_app = "";

        $query_text = "CALL SPACCIONPAGODIA_PRUEBA(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        ///$query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";/////este es el que funciona bien cuando se actualice la base de datos de produccion
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $fecha, PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4, $cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(8, $cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9, $ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10, $cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11, $monto, PDO::PARAM_STR);
        $stmt->bindParam(12, $tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13, $tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);
        //$stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
    public function queryProcedureDeletePago($cdgns_, $fecha_, $user_, $secuencia_)
    {

        $fecha_parseada = strtotime($fecha_);
        $fecha_parseada = date('d-m-Y', $fecha_parseada);


        $empresa = "EMPFIN";
        $fecha = $fecha_parseada;
        $fecha_aux = '';
        $cdgns = $cdgns_;
        $ciclo = "";
        $secuencia = $secuencia_;
        $nombre = "";
        $cdgocpe = "";
        $ejecutivo = "";
        $cdgpe = $user_;
        $monto = "";
        $tipo_mov = "P";
        $tipo = 3;
        $resultado = "";
        $identifica_app = "";

        $query_text = "CALL SPACCIONPAGODIA_PRUEBA(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        //$query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $fecha, PDO::PARAM_STR);
        $stmt->bindParam(3, $fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4, $cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(8, $cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9, $ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10, $cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11, $monto, PDO::PARAM_STR);
        $stmt->bindParam(12, $tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13, $tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);
        //$stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function queryProcedureActualizaSucursal($n_credito_p, $ciclo_p, $nueva_suc_p)
    {

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = $ciclo_p;
        $nuevaSucursal = $nueva_suc_p;
        $resultado = "";

        $query_text = "CALL SPACTUALIZASUC(?, ?, ?, ?, ?)";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $nuevaSucursal, PDO::PARAM_STR);
        $stmt->bindParam(5, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            return $resultado;
            //var_dump($resultado);

        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function queryProcedureInsertGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p)
    {

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = '10';
        $articulo = $articulo_p;
        $marca = $marca_p;
        $modelo = $modelo_p;
        $serie = $serie_p;
        $factura = $factura_p;
        $usuario = $usuario_p;
        $valor = $valor_p;
        $tipo_transaccion = '1';
        $resultado = "";


        //CALL ESIACOM.SPACCIONGARPREN('EMPFIN','001130','10','Articulo','Marca','Modelo','Serie','2652','Factura','DGNV','1',?)

        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)
";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            return $resultado;
            //var_dump($resultado);

        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
    public function queryProcedureDeleteGarantias($n_credito_p, $secuencia, $tipo_transaccion)
    {

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = $secuencia;
        $articulo = "";
        $marca = "";
        $modelo = "";
        $serie = "";
        $factura = "";
        $usuario = "";
        $valor = "";
        $tipo_transaccion = $tipo_transaccion;
        $resultado = "";


        //CALL ESIACOM.SPACCIONGARPREN('EMPFIN','001130','10','Articulo','Marca','Modelo','Serie','2652','Factura','DGNV','1',?)

        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)
";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            return $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
    public function queryProcedureUpdatesGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p, $secuencia_p)
    {


        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $secuencia = $secuencia_p;
        $articulo = $articulo_p;
        $marca = $marca_p;
        $modelo = $modelo_p;
        $serie = $serie_p;
        $factura = $factura_p;
        $usuario = $usuario_p;
        $valor = $valor_p;
        $tipo_transaccion = '2';
        $resultado = "";


        //CALL ESIACOM.SPACCIONGARPREN('EMPFIN','001130','10','Articulo','Marca','Modelo','Serie','2652','Factura','DGNV','1',?)

        $query_text = "CALL ESIACOM.SPACCIONGARPREN(?,?,?,?,?,?,?,?,?,?,?,?)
";
        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3, $secuencia, PDO::PARAM_STR);
        $stmt->bindParam(4, $articulo, PDO::PARAM_STR);
        $stmt->bindParam(5, $marca, PDO::PARAM_STR);
        $stmt->bindParam(6, $modelo, PDO::PARAM_STR);
        $stmt->bindParam(7, $serie, PDO::PARAM_STR);
        $stmt->bindParam(8, $valor, PDO::PARAM_STR);
        $stmt->bindParam(9, $factura, PDO::PARAM_STR);
        $stmt->bindParam(10, $usuario, PDO::PARAM_STR);
        $stmt->bindParam(11, $tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12, $resultado, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 100);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            //return $resultado;
            var_dump($resultado);
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function queryProcedureActualizaNumCredito($credito_a, $credito_n)
    {

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $credito_nuevo = $credito_n;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZACODIGOGPO(?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $credito_nuevo, PDO::PARAM_STR);
        $stmt->bindParam(4, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
            var_dump($resultado_s);
            //return $resultado_s;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
    public function queryProcedureActualizaNumCreditoCiclo($credito_a, $ciclo_n)
    {

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZACICLOGPO(?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            echo $resultado_s;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }

    public function queryProcedureActualizaNumCreditoSituacion($credito_a, $ciclo_n, $situacion)
    {

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $situacion_n = $situacion;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZASITUACION(?,?,?,?,?)";

        $stmt = $this->db_activa->prepare($query_text);
        $stmt->bindParam(1, $empresa, PDO::PARAM_STR);
        $stmt->bindParam(2, $credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3, $ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4, $situacion_n, PDO::PARAM_STR);
        $stmt->bindParam(5, $resultado_s, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            echo $resultado_s;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }
    }
}
