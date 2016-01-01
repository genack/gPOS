<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Pagos Proveedor',$predata="",$css='');
StartJs($js='modulos/pagoscobros/pagosproveedor.js?v=3.1');
StartJs($js='modulos/comprobanteventa/ventas.js?v=3.1');
?>
  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;
  var esFinanzas = true;
  var Local   = new Object();
  Local.CodigoAutorizacion  = new Array();
  Local.esAdmin = "<?php echo ( Admite('Precios'))? true:false; ?>";
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("pagosproveedor.php"); ?>
<!--  no-visuales -->

<!--  Encabezado -->
<vbox flex="1" id="boxComprobantesCompras" collapsed="false">
  
  <!--  Encabezado-->
  <hbox pack="center" class="box">
    <caption class="h1"><?php echo $tcbte ?></caption>
  </hbox>

  <!-- Busqueda Pagos -->
  <vbox flex="1" id="vboxbusquedapago" collapsed="false" class="box">

    <hbox align="start" pack="center" >
      <vbox>
	<?php if(getSesionDato("esAlmacenCentral")){?>
	<description>Local:</description>
	<hbox>
	  <menulist id="FiltroPagoLocal" label="FiltrosPagoLocal" oncommand="BuscarPago()">
	    <menupopup id="combolocales">
	      <menuitem value="0" label="Todos"/>
	      <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	    </menupopup>
	  </menulist>
	</hbox>
        <?php } else { ?>
        <textbox id="FiltroPagoLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
        <?php } ?>	  
      </vbox>

      <vbox id="vboxFecha_Pago" collapsed="true">
	<description>Fecha:</description>
	<menulist id="FiltroFecha" label="FiltrosPago">
	  <menupopup>
	    <menuitem value="Registro" label="Registro" selected="true"   oncommand="BuscarPago()"/>
	    <menuitem value="Facturacion"  label="Factuación"  oncommand="BuscarPago()" />
	    <menuitem value="Pago"     label="Pago" oncommand="BuscarPago()" />
	  </menupopup>
	</menulist>
      </vbox>
      <vbox>
	<description value="Desde:"/>
	<datepicker id="FechaBuscaPago" type="popup" />
      </vbox>
      <vbox>
	<description value="Hasta:"/>
	<datepicker id="FechaBuscaPagoHasta" type="popup" />
      </vbox>
      <vbox>
	<description>Documento:</description>
	<menulist id="FiltroPagoDocumento" label="FiltrosPagoDocumentos">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarPago()"/>
	    <menuitem value="Factura" id="modoConsultaFactura" label="Factura"  oncommand="BuscarPago()" />
	    <menuitem value="Boleta" id="modoConsultaBoleta" label="Boleta"  oncommand="BuscarPago()" />
	    <menuitem value="Ticket" id="modoConsultaTicket" label="Ticket" oncommand="BuscarPago()" />
	    <menuitem value="AlbaranInt" id="modoConsultaAlbaranInt" label="Albarán Int." oncommand="BuscarPago()" />
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxEstado" collapsed="true">
	<description>Estado:</description>
	<menulist id="FiltroPago" label="FiltrosPagos">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarPago()"/>
	    <menuitem value="Pendiente" id="modoConsultaPendiente" label="Pendiente"  oncommand="BuscarPago()" />
	    <menuitem value="Empezada" id="modoConsultaEmpezada" label="Empezada" oncommand="BuscarPago()" />
	    <menuitem value="Pagada" id="modoConsultaPagada" label="Pagada" oncommand="BuscarPago()" />
	    <menuitem value="Vencida" id="modoConsultaVencida" label="Vencida" oncommand="BuscarPago()" />

	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxMoneda" collapsed="true">
	<description>Moneda:</description>
	<menulist id="FiltroPagoMoneda" label="FiltrosPagoMoneda" oncommand="BuscarPago()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true" />
            <?php echo genXulComboMoneda(false,"menuitem") ?>
	    <menuitem value="todo1" label="Local"  />
	  </menupopup>
	</menulist>
      </vbox>

      <vbox id="vboxForma_Pago" collapsed="true">
	<description>Forma Pago:</description>
	<menulist id="FiltroFormaPago" label="FiltrosFormaPago" oncommand="BuscarPago()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true" />
	    <menuitem value="Contado" id="modoConsultaPagoContado" label="Contado"/>
	    <menuitem value="Crédito" id="modoConsultaPagoCredito" label="Crédito"/>
	  </menupopup>
	</menulist>
      </vbox>

      <vbox>
	<description>Proveedor:</description>
	<textbox onfocus="select()" id="NombreProveedorBusqueda" 
	         onkeyup="if (event.which == 13) BuscarPago()" 
	         onkeypress="return soloAlfaNumerico(event);" />
      </vbox>
      <vbox>
	<description>Código:</description>
	<textbox onfocus="select()" id="busquedaCodigoSeriep" style="width: 11em"
                 onkeyup="if (event.which == 13)  BuscarPago()" 
	         onkeypress="return soloNumericoCodigoSerie(event);"/>
      </vbox>

      <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton style="min-height: 2.7em;" 
	                 image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
          <menupopup >
	    <menuitem type="checkbox" value="Fecha Pago" label="Fecha Pago" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Estado" label="Estado" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	
	    <menuitem type="checkbox" value="Moneda" label="Moneda" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	      
	    <menuitem type="checkbox" value="Forma Pago" label="Forma Pago" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuseparator />
	    <menuitem type="checkbox" value="Fecha Emision" label="Fecha Emisión" 
                      checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Percepcion" label="Percepción" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Flete" label="Flete" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Usuario" label="Usuario"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Observacion" label="Observación" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
          </menupopup>
        </menu>
      </vbox>

      <vbox style="margin-top:1.1em">
	<button id="btnbuscar" label=" Buscar " class="btn"
                image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
                oncommand="BuscarPago()"/>
      </vbox>
    </hbox>

    <vbox flex="1" id="listboxComprobantesPagos" collapsed="false">
      <hbox flex="0">
	<caption class="box" label="<?php echo _("Comprobantes") ?>" />
      </hbox>

      <listbox flex="1" id="busquedaPago" contextmenu="AccionesBusquedaPago"
	       onkeypress="if (event.keyCode==13) RevisarPagoSeleccionada()"  
	       onclick="RevisarPagoSeleccionada()" ondblclick="NuevoPago()">
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
	  <listcol flex="1" id="vlistcolFecha_Emision" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolPercepcion" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolFlete" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolObservacion" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="0"/>
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;"/>
	  <listheader label="Código"/>
	  <listheader label="Documento"/>
	  <listheader label="Proveedor"/>
	  <listheader label="Estado Pago"/>
	  <listheader label="Forma Pago"/>
	  <listheader label="Fecha Emisión" id="vlistFecha_Emision" style="text-align:center" collapsed="true"/>
	  <listheader label="Fecha Pago" style="text-align:center"/>      
	  <listheader label="Impuesto" style="text-align:center"/>
	  <listheader label="Percepción" id="vlistPercepcion" style="text-align:center" collapsed="true"/>
	  <listheader label="Flete" id="vlistFlete" style="text-align:center" collapsed="true"/>
	  <listheader label="Total Neto" style="text-align:center"/>
	  <listheader label="Total a Pagar" style="text-align:center"/>
	  <listheader label="Pendiente" style="text-align:center"/>
	  <listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
	  <listheader label="Obs." id="vlistObservacion" collapsed="true"/>
	  <listheader label=""/>
	</listhead>
      </listbox>
      <splitter collapse="none" resizeafter="farthest" orient="vertical">• • •</splitter>
      <hbox pack="left">
	<caption class="box" label="<?php echo _("Detalle Pagos") ?>"></caption>
	<hbox flex="1" pack="center">
	  <!--label value="Pendientes:"/>
	  <description id="TotalPendientes" value="" />
	  <label value="Confirmadas:"/>
	  <description id="TotalConfirmada" value="" />
	  <label value="Pendiente:"/>
	  <description id="ImportePendiente" value="" />
	  <label value="Pagada:"/>
	  <description id="ImportePagada" value="" />
	  <label value="Mora:"/>
	  <description id="ImporteMora" value="" />
	  <label value="Excedente:"/>
	  <description id="ImporteExcedente" value="" /-->
	</hbox>
	<hbox style="margin-top:-1em;margin-right:0.5em;">
	  <menu>
	    <toolbarbutton style="min-height: 2.7em;" 
	                   image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
	    <menupopup >
	      <menuitem type="checkbox" value="- Fecha Registro" label="Fecha Registro" 
                        checked="false"
			oncommand = "mostrarBusquedaAvanzada(this);"/>
	      <menuitem type="checkbox" value="- Usuario" label="Usuario"  
			oncommand = "mostrarBusquedaAvanzada(this);"/>
	      <menuitem type="checkbox" value="- Observacion" label="Observación" 
                        checked="false"
			oncommand = "mostrarBusquedaAvanzada(this);"/>
	    </menupopup>
	  </menu>
	</hbox>
      </hbox>

      <listbox id="busquedaDetallesPago" flex="1" contextmenu="AccionesDetallesPago" 
	       onclick="RevisarDetallePago()" ondblclick="EditarPago()" 
               onkeypress="if (event.keyCode==13) EditarPago()">
	<listcols flex="1">
	  <listcol/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcol-_Fecha_Registro" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	   <!--listcol flex="1"/>
	  <splitter class="tree-splitter" /-->
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol  flex="1" id="vlistcol-_Usuario" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol  flex="1" id="vlistcol-_Observacion" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="0"/>
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;" />
	  <listheader label="Documento" />
	  <listheader label="Estado"/>
	  <listheader label="Forma Pago"/>
	  <listheader label="Fecha Registro" id="vlist-_Fecha_Registro" collapsed="true"/>
	  <listheader label="Fecha Límite" />
	  <listheader label="Fecha Pago" />
	  <listheader label="Importe Pago" style="text-align:center"/>
	  <listheader label="Mora" style="text-align:center"/>
	  <!--listheader label="Excedente" style="text-align:center"/-->
	  <listheader label="Estado Cuota" />
	  <listheader label="Usuario" id="vlist-_Usuario" collapsed="true"/>
	  <listheader label="Obs" id="vlist-_Observacion" collapsed="true"/>
	  <listheader label="" />
	</listhead>

      </listbox>
    </vbox>
      <vbox collapsed="false" class="box" id="boxResumenComprobanteCompra">
	<caption class="box" label="<?php echo _("Resumen Comprobantes") ?>" />
	<hbox class="resumen" pack="center" align="left">
	  <label value="Comprobantes:"/>
	  <description id="TotalFactura" value="" />
	  <label value="Pendientes:"/>
	  <description id="TotalFacturaePendiente" value="" />
	  <label value="Empezadas:"/>
	  <description id="TotalFacturaeEmpezada" value="" />
	  <label value="Pagadas:"/>
	  <description id="TotalFacturaePagada" value="" />
	  <label value="Vencidas:"/>
	  <description id="TotalFacturaeVencida" value="" />
	  <label value="Impuesto:"/>
	  <description id="TotalImpuesto" value="" />
	  <label value="Percepción:"/>
	  <description id="TotalPercepcion" value="" />
	  <label value="Total a Pagar:"/>
	  <description id="TotalImporte" value="" />
	  <label value="Pendiente:"/>
	  <description id="TotalPendiente" value="" />
	</hbox>
      </vbox>

      <vbox>
	<box flex="1"></box>
	<button  id="btnIrPagosProveedor" 
		 class="btn" image="<?php echo $_BasePath; ?>img/gpos_tpvmultipagos.png"  
	         label=" Pagos Proveedor" 
		 oncommand="mostrarOperacionesPagosProveedor('pagos')" 
                 <?php gulAdmite("Pagos") ?> />
      </vbox>
  </vbox>


  <!-- Busqueda Pagos -->
  <vbox flex="1" id="vboxbusquedacobros" collapsed="true">

    <!-- Busqueda Ventas -->
    <hbox align="start" pack="center" class="box">
      <vbox>
	<?php if(getSesionDato("esAlmacenCentral")){?>
	<description>Local:</description>
	<hbox>
	  <menulist id="FiltroVentaLocal" label="FiltrosPedidosVentaLocal" oncommand="BuscarVentas()">
	    <menupopup id="combolocalselect">
	      <menuitem value="0" label="Todos" />
	      <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	    </menupopup>
	  </menulist>
	</hbox>
	<?php } else { ?>
	<textbox id="FiltroVentaLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
	<?php } ?>	  
      </vbox>

      <vbox>
	<description>Desde:</description>
        <datepicker id="FechaBuscaVentas" type="popup" />
      </vbox>
      <vbox>
	<description>Hasta:</description>
        <datepicker id="FechaBuscaVentasHasta" type="popup" />
      </vbox>
      <vbox>
	<description>Documento:</description>
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
	    <!-- menuitem value="devolucion" id="modoConsultaDevolucion" label="Devolucion" 
		 oncommand="BuscarVentas()" / -->
	  </menupopup>
	</menulist>
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
      <vbox id="vboxvUsuario" collapsed="true">
        <description>Usuario:</description>
	<menulist  id="IdUsuario" oncommand="BuscarVentas()">
	  <menupopup>
	    <menuitem label="Todos" value="todos" selected="true"/>
	    <?php echo genXulComboUsuarios(false,"menuitem",$IdLocal) ?>
	  </menupopup>
        </menulist>	
      </vbox>
      <vbox id="vboxvTipo_Producto" collapsed="true">
        <description>Tipo Producto:</description>
        <menulist  id="TipoProducto" oncommand="BuscarVentas()">
	  <menupopup>
	    <menuitem label="Todos" value="todos" selected="true"/>
	    <menuitem label="Producto" value="Producto" />
	    <menuitem label="Servicio" value="Servicio" />
	  </menupopup>
        </menulist>
      </vbox>
      <hbox id="vboxvForma_Venta" collapsed="true">
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
      <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton style="min-height: 2.7em;" 
                         image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
          <menupopup >
	    <menuitem type="checkbox" label="Usuario" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>	 
	    <menuitem type="checkbox" label="Forma Venta" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>	 
	    <menuitem type="checkbox" label="Tipo Producto" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>	 
	    <menuseparator />
	    <menuitem type="checkbox" label="Codigo" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
	    <menuitem type="checkbox" label="OP" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
	    <menuitem type="checkbox" label="Fecha Registro" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
          </menupopup>
        </menu>
      </vbox>

      <vbox style="margin-top:1.1em">
	<button id="btnbuscar" label=" Buscar " class="btn"
                image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
                oncommand="BuscarVentas()"/>
      </vbox>
    </hbox>

    <!-- Ventas -->
    <vbox id="vboxListaVentas" flex="1" class="box">
      <hbox>
	<caption class="box" label="Comprobantes Ventas" />
      </hbox>

      <listbox flex="1" id="busquedaVentas" contextmenu="AccionesBusquedaVentas" 
	       onclick="RevisarVentaSeleccionada()" 
	       onkeypress="if (event.keyCode==13) RevisarVentaSeleccionada()">
	<listcols flex="1">
	  <listcol/>		
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvCodigo" collapsed="true"/>		
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvOP" collapsed="true"/>		
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>		
	  <splitter class="tree-splitter" />
	  <listcol  collapsed="true"/>
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvFecha_Registro" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvPlazoPago" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>		
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>				
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolFechaEntrega" collapsed="true"/>
           <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvUsuario" collapsed="true"/>	
	  <splitter class="tree-splitter" />
	  <listcol />				
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;"/>
	  <listheader label="Código" id="vlistvCodigo" collapsed="true"/>
	  <listheader label="OP" id="vlistvOP" collapsed="true"/>
	  <listheader label="Documento"/>
	  <listheader label="Número"  collapsed="true" />
	  <listheader label="Serie-Nro"/>
	  <listheader label="Cliente"/>		
	  <listheader label="Fecha Registro" id="vlistvFecha_Registro" collapsed="true"/>
	  <listheader label="Fecha Emisión"/>
	  <listheader label="Plazo Pago" id="vlistvPlazoPago" collapsed="true"/>
	  <listheader label="Total Importe"/>
	  <listheader label="Total Pendiente"/>
	  <listheader label="Estado Documento"/>
	  <listheader label="Fecha Entrega" id="vlistFechaEntrega" collapsed="true"/>
	  <listheader label="Usuario" id="vlistvUsuario" collapsed="true"/>
	  <listheader label=""/>
	</listhead>

      </listbox>
      <splitter collapse="none" resizeafter="farthest" orient="vertical">• • •</splitter>
      <!-- Detalles Cobros -->
      <hbox id="hboxDetallesCobro" pack="left">
	<caption class="box" label="<?php echo _("Detalle Cobros") ?>"></caption>
      </hbox>
      <listbox id="busquedaDetallesCobro" flex="1" contextmenu="AccionesBusquedaCobros" 
	       onclick="RevisarCobroSeleccionada()" 
	       onkeypress="if (event.keyCode==13) RevisarCobroSeleccionada()">
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
      <vbox class="box" id="boxResumenComprobanteVenta" collapsed="true">
	<caption class="box" label="Comprobantes Ventas" />
	  <hbox class="resumen" pack="center" align="left">
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
  </vbox>
