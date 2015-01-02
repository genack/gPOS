var po_servidorocupadoseries =" Servidor ocupado, inténtelo mas tarde. \n\n   - Añadiendo N/S al carrito - "
function xid(nombre) { return document.getElementById(nombre) };

window.onload = function() {
    xid("numerodeserie").focus();
}

//var n     = 0;
var nsAdd = false;
var fila  = 0;

function ltrim(s) {
        return s.replace(/^\s+/, "");
}

function rtrim(s) {
        return s.replace(/\s+$/, "");
}

function trim(s) {
        return rtrim(ltrim(s));
}
function limpiar_caja(){
        var ns   = xid("numerodeserie");
        ns.value = "";
        ns.focus();
}

function limpiar_cajackbox(){
        var ns   = xid("ckserie");
        ns.value = "";
        ns.focus();
}
function acciones(unidades,idproducto,validarSeries){

    var radio_group = xid("radio_group");

    switch(radio_group.selectedItem.label)
    {
    case "Agregar": 

        var lista         = xid("lista");
        var ns            = xid("numerodeserie");
        var numeros_serie = document.getElementsByTagName('listcell');
        var nserie        = trim(ns.value);

        if( n == unidades && !nsAdd)
            return alert("gPOS: \n\n La lista esta completa");

        if( n > unidades && !nsAdd )
            return alert('La lista tiene exedentes');

        if(trim(ns.value)=="") 
	    return;
	
        for(var i=0;i<lista.itemCount;i++ ){
	    
            if( numeros_serie[i*2+1].attributes.getNamedItem('label').nodeValue == nserie )
            {
                alert("gPOS: \n\n El Número de Serie " +nserie+" ya existe en la lista");
                var elemento = lista.getItemAtIndex(i);
                lista.ensureElementIsVisible(elemento);
                lista.selectItem(elemento);
                limpiar_caja();
                return;
            }	    
        }

        var validar   = ( validarSeries == 1 )? "validarCompraSerie":"validarSerie";
        var url       = "../../services.php?modo="+validar+
                      "&ns="+nserie+
                      "&idlocal="+cIdLocal+
                      "&idproducto="+idproducto;

        var xrequest  = new XMLHttpRequest();
        xrequest.open("GET",url,false);
        xrequest.send(null);
        var resultado = xrequest.responseText;
	//alert(resultado);

        resultado = parseInt(resultado);

        if(resultado==1){
            alert("gPOS: \n\n El Numero de Serie " +nserie+" ya esta registrado");
            return limpiar_caja();
        }


        var row = document.createElement('listitem');

        var cell_item = document.createElement('listcell');
        cell_item.setAttribute('label',lista.itemCount+1);

        var cell_ns = document.createElement('listcell');
        cell_ns.setAttribute('label',ns.value);
        cell_ns.setAttribute('name','ns');

        row.appendChild(cell_item);
        row.appendChild(cell_ns);

        n++;
        lista.appendChild(row);
        lista.ensureElementIsVisible(row);
        lista.clearSelection();
        limpiar_caja();

        break
	
    case "Quitar":
        var lista = xid("lista");
        var ns = xid("numerodeserie");
        var numeros_serie  = document.getElementsByTagName('listcell');
        var nserie = trim(ns.value);
        if(trim(ns.value)=="") return;
        for(var i=0;i<lista.itemCount;i++ ){
            if(numeros_serie[i*2+1].attributes.getNamedItem('label').nodeValue==nserie)
            {
                lista.removeItemAt(i);
                actualizarTotalNS();
                n--;
                limpiar_caja();
                for(var j=0;j<lista.itemCount;j++ ){
                    numeros_serie[j*2].attributes.getNamedItem('label').nodeValue=j+1;
                }
                return;
            }	    
        }
        alert("gPOS: \n\n No se puede quitar el numero de serie "+nserie+ " . Porque no se encuentra en el carrito");
        break
            case "Buscar": 
            var lista = xid("lista");
        var ns = xid("numerodeserie");
        var numeros_serie  = document.getElementsByTagName('listcell');
        var nserie = trim(ns.value);
        if(trim(ns.value)=="") return;
        for(var i=0;i<lista.itemCount;i++ ){
            if(numeros_serie[i*2+1].attributes.getNamedItem('label').nodeValue==nserie)
            {
                var elemento = lista.getItemAtIndex(i);
                lista.ensureElementIsVisible(elemento);
                lista.selectItem(elemento);
                limpiar_caja();
                return;
            }	    
        }
        lista.clearSelection();
        lista.ensureIndexIsVisible(lista.itemCount-1);
        alert("gPOS: \n\n No se encuentra");
        limpiar_caja();
        break
        default :
    }
    actualizarTotalNS();
}

