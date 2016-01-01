var icuentas      = 0;
var idctabancaria = 0;
var esmodifica    = false;

function BuscarCuenta(){
    var filtro = document.getElementById('buscacuenta').value;
    var ns = new String(filtro);
    ns = ns.toUpperCase();
    var theList=document.getElementById('listboxCuenta');
    filas = theList.itemCount;
    for(var i=0;i<filas;i++){
        theList.removeItemAt(0);
    }
    if(ns==""){
        for(var i=0;i<filas;i++){
            theList.removeItemAt(0);
        }
        for(var i in stcta){
	    var acta = stcta[i].split("~");

	    var lista = document.getElementById("listboxCuenta");
	    var xitem = document.createElement("listitem");
	    xitem.value = i;
	    xitem.setAttribute("id","cuenta_" + icuentas);
	    icuentas++;

            var row1 = document.createElement('listcell');
            row1.setAttribute('label',acta[0]);
            row1.setAttribute('value',i);

            var row2 = document.createElement('listcell');
            row2.setAttribute('label',acta[1]);
            row2.setAttribute('value',i);

            var row3 = document.createElement('listcell');
            row3.setAttribute('label',acta[2]);
            row3.setAttribute('value',i);

            var row4 = document.createElement('listcell');
            row4.setAttribute('collapsed',"true");
            row4.setAttribute('value',stcta[i]);
	    row4.setAttribute('id',"cuentas_"+i);

	    xitem.appendChild( row1 );
	    xitem.appendChild( row2 );
	    xitem.appendChild( row3 );
	    xitem.appendChild( row4 );
	    lista.appendChild( xitem );
        }
    }
    else
    {
        for(var i=0;i<filas;i++){
            theList.removeItemAt(0);
        }
        for(var i in stcta){

            var cadena = new String(stcta[i]);
            cadena = cadena.toUpperCase();
            if(cadena.indexOf(ns) != -1){

		var acta = stcta[i].split("~");
		var lista = document.getElementById("listboxCuenta");
		var xitem = document.createElement("listitem");
		xitem.value = i;
		xitem.setAttribute("id","cuenta_" + icuentas);
		icuentas++;

		var row1 = document.createElement('listcell');
		row1.setAttribute('label',acta[0]);
		row1.setAttribute('value',i);
		
		var row2 = document.createElement('listcell');
		row2.setAttribute('label',acta[1]);
		row2.setAttribute('value',i);

		var row3 = document.createElement('listcell');
		row3.setAttribute('label',acta[2]);
		row3.setAttribute('value',i);
		
		var row4 = document.createElement('listcell');
		row4.setAttribute('collapsed',"true");
		row4.setAttribute('value',stcta[i]);
		row4.setAttribute('id',"cuentas_"+i);

		xitem.appendChild( row1 );
		xitem.appendChild( row2 );
		xitem.appendChild( row3 );
		xitem.appendChild( row4 );
		lista.appendChild( xitem );

            }
            var elemento = theList.getItemAtIndex(0);
            theList.selectItem(elemento);
        }
    }
}

function agnadirDirecto(){
    var theList=document.getElementById('listboxCuenta');

    if(theList.selectedIndex == -1){
        return;
    }
    theList.ondblclick();
}

function loadfocus(){
    document.getElementById('buscacuenta').focus();
    BuscarCuenta();
    if(esmodifica){
	agnadirDirecto();
	esmodifica = false;
    }
}

function EliminarCuenta() {
    var idex = document.getElementById("listboxCuenta").selectedItem;
    if( ! idex ) return;
    var xdato = document.getElementById("cuentas_"+idex.value).getAttribute("value");
    idctabancaria = idex.value;

    var adato = xdato.split("~");

    var txtcuenta    = '('+adato[0]+') '+adato[1]+' '+adato[2];
    if( confirm('gPOS:\n'+
		'       Desea eliminar la cuenta:\n\n'+
		'       '+txtcuenta) ) {
	var url = 'selcuentabancaria.php';
	url = url +'?';
	url = url + 'modo';
	url = url + '=eliminacuenta';
	url = url + '&xid=' + idctabancaria;
	document.location.href = url;
    }
}

