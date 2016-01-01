<?php

include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
$IdLocal = getSesionDato("IdTienda");
$modo    = CleanText($_GET["modo"]);
$locales = getLocalesPrecios($IdLocal);
switch($modo) {
  case "mostrarPedidos":
    include("xulpedidosventa.php"); 
    break;
  case "mostrarPedidosVenta":
    $desde       = CleanCadena($_GET["desde"]);
    $hasta       = CleanCadena($_GET["hasta"]);
    $cliente     = CleanText($_GET["cliente"]);
    $presupuesto = CleanText($_GET["filtropresto"]);
    $tipoventa   = CleanText($_GET["filtrotipov"]);
    $estado      = CleanText($_GET["filtroestado"]);
    $codigo      = CleanText($_GET["filtrocodigo"]);
    $usuario     = CleanText($_GET["usuario"]);
    $producto    = CleanText($_GET["producto"]);
    $codigo      = ($codigo)? $codigo:false;
    $local   = getSesionDato("IdTienda");
    if(getSesionDato("esAlmacenCentral"))
      $local = CleanID($_GET["filtrolocal"]);
    
    $datos = PedidosVentaPeriodo($desde,$hasta,$cliente,$presupuesto,$tipoventa,
				 $estado,$local,$codigo,$usuario,$producto);
    
    VolcandoXML( Traducir2XML($datos),"PedidosVenta");
    
    exit();				
    break;

  case "mostrarDetallePedidosVenta":
    $IdPresupuesto = CleanCadena($_GET["xidp"]);
    $local         = CleanID(getSesionDato("IdTienda"));
    
    $datos = DestallePedidosVentaPeriodo($IdPresupuesto,$local);
    
    VolcandoXML( Traducir2XML($datos),"DetallePedidosVenta");
    
    exit();				
    break;

  case "ModificarPedidosVenta":
    $xid    = CleanID($_GET["xid"]); //IdPresupuesto
    $xocs   = CleanID($_GET["xocs"]);
    $xdato  = CleanText($_GET["xdato"]);
    $xidet  = CleanID($_GET["xidet"]);
    $xresto = CleanInt($_GET["resto"]);
    $tipoventa = CleanCadena($_GET["tv"]);

    //echo ModificaPedidosVenta($IdPresupuesto,$xocs);
    
    switch($xocs){
      case 1: // Moficando de Preventa a proforma
	$xdato        = explode("~",$xdato);
	$xlocal       = $xdato[0];
	$IdAutor      = $xdato[1];
	$texto        = $xdato[2];
	$campoxdato   = "TipoPresupuesto='Proforma'";
	$NPresupuesto = obtenerMaxNPresupuesto();
	$NPresupuesto = $NPresupuesto + 1;
	$Vigencia     = CleanInt(getSesionDato("VigenciaPresupuesto"));
	$ModoTPV      = "Pedidos";
	$campoxdato   = $campoxdato.", NPresupuesto = '".$NPresupuesto."'".
	  ", Serie = '".$xlocal."'".", ModoTPV = '".$ModoTPV."'".
	  ", VigenciaPresupuesto = '".$Vigencia."'";
	$IdAutor      = ($IdAutor)? $IdAutor:CleanID(getSesionDato("IdUsuario"));
	$titulo       = "Proforma ".$NPresupuesto;
	$modo         = "Normal";
	
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	RegistrarMensajePresupuesto($IdAutor,$titulo,$texto,$modo,$xlocal,
				    $toUser=0,$diasCaduca=1);
	break;
      case 2:
	$campoxdato = "IdLocal='".$xdato."'";
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	break;
      case 3:
	$campoxdato = "IdCliente='".$xdato."'";
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	break;
      case 4:
	$campoxdato = "VigenciaPresupuesto='".$xdato."'";
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	break;
      case 5:
	$campoxdato = "Observaciones='".$xdato."'";
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	break;
      case 6:
	$data       = explode(",",$xdato);
	$campoxdato = "  Cantidad = '".$data[0].
	  "',Precio   = '".$data[1].
	  "',Importe  = '".$data[0]*$data[1]."' ";
	echo ModificaPedidosVenta($xidet,$campoxdato,true,true);
	echo ConsolidaDetallePedidosVenta($xid);
	break;
      case 7:
	$data       = explode(",",$xdato);
	$campoxdato = "  Cantidad = '".$data[0].
	  "',Precio   = '".$data[1].
	  "',Importe  = '".$data[0]*$data[1]."' ";
	echo ModificaPedidosVenta($xidet,$campoxdato,true,true);
	echo ConsolidaDetallePedidosVenta($xid);
	break;
      case 8:
	$campoxdato = "Eliminado='1'";
	echo ModificaPedidosVenta($xidet,$campoxdato,true,true);
	echo ConsolidaDetallePedidosVenta($xid);
	break;
      case 9:
	$IdLocal    = getSesionDato("IdTienda");
	$mov        = new movimiento;
	$IdArqueo   = $mov->getIdArqueoEsCerradoCaja($IdLocal,$tipoventa);
	$seriep     = "Serie='".$IdArqueo."'";
	$campoxdato = ", Status='".$xdato."'";
	$campoxdato = $seriep.$campoxdato;
	if($xdato == 'Pendiente'){
	  $vigencia   = ", VigenciaPresupuesto = '".$xresto."'";
	  $campoxdato = $campoxdato.$vigencia;
	}
	echo ModificaPedidosVenta($xid,$campoxdato,false,false);
	break;
    }
    exit();
    break;
  case "obtenerNSReservadasPresupuesto":
    $xidproducto    = CleanID($_GET["xidproducto"]); //IdProducto
    $xidlocal       = CleanID($_GET["xidlocal"]); //IdLocal
    echo ";;".obtenerSeriesProductoPresupuesto($xidproducto,$xidlocal).";;";
    exit();
  break;
  case "salvarNSReservadaPresupuesto":
    $xidproducto    = CleanID($_GET["xidproducto"]); //IdProducto
    $xidlocal       = CleanID($_GET["xidlocal"]); //IdLocal
    $xseries        = CleanText($_POST["xdata"]); //IdLocal
    $xidpresupuesto = CleanID($_GET["xpresupuesto"]); //IdPresupuesto

    $nseries   = explode("~", $xseries);//[1]add,[2]del
    $addseries = ( isset( $nseries[1] ) )? explode(",",$nseries[1]): Array();
    $delseries = ( isset( $nseries[2] ) )? explode(",",$nseries[2]): Array();

    //Add
    for($j=0; $j< count($addseries); $j++){

      $pedidodetns  = explode(":", $addseries[$j]);
      if(isset( $pedidodetns[1] ))
	reservaSalidaSeriesPedidoDet($xidproducto,$xidpresupuesto,
				     $pedidodetns[1],$pedidodetns[0]);
      
    }
    //Del
    for($h=0; $h< count($delseries); $h++){

      $pedidodetns  = explode(":", $delseries[$h]);
      if(isset( $pedidodetns[1] ))
	liberarReservaSalidaNumeroSerie($xidproducto,$xidpresupuesto,
					$pedidodetns[1],$pedidodetns[0]);
    }
    echo "1~1";
    exit();
  break;
}

?>


