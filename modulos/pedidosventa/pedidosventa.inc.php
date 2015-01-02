<?php
function PedidosVentaPeriodo($desde,$hasta,$cliente,$presupuesto,$tipoventa,
			     $estado,$local,$codigo){

  $desde        = CleanRealMysql($desde);
  $hasta        = CleanRealMysql($hasta);
  $cliente      = CleanRealMysql($cliente);
  $cod          = ($codigo != '')? explode("-",$codigo):"";

  $extraFecha   = " AND date(ges_presupuestos.FechaPresupuesto) >= '$desde' AND date(ges_presupuestos.FechaPresupuesto) <= '$hasta' ";

  $extraCliente = ($cliente != '')? " AND ges_clientes.NombreComercial LIKE '%$cliente%' ":"";

  $extraPresup  = ($presupuesto != 'Todos')? " AND ges_presupuestos.TipoPresupuesto = '$presupuesto' ":"";
  $extraTipoVta = ($tipoventa != 'Todos')? " AND ges_presupuestos.TipoVentaOperacion = '$tipoventa' ":"";
  $extraEstado  = ($estado != 'Todos')? " AND ges_presupuestos.Status = '$estado' ":"";
  $extraLocal   = ($local)?" AND ges_presupuestos.IdLocal = '$local' ":"";

  $extraCodigo  = ($codigo != '')? " AND ges_presupuestos.Serie like '%$cod[0]%' AND ges_presupuestos.NPresupuesto like '%$cod[1]%' ":$extraFecha;

  $sql=
    " SELECT ges_presupuestos.IdCliente, IdPresupuesto as Id, Npresupuesto as Codigo,".
    "        ges_clientes.NombreComercial as Cliente, TipoPresupuesto, TipoVentaOperacion, ".
    "        ModoTPV as ModoVenta, TotalImporte, Status as Estado, ".
    "        DATE_FORMAT(ges_presupuestos.FechaPresupuesto, '%e %b %y %H:%i') as Fecha, ".
    "        IF(CBMetaProducto like '',' ',CBMetaProducto) as CBMetaProducto, ".
    "        IF ( DATE_FORMAT(ges_presupuestos.FechaAtencion, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_presupuestos.FechaAtencion, '%e %b %y %H:%i~%Y-%m-%d %H:%i:%S') ) as FechaAtencion, ".
    "        IF(ges_presupuestos.VigenciaPresupuesto like '',' ',ges_presupuestos.VigenciaPresupuesto) as Vigencia, ".
    "        IF ( ges_presupuestos.Observaciones like '', ' ',ges_presupuestos.Observaciones) as Observaciones, ".
    "        (SELECT sum( Descuento ) FROM ges_presupuestosdet 
             WHERE ges_presupuestosdet.IdPresupuesto = ges_presupuestos.IdPresupuesto ) As Descuento, ".
    "        ges_locales.NombreComercial as Local, ges_usuarios.Nombre as Usuario, Serie, ".
    "        DATEDIFF(CURDATE(),FechaPresupuesto) as Expira, ".
    "        ges_locales.IdLocal, ges_presupuestos.IdUsuario ".
    " FROM   ges_presupuestos ".
    " INNER  JOIN ges_clientes ON ".
    "        ges_presupuestos.IdCliente          = ges_clientes.IdCliente ".
    " INNER  JOIN ges_locales  ON ".
    "        ges_presupuestos.IdLocal            = ges_locales.IdLocal ".
    " INNER  JOIN ges_usuarios ON ".
    "        ges_presupuestos.IdUsuario          = ges_usuarios.IdUsuario ".
    " WHERE  ges_presupuestos.Eliminado          = '0' ".
    $extraCodigo.
    $extraCliente.
    $extraPresup.
    $extraTipoVta.
    $extraEstado.
    $extraLocal.
    " ORDER  BY ges_presupuestos.NPresupuesto DESC";

  $res = query($sql);
  if (!$res) return false;
  $PedidoVenta = array();
  $t = 0;
  while($row = Row($res)){
    $nombre = "PedidoVenta_" . $t++;
    $PedidoVenta[$nombre] = $row; 

    $id       = $row["Id"];
    $estado   = $row["Estado"];
    $doc      = $row["TipoPresupuesto"];
    $vigencia = $row["Vigencia"];
    $expira   = $row["Expira"];
    $dif      = $vigencia - $expira;
    if($estado == 'Pendiente' && $dif < 0 && $doc == 'Proforma')
      actualizarEstado($id);
  }	
  return $PedidoVenta;

}

