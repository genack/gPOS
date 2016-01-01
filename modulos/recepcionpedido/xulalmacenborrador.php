<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Recibir Pedidos',$predata="",$css='');
StartJs($js='modulos/recepcionpedido/almacenborrador.js?v=3.1');
?>
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

<vbox class="box"> 
  <hbox pack="center" >
    <caption class="h1">
      <?php echo _("Recibir Pedidos") ?>
    </caption>
  </hbox>

  <hbox align="start" pack="center" >
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
      <datepicker id="FechaBuscaCompra" type="popup" />
    </vbox>
    <vbox>
      <description value="Hasta:"/>
      <datepicker id="FechaBuscaCompraHasta" type="popup" />
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
    <vbox collapsed="true">
      <checkbox checked="true" id="modoConsultaCompraContado" label="Contado"/>
      <checkbox checked="true" id="modoConsultaCompraCredito" label="Credito"/>
    </vbox>
    <vbox style="margin-top:1.1em">
      <button class="btn" id="btnbuscar" label=" Buscar "  image="<?php echo $_BasePath; ?>img/gpos_buscar.png" oncommand="BuscarCompra()"/>
    </vbox>
  </hbox>

  <hbox flex="1" id="busquedaCompraResumen">
    <caption class="box" label="<?php echo _("Pedidos") ?>" />
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
<!-- splitter collapse="none" resizeafter="farthest" orient="vertical"></splitter -->
<!--Detalle-->
<hbox   class="box" pack="left" id="busquedaCompraDetalleResumen">
  <caption class="box" label="<?php echo _("Detalle Pedidos") ?>" />
</hbox>
<vbox  class="box" flex="1" style="overflow: auto;" id="busquedaCompraDetalle" >

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
	<row class="recibir" >
	  <label value="#"/>
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

<hbox  class="box" flex="1" id="boxDetCompra"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webDetCompra" name="webDetCompra" class="AreaDetCompra"  src="about:blank" flex="1"/>
</hbox>

<hbox  class="box" >
  <vbox flex="1">
    <caption class="box" label="<?php echo _("Resumen Pedidos") ?>" />
    <hbox  class="resumen" flex="1" pack="left" align="left">
      <label value="Comprobantes:"/>
      <description id="TotalFacturas" value="" />
      <label value="Albaranes:"/>
      <description id="TotalAlbaranes" value="" />
      <label value="Total Neto:"/>
      <description id="TotalImporte" value="" />
    </hbox>
  </vbox>
  <hbox  id="busquedaCompraFooter">
    <spacer flex="4"/>
    <button id="TipoCosto" type="menu" class="popup"
	    label="  Tipo Costo " collapsed="true" <?php gulAdmite("Precios") ?> >
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
  <button id="actualizarLPV" type="menu" class="popup" 
	  label="  Aplicar Precios "  collapsed="true" <?php gulAdmite("Precios") ?> >
  <menupopup>
    <menuitem label="Local Actual" oncommand="actualizarNuevosPV()"></menuitem>
    <?php if(getSesionDato("esAlmacenCentral")){?>
    <menuitem label="Todos los Locales" oncommand="actualizarAllNuevosPV()"></menuitem>
    <?php } ?>
  </menupopup>
   </button>
   <button  class="btn" id="guardarPrecios" collapsed="true" label="Guardar Precios" oncommand="guardarPrecios()"/>   
   <toolbarseparator />
   <button  class="btn" id="recibirProductos" collapsed="true" label="Recibir Produtos" oncommand="recibirProductos()" <?php gulAdmite("Almacen") ?> />   
  <spacer flex="1"/>
</hbox>
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