</vbox>

<hbox id="formAbonarComprobantesVentas" collapsed="true" pack="center" class="box">
  <box  align="center" pack="center">
    <spacer flex="1" />
    <deck>
      <groupbox>
	<hbox pack="center" align="center">
	  <caption  class="h1" label="Abonar Comprobante"/>
	</hbox>

	<hbox pack="center" align="center">
	  <caption  id="abono_numTicket" label=""/>
	</hbox>
        <spacer style="height:0.5em"/>
	<vbox>
	  <hbox>
	    <grid>
	      <column>
		<column></column>
		<column></column>
	      </column>
	      <rows>
		<row>
		  <caption  label="DEBE:" align="center"/>
		  <textbox  id="abono_Debe" value="0.00"
			    style="color:ref;font-weight:bold;width:10em" readonly="true"/>
		</row>
		<row>
		  <caption label="ABONA:" align="center"/>
		  <textbox id="abono_nuevo"  value="0" readonly="true"/>
		</row>
		<row>
		  <caption label="PENDIENTE:" align="center"/>
		  <textbox id="abono_Pendiente" value="0" readonly="true"/>
		</row>
		<row >
		  <caption label="EFECTIVO:" align="center" />
		  <textbox id="abono_Efectivo"   value="0"
			    onkeyup="ActualizaPeticionAbono()"
			    onkeypress="return soloNumeros(event,this.value);" />
		</row>
	      </rows>
	    </grid>
	    <spacer style="width: 2em"/>
	    <grid>
	      <column>
		<column></column>
		<column></column>
	      </column>
	      <rows>
		<row id="rowPlazoPago">
		  <caption label="PLAZO PAGO:" align="center"/>
		  <datepicker type="popup" id="plazo_pago"
			      onblur="ModificarEstadoPago('1')"/>
		</row>
		<row id="rowEstadoCobranza">
		  <caption label="ESTADO COBRANZA:" align="center"/>
		  <menulist id="estado_cobranza" 
			    label="Estado Cobranza" oncommand="ModificarEstadoPago('2')">
		    <menupopup>
		      <menuitem value="Ninguno" label="Ninguno" collapsed="true"/>
		      <menuitem value="Pendiente" label="Pendiente"  />
		      <menuitem value="Prorroga" label="Prórroga" />
		      <menuitem value="Coactivo" label="Coactivo" />
		    </menupopup>
		  </menulist>
		</row>
		<row id="rowObservaciones">
		  <caption  label="OBSERVACIONES:" align="center"/>
		  <textbox  id="observaciones" style="width:10em"
			    multiline="true" rows="1" onchange="ModificarEstadoPago('3')"/>
		</row>
	      </rows>
	    </grid>
	  </hbox>
	  <spacer style="height: 1em"/>
	  <hbox>
	    <button  class="btn" flex="1"  label=" Cancelar "
		     image="<?php echo $_BasePath; ?>img/gpos_cancelar.png"
		     oncommand="VolverCobros()"/>
	    <button  class="btn" flex="1"
		     image="<?php echo $_BasePath; ?>img/gpos_imprimir.png"
		     label=" Abonar " oncommand="RealizarAbono()"/>
	  </hbox>
	</vbox>
      </groupbox>
    </deck>
  </box>
</hbox>


<!-- Formulario Pago -->
<hbox flex="1" id="boxDetPago"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webDetPago" name="webDetPago" class="AreaDetPago"  src="about:blank" flex="1"/>
</hbox>

<hbox flex="1" id="boxPagoProveedor"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webPagoProveedor" name="webPagoProveedor" class="AreaPagoProveedor"  
           src="about:blank" flex="1"/>
</hbox>

  <script>//<![CDATA[
   <?php
     echo "mostrarComprobantes('".$xval."');";
     if($locales)
       if(getSesionDato("esAlmacenCentral")){
	 echo "iniComboLocales('".$locales."');";
	 echo "iniComboLocalSel('".$locales."');";
       }
   ?>
  //]]>

  </script>

  <?php
    EndXul();
  ?>
