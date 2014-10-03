<?php

function AlbaranFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdAlbaranTraspaso"];
	
	$oAlbaran = new albaran;
		
	if ($oAlbaran->Load($id))
		return $oAlbaran;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}



function CrearAlbaran() {

}


class albaran extends Cursor {
    function albaran() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_albaranes_traspaso", "IdAlbaranTraspaso", $id);
		return $this->getResult();
	} 
    	
	function Crea(){
	}
	
	function getNombre(){
		
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
	
		$sql = "INSERT INTO ges_albaranes_traspaso ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta albaran");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdAlbaranTraspaso",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_albaranes_traspaso.*		
		FROM
		ges_albaranes_traspaso 		
		WHERE
		ges_albaranes_traspaso.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteAlbaran() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdAlbaranTraspaso"));		
		return true;			
	}	
		
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_albaranes_traspaso","IdAlbaranTraspaso",$this->get("IdAlbaranTraspaso"));
		
		$res = query($sql,'Modificamos un albaran');
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}
}