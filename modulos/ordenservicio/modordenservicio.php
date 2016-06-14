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
  case "mostrarOrdenServicio":
    include("xulordenservicio.php"); 
    break;
  case "CreaOrdenServicio":
    $IdCliente  = CleanID($_GET["xclient"]);
    $Serie      = CleanID($_GET["xserie"]);
    $NumeroOrden= CleanID($_GET["xnumorden"]);
    $IdUsuario  = isset($_GET["xuser"])? CleanID($_GET["xuser"]):0;
    $Prioridad  = CleanID($_GET["xprioridad"]);
    $Tipo       = CleanText($_GET["xtipo"]);
    $IdUsuario  = ($IdUsuario != 0 )? $IdUsuario:getSesionDato('IdUsuario');
    $IdLocal    = CleanID(getSesionDato("IdTiendaDependiente"));
    $IdSuscrip  = CleanID($_GET["xidsuscrip"]);

    echo $id = guardarOrdenServicio($IdLocal,$IdUsuario,$IdCliente,$Serie,$NumeroOrden,
				    $Prioridad,$Tipo,$IdSuscrip);

    break;

  case "CreaOrdenServicioDet":
    $IdOrdenServicio = CleanID($_GET["xidos"]);
    $IdProducto      = CleanID($_GET["xidps"]);
    $FechaInicio     = CleanCadena($_GET["xfinit"]);;
    $FechaFin        = CleanCadena($_GET["xffin"]);;
    $Estado          = CleanText($_GET["xestado"]);
    $UsuarioRes      = CleanID($_GET["xidures"]);
    $Concepto        = CleanText($_GET["xcpto"]);
    $Cantidad        = CleanFloat($_GET["xcant"]);
    $Precio          = CleanFloat($_GET["xprecio"]);
    $Importe         = CleanFloat($_GET["ximpte"]);
    $TipoProducto    = CleanText($_GET["xtipoprod"]);
    $Opcion          = 'Nuevo';
    $IdOrdenServicioDet = (isset($_GET["xidosdet"]))? CleanID($_GET["xidosdet"]):false;
    $NumeroSerie     = (isset($_GET["xns"]))? CleanText($_GET["xns"]):false;
    $CodigoBarras    = (isset($_GET["xcb"]))? CleanText($_GET["xcb"]):false;
    $CodReferencia   = (isset($_GET["xref"]))? CleanText($_GET["xref"]):false;
    $Ubicacion       = (isset($_GET["xubi"]))? CleanText($_GET["xubi"]):false;
    $Direccion       = (isset($_GET["xdir"]))? CleanText($_GET["xdir"]):false;
    $Observacion     = (isset($_GET["xobs"]))? CleanText($_GET["xobs"]):false;
    $CodigoAnterior  = (isset($_GET["xcod"]))? CleanText($_GET["xcod"]):false;

    if($IdOrdenServicio == 0) return;

    echo $id = guardarOrdenServicioDet($IdOrdenServicio,$FechaInicio,$FechaFin,
				       $Estado,$UsuarioRes,$Concepto,$Cantidad,$Precio,
				       $Importe,$IdProducto,$TipoProducto,$IdOrdenServicioDet,
				       false,false,false,$Opcion,$NumeroSerie,
				       $CodigoBarras,$CodReferencia,$Ubicacion,$Direccion,
				       $Observacion,$CodigoAnterior);
    break;

  case "ModificaOrdenServicioDet":
    $IdOrdenServicio = CleanID($_POST["xidos"]);
    $IdOrdenServicioDet = CleanID($_POST["xidosdet"]);
    $FechaInicio     = CleanCadena($_POST["xfinit"]);;
    $FechaFin        = CleanCadena($_POST["xffin"]);;
    $Estado          = CleanText($_POST["xestado"]);
    $UsuarioRes      = CleanID($_POST["xidures"]);
    $Concepto        = CleanText($_POST["xcpto"]);
    $Cantidad        = CleanFloat($_POST["xcant"]);
    $Precio          = CleanFloat($_POST["xprecio"]);
    $Importe         = CleanFloat($_POST["ximpte"]);
    $TipoProducto    = CleanText($_POST["xtipoprod"]);
    $EstadoSolucion  = CleanText($_POST["xstdosol"]);
    $GarantiaCondicion  = CleanID($_POST["xgtiacond"]);
    $EstadoGarantia  = ($GarantiaCondicion == 1)? 'Atendida':'Garantia';
    $Opcion          = "Modifica";
    $NumeroSerie     = (isset($_POST["xns"]))? CleanText($_POST["xns"]):false;
    $Ubicacion       = (isset($_POST["xubi"]))? CleanText($_POST["xubi"]):false;
    $Direccion       = (isset($_POST["xdir"]))? CleanText($_POST["xdir"]):false;
    $Observacion     = (isset($_POST["xobs"]))? CleanText($_POST["xobs"]):false;
    $TieneSat        = CleanID($_POST["xtienesat"]);
  
    $xid = 0;
    if($TieneSat == 1){
      $IdProductoSat = CleanID($_POST["xidpsat"]);
      $Diagnostico   = CleanText($_POST["xdiag"]);
      $Motivo        = CleanText($_POST["xmotivo"]);
      $Resultado     = CleanText($_POST["xresul"]);
      $UbicacionSat  = CleanText($_POST["xubisat"]);
      $Opcion        = 'Modifica';

      $xid = guardarProductoSat(false,false,false,false,false,
				false,$Diagnostico,$Motivo,$Resultado,
				false,$IdProductoSat,$Opcion,$UbicacionSat);       
    }

    $id = guardarOrdenServicioDet($IdOrdenServicio,$FechaInicio,$FechaFin,
				  $Estado,$UsuarioRes,$Concepto,$Cantidad,$Precio,
				  $Importe,false,$TipoProducto,$IdOrdenServicioDet,
				  $EstadoSolucion,$GarantiaCondicion,
				  $EstadoGarantia,$Opcion,$NumeroSerie,false,false,
				  $Ubicacion,$Direccion,$Observacion,false);
    echo '~'.$id.'~'.$xid;
    break;

  case 'QuitaProductoOrdenServicioDet':
    $IdOrdenServicioDet = CleanID($_GET["xidosd"]);
    $IdOrdenServicio    = CleanID($_GET["xidos"]);
    $ImporteOS          = CleanFloat($_GET["ximporte"]);
    $ImpuestoOS         = CleanFloat($_GET["ximpuesto"]);
    quitarProductoOrdenServicio($IdOrdenServicioDet,$IdOrdenServicio,$ImporteOS,$ImpuestoOS);
    break;

  case "CreaModeloSat":
    $IdMarca    = CleanID($_GET["xmarca"]);
    $ModeloSat  = CleanText($_GET["modelo"]);

    echo $id = guardarModeloSat($IdMarca,$ModeloSat);

    break;

  case "CreaProductoIdiomaSat":
    $Producto = CleanText($_GET["xprod"]);
    $oProdSat = new productosat;
    $ProdBase = $oProdSat->getProdBaseSat();
    $IdExiste = $oProdSat->getIdProdSat($Producto);
    if($IdExiste != ''){
      echo $IdExiste.'~'.$ProdBase;
      return;
    }
    $id       = guardarProductoIdiomaSat($Producto,$ProdBase);
    echo '~'.$ProdBase;
    break;

  case "CreaMotivoSat":
    $MotivoSat  = CleanText($_GET["motivo"]);
    echo $id = guardarMotivoSat($MotivoSat);

    break;


  case "ModificaProductoSat":
    $IdProductoSat = CleanID($_POST["xidpsat"]);
    $Marca         = CleanID($_POST["xmarca"]);
    $Modelo        = CleanID($_POST["xmodelo"]);;
    $ProdBase      = CleanID($_POST["xprod"]);;
    $Descripcion   = CleanText($_POST["xdesc"]);
    $NumeroSerie   = CleanText($_POST["xns"]);
    $Diagnostico   = CleanText($_POST["xdiag"]);
    $Motivo        = CleanText($_POST["xmotivo"]);
    $Resultado     = CleanText($_POST["xresul"]);
    $Ubicacion     = CleanText($_POST["xubi"]);
    $Opcion        = 'Modifica';

    echo $id = guardarProductoSat(false,$Marca,$Modelo,$ProdBase,$Descripcion,
				  $NumeroSerie,$Diagnostico,$Motivo,$Resultado,
				  false,$IdProductoSat,$Opcion,$Ubicacion); 
    break;

  case "CreaProductoSat":
    $IdOrdenServicioDet = CleanID($_POST["xidosd"]);
    $Marca         = CleanID($_POST["xmarca"]);
    $Modelo        = CleanID($_POST["xmodelo"]);;
    $ProdBase      = CleanID($_POST["xprod"]);;
    $Descripcion   = CleanText($_POST["xdesc"]);
    $NumeroSerie   = CleanText($_POST["xns"]);
    $Diagnostico   = CleanText($_POST["xdiag"]);
    $Motivo        = CleanText($_POST["xmotivo"]);
    $Resultado     = CleanText($_POST["xresul"]);
    $esDetalle     = CleanText($_POST["xesdet"]);
    $Ubicacion     = CleanText($_POST["xubi"]);
    $Opcion        = 'Nuevo';

    echo $id = guardarProductoSat($IdOrdenServicioDet,$Marca,$Modelo,$ProdBase,
				  $Descripcion,$NumeroSerie,$Diagnostico,$Motivo,
				  $Resultado,$esDetalle,false,$Opcion,$Ubicacion); 
    break;


  case "CreaProductoDetSat":
    $IdProductoSat = CleanID($_GET["xidps"]);
    $Marca         = CleanID($_GET["xmarca"]);
    $Modelo        = CleanID($_GET["xmodelo"]);;
    $ProdBase      = CleanID($_GET["xprod"]);;
    $NumeroSerie   = CleanText($_GET["xnssat"]);
    $Opcion        = 'Nuevo';

    echo $id = guardarProductoDetSat($IdProductoSat,$Marca,$Modelo,$ProdBase,
				     $NumeroSerie,false,$Opcion); 
    break;

  case "ModificaProductoDetSat":
    $IdProductoSatDet = CleanID($_GET["xidpsd"]);
    $Opcion           = 'Modifica';

    echo $id = guardarProductoDetSat(false,false,false,false,
				     false,$IdProductoSatDet,$Opcion); 
    break;

  case "ModificaOrdenServicio":
    $IdCliente  = (isset($_GET["xclient"])  )? CleanID($_GET["xclient"])  : "";
    $Serie      = (isset($_GET["xserie"])   )? CleanID($_GET["xserie"])   : "";
    $NumOrden   = (isset($_GET["xnumorden"]))? CleanID($_GET["xnumorden"]): "";
    $Estado     = (isset($_GET["xestado"])  )? CleanText($_GET["xestado"])  : "";
    $Prioridad  = CleanID($_GET["xprioridad"]);
    $Tipo       = CleanText($_GET["xtipo"]);
    $IdOrdenServicio = CleanID($_GET["xidos"]);

    echo $Id = ModificarOrdenServicio($IdCliente,$Serie,$NumOrden,$IdOrdenServicio,$Estado,
				      $Prioridad,$Tipo);

  case "ObtenerOrdenServicio":

    $FiltroLocal = getSesionDato("IdTiendaDependiente");
    $Desde       = CleanCadena($_GET["xdesde"]);
    $Hasta       = CleanCadena($_GET["xhasta"]);
    $Cliente     = CleanText($_GET["xcliente"]);
    $Estado      = CleanText($_GET["xestado"]);
    $Codigo      = CleanText($_GET["xcodigo"]);
    $Usuario     = CleanID($_GET["xuser"]);
    $Facturacion = CleanText($_GET["xfact"]);
    $Tipo        = CleanText($_GET["xtipo"]);
    $esSoloLocal = trim($FiltroLocal);

    $datos = mostrarOrdenServicio($esSoloLocal,$Desde,$Hasta,$Cliente,
				  $Estado,$Codigo,$Facturacion,$Usuario,$Tipo);
    VolcandoXML( Traducir2XML($datos),"OrdenServicio");
    exit();
    break;

  case "ObtenerOrdenServicioDetalle":
    $IdOrdenServicio = CleanID($_GET["xidos"]);
    $datos = mostrarDetalleOrdenServicio($IdOrdenServicio);
    VolcandoXML( Traducir2XML($datos),"OrdenServicioDet");
    exit();
    break;

  case "ObtenerTipoServicio":
    $datos = genArrayTipoServicio();

    foreach ($datos as $key=>$value) {
      echo "$value=$key\n";
    }		
    break;

  case "ObtnerModeloSat":
    $IdMarca = CleanID($_GET["xidmarca"]);
    $datos   = genArrayModeloSat($IdMarca);

    foreach ($datos as $key=>$value) {
      echo "$value=$key\n";
    }		
    break;

  case "ObtenerSerieNumeroOS":
    echo $cod = ObtenerUltimoNumeroSerieOS();
    break;

  case "VerificaSerieNumeroOS":
    $SerieNum = CleanText($_GET["xserienum"]);
    $IdLocal  = CleanID(getSesionDato("IdTiendaDependiente"));

    switch($SerieNum){
    case 'Serie':
      $Serie    = CleanID($_GET["xserie"]);

      $oOrden  = new ordenservicio;
      $xNumero = $oOrden->getUltimoNumeroOS($Serie);
      echo $Serie.'~'.$xNumero;
      break;

    case 'Numero':
      $Serie    = CleanID($_GET["xserie"]);
      $NumOrden = CleanID($_GET["xnum"]);

      $oOrden   = new ordenservicio;
      $xNum     = $oOrden->getConsultaNumeroOS($NumOrden,$Serie);

      echo $xNum;
      break;

    }

    break;

  case "FacturarOrdenServicio":
    $xid           = CleanID($_GET["xid"]);
    $xlocal        = CleanID($_GET["xlocal"]);
    $xdependiente  = CleanID($_GET["xdependiente"]);
    $IdPresupuesto = facturarOrdenServicio($xid,$xlocal,$xdependiente);
    echo $IdPresupuesto;

    break;	

  case "VerificaProductoOrdenServicioDet":
    $IdProducto = CleanID($_GET["xidps"]);
    $IdOrdenServicioDet = CleanID($_GET["xidosd"]);

    echo $id = VerificarProductoOrdenServicio($IdOrdenServicioDet,$IdProducto);
    break;

  case "ActualizarGarantiaComprobanteDet":
    $IdOrdenServicio  = CleanID($_GET["xidos"]);
    $IdComprobanteDet = CleanID($_GET["xidcd"]);

    ActualizarGarantiaComprobanteDet($IdOrdenServicio,$IdComprobanteDet);
    break;
}

