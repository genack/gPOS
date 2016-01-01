<?php

$CabeceraXUL = '<'.'?xml version="1.0" encoding="UTF-8"?'.'><'.'?xml-stylesheet href="chrome://global/skin/" type="text/css"?'.'>';

if (!function_exists("_")){	
	function _($text){
		return $text;	
	}	
}



if (!isset($PHP_SELF)){
	if(isset($_SERVER["PHP_SELF"])){
		$PHP_SELF = $_SERVER["PHP_SELF"];
	} else 
		$PHP_SELF = $HTTP_SERVER_VARS["PHP_SELF"];
}

$action = $PHP_SELF;

$logMomentos = array();
$momentosIndex = 0;
$ultimoMomento = micro_time();
$sumaTiemposTotal = 0; 
$sqlTimeSuma = 0;


function gas($modo, $texto) {
	
	$modo = strtolower($modo);
	switch($modo){
		case "aviso":
			return "<center><div class='aviso'><div class='warning'><zh3>" . _("Aviso") . "</zh3></div><p>$texto</p></div></center>";
		case "nota":
			return "<center><div class='nota'><div class='warning'><zh3>" . _("Nota") . "</zh3></div><p>$texto</p></div></center>";
		case "titulo":
			return "<h2>$texto</h2>";			
		case "subtitulo":
			return "<h3>$texto</h3>";			
		case "cabecera":
			return "<h1 id='cabecera'>$texto</h1>";			
		default:			
		break;			
	}	
	
	return "<div class='$modo'>$texto</div>";
}

function gCentermenu($menu) {
	return "<div class=centermenu>$menu</div>";
}


function PageStart( $titulo = "gPOS",$cache=false,$fondoblanco=false) {
	global $esPruebas;
	
	//header("Content-languaje: es");
	//header("Content-Type: text/html; charset=UTF-8");

	if (!$cache)
  		$cache = "";
	else {
		$cache= '<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"><META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"><META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">\n<META NAME="GOOGLEBOT" CONTENT="NOARCHIVE">';
		header("Pragma: no-cache");
		header("Cache-control: no-cache");
	}

	if (0) {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	} else {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/DTD/loose.dtd\">\n";	
	}
	
	echo "<html><head><title>". htmlentities($titulo,ENT_QUOTES,'UTF-8') ."</title>";
		
	echo "<link rel='stylesheet' type='text/css' href='css/base.css?v=1'>";		
		
	echo "<link href='css/printcss.css' rel='stylesheet' type='text/css' media='print'>";			
	echo "<link href='modulos/calendario/calendar-blue.css' rel='stylesheet' type='text/css'>";			
		
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><META HTTP-EQUIV='CONTENT-LANGUAGE' CONTENT='es'>$cache";	
	echo "<script language='JavaScript' src='js/cadenas.js.php' type='text/JavaScript'></script>";
	echo "<script language='JavaScript' src='js/basejs.php' type='text/JavaScript'></script>";
		 
	echo "</head>";
	
	$uglybody = "topmargin='0' marginheight='0'";
	
	$fondo = "";
	if ($fondoblanco) {
		$uglybody = "leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'";//REPRUEBAS
		$fondo = "style='background-color: white!important;background-image: none !important;margin: 0px;padding: 0px' ";
	}
	echo "<div id='box-popup' class='box-popup-off'><span class='closepopup' onclick='closepopup()'></span>";	
	echo " <iframe id='windowpopup' class='xframe' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='95%'  onload='if(this.src != \"about:blank\" ) loadFocusPopup()'></iframe> ";
	echo " <iframe id='windowloadcart' class='noxframe' name='windowloadcart' src='about:blank' width='100%' style='border: 0' height='95%' ></iframe> ";
	echo "</div>";
	echo "<body $uglybody $fondo>";
}


function PageEnd($debug=true){
	global $link,$action,$modo,$esPruebas,$sqlTimeSuma,$sumaTiemposTotal,$esVuelcaTiming;
	
	if ($debug and isUsuarioAdministradorWeb()){
		
		
		if(isset($_GET["cargarmodoget"]))
			$add = " (get )modo = '$modo'";
		else
			$add = " (post)modo = '$modo'";
		
		echo "<p>";
		echo gColor("gray",glink($action,$action).  serialize($_GET) . $add);
		
	}	
	
	echo "<p>";
	
	if ($esVuelcaTiming ) {		
		VuelcaMomentos();
		echo "[Suma Tiempos SQL] $sqlTimeSuma<br>";
		echo "<script>timingTerminaGeneracionPagina($sumaTiemposTotal)</script>";					
	}

	$usuario = $_SESSION["NombreUsuario"];
		
	//if ($usuario and $debug)
	//	echo "<div class=piedepagina style='color:gray'>Operador: $usuario</div>";
	
	echo "</body></html>";	
	
	if ($link){
		mysql_close ($link);	
	}
	die();//Termina la ejecucion
} 
 
 
function Separador() {
	echo "<hr width='50%' noshade>";	
}
 
function CenterOpen(){
	//return "<p><a class=tb href='modulos/productos/selmarca.php?modo=creamarca'>". _("Nueva marca") . "</a>";	
	return '<center class="centered">';    
} 

function CenterClose(){
	return "</center>";
}
 
 
function genAutoCerrarVentana() {	
	return "<script>\nwindow.close()\n</script>";	
}
 
 
 



function micro_time() {
   $temp = explode(" ", microtime());
   //return bcadd($temp[0], $temp[1], 6);
   return $temp[0] + $temp[1];
}


