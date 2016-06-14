<?php

include("../../tool.php");

$IdLocal       = CleanID(getSesionDato("IdTiendaDependiente"));
$TipoVenta     = CleanText(getSesionDato("TipoVentaTPV"));
$Moneda        = getSesionDato("Moneda"); 
$esCajaCentral = getSesionDato("esCajaCentral"); 

StartXul('Guia Remision',$predata="",$css='');
StartJs($js='modulos/guiaremision/guiaremision.js?v=3.0');
?>

<?php getMonedaJS($Moneda); ?>

<script>//<![CDATA[

 //]]></script>


<hbox>	
    <html:div id='box-popup' class='box-popup-off'>
        <html:span class='closepopup' onclick='closepopup()'></html:span>
        <html:iframe id='windowpopup' name='windowpopup' src='about:blank'
                     width='100%' style='border: 0' height='100%'
                     onload='if(this.src != "about:blank" ) loadFocusPopup()'>
        </html:iframe> 
    </html:div>
</hbox>	

<groupbox flex="1">
  <vbox flex="1" align="center" pack="center" >
    <caption class="xtitle"  label="Nueva Guía de Remisión" id="txtTitleGuia" />
    <groupbox >
      <hbox>
      <grid>
        <columns><column/><column/></columns>
	<rows>
	  <row>
	    <caption label="Serie - Número" />
            <hbox>
	        <textbox id="SerieGuia"  size="5" value="1" readonly="true"
		         onkeypress="return soloNumeros(event,this.value)"/>
	        <textbox id="NumeroGuia"  size="15" value="1" readonly="true"
		         onkeypress="return soloNumeros(event,this.value)"/>
            </hbox>
	  </row>
	  <row>
	    <caption label="Fecha Emisión" />
	    <hbox>
	      <datepicker id="FechaEmision" type="popup"/>
	      <timepicker id="HoraEmision" type="popup" value="00:00:00" />
	    </hbox>
	  </row>
            
	  <row>
	    <caption label="Motivo traslado" />
	    <vbox>
              <menulist id="MotivoTraslado" style="width:150px"
                        oncommand="">
	        <menupopup>
                 <menuitem  label="Venta" value="1" style="font-weight: bold"
                            selected="true"/>
                  <menuitem label="Compra" value="3" style="font-weight: bold"/>
                  <menuitem label="Devolución" value="4" style="font-weight: bold"/>
                  <menuitem label="Consignación" value="2" style="font-weight: bold"/>
                  <menuitem label="Importación" value="11" style="font-weight: bold"/>
                  <menuitem label="Exportación" value="12" style="font-weight: bold"/>
	        </menupopup>
	      </menulist>
	    </vbox>
	  </row>
	  <row>
	    <caption label="Concepto Traslado" />
	    <textbox id="ConceptoTraslado"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	  <row>
	    <caption label="Punto Partida" />
	    <textbox id="PuntoPartida"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	  <row>
	    <caption label="Punto Llegada" />
	    <textbox id="PuntoLlegada"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	</rows>
      </grid>
      <spacer style="width:2.5em"/>
      <grid>
	<columns>
	  <column flex="1"></column>
	</columns>
	<rows id="DetallePagoDocumento">
	  <row>
	    <caption label="Marca Unidad Transp" />
	    <textbox id="MarcaUnidadTransporte"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	  <row>
	    <caption label="Placa Unidad Transp" />
	    <textbox id="PlacaUnidadTransporte"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	  <row>
	    <caption label="Licencia Conductor" />
	    <textbox id="LicenciaConductor"  size="20" value=''
		     onkeypress="return soloAlfaNumerico(event)"/>
	  </row>
	  <row>
	    <caption label="Peso de Carga" />
            <hbox>
	      <textbox id="PesoCarga"  size="10" value=''
		       onkeypress="return soloNumeros(event,this.value)"/>
	      <menulist id="UnidadPesoCarga"
		        flex="2" style="min-width: 7em"
		        oncommand="" label="Elija...">
	        <menupopup id="elementosPesoUnidadCarga">
                  <menuitem  label="Klg." value="KLG" style="font-weight: bold"/>
                  <menuitem  label="TN." value="TN" style="font-weight: bold"
                             selected="true"/>
	        </menupopup>
	      </menulist>
            </hbox>
	  </row>
	  <row>
	      <caption label="Fecha Inicio Traslado" />
	      <hbox>
	          <datepicker id="FechaTraslado" type="popup"/>
	          <timepicker id="HoraTraslado" type="popup" value="00:00:00" />
	      </hbox>
	  </row>
	  
	  <row>
              <hbox>              
              <caption label="Fletador:"/>

              <toolbarbutton id="btnSubsidiario" label="+"
                             oncommand="auxAltaSubsidiario()"/>
              <toolbarbutton id="btnSubsidiarioHab" label="..."
                             oncommand="auxSubsidiarioHab()"/>
              </hbox>              
              <textbox id="EmpresaTextGasto" value="" flex="1" readonly="true"
                       onkeypress="return soloAlfaNumerico(event);"/>
              <textbox id="IdSubsidiario" value="0" flex="1" collapsed="true"/>

	  </row>
          <row collapsed="true">
              <textbox id="txtTipoGuia" collapsed="true"/>
              <textbox id="txtIdComprobanteNum" collapsed="true"/>
              <textbox id="txtIdComprobanteProv" collapsed="true"/>
          </row>
	</rows>
      </grid>
      </hbox>
      <spacer style="height:0.5em"/>
      <hbox>
        <button flex="1" id="BotonGuiaRemision" image="img/gpos_vaciarcompras.png"
                class="media btn" label="  Siguiente  "
                oncommand="VerGuiaRemision()" collapsed="true"/>
	<button flex="1" id="BotonAceptarImpresion" image="img/gpos_imprimir.png"
                class="media btn" label="  Aceptar  "
                oncommand="RegistrarGuia()"/>
	<button flex="1" id="BotonCancelarImpresion" collapsed="true"
                image="img/gpos_vaciarcompras.png"
                class="media btn" label="  Cancelar  "
                oncommand="parent.CerrarPeticion()"/>
      </hbox>
      
    </groupbox>
  </vbox>
</groupbox>

<script>//<![CDATA[

 var Local = new Object();
 Local.IdLocalActivo = '<?php echo CleanID(getSesionDato("IdTiendaDependiente")) ?>';
 Local.CajaCentral   = '<?php echo $esCajaCentral ?>';

 //]]></script>

</window>
