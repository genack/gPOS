
var id = function(name) { return document.getElementById(name); }
var idetallesMovimiento  = 0;
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
var cImpuesto             = "";
var cCodigo               = "";
var cEstado               = "";
var cIdAlmacen            = 0;
var cIdPedido             = 0;
var cIdPedidoDet          = 0;
var ilinealistamovimiento = 0;
var Vistas = new Object(); 
Vistas.ventas = 7;

// Opciones Busqueda avanzada
var vOperacion  = true;
var vMovimiento = true;
var vFamilia    = true;
var vMarca      = true;
var vDocumento  = true;

// Paginacion
var cInicioPagina   = 0;
var cTotalFilas     = 0;
var cFilasPagina    = 40;
var cCadenaBusqueda = "";


function VerMovimiento(){

    VaciarBusquedaMovimiento();
    BuscarMovimiento();
}

//Limpieza de Box
function VaciarBusquedaMovimiento(){
    var lista = id("listadoMovimiento");

    for (var i = 0; i < ilinealistamovimiento; i++) { 
        kid = id("linealistamovimiento_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilinealistamovimiento = 0;
}

//Busqueda 
function BuscarMovimiento(){
    VaciarBusquedaMovimiento();

    var filtrolocal      = id("FiltroMovimientoLocal").value;
    var filtrooperacion  = id("filtroOperacion").value;
    var filtromovimiento = id("filtroMovimiento").value;
    var desde            = id("FechaBuscaDesde").value;
    var hasta            = id("FechaBuscaHasta").value;
    var nombre           = id("NombreBusqueda").value;
    var codigo           = id("CodigoBusqueda").value;
    var marca            = id("idmarca").value;
    var familia          = id("idfamilia").value;

    RawBuscarMovimiento(desde,hasta,nombre,codigo,filtrooperacion,marca,familia,
			filtromovimiento,filtrolocal,AddLineaMovimiento);
    //if(forzaid) buscarPorCodigo(filtrocodigo);
}

function buscarPorCodigo(elemento){
    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaMovimiento");
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
            RevisarMovimientoSeleccionada();
            return;
        }
    }
    alert('gPOS:\n   - El codigo " '+elemento+' " no esta la lista.');
    //id("busquedaCodigoSerie").value='';
}

