<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="CompraVista" title="Establecer Precio Por Pedido"
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/recepcionpedido/almacenborrador.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;
  var cMargenUtilidad = <?php echo $MargenUtilidad; ?>;
  var cDescuentoGral  = <?php echo $DescuentoGral ?>;
  var cMetodoRedondeo = "<?php echo $MetodoRedondeo ?>";
  var cImpuestoIncluido = <?php echo $COPImpuesto ?>;

  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("almacenborrador.php"); ?>
<!--  no-visuales -->


<!--Facturas-->

<vbox > 
  <hbox pack="center" >
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Recibir Pedidos") ?>
    </caption>
  </hbox>
  <spacer style="height:4px"/>

  <hbox align="start" pack="center"  style="background-color: #d7d7d7;padding:3px;">
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
    <vbox collapsed="true">
      <radiogroup flex="1">
	<radio id="FechaBuscaCompraRegistro" label="Registro"  selected="true" oncommand="BuscarCompra()" />
	<radio id="FechaBuscaCompraEmision" label="Facturación"   oncommand="BuscarCompra()" />
      </radiogroup>
    </vbox>
    
    <vbox>
      <description value="Desde:"/>
      <datepicker id="FechaBuscaCompra" type="popup" onblur="BuscarCompra()"/>
    </vbox>
    <vbox>
      <description value="Hasta:"/>
      <datepicker id="FechaBuscaCompraHasta" type="popup" onblur="BuscarCompra()"/>
    </vbox>
    <vbox>
      <description>Documento</description>
      <menulist id="FiltroCompraDocumento" label="FiltrosCompraDocumentos">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarCompra()"/>
	  <menuitem value="Factura" id="modoConsultaFactura" label="Factura"  oncommand="BuscarCompra()" />
	  <menuitem value="Boleta" id="modoConsultaBoleta" label="Boleta"  oncommand="BuscarCompra()" />
	  <menuitem value="Albaran" id="modoConsultaAlbaran" label="Albaran" oncommand="BuscarCompra()" />
	  <menuitem value="AlbaranInt" id="modoConsultaAlbaranInt" label="AlbaranInt" oncommand="BuscarCompra()" />
	  <menuitem value="Ticket" id="modoConsultaTicket" label="Ticket" oncommand="BuscarCompra()" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox collapsed="true">
      <description>Estado</description>
      <menulist id="FiltroCompra" label="FiltrosCompra">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true"   oncommand="BuscarCompra()"/>
	</menupopup>
      </menulist>
    </vbox>
    <vbox collapsed="true">
      <description>Moneda</description>
      <menulist id="FiltroCompraMoneda" label="FiltrosCompraMoneda">
	<menupopup>
	  <menuitem value="todo1" label="Todos" selected="true" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox>
      <description>Proveedor:</description>
      <textbox onfocus="select()" id="NombreProveedorBusqueda" style="width: 21em"
               onkeyup="if (event.which == 13) BuscarCompra()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>
    <vbox>
      <description>Código</description>
      <textbox onfocus="select()" id="busquedaCodigoSerie" style="width: 11em"
               onkeyup="if (event.which == 13)  BuscarCompra()" 
               onkeypress="return soloNumericoCodigoSerie(event);"/>
    </vbox>
    <vbox collapsed="true">
      <checkbox checked="true" id="modoConsultaCompraContado" label="Contado"/>
      <checkbox checked="true" id="modoConsultaCompraCredito" label="Credito"/>
    </vbox>
    <vbox style="margin-top:.9em">
      <button id="btnbuscar" label=" Buscar "  image="<?php echo $_BasePath; ?>img/gpos_buscar.png" oncommand="BuscarCompra()"/>
    </vbox>
  </hbox>

  <spacer style="height:5px"/>
  <hbox flex="1" id="busquedaCompraResumen">
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Pedidos") ?>" />
    <hbox  flex="1" pack="center">
      <label value="Comprobantes:"/>
      <description id="TotalFacturas" value="" />
      <label value="Albaranes:"/>
      <description id="TotalAlbaranes" value="" />
      <label value="Total Neto:"/>
      <description id="TotalImporte" value="" />
    </hbox>
  </hbox>

  <listbox id="busquedaCompra" contextmenu="AccionesBusquedaCompra" onkeypress="if (event.keyCode==13) RevisarCompraSeleccionada()"  onclick="RevisarCompraSeleccionada()" >
    <listcols flex="1">
      <listcol/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="3" />
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
    </listcols>
    <listhead>
      <listheader label="#" style="font-style:italic;"/>
      <listheader label="Almacén"/>
      <listheader label="Código"/>
      <listheader label="Documento"/>
      <listheader label="Proveedor"/>
      <listheader label="Total Neto"/>
      <listheader label="Fecha de Registro"/>
      <listheader label="Usuario"/>
      <listheader label="Obs."/>

    </listhead>
  </listbox>

