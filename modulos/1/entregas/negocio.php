<?php
class entregasBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new entregasModel();
    }
    public function traerTransportadores($nit){

        $where = " where nit = '$nit' ";
        $transportadoras = $this->Model->getTransportadores($where);
        if(empty($transportadoras)){
            $where = "";
            $transportadoras = $this->Model->getTransportadores($where);

        }
		return  $transportadoras;
	}

    public function traerDataTransportadores($tipo, $estado){

        if($estado == 'TODOS'){
            $estado = "";
        }else{
            $estado = " and e.estado_entrega = '$estado' ";
        }

		return   $this->Model->getTraerdata($tipo, $estado);
	}

    public function traerInfoData($id){

        return   $this->Model->getTraerInfodata($id);

    }

    public function actualizardatos($id_entrega,$estado,$fecha, $observaciones,$ruta){

        return  $this->Model->getActualizadatos($id_entrega,$estado,$fecha, $observaciones,$ruta);

    }
}