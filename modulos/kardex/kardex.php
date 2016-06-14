
<popupset>
  
  <popup id="AccionesBusquedaMovimientoInventario" class="media">
    <menuitem label="<?php echo _("Ver Kardex") ?>" oncommand="RevisarMovimientoSeleccionada()"/>
    <menuitem id="menuOperacionAjuste" label="<?php echo _("Nuevo Ajuste") ?>"   
              oncommand="nuevaOperacionAjuste()" collapsed="true" <?php gulAdmite("Ajustes") ?>/>
    
    <menuitem id="menuOperacionContinuar" label="<?php echo _("Continuar Inventario") ?>"  
              oncommand="continuarOperacionInventario()" collapsed="true" <?php gulAdmite("Ajustes") ?>/>

    <menuitem id="menuOperacionFinalizar" label="<?php echo _("Finalizar Inventario") ?>"  
              oncommand="finalizaOperacionInventario()" collapsed="true" <?php gulAdmite("Ajustes") ?>/>

    <menuitem id="menuOperacionInventarioInicial" label="<?php echo _("Inventario Inicial") ?>" 
	      oncommand="nuevaOperacionInventario('Inicial')" collapsed="true" <?php gulAdmite("Ajustes") ?>/>
    <menu id="menuOperacionInventario" label="<?php echo _("Nuevo Inventario") ?>" collapsed="true" <?php gulAdmite("Ajustes") ?> >
    <menupopup>
      <menuitem label="Periodico" oncommand="nuevaOperacionInventario(this.label)"/>
      <menuitem label="Intermitente" oncommand="nuevaOperacionInventario(this.label)"/>
      <menuitem label="Final" oncommand="nuevaOperacionInventario(this.label)"/>
    </menupopup>
  </menu>
  <menuitem id="menuObservaciones" label="<?php echo _("Observaciones") ?>" 
  oncommand="VerObservCompra()" collapsed="true"/>
</popup>


<popup id="AccionesBusquedaAlmacenInventario" class="media" >
  <menuitem id="menuModProducto" label="<?php echo _("Modificar Existencias") ?>"  
            oncommand="modificarArticuloSeleccionada()"/>


  <menuitem id="menuAltaRapida" label="<?php echo _("Alta Rápida") ?>"  
            oncommand="altarapidaArticulo()"  collapsed="true"/>

  <menuitem id="menuClonaArticulo" label="<?php echo _("Clonar") ?>"  
            oncommand="clonarArticulo()"  collapsed="true"/>
  <menuseparator />
  <menuitem id="menuModificarDescripcionArticulo"
            label="<?php echo _("Modificar Nombre Producto") ?>"  
            oncommand="modificarDescripcionArticulo('nombre')"  collapsed="true"/>
  <menuitem id="menuModificarDetalleArticulo"
            label="<?php echo _("Modificar Detalle Producto") ?>"  
            oncommand="modificarDescripcionArticulo('detalle')"  collapsed="true"/>  
  <vbox id="menuAlmacenFinalizarInventario" collapsed="true">
  <menuseparator />
  <menuitem  label="<?php echo _("Finalizar Inventario") ?>"  
            oncommand="finalizaOperacionInventario()"  <?php gulAdmite("Ajustes") ?> />
</vbox>
</popup>

</popupset>
