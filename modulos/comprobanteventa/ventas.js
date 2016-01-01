/*=========== REVISION VENTAS   ==============*/	

var idetallesVenta        = 0;
var cIdComprobanteVenta        = 0;
var cComprobanteVenta          = '';
var cSerieNroComprobanteVenta  = '';
var cClienteComprobanteVenta   = '';
var cIdClienteComprobanteVenta = 0;
var cMontoComprobanteVenta     = 0;
var cPendienteComprobanteVenta = 0;
var cIdLocalVenta              = 0;
var cIdSuscripcionVenta        = 0;
var cReservado                 = 0;

var RevDet = 0;

// Busqueda abanzada
var vFormaVenta    = true;
var vMoneda        = true;
var vUsuario       = true;
var vOP            = true;
var vCodigo        = true;
var vFechaRegistro = true;
var serialNum      = (Math.random()*9000).toFixed();

//Pagos comprobantes clientes
var idetallescobro = 0;

function VaciarDetallesVentas(){
    var lista = id("busquedaDetallesVenta");
    
    for (var i = 0; i < idetallesVenta; i++) { 
        kid = id("detalleventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesVenta = 0;
}

function VaciarBusquedaVentas(){
    var lista = id("busquedaVentas");
    
    for (var i = 0; i < ilineabuscaventas; i++) { 
        kid = id("lineabuscaventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscaventas = 0;
}

function VaciarDetallesCobros(){
    var lista = id("busquedaDetallesCobro");
    
    for (var i = 0; i < idetallescobro; i++) { 
        kid = id("detallecobro_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallescobro = 0;
}

function RevisarVentaSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    cIdComprobanteVenta        = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    cComprobanteVenta          = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    cSerieNroComprobanteVenta  = idex.childNodes[5].attributes.getNamedItem('label').nodeValue;
    cClienteComprobanteVenta   = idex.childNodes[6].attributes.getNamedItem('label').nodeValue;
    cIdClienteComprobanteVenta = idex.childNodes[6].attributes.getNamedItem('value').nodeValue;
    cMontoComprobanteVenta     = idex.childNodes[10].attributes.getNamedItem('label').nodeValue;
    cPendienteComprobanteVenta = idex.childNodes[11].attributes.getNamedItem('label').nodeValue;
    cIdLocalVenta              = idex.childNodes[15].attributes.getNamedItem('value').nodeValue;
    cIdSuscripcionVenta        = idex.childNodes[16].attributes.getNamedItem('value').nodeValue;
    cReservado                 = id("venta_reservado_"+idex.value).getAttribute("value");

    menuContextualVentasRealizadas(cIdComprobanteVenta);

    var verdet = (RevDet == 0 || RevDet != idex.value)? true:false;
    var esfin  = (esFinanzas)? idetallescobro:idetallesVenta;

    if(verdet || esfin == 0)
        setTimeout("loadDetallesVentas('"+idex.value+"')",100);

    RevDet = idex.value;
}
 
function loadDetallesVentas(xid){
    (esFinanzas)? VaciarDetallesCobros()   : VaciarDetallesVentas();
    (esFinanzas)? BuscarDetallesCobro(xid) : BuscarDetallesVenta(xid);
} 

function RevisarDetalleVentaSeleccionada(){

    var idex      = id("busquedaDetallesVenta").selectedItem;

    if(!idex) return;

    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
    var esSeries = ( mseries != 'false' )? false:true;

    id("VentaRealizadaDetalleNS").setAttribute("disabled",esSeries);
    id("VentaRealizadaDetalleMProducto").setAttribute("disabled",esSeries);


}


function verNSVentaSeleccionada(){

    var idex      = id("busquedaVentas").selectedItem;
    if(!idex) return;
    var idcomp    = idex.childNodes[1].attributes.getNamedItem('id').nodeValue.replace('venta_serie_','');
    var idex      = id("busquedaDetallesVenta").selectedItem;
    var cod       = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    var producto  = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
    var reporte   = '';
    var bandera   = 0;

    if(!idex) return;
    var esSeries = ( mseries != 'false' )? mseries:false;

    if (esSeries ) {

	var arrseries = esSeries.split(';');

	for( var j=0;j<arrseries.length;j++)
	{
	    reporte   = reporte+'\n       - '+arrseries[j];
	    bandera=1;
	}

 	//lanza mesaje Resumen
	if(parseInt(reporte) != 0 ) 
	    alert ( 'gPOS: NUMEROS DE SERIE \n\n Producto: '+ producto +'\n N/S:'+ reporte);
    }		
    //Si no es MProducto
    if(parseInt(bandera) != 1 )
	return alert ( " gPOS: NUMEROS DE SERIE\n"+
		       "      "+
		       "- No es un Producto con N/S, elija otro.");
}

function verDetMPSeleccionada(){
    
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;
    var idcomp = idex.childNodes[1].attributes.getNamedItem('id').nodeValue.replace('venta_serie_','');
    var idex      = id("busquedaDetallesVenta").selectedItem;
    var cod       = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    var mproducto = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
    var reporte   = '';
    var xdetalle  = '';
    var bandera   = 0;

    if(!idex) return;
    var esSeries = ( mseries != 'false' )? mseries:false;

    if (esSeries ) {

	var arrseries = esSeries.split(';');

	for( var j=0;j<arrseries.length;j++)
	{
	    xdetalle = row_mostrardetalleMProducto(arrseries[j],cod+' '+mproducto);
	    if(xdetalle) 
	    {
		reporte  = reporte+'\n\n '+xdetalle;
		bandera  += 1;
	    }
	}

 	//lanza mesaje Resumen
	if(parseInt(bandera) != 0 )  alert ( ' gPOS: META PROUCTO ' + reporte);
    }		
    //Si no es MProducto
    if(parseInt(bandera) != 1 )
	return alert ( " gPOS: META PROUCTO\n\n"+
		       "      "+
		       "- No es un Meta Producto, elija otro.");
    
}

function row_mostrardetalleMProducto(CBMP,MProducto){

    var cadena, filas;
    var xrequest = new XMLHttpRequest();
    var url      =   
	"../../services.php"+"?"+
	"modo=mostrardetalleMProducto"+"&"+
	"cbmp="+CBMP;
    xrequest.open("GET",url,false);
    xrequest.send(null);
    cadena = xrequest.responseText;

    //lanza mesaje Resumen
    if( trim(cadena) != '' )
	return "\n - "+ CBMP+" "+MProducto+" - \n"+cadena;
    return false;
}

function BuscarDetallesVenta(IdComprobanteVenta ){
    
    RawBuscarDetallesVenta(IdComprobanteVenta, AddLineaDetallesVenta);
    
}

function RawBuscarDetallesVenta(IdComprobanteVenta,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var url = "../../services.php?modo=mostrarDetallesVenta&IdComprobante=" + escape(IdComprobanteVenta)
        + "&r=" + serialNum;		
    serialNum++;		

    obj.open("GET",url,false);
    obj.send(null);	
    
    var tex = "";
    var cr = "\n";
    var Referencia,Nombre,Talla,Color,Unidades,Precio,Descuento,PV,Codigo,CodBar,Descripcion,Lab,Marca,Serie,Lote,Vence,Servicio,MProducto,Menudeo,Cont,UnidxCont,Unid,Costo,IdPedidoDet,IdComprobanteVentaDet;
    var node,t,i;
    var numitem = 0;
    if (!obj.responseXML) return alert('gPOS: '+po_servidorocupado);		
    
    var xml = obj.responseXML.documentElement;
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node && node.childNodes && node.childNodes.length >0){
	    t = 0;
	    numitem++;
	    if (node.childNodes[t].firstChild){
                Referencia  = node.childNodes[t++].firstChild.nodeValue;
                Nombre      = node.childNodes[t++].firstChild.nodeValue;
                Talla       = node.childNodes[t++].firstChild.nodeValue;
                Color       = node.childNodes[t++].firstChild.nodeValue;
                Unidades    = node.childNodes[t++].firstChild.nodeValue;
		Precio      = node.childNodes[t++].firstChild.nodeValue;
                Descuento   = node.childNodes[t++].firstChild.nodeValue;
                PV          = node.childNodes[t++].firstChild.nodeValue;
                Codigo      = node.childNodes[t++].firstChild.nodeValue;
                CodBar      = node.childNodes[t++].firstChild.nodeValue;
                Lab         = node.childNodes[t++].firstChild.nodeValue;
                Marca       = node.childNodes[t++].firstChild.nodeValue;
                Serie       = node.childNodes[t++].firstChild.nodeValue;
                Lote        = node.childNodes[t++].firstChild.nodeValue;
                Vence       = node.childNodes[t++].firstChild.nodeValue;
                Servicio    = node.childNodes[t++].firstChild.nodeValue;
                MProducto   = node.childNodes[t++].firstChild.nodeValue;
                Menudeo     = node.childNodes[t++].firstChild.nodeValue;
		Cont        = node.childNodes[t++].firstChild.nodeValue;
                UnidxCont   = node.childNodes[t++].firstChild.nodeValue;
                Unid        = node.childNodes[t++].firstChild.nodeValue;
                IdComprobanteVentaDet   = node.childNodes[t++].firstChild.nodeValue;
                IdPedidoDet = node.childNodes[t++].firstChild.nodeValue;
		Costo       = node.childNodes[t++].firstChild.nodeValue;
		
                FuncionRecogerDetalles(CodBar,Nombre,Talla,Color,Unidades,Descuento,PV,
				       Codigo,Lab,Marca,Serie,Lote,Vence,
				       Referencia,Precio,Servicio,MProducto,Menudeo,Cont,
				       UnidxCont,Unid,IdComprobanteVentaDet,numitem,IdPedidoDet,
				       Costo);
	    }
        }
    }
}

function AddLineaDetallesVenta(CodBar, Nombre,Talla, Color, unidades, Descuento,
			       PV,Codigo,Lab,Marca,serie,lote,vence,
			       Referencia,Precio,servicio,mproducto,menudeo,cont,
			       unidxcont,unid,IdComprobanteVentaDet,numitem,IdPedidoDet,Costo){
    
    // cod = prodCod[Codigo-1];

    var lista = id("busquedaDetallesVenta");
    var xitem, xReferencia,xNombre,xTalla,xColor,xUnidades,xDescuento,xPV,xSerie,xLote,xVencimiento,xDetalle,xIdProducto,xIdPedidoDet,xCosto;

    var xresto    = ( menudeo == 1)? unidades%unidxcont                    : false;
    var xcant     = ( menudeo == 1)? ( unidades - xresto )/unidxcont       : false;
    var xcont     = ( menudeo == 1)? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
    var xmenudeo  = ( menudeo == 1)? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;


    var vdetalle  = ( mproducto == 1)? '**MIXPRODUCTO** '       : '';
    var vdetalle  = ( menudeo   == 1)? vdetalle+xmenudeo      : vdetalle;
    var vdetalle  = ( serie!='false')? vdetalle+'NS. '+serie.slice(0,120)+' ' : vdetalle;
    var vdetalle  = ( vence!='false')? vdetalle+'FV. '+vence + ' ' : vdetalle;
    var vdetalle  = ( lote !='false' )? vdetalle+'LT. '+lote  + ' ' : vdetalle;
    var vdetalle  = ( servicio  == 1)? '**SERVICIO**' : vdetalle;

    var NombreProducto = Nombre+" "+Marca+" "+Color+" "+Talla+" "+Lab;
    
    xitem = document.createElement("listitem");
    xitem.value = IdComprobanteVentaDet;
    xitem.setAttribute("id","detalleventa_" + idetallesVenta);
    idetallesVenta++;
    
    xReferencia = document.createElement("listcell");
    xReferencia.setAttribute("label", Referencia);
    xReferencia.setAttribute("id","xdetalleventa_referencia_"+IdComprobanteVentaDet);

    xCodBar = document.createElement("listcell");
    xCodBar.setAttribute("label", CodBar);
    xCodBar.setAttribute("id","xdetalleventa_codigobarra_"+IdComprobanteVentaDet);
    
    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");
    
    xNombre = document.createElement("listcell");
    xNombre.setAttribute("label", Nombre);
    xNombre.setAttribute("id","xdetalleventa_nombre_"+IdComprobanteVentaDet);
    
    xTalla = document.createElement("listcell");
    xTalla.setAttribute("label", NombreProducto);
    xTalla.setAttribute("id","xdetalleventa_concepto_"+IdComprobanteVentaDet);
    
    xColor = document.createElement("listcell");
    xColor.setAttribute("label", Color);
    
    xUnidades = document.createElement("listcell");
    xUnidades.setAttribute("label", unidades+" "+unid);
    xUnidades.setAttribute("value",unidades);
    xUnidades.setAttribute("style","text-align:right");
    xUnidades.setAttribute("id","xdetalleventa_unidades_"+IdComprobanteVentaDet);

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label", formatDinero(parseFloat(Precio).toFixed(2)));
    xPrecio.setAttribute("value",Precio);
    xPrecio.setAttribute("style","text-align:right");
    xPrecio.setAttribute("id","xdetalleventa_precio_"+IdComprobanteVentaDet);

    xCosto = document.createElement("listcell");
    xCosto.setAttribute("value",Costo);
    xCosto.setAttribute("collapsed","true");
    xCosto.setAttribute("style","text-align:right");
    xCosto.setAttribute("id","xdetalleventa_costo_"+IdComprobanteVentaDet);
    
    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label", parseFloat(Descuento).toFixed(2));
    xDescuento.setAttribute("value", Descuento);
    xDescuento.setAttribute("style","text-align:right");
    xDescuento.setAttribute("id","xdetalleventa_descuento_"+IdComprobanteVentaDet)
    
    xPV = document.createElement("listcell");
    xPV.setAttribute("label", formatDinero(parseFloat(PV).toFixed(2)));
    xPV.setAttribute("value",PV);
    xPV.setAttribute("style","text-align:right");
    xPV.setAttribute("id","xdetalleventa_pv_"+IdComprobanteVentaDet)
    
    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label",( vdetalle.length > 30 )? vdetalle.slice(0,30)+ '....':vdetalle);

    xSerie = document.createElement("listcell");
    xSerie.setAttribute("value",serie);
    xSerie.setAttribute("collapsed","true");
    xSerie.setAttribute("id","xdetalleventa_serie_"+IdComprobanteVentaDet);

    xLote = document.createElement("listcell");
    xLote.setAttribute("value",lote);
    xLote.setAttribute("collapsed","true");
    xLote.setAttribute("id","xdetalleventa_lote_"+IdComprobanteVentaDet);

    xVencimiento = document.createElement("listcell");
    xVencimiento.setAttribute("value",vence);
    xVencimiento.setAttribute("collapsed","true");
    xVencimiento.setAttribute("id","xdetalleventa_vencimiento_"+IdComprobanteVentaDet);

    xIdPedidoDet = document.createElement("listcell");
    xIdPedidoDet.setAttribute("value",IdPedidoDet);
    xIdPedidoDet.setAttribute("collapsed","true");
    xIdPedidoDet.setAttribute("id","xdetalleventa_idpedidodet_"+IdComprobanteVentaDet);

    xIdProducto = document.createElement("listcell");
    xIdProducto.setAttribute("value",Codigo);
    xIdProducto.setAttribute("collapsed","true");
    xIdProducto.setAttribute("id","xdetalleventa_idproducto_"+IdComprobanteVentaDet);
    

    xitem.appendChild( xnumitem );
    xitem.appendChild( xReferencia );
    xitem.appendChild( xCodBar );
    // xitem.appendChild( xNombre );
    xitem.appendChild( xTalla );
    xitem.appendChild( xDetalle );
    // xitem.appendChild( xColor );
    xitem.appendChild( xUnidades );
    xitem.appendChild( xPrecio );
    xitem.appendChild( xDescuento );	
    xitem.appendChild( xPV );
    xitem.appendChild( xSerie );
    xitem.appendChild( xLote );
    xitem.appendChild( xVencimiento );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xIdPedidoDet );
    xitem.appendChild( xCosto );
    lista.appendChild( xitem );
}


function buscarPorCodSerie(elemento){
    //var elemento = id("busquedaCodigoSerie");
    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaVentas");
    n = lista.itemCount;
    if(n==0) return; 
    busca = busca.toUpperCase();
    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[1].getAttribute('label');
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(busca) != -1){
	    lista.selectItem(texto2);
	    RevisarVentaSeleccionada();
	    return;
        }
    }

    alert('gPOS:\n          El código " '+elemento+' " no está en la lista.');
}

