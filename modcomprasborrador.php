<?php
include("tool.php");
if (!getSesionDato("IdTienda")){
  session_write_close();
  //header("Location: #");
  exit();
}
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
include("xulcomprasborrador.php"); 
?>


