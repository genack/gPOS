var SubsidiarioPost   = "";
var IdSubsidiarioPost = 0;
var cConcepto         = "";
var cPartida          = "";
var cIdOperacionCaja  = 0;
var cCodPartida       = "0";
var cOperacion        = "";
var cDocumento        = "";
var cCodDocumento     = "";
var cSubsidiario      = "";
var cIdSubsidiario    = 0;
var mesactual         = 0;

var MonedaActual = 0;
var FechaCaja    = "";
var IdArqueoGral = 0;
var Simbolo      = 0;
var cEstadoCaja  = "";

var UltimosArqueos;
var IdArqueo2RefUltimos;
var esRecibidaListaArqueos = false;

var xrequestArqueo       = false;
var arqueoCargandose     = 0;
var xrequestListaArqueos = false;

var esCajaMon;
var UlArqueoMoneda;
var UlIdArqueoGral;

var xrequestListaMovimientos = false;
var MovimientosDineros       = false;

var ipartidasaportacion  = 0;
var ipartidassustraccion = 0;
var ipartidasingreso     = 0;
var ipartidasgasto       = 0;

var ianios = 0;

function id(id){
    return document.getElementById(id);
}

/*------------------------------------------*/

function CambioListaArqueos(){
    //...
    var anio = id("filtroAnio").value;
    var mes  = id("filtroMes").value;
    var mnda = id("SeleccionMoneda").value;

    log("<-- Solicitando la lista de arqueos de local:"+Local.IdLocalActivo);
    var cjadata = "";
    var	url     = "arqueoservices.php";
    cjadata = "&modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&anio="+anio+"&mes="+mes+"&mda="+mnda+"&r=" + Math.random();
    xrequestListaArqueos = new XMLHttpRequest();
    
    xrequestListaArqueos.open("POST",url,true);
    xrequestListaArqueos.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequestListaArqueos.onreadystatechange = RecibeListaArqueos;

    xrequestListaArqueos.send(cjadata);

}

function RecibeListaArqueos() {

    //log("Recibe lista arqueos?");

    if (xrequestListaArqueos.readyState!=4) 
	return;			

    log("--> RecibeListaArqueos():"+xrequestListaArqueos.responseText);
    
    
    //TODO: procesar aqui un arqueo que se solicitu su carga	
    var obj = eval( "(" + xrequestListaArqueos.responseText+ ")" );
    var t,cjadata,FechaApertura, IdArqueo;
    
    //Reinicializamos los arqueos 							
    UltimosArqueos = new Array(); 
    IdArqueo2RefUltimos = new Array();
    
    for( t=0; t<31; t++){
	log("--> parseando vuelta.. " +t);
	UltimosArqueos["arqueo_"+t] = obj["arqueo_"+t];
	try {
	    if(!obj["arqueo_"+t]) break;
 
	    cjadata = obj["arqueo_"+t];
	    IdArqueo2RefUltimos[cjadata.IdArqueo] = t; 
	    log("carga arqueo.."+cjadata.IdArqueo+",cjadata:" + cjadata.toSource() );
	} catch(e){ };		
    }
        
    ActualizarComboArqueos();
    esRecibidaListaArqueos = true;

}

function VaciarDeHijos(padreNombre){	
    var padre = id(padreNombre);
    while( padre.childNodes.length ){
	padre.removeChild( padre.lastChild );
    }		
}

function ActualizarComboArqueos(){
    var idarqueo, fecha, arqueo,t, IdArqueo,xmenu,fechaap;
    VaciarDeHijos("itemsArqueo");
    var padre = id("itemsArqueo");

    if(!padre){
	return alert("Fallo de formato. Recargue la pagina");
    }

    var cantMon = 0;

    for(t=0;t<31;t++){
	//..
	arqueo =  UltimosArqueos["arqueo_"+t];

	if (arqueo){		
	    //log("load arqueo:"+arqueo.toSource());
	    //			fecha = arqueo["FechaApertura"];
	    fecha      = arqueo["FechaCierre"];
	    idarqueo   = arqueo["IdArqueoCajaGral"];
	    moneda     = arqueo["Moneda"];
	    fechaap    = arqueo["FechaApertura"];

	    if(t==0){
		UlArqueoMoneda = arqueo.IdMoneda;
		UlIdArqueoGral = idarqueo;
	    }

	    (arqueo.esCerrada == 0)? cantMon++ : false;

	    xmenu = document.createElement("menuitem");
	    xmenu.setAttribute( "label",idarqueo + ". " +datetimeToFechaCastellano(fechaap) +" --- "+ datetimeToFechaCastellano(fecha));
	    xmenu.setAttribute( "id-referencia",idarqueo );
	    xmenu.setAttribute( "id","arqueo_"+idarqueo );			
	    xmenu.setAttribute( "value",idarqueo );
	    xmenu.setAttribute( "oncommand","CargarArqueoSeleccionado("+t+");" );
	    
	    id("itemsArqueo").appendChild( xmenu );
	    log("xul<-- mostrando.."+idarqueo);
	}
    }
    esCajaMon = cantMon;
    
    if(!UltimosArqueos["arqueo_"+0]){
	MonedaActual = id("SeleccionMoneda").value;
	IdArqueoGral = 0;
	UlIdArqueoGral = 0;
	cEstadoCaja    = '';
	xmenu = document.createElement("menuitem");
	xmenu.setAttribute( "label","Elige arqueo ...");
	id("itemsArqueo").appendChild( xmenu );
	cleanformVisualizacion();
    }
    CargarArqueoSeleccionado(0);
}

//recibe YYYY-MM-DD HH:MM, genera DD-MM-YYYY HH:MM
function datetimeToFechaCastellano(fecha){
    if (fecha== "0000-00-00 00:00:00" || !fecha){
	//return "00-00-0000 ";			
	//return "--/--/--- --:--";
	return " 00/00/0000 --:-- ";
    }
    if (fecha=="hoy"){
	var hoy = new Date();	
	return (hoy.getDate())+"-"+(hoy.getMonth()+1)+"-"+(hoy.getYear()+1900);
    }
    
    var partesdatetime = fecha.split(" ");
    
    var partefecha 	= partesdatetime[0];
    var partehora 	= partesdatetime[1];
    
    var parteshoras = partehora.split(":");
    
    var hora =  parteshoras[0]+":"+parteshoras[1];
    
    var datosfecha 	= partefecha.split("-");
    
    return datosfecha[2] + "/" + datosfecha[1] + "/" + datosfecha[0] + " " + hora; 
}

