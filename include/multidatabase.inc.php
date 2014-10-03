<?php


die("");

//NOTA: da bastantes problemas y de momento no se usara.

//ABOUT:
//Intenta detectar la configuracion de la base de datos del entorno.
// ,que podria venir desde un fichero de configuracion o de una sesion abierta
// procedente de gestor de multinegocios.


if (!$sesion_ges_database ){	
	$sesion_ges_database 	= $_SESSION["GlobalGesDatabase"];
	$sesion_global_host_db 	= $_SESSION["GlobalHostDatabase"];
	$sesion_global_user_db 	= $_SESSION["GlobalUserDatabase"];
	$sesion_global_pass_db 	= $_SESSION["GlobalPassDatabase"];
}

if ($sesion_ges_database){
	//Tenemos datos en sesion
	$ges_database   = $sesion_ges_database;
	$global_host_db = $sesion_global_host_db;
	$global_user_db = $sesion_global_user_db;
	$global_pass_db = $sesion_global_pass_db;		
} else {
	
	if(!isset($enProcesoDeInstalacion)){	
		session_write_close();
	//	header("Location: autologin.php");
	}
}





?>