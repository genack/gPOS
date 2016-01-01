   <panel id="panelElijeProducto" style="border:1px solid #aaa"  class="box">
     <vbox align="center" style="padding:1em">	
       <caption label="Agregar Productos"  class="h1"/>
     </vbox>
     <hbox align="center">	
       <spacer style="width: 5px"/>
       <image src="img/gpos_barcode.png" height="16" width="16"/>
       <caption  label="CB" class="compacta"/>
       <textbox   id="panelCB"   size="12" onfocus="select()" class="bbox"   flex="1" 
		  onkeypress="return soloNumerosTPV(event,this.value)" 
		  onkeyup="if (event.which == 13) agnadirPanelPorCodigoBarras()" />
       
       <spacer style="width: 32px"/>
       
       <image src="img/gpos_tpvreferencia.png" />
       <caption label=" CR"  class="compacta" />
       <textbox  id="panelREF"  size="8" onfocus="select()" class="bbox" flex="1" 
		 onkeyup="agnadirPanelPorReferencia()" />
       <spacer style="width: 30px"/>
       <image src="img/gpos_productos.png" height="16" width="16"/>
       <caption label="Producto"  class="compacta" />
       <textbox id="panelNOM"  size="20" onfocus="select()" class="bbox" flex="1" 
		placeholder=" nombre | marca ó modelo..." 
		onkeypress=" if (event.which == 13) focusPanelListaProductos(); else agnadirPanelPorNombre();" />
       <spacer style="width: 10px"/>
     </hbox>

     <vbox flex="1">
       <listbox id="listaPanelProductos" flex="1" ondblclick="panelAgnadirProducto()" 
		class="listado" onkeypress="if (event.which == 13) panelAgnadirProducto()">
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
     </vbox>
    </panel>

    <listbox id="listaProductos" flex="1" contextmenu="accionesLista" ondblclick="agnadirPorMenu(1)" 
	     class="listado" onclick="menuContextualPreVentaTPV(false)" 
	     onkeypress="if (event.which == 13) setTimeout('agnadirPorMenu(\'preguntar\')',50)" >
      <listcols flex="1">
	<listcol/>		
	<listcol/>
	<listcol/>		
	<listcol/>			
	<listcol/>
	<listcol/>			
	<listcol  flex="8"/>		
	<listcol/>			
	<listcol/>
	<listcol style="width:1em"/>
      </listcols>
      <listhead>
	<listheader label="CR"/>
	<listheader label="Producto"/>
	<listheader label=""/>
	<listheader label=""/>
	<listheader label=""/>
	<listheader label=""/>
	<listheader label=""/>
	<listheader label="Stock" />
	<listheader label="PV/U"/>
	<listheader label="" />
      </listhead>
    </listbox>
