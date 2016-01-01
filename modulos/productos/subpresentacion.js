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

function BuscarSubPresentacion(){
    var filtro = document.getElementById('buscapresentacion').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Talla');
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
    document.getElementById('btnNuevaTalla').setAttribute("collapsed", (theList.itemCount > 1) ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('Talla');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function loadfocus(){
	document.getElementById('buscapresentacion').focus();
}

function UsarNuevo(IdTallaje) {
    var talla, url;
    var idfamilia  = cIdFamiliaTalla;			
    var nuevocolor = document.getElementById('buscapresentacion');			
    if (nuevocolor){
        talla = nuevocolor.value;
        talla = trim(talla);
        //talla = limpiarcadena(talla);
    }
    if (!talla || talla == '')
        return;

    url = 'selmodelo.php';
    url = url +'?modo=nuevatalla';
    url = url + '&talla=' + talla;
    url = url + '&IdTallaje=' + IdTallaje;
    url = url + '&idfamilia=' + idfamilia;
    document.location.href = url;
} 

function ModificarTalla() {
    var idfamilia  = cIdFamiliaTalla;			
    var xtalla = document.getElementById('Talla').selectedItem;
    if( ! xtalla ) return;
    var idtalla   = xtalla.value;
    var txttalla    = xtalla.label;
    var newtxttalla = '';
    if( newtxttalla = prompt('gPOS:\n'+
			     ' Modifique  '+ctxtDetalle+' :',
			     txttalla) ) {

	if ( txttalla == newtxttalla || newtxttalla == '' ) return;

	var url = 'selmodelo.php';
	url = url +'?modo=modificatalla';
	url = url + '&txt=' + newtxttalla;
	url = url + '&xid=' + idtalla;
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&IdTallaje=' + cIdTallaje;
	document.location.href = url;
    }
}

function EliminarTalla() {
    var idfamilia  = cIdFamiliaTalla;			
    var xtalla = document.getElementById('Talla').selectedItem;
    if( ! xtalla ) return;
    var idtalla     = xtalla.value;
    var txttalla    = xtalla.label;
   if( confirm('gPOS:\n'+
		'       Desea eliminar '+ctxtDetalle+':\n\n'+
		'                 - '+txttalla+' -') ) {
	var url = 'selmodelo.php';
	url = url +'?modo=eliminatalla';
	url = url + '&txt=' + txttalla;
	url = url + '&xid=' + idtalla;
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&IdTallaje=' + cIdTallaje;
	document.location.href = url;
    }
}

function seleccionarDetalle(xdet){
    if(!xdet) return;
    document.getElementById('buscapresentacion').value = xdet;
    document.getElementById('buscapresentacion').focus();
}