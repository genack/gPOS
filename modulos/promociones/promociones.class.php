<?php

      function CrearPromocion($Promocion,$Modalidad,$Tipo,$InicioPeriodo,$FinPeriodo,
			      $MontoActual,$CatCliente,$Producto1,$Producto2,
			      $Descuento,$Bono,$DispLocal,$IdPromocion,$opcion,
			      $Prioridad,$Estado,$TipoVenta){
  
        $IdUsuario  = CleanID(getSesionDato("IdUsuario"));
	$oPromocion = new promocion;
	$tabla      = "ges_promociones";
	$idtabla    = "IdPromocion";

	$oPromocion->set("IdUsuario",$IdUsuario, FORCE);
	$oPromocion->set("IdLocal",$DispLocal,FORCE);
	$oPromocion->set("IdPromocionCliente",$CatCliente, FORCE);
	$oPromocion->set("Descripcion",$Promocion, FORCE);
	$oPromocion->set("FechaInicio",$InicioPeriodo, FORCE);
	$oPromocion->set("FechaFin",$FinPeriodo, FORCE);
	$oPromocion->set("Modalidad",$Modalidad, FORCE);
	$oPromocion->set("MontoCompraActual",$MontoActual, FORCE);
	$oPromocion->set("Tipo",$Tipo, FORCE);
	$oPromocion->set("CBProducto0",$Producto1, FORCE);
	$oPromocion->set("CBProducto1",$Producto2, FORCE);
	$oPromocion->set("Descuento",$Descuento, FORCE);
	$oPromocion->set("Bono",$Bono, FORCE);
	$oPromocion->set("Prioridad",$Prioridad, FORCE);
	$oPromocion->set("Estado",$Estado, FORCE);
	$oPromocion->set("TipoVenta",$TipoVenta, FORCE);
	
	switch($opcion){
	case "Crear":
	  if ($oPromocion->Alta($tabla,$idtabla)) {
	    $id = $oPromocion->get("IdPromocion");
	    return $id;
	  } else
	    return false;
	  break;
	case "Modificar":

	  if ($oPromocion->Modificar($IdPromocion,$tabla,$idtabla)) 
	    return $IdPromocion;
	  else 
	    return false;
	  break;
	}
	
      }

      function CrearPromocionCliente($Categoria,$Descripcion,$MontoDesde,$MontoHasta,
				     $CantidadDesde,$CantidadHasta,$Motivo,
				     $IdPromocionCliente,$opcion,$DispLocal,$EstadoCategoria,
				     $IdHistorialVenta){
  
        $IdUsuario  = CleanID(getSesionDato("IdUsuario"));
	$oPromocion = new promocion;
	$tabla      = "ges_promocionclientes";
	$idtabla    = "IdPromocionCliente";

	$oPromocion->set("IdUsuario",$IdUsuario, FORCE);
	$oPromocion->set("IdLocal",$DispLocal,FORCE);
	$oPromocion->set("CategoriaCliente",$Categoria, FORCE);
	$oPromocion->set("Descripcion",$Descripcion, FORCE);
	$oPromocion->set("DesdeMontoCompra",$MontoDesde, FORCE);
	$oPromocion->set("HastaMontoCompra",$MontoHasta, FORCE);
	$oPromocion->set("DesdeNumeroCompra",$CantidadDesde, FORCE);
	$oPromocion->set("HastaNumeroCompra",$CantidadHasta, FORCE);
	$oPromocion->set("MotivoPromocion",$Motivo, FORCE);
	$oPromocion->set("IdHistorialVentaPeriodo",$IdHistorialVenta, FORCE);
	if($EstadoCategoria == 'Eliminado')
	  $oPromocion->set("Eliminado",1,FORCE);
	else
	  $oPromocion->set("Estado",$EstadoCategoria, FORCE);
	
	switch($opcion){
	case "Crear":
	  if ($oPromocion->Alta($tabla,$idtabla)) {
	    $id = $oPromocion->get("IdPromocionCliente");
	    return $id;
	  } else
	    return false;
	  break;
	case "Modificar":
	  if ($oPromocion->Modificar($IdPromocionCliente,$tabla,$idtabla)) 
	    return $IdPromocionCliente;
	  else 
	    return false;
	  break;
	}
	
      }

      function CrearHistorialVentaPeriodo($HistorialVenta,$HistorialPeriodo,
					  $opcion,$Eliminar){

	$tabla      = "ges_historialventaperiodo";
	$idtabla    = "IdHistorialVentaPeriodo";

	$HistorialVentaPeriodo = 'Últimos '.$HistorialVenta.' meses';

	$oPromocion = new promocion;

	if($Eliminar == 0){
	  $oPromocion->set("Periodo",$HistorialVenta, FORCE);
	  $oPromocion->set("HistorialVentaPeriodo",$HistorialVentaPeriodo, FORCE);
	}
	else
	  $oPromocion->set("Eliminado",$Eliminar, FORCE);
	
	switch($opcion){
	case "Crear":
	  if ($oPromocion->Alta($tabla,$idtabla)) {
	    $id = $oPromocion->get("IdHistorialVentaPeriodo");
	    return $id;
	  } else
	    return false;
	  break;
	case "Modificar":
	  if ($oPromocion->Modificar($HistorialPeriodo,$tabla,$idtabla)) 
	    return $HistorialPeriodo;
	  else 
	    return false;
	  break;
	}
      }


