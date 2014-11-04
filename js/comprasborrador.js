
var idetallesCompra  = 0;
var idfacturaseleccionada = 0;
var nrodocumentodevol     = 0;
var seriedocumentodevol   = 0;
var idcomprobantedevol    = 0;
var seriecomprobantedevol = "";
var IdPedido              = 0;
var ProveedorPost         = false;
var IdProveedorPost       = 0;
var cIdMoneda             = 0;
var cCambioMoneda         = 0;
var cProveedor            = "";
var cAlmacen              = "";
var cIdLocal              = 0;
var cDocumento            = "";
var cImpuesto             = "";
var cCodigo               = "";
var cModopago             = "";
var cEstado               = "";
var cEmision              = "";
var cObservacion          = "";
var cPercepcion           = "";
var clPercepcion          = "";
var clFlete               = "";
var cPago                 = "";
var cIdPedido             = 0;
var cObs                  = "";
var ilineabuscacompra     = 0;
var Vistas = new Object(); 
Vistas.ventas = 7;

var RevDet = 0;

// Opciones Busqueda avanzada
var vEstado        = true;
var vFechaFacturacion   = true;
var vFormaPago     = true;
var vMoneda        = true;
var vLocal         = true;
var vUsuario       = true;
var vObservaciones = true;
var vFechaRegistro = true;
var vPercepcion    = true;
var vFlete         = true;

var id = function(name) { return document.getElementById(name); }

function VerCompra(){
    id("FechaBuscaCompra").value = id("FechaBuscaCompra").value;
    id("FechaBuscaCompraHasta").value = id("FechaBuscaCompraHasta").value;	
    VaciarDetallesCompra();
    VaciarBusquedaCompra();
    BuscarCompra();
}

