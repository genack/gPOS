<?php
//include("tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Comprobantes Venta',$predata="",$css='');
StartJs($js='modulos/comprobanteventa/ventas.js?v=3.2');
?>

  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;  
  var esFinanzas = false;
  var Local   = new Object();
  Local.CodigoAutorizacion = new Array();
  Local.esAdmin = "<?php echo ( Admite('Precios'))? true:false; ?>";
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<popupset>
  <popup id="AccionesBusquedaVentas">
    <menu id="mheadImprimir" label="<?php echo _("Imprimir") ?>">
      <menupopup> 
        <menuitem label="<?php echo _("Comprobante") ?>" 
                  oncommand="ReimprimirVentaSeleccionada(1)"/>
        <menuitem id="mheadImprimirSuscripcion" label="<?php echo _("Suscripción") ?>"
                  oncommand="ImprimirSuscripcionSeleccionada()" collapsed="true"/>
      </menupopup>
    </menu>
     <menu id="mheadImprimirInt" label="Imprimir" <?php gulAdmite("Compras") ?> collapsed="true" >
      <menupopup>
	<menuitem label="Formato gPOS" oncommand="ReimprimirVentaSeleccionada(1)"/>
	<menuitem label="Formato Oficial"  oncommand="ReimprimirVentaSeleccionada(2)"/>
      </menupopup>
     </menu>
     <menuseparator />
     <menuitem label="<?php echo _("Código de Autorización")?>" oncommand="ckCodigoAutorizacion('ck',false)"  <?php gulAdmite("Administracion") ?> />
  </popup>
  <popup id="AccionesDetallesVentas" class="media">
    <menuitem id="VentaRealizadaDetalleNS" label="<?php echo _("Ver Números de Serie") ?>" 
              oncommand="verNSVentaSeleccionada()"/>
    <menuitem id="VentaRealizadaDetalleMProducto" label="<?php echo _("Ver Detalle Meta Producto") ?>" oncommand="verDetMPSeleccionada()"/>
  </popup> 
</popupset>
<!--  no-visuales -->


<vbox flex="1" class="box">
  <hbox pack="center">
    <caption class="h1">
      <?php echo _("Comprobantes Ventas") ?>
    </caption>
  </hbox>

  <!-- Busqueda Ventas -->
  <hbox flex="1" class="box" align="start" pack="center">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	  <menulist id="FiltroVentaLocal" label="FiltrosPedidosVentaLocal" oncommand="BuscarVentas()">
	    <menupopup id="combolocales">
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
	    <!-- menuitem value="devolucion" id="modoConsultaDevolucion" label="Devolucion" 
		 oncommand="BuscarVentas()" / -->
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxvUsuario" collapsed="true">
	<description>Usuario</description>
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
      <vbox id="vboxvForma_Venta" collapsed="true">
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
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
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
	<button id="btnbuscar" label=" Buscar "  class="btn"
	        image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
                oncommand="BuscarVentas()"/>
      </vbox>
    </hbox>

    <!-- Ventas -->
    <vbox flex="1">
      <hbox flex="0">
	<caption class="box" label="<?php echo _("Comprobantes Ventas") ?>" />
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
	  <listcol flex="1" collapsed="true"/>		
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolvFecha_Registro" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
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
	  <listcol/>				
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;"/>
	  <listheader label="Código" id="vlistvCodigo" collapsed="true"/>
	  <listheader label="OP" id="vlistvOP" collapsed="true"/>
	  <listheader label="Documento"/>
	  <listheader label="Numero" collapsed="true"/>
	  <listheader label="Serie-Nro"/>
	  <listheader label="Cliente"/>		
	  <listheader label="Fecha Registro" id="vlistvFecha_Registro" collapsed="true"/>
	  <listheader label="Fecha Emisión"/>
	  <listheader label="Plazo Pago" id="vlistvPlazoPago" collapsed="true"/>
	  <listheader label="Total Importe"/>
	  <listheader label="Importe Pendiente"/>
	  <listheader label="Estado Documento"/>
	  <listheader label="Fecha Entrega" id="vlistFechaEntrega" collapsed="true"/>
	  <listheader label="Usuario" id="vlistvUsuario" collapsed="true"/>
	  <listheader label=""/>
	</listhead>

      </listbox>


      <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>

      <!-- Detalles -->
      <hbox pack="left" class="box">
	<caption class="box" label=" <?php echo _("Detalle Comprobantes Ventas") ?>" />
      </hbox>

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
	  <listheader label="PV"/>
	  <listheader label="" />
	</listhead>

      </listbox>
    </vbox>

</vbox>


<!-- Resumen -->
<vbox class="box" id="boxResumenComprobante" >
  <caption class="box" label="<?php echo _("Resumen Comprobantes Ventas") ?>" />
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

  <script>//<![CDATA[
  var id = function(name) { return document.getElementById(name); }
  BuscarVentas();   
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";
   ?>

  //]]></script>


  <?php
    EndXul();
  ?>
