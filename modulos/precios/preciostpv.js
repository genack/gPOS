var id  = function(name) { return document.getElementById(name); }
var IGV = 0;
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

    var row1,row2,row3,row4,row0;
    var col=id("iniciopagina").value;
    col++;
    for (var i = 0; i < numLista; i++) { 
        row0 = id('ROW0-'+(col));
        row1 = id('ROW1-'+(col));
        row2 = id('ROW2-'+(col));
        row3 = id('ROW3-'+(col));
        row4 = id('ROW4-'+(col));
	grid0.removeChild(row0);
	grid1.removeChild(row1);
	grid2.removeChild(row2);
	grid3.removeChild(row3);
	grid4.removeChild(row4);
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
}

function actualizarNuevosPV(){
    if(confirm("gPOS:\n   Aplicar los Nuevos Precios en el Local Actual?")){
	var listalocal= id("listalocal").value;
	url = "../../services.php?modo=actualizarNuevosPV&listalocal="+listalocal;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText) 
	    limpiarListadoProductos();
	else
	    alert('gPOS: Precios TPV \n\n - El servidor esta ocupado');
    }
}
function actualizarAllNuevosPV(){
    if(confirm("gPOS:\n   Aplicar los Nuevos Precios en Todos los Locales ?")){

	url = "../../services.php?modo=actualizarAllNuevosPV";
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText) 
	    limpiarListadoProductos();
	else
	    alert('gPOS:  Precios TPV \n\n - El servidor esta ocupado');
    }
}

function eliminarNuevosPV(){
    if(confirm("gPOS:\n   Descartar lista de nuevos precios?")){
	var listalocal= id("listalocal").value;
 	url = "selprecios.php?modo=eliminarNuevosPV&"+listalocal;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText) 
	    alert('gPOS: Precios TPV \n\n - El servidor esta ocupado');
	else
	    limpiarListadoProductos();
    }
}


