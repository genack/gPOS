
var lista = new Array();
var totalitems = 0;

function id(nombre) { return document.getElementById(nombre) };

function mostrarDatosExtra(xperiodo){
    var xdesde = true;
    var xhasta = true;
    var xsemestre  = true;
    var xtrimestre = true;
    var xmes  = true;
    var xanio = true;
    id("descDesde").value = 'Desde:';

    switch(xperiodo){
      case 'Todo':
	break;
      case 'Dia':
	xdesde = false;
	id("descDesde").value = 'Fecha:'
	break;
      case 'Semana':
	break;
      case 'Mes':
	xmes  = false;
	xanio = false;
	break;
      case 'Trimestre':
	xtrimestre = false;
	xanio      = false;
	break;
      case 'Semestre':
	xsemestre = false;
	xanio     = false;
	break;
      case 'Anio':
	xanio = false;
	break;
      case 'EntreFecha':
	xdesde = false;
	xhasta = false;
	break;
    }

    id("vboxDesde").setAttribute("collapsed",xdesde);
    id("vboxHasta").setAttribute("collapsed",xhasta);
    id("vboxMes").setAttribute("collapsed",xmes);
    id("vboxTrimestre").setAttribute("collapsed",xtrimestre);
    id("vboxSemestre").setAttribute("collapsed",xsemestre);
    id("vboxAnio").setAttribute("collapsed",xanio);

    buscarMovimientos();
}


function vaciarMovimientosDetalle(){
    var grid,fila,i;
    grid= id("contenedor");

    for(i = 0; i<totalitems; i++)
      {
        fila=id(i);
        grid.removeChild(fila);
      }
    //lista = new Array();
    totalitems = 0;
}

function buscarMovimientos(xperiodo){
    vaciarMovimientosDetalle();

    var idlocal    = id("FiltroLocal").value;
    var fechadesde = id("FechaDesde").value;
    var fechahasta = id("FechaHasta").value;
    var periodo    = id("FiltroPeriodo").value;
    var mes        = id("filtroMes").value;
    var trimestre  = id("filtroTrimestre").value;
    var semestre   = id("filtroSemestre").value;
    var anio       = id("filtroAnio").value;
    
    RawBuscarMovimientos(idlocal,fechadesde,fechahasta,periodo,mes,trimestre,
			 semestre,anio);
 }


function RawBuscarMovimientos(idlocal,fechadesde,fechahasta,periodo,mes,trimestre,
			      semestre,anio){

    var grid,xrequest,xitem,xclass;
    var url = "modreportes.php?modo=movimientos"+
	      "&local="+idlocal+
	      "&desde="+fechadesde+
	      "&hasta="+fechahasta+
	      "&periodo="+periodo+
	      "&mes="+mes+
	      "&trimestre="+trimestre+
	      "&semestre="+semestre+
	      "&anio="+anio;

    xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //xres     = xrequest.responseText;
    //alert(xrequest.responseText);
    //if(xres=="") return;
    var xml = xrequest.responseXML.documentElement;
    var item = 0;//xml.childNodes.length;
    totalitems = xml.childNodes.length;
    var node,t,i,codcompra; 
    var totalcosto=0, totalimpuesto=0,totalimporte=0,totalutilidad=0;
    var ttotalcosto=0, ttotalimpuesto=0,ttotalimporte=0,ttotalutilidad=0;
    var Local,Cpbts,Impuesto,Importe,Costo,Utilidad,IdLocal;
    var xlocal = 0;
    var count = 0;

    //lista    = new Array();
    //lista    = xres.split(";");
    grid     = id("contenedor");
    //xitem     = lista.length;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            Local 	    = node.childNodes[t++].firstChild.nodeValue;
            IdLocal 	    = node.childNodes[t++].firstChild.nodeValue;
            Cpbts 	    = node.childNodes[t++].firstChild.nodeValue;
            Impuesto 	    = node.childNodes[t++].firstChild.nodeValue;
	    Importe         = node.childNodes[t++].firstChild.nodeValue;
	    Costo           = node.childNodes[t++].firstChild.nodeValue;
	    Utilidad        = node.childNodes[t++].firstChild.nodeValue;

	    if(count == 0) xlocal = IdLocal;

	    if(IdLocal == xlocal){
		yLocal 	       = Local;
		yIdLocal       = IdLocal;
		totalcosto     = totalcosto+parseFloat(Costo);
		totalimpuesto  = totalimpuesto+parseFloat(Impuesto);
		totalimporte   = totalimporte+parseFloat(Importe);
		totalutilidad  = totalutilidad+parseFloat(Utilidad);
		xlocal = yIdLocal;
	    }

	    ttotalcosto     = ttotalcosto+parseFloat(Costo);
	    ttotalimpuesto  = ttotalimpuesto+parseFloat(Impuesto);
	    ttotalimporte   = ttotalimporte+parseFloat(Importe);
	    ttotalutilidad  = ttotalutilidad+parseFloat(Utilidad);

	    if(xlocal != IdLocal){
		AddLineaOrdenCompra(item,yLocal,yIdLocal,totalimpuesto,totalimporte,
				    totalcosto,totalutilidad,grid);
		item++;

		yLocal 	       = Local;
		yIdLocal        = IdLocal;
		totalcosto     = parseFloat(Costo);
		totalimpuesto  = parseFloat(Impuesto);
		totalimporte   = parseFloat(Importe);
		totalutilidad  = parseFloat(Utilidad);
	    }

	    if(i == (xml.childNodes.length-1)){
		AddLineaOrdenCompra(item,yLocal,yIdLocal,totalimpuesto,totalimporte,
				    totalcosto,totalutilidad,grid);
		item++;
	    }

	    count++;
	}
	totalitems = item;
    }

    id("totalCosto").value    = cMoneda[1]['S']+formatDinero(ttotalcosto);
    id("totalImpuesto").value = cMoneda[1]['S']+formatDinero(ttotalimpuesto);
    id("totalImporte").value  = cMoneda[1]['S']+formatDinero(ttotalimporte);
    id("totalUtilidad").value = cMoneda[1]['S']+formatDinero(ttotalutilidad);
}

