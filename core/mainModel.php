<?php

abstract class mainModel
{
	private $DB;
	public $conexion=array(
		"motor"=>"",
		"host"=>"",
		"db"=>"",
		"usuario"=>"",
		"password"=>"",
		"puerto"=>"",
		"server"=>"",
		"status"=>"off",
		"default"=>false
	);
	public $exceptionMode ="die";

	function __construct(){
		
	}
	function Conectarse($conexion_=""){
		$this->conexion=(object) $this->conexion;
		#Conexion personalizada desde archivo de configuracion .ini
		if($conexion_!=''){
			//si viene por local se conecta a la base de datos de pruebas
           $url_servidor = $_SERVER['SERVER_NAME'];
		   if($url_servidor == 'localhost'){
				$conexion_ = $conexion_."test";
		    }
			$file=CORE."/config/".trim($conexion_).".ini";
			if(file_exists($file)){
				$config=parse_ini_file($file,true);
				$config=$config['database'];
				$this->conexion->motor=$config['motor'];
				$this->conexion->host=$config['servidor'];
				$this->conexion->db=$config['base'];
				$this->conexion->usuario=$config['usuario'];
				$this->conexion->password=$config['clave'];
				$this->conexion->puerto=$config['puerto'];
				$this->conexion->server=$config['server'];
			}

		#Conexion por defecto segun .ini que se determina por el dominio	
		}else if($this->conexion->host==''){
			$this->conexion->default=true;
			$config=$_SESSION['config']['database'];
			$this->conexion->motor=$config['motor'];
			$this->conexion->host=$config['servidor'];
			$this->conexion->db=$config['base'];
			$this->conexion->usuario=$config['usuario'];
			$this->conexion->password=$config['clave'];
			$this->conexion->puerto=$config['puerto'];
			$this->conexion->server=$config['server'];
		}

		if(!isset($GLOBALS['DB']) || $this->conexion->default==false){
			#realiza conexion a la base de datos correspondiente
			try{
			
				$dbHandle = new PDO("mysql:host={$this->conexion->host}; dbname={$this->conexion->db};charset=utf8", $this->conexion->usuario, $this->conexion->password);
				$dbHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$dbHandle->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
				$this->DB= $dbHandle;
				$this->conexion->status='on';
			}catch( PDOException $exception ){
				$this->error_PDO($exception->getMessage(),false);
			}
			if($this->conexion->default){
				$GLOBALS['DB']=$this->DB;
			}
		}else{
			if($this->conexion->default){
				$this->DB=$GLOBALS['DB'];
			}
		}
	}

	function lee_todo($query){
		$rows=array();
		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		if (!isset($base)){
			$base='';
		}

		if (!isset($_SESSION['usuario'])){
			$_SESSION['usuario']='';
		}

		if (!isset($_SESSION['datos_adicionales'])){
			$_SESSION['datos_adicionales']='';
		}

		if (!isset($_SESSION['nombreusu'])){
			$_SESSION['nombreusu']='';
		}

		$bk_query=$query;
        $encontrado = "no";
        $arr_fechas=array();
        $arr_bool = array();
		$query = "-- $base".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$query;
		try{ 
			$statement = $this->DB->query($query);
            $colcount = $statement->columnCount();
            $encontrado = "no";
            $arr_bool = Array(); $arr_fechas = Array();
			$this->conexion=(object) $this->conexion;

            if(in_array($this->conexion->motor, array("mysql","sqlsrv"))){
                for ($i=1; $i <= $colcount; $i++) {
                    $meta = $statement->getColumnMeta(($i-1));
                    if($meta['native_type'] == "DATE"){
                        $encontrado = "si";
                        $arr_fechas[] = $meta['name'];
                    } else if($meta['native_type'] == "BOOLEAN"){
                        $encontrado = "si";
                        $arr_bool[] = $meta['name'];
                    }
                    if($meta['name']==''){
                        $encontrado = "si";
                    }
                }
            }
            $rows = $statement->fetchAll(PDO::FETCH_CLASS);

		}catch( PDOException $exception ){
			$this->error_PDO($exception,$query);
		}


		# Si encuentra campos del FIX los recorre para realizar la correccion
		if($encontrado == "si"){
		    $cantRows =count($rows);
			for ($i=0; $i < $cantRows; $i++) {
				// para corregir las fechas
				if(count($arr_fechas) > 0){
					$count_fechas=count($arr_fechas);
					for ($j=0; $j < $count_fechas; $j++) { 
						$registros='';
						@preg_match ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $rows[$i]->$arr_fechas[$j], $registros);
						if($registros!=''){//fix para campos tipo fecha nulos
							$rows[$i]->$arr_fechas[$j] = $registros[2]."/".$registros[3]."/".$registros[1];
						}
					}
				}
				// para corregir los booleanos
				if(count($arr_bool) > 0){
					$count_arr_bool=count($arr_bool);
					for ($j=0; $j < $count_arr_bool; $j++) { 
						$rows[$i]->$arr_bool[$j] = $rows[$i]->$arr_bool[$j]==0?"f":"t"; 
					}
				}
			}
		}
		
