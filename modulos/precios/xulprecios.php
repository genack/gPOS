<?php
SimpleAutentificacionAutomatica("visual-xulframe");
header("Content-type: application/vnd.mozilla.xul+xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<?xml-stylesheet href="chrome://global/skin/" type="text/css"?>';
echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
<window id="CompraVista" title="gPOS// Precios TPV" 
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
    <script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/precios/preciostpv.js" />
    <script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tools.js" />
    <script>//<![CDATA[
    var IGV = <?php echo getSesionDato("IGV"); ?>;
    //]]></script>
    <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>

  <spacer style="height:6px"/>
  <hbox pack="center">
    <caption style="font-size: 14px;font-weight: bold;">
      <?php echo _("Precios") ?>
    </caption>
  </hbox>
  <spacer style="height:6px"/>

  <hbox  style="background-color: #d7d7d7;padding:3px;"  pack="center">
    <?php if(getSesionDato("esAlmacenCentral")){?>
    <menulist id="listLocales" label="Locales" oncommand="cambiarLocales(0)">
      <menupopup id="combolocales">
	<menuitem label="Local Actual" value="1" selected="true"/>
      </menupopup>
    </menulist>
    <?php } ?>
    
    <toolbarseparator />
    <textbox id="codigo" size="12" onfocus="onFocusTextboxBusqueda(this.id)"  
             onchange="iniPaginar()" 
	     onblur="if(this.value == '') this.value='CB/Ref.';"  
	     onkeypress="return soloAlfaNumericoCodigo(event); 
             if (event.which == 13) { soloCodigoBuscar(event,this.value); 
	     filtrarProductos(); } else { return soloCodigoBuscar(event,this.value); }" />
    
    <textbox id="descripcion" size="38" onfocus="onFocusTextboxBusqueda(this.id)" 
             onchange="iniPaginar()" 
	     onblur="if(this.value == '') this.value='Descripcion del Producto';" 
	     onkeypress="return soloAlfaNumerico(event); 
             if (event.which == 13) { soloDescripcionBuscar(event,this.value); 
	     filtrarProductos(); } else { return soloDescripcionBuscar(event,this.value); } " />

    <menulist id="listfamilia" label="FAMILIA">
      <menupopup id="combofamilia">
	<menuitem label="Todas las Familias" value="1" oncommand="cambiarFamilia(0)" selected="true"  />
      </menupopup>
    </menulist>
    <menulist id="listmarca" label="MARCA">
      <menupopup id="combomarca">
	<menuitem label="Todas las Marcas" value="1" oncommand="cambiarMarca(0)" selected="true"  />
      </menupopup>
    </menulist>
    
    <button id="btnbuscar" label="Buscar" 
            image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
            oncommand='filtrarProductos()' />
    
    <toolbarseparator />
    <menu id="listaPaginas" style="color: #000" collapsed="true" label="Pag. 1 - 10">
      <menupopup id="comboPaginas">
	<menuitem type="checkbox" label="1 - 10" oncommand="Paginar(0,'1-10')" checked="true"  />
      </menupopup>
    </menu>
    <toolbarseparator />
    <menu label="Bloques"   style="color: #000">
      <menupopup>
	<menuitem type="checkbox" label="Ventas B2C" checked="true" oncommand = "ocultar('ventaDirecta');"/>
	<menuitem type="checkbox" label="Ventas B2B"  oncommand = "ocultar('ventaCorporativa');"/>
	<menuitem type="checkbox" label="Almacén" checked="true"
                  oncommand = "ocultar('detalleProductos');"/>
      </menupopup>
    </menu>
    <toolbarseparator />
    <checkbox  id="buscar-servidor" label="Stock" checked="true" name="buscar-en-internet"/>
    <toolbarseparator />
    <!-- spacer flex="1"/ -->
    <textbox id="numLista"  value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idfamilia" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="listalocal" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idmarca" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="iniciopagina" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idlistarPV" value="0" style="visibility:hidden; width:0px"/>
    </hbox>
    <hbox flex="1" style="overflow: auto" > 
      <hbox id="xboxprecios" pack="center" >
	<hbox> 
	  <groupbox >
	    <caption label="Productos" />
	    <grid >
	      <columns>
		<column/>
		<column flex="1"/>
	      </columns>
	      <rows id="productos">
		<row>
		  <caption label="#" style="font-style:italic;"/>
		  <caption label="Producto"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox id="detalleProductos" hidden="false" >
	    <caption label="Almacén" />
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="detalle_productos">
		<row>
		  <caption label ="Stock"/>
		  <caption label ="Stock Min"/>
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox >
	    <caption label="Kardex" />
	    <grid >
	      <columns>
		<column/>
	      </columns>
	      <rows id="costo_productos">
		<row>
		  <caption label="Costo"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox id="ventaDirecta" >
	    <caption label="Venta B2C"/>
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="detalle_directo">
		<row>
		  <caption label="MU"/>
		  <caption label="IGV"/>
		  <caption label="PV"/>
		  <caption label="PV/D"/>
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox id="ventaCorporativa" hidden="true" >
	    <caption label="Venta B2B"/>
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="detalle_corporativo">
		<row>
		  <caption label="MU"/>
		  <caption label ="IGV"/>
		  <caption label="PV"/>
		  <caption label="PV/D"/>
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
      </hbox>
    </hbox>
    <hbox>
      <toolbarseparator />
      <caption id="textoproductos" label="" />
      <toolbarseparator />
      <caption id="totalCPP" label="" />
      <toolbarseparator />
      <caption id="totalMU" label="" />
      <toolbarseparator />
      <caption id="totalIGV" label="" />
      <toolbarseparator />
      <caption id="totalPVP" label="" />
      <toolbarseparator />
      <spacer flex="1"/>
      <toolbarseparator />

      <button image="<?php echo $_BasePath; ?>img/gpos_listaclientes.png" 
              id="listarPV_MD" label="  Listar Nuevos Precios" 
	      oncommand="listarPVMD()"/>
      <button id="actualizarLPV" image="<?php echo $_BasePath; ?>img/gpos_almacen.png" 
              type="menu" label="  Aplicar" collapsed="true" <?php gulAdmite("Ventas") ?> >
	<menupopup>
	  <menuitem label="Local Actual" oncommand="actualizarNuevosPV()"></menuitem>
	  <?php if(getSesionDato("esAlmacenCentral")){?>
	  <menuitem label="Todos los Locales" oncommand="actualizarAllNuevosPV()"></menuitem>
	  <?php } ?>
	</menupopup>
      </button>

      <button  image="<?php echo $_BasePath; ?>img/gpos_cancelar.png" id="eliminarLPV" 
               label=" Descartar " oncommand="eliminarNuevosPV()" collapsed="true" 
               <?php gulAdmite("Ventas") ?>/>   
      <toolbarseparator />
      <button  label="Limpiar Busqueda"  oncommand="limpiarListadoProductos()"/>   
      <toolbarseparator />
    </hbox>
    <script>
     <?php
     //if($datos)
       //echo "inicializar('".$datos."');";

     if($marcas)
       echo "iniComboMarcas('".$marcas."');";

     if($familias)
       echo "iniComboFamilias('".$familias."');";

     if($locales)
       if(getSesionDato("esAlmacenCentral"))
	 echo "iniComboLocales('".$locales."');";

     if ($codigo == '')
       $codigo = 'CB/Ref.';
     if ($descripcion == '')
       $descripcion = 'Descripcion del Producto';
     echo "iniBusqueda('".$codigo."','".$descripcion."','".$idfamilia."','".$idmarca."');";

     ?>
    </script>
    <?php
    EndXul();
    ?>
