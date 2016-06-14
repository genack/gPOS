<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Documentos de Pagos',$predata="",$css='');
StartJs($js='modulos/pagoscobros/pagosproveedordoc.js?v=3.1.2');
?>

  <script>//<![CDATA[
  var IGV = <?php echo getSesionDato("IGV"); ?>;
  var cIdOrdenCompra = <?php echo $idordencompra;?>;
  var cModo          = '<?php echo $cmodo;?>';
  var ProvExterno    = "<?php echo $proveedor;?>";
  var IdProvExterno  = "<?php echo $idproveedor;?>";
  var ImporteOC      = "<?php echo $importeoc?>";
  var CambioMonedaOC = "<?php echo $cambiomoneda?>";
  var IdMonedaOC     = "<?php echo $idmoneda?>";

  //]]></script>
<?php getMonedaJS($Moneda); ?>

<!--  no-visuales -->
<?php include("pagosproveedor.php"); ?>
<!--  no-visuales -->
      <hbox>	
	<html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
	<html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
	</html:div>
      </hbox>	

<!--Encabesdado-->

<!--Búsqueda-->
<vbox id="buscaPagoDocumento" collapsed="<?php echo $blockprov;?>">
  <hbox pack="center" collapsed="<?php echo $blockprov;?>" class="box">
    <caption class="h1">
      <?php echo _("Pagos Proveedor") ?>
    </caption>
  </hbox>
  <hbox align="start" pack="center" class="box">
    <vbox>
      <?php if(getSesionDato("esAlmacenCentral")){?>
      <description>Local:</description>
      <hbox>
	<menulist id="FiltroLocal" label="FiltroLocal" oncommand="BuscarPagoDocumento()">
	  <menupopup id="combolocales">
	    <menuitem value="0" label="Todos" />
	    <menuitem value="<?php  echo $IdLocal ?>" label="Actual" selected="true"/>
	  </menupopup>
	</menulist>
      </hbox>
      <?php } else { ?>
      <textbox id="FiltroLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
      <?php } ?>	  
    </vbox>
    <vbox>
      <description value="Desde:"/>
      <datepicker id="FechaBuscaDesde" type="popup"/>
    </vbox>
    <vbox>
      <description value="Hasta:"/>
      <datepicker id="FechaBuscaHasta" type="popup"/>
    </vbox>
    <vbox>
      <description>Modalidad:</description>
      <menulist id="FiltroModalidad" label="FiltrosModalidad">
	<menupopup id='combomodalidades'>
	  <menuitem value="0" label="Todos" selected="true" oncommand="BuscarPagoDocumento()"/>
	  <?php echo genXulComboModalidadPago(false,"menuitem") ?>
	</menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxEstado" collapsed="true">
      <description>Estado:</description>
      <menulist id="FiltroEstado" label="FiltrosEstado">
	<menupopup>
	  <menuitem value="Todos"      label="Todos" selected="true"   
                    oncommand="BuscarPagoDocumento()"/>
	  <menuitem value="Borrador"   label="Borrador"   oncommand="BuscarPagoDocumento()" />
	  <menuitem value="Pendiente"  label="Abonado"  oncommand="BuscarPagoDocumento()" />
	  <menuitem value="Confirmado" label="Confirmado" oncommand="BuscarPagoDocumento()" />
	  <menuitem value="Cancelado"  label="Cancelado"  oncommand="BuscarPagoDocumento()" />
        </menupopup>
      </menulist>
    </vbox>
    <vbox id="vboxMoneda" collapsed="true">
      <description>Moneda:</description>
      <menulist id="FiltroMoneda" label="FiltroMoneda" oncommand="BuscarPagoDocumento()">
	<menupopup>
	  <menuitem value="Todos" label="Todos" selected="true" />
          <?php echo genXulComboMoneda(false,"menuitem") ?>
	  <menuitem value="todoSol" id="todoSol" label="Local" />
	</menupopup>
      </menulist>
    </vbox>
    <vbox>
      <description>Proveedor:</description>
      <textbox onfocus="select()" id="NombreProveedor" 
               onkeyup="if (event.which == 13) BuscarPagoDocumento()" 
               onkeypress="return soloAlfaNumerico(event);"/>
    </vbox>

    <vbox style="margin-top:1em">
        <menu>
          <toolbarbutton style="min-height: 2.7em;" 
	                 image="<?php echo $_BasePath; ?>img/gpos_busqueda_avanzada.png"/>
          <menupopup >
	    <menuitem type="checkbox" label="Estado" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	 
	    <menuitem type="checkbox" label="Moneda" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>	      
	    <menuseparator />
	    <menuitem type="checkbox" label="Referencia" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Fecha Registro" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Impuesto" checked="false"
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
	    <menuitem type="checkbox" label="Usuario"  
                      oncommand = "mostrarBusquedaAvanzada(this);"/>
          </menupopup>
        </menu>
      </vbox>

    <vbox style="margin-top:1.1em">
      <button id="btnbuscar" label=" Buscar " class="btn"
              image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
              oncommand="BuscarPagoDocumento()"/>
    </vbox>
  </hbox>
