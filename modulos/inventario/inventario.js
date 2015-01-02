
var id = function(name) { return document.getElementById(name); }
var cIdInventario         = 0;
var cInventario           = 'Ajuste';
var cInventarioDate       = '';
var aInventario           = new Array();
var cEstadoInventario     = 'none';
var cTipoInventario       = 'none';
var cIdArticulo           = 0;
var cIdAlmacen            = 0;
var cIdPedido             = 0;
var cIdComprobante        = 0;
var cIdProducto           = 0;
var cProducto             = '';
var cCosto                = 0;
var cPrecio               = 0;
var cPVD                  = 0;
var cPVDD                 = 0;
var cPVC                  = 0;
var cPVCD                 = 0;
var cExistencias          = 0;
var ctExistencias         = '';
var cAjusteExistencias    = 0;
var cAjusteModo           = 'igual';
var cResumenKardex        = '';
var esSerie               = false;
var cSeries               = '';
var cSeriesVence          = '';
var esLote                = false;
var esAltaRapida          = false;
var esAltaRapidaTotal     = 0;
var esAltaRapidaResto     = 0;
var esAltaRapidaArr       = new Array();
var esVence               = false;
var esMenudeo             = false;
var cContenedor           = '';
var cEmpaques             = 0;
var cUnidades             = 0;
var cUnidxCont            = 0;
var cUnidad               = 'unid';
//var cImpuesto           = 0;
var ilinealistamovimiento = 0;
var ilinealistaalmacen    = 0;
var n                     = 0;
var Vistas                = new Object(); 
Vistas.ventas             = 7;

// Opciones Busqueda avanzada
var vMovimiento    = true;
var vFamilia       = true;
var vMarca         = true;
var vDetalle       = true;
var vUsuario       = true;
var vObservaciones = true;

// Paginacion
var cInicioPagina   = 0;
var cTotalFilas     = 0;
var cFilasPagina    = 20;
var cCadenaBusqueda = "";

function VerMovimiento(){
    VaciarBusquedaMovimiento();
    BuscarMovimiento();
    id("menuOperacionAjuste").setAttribute("collapsed",false);
    id("menuObservaciones").setAttribute("collapsed",false);
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

//Busqueda Kardex
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

function BuscarPorArticuloStock(xdato){

    var busca = trim(xdato);

    if(busca.length == 0) return;

    var lista = id("listadoAlmacen");
    var n     = lista.itemCount;

    if(n==0) return; 

    busca     = busca.toUpperCase();

    for (var i = 0; i < n; i++) 
    {
        var xtexto = lista.getItemAtIndex(i);
        var celdas = xtexto.getElementsByTagName('listcell');
        var cadena = celdas[21].getAttribute('value');
	if(busca == cadena)
            return lista.selectItem(xtexto);
    }
}


function BuscarPorProductoStock(xdato){

    var busca = trim(xdato);

    if(busca.length == 0) return;

    var lista = id("listadoAlmacen");
    var n     = lista.itemCount;

    if(n==0) return; 

    busca     = busca.toUpperCase();

    for (var i = 0; i < n; i++) 
    {
        var xtexto = lista.getItemAtIndex(i);
        var celdas = xtexto.getElementsByTagName('listcell');
        var cadena = celdas[10].getAttribute('value');
	if( busca == cadena )
            return lista.selectItem(xtexto);
    }

}

function RawBuscarMovimiento(desde,hasta,nombre,codigo,filtrooperacion,marca,familia,
			     filtromovimiento,filtrolocal,FuncionProcesaLinea){

  	var xcadena = "&desde=" + escape(desde)
        + "&hasta=" + escape(hasta)
	+ "&familia=" + escape(familia)
        + "&xinventario=" + escape(cInventario)
        + "&xidinventario=" + escape(cIdInventario)
        + "&marca=" + escape(marca)
        + "&xnombre=" + escape(nombre)
        + "&xcodigo=" + escape(codigo)
        + "&xope=" + escape(filtrooperacion)
        + "&xidope=" + escape(cIdInventario)
        + "&xmov=" + escape(filtromovimiento)
        + "&xlocal=" + escape(filtrolocal)

    iniComboPaginas(xcadena);

    var url = "../kardex/selkardex.php?modo=kdxMovimientosInventario"+xcadena
	+ "&xlistadesde=" + escape(cInicioPagina)
        + "&xnumfilas=" + escape(cFilasPagina);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false); 
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,FechaMovimiento,KardexOperacion,Cantidad,CostoUnitario,Usuario,SaldoCantidad,TipoMovimiento,Producto,Almacen,IdPedidoDet,IdComprobanteDet,Documento,Detalle,Observaciones;
    var node,t,i,codmovimiento; 
    var totalProductos = 0;
    var totalMovimiento = 0;
    var totalMovimientoPendiente = 0;
    var ImporteTotalMovimiento = 0;
    var nroEntrada         = 0;
    var nroSalida          = 0;
    var nrototalmovimiento = 0;
    var totalImporte       = 0;
    var aProducto          = Array();

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
	    Observaciones    = node.childNodes[t++].firstChild.nodeValue;
	    Documento        = node.childNodes[t++].firstChild.nodeValue;
	    Detalle          = node.childNodes[t++].firstChild.nodeValue;  

	    if (TipoMovimiento == 'Entrada') nroEntrada++; 
 	    if (TipoMovimiento == 'Salida')  nroSalida++; 
	    
	    if(!aProducto[IdProducto])
	    {
		aProducto[IdProducto] = SaldoCantidad;
		totalImporte  += CostoUnitario*aProducto[IdProducto];
	    }

	    totalProductos++;
	
            FuncionProcesaLinea(item,IdKardex,FechaMovimiento,KardexOperacion,Cantidad,
				CostoUnitario,CostoTotal,Usuario,SaldoCantidad,TipoMovimiento,
				Producto,Almacen,IdPedidoDet,IdProducto,
				IdComprobanteDet,IdLocal,Contenedor,Unidades,UnidxCont,
				Menudeo,Documento,Detalle,nroEntrada,nroSalida,
				totalImporte,totalProductos,Observaciones);
	    item--;
        }
    }
}

