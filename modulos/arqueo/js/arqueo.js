var SubsidiarioPost   = "";
var IdSubsidiarioPost = 0;;

function id(nombreCosa){
	return document.getElementById(nombreCosa);
}

var xrequestArqueo = false;
var arqueoCargandose = 0;
/*
function CambioArqueo(IdArqueo){
	
	log("Solicitando CambioArqueo() para arqueo:"+IdArqueo);
	
	arqueoCargandose = IdArqueo; 
	
	var	url = "arqueoservices.php";

	var cjadata = "&modo=getArqueo&IdArqueo=" + IdArqueo + "&" + Math.random();
	xrequestArqueo = new XMLHttpRequest();
	
	
	xrequestArqueo.open("POST",url,true);
	xrequestArqueo.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequestArqueo.onreadystatechange = RecibeArqueo;
	xrequestArqueo.send(cjadata);
	
}


function RecibeArqueo() {
	if (xrequestArqueo.readyState!=4) 
			return;			
	//TODO: procesar aqui un arqueo que se solicitu su carga
	
    log("Recibiendo arqueo :"+arqueoCargandose);
	
	Local.IdArqueoActual = arqueoCargandose;	
}
*/
/*------------------------------------------*/

var xrequestListaArqueos = false;

function CambioListaArqueos(){
	//...

	log("<-- Solicitando la lista de arqueos de local:"+Local.IdLocalActivo);

        var cjadata = "";
	var	url = "arqueoservices.php";
	cjadata = "&modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
	xrequestListaArqueos = new XMLHttpRequest();
	
	xrequestListaArqueos.open("POST",url,true);
	xrequestListaArqueos.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequestListaArqueos.onreadystatechange = RecibeListaArqueos;
	xrequestListaArqueos.send(cjadata);
}

var UltimosArqueos;
var IdArqueo2RefUltimos;

