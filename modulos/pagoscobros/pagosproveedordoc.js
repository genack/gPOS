var cProveedor        = "";
var cIdProvHab        = 0;
var cFechaOperacion   = "";
var cFOperacion       = "";
var cHOperacion       = "";
var cModalidadPago    = '1';
var cModoPago         = "";
var cImporte          = 0;
var cFinanciera       = "";
var cCodOperacion     = "";
var cCtaEmpresa       = "";
var cCtaProveedor     = "";
var cNumDocumento     = "";
var cCambioMoneda     = 0;
var cIdMoneda         = 0;
var cObservaciones    = "";
var cEstado           = "";
var cCodigo           = 0;
var cSimbolo          = "";
var cTipoProveedor    = "";
var ProveedorPost     = false;
var IdProveedorPost   = 0;
var LocalPost         = false;
var IdLocalPost       = 0;
var cSaldo            = 0;

// Opciones Busqueda avanzada
var vEstado        = true;
var vMoneda        = true;
var vUsuario       = true;
var vFechaRegistro = true;
var vImpuesto      = true;
var vReferencia    = true;
var aDatoEmpresa   = new Array();
var aDatoProveedor = new Array();
var esNuevo        = true;

var ilineabuscadocumento     = 0;

var msj = false;

var id = function(name) { return document.getElementById(name); }

function VerPagoDocumento(){
    VaciarBusquedaPagoDocumento();
    BuscarPagoDocumento();
}

