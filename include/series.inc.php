<?php 

        //+++++++++++++++++++++++++++++++++ ALMACEN +++++++++++++++++++++++//

        function obtenerSeriesAlmacenProdBase($idprodbase){

	  $idlocal = getSesionDato("LocalMostrado");
	  $sql     = 
	    " select NumeroSerie ".
	    " from   ges_productos_series ".
	    " inner  join ges_pedidosdet  ".
	    " on     ges_productos_series.DocumentoEntrada = ges_pedidosdet.IdPedidodet ".
	    " inner  join ges_pedidos ".
	    " on     ges_pedidos.IdPedido = ges_pedidosdet.IdPedido ".
	    " inner  join ges_productos ".
	    " on     ges_productos_series.IdProducto = ges_productos.IdProducto ".
	    " where  ges_pedidos.IdLocal             = '".$idlocal."' ".
	    " and    ges_productos.IdProdBase        = '".$idprodbase."' ".
	    " and    ges_productos_series.Estado     = 'Almacen' ".
	    " and    ges_productos_series.Eliminado  = 0";
	  $res = query($sql); 
	  $arr = array();
	  while( $row = Row($res) ){
	    array_push($arr,$row['NumeroSerie']);
	  }
	  return implode($arr,";");
	}


        function obtenerSeriesAlmacenProducto($idproducto){
	  
	  $idlocal = getSesionDato("LocalMostrado");
	  $sql     = 
	    " select NumeroSerie ".
	    " from   ges_productos_series ".
	    " inner  join ges_pedidosdet  ".
	    " on     ges_productos_series.DocumentoEntrada = ges_pedidosdet.IdPedidodet ".
	    " inner  join ges_pedidos ".
	    " on     ges_pedidos.IdPedido = ges_pedidosdet.IdPedido ".
	    " where  ges_pedidos.IdLocal             = '".$idlocal."' ".
	    " and    ges_productos_series.IdProducto = '".$idproducto."' ".
	    " and    ges_productos_series.Estado     = 'Almacen' ".
	    " and    ges_productos_series.Eliminado  = 0";

	  $res = query($sql); 
	  $arr = array();

	  while( $row = Row($res) )
	    {
	      array_push($arr,$row['NumeroSerie']);
	    }

	  return implode($arr,";");
	}

       //+++++++++++++++++++++++++++++++++ VENTAS ++++++++++++++++++++++++//

        function esDisponibleVentaSerie($serie,$idproducto,$idpedidodet){

	  $sql = 
	    " select NumeroSerie ".
	    " from   ges_productos_series ".
	    " where  NumeroSerie      = '".$serie."' ".
	    " and    IdProducto       = '".$idproducto."' ".
	    " and    DocumentoEntrada = '".$idpedidodet."' ".
	    " and    Estado           = 'Almacen' ". 
	    " and    Disponible       = 1 ". 
	    " and    Eliminado        = 0";
	  $res    = query($sql);

	  if($row = Row($res)) 
	    return true;
	  else
	    return false;
	}

        function obtenerSeriesReservadas($iddocumento,$local,$idproducto,$operacion){

	  //Operacion salida? IdPresupuesto:IdMetaProducto
	  $local = ($local)? $local: getSesionDato("IdTiendaDependiente");
	  $sql   = 
	    " select concat(DocumentoEntrada,':',NumeroSerie) as NumeroSerie".
	    " from   ges_productos_series".
	    " inner  join ges_pedidosdet".
	    " on     ges_productos_series.DocumentoEntrada = ges_pedidosdet.IdPedidodet".
	    " inner  join ges_pedidos".
	    " on     ges_pedidos.IdPedido = ges_pedidosdet.IdPedido".
	    " where  ges_pedidos.IdLocal                   = '".$local."'".
	    " and    ges_productos_series.DocumentoSalida  = '".$iddocumento."'".
	    " and    ges_productos_series.OperacionSalida  = '".$operacion."'".
	    " and    ges_productos_series.IdProducto       = '".$idproducto."'".
	    " and    ges_productos_series.Estado           = 'Almacen'".
	    " and    ges_productos_series.Eliminado        = 0";
	  
	  $res = query($sql);
	  $arr = array();

	  while( $row = Row($res) ){
	    array_push($arr,$row['NumeroSerie']);
	  }

	  if( count($arr) > 0 )
	    return implode($arr,"~"); 
	  else 
	    return 0;
	}

        function registraSalidaSeriesPedidoDet($IdProducto,$IdComprobante,
					       $Series,$IdPedidoDet){

	  //Serie,Serie,Serie
	  $nseries = explode(",", $Series);
	  $xset    = "OperacionSalida='Venta',Estado='Salida',Disponible=0,";

	  for($j=0; $j< count($nseries); $j++)
	    {
	      registrarSalidaNumeroSerie($IdProducto,$IdComprobante,$nseries[$j],
					 $IdPedidoDet,$xset);	
	    }
	}

        function registraDevolucionSeriesVenta($IdProducto,$IdComprobante,$IdPresupuesto,$Series){

	  //Serie,Serie,Serie
	  $nseries  = explode(";", $Series);
	  $xset     = "OperacionSalida='Venta',Estado='Almacen',Disponible=1,";
	  $xset    .= "DocumentoSalida=".$IdPresupuesto;
	  for($j=0; $j< count($nseries); $j++)
	    {
	      actualizarSeries2ComprobantesTPV($IdComprobante,$IdProducto,$nseries[$j],$xset);
	    }
	}

        function reservaSalidaSeriesPedidoDet($IdProducto,$IdComprobante,
		 			       $Series,$IdPedidoDet){
	  //Serie;Serie;Serie
	  $nseries = explode(";", $Series);
          $xset    = "OperacionSalida ='Venta',";

	  for($j=0; $j< count($nseries); $j++)
	    {
	      registrarSalidaNumeroSerie($IdProducto,$IdComprobante,$nseries[$j],
					 $IdPedidoDet,$xset);	
	    }

	}

        function reservaSalidaSeriesMProductoDet($IdProducto,$IdComprobante,
						 $Series,$IdPedidoDet){
	  //Serie;Serie;Serie
	  $nseries = explode(";", $Series);
          $xset    = "OperacionSalida ='MetaProducto',";

	  for($j=0; $j< count($nseries); $j++)
	    {
	      registrarSalidaNumeroSerie($IdProducto,$IdComprobante,$nseries[$j],
					 $IdPedidoDet,$xset);	
	    }

	}

 
        function getSeries2IdProductoVentas($IdComprobante,$IdProducto,$IdPedidoDet){
	  
	  $xseries = getSeriesVenta2IdProducto($IdComprobante,$IdProducto,$IdPedidoDet);
	  return implode(';',$xseries);

	}

        function getSeriesVenta2IdProducto($IdComprobante,$IdProducto,$IdPedidoDet=false){
	  
	  $extra = ($IdPedidoDet)? " AND DocumentoEntrada = '$IdPedidoDet' ":"";

	  $sql = "SELECT NumeroSerie ".
	    "FROM   ges_productos_series ".
	    "WHERE  Eliminado = 0 ".
	    "AND    Estado          = 'Salida' ".
	    "AND    IdProducto      = '".$IdProducto."' ".
	    "AND    DocumentoSalida IN (".$IdComprobante.") ".
	    "$extra ".
	    "AND    OperacionSalida <> 'MetaProducto'";
	  $res = query($sql);
	  $arr = array();
	  while( $row = Row($res) ){
	    array_push($arr,$row['NumeroSerie']);
	  }
	  return $arr;
	}

        //+++++++++++++++++++++++++++++++++ KARDEX ++++++++++++++++++++++++//

        function validaNumeroSerie($idproducto,$serie,$idlocal){

	  $sql     = 
	    " select NumeroSerie ".
	    " from   ges_productos_series ".
	    " inner  join ges_pedidosdet  ".
	    " on     ges_productos_series.DocumentoEntrada = ges_pedidosdet.IdPedidodet ".
	    " inner  join ges_pedidos ".
	    " on     ges_pedidos.IdPedido = ges_pedidosdet.IdPedido ".
	    " where  ges_pedidos.IdLocal              = '".$idlocal."' ".
	    " and    ges_productos_series.NumeroSerie = '".$serie."' ".
	    " and    ges_productos_series.IdProducto  = '".$idproducto."' ".
	    " and    ges_productos_series.Eliminado   = 0";

	  $res = query($sql);

	  if($row= Row($res)) 
	    return 1;
	  else
	    return 0;
	}

        function actualizarSeries2PedidoDet($idproducto,$idpedidodet,$xset){
	  
	  $sql = 
	    " update ges_productos_series ".
	    " set    ".$xset.
	    " where  DocumentoEntrada  = '".$idpedidodet."'".
	    " and    IdProducto        = '".$idproducto."'".
	    " and    Eliminado         = '0'";
	  return query($sql);
	}

        function actualizarSeries2PedidoDetSerie($idproducto,$serie,$idpedidodet,$xset){

	  $sql = 
	    " update ges_productos_series ".
	    " set    ".$xset.
	    " where  NumeroSerie       = '".$serie."'".
	    " and    IdProducto        = '".$idproducto."'".
	    " and    DocumentoEntrada  = '".$idpedidodet."'"; 
	  query($sql);
	  
	}

        function actualizarSeries2ComprobantesTPV($IdComprobante,$IdProducto,$xserie,$xset){
	  $sql = 
	    " update ges_productos_series ".
	    " set    ".$xset.
	    " where  NumeroSerie       = '".$xserie."'".
	    " and    IdProducto        = '".$IdProducto."'".
	    " and    Estado            = 'Salida' ".
	    " and    DocumentoSalida   = '".$IdComprobante."'"; 
	  query($sql);
	  
	}

        function registrarSalidaNumeroSerie($IdProducto,$IdComprobante,$Serie,
					    $IdPedidoDet,$xset){
	  $Serie = str_replace(",", "','", $Serie);
	  $sql = 
	    " update ges_productos_series". 
	    " set    ".$xset.
	    "        DocumentoSalida  = '".$IdComprobante."'". 
	    " where  IdProducto       = '".$IdProducto."'".
	    " and    DocumentoEntrada = '".$IdPedidoDet."'".
	    " and    NumeroSerie      in ('".$Serie."')".
	    " and    Eliminado        = 0 ";
	  query($sql);
	}

 

        function ingresarSeriesProductoxPostCompra($id,$nseries,$idpedidodet,$OpEntrada,$SerieVence){

	  $jns   = count($nseries);

	  for($i=0;$i<$jns;$i++)
	    {
	      registrarNumeroSerieExtra($id,$idpedidodet,$nseries[$i],
					$SerieVence,'Pedido',$OpEntrada,'0');
	    }
	  unset($nseries);
	}

        function registrarNumeroSerie( $id, $IdPedido, $IdPedidoDet ){

	  $avisosNS        = null;
	  $detadoc         = getSesionDato("detadoc");
	  $idprodseriecart = getSesionDato("idprodseriebuy");
	  $seriescart      = getSesionDato ("seriesbuy" );

	  for($j=0;$j<count($idprodseriecart);$j++){

	    $idproducto = $idprodseriecart[$j];

	    if( $id == $idproducto ){
	      
	      $lineas = explode(";",$seriescart[$idproducto]);
	      
	      for($i=0;$i<count($lineas);$i++)
		{
		  $linea   = explode(",",$lineas[$i]);
		  $nskey   = "IdProducto";
		  $nsval   = "'".$idproducto."'";
		  $nskey  .= ",NumeroSerie";
		  $nsval  .= ",'".$linea[0]."'";
		  $nskey  .= ",DocumentoEntrada";
		  $nsval  .= ",'".$IdPedidoDet."'";
		  $nskey  .= ",Estado";
		  $nsval  .= ",'Pedido'";
		  $nskey  .= ",Disponible";
		  $nsval  .= ",0";
		  $sql     = "insert into ".
		    "ges_productos_series (".$nskey." ) values (".$nsval.")";
		  query($sql);
		}

	      validaSeriePedidoDet($idproducto,$IdPedidoDet);

	      registrarGarantia($idproducto,$IdPedido);
	      return;
	    }
	  }
	}
 
        function registrarNumeroSerieExtra($id,$IdPedidoDet,$Serie,$SerieVence,
					   $Estado,$Operacion,$Disponible){	  
 	         $nskey   = "IdProducto";
		 $nsval   = "'".$id."'";
		 $nskey  .= ",NumeroSerie";
		 $nsval  .= ",'".$Serie."'";
		 $nskey  .= ",DocumentoEntrada";
		 $nsval  .= ",'".$IdPedidoDet."'";
		 $nskey  .= ",Estado";
		 $nsval  .= ",'".$Estado."'";
		 $nskey  .= ",Disponible";
		 $nsval  .= ",'".$Disponible."'";
		 $nskey  .= ",OperacionEntrada";
		 $nsval  .= ",'".$Operacion."'";
		 $sql     = "insert into ".
		   "ges_productos_series (".$nskey." ) values (".$nsval.")";
		 query($sql);

		 if(!$SerieVence) return;

		 $sql = 
		   "update ges_pedidosdet ".
		   "set    FechaGarantia = '".$SerieVence."' ". 
		   "where  IdProducto    = '".$id."' ".
		   "and    IdPedidoDet   = '".$IdPedidoDet."'";
		 query($sql);

	}

        //+++++++++++++++++++++++++++++++++ COMPRAS ++++++++++++++++++++++++//

        function actualizarSeriesCompra($id,$nseries,$idpedidodet,$unidades,$OpEntrada,$fg){

	  $nseries  = cleanListSeriesProductoxPostCompra($id,$idpedidodet,$nseries);
	  ingresarSeriesProductoxPostCompra($id,$nseries,$idpedidodet,$OpEntrada,$fg);
	  
	}

        function cleanListSeriesProductoxPostCompra($id,$idpedidodet,$nseries){
	  
	  $arrns     = explode(";",$nseries);
	  $jns       = count($arrns);
	  $nseriesdb = obtenerSeriesCompraProducto($id,$idpedidodet,false);
	  $arrnsdb   = explode(";",$nseriesdb);
	  $jnsdb     = count($arrnsdb);

	  actualizarSeries2PedidoDet($id,$idpedidodet,' Eliminado = 1 ');
	  
	  for($i=0;$i<$jns;$i++)
	    {
	      for($j=0;$j<$jnsdb;$j++)
		{
		  if(!isset($arrns[$i])) continue;
		  if($arrns[$i]==$arrnsdb[$j])
		    {
		      actualizarSeries2PedidoDetSerie($id,$arrns[$i],$idpedidodet,' Eliminado = 0');
		      unset($arrns[$i]);
		    }
		}
	    }
	  return array_values($arrns);
	}


        function registrarTrasladoSeries($origen,$destino,$IdPedido,$nwIdPedidoDet,
					 $IdPedidoDet,$idarticulo,$IdProducto,
					 $IdComprobante){

	  $aSeries       = getSesionDato("CarritoMoverSeries");
	  $esSerie       = ( $aSeries[$idarticulo] )? false:true;

	  //Control
	  if($esSerie)   return;

	  $mSeries       = ( !$esSerie )? $aSeries[$idarticulo]:'';
	  $seriesxPedido = explode("~", $mSeries);
	  $Series        = '';
	  $srt           = false;
	  $vernseries    = array();

	  foreach ($seriesxPedido as $nsPedido )
	    {
	      $aPedido = explode(":", $nsPedido);

	      if( $IdPedidoDet ==  $aPedido[0] )
		$Series  = $aPedido[1];

	    }

	  $aSeries    = explode(",",$Series);
	  $nSeries    = count($aSeries);
	  $SerieVence = getGarantiaPedidoDet($IdPedidoDet,$IdProducto); 
	  $xset       = "OperacionSalida='TrasLocal',Estado='Salida',Disponible=0,";
	  for( $i=0; $i < $nSeries ; $i++)
	    {
	      $Serie  = $aSeries[$i];
	      //Ventas	
	      registrarSalidaNumeroSerie($IdProducto,$IdComprobante,
					 $Serie,$IdPedidoDet,$xset);
	      //Compras
	      registrarNumeroSerieExtra($IdProducto,$nwIdPedidoDet,
					$Serie,$SerieVence,
					'Pedido','TrasLocal','0');
	    }

	  //Valida Series

	  validaSeriePedidoDet($IdProducto,$nwIdPedidoDet);

	}

        function registrarAjusteEntradaSeries($IdPedido,$IdPedidoDet,
					      $IdProducto,$Series,$SerieVence){

	  $aSeries = explode(";",$Series);
	  $nSeries = count($aSeries);
	  
	  for( $i=0; $i < $nSeries ; $i++)
	    {
	      $Serie  = $aSeries[$i];
	      //Compras
	      registrarNumeroSerieExtra($IdProducto,$IdPedidoDet,
					$Serie,$SerieVence,
					'Almacen','AjusteExist','1');
	    }

	  //Valida Series

	  validaSeriePedidoDet($IdProducto,$IdPedidoDet);

	}

        function registrarAjusteSalidaSeries($Origen,$IdComprobante,$IdProducto,
					     $Series,$IdPedidoDet){

	  $aSeries = explode(";",$Series);
	  $nSeries = count($aSeries);
	  $xset    = "OperacionSalida='AjusteExist',Estado='Salida',Disponible=0,";	       
	  for( $i=0; $i < $nSeries ; $i++)
	    {
	      registrarSalidaNumeroSerie($IdProducto,$IdComprobante,$aSeries[$i],
					 $IdPedidoDet,$xset);	 
	    }
	  //Valida Series
	}

        function obtenerSeriesCompraProducto($id,$idpedidodet,$almacen=false){

	  $extra = ($almacen)? " and Estado='Almacen' ":"";

	  $sql=
	    " select NumeroSerie ".
	    " from   ges_productos_series ".
	    " where  IdProducto       = '".$id."'".
	    " and    DocumentoEntrada = '".$idpedidodet."'".
	    $extra. 
	    " and    Eliminado='0'";
	  $res = query($sql); 
	  $arr = array();
	  while( $row = Row($res) ){
	    array_push($arr,$row['NumeroSerie']);
	  }
	  return implode($arr,";");
	}

        function obtenerCantidadSeriesCompra($idproducto,$idpedido){

	  $sql=
	    " select count(*) as TotalNS ".
	    " from   ges_productos_series ".
	    " where  IdProducto       = '".$idproducto."' ".
	    " and    DocumentoEntrada = '".$idpedido."' ".
	    " and    Eliminado='0' ";
	  $res = query($sql); 
	  $row = Row($res);
	  return $row['TotalNS'];

	}

        function validaSeriePedidoDet($IdProducto,$IdPedidoDet){

	  $sql    = " select Unidades from ges_pedidosdet ".
	            " where  IdPedidoDet = '".$IdPedidoDet."' ";
	  $row    = queryrow($sql);
	  $cant   = $row["Unidades"];
	  $sql    = " select COUNT(*) as Series from ges_productos_series ".
	            " where  DocumentoEntrada = '".$IdPedidoDet."' ".
	            " and    IdProducto       = '".$IdProducto."'";
	  $row    = queryrow($sql);
	  $scant  = $row["Series"];
	  $status = ( $cant == $scant )? 1:2;
	  $sql    = " update ges_pedidosdet set Serie = '".$status."' ".
	            " where  IdPedidoDet = '".$IdPedidoDet."'";
	  query($sql); 
	}

        function getSerie2PedidoDet($xpedidodet,$xproducto){

	  $srt    = '';
	  $xserie = '';
	  $sql    = 
	    "select NumeroSerie ".
	    "from   ges_productos_series ".
	    "where  DocumentoEntrada = '".$xpedidodet."' ".
	    "and    IdProducto       = '".$xproducto."' ".
	    "and    Estado           = 'Almacen' ".
	    "and    Disponible       = 1 ".
	    "and    Eliminado        = 0 ";
	  $res = query($sql);

	  if (!$res) return false;

	  while($row = Row($res))
	    {
	      $xserie .= $srt.$row["NumeroSerie"];
	      $srt     = ",";
	    }	
	  return $xpedidodet.":".$xserie;
	}

	function getSeriesProductoPedidoDet($IdPedidoDet,$IdProducto){

	  $arr = array();
	  $sql = 
	    "select NumeroSerie ".
	    "from   ges_productos_series ".
	    "where  Eliminado        = 0 ".
	    "and    IdProducto       = '".$IdProducto."'".
	    "and    DocumentoEntrada = '".$IdPedidoDet."'";
	  $res = query($sql);

	  while( $row = Row($res) )
	    {
	      array_push($arr,$row['NumeroSerie']);
	    }
	  return $arr;
	}
         
       //++++++++++++++++++++++++++++++++++++++++ MetaProducto ++++++++++++++++++++//

        function getSeriesMProducto2IdProducto($IdComprobante,$IdProducto){

	  $sql = "SELECT NumeroSerie ".
	    "FROM   ges_productos_series ".
	    "WHERE  Eliminado = 0 ".
	    "AND    Estado          = 'Salida' ".
	    "AND    IdProducto      = '".$IdProducto."' ".
	    "AND    DocumentoSalida = '".$IdComprobante."' ".
	    "AND    OperacionSalida = 'MetaProducto'";
	  $res = query($sql); 
	  $arr = array();
	  while( $row = Row($res) ){
	    array_push($arr,$row['NumeroSerie']);
	  }
	  return $arr;
	}

        function getNSFromMetaProductoDet($IdProducto,$IdComprobante){

	  $ns  = 'N/S:';
	  $sql =
	    " SELECT NumeroSerie ".
	    " FROM   ges_productos_series ".
	    " WHERE  IdProducto      = '".$IdProducto."'".
	    " AND    OperacionSalida = 'Venta'".
	    " AND    DocumentoSalida = '".$IdComprobante."'".
	    " AND    Estado          = 'Salida'".
	    " AND    Eliminado       = '0'";
	  $res=query($sql);
	  while( $row = Row($res) )
	    {
	      $ns = $ns." ".$row['NumeroSerie'];
	    }
	  if(  $ns != 'N/S:' )
	    return $ns;
	}

?>