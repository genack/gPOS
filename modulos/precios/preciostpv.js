var id = function(name) { return document.getElementById(name); }

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

function iniComboLocales(cadena){
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

function onFocusTextboxBusqueda(textboxid){
    textbox=document.getElementById(textboxid);
    if(textboxid == "codigo"){ 
	if( textbox.value == 'CB/Ref.' ) 
	    textbox.value=''; 
	else 
	    textbox.select();
    }

    if(textboxid == "descripcion"){
	if( textbox.value == 'Descripcion del Producto' ) 
	    textbox.value=''; 
	else 
	    textbox.select();
    }
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
 	id("codigo").value='CB/Ref.';
	id("descripcion").value='Descripcion del Producto';
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
 	id("codigo").value='CB/Ref.';
	id("descripcion").value='Descripcion del Producto';
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

function AddLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,i,z,pvBloq,pvcBloq,UnidadMedida){

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
    var item = document.createElement('description');

    item.setAttribute('value', parseFloat(i)+parseFloat(1));

    var xNombreProducto = document.createElement('description');
    xNombreProducto.setAttribute('value',NombreProducto);
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
    xStockMin.setAttribute('id','SM'+(parseFloat(i)+parseFloat(1)));
    xStockMin.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xStockMin.setAttribute('onfocus','this.select()');
    xStockMin.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'SM')");

    var xCostoUnitario = document.createElement('description');
    xCostoUnitario.setAttribute('value',formatDineroPrecio(CostoUnitario));
    xCostoUnitario.setAttribute('readonly','true');
    xCostoUnitario.setAttribute('id','CP'+(parseFloat(i)+parseFloat(1)));

    var xMU_Directo = document.createElement('description');
    xMU_Directo.setAttribute('value',formatDineroPrecio(MU_Directo));
    xMU_Directo.setAttribute('readonly','true');
    xMU_Directo.setAttribute('id','MUD'+(parseFloat(i)+parseFloat(1)));

    var xIGV_Directo = document.createElement('description');
    xIGV_Directo.setAttribute('value',IGV_Directo);
    xIGV_Directo.setAttribute('readonly','true');
    xIGV_Directo.setAttribute('id','IGVD'+(parseFloat(i)+parseFloat(1)));

    var xPVD = document.createElement('textbox');
    xPVD.setAttribute('value',formatDineroPrecio(PVD));
    xPVD.setAttribute("style","text-align:right"); 
    xPVD.setAttribute('id','PVD'+(parseFloat(i)+parseFloat(1)));
    xPVD.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD.setAttribute('onblur','validarPVD('+(i+1)+')');
    xPVD.setAttribute('onfocus','this.select()');
    xPVD.setAttribute('oninput','actualizarCantidades('+(i+1)+')');
    xPVD.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVD',"+IdProducto+")");

    var xPVD_Descontado = document.createElement('textbox');
    xPVD_Descontado.setAttribute('value',formatDineroPrecio(PVD_Descontado));
    xPVD_Descontado.setAttribute("style","text-align:right"); 
    xPVD_Descontado.setAttribute('id','PVDD'+(parseFloat(i)+parseFloat(1)));
    xPVD_Descontado.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVD_Descontado.setAttribute('onfocus','this.select()');
    xPVD_Descontado.setAttribute('onblur','validarPVDD('+(i+1)+')');
    xPVD_Descontado.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVD',"+IdProducto+")");

    var xMU_Corporativo = document.createElement('description');
    xMU_Corporativo.setAttribute('value',formatDineroPrecio(MU_Corporativo));
    xMU_Corporativo.setAttribute('readonly','true');
    xMU_Corporativo.setAttribute('id','MUC'+(parseFloat(i)+parseFloat(1)));

    var xIGV_Corporativo = document.createElement('description');
    xIGV_Corporativo.setAttribute('value',formatDineroPrecio(IGV_Corporativo));
    xIGV_Corporativo.setAttribute('id','IGVC'+(parseFloat(i)+parseFloat(1)));

    var xPVC = document.createElement('textbox');
    xPVC.setAttribute('value',formatDineroPrecio(PVC));
    xPVC.setAttribute("style","text-align:right"); 
    xPVC.setAttribute('id','PVC'+(parseFloat(i)+parseFloat(1)));
    xPVC.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC.setAttribute('onblur','validarPVC('+(i+1)+')');
    xPVC.setAttribute('oninput','actualizarCantidades('+(i+1)+')');
    xPVC.setAttribute('onfocus','this.select()');
    xPVC.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVC',"+IdProducto+")");

    var xPVC_Descontado = document.createElement('textbox');
    xPVC_Descontado.setAttribute('value',formatDineroPrecio(PVC_Descontado));
    xPVC_Descontado.setAttribute("style","text-align:right"); 
    xPVC_Descontado.setAttribute('id','PVCD'+(parseFloat(i)+parseFloat(1)));
    xPVC_Descontado.setAttribute('onkeypress','return soloNumeros(event,this.value)');
    xPVC_Descontado.setAttribute('onblur','validarPVCD('+(i+1)+')');
    xPVC_Descontado.setAttribute('onfocus','this.select()');
    xPVC_Descontado.setAttribute('onchange',"buttonSave("+(parseFloat(i)+parseFloat(1))+",'PVC',"+IdProducto+")");

    var xIdProducto = document.createElement('textbox');
    xIdProducto.setAttribute('value',IdProducto);
    xIdProducto.setAttribute('id','IDP'+(parseFloat(i)+parseFloat(1)));

    button0.setAttribute('hidden','true');




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

    row0.appendChild(item);
    row0.appendChild(xIdProducto);
    row0.appendChild(xNombreProducto);
    grid0.appendChild(row0);

    row1.appendChild(xUnidades);
    row1.appendChild(xStockMin);
    row1.appendChild(button0);
    grid1.appendChild(row1);

    row4.appendChild(xCostoUnitario);
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

function getMargenUtilidad(){
    var url = "selprecios.php?modo=mostrarMargenUtilidad";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    return xrequest.responseText;
}

function getIGV(){
    var url = "selprecios.php?modo=mostrarIGV";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    return xrequest.responseText;
}

function RawfiltrarProductos(idfamilia,listalocal,idmarca,codigo,descripcion,iniciopagina,idlistarPV,FuncionProcesaLineaProductos,listarTodo){

    var tex = "";
    var cr = "\n";
    var IdProducto,Descripcion,Marca,Color,Talla,NombreComercial,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo;
    var PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,MargenUtilidad,IGV,NombreProducto,PrecioVenta;
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

    MargenUtilidad     = getMargenUtilidad();
    IGV                = getIGV();

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

 	    PVD            = (PrecioVenta == 0 )? CostoUnitario*(1+MargenUtilidad/100)*(1+IGV/100):PrecioVenta;
	    PVD_Descontado = (PrecioVenta == 0 )? PVD:PVDDescontado;
            PVD            = Math.round(PVD*100)/100;
            PVD_Descontado = Math.round(PVD_Descontado*100)/100;
            IGV_Directo    = Math.round(((PVD/(1+IGV/100))*IGV/100)*100)/100;
            MU_Directo     = Math.round( ( (PVD - IGV_Directo) - CostoUnitario ) *100 )/100;
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
    textoproductos.setAttribute('label','Total : '+
				totalFilasProductos+' productos.');
    totalCPP.setAttribute('label','Total CP: '+cMoneda[1]['S']+" "+
			  formatoMoneda( Math.round(totalCP*100)/100, 2, [',', "'", '.'] ));
    totalPVP.setAttribute('label','Total PVD: '+cMoneda[1]['S']+" "+ 
			  formatoMoneda( Math.round(totalPVD*100)/100, 2, [',', "'", '.'] ));
    totalMU.setAttribute('label','Total MU: '+cMoneda[1]['S']+" "+ 
			 formatoMoneda( Math.round(totalMUD*100)/100, 2, [',', "'", '.'] ));
    totalIGV.setAttribute('label','Total IGV: '+cMoneda[1]['S']+" "+ 
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
		PVD = CostoUnitario*(1+MargenUtilidad/100)*(1+IGV/100); 
		PVD_Descontado = PVD;
            }else{
		PVD = PrecioVenta;
		PVD_Descontado = PVDDescontado;
            }
            PVD = Math.round(PVD*100)/100;
            PVD_Descontado = Math.round(PVD_Descontado*100)/100;
            IGV_Directo = Math.round(((PVD/(1+IGV/100))*IGV/100)*100)/100;
            MU_Directo = Math.round( ( (PVD - IGV_Directo) - CostoUnitario ) *100 )/100;
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
            MU_Corporativo = Math.round( ( (PVC - IGV_Corporativo) - CostoUnitario ) *100 )/100;
	    FuncionProcesaLineaProductos(IdProducto,NombreProducto,Unidades,StockMin,CostoUnitario,MU_Directo,IGV_Directo,PVD,PVD_Descontado,MU_Corporativo,IGV_Corporativo,PVC,PVC_Descontado,i,z,pvBloq,pvcBloq,UnidadMedida);
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
    var CP = document.getElementById("CP"+fila);
    var MUD = document.getElementById("MUD"+fila);
    var IGVD = document.getElementById("IGVD"+fila);
    var PVD = document.getElementById("PVD"+fila);
    var MUC = document.getElementById("MUC"+fila);
    var IGVC = document.getElementById("IGVC"+fila);
    var PVC = document.getElementById("PVC"+fila);

    IGVD.value = ((PVD.value/(1+IGV/100))*(IGV/100)).toFixed(2);
    MUD.value = ((PVD.value - IGVD.value) - CP.value).toFixed(2);

    IGVC.value = ((PVC.value/(1+IGV/100))*(IGV/100)).toFixed(2);
    MUC.value = ((PVC.value - IGVC.value) - CP.value).toFixed(2);
}

function cancelar(){
    location.href='selprecios.php?modo=mostrarPedidos';
}
function validarPVD(fila){
    var CP = id("CP"+fila);
    var MUD = id("MUD"+fila);
    var PVD = id("PVD"+fila);
    var PVDD = id("PVDD"+fila);
    if(MUD.value<0){
        alert("gPOS: Precios TPV \n\n - Margen de Utilidad es negativo. ");
        var PrecioMinimo =  CP.value*(1+IGV/100);
        PrecioMinimo =PrecioMinimo.toFixed(2);
        PVD.value = PrecioMinimo;
        PVDD.value = PrecioMinimo;
        actualizarCantidades(fila);
	var idbutton = 'Save1-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
        PVDD.value = PVD.value;
    }
}
function validarPVDD(fila){
    var CP = id("CP"+fila);
    var PrecioMinimo =  CP.value*(1+IGV/100);
    PrecioMinimo = parseFloat(PrecioMinimo.toFixed(2));
    var PVD = id("PVD"+fila);
    var PVDD = id("PVDD"+fila);
    if( parseFloat(PVDD.value) > parseFloat(PVD.value) || parseFloat(PVDD.value) < PrecioMinimo){
	var idbutton = 'Save1-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
        alert("gPOS: Precios TPV \n\n - Precio de Venta con Descuento no es valido.");
        PVDD.value = PVD.value;
    }
}

function validarPVC(fila){
    var CP = id("CP"+fila);
    var MUC = id("MUC"+fila);
    var PVC = id("PVC"+fila);
    var PVCD = id("PVCD"+fila);
    if(MUC.value<0){
        alert("gPOS: Precios TPV \n\n - Margen de Utilidad  es negativo.");
        var PrecioMinimo =  CP.value*(1+IGV/100);
        PrecioMinimo =PrecioMinimo.toFixed(2);
        PVC.value = PrecioMinimo;
        PVCD.value = PrecioMinimo;
        actualizarCantidades(fila);
	var idbutton = 'Save2-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);
    }
}
function validarPVCD(fila){
    var CP = id("CP"+fila);
    var PrecioMinimo =  CP.value*(1+IGV/100);
    PrecioMinimo = PrecioMinimo.toFixed(2);
    var PVC = id("PVC"+fila);
    var PVCD = id("PVCD"+fila);
    if(parseFloat(PVCD.value)>parseFloat(PVC.value) || parseFloat(PVCD.value) < parseFloat(PrecioMinimo)){
	var idbutton = 'Save2-'+fila;
	var elemento=id(idbutton)
	if(elemento.hidden==false)    
            elemento.setAttribute('hidden',true);

        alert("gPOS: Precios TPV \n\n - El Precio de Venta Corporativo con Descuento no es valido.");
        PVCD.value = PVC.value;
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