//Limpieza de Box
function VaciarBusquedaPagoDocumento(){
    var lista = id("listaPagoDocumento");

    for (var i = 0; i < ilineabuscadocumento; i++) { 
        kid = id("lineabuscadocumento_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscadocumento = 0;
}

//Busqueda 
function BuscarPagoDocumento(){
    VaciarBusquedaPagoDocumento();

    var desde   = id("FechaBuscaDesde").value;
    var hasta   = id("FechaBuscaHasta").value;
    var nombre  = id("NombreProveedor").value;
   
    var filtromodalidad = id("FiltroModalidad").value;
    var filtroestado    = id("FiltroEstado").value;
    var filtromoneda    = id("FiltroMoneda").value;
    var filtrolocal     = id("FiltroLocal").value;
    RawBuscarPagoDocumento(desde,hasta,nombre,filtromodalidad,filtroestado,
		     filtromoneda,filtrolocal,AddLineaPagoDocumento);
}

function RawBuscarPagoDocumento(desde,hasta,nombre,filtromodalidad,filtroestado,
				filtromoneda,filtrolocal,FuncionProcesaLinea){
    var z = null;
    var url = "modpagoscobros.php?modo=mostrarPagoDocumento&desde=" + escape(desde)
        + "&hasta=" + escape(hasta)
        + "&nombre=" + trim(nombre)
        + "&filtromodalidad=" + escape(filtromodalidad)
	+ "&filtroestado=" + escape(filtroestado)
        + "&filtromoneda=" + escape(filtromoneda)
        + "&filtrolocal=" + escape(filtrolocal);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try {
	obj.send(null);
    } catch(z){
	return;
    }
    
    var tex = "";
    var cr = "\n";
    var item,Local,Pedido,Proveedor,FechaRegistro,FechaOperacion,ModalidadPago,Estado,Simbolo,Importe,CodOperacion,CtaEmpresa,CtaProveedor,NumDocumento,Usuario,CambioMoneda,IdPagoProvDoc,IdMoneda,IdOrdenCompra,IdModalidadPago,IdProveedor,Observaciones,Codigo,IdLocal,TipoProveedor,Saldo;
    var node,t,i; 
    var nroDocumento  = 0;
    var nroPendiente  = 0;
    var nroConfirmado = 0;
    var nroBorrador   = 0;
    var nroCancelado  = 0;
    var TotalImporte  = 0;

    if (!obj.responseXML)
       return alert(po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC = item;
    var totalImpuesto = 0;
    
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            Local 	     = node.childNodes[t++].firstChild.nodeValue;
	    Pedido           = node.childNodes[t++].firstChild.nodeValue;
            Proveedor 	     = node.childNodes[t++].firstChild.nodeValue;
            FechaRegistro    = node.childNodes[t++].firstChild.nodeValue;
            FechaOperacion   = node.childNodes[t++].firstChild.nodeValue;
            ModalidadPago    = node.childNodes[t++].firstChild.nodeValue;
            Estado 	     = node.childNodes[t++].firstChild.nodeValue;
            Simbolo 	     = node.childNodes[t++].firstChild.nodeValue;
            Importe 	     = node.childNodes[t++].firstChild.nodeValue;
            CodOperacion     = node.childNodes[t++].firstChild.nodeValue;
            CtaEmpresa       = node.childNodes[t++].firstChild.nodeValue;
            CtaProveedor     = node.childNodes[t++].firstChild.nodeValue;
            NumDocumento     = node.childNodes[t++].firstChild.nodeValue;
            Usuario          = node.childNodes[t++].firstChild.nodeValue;
            CambioMoneda     = node.childNodes[t++].firstChild.nodeValue;
            IdPagoProvDoc    = node.childNodes[t++].firstChild.nodeValue;
            IdMoneda         = node.childNodes[t++].firstChild.nodeValue;
            IdOrdenCompra    = node.childNodes[t++].firstChild.nodeValue;
            IdModalidadPago  = node.childNodes[t++].firstChild.nodeValue;
            IdProveedor      = node.childNodes[t++].firstChild.nodeValue;
            Codigo           = node.childNodes[t++].firstChild.nodeValue;
            IdLocal          = node.childNodes[t++].firstChild.nodeValue;
            TipoProveedor    = node.childNodes[t++].firstChild.nodeValue;
            Observaciones    = node.childNodes[t++].firstChild.nodeValue;
            Saldo            = node.childNodes[t++].firstChild.nodeValue;

	
	    //Consolidado basico
 	    if (Estado == 'Pendiente'  ) nroPendiente++; 
 	    if (Estado == 'Confirmado' ) nroConfirmado++; 
	    if (Estado == 'Cancelado') nroCancelado++; 
	    if (Estado == 'Borrador') nroBorrador++; 
	    TotalImpuesto = parseFloat(Importe*0.18);
	    nroDocumento++;


            FuncionProcesaLinea(item,Local,Pedido,Proveedor,FechaRegistro,FechaOperacion,
				ModalidadPago,Estado,Simbolo,Importe,
				CodOperacion,CtaEmpresa,CtaProveedor,NumDocumento,Usuario,
				CambioMoneda,IdPagoProvDoc,IdMoneda,IdOrdenCompra,
				IdModalidadPago,IdProveedor,Observaciones,TotalImpuesto,
				Codigo,IdLocal,TipoProveedor,Saldo);
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    id("TotalPagoDocumento").value       = parseFloat(tC);
    id("TotalDocumentoPendiente").value  = nroPendiente;
    id("TotalDocumentoConfirmado").value = nroConfirmado;
    id("TotalDocumentoCancelado").value  = nroCancelado;
    id("TotalDocumentoBorrador").value   = nroBorrador;

}

function AddLineaPagoDocumento(item,Local,Pedido,Proveedor,FechaRegistro,FechaOperacion,
			       ModalidadPago,Estado,Simbolo,Importe,
			       CodOperacion,CtaEmpresa,CtaProveedor,NumDocumento,Usuario,
			       CambioMoneda,IdPagoProvDoc,IdMoneda,IdOrdenCompra,
			       IdModalidadPago,IdProveedor,Observaciones,TotalImpuesto,
			       Codigo,IdLocal,TipoProveedor,Saldo){

    var lista = id("listaPagoDocumento");
    var xitem,xLocal,xPedido,xProveedor,xFechaRegistro,xFechaOperacion,xModalidadPago,xEstado,xSimbolo,xImporte,xCodOperacion,xCtaEmpresa,xCtaProveedor,xNumDocumento,xUsuario,xCambioMoneda,xIdPagoProvDoc,xIdMoneda,xIdOrdenCompra,xObservaciones,xTotalImpuesto, xDocumentoRef,xFOperacion,xHOPeracion,xCodigo,xIdLocal,xTipoProveedor,xSaldo;
    var lobs = (Observaciones ==' ')?'':'...';
    Pedido = (Pedido == ' ')?'':'Pedido - '+Pedido;

    var fAllOperacion = (FechaOperacion)?FechaOperacion.split("~"):'';
    var vOperacion    = (FechaOperacion)?fAllOperacion[0]:'';
    var fhOperacion   = (FechaOperacion)?fAllOperacion[1]:'';

    var fechaOperacion   = (fhOperacion)?fhOperacion.split(" "):'';
    var fOperacion       = (fhOperacion)?fechaOperacion[0]:'';
    var hOperacion       = (fhOperacion)?fechaOperacion[1]:'';
    var vEstado          = (Estado == 'Pendiente')? 'Abonado':Estado;

    xitem = document.createElement("listitem");
    xitem.value = IdPagoProvDoc;
    xitem.setAttribute("id","lineabuscadocumento_"+ilineabuscadocumento);
    xitem.setAttribute("oncontextmenu","seleccionarlineadocumentospago("+ilineabuscadocumento+")");
    ilineabuscadocumento++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",Local);
    xLocal.setAttribute("value",IdLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("id","local_"+IdPagoProvDoc);

    xIdPagoProvDoc = document.createElement("listcell");
    xIdPagoProvDoc.setAttribute("value",IdPagoProvDoc);
    xIdPagoProvDoc.setAttribute("collapsed","true");
    xIdPagoProvDoc.setAttribute("style","text-align:center;font-weight:bold;");
    xIdPagoProvDoc.setAttribute("id","idpagodoc_"+IdPagoProvDoc);

    xIdMoneda = document.createElement("listcell");
    xIdMoneda.setAttribute("value",IdMoneda);
    xIdMoneda.setAttribute("label",Simbolo);
    xIdMoneda.setAttribute("collapsed","true");
    xIdMoneda.setAttribute("id","idmoneda_"+IdPagoProvDoc);

    xCambioMoneda = document.createElement("listcell");
    xCambioMoneda.setAttribute("value",CambioMoneda);
    xCambioMoneda.setAttribute("collapsed","true");
    xCambioMoneda.setAttribute("id","cambiomoneda_"+IdPagoProvDoc);

    xDocumentoRef = document.createElement("listcell");
    xDocumentoRef.setAttribute("label",Pedido);
    xDocumentoRef.setAttribute("collapsed",vReferencia);
    xDocumentoRef.setAttribute("style","text-align:left;font-weight:bold;");
    xDocumentoRef.setAttribute("id","documento_"+IdPagoProvDoc);

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",Codigo);
    xCodigo.setAttribute("style","text-align:left;font-weight:bold;");
    xCodigo.setAttribute("id","codigo_"+IdPagoProvDoc);

    xProveedor = document.createElement("listcell");
    xProveedor.setAttribute("label",Proveedor);
    xProveedor.setAttribute("value",IdProveedor);
    xProveedor.setAttribute("style","text-align:left;");
    xProveedor.setAttribute("id","proveedor_"+IdPagoProvDoc);

    xTipoProveedor = document.createElement("listcell");
    xTipoProveedor.setAttribute("label",TipoProveedor);
    xTipoProveedor.setAttribute("collapsed","true");
    xTipoProveedor.setAttribute("id","tipoproveedor_"+IdPagoProvDoc);

    xFechaRegistro = document.createElement("listcell");
    xFechaRegistro.setAttribute("label", FechaRegistro);
    xFechaRegistro.setAttribute("collapsed",vFechaRegistro)
    xFechaRegistro.setAttribute("style","text-align:left");

    xFechaOperacion = document.createElement("listcell");
    xFechaOperacion.setAttribute("label", vOperacion);
    xFechaOperacion.setAttribute("style","text-align:feft;");
    xFechaOperacion.setAttribute("id","operacion_"+IdPagoProvDoc);

    xFOperacion = document.createElement("listcell");
    xFOperacion.setAttribute("value", fOperacion);
    xFOperacion.setAttribute("style","text-align:right;");
    xFOperacion.setAttribute("id","foperacion_"+IdPagoProvDoc);

    xHOperacion = document.createElement("listcell");
    xHOperacion.setAttribute("value", hOperacion);
    xHOperacion.setAttribute("style","text-align:right;");
    xHOperacion.setAttribute("id","hoperacion_"+IdPagoProvDoc);


    xModalidadPago = document.createElement("listcell");
    xModalidadPago.setAttribute("label", ModalidadPago);
    xModalidadPago.setAttribute("value", IdModalidadPago);
    xModalidadPago.setAttribute("style","text-align:left");
    xModalidadPago.setAttribute("id","modopago_"+IdPagoProvDoc);

    xCodOperacion = document.createElement("listcell");
    xCodOperacion.setAttribute("value",CodOperacion);
    xCodOperacion.setAttribute("collapsed","true");
    xCodOperacion.setAttribute("id","codoperacion_"+IdPagoProvDoc);

    xCtaEmpresa = document.createElement("listcell");
    xCtaEmpresa.setAttribute("value",CtaEmpresa);
    xCtaEmpresa.setAttribute("collapsed","true");
    xCtaEmpresa.setAttribute("id","ctaempresa_"+IdPagoProvDoc);

    xCtaProveedor = document.createElement("listcell");
    xCtaProveedor.setAttribute("value",CtaProveedor);
    xCtaProveedor.setAttribute("collapsed","true");
    xCtaProveedor.setAttribute("id","ctaproveedor_"+IdPagoProvDoc);

    xNumDocumento = document.createElement("listcell");
    xNumDocumento.setAttribute("value",NumDocumento);
    xNumDocumento.setAttribute("collapsed","true");
    xNumDocumento.setAttribute("id","numdocumento_"+IdPagoProvDoc);

    xTotalImpuesto = document.createElement("listcell");
    xTotalImpuesto.setAttribute("label", Simbolo+' '+formatDinero(TotalImpuesto));
    xTotalImpuesto.setAttribute("collapsed",vImpuesto);
    xTotalImpuesto.setAttribute("style","text-align:right;");

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", Simbolo+' '+formatDinero(Importe));
    xImporte.setAttribute("style","text-align:right;font-weight:bold; ");
    xImporte.setAttribute("value",Importe);
    xImporte.setAttribute("id","importe_"+IdPagoProvDoc);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", vEstado);
    xEstado.setAttribute("value", Estado);
    xEstado.setAttribute("style","text-align:left;");
    xEstado.setAttribute("id","estado_"+IdPagoProvDoc);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("collapsed", vUsuario);
    xUsuario.setAttribute("style","text-align:center;");

    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("label", lobs);
    xObservaciones.setAttribute("value",Observaciones );
    xObservaciones.setAttribute("collapsed","true");
    xObservaciones.setAttribute("id","obs_"+IdPagoProvDoc);
    xObservaciones.setAttribute("style","text-align:center");
    xObservaciones.setAttribute("crop", "end");

    xSaldo = document.createElement("listcell");
    xSaldo.setAttribute("label", Simbolo+' '+formatDinero(Saldo));
    xSaldo.setAttribute("style","text-align:right;font-weight:bold; ");
    xSaldo.setAttribute("value",Saldo);
    xSaldo.setAttribute("id","saldo_"+IdPagoProvDoc);


    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xDocumentoRef );
    xitem.appendChild( xProveedor );
    xitem.appendChild( xFechaRegistro );
    xitem.appendChild( xFechaOperacion );	
    xitem.appendChild( xModalidadPago );
    xitem.appendChild( xEstado );	
    xitem.appendChild( xTotalImpuesto );	
    xitem.appendChild( xImporte );
    xitem.appendChild( xSaldo );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xIdMoneda );
    xitem.appendChild( xCambioMoneda );
    xitem.appendChild( xCodOperacion );
    xitem.appendChild( xCtaEmpresa );
    xitem.appendChild( xCtaProveedor );
    xitem.appendChild( xNumDocumento);
    xitem.appendChild( xIdPagoProvDoc);
    xitem.appendChild( xFOperacion);
    xitem.appendChild( xHOperacion);
    xitem.appendChild( xTipoProveedor);
    
    lista.appendChild( xitem );		
}

function seleccionarlineadocumentospago(linea){
    var lista = id("listaPagoDocumento");
    var fila  = id("lineabuscadocumento_"+linea);
    lista.selectItem(fila);
}

function CesionDocumento(){
    var idex = id("listaPagoDocumento").selectedItem;
    if(!idex) return;

    cProveedor        = id("proveedor_"+idex.value).getAttribute("label");
    cIdProvHab        = id("proveedor_"+idex.value).getAttribute("value");
    cModalidadPago    = id("modopago_"+idex.value).getAttribute("value");
    cImporte          = id("importe_"+idex.value).getAttribute("label");
    cCodOperacion     = id("codoperacion_"+idex.value).getAttribute("value");
    cCtaEmpresa       = id("ctaempresa_"+idex.value).getAttribute("value");
    cCtaProveedor     = id("ctaproveedor_"+idex.value).getAttribute("value");
    cNumDocumento     = id("numdocumento_"+idex.value).getAttribute("value");
    cCambioMoneda     = id("cambiomoneda_"+idex.value).getAttribute("value");
    cIdMoneda         = id("idmoneda_"+idex.value).getAttribute("value");
    cSimbolo          = id("idmoneda_"+idex.value).getAttribute("label");
    cObservaciones    = id("obs_"+idex.value).getAttribute("value");
    cFOperacion       = id("foperacion_"+idex.value).getAttribute("value");
    cHOperacion       = id("hoperacion_"+idex.value).getAttribute("value");
    cFechaOperacion   = id("operacion_"+idex.value).getAttribute("label");
    cModoPago         = id("modopago_"+idex.value).getAttribute("label");
    cEstado           = id("estado_"+idex.value).getAttribute("value");
    cCodigo           = id("codigo_"+idex.value).getAttribute("label");
    cIdLocal          = id("local_"+idex.value).getAttribute("value");
    cSImporte         = (cImporte)?cImporte.split(" "):'';
    cLImporte         = (cImporte)?cSImporte[1]:'';
    cTImporte         = id("importe_"+idex.value).getAttribute("value");
    cSaldo         = id("saldo_"+idex.value).getAttribute("value");
    cTipoProveedor    = id("tipoproveedor_"+idex.value).getAttribute("label");

    cCtaEmpresa   = (trim(cCtaEmpresa))? cCtaEmpresa.split("~"):'';
    cCtaProveedor = (trim(cCtaProveedor))? cCtaProveedor.split("~"):'';

    xmenuPagoDocumento();
}

function ImprimirPagoDocumento(){

    var idex = id("listaPagoDocumento").selectedItem;
    if(!idex) return;
    var ctaemp = (cCtaEmpresa)? "("+cCtaEmpresa[1]+") "+cCtaEmpresa[2]+" "+cCtaEmpresa[3]:'';
    var ctaprov= (cCtaProveedor)? "("+cCtaProveedor[1]+") "+cCtaProveedor[2]+" "+cCtaProveedor[3]:'';

    var printpd = "";
    printpd += "gPOS: Pago  al Proveedor "+'"'+cProveedor+'"'+" \n\n";

    printpd += (cModoPago)?           "Modalidad Pago  : "+cModoPago+"\n":'';
    printpd += (cFechaOperacion)?"Fecha Operación : "+cFOperacion+" "+cHOperacion+"\n":'';
    printpd += (cCodOperacion != ' ')?"Número Operación : "+cCodOperacion+"\n":'';
    printpd += (cNumDocumento != ' ')?"Número Documento : "+cNumDocumento+"\n":'';
    printpd += (cCtaEmpresa)?"Cuenta Empresa    : "+ctaemp+"\n":'';
    printpd += (cCtaProveedor)?"Cuenta Proveedor  : "+ctaprov+"\n":'';
    printpd += (cIdMoneda == '1')?"Importe : "+cImporte+"\n":"Importe : "+cImporte+ "   Cambio: "+cCambioMoneda+"\n";
    printpd += (cObservaciones != ' ')?"Observaciones : "+cObservaciones:'';
    alert(printpd);
}

//Carga Proveedor

/*+++++++popup div++++++++++*/
function CogeLocal() { popup('../../modulos/locales/sellocal.php?modo=localpost','proveedorhab'); }    

function loadLocalHab() {    
    //Lista Proveedores
    closepopup();

    if(!LocalPost) return;
    id("ProvHab").value   = LocalPost;
    id("IdProvHab").value = IdLocalPost;
}


function CogeProvHab() { popup('../../modulos/proveedores/selproveedor.php?modo=proveedorpost','proveedorhab');  }
function loadProvHab() {    
    //Lista Proveedores
    closepopup();

    if(!ProveedorPost) return;
    id("ProvHab").value   = ProveedorPost;
    id("IdProvHab").value = IdProveedorPost;
    id("ProvHab").setAttribute("value",ProveedorPost);
    id("IdProvHab").setAttribute("value", IdProveedorPost);

    var xval = id("ModalidadPago").getAttribute("value");
    if(IdProveedorPost > 1){
	ObtenerDatosPreveedor(IdProveedorPost);
	CargarDetallePagoDocumento(xval);
    }

    RegenEntidadFinancieraProv();
    RegenCuentasProv();
}

function OcultarMoneda(xval){
    var cdivisa = true;
    if(xval==1){
        cdivisa = true;
        //id("cambmoneda").setAttribute('collapsed',true);
    }else{
        cdivisa = false;
        //id("cambmoneda").setAttribute('collapsed',false);
    }

    var txt = "Marque si desea que se registre operaciones "+
              "cambio moneda, de ["+cMoneda[1]['T']+
              "] a ["+cMoneda[xval]['T']+"] en caja general";
    
    id("checkCambioDivisa").checked = false;
    id("checkCambioDivisa").setAttribute('tooltiptext',txt);
    var xvalue = id("ModalidadPago").getAttribute("value");
    id("trasladomoneda").setAttribute('collapsed',cdivisa);

    if(esNuevo) CargarDetallePagoDocumento(xvalue);
}


function VerDatosExtra(xval){
    var idmon = id("TipoMoneda").value;

    OcultarMoneda(idmon);
    id("DetallePagoDocumento").setAttribute('collapsed',false);
    if(xval==0) id("DetallePagoDocumento").setAttribute('collapsed',true);

    switch (xval) {
    case '3':
    case '4':
    case '5':
	var xvalue = "false";
	var lvalue = "false";
	break;
    case '6':
	var xvalue = "false";
	var lvalue = "true";
	break;
    case '2':
    case '7':
	var xvalue = "true";
	var lvalue = "false";
	break;
    case '1':
    case '8':
    case '9':
    case '10':
	var xvalue = "true";
	var lvalue = "true";
	break;
    }

    if(lvalue == 'false'){
	RegenEntidadFinancieraProv();
	RegenCuentasProv();
    }

    if(xvalue == 'false'){
	RegenEntidadFinanciera();
	RegenCuentas();
    }

    id("rowCtaEmpresa").setAttribute('collapsed',xvalue);
    id("entFinancieraEmp").setAttribute('collapsed',xvalue);
    id("cuentaEmpresa").setAttribute('collapsed',xvalue);

    id("rowCtaProveedor").setAttribute('collapsed',lvalue);
    id("entFinancieraProv").setAttribute('collapsed',lvalue);
    id("cuentaProveedor").setAttribute('collapsed',lvalue);

    id("codOperacion").setAttribute('collapsed',lvalue);
    (xval != 9 && xval != 10)? id("numDocumento").setAttribute('collapsed',xvalue):id("numDocumento").setAttribute('collapsed',false);

}

function AltaPagoDocumento(){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=AltaPagoDocumento";
    var data     = "";
    var res;

    var proveedor         = id("ProvHab").value;
    var provhab           = id("IdProvHab").value;
    var modalidadpago     = id("ModalidadPago").value;
    var fechaoperacion    = id("FechaOperacion").value;
    var horaoperacion     = id("HoraOperacion").value;
    var codigooperacion   = trim(id("CodigoOperacion").value);
    var nrodocumento      = trim(id("NroDocumento").value);
    var cuentaproveedor   = id("NroCtaProveedor").value;
    var cuentaempresa     = id("NroCtaEmpresa").value;
    var tipomoneda        = id("TipoMoneda").value;
    var cambiomoneda      = id("CambioMoneda").value;
    var importe           = id("Importe").value;
    var obs               = trim(id("Observaciones").value);
    var idlocal           = (esAlmacen == 1)? id("FiltroLocal").value:'false';
    var estado            = id("EstadoDocumento").value;
    var tipoprov          = id("TipoProveedor").value;
    //var local             = (esAlmacen == 1)? id("FiltroLocal").value:'false';
    var cambiodivisa      = id("checkCambioDivisa").checked;
    cambiodivisa          = (cambiodivisa)? '1':'0';

    cambiomoneda          = (!cambiomoneda || cambiomoneda == 0)? 1:cambiomoneda;

    fechaoperacion = fechaoperacion+' '+horaoperacion;
    var mgpos = 'gPOS:   Registro Documento Pago\n\n ';

    var modopago   = (modalidadpago == 1 || modalidadpago == 2 || modalidadpago == 7)? 1:0;

    if(provhab == 0)
	return alert(mgpos+ ' - Seleccione un Proveedor' );

    if(modalidadpago == 0)
	return alert(mgpos+ ' - Escoja Modalidad de Pago' );

    if(id("Importe").value < 0.1)
	return alert(mgpos+ ' - Ingrese el Importe' );

    if(tipomoneda != 1 && cambiomoneda == 0)
	return alert(mgpos+ ' - Ingrese Cambio Moneda' );

    var xmsj = mgpos+" - Nro Cuenta no coinside con Moneda Seleccionada";
    switch (modalidadpago) {
    case '3':
    case '4':
    case '5':
	if(cuentaempresa == 0 || cuentaempresa == "")
	    return alert(mgpos+" - Elija nro de cuenta de la empresa");
	if(cuentaproveedor == 0 || cuentaproveedor == "")
	    return alert(mgpos+" - Elija nro de cuenta del proveedor");
	if(arrCtaEmpresa[cuentaempresa].idmon != arrCtaProveedor[cuentaproveedor].idmon)
	    return alert(mgpos+" - Nro de cuenta de la empresa y proveedor deben ser en la misma moneda");
	if((tipomoneda != arrCtaEmpresa[cuentaempresa].idmon) || (tipomoneda != arrCtaProveedor[cuentaproveedor].idmon))
	    return alert(xmsj);
	if(codigooperacion == '')
	    return alert(mgpos+" - Ingrese código operación");
	break;
    case '6':
	if(cuentaempresa == 0 || cuentaempresa == "")
	    return alert(mgpos+" - Elija nro de cuenta de la empresa");
	if(tipomoneda != arrCtaEmpresa[cuentaempresa].idmon)
	    return alert(xmsj);
	cuentaproveedor = 0;
	break;
    case '2':
    case '7':
	if(cuentaproveedor == 0 || cuentaproveedor == "")
	    return alert(mgpos+" - Elija nro de cuenta del proveedor");
	if(tipomoneda != arrCtaProveedor[cuentaproveedor].idmon)
	    return alert(xmsj);
	if(codigooperacion == '')
	    return alert(mgpos+" - Ingrese código operación");
	cuentaempresa = 0;
	break;
    case '3':
    case '4':
    case '5':
	cuentaempresa   = 0;
	cuentaproveedor = 0;
    }    

    data = data + "&xidp="+ provhab;
    data = data + "&xidoc="+ cIdOrdenCompra;
    data = data + "&xidmp="+ modalidadpago;
    data = data + "&xfp="+ fechaoperacion;
    data = data + "&xco="+ codigooperacion;
    data = data + "&xnd="+ nrodocumento;
    data = data + "&xcp="+ cuentaproveedor;
    data = data + "&xce="+ cuentaempresa;
    data = data + "&xidm="+ tipomoneda;
    data = data + "&xcm="+ cambiomoneda;
    data = data + "&ximp="+ importe;
    data = data + "&xobs="+ obs;
    data = data + "&xidl="+ idlocal;
    data = data + "&estado="+ estado;
    data = data + "&tipoprov="+ tipoprov;
    data = data + "&cambiodivisa="+ cambiodivisa;
    //data = data + "&local="+ local;

    msj=' Registrar pago del proveedor -'+proveedor+'-';
    if(!confirm('gPOS: \n\n '+msj+' '+
		' ¿desea continuar?')) return;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        //NOTA: posiblemente no tenemos conexion.
        res = false;	
    }

    var ares = res.split("~");
    
    if(ares[0] != '') 
	return alert("gPOS: \n\n"+po_servidorocupado+'\n\n -'+res+'-');

    if(ares[1] == "0")
	return alert("gPOS: Caja General \n\n  - Estado Cerrada \n  - No se registró la operación de egreso.");

    if(ares[1] == '1')
        return alert("gPOS: Caja General \n\n  - Caja moneda "+cMoneda[tipomoneda]['T']+" está cerrada \n  - No se registró la operación.");

    var xxmsj = '';
    if(tipomoneda != 1 && modopago == 1 && cambiodivisa == '1'){
        xxmsj = (estado == 'Pendiente' && cEstado != 'Pendiente')? '\n  - Se realizaron las siguientes operaciones: \n    * Se retiró dinero de la caja '+cMoneda[1]['T']+' \n    * Se ingresó dinero a la caja '+cMoneda[tipomoneda]['T']:'';
    }

    alert("gPOS: Caja General \n\n  - Se registró la operación con éxito."+xxmsj);

    CancelarPagoDocumento();
    BuscarPagoDocumento();
}

function PagoDocumento(xvalue){
    (cModo == 'comprdoc')? CleanFormPagoDocumento():false;
    var IdProveedor = id("IdProvHab").getAttribute("value");

    ObtenerDatosEmpresa();
    ObtenerDatosPreveedor(IdProveedor);
    var almacen = true;
    var lval    = true;
    var xval    = false;
    var mval    = false;

    if(xvalue == 'Modificar' && cEstado == 'Confirmado')
	return;

    RegenCuentasBancarias();
    RegenEntidadFinanciera();
    RegenCuentas();
    RegenEntidadFinancieraProv();
    RegenCuentasProv();

    switch(xvalue){
    case 'Nuevo':
	var msj  = "Nuevo Pago";
	var cbtn = "AltaPagoDocumento()";
	esNuevo  = true;

	if(cModo == 'pedidoc') id('Importe').value = ImporteOC;
	if(cModo == 'pedidoc') id('CambioMoneda').value = CambioMonedaOC;
	if(cModo == 'pedidoc') OcultarMoneda(IdMonedaOC);
	cModalidadPago = '1';
	id("ModalidadPago").value = cModalidadPago;
	break;

    case 'Modificar':
	CesionDocumento();
	OcultarMoneda(cIdMoneda);
	esNuevo = false;

	almacen = (esAlmacen == 1 && cEstado == 'Borrador')? false:true;
	var mcancelar = (cEstado == 'Borrador')? false:true;
	var mpendte   = (cEstado == 'Cancelado')? true:false;
	var estadopd  = (cEstado == 'Pendiente' || cEstado == 'Confirmado')? true:false;
	var moneda    = (cEstado == 'Pendiente')? true:false;
	var cmbmoneda = (cEstado != 'Confirmado')? true:false;
	var importe   = (cEstado == 'Pendiente')? true:false;
	var msj       = "Editando Pago";
	var cbtn      = "GuardarPagoDocumento()";
	var yval      = (cEstado == 'Borrador')? true:false;
	var idex      = id("listaPagoDocumento").selectedItem;
	if(!idex) return;

	id("ProvHab").value           = cProveedor;
	id("IdProvHab").value         = cIdProvHab;
	id("ModalidadPago").value     = cModalidadPago;
	id("FechaOperacion").value    = cFOperacion;
	id("HoraOperacion").value     = cHOperacion;
	id("CodigoOperacion").value   = cCodOperacion;
	id("NroDocumento").value      = cNumDocumento;
	id("EntidadFinancieraProv").value = (cCtaProveedor)? cCtaProveedor[3]:'';
	id("EntidadFinancieraEmp").value  = (cCtaEmpresa)? cCtaEmpresa[3]:'';
	RegenCuentas();
	RegenCuentasProv();
	id("NroCtaProveedor").value   = (cCtaProveedor)? cCtaProveedor[0]:'';
	id("NroCtaEmpresa").value     = (cCtaEmpresa)? cCtaEmpresa[0]:'';
	id("TipoMoneda").value        = cIdMoneda;
	id("CambioMoneda").value      = cCambioMoneda;
	id("Importe").value           = cTImporte;
	id("Observaciones").value     = cObservaciones;
	id("EstadoDocumento").value   = cEstado;
	id("TipoProveedor").value     = cTipoProveedor;
	(esAlmacen==1)? (id("FiltroLocalCambio").value = cIdLocal) : false;

	id("TipoMoneda").setAttribute('disabled',moneda);
	id("CambioMoneda").setAttribute('disabled',cmbmoneda);
	id("Importe").setAttribute('disabled',importe);
	id("EstadoDocumento").setAttribute('disabled',estadopd);

	(yval)? id("TipoMoneda").removeAttribute('disabled'):false;
	(yval || cEstado == 'Pendiente')? id("CambioMoneda").removeAttribute('disabled'):false;
	(yval)? id("Importe").removeAttribute('disabled'):false;
	(yval)? id("EstadoDocumento").removeAttribute('disabled'):false;

	id("menuEstadoCancelado").setAttribute('collapsed',mcancelar);
	id("menuEstadoPte").setAttribute('collapsed',mpendte);


	break;

    }

    VerDatosExtra(cModalidadPago);
    id("listboxPagoDocumento").setAttribute('collapsed',lval);
    id("DetallePagoDocumento").setAttribute('collapsed',xval);
    id("EstadoPagoDocumento").setAttribute('collapsed',mval);
    id("formularioDocumento").setAttribute('collapsed',xval);
    id("botonFormularioDocumento").setAttribute('collapsed',xval);
    id("filaLocalCambio").setAttribute('collapsed',almacen);
    id("btnAceptar").setAttribute('oncommand',cbtn);
    id("mensaje").setAttribute('label',msj);
    id("buscaPagoDocumento").setAttribute("collapsed",true);
    
    (xval)? BuscarPagoDocumento():false;
    (xval)? CleanFormPagoDocumento():false;
}

function CancelarPagoDocumento(){
    (cModo=='comprdoc')? CerrarPagoDocumento():parent.volverOrdenCompra();
}

function CerrarPagoDocumento(){

    id("listboxPagoDocumento").setAttribute('collapsed',false);
    id("formularioDocumento").setAttribute('collapsed',true);
    id("botonFormularioDocumento").setAttribute('collapsed',true);
    id("EstadoPagoDocumento").setAttribute('collapsed',true);
    id("buscaPagoDocumento").setAttribute("collapsed",false);
    CleanFormPagoDocumento();
}

function CleanFormPagoDocumento(){
    id("IdProvHab").value         = '1';
    id("ModalidadPago").value     = '0';
    id("HoraOperacion").value     = '00:00:00';
    id("CodigoOperacion").value   = '';
    id("NroDocumento").value      = '';
    id("NroCtaProveedor").value   = '';
    id("NroCtaEmpresa").value     = '';
    id("EntidadFinancieraEmp").value = '';
    id("EntidadFinancieraProv").value = '';
    id("TipoMoneda").value        = '1';
    id("CambioMoneda").value      = '1';
    id("Importe").value           = '0';
    id("Observaciones").value     = '';
    id("TipoMoneda").removeAttribute("disabled");
    id("Importe").removeAttribute("disabled");
    id("EstadoDocumento").removeAttribute("disabled");
    id("TipoProveedor").value     =  'Externo';
    id("ProvHab").value           =  ProvExterno;
}

function GuardarPagoDocumento(){
    var idex = id("listaPagoDocumento").selectedItem;
    var idoc = idex.value;
    if(!idex) return;

    cEstado           = id("estado_"+idex.value).getAttribute("value");

    if(cEstado == 'Confirmado') return alert("gPOS: \n\n Los Documentos Confirmados no son modificables");

    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ModificaPagoDocumento";
    var data     = "";
    var res;

    var provhab           = id("IdProvHab").value;
    var modalidadpago     = id("ModalidadPago").value;
    var fechaoperacion    = id("FechaOperacion").value;
    var horaoperacion     = id("HoraOperacion").value;
    var codigooperacion   = id("CodigoOperacion").value;
    var nrodocumento      = id("NroDocumento").value;
    var cuentaproveedor   = id("NroCtaProveedor").value;
    var cuentaempresa     = id("NroCtaEmpresa").value;
    var tipomoneda        = id("TipoMoneda").value;
    var cambiomoneda      = id("CambioMoneda").value;
    var importe           = id("Importe").value;
    var obs               = id("Observaciones").value;
    var estado            = id("EstadoDocumento").value;
    var tipoprov          = id("TipoProveedor").value;
    var IdLocal           = (esAlmacen == 1)? id("FiltroLocalCambio").value:'false';
    var cambiodivisa      = id("checkCambioDivisa").checked;
    cambiodivisa          = (cambiodivisa)? '1':'0';
    cambiomoneda          = (!cambiomoneda || cambiomoneda == 0)? cCambioMoneda:cambiomoneda;

    fechaoperacion = fechaoperacion+' '+horaoperacion;
    var cambiodoc  = verificarCambioDato();
    var mgpos = 'gPOS:   Modificando Documento Pago \n\n';

    var modopago   = (modalidadpago == 1 || modalidadpago == 2 || modalidadpago == 7)? 1:0;

    // Control
    if(cambiodoc == "")
	return alert(mgpos+ ' - No hay cambios');

    if(id("Importe").value < 0.1)
	return alert(mgpos+ ' - Ingrese el Importe' );

    if(tipomoneda != 1 && cambiomoneda == 0)
	return alert(mgpos+ ' - Ingrese Cambio Moneda' );

    var xmsj = mgpos+" - Nro Cuenta no coinside con Moneda Seleccionada";
    switch (modalidadpago) {
    case '3':
    case '4':
    case '5':
	if(arrCtaEmpresa[cuentaempresa].idmon != arrCtaProveedor[cuentaproveedor].idmon)
	    return alert(mgpos+" - Nro de cuenta de la empresa y proveedor deben ser en la misma moneda");
	if((tipomoneda != arrCtaEmpresa[cuentaempresa].idmon) || (tipomoneda != arrCtaProveedor[cuentaproveedor].idmon))
	    return alert(xmsj);
	if(codigooperacion == '')
	    return alert(mgpos+" - Ingrese código operación");

	break;
    case '6':
	if(tipomoneda != arrCtaEmpresa[cuentaempresa].idmon)
	    return alert(xmsj);
	cuentaproveedor = 0;
	break;
    case '2':
    case '7':
	if(tipomoneda != arrCtaProveedor[cuentaproveedor].idmon)
	    return alert(xmsj);

	if(codigooperacion == '')
	    return alert(mgpos+" - Ingrese código operación");
	cuentaempresa = 0;
	break;
    case '3':
    case '4':
    case '5':
	cuentaempresa   = 0;
	cuentaproveedor = 0;
    }    

    data = data + "&xidp="+ provhab;
    data = data + "&xidmp="+ modalidadpago;
    data = data + "&xfo="+ fechaoperacion;
    data = data + "&xco="+ codigooperacion;
    data = data + "&xnd="+ nrodocumento;
    data = data + "&xcp="+ cuentaproveedor;
    data = data + "&xce="+ cuentaempresa;
    data = data + "&xidm="+ tipomoneda;
    data = data + "&xcm="+ cambiomoneda;
    data = data + "&ximp="+ importe;
    data = data + "&xobs="+ obs;
    data = data + "&xstdo="+ estado;
    data = data + "&xidppd="+ idoc;
    data = data + "&xidl="+ IdLocal;
    data = data + "&cestado="+ cEstado;
    data = data + "&cambiodivisa="+ cambiodivisa;

    // Mensaje de confirmación

    msj=' Va Modificar el Pago  -'+cCodigo+'-';
    if(!confirm('gPOS: '+msj+'\n\n'+cambiodoc+
		' \n¿desea continuar?')) return;

    // envío de data
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        //NOTA: posiblemente no tenemos conexion.
        res = false;	
    }

    //if(!parseInt(res)) 
	//alert(po_servidorocupado+'\n\n -'+res+'-');

    var ares = res.split("~");
    
    if(ares[0] != '') 
	return alert("gPOS: \n\n"+po_servidorocupado+'\n\n -'+res+'-');

    if(ares[1] == "0")
	return alert("gPOS: Caja General \n\n  - Estado Cerrada \n  - No se registró la operación de egreso.");

    if(ares[1] == '1')
        return alert("gPOS: Caja General \n\n  - Caja moneda "+cMoneda[tipomoneda]['T']+" está cerrada \n  - No se registró la operación.");

    var xxmsj = '';
    if(tipomoneda != 1 && modopago == 1 && cambiodivisa == '1'){
        xxmsj = (estado == 'Pendiente' && cEstado != 'Pendiente')? '\n  - Se realizaron las siguientes operaciones: \n    * Se retiró dinero de la caja '+cMoneda[1]['T']+' \n    * Se ingresó dinero a la caja '+cMoneda[tipomoneda]['T']:'';
    }
    
    alert("gPOS: Caja General \n\n  - Se registró los cambios con éxito"+xxmsj);
    CerrarPagoDocumento();
    BuscarPagoDocumento();
}

