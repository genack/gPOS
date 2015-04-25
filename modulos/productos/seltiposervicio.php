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
      query("INSERT INTO ges_tiposervicio (TipoServicio,SAT) VALUES ('$servicio','$essat')");
      $max = $UltimaInsercion;

      break;

    case "eliminatiposervicio":
      $servicio = CleanText($_GET["marca"]);
      $sql = "UPDATE ges_tiposervicio SET Eliminado=1 WHERE TipoServicioMarca='$servicio'";
      query($sql);	
      break;
    default:
      break;	
    }
//Carga NUEVO
    if($max) echo "<script> opener.changeNewTipoServicio('".$max."','".$servicio."');window.close();</script>";
 
//SE EJECUTA SIEMPRE

    echo "<groupbox> <caption label='Buscar Tipo Servicio'/>";
    echo "<hbox>";
    echo "<textbox  flex='1'   id='buscatiposervicio' onkeyup='BuscarTipoServicio();   if (event.which == 13) agnadirDirecto(); ' onkeypress='return soloAlfaNumerico(event)' />";
    echo "</hbox>";
    echo "<vbox flex='1' id='boxnuevo' collapsed='true'>";
    echo "<checkbox id='esSAT' label='Servicio de Asistencia Técnica'  type='checkbox' checked='false' />";
    echo "<button flex='1' label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
    echo "</vbox>";


    echo "</groupbox>";
    echo "<groupbox><caption label='" . _("Tipo Servicios") . "'/>";


    $familias = genArrayTipoServicios();
    $combo = "";
    echo "<script>\n";
    echo " fam =new Object();\n";
    foreach ($familias as $key=>$value){
      echo "fam[$key] = '$value';\n";
    }
    echo "
		function UsarNuevo() {
			var talla, url;
			var nuevocolor = document.getElementById('buscatiposervicio');
			var essat      = document.getElementById('esSAT');

			if (nuevocolor)
                             talla = nuevocolor.value;

                        if (!talla || talla == '')
                             return;
			url = 'seltiposervicio.php';
			url = url +'?';
                        url = url + 'modo';
                        url = url + '=salvatiposervicio';
                        url = url + '&amp;'+'tiposervicio=' + talla;
                        url = url + '&amp;'+'essat=' + essat.checked;

			document.location.href = url;			
		}
		
		function Eliminar() {
			var marcaname, url;
			var lamarca = document.getElementById('buscatiposervicio');	
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
                        letras = ' abcdefghijklmnopqrstuvwxyz0123456789-';
                        especiales = [8, 13, 9];
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
echo "<script  type='application/x-javascript' src='tiposervicio.js' />";
echo "<listbox id='TipoServicio' rows='5' onclick='opener.changeTipoServicio(this.value,fam[this.value]);window.close();return true;'>\n";
echo  genXulComboTipoServicios();				
echo "</listbox>";
echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
echo "</groupbox>";


EndXul();
?>
