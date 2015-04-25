<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}

$IdLocal        = getSesionDato("IdTienda");
$locales        = getLocalesPrecios($IdLocal);
$modo            = CleanText($_GET["modo"]);
$DescuentoGral  = getSesionDato("DescuentoTienda");
$MetodoRedondeo = getSesionDato("MetodoRedondeo");
$COPImpuesto    = getSesionDato("COPImpuesto");
$Moneda         = getSesionDato("Moneda"); 
$MagenUtilidad  = getSesionDato("MargenUtilidad");
$Impuesto       = getSesionDato("IGV");

switch($modo) {
	case "verAjuste":
	  $xload    = "ajust";
	  include("xulinventario.php"); 
	  break;
}
?>


