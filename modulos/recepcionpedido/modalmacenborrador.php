<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}

//$modo=recibirProductosAlmacen
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
$modo    = CleanText($_GET["modo"]);

switch($modo) {
        case "recibirProductosAlmacen":
	  include("xulalmacenborrador.php"); 
	  break;
}
?>


