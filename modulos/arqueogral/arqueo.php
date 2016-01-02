<?php

//include("tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");
$Moneda = getSesionDato("Moneda"); 
StartXul('Caja General',$predata="",$css='');
StartJs($js='modulos/arqueogral/js/arqueogral.js?v=3.1.1');
?>

  <?php getMonedaJS($Moneda); ?>

  <!-- No Visuales /-->
  <popupset>
    <popup id="accionesMovimientosCajaGral" class="media">
      <menu label=" Exportar">
        <menupopup>
	  <menuitem label="<?php echo _("PDF"); ?>" 
		     oncommand="exportarMovimientosCajaGral('PDF')"/>
	  <menuitem label="<?php echo _("CSV"); ?>" 
                    oncommand="exportarMovimientosCajaGral('CSV')"/>
        </menupopup>
      </menu>
      <menuseparator />
      <menuitem label="<?php echo _("Modificar Concepto"); ?>" id="ConceptoOperacionCaja"
		oncommand="editarOperacionCajaGral()"/> 
    </popup>
  </popupset>
  <!-- No Visuales /-->

  <hbox>	
    <html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
    <html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
    </html:div>
  </hbox>	
      
<!--Encabesdado-->
<vbox class="box">
  <spacer style="height:6px"/>
  <hbox pack="center" collapsed="<?php echo $blockprov;?>">
    <caption id="titulocajagral" class="h1"
             label="Caja General">

    </caption>
  </hbox>

</vbox>

