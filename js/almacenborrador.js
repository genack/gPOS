
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
var ilineabuscacompra     = 0;
var Vistas = new Object(); 
Vistas.ventas = 7;

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
    RawBuscarCompraRecibir(desde,hasta,emision,nombre,modocontado,modocredito,filtrodocumento,
			   filtrocompra,filtrolocal,filtromoneda,forzaid,AddLineaCompra);
    if(forzaid) buscarPorCodigo(filtrocodigo);
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

function RawBuscarCompraRecibir(desde,hasta,emision,nombre,modocontado,modocredito,filtrodocumento,
				filtrocompra,filtrolocal,filtromoneda,forzaid,FuncionProcesaLinea){

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
        + "&forzaid=" + forzaid
        + "&xrecibir=" +true;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,Usuario,CambioMoneda,FechaCambioMoneda,IdPedidoDetalle,IdPedido,IdComprobanteProv,IdMoneda,IdOrdenCompra,IdLocal,ImpuestoVenta;
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

	    if (Documento == 'Albaran')    nroAlbaran++; 
 	    if (Documento == 'AlbaranInt') nroAlbaranInt++; 
	
	    //Consolidado basico
	    sldoc = ( Documento == 'Albaran'   )?true:false;
	    sldoc = ( Documento == 'AlbaranInt')?true:sldoc;
	    sldoc = ( Estado    == 'Cancelada' )?true:sldoc;
	    if ( Estado == 'Cancelada') nroCancelado++;
	    if (!sldoc)
	    {
		if (IdMoneda == 1)
		    totalImporte = parseFloat(totalImporte)+parseFloat(TotalImporte);
		if (IdMoneda == 2)
		    totalImporte = parseFloat(totalImporte)+parseFloat(TotalImporte*CambioMoneda);
	    }

            FuncionProcesaLinea(item,Almacen,Proveedor,Codigo,Documento,Registro,Emision,Pago,
				Impuesto,Percepcion,Simbolo,ImporteBase,ImporteImpuesto,
				TotalImporte,ImportePendiente,ImportePercepcion,ModoPago,Estado,
				Usuario,CambioMoneda,FechaCambioMoneda,IdPedidosDetalle,IdPedido,
				IdComprobanteProv,IdMoneda,IdOrdenCompra,Observaciones,IdLocal,
				ImpuestoVenta,IdAlmacen);
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
			ImpuestoVenta,IdAlmacen){

    var lista = id("busquedaCompra");
    var xitem,xnumitem,xAlmacen,xCodigo,XDocumento,xProveedor,xRegistro,xEmision,xPago,xModoPago,xBase,xImpuesto,xTotal,xPercepcion,xEstado,xUsuario,xObservaciones,xIdPedido,xIdLocal,xImpuestoVenta,xIdAlmacen;
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
    xBase.setAttribute("label", Simbolo+' '+ImporteBase);	
    xBase.setAttribute("style","text-align:right");

    xImpuesto = document.createElement("listcell");
    xImpuesto.setAttribute("label", Simbolo+' '+ImporteImpuesto);	
    xImpuesto.setAttribute("style","text-align:right;");

    xTotal = document.createElement("listcell");
    xTotal.setAttribute("label", Simbolo+' '+TotalImporte);
    xTotal.setAttribute("style","text-align:right;font-weight:bold; ");
    xTotal.setAttribute("value",TotalImporte);
    xTotal.setAttribute("id","importe_"+IdPedido);

    xPendiente = document.createElement("listcell");
    xPendiente.setAttribute("label", Simbolo+' '+ImportePendiente);
    xPendiente.setAttribute("style","text-align:right;font-weight:bold; ");
    xPendiente.setAttribute("value",ImportePendiente);
    xPendiente.setAttribute("id","pendiente_"+IdPedido);

    xPercepcion = document.createElement("listcell");
    xPercepcion.setAttribute("label", Simbolo+' '+ImportePercepcion);
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
    lista.appendChild( xitem );		
}

function RevisarCompraSeleccionada(){

    VaciarDetallesCompra();

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
    cDocumento    = id("documento_"+idex.value).getAttribute("label");
    cImpuesto     = id("impuestoventa_"+idex.value).getAttribute("value");
    cCodigo       = id("codigo_"+idex.value).getAttribute("label");
    cEstado       = id("estado_"+idex.value).getAttribute("label");
    cIdAlmacen    = id("idalmacen_"+idex.value).getAttribute("value");
    cIdLocal      = id("idlocal_"+idex.value).getAttribute("value");
    cIdPedido     = idex.value;
    cIdPedidoDet  = IdPedidos;

    //Documento
    xOperacion    = cDocumento.replace(" - ", "-");
    aOperacion    = xOperacion.split("-");
    cDocumentokdx = aOperacion[0];
    
    BuscarDetallesCompra(IdPedidos,cIdAlmacen);

    idfacturaseleccionada = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    var nrodocumento =  idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    nrodocumentodevol = nrodocumento;
    var seriedocumento =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    seriedocumentodevol = seriedocumento;
    var cadena    =  idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    id("guardarPrecios").setAttribute("collapsed","false");
    id("actualizarLPV").setAttribute("collapsed","false");
    id("recibirProductos").setAttribute("collapsed","false");
    //ExtraBuscarEnServidor("");  	  		
}

