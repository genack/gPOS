var SubsidiarioPost   = "";
var IdSubsidiarioPost = 0;
var cConcepto         = "";
var cPartida          = "";
var cIdOperacionCaja  = 0;
var cCodPartida       = "0";
var cOperacion        = "";
var mesactual         = 0;
var xtransfgral       = 0;

var xrequestArqueo   = false;
var arqueoCargandose = 0;

var xrequestListaArqueos = false;

var UltimosArqueos;
var IdArqueo2RefUltimos;
var esRecibidaListaArqueos = false;

var esCajaSol;
var UlIdArqueoCaja;

var FechaCaja    = "";
var IdArqueoCaja = 0;
var cEstadoCaja  = "";

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
    log("<-- Solicitando la lista de arqueos de local:"+Local.IdLocalActivo);
    var anio = id("filtroAnio").value;
    var mes  = id("filtroMes").value;
    var cjadata = "";
    var	url = "arqueoservices.php";
    cjadata = "&modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&anio="+anio+"&mes="+mes+"&r=" + Math.random();
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
    
    esRecibidaListaArqueos = true
    setTimeout("DemonioPrimeraApertura()",200);
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
    var cantSol = 0; 
    for(t=0;t<31;t++){
	//..
	arqueo =  UltimosArqueos["arqueo_"+t];
	
	if (arqueo){		
	    //log("load arqueo:"+arqueo.toSource());
	    //			fecha = arqueo["FechaApertura"];
	    fecha    = arqueo["FechaCierre"];
	    fechaap  = arqueo["FechaApertura"];
	    idarqueo = arqueo["IdArqueo"];

	    if(t==0)
		UlIdArqueoCaja = idarqueo;
	    
	    (arqueo.esCerrada == 0)? cantSol++:false;

	    xmenu = document.createElement("menuitem");
	    xmenu.setAttribute( "value",t);
	    xmenu.setAttribute( "label",idarqueo + ". " +datetimeToFechaCastellano(fechaap) +" --- "+ datetimeToFechaCastellano(fecha));
	    xmenu.setAttribute( "id-referencia",idarqueo );
	    xmenu.setAttribute( "id","arqueo_"+idarqueo );			
	    xmenu.setAttribute( "oncommand","CargarArqueoSeleccionado("+t+");" );
	    
	    id("itemsArqueo").appendChild( xmenu );
	    log("xul<-- mostrando.."+idarqueo);
	}
    }
    esCajaSol = cantSol;
    if(!UltimosArqueos["arqueo_"+0]){
	MonedaActual = 1;
	IdArqueoCaja = 0;
	cEstadoCaja  = '';
	xmenu = document.createElement("menuitem");
	xmenu.setAttribute( "label","Elige arqueo ...");
	xmenu.setAttribute( "value","0");
	id("itemsArqueo").appendChild( xmenu );
    }
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
    
    var url  = "arqueoservices.php";
    cjadata  = "&modo=getDatosActualizadosArqueo&IdArqueo="+arqueo.IdArqueo+
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
    FechaCaja    = arqueo.FechaApertura;
    IdArqueoCaja = arqueo.IdArqueo;
    cEstadoCaja  = (arqueo.esCerrada == 1)? 'CERRADA':'ABIERTA';
    
    log("Nos traemos del server:"+xrequest.responseText);		 	 
    log("Habia en casa:"+arqueo.toSource());
    
    //ActualizarVisualizacion(arqueo);
    
    ActualizarVisualizacion(arqueoactualizado);
    CambioListaMovimientos( arqueo.IdArqueo );	
}

