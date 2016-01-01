var aPago    = new Array();
var msj      = "";
var titulo   = "";

var id = function(name) { return document.getElementById(name); }

function CleanArrayPago(){
    aPagoProv.IdComprobante = 0;
    aPagoProv.IdProveedor   = 0;
    aPagoProv.IdMonedaCbte  = 0;
    aPagoProv.ImporteCbte   = 0;
    aPagoProv.CambioCobte   = 0;
    aPagoProv.Pendiente     = 0;
    aPagoProv.FormaPago     = "";

    aPagoProv.IdMonedaDoc   = 0;
    aPagoProv.ImportePago   = 0;
    aPagoProv.IdPagoDoc     = 0;
    aPagoProv.EstadoPago    = "";
    aPagoProv.FPago         = "";
    aPagoProv.Mora          = 0;
    aPagoProv.Obs           = "";
    aPagoProv.DocPago       = "";
    aPagoProv.IdPagoProv    = 0;
}

function AgregarPagoProv(xval){

    esAgregar = xval;
    var xPendientePlan = parseFloat(aPagoProv.PendientePlan);
    var xPendiente     = parseFloat(aPagoProv.Pendiente);
    var lval = (aPagoProv.FormaPago == "Contado" )? true:false;


    switch(esAgregar){
    case 'asociar':

	if(aPagoProv.FormaPago == 'Contado')
	    id("rdiocomprobante").setAttribute("collapsed",true);

	titulo   = (cModo == 'add')? "Asociar Pago":"Modificando Pago Asociado";
	var cbtn = (cModo == 'add')? "AltaPago()":"Modificar()";
	var xval = false;
	var vval = true;
	var doc  = (cModo == 'add')?"PAGO UNICO":aPagoProv.DocPago;

	(cModo == 'add')? CleanFormPago():editardocumento();

	break;

    case 'planificar':
	
	titulo   = (cModo == 'add')? "Planificar Pago":"Modificando Pago Planificado"
	var cbtn = (cModo == 'edit')?"AltaPagoPlan()":"Modificar()";
	var xval = true;
	var vval = false;
	var doc  = (cModo == 'add')? "LETRA A":aPagoProv.DocPago;

	if(aPagoProv.FormaPago == 'Contado')
	    return alert("gPOS:  El pago es al contado");
    
	(cModo == 'add')? iniciarplanificarPago():editardocumento();

	break;

    }

    id("btnAceptar").setAttribute('ocommand',cbtn);
    id("filaImportePago").setAttribute('collapsed',xval);
    id("Pago_Modo").setAttribute('collapsed',xval);
    id("filaImportePlan").setAttribute('collapsed',vval);
    id("filaFechaPlan").setAttribute('collapsed',vval);
    id("TipoCambioMoneda").setAttribute('collapsed',true);
    id("filaImporteMora").setAttribute('collapsed',xval);
    id("etiquetaPago").setAttribute('label',titulo);
    //id("radioplanificar").setAttribute('disabled',lval);
    (cModo == 'add')? id("DocumentoPlan").setAttribute('value',doc):false;

}

function iniciarplanificarPago(){
    var xPendiente = parseFloat(aPagoProv.Pendiente);
    var xPendientePlan = parseFloat(aPagoProv.PendientePlan);
    var imppte         = parseFloat(xPendiente - xPendientePlan).toFixed(2);

    CleanFormPago();

    id("TipoMoneda").value       = aPagoProv.IdMonedaCbte;
    id("TipoCambioMoneda").value = (aPagoProv.IdMonedaCbte==1)? '':aPagoProv.CambioCbte;
    id("ImportePendiente").value = imppte;

}

function CleanFormPago(){
    id("TipoMoneda").value       = 0;
    id("TipoCambioMoneda").value = "";
    id("ImportePago").value      = "0.00";
    id("xFormaPago").value       = 0;
    id("ImporteMora").value      = 0;
    id("ImportePendiente").value = aPagoProv.Pendiente;
    id("TipoCambioMoneda").setAttribute('collapsed',true);
    id("TipoExtra").setAttribute("collapsed",true);
}

