var id = function(name) { return document.getElementById(name); }

var cIdPromocion   = 0;
var cPromocion     = "";
var cIdCatCliente  = 0;
var cEstado        = "";
var cFechaInicio   = "";
var cFechaFin      = "";
var cModalidad     = "";
var cMontoActual   = 0;
var cTipo          = "";
var cDispLocal     = "";
var cDescuento     = 0;
var cBono          = 0;
var cProducto1     = "";
var cProducto2     = "";
var cPrioridad     = 0;
var cTipoVenta     = "";
var ilinealistapromocion = 0;


var gEstado     = "";
var gIdPromocionCliente  = 0;

var gCategoria     = "";
var gDescripcion   = "";
var gEstado        = "";
var gMontoDesde    = 0;
var gMontoHasta    = 0;
var gCantidadDesde = 0;
var gCantidadHasta = 0;
var gMotivo        = "";
var gIdLocal       = 0;
var gLocal         = "";
var gHistorialPeriodo = 0;
var gHistorialVenta   = '';
var gHVPeriodo     = '';
var ilinealistapromocioncliente = 0;

//Opciones de búsqueda avanzada promociones
var vTipo      = true;
var vTipoVenta = true;
var vModalidad = true;
var vLocal     = true;
var vUsuario   = true;
var vCategoriaCliente = true;
var vFechaRegistro    = true;
var vDisponibilidad   = true;

//Opciones de búsqueda avanzada promocion clientes



var zLocal            = true;
var zUsuario          = true;
var zDescripcion      = true;
var zMotivoPromocion  = true;
var zFechaRegistro    = true;
var zHistorialVenta   = true;


function SeleccionarCondicionPromocion(xvalue){
    var montoactual = true;
    var catcliente  = true;

    switch(xvalue){
    case 'MontoCompra':
	montoactual = false;
	break;
    case 'HistorialCompra':
	catcliente  = false;
	break;
    }

    id("rowMontoActualPromocion").setAttribute('collapsed',montoactual);
    id("rowCategoriaCliente").setAttribute('collapsed',catcliente);
    id("rowPrioridadPromocion").setAttribute('collapsed',catcliente);
}

function SeleccionarTipoPromocion(xvalue){
    var producto    = true;
    var descuento   = true;
    var bono        = true;

    switch(xvalue){
    case 'Descuento':
	descuento   = false;
	break;
    case 'Producto':
	producto    = false;
	break;
    case 'Bono':
	bono        = false;
	break
    }

    id("descProductoOferta").setAttribute('collapsed',producto);
    id("rowProductoPromocion1").setAttribute('collapsed',producto);
    id("rowProductoPromocion2").setAttribute('collapsed',producto);
    id("rowDescuentoPromocion").setAttribute('collapsed',descuento);
    id("rowBonoPromocion").setAttribute('collapsed',bono);
}

function mostrarFormPromocion(xvalue){
    switch(xvalue){
    case 'Nuevo':
	cleanFormPromocion();
	cIdPromocion = 0;
	cEstado      = "Nuevo";
	habilitarFormPromocion();
	mostrarEstadoPromocion();
	var xlist   = id("listadoPromocion");
	var rowlist = xlist.getRowCount(); 
	var xtitulo = "Nueva Promoción";
	
	for (var i = 0; i < rowlist; i++) { 
            kid = id("linealistapromocion_"+i);					
            if (kid) kid.removeAttribute('selected');
	}

	break;
    case 'Editar':
	var xtitulo = "Editando Promoción";
	cleanFormPromocion();
	id("nombrePromocion").value = trim(cPromocion);
	id("FiltroCondicionPromocion").value = cModalidad;
	id("FiltroTipoPromocion").value = cTipo;
	id("InicioPeriodoPromocion").value = cFechaInicio;
	id("FinPeriodoPromocion").value = cFechaFin;
	id("MontoActualPromocion").value = cMontoActual;
	id("FiltroCategoriaCliente").value = cIdCatCliente;
	id("ProductoPromocion1").value = trim(cProducto1);
	id("ProductoPromocion2").value = trim(cProducto2);
	id("DescuentoPromcion").value = cDescuento;
	id("BonoPromocion").value = cBono;
	id("FiltroDisponibilidadLocal").value = (cDispLocal == '0')? cDispLocal:'Actual';
	id("FiltroPrioridadPromocion").value = cPrioridad;
	id("FiltroEstado").value = cEstado;
	id("FiltroTipoVenta").value = cTipoVenta;

	habilitarFormPromocion();
	SeleccionarCondicionPromocion(cModalidad);
	SeleccionarTipoPromocion(cTipo);
	mostrarEstadoPromocion();

	break;
    }

    id("wtitleFormPromociones").label=xtitulo;//setAttribute('label',xtitulo);
    id("vboxFormPromocion").setAttribute('collapsed',false);
    id("resumenPromociones").setAttribute("collapsed",true);
}

function mostrarEstadoPromocion(){
    var xborrador   = true;
    var xejecucion  = true;
    var xsuspendido = true;
    var xcancelado  = true;
    var xfinalizado = true;

    switch(cEstado){

    case 'Nuevo':
	xborrador  = false;
	xejecucion = false;
	break;
    case 'Borrador':
	xborrador  = false;
	xejecucion = false;
	xcancelado = false;
	break;
    case 'Ejecucion':
	xsuspendido = false;
	xfinalizado = false;
	break;
    case 'Suspendido':
	xejecucion  = false;
	xfinalizado = false;
	break;
    case 'Cancelado':
	xborrador = false;
	break;
    }

    id("itmEstadoBorrador").setAttribute('collapsed',xborrador);
    id("itmEstadoEjecucion").setAttribute('collapsed',xejecucion);
    id("itmEstadoSuspendido").setAttribute('collapsed',xsuspendido);
    id("itmEstadoCancelado").setAttribute('collapsed',xcancelado);
    id("itmEstadoFinalizado").setAttribute('collapsed',xfinalizado);
}

function cleanFormPromocion(){

    var f = new Date();
    var fecha = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();

    id("nombrePromocion").value = "";
    id("FiltroCondicionPromocion").value = "MontoCompra";
    id("FiltroTipoPromocion").value = "Descuento";
    id("InicioPeriodoPromocion").value = fecha;
    id("FinPeriodoPromocion").value = fecha;
    id("MontoActualPromocion").value = "0";
    id("FiltroCategoriaCliente").value = "0";
    id("ProductoPromocion1").value = "";
    id("ProductoPromocion2").value = "";
    id("DescuentoPromcion").value = "0";
    id("BonoPromocion").value = "0";
    id("FiltroDisponibilidadLocal").value = "Actual";
    id("FiltroEstado").value = "Borrador";
    id("FiltroTipoVenta").value = "VD";

    SeleccionarCondicionPromocion('MontoCompra');
    SeleccionarTipoPromocion('Descuento');

 
}

function habilitarFormPromocion(){
    var xval = (cEstado != 'Borrador' && cIdPromocion != 0)? true:false;

    id("nombrePromocion").setAttribute('disabled',xval);
    id("FiltroCondicionPromocion").setAttribute('disabled',xval);
    id("FiltroTipoPromocion").setAttribute('disabled',xval);
    id("InicioPeriodoPromocion").setAttribute('disabled',xval);
    id("FinPeriodoPromocion").setAttribute('disabled',xval);
    id("MontoActualPromocion").setAttribute('disabled',xval);
    id("FiltroCategoriaCliente").setAttribute('disabled',xval);
    id("ProductoPromocion1").setAttribute('disabled',xval);
    id("ProductoPromocion2").setAttribute('disabled',xval);
    id("DescuentoPromcion").setAttribute('disabled',xval);
    id("BonoPromocion").setAttribute('disabled',xval);
    id("FiltroDisponibilidadLocal").setAttribute('disabled',xval);
    id("FiltroPrioridadPromocion").setAttribute('disabled',xval);
    id("FiltroTipoVenta").setAttribute('disabled',xval);

    if(!xval){
	id("nombrePromocion").removeAttribute('disabled');
	id("FiltroCondicionPromocion").removeAttribute('disabled');
	id("FiltroTipoPromocion").removeAttribute('disabled');
	id("InicioPeriodoPromocion").removeAttribute('disabled');
	id("FinPeriodoPromocion").removeAttribute('disabled');
	id("MontoActualPromocion").removeAttribute('disabled');
	id("FiltroCategoriaCliente").removeAttribute('disabled');
	id("ProductoPromocion1").removeAttribute('disabled');
	id("ProductoPromocion2").removeAttribute('disabled');
	id("DescuentoPromcion").removeAttribute('disabled');
	id("BonoPromocion").removeAttribute('disabled');
	id("FiltroDisponibilidadLocal").removeAttribute('disabled');
	id("FiltroPrioridadPromocion").removeAttribute('disabled');
	id("FiltroTipoVenta").removeAttribute('disabled');
    }
}

