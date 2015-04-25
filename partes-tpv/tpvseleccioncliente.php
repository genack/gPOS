
<vbox flex="1" zalign="center" pack="center">
  <spacer style="height:1.5em"/>
  <tabbox flex="1">
    <tabs style="font-size: 11px;">
      <tab id="tab-selcliente" label=" Selección de cliente" image="img/gpos_buscarcliente.png" />
      <tab id="tab-newcliente" label=" Nuevo cliente"  image="img/gpos_nuevocliente.png" />
      <tab id="tab-vistacliente" label="" collapsed="true"/>
    </tabs>
    <tabpanels id="tab-boxclient" flex="1" >
      <tabpanel flex="1">
	<groupbox flex="1">
	  <caption label="Listado de clientes:" style="font-size: 13px;"/>
	  <spacer style="height:1em"/>
	  <vbox flex="1" zalign="top" pack="center" style="overflow: auto">
	    <vbox align="left" pack="top">
	      <hbox>   
		<button  image="img/gpos_tpv_clientecontado.png" id="clieLista" 
			 label="  Cliente Contado" oncommand ="pickClienteContado()"
			 style="font-size: 13px;"/>        
		<button  image="img/gpos_clienteparticular.png"  style="font-size: 13px;"
			 label="  Usar seleccionado"   oncommand ="cargarCliente('sel')"/>
		<textbox  id="buscaCliente" onkeyup="if (event.which == 13) cargarCliente('uno')" 
			  oninput="buscarCliente()" placeholder=" Buscar " 
			  style="width: 25em;font-size: 13px;"/>
		<textbox  id="buscaClienteSelect" value="0" collapsed="true"/>
	      </hbox>
	    </vbox>   
	    <spacer style="height:0.9em"/>
	    <listbox id="clientPickArea" class="listado"  flex="1" style="height: 100%;" 
		     contextmenu="AccionesclientPickArea" value="0" 
		     onkeyup="if (event.which == 13) cargarCliente('sel')" >
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
      </tabpanel>
      <tabpanel>
	<groupbox flex="1">
	  <spacer style="height:1em"/>
	  <caption label="Datos administrativos del cliente:" style="font-size: 13px;"/>
	  <spacer style="height:0.5m"/>
	  <box flex="1">
	    <grid flex="1" style="font-size: 11px;">
	      <rows flex="1"> 
		<columns><column/></columns>
		<row><caption label="Tipo"/>
		  <menulist id="TipoCliente" sizetopopup="pref" value="0" label="Particular">
		    <menupopup>
		      <menuitem value="Particular" oncommand="setTipoCliente(this.value,'')" 
				label="Particular" selected="true"></menuitem>
		      <menuitem value="Independiente" oncommand="setTipoCliente(this.value,'')"
				label="Independiente"></menuitem>
		      <menuitem value="Empresa"  oncommand="setTipoCliente(this.value,'')"
				label="Empresa"></menuitem>
		      <menuitem value="Gobierno" oncommand="setTipoCliente(this.value,'')"
				label="Gobierno"></menuitem>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		  <caption id="mtxtNombreComercial" label="Nombre"/>
		  <textbox class="xnombre" onchange="ckeckNombreComercial()" 
			   onkeyup="validaCliente(this)"  id="NombreComercial"/>
		</row>

		<row id="mtxtNombreLegal" collapsed="true">
		  <caption label="Nombre Legal"/>
		  <textbox class="xnombre" onkeyup="validaCliente(this)" 
			   style="text-transform:uppercase;"  id="NombreLegal"/>
		</row>
		<row>
		<caption id="txtNFiscal" label="DNI"/>
		<textbox class="xnif"  onchange="validaCliente(this)" maxlength="8"
			 onkeypress="return soloNumerosTPV(event,this.value)"  id="NumeroFiscal"/>
		</row>
		<row>
		  <caption label="Dirección"/>
		  <textbox class="xdireccion" onkeyup="validaCliente(this)" id="Direccion"/>
		</row>
		<row>
		<caption label="Email"/>
		<textbox class="xmail" onchange="return validaCliente(this)" id="Email"/>
		</row>
		<row>
		  <caption label="Telefono"/>
		  <textbox id="Telefono1" onkeypress="return soloNumerosTPV(event,this.value)"/>
		</row>
		<row id="mtxtFechaNacimiento">
		  <caption label="Fecha Nacim."/>
		  <datepicker id="FechaNacimiento" type="popup" />
		</row>
		<row>
		  <caption label="Comentarios"/>
		  <textbox onkeypess="return soloAlfaNumericoTPV(event)"
			   multiline="true" id="Comentarios"/>
		</row>
		<row>
		  <box/>
		  <button image="img/gpos_clienteparticular.png" oncommand="AltaCliente()" 
			  label=" Registrar"/>
		</row>
	      </rows>
	    </grid>
	  </box>
	</groupbox>
      </tabpanel>
      <tabpanel>
	<groupbox  >
	  <spacer style="height:1em"/>
	  <caption label="Datos administrativos del cliente:" style="font-size: 13px;"/>
	  <spacer style="height:0.5m"/>
	  <box>
	    <grid style="font-size: 11px;">
	      <rows> 
		<columns><column/></columns>
		<row><caption label="Tipo"/>
		  <menulist id="visTipoCliente" sizetopopup="pref" value="0" label="Particular">
		    <menupopup>
		      <menuitem value="Particular" oncommand="setTipoCliente(this.value,'vis')" 
				label="Particular" selected="true"></menuitem>
		      <menuitem value="Independiente" oncommand="setTipoCliente(this.value,'vis')"
				label="Independiente"></menuitem>
		      <menuitem value="Empresa"  oncommand="setTipoCliente(this.value,'vis')"
				label="Empresa"></menuitem>
		      <menuitem value="Gobierno" oncommand="setTipoCliente(this.value,'vis')"
				label="Gobierno"></menuitem>
		    </menupopup>
		  </menulist>
		</row>
		<row>
		<caption id="vismtxtNombreComercial" label="Nombre Comercial"/>
		<textbox class="xnombre" onkeyup="validaCliente(this)"  
			 style="text-transform:uppercase;"  id="visNombreComercial"/>
		</row>
		<row id="vismtxtNombreLegal">
		<caption  label="Nombre Legal"/>
		<textbox class="xnombre" onkeyup="validaCliente(this)" 
			 style="text-transform:uppercase;" id="visNombreLegal"/>
		</row>
		<row>
		<caption id="vistxtNFiscal" label="RUC"/>
		<textbox class="xnif" onchange="validaCliente(this)" maxlength="11"
			 onkeypress="return soloNumerosTPV(event,this.value)"  id="visNumeroFiscal"/>
		</row>
		<row>
		<caption label="Dirección"/>
		<textbox class="xdireccion" onkeyup="validaCliente(this)" 
			 style="text-transform:uppercase;" id="visDireccion"/>
		</row>
		<row>
		<caption label="Email"/>
		<textbox class="xmail" onchange="return validaCliente(this)" id="visEmail"/>
		</row>
		<row>
		  <caption label="Telefono"/>
		  <textbox id="visTelefono1" onkeypress="return soloNumerosTPV(event,this.value)" />
		</row>
		<row id="vismtxtFechaNacimiento">
		  <caption label="Fecha Nacim."/>
		  <datepicker id="visFechaNacimiento" type="popup" />
		</row>
		<row>
		  <caption  label="Comentarios"/>
		  <textbox onkeypess="return soloAlfaNumericoTPV(event)" 
			   multiline="true" id="visComentarios"/>
		</row>
		<!-- row><caption   label="Modo pago hab."/>
		<menulist  id="visModoPago"  >
		  <menupopup -->
		  <?php
		    /*foreach( $modosDePago as $value=>$label ){
		      echo "<menuitem value='$value' label='$label'/>\n";
		      }*/
		  ?>
		  <!-- /menupopup>
		</menulist>
		</row -->
		  <row>	
		    <box/>
		    <box>
		      <button image="img/gpos_aceptar.png" oncommand="ModificarCliente()" 
			      label="Modificar" flex="1"/>
		      <!--button image="img/gpos_cancelar.png" label="Eliminar"
			      oncommand="EliminarClienteActual()" flex="1"/-->
		    </box>
		  </row>
	      </rows>
	    </grid>
	  </box>
	</groupbox>
      </tabpanel>
    </tabpanels>
  </tabbox>
  <button class="media"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>

</vbox>