function actualizarEstado($id){
  $sql = "UPDATE ges_presupuestos ".
    "SET Status = 'Vencido' ".
    "WHERE IdPresupuesto = '$id' ";
  query($sql);
}

function DestallePedidosVentaPeriodo($IdPresupuesto,$local){
  $IdPresupuesto = CleanID($IdPresupuesto);

  $sql = 
        "SELECT ges_presupuestosdet.IdProducto, ".
        "       ges_presupuestosdet.IdPresupuestoDet, ".
        "       ges_presupuestosdet.Referencia, ".
        "       ges_presupuestosdet.CodigoBarras, ".
        "       ges_productos_idioma.Descripcion as Nombre, ".
        "       ges_marcas.Marca, ".
        "       IF(ges_presupuestosdet.Color = '',' ',ges_presupuestosdet.Color) as Color, ".
        "       IF(ges_presupuestosdet.Talla = '',' ',ges_presupuestosdet.Talla) as Talla, ".
        "       ges_laboratorios.NombreComercial as Lab, ".
        "       ges_contenedores.Contenedor, ".
        "       ges_productos.VentaMenudeo, ".
        "       ges_productos.UnidadesPorContenedor, ".
        "       ges_productos.UnidadMedida, ".
        "       IF(ges_presupuestosdet.Concepto like '',' ', ".
        "       ges_presupuestosdet.Concepto) as Concepto, ".
        "       Cantidad, Precio, ges_presupuestosdet.Descuento, Importe ".
        "FROM   ges_presupuestosdet ".
        "INNER JOIN ges_productos ON ".
        "           ges_presupuestosdet.IdProducto = ges_productos.IdProducto ".
        "INNER JOIN ges_productos_idioma ON ".
        "           ges_productos.IdProdBase       = ges_productos_idioma.IdProdBase ".
        "INNER JOIN ges_marcas ON ".
        "           ges_productos.IdMarca          = ges_marcas.IdMarca ".
        "INNER JOIN ges_laboratorios ON ".
        "           ges_productos.IdLabHab         = ges_laboratorios.IdLaboratorio ".
        "INNER JOIN ges_contenedores ON ".
        "           ges_productos.IdContenedor     = ges_contenedores.IdContenedor ".
        "WHERE  ges_presupuestosdet.IdPresupuesto  = '$IdPresupuesto' ".
        "AND ges_presupuestosdet.Eliminado         = 0 ";

	$res = query($sql);
	if (!$res) return false;
	$detallepedidoventa = array();
	$t = 0;

	while($row = Row($res)){
		$nombre = "detalles_" . $t++;

		if($row["Concepto"]!= ' ')
		  { $row["Nombre"] = $row ["Concepto"];
		    $row["Marca"] = ' ';
		    $row["Talla"] = ' ';
		    $row["Color"] = ' ';
		    $row["Lab"]   = ' ';
	      }

		$detallepedidoventa[$nombre] = $row;
	}

	if(sizeof($detallepedidoventa)==0)
	  eliminarPresupuesto($IdPresupuesto);

	return $detallepedidoventa;
}

function ModificaPedidosVenta($xid,$campoxdato,$xdet=false,$xhead=false){

        $Tb         = ($xhead)?'ges_presupuestosdet':'ges_presupuestos';
	$IdKey      = ($xdet)?'IdPresupuestoDet':'IdPresupuesto';
	$Id         = CleanID($xid);
	$KeysValue  = $campoxdato;
	$sql   =
	  " update ".$Tb.
	  " set    ".$KeysValue." ".
	  " where  ".$IdKey." = ".$Id;
	return query($sql); 
}

