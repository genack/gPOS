<?php
include("../../tool.php");

$modo  = CleanCadena($_GET['modo']);
$aviso = CleanCadena($_GET['aviso']);
$url   = (isset($_GET['url']))? CleanCadena($_GET['url']):'';

StartXul(_("Progress")); 
?>
<script>//<![CDATA[
<?php 
   switch ($modo) {
   case "cAltaRapida" :
     echo 'setTimeout("reloadAltaRapida()",400);';//MENSAJES
     break;
   case "cAltaRapidaClon" :
     echo 'setTimeout("reloadAltaRapidaClon()",400);';//MENSAJES
     break;
   case "aCompras" :
     echo 'setTimeout("loadCompras()",10);';//MENSAJES
      break;

   case "aCarritoCompras" :
   case "aAltaRapida" :
     echo 'setTimeout("loadPresupuesto()",400);';//MENSAJES
     break;

   case "hWebForm" :
     echo 'setTimeout("hWebForm()",450);';//MENSAJES

     echo 'if( parent.document.getElementById("c_Nombre") )
       parent.document.getElementById("c_Nombre").focus()';

     break;

   case "hWebBox" :
     echo 'setTimeout("hWebBox()",450);';//MENSAJES

     echo 'if( parent.document.getElementById("c_Nombre") )
       parent.document.getElementById("c_Nombre").focus()';

     break;

   case "lWebFormCartBuy" :
     echo 'setTimeout("lWebFormCartBuy()",400);';//MENSAJES
     break;

   case "lWebFormCartMod" :
     echo 'setTimeout("lWebFormCartMod()",400);';//MENSAJES
     break;

   case "lWebFormCartSerieMod" :
     echo 'setTimeout("lWebFormCartSerieMod()",400);';//MENSAJES
     break;

   case "lWebFormAlmacen" :
     echo 'setTimeout("lWebFormAlmacen()",400);';//MENSAJES
     break;

   }
?>

function hWebForm(){
  parent.xwebCollapsed(false,true);
}

function hWebBox(){
  parent.setWebBoxCollapsed(false);
}

function lWebFormCartMod(){
  var main = parent.getWebForm();
  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");

  main.setAttribute("src",lurl);  
  parent.xwebCollapsed(true);
}

function lWebFormCartSerieMod(){
  var main = parent.getWebForm();
  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");

  main.setAttribute("src",lurl);  
  parent.xwebCollapsed(true);
}

function lWebFormAlmacen(){

  var main = parent.getWebForm();
  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");

  main.setAttribute("src",lurl);  
  parent.xwebCollapsed(true);

}


function lWebFormCartBuy(){

  var main = parent.getWebForm();
  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");

  main.setAttribute("src",lurl);  
  parent.xwebCollapsed(true);

}

function loadCompras(){
     //parent.MostrarDeck();
     //parent.Compras_buscar();
     parent.Compras_verCarrito()
     hWebForm();
}

function loadPresupuesto(){
     //parent.MostrarDeck();
     var subweb = parent.document.getElementById("web");
     if(subweb)
 	 subweb.setAttribute("src","about:blank");
     setTimeout("postloadPresupuesto()",50);
}

function postloadPresupuesto(){
     var subweb = parent.document.getElementById("web");
     if(subweb)
         subweb.setAttribute("src","vercarrito.php?modo=check");

     setTimeout("postViewPresupuesto()",280);     
}

function postViewPresupuesto(){ parent.xwebCollapsed(false);
                                parent.solapa('modulos/compras/xulcompras.php?modo=entra','Compras > Presupuestos','compras');
}


function reloadAltaRapida(){
    var main = parent.getWebForm();
    main.setAttribute('src','modulos/altarapida/xulaltarapida.php?modo=alta');
}

function reloadAltaRapidaClon(){

  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");
  var main = parent.getWebForm();
  main.setAttribute('src',lurl);
}

//]]></script>
<html:style>
  #boxprogress{
            background-image: url("../../img/gpos_marcagua.png");
            background-position: center center;
            background-repeat: no-repeat;
	    }
</html:style>

<vbox flex="1" id="boxprogress" class="box">
<spacer style="height:6px"/>
<hbox pack="center">
  <caption style="font-size: 14px;font-weight: bold;">
    <?php echo $aviso; ?>
  </caption>
</hbox>
<spacer style="height:6px"/>

<groupbox>
<!-- alta de prod -->
<progressmeter mode="undetermined" />
<!-- alta de prod -->
</groupbox>
</vbox>
<?php

EndXul();

?>