function RawBuscarMovimiento(desde,hasta,nombre,codigo,filtrooperacion,marca,familia,
			filtromovimiento,filtrolocal,FuncionProcesaLinea){

    var xcadena = "&desde=" + escape(desde)
        + "&hasta=" + escape(hasta)
	+ "&familia=" + escape(familia)
        + "&marca=" + escape(marca)
        + "&xnombre=" + escape(nombre)
        + "&xcodigo=" + escape(codigo)
        + "&xope=" + escape(filtrooperacion)
        + "&xmov=" + escape(filtromovimiento)
        + "&xlocal=" + escape(filtrolocal);

    iniComboPaginas(xcadena);

    var url = "selkardex.php?modo=kdxMovimientos"+xcadena
	+ "&xlistadesde=" + escape(cInicioPagina)
        + "&xnumfilas=" + escape(cFilasPagina);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,FechaMovimiento,KardexOperacion,Cantidad,CostoUnitario,Usuario,SaldoCantidad,TipoMovimiento,Producto,Almacen,IdPedidoDet,IdComprobanteDet,Documento,Detalle;
    var node,t,i,codmovimiento; 
    var totalMovimiento = 0;
    var totalMovimientoPendiente = 0;
    var ImporteTotalMovimiento = 0;
    var nroEntrada = 0;
    var nroSalida = 0;
    var nrototalmovimiento = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = parseInt(cTotalFilas) - parseInt(cInicioPagina);
    var tC = item;
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
	    IdKardex         = node.childNodes[t++].firstChild.nodeValue;
	    FechaMovimiento  = node.childNodes[t++].firstChild.nodeValue;
	    KardexOperacion  = node.childNodes[t++].firstChild.nodeValue;  
	    Cantidad         = node.childNodes[t++].firstChild.nodeValue; 
	    CostoUnitario    = node.childNodes[t++].firstChild.nodeValue;  
	    CostoTotal       = node.childNodes[t++].firstChild.nodeValue;  
	    Usuario          = node.childNodes[t++].firstChild.nodeValue;  
	    SaldoCantidad    = node.childNodes[t++].firstChild.nodeValue;
	    TipoMovimiento   = node.childNodes[t++].firstChild.nodeValue;
	    Producto         = node.childNodes[t++].firstChild.nodeValue;
	    Almacen          = node.childNodes[t++].firstChild.nodeValue;
	    IdPedidoDet      = node.childNodes[t++].firstChild.nodeValue;
	    IdProducto       = node.childNodes[t++].firstChild.nodeValue;
	    IdComprobanteDet = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal          = node.childNodes[t++].firstChild.nodeValue;
	    Contenedor       = node.childNodes[t++].firstChild.nodeValue;
	    Unidades         = node.childNodes[t++].firstChild.nodeValue;
	    UnidxCont        = node.childNodes[t++].firstChild.nodeValue;
	    Menudeo          = node.childNodes[t++].firstChild.nodeValue;
	    IdKdxAjusteOp    = node.childNodes[t++].firstChild.nodeValue;
	    IdInventario     = node.childNodes[t++].firstChild.nodeValue;
	    Documento        = node.childNodes[t++].firstChild.nodeValue;
	    Detalle          = node.childNodes[t++].firstChild.nodeValue;  

	    if (TipoMovimiento == 'Entrada') nroEntrada++; 
 	    if (TipoMovimiento == 'Salida')  nroSalida++; 
	
            FuncionProcesaLinea(item,IdKardex,FechaMovimiento,KardexOperacion,Cantidad,
				CostoUnitario,CostoTotal,Usuario,SaldoCantidad,TipoMovimiento,
				Producto,Almacen,IdPedidoDet,IdProducto,
				IdComprobanteDet,IdLocal,Contenedor,Unidades,UnidxCont,
				Menudeo,Documento,Detalle);
	    item--;
        }
    }
}

