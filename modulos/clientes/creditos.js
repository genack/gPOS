var id = function(name) { return document.getElementById(name); }

var icuentas      = 0;
var idctabancaria = 0;
var esmodifica    = false;

var ccImporte      = 0;
var ccConcepto     = '' ;
var ccTipo         = 0;
var ccFecha        = '';
var ccCuenta       = 0;
var ccCodOperacion = '000000';
var ccObs          = '';
var ccIdCredito    = 0;

function VaciarBusquedaCreditos(){
    var lista = id("listboxCredito");
    
    for (var i = 0; i < icreditos; i++) { 
        kid = id("creditos_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    icreditos = 0;
}

function BuscarCreditos(){
    VaciarBusquedaCreditos();
    var Desde   = id("fechaDesde").value;
    var Hasta   = id("fechaHasta").value;
    var TipoMov = id("listTipoMovimiento").value;
    RawBuscarCreditos(xidc,Desde,Hasta,TipoMov, AddLineaCreditos);
}

var serialNum = (Math.random()*9000).toFixed();
function RawBuscarCreditos(IdCliente,Desde,Hasta,TipoMov,FuncionRecogerDetalles){
    var obj = new XMLHttpRequest();
    var url = "../../services.php?modo=mostrarCreditosCliente&IdCliente=" + escape(IdCliente) 
        + "&desde=" + Desde 
        + "&hasta=" + Hasta 
        + "&mov=" + TipoMov 
        + "&r=" + serialNum;		
    serialNum++;
    obj.open("GET",url,false);
    obj.send(null);

    var IdClienteCredito,FechaOperacion,Tipo,Concepto,Importe,IdCuentaBancaria,CodOperacion,Cuenta,Observaciones;
    var node,t,i;
    var numitem = 0;
    var tentrada = 0;
    var tsalida  = 0;
    var tcredito = 0;

    if (!obj.responseXML) return alert('gPOS: '+po_servidorocupado);		
    
    var xml = obj.responseXML.documentElement;
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node && node.childNodes && node.childNodes.length >0){
	    t = 0;
	    numitem++;
	    if (node.childNodes[t].firstChild){
                IdClienteCredito  = node.childNodes[t++].firstChild.nodeValue;
                Concepto          = node.childNodes[t++].firstChild.nodeValue;
                Importe           = node.childNodes[t++].firstChild.nodeValue;
                Tipo              = node.childNodes[t++].firstChild.nodeValue;
                FechaOperacion    = node.childNodes[t++].firstChild.nodeValue;
		IdCuentaBancaria  = node.childNodes[t++].firstChild.nodeValue;
                CodOperacion      = node.childNodes[t++].firstChild.nodeValue;
                Observaciones     = node.childNodes[t++].firstChild.nodeValue;
                Cuenta            = node.childNodes[t++].firstChild.nodeValue;

		if(Tipo == 0)
		    tentrada = parseFloat(tentrada) + parseFloat(Importe);
		if(Tipo == 1)
		    tsalida  = parseFloat(tsalida) + parseFloat(Importe);

                FuncionRecogerDetalles(numitem,IdClienteCredito,Concepto,Importe,Tipo,
				       FechaOperacion,IdCuentaBancaria,CodOperacion,
				       Observaciones,Cuenta);
	    }
        }
    }
    tcredito = parseFloat(tentrada) + parseFloat(tsalida);
    id("TotalEntrada").value = parent.cMoneda[1]['S']+formatDinero(tentrada);
    id("TotalSalida").value  = parent.cMoneda[1]['S']+formatDinero(tsalida);
    id("TotalCredito").value = parent.cMoneda[1]['S']+formatDinero(tcredito);
    
}
var icreditos = 0;
function AddLineaCreditos(numitem,IdClienteCredito,Concepto,Importe,Tipo,
			  FechaOperacion,IdCuentaBancaria,CodOperacion,
			  Observaciones,Cuenta){

    var lista = id("listboxCredito");
    var xitem,xIdClienteCredito,xFechaOperacion,xTipo,xConcepto,xImporte,xIdCuentaBancaria,xCodOperacion,xCuenta,xObservaciones;

    var afecha       = FechaOperacion.split("~");
    var FechaOPLabel = afecha[0];
    var FechaOPValue = afecha[1];

    var lTipo = (Tipo == 1)? 'Salida':'Entrada';

    xitem = document.createElement("listitem");
    xitem.value = IdClienteCredito;
    xitem.setAttribute("id","creditos_" + icreditos);
    icreditos++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xFechaOperacion = document.createElement("listcell");
    xFechaOperacion.setAttribute("value",FechaOPValue);
    xFechaOperacion.setAttribute("id","creditos_fecha_" + IdClienteCredito);
    xFechaOperacion.setAttribute("label",FechaOPLabel);
    xFechaOperacion.setAttribute("style","text-align:left");

    xTipo = document.createElement("listcell");
    xTipo.setAttribute("value",Tipo);
    xTipo.setAttribute("id","creditos_tipo_" + IdClienteCredito);
    xTipo.setAttribute("label",lTipo);

    xConcepto = document.createElement("listcell");
    //xConcepto.setAttribute("value",CantidadDevuelta);
    xConcepto.setAttribute("id","creditos_concepto_" + IdClienteCredito);
    xConcepto.setAttribute("label",Concepto);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("value",Importe);
    xImporte.setAttribute("id","creditos_importe_" + IdClienteCredito);
    xImporte.setAttribute("label",formatDinero(Importe));
    xImporte.setAttribute("style","text-align:right;font-weight: bold;");

    xCuenta = document.createElement("listcell");
    xCuenta.setAttribute("value",IdCuentaBancaria);
    xCuenta.setAttribute("id","creditos_cuenta_" + IdClienteCredito);
    xCuenta.setAttribute("label",Cuenta);

    xCodOperacion = document.createElement("listcell");
    //xCodOperacion.setAttribute("value",CantidadDevuelta);
    xCodOperacion.setAttribute("id","creditos_codoperacion_" + IdClienteCredito);
    xCodOperacion.setAttribute("label",CodOperacion);

    xObservaciones = document.createElement("listcell");
    //xObservaciones.setAttribute("value",CantidadDevuelta);
    xObservaciones.setAttribute("id","creditos_obs_" + IdClienteCredito);
    xObservaciones.setAttribute("collapsed","true");
    xObservaciones.setAttribute("label",Observaciones);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xFechaOperacion );
    xitem.appendChild( xTipo );
    xitem.appendChild( xConcepto );
    xitem.appendChild( xImporte );
    xitem.appendChild( xCuenta );
    xitem.appendChild( xCodOperacion );
    xitem.appendChild( xObservaciones );	
    lista.appendChild( xitem );
}