function guardaPromocion(){

    if(cEstado == 'Finalizado')
	return alert("gPOS: \n\n Las promociones en estado -"+cEstado+
		     "- No se pueden modificar");

    var data          = "";
    var Promocion     = id("nombrePromocion").value;
    var Modalidad     = id("FiltroCondicionPromocion").value;
    var Tipo          = id("FiltroTipoPromocion").value;
    var InicioPeriodo = id("InicioPeriodoPromocion").value;
    var FinPeriodo    = id("FinPeriodoPromocion").value;
    var MontoActual   = id("MontoActualPromocion").value;
    var CatCliente    = id("FiltroCategoriaCliente").value;
    var Producto1     = id("ProductoPromocion1").value;
    var Producto2     = id("ProductoPromocion2").value;
    var Descuento     = id("DescuentoPromcion").value;
    var Bono          = id("BonoPromocion").value;
    var DispLocal     = id("FiltroDisponibilidadLocal").value;
    var Prioridad     = id("FiltroPrioridadPromocion").value;
    var Estado        = id("FiltroEstado").value;
    var TipoVenta     = id("FiltroTipoVenta").value;
    var IdPromocion   = cIdPromocion;
    var nombreLocal   = (DispLocal == 'Actual')? id("FiltroLocal").label:"Todos";

    var CategoriaCliente = (CatCliente != 0)? id("FiltroCategoriaCliente").label:' ';
    var ModalidadPromo   = id("FiltroCondicionPromocion").label;

    var esNuevoLista = (cIdPromocion == 0)? true:false;

    DispLocal = (DispLocal == 'Actual')? cIdLocal:0;

    if(Descuento > 100){
	id("DescuentoPromcion").value = 100;
	Descuento = 100;
    }

    var url = "modpromociones.php?modo=GuardaPromocion";
    data = data + "&xpromo="+Promocion;
    data = data + "&xmod="+Modalidad;
    data = data + "&xtipo="+Tipo;
    data = data + "&xinicio="+InicioPeriodo;
    data = data + "&xfin="+FinPeriodo;
    data = data + "&xmonto="+MontoActual;
    data = data + "&xcatclient="+CatCliente;
    data = data + "&xprod1="+trim(Producto1);
    data = data + "&xprod2="+trim(Producto2);
    data = data + "&xdesc="+Descuento;
    data = data + "&xbono="+Bono;
    data = data + "&xdisplocal="+DispLocal;
    data = data + "&xidpromo="+IdPromocion;
    data = data + "&xprioridad="+Prioridad;
    data = data + "&xestado="+Estado;
    data = data + "&xtipoventa="+TipoVenta;

    if(trim(Promocion) == ''){
	cleanFormPromocion();
	return alert("gPOS: \n\n Ingrese Nombre de la Promoción");
    }

    if(Estado != 'Borrador')
	if(!confirm('gPOS:\n     Promoción: '+Promocion+'\n\n     Aplicar el nuevo estado -'+
		    Estado+'-, ¿estas seguro?')) return RevisarPromocionSeleccionada();

    if(Estado == 'Ejecucion'){
	var msj = validarDatosPromocion(Estado,Modalidad,MontoActual,CatCliente,Tipo,
					Descuento,Bono,Producto1,InicioPeriodo,FinPeriodo);
	if(msj){
	    alert("gPOS:\n   Promoción  -"+Promocion+'-\n\n   '+msj);
	    RevisarPromocionSeleccionada();
	    return;
	}
    }

    var obj = new XMLHttpRequest();
    obj.open("POST",url,false); 
    obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');

    try{
	obj.send(data);
	res = obj.responseText;
    }catch(e){
	res=false;
    }

    if (!obj.responseText)
        return alert(po_servidorocupado);

    if(parseInt(res) == 0)
	return alert("gPOS: \n\n El Código de Barras no existe");

    cIdPromocion = parseInt(res);

    if(esNuevoLista){
	BuscarPromocion();
	esNuevoLista = false;
	return;
    }

    ActualizarListadoPromocion(Promocion,ModalidadPromo,Tipo,InicioPeriodo,FinPeriodo,
			       MontoActual,CategoriaCliente,Producto1,Producto2,
			       Descuento,Bono,DispLocal,Estado,TipoVenta,nombreLocal);

    cEstado = Estado;
    habilitarFormPromocion();
    mostrarEstadoPromocion();
}

function validarDatosPromocion(Estado,Modalidad,MontoActual,CatCliente,Tipo,
			       Descuento,Bono,Producto1,InicioPeriodo,FinPeriodo){

    var fechainicio  = InicioPeriodo.replace(/-/g,',');
    var fechafin     = FinPeriodo.replace(/-/g,',');
    var fechacompare = comparaFechas(fechainicio,fechafin);
    var msj          = (fechacompare >= 0)?"La fecha Fin Periodo debe ser mayor a Inicio Periodo":"";

    switch(Modalidad){
    case 'MontoCompra' : if( MontoActual == 0 ) msj = "Ingrese Monto ";break;
    case 'NumeroCompra': if( CatCliente  == 0 ) msj = "Seleccione Categoría Cliente";break;	
    }
    switch(Tipo){
    case 'Descuento'   : if( Descuento == 0 ) msj = "Ingrese Descuento"; break;
    case 'Bono'        : if( Bono == 0      ) msj = "Ingrese Bono"; break;
    case 'Producto'    : if( Producto1 == 0 || Producto1 == 0 ) 
	                                      msj = "Ingrese Código Barras del Producto";break;
    }
    msj = (msj !='')? Modalidad+': '+msj:msj;
    return msj;
}

function ActualizarListadoPromocion(Promocion,ModalidadPromo,Tipo,InicioPeriodo,FinPeriodo,
				    MontoActual,CategoriaCliente,Producto1,Producto2,
				    Descuento,Bono,DispLocal,Estado,TipoVenta,nombreLocal){

    var idx         = cIdPromocion;
    var FechaInicio = InicioPeriodo.split('-');
    InicioPeriodo   = FechaInicio[2]+'/'+FechaInicio[1]+'/'+FechaInicio[0];
    var FechaFin    = FinPeriodo.split('-');
    FinPeriodo      = FechaFin[2]+'/'+FechaFin[1]+'/'+FechaFin[0];
    var Producto    = (Producto1 == 0)? '':Producto1+' ';
    Producto        = (Producto2 == 0)? Producto:Producto+Producto2;

    var pTipoVenta  = (TipoVenta == 'VD')? 'B2C':'B2B';
    var pMontoActual= (MontoActual == 0)?'':cMoneda[1]['S']+" "+formatDinero(MontoActual);

    var pDescuento  = (Descuento == 0)? '':formatDinero(Descuento);
    var pBono       = (Bono == 0)? '':cMoneda[1]['S']+" "+formatDinero(Bono);

    id("p_promocion_"+idx).setAttribute("label",Promocion);	   
    id("p_promocion_"+idx).setAttribute("value",cIdPromocion);	   
    id("p_categoriacliente_"+idx).setAttribute("label",CategoriaCliente); 
    id("p_estado_"+idx).setAttribute("label",Estado);	   
    id("p_fechainicio_"+idx).setAttribute("label",InicioPeriodo);	   
    id("p_fechafin_"+idx).setAttribute("label",FinPeriodo);	   
    id("p_modalidad_"+idx).setAttribute("label",ModalidadPromo);	   
    id("p_montoactual_"+idx).setAttribute("label",pMontoActual);	   
    id("p_montoactual_"+idx).setAttribute("value",MontoActual);	   
    id("p_tipo_"+idx).setAttribute('label',Tipo);		   
    id("p_local_"+idx).setAttribute("value",DispLocal);	   
    id("p_local_"+idx).setAttribute("label",nombreLocal);	   
    id("p_descuento_"+idx).setAttribute("label",pDescuento);	   
    id("p_bono_"+idx).setAttribute("label",pBono);		   
    id("p_descuento_"+idx).setAttribute("value",Descuento);	   
    id("p_bono_"+idx).setAttribute("value",Bono);		   
    id("p_producto1_"+idx).setAttribute("value",Producto1);	   
    id("p_producto2_"+idx).setAttribute("value",Producto2);        
    id("p_producto_"+idx).setAttribute("label",Producto);        
    id("p_tipoventa_"+idx).setAttribute("label",pTipoVenta);        
    id("p_tipoventa_"+idx).setAttribute("value",TipoVenta);        

}

