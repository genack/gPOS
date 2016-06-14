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
  case "CreaSuscripcion":
    $IdTipoSuscrip   = CleanID($_GET["xtiposusc"]);
    $IdCliente       = CleanID($_GET["xclient"]);
    $Serie           = CleanText($_GET["xserie"]);

    echo guardarSuscripcion($IdTipoSuscrip,$IdCliente,$Serie);
       
    break;

  case "ModificaSuscripcion":

    $xitem  = CleanText($_GET["xitem"]);
    $Opcion = CleanText($_GET["xopcion"]);
    $xdato  = CleanText($_GET["xdato"]);
    $IdSuscripcion = CleanID($_GET["xids"]);
    $IdCliente = CleanID($_GET["xclient"]);

    switch($xitem){
    case '1': $campoxdato = "IdTipoSuscripcion='".$xdato."'"; break;
    case '2': $campoxdato = "TipoPago='".$xdato."'"; break;
    case '3': 
      $sdato    = explode("~",$xdato);
      $fechafin = ($sdato[0] == 'Ilimitado')? ",FechaFin='$sdato[0]'":",FechaFin='$sdato[1]'";
      $campoxdato = "Prolongacion='".$sdato[0]."'".$fechafin; break;
    case '4':
      $campoxdato    = "FechaInicio='".$xdato."'";
      $campoxdatodet = "FechaFacturacion='".$xdato."'";
      //Actualizar FechaFacturacionDetalle
      ModificaSuscripcionDet($IdSuscripcion,$campoxdatodet);
      break;
    case '5': $campoxdato = "FechaFin='".$xdato."'";   break;
    case '6': 
      $campoxdato = "Estado='".$xdato."'";
      RegistrarIncidenciasSuscripcion($IdSuscripcion,$xdato);
      break;
    case '7': $campoxdato = "Comprobante='".$xdato."'"; break;
    case '8': $campoxdato = "SerieComprobante='".$xdato."'"; break;
    case '9': $campoxdato = "Observaciones='".$xdato."'"; break;
    case '10': $campoxdato = "IdSubsidiario='".$xdato."'"; break;
    }

    ModificaSuscripcion($IdSuscripcion,$campoxdato);

    #Crea comprobantes
    if($xitem == '6' && $xdato == 'Ejecucion')
      validaSuscripcones2facturar();

    $datos     = mostrarSuscripcionCliente($IdCliente);
    VolcandoXML( Traducir2XML($datos),"suscripciones");

    exit();
    break;

  case "CreaSuscripcionLinea":
    $Concepto      = CleanText($_GET["xconcepto"]);
    $IdProducto    = CleanID($_GET["xidprod"]);
    $Cantidad      = CleanFloat($_GET["xcant"]);
    $Precio        = CleanFloat($_GET["xprecio"]);
    $Descuento     = CleanFloat($_GET["xdscto"]);
    $Importe       = CleanFloat($_GET["ximpte"]);
    $Intervalo     = CleanID($_GET["xintervalo"]);
    $UndIntervalo  = CleanText($_GET["xundinter"]);
    $Estado        = CleanText($_GET["xestado"]);
    $DiaFacturar   = CleanInt($_GET["xdiafacturar"]);
    $AdelantoPlazo = CleanInt($_GET["xadelanto"]);
    $PlazoPago     = CleanInt($_GET["xplazopago"]);
    $IdSuscripcion = CleanID($_GET["xids"]);
    $IdSuscripcionDet = CleanID($_GET["xidsd"]);
    $IdCliente     = CleanID($_GET["xclient"]);
    $Opcion        = ($IdSuscripcionDet == 0)? 'Nuevo':'Modifica';

    $id = guardarSuscripcionLinea($Concepto,$IdProducto,$Cantidad,$Precio,$Descuento,
				  $Importe,$Intervalo,$UndIntervalo,$Estado,
				  $IdSuscripcion,$IdSuscripcionDet,$Opcion,
				  $DiaFacturar,$AdelantoPlazo,$PlazoPago);

    $datos     = mostrarSuscripcionCliente($IdCliente);
    VolcandoXML( Traducir2XML($datos),"suscripciones");

    break;

  case "CreaTipoSuscripcion":

    $TipoSuscripcion = CleanText($_GET["xtiposuscrip"]);;

    $id = guardarTipoSuscripcion($TipoSuscripcion);       
    echo "0~".$id;
    break;

  case "ObtenerSuscripcionCliente":
    $IdCliente = CleanID($_GET["xclient"]);
    $datos     = mostrarSuscripcionCliente($IdCliente);
    VolcandoXML( Traducir2XML($datos),"suscripciones");
    exit();
    break;
}

