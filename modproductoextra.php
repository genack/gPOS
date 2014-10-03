<?php
include("tool.php");
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
     
}

?>