function guardarOrdenServicio($IdLocal,$IdUsuario,$IdCliente,$Serie,$NumeroOrden,
			      $Prioridad,$Tipo,$IdSuscrip){
  $table        = 'ges_ordenservicio';
  $idtable      = 'IdOrdenServicio';
  $oOrden       = new ordenservicio;
  $xNumeroOrden = $oOrden->getUltimoNumeroOS($Serie);
  $NumeroOrden  = ($NumeroOrden < $xNumeroOrden )? $xNumeroOrden:$NumeroOrden;

  $oOrden->set("IdUsuario",$IdUsuario, FORCE);
  $oOrden->set("IdLocal",$IdLocal, FORCE);
  $oOrden->set("IdCliente",$IdCliente, FORCE);
  $oOrden->set("Serie",$Serie,FORCE);
  $oOrden->set("NumeroOrden",$NumeroOrden,FORCE);
  $oOrden->set("Codigo",$Serie.'-'.$NumeroOrden, FORCE);
  $oOrden->set("Prioridad",$Prioridad, FORCE);
  $oOrden->set("Tipo",$Tipo, FORCE);
  $oOrden->set("IdSuscripcion",$IdSuscrip, FORCE);

  if($oOrden->Alta($table,$idtable)){
    $id = $oOrden->get("IdOrdenServicio");
    return $id;
  }

  else
    return false;
}

