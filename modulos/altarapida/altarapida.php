<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

define("VENTANA_POPUP",true);

function AccionesTrasAlta(){
	global $action;
	$ot = getTemplate("AccionesTrasAltaPopup");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
	
	$IdProducto = getSesionDato("UltimaAltaProducto");
				
	$ot->fijar("IdProducto", $IdProducto);
	
	//$ot->fijar("tEnviar" , _("Enviar"));
	$ot->fijar("action", $action);	
				
	echo $ot->Output();												
}

function FormularioAlta() {
	global $action;

	$oProducto = new producto;

	$oProducto->Crea();
	
	echo $oProducto->formEntrada($action,false,false,VENTANA_POPUP);	
}

PageStart();

switch($modo){
	case "newsave":
	        $nombre 	= CleanText($_POST["Nombre"]);			
		$referencia 	= CleanReferencia($_POST["Referencia"]);
		$descripcion 	= CleanText($_POST["Descripcion"]);
		$precioventa 	= CleanDinero($_POST["PrecioVenta"]);
		$precioonline 	= CleanDinero($_POST["PrecioOnline"]);
		$coste 	        = CleanDinero($_POST["CosteSinIVA"]);
		$idfamilia	= CleanID($_POST["IdFamilia"]);
		$idsubfamilia 	= CleanID($_POST["IdSubFamilia"]);
		$idprovhab 	= CleanID($_POST["IdProvHab"]);
		$codigobarras 	= CleanCB($_POST["CodigoBarras"]);
		$refprovhab 	= CleanReferencia($_POST["RefProvHab"]);
		
		$idcolor 	= CleanID($_POST["IdColor"]);
		$idtalla	= CleanID($_POST["IdTalla"]);
		$idmarca	= CleanID($_POST["IdMarca"]);
		
		if (CrearProducto($nombre,$referencia,
				$descripcion, $precioventa,
				$precioonline,$coste,$idfamilia,$idsubfamilia,$idprovhab,
				$codigobarras,$idtalla,$idcolor,
				$idmarca,$refprovhab)) {		
			// 			
			AccionesTrasAlta();								
		} 
		break;	
	default:
		FormularioAlta();
		break;	
}

PageEnd();
 
?>