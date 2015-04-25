<?php

function DetalleProductosAlmacen($codigo,$descripcion,$idmarca,$idfamilia,$IdLocal,$idlistarPV,$listarTodo,$listalocal){

 
    $descripcion = CleanCadenaSearch($descripcion);
    $codigo = CleanCadenaSearch($codigo);
    $condicion = "";

    if (!isset($codigo) || $codigo == 'CB/Ref.' || $codigo == '')
      $codigo = '';

    if (!isset($descripcion) || $descripcion == '')
      $descripcion = 'Descripcion del Producto';

    if ( $descripcion == 'todos' || $descripcion == 'all' )
      $descripcion = '';

    if ( $idmarca != 0 ||  $idfamilia != 0 || $codigo !='' ){

      if ( $descripcion == 'Descripcion del Producto') 
	$descripcion = '';
    }

    if (!isset($idmarca))
      $idmarca = 0;
    if (!isset($idfamilia))
      $idfamilia = 0; 

    if( $idlistarPV == 1 )
      $condicion = $condicion." AND ( PrecioVentaSource != '0' OR PrecioVentaCorpSource != '0' )";

    if($codigo != "")
      $condicion = $condicion." AND ( ges_productos.Referencia like '%$codigo%' OR ges_productos.CodigoBarras like '$codigo' )";

    if($descripcion != ""){
      $anombre   = explode("|", $descripcion);
    
      $condicion .= ($anombre[0] != '')? "  AND ges_productos_idioma.Descripcion like '%$anombre[0]%'":"";
      $condicion .= (isset($anombre[1]))? " AND ( ges_marcas.Marca like '%$anombre[1]%' OR ges_modelos.Color like '%$anombre[1]%' OR ges_detalles.Talla like '%$anombre[1]%' OR ges_laboratorios.NombreComercial like '%$anombre[1]%') ":"";

    }
    if($idfamilia != 0)
        $condicion = $condicion." AND ges_productos.IdFamilia = '$idfamilia' ";

    if($idmarca != 0)
        $condicion = $condicion." AND ges_productos.IdMarca = '$idmarca' ";

    if($listarTodo == 0)
         $condicion = $condicion." AND ( ges_almacenes.Unidades > 0 OR ges_productos.Servicio = 1 ) ";


    if($listalocal != 0)
      $IdLocal = $listalocal;

    $sql = " SELECT ges_almacenes.IdProducto, CONCAT(ges_productos.Referencia,'  ".
           " ',ges_productos.CodigoBarras,'   ',ges_productos_idioma.Descripcion) as Descripcion,".
           " Marca, ".
           " Color, Talla, ges_laboratorios.NombreComercial, StockMin,  CostoUnitario, ".
           " ges_almacenes.Unidades,  PrecioVenta, PVDDescontado, PrecioVentaCorporativo, ".
           " PVCDescontado, PrecioVentaSource, PrecioVentaCorpSource, UnidadMedida, CostoOperativo, ges_productos.IdFamilia, ges_productos.IdSubFamilia ".         

	   "FROM   ges_almacenes ".
	   "LEFT   JOIN ges_productos ON ges_almacenes.IdProducto = ges_productos.IdProducto ".
	   "INNER  JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "INNER  JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla ".
	   "INNER  JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor ".

	   "INNER  JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	   "INNER  JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	   "INNER  JOIN ges_locales      ON ges_locales.IdLocal    = ges_almacenes.IdLocal ".
           " WHERE  ges_almacenes.IdLocal = '".$IdLocal."' ".
           $condicion." ".
           " ORDER BY ges_productos_idioma.Descripcion ASC ";

   //$sql=$sql." limit ".$iniciopagina.",100";
    $res = query($sql);
    if (!$res) return false;
    $productosAlmacen = array();
    $t = 0;
    while($row = Row($res)){
      $nombre = "producto_" . $t++;
      $row["MUSubFamilia"] = ObtenerMUSubFamilia($row["IdProducto"],$row["IdFamilia"],$row["IdSubFamilia"]);
      $productosAlmacen[$nombre] = $row; 		
    }		

    return $productosAlmacen;
    
}


