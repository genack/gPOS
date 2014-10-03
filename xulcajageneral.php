<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-Type: application/vnd.mozilla.xul+xml");
//header("Content-languaje: es");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';

?>
<window id="cajageneral" title="Caja General"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">       	
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cajageneral.js" />
  <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;

  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

<!--Encabesdado-->
<vbox>
  <spacer style="height:6px"/>
  <hbox pack="center" collapsed="<?php echo $blockprov;?>">
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Caja General") ?>
    </caption>
  </hbox>
  <spacer style="height:6px"/>
</vbox>

<vbox>
  <!--groupbox  flex="1"-->
    <caption label="Arqueos de caja"/>

    <hbox align="start" flex="1">
      <hbox>
	  <description><?php echo _("Fecha de arqueo:") ?></description>
	  <menulist label="<?php echo _("Elige arqueo...") ?>" id="SeleccionArqueo">
	  <menupopup id="itemsArqueo">
	    <menuitem label="<?php echo _("Elige arqueo...")?>"/>      
  	  </menupopup>
        </menulist>
      </hbox>

      <button label="<?php echo _("Consultar caja") ?>" oncommand="onConsultarCaja()" collapsed="true"/> 
      <spacer flex="1"/>
      <vbox style="background-color: orange;text-align:center;font-size: 120%;font-weight: bold">
	<textbox id='estadoCajaTexto' style="background-color: orange;border: 0px;"  class="plain big" value="--OFF--"/>
	<textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  class="plain big" value="--/--/---- --:--"/>
	<textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  class="plain big" collapsed="true" value="IdTienda: 
	  <?php  echo CleanID(getSesionDato("IdTiendaDependiente")); ?>"/>
      </vbox>
    </hbox>
    <textbox id="log" flex="1" multiline="true" wrap="off" collapsed="true"/>  
    <description  style="text-decoration: underline"><html:u><?php echo _("Movimientos de caja:") ?></html:u></description>
    <listbox collapsed="false" id="listaMovimientos" flex="1" style="height:15em">
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
	<listheader label="<?php echo _("Operación") ?>" />
	<listheader label="<?php echo _("Concepto") ?>" />
	<listheader label="<?php echo _("Importe") ?>" />
	<listheader label="<?php echo _("Hora") ?>" />
	<listheader label="<?php echo _("Usuario") ?>" />
      </listhead>
    </listbox>
  <spacer style="height: 8px"/>		
    <!--groupbox flex="1"-->
      <caption label="<?php echo _("Arqueo actual") ?>"/>

      <hbox pack="center">

	<hbox>
	  <grid style="background-color: white;border: 2px #ccc solid;">
	    <rows>
	      <row>
		<description  style="text-decoration: underline"><?php echo _("SALD0 INICIAL:") ?></description>
		<textbox  class="plain"  id='saldoInicialText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("+INGRESOS:") ?></description>
		<textbox class="plain"   id='ingresosText' 
                         value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("-GASTOS:") ?></description>
		<textbox  class="plain"  id='gastosText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("+APORTACIONES:") ?></description>
		<textbox  class="plain"  id='aportacionesText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("-SUSTRACCIONES:") ?></description>
		<textbox  class="plain"  id='sustraccionesText' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	      <row>
		<description  style="text-decoration: underline"><?php echo _("=TEORICO CIERRE") ?></description>
		<textbox  class="plain"  id='TeoricoCierre' 
                          value="<?php echo $Moneda[1]['S']?> 0.00"/>
	      </row>

	    </rows>
	  </grid>
	</hbox>

	<spacer style="width: 16px"/>

	<grid style="background-color: white;border: 2px #ccc solid;">
	  <rows>
	    <row style="background-color: #ccc">
	      <description  class="plain" style="text-decoration: underline"><?php echo _("CIERRE CAJA") ?></description>
	    </row>
	    <row>
	      <textbox  class="plain"  id='cierreCajaText' style="font-size: 120%;font-weight: bold;text-align:center;text-decoration: underline" value="0 Soles"/>
	    </row>

	    <row style="background-color: #ccc">
	      <description style="text-decoration: underline"><?php echo _("DESCUADRE CAJA")?></description>
	    </row>
	    <row>
	      <textbox  class="plain" id='descuadreCajaText' style="font-size: 120%;font-weight: bold;text-align:center;text-decoration: underline" value="0 Soles"/>
	    </row>
	  </rows>
	</grid>
	<spacer style="width: 16px"/>
	<vbox>
	  <button id="botonAbrir" image="<?php echo $_BasePath ?>/img/hacercaja.gif" label=" <?php echo _("Abrir caja") ?> "  disabled="false"   flex="1" oncommand="Comando_AbrirCaja()"/>
	  <button id="botonCerrar" image="<?php echo $_BasePath ?>/img/hacercaja.gif" label=" <?php echo _("Cerrar caja") ?> " disabled="false"  flex="1" oncommand="Comando_CerrarCaja()"/>
	  <button image="<?php echo $_BasePath ?>/img/actualizar.gif" label=" <?php echo _("Arqueo caja") ?> " flex="1" oncommand="Comando_ArqueoCaja()"/>
	</vbox>
      </hbox>
      <spacer style="height: 16px"/>
      <tabbox  flex="1" >
	<tabs style="font-size:10px; font-weight: zbold;">
	  <tab label="<?php echo _("Aportación") ?>"/>
	  <tab label="<?php echo _("Sustracción") ?>"/>
	  <tab label="<?php echo _("Ingreso") ?>"/>
	  <tab label="<?php echo _("Gasto") ?>"/>
	</tabs>
	<tabpanels flex="1" >
	  <groupbox>
	    <hbox align="center">
              <description id="opAportacion" label="Aportacion" style="font-weight: zbold;" ><?php echo _("Aportación:") ?></description>
	      <menulist label="<?php echo _("Elige partida...") ?>" id="SeleccionPartidaAport"
                              style="width:30em;">
	        <menupopup id="itemsPartida">
	          <menuitem label="<?php echo _("Elige partida...")?>"/>
                  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Aportacion") ?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoText' value="" flex="1"/>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeText' value=""/>
	      <button image="<?php echo $_BasePath ?>img/icoguardar.gif" label="<?php echo _("Guardar") ?>" oncommand="Comando_HacerUnAporte()"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description id="opSustraccion" label="Sustraccion" style="font-weight: zbold;"><?php echo _("Sustracción:") ?></description>
	      <menulist label="<?php echo _("Elige partida...") ?>" id="SeleccionPartidaSust"
			      style="width:30em;">
	        <menupopup id="itemsPartida">
	          <menuitem label="<?php echo _("Elige partida...")?>"/>
                  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Sustraccion")?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;"><?php echo _("Concepto:") ?></description>
	      <textbox  id='conceptoTextSubs' value="" flex="1"/>
	      <spacer style="width: 16px"/>		
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox  id='importeTextSubs' value=""/>
	      <button image="<?php echo $_BasePath ?>img/icoguardar.gif" label="<?php echo _("Guardar") ?>" oncommand="Comando_HacerUnaSubstraccion()"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description id="opIngreso" label="Ingreso" style="font-weight: zbold;"><?php echo _("Ingreso:") ?></description>
	      <menulist label="<?php echo _("Elige partida...") ?>" id="SeleccionPartidaIngreso"
                              style="width:30em;">
	        <menupopup id="itemsPartida">
	          <menuitem label="<?php echo _("Elige partida...")?>"/>
                  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Ingreso") ?>
  	        </menupopup>
              </menulist>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoTextIngreso' value="" flex="1"/>
	      <spacer style="width: 16px"/>		
	      <description  style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeTextIngreso' value=""/>
	      <button image="<?php echo $_BasePath ?>img/icoguardar.gif" label="<?php echo _("Guardar") ?>" oncommand="Comando_HacerUnIngreso()"/>
	    </hbox>
	  </groupbox>
	  <groupbox>
	    <hbox align="center">
              <description id="opGasto" label="Gasto" style="font-weight: zbold;"><?php echo _("Gasto:") ?></description>
	      <menulist label="<?php echo _("Elige partida...") ?>" id="SeleccionPartidaGasto"
                              style="width:30em;">
	        <menupopup id="itemsPartida">
	          <menuitem label="<?php echo _("Elige partida...")?>"/>
		  <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Gasto") ?>
  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Documento:") ?></description>
	      <menulist label="<?php echo _("Documento...") ?>" id="SeleccionDocumentoGasto" editable="true" >
	        <menupopup id="itemsDocumento">
	          <menuitem label="<?php echo _("Boleta")?>" selected="true"/>      
	          <menuitem label="<?php echo _("Factura")?>" /> 
	          <menuitem label="<?php echo _("Boucher")?>" />
	          <menuitem label="<?php echo _("Proforma")?>" />
	          <menuitem label="<?php echo _("Otro")?>" />
  	        </menupopup>
              </menulist>
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Código:") ?></description>
              <textbox id="CodigoTextGasto" value="" />
	      <spacer style="width: 16px"/>
	      <description style="font-weight: zbold;"><?php echo _("Empresa:") ?></description>
              <textbox id="EmpresaTextGasto" value="" flex="1"/>
            </hbox>
	    <hbox align="center">
	      <description style="font-weight: zbold;"><?php echo _("Concepto:") ?></description>
	      <textbox id='conceptoTextGasto' value="" flex="1" onclick="addDatoGasto()"/>
	      <spacer style="width: 16px"/>		
	      <description style="font-weight: zbold;"><?php echo _("Importe:") ?></description>
	      <textbox id='importeTextGasto' value="" collapsed="true"/>
	      <button image="<?php echo $_BasePath ?>img/icoguardar.gif" label="<?php echo _("Guardar") ?>" oncommand="GuardarOperacionCaja()"/>
	    </hbox>
	  </groupbox>
	</tabpanels>          
      </tabbox>

    <!--/groupbox--> 
  <!--/groupbox--> 
</vbox>

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/arqueo/js/arquero.js?ver=1/r<?php echo rand(0,99999999); ?>"/>

<script>//<![CDATA[

//var Local = new Object();
//Local.IdLocalActivo = '<?php echo CleanID(getSesionDato("IdTiendaDependiente")) ?>';

//setTimeout("onLoadFormulario()",300);


//]]></script>




</window>