function verificarCambioDato(){
    var cambio;
    var msjdata = "";
    var fecha1 = id("FechaOperacion").value.replace(/-/g,"/");
    var hora1  = id("HoraOperacion").value;
    var fecha2 = cFOperacion.replace(/-/g,"/");
    var hora2  = cHOperacion;


    var date1  = new Date(fecha1+' '+hora1);
    var date2  = new Date(fecha2+' '+hora2);
    var datedif= date2 - date1;

    cambio   = (cProveedor != id("ProvHab").value)? true:false;
    msjdata += (cambio)? ' - Proveedor: '+id("ProvHab").value+'\n':"";

    cambio   = (cModalidadPago != id("ModalidadPago").value)? true:false;
    msjdata += (cambio)? ' - Modalidad Pago: '+id("ModalidadPago").value+'\n':"";

    cambio   = (datedif != 0)? true:false;
    msjdata += (cambio)? ' - Fecha Operación: '+id("FechaOperacion").value+' '+
                         id("FechaOperacion").value+'\n':"";

    cambio   = (cCodOperacion != id("CodigoOperacion").value)? true:false;
    msjdata += (cambio)? ' - Codigo Operación: '+id("CodigoOperacion").value+'\n':"";

    cambio   = (cNumDocumento != id("NroDocumento").value)? true:false;
    msjdata += (cambio)? ' - Nro. Documento: '+id("NroDocumento").value+'\n':"";

    if(cCtaProveedor){
	cambio   = (cCtaProveedor[0] != id("NroCtaProveedor").value)? true:false;
	msjdata += (cambio)? ' - Cta. Proveedor: '+id("NroCtaProveedor").label+'\n':"";
    }

    if(cCtaEmpresa){
	cambio   = (cCtaEmpresa[0] != id("NroCtaEmpresa").value)? true:false;
	msjdata += (cambio)? ' - Cta. Empresa: '+id("NroCtaEmpresa").label+'\n':"";
    }

    cambio   = (cIdMoneda != id("TipoMoneda").value ||+
                cCambioMoneda != id("CambioMoneda").value ||+
                cTImporte != id("Importe").value)? true:false;
    msjdata += (cambio)? ' - Importe: '+parseFloat(id("Importe").value).toFixed(2)+
                         ' '+id("TipoMoneda").label+
                         ((id("TipoMoneda").value!=1)?'   Tipo Cambio: '+
                         id("CambioMoneda").value:"")+'\n':"";

    cambio   = (cObservaciones != id("Observaciones").value)? true:false;
    msjdata += (cambio)? ' - Observación: '+id("Observaciones").value+'\n':"";

    cambio   = (cEstado != id("EstadoDocumento").value)? true:false;
    msjdata += (cambio)? ' - Estado Documento: '+id("EstadoDocumento").value+'\n':"";

    if(esAlmacen == 1){
	cambio = (cIdLocal != id("FiltroLocalCambio").value)? true:false;
	msjdata += (cambio)? ' - Local: '+id("FiltroLocalCambio").value+'\n':"";
    }

    return msjdata;
}