function obtenerMaxNPresupuesto(){
  
  $sql = "SELECT MAX(NPRESUPUESTO) as NPresupuesto ".
         "FROM ges_presupuestos ".
         "WHERE TipoPresupuesto = 'Proforma'";

    $row = queryrow($sql);
    return $row["NPresupuesto"];

}

function obtenerListaPresupuestosTPV($tipopresupuesto){

         //$tipopresupuesto = CleanRealMysql($_GET["tipopresupuesto"]);
         $TipoVenta       = getSesionDato("TipoVentaTPV");
	 $IdLocal         = getSesionDato("IdTiendaDependiente");
	 $esFecha         = "";

	 if($tipopresupuesto == 'Proforma')
	   $esFecha = "AND UNIX_TIMESTAMP(FechaPresupuesto ) > UNIX_TIMESTAMP() - 86400*VigenciaPresupuesto";

	 $sql=
	   " SELECT ges_presupuestos.IdCliente, IdPresupuesto as Id, Npresupuesto as Codigo,".
	   "        NombreComercial as Cliente, ".
	   "        UPPER(DATE_FORMAT(FechaPresupuesto, '%b %d %H:%i')) as Fecha, CBMetaProducto".
	   " FROM   ges_presupuestos ".
	   " INNER  JOIN ges_clientes ON ".
	   "        ges_presupuestos.IdCliente          = ges_clientes.IdCliente ".
	   " WHERE  ges_presupuestos.TipoPresupuesto    = '".$tipopresupuesto."' ".
	   $esFecha.
	   " AND    ges_presupuestos.TipoVentaOperacion = '".$TipoVenta."' ".
	   " AND    ges_presupuestos.IdLocal            = '".$IdLocal."'  ".
	   " AND    ges_presupuestos.Status             = 'Pendiente' ".
	   " AND    ges_presupuestos.Eliminado          = '0' ".
	   " ORDER  BY ges_presupuestos.NPresupuesto DESC";

	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   array_push($arr,$row['Id'].",".
		      $row['Codigo'].", -  ".
		      $row['Codigo']."  -  ".
		      $row['Fecha']."  -  ".
		      $row['Cliente'].",".
		      $row['IdCliente'].",".
		      str_replace(',', '_', $row['CBMetaProducto']));
	 }
	 return implode($arr,";");

}

function obtenerListaMProductosTPV($Estado){

         //$Estado    = trim(CleanRealMysql($_GET["Estado"]));
         $TipoVenta = getSesionDato("TipoVentaTPV");
	 $IdLocal   = getSesionDato("IdTiendaDependiente");

	 $sql=
	   "SELECT ges_clientes.IdCliente,".
	   "       IdMetaProducto as Id, ".
	   "       ges_metaproductos.IdProducto as IdMProducto, ".
	   "       CBMetaProducto as Codigo, ".
	   "       ges_productos.CodigoBarras, ".
	   "       ges_clientes.NombreComercial as Cliente, ".
	   "       UPPER(DATE_FORMAT(ges_metaproductos.FechaRegistro, '%b %d %H:%i'))  as Fecha,".
	   "       CONCAT(Descripcion,'  ',Marca,'  ',Color,'  ',Talla) as MProducto ".
	   "FROM   ges_metaproductos ".
	   "INNER  JOIN ges_clientes  ON ".
	   "       ges_metaproductos.IdCliente  = ges_clientes.IdCliente ".
	   "INNER  JOIN ges_productos  ON ".
	   "       ges_metaproductos.IdProducto  = ges_productos.IdProducto ".
	   "INNER  JOIN ges_modelos ON ".
	   "       ges_productos.IdColor = ges_modelos.IdColor ".
	   "INNER  JOIN ges_detalles ON ".
	   "       ges_productos.IdTalla = ges_detalles.IdTalla ".
	   "INNER  JOIN ges_marcas ON ".
	   "       ges_productos.IdMarca = ges_marcas.IdMarca ".
	   "INNER  JOIN ges_productos_idioma ON ".
	   "       ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "WHERE  ".
	   //"ges_metaproductos.TipoVentaOperacion = '".$TipoVenta."' ".
	   "       ges_metaproductos.IdLocal            = '".$IdLocal."' ".
	   "AND    ges_metaproductos.Status             = '".$Estado."'".
	   "AND    UNIX_TIMESTAMP(ges_metaproductos.FechaRegistro ) > UNIX_TIMESTAMP() - 86400*VigenciaMetaProducto ".
	   "ORDER  BY ges_metaproductos.IdMetaProducto DESC";
	 
	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   array_push($arr,$row['Id'].",".
		      $row['Codigo'].",- ".
		      $row['Codigo']." -   ".
		      $row['Fecha']."   -   ".
		      $row['MProducto'].",   ".
		      //$row['Cliente'].",".
		      $row['IdMProducto'].",".
		      $row['IdCliente'].",".
		      $row['CodigoBarras']);
	 }
	 return implode($arr,";");

}