function SalirToComprobante(CbtePendiente,CbteEstado){
    var codigo    = aPagoProv.Codigo;
    var boxframe  = parent.document.getElementById("webDetPago");
    var listcomp  = parent.document.getElementById("listboxComprobantesPagos");
    var boxns     = parent.document.getElementById("boxDetPago");
    var boxcompra = parent.document.getElementById("boxComprobantesCompras");

    if(CbtePendiente && CbteEstado){
	parent.actualizaEstadoImportePendiente(CbtePendiente,CbteEstado);
    }
    parent.RevisarPagoSeleccionada();
    parent.loadDetallesPago(aPagoProv.IdComprobante);

    boxframe.setAttribute("src","about:blank");
    listcomp.setAttribute("collapsed","false");
    boxns.setAttribute("collapsed","true");
    boxcompra.setAttribute("collapsed","false");
}


function MostrarPagoDocumento(xval){
 
    var detPagoDoc    = xval;

    if(detPagoDoc == 0)
	CleanFormPago();
    
    if(detPagoDoc != 0){
	var aPagosDoc      = (detPagoDoc)?detPagoDoc.split("~"):'';
	aPago.IdPagoDoc    = (detPagoDoc)?aPagosDoc[0]:'';
	aPago.IdMoneda     = (detPagoDoc)?aPagosDoc[1]:'';
	aPago.Moneda       = (detPagoDoc)?aPagosDoc[2]:'';
	aPago.CambioMoneda = (detPagoDoc)?aPagosDoc[3]:'';
	aPago.Importe      = (detPagoDoc)?aPagosDoc[4]:'';
	aPago.TipoProv     = (detPagoDoc)?aPagosDoc[5]:'';

	var lval           = (aPago.CambioMoneda == 1)? true:false;
	var PagoImporte    = parseFloat(aPago.Importe).toFixed(2);

	var IdMoneda       = aPagoProv.IdMonedaCbte;

	if(IdMoneda != aPago.IdMoneda){
	    //alert("gPOS:   Pago al Proveedor\n\n - El pago debe ser en la misma moneda");
	    //return MostrarPagoDocumento(0);

	}

	if(IdMoneda == 1 && aPago.IdMoneda != 1){
	    PagoImporte = parseFloat(aPago.Importe*aPago.CambioMoneda).toFixed(2);
	}
	
	if(IdMoneda != 1 && aPago.IdMoneda == 1){
	    var CambioMoneda = aPagoProv.CambioCbte;
	    var oldPagoImporte = parseFloat(aPago.Importe/CambioMoneda).toFixed(2);
	    PagoImporte = parseFloat(aPago.Importe/aPago.CambioMoneda).toFixed(2);

	    aPago.Desviacion = (oldPagoImporte - PagoImporte);

	    //var CambioMoneda = aPagoProv.CambioCbte;
	    //PagoImporte = parseFloat(aPago.Importe/CambioMoneda).toFixed(2);

	}

	if(IdMoneda != 1 && aPago.IdMoneda != 1)
	    aPago.Desviacion = (aPagoProv.CambioCbte*aPago.Importe - aPago.Importe*aPago.CambioMoneda);

	ActualizaPendiente(PagoImporte);

	id("TipoMoneda").value       = aPago.IdMoneda;
	id("ImportePago").value      = aPago.Importe;
	id("TipoCambioMoneda").value = aPago.CambioMoneda;
	id("TipoCambioMoneda").setAttribute('collapsed',lval);
    }
}


