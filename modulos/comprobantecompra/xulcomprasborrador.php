<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Compras Comprobantes',$predata="",$css='');
StartJs($js='modulos/comprobantecompra/comprasborrador.js?v=3.1');
?>

  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("comprasborrador.php"); ?>
<!--  no-visuales -->

      <hbox class="box">	
	<html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
	<html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
	</html:div>
      </hbox>	

<!-- Encabezado -->
<hbox pack="center" class="box" id="boxTitleComprobante">
  <caption class="h1">
    <?php echo _("Comprobantes") ?>
  </caption>
</hbox>

<!-- Busqueda -->
<vbox flex="1" class="box">
    <hbox align="start" pack="center" id="comprobantesBusqueda" collapsed="false">
      <vbox>
	<?php if(getSesionDato("esAlmacenCentral")){?>
	<description>Local:</description>
	<hbox>
	  <menulist id="FiltroCompraLocal" label="FiltrosCompraLocal" oncommand="BuscarCompra()">
	    <menupopup id="combolocales">
	      <menuitem value="0" label="Todos" />
	      <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	    </menupopup>
	  </menulist>
	</hbox>
        <?php } else { ?>
        <textbox id="FiltroCompraLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
        <?php } ?>	  
      </vbox>
      <vbox id="vboxFecha_Facturacion" collapsed="true">
        <description value="Fecha:"/>
	<menulist id="FiltroFecha" label="FiltrosComprobante">
	  <menupopup>
	    <menuitem value="Registro" label="Registro" selected="true" oncommand="BuscarCompra()"/>
	    <menuitem value="Facturacion"  label="Facturación"  oncommand="BuscarCompra()" />
	  </menupopup>
	</menulist>	 
      </vbox>

      <vbox>
	<description value="Desde:"/>
	<datepicker id="FechaBuscaCompra" type="popup" />
      </vbox>
      <vbox>
	<description value="Hasta:"/>
	<datepicker id="FechaBuscaCompraHasta" type="popup" />
      </vbox>
      <vbox>
	<description>Documento</description>
	<menulist id="FiltroCompraDocumento" label="FiltrosCompraDocumentos" oncommand="BuscarCompra()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"/>
	    <menuitem value="Factura" id="modoConsultaFactura" label="Factura"/>
	    <menuitem value="Boleta" id="modoConsultaBoleta" label="Boleta"/>
	    <menuitem value="Albaran" id="modoConsultaAlbaran" label="Albaran"/>
	    <menuitem value="AlbaranInt" id="modoConsultaAlbaranInt" label="AlbaranInt" />
	    <menuitem value="Ticket" id="modoConsultaTicket" label="Ticket" />
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxEstado" collapsed="true">
	<description>Estado</description>
	<menulist id="FiltroCompra" label="FiltrosCompra"  oncommand="BuscarCompra()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"  />
	    <menuitem value="Borrador" id="modoConsultaBorrador" label="Borrador" />
	    <menuitem value="Pendiente" id="modoConsultaPendiente" label="Pendiente"/>
	    <menuitem value="Confirmado" id="modoConsultaConfirmada" label="Confirmada" />
	    <menuitem value="Cancelada" id="modoConsultaCancelada" label="Cancelada" />
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxMoneda" collapsed="true">
	<description>Moneda</description>
	<menulist id="FiltroCompraMoneda" label="FiltrosCompraMoneda" oncommand="BuscarCompra()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true"   />
            <?php echo genXulComboMoneda(false,"menuitem") ?>
	    <menuitem value="todo1" label="Local"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox id="vboxForma_Pago" collapsed="true">
	<description>Forma Pago</description>
	<menulist id="FiltroFormaPago" label="FiltrosFormaPago" oncommand="BuscarPago()">
	  <menupopup>
	    <menuitem value="Todos" label="Todos" selected="true" />
	    <menuitem value="Contado" id="modoConsultaPagoContado" label="Contado"/>
	    <menuitem value="Credito" id="modoConsultaPagoCredito" label="Crédito"/>
	  </menupopup>
	</menulist>
      </vbox>
      <vbox>
	<description>Proveedor:</description>
	<textbox onfocus="select()" id="NombreProveedorBusqueda" 
		 onkeyup="if (event.which == 13) BuscarCompra()" 
                 onkeypress="return soloAlfaNumerico(event);"/>
      </vbox>
      <vbox>
	<description>Código</description>
	<textbox onfocus="select()" id="busquedaCodigoSerie" 
		 onkeyup="if (event.which == 13)  BuscarCompra()" 
                 onkeypress="return soloNumericoCodigoSerie(event);"/>
      </vbox>

      <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
          <menupopup>
	    <menuitem type="checkbox" label="Estado" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Fecha Facturacion" checked="false"
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
	    <menuitem type="checkbox" label="Percepcion" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Flete" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
          </menupopup>
        </menu>
      </vbox>

      <vbox style="margin-top:1.1em" >
	<button id="btnbuscar" class="btn" label=" Buscar "  image="<?php echo $_BasePath; ?>img/gpos_buscar.png" oncommand="BuscarCompra()"/>
      </vbox>
    </hbox>

  <vbox flex="1" id="listboxComprobantes" collapsed="false">
    <hbox flex="0" >
      <caption class="box" label="<?php echo _("Comprobantes") ?>" />
    </hbox>
    
    <listbox flex="1" id="busquedaCompra" contextmenu="AccionesBusquedaCompra" onkeypress="if (event.keyCode==13) RevisarCompraSeleccionada()"  onclick="RevisarCompraSeleccionada()" ondblclick="ModificarComprobante()" >
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
	<listcol flex="1" id="vlistcolForma_Pago" collapsed="true"/>
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
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolObservacion" collapsed="true"/>
      </listcols>
      <listhead>
	<listheader label=" # " style="font-style:italic;"/>
	<listheader label="Almacén"/>
	<listheader label="Código"/>
	<listheader label="Documento"/>
	<listheader label="Proveedor"/>
	<listheader label="Estado"/>
	<listheader label="Forma de Pago" id="vlistForma_Pago" collapsed="true"/>
	<listheader label="Percepción" id="vlistPercepcion" collapsed="true" style="text-align:center"/>
	<listheader label="Flete" id="vlistFlete" collapsed="true" style="text-align:center"/>
	<listheader label="Total Neto" style="text-align:center"/>
	<listheader label="Total a Pagar" style="text-align:center"/>
	<listheader label="Facturación"  style="text-align:center"/>
	<listheader label="Fecha de Pago" style="text-align:center"/>
	<listheader label="Fecha de Registro" id="vlistFecha_Registro" collapsed="true"/>
	<listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
	<listheader label="Obs." id="vlistObservacion" collapsed="true"/>
      </listhead>

    </listbox>

    <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>

    <hbox pack="left" class="box">
      <caption class="box" label=" <?php echo _("Detalle Comprobantes") ?>" />
    </hbox>

    <listbox id="busquedaDetallesCompra" flex="1" contextmenu="AccionesDetallesCompra" onkeypress="if (event.keyCode==13) ModificarComprobanteDetalle()" ondblclick="ModificarComprobanteDetalle()"  onclick="RevisarCompraDetalle()" >
      <listcols flex="1">
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol flex="3"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol />
	<splitter class="tree-splitter" />
	<listcol flex="0"/>
	<splitter class="tree-splitter" />
	<listcol flex="0"/>
	<splitter class="tree-splitter" />
	<listcol  flex="8"/>
      </listcols>
      <listhead>
	<listheader label=" # " style="font-style:italic;"/>
	<listheader label="CR" />
	<listheader label="CB" />
	<listheader label="Producto"/>
	<listheader label="Detalle"/>
	<listheader label="Cantidad"/>
	<listheader label="Costo" />
	<listheader label="Precio" />
	<listheader label="Dscto" />
	<listheader label="Valor Compra" />
	<listheader label="Precio Compra" />
	<listheader label="" />
      </listhead>

    </listbox>
  </vbox>