function obtenerDetalleMProductoTPV($IdProducto,$IdMetaProducto,$IdCliente){

         $TipoVenta = getSesionDato("TipoVentaTPV");
	 $IdLocal   = getSesionDato("IdTiendaDependiente");

	 $sql= 
	   " SELECT ges_metaproductosdet.IdProducto,".
	   "        ges_metaproductosdet.CodigoBarras,Cantidad,".
	   "        CONCAT(Descripcion,' ',Marca,' ',Color,' ',Talla) as Descripcion,".
	   "        Unidades AS CantidadAlmacen,".
	   "        ges_metaproductosdet.Costo,PrecioVenta,".
	   "        ges_almacenes.CostoUnitario,".
	   "        PrecioVentaCorporativo,Importe,Concepto,ges_metaproductosdet.Serie ".
	   " FROM   ges_metaproductosdet ".
	   " INNER  JOIN ges_metaproductos ON        ".
	   "          ges_metaproductosdet.IdMetaProducto   = ges_metaproductos.IdMetaProducto".
	   " INNER  JOIN ges_productos ON ".
	   "       ges_productos.IdProducto = ges_metaproductosdet.IdProducto ".
	   "INNER  JOIN ges_marcas ON ".
	   "       ges_productos.IdMarca = ges_marcas.IdMarca ".
	   " INNER  JOIN ges_productos_idioma ON ".
	   "       ges_productos_idioma.IdProdBase   =   ges_productos.IdProdBase ".
	   " INNER  JOIN ges_almacenes ON ".
	   "       ges_almacenes.IdProducto = ges_metaproductosdet.IdProducto  ".
	   " WHERE  ".
	   //"ges_metaproductos.TipoVentaOperacion = '".$TipoVenta."' ".
	   "        ges_metaproductos.IdMetaProducto     = '".$IdMetaProducto."' ".
	   " AND    ges_metaproductos.IdLocal            = '".$IdLocal."'  ".
	   " AND    ges_almacenes.IdLocal                = '".$IdLocal."'  ".
	   " AND    ges_metaproductos.IdCliente          = '".$IdCliente."'  ".
	   " AND    ges_metaproductos.Status             = 'Ensamblaje' ".
	   " AND    ges_metaproductos.Eliminado          = '0' ".
	   " AND    ges_metaproductosdet.Eliminado       = '0' ";
	 
	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){

	   //Series...
	   $row['Serie'] = ($row['Serie'])? obtenerSeriesReservadas($IdMetaProducto,false,
								    $row['IdProducto'],
								    'MetaProducto'):0;
	   //kardex Costo...
	   $row['Costo'] = ( $row['CostoUnitario'] != $row['Costo'] )? $row['CostoUnitario']:$row['Costo'];
	   
	   array_push($arr,$row['IdProducto'].","
		      .$row['CodigoBarras'].","
		      .$row['Cantidad'].","
		      .$row['CantidadAlmacen'].","
		      .$row['Costo'].","
		      .$row['PrecioVenta'].","
		      .$row['PrecioVentaCorporativo'].","
		      .$row['Importe'].","
		      .$row['Concepto'].","
		      .$row['Descripcion'].","
		      .$row['Serie']);
	 }
	 return implode($arr,";"); 
}

