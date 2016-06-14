var id = function(name) { return document.getElementById(name); }

// Variables Globales
var cIdGuiaRemision  = 0;
var gIdProveedorProv = 0;
var gIdUsuario       = 0;
// Variables Globales

function RegistrarGuia(){
    var serie    = id("SerieGuia").value;
    var numero   = id("NumeroGuia").value;
    var femision = id("FechaEmision").value+" "+id("HoraEmision");
    var motivo   = id("MotivoTraslado").value;
    var concepto = id("ConceptoTraslado").value;
    var ppartida = id("PuntoPartida").value;
    var pllegaga = id("PuntoLlegada").value;
    var marca    = id("MarcaUnidadTransporte").value;
    var placa    = id("PlacaUnidadTransporte").value;
    var licencia = id("LicenciaConductor").value;
    var pesocarga= id("PesoCarga").value;
    var undpeso  = id("UnidadPesoCarga").value;
    var ftraslado= id("FechaTraslado").value+" "+id("HoraTraslado").value;

    var tipoguia = id("txtTipoGuia").value;
    var idsubsidiario = id("IdSubsidiario").value;

    //parent.ImprimirTicket();
    
    switch(tipoguia){
    case 'Remitente':
        gIdComprobante = parent.cIdComprobante;
        gIdUsuario     = parent.Local.IdDependiente;
        break;
    case 'Transportista':
        gIdComprobante = parent.cIdComprobante;
        break;
    case 'Proveedor':
        gIdComprobante = parent.cIdComprobanteProv;
        break;
    }

    var idcnum = obtenerComprobanteNum(gIdComprobante,tipoguia);

    guardarGuia(idcnum,serie,numero,femision,motivo,concepto,ppartida,pllegaga,
                marca,placa,licencia,pesocarga,undpeso,ftraslado,false,tipoguia,
                idsubsidiario);

}

function guardarGuia(idcnum,serie,numero,femision,motivo,concepto,ppartida,
                     pllegaga,marca,placa,licencia,pesocarga,undpeso,ftraslado,
                     idguia,tipoguia,idsubsidiario){
    var data = "";
    var url  = "modguiaremision.php?modo=guardaGuiaRemsion";
    data = data + "&xidcnum=" +idcnum;
    data = data + "&xserie=" + serie;
    data = data + "&xnumero=" + numero;
    data = data + "&xfemision=" + femision;
    data = data + "&xmotivo=" + motivo;
    data = data + "&xconcepto=" + concepto;
    data = data + "&xppartida=" + ppartida;
    data = data + "&xpllegaga=" + pllegaga;
    data = data + "&xmarca=" + marca;
    data = data + "&xplaca=" + placa;
    data = data + "&xlicencia=" + licencia;
    data = data + "&xpesocarga=" + pesocarga;    
    data = data + "&xundpeso=" + undpeso;    
    data = data + "&xftraslado=" + ftraslado;
    data = data + "&xiduser=" + gIdUsuario;
    data = data + "&xidguia=" + cIdGuiaRemision;
    data = data + "&xtipoguia=" + tipoguia;
    data = data + "&xidcbteprov=" + gIdProveedorProv;
    data = data + "&xidsubsidiario=" + idsubsidiario;

    var obj = new XMLHttpRequest();
    obj.open("POST",url,false); 
    obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    
    try{
	obj.send(data);
	res = obj.responseText;
    }catch(e){
	res = false;
    }

    if(!res) return alert(po_servidorocupado+"\n  -"+res);

    parent.id("panelDerecho").setAttribute("collapsed",false);
    parent.id("modoVisual").setAttribute("selectedIndex",0);
    
    cleanFormGuiaRemision();
}

function obtenerComprobanteNum(IdComprobante,tipoguia){
    var xobj = new XMLHttpRequest();
    var url  = "modguiaremision.php?modo=obtenerIdComprobanteNum"+
               "&id="+IdComprobante+
               "&xtipo="+tipoguia;
    
    xobj.open("GET",url,false);
    xobj.send(null);

    var cadena = xobj.responseText;
    return cadena;
}