<!-- Formulario Comprobante -->
<vbox style="margin-top:3em" id="formularioComprobante" align="center"  pack="top" collapsed="true">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" label="<?php echo _("Modificar Comprobante") ?>" />
  </hbox>
  <hbox>
    <groupbox>
      <hbox>
	<grid>
	  <rows>
	    <row class="xbase">
 	      <caption class="xbase" label="Comprobante" />
	      <description class="xbase" id="xComprobante" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row>
	      <caption label="Código" />
	      <textbox id="xCodigo"  size="20" onfocus="this.select()"		     
		       onchange="ModificarCompra(3)"
                       onkeypress="return soloNumericoCodigoSerie(event,this.value);"/>
	    </row>
	    <row>					
	      <caption label="Almacén"/>    
	      <menulist  id="xComprobanteLocal" oncommand="ModificarCompra(18)" >
		<menupopup>
		  <?php echo genXulComboAlmacenes(false,"menuitem") ?>
		</menupopup>
	      </menulist>						
	    </row>
	    <row>
	      <caption label="Proveedor" />
	      <box>
		<toolbarbutton id="lProvHab" oncommand="CogeProvHab()" label="+"/>
		<textbox class="media" id="ProvHab" readonly="true" flex="1"/>
		<textbox  id="IdProvHab" value="1" collapsed="true"/>
	      </box>
	    </row>
	    <row>
	      <caption label="Forma Pago" />
	      <menulist  id="xFormaPago"  oncommand="ModificarCompra(4)" >
		<menupopup>
		  <menuitem label="Contado" value="Contado"/>
		  <menuitem label="Credito" value="Credito"/>
		</menupopup>
	      </menulist>
	    </row>
	    <row>
	      <caption id="tPercepcion" label="" />
	      <textbox id="xPercepcion"  size="20" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="ModificarCompra(7)"/>
	    </row>
	    <row>
	      <caption id="tFlete" label="" />
	      <textbox id="xFlete"  size="20" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="ModificarCompra(19)"/>
	    </row>
	    <row>
	      <caption label="Fecha Facturación" />
	      <hbox>
		<datepicker id="xEmision"  type="popup"/>
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
		       onchange="ModificarCompra(6)"
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
	    image="<?php echo $_BasePath; ?>img/gpos_volver.png" label=" Volver Comprobantes" 
	    oncommand="volverComprobantes(1)" collapsed="false" ></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>