function AddLineaMovimiento(item,IdKardex,FechaMovimiento,KardexOperacion,Cantidad,
			    CostoUnitario,CostoTotal,Usuario,SaldoCantidad,TipoMovimiento,
			    Producto,Almacen,IdPedidoDet,IdProducto,
			    IdComprobanteDet,IdLocal,Contenedor,Unidades,UnidxCont,
			    Menudeo,Documento,Detalle,nroEntrada,nroSalida,
			    totalImporte,totalProductos,Observaciones){

    var lista = id("listadoMovimiento");
    var xnumitem,xitem,xFechaMovimiento,xKardexOperacion,xCantidad,xCostoUnitario,xUsuario,xSaldoCantidad,xTipoMovimiento,xProducto,xAlmacen,xDocumento,xDetalle,xLocal,vExistencias,vResto,vCantidad,vSaldoCantidad,xObservaciones;

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
    xclass         = (item%2)? 'parrow':'imparrow';  
    xitem          = document.createElement("listitem");
    xitem.value    = IdKardex;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistamovimiento_"+ilinealistamovimiento);
    ilinealistamovimiento++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xFecha = document.createElement("listcell");
    xFecha.setAttribute("label",FechaMovimiento);
    xFecha.setAttribute("style","text-align:center;");
    xFecha.setAttribute("id","mov_fecha_"+IdKardex);

    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label",Detalle);
    xDetalle.setAttribute("collapsed", vDetalle);
    xDetalle.setAttribute("id","mov_detalle_"+IdKardex);
    xDetalle.setAttribute("style","text-align:left;");

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label",Producto);
    xProducto.setAttribute("id","mov_producto_"+IdKardex);
    xProducto.setAttribute("style","text-align:left;font-weight:bold;");

    xIdProducto = document.createElement("listcell");
    xIdProducto.setAttribute("value",IdProducto);
    xIdProducto.setAttribute("collapsed","true");
    xIdProducto.setAttribute("id","mov_idproducto_"+IdKardex);

    xIdLocal = document.createElement("listcell");
    xIdLocal.setAttribute("value",IdLocal);
    xIdLocal.setAttribute("collapsed","true");
    xIdLocal.setAttribute("id","mov_idlocal_"+IdKardex);

    xCantidad = document.createElement("listcell");
    xCantidad.setAttribute("value",Cantidad);
    xCantidad.setAttribute("collapsed","true");
    xCantidad.setAttribute("id","mov_cantidad_"+IdKardex);

    xSaldo = document.createElement("listcell");
    xSaldo.setAttribute("label",SaldoCantidad);
    xSaldo.setAttribute("collapsed","true");
    xSaldo.setAttribute("id","mov_saldo_"+IdKardex);

    xOperacion = document.createElement("listcell");
    xOperacion.setAttribute("label",KardexOperacion);
    xOperacion.setAttribute("id","mov_operacion_"+IdKardex);
    xOperacion.setAttribute("style","text-align:left;");
    /**
    xMovimiento = document.createElement("listcell");
    xMovimiento.setAttribute("label",TipoMovimiento);
    xMovimiento.setAttribute("id","movimiento_"+IdKardex);
    **/
    xExistencias = document.createElement("listcell");
    xExistencias.setAttribute("label",vExistencias);
    xExistencias.setAttribute("id","mov_existencias_"+IdKardex);
    xExistencias.setAttribute("style","text-align:right;font-weight:bold;");

    xCosto = document.createElement("listcell");
    xCosto.setAttribute("label",CostoUnitario);
    xCosto.setAttribute("id","mov_costo_"+IdKardex);
    xCosto.setAttribute("style","text-align:right;");

    xTtExistencias = document.createElement("listcell");
    xTtExistencias.setAttribute("label",vTtExistencias);
    xTtExistencias.setAttribute("id","mov_ttexistencias_"+IdKardex);
    xTtExistencias.setAttribute("style","text-align:right;font-weight:bold; ");
    /**
    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", CostoTotal);
    xImporte.setAttribute("style","text-align:right;");
    xImporte.setAttribute("id","importe_"+IdKardex);
    **/
    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed", vUsuario);
    xUsuario.setAttribute("style","text-align:center;");

    xAlmacen = document.createElement("listcell");
    xAlmacen.setAttribute("label", Almacen);
    xAlmacen.setAttribute("style","text-align:left;");
    xAlmacen.setAttribute("id","mov_almacen_"+IdKardex);

    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label",Observaciones.slice(0,30)+'...');
    xObservaciones.setAttribute("value",Observaciones);
    xObservaciones.setAttribute("collapsed",vObservaciones);
    xObservaciones.setAttribute("id","mov_observaciones_"+IdKardex);
    xObservaciones.setAttribute("style","text-align:left;");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xAlmacen );
    xitem.appendChild( xProducto );
    xitem.appendChild( xOperacion );
    xitem.appendChild( xFecha );
    //xitem.appendChild( xDocumento );
    xitem.appendChild( xDetalle );
    //xitem.appendChild( xMovimiento );
    xitem.appendChild( xCosto );
    xitem.appendChild( xExistencias );
    //xitem.appendChild( xImporte );
    xitem.appendChild( xTtExistencias );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xCantidad );
    xitem.appendChild( xSaldo );
    lista.appendChild( xitem );		

    //nroEntrada,nroSalida,totalImporte,totalProductos

    id("TotalMovimientosListado").value = totalProductos;
    id("MovValorTotal").value           = cMoneda[1]['S']+" "+formatDinero(totalImporte);
}


//Busqueda Almacen
function BuscarAlmacen(){

    VaciarBusquedaAlmacen();

    if(!esAltaRapida)
	volverStock();

    var filtrolocal = id("FiltroMovimientoLocal").value;
    var marca       = id("idmarca").value;
    var familia     = id("idfamilia").value;
    var nombre      = id("NombreBusqueda").value;
    var codigo      = id("CodigoBusqueda").value;
    var stock       = id("idstock").value;

    //Control Busqueda
    if( !nombre ) 
	if( !familia && !marca && !codigo ) return;

    RawBuscarAlmacen(nombre,codigo,familia,marca,stock,
		     filtrolocal,AddLineaAlmacen);
    //if(forzaid) buscarPorCodigo(filtrocodigo);
}

function VaciarBusquedaAlmacen(){
    var lista = id("listadoAlmacen");

    for (var i = 0; i < ilinealistaalmacen; i++) { 
        kid = id("linealistaalmacen_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilinealistaalmacen = 0;
}

function RawBuscarAlmacen(nombre,codigo,familia,marca,stock,
			  filtrolocal,FuncionProcesaLineaAlmacen){

    var url = "../kardex/selkardex.php?modo=kdxAlmacenInventario"
	+ "&familia=" + escape(familia)
        + "&marca=" + escape(marca)
        + "&xinventario=" + escape(cInventario)
        + "&xnombre=" + escape(nombre)
        + "&xcodigo=" + escape(codigo)
	+ "&xstock=" + escape(stock)
        + "&xlocal=" + escape(filtrolocal);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false); 
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,IdAlmacen,IdProducto,IdLocal,FechaMovimiento,Unidades,Costo,PVD,PVDD,PVC,PVCD,Producto,Almacen,ResumenKardex,Cont,Unid,UnidxCont,Menudeo,Serie,Lote,Vence;
    var node,t,i;
    var totalProductos = 0;
    var totalStock     = 0;
    var totalSinStock  = 0;
    var totalImporte   = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var xml  = obj.responseXML.documentElement;
    //var item = xml.childNodes.length;
    var item = 0;
    var tC   = item;
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    item++;
            t = 0;

	    IdAlmacen       = node.childNodes[t++].firstChild.nodeValue;
	    IdProducto      = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal         = node.childNodes[t++].firstChild.nodeValue;
	    FechaMovimiento = node.childNodes[t++].firstChild.nodeValue;
	    Unidades        = node.childNodes[t++].firstChild.nodeValue;
	    Costo           = node.childNodes[t++].firstChild.nodeValue;
	    PVD             = node.childNodes[t++].firstChild.nodeValue;
	    PVDD            = node.childNodes[t++].firstChild.nodeValue;
	    PVC             = node.childNodes[t++].firstChild.nodeValue;
	    PVCD            = node.childNodes[t++].firstChild.nodeValue;
	    Producto        = node.childNodes[t++].firstChild.nodeValue;
	    Almacen         = node.childNodes[t++].firstChild.nodeValue;
	    ResumenKardex   = node.childNodes[t++].firstChild.nodeValue;
	    Cont            = node.childNodes[t++].firstChild.nodeValue;
	    Unid            = node.childNodes[t++].firstChild.nodeValue;
	    UnidxCont       = node.childNodes[t++].firstChild.nodeValue;
	    Menudeo         = node.childNodes[t++].firstChild.nodeValue;
	    Serie           = node.childNodes[t++].firstChild.nodeValue;
	    Lote            = node.childNodes[t++].firstChild.nodeValue;
	    Vence           = node.childNodes[t++].firstChild.nodeValue;

	    if ( Unidades >0   ) totalStock++; 
 	    if ( Unidades == 0 ) totalSinStock++; 
	    if ( Unidades >0   ) totalImporte  += Unidades*Costo;
	    totalProductos++;

            FuncionProcesaLineaAlmacen(item,IdAlmacen,IdProducto,IdLocal,FechaMovimiento,
				       Unidades,Costo,PVD,PVDD,PVC,PVCD,Producto,Almacen,
				       ResumenKardex,Cont,Unid,UnidxCont,Menudeo,Serie,Lote,Vence,
				       totalStock,totalImporte,totalSinStock,totalProductos);
        }
    }
}

