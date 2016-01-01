<groupbox flex="1">
<vbox flex="1" align="center" pack="center" >
<caption class="xtitle"  label="Peticion Ticket" id="etiquetaTicket" />

<groupbox >
<grid>
  <columns><column/><column/></columns>
	<rows>
	    <!--  row>
	     <caption class="grande" label=" "/>
	    </row -->
		<row>		
		<caption class="grande" label="<?php echo _("TOTAL") ?>"/>
		<caption class="xbase-grande" id="peticionTotal"   label="0,00"/>
		</row>
		<row id="Fila-peticionEntrega">
		<caption class="grande" label="<?php echo _("ENTREGA") ?>" style="padding-top:0.6em"/>
		<textbox id="peticionEntrega" class="grande"  value="0,00" onfocus="select()"
                         onkeyup="ActualizaPeticion()" 
                         onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value) "/>
		</row>
		<row>
		<caption class="grande" label="<?php echo _("CAMBIO") ?>"/>
		<caption class="xbase-grande-cambio red" id="peticionPendiente" label="0,00"/>
		<textbox id="peticionCambioEntregado" collapsed="true" value="0"/>
		</row>	
		<spacer style="height: 8px"/>
		<row id="Pagos_1" collapsed="true">		
		<caption class="media" label="<?php echo _("EFECTIVO") ?>"/>
		<textbox id="peticionEfectivo" class="grande"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_2" collapsed="true">
		<caption class="media" label="<?php echo _("BONO") ?>"/>
		<textbox id="peticionBono" class="grande"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion();return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_3" collapsed="true">
		<caption class="media" label="<?php echo _("TARJETA") ?>"/>
		<textbox id="peticionTarjeta" class="grande"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion(); return soloNumerosTPV(event,this.value)"/>
		</row>			
		<row id="Pagos_4" collapsed="true">
		<caption class="media" label="<?php echo _("TRANSFERENCIA") ?>"/>
		<textbox id="peticionTransferencia" class="grande"  value="0,00" onkeyup="ActualizaPeticion()" onkeypress="ActualizaPeticion(); return soloNumerosTPV(event,this.value)"/>
		</row>			
		
		<row id="Pago_Modo">
		<caption class="grande" label="<?php echo _("PAGO") ?>" style="padding-top:0.6em"/>
		<menulist class="grande" id="modoDePagoTicket" oncommand="validarPagoCliente()">
		<menupopup>
		<?php echo genXulComboModalidadPago('1','menuitem') ?>
		</menupopup>
		</menulist>
		</row>
		<spacer style="height: 8px"/>

		<row id="Pagos_0">		
		<box/>
		<button image="img/gpos_tpvmultipagos.png" flex="1" class="media btn" 
			label=" Multipagos" oncommand="ModoMultipago()"/>
		</row>			

		<?php 
		//NOTA: condicionado a ser un administrador de facturas se muestran los controles extra 		
		if ($_SESSION["EsAdministradorFacturas"]){										
		?>		
		<row id="Admintic_0">		
		<box/>
		<button image="img/gpos_tpvmultipagos.png" flex="1" class="media btn"
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
		    <radiogroup id="comprobante" collapsed="false">
		      <radio id="radioproforma" class="rmedia" label="Proforma"
			     oncommand="tipocomprobante(5);"/>
		      <radio id="radioticket" class="rmedia" label="Ticket" selected="true" 
			     oncommand="tipocomprobante(0);" accesskey="T"/>
		      <radio id="radiofactura" class="rmedia" label="Factura"
			     oncommand="tipocomprobante(2);" accesskey="F"/>
		      <radio id="radioboleta" class="rmedia" label="Boleta"  
			     oncommand="tipocomprobante(1);" accesskey="B"/>
		      <radio id="radioalbaran" class="rmedia" label="Albaran"
			     oncommand="tipocomprobante(4);" accesskey="A"/>
		    </radiogroup>
		  </groupbox>
		  <groupbox id="gruponb">
		    <caption  class="xmedia" id="TextoDocumentoTPV" label="Serie - Nro. de Boleta" />
		    <hbox>
		      <textbox  class="grande serie" id="SerieNDocumento" value="1"  onchange="validaSerie()" onBlur="validaSerie()" onfocus="select()" onkeypress="return soloNumerosTPV(event,this.value)"/> 
		      <textbox class="grande numero" id="NumeroDocumento" value=""  onchange="validarNroDocumento()" onBlur="validarNroDocumento()" onfocus="select()" onkeypress="if (event.which == 13) ImprimirTicket();return soloNumerosTPV(event,this.value)" /> 
		    </hbox>
		  </groupbox>
		  <textbox id="idDocTPV" value="1" collapsed="true"/> 
		</row>
		
		<row>
		<box>
                  <checkbox id="checkreservar" class="Compacta red-core" label="Reservar" 
                            checked="false" oncommand="CambiarModoReserva(this.checked)" tooltiptext="Marcar el comprobante para una busqueda posterior"/>
                  <checkbox id="checkimprimir" class="Compacta red-core" label="Imprimir" tooltiptext="Lanzar la ventada de impresión"
                            checked="true" oncommand="CambiarModoImpresion(this.checked)"/>
                </box>
		<hbox>		
		<button flex="1" id="BotonAceptarImpresion" image="img/gpos_imprimir.png" class="media btn" label="  Aceptar  " oncommand="ImprimirTicket()"/>
		<button flex="1" id="BotonCancelarImpresion" image="img/gpos_vaciarcompras.png" class="media btn" label="  Cancelar  " oncommand="CerrarPeticion()"/>
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