function selcKBoxSerie(){

    var radio_group = xid("radio_group");

    switch(radio_group.selectedItem.label)
    {
     case "Buscar": 

        var lista          = xid("lista");
        var ns             = xid("ckserie");
        var numeros_serie  = document.getElementsByTagName('listitem');
        var nserie         = trim(ns.value);
        if(trim(ns.value)=="") return;
        for(var i=0;i<lista.itemCount;i++ ){
            if(numeros_serie[i].attributes.getNamedItem('label').nodeValue==nserie)
            {
                var elemento = lista.getItemAtIndex(i);
                lista.ensureElementIsVisible(elemento);
                lista.selectItem(elemento);

		selSeriesKardexcarrito(elemento.getAttribute('label'),elemento,true);
                limpiar_cajackbox();
		
                return;
            }	    
        }
        lista.clearSelection();
        lista.ensureIndexIsVisible(lista.itemCount-1);
        alert("gPOS: \n\n No se encuentra NS: "+nserie);
        limpiar_cajackbox();
        break
        default :
    }
    actualizarTotalNS();
}


function quitarns(){
    var lista=xid('lista');
    var ini =lista.itemCount;
    lista.removeItemAt(lista.selectedIndex);
    var fin = lista.itemCount;
    if(ini!=fin) 
        n=n-1;
    var numeros_serie  = document.getElementsByTagName('listcell');
    for(var i=0;i<lista.itemCount;i++ ){
        numeros_serie[i*2].attributes.getNamedItem('label').nodeValue=i+1;			  
    }
    actualizarTotalNS();
}

function actualizar_carrito(id,unidades,modo,idpedidodet,fila,trasAlta){

    unidades = parseInt(unidades);

    switch(modo){
    case "visualizarseriebuy" :
        var resto=unidades-n;

        if ( n<unidades )
            return alert("gPOS: \n\n Falta Ingresar "+ resto + " Números de Serie." );
	
        var cadena   = cadenaNS();
        var garantia = xid("fec_garantia").value;
        var xrequest = new XMLHttpRequest();
        var url      = "selcomprar.php?id="+id+"&modo=actualizarSeriesCarritoCompra&garantia="+garantia;

        xrequest.open("POST",url,false);
        xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xrequest.send("numerosdeserie="+cadena);
	//alert(xrequest.responseText);
        if (!parseInt(xrequest.responseText) )
            alert("gPOS: \n\n"+ po_servidorocupadoseries);
	var loadcart = (trasAlta==1)? false:true;	

	terminar('Añadiendo N/S al carrito...',loadcart);
        break

    case "visualizarserieAgregaInventario" :
        var resto=unidades-n;

        if ( n<unidades )
            return alert("gPOS: \n\n Falta Ingresar "+ resto + " Números de Serie." );
	
        var cadena   = cadenaNS();
        var garantia = xid("fec_garantia").value;
	//Termina
	parent.setSeccionSeries(cadena,garantia);
        break

    case "visualizarseriescart" :
        var lista    = xid("lista");    
        var cadena   = cadenaNS();
        var garantia = xid("fec_garantia").value;
        var xrequest = new XMLHttpRequest();
        var url      = "selcomprar.php?id="+id+"&modo=actualizarSeriesCarritoNS&garantia="+garantia;

        xrequest.open("POST",url,false);
        xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xrequest.send("numerosdeserie="+cadena);
	//alert(xrequest.responseText);
        if (!parseInt(xrequest.responseText) )
            alert("gPOS: \n\n "+ po_servidorocupadoseries);	

	parent.web.actualizarCantidadSeries(id,lista.itemCount,fila);
	terminar('Añadiendo N/S al carrito...',false);
        break


    case "validarSeriesCompraxProducto" :

        var resto=unidades-n;
        if (n<unidades) 
            return alert("gPOS: \n\n Faltan Ingresar "+resto+ " Numeros de Serie" );

	var resto=n-unidades;
        if (n>unidades)
            return alert("gPOS: \n\n Faltan quitar "+resto+ " Numeros de Serie" );

        var garantia = xid("fec_garantia").value;
        var cadena = cadenaNS();
        var xrequest = new XMLHttpRequest();
        var url = "selcomprar.php?id="+id+
	          "&modo=actualizarSeriesCompra"+
	          "&unidades="+unidades+
	          "&operacionentrada="+cOpEntrada+
	          "&idpedidodet="+idpedidodet+
	          "&garantia="+garantia;

        xrequest.open("POST",url,false);
        xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xrequest.send("numerosdeserie="+cadena);
        if ( !parseInt(xrequest.responseText) )
	    return alert("gPOS: \n\n "+ po_servidorocupadoseries+ ' L285 -> '+xrequest.responseText);
	SalirNStoAlmacenBorrador();
        break


    case "mostrarSeriesAlmacen" :
        window.close();
        break
    case "mostrarSeriesAlmacenxProducto" :
        window.close();
        break
    }
}

