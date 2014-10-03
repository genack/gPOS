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
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/inventario.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
  <script>//<![CDATA[
  var cImpuesto = <?php echo getSesionDato("IGV"); ?>;
  var cIdLocal  = <?php echo getSesionDato("IdTienda"); ?>;
  var cUtilidad = <?php echo getSesionDato("MargenUtilidad"); ?>;
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("partes-almacen/kardex.php"); ?>
<!--  no-visuales -->

<popup id="accionesNS" class="media">
  <menuitem class="menuitem-iconic" image="img/remove16.gif" label="Quitar"  oncommand="quitarns()"/>
</popup>


<!-- Movimientos Encabezado-->
<hbox pack="center">
  <caption style="font-size: 14px;font-weight: bold;">
    <?php echo _("Ajustes") ?>
  </caption>
</hbox>
<vbox> 
    <vbox pack="center" align="center">
      <radiogroup id="rdioInvetarioAjuste" flex="1" orient="horizontal" style="font-size: 14px;font-weight: bold;" collapsed="true">
	<radio id="ajust" value="ajust" label="Ajustes"  selected="true" oncommand="mostrarOperacion(this.id)"  />
      </radiogroup>
      <caption id="wtitleInventario" style="padding:0.4em;font-size: 14px;font-weight: bold;"
	       label="<?php echo _("Inventario") ?>" collapsed="true"/>
      <textbox id="filtroOperacion" collapsed="true" value="5"/>
    </vbox>
  <spacer style="height:4px"/>
</vbox> 

<!-- Movimientos Busqueda-->
<vbox id="busquedaMovimiento">
  <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroMovimientoLocal" label="FiltrosMovimientoLocal"  
		  oncommand="BuscarOperacionLocal(this.value)">
	  <menupopup id="combolocales">
	    <?php echo genXulComboAlmacenes($IdLocal,"menuitem") ?>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroMovimientoLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>

    <vbox collapsed="true" id="cmbInventarios">
      <description>Inventario:</description>
      <menulist id="filtroInventarios" label="FiltroInventarios" oncommand="mostrarOperacion('invent')">
	<menupopup id="filtroInventariosPopup">
	  <menuitem value="0:0:0" label="Elije Inventario" selected="true"/>
	  <?php echo genXulKardexInventario($selected=false,$xul="menuitem",$IdLocal);?>
	</menupopup>
      </menulist>
    </vbox>

    <vbox id="vboxMovimiento" collapsed="true">
      <description>Movimiento:</description>
      <menulist id="filtroMovimiento" label="FiltroMovimiento" oncommand="BuscarMovimiento()">
	<menupopup>
	  <menuitem value="0" label="Todos" selected="true"/>
	  <menuitem value="Entrada" label="Entrada"/>
	  <menuitem value="Salida"  label="Salida"/>
	</menupopup>
      </menulist>
    </vbox>

    <vbox id="fechaDesde">
      <description value="Desde:"/>
      <datepicker id="FechaBuscaDesde" type="popup" onblur="BuscarMovimiento()" 
                  oncommand="BuscarMovimiento()"/>
    </vbox>
    <vbox id="fechaHasta">
      <description value="Hasta:"/>
      <datepicker id="FechaBuscaHasta" type="popup" onblur="BuscarMovimiento()"
	          oncommand="BuscarMovimiento()"/>
    </vbox>

    <vbox>
      <description>Producto:</description>
      <textbox onfocus="select()" id="NombreBusqueda" style="width: 21em"
	       onkeyup="if (event.which == 13) BuscarMovimiento()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>

    <vbox id="vboxFamilia" collapsed="true">
      <description>Familia:</description>
       <menulist  id="idfamilia" oncommand="BuscarMovimiento()">
       <menupopup>
 	<menuitem label="Todos" style="font-weight: bold"/>
 	<?php echo genXulComboFamilias(false,"menuitem") ?>
       </menupopup>
      </menulist>
     </vbox>

     <vbox id="vboxMarca" collapsed="true">
      <description>Marca:</description>
      <menulist  id="idmarca" oncommand="BuscarMovimiento()">
       <menupopup>
 	 <menuitem label="Todos" style="font-weight: bold"/>
	 <?php echo genXulComboMarcas(false,"menuitem") ?>
       </menupopup>
      </menulist>
    </vbox>

    <vbox id="cmbStock" collapsed="true">
      <description>Existencias:</description>
      <menulist  id="idstock" oncommand="BuscarAlmacen()">
       <menupopup>
 	 <menuitem value="0" label="Todos" />
 	 <menuitem value="1" label="Stock" />
 	 <menuitem value="2" label="Sin Stock"/>
       </menupopup>
      </menulist>
    </vbox>

    <vbox>
      <description>CB:</description>
      <textbox onfocus="select()" id="CodigoBusqueda" style="width: 11em"
	       onkeyup="if (event.which == 13)  BuscarMovimiento()" 
               onkeypress="return soloNumeros(event,this.value);"/>
    </vbox>

      <vbox style="margin-top:1.2em">
        <menu>
          <toolbarbutton image="img/gpos_busqueda_avanzada.png" />
          <menupopup >
	    <menuitem type="checkbox" label="Movimiento" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Familia" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Marca" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	      
	    <menuseparator />
	    <menuitem type="checkbox" label="Detalle"  
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Usuario"  
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Observacion" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>

          </menupopup>
        </menu>
      </vbox>

    <vbox style="margin-top:.9em">
      <button id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" 
	      oncommand="BuscarMovimiento()"/>
    </vbox>
    <vbox id="btnAltaRapida"  collapsed="true" style="margin-top:.9em">
      <button  image="img/gpos_altarapida.png" label=" Alta Rápida..." 
	      oncommand="altarapidaArticulo()" <?php gulAdmite("Productos") ?> />
    </vbox>
    <vbox style="margin-top:.9em">
      <button id="btnImprimirInventarioPDF" image="img/gpos_pdf_ico.png" label=""
	      oncommand="exportarInventario('pdf');" collapsed="false" />
    </vbox>
    <vbox style="margin-top:.9em">
      <button id="btnImprimirInventarioCVS" image="img/gpos_csv_ico.png" label=""
	      oncommand="exportarInventario('cvs');" collapsed="false"/>
    </vbox>
    <vbox>
    <description value=""/>
    <menu id="listaPaginas" style="color: #000" collapsed="true" label="Pag. 1 - 20">
      <menupopup id="comboPaginas">

      </menupopup>
    </menu>
    </vbox>
    <textbox id="iniciopagina" value="0" style="visibility:hidden; width:0px"/>
  </hbox>
