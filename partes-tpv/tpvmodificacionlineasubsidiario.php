<hbox id="editandoServicio"  align="center" pack="center">
<spacer flex="1"/>
<groupbox>
  <caption class="grande red" label="Servicio"/>
  
  <hbox>
    <grid> 
      <columns><column flex="1"/></columns>
      <rows>
	<row>
	  <caption class="media" label="Descripcion:"/>
	  <menulist  class="media" id="arregloDescripcion" 
		     onkeypress="return soloAlfaNumericoTPV(event)" 
		     onkeyup="javascript:this.value=this.value.toUpperCase();" 
		     style="text-transform:uppercase;"
		     onchange="ActualizarListaServicio()" editable="true">
	    <menupopup id="itemsServicio" class="media" >
	      <?php echo $genServicios; ?>

	    </menupopup>			
	  </menulist>	
	</row>
	<row>
	  <caption class="media" label="Empresa:"/>
	  <menulist  class="media"  id="arregloSubsidiario">
	    <menupopup class="media" >
	      <Description class="media" label="Elije ..." style="font-weight: bold;background-color: none">
	      </Description>
	      <?php  echo $genSubsidiarios; ?>
	    </menupopup>			
	  </menulist>	
	</row>
	<row>
	  <caption class="media" label="Coste:"/>
	  <textbox value="0.00" class="media precio"  id="precioServicio" 
		   onkeypress="return soloNumerosTPV(event,this.value)" />
	</row>
	<row>
	  <box/>
	  <hbox flex="1">
	    <button class="media btn" flex="1" label="Cancelar" oncommand="CancelarServicio()"/>
	    <button class="media btn"  flex="1" label="Entrar" oncommand="agnadirLineaSubsidiario()"/>
	  </hbox>
	</row>
      </rows>
    </grid>
  </hbox>

</groupbox>
<spacer flex="1"/>
</hbox>