function EliminarPagoDocumento(){
    var idex     = id("listaPagoDocumento").selectedItem;
    if(!idex) return;

    var idoc     = idex.value;

    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=EliminaPagoDocumento";
    var data     = "";
    var res;

    data = data + "&idoc="+ idoc;
    data = data + "&xstado="+ cEstado;
    if(cEstado != 'Confirmado'){
	msj=' Va eliminar el Documento ';
	if(!confirm('gPOS: '+msj+','+
		' ¿desea continuar?')) return;
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	try {
            xrequest.send(data);
            res = xrequest.responseText;
	} catch(e) {
            //NOTA: posiblemente no tenemos conexion.
            res = false;	
	}

	if(!parseInt(res)) 
	    alert(po_servidorocupado+'\n\n -'+res+'-');	
    }
    else{
	alert("gPOS: \n\n  No Puede Eliminar El Documento en Estado Confirmado" );
    }
    BuscarPagoDocumento();
}

function xmenuPagoDocumento(){ 
    var edita = false;
    var elim  = false;
    var saldo = true;

    switch(cEstado){
    case 'Pendiente':
	elim = false;
	break;
    case 'Confirmado':
	elim  = true;
	edita = true;
	saldo = false;
	break;
    }

    id("mheadPagoEditar").setAttribute('disabled',edita);
    id("mheadEliminar").setAttribute('disabled',elim);
    //id("mheadModificarSaldo").setAttribute('disabled',saldo);
}

