
var idetallesPago         = 0;
var cIdMoneda             = 0;
var cSimbolo              = "";
var cCambioMoneda         = 0;
var cProveedor            = "";
var cDocumento            = "";
var cImpuesto             = "";
var cCodigo               = "";
var cEstado               = "";
var cIdPedido             = 0;
var cIdComprobante        = 0;
var cFormaPago            = "";
var cObs                  = "";
var cIdLocal              = 0;
var cTipoDocumento        = "";
var cImporte              = 0;
var cPendiente            = 0;
var resumenComprobante    = "";
var ilineabuscacompra     = 0;

var gIdPagoProv           = 0;
var gIdPagoDoc            = 0;
var gEstado               = "";
var gImportePte           = "";
var gCambioMoneda         = 0;
var gIdMoneda             = 0;
var gFPago                = "";
var gObs                  = "";
var gDocumento            = "";
var gPendientePlan        = 0;
var gIPendiente           = 0;
var gIPagado              = 0;
var gExistePago           = false;

var RevDet = 0;

// Opciones Busqueda avanzada
var vEstado        = true;
var vFechaPago     = true;
var vFormaPago     = true;
var vMoneda        = true;
var vUsuario       = true;
var vObservaciones = true;
var vFechaEmision  = true;
var vPercepcion    = true;
var vFlete         = true;
var dFechaRegistro = true;
var dUsuario       = true;
var dObservacion   = true;

var id = function(name) { return document.getElementById(name); }

function VerPago(){
    id("FechaBuscaPago").value = id("FechaBuscaPago").value;
    id("FechaBuscaPagoHasta").value = id("FechaBuscaPagoHasta").value;
    VaciarDetallesPago();
    VaciarBusquedaPago();
    BuscarPago();
}

