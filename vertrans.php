<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function ListarCarroTrans($seleccion){
	global $action;
	
	//Creamos template
	$ot = getTemplate("ListadoMultiAlmacenSeleccion");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }	
		
	$articulo = new articulo;		
	
	$tamPagina = $ot->getPagina();
	
	$indice = getSesionDato("PaginadorSeleccionAlmacen");
	$num = 0;
	$salta = 0;
	$ot->resetSeries(array("Unidades","PrecioVenta",
				"IdProducto","Nombre","Referencia","NombreComercial","Comprar","marcatrans","iconos"));	
	foreach ($seleccion as $idarticulo =>$unidadesMover){
		$salta ++;
		if ($num <= $tamPagina and $salta>=$indice){		
			$num++;			
			$articulo->Load($idarticulo);
					
			$ot->fijarSerie("Referencia",$articulo->get("Referencia"));
			$ot->fijarSerie("Nombre",$articulo->get("Nombre"));
			$ot->fijarSerie("Unidades",$articulo->get("Unidades"));
			$ot->fijarSerie("PrecioVenta",$articulo->get("PrecioVenta"));
			$ot->fijarSerie("NombreComercial",$articulo->get("NombreComercial"));				
			$ot->fijarSerie("IdProducto",$articulo->get("IdProducto"));
			$ot->fijarSerie("Comprar","");		
			$ot->fijarSerie("Traspasar","");
			$ot->fijarSerie("transid",$idarticulo);
			$ot->fijarSerie("iconos",$articulo->Iconos());
			$ot->fijarSerie("UMover",$unidadesMover);
		}						
	}	
	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	
	$ot->terminaSerie();
	echo $ot->Output();
	//echo "hi! '$num'";		
}


function ListaFormaDeUnidades() {
	//FormaListaCompraCantidades	
	global $action;
	$oProducto = new producto; 
	
	$ot = getTemplate("PopupCarritoCompra");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }

	$ot->resetSeries(array("IdProducto","Referencia","Nombre",
				"tBorrar","tEditar","tSeleccion","vUnidades"));
	
	$tamPagina = $ot->getPagina();
	
	$indice = getSesionDato("PaginadorSeleccionCompras2");			
	$carrito = getSesionDato("CarritoMover");

	//echo q($carrito,"Carrito Cantidades");
	
	
	$costescarrito = getSesionDato("CarroCostesMover");
	
	$quitar = _("Quitar");
	$ot->fijar("tTitulo",_("Carrito para Traslado"));
	//$ot->fijar("comboAlmacenes",getSesionDato("ComboAlmacenes"));
	$ot->fijar("comboAlmacenes",genComboAlmacenes(getParametro("AlmacenCentral")));
	
	$salta = 0;
	$num = 0;
	foreach ( $carrito as $key=>$value){		
		$salta ++;
		if ($num <= $tamPagina and $salta>=$indice){		
			$num++;			
		
			if ($oProducto->Load($key)) {
				$referencia = $oProducto->getReferencia();
				$nombre 	= $oProducto->getNombre();	
			} else {
				$referencia = "";
				$nombre = "";			
			}
			$ot->fijarSerie("vReferencia",$referencia);		
			$ot->fijarSerie("vNombre",$nombre);
			$ot->fijarSerie("tBorrar",$quitar);
			$ot->fijarSerie("vUnidades",$value);
			$ot->fijarSerie("vPrecio",$costescarrito[$key]);
			$ot->fijarSerie("IdProducto",$oProducto->getId());
		}
	}
	
	if (!$salta){
		$ot->fijar("aviso",gas("aviso",_("Carrito vacío")));
		$ot->eliminaSeccion("haydatos");			
	} else {
		$ot->fijar("aviso");
		$ot->confirmaSeccion("haydatos");
	}
	
	
	
	
	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	$ot->terminaSerie();
	
	echo $ot->Output();	
}

PageStart();

switch($modo){
	default:
	case "check":
	$marcado = getSesionDato("CarritoMover");
	ListarCarroTrans($marcado);
	break;	
}


PageEnd();


?>
