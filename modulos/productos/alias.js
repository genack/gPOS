function ltrim(s) {
        return s.replace(/^\s+/, '');
}

function rtrim(s) {
        return s.replace(/\s+$/, '');
}

function trim(s) {
        return rtrim(ltrim(s));
}
function limpiarcadena(cadena){
    cad = new String(cadena);
    cad = cad.replace(/['$&\;"?/%^]/gi,'');
    return cad;
}

function BuscarAlias(){
    var filtro = document.getElementById('buscaalias').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('ProductoAlias');
    filas = theList.itemCount;
    for(var i=0;i<filas;i++){
        theList.removeItemAt(0);
    }
    if(ns==""){
        for(var i=0;i<filas;i++){
            theList.removeItemAt(0);
        }
        for(var i in fam){
            var row = document.createElement('listitem');
            row.setAttribute('label',fam[i]);
            row.setAttribute('value',i);
            theList.appendChild(row);	
        }
    }
    else
    {
        for(var i=0;i<filas;i++){
            theList.removeItemAt(0);
        }
        for(var i in fam){
            var cadena = new String(fam[i]);
            cadena = cadena.toUpperCase();
            if(cadena.indexOf(ns) != -1){
                var row = document.createElement('listitem');
                row.setAttribute('label',fam[i]);
                row.setAttribute('value',i);
                theList.appendChild(row);	
            }
            var elemento = theList.getItemAtIndex(0);
            theList.selectItem(elemento);
        }
    }
   document.getElementById('btnNuevoAlias').setAttribute("collapsed", (theList.itemCount > 1) ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('ProductoAlias');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function loadfocus(){
    document.getElementById('buscaalias').focus();
}

function UsarNuevo() {

    var productoalias, url;
    var idfamilia = cIdFamiliaColor;			
    var id        = cId;
    var txtalias  = ctxtAlias;
    var nuevoproductoalias = document.getElementById('buscaalias');			
    if (nuevoproductoalias){
        productoalias = nuevoproductoalias.value;
        productoalias = trim(productoalias);
        productoalias = limpiarcadena(productoalias);
    }
    if (!productoalias || productoalias == '')
        return;
    
    url = 'selproductoalias.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=nuevoproductoalias';
    url = url + '&productoalias=' + productoalias;
    url = url + '&txtalias=' + txtalias;
    url = url + '&idfamilia=' + idfamilia;
    url = url + '&id=' + id;
    document.location.href = url;			
} 

function soloAlfaNumerico(e){ 
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = ' abcdefghijklmnopqrstuvwxyz0123456789-%';
    especiales = [8, 13, 9, 35, 36, 37, 39];
    tecla_especial = false
    for(var i in especiales){
        if(key == especiales[i]){
            tecla_especial = true;
            break;
        }
    }
    
    if(letras.indexOf(tecla)==-1) { 
        if(!tecla_especial){
            return false;
        }
    }
}

function ModificarAlias() {

    var xalias = document.getElementById('ProductoAlias').selectedItem;
    if( ! xalias ) return;
    var idalias     = xalias.value;
    var txtalias    = xalias.label;
    var newtxtalias = '';
    var idfamilia = cIdFamiliaColor;			
    var id        = cId;

    if( newtxtalias = prompt('gPOS:\n'+
			     '       Modifique '+ctxtAlias+':',
			     txtalias) ) {

	if ( txtalias == newtxtalias || newtxtalias == '' ) return;
	
	var url = 'selproductoalias.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=modificaalias';
	url = url + '&txt=' + newtxtalias;
	url = url + '&xid=' + idalias;
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&id=' + id;
	document.location.href = url;			
    }
} 

function EliminarAlias() {

    var xalias = document.getElementById('ProductoAlias').selectedItem;
    if( ! xalias ) return;
    var idalias     = xalias.value;
    var txtalias    = xalias.label;
    var idfamilia = cIdFamiliaColor;			
    var id        = cId;
    if( confirm('gPOS:\n'+
		'       Desea eliminar  '+ctxtAlias+':\n\n'+
		'                  - '+txtalias+' -') ) {
	var url = 'selproductoalias.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=eliminaalias';
	url = url + '&txt=' + txtalias;
	url = url + '&xid=' + idalias;
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&id=' + id;
	document.location.href = url;			
    }
} 

