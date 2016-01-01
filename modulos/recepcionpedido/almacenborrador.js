
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
var cDocumento            = "";
var cDocumentokdx         = "";
var cImpuesto             = "";
var cCodigo               = "";
var cEstado               = "";
var cIdAlmacen            = 0;
var cIdLocal              = 0;
var cIdPedido             = 0;
var cIdPedidoDet          = 0;
var cImporteFlete         = 0;
var ilineabuscacompra     = 0;
var cElementosPedido      = 0;
var cTipoCosto            = 'CP';
var cIdProveedor          = 0;
var cIdProvTras           = 0;
var Vistas = new Object(); 
Vistas.ventas = 7;

var RevDet = 0;

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
    VaciarDetallesCompra();
    var emision = id("FechaBuscaCompraEmision").selected;
    var desde   = id("FechaBuscaCompra").value;
    var hasta   = id("FechaBuscaCompraHasta").value;
    var nombre  = id("NombreProveedorBusqueda").value;

    var filtrodocumento = id("FiltroCompraDocumento").value;
    var filtrocompra    = id("FiltroCompra").value;
    var filtromoneda    = id("FiltroCompraMoneda").value;
    var filtrolocal     = id("FiltroCompraLocal").value;
    var filtrocodigo    = id("busquedaCodigoSerie").value
    var forzaid         = (filtrocodigo != '' )?filtrocodigo:false;
    var filtroformapago = "Todos";
    RawBuscarCompraRecibir(desde,hasta,emision,nombre,filtrodocumento,filtrocompra,
			   filtrolocal,filtromoneda,forzaid,filtroformapago,AddLineaCompra);
    if(forzaid) buscarPorCodigo(filtrocodigo);
    document.getElementById("busquedaCompraFooter").setAttribute("collapsed",true);
}

function buscarPorCodigo(elemento){
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
        var cadena = celdas[2].getAttribute('label');
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(busca) != -1){
            lista.selectItem(texto2);
            RevisarCompraSeleccionada();
            return;
        }
    }
    alert('gPOS:\n   - El codigo " '+elemento+' " no esta la lista.');
    //id("busquedaCodigoSerie").value='';
}

function RawBuscarCompraRecibir(desde,hasta,emision,nombre,filtrodocumento,filtrocompra,
				filtrolocal,filtromoneda,forzaid,filtroformapago,
				FuncionProcesaLinea){

    var url = "../../services.php?modo=mostrarCompra&desde=" + escape(desde)
        //+ "&modoconsulta=" + escape(modo)
        + "&hasta=" + escape(hasta)
        + "&nombre=" + trim(nombre)
        + "&emision=" + escape(emision)
	+ "&filtrodocumento=" + escape(filtrodocumento)
        + "&filtrocompra=" + escape(filtrocompra)
        + "&filtromoneda=" + escape(filtromoneda)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&filtropago=Todos"
        + "&forzaid=" + forzaid
        + "&filtroformapago="+escape(filtroformapago)
        + "&xrecibir=" +true;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidoDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,IdLocal,ImpuestoVenta,ImporteFlete,IdProveedor,TipoDoc;
    var node,t,i,codcompra; 
    var totalCompra = 0;
    var totalCompraPendiente = 0;
    var ImporteTotalCompra = 0;
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
            IdAlmacen        = node.childNodes[t++].firstChild.nodeValue;
	    IdProveedor      = node.childNodes[t++].firstChild.nodeValue;
            ImporteFlete     = node.childNodes[t+2].firstChild.nodeValue;
            TipoDoc          = node.childNodes[t+4].firstChild.nodeValue;

	    if (TipoDoc == 'Albaran')    nroAlbaran++; 
 	    if (TipoDoc == 'AlbaranInt') nroAlbaranInt++; 

	    //Consolidado basico
	    sldoc = ( TipoDoc == 'Albaran'   )?true:false;
	    sldoc = ( TipoDoc == 'AlbaranInt')?true:sldoc;
	    sldoc = ( Estado    == 'Cancelada' )?true:sldoc;
	    if ( Estado == 'Cancelada') nroCancelado++;
	    if (!sldoc)
		totalImporte = parseFloat(totalImporte)+parseFloat(TotalImporte);

            FuncionProcesaLinea(item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,
				Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,
				TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,
				Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,IdPedido,
				IdComprobanteProv,IdMoneda,IdOrdenCompra,Observaciones,IdLocal,
				ImpuestoVenta,IdAlmacen,ImporteFlete,IdProveedor);
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER

    id("TotalFacturas").value  = parseFloat(tC)-parseFloat(nroAlbaran+nroAlbaranInt);
    id("TotalAlbaranes").value = parseFloat(nroAlbaran+nroAlbaranInt);
    id("TotalImporte").value   = cMoneda[1]['S']+" "+formatDinero(totalImporte);
}