function SeleccionTipoProveedor(){
    var tipoprov = id("TipoProveedor").value;
    var cambio   = (tipoprov == 'Externo')? "CogeProvHab()":"CogeLocal()";
    id("lProvHab").setAttribute("oncommand",cambio);
    if(tipoprov == 'Externo'){
	id("ProvHab").value   = ProvExterno;
	id("IdProvHab").setAttribute("value", IdProvExterno);
    }

    if(tipoprov == 'Interno'){
	id("ProvHab").value   = "";
	id("IdProvHab").setAttribute("value", 0);
    }
    RegenEntidadFinancieraProv();
    RegenCuentasProv();
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Estado": 
	vEstado        = xchecked;
	break;
    case "Moneda" : 
	vMoneda        = xchecked;
	break;
    case "Usuario":
	vUsuario       = xchecked;
	break;
    case "Fecha_Registro":
	vFechaRegistro = xchecked;
	break;
    case "Impuesto" : 
	vImpuesto      = xchecked;
	break;
    case "Referencia":
	vReferencia    = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarPagoDocumento();
}

function CargarDetallePagoDocumento(xval){
    var idmon         = id("TipoMoneda").value;
    var CtaActualProv = '';
    var CtaActualEmpr = '';

    id("NroCtaProveedor").value  = 0;
    id("NroCtaEmpresa").value    = 0;
    id("NroCtaProveedor").label  = 'Elija...';
    id("NroCtaEmpresa").label    = 'Elija...';

    if(xval > 1 && xval < 8){
	var xMoneda = id("TipoMoneda").getAttribute("label");
	var xProv   = id("ProvHab").getAttribute("value");
	CtaActualProv   = (idmon == 1)? aDatoProveedor.Cta1:aDatoProveedor.Cta2;
	CtaActualEmpr   = (idmon == 1)? aDatoEmpresa.Cta1:aDatoEmpresa.Cta2;
    }

    switch (xval) {
    case '3':
    case '4':
    case '5':
	id("NroCtaProveedor").value  = CtaActualProv;
	id("NroCtaEmpresa").value    = CtaActualEmpr;
	break;
    case '6':
	id("NroCtaEmpresa").value    = CtaActualEmpr;
	break;
    case '2':
    case '7':
	id("NroCtaProveedor").value  = CtaActualProv;
	break;
    }    
}

