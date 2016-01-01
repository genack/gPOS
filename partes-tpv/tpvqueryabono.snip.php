<box>
  <spacer flex="1"/>
  <box  align="center" pack="center">
    <spacer flex="1" />
    <deck>
      <groupbox>
	<hbox>
	  <vbox align="center">
	    <caption id="titleAbonoVentana" class="xtitle" label="Abonar Ticket"/>
	    <spacer style="height: 8px"/>
	    <hbox>
	      <groupbox>
		<caption id="titleAbonoVentana" class="box" label="Abono:"/>
		<grid>
		  <columns><column/></columns>
		  <rows>
		    <row class="xbase">
		      <caption class="xbase" label="Cliente:"/>
		      <description  class="xbase" id="abono_cliente"  value=""/>
		    </row>
		    <row class="xbase">
		      <caption class="xbase">Código Doc.:</caption>
		      <description  class="xbase" id="abono_numTicket" value=""/>
		    </row>
		    <row class="xbase">
		      <caption  class="xbase" label="Debe:"/>
		      <description  class="xbase" id="abono_Debe" value="0.00"/>
		    </row>
		    <row class="xbase">
		      <caption class="xbase"  label="Abona:"/>
		      <description  class="xbase" id="abono_nuevo"  value="0.00"/>
		    </row>	
		    <row class="xbase"> 
		      <caption class="xbase" label="Nuevo Pendiente:"/>
		      <description class="xbase" id="abono_Pendiente"  value="0.00"/>
		    </row>	
		    <row id="Fila-AbonoEntrega">		
		      <caption  label="MONTO"></caption>
		      <textbox  class="xnormal" id="abono_Monto" value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticionAbono();return soloNumerosTPV(event,this.value)" onchange="validarFormularioAbono()" onfocus="select()" />
		    </row>
		    <row id="Abonos_1" collapsed="true">		
		      <caption  label="EFECTIVO"></caption>
		      <textbox  class="xnormal" id="abono_Efectivo"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticionAbono();return soloNumerosTPV(event,this.value)" onchange="validarFormularioAbono()"/>
		    </row>			
		    <row id="Abonos_2" collapsed="true">
		      <caption label="BONO"></caption>
		      <textbox  class="xnormal" id="abono_Bono"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticionAbono();return soloNumerosTPV(event,this.value)" onchange="validarFormularioAbono()"/>
		    </row>			
		    <row id="Abonos_3" collapsed="true">
		      <caption label="TARJETA"></caption>
		      <textbox  class="xnormal" id="abono_Tarjeta"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticionAbono();return soloNumerosTPV(event,this.value)" onchange="validarFormularioAbono()"/>
		    </row>
		    <row id="Abono_Modo">
		      <caption label="Modo Pago"></caption>
		      <menulist class="xnormal" id="modoDeAbonoTicket" 
				oncommand="verDetalleDocumentoCobros()" >
			<menupopup>
			  <?php echo genXulComboModalidadPago('1','menuitem') ?>
			</menupopup>
		      </menulist>
		    </row>
		    <spacer style="height: 8px"/>
		    <row id="Abonos_0">		
		      <box/>
		      <button image="img/gpos_tpvmultipagos.png" flex="1" class="xnormal btn" 
			      label=" Multipagos" oncommand="ModoMultipagoAbono()"/>
		    </row>			
		    <row id="rowFechaPago">
		      <caption label="Fecha Pago"></caption>
		      <hbox>
			<datepicker id="dateFechaPago" style="font-size: 13px" type="popup" />
			<timepicker id="timeFechaPago" style="font-size: 13px" type="popup" />
		      </hbox>
		    </row>			
		    <spacer style="height: 8px"/>
		    <row id="rowReservaEntregado">
		      <description class="xnormal">Entregar Reserva</description>
		      <checkbox  class="xnormal" id="checkReservaEntregago" 
				 oncommand="VerFechaReservaEntregado(this.checked)"/>
		    </row>
		    <row id="rowFechaReservaEntregado" collapsed="true">
		      <caption label="Fecha Entrega"></caption>
		      <hbox>
			<datepicker id="dateFechaEntrega" style="font-size: 13px" type="popup" />
			<timepicker id="timeFechaEntrega" style="font-size: 13px" type="popup" />
		      </hbox>
		    </row>			
		    <spacer style="height: 8px"/>				
		    <row>
		      <box/>
		    </row>	
		  </rows>
		</grid>
	      </groupbox>
              <spacer style="width: 10px"/>
	      <groupbox id="DetallePagoDocumento" collapsed="true" >
		<caption id="titleAbonoVentana" class="box" label="Detalle:"/>
		<hbox>
		  <grid>
		    <columns>
		      <column flex="1"></column>
		      <column flex="1"></column>
		    </columns>
		    <rows >
		      <row id="entFinanciera" collapsed="false">
			<caption label="Entidad Financiera"></caption>
			<hbox>		      
			  <toolbarbutton oncommand="CogeNroCuenta()" label="+"></toolbarbutton>
			  <menulist class="xnormal" id="EntidadFinanciera"
				    flex="2" style="min-width: 7em"
				    oncommand="RegenCuentas()" label="Elija...">
			    <menupopup class="xnormal" id="elementosEntFinanciera">
			    </menupopup>
			  </menulist>
			  <textbox class="xnormal" id="EntidadFinanciera1"
				   size="50" collapsed="true"
				   onkeypress="return soloAlfaNumerico(event);"/>
			</hbox>
		      </row>
		      <row id="cuentaEmpresa">
			<caption label="Nro Cuenta"></caption>
			<hbox>		      
			  <menulist class="xnormal" id="NroCtaEmpresa" flex="2" style="min-width: 7em" label="Elija..." >
			    <menupopup class="xnormal" id="elementosCuenta">
			    </menupopup>
			  </menulist>
			  <textbox id="CtaEmpresa1"  size="50"
				   onfocus="this.select()" collapsed="true"
				   onkeypress="return soloNumeros(event,this.value)"/>
			</hbox>
		      </row>
		      <row id="codOperacion">
			<caption label="Código Operación"></caption>
			<textbox class="xnormal" id="CodigoOperacion" size="40" 
                                 onfocus="this.select()" value="000000"
				 onkeypress="return soloNumeros(event,this.value)"/>
		      </row>
		      <row id="numDocumento">
			<caption label="Nro Documento"></caption>
			<textbox class="xnormal" id="NroDocumento"  size="40" onfocus="this.select()"
				 onkeypress="return soloNumeros(event,this.value)"/>
		      </row>
		      <row>
			<caption label="Observaciones"></caption>
			<textbox class="xnormal" id="ObservacionesDocCobro" 
				 multiline="true" rows="1" cols="10" 
				 style="width:20em;"  
				 onpaste="return false"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		    </rows>
		  </grid>
		</hbox>
	      </groupbox>


	    </hbox>
	    <hbox pack="center" align="center">
	      <button  class="xnormal btn" flex="1" image="img/gpos_aceptar.png"  label=" Abonar" oncommand="RealizarAbono()"/>		
	      <button  class="xnormal btn" flex="1" image="img/gpos_cancelar.png" label=" Cancelar" oncommand="VolverVentas()"/>
	    </hbox>
	  </vbox>
	</hbox>
      </groupbox>
    </deck>
  </box>
  <spacer flex="1"/>
</box>
