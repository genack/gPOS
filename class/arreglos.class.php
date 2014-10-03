<?php



class job  extends Cursor {	
	var $dadoDeAlta;

    function job() {
    }

	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_subsidiariostbjos ", "IdTbjoSubsidiario ", $id);
		return $this->getResult();
	}
	function Crea(){
		$this->setEstado("Pdte Envio");			
	}

	function CreaDesdeServicio($arreglo){
		$this->Crea();		
		$this->set("IdProducto",	$arreglo->idproducto,	FORCE);
		$this->set("NTicket",		$arreglo->nticket,	FORCE);
		$this->set("IdSubsidiario",  	$arreglo->idsubsidiario,	FORCE);
		$this->set("Coste",  	        $arreglo->importe,	FORCE);
		$this->set("CostePendiente",    $arreglo->importe,	FORCE);
		//"NombreProducto" - "Talla" - "Color"
		//$this->set("DescripcionProducto",  	$arreglo->concepto,	FORCE);
		
		if ( $this->Alta()) {
			$arreglo->AltaServicio($this);	
		}		
	}

	function setEstado($modo){
		$this->set("Status",$modo,FORCE);
	}

	function esMio($IdSubsidiario){
		return ($this->get("IdSubsidiario")==$IdSubsidiario);
	}

	function Alta(){
		global $UltimaInsercion;
		$data = $this->export();
		
		$coma = false;
		$listaKeys = "";
		$listaValues = "";
				
		foreach ($data as $key=>$value){
			if ($coma) {
			  $listaKeys .= ", ";
			  $listaValues .= ", ";
			}
			
			$listaKeys .= " $key";
			$listaValues .= " '$value'";
			$coma = true;
		}
	
		$sql = "INSERT INTO ges_subsidiariostbjos ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta trabajo de subsidiario");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->setId($id);
			$this->set("IdTbjoSubsidiario ",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function qModificacionEstado($estado=false,$idestado=false){		
		if (!$estado)	
			$estado = $this->get("Status");
		
		if (!$idestado)
			$idestado = $this->getId();
		else 
			$this->setId($idestado);		
		
		$this->setEstado($estado);
		
	//enum('Pdte Envio', 'Enviado', 'Recibido', 'Entregado')
	//	 FechaEnvio   	date  	   	No   	0000-00-00   	   	  Cambiar   	  Eliminar   	  Primaria   	  Indice   	  Unico   	 Fulltext
	// FechaRecepcion  	date
	 
		switch($estado){
			case "Enviado":
				$extra = ", FechaEnvio = NOW(), FechaRecepcion = '0000-00-00' ";
				break;
			case "Recogido":
			case "Recibido":
				$extra = ", FechaRecepcion = NOW() ";
				$estado = "Recibido";
				break;
			case "Pdte Envio":
				$extra = ", FechaEnvio = '0000-00-00', FechaRecepcion = '0000-00-00' ";
				break;    				
		}
		
		$sql =
		  " UPDATE ges_subsidiariostbjos ".
		  " SET    Status = '$estado' $extra ".
		  " WHERE (IdTbjoSubsidiario='$idestado')";
		query($sql);
	}

	function AgnadeId($nuevoconcepto){
		$conactual = $this->get("IdServicio");
		if (!$conactual or $conactual == ""){
			$conactual = $nuevoconcepto;				
		}  else {
			$conactual = $conactual . " - ". $nuevoconcepto; 
		}
		
		$this->set("IdServicio",$conactual,FORCE);		
	}
	
	function SaveIdServicio(){
		//$this->QuickSave("Servicios",$this->get("Servicios"));
		//$this->set($key,$value,FORCE);
		$IdServicio = CleanRealMysql($this->get("IdServicio"));
		$id         = $this->getId();
		$sql        = 
		  " UPDATE ges_subsidiariostbjos ".
		  " SET    IdServicio = '$IdServicio' ".
		  " WHERE (IdTbjoSubsidiario= $id)";
		$res = query($sql);
	}
}
?>