function BuscarPromocion(){

    VaciarBusquedaPromocion();

    var filtrolocal = id("FiltroLocal").value;
    var desde       = id("FechaBuscaPromocion").value;
    var hasta       = id("FechaBuscaPromocionHasta").value;
    var promo       = id("NombreBusqueda").value;
    var estado      = id("idEstadoPromocion").value;
    var tipo        = id("idTipoPromocion").value;
    var montocompra = (id("modoPromocionMontoCompra").checked)? "MontoCompra":"Todos";
    var historialcompra = (id("modoPromocionHistorialCompra").checked)? "HistorialCompra":"Todos";
    var tipoventa   = id("idTipoVenta").value;

    RawBuscarPromocion(filtrolocal,desde,hasta,promo,estado,tipo,montocompra,
		       historialcompra,tipoventa,AddLineaPromocion);

    cIdLocal = filtrolocal;
    synccomboCategoriaCliente();

    id("vboxFormPromocion").setAttribute('collapsed',true);
    id("resumenPromociones").setAttribute("collapsed",false);
}

function VaciarBusquedaPromocion(){
    var lista = id("listadoPromocion");

    for (var i = 0; i < ilinealistapromocion; i++) { 
        kid = id("linealistapromocion_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilinealistapromocion = 0;
}

function RawBuscarPromocion(filtrolocal,desde,hasta,promo,estado,tipo,montocompra,
			    historialcompra,tipoventa,FuncionProcesaLineaPromocion){

    var url = "modpromociones.php?modo=ObtenerPromociones"
	+ "&xdesde=" + escape(desde)
        + "&xhasta=" + escape(hasta)
        + "&xpromo=" + escape(promo)
        + "&xestado=" + escape(estado)
        + "&xtipo=" + escape(tipo)
        + "&xtipoventa=" + escape(tipoventa)
	+ "&xmontocompra=" + escape(montocompra)
	+ "&xhistorialcompra=" + escape(historialcompra)
        + "&xlocal=" + escape(filtrolocal);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false); 
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var Promocion,Condicion,Tipo,InicioPeriodo,FinPeriodo,MontoActual,CatCliente,Producto1,Producto2,Descuento,Bono,DispLocal,IdPromocion,Estado,IdUsuario,IdLocal,Prioridad,TipoVenta

    var node,t,i;
    var totalPromocion  = 0;
    var totalBorrador   = 0;
    var totalEjecucion  = 0;
    var totalConfirmado = 0;
    var totalCancelado  = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    //var item = 0;
    var tC   = item;
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    //item++;
            t = 0;

	    Local           = node.childNodes[t++].firstChild.nodeValue;
	    Usuario         = node.childNodes[t++].firstChild.nodeValue;
	    IdPromocion     = node.childNodes[t++].firstChild.nodeValue;
	    Promocion       = node.childNodes[t++].firstChild.nodeValue;
	    FechaRegistro   = node.childNodes[t++].firstChild.nodeValue;
	    FechaInicio     = node.childNodes[t++].firstChild.nodeValue;
	    FechaFin        = node.childNodes[t++].firstChild.nodeValue;
	    Estado          = node.childNodes[t++].firstChild.nodeValue;
	    Modalidad       = node.childNodes[t++].firstChild.nodeValue;
	    MontoActual     = node.childNodes[t++].firstChild.nodeValue;
	    Tipo            = node.childNodes[t++].firstChild.nodeValue;
	    DispLocal       = node.childNodes[t++].firstChild.nodeValue;
	    Producto1       = node.childNodes[t++].firstChild.nodeValue;
	    Producto2       = node.childNodes[t++].firstChild.nodeValue;
	    Descuento       = node.childNodes[t++].firstChild.nodeValue;
	    Bono            = node.childNodes[t++].firstChild.nodeValue;
	    Prioridad       = node.childNodes[t++].firstChild.nodeValue;
	    CategoriaCliente= node.childNodes[t++].firstChild.nodeValue;
	    IdPromocionCliente = node.childNodes[t++].firstChild.nodeValue;
	    TipoVenta       = node.childNodes[t++].firstChild.nodeValue;

	    if ( Estado == 'Borrador'  ) totalBorrador++; 
 	    if ( Estado == 'Ejecucion' ) totalEjecucion++; 
	    if ( Estado == 'Finalizado') totalConfirmado++;
	    if ( Estado == 'Cancelado') totalCancelado++;
	    totalPromocion++;

            FuncionProcesaLineaPromocion(item,Local,Usuario,IdPromocion,Promocion,
					 FechaRegistro,FechaInicio,FechaFin,Estado,
					 Modalidad,MontoActual,Tipo,DispLocal,Producto1,
					 Producto2,Descuento,Bono,CategoriaCliente,
					 IdPromocionCliente,totalPromocion,Prioridad,
					 TipoVenta);
	    item--;
        }
    }

    var srt = (totalPromocion > 1 )? 'es':'';
    id("TotalPromocion").value  = totalPromocion+' promocion'+srt;
    id("TotalBorrador").value   = totalBorrador;
    id("TotalEjecucion").value  = totalEjecucion;
    id("TotalFinalizado").value = totalConfirmado;
    id("TotalCancelado").value  = totalCancelado;
}