function obtenerDetalleBaseMProductoTPV($IdProducto,$IdMetaProducto,$IdCliente){

         $TipoVenta      = getSesionDato("TipoVentaTPV");
	 $IdLocal        = getSesionDato("IdTiendaDependiente");

	 $sql= 
	   " SELECT ges_metaproductosdet.IdProducto,".
	   "        ges_metaproductosdet.CodigoBarras,Cantidad,".
	   "        CONCAT(Descripcion,' ',Marca,' ',Color,' ',Talla) as Descripcion,".
	   "        Unidades AS CantidadAlmacen,".
	   //"        ges_metaproductosdet.Costo,".
	   "        ges_almacenes.CostoUnitario AS Costo,".
	   "        PrecioVenta,".
	   "        PrecioVentaCorporativo,Importe,Concepto".
	   " FROM   ges_metaproductosdet ".
	   " INNER  JOIN ges_metaproductos ON        ".
	   "          ges_metaproductosdet.IdMetaProducto   = ges_metaproductos.IdMetaProducto".
	   " INNER  JOIN ges_productos ON ".
	   "       ges_productos.IdProducto = ges_metaproductosdet.IdProducto ".
	   "INNER  JOIN ges_marcas ON ".
	   "       ges_productos.IdMarca = ges_marcas.IdMarca ".
	   " INNER  JOIN ges_productos_idioma ON ".
	   "       ges_productos_idioma.IdProdBase   =   ges_productos.IdProdBase ".
	   " INNER  JOIN ges_almacenes ON ".
	   "       ges_almacenes.IdProducto = ges_metaproductosdet.IdProducto  ".
	   " WHERE  ".
	   //"ges_metaproductos.TipoVentaOperacion = '".$TipoVenta."' ".
	   "        ges_metaproductos.IdMetaProducto     = '".$IdMetaProducto."' ".
	   " AND    ges_metaproductos.IdLocal            = '".$IdLocal."'  ".
	   " AND    ges_almacenes.IdLocal                = '".$IdLocal."'  ".
	   " AND    ges_metaproductos.Eliminado          = '0' ".
	   " AND    ges_metaproductosdet.Eliminado       = '0' ";
	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   array_push($arr,$row['IdProducto'].","
		      .$row['CodigoBarras'].","
		      .$row['Cantidad'].","
		      .$row['CantidadAlmacen'].","
		      .$row['Costo'].","
		      .$row['PrecioVenta'].","
		      .$row['PrecioVentaCorporativo'].","
		      .$row['Importe'].","
		      .$row['Concepto'].","
		      //.$row['Descuento'].","
		      .$row['Descripcion']);
	 }
	 return implode($arr,";"); 

}

function getDetBaseMProducto($IdLocal,$IdProducto){

         $sql= 
	   " SELECT IdMetaProducto,".
	   "        CBMetaProducto".
	   " FROM   ges_metaproductos ".
	   " WHERE  ".
	   "        ges_metaproductos.IdProducto         = '".$IdProducto."' ".
	   " AND    ges_metaproductos.IdLocal            = '".$IdLocal."'  ".
	   " AND    ges_metaproductos.Eliminado          = '0' ".
	   " ORDER  BY IdMetaProducto DESC LIMIT 0 , 1";
	 $res = query($sql);
	 if($row= Row($res)) 
	   return $row['IdMetaProducto'].";".$row['CBMetaProducto'];
	 else
	   return "0";
	 
}

