<?php 
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
   exit();
}

$modo = CleanText($_GET['modo']);
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
$DescuentoGral  = getSesionDato("DescuentoTienda");
$MetodoRedondeo = getSesionDato("MetodoRedondeo");
$MargenUtilidad = getSesionDato("MargenUtilidad");
$COPImpuesto    = getSesionDato("COPImpuesto");

switch ($modo) {
  case "mostrarProductosExhibicion":
    $codigo         = (isset($_GET["codigo"]))? CleanText($_GET["codigo"]):'';
    $descripcion    = (isset($_GET["descripcion"]))? CleanText($_GET["descripcion"]):'';
    $idmarca        = (isset($_GET["idmarca"]))? CleanID($_GET["idmarca"]):0;
    $txtMoDet       = getGiroNegocio2txt();
    $esWESL         = ( $txtMoDet[0] == "WESL" );
    $wesl           = ( $esWESL )?'false':'true';
    $idfamilia      = (isset($_GET["idfamilia"]))? CleanID($_GET["idfamilia"]):0; 
    if (!isset($codigo) ) $codigo = '';
    if (!isset($descripcion) ) $descripcion = '';
    if ( $descripcion == 'todos' || $descripcion == 'all' ) $descripcion = '';
    /* if ( $idmarca != 0 ||  $idfamilia != 0 || $codigo !='' ) */
    /*   if ( $descripcion == 'Descripcion del Producto') $descripcion = ''; */
    if (!isset($idmarca))   $idmarca   = 0;
    if (!isset($idfamilia)) $idfamilia = 0; 
    
    $familias = getFamiliasProductos();
    $marcas   = getMarcasProductos();
    $locales  = getLocalesPrecios($IdLocal);
    //$datos = getDetalleProductoPrecios($codigo,$descripcion,$idmarca,$idfamilia, $IdLocal);
    include("xulexhibicion.php");
    break;
    
  case "eliminarNuevosPV":
    $listalocal = (isset($_GET["listalocal"]))? $_GET["listalocal"]:'';	
    $IdLocal    = getSesionDato("IdTienda");
    if($listalocal!=0)
      $IdLocal = $listalocal;
    echo eliminarNuevosPVAlmacen($IdLocal);
    exit(); 		
    break;

  case "listarNuevosPV":
    $listalocal = $_GET["listalocal"];	
    $IdLocal    = getSesionDato("IdTienda");
    if($listalocal!=0)
      $IdLocal = $listalocal;
    echo listarNuevosPVAlmacen($IdLocal);
    exit(); 		
    break;

  case "actualizarExhibicion":
    $listalocal = CleanID($_GET["listalocal"]);	
    $IdLocal    = getSesionDato("IdTienda");
    $IdLocal    = ($listalocal!=0)? $listalocal:$IdLocal;
    $SM         = CleanFloat( $_GET["SM"]  );
    $SEM        = CleanFloat( $_GET["SEM"] );
    $SE         = CleanFloat( $_GET["SE"]  );
    $ST         = ( CleanText( $_GET["ST"] ) == "true" )? 1:0;
    $SW         = ( CleanText( $_GET["SW"] ) == "true" )? 1:0;
    $SI         = ( CleanText( $_GET["SI"] ) == "true" )? 1:0;	
    $idproducto = CleanID($_GET["idproducto"]);
    echo actualizarExhibicionAlmacen($SM,$SE,$SEM,$ST,$SW,$SI,$idproducto,$IdLocal);
    $xsync      = setSyncTPV('Stock');//Sync Exhibicion
    exit(); 		
    break;

  case "guardarPreciosVenta":
    $listalocal = $_GET["listalocal"];	
    $IdLocal    = getSesionDato("IdTienda");
    if($listalocal!=0)
      $IdLocal = $listalocal;
    $PV  = CleanDinero($_GET["PV"]);
    $PVD = CleanDinero($_GET["PVD"]);
    $MDS = CleanText($_GET["MDS"]);
    $idproducto = CleanID($_GET["idproducto"]);
    echo guardarPreciosVentaAlmacen($PV,$PVD,$MDS,$idproducto,$IdLocal);
    exit(); 		
    break;

  case "eliminarCambiosPV":
    $listalocal = $_GET["listalocal"];	
    $IdLocal    = getSesionDato("IdTienda");
    if($listalocal!=0)
      $IdLocal = $listalocal;
    $PV  = CleanDinero($_GET["PV"]);
    $PVD = CleanDinero($_GET["PVD"]);
    $MDS = CleanText($_GET["MDS"]);
    $idproducto = CleanID($_GET["idproducto"]);
    echo eliminarCambiosPreciosVentaAlmacen($PV,$PVD,$MDS,$idproducto,$IdLocal);
    exit(); 		
    break;

  case "mostrarProductosAlmacen":
    
    $IdLocal     = getSesionDato("IdTienda");
    $idfamilia   = CleanID($_GET["idfamilia"]);
    $idmarca     = CleanID($_GET["idmarca"]);
    $idlistarPV  = CleanText($_GET["idlistarPV"]);
    $descripcion = CleanText($_GET["descripcion"]);
    $codigo      = CleanText($_GET["codigo"]);
    $listarTodo  = CleanText($_GET["listarTodo"]);
    $listalocal  = CleanText($_GET["listalocal"]);
    $listarVitrina= CleanText($_GET["listarVitrina"]);
    
    $datos       = DetalleProductosExhibicion($codigo,$descripcion,$idmarca,
                                              $idfamilia,$IdLocal,$idlistarPV,
                                              $listarTodo,$listalocal,$listarVitrina);
    VolcandoXML( Traducir2XML($datos),"productosAlmacen");
    exit();
    break;

  case "actualizarCostoOperativo":
    $listalocal = CleanID($_GET["listalocal"]);	
    $IdLocal    = getSesionDato("IdTienda");
    $IdLocal    = ($listalocal!=0)? $listalocal:$IdLocal;
    $COP        = $_GET["COP"];
    $idproducto = CleanID($_GET["idproducto"]);
    echo guardarCostoOperativo($COP,$idproducto,$IdLocal);
    exit(); 		
    break;

  case "actualizarPrecioEmpaque":
    $listalocal = CleanID($_GET["listalocal"]);	
    $IdLocal    = getSesionDato("IdTienda");
    $IdLocal    = ($listalocal!=0)? $listalocal:$IdLocal;
    $PVDE       = $_GET["PVDE"];
    $PVDED      = $_GET["PVDED"];
    $idproducto = CleanID($_GET["idproducto"]);
    echo guardarPrecioVentaEmpaque($PVDE,$PVDED,$idproducto,$IdLocal);
    exit(); 		
    break;

}
?>