function AddLineaPromocion(item,Local,Usuario,IdPromocion,Promocion,
			   FechaRegistro,FechaInicio,FechaFin,Estado,
			   Modalidad,MontoActual,Tipo,DispLocal,Producto1,
			   Producto2,Descuento,Bono,CategoriaCliente,
			   IdPromocionCliente,totalPromocion,Prioridad,
			   TipoVenta){

    var lista    = id("listadoPromocion");
    var xnumitem,xitem,xLocal,xUsuario,xIdPromocion,xPromocion,xFechaRegistro,xFechaInicio,xFechaFin,xEstado,xModalidad,xMontoActual,xTipo,xDispLocal,xProducto1,xProducto2,xDescuento,xBono,xCategoriaCliente,xIdPromocionCliente,xPrioridad,xTipoVenta;
    var pFechaRegistro,vFechaInicio,vFechaFin,xclass,vProducto,pTipoVenta,pMontoActual,pDescuento,pBono;
    var iFechaInicio,iFechaFin,zFechaInicio,zFechaFin,yFechaInicio,yFechaFin,aFechaInicio,aFechaFin,oFechaInicio,oFechaFin,oHoraInicio,oHoraFin,iFechaRegistro,aFechaRegistro,oFechaRegistro,oHoraRegistro;
    var pModalidad = (Modalidad == 'MontoCompra')? "Monto Compra":"Historial Compra";

    //Fecha Registro
    FechaR = FechaRegistro.split('~');
    pFechaRegistro = FechaR[0];
    iFechaRegistro = FechaR[1];
    aFechaRegistro = iFechaRegistro.split(' ');
    oFechaRegistro = aFechaRegistro[0];
    oHoraRegistro  = aFechaRegistro[1];

    //Fecha Inicio
    FechaI = FechaInicio.split('~');
    vFechaInicio   = FechaI[0];
    zFechaInicio   = vFechaInicio.split(' ');
    yFechaInicio   = zFechaInicio[0];
    iFechaInicio   = FechaI[1];
    aFechaInicio   = iFechaInicio.split(' ');
    oFechaInicio   = aFechaInicio[0];
    oHoraInicio    = aFechaInicio[1];

    //Fecha Fin
    FechaF = FechaFin.split('~');
    vFechaFin   = FechaF[0];
    zFechaFin   = vFechaFin.split(' ');
    yFechaFin   = zFechaFin[0];
    iFechaFin   = FechaF[1];
    aFechaFin   = iFechaFin.split(' ');
    oFechaFin   = aFechaFin[0];
    oHoraFin    = aFechaFin[1];

    //Producto
    vProducto   = (Producto1 == 0)? '':Producto1+' ';
    vProducto   = (Producto2 == 0)? vProducto:vProducto+Producto2;

    //Tipo Venta
    pTipoVenta  = (TipoVenta == 'VD')? 'B2C':'B2B';

    //Monto Actual
    pMontoActual = (MontoActual == 0)? '':cMoneda[1]['S']+" "+formatDinero(MontoActual);

    //Descuento
    pDescuento   = (Descuento == 0)? '':formatDinero(Descuento);
    
    //Bono
    pBono        = (Bono == 0)? '':cMoneda[1]['S']+" "+formatDinero(Bono);

    xclass       = (item%2)?'imparrow':'parrow';  
    xitem        = document.createElement("listitem");
    xitem.value  = IdPromocion;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistapromocion_"+ilinealistapromocion);
    ilinealistapromocion++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",Local);
    xLocal.setAttribute("value",DispLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("id","p_local_"+IdPromocion);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label",Usuario);
    xUsuario.setAttribute("style","text-align:left");
    xUsuario.setAttribute("collapsed",vUsuario);
    xUsuario.setAttribute("id","p_usuario_"+IdPromocion);

    xPromocion = document.createElement("listcell");
    xPromocion.setAttribute("label",Promocion);
    xPromocion.setAttribute("value",IdPromocion);
    xPromocion.setAttribute("style","text-align:left;font-weight:bold;");
    xPromocion.setAttribute("id","p_promocion_"+IdPromocion);

    xFechaRegistro = document.createElement("listcell");
    xFechaRegistro.setAttribute("label",pFechaRegistro);
    xFechaRegistro.setAttribute("value",iFechaRegistro);
    xFechaRegistro.setAttribute("style","text-align:center;");
    xFechaRegistro.setAttribute("collapsed",vFechaRegistro);
    xFechaRegistro.setAttribute("id","p_fecharegistro_"+IdPromocion);

    xFechaInicio = document.createElement("listcell");
    xFechaInicio.setAttribute("label",yFechaInicio);
    xFechaInicio.setAttribute("value",oFechaInicio);
    xFechaInicio.setAttribute("style","text-align:center;");
    xFechaInicio.setAttribute("id","p_fechainicio_"+IdPromocion);

    xFechaFin = document.createElement("listcell");
    xFechaFin.setAttribute("label",yFechaFin);
    xFechaFin.setAttribute("value",oFechaFin);
    xFechaFin.setAttribute("style","text-align:center;");
    xFechaFin.setAttribute("id","p_fechafin_"+IdPromocion);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label",Estado);
    xEstado.setAttribute("id","p_estado_"+IdPromocion);

    xMontoActual = document.createElement("listcell");
    xMontoActual.setAttribute("label",pMontoActual);
    xMontoActual.setAttribute("value",MontoActual);
    xMontoActual.setAttribute("id","p_montoactual_"+IdPromocion);

    xModalidad = document.createElement("listcell");
    xModalidad.setAttribute("label",pModalidad);
    xModalidad.setAttribute("value",Modalidad);
    xModalidad.setAttribute("collapsed",vModalidad);
    xModalidad.setAttribute("id","p_modalidad_"+IdPromocion);

    xTipo = document.createElement("listcell");
    xTipo.setAttribute("label",Tipo);
    xTipo.setAttribute("collapsed",vTipo);
    xTipo.setAttribute("id","p_tipo_"+IdPromocion);

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label",vProducto);
    xProducto.setAttribute("id","p_producto_"+IdPromocion);

    xProducto1 = document.createElement("listcell");
    xProducto1.setAttribute("value",Producto1);
    xProducto1.setAttribute("collapsed","true");
    xProducto1.setAttribute("id","p_producto1_"+IdPromocion);

    xProducto2 = document.createElement("listcell");
    xProducto2.setAttribute("value",Producto2);
    xProducto2.setAttribute("collapsed","true");
    xProducto2.setAttribute("id","p_producto2_"+IdPromocion);

    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label",pDescuento);
    xDescuento.setAttribute("value",Descuento);
    xDescuento.setAttribute("id","p_descuento_"+IdPromocion);

    xBono = document.createElement("listcell");
    xBono.setAttribute("label",pBono);
    xBono.setAttribute("value",Bono);
    xBono.setAttribute("id","p_bono_"+IdPromocion);

    xCategoriaCliente = document.createElement("listcell");
    xCategoriaCliente.setAttribute("label",CategoriaCliente);
    xCategoriaCliente.setAttribute("value",IdPromocionCliente);
    xCategoriaCliente.setAttribute("collapsed",vCategoriaCliente);
    xCategoriaCliente.setAttribute("id","p_categoriacliente_"+IdPromocion);

    xPrioridad = document.createElement("listcell");
    xPrioridad.setAttribute("value",Prioridad);
    xPrioridad.setAttribute("collapsed","true");
    xPrioridad.setAttribute("id","p_prioridad_"+IdPromocion);

    xTipoVenta = document.createElement("listcell");
    xTipoVenta.setAttribute("label",pTipoVenta);
    xTipoVenta.setAttribute("value",TipoVenta);
    xTipoVenta.setAttribute("collapsed",vTipoVenta);
    xTipoVenta.setAttribute("id","p_tipoventa_"+IdPromocion);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xCategoriaCliente );
    xitem.appendChild( xPromocion );
    xitem.appendChild( xEstado );
    xitem.appendChild( xTipoVenta );
    xitem.appendChild( xFechaRegistro );
    xitem.appendChild( xFechaInicio );
    xitem.appendChild( xFechaFin );
    xitem.appendChild( xModalidad );
    xitem.appendChild( xTipo );
    xitem.appendChild( xMontoActual );
    xitem.appendChild( xProducto );
    xitem.appendChild( xDescuento );
    xitem.appendChild( xBono );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xProducto1 );
    xitem.appendChild( xProducto2 );
    xitem.appendChild( xPrioridad );
    lista.appendChild( xitem );	

}


function RevisarPromocionSeleccionada(){

    var idex       = id("listadoPromocion").selectedItem;
    
    if(!idex) return;

    cIdPromocion     = id("p_promocion_"+idex.value).getAttribute("value");
    cPromocion       = id("p_promocion_"+idex.value).getAttribute("label");
    cIdCatCliente    = id("p_categoriacliente_"+idex.value).getAttribute("value");
    cEstado          = id("p_estado_"+idex.value).getAttribute("label");
    cFechaInicio     = id("p_fechainicio_"+idex.value).getAttribute("value");
    cFechaFin        = id("p_fechafin_"+idex.value).getAttribute("value");
    cModalidad       = id("p_modalidad_"+idex.value).getAttribute("value");
    cMontoActual     = id("p_montoactual_"+idex.value).getAttribute("value");
    cTipo            = id("p_tipo_"+idex.value).getAttribute('label');
    cIdLocal         = id("p_local_"+idex.value).getAttribute("value");
    cDispLocal       = id("p_local_"+idex.value).getAttribute("label");
    cDescuento       = id("p_descuento_"+idex.value).getAttribute("value");
    cBono            = id("p_bono_"+idex.value).getAttribute("value");
    cProducto1       = id("p_producto1_"+idex.value).getAttribute("value");
    cProducto2       = id("p_producto2_"+idex.value).getAttribute("value");        
    cPrioridad       = id("p_prioridad_"+idex.value).getAttribute("value");        
    cTipoVenta       = id("p_tipoventa_"+idex.value).getAttribute("value");        

    mostrarFormPromocion('Editar');

}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.value.replace(" ","_");

    switch(xlabel){
    case "Tipo": 
	vTipo          = xchecked;
	break;
    case "Tipo_Venta":
	vTipoVenta     = xchecked;
	break;
    case "Modalidad":
	vModalidad     = xchecked;
	break;
    case "Local" : 
	vLocal         = xchecked;
	break;
    case "Usuario":
	vUsuario       = xchecked;
	break;
    case "Categoria_Cliente" :
	vCategoriaCliente = xchecked;
	break;
    case "Fecha_Registro":
	vFechaRegistro    = xchecked;
	break;
    case "Disponibilidad" : 
	vDisponibilidad   = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarPromocion();
}

function synccomboCategoriaCliente(){
    var	url = "modpromociones.php?modo=mostrarCategoriaClientes&xidlocal="+cIdLocal;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;

    comboCategoriaCliente(xres);
    
}