var ilineabuscaventas = 0;

function AddLineaVentas(item,vendedor,serie,num,fecha,total,pendiente,estado,
			IdComprobanteVenta,nombreCliente,NumeroDocumento,TipoDocumento,
			IdCliente,Local,IdLocal,MotivoAlba,IdSuscripcion,FechaEmision,
			PlazoPago,Cobranza,Observaciones,Reservado,FechaEntregaReserva){
    var lista = id("busquedaVentas");
    var xitem, xnumitem, xvendedor,xserie,xnum,xfecha,xtotal,xpendiente,xestado,xtipodoc,xop,xlocal,xsuscripcion,xemision,xplazopago,xcobranza,xobservaciones,xreserva,xfechareserva;
    
    var vfecha = "0000-00-00 00:00:00";
    var lfecha = "";
    if(FechaEntregaReserva != ' '){
	var afecha = FechaEntregaReserva.split("~");
	vfecha = afecha[1];
	lfecha = afecha[0];
    } 

    var aPlazo = PlazoPago.split("-");
    FechaPlazo = aPlazo[2]+"/"+aPlazo[1]+"/"+aPlazo[0];
    FechaPlazo = (PlazoPago == '0000-00-00')? "":FechaPlazo;
    
    xitem = document.createElement("listitem");
    xitem.value = IdComprobanteVenta;
    xitem.setAttribute("id","lineabuscaventa_"+ilineabuscaventas);
    ilineabuscaventas++;
    
    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xop = document.createElement("listcell");
    xop.setAttribute("label",IdComprobanteVenta);
    xop.setAttribute("collapsed",vOP);
    xop.setAttribute("style","text-align:center");
    
    xtipodoc = document.createElement("listcell");
    xtipodoc.setAttribute("label",TipoDocumento+' '+MotivoAlba);
    xtipodoc.setAttribute("style","text-align:left");
    xtipodoc.setAttribute("id","venta_tipodoc_"+IdComprobanteVenta);
    
    xvendedor = document.createElement("listcell");
    xvendedor.setAttribute("label",vendedor);
    xvendedor.setAttribute("collapsed",vUsuario);
    xvendedor.setAttribute("style","text-align:left");
    xvendedor.setAttribute("crop", "end");	
    
    xserie = document.createElement("listcell");
    xserie.setAttribute("label", serie + "-"+num);
    xserie.setAttribute("collapsed",vCodigo);
    xserie.setAttribute("style","text-align:left");
    xserie.setAttribute("id","venta_serie_"+IdComprobanteVenta);
    
    xnum = document.createElement("listcell");
    xnum.setAttribute("label", num);
    xnum.setAttribute("collapsed", "true");
    xnum.setAttribute("id","venta_num_"+IdComprobanteVenta);
    
    xfecha = document.createElement("listcell");
    xfecha.setAttribute("style","text-align:right");
    xfecha.setAttribute("label", fecha);	
    xfecha.setAttribute("collapsed", vFechaRegistro);	

    xemision = document.createElement("listcell");
    xemision.setAttribute("style","text-align:right");
    xemision.setAttribute("label", FechaEmision);

    xtotal = document.createElement("listcell");
    xtotal.setAttribute("label", parseFloat(total).toFixed(2));
    xtotal.setAttribute("style","text-align:right");
    xtotal.setAttribute("id","venta_importe_"+IdComprobanteVenta);

    xpendiente = document.createElement("listcell");
    xpendiente.setAttribute("label", parseFloat(pendiente).toFixed(2));
    xpendiente.setAttribute("style","text-align:right");
    xpendiente.setAttribute("id","venta_pendiente_"+IdComprobanteVenta);

    xestado = document.createElement("listcell");
    xestado.setAttribute("label", estado);
    xestado.setAttribute("style","text-align:center","width: 8em");
    xestado.setAttribute("crop", "end");
    xestado.setAttribute("id","venta_status_"+IdComprobanteVenta);
    
    
    xnombre = document.createElement("listcell");
    xnombre.setAttribute("label", nombreCliente);
    xnombre.setAttribute("value", IdCliente);
    xnombre.setAttribute("crop", "end");
    xnombre.setAttribute("id","venta_cliente_"+IdComprobanteVenta);
    
    if(NumeroDocumento=='0')
	NumeroDocumento = num;
    
    xnumdoc = document.createElement("listcell");
    xnumdoc.setAttribute("label", NumeroDocumento+'  ');
    xnumdoc.setAttribute("style","text-align:left");
    xnumdoc.setAttribute("id","venta_num_bol_"+IdComprobanteVenta);

    xlocal = document.createElement("listcell");
    xlocal.setAttribute("label", Local);
    xlocal.setAttribute("value", IdLocal);
    xlocal.setAttribute("collapsed", "true");
    xlocal.setAttribute("id","venta_local_"+IdComprobanteVenta);

    xsuscripcion = document.createElement("listcell");
    xsuscripcion.setAttribute("value", IdSuscripcion);
    xsuscripcion.setAttribute("collapsed", "true");
    xsuscripcion.setAttribute("id","venta_sucripcion_"+IdComprobanteVenta);

    xplazopago = document.createElement("listcell");
    xplazopago.setAttribute("value", PlazoPago);
    xplazopago.setAttribute("label", FechaPlazo);
    //xplazopago.setAttribute("collapsed", "true");
    xplazopago.setAttribute("style","text-align:right");
    xplazopago.setAttribute("id","venta_plazopago_"+IdComprobanteVenta);

    xcobranza = document.createElement("listcell");
    xcobranza.setAttribute("value", Cobranza);
    xcobranza.setAttribute("collapsed", "true");
    xcobranza.setAttribute("id","venta_cobranza_"+IdComprobanteVenta);

    xobservaciones = document.createElement("listcell");
    xobservaciones.setAttribute("value", Observaciones);
    xobservaciones.setAttribute("collapsed", "true");
    xobservaciones.setAttribute("id","venta_observaciones_"+IdComprobanteVenta);

    xreservado = document.createElement("listcell");
    xreservado.setAttribute("value", Reservado);
    xreservado.setAttribute("collapsed", "true");
    xreservado.setAttribute("id","venta_reservado_"+IdComprobanteVenta);

    xfechareserva = document.createElement("listcell");
    xfechareserva.setAttribute("value", vfecha);
    xfechareserva.setAttribute("label", lfecha);
    xfechareserva.setAttribute("id","venta_fechareserva_"+IdComprobanteVenta);

    
    xitem.appendChild( xnumitem );
    xitem.appendChild( xserie );
    xitem.appendChild( xop );
    xitem.appendChild( xtipodoc );
    xitem.appendChild( xnum );
    xitem.appendChild( xnumdoc );
    xitem.appendChild( xnombre );	
    xitem.appendChild( xfecha );
    xitem.appendChild( xemision );
    xitem.appendChild( xplazopago );
    xitem.appendChild( xtotal );
    xitem.appendChild( xpendiente );	
    xitem.appendChild( xestado );
    xitem.appendChild( xfechareserva );
    xitem.appendChild( xvendedor );
    xitem.appendChild( xlocal );
    xitem.appendChild( xsuscripcion );
    xitem.appendChild( xcobranza );
    xitem.appendChild( xobservaciones );
    xitem.appendChild( xreservado );

    lista.appendChild( xitem );		
}