function guardarOrdenServicioDet($IdOrdenServicio,$FechaInicio,$FechaFin,
				 $Estado,$UsuarioRes,$Concepto,$Cantidad,$Precio,
				 $Importe,$IdProducto,$TipoProducto,$IdOrdenServicioDet,
				 $EstadoSolucion,$GarantiaCondicion,
				 $EstadoGarantia,$Opcion,$NumeroSerie,$CodigoBarras,
				 $CodReferencia,$Ubicacion,$Direccion,$Observacion,
				 $CodigoAnterior){
  $xosEstado = $Estado;
  $table   = 'ges_ordenserviciodet';
  $idtable = 'IdOrdenServicioDet';
  $oOrden  = new ordenservicio;
  

  $Impuesto      = getSesionDato("IGV");
  $xImporteOS    = $oOrden->getImporteOS($IdOrdenServicio);
  $xImporteOSDet = $oOrden->getImporteOSDet($IdOrdenServicioDet);
  $xImpuestoOS   = $oOrden->getImpuestoOS($IdOrdenServicio);

  $FechaHoy      = date('Y-m-d H:i:s');
  $FechaBase     = '0000-00-00 00:00:00';
  $esInicio      = ($FechaInicio == $FechaBase);
  $esFin         = ($FechaFin == $FechaBase);

  switch($Opcion){
  case 'Nuevo':
      //$FechaInicio = ($Estado == 'Ejecucion')? $FechaHoy:$FechaBase;
    $ImporteOS   = $xImporteOS + $Importe;
    break;
  case 'Modifica':
      //$FechaInicio = ($esInicio && $Estado == 'Ejecucion')? $FechaHoy:$FechaInicio;
      //$FechaFin    = ($esFin && $Estado == 'Finalizado')? $FechaHoy:$FechaFin;
    $ImporteOS   = $xImporteOS - $xImporteOSDet + $Importe;
    break;
  }

  $ImpuestoOS = ($ImporteOS*$Impuesto/100);

  ActualizarImporteOrdenServicio($IdOrdenServicio,$ImporteOS,$ImpuestoOS);

  $garantiacomercial = getSesionDato("GarantiaComercial");
  $Garantia          = date('Y-m-d',strtotime('+'.$garantiacomercial.' month'));
  $zImpuesto         = ($Importe*$Impuesto/100);

  $oOrden->set("FechaInicio",$FechaInicio, FORCE);
  $oOrden->set("FechaFin",$FechaFin, FORCE);
  $oOrden->set("Estado",$Estado, FORCE);
  $oOrden->set("IdUsuarioResponsable",$UsuarioRes, FORCE);
  $oOrden->set("Concepto",CleanRealMysql($Concepto), FORCE);
  $oOrden->set("Unidades",$Cantidad, FORCE);
  $oOrden->set("Precio",$Precio, FORCE);
  $oOrden->set("Importe",$Importe, FORCE);
  $oOrden->set("Impuesto",$zImpuesto,FORCE);
  $oOrden->set("TipoProducto",$TipoProducto, FORCE);
  $oOrden->set("Ubicacion",$Ubicacion, FORCE);
  $oOrden->set("Direccion",$Direccion, FORCE);
  $oOrden->set("Observaciones",$Observacion, FORCE);
  if($NumeroSerie) $oOrden->set("NumeroSerie",$NumeroSerie,FORCE);
  if($CodigoAnterior) $oOrden->set("OrdenAnterior",$CodigoAnterior,FORCE);

  switch($Opcion){
  case 'Nuevo':
    $xNumlist = ($IdOrdenServicioDet)? $oOrden->getUltimoNumListServicio($IdOrdenServicioDet):false;
    $Numlist  = $oOrden->getNumListOS($IdOrdenServicio,$TipoProducto,$xNumlist,$IdOrdenServicioDet);
    $oOrden->set("IdOrdenServicio",$IdOrdenServicio, FORCE);
    $oOrden->set("IdProducto",$IdProducto, FORCE);
    $oOrden->set("NumList",$Numlist, FORCE);
    $oOrden->set("Garantia",$Garantia,FORCE);
    $oOrden->set("CodigoBarras",$CodigoBarras,FORCE);
    $oOrden->set("Referencia",$CodReferencia,FORCE);

    if($oOrden->Alta($table,$idtable)){
      $id = $oOrden->get("IdOrdenServicioDet");
    }

    break;

  case 'Modifica':

    $oOrden->set("EstadoSolucion",$EstadoSolucion, FORCE);
    $oOrden->set("GarantiaCondicion",$GarantiaCondicion, FORCE);
    $oOrden->set("EstadoGarantia",$EstadoGarantia, FORCE);

    if($oOrden->Modificar($table,$idtable,$IdOrdenServicioDet)){
      $id = $IdOrdenServicioDet;
      
    }
    
    break;
    
  }

  if($Estado != 'Pendiente')
    $xosEstado = ActualizarEstadoOrdenServicio($IdOrdenServicio);
  return $id.'~'.$xosEstado;

}