function CargarArqueoSeleccionado(IdRef){

    var cjadata = "";
    var arqueo;
    log("Peticion de cargar datos de en index:"+IdRef);
    
    try {		
	arqueo = UltimosArqueos["arqueo_"+IdRef];

    } catch(e){
	log("W: no encontrados datos en index:"+IdRef);
	return false;
    }
    
    if(!arqueo){
	log("W: arqueo no contiene datos. / index:"+IdRef+",UA:"+UltimosArqueos.toSource() );
		return;// alert("No hay arqueos para esta");			
    }	

    var	url  = "arqueoservices.php";
    cjadata  = "&modo=getDatosActualizadosArqueo&IdArqueo="+arqueo.IdArqueoCajaGral+
	       "&IdLocal="+ Local.IdLocalActivo+"&r=" + Math.random();

    xrequest = new XMLHttpRequest();
    
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');	
    xrequest.send(cjadata);
    
    if (!xrequest.responseText)
	alert("No hay conexion con el servidor");	
    
    try {
	var arqueoactualizado = eval( "(" + xrequest.responseText + ")" );
    } catch(e){
	//Degradacion amistosa: si no puede coger datos actualizados, mantiene los actuales conocidos.
	arqueoactualizado = arqueo;
    }

    UltimosArqueos["arqueo_"+IdRef] = arqueoactualizado;		
    MonedaActual = arqueo.IdMoneda;
    FechaCaja    = arqueo.FechaApertura;
    IdArqueoGral = arqueo.IdArqueoCajaGral;
    Simbolo      = arqueo.Simbolo;
    cEstadoCaja  = (arqueo.esCerrada == 1)? 'CERRADA':'ABIERTA';

    log("Nos traemos del server:"+xrequest.responseText);		 	 
    log("Habia en casa:"+arqueo.toSource());
    
    //ActualizarVisualizacion(arqueo);
    var titulo = 'Caja General  - '+arqueo.Moneda;
    id("titulocajagral").setAttribute('label',titulo);

    id("SeleccionArqueo").value = IdArqueoGral;

    ActualizarVisualizacion(arqueoactualizado);
    CambioListaMovimientos( arqueo.IdArqueoCajaGral );
    mostrarCambioMoneda(MonedaActual);
}


function ActualizarVisualizacion(arqueo){
    log("I: actualizando datos de arqueo:"+arqueo.toSource());
    var sign = arqueo.Simbolo;

    //Balances
    id("saldoInicialText").setAttribute("value", sign +" "+ formatDinero(arqueo.ImporteApertura));
    id("ingresosText").setAttribute("value",  sign +" "+  formatDinero(arqueo.ImporteIngresos) );
    id("egresosText").setAttribute("value",  sign +" "+  formatDinero(arqueo.ImporteCompras) );
    id("gastosText").setAttribute("value",  sign +" "+ formatDinero(arqueo.ImporteGastos) );
    id("aportacionesText").setAttribute("value",  sign +" "+ formatDinero(arqueo.ImporteAportaciones) );
    id("sustraccionesText").setAttribute("value",  sign +" "+ formatDinero(arqueo.ImporteSustracciones) );
    
    //Teorico de cierre
    id("TeoricoCierre").setAttribute("value",  sign +" "+ formatDinero(arqueo.ImporteTeoricoCierre) );		
    
    //Importe de cierre
    id("cierreCajaText").setAttribute("value", sign +" "+ formatDinero(arqueo.ImporteCierre) );
    
    
    //Importe descuadre

    var esdescuadre   = (0-arqueo.ImporteDescuadre);
    var title_des     = (esdescuadre < 0)? "FALTANTE":"SOBRANTE";
    var descuadre_imp = (esdescuadre < 0)? arqueo.ImporteDescuadre:esdescuadre;
    var dclass        = (esdescuadre < 0)? "cjafaltante plain":"cjasobrante plain";

    id("titledescuadre").setAttribute("value",title_des);
    //id("titledescuadre").setAttribute("class",dclass);
    id('descuadreCajaText').setAttribute("value", sign +" "+ formatDinero(descuadre_imp) );
    id('descuadreCajaText').setAttribute("class", dclass );

    
    //Fecha
    //id("estadoCajaFecha").setAttribute("value", datetimeToFechaCastellano(arqueo.FechaCierre));
    //Estado abierto/cerrado
    var esestado_cja = (arqueo.esCerrada>0)? "CERRADA":"ABIERTA";
    var esfecha_cja   = (arqueo.esCerrada>0)? arqueo.FechaCierre:arqueo.FechaApertura

    id("estadoCajaTexto").setAttribute("value",esestado_cja);
    id("estadoCajaFecha").setAttribute("value", datetimeToFechaCastellano(esfecha_cja));

    habilitabotones();
    changeColorEstadoCajaGral();
}

function test(cosaParaVer){
    prompt( "D:",cosaParaVer.toSource());
}

/*---------------------------------------------------*/

function CambioListaMovimientos(IdArqueo){
	
    if (!IdArqueo) {
	//return alert('Arqueo inexistente');
	return;//Si no hay arqueo definido, no hay datos que cargar
    }
    
    
    //var	url = "arqueoservices.php?modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
    var cjadata = "";
    var	url     = "arqueoservices.php";
    cjadata     = "&modo=getMovimientos&IdArqueo="+IdArqueo+"&r=" + Math.random();
    xrequestListaMovimientos = new XMLHttpRequest();
    
    xrequestListaMovimientos.open("POST",url,true);
    xrequestListaMovimientos.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xrequestListaMovimientos.onreadystatechange = RecibeListaMovimientos;
    xrequestListaMovimientos.send(cjadata);
}

function RecibeListaMovimientos() {
    
    if (xrequestListaMovimientos.readyState!=4) 
	return;			
    //TODO: procesar aqui un arqueo que se solicitu su carga
    
    var obj = eval( "(" + xrequestListaMovimientos.responseText+ ")" );
    var t;		
    var FechaApertura, IdArqueo;
    
    if (!obj){
	return alert("No se obtiene respuesta del servidor");
    }
    
    
    //Reinicializamos los arqueos 							
    MovimientosDineros = new Array(); 
    
    t=0;
    while( obj["mov_"+t] ){
	MovimientosDineros["mov_"+t] = obj["mov_"+t];
	t++;		
    }
    if(!t){
	//alert('No hubo movimientos');
	//Si no hay movimientos, no hay necesidad de generar nada, hemos terminado
	//return;
    }
    
    log("Se recibieron '"+t+"' movimientos");
    
    RegenerarCuadroDeMovimientos();
}

function VaciarDeHijosTag(padreNombre,Tag){	
    var padre = id(padreNombre);
    while( padre.childNodes.length && padre.lastChild && padre.lastChild.getAttribute(Tag) ){
	padre.removeChild( padre.lastChild );
    }		
}

function RegenerarCuadroDeMovimientos(){
    //VaciarDeHijos("listaMovimientos");
    VaciarDeHijosTag("listaMovimientos","esMov");
    //Borrando a mano
    
    var listaMov = id("listaMovimientos");
    var t = 0;
    while( mov = MovimientosDineros["mov_"+t] ) {

	var xrow = document.createElement("listitem");
	xrow.setAttribute("esMov",true);
	xrow.setAttribute("value",mov.IdOperacionCaja);

	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",datetimeToFechaCastellano(mov.FechaInsercion) );

	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.TipoOperacion+' - '+mov.PartidaCaja );
	xcell.setAttribute("value",mov.TipoOperacion);
	xcell.setAttribute("id","gral_operacion_"+mov.IdOperacionCaja);
	
	xrow.appendChild(xcell);

	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Proveedor2 );
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Concepto+' '+mov.Documento+' '+mov.CodigoDocumento+' '+mov.Proveedor );
	xcell.setAttribute("value",mov.Concepto);
	xcell.setAttribute("id","gral_concepto_"+mov.IdOperacionCaja );
	
	xrow.appendChild(xcell);

	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Simbolo+" "+formatDinero(mov.Importe) );
	xcell.setAttribute("style","text-align:right;");
	
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell");	xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Identificacion );
	xcell.setAttribute("style","text-align:center;");
	
	xrow.appendChild(xcell);

	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.PartidaCaja );
	xcell.setAttribute("value",mov.Codigo );
	xcell.setAttribute("id","gral_partida_"+mov.IdOperacionCaja );
 	xcell.setAttribute("collapsed","true" );

	xrow.appendChild(xcell);

	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Documento );
	xcell.setAttribute("value",mov.CodigoDocumento );
	xcell.setAttribute("id","gral_documento_"+mov.IdOperacionCaja );
 	xcell.setAttribute("collapsed","true" );

	xrow.appendChild(xcell);

	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Proveedor );
	xcell.setAttribute("value",mov.IdSubsidiario );
	xcell.setAttribute("id","gral_subsidiario_"+mov.IdOperacionCaja );
 	xcell.setAttribute("collapsed","true" );

	xrow.appendChild(xcell);
	
	listaMov.appendChild(xrow);			
		
	t++;
    }

}


