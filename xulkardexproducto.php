<?php
header("Content-type: application/vnd.mozilla.xul+xml");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
    echo '<?xml-stylesheet href="'.$_BasePath.'css/xulkardex.css" type="text/css"?>';
    echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="NumSerie" title="kardex" 
        xmlns:html="http://www.w3.org/1999/xhtml"
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/kardexproducto.js"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js"/>

<script>//<![CDATA[
var modo       = "<?php echo $modo;?>";
var idproducto = "<?php echo $id;?>";
var idalmacen  = "<?php echo $idalmacen;?>";
var idlocal    = "<?php echo $donde;?>";
var cProducto  = "<?php echo $producto;?>";
var cEmpaque   = "<?php echo $empaque;?>";
var cUnidxemp  = "<?php echo $unidxemp;?>";
var cMenudeo   = "<?php echo $menudeo;?>";
var cUnidad    = "<?php echo $unidades;?>";
var cLote      = "<?php echo $lote;?>";
var cFv        = "<?php echo $fv;?>";
var cSerie     = "<?php echo $serie;?>";
var cCantidad  = "<?php echo $cCantidad;?>";
var cDetalle   = "<?php echo $detalle;?>";
var cIGV       = "<?php echo $igv;?>";

cUnidxemp      = parseFloat(cUnidxemp);
cMenudeo       = (cMenudeo=='1')? true:false;
cLote          = (cLote=='1')?    true:false;
cFv            = (cFv=='1')?      true:false;
cSerie         = (cSerie=='1')?   true:false;

//]]></script>
<?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<popup  id="oe-date-picker-popup" position="after_start" oncommand="RecibeCalendario( this )" value=""/>	
<!-- Movimientos Encabezado -->
<vbox align="center" pack="center" collapsed="<?php echo $headkardex;?>">
  <caption style="font-size: 15px;font-weight: bold; padding-top:2em;" 
	   label="<?php echo _($theadkardex)?>"  
           collapsed="<?php echo $tkardex;?>"/>
</vbox>

<!-- Movimientos Encabezado Producto -->
<vbox align="center"  pack="center" id="boxmovimientoshead" collapsed="<?php echo $mval?>">
  <spacer style="height:0.8em"/>
  <caption style="font-size: 11px;font-weight: bold;" label="<?php echo _($producto)?>"/>
</vbox>

<!-- Existencias Encabezado -->
<vbox  id="boxexistenciashead" align="center" pack="center" collapsed="<?php echo $eval?>">
  <spacer style="height:0.8em"/>
  <caption style="font-size: 11px;font-weight: bold;" label="<?php echo _($producto)?>"/>
</vbox>

<vbox align="center" pack="center" collapsed="<?php echo $rheadkardex;?>">
  <groupbox>
    <radiogroup flex="1" orient="horizontal" >
      <radio label="Movimientos" style="font-size: 11px;font-weight: bold;" 
	     oncommand="setDecKardex('movimientos')"  <?php echo $selmval?>/> 
      <radio label="Existencias" style="font-size: 11px;font-weight: bold;"
	     oncommand="setDecKardex('existencias');" <?php echo $seleval?>/> 
    </radiogroup>
  </groupbox>
</vbox>

<!-- Movimientos Buscar -->
<hbox id="boxmovimientosbuscar" pack="center" collapsed="<?php echo $mval?>">
  <vbox>
    <description value="Desde:"/>
    <datepicker id="FechaDesde" type="popup" onblur="buscarMovimientos();"/>
  </vbox>
  <vbox>
    <description value="Hasta:"/>
    <datepicker id="FechaHasta" type="popup" onblur="buscarMovimientos();"/>
  </vbox>
  <vbox>
    <description>Operación:</description>
    <menulist id="filtroOperacion" label="FiltroOperacion" oncommand="buscarMovimientos();">
      <menupopup>
	<menuitem value="0" label="Todos" selected="true"/>
	<?php echo genXulKardexOperaciones($selected=false,$xul="menuitem");?>
      </menupopup>
    </menulist>
  </vbox>
  <vbox>
    <description>Movimiento:</description>
    <menulist id="filtroMovimiento" label="FiltroMovimiento" oncommand="buscarMovimientos();">
      <menupopup>
	<menuitem value="0" label="Todos" selected="true"/>
	<menuitem value="Entrada" label="Entrada"/>
	<menuitem value="Salida"  label="Salida"/>
      </menupopup>
    </menulist>
  </vbox>
  <vbox style="margin-top:.9em">
    <button image="img/gpos_buscar.png" label=" Buscar" oncommand="buscarMovimientos();"/>
  </vbox>
  <vbox style="margin-top:.9em">
    <button image="img/gpos_pdf_ico.png" label="" oncommand="imprimir();"/>
  </vbox>
  <textbox id="IdProducto" value="<?php echo $id; ?>" collapsed="true"/>
  <textbox id="idalmacen" value="<?php echo $idalmacen;?>" collapsed="true"/>
</hbox>

