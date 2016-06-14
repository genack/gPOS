<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",2);

$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);

$modo   = CleanText($_GET["modo"]);
$Moneda = getSesionDato("Moneda");

switch($modo) {
  case "verPagosProveedor":
    $xval  = 'rPagos';
    $tcbte = 'Comprobantes Proveedor';
    include("xulpagosproveedor.php"); 		
    break;
  case "verCobrosCliente":
    $xval  = 'rCobros';
    $tcbte = 'Cobros';
    include("xulpagosproveedor.php"); 		
    break;
  case "verPagosProveedorDocComprobantes":
    //$btnVolver     = true;
  case "verPagosProveedorDoc":
    $idordencompra = (isset($_GET["xorden"]))? CleanID($_GET["xorden"]):0;
    $idproveedor   = (isset($_GET["xprov"]))? CleanID($_GET["xprov"]):0;
    $idmoneda      = (isset($_GET["xidm"]))? CleanID($_GET["xidm"]):1;
    $importeoc     = (isset($_GET["ximpoc"]))? CleanFloat($_GET["ximpoc"]):0;
    $cambiomoneda  = (isset($_GET["xcm"]))? CleanFloat($_GET["xcm"]):1;
    $cmodo         = (isset($_GET["xmodo"]))? CleanText($_GET["xmodo"]):'comprdoc';//pedidoc
    $cmodo         = ($cmodo)?$cmodo:'comprdoc';
    $idproveedor   = ($idproveedor)? $idproveedor:1;
    $proveedor     = getNombreProveedor($idproveedor);
    $blockprov     = ($cmodo == 'pedidoc')? 'true':'false';
    $btnVolver     = (isset($btnVolver))? 'false':'true';
	  
    //$blocktipoprov = ($cmodo == 'pedidoc')? 
    $initDocumento = ($cmodo == 'pedidoc')? 'PagoDocumento("Nuevo")':'VerPagoDocumento()';
    include("xulpagosproveedordoc.php");
    break;
    
  case "verCobrosCliente":
    include("xulpagosproveedor.php"); 		
    break;
  case "verCobrosClienteCorp":
    include("xulpagosproveedor.php"); 		
    break;

  case "mostrarDetallesPago":
    $IdComprobanteProv = CleanText($_GET["IdComprobanteProv"]);
    $datos    = DetallesPago($IdComprobanteProv);
    VolcandoXML( Traducir2XML($datos),"detallespago");
    exit();				
    break;

  case "mostrarDocumento":
    $iddoc = CleanID($_GET["iddoc"]);
    $datos = PagoDocumentoPeriodo(false,false,false,false,false,false,
				  false,$iddoc);
    VolcandoXML( Traducir2XML($datos),"PagosDoc");

    exit();				
    break;

  case "ModificaPago":
    $idpagoprov    = CleanID($_GET["idpprov"]);
    $idcomprobante = CleanID($_GET["idcbte"]);
    $xdoc          = CleanText($_GET["xdoc"]);
    $Opcion        = (isset($_GET["Opcion"]))? CleanInt($_GET["Opcion"]):false;
    
    $IdUsuario     = CleanID(getSesionDato("IdUsuario"));
    $Documento     = (isset($_GET["xdesc"]))? CleanText($_GET["xdesc"]):false;;
    $Obs           = (isset($_GET["xobs"]))? CleanText($_GET["xobs"]):false;

    switch($xdoc){
      case "Eliminar":
	$Eliminar   = CleanInt($_GET["xeliminar"]);
	$Importe    = CleanFloat($_GET["xgimporte"]);
	$IdPagoDoc  = CleanID($_GET["iddoc"]);
	
	$id = ModificaPago($idpagoprov,$idcomprobante,$IdUsuario,$xdoc,$Eliminar,
			   false,false,$IdPagoDoc,$Importe,false,false,false,false,false,
			   false,false,0);
	break;
      case "Modificar":
	if($Opcion == 1){
	  $IdPagoDoc     = CleanID($_GET["xidpd"]);
	  $Importe       = CleanFloat($_GET["ximp"]);
	  $Mora          = CleanFloat($_GET["xmora"]);
	  $IdMoneda      = CleanID($_GET["xidm"]);
	  $Desviacion    = CleanFloat($_GET["xdes"]);
	  $EstadoPago    = CleanText($_GET["xsp"]);
	  $idcomprobante = CleanID($_GET["idcbte"]);
	  $IdLocal       = CleanID($_GET["xlocal"]);
	  $TipoDif       = CleanText($_GET["xtipodif"]);
	  $tipoprov      = CleanText($_GET["xtipoprov"]);
	  $esagregar     = CleanText($_GET["xesagregar"]);
	  $cambiomoneda  = CleanFloat($_GET["xcambiocbte"]);
	  $IdProveedor   = CleanID($_GET["xprov"]);
	  
	  
	  $mov           = new movimientogral;
	  $IdArqueo      = $mov->getIdArqueoEsCerrado($IdMoneda,$IdLocal);
	  $estado        = (!$IdArqueo)? "Borrador":"Pendiente";
	  $IdArqueo      = (!$IdArqueo)? 0:$IdArqueo;
	  $modalidadpago = '9';
	  $fechaoperacion= date("Y-m-d H:m:s");
	  $TipoMoneda    = $IdMoneda;
	  
	  $pago          = new pago;
	  $idpagodoc     = $pago->getIdPagoDoc($idcomprobante);
	  $xImportePlan  = $pago->getImportePlan($idcomprobante);
	  $xImporteCbte  = $pago->getImporteComprobante($idcomprobante);

	  switch($esagregar){
	    case 'asociar':
	      if($idpagodoc == $IdPagoDoc){
		echo 'completa';
		break;
	      }
	      /*
	      if($TipoDif == 'Excedente')
		CrearPagoDocumento($IdProveedor,$ordencompra,$modalidadpago,
				   $fechaoperacion,$codigooperacion,$nrodocumento,
				   $cuentaproveedor,$cuentaempresa,
				   $TipoMoneda,$cambiomoneda,$Mora,$obs,$IdLocal,
				   $IdUsuario,$estado,$IdArqueo,$tipoprov);
	      */
	      break;
	    case 'planificar':
	      if($xImportePlan >= $xImporteCbte){
		echo 'completa';
		break;
	      }
	      break;
	  }
	  
	  $exceso = ($TipoDif == 'Excedente')? $Mora:0;
	  $Mora   = ($TipoDif == 'Mora')? $Mora:0;
	  
	  $id = ModificaPago($idpagoprov,$idcomprobante,$IdUsuario,$xdoc,false,
			     $IdMoneda,$Opcion,$IdPagoDoc,$Importe,$Mora,false,
			     false,$Documento,$Obs,$Desviacion,$EstadoPago,$exceso);
	}
	
	if($Opcion == 2){
	  $ImportePlan   = CleanDinero($_GET["xiplan"]);
	  $FechaPagoPlan = CleanCadena($_GET["xfplan"]);
	  $IdMoneda      = CleanID($_GET["xidm"]);

	  $id = ModificaPago($idpagoprov,$idcomprobante,$IdUsuario,$xdoc,false,
			     $IdMoneda,$Opcion,false,false,false,$ImportePlan,
			     $FechaPagoPlan,$Documento,$Obs,false,false,false);
	}
	break;
    }
    if ($id)		
      echo "$id";
    else
      echo "0";
	       
    exit();
    break;

  case "ActualizaPendienteComprobante":
    
    $idcomprobante = CleanID($_POST["xidcp"]);
    $pendiente     = (isset($_POST["xpte"]))? CleanFloat($_POST["xpte"]):'';
    $totalcbte     = CleanFloat($_POST["xtcbte"]);
    $estadodoc     = CleanText($_POST["xedc"]);
    $importepago   = (isset($_POST["ximp"]))? CleanFloat($_POST["ximp"]):'';
    $importeant    = (isset($_POST["ximpant"]))? CleanFloat($_POST["ximpant"]):'';
    $xdoc          = CleanText($_POST["xdoc"]);
    $IdPagoDoc     = CleanID($_POST["xidpd"]);
    $estadopago    = (isset($_POST["xstdopago"]))? CleanText($_POST["xstdopago"]):'';
    $esAgregar     = (isset($_POST["xop"]))? CleanText($_POST["xop"]):'';
    
    $pago          = new pago;
    $xImportePago  = $pago->getImportePagada($idcomprobante);
    $xImporteCbte  = $pago->getImporteComprobante($idcomprobante);
    $xPendiente    = $pago->getImportePendiente($idcomprobante);
    
    $pagodoc       = new pagodoc;
    $xImporteDoc   = $pagodoc->getImportePagoDoc($IdPagoDoc);
    
    if($xdoc != 'Eliminar' && $xImporteCbte >= $xImportePago ){
      if($xPendiente==0){
	echo "completa";
	break;
      }
    }
    
    $pendiente = $xImporteCbte - $xImportePago;
    
    if($xdoc == 'Modificar'){
      $importeant  = ($importepago == $importeant)? 0:$importeant;
      $pendiente   = ($estadopago=='Confirmado')?$xPendiente-$xImporteDoc + $importeant:$xPendiente-$xImporteDoc;
    }
    
    if($xdoc == 'Eliminar')
      $pendiente   = $xImporteCbte-$xImportePago;
    
    $pendiente     = ($pendiente < 0)? 0 : $pendiente;
    $estadodoc     = ($estadodoc)? ",EstadoDocumento='".$estadodoc."'":"";
    $estado        = ($pendiente != 0)? "'Empezada' ":"'Pagada' ";
    $estado        = ($pendiente == $totalcbte)?"'Pendiente' ":$estado;
    $estado        = "EstadoPago =  ".$estado;
    $campoxdato    = $estado.$estadodoc;
    $campoxdato    = $campoxdato.",ImportePendiente = '".$pendiente."'";
    
    echo sModificarCompra($idcomprobante,$campoxdato,false,false);
    
    break;

  case "ActualizaEstadoPagoDoc":
    $idpagodoc    = CleanID($_POST["xidppd"]);
    $estado       = CleanText($_POST["xestado"]);

    $id  = ActualizaEstadoPagoDoc($idpagodoc,$estado);
    
    if ($id)		
      echo "$id";
    else
      echo "0";
    
    exit();
    break;

  case "mostrarPagoDocumento":
    $filtromoneda    = CleanText($_GET["filtromoneda"]);
    $filtrolocal     = getSesionDato("IdTienda");
    if(getSesionDato("esAlmacenCentral"))
      $filtrolocal   = CleanText($_GET["filtrolocal"]);
    $desde           = date("Y-m-d", strtotime( CleanFechaES($_GET["desde"]) ));
    $hasta           = date("Y-m-d", strtotime( CleanFechaES($_GET["hasta"]) ));
    $nombre          = CleanText($_GET["nombre"]);
    $filtroestado    = CleanText($_GET["filtroestado"]);
    $filtromodalidad = CleanID($_GET["filtromodalidad"]);
    $esSoloMoneda    = trim($filtromoneda);
    $esSoloLocal     = trim($filtrolocal);
    
    $mm            = intval(date("m"));
    $dd            = intval(date("d"));
    $aaaa          = intval(date("Y"));		
    if (!$hasta or $hasta == "") $hasta = "$aaaa-$mm-$dd";
    if (!$desde or $desde == "") $desde = "1900-01-01";
    
    $datos = PagoDocumentoPeriodo($desde,$hasta,$nombre,
				  $esSoloMoneda,$filtroestado,
				  $filtromodalidad,$esSoloLocal,false);
    VolcandoXML( Traducir2XML($datos),"PagosDocumento");
    
    exit();
    break;

  case "AltaPagoDocumento":
    $provhab           = CleanID($_POST["xidp"]);
    $ordencompra       = CleanID($_POST["xidoc"]);
    $modalidadpago     = CleanID($_POST["xidmp"]);
    $fechaoperacion    = CleanCadena($_POST["xfp"]);
    $codigooperacion   = CleanText($_POST["xco"]);
    $nrodocumento      = CleanText($_POST["xnd"]);
    $cuentaproveedor   = CleanID($_POST["xcp"]);
    $cuentaempresa     = CleanID($_POST["xce"]);
    $tipomoneda        = CleanID($_POST["xidm"]);
    $cambiomoneda      = CleanFloat($_POST["xcm"]);
    $importe           = CleanDinero($_POST["ximp"]);
    $obs               = CleanText($_POST["xobs"]);
    $idlocal           = CleanID($_POST["xidl"]);
    $xestado           = CleanText($_POST["estado"]);
    $IdUsuario         = CleanID(getSesionDato("IdUsuario"));
    $LocalActual       = CleanID(getSesionDato("IdTienda"));
    $idlocal           = ($idlocal=='false')? $LocalActual : $idlocal; 
    $tipoprov          = CleanText($_POST["tipoprov"]);
    $cambiodivisa      = CleanText($_POST["cambiodivisa"]); 

    $fechaoperacion    = ($fechaoperacion=='')? false:date("Y-m-d H:i:s", strtotime($fechaoperacion));

    $mov               = new movimientogral;
    $IdArqueo          = $mov->getIdArqueoEsCerrado($tipomoneda,$idlocal);
    $estado            = (!$IdArqueo)? "Borrador":$xestado;
    $IdArqueo          = (!$IdArqueo)? 0:$IdArqueo;
    $esRegistroCaja    = false;

    if($tipomoneda != 1 && $cambiodivisa == '1'){
        $CodPartida = 'S125';
        $IdMoneda = $tipomoneda;
        $IdMonedaCambio = $IdMoneda;//($IdMoneda == 1)? 2:1;
        $IdArqueoM  = $mov->getIdArqueoEsCerrado($IdMonedaCambio,$idlocal);
        if(!$IdArqueoM){ 
		    echo "~1~"; // Caja de moneda destino está cerrada;
		    return false;
        }
    }

    $id = CrearPagoDocumento($provhab,$ordencompra,$modalidadpago,$fechaoperacion,
			     $codigooperacion,$nrodocumento,$cuentaproveedor,
			     $cuentaempresa,$tipomoneda,
			     $cambiomoneda,$importe,$obs,$idlocal,$IdUsuario,
                             $estado,$IdArqueo,$tipoprov,$cambiodivisa);

    if($IdArqueo == 0){
      if($xestado == "Pendiente")
	$esRegistroCaja =  true;
    }

    if($esRegistroCaja && $id)
      echo "~0~";

    if(!$esRegistroCaja && $id)
      echo "~~".$id;

    exit();
    break;

  case "ModificaPagoDocumento":
    $provhab           = CleanID($_POST["xidp"]);
    $modalidadpago     = CleanID($_POST["xidmp"]);
    $fechaoperacion    = CleanCadena($_POST["xfo"]);
    $codigooperacion   = CleanText($_POST["xco"]);
    $nrodocumento      = CleanText($_POST["xnd"]);
    $cuentaproveedor   = CleanText($_POST["xcp"]);
    $cuentaempresa     = CleanText($_POST["xce"]);
    $tipomoneda        = CleanID($_POST["xidm"]);
    $cambiomoneda      = CleanFloat($_POST["xcm"]);
    $importe           = CleanDinero($_POST["ximp"]);
    $obs               = CleanText($_POST["xobs"]);
    $estado            = CleanText($_POST["xstdo"]);
    $idoc              = CleanID($_POST["xidppd"]);
    $idlocal           = CleanID($_POST["xidl"]);
    $cEstado           = CleanText($_POST["cestado"]);

    $IdUsuario         = CleanID(getSesionDato("IdUsuario"));
    $LocalActual       = CleanID(getSesionDato("IdTienda"));
    $idlocal           = ($idlocal=='false')? $LocalActual : $idlocal;
    $cambiodivisa      = CleanText($_POST["cambiodivisa"]); 


    $fechaoperacion    = ($fechaoperacion=='')? false:date("Y-m-d H:i:s", strtotime($fechaoperacion));

    $mov               = new movimientogral;
    $IdArqueo          = $mov->getIdArqueoEsCerrado($tipomoneda,$idlocal);
    $estado            = (!$IdArqueo)? "Borrador":$estado;
    $IdArqueo          = (!$IdArqueo)? 0:$IdArqueo;

    if($tipomoneda != 1 && $cambiodivisa == '1'){
        $CodPartida = 'S125';
        $IdMoneda = $tipomoneda;
        $IdMonedaCambio = $IdMoneda;//($IdMoneda == 1)? 2:1;
        $IdArqueoM  = $mov->getIdArqueoEsCerrado($IdMonedaCambio,$idlocal);
        if(!$IdArqueoM){ 
		    echo "~1~"; // Caja de moneda destino está cerrada;
		    return false;
        }
    }

    $id = ModificaPagoDocumento($provhab,$modalidadpago,$fechaoperacion,
				$codigooperacion,$nrodocumento,$cuentaproveedor,
				$cuentaempresa,$tipomoneda,
				$cambiomoneda,$importe,$obs,$idlocal,$IdUsuario,
                                $estado,$idoc,$IdArqueo,$cEstado,$cambiodivisa);
	  
    if ($id)		
      echo "~~".$id;
    else
      echo "~0~";
	  
    exit();
    break;

  case "EliminaPagoDocumento":
    $idoc              = CleanText($_POST["idoc"]);
    $IdLocal           = CleanID(getSesionDato("IdTienda"));
    $IdUsuario         = CleanID(getSesionDato("IdUsuario"));
    $Estado            = CleanText($_POST["xstado"]);

    if($Estado == 'Confirmado') {
      echo '0';
      return;
    }

    $id = EliminarPagoDocumento($IdLocal,$IdUsuario,$idoc,$Estado);

    if ($id)		
      echo "$id";
    else
      echo "0";

    exit();
    break;

  case "AltaPago":
    $IdComprobante     = CleanID($_POST["xidcp"]);
    $IdPagoDoc         = (isset($_POST["xidppd"]))? CleanID($_POST["xidppd"]):0;
    $importe           = (isset($_POST["ximp"]))? CleanFloat($_POST["ximp"]):'';
    $mora              = (isset($_POST["xmora"]))? CleanFloat($_POST["xmora"]):'';
    $documento         = CleanText($_POST["xdes"]);
    $obs               = CleanText($_POST["xobs"]);
    $estadopago        = (isset($_POST["xep"]))? CleanText($_POST["xep"]):'';
    $importeplan       = (isset($_POST["xiplan"]))? CleanFloat($_POST["xiplan"]):'';
    $fechaplan         = (isset($_POST["xfplan"]))? CleanCadena($_POST["xfplan"]):'';
    $IdMoneda          = CleanID($_POST["xidm"]);
    $Desviacion        = (isset($_POST["xdesviacion"]))? CleanFloat($_POST["xdesviacion"]):'';
    $TipoDif           = (isset($_POST["xtipodif"]))? CleanText($_POST["xtipodif"]):'';
    $IdProveedor       = (isset($_POST["xprov"]))? CleanID($_POST["xprov"]):0;
    $IdUsuario         = CleanID(getSesionDato("IdUsuario"));
    $cambiomoneda      = (isset($_POST["xcambiocbte"]))? CleanFloat($_POST["xcambiocbte"]):'';
    $IdLocal           = (isset($_POST["xlocal"]))? CleanID($_POST["xlocal"]):0;
    $tipoprov          = (isset($_POST["xtipoprov"]))? CleanText($_POST["xtipoprov"]):'';
    $esagregar         = CleanText($_POST["xesagregar"]);

    $mov               = new movimientogral;
    $IdArqueo          = $mov->getIdArqueoEsCerrado($IdMoneda,$IdLocal);
    $estado            = (!$IdArqueo)? "Borrador":"Pendiente";
    $IdArqueo          = (!$IdArqueo)? 0:$IdArqueo;
    $modalidadpago     = '9';
    $fechaoperacion    = date("Y-m-d H:m:s");
    $TipoMoneda        = $IdMoneda;

    $pago              = new pago;
    $xidpagodoc        = $pago->getIdPagoDoc($IdComprobante);
    $xImportePlan      = $pago->getImportePlan($IdComprobante);
    $xImporteCbte      = $pago->getImporteComprobante($IdComprobante);
    $xImportePago      = $pago->getImportePagada($IdComprobante);

    switch($esagregar){
    case 'asociar':
      if(($xidpagodoc == $IdPagoDoc) || ($xImportePago >= $xImporteCbte)){
	echo 'completa';
	break;
      }
      /*
      if($TipoDif == 'Excedente')
	CrearPagoDocumento($IdProveedor,$ordencompra,$modalidadpago,
			   $fechaoperacion,$codigooperacion,$nrodocumento,
			   $cuentaproveedor,$cuentaempresa,
			   $TipoMoneda,$cambiomoneda,$mora,$obs,$IdLocal,
			   $IdUsuario,$estado,$IdArqueo,$tipoprov);
      */
      break;
    case 'planificar':
      if($xImportePlan >= $xImporteCbte){
	echo 'completa';
	break;
      }
      break;
    }

    $exceso = ($TipoDif == 'Excedente')? $mora:0;
    $mora   = ($TipoDif == 'Mora')? $mora:0;
    $id     = CrearPago($IdComprobante,$IdPagoDoc,$importe,$mora,$documento,$obs,
			$IdUsuario,$estadopago,$importeplan,$fechaplan,$IdMoneda,
			$Desviacion,$exceso);
	       
    if ($id)		
      echo "$id";
    else
      echo "0";
	       
    exit();
    break;

  case 'actualizaEstadoPago':
    $Opcion = CleanID($_GET["xop"]);
    $xdato  = CleanText($_GET["xdato"]);
    $IdComprobante = CleanID($_GET["xid"]);

    switch($Opcion){
    case '1':
      $campoxdato = "PlazoPago='".$xdato."'";
      break;
    case '2':
      $campoxdato = "Cobranza='".$xdato."'";
      break;
    case '3':
      $campoxdato = "Observaciones='".$xdato."'";
      break;
    }

    echo ActualizarEstadoPago($IdComprobante,$campoxdato);
    break;

  case 'ObtieneDatosProveedor':
    $IdProveedor = (isset($_GET["xprov"]))? CleanID($_GET["xprov"]):0;
    $oProveedor   = new proveedor;
    $oProveedor->Load($IdProveedor);
 
    echo "::".$oProveedor->get("CuentaBancaria")."~~".$oProveedor->get("CuentaBancaria2")."~~";
    break;

  case 'ObtieneDatosEmpresa':
    $IdLocal  = getSesionDato("IdTienda");
    $oLocal   = new local;
    $oLocal->Load($IdLocal);
 
    echo "::".$oLocal->get("CuentaBancaria")."~~".$oLocal->get("CuentaBancaria2")."~~";
    break;

  case 'ModificarCobros':
    $Opcion          = CleanID($_GET["xop"]);
    $IdComprobante   = CleanID($_GET["idcbte"]);
    $IdOperacionCaja = CleanID($_GET["idopcja"]);
    $IdCliente       = CleanID($_GET["idc"]);
    $IdModalidadPago = CleanID($_GET["idmod"]);
    $IdLocal         = (isset($_GET["idl"]))? CleanID($_GET["idl"]):getSesionDato('IdTienda');
    $TipoVenta       = CleanText($_GET["tv"]);
    $ImporteCobro    = CleanFloat($_GET["ximp"]);

    switch($Opcion){
      case '1':
	//Eliminar Abono
	if($TipoVenta != 'CG'){
	  $mov      = new movimiento;
	  $arqueomov= $mov->getIdArqueMovimiento($IdOperacionCaja);
	  $esCaja   = explode("~",esCerradaCaja($arqueomov));
	  
	  if($esCaja[0] == 1){
	    echo '~cjacda~';
	    return;
	  }
	}
	else{
	  $mov      = new movimientogral;
	  $arqueomov= $mov->getIdArqueMovimientoGral($IdOperacionCaja);
	  $esCaja   = explode("~",esCerradaCajaGral($arqueomov));
	  
	  if($esCaja[0] == 1){
	    echo '~cjacda~';
	    return;
	  }
	}

	$xdato = ModificarCobros($Opcion,$IdComprobante,$IdOperacionCaja,$IdCliente,$IdModalidadPago,$ImporteCobro,$IdLocal,$TipoVenta);
	echo '~~'.$xdato;
	break;
    }
    break;
  case 'ModificaSaldoPago':
    $IdPagoDoc  = CleanID($_GET["idpagodoc"]);
    $Saldo      = CleanFloat($_GET["saldo"]);

    echo ModificarSaldoPago($IdPagoDoc,$Saldo);
    break;
}

?>