function quitarProductoOrdenServicio($IdOrdenServicioDet,$IdOrdenServicio,$ImporteOS,
				     $ImpuestoOS){
  $table   = 'ges_ordenserviciodet';
  $idtable = 'IdOrdenServicioDet';
  $oOrden  = new ordenservicio;

  ActualizarImporteOrdenServicio($IdOrdenServicio,$ImporteOS,$ImpuestoOS);  

  $oOrden->set("Eliminado",1, FORCE);
  $oOrden->Modificar($table,$idtable,$IdOrdenServicioDet);
}

function guardarProductoIdiomaSat($Producto,$ProdBase){
  $table    = 'ges_productosidiomasat';
  $idtable  = 'IdProductoIdiomaSat';
  $oProdSat = new productosat;

  $oProdSat->set("IdProdBaseSat",$ProdBase,FORCE);
  $oProdSat->set("Descripcion",CleanRealMysql($Producto),FORCE);

  if($oProdSat->Alta($table,$idtable)){
    $id = $oProdSat->get("IdProductoIdiomaSat");
    return $id;
  }

  else
    return false;
}

function guardarProductoSat($IdOrdenServicioDet,$Marca,$Modelo,$ProdBase,
			    $Descripcion,$NumeroSerie,$Diagnostico,$Motivo,
			    $Resultado,$esDetalle,$IdProductoSat,$Opcion,$Ubicacion){

  $table     = 'ges_productossat';
  $idtable   = 'IdProductoSat';
  $oProdSat  = new productosat;
  
  $oProdSat->set("Diagnostico",CleanRealMysql($Diagnostico),FORCE);
  $oProdSat->set("IdMotivoSat",$Motivo,FORCE);
  $oProdSat->set("Solucion",CleanRealMysql($Resultado),FORCE);
  $oProdSat->set("Ubicacion",$Ubicacion,FORCE);

  switch($Opcion){
  case 'Nuevo':
    $oProdSat->set("IdOrdenServicioDet",$IdOrdenServicioDet,FORCE);
    $oProdSat->set("IdMarca",$Marca,FORCE);
    $oProdSat->set("IdModeloSat",$Modelo,FORCE);
    $oProdSat->set("IdProdBaseSat",$ProdBase,FORCE);
    $oProdSat->set("Descripcion",CleanRealMysql($Descripcion),FORCE);
    $oProdSat->set("NumeroSerie",CleanRealMysql($NumeroSerie),FORCE);
    if($esDetalle) $oProdSat->set("Detalle",$esDetalle,FORCE);

    if($oProdSat->Alta($table,$idtable)){
      $id = $oProdSat->get("IdProductoSat");
      return $id;
    }

    else
      return false;

    break;

  case 'Modifica':
    if($oProdSat->Modificar($table,$idtable,$IdProductoSat)){
      $id = $IdProductoSat;
      return $id;
    }

    else
      return false;

    break;
  }

}

