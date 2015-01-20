<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function ListarTemplates() {
	global $action;
	$res = Seleccion( "Template","","IdTemplate DESC","");
	
	if (!$res){
		echo gas("aviso","No hay templates disponibles");	
	} else{
		
		echo gas("titulo",_("Lista de templates"));
		echo "<table border=1>";
		while ($oTemplate = TemplateFactory($res) ){		
		
			$id = $oTemplate->getId();
			//error("Info: id es '$id'");
		
			$nombre = $oTemplate->getNombre();
			$comentario = $oTemplate->get("Comentario");
			$linkEdicion = "<a href='#' onclick='editatemplate($id);'>Modificar</a>"; 
			$linkExtra = "<a href='$action?modo=editextra&id=$id'>Extra</a>";
			echo "<tr><td>$nombre</td><td>$comentario</td><td>$linkEdicion</td><td>$linkExtra</td></tr>";
					
		}		
		echo "</table>";
	}
}

function MostrarTemplateParaEdicion($id) {
	global $action;
	
	$oTemplate = new template;
	if (!$oTemplate->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oTemplate->formEntrada($action,true);	
}

function MostrarTemplateExtrasParaEdicion($id) {
	global $action;
	
	$oTemplate = new template;
	if (!$oTemplate->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oTemplate->formExtra($action,true);	
}

function ModificarTemplate($id,$nombre,$codigo){
	$oTemplate = new template;
	if (!$oTemplate->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	$nombreoriginal = $oTemplate->get("Nombre");
	$oTemplate->setNombre($nombre);		
	
	$codigo = str_replace("<#textarea","<textarea",$codigo);
	$codigo = str_replace("/#textarea>","/textarea>",$codigo);
	
	$oTemplate->setCodigo($codigo);
	
	if ($oTemplate->Save()){
		if(isVerbose())
			echo gas("aviso",_("Código cambiado"));
		$_SESSION["Template_$nombreoriginal"] = false;//invalida copia de sesion de template 	
	} else {
		echo gas("problema",_("No se puede cambiar dato"));	
	}	
}

function ModificarExtrasTemplate($id,$comentario,$paginas){
	$oTemplate = new template;
	if (!$oTemplate->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	$nombreoriginal = $oTemplate->get("Nombre");

	$oTemplate->set("Comentario",$comentario,FORCE);
	$oTemplate->set("Paginas",$paginas,FORCE);
	
	
	if ($oTemplate->Save()){
		if(isVerbose())
			echo gas("aviso",_("Datos extra cambiados"));
		$_SESSION["Template_$nombreoriginal"] = false;//invalida copia de sesion de template 	
	} else {
		echo gas("problema",_("No se puede cambiar dato"));	
	}	
}

function OperacionesConTemplates(){
	echo gas("titulo",_("Operaciones sobre Templates"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo template")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;
	
	$oTemplate = new template;

	$oTemplate->Crea();
	
	echo $oTemplate->formEntrada($action,false);	
}

function CrearTemplate($nombre,$codigo){
	$oTemplate = new template;

	$oTemplate->Crea();
	$oTemplate->setNombre($nombre);
	$oTemplate->setCodigo($codigo);
	
	if ($oTemplate->Alta()){
		if(isVerbose())
			echo gas("aviso",_("Nuevo template registrado"));	
	} else {
		echo gas("aviso",_("No se ha podido registrar el nuevo template"));
	}
}

function PaginaBasica(){
	OperacionesConTemplates();	
	ListarTemplates();	
	
}


PageStart();




switch($modo){
	case "newsave":		
	        $nombre = CleanText($_POST["Nombre"]);
		$codigo = CleanText($_POST["Codigo"]);				
		CrearTemplate($nombre,$codigo);
				Separador();
		PaginaBasica();	
		break;	
	case "alta":	
		FormularioAlta();	
		break;
	case "salvarextra":
		$id = CleanID($_POST["id"]);
		$comentario = CleanText($_POST["Comentario"]);
		$paginas = intval($_POST["Paginas"]);
		ModificarExtrasTemplate($id,$comentario,$paginas);
				Separador();
		PaginaBasica();	
		break;			
	case "modsave":
		$id = CleanID($_POST["id"]);
		$codigo = CleanText($_POST["Codigo"]);
		$nombre = CleanText($_POST["Nombre"]);
					
		ModificarTemplate($id,$nombre,$codigo);
				Separador();
		PaginaBasica();	
		break;
	case "editextra":
		$id = CleanID($_GET["id"]);
		MostrarTemplateExtrasParaEdicion($id);
		break;

	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarTemplateParaEdicion($id);
		break;
	default:
		PaginaBasica();
		break;		
}

PageEnd(false);

?>
