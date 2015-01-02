<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije Empaque")); 


switch($modo){
	case "salvacontenedor":
		$contenedor = CleanText($_GET["contenedor"]);
		if (!$contenedor or $contenedor == "")
			break;
		
		$sql = "SELECT IdContenedor FROM ges_contenedores WHERE Contenedor='$contenedor'";
		$row = queryrow($sql);
		
		if ($row and $row["IdContenedor"]) {
			$idold = $row["IdContenedor"];
			$sql = "UPDATE ges_contenedores SET Eliminado=0 WHERE IdContenedor='$idold'";					
			query($sql);// devolvemos a la vida una contenedor existente
			break;		
		}
		

		query("INSERT INTO ges_contenedores (Contenedor) VALUES ('$contenedor')");
		break;
		
	case "eliminacontenedor":
		$contenedor = CleanText($_GET["contenedor"]);
		$sql = "UPDATE ges_contenedores SET Eliminado=1 WHERE Contenedor='$contenedor'";
		query($sql);	
		break;
	default:
		break;	
}


//SE EJECUTA SIEMPRE

    echo "<groupbox> <caption label='Buscar Empaque:'/>";
     echo "<hbox>";
     echo "<textbox  flex='1'   id='buscacontenedor' onkeyup='BuscarContenedor();   if (event.which == 13) agnadirDirecto();' onkeypress='javascript:this.value=this.value.toUpperCase(); return soloAlfaNumerico(event)' /> ";
    echo "</hbox>";
    echo "<hbox flex='1'>";
    echo "<button flex='1' label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
    echo "</hbox>";
    echo "</groupbox>";



		echo "<groupbox><caption label='Empaque:'/>";
		
		$familias = genArrayContenedores();
		$combo = "";
		echo "<script>\n";
		echo " var fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
		}
		
		echo "
		function UsarNuevo() {
              
			var talla, url;
			var nuevocolor = document.getElementById('buscacontenedor');			
			if (nuevocolor)
                 talla = nuevocolor.value;
            if (!talla || talla == '')
                 return;
            
			url = 'selcontenedor.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=salvacontenedor';
            url = url + '&amp;'+'contenedor=' + talla;
			document.location.href = url			
		}
		
		function Eliminar() {
			var contenedorname, url;
			var lacontenedor = document.getElementById('buscacontenedor');	
			if (lacontenedor) 
				contenedorname = lacontenedor.value;
			if (!contenedorname || contenedorname== '')
				return;
				
			url = 'selcontenedor.php';
			url = url +'?';
            url = url + 'modo';
            url = url + '=eliminacontenedor';
            url = url + '&amp;'+'contenedor=' + contenedorname;
			document.location.href = url						  			
		}

                function soloAlfaNumerico(e){ 
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = 'abcdefghijklmnopqrstuvwxyz-';
                        especiales = [8, 13];
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

        echo "<script  type='application/x-javascript' src='contenedor.js' />";
				
	echo "<listbox id='Contenedor' rows='5' onclick='opener.changeContenedor(this,fam[this.value]);window.close();return true;'>\n";
	echo  genXulComboContenedores();				
	echo "</listbox>";
        echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
	echo "</groupbox>";


EndXul();

?>
