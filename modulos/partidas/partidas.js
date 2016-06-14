function BuscarPartida(){
    var filtro = document.getElementById('buscapartida').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('listboxPartida');
    filas = theList.itemCount;
    for(var i=0;i<filas;i++){
        theList.removeItemAt(0);
    }
    if(ns==""){
	document.getElementById('btnNuevaPartida').setAttribute("collapsed",true); 
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
    document.getElementById('btnNuevaPartida').setAttribute("collapsed", (theList.itemCount > 1) ); 
}

function agnadirDirecto(){
    var theList=document.getElementById('listboxPartida');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function loadfocus(){
    document.getElementById('buscapartida').focus();
    BuscarPartida();
    agnadirDirecto();
}
 
function GuardarCreaPartida() {
    var partida, url;
    var nuevopartida = document.getElementById('txtPartida');
    var idlocal      = document.getElementById('listIdLocal');

    if (nuevopartida)
        partida = nuevopartida.value;

    if (!partida || partida == '')
        return;
    url = 'selpartidas.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=salvapartida';
    url = url + '&partida=' + partida;
    url = url + '&local=' + idlocal;
    url = url + '&cja=' + cja;
    url = url + '&xop=' + op;
    url = url + '&xidl=' + xlocal;
    document.location.href = url;

    parent.RegenPartidas(op);
}

function EliminarPartida() {

    var xpartida = document.getElementById('listboxPartida').selectedItem;
    if( ! xpartida ) return;
    var codpartida = xpartida.value;
    var txtpartida = trim(xpartida.label);

    if ( codpartida.indexOf('S') > -1 )
	return alert("gPOS:   Partidas Caja General \n\n  "+
                     "  - Las partidas del sistema no se permite eliminar");

    if( confirm('gPOS:   Partidas Caja \n\n'+
		'       Desea eliminar la partida:\n\n'+
		'                 - '+txtpartida+' -') ) {
	var url = 'selpartidas.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=eliminapartida';
	url = url + '&xcod=' + codpartida;
	url = url + '&txt=' + txtpartida;
	url = url + '&cja=' + cja;
	url = url + '&xop=' + op;
	url = url + '&xidl=' + xlocal;
	document.location.href = url;
	parent.RegenPartidas(op);
    }
}

function ModificarPartida() {

    var xpartida = document.getElementById('listboxPartida').selectedItem;
    if( ! xpartida ) return;
    var newtxtpartida = '';
    var codpartida = xpartida.value;
    var txtpartida = xpartida.label;

    if ( codpartida.indexOf('S') > -1 )
	return alert("gPOS:   Partidas Caja General \n\n  "+
                     "  - Las partidas del sistema no se permite modificar");

    if( newtxtpartida = prompt('gPOS:\n'+
			     '       Modifique la partida:',
			     txtpartida) ) {

	if(newtxtpartida == null) return;
	newtxtpartida = trim(newtxtpartida);

	if ( txtpartida == newtxtpartida || newtxtpartida == '' ) return;

	var url = 'selpartidas.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=modificapartida';
	url = url + '&xcod=' + codpartida;
	url = url + '&txt=' + newtxtpartida;
	url = url + '&txtold=' + txtpartida;
	url = url + '&cja=' + cja;
	url = url + '&xop=' + op;
	url = url + '&xidl=' + xlocal;
	document.location.href = url;
	parent.RegenPartidas(op);
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

function VerFormPartida(xval){
    if(xval) LimpiarFormPartida();

    var partida = document.getElementById('buscapartida').value;
    document.getElementById('txtPartida').value = trim(partida);

    document.getElementById('formPartida').setAttribute('collapsed',xval);
    document.getElementById('ListaPartidas').setAttribute('collapsed',!xval);

    document.getElementById('btnGuardaPartida').setAttribute('label','Guardar');
    document.getElementById('btnGuardaPartida').setAttribute('oncommand','GuardarCreaPartida()');
}

function CancelarCreaPartida(){
    LimpiarFormPartida();
    VerFormPartida(true);
}

function LimpiarFormPartida(){
    document.getElementById('txtPartida').value = "";
    document.getElementById('listIdLocal').value = 1;
    document.getElementById('titlePartida').label = 'Nueva Partida';
}

