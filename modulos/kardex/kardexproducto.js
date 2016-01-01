var cExistencias   = "";
var cCostopromedio = "";
var cCostototal    = "";

var lista = new Array();

function xid(nombre) { return document.getElementById(nombre) };

function buscarMovimientos(){

    limpiar();

    var grid,xrequest,xitem,xclass;
    var desde = xid("FechaDesde").value;
    var hasta = xid("FechaHasta").value;
    var id    = xid("IdProducto").value;
    var xope  = xid("filtroOperacion").value;
    var xmov  = xid("filtroMovimiento").value;
    var	url   = "selkardex.php?modo=kdxMovimientosProducto&"+
                "idproducto="+id+
                "&xlocal="+idlocal+
	        "&desde="+desde+
	        "&hasta="+hasta+
	        "&xmov="+xmov+
	        "&xope="+xope;

    xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    xres     = xrequest.responseText;
    //alert(xrequest.responseText);
    if(xres=="") return;
    lista    = new Array();
    lista    = xres.split(";");
    grid     = xid("contenedor");
    xitem     = lista.length;

    for(var i = 0; i<lista.length; i++){

        var cadenafila = lista[i];
        var fila       = cadenafila.split(',');
	var detalle    = fila[7];

        var row       = document.createElement('row');
        var item      = document.createElement('description');
        var textbox1  = document.createElement('description');
        var textbox2  = document.createElement('description');
        var textbox3  = document.createElement('description');
        var textbox4  = document.createElement('description');
        var textbox5  = document.createElement('description');
        var textbox6  = document.createElement('description');
        var textbox7  = document.createElement('description');
        var textbox8  = document.createElement('description');
        var textbox9  = document.createElement('description');
        var textbox10 = document.createElement('description');

        item.setAttribute('value',xitem+'.');
        item.setAttribute('style','text-align:center');

        textbox1.setAttribute('value',fila[0]);

        textbox2.setAttribute('value',fila[9]);

        textbox3.setAttribute('value',fila[1]);
        textbox3.setAttribute('style','font-weight:bold;');

        textbox9.setAttribute('value',fila[2]);

        textbox10.setAttribute('value',detalle);

        textbox4.setAttribute('value',fila[3]+' '+cUnidad);
        textbox4.setAttribute('style','text-align:right;font-weight:bold;');

        textbox5.setAttribute('value',fila[4]);
        textbox5.setAttribute('style','text-align:right');

        textbox6.setAttribute('value',fila[5]);
        textbox6.setAttribute('style','text-align:right');

        textbox7.setAttribute('value',fila[8]+' '+cUnidad);
        textbox7.setAttribute('style','text-align:right;font-weight:bold;');

        textbox8.setAttribute('value',fila[6]);
        textbox8.setAttribute('style','text-align:center');

	xclass = (i%2)?'parrowkardex':'imparrowkardex';  
        row.setAttribute('class',xclass);
        row.setAttribute('onclick','selrowkardex(this)');
        row.setAttribute('id',i);

        row.appendChild(item);
        row.appendChild(textbox1);
        row.appendChild(textbox3);
        row.appendChild(textbox9);
        row.appendChild(textbox10);
        row.appendChild(textbox2);
        row.appendChild(textbox4);
        row.appendChild(textbox5);
        row.appendChild(textbox6);
        row.appendChild(textbox7);
        row.appendChild(textbox8);
        grid.appendChild(row);
	xitem--;
    }
 }

