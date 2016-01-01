<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Productos Ficha Tecnica',$predata="",$css='');
StartJs($js='modulos/productos/productoinfo.js?v=3.1');
?>

  <script>//<![CDATA[
  var cImpuesto = <?php echo getSesionDato("IGV"); ?>;
  var cIdLocal  = <?php echo getSesionDato("IdTienda"); ?>;
  var cUtilidad = <?php echo getSesionDato("MargenUtilidad"); ?>;
  //]]></script>

<!--  no-visuales -->

<!--  no-visuales -->

<!--  Encabezado-->
<vbox class="box"> 
    <vbox pack="center" align="center">
      <caption id="wtitleInventario" class="h1" > <?php echo _("Productos - Ficha Técnica") ?> </caption>
    </vbox>
</vbox> 

<!--Almacen  Busqueda-->
<vbox id="busquedaMovimiento" class="box">
  <hbox align="start" pack="center">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroLocal" label="FiltrosMovimientoLocal"  
		  oncommand="BuscarAlmacen()">
	  <menupopup id="combolocales">
	    <?php echo genXulComboAlmacenes($IdLocal,"menuitem") ?>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>


    <vbox>
      <description>Producto:</description>
      <textbox onfocus="select()" id="NombreBusqueda" style="width: 21em"
	       onkeyup="if (event.which == 13) BuscarAlmacen()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>

    <vbox id="cmbFamilia">
      <description>Familia:</description>
       <menulist  id="idfamilia" oncommand="BuscarAlmacen()">
       <menupopup>
 	<menuitem label="Todos" style="font-weight: bold"/>
 	<?php echo genXulComboFamilias(false,"menuitem") ?>
       </menupopup>
      </menulist>
     </vbox>

     <vbox id="cmbMarca">
      <description>Marca:</description>
      <menulist  id="idmarca" oncommand="BuscarAlmacen()">
       <menupopup>
 	 <menuitem label="Todos" style="font-weight: bold"/>
	 <?php echo genXulComboMarcas(false,"menuitem") ?>
       </menupopup>
      </menulist>
    </vbox>

    <vbox id="cmbStock" >
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
      <textbox onfocus="select()" id="CodigoBusqueda" 
	       onkeyup="if (event.which == 13)  BuscarAlmacen()" style="width: 11em"
               onkeypress="return soloNumeros(event,this.value);"/>
    </vbox>

    <vbox style="margin-top:1.1em" >
      <button id="btnbuscar" label=" Buscar "  class="btn"
              image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
	      oncommand="BuscarAlmacen()"/>
    </vbox>
    <vbox id="btnAltaRapida" style="margin-top:1.1em"  collapsed="true">
      <button image="<?php echo $_BasePath; ?>img/gpos_altarapida.png" 
	      label=" Alta Rápida..." class="btn"
	      oncommand="altarapidaArticulo()"/>
    </vbox>
    <vbox style="margin-top:1.1em" >
      <description style="-moz-opacity: 0">. </description>
      <button id="btnImprimirInventario" class="btn"
              image="<?php echo $_BasePath; ?>img/gpos_imprimir.png" label=" Imprimir" 
	      oncommand="imprimirInventario();" collapsed="true"/>
    </vbox>

  </hbox>
</vbox>


<!-- Resumen Almacen  -->
<vbox class="box">
  <caption class="box" label="<?php echo _("Productos:") ?>" />
</vbox>

<!-- Almacen Listado-->
<listbox flex="1" id="listadoAlmacen" 
	 contextmenu="AccionesBusquedaAlmacenInventario" 
	 onkeypress="if (event.keyCode==13) modificarArticuloSeleccionada()"  
	 onclick="modificarArticuloSeleccionada()" >

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

<vbox id="resumenAlmacen" class="box">
  <caption class="box" label="<?php echo _("Resumen Productos:") ?>" />
  <hbox class="resumen" align="left">
    <label id="TotalProductos" value="0 productos listados" />
  </hbox>
</vbox>


<!-- Web Extra -->
<hbox flex="1" id="boxkardex"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webkardex" name="webkardex" class="AreaKardex"  src="about:blank" flex="1"/>
</hbox>

