<?php

include("../../tool.php");


$sat = false;

    switch($modo){
    case "salvamarcasat":
      $sat = true;
    case "salvamarca":

      $marca = CleanText($_GET["marca"]);

      if (!$marca or $marca == "") break;
      
      $sql = "SELECT IdMarca FROM ges_marcas WHERE Marca='$marca'";
      $row = queryrow($sql);
      
      if ($row and $row["IdMarca"]) 
	{
	  $idold = $row["IdMarca"];
	  $sql = "UPDATE ges_marcas SET Eliminado=0 WHERE IdMarca='$idold'";
	  query($sql);// devolvemos a la vida una marca existente
	  if($sat) {
	    echo '~'.$idold;
	    return;
	  }
	  break;		
	}
      
      global $UltimaInsercion;
      query("INSERT INTO ges_marcas (Marca) VALUES ('$marca')");
      if($sat) {
	echo '~'.$UltimaInsercion;
	return;
      }
      break;
      
    case "eliminamarca":
      $marca = CleanText($_GET["marca"]);
      $sql = "UPDATE ges_marcas SET Eliminado=1 WHERE Marca='$marca'";
      query($sql);	
      break;
    default:
      break;	
    }

    SimpleAutentificacionAutomatica("visual-xulframe");
    StartXul(_("Elije Marca")); 

//SE EJECUTA SIEMPRE

    echo "<groupbox> <caption label='Buscar Marca'/>";
    echo "<hbox>";
    echo "<textbox  flex='1'   id='buscamarca' onkeyup='BuscarMarca();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' />";
    echo "</hbox>";
    echo "<hbox flex='1'>";
    echo "<button flex='1' label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
    echo "</hbox>";


    echo "</groupbox>";
    echo "<groupbox><caption label='" . _("Marcas") . "'/>";

    $familias = genArrayMarcas();
    $combo = "";
		echo "<script>\n";
		echo " fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
		}
		
		echo "
		function UsarNuevo() {
			var talla, url;
			var nuevocolor = document.getElementById('buscamarca');			

			if (nuevocolor)
                             talla = nuevocolor.value;

                        if (!talla || talla == '')
                             return;
			url = 'selmarca.php';
			url = url +'?';
                        url = url + 'modo';
                        url = url + '=salvamarca';
                        url = url + '&amp;'+'marca=' + talla;
			document.location.href = url;			
		}
		
		function Eliminar() {
			var marcaname, url;
			var lamarca = document.getElementById('buscamarca');	
			if (lamarca) 
				marcaname = lamarca.value;
			if (!marcaname || marcaname== '') return;				
			url = 'selmarca.php';
			url = url +'?';
                        url = url + 'modo';
                        url = url + '=eliminamarca';
                        url = url + '&amp;'+'marca=' + marcaname;
			document.location.href = url;				  			
		}

                function soloAlfaNumerico(e){ 
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = ' abcdefghijklmnñopqrstuvwxyz0123456789-.';
                        especiales = [8, 13, 9, 35, 36, 37, 39];
                        tecla_especial = false
                        for(var i in especiales){
                           if(key == especiales[i]){
                              tecla_especial = true;
                              break;
                           }
                        }
    
                        if(letras.indexOf(tecla)==-1) { 
                           if(!tecla_especial){
                              return false;
                           }
                        }
                }
 		";
		
echo "\n</script>";						
echo "<script  type='application/x-javascript' src='marca.js' />";
echo "<listbox id='Marca' rows='5' onclick='opener.changeMarca(this,fam[this.value]);window.close();return true;'>\n";
echo  genXulComboMarcas();				
echo "</listbox>";
echo "<button flex='1' label='"._("Eliminar")."' onkeypress='if (event.which == 13) Eliminar()' oncommand='Eliminar()'/>";
echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
echo "</groupbox>";


EndXul();






?>
