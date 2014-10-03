<?php

/* Inicializaciones que necesita el instalador */

$modo = ( isset($_REQUEST["modo"]) )?$_REQUEST["modo"]:'';

//Flag: estamos haciendo una instalacion
// evita que salte a autologin.php
$enProcesoDeInstalacion = true;

//Se usara en varios sitios.
//define (h2,"H2");

//Texto descriptivo de la tarea en ejecución
$UltimaTarea = "";


$cr = "<br>";
$nombreBase = "gpos_test";


/*  El instalador detectara y avisara de cualquier error */

function AddErrorHandler($errno,$errstr){		
		if ($errno!=8) {
			ErrorMensaje("$errno: $errstr",true);	
		} else {
			ErrorMensaje("$errno: $errstr");
		}
}

error_reporting  (E_ALL);
$old_errorlog = set_error_handler("AddErrorHandler");

include("install.inc.php");

/* Comienza el output de esta pagina */

header('Content-Type: text/html; charset=UTF-8');


?>
<html>
<head>
<BASE>
<title></title>
<style type="text/css" media="screen">@import url(css.css);</style>
</head>
<body>
<div id="container">
<h1>gPOS</h1>

<?php
switch($modo){
   case "EntradaDatosDB":
      $numErrores = 0;
      $numFatal = 0;
   
      $host = $_POST["hostname"];
      $nombreNegocio = $_POST["nombreNegocio"];
      $usuario = $_POST["usuario"];
      $password = $_POST["password"];
      $nombreBase = $_POST["database"];
      $baseurlDato = $_POST["baseurl"];
      $giroNegocioDato = "PINF";
      $adminEmailDato = $_POST["adminemail"];
      $passModulos = $_POST["passmodulos"];

      webAssert($nombreBase,"","No se proporciono nombre de base de datos",true);
      webAssert($usuario,"","No se proporciono usuario de base de datos",true);
      webAssert($usuario,"","No se proporciono host de base de datos",true);

      IniciaTarea("Cargando datos de disco..");
						
      webAssert(function_exists("dirname"),"","Su version de PHP no reune las caractersticias minimas",true);
      webAssert(function_exists("file_get_contents"),"","Su version de PHP no reune las caractersticias minimas",true);
      webAssert(function_exists("mysql_connect"),"","Su version de PHP no reune las caractersticias minimas: Soporte MySQL",true);
      //webAssert(function_exists("mysql_create_db"),"","Su version de PHP no reune las caractersticias minimas: Soporte MySQL",true);
      webAssert(function_exists("mysql_select_db"),"","Su version de PHP no reune las caractersticias minimas: Soporte MySQL",true);
      webAssert(function_exists("mysql_query"),"","Su version de PHP no reune las caractersticias minimas: Soporte MySQL",true);
      webAssert(function_exists("mysql_error"),"","Su version de PHP no reune las caractersticias minimas: Soporte MySQL",true);
      webAssert(function_exists("fwrite"),"","Su version de PHP no reune las caractersticias minimas",true);
      webAssert(function_exists("htmlentities"),"","Su version de PHP no reune las caractersticias minimas",true);				
						
      $pathTablas   = dirname(__FILE__) . "/../esquema/tablas.sql";
      $pathInserts  = dirname(__FILE__) . "/../esquema/datos.sql";
      $pathFunction = dirname(__FILE__) . "/../esquema/funciones.sql";

      $bigSQL       = file_get_contents($pathTablas);
      $bigINSERTS   = file_get_contents($pathInserts);
      $bigFunciones = file_get_contents($pathFunction);
	        
      webAssert($bigSQL,"","No se encuentra $pathTablas",true);
      webAssert($bigINSERTS,"","No se encuentra $pathInserts",true);
      webAssert($bigFunciones,"","No se encuentra $pathFunction",true);
										
      IniciaTarea("Conectando");
	
      $link = mysql_connect($host,$usuario,$password);

      webAssert($link,"Conexion ...realizada" ,'E: no puede conectar: ' . mysql_error(),true);

      //TEST: on test, we destroy the db
      //mysql_query("DROP DATABASE $nombreBase");
						
      $nombreBase_s = mysql_real_escape_string($nombreBase);
   
      $create = mysql_query("CREATE DATABASE $nombreBase_s charset=utf8");
			
      webAssert($create,"Database creada","E: no se pudo crear: ".mysql_error($link));
		
      $selectdb = mysql_select_db( $nombreBase ); 	
									
      webAssert($selectdb,"Database abierta","E: no se pudo abrir: ".mysql_error($link));
										
      IniciaTarea("Creando tablas");
      mysql_query("SET NAMES 'utf8'");			
      $querys = split_queris($bigSQL);

      foreach($querys as $query){
	if ($query and $query != "\n"){
	  $result = mysql_query($query);
	  webAssert($result,".","E: problema al iniciar tablas: ".mysql_error($link));
	}
      }	
   
      IniciaTarea("Cargando datos");
      $querys = split_queris($bigINSERTS);

      foreach($querys as $query){
	if ($query and $query != "\n"){
	  $result = mysql_query($query);
	  $query_s = htmlentities($query);
	  webAssert($result,".","E: problema al iniciar datos: $cr <font color=red>$query_s</font>  ".mysql_error($link));
	}
      }

      IniciaTarea("Creando funciones");
      $querys = split_queris($bigFunciones);

      foreach($querys as $query){
	if ($query and $query != "\n"){
	  $result  = mysql_query($query);
	  webAssert($result,".","E: Problema al iniciar funciones".mysql_error($link));
	}
      }

      IniciaTarea("Creando xulremoto");
   
        $xdomain    = bootstrapDomain($baseurlDato);
        $xjsOut     = bootStrapJS($xdomain);
        $path       = 'bootstrap.js';
        $filehandle = fopen($path, "w");	

        webAssert($filehandle,"Se abre boot strap ","E: no se puede abrir para escritura: $path");

        if ($filehandle){

	  fwrite($filehandle, $xjsOut. PHP_EOL);
	  fclose($filehandle);
	  
	  $xzip       = new ZipArchive();
	  $path       = dirname(__FILE__) . '/../config/gpos-installer.xpi';
	  $filehandle = fopen($path, "w");	

	  webAssert($filehandle,"Se crea gpos-installer.xpi","E: no se puede abrir para escritura: $path");
	  if($filehandle){
	    $xzip->open($path,ZIPARCHIVE::CREATE);
	    $xzip->addFile('install.rdf');
	    $xzip->addFile('bootstrap.js');
	    $xzip->close();
	    unlink('bootstrap.js');
	  }
	}

      IniciaTarea("Guardando configuración");
		
      $path = dirname(__FILE__) . "/configuration.template";		
      $phpConfiguration = file_get_contents($path);	
   
      webAssert($phpConfiguration,"Cargada template config","E: no abre template config");
				
      $phpConfiguration = str_replace("%HOST%",$host,$phpConfiguration);
      $phpConfiguration = str_replace("%NOMBRENEGOCIO%",$nombreNegocio,$phpConfiguration);
      $phpConfiguration = str_replace("%DATABASE%",$nombreBase,$phpConfiguration);
      $phpConfiguration = str_replace("%USER%",$usuario,$phpConfiguration);
      $phpConfiguration = str_replace("%PASS%",$password,$phpConfiguration);
      $phpConfiguration = str_replace("%BASEURL%",$baseurlDato,$phpConfiguration);
      $phpConfiguration = str_replace("%GIRONEGOCIO%",$giroNegocioDato,$phpConfiguration);
      $phpConfiguration = str_replace("%ADMINEMAIL%",$adminEmailDato,$phpConfiguration);
      $phpConfiguration = str_replace("%PASSMODULOS%",$passModulos,$phpConfiguration);
      $phpConfiguration = "<?php \n". $phpConfiguration . "\n?>";
				
      $path = dirname(__FILE__) . '/../config/configuration.php'; 		
   
      $filehandle = fopen($path, "w");	
 		
      webAssert($filehandle,"Se abre config","E: no se puede abrir para escritura: $path");
 		
      $result = false;
   
      if ($filehandle) {  			
	$result = fwrite($filehandle,$phpConfiguration);
	$numFatal += ($result?0:1);
	fclose($filehandle); 			 			
      }				

      webAssert($result,"Guardados los datos de configuración","E: no se puede guardar la configuración");				
						
      if ($numFatal<1){							
	if ($numErrores>0){
	  echo "<blockquote style='color:red;font-size:110%'>";			
	  echo "<b>Se han producido $numErrores. Es posible que la aplicación no funcione correctamente</b>";
	  echo "</blockquote>";				
	}
	
	$urlServicio = $baseurlDato . "xulentrar.php";			
	PresentarInterface("instalacionexito.dialog.php",array("urlServicio"=>$urlServicio) );
      } else {
	PresentarInterface("errorfatal.dialog.php");
      }								
      break;
			
   case "CargarDatos":
      PresentarInterface("datosdb.dialog.php");
      break;
			
   default:
      PresentarInterface("datosdb.dialog.php");
      break;	
}

echo "</div></body></html>"; 

?>
