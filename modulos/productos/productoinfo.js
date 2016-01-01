
var id = function(name) { return document.getElementById(name); }

var cInventario           = 'Ajuste';
var cIdArticulo           = 0;
var cIdAlmacen            = 0;
var cIdProducto           = 0;
var cProducto             = '';

var ilinealistaalmacen    = 0;
var n                     = 0;
var Vistas                = new Object(); 
Vistas.ventas             = 7;


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
	//alert(busca+" = "+cadena);
        //cadena = cadena.toUpperCase();
        //if(cadena.indexOf(busca) != -1)
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
	//alert(busca+" = "+cadena);
        //cadena = cadena.toUpperCase();
        //if(cadena.indexOf(busca) != -1)
	if( busca == cadena )
            return lista.selectItem(xtexto);
    }
    //alert('gPOS:\n\n   - El codigo " '+elemento+' " no esta la lista.');
    //id("busquedaCodigoSerie").value='';
}

//Busqueda Almacen
function BuscarAlmacen(){

    VaciarBusquedaAlmacen();

    var filtrolocal = id("FiltroLocal").value;
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
    id("resumenAlmacen").setAttribute("collapsed",false);
    id("formAjustesExistencias").setAttribute("collapsed",true);
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
        + "&xnombre=" + trim(nombre)
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

    var srt = (totalProductos > 1 )? 's':'';
    id("TotalProductos").value = totalProductos+" producto"+srt+" listado"+srt;
}


function verStockAlmacen(){

    id("NombreBusqueda").focus();
    //BuscarAlmacen();
}

function modificarArticuloSeleccionada(){

    var idex       = id("listadoAlmacen").selectedItem;
    
    if(!idex) return;

    cIdArticulo    = idex.value;
    cIdAlmacen     = id("idlocal_"+idex.value).getAttribute("value");

    cProducto      = id("producto_"+idex.value).getAttribute("label");
    cIdProducto    = id("idproducto_"+idex.value).getAttribute("value");

    id("resumenAlmacen").setAttribute("collapsed",true);  
    id("formAjustesExistencias").setAttribute("collapsed",false);
    id("xProducto").setAttribute("label",cProducto); 

    id("webkardex").setAttribute("src","about:blank");  
    cAjusteModo                    = 'igual';
    cAjusteExistencias             = 0;

    obtenerProductoInformacion(cIdProducto);

    //id("btnModificarStock").setAttribute("oncommand","validaModificarStock()");
}


/*++++++++++ Productos información ++++++++++*/ 


function guardaProductoInformacion(){
    var IdProducto = cIdProducto;
    var z          = null;
    var data       = '';
    var str        = '';
    var Indicacion = '';
    var ContraIndicacion = '';
    var Interaccion      = '';
    var Dosificacion     = '';
    var xitem,res;
    var xind,xcont,xint,xdosif;

    for(var i=0;i<5;i++ ){
	xitem = i+1;
	xind  = id("xIndicacion"+xitem).value;
	xind = (trim(xind))? xind:'';

	xcont = id("xContraIndicacion"+xitem).value;
	xcont = (trim(xcont))? xcont:'';
	
	xint  = id("xInteraccion"+xitem).value;
	xint = (trim(xint))? xint:'';
	
	xdosif = id("xDosificacion"+xitem).value;
	xdosif = (trim(xdosif))? xdosif:'';

	Indicacion       += str+xind;
	ContraIndicacion += str+xcont;
	Interaccion      += str+xint;
	Dosificacion     += str+xdosif;
	str = ';';
    }

    var url = "modproductoextra.php?modo=GuardaProductoInformacion";
    data = data + "&xidp="+IdProducto;
    data = data + "&xind="+Indicacion;
    data = data + "&xcind="+ContraIndicacion;
    data = data + "&xint="+Interaccion;
    data = data + "&xdos="+Dosificacion;

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
    
}

function obtenerProductoInformacion(idproducto){
    var url = "modproductoextra.php?modo=ObtenerProductoInformacion"+"&xidp="+idproducto;
    var obj = new XMLHttpRequest();
    var z   = null;

    obj.open("GET",url,false); 

    try{
	obj.send(null);
    }catch(z){
	return;
    }
    
    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var tex, node, item=0;
    var ProductoInformacion = false;
    var xml = obj.responseXML.documentElement;
    
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    item++;
            t = 0;
	    ProductoInformacion  = node.childNodes[t++].firstChild.nodeValue;
        }
    }

    AddLineaProductoInformacion(ProductoInformacion);
}