class promocion extends Cursor {
    function productoinformacion() {
    	return $this;
    }

    function Alta($tabla,$idtabla){
	global $UltimaInsercion;
	$data = $this->export();
	//print $data;
	$coma = false;
	$listaKeys = "";
	$listaValues = "";
				
	foreach ($data as $key=>$value){
	  if ($coma) {
	    $listaKeys .= ", ";
	    $listaValues .= ", ";
	  }
	  $listaKeys .= " $key";
	  $listaValues .= "'".$value."'";
	  $coma = true;
	}
		
	$sql = "INSERT INTO $tabla ( $listaKeys ) VALUES ( $listaValues )";
	$res = query($sql,"Alta Promocion");
		
	if ($res) {		
	  $id = $UltimaInsercion;	
	  $this->set($idtabla,$id,FORCE);
	  return $id;			
	}
						
	return false;				 		
    }

    function Modificar($idpromocion,$tabla,$idtabla){
	$data = $this->export();
	$coma = false;
	$str = "";
	
	foreach ($data as $key => $value) {
	  if ($coma)
	    $str .= ",";
	  $value = mysql_escape_string($value);
	  $str .= " $key = '".$value."'";
	  $coma = true;
	}

	$sql = "UPDATE $tabla SET $str ".
	       "WHERE  $idtabla = '$idpromocion'";

	$res = query($sql,"Promoción Modificado");

	if (!$res){
	  $this->Error(__FILE__ . __LINE__, "E: no pudo modificar la Promoción");
	  return false;	
	}		
	return true;				 		
    }

    function getIdPromocion($idpromo){
      $sql = "SELECT IdPromocion ".
             "FROM ges_promociones ".
	     "WHERE IdPromocion = '$idpromo' ".
	     "AND Eliminado = 0";
	   $row = queryrow($sql);
	   return $row["IdPromocion"];
	 }

	 function getPromocion($promo){
	   $sql = "SELECT IdPromocion ".
             "FROM ges_promociones ".
	     "WHERE Descripcion = '$promo' ".
	     "AND Eliminado = 0 ".
	     "ORDER BY IdPromocion DESC ".
	     "LIMIT 1";
	   $row = queryrow($sql);
	   return $row["IdPromocion"];
	 }

	 function getEstadoPromocion($IdPromocion){
	   $sql = "SELECT Estado ".
	     "FROM   ges_promociones ".
	     "WHERE  IdPromocion = '$IdPromocion' ".
	     "AND    Eliminado   = 0";
	   $row = queryrow($sql);
	   return $row["Estado"];
	 }