function ActualizarVisualizacion(arqueo){
    log("I: actualizando datos de arqueo:"+arqueo.toSource());
    var sign = cMoneda[1]['S'];
    
    //Balances
    id("saldoInicialText").setAttribute("value",  sign +" "+ formatDinero(arqueo.ImporteApertura) );
    id("ingresosText").setAttribute("value",  sign +" "+  formatDinero(arqueo.ImporteIngresos) );
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
    
    id('descuadreCajaText').setAttribute("value", sign +" "+ formatDinero(descuadre_imp));
    id('descuadreCajaText').setAttribute("class", dclass );
    id("titledescuadre").setAttribute("value",title_des);
    //id("titledescuadre").setAttribute("class",dclass);
    
    //Utilidad Venta
    id('utilidadVentaCajaText').setAttribute("value", sign +" "+ formatDinero(arqueo.UtilidadVenta) );
    
    //Fecha
    id("estadoCajaFecha").setAttribute("value", datetimeToFechaCastellano(arqueo.FechaCierre));
    
    //Estado abierto/cerrado
    
    var esestado_cja = (arqueo.esCerrada>0)? "CERRADA":"ABIERTA";
    var esfecha_cja   = (arqueo.esCerrada>0)? arqueo.FechaCierre:arqueo.FechaApertura

    id("estadoCajaTexto").setAttribute("value",esestado_cja);
    id("estadoCajaFecha").setAttribute("value", datetimeToFechaCastellano(esfecha_cja));

    habilitabotones();
    changeColorEstadoCaja();
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
    
    
    //var url = "arqueoservices.php?modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
    var cjadata = "";
    var url = "arqueoservices.php";
    cjadata = "&modo=getMovimientos&IdArqueo="+IdArqueo+"&r=" + Math.random();
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
    xtransfgral = 0;
    while( mov = MovimientosDineros["mov_"+t] ) {
	var xrow = document.createElement("listitem");
	xrow.setAttribute("esMov",true);
	xrow.setAttribute("value",mov.IdOperacionCaja);
        xrow.setAttribute("id","listamovimientos_"+mov.IdOperacionCaja);
        xrow.setAttribute("oncontextmenu","seleccionarlistamovimientos("+mov.IdOperacionCaja+")");
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",datetimeToFechaCastellano(mov.FechaPago) );
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.TipoOperacion.toUpperCase()+' - '+mov.PartidaCaja.toUpperCase() );
	xcell.setAttribute("value",mov.TipoOperacion);
	xcell.setAttribute("id","tpv_operacion_"+mov.IdOperacionCaja);
	
	xrow.appendChild(xcell);

	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Cliente);

	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Concepto );
	xcell.setAttribute("id","tpv_concepto_"+mov.IdOperacionCaja );
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",formatDinero(mov.Importe) );
  	xcell.setAttribute("style","text-align:right;");
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.Identificacion );
	xcell.setAttribute("style","text-align:center;");
	
	xrow.appendChild(xcell);
	
	xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	xcell.setAttribute("label",mov.PartidaCaja );
	xcell.setAttribute("value",mov.Codigo );
	xcell.setAttribute("id","tpv_partida_"+mov.IdOperacionCaja );
 	xcell.setAttribute("collapsed","true" );

	xrow.appendChild(xcell);
	
	listaMov.appendChild(xrow);			
	
	t++;
	
	if(mov.Codigo == "S112")
	    xtransfgral++;
    }
}