//Limpieza de Box
function VaciarBusquedaCompra(){
    var lista = id("busquedaCompra");

    for (var i = 0; i < ilineabuscacompra; i++) { 
        kid = id("lineabuscacompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscacompra = 0;
}
function VaciarDetallesCompra(){
    var lista = id("busquedaDetallesCompra");

    for (var i = 0; i < idetallesCompra; i++) { 
        kid = id("detallecompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesCompra = 0;
}

//Busqueda 
function BuscarCompra(){

    VaciarBusquedaCompra();
    volverComprobantes(0);
    var emision = id("FechaBuscaCompraEmision").selected;
    var desde   = id("FechaBuscaCompra").value;
    var hasta   = id("FechaBuscaCompraHasta").value;
    var nombre  = id("NombreProveedorBusqueda").value;

    if ((!hasta || hasta == "DD-MM-AAAA") &&  (!desde || desde == "DD-MM-AAAA") && (!nombre))return;

    var modocontado     = (id("modoConsultaCompraContado").checked)?"contado":"todos";
    var modocredito     = (id("modoConsultaCompraCredito").checked)?"credito":"todos";
    var filtrodocumento = id("FiltroCompraDocumento").value;
    var filtrocompra    = id("FiltroCompra").value;
    var filtromoneda    = id("FiltroCompraMoneda").value;
    var filtrolocal     = id("FiltroCompraLocal").value;
    var filtrocodigo    = id("busquedaCodigoSerie").value
    var forzaid         = (filtrocodigo != '' )?filtrocodigo:false;
    RawBuscarCompra(desde,hasta,emision,nombre,modocontado,modocredito,filtrodocumento,filtrocompra,filtrolocal,filtromoneda,forzaid,AddLineaCompra);

}

function buscarPorPedido(elemento){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaCompra");
    n = lista.itemCount;
    if(n==0) return; 
    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
	x=i+1;
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[16].getAttribute('value');

	if( busca == cadena )
	{
            lista.selectItem(texto2);
            RevisarCompraSeleccionada();
            return;
        }
    }

    alert('gPOS:\n   - El código " '+elemento+' " no está en la lista.');
}

function RawBuscarCompra(desde,hasta,emision,nombre,modocontado,modocredito,filtrodocumento,filtrocompra,filtrolocal,filtromoneda,forzaid,FuncionProcesaLinea){

    var url = "services.php?modo=mostrarCompra&desde=" + escape(desde)
        //+ "&modoconsulta=" + escape(modo)
        + "&hasta=" + escape(hasta)
        + "&nombre=" + escape(nombre)
        + "&emision=" + escape(emision)
        + "&modocontado=" + escape(modocontado)
        + "&modocredito=" + escape(modocredito)
	+ "&filtrodocumento=" + escape(filtrodocumento)
        + "&filtrocompra=" + escape(filtrocompra)
        + "&filtromoneda=" + escape(filtromoneda)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&filtropago=Todos"
        + "&filtroespagos=Comprobantes"
        + "&forzaid=" + forzaid;
    
    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidoDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,IdLocal,ImpuestoVenta,ImporteFlete,IdAlmacenRecepcion,IdProveedor,EstadoPago,IdMotivoAlbaran,ImpuestoVenta,TipoDocumento;
    var node,t,i,codcompra; 
    var totalCompra    = 0;
    var totalCompraPendiente = 0;
    var ImporteTotalCompra   = 0;
    var nroPendiente   = 0;
    var nroPedido      = 0;
    var nroRecibido    = 0;
    var nroCancelado   = 0;
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
    var nroBorrador  = 0;
    var nroConfirmado= 0;
    var nroCancelado = 0;
    var nroPendiente = 0;
    var nroAlbaran   = 0;
    var nroAlbaranInt= 0;
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
	    if ( Estado == 'Cancelada') nroCancelado++;
	    if (!sldoc)
	    {
 		if (Estado == 'Borrador'  ) nroBorrador++; 
 		if (Estado == 'Pendiente' ) nroPendiente++; 
		if (Estado == 'Confirmado') nroConfirmado++; 
		if (IdMoneda == 1 || filtromoneda == 'todo1')
		{
		    totalImporte = parseFloat(totalImporte)+parseFloat(TotalImporte);
		    timpuImporte = parseFloat(timpuImporte)+parseFloat(ImporteImpuesto);
		    tpendImporte = parseFloat(tpendImporte)+parseFloat(ImportePendiente);
		    tpercImporte = parseFloat(tpercImporte)+parseFloat(ImportePercepcion);
		}
		if (IdMoneda != 1 && filtromoneda != 'todo1')
		{
		    totalImporte = parseFloat(totalImporte)+parseFloat(TotalImporte*CambioMoneda);
		    timpuImporte = parseFloat(timpuImporte)+parseFloat(ImporteImpuesto*CambioMoneda);
		    tpendImporte = parseFloat(tpendImporte)+parseFloat(ImportePendiente*CambioMoneda);
		    tpercImporte = parseFloat(tpercImporte)+parseFloat(ImportePercepcion*CambioMoneda);
		}
	    }

            FuncionProcesaLinea(item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,Observaciones,IdLocal,ImpuestoVenta,ImporteFlete,ImportePago);		
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    id("TotalCompra").value            = parseFloat(tC)-parseFloat(nroAlbaran+nroAlbaranInt);
    id("TotalCompraeBorrador").value   = nroBorrador;
    id("TotalCompraePendiente").value  = nroPendiente;
    id("TotalCompraeConfirmada").value = nroConfirmado;
    id("TotalCompraeCancelada").value  = nroCancelado;
    id("TotalImporte").value           = cMoneda[1]['S']+' '+formatDinero(totalImporte);
    id("TotalPercepcion").value        = cMoneda[1]['S']+' '+formatDinero(tpercImporte);
}

function AddLineaCompra(item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,Observaciones,IdLocal,ImpuestoVenta,ImporteFlete,ImportePago){

    var lista = id("busquedaCompra");
    var xitem,xnumitem,xAlmacen,xCodigo,XDocumento,xProveedor,xRegistro,xEmision,xPago,xModoPago,xBase,xImpuesto,xTotal,xPercepcion,xFlete,xEstado,xUsuario,xObservaciones,xIdPedido,xIdLocal,xImpuestoVenta,xTotalPagar;
    var lobs = (Observaciones ==' ')?'':'...';

    var vPago   = (Pago)? Pago.split("~"):'';
    var xlPago  = (Pago)? vPago[0]:'';
    var xvPago  = (Pago)? vPago[1]:'';
    var TotalPagarImporte = 0;
    var vEmision  = (Emision)? Emision.split("~"):'';
    var xlEmision = (Emision)? vEmision[0]:'';
    var xvEmision = (Emision)? vEmision[1]:'';
    var vDocumento = Documento.split(" - ");

    xclass        = (item%2)?'parrow':'imparrow';      
    xitem         = document.createElement("listitem");
    xitem.value   = IdPedido;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","lineabuscacompra_"+ilineabuscacompra);
    ilineabuscacompra++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xAlmacen = document.createElement("listcell");
    xAlmacen.setAttribute("label",Almacen);
    xAlmacen.setAttribute("style","text-align:left");
    xAlmacen.setAttribute("id","almacen_"+IdPedido);

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",Codigo);
    xCodigo.setAttribute("style","text-align:center;font-weight:bold;");
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

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value",IdMoneda);
    xIdMoneda.setAttribute("collapsed","true");
    xIdMoneda.setAttribute("id","idmoneda_"+IdPedido);

    xMoneda = document.createElement("listcell");
    xMoneda.setAttribute("value",Simbolo);
    xMoneda.setAttribute("collapsed","true");
    xMoneda.setAttribute("id","moneda_"+IdPedido);

    xCambioMoneda = document.createElement("listcell");
    xCambioMoneda.setAttribute("value",CambioMoneda);
    xCambioMoneda.setAttribute("collapsed","true");
    xCambioMoneda.setAttribute("id","cambiomoneda_"+IdPedido);

    xDocumento = document.createElement("listcell");
    xDocumento.setAttribute("label",Documento);
    xDocumento.setAttribute("value",vDocumento[0]);
    xDocumento.setAttribute("style","text-align:left;font-weight:bold;");
    xDocumento.setAttribute("id","documento_"+IdPedido);

    xProveedor = document.createElement("listcell");
    xProveedor.setAttribute("label",Proveedor);
    xProveedor.setAttribute("style","text-align:left;font-weight:bold;");
    xProveedor.setAttribute("id","proveedor_"+IdPedido);

    xRegistro = document.createElement("listcell");
    xRegistro.setAttribute("label", Registro);
    xRegistro.setAttribute("collapsed",vFechaRegistro);
    xRegistro.setAttribute("style","text-align:center");

    xEmision = document.createElement("listcell");
    xEmision.setAttribute("label", xlEmision);
    xEmision.setAttribute("value", xvEmision);
    xEmision.setAttribute("style","text-align:center;");
    xEmision.setAttribute("id","emision_"+IdPedido);

    xPago = document.createElement("listcell");
    xPago.setAttribute("label", xlPago);	
    xPago.setAttribute("value", xvPago);	
    xPago.setAttribute("style","text-align:center");
    xPago.setAttribute("id","pago_"+IdPedido);

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("collapsed",vFormaPago);
    xModoPago.setAttribute("style","text-align:center");
    xModoPago.setAttribute("id","modopago_"+IdPedido);

    xBase = document.createElement("listcell");
    xBase.setAttribute("label", Simbolo+' '+formatDinero(ImporteBase));	
    xBase.setAttribute("style","text-align:right");

    xImpuesto = document.createElement("listcell");
    xImpuesto.setAttribute("label", Simbolo+' '+formatDinero(ImporteImpuesto));	
    xImpuesto.setAttribute("style","text-align:right;");

    xTotal = document.createElement("listcell");
    xTotal.setAttribute("label", Simbolo+' '+formatDinero(TotalImporte));
    xTotal.setAttribute("style","text-align:right;font-weight:bold; ");
    xTotal.setAttribute("value",TotalImporte);
    xTotal.setAttribute("id","importe_"+IdPedido);

    xTotalPagar = document.createElement("listcell");
    xTotalPagar.setAttribute("label", Simbolo+' '+formatDinero(ImportePago));
    xTotalPagar.setAttribute("style","text-align:right;font-weight:bold; ");
    xTotalPagar.setAttribute("value",ImportePago);
    xTotalPagar.setAttribute("id","importepago_"+IdPedido);

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

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed",vUsuario);
    xUsuario.setAttribute("style","text-align:center;");


    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label", lobs);
    xObservaciones.setAttribute("value",Observaciones );
    xObservaciones.setAttribute("collapsed",vObservaciones);
    xObservaciones.setAttribute("id","obs_"+IdPedido);
    xObservaciones.setAttribute("style","text-align:center");
    xObservaciones.setAttribute("crop", "end");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xAlmacen );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xDocumento );
    xitem.appendChild( xProveedor );
    xitem.appendChild( xEstado );
    xitem.appendChild( xModoPago );	
    xitem.appendChild( xPercepcion );
    xitem.appendChild( xFlete );
    xitem.appendChild( xTotal );
    xitem.appendChild( xTotalPagar );
    xitem.appendChild( xEmision );
    xitem.appendChild( xPago );
    xitem.appendChild( xRegistro );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xIdPedido );
    xitem.appendChild( xIdPedidoDetalle );
    xitem.appendChild( xIdMoneda );
    xitem.appendChild( xMoneda );
    xitem.appendChild( xCambioMoneda );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xImpuestoVenta );
    lista.appendChild( xitem );		
}

