<?php

include("../../tool.php");

$partida = '';

$IdLocal = (isset($_GET["xidl"]))? CleanID($_GET["xidl"]):0;
$IdLocal = ($IdLocal == 0)? getSesionDato("IdTienda"):$IdLocal;
$operacion = CleanText($_GET["xop"]);
$tipocaja = CleanText($_GET["cja"]);

function obtenerCodigoPartida(){
  $sql = "SELECT Codigo FROM ges_partidascaja ".
         "WHERE Codigo like '%U%' ".
         "ORDER BY IdPartidaCaja DESC ".
         "LIMIT 1";

  $row = queryrow($sql);
  
  if(!$row["Codigo"] || !$row || $row["Codigo"] == ''){
    $newcod = "U101";
    return $newcod;
  }

  $ultimoCod = $row["Codigo"];
  $xcod = substr($ultimoCod, 1, 3);
  $xnum = (int)$xcod;
  $nnum = $xnum+1;
  $snum = strval($nnum);
  $newcod = "U".$snum;

  return $newcod;
}

switch($modo){
  case "salvapartida":

    $partida   = CleanText($_GET["partida"]);
    $operacion = CleanText($_GET["xop"]);
    $tipocaja  = CleanText($_GET["cja"]);

    $codpartida = obtenerCodigoPartida();
    
    if (!$partida or $partida == "") break;
    
    $sql = "SELECT IdPartidaCaja FROM ges_partidascaja ".
           " WHERE PartidaCaja = '$partida' ".
           " AND TipoOperacion = '$operacion' ";
           " AND TipoCaja = '$tipocaja' ";
    $row = queryrow($sql);
    
    if ($row and $row["IdPartidaCaja"]) 
      {
	$idold = $row["IdPartidaCaja"];
	$sql = "UPDATE ges_partidascaja SET Eliminado=0 WHERE IdPartidaCaja='$idold'";
	query($sql);// devolvemos a la vida una partida existente
	break;		
      }
    
    global $UltimaInsercion;
    query("INSERT INTO ges_partidascaja (PartidaCaja,TipoCaja,TipoOperacion,IdLocal,Codigo) 
           VALUES ('$partida','$tipocaja','$operacion','$IdLocal','$codpartida')");
    break;
  
  case "eliminapartida":
    $codpartida = CleanCadena($_GET["xcod"]);
    $partida    = CleanText($_GET["txt"]);

    $pos = strpos($codpartida, 'S');
    if ($pos !== false) break;

    $sql = "UPDATE ges_partidascaja SET Eliminado=1 WHERE Codigo='$codpartida'";
    query($sql);	
    break;
    
  case "modificapartida":
    $partida    = CleanText($_GET["txt"]);
    $codpartida = CleanCadena($_GET["xcod"]);
    $oldpartida   = CleanText($_GET["txtold"]);

    $pos = strpos($codpartida, 'S');
    if ($pos !== false) break;
    
    $sql = "SELECT IdPartidaCaja FROM ges_partidascaja ".
           " WHERE PartidaCaja = '$partida' ".
           " AND TipoOperacion = '$operacion' ";
           " AND TipoCaja = '$tipocaja' ";
    $row = queryrow($sql);
    
    if ($row and $row["IdPartidaCaja"]) break;		

    $sql = "UPDATE ges_partidascaja SET PartidaCaja='$partida' WHERE Codigo='$codpartida'";
    query($sql);	
    break;
  default:
    break;	
}

SimpleAutentificacionAutomatica("visual-xulframe");
StartXul(_("Elije Partida")); 
StartJs($js='modulos/partidas/partidas.js?v=3.1');
//SE EJECUTA SIEMPRE

echo "<vbox class='box' flex='1'><groupbox> <caption label='Buscar Partida' class='box'/>";
echo "<hbox>";
echo "<textbox  flex='1'   id='buscapartida' onkeyup='BuscarPartida();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' value='".$partida."' />";
echo "</hbox>";
echo "<hbox flex='1'>";
echo "<button id='btnNuevaPartida' flex='1' label='"._("Nuevo")."' oncommand='UsarNuevo()' collapsed='true' class='btn'/>";
echo "</hbox>";


echo "</groupbox>";
echo "<groupbox><caption label='" . _("Partidas") . "' class='box'/>";

$familias = genArrayPartidas($operacion,$tipocaja,$IdLocal);
$combo = "";
echo "<script>\n";
echo " fam =new Object();\n";
echo " op = '$operacion';\n";
echo " cja = '$tipocaja';\n";
echo " xlocal = '$IdLocal';\n";

foreach ($familias as $key=>$value){
  echo "fam['$key'] = '$value';\n";
}

echo "\n</script>";						

echo "<listbox id='listboxPartida' ondblclick='parent.changePartida(this,fam[this.value],op);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changePartida(this,fam[this.value]);parent.closepopup();return true;}' contextmenu='accionesListaPartidas' >\n";
//echo  genXulComboPartidas();				
echo "</listbox>";

echo "<popupset>
       <popup id='accionesListaPartidas'> 
        <menuitem  label='Modificar' oncommand='ModificarPartida()'/>
        <menuitem  label='Eliminar' oncommand='EliminarPartida()'/>
       </popup>
      </popupset>";

//echo "<button flex='1' label='"._("Eliminar")."' onkeypress='if (event.which == 13) Eliminar()' oncommand='Eliminar()'/>";
//echo "<button label='". _("Cerrar")."' oncommand='parent.closepopup()'/>";	
echo "</groupbox></vbox>";

EndXul();






?>
