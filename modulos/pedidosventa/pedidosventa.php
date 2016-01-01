
<popupset>
  
  <popup id="AccionesBusquedaPedidosVenta" class="media">
     <menuitem id="mheadModifica" label="Editar" oncommand="ModificarPedidos()"/>
     <menuseparator />
     <menuitem id="mheadImprimir" label="<?php echo _("Imprimir") ?>" 
               oncommand="ImprimirPedidosVenta()"/>
     <menuitem label="<?php echo _("Ver Obs.") ?>" 
               oncommand="VerObservPedidosVenta()"/>
  </popup>  

  <popup id="AccionesDetallesPedidosVenta" class="media">
     <menuitem id="mheadModificaDetalle" label="Editar Item" 
               oncommand="ModificarDetallePedidos()"/>
     <menuitem id="mheadModificaDetalleNS" label="Editar Número Serie" 
               oncommand="ModificarDetallePedidosNS()" collapsed="false"/>
     <menuseparator />
     <menuitem id="mheadQuita" label="Quitar Producto" 
               oncommand="ModificarPedidosVenta(8,true)"/>
  </popup> 

</popupset>