// desde "4,43.33 $"  hacia  "443,33"
function formatDinero(numero) {

    var num = new Number(numero);
    num = num.toString();
    
    if(isNaN(num)) num = "0";
    var sign = (num == (num = Math.abs(num)));
    num = Math.round(num*100)/100;
    num = num.toFixed(2);
    return (((sign)?'':'-') + num );   
    /*    var num = new Number(numero);
	  num = num.toString().replace(/\$|\,/g,'');
	  
	  if(isNaN(num)) num = "0";
	  
	  var sign = (num == (num = Math.abs(num)));
	  num = Math.floor(num*100+0.50000000001);
	  var cents = num%100;
	  num = Math.floor(num/100).toString();
	  
	  if(cents<10) cents = "0" + cents;
	  
	  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
	  num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
	  
	  return (((sign)?'':'-') + num + ',' + cents);*/
}


/*---------------------------------------------------*/

function Comando_CerrarCaja(){

    var p = confirm(po_quierecerrar);
    if (!p) return;//Abortado por decision del usuario 

    if(!Comando_ArqueoCaja()) return;
    //return alert("ENTRO...");    
    //Guardamos datos actuales arqueo como actual
    // ..y creamos el siguiente.	 
    Local.IdArqueoActual = ActualizarDatosDeCierre();	
    //return alert(Local.IdArqueoActual);
    //Recargamos 		
    // No descomentar ActualizarComboArqueos();
    esRecibidaListaArqueos = false;
    onLoadFormulario();

    //document.location = "modarqueogral.php?&modo=verCajaGeneral&xidacg="+IdArqueoGral+
	//                "&xidl="+Local.IdLocalActivo;
}

function Comando_AbrirCaja(){
    var p = confirm("Desea Abrir Caja?");
    if (!p) return;//Abortado por decision del usuario 
    //Comando_ArqueoCaja();
    
    //Guardamos datos actuales arqueo como actual
    // ..y creamos el siguiente.

    var res = ActualizarDatosDeApertura();
    var ares = res.split("~");

    if(ares[0] != '')
	return alert("gPOS:  Caja General \n\n  - Error al Abrir Caja \n  - "+ares[0]);
    if(ares[2] != '')
	return alert("gPOS:  Caja General \n\n  - Error al Abrir Caja \n  - "+ares[2]);
    if(ares[3] > '0')
	return alert("gPOS:  Caja General \n\n  - La Caja ya está abierta \n ");

    Local.IdArqueoActual = 0;

    //Recargamos 		
    // No descomentar ActualizarComboArqueos();
    esRecibidaListaArqueos = false;
    onLoadFormulario();

   // document.location = "modarqueogral.php?&modo=verCajaGeneral&xidacg="+IdArqueoGral+
	//                "&xidl="+Local.IdLocalActivo;
	
}

/*---------------------------------------------------*/



function Comando_ArqueoCaja(){

    var p = prompt(po_importereal,"0");

    if(!p) return false;

    if(isNaN(p)||p<0)
        return false;

    log("Comando arqueocaja: actualizando arreglo.. "); 	
    sv_actualizarServicio(p); 	


    if (!UltimosArqueos){
        UltimosArquos = new Array();
    }		

    if (!UltimosArqueos["arqueo_0"]){
        UltimosArqueos["arqueo_0"] = new Object();
    }				

    UltimosArqueos["arqueo_0"].ImporteCierre = p;

    //Recargamos 		
    log("Comando arqueocaja: cambiolistaarqueos.. ");
    CambioListaArqueos();	

    //Cargamos primero
    log("Comando arqueocaja: cargamos el arqueo ultimo.. ");

    CargarArqueoSeleccionado(0);
    return true; //finaliza
}

function ActualizarDatosDeCierre(){
    var cjadata = "";
    log("arquearYAbrirNuevaCaja para local:"+Local.IdLocalActivo);

    var	url  = "arqueoservices.php";
    cjadata  = "&modo=arquearYAbrirNuevaCaja&IdLocal="+Local.IdLocalActivo+
	       "&xidm="+MonedaActual; 
               "&r=" + Math.random();
    
    var xrequest = new XMLHttpRequest();
    
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);	

    var res = xrequest.responseText;
    
    return 0;

}

function ActualizarDatosDeApertura(){

    log("arquearYAbrirNuevaCaja para local:"+Local.IdLocalActivo);
    var cjadata = "";
    var	url     = "arqueoservices.php";
    cjadata     = "&modo=soloAbrirCaja&xidl="+Local.IdLocalActivo+
	          "&xidm="+MonedaActual;
                  "&r=" + Math.random();

    var xrequest = new XMLHttpRequest();
    
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);	
    
    var res = xrequest.responseText;
    //if(res == 'error')
	//return res;
    
    return res;
}

//
// Actualiza la cantidad en caja en el servidor del ultimo arqueo 
//

function sv_actualizarServicio(CantidadCierre){

    log("Marcando nueva cantidad en caja.."+CantidadCierre);
    var cjadata = "";
    var	url     = "arqueoservices.php";
    cjadata     = "&modo=actualizarCantidadCaja&IdLocal="+Local.IdLocalActivo+
	          "&cantidad="+escape(CantidadCierre)+
	          "&xidm="+MonedaActual;
                  "&r=" + Math.random();
    var xrequest = new XMLHttpRequest();
    
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);	
    
    var res = xrequest.responseText;	
}

function onConsultarCaja(){
    var	sel = id("SeleccionArqueo");
    //alert( sel.toSource() );
}

/*---------------------------------------------------*/