function AddLineaOrdenCompra(xitem,Local,IdLocal,Impuesto,Importe,Costo,Utilidad,grid){
	  
    var item = document.createElement('label');
    item.setAttribute('value',xitem+1);
    item.setAttribute('style','text-align:center;font-weight:bold;');
    
    var local = document.createElement('label');
    local.setAttribute('value',Local);
    local.setAttribute('label',IdLocal);
    local.setAttribute('readonly','true');
    //localsunitarios.push(arreglo[i]);
    //local.setAttribute('id','local_'+arreglo[i+2]);
    //local.setAttribute('style','text-align:right');    
    
    var costo = document.createElement('label');
    costo.setAttribute('value',formatDineroTotal(Costo));
    costo.setAttribute('readonly','true');
    //costosunitarios.push(arreglo[i]);
    //costo.setAttribute('id','costo_'+arreglo[i+2]);
    costo.setAttribute('style','text-align:right');    

    var impuesto = document.createElement('label');
    impuesto.setAttribute('value',formatDineroTotal(Impuesto));
    impuesto.setAttribute('readonly','true');
    //impuestosunitarios.push(arreglo[i]);
    //impuesto.setAttribute('id','impuesto_'+arreglo[i+2]);
    impuesto.setAttribute('style','text-align:right');    

    var importe = document.createElement('label');
    importe.setAttribute('value',formatDineroTotal(Importe));
    importe.setAttribute('readonly','true');
    //importesunitarios.push(arreglo[i]);
    //importe.setAttribute('id','importe_'+arreglo[i+2]);
    importe.setAttribute('style','text-align:right'); 

    var utilidad = document.createElement('label');
    utilidad.setAttribute('value',formatDineroTotal(Utilidad));
    utilidad.setAttribute('readonly','true');
    //utilidadsunitarios.push(arreglo[i]);
    //utilidad.setAttribute('id','utilidad_'+arreglo[i+2]);
    utilidad.setAttribute('style','text-align:right;font-weight:bold;');    

    var row = document.createElement('row');
    //row.setAttribute('value',arreglo[i+2]);
    //row.setAttribute('onfocus','click()');
    var i = xitem;
    var xclass = (i%2)?'parrowkardex':'imparrowkardex';  
    row.setAttribute('class',xclass);
    row.setAttribute('onclick','selrowReporte(this)');
    row.setAttribute('id',xitem);

    row.appendChild(item);
    row.appendChild(local);
    row.appendChild(costo);
    row.appendChild(impuesto);
    row.appendChild(importe);
    row.appendChild(utilidad);
    grid.appendChild(row);
}

