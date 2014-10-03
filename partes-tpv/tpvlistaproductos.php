    <listbox id="listaProductos" flex="1" contextmenu="accionesLista" ondblclick="agnadirPorMenu(1)" class="listado" onkeypress="if (event.which == 13) setTimeout('agnadirPorMenu(\'preguntar\')',50)" onclick="menuContextualPreVentaTPV(false)">
     <listcols flex="1">
		<listcol/>		
		<listcol flex="8"/>
		<listcol/>		
		<listcol/>			
		<listcol/>
		<listcol style="width:1em"/>
     </listcols>
     <listhead>
		<listheader label="CR"/>
		<listheader label="Producto"/>
		<listheader label=""/>
		<listheader label="Stock" />
		<listheader label="PV/U"/>
		<listheader label="" />
     </listhead>

    </listbox>