function comando_HacerUnaOperacion(op){
    var	url      = "arqueoservices.php";
    var xrequest = new XMLHttpRequest();
    var cjadata  = "";
    var xmsj     = "";
    var idmoneda     = MonedaActual;
    var fechacaja    = FechaCaja;
    var idarqueogral = IdArqueoGral;
    var simbolo      = Simbolo;
    var cantidad, concepto, cambiomoneda, documento, codigodoc, proveedor, partida, codpartida, idcuenta;

    switch(op){
    case 'Aportacion':
	cantidad     = id("importeText").value;
	concepto     = id("conceptoText").value;
	cambiomoneda = (MonedaActual!=1)? id("importeCambioMonedaAp").value : '1';
	partida      = id("SeleccionPartidaAport").label;
	codpartida   = id("SeleccionPartidaAport").value;
	xmsj         = "Se hizo un aporte a la caja: ";
	break;

    case 'Sustraccion':
	cantidad     = id("importeTextSubs").value;
	concepto     = id("conceptoTextSubs").value;
	partida      = id("SeleccionPartidaSust").label;
	var xcambio  = id("importeCambioMonedaSust").value;
	codpartida   = id("SeleccionPartidaSust").value;
	cambiomoneda = (MonedaActual!=1)? xcambio : '1';
	cambiomoneda = (MonedaActual == 1 && codpartida == 'S125')? xcambio:cambiomoneda;
	idcuenta     = (codpartida == 'S105')? id("SeleccionCuentaBancariaSust").value:false;
	xmsj         = "Se hizo una sustracción de la caja: ";
	break;

    case 'Ingreso':
	cantidad     = id("importeTextIngreso").value;
	concepto     = id("conceptoTextIngreso").value;
	cambiomoneda = (MonedaActual!=1)? id("importeCambioMonedaIng").value : '1';
	partida      = id("SeleccionPartidaIngreso").label;
	codpartida   = id("SeleccionPartidaIngreso").value;
	idcuenta     = (codpartida == 'S106')? id("SeleccionCuentaBancariaIng").value:false;
	xmsj         = "Se hizo un ingreso a la caja: ";

	if(idcuenta){
	    if(parseFloat(id("saldoCuentaBancariaIng")) < parseFloat(cantidad))
		return alert("gPOS:   Caja General \n\n - El importe es mayor al saldo de cuenta");
	}
	break;

    case 'Gasto':
	cantidad     = id("importeTextGasto").value;
	concepto     = id("conceptoTextGasto").value;
	documento    = id("SeleccionDocumentoGasto").label;
	codigodoc    = id("CodigoTextGasto").value;
	proveedor    = id("IdSubsidiario").value;
	cambiomoneda = (MonedaActual!=1)? id("importeCambioMonedaGast").value : '1';
	partida      = id("SeleccionPartidaGasto").label;
	codpartida   = id("SeleccionPartidaGasto").value;
	xmsj         = "Se hizo un gasto de la caja: ";
	break;
    }
    codigodoc = (codigodoc)? codigodoc:"";
    proveedor = (proveedor)? proveedor:0;
    //concepto  = concepto.toUpperCase();

    cjadata =  "&modo=hacerOperacionDinero&xidl="+Local.IdLocalActivo+
	       "&cantidad="+escape(cantidad)+
               "&concepto="+encodeURIComponent(concepto)+
	       "&xidm="+idmoneda+
	       "&cm="+cambiomoneda+
	       "&fcaja="+fechacaja+
	       "&xidacg="+idarqueogral+
	       "&doc="+documento+
               "&coddoc="+codigodoc+
	       "&prov="+proveedor+
	       "&op="+op+
	       "&partida="+partida+
	       "&codpartida="+codpartida+
	       "&idcuenta="+idcuenta+
               "&r=" + Math.random();

    var val = validarOperacionCaja(cambiomoneda,codpartida,cantidad,concepto);

    if(val)
	return;

    if(trim(codpartida) == 'S124')
        if( !confirm("gPOS:  Operación Caja General\n\n"+" - Va transferir dinero a Almacén Central,  ¿Desea Continuar?") )
	    return;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);	
    var res = xrequest.responseText;
    var ares = res.split("~");

    if(ares[0] != '')
	return alert(po_servidorocupado+" "+ares[0]);

    var msj = "gPOS:  Operación caja General \n\n  - No se realizó la operación \n";
    if(ares[1] == '01')
	return alert(msj+ "  - El Id del arqueo a cambiado, consulte con su administrador ");

    if(ares[1] == '02')
	return alert(msj+ "  - La caja de almacén central está cerrada, consulte con su administrador ");

    if(ares[1] == '03')
	return alert(msj+"  - Estás en almacén central");

    if(ares[1] == '04')
	return alert(msj+"  - La caja de moneda destino está cerrada, Consulte con su administrador");

    alert("gPOS:   Operación Caja General \n\n "+xmsj+simbolo+" "+formatDinero(cantidad) );
    
    CleanFormOperacion();
    CargarArqueoSeleccionado(0);

}

function validarOperacionCaja(cambiomoneda,codpartida,cantidad,concepto){
    var estado  = id("estadoCajaTexto").value;
    var caja    = (esCajaMon == 0 || estado == 'CERRADA')? true:false;
    var msj     = "gPOS:   Caja General \n\n";
    var cjadata = "";

    var impteop   = (cantidad <= 0 || cantidad == "")? true:false;
    var cbmoneda  = (cambiomoneda <= 1)? true:false;
    var part      = (codpartida == "0")? true:false;
    var cpto      = (concepto == "")? true:false;

    cjadata += (part)  ? " - Partida":"";
    cjadata += (cpto) ? "\n - Concepto":"";
    cjadata += (impteop)  ? "\n - Importe":"";
    cjadata += (cbmoneda && MonedaActual == 2)? "\n - Cambio Moneda":"";

    //return (caja,cjadata);

    if(caja){
	alert(msj+"- La caja está cerrada");
	return true;
    }
    if(cjadata != ""){
	alert(msj+"Faltan los siguientes datos \n"+cjadata);
	return true;
    }

    return false;
}

/*---------------------------------------------------*/


function TestFuncionamiento(){	
    alert(xrequestListaArqueos.toSource());		
}

/* --------------------------------------------------*/

function onLoadFormulario(){
    regenAnioArqueo();
    cargarDatosDefault();
    CambioListaArqueos();
    ContarElementosPartidas();
    setTimeout("DemonioPrimeraApertura()",200);
    RegenCuentasBancarias();
}

function DemonioPrimeraApertura(){

    if (esRecibidaListaArqueos) {
	//cargamos el ultimo arqueo que tenemos.
	CargarArqueoSeleccionado(0); 		 	
    }else {
	//aun no se ha cargado la lista de arqueos..
	setTimeout("DemonioPrimeraApertura()",200);//reintentamos		
    }// reintentamos..hasta cargar la lista de arqueos 	

}

/* --------------------------------------------------*/

//Desde "1.331,33" hacia "1331.33"
function CleanMoney(cadena) {
    return parseMoney(new String(cadena) );
}

function parseMoney (cadena) {
    //var cadoriginal = cadena;
	
    if (!cadena) 
	return 0.0	
    
    cadena = new String( cadena );
    if( !cadena.replace ){
	return cadena;		 	
    }	
    
    cadena = cadena.replace(/\./g,"");
    cadena = cadena.replace(/\,/g,".");
    cadena = parseFloat( cadena );	
    
    if (isNaN( cadena ))
	return 0.0;
    
    return cadena;
}

function log(text){
    id("log").setAttribute("value", text + "\n" + id("log").getAttribute("value")  );
}