function AddLineaCompra(item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,
			Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,
			TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,
			Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,IdPedido,
			IdComprobanteProv,IdMoneda,IdOrdenCompra,Observaciones,IdLocal,
			ImpuestoVenta,IdAlmacen,ImporteFlete,IdProveedor){

    var lista = id("busquedaCompra");
    var xitem,xnumitem,xAlmacen,xCodigo,XDocumento,xProveedor,xRegistro,xEmision,xPago,xModoPago,xBase,xImpuesto,xTotal,xPercepcion,xEstado,xUsuario,xObservaciones,xIdPedido,xIdLocal,xImpuestoVenta,xIdAlmacen,xFlete;
    var Cont = (Observaciones!=' ' && Observaciones.length > 50 )?'...':'';
    var lobs = (Observaciones==' ')?'':Observaciones.substring(0, 50)+Cont;

    xitem = document.createElement("listitem");
    xitem.value = IdPedido;
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

    xIdAlmacen = document.createElement("listcell");
    xIdAlmacen.setAttribute("value",IdAlmacen);
    xIdAlmacen.setAttribute("collapsed","true");
    xIdAlmacen.setAttribute("id","idalmacen_"+IdPedido);

    xIdPedidoDetalle = document.createElement("listcell");
    xIdPedidoDetalle.setAttribute("value",IdPedidosDetalle);
    xIdPedidoDetalle.setAttribute("collapsed","true");
    xIdPedidoDetalle.setAttribute("id","idpedidosdetalle_"+IdPedido);

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value",IdMoneda);
    xIdMoneda.setAttribute("collapsed","true");
    xIdMoneda.setAttribute("id","idmoneda_"+IdPedido);

    xCambioMoneda = document.createElement("listcell");
    xCambioMoneda.setAttribute("value",CambioMoneda);
    xCambioMoneda.setAttribute("collapsed","true");
    xCambioMoneda.setAttribute("id","cambiomoneda_"+IdPedido);

    xDocumento = document.createElement("listcell");
    xDocumento.setAttribute("label",Documento);
    xDocumento.setAttribute("style","text-align:left;font-weight:bold;");
    xDocumento.setAttribute("id","documento_"+IdPedido);

    xProveedor = document.createElement("listcell");
    xProveedor.setAttribute("label",Proveedor);
    xProveedor.setAttribute("value",IdProveedor);
    xProveedor.setAttribute("style","text-align:left;font-weight:bold;");
    xProveedor.setAttribute("id","proveedor_"+IdPedido);

    xRegistro = document.createElement("listcell");
    xRegistro.setAttribute("label", Registro);
    xRegistro.setAttribute("style","text-align:center");

    xEmision = document.createElement("listcell");
    xEmision.setAttribute("label", Emision);
    xEmision.setAttribute("style","text-align:right;");

    xPago = document.createElement("listcell");
    xPago.setAttribute("label", Pago);	
    xPago.setAttribute("style","text-align:center;font-weight:bold;");

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
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

    xPendiente = document.createElement("listcell");
    xPendiente.setAttribute("label", Simbolo+' '+formatDinero(ImportePendiente));
    xPendiente.setAttribute("style","text-align:right;font-weight:bold; ");
    xPendiente.setAttribute("value",ImportePendiente);
    xPendiente.setAttribute("id","pendiente_"+IdPedido);

    xPercepcion = document.createElement("listcell");
    xPercepcion.setAttribute("label", Simbolo+' '+formatDinero(ImportePercepcion));
    xPercepcion.setAttribute("style","text-align:right");
    xPercepcion.setAttribute("value",Percepcion);
    xPercepcion.setAttribute("id","percepcion_"+IdPedido);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", Estado);
    xEstado.setAttribute("style","text-align:left;");
    xEstado.setAttribute("id","estado_"+IdPedido);
    xEstado.setAttribute("collapsed","true");

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("style","text-align:center;");


    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label", lobs);
    xObservaciones.setAttribute("value",Observaciones );
    //xObservaciones.setAttribute("collapsed","true");
    xObservaciones.setAttribute("id","obs_"+IdPedido);
    xObservaciones.setAttribute("style","text-align:left");
    xObservaciones.setAttribute("crop", "end");

    xFlete = document.createElement("listcell");
    xFlete.setAttribute("value", ImporteFlete);
    xFlete.setAttribute("id","flete_"+IdPedido);
    xFlete.setAttribute("collapsed","true");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xAlmacen );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xDocumento );
    xitem.appendChild( xProveedor );
    xitem.appendChild( xTotal );
    xitem.appendChild( xRegistro );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xIdPedido );
    xitem.appendChild( xIdPedidoDetalle );
    xitem.appendChild( xIdMoneda );
    xitem.appendChild( xCambioMoneda );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xImpuestoVenta );
    xitem.appendChild( xEstado );
    xitem.appendChild( xIdAlmacen );
    xitem.appendChild( xFlete );
    lista.appendChild( xitem );		
}

