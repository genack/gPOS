function BuscarMarca(){
    var filtro = document.getElementById('buscamarca').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Marca');
    for(var i=0; i<theList.itemCount; i++){
        var texto2  = document.getElementsByTagName('listitem');
        var cadena =  new String(texto2[i].attributes.getNamedItem('label').nodeValue);
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(ns) != -1){
            theList.ensureIndexIsVisible(i);
            theList.selectedIndex=i;
            return;
        }
    }
            theList.clearSelection();    
}

function agnadirDirecto(){
    var theList=document.getElementById('Marca');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}
