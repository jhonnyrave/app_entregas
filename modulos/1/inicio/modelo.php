<?php
/**
 * Explicacion corta del contenido de archivos y funciones
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin Y-M-D
 * @category     Area
 * @package      Modulo
 * @subpackage   SubModulo
 * @version         Version
 */
class inicio extends mainModel {
	                                                                    
	public function __construct($usuario=""){  
		$this->Conectarse();
	}

	public function GetUsuarios(){
		$consulta = "SELECT usuario,nombre,apellidos,correo from core_usuarios";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}
}  