function habilitabotones(){
    var estado  = cEstadoCaja;
    var mes     = id("filtroMes").getAttribute("value");

    var xabrir  = (mes != mesactual)? true:false;
    var xcerrar = (mes != mesactual)? true:false;
    var xarqueo = (mes != mesactual)? true:false;
    var xbtnop  = (mes != mesactual)? true:false;

    switch(estado){
    case 'ABIERTA':
	xabrir  = true;
	break;
    case 'CERRADA':
	xabrir  = (IdArqueoGral < UlIdArqueoGral)? true:xabrir;
	xcerrar = true;
	xarqueo = true;
	xbtnop  = true;
	break;
    default :
	xabrir  = (IdArqueoGral == 0 && mes == mesactual)? false:true;	
	xcerrar = true;
	xarqueo = true;
	xbtnop  = true;
    }

    id("botonCerrar" ).setAttribute("disabled",xcerrar);
    id("botonAbrir").setAttribute("disabled",xabrir);
    id("botonArqueo").setAttribute("disabled",xarqueo);

    id("btnAporte").setAttribute("disabled",xbtnop);
    id("btnSustracion").setAttribute("disabled",xbtnop);
    id("btnIngreso").setAttribute("disabled",xbtnop);
    id("btnGasto").setAttribute("disabled",xbtnop);
    
    id("botonAbrir").className  = (xabrir)? "btn_disabled":"btn";
    id("botonCerrar").className = (xcerrar)? "btn_disabled":"btn";
    id("botonArqueo").className = (xarqueo)? "btn_disabled":"btn";	
}

function mostrarCambioMoneda(MonedaActual){
    switch(MonedaActual){
    case '1':
	var xval=true;
	break;
    
    case '2':
	var xval=false;
	break;
    }
    id("cambioMonedaAp").setAttribute("collapsed",xval);
    id("importeCambioMonedaAp").setAttribute("collapsed",xval);
    id("cambioMonedaSust").setAttribute("collapsed",xval);
    id("importeCambioMonedaSust").setAttribute("collapsed",xval);
    id("cambioMonedaIng").setAttribute("collapsed",xval);
    id("importeCambioMonedaIng").setAttribute("collapsed",xval);
    id("cambioMonedaGast").setAttribute("collapsed",xval);
    id("importeCambioMonedaGast").setAttribute("collapsed",xval);
}

function CleanFormOperacion(){

    id("conceptoText").value = "";
    id("importeText").value = "";
    id("conceptoTextSubs").value = "";
    id("importeTextSubs").value = "";
    id("conceptoTextIngreso").value = "";
    id("importeTextIngreso").value = "";
    id("conceptoTextGasto").value = "";
    id("EmpresaTextGasto").value = "";
    id("CodigoTextGasto").value = "";
    id("SeleccionDocumentoGasto").value = "";
    id("IdSubsidiario").value = "";
    
}

function auxAltaSubsidiario(){
    var url   = '../../modsubsidiarios.php?modo=altapopup';
    var tipo  = 'altaproveedor';
    popup(url,tipo);
}

function auxSubsidiarioHab(){
    var url   = '../subsidiarios/selsubsidiario.php?modo=subsidiariopost';
    var tipo  = 'proveedorhab';
    popup(url,tipo);
}

function loadSubsidiarioHab(){
    if(!SubsidiarioPost || SubsidiarioPost == 'undefined') return;
}

function changeColorEstadoCajaGral(){
    var stdocja = id("estadoCajaTexto").value;
    var stle = (stdocja == 'ABIERTA')? "cjagralabierta plain big":"cjagralcerrada plain big";

    id("estadoCajaTexto").setAttribute('class',stle);
    id("estadoCajaFecha").setAttribute('class',stle);
}

function mostrarPanelOperacionesGral(xval){
    var xop    = (xval == 'arqueo')? 'operacion':'arqueo';
    var xlabel = (xval == 'arqueo')? 'Arqueo Actual':'Operaciones';
    var xvalue = (xval == 'arqueo')? false:true;
    var ximage = (xval == 'arqueo')? "../../img/gpos_arqueo.png":"../../img/gpos_tpvcaja_guardarpartida.png";
    var xcommand = "mostrarPanelOperacionesGral('"+xop+"')";

    id("boxOperacionesGral").setAttribute("collapsed",xvalue);
    id("boxArqueoActualGral").setAttribute("collapsed",!xvalue);
    id("btnOperacionesGral").setAttribute("label",xlabel);
    id("btnOperacionesGral").setAttribute("oncommand",xcommand);
    id("btnOperacionesGral").setAttribute("image",ximage);
}

function exportarMovimientosCajaGral(xtipo){
    if(IdArqueoGral == 0) return;
    var data = "&idarqueo="+IdArqueoGral+
	       "&idmoneda="+MonedaActual+
	       "&idlocal="+Local.IdLocalActivo;

    switch(xtipo){
    case 'PDF':
	var url = "../fpdf/imprimir_movimientoscajagral.php?" + data;
	location.href=url;
	break;
    case 'CSV':
	var url = "../generadorlistados/exportarlistados.php?"+data+
                  "&modo="+"movimientoscajagral";
	document.location=url;
	break;
    }
}

function ModificarOperacionCaja(){
    if(cCodPartida == "0") return;

    var	url      = "arqueoservices.php?";
    var xrequest = new XMLHttpRequest();
    var msj      = "";

    var concepto, documento, codigodoc, proveedor,subsidiario="";

    switch(cOperacion){
    case 'Aportacion':
	concepto     = id("conceptoText").value;
	break;

    case 'Sustraccion':
	concepto     = id("conceptoTextSubs").value;
	break;

    case 'Ingreso':
	concepto     = id("conceptoTextIngreso").value;
	break;

    case 'Gasto':
	concepto     = trim(id("conceptoTextGasto").value);
	documento    = id("SeleccionDocumentoGasto").label;
	codigodoc    = id("CodigoTextGasto").value;
	proveedor    = id("IdSubsidiario").value;
	subsidiario  = id("EmpresaTextGasto").value;

	if(!codigodoc || !proveedor)
	    return alert("gPOS:  Operación Caja General\n\n  - Ingrese el código del comprobante y seleccione la empresa");
	break;
    }
    msj       = "Se guardó los cambios: ";

    codigodoc = (codigodoc)? codigodoc:"";
    proveedor = (proveedor)? proveedor:0;
    //concepto  = concepto.toUpperCase();
    var xconcepto = trim(concepto+" "+documento+" "+codigodoc+" "+subsidiario);

    cjadata =  "modo=modificaOperacionCajaGral&xidl="+Local.IdLocalActivo+
               "&concepto="+encodeURIComponent(concepto)+
	       "&codigodoc="+codigodoc+
	       "&proveedor="+proveedor+
 	       "&doc="+documento+
	       "&xidoc="+cIdOperacionCaja;


    xrequest.open("GET",url+cjadata,false);
    xrequest.send(cjadata);
    var res = xrequest.responseText;
    var ares = res.split("~");

    if(ares[0] != '')
	return alert("gPOS:   Operación Caja General \n\n" + " - "+ares[0]);
    
    alert("gPOS:  Operación Caja General \n\n - "+ msj);
    id("gral_concepto_"+cIdOperacionCaja).setAttribute("label",xconcepto.toUpperCase());
    habilitarOperacionCaja();
    CargarArqueoSeleccionado(0);
}

