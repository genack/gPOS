<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije Empaque")); 

$contenedor = '';
switch($modo){
	case "salvacontenedor":
		$contenedor = CleanText($_GET["contenedor"]);
		if (!$contenedor or $contenedor == "")
			break;
		
		$sql = "SELECT IdContenedor FROM ges_contenedores WHERE Contenedor='$contenedor'";
		$row = queryrow($sql);
		
		if ($row and $row["IdContenedor"]) {
		  $idold = $row["IdContenedor"];
		  // devolvemos a la vida una contenedor existente
		  $sql = "UPDATE ges_contenedores SET Eliminado=0 WHERE IdContenedor='$idold'";
		  query($sql);
		  break;		
		}
		
		query("INSERT INTO ges_contenedores (Contenedor) VALUES ('$contenedor')");
		break;
		
	case "modificacontenedor":
	        $contenedor   = CleanText($_GET["txt"]);
		$idcontenedor = CleanID($_GET["xid"]);
		$sql = "UPDATE ges_contenedores SET Contenedor='$contenedor' WHERE IdContenedor='$idcontenedor'";
		query($sql);	
		break;
	case "eliminarcontenedor":
	        $contenedor   = '';//CleanText($_GET["txt"]);
		$idcontenedor = CleanID($_GET["xid"]);
		$sql = "UPDATE ges_contenedores SET Eliminado=1 WHERE IdContenedor='$idcontenedor'";
		query($sql);	
		break;
	default:
		break;	
}


//SE EJECUTA SIEMPRE

    echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar Empaque:'/>";
     echo "<hbox>";
     echo "<textbox  flex='1'   id='buscacontenedor' onkeyup='BuscarContenedor();   if (event.which == 13) agnadirDirecto();' onkeypress='javascript: return soloAlfaNumerico(event)' value='".$contenedor."' /> ";
    echo "</hbox>";
    echo "<hbox flex='1'>";
    echo "<button class='btn' flex='1' label='"._("Nuevo")."' oncommand='UsarNuevo()' id='btnNuevContenedor' collapsed='true' />";
    echo "</hbox>";
    echo "</groupbox>";

    echo "<groupbox><caption class='box' label='Empaque:'/>";

    $familias = genArrayContenedores();
    $combo = "";
    echo "<script>\n";
    echo " var fam =new Object();\n";
    foreach ($familias as $key=>$value){
      echo "fam[$key] = '$value';\n";
    }
    echo " var cContendorLoad = '$contenedor';\n";
    echo "\n</script>";						

        echo "<script  type='application/x-javascript' src='contenedor.js?v=3.1' />";
				
	echo "<listbox id='Contenedor' ondblclick='parent.changeContenedor(this,fam[this.value]);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changeContenedor(this,fam[this.value]);parent.closepopup();return true; }' contextmenu='accionesListaContenedor' >\n";
	echo  genXulComboContenedores();				
	echo "</listbox>";
        echo "<popupset>
                 <popup id='accionesListaContenedor'> 
                  <menuitem  label='Modificar' oncommand='ModificarContenedor()'/>
                  <menuitem  label='Eliminar'  oncommand='EliminarContenedor()'/>
                </popup>
              </popupset>";

        //echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
	echo "</groupbox></vbox>";


EndXul();

?>
