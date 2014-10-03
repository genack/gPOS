<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$idprod = CleanID($_GET["IdProducto"]);

if (!$idprod) {
	$cod = CleanCB($_GET["CodigoBarras"]);
	$idprod = getIdFromCodigoBarras($cod);
}

switch($modo){
	case "soloficha":
		if ($idprod) {
			echo "<center class='forma' style='zwidth: 600px;overflow:auto;border: 1px solid #ECE8DE'>";
			echo genListadoCruzado( $idprod, false, false );
			echo "</center>";
		}
		exit();
		break;		
	default:		
		PageStart();		
		if ($idprod) {
			echo "<center>";
			echo genListadoCruzado( $idprod, false, false );
			echo "</center>";
		}
		PageEnd();
}

?>
