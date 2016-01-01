
    <html:div id='box-popup' class='box-popup-off'><html:span class='closepopup' onclick='closepopup()'></html:span>
    <html:iframe id='windowpopup' name='windowpopup' src='about:blank' width='100%' style='border: 0' height='100%'  onload='if(this.src != "about:blank" ) loadFocusPopup()'></html:iframe> 
    </html:div>

<command id="quitaArticulo" oncommand="QuitarArticulo()"   disabled='false'  label="<?php echo _(" Quitar artículo") ?>"/>  

<popupset>
  <popup id="accionesLista" class="media">
    <menu label=" Vender">
      <menupopup>
	<menuitem label="<?php echo _("¿Cuántas?"); ?>" 
		  oncommand="setTimeout('agnadirPorMenu(\'preguntar\')',50)"/>
	<menuitem label="1" oncommand="agnadirPorMenu(1)"/>
	<menuitem label="2" oncommand="agnadirPorMenu(2)"/>
	<menuitem label="3" oncommand="agnadirPorMenu(3)"/>
	<menuitem label="4" oncommand="agnadirPorMenu(4)"/>
	<menuitem label="5" oncommand="agnadirPorMenu(5)"/>
	<menuitem label="6" oncommand="agnadirPorMenu(6)"/>
	<menuitem label="7" oncommand="agnadirPorMenu(7)"/>
	<menuitem label="8" oncommand="agnadirPorMenu(8)"/>
	<menuitem label="9" oncommand="agnadirPorMenu(9)"/>
	<menuitem label="10" oncommand="agnadirPorMenu(10)"/>				   
      </menupopup>
    </menu>
    <menuseparator />
    <menuitem class="menuitem-iconic" image="img/gpos_tpv_menudeo.png" label=" Vender"  oncommand="agnadirPorMenu()"/>
    <menuitem id="preventaMayoreo" class="menuitem-iconic" image="img/gpos_tpv_mayoreo.png" label=" Mayoreo"  oncommand="agnadirPorMenu('mayoreo')" disabled="true"/>
    <menuseparator />
    <menuitem id="preventaFichaTecnica" class="menuitem-iconic" image="img/gpos_tpv_fichatec.png" label="<?php echo _(" Ficha Tecnica") ?>"  oncommand="lanzarFichaTecnica()" disabled="true"/>
    <menuitem class="menuitem-iconic" image="img/gpos_tpv_fichaex.png" label="<?php echo _(" Ficha de Existencias") ?>"  oncommand="ToggleFichaForm()"/>
    <menuseparator />
    <menuitem class="menuitem-iconic" image="img/gpos_tpvmensaje.png" label="<?php echo _(" Anotar Nuevo Producto") ?>"  oncommand="lanzarRegistroBorrador()" />
    <menuseparator />	   	   	   
    <menuitem class="menuitem-iconic" image="img/gpos_tpv_cancelarventa.png" label="<?php echo _(" Cancelar Venta") ?>" oncommand="BotonCancelarVenta()" />
    <menuitem class="menuitem-iconic" image="img/gpos_tpv_limpiarlista.png" label="<?php echo _(" Limpiar Lista") ?>"  oncommand="VaciarListadoProductos()"/>	   	   
  </popup>
  
  <popup id="AccionesBusquedaVentas" class="media">
     <menuitem label="<?php echo _("Revisar Detalle") ?>" oncommand="RevisarVentaSeleccionada()"/>
     <menuitem id="VentaRealizadaAbonar" label="<?php echo _("Abonar") ?>" oncommand="VentanaAbonos()"/>
     <menuseparator />
     <menu id="VentaRealizadaImprimir" label="<?php echo _("Imprimir") ?>">
       <menupopup> 
         <menuitem label="<?php echo _("Comprobante") ?>" 
                   oncommand="ReimprimirVentaSeleccionada(1)"/>
         <menuitem label="<?php echo _("Ticket detallado") ?>" 
                   oncommand="imprimirFormatoDetalladoTicketSeleccionada()"/>
         <menuitem label="<?php echo _("Ticket de Pago") ?>" 
                   oncommand="imprimirFormatoImporteVentaSeleccionada()"/>
         <menuitem id="VentaSuscripcionImprimir" label="<?php echo _("Suscripción") ?>"
                   oncommand="ReimprimirVentaSuscripcion()" collapsed="true"/>
       </menupopup>
     </menu>
     <menuseparator />

     <menu id="VentaRealizadaDevolver" label="<?php echo _("Devolver") ?>">
       <menupopup> 
        <menuitem label="<?php echo _("Efectivo") ?>" 
        oncommand="habilitaDevolucionVentaSeleccionada('efectivo')"/>
        <menuitem label="<?php echo _("Nota de Crédito") ?>" 
        oncommand="habilitaDevolucionVentaSeleccionada('credito')"/>
       </menupopup>
     </menu>

     <menuitem id="VentaRealizadaEntregarReserva" label="<?php echo _("Entregar reserva") ?>" oncommand="EntregarReservas()"/>
     <menuseparator />
     <menuitem id="VentaRealizadaBoletar" label="<?php echo _("Boletar")?>" oncommand="BoletarNroDocumento('Boletar')" />
     <menuitem id="VentaRealizadaFacturar" label="<?php echo _("Facturar")?>" oncommand="FacturarNroDocumento('Facturar')" />
     <menuitem id="VentaRealizadaFacturarLote" label="<?php echo _("Facturar por lote")?>" oncommand="FacturarPorLote()" />
     <menuseparator />
     <menuitem id="VentaRealizadaCambioCliente" label="<?php echo _("Cambiar cliente")?>" oncommand="CambiarClienteDocumento()"  <?php gulAdmite("Administracion") ?> />
     <menuseparator />

     <menuitem id="VentaRealizadaAnularNro" label="<?php echo _("Anular Nro.")?>" oncommand="ModificarNroDocumento('Anular')" />
     <menu id="VentaVarios" label="<?php echo _("Modificar") ?>">
       <menupopup> 
     <menuitem id="VentaRealizadaCambioNro" label="<?php echo _("Serie-Nro")?>" oncommand="ModificarNroDocumento('Modificar')" /> 
         <menuitem id="VentaRealizadaCambioAnularNro" label="<?php echo _("Anular Serie-Nro")?>" oncommand="ModificarNroDocumento('Modificar_y_Anular')" />
         <menuitem id="VentaRealizadaAnularNro" label="<?php echo _("Fecha Emisión")?>" oncommand="ModificarNroDocumento('Modificar_FechaEmision')"  />
         <menuitem id="VentaRealizadaFechaPago" label="<?php echo _("Fecha Pago")?>" oncommand="ModificarNroDocumento('Modificar_FechaPago')"  />
         <menuitem id="VentaEstadoReserva" label="<?php echo _("Estado Reserva")?>" oncommand="formModificarEstadoReserva()" />
       </menupopup>
     </menu>
     <menuseparator />
     <menuitem id="ckCodigoAutorizacion" label="<?php echo _("Código de Autorización")?>" oncommand="ckCodigoAutorizacion('ck',false,false,false)"  <?php gulAdmite("Administracion") ?> />

  </popup>   
