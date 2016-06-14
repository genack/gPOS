
<vbox flex="1" zalign="center" pack="center">
  <tabbox flex="1">
    <tabs style="font-size: 11px;">
      <tab id="tab-selcliente" label=" Selección de cliente" image="img/gpos_buscarcliente.png" />
      <tab id="tab-newcliente" label=" Nuevo cliente"  image="img/gpos_nuevocliente.png" />
      <tab id="tab-vistacliente" label="" collapsed="true"/>
      <tab id="tab-suscripcion" label=" " image="img/gpos_tpvciclica.png" collapsed="true"/>
    </tabs>
    <tabpanels id="tab-boxclient" flex="1" class="box">
      <tabpanel flex="1">
	<groupbox flex="1">
	  <caption label="Listado de clientes:" style="font-size: 13px;"/>
	  <vbox flex="1" zalign="top" pack="center" style="overflow: auto">
	    <vbox align="left" pack="top">
	      <hbox>   
		<button class="btn" image="img/gpos_tpv_clientecontado.png" id="clieLista" 
			 label="  Cliente Contado" oncommand ="pickClienteContado()"
			 style="font-size: 13px;"/>        
		<button class="btn" image="img/gpos_clienteparticular.png"  style="font-size: 13px;"
			 label="  Usar seleccionado"   oncommand ="cargarCliente('sel')"/>
		<textbox  id="buscaCliente" onkeyup="if (event.which == 13) cargarCliente('uno')" 
			  oninput="buscarCliente()" placeholder=" Buscar cliente " 
			  style="width: 25em;font-size: 13px;" tooltiptext="Buscar Cliente: Nombre,Telefono,DNI"/>
		<textbox  id="buscaClienteSelect" value="0" collapsed="true"/>
                <toolbarbutton id="syncModuloClientes" class="sync_module_off" oncommand="pushSyncModule('Clientes')" tooltiptext="Sincronizar Clientes..."></toolbarbutton>
	      </hbox>
	    </vbox>   
	    <listbox id="clientPickArea" class="listado"  flex="1" style="height: 100%;" 
		     contextmenu="AccionesclientPickArea" value="0" 
		     onkeyup="if (event.which == 13) cargarCliente('sel')" >
	      <listcols>
		<listcol flex="1"/>
		<listcol flex="1"/>
		<listcol flex="8"/>
		<listcol flex="1"/>
		<listcol flex="1"/>
   		<listcol flex="1"/>
      		<listcol flex="1"/>
		<listcol flex="3"/>
	      </listcols>
	      <listhead>
		<listheader label="#" />
		<listheader label="DNI/RUC" />
		<listheader label="Cliente" />
		<listheader label="Debe" />
		<listheader label="Bono" />
   		<listheader label="Crédito" />
      		<listheader label="Categoría" />
		<listheader label="Teléfono"/>
	      </listhead>
	    </listbox>
	  </vbox>
	</groupbox>
      </tabpanel>
      <tabpanel>
	<groupbox flex="1">
	  <caption label="Datos administrativos del cliente:" style="font-size: 13px;"/>
	  <box flex="1">
	    <grid flex="1" style="font-size: 11px;">
	      <rows flex="1"> 
		<columns><column/></columns>
		<row><caption label="Tipo"/>
		  <menulist id="TipoCliente" sizetopopup="pref" value="0" label="Particular">
		    <menupopup>
		      <menuitem value="Particular" oncommand="setTipoCliente(this.value,'')" 
				label="Particular" selected="true"></menuitem>
		      <menuitem value="Independiente" oncommand="setTipoCliente(this.value,'')"
				label="Independiente"></menuitem>
		      <menuitem value="Empresa"  oncommand="setTipoCliente(this.value,'')"
				label="Empresa"></menuitem>
		      <menuitem value="Gobierno" oncommand="setTipoCliente(this.value,'')"
				label="Gobierno"></menuitem>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		  <caption id="mtxtNombreComercial" label="Nombre"/>
		  <textbox class="xnombre" onchange="ckeckNombreComercial()" 
			   onkeyup="validaCliente(this)"  id="NombreComercial"/>
		</row>

		<row id="mtxtNombreLegal" collapsed="true">
		  <caption label="Nombre Legal"/>
		  <textbox class="xnombre" onkeyup="validaCliente(this)" 
			   style="text-transform:uppercase;"  id="NombreLegal"/>
		</row>
		<row>
		<caption label="Email"/>
		<textbox class="xmail" onchange="return validaCliente(this)" id="Email"/>
		</row>
		<row>
		<caption id="txtNFiscal" label="DNI"/>
		<textbox class="xnif"  onchange="validaCliente(this)" maxlength="8"
			 onkeypress="return soloNumerosTPV(event,this.value)"  id="NumeroFiscal"/>
		</row>
		<row>
		  <caption label="Dirección"/>
		  <textbox class="xdireccion" onkeyup="validaCliente(this)" id="Direccion"/>
		</row>
		<row>
		  <caption label="Telefono"/>
		  <textbox id="Telefono1" onkeypress="return soloNumerosTelefono(event)"/>
		</row>
		<row id="mtxtFechaNacimiento">
		  <caption label="Fecha Nacim."/>
		  <datepicker id="FechaNacimiento" type="popup" />
		</row>
		<row>
		  <caption label="Comentarios"/>
		  <textbox onkeypess="return soloAlfaNumericoTPV(event)"
			   multiline="true" id="Comentarios"/>
		</row>
		<row>
		  <box/>
		  <button class="btn" image="img/gpos_clienteparticular.png" oncommand="AltaCliente()" 
			  label=" Registrar"/>
		</row>
	      </rows>
	    </grid>
	  </box>
	</groupbox>
      </tabpanel>
      <tabpanel>
	<groupbox  >
	  <caption label="Datos administrativos del cliente:" style="font-size: 13px;"/>
	  <box>
	    <grid style="font-size: 11px;">
	      <rows> 
		<columns><column/></columns>
		<row><caption label="Tipo"/>
		  <menulist id="visTipoCliente" sizetopopup="pref" value="0" label="Particular">
		    <menupopup>
		      <menuitem value="Particular" oncommand="setTipoCliente(this.value,'vis')" 
				label="Particular" selected="true"></menuitem>
		      <menuitem value="Independiente" oncommand="setTipoCliente(this.value,'vis')"
				label="Independiente"></menuitem>
		      <menuitem value="Empresa"  oncommand="setTipoCliente(this.value,'vis')"
				label="Empresa"></menuitem>
		      <menuitem value="Gobierno" oncommand="setTipoCliente(this.value,'vis')"
				label="Gobierno"></menuitem>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		<caption id="vismtxtNombreComercial" label="Nombre Comercial"/>
		<textbox class="xnombre" onkeyup="validaCliente(this)"  
			 style="text-transform:uppercase;"  id="visNombreComercial"/>
		</row>
		<row id="vismtxtNombreLegal">
		<caption  label="Nombre Legal"/>
		<textbox class="xnombre" onkeyup="validaCliente(this)" 
			 style="text-transform:uppercase;" id="visNombreLegal"/>
		</row>
		<row>
		<caption label="Email"/>
		<textbox class="xmail" onchange="return validaCliente(this)" id="visEmail"/>
		</row>
		<row>
		<caption id="vistxtNFiscal" label="RUC"/>
		<textbox class="xnif" onchange="validaCliente(this)" maxlength="11"
			 onkeypress="return soloNumerosTPV(event,this.value)"  id="visNumeroFiscal"/>
		</row>
		<row>
		<caption label="Dirección"/>
		<textbox class="xdireccion" onkeyup="validaCliente(this)" 
			 style="text-transform:uppercase;" id="visDireccion"/>
		</row>
		<row>
		  <caption label="Telefono"/>
		  <textbox id="visTelefono1" onkeypress="return soloNumerosTelefono(event)" />
		</row>
		<row id="vismtxtFechaNacimiento">
		  <caption label="Fecha Nacim."/>
		  <datepicker id="visFechaNacimiento" type="popup" />
		</row>
		<row>
		  <caption  label="Comentarios"/>
		  <textbox onkeypess="return soloAlfaNumericoTPV(event)" 
			   multiline="true" id="visComentarios"/>
		</row>
		<!-- row><caption   label="Modo pago hab."/>
		<menulist  id="visModoPago"  >
		  <menupopup -->
		  <?php
		    /*foreach( $modosDePago as $value=>$label ){
		      echo "<menuitem value='$value' label='$label'/>\n";
		      }*/
		  ?>
		  <!-- /menupopup>
		</menulist>
		</row -->
		  <row>	
		    <box/>
		    <box>
		      <button class="btn" image="img/gpos_aceptar.png" oncommand="ModificarCliente()" 
			      label="Modificar" flex="1"/>
		      <!--button image="img/gpos_cancelar.png" label="Eliminar"
			      oncommand="EliminarClienteActual()" flex="1"/-->
		    </box>
		  </row>
	      </rows>
	    </grid>
	  </box>
	</groupbox>
      </tabpanel>
      <tabpanel>
	<groupbox flex="1" >
	    <grid style="font-size: 11px;">
	      <rows> 
		<columns><column/></columns>
		<row >
		  <caption label="Contratos:   " style="padding-right:.45em"/>
		  <menulist id="suscripComboContratos" sizetopopup="pref" flex="1"
			    style="text-transform:uppercase;">
 		    <menupopup id="elementosSuscripcion"/>
		  </menulist>
		</row>
	      </rows>
	    </grid>
	  <hbox>
	    <grid style="font-size: 11px;">
	      <rows> 
		<columns><column/></columns>
		<row id="rowTipoSuscripcion">
		  <caption label="Suscripción"/>
		  <menulist id="suscripTipoSuscripcion" sizetopopup="pref" 
			    value="0" label="" >
		    <menupopup id="elementosTipoSuscripcion">
		      <menuitem value="0" label="Nueva Suscripción"
				oncommand="mostrarNuevoTipoSuscripcion('true')"
				style="font-weight: bold;"></menuitem>
		      <?php echo genXulComboTipoSuscripcion(false,'menuitem','RegistrarSuscripcionCliente()') ?>
		    </menupopup>
		  </menulist>
		</row>
		<row id="rowNuevoTipoSuscripcion" collapsed="true">
		  <caption label="Suscripción"/>
		  <textbox id="textNuevoTipoSuscripcion" value=""
			   onkeyup="if (event.which == 13) 
				    RegistrarTipoSuscripcion(this.value)"
			   placeholder="Nueva Suscripción"
			   onblur="mostrarNuevoTipoSuscripcion(false)"/>
		</row>
		<row>
		  <caption label="Tipo Pago"/>
		  <menulist id="suscripTipoPago" sizetopopup="pref"
			    value="0" label="" 
			    oncommand="ModificarSuscripcionCliente('2');">
		    <menupopup>
		      <menuitem value="Prepago" label="Pre-Pago" selected="true"/>
		      <menuitem value="Postpago" label="Post-Pago"/>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		  <caption label="Prolongación"/>
		  <menulist id="suscripProlongacion"
			    sizetopopup="pref" value="0" label=""
			    oncommand="mostrarSuscripcionProlongacion(this.value);
			    ModificarSuscripcionCliente('3');">
		    <menupopup>
		      <menuitem value="Ilimitado" 
				label="Plazo ilimitado" selected="true"/>
		      <menuitem value="Limitado" 
				label="Plazo limitado"/>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		  <caption label="Fecha inicio"/>
		  <datepicker id="suscripFechaInicio" type="popup" value=""
                              onblur="ModificarSuscripcionCliente('4');"/>
		</row>
		<row id="rowFechaFinSuscripcion">
		  <caption label="Fecha fin"/>
		  <datepicker id="suscripFechaFin" type="popup" value=""
                              onblur="ModificarSuscripcionCliente('5');"/>
		</row>
	      </rows>
	    </grid>
	    <grid style="font-size: 11px;">
	      <rows>
		<columns><column/></columns>
		<row>
                  <caption label="Estado"/>
		  <menulist id="suscripEstado" sizetopopup="pref"
			    value="0" label="" 
                            oncommand="ModificarSuscripcionCliente('6');">
		    <menupopup>
		      <menuitem value="Pendiente" id="itemSuscripcionPendiente"
				label="Pendiente" selected="true"/>
		      <menuitem value="Ejecucion" id="itemSuscripcionEjecucion"
				label="Ejecucion" />
		      <menuitem value="Suspendido" id="itemSuscripcionSuspendido"
				label="Suspendido" />
		      <menuitem value="Finalizado" id="itemSuscripcionFinalizado"
				label="Finalizado" />
		      <menuitem value="Cancelado" id="itemSuscripcionCancelado"
				label="Cancelado" />
		    </menupopup>
		  </menulist>
		</row>
		<row>
                  <caption label="Comprobante"/>
		  <menulist id="suscripComprobante" sizetopopup="pref"
			    value="0" label=""
			    oncommand="mostrarSuscripcionComprobante(this.value);
                                       ModificarSuscripcionCliente('7');">
		    <menupopup>
		      <menuitem value="Factura" label="Factura" ></menuitem>
		      <menuitem value="Boleta" label="Boleta" ></menuitem>
		      <menuitem value="Ticket" label="Ticket" selected="true"></menuitem>
		    </menupopup>
		  </menulist>
		</row>
		<row id="rowSuscripcionSerieComprobante">
		  <caption label="Serie"/>
		  <textbox id="suscripSerieComprobante" value="1" maxlength="4" 
                           onchange="ValidarSerieComprobanteSuscripcion();
                                     ModificarSuscripcionCliente('8');"
			   onkeypress="return soloAlfaNumericoTPV(event,this.value);"/>
		</row>
		<row>
		  <caption  label="Observaciones"/>
		  <textbox onkeypess="return soloAlfaNumericoTPV(event)" 
			   multiline="true" id="suscripComentarios"
                           onchange="ModificarSuscripcionCliente('9');"/>
		</row>
		<row>
                    <caption label="Administrador"/>
                    <hbox>
	                <toolbarbutton id="btnSubsidiario" label="+"
                                       oncommand="auxAltaSubsidiario()"/> 
	                <toolbarbutton id="btnSubsidiarioHab" label="..." oncommand="auxSubsidiarioHab()"/>
                        <textbox id="EmpresaTextGasto" value="" flex="1" readonly="true"
                                 onkeypress="return soloAlfaNumerico(event);"/>
                        <textbox id="IdSubsidiario" value="" flex="1" collapsed="true"/>
                    </hbox>
		</row>
	      </rows>
	    </grid>
	  </hbox>

	  <panel id="panelSuscripcionLinea" style="border:1px solid #aaa">
	    <vbox align="center">
	      <caption label="Suscripción Servicio" style="font-size: 12px;"
                       id="titleSuscripcionLinea"/>
	    </vbox>
	    <vbox style="padding:1.5em 1em 0 1em">
	      <hbox>
		<description  style="font-weight: bold;width:9em">Concepto:</description>
		<textbox onkeypess="return soloAlfaNumericoTPV(event)"
			 id="suscripLineaConcepto" style="width:33em"/>
		<textbox id="suscripProductoServicio" value="" collapsed="true"/>
		<textbox id="suscripProductoCodigoBarras" value="" collapsed="true"/>
		<textbox id="suscripProductoCodigoReferencia" value="" collapsed="true"/>
	      </hbox>
	    </vbox>
            <hbox style="padding:1em">
	      <hbox>
		<vbox>
		  <grid>
		    <rows>
		      <row>
			<description style="width:9em" >Cantidad</description>
			<textbox onkeypress="return soloNumerosEnterosTPV(event,this.value)"
				 value="1" id="suscripLineaCantidad"
				 onchange="calcularImporteSuscripcionLinea()"/>
		      </row>
		      <row>
			<description>Precio</description>
			<textbox onkeypress="return soloNumerosTPV(event,this.value)"
				 value="0.00" id="suscripLineaPrecio"
				 onchange="calcularImporteSuscripcionLinea()"/>
		      </row>
		      <row id="rowSuscripcionDescuento">
			<description>Descuento</description>
			<textbox onkeypress="return soloNumerosTPV(event,this.value)"
				 value="0.00" id="suscripLineaDescuento"
				 onchange="calcularImporteSuscripcionLinea()"/>
		      </row>
		      <row>
			<description>Importe</description>
			<textbox onkeypress="return soloNumerosTPV(event,this.value)"
				 value="0.00" id="suscripLineaImporte" readonly="true"/>
		      </row>
		      <row id="vboxSuscripcionLineaAdelantoPlazo" collapsed="true">
			<description>Periodo Adelanto</description>
			<textbox onkeypress="return soloNumerosEnterosTPV(event,this.value)"
				 value="0" id="suscripAdelantoPlazo"
				 onchange="calcularImporteSuscripcionAdelantoLinea()"/>
		      </row>

		      <row id="vboxSuscripcionLineaAdelantoPlazoImporte" collapsed="true">
			<description  >Importe Adelanto</description>
			<textbox id="suscripAdelantoPlazoImporte"  value="0.00" readonly="true"/>
		      </row>
		      <row id="rowOrdenServicioNumeroSerie" collapsed="true">
			<description >Número Serie</description>
			<textbox onkeypess="return soloAlfaNumericoTPV(event)"
				 placeholder="Ingrese series entre comas"
				 value="" id="OrdenServicioNumeroSerie" 
				 style="width: 20em;"/>
		      </row>

		    </rows>
		  </grid>
		</vbox>
	      <spacer style="width:2em"/>
		<vbox>
		  <grid>
		    <rows>

		      <row id="vboxSuscripcionLineaPlazoPago" collapsed="false">
			<description>Tolerancia Pago </description>
			<grid>
			  <rows>
			    <row>
			      <textbox onkeypress="return soloNumerosEnterosTPV(event,this.value)"
				 value="1" id="suscripPlazoPago"/>
			      <description>día(s)</description>
			    </row> 
			  </rows> 
			</grid>
		      </row>

		      <row id="rowSuscripcionIntervalo">
			<description >Intervalo</description>
			<grid>
			  <rows>
			    <row>
			      <textbox onkeypress="return soloNumerosEnterosTPV(event,this.value)"
				       value="1" id="suscripLineaIntervalo" />
			      <description>Mes</description>
			      <menulist id="suscripUnidadIntervalo" label="FiltrosUnidadIntervalo" 
					collapsed="true">
				<menupopup>
				  <menuitem value="Mes" label="Mensual"  selected="true" />
				  <menuitem value="Semana" label="Semanal"/>
				  <menuitem value="Dia" label="Diario" />
				</menupopup>
			      </menulist>
			    </row> 
			  </rows> 
			</grid>

		      </row>
		      <row id="vboxSuscripcionLineaDiaFacturacion" collapsed="false">
			<description>Día Facturación</description>

			<grid>
			  <rows>
			    <row>
			      <textbox onkeypress="return soloNumerosEnterosTPV(event,this.value)"
				       style="" onchange="validasuscripDiaFacturacion(this)"
				       value="1" id="suscripDiaFacturacion" />
			    </row> 
			  </rows> 
			</grid>

		      </row>

		      <row id="vboxSuscripcionLineaEstado" collapsed="false">
			<description>Estado</description>
			<menulist id="suscripEstadoLinea" label="EstadoLinea"  oncommand="">
			  <menupopup>
			    <menuitem value="Activo" label="Activo" selected="true"  />
			    <menuitem value="Inactivo" id="itemEstadoInactivo" label="Inactivo" />
			  </menupopup>
			</menulist>
		      </row>
		      
		    </rows>
		  </grid>
		</vbox>
	      </hbox>

          </hbox>

	  <hbox id="rowMostrarInformacionExtra" collapsed="true" align="start" pack="center">
	    <description id="MostrarInformacionExtra" style="color:#000;font-style: italic;"/>
	  </hbox>

            <hbox>
	      <button flex="1" image="img/gpos_cancelar.png" label="Cancelar"
                      id="btnSuscricpionLineaCancel" class="btn"
		      oncommand="salirSuscripcionLinea()"></button>
	      <button flex="1" image="img/gpos_aceptar.png" label="Aceptar"
                      id="btnSuscricpionLinea" class="btn"
		      oncommand="registrarSuscripcionLinea()"></button>
            </hbox>
	  </panel>
	  <tabbox flex="1">
	    <tabs style="font-size: 11px;">
	      <tab id="tab-suscriptlineas" label="Detalle" />
	      <tab id="tab-suscriptfacturas" label="Comprobantes"/>
	      <tab id="tab-otrasacciones" label="Otras Acciones"/>
	    </tabs>

	    <tabpanels flex="1" class="box">
	      <tabpanel flex="1">
		<vbox flex="1">

		  <hbox>
		    <listbox id="listSuscripcionLinea" class="listado"  flex="1"
                         contextmenu="AccionesSuscripcionLinea"
			 value="0" onkeyup="if (event.which == 13) cargarCliente('sel')" 
                         onclick="xmenuSuscripcionLinea()">
		      <listcols>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="8"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="0"/>
		      </listcols>
		      <listhead>
			<listheader label="#" /> 
			<listheader label="Estado" />
			<listheader label="Producto" />
			<listheader label="Cantidad" />
			<listheader label="Precio" />
			<listheader label="Dcto" />
			<listheader label="Importe" />
			<listheader label="Intervalo"/>
			<listheader label="Facturación"/>
			<listheader label=""/>
		      </listhead>
		    </listbox>
		  </hbox>
		</vbox>
	      </tabpanel>
	      <tabpanel flex="1">
		<vbox flex="1">
		  <hbox>
		    <hbox align="start" pack="center" 
			  id="comprobantesSuscripcionBusqueda" flex="1" >
		      <vbox>
			<description value="Desde:"/>
			<datepicker id="FechaComprobanteSuscripcion" type="popup" 
                                    onblur="BuscarSuscripcionComprobante()"/>
		      </vbox>
		      <vbox>
			<description value="Hasta:"/>
			<datepicker id="FechaComprobanteSuscripcionHasta" type="popup" 
                                    onblur="BuscarSuscripcionComprobante()"/>
		      </vbox>
		      <vbox>
			<description>Documento</description>
			<menulist id="FiltroSuscripcionDocumento" label="" 
                                  oncommand="BuscarSuscripcionComprobante()">
			  <menupopup>
			    <menuitem value="Todos" label="Todos" selected="true"/>
			    <menuitem value="Factura" id="modoConsultaFactura" label="Factura"/>
			    <menuitem value="Boleta" id="modoConsultaBoleta" label="Boleta"/>
			    <menuitem value="Ticket" id="modoConsultaTicket" label="Ticket" />
			  </menupopup>
			</menulist>
		      </vbox>
		      <vbox >
			<description>Estado</description>
			<menulist id="FiltroEstadoComprobante" label=""  
                                  oncommand="BuscarSuscripcionComprobante()">
			  <menupopup>
			    <menuitem value="Todos" label="Todos" selected="true"/>
			    <menuitem value="Pendiente" id="modoConsultaPendiente" label="Pendiente"/>
			    <menuitem value="Pagado" id="modoConsultaPagado" label="Pagado" />
			  </menupopup>
			</menulist>
		      </vbox>
		      <vbox  style="margin-top:1.1em">
		      <button class="btn" id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" 
                              oncommand="BuscarSuscripcionComprobante()"/>	
		      </vbox>	      
		    </hbox>
		  </hbox>

		  <hbox>
 		    <listbox id="listComprobantesSuscripcion" class="listado"  flex="1"  
			     contextmenu="AccionesComprobantesVentaSuscripcion" >
		      <listcols>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
			<listcol flex="1"/>
		      </listcols>
		      <listhead>
			<listheader label="#" /> 
			<listheader label="Documento" />
			<listheader label="Código" />
			<listheader label="Serie-Nro" />
			<listheader label="Fecha Emisión" />
			<listheader label="Importe" />
			<listheader label="Pendiente" />
			<listheader label="Estado"/>
		      </listhead>
		    </listbox>
		  </hbox>
		</vbox>
	      </tabpanel>
              <tabpanel pack="center">
                <vbox id="vboxAccionesExtraSuscripcion" collapsed="true">
	          <grid style="font-size: 13px;">
	            <rows>
		      <columns><column/></columns>
		      <row>
                        <button label=" Crear Orden servicio "  class="btn"
                                oncommand="mostrarFormSuscripcionToOrdenServicio()" />
		      </row>
		      <row id="SuscripFichaTecnica" collapsed="true">
                           <button label=" Ir a Ficha Técnica "  class="btn"
                                   oncommand="mostrarSuscripcionFichaTecnica('suscrip')" />
		      </row>
	            </rows>
	          </grid>
                </vbox>
                <vbox id="formSuscripcionToOrdenServicio" collapsed="true" align="center">
		  <caption class="h1" label="Crear Orden de Servicio" />

	          <hbox>
		    <caption label="Cliente:"></caption>
		    <caption id="SuscripToOrdenCliente"/>
	          </hbox>
                  <hbox >
	            <hbox> 
		      <grid>
		        <rows>
		          <row>
			    <caption label="Prioridad"/>
			    <menulist id="suscripToOrdenPrioridad" label="" >
			      <menupopup>
			        <menuitem value="1" label="Normal" selected="true"/>
			        <menuitem value="2" label="Alta"/>
			        <menuitem value="3" label="Muy Alta" />
			      </menupopup>
			    </menulist>
		          </row>
		          <row>
			    <caption label="Observaciones"/>
			    <textbox onkeypress="return soloAlfaNumericoTPV(event)" 
				     id="suscripToOrdenObs" multiline="true" rows="3"
                                     style="width:25em"/>
		          </row>
                          <row>
			    <caption label=""/>
                            <hbox>
	                      <button flex="1" image="img/gpos_cancelar.png" label="Cancelar"
                                       id="btnSuscripToOrdenCancel" class="btn"
		                       oncommand="salirSuscripToOrden()"></button>
	                      <button flex="1" image="img/gpos_aceptar.png" label="Aceptar"
                                       id="btnSuscripToOrden" class="btn"
		                       oncommand="registrarSuscripOrdenServicio()"></button>
                             </hbox>
                          </row>
                        </rows>
                      </grid>
                    </hbox>
                  </hbox>
                </vbox>
	      </tabpanel>
	    </tabpanels>
	  </tabbox>
	</groupbox>
      </tabpanel>
    </tabpanels>
  </tabbox>
  <button class="media btn"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>

</vbox>