function AddLineaProductoInformacion(ProductoInformacion){

    cleanFormProductoInfirmacion();

    if(ProductoInformacion){
	var ProductoInformacion = ProductoInformacion.split("~");
	var t_array        = ProductoInformacion.length;
	
	var Indicaciones   = ProductoInformacion[0];
	var CtraIndicacion = ProductoInformacion[1];
	var Interacciones  = ProductoInformacion[2];
	var Dosificaciones = ProductoInformacion[3];
	
	var Indicacion     = Indicaciones.split(";");
	var CIndicacion    = CtraIndicacion.split(";");
	var Interaccion    = Interacciones.split(";");
	var Dosificacion   = Dosificaciones.split(";");


	for(var i=0;i<5;i++){
	    var xitem = i+1;

	    if(Indicacion[i]){
		id("xIndicacion"+xitem).value = Indicacion[i];		
		id("xIndicacion"+xitem).setAttribute('collapsed',false);


		if(xitem < 5)
		    id("btn1"+xitem).setAttribute('collapsed',false);

		if(0<i && i<5 )
		    id("btn1"+i).setAttribute('collapsed',true);
	    }

	    if(CIndicacion[i]){
		id("xContraIndicacion"+xitem).value = CIndicacion[i];
		id("xContraIndicacion"+xitem).setAttribute('collapsed',false);

		if(xitem < 5)
		    id("btn2"+xitem).setAttribute('collapsed',false);

		if(0<i && i<5 )
		    id("btn2"+i).setAttribute('collapsed',true);
	    }

	    if(Interaccion[i]){
		id("xInteraccion"+xitem).value = Interaccion[i];
		id("xInteraccion"+xitem).setAttribute('collapsed',false);

		if(xitem < 5)
		    id("btn3"+xitem).setAttribute('collapsed',false);

		if(0<i && i<5 )
		    id("btn3"+i).setAttribute('collapsed',true);
	    }

	    if(Dosificacion[i]){
		id("xDosificacion"+xitem).value = Dosificacion[i];
		id("xDosificacion"+xitem).setAttribute('collapsed',false);

		if(xitem < 5)
		    id("btn4"+xitem).setAttribute('collapsed',false);

		if(0<i && i<5 )
		    id("btn4"+i).setAttribute('collapsed',true);
	    }
	    
	}
	
	
    }
    
}

function cleanFormProductoInfirmacion(){

    for(var i=0;i<5;i++){
	var xbtn = false;
	var xitem = i+1;
	id("xIndicacion"+xitem).value       = "";
	id("xContraIndicacion"+xitem).value = "";
	id("xInteraccion"+xitem).value      = "";
	id("xDosificacion"+xitem).value     = "";

	if(i>0){

	    id("xIndicacion"+xitem).setAttribute('collapsed',true);
	    id("xContraIndicacion"+xitem).setAttribute('collapsed',true);
	    id("xInteraccion"+xitem).setAttribute('collapsed',true);
	    id("xDosificacion"+xitem).setAttribute('collapsed',true);

	    xbtn = true;
	}

	if(xitem < 5){

	    id("btn1"+xitem).setAttribute('collapsed',xbtn);
	    id("btn2"+xitem).setAttribute('collapsed',xbtn);
	    id("btn3"+xitem).setAttribute('collapsed',xbtn);
	    id("btn4"+xitem).setAttribute('collapsed',xbtn);
	}
    }
}

function volverProductos(){
    parent.solapa('xulproductos.php?modo=lista',
		  'Productos - Gestión de Productos',
		  'productos');
}

function checkItemsFichaTecnica(xgroup,xitem){

    var xitem = parseFloat(xitem);
    var nitem = xitem + 1;

    switch(xgroup){
    case 1:

	if(trim(id("xIndicacion"+xitem).value)!=''){
	    id("btn"+xgroup+''+xitem).setAttribute('collapsed',true);
	    id("xIndicacion"+nitem).setAttribute('collapsed',false);

	    if(nitem<5)
		id("btn"+xgroup+''+nitem).setAttribute('collapsed',false);
	}

	break;

    case 2:
	if(trim(id("xContraIndicacion"+xitem).value)!=''){
	    id("btn"+xgroup+''+xitem).setAttribute('collapsed',true);
	    id("xContraIndicacion"+nitem).setAttribute('collapsed',false);

	    if(nitem<5)
		id("btn"+xgroup+''+nitem).setAttribute('collapsed',false);
	}

	break;

    case 3:
	if(trim(id("xInteraccion"+xitem).value)!=''){
	    id("btn"+xgroup+''+xitem).setAttribute('collapsed',true);
	    id("xInteraccion"+nitem).setAttribute('collapsed',false);

	    if(nitem<5)
		id("btn"+xgroup+''+nitem).setAttribute('collapsed',false);
	}

	break;
    case 4:
	if(trim(id("xDosificacion"+xitem).value)!=''){
	    id("btn"+xgroup+''+xitem).setAttribute('collapsed',true);
	    id("xDosificacion"+nitem).setAttribute('collapsed',false);

	    if(nitem<5)
		id("btn"+xgroup+''+nitem).setAttribute('collapsed',false);
	}

	break;
    }

}
