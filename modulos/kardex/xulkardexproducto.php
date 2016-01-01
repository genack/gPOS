<?php
StartXul('Movimientos Kardex',$predata="",$css='css/xulkardex.css');
StartJs($js='modulos/kardex/kardexproducto.js?v=1');
?>

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
<vbox class="box" align="center" pack="center" collapsed="<?php echo $headkardex;?>">
  <caption class="h1" collapsed="<?php echo $tkardex;?>"><?php echo _($theadkardex)?></caption>
</vbox>

<!-- Movimientos Encabezado Producto -->
<vbox align="center"  class="box"  pack="center" id="boxmovimientoshead" collapsed="<?php echo $mval?>">
  <caption class="xproducto" label="<?php echo _($producto)?>"/>  
</vbox>

<!-- Existencias Encabezado -->
<vbox  id="boxexistenciashead"  class="box" align="center" pack="center" collapsed="<?php echo $eval?>">
  <caption class="xproducto" label="<?php echo _($producto)?>"/>
</vbox>

<vbox align="center" pack="center" collapsed="<?php echo $rheadkardex;?>"  class="box">
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
<hbox id="boxmovimientosbuscar"  class="box" pack="center" collapsed="<?php echo $mval?>">
  <vbox>
    <description value="Desde:"/>
    <datepicker id="FechaDesde" type="popup" value="<?php echo $desde?>" 
                onblur="buscarMovimientos();"/>
  </vbox>
  <vbox>
    <description value="Hasta:"/>
    <datepicker id="FechaHasta" type="popup" value="<?php echo $hasta?>"
                onblur="buscarMovimientos();"/>
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
  <vbox style="margin-top:1.3em">
    <button class="btn" image="<?php echo $_BasePath; ?>img/gpos_buscar.png" label=" Buscar" 
            oncommand="buscarMovimientos();"/>
  </vbox>
  <vbox style="margin-top:1.3em">
    <button class="btn" image="<?php echo $_BasePath; ?>img/gpos_pdf_ico.png" label="" 
            oncommand="imprimir();"/>
  </vbox>
  <textbox id="IdProducto" value="<?php echo $id; ?>" collapsed="true"/>
  <textbox id="idalmacen" value="<?php echo $idalmacen;?>" collapsed="true"/>
</hbox>

<!-- Movimientos Resumen -->
<vbox id="boxmovimientosresumen"  class="box" collapsed="<?php echo $mval?>"> 
  <caption class="box" label="<?php echo _("Movimientos") ?>" />
</vbox>

<!-- Movimientos -->
<vbox id="boxmovimientos"  class="box" flex="1" collapsed="<?php echo $mval?>"> 
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
      <row class="movimientos">
	<label value=" # " />
	<label value="Fecha Movimiento"/>
	<label value="Operación"/>
	<label value="Documento"/>
	<label value="Detalle"/>
	<label value="Movimiento"/>
	<label value="Cantidad"/>
	<label value="Costo Unitario"/>
	<label value="Importe"/>
	<label value="Saldo"/>
	<label value="Usuario"/>
      </row>
      <rows id="contenedor">
      </rows>
    </rows>
  </grid>

</vbox>

<vbox id="boxmovimientoscore" class="box"  flex="1" pack="top" aling="left" collapsed="<?php echo $mval?>">
  <hbox align="left" >
    <caption class="box" label="<?php echo _("Resumen") ?>" />
  </hbox>
  <hbox class="resumen" style="border-top:1px solid #eee;">
    <label value="Existencias:"/>
    <description id="kdxExistencias" value="" />
    
    <label value="Costo Promedio:"/>
    <description id="kdxCostoPromedio" value="" />
    
    <label value="Costo Promedio Total:"/>
    <description id="kdxCostoTotalPromedio" value=""/>
  </hbox>
</vbox>


