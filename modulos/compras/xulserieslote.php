<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Productos Series',$predata="",$css='');
?>

<script>//<![CDATA[
var id               = function(name) { return document.getElementById(name); }
var cNumeroSerieLote = '';

function loadfocus(){ id('c_NumeroSerieLote').focus(); }

//]]></script>

  <vbox  align="center"  pack="top" class="box" flex="1">
    <spacer style="height:10px"/>
    <caption class="box" label="Número Serie por Lote" />

   <textbox id="c_NumeroSerieLote" flex="1" placeholder="Ingrese las serie entre saltos de linea." class="media" multiline="true" style="text-transform:uppercase;" onfocus="this.select()"
  onkeypress="return parent.soloAlfaNumericoSerieTextArea(event);" tooltiptext="Ingrese las serie entre saltos de linea."/>

   <button id="btnaceptar" image="<?php echo $_BasePath; ?>img/gpos_compras.png"
                        label=" Cargar lista NS " class="btn"  
			oncommand="cargarNumeroSerieLote()"/>
      <spacer style="height:10px"/>
  </vbox>

<script>//<![CDATA[

  function cargarNumeroSerieLote()
  {
    if( id("c_NumeroSerieLote").value !="" )
      parent.row_cargarNumeroSerieLote( id("c_NumeroSerieLote").value );  
    return parent.closepopup();
  }
  //]]></script>

</window>