//Limpieza de Box
function VaciarBusquedaPago(){
    var lista = id("busquedaPago");

    for (var i = 0; i < ilineabuscacompra; i++) { 
        kid = id("lineabuscacompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscacompra = 0;
    cIdComprobante = 0;
}
function VaciarDetallesPago(){
    var lista = id("busquedaDetallesPago");

    for (var i = 0; i < idetallesPago; i++) { 
        kid = id("detallepago_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesPago = 0;
}


//Busqueda 
function BuscarPago(){
    VolverComprobantes();
    VaciarBusquedaPago();
    VaciarDetallesPago();
    var emision     = false;

    var filtrofecha = id("FiltroFecha").value;

    emision     = (filtrofecha=='Facturacion')?true:emision;
    emision     = (filtrofecha=='Pago'    )?'Pago':emision;
    
    var desde   = id("FechaBuscaPago").value;
    var hasta   = id("FechaBuscaPagoHasta").value;
    var nombre  = id("NombreProveedorBusqueda").value;

    var filtrodocumento = id("FiltroPagoDocumento").value;
    var filtrocompra    = "Todos";
    var filtropago      = id("FiltroPago").value;
    var filtromoneda    = id("FiltroPagoMoneda").value;
    var filtrolocal     = id("FiltroPagoLocal").value;
    var filtrocodigo    = id("busquedaCodigoSeriep").value;
    var forzaid         = (filtrocodigo != '' )?filtrocodigo:false;
    var filtroformapago = id("FiltroFormaPago").value;

    RawBuscarComprobante(desde,hasta,emision,nombre,filtrodocumento,filtrocompra,
			 filtrolocal,filtromoneda,forzaid,filtropago,filtroformapago,
			 AddLineaComprobante);
    if(forzaid) buscarPorCodigo(filtrocodigo);
    var idex      = id("busquedaPago").selectedItem;
    var IdComprobanteProv = (idex)? idex.value:false;

    (idex)? BuscarDetallesPago(IdComprobanteProv):false;
}

function buscarPorCodigo(elemento){
    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaPago");
    n = lista.itemCount;
 
    if(n==0) return; 

    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
	x=i+1;
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[1].getAttribute('label');
        //cadena = cadena.toUpperCase();
        //if(cadena.indexOf(busca) != -1){
	if( busca == cadena ){
            lista.selectItem(texto2);
            RevisarPagoSeleccionada();
            return;
        }
    }
    //alert('gPOS:\n   - El codigo " '+elemento+' " no esta la lista.');
    //id("busquedaCodigoSerie").value='';
}

function RawBuscarComprobante(desde,hasta,emision,nombre,filtrodocumento,filtrocompra,
			      filtrolocal,filtromoneda,forzaid,filtropago,
			      filtroformapago,FuncionProcesaLinea){
    var z   = null;
    var url = "../../services.php?modo=mostrarCompra&desde=" + escape(desde)
        + "&hasta=" + escape(hasta)
        + "&nombre=" + trim(nombre)
        + "&emision=" + escape(emision)
	+ "&filtrodocumento=" + escape(filtrodocumento)
        + "&filtrocompra=" + escape(filtrocompra)
        + "&filtromoneda=" + escape(filtromoneda)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&filtropago=" + escape(filtropago)
        + "&filtroespagos=Pagos"
        + "&filtroformapago="+escape(filtroformapago)
        + "&forzaid=" + forzaid;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try {
	obj.send(null);
    } catch(z){
	return;
    }

    var tex = "";
    var cr = "\n";
    var item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidoDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,IdLocal,ImpuestoVenta,IdAlmacenRecepcion,EstadoPago,IdProveedor,TipoDocumento,IdMotivoAlbaran,ImportePago,ImporteFlete;
    var node,t,i; 
    var totalPago = 0;
    var totalPagoPendiente = 0;
    var ImporteTotalPago = 0;
    var nroPendiente = 0;
    var nroPedido = 0;
    var nroRecibido = 0;
    var nroCancelado = 0;
    var nrototalcompra = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC = item;
    var totalImporte = 0;
    var timpuImporte = 0;
    var tpendImporte = 0;
    var tpercImporte = 0;
    var timpuImporte = 0;
    var nroPendiente = 0;
    var nroEmpezada  = 0;
    var nroVencida   = 0;
    var nroPagada    = 0;
    var nroAlbaran   = 0;
    var nroAlbaranInt= 0;
    var nroExonerado = 0;
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            Almacen 	     = node.childNodes[t++].firstChild.nodeValue;
            Proveedor 	     = node.childNodes[t++].firstChild.nodeValue;
            Codigo 	     = node.childNodes[t++].firstChild.nodeValue;
            Documento 	     = node.childNodes[t++].firstChild.nodeValue;
            Registro 	     = node.childNodes[t++].firstChild.nodeValue;
            Emision 	     = node.childNodes[t++].firstChild.nodeValue;
            Pago 	     = node.childNodes[t++].firstChild.nodeValue;
            Impuesto 	     = node.childNodes[t++].firstChild.nodeValue;
            Percepcion 	     = node.childNodes[t++].firstChild.nodeValue;
            Simbolo 	     = node.childNodes[t++].firstChild.nodeValue;
            ImporteBase      = node.childNodes[t++].firstChild.nodeValue;
            ImporteImpuesto  = node.childNodes[t++].firstChild.nodeValue;
            TotalImporte     = node.childNodes[t++].firstChild.nodeValue;
            ImportePendiente = node.childNodes[t++].firstChild.nodeValue;
            ImportePercepcion= node.childNodes[t++].firstChild.nodeValue;
            ModoPago   	     = node.childNodes[t++].firstChild.nodeValue;
            Estado           = node.childNodes[t++].firstChild.nodeValue;
            Usuario          = node.childNodes[t++].firstChild.nodeValue;
            CambioMoneda     = node.childNodes[t++].firstChild.nodeValue;
            FechaCambioMoneda= node.childNodes[t++].firstChild.nodeValue;
            IdPedidosDetalle = node.childNodes[t++].firstChild.nodeValue;
            IdPedido 	     = node.childNodes[t++].firstChild.nodeValue;
            IdComprobanteProv= node.childNodes[t++].firstChild.nodeValue;
            IdMoneda         = node.childNodes[t++].firstChild.nodeValue;
            IdOrdenCompra    = node.childNodes[t++].firstChild.nodeValue;
            Observaciones    = node.childNodes[t++].firstChild.nodeValue;
            IdLocal          = node.childNodes[t++].firstChild.nodeValue;
            ImpuestoVenta    = node.childNodes[t++].firstChild.nodeValue;
            IdAlmacenRecepcion    = node.childNodes[t++].firstChild.nodeValue;
	    IdProveedor      = node.childNodes[t++].firstChild.nodeValue;
            EstadoPago       = node.childNodes[t++].firstChild.nodeValue;
	    IdMotivoAlbaran  = node.childNodes[t++].firstChild.nodeValue;
            ImporteFlete     = node.childNodes[t++].firstChild.nodeValue;
            ImportePago      = node.childNodes[t++].firstChild.nodeValue;
	    TipoDocumento    = node.childNodes[t++].firstChild.nodeValue;

	    if (Documento == 'Albaran')    nroAlbaran++; 
 	    if (Documento == 'AlbaranInt') nroAlbaranInt++; 
	
	    //Consolidado basico
	    sldoc = ( Documento == 'Albaran'   )?true:false;
	    sldoc = ( Documento == 'AlbaranInt')?true:sldoc;
	    sldoc = ( Estado    == 'Cancelada' )?true:sldoc;
	    if ( EstadoPago == 'Exonerado') nroExonerado++;
	    if ( EstadoPago == 'Vencida') nroVencida++;
	    if (!sldoc && EstadoPago != 'Exonerado')
	    {
 		if (EstadoPago == 'Pendiente'  ) nroPendiente++; 
 		if (EstadoPago == 'Empezada' ) nroEmpezada++; 
		if (EstadoPago == 'Pagada') nroPagada++; 
		if (IdMoneda == 1 || filtromoneda == 'todo1')
		{
		    totalImporte = parseFloat(totalImporte)+parseFloat(ImportePago);
		    timpuImporte = parseFloat(timpuImporte)+parseFloat(ImporteImpuesto);
		    tpendImporte = parseFloat(tpendImporte)+parseFloat(ImportePendiente);
		    tpercImporte = parseFloat(tpercImporte)+parseFloat(ImportePercepcion);
		}
		if (IdMoneda != 1 && filtromoneda != 'todo1')
		{
		    totalImporte = parseFloat(totalImporte)+parseFloat(ImportePago*CambioMoneda);
		    timpuImporte = parseFloat(timpuImporte)+parseFloat(ImporteImpuesto*CambioMoneda);
		    tpendImporte = parseFloat(tpendImporte)+parseFloat(ImportePendiente*CambioMoneda);
		    tpercImporte = parseFloat(tpercImporte)+parseFloat(ImportePercepcion*CambioMoneda);
		}
	    }

            FuncionProcesaLinea(item,Almacen,Proveedor,Codigo,Documento,
				Registro,Emision,Pago,Impuesto,Percepcion,
				Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,
				ImportePendiente,ImportePercepcion,ModoPago,Estado,
				Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,
				IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,
				Observaciones,IdLocal,ImpuestoVenta,EstadoPago,IdProveedor,
				TipoDocumento,ImportePago,ImporteFlete);
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    id("TotalFactura").value=parseFloat(tC)-parseFloat(nroAlbaran+nroAlbaranInt+nroExonerado);
    id("TotalFacturaePendiente").value = nroPendiente;
    id("TotalFacturaeEmpezada").value  = nroEmpezada;
    id("TotalFacturaePagada").value    = nroPagada;
    id("TotalFacturaeVencida").value   = nroVencida;
    id("TotalImporte").value           = cMoneda[1]['S']+" "+formatDinero(totalImporte);
    id("TotalPendiente").value         = cMoneda[1]['S']+" "+formatDinero(tpendImporte);
    id("TotalImpuesto").value          = cMoneda[1]['S']+" "+formatDinero(timpuImporte);
    id("TotalPercepcion").value        = cMoneda[1]['S']+" "+formatDinero(tpercImporte);
}

function AddLineaComprobante(item,Almacen,Proveedor,Codigo,Documento,
			     Registro,Emision,Pago,Impuesto,Percepcion,
			     Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,
			     ImportePendiente,ImportePercepcion,ModoPago,Estado,
			     Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,
			     IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,
			     Observaciones,IdLocal,ImpuestoVenta,EstadoPago,IdProveedor,
			     TipoDocumento,ImportePago,ImporteFlete){

    var lista = id("busquedaPago");
    var xitem,xnumitem,xAlmacen,xCodigo,XDocumento,xProveedor,xRegistro,xEmision,xPago,xModoPago,xImpuesto,xTotal,xPercepcion,xEstado,xUsuario,xObservaciones,xIdPedido,xIdLocal,xImpuestoVenta,xEstadoPago,xIdComprobanteProv,xIdProveedor,xFlete;
    var lobs = (Observaciones ==' ')?'':'...';

    var vPago   = (Pago)? Pago.split("~"):'';
    var xlPago  = (Pago)? vPago[0]:'';
    var xvPago  = (Pago)? vPago[1]:'';

    var vEmision   = (Emision)? Emision.split("~"):'';
    var xlEmision  = (Emision)? vEmision[0]:'';
    var xvEmision  = (Emision)? vEmision[1]:'';

    xitem = document.createElement("listitem");
    xitem.value = IdPedido;
    xitem.setAttribute("id","lineabuscacompra_"+ilineabuscacompra);
    xitem.setAttribute("oncontextmenu","seleccionarlineacompra("+ilineabuscacompra+",false)");
    ilineabuscacompra++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xAlmacen = document.createElement("listcell");
    xAlmacen.setAttribute("label",Almacen);
    xAlmacen.setAttribute("style","text-align:left");

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",Codigo);
    xCodigo.setAttribute("style","text-align:left;font-weight:bold;");
    xCodigo.setAttribute("id","codigo_"+IdPedido);

    xIdPedido = document.createElement("listcell");
    xIdPedido.setAttribute("value",IdPedido);
    xIdPedido.setAttribute("collapsed","true");
    xIdPedido.setAttribute("id","idpedido_"+Documento+"_"+Codigo);

    xImpuestoVenta = document.createElement("listcell");
    xImpuestoVenta.setAttribute("value",ImpuestoVenta);
    xImpuestoVenta.setAttribute("collapsed","true");
    xImpuestoVenta.setAttribute("id","impuestoventa_"+IdPedido);

    xIdLocal = document.createElement("listcell");
    xIdLocal.setAttribute("value",IdLocal);
    xIdLocal.setAttribute("collapsed","true");
    xIdLocal.setAttribute("id","idlocal_"+IdPedido);

    xIdPedidoDetalle = document.createElement("listcell");
    xIdPedidoDetalle.setAttribute("value",IdPedidosDetalle);
    xIdPedidoDetalle.setAttribute("collapsed","true");
    xIdPedidoDetalle.setAttribute("id","idpedidosdetalle_"+IdPedido);

    xIdComprobanteProv = document.createElement("listcell");
    xIdComprobanteProv.setAttribute("value",IdComprobanteProv);
    xIdComprobanteProv.setAttribute("collapsed","true");
    xIdComprobanteProv.setAttribute("id","idcomprobante_"+IdPedido);

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value",IdMoneda);
    xIdMoneda.setAttribute("label",Simbolo);
    xIdMoneda.setAttribute("collapsed","true");
    xIdMoneda.setAttribute("id","idmoneda_"+IdPedido);

    xCambioMoneda = document.createElement("listcell");
    xCambioMoneda.setAttribute("value",CambioMoneda);
    xCambioMoneda.setAttribute("collapsed","true");
    xCambioMoneda.setAttribute("id","cambiomoneda_"+IdPedido);

    xDocumento = document.createElement("listcell");
    xDocumento.setAttribute("label",Documento);
    xDocumento.setAttribute("value",TipoDocumento);
    xDocumento.setAttribute("style","text-align:left;font-weight:bold;");
    xDocumento.setAttribute("id","documento_"+IdPedido);

    xProveedor = document.createElement("listcell");
    xProveedor.setAttribute("label",Proveedor);
    xProveedor.setAttribute("value",IdProveedor);
    xProveedor.setAttribute("style","text-align:left;");
    xProveedor.setAttribute("id","proveedor_"+IdPedido);

    xRegistro = document.createElement("listcell");
    xRegistro.setAttribute("label", Registro);
    xRegistro.setAttribute("style","text-align:center");

    xEmision = document.createElement("listcell");
    xEmision.setAttribute("label", xlEmision);
    xEmision.setAttribute("collapsed",vFechaEmision);
    xEmision.setAttribute("style","text-align:center;");

    xPago = document.createElement("listcell");
    xPago.setAttribute("label", xlPago);	
    xPago.setAttribute("style","text-align:center;font-weight:bold;");
    xPago.setAttribute("id","fpagoprov_"+IdPedido);

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("style","text-align:center");
    xModoPago.setAttribute("id","modopago_"+IdPedido);

    xImpuesto = document.createElement("listcell");
    xImpuesto.setAttribute("label", Simbolo+' '+formatDinero(ImporteImpuesto));
    xImpuesto.setAttribute("style","text-align:right;");

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", Simbolo+' '+formatDinero(TotalImporte));
    xImporte.setAttribute("style","text-align:right;font-weight:bold; ");
    xImporte.setAttribute("value",TotalImporte);
    xImporte.setAttribute("id","importeneto_"+IdPedido);

    xTotal = document.createElement("listcell");
    xTotal.setAttribute("label", Simbolo+' '+formatDinero(ImportePago));
    xTotal.setAttribute("style","text-align:right;font-weight:bold; ");
    xTotal.setAttribute("value",ImportePago);
    xTotal.setAttribute("id","importe_"+IdPedido);

    xPendiente = document.createElement("listcell");
    xPendiente.setAttribute("label", Simbolo+' '+formatDinero(ImportePendiente));
    xPendiente.setAttribute("style","text-align:right;font-weight:bold; ");
    xPendiente.setAttribute("value",ImportePendiente);
    xPendiente.setAttribute("id","pendiente_"+IdPedido);

    xPercepcion = document.createElement("listcell");
    xPercepcion.setAttribute("label", Simbolo+' '+formatDinero(ImportePercepcion));
    xPercepcion.setAttribute("collapsed",vPercepcion);
    xPercepcion.setAttribute("style","text-align:right");
    xPercepcion.setAttribute("value",Percepcion);
    xPercepcion.setAttribute("id","percepcion_"+IdPedido);

    xFlete = document.createElement("listcell");
    xFlete.setAttribute("label", Simbolo+' '+formatDinero(ImporteFlete));
    xFlete.setAttribute("collapsed",vFlete);
    xFlete.setAttribute("style","text-align:right");
    xFlete.setAttribute("value",ImporteFlete);
    xFlete.setAttribute("id","flete_"+IdPedido);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", Estado);
    xEstado.setAttribute("style","text-align:left;");
    xEstado.setAttribute("id","estado_"+IdPedido);

    xEstadoPago = document.createElement("listcell");
    xEstadoPago.setAttribute("label", EstadoPago);
    xEstadoPago.setAttribute("style","text-align:left;font-weight:bold;");
    xEstadoPago.setAttribute("id","estado_"+IdPedido);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed", vUsuario);
    xUsuario.setAttribute("style","text-align:center;");


    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label", lobs);
    xObservaciones.setAttribute("value",Observaciones );
    xObservaciones.setAttribute("collapsed",vObservaciones);
    xObservaciones.setAttribute("id","obs_"+IdPedido);
    xObservaciones.setAttribute("style","text-align:center");
    xObservaciones.setAttribute("crop", "end");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xDocumento );
    xitem.appendChild( xProveedor );
    xitem.appendChild( xEstadoPago );
    xitem.appendChild( xModoPago );	
    xitem.appendChild( xEmision );
    xitem.appendChild( xPago );
    xitem.appendChild( xImpuesto );	
    xitem.appendChild( xPercepcion );
    xitem.appendChild( xFlete );
    xitem.appendChild( xImporte );
    xitem.appendChild( xTotal );
    xitem.appendChild( xPendiente );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xIdPedido );
    xitem.appendChild( xIdPedidoDetalle );
    xitem.appendChild( xIdComprobanteProv);
    xitem.appendChild( xIdMoneda );
    xitem.appendChild( xCambioMoneda );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xImpuestoVenta );
    lista.appendChild( xitem );		
}

