var cDocumento            = 'Pedido';
var cProveedor            = '';
var cLocal                = '';
var cIdProveedor          = 0;
var ProveedorPost         = false;
var IdProveedorPost       = 0;
var cCodigo               = 0;
var cModopago             = '';
var cEntrega              = '';
var cPago                 = '';
var cMonedax              = '';
var cMonedaTexto          = '';
var cOrdenCompra          = 0;
var idetallesOrdenCompra  = 0;
var cIdOrdenCompra        = 0;
var cEstado               = "";
var ilineabuscaordencompra= 0;
var cIdMoneda             = 0;
var cImporteOC            = 0;

var RevDet = 0;

// Opciones Busqueda avanzada
var vEstado        = true;
var vFechaEntrga   = true;
var vFormaPago     = true;
var vMoneda        = true;
var vLocal         = true;
var vUsuario       = true;
var vObservaciones = true;
var vFechaRegistro = true;
var vFechaPago     = true;
var vFechaRecibido = true;



var Vistas = new Object(); 
Vistas.ventas = 7;

var id = function(name) { return document.getElementById(name); }

function VerOrdenCompra(){
    id("FechaBuscaOrdenCompra").value = id("FechaBuscaOrdenCompra").value;
    id("FechaBuscaOrdenCompraHasta").value =id("FechaBuscaOrdenCompraHasta").value;	
    VaciarDetallesOrdenCompra();
    VaciarBusquedaOrdenCompra();
    BuscarOrdenCompra();
}

