<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function ListarUsuarios() {
	global $action;
	$res = Seleccion( "Usuario","","IdUsuario ASC","");
	
	if (!$res){
		echo gas("aviso","No hay usuarios disponibles");	
	} else{
		
		//echo gas("titulo",_("Lista de usuarios"));
		echo "<center>";
		echo "<table border=0 class=forma>";
		echo "<tr><td class='lh'>" ._("Usuario") . "</td><td class='lh'></td><td class='lh'></td><td class='lh'></td></tr>";
		$idusuario = getSesionDato("IdUsuario");
		
		while ($oUsuario = UserFactory($res) ){		
		
			$id = $oUsuario->getId();
			$activo = $oUsuario->get("Estado");
			$nombre = $oUsuario->getNombre();
			$idperfil = $oUsuario->get("IdPerfil");

			$linkEdicion = gAccion("editar",_("Modificar"),$id); 
			$linkborrado = gAccionConfirmada( "borrar", _("Eliminar") ,$id ,_("¿Seguro que quiere borrar?"));

			switch($idusuario){
			case 1:
			  $linkEdicion = ($id == 2)? "":$linkEdicion;
			  $linkborrado = ($id <=3 )? "":$linkborrado;
			  echo "<tr class='f'><td class='nombre'>$nombre</td><td>$linkEdicion</td><td>$linkborrado</td><td>$activo</td></tr>";
			  break;
			default :
			  if($id > 2){
			    if($idperfil == 1){
			      //$linkborrado = ($idusuario == $id )? "":$linkborrado;
			      $linkborrado = ($idperfil  == 1 )? "":$linkborrado;
			    }
			    echo "<tr class='f'><td class='nombre'>$nombre</td><td>$linkEdicion</td><td>$linkborrado</td><td>$activo</td></tr>";			    
			  }
			  break;
			}
					
		}		
		echo "</table>";
	}
	
	userOperacionesConUsuarios();
	echo "</center>";		
}

