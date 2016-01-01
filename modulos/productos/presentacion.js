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

function BuscarPresentacion(){
    var filtro = document.getElementById('buscapresentacion').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Color');
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
    document.getElementById('btnNuevoColor').setAttribute("collapsed", (theList.itemCount > 1) ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('Color');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function UsarNuevo() {
    var color, url;
    var idfamilia  = cIdFamiliaColor;			
    var nuevocolor = document.getElementById('buscapresentacion');
    if (nuevocolor){
        color = nuevocolor.value;
        //color = trim(color);
        color = trim(color);
    }
    if (!color || color == '') return;
    url = 'selmodelo.php';
    url = url +'?modo=nuevocolor';
    url = url + '&color=' + color;
    url = url + '&idfamilia=' + idfamilia;
    document.location.href = url;			
}

function ModificarColor() {
    var idfamilia  = cIdFamiliaColor;			
    var xcolor = document.getElementById('Color').selectedItem;
    if( ! xcolor ) return;
    var idcolor     = xcolor.value;
    var txtcolor    = xcolor.label;
    var newtxtcolor = '';
    if( newtxtcolor = prompt('gPOS:\n'+
			     ' Modifique  '+ctxtModelo+' :',
			     txtcolor) ) {

	if ( txtcolor == newtxtcolor || newtxtcolor == '' ) return;

	var url = 'selmodelo.php?';
	url = url + 'modo=modificacolor';
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&xid=' + idcolor;
	url = url + '&txt=' + newtxtcolor;
	document.location.href = url;
    }
}

function EliminarColor() {
    var idfamilia  = cIdFamiliaColor;			
    var xcolor = document.getElementById('Color').selectedItem;
    if( ! xcolor ) return;
    var idcolor     = xcolor.value;
    var txtcolor    = xcolor.label;
   if( confirm('gPOS:\n'+
		'       Desea eliminar '+ctxtModelo+':\n\n'+
		'                - '+txtcolor+' -') ) {
	var url = 'selmodelo.php?';
	url = url + 'modo=eliminacolor';
	url = url + '&idfamilia=' + idfamilia;
	url = url + '&xid=' + idcolor;
	url = url + '&txt=' + txtcolor;
	document.location.href = url;
    }
}

function loadfocus(){
	document.getElementById('buscapresentacion').focus();
}

function seleccionarModelo(xmodelo){
    if(!xmodelo) return;
    document.getElementById('buscapresentacion').value = xmodelo;
    document.getElementById('buscapresentacion').focus();
}