function AddLineaMovimiento(item,IdKardex,FechaMovimiento,KardexOperacion,Cantidad,
			    CostoUnitario,CostoTotal,Usuario,SaldoCantidad,TipoMovimiento,
			    Producto,Almacen,IdPedidoDet,IdProducto,
			    IdComprobanteDet,IdLocal,Contenedor,Unidades,UnidxCont,
			    Menudeo,Documento,Detalle){

    var lista = id("listadoMovimiento");
    var xnumitem,xitem,xFechaMovimiento,xKardexOperacion,xCantidad,xCostoUnitario,xUsuario,xSaldoCantidad,xTipoMovimiento,xProducto,xAlmacen,xDocumento,xDetalle,xLocal,vExistencias,vResto,vCantidad,vSaldoCantidad;

    //Cantidad
    vMenudeo       = parseFloat(Menudeo);
    vResto         = (vMenudeo)? Cantidad%UnidxCont:0;
    vCantidad      = (vMenudeo)? (Cantidad-vResto)/UnidxCont:Cantidad;
    vExistencias   = (vMenudeo)? vCantidad+' '+Contenedor+' '+vResto:vCantidad;
    vExistencias   = vExistencias+' '+Unidades;
    //Saldo
    vResto         = (vMenudeo)? SaldoCantidad%UnidxCont:0;
    vSaldoCantidad = (vMenudeo)? (SaldoCantidad-vResto)/UnidxCont:SaldoCantidad;
    vTtExistencias = (vMenudeo)? vSaldoCantidad+' '+Contenedor+' '+vResto:vSaldoCantidad;
    vTtExistencias = vTtExistencias+' '+Unidades;

    xitem = document.createElement("listitem");
    xitem.value = IdKardex;
    xitem.setAttribute("id","linealistamovimiento_"+ilinealistamovimiento);
    ilinealistamovimiento++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xFecha = document.createElement("listcell");
    xFecha.setAttribute("label",FechaMovimiento);
    xFecha.setAttribute("style","text-align:center;");
    xFecha.setAttribute("id","codigo_"+IdKardex);

    xDocumento = document.createElement("listcell");
    xDocumento.setAttribute("label",Documento);
    xDocumento.setAttribute("collapsed",vDocumento);
    xDocumento.setAttribute("style","text-align:left;font-weight:bold;");
    xDocumento.setAttribute("id","documento_"+IdKardex);

    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label",Detalle);
    xDetalle.setAttribute("id","detalle_"+IdKardex);
    xDetalle.setAttribute("style","text-align:left;");

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label",Producto);
    xProducto.setAttribute("id","producto_"+IdKardex);
    xProducto.setAttribute("style","text-align:left;font-weight:bold;");

    xIdProducto = document.createElement("listcell");
    xIdProducto.setAttribute("value",IdProducto);
    xIdProducto.setAttribute("collapsed","true");
    xIdProducto.setAttribute("id","idproducto_"+IdKardex);

    xIdLocal = document.createElement("listcell");
    xIdLocal.setAttribute("value",IdLocal);
    xIdLocal.setAttribute("collapsed","true");
    xIdLocal.setAttribute("id","idlocal_"+IdKardex);

    xCantidad = document.createElement("listcell");
    xCantidad.setAttribute("value",Cantidad);
    xCantidad.setAttribute("collapsed","true");
    xCantidad.setAttribute("id","cantidad_"+IdKardex);

    xSaldo = document.createElement("listcell");
    xSaldo.setAttribute("label",SaldoCantidad);
    xSaldo.setAttribute("collapsed","true");
    xSaldo.setAttribute("id","saldo_"+IdKardex);

    xOperacion = document.createElement("listcell");
    xOperacion.setAttribute("label",TipoMovimiento+' - '+KardexOperacion);
    xOperacion.setAttribute("id","operacion_"+IdKardex);
    xOperacion.setAttribute("style","text-align:left;");

    xMovimiento = document.createElement("listcell");
    xMovimiento.setAttribute("label",TipoMovimiento);
    xMovimiento.setAttribute("id","movimiento_"+IdKardex);

    xExistencias = document.createElement("listcell");
    xExistencias.setAttribute("label",vExistencias);
    xExistencias.setAttribute("id","existencias_"+IdKardex);
    xExistencias.setAttribute("style","text-align:right;font-weight:bold;");

    xCosto = document.createElement("listcell");
    xCosto.setAttribute("label",CostoUnitario);
    xCosto.setAttribute("id","costo_"+IdKardex);
    xCosto.setAttribute("style","text-align:right;");

    xTtExistencias = document.createElement("listcell");
    xTtExistencias.setAttribute("label",vTtExistencias);
    xTtExistencias.setAttribute("id","ttexistencias_"+IdKardex);
    xTtExistencias.setAttribute("style","text-align:right;font-weight:bold; ");

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", CostoTotal);
    xImporte.setAttribute("style","text-align:right;");
    xImporte.setAttribute("id","importe_"+IdKardex);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("style","text-align:center;");

    xAlmacen = document.createElement("listcell");
    xAlmacen.setAttribute("label", Almacen);
    xAlmacen.setAttribute("style","text-align:left;");


    xitem.appendChild( xnumitem );
    xitem.appendChild( xAlmacen );
    xitem.appendChild( xFecha );
    xitem.appendChild( xProducto );
    //xitem.appendChild( xMovimiento );
    xitem.appendChild( xOperacion );
    xitem.appendChild( xDocumento );
    //xitem.appendChild( xDetalle );
    xitem.appendChild( xExistencias );
    xitem.appendChild( xCosto );
    //xitem.appendChild( xImporte );
    xitem.appendChild( xTtExistencias );
    //xitem.appendChild( xUsuario );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xCantidad );
    xitem.appendChild( xSaldo );
    lista.appendChild( xitem );		
}

