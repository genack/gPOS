<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Inventario Ajustes',$predata="",$css='');
StartJs($js='modulos/inventario/inventario.js?v=3.1.9');
?>

  <script>//<![CDATA[
  var cImpuesto       = <?php echo $Impuesto ?>;
  var cIdLocal        = <?php echo $IdLocal; ?>;
  var cUtilidad       = <?php echo $MagenUtilidad; ?>;
  var cDescuentoGral  = <?php echo $DescuentoGral ?>;
  var cMetodoRedondeo = "<?php echo $MetodoRedondeo ?>";
  var COPImpuesto     = <?php echo $COPImpuesto ?>;
  var cModo           = "<?php echo $modo ?>";
  //]]></script>
  <?php getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("../kardex/kardex.php"); ?>
  <!--  no-visuales -->

  <hbox>	
      <html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
          <html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
      </html:div>
  </hbox>	  

<popup id="accionesNS" class="media">
  <menuitem class="menuitem-iconic" image="<?php echo $_BasePath; ?>img/remove16.gif" label="Quitar"  oncommand="quitarns()"/>
</popup>


<!-- Movimientos Encabezado-->
<vbox class="box">  
    <vbox pack="center" align="center">
      <hbox id="rdioInvetarioAjuste" flex="1" >
	<caption class="h1" id="ajust" value="ajust">Ajustes</caption>
	<caption class="h1" id="invent" value="invent">Inventarios</caption>
      </hbox>
      <caption  class="h1" id="wtitleInventario"  collapsed="true"
	       label="<?php echo _("Inventario") ?>"/>
      <textbox id="filtroOperacion" collapsed="true" value="5"/>
    </vbox>
</vbox> 

<!-- Movimientos Busqueda-->
<vbox class="box" id="busquedaMovimiento">
  <hbox align="start" pack="center" >
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroMovimientoLocal" label="FiltrosMovimientoLocal" oncommand="CambioLocalInventario()">
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
               onkeypress="return soloAlfaNumerico(event);" 
	       placeholder=" nombre | marca ó modelo ó detalle..."/>
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
 	 <menuitem label="Todos" />
	 <?php echo genXulComboMarcas(false,"menuitem") ?>
       </menupopup>
      </menulist>
    </vbox>

    <vbox id="cmbStock" collapsed="true">
      <description>Existencias:</description>
      <menulist  id="idstock" oncommand="BuscarAlmacen()">
       <menupopup>
 	 <menuitem value="4" label="Todos" /> 
 	 <menuitem value="0" label="C/S Stock" />
 	 <menuitem value="1" label="Con Stock" />
 	 <menuitem value="2" label="Sin Stock"/>
 	 <menuitem value="3" label="Inventariado"/>
       </menupopup>
      </menulist>
    </vbox>

    <vbox>
      <description>CB:</description>
      <textbox onfocus="select()" id="CodigoBusqueda" style="width: 11em"
	       onkeyup="if (event.which == 13)  BuscarMovimiento()" 
               onkeypress="return soloNumeros(event,this.value);"/>
    </vbox>

      <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
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

    <vbox style="margin-top:1.2em">
      <button class="btn" id="btnbuscar" label=" Buscar "  image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
	      oncommand="BuscarMovimiento()"/>
    </vbox>
    <vbox style="margin-top:1.2em">
      <button class="btn" id="btnImprimirInventarioPDF" image="<?php echo $_BasePath; ?>img/gpos_pdf_ico.png" label=""
	      oncommand="exportarInventario('pdf');" collapsed="false" />
    </vbox>
    <vbox style="margin-top:1.2em">
      <button class="btn" id="btnImprimirInventarioCVS" image="<?php echo $_BasePath; ?>img/gpos_csv_ico.png" label=""
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
<vbox id="resumenMovimiento" collapsed="true" class="box" >
<spacer style="height:5px"/>
  <hbox flex="1">
    <caption class="box" id="listKardexResumen" label="<?php echo _("Listado de Ajustes") ?>" />
    <caption id="fechaInventario" label="" style="font-weight: bold;" collapsed="true"/>
  </hbox>
</vbox>

<!-- Movimientos Listado-->
<listbox flex="1" id="listadoMovimiento" collapsed="true" 
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

