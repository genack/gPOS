<vbox id="boxServicios">
  <!-- Servicios Encabezado-->
  <vbox> 
    <vbox pack="center" align="center">
      <caption id="Outsourcing"  label="Outsourcing" style="font-size:14px"/>
    </vbox>
    <spacer style="height:4px"/>
  </vbox> 


    <!-- Formulario elije Cliente -->
    <panel id="panelElijeCliente" style="border:1px solid #aaa">

	<vbox pack="center" align="center">
	  <caption id="wtitleFormOrdenServicioDet"
		   style="padding:0.4em;font-size: 14px;font-weight: bold;"
		   label="<?php echo _("Elije Cliente") ?>" />
	</vbox>
	<groupbox flex="1">
	  <caption label="Listado de clientes:" style="font-size: 13px;"/>
	  <spacer style="height:.4em"/>
	  <vbox flex="1" zalign="top" pack="center" style="overflow: auto">
	    <vbox align="left" pack="top">
	      <hbox>   
		<button  image="img/gpos_clienteparticular.png"  style="font-size: 13px;"
			 label="  Usar seleccionado"   oncommand ="panelcargarCliente('sel')"/>
		<textbox  id="panelbuscaCliente" 
			  onkeyup="if (event.which == 13) panelcargarCliente('uno')" 
			  oninput="panelbuscarCliente()" placeholder=" Buscar " 
			  style="width: 25em;font-size: 13px;"/>
		<textbox  id="panelbuscaClienteSelect" value="0" collapsed="true"/>
	      </hbox>
	    </vbox>   
	    <spacer style="height:0.9em"/>
	    <listbox id="panelclientPickArea" class="listado"  flex="1"
		     style="width:60em;height: 15em;" ondblclick="panelcargarCliente('sel')"
		     onkeyup="if (event.which == 13) panelcargarCliente('sel')" >
	      <listcols>
		<listcol flex="1"/>
		<listcol flex="1"/>
		<listcol flex="8"/>
		<listcol flex="1"/>
		<listcol flex="1"/>
		<listcol flex="3"/>
	      </listcols>
	      <listhead>
		<listheader label="" />
		<listheader label="DNI/RUC" />
		<listheader label="Cliente" />
		<listheader label="Debe" />
		<listheader label="Bono" />
		<listheader label="Categoría"/>
	      </listhead>
	    </listbox>
	  </vbox>
	</groupbox>


    </panel>
    <!--/Formulario elije Cliente -->