function listarPVMD(){
    var listalocal= id("listalocal").value;
    url = "selprecios.php?modo=listarNuevosPV&listalocal="+listalocal;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    if(xrequest.responseText){
	id("idlistarPV").value=1;
 	id("listfamilia").value = 1;
	id("idfamilia").value = 0;
	id("listmarca").value = 1;
	id("idmarca").value = 0;
 	id("codigo").value='';
	id("descripcion").value='';
	id("codigo").setAttribute('readonly','true');
	id("descripcion").setAttribute('readonly','true');
	//BOTONES
	id("listarPV_MD").setAttribute("collapsed","true");
	id("actualizarLPV").setAttribute("collapsed","false");
	if(id("actualizarAllLPV"))
	    id("actualizarAllLPV").setAttribute("collapsed","false");
	id("eliminarLPV").setAttribute("collapsed","false");
	filtrarProductos(); 
    }else{
 	id("listfamilia").value = 1;
	id("idfamilia").value = 0;
	id("listmarca").value = 1;
	id("idmarca").value = 0;
 	id("codigo").value='';
	id("descripcion").value='';
	filtrarProductos(); 
    }
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

function AddLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,i,z,pvBloq,pvcBloq,UnidadMedida,CostoOperativo,MUSubFamilia){

    var grid0= id("productos"); 
    var grid1= id("detalle_productos");
    var grid2= id("detalle_directo");
    var grid3= id("detalle_corporativo");
    var grid4= id("costo_productos");
    var row0 = document.createElement('row');
    var row1 = document.createElement('row');
    var row2 = document.createElement('row');
    var row3 = document.createElement('row');
    var row4 = document.createElement('row');
    var button0 = document.createElement('image');
    var button1 = document.createElement('image');
    var button2 = document.createElement('image');
    var button3 = document.createElement('image');
    var item = document.createElement('description');

    var aMUSF   = MUSubFamilia.split('~~');
    var MUSFVD  = aMUSF[0];
    var MUSFVC  = aMUSF[1];
    var Descuento = aMUSF[2];
    var DSTO    = (Descuento/100).round(2);
    var DSTO_G  = (cDescuentoGral/100).round(2);

    DSTO = (Descuento != 0)? parseFloat(DSTO):parseFloat(DSTO_G);
    var MU_Dir    = (MUSFVD == 0)? parseFloat(cMargenUtilidad):parseFloat(MUSFVD);
    var MU_Corp   = (MUSFVC == 0)? parseFloat(cMargenUtilidad):parseFloat(MUSFVC);

    item.setAttribute('value', parseFloat(i)+parseFloat(1));

    var xNombreProducto = document.createElement('description');
    xNombreProducto.textContent = ( NombreProducto.length > 65 )? NombreProducto.slice(0,65)+ '....':NombreProducto;
    if ( NombreProducto.length > 65 ) xNombreProducto.setAttribute("tooltiptext",NombreProducto);
    xNombreProducto.setAttribute('readonly','true');
    xNombreProducto.setAttribute('id','PRDTO'+(parseFloat(i)+parseFloat(1)));

    var xUnidades = document.createElement('description');
    xUnidades.setAttribute('value',Unidades+' '+UnidadMedida);
    xUnidades.setAttribute('readonly','true');
    xUnidades.setAttribute('style',"text-align:right");
    xUnidades.setAttribute('id','CD'+(parseFloat(i)+parseFloat(1)));

    var xStockMin = document.createElement('textbox');
    xStockMin.setAttribute('value',StockMin);
    xStockMin.setAttribute("style","text-align:right"); 
    xStockMin.setAttribute("class","stockmin"); 
    xStockMin.setAttribute('id','SM'+(parseFloat(i)+parseFloat(1)));
    xStockMin.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xStockMin.setAttribute('onfocus','this.select()');
    xStockMin.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SM')");

    var xCostoUnitario = document.createElement('description');
    xCostoUnitario.setAttribute('value',formatDineroTotal(CostoUnitario));
    xCostoUnitario.setAttribute('readonly','true');
    xCostoUnitario.setAttribute('id','CP'+(parseFloat(i)+parseFloat(1)));

    var xCOP = document.createElement('textbox');
    xCOP.setAttribute('value',formatDineroTotal(CostoOperativo));
    xCOP.setAttribute('label',formatDineroTotal(CostoOperativo));
    xCOP.setAttribute("class","costoop"); 
    xCOP.setAttribute('id','COP'+(parseFloat(i)+parseFloat(1)));
    xCOP.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xCOP.setAttribute('onfocus','this.select()');
    xCOP.setAttribute('onchange',"validarCOP("+(i+1)+","+IdProducto+","+CostoOperativo+");buttonSave("+(parseFloat(i)+parseFloat(1))+",'COP',"+IdProducto+")");
    if ( NombreProducto.length > 65 ) xCOP.setAttribute("tooltiptext",NombreProducto);

    var xDSTO = document.createElement('textbox');//DSTO
    xDSTO.setAttribute('value',DSTO);
    xDSTO.setAttribute('id','DSTO'+(parseFloat(i)+parseFloat(1)));
    xDSTO.setAttribute('collapsed','true');

    var xMUG = document.createElement('textbox');//MUG
    xMUG.setAttribute('value',MU_Dir);
    xMUG.setAttribute('label',MU_Corp);
    xMUG.setAttribute('id','MUG'+(parseFloat(i)+parseFloat(1)));
    xMUG.setAttribute('collapsed','true');


    var xMU_Directo = document.createElement('description');
    xMU_Directo.setAttribute('value',MU_Directo);
    xMU_Directo.setAttribute('label',MU_Directo);
    xMU_Directo.setAttribute('readonly','true');
    xMU_Directo.setAttribute('id','MUD'+(parseFloat(i)+parseFloat(1)));

    var xIGV_Directo = document.createElement('description');
    xIGV_Directo.setAttribute('value',IGV_Directo);
    xIGV_Directo.setAttribute('readonly','true');
    xIGV_Directo.setAttribute('id','IGVD'+(parseFloat(i)+parseFloat(1)));

    var xPVD = document.createElement('textbox');
    xPVD.setAttribute('value',formatDineroTotal(PVD));
    xPVD.setAttribute('label',formatDineroTotal(PVD));
    xPVD.setAttribute("class","precio"); 
    xPVD.setAttribute('id','PVD'+(parseFloat(i)+parseFloat(1)));
    xPVD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD.setAttribute('onblur','validarPVD('+(i+1)+')');
    xPVD.setAttribute('onfocus','this.select()');
    xPVD.setAttribute('oninput','actualizarCantidades('+(i+1)+')');
    xPVD.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVD',"+IdProducto+")");
    if ( NombreProducto.length > 65 ) xPVD.setAttribute("tooltiptext",NombreProducto);

    var xPVD_Descontado = document.createElement('textbox');
    xPVD_Descontado.setAttribute('value',formatDineroTotal(PVD_Descontado));
    xPVD_Descontado.setAttribute("class","precio"); 
    xPVD_Descontado.setAttribute('id','PVDD'+(parseFloat(i)+parseFloat(1)));
    xPVD_Descontado.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD_Descontado.setAttribute('onfocus','this.select()');
    xPVD_Descontado.setAttribute('onblur','validarPVDD('+(i+1)+')');
    xPVD_Descontado.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVD',"+IdProducto+")");
    if ( NombreProducto.length > 65 ) xPVD_Descontado.setAttribute("tooltiptext",NombreProducto);

    var xMU_Corporativo = document.createElement('description');
    xMU_Corporativo.setAttribute('value',MU_Corporativo);
    xMU_Corporativo.setAttribute('label',MU_Corporativo);
    xMU_Corporativo.setAttribute('readonly','true');
    xMU_Corporativo.setAttribute('id','MUC'+(parseFloat(i)+parseFloat(1)));

    var xIGV_Corporativo = document.createElement('description');
    xIGV_Corporativo.setAttribute('value',IGV_Corporativo);
    xIGV_Corporativo.setAttribute('id','IGVC'+(parseFloat(i)+parseFloat(1)));

    var xPVC = document.createElement('textbox');
    xPVC.setAttribute('value',formatDineroTotal(PVC));
    xPVC.setAttribute('label',formatDineroTotal(PVC));
    xPVC.setAttribute("class","precio"); 
    xPVC.setAttribute('id','PVC'+(parseFloat(i)+parseFloat(1)));
    xPVC.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC.setAttribute('onblur','validarPVC('+(i+1)+')');
    xPVC.setAttribute('oninput','actualizarCantidades('+(i+1)+')');
    xPVC.setAttribute('onfocus','this.select()');
    xPVC.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVC',"+IdProducto+")");
    if ( NombreProducto.length > 65 ) xPVC.setAttribute("tooltiptext",NombreProducto);

    var xPVC_Descontado = document.createElement('textbox');
    xPVC_Descontado.setAttribute('value',formatDineroTotal(PVC_Descontado));
    xPVC_Descontado.setAttribute("class","precio"); 
    xPVC_Descontado.setAttribute('id','PVCD'+(parseFloat(i)+parseFloat(1)));
    xPVC_Descontado.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC_Descontado.setAttribute('onblur','validarPVCD('+(i+1)+')');
    xPVC_Descontado.setAttribute('onfocus','this.select()');
    xPVC_Descontado.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVC',"+IdProducto+")");
    if ( NombreProducto.length > 65 ) xPVC_Descontado.setAttribute("tooltiptext",NombreProducto);

    var xIdProducto = document.createElement('textbox');
    xIdProducto.setAttribute('value',IdProducto);
    xIdProducto.setAttribute('id','IDP'+(parseFloat(i)+parseFloat(1)));

    button0.setAttribute('hidden','true');
    button3.setAttribute('hidden','true');



    if(pvBloq == 1){
	xPVD.setAttribute('readonly','true');
	xPVD_Descontado.setAttribute('readonly','true');
	button1.setAttribute('hidden','false');
	button1.setAttribute('src','../../img/gpos_precios_eliminar.png');
	button1.setAttribute("onclick","EliminarCambiosPV("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save1-"+(parseFloat(i)+parseFloat(1))+"','PVD')");
    } else {
	button1.setAttribute('hidden','true');
	button1.setAttribute('src','../../img/gpos_precios_guardar.png');
	button1.setAttribute("onclick","SalvarCambiosPV("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save1-"+(parseFloat(i)+parseFloat(1))+"','PVD')");
    }
    xIGV_Corporativo.setAttribute('readonly','true');
    if(pvcBloq == 1){
	xPVC.setAttribute('readonly','true');
	xPVC_Descontado.setAttribute('readonly','true');
	button2.setAttribute('hidden','false');
	button2.setAttribute('src','../../img/gpos_precios_eliminar.png');
	button2.setAttribute("onclick","EliminarCambiosPV("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save2-"+(parseFloat(i)+parseFloat(1))+"','PVC')");
    }else{
	button2.setAttribute('hidden','true');
	button2.setAttribute('src','../../img/gpos_precios_guardar.png');
	button2.setAttribute("onclick","SalvarCambiosPV("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save2-"+(parseFloat(i)+parseFloat(1))+"','PVC')");
    }
    xIdProducto.setAttribute('hidden','true');



    row0.setAttribute('id','ROW0-'+(parseFloat(i)+parseFloat(1)));
    row1.setAttribute('id','ROW1-'+(parseFloat(i)+parseFloat(1)));
    row2.setAttribute('id','ROW2-'+(parseFloat(i)+parseFloat(1)));
    row3.setAttribute('id','ROW3-'+(parseFloat(i)+parseFloat(1)));
    row4.setAttribute('id','ROW4-'+(parseFloat(i)+parseFloat(1)));
    item.setAttribute('id','ITEM'+(parseFloat(i)+parseFloat(1)));


    button0.setAttribute('id','Save0-'+(parseFloat(i)+parseFloat(1)));
    button0.setAttribute('src','../../img/gpos_precios_guardar.png');
    button0.setAttribute('title','Guardar Cambios');
    button0.setAttribute("onclick","SalvarCambiosSM("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save0-"+(parseFloat(i)+parseFloat(1))+"','SM')");

    button1.setAttribute('id','Save1-'+(parseFloat(i)+parseFloat(1)));
    button1.setAttribute('title','Guardar Cambios');

    button2.setAttribute('id','Save2-'+(parseFloat(i)+parseFloat(1)));
    button2.setAttribute('title','Guardar Cambios');

    button3.setAttribute('id','Save3-'+(parseFloat(i)+parseFloat(1)));
    button3.setAttribute('src','../../img/gpos_precios_guardar.png');
    button3.setAttribute('title','Guardar Cambios');
    button3.setAttribute("onclick","SalvarCambiosCOP("+(parseFloat(i)+parseFloat(1))+","+IdProducto+",'Save3-"+(parseFloat(i)+parseFloat(1))+"','COP')");

    row0.appendChild(item);
    row0.appendChild(xIdProducto);
    row0.appendChild(xNombreProducto);
    grid0.appendChild(row0);

    row1.appendChild(xUnidades);
    row1.appendChild(xStockMin);
    row1.appendChild(button0);
    grid1.appendChild(row1);

    row4.appendChild(xCostoUnitario);
    row4.appendChild(xCOP);
    row4.appendChild(button3);
    row4.appendChild(xDSTO);
    row4.appendChild(xMUG);
    grid4.appendChild(row4);

    row2.appendChild(xMU_Directo);
    row2.appendChild(xIGV_Directo);
    row2.appendChild(xPVD);
    row2.appendChild(xPVD_Descontado);
    row2.appendChild(button1);
    grid2.appendChild(row2);

    row3.appendChild(xMU_Corporativo);
    row3.appendChild(xIGV_Corporativo);
    row3.appendChild(xPVC);
    row3.appendChild(xPVC_Descontado);
    row3.appendChild(button2);
    grid3.appendChild(row3);


    //TOTAL LISTA
    document.getElementById("numLista").value = z;
}

function SalvarCambiosSM(numfila,idproducto,idbutton,MDS){
    var savebutton = id(idbutton);
    var listalocal= id("listalocal").value;
    var url,PV,PVD,SM,SMvalue;
    SM = id('SM'+numfila);
    if(SM && MDS == 'SM'){
	SM.setAttribute('readonly','true');
	SM.setAttribute('style','display:block;border:0px solid black;background-color: #BEBDBC;');
	ocultar('Save0-'+numfila);
	url = "selprecios.php?modo=actualizarStockMinimo&SM="+SM.value+"&idproducto="+idproducto+"&listalocal="+listalocal;
	SM.setAttribute('value','...');
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText){
	    SM.setAttribute('value',xrequest.responseText);
	} else {
	    alert("gPOS: Precios TPV \n\n - No se registro los cambios del Stock Mínimo.\n Para volver a intentarlo, refresque la búsqueda.")
	    SM.setAttribute('value',0);
	}
    }
}

function SalvarCambiosPV(numfila,idproducto,idbutton,MDS){
    var savebutton = id(idbutton);
    var url,PV,PVD;
    var listalocal= id("listalocal").value;
    
    if (MDS == 'PVD') { 
	PV = id('PVD'+numfila); 
	PVD = id('PVDD'+numfila); 
	savebutton.setAttribute('src','../../img/gpos_precios_eliminar.png');
	savebutton.setAttribute("onclick","EliminarCambiosPV("+numfila+","+idproducto+",'"+idbutton+"','PVD')");
    }
    if (MDS == 'PVC') { 
	PV = id('PVC'+numfila);
	PVD = id('PVCD'+numfila); 
	savebutton.setAttribute('src','../../img/gpos_precios_eliminar.png');
	savebutton.setAttribute("onclick","EliminarCambiosPV("+numfila+","+idproducto+",'"+idbutton+"','PVC')");
    }

    PV.setAttribute('readonly','true');
    PVD.setAttribute('readonly','true');
    PV.setAttribute('style','display:block;border:0px solid black;background-color: #BEBDBC;');
    PVD.setAttribute('style','display:block;border:0px solid black;background-color: #BEBDBC;');

    if (MDS == 'PVC' || MDS == 'PVD')
	url = "selprecios.php?modo=guardarPreciosVenta&MDS="+MDS+"&PV="+PV.value+"&PVD="+PVD.value+"&idproducto="+idproducto+"&listalocal="+listalocal;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    if(xrequest.responseText){
	alert("gPOS: Precios TPV \n\n - No se registro los cambios del Precio Venta Directo.\n Para volver a intentarlo, refresque la búsqueda.")
    }
    var MUD = id('MUD'+numfila);
    var MUC = id('MUC'+numfila);

    if(MUD.getAttribute("label") < 0)
	MUD.setAttribute("label",MUD.getAttribute("value"));

    if(MUC.getAttribute("label") < 0)
	MUC.setAttribute("label",MUC.getAttribute("value"));
}


function EliminarCambiosPV(numfila,idproducto,idbutton,MDS){
    if(confirm("gPOS:\n  Eliminar los nuevos precios del producto?")){

	if(id("idlistarPV").value == 1){

	    id("listarPV_MD").setAttribute("collapsed","false");
	    id("actualizarLPV").setAttribute("collapsed","true");
	    id("eliminarLPV").setAttribute("collapsed","true");
	    id("codigo").setAttribute('readonly','false');
	    id("descripcion").setAttribute('readonly','false');
	}
	var listalocal = id("listalocal").value;
	var savebutton = id(idbutton);
	var url,PV,PVD;
	if (MDS == 'PVD') { PV = id('PVD'+numfila); PVD = id('PVDD'+numfila);}
	if (MDS == 'PVC') { PV = id('PVC'+numfila);	PVD = id('PVCD'+numfila);}

	if (MDS == 'PVC' || MDS == 'PVD')
	    url = "selprecios.php?modo=eliminarCambiosPV&MDS="+MDS+"&PV="+PV.value+"&PVD="+PVD.value+"&idproducto="+idproducto+"&listalocal="+listalocal;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText){
	    var arrayPV = xrequest.responseText.split("~")
	    PV.value = Math.round(arrayPV[0]*100)/100;
	    PV.readOnly=false;
	    PVD.value = Math.round(arrayPV[1]*100)/100;
	    PVD.readOnly=false;
	    PV.setAttribute('style','black;background-color: #FFFFFF;');
	    PVD.setAttribute('style','black;background-color: #FFFFFF;');
	    ocultar(idbutton);
	    //filtrarProductos();
	}else{
	    alert("gPOS: Precios TPV \n\n - No se registro los cambios del Precio Venta.\n Para volver a intentarlo, refresque la búsqueda.")
	}
    }
}

function RawfiltrarProductos(idfamilia,listalocal,idmarca,codigo,descripcion,iniciopagina,idlistarPV,FuncionProcesaLineaProductos,listarTodo){

    var tex = "";
    var cr = "\n";
    var IdProducto,Descripcion,Marca,Color,Talla,NombreComercial,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,CostoOperativo,MUSubFamilia;
    var PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,NombreProducto,PrecioVenta;
    var PVDDescontado,PrecioVentaCorporativo,PVCDescontado;
    var node,t,i,z,restoPaginas,totalPaginas,numFilasPaginas,totalFilasProductos,xml,filasProductos;
    var pvBloq = 0;
    var pvcBloq = 0;
    var totalCP = 0;
    var totalPVD = 0;
    var totalMUD = 0;
    var totalIGVD = 0;
    var listaPaginas = id("listaPaginas");
    var textoPM = id("textoPMistaNPP");
    var actualizarNPV = id("actualizarNPV") ;

    var listarPV_MD = id("listarPV_MD");
    var listarPV_TPV = id("listarPV_TPV");
    var actualizarLPV = id("actualizarLPV");
    var eliminarLPV = id("eliminarLPV");

    var textoproductos = id("textoproductos");
    var totalCPP       = id("totalCPP");
    var totalPVP       = id("totalPVP");
    var totalMU        = id("totalMU");
    var totalIGV       = id("totalIGV");
    var listPag        = id('listaPaginas');
    var cmbPag         = id('comboPaginas');

    IGV                = cImpuestoGral;

    if(idlistarPV == 1)
	descripcion='todos';

    var url = "selprecios.php?modo=mostrarProductosAlmacen&idfamilia=" + idfamilia
        + "&idmarca=" + idmarca
        + "&codigo=" + codigo
        + "&idlistarPV=" + idlistarPV
        + "&descripcion=" + descripcion
        + "&listalocal=" + listalocal
        + "&listarTodo=" + listarTodo;

    var obj = new XMLHttpRequest();

    obj.open("GET",url,false);
    obj.send(null);

    if (!obj.responseXML)
        return alert('gPOS: Precios TPV \n\n - El servidor esta ocupado.');

    xml = obj.responseXML.documentElement; 
    totalFilasProductos = xml.childNodes.length;

    //RESUMEN TOTALES
    for (i=0; i< totalFilasProductos; i++) {
	node = xml.childNodes[i];
        if (node){
            t = 7;
            CostoUnitario	  = node.childNodes[t++].firstChild.nodeValue;
            Unidades 	          = node.childNodes[t++].firstChild.nodeValue;
            PrecioVenta	          = node.childNodes[t++].firstChild.nodeValue;
            PVDDescontado         = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaCorporativo= node.childNodes[t++].firstChild.nodeValue;
            PVCDescontado         = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaSource     = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaCorpSource = node.childNodes[t++].firstChild.nodeValue;
	    UnidadMedida          = node.childNodes[t++].firstChild.nodeValue;
	    CostoOperativo        = node.childNodes[t++].firstChild.nodeValue;
	    MUSubFamilia          = node.childNodes[t+2].firstChild.nodeValue;

	    //Precios Sources
	    if(PrecioVentaSource != 0){
		var srcPV     = PrecioVentaSource.split("~")
		PrecioVenta   = srcPV[0];
		PVDDescontado = srcPV[1];
	    }

	    if(PrecioVentaCorpSource != 0){
		var srcPV              = PrecioVentaCorpSource.split("~")
		PrecioVentaCorporativo = srcPV[0];
		PVCDescontado          = srcPV[1];
	    }

 	    PVD            = (PrecioVenta == 0 )? CostoUnitario*(1+cMargenUtilidad/100)*(1+IGV/100):PrecioVenta;
	    PVD_Descontado = (PrecioVenta == 0 )? PVD:PVDDescontado;
            PVD            = Math.round(PVD*100)/100;
            PVD_Descontado = Math.round(PVD_Descontado*100)/100;
            IGV_Directo    = Math.round(((PVD/(1+IGV/100))*IGV/100)*100)/100;
            //MU_Directo     = Math.round( ( (PVD - IGV_Directo) - CostoUnitario ) *100 )/100;
	    MU_Directo     = (PVD*100/(100+parseFloat(IGV)))-CostoUnitario-CostoOperativo;
	    MU_Directo     = (cImpuestoIncluido)? (((PVD-CostoOperativo)/(1+IGV/100))-CostoUnitario):MU_Directo;
	    PVC            = (PrecioVentaCorporativo == 0)? PVD:PrecioVentaCorporativo; 
	    PVC_Descontado = (PrecioVentaCorporativo == 0)? PVC:PVCDescontado;
            PVC            = Math.round(PVC*100)/100;
            PVC_Descontado = Math.round(PVC_Descontado*100)/100

	    // SUMA COSTOS Y PRECIOS
	    totalCP   = parseFloat(totalCP) + parseFloat(CostoUnitario*Unidades);
	    totalPVD  = parseFloat(totalPVD) + parseFloat(PVD*Unidades);
	    totalMUD  = parseFloat(totalMUD) + parseFloat(MU_Directo*Unidades);
	    totalIGVD = parseFloat(totalIGVD) + parseFloat(IGV_Directo*Unidades);
        }					
    }

    //DECLAREAMOS PARA INCIAR CON LA VISTA
    filasProductos  = totalFilasProductos;
    numFilasPaginas = 10; 
    i               = iniciopagina;
    z               = 0;


    //PAGINAR
    listaPaginas.setAttribute('collapsed',true);

    //TOTALES RESUMEN
    textoproductos.setAttribute('label', totalFilasProductos+' productos.');
    totalCPP.setAttribute('label',cMoneda[1]['S']+" "+
			  formatoMoneda( Math.round(totalCP*100)/100, 2, [',', "'", '.'] ));
    totalPVP.setAttribute('label',cMoneda[1]['S']+" "+ 
			  formatoMoneda( Math.round(totalPVD*100)/100, 2, [',', "'", '.'] ));
    totalMU.setAttribute('label',cMoneda[1]['S']+" "+ 
			 formatoMoneda( Math.round(totalMUD*100)/100, 2, [',', "'", '.'] ));
    totalIGV.setAttribute('label',cMoneda[1]['S']+" "+ 
			  formatoMoneda( Math.round(totalIGVD*100)/100, 2, [',', "'", '.'] ));

    
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
            PrecioVenta	        = node.childNodes[t++].firstChild.nodeValue;
            PVDDescontado       = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaCorporativo   = node.childNodes[t++].firstChild.nodeValue;
            PVCDescontado       = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaSource   = node.childNodes[t++].firstChild.nodeValue;
            PrecioVentaCorpSource    = node.childNodes[t++].firstChild.nodeValue;
	    UnidadMedida        = node.childNodes[t++].firstChild.nodeValue;
	    CostoOperativo      = node.childNodes[t++].firstChild.nodeValue;
	    MUSubFamilia        = node.childNodes[t+2].firstChild.nodeValue;
            NombreProducto      = Descripcion+' '+Marca+' '+Color+' '+Talla+' '+NombreComercial;

	    //CARGA LOS PRECIOS SIN CONFIRMAR****

	    if(PrecioVentaSource != 0){
		var srcPV = PrecioVentaSource.split("~")
		PrecioVenta = srcPV[0];
		PVDDescontado = srcPV[1];
		pvBloq = 1;
	    }

	    if(PrecioVentaCorpSource != 0){
		var srcPV = PrecioVentaCorpSource.split("~")
		PrecioVentaCorporativo = srcPV[0];
		PVCDescontado = srcPV[1];
		pvcBloq = 1;
	    }

	    //*****
	    
            if( PrecioVenta == 0 ){
		PVD = CostoUnitario*(1+cMargenUtilidad/100)*(1+IGV/100); 
		PVD_Descontado = PVD;
            }else{
		PVD = PrecioVenta;
		PVD_Descontado = PVDDescontado;
            }
            PVD = Math.round(PVD*100)/100;
            PVD_Descontado = Math.round(PVD_Descontado*100)/100;
            IGV_Directo = Math.round(((PVD/(1+IGV/100))*IGV/100)*100)/100;
	    IGV_Directo = parseFloat(IGV_Directo).toFixed(2);

            //MU_Directo = Math.round( ( (PVD - IGV_Directo) - CostoUnitario ) *100 )/100;
	    MU_Directo = (PVD*100/(100+parseFloat(IGV)))-CostoUnitario-CostoOperativo;
	    MU_Directo     = (cImpuestoIncluido)? (((PVD-CostoOperativo)/(1+IGV/100))-CostoUnitario):MU_Directo;
	    MU_Directo = parseFloat(MU_Directo).toFixed(2);

            if(PrecioVentaCorporativo == 0){
		PVC = PVD; 
		PVC_Descontado = PVC;
            }else{
		PVC = PrecioVentaCorporativo;
		PVC_Descontado = PVCDescontado;
            }
            PVC = Math.round(PVC*100)/100;
            PVC_Descontado = Math.round(PVC_Descontado*100)/100;
            IGV_Corporativo = Math.round(((PVC/(1+IGV/100))*IGV/100)*100)/100;
            //MU_Corporativo = Math.round( ( (PVC - IGV_Corporativo) - CostoUnitario ) *100 )/100;
	    IGV_Corporativo = parseFloat(IGV_Corporativo).toFixed(2);
	    MU_Corporativo  = (PVC*100/(100+parseFloat(IGV)))-CostoUnitario-CostoOperativo;
	    MU_Corporativo  = (cImpuestoIncluido)? (((PVC-CostoOperativo)/(1+IGV/100))-CostoUnitario):MU_Corporativo;
	    MU_Corporativo  = parseFloat(MU_Corporativo).toFixed(2);

	    FuncionProcesaLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,i,z,pvBloq,pvcBloq,UnidadMedida,CostoOperativo,MUSubFamilia);
	    pvcBloq = 0;
	    pvBloq = 0;
        }

    }

    //***************
    setTimeout(verificarVariacionPrecios(iniciopagina,numFilasPaginas,totalFilasProductos),400);
}