function seleccionarlineacompra(linea,xval){
    var lista = (xval)? id("busquedaDetallesPago"):id("busquedaPago");
    var fila  = (xval)? id("detallepago_"+linea):id("lineabuscacompra_"+linea);
    lista.selectItem(fila);
}

function RevisarPagoSeleccionada(){

    var idex      = id("busquedaPago").selectedItem;

    if(!idex) return;
    cIdPedido     = idex.value;
    cBMoneda      = id("FiltroPagoMoneda").getAttribute("value");
    cIdMoneda     = (cBMoneda=='todoSol')?'0':id("idmoneda_"+idex.value).getAttribute("value");
    cCambioMoneda = id("cambiomoneda_"+idex.value).getAttribute("value");
    cProveedor    = id("proveedor_"+idex.value).getAttribute("label");
    cIdProveedor  = id("proveedor_"+idex.value).getAttribute("value");
    cDocumento    = id("documento_"+idex.value).getAttribute("label");
    cTipoDocumento= id("documento_"+idex.value).getAttribute("value");
    cImpuesto     = id("impuestoventa_"+idex.value).getAttribute("value");
    cCodigo       = id("codigo_"+idex.value).getAttribute("label");
    cEstado       = id("estado_"+idex.value).getAttribute("label");
    cImporte      = id("importe_"+idex.value).getAttribute("value");
    cPendiente    = id("pendiente_"+idex.value).getAttribute("value");
    cFPago        = id("fpagoprov_"+idex.value).getAttribute("label");
    cObs          = id("obs_"+idex.value).getAttribute("label");

    NCImporte     = id("importe_"+idex.value).getAttribute("value");
    NCPendiente   = id("pendiente_"+idex.value).getAttribute("value");
    cIdComprobante= id("idcomprobante_"+idex.value).getAttribute("value");

    cIdMoneda     = id("idmoneda_"+idex.value).getAttribute("value");
    cSimbolo      = id("idmoneda_"+idex.value).getAttribute("label");
    cFormaPago    = id("modopago_"+idex.value).getAttribute("label");
    cIdLocal      = id("idlocal_"+idex.value).getAttribute("value");

    resumenComprobante = cDocumento+' '+cCodigo+' '+cProveedor;

    var verdet = (RevDet == 0 || RevDet != idex.value)? true:false;
    if(verdet || idetallesPago == 0)
        setTimeout("loadDetallesPago('"+cIdComprobante+"')",100);

    RevDet = idex.value;
    xmenuPagos('cbte');
}

function loadDetallesPago(xid){
    VaciarDetallesPago();
    //BuscarDetallesPago(xid);
    if(cEsPagoComprobanteProv) BuscarDetallesPago(xid);
    else BuscarDetallesCompra(xid);
} 

function BuscarDetallesPago(IdComprobanteProv){
    if(IdComprobanteProv > 0)
	RawBuscarDetallesPago(IdComprobanteProv, AddLineaDetallesPago);

}

function RawBuscarDetallesPago(IdComprobanteProv,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var z   = null;
    var url = "modpagoscobros.php?modo=mostrarDetallesPago&IdComprobanteProv="+IdComprobanteProv;
    obj.open("GET",url,false);

    try {
	obj.send(null);
    } catch(z){
	return;
    }

    var tex = "";
    var cr = "\n";
    var item,Documento,Estado,ModoPago,FRegistro,FPago,ImportePago,ImporteMora,IdComprobante,IdPagoProv,IdPagoProvDoc,IdMoneda,Observacion,Usuario,Simbolo,FOperacion,ImpValuacion,Excedente,EstadoCuota,esPlanificado;
    var node,t,i;
    var numitem = 0;
    var Simbolo = "";
    var IPendiente = 0;
    var IMora      = 0;
    var IExcedente = 0;
    var IPagado    = 0;
    var tPendiente = 0;
    var tPagado    = 0;
    var IValuacion = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC   = item;
    var numitem = 0;
    gExistePago = (item == 0 )? true:false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
	    numitem++;
	    if(node.childNodes[t].firstChild){
            Documento    = node.childNodes[t++].firstChild.nodeValue;
            Estado       = node.childNodes[t++].firstChild.nodeValue;
            ModoPago     = node.childNodes[t++].firstChild.nodeValue;
            FRegistro    = node.childNodes[t++].firstChild.nodeValue;
            FPago        = node.childNodes[t++].firstChild.nodeValue;
            IdMoneda     = node.childNodes[t++].firstChild.nodeValue;
            Simbolo      = node.childNodes[t++].firstChild.nodeValue;
            ImportePago  = node.childNodes[t++].firstChild.nodeValue;
            ImporteMora  = node.childNodes[t++].firstChild.nodeValue;
	    Excedente    = node.childNodes[t++].firstChild.nodeValue;
            Usuario      = node.childNodes[t++].firstChild.nodeValue;
            IdPagoProv   = node.childNodes[t++].firstChild.nodeValue;
            IdPagoProvDoc= node.childNodes[t++].firstChild.nodeValue;
            IdComprobante= node.childNodes[t++].firstChild.nodeValue;
	    FOperacion   = node.childNodes[t++].firstChild.nodeValue;
	    ImpValuacion = node.childNodes[t++].firstChild.nodeValue;
	    Observacion  = node.childNodes[t++].firstChild.nodeValue;
	    EstadoCuota  = node.childNodes[t++].firstChild.nodeValue;
	    esPlanificado= node.childNodes[t++].firstChild.nodeValue;

	    if(Estado == 'Pendiente'){
		IPendiente  = parseFloat(IPendiente) + parseFloat(ImportePago);
		tPendiente++;
	    }
	    else{
		IPagado  = parseFloat(IPagado) + parseFloat(ImportePago);
		tPagado++;
	    }

            IMora      = (ImporteMora!=0)?parseFloat(IMora) + parseFloat(ImporteMora):IMora;
            IExcedente = (Excedente!=0)?parseFloat(IExcedente) + parseFloat(Excedente):IExcedente;
	    IValuacion = parseFloat(IValuacion) + parseFloat(ImpValuacion);

            FuncionRecogerDetalles(numitem,Documento,Estado,ModoPago,FRegistro,FPago,IdMoneda,
				   Simbolo,ImportePago,ImporteMora,IdPagoProv,IdPagoProvDoc,
				   IdComprobante,Observacion,Usuario,FOperacion,ImpValuacion,
				   Excedente,EstadoCuota,esPlanificado);
            //item--;
	    }
        }
    }
    gPendientePlan = IPendiente;
    gIPagado       = IPagado;
