<?php

function UserFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdUsuario"];
	
	$oUser = new usuario;
		
	if ($oUser->Load($id))
		return $oUser;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}

function getIdFromDependiente($nombre) {
	$cb = CleanTo($nombre," ");	
	if (!$cb or $cb=="")
		return false;
	
	$sql = 	"SELECT IdUsuario FROM ges_usuarios WHERE Nombre = '$cb'";
	$row = queryrow($sql);
	if (!$row){ 
		return false;
	}
	return $row["IdUsuario"];
}


function getUsuario( $id ){	
	$user = new usuario();				
	$res = $user->Load($id);	
	if (!$res) return false;	
	return $user;	
}


class usuario extends Cursor {
    function usuario() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_usuarios", "IdUsuario", $id);
		return $this->getResult();
	}
    
    
        // SET especializados    
        function setNombre($nombre){    	
	  $this->set("Nombre",$nombre,FORCE);	
	}
    
	// GET especializados
	function getNombre(){
	  return $this->get("Nombre");	
	}
	
	function getIdPerfil(){
	  return $this->get("IdPerfil");	
	}
	
	function getUser(){
	  return $this->get("User");
	}
	
	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
		$ot = getTemplate("ModificarUsuario");
		if (!$ot){return false;}
								
		$comboidiomas  = genComboIdiomas($this->get("IdIdioma"));
		$comboperfiles = genComboPerfiles($this->get("IdPerfil"));
		$combolocales  = genComboLocales($this->get("IdLocal"));

		$cambios = array(	
			"tFechaNacim" => _("Fecha nacim."),
			"vFechaNacim" => $this->get("FechaNacim"),
			"tCuentaBanco" => _("Cuenta banco"),
			"vCuentaBanco" => $this->get("CuentaBanco"),
			"TITULO" => _("Modificando usuario"),	
			"Identificacion" => _("Identificación"),
			"Password" => _("Contraseña"),
			"Direccion" => _("Dirección"),
			"Comision" => _("Comisión"),
			"Ver" => _("Ver"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"comboIdiomas" => $comboidiomas,
			"Local" => _("Local"),
			"comboLocales" => $combolocales,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,	
			"vNombre" => $this->getNombre(),
			"vIdentificacion" => $this->get("Identificacion"),
			"vPassword"=>$this->get("Password"),
			"vDireccion"=>$this->get("Direccion"),
			"vComision"=>$this->get("Comision"),
			"vTelefono"=>$this->get("Telefono"),		
			"ACTION" => "$action?modo=modsave",
			"HIDDENDATA" => Hidden("id",$this->getId())
		);

		return $ot->makear($cambios);		
				
	}
	
	function formAlta($action){
		$ot = getTemplate("AltaUsuario");
		if (!$ot){		return false;		}
		
		$comboidiomas = genComboIdiomas();
		$combolocales  = genComboLocales();
		$comboperfiles = genComboPerfiles();
		
		$cambios = array(	
			"tFechaNacim" => _("Fecha nacim."),
			"vFechaNacim" => $this->get("FechaNacim"),		
			"TITULO" => _("Alta usuario"),
			"Ver" => _("Ver"),	
			"Identificacion" => _("Identificación"),
			"Password" => _("Contraseña"),
			"Direccion" => _("Dirección"),
			"Poblacion" => _("Población"),
			"Comision" => _("Comisión"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"Ver" => _("Ver"),
			"comboIdiomas" => $comboidiomas,
			"Local" => _("Local"),
			"comboLocales" => $combolocales,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,			
			"vNombre" => $this->getNombre(),
			"vIdentificacion" => $this->get("Identificacion"),
			"vPassword"=>$this->get("Password"),
			"TEXTNOMBRE" => _("Nombre perfil"),
			"ACTION" => "$action?modo=newsave",
		);

		return $ot->makear($cambios);
	}
	
	function Crea(){
		$this->setNombre(_("Nuevo usuario"));
		$this->set("Identificacion",genMakePass(),FORCE);
		$this->set("Password",genMakePass(),FORCE);		
		$this->set("FechaNacim","1974-09-01",FORCE);		
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
	
		$sql = "INSERT INTO ges_usuarios ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}
		
}


?>