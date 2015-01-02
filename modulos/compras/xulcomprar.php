<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="CompraVista" title="<?php echo "gPOS // Compras - ".$producto?>" 
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
    <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/compras/comprar.js" />
    <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js"/>
<vbox  align="center"  pack="center">
  <spacer style="height:30px"/>
  <caption label="<?php echo $titulo;?>" style="font-size: 14px;font-weight: bold;"/>
  <spacer style="height:20px"/>
  <caption label="<?php echo $producto  ?>" style="font-size: 12px;font-weight: bold;"/>
</vbox>
<spacer style="height:10px"/>
<hbox  align="center"  pack="center">
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
	    <caption label="Costo" />
	    <hbox flex="1">
	      <textbox id = "costo" value="<?php echo $CostoUnitario; ?>" size="17" 
	              onchange="actualizarCostoTotal(<?php echo $UContenedor;?>); 
		      actualizarImporte();" onfocus="this.select()"
		      onkeypress="return soloNumeros(event,this.value)"/>

	      <?php if($menudeo) echo '
	      <button  label="x'.$Contenedor.'" 
                       oncommand="mostrarCostoTotal('.$UContenedor.');"/>';
                    else echo '
		    <label value="x Unidad"/>'; ?>
	    </hbox>
	  </row>

	  <row>
	    <caption label="Costo Total" />
	    <textbox id = "costototal" value="0" size="12" readonly="true" />
	  </row>

	  <row>
	    <caption label="Descuento" />
	    <hbox>
	      <textbox id="descuento" value="<?php echo $dscto;?>" size="17" 
		       onchange="actualizarPorcentajeDescuento();
			         actualizarImporte();" 
		       onfocus="this.select()"
		       onkeypress="return soloNumeros(event,this.value)" />

	      <button  label="%" oncommand="mostrarPorcentajeDescuento();"/>
	    </hbox>
	  </row>

	  <row>
	    <caption label="Importe" />
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
		<button label='Comprar...' image='<?php echo $_BasePath; ?>img/gpos_compras.png' 
			oncommand='aceptar(<?php echo $id.",".$manejaserie.",".$fila.",".$trasAlta?>)'/>
		<button label="Cancelar" image="<?php echo $_BasePath; ?>img/gpos_vaciarcompras.png"
			oncommand="terminar('Limpiando datos del Producto...');"/>
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
