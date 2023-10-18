<?php
class clientesBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new clientesModel();
    }
    public function traerClientes(){
		return $this->Model->getTraerClientes();
	}
}