</vbox>

<!-- Resumen -->
<vbox flex="1" id="listboxPagoDocumento" collapsed="false" class="box">
<vbox id="resumenPagoDocumento" collapsed="false">
  <caption class="box" label="<?php echo _("Documentos") ?>" />
</vbox>

<!-- Lista Listbox-->
<listbox flex="1" id="listaPagoDocumento" contextmenu="AccionesBusquedaDocumento" collapsed="false" onclick="CesionDocumento()" onkeypress="if (event.keyCode==13) PagoDocumento('Modificar')" ondblclick="PagoDocumento('Modificar')" >
    <listcols flex="1">
      <listcol/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolReferencia" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolFecha_Registro" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolImpuesto" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol flex="1" id="vlistcolUsuario" collapsed="true"/>
      <splitter class="tree-splitter" />
      <listcol flex="0"/>
    </listcols>
    <listhead>
      <listheader label=" # " style="font-style:italic;"/>
      <listheader label="Local"/>
      <listheader label="Código"/>
      <listheader label="Referencia" id="vlistReferencia" collapsed="true"/>
      <listheader label="Proveedor"/>
      <listheader label="Fecha Registro" id="vlistFecha_Registro" collapsed="true"/>
      <listheader label="Fecha Operación"/>
      <listheader label="Modalidad Pago"/>
      <listheader label="Estado Pago"/>
      <listheader label="Impuesto" id="vlistImpuesto" collapsed="true"/>
      <listheader label="Importe"/>
      <listheader label="Saldo"/>
      <listheader label="Usuario" id="vlistUsuario" collapsed="true"/>
      <listheader label=""/>
    </listhead>

</listbox>
<vbox id="resumenPagoDocumento" collapsed="false" class="box">
    <vbox flex="1">
      <caption class="box" label="<?php echo _("Resumen Pago Documentos") ?>" />
      <hbox class="resumen" pack="center" align="left">
	<label value="Documentos:"/>
	<description id="TotalPagoDocumento" value="" />
	<label value="  Borradores:"/>
	<description id="TotalDocumentoBorrador" value="" />
	<label value="Abonados:"/>
	<description id="TotalDocumentoPendiente" value="" />
	<label value="Confirmados:"/>
	<description id="TotalDocumentoConfirmado" value="" />
	<label value="Cancelados:"/>
	<description id="TotalDocumentoCancelado" value="" />
	<!--label value="Importe:"/>
	<description id="TotalImporte" value="" /-->
      </hbox>
    </vbox>
</vbox>

<vbox>
  <hbox flex="1">

  <button  id="btnVolverComprobantesCompras" flex="1"
           style="font-weight: bold;font-size:13px;"
           class="btn" image="<?php echo $_BasePath; ?>img/gpos_volver.png"  
           label=" Volver Comprobantes" 
           oncommand="parent.mostrarOperacionesPagosProveedor('comprobantes')" 
           collapsed="<?php echo $btnVolver ?>"/>

  <button  id="btnNuevo" style="font-weight: bold;font-size:13px;" flex="1"
           class="btn" image="<?php echo $_BasePath; ?>img/gpos_tpvmultipagos.png"  
           label=" Nuevo Pago" 
           oncommand="PagoDocumento('Nuevo')" collapsed="false"/>
  </hbox>
</vbox>
</vbox>

<!-- Formulario Documento de pagos  -->
<vbox id="formularioDocumento" align="center" pack="top" collapsed="true" 
      class="box" flex="1">
  <spacer style="height:1.8em"/>
  <caption id="mensaje" label="" class="h1"/>