function recuprarCreditosSeleccionado(){
    var idex = document.getElementById("listboxCredito").selectedItem;
    if( ! idex ) return;

    ccIdCredito    = idex.value;
    ccImporte      = id("creditos_importe_"+idex.value).getAttribute("value");
    ccConcepto     = id("creditos_concepto_"+idex.value).getAttribute("label");
    ccTipo         = id("creditos_tipo_"+idex.value).getAttribute("value");
    ccFecha        = id("creditos_fecha_"+idex.value).getAttribute("value");
    ccCuenta       = id("creditos_cuenta_"+idex.value).getAttribute("value");
    ccCodOperacion = id("creditos_codoperacion_"+idex.value).getAttribute("label");
    ccObs          = id("creditos_obs_"+idex.value).getAttribute("label");
}

function loadfocus(){
    BuscarCreditos();
    if(esmodifica){
	esmodifica = false;
    }
}

function EliminarCredito() {
    var idex = document.getElementById("listboxCredito").selectedItem;
    if( ! idex ) return;

    var txtcredito    = ccImporte;
    if( confirm('gPOS:\n'+
		'       Desea eliminar el crédito:\n\n'+
		'       '+txtcuenta) ) {
	var url = 'selcreditos.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=eliminacredito';
	url = url + '&xid=' + ccIdCredito;
	document.location.href = url;
    }
}

function ModificarCredito() {
    var entfinanciera = trim(document.getElementById('txtEntFinanciera').value);
    var nrocuenta     = trim(document.getElementById('txtNroCuenta').value);
    var idmoneda      = document.getElementById('listIdMoneda').value;
    var idproveedor   = document.getElementById('listIdProveedor').value;
    var estado        = document.getElementById('listEstado').value;
    var observacion   = trim(document.getElementById('txtObservaciones').value);

    if(xidprov == 0)
	idproveedor = 0;

    if (entfinanciera == "" || nrocuenta == '')
        return;

    url = 'selcuentabancaria.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=modificacuenta';
    url = url + '&financiera=' + entfinanciera;
    url = url + '&nrocuenta=' + nrocuenta;
    url = url + '&idmon=' + idmoneda;
    url = url + '&idprov=' + idproveedor;
    url = url + '&estado=' + estado;
    url = url + '&obs=' + observacion;
    url = url + '&xidprov=' + xidprov;
    url = url + '&xcta=' + trim(entfinanciera);
    url = url + '&xid=' + idctabancaria;
    url = url + '&xnro=' + nro;

    document.location.href = url;
    parent.RegenCuentasBancarias();
    VerFormCreditoCliente(true);
    esmodifica = true;
}

