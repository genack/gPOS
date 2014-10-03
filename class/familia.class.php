<?php

function CrearSubFamilia($nombre,$padre){
	$oFamilia = new familia;

	$oFamilia->CreaSubfamilia();
	
	$oFamilia->set("SubFamilia",$nombre,FORCE);
	$oFamilia->set("IdFamilia",$padre);
		
	if ($oFamilia->AltaSubfamilia($padre)){
		//if(isVerbose())	
		//	echo gas("aviso",_("Nuevo familia registrado"));
		return true;							
	} else {
		//if (isVerbose())
		//	echo gas("aviso",_("No se ha podido registrar el nuevo familia"));
		return false;
	}
}

function CrearFamilia($nombre){
	$oFamilia = new familia;

	$oFamilia->Crea();
	
	$oFamilia->setNombre($nombre);

		
	if ($oFamilia->Alta()){	
		//if(isVerbose())
		//	echo gas("aviso",_("Nuevo familia registrado"));			
		CrearSubFamilia("...",$oFamilia->get("IdFamilia"));
		return true;							
	} else {
		//echo gas("aviso",_("No se ha podido registrar el nuevo familia"));
		return false;
	}
}

function FamFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdFamilia"];
	
	$oFam = new familia;
		
	if ($oFam->Load($id))
		return $oFam;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class familia extends Cursor {
    function familia() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_familias", "Id", $id);
		return $this->getResult();
	}

    function LoadSub($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_subfamilias", "Id", $id);
		return $this->getResult();
	}
    
    
    // SET especializados    
    function setNombre($nombre){    	
    	$this->set("Familia",$nombre,FORCE);	
    }        
    
    // GET especializados
    function getNombre(){
    	return $this->get("Familia");	
    }
    
    function getFam(){
    	return $this->get("Familia");
    }
	
	//Formulario de modificaciones y altas
	function formEntrada($action){
		$ot = getTemplate("ModificarFamilia");
		if (!$ot){		return false;		}
								
		//$comboidiomas = genComboIdiomas($this->get("IdIdioma"));
		//$comboperfiles = genComboPerfiles($this->get("IdPerfil"));
									
		$cambios = array(	
			"tTitulo" => _("Modificando familia"),	
			"tFamilia" => _("Nombre"),	
			"vFamilia" => $this->get("Familia"),
			"action" => $action,
			"HIDDENDATA" => Hidden("id",$this->getId()) . Hidden("IdFamilia",$this->get("IdFamilia"))
		);

		return $ot->makear($cambios);		
				
	}
	
	//Formulario de modificaciones y altas
	function formModificarSubfamilia($action){
		$ot = getTemplate("ModificarSubFamilia");
		if (!$ot){		return false;		}
								
		//$comboidiomas = genComboIdiomas($this->get("IdIdioma"));
		//$comboperfiles = genComboPerfiles($this->get("IdPerfil"));
									
		$cambios = array(	
			"tTitulo" => _("Modificando subfamilia"),	
			"tSubFamilia" => _("Nombre"),	
			"vSubFamilia" => $this->get("SubFamilia"),
			"action" => $action,
			"HIDDENDATA" => Hidden("id",$this->getId()) . Hidden("IdFamilia",$this->get("IdFamilia"))
		);

		return $ot->makear($cambios);		
				
	}
		
	function formAlta($action,$padre=false){
		global $action;
		$ot = getTemplate("AltaFamilia");
		if (!$ot){		return false;		}
			
		$titulo  = _("Alta familia");
			
		$cambios = array(	
			"tTitulo" => $titulo,
			"tNombre" => _("Nombre"),
			"vNombre" => $this->get("Familia"),
			"action" => $action
		);

		return $ot->makear($cambios);
	}

	function formAltaSubfamilia($action,$padre=false){
		global $action;
		$ot = getTemplate("AltaSubFamilia");
		if (!$ot){		return false;		}
				
		$titulo  = _("Alta subfamilia");
			
		$cambios = array(	
			"vId" => $this->get("Id"),
			"tTitulo" => $titulo,
			"tNombre" => _("Nombre"),
			"vNombre" => $this->get("SubFamilia"),
			"vIdFamilia" => $padre,
			"action" => $action
		);

		return $ot->makear($cambios);
	}
	
	function Crea(){
		$this->setNombre(_("Nuevo familia"));	
		$this->set("IdIdioma",getSesionDato("IdLenguajeDefecto"),FORCE);		
	}

	function CreaSubfamilia(){
		$this->set("SubFamilia","...");	
		$this->set("IdIdioma",getSesionDato("IdLenguajeDefecto"),FORCE);
		$this->set("IdFamilia",0,FORCE);
	}
	
	function Alta(){
		global $UltimaInsercion;
		
		// VALIDACION, vamos a bloquear la creacion de una familia ya existente
		$familia_s = CleanRealMysql($this->get("Familia"));
		$sql = "SELECT IdFamilia FROM ges_familias WHERE Familia='".$familia_s."'";
		$row = queryrow($sql);
		
		
		if ($row and CleanID($row["IdFamilia"])) {
			//Ya existe otra familia con ese nombre
			$idotra = $row["IdFamilia"];
			
			//la marcamos usable, para el caso de que ya exitiera pero fue borrada
			$sql = "UPDATE ges_familias SET Eliminado=0 WHERE IdFamilia='$idotra'";
			query($sql);
			$this->setId($idotra);//para consultas
			return true; //la operacion es un exito: la familia pedida existe
		}
				
				
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
		$sql = "SELECT MAX(IdFamilia) as NextFam  FROM ges_familias WHERE IdIdioma = '$IdIdioma'";
		
		$row = queryrow($sql);				
		if (!$row) { 		
			error(__FILE__ . __LINE__ ,"E: $sql fallo");		
		} 
		$this->set("IdFamilia",intval($row["NextFam"])+1,FORCE);
		$this->set("IdIdioma",$IdIdioma,FORCE);					
	
		$data = $this->export();
		
		$coma = false;
		$listaKeys = "";
		$listaValues = "";
				
		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$value_s = CleanRealMysql($value);
			
			$listaKeys .= " $key";
			$listaValues .= " '$value_s'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_familias ( $listaKeys ) VALUES ( $listaValues )";
		
		$res =  query($sql,"Creando familia");
		
		if($res)
			$this->setId($UltimaInsercion);
			
		return $res;							 	
	}

	function AltaSubfamilia($padre){	
		global $UltimaInsercion;
			
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
		$sql = "SELECT MAX(IdSubFamilia) as NextFam  FROM ges_subfamilias WHERE (IdIdioma = '$IdIdioma') AND (IdFamilia='$padre')";
		
		$row = queryrow($sql);				
		if (!$row) { 		
			//error(__FILE__ . __LINE__ ,"E: $sql fallo");
			//Nota: quizas no hay datos de subfamilias
			// ..asi que nos recuperamos del error:
			$row = array("NextFam"=>1);		
		} 
		$this->set("IdFamilia",$padre,FORCE);
		$this->set("IdSubFamilia",intval($row["NextFam"])+1,FORCE);
		$this->set("IdIdioma",$IdIdioma,FORCE);					
	
		$data = $this->export();
		
		$coma = false;
		$listaKeys = "";
		$listaValues = "";
				
		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$value_s = CleanRealMysql($value);
			
			$listaKeys .= " $key";
			$listaValues .= " '$value_s'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_subfamilias ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Creando subfamilia");
		
		if($res)
			$this->setId($UltimaInsercion);
			
		return $res;				 	
	}
	
	function ListadoSub($lang,$min=0,$IdFamilia = 0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT ges_subfamilias.*	
		FROM
		ges_subfamilias		
		WHERE
		IdIdioma = '$lang'
		AND IdFamilia = '$IdFamilia'
		AND Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	
			
		return $res;					
	}


	function Listado($lang,$min=0,$padre=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT ges_familias.*	
		FROM
		ges_familias				
		WHERE
		IdIdioma = '$lang'
		AND Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	
			
		return $res;				
	}
		
	function SiguienteFamilia() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("Id"));		
		return true;			
	}
	
	function Modificacion(){
		$nombre = $this->getNombre();
		$id = $this->getId();
		$sql = "UPDATE ges_familias SET Familia='$nombre' WHERE Id='$id'";
		$res = query($sql);
		if (!$res){
			error(__FILE__ . __LINE__ , "E: no pudo modificar nombre de familia");
			return false; 
		}	
		return true;
	}
	
	function ModificacionSubfamilia(){
		$nombre = $this->get("SubFamilia");
		$id = $this->getId();
		$sql = "UPDATE ges_subfamilias SET SubFamilia='$nombre' WHERE Id='$id'";
		$res = query($sql);
		if (!$res){
			error(__FILE__ . __LINE__ , "E: no pudo modificar nombre de familia");
			return false; 
		}	
		return true;
	}
	
	function EliminarFamilia() {
		$this->MarcarEliminado();
	}
}


?>