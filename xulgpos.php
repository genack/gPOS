<?php

include("tool.php");


SimpleAutentificacionAutomatica("visual-xul","xulentrar.php");

$NombreEmpresa         = $_SESSION["GlobalNombreNegocio"];  
$NombreEmpresa         = ($NombreEmpresa =='gPOS')?'': $NombreEmpresa;
$NombreUsuarioDefecto  = $_SESSION["NombreUsuario"];
$NombreTiendaDefecto   = getNombreComercialLocal(getSesionDato("IdTienda"));
$esCarritoAlmacen      = getSesionDato("ModoCarritoAlmacen");
$esAgrupar             = ($esCarritoAlmacen == 'g')? 'true':'false';
$esTraslado            = ($esCarritoAlmacen == 't')? 'true':'false';
$lanzarTPV             = ( isset($_GET["t"]) == 'on');
//TODO: hacer esto XUL seguro
StartXul(_("gPOS ".$NombreEmpresa.' // Admin'));

if (isUsuarioAdministradorWeb()){

?>
<command id="verTemplates" 
  oncommand="solapa('modtemplates.php?modo=lista')"
  label="Templates"/>  
<command id="altaTemplate" 
  oncommand="solapa('modtemplates.php?modo=alta')"
  label="Alta template"/>  
<command id="editarJS" 
  oncommand="popup('xuleditor.php?id=24')"
  label="Editar JS"/>  
<command id="editarCSS" 
  oncommand="popup('xuleditor.php?id=26')"
  label="Editar CSS"/>  
<command id="editarxulCSS" 
  oncommand="popup('xuleditor.php?id=52')"
  label="Editar xul CSS"/>      
<command id="verLog" 
  oncommand="solapa('modulos/logactivo/logactivo.php?sesion=no&amp;num='+prompt('Lineas de log:',10))"
  label="Ver Log"/> 
<command id="verSesion" 
  oncommand="solapa('logactivo.php?num=1')"
  label="Ver sesion"/>   
<?php
}
?>

<?php 
  $btca = ( getSesionDato("GlobalGiroNegocio") == "BTCA" )?'':'collapsed="true"';
?>
<command id="verCarrito" oncommand="popup('vercarrito.php?modo=check','dependent=yes,width=600,height=320,screenX=200,screenY=300,titlebar=yes')"
  <?php gulAdmite("Compras") ?> label="<?php echo _("Ver carrito") ?>"/>  

<command id="altaProveedor" oncommand="proveedor_Alta()" <?php gulAdmite("Proveedores") ?> 
  label="<?php echo _(" Alta proveedor") ?>" />  

<command id="altaLaboratorio" oncommand="laboratorio_Alta()"  
  label="<?php echo _(" Alta laboratorio") ?>"  <?php echo $btca?>/>  

<command id="altaClienteMain" oncommand="solapa('modclientes.php?modo=alta','<?php echo _("Clientes: Alta") ?>')" 
  label="<?php echo _(" Alta cliente") ?>"  <?php gulAdmite("Clientes") ?>/>
  
<command id="altaClienteParticularMain"  oncommand="solapa('modclientes.php?modo=altaparticular','<?php echo _("Clientes: Alta") ?>')" 
  label="<?php echo _("Alta cliente particular") ?>"  <?php gulAdmite("Clientes") ?>/>  

<command id="altaUsuario"   oncommand="solapa('modusers.php?modo=alta','<?php echo _("Usuarios: Alta") ?>')"   
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta usuario") ?>"/>

  
<command id="verPerfiles"   oncommand="solapa('modperfiles.php?modo=lista','<?php echo _("Perfiles") ?>','varios')"
    <?php gulAdmite("Administracion") ?>  label="<?php echo _("Perfiles") ?>"/>
  
<command id="altaFamilia"  oncommand="solapa('modfamilias.php?modo=alta','<?php echo _("Familia: Alta") ?>')" 
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta familia") ?>"/>
  
<command id="cmdLogout"  oncommand="if ( tpvWindow ) if ( tpvWindow.close ) tpvWindow.close(); document.location.href='logout.php'"  label="<?php echo _(" Salir") ?>"/>

<command id="procesarCompra" oncommand="solapa('modcompras.php?modo=continuarCompra','<?php echo _("Compras") ?>')"
     <?php gulAdmite("Compras") ?> label="<?php echo _("Continuar compra") ?>"/>
  
<command id="seleccionRapida"   oncommand="popup('modulos/almacen/selalmacen.php?modo=empieza','dependent=yes,width=210,height=530,screenX=100,screenY=100,titlebar=yes')" 
    <?php gulAdmite("VerStocks") ?> label="<?php echo _("Captura CB") ?>"/>


<command id="nuevaCompra"  oncommand="solapa('modcompras.php?modo=noselecion')','<?php echo _("Compras") ?>')" 
  <?php gulAdmite("Compras") ?>  label="<?php echo _("Cancelar compra") ?>"/>
  
<command id="altaProducto" oncommand="solapa('modproductos.php?modo=alta','<?php echo _("Productos: Alta") ?>')" 
  <?php gulAdmite("Productos") ?>  label="<?php echo _("Alta producto") ?>"/>

<command id="altaTienda" oncommand="solapa('modlocal.php?modo=alta','<?php echo _("Tiendas: Alta") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta tienda") ?>"/>

<command id="buscaCB" oncommand="solapa('modproductos.php?modo=buscaporcb'+'&amp;'+'CodigoBarras='+prompt('CB',''),'<?php echo _("Productos") ?>')" 
   <?php gulAdmite("Productos") ?> label="Busca CB"/>

 
<!-- ====== MENU ALMACEN - ITEM ====== -->
<command id="verAlmacen"   oncommand="solapa('modulos/almacen/xulalmacen.php?modo=entra','<?php echo _("Almacén - Stock") ?>','almacen')" accesskey="S"
  <?php gulAdmite("VerStocks") ?> label="<?php echo _("Stock") ?>"/>

<command id="verKardex"   oncommand="solapa('modulos/kardex/selkardex.php?modo=verKardex','<?php echo _("Almacén - Kardex") ?>','framelist')" accesskey="K"
  <?php gulAdmite("Kardex") ?> label="<?php echo _("Kardex") ?>"/>

<command id="verInventario"   oncommand="solapa('modulos/inventario/modinventario.php?modo=verInventario','<?php echo _("Almacén - Inventario") ?>','framelist')" accesskey="I"
  <?php gulAdmite("VerAjustes") ?> label="<?php echo _("Inventario") ?>"/>


<!-- ====== MENU ALMACEN - ITEM  ====== -->

<!-- ====== MENU COMPRAS - ITEM  ====== -->
<command id="verPresupuesto" oncommand="solapa('modulos/compras/xulcompras.php?modo=entra','<?php echo _("Compras - Presupuestos ") ?>','compras');" accesskey="r"
  <?php gulAdmite("Presupuestos") ?> label="<?php echo _("Presupuestos") ?>"/>

<command id="verProveedores" oncommand="solapa('modproveedores.php?modo=lista','<?php echo _("Proveedores - Gestión de Proveedores") ?>','proveedores')" accesskey="v"
  <?php gulAdmite("Proveedores") ?>  label="<?php echo _("Proveedores") ?>"/>

<command id="verProductos"  oncommand="solapa('modulos/productos/xulproductos.php?modo=lista','<?php echo _("Productos - Gestión de Productos") ?>','productos')" accesskey="d"
  <?php gulAdmite("Productos") ?>    label="<?php echo _("Productos") ?>"/>

  <command id="verComprasBorrador"  oncommand="solapa('modulos/comprobantecompra/modcomprasborrador.php?modo=listarTodoCompra','<?php echo _("Compras - Comprobantes") ?>','framelist')" accesskey="o"
  <?php gulAdmite("ComprobantesCompra") ?>    label="<?php echo _("Comprobantes") ?>"/>


<command id="verPedidoAlta"  oncommand="solapa('#','<?php echo _("Compras - Pedido Nuevo") ?>','framelist')"
  <?php gulAdmite("Productos") ?>    label="<?php echo _("Nuevo") ?>"/>

<command id="verPedidos"  oncommand="solapa('#','<?php echo _("Compras - Pedidos") ?>','framelist')"
  <?php gulAdmite("Productos") ?>    label="<?php echo _("Pedidos") ?>"/>


<!--  ====== MENU COMPRAS - ITEM  ======  -->


<!--  ====== MENU VENTAS - ITEM   ======  -->
<command id="lanzarTPV" oncommand="lanzarTPV('rd')" <?php gulAdmite("TPV") ?>  label="<?php echo _("TPV B2C") ?>"/> 

