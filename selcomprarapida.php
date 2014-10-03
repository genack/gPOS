<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function FormularioEntrada() {
	global $action;
	$ot = getTemplate("SeleccionRapida");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
		
	$ot->fijar("action", $action . "?modo=agnade");	
				
	echo $ot->Output();					
}


function AgnadirCodigoCarrito($cb) {
	$id = getIdFromCodigoBarras($cb);
	if (!$id)
		return false;
		
	AgnadirCarritoCompras($id);
	return true;
}


switch($modo){
	case "noselecion":
	case "noseleccion":
	
		setSesionDato("CarritoCompras",0);
		setSesionDato("CarroCostesCompra",false);		
		
		setSesionDato("CompraProveedor",false);
		setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		
		setSesionDato("CarroCostesCompra",false);
		
		
		//Reseteamos carrito (no queremos mezclar productos de diferentes proveedores
		setSesionDato("CarritoCompras",false);
		setSesionDato("PaginadorSeleccionCompras",0);	
		
		break;
	
	case "agnadeuna":
	
		$id = CleanID($_GET["id"]);//IdProducto
		$u = intval($_GET["u"]);//Unidades
		
		if(isUsuarioAdministradorWeb())
			echo q($id,"añadiendo id");
				
		AgnadirCarritoCompras($id,$u);

		
		if ($_GET["close"]){		
			echo "<script> 
			window.close();
			</script>";		
		}	
		

		break;
	case "agnade":
		$listacompra = $_POST["listacompra"];
		
		foreach (split("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);		
			AgnadirCodigoCarrito($cb);
			//echo "$cb<br>";
		}		
		
		echo "<script> 
				opener.location.href='modcompras.php';
				window.close();
			</script>";				
		break;	
	default:
		PageStart();
		FormularioEntrada();
		PageEnd();
		break;	
}


 
?>