	 function getIdPromocionCliente($idpromocliente){
	   $sql = "SELECT IdPromocionCliente ".
             "FROM ges_promocionclientes ".
	     "WHERE IdPromocionCliente = '$idpromocliente' ".
	     "AND Eliminado = 0";
	   $row = queryrow($sql);
	   return $row["IdPromocionCliente"];
	 }

	 function getPromocionCliente($promocliente){
	   $sql = "SELECT IdPromocionCliente ".
             "FROM ges_promocionclientes ".
	     "WHERE CategoriaCliente = '$promocliente' ".
	     "AND Eliminado = 0 ".
	     "ORDER BY IdPromocionCliente DESC ".
	     "LIMIT 1";
	   $row = queryrow($sql);
	   return $row["IdPromocionCliente"];
	 }

	 function getMontoCatCliente($MontoDesde,$MontoHasta,$IdPromocionCliente,
				     $Motivo,$IdLocal){
	   $sql = "SELECT DesdeMontoCompra, HastaMontoCompra ".
	     "FROM   ges_promocionclientes ".
	     "WHERE  IdPromocionCliente <> '$IdPromocionCliente' ".
	     "AND    MotivoPromocion = '$Motivo' ".
	     "AND    Estado = 'Ejecucion' ".
     	     "AND    Eliminado = 0 ".
	     "AND    DesdeMontoCompra > 0 ".
	     "AND    HastaMontoCompra > 0 ".
	     "AND    IdLocal IN (0,'$IdLocal') ";

	   $res   = query($sql);
	   $t = 0;
	   $y = 0;

	   while($row = Row($res)){
	     $Desde  = $row["DesdeMontoCompra"];
	     $Hasta  = $row["HastaMontoCompra"];

	     ($MontoDesde >= $Desde  &&  $MontoDesde <= $Hasta)? $t++:$t;
	     ($MontoHasta >= $Desde  &&  $MontoHasta <= $Hasta)? $y++:$y;

	   }

	   $z = $t+$y;
	   return $z;
	 }

	 function getCantidadCatCliente($CantidadDesde,$CantidadHasta,$IdPromocionCliente,
					$Motivo,$IdLocal){
	   $sql = "SELECT DesdeNumeroCompra, HastaNumeroCompra ".
	     "FROM   ges_promocionclientes ".
	     "WHERE  IdPromocionCliente <> '$IdPromocionCliente' ".
	     "AND    MotivoPromocion = '$Motivo' ".
	     "AND    Estado = 'Ejecucion' ".
     	     "AND    Eliminado = 0 ".
	     "AND    DesdeNumeroCompra > 0 ".
	     "AND    HastaNumeroCompra > 0 ".
	     "AND    IdLocal IN (0,'$IdLocal') ";

	   $res   = query($sql);
	   $t = 0;
	   $y = 0;
	   while($row = Row($res)){
	     $Desde  = $row["DesdeNumeroCompra"];
	     $Hasta  = $row["HastaNumeroCompra"];
	     if($CantidadDesde >= $Desde  &&  $CantidadDesde <= $Hasta)
	       $t++;
	     if($CantidadHasta >= $Desde  &&  $CantidadHasta <= $Hasta)
	       $y++;
	   }
	   $z = $t+$y;
	   return $z;
	 }

	 function getIdHistorialVentaPeriodo($HistorialVenta){
	   $sql = "SELECT IdHistorialVentaPeriodo ".
	     "FROM   ges_historialventaperiodo ".
	     "WHERE  Periodo = '$HistorialVenta' ".
	     "AND    Eliminado   = 0";
	   $row = queryrow($sql);
	   return $row["IdHistorialVentaPeriodo"];
	 }

