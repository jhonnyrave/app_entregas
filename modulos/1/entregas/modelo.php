<?php
class entregasModel extends mainModel {
                                                            
	public function __construct($parametro=""){  

		$this->Conectarse($parametro);
	}

	public function getTransportadores($where){
		$consulta = "SELECT * from transportadoras $where";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}

	public function getTraerdata($tipo, $estado){
		$consulta = "SELECT * from entregas e
		inner join transportadoras t on(e.id_transportadora = t.id_transportadora)
		inner join pedidos p on(e.id_pedido = p.id_pedido)
		inner join clientes c on(e.id_cliente= c.id_cliente)
		inner join guias_de_transporte g on(e.numero_guia_transporte= g.id_guia_transporte)
		where t.id_transportadora = '$tipo' $estado ";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}

	public function getTraerInfodata($id){
		$consulta = "SELECT * from entregas e
		inner join transportadoras t on(e.id_transportadora = t.id_transportadora)
		inner join pedidos p on(e.id_pedido = p.id_pedido)
		inner join clientes c on(e.id_cliente= c.id_cliente)
		inner join guias_de_transporte g on(e.numero_guia_transporte= g.id_guia_transporte)
		where e.id_entrega = '$id'";
		$respuesta = $this->lee_todo($consulta);		
		return $respuesta;
	}

	public function getActualizadatos($id_entrega,$estado,$fecha, $observaciones,$ruta){
		$this->ejecuta_query("UPDATE entregas set estado_entrega='$estado', 
		fecha_entrega='$fecha', observaciones = '$observaciones' , prueba_entrega = '$ruta' where id_entrega='$id_entrega'");

	}

	

	
}  