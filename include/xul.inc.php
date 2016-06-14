<?php


function StartXul($titulo,$predata="",$css=false){
	global $esPruebas;

	header("Content-type: application/vnd.mozilla.xul+xml");

	//	header("Pragma: no-cache");
	//	header("Cache-control: no-cache");
	header("Content-languaje: es");
	
	$cr = "<?";
	$crf = "?>";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	$_BasePath   = $_SESSION['BasePath'];
	echo $predata;	
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';	
	echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css?v=3.2" type="text/css"?>';
        //if($css) echo $cr . "xml-stylesheet href='data:text/css,$css'" . $crf; 
        if($css) echo $cr . "xml-stylesheet href='".$_BasePath.$css."' type='text/css'" . $crf; 
?>

<window id="<?php echo $titulobreve ?>" title="<?php echo $titulo ?>"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">        
	<?php

}

function StartJs($js=false){
  $_BasePath   = $_SESSION['BasePath'];
  ?>
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js?v=2.4" /> 
  <?php
  if($js){
    ?>
    <script type="application/x-javascript" src="<?php echo $_BasePath.$js; ?>" />        
   <?php
  }
}

function StartXulOverlay($titulo,$predata=""){

	header("Content-type: application/vnd.mozilla.xul+xml");

	$cr = "<?";
	$crf = "?>";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	 echo $predata;
	 
	 echo '<?xml version="1.0" encoding="UTF-8"?>';
	 echo '<overlay
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">';          	
}

function declareOverlay($url) {
	return "<" ."?xml-overlay href='$url'?"."/>";
}

function EndXul() {
	echo "</window>";	
}

function EndXulOverlay() {
	echo "</overlay>";	
}
  
 	
	
function xulMakeMenuOptions( $elementos ) {
	
	$out = "";
	foreach ($elementos as $key=>$value) {
			$out .= "<menuitem label='$key' oncommand=\"$value\"/>";
	}
	return $out;
}

function xulMakeMenuOptionsCommands( $elementos ) {

	$out = "";
	foreach ($elementos as $key=>$value) {
			$out .= "<menuitem command=\"$value\"/>";
	}
	return $out;
}

function xulMakePopup($nombre,$cuerpo){
	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	
	$out = "<menupopup id='".$nombreBreve."-popup'>\n";
	return $out . $cuerpo . "</menupopup>\n";
}
	
function xulMakeMenu($nombre,$elementos){
	
	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	$out = "<menu id='menu-".$nombreBreve."' label='$nombre'>";
	
	
	$cuerpo = xulMakePopup($nombre,xulMakeMenuOptions($elementos));

	return $out . $cuerpo . "</menu>\n";	
}	
	
function xulMakeMenuCommands($nombre,$elementos){

	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	$out = "<menu id='menu-".$nombreBreve."' label='$nombre'>";
	
	$cuerpo = xulMakePopup($nombre,xulMakeMenuOptionsCommands($elementos));
	return $out . $cuerpo . "</menu>\n";	
}	
		
	


?>