function RevisarCompraSeleccionada(){

    var idex       = id("busquedaCompra").selectedItem;
    var xdockdx    = '';
    var xOperacion = '';    
    var aOperacion = Array();

    if(!idex) return;

    var IdPedidos = id("idpedidosdetalle_"+idex.value).getAttribute("value");
    cBMoneda      = id("FiltroCompraMoneda").getAttribute("value");
    cIdMoneda     = id("idmoneda_"+idex.value).getAttribute("value");
    cCambioMoneda = id("cambiomoneda_"+idex.value).getAttribute("value");
    cProveedor    = id("proveedor_"+idex.value).getAttribute("label");
    cIdProveedor  = id("proveedor_"+idex.value).getAttribute("value");
    cDocumento    = id("documento_"+idex.value).getAttribute("label");
    cImpuesto     = id("impuestoventa_"+idex.value).getAttribute("value");
    cCodigo       = id("codigo_"+idex.value).getAttribute("label");
    cEstado       = id("estado_"+idex.value).getAttribute("label");
    cIdAlmacen    = id("idalmacen_"+idex.value).getAttribute("value");
    cIdLocal      = id("idlocal_"+idex.value).getAttribute("value");
    cImporteFlete = id("flete_"+idex.value).getAttribute("value");
    cIdPedido     = idex.value;
    cIdPedidoDet  = IdPedidos;

    cIdProvTras   = (cDocumento == 'AlbaranInt - Traslado')? cIdProveedor:0;

    //Documento
    xOperacion    = cDocumento.replace(" - ", "-");
    aOperacion    = xOperacion.split("-");
    cDocumentokdx = aOperacion[0];
    
    idfacturaseleccionada = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    var nrodocumento =  idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    nrodocumentodevol = nrodocumento;
    var seriedocumento =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    seriedocumentodevol = seriedocumento;
    var cadena    =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    id("guardarPrecios").setAttribute("collapsed","false");
    id("actualizarLPV").setAttribute("collapsed","false");
    id("recibirProductos").setAttribute("collapsed","false");
    id("TipoCosto").setAttribute("collapsed","false");
    document.getElementById("busquedaCompraFooter").setAttribute("collapsed",false);

    var verdet = (RevDet == 0 || RevDet != idex.value)? true:false;

    if(verdet || idetallesCompra == 0)
        setTimeout("loadDetalleCompra("+IdPedidos+","+cIdAlmacen+")",100);

    RevDet = idex.value;
}

function loadDetalleCompra(xid,yid){
    VaciarDetallesCompra();
    BuscarDetallesCompra(xid,yid);
} 

function BuscarDetallesCompra(IdPedido,IdAlmacen){

    RawBuscarDetallesCompraRecibir(IdPedido,IdAlmacen,AddLineaDetallesCompraRecibir);

}

function RawBuscarDetallesCompraRecibir(IdPedido,IdAlmacen,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();

    var url = "../../services.php?modo=mostrarDetallesComprasRecibir"+
	      "&IdPedido="+IdPedido+
	      "&xidprov="+cIdProvTras+
	      "&IdAlmacen="+IdAlmacen;
    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia,IdProducto,CodigoBarras,Producto,Cantidad,Costo,CostoPromedio,PVD,PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedido,StockMin,MUSubFamilia,CostoOperativo,COPOrigen;
    var node,t,i;
    var numitem = 0;

    if (!obj.responseXML) return alert(po_servidorocupado);		
    var xml = obj.responseXML.documentElement;
    cElementosPedido = xml.childNodes.length;

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
                CostoPromedio= node.childNodes[t++].firstChild.nodeValue;
                PVD          = node.childNodes[t++].firstChild.nodeValue;
                PVDDcto      = node.childNodes[t++].firstChild.nodeValue;
                PVC          = node.childNodes[t++].firstChild.nodeValue;
                PVCDcto      = node.childNodes[t++].firstChild.nodeValue;
                LT           = node.childNodes[t++].firstChild.nodeValue;
                FV           = node.childNodes[t++].firstChild.nodeValue;
                NS           = node.childNodes[t++].firstChild.nodeValue;
  		IdPedidoDet  = node.childNodes[t++].firstChild.nodeValue;
  		StockMin     = node.childNodes[t++].firstChild.nodeValue;
		PVDS         = node.childNodes[t++].firstChild.nodeValue;
		PVCS         = node.childNodes[t++].firstChild.nodeValue; 
		PVD          = node.childNodes[t++].firstChild.nodeValue; 
		PVC          = node.childNodes[t++].firstChild.nodeValue;
		PVDD         = node.childNodes[t++].firstChild.nodeValue; 
		PVCD         = node.childNodes[t++].firstChild.nodeValue; 
		VentaMenudeo = node.childNodes[t++].firstChild.nodeValue;
		Contenedor   = node.childNodes[t++].firstChild.nodeValue;
		UndContenedor= node.childNodes[t++].firstChild.nodeValue;
		UndMedida    = node.childNodes[t++].firstChild.nodeValue;
		CostoOperativo = node.childNodes[t+2].firstChild.nodeValue;
		MUSubFamilia = node.childNodes[t+3].firstChild.nodeValue;
		COPOrigen    = node.childNodes[t+4].firstChild.nodeValue;

                FuncionRecogerDetalles(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,CostoPromedio,PVD,
				       PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedidoDet,StockMin,
				       PVDS,PVCS,PVD,PVC,PVDD,PVCD,VentaMenudeo,
				       Contenedor,UndContenedor,UndMedida,
				       MUSubFamilia,CostoOperativo,COPOrigen);
            }
        }
    }
}