function eliminarNuevosPVAlmacen($IdLocal){
    $sql = "UPDATE ges_almacenes SET PrecioVentaSource = '0', PrecioVentaCorpSource = '0' WHERE IdLocal='".$IdLocal."' AND Eliminado = 0";
    $res = query($sql);
    if(!$res)
      return false;
}

function listarNuevosPVAlmacen($IdLocal){
  $sql = "SELECT 
                IdProducto
          FROM 
                 ges_almacenes 
          WHERE 
                 IdLocal='".$IdLocal."' AND Eliminado = '0' AND 
                 ( PrecioVentaCorpSource != '0' OR PrecioVentaSource != '0')"; 

  $res = query($sql);
  if(!$res || mysql_num_rows($res) == 0)
    return false;
  if( mysql_num_rows($res) > 0 )
    return true;
}

function actualizarNuevosPVAlmacen($IdLocal){
  $update = false;
  $igv    = getSesionDato("IGV");
  $sql    = "SELECT 
                IdProducto,PrecioVentaSource, PrecioVentaCorpSource 
             FROM 
                 ges_almacenes 
             WHERE 
                 IdLocal='".$IdLocal."' AND Eliminado = '0' AND 
                 ( PrecioVentaCorpSource != '0' OR PrecioVentaSource != '0')"; 
  $res = query($sql);
  if (!$res) return false;
  
  while($row = Row($res)){
    $set = "";
    $coma = "";
    $update = false;
    $idproducto = $row["IdProducto"];
    
    if($row["PrecioVentaSource"] <> '0')
      { 
	$PV  = explode("~", $row["PrecioVentaSource"]); 
	$set = $set."PrecioVenta = '".$PV[0] ."', PVDDescontado = '".$PV[1]."', PrecioVentaSource = '0'"; 
	$coma = ",";
	$update = true;
      }
    if($row["PrecioVentaCorpSource"] <> '0')
      {
	$PVC = explode("~", $row["PrecioVentaCorpSource"]); 
	$set = $set."".$coma." PrecioVentaCorporativo = '".$PVC[0]."', PVCDescontado = '".$PVC[1]."', PrecioVentaCorpSource = '0' ";  
	$update = true;
      }

    if($update)
      {
	$sql = "UPDATE ges_almacenes SET Impuesto = '".$igv."',".$set." WHERE IdLocal='".$IdLocal."' AND IdProducto = '".$idproducto."' AND Eliminado = 0";
	query($sql);
      }
  }
  return $update;
}

function actualizarAllNuevosPVAlmacen(){
  $update = false;
  $sql = "SELECT 
                IdProducto,PrecioVentaSource, PrecioVentaCorpSource 
          FROM 
                 ges_almacenes 
          WHERE 
                 Eliminado = '0' AND 
                 ( PrecioVentaCorpSource != '0' OR PrecioVentaSource != '0')"; 
  $res = query($sql);
  if (!$res) return false;
  
  while($row = Row($res)){
    $set = "";
    $coma = "";
    $update = false;
    $idproducto = $row["IdProducto"];
    
    if($row["PrecioVentaSource"] <> '0'){ 
      $PV = explode("~", $row["PrecioVentaSource"]); 
      $set = $set."PrecioVenta = '".$PV[0] ."', PVDDescontado = '".$PV[1]."', PrecioVentaSource = '0'"; 
      $coma = ",";
      $update = true;
    }
    if($row["PrecioVentaCorpSource"] <> '0'){
      $PVC = explode("~", $row["PrecioVentaCorpSource"]); 
      $set = $set."".$coma." PrecioVentaCorporativo = '".$PVC[0]."', PVCDescontado = '".$PVC[1]."', PrecioVentaCorpSource = '0' ";  
      $update = true;
    }
    if($update){
      $sql = "UPDATE ges_almacenes SET ".$set." WHERE IdProducto = '".$idproducto."' AND Eliminado = 0";
      query($sql);
    }
  }
  return $update;
}