function imprimir(){
    var idlocal    = id("FiltroLocal").value;
    var fechadesde = id("FechaDesde").value;
    var fechahasta = id("FechaHasta").value;
    var opcionvence= id("FiltroEstado").value;

    if(!totalvenceitems) return;

    var	url      = "../fpdf/imprimir_fechavencimiento.php?"+
	           "xlocal="+idlocal+
	           "&desde="+fechadesde+
	           "&hasta="+fechahasta+
    	           "&opestado="+opcionvence;

    location.href=url;
}

function regenAnioReporte(){
    VaciarAnioReportes();
    var url = "modreportes.php?modo=obtenerAnios";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;
    
    var xanios = xres.split(",");
    
    for(var i=0;i<xanios.length;i++){
	AddAnioReporte(xanios[i]);
    }
}

var ianios = 0;
function AddAnioReporte(anios){
    var xlistitem = id("elementosanio");	

    var xanio = document.createElement("menuitem");
    xanio.setAttribute("id","anio_def_" + ianios);
	
    xanio.setAttribute("value",anios);
    xanio.setAttribute("label",anios);
    xanio.setAttribute("selected",true);
    
    xlistitem.appendChild( xanio);
    ianios ++;
}

function VaciarAnioReportes(){
    var xlistitem = id("elementosanio");
    var iditem;
    var t = 0;
    
    while( el = id("anio_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    ianios = 0;
}

function cargarDatosDefault(){
    var fechadesde = id("FechaDesde").value;

    var afecha = fechadesde.split("-");
    afecha[1] = parseInt(afecha[1]);
    var trimestre  = (afecha[1] <= 3 )? 1:"";
    trimestre  = (afecha[1] > 3 && afecha[1] <= 6  )? 2:trimestre;
    trimestre  = (afecha[1] > 6 && afecha[1] <= 9  )? 3:trimestre;
    trimestre  = (afecha[1] > 9 && afecha[1] <= 12  )? 4:trimestre;

    var semestre = (afecha[1] <= 6)? 1:2;
    
    id("filtroMes").value = parseInt(afecha[1]);
    id("filtroTrimestre").value = trimestre;
    id("filtroSemestre").value = semestre;
}

function selrowReporte(xthis){

    var xclass; 

    switch (xthis.getAttribute('class')) {

    case 'parrowkardex':
	xclass = 'parselrowkardex';
	break;

    case 'imparrowkardex':
	xclass = 'imparselrowkardex';
	break;

    case 'parselrowkardex':
	xclass = 'parrowkardex';
	break;

    case 'imparselrowkardex':
	xclass = 'imparrowkardex';
	break;
    }

    xthis.setAttribute("class",xclass);
}

// --------------- Vecimientos ----------------

var totalvenceitems = 0;
function mostrarDatosExtraVence(estado){
    var xdesde = true;
    var xhasta = true;
    id("descDesde").value = 'Desde:';

    switch(estado){
      case 'PorVencer':
	xdesde = false;
	xhasta = false;
	break;
    }

    id("vboxDesde").setAttribute("collapsed",xdesde);
    id("vboxHasta").setAttribute("collapsed",xhasta);

    buscarVencimientos();
}

function vaciarVencimientosDetalle(){
    var grid,fila,i;
    grid= id("vencimiento");

    for(i = 0; i<totalvenceitems; i++){
        fila=id(i);
        grid.removeChild(fila);
      }

    totalvenceitems = 0;
}

function buscarVencimientos(){
    vaciarVencimientosDetalle();

    var idlocal    = id("FiltroLocal").value;
    var fechadesde = id("FechaDesde").value;
    var fechahasta = id("FechaHasta").value;
    var opcionvence= id("FiltroEstado").value;
    
    RawBuscarVencimientos(idlocal,fechadesde,fechahasta,opcionvence);
 }


function RawBuscarVencimientos(idlocal,fechadesde,fechahasta,opcionvence){

    var grid,xrequest,xitem,xclass;
    var url = "modreportes.php?modo=vencimientos"+
	      "&local="+idlocal+
	      "&desde="+fechadesde+
	      "&hasta="+fechahasta+
 	      "&opvence="+opcionvence;

    xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = xrequest.responseText.split("<?xml");
    if(xres[0] != '') 
	return alert(po_servidorocupado+ "\n\n Error: "+xres[0]);

    var xml = xrequest.responseXML.documentElement;
    var item = 0;//xml.childNodes.length;
    totalvenceitems = xml.childNodes.length;

    var node,t,i,codcompra; 
    var Local,CB,Producto,Stock,FechaVencimneiento,Utilidad;

    grid     = id("vencimiento");

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            IdLocal 	    = node.childNodes[t++].firstChild.nodeValue;
            Local 	    = node.childNodes[t++].firstChild.nodeValue;
            IdPedidoDet     = node.childNodes[t++].firstChild.nodeValue;
            IdProducto 	    = node.childNodes[t++].firstChild.nodeValue;
            CB    	    = node.childNodes[t++].firstChild.nodeValue;
            Producto 	    = node.childNodes[t++].firstChild.nodeValue;
	    FechaVencimiento = node.childNodes[t++].firstChild.nodeValue;
	    Lote            = node.childNodes[t++].firstChild.nodeValue;
	    Stock           = node.childNodes[t++].firstChild.nodeValue;

	    AddLineaVencimientos(item,Local,CB,IdProducto,Producto,Stock,Lote,FechaVencimiento,grid);
	    item++;
	}
    }
    id("totalProductos").setAttribute("label",totalvenceitems);
}

