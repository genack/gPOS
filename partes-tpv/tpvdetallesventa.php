<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
<vbox>

  <vbox align="center"  pack="top" >
    <caption label="Comprobantes" style="font-size: 14px;"/>    
  </vbox>
  <spacer style="height:10px"/>

  <!--groupbox-->
  <!-- Busqueda Ventas -->
  <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
    <vbox>
      <description>Desde:</description>
      <datepicker id="FechaBuscaVentas" type="popup"/>
    </vbox>
    <vbox>
      <description>Hasta:</description>
      <datepicker id="FechaBuscaVentasHasta" type="popup"/>
    </vbox>
    <vbox>
      <description>Cliente:</description>
      <textbox onfocus="select()" id="NombreClienteBusqueda" 
	       onkeyup="if (event.which == 13) BuscarVentas()" style="width: 18em"
	       onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Código</description>
      <textbox onfocus="select()" id="busquedaCodigoSerie" 
	       onkeyup="if (event.which == 13) BuscarVentas()" 
	       style="width: 11em"
	       onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Documento</description>
      <menulist id="FiltroVenta" label="FiltrosVentas">
	<menupopup>
	  <menuitem value="todos" label="Todos" selected="true" oncommand="BuscarVentas()"/>
	  <menuitem value="factura" id="modoConsultaFactura" label="Factura" 
		    oncommand="BuscarVentas()" />
	  <menuitem value="boleta" id="modoConsultaBoleta" label="Boleta" 
		    oncommand="BuscarVentas()" />
	  <menuitem value="albaran" id="modoConsultaAlbaran" label="Albaran" 
		    oncommand="BuscarVentas()" />
	  <menuitem value="albaranint" id="modoConsultaAlbaranInt" label="AlbaranInt" 
		    oncommand="BuscarVentas()" />
	  <menuitem value="ticket" id="modoConsultaTicket" label="Ticket" 
		    oncommand="BuscarVentas()" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxForma_Venta" collapsed="true">
      <hbox>
	<vbox>
	  <checkbox id="modoConsultaVentas" label="Pendientes"/>
	  <checkbox id="modoConsultaVentasSerie" label="Cedidos"/>
	</vbox>
	<vbox>
	  <checkbox id="modoConsultaVentasSuscripcion" label="Suscripción"/>
	</vbox>
      </hbox>
    </vbox>
    <vbox style="margin-top:1.2em">
      <menu>
	<toolbarbutton image="img/gpos_busqueda_avanzada.png" />
	<menupopup >
	  <menuitem type="checkbox" label="Forma Venta" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	  <menuseparator />
	  <menuitem type="checkbox" label="Codigo" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="OP" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Usuario"  
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	</menupopup>
      </menu>
    </vbox>

    <vbox style="margin-top:.9em">
      <button id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" 
	      oncommand="BuscarVentas()"/>
    </vbox>
  </hbox>
  <!--/groupbox-->

  <!-- Ventas -->

    <spacer style="height:10px"/>
    <hbox flex="0">
      <caption style="font-size:10px; font-weight: bold;" label="Comprobantes" />
      <hbox flex="1" pack="center">
	<label value="Ventas:" />
	<description id="TotalVentasRealizadas" value="" />
	<label value="Facturas:" />
	<description id="TotalNroFacturas" value="" />
	<label value="Boletas:" />
	<description id="TotalNroBoletas" value="" />
	<label value="Tickets:" />
	<description id="TotalNroTicket" value="" />
	<label value="Importe:"/>
	<description id="TotalImporteVentas" value="" />
	<label value="Pendiente:"/>
	<description id="TotalImporteVentasPendiente" value="" />
	<label value="Total:"/>
	<description id="ImporteTotalVentas" value="" />
      </hbox>
    </hbox>

    <listbox id="busquedaVentas" contextmenu="AccionesBusquedaVentas" 
	     onclick="RevisarVentaSeleccionada()" 
	     onkeypress="if (event.keyCode==13) RevisarVentaSeleccionada()">
      <listcols flex="1">
	<listcol/>		
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolCodigo" collapsed="true"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolOP" collapsed="true"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1"/>		
	<splitter class="tree-splitter" />
	<listcol  collapsed="true"/>
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolUsuario" collapsed="true"/>	
      </listcols>
      <listhead>
	<listheader label=" # " style="font-style:italic;"/>
	<listheader label="Código" id="vlistCodigo" collapsed="true"/>
	<listheader label="OP" id="vlistOP" collapsed="true"/>
	<listheader label="Documento"/>
	<listheader label="Numero"  collapsed="true" />
	<listheader label="Serie-Nro"/>
	<listheader label="Cliente"/>		
	<listheader label="Fecha Registro"/>
	<listheader label="Fecha Emisión"/>
	<listheader label="Total Importe"/>
	<listheader label="Importe Pendiente"/>
	<listheader label="Estado Documento"/>
	<listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
      </listhead>

    </listbox>

    <!-- Detalles -->
    <spacer style="height:8px"/>
    <caption style="font-size:10px; font-weight: bold;" label="Detalle Comprobante" />
    <listbox id="busquedaDetallesVenta" flex="1" contextmenu="AccionesDetallesVentas" onclick="RevisarDetalleVentaSeleccionada()" >
      <listcols flex="1">
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />		
	<listcol/>
	<splitter class="tree-splitter" />		
	<listcol/>
	<splitter class="tree-splitter" />		
	<listcol/>
	<splitter class="tree-splitter" />		
	<listcol/>
	<splitter class="tree-splitter" />				
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<!--listcol flex="1"/-->
      </listcols>
      <listhead>
	<listheader label=" # " style="font-style:italic;" />
	<listheader label="CR" />
	<listheader label="CB" />
	<listheader label="Producto"  />
	<listheader label="Detalle"  />
	<listheader label="Cantidad"/>
	<listheader label="PU"/>
	<listheader label="DCTO(%)"/>	
	<listheader label="PV" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
	<listheader label="" />
      </listhead>
    </listbox>

  <hbox align="end" pack="end" id="btnsDevolucion" collapsed="true" style="padding-right: 1em;margin-top:.8em">
    <description value="Acciones Devolución:" style="font-size: 14px;margin: .7em;"/>
    <button  image="img/gpos_compras.png" label=" Seleccionar Todo" oncommand="seleccionarALLDetalle2Devolucion()"  />
    <button image="img/gpos_vaciarcompras.png" label=" Cancelar" oncommand="checkDevolucionDetalle(true)" />
    <button image="img/gpos_fincompras.png" label=" Devolver" oncommand="DevolverVentaSeleccionada()"/>
  </hbox>
  <box flex="1"/>
  <button class="media"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false" id="btnreturndetventa"/>
</vbox>
