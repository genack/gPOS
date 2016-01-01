

<listbox flex="1" id="buzon-mensajes">
	<listitem  id="guardianMensajes"  value="3" onclick="ToggleMensajes()">
	<button class="btn" id="btnEscribirMensajes" image="img/gpos_tpvnuevomensaje.png" label=" Escribir mensaje" oncommand="ToggleMensajes()"  /> 
	</listitem>	
</listbox>
	
	<groupbox><caption label="Mensaje"/>
	<hbox pack="center" style="padding-top:0.5em">
	  <image src="img/gpos_tpvmensaje.png"/>
	  <label crop="end" value="texto" id="tituloVisual" style="font-size:1.2em; "/>
	</hbox>

	<textbox multiline="true" readonly="true" value="texto aqui" flex="1" id="textoVisual" />
	<button class="btn" label="Ok" oncommand="mensajesModoRecepcion()" style="text-align:center; font-size:1.2em;" image="img/gpos_aceptar.png"/>	
</groupbox>	

<groupbox>
	<caption class="" crop="end" label="<?php echo _("Mensaje") ?>" id="tituloVisualMensaje" />
	<vbox flex="1">
	<caption id="localDestinoLabel" >Local </caption>
	<menulist  id="localDestino">
	<menupopup>
	<menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
		<?php echo genXulComboAlmacenes(false,"menuitem")  ?>
	</menupopup>
	</menulist>
        <vbox id="boxtituloNuevoMensaje">
	  <caption>Titulo </caption>
    	  <textbox id="tituloNuevoMensaje" onfocus="select()" class="compacta"
                 onkeypress="return soloAlfaNumericoTPV(event)"/>
        </vbox>
        <spacer style="height:0.2em"/>

        <vbox id="filaFechaEntregaProforma" collapsed="true">
          <caption label="Entrega" />
          <grid>
          <rows >
          <row>
            <description>Fecha: </description>
            <datepicker id="fechaEntregaProforma" type="popup"  />
          </row>
          <row>
            <description>Hora : </description>
            <timepicker id="horaEntregaProforma" type="popup"  />
          </row>
          <row>
            <description>Lugar: </description>
	    <textbox id="lugarEntregaProforma"  onfocus="select()"  
		     onkeypress="return soloAlfaNumericoTPV(event)" value=""/>
          </row>
          </rows>
          </grid>
        </vbox>
        <spacer style="height:0.2em"/>
   	<vbox>
          <grid>
          <rows>
          <row id="adelantoProformabox" collapsed="true">
            <caption label="Adelanto <?php echo $Moneda[1]['S']?>" style="font-size:1.2em"/>
   	    <textbox id="adelantoProforma" class="compacta" onfocus="select()" style="text-align:right;"
                 onkeypress="return soloNumerosTPV(event,this.value)" value="0.00"/>
          </row>

          <row id="vigenciaProformabox"  collapsed="true">
            <caption label="Vigencia Días "  style="font-size:1.2em"/>
	    <textbox id="vigenciaProforma" class="compacta" onfocus="select()" style="text-align:right;"
                 onkeypress="return soloNumerosTPV(event,this.value)" value=""/>
          </row>
          </rows>
          </grid>
          <vbox> 
	    <caption>Mensaje </caption>
	    <textbox id="cuerpoNuevoMensaje" multiline="true"  rows="2" onfocus="select()"
                 onkeypress="return soloAlfaNumericoTPV(event)" value="- "/>
          </vbox>
          <vbox id="vboxserieMProducto" collapsed="true">
             <description>Serie MixProducto: </description>
	    <textbox id="serieMProducto" onfocus="select()" value="" onchange="validaMProductoTicket()" collapsed="false" onkeypress="return soloAlfaNumericoTPV(event)" style="width:12.5em;"/>
          </vbox>
        </vbox>
	<hbox>
		<button class="btn" id="EnviarMensajePrivado" image="img/gpos_aceptar.png" label="<?php echo _(" Enviar") ?>" oncommand="EnviarMensajePrivado()"/>
		<button class="btn" id="CancelarMensajePrivado" image="img/gpos_cancelar.png" label="<?php echo _(" Cancelar") ?>" oncommand="ToggleMensajes()"/>
	</hbox>
	</vbox>
</groupbox>	