function esOnlineBusquedas(){
    if ( id("buscar-servidor").getAttribute("checked") == "true")
        return false;
    else 
        return true;
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

    RawfiltrarProductos(idfamilia,listalocal,idmarca,codigo,descripcion,iniciopagina,idlistarPV,AddLineaProductos,listarTodo);

}

function limpiarListadoProductos(){
    var url = "selprecios.php?modo=mostrarProductosPrecios";
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


function actualizarCantidades(fila){
    var CP   = document.getElementById("CP"+fila);
    var COP  = document.getElementById("COP"+fila);
    var MUD  = document.getElementById("MUD"+fila);
    var IGVD = document.getElementById("IGVD"+fila);
    var PVD  = document.getElementById("PVD"+fila);
    var MUC  = document.getElementById("MUC"+fila);
    var IGVC = document.getElementById("IGVC"+fila);
    var PVC  = document.getElementById("PVC"+fila);

    var Costo       = (cImpuestoIncluido)? 0: parseFloat(COP.value);
    var IGV_Directo = ((PVD.value/(1+IGV/100))*(IGV/100)).toFixed(2);
    IGVD.value      = parseFloat(IGV_Directo).toFixed(2);

    var MU_Directo  = ((PVD.value*100/(100+parseFloat(IGV)))-CP.value-COP.value).round(2);
    MU_Directo      = (cImpuestoIncluido)? (((parseFloat(PVD.value)-parseFloat(COP.value))/(1+IGV/100))-parseFloat(CP.value)):MU_Directo;
    MUD.value       = parseFloat(MU_Directo).toFixed(2);

    IGV_Corporativo = ((PVC.value/(1+IGV/100))*(IGV/100)).toFixed(2);
    IGVC.value      = parseFloat(IGV_Corporativo).toFixed(2);
    MU_Corporativo  = ((PVC.value*100/(100+parseFloat(IGV)))-CP.value-COP.value).round(2);
    MU_Corporativo  = (cImpuestoIncluido)? (((PVC.value-parseFloat(COP.value))/(1+IGV/100))-CP.value):MU_Corporativo;
    MUC.value   = parseFloat(MU_Corporativo).toFixed(2);

}

function cancelar(){
    location.href='selprecios.php?modo=mostrarPedidos';
}

function validarPVD(fila){
    var CP     = id("CP"+fila);
    var COP    = id("COP"+fila);
    var DSTO   = id("DSTO"+fila);
    var MUD    = id("MUD"+fila);
    var PVD    = id("PVD"+fila);
    var PVDD   = id("PVDD"+fila);
    var IGVD   = id("IGVD"+fila);

    PVD.value  = formatDineroTotal(PVD.value);
    PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));

    var idbutton = 'Save1-'+fila;
    var elemento=id(idbutton)

    if(MUD.value<0){
        alert("gPOS: Precios TPV \n\n - Margen de Utilidad es negativo. ");
        //var PrecioMinimo =  CP.value*(1+IGV/100);
        //PrecioMinimo =PrecioMinimo.toFixed(2);
        PVD.value  = PVD.getAttribute("label");
	MUD.value  = MUD.getAttribute("label");
        PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));
	COP.value  = formatDineroTotal(COP.getAttribute("label"));
	IGVD.value = (Math.round(((PVD.value/(1+IGV/100))*IGV/100)*100)/100);
        //actualizarCantidades(fila);
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
        //PVDD.value = PVD.value;
    }
}

