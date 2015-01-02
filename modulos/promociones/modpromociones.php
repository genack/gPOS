<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  exit();
}

$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
$modo    = CleanText($_GET["modo"]);

switch($modo) {
  case "mostrarPromociones":
    $catcliente = genXulComboPromocionCliente($IdLocal);
    include("xulpromociones.php");
    break;
    
  case "GuardaPromocion":
    $Promocion     = CleanText($_POST["xpromo"]);
    $Modalidad     = CleanText($_POST["xmod"]);
    $Tipo          = CleanText($_POST["xtipo"]);
    $InicioPeriodo = CleanText($_POST["xinicio"]);
    $FinPeriodo    = CleanText($_POST["xfin"]);
    $MontoActual   = CleanText($_POST["xmonto"]);
    $CatCliente    = CleanID($_POST["xcatclient"]);
    $Producto1     = CleanText($_POST["xprod1"]);
    $Producto2     = CleanText($_POST["xprod2"]);
    $Descuento     = CleanText($_POST["xdesc"]);
    $Bono          = CleanText($_POST["xbono"]);
    $DispLocal 	   = CleanText($_POST["xdisplocal"]);
    $IdPromocion   = CleanID($_POST["xidpromo"]);
    $Prioridad	   = CleanID($_POST["xprioridad"]);
    $Estado	   = CleanText($_POST["xestado"]);
    $TipoVenta	   = CleanText($_POST["xtipoventa"]);
    
    $Promocion     = str_replace(";",'.',$Promocion);
    $Promocion     = str_replace("~",' ',$Promocion);
    
    if($Estado == 'Ejecucion')
      {
	if($Modalidad == 'MontoCompra' && $MontoActual == 0  ) return;
	if($Modalidad == 'NumeroCompra' &&  $CatCliente == 0 ) return;
	
	switch($Tipo)
	  {
	  case 'Descuento': if($Descuento == 0) return; break;
	  case 'Bono'     : if($Bono == 0)      return; break;
	  case 'Producto' : if($Producto1 == '' || $Producto1 == 0) return; break;
	  }
      }
    
    if($Producto1 != '' && $Producto1 != '0')
      if(!checkCodigoBarra($Producto1)){ echo "0"; return; }
    
    if($Producto2 != '' && $Producto2 != '0')
      if(!checkCodigoBarra($Producto2)){ echo "0"; return ; }
    
    $oPromocion     = new promocion;
    $IdPromocion    = ($IdPromocion == 0)? $oPromocion->getPromocion($Promocion):$IdPromocion;
    $xIdPromocion   = $oPromocion->getIdPromocion($IdPromocion);
    $opcion         = ( $xIdPromocion )? "Modificar":"Crear";
    
    if( $opcion == 'Finalizado' && $Tipo == 'Bono' ) 
      updateBonoPromocion2Clientes($xIdPromocion);

    echo $id = CrearPromocion($Promocion,$Modalidad,$Tipo,$InicioPeriodo,$FinPeriodo,
			      $MontoActual,$CatCliente,$Producto1,$Producto2,
			      $Descuento,$Bono,$DispLocal,$xIdPromocion,$opcion,
			      $Prioridad,$Estado,$TipoVenta);
    exit();
    break;
    
  case "ObtenerPromociones":
    $FiltroLocal = getSesionDato("IdTienda");
    $FiltroLocal = (getSesionDato("esAlmacenCentral"))?CleanID($_GET["xlocal"]):$FiltroLocal;
    $Desde       = CleanCadena($_GET["xdesde"]);
    $Hasta       = CleanCadena($_GET["xhasta"]);
    $Promocion   = CleanText($_GET["xpromo"]);
    $Estado      = CleanText($_GET["xestado"]);
    $Tipo        = CleanText($_GET["xtipo"]);
    $TipoVenta   = CleanText($_GET["xtipoventa"]);
    $MontoCompra = CleanText($_GET["xmontocompra"]);
    $HistorialCompra  = CleanText($_GET["xhistorialcompra"]);
    $MontoCompra      = ($MontoCompra == 'MontoCompra');
    $HistorialCompra  = ($HistorialCompra == 'HistorialCompra');
    $esSoloLocal   = trim($FiltroLocal);  
    
    $datos = mostrarPromociones($esSoloLocal,$Desde,$Hasta,$Promocion,
				$Estado,$Tipo,$MontoCompra,$HistorialCompra,
				$TipoVenta);
    VolcandoXML( Traducir2XML($datos),"Promociones");
    exit();
    break;
    
  case "mostrarCategoriaClientes":
    $IdLocal = (isset($_GET["xidlocal"]))? CleanID($_GET["xidlocal"]):$IdLocal;
    echo genXulComboPromocionCliente($IdLocal);
    break;

  case "GuardaPromocionCliente":
    $Categoria	    = CleanText($_POST["xcat"]);
    $Descripcion    = CleanText($_POST["xdes"]);
    $MontoDesde     = CleanText($_POST["xmd"]);
    $MontoHasta     = CleanText($_POST["xmh"]);
    $CantidadDesde  = CleanText($_POST["xcd"]); 
    $CantidadHasta  = CleanText($_POST["xch"]); 
    $Motivo         = CleanText($_POST["xmot"]);
    $IdPromocionCliente = CleanID($_POST["xidpc"]);
    $DispLocal          = CleanID($_POST["xidlocal"]);
    $EstadoCategoria    = CleanText($_POST["xstdo"]);
    $IdHistorialVenta   = CleanID($_POST["xidhp"]);
    
    $Categoria      = str_replace(";",".",$Categoria);
    $Categoria      = str_replace("~"," ",$Categoria);
    $Descripcion    = str_replace(";",".",$Descripcion);
    $Descripcion    = str_replace("~"," ",$Descripcion);
    
    $oPromocion     = new promocion;
    if($IdPromocionCliente == 0){
      $IdPromocionCliente   = $oPromocion->getPromocionCliente($Categoria);
    }
    $IdPromocionCliente = $oPromocion->getIdPromocionCliente($IdPromocionCliente);
    $opcion             = ($IdPromocionCliente)? "Modificar":"Crear";
    
    if($EstadoCategoria == 'Ejecucion'){
      switch($Motivo){
      case 'MontoCompra':
	$xMonto = $oPromocion->getMontoCatCliente($MontoDesde,$MontoHasta,
						  $IdPromocionCliente,$Motivo,
						  $DispLocal);
	if($xMonto){
	  echo 'existe';
	  return;
	}
	
	break;
      case 'NumeroCompra':
	$xCantidad = $oPromocion->getCantidadCatCliente($CantidadDesde,$CantidadHasta,
							       $IdPromocionCliente,
							       $Motivo,$DispLocal);
	if($xCantidad){
	  echo 'existe';
	  return;
	}
	
	break;
      case 'Ambos':
	$xMonto = $oPromocion->getMontoCatCliente($MontoDesde,$MontoHasta,
						  $IdPromocionCliente,$Motivo,
						  $DispLocal);
	
	$xCantidad = $oPromocion->getCantidadCatCliente($CantidadDesde,$CantidadHasta,
							$IdPromocionCliente,
							$Motivo,$DispLocal);
	if($xMonto && $xCantidad){
	  echo 'existe';
	  return;
	}
	
	break;
      }
    }

    echo $id = CrearPromocionCliente($Categoria,$Descripcion,$MontoDesde,$MontoHasta,
				     $CantidadDesde,$CantidadHasta,$Motivo,
				     $IdPromocionCliente,$opcion,$DispLocal,
				     $EstadoCategoria,$IdHistorialVenta);
    exit();
    break;

  case "ObtenerPromocionCliente":
    $IdLocal   = CleanID($_GET["xidlocal"]);
    $Desde     = CleanCadena($_GET["xdesde"]);
    $Hasta     = CleanCadena($_GET["xhasta"]);
    $Categoria = CleanText($_GET["xcategoria"]);
    $Estado    = CleanText($_GET["xestado"]);
    
    $datos = mostrarPromocionClientes($IdLocal,$Desde,$Hasta,$Categoria,$Estado);
    VolcandoXML( Traducir2XML($datos),"PromocionesCliente");
    exit();
    break;

  case "mostrarHistorialVentaPeriodo":
    echo genXulComboHistorialVentaPeriodo();	   
    break;

  case "GuardaHistorialVentaPeriodo":
    $HistorialVenta   = CleanText($_POST["xhv"]);
    $HistorialPeriodo = CleanText($_POST["xhp"]);
    $Eliminar         = CleanID($_POST["xelim"]);
    
    $oPromocion     = new promocion;
    $xIdHistorialPeriodo = $oPromocion->getIdHistorialVentaPeriodo($HistorialVenta);
    
    if($Eliminar == 1){
      $checkHVPeriodo = checkHistorialVentaPeriodo($xIdHistorialPeriodo);
      $opcion = 'Modificar';
      if($checkHVPeriodo > 1){
	echo 'existe2';
	return;
      }
    }
    
    if($xIdHistorialPeriodo && $Eliminar == 0){
      echo 'existe1';
      return;
    }
    
    if($HistorialPeriodo == 'nuevo'){
      if(!$xIdHistorialPeriodo)
	$opcion = 'Crear';
    }

    if($HistorialPeriodo != 'nuevo'){
      $xHistorialVenta = $oPromocion->getHistorialVentaPeriodo($HistorialPeriodo);
      if($xHistorialVenta != $HistorialVenta) 
	$opcion = 'Modificar';
    }
    
    echo $id = CrearHistorialVentaPeriodo($HistorialVenta,$HistorialPeriodo,
					  $opcion,$Eliminar);
    exit();
    break;

  case "syncPromociones":
    $xlocal = CleanID($_GET["xlocal"]);
    echo getPromocionesSyncAlmacen($xlocal);
    exit();
    break;
}
?>


