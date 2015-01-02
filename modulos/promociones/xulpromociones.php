<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="MovimientoVista" title="Movimientos Kardex"
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/promociones/promociones.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />

  <script>//<![CDATA[

  //var esAlmacen = "<?php echo $esAlmacenCentral?>";
   var cIdLocal   = "<?php echo $IdLocal?>";

  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->


<!-- Promociones -->
<vbox id="vboxPromociones">
  <!--  Encabezado-->
  <vbox> 
    <vbox pack="center" align="center">
      <caption id="wtitlePromociones" style="padding:-1em;font-size: 14px;font-weight: bold;"
	       label="<?php echo _("Promociones") ?>" />
    </vbox>
    <spacer style="height:4px"/>
  </vbox> 

  <!-- Cuerpo principal -->

  <vbox id="busquedaPromociones">
    <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
      <vbox>
	<?php if(getSesionDato("esAlmacenCentral")){?>
	<description>Local:</description>
	<hbox>
	  <menulist id="FiltroLocal" label="FiltrosMovimientoLocal"  
		    oncommand="BuscarPromocion();ocultarFormCambioLocal()">
	    <menupopup id="combolocales">
              <menuitem value="0" label="Todos"/>
              <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	      <!--?php echo genXulComboAlmacenes($IdLocal,"menuitem") ?-->
	    </menupopup>
	  </menulist>
	</hbox>
	<?php } else { ?>
	<textbox id="FiltroLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>
      </vbox>
      <vbox>
	<description>Desde:</description>
	<datepicker id="FechaBuscaPromocion" type="popup" onblur="BuscarPromocion()"/>
      </vbox>
      <vbox>
	<description>Hasta:</description>
	<datepicker id="FechaBuscaPromocionHasta" type="popup" onblur="BuscarPromocion()"/>
      </vbox>
      <vbox>
	<description>Promoción:</description>
	<textbox onfocus="select()" id="NombreBusqueda" style="width: 21em"
		 onkeyup="if (event.which == 13) BuscarPromocion()"
		 onkeypress="return soloAlfaNumerico(event);"/>
      </vbox>
      <vbox id="vboxEstado">
	<description>Estado:</description>
	<menulist  id="idEstadoPromocion" oncommand="BuscarPromocion()">
	  <menupopup>
	    <menuitem label="Todos" value="Todos" style="font-weight: bold" />
	    <menuitem label="Borrador" value="Borrador"/>
	    <menuitem label="Ejecucion" value="Ejecucion"/>
	    <menuitem label="Finalizado" value="Finalizado"/>
	    <menuitem label="Suspendido" value="Suspendido"/>
	    <menuitem label="Cancelado" value="Cancelado"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxTipo" collapsed="true">
	<description>Tipo:</description>
	<menulist  id="idTipoPromocion" oncommand="BuscarPromocion()">
	  <menupopup>
	    <menuitem label="Todos" value="Todos" style="font-weight: bold"/>
	    <menuitem label="Descuento" value="Descuento"/>
	    <menuitem label="Producto" value="Producto"/>
	    <menuitem label="Bono" value="Bono"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxTipo_Venta" collapsed="true">
	<description>Tipo Venta:</description>
	<menulist  id="idTipoVenta" oncommand="BuscarPromocion()">
	  <menupopup>
	    <menuitem label="Todos" value="Todos" style="font-weight: bold"/>
	    <menuitem label="B2C" value="VD"/>
	    <menuitem label="B2B" value="VC"/>
	  </menupopup>
	</menulist>
      </vbox>

      <vbox id="vboxModalidad" collapsed="true">
	<checkbox checked="true" id="modoPromocionMontoCompra"
		  label="Monto Compra" oncommand="BuscarPromocion()"/>
	<checkbox checked="true" id="modoPromocionHistorialCompra"
		  label="Historial Compra" oncommand="BuscarPromocion()"/>
      </vbox>
      <vbox style="margin-top:1.2em">
	<menu>
	  <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
	  <menupopup >
	    <menuitem type="checkbox" label="Tipo" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Tipo Venta" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Modalidad" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuseparator />
	    <menuitem type="checkbox" label="Local"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Categoria Cliente"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Registro" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Usuario" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	  </menupopup>
	</menu>
      </vbox>
      <vbox style="margin-top:.9em">
	<button id="btnbuscar" label=" Buscar " image="<?php echo $_BasePath; ?>img/gpos_buscar.png"
		oncommand="BuscarPromocion()"/>
      </vbox>
    </hbox>
  </vbox>

  <!-- Resumen Promociones  -->
  <vbox id="resumenPromociones" >
    <spacer style="height:5px"/>
    <hbox flex="1">
      <caption style="font-size:10px; font-weight: bold;" 
	       label="<?php echo _("Promociones") ?>" />
      <description id="TotalPromocion" value="0 Promociones listados" />
      <hbox  flex="1" pack="center">
	<label value="Borrador:"/>
	<description id="TotalBorrador" value=" 0"/>
	<label value="Ejecución:"/>
	<description id="TotalEjecucion" value=" 0 "/>
	<label value="Finalizado:"/>
	<description id="TotalFinalizado" value=" 0 "/>
	<label value="Cancelado:"/>
	<description id="TotalCancelado" value="0"/>
      </hbox>
    </hbox>
  </vbox>
</vbox>
<!-- Resumen Promociones  -->

<!-- Lista de Promociones-->
<listbox flex="1" id="listadoPromocion"
	 contextmenu="AccionesBusquedaPromocion" 
	 onkeypress="if (event.keyCode==13) RevisarPromocionSeleccionada()"
	 onclick="RevisarPromocionSeleccionada()">
  <listcols flex="1">
    <listcol/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolLocal" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolCategoria_Cliente" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolTipo_Venta" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolModalidad" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolTipo" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol/>
  </listcols>
  <listhead>
    <listheader label=" # " style="font-style:italic;"/>
    <listheader label="Local" id="vlistLocal" collapsed="true"/>
    <listheader label="Categoria Cliente" id="vlistCategoria_Cliente" collapsed="true"/>
    <listheader label="Promoción"/>
    <listheader label="Estado"/>
    <listheader label="Tipo Venta" id="vlistTipo_Venta" collapsed="true"/>
    <listheader label="Fecha Registro" id="vlistFecha_Registro" collapsed="true"/>
    <listheader label="Fecha Inicio"/>
    <listheader label="Fecha Fin"/>
    <listheader label="Modalidad" id="vlistModalidad" collapsed="true"/>
    <listheader label="Tipo" id="vlistTipo" collapsed="true"/>
    <listheader label="Importe Ticket" />
    <listheader label="CB Productos" />
    <listheader label="Descuento (%)" />
    <listheader label="Bono" />
    <listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
    <listheader label="" />
  </listhead>
</listbox>

  <!-- Lista de Promociones -->

  <!-- Formulario de Promociones -->
<vbox id="vboxFormPromocion" collapsed="true">
  <vbox align="center"  pack="top">
    <vbox>
      <spacer style="height:6px"/>
      <vbox pack="center" align="center">
	<caption id="wtitleFormPromociones"
		 style="padding:0.4em;font-size: 14px;font-weight: bold;"
		 label="<?php echo _("Nueva Promoción") ?>" />
      </vbox>
      <spacer style="height:8px"/>
    </vbox>
    <hbox>
      <vbox>
	<caption label="Promoción:"/>
	<grid>
	  <rows>
	    <row>
	      <description value="Nombre"/>
	      <textbox id="nombrePromocion" value=""
		       onchange="guardaPromocion()"
		       onkeypress="return soloAlfaNumerico(event);"/>
	    </row>
	    <row>
	      <description value="Modalidad"/>
	      <menulist id="FiltroCondicionPromocion" label="FiltrosCategoriaCliente"
			oncommand="SeleccionarCondicionPromocion(this.value);
				   guardaPromocion()">
		<menupopup id="combopromocion">
		  <menuitem label="Monto Compra" value="MontoCompra" selected="true"/>
		  <menuitem label="Historial Compra" value="HistorialCompra"/>
		</menupopup>
	      </menulist>
	    </row>
	    <row>
	      <description value="Tipo"/>
	      <menulist id="FiltroTipoPromocion" label="FiltrosTipoPromocion"
			oncommand="SeleccionarTipoPromocion(this.value);
				   guardaPromocion()">
		<menupopup id="combotipopromocion">
		  <menuitem label="Descuento" value="Descuento" selected="true"/>
		  <menuitem label="Producto" value="Producto"/>
		  <menuitem label="Bono" value="Bono"/>
		</menupopup>
	      </menulist>
	    </row>
	   <?php if(getSesionDato("esAlmacenCentral")){?>
	    <row >
	      <description value="Local"/>
	      <menulist id="FiltroDisponibilidadLocal"
			label="FiltrosDisponibilidadLocal"
			oncommand="guardaPromocion()">
		<menupopup id="combodisponibilidadlocal">
		  <menuitem label="Seleccionado" value="Actual" selected="true"/>
		  <menuitem label="Todos" value="0"/>
		</menupopup>
	      </menulist>
	    </row>
	    <?php } else{?>
	    <textbox id="FiltroDisponibilidadLocal" value="Actual" collapsed="true"/>
	    <?php }?>
	    <row>
	      <description value="Tipo Venta"/>
	      <menulist id="FiltroTipoVenta"
			label="FiltrosTipoVenta"
			oncommand="guardaPromocion()">
		<menupopup id="combotipoventa">
		  <menuitem label="B2C" value="VD" selected="true"/>
		  <menuitem label="B2B" value="VC"/>
		</menupopup>
	      </menulist>
	    </row>
	    <spacer style="height:1em"/>

	    <caption label="Periodo:"/>
	    <row>
	      <description value="Inicio"/>
	      <datepicker id="InicioPeriodoPromocion" type="popup" 
			  onchange="guardaPromocion()"/>
	    </row>
	    <row>
	      <description value="Fin"/>
	      <datepicker id="FinPeriodoPromocion" type="popup" 
			  onchange="guardaPromocion()"/>
	    </row>
	  </rows>
	</grid>
      </vbox>
      <spacer style="width:2em"/>
      <vbox>
	<caption label="Condición de Oferta Ticket:"/>
	<grid>
	  <rows>
	    <row id="rowMontoActualPromocion">
	      <description value="Importe Mayor a"/>
	      <textbox id="MontoActualPromocion" value="0" 
		       onchange="guardaPromocion()"
		       onkeypress="return soloNumerosEnteros(event,this.value);"/>
	    </row>
	    <row id="rowCategoriaCliente" collapsed="true">
	      <description value="Categoría Cliente"/>
	      <menulist id="FiltroCategoriaCliente" label="FiltrosCategoriaCliente"
			oncommand="guardaPromocion()">
	      </menulist>
	    </row>
	    <row id="rowPrioridadPromocion" collapsed="true">
	      <description value="Prioridad"/>
	      <menulist id="FiltroPrioridadPromocion" label="FiltrosPrioridadPromocion"
			oncommand="guardaPromocion()">
		<menupopup id="comboprioridadpromocion">
		  <menuitem label="Ninguna" value="0" selected="true"/>
		  <menuitem label="Baja"    value="1"/>
		  <menuitem label="Media"   value="2"/>
		  <menuitem label="Alta"    value="3"/>
		</menupopup>
	      </menulist>
	    </row>
	    <spacer style="height:1em"/>
	    <caption label="Oferta:"/>
	    <description id="descProductoOferta" value="Producto" collapsed="true"/>
	    <row id="rowProductoPromocion1" collapsed="true">
	      <description value="Código de Barras"/>
	      <textbox id="ProductoPromocion1" value=""
		       onkeypress="return soloNumerosEnteros(event,this.value);"
		       onchange="guardaPromocion()"/>
	      </row>
	    <row id="rowProductoPromocion2" collapsed="true">
	      <description value="Código de Barras"/>
	      <textbox id="ProductoPromocion2" value=""
		       onchange="guardaPromocion()"
		       onkeypress="return soloNumerosEnteros(event,this.value);"
		       placeholder="Alternativo"/>
	    </row>
	    <row id="rowDescuentoPromocion">
	      <description value="Descuento (%)"/>
	      <textbox id="DescuentoPromcion" value="0"
		       onkeypress="return soloNumeros(event,this.value);"
		       onchange="guardaPromocion()"/>
	    </row>
	    <row id="rowBonoPromocion" collapsed="true">
	      <description value="Bono"/>
	      <textbox id="BonoPromocion" value="0"
		       onkeypress="return soloNumeros(event,this.value);"
		       onchange="guardaPromocion()"/>
	    </row>
	    <spacer style="height:1em"/>
	    <caption label="Estado:"/>
	    <row id="rowEstadoPromocion">
	      <description value="Estado"/>
	      <menulist id="FiltroEstado" label="FiltrosEstado"
			oncommand="guardaPromocion()">
		<menupopup id="comboestado">
		  <menuitem id="itmEstadoBorrador" label="Borrador"
			    value="Borrador" selected="true"/>
		  <menuitem id="itmEstadoEjecucion" label="Ejecucion" value="Ejecucion"/>
		  <menuitem id="itmEstadoSuspendido" label="Suspendido"
			    value="Suspendido" collapsed="true"/>
		  <menuitem id="itmEstadoFinalizado" label="Finalizado"
			    value="Finalizado" collapsed="true"/>
		  <menuitem id="itmEstadoCancelado" label="Cancelado"
			    value="Cancelado" collapsed="true"/>
		</menupopup>
	      </menulist>
	    </row>
	  </rows>
	</grid>
      </vbox>
    </hbox>
  </vbox>
<!-- Formulario de Promociones -->

</vbox>
<!-- Cuerpo principal -->




<!-- Categoría Cliente -->
<vbox id="hboxPromocionClientes" style="margin-top:-1em" collapsed="true">
  <vbox flex="1" align="center" pack="center">
      <vbox pack="center" align="center">
	<caption id="wtitlePromocionClientes" 
		 style="padding:0.4em;font-size: 14px;font-weight: bold;"
		 label="<?php echo _("Categoría Clientes") ?>" />
      </vbox>
  </vbox>
  <vbox id="busquedaCategoriaCliente">
    <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
      <vbox>
	<description>Desde:</description>
	<datepicker id="FechaBuscaCategoria" type="popup"
		    onchange="BuscarPromocionCliente()"/>
      </vbox>
      <vbox>
	<description>Hasta:</description>
	<datepicker id="FechaBuscaCategoriaHasta" type="popup" 
		    onchange="BuscarPromocionCliente()"/>
      </vbox>
      <vbox>
	<description>Categoría Cliente:</description>
	<textbox onfocus="select()" id="CategoriaBusqueda" style="width: 21em"
		 onkeyup="if (event.which == 13) BuscarPromocionCliente()"
		 onkeypress="return soloAlfaNumerico(event);"/>
      </vbox>
      <vbox id="vboxEstadoCategoria">
	<description>Estado:</description>
	<menulist  id="idEstadoCategoria" oncommand="BuscarPromocionCliente()">
	  <menupopup>
	    <menuitem label="Todos" value="Todos" style="font-weight: bold" />
	    <menuitem label="Borrador" value="Borrador"/>
	    <menuitem label="Ejecucion" value="Ejecucion"/>
	    <menuitem label="Finalizado" value="Finalizado"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox style="margin-top:1.2em">
	<menu>
	  <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
	  <menupopup >
	    <menuitem type="checkbox" label="- Local"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" label="- Fecha Registro"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" label="- Descripcion"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" label="- Ventas Periodo"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" label="- Motivo Promocion" checked="false"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" label="- Usuario" checked="false"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	  </menupopup>
	</menu>
      </vbox>
      <vbox style="margin-top:.9em">
	<button id="btnbuscarCategoria" label=" Buscar " 
                image="<?php echo $_BasePath; ?>img/gpos_buscar.png"
		oncommand="BuscarPromocionCliente()"/>
      </vbox>
    </hbox>
  </vbox>
</vbox>

<listbox flex="1" id="listadoPromocionCliente" collapsed="true"
	 contextmenu="AccionesBusquedaPromocionCliente" 
	 onkeypress="if (event.keyCode==13) RevisarPromocionClienteSeleccionada()"
	 onclick="RevisarPromocionClienteSeleccionada()">
  <listcols flex="1">
    <listcol/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Local" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Fecha_Registro" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Descripcion" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Ventas_Periodo" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" />
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Motivo_Promocion" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol flex="1" id="vlistcol-_Usuario" collapsed="true"/>
    <splitter class="tree-splitter" />
    <listcol/>
  </listcols>
  <listhead>
    <listheader label=" # " style="font-style:italic;"/>
    <listheader label="Local" id="vlist-_Local" collapsed="true"/>
    <listheader label="Fecha Registro" id="vlist-_Fecha_Registro" collapsed="true"/>
    <listheader label="Categoria Cliente"/>
    <listheader label="Descripcion" id="vlist-_Descripcion" collapsed="true"/>
    <listheader label="Estado"/>
    <listheader label="Ventas Periodo" id="vlist-_Ventas_Periodo"/>
    <listheader label="Desde Monto Compra"/>
    <listheader label="Hasta Monto Compra" />
    <listheader label="Desde Cantidad Compra"/>
    <listheader label="Hasta Cantidad Compra"/>
    <listheader label="Motivo Promocion" id="vlist-_Motivo_Promocion" collapsed="true"/>
    <listheader label="Usuario" id="vlist-_Usuario" collapsed="true"/>
    <listheader label=""/>
  </listhead>
</listbox>

<vbox id="boxFormPromocionCliente" collapsed="true" align="center"  pack="top">
  <vbox>
    <spacer style="height:6px"/>
    <vbox pack="center" align="center">
      <caption id="wtitleFormPromocionesCliente"
	       style="padding:0.4em;font-size: 14px;font-weight: bold;"
	       label="<?php echo _("Nueva Categoría Cliente") ?>" />
    </vbox>
    <spacer style="height:8px"/>
  </vbox>
  <hbox>
    <vbox>
      <caption label="Categoría Cliente:"/>
      <grid>
	<rows>
	  <row id="vboxCategoriaCliente">
	    <description value="Categoría"/>
	    <textbox id="CategoriaCliente" value="" 
		     onkeypress="return soloAlfaNumerico(event);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="vboxMotivoPromocion" collapsed="false">
	    <description value="Historial Compra"/>
	    <menulist id="FiltroMotivoPromocion" label="FiltrosMotivoPromocion"
		      oncommand="seleccionarMotitvoPromocion(this.value);
				 guardaPromocionCliente()">
	      <menupopup id="combomotivopromocion">
		<menuitem label="Monto Compra" value="MontoCompra" selected="true"/>
		<menuitem label="Cantidad Compra" value="NumeroCompra"/>
		<menuitem label="Ambos" value="Ambos"/>
	      </menupopup>
	    </menulist>
	  </row>
	  <row id="vboxHistorialVentaPeriodo" collapsed="false">
	    <description value="Ventas Periodo"/>
	    <hbox>
	      <menulist id="FiltroHistorialVentaPeriodo" label="Seleccione..." value='0'
			editable="false"
			oncommand="guardaPromocionCliente()">
		<menupopup id="combohistorialperiodo">
		</menupopup>
	      </menulist>
	      <textbox id="textHistorialVentaPeriodo" value="" style="width:50px"
                       onkeypress="return soloNumerosEnteros(event,this.value);"
	               onchange="guardaHistorialVentaPeriodo()" collapsed="true"/>
	    </hbox>
	  </row>
	  <?php if(getSesionDato("esAlmacenCentral")){?>
	  <row >
	    <description value="Local"/>
	    <menulist id="DisponibilidadLocalCat"
		      label="FiltrosDisponibilidadLocalCat"
		      oncommand="guardaPromocionCliente()">
	      <menupopup id="combodisponibilidadlocalcat">
		<menuitem label="Seleccionado" value="Actual" selected="true"/>
		<menuitem label="Todos" value="0"/>
	      </menupopup>
	    </menulist>
	  </row>
	  <?php } else{?>
	  <textbox id="DisponibilidadLocalCat" value="Actual" collapsed="true"/>
	  <?php }?>
	  <row id="rowDescripcionCategoria" collapsed="false">
	    <description value="Descripción"/>
	    <textbox id="DescripcionCategoria" value=""
		     multiline="true" rows="2"
		     onkeypress="return soloAlfaNumerico(event);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	</rows>
      </grid>
    </vbox>
    <spacer style="width:2em"/>
    <vbox>
      <caption label="Condición Categoría:"/>
      <grid>
	<rows id="rowsMontoCompra">
	  <description id="descMontoCompra" value="Monto Compra Histórica"/>
	  <row id="rowMontoCompraDesde">
	    <description value="Desde"/>
	    <textbox id="MontoCompraDesde" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="rowMontoCompraHasta">
	    <description value="Hasta"/>
	    <textbox id="MontoCompraHasta" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <description id="descCantidadCompra" value="Cantidad Compra Histórica" />
	  <row id="rowCantidadCompraDesde">
	    <description value="Desde"/>
	    <textbox id="CantidadCompraDesde" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="rowCantidadCompraHasta">
	    <description value="Hasta"/>
	    <textbox id="CantidadCompraHasta" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <spacer style="height:1em"/>
	  <caption label="Estado:"/>
	  <row id="rowEstadoCategoriaCliente">
	    <description value="Estado"/>
	    <menulist id="FiltroEstadoCategoriaCliente" label="FiltrosEstadoCategoriaCliente"
		      oncommand="guardaPromocionCliente()">
	      <menupopup id="comboestadocategoriacliente">
		<menuitem id="itmEstadoBorradorCat" label="Borrador"
			  value="Borrador" selected="true"/>
		<menuitem id="itmEstadoEjecucionCat" label="Ejecucion" value="Ejecucion"/>
		<menuitem id="itmEstadoFinalizadoCat" label="Finalizado"
			  value="Finalizado" collapsed="true"/>
		<menuitem id="itmEstadoEliminadoCat" label="Eliminado"
			  value="Eliminado" collapsed="true"/>
	      </menupopup>
	    </menulist>
	  </row>
	</rows>
      </grid>
    </vbox>
  </hbox>
</vbox>
<!-- Categoría cliente -->



<spacer style="height:1em"/>
<vbox>
  <box flex="1"></box>
  <hbox flex="1">
    <button  flex="1" id="btnPromocion" style="font-weight: bold;font-size:11px;"
	     class="media" image="<?php echo $_BasePath; ?>img/gpos_promo.png"  
             label=" Nueva Promoción"
	     oncommand="mostrarFormPromocion('Nuevo')" <?php gulAdmite("Ventas") ?>  />
    <button  flex="1" id="btnCategoriaCliente" style="font-weight: bold;font-size:11px;"
	     class="media" image="<?php echo $_BasePath; ?>img/gpos_catcliente.png"  
	     label=" Categoría Cliente"
	     oncommand="mostrarPromocionCliente('CategoriaCliente')" 
             <?php gulAdmite("Ventas") ?> />
  </hbox>
</vbox>

<script>//<![CDATA[

   
   BuscarPromocion();
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";

      echo "comboCategoriaCliente('".$catcliente."')";
   ?>


//]]></script>


<?php
  EndXul();
?>