function ActualizaPendiente(PagoImporte){

    var oImportePlan   = id("ImportePlan");
    if(!parseFloat( oImportePlan.value )){
	oImportePlan.value = 0;
        oImportePlan.select();
    }

    var lval           = (esAgregar == 'planificar')?true:false;
    var Pendiente      = 0;
    var ImpAnt         = 0;
    var nPendiente     = 0;
    var nImportePlan   = 0;
    var xNuevaMora     = 0;
    var xPagoImporte   = parseFloat( PagoImporte );
    var xImportePlan   = parseFloat( oImportePlan.value );
    var xPendiente     = parseFloat( aPagoProv.Pendiente );
    var xPendientePlan = parseFloat( aPagoProv.PendientePlan );
    var xImporteCbte   = parseFloat( aPagoProv.ImporteCbte );
    var xImportePago   = parseFloat( aPagoProv.ImportePago );
    var xImporte       = parseFloat( aPago.Importe );
    var xDiferencia    = parseFloat( aPagoProv.Diferencia );

    switch(cModo){
    case 'add':
	nImportePlan = (esAgregar == 'planificar')? xImportePlan : 0;
	Pendiente    = (esAgregar == 'asociar'   )? xPendiente   : parseFloat(xPendiente - xPendientePlan).toFixed(2);
	nPendiente   = (esAgregar == 'asociar'   )? Pendiente - xPagoImporte : Pendiente - nImportePlan;

	break;

    case 'edit':
	nImportePlan = (esAgregar == 'planificar')? parseFloat(oImportePlan.value)+(xPendientePlan-xImportePago ): xImportePago;
	Pendiente    = (esAgregar == 'asociar'   )? xPendiente:xImporteCbte - nImportePlan;
	impAnt       = (esAgregar == 'asociar' && aPagoProv.EstadoPago == 'Confirmado')? xImportePago:0;
	nPendiente   = (esAgregar == 'asociar'   )? Pendiente - PagoImporte + parseFloat(impAnt)-xDiferencia:Pendiente;
	break;
    }

    if(esAgregar == 'planificar' && nPendiente < 0){
	oImportePlan.value = 0;
	oImportePlan.select();
	return ActualizaPendiente(PagoImporte);
    }

    nPendiente = parseFloat(nPendiente).toFixed(2);
    xNuevaMora = (nPendiente <  0 )? parseFloat(nPendiente).toFixed(2)*(-1):0;
    nPendiente = (nPendiente >= 0 )? nPendiente:0;
    var xval   = (xNuevaMora == 0 )? true:false;

    if(xNuevaMora > 0 && nPendiente == 0)
	var cTipoDif = (aPagoProv.EstadoCbte == 'Vencida')? 'Mora':'Excedente';

    id("ImportePendiente").setAttribute('value',nPendiente);
    id("ImportePendiente").value = nPendiente;
    id("ImporteMora").value = xNuevaMora;
    id("TipoExtra").setAttribute("value",cTipoDif);
    id("TipoExtra").value = cTipoDif;
    id("TipoExtra").setAttribute("collapsed",xval);
    (esAgregar == 'planificar')? id("filaImporteMora").setAttribute('collapsed',lval):false;
}