//Limpieza de Box
function VaciarBusquedaOrdenCompra(){
    var lista = id("busquedaOrdenCompra");

    for (var i = 0; i < ilineabuscaordencompra; i++) { 
        kid = id("lineabuscaordencompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscaordencompra = 0;
}
function VaciarDetallesOrdenCompra(){
    var lista = id("busquedaDetallesOrdenCompra");

    for (var i = 0; i < idetallesOrdenCompra; i++) { 
        kid = id("detalleordencompra_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesOrdenCompra = 0;
}


//Busqueda 
function BuscarOrdenCompra(){
    VaciarBusquedaOrdenCompra();
    VaciarDetallesOrdenCompra();

    var desde   = id("FechaBuscaOrdenCompra").value;
    var hasta   = id("FechaBuscaOrdenCompraHasta").value;
    var nombre  = id("NombreProveedorBusqueda").value;	

    var modocontado   = (id("modoConsultaOrdenCompraContado").checked)?"contado":"todos";
    var modocredito   = (id("modoConsultaOrdenCompraCredito").checked)?"credito":"todos";
    var filtrocompra  = id("FiltroOrdenCompra").value;
    var filtromoneda  = id("FiltroOrdenCompraMoneda").value;
    var filtrolocal   = (id("FiltroOrdenCompraLocal"))?id("FiltroOrdenCompraLocal").value:false;
    var filtrocodigo  = id("busquedaCodigoSerie").value;
    var filtrofecha   = id("FiltroFecha").value;

    var entrega = (filtrofecha == 'Entrega')? true:false;

    RawBuscarOrdenCompra(desde,hasta,nombre,modocontado,modocredito,filtrocompra,filtrolocal,filtromoneda,false,entrega,filtrocodigo,AddLineaOrdenCompra);

    volverOrdenCompras(0);
    var elemento = id("busquedaCodigoSerie").value;

    //if( elemento != '' ) //buscarPorOrdenCompra(elemento);

}

function buscarPorOrdenCompra(elemento){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaOrdenCompra");
    n = lista.itemCount;
    if(n==0) return; 
    busca = busca.toUpperCase();
    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[2].getAttribute('label');
        //cadena = cadena.toUpperCase();
        //if(cadena.indexOf(busca) != -1){

	if ( busca == cadena )
	{
            lista.selectItem(texto2);
            RevisarOrdenCompraSeleccionada();
            return;
        }
    }
    alert('gPOS:\n\n    El código " '+elemento+' " no está en la lista.');
    //id("busquedaCodigoSerie").value='';
}

function RawBuscarOrdenCompra(desde,hasta,nombre,modocontado,modocredito,filtrocompra,filtrolocal,filtromoneda,IdOrdenCompra,entrega,filtrocodigo,FuncionProcesaLinea){

    var url = "modordencompra.php?modo=mostrarOrdenCompra&desde=" + escape(desde) 
        + "&hasta=" + escape(hasta) 
        + "&nombre=" + trim(nombre)
        + "&modocontado=" + escape(modocontado)
        + "&modocredito=" + escape(modocredito)
        + "&filtrocompra=" + escape(filtrocompra)
        + "&filtromoneda=" + escape(filtromoneda)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&entrega=" + escape(entrega)
        + "&filtrocodigo=" + escape(filtrocodigo)
        + "&IdOrdenCompra=" + IdOrdenCompra;
    var obj = new XMLHttpRequest();

    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,Codigo,Local,Proveedor,Registro,Pedido,Entrega,Moneda,Importe,ModoPago,Importe,estado,Fletador,Usuario;
    var node,t,i,codcompra; 
    var totalOrdenCompra = 0;
    var totalOrdenCompraPendiente = 0;
    var ImporteTotalOrdenCompra = 0;
    var nroPendiente = 0;
    var nroPedido = 0;
    var nroRecibido = 0;
    var nroCancelado = 0;
    var nrototalcompra = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var t_OC = item;
    var t_OCImporte = 0;
    var nroBorrador = 0;
    var nroPedido   = 0;
    var nroRecibido = 0;
    var nroCancelado= 0;
    var nroPendiente= 0;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            Codigo 	    = node.childNodes[t++].firstChild.nodeValue;
            Local 	    = node.childNodes[t++].firstChild.nodeValue;
            Proveedor 	    = node.childNodes[t++].firstChild.nodeValue;
            Registro 	    = node.childNodes[t++].firstChild.nodeValue;
            Pedido 	    = node.childNodes[t++].firstChild.nodeValue;
            Entrega 	    = node.childNodes[t++].firstChild.nodeValue;
	    Recibido 	    = node.childNodes[t++].firstChild.nodeValue;
            Pago 	    = node.childNodes[t++].firstChild.nodeValue;
            Moneda 	    = node.childNodes[t++].firstChild.nodeValue;
            MonedaTexto	    = node.childNodes[t++].firstChild.nodeValue;
            Importe 	    = node.childNodes[t++].firstChild.nodeValue;
            ModoPago 	    = node.childNodes[t++].firstChild.nodeValue;
            Estado 	    = node.childNodes[t++].firstChild.nodeValue;
            Usuario 	    = node.childNodes[t++].firstChild.nodeValue;
            IdOrdenCompra   = node.childNodes[t++].firstChild.nodeValue;
            CambioMoneda    = node.childNodes[t++].firstChild.nodeValue;
            IdMoneda        = node.childNodes[t++].firstChild.nodeValue;
            IdProveedor     = node.childNodes[t++].firstChild.nodeValue;
            FCambioMoneda   = node.childNodes[t++].firstChild.nodeValue;
            FPrevista       = node.childNodes[t++].firstChild.nodeValue;
            FPago           = node.childNodes[t++].firstChild.nodeValue;
            Observaciones   = node.childNodes[t++].firstChild.nodeValue;
            IdLocal         = node.childNodes[t++].firstChild.nodeValue;
            OrdenCompraPago = node.childNodes[t++].firstChild.nodeValue;
	    
 	    if (Estado == 'Borrador') nroBorrador++; 
 	    if (Estado == 'Pendiente') nroPendiente++; 
	    if (Estado == 'Pedido')    nroPedido++; 
	    if (Estado == 'Recibido')  nroRecibido++; 
	    if (Estado == 'Cancelado') nroCancelado++;
	    if (IdMoneda == 1) t_OCImporte = parseFloat(t_OCImporte)+parseFloat(Importe);
	    if (IdMoneda == 2) t_OCImporte = parseFloat(t_OCImporte)+parseFloat(Importe*CambioMoneda);

            FuncionProcesaLinea(item,Codigo,Local,Proveedor,Registro,Pedido,Entrega,Recibido,Pago,Moneda,MonedaTexto,Importe,ModoPago,Estado,Usuario,IdOrdenCompra,IdProveedor,IdMoneda,CambioMoneda,FCambioMoneda,Observaciones,IdLocal,OrdenCompraPago);
		
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    id("TotalOrdenCompra").value            = t_OC;
    id("TotalOrdenCompraBorrador").value    = nroBorrador;
    id("TotalOrdenCompraPendiente").value   = nroPendiente;
    id("TotalOrdenCompraPedido").value      = nroPedido;
    id("TotalOrdenCompraCancelados").value  = nroCancelado;
    id("TotalOrdenCompraConfirmados").value = nroRecibido;
    id("TotalOrdenCompraImporte").value     = cMoneda[1]['S']+' '+formatDinero(t_OCImporte);
}

function AddLineaOrdenCompra(item,Codigo,Local,Proveedor,Registro,Pedido,Entrega,Recibido,Pago,Moneda,MonedaTexto,Importe,ModoPago,Estado,Usuario,IdOrdenCompra,IdProveedor,IdMoneda,CambioMoneda,FCambioMoneda,Observaciones,IdLocal,OrdenCompraPago){

    var lista = id("busquedaOrdenCompra");
    var xitem,xnumitem,xCodigo,xLocal,xProveedor,xRegistro,xPedido,xEntrega,xRecibido,xPago,xMoneda,xImporte,xModoPago,xEstado,xUsuario,xObservaciones,xOrdenCompraPago;
    var lobs = (Observaciones ==' ')?'':'...';

    var vPago   = (Pago)? Pago.split("~"):'';
    var xlPago  = (Pago)? vPago[0]:'';
    var xvPago  = (Pago)? vPago[1]:'';

    var vEntrega   = (Entrega)? Entrega.split("~"):'';
    var xlEntrega  = (Entrega)? vEntrega[0]:'';
    var xvEntrega  = (Entrega)? vEntrega[1]:'';


    xitem = document.createElement("listitem");
    xitem.value = IdOrdenCompra;
    xitem.setAttribute("id","lineabuscaordencompra_"+ilineabuscaordencompra);
    ilineabuscaordencompra++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",Codigo);
    xCodigo.setAttribute("style","text-align:center;font-weight:bold;");
    xCodigo.setAttribute("id","codigo_"+IdOrdenCompra);

    xIdOrdenCompra = document.createElement("listcell");
    xIdOrdenCompra.setAttribute("value",IdOrdenCompra);
    xIdOrdenCompra.setAttribute("collapsed","true");
    xIdOrdenCompra.setAttribute("id","ordencompra_"+IdOrdenCompra);

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",Local);
    xLocal.setAttribute("value",IdLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("id","local_"+IdOrdenCompra);

    xProveedor = document.createElement("listcell");
    xProveedor.setAttribute("label",Proveedor);
    xProveedor.setAttribute("value",IdProveedor);
    xProveedor.setAttribute("style","text-align:left;font-weight:bold;");
    xProveedor.setAttribute("id","proveedor_"+IdOrdenCompra);

    xRegistro = document.createElement("listcell");
    xRegistro.setAttribute("label", Registro);
    xRegistro.setAttribute("collapsed",vFechaRegistro);
    xRegistro.setAttribute("style","text-align:left");

    xPedido = document.createElement("listcell");
    xPedido.setAttribute("label", Pedido);
    xPedido.setAttribute("style","text-align:center");

    xEntrega = document.createElement("listcell");
    xEntrega.setAttribute("label", xlEntrega);	
    xEntrega.setAttribute("value", xvEntrega);	
    xEntrega.setAttribute("style","text-align:center;font-weight:bold;");
    xEntrega.setAttribute("id","entrega_"+IdOrdenCompra);

    xRecibido = document.createElement("listcell");
    xRecibido.setAttribute("label", Recibido);
    xRecibido.setAttribute("collapsed",vFechaRecibido);
    xRegistro.setAttribute("style","text-align:center");

    xPago = document.createElement("listcell");
    xPago.setAttribute("label", xlPago);	
    xPago.setAttribute("value", xvPago);
    xPago.setAttribute("collapsed",vFechaPago);
    xPago.setAttribute("style","text-align:center;font-weight:bold;");
    xPago.setAttribute("id","pago_"+IdOrdenCompra);

    xMoneda = document.createElement("listcell");
    xMoneda.setAttribute("value", Moneda);
    xMoneda.setAttribute("label", MonedaTexto);
    xMoneda.setAttribute("collapsed","true");
    xMoneda.setAttribute("id","moneda_"+IdOrdenCompra);

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value", IdMoneda);
    xIdMoneda.setAttribute("label", CambioMoneda);
    xIdMoneda.setAttribute("collapsed","true");
    xIdMoneda.setAttribute("id","idmoneda_"+IdOrdenCompra);

    xOrdenCompraPago = document.createElement("listcell");
    xOrdenCompraPago.setAttribute("value", OrdenCompraPago);
    xOrdenCompraPago.setAttribute("collapsed","true");
    xOrdenCompraPago.setAttribute("id","ordencomprapago_"+IdOrdenCompra);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", Moneda+' '+formatDinero(Importe));
    xImporte.setAttribute("style","text-align:right;font-weight:bold; ");
    xImporte.setAttribute("value",Importe);
    xImporte.setAttribute("id","importe_"+IdOrdenCompra);

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("collapsed",vFormaPago);
    xModoPago.setAttribute("style","text-align:center");
    xModoPago.setAttribute("id","modopago_"+IdOrdenCompra);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", Estado);
    xEstado.setAttribute("style","text-align:left;font-weight:bold;");
    xEstado.setAttribute("id","estado_"+IdOrdenCompra);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed", vUsuario);
    xUsuario.setAttribute("style","text-align:center");
    xUsuario.setAttribute("crop", "end");

    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label", lobs);
    xObservaciones.setAttribute("value",Observaciones );
    xObservaciones.setAttribute("collapsed",vObservaciones);
    xObservaciones.setAttribute("id","obs_"+IdOrdenCompra);
    xObservaciones.setAttribute("style","text-align:center");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xProveedor );
    xitem.appendChild( xEstado );
    xitem.appendChild( xImporte );
    xitem.appendChild( xModoPago );	
    xitem.appendChild( xRegistro );
    xitem.appendChild( xPedido );
    xitem.appendChild( xEntrega );	
    xitem.appendChild( xRecibido );	
    xitem.appendChild( xPago );	
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xMoneda );	
    xitem.appendChild( xIdOrdenCompra );
    xitem.appendChild( xIdMoneda );
    xitem.appendChild( xOrdenCompraPago);
    lista.appendChild( xitem );		
}

function RevisarOrdenCompraSeleccionada(){

    var idex = id("busquedaOrdenCompra").selectedItem;
    if(!idex) return;

    cCodigo        = id('codigo_'+idex.value).getAttribute('label');
    cProveedor     = id('proveedor_'+idex.value).getAttribute('label');
    cLocal         = id('local_'+idex.value).getAttribute('label');
    cIdProveedor   = id('proveedor_'+idex.value).getAttribute('value');
    cIdOrdenCompra = id('ordencompra_'+idex.value).getAttribute('value');
    cEntrega       = id('entrega_'+idex.value).getAttribute('value');
    cPago          = id('pago_'+idex.value).getAttribute('value');
    cMonedax       = id('moneda_'+idex.value).getAttribute('value');
    cMonedaTexto   = id('moneda_'+idex.value).getAttribute('label');
    cModopago      = id('modopago_'+idex.value).getAttribute('label');
    cOrdenCompra   = id('ordencompra_'+idex.value).getAttribute('value');
    cEstado        = id('estado_'+idex.value).getAttribute('label');
    cIdMoneda      = id('idmoneda_'+idex.value).getAttribute('value');
    cImporteOC     = id('importe_'+idex.value).getAttribute('value');
    cCambioMoneda  = id('idmoneda_'+idex.value).getAttribute('label');
    cOCPago        = id('ordencomprapago_'+idex.value).getAttribute('value');

    var verdet = (RevDet == 0 || RevDet != idex.value)? true:false;
    if(verdet || idetallesOrdenCompra == 0)
        setTimeout("loadDetallesOrdenCompra('"+idex.value+"')",100);

    RevDet = idex.value;
    xmenuOrdenCompra();
}

function loadDetallesOrdenCompra(xid){
    VaciarDetallesOrdenCompra();
    BuscarDetallesOrdenCompra(xid);
} 

function BuscarDetallesOrdenCompra(IdOrdenCompra ){

    RawBuscarDetallesOrdenCompra(IdOrdenCompra, AddLineaDetallesOrdenCompra);

}

function RawBuscarDetallesOrdenCompra(IdOrdenCompra,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();

    var url = "modordencompra.php?modo=mostrarDetallesOrdenCompra&IdOrdenCompra=" + escape(IdOrdenCompra);

    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia, Nombre,Talla, Color, Unidades, Descuento, PV, IdAlbaran;
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
                Referencia = node.childNodes[t++].firstChild.nodeValue;
                IdProducto = node.childNodes[t++].firstChild.nodeValue;
                CodigoBarras = node.childNodes[t++].firstChild.nodeValue;
                Producto = node.childNodes[t++].firstChild.nodeValue;
                Cantidad = node.childNodes[t++].firstChild.nodeValue;
                Costo = node.childNodes[t++].firstChild.nodeValue;
		IdOrdenCompraDet = node.childNodes[t++].firstChild.nodeValue;
		IdOrdenCompra = node.childNodes[t++].firstChild.nodeValue;
		VentaMenudeo = node.childNodes[t++].firstChild.nodeValue;
		Contenedor   = node.childNodes[t++].firstChild.nodeValue;
		UndContenedor= node.childNodes[t++].firstChild.nodeValue;
		UndMedida    = node.childNodes[t++].firstChild.nodeValue;

                FuncionRecogerDetalles(numitem,Referencia,IdProducto,CodigoBarras,
				       Producto,Cantidad,Costo,IdOrdenCompraDet,
				       IdOrdenCompra,VentaMenudeo,Contenedor,
				       UndContenedor,UndMedida);
            }
        }
    }
}

