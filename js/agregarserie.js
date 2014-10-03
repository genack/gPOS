function init(){
    var elemento=document.getElementById('serie');
    elemento.focus();
}
function agregar(cant){
    //var ll=ss.split(",");
    var listserie=document.getElementById('listserie');
    var ll=listserie.value.split(",");
    var elemento=document.getElementById('serie');
    var theList=document.getElementById('lista');
    var texto=elemento.value;
    var b=0;
    for(i=0;i<ll.length;i++)
    {
        if(texto==ll[i])
        {
            alert("El Numero de Serie "+texto+" ya esta registrada");
            elemento.value="";
            elemento.focus();
            return;	    
        }

    }

    if (theList.itemCount<cant)
    {
        if(texto.length!=0)
        {
            for(var i=0;i<theList.itemCount;i++ ){
                var texto2  = document.getElementsByTagName('listcell');
                if(texto2[i*2+1].attributes.getNamedItem('label').nodeValue==texto)
                {
                    b=1;
                    alert("El Numero de Serie " +texto+" ya existe en la lista");
                }	    
            }
            if (b==0){
                var row = document.createElement('listitem');
                var cell = document.createElement('listcell');
                cell.setAttribute('label',theList.itemCount+1);
                row.appendChild(cell);
                cell = document.createElement('listcell');
                cell.setAttribute('label',elemento.value);
                row.appendChild(cell);
                theList.appendChild(row);		    	
            }

        }
    }else{
        alert("La Lista de Numeros de Series esta completa");
    }

    elemento.value="";
    elemento.focus();
}
function eliminar(){
    var theList=document.getElementById('lista');
    var elemento=document.getElementById('serie');
    theList.removeItemAt(theList.selectedIndex);
    var texto2  = document.getElementsByTagName('listcell');
    for(var i=0;i<theList.itemCount;i++ ){
        texto2[i*2].attributes.getNamedItem('label').nodeValue=i+1;			  
    }
    elemento.focus();
}


function aceptar(){

    var numserie_array =new Array();
    var theList=document.getElementById('lista');
    var texto2  = document.getElementsByTagName('listcell');
    var fecha = document.getElementById('Desde');
    var elemento = document.getElementById('cantidad');
    var cant = parseInt(elemento.value);
    if (fecha.value==""){
        alert('Atención, falta ingresar la fecha de finalización de la Garantía' );
        return;

    }
    if (theList.itemCount<cant)
    {  
        var resto=cant-theList.itemCount
            alert("Faltan Ingresar "+ resto + " Numeros de Serie" );
    }else{

        for(var i=0;i<theList.itemCount;i++ ){
            numserie_array[i]=texto2[i*2+1].attributes.getNamedItem('label').nodeValue;
        }

        numserie_array.unshift(fecha.value);
        window.returnValue = numserie_array;
        window.close();
    }
}

function cancelar(){
    window.close();
}