function BuscarVentas(){

    VaciarBusquedaVentas();
    (esFinanzas)? VaciarDetallesCobros() : VaciarDetallesVentas();

    var desde = id("FechaBuscaVentas").value;
    var hasta = id("FechaBuscaVentasHasta").value;
    var nombre = id("NombreClienteBusqueda").value;

    var modoend         = (id("modoConsultaVentasFin").checked);
    var modopen         = (id("modoConsultaVentasPen").checked);
    var modotpventa     = id("modoConsultaTipoVenta").value;
    var modoserie       = "todos";
    var modocontado     = "todos";
    var modosuscripcion = "todos";
    var modoreserva     = "todos";

    var modo            = "todos";    
    //modo                = ( modoend && modopen )? "endypen":"todos";
    modo                = ( modoend && !modopen )? "end":modo;
    modo                = ( !modoend && modopen )? "pen":modo;

    //alert(modotpventa);
    switch( modotpventa ){
    case "contado":     modoserie = "contado"; break;
    case "credito":     modoserie = "cedidos"; break;
    case "suscripcion": modosuscripcion = "suscripcion"; break;
    case "reservas":    modoreserva = "reservados"; break;
    }

    //habilita tree
    BuscarReservados();
    BuscarPlazo();
    
    var filtrocodigo   = id("busquedaCodigoSerie").value;
    var filtroventa    = id("FiltroVenta").value;
    var filtrolocal    = id("FiltroVentaLocal").value;
    var modofactura    = (filtroventa == "factura")?"factura":"todos";
    var modoboleta     = (filtroventa == "boleta")?"boleta":"todos";
    var modoticket     = (filtroventa == "ticket" )?"ticket":"todos";
    var modoalbaran    = (filtroventa == "albaran")?"albaran":"todos";
    var modoalbaranint = (filtroventa == "albaranint")?"albaranint":"todos";
    var forzarid       = (filtrocodigo != '' )? filtrocodigo:false;
    var usuario        = id("IdUsuario").getAttribute("value");
    var tipoproducto   = id("TipoProducto").value;

    RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,modoboleta,
		    modoalbaran,modoalbaranint,modoticket,false,false,filtrolocal,
		    forzarid,usuario,modoreserva,tipoproducto,AddLineaVentas);

    var elemento = id("busquedaCodigoSerie").value;

    //if( elemento != '' ) //buscarPorCodSerie(elemento);
    
}


function RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,
			 modoboleta, modoalbaran,modoalbaranint,modoticket,
			 IdComprobanteVenta,reimprimir,filtrolocal,forzarid,usuario,
			 modoreserva,tipoproducto,FuncionProcesaLinea){

    var url = "../../services.php?modo=mostrarVentas&desde=" + escape(desde) 
        + "&modoconsulta=" + escape(modo) 
        + "&hasta=" + escape(hasta) 
        + "&nombre=" + trim(nombre)
        + "&modoserie=" + escape(modoserie)
        + "&modosuscripcion=" + escape(modosuscripcion)
        + "&modoboleta=" + escape(modoboleta)
        + "&modoticket=" + escape(modoticket)
        + "&modoalbaran=" + escape(modoalbaran)
        + "&modoalbaranint=" + escape(modoalbaranint)
        + "&modofactura=" + escape(modofactura)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&esventas=on"
        + "&modoventa=notpv" 
        + "&forzarfactura=" + IdComprobanteVenta
        + "&usuario=" + usuario
        + "&modoreserva=" + modoreserva
        + "&tipoprod=" + tipoproducto
        + "&forzarid=" + forzarid;


    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    
    var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobanteVenta,NumeroDocumento,TipoDocumento,IdCliente,Local,IdLocal,FechaEmision,PlazoPago,Cobranza,Observaciones,Reservado,FechaEntregaReserva;
    var node,t,i,codventa; 
    var totalVenta = 0;
    var totalVentaPendiente = 0;
    var ImporteTotalVentas = 0;
    var nroboletas = 0;
    var nrofacturas = 0;
    var nrotickets = 0;
    var a_cv       = new Array();
    var a_cvdev    = new Array();
    var nrototalventas = 0;
    if (!obj.responseXML)
        return alert('gPOS: '+po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    t = 0;
	    vendedor 	    = node.childNodes[t++].firstChild.nodeValue;
	    serie 	    = node.childNodes[t++].firstChild.nodeValue;
	    num 	    = node.childNodes[t++].firstChild.nodeValue;
	    fecha 	    = node.childNodes[t++].firstChild.nodeValue;
	    total 	    = node.childNodes[t++].firstChild.nodeValue;
	    totalVenta      = parseFloat(totalVenta) + parseFloat(total);
	    pendiente 	    = node.childNodes[t++].firstChild.nodeValue;
	    totalVentaPendiente = parseFloat(totalVentaPendiente) + parseFloat(pendiente);
	    estado 	    = node.childNodes[t++].firstChild.nodeValue;
	    IdComprobanteVenta   = node.childNodes[t++].firstChild.nodeValue;
	    NumeroDocumento = node.childNodes[t++].firstChild.nodeValue;
	    TipoDocumento   = node.childNodes[t++].firstChild.nodeValue;
	    codventa        = serie+'-'+num;	    
	    if (TipoDocumento == 'Ticket') nrotickets++; 
	    if (TipoDocumento == 'Boleta') nroboletas++; 
	    if (TipoDocumento == 'Factura') nrofacturas++; 
	    if (a_cvdev[a_cvdev.indexOf(codventa)]==codventa)
		a_cv.push(codventa+':'+total);   
	    
	    if (node.childNodes[t].firstChild)
                nombreCliente = node.childNodes[t++].firstChild.nodeValue;
	    else 
                nombreCliente = "";

	    IdCliente     = node.childNodes[t++].firstChild.nodeValue;
	    Local         = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal       = node.childNodes[t++].firstChild.nodeValue;
	    MotivoAlba    = node.childNodes[t++].firstChild.nodeValue;
	    IdSuscripcion = node.childNodes[t++].firstChild.nodeValue;
	    FechaEmision  = node.childNodes[t++].firstChild.nodeValue;
	    PlazoPago     = node.childNodes[t++].firstChild.nodeValue;
	    Cobranza      = node.childNodes[t++].firstChild.nodeValue;
	    Observaciones = node.childNodes[t++].firstChild.nodeValue;
	    Reservado     = node.childNodes[t++].firstChild.nodeValue;
	    FechaEntregaReserva = node.childNodes[t++].firstChild.nodeValue;

	    totalVenta    = (TipoDocumento == 'AlbaranInt' && MotivoAlba == 'Devolución')? parseFloat(totalVenta)-parseFloat(total):totalVenta;
	    totalVentaPendiente    = (TipoDocumento == 'AlbaranInt' && MotivoAlba == 'Devolución')? parseFloat(totalVentaPendiente)-parseFloat(pendiente):totalVentaPendiente;

	    FuncionProcesaLinea(item,vendedor,serie,num,fecha,total,pendiente,estado,
				IdComprobanteVenta,nombreCliente,NumeroDocumento,
				TipoDocumento,IdCliente,Local,IdLocal,MotivoAlba,
				IdSuscripcion,FechaEmision,PlazoPago,Cobranza,Observaciones,
				Reservado,FechaEntregaReserva);
	    
	    item--;
        }
    }
    
    //Sin Resumen...
    if( reimprimir ) return; 

    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    var c_cvdev = String(unique(a_cvdev));
    var c_cv     = a_cv.toString();
    var a_cv     = c_cv.split(",");
    var a_cvdev  = c_cvdev.split(",");
    var a_cvres  = new Array();
    for (i=0; i<a_cvdev.length; i++) {
	var cvi = 0;
	for (j=0; j<a_cv.length; j++) {
	    var d_cv = a_cv[j].split(":");
	    if(a_cvdev[i]==d_cv[0])
		cvi = parseFloat(cvi) + parseFloat(d_cv[1]);
	}
	if(cvi>0)
	    a_cvres.push(a_cvdev[i]);
	
    }
    nrototalventas = parseFloat(nrofacturas+nroboletas+nrotickets) - parseFloat( a_cvres.length);

    ImporteTotalVentas = parseFloat(totalVenta) - parseFloat(totalVentaPendiente);
    id("TotalImporteVentas").value = cMoneda[1]['S']+" "+formatDinero(totalVenta.toFixed(2));
    id("TotalImporteVentasPendiente").value = cMoneda[1]['S']+" "+formatDinero(totalVentaPendiente.toFixed(2));
    id("ImporteTotalVentas").value    = cMoneda[1]['S']+" "+formatDinero(ImporteTotalVentas);
    id("TotalVentasRealizadas").value = nrototalventas;
    id("TotalNroFacturas").value      = nrofacturas;
    id("TotalNroBoletas").value       = nroboletas;
    id("TotalNroTicket").value        = nrotickets;
    a_cvres  = new Array();
    a_cv     = new Array();
    a_cvdev  = new Array();
}