function comboCategoriaCliente(xout){
  
    var xfiltro = id("FiltroCategoriaCliente");
    xfiltro.removeAllItems();

    var xpopup = document.createElement("menupopup");
    xpopup.setAttribute("id",'combocategoriacliente');
    xfiltro.appendChild( xpopup );
    xfiltro.setAttribute('label','Seleccione...');
    xfiltro.setAttribute('value','0');

    var xcombo = id("combocategoriacliente");
    var elemento = document.createElement('menuitem');
    elemento.setAttribute('label','Seleccione...');
    elemento.setAttribute('value','0');
    elemento.setAttribute('selected',"true");
    xcombo.appendChild(elemento);

    if(!xout) return;

    var filas = xout.split("~");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(":");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
	xcombo.appendChild(elemento);
    }
   
}

function ocultarFormCambioLocal(){
    cleanFormPromocion();
    mostrarPromocionCliente('Promocion');
    //id("vboxFormPromocion").setAttribute('collapsed',true);
}



/**** CATEGORIA CLIENTE ******/

function mostrarPromocionCliente(xvalue){
    var imgpromocion  = "";
    var btnpromocion  = "";
    var btncatcliente = "";
    var lblpromocion  = "";
    var lblcatcliente = "";
    var titulopcliente= false;
    var vboxpromocion = false;
    var listpromocion = false;
    var listcatcliente= false;

    switch(xvalue){
    case 'Promocion':
	imgpromocion  = "../../img/gpos_promo.png";
	btnpromocion  = "mostrarFormPromocion('Nuevo')";
	lblpromocion  = "Nueva Promoción";
	lblcatcliente = "Categoría Cliente";
	btncatcliente = "mostrarPromocionCliente('CategoriaCliente')";
	titulopcliente= true;
	listcatcliente= true;
	synccomboCategoriaCliente();
	var esresumen = false;
	break;
    case 'CategoriaCliente':
        BuscarPromocionCliente();
	imgpromocion  = "../../img/gpos_volver.png";
	btnpromocion  = "mostrarPromocionCliente('Promocion')";
	btncatcliente = "mostrarFormPromocionCliente('Nuevo')";
	lblpromocion  = "Volver Promoción";
	lblcatcliente = "Nueva Categoría";
	vboxpromocion = true;
	listpromocion = true;
	seleccionarMotitvoPromocion('MontoCompra');
	var esresumen = true;
	break;
    }

    id("vboxPromociones").setAttribute('collapsed',vboxpromocion);
    id("listadoPromocion").setAttribute('collapsed',listpromocion);
    id("vboxFormPromocion").setAttribute('collapsed',true);
    id("listadoPromocionCliente").setAttribute('collapsed',listcatcliente);
    id("boxFormPromocionCliente").setAttribute('collapsed',true);

    id("btnPromocion").setAttribute("oncommand",btnpromocion);
    id("btnPromocion").setAttribute("image",imgpromocion);
    id("btnPromocion").setAttribute("label",lblpromocion);
    id("btnCategoriaCliente").setAttribute("oncommand",btncatcliente);
    id("btnCategoriaCliente").setAttribute("label",lblcatcliente);
    id("hboxPromocionClientes").setAttribute('collapsed',titulopcliente);
    id("resumenPromociones").setAttribute("collapsed",esresumen);
}

function mostrarFormPromocionCliente(xvalue){
    cleanFormPromocionCliente();
    switch(xvalue){
    case 'Nuevo':
	var ztitulo = "Nueva Categoría Cliente";
	gIdPromocionCliente = 0;
	gEstado = 'Nuevo';
	gHistorialPeriodo = 0;
	habilitarFormPromocionCliente();
	mostrarEstadoCategoriaCliente();
	break;
    case 'Editar':
	var ztitulo = "Editando Categoría Cliente";
	id("CategoriaCliente").value      = trim(gCategoria);
	id("DescripcionCategoria").value  = trim(gDescripcion);
	id("MontoCompraDesde").value      = gMontoDesde;
	id("MontoCompraHasta").value      = gMontoHasta;
	id("CantidadCompraDesde").value   = gCantidadDesde;
	id("CantidadCompraHasta").value   = gCantidadHasta;
	id("FiltroMotivoPromocion").value = gMotivo;
	id("FiltroEstadoCategoriaCliente").value = gEstado;
	id("DisponibilidadLocalCat").value = (gIdLocal == '0')? gIdLocal:'Actual';
	id("FiltroHistorialVentaPeriodo").value = (gHistorialPeriodo)? gHistorialPeriodo:'0';
	id("textHistorialVentaPeriodo").value = gHVPeriodo;
	id("textHistorialVentaPeriodo").setAttribute('collapsed',true);

	if(gHVPeriodo > 0){
	    id("textHistorialVentaPeriodo").removeAttribute('readonly');
	    id("textHistorialVentaPeriodo").setAttribute('collapsed',false);
	}

	habilitarFormPromocionCliente();
	seleccionarMotitvoPromocion(gMotivo);
	mostrarEstadoCategoriaCliente();
	break;
    }


    id("wtitleFormPromocionesCliente").setAttribute('label',ztitulo);
    id("boxFormPromocionCliente").setAttribute('collapsed',false);
    id("resumenPromociones").setAttribute("collapsed",true);
}

function seleccionarMotitvoPromocion(xvalue){
    var montocompra = true;
    var numerocompra = true;

    switch(xvalue){
    case 'MontoCompra':
	montocompra = false;
	break;
    case 'NumeroCompra':
	numerocompra = false;
	break;
    case 'Ambos':
	montocompra = false;
	numerocompra = false;
	break;
    }

    id("rowMontoCompraDesde").setAttribute('collapsed',montocompra);
    id("rowMontoCompraHasta").setAttribute('collapsed',montocompra);
    id("descMontoCompra").setAttribute('collapsed',montocompra);
    id("rowCantidadCompraDesde").setAttribute('collapsed',numerocompra);
    id("rowCantidadCompraHasta").setAttribute('collapsed',numerocompra);
    id("descCantidadCompra").setAttribute('collapsed',numerocompra);

}

function cleanFormPromocionCliente(){

    id("CategoriaCliente").value      = "";	  
    id("DescripcionCategoria").value  = ""; 
    id("MontoCompraDesde").value      = 0;	  
    id("MontoCompraHasta").value      = 0;	  
    id("CantidadCompraDesde").value   = 0;  
    id("CantidadCompraHasta").value   = 0;  
    id("FiltroMotivoPromocion").value = 'MontoCompra';
    id("DisponibilidadLocalCat").value= 'Actual';
    id("FiltroEstadoCategoriaCliente").value = 'Borrador';
    id("FiltroHistorialVentaPeriodo").value  = 0;
    id("textHistorialVentaPeriodo").value    = '';

    seleccionarMotitvoPromocion('MontoCompra');

}

function habilitarFormPromocionCliente(){
    var xval = (gEstado != 'Borrador' && gIdPromocionCliente != 0)? true:false;

    id("CategoriaCliente").setAttribute('disabled',xval);
    id("DescripcionCategoria").setAttribute('disabled',xval);
    id("MontoCompraDesde").setAttribute('disabled',xval);
    id("MontoCompraHasta").setAttribute('disabled',xval);
    id("CantidadCompraDesde").setAttribute('disabled',xval);
    id("CantidadCompraHasta").setAttribute('disabled',xval);
    id("FiltroMotivoPromocion").setAttribute('disabled',xval);
    id("DisponibilidadLocalCat").setAttribute('disabled',xval);
    id("FiltroHistorialVentaPeriodo").setAttribute('disabled',xval);
    id("textHistorialVentaPeriodo").setAttribute('disbaled',xval);

    if(!xval){
	id("CategoriaCliente").removeAttribute('disabled');
	id("DescripcionCategoria").removeAttribute('disabled');
	id("MontoCompraDesde").removeAttribute('disabled');
	id("MontoCompraHasta").removeAttribute('disabled');
	id("CantidadCompraDesde").removeAttribute('disabled');
	id("CantidadCompraHasta").removeAttribute('disabled');
	id("FiltroMotivoPromocion").removeAttribute('disabled');
	id("DisponibilidadLocalCat").removeAttribute('disable');
	id("FiltroHistorialVentaPeriodo").removeAttribute('disabled');
	id("textHistorialVentaPeriodo").removeAttribute('disabled');
    }
}