<!-- Servicios tercerizados -->
<vbox id="boxOutsourcing" flex="1">
  <spacer style="height:0.4em"/>
  <hbox align="start" pack="center" style="background-color: #d7d7d7;padding:0.8em;">
    <vbox>
      <description>Empresas:</description>
      <menulist id="SubsidiarioListaServicios" oncommand="ListadoSubsidiarios()">
	<menupopup>
	  <menuitem label="Todos"/>
	  <?php  echo $genSubsidiarios; ?>
	</menupopup>
      </menulist>
    </vbox>
    <vbox>
      <description>Estado:</description>
      <menulist id="StatusListaServicios" oncommand="ListadoSubsidiarios()">
	<menupopup>
	  <menuitem label="Todos"/>
	  <?php
	     foreach( $statusServicios as $value=>$label ){
	       echo "<menuitem value='$value' label='$label'/>\n";
	     }
	  ?>
	</menupopup>
      </menulist>
    </vbox>
    <vbox>
      <description>Desde:</description>
      <datepicker id="FechaBuscaServiciosTerceros" type="popup" 
		  onblur="ListadoSubsidiarios()"/>
    </vbox>
    <vbox>
      <description>Hasta:</description>
      <datepicker id="FechaBuscaServiciosTercerosHasta" type="popup" 
		  onblur="ListadoSubsidiarios()"/>
    </vbox>
    <vbox>
      <description>Código Comprobante:</description>
      <textbox id="TicketListaServicios" onkeypress="return soloAlfaNumericoCodigoTPV(event)"/>
    </vbox>
    <vbox style="margin-top:.9em">
      <button id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" oncommand="ListadoSubsidiarios()"/>
    </vbox>

  </hbox>
  
  <caption style="font-size:10px; font-weight: bold;" label="<?php echo _("Servicios") ?>" />
  <listbox id="busquedaListaServicios" flex="1" contextmenu="popupListadoServicios" 
           onclick="RevizarServicioSeleccionado()">
    <listcols flex="1">
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />
      <listcol style="maxwidth: 35em"/>
      <splitter class="tree-splitter" />
      <listcol flex="1"/>
      <splitter class="tree-splitter" />
      <listcol style="maxwidth: 5em"/>
      <splitter class="tree-splitter" />
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />		
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />		
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />				
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />				
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />
      <listcol style="maxwidth: 11em"/>
      <splitter class="tree-splitter" />
    </listcols>
    <listhead>
      <listheader label="Empresa"/>
      <listheader label="Producto"/>
      <listheader label="Servicios"/>
      <listheader label="Código Comprobante"/> 
      <listheader label="Estado"/>
      <listheader label="Fecha registro"/> 
      <listheader label="Enviado"/>	
      <listheader label="Recibido" />
      <listheader label="Importe" />
      <listheader label="Pendiente" />
      <listheader label="" />
    </listhead>
  </listbox>
  
  <vbox align="center" id="vboxFormServicios" collapsed="true">
    <spacer style="height:1em"/>
    <hbox pack="center" align="center">
      <caption  style="font-size: 14px;font-weight: bold;" 
                label="Editar Servicios Tercerizados"/>
    </hbox>
        <spacer style="height:1em"/>
	<hbox pack="center" align="center">
	  <caption id="titleServicios" label=""/>
	</hbox>
        <spacer style="height:0.5em"/>
        <hbox>
          <groupbox>
	    <grid>
	      <column>
		<column></column>
		<column></column>
	      </column>
	      <rows>
		<row>
		  <caption label="Importe (<?php echo $Moneda[1]['S']?>) " align="center"/>
		  <textbox id="tbox_importe"  style="width:20em;"
			   onkeypress="return soloNumeros(event,this.value);"
			   onchange="ModificarServicio(1)"/>
		</row>
		<row>
              <hbox>
		<caption label="Subsidiario " align="center"/>
		<toolbarbutton label="+" oncommand="ModificarServicio(2)"/>
              </hbox>
	      <textbox id="tbox_subsidiario" value="" readonly="true"/>
	      <textbox id="idsubsidiariohab" value="" collapsed="true"/>
		</row>
		<row>
	      <caption label="Estado" align="center"/>
              <menulist id="StatusListaServicios1" 
                        oncommand="changeEstadoServicio();ModificarServicio(3)">
	        <menupopup>
	          <menuitem id="itm_pdte_envio" label="Pdte Envio" value="Pdte Envio"/>	
	          <menuitem id="itm_enviado" label="Enviado" value="Enviado"/>	
	          <menuitem id="itm_recibido" label="Recibido" value="Recibido"/> 
	          <menuitem id="itm_entregado" label="Entregado" value="Entregado"/>	
	        </menupopup>
              </menulist>
	      <textbox id="tbox_estado"
		       style="color: ref;font-weight: bold" value="" collapsed="true"/>
		</row>
		<row id="row_enviado" collapsed="true">
		  <caption label="Enviado" align="center"/>
		  <hbox>
	            <datepicker id="date_enviado" type="popup" onblur="ModificarServicio(4)"/>
	            <timepicker id="time_enviado" type="popup" onblur="ModificarServicio(4)"/>
		  </hbox>
		</row>
		<row id="row_recibido" collapsed="true">
		  <caption label="Recibido" align="center"/>
		  <hbox>
	            <datepicker id="date_recibido" type="popup" onblur="ModificarServicio(5)"/>
	            <timepicker id="time_recibido" type="popup" onblur="ModificarServicio(5)"/>
		  </hbox>
	    </row>
		<row id="row_entregado" collapsed="true">
		  <caption label="Entregado" align="center"/>
              <hbox>
	        <datepicker id="date_entregado" type="popup" onblur="ModificarServicio(6)"/>
	        <timepicker id="time_entregado" type="popup" onblur="ModificarServicio(6)"/>
              </hbox>
		</row>
		<spacer style="height: 10px"/>
	      </rows>
	    </grid>
	  </groupbox>
	  <groupbox>
	    <grid>
	      <rows>
		<row id="row_documento" collapsed="true">
		  <caption label="Documento" align="center"/>
		  <hbox>
		    <menulist id="SubsidiarioDocumento">
	              <menupopup>
			<menuitem id="itm_ticket" label="Ticket" value="Ticket"/>	
			<menuitem id="itm_boleta" label="Boleta" value="Boleta"/>	
			<menuitem id="itm_factura" label="Factura" value="Factura"/> 
	              </menupopup>
		    </menulist>
		    <textbox id="tbox_coddocumento" flex="1" 
			     onkeypress="return soloNumericoCodigoSerie(event)"
			     onchange="ModificarServicio(9)"/>
		  </hbox>
		</row>
		
		<row id="row_pendiente" collapsed="true">
		  <caption label="Pendiente (<?php echo $Moneda[1]['S']?>)" align="center"/>
		  <textbox id="tbox_pendiente" value="" readonly="true"/>
		</row>
		<row id="row_abonar" collapsed="true">
		  <caption label="Abonar (<?php echo $Moneda[1]['S']?>)" align="center"/>
		  <textbox id="tbox_abonar" value="0"
			   onkeypress="return soloNumeros(event,this.value);"
			   onkeyup="ActualizarPeticionCoste()"
			   onchange="ModificarServicio(7)"/>
		</row>
		<row>
		  <caption label="Observación" />
		  <textbox id="tbox_observacion" rows="1" multiline="true" style="width:20em;"
			   onkeypress="return soloAlfaNumerico(event);"
			   onchange="ModificarServicio(8)"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>
	</hbox>
  </vbox>
</vbox>
<button class="media" image="img/gpos_volver.png" label=" Volver TPV" oncommand="VerTPV()"/>
</vbox>
