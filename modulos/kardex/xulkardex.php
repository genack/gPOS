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
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/kardex/kardex.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;
  //]]></script>

<!--  no-visuales -->
<?php include("kardex.php"); ?>
<!--  no-visuales -->


<!-- Movimientos Encabezado-->
<vbox> 
  <hbox pack="center" >
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Kardex") ?>
    </caption>
  </hbox>
  <spacer style="height:4px"/>
</vbox> 

<!-- Web Extra -->
<hbox flex="1" id="boxkardex"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webkardex" name="webkardex" class="AreaKardex"  src="about:blank" flex="1"/>
</hbox>

<!-- Movimientos Busqueda-->
<vbox id="busquedaMovimiento">
  <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:3px;">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroMovimientoLocal" label="FiltrosMovimientoLocal"  
		  oncommand="BuscarMovimiento()">
	  <menupopup id="combolocales">
	    <menuitem value="0" label="Todos" />
	    <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroMovimientoLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>
    
    <vbox>
      <description value="Desde:"/>
      <datepicker id="FechaBuscaDesde" type="popup" onblur="BuscarMovimiento()" />
    </vbox>
    <vbox>
      <description value="Hasta:"/>
      <datepicker id="FechaBuscaHasta" type="popup" onblur="BuscarMovimiento()"/>
    </vbox>
    <vbox id="vboxOperacion" collapsed="true">
      <description>Operación:</description>
      <menulist id="filtroOperacion" label="FiltroOperacion" oncommand="BuscarMovimiento()">
	<menupopup>
	  <menuitem value="0" label="Todos" selected="true"/>
	  <?php echo genXulKardexOperaciones($selected=false,$xul="menuitem");?>
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
    <vbox>
      <description>CB:</description>
      <textbox onfocus="select()" id="CodigoBusqueda" style="width: 11em"
	       onkeyup="if (event.which == 13)  BuscarMovimiento()" 
               onkeypress="return soloNumerosEnteros(event,this.value);"/>
    </vbox>

    <vbox style="margin-top:1.2em">
        <menu>
          <toolbarbutton image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png" />
          <menupopup >
	    <menuitem type="checkbox" label="Operacion" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Movimiento" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Familia" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Marca" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	      
	    <!-- menuseparator />
	    <menuitem type="checkbox" label="Documento"  
                      oncommand = "mostrarBusquedaAvanzada(this);"/ -->
          </menupopup>
        </menu>
      </vbox>

    <vbox style="margin-top:.9em">
      <button id="btnbuscar" label=" Buscar "  
	      image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
              oncommand="BuscarMovimiento()"/>
    </vbox>
    <vbox>
    <description value=""/>
    <menu id="listaPaginas" style="color: #000" collapsed="true" label="Pag. 1 - 40">
      <menupopup id="comboPaginas">

      </menupopup>
    </menu>
    </vbox>
    <textbox id="iniciopagina" value="0" style="visibility:hidden; width:0px"/>
  </hbox>
</vbox>

<spacer style="height:5px"/>

<!-- Movimientos Resumen -->
<vbox id="resumenMovimiento">
  <hbox flex="1">
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Movimientos") ?>" />
    <hbox  flex="1" pack="center" collapsed="true">
      <label value="Movimientos:"/>
      <description id="TotalMovimientos" value="" />
      <label value="Entrada:"/>
      <description id="MovEntrada" value="" />
      <label value="Salida:"/>
      <description id="MovSalida" value="" />
    </hbox>
  </hbox>
</vbox>

<!-- Movimientos Listado-->

<!-- Movimientos Listado-->
<!-- listbox flex="1" id="listadoMovimiento" 
	 contextmenu="AccionesBusquedaMovimientoInventario" 
	 onkeypress="if (event.keyCode==13) RevisarMovimientoSeleccionada()"  
	 ondblclick="RevisarMovimientoSeleccionada()" >

    <listcols flex="1">
      <listcol/>
      <splitter class="tree-splitter" />
      <listcol flex="0" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolDocumento" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="0" />
    </listcols>
    <listhead>
      <listheader label=" # " style="font-style:italic;"/>
      <listheader label="Almacén"/>
      <listheader label="Fecha"/>
      <listheader label="Producto"/>
      <listheader label="Operación"/>
      <listheader label="Documento" id="vlistDocumento" collapsed="true"/>
      <listheader label="Cantidad" style="text-align:center;"/>
      <listheader label="Costo" style="text-align:center;"/>
      <listheader label="Saldo" style="text-align:center;"/>
      <listheader label="" />
    </listhead>
</listbox -->

<tree flex="1" id="listadoMovimiento" hidecolumnpicker="false" enableColumnDrag="true" contextmenu="AccionesBusquedaMovimientoInventario"  onkeypress="if (event.keyCode==13) RevisarMovimientoSeleccionada()"  ondblclick="RevisarMovimientoSeleccionada()" >

    <treecols >
      <treecol label=" # "  style="text-align:center;width:4em"  flex="0"/>
      <treecol label="Almacén" flex="1"/>
      <splitter class="tree-splitter" />
      <treecol label="Fecha" flex="1"/>
      <splitter class="tree-splitter" />
      <treecol label="Producto" flex="6"/>
      <splitter class="tree-splitter" />
      <treecol label="Operación" flex="4"/>
      <splitter class="tree-splitter" />
      <treecol label="Documento" id="vlistDocumento"  hidden="true"  flex="1"/>
      <splitter class="tree-splitter" />
      <treecol label="Cantidad" flex="1"/>
      <splitter class="tree-splitter" />
      <treecol label="Costo" style="text-align:center;" flex="1"/>
      <splitter class="tree-splitter" />
      <treecol label="Saldo" style="text-align:center;" flex="1"/>
      <!-- treecol label="" / -->
    </treecols>
 <treechildren id="my_tree_children" >

  </treechildren>
</tree>

<spacer style="height: 4px"></spacer>

<script>//<![CDATA[
  VerMovimiento();
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";
   ?>
//]]></script>


  <?php
    EndXul();
  ?>