function revisarOperacionCajaGral(){
    var idex = id("listaMovimientos").selectedItem;
    if(!idex) return;
    var xcpto = false;
    id("ConceptoOperacionCaja").removeAttribute("disabled");

    cConcepto      = id("gral_concepto_"+idex.value).getAttribute("value");
    cPartida       = id("gral_partida_"+idex.value).getAttribute("label");
    cCodPartida    = id("gral_partida_"+idex.value).getAttribute("value");
    cOperacion     = id("gral_operacion_"+idex.value).getAttribute("value");
    cDocumento     = id("gral_documento_"+idex.value).getAttribute("label");
    cCodDocumento  = id("gral_documento_"+idex.value).getAttribute("value");
    cSubsidiario   = id("gral_subsidiario_"+idex.value).getAttribute("label");
    cIdSubsidiario = id("gral_subsidiario_"+idex.value).getAttribute("value");
    cIdOperacionCaja = idex.value;

    if(cOperacion == 'Egreso')
	xcpto = true;
    
    id("ConceptoOperacionCaja").setAttribute("disabled",xcpto);

    habilitarOperacionCaja();
}

function editarOperacionCajaGral(){
    var op = cOperacion;
    var xselindex = 0;
    var xtabapor  = 'false';
    var xtabsust  = 'false';
    var xtabingr  = 'false';
    var xtabgast  = 'false';

    if(op == 'Egreso') return;

    switch(op){
    case 'Aportacion':
	id("conceptoText").value = cConcepto;
	id("SeleccionPartidaAport").value = cCodPartida;
	id("btnAporte").setAttribute("label","Modificar Concepto");
	id("btnAporte").setAttribute("oncommand",'ModificarOperacionCaja('+'"Aporte"'+')');
	id("conceptoText").focus();

	var xtabapor  = 'true';
	var xselindex = 0;

	break;

    case 'Sustraccion':
	id("conceptoTextSubs").value = cConcepto;
	id("SeleccionPartidaSust").value = cCodPartida;
	id("btnSustracion").setAttribute("label","Modificar Concepto");
	id("btnSustracion").setAttribute("oncommand",'ModificarOperacionCaja('+'"Sustracion"'+')');
	id("conceptoTextSubs").focus();

	var xtabsust  = 'true';
	var xselindex = 1;

	break;

    case 'Ingreso':
	id("conceptoTextIngreso").value = cConcepto;
	id("SeleccionPartidaIngreso").value = cCodPartida;
	id("btnIngreso").setAttribute("label","Modificar Concepto");
	id("btnIngreso").setAttribute("oncommand",'ModificarOperacionCaja('+'"Ingreso"'+')');
	id("conceptoTextIngreso").focus();

	var xtabingr  = 'true';
	var xselindex = 2;
	break;

    case 'Gasto':
	id("conceptoTextGasto").value = cConcepto;
	id("SeleccionPartidaGasto").value = cCodPartida;
	id("CodigoTextGasto").value = cCodDocumento;
	id("SeleccionDocumentoGasto").value = cDocumento;
	id("EmpresaTextGasto").value = cSubsidiario;
	id("IdSubsidiario").value = cIdSubsidiario;
	id("btnGasto").setAttribute("label","Modificar Concepto");
	id("btnGasto").setAttribute("oncommand",'ModificarOperacionCaja('+'"Gasto"'+')');
	id("conceptoTextGasto").focus();

	var xtabgast  = 'true';
	var xselindex = 3;

	break;
    }

    id("tab_aportacion").setAttribute("selected",xtabapor);
    id("tab_sustraccion").setAttribute("selected",xtabsust);
    id("tab_ingreso").setAttribute("selected",xtabingr);
    id("tab_gasto").setAttribute("selected",xtabgast);
    id("tab_boxoperacion").setAttribute("selectedIndex",xselindex);

    mostrarPanelOperacionesGral('arqueo');
}

function habilitarOperacionCaja(){
    var op = cOperacion;
    var xlabel,xcommand;
    var op = id("tab_boxoperacion").getAttribute("selectedIndex");

    switch(op){
    case '0':
	xlabel   = "Registrar Aporte";
	xcommand = "Aporte";
	id("conceptoText").value = "";
	break;

    case '1':
	xlabel   = "Registrar Sustracción";
	xcommand = "Sustracion";
	id("conceptoTextSubs").value = "";
	break;

    case '2':
	xlabel   = "Registrar Ingreso";
	xcommand = "Ingreso";
	id("conceptoText"+xcommand).value = "";
	break;

    case '3':
	xlabel   = "Registrar Gasto";
	xcommand = "Gasto";
	id("conceptoText"+xcommand).value = "";
	break;
    }

    if(!id("btn"+xcommand)) return;

    id("btn"+xcommand).setAttribute("label",xlabel);
    id("btn"+xcommand).setAttribute("oncommand",'comando_HacerUnaOperacion('+'"'+xcommand+'"'+')');
}

function CogePartidaCaja(operacion){
    popup("../partidas/selpartidas.php?modo=partida&xidl=0"+"&xop="+operacion+"&cja=CG",'marca');
}


function changePartida( quien, txtcuenta, operacion) {

    switch(operacion){
      case 'Aportacion':
	id("SeleccionPartidaAport").value = quien.value;
	break;
      case 'Sustraccion':
	id("SeleccionPartidaSust").value = quien.value;
	break;
      case 'Ingreso':
	id("SeleccionPartidaIngreso").value = quien.value;
	break;
      case 'Gasto':
	id("SeleccionPartidaGasto").value = quien.value;
	break;
    }
}

function VaciarPartidas(operacion){
    var xlistitem;
    var defpart,ipartidas;
    switch(operacion){
      case 'Aportacion':
	xlistitem = id("elementosPartidaAportacion");
	defpart = "partida_aportacion_def_";
	ipartidasaportacion = 0;
	break;
      case 'Sustraccion':
	xlistitem = id("elementosPartidaSustraccion");
	defpart = "partida_sustraccion_def_";
	ipartidassustraccion = 0;
	break;
      case 'Ingreso':
	xlistitem = id("elementosPartidaIngreso");
	defpart = "partida_ingreso_def_";
	ipartidasingreso = 0;
	break;
      case 'Gasto':
	xlistitem = id("elementosPartidaGasto");
	defpart = "partida_gasto_def_";
	ipartidasgasto = 0;
	break;
    }

    var t = 0;
    
    while( el = id(defpart + t) ) {
	if (el)	xlistitem.removeChild( el );
	t = t + 1;
    }
}

function RegenPartidas(operacion){
    VaciarPartidas(operacion);

    var xrequest = new XMLHttpRequest();
    var url = "../../services.php?modo=partidas&xidl=0"+"&xop="+operacion+"&cja=CG";
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res   = xrequest.responseText;
    var lines = res.split("\n");
    var ln    = lines.length-1;
    var actual;

    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	AddPartidaLine(actual[0],actual[1],operacion);
    }				
}

