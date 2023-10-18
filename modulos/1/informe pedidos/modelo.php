<?php
class informe pedidosModel extends mainModel {
	public $atributo1="Valor por defecto ejemplo";  
	public $atributo2;
	                                                                    
	public function __construct($parametro=""){  
		$this->atributo2 = strtoupper($parametro);
		$this->Conectarse($parametro);
	}

	public function GetUsuarios(){
		$consulta = "SELECT usuario,nombre,apellidos,correo from core_usuarios";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}
}  