function AddLineaDetallesCompraRecibir(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,CostoPromedio,PVD,
				       PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedidoDet,StockMin,
				       PVDS,PVCS,PVD,PVC,PVDD,PVCD,VentaMenudeo,
				       Contenedor,UndContenedor,UndMedida,
				       MUSubFamilia,CostoOperativo,COPOrigen){

    var lista = id("busquedaDetallesCompra");
    var xitem,xnumitem,xReferencia,xCodigoBarras,grid1,grid2,grid3,row1,row2,row3,xProducto,xCantidad,xStockMin,xCP,xMUD,xPVD,xPVDD,xMUC,xPVC,xPVCD,xIdProducto,button0,xDetalle,xMUSubFamiliaVD,xMUSubFamiliaVC,xDSTO,xMUG;

    var fclkNS  = 'sNSProductosAlmacenBorrador('+IdProducto+','+Cantidad+','+cIdPedido+','+IdPedidoDet+')';
    var telemt  = (NS!='0')?'button':'description';//Elemento Detalle
    var tprint  = (NS!='0')?'label':'value';
    var xdetclass = (NS!='0')? 'res-item btn':'res-item';
    var lvNS    = (NS=='2')?'***N/S***':'N/S';
    var oclkNS  = (telemt=='button')?fclkNS:'';
    var Detalle = '';

    var aMUSF   = MUSubFamilia.split('~~');
    var MUSFVD  = parseFloat(aMUSF[0]);
    var MUSFVC  = parseFloat(aMUSF[1]);
    var Descuento = parseFloat(aMUSF[2]);
    var DSTO    = (Descuento/100).round(2);


    Detalle  += (LT!=' ')?'Lt. '+LT :'';
    Detalle  += (FV!=' ')?' Fv. '+FV :'';
    Detalle   = (NS!='0')?lvNS:Detalle;
    var PV,MUD,MUC,ImpD,ImpC,CP;
    var aPVDS,aPVCS;

    //Convert a Números los variables
    COP   = parseFloat(CostoOperativo);

    // Costo Operativo del local origen
    COP   = (cIdProvTras != 0)? parseFloat(COPOrigen):COP;

    Costo = parseFloat(Costo);
    CostoPromedio  = parseFloat(CostoPromedio);
    CostoOperativo = parseFloat(CostoOperativo);
    cMargenUtilidad = parseFloat(cMargenUtilidad);
    cDescuentoGral = parseFloat(cDescuentoGral);
    cImpuesto      = parseFloat(cImpuesto);
    cCambioMoneda  = parseFloat(cCambioMoneda);
    var MU_Directo    = (MUSFVD == 0)? cMargenUtilidad:MUSFVD;
    var MU_Corporativo= (MUSFVC == 0)? cMargenUtilidad:MUSFVC;

    var DSTO_G     = (cDescuentoGral/100).round(2);
    DSTO           = (Descuento != 0)? DSTO:DSTO_G;
    CostoOperativo = (cImpuestoIncluido)? 0:CostoOperativo;

    //Costo
    CP    = (CostoPromedio!=0)? ((Costo+CostoPromedio)/2).round(2) : Costo;
    CP    = (cTipoCosto == 'CP')? CP:Costo;

    //Precio Venta Directo
    aPVDS = ( PVDS == '0')? false:PVDS.split('~');
    MUD   = (CP + CostoOperativo)*(MU_Directo/100);
    PVD   = CP + MUD + CostoOperativo;
    Imp   = (PVD*cImpuesto/100).round(2);
    PVD   = (cImpuestoIncluido)? (PVD + Imp + COP):(PVD + Imp);
    PVD   = ( !aPVDS     )? PVD:parseFloat(aPVDS[0]);
    PVD   = PVD.round(2);
    MUD   = (!aPVDS)? MUD:(PVD*100/(100+(cImpuesto))-CP-CostoOperativo);
    MUD   = MUD.round(2);
    PVDD  = ( !aPVDS     )? PVD:parseFloat(aPVDS[1]);
    PVDD  = (FormatPreciosTPV(PVD)-(MUD*DSTO)).round(2);

    //Precio Venta Coorporativo
    aPVCS = ( PVCS == '0' )? false:PVCS.split('~');

    MUC   = (CP + CostoOperativo)*(MU_Corporativo/100);
    PVC   = (CP + MUC + CostoOperativo);
    Imp   = (PVC*cImpuesto/100).round(2)
    PVC   = (cImpuestoIncluido)? (PVC + Imp + COP):(PVC + Imp);
    PVC   = ( !aPVCS      )? PVC:parseFloat(aPVCS[0]);
    PVC   = PVC.round(2);
    MUC   = (!aPVCS)? MUC:(PVC*100/(100+cImpuesto))-CP-CostoOperativo;
    MUC   = MUC.round(2);
    PVCD  = ( !aPVCS      )? PVC:aPVCS[1];
    PVCD  = (FormatPreciosTPV(PVC)-(MUC*DSTO)).round(2);

    //Cantidad
    cResto    = (Cantidad%UndContenedor==Cantidad)?Cantidad:Cantidad%UndContenedor;
    tCantidad = ( VentaMenudeo == '1'      )? Cantidad-cResto:Cantidad;
    tCantidad = ( VentaMenudeo == '1'      )? Math.floor(tCantidad/UndContenedor):Cantidad;
    cResto    = ' + '+cResto;
    tCantidad = ( VentaMenudeo == '1'      )? tCantidad+' '+Contenedor+''+cResto:Cantidad;
    tUnidad   = UndMedida;
    tCantidad = tCantidad+' '+tUnidad;
    Detalle   = ( VentaMenudeo=='1' )? Detalle+' '+Contenedor+'/'+UndContenedor+''+tUnidad:Detalle;

    grid0    = id("busquedaDetallesCompra");
    //var bgdt = (idetallesCompra % 2 != 0)?'#666':'transparent';

    row0  = document.createElement('row');
    row0.setAttribute("id","detallecompra_" + idetallesCompra);
    row0.setAttribute("value",IdPedidoDet);
    //row0.setAttribute('style','background-color:'+bgdt);
    idetallesCompra++;

    xitem = document.createElement('description');
    xitem.setAttribute('value',numitem+'.');
    xitem.setAttribute('readonly','true');
    xitem.setAttribute('style','text-align:center;width:0.5em');
    xitem.setAttribute('class','res-item');
    xitem.setAttribute("size","1");

    xProducto = document.createElement('description');//Producto 
    xProducto.setAttribute('id','NMP_'+IdPedidoDet);
    xProducto.setAttribute('value',Producto);
    xProducto.setAttribute('readonly','true');
    xProducto.setAttribute('style','width:35em');
    xProducto.setAttribute('class','res-item');

    xIdProducto = document.createElement('textbox');//IdProducto
    xIdProducto.setAttribute('value',IdProducto);
    xIdProducto.setAttribute('hidden','true');
    xIdProducto.setAttribute('id','IDP_'+IdPedidoDet);

    xCantidad = document.createElement('description');//Unidades
    xCantidad.setAttribute('value',tCantidad);
    xCantidad.setAttribute('readonly','true');
    xCantidad.setAttribute('style','width:10em;');
    xCantidad.setAttribute('class','res-item');
    xCantidad.setAttribute('id','CD_'+IdPedidoDet);

    xCP = document.createElement('description');//CP
    xCP.setAttribute('value',CP);
    xCP.setAttribute('readonly','true');
    xCP.setAttribute('style','width:3.2em;');
    xCP.setAttribute('class','res-item');
    xCP.setAttribute('id','CP_'+IdPedidoDet);
    xCP.setAttribute("size","5");

    xCOP = document.createElement('textbox');//COP
    xCOP.setAttribute('value',formatDineroTotal(COP));
    xCOP.setAttribute('label',formatDineroTotal(COP));
    xCOP.setAttribute('id','COP_'+IdPedidoDet);
    xCOP.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xCOP.setAttribute('onchange','validarCOP('+IdPedidoDet+')');
    xCOP.setAttribute('onfocus','this.select()');
    xCOP.setAttribute('style','width:4em;font-weight:bold;');
    xCOP.setAttribute('class','res-item');
    xCOP.setAttribute("size","5");


    xMUD = document.createElement('description');//MUD
    xMUD.setAttribute('value',MUD.toFixed(2));
    xMUD.setAttribute('label',MUD.toFixed(2));
    xMUD.setAttribute('readonly','true');
    xMUD.setAttribute('style','width:4em;');
    xMUD.setAttribute('class','res-item');
    xMUD.setAttribute('id','MUD_'+IdPedidoDet);
    xMUD.setAttribute("size","5");

    xPVD = document.createElement('textbox');//PVD
    xPVD.setAttribute('value',FormatPreciosTPV(PVD));
    xPVD.setAttribute('label',FormatPreciosTPV(PVD));
    xPVD.setAttribute('id','PVD_'+IdPedidoDet);
    xPVD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD.setAttribute('onblur','validarPVD('+IdPedidoDet+')');
    xPVD.setAttribute('onfocus','this.select()');
    xPVD.setAttribute('style','width:4em;font-weight:bold;');
    xPVD.setAttribute('class','res-item');
    xPVD.setAttribute('oninput','actualizarCantidades('+IdPedidoDet+')');
    xPVD.setAttribute("size","5");

    xPVDD = document.createElement('textbox');//PVDD
    xPVDD.setAttribute('value',FormatPreciosTPV(PVDD));
    xPVDD.setAttribute('id','PVDD_'+IdPedidoDet);
    xPVDD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVDD.setAttribute('onfocus','this.select()');
    xPVDD.setAttribute('style','width:4em;');
    xPVDD.setAttribute('class','res-item');
    xPVDD.setAttribute('onblur','validarPVDD('+IdPedidoDet+')');
    xPVDD.setAttribute("size","5");

    xMUC = document.createElement('description');//MUC
    xMUC.setAttribute('value',MUC.toFixed(2));
    xMUC.setAttribute('label',MUC.toFixed(2));
    xMUC.setAttribute('readonly','true');
    xMUC.setAttribute('style','width:3.2em;');
    xMUC.setAttribute('class','res-item');
    xMUC.setAttribute('id','MUC_'+IdPedidoDet);
    xMUC.setAttribute("size","5");

    xPVC = document.createElement('textbox');//PVC
    xPVC.setAttribute('value',FormatPreciosTPV(PVC));
    xPVC.setAttribute('label',FormatPreciosTPV(PVC));
    xPVC.setAttribute('id','PVC_'+IdPedidoDet);
    xPVC.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC.setAttribute('onblur','validarPVC('+IdPedidoDet+')');
    xPVC.setAttribute('style','width:4em;font-weight:bold;');
    xPVC.setAttribute('class','res-item');
    xPVC.setAttribute('oninput','actualizarCantidades('+IdPedidoDet+')');
    xPVC.setAttribute('onfocus','this.select()');
    xPVC.setAttribute("size","5");

    xPVCD = document.createElement('textbox');//PVCD
    xPVCD.setAttribute('value',FormatPreciosTPV(PVCD));
    xPVCD.setAttribute('id','PVCD_'+IdPedidoDet);
    xPVCD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVCD.setAttribute('style','width:4em;');
    xPVCD.setAttribute('class','res-item');
    xPVCD.setAttribute('onblur','validarPVCD('+IdPedidoDet+')');
    xPVCD.setAttribute('onfocus','this.select()');
    xPVCD.setAttribute("size","5");

    xDSTO = document.createElement('textbox');//DSTO
    xDSTO.setAttribute('value',DSTO);
    xDSTO.setAttribute('id','DSTO_'+IdPedidoDet);
    xDSTO.setAttribute('collapsed','true');

    xMUG = document.createElement('textbox');//MUG
    xMUG.setAttribute('value',parseFloat(MU_Directo));
    xMUG.setAttribute('label',parseFloat(MU_Corporativo));
    xMUG.setAttribute('id','MUG_'+IdPedidoDet);
    xMUG.setAttribute('collapsed','true');

    xDetalle = document.createElement( telemt );//Unidades
    xDetalle.setAttribute(tprint,Detalle);
    xDetalle.setAttribute('readonly','true');
    xDetalle.setAttribute('style','width:14em;');
    xDetalle.setAttribute('class',xdetclass);
    xDetalle.setAttribute('id','DT_'+IdPedidoDet);
    xDetalle.setAttribute('onclick',oclkNS);
    xDetalle.setAttribute("size","2");

    row0.appendChild(xitem);
    row0.appendChild(xIdProducto);
    row0.appendChild(xProducto);
    row0.appendChild(xCantidad);
    row0.appendChild(xCP);
    row0.appendChild(xCOP);
    row0.appendChild(xMUD);
    row0.appendChild(xPVD);
    row0.appendChild(xPVDD);
    row0.appendChild(xMUC);
    row0.appendChild(xPVC);
    row0.appendChild(xPVCD);
    row0.appendChild(xDetalle);
    row0.appendChild(xDSTO);
    row0.appendChild(xMUG);
    grid0.appendChild(row0);

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


function actualizarCantidades(fila){
    var CP   = id("CP_"+fila);
    var MUD  = id("MUD_"+fila);
    var ImpD = 0;
    var PVD  = id("PVD_"+fila);
    var PVDD = id("PVDD_"+fila);
    var MUC  = id("MUC_"+fila);
    var ImpC = 0;
    var PVC  = id("PVC_"+fila);
    var PVCD = id("PVCD_"+fila);
    var COP   = id("COP_"+fila);

    //Opera y set value 
    var Costo  = (cImpuestoIncluido)? 0: COP.value;
    var mg     = (PVD.value*100/(100+parseFloat(cImpuesto)))-CP.value-Costo;
    MUD.value  = mg.toFixed(2);

    var mgc    = (PVC.value*100/(100+parseFloat(cImpuesto)))-CP.value-Costo;
    MUC.value  = mgc.toFixed(2);
    //PVDD.value = PVD.value;
    //PVCD.value = PVC.value;
}

function validarCOP(fila){
    var CP   = id("CP_"+fila);
    var COP  = id("COP_"+fila);
    var DSTO = id("DSTO_"+fila);
    var MUG  = id("MUG_"+fila);

    var MUD  = id("MUD_"+fila);
    var PVD  = id("PVD_"+fila);
    var PVDD = id("PVDD_"+fila);

    var MUC  = id("MUC_"+fila);
    var PVC  = id("PVC_"+fila);
    var PVCD = id("PVCD_"+fila);

    COP.value  = formatDineroTotal(COP.value);

    var xCP  = parseFloat(CP.value);
    var xCOP = parseFloat(COP.value);
    var Costo  = (cImpuestoIncluido)? 0:xCOP;
    var xMUGD  = parseFloat(MUG.getAttribute("value"));
    var xMUGC  = parseFloat(MUG.label);

    MUD.value  = ((xCP+Costo)*(xMUGD/100)).round(2);
    var precio = parseFloat(CP.value)+Costo+parseFloat(MUD.value);
    var Imp    = precio*cImpuesto/100;
    precio     = (cImpuestoIncluido)? (precio+Imp+xCOP):(precio + Imp);
    precio     = precio.round(2);
    PVD.value  = FormatPreciosTPV(precio);
    PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));

    //Corporativo
    MUC.value  = ((xCP+Costo)*xMUGC/100).round(2);
    var precioc= parseFloat(CP.value)+Costo+parseFloat(MUC.value);
    var Impc   = precioc*cImpuesto/100;
    precioc    = (cImpuestoIncluido)? (precioc + Impc + xCOP):(precioc + Impc);
    precioc    = (precioc).round(2);
    PVC.value  = FormatPreciosTPV(precioc);
    PVCD.value = FormatPreciosTPV((precioc-MUC.value*DSTO.value).round(2));

    //actualizarCantidades(fila);

}