function eliminarCambiosPreciosVentaAlmacen($PV,$PVD,$MDS,$idproducto,$IdLocal){
  if( $MDS == 'PVD'){ $setPV = "PrecioVentaSource = '0'"; $selPV = "PrecioVenta,PVDDescontado"; $PV = "PrecioVenta"; $PVD = "PVDDescontado";}
  if( $MDS == 'PVC'){ $setPV = "PrecioVentaCorpSource = '0'"; $selPV = "PrecioVentaCorporativo,PVCDescontado"; $PV = "PrecioVentaCorporativo"; $PVD = "PVCDescontado";}
  if($PV >= $PVD){
    $sql = "UPDATE ges_almacenes SET ".$setPV." WHERE IdLocal='".$IdLocal."' AND IdProducto ='".$idproducto."' AND Eliminado = 0";
    $res = query($sql);
    if(!$res)
      return false;
    //ENVIA LOS PRECIOS TPV
    $sql = 
      " SELECT ".$selPV.
      " FROM  ges_almacenes ".
      " WHERE IdLocal  ='".$IdLocal."'".
      " AND   IdProducto ='".$idproducto."'".
      " AND   Eliminado  = 0";
    $row = queryrow($sql);
    if($row)
      return $row[$PV]."~".$row[$PVD];
    else
      return false;
  }    

}

function guardarPreciosVentaAlmacen($PV,$PVD,$MDS,$idproducto,$IdLocal){

    if( $MDS == 'PVD'){ $setPV = "PrecioVentaSource = '".$PV."~".$PVD."'"; }
    if( $MDS == 'PVC'){ $setPV = "PrecioVentaCorpSource = '".$PV."~".$PVD."'"; }
    if($PV >= $PVD){
      $sql = 
	" UPDATE ges_almacenes SET ".$setPV.
	" WHERE IdLocal  =".$IdLocal.
	" AND IdProducto =".$idproducto.
	" AND Eliminado  = 0";
      $res = query($sql);

    }
    if (!$res) return true;
    else return false;
}

function actualizarStockMinimoAlmacen($SM,$idproducto,$IdLocal){
  $sql = 
    " UPDATE ges_almacenes ".
    " SET StockMin=".$SM.
    " WHERE IdLocal=".$IdLocal.
    " AND IdProducto =".$idproducto.
    " AND Eliminado = 0";
  $res = query($sql);
  if (!$res) return false;
  else return $SM;
}

function MarcarGenerico($marcado,$marcador,$IdAlmacen){
	if (!$marcado or !is_array($marcado))
		return 0;
	$num = 0;
	$IdLocal = getSesionDato("IdTienda");

	if( $IdAlmacen != $IdLocal ){
	  echo gas("aviso",_("Verifica el local listado."));
	  return $num;		
	}

	foreach ($marcado as $Id){
		$num++;
		$sql="UPDATE ges_almacenes SET $marcador WHERE Id = '$Id' AND IdLocal='$IdAlmacen'";
		$res = query($sql,"Marcar cambios articulo");			
	}
	return $num;		
}
function MarcarGenericoUnidades($marcado,$marcador,$IdAlmacen,$cantidad,$unid=false){

	if (!$marcado or !is_array($marcado))
		return 0;

	$num       = 0;
	$Seleccion = 0;
	$IdLocal   = getSesionDato("IdTienda");

	if( $IdAlmacen != $IdLocal ){
	  echo gas("aviso",_("Verifica el local listado. "));
	  return $num;		
	}

	foreach ($marcado as $Id){
		$num++;

		$mSeleccion = $cantidad[$Id];
		$precio     = $cantidad['Precio'.$Id];	      
		$aSeleccion = explode("~", $mSeleccion);
		$Seleccion  = 0;
		foreach ( $aSeleccion as $Pedido )
		  {
		    $aPedido    = explode(":", $Pedido);
		    $Seleccion += $aPedido[1];
		  }

		$precio = ( $precio )? ",PrecioVentaOferta='".$precio."'":"";
		$unid   = ( $unid   )? $Seleccion:0;
		$sql    = " UPDATE ges_almacenes SET ".$marcador." ".$unid." ".$precio.
                          " WHERE  Id      = '".$Id."' ".
                          " AND    IdLocal = '".$IdAlmacen."'";
		$res    = query($sql,"Marcar cambios articulo");			
	}
	$unid="";
	return $num;		
}