<!-- Web Extra -->
<hbox flex="1" id="boxdetalleskardex"  collapsed="true" style="height:50em;" pack="center"> 
  <iframe  id="webdetalleskardex" name="webdetalleskardex" class="AreaDetallesKardex"  src="about:blank" flex="1"/>
</hbox>


<!-- Existencias -->
<vbox id="boxexistencias"  class="box" flex="1" style="overflow: auto;"  align="center" collapsed="<?php echo $eval?>">  

  <groupbox  flex="1"  style="min-width:65em;overflow:auto;" >
    <caption class="box" label="<?php echo _("Existencias") ?>" />
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
	<row class="movimientos">
	  <label value =" # "/>
	  <label value ="Cantidad"/>
	  <label value ="Costo Unitario"/>
	  <label value ="Costo Total"/>
	  <label value ="Número Series" collapsed="<?php echo $esserie?>"/>
	  <label value ="Lote" collapsed="<?php echo $eslote?>"/>
	  <label value ="Vencimiento" collapsed="<?php echo $esfv?>"/>
	  <label value ="Cantidad Elegida" collapsed="<?php echo $esCarrito?>"/>
	</row>
      </rows>
    </grid>
  </groupbox>
 
  <groupbox collapsed="<?php echo $esCarrito?>">
  <hbox id="boxmovimientoscore" class="resumen box" flex="1" pack="center" collapsed="<?php echo $mval?>">

    <label value="Existencias:"/>
    <description id="kdxExistencias" value="" />
    
    <label value="Costo Promedio:"/>
    <description id="kdxCostoPromedio" value="" />
    
    <label value="Costo Promedio Total:"/>
    <description id="kdxCostoTotalPromedio" value=""/>
    </hbox>


    <vbox id="contenedorcarrito"  flex="1" pack="top" align="left" >
      <hbox align="left" >
	<caption class="box" label="<?php echo _("Total") ?>" />
      </hbox>
      <hbox class="resumen" style="border-top:1px solid #eee;">
	<!-- description value="Existencias:"/>
	<caption id="total_existencias" value="" / -->

	<description value="Cantidad Elegida:"/>
	<label id="CantidadCarrito" value="0"/>
	<textbox id="CantidadAjuste" value="<?php echo $xajuste?>" collapsed="true" />
	<description  value="Costo:"/>
	<label id="CostoCarrito" value="0"/>

      </hbox>
    </vbox>

  <vbox flex="1" align="center" pack="top" >
    <spacer style="height:0.9em"/>
    <hbox>
      <button class="btn" id="btnguardar" image="<?php echo $_BasePath; ?>img/gpos_compras.png"
              label=" Aceptar" oncommand="<?php echo $btnAceptar;?>"/>
      <button class="btn" id="btncancelar" image="<?php echo $_BasePath; ?>img/gpos_vaciarcompras.png" 
              label=" Cancelar" oncommand="<?php echo $btnCancelar; ?>" />
    </hbox>
  </vbox>

  </groupbox>
  <spacer style="height:1em"/>

<vbox id="contenedorexistencias"  flex="1" pack="top" align="left" >
  <hbox align="left" >
    <caption class="box" label="<?php echo _("Resumen") ?>" />
  </hbox>
  <hbox class="resumen" style="border-top:1px solid #eee;">
    <description value="Existencias:"/>
    <caption id="total_existencias" value="" />
    
    <description value="Costo Promedio:"/>
    <caption id="costo_unitario" value="" />
    
    <description value="Costo Promedio Total:"/>
    <caption id="total_costo" value=""/>
    <textbox id="cart_existencias" value="" collapsed="true" />
    <textbox id="cart_costopromedio" value="" collapsed="true" />
  </hbox>
</vbox>

</vbox>

<vbox collapsed="<?php echo $btnrtn;?>"  class="box">
  <box flex="1"></box>
  <button style="font-weight: bold;font-size:13px;" id="btnvolveralmacen" class="media btn" 
	  image="<?php echo $_BasePath; ?>img/gpos_volver.png"  
          label="<?php echo $btnrtxt;?>" 
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