<!-- Form Producto Información -->
<vbox id="formAjustesExistencias" 
      align="center"  pack="top" collapsed="true" class="box">
  <spacer flex="1"></spacer>
  <hbox pack="center">
    <caption class="h1" label="<?php echo _("Ficha Técnica")?>"/>
  </hbox>
  <hbox pack="center">
    <caption id="xProducto" style="font-size: 1.1rem;"/>
  </hbox>
  <spacer style="height:1em"/>

  <hbox class="box">
    <groupbox id="gboxIndicaciones">
      <vbox>
 	<caption label="<?php echo $Indicaciones ?>" />
	<rows>
	 <row>
	<textbox id="xIndicacion1" multiline="true"
	         onfocus="this.select()"
	         onpaste="return false"
                 onkeypress="return soloAlfaNumerico(event);"
	         onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                       id="btn11"
                       oncommand="checkItemsFichaTecnica(1,1)" />
	 </row>
	 <row>
	<textbox id="xIndicacion2"  collapsed="true" multiline="true"
	         onfocus="this.select()"
	         onpaste="return false"
                 onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                       id="btn12"
                       oncommand="checkItemsFichaTecnica(1,2)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xIndicacion3" collapsed="true" multiline="true"
	         onfocus="this.select()"
	         onpaste="return false"
                 onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                       id="btn13"
                       oncommand="checkItemsFichaTecnica(1,3)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xIndicacion4"  collapsed="true" multiline="true"
	         onfocus="this.select()"
	         onpaste="return false"
                 onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/> 
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                       id="btn14"
                       oncommand="checkItemsFichaTecnica(1,4)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xIndicacion5"  collapsed="true" multiline="true"
	         onfocus="this.select()"
	         onpaste="return false"
                 onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/> 
	 </row>
	 </rows>
	<spacer style="height:0.8em"/>
      </vbox>
    </groupbox>	

    <groupbox id="gboxContraindicaciones">	
      <vbox>
	<caption label="<?php echo $CtraIndicacion ?>"/>
	<rows>
	 <row>

	<textbox id="xContraIndicacion1" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn21"
                 oncommand="checkItemsFichaTecnica(2,1)" />
	 </row>
	 <row>
	<textbox id="xContraIndicacion2" collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn22" 
                 oncommand="checkItemsFichaTecnica(2,2)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xContraIndicacion3"  collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn23"
                 oncommand="checkItemsFichaTecnica(2,3)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xContraIndicacion4"  collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn24"
                 oncommand="checkItemsFichaTecnica(2,4)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xContraIndicacion5"  collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
	 </row>
	 </rows>
        <spacer style="height:8px"/>
      </vbox>
    </groupbox>	

    <groupbox id="gboxInteracciones" >	
      <vbox>
        <caption label="<?php echo $Interaccion ?>" />
	 <rows>
	 <row>
        <textbox id="xInteraccion1" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn31"
                 oncommand="checkItemsFichaTecnica(3,1)" />
	 </row>
	 <row>
        <textbox id="xInteraccion2" collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn32"
                 oncommand="checkItemsFichaTecnica(3,2)" collapsed="true"/>
	 </row>
	 <row>
        <textbox id="xInteraccion3" collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn33"
                 oncommand="checkItemsFichaTecnica(3,3)" collapsed="true"/>
	 </row>
	 <row>
        <textbox id="xInteraccion4" collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn34"
                 oncommand="checkItemsFichaTecnica(3,4)" collapsed="true"/>
	 </row>
	 <row>
        <textbox id="xInteraccion5" collapsed="true" multiline="true"
	         onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
	 </row>
	 </rows>
	<spacer style="height:8px"/>
      </vbox>
    </groupbox>	
    <groupbox id="gboxDosificacion" >	
      <vbox>
	<caption label="<?php echo $Dosificacion ?>" />
	 <rows>
	 <row>
	<textbox id="xDosificacion1" multiline="true"
                 onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn41"
                 oncommand="checkItemsFichaTecnica(4,1)" />
	 </row>
	 <row>
	<textbox id="xDosificacion2" collapsed="true" multiline="true"
                 onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn42"
                 oncommand="checkItemsFichaTecnica(4,2)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xDosificacion3" collapsed="true" multiline="true"
                 onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn43"
                 oncommand="checkItemsFichaTecnica(4,3)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xDosificacion4" collapsed="true" multiline="true"
                 onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
        <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_fichatecnica_mas.png" 
                 id="btn44"
                 oncommand="checkItemsFichaTecnica(4,4)" collapsed="true"/>
	 </row>
	 <row>
	<textbox id="xDosificacion5" collapsed="true" multiline="true"
                 onfocus="this.select()" 
	         onpaste="return false"
	         onkeypress="return soloAlfaNumerico(event);"
                 onchange="guardaProductoInformacion()"/>
	 </row>
	 </rows>
        <spacer style="height:8px"/>
      </vbox>
    </groupbox>	

  </hbox>

  <spacer flex="1"></spacer>
</vbox>

<vbox class="box">
  <box flex="1"></box>
  <hbox flex="1">
    <button  flex="1" id="btnVolver" 
	     class="media btn" image="<?php echo $_BasePath; ?>img/gpos_volver.png"  
             label=" Volver Productos" 
	     oncommand="volverProductos()"/>
    
  </hbox>
</vbox>

<script>//<![CDATA[
	 verStockAlmacen();

//]]></script>


  <?php
    EndXul();
  ?>
