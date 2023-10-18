<?php
$plantilla="default/default.html";
class entregasController extends mainController{
private $Business;
public $extensions_img = array(
    "jpg",
    "jpeg",
    "WEBP",
    "webp",
    "png",
    "PNG",
    "JPG",
    "JPEG",
    "pdf"
);


public function __construct () {
$this->Business = new entregasBusiness();
}

    function traerTransportadora($parametros){

        try{
            /** @var int $resultado ejecuta el Metodo de la capa de negocio*/
            $nit = $parametros['nit'];
            $resultado = $this->Business->traerTransportadores($nit);
            /** @var object $this retorna la informacion al servicio REST o controlador JS*/
            $this->response($resultado);
        }catch (Exception $e){
            $this->response($this->logError($e));
        }
    }

    function traerDataTransportadores($parametros){

        try{
            /** @var int $resultado ejecuta el Metodo de la capa de negocio*/
            $tipo = $parametros['tipo'];
            $estado = $parametros['estado'];
            $resultado = $this->Business->traerDataTransportadores($tipo, $estado);
            /** @var object $this retorna la informacion al servicio REST o controlador JS*/
            $this->response($resultado);
        }catch (Exception $e){
            $this->response($this->logError($e));
        }
    }

    function traerInfoData($parametros){

        try{
            /** @var int $resultado ejecuta el Metodo de la capa de negocio*/
            $id = $parametros['id_entrega'];
          
            $resultado = $this->Business->traerInfoData($id);
            /** @var object $this retorna la informacion al servicio REST o controlador JS*/
            $this->response($resultado);
        }catch (Exception $e){
            $this->response($this->logError($e));
        }
    }

    public function actualizardatos(){

		try{

			$id_entrega = $_REQUEST['id_entrega'];
			$estado = $_REQUEST['estado'];
			$fecha = $_REQUEST['fecha'];
            $observaciones = $_REQUEST['observaciones'];
            $ruta = IMG_GUIAS_PATH;

            $nombreFinal = trim(strtolower($id_entrega . "_Guia"));
            $templocation = $_FILES['img_entrega']['tmp_name'];
            $name =  $_FILES['img_entrega']['name'];
            $ext = explode(".",$name);
            $nombreFinal = $nombreFinal.'.'.$ext[count($ext)-1];

            if (!empty($_FILES)) {			
                if (file_exists($ruta.$nombreFinal))
                unlink($ruta.$nombreFinal);		
                
                move_uploaded_file($templocation, $ruta."/".$nombreFinal);
            }

		    $msj = $this->Business->actualizardatos($id_entrega,$estado,$fecha, $observaciones,$nombreFinal);
            $respuesta["mensaje"]='exitoso';
            $this->response($respuesta);

        }catch (Exception $e){
            //$this->response($this->logError($e));
        }	

	}


    
}