</vbox>

<!-- Movimientos Resumen -->
<vbox id="resumenMovimiento" collapsed="false">
<spacer style="height:5px"/>
  <hbox flex="1">
    <caption id="listKardexResumen" style="font-size:10px; font-weight: bold;" 
	 label="<?php echo _("Listado de Ajustes") ?>" />
    <caption id="fechaInventario" label="" style="font-weight: bold;" collapsed="true"/>
    <hbox  flex="1" pack="center">
      <label value="Total Movimientos:"/>
      <description id="TotalMovimientos" value="" />
      <label value="Listados:"/>
      <description id="TotalMovimientosListado" value="" />
      <label value="Valor Listado:"/>
      <description id="MovValorTotal" value="" />
    </hbox>
  </hbox>
</vbox>

<!-- Movimientos Listado-->
<listbox flex="1" id="listadoMovimiento" collapsed="false" 
	 contextmenu="AccionesBusquedaMovimientoInventario"> 
    <!-- onkeypress="if (event.keyCode==13) RevisarMovimientoSeleccionada()"
	 ondblclick="RevisarMovimientoSeleccionada()" -->

    <listcols flex="1">
      <listcol/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolDetalle" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolObservacion" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="0"/>
    </listcols>
    <listhead>
      <listheader label=" # " style="font-style:italic;"/>
      <listheader label="Almacén"/>
      <listheader label="Producto"/>
      <listheader label="Operación"/>
      <listheader label="Fecha"/>
      <listheader label="Detalle" id="vlistDetalle" collapsed="true"/>
      <listheader label="Costo"/>
      <listheader label="Ajuste"/>
      <listheader label="Saldo"/>
      <listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
      <listheader label="Obs." id="vlistObservacion" collapsed="true"/>
      <listheader label=""/>
    </listhead>
</listbox>

<!-- Resumen Almacen  -->
<vbox id="resumenAlmacen" collapsed="true">
  <hbox flex="1">
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Stock Almacén") ?>" />
    <hbox  flex="1" pack="center">
      <label value="Total Productos:"/>
      <description id="TotalProductos" value=" 0" />
      <label value="Con Stock:"/>
      <description id="conStock" value=" 0 " />
      <label value="Sin Stock:"/>
      <description id="sinStock" value=" 0 " />
      <label value="Valor Total:"/>
      <description id="ValorTotal" value="<?php echo $Moneda[1]['S']?> 0.00" />
    </hbox>
  </hbox>