function AddPartidaLine(nombre, valor, operacion) {
    var xlistitem;
    var defpart,xpartidas;
    switch(operacion){
      case 'Aportacion':
	xlistitem = id("elementosPartidaAportacion");
	defpart   = "partida_aportacion_def_";
	xpartidas = ipartidasaportacion;
	break;
      case 'Sustraccion':
	xlistitem = id("elementosPartidaSustraccion");
	defpart   = "partida_sustraccion_def_";
	xpartidas = ipartidassustraccion;
	break;
      case 'Ingreso':
	xlistitem = id("elementosPartidaIngreso");
	defpart   = "partida_ingreso_def_";
	xpartidas = ipartidasingreso;
	break;
      case 'Gasto':
	xlistitem = id("elementosPartidaGasto");
	defpart   = "partida_gasto_def_";
	xpartidas = ipartidasgasto;
	break;
    }

    if(ipartidasaportacion == 0 ||
       ipartidassustraccion == 0 ||
       ipartidasingreso == 0 ||
       ipartidasgasto == 0){
	var xpartida = document.createElement("menuitem");
	xpartida.setAttribute("id",defpart + xpartidas);
	xpartida.setAttribute("value",'0');
	xpartida.setAttribute("label",'Elige...');
	xpartida.setAttribute("style",'font-weight: bold');
	xlistitem.appendChild( xpartida);

	if(operacion == 'Aportacion'){ipartidasaportacion++; xpartidas = ipartidasaportacion;}
	if(operacion == 'Sustraccion'){ipartidassustraccion++;xpartidas=ipartidassustraccion;}
	if(operacion == 'Ingreso'){ipartidasingreso++;xpartidas = ipartidasingreso;}
	if(operacion == 'Gasto') {ipartidasgasto++;xpartidas = ipartidasgasto;}
    }

    var xpartida = document.createElement("menuitem");
    xpartida.setAttribute("id",defpart + xpartidas);
    xpartida.setAttribute("value",valor);
    xpartida.setAttribute("label",nombre);
    xlistitem.appendChild( xpartida);

    if(operacion == 'Aportacion') ipartidasaportacion++;
    if(operacion == 'Sustraccion') ipartidassustraccion++;
    if(operacion == 'Ingreso') ipartidasingreso++;
    if(operacion == 'Gasto') ipartidasgasto++;
}

function ContarElementosPartidas(){
    ipartidasaportacion  = id("SeleccionPartidaAport").itemCount;
    ipartidassustraccion = id("SeleccionPartidaSust").itemCount;
    ipartidasingreso     = id("SeleccionPartidaIngreso").itemCount;
    ipartidasgasto       = id("SeleccionPartidaGasto").itemCount;
}

function validarDatosExtra(xlabel,xvalue){
    xval = (xlabel == 'Cambio moneda' && MonedaActual == 1)? false:true;
    xval = (MonedaActual != 1)? false:xval;

    xparts = id("SeleccionPartidaSust").value;
    xparti = id("SeleccionPartidaIngreso").value;

    xbni = (xparti == 'S106')? false:true;
    xbns = (xparts == 'S105')? false:true;

    id("cambioMonedaSust").setAttribute("collapsed",xval);
    id("importeCambioMonedaSust").setAttribute("collapsed",xval);

    id("boxCuentaBancariaSust").setAttribute("collapsed",xbns);
    id("boxCuentaBancariaIng").setAttribute("collapsed",xbni);
}

function regenAnioArqueo(){
    VaciarAnioArqueos();
    var	url  = "arqueoservices.php?modo=obtenerAnios";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;
    
    var xanios = xres.split(",");
    
    for(var i=0;i<xanios.length;i++){
	AddAnioArqueo(xanios[i]);
    }
}

function AddAnioArqueo(anios){
    var xlistitem = id("elementosanio");	

    var xanio = document.createElement("menuitem");
    xanio.setAttribute("id","anio_def_" + ianios);
	
    xanio.setAttribute("value",anios);
    xanio.setAttribute("label",anios);
    xanio.setAttribute("selected",true);
    if(ianios == 0){
	id("filtroAnio").setAttribute("value", anios);
	id("filtroAnio").setAttribute("label", anios);
    }

    xlistitem.appendChild( xanio);
    ianios ++;
}

