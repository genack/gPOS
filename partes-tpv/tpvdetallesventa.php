<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
<vbox id="boxComprobantesVenta" collapsed="true">

  <vbox align="center"  pack="top" >
    <caption class="h1" label="Comprobantes" />    
  </vbox>

  <!--groupbox-->
  <!-- Busqueda Ventas -->
  <hbox align="start" pack="center" >
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
	       onkeyup="if (event.which == 13) BuscarVentas()" 
	       onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Código:</description>
      <textbox onfocus="select()" id="busquedaCodigoSerie" 
	       onkeyup="if (event.which == 13) BuscarVentas()" 
	       style="width: 11em"
	       onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Documentos:</description>
      <menulist id="FiltroVenta" label="FiltrosVentas"  oncommand="BuscarVentas()">
	<menupopup>
	  <menuitem value="todos" label="Todos" selected="true" />
	  <menuitem value="caja" id="modoDcumentoSoloCaja" label="Solo Caja" <?php gulAdmite("CajaTPV") ?> />
	  <menuseparator />
	  <menuitem value="factura" id="modoConsultaFactura" label="Factura" />
	  <menuitem value="boleta" id="modoConsultaBoleta" label="Boleta" />
	  <menuitem value="albaran" id="modoConsultaAlbaran" label="Albaran" />
	  <menuitem value="albaranint" id="modoConsultaAlbaranInt" label="AlbaranInt" />
	  <menuitem value="ticket" id="modoConsultaTicket" label="Ticket"/>
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxUsuario" collapsed="true">
	<description>Usuario:</description>
	<menulist  id="IdUsuarioVentas" oncommand="BuscarVentas()">
	  <menupopup>
	    <menuitem label="Todos" value="todos" selected="true"/>
            <?php echo genXulComboUsuarios(false,"menuitem",$IdLocalActivo) ?>
	  </menupopup>
        </menulist>	
    </vbox>
    <vbox id="vboxTipo_Producto" collapsed="true">
      <description>Tipo Producto:</description>
      <menulist  id="TipoProducto" oncommand="BuscarVentas()">
	<menupopup>
	  <menuitem label="Todos" value="todos" selected="true"/>
	  <menuitem label="Producto" value="Producto" />
	  <menuitem label="Servicio" value="Servicio" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxForma_Venta" collapsed="true">
      <hbox>
	<vbox>
	  <description>Tipo Venta:</description>
	  <menulist id="modoConsultaTipoVenta" label="FiltrosTipoVentas"  oncommand="BuscarVentas()">
	    <menupopup>
	      <menuitem value="todos" label="Todos" selected="true" />
	      <menuseparator />
	      <menuitem value="contado" label="Contado" />
	      <menuitem value="credito" label="Crédito"  oncommand="BuscarPlazo()"/>
	      <menuitem value="reservas" label="Reserva" oncommand="BuscarReservados()"/>
	      <menuitem value="suscripcion" label="Suscripción" />
	    </menupopup>
	  </menulist>
	</vbox>

	<vbox>
	  <checkbox id="modoConsultaVentasPen" label="Pendientes" oncommand="BuscarVentas()"/>
	  <checkbox id="modoConsultaVentasFin" label="Finalizados" oncommand="BuscarVentas()"/>
	</vbox>
      </hbox>
    </vbox>
    <vbox style="margin-top:1em">
      <menu>
	<toolbarbutton image="img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
	<menupopup >
	  <menuitem type="checkbox" label="Usuario" checked="false"
                    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Forma Venta" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	  <menuitem type="checkbox" label="Tipo Producto" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/> 
	  <menuseparator />
	  <menuitem type="checkbox" label="Codigo" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="OP" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Registro" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	</menupopup>
      </menu>
    </vbox>

    <vbox style="margin-top:1.2em">
      <button class="btn" id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" 
	      oncommand="BuscarVentas()"/>
    </vbox>
  </hbox>
  <!--/groupbox-->

  <!-- Ventas -->


  <vbox flex="1" class="xvbox"> 
    <hbox flex="0" >
      <caption class="box" label="<?php echo _("Comprobantes") ?>" />
    </hbox>
    

    <listbox id="busquedaVentas" contextmenu="AccionesBusquedaVentas" 
	     onclick="RevisarVentaSeleccionada()" flex="1"
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
	<listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolPlazoPago" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolFechaEntrega" collapsed="true"/>
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
	<listheader label="Fecha Registro" id="vlistFecha_Registro" collapsed="true"/>
	<listheader label="Fecha Emisión"/>
	<listheader label="Plazo Pago" id="vlistPlazoPago" collapsed="true"/>
	<listheader label="Total Importe"/>
	<listheader label="Importe Pendiente"/>
	<listheader label="Estado Documento"/>
	<listheader label="Fecha Entrega" id="vlistFechaEntrega" collapsed="true"/>
	<listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
      </listhead>

    </listbox>

    <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>
    <!-- Detalles -->
    <hbox class="box">
    <menu id="onlistDetalle" class="menuhead" collapsed="false" label="Detalle Comprobantes">
      <menupopup id="combolistDetalle">
        <menuitem id="t_detalle" type="checkbox" label="Detalle Comprobantes" checked="true" 
                  oncommand="mostrarDetalleVenta('comprobante')"></menuitem>
        <menuitem id="t_cobros" type="checkbox" label="Detalle Cobros" 
                  oncommand="mostrarDetalleVenta('cobros')"
                  <?php gulAdmite("Cobros") ?>></menuitem>
      </menupopup>
    </menu>
    </hbox>
    <vbox flex="1">
    <vbox id="boxDetalleComprobantes" flex="1">
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
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
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
      </listhead>
    </listbox>
    </vbox>
    <vbox id="boxDetalleCobros" collapsed="true" flex="1">
      <listbox id="busquedaDetallesCobroVenta" flex="1" contextmenu="AccionesBusquedaCobrosVenta" 
	       onclick="RevisarCobroSeleccionadaVenta()" 
	       onkeypress="if (event.keyCode==13) RevisarCobroSeleccionadaVenta()">
	<listcols flex="1">
	  <listcol/>
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
	  <listcol flex="0"/>
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;" />
	  <listheader label="Fecha Pago" />
	  <listheader label="Forma Pago"/>
	  <listheader label="Importe Pago" />
	  <listheader label="Local Pago" />
	  <listheader label="Usuario" />
	  <listheader label="" />
	</listhead>
      </listbox>
    </vbox>
    </vbox>
  </vbox>

  <hbox align="end" pack="end" id="btnsDevolucion" collapsed="true" style="padding-right: 1em;margin-top:.8em">
    <description value="Acciones Devolución:" style="font-size: 14px;margin: .7em;"/>
    <button class="btn" image="img/gpos_compras.png" label=" Seleccionar Todo" oncommand="seleccionarALLDetalle2Devolucion()"  />
    <button class="btn" image="img/gpos_vaciarcompras.png" label=" Cancelar" oncommand="checkDevolucionDetalle(true)" />
    <button class="btn" image="img/gpos_fincompras.png" label=" Devolver" oncommand="DevolverVentaSeleccionada()"/>
  </hbox>