/*
    id("TotalPendientes").value    = tPendiente;
    id("TotalConfirmada").value    = tPagado;
    id("ImportePagada").value      = Simbolo+formatDinero(IPagado-IMora-IExcedente);
    id("ImportePendiente").value   = Simbolo+formatDinero(IPendiente);
    id("ImporteMora").value        = Simbolo+formatDinero(IMora);
    id("ImporteExcedente").value   = Simbolo+formatDinero(IExcedente);
*/
}

function AddLineaDetallesPago(numitem,Documento,Estado,ModoPago,FRegistro,FPago,IdMoneda,
			      Simbolo,ImportePago,ImporteMora,IdPagoProv,IdPagoProvDoc,
			      IdComprobante,Observacion,Usuario,FOperacion,ImpValuacion,
			      Excedente,EstadoCuota,esPlanificado){

    var lista = id("busquedaDetallesPago");
    var xitem,xnumitem,xDocumento,xEstado,xModoPago,xFRegistro,xFPago,xIdMoneda,xSimbolo,xImportePago,xImporteMora,xIdPagoProv,xIdPagoProvDoc,xIdComprobante,xObservacion,xUsuario,xFOperacion,xCambioMonedaDoc,xExcedente,xEstadoCuota,xesPlanificado;
    
    if(FPago != ' '){
	var FechaPago   = (FPago)?FPago.split("~"):'';
	var vFechaPago  = (FPago)?FechaPago[0]:'';
	var oFechaPago  = (FPago)?FechaPago[1]:'';
    }else{
	var vFechaPago  = FPago;
	var oFechaPago  = FPago;
    }
    FOperacion          = (FOperacion==' ')?false:FOperacion;
    FechaOperacion      = (FOperacion)?FOperacion.split("~"):'';
    FOperacion          = (FOperacion)?FechaOperacion[0]:'';
    var CambioMonedaDoc = (FOperacion)?FechaOperacion[1]:'';
    //var CambioMoneda    = (CambioMonedaDoc!=1)?CambioMonedaDoc:'';
    var CambioMoneda    = CambioMonedaDoc;

    ImpMora = (ImporteMora == 0)?' ':Simbolo+' '+formatDinero(ImporteMora);
    ImpExce = (Excedente == 0)?' ':Simbolo+' '+formatDinero(Excedente);
    var lobs = (Observacion ==' ')?'':'...';   

    var vEstadoCuota = (EstadoCuota == 'Vencido')? EstadoCuota:' ';

    xitem    = document.createElement("listitem");
    xitem.value = IdPagoProv;
    xitem.setAttribute("id","detallepago_" + idetallesPago);
    xitem.setAttribute("oncontextmenu","seleccionarlineacompra("+idetallesPago+",true)");
    idetallesPago++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xDocumento = document.createElement("listcell");
    xDocumento.setAttribute("label", Documento);
    xDocumento.setAttribute("id","p_documento_"+IdPagoProv);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", Estado);
    xEstado.setAttribute("style","font-weight:bold;");
    xEstado.setAttribute("id","p_estado_"+IdPagoProv);

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("id","p_modo_"+IdPagoProv);

    xFRegistro = document.createElement("listcell");
    xFRegistro.setAttribute("label", FRegistro);
    xFRegistro.setAttribute("collapsed",dFechaRegistro);
    xFRegistro.setAttribute("id","p_fregistro_"+IdPagoProv);

    xFPago = document.createElement("listcell");
    xFPago.setAttribute("label", vFechaPago);
    xFPago.setAttribute("value", oFechaPago);
    xFPago.setAttribute("style","font-weight:bold;");
    xFPago.setAttribute("id","p_fpago_"+IdPagoProv);

    xFOperacion = document.createElement("listcell");
    xFOperacion.setAttribute("label", FOperacion);
    xFOperacion.setAttribute("style","font-weight:bold;");
    xFOperacion.setAttribute("id","p_foperacion_"+IdPagoProv);

    xImportePago = document.createElement("listcell");
    xImportePago.setAttribute("label", Simbolo+' '+formatDinero(ImportePago));
    xImportePago.setAttribute("style","font-weight:bold;text-align:right");
    xImportePago.setAttribute("value",ImportePago);
    xImportePago.setAttribute("id","p_importe_"+IdPagoProv);

    xCambioMoneda = document.createElement("listcell");
    xCambioMoneda.setAttribute("value", CambioMoneda);
    //xCambioMoneda.setAttribute("style","font-weight:bold;text-align:right");
    xCambioMoneda.setAttribute("collapsed", true);
    xCambioMoneda.setAttribute("id", "p_cambiomoneda_"+IdPagoProv);

    xImporteMora = document.createElement("listcell");
    xImporteMora.setAttribute("label", ImpMora);
    xImporteMora.setAttribute("value", ImporteMora);
    xImporteMora.setAttribute("style","font-weight:bold;text-align:right");
    xImporteMora.setAttribute("id","p_mora_"+IdPagoProv);

    xExcedente = document.createElement("listcell");
    xExcedente.setAttribute("label", ImpExce);
    xExcedente.setAttribute("value", Excedente);
    xExcedente.setAttribute("style","font-weight:bold;text-align:right");
    xExcedente.setAttribute("collapsed", true);
    xExcedente.setAttribute("id","p_excedente_"+IdPagoProv);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed",dUsuario);
    xUsuario.setAttribute("style","text-align:center");
    xUsuario.setAttribute("id","p_usuario_"+IdPagoProv);

    xObservacion = document.createElement("listcell");
    xObservacion.setAttribute("label", lobs);
    xObservacion.setAttribute("value", Observacion);
    xObservacion.setAttribute("collapsed", dObservacion);
    xObservacion.setAttribute("id","p_observacion_"+IdPagoProv);

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value", IdMoneda);
    xIdMoneda.setAttribute("collapsed", true);
    xIdMoneda.setAttribute("id","p_idmoneda_"+IdPagoProv);

    xIdPagoProvDoc = document.createElement("listcell");
    xIdPagoProvDoc.setAttribute("value", IdPagoProvDoc);
    xIdPagoProvDoc.setAttribute("collapsed", true);
    xIdPagoProvDoc.setAttribute("id","p_idpagodoc_"+IdPagoProv);

    xIdPagoProv = document.createElement("listcell");
    xIdPagoProv.setAttribute("value", IdPagoProv);
    xIdPagoProv.setAttribute("collapsed", true);
    xIdPagoProv.setAttribute("id","p_idpagoprov_"+IdPagoProv);

    xEstadoCuota = document.createElement("listcell");
    xEstadoCuota.setAttribute("label",vEstadoCuota);
    xEstadoCuota.setAttribute("value",EstadoCuota);
    xEstadoCuota.setAttribute("style","text-align:center");
    xEstadoCuota.setAttribute("id","p_estadocuota_"+IdPagoProv);

    xesPlanificado = document.createElement("listcell");
    xesPlanificado.setAttribute("value",esPlanificado);
    xesPlanificado.setAttribute("collapsed", true);
    xesPlanificado.setAttribute("id","p_esplanifcado_"+IdPagoProv);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xDocumento );
    xitem.appendChild( xEstado );
    xitem.appendChild( xModoPago );
    xitem.appendChild( xFRegistro );
    xitem.appendChild( xFPago );
    xitem.appendChild( xFOperacion);
    xitem.appendChild( xImportePago );
    xitem.appendChild( xImporteMora );
    //xitem.appendChild( xExcedente );
    xitem.appendChild( xEstadoCuota );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservacion );	
    xitem.appendChild( xIdMoneda );	
    xitem.appendChild( xIdPagoProvDoc );
    xitem.appendChild( xIdPagoProv );
    xitem.appendChild( xCambioMoneda );

    xitem.appendChild( xesPlanificado );

    lista.appendChild( xitem );
}