<command id="VerComprobantesVentas" oncommand="solapa('modulos/comprobanteventa/modventas.php?modo=mostrarComprobantes','<?php echo _("Ventas - Comprobantes") ?>','framelist')" 
   <?php gulAdmite("ComprobantesVenta") ?> label="<?php echo _("Comprobantes") ?>"/>

<command id="VerPromociones" oncommand="solapa('modulos/promociones/modpromociones.php?modo=mostrarPromociones','<?php echo _("Ventas - Promociones") ?>','framelist')" 
   <?php gulAdmite("Promociones") ?> label="<?php echo _("Promociones") ?>"/>

<command id="verClientes"  oncommand="solapa('modclientes.php?modo=lista','<?php echo _("Clientes") ?>','clientes')" 
  <?php gulAdmite("Clientes") ?>  label="<?php echo _("Clientes") ?>"/>

<!--  ====== MENU VENTAS - ITEM   ====== -->


<!--  ====== MENU FINAZAS - ITEM   ====== -->

<command id="verEstablecerPrecioPedido" oncommand="solapa('modulos/recepcionpedido/modalmacenborrador.php?modo=recibirProductosAlmacen','<?php echo _("Almacén - Recibir Pedidos ") ?>','framelist')" accesskey="R"
   <?php gulAdmite("VerStocks") ?> label="<?php echo _("Recibir Pedidos") ?>"/>

<command id="verEstablecerPrecioAlmacen" oncommand="solapa('modulos/precios/selprecios.php?modo=mostrarProductosPrecios','<?php echo _("Ventas - Precios") ?>','framelist')" 
   <?php gulAdmite("Precios") ?> label="<?php echo _("Precios") ?>"/>

<!-- ====== MENU FINAZAS - ITEM  ====== -->

<!-- ====== MENU REPORTES  ====== -->  

<command id="verListados"   oncommand="solapa('modulos/generadorlistados/formlistados.php?area=admin','<?php echo _("Listados") ?>','framelist')"
  <?php gulAdmite("Informes") ?>  label="<?php echo _("Listados") ?>"/>  

<!-- ====== MENU REPORTES  ====== -->  


<command id="verTiendas" oncommand="solapa('modlocal.php?modo=lista','<?php echo _("Locales") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Locales") ?>"/>

<command id="verSubsidiarios" oncommand="solapa('modsubsidiarios.php?modo=lista','<?php echo _("Outsourcing") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Outsourcing") ?>"/>
  
<command id="verClientes"  oncommand="solapa('modclientes.php?modo=lista','<?php echo _("Clientes") ?>','clientes')" 
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Clientes") ?>"/>
  
<command id="verUsuarios"   oncommand="solapa('modusers.php?modo=lista','<?php echo _("Usuarios") ?>','varios')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Usuarios") ?>"/>  
  
<command id="verFamilias"   oncommand="solapa('modfamilias.php?modo=lista','<?php echo _("Familias") ?>','varios')"
  <?php gulAdmite("Administracion") ?>   label="<?php echo _("Familias") ?>"/>  

<command id="verConfiguracion"   oncommand="solapa('xulconfiguracion.php?modo=inicio','<?php echo _("Configuración") ?>','configuracion')"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Configuración") ?>"/>  

<command id="verCarritoAlmacen"   oncommand="almacen_MuestraCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Ver Carrito") ?>"/>  

<command id="verCarritoCancelar"   oncommand="ifConfirmExec('gPOS: Carrito Almacén\n\n ¿Esta seguro que quiere cancelar el carrito?','almacen_CancelarCarrito()')"  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Vaciar Carrito") ?>"/>     
  
<command id="verCarritoEnOferta"   oncommand="almacen_EnOfertaCarrito(0)" <?php gulAdmite("Almacen") ?>  label="<?php echo _(" En oferta") ?>"/>     

<command id="verificarSeriesAlma" oncommand="almacen_validarSerieProducto()" <?php gulAdmite("Almacen") ?>  label="<?php echo _("Verificar Números Series") ?>"/>     

<command id="verCarritoObsoleto"   oncommand="almacen_EsObsoletoCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Obsoleto") ?>"/>     

<command id="verCarritoNoObsoleto"   oncommand="almacen_NoEsObsoletoCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" No obsoleto") ?>"/>     

<command id="verCarritoEliminarProductos"   oncommand="almacen_EliminarProductos()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _("Eliminar Productos") ?>"/>     

<command id="verCarritoSinOferta"   oncommand="almacen_SinOfertaCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Sin Oferta") ?>"/>    
  
<command id="verCarritoSinVenta"  oncommand="almacen_nosondisponiblesCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Reservado") ?>"/>    
   
<command id="verCarritoEnVenta"  oncommand="almacen_disponiblesCarrito()"
  <?php gulAdmite("Almacen") ?>  label="<?php echo _(" Disponible") ?>"/>      

<command id="listaProveedores" oncommand="proveedor_Ver()"  
 <?php gulAdmite("Proveedores") ?>  label="<?php echo _("Lista proveedores") ?>"/>   

<command id="listaLaboratorios" oncommand="laboratorio_Ver()"  
  label="<?php echo _("Lista laboratorios") ?>"  <?php echo $btca?>  />   

<command id="listaClientes"  oncommand="clientes_Ver()"
  <?php gulAdmite("Administracion") ?>  label="<?php echo _("Lista clientes") ?>"/>   
<command id="altaCliente"  oncommand="clientes_Alta()" 
 <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta clientes empresa") ?>"/>

<command id="altaClienteParticular"  oncommand="clientes_AltaParticular()" 
 <?php gulAdmite("Administracion") ?>  label="<?php echo _("Alta cliente particular") ?>"/>


<command id="buzonSugerencia"  oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=feature','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia") ?>"/>
<command id="buzonReportefallo" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=bug','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer aviso de fallo") ?>"/> 

<command id="buzonReporte" oncommand="solapa('modulos/mensajeria/reporte.php','<?php echo _("Buzón") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia de mantenimiento") ?>"/> 

<command id="buzonNotaNormal" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=notanormal','<?php echo _("Nota normal") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Enviar nota normal") ?>"/> 

<command id="buzonNotaImportante" oncommand="solapa('modulos/mensajeria/modbuzon.php?modo=notaimportante','<?php echo _("Nota importante") ?>','buzon')" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Enviar nota importante") ?>"/> 

<!-- command id="lanzarTPV" oncommand="lanzarTPV(rd)" <?php gulAdmite("TPV") ?>  label="<?php echo _("TPV") ?>"/ --> 
   
<groupbox flex="1" class="frameExtraXX">
  <caption label="<?php echo $NombreEmpresa?>"/>

