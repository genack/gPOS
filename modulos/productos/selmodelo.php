<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");
$idfamilia  = CleanID($_GET['idfamilia']);
$txtMoDet   = getGiroNegocio2txt();
$txtModelo  = $txtMoDet[1];
$txtDetalle = $txtMoDet[2];

StartXul(_("Elije Propiedades del Producto"));
 

     echo "<script>\n";
     echo " function soloAlfaNumerico(e){ 
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = ' abcdefghijklmnñopqrstuvwxyz0123456789%-.+/';
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
                }";
     echo "\n</script>\n";
     $max = 0;

     switch($modo){

     case "nuevocolor":
       if( $modo == "nuevocolor")
	 {
	   $color  = CleanRealMysql(CleanText($_GET["color"]));
	   $sql    = "select IdColor from ges_modelos where IdFamilia='$idfamilia' and Color='$color'";
	   $row    = queryrow($sql);
	   if (!$row)
	     {
	       $color    = CleanText($_GET["color"]);
	       $IdIdioma = getSesionDato("IdLenguajeDefecto");
	       global $UltimaInsercion;
	       $sql      = "insert into ges_modelos 
                           ( IdIdioma, Color, IdFamilia) 
                           values ( '$IdIdioma', '$color', '$idfamilia' )";
	       query($sql,"Creando nuevo ".$txtModelo);
	       $max      = $UltimaInsercion;
	       $sql = "update ges_modelos  set IdColor=$max where Id=".$max;
	       query($sql);
	     } else{
	     // devolvemos a la vida una marca existente
	     $sql = "update ges_modelos  set Eliminado=0 where IdColor=".$row['IdColor'];
	     query($sql);
	   }
	   if ($row) $max = $row["IdColor"];
       }

     case "modificacolor":
      if( $modo == "modificacolor")
	{
	  $color = CleanText($_GET["txt"]);
	  $idcolor = CleanID($_GET["xid"]);
	  $sql = "UPDATE ges_modelos SET Color='$color' WHERE IdColor='$idcolor'";
	  query($sql);	
	}

    case "eliminacolor":
      if( $modo == "eliminacolor")
	{
	  $color = CleanText($_GET["txt"]);
	  $idcolor = CleanID($_GET["xid"]);
	  $sql = "UPDATE ges_modelos SET Eliminado=1 WHERE IdColor='$idcolor'";
	  query($sql);	
	}

     case "color":

       $familias = genArrayColores($idfamilia);
       $combo    = "";
       
       echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar ".$txtModelo."'/>";
       echo "<vbox>";
       echo "<textbox  flex='1'   id='buscapresentacion' style='text-transform:uppercase;' onkeyup='javascript:BuscarPresentacion();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' />";
       echo "<button class='btn' id='btnNuevoColor' label='"._("Nuevo")."' oncommand='UsarNuevo()'  collapsed='true' />";
       echo "</vbox>";
       echo "</groupbox>";
       echo "<groupbox> <caption class='box' label='" . $txtModelo . "'/>";

       echo "<script>\n";
       echo " var fam =new Object();\n";
       foreach ($familias as $key=>$value){ echo "fam[$key] = '$value';\n"; }
       if($max) echo "parent.changeNewColor('".$max."','".$color."');parent.closepopup();";
       echo " var cIdFamiliaColor = ".$idfamilia.";\n";
       echo " var cModoPresentacion = '".$modo."';";
       echo " var ctxtModelo = '".$txtModelo."';";
       echo "\n</script>\n";						
       echo "<script  type='application/x-javascript' src='presentacion.js?v=3.1' />";
       echo "<listbox flex='1' id='Color'  ondblclick='parent.changeColor(this,fam[this.value]);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changeColor(this,fam[this.value]);parent.closepopup();return true; }' onclick='seleccionarModelo(fam[this.value])' contextmenu='accionesListaColor'>\n";		
       echo  genXulComboColores($selected=false,$xul="listitem", $idfamilia,false);
       echo "</listbox>";		
       echo "<spacer flex='1'/>";
       echo "<popupset>
       <popup id='accionesListaColor' class='media'> 
        <menuitem  label='Modificar' oncommand='ModificarColor()'/>
        <menuitem  label='Eliminar' oncommand='EliminarColor()'/>
       </popup>
      </popupset>";

       echo "</groupbox> </vbox>";

       break;		

     case "xtallaje":
       echo "<vbox class='box' flex='1'><groupbox flex='1'> <caption class='box' label='" . $txtDetalle . " :'/>";

       $sql = "select IdTallaje,Tallaje from ges_detallescategoria order by Tallaje ASC";
       $res = query($sql);
       while( $row= Row($res) ) {
	 $txtalla = 	$row["Tallaje"];
	 $idtalla =  $row["IdTallaje"];

	 if (getParametro("TallajeLatin1")){				
	   $txtalla = iso2utf($txtalla);	
	 }

	 echo "<button class='btn' label='". $txtalla."' oncommand='changeNuestroTallaje(\"".$idtalla."\",\"".$txtalla."\",opener);'/>";	
       }				
       echo "<spacer flex='1'/>";

       echo "<script>
        function changeNuestroTallaje(idtallaje,txt,padre) {
            parent.changeTallaje(idtallaje,txt);
            parent.closepopup();			
        }
        function loadfocus(){}
        </script>";

       echo "</groupbox></vbox>";
       break;


     case "tallaje":
       echo "<vbox class='box' flex='1'><groupbox flex='1'> <caption class='box' label='" . $txtDetalle . " :'/>";

       $sql = "SELECT IdTallaje,Tallaje FROM ges_detallescategoria ORDER BY Tallaje ASC";
       $res = query($sql);
       while( $row= Row($res) ) {
	 $txtalla = $row["Tallaje"];

	 if (getParametro("TallajeLatin1")){				
	   $txtalla = iso2utf($txtalla);	
	 }

	 echo "<button class='btn' label='". $txtalla."' oncommand='UsaTallaje(".$row["IdTallaje"].",".$idfamilia.")'/>";	
       }				
       echo "<spacer flex='1'/>";
       echo "<script>
            function UsaTallaje(id,idfamilia){		
                document.location.href = 'selmodelo.php?modo=talla&amp;IdTallaje='+id+'&amp;idfamilia='+idfamilia;
            }

            function changeTalla(me,val) {
                document.parent.changeTalla(me,val);
            }
            function loadfocus(){}
            </script>";

       echo "</groupbox></vbox>";
       break;

     case "nuevatalla":
       $talla     = CleanText($_GET["talla"]);
       $tallaje   = CleanID($_GET["IdTallaje"]);
       if( $tallaje > 0)
	 {
	   $sql       = "select IdTalla from ges_detalles
	                 where IdFamilia = $idfamilia 
                         and   IdTallaje = $tallaje and talla = '$talla' ";
	   $row       = queryrow($sql);
	   if (!$row)
	     {
	       $IdIdioma = getSesionDato("IdLenguajeDefecto");
	       global $UltimaInsercion;

	       $sql      = "insert into ges_detalles 
                       ( IdIdioma, Talla, IdTallaje, IdFamilia) 
                       values ( '$IdIdioma', '$talla', '$tallaje', '$idfamilia')";
	       query($sql,"Creando nuevo ".$txtDetalle );
	       $max      = $UltimaInsercion;
	       $sql = "UPDATE ges_detalles  SET IdTalla=$max WHERE Id=".$max;
	       query($sql);
	     }
	   else
	     {
	       $max = $row["IdTalla"];
	       // devolvemos a la vida una marca existente
	       $sql = "UPDATE ges_detalles  SET Eliminado=0 WHERE IdTalla=".$max;
	       query($sql);
	       
	     }
	 }
     case "modificatalla":
      if( $modo == "modificatalla")
	{
	  $talla  = CleanText($_GET["txt"]);
	  $idtalla = CleanID($_GET["xid"]);
	  $sql = "UPDATE ges_detalles SET Talla='$talla' WHERE IdTalla='$idtalla'";
	  query($sql);	
	}

    case "eliminatalla":
      if( $modo == "eliminatalla")
	{
	  $talla = CleanText($_GET["txt"]);
	  $idtalla = CleanID($_GET["xid"]);
	  $sql = "UPDATE ges_detalles SET Eliminado=1 WHERE IdTalla='$idtalla'";
	  query($sql);	
	}

     case "talla":		
       $IdTallaje = CleanID($_GET["IdTallaje"]);
       $familias = genArrayTallas($IdTallaje,$idfamilia);
       $combo    = "";

       echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar ".$txtDetalle."'/>";
       echo "<vbox>";
       echo "<textbox  flex='1'   id='buscapresentacion' style='text-transform:uppercase;' onkeyup='javascript:BuscarSubPresentacion();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)'/>";
       echo "<button class='btn' id='btnNuevaTalla' label='"._("Nuevo")."' oncommand='UsarNuevo(".$IdTallaje.")' collapsed='true'/>";
       echo "</vbox>";
       echo "</groupbox>";
       echo "<groupbox flex='1'>
             <caption class='box' label='" . $txtDetalle . "'/>";

       echo "<script  type='application/x-javascript' src='subpresentacion.js?v=3.1' />";
       echo "<script>\n";
       echo " var fam =new Object();\n";
       foreach ( $familias as $key=>$value ){ echo "fam[$key] = '$value';\n"; }
       if( $max ) echo "parent.changeNewTalla('".$max."','".$talla."');parent.closepopup();";
       echo " var cIdFamiliaTalla = ".$idfamilia.";\n";
       echo " var cIdTallaje = ".$IdTallaje.";\n";
       echo " var cModoPresentacion = '".$modo."';";
       echo " var ctxtDetalle = '".$txtDetalle."';";
       echo "\n</script>";						
       
       
       echo "<listbox  id='Talla' flex='1'  ondblclick='parent.changeTalla(this,fam[this.value]);parent.closepopup();return true;'  onkeypress='if (event.which == 13) {parent.changeTalla(this,fam[this.value]);parent.closepopup();return true;}' onclick='seleccionarDetalle(fam[this.value])' contextmenu='accionesListaTalla'>\n";
       echo  genXulComboTallas(false,"listitem",$IdTallaje,false,$idfamilia);				
       echo "</listbox>";
       echo "<spacer flex='1'/>";
       echo "<popupset>
       <popup id='accionesListaTalla'> 
        <menuitem  label='Modificar' oncommand='ModificarTalla()'/>
        <menuitem  label='Eliminar'  oncommand='EliminarTalla()'/>
       </popup>
      </popupset>";
       echo "</groupbox></vbox>";

       break;		

     default:
       break;	
     }

//PageEnd();
EndXul();

?>