function seleccionarlistamovimientos(linea){
    var lista = id("listaMovimientos");
    var fila  = id("listamovimientos_"+linea);
    lista.selectItem(fila);
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

function hacerTransferenciaCjaGral(){
    if(xtransfgral == 0){
	var xt = confirm('gPOS:  Caja TPV \n\n  Desea realizar transferencia a la caja general?');

  	if(xt){
	    var teoricocierre = id("TeoricoCierre").value;
	    var saldoinicial  = id("saldoInicialText").value;
	    teoricocierre = teoricocierre.split(" ");
	    saldoinicial  = saldoinicial.split(" ");
	    teoricocierre = parseFloat(teoricocierre[1]);
	    saldoinicial  = parseFloat(saldoinicial[1]);
	    var transf = teoricocierre - saldoinicial;
	    var p = prompt("Importe transferencia",transf);
	    
	    if(!p) return false;
	    
	    if(isNaN(p)||p<0.01)
		return false;
	    
	    var cantidad = parseFloat(p);
	    var concepto = "Tranferencia a caja general";
	    var idarqueocaja = IdArqueoCaja;
	    var fechacaja  = FechaCaja;
	    var op         = 'Sustraccion';
	    var partida    = "Transferencia a caja general";
	    var codpartida = "S112";
	    var res = "";
		
	    var res = entregarOperacionPartidaCaja(cantidad,concepto,fechacaja,
						   idarqueocaja,op,partida,codpartida);

	    if(res == "cjacerrada"){
		alert("gPOS   Operación Caja  \n\n  - La caja está cerrada");
		return false;
	    }
		
	    if(res == "cjagralcerrada"){
		alert("gPOS   Operación Transferencia \n\n  - No se registró la operación \n  - Caja General está cerrada");
		return false;
	    }

	    if(res == "cjacentralcerrada"){
		alert("gPOS   Operación Transferencia \n\n  - No se registró la operación \n  - Caja Central está cerrada");
		return false;
	    }

	    CargarArqueoSeleccionado(0);
	    return true;
	}
	else
	    return true;
	}
    else 
	return true;
}

function Comando_CerrarCaja(){

    if(!hacerTransferenciaCjaGral()) return;
    
    var p = confirm('gPOS: '+po_quierecerrar);
    if (!p) return;//Abortado por decision del usuario 
    
    if(!Comando_ArqueoCaja()) return;
    
    //Guardamos datos actuales arqueo como actual
    // ..y creamos el siguiente.	 
    
    Local.IdArqueoActual = ActualizarDatosDeCierre();	
    parent.Local.esCajaCerrada  = 1;
    
    //Quitamos presupuestos preventa del dia
    setStatusPresupuestoCierreCaja('CleanPreventa');
    //parent.id("botonImprimir").setAttribute("disabled",true);
    //parent.habilitabotonvender(1,true);
    //Recargamos 		
    // No descomentar ActualizarComboArqueos();
    //document.location = "arqueo2.php?r="+Math.random();
    esRecibidaListaArqueos = false; 
    onLoadFormulario();
    xtransfgral = 0;
}

function setStatusPresupuestoCierreCaja(Opcion){
    var cadena, filas, resultado;
    var xrequest = new XMLHttpRequest();
    var url = 
	"../../services.php?"+
	"modo=setStatusPresupuestoTPV"+"&"+
	"op="+Opcion;
    xrequest.open("GET",url,false);
    xrequest.send(null);
    resultado = xrequest.responseText;
    resultado = parseInt(resultado);
    if (!resultado )	
            alert(po_error+"\n - Presupuesto Cierre caja");	
    //alert(resultado);

    //Reset preventa
    var combo =parent.document.getElementById("itemsPreventa");
    var selcombo= parent.document.getElementById("SelPreventa");
    while (combo.firstChild) {
	combo.removeChild(combo.firstChild);
    }
    selcombo.setAttribute("label", "Elije ticket....");
    selcombo.setAttribute("flex", "1");
}

function Comando_AbrirCaja(){
    var p = confirm("gPOS:  Desea Abrir Caja?");
    if (!p) return;//Abortado por decision del usuario 
    //Comando_ArqueoCaja();
    
    //Guardamos datos actuales arqueo como actual
    // ..y creamos el siguiente.	 
    Local.IdArqueoActual = ActualizarDatosDeApertura();	
    // Hbilita boton imprimir
    //parent.id("botonImprimir").removeAttribute("disabled");
    //parent.habilitabotonvender(0,true);
    //Recargamos 		
    // No descomentar ActualizarComboArqueos();
    
    // Asociar preventa 
    setStatusPresupuestoCierreCaja('AsociarPreventa');	
    //document.location = "arqueo2.php?r="+Math.random();
    esRecibidaListaArqueos = false; 
    onLoadFormulario();
}

function Comando_ArqueoCaja(){
    var teoricocierre = id("TeoricoCierre").value;
    teoricocierre = teoricocierre.split(" ");
    teoricocierre = parseFloat(teoricocierre[1]);

    var p = prompt(po_importereal,teoricocierre);

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
    id("SeleccionArqueo").value = 0;
    return true;
}

function ActualizarDatosDeCierre(){

   log("arquearYAbrirNuevaCaja para local:"+Local.IdLocalActivo);
    var cjadata = "";
    var	url = "arqueoservices.php";
    cjadata = "&modo=arquearYAbrirNuevaCaja&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
    
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
    var url = "arqueoservices.php";
    cjadata = "&modo=soloAbrirCaja&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
    
    var xrequest = new XMLHttpRequest();
    
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);	
    
    var res = xrequest.responseText;
    if(res == '-1')
        alert("gPOS: \n\n La Caja Está Abierta");
    return 0;

}