function validarPVDD(fila){
    var CP   = id("CP"+fila);
    var DSTO = id("DSTO"+fila);
    var COP  = id("COP"+fila);
    var MUD  = id("MUD"+fila);
    var PVD = id("PVD"+fila);
    var PVDD = id("PVDD"+fila);

    var Costo = (cImpuestoIncluido)? 0:parseFloat(COP.value);
    var PrecioMinimo =  (parseFloat(CP.value)+Costo)*(1+IGV/100);
    PrecioMinimo     = (cImpuestoIncluido)? PrecioMinimo+parseFloat(COP.value):PrecioMinimo;
    PrecioMinimo = parseFloat(PrecioMinimo.round(2));
    PVDD.value   = formatDineroTotal(PVDD.value);

    if( parseFloat(PVDD.value) > parseFloat(PVD.value) || parseFloat(PVDD.value) < PrecioMinimo){
	var idbutton = 'Save1-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
        alert("gPOS: Precios TPV \n\n - Precio de Venta con Descuento no es valido.");
        PVDD.value = FormatPreciosTPV((PVD.value-MUD.value*DSTO.value).round(2));
    }
}

function validarPVC(fila){
    var CP     = id("CP"+fila);
    var DSTO   = id("DSTO"+fila);
    var MUC    = id("MUC"+fila);
    var PVC    = id("PVC"+fila);
    var PVCD   = id("PVCD"+fila);
    var IGVC   = id("IGVC"+fila);
    PVC.value  = formatDineroTotal(PVC.value);
    PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).round(2));
    if(MUC.value<0){
        alert("gPOS: Precios TPV \n\n - Margen de Utilidad  es negativo.");
        //var PrecioMinimo =  CP.value*(1+IGV/100);
        //PrecioMinimo =PrecioMinimo.toFixed(2);
        PVC.value  = PVC.getAttribute("label");
	MUC.value  = MUC.getAttribute("label");
        PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).round(2));
	IGVC.value = (Math.round(((PVC.value/(1+IGV/100))*IGV/100)*100)/100);
        //actualizarCantidades(fila);

	var idbutton = 'Save2-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
    }
}
function validarPVCD(fila){
    var CP    = id("CP"+fila);
    var DSTO  = id("DSTO"+fila);
    var COP   = id("COP"+fila);
    var PVC   = id("PVC"+fila);
    var PVCD  = id("PVCD"+fila);
    var MUC   = id("MUC"+fila);

    var Costo = (cImpuestoIncluido)? 0:parseFloat(COP.value);
    var PrecioMinimo =  (parseFloat(CP.value)+Costo)*(1+IGV/100);
    PrecioMinimo     = (cImpuestoIncluido)? PrecioMinimo+parseFloat(COP.value):PrecioMinimo;
    PrecioMinimo     = PrecioMinimo.round(2);
    PVCD.value = formatDineroTotal(PVCD.value);

    if(parseFloat(PVCD.value)>parseFloat(PVC.value) || parseFloat(PVCD.value) < parseFloat(PrecioMinimo)){
	var idbutton = 'Save2-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);

        alert("gPOS: Precios TPV \n\n - El Precio de Venta Corporativo con Descuento no es valido.");
        PVCD.value = FormatPreciosTPV((PVC.value-MUC.value*DSTO.value).round(2));
    }
}