function AddLineaDetallesOrdenCompra(numitem,Referencia,IdProducto,CodigoBarras,
				     Producto,Cantidad,Costo,IdOrdenCompraDet,
				     IdOrdenCompra,VentaMenudeo,Contenedor,
				     UndContenedor,UndMedida){

    var lista = id("busquedaDetallesOrdenCompra");
    var xitem,xnumitem,xReferencia,xIdProducto,xCodigoBarras,xProducto,xCantidad,xCosto,xImporte,cResto,tUnidad,xDetalle;
    var Detalle = '';

    //Cantidad
    cResto    = (Cantidad%UndContenedor==Cantidad)?Cantidad:Cantidad%UndContenedor;
    tCantidad = ( VentaMenudeo=='1' )? Cantidad-cResto:Cantidad;
    tCantidad = ( VentaMenudeo=='1' )? Math.floor(tCantidad/UndContenedor):Cantidad;
    cResto    = ' + '+cResto;
    tCantidad = ( VentaMenudeo=='1' )? tCantidad+' '+Contenedor+''+cResto:Cantidad;
    tUnidad   = UndMedida;
    tCantidad = tCantidad+' '+tUnidad;
    Detalle   = ( VentaMenudeo=='1' )? Detalle+' '+UndContenedor+''+tUnidad+'x'+Contenedor:'';

    xitem = document.createElement("listitem");
    xitem.value = IdOrdenCompraDet;
    xitem.setAttribute("id","detalleordencompra_" + idetallesOrdenCompra);
    idetallesOrdenCompra++;

    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label", Detalle);
    xDetalle.setAttribute("id","detalle_"+IdOrdenCompraDet);

    xMenudeo = document.createElement("listcell");
    xMenudeo.setAttribute("value", VentaMenudeo);
    xMenudeo.setAttribute("collapsed","true");
    xMenudeo.setAttribute("id","menudeo_"+IdOrdenCompraDet);

    xContenedor = document.createElement("listcell");
    xContenedor.setAttribute("value", Contenedor);
    xContenedor.setAttribute("collapsed","true");
    xContenedor.setAttribute("id","contenedor_"+IdOrdenCompraDet);

    xUndContenedor = document.createElement("listcell");
    xUndContenedor.setAttribute("value", UndContenedor);
    xUndContenedor.setAttribute("collapsed","true");
    xUndContenedor.setAttribute("id","undcontenedor_"+IdOrdenCompraDet);

    xUnidad = document.createElement("listcell");
    xUnidad.setAttribute("value", tUnidad);
    xUnidad.setAttribute("collapsed","true");
    xUnidad.setAttribute("id","unidad_"+IdOrdenCompraDet);

    xIdOrdenCompraDet = document.createElement("listcell");
    xIdOrdenCompraDet.setAttribute("value", IdOrdenCompra);
    xIdOrdenCompraDet.setAttribute("collapsed","true");
    xIdOrdenCompraDet.setAttribute("id","ordencompradet_"+IdOrdenCompraDet);

    xReferencia = document.createElement("listcell");
    xReferencia.setAttribute("label", Referencia);

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label", Producto);
    xProducto.setAttribute("id","producto_"+IdOrdenCompraDet);

    xCodigoBarras = document.createElement("listcell");
    xCodigoBarras.setAttribute("label", CodigoBarras);
    xCodigoBarras.setAttribute("id","cb_"+IdOrdenCompraDet);

    xCantidad = document.createElement("listcell");
    xCantidad.setAttribute("label", tCantidad);
    xCantidad.setAttribute("value", Cantidad);
    xCantidad.setAttribute("id","cantidad_"+IdOrdenCompraDet);
    xCantidad.setAttribute("style","text-align:right");

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label", parseFloat(Costo).toFixed(2));
    xPrecio.setAttribute("style","text-align:right");
    xPrecio.setAttribute("id","precio_"+IdOrdenCompraDet);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", parseFloat(Costo*Cantidad).toFixed(2));
    xImporte.setAttribute("style","text-align:right");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xReferencia );
    xitem.appendChild( xCodigoBarras );
    xitem.appendChild( xProducto );
    xitem.appendChild( xDetalle );
    xitem.appendChild( xCantidad );
    xitem.appendChild( xPrecio );	
    xitem.appendChild( xImporte );	
    xitem.appendChild( xIdOrdenCompraDet );	
    xitem.appendChild( xMenudeo );
    xitem.appendChild( xContenedor );
    xitem.appendChild( xUndContenedor );
    xitem.appendChild( xUnidad );
    lista.appendChild( xitem );
}

