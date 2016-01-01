<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije Tipo Servicio")); 

    $max = false;

    switch($modo){
    case "salvatiposervicio":

      $servicio = CleanText($_GET["tiposervicio"]);
      $essat    = ( CleanText($_GET["essat"]) == 'true' )? 1:0;

      if (!$servicio or $servicio == "") break;
      
      $sql = "SELECT IdTipoServicio FROM ges_tiposervicio WHERE TipoServicio='$servicio'";
      $row = queryrow($sql);
      
      if ($row and $row["IdTipoServicio"]) 
	{
	  $idold = $row["IdTipoServicio"];
	  $sql = "UPDATE ges_tiposervicio SET Eliminado=0 WHERE IdTipoServicio='$idold'";
	  query($sql);// devolvemos a la vida una marca existente
	  break;		
	}      

      global $UltimaInsercion;
      query("insert into ges_tiposervicio (TipoServicio,SAT) values ('$servicio','$essat')");
      $max = $UltimaInsercion;

      break;

    case "eliminatiposervicio":
      $idservicio = CleanID($_GET["xid"]);
      $servicio = CleanText($_GET["txt"]);
      $sql = "update ges_tiposervicio set Eliminado=1 where IdTipoServicio='$idservicio'";
      query($sql);	
      break;

    case "modificatiposervicio":
      $idservicio = CleanID($_GET["xid"]);
      $servicio   = str_ireplace(" - SAT","", CleanText($_GET["txt"]) );
      $sql = "update ges_tiposervicio set TipoServicio='$servicio' where IdTipoServicio='$idservicio'";
      query($sql);	
      break;
    }
//Carga NUEVO
    if($max) echo "<script> parent.changeNewTipoServicio('".$max."','".$servicio."');parent.closepopup();</script>";
 
//SE EJECUTA SIEMPRE

    echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar Tipo Servicio'/>";
    echo "<hbox>";
    echo "<textbox  flex='1'   id='buscatiposervicio' onkeyup='BuscarTipoServicio();   if (event.which == 13) agnadirDirecto(); ' onkeypress='return soloAlfaNumerico(event)' />";
    echo "</hbox>";
    echo "<vbox flex='1' id='boxnuevo' collapsed='true'>";
    echo "<checkbox id='esSAT' label='Servicio de Asistencia Técnica'  type='checkbox' checked='false' />";
    echo "<button class='btn' flex='1' label='"._("Nuevo")."' oncommand='UsarNuevo()'/>";
    echo "</vbox>";


    echo "</groupbox>";
    echo "<groupbox><caption class='box' label='" . _("Tipo Servicios") . "'/>";


    $familias = genArrayTipoServicios();
    $combo = "";
    echo "<script>\n";
    echo " fam =new Object();\n";
    foreach ($familias as $key=>$value){ echo "fam[$key] = '$value';\n";}
    echo "\n</script>";						
    echo "<script  type='application/x-javascript' src='tiposervicio.js?v=3.1' />";
    echo "<listbox id='TipoServicio'  ondblclick='parent.changeTipoServicio(this.value,fam[this.value]);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changeTipoServicio(this.value,fam[this.value]);parent.closepopup();return true; }' contextmenu='accionesListaTipoServicio'>\n";
    echo  genXulComboTipoServicios();				
    echo "</listbox>";
    echo "<popupset>
       <popup id='accionesListaTipoServicio'> 
        <menuitem  label='Modificar' oncommand='ModificarTipoServicio()'/>
        <menuitem  label='Eliminar' oncommand='EliminarTipoServicio()'/>
       </popup>
      </popupset>";

//echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
echo "</groupbox></vbox>";


EndXul();
?>