function guardaPromocionCliente(){

    if(gEstado == 'Confirmado')
	return alert("gPOS: \n\n Las promociones cliente en estado -"+gEstado+
		     "- No se pueden modificar");

    var data          = "";
    var Categoria     = id("CategoriaCliente").value;
    var Descripcion   = id("DescripcionCategoria").value;
    var MontoDesde    = id("MontoCompraDesde").value;
    var MontoHasta    = id("MontoCompraHasta").value;
    var CantidadDesde = id("CantidadCompraDesde").value;
    var CantidadHasta = id("CantidadCompraHasta").value;
    var Motivo        = id("FiltroMotivoPromocion").value;
    var IdPromocionCliente   = gIdPromocionCliente;
    var EstadoCategoria = id("FiltroEstadoCategoriaCliente").value;
    var DispLocal     = id("DisponibilidadLocalCat").value;
    var esNuevoListaCat = (gIdPromocionCliente == 0)? true:false;
    var nombreLocal   = (DispLocal == 'Actual')? id("FiltroLocal").label:"Todos";
    var HistorialPeriodo = id("FiltroHistorialVentaPeriodo").value;
    var HistorialVenta = id("FiltroHistorialVentaPeriodo").label;

    DispLocal = (DispLocal == 'Actual')? cIdLocal:0;
    HistorialVenta = (HistorialPeriodo == 0)? '':HistorialVenta;
    var vHistorialPeriodo = HistorialPeriodo;

    if(trim(Categoria) == ''){
	cleanFormPromocionCliente();
	return alert("gPOS: \n\n Ingrese Categoría Cliente");
    }
    
    if(!HistorialPeriodo)
	return;

    if(HistorialPeriodo == 0){
	id("textHistorialVentaPeriodo").value = '';
	id("textHistorialVentaPeriodo").setAttribute('collapsed',true);
    }
    
    if(HistorialPeriodo == 'nuevo'){
	gHistorialVenta = '';
	id("textHistorialVentaPeriodo").value = '';
	id("textHistorialVentaPeriodo").setAttribute('collapsed',false);
	id("textHistorialVentaPeriodo").removeAttribute('readonly');
	id("textHistorialVentaPeriodo").focus();
	return;
    }

    if(HistorialPeriodo != 0 ){
	var xperiodo = HistorialPeriodo.split('~');
	id("textHistorialVentaPeriodo").value = xperiodo[1];
	HistorialPeriodo = xperiodo[0];
	gHistorialVenta  = xperiodo[1];

	id("textHistorialVentaPeriodo").removeAttribute('readonly');
	id("textHistorialVentaPeriodo").setAttribute('collapsed',false);
	if(HistorialPeriodo == 1){
	    id("textHistorialVentaPeriodo").setAttribute('collapsed',true);
	}
    }

    
    if(parseInt(MontoHasta) < parseInt(MontoDesde)){
	MontoHasta = parseInt(MontoDesde)*2;
	id("MontoCompraHasta").value = MontoHasta;
    }

    if(parseInt(CantidadHasta) < parseInt(CantidadDesde)){
	CantidadHasta = parseInt(CantidadDesde)*2;
	id("CantidadCompraHasta").value = CantidadHasta;
    }

    var url = "modpromociones.php?modo=GuardaPromocionCliente";
    data = data + "&xcat="+Categoria;
    data = data + "&xdes="+Descripcion;
    data = data + "&xmd="+MontoDesde;
    data = data + "&xmh="+MontoHasta;
    data = data + "&xcd="+CantidadDesde;
    data = data + "&xch="+CantidadHasta;
    data = data + "&xmot="+Motivo;
    data = data + "&xidpc="+IdPromocionCliente;
    data = data + "&xidlocal="+DispLocal;
    data = data + "&xstdo="+EstadoCategoria;
    data = data + "&xidhp="+HistorialPeriodo;

    if(trim(Categoria) == ''){
	cleanFormPromocionCliente();
	return alert("gPOS: \n\n Ingrese Categoría Cliente");
    }

    if(EstadoCategoria == 'Ejecucion'){
	var msj = "";
	switch(Motivo){
	case 'MontoCompra':
	    if(MontoDesde == 0 || MontoHasta == 0)
		msj = "Ingrese Monto Compra Histórica";
	    break
	case 'NumeroCompra':
	    if(CantidadDesde == 0 || CantidadHasta == 0)
		msj = "Ingrese Número Compras Histórica";
	    break;
	case 'Ambos':
	    if(MontoDesde == 0 || MontoHasta == 0 || CantidadDesde == 0 || CantidadHasta == 0)
		msj = "Ingrese Condiciones de Compra Histórica";
	    break;
	}

	if(HistorialPeriodo == 0)
	    msj = (msj !='')? msj+'\n'+"       Seleccione Periodo Venta":"       Seleccione Periodo Venta";

	if(msj != ''){
	    alert("gPOS: \n   Categoría Cliente \n       "+msj);
	    RevisarPromocionClienteSeleccionada();
	    return;
	}
    }

    if(EstadoCategoria != 'Borrador'){
	if(!confirm('gPOS:\n     Categoría Cliente: '+Categoria+
		    '\n\n     Aplicar el nuevo estado -'+EstadoCategoria+
		    '-, ¿estas seguro?')) return RevisarPromocionClienteSeleccionada();
    }

    var obj = new XMLHttpRequest();
    obj.open("POST",url,false); 
    obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');

    try{
	obj.send(data);
	res = obj.responseText;
    }catch(e){
	res=false;
    }

    if (!obj.responseText)
        return alert(po_servidorocupado);

    if(res == 'existe'){
	alert('gPOS: \n\n    El rango eligido ya existe');
	RevisarPromocionClienteSeleccionada();
	return;
    }

    gIdPromocionCliente = res;

    if(esNuevoListaCat){
	BuscarPromocionCliente();
	esNuevoListaCat = false;
	return;
    }

    if(EstadoCategoria == 'Eliminado'){
	mostrarPromocionCliente('CategoriaCliente');
    }

    ActualizarListadoPromocionCliente(Categoria,Descripcion,MontoDesde,MontoHasta,
				      CantidadDesde,CantidadHasta,Motivo,nombreLocal,
				      EstadoCategoria,HistorialVenta,vHistorialPeriodo);


    gEstado = EstadoCategoria;
    habilitarFormPromocionCliente();
    mostrarEstadoCategoriaCliente();

}

function ActualizarListadoPromocionCliente(Categoria,Descripcion,MontoDesde,MontoHasta,
					   CantidadDesde,CantidadHasta,Motivo,nombreLocal,
					   EstadoCategoria,HistorialVenta,vHistorialPeriodo){
    
    var idx         = gIdPromocionCliente;
    var pMontoDesde,pMontoHasta,pCantidadDesde,pCantidadHasta,vMotivo
    
    vMotivo      = (Motivo == 'MontoCompra')? "Monto Compra":Motivo;
    vMotivo      = (Motivo == 'NumeroCompra')? "Número Compra":vMotivo;

    pMontoDesde  = (MontoDesde == 0)? '':cMoneda[1]['S']+" "+formatDinero(MontoDesde);
    pMontoHasta  = (MontoHasta == 0)? '':cMoneda[1]['S']+" "+formatDinero(MontoHasta);
    pCantidadDesde  = (CantidadDesde == 0)? '':CantidadDesde;
    pCantidadHasta  = (CantidadHasta == 0)? '':CantidadHasta;

    var xperiodo = vHistorialPeriodo.split('~');


    id("c_categoria_"+idx).setAttribute("label",Categoria);    
    //id("c_categoria_"+idx).setAttribute("value",gIdPromocionCliente);    
    id("c_descripcion_"+idx).setAttribute("label",Descripcion);  
    id("c_estado_"+idx).setAttribute("label",EstadoCategoria);       
    id("c_montodesde_"+idx).setAttribute("label",pMontoDesde);   
    id("c_montodesde_"+idx).setAttribute("value",MontoDesde);   
    id("c_montohasta_"+idx).setAttribute("label",pMontoHasta);   
    id("c_montohasta_"+idx).setAttribute("value",MontoHasta);   
    id("c_cantidaddesde_"+idx).setAttribute("label",pCantidadDesde);
    id("c_cantidadhasta_"+idx).setAttribute("label",pCantidadHasta);
    id("c_cantidaddesde_"+idx).setAttribute("value",CantidadDesde);
    id("c_cantidadhasta_"+idx).setAttribute("value",CantidadHasta);
    id("c_motivo_"+idx).setAttribute('label',vMotivo);       
    id("c_motivo_"+idx).setAttribute('value',Motivo);
    id("c_local_"+idx).setAttribute('label',nombreLocal);
    id("c_historialventa_"+idx).setAttribute('label',HistorialVenta);
    id("c_historialventa_"+idx).setAttribute('value',vHistorialPeriodo);
    id("c_hvperiodo_"+idx).setAttribute('value',xperiodo[1]);

}

