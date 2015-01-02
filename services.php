<?php

include("tool.php");

SimpleAutentificacionAutomatica("novisual-services");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

define ("ALTA_MUDA",true);
//Posibles estados de una factura
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",2);
define("FAC_IMPAGADA",3);
define("FAC_ANULADA",4);


switch($modo) {
	case "cajaescerrado":
		$esCerrada = cajaescerrado();
		echo $esCerrada;
		exit();	
		break;	
	case "buscaproducto":	
		$nombre = $_REQUEST["nombre"];
		echo VolcarGeneracionJSParaProductos($nombre,false,false); 
		break;	
	
	case "buscarproductocb":
		$cb = CleanCB($_REQUEST["cb"]);
		echo VolcarGeneracionJSParaProductos(false,false,$cb);
		break;		

	case "buscaproductoref":
		$ref = CleanRef($_REQUEST["ref"]);		
		echo VolcarGeneracionJSParaProductos(false,$ref);
		break;
	
	case "eliminarcliente":
		$idcliente 	= CleanID($_GET["idcliente"]);
		
		$cliente = new cliente;
		if($cliente->Load($idcliente))		
			echo $cliente->MarcarEliminado();
		else
			echo 0;
		break;
	
	
	case "mostrarCliente":		
		$idcliente 	= CleanID($_GET["idcliente"]);
		
		$datos = DetallesCliente($idcliente);		
		
		VolcandoXML( Traducir3XML($datos),"clientes");
		exit(); 		
		break;	

	
	case "realizarAbono":
		$id = CleanID($_GET["IdComprobante"]);
		$pago = CleanFloat($_GET["pago"]);
		
		/*
		+ "&pago_efectivo=" + parseFloat(abono_efectivo)
		+ "&pago_bono=" + parseFloat(abono_bono)
		+ "&pago_tarjeta=" + parseFloat(abono_tarjeta)*/
		
		$pago_efectivo 	= CleanFloat($_GET["pago_efectivo"]);
		$pago_bono 		= CleanFloat($_GET["pago_bono"]);
		$pago_tarjeta 	= CleanFloat($_GET["pago_tarjeta"]);				
		
		$newpendiente = OperarPagoSobreTicket($id,$pago_efectivo, $pago_bono, $pago_tarjeta);
		echo $newpendiente;//Cantidad pendiente o cero.				
		break;
	
	case "numeroSiguienteDeFacturaParaNuestroLocal":	
		$IdLocalActivo = getSesionDato("IdTienda");
		$moticket = $_GET["moticket"];
		$numSerieTicketLocalActual = GeneraNumDeTicket($IdLocalActivo,$moticket);
		echo $numSerieTicketLocalActual;// . " con $moticket";
		exit();	
		break;	
	case "altaproducto":
		if ( $id = AltaDesdePostProducto(ALTA_MUDA) ) {

			$unidades = CleanInt($_POST["Unidades"]);
			$costo    = CleanFloat($_POST["CosteSinIVA"]);
			$vfv      = CleanCadena($_POST["vFV"]);
			$vlt      = CleanCadena($_POST["vLT"]);
			$PVD      = CleanCadena($_POST["vPVD"]);
			$PVDD     = CleanCadena($_POST["vPVDD"]);
			$PVC      = CleanCadena($_POST["vPVC"]);
			$PVCD     = CleanCadena($_POST["vPVCD"]);
			$cModo    = CleanCadena($_POST["vModo"]);
			$esInvent = ( $cModo == 'altainventario' )? true:false;
			$vfv      = ($vfv=='')? false:date("d-m-Y", strtotime($vfv));
			$vlt      = ($vlt=='')? false:$vlt;
			$importe  = $unidades*$costo;
			
			if(!$esInvent)
			  AgnadirCarritoComprasDirecto($id,$unidades,$costo,
						       $vfv,$vlt,0,
						       $importe,0);
			//Ventas Precios
			registrarPreciosVentaAlmacenProducto($PVD,$PVDD,$PVC,$PVCD,$id);

			echo $id;
		}  else {
			echo "0";
		}
		exit();
		break;
		
	case "mostrarServicios":		
		$idsubsidiario 	= CleanID($_GET["idsubsidiario"]);
		$status 	= CleanText($_GET["status"]);
		$ticket		= CleanText($_GET["ticket"]);
		$desde 	        = CleanCadena($_GET["desde"]);
		$hasta 		= CleanCadena($_GET["hasta"]);

		$datos = DetallesServicios($idsubsidiario,$status,$ticket,$desde,$hasta);
		VolcandoXML( Traducir3XML($datos),"arreglos");
		exit(); 		
		break;	
			
	case "mostrarDetallesVenta":
		$IdComprobante = CleanID($_GET["IdComprobante"]);
		$IdLocal       = getSesionDato("IdTienda");
		$datos         = DetallesVenta($IdComprobante,$IdLocal);
		VolcandoXML( Traducir2XML($datos),"detalles");				
		exit();				
		break;


	case "mostrarDetallesCompra":
		$IdPedido     = CleanText($_GET["IdPedido"]);
		$filtromoneda = CleanText($_GET["filtromoneda"]);
		$esSoloMoneda = trim($filtromoneda);
		$datos        = DetallesCompra($IdPedido,$filtromoneda);
		VolcandoXML( Traducir2XML($datos),"detalles");			
		exit();				
		break;

	case "mostrarDetallesComprasRecibir":
		$IdPedido  = CleanText($_GET["IdPedido"]);
		$IdAlmacen = CleanText($_GET["IdAlmacen"]);
		$datos     = DetallesCompraRecibir($IdPedido,$IdAlmacen);
		VolcandoXML( Traducir2XML($datos),"detalles");				
		exit();				
		break;
					
	case "mostrarVentas":
		$localtpv         = getSesionDato("IdTiendaDependiente");
		$localventa       = (isset($_GET["filtrolocal"]))? CleanID($_GET["filtrolocal"]):$localtpv;
	        $local            = ($_GET["esventas"] == 'on')? $localventa:$localtpv;
		$desde            = date("Y-m-d", strtotime( CleanFechaES($_GET["desde"]) ));
		$hasta            = date("Y-m-d", strtotime( CleanFechaES($_GET["hasta"]) ));
		$nombre           = CleanText($_GET["nombre"]);
		$modoserie        = CleanText($_GET["modoserie"]);
		$modosuscripcion  = CleanText($_GET["modosuscripcion"]);
		$idsuscripcion    = (isset($_GET["idsuscripcion"]))? CleanText($_GET["idsuscripcion"]):0;
		$modofactura      = CleanText($_GET["modofactura"]);
		$modoboleta       = CleanText($_GET["modoboleta"]);
		$modoticket       = CleanText($_GET["modoticket"]);
		$mododevolucion   = CleanText($_GET["mododevolucion"]);
		$modoalbaran      = CleanText($_GET["modoalbaran"]);
		$modoalbaranint   = CleanText($_GET["modoalbaranint"]);
		$modoconsulta     = CleanText($_GET["modoconsulta"]);		
		$modoventa        = CleanText($_GET["modoventa"]);		
		$forzarfacturaid  = CleanID($_GET["forzarfactura"]);
		$forzarid         = CleanText($_GET["forzarid"]);		
		
		$esSoloFactura    = ($modofactura == "factura");
		$esSoloBoleta     = ($modoboleta == "boleta");
		$esSoloTicket     = ($modoticket == "ticket");
		$esSoloDevolucion = ($mododevolucion == "devolucion");
		$esSoloAlbaran    = ($modoalbaran == "albaran");
		$esSoloAlbaranInt = ($modoalbaranint == "albaranint");
		$esSoloPendientes = ($modoconsulta == "pendientes");
		$esSoloCesion     = ($modoserie == "cedidos");	
		$esSoloSuscripcion = ($modosuscripcion == "suscripcion");	
		$TipoVenta        = ($modoventa == "tpv")? getSesionDato("TipoVentaTPV"):false;
		$forzaridsuscripcion = ($idsuscripcion != 0 )? $idsuscripcion:0;
		 		
		if (!$hasta or $hasta == ""){
			$mm    = intval(date("m"));
			$dd    = intval(date("d"));
			$aaaa  = intval(date("Y"));
			$hasta = "$aaaa-$mm-$dd";
		}
		if (!$desde or $desde == "") $desde = "1900-01-01";

					
		$datos = VentasPeriodo($local,$desde,$hasta,$esSoloPendientes,$esSoloFactura,
				       $esSoloBoleta,$esSoloDevolucion,$esSoloAlbaran,
				       $esSoloAlbaranInt,$esSoloTicket,$nombre,$esSoloCesion,
				       $esSoloSuscripcion,$forzarfacturaid,$TipoVenta,$forzarid,
				       $forzaridsuscripcion);
		VolcandoXML( Traducir2XML($datos),"ventas");
		exit();
		break;


	// SERVICIO - ACTUALIZAR PRECIOS

	case "actualizarNuevosPV":
	        $listalocal = CleanID($_GET["listalocal"]);	
		$IdLocal    = getSesionDato("IdTienda");
		$IdLocal    = ($listalocal!=0)? $listalocal:$IdLocal;
		echo actualizarNuevosPVAlmacen($IdLocal);
		exit(); 		
		break;

	case "actualizarAllNuevosPV":
		echo actualizarAllNuevosPVAlmacen();
		exit(); 		
		break;

	case "setStatusTrabajoSubsidiario":
		$idtrabajo 	= CleanID($_GET["idtrabajo"]);
		$status 	= CleanText($_GET["status"]);
		
		$job 		= new job;		
		$job->qModificacionEstado($status,$idtrabajo);
		exit();
		break;


        case "syncStockAlmacen":
	       $IdLocal = getSesionDato("IdTiendaDependiente");
	       $time    = CleanInt($_POST["timeSyncTPV"]);
	       $xjsOut  = syncUnidAlmacen($time,$IdLocal);
	       echo $xjsOut;
	       exit();
	       break;

        case "getStockAlmacen":
	       $IdLocal = getSesionDato("IdTiendaDependiente");
	       $xjsOut  = getUnidAlmacen($IdLocal);
	       echo $xjsOut;
	       exit();
	       break;

        case "getClientesTPV":
	       $xout = getClientesTPV();
	       echo $xout;
	       exit();
	       break;

        case "syncClientesTPV":
	       $time = CleanInt($_GET["tsyncClient"]);//segundos
	       $xout = getClientesTPV($time);

	       echo $xout;
	       exit();
	       break;

        case "syncPresupuestosTPV":
	       $tipo = CleanText($_GET["tipopresupuesto"]);
	       $xout = obtenerListaPresupuestosTPV($tipo);

	       echo $xout;
	       exit();
	       break;

        case "syncMProductosTPV":
	       $estado = trim(CleanRealMysql($_GET["Estado"]));
	       $xout   = obtenerListaMProductosTPV($estado);

	       echo $xout;
	       exit();
	       break;

        case "validarCompraSerie":
	       $serie      = CleanText($_GET["ns"]);
	       $idproducto = CleanID($_GET["idproducto"]);
	       $idlocal    = CleanID($_GET["idlocal"]);
	       $idlocal    = ($idlocal)? $idlocal : getSesionDato("IdTienda");
	       $xout       = validaNumeroSerie($idproducto,$serie,$idlocal);
	       echo $xout;
	       exit();
	       break;

        case "validarSerie":
	       $serie      = CleanText($_GET["ns"]);
	       $idproducto = CleanID($_GET["idproducto"]);
	       $idlocal    = CleanID($_GET["idlocal"]);
	       $idlocal    = ($idlocal)? $idlocal : getSesionDato("IdTienda");
	       $xout       = validaNumeroSerie($idproducto,$serie,$idlocal);
	       echo $xout;
	       exit();
	       break;

        case "cargarDetMProductoACarritoTPV":
	       $idproducto     = CleanID($_GET["idprod"]);
	       $idmetaproducto = CleanID($_GET["id"]);
	       $idcliente      = CleanID($_GET["idcliente"]);
	       $xout           = obtenerDetalleMProductoTPV($idproducto,$idmetaproducto,
							    $idcliente);
	       echo $xout;
	       exit();
	       break;

        case "cargarDetBaseMProductoACarritoTPV":
	       $idproducto     = CleanID($_GET["idprod"]);
	       $idmetaproducto = CleanID($_GET["id"]);
	       $idcliente      = CleanID($_GET["idcliente"]);
	       $xout           = obtenerDetalleBaseMProductoTPV($idproducto,$idmetaproducto,
								$idcliente);
	       echo $xout;
	       exit();
	       break;

        case "cargarIdBaseMProducto":

   	       $idproducto = CleanID($_GET["Id"]);
	       $idlocal    = getSesionDato("IdTiendaDependiente");
	       $xout       = getDetBaseMProducto($idlocal,$idproducto);

	       echo $xout;
	       exit();
	       break;

        case "registraProductoBorrador":

   	       $xproducto = CleanText($_GET["concepto"]);
   	       $xusuario  = CleanID($_GET["dependiente"]);
	       $xlocal    = getSesionDato("IdTiendaDependiente");

	       echo registraProductoBorrador($xlocal,$xproducto,$xusuario);
	       exit();
	       break;

        case "mostrardetalleMProducto":
	       $CBMP     = CleanCadena($_GET["cbmp"]);
	       $detalle  = getDetFromCBMetaProducto($CBMP);
	       $xout     = ($detalle != '')? str_replace("<br/>","\n       -",$detalle):0;
	       
	       echo $xout;	       
	       exit();
	       break;

        case "cargarDetPresupuestoACarritoTPV":
               $tipopresupuesto = CleanText($_GET["tipo"]);
	       $idpresupuesto   = CleanID($_GET["id"]);
	       $idcliente       = CleanID($_GET["idcliente"]);
               $xout            = obtenerDetPresupuestoTPV($tipopresupuesto,$idpresupuesto,
							   $idcliente);
	       
	       echo $xout;	       
	       exit();
	       break;

        case "cargarListaBaseMProductosTPV":
	       $xout = obtenerListaBaseMProductos();
	       
	       echo $xout;	       
	       exit();
	       break;

        case "setIdLocalDependienteTPV":
	       $id   = CleanID($_GET["id"]);
	       setSesionDato("IdTiendaDependiente",$id);

	       echo  getSesionDato("IdTiendaDependiente");
	       exit();
	       break;

        case "setStatusPresupuestoTPV":
	       $IdPresupuesto = (isset($_GET["id"]))? CleanID($_GET["id"]):0;
	       $Opcion        = CleanText($_GET["op"]);
	       $xout          = setStatusPresupuestoTPV($IdPresupuesto,$Opcion);

	       echo $xout;
	       exit();
	       break;

        case "setStatusMProductoTPV":
	       $IdMetaProducto = CleanID($_GET["id"]);
	       $Opcion         = trim(CleanText($_GET["op"]));
	       $xout           = setStatusMProductoTPV($IdMetaProducto,$Opcion);

	       echo $xout;
	       exit();
	       break;


        case "obtenerDatosComprobanteVenta":
	       $IdComprobante = (isset($_GET["IdComprobante"]))? CleanID($_GET["IdComprobante"]):0;
	       $IdLocal   = getSesionDato("IdTiendaDependiente");
	       $esVenta   = CleanText($_GET["esVenta"]);
	       $IdLocal   = ($esVenta == 'on')? CleanID($_GET["IdLocal"]):$IdLocal;
	       if (!$IdComprobante)
		 $IdComprobante = getIdFromComprobante(CleanInt($_GET["nroComprobante"]),
						       CleanText($_GET["tipoComprobante"]),
						       CleanInt($_GET["sreComprobante"]));
	       $IdComprobante = CleanID($IdComprobante);
	       echo getDatosComprobante($IdComprobante,$IdLocal);
	       exit();	
	       break;

        case "obtenerDatosComprobantePresupuesto":
	       echo getDatosPresupuesto(CleanInt($_GET["nroComprobante"]),
					CleanText($_GET["tipoComprobante"]));
	       exit();	
	       break;


        case "validarNumeroComprobante":
	      ValidarNumeroComprobante( CleanInt($_GET["nroComprobante"]),
					CleanText($_GET["textDoc"]),
					CleanInt($_GET["Serie"]));
	      exit();	
	      break;

        case "validarNumeroPresupuesto":
	      ValidarNumeroPresupuesto( CleanInt($_GET["nroPresupuesto"]),
					CleanText($_GET["textDoc"]),
					CleanInt($_GET["Serie"]));
	      exit();	
	      break;

        case "registraSerieDocumentoVenta":
	      $iddocumento = CleanInt($_GET["idDocumento"]);
	      $Serie       = CleanInt($_GET["Serie"]);
	      $IdLocal     = getSesionDato("IdTiendaDependiente");
	      $registro    = 0;

	      switch($iddocumento){
	      case 1: $documento = 'Boleta'; break;
	      case 2: $documento = 'Factura'; break;
	      case 4: $documento = 'Albaran'; break;
	      }
	      if(!extSerieComprobante($IdLocal,$Serie,$documento))
		if(regitraSerieComprobante($IdLocal,$Serie,$documento))
		  $registro = 1;
	      echo $registro;

	      exit();	
	      break;


        case "existeSerieDocumentoVenta":
              $iddocumento = CleanInt($_GET["idDocumento"]);
	      $Serie       = CleanInt($_GET["Serie"]);
	      $IdLocal     = getSesionDato("IdTiendaDependiente");
	      
	      switch($iddocumento){
	      case 1: $documento = 'Boleta'; break;
	      case 2: $documento = 'Factura'; break;
	      case 4: $documento = 'Albaran'; break;
	      case 5: $documento = 'Proforma'; break;
	      }
	      
	      if(extSerieComprobante($IdLocal,$Serie,$documento))
		echo 1;
	      else 
		echo 0;
	      
	      exit();	
	      break;

        case "cargaNroDocumentoVenta":
              $iddocumento = CleanInt($_GET["idDocumento"]);
	      $Serie       = CleanInt($_GET["Serie"]);
	      $IdLocal     = getSesionDato("IdTiendaDependiente");
	      
	      switch($iddocumento){
	      case 1: $documento = 'Boleta'; break;
	      case 2: $documento = 'Factura'; break;
	      case 4: $documento = 'Albaran'; break;
	      case 5: $documento = 'Proforma'; break;
	      }
	      
	      if($iddocumento!=5)
		echo NroComprobanteVentaMax($IdLocal,$documento,$Serie);
	      else
		echo NroComprobantePreVentaMax($IdLocal,$documento,$Serie);
	      
	      exit();	
	      break;
	      
        case "cargarDescripcionFichaProductoTPV":
 	      $cb = CleanCB($_GET["cb"]);
	      $id = getIdFromCodigoBarras($cb);

	      echo getDatosProductosExtra($id,'nombre');
	      exit();	
	      break;

        case "veridcarritoCompra":
	      $modo     = getSesionDato("modoserie");
	      $n        = count($modo);
	      $idprod   = getSesionDato("idprodserie");
	      $series   = getSesionDato ("series" );
	      $cant     = getSesionDato("cantserie" );
	      $local    = getSesionDato("DestinoAlmacen");
	      $garantia = getSesionDato("garantia");

	      for($j=0;$j<count($idprod);$j++)
		{
		  if($modo[$j]=="CB")
		    $idprod[$j] = getIdFromCodigoBarras($idprod[$j]);	
		}
	      $seriesidcarro=implode(",",$idprod);
	      echo $seriesidcarro;
	      exit();

	      break;


        case "verseriecarritoCompra":
	      $id       = CleanID($_GET["id"]);
	      $modo     = getSesionDato("modoserie");
	      $n        = count($modo);
	      $idprod   = getSesionDato("idprodserie");
	      $series   = getSesionDato ("series" );
	      $cant     = getSesionDato("cantserie" );
	      $local    = getSesionDato("DestinoAlmacen");
	      $garantia = getSesionDato("garantia");

	      for($j=0;$j<count($idprod);$j++)
		{
		  if($modo[$j]=="CB")
		    $idprod[$j]=getIdFromCodigoBarras($idprod[$j]);	
		  
		  if($idprod[$j]==$id)
		    {
		      $seriesdecarro=implode(",",$series[$j]);
		      echo $seriesdecarro;
		      exit();
		    }
		}
	      break;

        case "verseriefechaCompra":
	      $id      = CleanID($_GET["id"]);
	      $modo    = getSesionDato("modoserie");
	      $n       = count($modo);
	      $idprod  = getSesionDato("idprodserie");
	      $series  = getSesionDato ("series" );
	      $cant    = getSesionDato("cantserie" );
	      $local   = getSesionDato("DestinoAlmacen");
	      $garantia= getSesionDato("garantia");

	      for($j=0;$j<count($idprod);$j++)
		{
		  if($modo[$j]=="CB")
		    $idprod[$j]=getIdFromCodigoBarras($idprod[$j]);	
		  
		  if($idprod[$j]==$id)
		    {
		      $fecha = $garantia[$j];
		      echo $fecha;
		      exit();
		    }
		}
	      break;

        case "actualizarcarritoseriesCompra":
	      $ids           = CleanID($_GET["id"]);
	      $nuevaserie    = CleanCadena($_GET["nuevaserie"]);
	      $fechagarantia = CleanCadena($_GET["fechagarantia"]);
	      $nuevaserie    = explode(",",$nuevaserie);
	      $modo          = getSesionDato("modoserie");
	      $n             = count($modo);
	      $idprod        = getSesionDato("idprodserie");
	      $series        = getSesionDato ("series" );
	      $cant          = getSesionDato("cantserie" );
	      $local         = getSesionDato("DestinoAlmacen");
	      $garantia      = getSesionDato("garantia");
	      $respuesta     = "";

	      for($j=0;$j<count($idprod);$j++)
		{
		  if($modo[$j]=="CB")
		    $idprod[$j]=getIdFromCodigoBarras($idprod[$j]);	
		  
		  if($idprod[$j]==$ids)
		    {
		      $series[$j]   = null;
		      $garantia[$j] = null;
		      $cant[$j]     = null;
		      $series[$j]   = $nuevaserie;   
		      $garantia[$j] = $fechagarantia;
		      $cant[$j]     = count($nuevaserie);
		      $respuesta    = $garantia[$j];
		    }
	      }
	      //    setSesionDato("idprodserie",$arr);
	      setSesionDato("series",$series);
	      setSesionDato("cantserie",$cant);
	      //   setSesionDato("modoserie",$md);
	      setSesionDato("garantia",$garantia);
	      echo $respuesta;
	      exit();
	      break;
	      
        case "checkndocCompra":
              $idprov = CleanID($_GET["idprov"]);
	      $ndoc   = CleanText($_GET["ndoc"]);
	      $xout   = checkndocCompra($idprov,$ndoc);
	      
	      echo $xout;
	      exit();	
	      break;

        case "getTipoDocCompra":
	      $detadoc = getSesionDato("detadoc");
	      echo $detadoc[0];
	      exit();	
	      break;

        case "ResetearCarritoCompra":
	      ResetearCarritoCompras();
	      exit();	
	      break;

        case "datosproductoextra":
	      $id  = CleanID($_GET["id"]);
	      $arr = getDatosProductosExtra($id,'todos');

	      echo implode(",",$arr);
	      exit();	
	      break;

        case "ComprobarProveedor":
	      $compras = getSesionDato("CarritoCompras");
	      $costes =  getSesionDato("CarroCostesCompra");
	      if ($compras!=''){
		foreach ($compras as $id=>$unidades) {		
		  $idproveedor = getIdProveedorFromIdProducto($id);
		  if($idproveedor!=''){
		    echo '0';
		    exit();	
		  }
		}
	      }  
	      echo '1';
	      exit();	
	      break;

        case "setfdocCompra":
	      $fdoc       = CleanCadena($_GET["fdoc"]);
	      $detadoc    = getSesionDato('detadoc');
	      $detadoc[4] = $fdoc;

	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setfleteCompra":
	      $flete      = CleanDinero($_GET["flete"]);
	      $detadoc    = getSesionDato('detadoc');
	      $detadoc[13] = $flete;

	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setpercepcionCompra":
	      $percepcion = CleanDinero($_GET["percepcion"]);
	      $detadoc    = getSesionDato('detadoc');
	      $detadoc[14] = $percepcion;

	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setfpdocCompra":
	      $fpdoc      = CleanCadena($_GET["fpdoc"]);
	      $detadoc    = getSesionDato('detadoc');
	      $detadoc[8] = $fpdoc;
	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setaCreditoCompra":
	      setSesionDato("aCredito",CleanText($_GET["opcion"]));
	      exit();	
	      break;

        case "setincImpuestoDetCompra":
	      setSesionDato("incImpuestoDet",CleanText($_GET["opcion"]));
	      exit();
	      break;

        case "setincPercepcionCompra":
	      setSesionDato("incPercepcion",CleanText($_GET["opcion"]));
	      exit();
	      break;

        case "settipodocCompra":
              $tipodoc = CleanText($_GET["tipodoc"]);
	      $detadoc     = getSesionDato('detadoc');
	      $detadoc[0]  = $tipodoc;
	      if($tipodoc=='SD'){
		//$detadoc[1]='1';
		//$detadoc[2]='CASAS VARIAS';
 		$detadoc[3]=false;
		//$detadoc[4]=false;
		$detadoc[5]==1;
		$detadoc[6]==false;
		$detadoc[7]==false;
		$detadoc[8]==false;
		$detadoc[9]==false;
		$detadoc[10]==false;
		$detadoc[11]==false;
		$detadoc[12]==false;
		$detadoc[13]==0;
		$detadoc[14]==0;
	      }
	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setfcambioCompra":
	      $fcambio     = CleanCadena($_GET["fcambio"]);
	      $detadoc     = getSesionDato('detadoc');
	      $detadoc[7]  = $fcambio;
	      setSesionDato('detadoc',$detadoc);

	      exit();	
	      break;

        case "settipocambioCompra":
	      $tipocambio = CleanFloat($_GET["tipocambio"]);
	      $detadoc        = getSesionDato('detadoc');
	      $detadoc[6]     = $tipocambio;
	      setSesionDato('detadoc',$detadoc);

	      exit();	
	      break;

        case "setndocCompra":
	      $ndoc   = CleanText($_GET["ndoc"]);
	      $detadoc    = getSesionDato('detadoc');
	      $detadoc[3] = $ndoc;
	      setSesionDato('detadoc',$detadoc);

	      exit();	
	      break;

        case "settipomonedaCompra":
	      $tipodoc     = CleanInt($_GET["tipomoneda"]);
	      $detadoc     = getSesionDato('detadoc');
	      $detadoc[5]  = $tipodoc;
	      setSesionDato('detadoc',$detadoc);

	      exit();	
	      break;

        case "setprovdocCompra":
	      $provdoc    = CleanID($_GET["provdoc"]);
	      $nombreprov = CleanText($_GET["nombreprov"]);
	      $detadoc        = getSesionDato('detadoc');
	      $detadoc[1]     = $provdoc;
	      $detadoc[2]     = $nombreprov;
	      setSesionDato('detadoc',$detadoc);
	      exit();	
	      break;

        case "setsubsiddocCompra":
	      $subsiddoc    = CleanText($_GET["subsiddoc"]);
	      $nombresubsid = CleanText($_GET["nombresubsid"]);
	      $detadoc          = getSesionDato('detadoc');
	      $detadoc[9]       = $subsiddoc;
	      $detadoc[10]      = $nombresubsid;
	      setSesionDato('detadoc',$detadoc);

	      exit();	
	      break;

        case "verificadocCompra":
              $detadoc = getSesionDato('detadoc');
              $Moneda  = getSesionDato('Moneda');
	      $tdoc    = $detadoc[0];
	      $ndoc    = trim($detadoc[3]);
	      $fdoc    = trim($detadoc[4]);
	      $cambio  = trim($detadoc[6]);
	      $fcambio = trim($detadoc[7]);
	      $moneda  = ($detadoc[5] == 2)? true:false; 
	      $andoc   = explode("-", $ndoc);
	      $hmsgerr = "gPOS: Carrito de Compra \n\n ".
		"      Los campos con negrita son obligatorios ";
	      $msgerr  = "";
	      switch($tdoc){
	      case "F":
	      case "R":
	      case "G":
		$msgerr  = (empty($andoc[1]) && empty($andoc[0]))? "\n        * Serie - Nro ":""; 
	      $msgerr .= (empty($fdoc))? "\n        * Fecha Emisión":""; 
	      break;
	      case "SD":
		$msgerr = ''; 
		break;
	      case "O":
		$msgerr = (empty($fdoc))?  "\n      * Fecha Entrega":""; 
		break;
	      }
	      $msgerr .= ( $moneda && empty($cambio)  )? "\n        * Tipo Cambio":"";
	      $msgerr .= ( $moneda && empty($fcambio) )? "\n        * Fecha Cambio":"";
	      //Encabezado
	      if($msgerr != '') 
		{
		  echo $hmsgerr.$msgerr;
		  exit();
		}
	      //Detalle
	      echo validaxdtCarritoDirecto();
	      exit();
	      break;

 	case "mostrarCompra":
		$modocontado   = CleanText($_GET["modocontado"]);
		$modocredito   = CleanText($_GET["modocredito"]);
		$filtrodocumento = CleanText($_GET["filtrodocumento"]);
		$filtrocompra  = CleanText($_GET["filtrocompra"]);
		$filtromoneda  = CleanText($_GET["filtromoneda"]);
		$filtropago    = CleanText($_GET["filtropago"]);
		$filtroespagos = (isset($_GET["filtroespagos"]))?CleanText($_GET["filtroespagos"]):'';
		$forzaid       = CleanText($_GET["forzaid"]);
		$xrecibir      = (isset($_GET["xrecibir"]))? CleanText($_GET["xrecibir"]):'';
		$esRecibir     = ($xrecibir=='true')?true:false;
		$filtrolocal   = ( getSesionDato("esAlmacenCentral") )? CleanID($_GET["filtrolocal"]):getSesionDato("IdTienda");
		$desde         = date("Y-m-d", strtotime( CleanFechaES($_GET["desde"]) ));
		$hasta         = date("Y-m-d", strtotime( CleanFechaES($_GET["hasta"]) ));
		$emision       = CleanText($_GET["emision"]);
		$nombre        = CleanText($_GET["nombre"]);
		$esSoloContado = ($modocontado == "contado");
		$esSoloCredito = ($modocredito == "credito");
		$esSoloDocumento = trim($filtrodocumento);
		$esSoloMoneda  = trim($filtromoneda);
		$esSoloLocal   = trim($filtrolocal);  
		$esSoloCompra  = trim($filtrocompra);  
		$esSoloPagos   = trim($filtropago);  
		$esPagos       = ($filtroespagos == "Pagos");
		$mm            = intval(date("m"));
		$dd            = intval(date("d"));
		$aaaa          = intval(date("Y"));
		if (!$hasta or $hasta == "") $hasta = "$aaaa-$mm-$dd";
		if (!$desde or $desde == "") $desde = "1900-01-01";

		$datos = CompraPeriodo($filtrolocal,$desde,$hasta,$emision,$nombre,
				       $esSoloContado,$esSoloCredito,$esSoloMoneda,
				       $esSoloLocal,$esSoloCompra,$forzaid,
				       $esSoloDocumento,$esRecibir,$esSoloPagos,$esPagos);
		VolcandoXML( Traducir2XML($datos),"PedidosCompras");
		exit();
		break;


	case "ModificarCompra":
		$xid   = CleanID($_GET["xid"]);
		$xidet = CleanID($_GET["xidet"]);
		$xocs  = CleanID($_GET["xocs"]);
		$xdato = CleanText($_GET["xdato"]);

		switch($xocs) {
		case 1:
		  $campoxdato = "FechaFacturacion='".$xdato."'";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 2:
		  $campoxdato = "FechaPago='".$xdato."'";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 3:
		  $campoxdato = "Codigo='".$xdato."'";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 4:
		  $campoxdato = "ModoPago='".$xdato."'";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
 		case 5:
		  $campoxdato = "IdProveedor='".$xdato."'";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 6:
		  $campoxdato = "Observaciones = concat(Observaciones,'- ','".$xdato."')";
 		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 7:
		  $campoxdato  = " ImportePercepcion = '".$xdato."'";
		  $campoxdato .= ",ImportePago = TotalImporte+ImporteFlete+ '".$xdato."' ";
		  $campoxdato .= ",ImportePendiente = TotalImporte+ImporteFlete+ '".$xdato."' ";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 19:
		  $campoxdato  = " ImporteFlete = '".$xdato."'";
		  $campoxdato .= ",ImportePago = TotalImporte+ImportePercepcion+ '".$xdato."' ";
		  $campoxdato .= ",ImportePendiente = TotalImporte+ImportePercepcion+ '".$xdato."' ";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 8:
		  $estado =" EstadoDocumento = 'Cancelada' ";
		  $codigo = " Codigo='".$xdato."',TipoComprobante='Ticket' ";
		  $campoxdato = ($xdato=='Ticket')?$estado:$codigo;
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 9:
		  echo sConsolidaCompras($xid,$xdato);
		  break;
		case 10:
		  $aidx     = explode(",", $xdato); 
		  $facturar = ($aidx[0]=='Ticket')?true:false;
		  //Albaran
		  if(!$facturar) 
		    echo sFacturarCompra($xid,$xdato);
		  //Ticket
		  if($facturar) 
		    {
		      $campoxdato = " Codigo='".$aidx[1]."',TipoComprobante='Factura' ";
		      echo sModificarCompra($xid,$campoxdato,false,false);
		    }
		  break;
		case 11:
		  $campoxdato = " Codigo='".$xdato."',TipoComprobante='Boleta' ";
		  echo sModificarCompra($xid,$campoxdato,false,false);
		  break;
		case 12:
		case 13:
		  $aidx = explode(",", $xdato); 
		  $campoxdato = "  CostoUnidad  = '".$aidx[2].
		                "',PrecioUnidad = '".$aidx[1].
		                "',Importe      = '".$aidx[0]."' ";
		  echo sModificarCompra($xidet,$campoxdato,true,true);
		  echo ConsolidaDetalleCompra($xid,false);
		  break;
		case 15:
		  $campoxdato = " Lote='".$xdato."'";
		  echo sModificarCompra($xidet,$campoxdato,true,true);
		  break;
		case 16:
		  $campoxdato = " FechaVencimiento='".$xdato."'";
		  echo sModificarCompra($xidet,$campoxdato,true,true);
		  break;
		case 17:
		  $campoxdato = " Eliminado='1' ";
		  echo sModificarCompra($xidet,$campoxdato,true,true);
		  echo ConsolidaDetalleCompra($xid,false);
		  break;
 		case 18:
		  $campoxdato = "IdLocal='".$xdato."',IdAlmacenRecepcion='".$xdato."'";
		  echo sModificarPedido($xid,$campoxdato);
		  break;

		}
		exit();
		break;

	case "SalvaPreciosVenta":
		$xid     = CleanID($_GET["xid"]);
		$xdato   = CleanText($_GET["xdato"]);
		$IdLocal = CleanText($_GET["xlocal"]);
		$xPV     = explode("_", $xdato);
		//Directa
		$xPD     = explode("~", $xPV[0]);
		$PVD     = $xPD[0];
		$PVDD    = $xPD[1];
		echo guardarPreciosVentaAlmacen($PVD,$PVDD,"PVD",$xid,$IdLocal);
		//Corporativa
		$xPC     = explode("~", $xPV[1]);
		$PVC     = $xPC[0];
		$PVCD    = $xPC[1];
		echo guardarPreciosVentaAlmacen($PVC,$PVCD,"PVC",$xid,$IdLocal);
		exit();
		break;

	case "RecibirProductosAlmacen":
		$xid        = CleanID($_GET["xid"]);
		$xdato      = CleanText($_GET["xdato"]);
		$IdLocal    = CleanID($_GET["xlocal"]);
 		//$campoxdato = " EstadoDocumento = 'Pendiente' ";
		$Operacion  = CleanID($_GET["xoperacion"]);//1:Compras 3:Traslado interno

		registrarPedidoKardexFifo($xid,$xdato,$IdLocal,$Operacion,false,false,false);
		actualizarStatusPedido($xid,'2');
		actualizarEstadoDocumentoPedido($xid);
		//sModificarCompra($xid,$campoxdato,false,false);
		echo 1; 
		exit();
		break;

         case "BoletarNumeroComprobante":
	       BoletarNumeroComprobante(CleanInt($_GET["nro"]),
					CleanCadena($_GET["tipocomprobante"]),
					CleanID($_GET["IdComprobante"]),
					CleanCadena($_GET["accion"]),
					CleanInt($_GET["Serie"]));
	       
	   exit();				
	   break;

         case "FacturarLoteComprobante":
	   FacturarLoteComprobante(CleanInt($_GET["nro"]),
				   CleanCadena($_GET["ltAlbaran"]),
				   CleanCadena($_GET["cliAlbaran"]),
				   CleanInt($_GET["Serie"]),
				   CleanID($_GET["cidcomprobante"]));

	   exit();				
	   break;
         case "FacturarNumeroComprobante":
	   FacturarNumeroComprobante(CleanInt($_GET["nro"]),
				     CleanCadena($_GET["tipocomprobante"]),
				     CleanCadena($_GET["IdComprobante"]),
				     CleanCadena($_GET["accion"]),
				     CleanInt($_GET["Serie"]));
	   exit();	
	   break;

         case "ModificarNumeroComprobante":
	   ModificarNumeroComprobante(CleanInt($_GET["nro"]),
				      CleanCadena($_GET["tipocomprobante"]),
				      CleanID($_GET["IdComprobante"]),
				      CleanCadena($_GET["accion"]),
				      CleanInt($_GET["Serie"]));
	   exit();	
	   break;

         case "ModificarFechaEmicionComprobante":
	   ModificarFechaEmicionComprobante(CleanCadena($_GET["fecha"]),
					    CleanCadena($_GET["tipocomprobante"]),
					    CleanID($_GET["IdComprobante"]),
					    CleanCadena($_GET["accion"]));
	   exit();	
	   break;


         case "DevolverComprobanteTPV":
	   $IdComprobante = CleanID($_GET["comprobante"]);
	   $Monto         = CleanDinero($_GET["montocomprobante"]);
	   $Pendiente     = CleanDinero($_GET["pendientecomprobante"]);
	   $Concepto      = CleanText($_GET["concepto"]);
	   $iDependiente  = CleanID($_GET["dependiente"]);
	   $Monto         = $Monto - $Pendiente; 

	   //Obtenemos cantidad devuelta
	   $Presupuesto  = DevolverComprobanteTPV($IdComprobante,$Monto,
						  $iDependiente,$Concepto);
	   echo $Presupuesto;
	   exit();
 	   break;

         case "ObtenerTipoComprobante":
	   $IdLocal   = getSesionDato("IdTiendaDependiente");
	   $esVenta   = (isset($_GET["esVenta"]))? CleanText($_GET["esVenta"]):'';
	   $IdLocal   = ($esVenta == 'on')? CleanID($_GET["idlocal"]):$IdLocal;
	   echo getTipoComprobante(CleanID($_GET["idex"]),$IdLocal);
    	   exit();
 	   break;

         case "setIdClienteDocumento":
	   $iduser = CleanID($_GET["iduser"]);
	   $id = CleanID($_GET["id"]);
	   echo setIdClienteDocumento($iduser,$id); 
    	   exit();
 	   break;

         case "ObtenerNSMProducto":
	   //Problemas?????? No esta Claro Mproducto Series
	   $cb            = CleanCB($_GET["cod"]);
	   $IdProducto    = getIdFromCodigoBarras($cb);
	   $IdComprobante = CleanID($_GET["id"]);
	   $series        = array();
	   $series        = getSeriesMProducto2IdProducto($IdComprobante,$IdProducto);
	   echo implode($series,";");
	   exit();
 	   break;

         case "login":
	   $id   = CleanID( isset($_POST["xid"]) ? $_POST["xid"] : NULL );
	   $pass = CleanPass( isset($_POST["xp"]) ? $_POST["xp"] : NULL );

	   if( $id and $pass ){
	     if( verificarPassUser($id,md5($pass)) ){
	       $aprecio = getPerfilPrecios( $id );
	       echo "1~".$aprecio;
	     }
	   }
	   else
	     echo "fail";
	   
	   break;

        case "checkServicio":
	  $concepto = CleanText($_GET["xservicio"]);
	  esNuevo2CrearServicio($concepto);
	  exit();				
	  break;

	case "ModificarServicios":
	  $xid    = CleanID($_GET["xids"]);
	  $xocs    = CleanID($_GET["xopserv"]);
	  $xdato   = CleanText($_GET["xdata"]);
	  
	  switch($xocs) {
	  case 1:
	    $npdte = CleanText($_GET["pdte"]);

	    $campoxdato = "Coste='".$xdato."',CostePendiente='".$npdte."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 2:
	    $campoxdato = "IdSubsidiario='".$xdato."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 3:
	    if($xdato == 'Enviado') $campofecha = "FechaEnvio";
	    if($xdato == 'Recibido') $campofecha = "FechaRecepcion";
	    if($xdato == 'Entregado') $campofecha = "FechaEntrega";
	    $xfecha     = CleanText($_GET["xfecha"]);
	    $campoxdato = "Status='".$xdato."',$campofecha='".$xfecha."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 4:
	    $campoxdato = "FechaEnvio='".$xdato."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 5:
	    $campoxdato = "FechaRecepcion='".$xdato."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 6:
	    $campoxdato = "FechaEntrega='".$xdato."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 7:
	    $DocSubsid  = CleanText($_GET["subdoc"]);
	    $CodDocSub  = CleanText($_GET["doccod"]);
	    $xpendiente = obtenerCostePendiente($xid);
	    $npendiente = $xpendiente - $xdato;
	    $campoxdato = "CostePendiente = '".$npendiente."'";
	    $IdLocal    = CleanID(getSesionDato("IdTienda"));
	    $cantidad   = $xdato;
	    $concepto   = "Servicio ".CleanText($_GET["concepto"]);
	    $operacion  = "Gasto";
	    $TipoVenta  = getSesionDato("TipoVentaTPV");
	    $Partida    = 'Servicio tercerizado';
	    $mov        = new movimiento;
	    $IdArqueo   = $mov->getIdArqueoEsCerradoCaja($IdLocal,$TipoVenta);
	    $fechacaja  = $mov->getAperturaCaja($IdLocal,$TipoVenta);
	    $IdPartida  = obtenerPartidaCaja($Partida,$TipoVenta);

	    EntregarOperacionCaja($IdLocal,$cantidad,$concepto,$IdPartida,$operacion,
				  $fechacaja,$IdArqueo,$TipoVenta,$DocSubsid,$CodDocSub,
				  $xid);
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 8:
	    $campoxdato = "Observaciones = '".$xdato."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    break;
	  case 9:
	    $DocSubsid  = CleanText($_GET["subdoc"]);
	    $CodDocSub  = CleanText($_GET["doccod"]);
	    $campoxdato = "DocSubsidiario='".$DocSubsid.
	                  "',NDocSubsidiario='".$CodDocSub."'";
	    echo ModificarSubsidiarioTbjo($xid,$campoxdato);
	    echo ModificarMovDocSubsidiario($xid,$campoxdato);
	    break;
	  }
	  exit();
	  break;
         case "ObtenerDocServicio":
	   echo ObtenerDocumentoServicio(CleanID($_GET["idex"]));
    	   exit();
 	   break;

}

?>