<groupbox>
<hbox>
  <spacer flex="1"></spacer>
  <groupbox>
    <hbox>
      <grid>
	<columns>
	  <column flex="1"></column>
	</columns>
	<rows>
	  <row id="filaTipoProveedor" collapsed="<?php echo $blockprov;?>">
	    <caption label="Tipo Proveedor" />
	    <vbox>
              <menulist id="TipoProveedor" style="width:150px"
                        oncommand="SeleccionTipoProveedor()">
	        <menupopup>
                  <menuitem  label="Externo" value="Externo" style="font-weight: bold"/>
                  <menuitem  label="Interno" value="Interno" style="font-weight: bold"/>
	        </menupopup>
	      </menulist>
	    </vbox>
	  </row>
	  <row>
	    <caption label="Proveedor" />
	    <vbox>
  	      <box>
                <toolbarbutton id="lProvHab" style="width: 32px !important" 
		               oncommand="CogeProvHab()" label="+" 
                               collapsed="<?php echo $blockprov;?>"/>
                <textbox class="media" id="ProvHab" readonly="true" 
	                 value="<?php echo $proveedor;?>" flex="1"/>
                <textbox  id="IdProvHab" value="<?php echo $idproveedor;?>" collapsed="true"/>
	      </box>
	    </vbox>
	  </row>
	  <row>
	    <caption label="Modalidad Pago" />
            <menulist  id="ModalidadPago"  oncommand="VerDatosExtra(this.value)">
	       <menupopup>
	         <?php echo genXulComboModalidadPago(false,"menuitem") ?>
	       </menupopup>
	    </menulist>
	  </row>
	  <row>
	    <caption label="Fecha Operación" />
	    <hbox>
	      <datepicker id="FechaOperacion" type="popup"/>
	      <timepicker id="HoraOperacion" type="popup" value="00:00:00" />
	    </hbox>
	  </row>
	  <row id="moneda">
	    <caption label="Moneda" />
	    <menulist id="TipoMoneda"  oncommand="OcultarMoneda(this.value)">
	      <menupopup>
	        <?php echo genXulComboMoneda($idmoneda,"menuitem") ?>
	     </menupopup>
	    </menulist>
	  </row>
	  <row id="trasladomoneda" collapsed="false">
	    <caption label="" />
            <checkbox id="checkCambioDivisa" label="Registrar cambio divisa"/>
	  </row>           
	  <row id="cambmoneda">
	    <caption label="Cambio Moneda" />
	    <textbox id="CambioMoneda"  size="20" value="1"
	                 onblur="validarDatosPago('cm')"
	                 onchange="validarDatosPago('cm')"
			 onkeypress="return soloNumeros(event,this.value)"/>
	  </row>
	  <row>
	    <caption label="Importe" />
	    <textbox id="Importe"  size="20" value='0'
	                 onblur="validarDatosPago('imp')"
	                 onchange="validarDatosPago('imp')"
			 onkeypress="return soloNumeros(event,this.value)"/>
	  </row>

         <spacer style="height:8px"/>
	 <row id="filaLocalCambio" collapsed="true">
           <?php if(getSesionDato("esAlmacenCentral")){?>
           <caption label="Local "/>
           <menulist id="FiltroLocalCambio" label="FiltroLocal">
             <menupopup id="combolocalselect">
               <menuitem value="<?php  echo $IdLocal ?>" label="ACTUAL" selected="true"/>
	     </menupopup>
	   </menulist>
           <?php } else { ?>
           <textbox id="FiltroLocal" value="<?php echo $IdLocal; ?>" collapsed="true"/>
           <?php } ?>	  
	 </row>
         <row id="EstadoPagoDocumento" collapsed="false">
           <caption label="Estado Pago"/>
           <menulist  id="EstadoDocumento">
             <menupopup>
               <menuitem value="Borrador" label="Borrador"  />
               <menuitem id="menuEstadoPte" value="Pendiente" label="Abonado" 
                         selected="true" />
               <menuitem id="menuEstadoCancelado" value="Cancelado" label="Cancelado"  
                         collapsed="true"/>
            </menupopup>
          </menulist>
         </row>
	</rows>
      </grid>
    </hbox>
  </groupbox>
  <groupbox>
    <hbox>
      <grid>
	<columns>
	  <column flex="1"></column>
	</columns>
	 <rows id="DetallePagoDocumento">
	  <caption id="rowCtaEmpresa" label="Cta Empresa" />
	  <row id="entFinancieraEmp">
	    <caption label="Entidad Financiera" />
	    <hbox>
	      <toolbarbutton oncommand="CogeNroCuentaEmp()" label="+"></toolbarbutton>
	      <menulist id="EntidadFinancieraEmp"
		        flex="2" style="min-width: 7em"
		        oncommand="RegenCuentas()" label="Elija...">
	        <menupopup id="elementosEntFinancieraEmp">
	        </menupopup>
	      </menulist>
	    </hbox>
	    <textbox id="EntidadFinanciera"  size="30" collapsed="true"
	             onkeypress="return soloAlfaNumerico(event);"/>
	  </row>
	  <row id="cuentaEmpresa">
	    <caption label="Nro. Cuenta" />
	    <menulist id="NroCtaEmpresa" flex="2" style="min-width: 7em" label="Elija..." >
	      <menupopup id="elementosCuentaEmp">
	      </menupopup>
	    </menulist>
	    <textbox id="CtaEmpresa"  size="30" onfocus="this.select()" collapsed="true"
		     onkeypress="return soloNumeros(event,this.value)"/>
	  </row>
	  <caption id="rowCtaProveedor" label="Cta Proveedor" />
	  <row id="entFinancieraProv">
	    <caption label="Entidad Financiera" />
	    <hbox>
	      <toolbarbutton oncommand="CogeNroCuentaProv()" label="+"></toolbarbutton>
	      <menulist id="EntidadFinancieraProv"
		        flex="2" style="min-width: 7em"
		        oncommand="RegenCuentasProv()" label="Elija...">
	        <menupopup id="elementosEntFinancieraProv">
	        </menupopup>
	      </menulist>
	    </hbox>
	    <textbox id="EntidadFinanciera"  size="30" collapsed="true"
	             onkeypress="return soloAlfaNumerico(event);"/>
	  </row>
	  <row id="cuentaProveedor">
	    <caption label="Nro. Cuenta" />
	    <menulist id="NroCtaProveedor" flex="2" style="min-width: 7em" label="Elija...">
	      <menupopup id="elementosCuentaProv">
	      </menupopup>
	    </menulist>
	    <textbox id="CtaProveedor" size="30" onfocus="this.select()" collapsed="true"
		     onkeypress="return soloNumeros(event,this.value)"/>	
	  </row>
	  <row id="codOperacion">
	    <caption label="Código Operación" />
	    <textbox id="CodigoOperacion"  size="30" onfocus="this.select()"
		     onkeypress="return soloNumeros(event,this.value)"/>
	  </row>
	  <row id="numDocumento">
	    <caption label="Nro Documento" />
	    <textbox id="NroDocumento"  size="30" onfocus="this.select()"
		     onkeypress="return soloNumeros(event,this.value)"/>
	  </row>
	  <row>
	    <caption label="Observaciones" />
	    <textbox id="Observaciones" multiline="true" rows="1" cols="10" 
                     style="width:20em;"  
	      	     onpaste="return false"
                     onkeypress="return soloAlfaNumerico(event);"/>
	  </row>
	</rows>
      </grid>
    </hbox>
  </groupbox>
  <spacer flex="1"></spacer>

</hbox>

<!--Boton Formulario pago documento-->
<spacer style="height:0.5em"/>
<vbox flex="1" id="botonFormularioDocumento" align="center" pack="top" collapsed="true">
   <hbox flex="1">
     <button image="<?php echo $_BasePath; ?>img/gpos_cancelar.png" 
             id="cancelarDocumento" collapsed="false" class="btn"
	     label=" Cancelar" oncommand="CancelarPagoDocumento()"/>
     <button image="<?php echo $_BasePath; ?>img/gpos_aceptar.png"
             id="btnAceptar" collapsed="false" class="btn"
	     label=" Aceptar" oncommand=""/>
   </hbox>
</vbox>
</groupbox>
</vbox>

 <script>//<![CDATA[
	      

   <?php


     if($locales)
       if(getSesionDato("esAlmacenCentral")){
	 echo "iniComboLocales('".$locales."');";
	 echo "iniComboLocalSel('".$locales."');";
       }

     $esAlmacenCentral = ($locales)?getSesionDato("esAlmacenCentral"):false;
     echo $initDocumento;
   ?>
   
   var esAlmacen = "<?php echo $esAlmacenCentral?>";

  //]]></script>


  <?php
    EndXul();
  ?>
