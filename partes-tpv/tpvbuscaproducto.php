<panel id="panelElijeComprobanteTPV" style="border:1px solid #aaa">
  <hbox align="start">
    <vbox >
      <toolbarbutton  image="img/gpos_imprimir.png" label="Imprimir" style="font-size: 1.6em;"/>
      <spacer style="height: 0.9em"/>
      <button class="btn" label="Ticket" style="font-size: 1.8em;"  oncommand="keyAbrirPeticion(0)"/>
      <spacer style="height: 0.5em"/>
      <button class="btn" label="Boleta"  style="font-size: 1.8em;" oncommand="keyAbrirPeticion(1)"/>
      <spacer style="height: 0.5em"/>
      <button class="btn" label="Factura" style="font-size: 1.8em;" oncommand="keyAbrirPeticion(2)"/>
      <spacer style="height: 0.5em"/>
      <button class="btn" id="elijeAlbaranTPV"  label="Albaran" style="font-size: 1.8em;"
	      oncommand="keyAbrirPeticion(4)" disabled="true"/>
    </vbox>
  </hbox>
</panel>

<hbox align="center">	
  <spacer style="width: 5px"/>
  <image src="img/gpos_barcode.png" height="16" width="16"/>
  <caption  label="CB" class="bbox"/>
  <textbox   id="CB"   size="12" onfocus="select()" class="compacta"   flex="1" 
	     onkeypress="return soloNumerosTPV(event,this.value)" 
	     onkeyup="if (event.which == 13) agnadirPorCodigoBarras()" />

  <spacer style="width: 32px"/>

  <image src="img/gpos_tpvreferencia.png" />
  <caption label="CR"  class="bbox" />
  <textbox  id="REF"  size="8" onfocus="select()" class="compacta" flex="1" 
	    onkeyup="agnadirPorReferencia()" />
  <spacer style="width: 30px"/>
  <image src="img/gpos_productos.png" height="16" width="16"/>
  <caption label="Producto"  class="bbox" />
  <textbox id="NOM"  size="20" onfocus="select()" class="compacta" flex="1" placeholder=" nombre | marca ó modelo..."  onkeypress=" if (event.which == 13) focuslistaProductos(); else agnadirPorNombre();" />
  <spacer style="width: 10px"/>
  <toolbarbutton image="img/gpos_tpv_lista_compacta.png" id="gridListarTPV" oncommand="gridListarProductosTPV()"/>
  <spacer style="width: 10px"/>
  <toolbarbutton class="sync_pause" id="syncTPV" oncommand="pushSyncTPV()"/>
  <toolbarbutton image="img/gpos_tpvsynch_off.png" id="syncTPVOff"  collapsed="true" oncommand="syncTPV()"/>
  <spacer style="width: 10px"/>
  <toolbarbutton image="img/gpos_tpvnetwork_on.png" id="bolaMundo" oncommand="syncTPV()" />
  <toolbarbutton image="img/gpos_tpvnetwork_off.png" id="bolaMundoOff" oncommand="syncTPV()" collapsed="true"/>
  <spacer style="width: 5px"/>
  <checkbox  id="buscar-servidor"  label="Stock" checked="true" onclick="esOfflineBusquedas()" name="buscar-en-internet"/>
</hbox>