function guardarProductoDetSat($IdProductoSat,$Marca,$Modelo,$ProdBase,
			       $NumeroSerie,$IdProductoSatDet,$Opcion){
  $table     = 'ges_productossatdet';
  $idtable   = 'IdProductoSatDet';
  $oProdSat  = new productosat;
  
  switch($Opcion){
  case 'Nuevo':
    $oProdSat->set("IdProductoSat",$IdProductoSat,FORCE);
    $oProdSat->set("IdMarca",$Marca,FORCE);
    $oProdSat->set("IdModeloSat",$Modelo,FORCE);
    $oProdSat->set("IdProdBaseSat",$ProdBase,FORCE);
    $oProdSat->set("NumeroSerie",CleanRealMysql($NumeroSerie),FORCE);
    
    if($oProdSat->Alta($table,$idtable)){
      $id = $oProdSat->get("IdProductoSatDet");
      return $id;
    }
    
    else
      return false;
    break;

  case 'Modifica':
    $oProdSat->set("Eliminado",1,FORCE);

    if($oProdSat->Modificar($table,$idtable,$IdProductoSatDet)){
      $id = $IdProductoSatDet;
      return $id;
    }
    
    else
      return false;
    break;
  }
}

function guardarModeloSat($IdMarca,$ModeloSat){
  $table   = 'ges_modelosat';
  $idtable   = 'IdModeloSat';
  $oOrden  = new ordenservicio;
  $oOrden->set("IdMarca",$IdMarca, FORCE);
  $oOrden->set("Modelo",CleanRealMysql($ModeloSat), FORCE);

  if($oOrden->Alta($table,$idtable)){
    $id = $oOrden->get("IdModeloSat");
    return $id;
  }

  else
    return false;
}