function obtenerDetPresupuestoTPV($TipoPresupuesto,$IdPresupuesto,$IdCliente){

         $TipoVenta = getSesionDato("TipoVentaTPV");
	 $IdLocal   = getSesionDato("IdTiendaDependiente");

	 $sql= 
	   " SELECT ges_presupuestosdet.IdProducto,".
	   "        ges_presupuestosdet.CodigoBarras,".
	   "        ges_presupuestosdet.Cantidad,".
	   "        ges_almacenes.Unidades AS CantidadAlmacen,".
	   "        ges_presupuestosdet.Precio,".
	   "        ges_almacenes.PrecioVenta,".
	   "        ges_almacenes.PrecioVentaCorporativo,".
	   "        ges_presupuestosdet.Importe,".
	   "        ges_presupuestosdet.Descuento,".
	   "        ges_presupuestosdet.Concepto,".
	   "        ges_productos.Serie".
	   " FROM   ges_presupuestosdet ".
	   " INNER  JOIN ges_presupuestos ON ".
	   "        ges_presupuestosdet.IdPresupuesto   = ges_presupuestos.IdPresupuesto ".
	   " INNER  JOIN ges_productos ON ".
	   "        ges_productos.IdProducto   = ges_presupuestosdet.IdProducto ".
	   " INNER  JOIN ges_almacenes ON ".
	   "        ges_almacenes.IdProducto = ges_presupuestosdet.IdProducto ".
	   " WHERE  ges_presupuestos.TipoPresupuesto    = '".$TipoPresupuesto."' ".
	   " AND    ges_presupuestos.TipoVentaOperacion = '".$TipoVenta."' ".
	   " AND    ges_presupuestos.IdPresupuesto      = '".$IdPresupuesto."' ".
	   " AND    ges_presupuestos.IdLocal            = '".$IdLocal."'  ".
	   " AND    ges_almacenes.IdLocal               = '".$IdLocal."'  ".
	   " AND    ges_presupuestos.IdCliente          = '".$IdCliente."'  ".
	   " AND    ges_presupuestos.Status             = 'Pendiente' ".
	   " AND    ges_presupuestos.Eliminado          = '0' ";
	 //" ORDER  BY ges_presupuestos.NPresupuesto DESC";

	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   
	   $row['Serie'] = ($row['Serie'])? obtenerSeriesReservadas($IdPresupuesto,false,
								    $row['IdProducto'],
								    'Venta'):0;	       
	   array_push($arr,$row['IdProducto'].","
		      .$row['CodigoBarras'].","
		      .$row['Cantidad'].","
		      .$row['CantidadAlmacen'].","
		      .$row['Precio'].","
		      .$row['PrecioVenta'].","
		      .$row['PrecioVentaCorporativo'].","
		      .$row['Importe'].","
		      .$row['Concepto'].","
		      .$row['Descuento'].","
		      .$row['Serie']);
	 }
	 return implode($arr,";"); 
}

function obtenerListaBaseMProductos(){

         $sql=
	   " SELECT IdProducto as Id,CodigoBarras as Codigo, ".
	   "        CONCAT(Descripcion,'   ',Marca,'   ',Color,'   ',Talla) as MProducto".
	   " FROM   ges_productos ".
	   " INNER  JOIN ges_productos_idioma ON ".
	   "        ges_productos_idioma.IdProdBase = ges_productos.IdProdBase".
	   " INNER  JOIN ges_modelos  ON ".
	   "        ges_productos.IdColor = ges_modelos.IdColor ".
	   " INNER  JOIN ges_detalles  ON ".
	   "        ges_productos.IdTalla = ges_detalles.IdTalla ".
	   " INNER  JOIN ges_marcas ON ".
	   "        ges_productos.IdMarca= ges_marcas.IdMarca ".
	   " WHERE  ges_productos.Eliminado = 0 ".
	   " AND    MetaProducto = 1 AND Serie = 1".
	   " ORDER  BY Descripcion ASC";

	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   array_push($arr,
		      $row['Id'].",".
		      $row['Codigo']."  -  ".
		      $row['MProducto']);
	 }

	 return implode($arr,";");
}

