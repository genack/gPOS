<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function FormularioEntrada($local) {
	global $action;
	$ot = getTemplate("SeleccionRapidaAlmacen");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
		
	$ot->fijar("action", $action . "?modo=agnade");
	$ot->fijar("IdLocal",$local);	
				
	echo $ot->Output();					
}

function AgnadirCodigoCarritoAlmacen($xid,$local) {

	$id      = getIdFromAlmacen($xid,$local);
	if (!$id) return false;

	$rkardex = getResumenKardex2Articulo($id);
	$igv     = getSesionDato("IGV");
	$aPedido = split("~",$rkardex);
	$cPedido = split(":",$aPedido[1]);	
	$costo   = $aPedido[0];
	$xid     = $cPedido[0];
	$precio  = round($costo*($igv+100)/100,2);
	$u       = $xid.':1:'.$precio; 

	AgnadirCarritoTraspaso($id,$u);
	return true;
}


switch($modo){
	case "agnademudo_almacen":
		$listacompra = $_POST["listacompra"];
		$idlocal     = CleanID($_POST["IdLocal"]);
		$nuevos      = 0;

		foreach (split("\n",$listacompra) as $cb ){
			$cb       = CleanCB($cb);	
			$producto = getIdProductoSerieFromCB($cb);
			$id       = $producto["IdProducto"];
			$serie    = $producto["Serie"];

			if($id && !$serie)
			  {
			    $nuevos++;
			    AgnadirCodigoCarritoAlmacen($id,$idlocal);
			  }
		}	
		echo CleanParaWeb(_("gPOS:\n\n    Agregado $nuevos productos al carrito"));
		exit();
		break;

	case "agnademudo_compras":
		$listacompra = $_POST["listacompra"];		
		
		$num = 0;
		$nuevos = 0;
		foreach (explode("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);	
			$id = getIdFromCodigoBarras($cb);
			$num ++;
			if($id)	{
				AgnadirCarritoCompras($id,1);	
				$nuevos ++;			
			}				
		}	
				
		echo CleanParaWeb(_("gPOS:\n\n     Agregado $nuevos productos al carrito"));
			
		exit();
		break;		
}

PageStart();

switch($modo){
	case "agnadeuna":
		$id = CleanID($_GET["id"]);//Id en almacen
		$u = intval($_GET["u"]);//Unidades	
		
		if ($id)
			AgnadirCarritoTraspaso($id);
				
		echo "<script> 
			window.close();
			</script>";				
		break;
	case "agnade":
		$listacompra = $_POST["listacompra"];
		$idlocal = CleanID($_POST["IdLocal"]);
		
		foreach (split("\n",$listacompra) as $cb ){
			$cb = CleanCB($cb);	
			$id = getIdFromCodigoBarras($cb);
			if($id)
			  AgnadirCodigoCarritoAlmacen($id,$idlocal);
		}		

		echo "<script> 
				//opener.location.href='modalmacenes.php';
				if (opener.solapa)
					opener.solapa('modalmacenes.php?modo=refresh');
				window.close();
			</script>";				
		break;	
	default:
	        $local = CleanID($_GET["IdLocal"]);
		FormularioEntrada($local);
		break;	
}

PageEnd();
 
?>
