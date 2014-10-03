<?php

include("tool.php");

$defaultimg = "img/gpos_imgdefault.png";

SimpleAutentificacionAutomatica("visual-imagen","$defaultimg");

$cb 	= CleanCB($_GET["cb"]);
$cb_s 	= CleanRealMysql($cb);

$sql = "SELECT IdProducto,Imagen FROM ges_productos WHERE (CodigoBarras = '$cb_s')";

$row = queryrow($sql);


session_write_close();

if($row){
	$Imagen = $row["Imagen"];		
	if(strlen($Imagen)>3){
		header("Location: productos/$Imagen");	
	}else {
		header("Location: $defaultimg");	
	}		
} else {
		header("Location: $defaultimg");		
}




?>
