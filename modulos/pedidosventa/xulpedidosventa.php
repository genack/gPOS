<?php
//include("tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Pedidos Ventas',$predata="",$css='');
StartJs($js='modulos/pedidosventa/pedidosventa.js?v=3.1');
?>

  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;  
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->  
<?php include("pedidosventa.php"); ?>
<!--  no-visuales -->  

<hbox>	
  <html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
  <html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
  </html:div>
</hbox>	

<vbox flex="1" class="box">
  <hbox pack="center">
    <caption class="h1">
      <?php echo _("Pedidos") ?>
    </caption>
  </hbox>

  <hbox align="start" pack="center" >
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroPedidosVentaLocal" label="FiltrosPedidosVentaLocal" 
	          oncommand="BuscarPedidosVenta()">
	  <menupopup id="combolocales">
	    <menuitem value="0" label="Todos" />
	    <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroPedidosVentaLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>
    <vbox>
      <description>Desde:</description>
      <datepicker id="FechaBuscaPedidosVenta" type="popup" />
    </vbox>
    <vbox>
      <description>Hasta:</description>
      <datepicker id="FechaBuscaPedidosVentaHasta" type="popup" />
    </vbox>
    <vbox>
      <description>Pedidos</description>
      <menulist id="FiltroTipoPresupuesto" label="FiltrosTipoPresupuesto">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarPedidosVenta()"/>
	  <menuitem value="Proforma"  id="modoProforma" label="Proforma"   oncommand="BuscarPedidosVenta()"/>
	  <menuitem value="Preventa" id="modoPreventa" label="Preventa"  oncommand="BuscarPedidosVenta()" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxUsuario_Registro" collapsed="true">
      <description>Usuario</description>
      <menulist  id="IdUsuario" oncommand="BuscarPedidosVenta()">
	<menupopup>
	  <menuitem label="Todos" value="todos" selected="true"/>
	  <?php echo genXulComboUsuarios(false,"menuitem",$IdLocal) ?>
	</menupopup>
      </menulist>	
    </vbox>
    <vbox id="vboxTipo_Venta" collapsed="true">
      <description>Tipo Venta</description>
      <menulist id="FiltroTipoVenta" label="FiltrosTipoVenta">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarPedidosVenta()"/>
	  <menuitem value="VD"  id="modoVD" label="B2C"   oncommand="BuscarPedidosVenta()"/>
	  <menuitem value="VC" id="modoVC" label="B2B"  oncommand="BuscarPedidosVenta()" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxEstado" collapsed="true">
      <description>Estado</description>
      <menulist id="FiltroPresupuestoEstado" label="FiltrosPresupuestoEstado">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarPedidosVenta()"/>
	  <menuitem value="Pendiente" id="modoConsultaPendiente" label="Pendiente"  oncommand="BuscarPedidosVenta()" />
	  <menuitem value="Modificado" id="modoConsultaPedido" label="Modificado" oncommand="BuscarPedidosVenta()" />
	  <menuitem value="Confirmado" id="modoConsultaRecibido" label="Confirmado" oncommand="BuscarPedidosVenta()" />
	  <menuitem value="Vencido" id="modoConsultaBorrador" label="Vencido"  oncommand="BuscarPedidosVenta()" />
	  <menuitem value="Cancelado" id="modoConsultaCancelado" label="Cancelado" oncommand="BuscarPedidosVenta()" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="xboxCliente">
      <description>Cliente:</description>
      <textbox onfocus="select()" id="NombreClienteBusqueda" style="width: 18em"
               onkeyup="if (event.which == 13) BuscarPedidosVenta()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox id="vboxProducto" collapsed="true">
      <description>Producto:</description>
      <textbox onfocus="select()" id="NombreProductoBusqueda" style="width: 18em"
               onkeyup="if (event.which == 13) BuscarPedidosVenta()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Código</description>
      <textbox onfocus="select()" id="busquedaCodigoSerie" style="width: 11em"
               onkeyup="if (event.which == 13) BuscarPedidosVenta()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>

    <vbox style="margin-top:1em">
      <menu>
	<toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
	<menupopup >
	  <menuitem type="checkbox" label="Tipo Venta" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	  <menuitem type="checkbox" label="Estado" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Usuario Registro" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Producto" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	  <menuseparator />
	  <menuitem type="checkbox" label="Fecha Registro" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Adelanto" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	  <menuitem type="checkbox" label="Usuario" checked="false"
		    oncommand = "mostrarBusquedaAvanzada(this);"/>
	</menupopup>
      </menu>
    </vbox>

    <vbox style="margin-top:1.1em" class="btn">
      <button id="btnbuscar" label=" Buscar "  class="btn"
              image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
      oncommand="BuscarPedidosVenta()"/>
    </vbox>
  </hbox>
  <!--/vbox-->

  <vbox flex="1" id="listboxPedidosVenta" collapse="false">

    <hbox flex="0" >
      <caption class="box" label="<?php echo _("Pedidos") ?>" />
    </hbox>


    <listbox flex="1" id="busquedaPedidosVenta" contextmenu="AccionesBusquedaPedidosVenta" 
	     onkeypress="if (event.keyCode==13) RevisarPedidosVentaSeleccionada()"  
	     onclick="RevisarPedidosVentaSeleccionada()" 
	     ondblclick="ModificarPedidos()">
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
	<listcol flex="1" id="vlistcolTipo_Venta" collapsed="true"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>		
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolAdelanto" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>				
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolUsuario_Registro" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol/>
      </listcols>
      <listhead>
	<listheader label="#"/>
	<listheader label="Local"/>
	<listheader label="Código"/>
	<listheader label="Pedidos"/>
	<listheader label="Estado"/>
	<listheader label="Tipo Venta" id="vlistTipo_Venta" collapsed="true"/>	
	<listheader label="Fecha Registro" id="vlistFecha_Registro" collapsed="true"/>
	<listheader label="Fecha Atención"/>
	<listheader label="Vigencia"/>
	<listheader label="Descuento"/>
	<listheader label="Total Importe"/>
	<listheader label="Adelanto" id="vlistAdelanto" collapsed="true"/>
	<listheader label="Cliente"/>
	<listheader label="Usuario Registro" id="vlistUsuario_Registro" collapsed="true"/>
	<listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
	<listheader label=""/>
      </listhead>
    </listbox>

    <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>

    <hbox pack="left">
      <caption class="box" label=" <?php echo _("Detalle Pedidos") ?>" />
    </hbox>

    <listbox id="busquedaDetallesPedidosVenta" contextmenu="AccionesDetallesPedidosVenta"
	     onkeypress="if (event.keyCode==13) RevisarDetallePedidosVenta()" flex="1" 
	     onclick="RevisarDetallePedidosVenta()"
	     ondblclick="ModificarDetallePedidos()">
      <listcols flex="1">
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol flex="2"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />				
	<listcol />
	<splitter class="tree-splitter" />				
	<listcol />
	<splitter class="tree-splitter" />				
	<listcol flex="0"/>
	<splitter class="tree-splitter" />
	<listcol  flex="1"/>
      </listcols>
      <listhead>
	<listheader label="#" />
	<listheader label="REF" />
	<listheader label="CB" />
	<listheader label="Producto" />
	<listheader label="Detalle" />
	<listheader label="Cantidad" />
	<listheader label="Precio" />
	<listheader label="Descuento" />
	<listheader label="Importe" />
	<listheader label="" />
      </listhead>
    </listbox>
  </vbox>

  <!-- Formulario PedidosVenta -->
  <vbox style="margin-top:3em" id="formularioPedidosVenta" align="center"  pack="top" collapsed="true">
    <spacer flex="1"></spacer>
    <hbox pack="center">
      <caption class="box" label="<?php echo _("Modificar Pedido") ?>" />
    </hbox>
    <spacer style="height:1em"/>
    <hbox>
      <groupbox>
	<hbox>
	  <grid>
	    <rows>
	      <row id="lineaPedido1" class="xbase">
		<caption label="Pedido"/>
		<description id="xPedidosVenta" style="font-size:13px;"
			     size="20" onfocus="this.select()" readonly="true"/>
	      </row>
	      <row id="lineaPedido2" collapsed="true">
		<caption label="Pedido" />
		<menulist  id="xPedidosVenta" oncommand="ModificarPedidosVenta(1)">
		  <menupopup>
		    <menuitem label="Preventa" value="Preventa" collapsed="true"/> 
		    <menuitem label="Proforma" value="Proforma"/> 
		  </menupopup>
		</menulist>						
	      </row>
	      <row  class="xbase" >
		<caption  class="xbase" label="Código" />
		<description  class="xbase" id="xCodigo" readonly="true"/>
	      </row>
	      <row id="lineaLocalPedidos" collapsed="true">
		<caption label="Local"/>    
		<menulist  id="xPedidosVentaLocal" oncommand="ModificarPedidosVenta(2)">
		  <menupopup>
		    <?php echo genXulComboAlmacenes(false,"menuitem") ?>
		  </menupopup>
		</menulist>						
	      </row>
	      <!--row id="lineaEstado1">
		  <caption label="Estado"/>
		  <description id="xEstadoVenta" style="font-size:13px;"
		  size="20" onfocus="this.select()" readonly="true"/>
		  </row-->
	      <row id="lineaEstado2" collapsed="true">
		<caption label="Estado" />
		<menulist  id="xEstadoVenta" oncommand="ModificarPedidosVenta(9)">
		  <menupopup>
		    <menuitem id="mheadEstadoPte" label="Pendiente" value="Pendiente" /> 
		    <menuitem id="mheadEstadoCdo" label="Cancelado" value="Cancelado"/> 
		    <menuitem id="mheadEstadoVdo" label="Vencido" value="Vencido" /> 
		  </menupopup>
		</menulist>						
	      </row>
	      <row>
		<caption label="Cliente" />
		<box>
		  <toolbarbutton id="lClientHab" class="btn" oncommand="CogeCliente()" label="+"/>
		  <textbox class="media" id="ClientHab" readonly="true" flex="1"/>
		  <textbox  id="IdClientHab" value="1" collapsed="true"/>
		</box>
	      </row>
	      <row>
		<caption label="Vigencia" />
		<textbox class="media" id="xVigencia" 
			 onkeypress="return soloNumeros(event,this.value)"
			 onchange="ModificarPedidosVenta(4)"/>
	      </row>

	      <row>
		<caption label="Observaciones" />
		<textbox id="xObservacion" multiline="true" rows="2" 
			 style="width:15em;"
			 onpaste="return false"
			 onkeypress="return soloAlfaNumerico(event);"
			 onchange="ModificarPedidosVenta(5)"/>
	      </row>
	      <spacer style="height:8px"/>
	    </rows>
	  </grid>
	</hbox>
	<hbox flex="1">
	  <button id="btnvolveralmacen" class="media btn"
		  image="<?php echo $_BasePath; ?>img/gpos_volver.png" 
	  label=" Volver Pedidos Venta"
	  oncommand="volverPedidosVenta()" collapsed="false" flex="1"></button>
	</hbox>
      </groupbox>	
    </hbox>

    <spacer flex="1"></spacer>
  </vbox>

  <!-- Formulario Detalle PedidosVenta -->
  <vbox style="margin-top:3em" id="formularioDetallePedidosVenta" align="center"  pack="top" collapsed="true">
    <spacer flex="1"></spacer>
    <hbox pack="center">
      <caption class="box" label="<?php echo _("Modificar Detalle Pedido") ?>"/>
    </hbox>
    <spacer style="height:1em"/>
    <hbox pack="center">
      <description class="xtitulo" id="xPedidosVentadet" onfocus="this.select()" readonly="true"/>
    </hbox>
    <hbox pack="center">
      <description class="xproducto" id="xProducto" onfocus="this.select()" readonly="true"/>
    </hbox>
    <spacer style="height:1em"/>
    <hbox>
      <groupbox>
	<hbox>
	  <grid>
	    <rows>
	      <row class="xbase">
		<caption class="xbase" label="Detalle" />
		<description id="xDetalle" class="xbase" onfocus="this.select()" readonly="true"/>
	      </row>
	      <row id="noMenudeo"> 
		<caption label="Cantidad" />
		<row>
		  <textbox id="xCantidad"  size="15" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarPedidosVenta(6,true)"/>
		  <description class="xtext" id="xcUnidades" value="Unidades"/>
		</row>
	      </row>
	      
	      <row id="esMenudeo" collapsed="true">
		<caption id="xMenudeo" label="Cantidad" />
		<row>
		  <textbox id="xEmpaques"  size="15" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarPedidosVenta(6,true)"/>
		  <description class="xtext" id="xContenedor" value="Empaque"/>
		  <textbox id="xMenudencia"  size="5" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarPedidosVenta(6,true)"/>
		  <description class="xtext" id="xmUnidades" value="Unidades"/>
		</row>

	      </row>

	      <row>
		<caption label="Precio" />
		<row>
		  <textbox id="xPrecio"  size="15" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarPedidosVenta(7,true)"/>
		  <description  class="xtext" id="xMoneda" value="Soles"/>
		</row>
	      </row>
	      <spacer style="height:8px"/>
	    </rows>
	  </grid>
	</hbox>
	<hbox flex="1">
	  <button id="btnvolveralmacen" class="media btn"
		  image="<?php echo $_BasePath; ?>img/gpos_volver.png" 
	  label=" Volver Pedidos Ventas" flex="1"
	  oncommand="volverPedidosVenta()" collapsed="false"></button>
	</hbox>
      </groupbox>	
    </hbox>

    <spacer flex="1"></spacer>
  </vbox>

  <!-- NUmero Series  -->
  <vbox id="editandoSeries"  align="center" pack="top" collapsed="true"  style="margin-top:3em" >
    <spacer flex="1"/>

    <vbox  align="center"  pack="center" >
      <spacer style="height:0em"/>
      <caption class="box" label="<?php echo _("Modificar Número Serie - Detalle Pedido") ?>" />

      <spacer style="height:1.5em"/>
      <hbox pack="center">
	<description class="xproducto" id="xProductoNS" onfocus="this.select()" readonly="true"/>
      </hbox>

      <spacer style="height:.5em"/>
      <hbox pack="center">
	<description class="xtitulo" id="xCantidadNS" onfocus="this.select()" readonly="true"/>
      </hbox>

    </vbox>
    <spacer style="height:1em"/>

    <groupbox>
      <hbox>
	<grid>
	  <rows>
	    <row>
	      <caption label="Acciones"/>
	      <hbox>
		<radiogroup id="radio_group" oncommand="limpiar_caja()" > 
		  <hbox>
		    <radio label="Agregar"  disabled="true"/>
		    <radio label="Quitar" disabled="true" />
		    <radio label="Buscar" selected="true" />
		  </hbox>
		</radiogroup>
	      </hbox>
	    </row>
	    <row >
	      <caption label="Select NS"/>
	      <textbox id="ckserie" onkeypress="if (event.which == 13) selcKBoxSerie()"/>
	      <textbox id="selCB" collapsed="true"/>
	    </row>
	  </rows>
	</grid>
      </hbox>
      <listbox id="listaseries_presupuestos" >
	<listhead>
	  <listheader label="#" style="font-style:italic;" />
	  <listheader label="NS"/>
	</listhead>
	<listcols>
	  <listcol/>
	  <listcol flex="1"/>
	</listcols>
	<!-- listitem type="checkbox" -->
      </listbox>
      <hbox   flex="1">
	<grid>
	  <rows >
	    <row>
	      <description value="NS Stock"/>
	      <caption id="totalNS" label="0"/>
	    </row>
	    <row>
	      <description value="NS Selecciondos"/>
	      <caption id="totalSelNS" label="0"/>
	    </row>
	  </rows>
	</grid>
      </hbox>
      <hbox flex="1" pack="center" style="width:19.5em;">
	<button id="btnvolveralmacen" class="media btn" image="http://gpos.hark.net/img/gpos_volver.png" label=" Volver Pedidos Ventas" oncommand="volverPedidosVenta()" collapsed="false"></button>
      </hbox>
    </groupbox>	
    <spacer flex="1"/>
  </vbox>

</vbox>

<!-- Resumen -->
<vbox class="box">
  <caption class="box" label="<?php echo _("Resumen Pedidos") ?>" />
  <hbox  class="resumen" pack="center" align="left">
    <label value="Proforma:"/>
    <description id="TotalProforma" value="" />
    <label value="Preventa:"/>
    <description id="TotalPreventa" value="" />
    <label value="VD:"/>
    <description id="TotalVD" value="" />
    <label value="VC:"/>
    <description id="TotalVC" value="" />
    <label value="Pendientes:"/>
    <description id="TotalPendiente" value="" />
    <label value="Confirmados:"/>
    <description id="TotalConfirmado" value="" />
    <label value="Modificados:"/>
    <description id="TotalModificado" value="" />
    <label value="Vencidos:"/>
    <description id="TotalVencido" value="" />
    <label value="Cancelados:"/>
    <description id="TotalCancelado" value="" />
  </hbox>
</vbox>
<!-- /Resumen -->

  <script>//<![CDATA[

   VerPedidosVenta();
   
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";
   ?>

  //]]></script>


  <?php
    EndXul();
  ?>