function ObtenerDatosPreveedor(IdProveedor){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ObtieneDatosProveedor&xprov="+IdProveedor;

    xrequest.open("GET",url,false);
    xrequest.send(null);

    var ares = xrequest.responseText.split("::");
    if(ares[0] != '')
	return alert(po_servidorocupado+" Error:"+ares[0]);

    var adata =  ares[1].split("~~");
    aDatoProveedor.Cta1 = trim(adata[0]);
    aDatoProveedor.Cta2 = trim(adata[1]);
}

function ObtenerDatosEmpresa(){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ObtieneDatosEmpresa";

    xrequest.open("GET",url,false);
    xrequest.send(null);

    var ares = xrequest.responseText.split("::");
    if(ares[0] != '')
	return alert(po_servidorocupado+" Error:"+ares[0]);

    var adata = ares[1].split("~~");
    aDatoEmpresa.Cta1   = trim(adata[0]);
    aDatoEmpresa.Cta2   = trim(adata[1]);
}

function validarDatosPago(xcampo){
    switch(xcampo){
    case 'cm':
	if(id("CambioMoneda").value == '')
	    id("CambioMoneda").value = 1;
	break;
    case 'imp':
	if(id("Importe").value == '')
	    id("Importe").value = 0;
	break;
    }
}