</vbox>

<!-- Almacen Listado-->
<listbox flex="1" id="listadoAlmacen" collapsed="true"
	 contextmenu="AccionesBusquedaAlmacenInventario" 
	 onkeypress="if (event.keyCode==13) modificarArticuloSeleccionada()"  
	 ondblclick="modificarArticuloSeleccionada()" >

    <listcols flex="1">
      <listcol/>
      <splitter class="tree-splitter" />
      <listcol flex="0" />
      <splitter class="tree-splitter" />
      <listcol flex="0" />
      <splitter class="tree-splitter" />
      <listcol flex="6" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="8" />
    </listcols>
    <listhead>
      <listheader label=" # " style="font-style:italic;"/>
      <listheader label="Almacén"/>
      <listheader label="Ultimo Movimiento"/>
      <listheader label="Producto"/>
      <listheader label="Existencias"/>
      <listheader label="Costo"/>
      <listheader label="PV"/>
      <listheader label="PVD"/>
      <listheader label="PVC"/>
      <listheader label="PVCD"/>
      <listheader label=""/>
    </listhead>
</listbox>

<!-- Web Extra -->
<hbox flex="1" id="boxkardex"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webkardex" name="webkardex" class="AreaKardex"  src="about:blank" flex="1"/>
</hbox>

