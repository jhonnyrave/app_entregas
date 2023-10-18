<?php
class clientesModel extends mainModel {                                                                   
	public function __construct($parametro=""){  
		$this->Conectarse($parametro);
	}

	public function getTraerClientes(){
		$consulta = "SELECT * from clientes";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}
}  