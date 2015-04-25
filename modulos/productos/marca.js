function BuscarMarca(){
    var filtro = document.getElementById('buscamarca').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('Marca');
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
}

function agnadirDirecto(){
    var theList=document.getElementById('Marca');
    if(theList.selectedIndex == -1){
        return;
    }
    theList.onclick();
}
