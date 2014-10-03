<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elige cliente"));

switch($modo){
  /*
	case "clientehab":
			
		echo "<groupbox flex='1'><caption label='" . _("Cliente") . "'/>";		
		echo "<textbox id='buscacliente'  onkeyup='BuscaCliente(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayClientes();		
		echo "<script>\n";
		echo " clienthab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "clienthab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Cliente' rows='5' onclick='opener.changeClientHab(this,clienthab[this.value]);window.close();return true;'>";
		echo  genXulComboClientes();				
		echo "</listbox>";
		echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
		echo "</groupbox>";
		
		break;				
  */
	case "clientepost":
			
		echo "<groupbox flex='1'><caption label='" . _("Cliente") . "'/>";		
		echo "<textbox id='buscacliente'  onkeyup='BuscaCliente(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayClientes();		
		echo "<script>\n";
		echo " clienthab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "clienthab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Cliente' rows='5' onclick='opener.setClientPost(this,clienthab[this.value]);window.close();return true;'>";
		echo  genXulComboClientes();				
		echo "</listbox>";
		echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
		echo "</groupbox>";
		
		break;				
	default:
		break;	
}
?>

<script>//<![CDATA[

function BuscaCliente(){
    var elemento = document.getElementById("buscacliente");
    var ns = new String(elemento.value);
    ns = ns.toUpperCase();
    var lista = document.getElementById("Cliente");
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
    var theList=document.getElementById('Cliente');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}
//]]></script>
<?php
EndXul();


?>