function AltaPago(){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=AltaPago";
    var data     = "";
    var res;

    var Documento     = id("DocumentoPlan").value;
    var Obs           = id("xObservacion").value;
    Obs               = (Obs)? '- '+Obs:"";
    var IdComprobante = aPagoProv.IdComprobante;

    data = data + "&xidcp="+ IdComprobante;
    data = data + "&xdes="+ Documento;
    data = data + "&xobs="+ Obs;


    if(esAgregar == 'asociar'){
	var ImporteMora     = id("ImporteMora").value;
	var EstadoPago      = 'Confirmado';
	var IdMoneda        = aPago.IdMoneda;
	var esAlta          = true;
	var Desviacion      = aPago.Desviacion;
	var xNuevoPendiente = id("ImportePendiente").value;
	var TipoDif         = id("TipoExtra").value;
	var CbtePendiente   = xNuevoPendiente;
	var CbteEstado      = (CbtePendiente == 0)? "Pagada":"Empezada";
	var ImportePagar    = aPago.Importe;

	if(aPagoProv.IdMonedaCbte == aPago.IdMoneda)
	    ImportePagar = (CbtePendiente == 0)? aPagoProv.Pendiente:ImportePagar;
	else{
	    if(aPagoProv.IdMonedaCbte == 1 && aPago.IdMoneda != 1){
		ImporteMora  = parseFloat(ImporteMora/aPago.CambioMoneda).toFixed(1);
		ImportePagar = parseFloat(aPago.Importe-ImporteMora).toFixed(2);
	    }
	    if(aPagoProv.IdMonedaCbte != 1 && aPago.IdMoneda == 1){
		ImporteMora  = parseFloat(ImporteMora*aPago.CambioMoneda).toFixed(1);
		ImportePagar = parseFloat(aPago.Importe-ImporteMora).toFixed(2);
	    }
	}

	/*
	if(aPagoProv.IdMonedaCbte != aPago.IdMoneda){
	    alert("gPOS:   Pago al Proveedor\n\n - El pago debe ser en la misma moneda");
	    return MostrarPagoDocumento(0);
	}
	*/

	data = data + "&xidppd="+ aPago.IdPagoDoc;
	data = data + "&ximp="+ ImportePagar;
	data = data + "&xmora="+ ImporteMora;
	data = data + "&xep="+ EstadoPago;
	data = data + "&xidm="+ IdMoneda;
	data = data + "&xdesviacion="+ Desviacion;
	data = data + "&xtipodif="+ TipoDif;
	data = data + "&xprov="+ aPagoProv.IdProveedor;
	data = data + "&xcambiocbte="+ aPago.CambioMoneda;
	data = data + "&xlocal="+ aPagoProv.IdLocalCbte;
	data = data + "&xtipoprov="+ aPago.TipoProv;
	data = data + "&xesagregar="+ esAgregar;

	msj=' Va confirmar Pago ';
    }
    if(esAgregar == 'planificar'){
	var ImportePlan  = id("ImportePlan").value;
	var FechaPlan    = id("FechaPagoPlan").value;
	var IdMonedaPlan = aPagoProv.IdMonedaCbte;
	var xNuevoPendiente = aPagoProv.Pendiente;

	data = data + "&xiplan="+ ImportePlan;
	data = data + "&xfplan="+ FechaPlan;
	data = data + "&xidm="+ IdMonedaPlan;
	data = data + "&xesagregar="+ esAgregar;

	msj=' Agregar Nuevo Pago Planificado ';
    }

    if(esAgregar == 'asociar' && id("xFormaPago").value == 0 )
	return alert("gPOS:  Pago al Proveedor\n\n - Seleccione un Documento de Pago ...");

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

    if(res == 'completa') return alert("gPOS \n El Pago ya está asociado");

    if(!parseInt(res)) 
	alert(po_servidorocupado+'\n\n -'+res+'-');	

    if(esAgregar == "asociar"){
	ActualizarPendienteComprobante(aPagoProv.IdComprobante,xNuevoPendiente,
				       aPagoProv.ImporteCbte,false,aPago.IdPagoDoc);
	ActualizarEstadoPagoDoc(aPago.IdPagoDoc,false);
    }

    SalirToComprobante(CbtePendiente,CbteEstado);
    CleanArrayPago();
}

function AltaPagoPlan(){
    AltaPago();

}

function ActualizarPendienteComprobante(idcomprobante,pendiente,
					totalcomprobante,xdoc,IdPagoDoc){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ActualizaPendienteComprobante";
    var data     = "";
    var res;
    var estadodoc= (aPagoProv.EstadoCbte == 'Pendiente' || aPagoProv.Pendiente == aPagoProv.Importe)? 'Confirmado':"";

    data = data + "&xidcp="+idcomprobante;
    data = data + "&xpte="+ pendiente;
    data = data + "&xtcbte="+ totalcomprobante;
    data = data + "&xedc="+ estadodoc;
    data = data + "&ximp="+ aPago.Importe;
    data = data + "&ximpant="+ aPagoProv.ImportePago;
    data = data + "&xdoc="+ xdoc;
    data = data + "&xidpd="+ IdPagoDoc;
    data = data + "&xstdopago="+ aPagoProv.EstadoPago;
    data = data + "&xop="+ esAgregar;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        //NOTA: posiblemente no tenemos conexion.
        res = false;	
    }

    if(res == 'completa') return alert("gPOS \n El Comprobante ya está Pagada");

    if(!parseInt(res)) 
	alert(po_servidorocupado+'\n\n -'+res+'-');	

}

