<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elige laboratorio"));

switch($modo){
	case "laboratoriohab":
			
		echo "<groupbox flex='1'><caption label='" . _("Laboratorio") . "'/>";		
        echo "<textbox id='buscalaboratorio'  onkeyup='BuscaLaboratorio(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayLaboratorios();		
		echo "<script>\n";
		echo " labhab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "labhab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Laboratorio' rows='5' onclick='opener.changeLabHab(this,labhab[this.value]);window.close();return true;'>";
		echo  genXulComboLaboratorios();				
		echo "</listbox>";
		echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
		echo "</groupbox>";
		
		break;				
	default:
		break;	
}

?>

<script>//<![CDATA[

function BuscaLaboratorio(){
    var elemento = document.getElementById("buscalaboratorio");
    var ns = new String(elemento.value);
    ns = ns.toUpperCase();
    var lista = document.getElementById("Laboratorio");
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
    var theList=document.getElementById('Laboratorio');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}

//]]></script>
<?php



EndXul();


?>