function modificarGuiaRemision(){
    var serie    = id("SerieGuia").value;
    var numero   = id("NumeroGuia").value;
    var femision = id("FechaEmision").value+" "+id("HoraEmision");
    var motivo   = id("MotivoTraslado").value;
    var concepto = id("ConceptoTraslado").value;
    var ppartida = id("PuntoPartida").value;
    var pllegaga = id("PuntoLlegada").value;
    var marca    = id("MarcaUnidadTransporte").value;
    var placa    = id("PlacaUnidadTransporte").value;
    var licencia = id("LicenciaConductor").value;
    var pesocarga= id("PesoCarga").value;
    var undpeso  = id("UnidadPesoCarga").value;
    var ftraslado= id("FechaTraslado").value+" "+id("HoraTraslado").value;

    var tipoguia = id("txtTipoGuia").value;
    var idsubsidiario = id("IdSubsidiario").value;
    var idcnum   = id("txtIdComprobanteNum").value;

    switch(tipoguia){
    case 'Remitente':
        gIdComprobante = parent.cIdComprobante;
        gIdUsuario     = parent.Local.IdDependiente;
        break;
    case 'Transportista':
        gIdComprobante = parent.cIdComprobante;
        break;
    case 'Proveedor':
        gIdComprobante = parent.cIdComprobanteProv;
        break;
    }

    if(cIdGuiaRemision == 0 || !cIdGuiaRemision)
        return;
        
    guardarGuia(idcnum,serie,numero,femision,motivo,concepto,ppartida,pllegaga,
                marca,placa,licencia,pesocarga,undpeso,ftraslado,cIdGuiaRemision,
                tipoguia,idsubsidiario);
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

function cleanFormGuiaRemision(){
    id("BotonAceptarImpresion").setAttribute('oncommand','RegistrarGuia()');
    id("txtTitleGuia").setAttribute("label",'Nueva Guía de Remisión');
    cIdGuiaRemision = 0;
    
    //id("SerieGuia").value             = '';
    //id("NumeroGuia").value            = '';
    id("FechaEmision").value          = calcularFechaActual('fecha');
    id("HoraEmision").value           = '00:00:00';
    id("MotivoTraslado").value        = '';
    id("ConceptoTraslado").value      = '';
    id("PuntoPartida").value          = '';
    id("PuntoLlegada").value          = '';
    id("MarcaUnidadTransporte").value = '';
    id("PlacaUnidadTransporte").value = '';
    id("LicenciaConductor").value     = '';
    id("PesoCarga").value             = '';
    id("UnidadPesoCarga").value       = 'TN';
    id("FechaTraslado").value         = calcularFechaActual('fecha');
    id("HoraTraslado").value          = '00:00:00';
    id("txtTipoGuia").value           = ''
    id("IdSubsidiario").value         = '0';
    id("EmpresaTextGasto").value      = '';
}

function calcularFechaActual(xvalue){
    var f = new Date();
    var fecha  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
    var hora   = f.getHours() + ":" + f.getMinutes() + ":" + f.getSeconds();
    var actual = (xvalue == 'fecha')? fecha : hora;

    return actual;
}

function editarGuiaRemision(IdGuiaRemision){
    cIdGuiaRemision = IdGuiaRemision;
    id("BotonAceptarImpresion").setAttribute('oncommand','modificarGuiaRemision()');
    id("txtTitleGuia").setAttribute("label",'Modificar Guía de Remisión');

    var tipoguia = id("txtTipoGuia").value;
    
    var xobj = new XMLHttpRequest();
    var url  = "modguiaremision.php?modo=obtenerDataGuiaRemision"+
               "&xid="+IdGuiaRemision+
               "&xtipo="+tipoguia;
    
    xobj.open("GET",url,false);
    xobj.send(null);

    var cadena = xobj.responseText;

    cargarFormGuiaRemision(cadena);
}

function cargarFormGuiaRemision(cadena){
    //var aGuia = new Array();
    var aguia = cadena.split("~~");

    var afemision = aguia[9].split(" ");
    var aftraslado= aguia[10].split(" ");

    id("SerieGuia").value             = aguia[6];
    id("NumeroGuia").value            = aguia[7]; 
    id("FechaEmision").value          = afemision[0];
    id("HoraEmision").value           = afemision[1];
    id("MotivoTraslado").value        = aguia[5];
    id("ConceptoTraslado").value      = aguia[11];
    id("PuntoPartida").value          = aguia[12];
    id("PuntoLlegada").value          = aguia[13];
    id("MarcaUnidadTransporte").value = aguia[14];
    id("PlacaUnidadTransporte").value = aguia[15];
    id("LicenciaConductor").value     = aguia[16];
    id("PesoCarga").value             = aguia[17];
    id("UnidadPesoCarga").value       = aguia[18];
    id("FechaTraslado").value         = aftraslado[0];
    id("HoraTraslado").value          = aftraslado[1];
    id("txtTipoGuia").value           = aguia[8];
    id("IdSubsidiario").value         = aguia[4];
    id("txtIdComprobanteProv").value  = aguia[3];
    id("txtIdComprobanteNum").value   = aguia[0];
    id("EmpresaTextGasto").value      = aguia[19];    
}
