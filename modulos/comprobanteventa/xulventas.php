<?php
//include("tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="CompraVista" title="Establecer Precio Por Pedido" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/comprobanteventa/ventas.js"/>
  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;  
var esFinanzas = false;
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<popupset>
  <popup id="AccionesBusquedaVentas">
     <menuitem id="mheadImprimir" label="<?php echo _("Imprimir") ?>" oncommand="ReimprimirVentaSeleccionada()"/>
     <menuitem id="mheadImprimirSuscripcion" label="<?php echo _("Imprimir Suscripción") ?>" oncommand="ImprimirSuscripcionSeleccionada()" collapsed="true"/>
  </popup>
  <popup id="AccionesDetallesVentas" class="media">
    <menuitem id="VentaRealizadaDetalleNS" label="<?php echo _("Ver Números de Serie") ?>" oncommand="verNSVentaSeleccionada()"/>
    <menuitem id="VentaRealizadaDetalleMProducto" label="<?php echo _("Ver Detalle Meta Producto") ?>" oncommand="verDetMPSeleccionada()"/>
  </popup> 
</popupset>
<!--  no-visuales -->


<vbox>
  <spacer style="height:6px"/>
  <hbox pack="center">
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Comprobantes ") ?>


    </caption>
  </hbox>
  <spacer style="height:6px"/>

  <!-- Busqueda Ventas -->
  <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
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
        <datepicker id="FechaBuscaVentas" type="popup" onblur="BuscarVentas()"/>
      </vbox>
      <vbox>
	<description>Hasta:</description>
        <datepicker id="FechaBuscaVentasHasta" type="popup" onblur="BuscarVentas()"/>
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
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
          <menupopup >
	    <menuitem type="checkbox" label="Forma Venta" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>	 
	    <menuseparator />
	    <menuitem type="checkbox" label="Codigo" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
	    <menuitem type="checkbox" label="OP" checked="false"
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
	    <menuitem type="checkbox" label="Vendedor"  
                      oncommand = "mostrarBusquedaAvanzadaVenta(this);"/>
          </menupopup>
        </menu>
      </vbox>

      <vbox style="margin-top:.9em">
	<button id="btnbuscar" label=" Buscar "  
	        image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
                oncommand="BuscarVentas()"/>
      </vbox>
    </hbox>

    <!-- Ventas -->
    <vbox flex="1">
      <spacer style="height:5px"/>
      <hbox flex="1">
	<caption style="font-size:10px; font-weight: bold;" label="Comprobantes " />
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
	  <listcol flex="1" id="vlistcolVendedor" collapsed="true"/>	
	  <splitter class="tree-splitter" />
	  <listcol/>				
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;"/>
	  <listheader label="Código" id="vlistCodigo" collapsed="true"/>
	  <listheader label="OP" id="vlistOP" collapsed="true"/>
	  <listheader label="Documento"/>
	  <listheader label="Numero"  collapsed="true" />
	  <listheader label="Serie-Nro"/>
	  <listheader label="Cliente"/>		
	  <listheader label="Fecha"/>
	  <listheader label="Total Importe"/>
	  <listheader label="Importe Pendiente"/>
	  <listheader label="Estado Documento"/>
	  <listheader label="Usuario" id="vlistVendedor" collapsed="true"/>
	  <listheader label=""/>
	</listhead>

      </listbox>

      <!-- Detalles -->
      <spacer style="height:6px"/>
      <caption style="font-size:10px; font-weight: bold;" label="Detalle Comprobantes" />
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