<!-- Form Modificar -->
<vbox style="margin-top:1em" id="formAjustesExistencias" 
      align="center"  pack="top" collapsed="true">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Ajustar Existencias") ?>
    </caption>
  </hbox>
  <spacer style="height:1em"/>
  <hbox pack="center">
    <caption id="xProducto" label="Producto" style="font-size: 12px;font-weight: bold;"/>
  </hbox>
  <spacer style="height:0.5em"/>
  <hbox>
    <groupbox>
      <hbox>
	<grid>
	  <rows>

	    <row>
 	      <caption label="Existencias kardex" />
	      <description id="xExistencias" style="font-size:11px;text-align:right;" 
		       size="40" onfocus="this.select()" readonly="true"/> 
	    </row>
	    <row id="rowContenedor" collapsed="true">
	      <caption label="Unid/Empaque "/>
	      <description id="xUnidxEmpaque" style="text-align:right;font-size:11px;" 
		       size="40" onfocus="this.select()" readonly="true"/>
	    </row>
	    <row id="rowDatoContenedor" collapsed="true">
	      <caption id="txtContenedor" class="media" label="Empaque - Unid"/>
	      <hbox flex="1">
		<textbox class="media" id="xEmpaques" value="1" onfocus="this.select()" 
			 style="width: 4.5em;text-align:right;" 
			 onchange="validaDatoMenudeo(this)" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/> -
		<textbox class="media" id="xUnidades" value="0" onfocus="this.select()"
			 style="width: 4em;text-align:right;" 
			 onchange="validaDatoMenudeo(this)" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
	      </hbox>
	    </row>

	    <row  id="rowExistencias">
 	      <caption label="Existencias Fisico" />
	      <textbox style="text-align:right" id="xExistenciasFisico"  
		       size="40" onfocus="this.select()" value="0"
		       onkeypress="return soloNumeros(event,this.value);" 
		       onblur="totalAjusteExistencias(this.value)"/>
	    </row>

	    <spacer style="height:0.2em"/>
	    <row>
 	      <caption label="Ajuste Existencias" />
	      <description id="xAjuste" style="text-align:right;font-size:11px;" 
		       size="40" onfocus="this.select()" readonly="true"/>
	    </row>
	    <spacer style="height:0.2em"/>
	    <row>
 	      <caption label="Costo" />
	      <textbox style="text-align:right" id="xValorCompra"  size="40" onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('costo',this.value)"/>
	    </row>

	    <row>
	      <caption label="Precio Compra" />
	      <textbox style="text-align:right" id="xPrecioCompra"  size="40" onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('precio',this.value)" />
	    </row>

	    <spacer style="height:0.4em"/>
	    <row>
 	      <caption label="Precios de Venta" />
	    </row>
	    <spacer style="height:0.8em"/>


	    <row>
	      <caption label="PVD" />
	      <textbox style="text-align:right" id="xPVD"  size="40" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('pvd',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVD/Dcto." />
	      <textbox style="text-align:right" id="xPVDD"  size="40" onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('pvdd',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVC" />
	      <textbox style="text-align:right" id="xPVC"  size="40" onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('pvc',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVC/Dcto." />
	      <textbox style="text-align:right" id="xPVCD"  size="40" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onblur="setCostoPrecios('pvcd',this.value)"/>

	    </row>
	    <spacer style="height:0.8em"/>
	  </rows>
	</grid>

      </hbox>
    </groupbox>	

    <groupbox id="postAjusteBox" collapsed="true">	
      <caption label="Ingrese:" style="padding:0.5em 0 0.5em 0;"/>
      <hbox>
	<grid >
	  <rows>

	    <row>
	      <caption label="Existencias Ajustar" />
	      <description id="xCantidadAjuste" style="font-size:11px;"
		       size="20" onfocus="this.select()" readonly="true"/>
	    </row>

	    <!-- Lote -->
	    <row id="rowLote" collapsed="true">
	      <caption label="Lote" />
	      <textbox id="xLote"  size="20" onfocus="this.select()" 
		       style="text-transform:uppercase;" 
		       onkeyup="javascript:this.value=this.value.toUpperCase();"/>
	    </row>

	    <!-- Vencimiento -->
	    <row id="rowVencimiento" collapsed="true">
	      <caption label="Vencimiento" />
	      <hbox>
		<datepicker id="xVencimiento" type="popup" />
	      </hbox>
	    </row>

 	    <!-- Ajuste Operacion Salida -->
	    <row id="rowAjusteSalida" collapsed="true">
	      <caption label="Motivo Salida " />
	      <menulist  id="xAjusteOperacionSalida" editable="true" onfocus="this.select()">
		<menupopup id="xAjustepopupSalida">
		  <menuitem label="Elige motivo ajuste"
			    selected="true" style="font-weight: bold"/>
		  <?php echo genXulComboAjusteOperacion(false,"menuitem","Salida") ?>
		</menupopup>
	      </menulist>
	    </row>

 	    <!-- Ajuste Operacion Entrada -->
	    <row id="rowAjusteEntrada" collapsed="true">
	      <caption label="Motivo Entrada" />
	      <menulist  id="xAjusteOperacionEntrada" editable="true" onfocus="this.select()">
		<menupopup id="xAjustepopupEntrada">
		  <menuitem label="Elige motivo ajuste"
			    selected="true" style="font-weight: bold"/>
		  <?php echo genXulComboAjusteOperacion(false,"menuitem","Entrada") ?>
		</menupopup>
	      </menulist>
	    </row>

	    <!-- Observaciones -->
	    <row>
	      <caption label="Observaciones"></caption>
	      <textbox style="width: 15em;" id="xObservacion" multiline="true"
		       rows="1" cols="50" focused=""></textbox>
	    </row>

	    <spacer style="height:8px"/>
	    
	  </rows>
	</grid>
      </hbox>
    </groupbox>	
  </hbox>

  <hbox collapsed="false">
    <box flex="1"></box>
    <button class="media" style="font-size:10px;font-weight: bold;" id="btnModificarStock"
	    image="img/gpos_modify.png" label=" Modificar..." 
	    oncommand="validaModificarStock()" collapsed="false"></button>
    <button class="media" style="font-size:10px;font-weight: bold;" 
	    image="img/gpos_volver.png" label=" Volver Stock" 
	    oncommand="volverStock()" collapsed="false"></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>

<vbox>
  <box flex="1"></box>
  <hbox flex="1">
    <button  flex="1" id="btnVolver" style="font-weight: bold;font-size:11px;" 
	     class="media" image="img/gpos_nuevoajuste.png"  label=" Nuevo Ajuste" 
	     oncommand="nuevaOperacionAjuste()" collapsed="false" <?php gulAdmite("Ajustes") ?>/>
    
    <button  flex="1" id="btnFinalizarInventario" style="font-weight: bold;font-size:11px;" 
	     class="media" image="img/gpos_finalizarinventario.png"  label=" Finalizar Inventario" 
	     oncommand="finalizaOperacionInventario()" collapsed="true" <?php gulAdmite("Ajustes") ?>/>

  </hbox>
</vbox>

<spacer style="height: 4px"></spacer>

<script>//<![CDATA[
  VerMovimiento();
//]]></script>


  <?php
    EndXul();
  ?>
