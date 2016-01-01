<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Promociones',$predata="",$css='');
StartJs($js='modulos/promociones/promociones.js?v=1');
?>

  <script>//<![CDATA[

  //var esAlmacen = "<?php echo $esAlmacenCentral?>";
   var cIdLocal   = "<?php echo $IdLocal?>";

  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->


<!-- Promociones -->
<vbox id="vboxPromociones" class="box">
  <!--  Encabezado-->
  <vbox> 
    <vbox pack="center" align="center">
      <caption id="wtitlePromociones" class="h1"
	       label="<?php echo _("Promociones") ?>" />
    </vbox>
  </vbox> 

  <!-- Cuerpo principal -->

  <vbox id="busquedaPromociones" class="box">
    <hbox align="start" pack="center" >
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
	<datepicker id="FechaBuscaPromocion" type="popup" />
      </vbox>
      <vbox>
	<description>Hasta:</description>
	<datepicker id="FechaBuscaPromocionHasta" type="popup" />
      </vbox>
      <vbox>
	<description>Promoción:</description>
	<textbox onfocus="select()" id="NombreBusqueda" 
		 onkeyup="if (event.which == 13) BuscarPromocion()"
		 onkeypress="return soloAlfaNumerico(event);"/>
      </vbox>
      <vbox id="vboxEstado">
	<description>Estado:</description>
	<menulist  id="idEstadoPromocion" oncommand="BuscarPromocion()">
	  <menupopup>
	    <menuitem label="Todos" value="Todos" style="font-weight: bold" />
	    <menuitem label="Borrador" value="Borrador"/>
	    <menuitem label="Ejecución" value="Ejecucion"/>
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
	    <menuitem label="Personal" value="VD"/>
	    <menuitem label="Corporativo" value="VC"/>
	  </menupopup>
	</menulist>
      </vbox>

      <vbox id="vboxModalidad" collapsed="true">
	<checkbox checked="true" id="modoPromocionMontoCompra"
		  label="Monto Compra" oncommand="BuscarPromocion()"/>
	<checkbox checked="true" id="modoPromocionHistorialCompra"
		  label="Historial Compra" oncommand="BuscarPromocion()"/>
      </vbox>
      <vbox style="margin-top:1em">
	<menu>
	  <toolbarbutton style="min-height: 2.7em;"
                         image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
	  <menupopup >
	    <menuitem type="checkbox" value="Tipo" label="Tipo" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Tipo Venta" label="Tipo Venta" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Modalidad" label="Modalidad" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuseparator />
	    <menuitem type="checkbox" value="Local" label="Local"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Categoria Cliente" label="Categoría Cliente"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Fecha Registro" label="Fecha Registro" 
                      checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" value="Usuario" label="Usuario" checked="false"
		      oncommand = "mostrarBusquedaAvanzada(this);"/>
	  </menupopup>
	</menu>
      </vbox>
      <vbox style="margin-top:1.1em">
	<button id="btnbuscar" label=" Buscar " class="btn"
                image="<?php echo $_BasePath; ?>img/gpos_buscar.png"
		oncommand="BuscarPromocion()"/>
      </vbox>
    </hbox>
  </vbox>


  <vbox>
    <hbox flex="1">
      <caption class="box" label="<?php echo _("Promociones") ?>" />
    </hbox>
  </vbox>
</vbox>

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
    <listheader label="Categoría Cliente" id="vlistCategoria_Cliente" collapsed="true"/>
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

  <!-- Resumen Promociones  -->
  <vbox id="resumenPromociones" class="box">
      <caption class="box" label="<?php echo _("Resumen Promociones") ?>" />

      <hbox class="resumen" pack="center" align="left">
	<label value="Total:"/>
        <description id="TotalPromocion" value="0 Promociones listados" />
	<label value="Borrador:"/>
	<description id="TotalBorrador" value=" 0"/>
	<label value="Ejecución:"/>
	<description id="TotalEjecucion" value=" 0 "/>
	<label value="Finalizado:"/>
	<description id="TotalFinalizado" value=" 0 "/>
	<label value="Cancelado:"/>
	<description id="TotalCancelado" value="0"/>
      </hbox>
  </vbox>

  <!-- Formulario de Promociones -->
