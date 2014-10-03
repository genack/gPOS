<?php

$erroresPagina = array();

function AddBugToSession($bug){
	$oldError = $_SESSION["Errores_Session"];
	
	if (!$oldError)
		$oldError = array();
	$oldError[] = $bug;
	$_SESSION["Errores_Session"] = $oldError; 	
}



function error($donde,$texto=false){
	global $erroresPagina,$debug_mode;
	
	$donde = str_replace("\n"," ",$donde);
	$texto = str_replace("\n"," ",$texto);
	
	$strbug = $donde . ": ". $texto;
		
	if($debug_mode)	
		AddBugToSession($strbug);
	
	error_log( $strbug, 0); 
}



if (0){
			
	function AddErrorHandler($errno,$errstr){
		global $debug_mode;
		
		$time = date("H:i·s");
		
		$strbug =  "($time)EHanler $errno: $errstr";
		if ( $debug_mode) {						
			//INFO: para debugear mostrando en pantalla errores y warnings
			//if ($errno!=8) echo $strbug. "<br>";
				
		}		
		 
		error($errno,$errstr);
	}

	error_reporting  (E_ALL & ~E_NOTICE);
	set_error_handler("AddErrorHandler");
}



?>