/******************* Cuenta Bancaria **************************/

var acuentasb = "";
var icuentas = 0;
var ientidadfinanciera = 0;

var icuentasprov = 0;
var ientidadfinancieraprov = 0;

var actaemp = new Array();
var actaprov = new Array();

var arrCtaEmpresa   = new Array();
var arrCtaProveedor = new Array();

function CogeNroCuentaEmp(){
    var ctabank = id("EntidadFinancieraEmp").value;
    popup("selcuentabancaria.php?modo=cuenta&xidprov=0&xcta="+ctabank,'cuenta');
}

function CogeNroCuentaProv(){
    var idprov = id("IdProvHab").value;
    var ctabank = id("EntidadFinancieraProv").value;
    popup("selcuentabancaria.php?modo=cuenta&xidprov="+idprov+"&xcta="+ctabank,'cuenta');
}


function changeNroCuenta( quien, txtcuenta, idprov) {
    if(idprov == 0){
	RegenEntidadFinanciera();
	id("EntidadFinancieraEmp").value = txtcuenta;
	RegenCuentas();
	id("NroCtaEmpresa").value     = quien.value;
    }else{
	RegenEntidadFinancieraProv();
	id("EntidadFinancieraProv").value = txtcuenta;
	RegenCuentasProv();
	id("NroCtaProveedor").value     = quien.value;	
    }
}

