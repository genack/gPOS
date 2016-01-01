<?php
include("../../tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul(_("Compras"));
?>
<hbox flex='1' class="box">
  <hbox flex='1'>
    <html:iframe  id="subweb" class="frameNormal" src="" flex="1"/>
  </hbox>
  <vbox class="frameExtra" style="width: 300px">
    <box id="accionesweb" class="frameNormal">
      <groupbox class="frameNormal" flex="1">
	<caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	<button id='bcapturar' flex="1" type="menu" label="<?php echo _("Captura CB"); ?>" oncommand="selrapidaCompra()">	        	       
	<menupopup id="idlocal2">
	  <?php
	    echo genXulComboAlmacenes(false,"menuitem","setLocal");
	  ?>
	</menupopup>
      </button>
     <button flex="1" label="<?php echo _("Alta rápida"); ?>" oncommand="altaRapida()"/>
     <hbox  equalsize="always">
       <button label="<?php echo _("Ver carrito") ?>" oncommand="verCarrito()" flex="1"/>     
       <button label="<?php echo _("Cancelar carrito") ?>" oncommand="cancelarCarrito()" flex="1"/>
     </hbox>
     <button id='bcapturar' flex="1" type="menu" label="<?php echo _("Finalizar compra"); ?>" oncommand="compraEfectuar()">	        	       
     <menupopup id="idlocal3">
       <?php
		echo genXulComboAlmacenes(false,"menuitem","setLocal");
       ?>
     </menupopup>
   </button>	 	      
 </groupbox>
</box>
<tabbox class="frameExtra" flex="1">
  <tabs class="AreaPagina">
    <tab label="<?php echo _("Comprar") ?>"/>
    <tab label="<?php echo _("Buscar") ?>"/>
    <tab label="<?php echo _("Carrito selección") ?>" oncommand=""/>
  </tabs>
  <tabpanels flex="1">
    <tabpanel id="comprapanel" flex='1'>		    	
      <groupbox flex="1">
	<caption label="<?php echo _("Comprar"); ?>" collapse="true"/>
	<grid> 
	  <columns> 
	    <column flex="1"/><column flex="1"/>
	  </columns>
	  <rows>
	    <row>
	      <caption label="<?php echo _("CB"); ?>"/>
	      <textbox id="CB" flex="1"/>
	    </row>				
	    <row>
	      <caption label="<?php echo _("Nombre"); ?>"/>
	      <textbox id="Nombre" flex="1"/>
	    </row>												
	  </rows>				  
	</grid>
	<button label='<?php echo _("Comprar") ?>' oncommand="comprar()"/>
	<spacer style="height: 16px"/>					
      </groupbox>
    </tabpanel>		  
    <tabpanel id="normaltab" flex='1'>		    	
      <groupbox flex="1">
	<caption label="<?php echo _("Buscar"); ?>" collapse="true"/>
	<grid> 
	  <columns> 
	    <column flex="1"/><column flex="1"/>
	  </columns>
	  <rows>
	    <row>					
	      <caption label="<?php echo _("Local"); ?>"/>    
	      <menulist  id="idlocal">						
		<menupopup>
		  <menuitem label="Elije local" style="font-weight: bold"/>
		  <?php echo genXulComboAlmacenes(false,"menuitem") ?>
		</menupopup>
	      </menulist>						
	    </row>
	    <row>
	      <caption label="<?php echo _("CB"); ?>"/>
	      <textbox id="CB" flex="1"/>
	    </row>				
	    <row>
	      <caption label="<?php echo _("Ref"); ?>"/>
	      <textbox id="Referencia" flex="1"/>
	    </row>						
	    <row>
	      <caption label="<?php echo _("Nombre"); ?>"/>
	      <textbox id="Nombre" flex="1"/>
	    </row>						
	    
	  </rows>
	  
	</grid>
	<checkbox id="TC" label="<?php echo _("Modelo y Detalle") ?>"/>
	<button label='<?php echo _("Buscar") ?>' oncommand="buscar()"/>
      </groupbox>
    </tabpanel>
    <tabpanel id="avanzadatab" flex='1'>
      <groupbox flex="1">
	<caption label="<?php echo _(" "); ?>" collapse="true"/>

      </groupbox>
    </tabpanel>
  </tabpanels>
</tabbox>
</vbox>
</hbox>

  
<script><![CDATA[

var subweb = document.getElementById("subweb");

var local = 0;
var localtraslado=0;

function setLocal(valor) { local = valor;}

function cancelarCarrito() {
	var url = "modcompras.php?modo=noseleccion";
	subweb.setAttribute("src",url);
}

function compraEfectuar() {
	var url = "modcompras.php?modo=continuarCompra&IdLocal="+local;
	subweb.setAttribute("src",url);
}


function verCarrito() {
	var url = "vercarrito.php?modo=check";
	subweb.setAttribute("src",url);
}

function selrapidaCompra() {
    if (local==0)
    	return;

	var url = "../almacen/selalmacen.php?modo=empieza&IdLocal=" +local;
	subweb.setAttribute("src",url);
	local = 0;
}

function altaRapida() {
	var url = "modproductos.php?modo=alta";
	subweb.setAttribute("src",url);
}


function buscar()
{  
  
  var tc = document.getElementById("TC").checked;    
  if (tc)  tc="on"; else tc="";
   
  var extra  = "&CodigoBarras=" + document.getElementById("CB").value;  
  extra = extra +  "&IdLocal=" + document.getElementById("idlocal").value;
  extra = extra +  "&Referencia=" + document.getElementById("Referencia").value;
  extra = extra +  "&Nombre=" + document.getElementById("Nombre").value;   
  extra = extra +  "&verCompletas=" + tc;
  
  /*url = "modcompras.php?modo=buscarproductos" + extra; */
  url = "vercarrito.php?modo=check" + extra; 
  subweb.setAttribute("src", url);
}


]]></script>

<?php

EndXul();


?>
