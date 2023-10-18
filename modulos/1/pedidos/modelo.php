<?php
class pedidosModel extends mainModel {
                                                                 
	public function __construct($parametro=""){  
		$this->Conectarse($parametro);
	}

	public function getPedidos(){
		$consulta = "SELECT * from pedidos";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}
}  