<?php 
include("tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}

$modo = CleanText($_GET['modo']);
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);

switch ($modo) {
         case "mostrarProductosPrecios":
	   $codigo      = (isset($_GET["codigo"]))? CleanText($_GET["codigo"]):'';
	   $descripcion = (isset($_GET["descripcion"]))? CleanText($_GET["descripcion"]):'';
	   $idmarca     = (isset($_GET["idmarca"]))? CleanID($_GET["idmarca"]):0;

	   $idfamilia   = (isset($_GET["idfamilia"]))? CleanID($_GET["idfamilia"]):0; 
	   if (!isset($codigo) || $codigo == 'CB/Ref.' || $codigo == '') $codigo = '';
	   if (!isset($descripcion) || $descripcion == '') $descripcion = 'Descripcion del Producto';
	   if ( $descripcion == 'todos' || $descripcion == 'all' ) $descripcion = '';
	   if ( $idmarca != 0 ||  $idfamilia != 0 || $codigo !='' )
	     if ( $descripcion == 'Descripcion del Producto') $descripcion = '';
	   if (!isset($idmarca))   $idmarca   = 0;
	   if (!isset($idfamilia)) $idfamilia = 0; 

	   $familias = getFamiliasProductos();
	   $marcas   = getMarcasProductos();
	   $locales  = getLocalesPrecios($IdLocal);
	   //$datos = getDetalleProductoPrecios($codigo,$descripcion,$idmarca,$idfamilia, $IdLocal);
	   include("xulprecios.php");
	   break;

         case "syncPromociones":
	   $xlocal = CleanID($_GET["xlocal"]);
	   echo getPromocionesSyncAlmacen($xlocal);
	   exit();
	   break;


}
?>