function VaciarBusquedaPromocionCliente(){
    var lista = id("listadoPromocionCliente");

    for (var i = 0; i < ilinealistapromocioncliente; i++) { 
        kid = id("linealistapromocioncliente_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilinealistapromocioncliente = 0;
}

function BuscarPromocionCliente(){
    VaciarBusquedaPromocionCliente();
    var desde     = id("FechaBuscaCategoria").value;
    var hasta     = id("FechaBuscaCategoriaHasta").value;
    var categoria = id("CategoriaBusqueda").value;
    var estado    = id("idEstadoCategoria").value;
    RawBuscarPromocionCliente(desde,hasta,categoria,estado,AddLineaPromocionCliente);
    synccomboHistorialVentaPeriodo();

    id('boxFormPromocionCliente').setAttribute('collapsed',true);
}

function RawBuscarPromocionCliente(desde,hasta,categoria,estado,
				   FuncionProcesaLineaPromocionCliente){
    var url = "modpromociones.php?modo=ObtenerPromocionCliente"+
              "&xidlocal="+cIdLocal+
	      "&xdesde="+desde+
	      "&xhasta="+hasta+
	      "&xcategoria="+categoria+
	      "&xestado="+estado;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false); 
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var IdPromocionCliente,Estado,IdUsuario,IdLocal,Descripcion,Motivo,MontoDesde,MontoHasta,CantidadDesde,CantidadHasta,Categoria,Local,Usuario,FechaRegistro,HistorialVenta

    var node,t,i;

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

	    Local           = node.childNodes[t++].firstChild.nodeValue;
	    Usuario         = node.childNodes[t++].firstChild.nodeValue;
	    IdPromocionCliente = node.childNodes[t++].firstChild.nodeValue;
	    Categoria       = node.childNodes[t++].firstChild.nodeValue;
	    Descripcion     = node.childNodes[t++].firstChild.nodeValue;
	    Estado          = node.childNodes[t++].firstChild.nodeValue;
	    MontoDesde      = node.childNodes[t++].firstChild.nodeValue;
	    MontoHasta      = node.childNodes[t++].firstChild.nodeValue;
	    CantidadDesde   = node.childNodes[t++].firstChild.nodeValue;
	    CantidadHasta   = node.childNodes[t++].firstChild.nodeValue;
	    Motivo          = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal         = node.childNodes[t++].firstChild.nodeValue;
	    FechaRegistro   = node.childNodes[t++].firstChild.nodeValue;
	    HistorialVenta  = node.childNodes[t++].firstChild.nodeValue;
	    IdHistorialVenta= node.childNodes[t++].firstChild.nodeValue;

            FuncionProcesaLineaPromocionCliente(item,Local,Usuario,IdPromocionCliente,
						Categoria,Descripcion,Estado,MontoDesde,
						MontoHasta,CantidadDesde,CantidadHasta,
						Motivo,IdLocal,FechaRegistro,HistorialVenta,
						IdHistorialVenta);
        }
    }

}

function AddLineaPromocionCliente(item,Local,Usuario,IdPromocionCliente,Categoria,
				  Descripcion,Estado,MontoDesde,MontoHasta,CantidadDesde,
				  CantidadHasta,Motivo,IdLocal,FechaRegistro,HistorialVenta,
				  IdHistorialVenta){

    var lista    = id("listadoPromocionCliente");
    var xnumitem,xitem,xLocal,xUsuario,xIdPromocionCliente,xCategoria,xDescripcion,xEstado,xMontoDesde,xMontoHasta,xCantidadDesde,xCantidadHasta,xMotivo,xFechaRegistro,xHistorialVenta
    var pMontoDesde,pMontoHasta,pCantidadDesde,pCantidadHasta,vMotivo
    
    vMotivo      = (Motivo == 'MontoCompra')? "Monto Compra":Motivo;
    vMotivo      = (Motivo == 'NumeroCompra')? "Número Compra":vMotivo;

    pMontoDesde  = (MontoDesde == 0)? '':cMoneda[1]['S']+" "+formatDinero(MontoDesde);
    pMontoHasta  = (MontoHasta == 0)? '':cMoneda[1]['S']+" "+formatDinero(MontoHasta);
    pCantidadDesde  = (CantidadDesde == 0)? '':CantidadDesde;
    pCantidadHasta  = (CantidadHasta == 0)? '':CantidadHasta;

    var aHistorialVenta = (IdHistorialVenta != 0)? HistorialVenta.split('~'):false;
    var HVPeriodo       = (IdHistorialVenta != 0)? aHistorialVenta[0]:'';
    var HVDescripcion   = (IdHistorialVenta != 0)? aHistorialVenta[1]:HistorialVenta;
    var HVIdPeriodo      = (IdHistorialVenta != 0)? IdHistorialVenta+'~'+HVPeriodo:'';

    xclass       = (item%2)?'imparrow':'parrow';  
    xitem        = document.createElement("listitem");
    xitem.value  = IdPromocionCliente;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistapromocioncliente_"+ilinealistapromocioncliente);
    ilinealistapromocioncliente++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",Local);
    xLocal.setAttribute("value",IdLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("collapsed",zLocal);
    xLocal.setAttribute("id","c_local_"+IdPromocionCliente);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label",Usuario);
    xUsuario.setAttribute("style","text-align:left");
    xUsuario.setAttribute("collapsed",zUsuario);
    xUsuario.setAttribute("id","c_usuario_"+IdPromocionCliente);

    xCategoria = document.createElement("listcell");
    xCategoria.setAttribute("label",Categoria);
    xCategoria.setAttribute("value",IdPromocionCliente);
    xCategoria.setAttribute("style","text-align:left;font-weight:bold;");
    xCategoria.setAttribute("id","c_categoria_"+IdPromocionCliente);

    xDescripcion = document.createElement("listcell");
    xDescripcion.setAttribute("label",Descripcion);
    xDescripcion.setAttribute("style","text-align:left;");
    xDescripcion.setAttribute("collapsed",zDescripcion);
    xDescripcion.setAttribute("id","c_descripcion_"+IdPromocionCliente);

    xMontoDesde = document.createElement("listcell");
    xMontoDesde.setAttribute("label",pMontoDesde);
    xMontoDesde.setAttribute("value",MontoDesde);
    xMontoDesde.setAttribute("style","text-align:center;");
    xMontoDesde.setAttribute("id","c_montodesde_"+IdPromocionCliente);

    xMontoHasta = document.createElement("listcell");
    xMontoHasta.setAttribute("label",pMontoHasta);
    xMontoHasta.setAttribute("value",MontoHasta);
    xMontoHasta.setAttribute("style","text-align:center;");
    xMontoHasta.setAttribute("id","c_montohasta_"+IdPromocionCliente);

    xCantidadDesde = document.createElement("listcell");
    xCantidadDesde.setAttribute("label",pCantidadDesde);
    xCantidadDesde.setAttribute("value",CantidadDesde);
    xCantidadDesde.setAttribute("style","text-align:center;");
    xCantidadDesde.setAttribute("id","c_cantidaddesde_"+IdPromocionCliente);

    xCantidadHasta = document.createElement("listcell");
    xCantidadHasta.setAttribute("label",pCantidadHasta);
    xCantidadHasta.setAttribute("value",CantidadHasta);
    xCantidadHasta.setAttribute("style","text-align:center;");
    xCantidadHasta.setAttribute("id","c_cantidadhasta_"+IdPromocionCliente);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label",Estado);
    xEstado.setAttribute("id","c_estado_"+IdPromocionCliente);

    xMotivo = document.createElement("listcell");
    xMotivo.setAttribute("label",vMotivo);
    xMotivo.setAttribute("value",Motivo);
    xMotivo.setAttribute("collapsed",zMotivoPromocion);
    xMotivo.setAttribute("id","c_motivo_"+IdPromocionCliente);

    xHistorialVenta = document.createElement("listcell");
    xHistorialVenta.setAttribute("label",HVDescripcion);
    xHistorialVenta.setAttribute("value",HVIdPeriodo);
    xHistorialVenta.setAttribute("collapsed",zHistorialVenta);
    xHistorialVenta.setAttribute("id","c_historialventa_"+IdPromocionCliente);

    xHVPeriodo = document.createElement("listcell");
    xHVPeriodo.setAttribute("value",HVPeriodo);
    xHVPeriodo.setAttribute("collapsed","true");
    xHVPeriodo.setAttribute("id","c_hvperiodo_"+IdPromocionCliente);

    xFechaRegistro = document.createElement("listcell");
    xFechaRegistro.setAttribute("label",FechaRegistro);
    xFechaRegistro.setAttribute("collapsed",zFechaRegistro);
    xFechaRegistro.setAttribute("id","c_fecharegistro_"+IdPromocionCliente);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xFechaRegistro );
    xitem.appendChild( xCategoria );
    xitem.appendChild( xDescripcion );
    xitem.appendChild( xEstado );
    xitem.appendChild( xHistorialVenta );
    xitem.appendChild( xMontoDesde );
    xitem.appendChild( xMontoHasta );
    xitem.appendChild( xCantidadDesde );
    xitem.appendChild( xCantidadHasta );
    xitem.appendChild( xMotivo );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xHVPeriodo);
    lista.appendChild( xitem );	

}


