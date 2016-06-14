<?php
//include("tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Compras Pedidos',$predata="",$css='');
StartJs($js='modulos/ordencompra/ordencompra.js?v=3.1.1');
?>

  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;  
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->  
<?php include("ordencompra.php"); ?>
<!--  no-visuales -->  

      <hbox>	
	<html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
	<html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
	</html:div>
      </hbox>	

<!-- Encabezado -->
  <hbox pack="center" class="box" id="boxTitlePedido">
    <caption class="h1">
      <?php echo _("Pedidos") ?>
    </caption>
  </hbox>
<!-- Encabezado -->

<vbox flex="1" class="box">
  <hbox align="start" pack="center" id="boxBusquedaOrdenCompra">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroOrdenCompraLocal" label="FiltrosOrdenCompraLocal" oncommand="BuscarOrdenCompra()">
	  <menupopup id="combolocales" >
	    <menuitem value="0" label="Todos"/>
	    <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroOrdenCompraLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>
      <vbox id="vboxFecha_Entrega" collapsed="true">
        <description value="Fecha:"/>
	<menulist id="FiltroFecha" label="FiltrosOrdenCompra">
	  <menupopup>
	    <menuitem value="Registro" label="Registro" selected="true" oncommand="BuscarOrdenCompra()"/>
	    <menuitem value="Entrega"  label="Entrega"  oncommand="BuscarOrdenCompra()" />
	  </menupopup>
	</menulist>	 
      </vbox>
      <vbox>
	<description>Desde:</description>
	<datepicker id="FechaBuscaOrdenCompra" type="popup" />
      </vbox>
      <vbox>
	<description>Hasta:</description>
	<datepicker id="FechaBuscaOrdenCompraHasta" type="popup" />
      </vbox>
      <vbox id="vboxEstado" collapsed="true">
	<description>Estado:</description>
	<menulist id="FiltroOrdenCompra" label="FiltrosOrdenCompra" >
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarOrdenCompra()"/>
	    <menuitem value="Borrador" id="modoConsultaBorrador" label="Borrador"  oncommand="BuscarOrdenCompra()" />
	    <menuitem value="Pendiente" id="modoConsultaPendiente" label="Pendiente"  oncommand="BuscarOrdenCompra()" />
	    <menuitem value="Pedido" id="modoConsultaPedido" label="Pedido" oncommand="BuscarOrdenCompra()" />
	    <menuitem value="Recibido" id="modoConsultaRecibido" label="Recibido" oncommand="BuscarOrdenCompra()" />
	    <menuitem value="Cancelado" id="modoConsultaCancelado" label="Cancelado" oncommand="BuscarOrdenCompra()" />
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxMoneda" collapsed="true">
	<description>Moneda:</description>
	<menulist id="FiltroOrdenCompraMoneda" label="FiltrosOrdenCompraMoneda"
                  oncommand="BuscarOrdenCompra()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"/>
            <?php echo genXulComboMoneda(false,"menuitem") ?>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox>
	<description>Proveedor:</description>
	<textbox onfocus="select()" id="NombreProveedorBusqueda" 
                 onkeyup="if (event.which == 13) BuscarOrdenCompra()" 
                 onkeypress="return soloAlfaNumerico(event);"/>
      </vbox>
      <vbox>
	<description>Código:</description>
	<textbox onfocus="select()" id="busquedaCodigoSerie"
                 onkeyup="if (event.which == 13) BuscarOrdenCompra()" 
                 onkeypress="return soloAlfaNumericoCodigo(event);"/>
      </vbox>
      <vbox id="vboxForma_Pago" collapsed="true">
	<checkbox checked="true" id="modoConsultaOrdenCompraContado" label="Contado"/>
	<checkbox checked="true" id="modoConsultaOrdenCompraCredito" label="Credito"/>
      </vbox>
      <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
          <menupopup >
	    <menuitem type="checkbox" label="Estado" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Fecha Entrega" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Forma Pago" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Moneda" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	      
	    <menuseparator />
	    <menuitem type="checkbox" label="Usuario"  
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Observacion" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Registro" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Pago" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Recibido" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
          </menupopup>
        </menu>
      </vbox>

      <vbox style="margin-top:1.1em">
	<button class="btn" id="btnbuscar" label=" Buscar "  image="<?php echo $_BasePath; ?>img/gpos_buscar.png" oncommand="BuscarOrdenCompra()"/>
      </vbox>
    </hbox>

<hbox flex="1" id="boxOrdenCompra"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webOrdenCompra" name="webOrdenCompra" class="AreaOrdenCompra"  src="about:blank" flex="1"/>
</hbox>