function VaciarAnioArqueos(){
    var xlistitem = id("elementosanio");
    var iditem;
    var t = 0;
    
    while( el = id("anio_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    ianios = 0;
}

function cargarDatosDefault(){
    var fechadesde = calcularFechaActual('fecha');
    var afecha     = fechadesde.split("-");
    afecha[1]      = parseInt(afecha[1]);
    
    id("filtroMes").value = parseInt(afecha[1]);
    id("filtroMes").setAttribute("selected",true);

    mesactual = parseInt(afecha[1]);
}

function calcularFechaActual(xvalue){
    var f = new Date();
    var fecha  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
    var hora   = f.getHours() + ":" + f.getMinutes() + ":" + f.getSeconds();
    var actual = (xvalue == 'fecha')? fecha : hora;

    return actual;
}

function actualizarArqueoGral(){
    MonedaActual = id("SeleccionMoneda").value;
    VaciarDeHijosTag("listaMovimientos","esMov");
    VaciarDeHijos("itemsArqueo");
    id("SeleccionArqueo").setAttribute("label","Elige arqueo ...");
    CambioListaArqueos();
    RegenCuentasBancarias();
}

function cleanformVisualizacion(){
    var sign = "S/.";//arqueo.Simbolo;
    var dato = '00.00';
    //Balances
    id("saldoInicialText").setAttribute("value", sign +" "+ formatDinero(dato) );
    id("ingresosText").setAttribute("value",  sign +" "+  formatDinero(dato) );
    id("egresosText").setAttribute("value",  sign +" "+  formatDinero(dato) );
    id("gastosText").setAttribute("value",  sign +" "+ formatDinero(dato) );
    id("aportacionesText").setAttribute("value",  sign +" "+ formatDinero(dato) );
    id("sustraccionesText").setAttribute("value",  sign +" "+ formatDinero(dato) );
    
    //Teorico de cierre
    id("TeoricoCierre").setAttribute("value",  sign +" "+ formatDinero(dato) );
    
    //Importe de cierre
    id("cierreCajaText").setAttribute("value", sign +" "+ formatDinero(dato) );
    
    //Importe descuadre

    var esdescuadre   = 0;
    var title_des     = (esdescuadre < 0)? "FALTANTE":"SOBRANTE";
    var descuadre_imp = 0;
    var dclass        = (esdescuadre < 0)? "cjafaltante plain":"cjasobrante plain";

    id("titledescuadre").setAttribute("value",title_des);
    id('descuadreCajaText').setAttribute("value", sign +" "+ formatDinero(descuadre_imp) );
    id('descuadreCajaText').setAttribute("class", dclass );

    var esestado_cja = "--OFF--";
    var esfecha_cja   = "--/--/----";

    id("estadoCajaTexto").setAttribute("value",esestado_cja);
    id("estadoCajaFecha").setAttribute("value", esfecha_cja);
    habilitabotones();
    changeColorEstadoCajaGral();
}

function verCuentaBancaria(){
    var index  = id("tabbox_operaciones").selectedIndex;
    var xidcta = (index == 1)? id("SeleccionCuentaBancariaSust").value:false;
    xidcta = (index == 2)? id("SeleccionCuentaBancariaIng").value:xidcta;
    xidcta = (index == 4)? id("SeleccionCuentaBancaria").value:xidcta;

    actualizarDatosCuentaBancaria(xidcta);
}

function actualizarDatosCuentaBancaria(xidcta){
    var op      = id("tabbox_operaciones").selectedIndex;
    var ares    = obtenerDatosCuentaBancaria(xidcta);

    var ingreso = formatDinero(parseFloat(ares[0]));
    var salida  = formatDinero(parseFloat(ares[1]));
    var saldo   = formatDinero(parseFloat(ares[2]));

    switch(op){
      case 1:
	id("saldoCuentaBancariaSust").value = saldo;
	break;
      case 2:
	id("saldoCuentaBancariaIng").value = saldo;
	break;
      case 4:
	
	id("importeTotalCuentaIngreso").value = ingreso;
	id("importeTotalCuentaSalida").value  = salida;
	id("importeTotalCuentaSaldo").value   = saldo;
	break;
    }
}

function obtenerDatosCuentaBancaria(xidcta){
    var	url  = "arqueoservices.php?modo=obtenerDatosCuentaBancaria"+
	       "&xidc="+xidcta;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;

    var ares = xres.split("~");
    return ares;
}


function RegenCuentasBancarias(){
    VaciarCuentaBancaria('itemsCuentaBancariaSust');
    VaciarCuentaBancaria('itemsCuentaBancariaIng');
    VaciarCuentaBancaria('itemsCuentaBancaria');
    VaciarCuentaBancaria('itemsCuentaBancariaDest');

    var	url  = "arqueoservices.php?modo=obtenerCuentasBancarias"+"&xid="+MonedaActual;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;

    var lines = xres.split("\n");
    var actual;
    var ln = lines.length-1;
    ln = (ln == 0)? 1:ln;

    var acta;
    for(var i=0;i<ln;i++){
	acta =  lines[i].split("=");
	if(acta[0]){
	    AddCuentaBancaria(acta[1],acta[0],"itemsCuentaBancariaSust");
	    AddCuentaBancaria(acta[1],acta[0],"itemsCuentaBancariaIng");
	    AddCuentaBancaria(acta[1],acta[0],"itemsCuentaBancaria");
	    AddCuentaBancaria(acta[1],acta[0],"itemsCuentaBancariaDest");
	}else{

	    id("SeleccionCuentaBancariaSust").value = 0;
	    id("SeleccionCuentaBancariaIng").value = 0;
	    id("SeleccionCuentaBancaria").value = 0;
	    id("SeleccionCuentaBancariaDest").value = 0;

	    id("SeleccionCuentaBancariaSust").setAttribute("label",'Seleccione...');
	    id("SeleccionCuentaBancariaIng").label = 'Seleccione...';
	    id("SeleccionCuentaBancaria").label = 'Seleccione...';
	    id("SeleccionCuentaBancariaDest").label = 'Seleccione...';
	}
    }
    verCuentaBancaria();
}

var icta     = 0;
var ictasust = 0;
var ictaing  = 0;
var ictadest = 0;

function AddCuentaBancaria(xid,xcta,item){
    var xlistitem = id(item);

    if(item == 'itemsCuentaBancariaSust'){ 
	var xidelemt = 'cta_sust_'+ictasust;
	var xvalue = xid;
	var xlabel = xcta;
    }
    if(item == 'itemsCuentaBancariaIng'){
	var xidelemt = 'cta_ing_'+ictaing;
	var xvalue = xid;
	var xlabel = xcta;
    }
    if(item == 'itemsCuentaBancaria'){
	var xidelemt = 'cta_'+icta;
	var xvalue = xid;
	var xlabel = xcta;
    }

    if(item == 'itemsCuentaBancariaDest'){
	var xidelemt = 'cta_dest_'+ictadest;
	var xvalue = xid;
	var xlabel = xcta;
    }
    
    if(ictadest == 0 && item == 'itemsCuentaBancariaDest'){
	var xcuenta = document.createElement("menuitem");
	xcuenta.setAttribute("id",xidelemt);
	
	xcuenta.setAttribute("value",0);
	xcuenta.setAttribute("label",'Seleccione...');

	xlistitem.appendChild( xcuenta);
	ictadest++;
	xidelemt = 'cta_dest_'+ictadest;
    }

    var xcuenta = document.createElement("menuitem");
    xcuenta.setAttribute("id",xidelemt);
	
    xcuenta.setAttribute("value",xvalue);
    xcuenta.setAttribute("label",xlabel);

    xlistitem.appendChild( xcuenta);

    if(item == 'itemsCuentaBancariaSust'){ 
	if(Local.CuentaBancaria == xvalue)
	    id("SeleccionCuentaBancariaSust").value = Local.CuentaBancaria;
	ictasust++;
    }
    if(item == 'itemsCuentaBancariaIng'){
	if(Local.CuentaBancaria == xvalue)
	    id("SeleccionCuentaBancariaIng").value = Local.CuentaBancaria;
	ictaing++;
    }
    if(item == 'itemsCuentaBancaria'){
	if(Local.CuentaBancaria == xvalue)
	    id("SeleccionCuentaBancaria").value = Local.CuentaBancaria;
	icta++;
    }
    if(item == 'itemsCuentaBancariaDest'){
	id("SeleccionCuentaBancariaDest").value = 0;
	ictadest++;
    }
}

function VaciarCuentaBancaria(item){
    var xlistitem = id(item);
    var iditem;
    var t = 0;
    
    if(item == 'itemsCuentaBancariaSust')
	var xidelemt = 'cta_sust_';
    
    if(item == 'itemsCuentaBancariaIng')
	var xidelemt = 'cta_ing_';
    
    if(item == 'itemsCuentaBancaria')
	var xidelemt = 'cta_';

    if(item == 'itemsCuentaBancariaDest')
	var xidelemt = 'cta_dest_';
    
    while( el = id(xidelemt + t) ) {
	if (el)	xlistitem.removeChild( el ) ;
	t = t + 1;
    }
    
    if(item == 'itemsCuentaBancariaSust') ictasust = 0;
    if(item == 'itemsCuentaBancariaIng') ictaing   = 0;
    if(item == 'itemsCuentaBancaria') icta         = 0;
    if(item == 'itemsCuentaBancariaDest') ictadest = 0;
}

function registrarTrasferenciaBancaria(){
    var ctaOrigen  = id("SeleccionCuentaBancaria").value;
    var ctaDestino = id("SeleccionCuentaBancariaDest").value;
    var Concepto   = trim(id("conceptoTextTransferencia").value);
    var Importe    = id("importeTransferencia").value;
    var Saldo      = id("importeTotalCuentaSaldo").value;

    if(ctaOrigen == ctaDestino || ctaDestino == 0)
	return alert("gPOS:  Transferencia Bancaria \n\n  - Seleccione cuenta bancaria destino diferente" );

    if(parseFloat(Importe) > parseFloat(Saldo) )
	return alert("gPOS:  Transferencia Bancaria \n\n  - EL Importe de Transferencia debe ser menor o igual al saldo de la cuenta origen" );

    if(parseFloat(Importe) <= 0 || !Importe)
	return alert("gPOS:  Transferencia Bancaria \n\n  - Ingrese el importe a transferir");

    var	url  = "arqueoservices.php?modo=registrarTransferenciaBancaria"+
               "&ctaorig="+ctaOrigen+
	       "&ctadest="+ctaDestino+
	       "&concepto="+Concepto+
	       "&importe="+Importe;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = xrequest.responseText;

    if(xres) return;

    verCuentaBancaria();
    cleanFormMovimientoBancaria();
}

function cleanFormMovimientoBancaria(){
    id("SeleccionCuentaBancariaDest").value = 0;
    id("conceptoTextTransferencia").value = '';
    id("importeTransferencia").value = '';
}

function CogeNroCuenta(){
    popup("../pagoscobros/selcuentabancaria.php?modo=cuenta&xidprov=0&xcta=0",'cuenta');
}

function changeNroCuenta( quien, txtcuenta) {
    //id("SeleccionCuentaBancaria").value     = quien.value;
}
