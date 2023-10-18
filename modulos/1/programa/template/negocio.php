<?php
class [[programa]]Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new [[programa]]Model();
        $this->Modelbd_ipanu = new [[programa]]Model("sql_bdipanu");
        $this->ModelUbk = new [[programa]]Model("sql_ubkipanu");
        $this->ModelIpanu = new [[programa]]Model("sql_ipanuvd");
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}