function mostrarBusquedaAvanzadaVenta(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Forma_Venta":
	vFormaVenta    = xchecked;
	break;
    case "Usuario":
	vUsuario       = xchecked;
	if(xchecked) id("IdUsuario").value = 'todos';
	break;
    case "Moneda" : 
	vMoneda        = xchecked;
	break;
    case "Vendedor":
	vUsuario       = xchecked;
	break;
    case "OP" :
	vOP            = xchecked;
	break;
    case "Codigo":
	vCodigo        = xchecked;
	break;
    case "Fecha_Registro":
	vFechaRegistro = xchecked;
	break;
    }

    if(id("vboxv"+xlabel)) id("vboxv"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistv"+xlabel)) id("vlistv"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcolv"+xlabel)) id("vlistcolv"+xlabel).setAttribute("collapsed",xchecked);
    BuscarVentas();
}

function menuContextualVentasRealizadas(xval){
    var aAlbaInt  = id("venta_tipodoc_"+xval).getAttribute("label").split(" ");
    var AlbaInt   = trim(aAlbaInt[0]);
    var esAlbaInt = (AlbaInt == 'AlbaranInt')? true:false;

    if(esFinanzas){
	id("VentaRealizadaAbonar").setAttribute("disabled",true);
	var esAbonar =  ( id("venta_pendiente_"+xval).getAttribute("label") > 0 )? true:false
	//if ( esAbonar ) id("VentaRealizadaAbonar").removeAttribute("disabled");
	if(esAbonar && !esAlbaInt) id("VentaRealizadaAbonar").removeAttribute("disabled");
    }
    
    id("mheadImprimir").setAttribute('collapsed',esAlbaInt);
    if(!esFinanzas)
	id("mheadImprimirInt").setAttribute('collapsed',!esAlbaInt);

    var esSuscrip = (cIdSuscripcionVenta == 0)? true:false;
    var esImprimir = (id("venta_tipodoc_"+xval).getAttribute("label") != 'Ticket')?true:false
    if(esImprimir) id("mheadImprimir").removeAttribute('disabled');
    id("mheadImprimirSuscripcion").setAttribute('collapsed',esSuscrip);
}


/* ============= Operaciones con ventas ==================== */


function mostrarFormCobroComprobante(){
    id("hboxDetallesCobro").setAttribute('collapsed',true)
    id("busquedaDetallesCobro").setAttribute('collapsed',true);
    id("formAbonarComprobantesVentas").setAttribute('collapsed',false);
}

