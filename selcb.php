<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$op = new Producto;

$op->Crea(); 

$Referencia = $op->get("Referencia");
$Nombre 	= $op->get("Nombre");
$Marca 		= _("Varias");
$primerCB 	= $op->get("CodigoBarras");

switch($modo) {
	case "cb":
		echo $primerCB;
		break;		
	case "subfamilia":
		$IdFamilia  = CleanID($_GET["IdFamilia"]);
		$SubFamilia = genArraySubFamilias($IdFamilia);
		
		foreach ($SubFamilia as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
	case "tallas":
		$IdTallaje = CleanID($_GET["IdTallaje"]);
		$IdFamilia = CleanID($_GET["IdFamilia"]);
		$talla     = genArrayTallas($IdTallaje,$IdFamilia);
		
		foreach ($talla as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
	case "colores":
		$idfamilia = CleanID($_GET["IdFamilia"]);
		$color     = genArrayColores($idfamilia);

		foreach ($color as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
	case "alias":
		$idfamilia = CleanID($_GET["IdFamilia"]);
		$alias     = genArrayProductoAlias($idfamilia);

		foreach ($alias as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
	case "contenedores":
	        $contenedor = genArrayContenedores();
		foreach ($contenedor as $key=>$value) {
			echo "$value=$key\n";
		}		
		break;	
		
}



?>