<vbox  id="resumenMovimientoFooter"  pack="top" align="left" collapsed="true" class="box">
  <caption class="box" label="<?php echo _("Resumen") ?>" />
  <hbox class="resumen" >
    <label value="Total Movimientos:"/>
    <description id="TotalMovimientos" value="" />
    <label value="Listados:"/>
    <description id="TotalMovimientosListado" value="" />
    <label value="Valor Listado:"/>
    <description id="MovValorTotal" value="" />
  </hbox>
</vbox>



<!-- Resumen Almacen  -->
<vbox id="resumenAlmacen" collapsed="false" class="box">
  <hbox flex="1">
    <caption class="box" label="<?php echo _("Stock Almacén") ?>" />
  </hbox>
</vbox>


<!-- Almacen Listado-->
<listbox flex="1" id="listadoAlmacen" collapsed="false"
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
      <listheader label="Inventariado"/>
      <listheader label="Ultimo Movimiento"/>
      <listheader label="Producto"/>
      <listheader label="Existencias"/>
      <listheader label="Costo"/>
      <listheader label="PVP"/>
      <listheader label="PVPD"/>
      <listheader label="PVC"/>
      <listheader label="PVCD"/>
      <listheader label=""/>
    </listhead>
</listbox>

<vbox  id="resumenAlmacenFooter"  pack="top" align="left"  collapsed="false" class="box">
  <caption class="box" label="<?php echo _("Resumen") ?>" />
   <hbox class="resumen" >
      <label value="Total Productos:"/>
      <description id="TotalProductos" value=" 0" />
      <label value="Con Stock:"/>
      <description id="conStock" value=" 0 " />
      <label value="Sin Stock:"/>
      <description id="sinStock" value=" 0 " />
      <label value="Valor Total:"/>
      <description id="ValorTotal" value="<?php echo $Moneda[1]['S']?> 0.00" />
  </hbox>
</vbox>


<!-- Web Extra -->
<hbox flex="1" id="boxkardex"  collapsed="true" style="height:50em;" pack="center" class="box" > 
  <iframe  id="webkardex" name="webkardex" class="AreaKardex"  src="about:blank" flex="1"/>
</hbox>

