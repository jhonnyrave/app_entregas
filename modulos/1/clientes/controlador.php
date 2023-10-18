<?php
$plantilla="default/default.html";
class clientesController extends mainController{
private $Business;

    public function __construct () {
        $this->Business = new clientesBusiness();
    }

    function traerClientes(){

        try{
            /** @var int $resultado ejecuta el Metodo de la capa de negocio*/
            $resultado = $this->Business->traerClientes();
            /** @var object $this retorna la informacion al servicio REST o controlador JS*/
            $this->response($resultado);
        }catch (Exception $e){
            $this->response($this->logError($e));
        }
    }
}