function VerFormCreditoCliente(xval){
    if(xval) LimpiarFormCreditoCliente();

    document.getElementById('formCreditoCliente').setAttribute('collapsed',xval);
    document.getElementById('ListaCreditoCliente').setAttribute('collapsed',!xval);
    document.getElementById('boxbusqueda').setAttribute('collapsed',!xval);

    document.getElementById('btnGuardaCreditoCliente').setAttribute('label','Guardar');
    document.getElementById('btnGuardaCreditoCliente').setAttribute('oncommand','GuardarCreditoCliente()');
}

function CancelarCreaCuenta(){
    LimpiarFormCreditoCliente();
    VerFormCreditoCliente(true);
}

function LimpiarFormCreditoCliente(){
    id("txtImporte").value          = '';
    id("txtConcepto").value         = '';
    id("txtFechaOperacion").value   = parent.calcularFechaActual('fecha');
    id("txtHoraOperacion").value    = parent.calcularFechaActual('hora');
    id('txtObservaciones').value    = "";
    id('titleCreditoCliente').label = 'Nuevo Crédito';
    id("txtCodigoOperacion").value  = '';
}


function GuardarCreditoCliente(){
    var url;

    var importe  = parseFloat(trim(id('txtImporte').value));
    var concepto = trim(id('txtConcepto').value);
    var fechaop  = id('txtFechaOperacion').value+' '+id('txtHoraOperacion').value;
    var idcuenta = id('listIdCuentaBancaria').value;
    var codop    = trim(id('txtCodigoOperacion').value);
    var obs      = trim(id('txtObservaciones').value);
    codop        = (!codop || codop <= '00000')? '000000':codop;
    var tipocredito = id('listCredito').value;

    if( parent.esCajaCerrada() == 1 && tipocredito == 'Efectivo')
	return alert("gPOS Caja :\n\n"+
		     "  ESTADO CAJA : CERRADO \n\n"+
		     "  Debe -Abrir Caja- para continuar.")

    if(importe <= 0 || xidc <= 1)
	return;

    if(tipocredito == 'Bancario' && (!idcuenta || idcuenta == ''))
	return;

    xcredito = parseFloat(importe) + parseFloat(xcredito);

    url = 'selcreditos.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=salvacredito';
    url = url + '&importe=' + importe;
    url = url + '&concepto=' + concepto;
    url = url + '&fechaop=' + fechaop;
    url = url + '&idcuenta=' + idcuenta;
    url = url + '&codop=' + codop;
    url = url + '&obs=' + obs;
    url = url + '&xidc=' + xidc;
    url = url + '&xidu=' + xidu;
    url = url + '&xcliente=' + xcliente;
    url = url + '&xnro=' + 0;
    url = url + '&xcredit=' + xcredito;
    url = url + '&xtipo=' + tipocredito;

    document.location.href = url;

    VerFormCreditoCliente(true);
    parent.imprimirCreditoCliente(importe);
    //parent.closepopup();
}

function ModificarDatoCredito(){
    return;
    var idex = document.getElementById("listboxCredito").selectedItem;
    var xdato = document.getElementById("cuentas_"+idex.value).getAttribute("value");
    idctabancaria = idex.value;

    var adato = xdato.split("~");

    document.getElementById('txtNroCuenta').value     = adato[1];
    document.getElementById('txtEntFinanciera').value = adato[2];
    document.getElementById('listIdMoneda').value     = adato[3];
    document.getElementById('listIdProveedor').value  = adato[4];
    document.getElementById('listEstado').value       = adato[5];
    document.getElementById('txtObservaciones').value = adato[6];
    document.getElementById('titleCuentaBancaria').label = 'Modificar Cuenta Bancaria';
    document.getElementById('txtNroCuenta').label     = adato[1];

    var esprov = (adato[4] == 0);
    document.getElementById("rowProveedor").setAttribute("collapsed",esprov);
    xidprov = adato[4];

    document.getElementById('btnGuardaCreditoCliente').setAttribute('label','Modificar');
    document.getElementById('btnGuardaCreditoCliente').setAttribute('oncommand','ModificarCuenta()');
   
    document.getElementById('formCreditoCliente').setAttribute('collapsed',false);
    document.getElementById('ListaCreditoCliente').setAttribute('collapsed',true);
    //VerFormCreditoCliente(false);  
}

function changeTipoCredito(xtipo){

    var rowcuenta = document.getElementById('rowCuenta');
    var rowcodop  = document.getElementById('rowCodOperacion');
    var rowobs    = document.getElementById('rowObservaciones');

    if(xtipo == 'Bancario'){
	rowcuenta.setAttribute('collapsed',false);
	rowcodop.setAttribute('collapsed',false);
	rowobs.setAttribute('collapsed',false);
    }
    else{
	rowcuenta.setAttribute('collapsed',true);
	rowcodop.setAttribute('collapsed',true);
	rowobs.setAttribute('collapsed',true);
    }
}