	 function getHistorialVentaPeriodo($HistorialPeriodo){
	   $sql = "SELECT Periodo ".
	     "FROM   ges_historialventaperiodo ".
	     "WHERE  IdHistorialVentaPeriodo = '$HistorialPeriodo' ".
	     "AND    Eliminado   = 0";
	   $row = queryrow($sql);
	   return $row["Periodo"];
	 }

       }

       function checkCodigoBarra($CB){
	 $sql = "SELECT CodigoBarras FROM ges_productos ".
	   "WHERE  CodigoBarras = '$CB' ".
	   "AND    Eliminado    = 0 ".
	   "AND    Obsoleto     = 0";
	 $row = queryrow($sql);
	 if($row)
	   return true;
	 else
	   return false;
       }

       function mostrarPromociones($FiltroLocal,$Desde,$Hasta,$Promocion,
				   $Estado,$Tipo,$MontoCompra,$HistorialCompra,
				   $TipoVenta){
	 // Clean Datos 
	 $desde        = CleanRealMysql($Desde);
	 $hasta        = CleanRealMysql($Hasta);
	 $promocion    = CleanRealMysql($Promocion);

	 // Proveedor 
	 $extraNombre  = ($promocion and $promocion != '')?" AND ges_promociones.Descripcion LIKE '%$promocion%' ":"";

	 //Estado
	 $extraEstado  = ($Estado!='Todos')?" AND ges_promociones.Estado='$Estado' ":"";

	 //Tipo Promocion
	 $extraTipo  = ($Tipo!='Todos')?" AND ges_promociones.Tipo='$Tipo' ":"";

	 //Tipo Venta
	 $extraTipoVenta  = ($TipoVenta != 'Todos')?" AND ges_promociones.TipoVenta='$TipoVenta' ":"";

	 //Fechas: Desde,Hasta 
	 $extraFecha   = " AND date(ges_promociones.FechaRegistro) >= '$desde' AND date(ges_promociones.FechaRegistro) <= '$hasta' ";

	 //Local
	 $extraLocal   = ($FiltroLocal)?" AND ges_promociones.IdLocal IN (0,'$FiltroLocal') ":"";

	 //Modalidad de pago
	 $extraMontoCompra = ($MontoCompra)? "":" AND ges_promociones.Modalidad = 'HistorialCompra' ";
	 $extraHistorialCompra = ($HistorialCompra)? "":" AND ges_promociones.Modalidad = 'MontoCompra' ";

	 $sql = "SELECT IF(ges_promociones.IdLocal = 0,'Todos',(SELECT ges_locales.NombreComercial FROM ges_locales WHERE ges_locales.IdLocal = ges_promociones.IdLocal)) As Local, ".
	   "       ges_usuarios.Identificacion AS Usuario, ".
	   "       ges_promociones.IdPromocion, ".
	   "       IF(ges_promociones.Descripcion like '',' ',ges_promociones.Descripcion) AS Promocion, ".
	   "       CONCAT(DATE_FORMAT(ges_promociones.FechaRegistro,'%d/%m/%Y %H:%i'),'~',ges_promociones.FechaRegistro) AS FechaRegistro, ".
	   "       CONCAT(DATE_FORMAT(ges_promociones.FechaInicio,'%d/%m/%Y %H:%i'),'~',ges_promociones.FechaInicio) AS FechaInicio, ".
	   "       CONCAT(DATE_FORMAT(ges_promociones.FechaFin,'%d/%m/%Y %H:%i'),'~',ges_promociones.FechaFin) AS FechaFin, ".
	   "       ges_promociones.Estado, ".
	   "       ges_promociones.Modalidad, ".
	   "       ges_promociones.MontoCompraActual, ".
	   "       ges_promociones.Tipo, ".
	   "       ges_promociones.IdLocal, ".
	   "       IF(CBProducto0 like '',' ',CBProducto0) AS Producto1, ".
	   "       IF(CBProducto1 like '',' ',CBProducto1) AS Producto2, ".
	   "       ges_promociones.Descuento, ".
	   "       ges_promociones.Bono, ".
	   "       ges_promociones.Prioridad, ".
	   "       IF(ges_promociones.IdPromocionCliente = 0,' ',(SELECT ges_promocionclientes.CategoriaCliente FROM ges_promocionclientes WHERE ges_promocionclientes.IdPromocionCliente = ges_promociones.IdPromocionCliente AND ges_promocionclientes.Eliminado = 0)) AS CategoriaCliente, ".
	   "       ges_promociones.IdPromocionCliente, ".
	   "       ges_promociones.TipoVenta ".
	   "FROM   ges_promociones ".
	   "INNER JOIN ges_usuarios   ON ges_promociones.IdUsuario = ges_usuarios.IdUsuario ".
	   "WHERE ges_promociones.Eliminado = 0 ".
	   "$extraFecha".
	   "$extraNombre".
	   "$extraEstado".
	   "$extraTipo".
	   "$extraTipoVenta".
	   "$extraLocal".
	   "$extraMontoCompra".
	   "$extraHistorialCompra".
	   "ORDER BY ges_promociones.IdPromocion DESC";  
	 $res = query($sql);
	 if (!$res) return false;
	 $Promocion = array();
	 $t = 0;
	 while($row = Row($res)){
	   $nombre   = "Promocion_" . $t++;

	   //Actualiza Estado
	   if ( $row["Estado"] == 'Ejecucion' )
	     $row["Estado"] = verificarEstadoPromocion($row);
	   
	   $Promocion[$nombre] = $row; 		
	 }	
	 return $Promocion;
       }

       function verificarEstadoPromocion($row){

	 $aFecha     = explode("~",$row["FechaFin"]);
	 $Hoy        = strtotime('now');
	 $Fecha      = strtotime($aFecha[1]);
	 $xid        = $row["IdPromocion"];

	 if($Hoy > ($Fecha+86400))  
	   {
	     $sql = "UPDATE ges_promociones SET Estado = 'Finalizado' WHERE IdPromocion = '$xid'";
	     query($sql);

	     
	     if( $row["Tipo"]=='Bono' ) 
	       updateBonoPromocion2Clientes( $xid );//Quita Bono
	       
	     return 'Finalizado';
	   }
	 return 'Ejecucion';
       }

       function mostrarPromocionClientes($IdLocal,$Desde,$Hasta,$Categoria,$Estado){
	 $desde        = CleanRealMysql($Desde);
	 $hasta        = CleanRealMysql($Hasta);
	 $categoria    = CleanRealMysql($Categoria);

	 // Proveedor 
	 $extraCategoria  = ($categoria and $categoria != '')?" AND ges_promocionclientes.CategoriaCliente LIKE '%$categoria%' ":"";

	 //Estado
	 $extraEstado  = ($Estado!='Todos')?" AND ges_promocionclientes.Estado='$Estado' ":"";

	 //Fecha
	 $extraFecha   = " AND date(ges_promocionclientes.FechaRegistro) >= '$desde' AND date(ges_promocionclientes.FechaRegistro) <= '$hasta' ";

	 $sql = "SELECT IF(ges_promocionclientes.IdLocal = 0,'Todos',(SELECT ges_locales.NombreComercial FROM ges_locales WHERE ges_locales.IdLocal = ges_promocionclientes.IdLocal)) As Local, ".
	   "       ges_usuarios.Identificacion AS Usuario, ".
	   "       IdPromocionCliente, ".
	   "       IF(CategoriaCliente like '',' ',CategoriaCliente) AS CategoriaCliente, ".
	   "       IF(Descripcion like '',' ',Descripcion) AS Descripcion, ".
	   "       Estado, ".
	   "       DesdeMontoCompra, ".
	   "       HastaMontoCompra, ".
	   "       DesdeNumeroCompra, ".
	   "       HastaNumeroCompra, ".
	   "       MotivoPromocion, ".
	   "       ges_promocionclientes.IdLocal, ".
	   "       DATE_FORMAT(ges_promocionclientes.FechaRegistro,'%d/%m/%Y %H:%i') as FechaRegistro, ".
	   "       IF(ges_promocionclientes.IdHistorialVentaPeriodo = 0,' ',(SELECT CONCAT(Periodo,'~',HistorialVentaPeriodo) FROM ges_historialventaperiodo WHERE ges_historialventaperiodo.IdHistorialVentaPeriodo = ges_promocionclientes.IdHistorialVentaPeriodo AND ges_historialventaperiodo.Eliminado = 0)) as HistorialVentaPeriodo, ".
	   "       ges_promocionclientes.IdHistorialVentaPeriodo ".
	   "FROM   ges_promocionclientes ".
	   "INNER JOIN ges_usuarios ON ges_promocionclientes.IdUsuario = ges_usuarios.IdUsuario ".
	   "WHERE ges_promocionclientes.Eliminado = 0 ".
	   "AND   ges_promocionclientes.IdLocal IN (0,'$IdLocal') ".
	   "$extraFecha".
	   "$extraCategoria".
	   "$extraEstado".
	   "ORDER BY IdPromocionCliente ASC";  
	 $res = query($sql);
	 if (!$res) return false;
	 $PromocionCliente = array();
	 $t = 0;
	 while($row = Row($res)){
	   $nombre   = "PromocionCliente_" . $t++;
	   $PromocionCliente[$nombre] = $row; 		
	 }	
	 return $PromocionCliente;
       }

       function getPromocionesSyncAlmacen($IdLocalActivo){

         $xlocal    = ($IdLocalActivo)? "0,".$IdLocalActivo:0;
         $TipoVenta = getSesionDato("TipoVentaTPV");

         $sql = 
	   " select IdPromocion,IdPromocionCliente,Descripcion, ".
	   "        Estado,Modalidad,MontoCompraActual, ".
	   "        Tipo,IdLocal,CBProducto0, ".
	   "        CBProducto1,Descuento,Bono,Prioridad, ".
	   "        CONCAT(DATE_FORMAT(FechaFin,'%d/%m/%Y %H:%i'),'~',FechaFin) AS FechaFin".
	   " from   ges_promociones ".
	   " where  Estado    = 'Ejecucion' ".
	   " and    Eliminado = 0 ".
	   " and    TipoVenta = '".$TipoVenta."'".
	   " and    IdLocal   in (".$xlocal.") ".
	   " order  by MontoCompraActual asc";

	 $res = query($sql);
	 if (!$res) return false;
	 $out  = '';
	 $zsrt = 'tPL(';
	 $xsrt = ',';
	 while($row = Row($res))
	   {

	     //Estado...
	     if( verificarEstadoPromocion($row) == 'Finalizado' ) continue;

	     $out .= $zsrt;
	     $out .= $row["IdPromocion"].$xsrt;
	     $out .= $row["IdPromocionCliente"].$xsrt;
	     $out .= "'".$row["Descripcion"]."'".$xsrt;
	     $out .= "'".$row["Modalidad"]."'".$xsrt;
	     $out .= $row["MontoCompraActual"].$xsrt;
	     $out .= "'".$row["Tipo"]."'".$xsrt;
	     $out .= $row["IdLocal"].$xsrt;
	     $out .= $row["CBProducto0"].$xsrt;
	     $out .= $row["CBProducto1"].$xsrt;
	     $out .= $row["Descuento"].$xsrt;
	     $out .= $row["Bono"].$xsrt;
	     $out .= $row["Prioridad"].");";
	   }	

	 return $out;
       }

       function cargarPromocionCliente($id){

         //Historial...
         $ucat = false; 
	 $srt = "";
         $sql =
	   " select IdCliente, ".
	   "        IdHistorialVentaPeriodo as id, ".
	   "        MontoCompra as monto, ".
	   "        NumeroCompra as venta ".
	   " from   ges_historialventas ".
	   " where  IdCliente = ".$id;
	 $res = query($sql);
	 
	 while( $row = Row( $res )) 
	   {
	     //Categoria...
	     $xsql   =
	       " select  IdPromocionCliente,".
	       "         CategoriaCliente,".
	       "         MotivoPromocion, ".
	       "         DesdeMontoCompra as dmonto,".
	       "         HastaMontoCompra as hmonto,".
	       "         DesdeNumeroCompra as dventa, ".
	       "         HastaNumeroCompra as hventa".
	       " from    ges_promocionclientes ".
	       " where   IdHistorialVentaPeriodo = ".$row["id"].
	       " and     Estado = 'Ejecucion'";
	     $xres = query($xsql);

	     while( $xrow = Row( $xres )) 
	       {
		 $esMonto  = false;
		 $esVenta  = false;
		 $esAmbos  = false;
		 $xcat     = $xrow["CategoriaCliente"];
		 $xidcat   = $xrow["IdPromocionCliente"];
		 
		 $esDMonto = ( $row["monto"] >= $xrow["dmonto"] )? true:false;
		 $esHMonto = ( $row["monto"] <= $xrow["hmonto"] )? true:false;
		 $esDVenta = ( $row["venta"] >= $xrow["dventa"] )? true:false;
		 $esHVenta = ( $row["venta"] <= $xrow["hventa"] )? true:false;

		 $esMonto  = ( $esDMonto && $esHMonto )? true:false;
		 $esVenta  = ( $esDVenta && $esHVenta )? true:false;
		 $esAmbos  = ( $esVenta && $esMonto   )? true:false;

		 switch( $xrow["MotivoPromocion"] )
		   {
		   case 'MontoCompra' : if ($esMonto) $ucat .= $srt.$xcat.":".$xidcat; break;
		   case 'NumeroCompra': if ($esVenta) $ucat .= $srt.$xcat.":".$xidcat; break;
		   case 'Ambos'       : if ($esAmbos) $ucat .= $srt.$xcat.":".$xidcat; break;
		   }
		 $srt = ($ucat)? "~":"";
	       }	       
	   }
	 return $ucat;
       }

       function checkCliente2HistorialVenta($idcliente){
	 
         $sql = 
	   " select IdCliente ".
	   " from   ges_historialventas ".
	   " where IdCliente =".$idcliente." limit 1";
	 $row = queryrow($sql);
	 
	 if($row) return true;
	 return false;
       }

       function cargarPeriodosHistorialVenta(){
 	 $sql = 
	   " select IdHistorialVentaPeriodo as id ".
	   " from   ges_historialventaperiodo ".
	   " where  Eliminado = 0";
	 $res = query($sql);
	 return $res;
       }
  
       function cargarVenta2HistorialVenta($idcliente,$importe,$xop,$extra){
	 
	 $periodos  = cargarPeriodosHistorialVenta();
	 $escliente = checkCliente2HistorialVenta($idcliente);
	 $xop       = ( $xop )? '+':'-';
	 while( $periodo = Row($periodos))
	   {

	     if($escliente) 
	       updateVentas2HistorialVenta($idcliente,$importe,$periodo["id"],$xop);
	     else
	       creaCliente2HistorialVenta($idcliente,$importe,$periodo["id"],$xop);

	     updateVenta2Clientes($idcliente,$extra);
	   }
       }

       function updateVentas2HistorialVenta($idcliente,$importe,$xperiodo,$xop){
	 $sql = 
	   " update ges_historialventas ".
	   " set    NumeroCompra = NumeroCompra".$xop."1,".
	   "        MontoCompra  = MontoCompra".$xop.$importe.
	   " where  IdHistorialVentaPeriodo =".$xperiodo.
	   " and    IdCliente =".$idcliente;
	 query($sql);
       }

       function creaCliente2HistorialVenta($idcliente,$importe,$xperiodo){

	 $listaKeys    = "IdCliente,";
	 $listaValues  = "'".$idcliente."',";
	 $listaKeys   .= "IdHistorialVentaPeriodo,";
	 $listaValues .= "'".$xperiodo."',";
	 $listaKeys   .= "MontoCompra,";
	 $listaValues .= "'".$importe."',";
	 $listaKeys   .= "NumeroCompra"; 
	 $listaValues .= "'1'";

	 $sql = "insert into ges_historialventas ( $listaKeys ) VALUES ( $listaValues )";
	 query($sql);
       }

       function checkHistorialVentaPeriodo($idhvperiodo){
	 $sql = 
	   " select count(IdHistorialVentaPeriodo) as cantVentaPeriodo ".
	   " from   ges_promocionclientes ".
	   " where  Eliminado = 0 ".
	   " and    IdHistorialVentaPeriodo = '$idhvperiodo'";
	 $row = queryrow($sql);
	 
	 return $row["cantVentaPeriodo"];

       }

?>