function imprimir(){

    var existenc = cExistencias;
    var cpromedio= cCostopromedio;
    var ctotal   = cCostototal;
    var producto = cProducto;
    var unidad   = cUnidad;
    var desde    = xid("FechaDesde").value;
    var hasta    = xid("FechaHasta").value;
    var idprod   = xid("IdProducto").value;
    var xope     = xid("filtroOperacion").value;
    var xmov     = xid("filtroMovimiento").value;
    var	url      = "../fpdf/imprimir_movimientosproducto.php?xid="+idprod+
                   "&xproduct="+producto+
                   "&xlocal="+idlocal+
	           "&desde="+desde+
	           "&hasta="+hasta+
	           "&xmov="+xmov+
	           "&xope="+xope+
	           "&unidad="+unidad+
	           "&exist="+existenc+
	           "&cpromedio="+cpromedio+
	           "&ctotal="+ctotal;
    location.href=url;
}
function buscarMovimientosExistencias(){

    var	url = "selkardex.php"+
              "?modo=kdxInventarioAlmacen"+
              "&xlocal="+idlocal+
	      "&idproducto="+idproducto;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;
    //alert(xrequest.responseText);

    if(xres=="") return;
    //alert("No hay existencias");

    var arreglo = xres.split("~");

    var total_existencia = 0;
    var total_costo      = 0;
    var lineas           = 0;
    var costolinea       = 0;
    var grid             = document.getElementById("contenedorexistencias");

    for(var i = 0; i<arreglo.length; i=i+7)
      {
          lineas++;
	  
          var item = document.createElement('label');
          item.setAttribute('value',lineas);
          item.setAttribute('style','text-align:center;font-weight:bold;');
          item.setAttribute('class','xtext');
          var textbox1 = document.createElement('label');
          textbox1.setAttribute('value',formatDineroTotal(arreglo[i]));
          textbox1.setAttribute('readonly','true');
          textbox1.setAttribute('style','text-align:right');
          textbox1.setAttribute('id','costo_'+String(i+1));
          textbox1.setAttribute('class','xtext');
          costosunitarios.push(arreglo[i]);

          var textbox2 = document.createElement('label');
          textbox2.setAttribute('value',arreglo[i+1]+' '+cUnidad);
          textbox2.setAttribute('onkeypress',"if (event.which == 13) "+
				"actualizarcantidades("+String(i+1)+");");
          textbox2.setAttribute('id','existencias_'+String(i+1));
          textbox2.setAttribute('style','text-align:right');
          textbox2.setAttribute('class','xtext');

          total_existencia = total_existencia + parseInt(arreglo[i+1]);
          total_costo      = total_costo +arreglo[i]*arreglo[i+1];
          costolinea       = arreglo[i]*arreglo[i+1];

          var textbox3 = document.createElement('label');
          textbox3.setAttribute('value',formatDineroTotal(costolinea.toFixed(2)));
          textbox3.setAttribute('readonly','true');
          textbox3.setAttribute('id','costo_parcial'+String(i+1));
          textbox3.setAttribute('style','text-align:right');
          textbox3.setAttribute('class','xtext');

          var textbox4 = document.createElement('button');
	  var vSerie   = (cSerie)? false:true;
          textbox4.setAttribute('label','NS');
          textbox4.setAttribute('oncommand','verNSResumenKardex('+arreglo[i+1]+','+arreglo[i+2]+')');
          textbox4.setAttribute('collapsed',vSerie);
          textbox4.setAttribute('class','btn');

          var textbox5 = document.createElement('description');
	  var vLote    = (cLote)? false:true;
          textbox5.setAttribute('value',arreglo[i+3]);
          textbox5.setAttribute('collapsed',vLote);
          textbox5.setAttribute('class','xtext');

          var textbox6 = document.createElement('description');
	  var vFv      = (cFv)? false:true;
          textbox6.setAttribute('value',arreglo[i+4]);
	  textbox6.setAttribute('collapsed',vFv);
          textbox6.setAttribute('class','xtext');

	  var boton = document.createElement('caption');
	  boton.setAttribute('label', '');
	  
          var row = document.createElement('row');
          row.setAttribute('class','parrowkardex');
          row.appendChild(item);
          row.appendChild(textbox2);
          row.appendChild(textbox1);
          row.appendChild(textbox3);
          row.appendChild(textbox4);
          row.appendChild(textbox5);
          row.appendChild(textbox6);
          grid.appendChild(row);
      }

    var costo_unitario = total_costo/total_existencia;
    var unidresto      = (cMenudeo)? total_existencia%cUnidxemp:0; 
    var unidempaque    = (cMenudeo)? (total_existencia-unidresto)/cUnidxemp:0;
    var unidmenudeo    = (cMenudeo)? unidempaque+' '+cEmpaque+' '+unidresto:0;
    var existencias    = (cMenudeo)? unidmenudeo:total_existencia; 

    xid('total_existencias').setAttribute('label',existencias+' '+cUnidad+' '+cDetalle);
    xid('cart_existencias').setAttribute('value',total_existencia);
    xid('kdxExistencias').setAttribute('value',existencias+' '+cUnidad+' '+cDetalle);
    xid('total_costo').setAttribute('label',cMoneda[1]['S']+" "+total_costo.toFixed(2));
    xid('kdxCostoTotalPromedio').setAttribute('value',cMoneda[1]['S']+" "+total_costo.toFixed(2));
    xid('costo_unitario').setAttribute('label',cMoneda[1]['S']+" "+costo_unitario.toFixed(2));
    xid('kdxCostoPromedio').setAttribute('value',cMoneda[1]['S']+" "+costo_unitario.toFixed(2));

    cExistencias   = existencias+' '+cUnidad+' '+cDetalle;
    cCostopromedio = costo_unitario.toFixed(2);
    cCostototal    = total_costo.toFixed(2);
}