function VolverCobros(){
    id("vboxListaVentas").setAttribute('collapsed',false);
    id("hboxDetallesCobro").setAttribute('collapsed',false);
    id("busquedaDetallesCobro").setAttribute('collapsed',false);
    id("formAbonarComprobantesVentas").setAttribute('collapsed',true);
    id("boxResumenComprobanteVenta").setAttribute("collapsed",false);
}

function ActualizaPeticion(){

}


//Abonar

function CleanMoney(cadena) {
    return parseMoney(new String(cadena) );
}

function parseMoney (cadena) {
    //var cadoriginal = cadena;
    if (!cadena) {
	cadena = new String( cadena );
	if( !cadena.replace ){
            return 0.0;		 	
	}
    }
    
    cadena = parseFloat( cadena );	
    
    if (isNaN( cadena ))
	return 0.0;
    
    return cadena;
}

function obtenerTipoComprobanteVenta(num){

    var	url =
	"../../services.php?"
	+"modo=ObtenerTipoComprobante"
	+"&idex="+num
	+"&idlocal="+cIdLocalVenta
	+"&esVenta=on";

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var resultado = xrequest.responseText;

    var res = xrequest.responseText;
    return res.split('-');
    
}

function  ReimprimirVentaSeleccionada(xval){
    //VaciarDetallesVentas();
    var idex = id("busquedaVentas").selectedItem;

    if(!idex) return;

    var num  = idex.value;
    var res  = obtenerTipoComprobanteVenta(num);

    if (res[0]=='Ticket')
        var comprobante = 1;
    else
        var comprobante = 0;

    res[0] = (xval == 2)? 'Albaran':res[0];
    t_RecuperaTicket(num,res[0]);
}

function t_RecuperaTicket(IdComprobante,TipoVenta){

    switch( TipoVenta )
    {
    case 'Factura':
    case 'Boleta':
    case 'Albaran':
    case 'AlbaranInt':

	//obtenemos datos
	var url =
	    "../../services.php?modo=obtenerDatosComprobanteVenta"+"&"+
	    "IdComprobante="+IdComprobante+"&"+
            "IdLocal="+cIdLocalVenta+"&"+
            "esVenta=on"+"&"+
	    "tipoComprobante="+TipoVenta;

	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);

	var dtComprobante = xrequest.responseText.split("~");
	var importe       = dtComprobante[0];
	var codcliente    = dtComprobante[1];
	var idcomprobante = dtComprobante[2];
	var nroDocumento  = dtComprobante[3];
	var nroSerie      = dtComprobante[4];
	var importeletras = convertirNumLetras(importe,1);
	importeletras     = importeletras.toUpperCase();
	var nombreUsuario = '';

	//imprime pdf
	var url=
	    "../fpdf/imprimir_"+TipoVenta+"_tpv.php?"+
	    "nro"+TipoVenta+"="+nroDocumento+"&"+
	    "totaletras="+importeletras+"&"+
	    "codcliente="+codcliente+"&"+
	    "nroSerie="+nroSerie+"&"+
	    "nombreusuario="+nombreUsuario+"&"+
	    "idlocal="+cIdLocalVenta+"&"+
	    "idcomprobante="+IdComprobante;

	location.href=url;
	break;

    default:
	return false;
    }

}

function ImprimirSuscripcionSeleccionada(){
    var idex          = id("busquedaVentas").selectedItem;
    var IdComprobante = idex.value;
    var res           = obtenerTipoComprobanteVenta(IdComprobante);
    var TipoVenta     = res[0];

    //obtenemos datos
    var url =
	"../../services.php?modo=obtenerDatosComprobanteVenta"+"&"+
	"IdComprobante="+IdComprobante+"&"+
	"esVenta=off"+"&"+
	"tipoComprobante="+TipoVenta;
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var dtComprobante = xrequest.responseText.split("~");
    var importe       = dtComprobante[0];
    var codcliente    = dtComprobante[1];
    var idcomprobante = dtComprobante[2];
    var nroDocumento  = dtComprobante[3];
    var nroSerie      = dtComprobante[4];
    var importeletras = convertirNumLetras(importe,1);
    importeletras     = importeletras.toUpperCase();
    var nombreUsuario = '';
    
	//imprime pdf
    var url=
	"../fpdf/imprimir_suscripcion_tpv.php?"+
	"nro="+nroDocumento+"&"+
	"totaletras="+importeletras+"&"+
	"codcliente="+codcliente+"&"+
	"nroSerie="+nroSerie+"&"+
	"nombreusuario="+nombreUsuario+"&"+
	"idcomprobante="+IdComprobante;
    
    location.href=url;
}


var Abonar = new Object();

function VentanaAbonos(){
    
    //Valida Alabaran
    var idex = id("busquedaVentas").selectedItem;
    if(!idex)	return;//no se selecciono nada

    var num  = idex.value;
    var res  = obtenerTipoComprobanteVenta(num);

    if (res[0]=='Albaran'){
	alert("gPOS:\n "
	      +" - El ComprobanteVenta "+res[0]+" esta reservado."
	      +" \n  - Facture este comprobante para poder - Abonar - ")
	     return;
    }
    
    LimpiarFormaAbonos();
    mostrarFormCobroComprobante();

    var IdComprobanteVenta = num;

    var serie           = id("venta_serie_" + IdComprobanteVenta).getAttribute("label");
    var num             = id("venta_num_" + IdComprobanteVenta).getAttribute("label");
    var serienumfactura = serie;
    var plazopago       = id("venta_plazopago_" + IdComprobanteVenta).getAttribute("value");
    var cobranza        = id("venta_cobranza_" + IdComprobanteVenta).getAttribute("value");
    var observaciones   = trim(id("venta_observaciones_" + IdComprobanteVenta).getAttribute("value"));
    var ventapendiente  = id("venta_pendiente_" + IdComprobanteVenta).getAttribute("label");

    var f = new Date();
    var fechahoy  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();

    plazopago = (plazopago == '0000-00-00')? fechahoy:plazopago;
    //resetea nuevo abono
    Abonar = new Object();	
    
    //fijamos la id actual
    Abonar.IdComprobanteVenta = IdComprobanteVenta;
    Abonar.Maximo             = parseFloat(ventapendiente).toFixed(2);

    id("abono_Debe").setAttribute("value",formatDinero(Abonar.Maximo));
    id("abono_Efectivo").setAttribute("value",formatDinero(Abonar.Maximo));
    id("abono_numTicket").setAttribute("label",res[0]+' '+serienumfactura);
    id("plazo_pago").setAttribute("value",plazopago);
    id("plazo_pago").value = plazopago;
    id("estado_cobranza").setAttribute("value",cobranza);
    id("estado_cobranza").value = cobranza;
    id("observaciones").setAttribute("value",observaciones);

    var xpte = (ventapendiente > 0)? false:true;
    
    id("rowPlazoPago").setAttribute("collapsed",xpte);
    id("rowEstadoCobranza").setAttribute("collapsed",xpte);
    id("boxResumenComprobanteVenta").setAttribute("collapsed",true);
}

