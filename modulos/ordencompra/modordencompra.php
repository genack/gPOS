<?php

include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
$modo    = CleanText($_GET["modo"]);


switch($modo) {
  case "mostrarOrdenCompra":
    $modocontado   = CleanText($_GET["modocontado"]);
    $modocredito   = CleanText($_GET["modocredito"]);
    $filtrocompra  = CleanText($_GET["filtrocompra"]);
    $filtromoneda  = CleanText($_GET["filtromoneda"]);
    $filtrocodigo  = CleanText($_GET["filtrocodigo"]);
    $filtrolocal   = (getSesionDato("esAlmacenCentral"))?CleanID($_GET["filtrolocal"]):getSesionDato("IdTienda");
    $desde         = date("Y-m-d", strtotime( CleanFechaES($_GET["desde"]) ));
    $hasta         = date("Y-m-d", strtotime( CleanFechaES($_GET["hasta"]) ));
    $entrega       = CleanText($_GET["entrega"]);
    $nombre        = CleanText($_GET["nombre"]);
    $esSoloContado = ($modocontado == "contado");
    $esSoloCredito = ($modocredito == "credito");
    $esSoloMoneda  = trim($filtromoneda);
    $esSoloLocal   = trim($filtrolocal);  
    $esSoloCompra  = trim($filtrocompra);  
    $mm            = intval(date("m"));
    $dd            = intval(date("d"));
    $aaaa          = intval(date("Y"));		
    if (!$hasta or $hasta == "") $hasta = "$aaaa-$mm-$dd";
    if (!$desde or $desde == "") $desde = "1900-01-01";
    
    $datos = OrdenCompraPeriodo($filtrolocal,$desde,$hasta,$nombre,$esSoloContado,
				$esSoloCredito,$esSoloMoneda,$esSoloLocal,
				$esSoloCompra,$filtrocodigo,$entrega,0);
    VolcandoXML( Traducir2XML($datos),"PedidosCompra");
    exit();
    break;

  case "mostrarDetallesOrdenCompra":
    $IdOrdenCompra = CleanID($_GET["IdOrdenCompra"]);
    $datos = DetallesOrdenCompra($IdOrdenCompra);
    VolcandoXML( Traducir2XML($datos),"detalles");				
    exit();				
    break;

  case "ModificarOrdenCompra":
    $xid   = CleanID($_GET["xid"]);
    $xidet = CleanID($_GET["xidet"]);
    $xocs  = CleanID($_GET["xocs"]);
    $xdato = CleanText($_GET["xdato"]);
    
    switch($xocs) {
    case 1:
      $campoxdato = "FechaPrevista='".$xdato."'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 2:
      $campoxdato = "FechaPago='".$xdato."'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 3:
      $campoxdato = "Estado='Pendiente'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 4:
      $campoxdato = "FechaPedido=NOW()";
      sModificarOrdenCompra($xid,$campoxdato,false,false);
      $campoxdato = "Estado='Pedido'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 5:
      $campoxdato = "Estado='Cancelado'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 6:
      $campoxdato = "Costo='".$xdato."'";
      echo sModificarOrdenCompra($xidet,$campoxdato,true,true);
      echo ConsolidaDetalleOrdenCompra($xid);
      break;
    case 7:
      $campoxdato = "Unidades='".$xdato."'";
      echo sModificarOrdenCompra($xidet,$campoxdato,true,true);
      echo ConsolidaDetalleOrdenCompra($xid);
      break;
    case 8:
      $campoxdato = "Eliminado='1'";
      echo sModificarOrdenCompra($xidet,$campoxdato,true,true);
      echo ConsolidaDetalleOrdenCompra($xid);
      break;
    case 9:
      echo sConsolidarOrdenesCompra($xid,$xdato);
      break;
    case 10:
      echo EditarOrdenCompra($xid,'O',false);
      break;
    case 11:
      echo EditarOrdenCompra($xid,'F',false);
      break;
    case 12:
      echo EditarOrdenCompra($xid,'R',false);
      break;
    case 13:
      echo EditarOrdenCompra($xid,'G',false);
      break;
    case 14:
      echo EditarOrdenCompra($xid,'SD',false);
      break;
    case 20:
      echo EditarOrdenCompra($xid,'O',true);
      break;
    case 15:
      $campoxdato = "Observaciones = concat(Observaciones,'- ','".$xdato."')";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 16:
      $campoxdato = "Estado='Borrador'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 17:
      $campoxdato = "IdProveedor=".$xdato;
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 18:
      $campoxdato = "ModoPago='".$xdato."'";
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    case 19:
      $campoxdato = "IdLocal=".$xdato;
      echo sModificarOrdenCompra($xid,$campoxdato,false,false);
      break;
    }
    exit();
    break;

}
include("xulordencompra.php");
?>