function AddLineaAlmacen(item,IdAlmacen,IdProducto,IdLocal,FechaMovimiento,
			 Cantidad,Costo,PVD,PVDD,PVC,PVCD,Producto,Almacen,
			 ResumenKardex,Cont,Unid,UnidxCont,Menudeo,Serie,Lote,Vence,
			 totalStock,totalImporte,totalSinStock,totalProductos){

    var lista    = id("listadoAlmacen");
    var xnumitem,xitem,xFechaMovimiento,xUnidades,xCosto,xPVD,xPVDD,xPVC,xPVCD,xProducto,xAlmacen,vCantidad,vExistencias,xResumenKardex,xSerie,xLote,xVence,xMenudeo,xUnidxCont,xCont;
    var vResto,vMenudeo,vExistencias,vCantidad,xclass;

    //Cantidad
    vMenudeo     = parseFloat(Menudeo);
    vResto       = (vMenudeo)? Cantidad%UnidxCont:0;
    vCantidad    = (vMenudeo)? (Cantidad-vResto)/UnidxCont:Cantidad;
    vExistencias = (vMenudeo)? vCantidad+' '+Cont+' '+vResto:vCantidad;
    vExistencias = vExistencias+' '+Unid;
    xclass       = (item%2)?'imparrow':'parrow';  
    xitem        = document.createElement("listitem");
    xitem.value  = IdAlmacen;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistaalmacen_"+ilinealistaalmacen);
    ilinealistaalmacen++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xFechaMovimiento = document.createElement("listcell");
    xFechaMovimiento.setAttribute("label",FechaMovimiento);
    xFechaMovimiento.setAttribute("style","text-align:center;");
 
    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label",Producto);
    xProducto.setAttribute("id","producto_"+IdAlmacen);
    xProducto.setAttribute("style","text-align:left;font-weight:bold;");

    xIdProducto = document.createElement("listcell");
    xIdProducto.setAttribute("value",IdProducto);
    xIdProducto.setAttribute("collapsed","true");
    xIdProducto.setAttribute("id","idproducto_"+IdAlmacen);

    xIdArticulo = document.createElement("listcell");
    xIdArticulo.setAttribute("value",IdAlmacen);
    xIdArticulo.setAttribute("collapsed","true");
    xIdArticulo.setAttribute("id","idarticulo_"+IdAlmacen);

    xResumenKardex = document.createElement("listcell");
    xResumenKardex.setAttribute("value",ResumenKardex);
    xResumenKardex.setAttribute("collapsed","true");
    xResumenKardex.setAttribute("id","kardexresumen_"+IdAlmacen);

    xIdLocal = document.createElement("listcell");
    xIdLocal.setAttribute("value",IdLocal);
    xIdLocal.setAttribute("collapsed","true");
    xIdLocal.setAttribute("id","idlocal_"+IdAlmacen);

    xSerie = document.createElement("listcell");
    xSerie.setAttribute("value",Serie);
    xSerie.setAttribute("collapsed","true");
    xSerie.setAttribute("id","serie_"+IdAlmacen);

    xLote = document.createElement("listcell");
    xLote.setAttribute("value",Lote);
    xLote.setAttribute("collapsed","true");
    xLote.setAttribute("id","lote_"+IdAlmacen);

    xUnid = document.createElement("listcell");
    xUnid.setAttribute("value",Unid);
    xUnid.setAttribute("collapsed","true");
    xUnid.setAttribute("id","unidad_"+IdAlmacen);

    xMenudeo = document.createElement("listcell");
    xMenudeo.setAttribute("value",Menudeo);
    xMenudeo.setAttribute("collapsed","true");
    xMenudeo.setAttribute("id","menudeo_"+IdAlmacen);

    xUnidxCont = document.createElement("listcell");
    xUnidxCont.setAttribute("value",UnidxCont);
    xUnidxCont.setAttribute("collapsed","true");
    xUnidxCont.setAttribute("id","unidxcont_"+IdAlmacen);

    xCont = document.createElement("listcell");
    xCont.setAttribute("value",Cont);
    xCont.setAttribute("collapsed","true");
    xCont.setAttribute("id","cont_"+IdAlmacen);

    xVence = document.createElement("listcell");
    xVence.setAttribute("value",Vence);
    xVence.setAttribute("collapsed","true");
    xVence.setAttribute("id","vence_"+IdAlmacen);

    xCantidad = document.createElement("listcell");
    xCantidad.setAttribute("value",Cantidad);
    xCantidad.setAttribute("collapsed","true");
    xCantidad.setAttribute("id","cantidad_"+IdAlmacen);

    xExistencias = document.createElement("listcell");
    xExistencias.setAttribute("label",vExistencias);
    xExistencias.setAttribute("id","existencias_"+IdAlmacen);
    xExistencias.setAttribute("style","text-align:right;font-weight:bold;");

    xCosto = document.createElement("listcell");
    xCosto.setAttribute("label",formatDinero(Costo));
    xCosto.setAttribute("id","costo_"+IdAlmacen);
    xCosto.setAttribute("style","text-align:right;font-weight:bold;");

    xPVD = document.createElement("listcell");
    xPVD.setAttribute("label",PVD);
    xPVD.setAttribute("id","pvd_"+IdAlmacen);
    xPVD.setAttribute("style","text-align:right;");

    xPVDD = document.createElement("listcell");
    xPVDD.setAttribute("label",PVDD);
    xPVDD.setAttribute("id","pvdd_"+IdAlmacen);
    xPVDD.setAttribute("style","text-align:right;");

    xPVC = document.createElement("listcell");
    xPVC.setAttribute("label",PVC);
    xPVC.setAttribute("id","pvc_"+IdAlmacen);
    xPVC.setAttribute("style","text-align:right;");

    xPVCD = document.createElement("listcell");
    xPVCD.setAttribute("label",PVCD);
    xPVCD.setAttribute("id","pvcd_"+IdAlmacen);
    xPVCD.setAttribute("style","text-align:right;");

    xAlmacen = document.createElement("listcell");
    xAlmacen.setAttribute("label", Almacen);
    xAlmacen.setAttribute("style","text-align:left;");

    xitem.appendChild( xnumitem );
    xitem.appendChild( xAlmacen );
    xitem.appendChild( xFechaMovimiento );
    xitem.appendChild( xProducto );
    xitem.appendChild( xExistencias );
    xitem.appendChild( xCosto );
    xitem.appendChild( xPVD );
    xitem.appendChild( xPVDD );
    xitem.appendChild( xPVC );
    xitem.appendChild( xPVCD );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xResumenKardex );
    xitem.appendChild( xIdLocal );
    xitem.appendChild( xCantidad );
    xitem.appendChild( xSerie );
    xitem.appendChild( xLote );
    xitem.appendChild( xVence );
    xitem.appendChild( xUnid );
    xitem.appendChild( xMenudeo );
    xitem.appendChild( xCont );
    xitem.appendChild( xUnidxCont );
    xitem.appendChild( xIdArticulo );
    lista.appendChild( xitem );	

    //totalStock,totalImporte,totalSinStock,
    id("TotalProductos").value = totalProductos;
    id("conStock").value       = totalStock;
    id("sinStock").value       = totalSinStock;
    id("ValorTotal").value     = cMoneda[1]['S']+" "+formatDinero(totalImporte);
}

function VerObservCompra(){

    var idex      = id("listadoMovimiento").selectedItem;

    if(!idex) return;

    var idx       = idex.value;//IdCompraDet:false 
    var xalmacen  = id("mov_almacen_"+idx).getAttribute("label");
    var xproducto = id("mov_producto_"+idx).getAttribute("label");
    var xfecha    = id("mov_fecha_"+idx).getAttribute("label");
    var xoperacion= id("mov_operacion_"+idx).getAttribute("label");
    var xsaldo    = id("mov_ttexistencias_"+idx).getAttribute("label");
    var xobs      = id("mov_observaciones_"+idx).getAttribute("value");
    //Items?
    var xrest     = (trim(xobs))?false:true;
    var aobs;
    //Sin Item
    if(xrest) 
	xobs = '\n\n                               '+
	'- Sin observaciones - ';

    if(!xrest) 
	xobs = '\n                               '+
	'- '+xobs+' - ';
    //Termina
    return alert('gPOS: '+cInventario+'s \n\n'+
		 ' Operación        : '+xoperacion+'\n'+
		 ' Almacén           : '+xalmacen+'\n'+
		 ' Producto          : '+xproducto+'\n'+
		 ' Saldo               : '+xsaldo+'\n'+
		 ' Fecha               : '+xfecha+'\n'+
		 ' Observaciones : \n '+xobs+' \n ');
}
 
function RevisarMovimientoSeleccionada(){

    var idex      = id("listadoMovimiento").selectedItem;

    if(!idex) return;

    var xproducto = id("mov_idproducto_"+idex.value).getAttribute("value");
    var xlocal    = id("mov_idlocal_"+idex.value).getAttribute("value");
    var listado   = id("listadoMovimiento");
    var resumen   = id("resumenMovimiento");
    var busqueda  = id("busquedaMovimiento");
    var boxkardex = id("boxkardex");
    var webkardex = id("webkardex");
    var fdesde    = id("FechaBuscaDesde").value;
    var fhasta    = id("FechaBuscaHasta").value;
    var url       = "../kardex/selkardex.php?"+
	            "modo=xMovimientosExistenciasKardexInventario"+
	            "&xproducto="+xproducto+
	            "&xinventario="+cInventario+
	            "&xlocal="+xlocal+
	            "&xdesde="+fdesde+
	            "&xhasta="+fhasta;

    webkardex.setAttribute("src",url);  
    listado.setAttribute("collapsed","true");  
    resumen.setAttribute("collapsed","true");  
    busqueda.setAttribute("collapsed","true");  
    boxkardex.setAttribute("collapsed","false");  
    id("ajust").setAttribute("collapsed",true);
    id("invent").setAttribute("collapsed",true);
    id("btnVolver").setAttribute("collapsed",true);
    id("btnFinalizarInventario").setAttribute("collapsed",true);
    id("wtitleInventario").setAttribute("collapsed",false);
    id("wtitleInventario").setAttribute("label",cInventario+"s > Kardex");
}

