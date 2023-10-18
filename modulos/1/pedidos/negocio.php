<?php
class pedidosBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new pedidosModel();
    }
    public function traerPedidos(){
		return $this->Model->getPedidos();
	}
}