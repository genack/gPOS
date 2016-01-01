<vbox id="boxServicios" collapsed="true">
  <!-- Servicios Encabezado-->
  <vbox> 
    <vbox pack="center" align="center">
      <radiogroup  flex="1" orient="horizontal">
	<radio id="OrdenServicio" value="0" label="SAT" selected="true"
	       oncommand="mostrarServicios(this.id)"  />
	<radio id="Outsourcing" value="1"  crop="right" label="Outsourcing"
	       oncommand="mostrarServicios(this.id)" />
      </radiogroup>
    </vbox>
  </vbox> 
  <vbox id="boxOrdenservicio" flex="1" collapsed="false" >
    <!-- Ordenes de servicio -->
    <vbox id="vboxOrdenServicio">
      <!--  Encabezado-->
      
      <!-- Cuerpo principal -->
      
      <vbox id="busquedaOrdenServicio">
	<hbox align="start" pack="center" >
	  <vbox>
	    <description>Desde:</description>
	    <datepicker id="FechaBuscaDesde" type="popup" onchange="BuscarOrdenServicio()"/>
	  </vbox>
	  <vbox>
	    <description>Hasta:</description>
	    <datepicker id="FechaBuscaHasta" type="popup" onchange="BuscarOrdenServicio()"/>
	  </vbox>
	  <vbox>
	    <description>Cliente:</description>
	    <textbox onfocus="select()" id="NombreBusqueda"
		     onkeyup="if (event.which == 13) BuscarOrdenServicio()"
		     onkeypress="return soloAlfaNumerico(event);"/>
	  </vbox>
	  <vbox>
	    <description>Codigo:</description>
	    <textbox onfocus="select()" id="CodigoBusqueda" 
		     onkeyup="if (event.which == 13) BuscarOrdenServicio()"
		     onkeypress="return soloAlfaNumerico(event);"/>
	  </vbox>
	  <vbox id="vboxAsignado_a" collapsed="true">
	    <description>Asignado a:</description>
	    <menulist  id="UsuarioBusqueda" oncommand="BuscarOrdenServicio()">
	      <menupopup>
		<menuitem label="Todos" value="0" style="font-weight: bold" />
                <?php echo genXulComboUsuarios(false,"menuitem",
			  CleanID(getSesionDato("IdTiendaDependiente"))) ?>
	      </menupopup>
	    </menulist>
	  </vbox>
	  <vbox id="vboxEstado">
	    <description>Estado:</description>
	    <menulist  id="EstadoBusqueda" oncommand="BuscarOrdenServicio()">
	      <menupopup>
		<menuitem label="Todos" value="Todos" style="font-weight: bold" />
		<menuitem label="Pendiente" value="Pendiente"/>
		<menuitem label="Ejecución" value="Ejecucion"/>
		<menuitem label="Finalizado" value="Finalizado"/>
		<menuitem label="Cancelado" value="Cancelado"/>
	      </menupopup>
	    </menulist>
	  </vbox>
	  <vbox id="vboxTipo_Servicio" collapsed="true">
	    <description>Tipo Servicio:</description>
	    <menulist  id="TipoBusqueda" oncommand="BuscarOrdenServicio()">
	      <menupopup>
		<menuitem label="Todos" value="Todos" style="font-weight: bold" />
		<menuitem label="Regular" value="Regular"/>
		<menuitem label="Garantía" value="Garantia"/>
	      </menupopup>
	    </menulist>
	  </vbox>
          <vbox id="vboxFacturacion" collapsed="true">
	    <description>Facturación:</description>
	    <menulist  id="EstadoBusquedaFacturacion" oncommand="BuscarOrdenServicio()">
	      <menupopup>
		<menuitem label="Todos" value="Todos" style="font-weight: bold" />
		<menuitem label="Pendiente" value="0"/>
		<menuitem label="Facturado" value="1"/>
	      </menupopup>
	    </menulist>
          </vbox>
	  <vbox style="margin-top:1em">
	    <menu>
	      <toolbarbutton image="img/gpos_busqueda_avanzada.png" style="min-height: 2.7em;"/>
	      <menupopup >
		<menuitem type="checkbox" label="Asignado a" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
		<menuitem type="checkbox" label="Tipo Servicio" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
		<menuitem type="checkbox" label="Facturacion" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	         <menuseparator />
		<menuitem type="checkbox" label="Fecha Entrega" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	        <menuitem type="checkbox" label="Registrado por" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	        <menuitem type="checkbox" label="Entregado por" checked="false"
			  oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	      </menupopup>
	    </menu>
	  </vbox>
	  <vbox style="margin-top:1.3em">
	    <button class="btn" id="btnbuscar" label=" Buscar " image="img/gpos_buscar.png"
		    oncommand="BuscarOrdenServicio()"/>
	  </vbox>
	</hbox>
      </vbox>
    </vbox>
    
    <vbox flex="1" style="padding:1em;">
      <hbox flex="0" >
	<caption class="box" label="<?php echo _("Incidencias") ?>" />
      </hbox>
      
      <!-- Lista de Orden de Servicio-->
      <listbox flex="1" id="listadoOrdenServicio"
	       contextmenu="AccionesOrdenServicio" 
	       onkeypress="if (event.keyCode==13) RevisarOrdenServicioSeleccionada()"
	       onclick="RevisarOrdenServicioSeleccionada()"
	       ondblclick="mostrarFormOrdenServicio('Editar')">
	<listcols flex="1">
	  <listcol/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolFecha_Entrega" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" />
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolRegistrado_por" collapsed="true"/>
	  <splitter class="tree-splitter" />
	  <listcol flex="1" id="vlistcolEntregado_por" collapsed="true"/>
	  <listcol/>
	</listcols>
	<listhead>
	  <listheader label=" # " style="font-style:italic;"/>
	  <listheader label="Local" />
	  <listheader label="Código" />
	  <listheader label="Estado" />
	  <listheader label="Prioridad" />
	  <listheader label="Fecha Registro"/>
	  <listheader label="Facturación" />
	  <listheader label="Cliente" />
	  <listheader label="Fecha Entrega" id="vlistFecha_Entrega" collapsed="true"/>
	  <listheader label="Impuesto" style="text-align:center"/>
	  <listheader label="Importe" style="text-align:center"/>
	  <listheader label="Registrado por" id="vlistRegistrado_por" collapsed="true"/>
	  <listheader label="Entregado por" id="vlistEntregado_por" collapsed="true"/>
	  <listheader label="" />
	</listhead>
      </listbox>


    <splitter collapse="none"  resizeafter="farthest" orient="vertical">&#8226; &#8226; &#8226;</splitter>  
    <!-- Lista de Ordenes de Servicio -->
    
    
    <!-- Resumen Orden de Servicio Detalle -->
    <vbox id="resumenOrdenServicioDetalle" collapsed="true">
      <hbox flex="1">
	<caption class="box" label=" <?php echo _("Detalle Incidencia") ?>" />
	<spacer flex="1"/>
	<hbox style="margin-top:-2.2em;margin-right:0.5em;">
	  <menu>
	    <toolbarbutton image="img/gpos_busqueda_avanzada.png" />
	    <menupopup >
	      <menuitem type="checkbox" label="Fecha Fin" checked="false"
			oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	      <menuitem type="checkbox" label="Estado Solucion" checked="false"
			oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	      <menuitem type="checkbox" label="Estado Garantia" checked="false"
			oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	      <menuitem type="checkbox" label="Numero Serie" checked="false"
			oncommand = "mostrarBusquedaAvanzadaOrdenServicioDet(this);"/>
	    </menupopup>
	  </menu>
	</hbox>
      </hbox>

    </vbox>
    <!-- Resumen Orden de Servicio Detalle -->
    
    <listbox flex="1" id="listadoOrdenServicioDetalle" collapsed="true"
	     contextmenu="AccionesOrdenServicioDetalle" 
	     onkeypress="if (event.keyCode==13) RevisarOrdenServicioDetSeleccionada()"
	     onclick="RevisarOrdenServicioDetSeleccionada()"
	     ondblclick="mostrarFormOrdenServicioDet('Editar',false)">
      <listcols flex="1">
	<listcol/>
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolFecha_Fin" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolEstado_Garantia" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolEstado_Solucion" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" id="vlistcolNumero_Serie" collapsed="true"/>
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<splitter class="tree-splitter" />
	<listcol flex="1" />
	<listcol />
      </listcols>
      <listhead>
	<listheader label=" # " style="font-style:italic;"/>
	<listheader label="Concepto" />
	<listheader label="Fecha Asignación" />
	<listheader label="Fecha Fin" id="vlistFecha_Fin" collapsed="true"/>
	<listheader label="Estado" style="text-align:center"/>
	<listheader label="Estado Garantía" id="vlistEstado_Garantia" collapsed="true"/>
	<listheader label="Estado Solución" id="vlistEstado_Solucion" collapsed="true"/>
	<listheader label="Numero Serie" id="vlistNumero_Serie" collapsed="true"/>
	<listheader label="Cantidad" style="text-align:center"/>
	<listheader label="Precio" style="text-align:center"/>
	<listheader label="Importe" style="text-align:center"/>
	<listheader label="Asignado a" style="text-align:center"/>
	<listheader label=""/>
      </listhead>
    </listbox>
    </vbox>    


    <!-- Formulario de Orden de Servicio -->
    <vbox id="vboxFormOrdenServicio" collapsed="true">
      <vbox align="center"  pack="top">
	<vbox>
	    <caption id="wtitleFormOrdenServicio" class="box"
		     label="<?php echo _("Nueva Incidencia") ?>" />
	</vbox>


	<vbox id="rowNumeroOrdenServicio" collapsed="false">
	  <hbox style="font-size: 16px;">
	    
	    <toolbarbutton image="img/gpos_numeral.png" style="padding: .3em"/>		    
	    <textbox id="serieOrdenServicio" 
		     style="width:3em;margin:3px;text-align:right;"
		     onchange="verificarSerieNumOrdenServicio('Serie',this.value)"
		     value="" onfocus="select()"/>
	    <textbox flex="1" id="numOrdenServicio" value="" onfocus="select()" 
		     style="width:7em;text-align:right;"
		     onchange="verificarSerieNumOrdenServicio('Numero',this.value);"
		     onkeypress="if (event.which == 13) mostrarPanelElijeCliente();return soloNumerosEnteros(event,this.value);"/>
	  </hbox>
	</vbox>

	<vbox>
	  
	  <hbox style="font-size: 16px;">
	    
	    <toolbarbutton image="img/gpos_buscarcliente.png"  style="padding: .3em" 
			   id="lClientHab" oncommand="mostrarPanelElijeCliente()"/>
	    <description flex="1" id="nombreClienteOrdenServicio" value="Elije cliente..." readonly="true" class="xbase"
			 onclick="mostrarPanelElijeCliente()"  style="padding:1em 1em 1em 0em;"/>
	    
	    <textbox flex="1" id="idClienteOrdenServicio" value="" collapsed="true"/>
	    <textbox flex="1" id="TipoOrdenServicio" value="Regular" collapsed="true"/>
	  </hbox>
	  
	</vbox>
	<vbox style="font-size: 14px;padding-bottom:.6em">
          <grid> 
            <rows>
	      <row id="rowEstadoOrdenServicio" collapsed="false" class="xbase">
	        <description value="Estado:"/>
	        <menulist id="FiltroEstado" flex="1" label="FiltrosEstado">
	          <menupopup id="comboestado">
		    <menuitem id="itmEstadoPendiente" label="Pendiente"
			      value="Pendiente" selected="true"/>
		    <menuitem id="itmEstadoEjecucion" label="Ejecucion"
			      value="Ejecucion" collapsed="true"/>
		    <menuitem id="itmEstadoFinalizado" label="Finalizado"
			      value="Finalizado" collapsed="true"/>
		    <menuitem id="itmEstadoCancelado" label="Cancelado"
			      value="Cancelado" collapsed="true"/>
	          </menupopup>
	        </menulist>
	      </row>
	      <row id="rowPrioridadOrdenServicio" collapsed="false" class="xbase">
	        <description value="Prioridad:"/>
	        <menulist id="FiltroPrioridad" flex="1" label="FiltrosPrioridad">
	          <menupopup id="comboprioridad">
		    <menuitem id="itmPrioridadNormal" label="Normal"
			      value="1" selected="true"/>
		    <menuitem id="itmPrioridadAlta" label="Alta"
			      value="2" />
		    <menuitem id="itmPrioridadMuyAlta" label="Muy Alta"
			      value="3"/>
	          </menupopup>
	        </menulist>
	      </row>
            </rows>
          </grid>
        </vbox>
	<vbox>
	  <hbox>
	    <button  class="btn" flex="1" id="btnOrdenServicioCancel"
		     image="img/gpos_cancelar.png"
		     label=" Cancelar "
		     oncommand="volverOrdenServicio('Nuevo')" />
	    <button  class="btn" flex="1" id="btnOrdenServicioAceptar"
		     image="img/gpos_aceptar.png"
		     label=" Aceptar "
		     oncommand="RegistrarOrdenServicio(false)" />
	    </hbox>
	</vbox>
	

      </vbox>
      <!-- Formulario Orden de Servicio -->
      
    </vbox>
    <!-- Cuerpo principal -->
    


    <!-- Formulario Orden de Servicio Detalle -->
    <panel id="boxFormOrdenServicioDet" style="border:1px solid #aaa" class="box">
      <vbox pack="center" align="center">
	<caption id="wtitleFormOrdenServicioDet" class="box"
		 label="<?php echo _("Nuevo Servicio") ?>" />
      </vbox>
      <tabbox flex="1" class="box">
	<tabs style="font-size: 11px;">
	  <tab id="tab-servicios-oc" label="Servicio" />
	  <tab id="tab-servicios-sat-oc" collapsed="true" label="Información Técnica" onclick="LoadProductosSat()" />
	</tabs>
											  <tabpanels id="tab-boxservicios" flex="1" class="box">
	  <tabpanel flex="1" pack="center">
            <vbox >
	      <vbox align="center">
	      <caption label="Servicio"
	      style="padding-bottom:.5em;font-size:120%"/>
	      </vbox>
              <grid style="padding-bottom:.5em">
                <rows>
		  <row id="rowTipoServicio" >
		    <description value="Nombre" style="width:10em"/>
		    <menulist id="FiltroTipoServicio" 
			      oncommand="mostrarProductoSat()">
		      <menupopup id="elementosServicios"/>
		    </menulist>
		  </row>
		  <row id="rowDescTipoServicio" collapsed="true">
                    <description value="Nombre" style="width:10.6em;font-weight: bold;"/>
                    <description id="dctTipoServicio" flex="1"/>
		  </row>
                </rows>
              </grid>
	      <hbox id="vboxServicioDetalleSat">
		<vbox >
		  <grid>
		    <rows>
		      <row id="rowEstadoOrdenSericioDet" collapsed="false">
			<description value="Estado" style="width:10em"/>
			<menulist id="FiltroEstadoOSDet" label="" style="width:23em"
				  oncommand="verificarEstadoOrdenServicioDet(this.value)">
			  <menupopup id="comboestado">
			    <menuitem id="itmEstadoPendienteDet" label="Pendiente"
				      value="Pendiente" selected="true" collapsed="true"/>
			    <menuitem id="itmEstadoEjecucionDet" label="Ejecucion" 
				      value="Ejecucion"/>
			    <menuitem id="itmEstadoFinalizadoDet" label="Finalizado"
				      value="Finalizado" collapsed="true"/>
			    <menuitem id="itmEstadoCanceladoDet" label="Cancelado"
				      value="Cancelado" />
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnEstadoOrdenServicioDet" collapsed="true">
			<description value="Estado" style="width:8em;font-weight: bold;"/>
			<description id="dtnEstadoOrdenServicioDet" style="width:20em"/>
		      </row>
		      <row id="rowListaUsuario" collapsed="false">
			<description value="Asignado a"/>
			<menulist  id="listIdUsuario">
			  <menupopup>
			    <menuitem label="Elegir..." value="0" selected="true"
				      style="font-weight: bold;" />
			    <?php echo genXulComboUsuarios(false,"menuitem",
				  CleanID(getSesionDato("IdTiendaDependiente"))) ?>
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnListaUsuario" collapsed="true">
			<description value="Asignado a" style="font-weight: bold;"/>
			<description id="dtnListaUsuario" style="width:20em"/>
		      </row>
		      <row id="rowFechaInicioServicio" collapsed="true">
			<description value="Fecha Asignación" style="width:10em"/>
			<hbox>
			  <datepicker id="fInicioAtencionServicio" type="popup"/>
			  <timepicker id="hInicioAtencionServicio" type="popup"/>
			</hbox>
		      </row>
		      <row id="rowdtnFechaInicioServicio" collapsed="true">
			<description value="Fecha Asignación" style="font-weight: bold;"/>
			<description id="dtnFechaInicioServicio" />
		      </row>
		      <row id="rowFechaFinServicio" collapsed="true">
			<description value="Fecha Finalizado"/>
			<hbox>
			  <datepicker id="fFinAtencionServicio" type="popup"/>
			  <timepicker id="hFinAtencionServicio" type="popup"/>
			</hbox>
		      </row>
		      <row id="rowdtnFechaFinServicio" collapsed="true">
			<description value="Fecha Finalizado" style="font-weight: bold;"/>
			<description id="dtnFechaFinServicio" />
		      </row>
		      
		      <row id="rowConceptoServicio" collapsed="true">
			<description value="Concepto"/>
			<textbox id="ConceptoServicio" value="" rows="1" multiline="true"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnConceptoServicio" collapsed="true">
			<description value="Concepto" style="font-weight: bold;"/>
			<description id="dtnConceptoServicio" style="width:20em;"/>
		      </row>
		      
		      <row id="rowCantidadServivio" collapsed="false">
			<description value="Cantidad"/>
			<textbox id="CantidadServicio" value="1"
				 onkeypress="return soloNumeros(event,this.value);"
				 onchange="calcularImporteOSDet()"/>
		      </row>
		      <row id="rowdtnCantidadServivio" collapsed="true">
			<description value="Cantidad" style="font-weight: bold;"/>
			<description id="dtnCantidadServivio" style="width:20em;"/>
		      </row>
		      
		      <row id="rowPrecioServicio" collapsed="false">
			<description value="Precio"/>
			<textbox id="PrecioServicio" value="0"
				 onkeypress="return soloNumeros(event,this.value);"
				 onchange="calcularImporteOSDet()"/>
		      </row>
		      <row id="rowdtnPrecioServicio" collapsed="true">
			<description value="Precio" style="font-weight: bold;"/>
			<description id="dtnPrecioServicio" style="width:20em;"/>
		      </row>
		      
		      <row id="rowImporteServicio" collapsed="false">
			<description value="Importe"/>
			<textbox id="ImporteServicio" value="0"
				 onkeypress="return soloNumeros(event,this.value);"
				 onchange="calcularImporteOSDet()"/>
		      </row>
		      <row id="rowdtnImporteServicio" collapsed="true">
			<description value="Importe" style="font-weight: bold;"/>
			<description id="dtnImporteServicio" style="width:20em;"/>
		      </row>

		      <row id="rowObservacionServicio">
			<description value="Observación"/>
			<textbox id="ObservacionServicio" value="" rows="1" multiline="true"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnObservacionServicio" collapsed="true">
			<description value="Observación" style="font-weight: bold;"/>
			<description id="dtnObservacionServicio" style="width:20em;"/>
		      </row>
		    </rows>
		  </grid>
		</vbox>
		<spacer style="width:2em;"/>
		<vbox>
		  <grid>
		    <rows>
		      <row id="rowUbicacionServicio" collapsed="false">
			<description value="Ubicación"/>
			<menulist  id="UbicacionServicio" style="width:20em"
                                   oncommand="mostrarUbicacionServicio(this.value)">
			  <menupopup>
			    <menuitem label="Local" value="Local" selected="true"/>
                            <menuitem label="Externo" value="Externo"/>
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnUbicacionServicio" collapsed="true">
			<description value="Ubicación" style="font-weight: bold;"/>
			<description id="dtnUbicacionServicio" />
		      </row>
		      
		      <row id="rowDireccionServicio" collapsed="true">
			<description value="Dirección"/>
			<textbox id="DireccionServicio" value=""
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnDireccionServicio" collapsed="true">
			<description value="Dirección" style="font-weight: bold;"/>
			<description id="dtnDireccionServicio" style="width:20em;"/>
		      </row>

		      <row id="rowGarantiaCondicion" collapsed="true">
			<description value="Garantía"/>
			<menulist  id="listGarantiaCondicion" style="width:20em">
			  <menupopup>
			    <menuitem value="2" label="No Aplica" selected="true"/>
			    <menuitem value="1" label="Aplica"/>
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnGarantiaCondicion" collapsed="true">
			<description value="Garantía" style="font-weight: bold;"/>
			<description id="dtnGarantiaCondicion" />
		      </row>
		      <row id="rowEstadoSolucion" collapsed="true">
			<description value="Solución"/>
			<menulist  id="listEstadoSolucion">
			  <menupopup>
			    <menuitem label="Ninguna" value="Ninguna" selected="true"/>
			    <menuitem label="Parcial" value="Parcial"/>
			    <menuitem label="Completa" value="Completa" />
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnEstadoSolucion" collapsed="true">
			<description value="Solución" style="font-weight: bold;"/>
			<description id="dtnEstadoSolucion" />
		      </row>
		      <row id="rowUbicacionProducto" collapsed="false" >
			<description value="Seguimiento"/>
			<menulist  id="listUbicacionProducto" style="width:20em">
			  <menupopup>
			    <menuitem label="Taller" value="Taller" selected="true"/>
			    <menuitem label="Almacén" value="Almacen"/>
			    <menuitem label="Entregado" value="Entregado" />
			  </menupopup>
			</menulist>
		      </row>
		      <row id="rowdtnUbicacionProducto" collapsed="true">
			<description value="Seguimiento" style="font-weight: bold;"/>
			<description id="dtnUbicacionProducto" />
		      </row>
                      <caption id="titleEvaluacion" label="Evaluación Técnica:" 
                               style="padding:.5em 0 .5em;font-size:120%"/>
		      <row id="rowMotivoSat">
			<description value="Motivo" style="font-weight: bold;"/>
			<menulist  id="listMotivoSat">
			  <menupopup id="elementosMotivoSat">
                            <?php echo genXulComboMotivoSat(1,"menuitem")?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewMotivoSat" collapsed="true">
			<description value="Motivo" style="font-weight: bold;"/>
			<textbox id="txtMotivoSat" value="" 
				 placeholder="Nuevo Motivo, Enter para guardar"
				 onkeyup="if (event.which == 13) RegistrarMotivoSat(this.value)"
				 onblur="mostrarNuevoMotivoSat(false)"/>
		      </row>
		      <row id="rowdtnMotivoSat" collapsed="true">
			<description value="Motivo" style="font-weight: bold;"/>
			<description id="dtnMotivoSat" style="width:20em;"/>
		      </row>
		      <row id="rowDiagnostico" collapsed="true">
			<description value="Diagnóstico" style="font-weight: bold;"/>
			<textbox id="DiagnosticoSat" value="" rows="1" multiline="true"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnDiagnostico" collapsed="true">
			<description value="Diagnóstico" style="font-weight: bold;"/>
			<description id="dtnDiagnosticoSat" style="width:20em;"/>
		      </row>
		      <row id="rowResultado" collapsed="true">
			<description value="Resultado" style="font-weight: bold;"/>
			<textbox id="ResultadoSat" value="" rows="1" multiline="true"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnResultado" collapsed="true">
			<description value="Resultado" style="font-weight: bold;"/>
			<description id="dtnResultadoSat" style="width:20em;"/>
		      </row>
		    </rows>
		  </grid>   
		</vbox>
	      </hbox>
            </vbox>
	  </tabpanel>
	  <tabpanel id="tab-productosat" pack="center">
            <vbox align="center" class="box">
              <caption class="box" label="Información Técnica"
              style="padding-bottom:.5em;font-size:120%" />
	      <hbox style="padding:.3em;" class="box">
		<vbox class="box">
		  <caption label="Producto:" style="padding-bottom:.5em;font-size:120%"/>
		  <grid >
		    <rows>
		      <row id="rowProducto">
			<description value="Nombre"/>
			<menulist  id="listProductoSat" value="0"
				   label="Elegir" style="min-width:20em">
			  <menupopup id="elementosProductoSat">
                            <?php echo genXulComboProductoSat(1,"menuitem",0) ?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewProductoSat" collapsed="true">
			<description value="Producto"/>
			<textbox id="txtProductoSat" value="" style="min-width:20em"
				 placeholder="Nuevo Producto, Enter para guardar"
				 onkeyup="if (event.which == 13) RegistrarProductoIdiomaSat(this.value,0)"
				 onblur="mostrarNuevoProductoSat(false,0)"/>
		      </row>
		      <row id="rowdtnProducto" collapsed="true">
			<description value="Producto" style="font-weight: bold;"/>
			<description id="dtnProducto" style="width:20em;"/>
		      </row>
		      <row id="rowMarca">
			<description value="Marca" />
			<menulist  id="listMarca" value=" " style="width:20em"
				   oncommand="changeMarca(0)">
			  <menupopup id="elementosMarca">
                            <?php echo genXulComboMarcasSat(1,"menuitem",0) ?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewMarca" collapsed="true">
			<description value="Marca" />
			<textbox id="txtMarca" value="" style="width:20em"
				 placeholder="Nueva Marca, Enter para guardar"
				 onkeypress="if (event.which == 13) RegistrarMarcaSat(this.value,0)"
				 onblur="mostrarNuevoMarca(false,0,false);"/>
		      </row>
		      <row id="rowdtnMarca" collapsed="true">
			<description value="Marca" style="font-weight: bold;"/>
			<description id="dtnMarca" style="width:20em;"/>
		      </row>
		      
		      <row id="rowModeloSat">
			<description value="Modelo"/>
			<menulist  id="listModeloSat" value=" ">
			  <menupopup id="elementosModeloSat">
                            <?php echo genXulComboModeloSat(1,"menuitem",1,0) ?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewModeloSat" collapsed="true">
			<description value="Modelo"/>
			<textbox id="txtModeloSat" value=""
				 placeholder="Nuevo Modelo, Enter para guardar"
				 onkeyup="if (event.which == 13) RegistrarModeloSat(this.value,0)"
				 onblur="mostrarNuevoModelo(false,0)"/>
		      </row>
		      <row id="rowdtnModeloSat" collapsed="true">
			<description value="Modelo" style="font-weight: bold;"/>
			<description id="dtnModeloSat" style="width:20em;"/>
		      </row>
		      <row id="rowDescripcion" collapsed="false">
			<description value="Detalle"/>
			<textbox id="DescripcionSat" value=""
				 multiline="true" rows="2"
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnDescripcion" collapsed="true">
			<description value="Detalle" style="font-weight: bold;"/>
			<description id="dtnDescripcion" style="width:20em;"/>
		      </row>
		      <row id="rowNumeroSerie" collapsed="false">
			<description value="Número Serie"/>
			<textbox id="NumeroSerieSat" value=""
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row id="rowdtnNumeroSerie" collapsed="true">
			<description value="Número Serie" style="font-weight: bold;"/>
			<description id="dtnNumeroSerie" style="width:20em;"/>
		      </row>
		      <row id="rowDetalleProductoSat">
			<description value="" ></description>
			<checkbox id="idDetalleSat" oncommand="vertabProductoDetalleSat(this.checked)" label=" mas información técnica"/>
		      </row>
		    </rows>
		  </grid>
		</vbox>
                <spacer style="width:2em;"/>
		<vbox id="formProductoSatDetalle" collapsed="true" class="box">
                  <caption label="Detalle:"
                  style="padding-bottom:.5em;font-size:120%"/>
		  <grid>
		    <rows id="rowsProductoDetSat">
		      <row id="rowProductoDetSat">
			<description value="Producto"/>
			<menulist  id="listProductoDetSat" value=" ">
			  <menupopup id="elementosProductoDetSat">
                            <?php echo genXulComboProductoSat(1,"menuitem",1) ?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewProductoDetSat" collapsed="true">
			<description value="Producto"/>
			<textbox id="txtProductoDetSat" value=""
				 onkeyup="if (event.which == 13) RegistrarProductoIdiomaSat(this.value,1)"
				 placeholder="Nuevo Producto, Enter para guardar"
				 onblur="mostrarNuevoProductoSat(false,1)"/>
		      </row>
		      <row id="rowMarcaDetSat">
			<description value="Marca" />
			<menulist  id="listMarcaDetSat" style="min-width:20em" value=" "
				   oncommand="changeMarca(1)">
			  <menupopup id="elementosMarcaDetSat">
                            <?php echo genXulComboMarcasSat(1,"menuitem",1) ?>
                          </menupopup>
			</menulist>
		      </row>
		      <row id="rowNewMarcaDetSat" collapsed="true">
			<description value="Marca"/>
			<textbox id="txtMarcaDetSat" value="" style="min-width:20em"
				 placeholder=" Nueva Marca, Enter para guardar "
				 onkeyup="if (event.which == 13) RegistrarMarcaSat(this.value,1)"
				 onblur="mostrarNuevoMarca(false,1);"/>
		      </row>
		      <row id="rowModeloDetSat">
			<description value="Modelo"/>
			<menulist  id="listModeloDetSat" value=" ">
			  <menupopup id="elementosModeloDetSat">
                          <?php echo genXulComboModeloSat(1,"menuitem",1,1) ?>
                          </menupopup>
			</menulist>
		      </row>
		    
		      <row id="rowNewModeloDetSat" collapsed="true">
			<description value="Modelo"/>
			<textbox id="txtModeloDetSat" value=""
				 onkeyup="if (event.which == 13) RegistrarModeloSat(this.value,1)"
				 placeholder=" Nuevo Modelo, Enter para guardar "
				 onblur="mostrarNuevoModelo(false,1)"/>
		      </row>
		      
		      <row id="rowNumeroSerieDetSat" collapsed="false">
			<description value="Número Serie"/>
			<textbox id="NumeroSerieDetSat" value="" 
				 placeholder=" Ingrese la Serie "
				 onkeypress="return soloAlfaNumerico(event);"/>
		      </row>
		      <row>
			<description value=""/>
			<hbox>
			  <button class="btn" id="CancelarProductoDetSat" label=" Cancelar " 
				  image="img/gpos_cancelar.png" flex="1"
				  oncommand="cancelProductoDetSat()"/>
			  <button class="btn" id="agregarProductoDet" label=" Agregar" 
				  image="img/gpos_altarapida.png" flex="1"
				  oncommand="AgregarProductoDetSat()"/>
			</hbox>
		      </row>
		    </rows>
		  </grid>
		</vbox>
		<vbox id="listProductoSatDetalle" collapsed="true">
                  <caption label="Detalle:"
                  style="padding-bottom:.5em;font-size:120%"/>
		  <listbox flex="1" id="listadoProductoDetalleSat"
			   collapsed="false" style="height:10em;width:45em"
			   contextmenu="AccionesProductoDetalleSat">
		    <listcols flex="1">
		      <listcol flex="0"/>
		      <splitter class="tree-splitter" />
		      <listcol flex="2" />
		      <splitter class="tree-splitter" />
		      <listcol flex="1" />
		      <splitter class="tree-splitter" />
		      <listcol flex="0"/>
		      <splitter class="tree-splitter" />
		    </listcols>
		    <listhead>
		      <listheader label="#" style="font-style:italic;"/>
		      <listheader label="Producto" />
		      <listheader label="NS" />
		      <listheader label=""/>
		    </listhead>
		  </listbox>
		</vbox>
	      </hbox>
            </vbox>
	  </tabpanel>
        </tabpanels>
      </tabbox>
      <hbox id="rowMostrarInformacionExtraServicio" collapsed="true" pack="center">
	<description value="Pendientes:" style="color:#C91918;"/>
	<description id="MostrarInformacionExtraServicio" 
                     style="color:#C91918;"/>
      </hbox>      

      <hbox id="vboxServicioBtnDetalleSat" collapsed="false" flex="0">
	<button  flex="1" id="btnCancelServicioDet" collapsed="false"
		 style="font-weight: bold;font-size:11px;"
		 class="media btn" image="img/gpos_cancelar.png"
		 label=" Cancelar "
		 oncommand="CancelarOrdenServicioDet()" />
	<button  flex="1" id="btnAceptarServicioDet"  collapsed="false"
		 style="font-weight: bold;font-size:11px; "
		 class="media btn" image="img/gpos_aceptar.png"
		 label=" Aceptar"
		 oncommand="RegistrarOrdenServicioDet()" />
      </hbox>
    </panel>
    <!--/Formulario Orden de Servicio Detalle -->

    <!-- Formulario elije Cliente -->
    <panel id="panelElijeCliente" style="border:1px solid #aaa">

	<vbox pack="center" align="center">
	  <caption class="h1" label="<?php echo _("Clientes") ?>" />
	</vbox>
	<groupbox flex="1">
	  <caption label="Listado de clientes:" style="font-size: 13px;"/>
	  <spacer style="height:.4em"/>
	  <vbox flex="1" zalign="top" pack="center" style="overflow: auto">
	    <vbox align="left" pack="top">
	      <hbox>   
		<button  class="btn" image="img/gpos_clienteparticular.png"  style="font-size: 13px;"
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

    <!-- Resumen Orden de Servicio  -->
    <vbox class="box"  id="resumenOrdenServicio" style="padding: 0 1em 0 1em">
      <caption class="box" label="<?php echo _("Resumen Incidencias") ?>" />
      <hbox  class="resumen" pack="center" align="left">
	  <label value="Insidencias:"/>
	  <description id="TotalOrdenServicio" value="0" />
	  <label value="Pendiente:"/>
	  <description id="TotalPendiente" value=" 0 "/>
	  <label value="Ejecución:"/>
	  <description id="TotalEjecucion" value=" 0 "/>
	  <label value="Finalizado:"/>
	  <description id="TotalFinalizado" value=" 0 "/>
	  <label value="Cancelado:"/>
	  <description id="TotalCancelado" value=" 0 "/>
	  <label value="Facturado:"/>
	  <description id="TotalFacturado" value=" 0 "/>
	</hbox>
    </vbox>
    <!-- Resumen Orden de Servicio  -->

    <!-- Detalle Orden de Servicio -->
  </vbox>

  
