var porcentajedescuento = 0;
var costototal = 0;
var cantidadtotal = 0;
var costoxcontenedor = 0;

window.onload = function() {
    document.getElementById("cantidad").focus();
}

function aceptar(id,manejaserie,fila,trasAlta){


    var plchck  = document.getElementById("pdListCheck").checked;
    var cantd   = document.getElementById("cantidad");
    var unids   = document.getElementById("unidades");
    var lote    = document.getElementById("lote");
    var fv      = document.getElementById("Desde");
    var cadlote = "";
    var cadfv   = "";
    var md      = 0;

    trasAlta    = (plchck)? 1:trasAlta;

    if(unids){ 
	if(unids.value > 0) 
	    md = 1;
    }
    
    if( cantd.value == 0 && md == 0)
        return alert("gPOS: \n\n Ingresa cantidad significativa de unidades.");

    if(lote)
    {
        valor = strim(lote.value);
        if(valor=="")
	    return alert("gPOS: \n\n Ingrese el - Lote Producción-");
	cadlote = lote.value;
    }

    if(fv)
    {
        valor = strim(fv.value);
        if(valor=="") 
	    return alert("gPOS: \n\n Ingrese la - Fecha Vencimiento -");
	cadfv   = fv.value;
	var fvence = cadfv.replace(/-/g,',');
	var f = new Date();
	var hoy = f.getFullYear()+","+(f.getMonth() + 1)+","+f.getDate();
	var compara = comparaFechas(hoy,fvence);

	if(compara >= 0) 
	    return alert("gPOS: \n\n           Fecha de vencimiento incorrecto");
    }

    var cantidad      = document.getElementById("cantidad");
    var unidades      = document.getElementById("unidades");
    var costounitario = document.getElementById("costo");
    var descuento     = document.getElementById("descuento");
    var importe       = document.getElementById("importe");
    
    var url = "selcomprar.php?id="+id+
	"&modo=agnadirCarritoDirecto"+
	"&lt="+cadlote+
	"&costo="+costounitario.value+
	"&fv="+cadfv+
	"&unidades="+cantidadtotal+
	"&dscto="+descuento.value+
	"&importe="+importe.value+
	"&pdscto="+porcentajedescuento;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);
    var resultado = parseInt(xrequest.responseText);
    if (!resultado )
    {
        alert(po_servidorocupado);	
	return terminar('Limpiando formulario...',false);
    }

    if(manejaserie==1)
	return getNumeroSeriesCompras(id,cantidadtotal,trasAlta);//Termina en N/S

    if(fila) 
	parent.web.actualizarCantidadCarrito(id,
					     cantidadtotal,
					     costounitario.value,
					     descuento.value,
					     fila);//set datos Carrito Compra
    var loadcart = (trasAlta==1)? false:true;	
    terminar('Añadiendo Producto al carrito...',loadcart);//Termina		
}

function setblockListado(check){
 
    var url = "selcomprar.php?modo=postCompraListado&check="+check;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    //alert(xrequest.responseText);
    var check = xrequest.responseText
    document.getElementById("pdListCheck").setAttribute("checked",check);
}

function getNumeroSeriesCompras(id,cantidadtotal,trasAlta){

    var main    = parent.getWebForm();
    var url  = "selcomprar.php?id="+id+
               "&modo=visualizarseriebuy"+
               "&trasAlta="+trasAlta+
               "&u="+cantidadtotal;

    main.setAttribute("src",url);  
    parent.xwebCollapsed(true);
}

function terminar(aviso,loadcart){
    var modo = (loadcart)? 'aAltaRapida':'hWebForm';
    var main = parent.getWebForm();
    main.setAttribute("src",'modulos/compras/progress.php?modo='+modo+'&aviso='+aviso);
}

function actualizarImporte(){
    var costo = document.getElementById("costo");
    var boxcostototal = document.getElementById("costototal");
    var descuento = document.getElementById("descuento");
    var boximporte = document.getElementById("importe");
    var importe = 0;
    costototal = cantidadtotal*costo.value;
    costototal = costototal.toFixed(2); 
    boxcostototal.value = costototal;
    importe = costototal - descuento.value;
    boximporte.value = importe.toFixed(2); 

}    
function mostrarCostoTotal(unidadesxcontenedor){
    var p = prompt("gPOS:\n\n Costo Total", costoxcontenedor);

    if(isNaN(p)||p==""||p.lastIndexOf(' ')>-1||parseFloat(p)<0)
    {
	alert("gPOS: \n\n Ingrese correctamente el valor del campo");
	return mostrarCostoTotal(unidadesxcontenedor);
    }

    costoxcontenedor = p;
    var costo = document.getElementById("costo");
    if(cantidadtotal>0){
	var nuevocosto = p/unidadesxcontenedor;
        costo.value = nuevocosto.toFixed(2); 
        costototal = (p/unidadesxcontenedor)*cantidadtotal;
    }
    actualizarImporte();
}
function mostrarPorcentajeDescuento(){

    var p = prompt("gPOS:\n\n Porcentaje de descuento (%)", porcentajedescuento);

    if(!p) return;

    if(isNaN(p)||p==""||p.lastIndexOf(' ')>-1||parseFloat(p)<0)
    {
	alert("gPOS: \n\n Ingrese correctamente el valor del campo");
	return mostrarPorcentajeDescuento();
    }

    porcentajedescuento = p;
    var descuento   = document.getElementById("descuento");
    descuento.value = Math.round((costototal*p/100)*100)/100;
    actualizarImporte();
}
function actualizarPorcentajeDescuento(){
    var descuento = document.getElementById("descuento");
    porcentajedescuento = Math.round((descuento.value*100/costototal)*10)/10;
}
function actualizarCantidad(UContenedor){

    var cantidad  = document.getElementById("cantidad");
    var unidades  = document.getElementById("unidades");
    var descuento = document.getElementById("descuento");

    if(unidades){
	if(unidades.value < UContenedor)
            cantidadtotal = cantidad.value*parseFloat(UContenedor)+unidades.value*1;
	else {
	    //alert("gPOS: \n\n La cantidad que ingreso es mayor a "+ UContenedor+" unid."); 
	    unidades.value=0;
	    cantidad.focus();
	}
    }
    else{
        cantidadtotal = cantidad.value;
    }
    actualizarImporte();
    actualizarPorcentajeDescuento()
}

function actualizarCostoTotal(unidadesxcontenedor){
    var costo = document.getElementById("costo");
    costoxcontenedor = unidadesxcontenedor*costo.value;
    costototal = cantidadtotal*costo.value;
}
function ltrim(s) {
    return s.replace(/^\s+/, "");
}

function rtrim(s) {
    return s.replace(/\s+$/, "");
}

function strim(s) {
    return rtrim(ltrim(s));
}

