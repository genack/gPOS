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

function agnadirDirecto(){
    var theList=document.getElementById('TipoServicio');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}