<!-- Servicios tercerizados -->
<vbox id="boxOutsourcing" flex="1" collapsed="true">
  <spacer style="height:0.4em"/>
  <hbox align="start" pack="center" style="padding:0.8em;">
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
      <datepicker id="FechaBuscaServiciosTerceros" type="popup" />
    </vbox>
    <vbox>
      <description>Hasta:</description>
      <datepicker id="FechaBuscaServiciosTercerosHasta" type="popup"/>
    </vbox>
    <vbox>
      <description>Código Comprobante:</description>
      <textbox id="TicketListaServicios" onkeypress="return soloAlfaNumericoCodigoTPV(event)"/>
    </vbox>
    <vbox style="margin-top:.9em">
      <button class="btn" id="btnbuscar" label=" Buscar "  image="img/gpos_buscar.png" oncommand="ListadoSubsidiarios()"/>
    </vbox>

  </hbox>
  
  <caption class="box" label="<?php echo _("Servicios") ?>" />
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
      <caption  class="h1" 
                label="Editar Servicios Tercerizados"/>
    </hbox>

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
		<toolbarbutton label="..." oncommand="auxSubsidiarioHab()"/>
              </hbox>
	      <textbox id="tbox_subsidiario" value="" readonly="true" />
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
<button class="media btn" image="img/gpos_volver.png" label=" Volver TPV" oncommand="VerTPV()"/>
</vbox>