<vbox id="gpostoolbox" >
<hbox flex="1">
		<!-- button image="img/gpos_cajavacia.png" command="verAlmacen" <?php gulAdmite("VerStocks") ?> accesskey="a"/ -->
		<!-- button image="img/gpos_productos.png" command="verProductos" <?php gulAdmite("Productos") ?> accesskey="p"/ -->
		<!-- button image="img/gpos_compras.png"  command="verCompras" <?php gulAdmite("Compras") ?> accesskey="c"/ -->
		

		<button label="<?php echo _(" Compras") ?>" image="img/gpos_compras.png" type="menu" accesskey="C" id="btnCompras">	        	       
		  <menupopup id="idconfig">

		    <?php
		       $menuConfiguracion = array(	
		       _("Comprobantes") =>  "verPresupuesto",	
		       _("Facturar ") =>  "verComprasBorrador"		
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>
		    <menuseparator />
		    <?php
			$menuConfiguracion = array(	
		      _("Productos") =>  "verProductos"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

 		    <menuseparator />
		    <?php
 		
 		       $menuConfiguracion = array(		
        	       _("Proveedores") =>  "verProveedores"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    
		    ?>
		  </menupopup>
		</button>

		<button label="<?php echo _(" Almacén") ?>" image="img/gpos_almacen.png" type="menu"  accesskey="A">	        	       
		  <menupopup id="idconfig">
		    <?php
 		
 		       $menuConfiguracion = array(
		       _("Precios") =>  "verEstablecerPrecioPedido"
 		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

 		    <menuseparator />
		    <?php
 		       $menuConfiguracion = array(
		       _("Almacen") =>  "verAlmacen"	
 		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

 		    <menuseparator />
		    <?php
 		       $menuConfiguracion = array(

		       _("Kardex") =>  "verKardex",	
		       _("Inventario") =>  "verInventario"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    
		    ?>

		  </menupopup>
		</button>
		
		<button label="<?php echo _(" Ventas") ?>" image="img/gpos_ventas.png" type="menu" accesskey="V" >	        	       
		  <menupopup id="idconfig">
		    <?php
 		       
 		       $menuConfiguracion = array(		
		    _("TPV VD") =>  "lanzarTPV",
		    _("ComprobantesVenta") =>  "VerComprobantesVentas"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

		    <menuseparator />

		    <?php
 		       $menuConfiguracion = array(		
		       _("Precios de Venta") =>  "verEstablecerPrecioAlmacen",
		       _("Promociones") => "VerPromociones"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

		    <menuseparator />
		    <?php
 		       $menuConfiguracion = array(		
		       _("Clientes") =>  "verClientes"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    ?>

		  </menupopup>
		</button>

		<button label="<?php echo _(" Reportes ") ?>" image="img/gpos_reportes.png" type="menu"  accesskey="R" >	        	       
		  <menupopup id="idconfig">
		    <?php
 		
 		       $menuConfiguracion = array(		
						  _("Listados") =>  "verListados"
		    );  
 		    echo xulMakeMenuOptionsCommands($menuConfiguracion);
		    
		    ?>
		  </menupopup>
		</button>

               <!-- ?php
 		$menuModulos = array( );  
 		echo xulMakeMenuCommands("+",$menuModulos);
               ? --> 

       <?php
	if (isUsuarioAdministradorWeb()) {	
 		
 		$menuWebmaster = array(
		_("Templates") =>  "verTemplates",
		_("Alta template") =>  "altaTemplate"
		/*_("Editar CSS") =>  "editarCSS",
		_("Editar xul CSS") =>  "editarxulCSS",	
		_("Editar JS") =>  "editarJS",
		_("Mostrar log") =>  "verLog",
		_("Mostrar sesión") =>  "verSesion"*/	
		);  
 		echo xulMakeMenuCommands("src",$menuWebmaster);
	}
       ?>  		
	          
	<spacer flex="1"/>       
    <button  label="<?php echo _(" Buzón") ?>"  type="menu" image="img/gpos_buzon.png" <?php gulAdmite("Administracion") ?>>	       	       
    <menupopup id="idconfig">
     <?php
 		
 		$menuConfiguracion = array(
		_("Enviar nota normal")	=> "buzonNotaNormal",
		_("Enviar nota importante")	=> "buzonNotaImportante",
		_("Informar sugerencia o bug") =>  "buzonReporte",
		);  
 		echo xulMakeMenuOptionsCommands($menuConfiguracion);
	
	 ?>
    </menupopup>
    </button>

           
    <button label="<?php echo _(" Config.") ?>" image="img/gpos_config.png" type="menu"  <?php gulAdmite("Administracion") ?> >	        	       
    <menupopup id="idconfig">
     <?php

		$menuConfiguracion = array(		
					   _("Familias") =>  "verFamilias",
					   _("Usuarios") =>  "verUsuarios",
					   _("Perfiles") =>  "verPerfiles",
					   _("Tiendas") =>  "verTiendas",
					   _("Subsidiarios") =>  "verSubsidiarios"
							);  
 	      echo xulMakeMenuOptionsCommands($menuConfiguracion);
	
	 ?>
    </menupopup>
    </button>
       
    <button image="img/gpos_salir.png" command="cmdLogout" accesskey="s"/>
</hbox>
</vbox>

<menubar id="status-area" class="AreaPagina" style="">
  <caption id="status" class="enAreaPagina" label=""  style="font-size: 12px;font-weight: bold;" flex="1"/>
  <caption id="statusLocal" class="enAreaPagina" label="<?php echo _("Local: ") . $NombreTiendaDefecto; ?>" style="font-size: 12px;font-weight: bold;"/>
<caption id="statusOperador" class="enAreaPagina" label="<?php echo _("Operador: ") . $NombreUsuarioDefecto; ?>" style="font-size: 12px;font-weight: bold;"/>


</menubar>

<!-- VISTA NUEVOS MODULOS  -->
<hbox id="WebLista" flex="1" class="frameExtra"  collapsed="true">
 <box flex="1" class="frameExtra"> 
   <html:iframe  id="weblist"  class="AreaListados" src="about:blank" flex="1"/>
 </box>
</hbox>
<!-- VISTA NUEVOS MODULOS -->


<!-- VISTA OLD -->
<hbox id="WebNormal" flex="1" class="frameExtra">


 <box flex="1" id="boxAreaForm" class="frameExtra" collapsed="true"> 
   <iframe  id="webform" name="webform" class="AreaForm" src="about:blank" flex="1"/>
 </box>

 <box flex="1" id="boxAreaListados" class="frameExtra"> 
   <iframe  id="web" name="web" class="AreaListados" src="about:blank" flex="1"/>
 </box>



 <deck id="DeckArea" style="width: 260px;">
   <vbox id='DeckNormal' class="frameExtraDeck"/>
   <!-- Ventana Almacen -->
   <vbox id='DeckAlmacen' class="frameExtra" >
     <spacer style="height:15px"/>
     <tabbox class="frameExtra" flex="1">		
       <box id="accionesweb" class="frameNormal">
	 <groupbox class="frameNormal" flex="1">
	   <caption label="<?php echo _("Acciones"); ?>" class="frameNormal"/>
	   <spacer style="height:4px"/>
	   <hbox equalsize="always">
	     <button image="img/gpos_vercompras.png" command="verCarritoAlmacen" flex="1"/>     
	     <button crop="end" image="img/gpos_vaciarcompras.png" command="verCarritoCancelar" flex="1"/>
	   </hbox>
	 </groupbox>
       </box>
       
       <tabs class="AreaPagina">
	 <tab id="buscarAlmacen" onclick="almacen_buscar(0)" label="<?php echo _(" Buscar") ?>"/>
	 <tab id="capturarAlmacen" onclick="almacen_buscar(0)" label="<?php echo _(" Capturar CB ") ?>"/>
	 <tab id="accionesCarrito" onclick="almacen_MuestraCarrito()" label="<?php echo _(" Acciones Carrito") ?>" />
       </tabs>
       <tabpanels flex="1" id="tabpanelAlmacen">
	 <tabpanel id="normaltab" flex='1'>		    	
	   <groupbox flex="1">
	     <caption label="<?php echo _("Opciones:"); ?>" collapse="true"/>
	     <spacer style="height:6px"/>
	     <grid> 
	       <columns> 
		 <column flex="1"/><column flex="1"/>
	       </columns>
	       <rows>
		 <row>					
		   <caption label="<?php echo _("Local"); ?>"/>    
		   <menulist  id="a_idlocal" oncommand="" <?php gulAdmite("Almacen") ?> >
		     <menupopup>
		       <menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
		       <?php echo genXulComboAlmacenes(false,"menuitem") ?>
		     </menupopup>
		   </menulist>						
     		 </row>
		 <row>
		   <caption label="<?php echo _("CB"); ?>"/>
		   <textbox id="a_CB" flex="1" onkeypress="if (event.which == 13) { almacen_buscar(0) }"/>
		 </row>				
		 <row>
		   <caption label="<?php echo _("Ref"); ?>"/>
		   <textbox id="a_Referencia" flex="1" onkeypress="if (event.which == 13) { almacen_buscar(0) }"/>
		 </row>						
		 <row>
		   <caption label="<?php echo _("Nombre"); ?>"/>
		   <textbox id="a_Nombre" flex="1" onkeypress="if (event.which == 13) { almacen_buscar(0) }" onfocus="select()"/>
		 </row>						
		 <row>
		   <box/>
		   <vbox>
		     <spacer style="height:6px"/>
		     <row>
		     <caption label="<?php echo _("Solo con:"); ?>"/>
		       <vbox>
			 <checkbox id="a_Stock" label="<?php echo _("Stock"); ?>" checked="true"/>
			 <checkbox id="a_Oferta" label="<?php echo _("Oferta"); ?>" checked="false"/>
			 <checkbox id="a_NS" label="<?php echo _("NS"); ?>" checked="false"/>
			 <checkbox id="a_Lote" label="<?php echo _("Lote"); ?>" checked="false"/>
			 <checkbox id="a_Obsoleto" label="<?php echo _("Obsoletos"); ?>" checked="false"/>
			 <checkbox id="a_Reservado" label="<?php echo _("Reservados"); ?>" checked="false"/>

		       </vbox>
		     </row>
		   </vbox>
		 </row>						
	       </rows>
	     </grid>					
	     <button image="img/gpos_buscar.png" label='<?php echo _("Buscar") ?>' oncommand="almacen_buscar(0)"/>
	   </groupbox>
	 </tabpanel>
	 <tabpanel id="capturarAlmacen" flex='1'>
	   <groupbox flex="1">
	     <caption label="<?php echo _("Captura CB"); ?>" collapse="true"/>	
	     <menulist  id="a_idlocal_captura" oncommand="almacen_Guard_BotonCapturar()" <?php gulAdmite("Almacen") ?>>					
	       <menupopup>
		 <menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
		 <?php echo genXulComboAlmacenes(false,"menuitem") ?>
	       </menupopup>
	     </menulist>					
	     <textbox  id="a_CapturaCB" multiline="true" flex="1"/>																
	     <button id="botonCapturarAlmacen" disabled="true" image="img/gpos_compras.png"  label="<?php echo _("Añadir"); ?>" oncommand="Almacen_selrapidaCompra()"/>	        	       
	   </groupbox>
	 </tabpanel>
	 <tabpanel id="avanzadatabAlmacen" flex='1'>
	   <groupbox flex="1">
	     <caption label="<?php echo _("Opciones Selección: "); ?>" collapse="true"/>
			
	     <spacer style="height:1em"/>
	     <vbox pack="center" align="center" >
	       <radiogroup flex="1" orient="horizontal" style="font-size: 110%;font-weight: bold;" > 
		 <radio id="Atributos" label="Agrupar" selected="<?php echo $esAgrupar;?>" 
		        oncommand="selCarritoAlmacen('g')" <?php gulAdmite("Almacen") ?> />
		 <radio id="Traslado" label="Trasladar" selected="<?php echo $esTraslado;?>" 
		        oncommand="selCarritoAlmacen('t')" <?php gulAdmite("Almacen") ?> />
	       </radiogroup>
	     </vbox>

	     <vbox id="carritoAlmacenTraslado" collapsed="<?php echo $esAgrupar;?>">
	       <spacer style="height:.7em"/>

	       <caption label="<?php echo _("Motivo Albaran:"); ?>" />
	       <spacer style="height: .5em"/>
	       <menulist class="media" id="MotivoTraslado"  style="font-size: 130%;" 
			 oncommand="almacen_MotivoTraslado()">
		 <menupopup class="media" id="elementosTallas" >
		   <?php echo genXulComboMotivoAlbaran(5,"menuitem","Almacen");?>  	
		 </menupopup>
	       </menulist>
	       <spacer style="height: 1.3em"/>

	       <button id="cmbtraslado" image="img/gpos_almacen.png" type="menu" 
		       label="<?php echo _(" Elije Local"); ?>" 
	               oncommand="almacen_Traslado()"> 
                       <menupopup>
		       <?php echo genXulComboAlmacenes(false,"menuitem","almacen_setLocalTraslado"); ?>
		       </menupopup>
	       </button>

	       <button id="cmbtrasladoprovedor" image="img/gpos_proveedores.png" type="menu" 
		       label="<?php echo _(" Elije Proveedor"); ?>" 
	               oncommand="almacen_Traslado()" collapsed="true"> 
		       <menupopup>
		       <?php echo genXulComboProveedores(false,"menuitem","almacen_setLocalTraslado");?>
		     </menupopup>
	       </button>
	       <button id="btntrasladolocal" image="img/gpos_trasladar.png" 
		       label="<?php echo _(" Finalizar Carrito "); ?>" 
	               onclick="almacen_Traslado()" collapsed="true"> 
	       </button>
	       
	     </vbox>
	     <spacer style="height: 1em"/>
	     <vbox id="carritoAlmacenAgrupar"  collapsed="<?php echo $esTraslado;?>">
	       <grid>
		 <columns><column flex="1"/><column flex="1"/></columns>
		 <rows>
		   <row>
		     <button image="img/gpos_enoferta.png"  command='verCarritoEnOferta' flex="1"/>
		     <button image="img/gpos_sinoferta.png" command='verCarritoSinOferta' flex="1"/>
		   </row>
		   <row>					
		     <button image="img/gpos_enventa.png" command='verCarritoEnVenta' flex="1"/>
		     <button image="img/gpos_reservado.png" command='verCarritoSinVenta' flex="1"/>
		   </row>
		   <row>
		     <button image="img/gpos_noobsoleto.png" command='verCarritoNoObsoleto' flex="1"/>
		     <button image="img/gpos_obsoleto.png"  command='verCarritoObsoleto' flex="1"/>						
		   </row>										
		 </rows>
	       </grid>
	     </vbox>	     
	   </groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>
   </vbox>
   <!-- Ventana Almacen -->


   <!-- Ventana Compras -->
   <vbox class="frameExtra">
     <spacer style="height:15px"/>
     <box id="accionesweb" class="frameNormal">
       <groupbox class="frameNormal" flex="1">
	 <caption label="<?php echo _("Acciones:"); ?>" class="frameNormal"/>
	 <spacer style="height:4px"/>
	 <hbox  equalsize="always">
	   <button id="verBtnCarrito" image="img/gpos_vercompras.png" label="<?php echo _(" Ver Carrito") ?>" 
	   oncommand="Compras_verCarrito();" flex="1" collapsed="false"/>     

	   <button crop="end" image="img/gpos_vaciarcompras.png" label="<?php echo _(" Vaciar Carrito") ?>" oncommand="ifConfirmExec('gPOS:\n\n¿Esta seguro que quiere vaciar el carrito?','Compras_cancelarCarrito()')" flex="1"/>
	 </hbox>
           <spacer style="height:4px"/>
	 <button  image="img/gpos_fincompras.png" id='bcapturar' flex="1" type="menu" label="<?php echo _(" Finalizar Compra"); ?>" oncommand="Compras_compraEfectuar()">
	   <menupopup id="idlocal3">
	     <?php echo genXulComboAlmacenes(false,"menuitem","Compras_setLocal"); ?>
	  </menupopup>
	</button>	 	      
       </groupbox>
     </box>
     <box>
       <groupbox class="frameNormal" flex="1">
	 <caption label="<?php echo _("Recursos:"); ?>" class="frameNormal"/>
	 <hbox  equalsize="always">
	   <button image="img/gpos_productos.png" label="<?php echo _(' Productos'); ?>" 
	   oncommand="solapa('modulos/productos/xulproductos.php?modo=lista','<?php echo _("Productos") ?>','productos')" flex="1" <?php gulAdmite("Productos") ?>/>     

	   <button  image="img/gpos_altarapida.png" flex="1" label="<?php echo _(" Alta Rápida..."); ?>" oncommand="Compras_altaRapida()" <?php gulAdmite("Productos") ?> />
	 </hbox>
	 <spacer style="height:4px"/>
	 <!-- hbox  equalsize="always" -->
	 <spacer style="height:4px"/>

	 <!-- /hbox -->
       </groupbox>
     </box>
     <tabbox class="frameExtra" flex="1">
       <tabs class="AreaPagina">
	 <tab label="<?php echo _("Buscar") ?>"/>
	 <tab label="<?php echo _("Comprar") ?>"/>		    
	 <tab label="<?php echo _("Capturar CB") ?>"/>
       </tabs>
       
       <tabpanels flex="1">
	 <tabpanel id="normaltab" flex='1'>		    	
	   <groupbox flex="1">
	     <caption label="<?php echo _("Buscar"); ?>" collapse="true"/>
	     <grid> 
	       <columns> 
		 <column flex="1"/><column flex="1"/>
	       </columns>
	       <rows>
		 <row>
		   <caption label="<?php echo _("CB"); ?>"/>
		   <textbox id="c_CB" flex="1" onfocus="select()"
			    onkeypress="if (event.which == 13) { Compras_buscar() }"/>
		 </row>				
		 <row>
		   <caption label="<?php echo _("Ref"); ?>"/>
		   <textbox id="c_Referencia" flex="1" onfocus="select()"
			    onkeypress="if (event.which == 13) { Compras_buscar() }"/>
		 </row>						
		 <row>
		   <caption label="<?php echo _("Nombre"); ?>"/>
		   <textbox id="c_Nombre" flex="1" onfocus="select()" 
			    onkeypress="if (event.which == 13) { Compras_buscar() }"/>
		 </row>			
		 <row>
		   <caption label="<?php echo _("Solo"); ?>"/>
		 </row>

		 <row><box/><checkbox id="c_StockMinimo" label="<?php echo _("Stock Minimo"); ?>" checked="false"/></row>
		 <row><box/><checkbox id="c_PorProveedor" label="<?php echo _("Por Proveedor"); ?>" checked="false"/></row>
		 <row><box/><checkbox id="c_Obsoletos" label="<?php echo _("Obsoletos"); ?>" checked="false"/></row>
	       </rows>
	     </grid>
	     <button id='btnbuscar' image='img/gpos_buscar.png' label='Buscar' oncommand="Compras_buscar()"/>
	   </groupbox>
	 </tabpanel>
	 <tabpanel id="comprapanel" flex='1'>		    	
	   <groupbox flex="1">
	     <caption label="<?php echo _("Comprar"); ?>" collapse="true"/>
	     <grid> 
	       <columns> 
		 <column flex="1"/><column flex="1"/>
	       </columns>
	       <rows>
		 <row>
		   <caption label="<?php echo _("CB"); ?>"/>
		   <textbox id="c_CompraCB" flex="1" 
			    onkeypress="if (event.which == 13) { Compras_CBCompra() }"/>
		 </row>										
	       </rows>				  
	     </grid>
	     <button  image="img/gpos_compras.png" label='<?php echo _("Comprar") ?>' oncommand="Compras_CBCompra()"/>
	     <spacer style="height: 16px"/>					
	   </groupbox>
	 </tabpanel>		  
	 <tabpanel id="avanzadatabComprar" flex='1'>
	   <groupbox flex="1">
	     <caption label="<?php echo _("Captura CB"); ?>" collapse="true"/>
	     <textbox  id="c_CapturaCB" multiline="true" flex="1"/>
	     <button   image="img/gpos_compras.png"  label="<?php echo _("Comprar"); ?>" oncommand="Compras_selrapidaCompra()"/>	        	       
	   </groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>
   </vbox>
   <!-- Ventana Compras -->

   <!-- Ventana Productos -->
   <vbox class="frameExtra">
     <spacer style="height:15px"/>
     <box id="accionesweb" class="frameNormal">
       <groupbox class="frameNormal" flex="1">
	 <caption label="<?php echo _("Acciones:"); ?>" class="frameNormal"/>
	 <spacer style="height:4px"/>
	 <vbox>
	   <button  image="img/gpos_productos.png"  label="<?php echo _(" Nuevo producto"); ?>" oncommand="Productos_ModoAlta();"/>
           <spacer style="height:10px"/>
           <button  image="img/gpos_fichatecnica.png"  label="<?php echo _("  Ficha Técnica"); ?>" oncommand="solapa('modulos/productos/modproductoextra.php?modo=verProductoInformacion','<?php echo _("Productos - Ficha Técnica") ?>','framelist');" flex="1"/>
	 </vbox>
       </groupbox>
     </box>
     <box>
       <groupbox class="frameNormal" flex="1">
	 <caption label="<?php echo _("Recursos:"); ?>" class="frameNormal"/>
	 <hbox  equalsize="always">
	 <button  image="img/gpos_presupuesto.png"  label="<?php echo _(" Presupuestos"); ?>" 
	          oncommand="solapa('modulos/compras/xulcompras.php?modo=entra','<?php echo _("Compras - Presupuestos") ?>','compras');" <?php gulAdmite("Presupuestos") ?> flex="1"/>

	 </hbox>
       </groupbox>
     </box>

     <tabbox class="frameExtra" flex="1">
       <tabs class="AreaPagina">
	 <tab label="<?php echo _("Buscar") ?>"/>
	 <tab label="<?php echo _("Búsqueda avanzada") ?>" oncommand="Productos_loadAvanzado()"/>
       </tabs>
       <tabpanels flex="1">
	 <tabpanel id="normaltab" flex='1'>
	   <groupbox  flex="1">
	     <caption label="<?php echo _("Buscar") ?>" />
	     <grid>
	       <columns>
    		 <column flex="1" />
    		 <column flex="1" />
  	       </columns>
	       <rows>
    		 <row><caption label="<?php echo _("CB"); ?>"/><textbox id="p_CB" onkeypress="if (event.which == 13) { Productos_buscar() }"/></row>
    		 <row><caption label="<?php echo _("Ref"); ?>"/><textbox id="p_Referencia" onkeypress="if (event.which == 13) { Productos_buscar() }"/></row>
		 <row><caption label="<?php echo _("Nombre"); ?>"/><textbox id="p_Nombre" onkeypress="if (event.which == 13) { Productos_buscar() }" onfocus="select()"/></row>
		 <row><box/>
		 <checkbox id="p_Obsoletos" label="<?php echo _("Ver obsoletos"); ?>" checked="false"/></row>
	       </rows>
	     </grid>
	     <button  image="img/gpos_buscar.png" label='<?php echo _("Buscar") ?>' oncommand="Productos_buscar()"/>
	   </groupbox>
	 </tabpanel>
	 <tabpanel id="avanzadatabProductos" flex='1'>
	   <groupbox flex="1">
	     <!--caption label="<?php echo _("Búsqueda avanzada"); ?>"/ -->
	     <iframe id="subframe" src="" flex='1'/>
	   </groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>
   </vbox>
   <!-- Ventana Productos -->

   <!-- Ventana Proveedores -->
   <vbox class="frameNormal">
       <spacer style="height:2em"/>		
     <groupbox class="frameNormal" >
       <caption label="<?php echo _("Acciones:"); ?>" class="frameNormal"/>
       <button  image="img/gpos_proveedores.png"  command="altaProveedor"/>
       <spacer style="height:0.5em"/>
       <button  image="img/gpos_listaproveedores.png"  command="listaProveedores"/>
       <spacer style="height:0.5em"/>
       <button  image="img/gpos_labs.png"  command="altaLaboratorio"/>
       <spacer style="height:0.5em"/>
       <button  image="img/gpos_listaproveedores.png"  command="listaLaboratorios"/>
     </groupbox>
     
     <tabbox class="frameExtra" flex="1">
       <tabs class="AreaPagina">		    		    
       </tabs>
       <tabpanels flex="1">
	 <tabpanel id="atab" flex='1'>
	   <groupbox flex="1"></groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>
   </vbox>
   <!-- Ventana Proveedores -->

   <!-- Ventana Clientes -->
   <vbox flex="1" class="frameNormal">		
     <spacer style="height:1.5em"/>
     <groupbox class="frameNormal">
       <caption label="<?php echo _("Acciones:"); ?>" class="frameNormal"/>	     
       <spacer style="height:1em"/>
       <button  image="img/gpos_clienteempresa.png" command="altaCliente"/>
       <spacer style="height:1em"/>
       <button  image="img/gpos_clienteparticular.png" command="altaClienteParticular"/>
       <spacer style="height:1em"/>
       <button  image="img/gpos_listaclientes.png" command="listaClientes"/>
       <box flex="1"></box>	     
     </groupbox>	
     <tabbox class="frameExtra" flex="1">
       <tabs class="AreaPagina">		    		    
       </tabs>
       <tabpanels flex="1">
	 <tabpanel id="atab" flex='1'>
	   <groupbox flex="1"></groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>
   </vbox>
   <!-- Ventana Clientes -->	

   <!-- Ventana Servicio Tecnico -->
   <vbox flex="1" class="frameNormal">		
     <groupbox class="frameNormal">
       <caption label="<?php echo _("Acciones: "); ?>" class="frameNormal"/>	     
       <button  image="img/gpos_clienteparticular.png" command="buzonSugerencia"/>
       <button  image="img/gpos_clienteparticular.png" command="buzonReportefallo"/>
       <button  image="img/gpos_listaclientes.png" command="listaClientes"/>	  	        	              	     
       <box flex="1"></box>	     
     </groupbox>	
     
     <tabbox class="frameExtra" flex="1">
       <tabs class="AreaPagina">		    		    
       </tabs>
       <tabpanels flex="1">
	 <tabpanel id="atab" flex='1'>
	   <groupbox flex="1"></groupbox>
	 </tabpanel>
       </tabpanels>
     </tabbox>	     	
   </vbox>
   <!-- Ventana Servicio Tecnico -->	
   
 </deck>	
 <!-- Ventanas auxiliares -->
 
</hbox>

</groupbox>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/cadenas.js.php?ver=1/r<?php echo rand(0,99999999); ?>"/>
<script><![CDATA[



var id2nombreAlmacenes   = new Array();
var id2nombreProveedores = new Array();

<?php

	$prov = new proveedor;
	$arrayTodos = $prov->listaTodosConNombre();
		
	foreach($arrayTodos as $key=>$value){
		echo "id2nombreProveedores[$key] = '".addslashes($value). "';\n";
	}

	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "";	
	$call = "";
	foreach($arrayTodos as $key=>$value){
		echo "id2nombreAlmacenes[$key] = '".addslashes($value). "';\n";			
	}

        //Lanzar TPV 
        if( $lanzarTPV && Admite("TPV") ){ echo "lanzarTPV('rd');"; }
?>;	

var myBrowser = false;
var olddoc = false;

try {
 function $(cosa){
		return document.getElementById(cosa);
	}
} catch(e) {};


function getBrowser()  {
	if (document)
	  olddoc = document;
	else
	  document = olddoc;
	
    if (!myBrowser)
        myBrowser = document.getElementById("web");
                  
    xwebCollapsed(false,true);
    return myBrowser;
}

var myWebForm = false;
var myWebList = false;

function getWebForm(){
    myWebForm = document.getElementById("webform");
    if(myWebForm)
        return myWebForm
}

function getWebList(){
    myWebList = document.getElementById("weblist");
    if(myWebList)
        return myWebList
}

function xwebCollapsed(xval,clean=false){
    var yval = (xval)?false:true;
    var obox = document.getElementById("boxAreaListados");	
    var xbox = document.getElementById("boxAreaForm");	

    obox.setAttribute("collapsed",xval);  
    xbox.setAttribute("collapsed",yval);
    
    //Clean box Form
    var main = getWebForm();
    if(clean)
       main.setAttribute("src","about:blank");
}

function xwebcoreCollapsed(xval,clean=false){
    var yval = (xval)?false:true;
    var obox = document.getElementById("WebNormal");	
    var xbox = document.getElementById("WebLista");	

    obox.setAttribute("collapsed",xval);  
    xbox.setAttribute("collapsed",yval);
    
    //Clean box Form
    var main = getWebList();
    if(clean)
       main.setAttribute("src","about:blank");
}

function ifConfirmExec( mensaje, command){
  if (confirm(mensaje)){
    eval(command);
  }	
}


function OpenDeck(index){
  var deck = document.getElementById("DeckArea");  
  var main = getBrowser();
       
  deck.setAttribute("selectedIndex",index);
  main.setAttribute("src","about:blank");  
}

function CloseDeck(){ //No cierra realmente, solo oculta
	var main = getBrowser();
	var deck = document.getElementById("DeckArea");
	//main.setAttribute("collapsed","false"); 
	deck.setAttribute("selectedIndex",0);   
}


var extraVisible = 1;

function OcultaDeck(){
	var deck = document.getElementById("DeckArea");
	deck.setAttribute("collapsed","true");
	extraVisible = 0;	 	   
}

function MostrarDeck(){
	if (extraVisible)
		return;

	var deck = document.getElementById("DeckArea");
	deck.setAttribute("collapsed","false");	 	   
}

function solapa(url,area,deck){
	var main     = getBrowser();  
        var mainweb  = document.getElementById("WebNormal");
        var mainlist = document.getElementById("WebLista");
	var status   = document.getElementById("status");
        mainweb.setAttribute("collapsed","false");
        mainlist.setAttribute("collapsed","true");

   	status.setAttribute("label","Area " + area);
	switch(deck){
	  case "almacen":
	  	OpenDeck(1);
		document.getElementById("a_Nombre").focus();
		almacen_buscar(1);
		MostrarDeck();
	  	break;
	  case "compras":
	  	OpenDeck(2);
	  	document.getElementById("c_Nombre").focus();
		MostrarDeck();
		Compras_buscar();	  	
	  	break;
	  case "productos":
	  	OpenDeck(3);
	  	document.getElementById("p_Nombre").focus();
		MostrarDeck();
		Productos_buscar();	  	
	  	break;
	  case "proveedores":
	  	OpenDeck(4);
		MostrarDeck();	  	
	  	break;
	  case "clientes":
	  	OpenDeck(5);
		MostrarDeck();  	
	  	break;
	  case "listados":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
		break;	   
	 case "series":
		OcultaDeck();
		main.setAttribute("src", url);
	        break;
	 case "framelist":
                w_list = document.getElementById("weblist");
 
                // OCULTA VISTA GENERAL 
                mainweb.setAttribute("collapsed","true");

                // VISTA LISTAS CODEKA
                mainlist.setAttribute("collapsed","false");

                extraVisible = 0;	 	   

	        // CARGAR URL
		w_list.setAttribute("src", url);

                if(window.innerWidth){
                   var ancho = window.innerWidth - 12;
                   w_list.setAttribute("width",ancho);
                }

	        break;

	  case "buzon":	
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	break;	    	  	
	  case "configuracion":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	//alert("configuracion, deck oculto");
	  	break;
	  case "varios":
	  	OcultaDeck();
	  	main.setAttribute("src", url);
	  	break;
	  default:
	    avanzadoCargado = 0;//cambios en familias/etc se reflejan inmediatamente
		main.setAttribute("src", url);     		      	  
	    CloseDeck();
	    break;
	}     	     	    	

}
 
function popup(url,metodo){
	if (window)	   window.open(url,"aux",metodo);
	else if (document)   document.open(url,metodo);
   
}

/* ========== TPV ============ */

var tpvWindow;
var gposWindow;
function lanzarTPV(rt){
	
	<?php 
	if (Admite("TPV")){
	?>
	
	if (tpvWindow && tpvWindow.close) tpvWindow.close();

	var url    = 'tpvload.php?modo=tpv&t='+rt+'&espopup=on&r=' + Math.random();
	var metodo = 'fullscreen=yes,directories=No,toolbar=No,menubar=No,status=No,resizable=No,titlebar=No,location=No';
	tpvWindow  =  open(url,"gPOS // TPV ",metodo);	
	<?php  } ?>

}

function lanzarVentasGeneral(){
   	    var url="modulos/comprobanteventa/modventas.php?modo=mostrarComprobantes"
	    solapa(url,'<?php echo _("Ventas - Comprobantes") ?>','framelist')
}

/* ========== TPV ============ */

/* ========== BUZON ============ */

/*
<command id="buzonSugerencia"  oncommand="buzon_HacerSugerencia()" 
 <?php gulAdmite("Administracion","mensajeria") ?>  label="<?php echo _("Hacer sugerencia") ?>"/>
<command id="buzonReportefallo"  oncommand="buzon_Reporte()" 
*/

/* ========== BUZON ============ */

/* ========== ALMACEN ============ */



var subweb = document.getElementById("web");

var local = 0;
var localtraslado=0;
var localcaptura= 0;

function almacen_Guard_BotonCapturar() {
	localcaptura = document.getElementById("a_idlocal_captura").value;
	if (localcaptura){
		document.getElementById("botonCapturarAlmacen").setAttribute("disabled","false");
	}else{
			document.getElementById("botonCapturarAlmacen").setAttribute("disabled","true");
	}	
}


function Almacen_selrapidaCompra() {

	var cc  = document.getElementById("a_CapturaCB");		
		
	var url="modulos/almacen/selalmacen.php?modo=agnademudo_almacen";


	var xrequest = new XMLHttpRequest();
	var data = "IdLocal="+localcaptura+"&listacompra=" + escape(cc.value);       

	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	alert(xrequest.responseText);
	cc.value= "";
	cc.setAttribute("value","");
}


function almacen_setLocal(valor) { local = valor;}

function almacen_setLocalTraslado(valor) { localtraslado = valor; }


function almacen_CancelarCarrito(){
	var url = "modalmacenes.php?modo=borrarseleccion";
	subweb.setAttribute("src",url);
	hiddenbtntraslado(0);
	almacen_MuestraCarrito();
}

function hiddenbtntraslado(mbool,xcmd){

        var btncancelar = document.getElementById("verCarritoCancelar");
	var btntraslado = document.getElementById("cmbtraslado");
	var btncmd      = "almacen_cmdCancelarCarrito()";
        var cmdbool     = (mbool)? xcmd:btncmd;
	var txtbool     = (mbool)? ' Finalizar carrito':' Vaciar Carrito';
	var xbtntras    = (mbool)? true:false;

	btncancelar.setAttribute('label',txtbool);
	btncancelar.setAttribute('oncommand',cmdbool);
	btntraslado.setAttribute('collapsed',xbtntras);
}

function almacen_cmdCancelarCarrito(){
         ifConfirmExec('gPOS: Carrito Almacén \n\n ¿Esta seguro que quiere cancelar el carrito?',
	               'almacen_CancelarCarrito()');
}
 
function almacen_SinOfertaCarrito(xop){
	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=nosonoferta';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
	//hiddenbtntraslado();
}
 
function almacen_EnOfertaCarrito(xop){

        var modo   = (xop)? 'sonoferta':'versonoferta';
	var xmodo  = (xop)? 0:1;
	var main   = getWebForm();
	var aviso  = 'Cargando Carrito Almacén...';
	var url    = 'modalmacenes.php?modo='+modo;
	var lurl   = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;


	hiddenbtntraslado(xmodo,'almacen_EnOfertaCarrito(1)');

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);

	//var xurl   = "modulos/kardex/selkardex.php"+"?modo=setModoCarritoAlmacen"+"&xmodo=t";
        //xWebMensaje (xurl);
}

function almacen_disponiblesCarrito(){

	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=sondisponibles';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
}

function almacen_nosondisponiblesCarrito(){

	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=nosondisponibles';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
}

function almacen_EsObsoletoCarrito(){

	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=esobsoleto';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
}

function almacen_NoEsObsoletoCarrito(){

	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=noobsoleto';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
}

function almacen_MotivoTraslado(){

    var trasmotivo    = document.getElementById("MotivoTraslado");
    var traslocal     = document.getElementById("cmbtraslado");
    var trasproveedor = document.getElementById("cmbtrasladoprovedor");
    var trasbtnlocal  = document.getElementById("btntrasladolocal");
    
    var xtproveedor   = true;    
    var xtlocal       = true;
    var xtbtnlocal    = true;



    switch (trasmotivo.value) {

    case '5'://Traslado
    case '2'://Consignacion
        xtlocal     = false;
	break;

    case '6'://inmovilizacion
	xtbtnlocal  = false;
	almacen_setLocalTraslado(localcesion);
	break;

    case '4'://Devolucion
        xtproveedor = false;    
	break;
    }    

    //alert(trasmotivo.value+' '+xtlocal);

    traslocal.setAttribute("collapsed",xtlocal);
    trasproveedor.setAttribute("collapsed",xtproveedor);
    trasbtnlocal.setAttribute("collapsed",xtbtnlocal);
}

function almacen_Traslado() {

    if (localtraslado==0) return;

    var motivo  = document.getElementById("MotivoTraslado").value;
    var tmotivo = document.getElementById("MotivoTraslado").label;
    var Destino = '';

    switch (motivo) {
    case '5'://Traslado
    case '2'://Consignacion
    case '6'://inmovilizacion
        Destino = new String(id2nombreAlmacenes[localtraslado]);
        Destino = Destino.toUpperCase();
	break;

    case '4'://Devolucion
        Destino = new String( id2nombreProveedores[localtraslado] );
        Destino = Destino.toUpperCase();
	break;
    }    
    
    if (!confirm( po_moviendoa +" " +Destino + 
                  ".\n\nMotivo: "+tmotivo+".\n\n                     " +
		  po_confirmatraslado))  return;

    document.getElementById("accionesCarrito").setAttribute("selected",false);
    document.getElementById("buscarAlmacen").setAttribute("selected",true);
    document.getElementById("tabpanelAlmacen").setAttribute("selectedIndex",0);

    var url   = "modalmacenes.php?"+
                "modo=albaran"+
                "%IdLocalDestino="+localtraslado+
		"%tmotivo="+tmotivo+ 
		"%tdestino="+Destino+ 
		"%motivo="+motivo; 
    var main  = getWebForm();
    var aviso = 'Cargando Carrito Almacén...';
    var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;
    local     = 0;
    
    main.setAttribute("src",lurl);  
    xwebCollapsed(true);


}

function almacen_EliminarProductos(){

}

function almacen_selrapidaalmacen() {
    if (local==0)
    	return;

	var url = "modulos/almacen/selalmacen.php?modo=empieza&IdLocal=" +local;
	subweb.setAttribute("src",url);
	local = 0;
}

function almacen_MuestraCarrito() {

        var sacci = document.getElementById("accionesCarrito");
	var bacci = document.getElementById("buscarAlmacen");
        var tacci = document.getElementById("tabpanelAlmacen");
	sacci.setAttribute("selected",true);
	bacci.setAttribute("selected",false);
	tacci.setAttribute("selectedIndex",'2');

	var main  = getWebForm();
	var aviso = 'Cargando Carrito Almacén...';
	var url   = 'modalmacenes.php?modo=seleccion';
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormCartSerieMod&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);  
	xwebCollapsed(true);
}

function return_almacen(id)
{
    subweb.setAttribute('src','modalmacenes.php?Id='+id);
}

function ckActionCancel(id,xid)
{
    var p          = 0;
    var tipoAction = 'notrans';     
    var url        = 'modalmacenes.php?modo='+tipoAction+'&id='+id+'&u='+p;
    xWebMensaje (url);
}

 
function selCarritoAlmacen(xval){

  var straslado = document.getElementById("carritoAlmacenTraslado");
  var sagrupar  = document.getElementById("carritoAlmacenAgrupar");
  var vtraslado = (xval=='t')? false:true;
  var vagrupar  = (xval=='g')? false:true;

  straslado.setAttribute("collapsed",vtraslado);
  sagrupar.setAttribute("collapsed",vagrupar);  

  var url = "modulos/kardex/selkardex.php"+
            "?modo=setModoCarritoAlmacen"+
            "&xmodo="+xval;
  xWebMensaje (url);

  almacen_MuestraCarrito();
  hiddenbtntraslado(0,false);
  almacen_MotivoTraslado();
}


function ckActionGuardar(xSeries,xCantidad,xPedidoDet,cProducto,id)
{

    var ptrans  = '';
    var srt     = '';
    var srtns   = '';
    var nstrans = '';
    var lftrans = '';

    //Lote & Vence -> lote vence
    //Cantidad -> idpedidodet:cantidad~
    //Serie -> idpedidodet:serie1,serie2~

    for(var i = 0; i<xPedidoDet.length; i++)
    {

      cantrans    = xCantidad['cAlmacen_'+xPedidoDet[i]];
      preciotrans = xCantidad['cAlmacenPrecio_'+xPedidoDet[i]];
      lftrans     = xCantidad['cAlmacenLF_'+xPedidoDet[i]];
      lftrans     = ( lftrans )? ':'+lftrans:'';

      if(cantrans>0)
        {
          ptrans    = ptrans+srt+xPedidoDet[i]+':'+cantrans+':'+preciotrans+lftrans;
 	  srt       = '~'; 
	}

      if(xSeries && cantrans>0)
          { 
            nstrans   = nstrans+srtns+xPedidoDet[i]+':'+xSeries[xPedidoDet[i]];
 	    srtns     = '~'; 
	  }
    }

    if(ptrans == '')
       return alert("gpos:\n\n Elija una cantidad del Producto: \n\n  - "+cProducto);

    var tipoAction = 'trans';     
    var url        = 'modalmacenes.php?modo='+tipoAction+'&id='+id+'&series='+nstrans+'&u='+ptrans;

    xWebMensaje (url);
    return true;
}

function xWebMensaje(url)
{  
  xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange=xWebDummyFunction();
  xmlhttp.open("GET",url,true);
  xmlhttp.send(null);
}

function xWebDummyFunction() {};

function almacen_buscar(xclean)
{  
  subweb.setAttribute("src","about:blank");
  xwebCollapsed(false,true); 

  var extra = "&CodigoBarras=" + document.getElementById("a_CB").value;  
      extra = extra +  "&IdLocal=" + document.getElementById("a_idlocal").value;
      extra = extra +  "&Referencia=" + document.getElementById("a_Referencia").value;
      extra = extra +  "&Nombre=" + document.getElementById("a_Nombre").value;
  var solollenos = (document.getElementById("a_Stock").checked)?1:0;
  var soloNS     = (document.getElementById("a_NS").checked)?1:0;
  var sololote   = (document.getElementById("a_Lote").checked)?1:0;
  var solooferta = (document.getElementById("a_Oferta").checked)?1:0;
  var obsoletos  = (document.getElementById("a_Obsoleto").checked)?1:0;   
  var reservados = (document.getElementById("a_Reservado").checked)?1:0;   
  extra = extra +  "&soloConStock=" + solollenos ;
  extra = extra +  "&soloConNS=" + soloNS ;
  extra = extra +  "&soloConLote=" + sololote ;
  extra = extra +  "&soloConOferta=" + solooferta ;
  extra = extra +  "&mostrarObsoletos=" + obsoletos ;
  extra = extra +  "&mostrarReservados=" + reservados ;
  
  var url = "modalmacenes.php?modo=buscarproductos" + extra; 
  subweb.setAttribute("src", url);
  
  document.getElementById("a_Nombre").focus();
  
}




/* ========== ALMACEN ============ */
/* ========== COMPRAS ============ */

var c_local = 0;
var c_capturalocal = 0;

function Compras_setLocal(valor) { 
    c_local = valor;
}

function Compras_CapturasetLocal(valor) { c_capturalocal = valor;}

function Compras_cancelarCarrito() {
	var url = "vercarrito.php?modo=noseleccion";
	subweb.setAttribute("src",url);
}

function Compras_compraEfectuar() {

    var	url = "services.php?modo=verificadocCompra";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res = xrequest.responseText;
    if(res!='')
      {
        alert(xrequest.responseText);
        return Compras_verCarrito();
       }
    //Finaliza Compra	
    var url = "modcompras.php?modo=continuarCompra&IdLocal="+c_local;
    subweb.setAttribute("src",url);

    //WebFrom hide
    xwebCollapsed(false,true);
}


function Compras_verCarrito(modo=false) {
    var btncart = document.getElementById("loadBtnCarrito");

    if( btncart && !modo ) return;

    var main  = parent.getWebForm();
    var aviso = 'Cargando Carrito Compras...';

    main.setAttribute("src",'modulos/compras/progress.php?modo=aCarritoCompras&aviso='+aviso);
    xwebCollapsed(true);
}

function Compras_cambiaBtnCarrito(modo) { 

    //Id Actual
    var btnVer    = document.getElementById("verBtnCarrito");
    var btnLoad   = document.getElementById("loadBtnCarrito")
    var thisbtn   = (btnVer)? btnVer:btnLoad;
    var actualiza = false;

    //WebFrom hide
    xwebCollapsed(false,true);

    switch(modo)
	{
	case "Ver":
	  if(thisbtn.id == 'verBtnCarrito') return;
 	  var btnid    =  'verBtnCarrito';
 	  var btnlink  =  'Compras_verCarrito();';
	  var btnimg   =  'img/gpos_vercompras.png';
	  var btnlabel =  ' Ver Carrito';		
	  var actualiza = true;
	break;

	case "Load":
	 if(thisbtn.id == 'loadBtnCarrito') return;
	 var btnid    = 'loadBtnCarrito';		
	 var btnlink  = 'Compras_loadCarrito()';
	 var btnimg   = 'img/gpos_compras.png';
	 var btnlabel = ' Cargar Carrito';
	 var actualiza = true;
	break;
	}
	
    //Actualiza Atributos
    thisbtn.setAttribute("id",btnid);
    thisbtn.setAttribute("oncommand",btnlink);
    thisbtn.setAttribute("label",btnlabel);
    thisbtn.setAttribute("image",btnimg);
}

function Compras_loadCarrito(){
  solapa("modulos/compras/xulcompras.php?modo=entra"," Compras - Presupuestos ","compras");
}

function Compras_CBCompra() {  Compras_selrapidaCompra(true); }

function Compras_selrapidaCompra(cb=false) {
        
	var idcc  = (cb)? "c_CompraCB":"c_CapturaCB";
	var cc    = document.getElementById(idcc);		

	var url="modulos/almacen/selalmacen.php?modo=agnademudo_compras";

	var xrequest = new XMLHttpRequest();
	var data = "listacompra=" + escape(cc.value);       

	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	alert(xrequest.responseText);
	cc.value= "";
	cc.setAttribute("value","");

	Compras_verCarrito(true);
}

function Compras_altaRapida() {
	var url   = "modulos/altarapida/xulaltarapida.php?modo=alta";
	var aviso = 'Cargando Carrito Alta Rapida...';
	var main  = parent.getWebForm();
	var lurl  = 'modulos/compras/progress.php?modo=lWebFormAltaRapida&aviso='+aviso+'&url='+url;

	main.setAttribute("src",lurl);
	xwebCollapsed(true);
}

function Compras_buscar()
{  
  var xnombre     = document.getElementById("c_Nombre").value; 
  var codigo      = document.getElementById("c_CB").value;
  var referencia  = document.getElementById("c_Referencia").value;
  var obsoletos   = ( document.getElementById("c_Obsoletos").checked )?1:0;
  var porproveedor= ( document.getElementById("c_PorProveedor").checked )?1:0;
  var stockminimo = ( document.getElementById("c_StockMinimo").checked )?1:0;

  var extra ="";
	extra = extra + "&CodigoBarras="+codigo;
	extra = extra + "&Nombre="+xnombre;
	extra = extra + "&Referencia="+referencia;
	extra = extra + "&Obsoletos="+obsoletos;
	extra = extra + "&PorProveedor="+porproveedor;
	extra = extra + "&StockMinimo="+stockminimo;
  
  url = "modcompras.php?modo=buscarproductos" + extra; 

  subweb.setAttribute("src", url);
  document.getElementById("c_Nombre").focus();
}
/* ========== COMPRAS ============ */

/* ========== PRODUCTOS ============ */

var avanzadoCargado = 0;
var visiblebusca = 0;
var numPeticiones = 0;

function Productos_ModoAlta() {   

if( subweb.getAttribute('src') == "modproductos.php?modo=alta" )
  subweb.setAttribute("src","modproductos.php"); 
subweb.setAttribute("src","modproductos.php?modo=alta"); 

}



function Productos_buscarextra(idprov,idcolor,idmarca,idtalla,idfam,idlab,idalias,tc,nombre,idsubfam) {
        
   if (tc)  tc="on"; else tc="";
   
  var extra = "&IdProveedor=" + idprov;
  extra = extra + "&IdColor=" + idcolor;
  extra = extra + "&IdMarca=" + idmarca;
  extra = extra + "&IdTalla=" + idtalla;
  extra = extra + "&IdFamilia=" + idfam;
  extra = extra + "&IdLaboratorio=" + idlab;
  extra = extra + "&IdAlias=" + idalias;
  extra = extra + "&Nombre=" + nombre;
  extra = extra + "&IdSubFamilia=" + idsubfam;

  var url = "modproductos.php?modo=mostrar" + extra + "&verCompletas=" + tc;
  subweb.setAttribute("src", url);
}

function Productos_loadAvanzado(){
 var subframe;
 
 //Fuerza un update de las avanzadas cada 10 vistas
 {
	 numPeticiones = numPeticiones + 1;
	 if (numPeticiones > 10){
 		avanzadoCargado = 0;
 		numPeticiones = 0;
	 }
 }
 
 if (avanzadoCargado)
 	return;
 
 subframe = document.getElementById("subframe");
 subframe.setAttribute("src","modulos/productos/xulavanzado.php?modo=productos&rnd="+Math.random());
 subframe.setAttribute("opener",document.getElementById("web"));
  
 avanzadoCargado = 1;
}


function Productos_buscar()
{  
  var xnombre    = document.getElementById("p_Nombre").value;
  var codigo     = document.getElementById("p_CB").value;
  var referencia = document.getElementById("p_Referencia").value;

  var extra ="";
	
	extra = extra + "&CodigoBarras="+codigo;
	extra = extra + "&Nombre="+xnombre;
	extra = extra + "&Referencia="+referencia;
 
  var obsoletos  = (document.getElementById("p_Obsoletos").checked)?1:0;
	extra = extra + "&Obsoletos="+obsoletos;
       
  url = "modproductos.php?modo=buscarproductos" + extra;
  subweb.setAttribute("src", url);
  document.getElementById("p_Nombre").focus();
}

/* ========== PRODUCTOS ============ */
/* ========== PROVEEDORES ============ */

function proveedor_Alta(){
       var url = "modproveedores.php?modo=alta";
       if(subweb.getAttribute("src") == url)
	 subweb.setAttribute("src","modproveedores.php");
       subweb.setAttribute("src",url);
}
function laboratorio_Alta(){
	var url = "modlaboratorios.php?modo=alta";
	if(subweb.getAttribute("src") == url)
	  subweb.setAttribute("src","modlaboratorios.php");
	subweb.setAttribute("src",url);
}
function proveedor_Ver(){
	var url = "modproveedores.php?modo=lista";
	subweb.setAttribute("src",url);
}
function laboratorio_Ver(){
    var url = "modlaboratorios.php?modo=lista";
	subweb.setAttribute("src",url);
}


/* ========== PROVEEDORES ============ */
/* ========== CLIENTES ============ */

function clientes_Alta(){
	var url = "modclientes.php?modo=alta";
	if(subweb.getAttribute("src") == url)
	  subweb.setAttribute("src","modclientes.php");
	subweb.setAttribute("src",url);
}

function clientes_AltaParticular(){
	var url = "modclientes.php?modo=altaparticular";
	if(subweb.getAttribute("src") == url)
	  subweb.setAttribute("src","modclientes.php");
	subweb.setAttribute("src",url);
}

function clientes_Ver(){
	var url = "modclientes.php?modo=lista";
	subweb.setAttribute("src",url);
}


/* ========== CLIENTES ============ */

//if(window.innerWidth){
    //var iframe = document.getElementById("web");
    //alert(window.innerWidth);
    //var ancho = window.innerWidth - 280 + 7;
    //iframe.setAttribute("width",ancho); 
//}

var localcesion = "<?php echo getSesionDato("IdTienda");?>";
]]></script>

<?php

EndXul();

?>

