<?php

include("tool.php");
$txtMoDet    = getModeloDetalle2txt();
$txtalias    = $txtMoDet[3];
$idfamilia   = $_GET['idfamilia'];
$id          = $_GET['id'];

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Elije ".$txtalias));


switch($modo){

case "nuevoproductoalias":
  $productoalias = CleanRealMysql(CleanText($_GET["productoalias"]));
  $sql = "SELECT IdProductoAlias
          FROM   ges_productos_alias
          WHERE  IdFamilia     ='".$idfamilia."'
          AND    ProductoAlias ='".$productoalias."'";
  $row = queryrow($sql);
  if ($row == ''){
    $sql = "SELECT Max(IdProductoAlias) as MaxAlias 
            FROM   ges_productos_alias";
    $row = queryrow($sql);
    if ($row){
      $IdIdioma = getSesionDato("IdLenguajeDefecto");
      $max = intval($row["MaxAlias"])+1; 	
      $productoalias = CleanRealMysql(CleanText($_GET["productoalias"]));
      $sql = "INSERT INTO ges_productos_alias 
            (IdProductoAlias, IdIdioma, ProductoAlias, IdFamilia) 
            VALUES ( '".$max."', '".$IdIdioma."', '".$productoalias."', '".$idfamilia."' )";
      query($sql,"Creando nuevo Alias");
    }
  } 
  else {
    $mesg="Existe el registro - ".$productoalias." ".$idfamilia." - ";
  }
  
case "alias": 

    echo "<groupbox> <caption label='Buscar $txtalias:'/>";
    echo "<vbox>";
    echo "<textbox  flex='1'   id='buscaalias' style='text-transform:uppercase;' onkeyup='javascript:this.value=this.value.toUpperCase(); BuscarAlias();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)'/>";
    echo "<button label='"._("Nuevo ")."' oncommand='UsarNuevo()'/>";
    echo "</vbox>";
    echo "</groupbox>";
    echo "<groupbox> <caption label=' ".$txtalias.":'/>";

    $familias = genArrayProductoAlias($idfamilia);
    $combo = "";
    echo "<script>\n";
    echo " var fam =new Object();\n";
    foreach ($familias as $key=>$value){
        echo "fam[$key] = '$value';\n";
        //$combo = "<option 			
    }

    if(isset($mesg)) {
	echo " alert('".$mesg."');";
    }

    if(isset($max)) {
	echo "opener.changeNewProductoAlias('".$max."','".$productoalias."','".$id."');window.close();";
    }

    echo "
        function UsarNuevo() {

            var productoalias, url;
            var idfamilia =".$idfamilia.";			
            var id        =".$id.";			
            var txtalias  ='".$txtalias."';
            var nuevoproductoalias = document.getElementById('buscaalias');			
            if (nuevoproductoalias){
                productoalias = nuevoproductoalias.value;
                productoalias = trim(productoalias);
                productoalias = limpiarcadena(productoalias);
            }
            if (!productoalias || productoalias == '')
                return;

            url = 'selproductoalias.php';
            url = url +'?';
            url = url + 'modo';
            url = url + '=nuevoproductoalias';
            url = url + '&amp;'+'productoalias=' + productoalias;
            url = url + '&amp;'+'txtalias=' + txtalias;
            url = url + '&amp;'+'idfamilia=' + idfamilia;
            url = url + '&amp;'+'id=' + id;
            document.location.href = url;			
        } 

        function soloAlfaNumerico(e){ 
                        key = e.keyCode || e.which;
                        tecla = String.fromCharCode(key).toLowerCase();
                        letras = ' abcdefghijklmnopqrstuvwxyz0123456789-%';
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


echo "\n</script>\n";						

echo "<script  type='application/x-javascript' src='js/alias.js' />";

echo "<listbox rows='5' flex='1' id='ProductoAlias'  onclick='opener.changeProductoAlias(this,fam[this.value],".$id.");window.close();return true;'>\n";		
echo  genXulComboProductoAlias($selected=false,$xul="listitem", $idfamilia,false);				
echo "</listbox>";		
echo "<button label='". _("Cerrar")."' oncommand='window.close()'/>";	
echo "</groupbox>";

break;		

default:
    break;	
}

//PageEnd();
EndXul();

?>
