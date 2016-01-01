function BuscarTipoServicio(){
    var filtro = document.getElementById('buscatiposervicio').value;
    var ns     = new String(filtro);
    var xnuevo = document.getElementById('boxnuevo');
    ns = ns.toUpperCase();
    var theList=document.getElementById('TipoServicio');
    for(var i=0; i<theList.itemCount; i++){
        var texto2  = document.getElementsByTagName('listitem');
        var cadena =  new String(texto2[i].attributes.getNamedItem('label').nodeValue);
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(ns) != -1){
            theList.ensureIndexIsVisible(i);
            theList.selectedIndex=i;
	    xnuevo.setAttribute('collapsed','true');
            return;
        }
    }
            theList.clearSelection();    
            xnuevo.setAttribute('collapsed','false');
}

function loadfocus(){
    document.getElementById('buscatiposervicio').focus();
}

function agnadirDirecto(){
    var theList=document.getElementById('TipoServicio');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function soloAlfaNumerico(e){ 
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = ' abcdefghijklmnopqrstuvwxyz0123456789-';
    especiales = [8, 13, 9];
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

function UsarNuevo() {
    var talla, url;
    var nuevocolor = document.getElementById('buscatiposervicio');
    var essat      = document.getElementById('esSAT');

    if (nuevocolor)
        talla = nuevocolor.value;

    if (!talla || talla == '')
        return;
    url = 'seltiposervicio.php';
    url = url +'?modo=salvatiposervicio';
    url = url + '&tiposervicio=' + talla;
    url = url + '&essat=' + essat.checked;

    document.location.href = url;			
}

function EliminarTipoServicio() {

    var xtiposervicio = document.getElementById('TipoServicio').selectedItem;
    if( ! xtiposervicio ) return;
    var idtiposervicio     = xtiposervicio.value;
    var txttiposervicio    = xtiposervicio.label;
    if( confirm('gPOS:\n'+
		'       Desea eliminar servicio:\n'+
		'             - '+txttiposervicio+' -') ) {
	url = 'seltiposervicio.php';
	url = url + '?modo=eliminatiposervicio';
	url = url + '&txt=' + txttiposervicio;
	url = url + '&xid=' + idtiposervicio;
	document.location.href = url;				  			
    }
}

function ModificarTipoServicio() {

    var xtiposervicio = document.getElementById('TipoServicio').selectedItem;
    if( ! xtiposervicio ) return;
    var idtiposervicio     = xtiposervicio.value;
    var txttiposervicio    = xtiposervicio.label;
    var newtxttiposervicio = '';
    if( newtxttiposervicio = prompt('gPOS:\n'+
			     '       Modifique  servicio:',
			     txttiposervicio) ) {

	if ( txttiposervicio == newtxttiposervicio || newtxttiposervicio == '' ) return;

	url = 'seltiposervicio.php';
	url = url + '?modo=modificatiposervicio';
	url = url + '&txt=' + newtxttiposervicio;
	url = url + '&xid=' + idtiposervicio;
	document.location.href = url;				  			
    }
}