function salirKardexProducto(){

    verKardexInventario();


    id("wtitleInventario").setAttribute("collapsed",true);
    id("ajust").setAttribute("collapsed",false);
    id("invent").setAttribute("collapsed",false);
    id("btnVolver").setAttribute("collapsed",false);
    id("wtitleInventario").setAttribute("label"," Inventario ");

    //Inventario btn continuar
    var xinvent    = (id("inventario_Pendiente"))? true:false;
    var vinvent    = (xinvent)? id("inventario_Pendiente").value:'';
    var cinvent    = id("filtroInventarios").value;
    var xcontinuar = ( vinvent == cinvent )? false:true; 
    id("btnFinalizarInventario").setAttribute("collapsed",xcontinuar);
}

function BuscarOperacionLocal(xid){

    window.location.href='modinventario.php?'+
	                 'modo=verInventario&'+
	                 'xlocal='+xid;
}

function nuevaOperacionAjuste(){

    BuscarAlmacen();
    //alert('nuevo Ajuste');
    cEstadoInventario = 'none';
    cTipoInventario   = 'none';
    cIdInventario     = 0;//cinvent
    cIdPedido         = 0;
    cIdComprobante    = 0;

    id("btnVolver").setAttribute("label",' Volver Ajustes');
    id("btnVolver").setAttribute("oncommand","mostrarOperacion('ajust')");
    id("btnVolver").setAttribute("image","../../img/gpos_volver.png");
    
    id("btnImprimirInventarioPDF").setAttribute("collapsed",true);
    id("btnImprimirInventarioCVS").setAttribute("collapsed",true);

    id("ajust").setAttribute("collapsed",true);
    id("invent").setAttribute("collapsed",true);
    id("wtitleInventario").setAttribute("collapsed",false);
    id("wtitleInventario").setAttribute("label","Ajustes > Stock Almacén");

    xlistboxInventario('almacen');

}

function nuevaOperacionInventario(xinv){
    
    BuscarAlmacen();
    //alert('nuevo Inventario');
    cTipoInventario   = xinv;
    cEstadoInventario = 'none';
    cIdInventario     = 0;//cinvent
    cIdPedido         = 0;
    cIdComprobante    = 0;

    xlistboxInventario('almacen');
    id("cmbInventarios").setAttribute("collapsed",true);
    id("wtitleInventario").setAttribute("label","Nuevo Inventario "+xinv+" > Stock Almacén");


    id("ajust").setAttribute("collapsed",true);
    id("invent").setAttribute("collapsed",true);
    id("wtitleInventario").setAttribute("collapsed",false);
    id("btnAltaRapida").setAttribute("collapsed",false);    
    id("btnImprimirInventarioPDF").setAttribute("collapsed",true);
    id("btnImprimirInventarioCVS").setAttribute("collapsed",true);

    id("btnVolver").setAttribute("label",' Volver Inventarios');
    id("btnVolver").setAttribute("oncommand","mostrarOperacion('invent')");
    id("btnVolver").setAttribute("image","../../img/gpos_volver.png");

}

function continuarOperacionInventario(){

    //alert('continuar Inventario');
    var xinvent = (id("inventario_Pendiente"))? true:false;
    var vinvent = (xinvent)? id("inventario_Pendiente").value:'';
    var linvent = (xinvent)? id("inventario_Pendiente").label:'';
    var cinvent = id("filtroInventarios").value;

    if(!(vinvent == cinvent)) return;

    BuscarAlmacen();

    var acinvent      = cinvent.split(":");//IdInventario:IdPedido:IdComprobante
    cIdInventario     = acinvent[0];
    cIdPedido         = acinvent[1];
    cIdComprobante    = acinvent[2];
    cEstadoInventario = 'Pendiente';

    id("btnVolver").setAttribute("label",' Volver Inventarios');
    id("btnVolver").setAttribute("oncommand","mostrarOperacion('invent')");
    id("btnVolver").setAttribute("image","../../img/gpos_volver.png");

    xlistboxInventario('almacen');


    id("ajust").setAttribute("collapsed",true);
    id("invent").setAttribute("collapsed",true);
    id("btnImprimirInventarioPDF").setAttribute("collapsed",true);
    id("btnImprimirInventarioCVS").setAttribute("collapsed",true);
    id("btnAltaRapida").setAttribute("collapsed",false);
    id("wtitleInventario").setAttribute("collapsed",false);
    id("wtitleInventario").setAttribute("label","Inventario "+linvent+" > Stock Almacén");

}

function finalizaOperacionInventario(){

    var inv = id("inventario_Pendiente").label;
    if(!confirm('gPOS: Kardex - Inventario \n\n Finalizar Inventario '+inv+
		','+' ¿desea continuar?')) return;
    var xlocal = id("FiltroMovimientoLocal").value
    var url    = "../kardex/selkardex.php?modo=kdxFinalizaInventario"
	         + "&xidinventario=" + escape(cIdInventario)
	         + "&xlocal=" + escape(xlocal);

    var newinv   = inv.split("- ");
    var xrequest = new XMLHttpRequest();
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequest.send(null);

    if (!parseInt(xrequest.responseText) )
        return alert(po_servidorocupado);

    id("inventario_Pendiente").setAttribute("label",newinv[0]+"- Finalizado");
    id("inventario_Pendiente").setAttribute("id","inventario_Finalizado");
    mostrarOperacion('invent');
}

function xlistboxInventario(xlist){

    var xresukardex = true;
    var xlistkardex = true;
    var xresualma   = true;
    var xlistalma   = true;
    var xmov        = true;
    var xfecha      = true;
    var xkeyope     = 'if (event.which == 13)  BuscarMovimiento()';
    var xope        = 'BuscarMovimiento()'
    var cmbinv      = true;
    var xbtnvolver  = true;
    var xstock      = true;

    switch (xlist) {

    case 'almacen':
	xresualma  = false;
	xlistalma  = false;
	xkeyope    = 'if (event.which == 13)  BuscarAlmacen()';
	xope       = 'BuscarAlmacen()'
	xbtnvolver = false;
	xstock     = false;
	break;

    case 'kardex':
	xmov        = false;
	xfecha      = false;
	xresukardex = false;
	xlistkardex = false;	
	cmbinv      = (cInventario=="Inventario")?false:true;
	xinvent     = true;
	break;
    }

    id("cmbStock").setAttribute("collapsed",xstock);
    id("btnVolver").setAttribute("collapsed",xbtnvolver);
    id("cmbInventarios").setAttribute("collapsed",cmbinv)
    id("fechaDesde").setAttribute("collapsed",xfecha);
    id("fechaHasta").setAttribute("collapsed",xfecha);
    //id("vboxMovimiento").setAttribute("collapsed",xmov);
    id("filtroMovimiento").setAttribute("oncommand",xope);
    id("NombreBusqueda").setAttribute("onkeyup",xkeyope);
    id("CodigoBusqueda").setAttribute("onkeyup",xkeyope);
    id("btnbuscar").setAttribute("oncommand",xope);
    id("idfamilia").setAttribute("oncommand",xope);
    id("idmarca").setAttribute("oncommand",xope);
    id("resumenAlmacen").setAttribute("collapsed",xresualma);
    id("listadoAlmacen").setAttribute("collapsed",xlistalma);
    id("resumenMovimiento").setAttribute("collapsed",xresukardex);
    id("listadoMovimiento").setAttribute("collapsed",xlistkardex);
    id("NombreBusqueda").focus();
}

