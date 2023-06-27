<?php
namespace Core;
defined("APPPATH") OR die("Access denied");

use \Core\App;
use \PDO;

/**
 * @class Conn
 */

Class Database{

const MAIL = "cesar.cor.riv@gmail.com" /*"tecnico@webmaster.com"*/;
const TEMA = 'ecommerce';
static $_instance;
static $_mysqli;

static $_debug;
static $_mail;

    private function __construct(){
        $this->conectar();
    }

    private function __clone(){ }

    public static function getInstance($debug = true, $mail = false){

	self::$_debug = $debug;
	self::$_mail = $mail;

        if (!(self::$_instance instanceof self)){
            self::$_instance=new self();
        }
        return self::$_instance;
    }

    private function conectar(){

	//load from config/config.ini
        $dsn = 'oci:dbname=ESIACOM';

        //OR connect using the Oracle Instant Client
        $dsn = 'oci:dbname=//25.67.211.74:1521/ESIACOM';

        $username = 'ESIACOM';
        $password = 'ESIACOM';
        $dbh = null;


        try {

            $this->_mysqli =  new PDO($dsn, $username, $password);
	    //$this->_mysqli->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }catch(\PDOException $e){
	    if(self::$_debug)
                echo $e->getMessage();
	    if(self::$_mail)
                mail(self::MAIL,'error en conexion '.self::TEMA,$e->getMessage());

	    die();
        }
    }
    public function insert($sql,$params = ''){

        if($params == '' ){
            try{
		$this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->exec($sql);
                $res = $this->_mysqli->lastInsertId();
                $this->_mysqli->commit();
                return $res;
            }catch(\PDOException $e){
		$this->_mysqli->rollback();
		if(self::$_mail)
                    mail(self::MAIL,'error en insert '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo $e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);

                return false;
            }
        }else{
            try{
		$this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->prepare($sql);
                $stmt->execute($params);
                $res = $this->_mysqli->lastInsertId();
                $this->_mysqli->commit();
                return $res;
            }catch(\PDOException $e){
		$this->_mysqli->rollback();
		if(self::$_mail)
                    mail(self::MAIL,'error en insert '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }
    }
    public function update($sql,$params = ''){

        if($params == ''){
            try{
                $this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->exec($sql);
                $this->_mysqli->commit();
                return $stmt;
            }catch(\PDOException $e){
                $this->_mysqli->rollback();
                if(self::$_mail)
                    mail(self::MAIL,'error en update '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
                if(self::$_debug)
                    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                    return false;
            }
        }else{
            try{
                $this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->prepare($sql);
                $stmt->execute($params);
                $this->_mysqli->commit();
                return $stmt->rowCount();
            }catch(\PDOException $e){
                $this->_mysqli->rollback();
                if(self::$_mail)
                    mail(self::MAIL,'error en update '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
                if(self::$_debug)
                    //echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }
    }
    public function delete($sql,$params = ''){

        if($params == ''){
            try{
                $this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->exec($sql);
                $this->_mysqli->commit();
                return $stmt;
            }catch(\PDOException $e){
		$this->_mysqli->rollback();
		if(self::$_mail)
                    mail(self::MAIL,'error en delete '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }else{
            try{
                $this->_mysqli->beginTransaction();
                $stmt = $this->_mysqli->prepare($sql);
                $stmt->execute($params);
                $this->_mysqli->commit();
                return $stmt->rowCount();
            }catch(\PDOException $e){
		$this->_mysqli->rollback();
		if(self::$_mail)
                    mail(self::MAIL,'error en delete '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }
    }
    public function queryOne($sql,$params = ''){

        if($params == ''){
            try{
                $stmt = $this->_mysqli->query($sql);
                return array_shift($stmt->fetchAll(PDO::FETCH_ASSOC));
            }catch(\PDOException $e){
		if(self::$_mail)
                    mail(self::MAIL,'error en queryOne '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }else{
            try{
                $stmt = $this->_mysqli->prepare($sql);
                foreach($params AS $values=>$val)
                    $stmt->bindParam($values,$val);
                $stmt->execute($params);
                return array_shift($stmt->fetchAll(PDO::FETCH_ASSOC));
            }catch(\PDOException $e){
		if(self::$_mail)
                    mail(self::MAIL,'error en queryOne '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }
    }
    public function queryAll($sql,$params = ''){

        if($params == ''){
            try{
                $stmt = $this->_mysqli->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch(\PDOException $e){
		if(self::$_mail)
                    mail(self::MAIL,'error en queryAll '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }else{
            try{
                $stmt = $this->_mysqli->prepare($sql);
                foreach($params AS $values=>$val)
                    $stmt->bindParam($values,$val);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch(\PDOException $e){
		if(self::$_mail)
                    mail(self::MAIL,'error en queryAll '.self::TEMA,"Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1));
		if(self::$_debug)
		    echo "Error sql : ".$e->getMessage()."\nSql : $sql \n params :\n".print_r($params,1);
                return false;
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////7

    public function queryProcedurePago($credito, $ciclo_, $monto_, $tipo_, $nombre_, $user_, $ejecutivo_id,  $ejec_nom_){

        $fechaActual = date('d-m-Y');

        $empresa = "EMPFIN";
        $fecha = $fechaActual;
        $fecha_aux = "";
        $cdgns = $credito;
        $ciclo = $ciclo_;
        $secuencia = "";
        $nombre = $nombre_;
        $cdgocpe = $ejecutivo_id;
        $ejecutivo = $ejec_nom_;
        $cdgpe = $user_;
        $monto = $monto_;
        $tipo_mov = $tipo_;
        $tipo = 1;
        $resultado = "";
        $identifica_app = "";

        $query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$fecha, PDO::PARAM_STR);
        $stmt->bindParam(3,$fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4,$cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5,$ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6,$secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7,$nombre, PDO::PARAM_STR);
        $stmt->bindParam(8,$cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9,$ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10,$cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11,$monto, PDO::PARAM_STR);
        $stmt->bindParam(12,$tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13,$tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);
        $stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }

    }
    public function queryProcedureDeletePago($cdgns_, $fecha_, $user_, $secuencia_){

        $empresa = "EMPFIN";
        $fecha = $fecha_;
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

        $query_text = "CALL SPACCIONPAGODIA(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$fecha, PDO::PARAM_STR);
        $stmt->bindParam(3,$fecha_aux, PDO::PARAM_STR);
        $stmt->bindParam(4,$cdgns, PDO::PARAM_STR);
        $stmt->bindParam(5,$ciclo, PDO::PARAM_STR);
        $stmt->bindParam(6,$secuencia, PDO::PARAM_STR);
        $stmt->bindParam(7,$nombre, PDO::PARAM_STR);
        $stmt->bindParam(8,$cdgocpe, PDO::PARAM_STR);
        $stmt->bindParam(9,$ejecutivo, PDO::PARAM_STR);
        $stmt->bindParam(10,$cdgpe, PDO::PARAM_STR);
        $stmt->bindParam(11,$monto, PDO::PARAM_STR);
        $stmt->bindParam(12,$tipo_mov, PDO::PARAM_STR);
        $stmt->bindParam(13,$tipo, PDO::PARAM_INT, 10);
        $stmt->bindParam(14,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);
        $stmt->bindParam(15,$identifica_app, PDO::PARAM_STR);


        $result = $stmt->execute();

        if ($result) {
            echo $resultado;
        } else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);
        }

    }

    public function queryProcedureActualizaSucursal($n_credito_p, $ciclo_p, $nueva_suc_p){

        $empresa = "EMPFIN";
        $no_credito = $n_credito_p;
        $ciclo = $ciclo_p;
        $nuevaSucursal = $nueva_suc_p;
        $resultado = "";

        $query_text = "CALL SPACTUALIZASUC(?, ?, ?, ?, ?)";
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3,$ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4,$nuevaSucursal, PDO::PARAM_STR);
        $stmt->bindParam(5,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);

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

    public function queryProcedureInsertGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p){

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
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3,$ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4,$articulo, PDO::PARAM_STR);
        $stmt->bindParam(5,$marca, PDO::PARAM_STR);
        $stmt->bindParam(6,$modelo, PDO::PARAM_STR);
        $stmt->bindParam(7,$serie, PDO::PARAM_STR);
        $stmt->bindParam(8,$valor, PDO::PARAM_STR);
        $stmt->bindParam(9,$factura, PDO::PARAM_STR);
        $stmt->bindParam(10,$usuario, PDO::PARAM_STR);
        $stmt->bindParam(11,$tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);

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
    public function queryProcedureDeleteGarantias($n_credito_p, $secuencia, $tipo_transaccion){

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
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3,$ciclo, PDO::PARAM_STR);
        $stmt->bindParam(4,$articulo, PDO::PARAM_STR);
        $stmt->bindParam(5,$marca, PDO::PARAM_STR);
        $stmt->bindParam(6,$modelo, PDO::PARAM_STR);
        $stmt->bindParam(7,$serie, PDO::PARAM_STR);
        $stmt->bindParam(8,$valor, PDO::PARAM_STR);
        $stmt->bindParam(9,$factura, PDO::PARAM_STR);
        $stmt->bindParam(10,$usuario, PDO::PARAM_STR);
        $stmt->bindParam(11,$tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);

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
    public function queryProcedureUpdatesGarantias($n_credito_p, $articulo_p, $marca_p, $modelo_p, $serie_p, $factura_p, $usuario_p, $valor_p, $secuencia_p){


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
        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$no_credito, PDO::PARAM_STR);
        $stmt->bindParam(3,$secuencia, PDO::PARAM_STR);
        $stmt->bindParam(4,$articulo, PDO::PARAM_STR);
        $stmt->bindParam(5,$marca, PDO::PARAM_STR);
        $stmt->bindParam(6,$modelo, PDO::PARAM_STR);
        $stmt->bindParam(7,$serie, PDO::PARAM_STR);
        $stmt->bindParam(8,$valor, PDO::PARAM_STR);
        $stmt->bindParam(9,$factura, PDO::PARAM_STR);
        $stmt->bindParam(10,$usuario, PDO::PARAM_STR);
        $stmt->bindParam(11,$tipo_transaccion, PDO::PARAM_STR);
        $stmt->bindParam(12,$resultado, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 100);

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
    public function queryProcedureActualizaNumCredito($credito_a, $credito_n){

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $credito_nuevo = $credito_n;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZACODIGOGPO(?,?,?,?)";

        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3,$credito_nuevo, PDO::PARAM_STR);
        $stmt->bindParam(4,$resultado_s, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 300);

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
    public function queryProcedureActualizaNumCreditoCiclo($credito_a, $ciclo_n){

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZACICLOGPO(?,?,?,?)";

        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3,$ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4,$resultado_s, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
           echo $resultado_s;
        }
        else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);

        }

    }

    public function queryProcedureActualizaNumCreditoSituacion($credito_a, $ciclo_n, $situacion){

        $empresa = "EMPFIN";
        $credito_actual = $credito_a;
        $ciclo_n = $ciclo_n;
        $situacion_n = $situacion;
        $resultado_s = "";


        $query_text = "CALL SPACTUALIZASITUACION(?,?,?,?,?)";

        $stmt = $this->_mysqli->prepare($query_text);
        $stmt->bindParam(1,$empresa, PDO::PARAM_STR);
        $stmt->bindParam(2,$credito_actual, PDO::PARAM_STR);
        $stmt->bindParam(3,$ciclo_n, PDO::PARAM_STR);
        $stmt->bindParam(4,$situacion_n, PDO::PARAM_STR);
        $stmt->bindParam(5,$resultado_s, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 300);

        $result = $stmt->execute();

        if ($result) {
            echo $resultado_s;
        }
        else {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $stmt->errorInfo();
            print_r($arr);

        }

    }

}