function AddLineaVencimientos(xitem,Local,CB,IdProducto,Producto,Stock,Lote,
			      FechaVencimiento,grid){
	  
    var item = document.createElement('label');
    item.setAttribute('value',xitem+1);
    item.setAttribute('style','text-align:center;font-weight:bold;');
    
    var xlocal = document.createElement('label');
    xlocal.setAttribute('value',Local);
    xlocal.setAttribute('readonly','true');
    //localsunitarios.push(arreglo[i]);
    //local.setAttribute('id','local_'+arreglo[i+2]);
    //local.setAttribute('style','text-align:right');    
    
    var xcb = document.createElement('label');
    xcb.setAttribute('value',CB);
    xcb.setAttribute('readonly','true');
    //CBsunitarios.push(arreglo[i]);
    //CB.setAttribute('id','CB_'+arreglo[i+2]);
    xcb.setAttribute('style','text-align:right');    

    var xproducto = document.createElement('label');
    xproducto.setAttribute('value',Producto);
    xproducto.setAttribute('readonly','true');
    //productosunitarios.push(arreglo[i]);
    //producto.setAttribute('id','producto_'+arreglo[i+2]);
    xproducto.setAttribute('style','text-align:left');    


    var xstock = document.createElement('label');
    xstock.setAttribute('value',Stock);
    xstock.setAttribute('readonly','true');
    //stocksunitarios.push(arreglo[i]);
    //stock.setAttribute('id','stock_'+arreglo[i+2]);
    xstock.setAttribute('style','text-align:right');    

    var xfechavencimiento = document.createElement('label');
    xfechavencimiento.setAttribute('value',FechaVencimiento);
    xfechavencimiento.setAttribute('readonly','true');
    //fechavencimientosunitarios.push(arreglo[i]);
    //fechavencimiento.setAttribute('id','fechavencimiento_'+arreglo[i+2]);
    xfechavencimiento.setAttribute('style','text-align:right'); 

    var xlote = document.createElement('label');
    xlote.setAttribute('value',Lote);
    xlote.setAttribute('readonly','true');
    //lotesunitarios.push(arreglo[i]);
    //lote.setAttribute('id','lote_'+arreglo[i+2]);
    xlote.setAttribute('style','text-align:right;font-weight:bold;');    

    var row = document.createElement('row');
    //row.setAttribute('value',arreglo[i+2]);
    //row.setAttribute('onfocus','click()');
    var i = xitem;
    var xclass = (i%2)?'parrowkardex':'imparrowkardex';  
    row.setAttribute('class',xclass);
    row.setAttribute('onclick','selrowReporte(this)');
    row.setAttribute('id',xitem);

    row.appendChild(item);
    row.appendChild(xlocal);
    row.appendChild(xcb);
    row.appendChild(xproducto);
    row.appendChild(xstock);
    row.appendChild(xfechavencimiento);
    row.appendChild(xlote);
    grid.appendChild(row);
}