function NuevoPago(){

    var idex = id("busquedaPago").selectedItem;
    if(!idex) return;

    if(cEstado == "Exonerado")
	return;

    var resComprobante     = resumenComprobante.toUpperCase();
    var idcomprobante      = cIdComprobante;
    var idproveedor        = cIdProveedor;
    var importecomprobante = cImporte;
    var importependiente   = cPendiente;
    var idmoneda           = cIdMoneda;
    var cambiomoneda       = cCambioMoneda;
    var formapago          = cFormaPago;
    var pendienteplan      = gPendientePlan;
    var estadocbte         = cEstado;
    var simbolomoneda      = cSimbolo;
    var codigo             = cCodigo;
    var local              = cIdLocal;
    var tipodoc            = cTipoDocumento;

    if(cEstado == 'Pagada')
	return alert("gPOS: "+cDocumento.toUpperCase()+"  Finalizado");

    if(cFormaPago == 'Contado' && cFPago == ' ')
	return alert("gPOS: Debe registrar Fecha de Pago del Comprobante \n\n          "+resumenComprobante);

    CargarFormularioPago(idcomprobante,idproveedor,resComprobante,importecomprobante,
			 importependiente,idmoneda,cambiomoneda,formapago,pendienteplan,
			 estadocbte,simbolomoneda,codigo,local,tipodoc);
}

function CargarFormularioPago(idcomprobante,idproveedor,resComprobante,importecomprobante,
			      importependiente,idmoneda,cambiomoneda,formapago,pendienteplan,
			      estadocbte,simbolomoneda,codigo,local,tipodoc){

    var url  = "selpagos.php?id="+idcomprobante+"&"+
	       "idprov="+idproveedor+"&"+
	       "resumencbte="+resComprobante+"&"+
  	       "importecbte="+importecomprobante+"&"+
  	       "importepte="+importependiente+"&"+
  	       "idm="+idmoneda+"&"+
  	       "cambiomda="+cambiomoneda+"&"+
  	       "formapago="+formapago+"&"+
  	       "pteplan="+pendienteplan+"&"+
  	       "estadocbte="+estadocbte+"&"+
  	       "simbmoneda="+simbolomoneda+"&"+
  	       "codigo="+codigo+"&"+
  	       "local="+local+"&"+
  	       "tipodoc="+tipodoc+"&"+
	       "modo=add";

    var boxframe  = document.getElementById("webDetPago");
    var listcomp  = document.getElementById("listboxComprobantesPagos");
    var boxns     = document.getElementById("boxDetPago");

    boxframe.setAttribute("src",url);  
    listcomp.setAttribute("collapsed","true");  
    boxns.setAttribute("collapsed","false");
    //id("btnIrPagosProveedor").setAttribute("collapsed",true);
    //id("boxResumenComprobanteCompra").setAttribute("collapsed",true);
    id("boxComprobantesCompras").setAttribute("collapsed",true);
}

function VolverComprobantes(){

    var listcomp  = document.getElementById("listboxComprobantesPagos");
    var boxns     = document.getElementById("boxDetPago");

    listcomp.setAttribute("collapsed","false");  
    boxns.setAttribute("collapsed","true");
}

function RevisarDetallePago(){
    var idex = id("busquedaDetallesPago").selectedItem;
    if(!idex) return;

    gIdPagoProv           = id("p_idpagoprov_"+idex.value).getAttribute("value");
    gIdPagoDoc            = id("p_idpagodoc_"+idex.value).getAttribute("value");
    gEstado               = id("p_estado_"+idex.value).getAttribute("label");
    gImporte              = id("p_importe_"+idex.value).getAttribute("value");
    gIdMoneda             = id("p_idmoneda_"+idex.value).getAttribute("value");
    gFPago                = id("p_fpago_"+idex.value).getAttribute("value");
    gObs                  = id("p_observacion_"+idex.value).getAttribute("value");
    glObs                 = id("p_observacion_"+idex.value).getAttribute("label");
    gDocumento            = id("p_documento_"+idex.value).getAttribute("label");
    gMora                 = id("p_mora_"+idex.value).getAttribute("value");
    //gExceso               = id("p_excedente_"+idex.value).getAttribute("value");
    gFechaPago            = id("p_fpago_"+idex.value).getAttribute("value");
    gCambioMoneda         = id("p_cambiomoneda_"+idex.value).getAttribute("value");

    xmenuPagos('pago');
}

function EditarPago(xop){
    var idex = id("busquedaDetallesPago").selectedItem;
    if(!idex) return;

    var resComprobante     = resumenComprobante.toUpperCase();
    var idcomprobante      = cIdComprobante;
    var idproveedor        = cIdProveedor;
    var importecomprobante = cImporte;
    var importependiente   = cPendiente;
    var idmoneda           = gIdMoneda;
    var monedacomprobante  = cIdMoneda;
    var cambiocomprobante  = cCambioMoneda;
    var importepago        = gImporte;
    var estado             = gEstado;
    var idpagodoc          = gIdPagoDoc;
    var documento          = gDocumento;
    var obs                = gObs;
    var fpago              = gFechaPago;
    var mora               = gMora;
    var excedente          = 0;//gExceso;
    var idpagoprov         = idex.value;
    var estadocbte         = cEstado;
    var simbolomoneda      = cSimbolo;
    var codigo             = cCodigo;
    var pendienteplan      = gPendientePlan;
    var tipodoc            = cTipoDocumento;
    var local              = cIdLocal;

    CargarEditaPago(idcomprobante,idproveedor,resComprobante,importecomprobante,
		    importependiente,idmoneda,importepago,idpagodoc,documento,obs,fpago,
		    mora,estado,idpagoprov,monedacomprobante,cambiocomprobante,estadocbte,
		    simbolomoneda,codigo,xop,pendienteplan,excedente,tipodoc,local);
}

function CargarEditaPago(idcomprobante,idproveedor,resComprobante,importecomprobante,
			 importependiente,idmoneda,importepago,idpagodoc,documento,obs,
			 fpago, mora,estado,idpagoprov,monedacomprobante,cambiocomprobante,
			 estadocbte,simbolomoneda,codigo,xop,pendienteplan,excedente,
			 tipodoc,local){

    var url  = "selpagos.php?id="+idcomprobante+"&"+
	       "idprov="+idproveedor+"&"+
	       "resumencbte="+resComprobante+"&"+
  	       "importecbte="+importecomprobante+"&"+
  	       "importepte="+importependiente+"&"+
  	       "idm="+idmoneda+"&"+
  	       "monedacbte="+monedacomprobante+"&"+
  	       "cambiocbte="+cambiocomprobante+"&"+
  	       "importep="+importepago+"&"+
  	       "idpd="+idpagodoc+"&"+
  	       "doc="+documento+"&"+
  	       "obs="+obs+"&"+
  	       "fpago="+fpago+"&"+
  	       "mora="+mora+"&"+
  	       "estado="+estado+"&"+
  	       "idpp="+idpagoprov+"&"+
  	       "estadocbte="+estadocbte+"&"+
  	       "simbmoneda="+simbolomoneda+"&"+
	       "codigo="+codigo+"&"+
  	       "pteplan="+pendienteplan+"&"+
	       "xop="+xop+"&"+
	       "exceso="+excedente+"&"+
  	       "tipodoc="+tipodoc+"&"+
  	       "local="+local+"&"+
	       "modo=edit";

    var boxframe  = document.getElementById("webDetPago");
    var listcomp  = document.getElementById("listboxComprobantesPagos");
    var boxns     = document.getElementById("boxDetPago");

    boxframe.setAttribute("src",url);  
    listcomp.setAttribute("collapsed","true");  
    boxns.setAttribute("collapsed","false");

}

