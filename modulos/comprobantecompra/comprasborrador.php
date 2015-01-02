
<popupset>
  
  <popup id="AccionesBusquedaCompra" class="media">

     <menuitem label="<?php echo _("Detalles") ?>"  oncommand="RevisarCompraSeleccionada()"/>
     <menuitem id="mheadObs" label="<?php echo _("Observaciones") ?>" oncommand="VerObservCompra()"/>
     <menuitem label="<?php echo _("Imprimir") ?>" oncommand="ImprimirCompraSeleccionada('pdf')"/>
     <menuseparator />
     <menuitem id="mheadModifica" label="<?php echo _("Modificar")?>" oncommand="ModificarComprobante()" <?php gulAdmite("Compras") ?>  />
     <menuitem id="mheadConsolida" label="<?php echo _("Consolidar")?>" oncommand="ModificarCompra(9)" <?php gulAdmite("Compras") ?> />
     <menuitem id="mheadFactura" label="<?php echo _("Facturar")?>" oncommand="ModificarCompra(10)" <?php gulAdmite("Compras") ?> />
     <menuitem id="mheadBoleta" label="<?php echo _("Boletar")?>" oncommand="ModificarCompra(11)" <?php gulAdmite("Compras") ?> />
     <menuseparator />
     <menuitem id="mheadcancela" label="<?php echo _("Cancelar") ?>"  oncommand="ModificarCompra(8)" <?php gulAdmite("Compras") ?> />

  </popup>

  <popup id="AccionesDetallesCompra" class="media">
     <menuitem id="mdetModifica" label="Modificar"    oncommand="ModificarComprobanteDetalle()" <?php gulAdmite("Compras") ?>/>
     <menuitem id="mdetNSerie" label="Número Serie"    oncommand="sNSProductosCompra()"/>
     <menuseparator />
     <menuitem id="mdetQuita" label="Quitar" oncommand="ModificarCompra(17,true)"/>
  </popup>

</popupset>
