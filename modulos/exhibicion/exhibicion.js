var id  = function(name) { return document.getElementById(name); }
var IGV = 0;
//var esSyncStock = false;//Sync Exhibicion
var AjaxSyncDemon = new XMLHttpRequest();//Sync Exhibicion

function inicializar(datos){
    //NO NECESARIO
}

function formatoMoneda(value, decimals, separators) {
    decimals = decimals >= 0 ? parseInt(decimals, 0) : 2;
    separators = separators || ['.', "'", ','];
    var number = (parseFloat(value) || 0).toFixed(decimals);
    if (number.length <= (4 + decimals))
        return number.replace('.', separators[separators.length - 1]);
    var parts = number.split(/[-.]/);
    value = parts[parts.length > 1 ? parts.length - 2 : 0];
    var result = value.substr(value.length - 3, 3) + (parts.length > 1 ?
        separators[separators.length - 1] + parts[parts.length - 1] : '');
    var start = value.length - 6;
    var idx = 0;
    while (start > -3) {
        result = (start > 0 ? value.substr(start, 3) : value.substr(0, 3 + start))
            + separators[idx] + result;
        idx = (++idx) % 2;
        start -= 3;
    }
    return (parts.length == 3 ? '-' : '') + result;
}


function VaciarProductosAlamcen(){
    var numLista= id("numLista").value;
    var numFilas = id("numLista").value;
    var grid0= id("productos");
    var grid1= id("detalle_productos");
    var grid2= id("detalle_directo");
    var grid3= id("detalle_corporativo");
    var grid4= id("costo_productos");
    var grid5= id("precio_empaque");

    var row1,row0;
    var col=id("iniciopagina").value;
    col++;
    for (var i = 0; i < numLista; i++) { 
        row0 = id('ROW0-'+(col));
        row1 = id('ROW1-'+(col));
	grid0.removeChild(row0);
	grid1.removeChild(row1);
	col++;
 	numFilas--;
	id("numLista").value=numFilas;
    }

}

function iniComboFamilias(cadena){
    var combofamilia = id("combofamilia");
    var filas = cadena.split(";");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(",");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
        elemento.setAttribute('oncommand','cambiarFamilia('+celdas[0]+")");
        combofamilia.appendChild(elemento);
    }
    //<menuitem label="Todos" selected="true"  />
}

function iniComboLocalesPrecios(cadena){
    var combolocales = id("combolocales");
    var filas = cadena.split(";");

    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(",");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
        elemento.setAttribute('oncommand','cambiarLocales('+celdas[0]+")");
        combolocales.appendChild(elemento);
    }
    //<menuitem label="Todos" selected="true"  />
}

function iniComboMarcas(cadena){
    var combomarca = id("combomarca");
    var filas = cadena.split(";");
    for(var i = 0; i<filas.length; i++){
        var celdas = filas[i].split(",");
        var elemento = document.createElement('menuitem');
        elemento.setAttribute('label',celdas[1]);
        elemento.setAttribute('value',celdas[0]);
        elemento.setAttribute('oncommand','cambiarMarca('+celdas[0]+")");
        combomarca.appendChild(elemento);
    }
    //<menuitem label="Todos" selected="true"  />
}


function iniComboPaginas(totalPaginas,numFilasPaginas,totalFilasProductos){
    var iniciopagina = id("iniciopagina").value;
    var listaPaginasMenu = id("listaPaginas");
    var elementoMenu = document.createElement('menupopup');
    elementoMenu.setAttribute('id','comboPaginas');
    listaPaginasMenu.appendChild(elementoMenu);

    var combopagina = id("comboPaginas");
    var inipagina, endpagina; 
    var inipagina = 0;
    var endpagina = numFilasPaginas;
    var rangopagina = numFilasPaginas;

    for(var i = 0; i < totalPaginas; i++){

        var elemento = document.createElement('menuitem');
	//if (inipagina > 0){
	var rangolist =parseFloat(inipagina)+parseFloat(1)+' - '+endpagina;
        elemento.setAttribute('label',rangolist);
        elemento.setAttribute('type','checkbox');
        elemento.setAttribute("oncommand","Paginar("+inipagina+",'"+rangolist+"')");
	elemento.setAttribute('checked',false);
	if(iniciopagina == inipagina)
	            elemento.setAttribute('checked',true);
        combopagina.appendChild(elemento);
	//}
	inipagina = parseFloat(inipagina) + parseFloat(rangopagina); 
	endpagina = parseFloat(inipagina) + parseFloat(rangopagina);
    }
}
function Paginar(inipagina,rangolist){

    var listaPaginas = id("listaPaginas");
    listaPaginas.setAttribute('label','Pag. '+rangolist);
    filtrarProductos(inipagina);

}

