<?php
include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

$_motivoFallo = "";


switch($modo)
  {
  case "usuario":
    $login = CleanLogin($_GET["usuario"]);
    $pass =  CleanPass($_GET["pass"]);

    $user = true;
    if ($login and $pass){
      $id = identificacionUsuarioValidoMd5($login,$pass);
      if ($id){		
	RegistrarUsuarioLogueado($id);
	session_write_close();
	header("Location: xulmenu.php");
	exit();	
      } else {
	$fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";	
      }
    }		
    break;	
  case "local":
  default:
    $login = CleanLogin($_GET["local"]);
  $pass =  CleanPass($_GET["pass"]);
  
  $user = false;
  if ($login and $pass){
    $id = identificacionLocalValidaMd5($login,$pass);
    if ($id){		
      RegistrarTiendaLogueada($id);
      session_write_close();
      header("Location: xulentrar.php?modo=usuario");
      exit();	
    } else {
      $fail = "Nombre ('$login') o password ('$pass') incorrectas: $_motivoFallo";	
    }
  }
  break;
}

StartXul(_("Productos"));

?>
<hbox flex='1'>

  <hbox flex='1'>
    <html:iframe  id="subweb" class="frameNormal" src="" flex="1"/>
  </hbox>
  
  <vbox class="frameExtra" style="width: 300px">
    <box id="accionesweb" class="frameNormal">
      <groupbox class="frameNormal" flex="1">
	<caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	<button label="<?php echo _("Nuevo producto"); ?>" oncommand="ModoAlta();"/>
      </groupbox>
    </box>
    <tabbox class="frameExtra" flex="1">
      <tabs class="AreaPagina">
	<tab label="<?php echo _("Normal") ?>"/>
	<tab label="<?php echo _("Búsqueda avanzada") ?>" oncommand="loadAvanzado()"/>
      </tabs>
      <tabpanels flex="1">
	<tabpanel id="normaltab" flex='1'>
	  <groupbox flex="1">
	    <caption label="<?php echo _("CB"); ?>"/>
	    <textbox id="CB"/>
	    <checkbox id="TC" label="<?php echo _("Modelo y Detalle") ?>"/>
	    <button label='<?php echo _("Buscar") ?>' oncommand="buscar()"/>
	  </groupbox>
	</tabpanel>
	<tabpanel id="avanzadatab" flex='1'>
	  <groupbox flex="1">
	    <caption label="<?php echo _("Búsqueda avanzada"); ?>"/>
	    <iframe id="subframe" src="" flex='1'/>
	  </groupbox>
	</tabpanel>
      </tabpanels>
    </tabbox>
  </vbox>
</hbox>


<iframe  id="novisual-subweb" collapsed="true" src="modproductos.php?modo=nomostrar"/>
<script><![CDATA[

var avanzadoCargado = 0;
var visiblebusca = 0;

var subweb = document.getElementById("subweb");

function ModoAlta() {   
 subweb.setAttribute("src","modproductos.php?modo=alta"); 
}



function buscarextra(idprov,idcolor,idmarca,idtalla,idfam,tc) {
        
   if (tc)  tc="on"; else tc="";
   
  var extra = "&IdProveedor=" + idprov;
  extra = extra + "&IdColor=" + idcolor;
  extra = extra + "&IdMarca=" + idmarca;
  extra = extra + "&IdTalla=" + idtalla;
  extra = extra + "&IdFamilia=" + idfam;
       
  var url = "modproductos.php?modo=mostrar" + extra + "&verCompletas=" + tc;
  subweb.setAttribute("src", url);
}

function loadAvanzado(){
 var subframe;
 
 if (avanzadoCargado)
 	return;
 
 subframe = document.getElementById("subframe");
 subframe.setAttribute("src","xulavanzado.php?modo=productos");
 subframe.setAttribute("opener",document.getElementById("subweb"));
  
 avanzadoCargado = 1;
}


function buscar(){  
  var codigo = document.getElementById("CB").value;
  var tc = document.getElementById("TC").checked;

  if (tc)  tc="on"; else tc="";
   
  url = "modproductos.php?modo=buscaporcb&CodigoBarras=" + codigo + "&verCompletas=" + tc;
  subweb.setAttribute("src", url);
}

]]></script>

<?php

EndXul();



?>
