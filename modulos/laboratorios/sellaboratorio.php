<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elige laboratorio"));

switch($modo){
	case "laboratoriohab":
			
		echo "<vbox class='box' flex='1'><groupbox flex='1'><caption label='" . _("Laboratorio") . "'/>";		
        echo "<textbox id='buscalaboratorio'  onkeyup='BuscaLaboratorio(); if (event.which == 13) agnadirDirecto();' />";
		$familias = genArrayLaboratorios();		
		echo "<script>\n";
		echo " labhab =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "labhab[$key] = '$value';\n";			
		}
		echo "\n</script>";						
				
		echo "<listbox flex='1' id='Laboratorio' rows='5' ondblclick='parent.changeLabHab(this,labhab[this.value]);parent.closepopup();return true;'  onkeypress='if (event.which == 13) { parent.changeLabHab(this,labhab[this.value]);parent.closepopup();return true; }'>";
		echo  genXulComboLaboratorios();				
		echo "</listbox>";
		//echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
		echo "</groupbox></vbox>";
		
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
  filas = lista.itemCount;
  
  for(var i=0;i<filas;i++){
    lista.removeItemAt(0);
  }
  
  //var texto2  = document.getElementsByTagName('listitem');
  if(ns.length == ""){
    for(var i=0;i<filas;i++){
      lista.removeItemAt(0);
    }
    
    for(var i in labhab){
      var row = document.createElement('listitem');
      row.setAttribute('label',labhab[i]);
      row.setAttribute('value',i);
      lista.appendChild(row);	
    }
  }else{
    for(var i=0;i<filas;i++){
      lista.removeItemAt(0);
    }
    for(var i in labhab){
      var cadena = new String(labhab[i]);
      cadena = cadena.toUpperCase();
      if(cadena.indexOf(ns) != -1){
	var row = document.createElement('listitem');
	row.setAttribute('label',labhab[i]);
	row.setAttribute('value',i);
	lista.appendChild(row);	
      }
      var elemento = lista.getItemAtIndex(0);
      lista.selectItem(elemento);
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

function loadfocus(){
    document.getElementById('buscalaboratorio').focus();
}

//]]></script>
<?php



EndXul();


?>
