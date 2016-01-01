<?php

function VolcarGeneracionJSParaProductos($nombre=false,$referencia=false,$cb=false){

         $nombre_up     = CleanRealMysql($nombre);
	 $referencia_s  = CleanRealMysql(strtoupper(trim($referencia)));
	 $cb_s          = CleanRealMysql($cb);
	 $IdLocalActivo = getSesionDato("IdTiendaDependiente");
	 $extraBusqueda = explode("|",$nombre_up);
	 $extrafiltro   = (isset($extraBusqueda[1]))? " AND (ges_detalles.Talla like '%$extraBusqueda[1]%' OR ges_marcas.Marca like '%$extraBusqueda[1]%' OR ges_modelos.Color like '%$extraBusqueda[1]%' OR  ges_laboratorios.NombreComercial like '%$extraBusqueda[1]%') ":"";
	 
	 $filtro  = ( $nombre )?" AND UPPER(ges_productos_idioma.Descripcion) LIKE '%$extraBusqueda[0]%' ".$extrafiltro:"";
	 $filtro .= ( $referencia )? " AND  ges_productos.Referencia LIKE '%$referencia_s%' ":"";
	 $filtro .= ( $cb )?" AND ges_productos.CodigoBarras = '$cb_s' ":"";
	 $filtro .= " AND  ges_almacenes.Unidades = 0 ";
	 
	 $jsOut   = getProductosSyncAlmacen(array(),$IdLocalActivo,$filtro,false);
	 
	 return $jsOut;
}

?>