function terminar(aviso,loadcart){
    var modo = (loadcart)? 'aAltaRapida':'hWebForm';
    var main = parent.getWebForm();
    main.setAttribute("src",'modulos/compras/progress.php?modo='+modo+'&aviso='+aviso);
}

function SalirNStoComprobante(){
    parent.viewComprobantes();
}

function SalirNStoAlmacen(){
    parent.xwebCollapsed(false,true);
}

function SalirNStoAlmacenCarrito(){
    parent.almacen_MuestraCarrito();
}

function SalirNStoKardex(){

    var boxdetkardex   = parent.xid("boxdetalleskardex");
    var webdetkardex   = parent.xid("webdetalleskardex");
    var boxextkardex   = parent.xid("boxexistencias");
    var boxexistencias = parent.xid("btnvolveralmacen");

    webdetkardex.setAttribute("src","about:blank");  
    boxextkardex.setAttribute("collapsed","false");  
    boxdetkardex.setAttribute("collapsed","true");  
    boxexistencias.setAttribute("collapsed","false");  
}

function SalirNStoAlmacenBorrador(){ parent.verNSDetalleAlmacenBorrador(false,"about:blank");}

function cadenaNS(){
    var lista = xid("lista");    
    var numserie_array = new Array();
    var celdas  = document.getElementsByTagName('listcell');
    for(var i=0;i<lista.itemCount;i++ ){
        numserie_array.push(celdas[i*2+1].attributes.getNamedItem('label').nodeValue);
    }
    if(lista.itemCount!=0)
        var cadena = numserie_array.join(";");
    else
        var cadena = "";
    return cadena;
}
function actualizarTotalNS(){
    var total = xid("totalNS");
    var lista = xid("lista");
    total.label = lista.getRowCount();
}
function selSeriesKardexcarrito(xdato,thisck,xdonde){

    var vckValue  = thisck.getAttribute('checked');
    var vydonde   = ( vckValue != 'false' )? false:true;
    var vckValue  = ( xdonde )? vydonde:vckValue;	
    var vxdonde   = ( vckValue )? true:false;

    //textbox
    if(xdonde) 
	thisck.setAttribute('checked',vxdonde);

    //Add serie
    if(vckValue)
	alSeries.push(xdato);

    //Del serie
    if(!vckValue){
	for (var k in alSeries)
	{
	    if( alSeries[k] == xdato)
		alSeries.splice(k,1);
 	}
    }

    var nSeries = alSeries.length
    var cSeries = alSeries.toString();
    xid("totalSelNS").setAttribute('label',nSeries);
    parent.loadSeriesCarritoAlmacen(cIdPedidoDet,nSeries,cSeries);
}

function setcKBoxSerie(){

    var arrSeries = new Array();
    arrSeries.push(parent.aSeries[cIdPedidoDet]);
    //arrSeries = xdato.split(',');
    var cdnSeries  = arrSeries.toString();
    var arrSeries  = cdnSeries.split(',');

    for(var i = 0; i<arrSeries.length; i++)
    {
        xid("ckserie").value = trim(arrSeries[i]);
	selcKBoxSerie();
    }
}

function soloAlfaNumericoSerie(e){
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "abcdefghijklmnopqrstuvwxyz0123456789-";
    especiales = [8, 13];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1 && !tecla_especial){
        return false;
    }
}