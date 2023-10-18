<?php
class transportadoresBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new transportadoresModel();
    }
    public function traerTransportadores(){
		return $this->Model->getTransportadores();
	}
}