function actualizaEstadoImportePendiente(CbtePendiente,CbteEstado){
    var idx       = cIdPedido;
    var pendiente = cSimbolo+' '+formatDinero(CbtePendiente);
    id("estado_"+idx).setAttribute("label",CbteEstado);
    id("pendiente_"+idx).setAttribute("value",CbtePendiente);
    id("pendiente_"+idx).setAttribute("label",pendiente);
}

function VerObsComprobante(xdoc){
    var xid       = (xdoc=='comprobante')? "busquedaPago":"busquedaDetallesPago";
    var idex      = id(xid).selectedItem;
    if(!idex) return;

    var idx       = idex.value;//IdCompraDet:false 
    var codigo    = cCodigo;
    var estado    = (xdoc=='comprobante')? cEstado:gEstado;
    var documento = (xdoc=='comprobante')? cDocumento:gDocumento;
    var xobs      = (xdoc=='comprobante')? id("obs_"+idx).getAttribute('value'):gObs;

    //Items?
    var xrest     = (xobs != ' ')?false:true;
    var aobs;
    //Sin Item
    if(xrest) xobs = '\n\n                               '+
	             '- Sin observaciones - ';
    //Item 
    if(!xrest){
	aobs = xobs.split('-');
	xobs = '';
	for(x in aobs){
	    if(x != 0 && aobs[x]!='' ) 
		xobs += '\n        -'+aobs[x];
	}
    }
    //Termina
    if(xdoc == 'pago')
	var msj = 'gPOS:\n\n'+
		 ' Observaciones de '+documento+' -'+estado+'-\n'+
		 '     '+cDocumento+' '+codigo+' '+cProveedor+'\n'+
		 '     '+xobs+' \n ';
    else
	var msj = 'gPOS:\n\n'+
		 ' Observaciones de '+documento+' -'+estado+'-\n'+
		 '     Nro. '+codigo+' '+cProveedor+'\n'+
		 '     '+xobs+' \n ';
    return alert(msj);

}

function xmenuPagos(op){ 
    switch(op){
    case 'cbte':
	var tpagado   = parseFloat(gPendientePlan) + parseFloat(gIPagado);
	var nuevopago = (tpagado >= NCImporte || cEstado == 'Pagada' || cEstado=='Exonerado')? true:false ;
	var obs       = (!cObs)? true:false;
	break;
    case 'pago':
	var DetObs    = (!glObs)? true:false;
	var verdoc    = (gEstado == 'Pendiente')? true:false;
	var PagoEdit  = (gEstado == 'Confirmado')? true:false;
	break;
    }
    
    id("mheadNuevoPago").setAttribute('disabled',nuevopago);
    id("mheadImprimir").setAttribute('disabled',gExistePago);
    id("mheadObs").setAttribute('disabled',obs);
    id("mheadDetObs").setAttribute('disabled',DetObs);
    id("mheadDocumento").setAttribute('disabled',verdoc);
    id("mheadEditar").setAttribute('disabled',PagoEdit);
}

function ImprimirPagoSeleccionada(){
    var idex = id("busquedaPago").selectedItem;
    if(!idex) return;

    var idoc          = idex.value;
    var importe       = id("importe_"+idoc).getAttribute("value");
    var moneda        = id("idmoneda_"+idoc).getAttribute("value");
    var importeletras = convertirNumLetras(importe,moneda);
    importeletras     = importeletras.toUpperCase();
    var url           = "../fpdf/imprimir_pagos.php?idoc="+idoc+
                        "&totaletras="+importeletras;

    location.href=url;

}

function verDocumento(){
    var idex = id("busquedaDetallesPago").selectedItem;
    if(!idex) return;
    var idpagodoc = gIdPagoDoc;
    var url = "modpagoscobros.php?modo=mostrarDocumento&iddoc=" + escape(idpagodoc);

    var obj = new XMLHttpRequest();
    var z   = null;
    obj.open("GET",url,false);

    try {
	obj.send(null);
    } catch(z){
	return;
    }

    var item,Local,Pedido,Proveedor,FechaRegistro,FechaOperacion,ModalidadPago,Estado,Simbolo,Importe,EntidadFinanciera,CodOperacion,CtaEmpresa,CtaProveedor,NumDocumento,Usuario,CambioMoneda,IdPagoProvDoc,IdMoneda,IdOrdenCompra,IdModalidadPago,IdProveedor,Observaciones,Codigo,IdLocal,TipoProveedor;
    var node,t,i; 
 
    if (!obj.responseXML)
       return alert(po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            Local 	     = node.childNodes[t++].firstChild.nodeValue;
	    Pedido           = node.childNodes[t++].firstChild.nodeValue;
            Proveedor 	     = node.childNodes[t++].firstChild.nodeValue;
            FechaRegistro    = node.childNodes[t++].firstChild.nodeValue;
            FechaOperacion   = node.childNodes[t++].firstChild.nodeValue;
            ModalidadPago    = node.childNodes[t++].firstChild.nodeValue;
            Estado 	     = node.childNodes[t++].firstChild.nodeValue;
            Simbolo 	     = node.childNodes[t++].firstChild.nodeValue;
            Importe 	     = node.childNodes[t++].firstChild.nodeValue;
            CodOperacion     = node.childNodes[t++].firstChild.nodeValue;
            CtaEmpresa       = node.childNodes[t++].firstChild.nodeValue;
            CtaProveedor     = node.childNodes[t++].firstChild.nodeValue;
            NumDocumento     = node.childNodes[t++].firstChild.nodeValue;
            Usuario          = node.childNodes[t++].firstChild.nodeValue;
            CambioMoneda     = node.childNodes[t++].firstChild.nodeValue;
            IdPagoProvDoc    = node.childNodes[t++].firstChild.nodeValue;
            IdMoneda         = node.childNodes[t++].firstChild.nodeValue;
            IdOrdenCompra    = node.childNodes[t++].firstChild.nodeValue;
            IdModalidadPago  = node.childNodes[t++].firstChild.nodeValue;
            IdProveedor      = node.childNodes[t++].firstChild.nodeValue;
            Codigo           = node.childNodes[t++].firstChild.nodeValue;
            IdLocal          = node.childNodes[t++].firstChild.nodeValue;
	    TipoProveedor    = node.childNodes[t++].firstChild.nodeValue;
            Observaciones    = node.childNodes[t++].firstChild.nodeValue;

	    item--;
        }
    }

    var fAllOperacion = (FechaOperacion)?FechaOperacion.split("~"):'';
    var vFOperacion   = (FechaOperacion)?fAllOperacion[0]:'';
    var lImporte      = "Importe : ";
    var CambioMoneda  = "Cambio: "+CambioMoneda;
    var Importe       = Simbolo+parseFloat(Importe).toFixed(2);
    var Importe       = lImporte+Importe;

    var aCtaProv = (CtaProveedor != " ")? CtaProveedor.split("~"):false;
    Financiera   = (aCtaProv)? aCtaProv[3]:" ";
    CtaProveedor = (aCtaProv)? aCtaProv[2]:" ";

    var aCtaEmp = (CtaEmpresa != " ")? CtaEmpresa.split("~"):false;
    CtaEmpresa  = (aCtaEmp)? aCtaEmp[2]+" "+aCtaEmp[3]:" ";

    var printpd = "";
    printpd += "gPOS: Constancia de Pago   "+((Financiera != ' ')?  "- "+Financiera+" -":'')+" \n\n";

    printpd += (ModalidadPago)? "Modalidad Pago  : "+ModalidadPago+"\n":'';
    printpd += (FechaOperacion)?"Fecha Operacion : "+vFOperacion+"\n":'';
    printpd += (CodOperacion != ' ')?"Número Operacion : "+CodOperacion+"\n":'';
    printpd += (NumDocumento != ' ')?"Número Documento : "+NumDocumento+"\n":'';
    printpd += (CtaEmpresa != ' ')?"Cuenta Empresa : "+CtaEmpresa+"\n":'';
    printpd += (CtaProveedor != ' ')?"Cuenta Proveedor : "+CtaProveedor+"\n":'';
    printpd += (Proveedor)?"Proveedor : "+Proveedor+"\n":'';
    printpd += (IdMoneda == '1')?Importe+"\n":Importe+ "   "+CambioMoneda+"\n";
    printpd += (Observaciones != ' ')?"Observaciones : "+Observaciones:'';
    alert(printpd);

}