function validarPVD(fila){
    var CP    = id("CP_"+fila);
    var DSTO  = id("DSTO_"+fila);
    var MUD   = id("MUD_"+fila);
    var PVD   = id("PVD_"+fila);
    var PVDD  = id("PVDD_"+fila);
    var COP   = id("COP_"+fila);

    PVD.value = parseFloat(PVD.value).toFixed(2);
    PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));
    if(parseFloat(MUD.value)<0){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n - Margen de Utilidad negativo:  "+cMoneda[1]['S']+" "+MUD.value+".");

	COP.value  = formatDineroTotal(COP.label);
	MUD.value  = MUD.getAttribute("label");
	PVD.value  = FormatPreciosTPV(PVD.label);
	PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));
        //actualizarCantidades(fila);
    }
}
function validarPVDD(fila){
    var CP   = id("CP_"+fila);
    var COP  = id("COP_"+fila);
    var MUD  = id("MUD_"+fila);
    var DSTO = id("DSTO_"+fila);
    var Costo = (cImpuestoIncluido)? 0:parseFloat(COP.value);
    var PrecioMinimo = (parseFloat(CP.value)+Costo)*(1+cImpuesto/100);
    PrecioMinimo     = (cImpuestoIncluido)? PrecioMinimo+parseFloat(COP.value):PrecioMinimo;
    PrecioMinimo     = PrecioMinimo.round(2);
    var PVD    = id("PVD_"+fila);
    var PVDD   = id("PVDD_"+fila);
    PVDD.value = parseFloat(PVDD.value).toFixed(2);
    if(parseFloat(PVDD.value)>parseFloat(PVD.value) || parseFloat(PVDD.value) < PrecioMinimo){
        alert("gPOS: ¡Acción Restingida! "+
	      "\n\n  - Precio con descuento  "+cMoneda[1]['S']+" "+
	      PVDD.value+", el precio minimo es: "+cMoneda[1]['S']+" "+PrecioMinimo);
        PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));
    }
}

