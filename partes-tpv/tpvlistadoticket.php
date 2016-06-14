
  <spacer style="height: 8px"/>
  <splitter collapse="none"  resizeafter="farthest" resizebefore="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>
  <vbox flex="3" >
    <hbox align="center" >
      <menu id="onlistTicket" class="menuhead" collapsed="false" label="TICKET ACTUAL" >
	<menupopup id="combolistTicket">
	  <menuitem id="t_actual" type="checkbox" label="TICKET ACTUAL"   checked="true"  
		    oncommand="selTipoPresupuesto(0)" />
	  <menuseparator />
	  <menuitem id="t_preventa" type="checkbox" label="TICKET PREVENTA"  
		    oncommand="selTipoPresupuesto(1)" />
	  <menuitem id="t_proforma" type="checkbox" label="TICKET PROFORMA" 
		    oncommand="selTipoPresupuesto(2)" />
	  <menuitem id="t_proformaonline" type="checkbox" label="TICKET ONLINE"  
		    oncommand="selTipoPresupuesto(3)" />
	  <menuseparator />
	  <menuitem id="t_mproducto" type="checkbox" label="TICKET MIXPRODUCTO"  
		    oncommand="selTipoPresupuesto(4)" />
	</menupopup>
      </menu>
                                                                                                             
      <textbox id="buscapedido" collapsed="true" size="9" value="Nro" onfocus="if(this.value=='Nro')this.value='';select();" onblur="if(this.value=='')this.value='Nro';" class="nro"  onkeypress=" if (event.which == 13) buscarNroTicket(); return soloNumerosEnterosTPV(event,this.value);" />

      <menulist label="Elije ticket...."  flex="1" id="SelPreventa" collapsed="true"  class="listado">
      <menupopup id="itemsPreventa">
	<menuitem id="0" style="width:14em" label="Elije ticket...." oncommand="selTipoPresupuesto(1)" />      
      </menupopup>
      </menulist>

      <menulist label="Elije ticket...." flex="1" id="SelProforma" collapsed="true"  class="listado">
      <menupopup id="itemsProforma">
	<menuitem id="0" style="width:14em" label="Elije ticket...."/>      
      </menupopup>
      </menulist>

      <menulist label="Elije ticket...." flex="1" id="SelProformaOnline" collapsed="true"  class="listado">
      <menupopup id="itemsProformaOnline">
	<menuitem id="0" style="width:14em" label="Elije ticket...."/>      
      </menupopup>
      </menulist>

      <menulist label="Elije ticket...."  flex="1" id="SelMProducto" collapsed="true"  class="listado">
      <menupopup id="itemsMProducto">
	<menuitem id="0" style="width:14em" label="Elije ticket...."/>      
      </menupopup>
      </menulist>

      <menulist label="Elije MixProducto...." flex="1" id="SelBaseMProducto" collapsed="true"  class="listado">
      <menupopup id="itemsBaseMProducto"  class="listado">
	<menuitem id="0" style="width:20em" label="Elije MixProducto...." oncommand="cargarIdMProducto(0)"/>      
      </menupopup>
      </menulist>

    <checkbox  id="prevt-stock"  label="Stock" checked="true" onclick="esOffStockPreventa()" collapsed="true"/>

      <spacer flex="1"/>
      <radiogroup orient="horizontal" id="rgModosTicket" oncommand="NuevoModo()"  class="listado">
	<radio id="rVenta" label="Contado" selected="true" value="venta"/>
	<radio id="rCesion" label="Crédito" value="cesion"/>
	<radio id="rPedido" label="Proforma" selected="true" value="pedidos"/>
	<radio id="rMProducto" label="MixProductos" value="mproducto"/>
	<radio id="rInterno" label="Servicios" value="interno" collapsed="true"/>
      </radiogroup>    	
    </hbox>
    <listbox id="listadoTicket" rows="4" flex="1" contextmenu="accionesTicket"  class="listado" oncontextmenu="menuContextualPreVentaTPV(true)" onkeypress="if (event.which == 13) setTimeout('ModificaTicketUnidades(-1)',50)" >
      <listcols flex="1">
	<listcol id="colListaTicketViewCR" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol id="colListaTicketViewUND" />
	<splitter class="tree-splitter" />
	<listcol id="colListaTicketViewDCTO" />
	<splitter class="tree-splitter" />		
	<listcol  collapsed="<?php echo $esOcultoImpuesto ?>"/>
	<splitter class="tree-splitter" />				
	<listcol id="colListaTicketViewPV"/>
	<listcol collapsed="true" />
	<listcol collapsed="true" />
	<listcol collapsed="true" />
	<listcol id="colListaTicketViewImporte"/>
	<listcol style="width:1em"/>
      </listcols>
      <listhead>
	<listheader id="headListaTicketViewCR" collapsed="true" label="CR" />
	<listheader label="Producto"  />
	<listheader label="" />
	<listheader label="Und" style="text-align:center"/>
	<listheader label="Dcto"/>	
	<listheader label="Impuesto" collapsed="<?php echo $esOcultoImpuesto ?>"/>

	<listheader id="pvpUnidadTicket" label="<?php echo $pvpUnidad ?>" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="Importe" />
	<listheader label="" />
      </listhead>
	  
    </listbox>
    </vbox>	
