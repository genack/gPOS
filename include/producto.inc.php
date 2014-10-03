<?php

function VolcarGeneracionJSParaProductos($nombre=false,$referencia=false,$cb=false){

         $nombre_up     = CleanRealMysql(strtoupper($nombre));
	 $referencia_s  = CleanRealMysql(strtoupper(trim($referencia)));
	 $cb_s          = CleanRealMysql($cb);
	 $IdLocalActivo = getSesionDato("IdTiendaDependiente");
	 
	 $filtro  = ( $nombre )?" AND UPPER(ges_productos_idioma.Descripcion) LIKE '%$nombre_up%' ":"";
	 $filtro .= ( $referencia )? " AND  ges_productos.Referencia LIKE '%$referencia_s%' ":"";
	 $filtro .= ( $cb )?" AND ges_productos.CodigoBarras = '$cb_s' ":"";
	 $filtro .= " AND  ges_almacenes.Unidades = 0 ";
	 
	 $jsOut   = getProductosSyncAlmacen(array(),$IdLocalActivo,$filtro,false);
	 
	 return $jsOut;
}

?>