function guardarSuscripcion($IdTipoSuscrip,$IdCliente,$Serie){

  $table        = 'ges_suscripciones';
  $idtable      = 'IdSuscripcion';
  $oSuscripcion = new suscripciones;
  $IdUsuario    = CleanID(getSesionDato("IdUsuario"));
  $IdLocal      = CleanID(getSesionDato("IdTiendaDependiente"));
  $date         = date('Y-m-d');
  $oSuscripcion->set("IdTipoSuscripcion",$IdTipoSuscrip,FORCE);
  $oSuscripcion->set("IdUsuario",$IdUsuario,FORCE);
  $oSuscripcion->set("IdLocal",$IdLocal,FORCE);
  $oSuscripcion->set("IdCliente",$IdCliente,FORCE);
  $oSuscripcion->set("FechaInicio",$date,FORCE);
  $oSuscripcion->set("SerieComprobante",$Serie,FORCE);

  if($oSuscripcion->Alta($table,$idtable)){
    $id = $oSuscripcion->get("IdSuscripcion");
    return $id;
  }
  
  else
    return false;
}


function guardarSuscripcionLinea($Concepto,$IdProducto,$Cantidad,$Precio,$Descuento,
				 $Importe,$Intervalo,$UndIntervalo,$Estado,
				 $IdSuscripcion,$IdSuscripcionDet,$Opcion,
				 $DiaFacturar,$AdelantoPlazo,$PlazoPago){

  $table   = 'ges_suscripcionesdet';
  $idtable = 'IdSuscripcionDet';
  $oSuscripcion  = new suscripciones;
  $id      = 0;
  $oSuscripcion->set("Intervalo",$Intervalo,FORCE);
  $oSuscripcion->set("UnidadIntervalo",$UndIntervalo,FORCE);
  $oSuscripcion->set("Estado",$Estado,FORCE);
  $oSuscripcion->set("Cantidad",$Cantidad,FORCE);
  $oSuscripcion->set("Precio",$Precio,FORCE);
  $oSuscripcion->set("Descuento",$Descuento,FORCE);
  $oSuscripcion->set("Importe",$Importe,FORCE);
  $oSuscripcion->set("DiaFacturacion",$DiaFacturar,FORCE);
  $oSuscripcion->set("AdelantoPeriodo",$AdelantoPlazo,FORCE);
  $oSuscripcion->set("PlazoPago",$PlazoPago,FORCE);
  $oSuscripcion->set("Concepto",$Concepto,FORCE);
  
  switch($Opcion){
  case 'Nuevo':
    $oSuscripcion->set("IdSuscripcion",$IdSuscripcion,FORCE);
    $oSuscripcion->set("IdProducto",$IdProducto,FORCE);

    if($oSuscripcion->Alta($table,$idtable))
      $id = $oSuscripcion->get("IdSuscripcionDet");
    break;

  case 'Modifica':
    if($oSuscripcion->Modificar($table,$idtable,$IdSuscripcionDet))
      $id = $IdSuscripcionDet;
    break;
  }
  return $id;
}

function guardarTipoSuscripcion($TipoSuscripcion){
  $table = "ges_suscripciontipo";
  $idtable = "IdTipoSuscripcion";

  $oSuscripcion = new suscripciones;
  
  $oSuscripcion->set("TipoSuscripcion",$TipoSuscripcion,FORCE);

  if($oSuscripcion->Alta($table,$idtable)){
    $id = $oSuscripcion->get("IdTipoSuscripcion");
    return $id;
  }
}

?>
