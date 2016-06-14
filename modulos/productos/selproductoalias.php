<?php

include("../../tool.php");
$txtMoDet    = getGiroNegocio2txt();
$txtalias    = $txtMoDet[3];
$idfamilia   = CleanID($_GET['idfamilia']);
$idalias     = CleanText($_GET['id']);

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije ".$txtalias));


switch($modo){

case "modificaalias":
  if( $modo == "modificaalias")
    {
      $productoalias = '';
      $alias         = CleanText($_GET["txt"]);
      $idalias       = CleanID($_GET["xid"]);
      $sql = "UPDATE ges_productos_alias SET ProductoAlias='$alias' WHERE IdProductoAlias='$idalias'";
      query($sql);	
    }

case "eliminaalias":
  if( $modo == "eliminaalias")
    {
      $productoalias = '';
      $alias         = CleanText($_GET["txt"]);
      $idalias       = CleanID($_GET["xid"]);
      $sql = "UPDATE ges_productos_alias SET Eliminado=1 WHERE IdProductoAlias='$idalias'";
      query($sql);	
    }

case "nuevoproductoalias":
  if( $modo == "nuevoproductoalias"){
    $productoalias = CleanRealMysql(CleanText($_GET["productoalias"]));
    $sql = "select IdProductoAlias
            from   ges_productos_alias
            where  IdFamilia     ='".$idfamilia."'
            and    ProductoAlias ='".$productoalias."'";
    $row = queryrow($sql);
    if (!$row){
	$productoalias = CleanRealMysql(CleanText($_GET["productoalias"]));
	$IdIdioma      = getSesionDato("IdLenguajeDefecto");
	global $UltimaInsercion;

	$sql           = "insert into ges_productos_alias 
                          (IdIdioma, ProductoAlias, IdFamilia) 
                          values ('".$IdIdioma."','".$productoalias."','".$idfamilia."')";
	query($sql,"Creando nuevo Alias");
	$max      = $UltimaInsercion;
	$sql = "UPDATE  ges_productos_alias SET IdProductoAlias=$max WHERE Id=".$max;
	query($sql);	       
    } 
    else {
      // devolvemos a la vida una marca existente
      $sql = "UPDATE  ges_productos_alias SET Eliminado=0 WHERE IdProductoAlias=".$row['IdProductoAlias'];
      query($sql);
    }
  }
case "alias": 

    echo "<vbox class='box' flex='1'><groupbox> <caption class='box' label='Buscar $txtalias:'/>";
    echo "<vbox>";
    echo "<textbox  flex='1'   id='buscaalias' style='text-transform:uppercase;' onkeyup='javascript:BuscarAlias();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)'/>";
    echo "<button class='btn' id='btnNuevoAlias' label='"._("Nuevo ")."' oncommand='UsarNuevo()' collapsed='true'/>";
    echo "</vbox>";
    echo "</groupbox>";
    echo "<groupbox> <caption class='box' label=' ".$txtalias.":'/>";

    $familias = genArrayProductoAlias($idfamilia);
    $combo = "";
    echo "<script>\n";
    echo " var fam =new Object();\n";
    foreach ($familias as $key=>$value){
        echo "fam[$key] = '$value';\n";
        //$combo = "<option 			
    }

    if(isset($max)) {
	echo "parent.changeNewProductoAlias('".$max."','".$productoalias."','".$idalias."');parent.closepopup();";
    }
    echo " var cIdFamiliaColor = ".$idfamilia.";\n";
    echo " var cId             = '".$idalias."';";
    echo " var ctxtAlias       = '".$txtalias."';";

echo "\n</script>\n";						

echo "<script  type='application/x-javascript' src='alias.js?v=3.1' />";

echo "<listbox flex='1' id='ProductoAlias'  ondblclick='parent.changeProductoAlias(this,fam[this.value],".$idalias.");parent.closepopup();return true;' onkeypress='if (event.which == 13) {parent.changeProductoAlias(this,fam[this.value],".$idalias.");parent.closepopup();return true;}' contextmenu='accionesListaAlias' >\n";		
echo  genXulComboProductoAlias($selected=false,$xul="listitem", $idfamilia,false);				
echo "</listbox>";		
echo "<popupset>
       <popup id='accionesListaAlias'> 
        <menuitem  label='Modificar' oncommand='ModificarAlias()'/>
        <menuitem  label='Eliminar'  oncommand='EliminarAlias()'/>
       </popup>
      </popupset>";

//echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
echo "</groupbox></vbox>";

break;		

default:
    break;	
}

//PageEnd();
EndXul();

?>
