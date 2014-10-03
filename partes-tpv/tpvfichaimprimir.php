<groupbox flex="1">
<vbox flex="1" align="center" pack="center" style="background-color: #eee">
<caption class="media"  label="Peticion Ticket" id="etiquetaTicket" style="background-color: #eee"/>

<groupbox style="background-color: -moz-dialog">
<grid>
  <columns><column/><column/></columns>
	<rows>
	    <row>
	     <caption class="grande" label=" "/>
	    </row>
		<row>		
		<caption class="grande" label="<?php echo _("TOTAL") ?>"/>
		<caption id="peticionTotal" class="grande"  label="0,00"/>
		</row>
		<row id="Fila-peticionEntrega">
		<caption class="grande" label="<?php echo _("ENTREGA") ?>" style="padding-top:0.6em"/>
		<textbox id="peticionEntrega" class="grande"  value="0,00" onfocus="select()"
                         onkeyup="ActualizaPeticion()" 
                         onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value) "/>
		</row>
		<row>
		<caption class="grande" label="<?php echo _("CAMBIO") ?>"/>
		<caption id="peticionPendiente" class="grande" label="0,00"/>
		<textbox id="peticionCambioEntregado" collapsed="true" value="0"/>
		</row>	
		<spacer style="height: 8px"/>
		<row id="Pagos_1" collapsed="true">		
		<caption class="media" label="<?php echo _("EFECTIVO") ?>"/>
		<textbox id="peticionEfectivo" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_2" collapsed="true">
		<caption class="media" label="<?php echo _("BONO") ?>"/>
		<textbox id="peticionBono" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_3" collapsed="true">
		<caption class="media" label="<?php echo _("TARJETA") ?>"/>
		<textbox id="peticionTarjeta" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion(); return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_4" collapsed="true">
		<caption class="media" label="<?php echo _("TRANSFERENCIA") ?>"/>
		<textbox id="peticionTransferencia" class="media"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion(); return soloNumerosTPV(event,this.value)"/>
		</row>			
		
		<row id="Pago_Modo">
		<caption class="grande" label="<?php echo _("PAGO") ?>" style="padding-top:0.6em"/>
		<menulist class="grande" id="modoDePagoTicket">
		<menupopup>
		<?php
	foreach( $modosDePago as $value=>$label ){
		echo "<menuitem value='$value' label='$label'/>\n";
	}
		?>
		</menupopup>
		</menulist>
		</row>
		<spacer style="height: 8px"/>

		<row id="Pagos_0">		
		<box/>
		<button image="img/gpos_tpvmultipagos.png" flex="1" class="media" 
			label=" Multipagos" oncommand="ModoMultipago()"/>
		</row>			

		<?php 
		//NOTA: condicionado a ser un administrador de facturas se muestran los controles extra 		
		if ($_SESSION["EsAdministradorFacturas"]){										
		?>		
		<row id="Admintic_0">		
		<box/>
		<button image="img/gpos_tpvmultipagos.png" flex="1" class="media"
			label=" Personalizar" oncommand="ModoPersonalizado()" collapsed="true" />
		</row>	
		<row id="Admintic_1"  collapsed="true">
		<caption class="media" label="<?php echo _("Serie ticket") ?>"/>
		<textbox id="ajusteSerieTicket" class="media"  
 			value="<?php
				echo "B" . CleanID(getSesionDato("IdTienda"));
				?>"/>
		</row>		
		<row  id="Admintic_2"  collapsed="true">
		<caption class="media" label="<?php echo _("Nº Ticket") ?>"/>
		<textbox id="ajusteNumeroTicket" class="media"  
			value="<?php
				echo CleanID($numSerieTicketLocalActual);
				?>"/>				
		</row>		
		<row  id="Admintic_3"  collapsed="true">
		<caption class="media" label="<?php echo _("Fecha Ticket") ?>"/>
		<textbox id="ajusteFechaTicket" class="media"  
			value="<?php
				$cad = "%A %d del %B, %Y";
				setlocale(LC_ALL,"es_ES");			
				echo strftime($cad);
				?>"/>				
		</row>	
		<?php
		}
		?>		
		<row>
		  <groupbox>
		    <caption label="" />
		    <radiogroup id="comprobante" collapsed="false">
		      <radio id="radioproforma" class="media" label="Proforma"
			     oncommand="tipocomprobante(5);"/>
		      <radio id="radiofactura" class="media" label="Factura"
			     oncommand="tipocomprobante(2);" accesskey="F"/>
		      <radio id="radioboleta" class="media" label="Boleta" selected="true" 
			     oncommand="tipocomprobante(1);" accesskey="B"/>
		      <radio id="radioticket" class="media" label="Ticket"  
			     oncommand="tipocomprobante(0);" accesskey="T"/>
		      <radio id="radioalbaran" class="media" label="Albaran"
			     oncommand="tipocomprobante(4);" accesskey="A"/>
		    </radiogroup>
		  </groupbox>
		  <groupbox id="gruponb">
		    <caption  class="xmedia" id="TextoDocumentoTPV" label="Serie - Nro. de Boleta" />
		    <hbox>
		      <textbox  class="ygrande" id="SerieNDocumento" value="1"  onchange="validaSerie()" onBlur="validaSerie()" onfocus="select()" onkeypress="return soloNumerosTPV(event,this.value)"/> 
		      <textbox class="xgrande" id="NumeroDocumento" value=""  onchange="validarNroDocumento()" onBlur="validarNroDocumento()" onfocus="select()" onkeypress="if (event.which == 13) ImprimirTicket();return soloNumerosTPV(event,this.value)" /> 
		    </hbox>
		  </groupbox>
		  <textbox id="idDocTPV" value="1" collapsed="true"/> 
		</row>
		
		<row>
		<box/>
		<hbox>		
		<button flex="1" id="BotonAceptarImpresion" image="img/gpos_imprimir.png" class="media" label="  Aceptar  " oncommand="ImprimirTicket()"/>
		<button flex="1" id="BotonCancelarImpresion" image="img/gpos_vaciarcompras.png" class="media" label="  Cancelar  " oncommand="CerrarPeticion()"/>
		</hbox>
		</row>	
		<row>
		<toolbarbutton collapsed="true" flex="1" image="img/gpos_imprimir.png" class="media" label="Copia" oncommand="ImprimirTicket('copia')"/>		
		</row>	
	</rows>
</grid>
</groupbox>
</vbox>
</groupbox>
