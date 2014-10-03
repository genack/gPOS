

<listbox flex="1" id="buzon-mensajes">
	<listitem  id="guardianMensajes"  value="3" onclick="ToggleMensajes()">
	<button id="btnEscribirMensajes" image="img/gpos_tpvnuevomensaje.png" label=" Escribir mensaje" oncommand="ToggleMensajes()"  /> 
	</listitem>	
</listbox>
	
	<groupbox><caption label="Contenido:"/>
	<toolbarbutton label="Ok" oncommand="mensajesModoRecepcion()"/>	
	<label crop="end" value="texto" id="tituloVisual" style="max-width: 200px;font-weight: bold;color: blue"/>
	<textbox multiline="true" readonly="true" value="texto aqui" flex="1" id="textoVisual" 
		style="color: blue; background-color: ThreeDFace !important"/>
</groupbox>	

<groupbox>
	<caption class="" crop="end" label="<?php echo _("Mensaje") ?>" id="tituloVisualMensaje"/>
	<vbox flex="1">
	<menulist  id="localDestino">						
	<menupopup>
	<menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
		<?php echo genXulComboAlmacenes(false,"menuitem")  ?>
	</menupopup>
	</menulist>
        <vbox>
    	  <textbox id="tituloNuevoMensaje" onfocus="select()" collapsed="true"
                 onkeypress="return soloAlfaNumericoTPV(event)"/>
        </vbox>
        <spacer style="height:0.5em"/>

        <vbox id="filaFechaEntregaProforma" collapsed="true">
          <caption label="Entrega:"/>
          <grid>
          <rows>
          <row>
            <description>Fecha: </description>
            <datepicker id="fechaEntregaProforma" type="popup" />
          </row>
          <row>
            <description>Hora : </description>
            <timepicker id="horaEntregaProforma" type="popup" />
          </row>
          <row>
            <description>Lugar: </description>
	    <textbox id="lugarEntregaProforma" onfocus="select()" style="width:12.5em;"
                 onkeypress="return soloAlfaNumericoTPV(event)" value=""/>
          </row>
          </rows>
          </grid>
        </vbox>
        <spacer style="height:0.5em"/>
   	<vbox>
          <grid>
          <rows>
          <row id="adelantoProformabox" collapsed="true">
            <caption label="Adelanto:"/>
   	    <textbox id="adelantoProforma" onfocus="select()" style="width:10.5em;"
                 onkeypress="return soloNumerosTPV(event,this.value)" value="0"/>
          </row>

          <row id="vigenciaProformabox"  collapsed="true">
            <caption label="Vigencia:"/>
	    <textbox id="vigenciaProforma" onfocus="select()" style="width:10.5em;"
                 onkeypress="return soloNumerosTPV(event,this.value)" value=""/>
          </row>
          </rows>
          </grid>
          <vbox> 
            <caption label="Mensaje:"/>
	    <textbox id="cuerpoNuevoMensaje" multiline="true"  rows="2" onfocus="select()"
                 onkeypress="return soloAlfaNumericoTPV(event)" style="width:12.5em;" value="- "/>
          </vbox>
          <vbox id="vboxserieMProducto" collapsed="true">
            <caption label="Serie MProducto:"/>
	    <textbox id="serieMProducto" onfocus="select()" value="" onchange="validaMProductoTicket()" collapsed="false" onkeypress="return soloAlfaNumericoTPV(event)" style="width:12.5em;"/>
          </vbox>
        </vbox>
	<hbox>
		<button id="EnviarMensajePrivado" image="<?php echo $_BasePath ?>/img/gpos_aceptar.png" label="<?php echo _(" Enviar") ?>" oncommand="EnviarMensajePrivado()"/>
		<button id="CancelarMensajePrivado" image="<?php echo $_BasePath ?>/img/gpos_cancelar.png" label="<?php echo _(" Cancelar") ?>" oncommand="ToggleMensajes()"/>
	</hbox>
	</vbox>
</groupbox>	