function MostrarUsuarioParaEdicion($id) {
	global $action;
	
	$oUsuario = new usuario;
	if (!$oUsuario->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oUsuario->formEntrada($action,true);	
}

function ModificarUsuario($id,$nombre,$identificacion,$direccion,$comision,$telefono,
			  $pass,$idioma,$perfil,$cc,$nace,$local,$idlocales,$estado){
	$oUsuario = new usuario;
	if (!$oUsuario->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}	
	$oUsuario->setNombre($nombre);
	$oUsuario->set("Identificacion",$identificacion,FORCE);
	$oUsuario->set("Direccion",$direccion,FORCE);
	$oUsuario->set("Comision",$comision,FORCE);
	$oUsuario->set("Telefono",$telefono,FORCE);
	if($pass != 'usuarios')
	  $oUsuario->set("Password",md5($pass),FORCE);
	$oUsuario->set("IdIdioma",$idioma,FORCE);
	$oUsuario->set("IdPerfil ",$perfil,FORCE);
	$oUsuario->set("IdLocal ",$local,FORCE);
	$oUsuario->set("CuentaBanco",$cc,FORCE);		
	$oUsuario->set("FechaNacim",$nace,FORCE);
	$oUsuario->set("GrupoLocales",$idlocales,FORCE);
	$oUsuario->set("Estado",$estado,FORCE);
	
	if ($oUsuario->Save()){
		//if(isVerbose())
		//	echo gas("aviso",_("Usuario cambiado"));	
		return true;
	} else {
		//echo gas("problema",_("No se puedo cambiar dato"));	
		return false;
	}	
}

function BorrarUsuario($id){
	$oUsuario = new usuario;	
	
	if ($oUsuario->Load($id)) {		
		$nombre = $oUsuario->getNombre();
		//echo gas("Aviso",_("Usuario $nombre borrado"));
		
		$oUsuario->MarcarEliminado();		
		return true;
	}	else {
		//echo gas("Aviso",_("No se ha podido borrar el usuario"));	
		return false;	
	}
}

function userOperacionesConUsuarios(){
	?>	
	<form action="modusers.php?modo=alta" method="post">
	<table class='forma'>
	<tr><td><input  class='btn' value="Crear" type="submit"></td></tr>
	</table>
	</form>
	<?php
}


function OperacionesConUsuarios(){
	if (!isUsuarioAdministradorWeb())
		return;	
	
	echo gas("titulo",_("Operaciones sobre Usuarios"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo usuario")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;
	
	$oUsuario = new usuario;

	$oUsuario->Crea();
	
	echo $oUsuario->formAlta($action,false);	
}

function CrearUsuario($nombre,$identificacion,$direccion,$comision,$telefono,
		      $pass,$idioma,$perfil,$cc,$nace,$local,$idlocales){
	$oUsuario = new usuario;

	$oUsuario->Crea();
	
	$oUsuario->set("Identificacion",$identificacion,FORCE);
	$oUsuario->set("Direccion",$direccion,FORCE);
	$oUsuario->set("Comision",$comision,FORCE);
	$oUsuario->set("Telefono",$telefono,FORCE);
	$oUsuario->set("Password",md5($pass),FORCE);
	$oUsuario->set("IdIdioma",$idioma,FORCE);
	$oUsuario->set("IdPerfil ",$perfil,FORCE);
	$oUsuario->set("IdLocal ",$local,FORCE);
	$oUsuario->set("Nombre",$nombre,FORCE);		
	$oUsuario->set("CuentaBanco",$cc,FORCE);
	$oUsuario->set("FechaNacim",$nace,FORCE);
	$oUsuario->set("GrupoLocales",$idlocales,FORCE);
	
	if ($oUsuario->Alta()){
		//if(isVerbose())
		//	echo gas("aviso",_("Nuevo perfil registrado"));	
		return true;
	} else {
		//echo gas("aviso",_("No se ha podido registrar el nuevo perfil"));
		return false;
	}
}

function PaginaBasica(){
	ListarUsuarios();	
	OperacionesConUsuarios();	
}


PageStart();

//echo gas("cabecera",_("Gestion de Usuarios"));


switch($modo){
	case "newsave":		
	        $nombre    = CleanText($_POST["Nombre"]);	
		$identificacion = CleanText($_POST["Identificacion"]);
		$direccion = CleanText($_POST["Direccion"]);
		$comision  = (isset($_POST["Comision"]))? CleanDinero($_POST["Comision"]):0;
		$telefono  = CleanText($_POST["Telefono"]);
		$pass      = CleanPass($_POST["Password"]);
		$idioma    = CleanID($_POST["Idioma"]);
		$perfil    = CleanID($_POST["Perfil"]);
		$local     = CleanID($_POST["Local"]);
		$cc 	   = (isset($_POST["CuentaBanco"]))? CleanCC($_POST["CuentaBanco"]):0;
		$nace	   = CleanText($_POST["FechaNacim"]);
		$glocales  = CleanText($_POST["GrupoLocales"]);
		$idlocales = obtnerIdLocales($glocales,$local);
		$verificar = verficarExistenciaUsuario($identificacion,0);
		if($verificar) return FormularioAlta();
		if(strlen($pass) < 8 ) return FormularioAlta();

		if($local == 0)
		  $idlocales = '';

		$userpermitidos = obtenerUsuariosPermitidos();
		$xusers    = obtenrUsuariosActivos();

		if($xusers >= $userpermitidos && $userpermitidos != 0){
		  echo gas("aviso","A excedido cantidad de usuarios permitidos");	
		  return PaginaBasica();
		}

	
		CrearUsuario($nombre,$identificacion,$direccion,$comision,$telefono,
			     $pass,$idioma,$perfil,$cc,$nace,$local,$idlocales);
		PaginaBasica();	
		break;	
	case "alta":
		FormularioAlta();	
		break;
	case "modsave":
		$id        = CleanID($_POST["id"]);
		$nombre    = CleanText($_POST["Nombre"]);
		$identificacion = CleanText($_POST["Identificacion"]);
		$direccion = CleanText($_POST["Direccion"]);
		$comision  = CleanDinero($_POST["Comision"]);
		$telefono  = CleanText($_POST["Telefono"]);
		$pass      = CleanPass($_POST["Password"]);
		$idioma    = CleanID($_POST["Idioma"]);
		$perfil    = CleanID($_POST["Perfil"]);
		$local     = CleanID($_POST["Local"]);
		$cc 	   = CleanCC($_POST["CuentaBanco"]);
		$nace	   = CleanText($_POST["FechaNacim"]);
		$estado    = CleanText($_POST["Estado"]);
		$glocales  = CleanText($_POST["GrupoLocales"]);
		$idlocales = obtnerIdLocales($glocales,$local);
		$existe    = verficarExistenciaUsuario($identificacion,$id);
		
		if($estado == 'Activo'){
		  $userpermitidos = obtenerUsuariosPermitidos();
		  $xusers         = obtenrUsuariosActivos();

		  if($xusers >= $userpermitidos && $userpermitidos != 0){
		    echo gas("aviso","A excedido cantidad de usuarios permitidos");	
		    return PaginaBasica();
		  }
		}else{
		  if($id <= 3)
		    $estado = 'Activo';
		  if($id == 2)
		    $estado = 'Inactivo';
		}

		if($existe) return MostrarUsuarioParaEdicion($id);
		if(strlen($pass) < 8 ) return MostrarUsuarioParaEdicion($id);

		if($local == 0)
		  $idlocales = '';

		ModificarUsuario($id,$nombre,$identificacion,$direccion,$comision,
				 $telefono,$pass,$idioma,$perfil,$cc,$nace,$local,$idlocales,
				 $estado);
		PaginaBasica();	
		break;
	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarUsuarioParaEdicion($id);
		break;
	case "borrar":
		$id = CleanID($_GET["id"]);
		BorrarUsuario($id);
		PaginaBasica();			
		break;		
	default:
		PaginaBasica();
		break;		
}



PageEnd();

?>
