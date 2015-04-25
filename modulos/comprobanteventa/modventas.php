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
	case "mostrarComprobantes":
	  #Valida Suscripciones
	  include("xulventas.php"); 
	  break;
}

?>


