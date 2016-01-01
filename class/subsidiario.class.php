<?php

 
function getNombreSubsidiario($idProv){
	$oProv = new Subsidiario;
	
	if (!$oProv->Load($idProv)){
		return "???";	
	}
	return $oProv->get("NombreComercial");	
}
 
// ListadoSubsidiarios
 
class Subsidiario extends Cursor {

	function Subsidiario() {
		return $this;
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_subsidiarios", "IdSubsidiario", $id);
		return $this->getResult();
	}
  	
  	function setNombre($nombre) {
  		$this->set("NombreComercial",$nombre,FORCE);	
  	}
  	
  	function Crea(){
		$this->setNombre(_("Nuevo Subsidiario"));
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
	
		$sql = "INSERT INTO ges_subsidiarios ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}	
	
	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_subsidiarios.*		
		FROM
		ges_subsidiarios 		
		WHERE
		ges_subsidiarios.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteSubsidiario() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdSubsidiario"));		
		return true;			
	}
	
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_subsidiarios","IdSubsidiario",$this->get("IdSubsidiario"));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo Subsidiario");
			return false;
		}		
		return true;
	}
	
	
}

function buscarNumeroFiscalSubs($nfiscal,$idsubs){
  $xwhere = ($idsubs)? " AND IdSubsidiario = '$idsubs'":" AND NumeroFiscal = '$nfiscal'";
  $sql = "SELECT NumeroFiscal FROM ges_subsidiarios ".
         "WHERE Eliminado = 0".
         "$xwhere";
  $row = queryrow($sql);
  return $row["NumeroFiscal"];
}


?>