function BuscarDetallesCompra(IdPedido,IdAlmacen){

    RawBuscarDetallesCompraRecibir(IdPedido,IdAlmacen,AddLineaDetallesCompraRecibir);

}

function RawBuscarDetallesCompraRecibir(IdPedido,IdAlmacen,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();

    var url = "services.php?modo=mostrarDetallesComprasRecibir"+
	      "&IdPedido="+IdPedido+
	      "&IdAlmacen="+IdAlmacen;
    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia,IdProducto,CodigoBarras,Producto,Cantidad,Costo,CostoPromedio,PVD,PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedido,StockMin;
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

                FuncionRecogerDetalles(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,CostoPromedio,PVD,
				       PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedidoDet,StockMin,
				       PVDS,PVCS,PVD,PVC,PVDD,PVCD,VentaMenudeo,
				       Contenedor,UndContenedor,UndMedida);
            }
        }
    }
}

function AddLineaDetallesCompraRecibir(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,CostoPromedio,PVD,
				       PVDDcto,PVC,PVCDcto,LT,FV,NS,IdPedidoDet,StockMin,
				       PVDS,PVCS,PVD,PVC,PVDD,PVCD,VentaMenudeo,
				       Contenedor,UndContenedor,UndMedida){

    var lista = id("busquedaDetallesCompra");
    var xitem,xnumitem,xReferencia,xCodigoBarras,grid1,grid2,grid3,row1,row2,row3,xProducto,xCantidad,xStockMin,xCP,xMUD,xPVD,xPVDD,xMUC,xPVC,xPVCD,xIdProducto,button0,xDetalle;

    var fclkNS  = 'sNSProductosAlmacenBorrador('+IdProducto+','+Cantidad+','+cIdPedido+','+IdPedidoDet+')';
    var telemt  = (NS!='0')?'button':'description';//Elemento Detalle
    var tprint  = (NS!='0')?'label':'value';
    var lvNS    = (NS=='2')?'***N/S***':'N/S';
    var oclkNS  = (telemt=='button')?fclkNS:'';
    var Detalle = '';

    
    Detalle  += (LT!=' ')?'Lt. '+LT :'';
    Detalle  += (FV!=' ')?' Fv. '+FV :'';
    Detalle   = (NS!='0')?lvNS:Detalle;
    var PV,MUD,MUC,ImpD,ImpC,CP;
    var aPVDS,aPVCS;

    //Costo & Impuesto
    CP    = (CostoPromedio=='')?parseFloat((Costo+CostoPromedio)/2).toFixed(2): Costo;
    CP    = parseFloat(CP).toFixed(2);
    Imp   = parseFloat(CP*cImpuesto/100).toFixed(2)
    PV    = parseFloat(CP)+parseFloat(Imp);
    PV    = PV.toFixed(2);

    //Precio Venta Directo
    aPVDS = ( PVDS == '0')? false:PVDS.split('~');
    PVD   = ( PVD < PV   )? PV:PVD;
    PVD   = ( !aPVDS     )? PVD:aPVDS[0];
    PVD   = ( PVD == '0' )? PV:PVD;
    PVDD  = ( !aPVDS     )? PVD:aPVDS[1];
    PVDD  = ( PVDD == '0')? PV:PVDD;
    MUD   = parseFloat(PVD-PV).toFixed(2);

    //Precio Venta Coorporativo
    aPVCS = ( PVCS == '0' )? false:PVCS.split('~');
    PVC   = ( PVC<PV      )? PV:PVC;
    PVC   = ( !aPVCS      )? PVC:aPVCS[0];
    PVC   = ( PVC == '0'  )? PV:PVC;
    PVCD  = ( !aPVCS      )? PVC:aPVCS[1];
    PVCD  = ( PVCD == '0' )? PV:PVCD;
    MUC   = parseFloat(PVC-PV).toFixed(2);


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

    xitem = document.createElement('label');
    xitem.setAttribute('value',numitem+'.');
    xitem.setAttribute('readonly','true');
    xitem.setAttribute('style','padding-top:5px;text-align:center;width:0.5em');
    xitem.setAttribute("size","1");

    xProducto = document.createElement('description');//Producto 
    xProducto.setAttribute('id','NMP_'+IdPedidoDet);
    xProducto.setAttribute('value',Producto);
    xProducto.setAttribute('readonly','true');
    xProducto.setAttribute('style','border:1px solid #BEBDBC;width:35em;background-color: #BEBDBC;');

    xIdProducto = document.createElement('textbox');//IdProducto
    xIdProducto.setAttribute('value',IdProducto);
    xIdProducto.setAttribute('hidden','true');
    xIdProducto.setAttribute('id','IDP_'+IdPedidoDet);

    xCantidad = document.createElement('description');//Unidades
    xCantidad.setAttribute('value',tCantidad);
    xCantidad.setAttribute('readonly','true');
    xCantidad.setAttribute('style','border:1px solid #BEBDBC;width:10em;background-color: #BEBDBC;font-weight:bold;');
    xCantidad.setAttribute('id','CD_'+IdPedidoDet);

    xCP = document.createElement('description');//CP
    xCP.setAttribute('value',CP);
    xCP.setAttribute('readonly','true');
    xCP.setAttribute('style','border:1px solid #BEBDBC;width:3.2em;background-color: #BEBDBC;font-weight:bold;');
    xCP.setAttribute('id','CP_'+IdPedidoDet);
    xCP.setAttribute("size","5");

    xMUD = document.createElement('description');//MUD
    xMUD.setAttribute('value',MUD);
    xMUD.setAttribute('readonly','true');
    xMUD.setAttribute('style','border:1px solid #BEBDBC;width:4em;background-color: #BEBDBC;');
    xMUD.setAttribute('id','MUD_'+IdPedidoDet);
    xMUD.setAttribute("size","5");

    xPVD = document.createElement('textbox');//PVD
    xPVD.setAttribute('value',PVD);
    xPVD.setAttribute('id','PVD_'+IdPedidoDet);
    xPVD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD.setAttribute('onblur','validarPVD('+IdPedidoDet+')');
    xPVD.setAttribute('onfocus','this.select()');
    xPVD.setAttribute('style','width:4em;font-weight:bold;');
    xPVD.setAttribute('oninput','actualizarCantidades('+IdPedidoDet+')');
    xPVD.setAttribute("size","5");

    xPVDD = document.createElement('textbox');//PVDD
    xPVDD.setAttribute('value',PVDD);
    xPVDD.setAttribute('id','PVDD_'+IdPedidoDet);
    xPVDD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVDD.setAttribute('onfocus','this.select()');
    xPVDD.setAttribute('style','width:4em;');
    xPVDD.setAttribute('onblur','validarPVDD('+IdPedidoDet+')');
    xPVDD.setAttribute("size","5");

    xMUC = document.createElement('description');//MUC
    xMUC.setAttribute('value',MUC);
    xMUC.setAttribute('readonly','true');
    xMUC.setAttribute('style','border:1px solid #BEBDBC;width:3.2em;background-color: #BEBDBC;');
    xMUC.setAttribute('id','MUC_'+IdPedidoDet);
    xMUC.setAttribute("size","5");

    xPVC = document.createElement('textbox');//PVC
    xPVC.setAttribute('value',PVC);
    xPVC.setAttribute('id','PVC_'+IdPedidoDet);
    xPVC.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC.setAttribute('onblur','validarPVC('+IdPedidoDet+')');
    xPVC.setAttribute('style','width:4em;font-weight:bold;');
    xPVC.setAttribute('oninput','actualizarCantidades('+IdPedidoDet+')');
    xPVC.setAttribute('onfocus','this.select()');
    xPVC.setAttribute("size","5");

    xPVCD = document.createElement('textbox');//PVCD
    xPVCD.setAttribute('value',PVCD);
    xPVCD.setAttribute('id','PVCD_'+IdPedidoDet);
    xPVCD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVCD.setAttribute('style','width:4em;');
    xPVCD.setAttribute('onblur','validarPVCD('+IdPedidoDet+')');
    xPVCD.setAttribute('onfocus','this.select()');
    xPVCD.setAttribute("size","5");

    xDetalle = document.createElement( telemt );//Unidades
    xDetalle.setAttribute(tprint,Detalle);
    xDetalle.setAttribute('readonly','true');
    xDetalle.setAttribute('style','border:1px solid #BEBDBC;width:14em;background-color: #BEBDBC;');
    xDetalle.setAttribute('id','DT_'+IdPedidoDet);
    xDetalle.setAttribute('onclick',oclkNS);
    xDetalle.setAttribute("size","2");

    row0.appendChild(xitem);
    row0.appendChild(xIdProducto);
    row0.appendChild(xProducto);
    row0.appendChild(xCantidad);
    row0.appendChild(xCP);
    row0.appendChild(xMUD);
    row0.appendChild(xPVD);
    row0.appendChild(xPVDD);
    row0.appendChild(xMUC);
    row0.appendChild(xPVC);
    row0.appendChild(xPVCD);
    row0.appendChild(xDetalle);

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
    //Opera y set value 
    ImpD       = ((PVD.value/(1+cImpuesto/100))*(cImpuesto/100)).toFixed(2);
    MUD.value  = ((PVD.value - ImpD) - CP.value).toFixed(2);
    ImpC       = ((PVC.value/(1+cImpuesto/100))*(cImpuesto/100)).toFixed(2);
    MUC.value  = ((PVC.value - ImpC) - CP.value).toFixed(2);
    //PVDD.value = PVD.value;
    //PVCD.value = PVC.value;
}

