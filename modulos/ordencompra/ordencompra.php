
<popupset>
  
  <popup id="AccionesBusquedaOrdenCompra" class="media">
     <menuitem label="<?php echo _("Detalles") ?>"  oncommand="RevisarOrdenCompraSeleccionada()"/>
     <menuitem label="<?php echo _("Observaciones") ?>" oncommand="VerObservOrdenCompra()"/>
     <menuitem label="<?php echo _("Imprimir") ?>" oncommand="ImprimirOrdenCompraSeleccionada('pdf')"/>
     <menuseparator />
     <menuitem id="mheadModifica" label="<?php echo _("Modificar")?>" oncommand="ModificarOrden()" />

     <menuitem id="mheadEdita" label="<?php echo _("Editar Presupuesto") ?>"   oncommand="ModificarOrdenCompra(10)" />
     <menuitem id="mheadClonar" label="<?php echo _("Clonar")?>" oncommand="ModificarOrdenCompra(20)" />
     <menuitem id="mheadConsolida" label="<?php echo _("Consolidar")?>" oncommand="ModificarOrdenCompra(9)" />
     <menuseparator />
     <menuitem id="mheadAgregaPago" label="<?php echo _("Agregar Pago")?>" oncommand="AddOrdenCompra()" <?php gulAdmite("Pagos") ?>  />
     <menuitem id="mheadCreaProforma" label="<?php echo _("Proformar")?>" oncommand="proformarOrdenCompra()" />
     <menuseparator />
     <menuitem id="mheadConfirma" label="<?php echo _("Confirmar") ?>"  oncommand="ModificarOrdenCompra(3)"/>
     <menu id="mheadRecibe" label="Recibir" <?php gulAdmite("Compras") ?>  >
      <menupopup>
	<menuitem label="Factura" oncommand="ModificarOrdenCompra(11)"/>
	<menuitem label="Boleta"  oncommand="ModificarOrdenCompra(12)"/>
	<menuitem label="Albaran" oncommand="ModificarOrdenCompra(13)"/>
	<menuitem label="Ticket"  oncommand="ModificarOrdenCompra(14)"/>
      </menupopup>
     </menu>
     <menuseparator />
     <menuitem id="mheadCancela" label="<?php echo _("Cancelar") ?>"  oncommand="ModificarOrdenCompra(5)"/>
  </popup>  

  <popup id="AccionesDetallesOrdenCompra" class="media">
     <menuitem id="mdetModifica" label="Modificar" oncommand="ModificarOrdenDetalle()"/>
     <menuseparator />
     <menuitem id="mdetQuita" label="Quitar Producto" oncommand="ModificarOrdenCompra(8,true)"/>
  </popup> 

</popupset>
