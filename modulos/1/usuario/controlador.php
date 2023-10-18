<?php
$plantilla="default/default.html";

class usuarioController extends mainController{
	function traerUsuario($parametros){
		$usuario_=$parametros['usuario'];
		$modelo= new usuario($usuario_);
		$this->response($modelo);
	}
	function grabarUsuario($parametros){

		$frm = json_decode($_POST['frm']);
		
		$modelo= new usuario(trim(strtolower($frm->usuario)));
		$modelo->nombres=ucwords(strtolower($frm->nombres));
		$modelo->apellidos=ucwords(strtolower($frm->apellidos));
		$modelo->correo=strtolower($frm->correo);
		$modelo->estado=strtoupper($frm->estado);
		$modelo->begin_work();
		$modelo->grabar();

		$respuesta["mensaje"]='exitoso';		

		$ruta = IMG_USURIOS_PATH;

		$nombreFinal = trim(strtolower($frm->usuario));
		$archivo = $_FILES[0];
		$templocation = $_FILES[0]['tmp_name'];
		$name = $_FILES[0]['name'];
		$ext = explode(".",$name);
		$nombreFinal = $nombreFinal.'.'.$ext[count($ext)-1];

		if (!empty($_FILES)) {			
			if (file_exists($ruta.$nombreFinal))
			unlink($ruta.$nombreFinal);		
			
			move_uploaded_file($templocation, $ruta."/".$nombreFinal);
		}
		
		if($frm->password1!=''){		
				$modelo->password=sha1($frm->password1);
				$modelo->setPassword();
		}

		$modelo->commit();
		$this->response($respuesta);	
	}

	function traerLista($parametros){
		$estado=$parametros['estado'];
		$modelo= new usuario();
		$modelo->estado=$estado;
		$lista=$modelo->getListado();
		$ruta = IMG_USURIOS_PATH;

		foreach ($lista as  $value) {	
			
		if (file_exists($ruta.$value->usuario.".png"))
			$value->imagen = $value->usuario.".png";
		else
			$value->imagen = "default.png";				
		}
		
		$this->response($lista);	
	}
	function cambioContrasena($parametros){
		$actual=$parametros['contrasena-actual'];
		$nueva=$parametros['contrasena-nueva'];
		$nueva2=$parametros['contrasena-nueva2'];

		$modelo= new usuario($_SESSION['usuario']);
		if($nueva==$nueva2 && $nueva!="" && $modelo->password==$actual){
			$modelo->password=$nueva;
			$modelo->setPassword();	
			$respuesta["mensaje"]='exitoso';
		}else{
			$respuesta["mensaje"]="Contrase&ntilde;as no coinciden, verifique..";
		}
		$this->response($respuesta);	

	}
}	