<!-- Form Modificar -->
<vbox id="formAjustesExistencias" class="box" flex="1" collapsed="true">
<vbox  class="box" style="margin-top:0.5em"
      align="center"  pack="top" >
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" > <?php echo _("Ajustar Existencias") ?> </caption>
  </hbox>
  <hbox pack="center">
    <caption class="xproducto" id="xProducto" label="Producto" />
  </hbox>
  <hbox>
    <groupbox>
      <hbox>
	<grid>
	  <rows>

	    <row class="xbase">
 	      <caption class="xbase" label="Existencias kardex" />
	      <description class="xbase" id="xExistencias" onfocus="this.select()" readonly="true"/> 
 	    </row>
	    <row class="xbase">
 	      <caption class="xbase" label="Ajuste Existencias" />
	      <description class="xbase" id="xAjuste" onfocus="this.select()" readonly="true"/>
	    </row>

	    <row id="rowContenedor" class="xbase" collapsed="true">
	      <caption label="Unid/Empaque "/>
	      <description id="xUnidxEmpaque" class="xbase" 
		       onfocus="this.select()" readonly="true"/>
	    </row>
	    <row id="rowDatoContenedor" collapsed="true">
	      <caption id="txtContenedor" class="media" label="Empaque - Unid"/>
	      <hbox flex="1">
		<textbox flex="1" class="media" id="xEmpaques" value="1" onfocus="this.select()" 
			 style="width: 4.5em;text-align:right;" 
			 onchange="validaDatoMenudeo(this)" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
		<textbox flex="1" class="media" id="xUnidades" value="0" onfocus="this.select()"
			 style="width: 4em;text-align:right;" 
			 onchange="validaDatoMenudeo(this)" 
			 onkeypress="return soloNumerosEnteros(event,this.value)"/>
	      </hbox>
	    </row>

	    <row  id="rowExistencias">
 	      <caption label="Existencias Fisico" />
	      <textbox style="text-align:right" id="xExistenciasFisico"  
		        onfocus="this.select()" value="0"
		       onkeypress="return soloNumeros(event,this.value);" 
		       onblur="totalAjusteExistencias(this.value)"/>
	    </row>

	    <row>
 	      <caption label="Costo" />
	      <hbox flex="1">
	        <textbox style="text-align:right" id="xValorCompra"   onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('costo',this.value)"/>
	        <textbox id="xCostoEmpaque" value="0" collapsed="true"/>
	        <button id="xEmpaqueProducto" oncommand="mostrarCostoTotalInvent()" collapsed="false"/>
	      </hbox>
	    </row>

	    <row>
	      <caption label="Costo Operativo" />
	      <textbox style="text-align:right" id="xCostoOP"   onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('precio',this.value)" />
	    </row>

	    <spacer style="height:0.4em"/>
	    <row>
 	      <caption label="Precios de Venta" />
	    </row>
	    <spacer style="height:0.8em"/>


	    <row>
	      <caption label="PVP" />
	      <textbox style="text-align:right" id="xPVD"   onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvd',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVP/Dcto." />
	      <textbox style="text-align:right" id="xPVDD" onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvdd',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVC" />
	      <textbox style="text-align:right" id="xPVC"  onfocus="this.select()" 
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvc',this.value)"/>
	    </row>

	    <row>
	      <caption label="PVC/Dcto." />
	      <textbox style="text-align:right" id="xPVCD" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvcd',this.value)"/>

	    </row>

	    <row id="rowPrecioxEmpaque" collapsed="true">
	      <caption label="PV/Empaq.." />
	      <textbox style="text-align:right" id="xPVDE" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvde',this.value)"/>

	    </row>

	    <row id="rowPrecioxDocena" collapsed="true">
	      <caption label="PV/Docena." />
	      <textbox style="text-align:right" id="xPVDED" onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" 
		       onchange="setCostoPrecios('pvded',this.value)"/>

	    </row>
	    <spacer style="height:0.8em"/>
	  </rows>
	</grid>

      </hbox>
    </groupbox>	

    <groupbox id="postAjusteBox" collapsed="true">	
      <hbox>
	<grid >
	  <rows>

	    <row class="xbase">
	      <caption class="xbase" label="Existencias Ajustar" />
	      <description class="xbase" id="xCantidadAjuste" onfocus="this.select()" readonly="true"/>
	    </row>

	    <!-- Lote -->
	    <row id="rowLote" collapsed="true">
	      <caption label="Lote" />
	      <textbox id="xLote"  onfocus="this.select()" 
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
    <button class="media btn" style="font-size:10px;font-weight: bold;" id="btnModificarStock"
	    image="<?php echo $_BasePath; ?>img/gpos_modify.png" label=" Modificar..." 
	    oncommand="validaModificarStock()" collapsed="false"></button>
    <button class="media btn" style="font-size:10px;font-weight: bold;" 
	    image="<?php echo $_BasePath; ?>img/gpos_volver.png" label=" Volver Stock" 
	    oncommand="volverStock()" collapsed="false"></button>
  </hbox>
  <spacer flex="1"></spacer>
</vbox>
</vbox>

<vbox class="box">
  <box flex="1"></box>
  <hbox flex="1">
    <button  flex="1" id="btnVolver" style="font-weight: bold;font-size:11px;" 
	     class="media btn" image="<?php echo $_BasePath; ?>img/gpos_nuevoajuste.png"  
             label=" Nuevo Ajuste" 
	     oncommand="nuevaOperacionAjuste()" collapsed="false" 
             <?php gulAdmite("Ajustes") ?>/>
    
    <button  flex="1" id="btnFinalizarInventario" style="font-weight: bold;font-size:11px;" 
	     class="media btn" image="<?php echo $_BasePath; ?>img/gpos_finalizarinventario.png"  
	     label=" Finalizar Inventario" 
	     oncommand="finalizaOperacionInventario()" collapsed="true" 
             <?php gulAdmite("Ajustes") ?>/>

    <button class="btn" flex="1" id="btnAltaRapida" style="font-weight: bold;font-size:11px;" 
	    image="<?php echo $_BasePath; ?>img/gpos_altarapida.png" label=" Alta Rápida..."
	    oncommand="altarapidaArticulo()" collapsed="true" 
	    <?php gulAdmite("Productos") ?> />

  </hbox>
</vbox>

<script>//<![CDATA[
     <?php echo "mostrarOperacion('".$xload."',true);"?>;
     <?php  if($xload == 'invent') echo "continuarOperacionInventario();"?>;
     <?php if($xload == 'ajust') echo "nuevaOperacionAjuste();" ?>;
 
//]]></script>

  <?php
    EndXul();
  ?>