function MarcarGenericoProducto($marcado,$marcador){
	if (!$marcado or !is_array($marcado))
		return 0;
	
	$num = 0;
	foreach ($marcado as $Id){
		$num++;
		$IdProducto = getIdProducto2Articulo($Id);
		$sql = 
		  "update ges_productos set $marcador ".
		  "where  IdProducto = '$IdProducto'";
		$res = query($sql,"Marcar cambios producto");
			
	}
	return $num;		
}

function ModificarArticulo($id,$esDisponible,$esDisponibleOnline,$esOferta,$esStockIlimitado,$stockmin,
			   $Producto,$esObsoleto,$UnidDisponible,$UnidReservadas,
			   $UnidOferta,$PrecioOferta,$Stock) {	
	
	if (!Admite("Stocks"))	return false;

	$Obsoleto       = ( $esObsoleto )? 1:0;
	$Disponible     = ( $esDisponible && !$esObsoleto )? 1:0;
	$DispOnline     = ( $esDisponibleOnline )? 1:0;
	$UnidDisponible = ( $esDisponible && !$esObsoleto )? $UnidDisponible:0;
	$UnidDisponible = ( $UnidDisponible == $Stock )? 0:$UnidDisponible;
	$Oferta         = ( $esOferta )? 1:0;
	$OfertaUnidades = ( $esOferta && $esDisponible )? $UnidOferta:0;
	$StockIlimitado = ( $esStockIlimitado )? 1:0;

	//Almacen
	$KeyValue  = "Disponible='$Disponible',";
	$KeyValue .= "DisponibleOnline='$DispOnline',";
	$KeyValue .= "DisponibleUnidades='$UnidDisponible',";
	$KeyValue .= "Oferta='$Oferta',";
	$KeyValue .= "OfertaUnidades='$UnidOferta',";
	$KeyValue .= "PrecioVentaOferta='$PrecioOferta',";
	$KeyValue .= "StockIlimitado='$StockIlimitado',";
	$KeyValue .= "StockMin='$stockmin'";
	$IdAlmacen = $_SESSION["LocalMostrado"];	  
	$num       = MarcarGenerico(array("$id"), $KeyValue, $IdAlmacen);

	//Obsoleto
	MarcarGenericoProducto(array("$id"), " Obsoleto = '$Obsoleto' ");

	if( $num != 0 )
	  echo gas("aviso",_(" Modificado Correctamente el Producto : <br/><br/>  $Producto "));

	return true;
}

function OperacionTrasladoResumida($Destino,$Origen,$Motivo) {

	$oTraslado = new traslado;
	$oTraslado->OperacionTraslado($Destino,$Origen,$Motivo);
}

function guardarCostoOperativo($COP,$xid,$IdLocal){
  $sql = 
        " UPDATE ges_almacenes SET CostoOperativo = '$COP' ".
	" WHERE IdProducto =".$xid.
	" AND IdLocal =".$IdLocal.
	" AND Eliminado  = 0";
  $res = query($sql);

  if (!$res) return false;
  else return $COP;
}

?>