<vbox flex="1" id="listboxOrdenCompras" collapsed="false">

  <hbox  flex="0">
    <caption class="box" label="<?php echo _("Pedidos") ?>" />
  </hbox>

  <listbox flex="1" id="busquedaOrdenCompra" contextmenu="AccionesBusquedaOrdenCompra" onkeypress="if (event.keyCode==13) RevisarOrdenCompraSeleccionada()"  onclick="RevisarOrdenCompraSeleccionada()" ondblclick="ModificarOrden()" >
    <listcols flex="1">
      <listcol/>		
      <splitter class="tree-splitter" />
      <listcol flex="1"/>		
      <splitter class="tree-splitter" />
      <listcol flex="1"/>		
      <splitter class="tree-splitter" />
      <listcol flex="2"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>				
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolForma_Pago" collapsed="true"/>		
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>		
      <splitter class="tree-splitter" />
      <listcol flex="1"/>				
      <splitter class="tree-splitter" />
      <listcol flex="1"/>				
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolFecha_Recibido" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolFecha_Pago" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolUsuario" collapsed="true"/>				
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolObservacion" collapsed="true"/>
    </listcols>
    <listhead>
      <listheader label=" # " style="font-style:italic;"/>
      <listheader label="Local"/>
      <listheader label="Código"/>
      <listheader label="Proveedor"/>	
      <listheader label="Estado"/>
      <listheader label="Total Neto"/>
      <listheader label="Forma Pago" id="vlistForma_Pago" collapsed="true"/>
      <listheader label="Fecha Registro" id="vlistFecha_Registro" collapsed="true"/>
      <listheader label="Fecha Pedido"/>
      <listheader label="Fecha Entrega"/>
      <listheader label="Fecha Recibido" id="vlistFecha_Recibido" collapsed="true"/>
      <listheader label="Fecha Pago" id="vlistFecha_Pago" collapsed="true"/>
      <listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
      <listheader label="Obs." id="vlistObservacion" collapsed="true"/>
    </listhead>

</listbox>
	 <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>

<hbox pack="left">
  <caption class="box" label="<?php echo _("Detalle Pedido") ?>" />
</hbox>

<listbox id="busquedaDetallesOrdenCompra" flex="1" contextmenu="AccionesDetallesOrdenCompra" ondblclick="ModificarOrdenDetalle()" onkeyup="if (event.which == 13) ModificarOrdenDetalle()" >
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
    <listcol flex="1"/>
    <splitter class="tree-splitter" />
    <listcol  flex="1"/>
  </listcols>
  <listhead>
    <listheader label=" # " style="font-style:italic;"/>
    <listheader label="REF" />
    <listheader label="CB" />
    <listheader label="Producto"  />
    <listheader label="Detalle"  />
    <listheader label="Cantidad"/>
    <listheader label="Precio" />
    <listheader label="Precio Compra" />
    <listheader label="" />
    <listheader label="" />
  </listhead>

</listbox>
</vbox>
<!-- Formulario OrdenCompra -->
<vbox style="margin-top:3em" id="formularioOrdenCompra" align="center"  pack="top" collapsed="true">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" label="<?php echo _("Modificar Pedido") ?>" />
  </hbox>

  <hbox>
    <groupbox>
      <hbox>
	<grid>
	  <rows>
	    <row class="xbase">
 	      <caption class="xbase" label="Documento" />
	      <description  class="xbase" id="xOrdenCompra" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row class="xbase">
	      <caption class="xbase" label="Código" />
	      <description class="xbase" id="xCodigo"  readonly="true"/>
	    </row>
	    <row>					
	      <caption label="Local"/>    
	      <menulist  id="xOrdenCompraLocal" oncommand="ModificarOrdenCompra(19)" >
		<menupopup>
		  <?php echo genXulComboAlmacenes(false,"menuitem") ?>
		</menupopup>
	      </menulist>						
	    </row>
	    <row>
	      <caption label="Proveedor" />
	      <box>
		<toolbarbutton id="lProvHab"  class="btn" 
			       oncommand="CogeProvHab()" label="+"/>
		<textbox class="media" id="ProvHab" readonly="true" flex="1"/>
		<textbox  id="IdProvHab" value="1" collapsed="true"/>
	      </box>
	    </row>
	    <row>
	      <caption label="Forma Pago" />
	      <menulist  id="xFormaPago"  oncommand="ModificarOrdenCompra(18)">
		<menupopup>
		  <menuitem label="Contado" value="Contado"/>
		  <menuitem label="Credito" value="Credito"/>
		</menupopup>
	      </menulist>
	    </row>
	    <row>
	      <caption label="Fecha Entrega" />
	      <hbox>
		<datepicker id="xEntrega" type="popup"/>
	      </hbox>
	    </row>
	    <row>
	      <caption label="Fecha Pago" />
	      <hbox>
		<datepicker id="xPago" type="popup"/>
	      </hbox>
	    </row>
	    <row>
	      <caption label="Observaciones" />
	      <textbox id="xObservacion" multiline="true" rows="2" cols="10"  
		       onchange="ModificarOrdenCompra(15)"
                       onpaste="return false"
                       onkeypress="return soloAlfaNumerico(event);"/>
	    </row>
	    <spacer style="height:8px"/>
	  </rows>
	</grid>
      </hbox>
    </groupbox>	
  </hbox>
  <hbox collapsed="false">
    <box flex="1"></box>
    <button id="btnvolveralmacen" class="media btn" 
	    image="<?php echo $_BasePath; ?>img/gpos_volver.png" label=" Volver a Pedidos" 
	    oncommand="volverOrdenCompras(1)" collapsed="false"></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>

