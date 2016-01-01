
var idetallesPedidosVenta  = 0;
var ilineabuscapedidosventa= 0;

var cIdCliente = 0;
var cCodigo    = "";
var cSerie     = "";
var cTipoDoc   = "";
var cExpira    = 0;
var cEstado    = "";
var cVigencia  = 0;
var cObs       = "";
var cTipoVenta = "";
var cProducto  = "";
var cIdProducto  = 0;
var cDetalle   = "";
var cCantidad  = 0;
var cNumSerie  = 0;
var cCodigoB   = "";
var cPrecio    = 0;
var cIdPresDet = 0;
var cMenudeo   = 0;
var cEmpaque   = "";
var cUPC       = 0;
var cUnidadM   = "";
var cIdLocal   = 0;
var cCliente   = "";
var cIdPto     = 0;
var cSerie2Cart    = Array(); 
var cCantSerieCart = 0; 
var cAddSerie2Cart = Array(); 
var cDelSerie2Cart = Array(); 
var cEditSerie = false; 
var RevDet = 0;
var cEsSerie = 0;

// Opciones Busqueda avanzada
var vEstado        = true;
var vTipoVenta     = true;
var vUsuario       = true;
var vFechaRegistro = true;
var vAdelanto      = true;
var vUsuarioRegistro = true;

var id = function(name) { return document.getElementById(name); }

function VerPedidosVenta(){
    VaciarDetallesPedidosVenta();
    VaciarBusquedaPedidosVenta();
    BuscarPedidosVenta();
}