function buttonSave(fila,GridId,IdProducto){
    var idbutton;
    if (GridId == 'SM')
	idbutton = 'Save0-'+fila;
    if (GridId == 'PVD')
	idbutton = 'Save1-'+fila;
    if (GridId == 'PVC')
	idbutton = 'Save2-'+fila;
    if (GridId == 'COP')
	idbutton = 'Save3-'+fila;

    if ( GridId == 'PVC' || GridId == 'PVD' ){
	var elemento = id(idbutton);

	elemento.setAttribute('src','../../img/gpos_precios_guardar.png');
	elemento.setAttribute("onclick","SalvarCambiosPV("+fila+","+IdProducto+",'"+idbutton+"','"+GridId+"')");
    }

    //AQUI VALIDAR CAMBIO
    var elemento=id(idbutton)
    if(elemento.hidden==true)    
        elemento.setAttribute('hidden',false);
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

function validarCOP(fila,IdProducto,xcop){
    var CP   = id("CP"+fila);
    var COP  = id("COP"+fila);
    var DSTO = id("DSTO"+fila);
    var MUG  = id("MUG"+fila);

    var MUD  = id("MUD"+fila);
    var PVD  = id("PVD"+fila);
    var PVDD = id("PVDD"+fila);

    var MUC  = id("MUC"+fila);
    var PVC  = id("PVC"+fila);
    var PVCD = id("PVCD"+fila);

    var IGVD = id("IGVD"+fila);
    var IGVC = id("IGVC"+fila);
    var yCOP = (COP.value)? COP.value:0;

    COP.value  = formatDineroTotal(yCOP);

    var xCP    = parseFloat(CP.value);
    var xCOP   = parseFloat(COP.value);

    var Costo  = (cImpuestoIncluido)? 0:xCOP;
    var xMUGD  = parseFloat(MUG.value);
    var xMUGC  = parseFloat(MUG.label);

    MUD.value  = ((xCP+Costo)*(xMUGD/100)).round(2);;
    var precio = parseFloat(CP.value)+Costo+parseFloat(MUD.value);
    var Imp    = (precio*IGV/100).round(2);
    precio     = (cImpuestoIncluido)? (precio+Imp+xCOP):(precio + Imp);
    precio     = precio.round(2);
    PVD.value  = FormatPreciosTPV(precio);
    PVDD.value = FormatPreciosTPV((PVD.value - MUD.value*DSTO.value).round(2));
    IGVD.value = Imp;

    //Corporativo
    MUC.value  = ((xCP+Costo)*xMUGC/100).round(2);
    var precioc= parseFloat(CP.value)+Costo+parseFloat(MUC.value);
    var Impc   = (precioc*IGV/100).round(2);
    precioc    = (cImpuestoIncluido)? (precioc + Impc + xCOP):(precioc + Impc);
    precioc    = (precioc).round(2);
    PVC.value  = FormatPreciosTPV(precioc);
    PVCD.value = FormatPreciosTPV((PVC.value - MUC.value*DSTO.value).round(2));
    IGVC.value = Impc;

    if(formatDineroTotal(xcop) != formatDineroTotal(COP.value)){
	buttonSave(fila,'PVD',IdProducto);
	buttonSave(fila,'PVC',IdProducto);
    }

    actualizarCantidades(fila);

}

function SalvarCambiosCOP(numfila,idproducto,idbutton,MDS){
    var savebutton = id(idbutton);
    var listalocal= id("listalocal").value;
    var url,PV,PVD,COP;
    COP = id('COP'+numfila);
    if(COP && MDS == 'COP'){
	COP.setAttribute('readonly','true');
	COP.setAttribute('style','display:block;border:0px solid black;background-color: #BEBDBC;');
	ocultar('Save3-'+numfila);
	url = "selprecios.php?modo=actualizarCostoOperativo&COP="+COP.value+"&idproducto="+idproducto+"&listalocal="+listalocal;
	//COP.setAttribute('value','...');
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);

	if(xrequest.responseText){
	    COP.setAttribute('value',xrequest.responseText);
	} else {
	    alert("gPOS: Precios TPV \n\n - No se registro los cambios del Stock Mínimo.\n Para volver a intentarlo, refresque la búsqueda.");
	    COP.setAttribute('value',0);
	}
    }
}