<!-- Resumen -->
<vbox class="box" id="boxResumenComprobante" style="padding: 0 1em 0 1em">
  <caption class="box" label="<?php echo _("Resumen Comprobantes") ?>" />
  <hbox  class="resumen" pack="center" align="left">
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
</vbox>
<!-- /Resumen -->


  <button class="media btn"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false" id="btnreturndetventa"/>

    <!-- Formulario Fecha Emisión -->
    <panel id="panelFechaEmision" style="border:1px solid #aaa" align="center" class="box">
      <vbox class="box" flex="1" align="center">
	<vbox class="box" flex="1">
	  <caption id="wtitleFechaEmision"
                   class="h1"
		   label="<?php echo _("Fecha Emisión") ?>" />
	</vbox>

        <hbox flex="1">
        <vbox><spacer style="width:5px"/></vbox>
	<vbox align="center" flex="1">
	  <caption id="wtitleComprobanteVenta"
		   style="padding:0.4em;font-size: 12px;font-weight: bold;"		   
		   label="<?php echo _("Fecha Emisión") ?>" />
          <hbox>
            <datepicker id="dateFechaEmision" type="popup" />
            <timepicker id="timeFechaEmision" type="popup" />
          </hbox>
          <hbox align="center" id="hboxReservado" collapsed="true">
            <caption class="xbase" label="Reservado "/>
            <checkbox id="checkReserva" />
          </hbox>
          <spacer style="height:5px"/>
          <button id="btnGuardarFechaEmision" label=" Aceptar " image="img/gpos_guardar.png"
		   oncommand="" class="btn" flex="1"/>
            <spacer style="height:5px"/>
	</vbox>
        <vbox><spacer style="width:5px"/></vbox>
        </hbox>
      </vbox>
    </panel>

</vbox>

