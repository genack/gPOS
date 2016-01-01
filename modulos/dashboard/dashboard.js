
var id          = function(name) { return document.getElementById(name); }
var AjaxDemon   = new XMLHttpRequest();
var DashBoard   = new Object();
var aDashBoard  = new Array();
var loadDemon   = false;


DashBoard.ComprobantesBorrador    = 0;
DashBoard.ComprobantesPendientes  = 0;
DashBoard.ComprobantesPendientes  = 0;
DashBoard.PedidosBorrador         = 0;
DashBoard.PedidosPendientes       = 0;
DashBoard.Productos               = 0;
DashBoard.Servicios               = 0;
DashBoard.PedidosPorRecibir       = 0;
DashBoard.ProductosStockMinimo    = 0;
DashBoard.ProntoVencimiento       = 0;
DashBoard.ProductosSinStock       = 0;
DashBoard.PendientesServicios     = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.ReservasEntregar        = 0;
DashBoard.PendientesReservas      = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.PendientesCreditos      = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.PendientesPreventas     = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.PendientesProforma      = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.Promociones             = 0;
DashBoard.PendientePorCobrar      = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.PendientePorPagar       = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.VencidoPorCobrar        = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.VencidoPorPagar         = '0 ( '+cMoneda[1]['S']+' '+formatDinero(0) +' )';
DashBoard.CostoTotal              = cMoneda[1]["S"]+' '+formatDinero(0);
DashBoard.PrecioTotal             = cMoneda[1]["S"]+' '+formatDinero(0);
DashBoard.ProductosTotal          = 0;
DashBoard.UtilidadTotal           = cMoneda[1]["S"]+' '+formatDinero(0);
DashBoard.ImpuestoTotal           = cMoneda[1]["S"]+' '+formatDinero(0);