function buscarMovimientosExistenciasCarrito(){

    var	url = "selkardex.php"+
              "?modo=kdxInventarioAlmacen"+
              "&xlocal="+idlocal+
	      "&idproducto="+idproducto;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;
    //alert(xrequest.responseText);

    if(xres=="") return;
    //alert("No hay existencias");

    var arreglo = xres.split("~");

    var total_existencia = 0;
    var total_costo      = 0;
    var lineas           = 0;
    var costolinea       = 0;
    var grid             = document.getElementById("contenedorexistencias");

    for(var i = 0; i<arreglo.length; i=i+7)
      {
          lineas++;
	  
          var item = document.createElement('label');
          item.setAttribute('value',lineas);
          item.setAttribute('class','xtext');
          item.setAttribute('style','text-align:center;font-weight:bold;');
	  
          var costo = document.createElement('label');
          costo.setAttribute('value',formatDineroTotal(arreglo[i]));
          costo.setAttribute('readonly','true');
          costo.setAttribute('class','xtext');
          costosunitarios.push(arreglo[i]);
          costo.setAttribute('id','costo_'+arreglo[i+2]);
          costo.setAttribute('style','text-align:right');

          var xExistencias = document.createElement('label');
          xExistencias.setAttribute('value',arreglo[i+1]);

          xExistencias.setAttribute('id','existencias_'+arreglo[i+2]);
          xExistencias.setAttribute('style','text-align:right');
          xExistencias.setAttribute('class','xtext');
          total_existencia = total_existencia + parseInt(arreglo[i+1]);
          total_costo      = total_costo +arreglo[i]*arreglo[i+1];
          costolinea       = arreglo[i]*arreglo[i+1];

          var costoparcial = document.createElement('label');
          costoparcial.setAttribute('value',formatDinero(costolinea.toFixed(2)));
          costoparcial.setAttribute('readonly','true');
          costoparcial.setAttribute('class','xtext');
          costoparcial.setAttribute('id','costoparcial_'+arreglo[i+2]);
          costoparcial.setAttribute('style','text-align:right');

          var xSerie = document.createElement('button');
	  var vSerie = (cSerie)? false:true;
          xSerie.setAttribute('label','NS');
          xSerie.setAttribute('class','btn');
          xSerie.setAttribute('oncommand','seriesKardexCarrito('+arreglo[i+1]+','+arreglo[i+2]+')');
          xSerie.setAttribute('collapsed',vSerie);

          var xLote = document.createElement('label');
	  var vLote = (cLote)? false:true;
          xLote.setAttribute('value',arreglo[i+3]);
          xLote.setAttribute('class','xtext');
          xLote.setAttribute('collapsed',vLote);
          xLote.setAttribute('id','lote_'+arreglo[i+2]);

          var xVence = document.createElement('label');
	  var vFv    = (cFv)? false:true;
          xVence.setAttribute('value',arreglo[i+4]);
	  xVence.setAttribute('collapsed',vFv)
          xVence.setAttribute('class','xtext');;
          xVence.setAttribute('id','vence_'+arreglo[i+2]);
	  var esLoteFv = (cFv || cLote)? '"'+arreglo[i+3]+'/'+arreglo[i+4]+'"':false;

          var xCarrito = document.createElement('textbox');
          xCarrito.setAttribute('value',0);
          xCarrito.setAttribute('onblur','loadPedidoCarritoAlmacen('+arreglo[i+2]+',this.value,'+
                                                                    esLoteFv+')');
          xCarrito.setAttribute('style','text-align:right');
          xCarrito.setAttribute('size','5');
          if(cSerie) xCarrito.setAttribute('readonly',true);
	  xCarrito.setAttribute('onfocus','this.select()');
	  xCarrito.setAttribute('onkeypress','return soloNumerosEnteros(event,this.value)');
          xCarrito.setAttribute('id','carrito_'+arreglo[i+2]);

	  var boton = document.createElement('caption');
	  boton.setAttribute('label', '');
	  
          var row = document.createElement('row');
          row.setAttribute('value',arreglo[i+2]);
	  row.setAttribute('onfocus','click()');
          row.setAttribute('class','ldparrowkardex');
          row.appendChild(item);
          row.appendChild(xExistencias);
          row.appendChild(costo);
          row.appendChild(costoparcial);
          row.appendChild(xSerie);
          row.appendChild(xLote);
          row.appendChild(xVence);
          row.appendChild(xCarrito);
          grid.appendChild(row);

	  //arreglo[i+2]:IdPedido
	  //arreglo[i+1]:Existencias
	  aCantidad['xAlmacen_'+arreglo[i+2] ]=arreglo[i+1];
	  aCantidad['cAlmacen_'+arreglo[i+2] ]=0;//Existencias en Carrito
	  aPedidoDet.push(arreglo[i+2]);
      }
    var costo_unitario = Math.round((total_costo/total_existencia)*100)/100;
    var unidresto      = (cMenudeo)? total_existencia%cUnidxemp:0; 
    var unidempaque    = (cMenudeo)? (total_existencia-unidresto)/cUnidxemp:0;
    var unidmenudeo    = (cMenudeo)? unidempaque+' '+cEmpaque+' '+unidresto:0;
    var existencias    = (cMenudeo)? unidmenudeo:total_existencia; 
    
    xid('total_existencias').setAttribute('label',existencias+' '+cUnidad+' '+cDetalle);
    xid('cart_existencias').setAttribute('value',total_existencia);
    xid('total_costo').setAttribute('label',cMoneda[1]['S']+" "+total_costo.toFixed(2));
    xid('costo_unitario').setAttribute('label',cMoneda[1]['S']+" "+costo_unitario.toFixed(2));
    xid('cart_costopromedio').setAttribute('value',costo_unitario);
    //xid('CostoCarrito').setAttribute('value',cMoneda[1]['S']+" "+costo_unitario);
}
 