//
// Actualiza la cantidad en caja en el servidor del ultimo arqueo 
//

function sv_actualizarServicio(CantidadCierre){
   log("Marcando nueva cantidad en caja.."+CantidadCierre);
    
    var cjadata = "";
    var	url = "arqueoservices.php";
    cjadata = "&modo=actualizarCantidadCaja&IdLocal="+Local.IdLocalActivo+
	      "&cantidad="+escape(CantidadCierre)+"&r=" + Math.random();
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
    var msj      = "";
    var msj1     = "";
    var fechacaja    = FechaCaja;
    var idarqueocaja = IdArqueoCaja;
    var cantidad, concepto, documento, codigodoc, proveedor, partida, codpartida;

    if(esCajaSol == 0)
	return alert("gPOS: Operación Caja TPV \n\n - La caja está cerrada \n - Debe abrir para realizar la operación");

    switch(op){
    case 'Aportacion':
	cantidad     = id("importeText").value;
	concepto     = id("conceptoText").value;
	partida      = id("SeleccionPartidaAport").label;
	codpartida   = id("SeleccionPartidaAport").value;
	msj          = "Se hizo un aporte a la caja: ";
	break;

    case 'Sustraccion':
	cantidad     = id("importeTextSubs").value;
	concepto     = id("conceptoTextSubs").value;
	partida      = id("SeleccionPartidaSust").label;
	codpartida   = id("SeleccionPartidaSust").value;
	msj          = "Se hizo una sutracción de la caja: ";
	if(trim(codpartida) == 'S112'){
	    msj1 = "\n - Se hizo un ingreso a la caja general: "+cMoneda[1]['S']+" "+formatDinero(cantidad);
	    if(Local.CajaCentral)
		msj1 += "\n - Se hizo una sustracción a la caja central: "+cMoneda[1]['S']+" "+formatDinero(cantidad);
	}
	break;

    case 'Ingreso':
	cantidad     = id("importeTextIngreso").value;
	concepto     = id("conceptoTextIngreso").value;
	partida      = id("SeleccionPartidaIngreso").label;
	codpartida   = id("SeleccionPartidaIngreso").value;
	msj          = "Se hizo un ingreso a la caja: ";
	break;

    case 'Gasto':
	cantidad     = id("importeTextGasto").value;
	concepto     = id("conceptoTextGasto").value;
	partida      = id("SeleccionPartidaGasto").label;
	codpartida   = id("SeleccionPartidaGasto").value;
	documento    = id("SeleccionDocumentoGasto").label;
	codigodoc    = id("CodigoTextGasto").value;
	proveedor    = id("EmpresaTextGasto").value;
	concepto     = trim(concepto+' '+documento+' '+codigodoc+' '+proveedor);
	msj          = "Se hizo un gasto de la caja: ";

	if(!codigodoc || !proveedor)
	    return alert("gPOS: \n\n   - Ingrese el código del comprobante y seleccione la empresa");
	break;
    }

    codigodoc = (codigodoc)? codigodoc:"";
    proveedor = (proveedor)? proveedor:0;
    //concepto  = concepto.toUpperCase();

    var val = validarOperacionCaja(codpartida,cantidad,concepto);

    if(val)
	return;

    var p = confirm('gPOS:   Operación Caja TPV \n\n  - Va registrar operación caja, desea continuar?');
    if (!p) return;

    var res = entregarOperacionPartidaCaja(cantidad,concepto,fechacaja,idarqueocaja,op,
					   partida,codpartida);

    if(res == "cjacerrada"){
	alert("gPOS   Operación Caja  \n\n  - La caja está cerrada");
    }

    if(res == "cjagralcerrada"){
	alert("gPOS   Operación Transferencia \n\n  - No se registró la operación \n  - Caja General está cerrada");
    }

    if(res == "cjacerrada"){
	alert("gPOS   Operación Caja \n\n  - Partida no seleccionada");
    }

    if(res == "cjacentralcerrada"){
	alert("gPOS   Operación Transferencia \n\n  - No se registró la operación \n  - Caja Central está cerrada");
	return false;
    }

    if(res == "exito")
	alert("gPOS:   Operación Caja\n\n - "+msj+cMoneda[1]['S']+" "+formatDinero(cantidad)+msj1 );
    
    CleanFormOperacion();
    CargarArqueoSeleccionado(0);
    id("SeleccionArqueo").value = 0;
}