function verificarVariacionPrecios(iniciopagina,numFilasPaginas,totalFilasProductos){
    var restoPaginas   = totalFilasProductos % numFilasPaginas; 
    var filasProductos = parseFloat(iniciopagina)+parseFloat(numFilasPaginas);
    var fila,inipag    = parseFloat(iniciopagina)+1;

    for(fila = (inipag); fila <= filasProductos;fila++){
	if(!id("CP"+fila)) return;

	var CP   = id("CP"+fila);
	var COP  = id("COP"+fila);
	var DSTO = id("DSTO"+fila);
	var MUG  = id("MUG"+fila);
	
	var MUD  = id("MUD"+fila);
	var PVD  = id("PVD"+fila);
	var PVDD = id("PVDD"+fila);
	
	var MUC  = id("MUC"+fila);
	var PVC  = id("PVC"+fila);
	var PVCD = id("PVCD"+fila);
	
	var cImpuesto = parseFloat(cImpuestoGral);
	var COPE   = parseFloat(COP.value);
	xCOP       = (cImpuestoIncluido)? 0:COPE;
	var xDSTO  = parseFloat(DSTO.value);
	var yCP    = parseFloat(CP.value);

	var MU_Dir = parseFloat(MUG.value);
	var yMUD   = (yCP + xCOP)*(MU_Dir/100);
	var yPVD   = yCP + yMUD + xCOP;
	var yImp   = (yPVD*cImpuesto/100).round(2);
	yPVD       = (cImpuestoIncluido)? (yPVD + yImp + COPE):(yPVD + yImp);
	yPVD       = yPVD.round(2);
	yPVD       = FormatPreciosTPV(yPVD);
	var yPVDD  = (yPVD-(yMUD*xDSTO)).round(2);
	yPVDD      = FormatPreciosTPV(yPVDD);

	var MU_Corp= parseFloat(MUG.label);
	var yMUC   = (yCP + xCOP)*(MU_Corp/100);
	var yPVC   = yCP + yMUC + xCOP;
	var yImp   = (yPVC*cImpuesto/100).round(2);
	yPVC       = (cImpuestoIncluido)? (yPVC + yImp + COPE):(yPVC + yImp);
	yPVC       = yPVC.round(2);
	yPVC       = FormatPreciosTPV(yPVC);
	var yPVCD  = (yPVC-(yMUC*xDSTO)).round(2);
	yPVCD      = FormatPreciosTPV(yPVCD);

	if(PVD.value  != yPVD )
	    PVD.setAttribute("style","color:#C91918;text-align:right");
	if(PVDD.value != yPVDD)
	    PVDD.setAttribute("style","color:#C91918;text-align:right");
	if(PVC.value  != yPVC )
	    PVC.setAttribute("style","color:#C91918;text-align:right");
	if(PVCD.value != yPVCD)
	    PVCD.setAttribute("style","color:#C91918;text-align:right");
    }
}