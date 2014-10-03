<box>
<spacer flex="1"/>
<box  align="center" pack="center">
<spacer flex="1" />
<deck>
<groupbox><caption  class="media" label="Abonar Ticket"/>
<grid>
	<columns><column/></columns>
	<rows>
		<row>
		<description class="media">Num Ticket:</description>
		<caption  class="media" id="abono_numTicket"   label=""/>
		</row>
		<row>
		<description  class="media">Debe:</description>
		<caption  class="media" id="abono_Debe" style="color: ref;font-weight: bold"  label="0.00"/>
		</row>
		<row>
		<description class="media">Abona:</description>
		<caption  class="media" id="abono_nuevo"  label="0.00"/>
		</row>	
		<row>
		<description  class="media">Nuevo pendiente:</description>
		<caption  class="media" id="abono_Pendiente"  label="0.00"/>
		</row>	

		<spacer style="height: 8px"/>
		<row >		
		<description  class="media">EFECTIVO</description>
		<textbox  class="media" id="abono_Efectivo"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<row>
		<description class="media">BONO</description>
		<textbox  class="media" id="abono_Bono"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<row >
		<description class="media">TARJETA</description>
		<textbox  class="media" id="abono_Tarjeta"   value="0" onkeyup="ActualizaPeticionAbono()" onkeypress="ActualizaPeticion()"/>
		</row>			
		<spacer style="height: 8px"/>				
		<row>
		<box/>
		<hbox>
		<button  class="media" flex="1" image="img/gpos_imprimir.png"  label=" ¿Abonar?" oncommand="RealizarAbono()"/>		
		<button  class="media" flex="1"  label="Cancelar" oncommand="VolverVentas()"/>
		</hbox>
		</row>	
	</rows>
</grid>
</groupbox>
</deck>
</box>
<spacer flex="1"/>
</box>
