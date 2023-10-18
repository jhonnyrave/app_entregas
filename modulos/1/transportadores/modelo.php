<?php
class transportadoresModel extends mainModel {
	                                                                    
	public function __construct($parametro=""){  
		$this->Conectarse($parametro);
	}

	public function getTransportadores(){
		$consulta = "SELECT * from transportadoras";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}
}  