function mostrarOperacion(xval){

    //BuscarInventarioPendiente
    setInventarioPendiente();

    var xcmbinv     = true;
    var xfecha      = true;
    var xmenuop     = true;
    var xmenuopal   = true;
    var xinvent     = (id("inventario_Pendiente"))? true:false;
    var vinvent     = (xinvent)? id("inventario_Pendiente").value:'';
    var ltinvent    = id("filtroInventarios");
    var numltinvent = ltinvent.itemCount;
    var cinvent     = ltinvent.value;
    var acinvent    = cinvent.split(":");//IdInventario:IdPedido:IdComprobante
    var xope        = '';
    var xcontinuar  = true; 
    var xinventario = true;
    var xinventprint= true;
    var xinventini  = true;
    var xajuste     = true;
    var xmenualta   = true;
    var xfechainv   = true;
    var xbtnvolver  = true;
    var xtxtinvent  = '';
    var lbtnvolver  = ' Volver Inventarios';
    var cbtnvolver  = "mostrarOperacion('invent')";
    var mbtnvolver  = "../../img/gpos_volver.png";
    var txtInvent   = 'Inventarios';

    //nels
    var ltajust     = id("listadoMovimiento");
    var numltajust  = ltajust.itemCount;

    switch (xval) {

    case 'Ajuste':
    case 'ajust':
	xfecha            = false;
	xajuste           = false;
	xope              = 5;
	cEstadoInventario ='none';
	cIdInventario     = 0;
	cIdPedido         = 0;
	cIdComprobante    = 0;
	//lbtnvolver        = ' Volver Ajustes'
	//cbtnvolver        = "mostrarOperacion('ajust')"
	cInventario       = 'Ajuste';
	xlistkdxresumen   = 'Listado de Ajustes';
	lbtnvolver        = ' Nuevo Ajuste';
	cbtnvolver        = "nuevaOperacionAjuste()";
	xbtnvolver        = false;
	mbtnvolver        = "../../img/gpos_nuevoajuste.png";
	xinventprint      = (numltajust != 0)? false:true;
	
	break;

    case 'Inventario':
    case 'invent':
 	xcmbinv         = false; 
	xajuste         = true;
	xmenualta       = false;
	xfechainv       = false;
	xope            = 6;
	cInventario     = 'Inventario';
	xlistkdxresumen = 'Listado de Ajustes Inventario';
	cIdInventario   = acinvent[0];//cinvent
	cIdPedido       = acinvent[1];//cinvent
	cIdComprobante  = acinvent[2];//cinvent
	cInventarioDate = ( acinvent[3] )? acinvent[3]:'';//fecha  inventario
	xinventprint    = ( cIdInventario!=0 )? false:true;
	txtInvent       = ( cIdInventario!=0 )? 'Inventario '+ltinvent.getAttribute('label'):txtInvent;
	xcontinuar      = ( vinvent == cinvent )? false:true; 
	xinventini      = ( numltinvent == 1 && cIdInventario == 0 )? false:true;
	xinventario     = ( xinvent || !xinventini )? true:false;
	xmenuopal       = ( xinvent )? false:true;
	xtxtinvent      = ( !xinventini )? 'Inicial':'Periodico';
	lbtnvolver      = ( vinvent == cinvent )? ' Continuar Inventario':' Nuevo Inventario '+xtxtinvent;
	cbtnvolver      = ( vinvent == cinvent )? "continuarOperacionInventario()":"nuevaOperacionInventario('"+xtxtinvent+"')";
	xbtnvolver      = ( vinvent == cinvent )? false:true;
	xbtnvolver      = ( !xinventario       )? false:xbtnvolver;
	xbtnvolver      = ( !xinventini        )? false:xbtnvolver;
	mbtnvolver      = ( vinvent == cinvent )? "../../img/gpos_continuarinventario.png":"../../img/gpos_nuevoinventario.png";
	break;
    }

    verKardexInventario();
    xlistboxInventario('kardex');

    id("TotalMovimientos").value = 0;
    id("MovValorTotal").value    = 0.00;
    id("ajust").setAttribute("collapsed",false);
    id("invent").setAttribute("collapsed",false);
    id("fechaInventario").setAttribute("collapsed",xfechainv);
    id("fechaInventario").setAttribute("label",cInventarioDate.replace(/~/g,":"));
    id("wtitleInventario").setAttribute("collapsed",true);
    id("wtitleInventario").setAttribute("label","Inventario");
    id("formAjustesExistencias").setAttribute("collapsed",true);
    id("btnVolver").setAttribute("collapsed",xbtnvolver);
    id("btnVolver").setAttribute("label",lbtnvolver);
    id("btnVolver").setAttribute("oncommand",cbtnvolver);
    id("btnVolver").setAttribute("image",mbtnvolver);
    id("btnAltaRapida").setAttribute("collapsed",true);
    id("menuAltaRapida").setAttribute("collapsed",xmenualta);
    id("listKardexResumen").setAttribute("label",xlistkdxresumen);
    id("invent").setAttribute("label",txtInvent);
    id("filtroOperacion").setAttribute("value",xope);
    id("menuOperacionAjuste").setAttribute("collapsed",xajuste);
    id("menuOperacionInventario").setAttribute("collapsed",xinventario);
    id("menuOperacionInventarioInicial").setAttribute("collapsed",xinventini);
    id("menuOperacionContinuar").setAttribute("collapsed",xcontinuar);
    id("menuOperacionFinalizar").setAttribute("collapsed",xcontinuar);
    id("btnFinalizarInventario").setAttribute("collapsed",xcontinuar);
    id("btnImprimirInventarioPDF").setAttribute("collapsed",xinventprint);
    id("btnImprimirInventarioCVS").setAttribute("collapsed",xinventprint);
    id("menuAlmacenFinalizarInventario").setAttribute("collapsed",xmenuopal);
    id("fechaHasta").setAttribute("collapsed",xfecha);
    id("fechaDesde").setAttribute("collapsed",xfecha);
    
    BuscarMovimiento();
}

function setInventarioPendiente(){

    if( id("inventario_Pendiente") )
	id("filtroInventarios").value = id("inventario_Pendiente").value; 	
}

function altarapidaArticulo(){

    var url = "../altarapida/xulaltarapida.php?modo=altainventario";
    
    id("webkardex").setAttribute("src",url);  
    id("formAjustesExistencias").setAttribute("collapsed",true);
    id("boxkardex").setAttribute("collapsed",false);  
    id("listadoAlmacen").setAttribute("collapsed",true);
    id("resumenAlmacen").setAttribute("collapsed",true);
    id("btnVolver").setAttribute("collapsed",true);
    id("btnFinalizarInventario").setAttribute("collapsed",true);
    //id("btnVolver").setAttribute("oncommand","volverStock()");
}

function modificarArticuloSeleccionada(){

    var idex       = id("listadoAlmacen").selectedItem;
    
    if(!idex) return;

    cIdArticulo    = idex.value;
    cIdAlmacen     = id("idlocal_"+idex.value).getAttribute("value");
    cProducto      = id("producto_"+idex.value).getAttribute("label");
    cIdProducto    = id("idproducto_"+idex.value).getAttribute("value");
    cExistencias   = parseFloat( id("cantidad_"+cIdArticulo).getAttribute("value") );
    cResumenKardex = id("kardexresumen_"+idex.value).getAttribute("value");
    esSerie        = (id("serie_"+idex.value).getAttribute("value")!='0')? true:false;
    esVence        = (id("vence_"+idex.value).getAttribute("value")!='0')? false:true;
    esLote         = (id("lote_"+idex.value).getAttribute("value") !='0')? false:true;
    esMenudeo      = (id("menudeo_"+idex.value).getAttribute("value") !='0')? false:true;
    cContenedor    = id("cont_"+idex.value).getAttribute("value");
    ctExistencias  = id("existencias_"+idex.value).getAttribute("label");
    cUnidxCont     = parseFloat( id("unidxcont_"+idex.value).getAttribute("value") );    

    cUnidades      = cExistencias%cUnidxCont;
    cEmpaques      = (cExistencias-cUnidades)/cUnidxCont;

    cUnidad        = id("unidad_"+idex.value).getAttribute("value");
    cCosto         = id("costo_"+idex.value).getAttribute("label");
    cPVD           = id("pvd_"+idex.value).getAttribute("label");
    cPVDD          = id("pvdd_"+idex.value).getAttribute("label");
    cPVC           = id("pvc_"+idex.value).getAttribute("label");
    cPVCD          = id("pvcd_"+idex.value).getAttribute("label");
    cPrecio        = parseFloat(cCosto*(parseFloat(cImpuesto)+100)/100).toFixed(2);

    id("formAjustesExistencias").setAttribute("collapsed",false);
    id("btnVolver").setAttribute("collapsed",true);
    id("btnFinalizarInventario").setAttribute("collapsed",true);
    id("listadoAlmacen").setAttribute("collapsed",true);  
    id("resumenAlmacen").setAttribute("collapsed",true);  
    id("btnModificarStock").setAttribute("oncommand","validaModificarStock()");
    id("btnModificarStock").setAttribute("label"," Modificar...");
    id("postAjusteBox").setAttribute("collapsed",true);
    id("xProducto").setAttribute("label",cProducto);  
    id("rowLote").setAttribute("collapsed",true);
    id("rowVencimiento").setAttribute("collapsed",true);
    id("rowAjusteSalida").setAttribute("collapsed",true);
    id("webkardex").setAttribute("src","about:blank");  
    id("xExistenciasFisico").removeAttribute("readonly");
    id("xExistenciasFisico").focus();
    id("xAjusteOperacionSalida").label  = "Elige motivo ajuste";
    id("xAjusteOperacionEntrada").label = "Elige motivo ajuste";
    id("rowContenedor").setAttribute("collapsed",esMenudeo);
    id("rowExistencias").setAttribute("collapsed",!esMenudeo);
    id("rowDatoContenedor").setAttribute("collapsed",esMenudeo);
    id("xUnidxEmpaque").setAttribute("value",cUnidxCont+''+cUnidad+'/'+cContenedor);
    id("xEmpaques").value          = cEmpaques;
    id("xUnidades").value          = cUnidades;
    id("xLote").value              = '';
    id("xObservacion").value       = '';
    id("xAjuste").value            = '0 '+cUnidad;
    id("xExistencias").value       = ctExistencias;
    id("xExistenciasFisico").value = cExistencias;  
    id("xValorCompra").value       = cCosto;  
    id("xPrecioCompra").value      = cPrecio;  
    id("xPVD").value               = cPVD;  
    id("xPVDD").value              = cPVDD;  
    id("xPVC").value               = cPVC;  
    id("xPVCD").value              = cPVCD;  
    cAjusteModo                    = 'igual';
    cAjusteExistencias             = 0;
    cSeries                        = false;
    cSeriesVence                   = false;
}

