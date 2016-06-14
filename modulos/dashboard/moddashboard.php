<?php
    function syncDashBoard(){
      $id       = getSesionDato("IdTienda");
      $sql      = "select * from ges_dashboard where IdLocal = '$id'";
      $row      = queryrow($sql);
      $esAdmin  = ( Admite("Administracion",false) );
      $Impuesto = getSesionDato("IGV");

      $PrecioTotal        = ( $esAdmin )? $row['v_PrecioTotal']:0;
      $CostoTotal         = ( $esAdmin )? $row['a_CostoTotal']:0;
      $VencPorPagarMonto  = ( $esAdmin )? $row['f_VencPorPagarMonto']:0;
      $VencPorPagar       = ( $esAdmin )? $row['f_VencPorPagar']:0;
      $VencPorCobrarMonto = ( $esAdmin )? $row['f_VencPorCobrarMonto']:0;
      $VencPorCobrar      = ( $esAdmin )? $row['f_VencPorCobrar']:0;
      $PendPorPagarMonto  = ( $esAdmin )? $row['f_PendPorPagarMonto']:0;
      $PendPorPagar       = ( $esAdmin )? $row['f_PendPorPagar']:0;
      $PendPorCobrarMonto = ( $esAdmin )? $row['f_PendPorCobrarMonto']:0;
      $PendPorCobrar      = ( $esAdmin )? $row['f_PendPorCobrar']:0;
      $Promociones        = ( $esAdmin )? $row['v_EjecPromociones']:0;
      $ImpuestoTotal      = ( $esAdmin )? $PrecioTotal-( $PrecioTotal / ((100 + $Impuesto) / 100) ):0;
      $UtilidadTotal      = ( $esAdmin )? $PrecioTotal-$ImpuestoTotal-$CostoTotal:0;
      $ProductosTotal     = ( $esAdmin )? $row['p_Productos']-$row['a_ProductosSinStock']:0;
      
      //js code run to eval
      echo	
	"aDashBoard['ComprobantesBorrador']     = ".$row['c_CmbteBorrador'].";\n".
	"aDashBoard['ComprobantesPendientes']   = ".$row['c_CmbtePendiente'].";\n".
	"aDashBoard['PedidosBorrador']          = ".$row['c_PedidosBorrador'].";\n". 
	"aDashBoard['PedidosPendientes']        = ".$row['c_PedidosPendiente'].";\n". 
	"aDashBoard['Productos']                = ".$row['p_Productos'].";\n". 
	"aDashBoard['Servicios']                = ".$row['p_Servicios'].";\n".
	
	"aDashBoard['PedidosPorRecibir']        = ".$row['a_PedidosPorRecibir'].";\n". 
	"aDashBoard['StockMinimo']              = ".$row['a_StockMinimo'].";\n". 
	"aDashBoard['ProntoVencimiento']        = ".$row['a_ProntoVencimiento'].";\n". 
	"aDashBoard['ProductosSinStock']        = ".$row['a_ProductosSinStock'].";\n". 
	
	"aDashBoard['PendientesServicios']      = ".$row['v_PendServicio'].";\n".
	"aDashBoard['PendientesServiciosMonto'] = ".$row['v_PendServicioMonto'].";\n".
	"aDashBoard['ReservasEntregar']         = ".$row['v_ReservasEntregar'].";\n".
	"aDashBoard['PendientesReservas']       = ".$row['v_PendReservas'].";\n".
	"aDashBoard['PendientesReservasMonto']  = ".$row['v_PendReservasMonto'].";\n".
	"aDashBoard['PendientesCreditos']       = ".$row['v_PendCreditos'].";\n".
	"aDashBoard['PendientesCreditosMonto']  = ".$row['v_PendCreditosMonto'].";\n". 
	"aDashBoard['PendientesPreventas']      = ".$row['v_PendPreventas'].";\n".
	"aDashBoard['PendientesPreventasMonto'] = ".$row['v_PendPreventasMonto'].";\n".
	"aDashBoard['PendientesProformas']      = ".$row['v_PendProformas'].";\n".
	"aDashBoard['PendientesProformasMonto'] = ".$row['v_PendProformasMonto'].";\n".
	"aDashBoard['Promociones']              = ".$Promociones.";\n".
	
	"aDashBoard['PendientePorCobrar']       = ".$PendPorCobrar.";\n".
	"aDashBoard['PendientePorCobrarMonto']  = ".$PendPorCobrarMonto.";\n".
	"aDashBoard['PendientePorPagar']        = ".$PendPorPagar.";\n".
	"aDashBoard['PendientePorPagarMonto']   = ".$PendPorPagarMonto.";\n".
	"aDashBoard['VencidoPorCobrar']         = ".$VencPorCobrar.";\n".
	"aDashBoard['VencidoPorCobrarMonto']    = ".$VencPorCobrarMonto.";\n".
	"aDashBoard['VencidoPorPagar']          = ".$VencPorPagar.";\n".
	"aDashBoard['VencidoPorPagarMonto']     = ".$VencPorPagarMonto.";\n".
	"aDashBoard['UtilidadTotal']            = ".$UtilidadTotal.";\n".
	"aDashBoard['CostoTotal']               = ".$CostoTotal.";\n".
	"aDashBoard['PrecioTotal']              = ".$PrecioTotal.";\n".
	"aDashBoard['ProductosTotal']           = ".$ProductosTotal.";\n".
	"aDashBoard['UtilidadTotal']            = ".$UtilidadTotal.";\n".
	"aDashBoard['ImpuestoTotal']            = ".$ImpuestoTotal.";\n";

    }

    function updateDashBoard($IdLocal = false){
      $IdLocal = (!$IdLocal)? getSesionDato("IdTienda"):$IdLocal;

      // comprobante borrador
      $sql = "SELECT COUNT(IdComprobanteProv) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.EstadoDocumento = 'Borrador' ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND p.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'c_CmbteBorrador',$IdLocal);
      actualizarDataDB($sql,'a_PedidosPorRecibir',$IdLocal);

      // comprobante pendiente
      $sql = "SELECT COUNT(IdComprobanteProv) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.EstadoDocumento = 'Pendiente' ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND p.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'c_CmbtePendiente',$IdLocal);

      // pedidos borrador
      $sql = "SELECT COUNT(IdOrdenCompra) as Data ".
	     "FROM ges_ordencompra ".
	     "WHERE Eliminado = 0 ".
	     "AND Estado = 'Borrador' ".
	     "AND IdLocal = $IdLocal ";

      actualizarDataDB($sql,'c_PedidosBorrador',$IdLocal);

      // pedidos pendiente
      $sql = "SELECT COUNT(IdOrdenCompra) as Data ".
	     "FROM ges_ordencompra ".
	     "WHERE Eliminado = 0 ".
	     "AND Estado = 'Pendiente' ".
	     "AND IdLocal = $IdLocal ";

      actualizarDataDB($sql,'c_PedidosPendiente',$IdLocal);

      // Productos
      $sql = "SELECT COUNT(IdProducto) as Data ".
	     "FROM ges_productos ".
	     "WHERE Eliminado = 0 ".
	     "AND Servicio = 0 ".
	     "AND Obsoleto = 0 ";

      actualizarDataDB($sql,'p_Productos',$IdLocal);
      //return;
      // Servicios
      $sql = "SELECT COUNT(IdProducto) as Data ".
	     "FROM ges_productos ".
	     "WHERE Eliminado = 0 ".
	     "AND Servicio = 1 ".
	     "AND Obsoleto = 0 ";

      actualizarDataDB($sql,'p_Servicios',$IdLocal);

      // productos con stock minimo
      $sql = "SELECT StockMin, Unidades FROM ges_almacenes WHERE IdLocal = $IdLocal ";

      $res = query($sql);
      $data = 0;

      while($row = Row($res)){
	$stockmin = $row["StockMin"];
	if(($stockmin > 0) && ($row["Unidades"] <= $stockmin)){
	  $data = $data+1;
	}
      }

      $data = ($data)? $data:'0';

      actualizarDataDB(false,'a_StockMinimo',$IdLocal,$data);

      // Productos sin stock
      $sql = "SELECT COUNT(Id) as Data ".
	     "FROM ges_almacenes a ".
	     "INNER JOIN ges_productos p ON p.IdProducto = a.IdProducto ".
	     "WHERE p.Eliminado = 0 ".
	     "AND a.Unidades = 0 ".
	     "AND p.Servicio = 0 ".
	     "AND a.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'a_ProductosSinStock',$IdLocal);

      // Costo Total
      $sql = "SELECT SUM(CostoUnitario*Unidades) as Data ".
	     "FROM ges_almacenes ".
	     "WHERE Eliminado = 0 ".
	     "AND Unidades > 0 ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'a_CostoTotal',$IdLocal);

      // Precio Total
      $sql = "SELECT SUM(PrecioVenta*Unidades) as Data ".
	     "FROM ges_almacenes ".
	     "WHERE Eliminado = 0 ".
	     "AND Unidades > 0 ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PrecioTotal',$IdLocal);

      // orden servicio pendientes 
      $sql = "SELECT COUNT(IdOrdenServicio) as Data ".
	     "FROM ges_ordenservicio ".
	     "WHERE Eliminado = 0 ".
	     "AND Estado = 'Pendiente' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendServicio',$IdLocal);

      // orden servicio pendientes Monto
      $sql = "SELECT SUM(Importe) as Data ".
	     "FROM ges_ordenservicio ".
	     "WHERE Eliminado = 0 ".
	     "AND Estado = 'Pendiente' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendServicioMonto',$IdLocal);


      // Reservas por entregar
      $sql = "SELECT COUNT(c.IdComprobante) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.Reservado = 1 ".
	     "AND c.FechaEntregaReserva = '0000-00-00 00:00:00' ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'v_ReservasEntregar',$IdLocal);


      // Reservas pendientes
      $sql = "SELECT COUNT(c.IdComprobante) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.Reservado = 1 ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'v_PendReservas',$IdLocal);

      // Reservas pendientes
      $sql = "SELECT SUM(c.ImportePendiente) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.Reservado = 1 ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'v_PendReservasMonto',$IdLocal);

      // Creditos pendientes
      $sql = "SELECT COUNT(c.IdComprobante) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND c.Status = 1 ".
	     "AND c.SerieComprobante like 'CS%' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'v_PendCreditos',$IdLocal);


      // Creditos pendientes Monto
      $sql = "SELECT SUM(c.ImportePendiente) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND c.Status = 1 ".
	     "AND c.SerieComprobante like 'CS%' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'v_PendCreditosMonto',$IdLocal);

      // pendientes por cobrar
      $sql = "SELECT COUNT(c.IdComprobante) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'f_PendPorCobrar',$IdLocal);


      // pendientes por cobrar Monto
      $sql = "SELECT SUM(c.ImportePendiente) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'f_PendPorCobrarMonto',$IdLocal);

      // pendientes por cobrar vencidos
      $sql = "SELECT COUNT(c.IdComprobante) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
	     "AND c.Cobranza = 'Coactivo' ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'f_VencPorCobrar',$IdLocal);

      // pendientes por cobrar vencidos Monto
      $sql = "SELECT SUM(c.ImportePendiente) as Data ".
	     "FROM ges_comprobantes c ".
	     "INNER JOIN ges_comprobantesnum cn ON cn.IdComprobante = c.IdComprobante ".
	     "INNER JOIN ges_comprobantestipo ct ON ct.IdTipoComprobante = cn.IdTipoComprobante ".
	     "WHERE c.Eliminado = 0 ".
	     "AND cn.Status = 'Emitido' ".
	     "AND ct.TipoComprobante IN ('Factura','Boleta','Albaran','Ticket') ".
	     "AND c.ImportePendiente <> 0 ".
	     "AND c.Cobranza = 'Coactivo' ".
 	     "AND c.IdLocal = $IdLocal ";

      actualizarDataDB($sql,'f_VencPorCobrarMonto',$IdLocal);

      // preventas pendientes 
      $sql = "SELECT COUNT(IdPresupuesto) as Data ".
	     "FROM ges_presupuestos ".
	     "WHERE Eliminado = 0 ".
	     "AND Status = 'Pendiente' ".
	     "AND TipoPresupuesto = 'Preventa' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendPreventas',$IdLocal);

      // preventas pendientes Monto
      $sql = "SELECT SUM(TotalImporte) as Data ".
	     "FROM ges_presupuestos ".
	     "WHERE Eliminado = 0 ".
	     "AND Status = 'Pendiente' ".
	     "AND TipoPresupuesto = 'Preventa' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendPreventasMonto',$IdLocal);

      // proformas pendientes 
      $sql = "SELECT COUNT(IdPresupuesto) as Data ".
	     "FROM ges_presupuestos ".
	     "WHERE Eliminado = 0 ".
	     "AND Status = 'Pendiente' ".
	     "AND TipoPresupuesto = 'Proforma' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendProformas',$IdLocal);

      // proformas pendientes Monto
      $sql = "SELECT SUM(TotalImporte) as Data ".
	     "FROM ges_presupuestos ".
	     "WHERE Eliminado = 0 ".
	     "AND Status = 'Pendiente' ".
	     "AND TipoPresupuesto = 'Proforma' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_PendProformasMonto',$IdLocal);

      // Promociones en ejecucion
      $sql = "SELECT COUNT(IdPromocion) as Data ".
	     "FROM ges_promociones ".
	     "WHERE Eliminado = 0 ".
	     "AND Estado = 'Ejecucion' ".
	     "AND IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'v_EjecPromociones',$IdLocal);

      // pendientes por pagar 
      $sql = "SELECT COUNT(IdComprobanteProv) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND c.EstadoDocumento = 'Pendiente' ".
	     "AND p.IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'f_PendPorPagar',$IdLocal);

      // pendientes por pagar monto
      $sql = "SELECT SUM(ImportePendiente) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND c.EstadoDocumento = 'Pendiente' ".
	     "AND p.IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'f_PendPorPagarMonto',$IdLocal);

      // pendientes por pagar vencidos
      $sql = "SELECT COUNT(IdComprobanteProv) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND c.EstadoDocumento = 'Pendiente' ".
	     "AND c.EstadoPago = 'Vencida' ".
	     "AND p.IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'f_VencPorPagar',$IdLocal);

      // pendientes por pagar vencidos monto
      $sql = "SELECT SUM(ImportePendiente) as Data ".
	     "FROM ges_comprobantesprov c ".
	     "INNER JOIN ges_pedidos p ON p.IdPedido = c.IdPedido ".
	     "WHERE c.Eliminado = 0 ".
	     "AND c.TipoComprobante <> 'AlbaranInt' ".
	     "AND c.EstadoDocumento = 'Pendiente' ".
	     "AND c.EstadoPago = 'Vencida' ".
	     "AND p.IdLocal = $IdLocal ";
      
      actualizarDataDB($sql,'f_VencPorPagarMonto',$IdLocal);

      // Pronto vencimiento
      //$Desde = date("Y-m-d");
      //$nuevafecha = strtotime ( '+30 day' , strtotime ( $Desde ) ) ;
      //$nuevafecha = date ( 'Y-m-d' , $nuevafecha );
      //$Hasta = $nuevafecha;
      //$Estado = 'PorVencer';
      //obtenerVencimientosDashBoard($IdLocal,$Desde,$Hasta,$Estado);

    }

    function actualizarDataDB($sql,$column,$IdLocal,$xdata=false){
      if($sql){
	$row = queryrow($sql);
	$data = ($row["Data"] == "" || !$row )? 0:$row["Data"];
      }else{
	$data = ($xdata == '0' || $xdata >= 0)? $xdata:0;
      }

      $sql = "UPDATE ges_dashboard SET $column  = $data WHERE IdLocal = $IdLocal ";
      query($sql);
    }

    function obtenerVencimientosDashBoard($IdLocal,$Desde,$Hasta,$Estado){
      if( getParametro("Suscripcion") == $Desde ) return;

      $xIdLocal = ($IdLocal == 0)? "":" AND ges_locales.IdLocal = $IdLocal ";

      switch($Estado){
      case 'Vencido':
	$esVencido = true;
	break;
      case 'PorVencer':
	$esVencido = false;
	break;
      }
      
      $sql2 = "SELECT  IdPedidoDet, SUM(CantidadMovimiento) as Saldo ".
	"FROM ges_kardex ".
	"INNER JOIN ges_locales ON ges_kardex.IdLocal = ges_locales.IdLocal ".
	"WHERE ges_kardex.Eliminado = 0 ".
	"$xIdLocal ".
	"GROUP BY IdPedidoDet ".
	"HAVING Saldo > 0 ";
      
      $res2 = query($sql2);
      $iddets = array();
      $iddet = array();
      $vencimiento = Array();
      $t=0;
      
      while($row2 = Row($res2)){
	
	$yid = $row2["IdPedidoDet"];
	
	$iddet[$yid]["id"] = $yid;
	$iddet[$yid]["Saldo"] = $row2["Saldo"];
	
	array_push($iddets,$row2["IdPedidoDet"]);
      }
      
      if(sizeof($iddets) == 0) return;
      
      $data = obtenerVencimientoDashboard($iddets,$esVencido,$Desde,$Hasta,$IdLocal);

      actualizarDataDB(false,'a_ProntoVencimiento',$IdLocal,$data);
    }

    function obtenerVencimientoDashboard($iddets,$esVencido,$Desde,$Hasta,$IdLocal){
      $IdLocal = ($IdLocal == 0)? "":" AND ges_pedidos.IdLocal = $IdLocal ";
      $Hoy = date("Y-m-d");
      $xid = "";
      $xc  = "";
      
      foreach($iddets as $key=>$value){
	$xid = $xid.$xc.$value;
	$xc = ",";
      }

      $extrafecha = ($esVencido)? " AND ges_pedidosdet.FechaVencimiento < '$Hoy'":" AND ges_pedidosdet.FechaVencimiento >= '$Desde' AND ges_pedidosdet.FechaVencimiento <= '$Hasta' ";

      $sql = " SELECT COUNT(ges_pedidos.IdLocal) as Data ".
         " FROM ges_pedidosdet".
         " INNER JOIN ges_pedidos ON ges_pedidosdet.IdPedido = ges_pedidos.IdPedido ".
         " WHERE IdPedidoDet IN ($xid) ".
         " AND ges_pedidosdet.FechaVencimiento <> '0000-00-00'".
         " $IdLocal ".
 	 " $extrafecha ";

      $row     = queryrow($sql);
      
      return  $row["Data"];
    }

?>