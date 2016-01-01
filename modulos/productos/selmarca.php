<?php

include("../../tool.php");

$marca = '';
$sat   = false;

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
      $idmarca = CleanID($_GET["xid"]);
      $sql = "UPDATE ges_marcas SET Eliminado=1 WHERE IdMarca='$idmarca'";
      query($sql);	
      break;

    case "modificamarca":
      $marca = CleanText($_GET["txt"]);
      $idmarca = CleanID($_GET["xid"]);
      $sql = "UPDATE ges_marcas SET Marca='$marca' WHERE IdMarca='$idmarca'";
      query($sql);	
      break;
    default:
      break;	
    }

    SimpleAutentificacionAutomatica("visual-xulframe");
    StartXul(_("Elije Marca")); 

//SE EJECUTA SIEMPRE

    echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar Marca'/>";
    echo "<hbox>";
    echo "<textbox  flex='1'   id='buscamarca' onkeyup='BuscarMarca();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' value='".$marca."' />";
    echo "</hbox>";
    echo "<hbox flex='1'>";
    echo "<button class='btn' id='btnNuevaMarca' flex='1' label='"._("Nuevo")."' oncommand='UsarNuevo()' collapsed='true'/>";
    echo "</hbox>";


    echo "</groupbox> ";
    echo "<groupbox><caption class='box' label='" . _("Marcas") . "'/>";

    $familias = genArrayMarcas();
    $combo = "";
    echo "<script>\n";
    echo " fam =new Object();\n";
		foreach ($familias as $key=>$value){
			echo "fam[$key] = '$value';\n";
		}
    echo "\n</script>";						
    echo "<script  type='application/x-javascript' src='marca.js?v=3.1' />";
    echo "<listbox id='listboxMarca' ondblclick='parent.changeMarca(this,fam[this.value]);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changeMarca(this,fam[this.value]);parent.closepopup();return true;}' contextmenu='accionesListaMarcas' >\n";
    echo  genXulComboMarcas();				
    echo "</listbox>";

    echo "<popupset>
       <popup id='accionesListaMarcas'> 
        <menuitem  label='Modificar' oncommand='ModificarMarca()'/>
        <menuitem  label='Eliminar' oncommand='EliminarMarca()'/>
       </popup>
      </popupset>";

    //echo "<button flex='1' label='"._("Eliminar")."' onkeypress='if (event.which == 13) Eliminar()' oncommand='Eliminar()'/>";
    //echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
    echo "</groupbox> </vbox>";

EndXul();






?>