<!-- Movimientos Resumen -->
<vbox id="boxmovimientosresumen" collapsed="<?php echo $mval?>"> 
  <hbox flex="1"  style="padding-top:1em;padding-bottom:-1px;">
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Movimientos") ?>" />
    <hbox  flex="1" pack="center">

      <description value="Existencias:"/>
      <caption id="kdxExistencias" label="" />

      <description value="Costo Promedio:"/>
      <caption id="kdxCostoPromedio" label="" />

      <description value="Costo Promedio Total:"/>
      <caption id="kdxCostoTotalPromedio" label=""/>

    </hbox>
  </hbox>
</vbox>

<!-- Movimientos -->
<vbox id="boxmovimientos" flex="1" collapsed="<?php echo $mval?>"> 
  <grid>
    <columns>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="1"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="1"/>
      <column flex="8"/>
    </columns>
    <rows>
      <row style="background-color:#292929;padding-top:0.2em;color:#c9c9c9; ">
	<description value=" # " style="font-style:italic;"/>
	<description value="Fecha Movimiento"/>
	<description value="Operación"/>
	<description value="Documento"/>
	<description value="Detalle"/>
	<description value="Movimiento"/>
	<description value="Cantidad"/>
	<description value="Costo Unitario"/>
	<description value="Importe"/>
	<description value="Saldo"/>
	<description value="Usuario"/>
      </row>
      <rows id="contenedor">
      </rows>
    </rows>
  </grid>
</vbox>


<!-- Web Extra -->
<hbox flex="1" id="boxdetalleskardex"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webdetalleskardex" name="webdetalleskardex" class="AreaDetallesKardex"  src="about:blank" flex="1"/>
</hbox>


<!-- Existencias -->
<vbox id="boxexistencias" flex="1" style="overflow: auto;"  align="center" collapsed="<?php echo $eval?>">  
  <spacer style="height:0.6em"/>
  <hbox>
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Existencias") ?>" />
  </hbox>

  <groupbox  flex="1"  style="min-width:65em;overflow:auto;" >
    <grid>
      <columns>
	<column />
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
      </columns>
      <rows id="contenedorexistencias">
	<row style="background-color:#292929;padding-top:0.2em;color:#c9c9c9;">
	  <description value =" # " style="font-style:italic;"/>
	  <description value ="Cantidad"/>
	  <description value ="Costo Unitario"/>
	  <description value ="Costo Total"/>
	  <description value ="Número Series" collapsed="<?php echo $esserie?>"/>
	  <description value ="Lote" collapsed="<?php echo $eslote?>"/>
	  <description value ="Vencimiento" collapsed="<?php echo $esfv?>"/>
	  <description value ="Cantidad Elegida" collapsed="<?php echo $esCarrito?>"/>
	</row>
      </rows>
    </grid>
  </groupbox>
 
  <groupbox collapsed="<?php echo $esCarrito?>">
    <grid>
      <columns>
	<column />
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
      </columns>
      <rows id="contenedorcarrito">
	<row>
	  <caption label="Total Cantidad Elegida:"/>
	  <description id="CantidadCarrito" value="0"/>
	  <textbox id="CantidadAjuste" value="<?php echo $xajuste?>" collapsed="true" />
	  <caption label="Costo:"/>
	  <description id="CostoCarrito" value="0"/>
	</row>
      </rows>
    </grid>

  <vbox flex="1" align="center" pack="top" >
    <spacer style="height:0.9em"/>
    <hbox>
      <button id="btnguardar" image='img/gpos_compras.png' label=' Aceptar' oncommand='<?php echo $btnAceptar;?>' />
      <button id="btncancelar" image='img/gpos_vaciarcompras.png' label=' Cancelar' oncommand='<?php echo $btnCancelar; ?>' />
    </hbox>
  </vbox>

  </groupbox>
  <spacer style="height:3em"/>
  <hbox>
    <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Resumen") ?>" />
  </hbox>

  <groupbox>
    <grid>
      <columns>
	<column />
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
	<column flex="1"/>
      </columns>
      <rows id="contenedorexistencias">
	<row>
	  <description value="Existencias:"/>
	  <caption id="total_existencias" label=""></caption>
	  <description value="Costo Promedio:"/>
	  <caption id="costo_unitario" label=""></caption>
	  <description value="Costo Total:"/>
	  <caption id="total_costo" label=""></caption>
	  <textbox id="cart_existencias" value="" collapsed="true" />
	  <textbox id="cart_costopromedio" value="" collapsed="true" />
	</row>
      </rows>
    </grid>
  </groupbox>


  <vbox flex="1">
  </vbox>
</vbox>

<vbox collapsed="<?php echo $btnrtn;?>">
  <box flex="1"></box>
  <button style="font-weight: bold;font-size:13px;" id="btnvolveralmacen" class="media" 
	  image="img/gpos_volver.png"  label="<?php echo $btnrtxt;?>" 
	  oncommand="<?php echo $btnrtnacc; ?>" collapsed="false"/>
</vbox>

<script>//<![CDATA[
var arreglo;
var manejaserie     = 1;
var series          = new Array();
var seriesProducto  = new Array();
var costosunitarios = new Array();
var aCantidad       = new Array();
var aPedidoDet      = new Array();
var aSeries         = new Array();
buscarMovimientos();
<?php echo $LoadResumen;?>

//]]></script>

<?php
EndXul();
?>