function RevisarCompraSeleccionada(){

    var idex      = id("busquedaCompra").selectedItem;
    if(!idex) return;
    cBMoneda      = id("FiltroCompraMoneda").getAttribute("value");
    cIdMoneda     = (cBMoneda=='todoSol')?'0':id("idmoneda_"+idex.value).getAttribute("value");
    cCambioMoneda = id("cambiomoneda_"+idex.value).getAttribute("value");
    cProveedor    = id("proveedor_"+idex.value).getAttribute("label");
    cDocumento    = id("documento_"+idex.value).getAttribute("value");
    cAlmacen      = id("almacen_"+idex.value).getAttribute("label");
    cIdLocal      = id("idlocal_"+idex.value).getAttribute("value");
    cImpuesto     = id("impuestoventa_"+idex.value).getAttribute("value");
    cCodigo       = id("codigo_"+idex.value).getAttribute("label");
    cModopago     = id("modopago_"+idex.value).getAttribute("label");
    cEstado       = id("estado_"+idex.value).getAttribute("label");
    cPago         = id("pago_"+idex.value).getAttribute("value");
    cEmision      = id("emision_"+idex.value).getAttribute("value");
    cPercepcion   = id("percepcion_"+idex.value).getAttribute("value");
    clPercepcion  = id("percepcion_"+idex.value).getAttribute("label");
    clFlete       = id("flete_"+idex.value).getAttribute("label");
    cObs          = id("obs_"+idex.value).getAttribute("value");
    clPercepcion  = clPercepcion.replace(cMoneda[1]['S'],'');
    clFlete       = clFlete.replace(cMoneda[1]['S'],'');
    cIdPedido     = idex.value;

    var IdPedidos = id("idpedidosdetalle_"+idex.value).getAttribute("value");

    idfacturaseleccionada = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    var nrodocumento    =  idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    nrodocumentodevol   = nrodocumento;
    var seriedocumento  =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    seriedocumentodevol = seriedocumento;
    var cadena          =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    //ExtraBuscarEnServidor("");

    if(RevDet == 0 || RevDet != idex.value)
        setTimeout("loadDetallesCompras('"+IdPedidos+"')",100);

    RevDet = idex.value;
    xmenuCompraBorrador();
}

function loadDetallesCompras(xid){
    VaciarDetallesCompra();
    BuscarDetallesCompra(xid);
} 

function BuscarDetallesCompra(IdPedido ){

    RawBuscarDetallesCompra(IdPedido, AddLineaDetallesCompra);

}

function RawBuscarDetallesCompra(IdPedido,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var filtromoneda    = id("FiltroCompraMoneda").value;
    var url = "services.php?modo=mostrarDetallesCompra&IdPedido="+IdPedido+
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

function ImprimirCompraSeleccionada(tp){
    var idex = id("busquedaCompra").selectedItem;
    var idoc = idex.value;
    o_PrintCompra(idoc,tp);
}

function o_PrintCompra(idoc,tp){
    switch (tp) {
    case 'txt':
 	//obtenemos datos
	break;
    case 'pdf':
	//imprime pdf
	var importe = id("importepago_"+idoc).getAttribute("value");
	var moneda = id("idmoneda_"+idoc).getAttribute("value");
	var importeletras = convertirNumLetras(importe,moneda);
	importeletras     = importeletras.toUpperCase();
	var url= "modulos/fpdf/imprimir_comprobantes.php?idoc="+idoc+"&totaletras="+importeletras;
	location.href=url;
	break;
    default:
	alert("gPOS:\n\n - "+po_servidorocupado+'. Acción Desconocida.');
    }
}


function getNuevoDatoCompra(xocs,campo,cvalue,mj,mja){

    //Inicia
    var xmj = (mj)?'- Ingrese correctamente'+mja+'\n\n':'';
    var xfh  = prompt("gPOS: \n\n"+
		       " Ingrese "+campo+":\n\n"+xmj, cvalue);

    //Cancelar
    if( xfh == null) return false;//Brutal termino el proceso!!! 

    switch (xocs) {
    case 3:
 
	if( xfh == '' )
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);

	//Filtro Codigo
	xfh           = trim(xfh);
	var patron    = /^\d*$/;    
	var axfh      = xfh.split('-');
	var esCodigo  = true;

	//Filtro Numero/Caracteres
	if( xfh != '' || xfh != cCodigo || axfh.length == 2 )
	    if( patron .test(axfh[0]) && patron .test(axfh[1]) )
		if( axfh[0] !='' && axfh[1] != '' )
		    if( axfh[0].length < 4 && axfh[1].length < 8 )
			esCodigo = false;
	
	if(esCodigo)
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja); 

 	return xfh;//Termina
	break;
    case 9:
	//Consolida
	xfh  = trim(xfh);
	if( xfh == '' )
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);
	//Codigos
	var afh = xfh.split(',');

	//Codigo base
	if(afh[0]+','!=cvalue || afh[1]+','==cvalue)
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia

	if( xfh == cvalue || afh.length < 2  )
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	//Detalle Numeros
	for(var i=0; i<afh.length; i++){

	    //Abalisa codigos
	    var axfh   = afh[i].split('-');
	    var patron = /^\d*$/;    

	    if ( patron .test(axfh[0]) && patron .test(axfh[1]) && axfh.length == 2 )
	    {
		if (!(axfh[0].length < 4 && axfh[1].length < 8)) 
		    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	    }
	    else
	    {
		return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	    }
	}
 	return xfh;//Termina
	break;
    case 10:
	//Facturar

	xfh  = trim(xfh);
	if( xfh == '' )
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);
	//Codigos
	var afh = xfh.split(',');

	//Codigo base
	if(afh[0] != cvalue || afh[1] == cvalue)
	    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	//if( xfh == cvalue || afh.length < 2  )
	//   return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia

	//Detalle Numeros
	for(var i=0; i<afh.length; i++){

	    //Analisa codigos
	    var axfh   = afh[i].split('-');
	    var patron = /^\d*$/;    

	    if ( patron .test(axfh[0]) && patron .test(axfh[1]) && axfh.length == 2 )
	    {
		if (!(axfh[0].length < 4 && axfh[1].length < 8))
		    return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	    }
	    else
	    {
		return getNuevoDatoCompra(xocs,campo,cvalue,true,mja);//Inicia
	    }
	}
	
 	return xfh;//Termina
	break;
    }
}