//Limpieza de Box
function VaciarBusquedaPedidosVenta(){
    var lista = id("busquedaPedidosVenta");

    for (var i = 0; i < ilineabuscapedidosventa; i++) { 
        kid = id("lineabuscapedidosventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscapedidosventa = 0;
}
function VaciarDetallesPedidosVenta(){
    var lista = id("busquedaDetallesPedidosVenta");

    for (var i = 0; i < idetallesPedidosVenta; i++) { 
        kid = id("detallepedidosventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesPedidosVenta = 0;
}


//Busqueda 
function BuscarPedidosVenta(){
    VaciarBusquedaPedidosVenta();
    VaciarDetallesPedidosVenta();
    var desde             = id("FechaBuscaPedidosVenta").value;
    var hasta             = id("FechaBuscaPedidosVentaHasta").value;
    var cliente           = id("NombreClienteBusqueda").value;
    var filtropresupuesto = id("FiltroTipoPresupuesto").value;
    var filtrotipoventa   = id("FiltroTipoVenta").value;
    var filtroestado      = id("FiltroPresupuestoEstado").value;

    var filtrolocal       = (id("FiltroPedidosVentaLocal"))?id("FiltroPedidosVentaLocal").value:false;
    var filtrocodigo      = id("busquedaCodigoSerie").value;
    var usuario           = id("IdUsuario").getAttribute("value");
    var producto          = id("NombreProductoBusqueda").value;
    var esproducto        = id("vboxProducto").collapsed;
    producto              = (esproducto)? "":producto;

    RawBuscarPedidosVenta(desde,hasta,cliente,filtropresupuesto,filtrotipoventa,filtroestado,
			  filtrolocal,filtrocodigo,usuario,producto,AddLineaPedidosVenta);

    var elemento = id("busquedaCodigoSerie").value;
    
    //if( elemento != '' ) //buscarPorCodigo(elemento);
    
    volverPedidosVenta();
}

function buscarPorCodigo(elemento){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaPedidosVenta");
    n = lista.itemCount;
    if(n==0) return; 
    busca = busca.toUpperCase();
    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[2].getAttribute('label');
        //cadena = cadena.toUpperCase();
        //if(cadena.indexOf(busca) != -1){
	if( busca == cadena )
	{
            lista.selectItem(texto2);
            RevisarPedidosVentaSeleccionada();
            return;
        }
    }
    alert('gPOS:\n\n    El código " '+elemento+' " no está en la lista.');
    //id("busquedaCodigoSerie").value='';
}
function RawBuscarPedidosVenta(desde,hasta,cliente,filtropresupuesto,filtrotipoventa,
			       filtroestado,filtrolocal,filtrocodigo,usuario,producto,
			       FuncionProcesaLinea){

    var url = "modpedidosventa.php?modo=mostrarPedidosVenta&desde=" + escape(desde) 
        + "&hasta=" + escape(hasta) 
        + "&cliente=" + escape(cliente)
        + "&filtropresto=" + escape(filtropresupuesto)
        + "&filtrotipov=" + escape(filtrotipoventa)
        + "&filtroestado=" + escape(filtroestado)
        + "&filtrolocal=" + escape(filtrolocal)
        + "&usuario=" + usuario
        + "&producto=" + escape(producto)
        + "&filtrocodigo=" + escape(filtrocodigo);
 
    var obj = new XMLHttpRequest();

    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    var item,Codigo,Local,Cliente,FRegistro,FAtencion,TPresupuesto,TipoVenta,Importe,ModoTPV,Vigencia,Estado,Observaciones,Usuario,IdCliente,IdPresupuesto,Importe,CBMProducto,Descuento,Serie,Expira,IdLocal,IdUsuario,Adelanto,IdUsuarioRegistro,UsuarioRegistro;
    var node,t,i,codpeidoventa; 
    var totalPedidosVenta = 0;
    var totalPedidosVentaPendiente = 0;
    var ImporteTotalPedidosVenta = 0;
    var nroPendiente = 0;
    var nroPedido = 0;
    var nroRecibido = 0;
    var nroCancelado = 0;
    var nrototalcompra = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var t_OC = item;
    var t_OCImporte = 0;

    var nroProforma   = 0;
    var nroPreventa   = 0;
    var nroVD         = 0;
    var nroVC         = 0;

    var nroConfirmado = 0;
    var nroVencido    = 0;
    var nroCancelado  = 0;
    var nroPendiente  = 0;
    var nroModificado = 0;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            IdCliente 	    = node.childNodes[t++].firstChild.nodeValue;
            IdPresupuesto   = node.childNodes[t++].firstChild.nodeValue;
            Codigo 	    = node.childNodes[t++].firstChild.nodeValue;
            Cliente 	    = node.childNodes[t++].firstChild.nodeValue;
            TPresupuesto    = node.childNodes[t++].firstChild.nodeValue;
            TipoVenta	    = node.childNodes[t++].firstChild.nodeValue;
	    ModoVenta 	    = node.childNodes[t++].firstChild.nodeValue;
            Importe 	    = node.childNodes[t++].firstChild.nodeValue;
            Estado 	    = node.childNodes[t++].firstChild.nodeValue;
            FRegistro 	    = node.childNodes[t++].firstChild.nodeValue;
            CBMProducto	    = node.childNodes[t++].firstChild.nodeValue;
            FAtencion 	    = node.childNodes[t++].firstChild.nodeValue;
            Vigencia 	    = node.childNodes[t++].firstChild.nodeValue;
            Observaciones   = node.childNodes[t++].firstChild.nodeValue;
	    Descuento       = node.childNodes[t++].firstChild.nodeValue;
            Local           = node.childNodes[t++].firstChild.nodeValue;
            UsuarioRegistro = node.childNodes[t++].firstChild.nodeValue;
	    Serie           = node.childNodes[t++].firstChild.nodeValue;
	    Expira          = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal         = node.childNodes[t++].firstChild.nodeValue;
	    IdUsuario       = node.childNodes[t++].firstChild.nodeValue;
	    Adelanto        = node.childNodes[t++].firstChild.nodeValue;
	    IdUsuarioRegistro = node.childNodes[t++].firstChild.nodeValue;
	    Usuario = node.childNodes[t++].firstChild.nodeValue;

 	    (TPresupuesto == 'Proforma')? nroProforma++:nroPreventa++;
 	    (TipoVenta    == 'VD')? nroVD++:nroVC++;

 	    if (Estado == 'Pendiente')  nroPendiente++; 
	    if (Estado == 'Confirmado') nroConfirmado++; 
	    if (Estado == 'Modificado') nroModificado++; 
	    if (Estado == 'Vencido')    nroVencido++; 
	    if (Estado == 'Cancelado')  nroCancelado++;

            FuncionProcesaLinea(item,Codigo,Local,Cliente,FRegistro,FAtencion,TPresupuesto,
				TipoVenta,ModoVenta,Importe,CBMProducto,Vigencia,Descuento,
				Observaciones,Usuario,IdCliente,IdPresupuesto,Estado,Serie,
				Expira,IdLocal,IdUsuario,Adelanto,IdUsuarioRegistro,
				UsuarioRegistro);
		
	    item--;
        }
    }
    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    id("TotalProforma").value   = nroProforma;
    id("TotalPreventa").value   = nroPreventa;
    id("TotalVD").value         = nroVD;
    id("TotalVC").value         = nroVC;
    id("TotalPendiente").value  = nroPendiente;
    id("TotalConfirmado").value = nroConfirmado;
    id("TotalModificado").value = nroModificado;
    id("TotalVencido").value    = nroVencido;
    id("TotalCancelado").value  = nroCancelado;
}

function AddLineaPedidosVenta(item,Codigo,Local,Cliente,FRegistro,FAtencion,TPresupuesto,
			      TipoVenta,ModoVenta,Importe,CBMProducto,Vigencia,Descuento,
			      Observaciones,Usuario,IdCliente,IdPresupuesto,Estado,Serie,
			      Expira,IdLocal,IdUsuario,Adelanto,IdUsuarioRegistro,
			      UsuarioRegistro){

    var lista = id("busquedaPedidosVenta");
    var xitem,xCodigo,xLocal,xCliente,xFRegistro,xFAtencion,xTPresupuesto,xTipoVenta,xModoVenta,xImporte,xCBMProducto,xVigencia,xObservaciones,xUsuario,xIdCliente,xIdPresupuesto,xDescuento,xSerie,xExpira,xAdelanto,xUsuarioRegistro;

    var Fecha = FAtencion.split('~');
    FAtencion = Fecha[0];

    var lTipoVenta = (TipoVenta=='VD')? 'B2C':'B2B';

    var lAdelanto  = (Adelanto > 0)? formatDinero(Adelanto):"";
    

    Expira = Vigencia - Expira;
    ExpiraEn = (Expira >= 0 && Estado == "Pendiente")? Expira+" días":" ";
    ExpiraEn = (TPresupuesto == 'Preventa' && Estado == 'Pendiente')? " ":ExpiraEn;

    xitem = document.createElement("listitem");
    xitem.value = IdPresupuesto;
    xitem.setAttribute("id","lineabuscapedidosventa_"+ilineabuscapedidosventa);
    ilineabuscapedidosventa++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",Serie+'-'+Codigo);
    xCodigo.setAttribute("value",Codigo);
    xCodigo.setAttribute("style","text-align:center;font-weight:bold;");
    xCodigo.setAttribute("id","codigo_"+IdPresupuesto);

    xSerie = document.createElement("listcell");
    xSerie.setAttribute("value",Serie);
    xSerie.setAttribute("collapsed","true");
    xSerie.setAttribute("id","serie_"+IdPresupuesto);

    xIdPresupuesto = document.createElement("listcell");
    xIdPresupuesto.setAttribute("value",IdPresupuesto);
    xIdPresupuesto.setAttribute("collapsed","true");
    xIdPresupuesto.setAttribute("id","idpresupuesto_"+IdPresupuesto);

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",Local);
    xLocal.setAttribute("value",IdLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("id","local_"+IdPresupuesto);

    xCliente = document.createElement("listcell");
    xCliente.setAttribute("label",Cliente);
    xCliente.setAttribute("value",IdCliente);
    xCliente.setAttribute("style","text-align:center;");
    xCliente.setAttribute("id","cliente_"+IdPresupuesto);

    xFRegistro = document.createElement("listcell");
    xFRegistro.setAttribute("label", FRegistro);
    xFRegistro.setAttribute("collapsed",vFechaRegistro);
    xFRegistro.setAttribute("style","text-align:left");

    xFAtencion = document.createElement("listcell");
    xFAtencion.setAttribute("label", FAtencion);
    xFAtencion.setAttribute("style","text-align:left");

    xTPresupuesto = document.createElement("listcell");
    xTPresupuesto.setAttribute("label", TPresupuesto);
    xTPresupuesto.setAttribute("style","text-align:left");
    xTPresupuesto.setAttribute("id","tpresupuesto_"+IdPresupuesto);

    xTipoVenta = document.createElement("listcell");
    xTipoVenta.setAttribute("label", lTipoVenta);	
    xTipoVenta.setAttribute("value", TipoVenta);	
    xTipoVenta.setAttribute("collapsed", vTipoVenta);	
    xTipoVenta.setAttribute("style","text-align:center;");
    xTipoVenta.setAttribute("id","tipoventa_"+IdPresupuesto);

    xModoVenta = document.createElement("listcell");
    xModoVenta.setAttribute("label", ModoVenta);
    xModoVenta.setAttribute("collapsed","true");	
    xModoVenta.setAttribute("style","text-align:center");

    xCBMProducto = document.createElement("listcell");
    xCBMProducto.setAttribute("label", CBMProducto);	
    xCBMProducto.setAttribute("collapsed","true");
    xCBMProducto.setAttribute("style","text-align:center;font-weight:bold;");

    xExpira = document.createElement("listcell");
    xExpira.setAttribute("label", ExpiraEn);
    xExpira.setAttribute("value", Expira);
    xExpira.setAttribute("id","expira_"+IdPresupuesto);

    xVigencia = document.createElement("listcell");
    xVigencia.setAttribute("value", Vigencia);
    xVigencia.setAttribute("collapsed","true");
    xVigencia.setAttribute("id","vigencia_"+IdPresupuesto);


    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", cMoneda[1]['S']+" "+formatDinero(Importe));
    xImporte.setAttribute("style","text-align:right;font-weight:bold; ");
    xImporte.setAttribute("value",Importe);
    xImporte.setAttribute("id","importe_"+IdPresupuesto);

    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label",cMoneda[1]['S']+" "+formatDinero(Descuento));
    xDescuento.setAttribute("value",Descuento);
    xDescuento.setAttribute("style","text-align:right;");
    xDescuento.setAttribute("id","descuento_"+IdPresupuesto);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label", Estado);
    xEstado.setAttribute("style","text-align:left;font-weight:bold;");
    xEstado.setAttribute("id","estado_"+IdPresupuesto);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("value", IdUsuario);
    xUsuario.setAttribute("collapsed", vUsuario);
    xUsuario.setAttribute("crop", "end");
    xUsuario.setAttribute("style","text-align:center;");
    xUsuario.setAttribute("id","usuario_"+IdPresupuesto);

    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("value",Observaciones );
    xObservaciones.setAttribute("collapsed","true");
    xObservaciones.setAttribute("id","obs_"+IdPresupuesto);

    xAdelanto = document.createElement("listcell");
    xAdelanto.setAttribute("value",Adelanto );
    xAdelanto.setAttribute("label",lAdelanto );
    xDescuento.setAttribute("style","text-align:right;");
    xObservaciones.setAttribute("collapsed",vAdelanto);
    xAdelanto.setAttribute("id","adelanto_"+IdPresupuesto);

    xUsuarioRegistro = document.createElement("listcell");
    xUsuarioRegistro.setAttribute("label", UsuarioRegistro);
    xUsuarioRegistro.setAttribute("value", IdUsuarioRegistro);
    xUsuarioRegistro.setAttribute("collapsed", vUsuarioRegistro);
    xUsuarioRegistro.setAttribute("crop", "end");
    xUsuarioRegistro.setAttribute("style","text-align:center;");
    xUsuarioRegistro.setAttribute("id","usuarioregistro_"+IdPresupuesto);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xTPresupuesto );
    xitem.appendChild( xEstado );
    xitem.appendChild( xTipoVenta );
    xitem.appendChild( xFRegistro );	
    xitem.appendChild( xFAtencion );	
    xitem.appendChild( xExpira );
    xitem.appendChild( xDescuento );	
    xitem.appendChild( xImporte );	
    xitem.appendChild( xAdelanto );
    xitem.appendChild( xCliente );
    xitem.appendChild( xUsuarioRegistro );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xIdPresupuesto);
    xitem.appendChild( xSerie);
    xitem.appendChild( xObservaciones);
    xitem.appendChild( xVigencia );
    xitem.appendChild( xModoVenta );
	
    lista.appendChild( xitem );		
}