		return $rows;
	}
	function ejecuta_query($queri,$retorna='count'){
		
		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		if (!isset($base)){
			$base='';
		}

		if (!isset($_SESSION['usuario'])){
			$_SESSION['usuario']='';
		}

		if (!isset($_SESSION['datos_adicionales'])){
			$_SESSION['datos_adicionales']='';
		}

		if (!isset($_SESSION['nombreusu'])){
			$_SESSION['nombreusu']='';
		}

		$bk_query=$queri;
		$queri = "-- $base".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$queri;
		try{
			$cant=$this->DB->exec($queri);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$queri);
		}

		if (!isset($_SESSION['debug_ejecuta_query'])){
			$_SESSION['debug_ejecuta_query']='';
		}

		if($_SESSION['debug_ejecuta_query'] == "1"){
			$_SESSION['contenido_debug'] .= "<div class='debug_ejecuta_query'><br>$bk_query<br><b><i>-&gt; ".$cant." registros afectados</i></b></div>";
		}
		if($retorna=='count') return $cant;
		else return $this->DB->lastInsertId();
	}
	function begin_work(){
		$this->DB->beginTransaction();
	}
	function commit(){
		$this->DB->commit();
	}
	function rollback(){
		$this->DB->rollback();
	}
	function ejecuta_sp($queri){

		if (isset($_REQUEST['modulo'])){
			$modulo_=$_REQUEST['modulo'];
		}else{
			$modulo_='';
		}

		if (isset($_REQUEST['metodo'])){
			$metodo_=$_REQUEST['metodo'];
		}else{
			$metodo_='';
		}

		$queri = "-- ".trim($_SESSION['usuario']).$_SESSION['datos_adicionales']."=>".trim($_SESSION['nombreusu'])." (".$_SERVER['REMOTE_ADDR'].") [".$_SERVER['SCRIPT_NAME']." modulo:$modulo_ metodo:$metodo_] ".date("h:i:s a")."
		".$queri;
		try{
			$statement = $this->DB->query($queri);
		}catch( PDOException $exception ){
			$this->error_PDO($exception,$queri);
		}
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		for ($i=0; $i <count($rows) ; $i++) { 
			$result[]=trim($rows[$i]['']);
		} 
		if(count($rows)==1) $result=$result[0];
		return $result;
	}

	function lee_uno($query){
		$mat=$this->lee_todo($query);
		if(!empty($mat)){
			return $mat['0'];
		}else{
			return $mat;
		}
	}

	function isolation($tipo){
		$this->conexion=(object) $this->conexion;
		switch ($this->conexion->motor) {
			case 'sqlsrv':
				switch (strtolower($tipo)) {
					case 'repeatable':
					    $sql_add="REPEATABLE READ";
					    break;
					case 'committed':
					    $sql_add="READ COMMITTED";
					    break;
					case 'uncommitted':
						$sql_add="READ UNCOMMITTED";
						break;	
                    default:
                        $sql_add='';
                        break;
				}
				$this->ejecuta_query("SET TRANSACTION ISOLATION LEVEL $sql_add");
				break;
			default:
				# code...
				break;
		}
		
	}
	function wait(){
		$this->conexion=(object) $this->conexion;
		switch ($this->conexion->motor) {
			case 'sqlsrv':
				$this->ejecuta_query("WAITFOR DELAY '00:00:02'");
				break;
			default:
				# code...
				break;
		}
	}

	/*Funcion para cargar los datos de un array  a atributos del modelo*/
	function atributos($datos,$destino){
		foreach ((array)$datos as $key => $value) {
			$this->$destino->$key=trim($value);
		}
	}
	function error_PDO($exception,$query){
		switch ($this->exceptionMode) {
			case 'throw':
				throw $exception;
				break;
			case 'die':
				echo "<pre>";
				print_r($exception);
				print_r($query);
				die("");
				break;
		}	
	}


	function core_log_programa($modulo){

		$usuario = $_SESSION['usuario'];
	
	   $consulta = "SELECT count(distinct programa)conteo FROM logs_programas WHERE programa ='$modulo' AND  usuario = '$usuario' ";
		$respuesta = $this->lee_uno($consulta);
			
	   	if ($respuesta->conteo == 0) {
			$insert = "INSERT INTO logs_programas(usuario,programa,f_ingreso,nro_ingresos,tipo_programa) VALUES('$usuario','$modulo',NOW(),1,'new_core')";
			$this->ejecuta_query($insert);
	    }else{
	        $update = "UPDATE logs_programas SET nro_ingresos = nro_ingresos+1, f_ingreso = NOW() WHERE usuario ='$usuario' and programa ='$modulo'";
	       $this->ejecuta_query($update);
	
	 	}
		
	}
}