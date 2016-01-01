function BuscarContenedor(){
    var filtro = document.getElementById('buscacontenedor').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Contenedor');
    for(var i=0; i<theList.itemCount; i++){
        var texto2  = document.getElementsByTagName('listitem');
        var cadena =  new String(texto2[i].attributes.getNamedItem('label').nodeValue);
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(ns) != -1){
            theList.ensureIndexIsVisible(i);
            theList.selectedIndex=i;
	    document.getElementById('btnNuevContenedor').setAttribute("collapsed", true); 
            return;
        }
    }
    theList.clearSelection();    
    document.getElementById('btnNuevContenedor').setAttribute("collapsed", false ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('Contenedor');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}


function UsarNuevo() {
    
    var talla, url;
    var nuevocolor = document.getElementById('buscacontenedor');			
    if (nuevocolor)
        talla = nuevocolor.value;
    if (!talla || talla == '')
        return;
    
    url = 'selcontenedor.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=salvacontenedor';
    url = url + '&contenedor=' + talla;
    document.location.href = url			
}

function ModificarContenedor() {

    var xcontenedor = document.getElementById('Contenedor').selectedItem;
    if( ! xcontenedor ) return;
    var idcontenedor     = xcontenedor.value;
    var txtcontenedor    = xcontenedor.label;
    var newtxtcontenedor = '';
    if( newtxtcontenedor = prompt('gPOS:\n'+
			     '       Modifique la contenedor:',
			     txtcontenedor) ) {

	if ( txtcontenedor == newtxtcontenedor || newtxtcontenedor == '' ) return;

	var url = 'selcontenedor.php';
	url = url +'?';
	url = url + 'modo=modificacontenedor';
	url = url + '&xid=' + idcontenedor;
	url = url + '&txt=' + newtxtcontenedor;
	document.location.href = url;
    }
}

function EliminarContenedor() {

    var xcontenedor = document.getElementById('Contenedor').selectedItem;
    if( ! xcontenedor ) return;
    var idcontenedor     = xcontenedor.value;
    var txtcontenedor    = xcontenedor.label;
    if( confirm('gPOS:\n'+
		'       Desea eliminar el empaque:\n\n'+
		'                 - '+txtcontenedor+' -') ) {

	var url = 'selcontenedor.php';
	url = url +'?';
	url = url + 'modo=eliminarcontenedor';
	url = url + '&xid=' + idcontenedor;
	url = url + '&txt=' + txtcontenedor;
	document.location.href = url;
    }
}

function soloAlfaNumerico(e){ 
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = 'abcdefghijklmnopqrstuvwxyz-';
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

		
        
function loadfocus(){
    document.getElementById('buscacontenedor').focus();
 
   if (cContendorLoad !=''){
	BuscarContenedor();
	agnadirDirecto();
    }
}