function RevisarMovimientoSeleccionada(){

    var idex      = id("listadoMovimiento").selectedItem;
    if(!idex) return;

    var xproducto = id("idproducto_"+idex.value).getAttribute("value");
    var xlocal    = id("idlocal_"+idex.value).getAttribute("value");
    var listado   = id("listadoMovimiento");
    var resumen   = id("resumenMovimiento");
    var busqueda  = id("busquedaMovimiento");
    var boxkardex = id("boxkardex");
    var webkardex = id("webkardex");
    var fdesde    = id("FechaBuscaDesde").value;
    var fhasta    = id("FechaBuscaHasta").value;
    var url       = "selkardex.php?"+
	            "modo=xMovimientosExistenciasKardex"+
	            "&xproducto="+xproducto+
	            "&xlocal="+xlocal+
	            "&xdesde="+fdesde+
	            "&xhasta="+fhasta;

    webkardex.setAttribute("src",url);  
    listado.setAttribute("collapsed","true");  
    resumen.setAttribute("collapsed","true");  
    busqueda.setAttribute("collapsed","true");  
    boxkardex.setAttribute("collapsed","false");  
}
 
function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Operacion": 
	vOperacion        = xchecked;
	break;
    case "Movimiento":
	vMovimiento       = xchecked;
	break;
    case "Familia":
	vFamilia          = xchecked;
	break;
    case "Marca" : 
	vMarca            = xchecked;
	break;
    case "Documento":
	vDocumento        = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarMovimiento();
}

function iniComboPaginas(xcadena){
    //totalPaginas,numFilasPaginas,totalFilasProductos
    
    if(xcadena == cCadenaBusqueda) return;

    obtenerNumFilas(xcadena);
    cInicioPagina = 0;
    id("listaPaginas").setAttribute('label','Pag. 1 - '+cFilasPagina);
    id("TotalMovimientos").value = cTotalFilas;

    var xval = (cTotalFilas > cFilasPagina)? false:true;
    id("listaPaginas").setAttribute('collapsed',xval);

    var numFilasPaginas     = cFilasPagina;
    var totalFilasProductos = cTotalFilas;
    var restoPaginas        = totalFilasProductos % numFilasPaginas; 
    var totalPaginas        = (totalFilasProductos - restoPaginas) / numFilasPaginas;
    
    if (restoPaginas > 0) totalPaginas++;
    
    var iniciopagina = id("iniciopagina").value;
    var listaPaginasMenu = id("listaPaginas");
    listaPaginasMenu.removeChild(id('comboPaginas'));

    var elementoMenu = document.createElement('menupopup');
    elementoMenu.setAttribute('id','comboPaginas');
    listaPaginasMenu.appendChild(elementoMenu);

    var combopagina = id("comboPaginas");
    var inipagina, endpagina; 
    var inipagina   = 0;
    var endpagina   = numFilasPaginas;
    var rangopagina = numFilasPaginas;

    for(var i = 0; i < totalPaginas; i++){

        var elemento = document.createElement('menuitem');
	
	var rangolist =parseFloat(inipagina)+parseFloat(1)+' - '+endpagina;
        elemento.setAttribute('label',rangolist);
        elemento.setAttribute('type','checkbox');
	elemento.setAttribute('id','itempag_'+inipagina);
        elemento.setAttribute("oncommand","Paginar("+inipagina+",'"+rangolist+"')");
	elemento.setAttribute('checked',false);
	if(iniciopagina == inipagina)
	    elemento.setAttribute('checked',true);
        combopagina.appendChild(elemento);

	inipagina = parseFloat(inipagina) + parseFloat(rangopagina); 
	endpagina = parseFloat(inipagina) + parseFloat(rangopagina);
    }
}

function Paginar(inipagina,rangolist){
    //alert(cInicioPagina+' '+inipagina);
    id("itempag_"+cInicioPagina).setAttribute('checked',false);
    id("itempag_"+inipagina).setAttribute('checked',true);

    cInicioPagina    = inipagina;
    id("listaPaginas").setAttribute('label','Pag. '+rangolist);

    BuscarMovimiento();
}

function obtenerNumFilas(xcadena){
   
    var url = "selkardex.php?modo=countMovimientos"+xcadena;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false); 
    xrequest.send(null);
    
    cTotalFilas     = (!isNaN(xrequest.responseText))? xrequest.responseText:0;
    cCadenaBusqueda = xcadena;
}