function editardocumento(){

    if(aPagoProv.EstadoPago== 'Confirmado')
	id("rdiocomprobante").setAttribute("collapsed",true);
    var xPendiente     = parseFloat(aPagoProv.Pendiente);
    var xPendientePlan = parseFloat(aPagoProv.PendientePlan);
    var imppte         = (esAgregar == 'asociar')? xPendiente:(xPendiente - xPendientePlan);
    var diferencia     = 0;
    var tipoextra      = "";

    imppte = parseFloat(imppte.toFixed(2));

    aPagoProv.Diferencia = diferencia;

    if(aPagoProv.Mora == 0 && aPagoProv.Excedente != 0){
	diferencia = aPagoProv.Excedente;
	aPagoProv.Diferencia = diferencia;
	tipoextra  = 'Excedente';
    }
    if(aPagoProv.Mora != 0 && aPagoProv.Excedente == 0){
	diferencia = aPagoProv.Mora;
	aPagoProv.Diferencia = diferencia;
	tipoextra  = 'Mora';
    }

    var vval = (diferencia != 0)? false:true;

    id("ImportePago").value      = aPagoProv.ImportePago;
    //id("xObservacion").value     = aPagoProv.Obs.replace(/~/g,"\n");
    id("ImporteMora").value      = diferencia;
    id("TipoExtra").value        = tipoextra;
    id("TipoMoneda").value       = aPagoProv.IdMonedaDoc;
    id("ImportePlan").value      = aPagoProv.ImportePago;    
    id("ImportePendiente").value = imppte;
    id("FechaPagoPlan").setAttribute("value",aPagoProv.FPago);
    id("ImportePendiente").setAttribute('value',imppte);
    id("btnAceptar").setAttribute('oncommand',cbtnAceptar);
    id("TipoExtra").setAttribute('collapsed',vval);

    (aPagoProv.EstadoPago == 'Confirmado')? id("radioagregar").setAttribute('selected',true):false;

}

function Modificar(){
    var xdoc      = "Modificar";
    ModificarPago(xdoc);
    SalirToComprobante(false,false);
}