function validaDatoMenudeo(xthis){

    if( xthis.value == '')
    {
	xthis.value = 0;
	return;
    }
    
    var xMenudeo  = 0;
    var xUnidades = parseInt( id("xUnidades").value );
    var xEmpaques = parseInt( id("xEmpaques").value );

    if( xUnidades > cUnidxCont || xUnidades == cUnidxCont )
    {
	xthis.value = 0;
	xUnidades   = 0;
    }

    xMenudeo = xEmpaques*cUnidxCont+xUnidades;
    totalAjusteExistencias( xMenudeo );
}

function modificarArticuloSeleccionadaAltaRapida(){

    var idex       = id("listadoAlmacen").selectedItem;
    
    if(!idex) return;

    cIdArticulo    = idex.value;
    cIdAlmacen     = id("idlocal_"+idex.value).getAttribute("value");

    cProducto      = id("producto_"+idex.value).getAttribute("label");
    cIdProducto    = id("idproducto_"+idex.value).getAttribute("value");
    cExistencias   = id("cantidad_"+idex.value).getAttribute("value");
    cResumenKardex = id("kardexresumen_"+idex.value).getAttribute("value");
    esSerie        = (id("serie_"+idex.value).getAttribute("value")!='0')? true:false;
    esVence        = (id("vence_"+idex.value).getAttribute("value")!='0')? false:true;
    esLote         = (id("lote_"+idex.value).getAttribute("value") !='0')? false:true;

    cAjusteModo                    = 'igual';
    cAjusteExistencias             = 0;
    cSeries                        = false;
    cSeriesVence                   = false;
}

function validaModificarStock(){

    var xcmdbtn,xtxtbtn;
    var xSalida  = true;
    var xEntrada = true;
    var xLote    = true;
    var xVence   = true;
    var esInventario = ( cInventario == 'Inventario' )? true:false;

    switch (cAjusteModo) {

    case 'mas':
	cAjusteExistencias = (-1)*cAjusteExistencias;
	xcmdbtn  = "agregaStock(false)";
	xtxtbtn  = " Aceptar...";
	xEntrada = false;
	xLote    = (!esLote)? false:true;
	xVence   = (!esVence)? false:true;
	break;

    case 'igual':
	if(esInventario)
	    return igualStock();
	return volverStock();
	break;

    case 'cero':
	xcmdbtn = "quitaStock(false)";
	xtxtbtn = " Aceptar...";
	xSalida = false;
	break;
 
    case 'menos':
	xcmdbtn = "quitaStock(false)";
	xtxtbtn = " Aceptar...";
	xSalida = false;
	break;
    }

    id("rowLote").setAttribute("collapsed",xLote);
    id("rowVencimiento").setAttribute("collapsed",xVence);
    id("btnModificarStock").setAttribute("oncommand",xcmdbtn);
    id("btnModificarStock").setAttribute("label",xtxtbtn);
    id("rowAjusteEntrada").setAttribute("collapsed",xEntrada);
    id("rowAjusteSalida").setAttribute("collapsed",xSalida);
    id("xExistenciasFisico").setAttribute("readonly",true);
    id("xCantidadAjuste").setAttribute("value", cAjusteExistencias+' '+cUnidad);
    id("postAjusteBox").setAttribute("collapsed",false);
}
function igualStock(){

    var esInventario = ( cInventario == 'Inventario' )? true:false;
    var xrequest = new XMLHttpRequest();
    var xrequest = new XMLHttpRequest();
    var url      = "../kardex/selkardex.php?modo=kdxIgualExistencias"+
	           "&xarticulo="+cIdArticulo+
	           "&xproducto="+cIdProducto+
	           "&xlocal="+cIdAlmacen+
	           "&xinventario="+cInventario+
	           "&xidinventario="+cIdInventario+
	           "&xestinvent="+cEstadoInventario+
	           "&xtipoinventario="+cTipoInventario+
	           "&xpedido="+cIdPedido+
	           "&xcomprobante="+cIdComprobante+
	           "&xpvd="+cPVD+
	           "&xpvdd="+cPVDD+
	           "&xpvc="+cPVC+
	           "&xpvcd="+cPVCD;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequest.send(null);
    xres        = xrequest.responseText;

    aInventario = xres.split("~");

    if (!parseInt(aInventario[0]) )
    {
	alert(po_servidorocupado);
	return mostrarOperacion(cInventario);
    }

    if( cInventario=="Inventario" && cIdInventario == '0' )
	actualizaComboInventarios(aInventario);

    cIdInventario     = aInventario[1];
    cIdPedido         = aInventario[2];
    cIdComprobante    = aInventario[3];
    cEstadoInventario = ( esInventario )? 'Pendiente':'none';

    //Carga cambios
    volverStock();
    BuscarAlmacen();
}

function quitaStock(xget){

    var xrow         = id("listadoAlmacen").selectedItem;
    var xLote        = (!esLote)? id("xLote").value:'';
    var xVence       = (!esVence)? id("xVencimiento").value:'';
    var LoteVence    = xLote+"/"+xVence;
    var esOpeAjuste  = ( id("xAjusteOperacionSalida").value != "Elige motivo ajuste" )? true:false;
    var xOpeAjuste   = id("xAjusteOperacionSalida").value;
    var xObs         = id("xObservacion").value;
    var aExistencias = parseFloat(cExistencias+cAjusteExistencias);
    var esInventario = ( cInventario == 'Inventario' )? true:false;
 
    //Operacion Ajuste
    if( !esOpeAjuste ) 
 	return alert("gPOS: Ajustes Existencias \n\n Elije - Motivo Ajuste - "+
		     "de las existenias a ajustar.");

    //Ajuste kardex
    if(!xget)
	return getAjusteKardexResumen();

    //return alert(cAjusteExistencias+' '+cSeries);

    var xrequest = new XMLHttpRequest();
    var url      = "../kardex/selkardex.php?modo=kdxSalidaExistencias"+
	           "&xarticulo="+cIdArticulo+
	           "&xproducto="+cIdProducto+
	           "&xlocal="+cIdAlmacen+
	           "&xinventario="+cInventario+
	           "&xidinventario="+cIdInventario+
	           "&xestinvent="+cEstadoInventario+
	           "&xtipoinventario="+cTipoInventario+
	           "&xpedido="+cIdPedido+
	           "&xcomprobante="+cIdComprobante+
	           "&xopeajuste="+xOpeAjuste+
	           "&xcosto="+cCosto+
	           "&xprecio="+cPrecio+
	           "&xpvd="+cPVD+
	           "&xpvdd="+cPVDD+
	           "&xpvc="+cPVC+
	           "&xpvcd="+cPVCD+
	           "&esserie="+esSerie+
	           "&lotevence="+LoteVence+
	           "&serievence="+cSeriesVence+
                   "&xajustes="+cAjusteExistencias;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequest.send("numerosdeserie="+cSeries+"&xobservacion='"+xObs+"'");
    xres        = xrequest.responseText;

    aInventario = xres.split("~");

    if (!parseInt(aInventario[0]) )
    {
	alert(po_servidorocupado+' \n\n Ajuste -> Quita Stock > '+ cInventario +':::\n\n'+xres);
	
	return mostrarOperacion(cInventario);
    }

    if( cInventario=="Inventario" && cIdInventario == '0' )
	actualizaComboInventarios(aInventario);

    cIdInventario     = aInventario[1];
    cIdPedido         = aInventario[2];
    cIdComprobante    = aInventario[3];
    cEstadoInventario = ( esInventario )? 'Pendiente':'none';

    //Carga cambios
    volverStock();
    BuscarAlmacen();
    BuscarPorArticuloStock(cIdArticulo);
    actualizaComboAjusteOperacion('Salida',xOpeAjuste);


}

function getAjusteKardexResumen(){

    var url = "../kardex/selkardex.php?modo=xInventarioAlmacenCarrito"+
	      "&xproducto="+cIdProducto+
 	      "&xlocal="+cIdAlmacen+
	      "&xinventario="+cInventario+
              "&xajuste="+cAjusteExistencias+
	      "&xalmacen="+cIdArticulo;
    
    id("webkardex").setAttribute("src",url);  
    id("formAjustesExistencias").setAttribute("collapsed",true);
    id("boxkardex").setAttribute("collapsed","false");  
    
    //-> retorna con ckAjusteGuardar()
}