function iniBusqueda(codigo,descripcion,idfamilia,idmarca){
    id("codigo").value = codigo;
    id("descripcion").value = descripcion;

    if(idmarca != 0){
	id("listmarca").value = idmarca;
        id("idmarca").value = idmarca;
    }else{
	id("idmarca").value = 0;
    }

    if(idfamilia != 0){
	id("listfamilia").value = idfamilia;
	id("idfamilia").value = idfamilia;
    }else{
	id("idfamilia").value = 0;
    }
    setTimeout("filtrarProductos()",400);
    setTimeout("Demon_syncExhibicion()",20000);//Sync Exhibicion
}


function soloNumeros(evt,num){
    var keynum
    if(window.event){
        keynum = evt.keyCode;
    }else{
        keynum = evt.which;
    }
    if(keynum == 46){
        var sChar=String.fromCharCode(keynum);
        if(isNaN(num+sChar)) return false;
    }
    return (keynum <= 13 || (keynum >= 48 && keynum <= 57) || keynum == 46);
}

function soloNumerosEnteros(evt,num){
    // Backspace = 8, Enter = 13, ’0′ = 48, ’9′ = 57, ‘.’ = 46
    keynum = (window.event)?evt.keyCode:evt.which;
    if(keynum == 46) 
    {
	var sChar=String.fromCharCode(keynum);
	if(isNaN(num+sChar)) return false;
    }
    return (keynum <= 13 || (keynum >= 48 && keynum <= 57));
}

function cambiarFamilia(valor){
    id("listaPaginas").label='Pag. 1 - 10';
    VaciarProductosAlamcen();
    document.getElementById("idfamilia").value=valor;
    id("iniciopagina").value=0; 
    filtrarProductos();
}

function cambiarLocales(valor){
    id("listaPaginas").label='Pag. 1 - 10';
    VaciarProductosAlamcen();
    document.getElementById("listalocal").value=valor;
    id("iniciopagina").value=0; 
    filtrarProductos();
}

function iniPaginar(){
    id("listaPaginas").label='Pag. 1 - 10';
    VaciarProductosAlamcen();
    filtrarProductos();
}

