<?php

function CleanParaWeb($valor){
	return htmlentities($valor,ENT_QUOTES,'UTF-8');
}

function CleanXSS($valor){
	return strip_tags($valor);
}


function CleanFechaFromDB($fecha){
	if ($fecha == "0000-00-00")
		return "";

	list($agno,$mes,$dia) = explode("-",$fecha);
	return($dia . "-" . $mes . "-" . $agno);
}

function CleanFechaES($fecha){
	if (!$fecha)
		return "";

	if ($fecha == "DD/MM/AAAA")
		return "";
	if ($fecha == "DD-MM-AAAA")
		return "";
	list($dia,$mes,$agno) = explode("-",$fecha);
	return($agno . "-".$mes."-".$dia);
}

function CleanCP($local){
	$local = CleanTo($local);
	$local = str_replace('"',"",$local);
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}



function CleanInt($int){
	return intval($int,10);	
}

function CleanPass($pass){
	return CleanText($pass);	
}

function CleanLogin($login){
	return CleanText($login);	
}

function CleanXulLabel($label){
	return $label;	
}

function CleanCB($cb){
	$ref = str_replace("\t","",$cb);
	$ref = trim($cb);
	$ref = str_replace(" ","",$ref);
	return $ref;	
}

function CleanRef($ref){
	return CleanReferencia($ref);
}

function CleanReferencia($ref){
	$ref = trim($ref);
	$ref = str_replace(" ","",$ref);	
	$ref = strtoupper($ref);
	
	return $ref;	
}

function CleanDinero($val){
	return CleanFloat($val);	
}

//Heavy, quita metacaracteres y espacios. Util para palabras
function CleanTo($text,$to="")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("@",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);
	
	return $text;	
}
function CleanToTel($text,$to="")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("@",$to,$text);
	//$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);
	
	return $text;	
}


function CleanText($text){
	return CleanTo($text," ");	
}

function CleanTextTel($text){
	return CleanToTel($text," ");	
}

function Clean($text){
	return CleanTo($text," ");
}



//Para limpiar nombres
function CleanPersonales($text,$to=" ")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);	
	return $text;	
}


//Para identificadores 
function CleanID($IdentificadorNumerico) {
	return 	intval($IdentificadorNumerico);
}

//Para numeros positivos
function CleanIndexPositivo($num){
	$num = intval($num);
	if ($num<0)
		return - $num;
	return $num;	
}

//Convierte texto en html
function CleanToHtml($str) {	
	$str = htmlentities($str,ENT_QUOTES,'UTF-8'); 
	return str_replace("\n","<br>",$str);	
	//return nl2br($str);
}

function entichar($chr){
	return "&#" . ord($chr) . ";";
}


function CleanHTMLtoBD($text) {
	$text = str_replace("#",entichar("#"),$text);
	$text = str_replace("'",entichar("'"),$text);	
	$text = str_replace("\\",entichar("\\"),$text);	
	$text = strip_tags($text,"<br>");
	//$text = str_replace("<br>","\n",$text);	
	return $text;		
}
function CleanCadena($text) {
    $text = trim($text);
	$text = str_replace('"'," ",$text);
	$text = str_replace("'"," ",$text);	
	$text = str_replace("\n"," ",$text);	
	$text = str_replace("$"," ",$text);	
    return (string)str_replace(array("\r", "\r\n", "\n"), '', $text);    
}
function CleanBDtoTexto($text) {
	$text = str_replace("\\'","'",$text);
	return $text;	
}

function CleanDoMagicQuotes($text) {
	if( get_magic_quotes_gpc())
		return $text;
	return addslashes($text);
}

function CleanNL2BR($text){
	return nl2br($text);	
}

function CodificaScript($script) {
	return str_replace("'","@",$script);	
}

function DescodificaScript($script,$IdAlojamiento) {
	$IdAlojamiento = CleanID($IdAlojamiento);
	$sql = "SELECT DISTINCT URLFotoVirtual FROM dat_fotosvisitavirtual WHERE IdAlojamiento=$IdAlojamiento";
	
		//AddError(__FILE__ . __LINE__ , "Info: $sql");	
		
	$cambiados = array();
		
	$res = query($sql);
	
	if ($res) {
		while($row = Row($res)) {
			$img = $row["URLFotoVirtual"];
			
			if (!$cambiados[$img])
				$script = eregi_replace($img,"fullres/" . $img ,$script);
			$cambiados[$img] = 1;
		//	AddError(0,"Info: reemplazando $img");		
		}	
	} else {
		//AddError(__FILE__ . __LINE__ , "W: no le gusto $sql");	
	}
	
	//die("caput!");
	
	// Full Texts   	  IdFotoVirtual   	  IdAlojamiento   	  URLFotoVirtual
	 
	return str_replace("@","'",$script);
}