var esRecibidaListaArqueos = false;


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
	
	for( t=0; t<10; t++){
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

var esCajaSol;
function ActualizarComboArqueos(){
	var idarqueo, fecha, arqueo,t, IdArqueo,xmenu;
	VaciarDeHijos("itemsArqueo");
	var padre = id("itemsArqueo");
	
	if(!padre){
		return alert("Fallo de formato. Recargue la pagina");
	}
    var cantSol = 0; 
	for(t=0;t<10;t++){
		//..
		arqueo =  UltimosArqueos["arqueo_"+t];
		
		if (arqueo){		
			//log("load arqueo:"+arqueo.toSource());
//			fecha = arqueo["FechaApertura"];
			fecha = arqueo["FechaCierre"];
			idarqueo = arqueo["IdArqueo"];
		    
		        (arqueo.esCerrada == 0)? cantSol++:false;

			xmenu = document.createElement("menuitem");
			xmenu.setAttribute( "label",idarqueo + "-" + datetimeToFechaCastellano(fecha) );
			xmenu.setAttribute( "id-referencia",idarqueo );
			xmenu.setAttribute( "id","arqueo_"+idarqueo );			
			xmenu.setAttribute( "oncommand","CargarArqueoSeleccionado("+t+");" );
			
			id("itemsArqueo").appendChild( xmenu );
			log("xul<-- mostrando.."+idarqueo);
		}
	}
    esCajaSol = cantSol;

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

var FechaCaja    = "";
var IdArqueoCaja = 0;

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

var xrequestListaMovimientos = false;
var MovimientosDineros = false;


function CambioListaMovimientos(IdArqueo){
	
	if (!IdArqueo) {
		//return alert('Arqueo inexistente');
		return;//Si no hay arqueo definido, no hay datos que cargar
	}


	//var	url = "arqueoservices.php?modo=getListaUltimosDiez&IdLocal="+Local.IdLocalActivo+"&r=" + Math.random();
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
	while( mov = MovimientosDineros["mov_"+t] ) {
	
		var xrow = document.createElement("listitem");
		xrow.setAttribute("esMov",true);

		xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
		xcell.setAttribute("label",datetimeToFechaCastellano(mov.FechaInsercion) );

		xrow.appendChild(xcell);
		
		xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	    xcell.setAttribute("label",mov.TipoOperacion.toUpperCase()+' - '+mov.PartidaCaja.toUpperCase() );

		xrow.appendChild(xcell);

		xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
		xcell.setAttribute("label",mov.Concepto );

		xrow.appendChild(xcell);

		xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
		xcell.setAttribute("label",formatDinero(mov.Importe) );
  	        xcell.setAttribute("style","text-align:right;");

		xrow.appendChild(xcell);

	        xcell = document.createElement("listcell"); xcell.setAttribute("esMov",true);
	        xcell.setAttribute("label",mov.Identificacion );
	        xcell.setAttribute("style","text-align:center;");
	
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


function CambioAuxliaresDeInterface(){

}



function Comando_CerrarCaja(){
    

	var p = confirm('gPOS: '+po_quierecerrar);
	if (!p) return;//Abortado por decision del usuario 

	Comando_ArqueoCaja();
	
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
function OLD_Comando_CerrarCaja(){

}

/*---------------------------------------------------*/



function Comando_ArqueoCaja(){

    var p = prompt(po_importereal,"0");	
    if(isNaN(p)||p<0)
        return;

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
    var	url      = "arqueoservices.php";
    var xrequest = new XMLHttpRequest();
    var cjadata  = "";
    var msj      = "";
    var fechacaja    = FechaCaja;
    var idarqueocaja = IdArqueoCaja;
    var cantidad, concepto, documento, codigodoc, proveedor, partida;

    if(esCajaSol == 0)
	return alert("gPOS: Caja \n\n - La caja está cerrada \n - Debe abrir para realizar la operacion");

    switch(op){
    case 'Aportacion':
	cantidad     = id("importeText").value;
	concepto     = id("conceptoText").value;
	partida      = id("SeleccionPartidaAport").label;
	msj          = "Se hizo un aporte a la caja: ";
	break;

    case 'Sustraccion':
	cantidad     = id("importeTextSubs").value;
	concepto     = id("conceptoTextSubs").value;
	partida      = id("SeleccionPartidaSust").label;
	msj          = "Se hizo una sutraccion de la caja: ";
	break;

    case 'Ingreso':
	cantidad     = id("importeTextIngreso").value;
	concepto     = id("conceptoTextIngreso").value;
	partida      = id("SeleccionPartidaIngreso").label;
	msj          = "Se hizo un ingreso a la caja: ";
	break;

    case 'Gasto':
	cantidad     = id("importeTextGasto").value;
	concepto     = id("conceptoTextGasto").value;
	partida      = id("SeleccionPartidaGasto").label;
	documento    = id("SeleccionDocumentoGasto").label;
	codigodoc    = id("CodigoTextGasto").value;
	proveedor    = id("EmpresaTextGasto").value;
	concepto     = concepto+' '+documento+' '+codigodoc+' '+proveedor;
	msj          = "Se hizo un gasto de la caja: ";
	break;
    }

    codigodoc = (codigodoc)? codigodoc:"";
    proveedor = (proveedor)? proveedor:0;
    concepto  = concepto.toUpperCase();

    if(partida == 'Nueva partida')
	partida = "";
    
    cjadata =  "&modo=hacerOperacionDinero&xidl="+Local.IdLocalActivo+
	       "&cantidad="+escape(cantidad)+
               "&concepto="+encodeURIComponent(concepto)+
	       "&fcaja="+fechacaja+
	       "&xidacg="+idarqueocaja+
	       "&op="+op+
	       "&partida="+partida+
               "&r=" + Math.random();

    var val = validarOperacionCaja(partida,cantidad,concepto);

    if(val)
	return;

    RegistrarPartidaOperacion(partida,op);

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);		 

    if(isNaN(xrequest.responseText))
	return alert(po_servidorocupado);

    alert("gPOS: \n\n "+msj+cMoneda[1]['S']+" "+formatDinero(cantidad) );
    
    CleanFormOperacion();
    CargarArqueoSeleccionado(0);

}

function RegistrarPartidaOperacion(partida,op){
    var	url      = "arqueoservices.php";
    var xrequest = new XMLHttpRequest();
    var cjadata  = "";
    
    cjadata =  "&modo=AltaPartidaCaja&xidl="+Local.IdLocalActivo+
	       "&op="+op+
  	       "&partida="+partida;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
    xrequest.send(cjadata);

    var res = (xrequest.responseText);
    if(res == 0)
	ActualizarListaPartida(partida,res);
}

function ActualizarListaPartida(Partida,res){
    var lista = id("itemsPartida");

    xPartida = document.createElement("menuitem");
    xPartida.setAttribute("value",res);
    xPartida.setAttribute("label",Partida);

    lista.appendChild( xPartida );		
}

function validarOperacionCaja(partida,cantidad,concepto){
    var caja      = (esCajaSol == 0)? true:false;
    var msj       = "gPOS:   Caja \n\n";
    var cjadata      = "";

    var impteop   = (cantidad <= 0 || cantidad == "")? true:false;
    var part      = partida.slice(0,5);
    part          = (part == "Nueva" || partida == "" || part == "Elige")? true:false;
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


/*---------------------------------------------------*/


function TestFuncionamiento(){	
	alert(xrequestListaArqueos.toSource());		
}

/* --------------------------------------------------*/

function onLoadFormulario(){
	CambioListaArqueos();
	
	setTimeout("DemonioPrimeraApertura()",200);

}



function DemonioPrimeraApertura(){
	
	if (esRecibidaListaArqueos) {
	    //cargamos el ultimo arqueo que tenemos.
	    CargarArqueoSeleccionado(0);
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

    var estado  = id("estadoCajaTexto").getAttribute("value");
    var xabrir  = (esCajaSol == 1)? true:false;
    var xcerrar = false;
    var xarqueo = false;
    var xbtnop  = (esCajaSol == 0)? true:false;
    var utilcja = true;


    if(estado == "CERRADA" || estado == "--OFF--"){
	xcerrar = true;
	xarqueo = true;
	//utilcja = false;
    }

    id("botonAbrir").setAttribute("disabled",xabrir);
    id("botonCerrar" ).setAttribute("disabled",xcerrar);
    id("botonArqueo").setAttribute("disabled",xarqueo);
    id("btnAporte").setAttribute("disabled",xbtnop);
    id("btnSustracion").setAttribute("disabled",xbtnop);
    id("btnIngreso").setAttribute("disabled",xbtnop);
    id("btnGasto").setAttribute("disabled",xbtnop);

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
    var extra = "dialogWidth:" + "400" + "px;dialogHeight:" + "520" + "px"; 
    window.showModalDialog(url,tipo,extra);
}

function auxSubsidiarioHab(){
    var url   = '../subsidiarios/selsubsidiario.php?modo=subsidiariopost';
    var tipo  = 'proveedorhab';
    var extra = "dialogWidth:" + "350" + "px;dialogHeight:" + "350" + "px";
    window.showModalDialog(url,tipo,extra);

    if(SubsidiarioPost) {
	id("EmpresaTextGasto").value = SubsidiarioPost;
	id("IdSubsidiario").value = IdSubsidiarioPost;
    }
}

function changeColorEstadoCaja(){
    var stdocja = id("estadoCajaTexto").value;

    var stle = (stdocja == 'ABIERTA')? "cjagralabierta plain big":"cjagralcerrada plain big";

    id("estadoCajaTexto").setAttribute('class',stle);
    id("estadoCajaFecha").setAttribute('class',stle);
    
}