</vbox>

<!--/Facturas-->

<!--Detalle-->
<spacer style="height: 4px"/>
<hbox pack="left" id="busquedaCompraDetalleResumen">
  <caption style="font-size: 10px;font-weight: bold;">
    <?php echo _("Detalle Pedidos") ?>
  </caption>
</hbox>
<spacer style="height: 4px"></spacer>

<vbox flex="1" style="overflow: auto;border:1px solid #888;" id="busquedaCompraDetalle" >

  <grid>
    <columns>
      <column flex="0" />
      <column flex="8" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
      <column flex="1" />
    </columns>
    <rows>
	<row style="background-color:#292929;padding:0.2em;color:#c9c9c9;" >
	  <label value="#" style="font-style:italic;" />
	  <label value="Producto"/>
	  <label value="Cantidad"/>
	  <label value="Costo"/>
	  <label value="COP"/>
	  <label value="MUP"/>
	  <label value="PVP"/>
	  <label value="PVP/D"/>
	  <label value="MUC"/>
	  <label value="PVC"/>
	  <label value="PVC/D"/>
	  <label value="Detalle"/>
	</row>
      <rows id="busquedaDetallesCompra">
      </rows>
    </rows>
  </grid>
</vbox> 
<!--/Detalle--> 

<hbox flex="1" id="boxDetCompra"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webDetCompra" name="webDetCompra" class="AreaDetCompra"  src="about:blank" flex="1"/>
</hbox>


<spacer style="height: 4px"></spacer>
<hbox id="busquedaCompraFooter">
  <spacer flex="4"/>
  <button id="TipoCosto" type="menu" 
	  label="  Tipo Costo" collapsed="true" <?php gulAdmite("Precios") ?> >
    <menupopup>
      <menuitem id="tipo_costopromedio" label="Costo Promedio" value="CP" checked="true"
                oncommand="ActualizarTipoCosto(this.value,this.id)">
      </menuitem>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <menuitem id="tipo_ultimocosto" label="Último Costo" value="UC" checked="false"
	        oncommand="ActualizarTipoCosto(this.value,this.id)">
      </menuitem>
      <?php } ?>
    </menupopup>
  </button>
  <button id="actualizarLPV" type="menu" 
	  label="  Aplicar Precios" collapsed="true" <?php gulAdmite("Precios") ?> >
  <menupopup>
    <menuitem label="Local Actual" oncommand="actualizarNuevosPV()"></menuitem>
    <?php if(getSesionDato("esAlmacenCentral")){?>
    <menuitem label="Todos los Locales" oncommand="actualizarAllNuevosPV()"></menuitem>
    <?php } ?>
  </menupopup>
</button>
<button id="guardarPrecios" collapsed="true" label="Guardar Precios" oncommand="guardarPrecios()"/>   
<toolbarseparator />
<button id="recibirProductos" collapsed="true" label="Recibir Produtos" oncommand="recibirProductos()" <?php gulAdmite("Almacen") ?> />   
  <spacer flex="1"/>
</hbox>

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