function setStatusPresupuestoTPV($IdPresupuesto,$Opcion){

         $TipoVenta = getSesionDato("TipoVentaTPV");
	 $IdLocal   = getSesionDato("IdTiendaDependiente");

	 $where     = 
	   " WHERE  IdPresupuesto   = '".$IdPresupuesto."'".
	   " AND Status = 'Pendiente'".
	   " AND TipoVentaOperacion = '".$TipoVenta."'".
	   " AND IdLocal ='".$IdLocal."'";

	 //Cierre Caja
	 if($Opcion=='CleanPreventa')
	   {
	     $where =
	       " WHERE  TipoPresupuesto  = 'Preventa' ".
	       " AND TipoVentaOperacion = '".$TipoVenta."'".
	       " AND Status = 'Pendiente'".
	       " AND IdLocal = '".$IdLocal."'";
	     $Opcion = 'Vencido';
	   }

	 //Ejecuta accion
	 $sql = 
	   " UPDATE ges_presupuestos ".
	   " SET    Status = '".$Opcion."', ".
	   "        FechaAtencion = NOW() ".$where;
	 query($sql);	

	 return 1;

}

function setStatusMProductoTPV($IdMetaProducto,$Opcion){

         $TipoVenta = getSesionDato("TipoVentaTPV");
	 $IdLocal   = getSesionDato("IdTiendaDependiente");
	 $IdUsuario = getSesionDato("IdUsuario"); 	 
	 $sql = 
	   " UPDATE ges_metaproductos ".
	   " SET    Status = '".$Opcion."',".
	   "        FechaEntrega = NOW(),".
	   "        UsuarioAAT = '".$IdUsuario."'".
	   " WHERE  ".
	   //"TipoVentaOperacion = '".$TipoVenta."'".
	   "        IdMetaProducto = '".$IdMetaProducto."'".
	   " AND    IdLocal = '".$IdLocal."'";
	 query($sql);	

	 return 1;
}


function checkndocCompra($idprov,$ndoc){

  $detadoc = getSesionDato('detadoc');
  $tipodoc = $detadoc[0];
  $tcomp   = ($tipodoc=='F')?'Factura':'';
  $tcomp   = ($tipodoc=='R')?'Boleta':$tcomp;
  $tcomp   = ($tipodoc=='G')?'Albaran':$tcomp;
  $tcomp   = ($tipodoc=='SD')?'Ticket':$tcomp;
  
  $sql="SELECT  Codigo 
              FROM  ges_comprobantesprov
              WHERE IdProveedor ='$idprov' 
              AND   Codigo      = '$ndoc' 
              AND   TipoComprobante = '$tcomp' 
              AND   Eliminado = '0' limit 1";

  $row = queryrow($sql);
  if($row)
    return 0;
  else
    return 1;
}

function eliminarPresupuesto($IdPresupuesto){
  $sql = "UPDATE ges_presupuestos ".
         "SET Eliminado = 1 ".
         "WHERE IdPresupuesto = '$IdPresupuesto' ";

  query($sql);
}

function RegistrarMensajePresupuesto($IdAutor,$titulo,$texto,$modo,$toLocal=0,
				     $toUser=0,$diasCaduca=1){
	$titulo = CleanText($titulo);
	$texto	= CleanText($texto);

	//Hora
	$titulo .= " - ".date('M d H:i');

	$sql = "INSERT INTO ges_mensajes (Titulo, Texto, IdAutor, IdLocalRestriccion, IdUsuarioRestriccion, Status, Fecha, DiasCaduca) 
		VALUES 
		('$titulo', '$texto', '$IdAutor', '$toLocal', '$toUser', '$modo', NOW(),'$diasCaduca')";
		
	query($sql);		
}

function ConsolidaDetallePedidosVenta($xid){
               $sql = 
		 " select round(sum(Precio*Cantidad),2) as Total".
		 " from   ges_presupuestosdet ".
		 " where  IdPresupuesto = ".$xid.
		 " and    Eliminado     = '0'";
	       
	       $res = query($sql);
	       $row = Row($res);
	       return ModificaPedidosVenta($xid,
					    'TotalImporte = '.$row["Total"],false,false);
}


?>