<vbox id="editandoSeries"  align="center" pack="center">
<spacer flex="1"/>

<vbox  align="center"  pack="center" >
  <caption id="nsTitulo" label="" class="h1"/>
  <spacer style="height:5px"/>
  <caption id="nsProducto" label="Producto" class="box"/>
</vbox>
  <groupbox>
    <hbox>
      <grid>
	<rows>
	  <row>
	    <caption label="Acciones"/>
	    <hbox>
		<radiogroup id="radio_group" oncommand="limpiar_caja()" > 
		  <hbox>
		    <radio label="Agregar"  disabled="true"/>
		    <radio label="Quitar" disabled="true" />
		    <radio label="Buscar" selected="true" />
		  </hbox>
		</radiogroup>
	      </hbox>
	  </row>
          <row >
	    <caption label="Select NS"/>
	    <textbox id="ckserie" onkeypress="if (event.which == 13) selcKBoxSerie()"/>
	    <textbox id="selCB" collapsed="true"/>
	  </row>
	</rows>
      </grid>
    </hbox>
    <listbox id="listaseries_tpv" >
      <listhead>
	<listheader label="#" style="font-style:italic;" />
	<listheader label="NS"/>
      </listhead>
      <listcols>
	<listcol/>
	<listcol flex="1"/>
      </listcols>
      <!-- listitem type="checkbox" -->
    </listbox>
    <hbox   flex="1">
      <grid>
	<rows >
	  <row>
	    <description value="NS Stock"/>
	    <caption id="totalNS" label="0" class="xtotal"/>
	  </row>
	  <row>
	    <description value="NS Selecciondos"/>
	    <caption id="totalSelNS" label="0" class="xtotal"/>
	  </row>
	</rows>
      </grid>
    </hbox>
    <hbox flex="1" pack="center" style="width:19.5em;">
      <button id="btnreturndetventa" class="media btn"  flex="1" image="img/gpos_volver.png"
	      label=" Volver TPV" oncommand="VerTPV()" style="font-size: 15px;font-weight: bold;"/>
    </hbox>
  </groupbox>	
<spacer flex="1"/>
</vbox>
