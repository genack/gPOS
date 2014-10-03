<?php

 
function getNombreLaboratorio($idLab){
	$oLab = new laboratorio;
	
	if (!$oLab->Load($idLab)){
		return "???";	
	}
	return $oLab->get("NombreComercial");	
}
 
 
class laboratorio extends Cursor {

	function laboratorio() {
		return $this;
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_laboratorios", "IdLaboratorio", $id);
		return $this->getResult();
	}
  	
  	function setNombre($nombre) {
  		$this->set("NombreComercial",$nombre,FORCE);	
  	}
  	
  	function Crea(){
		$this->setNombre(_("Nuevo laboratorio"));
	}  
    
	function Alta(){
	
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
	
		$sql = "INSERT INTO ges_laboratorios ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}	
	
	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_laboratorios.*		
		FROM
		ges_laboratorios 		
		WHERE
		ges_laboratorios.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteLaboratorio() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdLaboratorio"));		
		return true;			
	}
	
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_laboratorios","IdLaboratorio",$this->get("IdLaboratorio"));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo laboratorio");
			return false;
		}		
		return true;
	}
	
	
}




?>
