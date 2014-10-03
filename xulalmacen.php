<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

StartXul(_("Almacén"));

?>
<hbox flex='1'>

	<hbox flex='1'>
		 <html:iframe  id="subweb" class="frameNormal" src="" flex="1"/>
	</hbox>
	
	<vbox class="frameExtra" style="width: 300px">
		<box id="accionesweb" class="frameNormal">
		<groupbox class="frameNormal" flex="1">
	    <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	     <button id='bcapturar' flex="1" type="menu" label="<?php echo _("Captura CB"); ?>" oncommand="selrapidaalmacen()">	        	       
       <menupopup id="idlocal2">
       <?php
	echo genXulComboAlmacenes(false,"menuitem","setLocal");
	?>
       </menupopup>
       
     </button>
	    </groupbox>
	    </box>
		<tabbox class="frameExtra" flex="1">
		  <tabs class="AreaPagina">
		    <tab label="<?php echo _("Normal") ?>"/>
		    <tab label="<?php echo _("Carrito selección") ?>" oncommand=""/>
		  </tabs>
		  <tabpanels flex="1">
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
						<menulist  id="idlocal" oncommand="">						
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
					<caption label="<?php echo _("Carrito selección"); ?>" collapse="true"/>
					<button label="Ver carrito"/>
					<button label="Cancelar carrito"/>
					<spacer style="height: 8px;"/>
								
					<button  type="menu" label="<?php echo _("Trasladar mercancía"); ?>" oncommand="Traslado()">	        	       
    	   			<menupopup>
	       			<?php
					echo genXulComboAlmacenes(false,"menuitem","setLocalTraslado");
					?>
       				</menupopup>
       				</button>
					<grid>
					<columns><column flex="1"/><column flex="1"/></columns>
					<rows>
					<row>
					<button label="En oferta" flex="1"/>
					<button label="Sin oferta" style="text-decoration: overstrike;" flex="1"/>
					</row>
					<row>
					<button label="En venta" flex="1"/>
					<button label="Reservado" style="text-decoration: overstrike;" flex="1"/>
					</row>
					</rows>
					</grid>
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

function setLocalTraslado(valor) { localtraslado = valor; }

function Traslado() {
    if (localtraslado==0)
    	return;

    if (!confirm(po_quieretrasladarmercancia) ){
    	return;
    }	
    	
	var url = "modalmacenes.php?modo=albaran&IdLocal=" +localtraslado;
	subweb.setAttribute("src",url);
	local = 0;
}

function selrapidaalmacen() {
    if (local==0)
    	return;

	var url = "selalmacen.php?modo=empieza&IdLocal=" +local;
	subweb.setAttribute("src",url);
	local = 0;
}


function buscar(){  
  
  var tc = document.getElementById("TC").checked;    
  if (tc)  tc="on"; else tc="";
   
  var extra  = "&CodigoBarras=" + document.getElementById("CB").value;  
  extra = extra +  "&IdLocal=" + document.getElementById("idlocal").value;
  extra = extra +  "&Referencia=" + document.getElementById("Referencia").value;
  extra = extra +  "&Nombre=" + document.getElementById("Nombre").value;   
  extra = extra +  "&verCompletas=" + tc;
  
  var url = "modalmacenes.php?modo=buscarproductos" + extra; 
  subweb.setAttribute("src", url);
}


]]></script>

<?php

/*
<center>
<table><tr><td>
<form action="%ACTION%?modo=buscarproductos" method=post>
<table class=forma>
<tr class="f"><td>%Referencia%</td><td>Local</td><td></td><td>%CB%</td></tr>
<tr class="f">
 <td><input type=text name=Referencia value=""></td>
 <td><select name=IdLocal>%comboAlmacenes%</select></td>
 <td></td>
 <td><input id="entraCodigoBarras" type=text name=CodigoBarras value=""></td> 
 <td><input type="checkbox" name=verCompletas>%tTallaycolores%</td>
</tr>
<tr><td colspan="5"><input name=busqueda  class="btn item" type=submit value="%bEnviar%"/></td></tr>
</table>
</form>

</td><td>

<haylocal>
<table class="forma">
<tr><td><input class="btn item" onclick="auxSeleccionRapidaAlmacen(%vIdLocal%);" type="button" value='Seleccion CB'></td></tr></table>
</haylocal>


</td></tr></table>

<script>
*/

EndXul();


?>