function guardarMotivoSat($MotivoSat){
  $table   = 'ges_motivosat';
  $idtable = 'IdMotivoSat';
  $oOrden  = new ordenservicio;
  $oOrden->set("Motivo",CleanRealMysql($MotivoSat), FORCE);
  
  if($oOrden->Alta($table,$idtable)){
    $id = $oOrden->get("IdMotivoSat");
    return $id;
  }
  
  else
    return false;
  
}


function ModificarOrdenServicio($IdCliente,$Serie,$NumOrden,$IdOrdenServicio,$Estado,
				$Prioridad,$Tipo){
  $table     = 'ges_ordenservicio';
  $idtable   = 'IdOrdenServicio';
  $xcampo    = 'NumeroOrden';
  $oOrden    = new ordenservicio;
  $xNumOrden = $oOrden->getConsultaNumeroOS($NumOrden,$Serie);
  $nNumOrden = $oOrden->getNumeroOS($xcampo,$IdOrdenServicio);

  if($xNumOrden == $NumOrden){
    if($NumOrden != $nNumOrden)
      return "0";
    else
      $NumOrden = $nNumOrden;
  }

  $oOrden->set("Serie",$Serie,FORCE);
  $oOrden->set("NumeroOrden",$NumOrden,FORCE);
  $oOrden->set("IdCLiente",$IdCliente,FORCE);
  $oOrden->set("Codigo",$Serie.'-'.$NumOrden,FORCE);
  $oOrden->set("Estado",$Estado,FORCE);
  $oOrden->set("Prioridad",$Prioridad,FORCE);
  
  if($oOrden->Modificar($table,$idtable,$IdOrdenServicio)){
    $id = $IdOrdenServicio;
    if($Estado == 'Cancelado') ModificarEstadoOrdenServicioDetalle($Estado,$IdOrdenServicio);

    if(($Estado == 'Cancelado') && ($Tipo == 'Garantia'))
      ActualizarGarantiaComprobanteDet($IdOrdenServicio,0);

    return $id;
  }
  
  else
    return false;  
}

