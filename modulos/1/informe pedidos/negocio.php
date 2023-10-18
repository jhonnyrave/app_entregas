<?php
class informe pedidosBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new informe pedidosModel();
        $this->Modelbd_ipanu = new informe pedidosModel("sql_bdipanu");
        $this->ModelUbk = new informe pedidosModel("sql_ubkipanu");
        $this->ModelIpanu = new informe pedidosModel("sql_ipanuvd");
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}