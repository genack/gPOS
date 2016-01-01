<?php
include("../../tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);

$modo = CleanText($_GET["modo"]);
switch($modo) {
  case "verCajaGeneral":
    $blockprov = false;
    $IdArqueo  = CleanID($_GET["xidacg"]);
    $IdLocal   = ($IdArqueo)? CleanID($_GET["xidl"]):$IdLocal;
    $CtaBancaria  = getSesionDato("CuentaBancaria");
    $CtaBancaria2 = getSesionDato("CuentaBancaria2");
    include("arqueo.php");
    break;

}

?>


