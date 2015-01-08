<?php

include("../../tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");
$idfamilia  = CleanID($_GET['idfamilia']);
$txtMoDet   = getModeloDetalle2txt();
$txtModelo  = $txtMoDet[1];
$txtDetalle = $txtMoDet[2];

StartXul(_("Elije Propiedades del Producto"));
 

     echo "<script>\n";
     echo " function soloAlfaNumerico(e){ 
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = ' abcdefghijklmnñopqrstuvwxyz0123456789%-.';
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
                }";
     echo "\n</script>\n";
     $max = 0;

     switch($modo){
     case "nuevocolor":
       
       $color  = CleanRealMysql(CleanText($_GET["color"]));
       $sql    = "SELECT IdColor FROM ges_modelos WHERE  IdFamilia='$idfamilia' AND Color='$color' ";
       $row    = queryrow($sql);
       if (!$row)
	 {
	   $sql      = "SELECT Max(IdColor) as MaxCol FROM ges_modelos";
	   $xrow      = queryrow($sql);
	   $IdIdioma = getSesionDato("IdLenguajeDefecto");
	   $max      = intval($xrow["MaxCol"])+1; 	
	   $color    = CleanRealMysql(CleanText($_GET["color"]));
	   $sql      = "INSERT INTO ges_modelos 
                        (IdColor, IdIdioma, Color, IdFamilia) 
                        VALUES ( '$max', '$IdIdioma', '$color', '$idfamilia' )";
	   query($sql,"Creando nuevo ".$txtModelo);
       } 
       if ($row) $max = $row["IdColor"];

     case "color":

       $familias = genArrayColores($idfamilia);
       $combo    = "";
       
       echo "<groupbox> <caption label='Buscar ".$txtModelo."'/>";
       echo "<vbox>";
       echo "<textbox  flex='1'   id='buscapresentacion' style='text-transform:uppercase;' onkeyup='javascript:this.value=this.value.toUpperCase();BuscarPresentacion();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' />";
       echo "<button label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo()'/>";
       echo "</vbox>";
       echo "</groupbox>";
       echo "<groupbox> <caption label='" . $txtModelo . "'/>";

       echo "<script>\n";
       echo " var fam =new Object();\n";
       foreach ($familias as $key=>$value){ echo "fam[$key] = '$value';\n"; }
       if($max) echo "opener.changeNewColor('".$max."','".$color."');window.close();";
       echo "
        function UsarNuevo() {
            var color, url;
            var idfamilia =".$idfamilia.";			
            var nuevocolor = document.getElementById('buscapresentacion');
            if (nuevocolor){
                color = nuevocolor.value;
                //color = trim(color);
                color = limpiarcadena(color);
            }
            if (!color || color == '') return;
            url = 'selmodelo.php';
            url = url +'?';
            url = url + 'modo';
            url = url + '=nuevocolor';
            url = url + '&amp;'+'color=' + color;
            url = url + '&amp;'+'idfamilia=' + idfamilia;
            document.location.href = url;			
        }";
       echo "\n</script>\n";						
       echo "<script  type='application/x-javascript' src='presentacion.js' />";
       echo "<listbox rows='5' flex='1' id='Color'  onclick='opener.changeColor(this,fam[this.value]);window.close();return true;'>\n";		
       echo  genXulComboColores($selected=false,$xul="listitem", $idfamilia,false);
       echo "</listbox>";		
       echo "<spacer flex='1'/>";
       echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
       echo "</groupbox>";

       break;		

     case "xtallaje":
       echo "<groupbox flex='1'> <caption label='" . $txtDetalle . " :'/>";

       $sql = "SELECT IdTallaje,Tallaje FROM ges_detallescategoria ORDER BY Tallaje ASC";
       $res = query($sql);
       while( $row= Row($res) ) {
	 $txtalla = 	$row["Tallaje"];
	 $idtalla =  $row["IdTallaje"];

	 if (getParametro("TallajeLatin1")){				
	   $txtalla = iso2utf($txtalla);	
	 }

	 echo "<button label='". $txtalla."' oncommand='changeNuestroTallaje(\"".$idtalla."\",\"".$txtalla."\",opener);'/>";	
       }				
       echo "<spacer flex='1'/>";
       echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	

       echo "<script>
        function changeNuestroTallaje(idtallaje,txt,padre) {
            padre.changeTallaje(idtallaje,txt);
            window.close(); 			
        }
        </script>";

       echo "</groupbox>";
       break;


     case "tallaje":
       echo "<groupbox flex='1'> <caption label='" . $txtDetalle . " :'/>";

       $sql = "SELECT IdTallaje,Tallaje FROM ges_detallescategoria ORDER BY Tallaje ASC";
       $res = query($sql);
       while( $row= Row($res) ) {
	 $txtalla = $row["Tallaje"];

	 if (getParametro("TallajeLatin1")){				
	   $txtalla = iso2utf($txtalla);	
	 }

	 echo "<button label='". $txtalla."' oncommand='UsaTallaje(".$row["IdTallaje"].",".$idfamilia.")'/>";	
       }				
       echo "<spacer flex='1'/>";
       echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	

       echo "<script>
            function UsaTallaje(id,idfamilia){		
                document.location.href = 'selmodelo.php?modo=talla&amp;IdTallaje='+id+'&amp;idfamilia='+idfamilia;
            }

            function changeTalla(me,val) {
                document.opener.changeTalla(me,val);
            }
            </script>";

       echo "</groupbox>";
       break;

     case "nuevatalla":
       $talla     = CleanRealMysql(CleanText($_GET["talla"]));
       $tallaje   = CleanID($_GET["IdTallaje"]);
       $sql       = "SELECT IdTalla FROM ges_detalles WHERE IdFamilia=$idfamilia AND IdTallaje = $tallaje AND Talla='$talla' ";
       $row       = queryrow($sql);

       if (!isset($row["IdTalla"]) && $tallaje <> 0)
	 {
	   $sql      = "SELECT Max(IdTalla) as MaxTal FROM ges_detalles";
	   $xrow     = queryrow($sql);
	   $IdIdioma = getSesionDato("IdLenguajeDefecto");
	   $max      = intval($xrow["MaxTal"])+1; 	
	   $sql      = "INSERT INTO ges_detalles 
                       (IdTalla, IdIdioma, Talla, IdTallaje, IdFamilia) 
                       VALUES ( '$max', '$IdIdioma', '$talla', '$tallaje', '$idfamilia')";
	   query($sql,"Creando nuevo ".$txtDetalle );
	 }
       if (isset($row["IdTalla"])) $max = $row["IdTalla"];

     case "talla":		
       $IdTallaje = CleanID($_GET["IdTallaje"]);
       $familias = genArrayTallas($IdTallaje,$idfamilia);
       $combo    = "";

       echo "<groupbox> <caption label='Buscar ".$txtDetalle."'/>";
       echo "<vbox>";
       echo "<textbox  flex='1'   id='buscapresentacion' style='text-transform:uppercase;' onkeyup='javascript:this.value=this.value.toUpperCase();BuscarSubPresentacion();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)'/>";
       echo "<button label='"._("Nuevo")."' onkeypress='if (event.which == 13) UsarNuevo()' oncommand='UsarNuevo(".$IdTallaje.")'/>";
       echo "</vbox>";
       echo "</groupbox>";
       echo "<groupbox flex='1'>
             <caption label='" . $txtDetalle . "'/>";

       echo "<script  type='application/x-javascript' src='subpresentacion.js' />";
       echo "<script>\n";
       echo " var fam =new Object();\n";
       foreach ( $familias as $key=>$value ){ echo "fam[$key] = '$value';\n"; }
       if( $max ) echo "opener.changeNewTalla('".$max."','".$talla."');window.close();";

       echo " 
            function UsarNuevo(IdTallaje) {
                var talla, url;
                var idfamilia =".$idfamilia.";			
                var nuevocolor = document.getElementById('buscapresentacion');			
                if (nuevocolor){
                    talla = nuevocolor.value;
                    talla = trim(talla);
                    talla = limpiarcadena(talla);
                }
                if (!talla || talla == '')
                    return;

                url = 'selmodelo.php';
                url = url +'?';
                url = url + 'modo';
                url = url + '=nuevatalla';
                url = url + '&amp;'+'talla=' + talla;
                url = url + '&amp;'+'IdTallaje=' + IdTallaje;
                url = url + '&amp;'+'idfamilia=' + idfamilia;
                document.location.href = url;
        } 


        ";

       echo "\n</script>";						
       
       
       echo "<listbox id='Talla' flex='1' onclick='opener.changeTalla(this,fam[this.value]);window.close();return true;'>\n";
       echo  genXulComboTallas(false,"listitem",$IdTallaje,false,$idfamilia);				
       echo "</listbox>";
       echo "<spacer flex='1'/>";
       echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
       echo "</groupbox>";

       break;		

     default:
       break;	
     }

//PageEnd();
EndXul();

?>