function ModificarPago(xval){
    var idex = id("busquedaDetallesPago").selectedItem;
    if(!idex) return;
    var idpagodoc     = gIdPagoDoc;
    var Eliminar      = 1;
    var CbtePendiente = 0;
    var CbteEstado    = "";
    var ImporteRet    = parseFloat(gImporte)+parseFloat(gMora);

    var url = "modpagoscobros.php?modo=ModificaPago&iddoc=" + escape(idpagodoc)+
	"&xdoc="+xval+
	"&idcbte="+cIdComprobante+
	"&idpprov="+gIdPagoProv+
	"&xgimporte="+ImporteRet+
	"&xeliminar="+Eliminar;

    var msj = "Va eliminar el pago";
    if(!confirm('gPOS: '+msj+',\n'+' ¿desea continuar?'))
	return;

    var obj = new XMLHttpRequest();
    var z   = null;
    obj.open("GET",url,false);

    try {
	obj.send(null);
    } catch(z){
	return;
    }
    
    if(gEstado == 'Confirmado'){

	if(cIdMoneda == gIdMoneda)
	    CbtePendiente = parseFloat(cPendiente) + parseFloat(gImporte) - parseFloat(gMora);// - parseFloat(gExceso);

	if(cIdMoneda == 1 && gIdMoneda != 1){
	    CbtePendiente = parseFloat(cPendiente) + parseFloat(gImporte*gCambioMoneda) - parseFloat(gMora);
	}

	if(cIdMoneda != 1 && gIdMoneda == 1){
	    CbtePendiente = parseFloat(cPendiente) + parseFloat(gImporte/gCambioMoneda) - parseFloat(gMora);
	}

	CbteEstado = (CbtePendiente.toFixed(2) >= parseFloat(cImporte))? 'Pendiente':'Empezada';
	CbtePendiente = CbtePendiente.toFixed(1);
	ActualizarPendienteComprobante(cIdComprobante,xdoc='Eliminar',gIdPagoDoc);
	ActualizarEstadoPagoDoc(gIdPagoDoc,Estado='Pendiente');
	//(aPagoDoc)? ActualizarEstadoPagoDoc(aPagoProv.IdPagoDoc,'Pendiente'):false;
	actualizaEstadoImportePendiente(CbtePendiente,CbteEstado);
    }

    loadDetallesPago(cIdComprobante);
}

function ActualizarPendienteComprobante(cIdComprobante,xdoc,gIdPagoDoc){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ActualizaPendienteComprobante";
    var data     = "";
    var res;
    var estadodoc= (cEstado == 'Pendiente' || cPendiente == gImporte)? 'Confirmado':"";

    data = data + "&xidcp="+cIdComprobante;
    data = data + "&xtcbte="+ cImporte;
    data = data + "&xedc="+ estadodoc;
    data = data + "&xdoc="+ xdoc;
    data = data + "&xidpd="+ gIdPagoDoc;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        //NOTA: posiblemente no tenemos conexion.
        res = false;	
    }

    if(res == 'completa') return alert("gPOS \n El Comprobante ya está Pagada");

    if(!parseInt(res)) 
	alert(po_servidorocupado+'\n\n -'+res+'-');	
    
}

function ActualizarEstadoPagoDoc(idpagodoc,estado){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ActualizaEstadoPagoDoc";
    var data     = "";
    var res;
    var estado   = (!estado)? 'Confirmado':estado;

    data = data + "&xidppd="+idpagodoc;
    data = data + "&xestado="+ estado;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        //NOTA: posiblemente no tenemos conexion.
        res = false;	
    }

    if(!parseInt(res)) 
	alert(po_servidorocupado+'\n\n -'+res+'-');	   
}

function mostrarOperacionesPagosProveedor(xop){
    var xpago        = true;
    var xcomprobante = true;

    var xsrcpago = "modpagoscobros.php?modo=verPagosProveedorDocComprobantes";
    switch(xop){
    case 'pagos':
	xpago = false;

	break;
    case 'comprobantes':
	xcomprobante = false;
	break;
    }

    id("boxPagoProveedor").setAttribute("collapsed",xpago);
    if( id("webPagoProveedor").getAttribute("src") != xsrcpago)
	id("webPagoProveedor").setAttribute("src",xsrcpago);
    else
	if(xop=="pagos") setTimeout("webPagoProveedor.BuscarPagoDocumento()",400);
    id("boxComprobantesCompras").setAttribute("collapsed",xcomprobante);
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.value.replace(/ /g,"_");
    var esdet    = xlabel.split("-");

    switch(xlabel){
    case "Estado": 
	vEstado        = xchecked;
	break;
    case "Fecha_Pago":
	vFechaEntrga   = xchecked;
	break;
    case "Forma_Pago":
	vFormaPago     = xchecked;
	break;
    case "Moneda" : 
	vMoneda        = xchecked;
	break;
    case "Usuario":
	vUsuario       = xchecked;
	break;
    case "Observacion" :
	vObservaciones = xchecked;
	break;
    case "Fecha_Emision":
	vFechaEmision  = xchecked;
	break;
    case "Percepcion" : 
	vPercepcion    = xchecked;
	break;
    case "Flete" : 
	vFlete         = xchecked;
	break;
    case "-_Fecha_Registro":
	dFechaRegistro = xchecked;
	break;
    case "-_Usuario":
	dUsuario       = xchecked;
	break;
    case "-_Observacion":
	dObservacion   = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);

    (!esdet[1])? BuscarPago():loadDetallesPago(cIdComprobante);
}

function mostrarComprobantes(xval){

    var xrpagos  = true;
    var xrcobros = true;

    switch(xval){
    case 'rPagos':
	BuscarPago();
	xrpagos = false;
	break;
    case 'rCobros':
	xrcobros = false;
	BuscarVentas();
	break;	
    }

    id("boxPagoProveedor").setAttribute("collapsed",true);
    id("vboxbusquedapago").setAttribute("collapsed",xrpagos);
    id("vboxbusquedacobros").setAttribute("collapsed",xrcobros);

    document.getElementById("listboxComprobantesPagos").setAttribute("collapsed","false");
    document.getElementById("boxDetPago").setAttribute("collapsed","true");

}

var cEsPagoComprobanteProv = true;
var idetallesCompra = 0;
function mostrarDetalleComprobanteProv(xval){
    switch(xval){
      case 'comprobante':
	var cbte = false;
	var pago = true;
	cEsPagoComprobanteProv = false;
	break;
      case 'pago':
	var cbte = true;
	var pago = false;
	cEsPagoComprobanteProv = true;
	break;	
    }

    var xtitle = (cbte)? "Detalle Pagos":"Detalle Comprobantes";
    id("busquedaDetallesPago").setAttribute("collapsed",pago);
    //id("boxResumenComprobanteCompra").setAttribute("collapsed",pago);
    id("busquedaDetallesCompra").setAttribute("collapsed",cbte);
    
    id("t_detalle_pago").setAttribute("checked",cbte);
    id("onlistDetalle").setAttribute("label",xtitle);
    id("t_detalle_cbte").setAttribute("checked",pago);

    var idex = id("busquedaPago").selectedItem;
    if(!idex) return;
    
    if(!cbte) setTimeout("BuscarDetallesCompra("+cIdComprobante+")",0);
    if(!pago) setTimeout("BuscarDetallesPago("+cIdComprobante+")",0);
}

function BuscarDetallesCompra(IdPedido ){
    VaciarDetallesCompra();
    RawBuscarDetallesCompra(IdPedido, AddLineaDetallesCompra);

}

