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
$DescuentoGral  = getSesionDato("DescuentoTienda");
$MetodoRedondeo = getSesionDato("MetodoRedondeo");
$COPImpuesto    = getSesionDato("COPImpuesto");

switch($modo) {
	case "verAjuste":
	  $xload    = "ajust";
	  $IdLocal = (isset($_GET["xlocal"]))? CleanID($_GET["xlocal"]):$IdLocal;
	  include("xulinventario.php"); 
	  break;
}
?>