<!-- Formulario Detalle Comprobante -->
<vbox style="margin-top:3em" id="formularioDetalleComprobante" align="center"  pack="top" collapsed="true">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" label="<?php echo _("Modificar Detalle Comprobante") ?>"/>
  </hbox>
  <hbox pack="center">
    <description class="xtitulo" id="xComprobanteDetalle" onfocus="this.select()" readonly="true"/>
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
 	      <caption  class="xbase" label="Detalle" />
	      <description  class="xbase" id="xDetalle" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row class="xbase">
 	      <caption  class="xbase" label="Cantidad" />
	      <description   class="xbase" id="xCantidad" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row>
	      <caption label="Valor Compra" />
	      <textbox id="xValorCompra"  onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="ModificarCompra(13,true)"/>
	    </row>

	    <row>
	      <caption label="Precio Compra" />
	      <textbox id="xPrecioCompra" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="ModificarCompra(12,true)"/>
	    </row>
	    <row id="rowLote">
	      <caption label="Lote" />
	      <textbox id="xLote"  onfocus="this.select()" 
		       style="text-transform:uppercase;" 
		       onkeyup="javascript:this.value=this.value.toUpperCase();"		     
		       onchange="ModificarCompra(15,true)"/>
	    </row>
	    <row id="rowVencimiento">
	      <caption label="Vencimiento" />
	      <hbox>
		<datepicker id="xVencimiento" type="popup"/>
	      </hbox>
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
	    image="<?php echo $_BasePath; ?>img/gpos_volver.png" label=" Volver Comprobantes" 
	    oncommand="volverComprobantes(2)" collapsed="false"></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>


<hbox flex="1" id="boxDetCompra"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webDetCompra" name="webDetCompra" class="AreaDetCompra"  src="about:blank" flex="1"/>
</hbox>

</vbox>

<!-- Resumen -->
<vbox class="box" id="boxResumenComprobante">
  <caption class="box" label="<?php echo _("Resumen Comprobantes") ?>" />
  <hbox  class="resumen" pack="center" align="left">
    <label value="Total Comprobantes:"/>
    <description id="TotalCompra" value="" />
    <label value="Borrador:"/>
    <description id="TotalCompraeBorrador" value="" />
    <label value="Pendientes:"/>
    <description id="TotalCompraePendiente" value="" />
    <label value="Confirmadas:"/>
    <description id="TotalCompraeConfirmada" value="" />
    <label value="Canceladas:"/>
    <description id="TotalCompraeCancelada" value="" />
    <label value="Percepción:"/>
    <description id="TotalPercepcion" value="" />
    <label value="Total Neto:"/>
    <description id="TotalImporte" value="" />
  </hbox>
</vbox>
<!-- /Resumen -->
<script>//<![CDATA[

VerCompra();
<?php
if($locales)
  if(getSesionDato("esAlmacenCentral"))
    echo "iniComboLocales('".$locales."');";
?>


//]]></script>


  <?php
    EndXul();
  ?>
