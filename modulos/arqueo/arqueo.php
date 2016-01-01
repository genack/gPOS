<?php

include("../../tool.php");

$IdLocal   = CleanID(getSesionDato("IdTiendaDependiente"));
$TipoVenta = CleanText(getSesionDato("TipoVentaTPV"));
$Moneda = getSesionDato("Moneda"); 

StartXul('Caja TPV',$predata="",$css='');
StartJs($js='modulos/arqueo/js/arqueo.js?v=3.1');
?>

  <?php getMonedaJS($Moneda); ?>

  <!-- No Visuales /-->
  <popupset>
    <popup id="accionesMovimientosCaja" class="media">
      <menu label=" Exportar">
        <menupopup>
	  <menuitem label="<?php echo _("PDF"); ?>" 
		     oncommand="exportarMovimientosCaja('PDF')"/>
	  <menuitem label="<?php echo _("CSV"); ?>" 
                    oncommand="exportarMovimientosCaja('CSV')"/>
        </menupopup>
      </menu>
      <menuseparator />
	<menuitem label="<?php echo _("Modificar Concepto"); ?>" id="ConceptoOperacionCaja"
		  oncommand="editarOperacionCaja()"/> 
    </popup>
  </popupset>
  <!-- No Visuales /-->

  <hbox>	
    <html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
    <html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
    </html:div>
  </hbox>	
      
  <vbox  flex="1" class="box">
    <hbox align="start" flex="1">
      <hbox>
          <caption class="box" label="Arqueos de caja"/>
          <menulist id="filtroAnio" oncommand="actualizarArqueo()">
            <menupopup id="elementosanio">
            </menupopup>
          </menulist>
          <menulist id="filtroMes" label="Mes" oncommand="actualizarArqueo()">
            <menupopup id="menuMes">
	    <menuitem value="01" label="Enero" />
	    <menuitem value="02" label="Febrero" />
	    <menuitem value="03" label="Marzo" />
	    <menuitem value="04" label="Abril" />
	    <menuitem value="05" label="Mayo" />
	    <menuitem value="06" label="Junio" />
	    <menuitem value="07" label="Julio" />
	    <menuitem value="08" label="Agosto" />
	    <menuitem value="09" label="Setiembre" />
	    <menuitem value="10" label="Octubre" />
	    <menuitem value="11" label="Noviembre" />
	    <menuitem value="12" label="Diciembre" />
          </menupopup>
        </menulist>
	<menulist label="Elige arqueo..." id="SeleccionArqueo">
	  <menupopup id="itemsArqueo">
	    <menuitem value="0" label="Elige arqueo..."/>
  	  </menupopup>
        </menulist>
      </hbox>

      <button label="<?php echo _("Consultar caja") ?>" oncommand="onConsultarCaja()" collapsed="true"/> 
      <spacer flex="1"/>
      <vbox style="background-color: orange;text-align:center;font-size: 120%;font-weight: bold">
	<textbox id='estadoCajaTexto' class="cjagralcerrada plain big" value="--OFF--"/>
	<textbox id='estadoCajaFecha' class="cjagralcerrada plain big" value="--/--/---- --:--"/>
	<textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  class="plain big" collapsed="true" value="IdTienda: 
	  <?php  echo CleanID(getSesionDato("IdTiendaDependiente")); ?>"/>
      </vbox>
    </hbox>
    <textbox id="log" flex="1" multiline="true" wrap="off" collapsed="true"/>  
    <caption class="box" label="Movimientos de caja:"></caption>
    <listbox collapsed="false" id="listaMovimientos" flex="1" contextmenu="accionesMovimientosCaja" onclick="revisarOperacionCaja()">
      <listcols>
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="2" />
	<splitter class="tree-splitter" />
	<listcol flex="2" />
	<splitter class="tree-splitter" />
	<listcol flex="2" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
      </listcols>
      <listhead>
	<listheader label="<?php echo _("Fecha") ?>" />
	<listheader label="<?php echo _("Operación") ?>" />
	<listheader label="<?php echo _("Cliente") ?>" />
	<listheader label="<?php echo _("Concepto") ?>" />
        <listheader label="<?php echo _("Importe") ?>"/>
	<listheader label="<?php echo _("Usuario") ?>" />
      </listhead>
    </listbox>

    <groupbox flex="1">
      <caption class="box" label="<?php echo _("Arqueo actual") ?>"/>

      <hbox pack="center">

	<hbox>
	  <grid class="consolidado" style="background-color: white;border: 0px #ccc solid;">
	    <rows>
	      <row>
		<description  style="text-decoration: underline"><?php echo _("SALD0 INICIAL:") ?></description>
		<textbox  class="plain arqueoaling"  id='saldoInicialText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("+INGRESOS:") ?></description>
		<textbox class="plain arqueoaling"   id='ingresosText' 
                         value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("-GASTOS:") ?></description>
		<textbox  class="plain arqueoaling"  id='gastosText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("+APORTACIONES:") ?></description>
		<textbox  class="plain arqueoaling"  id='aportacionesText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("-SUSTRACCIONES:") ?></description>
		<textbox  class="plain arqueoaling"  id='sustraccionesText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("=TEORICO CIERRE") ?></description>
		<textbox  class="plain arqueoaling"  id='TeoricoCierre' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	    </rows>
	  </grid>
	</hbox>

	<spacer style="width: 16px"/>

	<grid class="consolidado" style="background-color: white;border: 0px #ccc solid;">
	  <rows>
	    <row style="padding-bottom:.3em">
	      <description  class="plain" style="text-decoration: underline"><?php echo _("CIERRE CAJA") ?></description>
	    </row>
	    <row>
	      <textbox  class="plain cjacierre"  id='cierreCajaText' 
                        value="<?php echo $Moneda[1]['S']?> 0.00"/>
	    </row>

	    <row style="padding-top:1em">
            <hbox>
	      <description class="plain" style="text-decoration: underline"><?php echo _("DESCUADRE CAJA:")?></description>
              <description id="titledescuadre"> </description>
            </hbox>
	    </row>
	    <row >
	      <textbox  class="plain" id='descuadreCajaText'  
                        value="<?php echo $Moneda[1]['S']?> 0.00"/>
	    </row >

	    <row id="row_utilidadventacajatitle" style="background-color: #ccc" collapsed="true">
	      <description style="text-decoration: underline"><?php echo _("UTILIDAD VENTA")?></description>
	    </row>
	    <row id="row_utilidadventacaja" collapsed="true">
	      <textbox  class="plain" id='utilidadVentaCajaText' 
                        style="font-size: 120%;font-weight: bold;text-align:center;
                               text-decoration: underline" 
                        value="<?php echo $Moneda[1]['S']?> 0.00"/>
	    </row>
	  </rows>
	</grid>
	<spacer style="width: 16px"/>
	<vbox>
	  <button id="botonAbrir" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_abrir.png" label=" <?php echo _("  Abrir caja") ?> "  disabled="false"   flex="1" oncommand="Comando_AbrirCaja()" class="btn"/>
	  <button id="botonCerrar" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_cerrar.png" label=" <?php echo _(" Cerrar caja") ?> " disabled="false"  flex="1" oncommand="Comando_CerrarCaja()" class="btn"/>
	  <button id="botonArqueo" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_arqueo.png" label=" <?php echo _(" Arqueo caja") ?> " flex="1" oncommand="Comando_ArqueoCaja()" class="btn"/>
	</vbox>
      </hbox>
      <spacer style="height: 16px"/>
      <tabbox  flex="1">
	<tabs >
	  <tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_aport.png"
               id="tab_aportacion" label="<?php echo _(" Aportación") ?>"/>
	  <tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_sust.png"
               id="tab_sustraccion" label="<?php echo _(" Sustracción") ?>"/>
	  <tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_input.png"
               id="tab_ingreso" label="<?php echo _(" Ingreso") ?>"/>
	  <tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png"
               id="tab_gasto" label="<?php echo _(" Gasto") ?>"/>
	</tabs>
	<tabpanels flex="1" id="tab_boxoperacion" class="box">
	  <groupbox>
	    <hbox align="center">
              <caption label="<?php echo _("Partida:") ?>"/>
	      <toolbarbutton id="btnSeleccionPartidaAport" label=" + "
                         oncommand="CogePartidaCaja('Aportacion')"/>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaAport"
                              style="width:30em;">
	        <menupopup id="elementosPartidaAportacion">
    	          <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Aportacion",$TipoVenta) ?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
              <caption label="Concepto:"/>
	      <textbox id='conceptoText' value="" flex="1" onpaste="return false"/>
	      <spacer style="width: 16px"/>
              <caption label="Importe:"/>
	      <textbox id='importeText' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnAporte" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_aport.png" label="<?php echo _(" Registrar Aporte ") ?>" oncommand="comando_HacerUnaOperacion('Aportacion')" class="btn"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <caption label="<?php echo _("Partida:") ?>"/>
	      <toolbarbutton id="btnSeleccionPartidaSust" label=" + "
                             oncommand="CogePartidaCaja('Sustraccion')"/>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaSust"
			      style="width:30em;" >
	        <menupopup id="elementosPartidaSustraccion">
		  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Sustraccion",$TipoVenta)?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
              <caption label="Concepto:"/>
	      <textbox  id='conceptoTextSubs' value="" flex="1" onpaste="return false"/>
	      <spacer style="width: 16px"/>
              <caption label="Importe:"/>
	      <textbox  id='importeTextSubs' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnSustracion" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_sust.png" label="<?php echo _(" Registrar Sustracción ") ?>" oncommand="comando_HacerUnaOperacion('Sustraccion')" class="btn"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <caption label="Partida:"/>
 	      <toolbarbutton id="btnSeleccionPartidaIngreso" label=" + "
                             oncommand="CogePartidaCaja('Ingreso')"/>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaIngreso"
                              style="width:30em;">
	        <menupopup id="elementosPartidaIngreso">
                  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Ingreso",$TipoVenta) ?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
              <caption label="Concepto:"/>
	      <textbox id='conceptoTextIngreso' value="" flex="1" onpaste="return false"/>
	      <spacer style="width: 16px"/>		
              <caption label="Importe:"/>
	      <textbox id='importeTextIngreso' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnIngreso" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_input.png" label="<?php echo _(" Registar Ingreso ") ?>" oncommand="comando_HacerUnaOperacion('Ingreso')" class="btn"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <caption label="Partida:"/>
	      <toolbarbutton id="btnSeleccionPartidaGasto" label=" + "
                             oncommand="CogePartidaCaja('Gasto')"/>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaGasto"
                              style="width:30em;" >
	        <menupopup id="elementosPartidaGasto">
		  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Gasto",$TipoVenta) ?>
  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
              <caption label="Comprobante:"/>
	      <menulist id="SeleccionDocumentoGasto" >
	        <menupopup id="itemsDocumento">
	          <menuitem label="<?php echo _("Boleta")?>" />      
	          <menuitem label="<?php echo _("Factura")?>" /> 
	          <menuitem label="<?php echo _("Voucher")?>" />
	          <menuitem label="<?php echo _("Ticket")?>" selected="true"/>

  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
              <caption label="Código:"/>
              <textbox id="CodigoTextGasto" value="" 
                       onkeypress="return soloNumericoCodigoSerie(event,this.value)"/>
	      <spacer style="width: 16px"/>
              <caption label="Empresa:"/>
	      <toolbarbutton id="btnSubsidiario" label="+"
                  oncommand="auxAltaSubsidiario()"/> 
	      <toolbarbutton id="btnSubsidiarioHab" label="..." oncommand="auxSubsidiarioHab()"/>
              <textbox id="EmpresaTextGasto" value="" flex="1" readonly="true"
                       onkeypress="return soloAlfaNumerico(event);"/>
              <textbox id="IdSubsidiario" value="" flex="1" collapsed="true"/>
            </hbox>
	    <hbox align="center">
              <caption label="Concepto:"/>
	      <textbox id='conceptoTextGasto' value="" flex="1" onpaste="return false"/>
	      <spacer style="width: 16px"/>		
              <caption label="Importe:"/>
	      <textbox id='importeTextGasto' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnGasto" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png" label="<?php echo _(" Registrar Gasto ") ?>" oncommand="comando_HacerUnaOperacion('Gasto')" class="btn"/>
	    </hbox>
	  </groupbox>
	</tabpanels>          
      </tabbox>
    </groupbox> 
  </vbox> 

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>

<script>//<![CDATA[


document.onkeydown = function(event) {   
//alert(event.keyCode); 
    switch (event.keyCode) { 

        case 112 : 
	parent.VerTPV();
	break;

        case 113 : 
	parent.MostrarUsuariosForm();
	break;

        case 118 : 
	parent.selTipoPresupuesto(2);
	parent.id("buscapedido").focus(); 
	break;

        case 119 : 
	parent.selTipoPresupuesto(1);
	parent.id("buscapedido").focus(); 
	break;

        case 120 : 
	parent.VerVentas();
	break;

    }
}


var Local = new Object();
Local.IdLocalActivo = '<?php echo CleanID(getSesionDato("IdTiendaDependiente")) ?>';

setTimeout("onLoadFormulario()",300);


//]]></script>




</window>