function validarPVD(fila){
    var CP = id("CP_"+fila);
    var MUD = id("MUD_"+fila);
    var PVD = id("PVD_"+fila);
    var PVDD = id("PVDD_"+fila);
    if(parseFloat(MUD.value)<0){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n - Margen de Utilidad negativo:  "+cMoneda[1]['S']+" "+MUD.value+".");
        var PrecioMinimo =  CP.value*(1+cImpuesto/100);
        PrecioMinimo =PrecioMinimo.toFixed(2);
        PVD.value = PrecioMinimo;
        PVDD.value = PrecioMinimo;
        actualizarCantidades(fila);
    }
}
function validarPVDD(fila){
    var CP = id("CP_"+fila);
    var PrecioMinimo =  CP.value*(1+cImpuesto/100);
    PrecioMinimo = parseFloat(PrecioMinimo.toFixed(2));
    var PVD = id("PVD_"+fila);
    var PVDD = id("PVDD_"+fila);
    if(parseFloat(PVDD.value)>parseFloat(PVD.value) || parseFloat(PVDD.value) < PrecioMinimo){
        alert("gPOS: ¡Acción Restingida! "+
	      "\n\n  - Precio con descuento  "+cMoneda[1]['S']+" "+
	      PVDD.value+", el precio minimo es: "+cMoneda[1]['S']+" "+PrecioMinimo);
        PVDD.value = PVD.value;
    }
}

