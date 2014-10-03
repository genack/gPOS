<?php
include("../tool.php");

$modo  = CleanCadena($_GET['modo']);
$aviso = CleanCadena($_GET['aviso']);
$url   = (isset($_GET['url']))? CleanCadena($_GET['url']):'';

StartXul(_("Progress")); 
?>
<script>//<![CDATA[
<?php 
   switch ($modo) {
   case "cAltaRapida" :
     echo 'setTimeout("reloadAltaRapida()",300);';//MENSAJES
     break;
   case "aCarritoCompras" :
   case "aAltaRapida" :
     echo 'setTimeout("loadPresupuesto()",50);';//MENSAJES
     break;

   case "hWebForm" :
     echo 'setTimeout("hWebForm()",350);';//MENSAJES

     echo 'if( parent.document.getElementById("c_Nombre") )
       parent.document.getElementById("c_Nombre").focus()';

     break;

   case "lWebFormCartBuy" :
     echo 'setTimeout("lWebFormCartBuy()",300);';//MENSAJES
     break;

   case "lWebFormCartMod" :
     echo 'setTimeout("lWebFormCartMod()",300);';//MENSAJES
     break;

   case "lWebFormCartSerieMod" :
     echo 'setTimeout("lWebFormCartSerieMod()",300);';//MENSAJES
     break;

   case "lWebFormAlmacen" :
     echo 'setTimeout("lWebFormAlmacen()",300);';//MENSAJES
     break;

   case "lWebFormAltaRapida" :
     echo 'setTimeout("lWebFormAltaRapida()",300);';//MENSAJES
     break;

   }
?>

function hWebForm(){
  parent.xwebCollapsed(false,true);
}

function lWebFormAltaRapida(){

  var main = parent.getWebForm();
  var url  = '<?php echo $url;?>';
  var lurl = url.replace(/%/g,"&");

  main.setAttribute("src",lurl);  
  parent.xwebCollapsed(true);

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

function loadPresupuesto(){
     var subweb = parent.document.getElementById("web");
     if(subweb)
 	 subweb.setAttribute("src","about:blank");
     setTimeout("postloadPresupuesto()",50);
}

function postloadPresupuesto(){
     var subweb = parent.document.getElementById("web");
     if(subweb)
         subweb.setAttribute("src","vercarrito.php?modo=check");
     setTimeout("postViewPresupuesto()",400);
}

function postViewPresupuesto(){ parent.xwebCollapsed(false);}

function reloadAltaRapida(){
    var main = parent.getWebForm();
    main.setAttribute('src','xulaltarapida.php?modo=alta');
}
//]]></script>

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

<?php

EndXul();

?>