function validarOperacionCaja(codpartida,cantidad,concepto){
    var caja      = (esCajaSol == 0)? true:false;
    var msj       = "gPOS:   Caja \n\n";
    var cjadata   = "";

    var impteop   = (cantidad <= 0 || cantidad == "")? true:false;
    var part      = (codpartida == '0' || !codpartida)? true:false;
    var cpto      = (concepto == "")? true:false;

    cjadata += (part)  ? " - Partida":"";
    cjadata += (cpto) ? "\n - Concepto":"";
    cjadata += (impteop)  ? "\n - Importe":"";

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

function entregarOperacionPartidaCaja(cantidad,concepto,fechacaja,idarqueocaja,op,partida,
				      codpartida){

    var	url      = "arqueoservices.php";
    var xrequest = new XMLHttpRequest();
    var cjadata  = "";
    cjadata =  "&modo=hacerOperacionDinero&xidl="+Local.IdLocalActivo+
	       "&cantidad="+escape(cantidad)+
               "&concepto="+encodeURIComponent(concepto)+
	       "&fcaja="+fechacaja+
	       "&xidacg="+idarqueocaja+
	       "&op="+op+
	       "&partida="+partida+
	       "&codpartida="+codpartida+
               "&r=" + Math.random();


    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);
    var res = xrequest.responseText;
    return res;
}

/*---------------------------------------------------*/


function TestFuncionamiento(){	
    alert(xrequestListaArqueos.toSource());		
}

/* --------------------------------------------------*/

function onLoadFormulario(){
    id("conceptoText").value = "";
    regenAnioArqueo();
    cargarDatosDefault();
    CambioListaArqueos();
    ContarElementosPartidas();    
    //setTimeout("DemonioPrimeraApertura()",200);

}