function MarcaMomentos($momento){
	global $momentosIndex,$logMomentos,$ultimoMomento,$sumaTiemposTotal;
	$ahora =  micro_time();	
	//$diff = bcsub($ahora, $ultimoMomento, 6);
	$diff = $ahora - $ultimoMomento;
	
	
	$logMomentos[$momentosIndex] = "<tr><td>[$momento]</td><td>$diff</td></tr>";
	$ultimoMomento = $ahora;	
	$momentosIndex ++;
	
	//$sumaTiemposTotal = bcadd($sumaTiemposTotal, $diff,6);
	$sumaTiemposTotal = $sumaTiemposTotal + $diff;
	
}

function VuelcaMomentos(){
	global $logMomentos;
	echo "<table>";
	foreach ($logMomentos as $key=>$value) {
		echo "$value";
	}		
	echo "</table>";
}
 
 
function g($tag="br",$txt ="", $clas="") {
	if($clas!="")
		$clas = " class=\"$clas\" ";
	
	return "<$tag $clas>$txt</$tag>";
}

function glinkMain($to,$txt,$class=false) {
	if ($class){
		$class = " class='$class'";	
	}
	return "<a $class target='main' href=\"". $to. "\"><nobr>$txt</nobr></a>";	
}

function glink($to,$txt) {
	return "<a  href=\"". $to. "\"><nobr>$txt</nobr></a>";	
}

function glinkSolapa($to,$txt) {
	return "<a class=solapa href=\"". $to. "\"><nobr>$txt</nobr></a>";	
}



function gColor($color,$txt,$bold=false){
	if(!$bold)
		return "<font color='$color'>$txt</font>";
	return "<font color='$color'><b>$txt</b></font>";
			
}


function pAction($action) {

	$actionsin = str_replace("?","",$action);
	if ($actionsin!=$action) {
		return $action . gSesion();	
	}
	return $action . gSesion(true);		
}

function gSesion($empieza=false){
	if (!$empieza)
		return "&".session_name()."=".session_id();
	else
		return "?".session_name()."=".session_id();
}

function gModo($modo,$texto=false,$pad=""){
	global $PHP_SELF;	
	if (!$texto)
		$texto = $modo;			
	return glink(pAction($PHP_SELF ."?modo=$modo&" . $pad),$texto); 	
}

function gModoButton($modo,$texto=false,$pad=""){
	global $PHP_SELF;	
	if (!$texto)
		$texto = $modo;			
	return "<span class=tb>" . glink(pAction($PHP_SELF ."?modo=$modo&" . $pad),$texto) . "</span>"; 	
}


function Input($name,$value="",$tipo="text"){
	return "<input type='$tipo' name='$name' value='$value'>";
}


function Enviar($texto){
	return "<input type=submit value='$texto'>";
}

function Hidden($name,$texto){
	return "<input type=hidden id='$name' name='$name' value='$texto'>";
}

//Lee de un post un checkbox
function checkPOST($nombre){
	if (!isset($_POST[$nombre]))
		return 0;
	if ($_POST[$nombre]=="on")
		return 1;
	//TODO: puede colarse problemas aqui?
	error("checkPOST","Unknom: ".$_POST[$nombre]);
	return 0;		
}

function gCheck($val) {
	if ($val)
		return "checked";
	return "";
}
 
function genMakeFonemas($max){
	$vocales = array ("a","e","i","u","a","e","i");
	$todas = array("b","c","d","f","g","h","j","k","m","n","p","q","r","s","t","v","l","r","b","c","d","f");

	$cad = "";
		for ($i=0;$i<$max;$i++)
		{
		$r1 = intval(rand(0,20));
		$r2 = intval(rand(0,4));
		$r3 = intval(rand(0,100));

		$c = $todas[$r1];
		$v = $vocales[$r2];

		if ($c == "q" || $c == "g")
		  $c .= "u";

		if ($c == "x")
		  $c = "s";

		if ($v == "u" && $r3> 10)
		  $v = "a";

			$cad .= $c . $v;
		}
	return $cad;
}

function genMakePass() {
  $r = intval(rand(10,99));
  $myname  = '';
  $myname .= genMakeFonemas(2) . $r ;
  return $myname;
}
 


function isUsuarioAdministradorWeb(){
	global $_SESSION;
	$id = $_SESSION["UsuarioAdministradorWeb"];
	
	if ($id==1 or $id == "1")
		return  true;
	return false;
}

 

function isVerbose(){
	global $modo_verbose;
	if ($modo_verbose)
		return  true;
	return false;
}

 
function q($var,$label="value") {
	
	if (is_array($var))
		$var = str_replace("\n","<br>",var_export($var,true));
	
	return "[ $label='$var' ]";	
} 
 




function GET($valor){
	if (isset($_GET[$valor]))
		return $_GET[$valor];
	
	if(isset($_POST[$valor])) 
		return $_POST[$valor];
	
	return false;	
}


function gAccion($modo,$label,$id=false){	
	global $action;		
	return 	"<input class='btn' type='button' onclick='window.location.href=\"$action?modo=$modo&id=$id\";' value='" . $label . "' />";	  	
}

function gAccionConfirmada($modo,$label,$id=false,$aviso=""){
	global $action;	
	return 	"<input class='btn' type='button' onclick='ifConfirmGo(\"".$aviso .
			"\",\"$action?modo=$modo&id=$id\")' value='" . $label . "' />";	  	
}
 
  
  
function adderror($f=false,$f2=false){
	
}
 

?>