<vbox id="vboxFormPromocion" collapsed="true" class="box">
  <vbox align="center"  pack="top">
    <vbox>
      <vbox pack="center" align="center">
	<caption id="wtitleFormPromociones"
		 class="h1"
		 label="<?php echo _("Nueva Promoción") ?>" />
      </vbox>
    </vbox>
    <hbox>
      <vbox>
	<caption label="Promoción:"/>
	<grid>
	  <rows>
	    <row>
	      <caption label="    Nombre"/>
	      <textbox id="nombrePromocion" value=""
		       onchange="guardaPromocion()"
		       onkeypress="return soloAlfaNumerico(event);"/>
	    </row>
	    <row>
	      <caption label="    Modalidad"/>
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
	      <caption label="    Tipo"/>
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
	      <caption label="    Local"/>
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
	      <caption label="    Tipo Venta"/>
	      <menulist id="FiltroTipoVenta"
			label="FiltrosTipoVenta"
			oncommand="guardaPromocion()">
		<menupopup id="combotipoventa">
		  <menuitem label="Personal" value="VD" selected="true"/>
		  <menuitem label="Corporativo" value="VC"/>
		</menupopup>
	      </menulist>
	    </row>
	    <spacer style="height:1em"/>

	    <caption label="Periodo:"/>
	    <row>
	      <caption label="    Inicio"/>
	      <datepicker id="InicioPeriodoPromocion" type="popup" 
			  onchange="guardaPromocion()"/>
	    </row>
	    <row>
	      <caption label="    Fin"/>
	      <datepicker id="FinPeriodoPromocion" type="popup" 
			  onchange="guardaPromocion()"/>
	    </row>
	  </rows>
	</grid>
      </vbox>
      <spacer style="width:10em"/>
      <vbox>
	<caption label="Condición de Oferta Ticket:"/>
	<grid>
	  <rows>
	    <row id="rowMontoActualPromocion">
	      <caption label="    Importe Mayor a"/>
	      <textbox id="MontoActualPromocion" value="0" 
		       onchange="guardaPromocion()"
		       onkeypress="return soloNumerosEnteros(event,this.value);"/>
	    </row>
	    <row id="rowCategoriaCliente" collapsed="true">
	      <caption label="    Categoría Cliente"/>
	      <menulist id="FiltroCategoriaCliente" label="FiltrosCategoriaCliente"
			oncommand="guardaPromocion()">
	      </menulist>
	    </row>
	    <row id="rowPrioridadPromocion" collapsed="true">
	      <caption label="    Prioridad"/>
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
	      <caption label="    Código de Barras"/>
	      <textbox id="ProductoPromocion1" value=""
		       onkeypress="return soloNumerosEnteros(event,this.value);"
		       onchange="guardaPromocion()"/>
	      </row>
	    <row id="rowProductoPromocion2" collapsed="true">
	      <caption label="    Código de Barras"/>
	      <textbox id="ProductoPromocion2" value=""
		       onchange="guardaPromocion()"
		       onkeypress="return soloNumerosEnteros(event,this.value);"
		       placeholder="Alternativo"/>
	    </row>
	    <row id="rowDescuentoPromocion">
	      <caption label="    Descuento (%)"/>
	      <textbox id="DescuentoPromcion" value="0"
		       onkeypress="return soloNumeros(event,this.value);"
		       onchange="guardaPromocion()"/>
	    </row>
	    <row id="rowBonoPromocion" collapsed="true">
	      <caption label="    Bono"/>
	      <textbox id="BonoPromocion" value="0"
		       onkeypress="return soloNumeros(event,this.value);"
		       onchange="guardaPromocion()"/>
	    </row>
	    <spacer style="height:1em"/>
	    <caption label="Estado:"/>
	    <row id="rowEstadoPromocion">
	      <caption label="    Estado"/>
	      <menulist id="FiltroEstado" label="FiltrosEstado"
			oncommand="guardaPromocion()">
		<menupopup id="comboestado">
		  <menuitem id="itmEstadoBorrador" label="Borrador"
			    value="Borrador" selected="true"/>
		  <menuitem id="itmEstadoEjecucion" label="Ejecución" value="Ejecucion"/>
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
<vbox id="hboxPromocionClientes" collapsed="true" class="box">
  <vbox >
      <vbox pack="center" align="center">
	<caption id="wtitlePromocionClientes" 
		 class="h1"
		 label="<?php echo _("Categoría Clientes") ?>" />
      </vbox>
  </vbox>
  <vbox id="busquedaCategoriaCliente" class="box">
    <hbox align="start" pack="center" >
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
	    <menuitem label="Ejecución" value="Ejecucion"/>
	    <menuitem label="Finalizado" value="Finalizado"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox style="margin-top:1em">
	<menu>
	  <toolbarbutton style="min-height: 2.7em;" 
                         image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
	  <menupopup >
	    <menuitem type="checkbox" value="- Local" label="Local"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" value="- Fecha Registro" label="Fecha Registro"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" value="- Descripcion" label="Descripción"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" value="- Ventas Periodo" label="Ventas Periodo"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" value="- Motivo Promocion" label="Motivo Promoción" 
                      checked="false"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	    <menuitem type="checkbox" value="- Usuario" label="Usuario" checked="false"
		      oncommand = "BusquedaAvanzadaPromocionCliente(this);"/>
	  </menupopup>
	</menu>
      </vbox>
      <vbox style="margin-top:1.1em">
	<button id="btnbuscarCategoria" label=" Buscar " class="btn"
                image="<?php echo $_BasePath; ?>img/gpos_buscar.png"
		oncommand="BuscarPromocionCliente()"/>
      </vbox>
    </hbox>
  </vbox>
  <spacer style="height:5px" class="box"/>
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
    <listheader label="Categoría Cliente"/>
    <listheader label="Descripción" id="vlist-_Descripcion" collapsed="true"/>
    <listheader label="Estado"/>
    <listheader label="Ventas Periodo" id="vlist-_Ventas_Periodo"/>
    <listheader label="Desde Monto Compra"/>
    <listheader label="Hasta Monto Compra" />
    <listheader label="Desde Cantidad Compra"/>
    <listheader label="Hasta Cantidad Compra"/>
    <listheader label="Motivo Promoción" id="vlist-_Motivo_Promocion" collapsed="true"/>
    <listheader label="Usuario" id="vlist-_Usuario" collapsed="true"/>
    <listheader label=""/>
  </listhead>