function loadDatosDashBoard(){

    DashBoard.ComprobantesBorrador    = aDashBoard['ComprobantesBorrador']; 
    DashBoard.ComprobantesPendientes  = aDashBoard['ComprobantesPendientes'];
    DashBoard.ComprobantesPendientes  = aDashBoard['ComprobantesPendientes'];
    DashBoard.PedidosBorrador         = aDashBoard['PedidosBorrador'];
    DashBoard.PedidosPendientes       = aDashBoard['PedidosPendientes'];
    
    DashBoard.Productos               = aDashBoard['Productos'];
    DashBoard.Servicios               = aDashBoard['Servicios'];

    DashBoard.PedidosPorRecibir       = aDashBoard['PedidosPorRecibir'];
    DashBoard.ProductosStockMinimo    = aDashBoard['StockMinimo']; 
    DashBoard.ProntoVencimiento       = aDashBoard['ProntoVencimiento']; 
    DashBoard.ProductosSinStock       = aDashBoard['ProductosSinStock'];
    DashBoard.PendientesServicios     = aDashBoard['PendientesServicios']+ " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientesServiciosMonto']) +" )";    
    DashBoard.ReservasEntregar        = aDashBoard['ReservasEntregar'];
    DashBoard.PendientesReservas      = aDashBoard['PendientesReservas'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientesReservasMonto'] )+" )";
    DashBoard.PendientesCreditos      = aDashBoard['PendientesCreditos'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientesCreditosMonto'] )+" )";
    DashBoard.PendientesPreventas     = aDashBoard['PendientesPreventas']  + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientesPreventasMonto'] )+" )";
    DashBoard.PendientesProforma      = aDashBoard['PendientesProformas'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientesProformasMonto'] )+" )";
    DashBoard.Promociones                = aDashBoard['Promociones'];
    DashBoard.PendientePorCobrar      = aDashBoard['PendientePorCobrar'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientePorCobrarMonto'] )+" )";
    DashBoard.PendientePorPagar       = aDashBoard['PendientePorPagar'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['PendientePorPagarMonto'] )+" )";
    DashBoard.VencidoPorCobrar        = aDashBoard['VencidoPorCobrar'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['VencidoPorCobrarMonto'] )+" )";
    DashBoard.VencidoPorPagar         = aDashBoard['VencidoPorPagar'] + " ( "+cMoneda[1]["S"]+' '+formatDinero( aDashBoard['VencidoPorPagarMonto'] )+" )";

    DashBoard.CostoTotal              = cMoneda[1]["S"]+' '+formatDinero(aDashBoard['CostoTotal']);
    DashBoard.PrecioTotal             = cMoneda[1]["S"]+' '+formatDinero(aDashBoard['PrecioTotal']);
    DashBoard.ProductosTotal          = aDashBoard['ProductosTotal'];
    DashBoard.UtilidadTotal           = cMoneda[1]["S"]+' '+formatDinero(aDashBoard['UtilidadTotal']);
    DashBoard.ImpuestoTotal           = cMoneda[1]["S"]+' '+formatDinero(aDashBoard['ImpuestoTotal']);


}


function mostrarDatosDashBoard(){

    //COMPRAS
    id("vComprobantesPendientes").innerHTML = DashBoard.ComprobantesPendientes;
    id("vComprobantesBorrador").innerHTML   = DashBoard.ComprobantesBorrador;
    id("vPedidosBorrador").innerHTML        = DashBoard.PedidosBorrador;
    id("vPedidosPendientes").innerHTML      = DashBoard.PedidosPendientes;

    //ALMACEN
    id("vProductosStockMinimo").innerHTML   = DashBoard.ProductosStockMinimo;
    id("vProductosSinStock").innerHTML      = DashBoard.ProductosSinStock;
    //id("vProductos").innerHTML              = DashBoard.Productos;
    id("vProntoVencimiento").innerHTML      = DashBoard.ProntoVencimiento;
    id("vPedidosPorRecibir").innerHTML      = DashBoard.PedidosPorRecibir; 
    
    //FINANZAS
    id("vPagosPendientes").innerHTML        = DashBoard.PendientePorPagar;
    id("vPagosVencidos").innerHTML          = DashBoard.VencidoPorPagar;
    id("vCobrosPendientes").innerHTML       = DashBoard.PendientePorCobrar;
    id("vCobrosVencidos").innerHTML         = DashBoard.VencidoPorCobrar;
    id("vPromociones").innerHTML            = DashBoard.Promociones;
 
    //VENTAS
    id("vCreditosPendientes").innerHTML     = DashBoard.PendientesCreditos;
    //id("vServicios").innerHTML              = DashBoard.Servicios;
    id("vServiciosPendientes").innerHTML    = DashBoard.PendientesServicios;

    //PRE VENTA
    id("vPreventasPendientes").innerHTML    = DashBoard.PendientesPreventas;
    id("vProformaPendientes").innerHTML     = DashBoard.PendientesProforma;
    id("vReservasEntregar").innerHTML       = DashBoard.ReservasEntregar;
    id("vReservasPendientes").innerHTML     = DashBoard.PendientesReservas;
    
    //INVENTARIO ACTUAL
    id("vCostoTotal").innerHTML             = DashBoard.CostoTotal;
    id("vPrecioTotal").innerHTML            = DashBoard.PrecioTotal;
    id("vUtilidadTotal").innerHTML          = DashBoard.UtilidadTotal;
    id("vImpuestoTotal").innerHTML          = DashBoard.ImpuestoTotal;
    id("vProductosTotal").innerHTML         = DashBoard.ProductosTotal;

    if( !loadDemon ) Demon_syncDashBoard()//Lanza Demonio
}

function formatDinero(numero) {

    var num = new Number(numero);
    num = num.toString();

    if(isNaN(num)) num = "0";

    num = Math.round(num*100)/100;
    var sign = (num == (num = Math.abs(num)));
    num = num.toFixed(2);
    return (((sign)?'':'-') + num );   
}

function lanzademonio(){ setTimeout("mostrarDatosDashBoard()",100); }

function Demon_syncDashBoard(){
    loadDemon = true; //Control
    if(!parent.document.getElementById("WebLista").collapsed)
	syncDashBoard(); //Sync DashBoard
    setTimeout("Demon_syncDashBoard()",10000); //Recursivo
}

function syncDashBoard(){

    var xres,prod,xjsOut;
    var z   = null;	    
    var url = "../../services.php?modo=syncDashBoard";

    AjaxDemon.open("POST",url,false);
    AjaxDemon.setRequestHeader('Content-Type',
			       'application/x-www-form-urlencoded; charset=UTF-8');
    try {
	AjaxDemon.send(null);
	if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
	    return false;
    } catch(z){
	return false;
    }
    xjsOut    = AjaxDemon.responseText;
    xres      = parseInt(xjsOut);
    
    if(xres == 1) return false;

    try {	
	if (xjsOut) {
            eval(xjsOut);//“eval es el mal"
	}	
    } catch(e){	
	return false;
    }
    loadDatosDashBoard();
    mostrarDatosDashBoard();
}

function actualizarDashBoard(){
    var url = "../../services.php?modo=actualizarDashBoard";

    AjaxDemon.open("POST",url,false);
    AjaxDemon.setRequestHeader('Content-Type',
			       'application/x-www-form-urlencoded; charset=UTF-8');
    try {
	AjaxDemon.send(null);
	if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
	    return false;
    } catch(z){
	return false;
    }
    syncDashBoard();
}
