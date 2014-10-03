<?php

require("../../tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

function esNumero($cadena) {
	if ($cadena == "0")
		return true;

	return (( $cadena * 1 ) >0);
}

$IdListado = CleanID($_GET["id"]);

$sql = "SELECT IdListado, NombrePantalla FROM ges_listados WHERE Eliminado=0";

	
$res = query( $sql  );



$datos = "<ul>\n";	
		
if ($res){
	while( $row = Row($res) ){			
		$datos .= "<li><a href='listado.php?id=".$row['IdListado']."'>".$row["NombrePantalla"]."</a></li>\n";					
	}
}

$datos .= "</ul>";

		
PageStart();

	echo "<div class='forma' style='margin-left: 32px;margin-right: 32px;'>";
	echo "<div class='lh'>" ._("Listados") . "</div>";
	echo $datos;
	echo "</div>";
	
PageEnd();

?>