function validarPVC(fila){
    var CP     = id("CP_"+fila);
    var DSTO   = id("DSTO_"+fila);
    var MUC    = id("MUC_"+fila);
    var PVC    = id("PVC_"+fila);
    var PVCD   = id("PVCD_"+fila);
    PVC.value  = parseFloat(PVC.value).toFixed(2);
    PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).toFixed(2));
    if(parseFloat(MUC.value)<0){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n - Margen de Utilidad negativo: "+cMoneda[1]['S']+" "+MUC.value);

	MUC.value  = MUC.getAttribute("label");
	PVC.value  = FormatPreciosTPV(PVC.label);
	PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).round(2));
        //actualizarCantidades(fila);
    }
}

function validarPVCD(fila){
    var CP   = id("CP_"+fila);
    var COP  = id("COP_"+fila);
    var DSTO = id("DSTO_"+fila);
    var MUC  = id("MUC_"+fila);
    var Costo = (cImpuestoIncluido)? 0:parseFloat(COP.value);
    var PrecioMinimo = (parseFloat(CP.value)+Costo)*(1+cImpuesto/100);
    PrecioMinimo     = (cImpuestoIncluido)? PrecioMinimo+parseFloat(COP.value):PrecioMinimo;
    PrecioMinimo     = PrecioMinimo.round(2);
    var PVC  = id("PVC_"+fila);
    var PVCD = id("PVCD_"+fila);
    PVCD.value = parseFloat(PVCD.value).toFixed(2);
    if(parseFloat(PVCD.value)>parseFloat(PVC.value) || parseFloat(PVCD.value) < PrecioMinimo){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n  - Precio con descuento "+
	      PVCD.value+", el precio minimo es: "+PrecioMinimo);
        PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).round(2));
    }
}