function ActualizaPeticionAbono() {
    var cr = "\n";
    var color ="black";

    var entrega = 0;
    entrega += parseFloat(CleanMoney(document.getElementById("abono_Efectivo").value));	
    var pendiente = Abonar.Maximo - entrega;
    id("abono_Pendiente").setAttribute("value", formatDinero(pendiente));
    id("abono_nuevo").setAttribute("value", formatDinero(entrega));	
}

function ModificarEstadoPago(xvalue){
    var xdato = "";
    var idex  = id("busquedaVentas").selectedItem;
    var IdComprobanteVenta  = idex.value;

    switch(xvalue){
    case '1':
	var xplazo = id("plazo_pago").value;
	xdato = xplazo;
	
	break;
    case '2':
	var xcobranza = id("estado_cobranza").value;
	xdato = xcobranza;
	
	break;
    case '3':
	var xobs = id("observaciones").value;
	xdato = trim(xobs);
    
	break;
    }
    
    var obj = new XMLHttpRequest();
    var url = "modpagoscobros.php?modo=actualizaEstadoPago"
	+ "&xid=" + IdComprobanteVenta
        + "&xdato=" + xdato
        + "&xop=" + xvalue;

    obj.open("GET",url,false);
    obj.send(null);
    if (obj.responseText != 1)
	return alert("gPOS:\n\n "+obj.responseText);

    if(xvalue == 1) 
	id("venta_plazopago_" + IdComprobanteVenta).setAttribute("value",xplazo);
    if(xvalue == 2)
	id("venta_cobranza_" + IdComprobanteVenta).setAttribute("value",xcobranza);
    if(xvalue == 3)
	id("venta_observaciones_" + IdComprobanteVenta).setAttribute("value",xobs);
}

function LimpiarFormaAbonos(){
    id("abono_Efectivo").value = "0";
    Abonar.Maximo = 0;
    ActualizaPeticionAbono();	
}


function RealizarAbono(){
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;
    var idx  = idex.value;

    var IdComprobanteVenta = Abonar.IdComprobanteVenta;
    var abono_efectivo     = CleanMoney(id("abono_Efectivo").value);
    var comprobante        = id("venta_tipodoc_"+idx).getAttribute('label');
    var numcomprobante     = id("venta_num_bol_"+idx).getAttribute('label');
    var idcliente          = id("venta_cliente_"+idx).getAttribute("value");

    if(comprobante == 'AlbaranInt Devolución')
	return;

    var totalpendiente = id("abono_Debe").getAttribute("value");

    if(totalpendiente < abono_efectivo)
	abono_efectivo = totalpendiente;

    if(abono_efectivo < 0.01)
	return alert('gPOS:  Abono Cliente \n\n  - Ingrese monto mayor a 0.00');

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Eliminar Abono' ) ) return;

    var obj = new XMLHttpRequest();
    var url = "../../services.php?modo=realizarAbonoComprobanteCliente"
	+ "&IdComprobanteVenta=" + escape(IdComprobanteVenta)
        + "&pago_efectivo=" + parseFloat(abono_efectivo)
        + "&comprobante=" + trim(comprobante)
        + "&numcomprobante=" + escape(numcomprobante)
        + "&idc=" + escape(idcliente);

    obj.open("POST",url,false);
    obj.send("");	
    
    var text = obj.responseText;
    var ares = text.split("~");

    if (ares[0] != "") return alert('gPOS: '+po_servidorocupado+'\n'+ares[0]);

    if(ares[1] == 'cjacda')
	return alert('gPOS:  Abono Cliente \n\n  - La caja general está cerrada, abra para continuar');

    var xpen = id("venta_pendiente_"+IdComprobanteVenta);
    var xstatus = id("venta_status_"+IdComprobanteVenta);

    text = parseFloat(ares[2]);

    xpen.setAttribute("label",parseFloat(text).toFixed(2));//Nuevo valor pendiente
    
    if (text<0.01){
        if (xstatus)
	    xstatus.setAttribute("label","PAGADO");//Correspondiente nuevo estado	
    }
    
    LimpiarFormaAbonos();
    BuscarDetallesCobro(IdComprobanteVenta);
    VolverCobros();
}

function BuscarDetallesCobro(IdComprobante){
    VaciarDetallesCobros();
    RawBuscarDetallesCobro(IdComprobante, AddLineaDetallesCobro);
}

function RawBuscarDetallesCobro(IdComprobante,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var z   = null;
    var url = "../../services.php?modo=mostrarDetallesCobro&IdComprobante="+IdComprobante;
    obj.open("GET",url,false);

    try {
	obj.send(null);
    } catch(z){
	return;
    }

    var tex = "";
    var cr = "\n";
    var item,ModoPago,FechaPago,ImportePago,IdComprobante,Usuario,Simbolo,Caja,Local,LocalPago,IdModalidad,TipoVenta;
    var node,t,i;
    var numitem = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC   = item;
    var numitem = 0;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
	    numitem++;
	    if(node.childNodes[t].firstChild){
		ModoPago     = node.childNodes[t++].firstChild.nodeValue;
		FechaPago    = node.childNodes[t++].firstChild.nodeValue;
		ImportePago  = node.childNodes[t++].firstChild.nodeValue;
		Usuario      = node.childNodes[t++].firstChild.nodeValue;
		IdOperacion  = node.childNodes[t++].firstChild.nodeValue;
		Local        = node.childNodes[t++].firstChild.nodeValue;
		IdModalidad  = node.childNodes[t++].firstChild.nodeValue;
		TipoVenta    = node.childNodes[t++].firstChild.nodeValue;
		LocalPago    = node.childNodes[t++].firstChild.nodeValue;
		
		FuncionRecogerDetalles(numitem,FechaPago,ImportePago,Usuario,ModoPago,
				       IdOperacion,Local,LocalPago,IdModalidad,TipoVenta);
            //item--;
	    }
        }
    }
}

function AddLineaDetallesCobro(numitem,FechaPago,ImportePago,Usuario,ModoPago,
			       IdOperacion,Local,LocalPago,IdModalidad,TipoVenta){

    var lista = id("busquedaDetallesCobro");
    var xitem,xnumitem,xFechaPago,xModoPago,xUsuario,xIMportePago,xLocalPago,xTipoVenta;

    xitem = document.createElement("listitem");
    xitem.value = IdOperacion;
    xitem.setAttribute("id","detallecobro_" + idetallescobro);
    idetallescobro++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("value", IdModalidad);
    xModoPago.setAttribute("id","c_modopago_"+IdOperacion);

    xFechaPago = document.createElement("listcell");
    xFechaPago.setAttribute("label", FechaPago);
    xFechaPago.setAttribute("id","c_fechapago_"+IdOperacion);

    xImportePago = document.createElement("listcell");
    xImportePago.setAttribute("label", formatDinero(ImportePago));
    xImportePago.setAttribute("value",ImportePago);
    xImportePago.setAttribute("style","font-weight:bold;text-align:right");
    xImportePago.setAttribute("id","c_importe_"+IdOperacion);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("style","text-align:center");
    xUsuario.setAttribute("id","c_usuario_"+IdOperacion);

    xLocalPago = document.createElement("listcell");
    xLocalPago.setAttribute("label", LocalPago+' - '+Local);
    xLocalPago.setAttribute("style","text-align:center");
    xLocalPago.setAttribute("id","c_localpago_"+IdOperacion);

    xTipoVenta = document.createElement("listcell");
    xTipoVenta.setAttribute("value", TipoVenta);
    xTipoVenta.setAttribute("collapsed","true");
    xTipoVenta.setAttribute("id","c_tipoventa_"+IdOperacion);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xFechaPago );
    xitem.appendChild( xModoPago );
    xitem.appendChild( xImportePago );
    xitem.appendChild( xLocalPago );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xTipoVenta );
    lista.appendChild( xitem );
}