function ckAjusteGuardar(xSeries,xCantidad,xPedidoDet)
{
    //return alert(xPedidoDet.toString());
    var ptrans  = '';
    var srt     = '';
    var srtns   = '';
    var nstrans = '';
    var lftrans = '';

    //Lote & Vence -> lote vence
    //Cantidad -> idpedidodet:cantidad~
    //Serie -> idpedidodet:serie1,serie2~

    for(var i = 0; i<xPedidoDet.length; i++)
    {

      cantrans    = xCantidad['cAlmacen_'+xPedidoDet[i]];
      preciotrans = xCantidad['cAlmacenPrecio_'+xPedidoDet[i]];
      lftrans     = xCantidad['cAlmacenLF_'+xPedidoDet[i]];
      lftrans     = ( lftrans )? ':'+lftrans:'';

      if(cantrans>0)
        {
          ptrans    = ptrans+srt+xPedidoDet[i]+':'+cantrans+':'+preciotrans+lftrans;
 	  srt       = '~'; 
	}

      if(xSeries && cantrans>0)
          { 
            nstrans   = nstrans+srtns+xPedidoDet[i]+':'+xSeries[xPedidoDet[i]];
 	    srtns     = '~'; 
	  }
    }

    if(ptrans == '')
	return volverStock();

    cAjusteExistencias = ptrans;
    cSeries            = nstrans;

    quitaStock(true);
}

function agregaStockAltaRapida(arProducto,xlist,xitem){

    //Inicia 
    if(!esAltaRapida)
    {
	esAltaRapidaArr   = arProducto;
        esAltaRapidaTotal = xlist;    
    }

    //Valida 
    esAltaRapida      = ( xitem == xlist )? false:true;
    esAltaRapidaResto = xitem;

    //Termina Brutal
    if(!esAltaRapida)
    {
	esAltaRapidaTotal = 0;
	esAltaRapidaResto = 0;
	arProducto        = new Array();
	esAltaRapidaArr   = new Array();
	return BuscarAlmacen();
    }

    //Inicia Inventario
    var t              = esAltaRapidaResto;
    var cb             = arProducto[t];
    id("CodigoBusqueda").value = cb;

    BuscarAlmacen();    
    BuscarPorProductoStock( arProducto[cb+'_idproducto'] );
    modificarArticuloSeleccionadaAltaRapida();

    //Actualiza Valores Formulario
    cAjusteExistencias = arProducto[cb+'_cantidad'];
    cCosto             = arProducto[cb+'_costo'];
    cPVD               = arProducto[cb+'_pvd'];
    cPVDD              = arProducto[cb+'_pvdd'];
    cPVC               = arProducto[cb+'_pvc'];
    cPVCD              = arProducto[cb+'_pvcd'];
    cPrecio            = parseFloat(cCosto*(parseFloat(cImpuesto)+100)/100).toFixed(2);

    esVence            = (arProducto[cb+'_fv'])? false:true;
    esLote             = (arProducto[cb+'_lt'])? false:true;

    id("xAjusteOperacionEntrada").label  = "Inicio de operaciones";
    id("xLote").value                    = (!esLote)?  arProducto[cb+'_lt']:'';
    if(!esVence)
	id("xVencimiento").value         =  arProducto[cb+'_fv'];

    //Siguiente Item
    t++;
    esAltaRapidaResto = t;
    
    //Ejecuta Registro Inventario
    id("CodigoBusqueda").value = '';
    agregaStock(false);

}

function agregaStock(xget){

    var xrow         = id("listadoAlmacen").selectedItem;
    var xLote        = (!esLote)? id("xLote").value:'';
    var xVence       = (!esVence)? id("xVencimiento").value:'';
    var LoteVence    = xLote+"/"+xVence;
    var esOpeAjuste  = ( id("xAjusteOperacionEntrada").label != "Elige motivo ajuste" )? true:false;
    var xOpeAjuste   = id("xAjusteOperacionEntrada").label;
    var xObs         = id("xObservacion").value;
    var aExistencias = parseFloat(cExistencias+cAjusteExistencias);
    var esInventario = ( cInventario == 'Inventario' )? true:false;
    //Lote
    if( !esLote && !xLote ) 
	return alert("gPOS:Ajustes\n\n Ingrese - Lote - "+
		     "de las existenias ajustar");

    var fvence  = xVence.replace(/-/g,',');
    var f       = new Date();
    var hoy     = f.getFullYear()+","+(f.getMonth() + 1)+","+f.getDate();
    var compara = comparaFechas(hoy,fvence);
    
    if(compara >= 0) 
	return alert("gPOS: \n\n           Fecha de vencimiento incorrecto");

    //Operacion Ajuste
    if( !esOpeAjuste ) 
	return alert("gPOS: Ajustes Existencias \n\n Elije - Motivo Ajuste - "+
		     "de las existenias a ajustar.");
    //Series
    if( esSerie && !xget )
	return getAgregaSeries();

    var xrequest = new XMLHttpRequest();
    var url      = "../kardex/selkardex.php?modo=kdxEntradaExistencias"+
	           "&xarticulo="+cIdArticulo+
	           "&xproducto="+cIdProducto+
	           "&xlocal="+cIdAlmacen+
	           "&xinventario="+cInventario+
	           "&xidinventario="+cIdInventario+
	           "&xestinvent="+cEstadoInventario+
	           "&xtipoinventario="+cTipoInventario+
	           "&xpedido="+cIdPedido+
	           "&xcomprobante="+cIdComprobante+
	           "&xopeajuste="+xOpeAjuste+
	           "&xcosto="+cCosto+
	           "&xprecio="+cPrecio+
	           "&xpvd="+cPVD+
	           "&xpvdd="+cPVDD+
	           "&xpvc="+cPVC+
	           "&xpvcd="+cPVCD+
	           "&esserie="+esSerie+
	           "&lotevence="+LoteVence+
	           "&serievence="+cSeriesVence+
                   "&xajuste="+cAjusteExistencias;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequest.send("numerosdeserie="+cSeries+"&xobservacion='"+xObs+"'");
    xres           = xrequest.responseText;

    aInventario    = xres.split("~");

    if (!parseInt(aInventario[0]) )
    {
	alert(po_servidorocupado+' \n\n Ajuste -> Agrega Stock > '+xres);
	return mostrarOperacion(cInventario);
    }

    if( cInventario=="Inventario" && cIdInventario == '0' )
	actualizaComboInventarios(aInventario);


    cIdInventario     = aInventario[1];
    cIdPedido         = aInventario[2];
    cIdComprobante    = aInventario[3];
    cEstadoInventario = ( esInventario )? 'Pendiente':'none';

    //Alta Rapida 
    if(esAltaRapida)
	return agregaStockAltaRapida( esAltaRapidaArr,esAltaRapidaTotal,esAltaRapidaResto );

    //Carga cambios
    volverStock();
    BuscarAlmacen();
    BuscarPorArticuloStock(cIdArticulo);
    actualizaComboAjusteOperacion('Entrada',xOpeAjuste);
    //Actualizar combo Inventarios??

}


function actualizaComboInventarios(aInventario){

    var lista  = id("filtroInventariosPopup");
    var n      = lista.itemCount;
    var xitem  = document.createElement("menuitem");
    var xmeses = new Array ("enero","febrero","marzo","abril","mayo","Junio","Julio",
			    "agosto","septiembre","octubre","noviembre","diciembre");
    var xhoy   = new Date();
    var xfecha = xmeses[xhoy.getMonth()]+' '+xhoy.getFullYear();

    xitem.setAttribute("label",cTipoInventario+" "+xfecha+" - Pendiente");
    xitem.setAttribute("id","inventario_Pendiente");
    xitem.setAttribute("value",aInventario[1]+':'+aInventario[2]+':'+aInventario[3]);
    lista.appendChild( xitem );		



    id("invent").setAttribute("label","Inventario "+cTipoInventario+" - Pendiente")
    id("filtroInventarios").value = aInventario[1]+':'+aInventario[2]+':'+aInventario[3];
}

function actualizaComboAjusteOperacion(xop,xval){

    var busca   = trim(xval);

    if(busca.length == 0) return;

    var xitem   = false;
    var xtexto  = false;
    var lista   = id("xAjusteOperacion"+xop);
    var ltpopup = id("xAjustepopup"+xop);
    var n       = lista.itemCount;

    if(n==0) return; 

    //Busca
    for (var i = 0; i < n; i++) 
    {
        xtexto = lista.getItemAtIndex(i);

	if(xtexto.getAttribute('label') == xval) 
	    return;//Termina Brutal
    }

    //Crea item
    xitem = document.createElement("menuitem");
    xitem.setAttribute("label",xval);
    ltpopup.appendChild( xitem );		
}

