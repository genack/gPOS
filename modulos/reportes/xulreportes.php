<?php
header("Content-type: application/vnd.mozilla.xul+xml");

StartXul('Reportes',$predata="",$css='css/xulkardex.css');
StartJs($js='modulos/reportes/reportes.js?v=3.1');
?>

<script>//<![CDATA[
      var xreporte = "<?php echo $xreporte?>";
//]]></script>
<?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<popup  id="oe-date-picker-popup" position="after_start" oncommand="RecibeCalendario( this )" value=""/>	
<!-- Movimientos Encabezado -->
<vbox align="center" pack="center" class="box">
  <caption class="h1" 
           id="titleReportes"
	   label="<?php echo $Title ?>"/>
</vbox>

<!-- Movimientos Buscar -->
<hbox id="boxmovimientosbuscar" pack="center" class="box">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroLocal" oncommand="<?php echo $btnBuscar; ?>">
	  <menupopup id="combolocales">
	    <menuitem value="0" label="Todos"/>
	    <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>

  <!-- Utilidad -->
  <vbox id="vboxPeriodo" collapsed="<?php echo $Periodo; ?>">
    <description>Periodo:</description>
    <menulist id="FiltroPeriodo" oncommand="mostrarDatosExtra(this.value);">
      <menupopup>
	<menuitem value="Todo" label="Todo"/>
	<menuitem value="Dia" label="Días" selected="true"/>
	<menuitem value="Semana" label="Semanal" collapsed="true"/>
	<menuitem value="Mes"  label="Mensual"/>
	<menuitem value="Trimestre"  label="Trimestral"/>
	<menuitem value="Semestre"  label="Semestral"/>
	<menuitem value="Anio"  label="Anual"/>
	<menuitem value="EntreFecha"  label="Entre Fechas"/>
      </menupopup>
    </menulist>
  </vbox>
  <vbox id="vboxOpcionVence" collapsed="<?php echo $Vence; ?>">
    <description>Estado:</description>
    <menulist id="FiltroEstado" oncommand="mostrarDatosExtraVence(this.value);">
      <menupopup>
	<menuitem value="Vencido" label="Vencido" selected="true"/>
	<menuitem value="PorVencer" label="Por Vencer" />
      </menupopup>
    </menulist>
  </vbox>
  <vbox id="vboxDesde" collapsed="<?php echo $FechaDesde; ?>">
    <description id="descDesde" value="Fecha:"/>
    <datepicker id="FechaDesde" type="popup" value=""/>
  </vbox>
  <vbox id="vboxHasta" collapsed="true">
    <description value="Hasta:"/>
    <datepicker id="FechaHasta" type="popup" value=""/>
  </vbox>
  <vbox id="vboxMes" collapsed="true">
    <description>Mes:</description>
    <menulist id="filtroMes" >
      <menupopup id="menuMes">
	<menuitem value="1" label="Enero" />
	<menuitem value="2" label="Febrero" />
	<menuitem value="3" label="Marzo" />
	<menuitem value="4" label="Abril" />
	<menuitem value="5" label="Mayo" />
	<menuitem value="6" label="Junio" />
	<menuitem value="7" label="Julio" />
	<menuitem value="8" label="Agosto" />
	<menuitem value="9" label="Setiembre" />
	<menuitem value="10" label="Octubre" />
	<menuitem value="11" label="Noviembre" />
	<menuitem value="12" label="Diciembre" />
      </menupopup>
    </menulist>
  </vbox>
  <vbox id="vboxTrimestre" collapsed="true">
    <description>Trimestre:</description>
    <menulist id="filtroTrimestre" >
      <menupopup id="menuTrimestre">
	<menuitem value="1" label="1er Trimestre" />
	<menuitem value="2" label="2do Trimestre" />
	<menuitem value="3" label="3er Trimestre" />
	<menuitem value="4" label="4to Trimestre" />
      </menupopup>
    </menulist>
  </vbox>
  <vbox id="vboxSemestre" collapsed="true">
    <description>Semestre:</description>
    <menulist id="filtroSemestre" >
      <menupopup id="menuSemestre">
	<menuitem value="1" label="1er Semestre" />
	<menuitem value="2" label="2do Semestre" />
      </menupopup>
    </menulist>
  </vbox>

  <vbox id="vboxAnio" collapsed="true">
    <description>Año:</description>
    <menulist id="filtroAnio" >
      <menupopup id="elementosanio">
      </menupopup>
    </menulist>
  </vbox>
  <!-- /Utilidad -->



  <vbox style="margin-top:1.1em">
    <button image="<?php echo $_BasePath; ?>img/gpos_buscar.png" label=" Buscar" 
            oncommand="<?php echo $btnBuscar; ?>" class="btn"/>
  </vbox>
  <vbox style="margin-top:1.1em">
    <button image="<?php echo $_BasePath; ?>img/gpos_pdf_ico.png" label="" 
            oncommand="imprimir();" collapsed="<?php echo $btnprint ?>" class="btn"/>
  </vbox>

