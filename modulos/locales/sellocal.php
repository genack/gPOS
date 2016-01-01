<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elige local"));

switch($modo){
  /*
	case "localhab":
			
		echo "<groupbox flex='1'><caption label='" . _("Local") . "'/>";		
		echo "<textbox id='buscalocal'  onkeyup='BuscaLocal(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayLocales();		
		echo "<script>\n";
		echo " provhab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "provhab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Local' rows='5' onclick='parent.changeProvHab(this,provhab[this.value]);parent.closepopup();return true;'>";
		echo  genXulComboLocales();				
		echo "</listbox>";
		echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
		echo "</groupbox>";
		
		break;				
  */
	case "localpost":
			
		echo "<groupbox flex='1'><caption label='" . _("Local") . "'/>";		
		echo "<textbox id='buscalocal'  onkeyup='BuscaLocal(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayLocales();		
		echo "<script>\n";
		echo " loclhab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "loclhab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Local' rows='5' onclick='parent.setLocalPost(this,loclhab[this.value]);parent.closepopup();return true;'>";
		echo  genXulComboLocales();				
		echo "</listbox>";
		//echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
		echo "</groupbox>";
		
		break;				
	default:
		break;	
}
?>

<script>//<![CDATA[

function BuscaLocal(){
    var elemento = document.getElementById("buscalocal");
    var ns = new String(elemento.value);
    ns = ns.toUpperCase();
    var lista = document.getElementById("Local");
    var texto2  = document.getElementsByTagName('listitem');
    if(ns.length >0){
        for (var i=0;i<lista.itemCount;i++){
            var cadena = new String(texto2[i].attributes.getNamedItem('label').nodeValue);
            cadena = cadena.toUpperCase();
            if(cadena == ns){
                lista.ensureIndexIsVisible(i);
                lista.selectedIndex=i;
                lista.onclick();
                return;
            }
            if(cadena.indexOf(ns) != -1){
                lista.ensureIndexIsVisible(i);
                lista.selectedIndex=i;
            }
        }
    }
}

function agnadirDirecto(){
    var theList=document.getElementById('Local');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}
function loadfocus(){
    document.getElementById('buscalocal').focus();
}

//]]></script>
<?php
EndXul();


?>

