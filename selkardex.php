<?php 
require("tool.php");
SimpleAutentificacionAutomatica("novisual-services");

$modo = CleanText($_GET["modo"]);

switch($modo){		
	    case "kdxMovimientosProducto":
	      $idproducto = CleanID($_GET["idproducto"]);
	      $hasta      = CleanFechaES($_GET["hasta"]);
	      $desde      = CleanFechaES($_GET["desde"]);
	      //$donde      = CleanID($_SESSION["LocalMostrado"]);
	      $donde      = CleanID($_GET["xlocal"]);
	      $xope       = CleanID($_GET["xope"]);
	      $xmov       = CleanText($_GET["xmov"]);
	      $tabla      = obtenerKardexMovimientosProducto($idproducto,$donde,
							     $desde,$hasta,$xope,$xmov);
	      echo $tabla;
	      exit();
	      break;

	    case "countMovimientos":
	      $hasta   = CleanFechaES($_GET["hasta"]);
	      $desde   = CleanFechaES($_GET["desde"]);
	      $xfamilia = CleanID($_GET["familia"]);
	      $xmarca   = CleanID($_GET["marca"]);
	      $donde   = CleanID($_GET["xlocal"]);
	      $xnombre = CleanText($_GET["xnombre"]);
	      $xcodigo = CleanCB($_GET["xcodigo"]);
	      $xope    = CleanID($_GET["xope"]);
	      $xmov    = CleanText($_GET["xmov"]);
	      $datos   = obtenerKardexMovimientos($donde,$desde,$hasta,$xope,$xfamilia,
		 				  $xmarca,$xmov,$xnombre,$xcodigo,
						  false,false,true);
	      
	      echo $datos;
	      exit();
	      break;

	    case "kdxMovimientos":
	      $hasta   = CleanFechaES($_GET["hasta"]);
	      $desde   = CleanFechaES($_GET["desde"]);
	      $xfamilia = CleanID($_GET["familia"]);
	      $xmarca   = CleanID($_GET["marca"]);
	      $donde   = CleanID($_GET["xlocal"]);
	      $xnombre = CleanText($_GET["xnombre"]);
	      $xcodigo = CleanCB($_GET["xcodigo"]);
	      $xope    = CleanID($_GET["xope"]);
	      $xmov    = CleanText($_GET["xmov"]);
	      $listadesde = CleanInt($_GET["xlistadesde"]);
	      $numerofilas = CleanInt($_GET["xnumfilas"]);
	      $datos   = obtenerKardexMovimientos($donde,$desde,$hasta,$xope,$xfamilia,
		 				  $xmarca,$xmov,$xnombre,$xcodigo,
						  $listadesde,$numerofilas,false);
	      
	      VolcandoXML( Traducir2XML($datos),"Kardex");
	      exit();
	      break;

	    case "countMovimientosInventario":
	      $hasta    = CleanFechaES($_GET["hasta"]);
	      $desde    = CleanFechaES($_GET["desde"]);
	      $xfamilia = CleanID($_GET["familia"]);
	      $xmarca   = CleanID($_GET["marca"]);
	      $esInvent = ( $_GET["xinventario"] == "Inventario" )? true:false;
	      $xinvent  = CleanID($_GET["xidinventario"]);
	      $donde    = CleanID($_GET["xlocal"]);
	      $xnombre  = CleanText($_GET["xnombre"]);
	      $xcodigo  = CleanCB($_GET["xcodigo"]);
	      $xope     = CleanID($_GET["xope"]);
	      $xidope   = ($xope==6)?CleanID($_GET["xidope"]):false;//**Pendiente busqueda inventarios
	      $xmov     = CleanText($_GET["xmov"]);
	      $datos    = obtenerKardexMovimientosInventario($donde,$desde,$hasta,$xfamilia,
							    $xmarca,$xope,$xmov,$xnombre,
							    $xcodigo,$xinvent,$esInvent,
							    false,false,false,
							    false,true);

	      echo $datos;
	      exit();
	      break;
	      

	    case "kdxMovimientosInventario":
	      $hasta    = CleanFechaES($_GET["hasta"]);
	      $desde    = CleanFechaES($_GET["desde"]);
	      $xfamilia = CleanID($_GET["familia"]);
	      $xmarca   = CleanID($_GET["marca"]);
	      $esInvent = ( $_GET["xinventario"] == "Inventario" )? true:false;
	      $xinvent  = CleanID($_GET["xidinventario"]);
	      $donde    = CleanID($_GET["xlocal"]);
	      $xnombre  = CleanText($_GET["xnombre"]);
	      $xcodigo  = CleanCB($_GET["xcodigo"]);
	      $xope     = CleanID($_GET["xope"]);
	      $xidope   = ($xope==6)?CleanID($_GET["xidope"]):false;//**Pendiente busqueda inventarios
	      $xmov     = CleanText($_GET["xmov"]);
	      $listadesde = CleanInt($_GET["xlistadesde"]);
	      $numerofilas = CleanInt($_GET["xnumfilas"]);
	      $datos    = obtenerKardexMovimientosInventario($donde,$desde,$hasta,$xfamilia,
							     $xmarca,$xope,$xmov,$xnombre,
							     $xcodigo,$xinvent,$esInvent,
							     false,false,$numerofilas,
							     $listadesde,false);
	      
	      VolcandoXML( Traducir2XML($datos),"Kardex");
	      exit();
	      break;

	    case "kdxAlmacenInventario":
	      $donde    = CleanID($_GET["xlocal"]);
	      $xfamilia = CleanID($_GET["familia"]);
	      $xmarca   = CleanID($_GET["marca"]);
	      $xnombre  = CleanText($_GET["xnombre"]);
	      $esInvent = ( $_GET["xinventario"] == "Inventario" )? true:false;
	      $xcodigo  = CleanCB($_GET["xcodigo"]);
	      $xstock   = CleanCB($_GET["xstock"]);
	      $datos    = obtenerKardexInventarioAlmacen($donde,$xfamilia,$xmarca,$xstock,
							 $xnombre,$xcodigo,$esInvent);
	      
	      VolcandoXML( Traducir2XML($datos),"kardexAlmacen");
	      exit();
	      break;

	    case "xMovimientosExistenciasAlmacen":

	      $idalmacen  = CleanID($_GET["id"]);//IdAlmacen
	      $id         =  getIdFromIdAlmacen($idalmacen);
	      $oProducto  = getDatosProductosExtra($id,'nombretodos');
	      $producto   = $oProducto["Nombre"];
	      $empaque    = $oProducto["Empaque"];
	      $unidxemp   = $oProducto["UndxEmp"];
	      $menudeo    = $oProducto["Menudeo"];
	      $unidades   = $oProducto["Und"];
	      $serie      = $oProducto["Serie"];

	      $lote       = $oProducto["Lote"];
	      $fv         = $oProducto["Vence"];
	      $esserie    = ($serie)? 'false':'true';
	      $eslote     = ($lote)?  'false':'true';
	      $esfv       = ($fv)?    'false':'true';
	      $esCarrito  = 'true';

	      $donde      = CleanID($_SESSION["LocalMostrado"]);
	      $costototal = 0;
	      $existencias= 0;
	      $xajuste    = 0;
	      $cCantidad  = 0;//Aqui falta cargar lo que hay en carrito
	      $igv        = getSesionDato("IGV");
	      $btnrtn     = "false";
	      $btnrtxt    = " Volver Stock ";
	      $btnrtnacc  = "cancelar()";
	      $btnsexist  = "true";
	      $mval       = "false";//Movimiento
	      $eval       = "true";//Existencia
	      $headkardex = "false";
	      $theadkardex= "Kardex";	      
	      $rheadkardex= "false";
	      $tkardex    = "false";
	      $tmoviv     = "true";
	      $texist     = "true";
	      $selmval    = ($mval=="false")? 'selected="true"':'';
	      $seleval    = ($eval=="false")? 'selected="true"':'';
	      $detalle     = ($menudeo)? "(".$unidxemp." ".$unidades." x ".$empaque.")":""; 
	      $LoadResumen = "buscarMovimientosExistencias()";
	      $btnAceptar  = "guardarCantidadCarritoAlmacen()";
	      $btnCancelar = "salirKardexCarritoAlmacen(".$idalmacen.",".$id.")";

	      include("xulkardexproducto.php");
	      exit();
	      break;

	    case "xMovimientosExistenciasKardex":

 	      $id         = CleanID($_GET["xproducto"]);
	      $donde      = CleanID($_GET["xlocal"]);
	      $idalmacen  = 0;
	      $xajuste    = 0;
	      $cCantidad  = "";//Aqui falta cargar lo que hay en carrito
	      $igv        = getSesionDato("IGV");
	      $oProducto  = getDatosProductosExtra($id,'nombretodos');
	      $producto   = $oProducto["Nombre"];
	      $empaque    = $oProducto["Empaque"];
	      $unidxemp   = $oProducto["UndxEmp"];
              $menudeo    = $oProducto["Menudeo"];
	      $unidades   = $oProducto["Und"];
	      $serie      = $oProducto["Serie"];
	      $lote       = $oProducto["Lote"];
	      $fv         = $oProducto["Vence"];

	      $esserie    = ($serie)? 'false':'true';
	      $eslote     = ($lote)?  'false':'true';
	      $esfv       = ($fv)?    'false':'true';
	      $esCarrito  = 'true';

	      $costototal = 0;
	      $existencias= 0;
	      $btnrtn     = "false";
	      $btnrtnacc  = "salirKardexProducto()";
	      $btnrtxt    = " Volver kardex ";
	      $btnsexist  = "true";
	      $mval       = "false";//Movimiento
	      $eval       = "true";//Existencia
	      $headkardex = "false";
	      $theadkardex= "Kardex";	      
	      $rheadkardex= "false";
	      $tkardex    = "true";
	      $tmoviv     = "true";
	      $texist     = "true";
	      $selmval    = ($mval=="false")? 'selected="true"':'';
	      $seleval    = ($eval=="false")? 'selected="true"':'';
	      $detalle     = ($menudeo)? "(".$unidxemp." ".$unidades." x ".$empaque.")":""; 
	      $LoadResumen = "buscarMovimientosExistencias()";
	      $btnAceptar  = "guardarCantidadCarritoAlmacen()";
	      $btnCancelar = "salirKardexCarritoAlmacen(".$idalmacen.",".$id.")";

	      include("xulkardexproducto.php");
	      exit();
	      break;

	    case "xMovimientosExistenciasKardexInventario":

 	      $id         = CleanID($_GET["xproducto"]);
	      $donde      = CleanID($_GET["xlocal"]);
	      $inventario = CleanText($_GET["xinventario"]);

	      $oProducto  = getDatosProductosExtra($id,'nombretodos');
	      $producto   = $oProducto["Nombre"];
	      $empaque    = $oProducto["Empaque"];
	      $unidxemp   = $oProducto["UndxEmp"];
              $menudeo    = $oProducto["Menudeo"];
	      $unidades   = $oProducto["Und"];
	      $serie      = $oProducto["Serie"];
	      $lote       = $oProducto["Lote"];
	      $fv         = $oProducto["Vence"];

	      $esserie    = ($serie)? 'false':'true';
	      $eslote     = ($lote)?  'false':'true';
	      $esfv       = ($fv)?    'false':'true';
	      $esCarrito  = 'true';

	      $costototal = 0;
	      $existencias= 0;
	      $btnrtn     = "false";
	      $btnrtnacc  = "salirKardexProductoInventario()";
	      $btnrtxt    = " Volver ".$inventario;
	      $btnsexist  = "true";
	      $mval       = "false";//Movimiento
	      $eval       = "true";//Existencia
	      $headkardex = "false";
	      $theadkardex= "Kardex";	      
	      $rheadkardex= "false";
	      $tkardex    = "true";
	      $tmoviv     = "true";
	      $texist     = "true";
	      $selmval    = ($mval=="false")? 'selected="true"':'';
	      $seleval    = ($eval=="false")? 'selected="true"':'';
	      $detalle     = ($menudeo)? "(".$unidxemp." ".$unidades." x ".$empaque.")":""; 
	      $LoadResumen = "buscarMovimientosExistencias()";
	      $btnAceptar  = "guardarCantidadCarritoAlmacen()";
	      $btnCancelar = "salirKardexCarritoAlmacen(".$idalmacen.",".$id.")";

	      include("xulkardexproducto.php");
	      exit();
	      break;

	    case "xInventarioAlmacenCarrito":

 	      $id          = CleanID($_GET["xproducto"]);
	      $idlocal     = CleanID($_GET["xlocal"]);
	      $idalmacen   = CleanInt($_GET["xalmacen"]);
	      $xinventario = CleanText($_GET["xinventario"]);

	      $xajuste     = CleanInt($_GET["xajuste"]);
	      $idlocal     = (!$idlocal)? CleanID($_SESSION["LocalMostrado"]):$idlocal;	
	      $donde       = (!$idlocal)? getSesionDato("IdTienda"):$idlocal;
	      //$idalmacen  = CleanID($_GET["id"]);//IdProducto
	      //$id         = getIdFromIdAlmacen($idalmacen);
	      $igv         = getSesionDato("IGV");
	      $oProducto   = getDatosProductosExtra($id,'nombretodos');
	      $producto    = $oProducto["Nombre"];
	      $empaque     = $oProducto["Empaque"]; 
	      $unidxemp    = $oProducto["UndxEmp"];
	      $menudeo     = $oProducto["Menudeo"];
	      $unidades    = $oProducto["Und"];
	      $serie       = $oProducto["Serie"];
	      $lote        = $oProducto["Lote"];
	      $fv          = $oProducto["Vence"];
	      $esserie     = ($serie)? 'false':'true';
	      $eslote      = ($lote)?  'false':'true';
	      $esfv        = ($fv)?    'false':'true';
	      $esCarrito   = 'false';
	      $costototal  = 0;
	      $existencias = 0;
	      $btnrtn      = "true";
	      $btnrtnacc   = "";
	      $btnsexist   = "true";
	      $btnrtxt     = " Volver Stock ";
	      $mval        = "true";//Movimiento
	      $eval        = "false";//Existencia
	      $headkardex  = "false";	      
	      $theadkardex = $xinventario." Existencias - Elegir ".$xajuste." ".$unidades.".";
	      $rheadkardex = "true";
	      $tkardex     = "false";
	      $tmoviv      = "true";
	      $texist      = "true";
	      $cCantidad   = "";//Aqui falta cargar lo que hay en carrito
	      $selmval     = ( $mval == "false" )? 'selected="true"':'';
	      $seleval     = ( $eval == "false" )? 'selected="true"':'';
	      $detalle     = ( $menudeo )? "(".$unidxemp." ".$unidades." x ".$empaque.")":""; 
	      $LoadResumen = "buscarMovimientosExistenciasCarrito()";
	      $btnAceptar  = "guardarCantidadAjuste()";
	      $btnCancelar = "parent.volverStock()";

	      include("xulkardexproducto.php");
	      exit();
	      break;

            case "xExistenciasAlmacenCarrito":

 	      $id          = CleanID($_GET["xproducto"]);
	      $idlocal     = (isset($_GET["xlocal"]))?CleanID($_GET["xlocal"]):false;
	      $idalmacen   = CleanInt($_GET["xalmacen"]);
	      $idlocal     = (!$idlocal)? CleanID($_SESSION["LocalMostrado"]):$idlocal;	
	      $donde       = (!$idlocal)? getSesionDato("IdTienda"):$idlocal;
	      //$idalmacen  = CleanID($_GET["id"]);//IdProducto
	      //$id         = getIdFromIdAlmacen($idalmacen);
	      $igv         = getSesionDato("IGV");
	      $oProducto   = getDatosProductosExtra($id,'nombretodos');
	      $producto    = $oProducto["Nombre"];
	      $empaque     = $oProducto["Empaque"];
	      $unidxemp    = $oProducto["UndxEmp"];
	      $menudeo     = $oProducto["Menudeo"];
	      $unidades    = $oProducto["Und"];
	      $serie       = $oProducto["Serie"];
	      $lote        = $oProducto["Lote"];
	      $fv          = $oProducto["Vence"];
	      $esserie     = ($serie)? 'false':'true';
	      $eslote      = ($lote)?  'false':'true';
	      $esfv        = ($fv)?    'false':'true';
	      $esCarrito   = 'false';
	      $costototal  = 0;
	      $existencias = 0;
	      $xajuste     = 0;
	      $btnrtn      = "true";
	      $btnrtnacc   = "";
	      $btnrtxt     = " Volver Stock ";
	      $btnsexist   = "true";
	      $mval        = "true";//Movimiento
	      $eval        = "false";//Existencia
	      $headkardex  = "false";	      
	      $theadkardex = "Carrito Almacén - Elegir Existencias";
	      $rheadkardex = "true";
	      $tkardex     = "false";
	      $tmoviv      = "true";
	      $texist      = "true";
	      $cCantidad   = "";//Aqui falta cargar lo que hay en carrito
	      $selmval     = ( $mval == "false" )? 'selected="true"':'';
	      $seleval     = ( $eval == "false" )? 'selected="true"':'';
	      $detalle     = ( $menudeo )? "(".$unidxemp." ".$unidades." x ".$empaque.")":""; 
	      $LoadResumen = "buscarMovimientosExistenciasCarrito()";
	      $btnAceptar  = "guardarCantidadCarritoAlmacen()";
	      $btnCancelar = "salirKardexCarritoAlmacen(".$idalmacen.",".$id.")";

	      include("xulkardexproducto.php");
	      exit();
	      break;

	    case "kdxFinalizaInventario":
	      $xInvent   = CleanID($_GET["xidinventario"]);//>IdInventario
	      $xLocal    = CleanID($_GET["xlocal"]);
	      $almacenes = new almacenes;

	      registraCambiosInventario($xInvent," Estado = 'Finalizado', ".
					" FechaInventarioFin = NOW() ");
	      $almacenes->actualizaEstadoInventarioLocal($xLocal);
	      echo 1;
	      exit();
	      break;

	    case "kdxIgualExistencias":

		$IdArticulo    = CleanID($_GET["xarticulo"]);
		$IdProducto    = CleanID($_GET["xproducto"]);
		$IdLocal       = CleanID($_GET["xlocal"]);
		$IdPedido      = CleanID($_GET["xpedido"]);//IdPedido
		$xIdComprobante = CleanID($_GET["xcomprobante"]);//IdComprobante
		$PVD           = CleanDinero($_GET["xpvd"]);
		$PVDD          = CleanDinero($_GET["xpvdd"]);
		$PVC           = CleanDinero($_GET["xpvc"]);
		$PVCD          = CleanDinero($_GET["xpvcd"]);
		$SerieVence    = CleanCadena($_GET["serievence"]);
		$Series        = CleanCadena($_POST["numerosdeserie"]);
		$xIdInvent     = CleanID($_GET["xidinventario"]);//>IdInventario
		$tipInvent     = CleanText($_GET["xtipoinventario"]);//TipoInventario(inicial,final,etc)
		$esInvent      = ( $_GET["xinventario"] == "Inventario" )? true:false;
		$esPendInvent  = ( $_GET["xestinvent"] == "Pendiente" )? true:false;//EstadoInventario

		//Control si Inventario es Pendiente => IdInventrario != 0
		if ( $esPendInvent && $xIdInvent == 0 )  return;

		//Inventario? ó Inventario Nuevo? 
		$IdInventario = ($esInvent)? registraInventario($tipInvent,$IdLocal,$IdPedido,
								$IdComprobante):0;

		//Agregar lista Inventario
		actualizaIdInventarioToKardex($IdLocal,$IdInventario,$IdProducto);

		//Ventas Precios
		registrarPreciosVentaAlmacen($PVD,$PVDD,$PVC,$PVCD,$IdArticulo);

		if($IdInventario && $esInvent)
		  {    
		    $almacenes = new almacenes;
		    $almacenes->actualizaEstadoInventario($IdLocal,$IdProducto);
		  }

		//Retorna IdInventario, IdPedido & IdComprobante
		echo "1~".$IdInventario."~".$IdPedido."~".$IdComprobante;
	      exit();
	      break;


	    case "kdxSalidaExistencias":

		$IdArticulo    = CleanID($_GET["xarticulo"]);
		$IdProducto    = CleanID($_GET["xproducto"]);
		$IdLocal       = CleanID($_GET["xlocal"]);
		$IdPedido      = CleanID($_GET["xpedido"]);//IdPedido
		$xIdComprobante = CleanID($_GET["xcomprobante"]);//IdComprobante
 		$Costo         = CleanDinero($_GET["xcosto"]);
		$Precio        = CleanDinero($_GET["xprecio"]);
		$PVD           = CleanDinero($_GET["xpvd"]);
		$PVDD          = CleanDinero($_GET["xpvdd"]);
		$PVC           = CleanDinero($_GET["xpvc"]);
		$PVCD          = CleanDinero($_GET["xpvcd"]);
		$Ajustes       = CleanCadena($_GET["xajustes"]);
		$SerieVence    = CleanCadena($_GET["serievence"]);
		$Series        = CleanCadena($_POST["numerosdeserie"]);
		$esSerie       = ( $_GET["esserie"] == "true" )? true:false;
		$LoteVence     = CleanCadena($_GET["lotevence"]);
		$xObs          = CleanText($_POST["xobservacion"]);
		$OpeAjuste     = CleanText($_GET["xopeajuste"]);// >IdKardexAjusteOperacion
		$xIdInvent     = CleanID($_GET["xidinventario"]);//>IdInventario
		$tipInvent     = CleanText($_GET["xtipoinventario"]);//TipoInventario(inicial,final,etc)
		$esAjuste      = ( $_GET["xinventario"] == "Ajuste" )? true:false;
		$esInvent      = ( $_GET["xinventario"] == "Inventario" )? true:false;
		$esPendInvent  = ( $_GET["xestinvent"] == "Pendiente" )? true:false;//EstadoInventario
		$esNewInvent   = ( $esInvent && !$esPendInvent )? true:false;
		$esPedido      = ( $esPendInvent && $xIdComprobante!=0 )? true:false;
		$Motivo        = ( $esAjuste )? 7:8;//7:Ajuste,8:Inventario>IdMotivoAlbaran
		$Operacion     = ( $esAjuste )? 5:6;//5:Ajuste,6:Inventario>IdKardexOperacion
		$Destino       = $IdLocal;
		$Origen        = $IdLocal; 
		$Codigo        = getNextId('ges_comprobantes','NComprobante');
 		$campoxdato    = " EstadoDocumento = 'Pendiente' ";

		//Control si Inventario es Pendiente => IdInventrario != 0
		if ( $esPendInvent && $xIdInvent == 0 )  return;

		//Valida Kardex pedido detalle
		if( ValidarAjusteExistenciasDetalle($Ajustes,$Series,$IdProducto,
						    $esSerie,$Origen) ) return;

		//Ventas AlbaranInt
		$IdComprobante = ($esPedido)? $xIdComprobante:registrarAlbaranOrigen($Destino,$Origen,
										     $Motivo,$Codigo);
		//Ajustes 
		//$Ajuste -> ~IdPedidoDet:Cantidad:Precio
		//$Series -> ~IdPedidoDet:Series,Series		
		$aAjustes = explode("~",$Ajustes);
		$nAjustes = count($aAjustes);
		$aSeries  = explode("~",$Series);
		$nSeries  = count($aSeries);
		
		for( $i=0; $i < $nAjustes ; $i++)
		  {

		    $Ajuste      = explode(":",$aAjustes[$i]);
		    $IdPedidoDet = $Ajuste[0];
		    $Cantidad    = $Ajuste[1];

		    //Venta AlbaranInt Detalle
		    registrarDetalleTrasladoSalida($IdProducto,$Cantidad,$Costo,
						   $Precio,$IdComprobante,
						   $IdPedidoDet,$esSerie,
						   $LoteVence);
		    //Series 
		    if($esSerie)
		      {

			$Serie   = explode(":",$aSeries[$i]);
			$cSeries = $Serie[1];
			registrarAjusteSalidaSeries($Origen,$IdComprobante,$IdProducto,
						    $cSeries,$IdPedidoDet);

		      }
		  }

		//Venta AlbaranInt Importes
		ConsolidaDetalleVenta($IdComprobante,false);

		//Inventario? ó Inventario Nuevo? 
		$IdInventario = ($esInvent)? registraInventario($tipInvent,$IdLocal,$IdPedido,
								$IdComprobante):0;

		//Inventario? Nuevo IdComprobante?
		if( $esInvent && !$esPedido )
		  registraCambiosInventario($IdInventario,' IdComprobante = '.$IdComprobante);

		//kardex IdAjuste
		$IdOpeAjuste = getIdAjusteOperacion($OpeAjuste,'Salida');

		//Kardex Salida
		registrarAjusteSalidaKardex($IdComprobante,$IdLocal,$Operacion,
					    $IdOpeAjuste,$IdInventario,$xObs);

		//Ventas Precios
		registrarPreciosVentaAlmacen($PVD,$PVDD,$PVC,$PVCD,$IdArticulo);

		//Retorna IdInventario, IdPedido & IdComprobante
		echo "1~".$IdInventario."~".$IdPedido."~".$IdComprobante;
	      exit();
	      break;


	    case "kdxEntradaExistencias":

		$IdArticulo    = CleanID($_GET["xarticulo"]);
		$IdProducto    = CleanID($_GET["xproducto"]);
		$IdLocal       = CleanID($_GET["xlocal"]);
		$xIdPedido     = CleanID($_GET["xpedido"]);//IdPedido
		$IdComprobante = CleanID($_GET["xcomprobante"]);//IdComprobante
 		$Costo         = CleanDinero($_GET["xcosto"]);
		$Precio        = CleanDinero($_GET["xprecio"]);
		$PVD           = CleanDinero($_GET["xpvd"]);
		$PVDD          = CleanDinero($_GET["xpvdd"]);
		$PVC           = CleanDinero($_GET["xpvc"]);
		$PVCD          = CleanDinero($_GET["xpvcd"]);
		$Cantidad      = CleanFloat($_GET["xajuste"]);
		$SerieVence    = CleanCadena($_GET["serievence"]);
		$Series        = CleanCadena($_POST["numerosdeserie"]);
		$esSerie       = ( $_GET["esserie"] == "true" )? true:false;
		$LoteVence     = CleanCadena($_GET["lotevence"]);
		$xObs          = CleanText($_POST["xobservacion"]);
		$OpeAjuste     = CleanText($_GET["xopeajuste"]);// >IdKardexAjusteOperacion
		$xIdInvent     = CleanID($_GET["xidinventario"]);//>IdInventario
		$tipInvent     = CleanText($_GET["xtipoinventario"]);//TipoInventario(inicial,final,etc)
		$esAjuste      = ( $_GET["xinventario"] == "Ajuste" )? true:false;
		$esInvent      = ( $_GET["xinventario"] == "Inventario" )? true:false;
		$esPendInvent  = ( $_GET["xestinvent"] == "Pendiente" )? true:false;//EstadoInventario
		$esNewInvent   = ( $esInvent && !$esPendInvent )? true:false;
		$esPedido      = ( $esPendInvent && $xIdPedido!=0 )? true:false;
		$Motivo        = ( $esAjuste )? 7:8;//7:Ajuste,8:Inventario>IdMotivoAlbaran
		$Operacion     = ( $esAjuste )? 5:6;//5:Ajuste,6:Inventario>IdKardexOperacion
		$Destino       = $IdLocal;
		$Origen        = $IdLocal; 
		$Codigo        = getNextId('ges_comprobantesprov','IdComprobanteProv');
 		$campoxdato    = " EstadoPago='Exonerado',EstadoDocumento='Confirmado',".
		                 " ImportePendiente=0";

		//Control si Inventario es Pendiente => IdInventrario != 0
		if ( $esPendInvent && $xIdInvent == 0 )  return;

		//Compras AlbaranInt

		$IdPedido = ( $esPedido )? $xIdPedido:registrarAlbaranDestino($Destino,$Origen,
									      $Motivo,$Codigo,
									      'TrasLocal');
		//Compras AlbaranInt IdPedidodet
		$IdPedidoDets = $IdPedido;

		//Compras AlbaranInt Detalle
		$IdPedidoDet = registrarDetalleTrasladoEntrada($IdPedido,$IdProducto,$LoteVence,
							       $Cantidad,$Costo,$Precio,$esSerie);
		//Compras AlbaranInt Detalle Series?
		if($esSerie)   registrarAjusteEntradaSeries($IdPedido,$IdPedidoDet,
							    $IdProducto,$Series,$SerieVence);
		//Compras AlbaranInt Importes
		ConsolidaDetalleCompra($IdPedido,false);

		//Inventario? ó Inventario Nuevo? 
		$IdInventario = ($esInvent)? registraInventario($tipInvent,$IdLocal,
								$IdPedido,$IdComprobante):0;

		//Inventario? Nuevo IdPedido?
		if( $esInvent && !$esPedido )
		  registraCambiosInventario($IdInventario,'IdPedido = '.$IdPedido);

		//kardex IdAjuste
		$IdOpeAjuste = getIdAjusteOperacion($OpeAjuste,'Entrada');

		//Kardex Entrada
		registrarAjusteEntradaKardex($IdPedido,$IdPedidoDet,$IdLocal,$Operacion,
					     $IdOpeAjuste,$IdInventario,$xObs);

		//Pedido & Compras Estados
		actualizarStatusPedido($IdPedido,'2');

		//Compras Estado Documento & Pago AlbaranInt
		sModificarCompra($IdPedido,$campoxdato,false,false);
		
		//Ventas Precios
		registrarPreciosVentaAlmacen($PVD,$PVDD,$PVC,$PVCD,$IdArticulo);

		//Retorna IdInventario, IdPedido & IdComprobante
		echo "1~".$IdInventario."~".$IdPedido."~".$IdComprobante;
	      exit();
	      break;

	    case "kdxInventarioAlmacen":

	      $idproducto = CleanID($_GET["idproducto"]);
	      $IdLocal    = CleanID($_GET["xlocal"]);
	      $inventario = obtenerInventarioProductoFifo($idproducto,$IdLocal);
	      $n          = count($inventario);
	      $arr        = Array();
	      $pedidodet  = Array();

	      for($i=0;$i<$n;$i++)
		{
		  $row  = Row( obtenerPedidoDet( $inventario[$i][2]) );

		  array_push($arr,$inventario[$i][0]);
		  array_push($arr,$inventario[$i][1]);
		  array_push($arr,$row['IdPedidoDet']);
		  array_push($arr,$row['Lote']);
		  array_push($arr,$row['FechaVencimiento']);
		  array_push($arr,$row['Serie']);
		  array_push($arr,$row['IdPedido']);
		}

	      echo implode("~",$arr);
	      exit();
	      break;

             case "setPrecioCarritoAlmacen":

	       $id      = CleanID($_GET["xid"]);
	       $xdato   = CleanDinero($_GET["xdato"]);
	       $mover   = getSesionDato("CarritoMover");

	       $mover['Precio'.$id] = $xdato;
	       setSesionDato("CarritoMover",$mover);	
	       exit();
	       break;

             case "setModoCarritoAlmacen";
	       $xmodo   = CleanText($_GET["xmodo"]);
	       setSesionDato("ModoCarritoAlmacen",$xmodo);
	       exit();
	       break;

}


?>
