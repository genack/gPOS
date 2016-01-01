<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Compras Productos',$predata="",$css='');
StartJs($js='modulos/compras/comprar.js?v=1');
?>

<vbox  align="center"  pack="center"  class="box">
  <spacer style="height:10px"/>
  <caption class="h1"><?php echo $titulo;?></caption>
  <spacer style="height:10px"/>
  <caption label="<?php echo $producto  ?>" />
  <spacer style="height:5px"/>
</vbox>
<hbox  align="top"  pack="center"  class="box" flex="1">
  <spacer flex="1"></spacer>
  <groupbox>

    <hbox>
      <grid>
	<rows>
	  <row flex="0">
	    <caption label="Cantidad" />
	    <hbox>
	      <vbox flex='1'>

	      <?php if($menudeo) echo '
	      <label value="'.$Contenedor.' / '.$UContenedor.'"/>';?>
	      <textbox id="cantidad" size="13" 
 	                onchange="actualizarCantidad(<?php echo $UContenedor;?>)"  
	                onfocus="this.select()" value="<?php echo $cantidad;?>"
			onkeypress="return soloNumerosEnteros(event,this.value)" >                        
	       </textbox>	 	
	      </vbox>

	      <?php if($menudeo) echo '
              <vbox>
               <label value="Unidades"/>
               <textbox id="unidades" size="7" 
                        onchange="actualizarCantidad('.$UContenedor.')"
                        onfocus="this.select()" value="'.$unidades.'"
			onkeypress="return soloNumerosEnteros(event,this.value)">			
	       </textbox>	
              </vbox>';?>

	    </hbox>
	  </row>

	  <row>
	    <caption label="Costo <?php echo $txtcosto?>" />
	    <hbox flex="1">
	      <textbox id = "costo" value="<?php echo $CostoUnitario; ?>" size="17" 
	              onchange="actualizarCostoTotal(<?php echo $UContenedor;?>); 
		      actualizarImporte();" onfocus="this.select()"
		      onkeypress="return soloNumeros(event,this.value)"/>

	      <?php if($menudeo) echo '
	      <button class="btn"  label="x'.$Contenedor.'" 
                       oncommand="mostrarCostoTotal('.$UContenedor.');"/>';
                    else echo '
		    <label value="x Unidad"/>'; ?>
	    </hbox>
	  </row>

	  <row>
	    <caption label="Costo Total <?php echo $txtcosto?>" />
	    <textbox id = "costototal" value="0" size="12" readonly="true" />
	  </row>

	  <row>
	    <caption label="Descuento <?php echo $txtcosto?>" />
	    <hbox>
	      <textbox id="descuento" value="<?php echo $dscto;?>" size="17" 
		       onchange="actualizarPorcentajeDescuento();
			         actualizarImporte();" 
		       onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" />

	      <button class='btn'  label="%" oncommand="mostrarPorcentajeDescuento();"/>
	    </hbox>
	  </row>

	  <row>
	    <caption label="Importe <?php echo $txtcosto?>" />
	    <textbox id = "importe"  value="0" size="12" readonly="true"/>
	  </row>
	  
          <?php if ($manejalote) echo '
          <row>
            <caption label="Lote Producción"/>
            <textbox id = "lote" size="12" value="'.$lt.'" style="text-transform:uppercase;" 
	       onkeyup="javascript:this.value=this.value.toUpperCase();" 
               onkeypress="return soloAlfaNumericoCodigo(event)"/>
          </row>';?>

          <?php if ($manejafv) echo ' 
          <row>
            <caption label="Fecha Vencimiento"/>
	    <hbox>
             <datepicker id="Desde" type="popup" value="'.$fv.'"/>
	   </hbox>
          </row>';?>

 	  <row>
	    <vbox></vbox>
	    <vbox flex="1"> 
	      <checkbox flex="1" label="Seguir en la búsqueda" tabindex="6" 
			id="pdListCheck" collapsed="<?php echo $pblistcheck;?>"
			oncommand="setblockListado(this.checked);" checked="<?php echo $pblist;?>"/>
	      <hbox flex="1" pack="center">
		<button class='btn' label='Comprar...' image='<?php echo $_BasePath; ?>img/gpos_compras.png' 
			oncommand='aceptar(<?php echo $id.",".$manejaserie.",".$fila.",".$trasAlta.",".$esPopup?>)'/>
		<button class='btn' label="Cancelar" image="<?php echo $_BasePath; ?>img/gpos_vaciarcompras.png"
			oncommand="terminar('Limpiando datos del Producto...',0,<?php echo $esPopup; ?>);"/>
	      </hbox>
	    </vbox>
	  </row>

	</rows>
      </grid>
    </hbox>
  </groupbox>	
  <spacer flex="1"></spacer>
</hbox>

<script>
  actualizarCantidad(<?php echo $UContenedor; ?>);
</script>

</window>
