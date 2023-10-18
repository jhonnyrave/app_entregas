<?php
class negocioBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new negocioModel();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}