function RegenCuentasBancarias(){
    var idprov = 0;
    actaemp = new Array();
    actaprov = new Array();
    var xrequest = new XMLHttpRequest();
    var url = "../../services.php?modo=cuentasbancarias&idprov="+idprov+"&todo=1";
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res   = xrequest.responseText;
    var lines = res.split("\n");
    var ln    = lines.length-1;	
    var actual,ent;

    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[4] == 0){
	    arrCtaEmpresa[actual[1]] = new Array;
	    arrCtaEmpresa[actual[1]].idcta  = actual[1];
	    arrCtaEmpresa[actual[1]].smon   = ent[0];
	    arrCtaEmpresa[actual[1]].nrocta = ent[1];
	    arrCtaEmpresa[actual[1]].finan  = ent[2];
	    arrCtaEmpresa[actual[1]].idmon  = ent[3];
	    arrCtaEmpresa[actual[1]].idprov = ent[4];
	    arrCtaEmpresa[actual[1]].estado = ent[5];
	    arrCtaEmpresa[actual[1]].obs    = ent[6];

	    actaemp.push(lines[t]);
	    
	}
	else{
	    arrCtaProveedor[actual[1]] = new Array;
	    arrCtaProveedor[actual[1]].idcta  = actual[1];
	    arrCtaProveedor[actual[1]].smon   = ent[0];
	    arrCtaProveedor[actual[1]].nrocta = ent[1];
	    arrCtaProveedor[actual[1]].finan  = ent[2];
	    arrCtaProveedor[actual[1]].idmon  = ent[3];
	    arrCtaProveedor[actual[1]].idprov = ent[4];
	    arrCtaProveedor[actual[1]].estado = ent[5];
	    arrCtaProveedor[actual[1]].obs    = ent[6];

	    actaprov.push(lines[t]);
	}
    }
}

/*Cuenta empresa*/
function VaciarCuentas(){
    var xlistitem = id("elementosCuentaEmp");
    var iditem;
    var t = 0;
    
    while( el = id("cuenta_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    icuentas = 0;
    
    id("NroCtaEmpresa").setAttribute("label","Elija...");	
}

function RegenCuentas() {
    VaciarCuentas();
    var entidad = id("EntidadFinancieraEmp").getAttribute("label");

    var actual,ent;
    var ln = actaemp.length;	
    for(var t=0;t<ln;t++){
	actual = actaemp[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[2] == entidad)
	    AddCuentaLine('('+ent[0]+') '+ent[1],actual[1]);		
    }
}

function AddCuentaLine(nombre, valor) {
    var xlistitem = id("elementosCuentaEmp");	

    var xcuenta = document.createElement("menuitem");
    xcuenta.setAttribute("id","cuenta_def_" + icuentas);
	
    xcuenta.setAttribute("value",valor);
    xcuenta.setAttribute("label",nombre);
    xcuenta.setAttribute("selected",true);
    
    xlistitem.appendChild( xcuenta);
    if(icuentas == 0) id("NroCtaEmpresa").value = valor;
    icuentas ++;

}

function RegenEntidadFinanciera(){
    VaciarEntidadFinanciera();

    var actual,ent;
    var xent = "";
    var ln = actaemp.length;	

    for(var t=0;t<ln;t++){
	actual = actaemp[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[2] != xent)
	    AddEntidadFinancieraLine(ent[2],actual[1]);
	xent = ent[2];
    }				   
}

function AddEntidadFinancieraLine(nombre, valor) {
    var xlistitem = id("elementosEntFinancieraEmp");	

    var xentidad = document.createElement("menuitem");
    xentidad.setAttribute("id","entidad_def_" + ientidadfinanciera);
    
    xentidad.setAttribute("value",nombre);
    xentidad.setAttribute("label",nombre);
    
    xlistitem.appendChild( xentidad);
    if(ientidadfinanciera == 0) id("EntidadFinancieraEmp").value = nombre;	
    ientidadfinanciera++;
}

function VaciarEntidadFinanciera(){
    var xlistitem = id("elementosEntFinancieraEmp");
    var iditem;
    var t = 0;
    
    while( el = id("entidad_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    ientidadfinanciera = 0;
    
    id("EntidadFinancieraEmp").setAttribute("label","Elija...");	
}

/*Cuenta proveedores*/
function VaciarCuentasProv(){
    var xlistitem = id("elementosCuentaProv");
    var iditem;
    var t = 0;
    
    while( el = id("cuentaprov_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    icuentasprov = 0;
    
    id("NroCtaProveedor").setAttribute("label","Elija...");	
}

function RegenCuentasProv() {
    VaciarCuentasProv();

    var tprov   = id("TipoProveedor").value;
    var idprov  = id("IdProvHab").value;
    var entidad = id("EntidadFinancieraProv").getAttribute("label");
    var lines = (tprov == 'Externo')? actaprov:actaemp;//acuentasb.split("\n");
    var actual,ent;
    var ln = lines.length;	

    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	ent = actual[0].split("~");

	if(ent[2] == entidad && ent[4] == idprov)
	    AddCuentaLineProv('('+ent[0]+') '+ent[1],actual[1]);		
    }				
}

function AddCuentaLineProv(nombre, valor) {
    var xlistitem = id("elementosCuentaProv");	

    var xcuenta = document.createElement("menuitem");
    xcuenta.setAttribute("id","cuentaprov_def_" + icuentasprov);
	
    xcuenta.setAttribute("value",valor);
    xcuenta.setAttribute("label",nombre);
    xcuenta.setAttribute("selected",true);
    
    xlistitem.appendChild( xcuenta);
    if(icuentasprov == 0) id("NroCtaProveedor").value = valor;
    icuentasprov ++;

}

function RegenEntidadFinancieraProv(){
    VaciarEntidadFinancieraProv();
    var tprov = id("TipoProveedor").value;
    var lines = (tprov == 'Externo')? actaprov:actaemp;//acuentasb.split("\n");
    var actual,ent;
    var xent = "";
    var ln = lines.length;	
    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[2] != xent)
	    AddEntidadFinancieraLineProv(ent[2],actual[1]);
	xent = ent[2];
    }				   
}

function AddEntidadFinancieraLineProv(nombre, valor) {
    var xlistitem = id("elementosEntFinancieraProv");	
    
    var xentidad = document.createElement("menuitem");
    xentidad.setAttribute("id","entidadprov_def_" + ientidadfinancieraprov);
    
    xentidad.setAttribute("value",nombre);
    xentidad.setAttribute("label",nombre);
    
    xlistitem.appendChild( xentidad);
    if(ientidadfinancieraprov == 0) id("EntidadFinancieraProv").value = nombre;	
    ientidadfinancieraprov++;
}

function VaciarEntidadFinancieraProv(){
    var xlistitem = id("elementosEntFinancieraProv");
    var iditem;
    var t = 0;
    
    while( el = id("entidadprov_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    ientidadfinancieraprov = 0;
    
    id("EntidadFinancieraProv").setAttribute("label","Elija...");	
}

function ModificarSaldoPago(){

    var idex = id("listaPagoDocumento").selectedItem;
    if(!idex) return;
    var idoc = idex.value;

    var xsaldo = prompt('gPOS:  MODIFICAR SALDO\n\n'+
			   ' Saldo: ',cSaldo);
    var xrequest  = new XMLHttpRequest();
    
    if( trim( xsaldo ) == '' || xsaldo == null ) return;

    xsaldo = parseFloat(xsaldo);
    
    if(xsaldo == cSaldo) return;

    if(xsaldo < 0 || xsaldo > cTImporte) return ModificarSaldoPago();
    
    var url = 
	"modpagoscobros.php?"+
	"modo=ModificaSaldoPago"+
	"&saldo="+xsaldo+
	"&idpagodoc="+idoc;
    
    xrequest.open("GET",url,false);
    xrequest.send(null);
    xres = xrequest.responseText;
    
    if(parseInt(xres))
	alert('gPOS:  Saldo Modifcado\n\n Saldo:  '+xsaldo);

    BuscarPagoDocumento();
}