function validarPVC(fila){
    var CP = id("CP_"+fila);
    var MUC = id("MUC_"+fila);
    var PVC = id("PVC_"+fila);
    var PVCD = id("PVCD_"+fila);
    if(parseFloat(MUC.value)<0){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n - Margen de Utilidad negativo: "+cMoneda[1]['S']+" "+MUC.value);
        var PrecioMinimo =  CP.value*(1+cImpuesto/100);
        PrecioMinimo =PrecioMinimo.toFixed(2);
        PVC.value = PrecioMinimo;
        PVCD.value = PrecioMinimo;
        actualizarCantidades(fila);
    }
}

function validarPVCD(fila){
    var CP = id("CP_"+fila);
    var PrecioMinimo =  CP.value*(1+cImpuesto/100);
    PrecioMinimo = parseFloat(PrecioMinimo.toFixed(2));
    var PVC = id("PVC_"+fila);
    var PVCD = id("PVCD_"+fila);
    if(parseFloat(PVCD.value)>parseFloat(PVC.value) || parseFloat(PVCD.value) < PrecioMinimo){
        alert("gPOS: ¡Acción Restringida! "+
	      "\n\n  - Precio con descuento "+
	      PVCD.value+", el precio minimo es: "+PrecioMinimo);
        PVCD.value = PVC.value;
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
    var url="services.php?"+
	"modo=RecibirProductosAlmacen"+
	"&xid="+cIdPedido+
	"&xdato="+cIdPedidoDet+
	"&xoperacion="+xoperacion+
	"&xlocal="+cIdAlmacen;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);
    if(isNaN(xrequest.responseText))
	return alert(po_servidorocupado);

    id("recibirProductos").setAttribute("collapsed","true");
    id("guardarPrecios").setAttribute("collapsed","true");
    id("actualizarLPV").setAttribute("collapsed","true");
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
	pv  = pvd+'_'+pvc;
	//Ejecuta
	var url="services.php?"+
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
    }
    //Guarda Precios Sources
    id("guardarPrecios").setAttribute("collapsed","true");
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
    var url  = "selcomprar.php?id="+idproducto+"&"+
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
	var listalocal= id("FiltroCompraLocal").value;
	url = "services.php?modo=actualizarNuevosPV&listalocal="+listalocal;
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

	url = "services.php?modo=actualizarAllNuevosPV";
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText) 
	    id("actualizarLPV").setAttribute("collapsed",true);
	else
	    alert('gPOS:  Precios TPV \n\n - El servidor esta ocupado');
    }
}
