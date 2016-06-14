<?php
SimpleAutentificacionAutomatica("visual-xulframe");
StartXul('Precios',$predata="",$css='');
StartJs($js='modulos/precios/preciostpv.js?v=4.5');
?>

  <script>//<![CDATA[
  var cImpuestoGral = <?php echo getSesionDato("IGV"); ?>;
  var cMargenUtilidad = <?php echo $MargenUtilidad; ?>;
  var cDescuentoGral  = <?php echo $DescuentoGral ?>;
  var cMetodoRedondeo = "<?php echo $MetodoRedondeo ?>";
  var cImpuestoIncluido = <?php echo $COPImpuesto ?>;
  var cWhosesale        = <?php echo $wesl ?>;
  //]]></script>
  <?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda); ?>


<hbox class="box" pack="center">
  <caption class="h1">
    <?php echo _("Precios") ?>
  </caption>
</hbox>


  <hbox  class="box"  pack="center">
    <?php if(getSesionDato("esAlmacenCentral")){?>
    <menulist id="listLocales" label="Locales" >
      <menupopup id="combolocales">
	<menuitem label="Local Actual" value="1" oncommand="cambiarLocales(<?php echo $IdLocal?>)" selected="true"/>
      </menupopup>
    </menulist>
    <?php } ?>
    
    <textbox id="codigo" size="12" onchange="iniPaginar()" placeholder="CB/Ref." 
	     onkeypress="return soloAlfaNumericoCodigo(event); 
             if (event.which == 13) { soloCodigoBuscar(event,this.value); 
	     filtrarProductos(); } else { return soloCodigoBuscar(event,this.value); }" />
    
    <textbox id="descripcion" size="38" onchange="iniPaginar()" placeholder=" producto | marca ó modelo ó detalle..."  onkeypress="return soloAlfaNumerico(event); 
             if (event.which == 13) { soloDescripcionBuscar(event,this.value); 
	     filtrarProductos(); } else { return soloDescripcionBuscar(event,this.value); } " tooltiptext=" Para listar todos los productos [ todos ] " />

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
    
    <button id="btnbuscar" label="Buscar" class="btn"
            image="<?php echo $_BasePath; ?>img/gpos_buscar.png" 
            oncommand='filtrarProductos()' />
    
    <menu id="listaPaginas" style="color: #000" collapsed="true" label="Pag. 1 - 10">
      <menupopup id="comboPaginas">
	<menuitem type="checkbox" label="1 - 10" oncommand="Paginar(0,'1-10')" checked="true"  />
      </menupopup>
    </menu>
    <menu label="Bloques"   style="color: #000">
      <menupopup>
	<menuitem type="checkbox" label="Ventas Personales" checked="true" oncommand = "ocultar('ventaDirecta');"/>
	<menuitem type="checkbox" label="Ventas Corporativas"  oncommand = "ocultar('ventaCorporativa');"/>
	<menuitem type="checkbox" label="Almacén" checked="true"
                  oncommand = "ocultar('detalleProductos');"/>
      </menupopup>
    </menu>
    <checkbox  id="buscar-servidor" label="Stock" checked="true" name="buscar-en-internet"/>
    <!-- spacer flex="1"/ -->
    <textbox id="numLista"  value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idfamilia" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="listalocal" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idmarca" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="iniciopagina" value="0" style="visibility:hidden; width:0px"/>
      <textbox id="idlistarPV" value="0" style="visibility:hidden; width:0px"/>
    </hbox>
    <hbox flex="1" style="overflow: auto" class="box"> 
      <hbox id="xboxprecios" pack="center" >
	<hbox> 
	  <groupbox >
	    <caption class="box" label="Productos" />
	    <grid >
	      <columns>
		<column/>
		<column flex="1"/>
	      </columns>
	      <rows id="productos">
		<row  class="box">
		  <caption label="#" style="font-style:italic;"/>
		  <caption label="Producto"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox id="detalleProductos" hidden="false" >
	    <caption class="box" label="Almacén" />
	    <grid >
	      <columns>
		<column/>
		<column/>
		<!--column/-->
	      </columns>
	      <rows id="detalle_productos">
		<row  class="box">
		  <caption label ="Stock"/>
		  <!--caption label ="Stock Min"/-->
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox >
	    <caption  class="box" label="Kardex" />
	    <grid >
	      <columns>
		<column/>
		<column/>
	      </columns>
	      <rows id="costo_productos">
		<row  class="box">
		  <caption label="Costo"/>
		  <caption label="COP"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>
	<hbox> 
	  <groupbox id="ventaDirecta" >
	    <caption class="box" label="Venta Personal"/>
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="detalle_directo">
		<row class="box">
		  <caption label="MUP"/>
		  <caption label="IGV"/>
		  <caption label="PVP"/>
		  <caption label="PVP/D"/>
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>


	<hbox> 
	  <groupbox id="ventaCorporativa" hidden="true" >
	    <caption class="box" label="Venta Corporativa"/>
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="detalle_corporativo">
		<row class="box">
		  <caption label="MUC"/>
		  <caption label="IGV"/>
		  <caption label="PVC"/>
		  <caption label="PVC/D"/>
		  <!--label value="."/-->
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>

        	<hbox> 
	  <groupbox >
	    <caption  class="box" label="Venta/Mayor" />
	    <grid >
	      <columns>
		<column/>
		<column/>
		<column/>
	      </columns>
	      <rows id="precio_empaque">
		<row  class="box">
		  <caption label=""/>
		  <caption label="PV/Empaq."/>
		  <caption label="PV/Docena" collapsed="<?php echo $wesl ?>"/>
		</row>
	      </rows>
	    </grid>
	  </groupbox>  
	</hbox>

      </hbox>
    </hbox>

    <hbox class="box" style="padding-top:1em">
      <vbox class="box" flex="1">
	<caption class="box" label="<?php echo _("Resumen Precios") ?>" />
	<hbox  class="resumen" pack="center" align="left">
	  <description value="Productos:"/>	  
	  <caption id="textoproductos" label="" />
	  <description value="Costo Promedio:"/>	  
	  <caption id="totalCPP" label="" />
	  <description value="Margen utilidad:"/>	  
	  <caption id="totalMU" label="" />
	  <description value="Impuesto:"/>	  
	  <caption id="totalIGV" label="" />
	  <description value="Precio Venta:"/>	  
	  <caption id="totalPVP" label="" />
	  <spacer flex="1"/>
	</hbox>
      </vbox>


      <button class="btn" image="<?php echo $_BasePath; ?>img/gpos_listaclientes.png" 
              id="listarPV_MD" label="  Listar Nuevos Precios " 
	      oncommand="listarPVMD()"/>
      <button class="popup" id="actualizarLPV" image="<?php echo $_BasePath; ?>img/gpos_almacen.png" 
              type="menu" label="  Aplicar " collapsed="true" <?php gulAdmite("Ventas") ?> >
	<menupopup>
	  <menuitem label="Local Actual" oncommand="actualizarNuevosPV()"></menuitem>
	  <?php if(getSesionDato("esAlmacenCentral")){?>
	  <menuitem label="Todos los Locales " oncommand="actualizarAllNuevosPV()"></menuitem>
	  <?php } ?>
	</menupopup>
      </button>

      <button  class="btn" image="<?php echo $_BasePath; ?>img/gpos_cancelar.png" id="eliminarLPV" 
               label=" Descartar " oncommand="eliminarNuevosPV()" collapsed="true"
               <?php gulAdmite("Ventas") ?>/>   
      <button  class="btn" label="Limpiar Búsqueda"  oncommand="limpiarListadoProductos()"/>   
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
	 echo "iniComboLocalesPrecios('".$locales."');";

     echo "iniBusqueda('".$codigo."','".$descripcion."','".$idfamilia."','".$idmarca."');";

     ?>

    </script>
    <?php
    EndXul();
    ?>
