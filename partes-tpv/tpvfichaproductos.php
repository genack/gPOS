<vbox flex="1" style="overflow: auto">
<groupbox flex="1">
  <vbox pack="center" align="center">
    <caption style="font-size: 16px;font-weight: bold;">
      <?php echo _("Ficha de Existencias") ?>
    </caption>
    <spacer style="height:8px"/>
    <caption style="font-size: 12px;" id="fichaProductoNombre" class="compacta"  label=""  />
  </vbox>
  <spacer style="height:10px"/>
  <box id="fichaProducto" style="overflow: -moz-scrollbars-horizontal;width: 800px;height: 100%"/>
</groupbox>
<button class="media btn" image="img/gpos_volver.png" label="Volver TPV" oncommand="ToggleFichaFormOUT()"/>
</vbox>
