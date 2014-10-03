<?php

 
function getNombreProveedor($idProv){
	$oProv = new proveedor;
	
	if (!$oProv->Load($idProv)){
		return "???";	
	}
	return $oProv->get("NombreComercial");	
}
 
 
class proveedor extends Cursor {

	function proveedor() {
		return $this;
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_proveedores", "IdProveedor", $id);
		return $this->getResult();
	}
  	
  	function setNombre($nombre) {
  		$this->set("NombreComercial",$nombre,FORCE);	
  	}
  	
  	function Crea(){
		$this->setNombre(_("Nuevo proveedor"));
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
	
		$sql = "INSERT INTO ges_proveedores ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}	
	
	function Listado($lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_proveedores.*		
		FROM
		ges_proveedores 		
		WHERE
		ges_proveedores.Eliminado = 0
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function listaTodosConNombre(){						

		$sql = "SELECT		
		ges_proveedores.*		
		FROM
		ges_proveedores 		
		WHERE
		ges_proveedores.Eliminado = 0
		";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		$out = array();
		
		while($row = Row($res))
		  {
		    $key       = $row["IdProveedor"];
		    $value     = $row["NombreComercial"];
		    $out[$key] = $value;			
		  }				
								
		return $out;			
	}
 
	function SiguienteProveedor() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdProveedor"));		
		return true;			
	}
	
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_proveedores","IdProveedor",$this->get("IdProveedor"));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}
	
	
}




?>
