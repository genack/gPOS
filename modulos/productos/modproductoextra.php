<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
$esBTCA  = ( getSesionDato("GlobalGiroNegocio") == "BTCA" )? true:false;
$modo    = CleanText($_GET["modo"]);

switch($modo) {
  case "verProductoInformacion":
    $Indicaciones   = ($esBTCA)? "Indicaciones":"Propiedades Distintivas";
    $Dosificacion   = ($esBTCA)? "Dosificación":"Modo de Uso";
    $CtraIndicacion = ($esBTCA)? "Contra Indicaciones":"Advertencias";
    $Interaccion    = ($esBTCA)? "Interacciones":"Compatibilidad";
    include("xulproductoinfo.php");
    break;

  case "GuardaProductoInformacion":
    $IdProducto     = CleanID($_POST["xidp"]);
    $Indicacion     = CleanText($_POST["xind"]);
    $CtraIndicacion = CleanText($_POST["xcind"]);
    $Interaccion    = CleanText($_POST["xint"]);
    $Dosificacion   = CleanText($_POST["xdos"]);
    
    $oProdInfo       = new productoinformacion;
    $IdProductoInfo = $oProdInfo->getIdProductoInformacion($IdProducto);
    $opcion         = ($IdProductoInfo)? "Modificar":"Crear";
    
    echo $id = CrearProductoInformacion($IdProducto,$Indicacion,$CtraIndicacion,
					$Interaccion,$Dosificacion,$opcion,$IdProductoInfo);
    
    exit();
    break;

  case "ObtenerProductoInformacion":
    $IdProducto = CleanID($_GET["xidp"]);
    $datos      = mostrarProductoInformacion($IdProducto);
    VolcandoXML( Traducir2XML($datos),"ProductoInformacion");
    exit();
    break;
     
}

?>