function DemonioPrimeraApertura(){
	
    if (esRecibidaListaArqueos) {
	//cargamos el ultimo arqueo que tenemos.
	CargarArqueoSeleccionado(0);
	id("SeleccionArqueo").value = 0;
	habilitabotones(); 		 	
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

function habilitaventa(){
    var xval    = (esCajaSol == 0)? 1:0;
    parent.habilitabotonvender(xval);
}

function habilitabotones(){
    var mes     = id("filtroMes").getAttribute("value");
    var estado  = cEstadoCaja;
    var utilcja = true;

    var xabrir  = (mes != mesactual)? true:false;
    var xcerrar = (mes != mesactual)? true:false;
    var xarqueo = (mes != mesactual)? true:false;
    var xbtnop  = (mes != mesactual)? true:false;

    switch(estado){
    case 'ABIERTA':
	xabrir  = true;
	break;
    case 'CERRADA':
	xabrir  = (IdArqueoCaja < UlIdArqueoCaja)? true:xabrir;
	xcerrar = true;
	xarqueo = true;
	xbtnop  = true;
	break;
    default :
	xabrir  = (IdArqueoCaja == 0 && mes == mesactual)? false:true;	
	xcerrar = true;
	xarqueo = true;
	xbtnop  = true;
    }

    id("botonAbrir").setAttribute("disabled",xabrir);
    id("botonCerrar" ).setAttribute("disabled",xcerrar);
    id("botonArqueo").setAttribute("disabled",xarqueo);
    id("btnAporte").setAttribute("disabled",xbtnop);
    id("btnSustracion").setAttribute("disabled",xbtnop);
    id("btnIngreso").setAttribute("disabled",xbtnop);
    id("btnGasto").setAttribute("disabled",xbtnop);

    id("botonAbrir").className = (xabrir)? "btn_disabled":"btn";
    id("botonCerrar").className = (xcerrar)? "btn_disabled":"btn";
    id("botonArqueo").className = (xarqueo)? "btn_disabled":"btn";

    id("row_utilidadventacajatitle").setAttribute("collapsed",utilcja);
    id("row_utilidadventacaja").setAttribute("collapsed",utilcja);
    habilitaventa();
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
    
}

function auxAltaSubsidiario(){
    var url   = '../../modsubsidiarios.php?modo=altapopup';
    var tipo  = 'altaproveedor';
    //var extra = "dialogWidth:" + "400" + "px;dialogHeight:" + "520" + "px"; 
    //window.showModalDialog(url,tipo,extra);
    popup(url,tipo);
}

function auxSubsidiarioHab(){
    var url   = '../subsidiarios/selsubsidiario.php?modo=subsidiariopost';
    var tipo  = 'proveedorhab';
    popup(url,tipo);
}

function loadSubsidiarioHab(){
    if(!SubsidiarioPost) return;
}

function changeColorEstadoCaja(){
    var stdocja = id("estadoCajaTexto").value;

    var stle = (stdocja == 'ABIERTA')? "cjagralabierta plain big":"cjagralcerrada plain big";

    id("estadoCajaTexto").setAttribute('class',stle);
    id("estadoCajaFecha").setAttribute('class',stle);
    
}

function exportarMovimientosCaja(xtipo){
    if(IdArqueoCaja == 0) return;
    var data = "&idarqueo="+IdArqueoCaja+
	       "&idlocal="+Local.IdLocalActivo;

    switch(xtipo){
    case 'PDF':
	var url = "../fpdf/imprimir_movimientoscaja.php?" + data;
	location.href=url;
	break;
    case 'CSV':
	var url = "../generadorlistados/exportarlistados.php?"+data+
                  "&modo="+"movimientoscaja";
	document.location=url;
	break;
    }
}

function ModificarOperacionCaja(){
    if(cCodPartida == "0") return;

    var p = prompt("Modificar concepto",cConcepto);

    if(!p) return false;

    if(p == '')
        return false;

    p = trim(p);
    
    var	url      = "arqueoservices.php?";
    var xrequest = new XMLHttpRequest();
    var msj      = "";

    var concepto, documento, codigodoc, proveedor;

    switch(cOperacion){
    case 'Aportacion':
	concepto     = p;//id("conceptoText").value;
	break;

    case 'Sustraccion':
	concepto     = p;//id("conceptoTextSubs").value;
	break;

    case 'Ingreso':
	concepto     = p;//id("conceptoTextIngreso").value;
	break;

    case 'Gasto':
	concepto     = p;//trim(id("conceptoTextGasto").value);
	documento    = id("SeleccionDocumentoGasto").label;
	codigodoc    = id("CodigoTextGasto").value;
	proveedor    = id("EmpresaTextGasto").value;
	concepto     = trim(concepto+', '+documento+' '+codigodoc+' '+proveedor);

	//if(!codigodoc || !proveedor)
	    //return alert("gPOS: \n\n   - Ingrese el código del comprobante y seleccione la empresa");
	break;
    }
    msj          = "Se guardó los cambios: ";

    codigodoc = (codigodoc)? codigodoc:"";
    proveedor = (proveedor)? proveedor:0;
    //concepto  = concepto.toUpperCase();

    cjadata =  "modo=modificaOperacionCaja&xidl="+Local.IdLocalActivo+
               "&concepto="+encodeURIComponent(concepto)+
	       "&xidoc="+cIdOperacionCaja;


    xrequest.open("GET",url+cjadata,false);
    xrequest.send(cjadata);
    var res = xrequest.responseText;
    var ares = res.split("~");

    if(ares[0] != '')
	return alert("gPOS:   Operación Caja \n\n" + " - "+ares[0]);
    
    alert("gPOS:  Operación Caja \n\n - "+ msj);
    id("tpv_concepto_"+cIdOperacionCaja).setAttribute("label",concepto);
    //habilitarOperacionCaja();
    CargarArqueoSeleccionado(0);
    id("SeleccionArqueo").value = 0;
}

function revisarOperacionCaja(){
    var idex = id("listaMovimientos").selectedItem;
    if(!idex) return;
    var xcpto = false;
    id("ConceptoOperacionCaja").removeAttribute("disabled");

    cConcepto   = id("tpv_concepto_"+idex.value).getAttribute("label");
    cPartida    = id("tpv_partida_"+idex.value).getAttribute("label");
    cCodPartida = id("tpv_partida_"+idex.value).getAttribute("value");
    cOperacion  = id("tpv_operacion_"+idex.value).getAttribute("value");
    cIdOperacionCaja = idex.value;

    if(cCodPartida == "0")
	xcpto = true;
    
    id("ConceptoOperacionCaja").setAttribute("disabled",xcpto);

    //habilitarOperacionCaja();
}

function editarOperacionCaja(){
    var op = cOperacion;
    var xselindex = 0;
    var xtabapor  = 'false';
    var xtabsust  = 'false';
    var xtabingr  = 'false';
    var xtabgast  = 'false';

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
	//id("conceptoTextGasto").value = cConcepto;
	id("SeleccionDocumentoGasto").value = cCodPartida;
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

    mostrarPanelOperacionesCaja('arqueo');
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

    id("btn"+xcommand).setAttribute("label",xlabel);
    id("btn"+xcommand).setAttribute("oncommand",'comando_HacerUnaOperacion('+'"'+xcommand+'"'+')');
}

function CogePartidaCaja(operacion){
    popup("../partidas/selpartidas.php?modo=partida&xidl=0"+"&xop="+operacion+"&cja="+parent.Local.TPV,'marca');
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
    var url = "../../services.php?modo=partidas&xidl=0"+"&xop="+operacion+"&cja="+parent.Local.TPV;
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res   = xrequest.responseText;
    var lines = res.split("\n");
    var ln    = lines.length-1;
    var actual;

    for(var t=0;t<=ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	AddPartidaLine(actual[0],actual[1],operacion);
    }
    
    if(operacion == 'Aportacion') id("SeleccionPartidaAport").value = 0;
    if(operacion == 'Sustraccion') id("SeleccionPartidaSust").value = 0;
    if(operacion == 'Ingreso') id("SeleccionPartidaIngreso").value = 0;
    if(operacion == 'Gasto') id("SeleccionPartidaGasto").value = 0;
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
	//xpartida.setAttribute("selected",'true');
	xlistitem.appendChild( xpartida);

	if(operacion == 'Aportacion'){ipartidasaportacion++; xpartidas = ipartidasaportacion;}
	if(operacion == 'Sustraccion'){ipartidassustraccion++;xpartidas=ipartidassustraccion;}
	if(operacion == 'Ingreso'){ipartidasingreso++;xpartidas = ipartidasingreso;}
	if(operacion == 'Gasto') {ipartidasgasto++;xpartidas = ipartidasgasto;}
    }
    if(valor){
    var xpartida = document.createElement("menuitem");
    xpartida.setAttribute("id",defpart + xpartidas);
    xpartida.setAttribute("value",valor);
    xpartida.setAttribute("label",nombre);
    xlistitem.appendChild( xpartida);

    if(operacion == 'Aportacion') ipartidasaportacion++;
    if(operacion == 'Sustraccion') ipartidassustraccion++;
    if(operacion == 'Ingreso') ipartidasingreso++;
    if(operacion == 'Gasto') ipartidasgasto++;}
}

function ContarElementosPartidas(){
    ipartidasaportacion  = id("SeleccionPartidaAport").itemCount;
    ipartidassustraccion = id("SeleccionPartidaSust").itemCount;
    ipartidasingreso     = id("SeleccionPartidaIngreso").itemCount;
    ipartidasgasto       = id("SeleccionPartidaGasto").itemCount;
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
    var xfecha = obtenerUltimaFechaCaja();
    xfecha     = (xfecha)? xfecha:calcularFechaActual('fecha');
    var afecha = xfecha.split("-");
    afecha[1]  = parseInt(afecha[1]);

    id("filtroMes").value = afecha[1];
    id("filtroMes").setAttribute("selected",true);
    id("filtroAnio").value = afecha[0];
    id("filtroAnio").setAttribute("selected",true);

    mesactual = parseInt(afecha[1]);
}

function calcularFechaActual(xvalue){
    var f = new Date();
    var fecha  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
    var hora   = f.getHours() + ":" + f.getMinutes() + ":" + f.getSeconds();
    var actual = (xvalue == 'fecha')? fecha : hora;

    return actual;
}

function actualizarArqueo(){
    id("SeleccionArqueo").value = 0;
    VaciarDeHijosTag("listaMovimientos","esMov");
    VaciarDeHijos("itemsArqueo");
    cleanformVisualizacion();
    CambioListaArqueos();
    setTimeout("precargaListadoArqueo()",500);
}

function precargaListadoArqueo(){
    var val = id('SeleccionArqueo');
    if(val.itemCount > 0) CargarArqueoSeleccionado(0);
}

function cleanformVisualizacion(){
    var sign = "S/.";//arqueo.Simbolo;
    var dato = '00.00';
    //Balances
    id("saldoInicialText").setAttribute("value", sign +" "+ formatDinero(dato) );
    id("ingresosText").setAttribute("value",  sign +" "+  formatDinero(dato) );
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
    changeColorEstadoCaja();
}

function obtenerUltimaFechaCaja(){
    var	url  = "arqueoservices.php?modo=obtenerUltimaFechaCaja";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = xrequest.responseText;

    return xres;
}

function mostrarPanelOperacionesCaja(xval){
    var xop    = (xval == 'arqueo')? 'operacion':'arqueo';
    var xlabel = (xval == 'arqueo')? 'Arqueo Actual':'Operaciones';
    var xvalue = (xval == 'arqueo')? false:true;
    var ximage = (xval == 'arqueo')? "../../img/gpos_arqueo.png":"../../img/gpos_tpvcaja_guardarpartida.png";
    var xcommand = "mostrarPanelOperacionesCaja('"+xop+"')";

    id("boxOperacionesCaja").setAttribute("collapsed",xvalue);
    id("boxArqueoActualCaja").setAttribute("collapsed",!xvalue);
    id("btnOperacionesCaja").setAttribute("label",xlabel);
    id("btnOperacionesCaja").setAttribute("oncommand",xcommand);
    id("btnOperacionesCaja").setAttribute("image",ximage);
}