function getAgregaSeries(){

    var url  = "../compras/selcomprar.php?id="+cIdProducto+
               "&modo=visualizarserieAgregaInventario"+
               "&trasAlta=1"+
               "&u="+cAjusteExistencias;

    id("webkardex").setAttribute("src",url);  
    id("formAjustesExistencias").setAttribute("collapsed",true);
    id("boxkardex").setAttribute("collapsed",false);  

}

function setSeccionSeries(xseries,xvence){

    if(!esSerie) return ;
    if(!(xseries!='')) return volverStock();

    cSeries      = xseries;
    cSeriesVence = xvence;
    agregaStock(true);
}

function volverStock(){

    //Inventario btn continuar
    var xinvent    = (id("inventario_Pendiente"))? true:false;
    var vinvent    = (xinvent)? id("inventario_Pendiente").value:'';
    var cinvent    = id("filtroInventarios").value;
    var xcontinuar = (  cInventario != 'Ajuste' && vinvent == cinvent )? false:true; 
    var xbtnvolver = (	cInventario != 'Ajuste')? " Volver Inventarios":" Volver Ajustes";
    var cbtnvolver = (	cInventario != 'Ajuste')? "mostrarOperacion('invent')":"mostrarOperacion('ajust')";
 
    id("formAjustesExistencias").setAttribute("collapsed",true);
    id("btnVolver").setAttribute("collapsed",false);
    id("btnFinalizarInventario").setAttribute("collapsed",xcontinuar);
    id("listadoAlmacen").setAttribute("collapsed",false);  
    id("resumenAlmacen").setAttribute("collapsed",false);
    id("boxkardex").setAttribute("collapsed",true);  
    id("webkardex").setAttribute("src","about:blank");  
    id("btnVolver").setAttribute("label",xbtnvolver);
    id("btnVolver").setAttribute("oncommand",cbtnvolver);
    id("NombreBusqueda").focus();
}

function verKardexInventario(){

    id("webkardex").setAttribute("src","about:blank");  
    id("listadoMovimiento").setAttribute("collapsed","false");  
    id("resumenMovimiento").setAttribute("collapsed","false");  
    id("busquedaMovimiento").setAttribute("collapsed","false");  
    id("boxkardex").setAttribute("collapsed","true");  
}

function totalAjusteExistencias(xval){

    if( trim(xval) == '')
     {
	 id("xExistenciasFisico").value = 0;
	 cAjusteExistencias             = cExistencias;

	 id("xAjuste").setAttribute("value",(-1)*cAjusteExistencias+' '+cUnidad);
	 return;
     }

    var xAjusteModo = false;

    xval               = parseFloat(xval);
    cExistencias       = parseFloat(cExistencias);
    xAjusteModo        = ( cExistencias > xval  )? 'menos':xAjusteModo; 
    xAjusteModo        = ( cExistencias < xval  )? 'mas':xAjusteModo; 
    xAjusteModo        = ( cExistencias == xval )? 'igual':xAjusteModo; 
    xAjusteModo        = ( xval == 0 && cExistencias > 0 )? 'cero':xAjusteModo; 
    cAjusteModo        = xAjusteModo;
    cAjusteExistencias = cExistencias - xval;

    id("xAjuste").setAttribute("value",(-1)*cAjusteExistencias+' '+cUnidad);
 
}

function setCostoPrecios(xval,xdato){
    
    xdato = parseFloat(xdato);
    //xdato = xdato.toFixed(2);

    switch (xval) {

    case 'costo':
	cPrecio = parseFloat(xdato*(parseFloat(cImpuesto)+100)/100).toFixed(2);
	cCosto  = xdato;

	var xPVD = (cPrecio>cPVD)? parseFloat(cPrecio*(parseFloat(cUtilidad)+100)/100):cPVD;
	cPVDD    = (xPVD != cPVD)? cPrecio:cPVDD;
	cPVC     = (xPVD != cPVD)? xPVD:cPVC;
	cPVCD    = (xPVD != cPVD)? cPrecio:cPVCD;
	cPVD     = xPVD;
	break;

    case 'precio':
	cPrecio  = xdato;
	cCosto   = (xdato*100/(parseFloat(cImpuesto)+100)).toFixed(2);
	var xPVD = (cPrecio>cPVD)? parseFloat(cPrecio*(parseFloat(cUtilidad)+100)/100):cPVD;
	cPVDD    = (xPVD != cPVD)? cPrecio:cPVDD;
	cPVC     = (xPVD != cPVD)? xPVD:cPVC;
	cPVCD    = (xPVD != cPVD)? cPrecio:cPVCD;
	cPVD     = xPVD;
	break;

    case 'pvd':
	cPVD    = ( xdato >= cPrecio)? xdato:cPrecio;
	cPVDD   = ( cPVDD >= cPrecio && cPVDD <= cPVD )? cPVDD:cPVD;
	break;

    case 'pvdd':
	cPVDD  = (xdato <= cPVD && xdato >= cPrecio )? xdato:cPVD;
	break;

    case 'pvc':
	cPVC    = (xdato >= cPrecio)? xdato:cPrecio;
	cPVCD   = (cPVCD <= cPVC && cPVCD >= cPrecio)? cPVCD:cPVC;
	break;

    case 'pvcd':
	cPVCD  = (xdato <= cPVC && xdato >= cPrecio)? xdato:cPVC;
	break;
    }

    id("xValorCompra").value  = formatDinero(cCosto);  
    id("xPrecioCompra").value = formatDinero(cPrecio);  
    id("xPVD").value          = formatDinero(cPVD);  
    id("xPVDD").value         = formatDinero(cPVDD);  
    id("xPVC").value          = formatDinero(cPVC); 
    id("xPVCD").value         = formatDinero(cPVCD);
}

function exportarInventario(xval){

    var radioInvent = id("rdioInvetarioAjuste").value;
    var filtrolocal      = id("FiltroMovimientoLocal").value;
    var filtrooperacion  = id("filtroOperacion").value;
    var filtromovimiento = id("filtroMovimiento").value;
    var desde            = id("FechaBuscaDesde").value;
    var hasta            = id("FechaBuscaHasta").value;
    var nombre           = id("NombreBusqueda").value;
    var codigo           = id("CodigoBusqueda").value;
    var marca            = id("idmarca").value;
    var familia          = id("idfamilia").value;
    var almacen          = id("FiltroMovimientoLocal").label;
    var inventario       = (cInventario == 'Inventario')? id("invent").label:cInventario+' '+desde+' a '+hasta;

    var data  = "&desde=" + escape(desde)
            + "&hasta=" + escape(hasta)
	    + "&familia=" + escape(familia)
            + "&xinventario=" + escape(cInventario)
            + "&xidinventario=" + escape(cIdInventario)
            + "&marca=" + escape(marca)
            + "&xnombre=" + escape(nombre)
            + "&xcodigo=" + escape(codigo)
            + "&xope=" + escape(filtrooperacion)
            + "&xidope=" + escape(cIdInventario)
            + "&xmov=" + escape(filtromovimiento)
            + "&xlocal=" + escape(filtrolocal)
            + "&xtitulo=" + escape(inventario.toUpperCase())
            + "&alma=" + escape(almacen)
            + "&idinv="+ escape(cIdInventario);

    switch(xval){
    case 'pdf':
	var url = "../fpdf/imprimir_inventario.php?" + data;
	location.href=url;
	break;
    case 'cvs':
	var url = "../generadorlistados/exportarlistados.php?"+data+
                  "&modo="+"inventaAjuste";
	document.location=url;
	break;
    }
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Movimiento":
	vMovimiento       = xchecked;
	break;
    case "Familia":
	vFamilia          = xchecked;
	break;
    case "Marca" : 
	vMarca            = xchecked;
	break;
    case "Detalle":
	vDetalle          = xchecked;
	break;
    case "Usuario":
	vUsuario          = xchecked;
	break;
    case "Observacion" :
	vObservaciones    = xchecked;
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
    id("itempag_"+cInicioPagina).setAttribute('checked',false);
    id("itempag_"+inipagina).setAttribute('checked',true);

    cInicioPagina    = inipagina;
    id("listaPaginas").setAttribute('label','Pag. '+rangolist);

    BuscarMovimiento();
}

function obtenerNumFilas(xcadena){
   
    var url = "../kardex/selkardex.php?modo=countMovimientosInventario"+xcadena;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false); 
    xrequest.send(null);
    
    cTotalFilas     = (!isNaN(xrequest.responseText))? xrequest.responseText:0;
    cCadenaBusqueda = xcadena;
}