function ObtenerUltimoNumeroSerieOS(){
  $table    = 'ges_ordenservicio';
  $oOrden   = new ordenservicio;
  
  $Serie    = $oOrden->getUltimoRegistroSerieOS();
  $NumeroOS = $oOrden->getUltimoNumeroOS($Serie);
  $cod      = $Serie."~".$NumeroOS;
  
  return $cod;
}

function ActualizarEstadoOrdenServicio($IdOrdenServicio){
  $table     = 'ges_ordenservicio';
  $idtable   = 'IdOrdenServicio';
  $oOrden    = new ordenservicio;
  $ocEstado  = CheckEstadoFinalizadoOSDetalle($IdOrdenServicio); 

  $oOrden->set("Estado",$ocEstado,FORCE);

  $oOrden->Modificar($table,$idtable,$IdOrdenServicio);
  return $ocEstado;
 }

function ActualizarImporteOrdenServicio($IdOrdenServicio,$ImporteOS,$ImpuestoOS){
  $table     = 'ges_ordenservicio';
  $idtable   = 'IdOrdenServicio';
  $oOrdenx    = new ordenservicio;

  $oOrdenx->set("Importe",$ImporteOS,FORCE);
  $oOrdenx->set("Impuesto",$ImpuestoOS,FORCE);

  if($oOrdenx->Modificar($table,$idtable,$IdOrdenServicio)){
    $id = $IdOrdenServicio;
    return $id;
  }
  
  else
    return false;  
}

function facturarOrdenServicio($xid,$xlocal,$xdependiente){

  $xpresupuesto = ckeckOrdenServicio2Preventa($xid);

  if($xpresupuesto) return "0~".$xpresupuesto;
  
  $xpresupuesto = registrarOrdenServicio2Preventa($xid,$xlocal,$xdependiente);//Pedido
  $aPresupuesto = explode("~",$xpresupuesto);

  registrarOrdenServicio2PreventaDetalle($aPresupuesto[0],$xid);//Pedido Detalle
  
  return "0~".$xpresupuesto;
}