function cambiarMarca(valor){
    id("listaPaginas").label='Pag. 1 - 10';
    VaciarProductosAlamcen();
    document.getElementById("idmarca").value=valor;
    id("iniciopagina").value=0;
    filtrarProductos();
}
//MU_Directo,IGV_Directo,PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado
//CostoOperativo,PVDE,
//,PVDED
function AddLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,i,z,pvBloq,pvcBloq,UnidadMedida,esMenudeo,Empaque,StockIlimitado,Disponible,DisponibleOnline,UnidadesVitrina,StockVitrina,StockVitrinaDevol){

    var grid0= id("productos"); 
    var grid1= id("detalle_productos");
    var grid2= id("detalle_directo");
    var grid3= id("detalle_corporativo");
    var grid4= id("costo_productos");
    var grid5= id("precio_empaque");
    var row0 = document.createElement('row');
    var row1 = document.createElement('row');
    var item             = document.createElement('description');
    var xNombreProducto  = document.createElement('description');
    
    var nameprod         = NombreProducto.split('~~');
    var CodProducto      = nameprod[0];
    var ckStockIlimitado = ( StockIlimitado == 1);
    var ckStockWeb       = ( DisponibleOnline == 1 );
    var ckStockTPV       = ( Disponible == 1 );
    var StockExhibicion  = UnidadesVitrina;
    var rstStockVitrina  = parseFloat(StockVitrina)-parseFloat(UnidadesVitrina);
    var viewbtnReponer   = (!( rstStockVitrina > 0 || parseFloat( StockVitrinaDevol ) > 0 ));
    var mjsbtnReponer    = ( parseFloat( StockVitrinaDevol ) > 0 )?
                           'Reponer '+StockVitrinaDevol+' '+UnidadMedida+' x Devolución':
                           'Reponer '+rstStockVitrina+' '+UnidadMedida+' x Venta';
    var txtEmpaque       = (Empaque == '...')? '...':'x'+Empaque;
    NombreProducto       = nameprod[1];
    esMenudeo            = (esMenudeo == 1)? false:true;


    item.setAttribute('value', parseFloat(i)+parseFloat(1));

    xNombreProducto.textContent = ( NombreProducto.length > 105 )? NombreProducto.slice(0,105)+ '....':NombreProducto;

    xNombreProducto.setAttribute('readonly','true');
    xNombreProducto.setAttribute('id','PRDTO'+(parseFloat(i)+parseFloat(1)));

    var xUnidades = document.createElement('description');
    xUnidades.setAttribute('value',Unidades+' '+UnidadMedida);
    xUnidades.setAttribute('readonly','true');
    xUnidades.setAttribute('style',"text-align:right");
    xUnidades.setAttribute('id','CD'+(parseFloat(i)+parseFloat(1)));

    var xStock = document.createElement('textbox');
    xStock.setAttribute('value',Unidades);
    xStock.setAttribute('hidden','true');
    xStock.setAttribute('id','STK'+(parseFloat(i)+parseFloat(1)));

    var xStockUnid = document.createElement('textbox');
    xStockUnid.setAttribute('value',UnidadMedida);
    xStockUnid.setAttribute('hidden','true');
    xStockUnid.setAttribute('id','STKUnid'+(parseFloat(i)+parseFloat(1)));
    
    var xStockMin = document.createElement('textbox');
    xStockMin.setAttribute('value',StockMin);
    xStockMin.setAttribute("style","text-align:right"); 
    xStockMin.setAttribute("class","stockmin"); 
    xStockMin.setAttribute('id','SM'+(parseFloat(i)+parseFloat(1)));
    xStockMin.setAttribute('onkeypress','return soloNumerosEnteros(event,this.value)');
    xStockMin.setAttribute('onfocus','this.select()');
    xStockMin.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SV')");

    var xStockExhibicion = document.createElement('description');
    xStockExhibicion.setAttribute('value',StockExhibicion+' '+UnidadMedida);
    xStockExhibicion.setAttribute("style","text-align:right");
    xStockExhibicion.setAttribute('id','SET'+(parseFloat(i)+parseFloat(1)));
    xStockExhibicion.setAttribute("class","stockmin"); 

    var xStockExhibicionMin = document.createElement('textbox');
    xStockExhibicionMin.setAttribute('value',StockVitrina);
    xStockExhibicionMin.setAttribute("style","text-align:right"); 
    xStockExhibicionMin.setAttribute("class","stockmin"); 
    xStockExhibicionMin.setAttribute('id','SEM'+(parseFloat(i)+parseFloat(1)));
    xStockExhibicionMin.setAttribute('onkeypress','return soloNumerosEnteros(event,this.value)');
    xStockExhibicionMin.setAttribute('onfocus','this.select()');
    xStockExhibicionMin.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SV')");

    var xStockExhibicionReponer = document.createElement('description');
    xStockExhibicionReponer.setAttribute('value',mjsbtnReponer);
    xStockExhibicionReponer.setAttribute("style","text-align:left;color:#aa0000!important");
    xStockExhibicionReponer.setAttribute('id','SER'+(parseFloat(i)+parseFloat(1)));
    xStockExhibicionReponer.setAttribute("class","btn");
    xStockExhibicionReponer.setAttribute("collapsed",viewbtnReponer);

    var xStockIlimitado = document.createElement('checkbox');
    xStockIlimitado.setAttribute('Label','');
    xStockIlimitado.setAttribute("class","stockmin"); 
    xStockIlimitado.setAttribute('id','SI'+(parseFloat(i)+parseFloat(1)));
    xStockIlimitado.setAttribute('checked',ckStockIlimitado);
    xStockIlimitado.setAttribute('onclick',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SV')");

    var xStockWeb = document.createElement('checkbox');
    xStockWeb.setAttribute('Label','');
    xStockWeb.setAttribute("class","stockmin"); 
    xStockWeb.setAttribute('id','SW'+(parseFloat(i)+parseFloat(1)));
    xStockWeb.setAttribute('checked',ckStockWeb);
    xStockWeb.setAttribute('onclick',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SV')");

    var xStockTPV = document.createElement('checkbox');
    xStockTPV.setAttribute('Label','');
    xStockTPV.setAttribute("class","stockmin"); 
    xStockTPV.setAttribute('id','ST'+(parseFloat(i)+parseFloat(1)));
    xStockTPV.setAttribute('checked',ckStockTPV);
    xStockTPV.setAttribute('onclick',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SV')");
    
    var xCostoUnitario = document.createElement('description');
    xCostoUnitario.setAttribute('value',formatDineroTotal(CostoUnitario));
    xCostoUnitario.setAttribute('readonly','true');
    xCostoUnitario.setAttribute('id','CP'+(parseFloat(i)+parseFloat(1)));

    var xIdProducto = document.createElement('textbox');
    xIdProducto.setAttribute('value',IdProducto);
    xIdProducto.setAttribute('id','IDP'+(parseFloat(i)+parseFloat(1)));
    xIdProducto.setAttribute('hidden','true');

    row0.setAttribute("tooltiptext",CodProducto+' '+NombreProducto);

    row0.setAttribute('id','ROW0-'+(parseFloat(i)+parseFloat(1)));
    row1.setAttribute('id','ROW1-'+(parseFloat(i)+parseFloat(1)));
    item.setAttribute('id','ITEM'+(parseFloat(i)+parseFloat(1)));
    var buttonimg = (viewbtnReponer)? '../../img/gpos_precios_guardar.png':'../../img/gpos_tpvciclica.png';
    var button0  = document.createElement('image');
    button0.setAttribute('hidden',viewbtnReponer);
    button0.setAttribute('id','Save0-'+(parseFloat(i)+parseFloat(1)));
    button0.setAttribute('src',buttonimg);
    button0.setAttribute('tooltiptext','Guardar Cambios');
    button0.setAttribute("class","btn"); 
    button0.setAttribute("onclick","SalvarCambios("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save0-"+(parseFloat(i)+parseFloat(1))+"')");

    row0.appendChild(item);
    row0.appendChild(xIdProducto);
    row0.appendChild(xNombreProducto);
    grid0.appendChild(row0);
    row1.appendChild(xUnidades);
    row1.appendChild(xStockTPV);
    row1.appendChild(xStockWeb);
    row1.appendChild(xStockIlimitado);

    row1.appendChild(xStockExhibicion );
    row1.appendChild(xStockMin );
    row1.appendChild(xStockExhibicionMin );
    row1.appendChild(button0);
    row1.appendChild(xStockExhibicionReponer);
    row1.appendChild(xStock);
    row1.appendChild(xStockUnid);

    grid1.appendChild(row1);

    //TOTAL LISTA
    document.getElementById("numLista").value = z;
}

function SalvarCambios(numfila,idproducto,idbutton){

    var savebutton = id(idbutton);
    var listalocal = id("listalocal").value;
    var tstockmim  = id('SM'+numfila);
    var tstockexh  = id('SEM'+numfila);
    var cstocktpv  = id('ST'+numfila);
    var cstockweb  = id('SW'+numfila);
    var cstockili  = id('SI'+numfila);
    var vstock      = parseFloat( id('STK'+numfila).value );
    var vstockunid  = id('STKUnid'+numfila).value;
    tstockexh.value = ( parseFloat( tstockexh.value ) > vstock )? vstock:parseFloat( tstockexh.value );
    var vstockvitrimax = ( parseFloat( tstockexh.value ) > vstock )? vstock:parseFloat( tstockexh.value );
    
    ocultar('Save0-'+numfila);
    ocultar('SER'+numfila);
    id('SET'+numfila).value = vstockvitrimax+' '+vstockunid;
    
    var z        = null;	    
    var xrequest = new XMLHttpRequest();
    var url      = "selexhibicion.php?modo=actualizarExhibicion";
    var datos    =
        "&SM="+tstockmim.value+
        "&SE="+vstockvitrimax+
        "&SEM="+tstockexh.value+
        "&ST="+cstocktpv.checked+
        "&SW="+cstockweb.checked+
        "&SI="+cstockili.checked+
        "&idproducto="+idproducto+
        "&listalocal="+listalocal;

    xrequest.open("GET",url+datos,false);

    try {
	xrequest.send(null);
        if( xrequest.responseText !=  idproducto )
	    alert("gPOS: Exhibición \n\n - No se registro los cambios.\n Para volver a intentarlo, refresque la búsqueda.")
    } catch(z){
	return;
    }
}

function RawfiltrarProductos(idfamilia,listalocal,idmarca,codigo,descripcion,iniciopagina,idlistarPV,FuncionProcesaLineaProductos,listarTodo,listarVitrina){

    var tex = "";
    var cr = "\n";
    var IdProducto,Descripcion,Marca,Color,Talla,NombreComercial,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,CostoOperativo,MUSubFamilia;
    var PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,NombreProducto,PrecioVenta,PVDE,PrecioEmpaque,esMenudeo,Empaque,PrecioDocena,PVDED;
    var PVDDescontado,PrecioVentaCorporativo,PVCDescontado;
    var node,t,i,z,restoPaginas,totalPaginas,numFilasPaginas,totalFilasProductos,xml,filasProductos;
    var pvBloq = 0;
    var pvcBloq = 0;
    var listaPaginas = id("listaPaginas");
    var textoPM = id("textoPMistaNPP");
    var actualizarNPV = id("actualizarNPV") ;

    var listarPV_MD = id("listarPV_MD");
    var listarPV_TPV = id("listarPV_TPV");
    var actualizarLPV = id("actualizarLPV");
    var eliminarLPV = id("eliminarLPV");

    var textoproductos = id("textoproductos");

    var listPag        = id('listaPaginas');
    var cmbPag         = id('comboPaginas');

    IGV                = cImpuestoGral;

    if(idlistarPV == 1)
	descripcion='todos';

    var url = "selexhibicion.php?modo=mostrarProductosAlmacen&idfamilia=" + idfamilia
        + "&idmarca=" + idmarca
        + "&codigo=" + codigo
        + "&idlistarPV=" + idlistarPV
        + "&descripcion=" + descripcion
        + "&listalocal=" + listalocal
        + "&listarTodo=" + listarTodo
        + "&listarVitrina=" + listarVitrina;

    var obj = new XMLHttpRequest();

    obj.open("GET",url,false);
    obj.send(null);

    if (!obj.responseXML)
        return alert('gPOS: Precios TPV \n\n - El servidor esta ocupado.');

    xml = obj.responseXML.documentElement; 
    totalFilasProductos = xml.childNodes.length;

    //RESUMEN TOTALES
 
    //DECLAREAMOS PARA INCIAR CON LA VISTA
    filasProductos  = totalFilasProductos;
    numFilasPaginas = 10; 
    i               = iniciopagina;
    z               = 0;


    //PAGINAR
    listaPaginas.setAttribute('collapsed',true);

    //TOTALES RESUMEN
    textoproductos.setAttribute('label', totalFilasProductos+' productos.');

    
    if( totalFilasProductos >= numFilasPaginas ){

	listPag.removeChild(cmbPag);
     
	listaPaginas.setAttribute('collapsed',false);

	restoPaginas = totalFilasProductos % numFilasPaginas; 
	totalPaginas = (totalFilasProductos - restoPaginas) / numFilasPaginas;

	if (restoPaginas > 0)
	    totalPaginas++;
	
	iniComboPaginas(totalPaginas,numFilasPaginas,totalFilasProductos);
	filasProductos=parseFloat(iniciopagina) + parseFloat(numFilasPaginas); //tope pagina

    }

    //LISTA DE PRODUCTOS PAGINADO
    for (i; i< filasProductos; i++) {
        z++;
	node = xml.childNodes[i];
        if (node){
            t = 0;

            IdProducto  	= node.childNodes[t++].firstChild.nodeValue;
            Descripcion		= node.childNodes[t++].firstChild.nodeValue;
            Marca 		= node.childNodes[t++].firstChild.nodeValue;
            Color 	        = node.childNodes[t++].firstChild.nodeValue;
            Talla 		= node.childNodes[t++].firstChild.nodeValue;
            NombreComercial	= node.childNodes[t++].firstChild.nodeValue;
            StockMin 	        = node.childNodes[t++].firstChild.nodeValue;
            CostoUnitario	= node.childNodes[t++].firstChild.nodeValue;
	    CostoUnitario	= Math.round(CostoUnitario*100)/100;
            Unidades 	        = node.childNodes[t++].firstChild.nodeValue;
	    UnidadMedida        = node.childNodes[t++].firstChild.nodeValue;
	    esMenudeo           = node.childNodes[t++].firstChild.nodeValue;
	    Empaque             = node.childNodes[t++].firstChild.nodeValue;
            StockIlimitado      = node.childNodes[t++].firstChild.nodeValue;
            Disponible          = node.childNodes[t++].firstChild.nodeValue;
            DisponibleOnline    = node.childNodes[t++].firstChild.nodeValue;
            UnidadesVitrina     = node.childNodes[t++].firstChild.nodeValue;
            StockVitrina        = node.childNodes[t++].firstChild.nodeValue;
            StockVitrinaDevol   = node.childNodes[t++].firstChild.nodeValue;
            NombreProducto      = Descripcion+' '+Marca+' '+Color+' '+Talla+' '+NombreComercial;

            

	    FuncionProcesaLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,i,z,pvBloq,pvcBloq,UnidadMedida,esMenudeo,Empaque,StockIlimitado,Disponible,DisponibleOnline,UnidadesVitrina,StockVitrina,StockVitrinaDevol);
	    pvcBloq = 0;
	    pvBloq = 0;
        }

    }

    //***************
}

function esOnlineBusquedas(){
    if ( id("buscar-servidor").getAttribute("checked") == "true")
        return false;
    else 
        return true;
}

function esVitrinaBusquedas(){
    return ;
}

function filtrarProductos(iniciopagina){

    if (isNaN(iniciopagina)==true)
	iniciopagina=id("iniciopagina").value; 

    VaciarProductosAlamcen();
    id("iniciopagina").value=iniciopagina;

    var idfamilia   = id("idfamilia").value;
    var listalocal  = id("listalocal").value;
    var idmarca     = id("idmarca").value;
    var codigo      = id("codigo").value;
    var descripcion = id("descripcion").value;
    var idlistarPV  = id("idlistarPV").value;
    var listarTodo  = (esOnlineBusquedas())? 1:0;
    var listarVitrina = id("buscar-vitrina").value;

    RawfiltrarProductos(idfamilia,listalocal,idmarca,codigo,descripcion,iniciopagina,idlistarPV,AddLineaProductos,listarTodo,listarVitrina);

}

function limpiarListadoProductos(){
    var url = "selexhibicion.php?modo=mostrarProductosExhibicion";
    location.href = url;
} 

function soloCodigoBuscar(evt,texto){
    texto = texto.replace(/'/g, '');
    texto = texto.replace(/"/g, '');
    texto = texto.replace(/</g, '');
    texto = texto.replace(/>/g, '');
    texto = texto.replace(/,/g, '');
    texto = texto.replace(/;/g, '');
    texto = texto.replace(/=/g, '');
    texto = texto.replace(/ /g, '');
    document.getElementById("codigo").value=texto;

}

function soloDescripcionBuscar(evt,texto){
    texto = texto.replace(/'/g, '');
    texto = texto.replace(/"/g, '');
    texto = texto.replace(/</g, '');
    texto = texto.replace(/>/g, '');
    texto = texto.replace(/,/g, '');
    texto = texto.replace(/;/g, '');
    texto = texto.replace(/=/g, '');
    document.getElementById("descripcion").value=texto;
}



function cancelar(){
    location.href='selexhibicion.php?modo=mostrarPedidos';
}

function buttonSave(fila,GridId,IdProducto){
    var xelemento;
    var tstockmim  = id('SM'+fila);
    var tstockexh  = id('SEM'+fila);
    var vstock     = parseInt( id('STK'+fila).value );
    
    tstockmim.value = ( tstockmim.value == '')? 0:tstockmim.value;
    tstockexh.value = ( tstockexh.value == '')? 0:tstockexh.value;
    tstockexh.value = ( parseInt( tstockexh.value ) > vstock )? 0 : parseInt( tstockexh.value );
    
    if (GridId == 'SV')
	xelemento = id('Save0-'+fila);
    if( xelemento )    
        if( xelemento.hidden==true )    
            xelemento.setAttribute('hidden',false);
}

function ocultar(id){
    var elemento=document.getElementById(id);
    if(elemento.hidden==true){
        elemento.setAttribute('hidden',false);
	filtrarProductos();
    }else{
        elemento.setAttribute('hidden',true);
    }
}

function formatDineroPrecio(numero) {
    
    var num = new Number(numero);
    num = num.toString();
    
    if(isNaN(num)) num = "0";
    
    num = Math.round(num*100)/100;
    //num = Math.round(num*10)/10;
    //more  alert(num);
    var sign = (num == (num = Math.abs(num)));
    num = num.toFixed(2);
    var num = new Number(numero);
    num = num.toString().replace(/\$|\,/g,'');
    
    if(isNaN(num)) num = "0";
    
    var sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    var cents = num%100;
    num = Math.floor(num/100).toString();
    
    if(cents<10) cents = "0" + cents;
    
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+','+ num.substring(num.length-(4*i+3));
    
    return (((sign)?'':'-') + num + '.' + cents);
    return (((sign)?'':'-') + num );   
}

//**************** Sync Exhibicion ******

var ckTexElementActivo = function() { 
    if( id( "descripcion").getAttribute("focused") ) return true; else return false; 
}

//setTimeout("Demon_syncExhibicion()",29999);//MENSAJES
function Demon_syncExhibicion(){
    if ( !ckTexElementActivo() ) syncExhibicion(); //Sync Modulos
    setTimeout("Demon_syncExhibicion()",19999); //Recursivo
}

function syncExhibicion(){
    if (!AjaxSyncDemon )
        AjaxSyncDemon = new XMLHttpRequest();	

    var url = "../../services.php?modo=getsyncTPV";
    AjaxSyncDemon.open("POST",url,true);
    AjaxSyncDemon.onreadystatechange = RececepcionSyncExhibicion;
    AjaxSyncDemon.send(null);
}

function RececepcionSyncExhibicion(){

    if (AjaxSyncDemon.readyState==4) {

	if (AjaxSyncDemon)
            if (! ( AjaxSyncDemon.status=="200" ) ) return;
        
	//Si responden, es que estamos online, por tanto "hay respuesta"
	// y borramos el acumulativo de peticiones sin respuesta. 
	//alert(AjaxSyncDemon.responseText);
	
	var rawtext = AjaxSyncDemon.responseText.split(':');
	if( rawtext[0] != '')
	    alert('TPV:\n Servidor Ocupado \n\n'+ rawtext[0]);
        
	if( !(rawtext[1]) || rawtext[1] == '') {
	    parent.location.href='../../logout.php'
	    return; //cierra sesión
	}
	    
	//0~0~0~0~0~0~0~1~0
	//Preventa~Proforma~ProformaOline~Stock~Cliente~Promocion~Mensaje~Caja~MProducto
	//Procesar
	var xsync = rawtext[1].split('~');
	//esSyncPreventa       = ( xsync[0] == 1 );
	//esSyncProforma       = ( xsync[1] == 1 );
	//esSyncProOnline      = ( xsync[2] == 1 );
	//esSyncStock          = ( xsync[3] == 1 );
	//esSyncClientes       = ( xsync[4] == 1 );
	//esSyncPromociones    = ( xsync[5] == 1 ); 
	//esSyncMensajes       = ( xsync[6] == 1 );
	//esSyncCaja           = ( xsync[7] == 1 );
	//esSyncMProducto      = ( xsync[8] == 1 );
	if( xsync[3] == 1 )
            setTimeout("filtrarProductos()",800);//Lanza la funcion
    }
}