<!-- Formulario Detalle OrdenCompra -->
<vbox style="margin-top:3em" id="formularioDetalleOrdenCompra" align="center"  pack="top" collapsed="true">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" label="<?php echo _("Modificar Detalle Pedido") ?>"/>
  </hbox>
  <spacer style="height:1em"/>
  <hbox pack="center">
    <description class="xtitulo" id="xOrdenDetalle" onfocus="this.select()" readonly="true"/>
  </hbox>
  <spacer style="height:1em"/>
  <hbox pack="center">
    <description class="xproducto" id="xProducto"  onfocus="this.select()" readonly="true"/>
  </hbox>
  <spacer style="height:1em"/>
  <hbox>
    <groupbox>
      <hbox>
	<grid>
	  <rows>
	    <row class="xbase">
 	      <caption class="xbase" label="Detalle" />
	      <description class="xbase" id="xDetalle" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row id="noMenudeo"> 
	      <caption label="Cantidad" />
	      <row>
		<textbox id="xCantidad"  size="15" onfocus="this.select()"
			 onkeypress="return soloNumeros(event,this.value)" 
			 onchange="ModificarOrdenCompra(7,true)"/>
		<description class="xtext"  id="xcUnidades" value="Unidades"/>
	      </row>
	    </row>
	    
	    <row id="esMenudeo" collapsed="true">
		  <caption id="xMenudeo" label="Cantidad" />
		<row>
		  <textbox id="xEmpaques"  size="15" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarOrdenCompra(7,true)"/>
		  <description class="xtext" id="xContenedor" value="Empaque"/>
		  <textbox id="xMenudencia"  size="5" onfocus="this.select()"
			   onkeypress="return soloNumeros(event,this.value)" 
			   onchange="ModificarOrdenCompra(7,true)"/>
		  <description class="xtext" id="xmUnidades" value="Unidades"/>
		</row>

	    </row>

	    <row>
	      <caption label="Precio" />
	      <row>
		<textbox id="xPrecio"  size="15" onfocus="this.select()"
			 onkeypress="return soloNumeros(event,this.value)" 
			 onchange="ModificarOrdenCompra(6,true)"/>
		<description  class="xtext" id="xMoneda" value="Unidades"/>
	      </row>
	    </row>
	    <spacer style="height:8px"/>
	  </rows>
	</grid>
      </hbox>
    </groupbox>	
  </hbox>
  <hbox collapsed="false">
    <box flex="1"></box>
    <button id="btnvolveralmacen" class="media btn"
	    image="<?php echo $_BasePath; ?>img/gpos_volver.png" label=" Volver a Pedidos" 
	    oncommand="volverOrdenCompras(0)" collapsed="false"></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>

</vbox>

<vbox class="box" id="boxResumenPedido">
  <caption class="box" label="<?php echo _("Resumen Pedidos") ?>" />
  <hbox  class="resumen" pack="center" align="left">
    <label value="Total Pedidos"/>
    <description id="TotalOrdenCompra" value="" />
    <label value="Borrador"/>
    <description id="TotalOrdenCompraBorrador" value="" />
    <label value="Pendientes"/>
    <description id="TotalOrdenCompraPendiente" value="" />
    <label value="Pedidos"/>
    <description id="TotalOrdenCompraPedido" value="" />
    <label value="Recibidos"/>
    <description id="TotalOrdenCompraConfirmados" value="" />
    <label value="Cancelados"/>
    <description id="TotalOrdenCompraCancelados" value="" />
    <label value="Total"/>
    <description id="TotalOrdenCompraImporte" value="" />
  </hbox>
</vbox>
<script>//<![CDATA[

   VerOrdenCompra();
   
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";
   ?>

  //]]></script>


  <?php
    EndXul();
  ?>