function recibirProductos(){

    //cIdPedido;
    var Almacen = id("almacen_"+cIdPedido).getAttribute("label");
    //Control 
    if(!confirm('gPOS: Modificar '+cDocumento+
		' Nro '+cCodigo+
		'\n\n Recibir productos en el Almacén '+Almacen+','+
		' ¿desea continuar?')) return;
    //OPeracion
    var xoperacion = ( cDocumentokdx == 'AlbaranInt')? 3:1;//1:Compras 3:Traslado interno

    //Ejecuta
    var url="../../services.php?"+
	"modo=RecibirProductosAlmacen"+
	"&xid="+cIdPedido+
	"&xdato="+cIdPedidoDet+
	"&xoperacion="+xoperacion+
	"&xlocal="+cIdAlmacen;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);
    var xres = xrequest.responseText;
    var ares = xres.split('~');

    if(!parseInt(ares[0])){

	switch(ares[0]){
	case 'Registro':
	    return alert ("gPOS: \n\n"+"El documento "+cDocumento+" "+cCodigo+" está registrado");
	    break;
	case 'Series':
	    alert ("gPOS: "+cDocumento+" "+cCodigo+"\n\n"+"Los productos marcados con **NS** tienen los siguientes posibles problemas: \n - Si la cantidad de series ingresadas es mayor a la cantidad registrada,\n   Ingrese las series que faltan. \n - Si la cantidad de series es correcta y la cantidad del producto es diferente,\n   Cancele el comprobante.");
	    break;
	default:
	    alert(po_servidorocupado+'\n\n    -- '+cDocumento+" "+cCodigo+" --\n"+ares[0]);
	}

	VaciarDetallesCompra();
	BuscarCompra();
	return;
    }

    id("recibirProductos").setAttribute("collapsed","true");
    id("guardarPrecios").setAttribute("collapsed","true");
    id("actualizarLPV").setAttribute("collapsed","true");
    id("TipoCosto").setAttribute("collapsed","true");
    VaciarDetallesCompra();
    BuscarCompra();
    
}