<vbox flex="1" class="box">
  <hbox align="start">
    <hbox>
      <caption class="box" label="Arqueos de caja"/>
      <menulist id="filtroAnio" oncommand="actualizarArqueoGral()">
        <menupopup id="elementosanio">
        </menupopup>
      </menulist>
      <menulist id="filtroMes" label="Mes" oncommand="actualizarArqueoGral()">
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
      <menulist label="<?php echo _("Elige Moneda...") ?>" id="SeleccionMoneda" 
                oncommand="actualizarArqueoGral()" >
	<menupopup id="itemsMoneda">
          <?php echo genXulComboMoneda(false,"menuitem")?>
  	</menupopup>
      </menulist>
      <menulist label="<?php echo _("Elige arqueo...") ?>" id="SeleccionArqueo" >
	<menupopup id="itemsArqueo">
	  <menuitem label="<?php echo _("Elige arqueo...")?>"/>      
  	</menupopup>
      </menulist>
    </hbox>

    <button label="<?php echo _("Consultar caja") ?>" 
            oncommand="onConsultarCaja()" collapsed="true"/> 
    <spacer flex="1"/>
    <vbox style="background-color: orange;text-align:center;
                 font-size: 120%;font-weight: bold">
      <textbox id='estadoCajaTexto' class="cjagralcerrada plain big" value="--OFF--"/>
      <textbox id='estadoCajaFecha' class="cjagralcerrada plain big" value="--/--/---- --:--"/>
      <textbox id='estadoCajaFecha' style="background-color: orange;border: 0px;"  
               class="plain big" collapsed="true" value="IdTienda: 
      <?php  echo CleanID(getSesionDato("IdTiendaDependiente")); ?>"/>
    </vbox>
  </hbox>
  <textbox id="log" flex="1" multiline="true" wrap="off" collapsed="true"/>  
  <caption  class="box" label="<?php echo _("Movimientos de caja:") ?>">
  </caption>
  <listbox collapsed="false" id="listaMovimientos" flex="1" contextmenu="accionesMovimientosCajaGral" onclick="revisarOperacionCajaGral()">
    <listcols>
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
      <splitter class="tree-splitter" />
      <listcol flex="1" />
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
      <listheader label="<?php echo _("Proveedor") ?>" />
      <listheader label="<?php echo _("Concepto") ?>" />
      <listheader label="<?php echo _("Importe") ?>" />
      <listheader label="<?php echo _("Usuario") ?>" />
    </listhead>
  </listbox>
  <spacer style="height: 8px"/>		
  <vbox id="boxArqueoActualGral">
  <caption class="box" label="<?php echo _("Arqueo actual") ?>"/>
  <hbox pack="center">
    <hbox>
      <grid class="consolidado" style="background-color: white;border: 0px #ccc solid;">
	<rows>
	  <row>
	    <description  style="text-decoration: underline">
                          <?php echo _("SALD0 INICIAL:") ?>
            </description>
	    <textbox  class="plain arqueoaling"  id='saldoInicialText' 
                      value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description  style="text-decoration: underline">
                          <?php echo _("+INGRESOS:") ?>
            </description>
	    <textbox class="plain arqueoaling"   id='ingresosText' 
                     value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description  style="text-decoration: underline">
                          <?php echo _("-EGRESOS:") ?>
            </description>
	    <textbox class="plain arqueoaling"   id='egresosText' 
                     value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description style="text-decoration: underline">
                         <?php echo _("-GASTOS:") ?>
            </description>
	    <textbox class="plain arqueoaling" id='gastosText' 
                     value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description style="text-decoration: underline">
                         <?php echo _("+APORTACIONES:") ?>
            </description>
	    <textbox  class="plain arqueoaling"  id='aportacionesText' 
                      value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description style="text-decoration: underline">
                         <?php echo _("-SUSTRACCIONES:") ?>
            </description>
	    <textbox  class="plain arqueoaling"  id='sustraccionesText' 
                      value="<?php echo $Moneda[1]['S']?> 0.00"/>
	  </row>
	  <row>
	    <description style="text-decoration: underline">
                         <?php echo _("=TEORICO CIERRE") ?>
            </description>
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
	  <description class="plain" style="text-decoration: underline">
                       <?php echo _("CIERRE CAJA") ?>
          </description>
	</row>

	<row>
	  <textbox class="plain"  id='cierreCajaText' 
                   style="font-size: 120%;font-weight: bold;text-align:center;
                          text-decoration: underline" value="0 Soles"/>
	</row>

	<row style="padding-top:1em">
	<hbox pack="left">
	  <description class="plain" style="text-decoration: underline" >
                       <?php echo _("DESCUADRE CAJA:")?>
          </description>
          <description id="titledescuadre"></description>
        </hbox>
        </row>
	<row>
	  <textbox class="plain" id="descuadreCajaText" value="0 Soles"/>
	</row>
      </rows>
    </grid>
    <spacer style="width: 16px"/>
    <vbox>
      <button id="botonAbrir" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_abrir.png" 
              label=" <?php echo _("Abrir caja") ?> "  disabled="false"   flex="1" 
              oncommand="Comando_AbrirCaja()" class="btn"/>
      <button id="botonCerrar" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_cerrar.png" 
              label=" <?php echo _("Cerrar caja") ?> " disabled="true"  flex="1" 
              oncommand="Comando_CerrarCaja()" class="btn"/>
      <button id="botonArqueo" image="<?php echo $_BasePath ?>/img/gpos_tpvcaja_arqueo.png" 
              label=" <?php echo _("Arqueo caja") ?> " disabled="true" flex="1" 
              oncommand="Comando_ArqueoCaja()" class="btn"/>
    </vbox>
  </hbox>
  </vbox>
  <spacer style="height: 8px"/>
  <vbox id="boxOperacionesGral" collapsed="true">
    <caption class="box" label="<?php echo _("Operaciones") ?>"/>
    <tabbox flex="1" id="tabbox_operaciones" >
      <tabs>
	<tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_aport.png"
             id="tab_aportacion" label="<?php echo _(" Aportación ") ?>"/>
	<tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_sust.png"
             id="tab_sustraccion" label="<?php echo _(" Sustracción ") ?>" 
             oncommand="verCuentaBancaria()"/>
        <tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_input.png"
             id="tab_ingreso" label="<?php echo _(" Ingreso ") ?>" 
             oncommand="verCuentaBancaria()"/>
	<tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png"
             id="tab_gasto" label="<?php echo _(" Gasto ") ?>"/>
	<tab image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png"
             id="tab_cuenta_bancaria" label="<?php echo _(" Cuenta Bancaria ") ?>" 
             oncommand="verCuentaBancaria()"/>
      </tabs>
      <tabpanels flex="1" id="tab_boxoperacion" class="box">
      <groupbox>
	<hbox align="center">
          <caption label="<?php echo _("Partida:") ?>"/>
	  <toolbarbutton id="btnSeleccionPartidaAport" label=" + "
                         oncommand="CogePartidaCaja('Aportacion')"/>
	  <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaAport"
                    style="width:30em;" 
                    onkeypress="return soloAlfaNumerico(event);">
	    <menupopup id="elementosPartidaAportacion">
	      <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Aportacion","CG")?>
  	    </menupopup>
          </menulist>
        </hbox>
	<hbox align="center">
          <caption label="Concepto:"/>
	  <textbox id='conceptoText' value="" flex="1" onpaste="return false"
                   onkeypress="return soloAlfaNumerico(event);"/>
	  <spacer style="width: 16px"/>
          <caption label="Importe:"/>
	  <textbox id='importeText' value="" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <spacer style="width: 16px"/>
          <caption id="cambioMonedaAp" label="Tipo Cambio:" collapsed="true"/>
	  <textbox id='importeCambioMonedaAp' value="" collapsed="true" style="width:10em;" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <button image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_aport.png" 
                  label="<?php echo _(" Registrar Aporte ") ?>" id="btnAporte"
                  oncommand="comando_HacerUnaOperacion('Aportacion')" class="btn"/>
	</hbox>
      </groupbox>
      <groupbox>
        <hbox align="center">
          <caption label="Partida:"/>
	  <toolbarbutton id="btnSeleccionPartidaSust" label=" + "
                         oncommand="CogePartidaCaja('Sustraccion')"/>
	  <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaSust"
                    style="width:30em;" oncommand="validarDatosExtra(this.label,this.value)"
                    onkeypress="return soloAlfaNumerico(event);">
	    <menupopup id="elementosPartidaSustraccion">
	      <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Sustraccion","CG")?>
  	    </menupopup>
          </menulist>
          <hbox id="boxCuentaBancariaSust" collapsed="true" align="center">
            <caption label='   a    Cuenta bancaria:'/>
	    <menulist id="SeleccionCuentaBancariaSust" 
                      oncommand="actualizarDatosCuentaBancaria(this.value)">
	      <menupopup id="itemsCuentaBancariaSust">

  	      </menupopup>
            </menulist>
            <caption label="   Saldo:"/>
            <textbox id="saldoCuentaBancariaSust" readonly="true"/>
          </hbox>
        </hbox>
	<hbox align="center">
          <caption label="Concepto:"/>
	  <textbox  id='conceptoTextSubs' value="" flex="1" onpaste="return false"
                    onkeypress="return soloAlfaNumerico(event);"/>
	  <spacer style="width: 16px"/>	
          <caption label="Importe:"/>
	  <textbox id='importeTextSubs' value="" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <spacer style="width: 16px"/>
          <caption id="cambioMonedaSust" label="Tipo Cambio:" collapsed="true"/>
	  <textbox id='importeCambioMonedaSust' value="" collapsed="true" style="width:10em;"
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <button image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_sust.png" 
                  label="<?php echo _(" Registrar Sustracción ") ?>" id="btnSustracion"
                  oncommand="comando_HacerUnaOperacion('Sustraccion')" class="btn"/>
	</hbox>
      </groupbox>
      <groupbox>
	<hbox align="center">
          <caption label="Partida:"/>
	  <toolbarbutton id="btnSeleccionPartidaIngreso" label=" + "
                         oncommand="CogePartidaCaja('Ingreso')"/>
	  <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaIngreso"
                    style="width:30em;" oncommand="validarDatosExtra(this.label,this.value)"
                    onkeypress="return soloAlfaNumerico(event);">
	    <menupopup id="elementosPartidaIngreso">
	      <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Ingreso","CG") ?>
  	    </menupopup>
          </menulist>
          <hbox id="boxCuentaBancariaIng" collapsed="true" align="center">
            <caption label='   de    Cuenta bancaria:'/>
	    <menulist id="SeleccionCuentaBancariaIng" 
                      oncommand="actualizarDatosCuentaBancaria(this.value)">
	      <menupopup id="itemsCuentaBancariaIng">
  	      </menupopup>
            </menulist>
            <caption label="   Saldo:"/>
            <textbox id="saldoCuentaBancariaIng" readonly="true"/>
          </hbox>
        </hbox>
        <hbox align="center">
          <caption label="Concepto:"/>
	  <textbox id='conceptoTextIngreso' value="" flex="1" onpaste="return false"
                   onkeypress="return soloAlfaNumerico(event);"/>
	  <spacer style="width: 16px"/>		
          <caption label="Importe:"/>
	  <textbox id='importeTextIngreso' value="" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <spacer style="width: 16px"/>
          <caption id="cambioMonedaIng" label="Tipo Cambio:" collapsed="true"/>
	  <textbox id='importeCambioMonedaIng' value="" collapsed="true" style="width:10em;" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <button image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_input.png" 
                  label="<?php echo _(" Registrar Ingreso ") ?>" id="btnIngreso"
                  oncommand="comando_HacerUnaOperacion('Ingreso')" class="btn"/>
	</hbox>
      </groupbox>
      <groupbox>
	<hbox align="center">
          <caption label="Partida:"/>
	  <toolbarbutton id="btnSeleccionPartidaGasto" label=" + "
                         oncommand="CogePartidaCaja('Gasto')"/>
	  <menulist label="<?php echo _("Elige...") ?>" id="SeleccionPartidaGasto"
                    style="width:30em;" 
                    onkeypress="return soloAlfaNumerico(event);">
	    <menupopup id="elementosPartidaGasto">
	      <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,"Gasto","CG") ?>
  	    </menupopup>
          </menulist>
	  <spacer style="width: 16px"/>
          <caption label="Comprobante:"/>
	  <menulist id="SeleccionDocumentoGasto">
	    <menupopup id="itemsDocumento">
	      <menuitem value="Boleta" label="<?php echo _("Boleta")?>" />      
	      <menuitem value="Factura" label="<?php echo _("Factura")?>" /> 
	      <menuitem value="Voucher" label="<?php echo _("Voucher")?>" />
	      <menuitem value="Ticket" label="<?php echo _("Ticket")?>" selected="true"/>
  	    </menupopup>
          </menulist>
	  <spacer style="width: 16px"/>
          <caption label="Código:"/>
          <textbox id="CodigoTextGasto" value="" 
                   onkeypress="return soloNumericoCodigoSerie(event)"/>
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
	  <textbox id='conceptoTextGasto' value="" flex="1" onpaste="return false"
                   onkeypress="return soloAlfaNumerico(event);"/>
	  <spacer style="width: 16px"/>		
          <caption label="Importe:"/>
	  <textbox id='importeTextGasto' value="" 
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <spacer style="width: 16px"/>
          <caption id="cambioMonedaGast" label="Tipo Cambio:" collapsed="true"/>
	  <textbox id='importeCambioMonedaGast' value="" collapsed="true" style="width:10em;"
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <button image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png" 
                  label="<?php echo _(" Registrar Gasto ") ?>" id="btnGasto"
                  oncommand="comando_HacerUnaOperacion('Gasto')" class="btn"/>
	</hbox>
      </groupbox>
      <groupbox>
	<hbox align="center">
          <caption label='Cuenta bancaria:'/>
          <toolbarbutton oncommand="CogeNroCuenta()" label="+"></toolbarbutton>
	  <menulist id="SeleccionCuentaBancaria" 
                    oncommand='actualizarDatosCuentaBancaria(this.value)'>
	    <menupopup id="itemsCuentaBancaria">
  	    </menupopup>
          </menulist>
          <spacer style="width: 16px"/>
          <caption label='Total Ingreso: '/>
	  <textbox id='importeTotalCuentaIngreso' value="" readonly='true'/>
          <spacer style="width: 16px"/>
          <caption label='Total Salida: '/>
	  <textbox id='importeTotalCuentaSalida' value="" readonly='true'/>
          <spacer style="width: 16px"/>
          <caption label='Total Saldo: '/>
	  <textbox id='importeTotalCuentaSaldo' value="" readonly='true'/>
	</hbox>
        <hbox align="center">
          <caption label='Transferir a :'/>
	  <menulist id="SeleccionCuentaBancariaDest" >
	    <menupopup id="itemsCuentaBancariaDest">
  	    </menupopup>
          </menulist>
	  <spacer style="width: 16px"/>
          <caption label="Concepto:"/>
	  <textbox id='conceptoTextTransferencia' value="" flex="1" onpaste="return false"
                   onkeypress="return soloAlfaNumerico(event);" 
                   placeholder='Ingrese detalle del documento de tranferencia'/>
	  <spacer style="width: 16px"/>
          <caption label="Importe:" />
	  <textbox id='importeTransferencia' value="" style="width:10em;"
                   onkeypress="return soloNumeros(event,this.value)"/>
	  <button image="<?php echo $_BasePath ?>img/gpos_tpv_dinero_output.png" 
                  label="<?php echo _(" Registrar Trasferencia ") ?>" id="btnTrasnferencia"
                  oncommand="registrarTrasferenciaBancaria()" class="btn"/>
        </hbox>
      </groupbox>
    </tabpanels>          
  </tabbox>
  </vbox>
  <spacer style="height:6px"/>
  <button id="btnOperacionesGral" label="Operaciones" collapsed="false" 
          image="<?php echo $_BasePath; ?>img/gpos_tpvcaja_guardarpartida.png" 
          oncommand="mostrarPanelOperacionesGral('arqueo')" 
          style="height:2.5em;font-size:18px" class="btn">
  </button>
</vbox>

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>

<script>//<![CDATA[

var Local = new Object();
Local.IdLocalActivo = '<?php echo CleanID($IdLocal) ?>';
Local.CuentaBancaria = '<?php echo CleanID($CtaBancaria) ?>';
Local.CuentaBancaria2 = '<?php echo CleanID($CtaBancaria2) ?>';

setTimeout("onLoadFormulario()",300);


//]]></script>




</window>