function ModificarPago(xdoc){
    var idpagoprov      = aPagoProv.IdPagoProv;
    var idcomprobante   = aPagoProv.IdComprobante;
    var extra           = parseFloat(aPagoProv.Excedente) + parseFloat(aPagoProv.Mora);
    var data            = "";
    var aPagoDoc        = false;
    var z               = null;

    switch(xdoc){
    case "Modificar":
        var Observacion   = aPagoProv.Obs;

	if(esAgregar == 'asociar'){
	    var formapago       = id("xFormaPago").value;
	    var ImporteMora     = id("ImporteMora").value;
	    var IdMoneda        = (formapago==0)?aPagoProv.IdMonedaDoc:aPago.IdMoneda;
	    var xNuevoPendiente = id("ImportePendiente").value;
	    var Opcion          = 1;
	    var TipoDif         = id("TipoExtra").value;
	    var Desviacion      = (aPago.Desviacion)?aPago.Desviacion:0;
	    var IdPagoDoc       = (formapago==0)?aPagoProv.IdPagoDoc:aPago.IdPagoDoc;
	    var Importe         = (formapago==0)?aPagoProv.ImportePago:aPago.Importe;
	    var Estado          = 'Confirmado';
	    var CbtePendiente   = xNuevoPendiente;
	    var CbteEstado      = (CbtePendiente == 0)? "Pagada":"Empezada";
	    var aPagoDoc        = (formapago != 0 && aPagoProv.EstadoPago == 'Confirmado')? true:false;

	    data = data + "&xidpd="+ IdPagoDoc;
	    data = data + "&ximp="+ Importe;
	    data = data + "&xmora="+ ImporteMora;
	    data = data + "&xidm="+ IdMoneda;
	    data = data + "&xdes="+ Desviacion;
	    data = data + "&xsp="+ Estado;
	    data = data + "&idcbte="+ aPagoProv.IdComprobante;
	    data = data + "&xlocal="+ aPagoProv.IdLocalCbte;
	    data = data + "&xtipodif="+ TipoDif;
	    data = data + "&xtipoprov="+ aPago.TipoProv;
	    data = data + "&xesagregar="+ esAgregar;
	    data = data + "&xcambiocbte="+ aPago.CambioMoneda;
	    data = data + "&xprov="+ aPagoProv.IdProveedor;

	    msj = ' Se modificará el Pago ';
	}

	if(esAgregar == 'planificar'){
	    var FechaPlan     = id("FechaPagoPlan").value;
	    var ImportePlan   = id("ImportePlan").value;
	    var IdMonedaPlan  = aPagoProv.IdMonedaDoc;
	    var Opcion        = 2;

	    data = data + "&xiplan="+ ImportePlan;
	    data = data + "&xfplan="+ FechaPlan;
	    data = data + "&xidm="+ IdMonedaPlan;
	    data = data + "&xesagregar="+ esAgregar;
	    
	    msj=' se modificará datos del Pago ';
	}
	var nuevaObs  = id("xObservacion").value;
	Observacion   = (Observacion == ' ')? "":Observacion;
	Observacion   += (nuevaObs != '')? Observacion+'- '+nuevaObs:"";

	var Documento = id("DocumentoPlan").value;

	data = data + "&xdesc="+ Documento;
	data = data + "&xobs="+ Observacion;
	data = data + "&Opcion="+ Opcion;
    }

    if(xdoc == 'Modificar' && formapago == 0)
	return alert('gPOS: \n Seleccione un Pago' );
    
    if(!confirm('gPOS: '+msj+',\n'+' ¿desea continuar?'))
	return editardocumento();

    var xrequest = new XMLHttpRequest();

    var url = "modpagoscobros.php?"+
	"modo=ModificaPago"+
	"&idpprov="+idpagoprov+
	"&idcbte="+idcomprobante+
	""+data+
	"&xdoc="+xdoc;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');

    try {
	xrequest.send(null);

    } catch(z){
	return;
    }
    
    var res = xrequest.responseText;

    if(res == 'completa') return alert("gPOS \n El Pago ya está asociado");

    if(!parseInt(res)) 
	alert(po_servidorocupado+'\n\n -'+res+'-');	

    if(esAgregar == 'asociar'){
	ActualizarPendienteComprobante(aPagoProv.IdComprobante,xNuevoPendiente,
				       aPagoProv.ImporteCbte,xdoc,IdPagoDoc);
	ActualizarEstadoPagoDoc(IdPagoDoc,Estado);
	(aPagoDoc)? ActualizarEstadoPagoDoc(aPagoProv.IdPagoDoc,'Pendiente'):false;
    }

    SalirToComprobante(CbtePendiente,CbteEstado);
    CleanArrayPago();
}

function ActualizarEstadoPagoDoc(idpagodoc,estado){
    var xrequest = new XMLHttpRequest();
    var url      = "modpagoscobros.php?modo=ActualizaEstadoPagoDoc";
    var data     = "";
    var res;
    var estado   = (!estado)? 'Confirmado':estado;

    data = data + "&xidppd="+idpagodoc;
    data = data + "&xestado="+ estado;

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

function recalcularExcedentePagoDoc(ImporteMora){
    var exceso = ImporteMora;
    if(aPagoProv.IdMonedaCbte == 1 && aPago.IdMoneda !=1 )
	exceso = ImporteMora/aPago.CambioMoneda;
    if(aPagoProv.IdMonedaCbte != 1 && aPago.IdMoneda == 1 )
	exceso = ImporteMora*aPagoProv.CambioCbte;

    return parseFloat(exceso).toFixed(2);
}