
<popupset>
  <popup id="AccionesOrdenServicio">
    <menuitem id="btnOrdenServicio" <?php gulAdmite("SAT") ?> 
              label="<?php echo _("Nuevo") ?>"
              oncommand="mostrarFormOrdenServicio('Nuevo')" />
    <menuitem id="itemEditarOrdenServicio" <?php gulAdmite("SAT") ?> 
              label="<?php echo _("Editar") ?>"
              oncommand="mostrarFormOrdenServicio('Editar')"/>
    <menuseparator />
    <menuitem id="itemFacturarOrdenServicio" label="<?php echo _("Facturar") ?>"
              oncommand="facturarOrdenServicio()"/>
    <menuitem id="itemImprimirOrdenServicio" label="<?php echo _("Imprimir") ?>"
              oncommand="imprimirOrdenServicio()"/>
    <menuseparator />
    <menuitem id="itemMostrarSuscripcionFichaTecnica" label="<?php echo _("Ficha Técnica") ?>"
              oncommand="mostrarSuscripcionFichaTecnica('orden')" collapsed="true"/>
  </popup>
  <popup id="AccionesOrdenServicioDetalle">
    <menuitem id="itemAgregarServicio" <?php gulAdmite("SAT") ?>
              label="<?php echo _("Agregar Servicio") ?>"
              oncommand="mostrarFormOrdenServicioDet('Nuevo',true)"/>
    <menuitem id="btnOrdenServicioDet" label="<?php echo _("Mostrar Panel Servicio") ?>"
              oncommand="mostrarFormOrdenServicioDet('Nuevo',false)"/>
    <menuitem id="itemAgregarProducto" <?php gulAdmite("SAT") ?>
              label="<?php echo _("Agregar Producto") ?>"
              oncommand="elijePanelProducto('OrdenServicio')"/>

    <menuseparator />   
    <menuitem id="itemVerOrdenServicioDet" label="<?php echo _("Ver detalle") ?>"
              oncommand="mostrarFormOrdenServicioDet('Ver',false)"/>
    <menuitem id="itemEditarOrdenServicioDet" <?php gulAdmite("SAT") ?> 
              label="<?php echo _("Editar detalle") ?>"
              oncommand="mostrarFormOrdenServicioDet('Editar',false)"/>
    <menuitem id="itemQuitarProducto" <?php gulAdmite("SAT") ?>
              label="<?php echo _("Quitar Detalle") ?>"
              oncommand="quitarProductoOrdenServicioDet()"/>
    <menuseparator />   
    <menuitem id="itemClonarServicio" <?php gulAdmite("SAT") ?>
              label="<?php echo _("Clonar Servicio") ?>"
              oncommand="clonarOrdenServicioDet()"/>
  </popup>
</popupset>