function RevisarPedidosVentaSeleccionada(){

    var idex = id("busquedaPedidosVenta").selectedItem;
    if(!idex)return;
    
    cIdCliente = id("cliente_"+idex.value).getAttribute("value");
    cCliente   = id("cliente_"+idex.value).getAttribute("label");
    cCodigo    = id("codigo_"+idex.value).getAttribute("label");
    cSerie     = id("serie_"+idex.value).getAttribute("value");
    cTipoDoc   = id("tpresupuesto_"+idex.value).getAttribute("label");
    cExpira    = id("expira_"+idex.value).getAttribute("value");
    cEstado    = id("estado_"+idex.value).getAttribute("label");
    cVigencia  = id("vigencia_"+idex.value).getAttribute("value");
    cObs       = id("obs_"+idex.value).getAttribute("value");
    cTipoVenta = id("tipoventa_"+idex.value).getAttribute("value");
    cIdLocal   = id("local_"+idex.value).getAttribute("value");
    cIdUsuario = id("usuario_"+idex.value).getAttribute("value");
    cIdPresupuesto = id("idpresupuesto_"+idex.value).getAttribute("value");

    var idpresupto = id("idpresupuesto_"+idex.value).getAttribute("value");

    var verdet = (RevDet == 0 || RevDet != idex.value)? true:false;
    if(verdet || idetallesPedidosVenta == 0)
        setTimeout("loadDetallesPedidosVenta('"+idpresupto+"')",100);

    RevDet = idex.value;
    xmenuPedidosVenta();
}

function loadDetallesPedidosVenta(xid){
    VaciarDetallesPedidosVenta();
    BuscarDetallesPedidosVenta(xid);
} 

function BuscarDetallesPedidosVenta(IdPresupuesto ){

    RawBuscarDetallesPedidosVenta(IdPresupuesto, AddLineaDetallesPedidosVenta);

}

function RawBuscarDetallesPedidosVenta(IdPresupuesto,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();

    var url = "modpedidosventa.php?modo=mostrarDetallePedidosVenta"+
	      "&xidp=" + escape(IdPresupuesto);

    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia, Producto, CodigoBarras, Unidades, Descuento, Importe, Precio, Contenedor, VentaMenudeo, UPC, UnidadMedida, IdProducto, Concepto, IdPresupuestoDet;
    var node,t,i;
    var numitem = 0;
    if (!obj.responseXML) return alert(po_servidorocupado);		

    var xml = obj.responseXML.documentElement;
    //alert(xml.childNodes.length)
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node && node.childNodes && node.childNodes.length >0){
            t = 0;
	    numitem++;
            if (node.childNodes[t].firstChild){
                IdProducto       = node.childNodes[t++].firstChild.nodeValue;
		IdPresupuestoDet = node.childNodes[t++].firstChild.nodeValue;
                Referencia       = node.childNodes[t++].firstChild.nodeValue;
                CodigoBarras     = node.childNodes[t++].firstChild.nodeValue;
                Nombre           = node.childNodes[t++].firstChild.nodeValue;
		Marca            = node.childNodes[t++].firstChild.nodeValue;
		Color            = node.childNodes[t++].firstChild.nodeValue;
		Talla            = node.childNodes[t++].firstChild.nodeValue;
		Lab              = node.childNodes[t++].firstChild.nodeValue;
		Cont             = node.childNodes[t++].firstChild.nodeValue;
		Menudeo          = node.childNodes[t++].firstChild.nodeValue;
		UnidxCont        = node.childNodes[t++].firstChild.nodeValue;
		Unid             = node.childNodes[t++].firstChild.nodeValue;
		Concepto         = node.childNodes[t++].firstChild.nodeValue;
                Unidades         = node.childNodes[t++].firstChild.nodeValue;
                Precio           = node.childNodes[t++].firstChild.nodeValue;
		Descuento        = node.childNodes[t++].firstChild.nodeValue;
		Importe          = node.childNodes[t++].firstChild.nodeValue;
		Serie            = node.childNodes[t++].firstChild.nodeValue;
		

                FuncionRecogerDetalles(numitem,Referencia,IdProducto,CodigoBarras,
				       Nombre,Unidades,Precio,Concepto,Cont,Unid,
				       Descuento,Importe,Menudeo,UnidxCont,IdPresupuestoDet,
				       Marca,Color,Talla,Lab,Serie);
            }
        }
    }
}

