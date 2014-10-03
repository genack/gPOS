<?php

include("../../tool.php");

header("Content-Type: application/vnd.mozilla.xul+xml");
header("Content-languaje: es");

$CabeceraXUL = '<#xml version="1.0" encoding="UTF-8"#>';
$CabeceraXUL .=	'<#xml-stylesheet href="chrome://global/skin/" type="text/css"#>';
$CabeceraXUL .= '<#xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"#>';
$CabeceraXUL = str_replace("#","?",$CabeceraXUL);

echo $CabeceraXUL;
$IdLocal   = CleanID(getSesionDato("IdTiendaDependiente"));
$TipoVenta = CleanText(getSesionDato("TipoVentaTPV"));

?>

<window id="ventana-principal" title="ventana principal"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">       	
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />

  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

  <groupbox  flex="1">
    <hbox align="start" flex="1">
      <hbox>
	  <!--description><?php echo _("Fecha de arqueo:") ?></description-->
          <caption label="Arqueos de caja"/>
	  <menulist label="<?php echo _("Elige arqueo...") ?>" id="SeleccionArqueo">
	  <menupopup id="itemsArqueo">
	    <menuitem label="<?php echo _("Elige arqueo...")?>"/>      
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
    <description  style="text-decoration: underline"><html:u><?php echo _("Movimientos de caja:") ?></html:u></description>
    <listbox collapsed="false" id="listaMovimientos" flex="1">
      <listcols>
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="2" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
      </listcols>
      <listhead>
	<listheader label="<?php echo _("Fecha") ?>" />
	<listheader label="<?php echo _("Operación") ?>" />
	<listheader label="<?php echo _("Concepto") ?>" />
        <listheader label="<?php echo _("Importe") ?>"/>
	<listheader label="<?php echo _("Usuario") ?>" />
      </listhead>
    </listbox>

    <groupbox flex="1">
      <caption label="<?php echo _("Arqueo actual") ?>"/>

      <hbox pack="center">

	<hbox>
	  <grid style="background-color: white;border: 2px #ccc solid;">
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

	<grid style="background-color: white;border: 2px #ccc solid;">
	  <rows>
	    <row style="background-color: #ccc;padding-bottom:.3em">
	      <description  class="plain" style="text-decoration: underline"><?php echo _("CIERRE CAJA") ?></description>
	    </row>
	    <row>
	      <textbox  class="plain cjacierre"  id='cierreCajaText' 
                        value="<?php echo $Moneda[1]['S']?> 0.00"/>
	    </row>

	    <row style="background-color: #ccc;padding-top:1em">
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
	  <button id="botonAbrir" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_abrir.png" label=" <?php echo _("  Abrir caja") ?> "  disabled="false"   flex="1" oncommand="Comando_AbrirCaja()"/>
	  <button id="botonCerrar" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_cerrar.png" label=" <?php echo _(" Cerrar caja") ?> " disabled="false"  flex="1" oncommand="Comando_CerrarCaja()"/>
	  <button id="botonArqueo" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_arqueo.png" label=" <?php echo _(" Arqueo caja") ?> " flex="1" oncommand="Comando_ArqueoCaja()"/>
	</vbox>
      </hbox>
      <spacer style="height: 16px"/>
      <tabbox  flex="1">
	<tabs >
	  <tab label="<?php echo _("Aportación") ?>"/>
	  <tab label="<?php echo _("Sustracción") ?>"/>
	  <tab label="<?php echo _("Ingreso") ?>"/>
	  <tab label="<?php echo _("Gasto") ?>"/>
	</tabs>
	<tabpanels flex="1" >
	  <groupbox>
	    <hbox align="center">
              <description style="font-weight: zbold;width:6em" ><?php echo _("Partida:") ?></description>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaAport"
                              style="width:30em;" editable="true">
	        <menupopup id="itemsPartida">
    	          <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Aportacion",$TipoVenta) ?>
                  <menuitem label="Nueva partida" style="font-weight: bold;"/>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;width:6em;"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoText' value="" flex="1" onpaste="return false"
           	       onkeyup="convertToUpperCase(this);"
                       onkeypress="return soloAlfaNumerico(event);"/>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeText' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnAporte" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_aport.png" label="<?php echo _(" Registrar Aporte") ?>" oncommand="comando_HacerUnaOperacion('Aportacion')"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description style="font-weight: zbold;width:6em" ><?php echo _("Partida:") ?></description>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaSust"
			      style="width:30em;" editable="true">
	        <menupopup id="itemsPartida">
		  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Sustraccion",$TipoVenta)?>
                  <menuitem label="Nueva partida" style="font-weight: bold;"/>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;width:6em"><?php echo _("Concepto:") ?></description>
	      <textbox  id='conceptoTextSubs' value="" flex="1" onpaste="return false"
		        onkeyup="convertToUpperCase(this);"
                        onkeypress="return soloAlfaNumerico(event);"/>
	      <spacer style="width: 16px"/>		
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox  id='importeTextSubs' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnSustracion" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_sust.png" label="<?php echo _(" Registrar Sustracción") ?>" oncommand="comando_HacerUnaOperacion('Sustraccion')"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description style="font-weight: zbold;width:6em" ><?php echo _("Partida:") ?></description>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaIngreso"
                              style="width:30em;" editable="true">
	        <menupopup id="itemsPartida">
                  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Ingreso",$TipoVenta) ?>
                  <menuitem label="Nueva partida" style="font-weight: bold;"/>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;width:6em"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoTextIngreso' value="" flex="1" onpaste="return false"
		       onkeyup="convertToUpperCase(this);"
                       onkeypress="return soloAlfaNumerico(event);"/>
	      <spacer style="width: 16px"/>		
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeTextIngreso' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnIngreso" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_input.png" label="<?php echo _(" Registar Ingreso") ?>" oncommand="comando_HacerUnaOperacion('Ingreso')"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description style="font-weight: zbold;width:6em" ><?php echo _("Partida:") ?></description>
	      <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaGasto"
                              style="width:30em;" editable="true">
	        <menupopup id="itemsPartida">
		  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Gasto",$TipoVenta) ?>
                  <menuitem label="Nueva partida" style="font-weight: bold;"/>
  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Comprobante:") ?></description>
	      <menulist id="SeleccionDocumentoGasto" >
	        <menupopup id="itemsDocumento">
	          <menuitem label="<?php echo _("Boleta")?>" />      
	          <menuitem label="<?php echo _("Factura")?>" /> 
	          <menuitem label="<?php echo _("Voucher")?>" />
	          <menuitem label="<?php echo _("Ticket")?>" selected="true"/>

  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Código:") ?></description>
              <textbox id="CodigoTextGasto" value="" 
                       onkeypress="return soloNumericoCodigoSerie(event,this.value)"/>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Empresa:") ?></description>
	      <toolbarbutton id="btnSubsidiario" label="+"
                  oncommand="auxAltaSubsidiario()"/> 
	      <toolbarbutton id="btnSubsidiarioHab" label="..." oncommand="auxSubsidiarioHab()"/>
              <textbox id="EmpresaTextGasto" value="" flex="1" readonly="true"
                       onkeypress="return soloAlfaNumerico(event);"/>
              <textbox id="IdSubsidiario" value="" flex="1" collapsed="true"/>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold; width:6em"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoTextGasto' value="" flex="1" onpaste="return false"
		       onkeyup="convertToUpperCase(this);"
                       onkeypress="return soloAlfaNumerico(event);"/>
	      <spacer style="width: 16px"/>		
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeTextGasto' value="" onkeypress="return soloNumeros(event,this.value)"/>
	      <button id="btnGasto" image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png" label="<?php echo _(" Registrar Gasto") ?>" oncommand="comando_HacerUnaOperacion('Gasto')"/>
	    </hbox>
	  </groupbox>
	</tabpanels>          
      </tabbox>
    </groupbox> 
  </groupbox> 

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/arqueo/js/arquero.js?ver=1/r<?php echo rand(0,99999999); ?>"/>

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