function cancelar(){ location.href='../../modalmacenes.php?Id='+xid("idalmacen").value; }


function salirKardexCarritoAlmacen(id,xid){
    parent.return_almacen(id);
    parent.ckActionCancel(id,xid);

    var main  = parent.getWebForm();
    var aviso = 'Limpiando Existencias Carrito...';
    var lurl  = 'modulos/compras/progress.php?modo=hWebForm&aviso='+aviso;
    main.setAttribute("src",lurl);  
    //setTimeout("parent.xwebCollapsed(false,true)",150);//MENSAJES
}

function salirKardexProducto(){

    var listado   = parent.id("listadoMovimiento");
    var resumen   = parent.id("resumenMovimiento");
    var busqueda  = parent.id("busquedaMovimiento");
    var boxkardex = parent.id("boxkardex");
    var webkardex = parent.id("webkardex");

    webkardex.setAttribute("src","about:blank");  
    listado.setAttribute("collapsed","false");  
    resumen.setAttribute("collapsed","false");  
    busqueda.setAttribute("collapsed","false");  
    boxkardex.setAttribute("collapsed","true");  

}

function salirKardexProductoInventario(){
    parent.salirKardexProducto();
}

function selrowkardex(xthis){

    var xclass; 

    switch (xthis.getAttribute('class')) {

    case 'parrowkardex':
	xclass = 'parselrowkardex';
	break;

    case 'imparrowkardex':
	xclass = 'imparselrowkardex';
	break;

    case 'parselrowkardex':
	xclass = 'parrowkardex';
	break;

    case 'imparselrowkardex':
	xclass = 'imparrowkardex';
	break;
    }

    xthis.setAttribute("class",xclass);
}

function limpiar(){

    var grid,fila,i;
    grid= xid("contenedor");

    for(i = 0; i<lista.length; i++)
      {
        fila=xid(i);
        grid.removeChild(fila);
      }
    lista = new Array();
}

function setDecKardex(xdeck){
    var eval,mval;

    switch (xdeck) {
    case 'movimientos':
	mval = false;
	eval = true;
	break;

    case 'existencias':
	mval = true;
	eval = false;
	break;
    }

    //xid("boxmovimientoshead").setAttribute("collapsed",mval);
    xid("boxmovimientosresumen").setAttribute("collapsed",mval);
    xid("boxmovimientosbuscar").setAttribute("collapsed",mval);
    xid("boxmovimientos").setAttribute("collapsed",mval);
    xid("boxmovimientoscore").setAttribute("collapsed",mval);
    //xid("boxexistenciashead").setAttribute("collapsed",eval);
    xid("boxexistencias").setAttribute("collapsed",eval);
    xid("boxdetalleskardex").setAttribute("collapsed",true);
    xid("btnvolveralmacen").setAttribute("collapsed",false);
}
function seriesKardexCarrito(cantidad,idpedidodet){

    var xtitulo = 'Carrito Almacén - Elegir Número Series';
    var xmodo   = 'selSeriesKardexProductoPedido';
    loadNSResumenKardex(idproducto,cProducto,idpedidodet,xtitulo,cantidad,xmodo);
}
function verNSResumenKardex(cantidad,idpedidodet){

    var xtitulo = 'Kardex Existencias - Número Series';
    var xmodo   = 'validarSeriesKardexProducto';
    loadNSResumenKardex(idproducto,cProducto,idpedidodet,xtitulo,cantidad,xmodo);

}

