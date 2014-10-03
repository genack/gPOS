var selseries = new Array();
function init(){
    var elemento=document.getElementById('filtro');
    elemento.focus();
}
function listarseries(series){
    var ll=series.split(",");
    var theList=document.getElementById('lista');
    for(i=0;i<ll.length;i++)
    {
        var row = document.createElement('listitem');
        row.setAttribute('type','checkbox');
        row.setAttribute('label',ll[i]);
        row.setAttribute('oncommand',"seleccionar('"+ll[i]+"')");
        theList.appendChild(row);		    
    }
}

function seleccionar(serie){

    var posBorrar=selseries.indexOf(serie);
    if(posBorrar == -1){
        var nn = validar();
        if(nn==1){
            var theList = document.getElementById("lista");
            var tam = theList.getRowCount();

            for(var i=0 ;i<tam; i++)			    
            {
                var serielista = theList.getItemAtIndex(i).getAttribute("label");
                if(serielista == serie)
                {	
                    theList.getItemAtIndex(i).setAttribute("checked","false");
                }

            }   
            alert("Seleccion Completa ");
            return false;		    
        }
        selseries.push(serie);
    }
    else{
        selseries.splice(posBorrar, 1);
    }
    document.getElementById('nsel').value=selseries.length;
    return true;

}

function filtrarserie(evento){
    var series = document.getElementById("listaserie").value;
    var filtro = document.getElementById("filtro").value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var ll=series.split(",");
    var seriesfiltradas = new Array();
    for(var i=0; i<ll.length; i++){
        var serielista = new String(ll[i]);
        serielista = serielista.toUpperCase();
        if(serielista.indexOf(ns) != -1){
            seriesfiltradas.push(ll[i]);   
            var theList=document.getElementById('lista');
            theList.ensureIndexIsVisible(i);
            if(serielista==ns && evento == 13){
                theList.selectedIndex=i;
                var elemento = theList.currentItem;
               var estado = seleccionar(ll[i]);
                if(elemento.checked){
                    elemento.checked=false;
                }
                else{
                    if(estado)
                        elemento.checked=true;
                }
                var box = document.getElementById("filtro");
                box.value="";
            }
        }
    }
}

function validar(){

    var cantventa = document.getElementById("cantidad").getAttribute("value");
    var nsel = selseries.length;
    if (cantventa==nsel){
        return 1;
    }
    return 0;
}


function aceptar(){

	var res = validar();
	if(res==0){
	    alert("Faltan selecionar");
	    return;
	}
	window.returnValue = selseries;
	window.close();
}

function cancelar(){
    window.close();

}