function Convertir2Textoplano($html) {
	$out = str_replace("<br>","\n",$html);
	$out = strip_tags($out);		
	return $out;
}

//Elimina los atributos del html
function SimplificaHTML($html){
	$out = "";
	//Interprete de HTML
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)	{
		if($i%2==0)		{
			//Text
			$out .= $e;			
		}	else		{
			//Etiqueta			
			//$out .= "<$e>";
			
			//Extraer atributos			
			$tag=array_shift(explode(' ',$e));
			$out .= "<$tag>";
		}
	}
	
	return $out;
}

function CleanFloat($val) {	
	$val = str_replace(",", ".", $val );
	return (float)$val;	
}

function Convertir2WebCSS($html,$titulo=""){

$begin =<<<HEREDOC
<html><head>
<style type="text/css">
<!--
%CUERPOCSS%
//-->
</style>
<title>$titulo</title>
</head>
<body lang='es'>
HEREDOC;
	$css = CargarContenidoFichero("trv.css");
	
	$begin = str_replace("%CUERPOCSS%",$css,$begin);	
	
	$end = "</body></html>";
	return $begin . $html . $end;
}

//Para localizadores 
function CleanLocalizador($local) {
	return trim($local); 	
}

//Para DNI
function CleanDNI($local) {
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}


function CleanUrl($url){
	$url = str_replace("'","",$url);
	$url = trim($url);
	return $url;	
}

/*
function CleanCP($cp){
	$cp = trim($cp);
	return $cp;
}*/

function CleanRealMysql($dato,$quitacomilla=true){
	global  $link;
	
	if (!$link){
		//NOTA:
		//  mysql real escape necesita exista una conexion,
		// ..por eso si no hay ninguna establecida, la abrimos. 
		forceconnect();
	}
		
	if ($quitacomilla)
		$dato = str_replace("'"," ",$dato);
	$dato_s = mysql_real_escape_string($dato);
	return $dato_s;
}

function CleanNif($nif){
	return CleanDNI($nif);	
}

function CleanEmail($correo){
	return CleanCorreo($correo);	
}

function CleanCC($cc){
	$cc = trim($cc);
	$cc = str_replace(" ","",$cc);
	return $cc;	
}


function CleanCorreo($correo){
	$correo = trim($correo);
	$correo = str_replace(" ","",$correo);
	return $correo;
}

function esCorreoValido($correo){
	$correo = CleanCorreo($correo);
	list($usuario,$host) = explode("\@",$correo);

	$len = strlen($usuario);
	if ($len<1)	return false;
	$len = strlen($host);
	if ($len<1)	return false;
	return true;
}

function CleanTelefono($tel){
	$tel = trim($tel);
	$tel = str_replace(" ","",$tel);
	return $tel;	
}


function esTelefonoValido($tel){

	if (!$tel or $tel=="")
		return false;		
			
	$len = strlen($tel);
	if ($len<6)	return false;
	
	return true;
}


function FormatMoney($val) {
	$val = CleanDinero($val);
	$Moneda = getSesionDato("Moneda");
	//return htmlentities(money_format('%.2n Soles', $val),ENT_QUOTES,'ISO-8859-15');
	return money_format($Moneda[1]['S'].' %.2n ', $val);
	//return number_format($val, 2, ',', ""). " Soles";
}

function FormatUnits($val) {
	return $val . " u.";	
}


if(function_exists("iconv")) {
	function iso2utf($text) {	
		return iconv("ISO-8859-1","UTF8",$text);
	}
	function utf8iso($text){
		return iconv("UTF8","ISO-8859-1//TRANSLIT",$text);		
	}	
	
} else {
	//TODO: buscar alternativa que no sea lenta
	function iso2utf($text) {	
		return $text;
	}
	function utf8iso($text){
		return $text;		
	}			
}

function CleanTextExt($text,$to=" ")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace(";",$to,$text);
	$text = str_replace("\t",$to,$text);
	
	return $text;	
}


?>