function RawBuscarDetallesCompra(IdPedido,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var filtromoneda    = id("FiltroPagoMoneda").value;
    var url = "../../services.php?modo=mostrarDetallesCompra&IdPedido="+IdPedido+
              "&filtromoneda="+filtromoneda;
    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia, Nombre,Talla, Color, Unidades, Descuento, PV, IdAlbaran,VentaMenudeo,Contenedor,UndContenedor,UndMedida;
    var node,t,i;
    var numitem = 0;
    if (!obj.responseXML) return alert(po_servidorocupado);		

    var xml = obj.responseXML.documentElement;
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node && node.childNodes && node.childNodes.length >0){
            t = 0;
	    numitem++;
            if (node.childNodes[t].firstChild){

                Referencia   = node.childNodes[t++].firstChild.nodeValue;
                IdProducto   = node.childNodes[t++].firstChild.nodeValue;
                CodigoBarras = node.childNodes[t++].firstChild.nodeValue;
                Producto     = node.childNodes[t++].firstChild.nodeValue;
                Cantidad     = node.childNodes[t++].firstChild.nodeValue;
                Costo        = node.childNodes[t++].firstChild.nodeValue;
                Precio       = node.childNodes[t++].firstChild.nodeValue;
                Descuento    = node.childNodes[t++].firstChild.nodeValue;
                Importe      = node.childNodes[t++].firstChild.nodeValue;
                LT           = node.childNodes[t++].firstChild.nodeValue;
                FV           = node.childNodes[t++].firstChild.nodeValue;
                NS           = node.childNodes[t++].firstChild.nodeValue;
		IdPedidoDet  = node.childNodes[t++].firstChild.nodeValue;
		IdPedido     = node.childNodes[t++].firstChild.nodeValue;
		VentaMenudeo = node.childNodes[t++].firstChild.nodeValue;
		Contenedor   = node.childNodes[t++].firstChild.nodeValue;
		UndContenedor= node.childNodes[t++].firstChild.nodeValue;
		UndMedida    = node.childNodes[t++].firstChild.nodeValue;

                FuncionRecogerDetalles(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,Precio,Descuento,Importe,
				       LT,FV,NS,IdPedidoDet,IdPedido,VentaMenudeo,
				       Contenedor,UndContenedor,UndMedida);
            }
        }
    }
}

function AddLineaDetallesCompra(numitem,Referencia,IdProducto,CodigoBarras,
				Producto,Cantidad,Costo,Precio,Descuento,Importe,
				LT,FV,NS,IdPedidoDet,IdPedido,VentaMenudeo,
				Contenedor,UndContenedor,UndMedida){

    var xitem,xnumitem,xReferencia,xIdProducto,xCodigoBarras,xProducto,xCantidad,xCosto,xPrecio,xDescuento,xImporte,xIdPedido,cResto,tUnidad,xclass;
    var lista   = id("busquedaDetallesCompra");
    var Detalle = '';
    var arFV    = (FV != ' ')? FV.split("~"):'';
    var lFV     = (FV != ' ')? arFV[0]:' ';
    var vFV     = (FV != ' ')? arFV[1]:' ';

    Detalle    += (LT !=' ')?' Lt. '+LT :'';
    Detalle    += (lFV !=' ')?' F.V. '+lFV :'';
    Detalle    += (NS !='0')?' NS ' :'';
   
    Costo       = (cIdMoneda=='2')?Costo/cCambioMoneda:Costo;
    Importe     = (cIdMoneda=='0')?Importe*cCambioMoneda:Importe;
    Descuento   = (cIdMoneda=='0')?Descuento*cCambioMoneda:Descuento;
    Precio      = (cIdMoneda=='0')?Precio*cCambioMoneda:Precio;

    //Cantidad
    cResto      = (Cantidad%UndContenedor==Cantidad)?Cantidad:Cantidad%UndContenedor;
    tCantidad   = ( VentaMenudeo=='1' )? Cantidad-cResto:Cantidad;
    tCantidad   = ( VentaMenudeo=='1' )? Math.floor(tCantidad/UndContenedor):Cantidad;
    cResto      = ' + '+cResto;
    tCantidad   = ( VentaMenudeo=='1' )? tCantidad+' '+Contenedor+''+cResto:Cantidad;
    tUnidad     = UndMedida;
    tCantidad   = tCantidad+' '+tUnidad;
    Detalle     =  ( VentaMenudeo=='1' )? Detalle+' '+UndContenedor+''+tUnidad+'x'+Contenedor:Detalle;
    xclass      = (numitem%2)?'parrow':'imparrow';  
    xitem       = document.createElement("listitem");
    xitem.value = IdPedidoDet;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","detallecompra_" + idetallesCompra);
    idetallesCompra++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xReferencia = document.createElement("listcell");
    xReferencia.setAttribute("label", Referencia);
    xReferencia.setAttribute("style","font-weight:bold;");

    xCodigoBarras = document.createElement("listcell");
    xCodigoBarras.setAttribute("label", CodigoBarras);
    xCodigoBarras.setAttribute("id","cb_"+IdPedidoDet);

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label", Producto);
    xProducto.setAttribute("style","font-weight:bold;");
    xProducto.setAttribute("id","producto_"+IdPedidoDet);
    xProducto.setAttribute("value", IdProducto);
    
    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label", Detalle);
    xDetalle.setAttribute("id","detalle_"+IdPedidoDet);

    xCantidad = document.createElement("listcell");
    xCantidad.setAttribute("label", tCantidad);
    xCantidad.setAttribute("value", Cantidad);
    xCantidad.setAttribute("style","font-weight:bold;");
    xCantidad.setAttribute("id","cantidad_"+IdPedidoDet);


    xCosto = document.createElement("listcell");
    xCosto.setAttribute("label", formatDinero(Costo));
    xCosto.setAttribute("style","text-align:right");
    xCosto.setAttribute("id","costo_"+IdPedidoDet);

    xVV = document.createElement("listcell");
    xVV.setAttribute("label", parseFloat(Costo*Cantidad).toFixed(2));
    xVV.setAttribute("style","text-align:right;");
    xVV.setAttribute("id","valorventa_"+IdPedidoDet);

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label", parseFloat(Precio).toFixed(2));
    xPrecio.setAttribute("style","text-align:right;font-weight:bold;");
    xPrecio.setAttribute("id","precio_"+IdPedidoDet);

    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label", parseFloat(Descuento).toFixed(2));
    xDescuento.setAttribute("style","text-align:right");
    xDescuento.setAttribute("id","descuento_"+IdPedidoDet);

    xPV = document.createElement("listcell");
    xPV.setAttribute("label", parseFloat(Importe).toFixed(2));
    xPV.setAttribute("style","text-align:right;font-weight:bold;");
    xPV.setAttribute("id","precioventa_"+IdPedidoDet);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", parseFloat(Importe).toFixed(2));
    xImporte.setAttribute("style","text-align:right;font-weight:bold;");

    xIdPedido = document.createElement("listcell");
    xIdPedido.setAttribute("value", IdPedido);
    xIdPedido.setAttribute("collapsed","true");
    xIdPedido.setAttribute("id","pedido_"+IdPedidoDet);

    xLote = document.createElement("listcell");
    xLote.setAttribute("value", LT);
    xLote.setAttribute("collapsed","true");
    xLote.setAttribute("id","lote_"+IdPedidoDet);

    xVencimiento = document.createElement("listcell");
    xVencimiento.setAttribute("value", vFV);
    xVencimiento.setAttribute("collapsed","true");
    xVencimiento.setAttribute("id","vencimiento_"+IdPedidoDet);

    //xIdProducto = document.createElement("listcell");
    //xIdProducto.setAttribute("collapsed","true");
    //xIdProducto.setAttribute("value", IdProducto);
    //xIdProducto.setAttribute("id","idproducto_"+IdPedidoDet);

    xNS = document.createElement("listcell");
    xNS.setAttribute("collapsed","true");
    xNS.setAttribute("value", NS);
    xNS.setAttribute("id","ns_"+IdPedidoDet);
    xitem.appendChild( xnumitem );
    xitem.appendChild( xReferencia );
    xitem.appendChild( xCodigoBarras );
    xitem.appendChild( xProducto );
    xitem.appendChild( xDetalle );
    xitem.appendChild( xCantidad );
    xitem.appendChild( xCosto );
    xitem.appendChild( xPrecio );
    xitem.appendChild( xDescuento );
    xitem.appendChild( xVV );	
    xitem.appendChild( xPV );	
    xitem.appendChild( xIdPedido );
    xitem.appendChild( xLote );
    xitem.appendChild( xVencimiento );
    //xitem.appendChild( xIdProducto );
    xitem.appendChild( xNS );
    lista.appendChild( xitem );
}

function VaciarDetallesCompra(){
    var lista = id("busquedaDetallesCompra");

    for (var i = 0; i < idetallesCompra; i++) { 
        kid = id("detallecompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesCompra = 0;
}