<popup id="AccionesDetallesVentas" class="media">
     <menuitem id="menuDevolverProducto" image="img/gpos_tpv_ventas.png"  label="<?php echo _("Devolver") ?>" oncommand="cargarProducto2Devolver()" collapsed="true"/>
     <menuitem id="VentaGarantiaComprobante" label="<?php echo _("Garantía") ?>" oncommand="verGarantiaComprobante()"/>

     <menuseparator />
     <menuitem id="VentaRealizadaDetalleNS" label="<?php echo _("Ver Números de Serie") ?>" oncommand="verNSVentaSeleccionada()"/>
     <menuitem id="VentaRealizadaDetalleMProducto" label="<?php echo _("Ver Detalle MixProducto") ?>" oncommand="verDetMPSeleccionada()"/>
  </popup> 

  <popup id="accionesTicket" class="media">
    <menu id='ticketUnidades' label="Unidades">
      <menupopup>
	<menuitem label="<?php echo _("¿Cuántas?") ?>" 
	oncommand='ModificaTicketUnidades(-1)'/>
	<menuitem label="1" oncommand='ModificaTicketUnidades(1)'/>
	<menuitem label="2" oncommand='ModificaTicketUnidades(2)'/>
	<menuitem label="3" oncommand='ModificaTicketUnidades(3)'/>
	<menuitem label="4" oncommand='ModificaTicketUnidades(4)'/>
	<menuitem label="5" oncommand='ModificaTicketUnidades(5)'/>
	<menuitem label="6" oncommand='ModificaTicketUnidades(6)'/>
	<menuitem label="7" oncommand='ModificaTicketUnidades(7)'/>
	<menuitem label="8" oncommand='ModificaTicketUnidades(8)'/>
	<menuitem label="9" oncommand='ModificaTicketUnidades(9)'/>
	<menuitem label="10" oncommand='ModificaTicketUnidades(10)'/>
      </menupopup>
    </menu>
    <menuseparator />	      
    <menuitem id="ticketModificarPrecio" label="<?php echo _(" Modificar precio") ?>" 
    class="menuitem-iconic" image="img/gpos_tpv_ventas.png" 
    oncommand="ModificarPrecio(false)" <?php gulAdmite("Precios") ?> />
    <menuitem id="ticketModificarDescuento" label="<?php echo _(" Modificar descuento") ?>" 
    class="menuitem-iconic" image="img/gpos_tpv_ventas.png" 
    oncommand="ModificarDescuento(<?php jsAdmite("Precios",false) ?>,false)"/>
    <menuitem id="ticketModificarImporte" label="<?php echo _(" Modificar importe") ?>" 
    class="menuitem-iconic" image="img/gpos_tpv_ventas.png" 
    oncommand="ModificarImporte()" <?php gulAdmite("Precios") ?> />
    <menuseparator />
    <menuitem class="menuitem-iconic" image="img/gpos_tpvservicios.png" 
	      label="<?php echo _(" Agregar Servicio") ?>"
    oncommand="ServicioParaFila()"/>	   
    <menuitem class="menuitem-iconic" image="img/gpos_tpvservicios.png" 
	      label="<?php echo _(" Modificar Concepto") ?>"
    oncommand="ConceptoParaFila()"/>	   
    <menuseparator />
    <menuitem class="menuitem-iconic" image="img/gpos_productos.png" 
	      command="quitaArticulo" />
    <menuseparator />
    <menuitem id="preventaNumerosSeries" label="<?php echo _(" Mostrar Números Serie") ?>" 
    class="menuitem-iconic" image="img/gpos_barcode.png" disabled="true"
    oncommand="mostrarseries('mostrar',0)"/>
    <menuitem id="preventaDetalleMProducto" label="<?php echo _(" Mostrar MixProductos") ?>"
    class="menuitem-iconic" image="img/gpos_tpvreferencia.png" disabled="true"
    oncommand="mostrardetalleMProducto('mostrar',0)"/>
    <menuseparator />
    <menuitem class="menuitem-iconic" image="img/gpos_tpv_cancelarventa.png" 
	      label="<?php echo _(" Cancelar venta") ?>" oncommand="CancelarTicket()" />
    
  </popup>
  <popup id="AccionesProductoDetalleSat">
    <menuitem id="itemAgregarProductoSatDet" label="<?php echo _("Agregar") ?>"
              oncommand="mostrarFormProductoSatDetalle('true')"/>
    <menuitem id="itemModificarProductoSatDet" label="<?php echo _("Quitar") ?>"
              oncommand="ModificarProductoDetSat('Quitar')"/>
  </popup>
  <popup id="AccionesSuscripcionLinea"> 
        <menuitem id="itemAgregarSuscripcionLinea" label="<?php echo _("Agregar") ?>"
              oncommand="elijePanelProducto('Suscripcion')"/>
        <menuitem id="itemEditarSuscripcionLinea" label="<?php echo _("Editar") ?>"
              oncommand="editarSuscripcionLinea()"/>
  </popup>
  <popup id="AccionesComprobantesVentaSuscripcion"> 
        <menuitem  label="<?php echo _("Ver Detalle") ?>"
              oncommand="verComprobanteVentaSuscripcion()"/>
  </popup>
  <popup id="AccionesclientPickArea"> 
    <menuitem  label="<?php echo _("Usar Seleccionado") ?>" 
    oncommand="cargarCliente('sel')"/>

    <menuitem  label="<?php echo _("Modificar") ?>" 
    oncommand="VerClienteId()"/>
    <menuitem  label="<?php echo _("Suscripción") ?>" id="cargarSuscripcion"
    oncommand="cargarSuscripcion()" <?php gulAdmite("Suscripcion") ?> />
    <menuitem  label="<?php echo _("Abonar Efectivo") ?>" id="cargarVentaAbono"
    oncommand="AbonarPorCliente()"  <?php gulAdmite("CajaTPV") ?> />
    <menuitem  label="<?php echo _("Asignar Crédito ") ?>" id="cargarVentaAbonoCredito"
    oncommand="AsignarCreditoPorCliente()"  <?php gulAdmite("CajaTPV") ?> />
    <menuitem id="ckCodigoAutorizacionCliente" label="<?php echo _("Código de Autorización")?>" oncommand="ckCodigoAutorizacionCliente('ckcliente',false)"  <?php gulAdmite("Administracion") ?> />

  </popup>
  <popup id="AccionesBusquedaCobrosVenta">
    <menuitem label="<?php echo _("Eliminar") ?>" 
              oncommand="ModificarCobrosVenta('1')"
              <?php gulAdmite("Cobros") ?>/>
    <menuseparator />
    <menuitem id="mheadImprimir" label="<?php echo _("Imprimir") ?>" oncommand="ImprimirCobroSeleccionadaVenta()"/>
  </popup>
</popupset>
	  