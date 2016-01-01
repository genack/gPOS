<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}

$IdLocal        = isset($_GET["local"])? CleanID($_GET["local"]):getSesionDato("IdTienda");
$modo           = CleanText($_GET["modo"]);
$Moneda         = getSesionDato("Moneda"); 

$aLista         = obetnerDatosLocalSeleccionado($IdLocal);
$DescuentoGral  = $aLista["Descuento"];
$MetodoRedondeo = $aLista["MetodoRedondeo"];
$COPImpuesto    = $aLista["COPImpuesto"];
$MagenUtilidad  = $aLista["MargenUtilidad"];
$Impuesto       = $aLista["Impuesto"];

switch($modo) {
	case "verInventario":
	  $xload    = "invent";
	  include("xulinventario.php"); 
	  break;
	case "verAjuste":
	  $xload    = "ajust";
	  include("xulinventario.php"); 
	  break;
}
?>


