<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function OpcionesBusqueda($retorno) {
	global $action;
	
	
	$ot = getTemplate("BusquedaAvanzada");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	$idprov = getSesionDato("FiltraProv");
	$idmarca = getSesionDato("FiltraMarca");
	$idcolor = getSesionDato("FiltraColor");
	$idtalla = getSesionDato("FiltraTalla");

		
	$ot->fijar("action",$action);
	$ot->fijar("pagRetorno",$retorno);
	$ot->fijar("comboProveedores",genComboProveedores($idprov));
	$ot->fijar("comboMarcas",genComboMarcas($idmarca));
		
		//echo q($idcolor,"color a mostrar en template");
		//echo q(intval($idcolor),"intval color a mostrar en template");
			
	if (intval($idcolor) >=0)
			$ot->fijar("comboColores",genComboColores($idcolor));
	else
			$ot->fijar("comboColores",genComboColores("ninguno"));
			
	$ot->fijar("comboTallas",genComboTallas($idtalla));
		
	
	echo $ot->Output();	
}

PageStart();

switch($modo){
	default:
	case "avanzada":
	$retorno = $_GET["vuelta"];
	OpcionesBusqueda($retorno);
	break;	
}


PageEnd();


?>