function guardarPrecios(){

    var kid,kit,pvd,pvc,pv;
    var lista = id("busquedaDetallesCompra");
    
    for (var i = 0; i < idetallesCompra; i++) { 
        kid = id("detallecompra_"+i);
	kit = kid.getAttribute('value');
	pvd = id("PVD_"+kit).value+'~'+id("PVDD_"+kit).value;
        pvc = id("PVC_"+kit).value+'~'+id("PVCD_"+kit).value;
	idx = id("IDP_"+kit).value;	    
	pv  = pvd+'_'+pvc+'_'+id("COP_"+kit).value;
	//Ejecuta
	var url="../../services.php?"+
	    "modo=SalvaPreciosVenta"+
	    "&xid="+idx+
	    "&xlocal="+cIdAlmacen+
	    "&xdato="+pv;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	//alert(xrequest.responseText);
	if(isNaN(xrequest.responseText))
	    alert(po_servidorocupado);

	id("PVD_"+kit).setAttribute('readonly','true');
	id("PVDD_"+kit).setAttribute('readonly','true');
	id("PVC_"+kit).setAttribute('readonly','true');
	id("PVCD_"+kit).setAttribute('readonly','true');
	id("COP_"+kit).setAttribute('readonly','true');
    }
    //Guarda Precios Sources
    id("guardarPrecios").setAttribute("collapsed","true");
    id("TipoCosto").setAttribute("collapsed",true);
}

function sNSProductosAlmacenBorrador(IdProducto,Cantidad,IdPedido,IdPedidoDet){
    //var idex      = id("busquedaDetallesCompra").selectedItem; 
    //var idetx     = idex.value;
    var xProducto = id("NMP_"+IdPedidoDet).getAttribute("value");
    var xtitulo   = cDocumento+' Nro. '+cCodigo+' '+cProveedor;

    ldNSDetalleAlmacenBorrador(IdProducto,xProducto,Cantidad,IdPedidoDet,xtitulo);

}

function ldNSDetalleAlmacenBorrador(idproducto,producto,cantidad,idpedidodet,titulo){

    var opentrada = ( cDocumentokdx == 'AlbaranInt')? "TrasLocal":"Compra";
    var url  = "../compras/selcomprar.php?id="+idproducto+"&"+
	       "producto="+producto+"&"+
	       "idpedidodet="+idpedidodet+"&"+
  	       "cantidad="+cantidad+"&"+
  	       "titulo="+titulo+"&"+
  	       "idlocal="+cIdLocal+"&"+
	       "operacionentrada="+opentrada+"&"+
  	       "valor=false&"+
  	       "modificar=true&"+
	       "modo=validarSeriesCompraxProducto";

    verNSDetalleAlmacenBorrador(true,url);
}

function verNSDetalleAlmacenBorrador(val,url){

    var nval = (val)? false:true;
    var boxframe  = document.getElementById("webDetCompra");
    var boxbc     = document.getElementById("busquedaCompra");
    var boxbcrs   = document.getElementById("busquedaCompraResumen");
    var boxdbc    = document.getElementById("busquedaCompraDetalle");
    var boxdbcrs  = document.getElementById("busquedaCompraDetalleResumen");
    var boxfooter = document.getElementById("busquedaCompraFooter");
    var boxns     = document.getElementById("boxDetCompra");

    boxframe.setAttribute("src",url);  
    boxbc.setAttribute("collapsed",val);  
    boxbcrs.setAttribute("collapsed",val);  
    boxdbc.setAttribute("collapsed",val);  
    boxdbcrs.setAttribute("collapsed",val);  
    boxfooter.setAttribute("collapsed",val);  
    boxns.setAttribute("collapsed",nval);  
}


function actualizarNuevosPV(){

    guardarPrecios();

    if(confirm("gPOS:\n   Aplicar los Nuevos Precios en el Local Actual?")){
	url = "../../services.php?modo=actualizarNuevosPV&listalocal="+cIdAlmacen;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText)
	    id("actualizarLPV").setAttribute("collapsed",true);
	else
	    alert('gPOS: Precios TPV \n\n - El servidor esta ocupado');
    }
}

function actualizarAllNuevosPV(){

    guardarPrecios();

    if(confirm("gPOS:\n   Aplicar los Nuevos Precios en Todos los Locales ?")){

	url = "../../services.php?modo=actualizarAllNuevosPV";
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText)
	    id("actualizarLPV").setAttribute("collapsed",true);
	else
	    alert('gPOS:  Precios TPV \n\n - El servidor esta ocupado');
    }
}

function ActualizarTipoCosto(xvalue,xid){
    cTipoCosto = xvalue;
    var xval = id(xid).getAttribute("checked");

    id("tipo_costopromedio").setAttribute("checked",false);
    id("tipo_ultimocosto").setAttribute("checked",false);

    id(xid).setAttribute("checked",true);
    
    if(xval=='false')
	loadDetalleCompra(cIdPedidoDet,cIdAlmacen);
}