function ImprimirOrdenCompraSeleccionada(tp){
    var idex = id("busquedaOrdenCompra").selectedItem;
    var idoc = idex.value;
    o_PrintOrdenCompra(idoc,tp);
}

function o_PrintOrdenCompra(idoc,tp){
    switch (tp) {
    case 'txt':
 	//obtenemos datos
	break;
    case 'pdf':
	//imprime pdf
	var importe       = id("importe_"+idoc).getAttribute("value");
	var moneda        = id("idmoneda_"+idoc).getAttribute("value");
	var importeletras = convertirNumLetras(importe,moneda);
	importeletras     = importeletras.toUpperCase();
	var url= "../fpdf/imprimir_ordencompra.php?idoc="+idoc+"&totaletras="+importeletras;
	location.href=url;

	break;
    default:
	alert("TPV:\n\n - "+po_servidorocupado+'. Acción Desconocida.');
    }
}

function getNuevoDatoOrdenCompra(xocs,campo,cvalue,mj,mja){

    //Inicia
    var xmj = (mj)?'\n\n- Ingrese correctamente'+mja+'\n\n':'';
    var xfh  = prompt("gPOS:\n\n"+
		       " Ingrese "+campo+":\n\n"+xmj, cvalue);

    //Cancelar
    if( xfh == null) return false;//Brutal termino el proceso!!! 

    switch (xocs) {

    case 9:

	//Valida lista a consolidar
	xfh  = trim(xfh);
	if( xfh == '' )
	    return getNuevoDatoOrdenCompra(xocs,campo,cvalue,true,mja);
	var afh = xfh.split(',');
	var nfh = false;
	var patron = /^\d*$/;    
	for(var i=0; i<afh.length; i++){
	    if ( patron .test(afh[i]) && afh[i] != '' ){
		if(!nfh) nfh = afh[i];
		else     nfh += ','+afh[i];
	    }
	}
	nfh = (nfh=='')?cvalue:nfh;
	xfh = nfh.split(',');
	if( nfh == cvalue || xfh.length < 2  )
	    return getNuevoDatoOrdenCompra(xocs,campo,cvalue,true,mja);//Inicia
	return nfh;//Termina
	break;

    }
}