function ModificarCompra(xocs,xdet){

    var lbox     = (xdet)?"busquedaDetallesCompra":"busquedaCompra";
    var idex     = id(lbox).selectedItem; 
    var idetx    = (xdet)?idex.value:false;//IdPedidoDet:false 
    var idx      = (xdet)?id("pedido_"+idetx).getAttribute("value"):idex.value;//IdPedido:IdPedido
    var msj      = false;
    var reload   = true;
    var reloadet = true;
    var reloadsl = false;
    var reloadpd = false;
    var xdato    = 0;
    var xret     = false;
    var amsj     = 'gPOS: ¡Acción restringida!\n\n '+
	           '-  Al modificar '+cDocumento+' Nro.'; 

    switch (xocs) {
    case 1:
	//Fecha Facturacion
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?true:false;
	if(!xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado  -Borrador- ó -Pendiente-');
	//Optiene fecha
 	xdato = id("xEmision").value;
	if(cEmision  != 'undefined')
	    if(xdato == cEmision) return;

	id("emision_"+cIdPedido).value = xdato;
	id("emision_"+cIdPedido).setAttribute("label",xdato);

	reloadet = false;
	reloadpd = true;
	volverComprobantes(0);

	msj=' Modificar '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+'\n\n '+
	    ' - Nueva Fecha de Facturación '+xdato;
	break;
    case 2:
	//Fecha Pago
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?true:false;
	if(!xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado  -Borrador- ó -Pendiente-');
	//Optiene fecha
 	xdato = id("xPago").value;
	if(cPago  != 'undefined')
	    if(xdato == cPago) return;

	id("pago_"+cIdPedido).value = xdato;
	id("pago_"+cIdPedido).setAttribute("label",xdato);

	reloadet = false;
	reloadpd = true;
	volverComprobantes(0);

	msj=' Modificar '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+'\n\n '+
	    ' - Nueva Fecha de Pago '+xdato;
	break;

    case 3:
 	//CCodigo
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;
	if(xrest) 
	{	    
	    id("xCodigo").value = cCodigo;
	    return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			 ' debe tener estado -Borrador- ó -Pendiente-');
	}

	//Carga CCodigo
	var xfh      = id("xCodigo").value;
	//Inicia
	var xmj      = 'gPOS: \n\n- Ingrese correctamente '+
	               'el Código de '+cDocumento+': Serie-Número';


	//Filtro Codigo
	xfh           = trim(xfh);
	var patron    = /^\d*$/;    
	var axfh      = xfh.split('-');
	var esCodigo  = true;

	//Filtro Numero/Caracteres
	if( xfh != '' || xfh != cCodigo || axfh.length == 2 )
	    if( patron .test(axfh[0]) && patron .test(axfh[1]) )
		if( axfh[0] !='' && axfh[1] != '' )
		    if( axfh[0].length < 4 && axfh[1].length < 8 )
			esCodigo = false;
	
	if(esCodigo) id("xCodigo").value = cCodigo;
	if(esCodigo) return alert(xmj);

	//Carga dato
	xdato     = xfh;

	reload    = false;
	reloadet  = false;
	id("codigo_"+cIdPedido).setAttribute('label',xdato);

	volverComprobantes(0);

	//Carga mesaje 
	msj=' Modificar '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+cEstado+'-\n\n '+
	    '- Nuevo Código Nro.'+xdato+' ';
	break;

    case 4:
 	//Tipo Pago
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;

	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador ó -Pendiente-');
	//Carga mesaje 
	xdato = id("modopago_"+idx).getAttribute("label");
	xdato = (xdato=='Contado')?'Credito':'Contado';

	id("modopago_"+cIdPedido).setAttribute("label",xdato);
	reload   = false;
	reloadet = false;

	volverComprobantes(0);

	msj=' Modificar  Tipo Pago '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+cEstado+
	    '-\n\n - Nuevo Tipo Pago -'+xdato+'-';
	break;

    case 5:
 	//Proveedor
	xrest = (cEstado=='Borrador'||cEstado=='Pendiente')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Lista Proveedores
	selProveedorAux();
 
	//Dato Global IdProveedor Select
	if(!ProveedorPost) return;
	if(cProveedor == ProveedorPost) return;

	xdato = IdProveedorPost;

	volverComprobantes(0);

	//Mensaje 
	msj=' Modificar el Proveedor -'+cProveedor+'- '+cDocumento+
	    ' Nro.'+cCodigo+' -'+cEstado+'-\n\n '+
	    '- Nuevo Proveedor -'+ProveedorPost+'-';
	id("proveedor_"+cIdPedido).setAttribute('label',ProveedorPost);
	id("ProvHab").value = ProveedorPost;

	//Clean
	ProveedorPost   = false;
	IdProveedorPost = 0;
	reload          = false;
	reloadet        = false;
	break;

    case 6:
 	//Observaciones
	xrest = (cEstado=='Pagada'||cEstado=='Cancelada')?true:false;//Control
	if(xrest)
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-,   debe tener estado diferente  -Pagada- ó -Cancelada-');
	//Carga dato
	xdato = id("xObservacion").value;

	//Observaciones
	if( xdato == '' ) return;
        id("obs_"+cIdPedido).setAttribute("label","...");
	//reload   = true;
	reloadet = false;
	reloadpd = true;
	volverComprobantes(0);
	//Carga mesaje 
	msj='\n Agregar la observación: \n\n - '+xdato+
	    '\n\n en la '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+cEstado+'- '; 


	break;
    case 7:
 	//Importe Percepcion
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Percepcion
	xdato = id("xPercepcion").value;
	xdato = trim(xdato);

 	if(!xdato || xdato == '' ) {
	    id("xPercepcion").value = clPercepcion;
 	    return;
	}

	id("percepcion_"+cIdPedido).getAttribute("value",xdato);
        id("percepcion_"+cIdPedido).setAttribute("label",cMoneda[1]['S']+" "+xdato);

	reloadet = false;
	reloadpd = true;
	volverComprobantes(0);

	//Carga mesaje 
	msj    = ' Modificar '+cDocumento+' Nro.'+cCodigo+
	         ' '+cProveedor+' -'+cEstado+'-\n\n '+
	         '- Nuevo Importe Percepción '+cMoneda[1]['S']+" "+xdato+' ';

	break;
    case 8:
 	//Cancelar
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')? false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado diferente a -'+cEstado+'-');
 
	if((cDocumento=='Albaran' && cEstado=='Pendiente')) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe ser un documento diferente a -'+cDocumento+'-');
 
	xrest = (cEstado=='Pendiente'&&cDocumento=='Ticket')? true:false;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado diferente a -'+cEstado+'-');
 
	var cmsj;
	xdato = (cDocumento=="Ticket")?"Ticket":id("idlocal_"+idx).getAttribute("value")+'-'+idx;
	cmsj  = (cDocumento=="Ticket")?"Nuevo estado -Cancelada-":"Nuevo Documento Ticket Nro."+xdato+" -"+cEstado+"-";
	//Carga mesaje 
	msj=' Cancelar '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+cEstado+
	    '-\n\n - '+cmsj;
	break;


    case 9:
 	//Consolidar 
	if(cDocumento!='Ticket')
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, los documentos a consolidar son los Ticket.');

	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?true:false;//Control
	if(!xrest)
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, debe tener estado -Borrador- ó -Pendiente-');
	//Carga dato
	xdato = getNuevoDatoCompra(xocs,
				   'los códigos de Ticket a Consolidar,\n'+
				   '  los códigos entre comas',
				   cCodigo+',',
				   false,
				   ' los códigos: '+cCodigo+',Serie-Número' );
	if(!xdato) return;
	//Valida Ticket
	var afh = xdato.split(',');
	var xid = new Array();//IdPedidos
	var xcd = new Array();//CCodigos
	var sid,smd;
	for(var i=0; i<afh.length; i++)
	{
	    if( id("idpedido_Ticket_"+afh[i]) )
	    {
		if(i!=0){

		    sid = id("idpedido_Ticket_"+afh[i]).getAttribute("value");
		    smd = id("idmoneda_"+sid).getAttribute("value");
		    std = id("estado_"+sid).getAttribute("label");
		    spr = id("proveedor_"+sid).getAttribute("label");

		    if(smd == cIdMoneda && spr == cProveedor && cEstado == std)
		    {
	    		xid.push(sid);
			xcd.push(afh[i]);
		    }
	 	}
	    }
	}

	//Excluidos
	xdato = (xid.toString())?xid.toString():false;
	if(!xdato){ alert('gPOS: ¡Acción Restringida! \n\n - '+
			  'Ingrese Tickets  del mismo Proveedor y/o Estado - '); 
		    return ModificarCompra(9);}

	//Carga mesaje 
	msj='\n\n Consolidar Tickets Nros. '+xcd.toString()+
	    ' en el Ticket Nro. '+cCodigo+' '+cProveedor;
	break;

    case 10:
 	//Facturar
	if(!(cDocumento=='Albaran' || cDocumento=='Ticket'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, los documentos a Facturar son los Albaranes y Ticket.');

	if(!(cEstado=='Pendiente'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, debe tener estado -Pendiente-');	

	var xfactura = getNuevoDatoCompra(3,
				   'el Código de Factura del proveedor '+cProveedor,
				   'Serie-Número',
				   false,' el código: Serie-Número' );
	if(!xfactura) return;
	//CCodigos
	if(cDocumento=='Albaran') 
	    xdato = getNuevoDatoCompra(xocs,
				       'los códigos de Albaran en la Factura '+
				       xfactura+'\n del proveedor '+
				       cProveedor+':\n\n    Los códigos entre comas.',
				       cCodigo,false,' los códigos: '+
				       cCodigo+',Serie-Número' );

	if(cDocumento=='Ticket') xdato = cCodigo;

	if(!xdato) return;
	//Valida Ticket
	var afh  = xdato.split(',');
	var xid  = new Array();//IdPedidos
	var xcd  = new Array();//CCodigos
	var dmsj = new Array();//Excluidos
	var sid,smd,spr,std;
	for(var i=0; i<afh.length; i++)
	{
	    if( id("idpedido_"+cDocumento+"_"+afh[i]) )
	    {
		sid = id("idpedido_"+cDocumento+"_"+afh[i]).getAttribute("value");
		smd = id("idmoneda_"+sid).getAttribute("value");
		spr = id("proveedor_"+sid).getAttribute("label");
		std = id("estado_"+sid).getAttribute("label");
		
		if(smd == cIdMoneda && spr == cProveedor && cEstado == std)
		{
	    	    xid.push(sid);
		    xcd.push('\n    - '+cDocumento+' Nro '+afh[i]+' '+spr);
		}else{
		    dmsj.push('\n    - '+cDocumento+' Nro '+afh[i]+' '+spr)
		}
	    }
	}
	xdato = (xid.toString())?xid.toString():false;
	if(!xdato) return ModificarCompra(9);

	//Mensaje Excluidos
	xdmsj = (dmsj.toString())?'\n\n '+cDocumento+' excluidos: '+dmsj.toString():'';
	if(xdmsj!='')alert('gPOS:'+xdmsj+'\n\n');

	//Cadena CCodigo,IdPedidos 
	xdato = xfactura+','+xid.toString();
	xdato = (cDocumento=='Ticket')?'Ticket,'+xdato:xdato; 
	//Control
	msj='\n\n'+cDocumento+'(es) a facturar: '+xcd.toString()+
	    '\n\nFactura Nro. '+xfactura+' '+cProveedor;
	break;

    case 11:
 	//Boletar
	if(!(cDocumento=='Ticket'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, los documentos a Boletar son los Ticket.');

	if(!(cEstado=='Pendiente')) return alert(amsj+cCodigo+' -'+cEstado+
			 '-, debe tener estado -Pendiente-');	

	var xdato = getNuevoDatoCompra(3,
				       'el Código de Boleta del proveedor '+cProveedor,
				       'Serie-Número',
				       false,' el código: Serie-Número' );
	if(!xdato) return;
	//Control
	msj='\n\n'+cDocumento+' a boletar:\n    - '+cDocumento+
	    ' Nro '+cCodigo+' '+cProveedor+
	    '\n\nBoleta Nro. '+xdato;
	break;
    case 12:
    case 13:
 	//Precio Compra - Valor Compra
	if((cDocumento=='AlbaranInt'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, este Documento no está sujeto a cambios.');

	xrest = (cEstado=='Borrador')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador-');
	//msj
	var otext = ( xocs == 12 )?'Precio Compra':'Valor Compra';
	//Precio 
	var vtext = ( xocs == 12 )? "precioventa_"+idetx:"valorventa_"+idetx;
	var vdato = id(vtext).getAttribute("label");
	xProducto = id("producto_"+idetx).getAttribute("label");
	xCantidad = id("cantidad_"+idetx).getAttribute("value");
	vtext     = ( xocs == 12 )? "xPrecioCompra":"xValorCompra";
	xdato     = id(vtext).value;

	//Termina brutal 
	if( xdato == vdato ) return;

	var PV,PU,CT;
	//Costos y Precios 
	PV = (xocs==12)?xdato:parseFloat(xdato)*( parseFloat(cImpuesto)+100 )/100;
	VV = (xocs==12)?parseFloat(xdato)*100/( parseFloat(cImpuesto)+100 ):xdato;
	PV = parseFloat(PV).toFixed(2);
	VV = parseFloat(VV).toFixed(2);
	PU = parseFloat(PV/xCantidad).toFixed(2);
	CT = parseFloat(VV/xCantidad).toFixed(4);
 	//Carga mesaje 
	msj=' Modificar el '+cDocumento+' Nro.'+cCodigo+
	    ' -'+cEstado+' '+cProveedor+'-\n\n '+
	    '   -  '+xProducto+' \n\n Nuevo '+otext+' '+xdato;
	//Arreglo datos
	reloadpd = true;
	reloadet = false;
	reloadsl = false;

	volverComprobantes(0);

	xdato = PV+','+PU+','+CT;
	break;
    case 15:
  	//Lote Produccion
	if((cDocumento=='AlbaranInt'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, este Documento no está sujeto a cambios.');

	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Datos
	xProducto = id("producto_"+idetx).getAttribute("label");
	var xlote = id("lote_"+idetx).getAttribute("value");
	xdato     = id("xLote").value;

	if(xdato=='' ||xdato == xlote ) return;

 	//Carga mesaje 
	reloadet = true;
	reload   = false;
	reloadsl = true;
	volverComprobantes(0);

	msj=' Modificar el '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+'-'+cEstado+'-\n\n '+
	    '   -  '+xProducto+' \n\n Nuevo Lote de Producción '+xdato;
	break;
    case 16:
  	//Fecha Vencimiento
	if((cDocumento=='AlbaranInt'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, este Documento no está sujeto a cambios.');

	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Datos
	var xvencimiento  = id("vencimiento_"+idetx).getAttribute("value");
	xProducto         = id("producto_"+idetx).getAttribute("label");
	xdato             = id("xVencimiento").value;
	
	if(xdato == xvencimiento) return;

	volverComprobantes(0);
	reloadet = true;
	reload   = false;
	reloadsl = true;

 	//Carga mesaje 
	msj=' Modificar el '+cDocumento+' Nro.'+cCodigo+
	    ' -'+cEstado+' '+cProveedor+'-\n\n '+
	    '   -  '+xProducto+' \n\n Nuevo Fecha de Vencimiento '+xdato;
	break;

    case 17:
  	//Eliminar Producto
	if(!(cDocumento=='Ticket'))
	    return alert(amsj+cCodigo+' -'+cEstado+
			 '-, los documentos sujetos a cambios son los Ticket.');

	xrest = (cEstado=='Borrador')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador-');
	//Datos
	xProducto = id("producto_"+idetx).getAttribute("label");
 	//Carga mesaje 
	msj=' Modificar el '+cDocumento+' Nro.'+cCodigo+
	    ' -'+cEstado+' '+cProveedor+'-\n\n '+
	    ' Quitar el producto '+xProducto;
	break;

    case 18:
 	//Almacen
	var xrest  = (cEstado=='Borrador')?false:true;
	var xlocal = id("idlocal_"+cIdPedido).getAttribute("value");	

	if(xrest)
	{ 
	    id("xComprobanteLocal").value=xlocal;
	    return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			 ' debe tener estado -Borrador-');
	}
	//Lista Almacen
	//var xlocal = id("idlocal_"+cIdPedido).getAttribute("value");
	var xdato  = id("xComprobanteLocal").getAttribute("value");
	var llocal = id("xComprobanteLocal").getAttribute("label");

	//xComprobantes?
	if( xlocal == xdato ) return;

	volverComprobantes(0);

	//Mensaje 
	msj=' Modificar el Almacén '+cAlmacen+'  de '+cDocumento+
	    ' Nro.'+cCodigo+' - '+cEstado+'\n\n '+
	    '- Nuevo Almacén '+llocal+'';
	//Clean
	reload          = true;
	reloadet        = false;
	break;

    case 19:
 	//Importe Flete
	xrest = (cEstado=='Borrador' || cEstado=='Pendiente')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+cEstado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Flete
	xdato = id("xFlete").value;
	xdato = trim(xdato);
 	if(!xdato || xdato == '' ) {
	    id("xFlete").value = clFlete;
 	    return;
	}

	id("flete_"+cIdPedido).getAttribute("value",xdato);
        id("flete_"+cIdPedido).setAttribute("label",cMoneda[1]['S']+" "+xdato);

	reloadet = false;
	reloadpd = true;
	volverComprobantes(0);

	//Carga mesaje 
	msj    = ' Modificar '+cDocumento+' Nro.'+cCodigo+
	         ' '+cProveedor+' -'+cEstado+'-\n\n '+
	         '- Nuevo Importe Flete '+cMoneda[1]['S']+" "+xdato+' ';

	break;

    }

    //Control Brutal
    if(!confirm('gPOS: '+msj+','+' ¿desea continuar?'))
	return resetFormModificarCompra(xocs);

    //Ejecuta
    var url="services.php?"+
	"modo=ModificarCompra"+
	"&xid="+idx+
	"&xidet="+idetx+
	"&xdato="+xdato+
	"&xocs="+xocs; 
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);
    if(isNaN(xrequest.responseText)) alert(po_servidorocupado);    

    //Acciones Extra Vista
    if(reloadet) VaciarDetallesCompra();
    if(reload)   BuscarCompra();   
    if(reloadsl) RevisarCompraSeleccionada();
    if(reloadpd) buscarPorPedido(cIdPedido);
}

function VerObservCompra(){
    var idex      = id("busquedaCompra").selectedItem; 
    var idx       = idex.value;//IdCompraDet:false 
    var codigo    = id("codigo_"+idx).getAttribute("label");
    var estado    = id("estado_"+idx).getAttribute("label");
    var documento = id("documento_"+idx).getAttribute("label");
    var xobs      = id("obs_"+idx).getAttribute("value");
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
    return alert('gPOS:\n\n'+
		 '     Observaciones de '+documento+
		 ' Nro.'+codigo+' '+cProveedor+' -'+estado+'-\n'+
		 '     '+xobs+' \n ');
}

function sNSProductosCompra(){
    var idex      = id("busquedaDetallesCompra").selectedItem; 
    var idetx     = idex.value;
    var xProducto = id("producto_"+idetx).getAttribute("label");
    var vlnsx     = (id("ns_"+idetx).getAttribute("value")=='0')?true:false;
    
    if(vlnsx) return alert('gPOS: ¡Acción restringida!\n\n'+
			   '  Producto '+xProducto+' sin Número de Serie. ');

    var idx       = id("pedido_"+idetx).getAttribute("value");
    var vlcantx   = id("cantidad_"+idetx).getAttribute("value");
    var idprodx   = id("producto_"+idetx).getAttribute("value");
    var xtitulo   = cDocumento+' Nro. '+cCodigo+' '+cProveedor;

    ldNSDetalleComprobante(idprodx,xProducto,vlcantx,idetx,xtitulo);

}
function ModificarComprobante(){

    if(cEstado == 'Cancelada') return false;
    if(cEstado == 'Confirmada') return false;

    id("xComprobante").value      = cDocumento;
    id("xComprobanteLocal").value = id("idlocal_"+cIdPedido).getAttribute("value");
    id("xCodigo").value           = cCodigo;
    id("ProvHab").value           = cProveedor;
    id("xPercepcion").value       = clPercepcion;
    id("xFlete").value            = clFlete;
    id("xFormaPago").value        = cModopago;

    id("tPercepcion").setAttribute("label",'Percepción ( '+cMoneda[1]['S']+" "+cPercepcion+' )');
    id("tFlete").setAttribute("label",'Flete ( '+cMoneda[1]['S']+' )');

    if (cEmision  != 'undefined') id("xEmision").value = cEmision;
    if (cPago != 'undefined')	  id("xPago").value = cPago;

    var listcomp  = document.getElementById("listboxComprobantes");
    var formcomp  = document.getElementById("formularioComprobante");
    listcomp.setAttribute("collapsed","true");  
    formcomp.setAttribute("collapsed","false");  

}

function ModificarComprobanteDetalle(){

    if(cEstado != 'Borrador') return false;

    //Consige Valores
    var detx                = id("busquedaDetallesCompra").selectedItem; 
    var idetx               = detx.value
    var xComprobanteDetalle = cDocumento+' Nro '+cCodigo+'  '+cProveedor;
    var xProducto           = id("producto_"+idetx).getAttribute("label");
    var xCodigoBarras       = id("cb_"+idetx).getAttribute("label");
    var xValorCompra        = id("valorventa_"+idetx).getAttribute("label");
    var xCantidad           = id("cantidad_"+idetx).getAttribute("label");
    var xDetalle            = id("detalle_"+idetx).getAttribute("label");
    var xPrecioCompra       = id("precioventa_"+idetx).getAttribute("label");
    var xLote               = id("lote_"+idetx).getAttribute("value");
    var xVencimiento        = id("vencimiento_"+idetx).getAttribute("value");
    var esVencimiento       = ( xVencimiento != ' ' )? false:true;
    var esLote              = ( xLote !=' ' )? false:true;
    var listcomp            = document.getElementById("listboxComprobantes");
    var formdetcomp         = document.getElementById("formularioDetalleComprobante");

    //Carga Valores
    id("xComprobanteDetalle").value = xComprobanteDetalle;
    id("xProducto").value     = xCodigoBarras+' '+xProducto;
    id("xValorCompra").value  = xValorCompra;
    id("xPrecioCompra").value = xPrecioCompra;
    id("xDetalle").value      = xDetalle;
    id("xCantidad").value     = xCantidad;
    id("rowLote").setAttribute("collapsed",esLote);
    id("rowVencimiento").setAttribute("collapsed",esVencimiento);
    id("xLote").value         = xLote;
    if(xVencimiento  != ' ')
	id("xVencimiento").value = xVencimiento;
    
    //Muestra Formuario
    listcomp.setAttribute("collapsed","true");  
    formdetcomp.setAttribute("collapsed","false");  
}

function volverComprobantes(xcheck){
    var listcomp    = document.getElementById("listboxComprobantes");
    var formcomp    = document.getElementById("formularioComprobante");
    var formdetcomp = document.getElementById("formularioDetalleComprobante");
    var boxdetcomp = document.getElementById("boxDetCompra");
    listcomp.setAttribute("collapsed","false");  
    formcomp.setAttribute("collapsed","true");  
    boxdetcomp.setAttribute("collapsed","true");  
    formdetcomp.setAttribute("collapsed","true");

    if(xcheck == 1) validarFechaCambio(0);   // Fecha Comprobante
    if(xcheck == 2) validarFechaCambio(1);   // Fecha Comprobante detalle
}
function ldNSDetalleComprobante(idproducto,producto,cantidad,idpedidodet,titulo){

    var url  = "selcomprar.php?id="+idproducto+"&"+
	       "producto="+producto+"&"+
	       "idpedidodet="+idpedidodet+"&"+
  	       "cantidad="+cantidad+"&"+
  	       "titulo="+titulo+"&"+
  	       "idlocal="+cIdLocal+"&"+
  	       "valor=true&"+
	       "modo=validarSeriesCompraxProducto";

    var boxframe  = document.getElementById("webDetCompra");
    var listcomp  = document.getElementById("listboxComprobantes");
    var boxns     = document.getElementById("boxDetCompra");

    boxframe.setAttribute("src",url);  
    listcomp.setAttribute("collapsed","true");  
    boxns.setAttribute("collapsed","false");  

}

function resetFormModificarCompra(xocs){
    switch (xocs) {
    case 1:
	//Fecha Facturacion
	var setEmision = (cEmision  != 'undefined')? cEmision:'';
	id("emision_"+cIdPedido).value = setEmision;
	id("emision_"+cIdPedido).setAttribute("label",setEmision);
	break;

    case 2:
	//Fecha Pago
	var setPago = (cPago  != 'undefined')? cPago : '';
	id("pago_"+cIdPedido).value = setPago;
	id("pago_"+cIdPedido).setAttribute("label",setPago);
	break;

    case 3:
 	//CCodigo
	id("codigo_"+cIdPedido).setAttribute('label',cCodigo);
	id("xCodigo").value = cCodigo;
	break;

    case 4:
 	//Tipo Pago
	id("modopago_"+cIdPedido).setAttribute("label",cModopago);
	id("xFormaPago").value = cModopago;
	break;

    case 5:
 	//Proveedor
        id("ProvHab").value = cProveedor;
	id("proveedor_"+cIdPedido).setAttribute('label',cProveedor);
	break;

    case 6:
        //Observaciones
        id("xObservacion").value = '';
        var obs = ( trim(id("obs_"+cIdPedido).getAttribute('value')) != "" )? '...':'';
        id("obs_"+cIdPedido).setAttribute("label",obs);
	break;
    case 7:
	id("xPercepcion").value = '';
	BuscarCompra();   
	buscarPorPedido(cIdPedido);
	break;
    }
}

function viewComprobantes(){

    var boxframe  = document.getElementById("webDetCompra");
    var listcomp  = document.getElementById("listboxComprobantes");
    var boxns     = document.getElementById("boxDetCompra");

    boxframe.setAttribute("src","about:blank");
    listcomp.setAttribute("collapsed","false");  
    boxns.setAttribute("collapsed","true");
}

function xmenuCompraBorrador(){ 
    var modificar    = true;
    var consolidar   = true;
    var facturar     = true;
    var boletar      = true;
    var cancelar     = true;
    var modificardet = true;
    var quitardet    = true;
    var observacion  = (cObs != " ")?false:true;

    switch(cEstado){
    case 'Borrador':
	modificar    = false;
	consolidar   = (cDocumento == 'Ticket')? false:true;
	cancelar     = false;
	modificardet = (cDocumento != 'AlbaranInt')? false:true;
	quitardet    = (cDocumento != 'AlbaranInt')? false:true;
	break;
    case 'Pendiente':
	modificar    = false;
	cancelar     = (cDocumento == 'Albaran')? true:false;
	cancelar     = (cDocumento == 'Ticket' && cEstado== 'Pendiente')? true:cancelar;
	consolidar   = (cDocumento == 'Ticket')? false:true;
	facturar     = (cDocumento == 'Ticket' || cDocumento == 'Albaran')? false:true;
	boletar      = (cDocumento == 'Ticket')? false:true;
	break;
    case 'Confirmado':
	modificar    = false;
	break;
    case 'Cancelada':
	break;

    }

    id("mheadModifica").setAttribute("disabled",modificar);
    id("mheadConsolida").setAttribute("disabled",consolidar);
    id("mheadFactura").setAttribute("disabled",facturar);
    id("mheadBoleta").setAttribute("disabled",boletar);
    id("mheadcancela").setAttribute("disabled",cancelar);
    id("mdetModifica").setAttribute("disabled",modificardet);
    id("mdetQuita").setAttribute("disabled",quitardet);
    id("mheadObs").setAttribute("disabled",observacion);
}

function RevisarCompraDetalle(){

    var detx = id("busquedaDetallesCompra").selectedItem; 

    if(!detx) return;

    var idetx = detx.value;

    cVencimiento = id("vencimiento_"+idetx).getAttribute("value");

    xmenuCompraBorradorDetalle(idetx);
}

function xmenuCompraBorradorDetalle(xval){
    var serie  = id("ns_"+xval).getAttribute("value");
    var xserie = (serie != 0)? false:true;
    id("mdetNSerie").setAttribute("collapsed",xserie);
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Estado": 
	vEstado        = xchecked;
	break;
    case "Fecha_Facturacion":
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
    case "Fecha_Registro":
	vFechaRegistro = xchecked;
	break;
    case "Percepcion" : 
	vPercepcion     = xchecked;
	break;
    case "Flete" : 
	vFlete          = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarCompra();
}

function validarFechaCambio(xfecha){


    switch(xfecha){
    case 0:
	var xFEmision =  id("xEmision").value;  //cEmision 
	var xFPago    =  id("xPago").value;     // = cPago;
	// Fecha Emision 
	var xFecha1   = xFEmision.replace(/-/g,',');
	var xFecha2   = cEmision.replace(/-/g,',');

	if(compararIgualdadFechas(xFecha1, xFecha2))
	    ModificarCompra(1);
	
	// Fecha Pago
	xFecha1   = xFPago.replace(/-/g,',');
	xFecha2   = cPago.replace(/-/g,',');
	
	if(compararIgualdadFechas(xFecha1, xFecha2)) 
	    ModificarCompra(2);

	break;
    case 1:
	if(cVencimiento == ' ') return;
	var xFVence =  id("xVencimiento").value;  // cEmision 
	var xFecha1   = xFVence.replace(/-/g,',');
	var xFecha2   = cVencimiento.replace(/-/g,',');

	if(compararIgualdadFechas(xFecha1, xFecha2))
	    ModificarCompra(16,true);

	break;
    }
}