</listbox>

<vbox id="boxFormPromocionCliente" collapsed="true" align="center" pack="top" class="box">
  <vbox>
    <spacer style="height:6px"/>
    <vbox pack="center" align="center">
      <caption id="wtitleFormPromocionesCliente"
	       class="h1"
	       label="<?php echo _("Nueva Categoría Cliente") ?>" />
    </vbox>
  </vbox>
  <hbox>
    <vbox>
      <caption label="Categoría Cliente:"/>
      <grid>
	<rows>
	  <row id="vboxCategoriaCliente">
	    <caption label="    Categoría"/>
	    <textbox id="CategoriaCliente" value="" 
		     onkeypress="return soloAlfaNumerico(event);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="vboxMotivoPromocion" collapsed="false">
	    <caption label="    Historial Compra"/>
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
	    <caption label="    Ventas Periodo"/>
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
	    <caption label="    Local"/>
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
	    <caption label="    Descripción"/>
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
	  <caption id="descMontoCompra" label="    Monto Compra Histórica"/>
	  <row id="rowMontoCompraDesde">
	    <caption label="    Desde"/>
	    <textbox id="MontoCompraDesde" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="rowMontoCompraHasta">
	    <caption label="    Hasta"/>
	    <textbox id="MontoCompraHasta" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <caption id="descCantidadCompra" label="    Cantidad Compra Histórica" />
	  <row id="rowCantidadCompraDesde">
	    <caption label="    Desde"/>
	    <textbox id="CantidadCompraDesde" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <row id="rowCantidadCompraHasta">
	    <caption label="    Hasta"/>
	    <textbox id="CantidadCompraHasta" value="0"
		     onkeypress="return soloNumerosEnteros(event,this.value);"
		     onchange="guardaPromocionCliente()"/>
	  </row>
	  <spacer style="height:1em"/>
	  <caption label="Estado:"/>
	  <row id="rowEstadoCategoriaCliente">
	    <caption label="    Estado"/>
	    <menulist id="FiltroEstadoCategoriaCliente" label="FiltrosEstadoCategoriaCliente"
		      oncommand="guardaPromocionCliente()">
	      <menupopup id="comboestadocategoriacliente">
		<menuitem id="itmEstadoBorradorCat" label="Borrador"
			  value="Borrador" selected="true"/>
		<menuitem id="itmEstadoEjecucionCat" label="Ejecución" value="Ejecucion"/>
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

<vbox class="box">
  <box flex="1"></box>
  <hbox flex="1">
    <button  flex="1" id="btnPromocion" style="font-weight: bold;font-size:11px;"
	     class="btn" image="<?php echo $_BasePath; ?>img/gpos_promo.png"  
             label=" Nueva Promoción"
	     oncommand="mostrarFormPromocion('Nuevo')" <?php gulAdmite("Ventas") ?>  />
    <button  flex="1" id="btnCategoriaCliente" style="font-weight: bold;font-size:11px;"
	     class="btn" image="<?php echo $_BasePath; ?>img/gpos_catcliente.png"  
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
