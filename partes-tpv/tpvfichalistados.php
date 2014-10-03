<vbox flex="1">
<groupbox flex="1"><caption class="media"  label="Listados de TPV"/>
<iframe id="generadorListados" flex="1" src="<?php echo $_BasePath; ?>modulos/generadorlistados/formlistados.php?area=tpv" style="background-color: white"/>
</groupbox>
<button class="media"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>
</vbox>