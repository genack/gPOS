<groupbox id="panelDerecho"> 
<groupbox align="center" pack="center" flex="1" style="height: 100px;max-height: 100px">
	<spacer flex="1"/>
	<image src="img/gpos_imgdefault.png" id="muestraProducto" style="width:90px;height:90px"/>
	<spacer flex="1"/>
</groupbox>
<groupbox align="center" pack="center" >
  <hbox>
  <image src="img/gpos_barcode.png" height="16" width="16" id="muestraProductoIcon" collapsed="true"/>
  <label id='muestraProductoCB' style="font-size: 1.8em;"/>
  </hbox>
  <!-- box><label id='nombreProducto'   style="width:70px"  /></box -->
</groupbox>

<button id="botonImprimir" disabled="<?php esCerradaArqueoCaja($IdLocalActivo)?>"  crop="end" image="img/gpos_imprimir.png" label=" <?php echo _("Vender") ?>" class="compacta btn" oncommand="AbrirPeticion()"/>

 <hbox>
   <button id="botonBorrar" flex="1" crop="end" image="img/gpos_vaciarcompras.png" label=" <?php echo _("Cancelar") ?>"  class="compacta btn" oncommand="BorrarVentaTPV()"/>	
   <button id="botonGuardar" flex="1" crop="end" image="img/gpos_compras.png" label=" <?php echo _("Guardar") ?>"  class="compacta btn" oncommand="GuardarPreVentaTPV()"/>	
 </hbox>          

<deck id="modoMensajes" flex="1" style="width: 200 !important">
<?php include("tpvmensajeria.php"); ?>
</deck>
<vbox id="ventasButton">
 <hbox >
	<button flex="1" id="VerVentasButton" crop="end" image="img/gpos_tpv_ventas.png" label="<?php echo _(" Ventas") ?>"  class="compacta btn" oncommand="VerVentas()"/>		
	<?php if($modulos["arreglodecaja"]): ?>
	<button flex="1" id="VerCajaButton" crop="end" image="img/gpos_tpvcaja.png" label="<?php echo _(" Caja") ?>"  class="compacta btn" oncommand="VerCaja()" <?php gulAdmite("CajaTPV") ?>/>
	<?php endif; ?>
 </hbox>
 <hbox>
    <button  flex="1" id="VerServiciosButton" crop="end" image="img/gpos_tpvservicios.png" label="<?php echo _(" Servicios") ?>" <?php gulAdmite("Servicios") ?>
            class="compacta btn" oncommand="VerServicios()"/>
    <?php if($modulos["generadorlistados"]): ?>	    
    <button flex="1" id="VerListadosButton" crop="end" image="img/gpos_tpvlistado.png" label="<?php echo _("Listados") ?>" <?php gulAdmite("InformeLocal") ?> class="compacta btn" oncommand="VerListados()"/>
    <?php endif; ?>
 </hbox>
</vbox>
    <!-- 
    <button id="VolverTPV"  image="img/gpos_volver.png"  crop="end" label="<?php echo _("Volver TPV") ?>"  class="compacta" oncommand="VerTPV()"/>
    -->


</groupbox>
