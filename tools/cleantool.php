<?php

include("../tool.php");

if(!isUsuarioAdministradorWeb())
	return;



PageStart();


if ($_GET["borrar"]) {
	switch($modo){
		case "compras":
		case "proveedores":
		case "clientes":
			query("DELETE FROM ges_$modo");
			break;
		case "familias":
			query("DELETE FROM ges_$modo");
			query("DELETE FROM ges_sub$modo");
			break;
		case "pedidos":
			query("DELETE FROM ges_$modo");	
			break;	
		case "productos":
			query("DELETE FROM ges_$modo");
			query("DELETE FROM ges_".$modo."_idioma");	
			break;					
		case "stock":
			query("DELETE FROM ges_productos");
			query("DELETE FROM ges_productos_idioma");
			query("DELETE FROM ges_almacenes");		
			break;		
	}
}

PageEnd();

?>