function loadNSResumenKardex(xid,xproducto,idpedidodet,xtitulo,cantidad,modo){

    var url  = "../compras/selcomprar.php?id="+xid+"&"+
	       "producto="+xproducto+"&"+
	       "idpedidodet="+idpedidodet+"&"+
  	       "cantidad="+cantidad+"&"+
  	       "titulo="+xtitulo+"&"+
  	       "valor=true&"+
	       "modo="+modo;

    var boxdetkardex   = document.getElementById("boxdetalleskardex");
    var webdetkardex   = document.getElementById("webdetalleskardex");
    var boxextkardex   = document.getElementById("boxexistencias");
    var boxexistencias = document.getElementById("btnvolveralmacen");
 
    webdetkardex.setAttribute("src",url);  
    boxextkardex.setAttribute("collapsed","true");  
    boxexistencias.setAttribute("collapsed","true");  
    boxdetkardex.setAttribute("collapsed","false");  
}

function loadPedidoCarritoAlmacen(xPedidoDet,xdato,esLoteFv){

    var xexistencias,xcosto,xprecio;
    var xsumcarrito = 0;
    var esxdato     = false;

    xexistencias = parseFloat( xid("existencias_"+xPedidoDet).value);
    xdato        = parseFloat(xdato);
    xcosto       = xid("costo_"+xPedidoDet).value;
    xprecio      = xcosto*xdato*(100+parseFloat(cIGV))/100;
    esxdato      = ( isNaN(xdato) )?        true:esxdato;
    esxdato      = ( xdato > xexistencias)? true:esxdato;

    //control
    if(esxdato) 
    {
	//brutal
	xid("carrito_"+xPedidoDet).value = aCantidad['cAlmacen_'+xPedidoDet]; 
	return;
    }

    aCantidad['cAlmacenLF_'+xPedidoDet]     = (esLoteFv)? esLoteFv:'';
    aCantidad['cAlmacen_'+xPedidoDet]       = xdato;
    aCantidad['cAlmacenPrecio_'+xPedidoDet] = Math.round(( xprecio )*100)/100;;

    for(var i = 0; i<aPedidoDet.length; i++)
    {
	xsumcarrito  += parseFloat( aCantidad['cAlmacen_'+aPedidoDet[i]] );
    }
    var costo_unitario = parseFloat( xid('cart_costopromedio').value ); 

    xid('CantidadCarrito').value           = xsumcarrito;
    xid('CostoCarrito').value              = cMoneda[1]['S']+" "+ Math.round( ( costo_unitario*xsumcarrito) * 100)/100;
}

function loadSeriesCarritoAlmacen(xPedidoDet,nSeries,xSeries){

    xid('carrito_'+xPedidoDet).value = nSeries;
    aSeries[xPedidoDet]              = xSeries;

    loadPedidoCarritoAlmacen(xPedidoDet,nSeries,false);

}

function guardarCantidadCarritoAlmacen(){

    var main    = parent.getWebForm();
    var aviso   = 'Cargando Carrito...';
    var lurl    = 'modulos/compras/progress.php?modo=hWebForm&aviso='+aviso;
    var kSeries = (cSerie)? aSeries:false;

    if( parent.ckActionGuardar(kSeries,aCantidad,aPedidoDet,cProducto,idalmacen) )
	main.setAttribute("src",lurl);  
}

function guardarCantidadAjuste(){

    var xselajuste = parseFloat(xid("CantidadCarrito").value);
    var xajuste    = parseFloat(xid("CantidadAjuste").value);
    var xmensj     = false;
    var xresto     = xajuste-xselajuste;

    //Control
    xmensj = ( xselajuste > xajuste )? 
	     "- Las existencias seleccionadas exede en "+(-1)*xresto+" und.":xmensj;
    xmensj = ( xselajuste < xajuste )? 
             "- Falta seleccionar "+xresto+" und.":xmensj;

    if(xmensj)
	return alert("gPOS: Ajustes Existencias\n\n"+ xmensj);

    //Termina
    var kSeries = (cSerie)? aSeries:false;
    parent.ckAjusteGuardar(kSeries,aCantidad,aPedidoDet);

}