function ModificarOrdenCompra(xocs,xdet){

    var lbox     = (xdet)?"busquedaDetallesOrdenCompra":"busquedaOrdenCompra";
    var idex     = id(lbox).selectedItem; 
    var idetx    = (xdet)?idex.value:false;//IdOrdenCompraDet:false 
    var idx      = (xdet)?id("ordencompradet_"+idetx).getAttribute("value"):idex.value;
    var codigo   = id("codigo_"+idx).getAttribute("label");
    var estado   = id("estado_"+idx).getAttribute("label");
    var msj      = false;
    var xdato    = 0;
    var xrest    = false;
    var xtdoc    = false;
    var xedit    = false;
    var reload   = true;
    var reloadet = true;
    var reloadod = false;
    var amsj     = 'gPOS: Acción restingida!\n\n '+
	           '-  Al modificar el Pedido Nro.'; 

    switch (xocs) {
    case 1:
	//Fecha Entrega
	xrest = (estado=='Recibido' || estado=='Cancelado')?true:false;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado diferente a -Recibido-');

	//Optiene fecha
 	xdato = id("xEntrega").value;
	if(cEntrega  != 'undefined')
	    if(xdato == cEntrega) return;

	id("entrega_"+cOrdenCompra).setAttribute("value",xdato);
	id("entrega_"+cOrdenCompra).setAttribute("label",xdato);

	reload   = false;
	reloadet = false;
	reloadod = false;
	volverOrdenCompras(0);

	if(!xdato) return;
	msj=' Modificar Pedido Nro.'+codigo+'\n\n '+
	    ' - Nueva Fecha de Entrega '+xdato;
	break;

    case 2:
	//Fecha Pago
	xrest = (estado=='Recibido' || estado=='Cancelado')?true:false;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       '      debe tener estado diferente a -Recibido-');
	//Optiene fecha
 	xdato = id("xPago").value;
	if(cEntrega  != 'undefined')
	    if(xdato == cPago) return;

	id("pago_"+cOrdenCompra).setAttribute("value",xdato);
	id("pago_"+cOrdenCompra).setAttribute("label",xdato);

	reload   = false;
	reloadet = false;
	reloadod = false;
	volverOrdenCompras(0);

	msj=' Modificar Pedido Nro.'+codigo+'\n\n '+
	    ' - Nueva Fecha de Pago '+xdato;
	break;

    case 3:
 	//Estado Borrador > Pendiente
	xrest = (estado=='Pendiente' || estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       '\n   debe tener estado Borrador o Pendiente. ');
 	//Estado Pendiente to Pedido
	xocs    = (estado=='Borrador')?xocs:4;
	xestado = (xocs==3)?'Pendiente':'Pedido';
	//Carga mesaje 
	msj='\n     Confirmar Pedido Nro.'+codigo+' -'+estado+'-\n\n '+
	    '    - Nuevo estado: '+xestado+'';
	break;

    case 5:
 	//Estado Cancelar
	xrest = (estado=='Recibido' || estado=='Cancelado')?true:false;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado diferente a -'+estado+'-');
	//Carga mesaje 
	msj=' Cancelar Pedido Nro.'+codigo+' -'+estado+
	    '-\n\n - Nuevo estado -Cancelado-';
	break;

    case 6:
 	//Costo Detalle
	xrest = (estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador-');
	//Carga Costo
	var xdato    = id("xPrecio").value;
	var producto = id("xProducto").value
	if( xdato < 0 ) return;

	//Carga mesaje 
	reloadod = true;
	volverOrdenCompras(0);

	msj=' Modificar Pedido Nro.'+codigo+' -'+estado+'-\n\n '+
	    ' Producto: '+producto+'.\n\n Nuevo Precio: '+cMonedax+' '+xdato;
	break;

    case 7:
 	//Cantidad Detalle
	xrest = (estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador-');
	var xUnidad     = id("unidad_"+idetx).getAttribute("value");
	var xMenudeo    = id("menudeo_"+idetx).getAttribute("value");
	var xContenedor = id("contenedor_"+idetx).getAttribute("value");
	var xCantidad   = id("cantidad_"+idetx).getAttribute("value");
	var xUndCont    = id("undcontenedor_"+idetx).getAttribute("value");
	var producto    = id("xProducto").value
	var esMenudeo   = (xMenudeo=='1')? true:false;
	var xEmpaques   = (esMenudeo)? parseFloat(id("xEmpaques").value):0;
	var xMenudencia = (esMenudeo)? parseFloat(id("xMenudencia").value):0;
	var xMenudencia = ( esMenudeo && xUndCont > xMenudencia )? xMenudencia:0;
	//Carga Cantidad
	var mdato       = (esMenudeo)? parseFloat(xEmpaques*xUndCont)+parseFloat(xMenudencia):0;
	var xdato       = (esMenudeo)? mdato:id("xCantidad").value;
	var vdato       = (esMenudeo)? xEmpaques+' '+xContenedor+' '+xMenudencia:xdato;


	//Termina Brutall
	if( xdato == xCantidad ) return;

	//Carga mesaje 
	reloadod = true;
	volverOrdenCompras(0);
	msj      = ' Modificar Pedido Nro.'+codigo+' -'+estado+'-\n\n '+
	           ' Producto: '+producto+' \n\n   Nueva Cantidad:  '+vdato+' '+xUnidad;
	break;

    case 8:
 	//Quitar Detalle
	//Estado Borrador to Pendiente
	//Control
	xrest = (estado=='Borrador')?false:true;

	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador-');
	//Carga mesaje 
	msj=' Quitar Producto del Pedido Nro.'+codigo+
	    ' -'+estado+'-';
	break;

    case 9:
 	//Consolidar 
	xrest = (estado!='Borrador')?true:false;//Control
	if(xrest)
	    return alert(amsj+codigo+' -'+estado+'-, debe tener estado  -Borrador-');
	//Carga dato
	xdato = getNuevoDatoOrdenCompra(xocs,
					'los codigos de las Ordenes de Compra a Consolidar:\n'+
					'       - Codigos entre comas',
					codigo+',',
					false,
					' los codigos');
	if(!xdato) return;
	//Carga mesaje 
	msj='\n\n Consolidar las Ordenes de Compra -'+xdato+
	    '- en -'+codigo+'-';
	break;

    case 10:
 	//Regresar a Borrador de pendiente
	if(estado=='Pendiente'||estado=='Cancelado') return ModificarOrdenCompra(16,false);

 	//Editar
	xrest = (estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador-');
	//Carga mesaje 
	msj=' Editar Pedido Nro.'+codigo+' -'+estado+'-';
	xedit = true;
	break;

    case 11:
 	//Recibir Factura
	xrest = ( estado == 'Pedido')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Pedido-');
	//Carga mesaje 
	msj='\n     Recibir Factura del Pedido Nro.'+codigo+' -'+estado+'-';
	xedit = true;
	break;
    case 12:
 	//Recibir Boleta
	xrest = ( estado == 'Pedido')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Pedido-');
	//Carga mesaje 
	msj='\n     Recibir Boleta del Pedido Nro.'+codigo+' -'+estado+'-';
	xedit = true;
	break;
    case 13:
 	//Recibir Albaran
	xrest = ( estado == 'Pedido')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Pedido-');
	//Carga mesaje 
	msj='\n     Recibir Albaran del Pedido Nro.'+codigo+' -'+estado+'-';
	xedit = true;
	break;
    case 14:
 	//Recibir Ticket
	xrest = ( estado == 'Pedido')?false:true;
	if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
			       ' debe tener estado -Pedido-');
	//Carga mesaje 
	msj='\n     Recibir Ticket del Pedido  Nro.'+codigo+' -'+estado+'-';
	xedit = true;
	break;
    case 15:
 	//Observaciones
	xrest = (estado=='Recibido')?true:false;//Control
	if(xrest)
	    return alert(amsj+codigo+' -'+estado+
			 '-,   debe tener estado diferente  -Recibido-');
 	//Carga dato
	xdato = id("xObservacion").value;

	//Observaciones
	if( xdato == '' ) return;
	//reload   = true;
	reloadet = false;
	reloadod = true;
	volverOrdenCompras(0);

	//Carga mesaje 
	msj='\n Agregar la observación: \n\n - '+xdato+
	    '\n\n en el Pedido Nro.'+codigo+' -'+estado+'- ';
	break;
    case 16:
 	//Regresar a Borrador de pendiente
	//Carga mesaje 
	msj=' Modificar Pedido Nro.'+codigo+' -'+estado+
	    '-\n\n - Nuevo estado -Borrador-';
	break;


    case 17:
 	//Proveedor & Editar
	xrest = (estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Lista Proveedores
 	closepopup();

	//Dato Global IdProveedor Select
	if(!ProveedorPost) return;

	if(cProveedor == ProveedorPost) return;

	xdato = IdProveedorPost;

	//Mensaje 
	msj=' Modificar el Proveedor -'+cProveedor+'- '+cDocumento+
	    ' Nro.'+codigo+' -'+estado+'-\n\n '+
	    '- Nuevo Proveedor '+ProveedorPost;
	id("proveedor_"+idx).setAttribute('label',ProveedorPost);
	id("ProvHab").value = ProveedorPost;

	//Clean
	ProveedorPost   = false;
	IdProveedorPost = 0;

	reloadet        = false;
	reload          = false;
	reloadod        = false;
	volverOrdenCompras(0);
	//return alert(xdato);
	break;

    case 18:
 	//Tipo Pago
	xrest = (estado=='Borrador')?false:true;
	if(xrest) return alert(amsj+cCodigo+' -'+estado+'-,'+
			       ' debe tener estado -Borrador- ó -Pendiente-');
	//Carga mesaje 
	xdato = id("modopago_"+idx).getAttribute("label");
	xdato = (xdato=='Contado')?'Credito':'Contado';

	id("modopago_"+cOrdenCompra).setAttribute("label",xdato);
	reload   = false;
	reloadet = false;

	volverOrdenCompras(0);

	msj=' Modificar  Tipo Pago '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+estado+
	    '-\n\n - Nuevo Tipo Pago -'+xdato+'-';
	break;

    case 19:

 	//Almacen
	var xrest  = (estado=='Pendiente' || estado=='Borrador')?false:true;
	var xlocal = id("local_"+cIdOrdenCompra).getAttribute("value");	

	if(xrest)
	{ 
	    id("xOrdenCompraLocal").value=xlocal;
	    return alert(amsj+cCodigo+' - '+estado+','+
			 ' debe tener estado -Borrador- ó -Pendiente-');
	}
	//Lista Almacen
	//var xlocal = id("idlocal_"+cIdPedido).getAttribute("value");
	var xdato  = id("xOrdenCompraLocal").getAttribute("value");
	var llocal = id("xOrdenCompraLocal").getAttribute("label");

	//xComprobantes?
	if( xlocal == xdato ) return;

	volverOrdenCompras(0);
	//Mensaje 
	msj=' Modificar el Local '+cLocal+'  de '+cDocumento+
	    ' Nro.'+cCodigo+' - '+estado+'\n\n '+
	    '- Nuevo Local '+llocal+'';
	//Clean
	reload          = true;
	reloadet        = false;
	break;
    case 20:
 	//Clonar
	xedit = true;

	msj=' Clonar Pedido '+cDocumento+' Nro.'+cCodigo+
	    ' '+cProveedor+' -'+estado+'-';
	break;
    }

    //Control 
    if(!confirm('gPOS: '+msj+','+
		' ¿desea continuar?')) 
	return resetFormModificarOrdenCompra(xocs);

    //Ejecuta
    var url="modordencompra.php?"+
	"modo=ModificarOrdenCompra"+
	"&xid="+idx+
	"&xidet="+idetx+
	"&xdato="+xdato+
	"&xocs="+xocs;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    if(isNaN(xrequest.responseText))
	alert(po_servidorocupado+'\n\n '+xrequest.responseText);

    //Termina verCarritoCompra
    if(xedit)
	return  parent.solapa('modulos/compras/xulcompras.php?modo=entra','Compras - Presupuestos','compras');

    //Termina OrdenCompra
    if(reload)   BuscarOrdenCompra();    
    if(reloadet) VaciarDetallesOrdenCompra();
    if(reloadod) buscarPorOrdenCompra(codigo);
}

function VerObservOrdenCompra(){
    var idex   = id("busquedaOrdenCompra").selectedItem; 
    var idx    = idex.value;//IdOrdenCompraDet:false 
    var codigo = id("codigo_"+idx).getAttribute("label");
    var estado = id("estado_"+idx).getAttribute("label");
    var xobs   = id("obs_"+idx).getAttribute("value");
    //Items?
    var xrest  = (xobs != ' ')?false:true;
    var aobs;

    //Sin Item
    if(xrest) xobs = '\n\n                          - Sin observaciones - ';

    //Item 
    if(!xrest)
    {
	aobs = xobs.split('-');
	xobs = '';
	for(x in aobs){ if(x != 0 && aobs[x]!='' ) xobs += '\n        -'+aobs[x];}
    }

    //Termina
    return alert('gPOS:\n\n'+
		 '     Observaciones Pedido Nro.'+codigo+' -'+estado+'-\n'+
		 '     '+xobs+' \n ');
}

function ModificarOrden(){

    if(cEstado == 'Recibido') return false; 
    if(cEstado == 'Cancelado') return false; 

    id("xOrdenCompra").value      = cDocumento;
    id("xCodigo").value           = cCodigo;
    id("ProvHab").value           = cProveedor;
    id("xFormaPago").value        = cModopago;
    id("xOrdenCompraLocal").value = id("local_"+cIdOrdenCompra).getAttribute("value");	
    if (cEntrega  != 'undefined') id("xEntrega").value = cEntrega;
    if (cPago != 'undefined')	  id("xPago").value = cPago;

    id("listboxOrdenCompras").setAttribute("collapsed","true");
    id("formularioOrdenCompra").setAttribute("collapsed","false");
    id("boxBusquedaOrdenCompra").setAttribute("collapsed","true");
    id("boxResumenPedido").setAttribute("collapsed","true");
    id("boxTitlePedido").setAttribute("collapsed","true");
}

function ModificarOrdenDetalle(){

    if(cEstado == 'Recibido') return false; 
    if(cEstado == 'Cancelado') return false; 

    //GetValues
    var detx           = id("busquedaDetallesOrdenCompra").selectedItem; 
    var idetx          = detx.value
    var xOrdenDetalle  = cDocumento+' Nro '+cCodigo+'  '+cProveedor;
    var xProducto      = id("producto_"+idetx).getAttribute("label");
    var xCodigoBarras  = id("cb_"+idetx).getAttribute("label");
    var xPrecio        = id("precio_"+idetx).getAttribute("label");
    var xCantidad      = id("cantidad_"+idetx).getAttribute("value");
    var xDetalle       = id("detalle_"+idetx).getAttribute("label");
    var xUnidad        = id("unidad_"+idetx).getAttribute("value");
    var xMenudeo       = id("menudeo_"+idetx).getAttribute("value");
    var xContenedor    = id("contenedor_"+idetx).getAttribute("value");
    var xUndContenedor = id("undcontenedor_"+idetx).getAttribute("value");
    var xResto         = (xCantidad%xUndContenedor==xCantidad)? 0:xCantidad%xUndContenedor;
    var xEmpaques      = (xMenudeo=='1')? (xCantidad-xResto)/xUndContenedor:false;
    var esMenudeo      = (xMenudeo=='1')? false:true;
    var noMenudeo      = (esMenudeo)? false:true;

    //SetValues
    id("xContenedor").value   = xContenedor;
    id("xmUnidades").value    = xUnidad;
    id("xcUnidades").value    = xUnidad;
    id("xEmpaques").value     = xEmpaques;
    id("xMenudencia").value   = xResto;
    id("xMoneda").value       = cMonedaTexto;
    id("xOrdenDetalle").value = xOrdenDetalle;
    id("xProducto").value     = xCodigoBarras+' '+xProducto;
    id("xPrecio").value       = xPrecio;
    id("xCantidad").value     = xCantidad;
    id("xDetalle").value      = xDetalle;
    
    //PrintValues
    id("esMenudeo").setAttribute("collapsed",esMenudeo);
    id("noMenudeo").setAttribute("collapsed",noMenudeo);
    id("listboxOrdenCompras").setAttribute("collapsed","true");
    id("formularioOrdenCompra").setAttribute("collapsed","true");
    id("formularioDetalleOrdenCompra").setAttribute("collapsed","false");

    id("boxBusquedaOrdenCompra").setAttribute("collapsed","true");
    id("boxResumenPedido").setAttribute("collapsed","true");
    id("boxTitlePedido").setAttribute("collapsed","true");
}


function volverOrdenCompras(xcheck){

    id("listboxOrdenCompras").setAttribute("collapsed","false");
    id("formularioOrdenCompra").setAttribute("collapsed","true");
    id("formularioDetalleOrdenCompra").setAttribute("collapsed","true");

    id("boxBusquedaOrdenCompra").setAttribute("collapsed","false");
    id("boxResumenPedido").setAttribute("collapsed","false");
    id("boxTitlePedido").setAttribute("collapsed","false");

    if(xcheck == 1) validarFechaCambio();   // Fecha Pedido

}

function resetFormModificarOrdenCompra(xocs){
    switch (xocs) {
    case 1:
	//Fecha Facturacion

	var setEntrega = (cEntrega  != 'undefined')? cEntrega:'';
	id("entrega_"+cOrdenCompra).setAttribute("value",setEntrega);
	id("entrega_"+cOrdenCompra).setAttribute("label",setEntrega);
	break;
    case 2:
	//Fecha Pago
	var setPago = (cPago  != 'undefined')? cPago : '';
	id("pago_"+cOrdenCompra).value = setPago;
	id("pago_"+cOrdenCompra).setAttribute("label",setPago);
	break;

    case 17:
 	//Proveedor
	id("proveedor_"+cOrdenCompra).setAttribute('label',cProveedor);
	break;
    case 18:
	id("modopago_"+cOrdenCompra).setAttribute("label",cModopago);
	break;
    }

}

function AddOrdenCompra(){
    var idex = id("busquedaOrdenCompra").selectedItem;
    if(!idex) return;
    if(cEstado != 'Pedido') return;
    var xmsj  = "Pedido: "+cCodigo+" - "+cProveedor+"\n";
    xmsj += "Monto: "+cMonedax+formatDinero(cImporteOC)+"\n\n";
    var xpago = 0;
    if(cEstado == 'Pedido'){

	if(cOCPago != 0){
	    var xopoc  = cOCPago.split("~~");
	    var xopimp = xopoc[0].split(",");
	    if(xopoc[1] >= cImporteOC ) return;
	    xmsj += "Operaciones realizadas: \n";
	    for(var i = 0;i < xopimp.length;i++){
		var ximp = xopimp[i].split("-");
		xmsj += " - "+ximp[0]+" ("+cMonedax+formatDinero(ximp[1])+")\n";
	    }
	    xmsj += '\n   Total Operaciones = '+cMonedax+formatDinero(xopoc[1])+'\n\n';
	    xpago = parseFloat(xopoc[1]);
	}else{
	    xmsj += "Operaciones realizadas: \n - Sin Operaciones \n\n";
	}
    }

    if(xmsj || cOCPago == 0){
	if(!confirm('gPOS:  Pago Pedidos \n\n'+xmsj+
		    '¿desea continuar?')) 
	    return;
    }
    
    if((cImporteOC - xpago) <= 0) return;

    var url  = "../pagoscobros/modpagoscobros.php?xmodo=pedidoc&"+
	       "xorden="+cIdOrdenCompra+"&"+
	       "xprov="+cIdProveedor+"&"+
	       "xidm="+cIdMoneda+"&"+
   	       "ximpoc="+(cImporteOC-xpago)+"&"+
	       "xcm="+cCambioMoneda+"&"+
	       "modo=verPagosProveedorDoc";

    var boxweb    = id("boxOrdenCompra");
    var listcomp  = id("listboxOrdenCompras");
    var boxframe  = id("webOrdenCompra");

    boxframe.setAttribute("src",url);  
    listcomp.setAttribute("collapsed","true");  
    boxweb.setAttribute("collapsed","false");  

}

function volverOrdenCompra(){
    setTimeout("BuscarOrdenCompra()",100);

    var boxweb    = id("boxOrdenCompra");
    var listcomp  = id("listboxOrdenCompras");
    var boxframe  = id("webOrdenCompra");

    boxframe.setAttribute("src","about:blank");  
    listcomp.setAttribute("collapsed","false");  
    boxweb.setAttribute("collapsed","true");  
}

function xmenuOrdenCompra(){
//cEstado
    var editar       = true;
    var modificar    = true;
    var consolidar   = true;
    var confirmar    = true;
    var cancelar     = true;
    var modificardet = true;
    var quitardet    = true;
    var agregarpago  = true;
    var recibir      = true;

    switch(cEstado){
    case 'Borrador':
	editar       = false;
	modificar    = false;
	consolidar   = false;
	confirmar    = false;
	cancelar     = false;
	modificardet = false;
	quitardet    = false;
	break;
    case 'Pendiente':
	editar       = false;
	modificar    = false;
	confirmar    = false;
	cancelar     = false;
	modificardet = false;
	quitardet    = false;
	break;
    case 'Pedido':
	modificar    = false;
	agregarpago  = (cOCPago == 0)? false:true;
	var xocpago  = (agregarpago)? cOCPago.split("~~"):false;
	xocpago[1]   = (xocpago)? xocpago[1]:0;
	agregarpago  = ((xocpago[1] < cImporteOC) || cOCPago == 0)? false:true;
	recibir      = false;
	break;
    case 'Recibido':
	break;
    case 'Cancelado':
	editar       = false;
	break;

    }

    id("mheadModifica").setAttribute("disabled",modificar);
    id("mheadEdita").setAttribute("disabled",editar);
    id("mheadConsolida").setAttribute("disabled",consolidar);
    id("mheadAgregaPago").setAttribute("disabled",agregarpago);
    id("mheadConfirma").setAttribute("disabled",confirmar);
    id("mheadRecibe").setAttribute("disabled",recibir);
    id("mheadCancela").setAttribute("disabled",cancelar);
    id("mdetModifica").setAttribute("disabled",modificardet);
    id("mdetQuita").setAttribute("disabled",quitardet);
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Estado": 
	vEstado        = xchecked;
	break;
    case "Fecha_Entrega":
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
    case "Fecha_Pago" : 
	vFechaPago     = xchecked;
	break;
    case "Fecha_Recibido":
	vFechaRecibido = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarOrdenCompra();
}

function validarFechaCambio(){

    var xFEntrega =  id("xEntrega").value;
    var xFPago    =  id("xPago").value;
    // Fecha Entrega
    var xFecha1   = xFEntrega.replace(/-/g,',');
    var xFecha2   = cEntrega.replace(/-/g,',');
    
    if(compararIgualdadFechas(xFecha1, xFecha2))
	ModificarOrdenCompra(1);
    
    // Fecha Pago
    xFecha1   = xFPago.replace(/-/g,',');
    xFecha2   = cPago.replace(/-/g,',');
    
    if(compararIgualdadFechas(xFecha1, xFecha2)) 
	ModificarOrdenCompra(2);
    
}

function OrdenCompraToProforma(){

    //Lista Clientes
    closepopup();

    if(!ClientePost) return;
    if(IdClientePost <= 2) return;
    var msj = "Se creará una nueva proforma para el cliente "+ClientePost;
    if(!confirm('gPOS: Ventas - Pedidos \n\n     '+msj+','+
		' ¿desea continuar?')) 
	return;

    var url="../../services.php?"+
	"modo=creaProforma"+
	"&xidoc="+cIdOrdenCompra+
	"&xidc="+IdClientePost+
	"&cod="+cCodigo;
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    
    var res  = xrequest.responseText;
    var ares = res.split("~");
    
    if(ares[0] != '')
	return alert(po_servidorocupado+'\n\n '+ares[0]);

    alert("gPOS: Ventas - Pedidos \n\n   PROFORMA : "+ares[1]+" \n   CLIENTE    : "+ClientePost+"\n\n Se creó nueva Proforma");
}

/*+++++++popup div++++++++++*/
function CogeProvHab() { popup('../../modulos/proveedores/selproveedor.php?modo=proveedorpost','proveedorhab');  }
function CogeCliente() { popup('../../modulos/clientes/selcliente.php?modo=clientepost','proveedorhab');  }
function loadProvHab() { ModificarOrdenCompra(17); }
function proformarOrdenCompra(){ CogeCliente(); }
function loadCliente(){ OrdenCompraToProforma();}