</hbox>

<!-- Reporte Resumen -->
<vbox id="boxmovimientosresumen" collapsed="<?php echo $listutilidad ?>" class="box"> 
    <caption class="box" 
             label="<?php echo _("Movimientos") ?>" />
</vbox>

<!-- Movimientos -->

<vbox id="boxmovimientos" flex="1" collapsed="<?php echo $listutilidad ?>"> 
  <grid>
    <columns>
      <column flex="0"/>
      <column flex="1"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="1"/>
      <column flex="8"/>
    </columns>
    <rows>
      <row style="background-color:#eee;padding-top:0.2em;font-style: italic; ">
	<description value=" # " style="font-style:italic;"/>
	<description value="Local"/>
	<description value="Costo"/>
	<description value="Impuesto"/>
	<description value="Ingreso"/>
	<description value="Utilidad"/>
      </row>
      <rows id="contenedor">
      </rows>
    </rows>
  </grid>
</vbox>

<vbox id="boxmovimientosresumen" collapsed="<?php echo $listutilidad ?>" class="box"> 
    <caption class="box" 
             label="<?php echo _("Resumen Movimientos") ?>" />
    <hbox align="left" pack="center" class="resumen">
      <label value="Total Costo:"/>
      <description id="totalCosto" value="0.00" />

      <label value="Total Impuesto:"/>
      <description id="totalImpuesto" value="0.00" />

      <label value="Total Importe:"/>
      <description id="totalImporte" value="0.00"/>

      <label value="Total Utilidad:"/>
      <description id="totalUtilidad" value="0.00"/>
    </hbox>
</vbox>


<!-- Vencimiento Resumen -->
<vbox id="boxvencimientosresumen" collapsed="<?php echo $listvencidos ?>" class="box"> 
    <caption class="box" 
             label="<?php echo _("Vencimientos") ?>" />
</vbox>

<!-- Vencimientos -->
<vbox id="boxvencimientos" flex="1" collapsed="<?php echo $listvencidos ?>" class="box"> 
  <grid>
    <columns>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="3"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="0"/>
      <column flex="6"/>
    </columns>
    <rows>
      <row style="background-color:#eee;padding-top:0.2em;color:#c9c9c9;font-style:italic; ">
	<description value=" # " style="font-style:italic;"/>
	<description value="Local"/>
	<description value="CB"/>
	<description value="Producto"/>
	<description value="Stock"/>
	<description value="Fecha Vencimiento"/>
	<description value="Lote"/>
      </row>
      <rows id="vencimiento">
      </rows>
    </rows>
  </grid>
</vbox>

<vbox id="boxvencimientosresumen" collapsed="<?php echo $listvencidos ?>" class="box"> 
    <caption class="box" 
             label="<?php echo _("Resumen Vencimientos") ?>" />
    <hbox align="left" pack="center" class="resumen">

      <description value="Total Productos:"/>
      <caption id="totalProductos" label="" />

    </hbox>
</vbox>


<script>//<![CDATA[
	 switch(xreporte){
	 case 'utilidad':
         cargarDatosDefault();
         regenAnioReporte();
	 buscarMovimientos();
	 break;
	 case 'vencimiento':
	 buscarVencimientos();
	 break;
	 }
   <?php
     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";
   ?>


//]]></script>

<?php
EndXul();
?>
