<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Pagos Proveedor',$predata="",$css=false);
StartJs($js='modulos/pagoscobros/pagos.js?v=3.1');
?>

<script> //<![CDATA[
  //  alert("<?php echo $cbtnAceptar ?>");
var aPagoProv           = new Array();

aPagoProv.IdComprobante = "<?php echo $idcomprobante?>";

aPagoProv.IdProveedor   = "<?php echo $idproveedor?>";
aPagoProv.IdMonedaCbte  = "<?php echo $idmonedacbte?>";
aPagoProv.ImporteCbte   = "<?php echo $ImporteComprobante?>";
aPagoProv.CambioCbte    = "<?php echo $cambiocomprobante?>";
aPagoProv.Pendiente     = "<?php echo $ImportePendiente?>";
aPagoProv.FormaPago     = "<?php echo $formapago?>";
aPagoProv.PendientePlan = "<?php echo $pendienteplan?>";
aPagoProv.EstadoCbte    = "<?php echo $estadocbte?>";
aPagoProv.IdMonedaDoc   = "<?php echo $idmoneda?>";
aPagoProv.ImportePago   = "<?php echo $importepago?>";
aPagoProv.IdPagoDoc     = "<?php echo $idpagodoc?>";
aPagoProv.EstadoPago    = "<?php echo $estado?>";
aPagoProv.FPago         = "<?php echo $fpago?>";
aPagoProv.Mora          = "<?php echo $mora?>";
aPagoProv.Obs           = "<?php echo $obs?>";
aPagoProv.DocPago       = "<?php echo $documento?>";
aPagoProv.IdPagoProv    = "<?php echo $idpagoprov?>";
aPagoProv.Codigo        = "<?php echo $codigo?>";
aPagoProv.IdLocalCbte   = "<?php echo $idlocal?>";
aPagoProv.Excedente     = "<?php echo $exceso?>";

esAgregar               = "<?php echo $esAgregar?>";
cModo                   = "<?php echo $modo?>";
cbtnAceptar             = "<?php echo $cbtnAceptar?>";


//]]></script>

<vbox flex="1" id="formularioPago" align="center" pack="top" class="box">

  <spacer style="height:1.9em"/>
  <caption class="h1" label="Asociar Pago" id="etiquetaPago"></caption>
  <spacer style="height:0.6em"/>
  <hbox >
    <caption align="center" id="ResumenComprobante" style="font-size: 11px;"
             label="<?php echo $rescomprobante ?>"></caption>
    <textbox id="idComprobante" value="<?php echo $idcomprobante ?>"
             collapsed="true"></textbox>
  </hbox>
  <spacer style="height:0.4em"/>
  <radiogroup id="rdiocomprobante" collapsed="false" value="">
    <hbox pack="center">
      <radio id="radioagregar" label="Asociar" style="font-size: 13px;"
             oncommand="AgregarPagoProv('asociar');" disabled="false" selected="true"></radio>
      <radio id="radioplanificar" label="Planificar" style="font-size: 13px;"
             oncommand="AgregarPagoProv('planificar');" disabled="false" selected="false"></radio>
    </hbox>
  </radiogroup>
  <spacer style="height: 0.3em"></spacer>
  <groupbox>
    <grid>
      <columns>
        <column></column>
        <column></column>
      </columns>
      <rows>
        <row>
          <caption label="TOTAL  (<?php echo $SimboloComprobante ?>)"></caption>
          <textbox id="ImporteComprobante" readonly="true"
                   value="<?php echo $ImporteComprobante; ?>"></textbox>
        </row>
        <row>
          <caption label="PENDIENTE"></caption>
          <textbox id="ImportePendiente" readonly="true" 
                   value="<?php echo $ImportePendiente; ?>" style="color: red;"></textbox>
        </row>
        <row id="filaImporteMora" collapsed="false">
          <caption label="DIFERENCIA"></caption>
          <hbox flex="1">
            <textbox id="ImporteMora" style="color: Green;" flex="1"
                     onkeypress="return soloNumeros(event,this.value);"/>
            <menulist id="TipoExtra" collapsed="true" >
	      <menupopup>
                 <menuitem  label="EXCEDENTE" value="Excedente" style="font-weight: bold"/>
                 <menuitem  label="MORA" value="Mora" style="font-weight: bold"/>
	      </menupopup>
	    </menulist>
          </hbox>
        </row>
        <!--spacer style="height: 0.5em"></spacer-->
        <row id="Pago_Modo" collapsed="false">
          <caption label="PAGO"></caption>
          <menulist id="xFormaPago"  oncommand="MostrarPagoDocumento(this.value)" sizetopopup="pref">
            <menupopup >
              <menuitem  label="ELIGE PAGO" value="0" style="font-weight: bold"/>
  <?php echo genXulComboPagoDoc(false,"menuitem",$idproveedor,$tipoproveedor) ?>
            </menupopup>
          </menulist>
        </row>
        <row id="Fila-moneda" collapsed="false" flex="1">
          <caption label="MONEDA"></caption>
          <hbox flex="1">
          <menulist id="TipoMoneda" disabled="true" style="width:150px" flex="1">
	    <menupopup>
              <menuitem  label="MONEDA" value="0" style="font-weight: bold"/>
	      <?php echo genXulComboMoneda(false,"menuitem") ?>
	   </menupopup>
	 </menulist>
         <textbox id="TipoCambioMoneda" style="width:100px" readonly="true"
                  collapsed="true"/>
         </hbox>
        </row>
        <row id="filaImportePago">
          <caption label="IMPORTE"></caption>
          <textbox id="ImportePago" value="0.00" readonly="true" ></textbox>
        </row>
        <row id="filaImportePlan" collapsed="true">
          <caption label="IMPORTE"></caption>
          <textbox id="ImportePlan" value="0" onfocus="select()"
                   onkeyup="ActualizaPendiente(false)"
                   onkeypress="return soloNumeros(event,this.value)"></textbox>
        </row>
        <row id="filaDocumentoPlan" collapsed="false">
          <caption label="DOCUMENTO"></caption>
          <textbox id="DocumentoPlan" 
                   value="<?php echo $documento;?>"
                   onkeypress="return soloAlfaNumerico(event);"/>
        </row>
        <row id="filaFechaPlan" collapsed="true">
          <caption label="FECHA PAGO"></caption>
          <datepicker id="FechaPagoPlan"  type="popup"></datepicker>
        </row>
	<row id="filaObservacion">
	  <caption label="OBS." />
	  <textbox id="xObservacion" multiline="true" rows="2" cols="10" 
                   style="font-size: 15px"
	           onpaste="return false"
                   onkeypress="return soloAlfaNumerico(event);"/>
	</row>
        <row flex="1">
          <box></box>
          <hbox>
            <button image="<?php echo $_BasePath; ?>img/gpos_cancelar.png" label=" Cancelar " 
                    oncommand="<?php echo $cbtnSalir;?>" class="btn" flex="1"></button>
            <button image="<?php echo $_BasePath; ?>img/gpos_aceptar.png" 
                    id="btnAceptar" label="  Aceptar  " flex="1"
                    oncommand="<?php echo $cbtnAceptar;?>" class="btn"></button>
          </hbox>
        </row>
      </rows>
    </grid>
  </groupbox>
</vbox>

<script>//<![CDATA[

<?php echo $esPago;?>

//]]></script>

</window>