function AddLineaDetallesPedidosVenta(numitem,Referencia,IdProducto,CodigoBarras,
				      Nombre,Unidades,Precio,Concepto,Cont,Unid,
				      Descuento,Importe,Menudeo,UnidxCont,IdPresupuestoDet,
				      Marca,Color,Talla,Lab,Serie){

    var lista = id("busquedaDetallesPedidosVenta");
    var xitem,xnumitem,xReferencia,xIdProducto,xCodigoBarras,xProducto,xUnidades,xPrecio,xImporte,xDetalle,xDescuento,xIdPresupuestoDet,xMenudeo,xEmpaque,xUnid;
    var cResto    = '';
    var tCantidad = '';
    var tUnidad   = '';
    var Detalle   = '';
    var vNombre   = Nombre+" "+Marca;
    vNombre       = (Color != ' ')? vNombre+" "+Color:vNombre;
    vNombre       = (Talla != ' ')? vNombre+" "+Talla:vNombre;
    vNombre       = vNombre+" "+Lab;
    var Producto  = (Concepto != ' ')? Concepto:vNombre;

    //Cantidad
    cResto    = (Unidades < UnidxCont)? Unidades:false;
    cResto    = (Unidades >= UnidxCont)? Unidades%UnidxCont:cResto;
    tCantidad = ( Menudeo=='1' )? (Unidades-cResto)/UnidxCont:false;
    tCantidad = ( Menudeo=='1' )? tCantidad+' '+Cont+'  '+cResto:Unidades;
    tUnidad   = Unid;
    tCantidad = tCantidad+' '+tUnidad;
    Detalle   = ( Menudeo=='1' )? UnidxCont+''+tUnidad+'/'+Cont:'';
    Detalle   = ( Serie!='0' )? getListaNSReserva(Serie,IdPresupuestoDet,Unidades):Detalle;
    
    xitem       = document.createElement("listitem");
    xitem.value = IdPresupuestoDet;
    xitem.setAttribute("id","detallepedidosventa_" + idetallesPedidosVenta);
    idetallesPedidosVenta++;

    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label", Detalle);
    xDetalle.setAttribute("value", Serie);
    xDetalle.setAttribute("id","detalle_"+IdPresupuestoDet);

    xIdPresupuestoDet = document.createElement("listcell");
    xIdPresupuestoDet.setAttribute("value", IdPresupuestoDet);
    xIdPresupuestoDet.setAttribute("collapsed","true");
    xIdPresupuestoDet.setAttribute("id","idpresupuestodet_"+IdPresupuestoDet);

    xReferencia = document.createElement("listcell");
    xReferencia.setAttribute("label", Referencia);

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label", Producto);
    xProducto.setAttribute("value", IdProducto);
    xProducto.setAttribute("id","producto_"+IdPresupuestoDet);

    xCodigoBarras = document.createElement("listcell");
    xCodigoBarras.setAttribute("label", CodigoBarras);
    xCodigoBarras.setAttribute("id","codigobarras_"+IdPresupuestoDet);

    xUnidades = document.createElement("listcell");
    xUnidades.setAttribute("label", tCantidad);
    xUnidades.setAttribute("value", Unidades);
    xUnidades.setAttribute("id","cantidad_"+IdPresupuestoDet);
    xUnidades.setAttribute("style","text-align:right");

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label", formatDinero(Precio));
    xPrecio.setAttribute("value", Precio);
    xPrecio.setAttribute("style","text-align:right");
    xPrecio.setAttribute("id","precio_"+IdPresupuestoDet);

    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label", formatDinero(Descuento));
    xDescuento.setAttribute("style","text-align:right");
    xDescuento.setAttribute("id","descuento_"+IdPresupuestoDet);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label", formatDinero(Importe));
    xImporte.setAttribute("style","text-align:right;font-weight:bold;");

    xMenudeo = document.createElement("listcell");
    xMenudeo.setAttribute("value",Menudeo);
    xMenudeo.setAttribute("collapsed","true");
    xMenudeo.setAttribute("id","menudeo_"+IdPresupuestoDet);

    xEmpaque = document.createElement("listcell");
    xEmpaque.setAttribute("label",Cont);
    xEmpaque.setAttribute("value",UnidxCont);
    xEmpaque.setAttribute("collapsed","true");
    xEmpaque.setAttribute("id","empaque_"+IdPresupuestoDet);

    xUnid = document.createElement("listcell");
    xUnid.setAttribute("label",Unid);
    xUnid.setAttribute("collapsed","true");
    xUnid.setAttribute("id","unidadmedida_"+IdPresupuestoDet);


    xitem.appendChild( xnumitem );
    xitem.appendChild( xReferencia );
    xitem.appendChild( xCodigoBarras );
    xitem.appendChild( xProducto );
    xitem.appendChild( xDetalle );
    xitem.appendChild( xUnidades );
    xitem.appendChild( xPrecio );	
    xitem.appendChild( xDescuento );	
    xitem.appendChild( xImporte );	
    xitem.appendChild( xMenudeo );
    xitem.appendChild( xEmpaque );
    xitem.appendChild( xIdPresupuestoDet);
    xitem.appendChild( xUnid);

    lista.appendChild( xitem );
}