function ModificarCuenta() {
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

    if(validarNroCuenta(nrocuenta,'edit'))
	return alert("gPOS:  Registro de cuenta\n\n - la cuenta "+nrocuenta+" está registrado");

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
    VerFormCuenta(true);
    esmodifica = true;
}

function VerFormCuenta(xval){
    if(xval) LimpiarFormCuenta();

    var cuenta = document.getElementById('buscacuenta').value;
    document.getElementById('txtEntFinanciera').value = trim(cuenta);

    var esprov = (xidprov == 0)? true:false;
    document.getElementById('rowProveedor').setAttribute('collapsed',esprov);
    //document.getElementById('rowMoneda').setAttribute('collapsed',esprov);

    document.getElementById('formCuenta').setAttribute('collapsed',xval);
    document.getElementById('ListaCuentas').setAttribute('collapsed',!xval);

    document.getElementById('btnGuardaCuenta').setAttribute('label','Guardar');
    document.getElementById('btnGuardaCuenta').setAttribute('oncommand','GuardarCreaCuenta()');
}

function CancelarCreaCuenta(){
    LimpiarFormCuenta();
    VerFormCuenta(true);
}

function LimpiarFormCuenta(){
    document.getElementById('txtEntFinanciera').value = "";
    document.getElementById('txtNroCuenta').value = "";
    document.getElementById('listIdMoneda').value = 1;
    //document.getElementById('listIdProveedor').value = 1;
    document.getElementById('listEstado').value = "Activo";
    document.getElementById('txtObservaciones').value = "";
    document.getElementById('titleCuentaBancaria').label = 'Nueva Cuenta Bancaria';
}


function GuardarCreaCuenta(){
    var cuenta, url;

    var entfinanciera = trim(document.getElementById('txtEntFinanciera').value);
    var nrocuenta     = trim(document.getElementById('txtNroCuenta').value);
    var idmoneda      = document.getElementById('listIdMoneda').value;
    var idproveedor   = document.getElementById('listIdProveedor').value;
    var estado        = document.getElementById('listEstado').value;
    var observacion   = trim(document.getElementById('txtObservaciones').value);

    if(xidprov == 0)
	idproveedor = 0;

    if (entfinanciera == "" || nrocuenta == "")
        return;

    if(validarNroCuenta(nrocuenta,'new'))
	return alert("gPOS:  Registro de cuenta\n\n - la cuenta "+nrocuenta+" ya está registrado");

    url = 'selcuentabancaria.php';
    url = url +'?';
    url = url + 'modo';
    url = url + '=salvacuenta';
    url = url + '&financiera=' + entfinanciera;
    url = url + '&nrocuenta=' + nrocuenta;
    url = url + '&idmon=' + idmoneda;
    url = url + '&idprov=' + idproveedor;
    url = url + '&estado=' + estado;
    url = url + '&obs=' + observacion;
    url = url + '&xidprov=' + xidprov;
    url = url + '&xcta=' + trim(entfinanciera);
    url = url + '&xnro=' + nro;

    document.location.href = url;

    VerFormCuenta(true);
    parent.RegenCuentasBancarias();
}

function ModificarDatoCuenta(){
    var idex = document.getElementById("listboxCuenta").selectedItem;
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

    document.getElementById('btnGuardaCuenta').setAttribute('label','Modificar');
    document.getElementById('btnGuardaCuenta').setAttribute('oncommand','ModificarCuenta()');
   
    document.getElementById('formCuenta').setAttribute('collapsed',false);
    document.getElementById('ListaCuentas').setAttribute('collapsed',true);
    //VerFormCuenta(false);  
}

function validarNroCuenta(nrocuenta,xval){
    var xcta = false;
    var vcta = document.getElementById('txtNroCuenta').label;

    for(var i in stcta){
	var acta = stcta[i].split("~");
	if(acta[1] == nrocuenta){
	    xcta = (nrocuenta == vcta && xval == 'edit')? false:true;
	}
    }

    return xcta;
}