function ImprimirCobroSeleccionada(){

    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    var idoc          = idex.value;
    var importe       = id("venta_importe_"+idoc).getAttribute("label");
    var moneda        = 1;
    var importeletras = convertirNumLetras(importe,moneda);
    importeletras     = importeletras.toUpperCase();
    var url           = "../fpdf/imprimir_cobros.php?idoc="+idoc+
                        "&totaletras="+importeletras;
    location.href=url;
}

function BuscarReservados(){
    var xval = (id("modoConsultaTipoVenta").value == "reservas")? false:true;
    id("vlistcolFechaEntrega").setAttribute("collapsed",xval);
    id("vlistFechaEntrega").setAttribute("collapsed",xval);
}

function BuscarPlazo(){
    var xval = (id("modoConsultaVentasPen").checked)? false:true;
    var yval = ( id("modoConsultaTipoVenta").value == "credito" )? false:true;
    xval     = (!xval || !yval)? false:true;

    id("vlistcolvPlazoPago").setAttribute("collapsed",xval);
    id("vlistvPlazoPago").setAttribute("collapsed",xval);
}

function RevisarCobroSeleccionada(){
    var idex = id("busquedaDetallesCobro").selectedItem;
    if(!idex) return;
    var idpago = idex.value;

    cIdOperacionCaja = idex.value;
    cIdModalidadPago = id("c_modopago_"+idex.value).getAttribute("value");
    cTipoVenta       = id("c_tipoventa_"+idex.value).getAttribute("value");
    cImporteCobro    = id("c_importe_"+idex.value).getAttribute("value");
}

function ModificarCobros(xval){
    var idex = id("busquedaDetallesCobro").selectedItem;
    if(!idex) return;

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Eliminar Abono' ) ) return;

    var msj = "- Cliente: "+cClienteComprobanteVenta+"\n- Monto : "+cImporteCobro;
    if(!confirm('gPOS:  Eliminar Abonos Cliente \n\n'+msj+',\n'+'Va eliminar el abono.  ¿desea continuar?'))
	return;

    var obj = new XMLHttpRequest();
    var url = "modpagoscobros.php?modo=ModificarCobros"
	+ "&idopcja=" + escape(cIdOperacionCaja)
        + "&idcbte=" + escape(cIdComprobanteVenta)
        + "&idmod=" + escape(cIdModalidadPago)
        + "&idc=" + escape(cIdClienteComprobanteVenta)
        + "&idl=" + escape(cIdLocalVenta)
        + "&tv=" + escape(cTipoVenta)
        + "&ximp=" + escape(cImporteCobro)
        + "&xop=" + escape(xval);

    obj.open("POST",url,false);
    obj.send("");	
    
    var text = obj.responseText;    
    var ares = text.split("~");
    
    if(ares[0] != '')
	return alert('gPOS:  Eliminar Abono Cliente \n\n- Error al Eliminar \n'+po_servidorocupado+'\n'+ares[0]);

    if(ares[1] == 'cjacda')
	return alert("gPOS:  Eliminar Abono Cliente \n\n- No se eliminó el abono, la caja está cerrada");
    if(ares[2]){
	alert("gPOS:  Eliminar Abono Cliente \n\n- Se Eliminó el abono");
	BuscarVentas();
    }
}

function ckCodigoAutorizacion(xaccion,xck){
    var idex = id("busquedaVentas").selectedItem;
    var xid  = idex.value;
    var url  = "../../services.php?modo=codigoAutorizacionTPV"+
	       "&xid="+xid+
	       "&xaccion="+xaccion;

    var xres = new XMLHttpRequest();

    xres.open("GET",url,false);
    try{
	xres.send(null);
    } catch(z){
	return;
    }

    var xarrcod  = xres.responseText.split("~");
    var xtrmsj   = ( !xarrcod[2] )? '\n\n      *** Nuevo Código Generado  ***':'';
 
    if(xarrcod[0] != 0 )
	return alert(po_servidorocupado);
    var xmsj  = "gPOS:   Código de Autorización de Operaciones: \n"+
		"\n  CLIENTE "+ cClienteComprobanteVenta +
		"\n  COMPROBANTE        : "+ cComprobanteVenta +" "+ cSerieNroComprobanteVenta +
	        "\n  CODIGO                   : ***"+xarrcod[1]+xtrmsj;

    //codigo nuevo
    if( xaccion == 'resetck' )
	return alert( xmsj+'\n' );

    //codigo guardado
    if( confirm( xmsj+"\n\n  desea generar un nuevo código de autorización?" ) )
	ckCodigoAutorizacion('resetck',false);
}

function validaCodigoAutorizacion(xid,xop){

    var xcod;
    
    if ( Local.CodigoAutorizacion[xid] )
        xcod = Local.CodigoAutorizacion[xid]; 
    else
	xcod = prompt( "gPOS:   Código de Autorización de Operaciones: \n"+
		       "\n  CLIENTE "+ cClienteComprobanteVenta +
		       "\n  COMPROBANTE       : "+ cComprobanteVenta +" "+ cSerieNroComprobanteVenta +
		       "\n  OPERACION             : "+ xop +
		       "\n\nIngrese el código de autorización de operaciones:\n\n", '');

    //cancelo?
    if( xcod == null)
	return false;

    //codigo vacio?
    if ( xcod == '') {
	alert( "gPOS:   Código de Autorización de Operaciones: \n"+
	       "\n     -   Ingrese correctamente el código de autorización  - ");
	return validaCodigoAutorizacion(xid,xop);
    }

    //servidor?
    var url,xres,xarrcod;

    url  = "../../services.php?modo=validaCodigoAutorizacionTPV"+
	"&xid="+xid+
	"&xcod="+xcod;
    xres = new XMLHttpRequest();
    xres.open("GET",url,false);
    try{
	xres.send(null);
    } catch(z){
	return;
    }
    
    xarrcod = xres.responseText.split("~");

    //respuesta del servidor
    if(xarrcod[0] != 0 ){ 
	alert(po_servidorocupado); return false; 
    }
    
    Local.CodigoAutorizacion[xid] = ( xarrcod[1] == 1 )? xcod: false;

    if( !Local.CodigoAutorizacion[xid] )
	alert( "gPOS:  Código de Autorización de Operaciones: "+
	       "\n\n    -   El código de autorización es incorrecto ó expiró  -\n");

    return ( xarrcod[1] == 1 );
}