function getListaNSReserva(xSerie,xid,xcantidad){

    if(xSerie=='1') return '';
    if(xSerie=='-NS') return 'NS:...';

    var arSerie  = Array(); 
    var arpSerie = Array(); 
    var lSerie   = '';
    var xsrt     = '';
    var serieCart = Array(); 

    arSerie = xSerie.split("~");

    for( var xns=0; xns < arSerie.length; xns++)
    {
	arpSerie = arSerie[xns].split(":");
	lSerie  += xsrt+arpSerie[1];
	xsrt     = ',';
	//serieCart[ trim(arpSerie[1]) ] = parseInt( arpSerie[0] );
	serieCart.push( arpSerie[0]+':'+arpSerie[1] );
    }

    if( serieCart.length != xcantidad ) return 'NS: ';    
    cSerie2Cart[ xid ]    = serieCart; 
    cDelSerie2Cart[ xid ] = Array(); 
    cAddSerie2Cart[ xid ] = Array(); 
    lSerie = ( lSerie.length > 30 )? lSerie.slice(0,30)+ '...':lSerie;
    return ( lSerie != '')? 'NS: '+lSerie : '';
}

    function RevisarDetallePedidosVenta(){
	//VaciarDetallesPedidosVenta();
	var idex = id("busquedaDetallesPedidosVenta").selectedItem;
	if(!idex)return;
	
	cProducto  = id("producto_"+idex.value).getAttribute("label");
	cIdProducto= id("producto_"+idex.value).getAttribute("value");
	cDetalle   = id("detalle_"+idex.value).getAttribute("label");
	cCantidad  = id("cantidad_"+idex.value).getAttribute("value");
	cNumSerie  = id("detalle_"+idex.value).getAttribute("value");
	cCodigoB   = id("codigobarras_"+idex.value).getAttribute("label");
	cPrecio    = id("precio_"+idex.value).getAttribute("value");
	cIdPresupuestoDet  = id("idpresupuestodet_"+idex.value).getAttribute("value");
	cMenudeo   = id("menudeo_"+idex.value).getAttribute("value");
	cUPC       = id("empaque_"+idex.value).getAttribute("value");
	cEmpaque   = id("empaque_"+idex.value).getAttribute("label");
	cUnidadM   = id("unidadmedida_"+idex.value).getAttribute("label");
	cEsSerie   = id("detalle_"+idex.value).getAttribute("value");

	xmenuPedidosVenta();
	xmenuPedidosVentaDetNS();
    }


    function ImprimirPedidosVenta(){
	var idex = id("busquedaPedidosVenta").selectedItem;

	if(!idex)return;

	var idoc = idex.value;

	o_PrintPedidosVenta(idoc);
    }

    function o_PrintPedidosVenta(idoc){
	var importe = id("importe_"+idoc).getAttribute("value");
	var nroDocumento = cCodigo;
	var nroSerie     = cSerie;
	var codcliente   = cIdCliente;
	var idcomprobante = idoc;
	var importeletras = convertirNumLetras(importe,1);
	importeletras     = importeletras.toUpperCase();
	var url=
	    "../fpdf/imprimir_Proforma_tpv.php?"+
	    "nroProforma="+nroDocumento+"&"+
	    "nroSerie="+nroSerie+"&"+
	    "totaletras="+importeletras+"&"+
	    "codcliente="+codcliente+"&"+
	    "idlocal="+cIdLocal+"&"+
	    "idcomprobante="+idcomprobante;

	location.href=url;
    }

    function ModificarPedidosVenta(xocs,xdet){
	var lbox     = (xdet)?"busquedaDetallesPedidosVenta":"busquedaPedidosVenta";
	var idex     = id(lbox).selectedItem; 
	if(!idex) return;
	
	var idetx    = (xdet)? cIdPresupuestoDet:false;
	var idx      = (xdet)? cIdPresupuesto:idex.value;
	var codigo   = cCodigo;
	var estado   = cEstado;
	var tipodoc  = cTipoDoc;
	var msj      = false;
	var xrest    = false;
	var reload   = true;
	var reloadet = true;
	var reloadod = false;
	var xdato    = 0;

	var amsj     = 'gPOS: Acción restingida!\n\n '+
	    '-  Al modificar la '+tipodoc+' Nro.'; 

	switch (xocs) {
	case 1:
	    //Preventa a Proforma
	    xrest = (estado != 'Pendiente')?true:false;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente-');
	    
	    xdato = cIdLocal+'~'+cIdUsuario+'~'+cObs;

	    msj=' Modificar '+tipodoc+' Nro.'+codigo+'\n\n '+
		' - Nuevo Documento -Proforma-';
	    reloadet = false;
	    volverPedidosVenta();
	    break;

	case 2:
 	    //Local
	    xrest = (estado=='Pendiente' || estado=='Vencido')?true:false;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado diferente a -'+estado+'-');
	    //Carga mesaje 
	    var local = id("xPedidosVentaLocal").value;
	    xdato     = local;

	    msj=' Cancelar '+tipodoc+' Nro.'+codigo+' -'+estado+
		'-\n\n - Nuevo Local -'+local+'-';
	    reloadet = false;
	    volverPedidosVenta();
	    break;
	case 3:
	    //Modificar Cliente
	    xrest = (estado=='Pendiente' || estado=='Vencido')? false:true;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente o Vencido-');

	    //Lista Proveedores
 	    closepopup();

	    if(!ClientePost) return;
	    if(cCliente == ClientePost) return;

	    id("cliente_"+idx).setAttribute('label',ClientePost);
	    id("ClientHab").value = ClientePost;
	    id("IdClientHab").setAttribute("value",IdClientePost);// = IdClientePost;

	    xdato = id("IdClientHab").value;
	    //Carga mesaje 
	    msj = tipodoc+' Nro.'+codigo+' -'+estado+
		'-\n\n - Nuevo Cliente  -'+ClientePost+'-';

	    //Clean
	    ProveedorPost   = false;
	    IdProveedorPost = 0;

	    reloadet = false;
	    volverPedidosVenta();
	    break;

	case 4:
	    // Vigencia

	    xrest = (estado=='Pendiente' || estado=='Vencido')?false:true;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente o Vencido-');
	    //Carga Costo
	    var xnuevo = id("xVigencia").value;
	    xdato      = parseInt(cVigencia) + parseInt(xnuevo) - parseInt(cExpira);

	    msj=' Modificar '+cTipoDoc+' Nro.'+codigo+' -'+estado+'-\n\n '+
		' Vence en '+xnuevo+' días';

	    reloadet = false;
	    volverPedidosVenta();
	    break;

	case 5:
	    // Observaciones
	    xrest = (estado=='Pendiente')? false:true;//Control
	    if(xrest)
		return alert(amsj+codigo+' -'+estado+
			     '-,   debe tener estado -Pendiente-');
 	    //Carga dato
	    xdato = trim(id("xObservacion").value);

	    //Observaciones
	    if( xdato == '' || xdato == cObs) return;
	    //reload   = true;
	    reloadet = false;
	    reloadod = true;
	    //volverOrdenCompras();

	    //Carga mesaje 
	    msj='\n Agregar la observación: \n\n - '+xdato+
		'\n\n en el Pedido Nro.'+codigo+' -'+estado+'- ';

	    reloadet = false;
	    volverPedidosVenta();
	    break;

	case 6:
	    // Cantidad
	    if(cEsSerie != 0) return;
	    xrest = (estado=='Pendiente')?false:true;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente-');
	    var xUnidad     = cUnidadM;
	    var xMenudeo    = cMenudeo;
	    var xContenedor = cEmpaque;
	    var xCantidad   = cCantidad;
	    var xUndCont    = cUPC;
	    var producto    = id("xProducto").value
	    var esMenudeo   = (xMenudeo=='1')? true:false;
	    var xEmpaques   = (esMenudeo)? parseFloat(id("xEmpaques").value):0;
	    var xMenudencia = (esMenudeo)? parseFloat(id("xMenudencia").value):0;
	    var xMenudencia = ( esMenudeo && xUndCont > xMenudencia )? xMenudencia:0;
	    //Carga Cantidad
	    var mdato       = (esMenudeo)? parseFloat(xEmpaques*xUndCont)+parseFloat(xMenudencia):0;
	    xdato           = (esMenudeo)? mdato:id("xCantidad").value;
	    var vdato       = (esMenudeo)? xEmpaques+' '+xContenedor+' '+xMenudencia:xdato;

	    //Termina Brutall
	    if( xdato == xCantidad ) return;

	    xdato = xdato+","+cPrecio;

	    //Carga mesaje 
	    reloadod = true;

	    msj      = ' Modificar '+cTipoDoc+' Nro.'+codigo+' -'+estado+'-\n\n '+
	        ' Producto: '+producto+' \n\n   Nueva Cantidad:  '+vdato+' '+xUnidad;

	    volverPedidosVenta();
	    break;

	case 7:
	    // Precio
	    xrest = (estado=='Pendiente')?false:true;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente-');
	    //Carga Costo
	    xdato    = id("xPrecio").value;
	    var producto = id("xProducto").value
	    if( xdato < 0 || xdato == cPrecio) return;

	    //Carga mesaje 
	    //reloadod = true;
	    //volverOrdenCompras();
	    xdato  = cCantidad+","+xdato;

	    reloadod = true;
	    msj=' Modificar '+cTipoDoc+' Nro.'+codigo+' -'+estado+'-\n\n '+
		' Producto: '+producto+'.\n\n Nuevo Precio: '+cMoneda[1]['S']+" "+xdato;

	    volverPedidosVenta();
	    break;

	case 8:
	    // Quitar Producto
	    xrest = (estado=='Pendiente')?false:true;

	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado -Pendiente-');

	    xdato = cIdPresupuestoDet;
	    //Carga mesaje 
	    msj=' Quitar Producto de la '+cTipoDoc+' Nro.'+codigo+
		' -'+estado+'-';

	    reloadod = true;
	    volverPedidosVenta();
	    break;

	case 9:
	    // Cambio de estado
	    xrest = (estado=='Confirmado' || cEstado == "Modificado")? true:false;
	    if(xrest) return alert(amsj+codigo+' -'+estado+'-,'+
				   ' debe tener estado diferente a -'+cEstado+'-');
	    var nEstado = id("xEstadoVenta").value;
	    var resto   = cExpira;
	    resto = (resto < 0)? (cVigencia-cExpira+1):resto;

	    if(nEstado == cEstado) return;
	    
	    xdato = nEstado;

	    msj=' Modificar '+cTipoDoc+' Nro.'+codigo+' -'+estado+'-\n\n '+
		' \n\n Nuevo Estado: -'+xdato+'-';

	    reloadet = false;
	    volverPedidosVenta();
	    
	}

	//Control

	if(!confirm('gPOS: '+msj+','+
		    ' ¿desea continuar?')) return;
	//Ejecuta
	var url="modpedidosventa.php?modo=ModificarPedidosVenta"+
            "&xid="+idx+
            "&xocs="+xocs+
	    "&xidet="+idetx+
            "&xdato="+xdato+
	    "&resto="+resto+
	    "&tv="+cTipoVenta;

	var xres = new XMLHttpRequest();
	xres.open("GET",url,false);

	xres.send(null);
	//alert(xres.responseText);
	if(isNaN(xres.responseText))
	    alert(po_servidorocupado);

	//Termina PedidosVenta

	if(reload)   BuscarPedidosVenta();
	if(reloadet) VaciarDetallesPedidosVenta();
	if(reloadod) BuscarDetallesPedidosVenta(idx);
    }

    function VerObservPedidosVenta(){
	var idex   = id("busquedaPedidosVenta").selectedItem; 
	
	if(!idex) return;

	var idx    = idex.value;//IdPedidosVentaDet:false 
	var codigo = id("codigo_"+idx).getAttribute("label");
	var estado = id("estado_"+idx).getAttribute("label");
	var xobs   = trim(id("obs_"+idx).getAttribute("value"));
	var tipodoc= cTipoDoc;
	//Items?
	var xrest  = (xobs != '')?false:true;
	var aobs;

	//Sin Item
	if(xrest) xobs = '\n\n                    - Sin observaciones - ';

	/*//Item 
	if(!xrest)
	{
	    aobs = xobs.split('-');
	    xobs = '';
	    for(x in aobs){ if(x != 0 && aobs[x]!='' ) xobs += '\n        -'+aobs[x];}
	}
*/
	//Termina
	aobs = xobs;
	return alert('gPOS:  Observaciones '+tipodoc+' Nro.'+codigo+' -'+estado+'\n\n'+
		     '     '+xobs+' \n ');
    }

    function xmenuPedidosVenta(){
	var imprimir = (cTipoDoc == "Proforma")?false:true;
	var modifica = true;
	var vence    = true;
	var quita    = true;
	var modidet  = true;

	switch(cEstado){
	case "Pendiente":
	    modifica = false;
	    vence    = (cExpira >= 0)? true:false;
	    quita    = false;
	    modidet  = false;
	    break;
	case "Modificado":
	    vence    = (cExpira >= 0)? true:false;	
	    break;
	case "Confirmado":
	    modifica = true;
	    break;
	case "Vencido":
	    modifica = false;
	    break;
	case "Cancelado":
	    modifica = false;
	    break;

	}

	id("mheadImprimir").setAttribute("disabled",imprimir);
	id("mheadModifica").setAttribute("disabled",modifica);
	id("mheadQuita").setAttribute("disabled",quita);
	id("mheadModificaDetalle").setAttribute("disabled",modidet);
	id("mheadModificaDetalleNS").setAttribute("disabled",modidet);
    }

    function xmenuPedidosVentaDetNS(){
	var xmodifica = true;
	switch(cEstado){
	case "Pendiente":
	    xmodifica = ( cNumSerie != '0' )? false:true;
	    break;
	case "Modificado":
	case "Confirmado":
	case "Vencido":
	case "Cancelado":
	    xmodifica = true;
	    break;
	}
	id("mheadModificaDetalleNS").setAttribute("disabled",xmodifica);
    }

    function ModificarPedidos(){
	var idex = id("busquedaPedidosVenta").selectedItem;
	if(!idex)return;

	var docval = (cTipoDoc == "Preventa" && cEstado == "Pendiente")? false:true;
	var lval   = (docval)? false:true;
	var xval   = (!docval)? false:true;
	var stval  = (cEstado != "Confirmado" || cEstado != "Modificado" )? false:true;
	var sval   = (stval)? false:true;
	var zval   = (!stval)? false:true;
	var expira = cExpira;
	expira     = (expira < 0)? 0:expira;
	var oval   = (cEstado == "Vencido" || cEstado == "Cancelado")? true:false;
	var xoval  = (!oval)? true:false;

	if(cEstado == 'Confirmado') return false; 
	if(cEstado == 'Modificado') return false;

	id("xEstadoVenta").value       = cEstado;
	id("xPedidosVenta").value      = cTipoDoc;
	id("xCodigo").value            = cCodigo;
	id("ClientHab").value          = cCliente;
	id("xVigencia").value          = expira;
	id("xObservacion").value       = trim(cObs);
	id("xEstadoVenta").setAttribute("value",cEstado);
	id("xPedidosVenta").setAttribute("value",cTipoDoc);
	id("IdClientHab").setAttribute("value",cIdCliente);

	id("ClientHab").setAttribute("disabled",oval);
	id("xVigencia").setAttribute("disabled",oval);
	id("xObservacion").setAttribute("disabled",oval);
	(xoval)? id("ClientHab").removeAttribute("disabled"):false;
	(xoval)? id("xVigencia").removeAttribute("disabled"):false;
	(xoval)? id("xObservacion").removeAttribute("disabled"):false;

	id("listboxPedidosVenta").setAttribute("collapsed",true);
	id("formularioPedidosVenta").setAttribute("collapsed",false);
	id("formularioDetallePedidosVenta").setAttribute("collapsed",true);
	id("lineaPedido1").setAttribute("collapsed",lval);
	id("lineaPedido2").setAttribute("collapsed",xval);
	id("lineaEstado2").setAttribute("collapsed",zval);

	habilitarEstadoPedidosVenta();

    }

    function ModificarDetallePedidos(){
	if(cEstado != 'Pendiente') return;
	
	var esMenudeo      = (cMenudeo=='1')? false:true;
	var noMenudeo      = (esMenudeo)? false:true;
	var xResto         = (cCantidad < cUPC)? cCantidad:false;
	xResto             = (cCantidad >= cUPC)? cCantidad%cUPC:xResto;
	var xEmpaques      = (cMenudeo=='1')? (cCantidad-xResto)/cUPC:false;

	id("xProducto").value        = cCodigoB+' '+cProducto;
	id("xDetalle").value         = cDetalle;
	id("xCantidad").value        = cCantidad;
	id("xEmpaques").value        = xEmpaques;
	id("xContenedor").value      = cEmpaque;
	id("xMenudencia").value      = xResto;
	id("xPrecio").value          = cPrecio;
	id("xPedidosVentadet").value = cTipoDoc+' - '+cCliente;
	id("xmUnidades").value       = cUnidadM.toUpperCase();

	id("listboxPedidosVenta").setAttribute("collapsed",true);
	id("formularioPedidosVenta").setAttribute("collapsed",true);
	id("formularioDetallePedidosVenta").setAttribute("collapsed",false);

	id("esMenudeo").setAttribute("collapsed",esMenudeo);
	id("noMenudeo").setAttribute("collapsed",noMenudeo);

	id("xCantidad").removeAttribute('disabled');
	if(cEsSerie != 0) id("xCantidad").setAttribute('disabled',true);
    }

    function volverPedidosVenta(){

	if(cEditSerie) return salvarReservaNS2Presupuesto();//Salvar series

	id("listboxPedidosVenta").setAttribute("collapsed",false);
	id("formularioPedidosVenta").setAttribute("collapsed",true);
	id("formularioDetallePedidosVenta").setAttribute("collapsed",true);
	id("editandoSeries").setAttribute("collapsed",true);
    }

    function habilitarEstadoPedidosVenta(){
	var estadopte = (cEstado != "Pendiente")? false:true;
	var estadocdo = (cEstado == "Pendiente")? false:true;
	var estadovdo = true;

	id("mheadEstadoPte").setAttribute("collapsed",estadopte);
	id("mheadEstadoCdo").setAttribute("collapsed",estadocdo);
	id("mheadEstadoVdo").setAttribute("collapsed",estadovdo);
	id("editandoSeries").setAttribute("collapsed",estadovdo);
    }

    function mostrarBusquedaAvanzada(xthis){

	var xchecked = (xthis.getAttribute('checked'))? false:true;
	var xlabel   = xthis.label.replace(" ","_");

	switch(xlabel){
	case "Estado": 
	    vEstado        = xchecked;
	    break;
	case "Tipo_Venta":
	    vTipoVenta   = xchecked;
	    break;
	case "Usuario":
	    vUsuario       = xchecked;
	    break;
	case "Fecha_Registro":
	    vFechaRegistro = xchecked;
	    break;
	case "Producto":
	    id("xboxCliente").setAttribute("collapsed",!xchecked);
	    //if(!xchecked) id("NombreProductoBusqueda").value = "";
	    vProducto       = xchecked;
	    break;
	case "Adelanto":
	    vAdelanto       = xchecked;
	    break;
	case "Usuario_Registro":
	    vUsuarioRegistro = xchecked;
	    break;
	}

	if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
	if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
	if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
	BuscarPedidosVenta();
    }

    function ModificarDetallePedidosNS(){
	if(cEstado != 'Pendiente') return;
	limpiarlistaserie();

	id("xProductoNS").value        = 'Producto : '+cCodigoB+' '+cProducto;
	id("xCantidadNS").value        = 'Cantidad : '+cCantidad+' '+cUnidadM;

	id("listboxPedidosVenta").setAttribute("collapsed",true);
	id("formularioPedidosVenta").setAttribute("collapsed",true);
	id("formularioDetallePedidosVenta").setAttribute("collapsed",true);
	id("editandoSeries").setAttribute("collapsed",false);
	cEditSerie = true;
	setTimeout("listarseries()",100);
    }

    function listarseries(){

	var xreq = new XMLHttpRequest();
	var url  = "modpedidosventa.php?modo=obtenerNSReservadasPresupuesto"+
	    "&xidproducto=" + escape(cIdProducto)+
	    "&xidlocal=" + escape(cIdLocal);
	xreq.open("GET",url,false);
	xreq.send(null);
	var xres  = xreq.responseText;

	var axres = xres.split(';;');
	if( axres[0] )
	    return alert(po_servidorocupado+'\n '+axres[0]);

	var listaseries = axres[1];
	var xseries = listaseries.split("~");
	var theList = document.getElementById('listaseries_presupuestos');
	var esCart  = false;
	var aseries = Array();
	var pseries = Array();
	var idpedidodet = 0;
	var xnsstock    = 0;
	var xnscart     = 0;
	for(var j=0; j<xseries.length; j++)
	{
	    //IdPedidoDet:Serie,Serie,Serie
	    //pseries[1] Serie,Serie
	    //pseries[0] IdPedidoDet
	    pseries = xseries[j].split(":");
	    aseries = pseries[1].split(",");
	    
	    for(var i=0; i<aseries.length; i++)
	    {
		var row = document.createElement('listitem');

		esCart = ckSerie2Cart(trim(aseries[i]),cIdPresupuestoDet,pseries[0]);
		
		row.setAttribute('type','checkbox');
		row.setAttribute('label',aseries[i]);
		row.setAttribute('value',pseries[0]);
		row.setAttribute('oncommand',"cargarSerie2Carrito('"+aseries[i]+"',this,false)");
		row.setAttribute('checked',esCart);
		theList.appendChild(row);		    
		xnsstock++;
		if(esCart) xnscart++;

	    }
	}	    
	//id("totalSelNS").setAttribute('label',xnscart);
	id("totalNS").setAttribute("label",xnsstock);

    }

    function ckSerie2Cart(xSerie,xid,xiddet){
	
	if( !cSerie2Cart[ xid ] ) return false;
	var arSeries = cSerie2Cart[ xid ];
	var zSerie   = Array();

	for(var xns=0; xns<arSeries.length; xns++){

	    zSerie = arSeries[xns].split(':');
	    if( zSerie[1]== xSerie){
		ckCantSeriesCart(1,false,false);
		return true;
	    }
	}
	return false;
    }


    function limpiarlistaserie(){
	var lista  = id('listaseries_presupuestos');
	var i        = lista.itemCount-1;
        for(i;i>=0;i--)
        {
            lista.removeItemAt(i);
	}
    }

    function selcKBoxSerie(){
	
	var radio_group = id("radio_group");

	switch(radio_group.selectedItem.label)
	{
	case "Buscar": 

            var lista  = id("listaseries_presupuestos");
            var ns     = id("ckserie");
            var nserie = trim(ns.value);

            if(trim(ns.value)=="") return;

            limpiar_cajackbox();

            for(var i=0;i<lista.itemCount;i++ ){
		
		var xlist  = lista.getItemAtIndex(i);
		var xserie = xlist.getAttribute('label');

		if(xserie == nserie)
		{
                    lista.ensureElementIsVisible(xlist);
                    lista.selectItem(xlist);
		    limpiar_cajackbox();
                    return cargarSerie2Carrito(xserie,xlist,true);
		}	    
            }
            alert( "gPOS: \n\n No se encuentra NS: "+nserie);
            break;
	}
    }

    function cargarSerie2Carrito(xserie,xthisck,xdonde){

	var vckValue   = xthisck.getAttribute('checked');
	var xpedidodet = xthisck.value;

	//Busqueda
	if(xdonde)
	{
	    vckValue = ( vckValue == 'true' )? false:true;
	    xthisck.setAttribute('checked', vckValue);
	}

	if (ckCantSeriesCart(2,xthisck,vckValue)) return;

	//Add Carrito Serie
	if(vckValue) AddCarritoSerie(xpedidodet,xserie);
	
	//Del Carrito Serie
	if(!vckValue) DelCarritoSerie(xpedidodet,xserie);
    }

    function AddCarritoSerie(xpedidodet,xserie){
	var arSeries = cAddSerie2Cart[ cIdPresupuestoDet ];
	var zSerie   = Array();

	for (var k in arSeries)
	{
	    zSerie = arSeries[k].split(':');

	    if(  zSerie[1] == xserie ) return;
 	}
	cAddSerie2Cart[ cIdPresupuestoDet ].push(xpedidodet+':'+xserie);//add
	AddDelCarritoSerie( xpedidodet,xserie );//Repetidos
	ckCantSeriesCart(1,false,false);//Cantidad
    }

    function DelCarritoSerie(xpedidodet,xserie){

	var arSeries = cDelSerie2Cart[ cIdPresupuestoDet ];
	var zSerie   = Array();

	for (var k in arSeries)
	{
	    zSerie = arSeries[k].split(':');

	    if( zSerie[1] == xserie ) return;
 	}
	cDelSerie2Cart[ cIdPresupuestoDet ].push(xpedidodet+':'+xserie);//add
	AddDelCarritoSerie( xpedidodet,xserie );//Repetidos
	ckCantSeriesCart(0,false,false);//Cantidad
    }

    function ckCantSeriesCart(xload,xthisck,vckValue){

	switch(xload){
	case 0:    
	    cCantSerieCart--;
	    break;
	case 1:    
	    cCantSerieCart++;
	    break;
	case 2:    
	    if( cCantidad > cCantSerieCart ) 
		return false;
	    if( cCantidad == cCantSerieCart && vckValue )
	    {
		xthisck.setAttribute('checked', false);
		alert('gPOS: Números Serie\n\n    Cantidad : '+cCantidad+' '+cUnidadM+'\n    Series     : '+cCantSerieCart+' Series seleccionadas \n\n      - La lista esta completa -');
		return true;
	    }
	    break;
	    }

	//cCantidad 
	id("totalSelNS").setAttribute('label',cCantSerieCart);
    }

    function AddDelCarritoSerie( xpedidodet,xserie ){
	var delarSeries = cDelSerie2Cart[ cIdPresupuestoDet ];
	var addarSeries = cAddSerie2Cart[ cIdPresupuestoDet ];
	var zSerie   = Array();
	var xdel     = false;
	var xadd     = false;
	
	//Del
	for (var k in delarSeries)
	{
	    zSerie = delarSeries[k].split(':');
	    if( zSerie[1] == xserie ) xdel = true;
 	}

	//Add
	for (var k in addarSeries)
	{
	    zSerie = addarSeries[k].split(':');
	    if( zSerie[1] == xserie ) xadd = true;
 	}

	if( xadd && xdel ){

	    //Add
	    for (var k in addarSeries)
	    {
		zSerie = addarSeries[k].split(':');	
		if( zSerie[1] == xserie ) 
		    addarSeries.splice(k,1);
 
 	    }
	    cAddSerie2Cart[ cIdPresupuestoDet ]=addarSeries;
	    //Del
	    for (var k in delarSeries)
	    {
		zSerie = delarSeries[k].split(':');
		if( zSerie[1] == xserie ) 
		    delarSeries.splice(k,1);
 	    }
	    cDelSerie2Cart[ cIdPresupuestoDet ]=delarSeries;

	}
    }

    function limpiar_cajackbox(){
        id("ckserie").value = "";
        id("ckserie").focus();
    }

    function salvarReservaNS2Presupuesto(){
	
	var xdata  = ( cAddSerie2Cart[ cIdPresupuestoDet ] )? '~'+cAddSerie2Cart[ cIdPresupuestoDet ].toString()+'~'+cDelSerie2Cart[ cIdPresupuestoDet ].toString():'~~';
        if( xdata != '~~' )
	{
	    if (cCantidad == cCantSerieCart)
	    {
		var xrequest = new XMLHttpRequest();
		var url      = "modpedidosventa.php?modo=salvarNSReservadaPresupuesto"+
 		    "&xidproducto=" + escape(cIdProducto)+
		    "&xidlocal="+cIdLocal+"&"+
		    "&xpresupuesto=" + escape(cIdPresupuesto);

		xrequest.open("POST",url,false);
		xrequest.setRequestHeader('Content-Type',
					  'application/x-www-form-urlencoded; charset=UTF-8');
		xrequest.send('xdata='+xdata);

		var xres  = xrequest.responseText;
		var axres = xres.split('~');
		if( axres[0] != '1' )
		    alert(po_servidorocupado+'\n '+axres[0]);

		loadDetallesPedidosVenta( cIdPresupuesto );
	    }else{

		if(!confirm( 'gPOS: Números Serie\n\n     Cantidad : '+cCantidad+' '+cUnidadM+'\n     Series     : '+cCantSerieCart+' Series seleccionadas\n\n      - La lista esta incompleta - ' )) return;
		
	    }
	}
	
	cDelSerie2Cart[ cIdPresupuestoDet ] = Array(); 
	cAddSerie2Cart[ cIdPresupuestoDet ] = Array(); 
	cCantSerieCart = 0;
	cEditSerie     = false; 
	volverPedidosVenta();
    }
function CogeCliente() { popup('../../modulos/clientes/selcliente.php?modo=clientepost','proveedorhab');  }
function loadCliente() { ModificarPedidosVenta(3); }