function RevisarPromocionClienteSeleccionada(){

    var idex       = id("listadoPromocionCliente").selectedItem;
    
    if(!idex) return;

    gIdPromocionCliente = id("c_categoria_"+idex.value).getAttribute("value");
    gCategoria       = id("c_categoria_"+idex.value).getAttribute("label");
    gDescripcion     = id("c_descripcion_"+idex.value).getAttribute("label");
    gEstado          = id("c_estado_"+idex.value).getAttribute("label");
    gMontoDesde      = id("c_montodesde_"+idex.value).getAttribute("value");
    gMontoHasta      = id("c_montohasta_"+idex.value).getAttribute("value");
    gCantidadDesde   = id("c_cantidaddesde_"+idex.value).getAttribute("value");
    gCantidadHasta   = id("c_cantidadhasta_"+idex.value).getAttribute("value");
    gMotivo          = id("c_motivo_"+idex.value).getAttribute('value');       
    gIdLocal         = id("c_local_"+idex.value).getAttribute('value');       
    gLocal           = id("c_local_"+idex.value).getAttribute('label');       
    gHistorialVenta  = id("c_historialventa_"+idex.value).getAttribute('label');       
    gHistorialPeriodo= id("c_historialventa_"+idex.value).getAttribute('value');
    gHVPeriodo       = id("c_hvperiodo_"+idex.value).getAttribute('value');

    mostrarFormPromocionCliente('Editar');
}

function BusquedaAvanzadaPromocionCliente(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.value.replace(/ /g,"_");

    switch(xlabel){
    case "-_Local" : 
	zLocal         = xchecked;
	break;
    case "-_Fecha_Registro" : 
	zFechaRegistro = xchecked;
	break;
    case "-_Usuario":
	zUsuario       = xchecked;
	break;
    case "-_Descripcion" :
	zDescripcion   = xchecked;
	break;
    case "-_Ventas_Periodo" :
	zHistorialVenta = xchecked;
	break;
    case "-_Motivo_Promocion":
	zMotivoPromocion  = xchecked;
	break;
    }

    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarPromocionCliente();
}

function mostrarEstadoCategoriaCliente(){
    var xborrador   = true;
    var xejecucion  = true;
    var xcancelado  = true;
    var xfinalizado = true;

    switch(gEstado){
	
    case 'Nuevo':
	xborrador  = false;
	xejecucion = false;
	break;
    case 'Borrador':
	xborrador  = false;
	xejecucion = false;
	xcancelado = false;
	break;
    case 'Ejecucion':
	xfinalizado = false;
	break;

    }

    id("itmEstadoBorradorCat").setAttribute('collapsed',xborrador);
    id("itmEstadoEjecucionCat").setAttribute('collapsed',xejecucion);
    id("itmEstadoEliminadoCat").setAttribute('collapsed',xcancelado);
    id("itmEstadoFinalizadoCat").setAttribute('collapsed',xfinalizado);
}

function synccomboHistorialVentaPeriodo(){
    var	url = "modpromociones.php?modo=mostrarHistorialVentaPeriodo";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;

    comboHistorialVentaPeriodo(xres);
    id("FiltroHistorialVentaPeriodo").value = gHistorialPeriodo;
    
}

function comboHistorialVentaPeriodo(xout=false){

    var xfiltro = id("FiltroHistorialVentaPeriodo");
    xfiltro.removeAllItems();

    var xpopup = document.createElement("menupopup");
    xpopup.setAttribute("id",'combohistorialperiodo');
    xfiltro.appendChild( xpopup );
    //xfiltro.setAttribute('label','Inicio de los tiempos');
    //xfiltro.setAttribute('value','1');

    var xcombo = id("combohistorialperiodo");
    var elemento = document.createElement('menuitem');
    elemento.setAttribute('label','Seleccione...');
    elemento.setAttribute('value','0');
    //elemento.setAttribute('selected',"true");
    xcombo.appendChild(elemento);

    if(!xout) return;
    var pinicio = 'Inicio de los tiempos';
    var pmsj    = "Últimos";
    var filas = xout.split("~");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(":");
	var vlabel = "";
        var elemento = document.createElement('menuitem');
	if(celdas[0] == 1)
	    vlabel = pinicio;
	if(celdas[0] != 1)
	    vlabel = pmsj+' '+celdas[1]+' meses';
        elemento.setAttribute('label',vlabel);
        elemento.setAttribute('value',celdas[0]+'~'+celdas[1]);
	xcombo.appendChild(elemento);
    }
    var elemento = document.createElement('menuitem');
    elemento.setAttribute('label','Nuevo Periodo');
    elemento.setAttribute('style','font-weight:bold;')
    elemento.setAttribute('value','nuevo');
    xcombo.appendChild(elemento);
}

function guardaHistorialVentaPeriodo(){

    var HistorialVenta   = id("textHistorialVentaPeriodo").value;
    var HistorialPeriodo = id("FiltroHistorialVentaPeriodo").value;
    var esEliminar       = 0;
    var Categoria        = id("CategoriaCliente").value;

    if(HistorialPeriodo == 0)
	return;

    if(HistorialPeriodo == 'nuevo'){
	if(trim(HistorialVenta) == '')
	    return;
    }

    if(HistorialPeriodo !='nuevo'){
	var xperiodo         = HistorialPeriodo.split('~');
	HistorialPeriodo     = xperiodo[0];
	var xHistorialVenta  = xperiodo[1];

	if(HistorialVenta == xHistorialVenta)
	    return;
    }

    if(HistorialPeriodo != 'Nuevo' && HistorialVenta == ''){
	var esEliminar = 1;
    }

    if(HistorialPeriodo != 'Nuevo' && HistorialVenta != gHVPeriodo && HistorialVenta > 0)
	if(!confirm('gPOS:\n        Categoría Cliente: '+
		    Categoria+'\n\n         -  Aplicar nuevo Periodo Venta -'+
		    'Últimos '+HistorialVenta+' meses-, ¿estas seguro?')) return RevisarPromocionClienteSeleccionada();

    if(esEliminar == 1){
	if(!confirm('gPOS:\n        Categoría Cliente: '+
		    Categoria+'\n\n         -  Eliminar Periodo Venta -'+
		    'Últimos '+gHVPeriodo+' meses-, ¿estas seguro?')) return RevisarPromocionClienteSeleccionada();
	HistorialVenta = gHVPeriodo;
    }

    var data          = "";
    var url = "modpromociones.php?modo=GuardaHistorialVentaPeriodo";
    data = data + "&xhv="+HistorialVenta;
    data = data + "&xhp="+HistorialPeriodo;
    data = data + "&xelim="+esEliminar;

    var obj = new XMLHttpRequest();
    obj.open("POST",url,false); 
    obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');

    try{
	obj.send(data);
	res = obj.responseText;
    }catch(e){
	res=false;
    }

    if (!obj.responseText)
        return alert(po_servidorocupado);

    if(res == 'existe1'){
	alert('gPOS:\n    El periodo eligo -'+HistorialVenta+'- ya existe');
	RevisarPromocionClienteSeleccionada();
	return;
    }

    if(res == 'existe2'){
	alert('gPOS:\n    Al periodo eligo -'+gHVPeriodo+'- está usando otras categorías');
	RevisarPromocionClienteSeleccionada();
	return;
    }

    synccomboHistorialVentaPeriodo();

    if(HistorialPeriodo == 'nuevo')
	HistorialPeriodo = res;

    id("FiltroHistorialVentaPeriodo").value = (esEliminar == 0)? HistorialPeriodo+'~'+HistorialVenta:0;
    guardaPromocionCliente();

}