function registrarOrdenServicio2PreventaDetalle($idPresupuesto,$xid){


  //++++++ DETALLE PREVENTA ++++++++++++
  // trae detalle comprobante...
  $sql= 
    " select IdProducto,sum(Unidades) as Unidades,round( SUM(Importe)/sum(Unidades),2) as Precio,".
    "        sum( Importe) as Importe,Concepto,CodigoBarras,Referencia,TipoProducto,NumeroSerie".
    " from   ges_ordenserviciodet ".
    " where  IdOrdenServicio = '".$xid."'".
    " and    Estado <> 'Cancelado' ".
    " and    Eliminado = 0 group by IdProducto ";
  $res = query($sql);
  if (!$res) return false;
  
  while($row = Row($res))
    {
      //servicios repetidos
      $concepto = ( $row['Unidades'] > 1 )? '':$row['Concepto'];

      $Keys    = "IdPresupuesto,";
      $Values  = "'".$idPresupuesto."',";
      $Keys   .= "IdProducto,";
      $Values .= "'".$row['IdProducto']."',";
      $Keys   .= "Cantidad,";
      $Values .= "'".$row['Unidades']."',";
      $Keys   .= "Precio,";
      $Values .= "'".$row['Precio']."',";
      $Keys   .= "Descuento,";
      $Values .= "'0',";
      $Keys   .= "Importe,";
      $Values .= "'".$row['Importe']."',";
      $Keys   .= "Concepto,";
      $Values .= "'".$concepto."',";
      $Keys   .= "Talla,";
      $Values .= "'',";
      $Keys   .= "Color,";
      $Values .= "'',";
      $Keys   .= "Referencia,";
      $Values .= "'".$row['Referencia']."',";
      $Keys   .= "CodigoBarras";
      $Values .= "'".$row['CodigoBarras']."'";

      $sql     = "insert into ges_presupuestosdet (".$Keys.") values (".$Values.")";
      query($sql);   

      //Reservar Series
      //Reserva...
      if( $row["NumeroSerie"] !='' )
	{
	  //IdPedidoDet:unidades:Serie;Serie,IdPedidoDet:uni...
	  $aPedidoDet = explode(",",$row["NumeroSerie"]);
	  
	  foreach ($aPedidoDet as $xrow) 
	    {
	      
	      $axrow       = explode(":", $xrow); 
	      $idpedidodet = $axrow[0];
	      $xnseries    = (isset($axrow[1]))? $axrow[1] : false;
	      
	      //Series...
	      if($xnseries) 
		reservaSalidaSeriesPedidoDet($row['IdProducto'],$idPresupuesto,
					     $xnseries,$idpedidodet);
	    }
	}
    }
}

function registrarOrdenServicio2Preventa($xid,$IdLocal,$xdependiente){

         global $UltimaInsercion;

	 //++++++ PREVENTA ++++++++++++
	 
	 $arqueo       = new movimiento;
	 $IdArqueo     = $arqueo->GetArqueoActivo($IdLocal);
	 $TipoVenta    = getSesionDato("TipoVentaTPV");
	 $textDoc      = 'Preventa';
	 $codDocumento = explode("-",NroComprobantePreVentaMax($IdLocal,$textDoc,$IdArqueo));
	 $sreDocumento = ( $codDocumento[0] != $IdArqueo )? $IdArqueo:$codDocumento[0];
	 $nroDocumento = ( $codDocumento[0] != $IdArqueo )? 1:$codDocumento[1];

	 $oOrden       = new ordenservicio;
	 $oOrden->Load($xid);
	 $ImporteNeto  = round($oOrden->get('Importe') - $oOrden->get('Impuesto'),2);

	 // crea preventa...
	 Global $UltimaInsercion;

	 $Keys    = "IdOrdenServicio,";
	 $Values  = "'".$xid."',";
	 $Keys   .= "IdLocal,";
	 $Values .= "'".$IdLocal."',";
	 $Keys   .= "IdUsuario,";
	 $Values .= "'".$xdependiente."',";
	 $Keys   .= "IdUsuarioRegistro,";
	 $Values .= "'".$xdependiente."',";
	 $Keys   .= "NPresupuesto,";
	 $Values .= "'".$nroDocumento."',";
	 $Keys   .= "TipoPresupuesto,";
	 $Values .= "'".$textDoc."',";
	 $Keys   .= "TipoVentaOperacion,";
	 $Values .= "'".$TipoVenta."',";
	 $Keys   .= "FechaPresupuesto,";
	 $Values .= "NOW(),";
	 $Keys   .= "ImporteNeto,";
	 $Values .= "'".$ImporteNeto."',";
	 $Keys   .= "ImporteImpuesto,";
	 $Values .= "'".$oOrden->get('Impuesto')."',";
	 $Keys   .= "Impuesto,";
	 $Values .= "'".getSesionDato("IGV")."',";
	 $Keys   .= "TotalImporte,";
	 $Values .= "'".$oOrden->get('Importe')."',";
	 $Keys   .= "Status,";
	 $Values .= "'Pendiente',";
	 $Keys   .= "IdCliente,";
	 $Values .= "'".$oOrden->get('IdCliente')."',";
	 $Keys   .= "ModoTPV,";
	 $Values .= "'venta',";
	 $Keys   .= "Serie";
	 $Values .= "'".$sreDocumento."'";
	 $sql     = "insert into ges_presupuestos (".$Keys.") values (".$Values.")";
	 query($sql);

	 //Presupuesto
	 $IdPresupuesto = $UltimaInsercion;  
	 return $IdPresupuesto.'~'.$nroDocumento;
}

?>
