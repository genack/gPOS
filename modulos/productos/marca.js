function BuscarMarca(){
    var filtro = document.getElementById('buscamarca').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('listboxMarca');
    filas = theList.itemCount;
    for(var i=0;i<filas;i++){
        theList.removeItemAt(0);
    }
    if(ns==""){
	document.getElementById('btnNuevaMarca').setAttribute("collapsed",true); 
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
    document.getElementById('btnNuevaMarca').setAttribute("collapsed", (theList.itemCount > 1) ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('listboxMarca');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function loadfocus(){
    document.getElementById('buscamarca').focus();
    BuscarMarca();
    agnadirDirecto();
}
 
function UsarNuevo() {
    var marca, url;
    var nuevomarca = document.getElementById('buscamarca');			

    if (nuevomarca)
        marca = nuevomarca.value;

    if (!marca || marca == '')
        return;
    url = 'selmarca.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=salvamarca';
    url = url + '&marca=' + marca;
    document.location.href = url;			
}

function EliminarMarca() {

    var xmarca = document.getElementById('listboxMarca').selectedItem;
    if( ! xmarca ) return;
    var idmarca     = xmarca.value;
    var txtmarca    = xmarca.label;
    if( confirm('gPOS:\n'+
		'       Desea eliminar la marca:\n\n'+
		'                 - '+txtmarca+' -') ) {
	var url = 'selmarca.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=eliminamarca';
	url = url + '&xid=' + idmarca;
	document.location.href = url;
    }
}

function ModificarMarca() {

    var xmarca = document.getElementById('listboxMarca').selectedItem;
    if( ! xmarca ) return;
    var idmarca     = xmarca.value;
    var txtmarca    = xmarca.label;
    var newtxtmarca = '';
    if( newtxtmarca = prompt('gPOS:\n'+
			     '       Modifique la marca:',
			     txtmarca) ) {

	if ( txtmarca == newtxtmarca || newtxtmarca == '' ) return;

	var url = 'selmarca.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=modificamarca';
	url = url + '&xid=' + idmarca;
	url = url + '&txt=' + newtxtmarca;
	document.location.href = url;
    }
}

function soloAlfaNumerico(e){ 
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = ' abcdefghijklmnñopqrstuvwxyz0123456789-.';
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
