/*++++++++++++++++++++++ INIT +++++++++++++++++++++++++*/

    var id          = function(name) { return document.getElementById(name); }
    var comprobante = 0;
    var cLoadCBalter= '';
    var Vistas      = new Object(); 
    Vistas.ventas   = 7;
    Vistas.abonar   = 10;
    Vistas.tpv      = 0; 
    Vistas.caja     = 11;
    Vistas.guia     = 13;
    cURLPrint       = '';


    //Ultimo articulo añadido al carrito.
    var xlastArticulo;
    var accionInicioTPV   = function() {  despachadortpv(); cargarDatosInicio(); }
    var despachadortpv    = function() {  addEventListener("focus",setFocusedElement,true); }
    var setFocusedElement = function() { 
	if( document.commandDispatcher.focusedElement )
	    Local.textActive = ( document.commandDispatcher.focusedElement.tagName == 'html:input');
    }
    var ckTexElementActivo = function() { 

	if ( !Local.textActive ) return false;

	for(var v=0;v < Local.textId.length;v++) 
	{
	    if( id( Local.textId[v] ).getAttribute("focused") ) 
		if( esTextOcupado( id( Local.textId[v] ) ) ) 
		    return true; 
	}
	return false; 
    }
    var esTextOcupado =  function( xthis ){
	//focus?
	if( !( xthis.getAttribute("focused") ) ) return false;

	//ocupado?
	Local.textOcupado = ( Local.textValue != '~' && Local.textValue == xthis.value)? false:true;
	Local.textValue   = ( Local.textOcupado )? xthis.value : '~';
	return Local.textOcupado;
    }

    /*++++ Conexion Estatus ++++++++*/

    //INFO: imagen de prohibido, para utilizar en seÃ±alizar conexion perdida
    var esGraficoConectado = true;
    var urlprohibido       = "data:image/gif;base64,R0lGODlhDAAMAMQAAPpbW/8AAPPz8/8zM/XFxfednf9aWveUlP4PD/iJifTl5flycv0iIvTV1f4KCv9mZvenp/4XF////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAUUABIALAAAAAAMAAwAAAVHoCAeQxAMhygqpekOisiYAAQB5iAcppOoEBMp4MgRRLhW4eF6KIIuiIDQYixyAYAqgQhETryAVNRopVq1W27Vcp1iqiFYFQIAOw==";

    /*+++++++ POOL  ++++++*/

    //var productos_series = new Array();
    var all_series      = new Array();
    var all_series_cb   = new Array();
    var iprodSerie      = 0;
    var cListaProductos = 'compact';  //compact, column
    var cListaCompacta  = true;  //compact, column
    var cEsDevolucionDetalle = false;
    var cDevolucionModo = '';
    var cDevolucion     = new Array();
    var cDevolucionList = new Array();
    var prodSerie       = new Array();
    var series_carrito  = new Array();
    var carritoserie    = new Array();
    var ticketserie     = new Array(); //Productos en la cesta
    var ticketTotalImporte = 0;        //TotalImporte Ticket
    var ticketlistserie = new Array(); //ayuda al listado de cesta

    var productos       = new Array(); //Unidades en tienda
    var servicios       = new Array(); //Servicios en tienda
    var promociones     = new Array(); //Promociones
    var promocioneslist = new Array(); //Promociones Lista
    var promocionesval  = new Array(); //Promociones Validadas 
    var promocionesmontoval    = new Array(); //Promociones Monto Validadas 
    var PromocionSeleccionado  = 0;      //Id Promocion
    var xPromocionSeleccionado = false;
    var lPromocionSeleccionado = false;
    var promocionessynlist     = new Array(); //Promociones
    var prodlist        = new Array(); //ayuda al listado de "productos" (minivista de productos)
    var prodlist_cb     = new Array(); //ayuda al listado de "productos" (minivista de productos)
    var prodlist_tag    = new Array(); //ayuda al listado de "productos" (minivista de productos)
    var iprod           = 0;
    var prodCod         = new Array(); //listado de codigos de barras
    var iprodCod        = 0; //indexado de codigos de barras

    var carrito         = new Array(); //Unidades de cesta (obsoleto)
    var ticket          = new Array(); //Productos en la cesta
    var ticketlist      = new Array(); //ayuda al listado de cesta
    var iticket         = 0;

    var isuscripciones  = 0;
    var suscripciones     = new Array();
    var suscripcionesclient = new Array();

    var pool            = new Pool();
    var tpv             = new Object();
    var ref2code        = new Array();
    var cLoadModulo     = '';

    /*+++++++++++++++++ Mensajes Demonio ++++++++++++*/

    var buzonMensajes = id("buzon-mensajes");
    var AjaxMensajes  = new XMLHttpRequest();
    var ocupado       = 0;
    var ultimoLeido   = 0;

    var  peticionesSinRespuesta = 0;
    var  test1                  = 0;

    /*+++++++++++++++++ Demon ++++++++++++++++++++*/
    var AjaxDemon     = new XMLHttpRequest();
    var AjaxSyncDemon = new XMLHttpRequest();

    function runsyncTPV( xdemon ){

	switch ( xdemon ){ 
	case 'Preventa'      : syncPresupuesto('Preventa');       break;
	case 'Proforma'      : syncPresupuesto('Proforma');       break;
	case 'ProformaOnline': syncPresupuesto('ProformaOnline'); break;
	case 'MProductos'    : syncMProducto();    break;
	case 'Clientes'      : syncClientes();     break;
	case 'Caja'          : reloadCaja();       break;
	case 'Mensajes'      : syncMensajes();     break;
	case 'Stock'         : syncProductosTPV(); break;
	case 'StockPost'     : syncProductosPostTicket(); break;
	case 'Promociones'   : syncPromociones();  break;
	}//setTimeout("runsyncTPV('')",200);
    }

    function endRunDemonTPV(){
	//alert('termina');
        esSyncBoton('pause');
    }

    function Reload_demon_syncTPV(){
	//Sync
	esOffLineSyncTPV = false;
	esSyncBoton();
	pushSyncTPV();//lanza Sync Demon
    }

    function pushSyncTPV(){
	
	//Termina Brutal
	if(esSyncBoton('on')) return;

	mostrarMensajeTPV(1,'Sincronizando datos...',4200);

	syncProductosPostTicket();               //Productos
	syncPresupuesto('Preventa');             //PreVentas
        syncPresupuesto('Proforma');             //Proformas
        syncPresupuesto('ProformaOnline');       //ProformasOnline
        setTimeout("syncMensajes()",100);        //Mensajes
	setTimeout("syncMProducto()",200);       //MetaProductos
	setTimeout("syncPromociones()",300);     //Promociones
        setTimeout("syncClientes()",400);        //Clientes 

    }

    function pushSyncModule(xmodulo){

    	//Termina Brutal
	if(esSyncBoton('on')) return;
    	//Termina Brutal
	if( esSyncModuleBoton('syncModulo'+xmodulo,'on') ) return;
        syncClientes();
        buscarCliente();
        //Se oculta muy pronto
        setTimeout("esSyncModuleBoton('syncModulo"+xmodulo+"')",2000);
    }

    function syncTipoVentaTPV(){
	
	//Termina Brutal
	if(esSyncBoton('on')) return;

	mostrarMensajeTPV(1,'Sincronizando datos...',2400);

	//syncProductosPostTicket();               //Productos
	syncPresupuesto('Preventa');               //PreVentas
        syncPresupuesto('Proforma');               //Proformas
        syncPresupuesto('ProformaOnline');         //ProformasOnline
	setTimeout("reloadCaja()",10);	           //Recargar Caja
        //setTimeout("syncMensajes()",100);        //Mensajes
	//setTimeout("syncMProducto()",200);       //MetaProductos
	//setTimeout("syncPromociones()",300);     //Promociones
        //setTimeout("syncClientes()",400);        //Clientes 

    }

    function Demon_syncTPV(){

	if ( !ckTexElementActivo() ) syncTPV(); //Sync Modulos
        setTimeout("Demon_syncTPV()",19999); //Recursivo
    }

    function syncTPV(){

 	//Check Conecction
	//if(syncCheckConnection()) return;

	//Termina Brutal
	if(esSyncBoton('on')) return;

        if (!AjaxSyncDemon)
            AjaxSyncDemon = new XMLHttpRequest();	

        //Peticiones realizadas
        peticionesSinRespuesta = peticionesSinRespuesta +1;			

        var url = "services.php?modo=getsyncTPV";
        AjaxSyncDemon.open("POST",url,true);
        AjaxSyncDemon.onreadystatechange = RececepcionSyncTPV;
        AjaxSyncDemon.send(null);
	endRunDemonTPV();

	//Arqueo caja
	setTimeout("ActualizacionEstadoOnline()",4000);
    }

    function RececepcionSyncTPV(){

        if (AjaxSyncDemon.readyState==4) {
	    if (AjaxSyncDemon){
                if (AjaxSyncDemon.status=="200")
		    peticionesSinRespuesta = 0;
	        else
		    return;
	    }
	    //Si responden, es que estamos online, por tanto "hay respuesta"
	    // y borramos el acumulativo de peticiones sin respuesta. 
	    //alert(AjaxSyncDemon.responseText);
	    
	    var rawtext = AjaxSyncDemon.responseText.split(':');
	    if( rawtext[0] != '')
		alert( c_gpos + po_servidorocupado+ '\n\n'+ rawtext[0]);

	    if( !(rawtext[1]) || rawtext[1] == '') {

		if(window.opener){
		    window.opener.location.href='logout.php'
		    window.close();
		}
		else
		    SalirNice();
		return; //cierra sesión
	    }
	    
	    //0~0~0~0~0~0~0~1~0
	    //Preventa~Proforma~ProformaOline~Stock~Cliente~Promocion~Mensaje~Caja~MProducto
	    //Procesar
	    var xsync = rawtext[1].split('~');
	    Local.esSyncPreventa       = ( xsync[0] == 1 );
	    Local.esSyncProforma       = ( xsync[1] == 1 );
	    Local.esSyncProOnline      = ( xsync[2] == 1 );
	    Local.esSyncStock          = ( xsync[3] == 1 );
	    Local.esSyncClientes       = ( xsync[4] == 1 );
	    Local.esSyncPromociones    = ( xsync[5] == 1 ); 
	    Local.esSyncMensajes       = ( xsync[6] == 1 );
	    Local.esSyncCaja           = ( xsync[7] == 1 );
	    Local.esSyncMProducto      = ( xsync[8] == 1 );

	    setTimeout("syncCoreTPV()",1000);//Lanza demonio sync core
        }
    }

    function syncCoreTPV(){

	//Stock
	if( Local.esSyncStock ) {  
	    if ( !ckTexElementActivo() )
		runsyncTPV('Stock');
	    else 
		return setTimeout("syncCoreTPV()",1000);
	}

	//Mensajes
	if( Local.esSyncMensajes ){
	    if ( !ckTexElementActivo() )
		runsyncTPV('Mensajes');
	    else 
		return setTimeout("syncCoreTPV()",1000);
	}
	
	//Promociones
	if( Local.esSyncPromociones ){
	    if ( !ckTexElementActivo() ) 
		runsyncTPV('Promociones');
	    else
		return setTimeout("syncCoreTPV()",1000);
	}

	//PostTicket
	//Clientes
	if( Local.esSyncClientesPost ){
	    if ( !ckTexElementActivo() ) 
		runsyncTPV('Clientes');
	    else
		return setTimeout("syncCoreTPV()",1000);
	}
	//Stock
	if( Local.esSyncStockPost ) {  
	    if ( !ckTexElementActivo() )
		runsyncTPV('StockPost');
	    else 
		return setTimeout("syncCoreTPV()",1000);
	}

    }

    function  syncMensajes(){

 	//Check Conecction
	//if(syncCheckConnection()) return endRunDemonTPV();

	mostrarMensajeTPV(1,'Sincronizando mensajes...',3200);
	Local.esSyncMensajes=false;

        if (!AjaxMensajes)
            AjaxMensajes = new XMLHttpRequest();	

        //Peticiones realizadas
        //peticionesSinRespuesta = peticionesSinRespuesta +1;			

        var url = "modulos/mensajeria/modbuzon.php?modo=leernuevos&IdUltimo=" + ultimoLeido;
        url = url + "&desdelocal="+encodeURIComponent(  Local.nombretienda );	

        AjaxMensajes.open("POST",url,true);
        AjaxMensajes.onreadystatechange = RececepcionMensajes;
        AjaxMensajes.send(null);
	//Arqueo caja
	//setTimeout("ActualizacionEstadoOnline()",5000);

	endRunDemonTPV();
    }

    function getMensajes(){
	esSyncBoton('on');//Boton Sync
	mostrarMensajeTPV(1,'Cargando mensajes...',3200);
        try {
            if (!AjaxMensajes)
                AjaxMensajes = new XMLHttpRequest();	
        } catch(e) {
            return;
        }
        var url = "modulos/mensajeria/modbuzon.php?modo=hoy";

        AjaxMensajes.open("POST",url,true);
        AjaxMensajes.onreadystatechange = RececepcionMensajes;
        AjaxMensajes.send(null);	
        esSyncBoton('pause');//Boton Sync Termina
    }

    function getPreventas(){
	esSyncBoton('on');//Boton Sync
	CargarPresupuesto('Preventa');//Combo Preventa
        CargarPresupuesto('Proforma');//Combo Proformas
        CargarPresupuesto('ProformaOnline');//Combo Proformas
        esSyncBoton('pause');//Boton Sync Termina
    } 

    function getMProductos(){
	
	mostrarMensajeTPV(1,'Cargando mproductos...',3200);
	//Combo MProductos	     
	CargarComboMProducto();
	//Combo 
        setTimeout("CargarMProducto()",2000);//5seg
    }

    /*+++++++++++++++++ Pool ++++++++++++++++++++*/
    function Pool(nombre){
        this._nombre = nombre;
        this._selected = 0;

        this.get = function () {
            return productos[this._selected];
        }

        this.select = function (idex) {
            this._selected = idex;
        }

        this.add = function (idex, self) {
            productos[idex] = self;
            carrito[idex] = new Object();
            carrito[idex].unidades = 0;
        }

        this.addpromocion = function (idex, self) {
            promociones[idex] = self;
	    promocioneslist.push(idex);
        }

        this.addCarrito = function (idex,uc) {
            var ouc = carrito[idex].unidades;
            carrito[idex].unidades = parseFloat(ouc) + parseFloat(uc);
        }

        this.addCarritoSerie = function (idex,uc){
            var ouc = carrito[idex].numeroserie;
            ouc.push(uc);
            carritoserie[idex].numeroserie = ouc;	
        }

        this.Existe = function (idbusca) {
            if (productos[idbusca]) return 1;
            return null;
        }

        this.ExisteTicket = function ( idt ) {
            if (ticket[idt]) return 1;
            return null;		
        }

        this.CreaTicket = function ( idt ) {

            ticketlist[iticket++]    = idt;		
            ticket[idt]              = new Object();	
            ticket[idt].producto     = productos[idt].producto;
            ticket[idt].nombre       = productos[idt].nombre;
            ticket[idt].descripcion  = productos[idt].descripcion;
            ticket[idt].codigobarras = productos[idt].codigobarras;
            ticket[idt].idproducto   = productos[idt].idproducto;
            ticket[idt].referencia   = productos[idt].referencia;
            ticket[idt].precio       = (Local.TPV=='VC')? productos[idt].pvc  : productos[idt].pvd;
            ticket[idt].costo        = productos[idt].costo;
            ticket[idt].impuesto     = productos[idt].impuesto;
            ticket[idt].color        = productos[idt].color;
            ticket[idt].talla        = productos[idt].talla;
            ticket[idt].menudeo      = productos[idt].menudeo;
	    ticket[idt].cont         = productos[idt].cont;
	    ticket[idt].unid         = productos[idt].unid;
            ticket[idt].unidxcont    = productos[idt].unidxcont;
	    ticket[idt].lote         = productos[idt].lote;
	    ticket[idt].vence        = productos[idt].vence;
	    ticket[idt].oferta       = productos[idt].oferta;
	    ticket[idt].ofertaunid   = productos[idt].ofertaunid;
	    ticket[idt].pvo          = productos[idt].pvo;
            ticket[idt].series       = new Array();
            ticket[idt].pedidodet    = '';
            ticket[idt].concepto     = '';
            ticket[idt].unidades     = 0;
	    ticket[idt].idsubsidiario = 0;
	    ticket[idt].descuento    = 0;
            ticket[idt].vdetalle     = '';
            ticket[idt].cStatus      = '';
	    ticket[idt].importe      = 0;
        }

    }


    /*+++++++++++++++++ Promociones ++++++++++++++++*/

    function tPL(IdPromocion,IdPromocionCliente,Descripcion,Modalidad,MontoCompraActual,
		 Tipo,IdLocalPromo,CBProducto0,CBProducto1,Descuento,Bono,Prioridad){

	if(promociones[IdPromocion])
	    return utPL(IdPromocion,IdPromocionCliente,Descripcion,Modalidad,MontoCompraActual,
			Tipo,IdLocalPromo,CBProducto0,CBProducto1,Descuento,Bono,Prioridad);
	
        var p          = new Object();

	p.id           = IdPromocion;
	p.promocliente = IdPromocionCliente;
	p.promocion    = Descripcion;
	p.modalidad    = Modalidad;
	p.monto        = MontoCompraActual;
	p.tipo         = Tipo;
	p.IdLocal      = IdLocalPromo;
	p.cb0          = CBProducto0;
	p.cb1          = CBProducto1;
	p.descuento    = Descuento;
	p.bono         = Bono;
	p.prioridad    = Prioridad;

        pool.addpromocion(IdPromocion,p);
    }

    function utPL(IdPromocion,IdPromocionCliente,Descripcion,Modalidad,MontoCompraActual,
		  Tipo,IdLocalPromo,CBProducto0,CBProducto1,Descuento,Bono,Prioridad){

	promociones[IdPromocion].promocliente = IdPromocionCliente;
	promociones[IdPromocion].promocion    = Descripcion;
	promociones[IdPromocion].modalidad    = Modalidad;
	promociones[IdPromocion].monto        = MontoCompraActual;
	promociones[IdPromocion].tipo         = Tipo;
	promociones[IdPromocion].IdLocal      = IdLocalPromo;
	promociones[IdPromocion].cb0          = CBProducto0;
	promociones[IdPromocion].cb1          = CBProducto1;
	promociones[IdPromocion].descuento    = Descuento;
	promociones[IdPromocion].bono         = Bono;
	promociones[IdPromocion].prioridad    = Prioridad;
	promocioneslist.push(IdPromocion);
    }

    /*+++++++++++++++++ Productos ++++++++++++++++*/

    //Funcion compacta para crear articulo
    function tA(idproducto,codigo,Lnombre,imagen,referencia,centimopvd,pvemp,pvdoce,centimopvc,
		impuesto,LTalla,LColor,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,
		Lalias1,Lalias2,refprovhab,unidades,serie,LMarca,costo,ventamenudeo,unidxcontenedor,
		unidmedida,Llaboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
		ilimitado,dosis){

        //Traduce desde lex a normal.
        var talla       = (LTalla)?L[LTalla]:"";		
        var color       = (LColor)?L[LColor]:"";
        var marca       = (LMarca)?L[LMarca]:"";
        var nombre      = (Lnombre)?L[Lnombre]:"";
        var laboratorio = (Llaboratorio)?L[Llaboratorio]:"";
        var alias1      = (Lalias1)?L[Lalias1]:"";
        var alias2      = (Lalias2)?L[Lalias2]:"";
        talla           = ( talla!="..." )? talla:"";
        color           = ( color!="..." )? color:"";
        marca           = ( marca!="..." )? marca:"";
        laboratorio     = ( laboratorio!="."  )? laboratorio:"";
        contenedor      = ( contenedor!="..." )? contenedor:"";

        //Funcion "larga" que no acepta el uso de lexers
	tAL(idproducto,codigo,nombre,imagen,referencia,centimopvd,pvemp,pvdoce,centimopvc,impuesto,
	    talla,color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,alias1,
	    alias2,refprovhab,unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,unidmedida,
	    laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
	    ilimitado,dosis);
    }

    function tAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,pvemp,pvdoce,centimopvc,
		 impuesto,Talla,Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,
		 alias1,alias2,refprovhab,unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,
		 unidmedida,laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,ilimitado,
		 dosis){
	
        if (!codigo) return;//No acepta lexers

        codigo   = new String(codigo);	

	//Ya tenemos este producto listado
        if (pool.Existe( codigo.toUpperCase() ))
            return stAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,pvemp,pvdoce,centimopvc,
			impuesto,Talla,Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,
			rKardex,alias1,alias2,refprovhab,unidades,serie,marca,costo,ventamenudeo,
			unidxcontenedor,unidmedida,laboratorio,contenedor,pvdd,pvcd,vence,lote,
			servicio,mproducto,ilimitado,dosis);


        var alote       = ( lote  )? lote.split("~")    :false;
        var adosis      = ( dosis )? dosis.split("&")   :false;
        var avence      = ( vence )? vence.split("~")   :false;
        var aserie      = ( serie )? serie.split("~")   :false;
        var akardex     = ( rKardex )? rKardex.split("~") :false;
	
	var xsrt        = " ";
	var xproduct    = codigo+xsrt+xsrt+Nombre+xsrt+marca+xsrt+Color+xsrt+Talla+xsrt+laboratorio;
        var a           = new Object();
	
        codigo          = new String(codigo);
        codigo          = codigo.toUpperCase();

        a.nombre2 	= nombre2;	
        a.idsubsidiario = idsubsidiario;
        a.esServicio    = (idsubsidiario)?1:0;
	//Codigos
        a.idproducto 	= idproducto;
        a.codigobarras 	= codigo;
        a.referencia 	= referencia;
        a.refprovhab	= refprovhab;
	//Precio
	a.oferta 	= Oferta;
	a.ofertaunid 	= OfertaUnid;
        a.pvd   	= (centimopvd/100).toFixed(2);
	a.pvemp   	= pvemp;
	a.pvdoce   	= pvdoce;
        a.pvc   	= (centimopvc/100).toFixed(2);
        a.impuesto 	= impuesto;
        a.costo         = costo;
        a.pvdd          = pvdd;
        a.pvcd          = pvcd;
        a.pvo           = pvo;
	//Descripcion
        a.producto      = unescapeHTML(xproduct);
        a.imagen        = imagen;
        a.talla         = Talla;		
        a.color         = Color;
        a.marca         = marca;
        a.nombre        = Nombre
        a.nombre        = unescapeHTML(a.nombre);
        a.color         = unescapeHTML(a.color);
        a.talla         = unescapeHTML(a.talla);	
        a.marca         = unescapeHTML(a.marca);	
        a.laboratorio   = laboratorio;
        a.alias1        = alias1;
        a.alias2        = alias2;
	//Detalles
        a.rkardex       = akardex;
        a.unidades      = (servicio || ilimitado )? 1: unidades;
        a.serie         = aserie;
        a.vence         = avence;
        a.lote          = alote;
        a.menudeo       = ventamenudeo;
        a.servicio      = servicio;
        a.mproducto     = mproducto;
        a.ilimitado     = ilimitado;
        a.unidxcont     = unidxcontenedor;
        a.unid          = unidmedida;
        a.cont          = contenedor;
        a.dosis         = adosis
        a.condventa     = condventa;

        //log("Creando articulo "+a.nombre+" - "+codigo);	 	 
        pool.add(codigo,a);

	//almacenando nuevos servicios
	if( servicio ) servicios.push(codigo+'~'+servicio);

        /* Mantiene tablas cruzadas que ayudan en las busquedas */
        var refstr           = ref2code[referencia];
	ref2code[referencia] = (refstr)? refstr + "," + codigo:codigo;

        if ( prodCod[iprodCod] != codigo ) 
	{
            prodCod[iprodCod] = codigo;
            iprodCod ++;
        }	

	Local.productos++;
	id("txt-productoprogress").label = 'Cargando '+Local.productos+' productos...'   	
	//setTimeout(function(){xProgress(true);},600);
    }

    function stAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,pvemp,pvdoce,centimopvc,
		  impuesto,Talla,Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,
		  alias1,alias2,refprovhab,unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,
		  unidmedida,laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
		  ilimitado,dosis){
	//alert("stAL ->"+codigo );
        codigo  = new String(codigo);	
	
        var alote    = ( lote  )? lote.split("~")    :false;
        var adosis   = ( dosis )? dosis.split("~")   :false;
        var avence   = ( vence )? vence.split("~")   :false;
        var aserie   = ( serie )? serie.split("~")   :false;
        var akardex  = ( rKardex )? rKardex.split("~") :false;
	var xsrt     = " ";
	var precio   = (Local.TPV=='VC')? (centimopvc/100).toFixed(2):(centimopvd/100).toFixed(2);
	var xproduct = codigo+xsrt+xsrt+Nombre+xsrt+marca+xsrt+Color+xsrt+Talla+xsrt+laboratorio;
        var xsyn     = productos[codigo];		

        if (xsyn) 
	{					
            xsyn.nombre2       = nombre2;	
            xsyn.idsubsidiario = idsubsidiario;
            xsyn.esServicio    = (idsubsidiario)?1:0;
	    //Codigos
	    xsyn.oferta        = Oferta;
	    xsyn.ofertaunid    = OfertaUnid;
            xsyn.pvd           = (centimopvd/100).toFixed(2);
	    xsyn.pvemp         = pvemp;
	    xsyn.pvdoce        = pvdoce;
            xsyn.pvc           = (centimopvc/100).toFixed(2);
            xsyn.impuesto      = impuesto;
            xsyn.costo         = costo;
            xsyn.pvdd          = pvdd;
            xsyn.pvcd          = pvcd;
            xsyn.pvo           = pvo;
	    //Descripcion
            xsyn.producto      = unescapeHTML(xproduct);
            xsyn.imagen        = imagen;
            xsyn.talla         = Talla;		
            xsyn.color         = Color;
            xsyn.marca         = marca;
            xsyn.nombre        = Nombre
            xsyn.nombre        = unescapeHTML(xsyn.nombre);
            xsyn.color         = unescapeHTML(xsyn.color);
            xsyn.talla         = unescapeHTML(xsyn.talla);	
            xsyn.marca         = unescapeHTML(xsyn.marca);	
            xsyn.laboratorio   = laboratorio;
            xsyn.alias1        = alias1;
            xsyn.alias2        = alias2;
	    //Detalles
            xsyn.rkardex       = akardex;
            xsyn.unidades      = (servicio || ilimitado )? 1: unidades;
            xsyn.serie         = aserie;
            xsyn.vence         = avence;
            xsyn.lote          = alote;
            xsyn.menudeo       = ventamenudeo;
            xsyn.servicio      = servicio;
            xsyn.mproducto     = mproducto;
            xsyn.ilimitado     = ilimitado;
            xsyn.unidxcont     = unidxcontenedor;
            xsyn.unid          = unidmedida;
            xsyn.cont          = contenedor;
            xsyn.dosis         = adosis;
            xsyn.condventa     = condventa;

	    //Refresca Lista
	    if ( prodlist_cb[codigo]) 
		ModificarEntradaEnProductos(xsyn.producto,codigo,xsyn.referencia,
					    precio,pvemp,pvdoce,xsyn.pvc,
					    xsyn.impuesto,xsyn.unidades,xsyn.costo,xsyn.lote,
					    xsyn.vence,xsyn.serie,xsyn.menudeo,xsyn.unidxcont,
					    xsyn.unid,xsyn.cont,xsyn.servicio,xsyn.ilimitado,
					    xsyn.oferta,xsyn.ofertaunid,xsyn.pvo,xsyn.condventa,
					    xsyn.mproducto);
	    
        }	
    }
   

    /*+++++++++++++++ HOT KEYS +++++++++++++++*/

    document.onkeydown = function(event) {   

	//alert(event.ctrlKey+' '+event.keyCode); 
	//alert(event.shiftKey+' '+event.keyCode);
	//alert(event.keyCode); 

	switch (event.keyCode) { 
            
        case 45 : //Insert
	    pushSyncTPV();
	    break;
        case 112 : //F1
            VerTPV();
	    break;
        case 113 ://F2 
	    MostrarUsuariosForm();
	    break;
        case 114 ://F3 
	    break;
        case 115 : //F4
            VerTPV();
            elijeComprobanteTPV();
	    break;
        case 118 : //F7
	    selTipoPresupuesto(2);
	    id("buscapedido").focus(); 
	    break;
        case 119 : //F8
	    selTipoPresupuesto(1);
	    id("buscapedido").focus(); 
	    break;
        case 120 : //F9
            syncClientes();
	    break;
        case 122 :
	    break;
	}

	if(event.shiftKey) 

	    switch (event.keyCode) { 

            case 13 : 
		//Shift + Enter
                //Pregunta unidades del ultimo articulo agregado
                if ( ticketlist.length == 0 )
	            return;
                
                //alert( cLoadCBalter );
                ModificaTicketUnidades(-1);
		break;

            case 46 : 
                BorrarVentaTPV();
                selTipoPresupuesto(0);
		break;
	    } 

	if(event.ctrlKey) 

	    switch (event.keyCode) { 

            case 112 ://ctrol + F1  
                VerVentas();
	        break;
            case 113 ://ctrol + F2
                VerServicios();
	    break;
            case 114 ://ctrol + F3
                VerTPV();
                id("CB").focus();
	        break;
            case 120 : //F9 
	        VerCaja();
	        break;
            case 122 : //F11 
                syncPresupuesto('Preventa');
	        break;
	    }


    }


/*+++++++++++++++++++++++++++++ PREVENTA ++++++++++++++++++++++++++++++++++*/


        /*++++++++++++ PREVENTA ++++++++++++*/

        //Array Presupuestos
        var aProforma = new Array();
        var aProformaOnline = new Array();
        var aPreventa = new Array();
        var aPedido   = new Array();

        //Array Meta Productos
        var aMProductos = new Array();

        var cFechaProforma = '';

        /*++++++++++++++++ Busquedas ++++++++++++++*/

        function buscarNroTicket(){
 	    var snr = id("buscapedido");
	    var stv = id("t_preventa").getAttribute('checked');
	    var stp = id("t_proforma").getAttribute('checked');
	    var stpol= id("t_proformaonline").getAttribute('checked');
	    var stm = id("t_mproducto").getAttribute('checked');
	    var tpd;

	    //Numero buscado
	    sid = snr.value;

	    //Ticket 
	    if( stv == 'true') tpd = 'Preventa';
	    if( stp == 'true') tpd = 'Proforma';
	    if( stpol == 'true') tpd = 'ProformaOnline';
	    if( stm == 'true') tpd = 'MProducto';
	    
	    var combo = id("Sel"+tpd);

	    //Validamos
	    if(isNaN(parseInt(sid)))
		return alert( c_gpos + '\n  - Ingrese un número de '+tpd+'.' );

	    //Busqueda
	    for (var q=0; q<aPedido.length; q++) {
		var it = aPedido[q].split(':');
		if( tpd == it[0] ){
		    if( parseInt(sid) == parseInt(it[1]) )
		    {
			if( stm == 'true')			
			    var item = id(it[1]);
			else
			    var item = id(it[2]);


			if(item)
			{
			    //Load 
			    combo.setAttribute('label', item.getAttribute('label') );
			    if( stm == 'true')			
				cargarDetMProductoACarrito(it[2],it[1],it[3]);
			    else
				cargarDetPresupuestoACarrito(it[2],it[3],it[4]);
			    return snr.value ='Nro';//termina proceso!!
			} 
			else
			{
			    //Delete, si no esta en la lista
			    aPedido.splice(q,1);
			    return alert( c_gpos + '\n   -  '+tpd+' Nro. '+sid+', atendido.' ); 
			} 

		    }
		}
	    }
	    snr.value ='Nro'; 
	    return alert( c_gpos +'\n   -  '+tpd+' Nro. '+sid+', no disponible.' ); 
	    
	}


        /*++++++++++++ AGNADIR +++++++++++++++++*/

        function focuslistaProductos(){
	    var xbosprodutos = id("listaProductos"); 
	    xbosprodutos.focus();
	}

        function agnadirPorReferencia()	{
            var referencia = id("REF").value.toUpperCase();
            if (!referencia) return;
            referencia  = new String(referencia);

            if (referencia.length <1) return;

            raw_agnadirPorReferencia( CleanRef(referencia) );
	}

        function agnadirPorRefProv()	{ 
            var cod,text = "",k;
	    var precio   = 0;
            var refprov  = new String(id("REFPROV").value);
            refprov=trim(refprov);
            id("REFPROV").value="";
            if(refprov.length<1){
		OcultarAjax();
		return;	
            }
            VaciarListadoProductos();	
            refprov = refprov.toUpperCase();
            //var tienda = document.getElementById("NombreLocalActivo").firstChild.nodeValue;	
            //var tienda1 = new String(tienda);
            for(var t=0;t<iprodCod;t++) {
		cod = prodCod[t];
		nom = productos[cod].refprovhab;
		if   (nom==refprov)   
		{
                    k      = productos[cod];
		    precio = (Local.TPV=='VC')? k.pvc:k.pvd;
                    if (k)
			CrearEntradaEnProductos(k.producto,k.nombre,k.marca,k.color,k.talla,
						k.laboratorio,
						k.codigobarras,k.referencia,precio,
						k.pvemp,k.pvdoce,k.pvc,
						k.impuesto,k.unidades,k.costo,k.lote,k.vence,
						k.serie,k.menudeo,k.unidxcont,k.unid,k.cont,
						k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
						k.pvo,k.condventa,k.mproducto);
		}
            }

	}

        function agnadirPorNombre() {
	    //MostrarAjax();
	    setTimeout("raw_agnadirPorNombre()",200);
	}

        function agnadirPorMenu( unidades )	{

            var cod     = getCodigoSelectedProd();
	    var pstock  = id("prevt-stock").getAttribute("checked");
	    var modo    = (pstock != "true")? "pedidos":id("rgModosTicket").value;
	    var esMayoreo = (unidades=="mayoreo");
	    var esDocena  = (unidades=="docena");
	    var esCorporativo = (unidades=="corporativo");
	    
            if (!cod) return;
	    if (habilitarAddMProducto()) return;
	    if (productos[cod].servicio || productos[cod].ilimitado) modo = "pedidos";//Servicio

	    //inicia
            if (unidades=="preguntar")
	    {
		unidades = prompt(po_cuantasunidades,0);
		unidades = parseInt(unidades);//Control de Enteros

		if ( isNaN(unidades) )
		    return alert( c_gpos + 'Ingresar un valor numérico');
		
		if ( unidades < 0 )
		    return alert( c_gpos + 'Ingresar un valor numérico positivo.');

		if ( !unidades || unidades<0 ) 
		    return;
            }

	    //Inicia Menudeo
            if (esMayoreo)
	    {
		
		if(!productos[cod].menudeo) return;

		var xunidades = prompt('¿Cuántas '+productos[cod].cont+'+'+
				       productos[cod].unid+' ( PV/E: '+productos[cod].pvemp+' ) ?',0);
		if(!xunidades) return;
		var aUnidades = xunidades.split('+');
		var xcont     = aUnidades[0]*productos[cod].unidxcont;
		var xunid     = (aUnidades[1])? parseInt(aUnidades[1]):0;
		unidades      = xcont+xunid;

		//Control de Enteros

		if ( isNaN(unidades) )
		    return alert(c_gpos + 'Ingresar un valor numérico');
		
		if ( unidades < 0 )
		    return alert(c_gpos + 'Ingresar un valor numérico positivo.');

		if ( !unidades || unidades<0 ) 
		    return;
            }

            if ( esDocena )
	    {
		//if(!productos[cod].menudeo) return;
		var aunidades;
		var xunidades = prompt('¿Cuántas Docenas ( PV/D: '+productos[cod].pvdoce+' ) ?',0);
		if(!xunidades) return;

		aunidades     = xunidades.split(".");

		if( aunidades[1] )
		{
		    xunidades    = parseFloat(xunidades);
		    xunidades    = xunidades.toFixed(1);
		    aunidades    = xunidades.split(".");
		    aunidades[1] = ( parseInt( aunidades[1] ) <=  5  )? aunidades[1]:0;
		    aunidades[1] = ( parseInt( aunidades[1] ) ==  5  )? 6:aunidades[1];
		    unidades     = parseInt(aunidades[1]) + parseInt( aunidades[0] ) * parseInt( 12 );
		} 
		else
		    unidades  = parseInt(xunidades) * parseInt( 12 );
		
		//Control de Enteros
		if ( isNaN(unidades) )
		    return alert(c_gpos + 'Ingresar un valor numérico');
		
		if ( unidades < 0 )
		    return alert(c_gpos + 'Ingresar un valor numérico positivo.');

		if ( !unidades || unidades<0 ) 
		    return;
            }

            if ( esCorporativo )
	    {
		if( Local.esB2B == 0 ) return;
		
		var xunidades = prompt('¿Cuántas unidades a precio corporativo ( PVC/U: '+productos[cod].pvc+' ) ?',0);
		if(!xunidades) return;
		unidades     = parseInt(xunidades);

            }
	    
 	    //Stock
            if (!unidades) { unidades = 1;}
            unidades = ConvertirSignoApropiado( unidades );

            //Stock Almacen
            unidadesalmacen = productos[cod].unidades;

            if(ticket[cod])
	    {
		var ticketunidades = parseFloat(ticket[cod].unidades)+parseFloat(unidades);

		if(modo!="pedidos")
		{
		    if(ticketunidades > unidadesalmacen)
			return alert( c_stockalmacen + productos[cod].producto +
				     "\n\n * Las unidades seleccionadas - "+ ticketunidades +
				     " - excede a  "+unidadesalmacen+
				     " - del stock.");
		    if(ticket[cod].unidades == unidadesalmacen)
			return alert( c_stockalmacen + productos[cod].producto +
				     "\n\n * No hay mas unidades de este producto.");
		}
            }

	    if(modo!="pedidos")
	    {
		if(unidadesalmacen == 0 )
		    return alert( c_stockalmacen + productos[cod].producto +
				  "\n\n   * Existen "+unidadesalmacen+
				  " unidades de este producto en almacén.");
		if(unidades > unidadesalmacen)
		{
		    if(unidadesalmacen>1)
			return alert( c_stockalmacen + productos[cod].producto +
				     "\n\n   * Existen "+unidadesalmacen+
				     " unidades de este producto en almacén.");
		    if(unidadesalmacen==1)
			return alert( c_stockalmacen + productos[cod].producto +
				     "\n\n   * Existe "+unidadesalmacen+
				     " unidad de este producto en almacén");
		}
	    }

	    //Series
 	    if( modo != "pedidos" && productos[cod].serie ) 
		return agnadirPorSeries(productos[cod].serie,
					productos[cod].producto,
					productos[cod].unidades,
					cod,unidades);

	    //Carrito TPV
	    if ( esMayoreo )
		tpv.AddCarritoMayoreo( cod.toUpperCase() , unidades);
	    else if( esDocena )
		tpv.AddCarritoDocena( cod.toUpperCase() , unidades);
	    else if( esCorporativo )
		tpv.AddCarritoCorporativo( cod.toUpperCase() , unidades);
	    else
		tpv.AddCarrito( cod.toUpperCase() , unidades);

            var extatus = id("rgModosTicket").value;	
            if (extatus == 'interno' ){		

		if(xlastArticulo) 
		    xlastArticulo.focus();		
		ServicioParaFila();

            }
	    id("NOM").focus();
	}

        function agnadirPorCodigoBarras() {

            var cb  = id("CB");
            var vcb = CleanCB(cb.value);

            if (vcb.length < 1 ) return;

	    if(habilitarAddMProducto()) return;

            cb.value = "";
            cb.setAttribute("value","");

  
	    //Tenemos este producto listado?
            if (!pool.Existe( vcb.toUpperCase() ))
	    {
		//Intenta anhadirlo...
		ExtraBuscarEnServidorXCB(vcb);
		//Existe...
		if(!productos[vcb]) return; 
	    }
            //Encuentra, 
	    if(vcb!='') raw_agnadirPorCodigoBarras(vcb,true);
	} 


        /*++++++++++++ ROW +++++++++++++++++++++*/
  
        //NOTA: agnadir por nombre se ejecuta en dos fases, para permitir que la visualizacion de iconos
        // ajax sea visible para el usuario.
        function raw_agnadirPorNombre() {	
	    
	    var cod,text  = "",k;
	    var nombre    = new String(id("NOM").value);
	    var cadenas   = nombre.split("|");
	    var precio    = 0;
	    var cadena1   = trim(cadenas[0]).toUpperCase();
            var cadena2   = (cadenas[1])? trim(cadenas[1]).toUpperCase():"";
	    //id("NOM").value = "";
	    
	    if (nombre.length < 3) return;

	    VaciarListadoProductos();		

	    nombre     = nombre.toUpperCase();
	    //var tienda = id("NombreLocalActivo").firstChild.nodeValue;

	    //Busqueda Array
	    for(var t=0;t<iprodCod;t++) 
	    {
		cod         = prodCod[t];
		nom         = productos[cod].nombre.toUpperCase();
		al1         = productos[cod].alias1.toUpperCase();
		al2         = productos[cod].alias2.toUpperCase();	
		marca       = productos[cod].marca.toUpperCase();	
		modelo      = productos[cod].talla.toUpperCase();	
		detalle     = productos[cod].color.toUpperCase();	
		ref         = productos[cod].refprovhab;//.toUpperCase();	
		laboratorio = productos[cod].laboratorio.toUpperCase();	
		//var tienda1 = new String(tienda);

		//if (nom.indexOf( nombre ) != -1) {
		if  ( (nom.indexOf( cadena1 ) != -1))  
		{
		    k      = productos[cod];
		    precio = (Local.TPV=='VC')? k.pvc:k.pvd;

		    if(cadena2=="")
		    {
			CrearEntradaEnProductos(k.producto,k.nombre,k.marca,k.color,k.talla,
						k.laboratorio,
						k.codigobarras,k.referencia,precio,
						k.pvemp,k.pvdoce,k.pvc,
						k.impuesto,k.unidades,k.costo,k.lote,k.vence,
						k.serie,k.menudeo,k.unidxcont,k.unid,k.cont,
						k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
						k.pvo,k.condventa,k.mproducto);
 		    }
		    else{
			if( (marca.indexOf(cadena2)!= -1   ) ||
			    (modelo.indexOf(cadena2) != -1 ) ||
			    (detalle.indexOf(cadena2) != -1) || 
			    (laboratorio.indexOf(cadena2) != -1) || 
			    (al1.indexOf( cadena2 ) != -1) || 
			    (al2.indexOf( cadena2 ) != -1) || 
			    (ref.indexOf( cadena2 ) != -1) )
			    CrearEntradaEnProductos(k.producto,k.nombre,k.marca,k.color,k.talla,
						    k.laboratorio,
						    k.codigobarras,k.referencia,precio,
						    k.pvemp,k.pvdoce,k.pvc,
						    k.impuesto,k.unidades,k.costo,k.lote,k.vence,
						    k.serie,k.menudeo,k.unidxcont,k.unid,k.cont,
						    k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
						    k.pvo,k.condventa,k.mproducto);
		    }
		}
	    }

	    if (  esOnlineBusquedas()  )
		ExtraBuscarEnServidor(nombre);  	  		
	    //else
	    //OcultarAjax();  	
	}


        function raw_agnadirPorCodigoBarras(vcb, reEntrar) {

            var vcb        = CleanCB(vcb);
            var codbar     = vcb.toUpperCase();
	    var modo       = id("rgModosTicket").value;
	    var encontrado = false;
            var estado     = false;

	    //Verifica si en Docuemento Pedidos
	    reEntrar       = ( modo=="pedidos" )? false:reEntrar;

	    //Servicio
	    if(productos[vcb].servicio || productos[vcb].ilimitado )
		modo = "pedidos";

	    //Valida Existencias
	    if(productos[vcb] && modo!="pedidos")
		if(productos[vcb].unidades == 0) return; 

	    //Validar unidades almacen y ticket
            if(ticket[vcb] && modo!="pedidos")
	    {
		var unidalma = parseFloat(productos[vcb].unidades);
		var unidtick = parseFloat(ticket[vcb].unidades+1);

		if(unidtick > unidalma) return; 
	    }

	    //Series...
	    if( modo != "pedidos" && productos[vcb].serie ){

		CEEP(vcb);
		//Intenta anhadirlo...
		return agnadirPorSeries(productos[vcb].serie,
					productos[vcb].producto,
					productos[vcb].unidades,
					vcb,1);
	    }
	    
	    //Intenta anhadirlo...
            encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(1) );
	    //NOTA: debe añadirse al listado para que lo puedan consultar.
	    CEEP(vcb);
            //Si lo ha encontrado, sera una buena idea mostrar el cb y su foto,si la hay..
	    setImagenProducto( vcb );
            //NOTA: no se añade entrada en productos
	}


        function raw_agnadirPorReferencia(referencia)	{
            var k,yaexiste;	
	    var modo       = id("rgModosTicket").value;
	    var precio     = 0;

            VaciarListadoProductos();

            for(var t=0;t<iprodCod;t++) 
	    {
		cod = prodCod[t];
		ref = productos[cod].referencia.toUpperCase(); 

		if( ref.indexOf( referencia ) != -1 )  
                    break;
		else
                    ref="";
            }
	    
            if (ref2code[ref]) 
	    {
		var productosRef = ref2code[ref].split(",");
		var p;
		
		for(var t=0;t<productosRef.length;t++) {
                    p = productosRef[t];
                    if (p)
		    {
			k      = productos[p];
			precio = (Local.TPV=='VC')? k.pvc:k.pvd;

			if (k) 
			{		
                            yaexiste = prodlist_cb[k.codigobarras];
                            if (!yaexiste)
				CrearEntradaEnProductos(k.producto,k.nombre,k.marca,k.color,k.talla,
							k.laboratorio,
							k.codigobarras,k.referencia,
							precio,k.pvemp,k.pvdoce,k.pvc,k.impuesto,
							k.unidades,k.costo,
							k.lote,k.vence,k.serie,k.menudeo,
							k.unidxcont,k.unid,k.cont,
							k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
							k.pvo,k.condventa,k.mproducto);
			}
                    }
		}
            }
	    else
		VaciarListadoProductos();

            if (  esOnlineBusquedas()  ) 
		ExtraBuscarEnServidorXRef(ref);
	}

        function row_cargarDetMProductoACarrito(IdMP,Id,IdCliente){

            var modo   = id("rgModosTicket").value;
	    var pstock = id("prevt-stock").getAttribute("checked");
	    var im     ='';
	    var mm     ='';
	    var mesadd ='';
	    var iadd   = 1;
            var labelCliente = id("tCliente");
            var nuevoNombreUsuario,iexd,cadena,filas,nSeries,xrequest,url;;

            UsuarioSeleccionado = IdCliente; 
            nuevoNombreUsuario  = usuarios[IdCliente].nombre;	

            labelCliente.setAttribute("label", nuevoNombreUsuario );

	    //Carga... 
	    xrequest = new XMLHttpRequest();
	    url      = 
		"services.php"+"?"+
		"modo=cargarDetMProductoACarritoTPV"+"&"+
		"id="+IdMP+"&"+
		"idcliente="+IdCliente+"&"+
		"idprod="+Id;

	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    //alert(xrequest.responseText);
	    cadena = xrequest.responseText;
	    filas  = cadena.split(";");

	    //Insertar
	    for(var i = 0; i<filas.length; i++)
	    {
		var precio=0,cantidad=0,acantidad=0,celdas='',vcb='';
		celdas    = filas[i].split(",");
		vcb       = CleanCB(celdas[1]);

		//Intenta anadir si no existe... 
		if (!productos[vcb]) ExtraBuscarEnServidorXCB(vcb);

		precio    = parseFloat(celdas[4]);
		cantidad  = parseFloat(celdas[2]);
		acantidad = ( productos[vcb] && productos[vcb].servicio )? cantidad:celdas[3];
		acantidad = ( productos[vcb] && productos[vcb].ilimitado )? cantidad:acantidad;
		acantidad = parseFloat( acantidad );
		iadd      = 1;
		im        = '';

		//Cantidad Excede 
		if( cantidad>acantidad && acantidad > 0 && pstock == "true")
		{ 
		    iexd     = cantidad - acantidad;
		    cantidad = acantidad;

		    im  = "\n     - Existe "+cantidad+" unidad(es) de este producto en almacén.";
		    im += "\n     - El pedido excede "+iexd+" unidad(es)";
		    im += "\n     [-] Producto atendido descontando el exceso.";
		}

		//Cantidad Cero
		if( acantidad <= 0 && pstock == "true")
		{
		    acantidad = 0;
		    iadd      = 0;

		    im  = "\n     - Existe "+acantidad+" unidad(es) de este producto en almacén.";
		    im += "\n     - Cantidad requerida "+cantidad+".";
		    im += "\n     [x] Producto no atendido.";
		}

		//Series... 
		if( productos[vcb].serie && iadd == 1 && pstock == "true" ) 
		{
		    im  = "\n     - Seleccione "+cantidad+" Número Serie de este producto.";
		    im += "\n     - Cantidad requerida "+cantidad+".";
		    im += "\n     [*] Producto con Número Serie.";

		    //filtros basico
		    aSeries  = ( celdas[10] != '0')? celdas[10]:0;
		    aSeries  = ( celdas[10] != '-NS')? celdas[10]:0;

		    //filtra cantidad y numero de series
		    if( aSeries != '0'){
			nSeries = aSeries.split("~");
                     	aSeries = ( nSeries.length == cantidad )? celdas[10]:0;			
		    }

		    //Carga cantidad despues de filtros
		    cantidad = ( aSeries )? cantidad:0;

		    //Existe Reservas...
		    if( aSeries ) {

			nSeries   = aSeries.split("~");
			acantidad = nSeries.length;
			iexd      = cantidad - acantidad;

			//Cantidad Execede...
			if( iexd != 0 )
			{
			    im  = "\n     - Cantidad requerida: "+cantidad+' '+productos[vcb].unid;
			    im += "\n     - El pedido excede: "+iexd+' '+productos[vcb].unid;
			    im += "\n     - Seleccione : "+iexd+' '+productos[vcb].unid;
			    im += "\n     [-] Producto atendido descontando el exceso.";
			    im += "\n     [*] Producto con Número Serie.";

			    mm  += "\n   Producto: "+productos[vcb].producto+" "+ im +"\n";
			}
			
			//Carga Carrito...
			setNSReservadasACarrito(vcb,aSeries,precio,0);
			//Salta siguiente...
			continue;
		    }
		}

		//Carga Carrito...
		if(vcb!='' && iadd == 1)
		    var esAdd = raw_agnadirMProductosPorCB(vcb,cantidad,precio,modo,pstock);

		//Guarda mensaje: producto no cargado
		if( !esAdd && pstock != "true" )
		    mesadd += "\n    *  "+celdas[9]+".";

		//Guarda mesanjes
		if(im!='')
		    mm  += "\n   Producto: "+productos[vcb].producto+" "+ im +"\n";
	    } 

	    //Add mensaje: Producto no cargados
	    if( mesadd!='' && pstock!="true" )
		mm = '\n\n  Realice una busqueda de los siguientes productos,'+
		' luego carge el - MProducto -  elegido. \n '+mesadd;  

	    //Lanza total mensaje
	    if(mm!='')
		alert (c_mproducto+ '\n' + mm);
	}


        function row_mostrardetalleMProducto(CBMP,MProducto){

	    var cadena, filas;
	    var xrequest = new XMLHttpRequest();
	    var url      =   
		"services.php"+"?"+
		"modo=mostrardetalleMProducto"+"&"+
		"cbmp="+CBMP;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    cadena = xrequest.responseText;

	    //lanza mesaje Resumen
	    if( trim(cadena) != '' )
		return "\n - "+ CBMP+" "+MProducto+" - \n"+cadena;
	    return false;
	}

        function row_cargarDetBaseMProductoACarrito(IdMP,Id){

            var modo   = id("rgModosTicket").value;
	    var pstock = id("prevt-stock").getAttribute("checked");
	    var im     = '';
	    var mm     = '';
	    var mesadd = '';
	    var iadd   = 1;
	    var iexd, cadena, filas, xrequest, url;

	    xrequest = new XMLHttpRequest();
	    url = 
		"services.php"+"?"+
		"modo=cargarDetBaseMProductoACarritoTPV"+"&"+
		"id="+IdMP+"&"+
		"idprod="+Id;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    cadena = xrequest.responseText;

	    filas  = cadena.split(";");

	    //Inserta
	    for(var i = 0; i<filas.length; i++)
	    {
		var precio=0,cantidad=0,acantidad=0,celdas='',vcb='';
		celdas    = filas[i].split(",");
 		vcb       = CleanCB(celdas[1]);

		//Intenta anadir si no existe... 
		if (!productos[vcb]) ExtraBuscarEnServidorXCB(vcb);

		precio    = obtenerPrecioBaseMProducto( productos[vcb].costo );
		//precio    = parseFloat(celdas[4]);
		cantidad  = parseFloat(celdas[2]);
		acantidad = parseFloat( cantidad );//Pre-MProducto
		iadd      = 1;
		im        ='';

		//Carga...
		if(vcb!='' && iadd == 1)
		    var esAdd = raw_agnadirMProductosPorCB(vcb,cantidad,precio,modo,pstock);

		//Mensaje...
		if( !esAdd && pstock != "true" )
		    mesadd += "\n    *  "+celdas[9]+".";

		//Mensaje...
		if(mesadd!=''&&pstock!="true")
		    mm = '\n\n  Realice una busqueda de los siguientes productos,'+
		    ' luego carge el - MProducto -  elegido. \n '+mesadd;  

		//Lanza Total Mensaje...
		if(im!='')
		    mm  += "\n   Producto: "+productos[vcb].producto+" "+ im +"\n";
	    } 

	    //Lanza Total Mesaje
	    if(mm!='')
		alert ( c_mproducto + mm);

	}


        function row_cargarDetPresupuestoACarrito(IdPresupuesto,TipoPresupuesto,IdCliente){

            var modo   = id("rgModosTicket").value;
	    var pstock = id("prevt-stock").getAttribute("checked");
	    var im     = '';
	    var mm     = '';
	    var mesadd = '';
	    var iexd   = 0;
	    var iadd   = 1;

	    //Inserta Cliente
            var labelCliente = id("tCliente");
	    var cadena,filas,nSeries,nuevoNombreUsuario;

            UsuarioSeleccionado = IdCliente; 
            nuevoNombreUsuario  = usuarios[IdCliente].nombre;	

            labelCliente.setAttribute("label", nuevoNombreUsuario );

	    //Carga Presupusto

 	    var z        = null;	    
	    var xrequest = new XMLHttpRequest();
	    var url      = 
		"services.php"+"?"+
		"modo=cargarDetPresupuestoACarritoTPV"+"&"+
		"id="+IdPresupuesto+"&"+
		"idcliente="+IdCliente+"&"+
		"tipo="+TipoPresupuesto;
	    xrequest.open("GET",url,false);

	    try {
		xrequest.send(null);
	    } catch(z){
		return;
	    }
	    cadena = xrequest.responseText;
	    filas  = cadena.split(";");

	    //Inserta...
	    for(var i = 0; i<filas.length; i++)
	    {
		var precio=0,concepto='',descuento=0,cantidad=0,acantidad=0,celdas='',vcb='';

		celdas    = filas[i].split(",");
		vcb       = CleanCB(celdas[1]);
		
		//Intenta anadir si no existe... 
		if (!productos[vcb]) ExtraBuscarEnServidorXCB(vcb);

		precio    = parseFloat(celdas[4]);
		concepto  = ( trim( celdas[8] ) != '')? celdas[8] : '';
		descuento = parseFloat(celdas[9]);
		cantidad  = parseFloat(celdas[2]);
		acantidad = ( productos[vcb] && productos[vcb].servicio )? cantidad:celdas[3];
		acantidad = ( productos[vcb] && productos[vcb].ilimitado )? cantidad:acantidad;
		acantidad = parseFloat( acantidad );
		iadd      = 1;
		im        = '';

		//Cantidad Excedente...
		if(cantidad>acantidad && acantidad > 0 && pstock == "true" )
		{ 
		    iexd     = cantidad - acantidad;
		    cantidad = acantidad;

		    im  = "\n     - Existe "+cantidad+" unidad(es) de este producto en almacén.";
		    im += "\n     - El pedido excede "+iexd+" unidad(es)";
		    im += "\n     [-] Producto atendido descontando el exceso.";
		}

		//Cantidad Cero...
		if( acantidad <= 0 && pstock == "true")
		{
		    acantidad = 0;
		    iadd      = 0;

		    im  = "\n     - Existe "+acantidad+" unidad(es) de este producto en almacén.";
		    im += "\n     - Cantidad requerida "+celdas[2]+".";
		    im += "\n     [x] Producto no atendido.";
		}

		//Series...
		if( productos[vcb] && productos[vcb].serie && iadd == 1 && pstock == "true") 
		{
		    im  = "\n     - Seleccione "+cantidad+" Número Serie de este producto.";
		    im += "\n     - Cantidad requerida "+celdas[2]+".";
		    im += "\n     [*] Producto con Número Serie.";

		    //filtros basico
		    aSeries  = ( celdas[10] != '0')? celdas[10]:0;
		    aSeries  = ( celdas[10] != '-NS')? celdas[10]:0;

		    //filtra cantidad y numero de series
		    if( aSeries != '0'){
			nSeries = aSeries.split("~");
                     	aSeries = ( nSeries.length == cantidad )? celdas[10]:0;			
		    }

		    //Carga cantidad despues de filtros
		    cantidad = ( aSeries )? cantidad:0;

		    //Reservas...
		    if( aSeries ) {

			nSeries   = aSeries.split("~");
			acantidad = nSeries.length;
			iexd      = cantidad - acantidad;

			//Execede...
			if( iexd != 0 )
			{
			    im  = "\n     - Cantidad requerida: "+celdas[2]+' '+productos[vcb].unid;
			    im += "\n     - El pedido excede: "+iexd+' '+productos[vcb].unid;
			    im += "\n     - Seleccione : "+iexd+' '+productos[vcb].unid;
			    im += "\n     [-] Producto atendido descontando el exceso.";
			    im += "\n     [*] Producto con Número Serie.";

			    mm  += "\n   Producto: "+productos[vcb].producto+" "+ im +"\n";
			}

			//Carga Carrito...
			setNSReservadasACarrito(vcb,aSeries,precio,descuento);
			//Salta siguiente...
			continue;
		    }
		}

		//Carrito...
		if(vcb!='' && iadd == 1)
		    var esAdd = raw_agnadirPresupuestosPorCB(vcb,cantidad,precio,
							     descuento,modo,
							     pstock,concepto);
 		//Mensaje...
		if( !esAdd && pstock != "true" )
		    mesadd  += "\n    *  "+productos[vcb].producto+".";

		//Mensaje...
		if(im!='')
		    mm  += "\n   Producto: "+productos[vcb].producto+" "+ im +"\n";
		
	    } 

	    //Mensaje...
	    if(mesadd!='' && pstock != "true")
		mm = '\n\n  Realice una busqueda de los siguientes productos,'+
		' luego carge la - '+TipoPresupuesto+' - elegida. \n '+mesadd;  

	    //LANZA MENSAJE
	    if(mm!='')
		alert (c_preventa + mm);
	}

        function raw_agnadirPresupuestosPorCB(vcb,cantidad,precio,descuento,modo,pstock,concepto) {

	    var esServicio,esSerie,reEntrar;
	    reEntrar   = (modo=="pedidos")? false:true;
            vcb        = CleanCB(vcb);
	    cantidad   = parseFloat(cantidad);
	    descuento  = parseFloat(descuento);
	    precio     = parseFloat(precio);
            codbar     = vcb.toUpperCase();
	    esServicio = agnadirLineaPresupuestoSubsidiario(vcb,precio,concepto,descuento);

	    //Valida respuesta ...
            if (!esServicio) return
	    
            //Intenta anhadirlo...
            var encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(cantidad) );
	    
	    //Validamos esta en la lista CB
            if (!encontrado){
		ExtraBuscarEnServidorXCB(vcb);
		encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(cantidad) );
	    }
	    
	    //Validamos si es un CB o NS
            if (!encontrado) return encontrado;
	    
	    //NOTA: debe añadirse al listado para que lo puedan consultar.
	    CEEP(vcb);

	    //Concepto
	    if( trim( concepto ) != '' ) ConceptoParaFilaPreventa(concepto,vcb);	    
	    
	    //Precio
	    ticket[vcb].precio = formatDinero(precio);
	    id("tic_precio_"+ vcb).value= ticket[vcb].precio;	
            id("tic_precio_"+ vcb).setAttribute("value",ticket[vcb].precio);	
            Blink("tic_precio_" + vcb, "label-precio" );

	    //Descuento
	    ticket[vcb].descuento = descuento;
	    id("tic_descuento_"+ vcb ).value=FormateComoDescuento(ticket[vcb].descuento);
            id("tic_descuento_"+ vcb ).setAttribute("value",FormateComoDescuento(ticket[vcb].descuento));
	    Blink("tic_descuento_" + vcb, "label-descuento" );	

	    //Actulaliza el consolidado carrito productos
            RecalculoTotal();
	    
            //NOTA: no se añade entrada en productos
	    return encontrado;
	}


        function raw_agnadirMProductosPorCB(vcb,cantidad,precio,modo,pstock) {
	    
	    var reEntrar = true;
	    var aSeries;
	    //Modo pedido
	    if(modo=="pedidos")
		reEntrar = false;
	    
	    //Limpiamos variables
            vcb        = CleanCB(vcb);
	    cantidad   = parseFloat(cantidad);
	    precio     = parseFloat(precio);
            codbar     = vcb.toUpperCase();
	    
            //Intenta anhadirlo...
            var encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(cantidad) );

	    //Lista extra...
            if (!encontrado)
	    {
		//Buscar...
		ExtraBuscarEnServidorXCB(vcb);
		//Intenta anhadirlo...
		encontrado = tpv.AddCarrito( vcb ,ConvertirSignoApropiado(cantidad) );
	    }

	    //Validamos esta en la lista CB
            if (!encontrado) return encontrado;
	    
	    //NOTA: debe añadirse al listado para que lo puedan consultar.
	    CEEP(vcb);
	    
	    //Precio
	    ticket[vcb].precio   = precio;
	    id("tic_precio_"+ vcb).value=formatDinero( ticket[vcb].precio );	
            id("tic_precio_"+ vcb).setAttribute("value",formatDinero( ticket[vcb].precio ));	
            Blink("tic_precio_" + vcb, "label-precio" );
	    
            //NOTA: no se añade entrada en productos
	    return encontrado;
	}


        /*++++++++++++ Generador ++++++++++++++*/

        function  generadorCargarMProducto(tipopresupuesto,add){

	    //alert('Generador: '+tipopresupuesto+" item:"+add);	    

	    //add new item preventa o proforma

 	    //Check Conecction
	    if(syncCheckConnection()) return;

	    var combo = id("items"+tipopresupuesto);
	    var cadena, filas;
	    var xrequest = new XMLHttpRequest();

	    //consigue listado 
	    var url = 
		"services.php"+"?"+
		"modo=syncMProductosTPV"+"&"+
		"Estado='Ensamblaje'";
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    //recibe listado
	    cadena = xrequest.responseText;

	    //separa item del listado
	    filas  = cadena.split(";");

	    //procesa item del listado
	    add = parseInt(add);

	    for(var h = 0; h<filas.length; h++){

		//separa campos del item 
		var celdas = filas[h].split(",");

		//ADD ALL ITEM
		if( add == 0 && celdas[0] !='' ){
		    var elemento = document.createElement('menuitem');
		    elemento.setAttribute('label',celdas[2]);
		    elemento.setAttribute('id',celdas[1]);
		    elemento.setAttribute('oncommand',
					  'cargarDetMProductoACarrito('+
					  celdas[0]+','+
					  celdas[1]+','+
					  celdas[4]+')');
		    combo.appendChild(elemento);
		    //inicia array
		    addToArrayMProductos(celdas[1],celdas[0],celdas[4],celdas[5]);
		}

		//ADD NEW ITEM
		if( add != 0 && add == celdas[1] ){
		    //alert('Preventa: '+aPreventa.toString()+'\nProforma: '+aProforma.toString());
		    var elemento = document.createElement('menuitem');
		    elemento.setAttribute('label',celdas[2]);
		    elemento.setAttribute('id',celdas[1]);
		    elemento.setAttribute('oncommand',
					  'cargarDetMProductoACarrito('+
					  celdas[0]+','+
					  celdas[1]+','+
					  celdas[4]+')');
		    combo.insertBefore(elemento,combo.childNodes[1]);
		    //Actualiza array
		    addToArrayMProductos(celdas[1],celdas[0],celdas[4],celdas[5]);
		}
	    }
	    //<menuitem label="Todos" selected="true"  />
	}

        function  generadorCargarPresupuesto(tipopresupuesto,add,sel){

 	    //Check Conecction
	    if(syncCheckConnection()) return;

	    mostrarMensajeTPV(1,'Cargando tickets '+tipopresupuesto+'...',3200);

	    //add new item preventa o proforma
	    if( sel == "cd" ) var iadd = 1;//Preventa
	    if( sel == "id" ) var iadd = 0;//Proforma

	    var combo = id("items"+tipopresupuesto);
	    var cadena, filas;
	    var xrequest = new XMLHttpRequest();

	    //consigue listado 
	    var url = 
		"services.php?"+
		"modo=syncPresupuestosTPV"+"&"+
		"tipopresupuesto="+tipopresupuesto;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    //recibe listado
	    cadena = xrequest.responseText;

	    //separa item del listado
	    filas  = cadena.split(";");

	    //procesa item del listado
	    add = parseInt(add);

	    for(var h = 0; h<filas.length; h++){

		//separa campos del item 
		var celdas = filas[h].split(",");

		//ADD ALL ITEM
		if( add == 0 && celdas[0] !='' ){
		    var elemento = document.createElement('menuitem');
		    elemento.setAttribute('label',celdas[2]);
		    elemento.setAttribute('id',celdas[0]);
		    var celda4 = '';
		    if(celdas[4] =='')	
			celdas[4] = 0;//No tiene CBMProductos
		    else 
		    {
			celda4 = celdas[4];
			celdas[4] = "'"+celdas[4]+"'";
		    }//Add comillas 
		    elemento.setAttribute('oncommand',
					  'cargarDetPresupuestoACarrito('+celdas[0]+','+celdas[3]+','+celdas[4]+')');
		    combo.appendChild(elemento);
		    //inicia array
		    addToArrayPresupuestos(tipopresupuesto,celdas[0],celdas[1],celdas[3],celda4);

		}

		//ADD NEW ITEM
		if( add != 0 && add == celdas[iadd] ){

		    var elemento = document.createElement('menuitem');
		    elemento.setAttribute('label',celdas[2]);
		    elemento.setAttribute('id',celdas[0]);
		    var celda4 = '';
		    if(celdas[4] =='')	
			celdas[4] = 0;//No tiene CBMProductos 
		    else 
		    {
			celda4 = celdas[4];
			celdas[4] = "'"+celdas[4]+"'";
		    }//Add comillas 
		    
		    elemento.setAttribute('oncommand',
			 		  'cargarDetPresupuestoACarrito('+celdas[0]+','+celdas[3]+','+celdas[4]+')');
		    combo.insertBefore(elemento,combo.childNodes[1]);
		    //Actualiza array
		    addToArrayPresupuestos(tipopresupuesto,celdas[0],celdas[1],celdas[3],celda4);
		}
	    }
	    //<menuitem label="Todos" selected="true"  />
	}

        function  generadorEliminaPresupuesto(tipopresupuesto,del){
	    var combo = id("items"+tipopresupuesto);
	    var item = id(del);

	    if(item){
		//Elimina item menulist
		combo.removeChild(item);
		//Actualiza array
		delToArrayPresupuestos(tipopresupuesto,del);
		//Reinicia combo
		if(parseInt(IdPresupuesto) == parseInt(del))
		    id("Sel"+tipopresupuesto).setAttribute("label", "Elije ticket....");
	    }
	}

        function  generadorEliminaMProducto(IdMP,Id){
	    var combo = id("itemsMProducto");
	    var item = id(Id);
	    if(item){

		//Elimina item menulist
		combo.removeChild(item);

		//Actualiza array
		delToArrayMProductos(Id);

		//Reinicia combo SI ESTA CARGADO 
		//if(parseInt(IdMProducto) == parseInt(Id))
		//    id("SelMProducto").setAttribute("label", "Elije ticket....");

	    }
	}


        /*++++++++++++++++  ADD/DEL  ++++++++++++++++++*/

        function addToArrayPresupuestos(tp,id,nro,cli,acbmp){
	    if( tp == 'Proforma' ) aProforma.push(id);
	    if( tp == 'ProformaOnline' ) aProformaOnline.push(id);
	    if( tp == 'Preventa' ) aPreventa.push(id);
	    aPedido.push(tp+':'+nro+':'+id+':'+cli+':'+acbmp);//Id,ProformaNro
	}

        function delMProductoToArrMP(nsmprod){
	    if(nsmprod!=''){
		var nsmp = nsmprod.split(',');
		for (var r in nsmp){ 
		    for (var u in ArrMP){
			var dsrt = ArrMP[u].split('~'); 
			if( dsrt[1] == UsuarioSeleccionado )
			    ArrMP.splice(u, 1);
		    }
		}
	    }
	}

        function addToArrayMProductos(nro,id,cli,vcb){
	    aMProductos.push(nro);
	    aPedido.push('MProducto:'+nro+':'+id+':'+cli+':'+vcb);//Id,ProformaNro
	}

        function delToArrayPresupuestos(tp,id){
 	    if( tp == 'Proforma' )
	    {
		for (var y=0; y<aProforma.length; y++) {
		    if(aProforma[y]==id){
			aProforma.splice(y, 1);
		    }
		}
 		//if(aProforma.length == 0) 
		//return;	    
	    }
 	    if( tp == 'ProformaOnline' )
	    {
		for (var y=0; y<aProformaOnline.length; y++) {
		    if(aProformaOnline[y]==id){
			aProformaOnline.splice(y, 1);
		    }
		}
 		//if(aProforma.length == 0) 
		//return;	    
	    }
	    if( tp == 'Preventa' )
	    {
		for (var x=0; x<aPreventa.length; x++) {
		    if(aPreventa[x]==id)
			aPreventa.splice(x, 1);
		}
		
		//if(aPreventa.length == 0) 
		//return;	    
	    }

	}

        function delToArrayMProductos(tp,id){
 	    if( tp == 'Proforma' )
	    {
		for (var y=0; y<aProforma.length; y++) {
		    if(aProforma[y]==id)
			aProforma.splice(y, 1);
		}
 		//if(aProforma.length == 0) 
		//return;	    
	    }
	    if( tp == 'Preventa' )
	    {
		for (var x=0; x<aPreventa.length; x++) {
		    if(aPreventa[x]==id)
			aPreventa.splice(x, 1);
		}
		
		//if(aPreventa.length == 0) 
		//return;	    
	    }
	}

        function delToArrayMProductos(id){
		for (var y=0; y<aMProductos.length; y++) {
		    if(aMProductos[y]==id)
			aMProductos.splice(y, 1);
		}
	}


        /*+++++++++++ Cargar +++++++++++++++*/

        function cargarDetPresupuestoACarrito(Id,IdCliente,mcb){

 	    //Variable global presupuesto
	    IdPresupuesto = Id;  

	    //reset modo
	    resetPresupuestoCarrito();

	    //Limpia listado producto
            VaciarListadoProductos();

 	    //Clientes
	    if(!usuarios[IdCliente])syncClientes();

	    switch( IdTipoPresupuesto ){

	    case 1:
 		//Set Label Combo
		id("SelPreventa").setAttribute("label",id(Id).label);

                //Ticket Preventa
		row_cargarDetPresupuestoACarrito(IdPresupuesto,
						 'Preventa',
						 IdCliente);
		break;

	    case 2:
 		//Set Label Combo
 		id("SelProforma").setAttribute("label",id(Id).label);

		//CB Meta Producto 
		if(mcb!=0)
		    mcb = mcb.replace("_",",");
		else 
		    mcb = '';
		id("serieMProducto").value = mcb;

		//Ticket Proforma
		row_cargarDetPresupuestoACarrito(IdPresupuesto,
						 'Proforma',
						 IdCliente);
		break;

	    case 3:
 		//Set Label Combo
 		id("SelProformaOnline").setAttribute("label",id(Id).label);

		//CB Meta Producto 
		if(mcb!=0)
		    mcb = mcb.replace("_",",");
		else 
		    mcb = '';
		id("serieMProducto").value = mcb;

		//Ticket Proforma
		row_cargarDetPresupuestoACarrito(IdPresupuesto,
						 'ProformaOnline',
						 IdCliente);
		break;

	    }
	    //Variable global presupuesto
	    //IdPresupuesto = 0;  

	}

        function cargarDetMProductoACarrito(Id,IdMP,IdCliente){

            //METAPRODUCTO*******************
 	    //Variable global mproducto
	    IdMetaProducto  = Id;  

 	    //Variable global MProducto
	    IdMProducto = IdMP;
	    
	    //reset label combo SelMProducto
            id("SelMProducto").setAttribute("label",id(IdMP).label);

	    //Limpia listado producto
            VaciarListadoProductos();

	    //reset modo
	    resetPresupuestoCarrito();

 	    //Sincorniza Clientes ******
	    if(!usuarios[IdCliente])
		syncClientes();

            //Ticket MProducto
	    row_cargarDetMProductoACarrito(Id,IdMP,IdCliente);

	}

        function CargarComboMProducto(){

	    //add item mproducto
	    esSyncBoton('on');//Boton Sync

	    //consigue cadena listado 
	    var combo = id("itemsBaseMProducto");
	    var cadena, filas;
	    var xrequest = new XMLHttpRequest();
	    var url = 
		"services.php"+"?"+
		"modo=cargarListaBaseMProductosTPV";
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    cadena = xrequest.responseText;
	    
	    //Separa item del listado de la cadena
	    filas  = cadena.split(";");

	    //Inserta item al combo MaseMProducto
	    for(var h = 0; h<filas.length; h++){

		//Obtiene los item 
		var celdas = filas[h].split(",");
		//Agrega todos los items
		if( celdas[0] !='' ){
		    var elemento = document.createElement('menuitem');
		    elemento.setAttribute('label',celdas[1]);
		    elemento.setAttribute('id',celdas[0]);
		    elemento.setAttribute('oncommand',
					  'cargarIdMProducto('+celdas[0]+',"'+celdas[1]+'","'+celdas[2]+'")');
		    combo.appendChild(elemento);
		}

	    }
	    //<menuitem label="Todos" selected="true"  />
	    esSyncBoton('pause');//Boton Sync
	}
        function obtenerPrecioBaseMProducto( xvalue ){
	    var xvalorventa  = parseFloat( xvalue + ( xvalue * Local.MPUtilidad ) /100 );
	    var xprecioventa = parseFloat( xvalorventa + ( xvalorventa * Local.Impuesto ) /100);
	    return xprecioventa;
	}
        function cargarIdMProducto(Id,label,margenutil){

	    //Margen util
	    cargarMargenUtilMproducto( margenutil );

 	    //Variable global MProducto
            id("SelBaseMProducto").setAttribute("label",label); //Meta Producto
	    
	    IdMProducto = Id;
	    
	    //Sin STOCK
	    StockMetaProducto = 1;

	    //Limpia lista productos
            VaciarListadoProductos();

	    //reset modo
	    resetPresupuestoCarrito();

	    //Cargar Base Detalle Meta Producto, Si existe
	    getIdDetBaseMProducto();
	}

        function cargarMargenUtilMproducto(margenutil){

	    var xmutil = margenutil.split('~~');
	    switch(Local.TPV )
	    {
	    case "VD":Local.MPUtilidad = (parseFloat( xmutil[0] )>0 )? xmutil[0]:Local.Utilidad;break;
	    case "VC":Local.MPUtilidad = (parseFloat( xmutil[1] )>0 )? xmutil[1]:Local.Utilidad;break;
	    }
	}

        function CargarMProducto(){

	    generadorCargarMProducto('MProducto','0');
	    return;
	}

        function CargarPresupuesto(tipopresupuesto){

	    generadorCargarPresupuesto(tipopresupuesto,'0','');
	    return;
	}

        /*++++++++++++++++++ SET/PON +++++++++++++++++++++*/

        function setImagenProducto( CodigoBarras ){
            if(!CodigoBarras){
		return;
            }

            if(Imageview.oldcb == CodigoBarras) return;

            Imageview.cb = CodigoBarras;

	    if( productos[ CodigoBarras ].imagen == '0') {

		id("muestraProductoCB").setAttribute("value",Imageview.cb);
		id("muestraProductoIcon").setAttribute("collapsed","false");
		id("muestraProducto").setAttribute("src", "img/gpos_imgdefault.png" );
		Imageview.oldcb = Imageview.cb;
		return;
	    }

            setTimeout("UpdateImageview()",50);

            //Resetea el ultimo cambio, de modo que de forma efectiva retrasa el siguiente
            Imageview.lastchange = (new Date()).getTime(); 
	}

        function setStatusPresupuesto(IdPresupuesto,Opcion){

	    var cadena, filas, resultado, Opcion;
	    var xrequest = new XMLHttpRequest();
	    var url = 
		"services.php"+"?"+
		"modo=setStatusPresupuestoTPV"+"&"+
		"id="+IdPresupuesto+"&"+
		"op="+Opcion;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);

	    resultado = xrequest.responseText;
	    //alert(resultado);
	    resultado = parseInt(resultado);
	    if (!resultado )	
		alert(c_gpos + po_error+"\n - Al actualizar Status Presupuesto");	
	    return;

	}

        function setStatusMProducto(IdMP,Opcion){

	    var cadena, filas, resultado, Opcion;
	    var xrequest = new XMLHttpRequest();
	    var url = 
		"services.php?"+
		"modo=setStatusMProductoTPV"+"&"+
		"id="+IdMP+"&"+
		"op="+Opcion;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);

	    resultado = xrequest.responseText;
	    //alert(resultado);
	    resultado = parseInt(resultado);
	    if (!resultado )	
		alert(c_gpos + po_error+"\n - Al actualizar status  MetaProducto");	
	    return;

	}

        function selTipoPresupuesto(selticket){

	    var t_comb  = id("onlistTicket");
	    var i_actu  = id("t_actual");
	    var i_prev  = id("t_preventa");
	    var i_prof  = id("t_proforma");
	    var i_profol= id("t_proformaonline");
	    var i_mpro  = id("t_mproducto");
	    var s_prev  = id("SelPreventa");
	    var s_prof  = id("SelProforma");
	    var s_profol= id("SelProformaOnline");
	    var s_mpro  = id("SelMProducto");
	    var p_stock = id("prevt-stock");
	    var r_mprod = id("rMProducto");
	    var r_pedid = id("rPedido");
	    var r_cesio = id("rCesion");
	    var r_venta = id("rVenta");
	    var modo    = id("rgModosTicket");	   
 	    var t_bpedi = id("buscapedido");
            
	    var modotpv,noreset=true;

	    //Default Variables Globales
	    IdTipoPresupuesto = 0;
	    IdPresupuesto     = 0;  
	    IdMProducto       = 0;  
	    IdMetaProducto    = 0;  
	    StockMetaProducto = 0;
            
	    //CONTROL
	    switch( modo.value ){
	    case 'venta':
	    case 'cesion':
		modotpv = 1;
		noreset = ( IdPresupuesto > 0 )? true:false;
 		break;
	    case 'pedidos': 
		//if( selticket == 2 )
		modotpv = 1;
		break;
	    case 'mproducto': 
		if( selticket == 4 ) 
		    modotpv = 1;
		break;
	    default:
		modotpv = 0;
		//selticket = 0;
	    }

	    //Valida MetaProductos Tickets
	    if( modotpv != 0 && selticket == 4 ) 
	    {
		modotpv = 1;
		modo.setAttribute('value','mproducto');
		r_mprod.setAttribute('selected','true');
		r_venta.removeAttribute('selected');
		r_cesio.removeAttribute('selected');
		r_pedid.removeAttribute('selected');
 		NuevoModo();
	    }

	    //ALERT
	    if( modotpv != 1 && selticket != 0){
		//DEFAULT
		var m_alert = 'CONTADO ó CREDITO';

		//PROFORMA
		if(selticket == 2)
		    m_alert = 'CONTADO, CREDITO ó PROFORMA';

		//METAPRODUCTO
		if(selticket == 4)
		    m_alert = 'MIXPRODUCTO';

		//LANZA MESAJE
		alert( c_gpos + '\n - Selecione el modo '+
		      '- '+m_alert+' - en el TPV.');
		selticket = 0;
	    }

	    //CONTOL COMBO TICKET
	    switch( selticket ){
		    
	    case 0:
		//Ticket Actual
		t_comb.label="TICKET ACTUAL";
		i_prof.setAttribute('checked', 'false');
		i_profol.setAttribute('checked', 'false');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'true');

		//Oculta listas
		s_prev.setAttribute("collapsed", "true");
		s_prof.setAttribute("collapsed", "true");
		s_profol.setAttribute("collapsed", "true");
		s_mpro.setAttribute("collapsed", "true");
		t_bpedi.setAttribute("collapsed", "true");

		//Oculta check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "true");

		//Limpia Lista proformas
		s_prof.selectedItem=2;
		s_prof.setAttribute("label", "Elije ticket....");

		//Limpia Lista proformas online
		s_profol.selectedItem=2;
		s_profol.setAttribute("label", "Elije ticket....");
		
		//Limpia Lista preventas
		s_prev.selectedItem=0;
		s_prev.setAttribute("label", "Elije ticket....");

		//Muestra Controles
		r_mprod.setAttribute("collapsed", "false");
		r_pedid.setAttribute("collapsed", "false");

		//Default Variables Globales
		//IdTipoPresupuesto = 0;
		//IdPresupuesto     = 0;  
		//IdMProducto       = 0;  
		//IdMetaProducto    = 0;  
		//StockMetaProducto = 0;
		//reset modo
		AjustarEtiquetaMetaproducto();

		if( noreset )
		    resetPresupuestoCarrito();

		break;

	    case 1:
		//Sync
		if( Local.esSyncPreventa )
		    setTimeout("runsyncTPV('Preventa')",200);

                //Ticket Preventa
		t_comb.label="TICKET PREVENTA";

		i_prof.setAttribute('checked', 'false');
		i_profol.setAttribute('checked', 'false');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'true');
		i_actu.setAttribute('checked', 'false');

		//Limpia Lista
		s_prev.selectedItem=0;
		s_prev.setAttribute("label", "Elije ticket....");
		
		//Oculta listas
		s_prof.setAttribute("collapsed", "true");
		s_profol.setAttribute("collapsed", "true");
		s_prev.setAttribute("collapsed", "false");
		s_mpro.setAttribute("collapsed", "true");

		//Oculta Controles
		r_mprod.setAttribute("collapsed", "true");
		r_pedid.setAttribute("collapsed", "false");

		//Busqueda de nro pedidos
		t_bpedi.setAttribute("collapsed", "false");

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//set tipo presupuesto
		IdTipoPresupuesto = 1;

		//reset modo
		resetPresupuestoCarrito();
 		break;

	    case 2:
		//Sync
		if( Local.esSyncProforma )
		    setTimeout("runsyncTPV('Proforma')",200);

		//MENU Ticket Proforma
		t_comb.label="TICKET PROFORMA";
		i_prof.setAttribute('checked', 'true');
		i_profol.setAttribute('checked', 'false');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'false');

		s_prof.setAttribute("collapsed", "false");
		s_profol.setAttribute("collapsed", "true");
		s_prev.setAttribute("collapsed", "true");
		s_mpro.setAttribute("collapsed", "true");

		//Oculta Controles
		r_mprod.setAttribute("collapsed", "true");
		r_pedid.setAttribute("collapsed", "false");

		//Busqueda de nro pedidos
		t_bpedi.setAttribute("collapsed", "false");

		//Limpia Lista
		s_prof.selectedItem=0;
		s_prof.setAttribute("label", "Elije ticket....");

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//set tipo presupuesto
		IdTipoPresupuesto = 2;

		//reset modo
		resetPresupuestoCarrito();
		break;
	    case 3:
		//Sync
		if( Local.esSyncProOnline )
		    setTimeout("runsyncTPV('ProformaOnline')",200);

		//MENU Ticket OnLine
		t_comb.label="TICKET ONLINE";
		i_profol.setAttribute('checked', 'true');
		i_prof.setAttribute('checked', 'false');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'false');
 
		s_profol.setAttribute("collapsed", "false");
		s_prof.setAttribute("collapsed", "true");
		s_prev.setAttribute("collapsed", "true");
		s_mpro.setAttribute("collapsed", "true");

		//Oculta Controles
		r_mprod.setAttribute("collapsed", "true");
		r_pedid.setAttribute("collapsed", "false");

		//Busqueda de nro pedidos
		t_bpedi.setAttribute("collapsed", "false");

		//Limpia Lista online
		s_profol.selectedItem=0;
		s_profol.setAttribute("label", "Elije ticket....");

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//set tipo presupuesto
		IdTipoPresupuesto = 3;

		//reset modo
		resetPresupuestoCarrito();

		break;
	    case 4:
		//Sync
		if( Local.esSyncMProducto )
		    setTimeout("runsyncTPV('MProductos')",200)

		//MENU Ticket MProducto
		t_comb.label="TICKET MIXPRODUCTO";
		i_mpro.setAttribute('checked', 'true');
		i_prof.setAttribute('checked', 'false');
		i_profol.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'false');

		s_prof.setAttribute("collapsed", "true");
		s_profol.setAttribute("collapsed", "true");
		s_prev.setAttribute("collapsed", "true");
		s_mpro.setAttribute("collapsed", "false");

		//Limpia Lista
		s_mpro.selectedItem=0;
		s_mpro.setAttribute("label", "Elije ticket....");

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//Busqueda de nro pedidos
		t_bpedi.setAttribute("collapsed", "false");

		//set tipo presupuesto
		IdTipoPresupuesto = 4;

		//reset modo
		resetPresupuestoCarrito();
		AjustarEtiquetaMetaproducto();

		break;
	    }
	}


        function ponMPparaPedido(){

	    //IdProducto~IdUsuario~CBMP_CB_DETALLEMP_COSTOTOTAL
	    //id("serieMProducto").setAttribute('value',' ');
	    id("serieMProducto").removeAttribute('readonly');
	    
	    if(ArrMP.length<1) return;
	    var asrt = new Array();//Productos
	    //CADENA 
	    for (var k in ArrMP){
		var srt = ArrMP[k].split('~'); 
		if(asrt[srt[1]])
		    asrt[srt[1]] += '~'+srt[0]+';'+srt[2];
		else 
		    asrt[srt[1]] = srt[1]+':'+srt[0]+';'+srt[2];
	    }
	    //PRODUCTO Y CLIENTE
	    for (var k in asrt){
		var prod = new Array();
		var nseries = Array();
		var atotal  = Array();
		var acb     = Array();
		var aprod   = Array();
		var t_prod ='';
		var m_m ='';
		var m_esadd ='';
		var csr  = asrt[k].split(':');//Separa cliente de mprodutos 
		var srt  = csr[1].split('~');//Separa idproducto y series 
		//ARREGLO PRODUCTOS
		for (var v in srt){
		    var sr  = srt[v].split(';'); 
		    if(prod[sr[0]])
			prod[sr[0]] += ';'+sr[1];
		    else 
			prod[sr[0]] = sr[0]+'~'+sr[1];
		}
		//PROCESA POR PRODUCTO
		for (var g in prod){
		    var psr  = prod[g].split('~');
		    var ser  = psr[1].split(';');
		    //SERIE TOTAL
		    for (var s in ser){ 
			var aser  = ser[s].split('_');
			nseries.push(aser[0]);//Numero de series
			var costo = aser[2].replace(cMoneda[1]['S'],"");
			atotal.push(aser[2]);
 			var mpdet = aser[1].split(' ');
			acb.push(mpdet[0]); //guarda CB 

			if(productos[mpdet[0]])
			    productos[mpdet[0]].costo = costo;
			t_prod += "\n   - "+aser[1]+
		            "\n     Costo: "+cMoneda[1]['S']+" "+costo+
		            "\n     NS: "+aser[0]+"\n"; 
		    }
		}
		//LANZA PREGUNTA?
		var nuevoNombreUsuario = usuarios[csr[0]].nombre;	
		var labelCliente = id("tCliente");
		var smp = id("serieMProducto");
		if(confirm(c_gpos + " TPV MIXPRODUCTOS \n"+
			   "\n Cliente  : "+nuevoNombreUsuario+
			   "\n\n MProducto(s) : \n" +t_prod+
			   "\n                                    "+
			   " Cargar MProducto(s) al Pedido? ") ){
		    //INSERTA CLIENTE
		    UsuarioSeleccionado = csr[0]; 
		    labelCliente.setAttribute("label", nuevoNombreUsuario );
		    //INSERTA SERIE METAPRODUCTO
		    //smp.setAttribute('value',nseries.toString());
		    smp.value = nseries.toString();
		    smp.setAttribute('readonly',true);
		    //VACIA LISTADO
		    VaciarListadoTickets();
		    //INSERTA METAPRODUCTO	    
		    for (var p in acb){ 
			var vcb = acb[p];
 			//var tvcb = id("tic_" + vcb);
			//if (!tvcb)
			//raw_agnadirPorCodigoBarras(vcb);//Agnade Item
			
			//Tenemos este producto listado?
			if (!pool.Existe( vcb.toUpperCase() ))
			{
			    //Intenta anhadirlo...
			    ExtraBuscarEnServidorXCB(vcb);
			    //Existe...
			    if(!productos[vcb]) return; 
			}
			//Encuentra, 
			raw_agnadirPorCodigoBarras(vcb);
		    }

		    return;//Termina lista
		    //var dasrt = new Array();//Productos
		} 
	    }
	}
 
        function getTipoPresupuesto(IdTipoPresupuesto){


	    switch( IdTipoPresupuesto ){
	    case 1:
		var tipopresupuesto = 'Preventa'; 
		break;
	    case 2:
		var tipopresupuesto = 'Proforma'; 
		break;
	    case 3:
		var tipopresupuesto = 'ProformaOnline'; 
		break;
	    case 4:
	    case 5:
		var tipopresupuesto = 'MProducto'; 
		break;
	    }

	    if( IdTipoPresupuesto != '0' ) 
		return tipopresupuesto;
	    else
		return false;
	}

        function setNSReservadasACarrito(vcb,aSeries,precio,descuento){

	    var arSeries,xunidades;
	    var arSeries = Array(); 
	    var pSeries  = Array();
	    var srt      = '';
	    var xSeries  = '';

	    //Series...
	    arSeries = aSeries.split("~");

	    //NOTA: debe añadirse al listado para que lo puedan consultar.
	    CEEP(vcb);
	    
	    //Add Carrito...
	    tpv.AddCarrito( vcb.toUpperCase() , arSeries.length);
	    
	    //Add Carrito Serie...
	    for( var xns=0; xns < arSeries.length; xns++)
	    {
	 	tpv.AddCarritoSerie( vcb.toUpperCase() , arSeries[xns] );
	    }
	    
	    //Unidades...
	    xunidades = ticket[vcb].series.length;
	    ticket[vcb].unidades        = xunidades;
	    id("tic_unid_" + vcb).value = ticket[vcb].unidades;


	    //Precio...
	    ticket[vcb].precio   = precio;
	    id("tic_precio_"+ vcb).value = formatDinero( ticket[vcb].precio  );	
            id("tic_precio_"+ vcb).setAttribute("value",formatDinero( ticket[vcb].precio  ));	
            Blink("tic_precio_" + vcb, "label-precio" );

	    //Descuento...
	    ticket[vcb].descuento = descuento;
	    id("tic_descuento_"+vcb).value = FormateComoDescuento(ticket[vcb].descuento);
	    id("tic_descuento_"+vcb).setAttribute("value",FormateComoDescuento(ticket[vcb].descuento));
	    Blink("tic_descuento_" + vcb, "label-descuento" );	

	    //Detalle...
	    for(var j=0; j < ticket[vcb].series.length; j++)
	    {
		pSeries = ticket[vcb].series[j].split(":");
		xSeries = xSeries+srt+pSeries[1];
		srt     = ',';
	    }
	    
	    srt      = ( xSeries.length >20)? '...':'';
	    vdetalle = ' NS.'+xSeries.slice(0,20)+srt;
	    
	    id("tic_detalle_"+vcb).value = vdetalle;
	    id("tic_status_"+vcb).value  = '1~0~0';
	    CargarPedidoDetFila(vcb,ticket[vcb].unidades );//Pedido Detalle...	    
	    RecalculoTotal();
	}

        function getIdDetBaseMProducto(){

	    var cadena, datos,labelCliente,nuevoNombreUsuario;
	    var xrequest = new XMLHttpRequest();

	    //Listado 
	    var url = 
		"services.php?"+
		"modo=cargarIdBaseMProducto"+"&"+
		"Id="+IdMProducto;
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    datos = xrequest.responseText.split(";");

	    if( parseInt(datos[0]) > 0 && parseInt(datos[1]) > 0 ){

		labelCliente        = id("tCliente");
		UsuarioSeleccionado = 1; 
		nuevoNombreUsuario  = usuarios[1].nombre;

		//Carga...
		row_cargarDetBaseMProductoACarrito(datos[0],datos[1]);
		labelCliente.setAttribute("label", nuevoNombreUsuario );
	    }
	    return;
	}

        /*+++++++++++++++++++ OTROS +++++++++++*/

        function lanzarFichaTecnica(){

	    var cod = getCodigoSelectedProd();
	    if(cod == null) return;

            if(!trim(productos[cod].dosis)){
                var url       = "services.php?modo=verificaProductoInformacion&"+
	                        "xidp="+productos[cod].idproducto;
 	        var z         = null;
	        var xrequest  = new XMLHttpRequest();
                
	        xrequest.open("GET",url,false);
	        try {
	            xrequest.send(null);
	        } catch(z){
	            return;
	        }
	        var xres   = xrequest.responseText;
                if(!xres) return;

                productos[cod].dosis = xres.split('&');
            }
            
	    if (!trim(productos[cod].dosis) ) return;
            
	    var cfichaTecnica = productos[cod].dosis;
	    var esBTCA        = (Local.Giro=='BTCA');
	    var titleFicha    = '';
	    var mm            = '';
	    var tm            = '';
	    var im            = '';

            for( var j=0; j < cfichaTecnica.length; j++)
	    {
		//Titulo...
		switch(j)
		{
		 case 0: titleFicha = (esBTCA)? 'Indicaciones':'Propiedades Distintivas'; break;
		 case 1: titleFicha = (esBTCA)? 'Contra-Indicaciones':'Advertencias'; break;
		 case 2: titleFicha = (esBTCA)? 'Interacción':'Compatibilidad'; break;
		 case 3: titleFicha = (esBTCA)? 'Dosificación':'Modo de Uso'; break;
		}
		tm = titleFicha;
		//Detalle
		var aitemFicha = cfichaTecnica[j].split(";");

		for( var k=0; k < aitemFicha.length; k++)
		{
		    if( trim( aitemFicha[k] ) != '' ) im += '\n      - ' + aitemFicha[k]+'.';
		}
		
		if(trim(im)) mm += '\n\n    '+ tm + ':'+ im;
		im  = '';
		tm  = ''; 
	    }
	    
	    alert( 'gPOS:  FICHA TECNICA  \n\n' +
		   '  Producto: ' + productos[cod].producto + mm );
	}

        function ConceptoParaFila(){

	    var cod = getCodigoSelectedTicket();
	    if(cod == null) return;


	    var xconcepto = prompt('gPOS:  MODIFICAR CONCEPTO    \n\n'+
				   'Producto: ' + productos[cod].producto+'.\n\n',
				   trim( productos[cod].nombre+' '+
					 productos[cod].marca+' '+
					 productos[cod].color+' '+
					 productos[cod].talla+' '+
					 productos[cod].laboratorio ) );

	    if( trim( xconcepto ) == '' || !xconcepto ) return;

	    //xconcepto = xconcepto.toUpperCase();
	    ticket[cod].concepto = xconcepto;

	    id("tic_nombre_"+cod).setAttribute('value', ticket[cod].concepto); 
	    id("tic_concepto_"+cod).setAttribute('value', ticket[cod].concepto); 
	} 


         function ConceptoParaFilaPreventa( xconcepto,vcb ){
 	    //xconcepto = xconcepto.toUpperCase();
	    ticket[vcb].concepto = xconcepto;

	    id("tic_nombre_"+vcb).setAttribute('value',ticket[vcb].concepto); 
	    id("tic_concepto_"+vcb).setAttribute('value',ticket[vcb].concepto); 
	} 

        function lanzarRegistroBorrador(){

	    var xconcepto = prompt('gPOS:  ANOTAR NUEVO PRODUCTO\n\n'+
				   ' Producto: ','');
	    var xrequest  = new XMLHttpRequest();

	    if( trim( xconcepto ) == '' || xconcepto == null ) return;

	    xconcepto = xconcepto.toUpperCase();

	    var url = 
		"services.php?"+
		"modo=registraProductoBorrador"+
		"&concepto="+xconcepto+
		"&dependiente="+Local.IdDependiente;
	       
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    xres = xrequest.responseText;

	    if(parseInt(xres))
		alert('gPOS:  ANOTADO NUEVO PRODUCTO\n\n Producto:  '+xconcepto);
	}

        function menuContextualPreVentaTPV(xlisttpv){

	    id("preventaFichaTecnica").setAttribute("collapsed",true);
	    id("preventaMayoreoEmpaque").setAttribute("collapsed",true);
	    //id("preventaMayoreoDocena").setAttribute("collapsed",true);
	    id("preventaDetalleMProducto").setAttribute("collapsed",true);
	    id("preventaNumerosSeries").setAttribute("collapsed",true);
	    id("preventaCorporativo").setAttribute("collapsed",true);
	    id("viewpreventaCorporativo").setAttribute("collapsed",true);
	    id("viewpreventaCostos").setAttribute("collapsed",true);
	    id("ticketModificarPrecio").setAttribute("collapsed",true);
	    id("ticketModificarImporte").setAttribute("collapsed",true);

	    habilitaAgnadirMenoreo(true);

	    if ( Local.esB2B == 1 ) id("viewpreventaCorporativo").setAttribute("collapsed",false);
	    if ( Local.esAdmin ) id("viewpreventaCostos").setAttribute("collapsed",false);
	    
	    var cod  = (xlisttpv)? getCodigoSelectedTicket():getCodigoSelectedProd();
	    if(cod == null) return;

	    if ( productos[cod].serie ) id("preventaNumerosSeries").setAttribute("collapsed",false);
	    if ( productos[cod].menudeo ) id("preventaMayoreoEmpaque").setAttribute("collapsed",false)
	    //if ( productos[cod].menudeo ) id("preventaMayoreoDocena").setAttribute("collapsed",false)
	    if ( productos[cod].mproducto ) id("preventaDetalleMProducto").setAttribute("collapsed",false);
	    if ( productos[cod].dosis ) id("preventaFichaTecnica").setAttribute("collapsed",false);
	    if ( Local.esB2B == 1 ) id("preventaCorporativo").setAttribute("collapsed",false);

	    if ( Local.esPrecios ) id("ticketModificarPrecio").setAttribute("collapsed",false);
	    if ( Local.esPrecios ) id("ticketModificarImporte").setAttribute("collapsed",false);
	    
	    if ( productos[cod].unid =='mts') habilitaAgnadirMenoreo(false);
	    if ( productos[cod].unid =='lts') habilitaAgnadirMenoreo(false);
	    if ( productos[cod].unid =='kls') habilitaAgnadirMenoreo(false);  
            
            //Si lo ha encontrado, sera una buena idea mostrar el cb y su foto,si la hay..
	    //setImagenProducto( cod );
            //setTimeout("UpdateImageview()",50);

            setTimeout("setImagenProducto("+cod+")",200);
        }
        function habilitaAgnadirMenoreo(xsetcoll){
	    id("agnadirMenoreoPorCuarto").setAttribute("collapsed",xsetcoll);
	    id("agnadirMenoreoPorMedio").setAttribute("collapsed",xsetcoll);
	}

/*+++++++++++++++++++++++++++++ SERVICES ++++++++++++++++++++++++++++++++++*/


 
    var impuesto_normal = Local.Impuesto;//TODO: impuesto de subsidiarios
    var arreglosprenda  = new Array();

    function agnadirLineaSubsidiario() {

        var aque          = id("arregloDescripcion");
        var aquien        = id("arregloSubsidiario");
        var acuanto       = id("precioServicio");
        var ticketcodigo  = getCodigoSelectedTicket();

        if (!aquien.value || !ticketcodigo) return;

        var aquiename     = aquien.label;		
        var asubsidiario  = aquien.value;
        var aservicio     = aque.value;

        if (!aquiename || !asubsidiario || ! aservicio) return;

        if (!arreglosprenda[ticketcodigo]) arreglosprenda[ticketcodigo] = 0;
        arreglosprenda[ticketcodigo]++;

        var numeroServicio = arreglosprenda[ticketcodigo];	
        var arregloid      = ticketcodigo+"."+asubsidiario+"."+numeroServicio;
        var arregloref     = "SRV"+asubsidiario+"N"+numeroServicio;

        tpv.AddServicio(ticketcodigo,arregloid,aque.value,arregloref,acuanto.value,
			impuesto_normal,asubsidiario);

        ticket[ arregloid ].pedidodet = 'servicio';
        ticket[ arregloid ].nombre    =  arregloid+' '+aque.value;
        
        id("tic_pedidodet_"+ arregloid ).setAttribute("value",'servicio');
        id("tic_nombre_"+ arregloid ).setAttribute("value",arregloid+' '+aque.value);

        OcultarDialogoServicios();
        LimpiarSubVentanaServicio();		
        RecalculoTotal();
    }

    function ActualizarListaServicio(){

        var aservicio = id("arregloDescripcion").value;
	var lista     = id("itemsServicio");
	var url       = "services.php?modo=checkServicio&"+
	                "xservicio="+aservicio;
 	var z         = null;	    
	var xrequest  = new XMLHttpRequest();

	xrequest.open("GET",url,false);
	try {
	    xrequest.send(null);
	} catch(z){
	    return;
	}
	var xres   = xrequest.responseText;
	var xid    = xres.split(":");

	if(xid[0]=='0') return;

	var xServicio = document.createElement("menuitem");
	xServicio.setAttribute("label",aservicio);
	xServicio.setAttribute("class","media");
	xServicio.setAttribute("value",xid[1]);
	lista.appendChild( xServicio );		
    }

    function agnadirLineaPresupuestoSubsidiario(vcb,precio,servicio,descuento) {

	//90613614.1.1 =  CB.IDSUB.NUMSERV
	var aSRV         = vcb.split('.');

	if(!aSRV[1]) return true;

        var acuanto       = formatDinero(precio);
        var ticketcodigo  = aSRV[0];
        var asubsidiario  = aSRV[1];

        if (!arreglosprenda[ticketcodigo]) arreglosprenda[ticketcodigo] = 0;
        arreglosprenda[ticketcodigo]++;

        var numeroServicio = arreglosprenda[ticketcodigo];	
        var arregloid      = ticketcodigo+"."+asubsidiario+"."+numeroServicio;
        var arregloref     = "SRV"+asubsidiario+"N"+numeroServicio;

        tpv.AddServicio(ticketcodigo,arregloid,servicio,arregloref,acuanto,
			impuesto_normal,asubsidiario);

	ticket[arregloid].nombre    = arregloid+' '+servicio;
	ticket[arregloid].pedidodet = 'servicio-externo';
	ticket[arregloid].descuento = descuento;
	
        id("tic_nombre_"+ arregloid ).setAttribute("value",ticket[arregloid].nombre);
        id("tic_pedidodet_"+ arregloid ).setAttribute("value",ticket[arregloid].pedidodet);
        id("tic_descuento_"+ arregloid ).setAttribute("value",FormateComoDescuento(ticket[arregloid].descuento));
        Blink("tic_descuento_" + arregloid, "label-descuento" );	

        RecalculoTotal();
	return false;
    }

    function CancelarServicio() {  
        LimpiarSubVentanaServicio();
        OcultarDialogoServicios();
    }


    function MostrarDialogoServicios() {
        document.getElementById("modoVisual").setAttribute("selectedIndex",1);
    }

    function MostrarDialogoSeries() {
        document.getElementById("modoVisual").setAttribute("selectedIndex",12);
    }

    function OcultarDialogoServicios() {
        document.getElementById("modoVisual").setAttribute("selectedIndex",0);
    }


    function ServicioParaFila() {

        var ticketcodigo = getCodigoSelectedTicket();
        if (!ticketcodigo) return;

	if(id("tic_subsidiario_"+ ticketcodigo)) return;

        LimpiarSubVentanaServicio();
        MostrarDialogoServicios();
    }

    function LimpiarSubVentanaServicio(){
        id("arregloDescripcion").value = po_Elige;
        //id("arregloSubsidiario").setAttribute("label",po_Elige);
        id("precioServicio").value = "0.00";
    }

    //------------------------------------------------------
    //Numero de Serie

function agnadirPorSeries(xseries,xproducto,xcantidad,xcod,xunidades){

	id("selCB").value = xcod;
	limpiarlistaserie();
        listarseries(xseries,xunidades);	

	id("nsProducto").setAttribute("label",xproducto);
	id("totalNS").setAttribute("label",xcantidad);
	id("nsTitulo").setAttribute("label","Carrito TPV - Elegir Stock");

        MostrarDialogoSeries();
	id("ckserie").focus();
    }

function listarseries(xseries,xunidades){

	var xcod    = id("selCB").value;
	//var aseries = xseries.split(",");
	var theList = document.getElementById('listaseries_tpv');
	var esCart  = false;
	var aseries = Array();
	var pseries = Array();
	var xnscart = (ticket[xcod])? ticket[xcod].series.length:0;
	var idpedidodet = 0;
 	//return alert(xseries.toString());
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
		
		esCart = ckSerie2Carrito(aseries[i]);
		
		row.setAttribute('type','checkbox');
		row.setAttribute('label',aseries[i]);
		row.setAttribute('value',pseries[0]);
		row.setAttribute('oncommand',"cargarSerie2Carrito('"+aseries[i]+"',this,false)");
		row.setAttribute('checked',esCart);
		theList.appendChild(row);		    
	    }
	}	    
	id("totalSelNS").setAttribute('label',xnscart);
	id("totalSelNSDef").value = xunidades;
    }

    function limpiarlistaserie(){
	var lista  = id('listaseries_tpv');
	var i        = lista.itemCount-1;
        for(i;i>=0;i--)
        {
            lista.removeItemAt(i);
	}
    }

    function ckSerie2Carrito(xSerie){

	var xcod     = id("selCB").value;
	var xSeries  = ( ticket[xcod] )? ticket[xcod].series:'';
	var aSeries  = xSeries.toString();
	var arSeries = ( ticket[xcod] )? aSeries.split(","):Array();
	var pSeries  = Array();

	for( var xns=0; xns < arSeries.length; xns++)
	{
	    pSeries = arSeries[xns].split(":");
	    
	    if( pSeries[1] == xSerie )
		return true;

	}
	return false;
    }

    function selcKBoxSerie(){
	
	var radio_group = id("radio_group");

	switch(radio_group.selectedItem.label)
	{
	case "Buscar": 

            var lista  = id("listaseries_tpv");
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
            alert( c_gpos + "\n\n No se encuentra NS: "+nserie);
            break;
	}
    }

    function cargarSerie2Carrito(xdato,thisck,xdonde){

	var vckValue   = thisck.getAttribute('checked');
	var xpedidodet = thisck.value;
	var vydonde    = ( vckValue != 'false' )? false:true;
	var vckValue   = ( xdonde   )? vydonde:vckValue;	
	var vxdonde    = ( vckValue )? true:false;
	var xcod       = id("selCB").value;
        var fila       = id("tic_" + xcod);
	var xunidades  = 0;
	var xlistserie = productos[xcod].unidades;
 	var xcartserie = (ticket[xcod])? ticket[xcod].series.length+parseInt(1):0;
	var xSeries    = ''; 
	var pSeries    = Array();
	var vdetalle   = false;
	var srt        = '';

	//Disponible...
	if( vckValue && (xlistserie < xcartserie ))  
	{
	    thisck.setAttribute('checked',false);
	    return alert( c_stockalmacen + productos[xcod].producto +
			  "\n\n * Las Series Seleccionadas - "+ xcartserie +
			  " - excede a - "+xlistserie+ " - disponibles.");
	}

	//textbox
	if(xdonde) thisck.setAttribute('checked',vxdonde);

	//Add Carrito
        if (!ticket[xcod]) tpv.AddCarrito( xcod.toUpperCase() , xunidades);
        
	//Add Carrito Serie
	if(vckValue) tpv.AddCarritoSerie(xcod.toUpperCase() , xpedidodet+':'+xdato);
	
	//Del Carrito Serie
	if(!vckValue) tpv.DelCarritoSerie(xcod.toUpperCase() , xpedidodet+':'+xdato);
	
	//Stock
	xunidades = (ticket[xcod])? ticket[xcod].series.length:0; 
	id("totalSelNS").setAttribute('label',xunidades);

	//Stock 0 Carrito 
	if( xunidades == 0 )
	{
	    limpiarSelectCart();
            fila.setAttribute("selected",true);
            fila.setAttribute("current",true);
	    QuitarArticulo();
	    return limpiarSelectCart();
	}

	//Stock Carrito
	ticket[xcod].unidades        = xunidades;
	id("tic_unid_" + xcod).value = ticket[xcod].unidades;

	//Pedido Detalle...
	if (ticket[xcod]) CargarPedidoDetFila(xcod,ticket[xcod].unidades );

	//Detalle...
	for(var j=0; j < ticket[xcod].series.length; j++)
	{
	    pSeries = ticket[xcod].series[j].split(":");
	    xSeries = xSeries+srt+pSeries[1];
	    srt     = ',';
	}

	srt      = ( xSeries.length >20)? '...':'';
	vdetalle = ' NS.'+xSeries.slice(0,20)+srt;

        id("tic_detalle_"+xcod).value = vdetalle;
	id("tic_status_"+xcod).value  = '1~0~0';
        RecalculoTotal();
        //Pregunta si ya eligio todo

        //Unidades Elegidas
        if( id("totalSelNSDef").value == xunidades ) return VerTPV();
        //Todas las Unidades Seleccionadas
        if( productos[xcod].serie      == xunidades ) 
            if(confirm('gPOS TPV:  \n\n'+
                       '  Número de Serie Seleccionados : '+xunidades+'\n\n'+
	               '  ¿Desea regresar a TPV ?'))
                return VerTPV();
    }

    function limpiar_cajackbox(){
        var ns   = id("ckserie");
        ns.value = "";
        ns.focus();
    }

    function QuitarArticulo() {
        var t,codigo;	
        var fila;
        for (t=0;t<ticketlist.length;t++) {
            if (ticketlist[t]) {
                codigo = ticketlist[t];

                if (codigo) {
                    fila = id("tic_" + codigo);
                    if (fila && fila.selected) {
                        //alert("gPOS: Eliminando " +codigo);
                        fila.parentNode.removeChild(fila);
                        ticket[codigo] = null;
                        ticketlist.splice(t,1);
			iticket--;
                    }
                }		 
            }
        }	
        RecalculoTotal();
    }
   
    function limpiarSelectCart(){
        var t,codigo;	
        var xfila;
        for (t=0;t<ticketlist.length;t++) 
	{
            if (ticketlist[t]) 
	    {
                codigo = ticketlist[t];
                if (codigo) 
		{
                    xfila = id("tic_" + codigo);
                    if (xfila && xfila.selected) 
		    {
			xfila.setAttribute("selected","");
			xfila.setAttribute("current","");
                    }
                }
            }
        }	
    }


    function VaciarListadoProductos(){
        var oldListbox = id('listaProductos');

        var newListbox = document.gClonedListbox.cloneNode(true);
        oldListbox.parentNode.replaceChild( newListbox,oldListbox);     

        prodlist       = new Array();
        prodlist_cb    = new Array();
        prodlist_tag   = new Array();
	setViewListaProductoPrecios();
    }

    function VaciarListadoTickets(){
        var lista = id("listadoTicket");

        for (var i = 0; i < ticketlist.length; i++) { 
            if (ticketlist[i]) {
                kid = id("tic_"+ticketlist[i]);		
                if (kid)
		    lista.removeChild( kid ); 
            }
        }
        ticketlist = new Array();
    }

    function ModificaTicketUnidades(cuantas) {		

	var p_stock = id("prevt-stock").getAttribute("checked");
	var modo    = (p_stock != "true")? "pedidos":id("rgModosTicket").value;
        var cod     = getCodigoSelectedTicket();
        var unidcod = id("tic_unid_" + cod );	

	if (!cod) return;
        if (productos[cod].servicio || productos[cod].ilimitado) modo = "pedidos";//Servicio

	//Series
	if( modo != "pedidos" && productos[cod].serie)
	    return agnadirPorSeries(productos[cod].serie,productos[cod].producto,
				    productos[cod].unidades,cod,0);

        cuantas = ( cuantas<0 )? prompt(po_cuantasunidades,0):parseInt(cuantas);
	cuantas = parseInt(cuantas);//Control de Enteros

	if (isNaN(cuantas)) return alert( c_gpos + 'Ingresar un valor numérico');
	
        if (cuantas<0) return alert( c_gpos + 'Ingresar un valor numérico positivo.');

        if (cuantas==0) return QuitarArticulo();

        unidadesenventa = ticket[cod].unidades;//unidcod.getAttribute("value");
        unidadesalmacen = productos[cod].unidades;

	if ( cuantas > unidadesalmacen && modo!="pedidos" )
            return alert( c_gpos + "Existe "+unidadesalmacen+
			 " unidades de este producto en almacén");
	
	if(cuantas > unidadesenventa )
	{
            unidades        = cuantas - unidadesenventa;
            unidadesalmacen = productos[cod].unidades;
	    
            if(ticket[cod] && modo!="pedidos")
		if(ticket[cod].unidades == unidadesalmacen)
                    return alert(c_gpos + "Sin stock de este producto");
	    
            if(unidades > unidadesalmacen && modo!="pedidos")
	    {
		if(unidadesalmacen>1)
                    return alert( c_gpos + "Existe "+unidadesalmacen+
				 " unidades de este producto en almacén");
		
		if(unidadesalmacen==1)
		    return  alert( c_gpos + "Existe "+unidadesalmacen+
				  " unidad de este producto en almacén");	    
            }
        }

        if ( unidcod )
	{
            unidcod.setAttribute("value",cuantas);

	    if(ticket[cod]) ticket[cod].unidades = cuantas;	

	    if (ticket[cod]) CargarPedidoDetFila(cod,ticket[cod].unidades);//Pedido Detalle

	    //*+++ Ofertas Stock +++*//
	    if( ticket[cod].oferta ){
		var oferta   = ticket[cod].oferta;
		var tunid    = parseInt( ticket[cod].unidades ); 
		var ounid    = parseInt( ticket[cod].ofertaunid );
		var pvo      = parseFloat( ticket[cod].pvo );
		var precio   = ticket[cod].precio;		
		var unid     = ticket[cod].unid;

		oferta   = ( ounid >= tunid )? tunid:ounid;
		xdetalle = '**OFERTA '+oferta+''+unid+' c/u '+formatDinero(pvo)+'** ';
		oferta   = tunid+'~'+ounid+'~'+precio+'~'+pvo;//uni:ofertaunid:pv:pvo
		precio   = ( ounid >= tunid )? pvo:(pvo*ounid+(tunid-ounid)*precio)/tunid;

		ticket[cod].precio   = precio.toFixed(2);
		ticket[cod].oferta   = oferta;
		ticket[cod].vdetalle = xdetalle;
		id("tic_precio_"+cod).value  = ticket[cod].precio;
		id("tic_oferta_"+cod).value  = ticket[cod].oferta;
		id("tic_detalle_"+cod).value = ticket[cod].vdetalle;
	    }


            Blink("tic_unid_" + cod );			
            RecalculoTotal();
        }

	id("NOM").focus();
    }

    function mostrarseries(){

	var p_stock = id("prevt-stock").getAttribute("checked");
	var modo    = (p_stock != "true")? "pedidos":id("rgModosTicket").value;
        var cod     = getCodigoSelectedTicket();

	if (!cod) return;

	//Series
	if( modo != "pedidos" )
	    if(productos[cod].serie) 
		return agnadirPorSeries(productos[cod].serie,
					productos[cod].producto,
					productos[cod].unidades,
					cod,0);
    }

    function ConvertirSignoApropiado(unidades){
        var modo = id("rgModosTicket").value;
        var salida = 0;
        switch( modo ){
        case "interno":
            salida = 1;//Unidades en interno siempre es cero.	
            break;
        default:
        case "cesion":					
        case "venta":					
            salida =  Math.abs(unidades);
            break;			
        }
        //	alert("gPOS: modo:"+ modo + ",entra: "+unidades+",sale: "+salida);
        return salida;
    }
 
    function getCodigoSelectedTicket() {
        var t,codigo;	
        var fila;

        for (t=0;t<ticketlist.length;t++) {
            if (ticketlist[t]) {
                codigo = ticketlist[t];	
                if (codigo) {
                    fila = id("tic_" + codigo);
                    if (fila && fila.selected) {
                        return codigo;
                    }

                    if (fila == xlastArticulo){
                        return codigo;
                    }	
                }		 
            }
        }	
        //xlastArticulo.value
        return null;
    }

    function getCodigoSelectedProd() {
        var t,codigo;	
        var fila;

        for (t=0;t<prodlist.length;t++) {
            if (prodlist[t]) {
                codigo = prodlist[t];	
                if (codigo) {
                    fila = id("prod_" + codigo);
                    if (fila && fila.selected) {
                        return codigo;
                    }
                }		 
            }
        }	
        return null;
    }

    function formatDineroTotal(numero) {

        var num = new Number(numero);
        num = num.toString();

        if(isNaN(num)) num = "0";

        num = Math.round(num*100)/100;
        num = Math.round(num*10)/10;
	//more  alert(num);
        var sign = (num == (num = Math.abs(num)));
        num = num.toFixed(2);
        return (((sign)?'':'-') + num );   
    }

    function formatDinero(numero) {

        var num = new Number(numero);
        num = num.toString();

        if(isNaN(num)) num = "0";

        num = Math.round(num*100)/100;
        //num = Math.round(num*10)/10;
	//more  alert(num);
        var sign = (num == (num = Math.abs(num)));
        num = num.toFixed(2);
        /*	var num = new Number(numero);
            num = num.toString().replace(/\$|\,/g,'');

            if(isNaN(num)) num = "0";

            var sign = (num == (num = Math.abs(num)));
            num = Math.floor(num*100+0.50000000001);
            var cents = num%100;
            num = Math.floor(num/100).toString();

            if(cents<10) cents = "0" + cents;

            for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
            num = num.substring(0,num.length-(4*i+3))+','+ num.substring(num.length-(4*i+3));

            return (((sign)?'':'-') + num + '.' + cents);*/
        return (((sign)?'':'-') + num );   
    }


    function LimpiarCliente(){
        //restaurar cliente contado
        pickClienteContado();
    }

    function CancelarTicket(){
	habilitarControles();
	id('busquedaVentas').removeAttribute('disabled'); 
	VaciarDetallesVentas(); 
	id('NumeroDocumento').removeAttribute('disabled'); 
	CancelarVenta(); 
	//RESET 
	selTipoPresupuesto(0);//Presupuestos
	agnadirPorNombre();
    }


    function CancelarVenta(mantenermodo) {	
        LimpiaToClienteContado();
        VaciarListadoProductos();
        VaciarListadoTickets();
        CerrarPeticion();//Evita que se quede el interface de ticket abierto
        ClearImageview();//Borra la imagen/datos producto en visualizaciÃ³n
        id("NOM").value = "";
        id("REF").value = "";
        id("CB").value 	= "";	
        LimpiarMultipagos();	
        LimpiarSubVentanaServicio();
        if(!mantenermodo){
            LimpiarModosTicket();	
        }

        arreglosprenda 	= new Array(); //arreglos
        prodlist 	= new Array(); //ayuda al listado de "productos" (minivista de productos)
        prodlist_cb	= new Array(); // ... igual que el anterior, pero guarda cbs almacenados
        prodlist_tag	= new Array(); // ... igual que el anterior, pero guarda cbs almacenados
        iprod 		= 0;
        carrito 	= new Array(); //Unidades de cesta (obsoleto)
        ticket 		= new Array(); //Productos en la cesta
        ticketlist 	= new Array(); //ayuda al listado de cesta	
        iticket 		= 0;
	ticketTotalImporte      = 0;
	promocionesval          = new Array();
        carritoserie            = new Array();
        ticketserie             = new Array(); //Productos en la cesta

	/*+++++ Promociones ++++++++*/
	PromocionSeleccionado  = 0;
	xPromocionSeleccionado = false; 
	lPromocionSeleccionado = false; 
	id("ticketPromocionSeleccionado").setAttribute('collapsed',true);   
	id("ticketPromocionSeleccionado").setAttribute('label','');     
    }

    function LimpiarModosTicket(){

        var xmodos = id("rgModosTicket");

        id("rCesion").setAttribute("selected","false");
        id("rPedido").setAttribute("selected","false");
        id("rMProducto").setAttribute("selected","false");
        id("rVenta").setAttribute("selected","true");
        setTimeout("TicketAjusta()",90);	
        xmodos.value = "venta";
        xmodos.setAttribute("value","venta");

    }

    function RecalculoTotal() {

        var codigo,subtotal,subtotalconimpuesto,conimpuestoydescuento;	
        var fila, dato, impuesto,importe;
        var totalbase = 0;
        var totaldescuento = 0;
        var totalimporte = 0;
        var filaprecio,filacantidad,filasubtotal,filatotal,filadscto;
	var ticketsubtotal= 0,tickettotal=0,tickettotaldscto=0;

        for (var t=0;t<ticketlist.length;t++) {

            if (ticketlist[t]) {

                codigo = ticketlist[t];	

                if (codigo) 
		{
                    fila = id("tic_" + codigo);
                    if (fila) 
		    {
                        dato 	       = ticket[codigo].unidades;
                        impuesto       = parseMoney(ticket[codigo].impuesto);//id("tic_impuesto_" + codigo).value;
                        filacantidad   = parseMoney( dato );
                        filaprecio     = parseMoney( ticket[codigo].precio ); // id("tic_precio_" + codigo).value
                        filadscto      = CleanDescuento( ticket[codigo].descuento ); // id("tic_descuento_" + codigo).value
                        filasubtotal   = (parseFloat( filacantidad ) * parseFloat( filaprecio ));
                        filatotal      = parseFloat( filasubtotal ) - parseFloat( filasubtotal )*(parseFloat( filadscto )/100);
			filatotal       = formatDineroTotal( filatotal );
                        ticketsubtotal += parseFloat( filasubtotal);
			tickettotal    += parseFloat( filatotal );

			ticket[codigo].importe = formatDineroTotal( filatotal );
                        id("tic_importe_" + codigo).value = ticket[codigo].importe;
			id("tic_importe_" + codigo).setAttribute("value",ticket[codigo].importe);
                    }
                }		 
            }
        }

	tickettotaldscto = ticketsubtotal - tickettotal;

        id("TotalLabel").setAttribute("label", cMoneda[1]['S'] +" "+formatDineroTotal(tickettotal));
        id("SubTotalLabel").setAttribute("value",cMoneda[1]['S']+" "+formatDineroTotal(ticketsubtotal));
        id("DescuentoLabel").setAttribute("value",cMoneda[1]['S']+" "+formatDineroTotal(tickettotaldscto));
        id("TotalListadoLabel").setAttribute("value", 'en '+ticketlist.length+' productos');
	
        Global.totalbase   = formatDineroTotal( tickettotal );
	ticketTotalImporte = formatDineroTotal( ticketsubtotal );
 

 	/*++++ Promocion +++++*/
	if( !lPromocionSeleccionado ) cargarPromocion();
	lPromocionSeleccionado = false;
    }

    function ModificarPrecio(precio) {
        var ticketcodigo = getCodigoSelectedTicket();
        if (!ticketcodigo)	return;
        var ticprecio = id("tic_precio_"+ ticketcodigo);
        if (!ticprecio) return;

        p = (precio)? precio:parseMoney(prompt("Nuevo precio?", ticprecio.value ));
        if(p){
	    ticket[ticketcodigo].precio = formatDinero(p);
	    ticprecio.value             = ticket[ticketcodigo].precio;	
            ticprecio.setAttribute("value", ticket[ticketcodigo].precio );	
            Blink("tic_precio_" + ticketcodigo, "label-precio" );
            RecalculoTotal();
        }
    }

    function ModificarImporte() {
        var ticketcodigo = getCodigoSelectedTicket();
        if (!ticketcodigo)	return;
        var ticimporte = id("tic_importe_"+ ticketcodigo);
        if (!ticimporte) return;

	var oldimporte = parseFloat( ticket[ticketcodigo].importe );
	var cantidad   = parseFloat( ticket[ticketcodigo].unidades );
	var precioorig = (Local.TPV=='VC')? productos[ticketcodigo].pvc  : productos[ticketcodigo].pvd;
	var importeorig= formatDinero(precioorig*cantidad);
	var dscto      = '0';
	var newprecio  = precioorig;
 
        p = parseMoney(prompt("Nuevo importe?", ticket[ticketcodigo].importe));
	if(!p) return;

	if(parseMoney(oldimporte) == p) return;

	if(parseMoney(importeorig) != p || parseMoney(oldimporte) != p){
	    oldimporte = parseFloat(importeorig);
	}

	if(p > oldimporte){
	    newprecio = parseFloat(p)/cantidad ;
	}
	else{
	    dscto = parseFloat(oldimporte) - parseFloat(p);
	    dscto = (parseFloat(dscto) == 0)? '0':parseFloat(dscto);
	}

	var newimport = formatDinero(newprecio)*cantidad;
	newimport = Math.round(newimport*100)/100;

	if(newimport < p){
	    newprecio  = parseFloat(newprecio) + parseFloat(0.01);
	    newimport  = parseFloat(formatDinero(newprecio))*cantidad;
	    var xresto = newimport - p;
	    dscto      = parseFloat(xresto);
	}
	if(newimport > p){
	    var xresto = newimport - p;
	    dscto      = parseFloat(xresto);
	}

	ModificarPrecio(newprecio);
	ModificarDescuento(Local.esPrecios,dscto);
    }

    function ModificarDescuento(adm,dcto) {

	var modo = id("rgModosTicket").value;
        var ticketcodigo = getCodigoSelectedTicket();

        if (!ticketcodigo) return;

        var ticdscto   = id("tic_descuento_"+ ticketcodigo);
        var ticprecio  = parseFloat( ticket[ticketcodigo].precio );
        var cantidad   = parseFloat( ticket[ticketcodigo].unidades );
 	var precio     = (Local.TPV=='VC')? productos[ticketcodigo].pvc  : productos[ticketcodigo].pvd;
        var preciodcto = (Local.TPV=='VC')? productos[ticketcodigo].pvcd : productos[ticketcodigo].pvdd;
        var maxdes     = Math.round(parseFloat((precio - preciodcto)*cantidad)*100)/100;
        var dscto      = (dcto)? dcto:prompt("Descuento máximo permitido: "+maxdes+" "+cMoneda[1]['TP'], 0);
	var esDscto    = ( dscto < 0 || dscto > maxdes )? true:false;
	var esAdmin    = ( modo!="pedidos" && adm=='0' )? true:false;

	dscto = parseFloat(dscto);
	dscto = ( esAdmin && esDscto )? 0 : dscto;
        dscto = ( 100*dscto / ticprecio ) / cantidad;
        dscto = dscto.toFixed(2);
	dscto = ( dscto<0   )? 0.0   : dscto;
	dscto = ( dscto>100 )? 100.0 : dscto;
        dscto = parseMoney(dscto);

	if ( esAdmin && esDscto ) alert( c_gpos + "\n    Descuento no permitido.");

	ticket[ticketcodigo].descuento = dscto;
	ticdscto.value = FormateComoDescuento(ticket[ticketcodigo].descuento);
	ticdscto.setAttribute("value", FormateComoDescuento(ticket[ticketcodigo].descuento));
	Blink("tic_descuento_" + ticketcodigo, "label-descuento" );
        RecalculoTotal();
    }

    function CleanDescuento( valor ) {
        if (!valor) 	return 0.0;

        //valor = valor.replace(/ /g,"");
        //valor = valor.replace(/%/g,"");
        valor = parseFloat(valor);
        if (isNaN( valor ))
            return 0.0;
        return valor;	
    }

    function formatDescuento(valor) {
        return FormateComoDescuento(valor);
    }

    function FormateComoDescuento(valor) {
        if (!valor || valor ==0 || valor =="0")
            return "  ";
	//Especial para no hacer tan presente el descuento, dado que la mayor parte del 
        // tiempo no es bonito ni relevante.

        return valor + " %";
    }


    function GuardarMProductoTPV() {    
	//  - MPRODUCTO  : 2

	id("NOM").focus();
	
	//Variables
	var alertuser='';
	var alertpreventa='';
	var modo     = id("rgModosTicket").value;
	var TotalMP  = id("TotalLabel").label;
	var tcliente = id("tCliente").label;
	var modotpv  = 0;
	var noticket = 1;
	var Estado   = 'Ensamblaje';
	var t_client = id("tCliente").label;
	var t_mprod  = id("SelBaseMProducto").label;

	//Valida modos TPV
	if( modo=="mproducto") var modotpv = 1;

	//Select item baseMetaProducto
	if(habilitarAddMProducto()) return;

	//STOCK
	var p_stock = id("prevt-stock").getAttribute("checked");
	if( p_stock != "true" && IdMProducto !=0 )
	    if(StockMetaProducto == 0)
		return alert(  c_gpos + "\n  - Active check - [x] Stock - del Ticket Actual")

	//Mensaje Usuario
	if ( UsuarioSeleccionado == 1 )
	    alertuser = '\n - Selecione un cliente diferente al - '+tcliente+' - ';

	//Mensaje Modo TPV
	if(modotpv == '0') 
	    alertpreventa = '\n - Selecione el modo - METAPRODUCTO - en el TPV.';

	//Alert Existencias Carrito
	if(parseInt(ticketlist.length) <= 1)
	    return alert( c_gpos + "\n - El ticket actual esta vacio ó "+
			  "solo tiene un articulo selecionado."+
			 "\n - Para Guardar el MProducto liste "+
			  "más de un articulo."+alertpreventa+alertuser);
	//Alert modo TPV
	if(modotpv == '0') 
	    return alert( c_mproducto + alertpreventa +' '+ alertuser);

	//Alert Usuario TPV
	if ( UsuarioSeleccionado == 1 )
	    return alert( c_mproducto + alertuser);

	//Inicia variables Ajax 
	var data,firma,resultado, esCopia;
	var xrequest = new XMLHttpRequest();
	var unidades, precio, descuento,codigo;
	var url = "";

	//Numero de serie random, para evitar peticiones multiples.
	// si dos peticiones seguidas llevan el mismo serial, la segunda se ignora
	var sr = parseInt(Math.random()*999999999999999);

	//Inicia proceso
	var mpticket    = t_CrearTicket(esCopia,noticket);
	var text_ticket = mpticket.text_data;
	var post_ticket = mpticket.post_data;
	var modoticket  = 'mproducto';
	
	//Update MProducto
	if(parseInt(IdMetaProducto) !=0 && parseInt(IdMProducto) != 0 ){

	    if(confirm('gPOS: META PRODUCTOS \n\n'+
		       '     ¿Finalizar Ticket MProducto - '+IdMProducto+' - ?'))
	    {
		Estado   = 'Finalizado';
		//setStatusMProducto(IdMetaProducto,Estado);//Update
	    } 
	    else
	    { 
		if(!confirm('gPOS: META PRODUCTOS \n\n'+
			    '      ¿Salvar los cambios del Ticket MProducto - '+IdMProducto+' - ?'))
		{
		    CancelarVenta();
		    selTipoPresupuesto(0);//Reset PreVenta
		    AjustarEtiquetaMetaproducto();//Reset MetaProductos
		    return;//Salir de proceso 
		}
	    }
	    
	    modoticket = 'endmproducto';
	    t_mprod    = id("SelMProducto").label;
	    generadorEliminaMProducto(IdMetaProducto,IdMProducto);//Elimina Item MProducto
	}

	//Salvar Datos para Pedido
	if(modoticket != 'endmproducto') 
	    var srt_p = IdMProducto+'~'+UsuarioSeleccionado;

	//Inicia Proceso
	DesactivarImpresion();

	//Registrar Comprobante y detalles del comprobante
	url =
	    "xcreamprodticket.php?modo="+modo+"&"+
	    "moticket="+modoticket+"&"+
	    "tpv_serialrand="+sr;
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type',
				  'application/x-www-form-urlencoded;'+
				  ' charset=UTF-8');
	//alert("gPOS: IdMetaProducto: "+IdMetaProducto);
	xrequest.send(post_ticket+
		      '&IdMProducto='+IdMProducto+'&'+
		      'IdMetaProducto='+IdMetaProducto+'&'+
		      'Estado='+Estado);
	//alert( xrequest.responseText );
        var xres = xrequest.responseText;	    
	//Valida...
        //x~val~90001002:15:5:4:0;90003005:17:2:5:fff,ggg
	var prod = xres.split("~val~");    
	
	//Problemas...
	if(prod[1]) return ValidaTicket('mproducto','validamproducto',prod[1]);

	//Status...
	if( Estado=='Finalizado' ) setStatusMProducto(IdMetaProducto,Estado);

	resultado = xrequest.responseText;
	//alert(resultado);
	resultado = parseInt(resultado);
	
	if (!resultado )	
            return alert( c_gpos + po_error + "\n - Al guardar MetaProducto ");	

	//Salvar Datos para Pedido *****
	if(modoticket != 'endmproducto') 
	    srt_p += '~'+resultado+'_'+t_mprod+'_'+TotalMP;

	//Actualiza datos en TPV
	if( Estado == 'Finalizado' )
	{

	    //Sunc...
	    syncProductosPostTicket();

	    //PDF Codigo barras
	    //var url="modulos/fpdf/codigo.php?codigo="+IdMProducto;

	}
	var xEstado = ( Estado == 'Ensamblaje' )? 'Arreglo':Estado;
	//Regresamos el Numero Pre-Venta
	alert( c_gpos + ".                                 "+
	      "- TPV METAPRODUCTOS -"+
	      "                                 .\n\n"+
	      "Cliente     :  "+t_client+" \n"+	  
	      "Producto  :  "+t_mprod+" \n"+	  
	      "Estado     :  "+xEstado+" *** \n"+	  
	      "Código          :  "+resultado+" *** ");

	// Finaliza el proceso
	CerrarPeticion();
	habilitarControles(); 
	document.getElementById('busquedaVentas').removeAttribute('disabled');
	VaciarDetallesVentas(); 
	document.getElementById('NumeroDocumento').removeAttribute('disabled');
	CancelarVenta();
	HabilitarImpresion();

	//ASEGURAMOS QUE LAS FACTURAS TENGAN UN CLIENTE SELECIONADO
	//var radiofactura = id("radiofactura");
	//if(radiofactura.selected)
	//   tipocomprobante(1); 

	//ADD ITEM DEFAULT
	generadorCargarMProducto('MProducto',resultado);

	//RESET 
	selTipoPresupuesto(0);//Presupuestos

	//Guarda Detalle para pedido
	if(modoticket != 'endmproducto')
	    salvarMPparaPedido(srt_p);//IdUsuario~CBMP~IdProducto

	//IMPRIME UN TICKET MPRODUCTO
	//if( Estado == 'Finalizado' )//Imprime Comprobante
	//    location.href=url;
    }

    function salvarMPparaPedido(srt){
    
	if(srt!='')
	    ArrMP.push(srt);
	//alert(ArrMP.toString());

    }

    function DesactivarImpresion(){ id("BotonAceptarImpresion").setAttribute("disabled","true");}
    function HabilitarImpresion(){ id("BotonAceptarImpresion").setAttribute("disabled","false");}
    function AjustarEtiquetaMetaproducto(){
	    //Boton Imprimir
	    var btimpr   = id("botonImprimir");
	    var btborr   = id("botonBorrar");
	    var btguar   = id("botonGuardar");
	    var s_bmprod = id("SelBaseMProducto");
	    var modo     = id("rgModosTicket").value;	    
	    var id_modo  = id("rgModosTicket");	    
	    var p_stock  = id("prevt-stock");
	    var r_pedid  = id("rPedido");
	    var r_cesio  = id("rCesion");
	    //var l_pvpti  = id("pvpUnidadTicket");

	    //Default
	    btimpr.setAttribute("collapsed", "false");  //Boton Imprimir
	    id_modo.setAttribute("collapsed", "false");  //Boton Imprimir
	    s_bmprod.setAttribute("collapsed", "true"); //Combo MProducto
	    btguar.setAttribute("oncommand", "GuardarPreVentaTPV()"); //Boton Guardar
	    btborr.setAttribute("oncommand", "BorrarVentaTPV()"); //Boton Guardar
	    r_pedid.setAttribute("collapsed", "false");//Oculta Pedidos
	    r_cesio.setAttribute("collapsed", "false");//Oculta Cesion
	    s_bmprod.setAttribute("label","Elije MixProducto...."); //Meta Producto
	    //l_pvpti.setAttribute("label","PV/U"); 
	    //MetaProducto
	    if( modo == "mproducto" ){
		btimpr.setAttribute("collapsed", "true");  //Boton Imprimir
		s_bmprod.setAttribute("collapsed", "false"); //Combo MProducto
		btguar.setAttribute("oncommand", "GuardarMProductoTPV()"); //Boton Guardar
		btborr.setAttribute("oncommand", "BorrarMProductoTPV()"); //Boton Borrar
		r_pedid.setAttribute("collapsed", "true");//Oculta Pedidos
		r_cesio.setAttribute("collapsed", "true");//Oculta Cesion
		//l_pvpti.setAttribute("label","Costo/U"); 

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//Reset base Metaproducto
		if(IdTipoPresupuesto == 0)
		    IdTipoPresupuesto = 5;

		if(IdTipoPresupuesto == 4){
		    IdMProducto    = 0 // Reset (IdProducto) to (CBMetaProducto)
		    IdMetaProducto = 0 // Reset (IdProducto) to (CBMetaProducto)
		    s_bmprod.setAttribute("collapsed", "true");  //Combo MProducto
		    id_modo.setAttribute("collapsed", "true");  //Boton Imprimir
		}
	    }
	}


        function mostrardetalleMProducto(){

            //METAPRODUCTO*******************
            var t,codigo,aserie;	
            var fila,mproducto;
	    var esMProducto=false;
	    var reporte='';
            var bandera=0;
	    var iticketserie = 0;//Borrame
            for (t=0;t<ticketlist.length;t++) {
		if (ticketlist[t]) 
		{
                    codigo      = ticketlist[t];
 		    fila        = id("tic_" + codigo);
		    esMProducto = (productos[codigo].mproducto)? true:false;

		    if (fila && fila.selected && esMProducto) 
		    {
                        //var cb        = ticket[codigo].codigobarras;
			var arrseries = ticket[codigo].series;

			for( var j=0;j<arrseries.length;j++)
			{
			    aserie    = arrseries[j].split(":");//IdPedidoDet:Stock
			    mproducto = productos[codigo].producto;
			    reporte   = reporte+'\n\n '+row_mostrardetalleMProducto(aserie[1],
										    mproducto);
			    bandera=1;
			    
			}
 			//lanza mesaje Resumen
			if(parseInt(reporte) != 0 ) alert ( c_mproducto + reporte);
                    }					    
		}
            }
	    //Si no es MProducto
	    if(parseInt(bandera) != 1 )
		return alert ( c_mproducto + "\n"+
			      "      "+
			      "- No es un Meta Producto, elija otro.");
	}

        function CargarPedidoDetFila(xcod,xstock){

	    var modo       = id("rgModosTicket").value;
	    var akardex    = productos[xcod].rkardex;
	    var apkardex   = Array();
	    var nkardex    = Array();
	    var xresto     = xstock;
	    var selstock   = 0;
	    var xpedidodet = '';

	    for(var j=0; j< akardex.length; j++)
	    {
		apkardex = akardex[j].split(":");//IdPedidoDet:Stock
		selstock = ( xresto <= apkardex[1] )? xresto : apkardex[1];
		xresto   = ( xresto > apkardex[1]  )? xresto - apkardex[1] : 0;

		if (selstock>0)
		    nkardex.push( apkardex[0] + ':' + selstock );//IdPedidoDet:Stock
	    }	  

	    //Existe...
	    if ( !id("tic_pedidodet_"+xcod) ) return;

	    //Propiedades...
	    xpedidodet = ( productos[xcod].serie)? CargarPedidoDetFilaSerie(xcod) : nkardex.toString();
	    xpedidodet = ( akardex.length > 0        )? xpedidodet  : '';
	    xpedidodet = ( productos[xcod].ilimitado )? 'ilimitado' : xpedidodet;
	    xpedidodet = ( productos[xcod].servicio  )? 'servicio'  : xpedidodet;

	    //Carrito...
	    ticket[xcod].pedidodet = xpedidodet;
	    id("tic_pedidodet_"+xcod).value = ticket[xcod].pedidodet;
	    id("tic_pedidodet_"+xcod).setAttribute('value',ticket[xcod].pedidodet);

	    //Stock...
	    if( productos[xcod].ilimitado || productos[xcod].servicio ) return;	    

	    //Series...
	    if( !(ticket[xcod].series != '') ) return; 

	    //Kardex... 
	    var aselpedido = xpedidodet.split(",");
	    var xselunidad = 0;
	    
 	    for (var sel in aselpedido )
	    {
		var selpedido  = aselpedido[sel].split(":");
		xselunidad     = parseInt(xselunidad)+parseInt( selpedido[1]);
	    }
	    ticket[xcod].unidades        = xselunidad;
	    id("tic_unid_" + xcod).value = ticket[xcod].unidades;
	}

        function CargarPedidoDetFilaSerie(xcod){
	    var xnkardex   = Array();
	    var xselstock  = Array();
	    var xrPedido   = new Array(); 
	    var pSeries    = Array();

	    for(var m=0; m < ticket[xcod].series.length; m++)
	    {
		//IdPedidoDet:Serie
		pSeries = ticket[xcod].series[m].split(":");
		
		//Serie;Serie
		xrPedido[ pSeries[0] ] = (!xrPedido[ pSeries[0] ])? pSeries[1] : xrPedido[ pSeries[0] ]+';'+pSeries[1];
	    }

	    for (var g in xrPedido)
	    {
		xselstock = xrPedido[g].split(";");

		//Pedido:Stock:serie
		xnkardex.push( g + ':' + xselstock.length + ':' + xrPedido[g] );
	    }

	    return xnkardex.toString();
	}



/*+++++++++++++++++++++++++++++ TOOLS ++++++++++++++++++++++++++++++++++*/


         /*+++++++++++++ CADENAS +++++++++++++++++++++++++*/
         var c_gpos         = "gPOS: ";
         var c_stockalmacen = c_gpos+"STOCK ALMACÉN \n\n Producto: ";
         var c_mproducto    = c_gpos+"TPV MIXPRODUCTO ";
         var c_preventa     = c_gpos+"TPV PREVENTA "
         var c_pedido       = c_gpos+"TPV PROFORMA "

         /*+++++++++++++  TOOL ++++++++++++++++++++++++*/
         var log = function (param) {
             return;
             var datalog =  id("logarea");
             var actual  = datalog.getAttribute("value");
             datalog.setAttribute("value",param+"\n"+actual);
	 }

         //NOTA: Esto esta bien aqui?
         var L = new Array();
         L[1]  = "unica";
         L[2]  = "unico";
         L[3]  = "L";
         L[4]  = "negro";

         /*+++++++++++++++ VISUAL BLINK ++++++++++++++++++++*/

         function blockUnidTicket(name){
	     var xundsel  = name.split('_');
	     if(id('tic_unid_'+xundsel[2]))
	     {
		 id('tic_unid_'+xundsel[2]).style.align ="right";
		 id('tic_unid_'+xundsel[2]).style.textAlign ="right";
	     }
	 }

         function unIluminate(name,tipo) {

             var me  = id(name);
             id(name).style.backgroundColor='transparent';
             id(name).style.color='black';
	     
             if (tipo=="listbox"){
		 
		 //id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
		 blockUnidTicket(name);//solo para unidades en ticket
	     }
             else
		 if (tipo=="menulist") 
                     id(name).style.cssText = "-moz-binding: url(\"chrome://global/content/bindings/menulist.xml#menulist\");";			  
             else 
                 if (tipo =="label-precio") {
                     id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
                     me.style.align ="right";
                     me.style.textAlign ="right";							
                 }
             else
                 if (tipo == "groupbox"){
                     id(name).style.cssText = "-moz-binding: url(\"chrome://global/content/bindings/groupbox.xml#groupbox\");";			  			
                 }else 
                     if (tipo =="label-descuento") {
                         id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
                         me.style.align ="right";
                         me.style.textAlign ="right";							
                     }		

	 }

         function Iluminate(name) {
             //id(name).style.backgroundColor=' yellow !important';
             //id(name).style.color=' black !important ';
	     id(name).style.cssText = "background-color:yellow!important; color:black!important; "
	 }

         function Blink(name,tipo) {
             Iluminate(name);
	     
             if (!tipo) tipo ="listbox";

             setTimeout("unIluminate('"+name+"','"+tipo+"') ",500);
	 }


         /*+++++++++++++++++ VISUAL HINTS +++++++++++++++++++++++*/

         var Imageview        = new Object();
         Imageview.lastchange = 0;
         Imageview.cb         = false;
         Imageview.oldcb      = false;
         Imageview.nombre     = false;

         function ClearImageview(){
             //Borra la imagen/datos de producto en la ventana de visualizacion del mismo
             Imageview.cb = "";
             id("muestraProductoCB").setAttribute("value","");
             //id("nombreProducto").setAttribute("value","");
             id("muestraProducto").setAttribute("src", "" );
             id("muestraProductoIcon").setAttribute("collapsed","true");
	 }

         function UpdateImageview(){
	     var difftime = (new Date()).getTime() - parseFloat(Imageview.lastchange);
	     
	     if(difftime>50){	
		 if (Imageview.oldcb != Imageview.cb){	
		     id("muestraProductoCB").setAttribute("value",Imageview.cb);
		     id("muestraProductoIcon").setAttribute("collapsed","false");
		     id("muestraProducto").setAttribute("src", "productos_img/" + escape( productos[ Imageview.cb ].imagen ) );
		     Imageview.oldcb = Imageview.cb;
		 }
	     } else {
		 //wait more!
		 setTimeout("UpdateImageview()",25);
	     }
	  }


          /*+ DEPENDIENTES +*/
          var cMenuItemDependiente      = false;
          var cMenuItemNuevoDependiente = false;

          function cambiaDependiente(xthis) {

	      cMenuItemDependiente      = false;
              cMenuItemNuevoDependiente = false;

              var dep;
	      var esCheked = false;

	      for (var h=0;h<Local.max_dep;h++) {

		  dep = id ("dep_"+h);
		  
		  // Dependiente
		  if( dep.value == Local.IdDependiente )
		      cMenuItemDependiente = dep;

		  // Nuevo Dependiente
		  if( dep.getAttribute("checked") == 'true' )
		      cMenuItemNuevoDependiente = dep;
	      }

	      // Dependiente = nuevo dependiente
	      if(Local.IdDependiente == cMenuItemNuevoDependiente.value) 
		  return actualizaUsuarioTPV(2,false);

	      // cambia nombre dependiente
	      var xnombre = cMenuItemNuevoDependiente.getAttribute("label");
	      var xiduser = cMenuItemNuevoDependiente.value;
	      xthis.setAttribute("label",xnombre+' ');
	      
	      // marcando dependiente sesion
	      if(cMenuItemDependiente.value == id("DependienteSession").value)
		  cMenuItemDependiente.setAttribute("style","font-weight:bold");

	      // Pide contraseña
	      id("cambioPassUsuario").value = "";
	      id("cambioPassUsuario").setAttribute("placeholder","Ingrese contraseña");
	      id("cambioPassUsuario").setAttribute("collapsed",false);
	      id("cambioPassUsuario").focus();
	  }

          function checkCambioPassUsuario(apass){

	      var espass = (apass)? true:false;

	      if(espass ) cambiarUsuarioTPV();	      
	      if(!espass) actualizaUsuarioTPV(2,false);	      
	  }

          function cambiarUsuarioTPV(){

	      var xuser = cMenuItemNuevoDependiente.value;
	      var xpass = id("cambioPassUsuario");
	      var pass  = xpass.value;
	      var data  = "";
	      var xdato = 0;
	     
	      if( xpass.getAttribute("collapsed") == 'true' ) return;
	      xpass.setAttribute("collapsed",true);

	      pass = (trim(pass))? pass:false;
	      if(!pass) return actualizaUsuarioTPV(2,false);

	      var url  = "services.php?modo=login";
	      data = data + "&xid="+xuser;
	      data = data + "&xp="+pass;
	      
	      var obj = new XMLHttpRequest();
	      obj.open("POST",url,false); 
	      obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	      
	      try{
		  obj.send(data);
		  res = obj.responseText;
	      }catch(e){
		  res = false;
	      }

	      if(!res) return actualizaUsuarioTPV(2,false);
	      var ares = res.split("~");

	      if ( !parseInt( ares[0] )) return alert(po_servidorocupado);


	      Local.esPrecios            = ares[1];
	      Local.esAdmin              = ( ares[1] == 1)? true:false;
	      Local.esCajaTPV            = ares[2];
	      Local.esB2B                = ares[3];
	      Local.esServicios          = ares[4];
	      Local.esSuscripcion        = ares[5];
	      Local.esSAT                = ares[6];
	      Local.esStock              = ares[6];

	      updatexAtt("ticketModificarPrecio",Local.esPrecios);
	      updatexAtt("ticketModificarImporte",Local.esPrecios);
	      updatexAtt("ckCodigoAutorizacionCliente",Local.esPrecios);	      
	      updatexAtt("ckCodigoAutorizacion",Local.esPrecios);	      
	      updatexAtt("VentaRealizadaDevolver",Local.esStock);
	      updatexAtt("VerCajaButton",Local.esCajaTPV);
	      updatexAtt("modoDcumentoSoloCaja",Local.esCajaTPV);
	      updatexAtt("depTipoVentaVC",Local.esB2B);

	      if( Local.esB2B == 0 )
		  if(Local.TPV == 'VC')
		      cambiarTipoVenta( id("depTipoVentaVD") );
	      
	      updatexAtt("VerServiciosButton",Local.esServicios );
	      updatexAtt("cargarSuscripcion",Local.esSuscripcion );
	      
	      updatexAtt("btnOrdenServicio",Local.esSAT );
	      updatexAtt("btnOrdenServicioDet",Local.esSAT );
	      updatexAtt("itemEditarOrdenServicio",Local.esSAT );
	      updatexAtt("itemEditarOrdenServicioDet",Local.esSAT );
	      updatexAtt("itemAgregarServicio",Local.esSAT );
	      updatexAtt("itemAgregarProducto",Local.esSAT );
	      updatexAtt("itemQuitarProducto",Local.esSAT );
	      updatexAtt("itemClonarServicio",Local.esSAT );

	      id("ticketModificarDescuento").setAttribute("oncommand",
							  "ModificarDescuento("+ares[1]+",false)");
	      //cambio usuario: mantiene usuario
	      xdato = ( res[0] == 1 )? 1:2;  
	      actualizaUsuarioTPV(xdato,xuser);

	      //limpiamos lista de precios
	      //VaciarListadoProductos();
	      //listamos busqueda actual
	      agnadirPorNombre();

	  }

           function updatexAtt(xid,xstatus){
	       if( xstatus == 1 ) id( xid ).setAttribute("collased",false);
	       if( xstatus == 0 ) id( xid ).setAttribute("collased",true);
	       if( xstatus == 1 ) id( xid ).removeAttribute("disabled");
	       if( xstatus == 0 ) id( xid ).setAttribute("disabled",true);
           }


          function actualizaUsuarioTPV(xdato,xuser){

	      switch(xdato){
	      case 1:
		  Local.nombreDependiente =  trim(id("depLista").getAttribute("label"));
		  Local.IdDependiente = xuser;
		  //id("cambioPassUsuario").setAttribute("collapsed",true);
		  //habilitarCajaTPV();
		  break;

	      case 2:
		  id("cambioPassUsuario").value = "";
		  id("cambioPassUsuario").setAttribute("placeholder"," Contraseña incorrecta");

		  cMenuItemDependiente.setAttribute("checked",true);

		  if(cMenuItemNuevoDependiente.value !=  cMenuItemDependiente.value)
		      cMenuItemNuevoDependiente.setAttribute("checked",false);

		  id("depLista").setAttribute("label",Local.nombreDependiente+' ');
		  break;
	      }
	      id("NOM").focus();
	      id("cambioPassUsuario").setAttribute("collapsed",true);
	      id("cambioPassUsuario").value = "";

	  }

          function habilitarCajaTPV(){
	      var usersession = id("DependienteSession").value;
	      var op = (usersession == Local.IdDependiente)? false:true;
	      id("VerCajaButton").setAttribute("disabled",op);
	      
	  }

          /*+ FICHA PRODUCTO +*/

          var esFichaVisible = 0;

          function ToggleFichaFormOUT() {
              //	CBFocus();
              esFichaVisible = 0;

              id("panelDerecho").setAttribute("collapsed",false);
              id("modoVisual").setAttribute("selectedIndex",0);
              id("fichaProducto").setAttribute("src","about:blank");

	      //Actualiza lista productos 
	      resizelistboxticket(true);
	  }


          function ToggleFichaForm() {
              var code;
              if(!getCodigoSelectedProd()) return;
              
              if (esFichaVisible) {
		  id("panelDerecho").setAttribute("collapsed",false);
		  code = 0;//ocultar
              } else {
		  id("panelDerecho").setAttribute("collapsed",true);
		  code = 4;
              }

              var cod = getCodigoSelectedProd();
              var fichaProducto = id("fichaProducto");
	      id("fichaProductoNombre").setAttribute("label",productos[cod].producto);
              id("modoVisual").setAttribute("selectedIndex",code);

              var url = "simplecruzado.json.php?CodigoBarras=" + cod;
              var obj  = Meca.cargarJSON( false,url,true);
              if(obj){
		  Dom.MatarTodosHijos("fichaProducto");		
		  Meca.generaCruzadoProductos( "fichaProducto", obj );	
              }
              esFichaVisible = code;
	      resizelistboxticket(false);
	  }


          /*+ FICHA SECCION +*/
          var esOffLineSyncTPV = false;//Control de Reload demon syncTPV

          function ActualizacionEstadoOnline(){
              if ( peticionesSinRespuesta >= 2 )
	      {
		  mostrarMensajeTPV(1,'...no se puede conectar con gPOS',7500);
		  id("bolaMundoOff").setAttribute("collapsed",false);
		  id("bolaMundo").setAttribute("collapsed",true);
		  id("syncTPVOff").setAttribute("collapsed",false);
		  id("syncTPV").setAttribute("collapsed",true);
		  id("botonImprimir").setAttribute("disabled",true);
		  id("botonGuardar").setAttribute("disabled",true);
		  esOffLineSyncTPV = true;
              }
	      else 
	      {
		  
		  id("bolaMundoOff").setAttribute("collapsed",true);
		  id("bolaMundo").setAttribute("collapsed",false);
		  id("syncTPVOff").setAttribute("collapsed",true);
		  id("syncTPV").setAttribute("collapsed",false);
		  id("botonGuardar").removeAttribute("disabled");
		  if ( Local.esCajaCerrada == 0 ) id("botonImprimir").removeAttribute("disabled");

		  if ( esOffLineSyncTPV ){
		      mostrarMensajeTPV(1,'...hemos vuelto',1200);
		      setTimeout("Reload_demon_syncTPV()",1200);
		  }
              }
          } 


          function esSyncBoton(xval){
	      if( xval == 'pause') 
		  return setTimeout("esSyncBoton()",3000);
	      if( !xval )
		  return id("syncTPV").className = "sync_pause";
	      if( id("syncTPV").className == "sync_run" ) 
		  return true;

	      id("syncTPV").className = "sync_run";
	      return false;
	  }

          function esSyncModuleBoton(xmodulo,xval){

	      if( xval == 'on')
              {
	          if( id(xmodulo).className == "sync_module_off" )
                  {
                      id(xmodulo).className = "sync_module_on";
                      return false;
                  }
                  return true;
              }
              //Para todo caso lo desactiva              
	      if( id(xmodulo).className == "sync_module_on" )
                  id(xmodulo).className = "sync_module_off";
              return true;
	  }

          function esOfflineBusquedas(){
	      if ( esOnlineBusquedas() ) 
		  return true;
	      return false;
	  }

          function esOnlineBusquedas(){
              if ( id("buscar-servidor").getAttribute("checked") == "true"){
		  return false;
              } else {
		  return true;
              }
	  }


          /*+++++++++++++++++++ BUSQUEDA EXTRA ++++++++++++++++++++++*/
          var AjaxBuscaEnServidorXRef = false;
          var AjaxBuscaEnServidorXCB  = false;
          var AjaxFuncionExito        = false;//Para peticion asincrona
          var AjaxBuscaEnServidor     = false;


          function ExtraBuscarEnServidor(nombreProducto){
              var url = "services.php?modo=buscaproducto&nombre="+encodeURIComponent(nombreProducto);
              AjaxBuscaEnServidor = new XMLHttpRequest();	
              AjaxBuscaEnServidor.open("POST",url,true);
              AjaxBuscaEnServidor.setRequestHeader('Content-Type',
						   'application/x-www-form-urlencoded; charset=UTF-8');
              AjaxBuscaEnServidor.onreadystatechange = Rececepcion_BuscaEnServidor;
              AjaxBuscaEnServidor.send(null);

	  }
 
          function ExtraBuscarEnServidorCB(cb){
              var url = "services.php?modo=buscarproductocb&cb="+encodeURIComponent(cb);
              AjaxBuscaEnServidor = new XMLHttpRequest();	
              AjaxBuscaEnServidor.open("POST",url,true);
              AjaxBuscaEnServidor.setRequestHeader('Content-Type',
						   'application/x-www-form-urlencoded; charset=UTF-8');
              AjaxBuscaEnServidor.onreadystatechange = Rececepcion_BuscaEnServidor;
              AjaxBuscaEnServidor.send(null);

	  }

          function ExtraBuscarEnServidorXRef(refProducto){
	      var url = "services.php?modo=buscaproductoref&ref="+escape(refProducto);
              AjaxBuscaEnServidorXRef = new XMLHttpRequest();	
              AjaxBuscaEnServidorXRef.open("POST",url,true);
              AjaxBuscaEnServidorXRef.setRequestHeader('Content-Type',
						       'application/x-www-form-urlencoded; charset=UTF-8');
              AjaxBuscaEnServidorXRef.onreadystatechange = Rececepcion_BuscaEnServidorXRef;
              AjaxBuscaEnServidorXRef.send(null);
	      
	  }

          function ExtraBuscarEnServidorXCB(cbProducto){		
              //Peticion sincrona
              //NOTA: es burcaRproductocb
              var url = "services.php?modo=buscarproductocb&cb="+escape(cbProducto);
              AjaxBuscaEnServidorXCB = new XMLHttpRequest();	
              AjaxBuscaEnServidorXCB.open("POST",url,false);
              AjaxBuscaEnServidorXCB.setRequestHeader('Content-Type',
						      'application/x-www-form-urlencoded; charset=UTF-8');
              AjaxBuscaEnServidorXCB.send(null);
	      
              var rawtext = AjaxBuscaEnServidorXCB.responseText;
              try {	
		  //alert(rawtext);
		  if (rawtext) {
                      eval(rawtext);
		  }	
              } catch(e){	
		  //alert("gPOS: error: "+e.toSource());	
              }							
	  }

         function Old_ExtraBuscarEnServidorXCB(cbProducto, functionSiExito){		
             //INFO: Peticion asincrona, ahora no se usa
             var url = "services.php?modo=buscaproductocb&cb="+escape(cbProducto);
             AjaxBuscaEnServidorXCB = new XMLHttpRequest();	
             AjaxBuscaEnServidorXCB.open("POST",url,true);
             AjaxBuscaEnServidorXCB.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
             AjaxBuscaEnServidorXCB.onreadystatechange = Rececepcion_BuscaEnServidorXCB;
             AjaxBuscaEnServidorXCB.send(null);
	     
             AjaxFuncionExito = functionSiExito;			
	 }

         
          function Rececepcion_BuscaEnServidor(){
	      //alert(AjaxBuscaEnServidor);
              if (!AjaxBuscaEnServidor) {
		  AjaxBuscaEnServidor = new XMLHttpRequest();
		  return;
              }


              if (AjaxBuscaEnServidor.readyState==4) {		
		  var rawtext = AjaxBuscaEnServidor.responseText;
		  try {	
                      eval(rawtext);
                      //alert(rawtext);			
		  } catch(e){
                      //alert("gPOS: ERROR en la evaluacion de la respuesta");	
                      alert( c_gpos + "error: "+e.toSource());	
		  }
              }	
	  }

          function Rececepcion_BuscaEnServidorXRef(){
	      if (!AjaxBuscaEnServidorXRef) {
		  AjaxBuscaEnServidorXRef = new XMLHttpRequest();
		  return;
              }
	      
              if (AjaxBuscaEnServidorXRef.readyState==4) {		
		  var rawtext = AjaxBuscaEnServidorXRef.responseText;
		  try {	
                      eval(rawtext);			
		  } catch(e){	
                      //alert("gPOS: error: "+e.toSource());	
		  }
              }	
	  }


         function Rececepcion_BuscaEnServidorXCB(){
             //INFO: version asincrona, ahora no se usa
             if (!AjaxBuscaEnServidorXCB) {
		 AjaxBuscaEnServidorXCB = new XMLHttpRequest();
		 return;
             }
	     
             if (AjaxBuscaEnServidorXCB.readyState==4) {		
		 var rawtext = AjaxBuscaEnServidorXCB.responseText;
		 try {				
                     eval(rawtext);	
                     if (AjaxFuncionExito){
			 eval(AjaxFuncionExito);				
			 AjaxFuncionExito = false;//por si acaso hay rellamadas
                     }		
		 } catch(e){	
                     //alert("gPOS: error: "+e.toSource());	
		 }
             }	
	 }

          function CEEP(codigo){
              var k      = productos[codigo];		
	      var precio = (Local.TPV=='VC')? k.pvc:k.pvd;

              if (k) 
	      {					
		  if ( prodlist_cb[codigo]) return;
		  
		  CrearEntradaEnProductos(k.producto,k.nombre,k.marca,k.color,k.talla,k.laboratorio,
					  k.codigobarras,k.referencia,precio,k.pvemp,k.pvdoce,k.pvc, 
					  k.impuesto,k.unidades,k.costo,k.lote,k.vence,k.serie,
					  k.menudeo,k.unidxcont,k.unid,k.cont,
					  k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
					  k.pvo,k.condventa,k.mproducto);
              }	
	  }


        /*++++++++++++ FX ++++++++++++++++++++*/

        function MostrarAjax(){
            //id("ajax-icon").setAttribute("src","img/ajax-loader.gif");
            //id("download-icon").setAttribute("src","img/mundo1.gif");
	}

        function OcultarAjax(){
            //id("ajax-icon").setAttribute("src","");
            //id("download-icon").setAttribute("src","");
	}

        /*++++++++++++++++++ Utilitarios Interfast TPV ++++++++++++++++++++++*/

        function habilitarAddMProducto(){
	    var modo = id("rgModosTicket").value;
	    if( modo == "mproducto" && IdMProducto == 0 ){
		alert( c_mproducto + "\n  - Elije un MixProducto, para seguir.");
		return true;
	    }
	}

        function habilitarMProducto(){

	    if( parseInt(IdMProducto) != 0 && parseInt(IdMetaProducto) !=0 )
		if ( ticketlist.length != 0 )
		    if(confirm('¿Borrar Ticket MProducto - '+IdMProducto+' -?')){
			generadorEliminaMProducto(IdMetaProducto,IdMProducto);//Elimina Item MProducto
			setStatusMProducto(IdMetaProducto,'Cancelado');//Update Cancelado
		    }
	}

        function habilitarPresupuesto(Opcion){

	    //alert("gPOS: habilita presupuesto ID: "+IdPresupuesto+" Opcion "+Opcion+" Tipo pre:"+IdTipoPresupuesto );
	    
	    if( IdPresupuesto !=0 && IdTipoPresupuesto !=0 ){

		var TipoPresupuesto = getTipoPresupuesto(IdTipoPresupuesto); 		

		switch( Opcion ){
		case 0:
		    Opcion = 'Confirmado';
		    generadorEliminaPresupuesto(TipoPresupuesto,IdPresupuesto);
		    break;
		case 1:
		    //Borrar
		    if ( ticketlist.length != 0 )
			if(confirm('¿Quiere borrar este Ticket '+TipoPresupuesto+' ?')){
			    Opcion = 'Cancelado';
			    generadorEliminaPresupuesto(TipoPresupuesto,IdPresupuesto);
			}
		    break;
		case 2:
		    //Guardar
		    Opcion = 'Modificado';
		    generadorEliminaPresupuesto(TipoPresupuesto,IdPresupuesto);
		    break;

		case 3:
		    //Proforma
		    Opcion = 'Vencido';
		    break;

		default:
		    return;
		}
		//Trae detalle presupusto
		if(Opcion!='')
		    setStatusPresupuesto(IdPresupuesto,Opcion);

		//IdPresupuesto      = 0;
		//IdTipoPresupuesto  = 0;
		selTipoPresupuesto(0);
		//return;

	    }
	}

        function deshabilitarControles(){
	    id("CB").setAttribute("disabled","true"); 
	    id("REF").setAttribute("disabled","true"); 
	    id("NOM").setAttribute("disabled","true"); 
	    id("listaProductos").setAttribute("disabled","true");
	}

        function habilitarControles(){
	    id("CB").removeAttribute("disabled"); 
	    id("REF").removeAttribute("disabled"); 
	    id("NOM").removeAttribute('disabled'); 
	    id("listaProductos").removeAttribute("disabled");
	}

        function cambiarModoDevolucion(){
	    var r_mprod = id("rMProducto");
	    var r_pedid = id("rPedido");
	    var r_cesio = id("rCesion");
	    var r_venta = id("rVenta");
	    var modo    = id("rgModosTicket");	   
	    r_mprod.setAttribute('selected','true');
	    r_venta.removeAttribute('selected');
	    r_cesio.removeAttribute('selected');
	    r_pedid.removeAttribute('selected');
	    modo.setAttribute('value','mproducto');
	    NuevoModo();
	}

        function VolverVentas(){	
	    id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);
	    id("boxComprobantesVenta").setAttribute("collapsed",true);
	    id("checkReservaEntregago").checked = false;
	    id("modoDeAbonoTicket").value = 1;
	    VerFechaReservaEntregado(true);
	    VerVentas();
	}

        function VerVentas(){

	    id("FechaBuscaVentas").value =id("FechaBuscaVentas").value;
	    id("FechaBuscaVentasHasta").value = id("FechaBuscaVentasHasta").value;	
	    id("panelDerecho").setAttribute("collapsed",true);
	    id("boxComprobantesVenta").setAttribute("collapsed",false);
	    id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);	
	    setTimeout('BuscarVentas()',400);
	    if(ientidadfinanciera == 0) RegenCuentasBancarias();
	    id("NombreClienteBusqueda").focus();
	    resizelistboxticket(false); 
	}

        function VerCaja(){

	    if( !Local.esCajaTPV) return;
	    
	    id("panelDerecho").setAttribute("collapsed",true);
	    id("modoVisual").setAttribute("selectedIndex",Vistas.caja);	
	    frameArqueo.RegenPartidas('Aportacion');
	    frameArqueo.RegenPartidas('Sustraccion');
	    frameArqueo.RegenPartidas('Ingreso');
	    frameArqueo.RegenPartidas('Gasto');
	    //Sync
	    if( Local.esSyncCaja )
		setTimeout("runsyncTPV('Caja')",800);
	    resizelistboxticket(false);
	}

        function reloadCaja(){
	    //Recargar Caja*****

 	    //Check Conecction
	    if(syncCheckConnection()) return;

	    mostrarMensajeTPV(1,'Sincronizando caja...',3200);
	    Local.esSyncCaja = false;

	    frameArqueo.esRecibidaListaArqueos = true; 
	    frameArqueo.onLoadFormulario();
        }

        function VerListados(){
            id("panelDerecho").setAttribute("collapsed",true);
            id("modoVisual").setAttribute("selectedIndex",9);	
	    resizelistboxticket(false);
	}

        function esCajaCerrada(){
	    var	url = "services.php?modo=cajaescerrado";
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
            Local.esCajaCerrada = xrequest.responseText;
	    return Local.esCajaCerrada;
	}
           
        function habilitabotonvender(xval){

	    Local.esCajaCerrada = xval;

	    if( Local.esCajaCerrada == 1) id("botonImprimir").setAttribute("disabled",true);
	    if( Local.esCajaCerrada == 0) id("botonImprimir").removeAttribute("disabled");
	}

        function VerTPV(){
 	    id("panelDerecho").setAttribute("collapsed",false);
	    id("boxComprobantesVenta").setAttribute("collapsed",true);
	    id("boxServicios").setAttribute("collapsed",true);	    
	    id("modoVisual").setAttribute("selectedIndex",Vistas.tpv);	
	    id("NOM").focus();

	    //Actualiza lista productos 
	    resizelistboxticket(true);
            cIdSuscripcion = 0;
	}

        function resizelistboxticket( xw ){
	    var xvwlist = ( xw )? 1:0;
	    id("listadoTicket").setAttribute("flex",xvwlist);
	    id("listaProductos").setAttribute("flex",xvwlist);
	    id("clientPickArea").setAttribute("flex",xvwlist);
	    resizelistboxcliente( false );
	}
        function resizelistboxcliente( xw ){
	    var xvwlist = ( xw )? 1:0;
	    id("clientPickArea").setAttribute("flex",xvwlist);
	}

        function CBFocus(){ id("CB").focus(); }

        function SalirNice(){ parent.window.document.location.href="logout.php"; }

        function SalirTPV(){ window.close(); }

         function BotonCancelarVenta(){
	     habilitarControles(); 
	     document.getElementById('busquedaVentas').removeAttribute('disabled');
	     VaciarDetallesVentas(); 
	     document.getElementById('NumeroDocumento').removeAttribute('disabled'); 
	     CancelarVenta();
	 }

         function cajacerrada(){
             alert( c_gpos + "\n - DEBE ABRIR CAJA");
	 }

        function habilitarMensajePrivado(op){

	    switch (op) {
	    case 'pedidos':		
		if (id("rPedido").selected == true){
		    id("tituloNuevoMensaje").value = 'Proforma';
		    id("localDestino").setAttribute('collapsed',true);
		    id("localDestinoLabel").setAttribute('collapsed',true);
		    id("boxtituloNuevoMensaje").setAttribute('collapsed',true);
		    id("tituloNuevoMensaje").setAttribute('readonly',true);
		    id("EnviarMensajePrivado").setAttribute('collapsed',true);
		    id("CancelarMensajePrivado").setAttribute('collapsed',true);
 		    id("adelantoProformabox").setAttribute('collapsed',false);
		    id("filaFechaEntregaProforma").setAttribute('collapsed',false);
		    id("ventasButton").setAttribute('collapsed',true);
		    id("vigenciaProformabox").setAttribute('collapsed',false);
		    id("btnEscribirMensajes").setAttribute('label',' Observaciones');
		    id("tituloVisualMensaje").setAttribute('label','Observaciones:');
		    //if( id("rMProducto").collapsed == false )
		    id("vboxserieMProducto").setAttribute('collapsed',false);
		}
		break;
	    case 'e_pedidos':		
		if (id("rPedido").selected == true)
		    id("tituloNuevoMensaje").value = 'Proforma '+id("NumeroDocumento").value;
		id("tituloNuevoMensaje").value = 'Proforma '+id("NumeroDocumento").value;
		break;
		
	    default:
		id("tituloNuevoMensaje").value = '';
		id("localDestino").setAttribute('collapsed',false);
		id("localDestinoLabel").setAttribute('collapsed',false);
		id("EnviarMensajePrivado").setAttribute('collapsed',false);
		id("CancelarMensajePrivado").setAttribute('collapsed',false);
		id("tituloNuevoMensaje").removeAttribute('readonly');
		id("boxtituloNuevoMensaje").setAttribute('collapsed',false);
		id("vboxserieMProducto").setAttribute('collapsed',true);
 		id("adelantoProformabox").setAttribute('collapsed',true);
		id("filaFechaEntregaProforma").setAttribute('collapsed',true);
		id("ventasButton").setAttribute('collapsed',false);
		id("vigenciaProformabox").setAttribute('collapsed',true);
		id("btnEscribirMensajes").setAttribute('label',' Escribir mensaje');
		id("tituloVisualMensaje").setAttribute('label','Mensaje');
		id("tituloNuevoMensaje").setAttribute('collapsed',false);
		break;
	    }
	}


        function AjustarEtiquetaModo(){
	    var extatus = id("rgModosTicket").value;	
	    var r_mprod = id("rMProducto");
	    var r_pedid = id("rPedido");
	    var r_cesio = id("rCesion");

	    habilitarMensajePrivado();

	    switch (extatus) {
            case 'venta':		
		id("etiquetaTicket").setAttribute("label",  po_txtTicketVenta  );
		ModoDeTicket = "venta";
		habilitarControles();
		break;
            case 'cesion': 		
		id("etiquetaTicket").setAttribute("label", po_txtTicketCesion );
		r_pedid.setAttribute("collapsed", "true");//Oculta Pedidos
		r_mprod.setAttribute("collapsed", "true");//Oculta Mproductos
		ModoDeTicket = "cesion";
		habilitarControles();
		break;
            case 'pedidos': 		
		id("etiquetaTicket").setAttribute("label", po_txtTicketPedido );
		r_mprod.setAttribute("collapsed", "true");//Oculta Mproductos
		r_cesio.setAttribute("collapsed", "true");//Oculta Cesion
		ModoDeTicket = "pedidos";
		habilitarControles();
		habilitarMensajePrivado('pedidos');
		id("fechaEntregaProforma").value = calcularFechaActual('fecha');
		id("horaEntregaProforma").value = calcularFechaActual('hora');
		cFechaProforma = id("fechaEntregaProforma").value+" "+id("horaEntregaProforma").value;
		break;
            case 'mproducto': 		
		id("etiquetaTicket").setAttribute("label", po_txtTicketMProducto );
		ModoDeTicket = "mproducto";
		habilitarControles();
		break;
            case 'interno': 		
		id("etiquetaTicket").setAttribute("label", po_txtTicketServicioInterno);
		ModoDeTicket = "interno";			
		habilitarControles();
		break;
	    }

	    id("modoDePagoTicket").value = 1;

	}

        function  BorrarMProductoTPV(){

	    habilitarMProducto();//Borrar mproducto 'Cancelado'
	    habilitarControles();
	    habilitarPresupuesto(1);
	    document.getElementById('busquedaVentas').removeAttribute('disabled');
	    VaciarDetallesVentas(); 
	    document.getElementById('NumeroDocumento').removeAttribute('disabled');
	    CancelarVenta();
	    selTipoPresupuesto(0);//Reset PreVenta
	    AjustarEtiquetaMetaproducto();//Reset MetaProductos
	}

        function BorrarVentaTPV(){
	   
	    habilitarPresupuesto(1);
	    habilitarControles();
	    document.getElementById('busquedaVentas').removeAttribute('disabled');
	    VaciarDetallesVentas(); 
	    document.getElementById('NumeroDocumento').removeAttribute('disabled');
	    CancelarVenta();
	}

        function GuardarVentaTPV(){
	    habilitarControles();
	    document.getElementById('busquedaVentas').removeAttribute('disabled');
	    VaciarDetallesVentas(); 
	    document.getElementById('NumeroDocumento').removeAttribute('disabled');
	    CancelarVenta();
	}

        function ingresoAdelantoDineroCaja(cantidad,nroDocumento){

            var concepto = "Adelanto Proforma Nro. "+nroDocumento;
	    var url      = "modulos/arqueo/arqueoservices.php"
            var data     = "&modo=hacerIngresoAdelantoDinero&cantidad="+escape(cantidad)+
		           "&concepto="+encodeURIComponent(concepto)+
		           "&xidu="+Local.IdDependiente+
		           "&r=" + Math.random();
	    
	    var xrequest = new XMLHttpRequest();
	    
	    xrequest.open("POST",url,false);
	    xrequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
	    xrequest.send(data);		 
	}

        /*++++++++++++++++++ Mensajes +++++++++++++++++********/

        function insertAfter(parent, node, referenceNode) {
            parent.insertBefore(node, referenceNode.nextSibling);
	}

        function AgnadirMensaje(IdMensaje, Titulo, Status ){

            IdMensaje = parseInt( IdMensaje );
            if (!IdMensaje)
		return;	
            if (IdMensaje> ultimoLeido)
		ultimoLeido = IdMensaje; 

            var imagen;
            var xmen = document.createElement("listitem");
            xmen.setAttribute("class","listitem-iconic");
	    imagen   = (Status == "Normal")? "img/gpos_tpvmensaje.png":"img/gpos_tpvmensaje_alert.png";

            xmen.setAttribute("image",imagen);
            xmen.setAttribute("label", Titulo);
            xmen.setAttribute("ondblclick", "EncargarLecturaMensaje('"+IdMensaje+"')" );
            //buzonMensajes.appendChild( xmen );
            //id("guardianMensajes").insertBefore(xmen)	
            //buzonMensajes.insertAfter(xmen, id("guardianMensajes") );
            insertAfter( buzonMensajes, xmen, id("guardianMensajes") );	
	}

        function ProcesarNuevosMensajes( rawtext){
            var dato, row;	
            var filadatos = rawtext.split("\n");

            for(var t=0;t<filadatos.length;t++){
		dato	= filadatos[t];
		row = dato.split("'");	

		AgnadirMensaje( row[0], row[1], row[2]);
            }	
	}

        function RececepcionMensajes(){
            if (!AjaxMensajes) {
		AjaxMensajes = new XMLHttpRequest();
		return;
            }
	    
            ocupado = 0;
            if (AjaxMensajes.readyState==4) {

		if (AjaxMensajes){
                    if (AjaxMensajes.status=="200")
			peticionesSinRespuesta = 0;
		    else{
			peticionesSinRespuesta = peticionesSinRespuesta +1;			
			return;
		    }
		}
		//Si responden, es que estamos online, por tanto "hay respuesta"
		// y borramos el acumulativo de peticiones sin respuesta. 

		var rawtext = AjaxMensajes.responseText;			
		//alert(rawtext);
		//alert(AjaxMensajes.status);
		ProcesarNuevosMensajes(rawtext);		
            }
	}

        function EncargarLecturaMensaje(IdMensaje){

            IdMensaje = parseInt(IdMensaje);
            var url = "modulos/mensajeria/modbuzon.php?modo=CargarMensaje&IdMensaje="+IdMensaje;
	    
            AjaxMensajes.open("POST",url,true);
            AjaxMensajes.onreadystatechange = CargarMensaje;
            AjaxMensajes.send(null)
	    
	}

        function CargarMensaje(){
	    
            ocupado = 0;
            if (AjaxMensajes.readyState==4) {
		var rawtext = AjaxMensajes.responseText;	
		if (rawtext=="error"){
                    return;		
		}
		RecibirMensajeCompleto(rawtext);				
            }
	}

        function RecibirMensajeCompleto(rawtext){
            if (rawtext=="error")
		return;		
            var row = rawtext.split("'");	
	    
            var IdMensaje = parseInt(row[0]);
            if (!IdMensaje) return;
	    
            var Titulo	= row[1];
            var Status	= row[2];
            var Texto	= row[3];
            VisualizarMensaje( IdMensaje, Titulo,Status, Texto);
	}

        function VisualizarMensaje(IdMensaje, Titulo, Status,Texto ){
            IdMensaje = parseInt( IdMensaje );
            if (!IdMensaje)
		return;	
	    
            id("tituloVisual").value = Titulo;		
            id("textoVisual").value = Texto;
	    
            mensajesModoLeer();
	}

        function mensajesModoLeer(){
            var mensajeArea =  id("modoMensajes");
            mensajeArea.setAttribute("selectedIndex",1);
	}

        function mensajesModoRecepcion(){
            var mensajeArea =  id("modoMensajes");
	    
            id("tituloVisual").value = "";
            id("textoVisual").value = "";
            mensajeArea.setAttribute("selectedIndex",0);	
	}

        function ToggleMensajes(){
            var mensajeArea =  id("modoMensajes");

            var modo = mensajeArea.getAttribute("selectedIndex");

            if (modo==2){
                mensajeArea.setAttribute("selectedIndex",0);
            } else {
                mensajeArea.setAttribute("selectedIndex",2);
            }
        }

        function EnviarMensajePrivado(){
	    var modo = id("rgModosTicket").value;
            var xrequest = new XMLHttpRequest();
            var resultado;
            var url = "";

            var local = parseInt(id("localDestino").value);
            if (!local) return;
            var titulo = id("tituloNuevoMensaje").value;
            var texto = id("cuerpoNuevoMensaje").value;
	    //vigencia mensaje
	    var vigencia = parseInt(id("vigenciaProforma").value);
	    if(vigencia < 1 || isNaN(vigencia) ) vigencia = 1;

            url = "modulos/mensajeria/modbuzon.php?modo=avisonotaprivada";

            var data = "&titulo="+encodeURIComponent( titulo );
            data = data + "&cuerpo="+encodeURIComponent( texto + "\n( "+ Local.nombreDependiente + " )" );
            data = data + "&idestino="+ encodeURIComponent( local )+ "&vigencia="+vigencia;

            xrequest.open("POST",url,false);
            xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');

            try {
                xrequest.send(data);
                resultado = xrequest.responseText;
            } catch(e) {
                //NOTA: posiblemente no tenemos conexion.
                resultado = false;	
            }

	    //if( modo != "pedidos" )
            //alert( c_gpos + po_mensajeenviado);

            if ( resultado == "OK" )
		setTimeout("syncMensajes()",600);//Mensajes
            else 
                alert( c_gpos + po_servidorocupado);

            ToggleMensajes();

        }


        function AdjuntarObservacionesPedido(modo,p_vigencia,p_nsmprod,p_adelanto){

	    //PEDIDOS************ PROFORMA
	    var c_mensaje  = id("cuerpoNuevoMensaje").value;
	    //var p_nsmprod  = id("serieMProducto").value;
	    //var p_adelanto = id("adelantoProforma").value;
	    var p_enlugar  = id("lugarEntregaProforma").value;
	    var p_enfecha  = id("fechaEntregaProforma").value;
	    var p_enhora   = id("horaEntregaProforma").value;
	    var res = p_enfecha.split("-");

	    if(cFechaProforma >= p_enfecha+" "+p_enhora){
		res = calcularFechaActual('fecha').split("-");
		p_enhora = calcularFechaActual('hora');
	    }

	    p_enfecha = res[2]+'/'+res[1]+'/'+res[0];

	    //var p_vigencia = id("vigenciaProforma").value;
	    var mp_mensaje ='';
	    var ad_mensaje ='';
	    var el_mensaje ='';
	    var ef_mensaje ='';
	    var vi_mensaje ='';
	    var ot_mensaje ='';
	    var m_envia    = 0;

	    //Meta Producto
	    if( p_nsmprod != '')
	    {
		var mp_mensaje = '- MProducto: '+ p_nsmprod; 
		m_envia = 1;
	    }

	    //Adelanto	
	    if( parseFloat(p_adelanto) > 0)
	    {
		var ad_mensaje = '\n- Adelanto: '+p_adelanto+' '+cMoneda[1]['TP'];
		m_envia = 1;
	    }
	    
	    //Lugar entrega
	    if( p_enlugar != '')
	    {
		var el_mensaje = '\n- Lugar Entrega: '+p_enlugar;
		m_envia = 1;
	    }

	    //Vigencia
	    if( parseInt(p_vigencia) > 0)
	    {
		var vi_mensaje = '\n- Vigencia: '+p_vigencia+' día(s)';
		m_envia = 1;
	    }

	    //Otros
	    if( trim(c_mensaje) != '-')
	    {
		var ot_mensaje = '\n'+c_mensaje;
		m_envia = 1;
	    }

	    //VALIDA ENVIO MENSAJE	
	    if( m_envia == 1 )
	    {
		//Fecha entrega
		var ef_mensaje = '\n- Fecha Entrega: '+p_enfecha+' '+p_enhora;

 		//EL MENSAJE
		var all_mensaje = mp_mensaje+''+
		    ad_mensaje+''+
		    el_mensaje+''+
		    ef_mensaje+''+
		    ot_mensaje;

		//Carga Local
		id("localDestino").value=Local.IdLocalActivo;
		
		//Carga Mensaje
		id("cuerpoNuevoMensaje").value = all_mensaje+''+vi_mensaje;

		//ENVIA EL MENSAJE
		EnviarMensajePrivado();

		//Limpia TextBox
		id("cuerpoNuevoMensaje").value='-';
		id("serieMProducto").value='';
 		id("adelantoProforma").value='0';
		id("lugarEntregaProforma").value='';
		//id("fechaEntregaProforma").value='0000-00-00';
		//id("horaEntregaProforma").value='00:00:00';
		id("vigenciaProforma").value='0';
 		//EL MENSAJE
		return all_mensaje;
	    } 
	    else 
		return 0;
	}

        /*++++++++++++++++++ Tipo Venta Dependiente ++++++++++++++++++*/ 

        function cambiarTipoVenta(xthis){
	    
	    if( Local.TPV == xthis.value ) return;

            //limpiamos lista de precios
	    VaciarListadoProductos()

	    var xtv;
	    switch(xthis.value){
	    case 'VC': xtv = "rc";break;
	    case 'VD': xtv = "rd";break;
	    default: return;
	    }

	    if( !setTipoVentaDependiente(xtv) ) {
		id("depTipoVentaVC").setAttribute("checked", (xthis.value == 'VD'));
		id("depTipoVentaVD").setAttribute("checked", (xthis.value == 'VC'));
		return;
	    }
	    Local.TPV = xthis.value;
	    id('depTipoVenta').setAttribute('label',xthis.label);
	    id("depTipoVentaVC").setAttribute("checked", (xthis.value == 'VC'));
	    id("depTipoVentaVD").setAttribute("checked", (xthis.value == 'VD'));
	    //Actualizar Titulo TPV
	    id("window-tpv").setAttribute('title','gPOS '+Local.NegocioTipoVenta+' // '+xthis.label);
	    //mostrarMensajeTPV(1,'Cargando cambios de '+trim(xthis.label)+'...',2200);

	    //Limpia TPV
	    selTipoPresupuesto(0);
	    VerTPV();    
	    //Recargar Preventa,Clientes,Promociones,Mensajes
	    syncTipoVentaTPV();
	    //Muestra mensaje en TPV

	    //actualiamos la vista del listado
	    Local.ListaProductoViewPVC = true;
	    setViewListaProductoPrecios();
	    //listamos busqueda actual
	    agnadirPorNombre();
	} 

        function setTipoVentaDependiente(xvalue){

 	    //Check Conecction
	    if(syncCheckConnection()) return false;

	    var xres,url,prod,xjsOut,z;
	    z   = null;	    
	    
	    url = 
		"services.php?"+
		"modo=setTipoVentaDependiente&"+
		"xtipoventa="+xvalue;
	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
	    } catch(z){
		return false;
	    }

            xjsOut  = AjaxDemon.responseText;
	    //alert(xjsOut);
	    if( trim(xjsOut) == xvalue ) return true;
	    return false;//OK, detiene el proceso.
	}

        /*++++++++++++++++++ Local Dependiente +++++++++++++++++++++*/

        function cambiaLocalDependiente(dlocal){

	    //Limipa registros en carrito
	    //CancelarVenta();

	    //carga nuevo id local dependiente
	    setIdLocalDependiente(dlocal);
	    //Reinicia todo con nuevo id 
            setTimeout("location.reload()",50);	
	}

        function setIdLocalDependiente(id){
	    var	url = 
		"services.php?"+
		"modo=setIdLocalDependienteTPV&"+
		"id="+id;

	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    xrequest.responseText;
	    //Local.IdLocalDependiente = parseInt( xrequest.responseText );
	}


        /*++++++++++++++++++++ tools ++++++++++++++++++++++*/

        /* Devuelve una coletilla aleatoria */
        function ApendRand(){ 
	    return "r="+ Math.random();
	}  	

        function unique(arr) {
	    var i,len=arr.length,out=[],obj={};
	    for (i=0;i<len;i++) {
		obj[arr[i]]=0;
	    }
	    for (i in obj) {
		out.push(i);
	    }
	    return out;
	}

         function Nombre2Lex(nombre){
             var len = L.length;
             for(var t=0;t<len;t++){
		 if (L[t]==nombre) return nombre;
             }
             L[t] = nombre;
             return t;
	 }

         function unescapeHTML(codigoHtml) {
             var s = new String(codigoHtml);
             s = s.replace (/&amp;/g, "&");
             s = s.replace (/&Ntilde;/g, "\xf1");
             s = s.replace (/&ntilde;/g, "\xf1");
             return s;

             /*var div = document.createElement('div');
               div.innerHTML = codigoHtml;

               if (div.innerText)
               return div.innerText;

               return div.childNodes[0] ? div.childNodes[0].nodeValue : codigoHtml;*/
         }


        function CleanMoney(cadena) {
            return parseMoney(new String(cadena) );
	}

        function parseMoney (cadena) {
            //var cadoriginal = cadena;
            if (!cadena) {
		cadena = new String( cadena );
		if( !cadena.replace ){
                    return 0.0;		 	
		}
            }
	    
            /*	if (cadena.replace){	
		cadena = cadena.replace(/\./g,"");
		cadena = cadena.replace(/\,/g,".");
		}*/
	    
            cadena = parseFloat( cadena );	

            if (isNaN( cadena ))
		return 0.0;
	    
            return cadena;
	}

        function CleanInpuesto( iva ) {
            if (!iva)	return 0;
	    
            iva = iva.replace(/\%/g,"");
            iva = iva.replace(/ /g,"");
	    
            if (isNaN(iva))
		return 0;	
            return iva;
	}

        function toNegativo(unidades){
            return (unidades<0)?unidades:0 - unidades;
	}


        function CleanCB(cadena){
            var cad = new String(cadena);
            cad = cad.toUpperCase();
            cad = trim(cadena);
            return cad;
        }

        function CleanRef(cadena){
            var cad = new String(cadena);
            cad = cad.toUpperCase();
            cad = trim(cadena);
            return cad;
        }

        function trim(cadena) { 
            cadena = new String(cadena);
            for(i=0; i<cadena.length; ) { 
                if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
                    cadena=cadena.substring(i+1, cadena.length); 
                else 
                    break; 
            } 
            for(i=cadena.length-1; i>=0; i=cadena.length-1) { 
                if(cadena.charAt(i)==" " || cadena.charAt(i)=="\t" ) 
                    cadena=cadena.substring(0,i); 
                else 
                    break; 
            } 
            return cadena; 
        }

function convertirNumLetras(number){

	    var cad, millions_final_string, thousands_final_string, centenas_final_string, descriptor; 
	    //number = number_format (number, 2);
	    var number1=number.toString();
	    //settype (number, "integer");
	    var cent = number1.split(".");   
	    var centavos = cent[1];
	    //Mind Mod
	    var number=cent[0];
	    if (centavos == 0 || centavos == undefined)
	    {
		centavos = "00";
	    }
	    if (number == 0 || number == "") 
	    { // if amount = 0, then forget all about conversions, 
		centenas_final_string=" cero "; // amount is zero (cero). handle it externally, to 
		// function breakdown 
	    } 
	    else 
	    { 
		var millions  = ObtenerParteEntDiv(number, 1000000); // first, send the millions to the string 
		number = mod(number, 1000000);           // conversion function 
		
		if (millions != 0)
		{                      
		    // This condition handles the plural case 
		    if (millions == 1) 
		    {              // if only 1, use 'millon' (million). if 
			descriptor= " millon ";  // > than 1, use 'millones' (millions) as 
		    } 
		    else 
		    {                           // a descriptor for this triad. 
			descriptor = " millones "; 
		    } 
		} 
		else 
		{    
		    descriptor = " ";                 // if 0 million then use no descriptor. 
		} 
		var millions_final_string = string_literal_conversion(millions)+descriptor; 
		thousands = ObtenerParteEntDiv(number, 1000);  // now, send the thousands to the string 
		number = mod(number, 1000);            // conversion function. 
		//print "Th:".thousands;
		if (thousands != 1) 
		{                   // This condition eliminates the descriptor 
		    thousands_final_string =string_literal_conversion(thousands) + " mil "; 
		    //  descriptor = " mil ";          // if there are no thousands on the amount 
		} 
		if (thousands == 1)
		{
		    thousands_final_string = " mil "; 
		}
		if (thousands < 1) 
		{ 
		    thousands_final_string = " "; 
		} 
		// this will handle numbers between 1 and 999 which 
		// need no descriptor whatsoever. 
		centenas  = number;                     
		centenas_final_string = string_literal_conversion(centenas) ; 
	    } //end if (number ==0) 

	    /*if (ereg("un",centenas_final_string))
	      {
	      centenas_final_string = ereg_replace("","o",centenas_final_string); 
	      }*/
	    //finally, print the output. 

	    /* Concatena los millones, miles y cientos*/
	    cad = millions_final_string+thousands_final_string+centenas_final_string; 
	    /* Convierte la cadena a MayÃºsculas*/
	    cad = cad.toUpperCase();       
	    if (centavos.length>2)
	    {  
		if(centavos.substring(2,3)>= 5){
		    centavos = centavos.substring(0,1)+(parseInt(centavos.substring(1,2))+1).toString();
		}   else{
		    
		    centavos = centavos.substring(0,1);
		}
	    }

	    /* Concatena a los centavos la cadena "/100" */
	    if (centavos.length==1)
	    {
		centavos = centavos+"0";
	    }
	    centavos = centavos+ "/100"; 


	    /* Asigna el tipo de moneda, para 1 = PESO, para distinto de 1 = PESOS*/

            moneda = (number == 1)? cMoneda[1]['T']:cMoneda[1]['TP'];
            /*
	    if (number == 1)
	    {
		moneda = " SOL ";  
	    }
	    else
	    {
		moneda = " SOLES  ";  
	    }
	    */
	    /* Regresa el nÃºmero en cadena entre parÃ©ntesis y con tipo de moneda y la fase M.N.*/
	    //Mind Mod, si se deja MIL  y se utiliza esta funciÃ³n para imprimir documentos
	    //de caracter legal, dejar solo MIL es incorrecto, para evitar fraudes se debe de poner UM MIL 
	    if(cad == '  MIL ')
	    {
		cad=' UN MIL ';
	    }
	    // alert( "FINAL="+cad+moneda+centavos+" M.N.");
	    return cad+" CON "+centavos+" "+moneda;
	}

        function aPCB(vcb){
            raw_agnadirPorCodigoBarras(new String(vcb),true);
	}


       function validaMProductoTicket(){

	    var nsmprod = id("serieMProducto").value.replace(' ','')
	    var v_nsmp = new Array();
	    var i_m = '';
	    if(nsmprod!=''){
		var nsmp = nsmprod.split(',');
		//Buscar
		for (var r in nsmp){ 

		    for (var q=0; q<aPedido.length; q++) {

			var it = aPedido[q].split(':');
			if( 'MProducto' == it[0] ){
			    if( parseInt(nsmp[r]) == parseInt(it[1]) ){
				if(id("tic_"+it[4]))
				    v_nsmp.push(nsmp[r]);
				else
				    i_m += "\n    - "+nsmp[r];
			    }
			}
		    }
		}
		if(i_m!='')
		    alert( c_gpos + '\n'+
			  '     Los codigos mproductos listados, no estan'+
			  ' relacionado al Ticket Actual.\n'+
			  '     Liste los productos que corresponda al codigo mproducto.\n'+i_m);
		//Refrescaos los datos
		id("serieMProducto").value =  v_nsmp.join(',');
		return  v_nsmp.join(',');
	    }
	    return id("serieMProducto").value = '';
	}


        function resetPresupuestoCarrito(){
	    var MANTENER_MODO = true;
	    id("comprobante").collapsed='false';
	    id("NumeroDocumento").readonly="false";
	    
	    AjustarEtiquetaModo();
	    CancelarVenta(MANTENER_MODO);
	    TicketAjusta();
	}

        function esOffStockPreventa(){
 	    //Variable global presupuesto
	    IdPresupuesto = 0;  
	    //reset modo
	    //alert(IdTipoPresupuesto);
	    var tipopresupuesto = getTipoPresupuesto(IdTipoPresupuesto); 
	    id("Sel"+tipopresupuesto).setAttribute("label", "Elije ticket....");
	    resetPresupuestoCarrito();
	    
            if ( id("prevt-stock").getAttribute("checked") == "true"){
		id("buscar-servidor").setAttribute("checked","false");//Cargamos Todos los Productos
 	    }
	    else {
		esOfflineBusquedas();
		id("buscar-servidor").setAttribute("checked","true");
	    }
	    
	}



        /*+++++++++++++ Crea Listas, Fichas, Array en PreVentas  +++++++++++++*/

        tpv.AddCarrito = function (codigobarras,unidades) 
        {
	    
  	    var modo   = id("rgModosTicket").value;//MProducto

            if (!pool.Existe(codigobarras)) return false;

            setImagenProducto(codigobarras);

            pool.select(codigobarras);

 	    var precio      = ( Local.TPV=='VC'    )? pool.get().pvc:pool.get().pvd;
 	    var pool_precio = ( modo == "mproducto")? obtenerPrecioBaseMProducto( pool.get().costo ) : precio;

            this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool_precio,
			 pool.get().impuesto,unidades,pool.get().talla, pool.get().color, 
			 pool.get().descuento,0);
            return true;
	}

        tpv.AddCarritoCorporativo = function (codigobarras,unidades) 
        {
	    
  	    var modo   = id("rgModosTicket").value;//MProducto

            if (!pool.Existe(codigobarras)) return false;

            setImagenProducto(codigobarras);

            pool.select(codigobarras);

 	    var precio      = ( Local.esB2B == 1 )? pool.get().pvc:pool.get().pvd;
 	    var pool_precio = ( modo == "mproducto")? obtenerPrecioBaseMProducto( pool.get().costo ) : precio;
	    //Actualizamos precios listados
	    if(ticket[codigobarras] ){
		ticket[codigobarras].precio = pool_precio;
		id("tic_precio_"+ codigobarras).value= ticket[codigobarras].precio;}

            this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool_precio,
			 pool.get().impuesto,unidades,pool.get().talla, pool.get().color, 
			 pool.get().descuento,0);
            return true;
	}

        tpv.AddCarritoMayoreo = function (codigobarras,unidades) 
        {
            if (!pool.Existe(codigobarras)) return false;
  	    var modo        = id("rgModosTicket").value;//MProducto
            setImagenProducto(codigobarras);
            pool.select(codigobarras);

	    var xcuantosemp = parseFloat( unidades/pool.get().unidxcont );
	    var ximpemp     = pool.get().pvemp * xcuantosemp.toFixed(2);//total base
	    var xprecio_emp = parseFloat( (ximpemp/unidades).toFixed(2) );
	    var xnewimpemp  = xprecio_emp*unidades;//total base redondeado
	    var xdscto_emp  = 0;
	    var xresto_emp  = ximpemp-xnewimpemp;

	    if( xresto_emp < 0)
	    {
		//EXESO
		//***calcular el descuento
		xdscto_emp = (-1)*xresto_emp;
	    }
	    else if( xresto_emp > 0)
	    {
		//DEFECTO
		//***calcular nuevo precio incrementando
		//***calcular el descuento
		xprecio_emp = parseFloat( xprecio_emp ) + parseFloat( 0.01 );
		xnewimpemp  = xprecio_emp*unidades;//total base redondeado
		xresto_emp  = ximpemp-xnewimpemp;
		xdscto_emp  = ( xresto_emp < 0 )? (-1)*xresto_emp:0;
	    }
	    if ( xdscto_emp > 0 )
	    {
		//Utilizamos el formato dscto del carrito tpv
		xdscto_emp = parseFloat(xdscto_emp);
		xdscto_emp = ( 100*xdscto_emp / xprecio_emp ) / unidades;
		xdscto_emp = xdscto_emp.toFixed(2);
	    }
	    else
		xdscto_emp = 0;
 
	    //CERO
	    //***cargar precio
	    var pool_dscto  = xdscto_emp ;
 	    var precio      = ( pool.get().pvemp > 0 )? xprecio_emp:pool.get().pvd;
 	    var pool_precio = ( modo == "mproducto" )? obtenerPrecioBaseMProducto( pool.get().costo ) : precio;
	    //Actualizamos precios listados
	    if(ticket[codigobarras] ){
		ticket[codigobarras].precio = pool_precio;
		ticket[codigobarras].descuento = pool_dscto;
		id("tic_precio_"+ codigobarras).value= ticket[codigobarras].precio;
		id("tic_descuento_"+ codigobarras).value= ticket[codigobarras].descuento;}

            this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool_precio,
			 pool.get().impuesto,unidades,pool.get().talla, pool.get().color, 
			 pool_dscto,0);
            return true;
	}

        tpv.AddCarritoDocena = function (codigobarras,unidades) 
        {
	    
            if (!pool.Existe(codigobarras)) return false;
            setImagenProducto(codigobarras);
            pool.select(codigobarras);
	    
  	    var modo        = id("rgModosTicket").value;//MProducto
 
	    var xcuantasdoc = parseFloat( unidades/12 );
	    var ximpdoc     = pool.get().pvdoce * xcuantasdoc;//total base  
	    var xprecio_doc = parseFloat( (ximpdoc/unidades).toFixed(2) );
	    var xnewimpdoc  = xprecio_doc*unidades;//total base redondeado
	    var xdscto_doc  = 0;
	    var xresto_doc  = ximpdoc-xnewimpdoc;

	    if( xresto_doc < 0)
	    {
		//EXESO
		//***calcular el descuento
		xdscto_doc = (-1)*xresto_doc;
	    }
	    else if( xresto_doc > 0)
	    {
		//DEFECTO
		//***calcular nuevo precio incrementando
		//***calcular el descuento
		xprecio_doc = parseFloat( xprecio_doc ) + parseFloat( 0.01 );
		xnewimpdoc  = xprecio_doc*unidades;//total base redondeado
		xresto_doc  = ximpdoc-xnewimpdoc;
		xdscto_doc  = ( xresto_doc < 0 )? (-1)*xresto_doc:0;
	    }
	    if ( xdscto_doc > 0 )
	    {
		//Utilizamos el formato dscto del carrito tpv
		xdscto_doc = parseFloat(xdscto_doc);
		xdscto_doc = ( 100*xdscto_doc / xprecio_doc ) / unidades;
		xdscto_doc = xdscto_doc.toFixed(2);
	    }
	    else
		xdscto_doc = 0;
 
	    //CERO
	    //***cargar precio
	    var pool_precio        = ( pool.get().pvdoce > 0 )? xprecio_doc:pool.get().pvd;
	    var pool_dscto          = xdscto_doc;

	    //Actualizamos precios listados
	    if(ticket[codigobarras] ){
		ticket[codigobarras].precio = pool_precio;
		ticket[codigobarras].descuento = pool_dscto;
		id("tic_precio_"+ codigobarras).value= ticket[codigobarras].precio;
		id("tic_descuento_"+ codigobarras).value= ticket[codigobarras].descuento;}
	    
	    //recalcula el Precio desde el importe i el descuento 
	    this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool_precio,
			 pool.get().impuesto,unidades,pool.get().talla, pool.get().color, 
			 pool_dscto,0);
            return true;
	}

        tpv.DelCarritoSerie = function (codigobarras,serie) {
	    this.DelCompraSerie(codigobarras,serie);
	    return true;
	}

        tpv.DelCompraSerie  = function (codigo,serie) {
	    if (pool.ExisteTicket(codigo)) {
		
		for (var k in ticket[codigo].series)
		{
		    if( ticket[codigo].series[k] == serie)
			ticket[codigo].series.splice(k,1);
 		}
		
	    }	
	}

        tpv.AddCarritoSerie = function (codigobarras,serie) {
	    this.CompraSerie(codigobarras,serie);
	    return true;
	}

        tpv.CompraSerie = function (codigo,serie) {
	    if (pool.ExisteTicket(codigo)) 
		ticket[codigo].series.push(serie);	
	}

        tpv.Compra = function (codigo,nombre,referencia,
			       precio,impuesto,unidades,talla,color,
			       descuento,idsubsidiario,nombre2) {

	    var modo  = id("rgModosTicket").value;//Mproducto	    
	    var nuevo = null;
	    
	    if (!pool.ExisteTicket(codigo)) 
	    {
                //Valida Tipo Ticket Presupuesto
                if( IdTipoPresupuesto != 0 )
                    if( parseInt( IdPresupuesto ) == 0 )
                        return selTipoPresupuesto(0);

		pool.CreaTicket(codigo);
		nuevo = 1;
	    }	


	    ticket[codigo].unidades += unidades;	
	    ticket[codigo].impuesto = impuesto;	
	    ticket[codigo].nombre   = nombre;	
	    ticket[codigo].referencia = referencia;
	    ticket[codigo].precio    = precio;
	    ticket[codigo].talla     = talla;
	    ticket[codigo].color     = color;
	    ticket[codigo].descuento = descuento;
	    ticket[codigo].idsubsidiario = idsubsidiario;
	    ticket[codigo].concepto      = nombre2;
	    //stock oferta?
	    var oferta    = ticket[codigo].oferta;
	    var xunidades = ticket[codigo].unidades;
	    var menudeo   = ticket[codigo].menudeo;
	    var cont      = ticket[codigo].cont;
	    var unid      = ticket[codigo].unid;
	    var unidxcont = ticket[codigo].unidxcont;
	    var lote      = ticket[codigo].lote;
	    var vence     = ticket[codigo].vence;
	    var series    = ticket[codigo].series;
	    var vdetalle  = ( menudeo )? '':productos[codigo].unid;
	    //Detalle
	    var xvence    = ( vence   )? vence[0].split(":")      : false;
	    var xlote     = ( lote    )? lote[0].split(":")       : false;
	    var vdetalle  = ( vence   )? ' FV:'+xvence[1]         : vdetalle;
	    var vdetalle  = ( lote    )? vdetalle+' LT:'+xlote[1] : vdetalle;
	    //Menudeo
	    var xresto    = ( menudeo )? xunidades%unidxcont              : false;
	    var xcant     = ( menudeo )? ( xunidades - xresto )/unidxcont : false;
	    var xmenudeo  = ( menudeo )? xcant+''+cont+'+'+xresto+''+unid : false;
	    var vdetalle  = ( menudeo )? ' '+xmenudeo+'  '+vdetalle       : vdetalle;
	    var srt       = ( vdetalle.length >80)? '...':'';
	    var cssdetalle = ( oferta )? 'font-weight: bold;text-align:right;':'text-align:right';
	    vdetalle      = ( vdetalle)? vdetalle.slice(0,80)+srt:''; 
	    //Status
	    var esSerie   = ( productos[codigo].serie )? 1:0;
	    var esLote    = ( lote   )? 1:0;
	    var esVence   = ( vence  )? 1:0;
	    var cStatus   = esSerie+'~'+esLote+'~'+esVence;
	    var tunid     = parseInt( ticket[codigo].unidades ); 
	    var ounid     = parseInt( ticket[codigo].ofertaunid );
	    var pvo       = parseFloat( ticket[codigo].pvo );

	    //*+++ Ofertas Stock +++*//
	    if( oferta ){

		oferta    = ( ounid >= tunid )? tunid:ounid;
		vdetalle  = '**OFERTA '+oferta+''+unid+' c/u '+formatDinero(pvo)+'** '+vdetalle;
		oferta    = tunid+'~'+ounid+'~'+precio+'~'+pvo;//uni:ofertaunid:pv:pvo
		precio    = ( ounid >= tunid )? pvo:(pvo*ounid+(tunid-ounid)*precio)/tunid;
		
		if (!nuevo) {
		    ticket[codigo].precio = precio.toFixed(2);
		    ticket[codigo].oferta = oferta;
		    
		    id("tic_precio_"+codigo).value = ticket[codigo].precio;
		    id("tic_oferta_"+codigo).value = ticket[codigo].oferta;
		}
		
	    }

	    ticket[codigo].vdetalle   = vdetalle;
	    ticket[codigo].cStatus    = cStatus;
	    ticket[codigo].oferta     = oferta;

	    //*+++ Nuevo +++++*//
	    if (nuevo) { //agnadimos	
		
		var xlistadoTicket = id("listadoTicket");				

		ticket[codigo].precio = precio;

		var xcod      = document.createElement("label");
		xcod.setAttribute("value",ticket[codigo].referencia);		
		xcod.setAttribute("id","tic_referencia_"+codigo);

		var xnombre   = document.createElement("label");
		xnombre.setAttribute("value",ticket[codigo].producto);	
		xnombre.setAttribute("id","tic_nombre_"+codigo);
		xnombre.setAttribute("crop","end");
		xnombre.setAttribute("style","width: 100px");

		var xproducto = document.createElement("label");
		xproducto.setAttribute("value",ticket[codigo].idproducto);	
		xproducto.setAttribute("id","tic_idproducto_"+codigo);
		xproducto.setAttribute("collapsed","true");

		var xunid     = document.createElement("label");
		xunid.setAttribute("value",ticket[codigo].unidades);			

		var xprecio   = document.createElement("label");
		xprecio.setAttribute("value",formatDinero(ticket[codigo].precio));	

		var ximporte  = document.createElement("label");
		ximporte.setAttribute("value",formatDinero(ticket[codigo].precio* ticket[codigo].unidades));	

		var xtalla    = document.createElement("label");
		xtalla.setAttribute("value",ticket[codigo].talla);	
		xtalla.setAttribute("id","tic_talla_"+codigo);
		xtalla.setAttribute("collapsed","true");		
		xtalla.setAttribute("style","width:300px");
		
		var xcolor     = document.createElement("label");
		xcolor.setAttribute("value",ticket[codigo].color);		
		xcolor.setAttribute("id","tic_color_"+codigo);		
		xcolor.setAttribute("collapsed","true");		

		var ximpuesto  = document.createElement("label");
		ximpuesto.setAttribute("value",impuesto);

		var xdescuento = document.createElement("label");
		xdescuento.setAttribute("value",FormateComoDescuento(ticket[codigo].descuento));		

		var xdetalle   = document.createElement("label");
		xdetalle.setAttribute("value", ticket[codigo].vdetalle);	
		xdetalle.setAttribute("id","tic_detalle_"+codigo);
		xdetalle.setAttribute("style",cssdetalle);

		var xpedidodet = document.createElement("label");
		xpedidodet.setAttribute("value",'');		
		xpedidodet.setAttribute("id","tic_pedidodet_"+codigo);		
		xpedidodet.setAttribute("collapsed","true");		

		var xstatus = document.createElement("label");
		xstatus.setAttribute("value",ticket[codigo].cStatus);		
		xstatus.setAttribute("id","tic_status_"+codigo);		
		xstatus.setAttribute("collapsed","true");		

		var xoferta = document.createElement("label");
		xoferta.setAttribute("value",ticket[codigo].oferta);		
		xoferta.setAttribute("id","tic_oferta_"+codigo);		
		xoferta.setAttribute("collapsed","true");		

		var xcosto = document.createElement("label");
		xcosto.setAttribute("value",ticket[codigo].costo);		
		xcosto.setAttribute("id","tic_costo_"+codigo);		
		xcosto.setAttribute("collapsed","true");		

		var xconcepto = document.createElement("label");
		xconcepto.setAttribute("value", ticket[codigo].concepto);
		xconcepto.setAttribute("id","tic_concepto_"+codigo);		
		xconcepto.setAttribute("collapsed","true");		

		if(idsubsidiario)
		{
		    var xsubsidiario = document.createElement("label");
		    xsubsidiario.setAttribute("value",ticket[codigo].idsubsidiario);
		    xsubsidiario.setAttribute("id","tic_subsidiario_"+codigo);
		}

		var xlistitem = document.createElement("listitem");	
		xlistitem.setAttribute("id","tic_"+codigo);
                xlistitem.setAttribute("oncontextmenu","seleccionarfilatpv("+codigo+",true)");
                
		xunid.setAttribute("id","tic_unid_"+codigo);
		xunid.style.textAlign ="right";

		xprecio.style.align ="right";
		xprecio.style.textAlign ="right";
		xprecio.setAttribute("id","tic_precio_"+codigo);

		ximporte.style.align ="right";
		ximporte.style.textAlign ="right";
		ximporte.setAttribute("id","tic_importe_"+codigo);

		ximpuesto.style.align ="right";
		ximpuesto.style.textAlign ="right";
		ximpuesto.setAttribute("id","tic_impuesto_"+codigo);

		xdescuento.style.align ="right";
		xdescuento.style.textAlign ="right";
		xdescuento.setAttribute("id","tic_descuento_"+codigo);

		xlistitem.appendChild( xcod);
		xlistitem.appendChild( xnombre);
		xlistitem.appendChild( xdetalle);
		xlistitem.appendChild( xunid );
		xlistitem.appendChild( xdescuento );
		xlistitem.appendChild( ximpuesto );
		xlistitem.appendChild( xprecio );			
		xlistitem.appendChild( xtalla );			
		xlistitem.appendChild( xcolor );
		xlistitem.appendChild( xpedidodet );
		xlistitem.appendChild( ximporte );			
		if(xsubsidiario) xlistitem.appendChild( xsubsidiario );	
		xlistitem.appendChild( xcosto );
		xlistitem.appendChild( xconcepto );
		xlistitem.appendChild( xproducto );
		xlistitem.appendChild( xstatus );
		xlistitem.appendChild( xoferta );
		if( ticket[codigo].producto.length > 80 ) xlistitem.setAttribute("tooltiptext",ticket[codigo].producto);
		xlastArticulo = xlistitem;//recordamos el ultimo articulo aÃ±adido
		xlistadoTicket.appendChild( xlistitem );	
		Blink("tic_"+codigo);

	    } else {

		//*+++ Actualiza +++++*//
		var name   = "tic_unid_"+codigo
		var xunid  = id(name);

		if (xunid) 
		{
		    xunid.setAttribute("value", ticket[codigo].unidades);
		    Blink(name);

		    id("tic_detalle_"+codigo).value = ticket[codigo].vdetalle;
		    id("tic_detalle_"+codigo).setAttribute("style",cssdetalle);
		}
	    }

	    //Pedido Detalle...
	    CargarPedidoDetFila(codigo,ticket[codigo].unidades );

            //Selecciona ultimo producto agregado o modificado
            seleccionarfilatpv(codigo,true);
            
	    //Redibuja el nuevo TOTAL
	    RecalculoTotal();
	    //log("Comprando "+ticket[codigo].unidades +" de "+ticket[codigo].nombre);
            //log
            cLoadCBalter = codigo;
	}

        function gridListarProductosTPV(){

	    switch (cListaProductos) { 
	    case 'column'  : 
		xlista   = 'compact';  
		xview    = true;
		cListaCompacta = true;
		ximage   = 'gpos_tpv_lista_compacta.png';
		break;

	    case 'compact' : 
		xlista   = 'column';  
		xview    = false;
		ximage   = 'gpos_tpv_lista_columna.png';
		cListaCompacta = false;
		break;
	    }
	    id("gridListarTPV").setAttribute('image','img/'+ximage);
	    cListaProductos = xlista;
	    agnadirPorNombre();
	}

        function CrearEntradaEnProductos(producto,nombre,marca,color,talla,labo,
					 codigo,referencia,precio,precioemp,preciodoce,preciocorp,
  					 impuesto,unidades,costo,lote,vence,serie,
					 menudeo,unidxcont,unid,cont,servicio,ilimitado,
					 oferta,ofertaunid,
					 pvo,condventa,mproducto){

	    if( unidades == 0 )
		if( !esOnlineBusquedas() )
		    return;

	    var modo       = id("rgModosTicket").value;//Mproducto
            var vprecio    = ( modo == "mproducto")? obtenerPrecioBaseMProducto(costo):precio;
            var vprecioemp = ( !Local.ListaProductoViewPVE && menudeo )? precioemp  : 0.00;
	    var vcosto     = ( !Local.ListaProductoViewCTO && Local.esAdmin )? costo : 0.00;
	    var vpreciodoce= ( !Local.ListaProductoViewPVD )? preciodoce : 0.00;
	    var vpreciocorp= ( !Local.ListaProductoViewPVC && Local.esB2B == 1 )? preciocorp : 0.00;
            vcosto = parseFloat(vcosto) + parseFloat(vcosto)*impuesto/100;
	    
            prodlist_cb[codigo] = 1;
	    //Listar
	    var xnombre    = ( cListaCompacta )? producto:codigo+' '+nombre; 
	    //Detalle
	    var xvence     = ( vence  )? vence[0].split(":") :false;
	    var xlote      = ( lote   )? lote[0].split(":")  :false;
	    var xserie     = ( serie  )? serie[0].split(":") :false;
	    var cssdetalle = ( oferta )? 'font-weight: bold;text-align:right;':'text-align:right;';

	    //Menudeo
	    var xresto    = ( menudeo )? unidades%unidxcont                    : false;
	    var xcant     = ( menudeo )? ( unidades - xresto )/unidxcont       : false;
	    var xcont     = ( menudeo )? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
	    var xmenudeo  = ( menudeo )? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;
	    var vdetalle  = '';

            //Producto
            var xnombreproducto = (parseInt(xnombre.length) >80)? xnombre.slice(0,81)+ '...':xnombre;

	    switch(condventa){
	    case 'CRM' : condventa = "C/RM.";	break;
	    case 'CRMR': condventa = "C/RMR."; break;
	    default    : condventa = false;
	    }
	    

	    vdetalle  = ( mproducto )? '**MIXPRODUCTO** ' : vdetalle;
	    vdetalle  = ( oferta    )? '**OFERTA '+ofertaunid+''+unid+' c/u '+formatDinero(pvo)+'** '+vdetalle : vdetalle;
	    vdetalle  = ( menudeo   )? vdetalle+xmenudeo : vdetalle;
	    vdetalle  = ( ilimitado )? vdetalle+'**STOCK ILIMITADO** ' : vdetalle;
	    vdetalle  = ( serie     )? vdetalle+'NS. '+xserie[1].slice(0,30)+' ' : vdetalle;
	    vdetalle  = ( vence     )? vdetalle+'FV. '+xvence[1] + ' ' : vdetalle;
	    vdetalle  = ( lote      )? vdetalle+'LT. '+xlote[1]  + ' ' : vdetalle;
	    vdetalle  = ( servicio  )? '**SERVICIO**' : vdetalle;
	    vdetalle  = ( condventa )? vdetalle+' '+condventa : vdetalle;

            var xlistadoProductos = id("listaProductos");	

            var xref         = document.createElement("label"); 
	    xref.setAttribute("value",referencia);
            xref.setAttribute("id","ref_"+codigo);

            var xdescripcion = document.createElement("label");

            xdescripcion.setAttribute("value",xnombreproducto);
            xdescripcion.setAttribute("id","descripcion_"+codigo);

            var xmarca = document.createElement("label");
	    xmarca.setAttribute("value",marca);
            xmarca.setAttribute("collapsed",cListaCompacta);

            var xcolor = document.createElement("label");
	    xcolor.setAttribute("value",color);
            xcolor.setAttribute("collapsed",cListaCompacta);

            var xlab = document.createElement("label");
	    xlab.setAttribute("value",labo);
            xlab.setAttribute("collapsed",cListaCompacta);

            var xtalla = document.createElement("label");
	    xtalla.setAttribute("value",talla);
            xtalla.setAttribute("value",( talla.length > 30 )? talla.slice(0,30)+ '...':talla );
            xtalla.setAttribute("collapsed",cListaCompacta);
    

            var xexistencias = document.createElement("label");
	    xexistencias.setAttribute("value",unidades+' '+unid );
            xexistencias.style.textAlign ="right";
            xexistencias.setAttribute("id","stock_"+codigo);

            var xprecio      = document.createElement("label");
	    xprecio.setAttribute("value",formatDinero(vprecio));	
            xprecio.style.align     ="right";
            xprecio.style.textAlign ="right";
            xprecio.setAttribute("id","precio_"+codigo);
	    xprecio.style.color='#000';
    
            var xpreciocorp      = document.createElement("label");
	    xpreciocorp.setAttribute("value",formatDinero(vpreciocorp));	
            xpreciocorp.style.align     ="right";
            xpreciocorp.style.textAlign ="right";
            xpreciocorp.setAttribute("id","preciocorp_"+codigo);
	    //xpreciocorp.setAttribute("collapsed",Local.ListaProductoViewPVC);

            var xprecioemp      = document.createElement("label");
	    xprecioemp.setAttribute("value",formatDinero(vprecioemp));	
            xprecioemp.style.align     ="right";
            xprecioemp.style.textAlign ="right";
            xprecioemp.setAttribute("id","precioemp_"+codigo);
	    //xprecioemp.setAttribute("collapsed",Local.ListaProductoViewPVE);

            var xpreciodoce      = document.createElement("label");
	    xpreciodoce.setAttribute("value",formatDinero(vpreciodoce));	
            xpreciodoce.style.align     ="right";
            xpreciodoce.style.textAlign ="right";
            xpreciodoce.setAttribute("id","preciodoce_"+codigo);
	    //xpreciodoce.setAttribute("collapsed",Local.ListaProductoViewPVD);
	    
            var xcosto      = document.createElement("label");
	    xcosto.setAttribute("value",formatDinero(vcosto));
            xcosto.style.align     ="right";
            xcosto.style.textAlign ="right";
            xcosto.setAttribute("id","costo_"+codigo);
	    //xcosto.setAttribute("collapsed",Local.ListaProductoViewCTO);
	    
            var xdetalle     = document.createElement("label");
	    xdetalle.setAttribute("value",vdetalle);	
            xdetalle.setAttribute("id","detalle_"+codigo);
	    xdetalle.setAttribute("style",cssdetalle);

            var xlistitem    = document.createElement("listitem");
            xlistitem.setAttribute("id","prod_"+codigo);
            xlistitem.setAttribute("oncontextmenu","seleccionarfilatpv("+codigo+",false)");

            xlistitem.appendChild( xref);
            xlistitem.appendChild( xdescripcion);
            xlistitem.appendChild( xmarca);
            xlistitem.appendChild( xcolor);
            xlistitem.appendChild( xtalla);
            xlistitem.appendChild( xlab);
            xlistitem.appendChild( xdetalle);
            xlistitem.appendChild( xexistencias);
	    xlistitem.appendChild( xcosto );
            xlistitem.appendChild( xprecio );
	    xlistitem.appendChild( xpreciocorp );
	    xlistitem.appendChild( xpreciodoce );
            xlistitem.appendChild( xprecioemp );
            if( xnombre.length > 80 || talla.length > 30 ) xlistitem.setAttribute("tooltiptext",producto);
            xlistadoProductos.appendChild( xlistitem );

            prodlist_tag[iprod] = xlistitem;
            prodlist[iprod++]   = codigo;	 	
	}

        function seleccionarfilatpv(cb,xval){
            var lista = (xval)? id("listadoTicket"):id("listaProductos");
            var fila  = (xval)? id("tic_"+cb):id("prod_"+cb);
            lista.selectItem(fila);
        }

        function viewListaProductoPrecios(xpvview){
            //limpiamos lista de precios
	    VaciarListadoProductos();
	    //Control perfil
    	    switch( xpvview ){
	    case 'CTO': Local.ListaProductoViewCTO = ( Local.ListaProductoViewCTO )? false:true; break;
	    case 'PVC': Local.ListaProductoViewPVC = ( Local.ListaProductoViewPVC )? false:true; break;
	    case 'PVD': Local.ListaProductoViewPVD = ( Local.ListaProductoViewPVD )? false:true; break;
	    case 'PVE': Local.ListaProductoViewPVE = ( Local.ListaProductoViewPVE )? false:true; break;
	    case 'CR' : Local.ListaProductoViewCR  = ( Local.ListaProductoViewCR  )? false:true; break;
	    }

	    if( Local.TPV == 'VC' )
		Local.ListaProductoViewPVC = true;
	    
	    //actualiamos la vista del listado
	    setViewListaProductoPrecios();
	    //listamos busqueda actual
	    agnadirPorNombre();
	}

        function setViewListaProductoPrecios(){
	    //col list
	    id("colListaProductoViewCR").setAttribute("collapsed", Local.ListaProductoViewCR);
	    id("colListaTicketViewCR").setAttribute("collapsed", Local.ListaProductoViewCR);
	    id("colListaProductoViewCTO").setAttribute("collapsed", Local.ListaProductoViewCTO);
	    id("colListaProductoViewPVC").setAttribute("collapsed", Local.ListaProductoViewPVC);
	    id("colListaProductoViewPVE").setAttribute("collapsed", Local.ListaProductoViewPVE);
	    id("colListaProductoViewPVD").setAttribute("collapsed", Local.ListaProductoViewPVD);
	    //head list
	    id("headListaProductoViewCR").setAttribute("collapsed", Local.ListaProductoViewCR);
	    id("headListaTicketViewCR").setAttribute("collapsed", Local.ListaProductoViewCR);
	    id("headListaProductoViewCTO").setAttribute("collapsed", Local.ListaProductoViewCTO);
	    id("headListaProductoViewPVC").setAttribute("collapsed", Local.ListaProductoViewPVC);
	    id("headListaProductoViewPVE").setAttribute("collapsed", Local.ListaProductoViewPVE);
	    id("headListaProductoViewPVD").setAttribute("collapsed", Local.ListaProductoViewPVD);
	    var xlabelpv = ( Local.TPV == 'VC' )? 'PVC/U':'PVP/U';
	    id("headListaProductoViewPVP").setAttribute('label',xlabelpv);

	}
        function ModificarEntradaEnProductos(producto,codigo,referencia,
					     precio,precioemp,preciodoce,preciocorp,
  					     impuesto,unidades,costo,lote,vence,serie,
					     menudeo,unidxcont,unid,cont,servicio,ilimitado,
					     oferta,ofertaunid,pvo,condventa,
					     mproducto){

	    var modo            = id("rgModosTicket").value;//Mproducto
	    var vprecio         = ( modo == "mproducto")? obtenerPrecioBaseMProducto(costo):precio;
            var xref            = id("ref_"+codigo);
            var xdescripcion    = id("descripcion_"+codigo);
            var xexistencias    = id("stock_"+codigo);
            var xprecio         = id("precio_"+codigo);
	    var xpreciocorp     = id("preciocorp_"+codigo);
	    var xpreciodoce     = id("preciodoce_"+codigo);
	    var xprecioemp      = id("precioemp_"+codigo);
	    var xcosto          = id("costo_"+codigo);
            var xdetalle        = id("detalle_"+codigo);
            var xnombreproducto = (parseInt(producto.length)>80)? producto.slice(0,81)+ '...':producto;
	    //Detalle
	    var xvence = ( vence )? vence[0].split(":") :false;
	    var xlote  = ( lote  )? lote[0].split(":")  :false;
	    var xserie = ( serie )? serie[0].split(":") :false;
	    var cssdetalle = ( oferta )? 'font-weight: bold;':'';

	    //Menudeo
	    var xresto   = ( menudeo )? unidades%unidxcont                    : false;
	    var xcant    = ( menudeo )? ( unidades - xresto )/unidxcont       : false;
	    var xcont    = ( menudeo )? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
	    var xmenudeo = ( menudeo )? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;

	    var vdetalle = '';
	    switch(condventa){
	    case 'CRM' : condventa = "C/RM.";	break;
	    case 'CRMR': condventa = "C/RMR."; break;
	    default    : condventa = false;
	    }

	    vdetalle  = ( mproducto )? '**MIXPRODUCTO** ' : vdetalle;
	    vdetalle  = ( oferta    )? '**OFERTA '+ofertaunid+''+unid+' c/u '+formatDinero(pvo)+'** '+vdetalle : vdetalle;
	    vdetalle  = ( menudeo   )? vdetalle+xmenudeo      : vdetalle;
	    vdetalle  = ( ilimitado )? vdetalle+'**STOCK ILIMITADO** ' : vdetalle;
	    vdetalle  = ( serie     )? vdetalle+'NS. '+xserie[1].slice(0,30)+' ' : vdetalle;
	    vdetalle  = ( vence     )? vdetalle+'FV. '+xvence[1]+' ' : vdetalle;
	    vdetalle  = ( lote      )? vdetalle+'LT. '+xlote[1]+' '  : vdetalle;
	    vdetalle  = ( servicio  )? '**SERVICIO**' : vdetalle;
	    vdetalle  = ( condventa )? vdetalle+' '+condventa : vdetalle;

	    xref.setAttribute("value",referencia);
	    xdescripcion.setAttribute("value", xnombreproducto);
	    xexistencias.setAttribute("value",unidades+' '+unid );
	    xprecio.setAttribute("value",formatDinero(vprecio));
	    xcosto.setAttribute("value",formatDinero(costo));
	    xprecioemp.setAttribute("value",formatDinero(precioemp));
	    xpreciodoce.setAttribute("value",formatDinero(preciodoce));
	    xpreciocorp.setAttribute("value",formatDinero(preciocorp));	
	    xdetalle.setAttribute("style",cssdetalle);	
	    xdetalle.setAttribute("value",vdetalle);
	}

        /*+++++++++++++++ SYNC ++++++++++++++++++++*/
        function syncCheckConnection() {

	    var xhr = new XMLHttpRequest();
	    var xfile = "img/gpos_tpvhotkey.png";
	    var xrandomNum = Math.round(Math.random() * 100000);
	    xhr.open('HEAD', xfile + "?rand=" + xrandomNum, false);
	    
	    try {
		xhr.send();
		
		if (xhr.status >= 200 && xhr.status < 304) {
		    peticionesSinRespuesta = 0;			
		} else {
		    peticionesSinRespuesta = peticionesSinRespuesta +1;			
		}
	    } catch (e) {
		peticionesSinRespuesta = peticionesSinRespuesta +1;			
	    }
            return ( peticionesSinRespuesta > 0 );
	}

        function syncClientes(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return endRunDemonTPV();

	    mostrarMensajeTPV(1,'Sincronizando clientes...',3200);
	    Local.esSyncClientes     = false;
	    Local.esSyncClientesPost = false;

	    //hora actual
	    var ts = Math.round((new Date()).getTime() / 1000); 
	    tsyncClient  = parseInt(ts) -  parseInt(ctsyncClient);
	    ctsyncClient = Math.round((new Date()).getTime() / 1000); 

	    var xres,url,prod,xjsOut,z;

	    z   = null;	    
	    url = "services.php?modo=syncClientesTPV&tsyncClient="+tsyncClient;

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
		if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
		    return endRunDemonTPV();
	    } catch(z){
		return endRunDemonTPV();
	    }

            xjsOut    = AjaxDemon.responseText;

	    if(!xjsOut)
		return endRunDemonTPV();//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return endRunDemonTPV();
            }
	    buscarCliente();
	    endRunDemonTPV();
	}

        function getClientesTPV(){

	    esSyncBoton('on');//Boton Sync

	    mostrarMensajeTPV(1,'Cargando clientes...',3200);
 	    //Check Conecction
	    var xres,url,prod,xjsOut,z;

	    z   = null;	    
	    url = "services.php?modo=getClientesTPV";

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
	    } catch(z){
		return;
	    }

            xjsOut    = AjaxDemon.responseText;

	    if(!xjsOut) return;//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }		
            esSyncBoton('pause');//Boton Sync Termina
	}

        function syncProductosTPV(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return endRunDemonTPV();

	    mostrarMensajeTPV(1,'Sincronizando stock...',3200); 
	    Local.esSyncStock     = false;
	    Local.esSyncStockPost = false;

	    //Cargamos tiempo de espera sg.
	    var ts       = Math.round((new Date()).getTime() / 1000);//Current time 
	    timeSyncTPV  = parseInt(ts) -  parseInt(ctimeSyncTPV);//Diferencia de tiempo
	    ctimeSyncTPV = Math.round((new Date()).getTime() / 1000); //Current time

	    //Variables globales
	    //timeSyncTPV  set time mm
	    //Inicia variables Ajax 
	    var xres,url,prod,xjsOut,z;

	    z   = null;	    
	    url = "services.php?modo=syncStockAlmacen";

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send('timeSyncTPV='+timeSyncTPV);
		if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
		    return endRunDemonTPV();
	    } catch(z){
		return endRunDemonTPV();
	    }

            xjsOut    = AjaxDemon.responseText;

	    xres      = parseInt(xjsOut);

	    if(xres == 1) return endRunDemonTPV();//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return endRunDemonTPV();
            }							
	    endRunDemonTPV();
	}

        function getProductosTPV(){

	    esSyncBoton('on');//Boton Sync
	    mostrarMensajeTPV(1,'Cargando productos...',2000);

 	    //Check Conecction
	    var xres,url,prod,xjsOut,z;

	    z   = null;	    
	    url = "services.php?modo=getStockAlmacen";

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
	    } catch(z){
		return;
	    }

            xjsOut    = AjaxDemon.responseText;
	    xres      = parseInt(xjsOut);

	    if(xres == 1) return;//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }
            esSyncBoton('pause');//Boton Sync Termina
	}

        function syncPromociones(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return endRunDemonTPV();

	    mostrarMensajeTPV(1,'Sincronizando promociones...',3200); 
	    Local.esSyncPromociones=false; 
	    
	    var xres,prod,xjsOut;
	    var z   = null;	    
	    var url = "modulos/promociones/modpromociones.php?modo=syncPromociones&xlocal="+Local.IdLocalActivo;

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
		if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
		    return endRunDemonTPV();
	    } catch(z){
		return endRunDemonTPV();
	    }

            xjsOut    = AjaxDemon.responseText;
	    xres      = parseInt(xjsOut);

	    if(xres == 1) return endRunDemonTPV();//OK, detiene el proceso.

	    promocioneslist = new Array();

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return endRunDemonTPV();
            }	

	    endRunDemonTPV();
	}

        function syncProductosPostTicket(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return;
	    mostrarMensajeTPV(1,'Sincronizando stock...',3200); 
	    Local.esSyncStockPost = false;
	    Local.esSyncStock     = false;

	    var xres,url,prod,xjsOut,z,ts;
	    //Cargamos tiempo de espera sg.
	    ts           = Math.round((new Date()).getTime() / 1000);//Current time 
	    timeSyncTPV  = parseInt(ts) -  parseInt(ctimeSyncTPV);//Diferencia de tiempo
	    ctimeSyncTPV = Math.round((new Date()).getTime() / 1000); //Current time

	    //Inicia variables Ajax 
	    z   = null;	    
	    url = "services.php?modo=syncStockAlmacen";

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send('timeSyncTPV='+timeSyncTPV);
		if (!( AjaxDemon.status >= 200 && AjaxDemon.status < 304 ) ) 
		    return;
	    } catch(z){
		return;
	    }
	    //alert(AjaxDemon.responseText);
            xjsOut    = AjaxDemon.responseText;
	    xres      = parseInt(xjsOut);

	    if(xres == 1) return;//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }							

	}

        function  syncPresupuesto(tp){

 	    //Check Conecction
	    //if(syncCheckConnection()) return endRunDemonTPV();

	    mostrarMensajeTPV(1,'Sincronizando tickets '+tp+'...',3200);

	    //add new item preventa o proforma o proformaonline
	    var combo = id("items"+tp);
	    var cadena;
	    var xrequest = new XMLHttpRequest();
	    var z        = null;
	    //consigue listado 
	    var url = 
		"services.php?"+
		"modo=syncPresupuestosTPV"+"&"+
		"tipopresupuesto="+tp;
	    xrequest.open("GET",url,false);
	    try {
		xrequest.send(null);
		if (!( xrequest.status >= 200 && xrequest.status < 304 ) ) 
		    return endRunDemonTPV();
		
	    } catch(z){
		return endRunDemonTPV();
	    }

	    cadena = xrequest.responseText;

	    //PREVENTA
	    if(tp=='Preventa')
	    {
		Local.esSyncPreventa = false;   
		syncPreventa(cadena,tp);
	    }
	    //PROFORMA
	    if(tp=='Proforma')
	    { 
		Local.esSyncProforma =false;
		syncProforma(cadena,tp); 
	    }

	    //PROFORMAONLINE
	    if(tp=='ProformaOnline')
	    {
		Local.esSyncProOnline=false;   
		syncProformaOnline(cadena,tp);
	    }

	    endRunDemonTPV();
	    //<menuitem label="Todos" selected="true"  />
	}

        function  syncMProducto(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return endRunDemonTPV();
	    
	    mostrarMensajeTPV(1,'Sincronizando tickets mproducto...',3200);
	    Local.esSyncMProducto = false;  

	    //METAPRODUCTO****************
	    //add new item preventa o proforma
	    var combo    = id("itemsMProducto");
	    var xrequest = new XMLHttpRequest();
	    var z        = null;
	    //consigue listado 
	    var url = 
		"services.php?"+
		"modo=syncMProductosTPV"+"&"+
		"Estado='Ensamblaje'";
	    xrequest.open("GET",url,false);
	    try {
		xrequest.send(null);

		if (!( xrequest.status >= 200 && xrequest.status < 304 ) ) 
		    return endRunDemonTPV();

	    } catch(z){
		return endRunDemonTPV();
	    }
	    var cadena = xrequest.responseText;

	    cargaMProducto(cadena);
	    endRunDemonTPV();
	    //<menuitem label="Todos" selected="true"  />
	}

        function cargaMProducto( xcadena ){
	    
	    var aDelMProducto = new Array();
	    var filas;
	    
	    filas  = xcadena.split(";");
	    if(aMProductos.length == 0 && filas[1] == '') 
		return; 

	    //alert('IN MProductos: '+aMProductos.toString());
	    
	    //SEL DEL ITEM
	    for (var k=0; k<aMProductos.length; k++) {
		var idel   = 1;
		for(var n = 0; n<filas.length; n++){
		    var celdas = filas[n].split(",");

 		    if( parseInt(aMProductos[k]) == parseInt(celdas[1]) && celdas[1] !='' )
			idel = 0;
		}

		if( parseInt(idel) == 1 )
		    aDelMProducto.push(aMProductos[k]); 
	    }


	    //DELETE ITEM 
	    for(var d = 0; d<aDelMProducto.length; d++){

		generadorEliminaMProducto(IdMetaProducto,aDelMProducto[d])
		//SI ESTA CARGADO 
		if(parseInt(IdMProducto) == parseInt(aDelMProducto[d]))
		    selTipoPresupuesto(0);
	    }

	    aDelMProducto = Array();//reset array
	    
	    //ADD ITEM
	    if(filas == '') return;//Validar filas Ajax

	    for(var n = 0; n<filas.length; n++){
		var iadd   = 1;
		var celdas = filas[n].split(",");

 		for (var k=0; k<aMProductos.length; k++) 
		{
 		    if(parseInt(aMProductos[k]) == parseInt(celdas[1]) && celdas[1] !='' ) 
			iadd = 0;
		}
		
		if( parseInt(iadd) == 1 )
		    generadorCargarMProducto('MProducto',celdas[1]);//cargamos nuevos
	    }
	    //alert('OUT MProductos: '+aMProductos.toString());
	}


        function syncProforma(cadena,tp){

	    var aDelProforma = new Array();
	    var filas;
	    
	    filas  = cadena.split(";");

	    if( aProforma.length == 0 && filas[0] == '') 
		//alert('gPOS: \n   - Sin Tickets disponibles');
		return; 
	    
	    //DEL ITEM PROFORMA
	    for (j=0; j<aProforma.length; j++) {
		var idel   = 1;
		for(var i = 0; i<filas.length; i++){
		    var celdas = filas[i].split(",");
		    if( tp == 'Proforma' )
 			if( aProforma[j] == celdas[0] && celdas[0] !='' )
			    idel = 0;
		}
		if( idel == 1 )
		    aDelProforma.push(aProforma[j]);
	    }
	    //DELETE ITEM PROFORMA
	    for(var i = 0; i<aDelProforma.length; i++){
		generadorEliminaPresupuesto(tp,aDelProforma[i]);
		//SI ESTA EN CARRITO
		if(IdPresupuesto == aDelProforma[i])
		    selTipoPresupuesto(0);
	    }
	    aDelProforma = Array();
	    
	    //ADD ITEM PROFORMA
	    if(filas == '') return;//Validar filas Ajax

	    for(var i = 0; i<filas.length; i++){
		var idel   = 1;
		var celdas = filas[i].split(",");
 		for (j=0; j<aProforma.length; j++) {
		    if( tp == 'Proforma' )
 			if( aProforma[j] == celdas[0]  && celdas[0] !='' ) 
			    idel = 0;
		}
		if( idel == 1 )
		    generadorCargarPresupuesto(tp,celdas[0],'id');
	    }
	}


        function syncProformaOnline(cadena,tp){

	    var aDelProformaOnline = new Array();
	    var filas;
	    
	    filas  = cadena.split(";");

	    if( aProformaOnline.length == 0 && filas[0] == '') 
		//alert('gPOS: \n   - Sin Tickets disponibles');
		return; 
	    
	    //DEL ITEM PROFORMA
	    for (j=0; j<aProformaOnline.length; j++) {
		var idel   = 1;
		for(var i = 0; i<filas.length; i++){
		    var celdas = filas[i].split(",");
		    if( tp == 'ProformaOnline' )
 			if( aProformaOnline[j] == celdas[0] && celdas[0] !='' )
			    idel = 0;
		}
		if( idel == 1 )
		    aDelProformaOnline.push(aProformaOnline[j]);
	    }
	    //DELETE ITEM PROFORMAONLINE
	    for(var i = 0; i<aDelProformaOnline.length; i++){
		generadorEliminaPresupuesto(tp,aDelProformaOnline[i]);
		//SI ESTA EN CARRITO
		if(IdPresupuesto == aDelProformaOnline[i])
		    selTipoPresupuesto(0);
	    }
	    aDelProformaOnline = Array();
	    
	    //ADD ITEM PROFORMAONLINE
	    if(filas == '') return;//Validar filas Ajax

	    for(var i = 0; i<filas.length; i++){
		var idel   = 1;
		var celdas = filas[i].split(",");
 		for (j=0; j<aProformaOnline.length; j++) {
		    if( tp == 'ProformaOnline' )
 			if( aProformaOnline[j] == celdas[0]  && celdas[0] !='' ) 
			    idel = 0;
		}
		if( idel == 1 )
		    generadorCargarPresupuesto(tp,celdas[0],'id');
	    }
	}


        function syncPreventa(cadena,tp){

	    var aDelPreventa = new Array();
	    var filas;
	    
	    filas  = cadena.split(";");
	    if(aPreventa.length == 0 && filas[0] == '') 
		//alert('gPOS: \n   - Sin Tickets disponibles');
		return; 

	    
	    //DEL ITEM PREVENTA
	    for (j=0; j<aPreventa.length; j++) {
		var idel   = 1;
		for(var i = 0; i<filas.length; i++){
		    var celdas = filas[i].split(",");
		    if( tp == 'Preventa' )
 			if( aPreventa[j] == celdas[0] && celdas[0] !='' )
			    idel = 0;
		}
		if( idel == 1 )
		    aDelPreventa.push(aPreventa[j]);
	    }
	    //DELETE ITEM PREVENTA
	    for(var i = 0; i<aDelPreventa.length; i++){
		generadorEliminaPresupuesto(tp,aDelPreventa[i])
		//SI ESTA CARGADO 
		if(IdPresupuesto == aDelPreventa[i])
		    selTipoPresupuesto(0);
	    }
	    aDelPreventa = Array();
	    
	    //ADD ITEM PREVENTA
	    if(filas == '') return;//Validar filas Ajax

	    for(var i = 0; i<filas.length; i++){
		var idel   = 1;
		var celdas = filas[i].split(",");
 		for (j=0; j<aPreventa.length; j++) {
		    if( tp == 'Preventa' )
 			if( aPreventa[j] == celdas[0]  && celdas[0] !='' ) 
			    idel = 0;
		}
		if( idel == 1 )
		    generadorCargarPresupuesto(tp,celdas[0],'id');
	    }
	}


        function soloNumerosTPV(evt,num){
	    keynum = (window.event)?evt.keyCode:evt.which;
	    if(keynum == 46) 
	    {
		var sChar=String.fromCharCode(keynum);
		if(isNaN(num+sChar)) return false;
	    }
	    return (keynum <= 13 || (keynum >= 48 && keynum <= 57) || keynum == 46);
	}

        function soloNumerosEnterosTPV(evt,num){
	    // Backspace = 8, Enter = 13, ’0′ = 48, ’9′ = 57, ‘.’ = 46
	    keynum = (window.event)?evt.keyCode:evt.which;
	    if(keynum == 46) 
	    {
		var sChar=String.fromCharCode(keynum);
		if(isNaN(num+sChar)) return false;
	    }
	    return (keynum <= 13 || (keynum >= 48 && keynum <= 57));
	}

        function soloAlfaNumericoTPV(e){
	    key = e.keyCode || e.which;
	    tecla = String.fromCharCode(key).toLowerCase();
	    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789,|-";
	    especiales = [8, 9, 13, 37, 38, 39, 40, 46];
	    tecla_especial = false
	    for(var i in especiales){
		if(key == especiales[i]){
		    tecla_especial = true;
		    break;
		}
	    }
	    
	    if(letras.indexOf(tecla)==-1 && !tecla_especial){
		return false;
	    }
	}

        function soloAlfaNumericoCodigoTPV(e){
	    key = e.keyCode || e.which;
	    tecla = String.fromCharCode(key).toLowerCase();
	    letras = "abcdefghijklmnopqrstuvwxyz0123456789-";
	    especiales = [8, 13, 9, 39, 46];
	    tecla_especial = false
	    for(var i in especiales){
		if(key == especiales[i]){
		    tecla_especial = true;
		    break;
		}
	    }
    
	    if(letras.indexOf(tecla)==-1 && !tecla_especial){
		return false;
	    }
	}

        function soloNumerosTelefono(e){
	    key = e.keyCode || e.which;
	    tecla = String.fromCharCode(key).toLowerCase();
	    letras = "0123456789#* ";
	    especiales = [8, 13, 9, 39, 46];
	    tecla_especial = false
	    for(var i in especiales){
		if(key == especiales[i]){
		    tecla_especial = true;
		    break;
		}
	    }
    
	    if(letras.indexOf(tecla)==-1 && !tecla_especial){
		return false;
	    }
	}

        /*++++++++++++++++++++++++ SERIE ++++++++++++++++++++++++++*/

        function quitaSeriesTicket(xcod,delSeries){

            var ardSeries = delSeries.split(",");
	    var xSeries   = ( ticket[xcod] )? ticket[xcod].series:'';
	    var aSeries   = xSeries.toString();
	    var arSeries  = ( ticket[xcod] )? aSeries.split(","):Array();
	    var pSeries   = Array();
	    
	    //Series...
            for( var j=0; j < ardSeries.length; j++)
	    {
		//Series Ticket...
		for( var xns=0; xns < arSeries.length; xns++)
		{
		    pSeries = arSeries[xns].split(":");


		    if( ardSeries[j] == pSeries[1] )
		    {
			//Del Carrito Serie...		    
			tpv.DelCarritoSerie(xcod.toUpperCase() , arSeries[xns]);

			//Stock Carrito...
			xunidades = (ticket[xcod])? ticket[xcod].series.length:0;
			ticket[xcod].unidades        = xunidades;
			id("tic_unid_" + xcod).value = ticket[xcod].unidades;


			//Detalle...
			var vxSeries = '';
			var vpSeries = Array();
			var vdetalle = false;
			var vsrt     = '';
			var xpedidodet = CargarPedidoDetFilaSerie(xcod);

			for(var i=0; i < ticket[xcod].series.length; i++)
			{
			    vpSeries = ticket[xcod].series[i].split(":");
			    vxSeries = vxSeries+vsrt+vpSeries[1];
			    vsrt     = ',';
			}
			
			vsrt     = ( vxSeries.length >20)? '...':'';
			vdetalle = ' NS.'+vxSeries.slice(0,20)+vsrt;
			
			id("tic_detalle_"+xcod).value   = vdetalle;
			id("tic_pedidodet_"+xcod).value = xpedidodet;
			ticket[xcod].pedidodet          = xpedidodet;

		    }
		}
	    }
	}

        function loadListHotKey(){

	    alert('gPOS: TECLAS DE ACCESO DIRECTO:\n\n'+
                  '     [ F1 ] -> Stock / Buscar         \n'+
		  '     [ F2 ] -> Clientes / Buscar      \n'+
		  '     [ F4 ] -> Imprimir Comprobante   \n'+
                  '     [ F7 ] -> Proforma / Buscar      \n'+
		  '     [ F8 ] -> Preventa / Buscar      \n'+
                  '     [ F9 ] -> Sincroniza Clientes    \n\n'+
                  '     [ Ctrl + F1 ] -> Ventas                  \n'+
                  '     [ Crtl + F2 ] -> Servicios               \n'+
                  '     [ Crtl + F3 ] -> Codigo Barras / Buscar  \n'+
                  '     [ Crtl + F9 ] -> Caja                    \n'+
                  '     [ Ctrl + F11] -> Sincroniza Ticket Preventa  \n\n'+
		  '     [ Shift + Supr ] -> Limpia Ticket          ');
	}

        function elijeComprobanteTPV(){
	    var xtop  = parseInt( window.screen.width)/2 - 65;
	    var xleft = parseInt( window.screen.height)/2 - 247;

	    switch( ModoDeTicket )
	    {
	    case "cesion": id("elijeAlbaranTPV").removeAttribute("disabled");     break;
	    case "venta" : id("elijeAlbaranTPV").setAttribute("disabled","true"); break;
	    default      : return;
	    }

	    //id("panelElijeComprobanteTPV").openPopup(null, "", xtop, xleft, false, false);
	    id("panelElijeComprobanteTPV").openPopupAtScreen(xtop, xleft, false);
	}

        function keyAbrirPeticion(xval){
	    id("panelElijeComprobanteTPV").hidePopup();
	    AbrirPeticion(xval);
	}

        function keyLoadTipoComprobante(xval){


	    if( isNaN( parseInt( xval ) ) ) return;

	    id("radioticket").removeAttribute("selected");
	    id("radioboleta").removeAttribute("selected");
	    id("radiofactura").removeAttribute("selected");
	    id("radioalbaran").removeAttribute("selected");

	    switch( xval )
	    {
	    case 0: id("radioticket").setAttribute("selected","true");  break;
	    case 1: id("radioboleta").setAttribute("selected","true");  break;
	    case 2: id("radiofactura").setAttribute("selected","true"); break;
	    case 4: id("radioalbaran").setAttribute("selected","true"); break;
	    default :
		id("radioticket").setAttribute("selected","true"); 
		tipocomprobante(0);
		return;
	    }		    
	    tipocomprobante(xval);
	}
 
        /*+++++++++++++++++ DEMONIOS +++++++++++++++++*/
        //NOTA:
        // El demonio se ejecutara cada X segundos y enviara una peticion
        // el servidor, para leer si hay mensajes nuevos.
        // Ademas mantiene una variable numFallosConexion, que se incrementa con 
        // cada peticion, y se anula con cada respuesta. Si muchas peticiones no reciben 
        // respuesta, es que hemos perdido la conexion con el servidor. Avisaremos al usuario
        // y lo protegeremos de problemas.

       //Productos & Clientes
       //setTimeout("cargarDatosInicio()",1000);//DATOS

       //Inicia demonios
       //setTimeout("Demon_syncTPV()",29999);//MENSAJES

       function cargarDatosInicio(){
	   getMProductos();   //METAPRODUCTOS
	   getMensajes();     //MENSAJES
	   getPreventas();    //PEDIDOS
	   getClientesTPV();  //CLIENTES
	   getProductosTPV(); //PRODUCTOS
	   setTimeout("Demon_syncTPV()",29999);//MENSAJES
       }

       function esValidaFechaTPV(day,month,year){
	   var dteDate;
	   month=month-1;
	   dteDate=new Date(year,month,day);
	   return ((day==dteDate.getDate()) && 
		   (month==dteDate.getMonth()) && 
		   (year==dteDate.getFullYear()));
       }

       function validaFechaTPV(fecha){
	   var patron = new RegExp("^(19|20)+([0-9]{2})([-])([0-9]{1,2})([-])([0-9]{1,2})$");
	   var values = fecha.split("-");
	   
	   if(fecha.search(patron)==0)
	       
               if( esValidaFechaTPV(values[2],values[1],values[0]) )
		   return true;
	   
	   return false;
       }
/*+++++++++++++++++++++++++++++ TOOLS ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ CLIENTES  ++++++++++++++++++++++++++++++++++*/


    /*+++++++++++ Clientes ++++++++++++++*/

    var usuarios = new Array();
    var idusuarios = new Array();
    var iusers = 0;

    var ixusuarios = 0;
    var UltimoRowID = "";
    var indiceDeRow = 0;

    var UsuarioSeleccionado = 1;//por defecto el cliente contado

    var IdTipoPresupuesto = 0;//por defecto tipo presupuesto 
    var IdPresupuesto     = 0;//por defecto id presupuesto 
    var IdMProducto       = 0;// Usado para crear mproducto .dato( IdProducto ).
                              // Usado para editar el mproducto .dato( CBMetaProducto ).
    var IdMetaProducto    = 0;// Usado para .dato( IdMetaProducto ).
    var lotePorFacturar   = new Array();// Usado como lista IdComprobante(s) a facturar
    var mltPorFacturar    = new Array();// Usado como lista detalles de Albaranes para facturar
    var nltPorFacturar    = 0;// Usado para nro de albaranes a facturar
    var cliPorFacturar    = 0;// Usado para IdCliente albaran a facturar
    var StockMetaProducto = 0;// Usado para Guardar MProducto Sin Stock
    var timeSyncTPV       = 0;// Usado para ejecutar y contolar el tiempo de sync TPV
    var ctimeSyncTPV      = Math.round((new Date()).getTime() / 1000); //Current time as number
    var tsyncClient       = 0;//Clientes 
    var esActivoServer    = true;//Servidor Activo?
    var ctsyncClient      = Math.round((new Date()).getTime() / 1000);//Clientes 
    var ArrMP             = new Array();//Usado para salvar datos MProductos para usarlos en Pedidos.
    var esCambiodeCliente = false;//Cambio de cliente de Comprobantes
    var IdCompCambioCliente = 0;//Cambio de cliente de Comprobantes
    var esListadoUsuariosVisible = false;

    /*+++++++++++++++++ Clientes  ++++++++++++++++*/

    // Agnadir Cliente Contado     
    aU( "Cliente Contado",1,0,'',0,0,0,'Interno','','','','Cliente Contado','',0,'');

    // Agnadir Otros Clientes
    function aU(nombre,idcliente,debe,ruc,bono,credito,promo,tipo,telf,email,dir,legal,naci,
                suscrip,obs) {
	
	if( usuarios[idcliente] )
	    return saU(nombre,idcliente,debe,ruc,bono,credito,promo,tipo,telf,email,dir,legal,naci,
                       suscrip,obs);

        idusuarios.push(idcliente);
        usuarios[idcliente] = new Object();
        usuarios[idcliente].nombre = nombre;
        usuarios[idcliente].ruc    = ruc;
        usuarios[idcliente].id     = idcliente;	
        usuarios[idcliente].debe   = debe;	
        usuarios[idcliente].bono   = bono;	
        usuarios[idcliente].credito = credito;	
        usuarios[idcliente].promo  = promo;	
        usuarios[idcliente].tipo   = tipo;	
        usuarios[idcliente].telf   = telf;
        usuarios[idcliente].email  = email;
        usuarios[idcliente].dir    = dir;
        usuarios[idcliente].legal  = legal;
        usuarios[idcliente].obs    = obs;
        usuarios[idcliente].naci   = naci;
        usuarios[idcliente].suscrip= suscrip;

        addXUser(nombre,idcliente,debe,ruc,bono,credito,promo,tipo,telf,suscrip);
    }

    function saU(nombre,idcliente,debe,ruc,bono,credito,promo,tipo,telf,email,dir,legal,naci,
                 suscrip,obs) {

        usuarios[idcliente].nombre = nombre;
        usuarios[idcliente].ruc    = ruc;
	usuarios[idcliente].debe   = debe;
        usuarios[idcliente].bono   = bono;	
        usuarios[idcliente].credito = credito;	
        usuarios[idcliente].promo  = promo;	
        usuarios[idcliente].tipo   = tipo;	
        usuarios[idcliente].telf   = telf;
        usuarios[idcliente].email  = email;
        usuarios[idcliente].dir    = dir;
        usuarios[idcliente].legal  = legal;
        usuarios[idcliente].obs    = obs;
        usuarios[idcliente].naci   = naci;
        usuarios[idcliente].suscrip= suscrip;
        
        //Lista...
        if(!id("user_picker_"+idcliente))
            return addXUser(nombre,idcliente,debe,ruc,bono,credito,promo,tipo,telf,suscrip);

        //Actualiza...
        var txtsuscrip = ( parseInt(suscrip) == 1)? '  **Suscrito.':'';
    	var xdebe = (debe>0)? cMoneda[1]['S']+" "+formatDinero(debe):"";
        id("user_picker_ruc_"+idcliente).setAttribute("label",ruc );
	id("user_picker_nombre_"+idcliente).setAttribute("label",nombre+txtsuscrip );
	id("user_picker_debe_"+idcliente).setAttribute("label",xdebe);	
    }

    function MostrarUsuariosForm() {

	id("panelDerecho").setAttribute("collapsed",true);	
        id("modoVisual").setAttribute("selectedIndex",3);

        esListadoUsuariosVisible = true;
	id("buscaCliente").focus();

	//Sync
	if( Local.esSyncClientes ) 
	    setTimeout("runsyncTPV('Clientes')",600);

	resizelistboxticket(false); 
	resizelistboxcliente( true );
    }

    function ToggleListadoUsuariosForm() {
        var code;

        if (esListadoUsuariosVisible) {
            code = 0;//ocultar
            CBFocus();
        }  else
            code = 3;      

        id("modoVisual").setAttribute("selectedIndex",code);

        esListadoUsuariosVisible = (code==3);
    }

    var preSeleccionadoCliente = false;

   function SeleccionaCliente(idcliente,xthis){
       preSeleccionadoCliente = idcliente;
       id("buscaClienteSelect").value = preSeleccionadoCliente;
       id("tab-suscripcion").setAttribute("collapsed",true); 
       id("tab-vistacliente").setAttribute("collapsed",true);
   }


    function EliminarClienteActual(){

        if(!preSeleccionadoCliente)
            return alert(c_gpos+po_nopuedeseliminarcontado);

        if(!confirm(c_gpos+po_seguroborrarcliente))
            return;

        var obj = new XMLHttpRequest();
        var url = "services.php?modo=eliminarcliente&idcliente=" + escape(preSeleccionadoCliente)
            + "&r=" + Math.random();		
        serialNum++;		

        obj.open("GET",url,false);
        obj.send(null);	

        var resultado =	parseInt(obj.responseText);

        if(resultado){
            delXUser( preSeleccionadoCliente);
            id("tab-vistacliente").setAttribute("collapsed","true");
            alert(c_gpos+po_clienteeliminado);
        } else {
            alert(c_gpos+po_noseborra);
        }
    }

    function pickClient(idusuario,myself) {
        var labelCliente = id("tCliente");
        var nuevoNombreUsuario;

	//Finaliza Cambio Cliente de Comprobante
	if(esCambiodeCliente)
	    return CambiarIdClienteDocumento(idusuario);
	
        UsuarioSeleccionado = idusuario;
        nuevoNombreUsuario = usuarios[idusuario].nombre;	

        labelCliente.setAttribute("label", nuevoNombreUsuario );

        ToggleListadoUsuariosForm();

	/*++++ Promocion +++++*/
	cargarPromocion();

	VerTPV();
    }	    //Local.esSyncStock = false;

    function LimpiaToClienteContado() {
        var labelCliente = id("tCliente");
        var nuevoNombreUsuario;

        UsuarioSeleccionado = 1;
        nuevoNombreUsuario = usuarios[1].nombre;	

        labelCliente.setAttribute("label", nuevoNombreUsuario );	 
    }



    function pickClienteContado(){
        pickClient(1);
    }

    function PasaTab(desde,hasta,pad){
        var xPanelMod = id(hasta);
        //TODO: probablemente hay una forma mejor de hacer esto	
        id(desde).setAttribute("fistTab","true");
        id(desde).setAttribute("selectedIndex",pad);
        id(desde).setAttribute("selected","false");
        id(desde).setAttribute("selectedItem",xPanelMod);
        id(hasta).setAttribute("beforeselected","true");
        //	id(desde).setAttribute("afterselected","false");

        id(hasta).setAttribute("selectedIndex",pad);
        id(hasta).setAttribute("selected","true");
        id(hasta).setAttribute("selectedItem",xPanelMod);
        //id(hasta).setAttribute("beforeselected","false");
        id(desde).setAttribute("last-tab","true");
    }


    function delXUser(iduser){
        var root = id("clientPickArea");
        var xrow = id("user_picker_"+iduser);

        if(xrow)
            root.removeChild(xrow);
    }

    function gettxtPromocion( txtpromo ){

	if( txtpromo == '0' ) return;

	var aPromo   = txtpromo.split("~");
	var tPromo   = Array();
	var catPromo = '';
	var srt      = '';

	for( var j=0; j < aPromo.length; j++){

	    tPromo    = aPromo[j].split(":");
	    catPromo += srt+tPromo[0];
	    srt       = ','; 
	}
	return catPromo;
    }


    function gettxtPromocion2id( txtpromo,xid ){

	if( txtpromo == '0' ) return;

	var aPromo   = txtpromo.split("~");
	var tPromo   = Array();
	var catPromo = '';
	var srt      = '';

	for( var j=0; j < aPromo.length; j++){

	    tPromo = aPromo[j].split(":");

	    if( tPromo[1] == xid ) return tPromo[0];
	}
	return '';
    }


    function getIdPromocion( promo ){

	if( promo == '0' ) return Array();

	var aPromo   = promo.split("~");
	var idPromo  = Array();
	var idsPromo = Array();

	for( var x=0; x < aPromo.length; x++)
	{
	    idPromo  = aPromo[x].split(":");
	    idsPromo.push(idPromo[1]);
	}
	
	return idsPromo;
    }

    //INFO: agnade un usuario al listado de usuarios
function addXUser(nombreUser,iduser,debe,ruc,bono,credito,promo,tipo,telf,suscrip){

        var xroot    = id("clientPickArea");
        var xclient  =  document.createElement("listitem");
        var xnombre  = document.createElement("listcell");
        var xdebe    = document.createElement("listcell");
        var xicon    = document.createElement("listcell");
        var xbono    = document.createElement("listcell");
	var xcredito = document.createElement("listcell");
        var xpromo   = document.createElement("listcell");
	var xtelefono= document.createElement("listcell");
        var xnf      = document.createElement("listcell");
	var txtdebe  = (debe)? cMoneda[1]['S']+" "+formatDinero(debe):"";
	var txtbono  = (bono)? cMoneda[1]['S']+" "+formatDinero(bono):"";
	var txtcredito  = (credito)? cMoneda[1]['S']+" "+formatDinero(credito):"";
	var txtpromo = ( promo != '0' )? gettxtPromocion( promo ):"";
        var txttelefono = telf;
        var txtsuscrip = ( parseInt(suscrip) == 1)? '  **Suscrito.':'';
	var imgico   = 'gpos_clienteparticular.png';
	imgico = (tipo == 'Empresa')? 'gpos_clienteempresa.png':imgico;
	imgico = (tipo == 'Interno')? 'gpos_tpv_clientecontado.png':imgico;

        xdebe.setAttribute("label",txtdebe);	
        xdebe.setAttribute("value",iduser );
        xdebe.setAttribute("readonly","true");
        xdebe.setAttribute("id","user_picker_debe_"+iduser);

        //xcell0.setAttribute("onclick","pickClient("+iduser+")");	
        xnf.setAttribute("id","user_picker_ruc_"+iduser);
        xnf.setAttribute("label",ruc );	
        xnf.setAttribute("value",iduser );
        xnf.setAttribute("readonly","true");

        xnombre.setAttribute("id","user_picker_nombre_"+iduser);
        xnombre.setAttribute("label",nombreUser+txtsuscrip );	
        xnombre.setAttribute("value",iduser );
        xnombre.setAttribute("readonly","true");
        //xcell1.setAttribute("onclick","pickClient("+iduser+")");	

        xicon.setAttribute("label",tipo);	
        xicon.setAttribute("class","listitem-iconic");
        xicon.setAttribute("image","img/"+imgico);
        xicon.setAttribute("id","user_picker_tipo_"+iduser);

        xbono.setAttribute("label",txtbono);

	xcredito.setAttribute("label",txtcredito);	

        xpromo.setAttribute("label",txtpromo);
	xtelefono.setAttribute("label",txttelefono);

        xclient.setAttribute("id","user_picker_"+iduser);
        xclient.setAttribute("ondblclick","pickClient("+iduser+",this)");	
        xclient.setAttribute("onclick","SeleccionaCliente("+iduser+",this)");
        xclient.setAttribute("value",iduser );	

        xclient.appendChild( xicon );
        xclient.appendChild( xnf );
        xclient.appendChild( xnombre );
        xclient.appendChild( xdebe );
        xclient.appendChild( xbono );
	xclient.appendChild( xcredito );
        xclient.appendChild( xpromo );
	xclient.appendChild( xtelefono );
        xroot.appendChild( xclient);	
    }
    
    function buscarCliente(){

        var busca    = id("buscaCliente").value;
        var n        = usuarios.length;    
        var ns       = new String(busca);
        var theList  = id('clientPickArea');
	var xcliente = 0;

        ns    = ns.toUpperCase();
        filas = theList.itemCount;

        for(var i=0;i<filas;i++)
	{
	    theList.removeItemAt(0);
        }

        if(ns=="")
	{
	    for(var i=0;i<filas;i++)
	    {
                theList.removeItemAt(0);
	    }

	    for(var i=0;i<idusuarios.length;i++){
                var idcliente = idusuarios[i];
                var cliente   = theList.getItemAtIndex(0);
                addXUser(usuarios[idcliente].nombre, 
			 usuarios[idcliente].id, 
			 usuarios[idcliente].debe, 
			 usuarios[idcliente].ruc, 
			 usuarios[idcliente].bono, 
			 usuarios[idcliente].credito, 
			 usuarios[idcliente].promo,
			 usuarios[idcliente].tipo,
			 usuarios[idcliente].telf,
                         usuarios[idcliente].suscrip);
	    }
        }else{

	    for(var i=0;i<filas;i++)
	    {
                theList.removeItemAt(0);
	    }
	    
	    for(var i=0;i<idusuarios.length;i++)
	    {
                var idcliente = idusuarios[i];
                var ruc       = new String(usuarios[idcliente].ruc);
                var nombre    = new String(usuarios[idcliente].nombre);
                var telf      = new String(usuarios[idcliente].telf);
                var legal     = new String(usuarios[idcliente].legal);
                var cliente   = theList.getItemAtIndex(0);

		ruc    = ruc.toUpperCase();
                nombre = nombre.toUpperCase();

                if((nombre.indexOf(ns) != -1) ||
                   (ruc.indexOf(ns) != -1)    ||
                   (telf.indexOf(ns) != -1)   ||
                   (legal.indexOf(ns) != -1) )                    
		{
		    addXUser(usuarios[idcliente].nombre,
			     usuarios[idcliente].id,
			     usuarios[idcliente].debe, 
			     usuarios[idcliente].ruc, 
			     usuarios[idcliente].bono, 
			     usuarios[idcliente].credito, 
			     usuarios[idcliente].promo,
			     usuarios[idcliente].tipo,
                             usuarios[idcliente].telf,
                             usuarios[idcliente].suscrip);


		    xcliente = ( theList.itemCount == 1 )? usuarios[idcliente].id:0;
		    id("buscaClienteSelect").value = xcliente;
                }
               
		theList.selectItem(cliente);
	    }
        }

	if( theList.itemCount == 1 && id("user_picker_"+usuarios[idcliente].id) )
	{
	    id("user_picker_"+idcliente).setAttribute("selected",true);
	    id("buscaClienteSelect").value = idcliente;
	}
    }

    function cargarCliente(xval){

        var theList = id('clientPickArea');
	var xid     = id("buscaClienteSelect").value;

	switch ( xval )
	{
	case 'uno':
	case 'sel':
	    if( xid != '' )
		if( id("user_picker_"+xid) ) 
		    id("user_picker_"+xid).ondblclick();
	    break;
	}
    }

    function UpdateCliente(idcliente,nombrecliente,ruc,tipo){

	//Listado
        if(! usuarios[idcliente]) return;
        usuarios[idcliente].nombre = id("visNombreComercial").value; 
        usuarios[idcliente].legal  = id("visNombreLegal").value;
        usuarios[idcliente].email  = id("visEmail").value;
        usuarios[idcliente].ruc    = id("visNumeroFiscal").value;
        usuarios[idcliente].obs    = id("visComentarios").value;
        usuarios[idcliente].dir    = id("visDireccion").value;
        usuarios[idcliente].telf   = id("visTelefono1").value;
        usuarios[idcliente].tipo   = id("visTipoCliente").value;
        usuarios[idcliente].naci   = id("visFechaNacimiento").value;

	//Box
        id("user_picker_nombre_"+idcliente).setAttribute("label",nombrecliente);		
        id("user_picker_ruc_"+idcliente).setAttribute("label",ruc);
        id("user_picker_tipo_"+idcliente).setAttribute("label",tipo);

        //alert(c_gpos+po_clientemodificado);

	id("tab-vistacliente").setAttribute("selected",false);
	id("tab-vistacliente").setAttribute("collapsed",true);
	id("tab-selcliente").setAttribute("selected",true);
	id("tab-boxclient").setAttribute("selectedIndex",0);
    }


    function AltaCliente(){
	var vlNombrecomercial = id("NombreComercial").value;
	var vlNombrelegal     = id("NombreLegal").value;
	var vlDireccion       = id("Direccion").value;
	var vlEmail           = id("Email").value;
	var vlNumeroFiscal    = id("NumeroFiscal").value;
	var itemfrom          = '';
	var esCorporativo     = (id("TipoCliente").value!='Particular');
	var esIndependiente   = (id("TipoCliente").value=='Independiente');
	var txtNumeroFical    = (esCorporativo )? 'RUC':'DNI';

	//RUC
	if ( esCorporativo )  
	    esNumeroFiscal = (vlNumeroFiscal.length == 11 )? false:true; 
	//DNI
	if ( !esCorporativo ) 
	    esNumeroFiscal = (vlNumeroFiscal.length == 8 )? false:true;

	xvalmsj = '';

	if(vlNombrecomercial=='')
	    xvalmsj += ' \n       - Nombre';

	if(vlNombrelegal=='' && esCorporativo && !esIndependiente)
	    xvalmsj += ' \n       - Nombre Legal';
/*
	if(vlDireccion=='')
	    xvalmsj += '\n       - Direccion';
*/
	if( esNumeroFiscal && esCorporativo )
	    xvalmsj +=' \n       - '+txtNumeroFical;

	if(xvalmsj)
	    return alert(c_gpos + "\n      Complete correctamente el formulario; "+
			 "items faltantes:\n "+xvalmsj);

	EnviarCliente(false,0);
    }

    function ModificarCliente(){

	var vlNombrecomercial = id("visNombreComercial").value;
	var vlNombrelegal     = id("visNombreLegal").value;
	var vlDireccion       = id("visDireccion").value;
	var vlEmail           = id("visEmail").value;
	var vlNumeroFiscal    = id("visNumeroFiscal").value;
	var itemfrom          = '';
	var esCorporativo     = (id("visTipoCliente").getAttribute("value")!='Particular')? true:false;
	var esIndependiente   = (id("visTipoCliente").value=='Independiente');
	var txtNumeroFical    = (esCorporativo )? 'RUC':'DNI';

	//RUC
	if ( esCorporativo )  
	    esNumeroFiscal = (vlNumeroFiscal.length == 11 )? false:true; 

	//DNI
	if ( !esCorporativo ) 
	    esNumeroFiscal = (vlNumeroFiscal.length == 8 )? false:true;

	xvalmsj = '';

	if(vlNombrecomercial=='')
	    xvalmsj += ' \n       - Nombre';

	if(vlNombrelegal=='' && esCorporativo && !esIndependiente)
	    xvalmsj += ' \n       - Nombre Legal';
/*
	if(vlDireccion=='')
	    xvalmsj += '\n        - Direccion';
*/
	if( esNumeroFiscal && esCorporativo )
	    xvalmsj +=' \n       - '+txtNumeroFical;

	if(xvalmsj)
	    return alert(c_gpos + "\n      Complete correctamente el formulario; "+
			 "items faltantes:\n "+xvalmsj);
	
        EnviarCliente(true,preSeleccionadoCliente);
    }

    function getDatoCliente(vistamodificada,nombre) {
        var nombrefinal = nombre;
        if(vistamodificada)
            nombrefinal = "vis"+nombrefinal;

        var obj = id(nombrefinal);
        if(obj)
            return encodeURIComponent(obj.value);

        return false;
    }
    function getDatoClienteTelef(vistamodificada,nombre) {
        var nombrefinal = nombre;
        if(vistamodificada)
            nombrefinal = "vis"+nombrefinal;

        var obj = id(nombrefinal);
        if(obj)
            return obj.value;
        return false;
    }


    function EnviarCliente(modificar,idcliente){
        var data;
        var nombrecliente = getDatoCliente(modificar,"NombreComercial");
        var nombrelegal   = getDatoCliente(modificar,"NombreLegal");
	var tipo          = getDatoCliente(modificar,"TipoCliente");
        var ruc           = getDatoCliente(modificar,"NumeroFiscal");
        var cr            = "&";

        if ( !nombrecliente || nombrecliente.length < 2) 
            return  alert(c_gpos+po_nombrecorto);

        if(!modificar){
            var url = "modulos/clientes/xaltacliente.php?modo=altarapida"
            data    = "modo=altarapida" + cr;
        } else {
            var url = "modulos/clientes/xaltacliente.php?modo=modificarcliente"
            data    = "modo=modificarcliente" + cr;	
            data    = "IdCliente=" +parseInt(idcliente)+ cr;				
        }

        data =  data + "NombreComercial=" + nombrecliente + cr;       
        data =  data + "NombreLegal=" + nombrelegal + cr;       
        data =  data + "Direccion=" + getDatoCliente(modificar,"Direccion") + cr;    
        data =  data + "CP=" + getDatoCliente(modificar,"CP") + cr;    
        data =  data + "Telefono1=" + getDatoClienteTelef(modificar,"Telefono1") + cr;    
        data =  data + "NumeroFiscal=" + getDatoCliente(modificar,"NumeroFiscal") + cr;    
        data =  data + "Comentarios=" + getDatoCliente(modificar,"Comentarios") + cr;    
        data =  data + "TipoCliente=" + getDatoCliente(modificar,"TipoCliente") + cr;    
        data =  data + "Email=" + getDatoCliente(modificar,"Email") + cr;    
        data =  data + "FechaNacimiento=" + getDatoCliente(modificar,"FechaNacimiento");    
	
        var xrequest = new XMLHttpRequest();
        xrequest.open("POST",url,false);
        xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xrequest.send(data);

        var respuesta = xrequest.responseText;//.split("=")[1];

        var idCliente = parseInt(respuesta);

        if (idCliente) {
            if(!modificar){

                aU(decodeURIComponent(nombrecliente),
		   parseInt(idCliente), 
		   0,ruc,0,0,0,
		   tipo,
		   getDatoClienteTelef(modificar,"Telefono1"),
		   decodeURIComponent(getDatoCliente(modificar,"Email")),
		   decodeURIComponent(getDatoCliente(modificar,"Direccion")),
		   decodeURIComponent(nombrelegal),
		   decodeURIComponent(getDatoCliente(modificar,"FechaNacimiento")),0,
		   decodeURIComponent(getDatoCliente(modificar,"Comentarios")) );

                LimpiarClienteForm();

                //alert(c_gpos+po_nuevocreado);
		id("buscaCliente").value = ruc;
		buscarCliente();
		id("tab-newcliente").setAttribute("selected",false);
		id("tab-selcliente").setAttribute("selected",true);
		id("tab-boxclient").setAttribute("selectedIndex",0);
		id("buscaCliente").focus();
            } else{
                return UpdateCliente(idCliente,decodeURIComponent(nombrecliente),ruc,tipo);
            }
        } else
            return alert(c_gpos+po_operacionincompleta);
    }

    function LimpiarClienteForm(){
        id("NombreComercial").value = "";
        id("NombreLegal").value     = "";
        id("Direccion").value       = "";
        id("Email").value           = "";   
        id("Telefono1").value       = ""; 
        id("NumeroFiscal").value    = "";    
        id("Comentarios").value     = "";    
    }

    function setTipoCliente(xtipo,xextra){

	var xnombre = ( xtipo == 'Particular')? 'Nombre':'Nombre Comercial';
	var eslegal = ( xtipo == 'Particular')? true:false;
	var esnif   = ( xtipo == 'Particular')? 'DNI':'RUC';
	var eslegal = ( xtipo == 'Independiente')? true:eslegal;
	var xnombre = ( xtipo == 'Independiente')? 'Nombre':xnombre;
	var xnumnif = ( xtipo == 'Particular')?  8:11; 
	var xnaci   = ( xtipo == 'Particular')?  false:true; 

	id(xextra+"NumeroFiscal").setAttribute("maxlength",xnumnif);
	id(xextra+"txtNFiscal").setAttribute("label",esnif);
	id(xextra+"mtxtNombreComercial").setAttribute("label",xnombre);
	id(xextra+"mtxtNombreLegal").setAttribute("collapsed",eslegal);
	id(xextra+"mtxtFechaNacimiento").setAttribute("collapsed",xnaci);
    }

    function VerClienteId(){

	if( !preSeleccionadoCliente || preSeleccionadoCliente == 1)
            return id("tab-vistacliente").setAttribute("collapsed",true);

        cargarClienteId();

	id("tab-suscripcion").setAttribute("collapsed",true); 
	id("tab-vistacliente").setAttribute("collapsed",false);
	id("tab-vistacliente").setAttribute("label", "Cliente: "+ id("visNombreComercial").value);
	id("tab-vistacliente").setAttribute("selected",true);
        id("tab-vistacliente").setAttribute("visuallyselected",true);

        id("tab-selcliente").setAttribute("visuallyselected",false);
	id("tab-selcliente").setAttribute("selected",false);
	id("tab-boxclient").setAttribute("selectedIndex",2);
	id("buscaCliente").focus();	
    }

    function cargarClienteId(){

        id("visNombreComercial").value = usuarios[ preSeleccionadoCliente ].nombre;
        id("visNombreLegal").value     = usuarios[ preSeleccionadoCliente ].legal;
        id("visEmail").value           = usuarios[ preSeleccionadoCliente ].email;
        id("visNumeroFiscal").value    = usuarios[ preSeleccionadoCliente ].ruc;
        id("visComentarios").value     = usuarios[ preSeleccionadoCliente ].obs;
        id("visDireccion").value       = usuarios[ preSeleccionadoCliente ].dir;
        id("visTelefono1").value       = usuarios[ preSeleccionadoCliente ].telf;
        id("visTipoCliente").value     = usuarios[ preSeleccionadoCliente ].tipo;
        id("visFechaNacimiento").value = usuarios[ preSeleccionadoCliente ].naci;

	setTipoCliente(usuarios[ preSeleccionadoCliente ].tipo,'vis');
	setTimeout('syncClientes()',4000);
    }


    var max_cli = 200;

    //Cambia de Dependiente
    function cambiaCliente() {
        var dep;
        var tDep = id("tCliente");

        for (var t=0;t<max_cli;t++) {
            dep = id ("clie_"+t);
            if (dep && dep.getAttribute("checked")){
                tDep.setAttribute( "value",dep.getAttribute("label"));
                return;
            }  
        }
    }

    function MostrarClienteForm() {
        document.getElementById("modoVisual").setAttribute("selectedIndex",2);
    }

    function OcultarClienteForm() {
        document.getElementById("modoVisual").setAttribute("selectedIndex",0);
    }

    function AltaClienteForm() {
        MostrarClienteForm();	
    }

        function CancelarAlta() {  
            OcultarClienteForm();
	}


        function validanombreCliente(itm){
	    //itm = itm.toUpperCase();		
	    itm = itm.replace('"',"");
	    itm = itm.replace("'","");
	    itm = itm.replace("  "," ");
	    itm = itm.replace("/","");
	    itm = itm.replace("(","");
	    itm = itm.replace(")","");
	    itm = itm.replace(",","");
	    //itm = itm.replace(".","");
	    itm = itm.replace(":","");
	    itm = itm.replace(";","");
	    itm = itm.replace("?","");
	    itm = itm.replace("¿","");
	    itm = itm.replace("~","");
	    itm = itm.replace("[","");
	    itm = itm.replace("]","");
	    itm = itm.replace("*","");
	    itm = itm.replace("_","");
	    itm = itm.replace(">","mayor");
	    itm = itm.replace("<","menor");
	    itm = itm.replace("@","en");
	    //itm = itm.replace("&","Y");
	    itm = itm.replace("NRO","Nº");
	    itm = itm.replace("#","Nº");
	    //itm = itm.toUpperCase();

	    return itm;
	}

        function validanifCliente(nif,xextra){

	    if( isNaN( parseInt(nif) ))
		return nif = '';

	    if( parseInt(nif.length) == 8 || parseInt(nif.length) == 11 )
		return nif;

	    alert(c_gpos +' Ingrese correctamente los datos:\n\n     - '+
		  id(xextra+"txtNFiscal").getAttribute("label")+' incorrecto. ');  
	    return nif = '';
	}

        function validamailCliente(email){

	    if(email !='-' ){

		expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		if ( !expr.test(email) )
		{
		    alert( c_gpos + "\n La dirección de correo " + email + " es incorrecta.");
		    return email='';
		} 
		else 
		    return email;
	    }
	    else
		return email;

	}

        function ckeckNombreComercial(){
		var nom = id("NombreComercial");
		nom.value =  validanombreCliente(nom.value);
		for(var i=0;i<idusuarios.length;i++){
                    var idcliente = idusuarios[i];
		    
		    if( usuarios[idcliente].nombre == nom.value )
		    {
			id("NombreComercial").value = '';
			id("Direccion").value = '';
			id("NumeroFiscal").value = '';
			return alert( c_gpos  +'\n El cliente '+
				     usuarios[idcliente].nombre+
				     ' esta registrado.\n '+
				     'Busquelo por su DNI/RUC '+
				     usuarios[idcliente].ruc );
		    }
		}
	}

        function validaCliente(xthis) {

	    var extranif = 'vis';
	    
	    switch ( xthis.id ) {
            case 'NombreComercial':		
	    case 'NombreLegal':
            case 'Direccion':
	    case 'visNombreComercial':	
	    case 'visNombreLegal':
	    case 'visDireccion':		
		xthis.value =  validanombreCliente(xthis.value);
		break;

            case 'NumeroFiscal':
		var extranif = '';
	    case 'visNumeroFiscal':		 
		xthis.value  =  validanifCliente(xthis.value,extranif);

		//if(!xthis.value ) return;
		var xid = "";
		if(usuarios[preSeleccionadoCliente])
		    xid = usuarios[preSeleccionadoCliente].ruc;

		if(xid && !xthis.value){
		    id("visNumeroFiscal").value = xid;
		    return;
		}

		for(var i=0;i<idusuarios.length;i++)
		{
                    var idcliente = idusuarios[i];
		    if( parseInt( usuarios[idcliente].ruc ) == parseInt( xthis.value ))
		    {
			if( extranif == '') id("NumeroFiscal").value = "";//LimpiarClienteForm();
			return alert( c_gpos + '\n - El cliente '+
				      usuarios[idcliente].nombre+
				      ' está registrado.\n'+
				      ' - Búsquelo por su Registro Fiscal: '+
				      usuarios[idcliente].ruc );
		    }
		}
		break;


            case 'Email':		
	    case 'visEmail':		
		xthis.value = validamailCliente( xthis.value );
		break;
		
	    }
        }

/*+++++++++++++++++++++++++++++ CLIENTES  ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ TICKETS  ++++++++++++++++++++++++++++++++++*/

    var data_tickets;
    var ModoDeTicket = "venta";

    function NuevoModo(){
	var MANTENER_MODO = true;
	var rpedido = id("rPedido");

	id("comprobante").collapsed='false';
	id("NumeroDocumento").readonly="false";

	AjustarEtiquetaModo();
	//CancelarVenta(MANTENER_MODO);
	TicketAjusta();

	//Presupuestos
	selTipoPresupuesto(0);

	//Pedidos
	var mensajeArea =  id("modoMensajes");
	if(rpedido.selected){
	    //ToggleMensajes
	    mensajeArea.setAttribute("selectedIndex",2);
	    ponMPparaPedido();//Carga pedientes meta productos 
	} else 
	    mensajeArea.setAttribute("selectedIndex",0);
    }


    function EncapsrTextoParaImprimir(texto) {

	var salida;
	var header = 'data:text/html,';

	salida = "<html><head><title>gPOS</title>"+
            "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />"+
            "<style type='text/css' media='screen'>"+
            ".ticket{"+
            "	border: 1px gray solid;"+
            "	padding: 4px;	"+
            "}"+
            ".botonera{"+
            "	border: 1px gray solid;"+
            "	padding: 1px;	"+
            "	margin: 2px;"+
            "	text-align: center;"+
            "	align: center;"+
            "}"+
            "</style>"+	
            "<style type='text/css' media='print'>"+
            "input {	visibility: hidden;}"+
            ".botonera { visibility: hidden; }"+
            ".ticket {"+
            "	border. none;"+
            "	border: 0pt;"+
            "	padding: 0px;"+
            "	font-size: 95%;"+
            "	font-family: arial;"+
            "}"+
            "</style>"+
            "</head><body><div class='ticket'><xmp>\n";
	salida = salida + texto;
	salida = salida + "</xmp></div>\n<script>\nsetTimeout('window.print()',100);\n</script>"+
            "<div class='botonera'>"+
            "<input onclick=' window.print()' value='"+po_imprimircopia+"' type='button'>"+
            "<input onclick=' document.title=' '; value='prueba' type='button'>"+
            " <input onclick='window.close()' value='"+po_cerrar+"' type='button'></div>"+
            "</body></html>";
	salida = header + encodeURIComponent( salida );
	return salida;
    }

    function ActualizaPeticion() {
	var cr         = "\n";
	var color      ="black";
	var pendiente  = 0;
	var abonopendiente = 0;
	var xentrega   = id("peticionEntrega");
	//xentrega.value = (trim( xentrega.value ) == '')? 0:xentrega.value;
	var xpago = id("modoDePagoTicket").value;

	//Bono
	if(xpago == 10){
	    if(xentrega.value > usuarios[UsuarioSeleccionado].bono)
		xentrega.value = usuarios[UsuarioSeleccionado].bono;
	}
	//Credito
	if(xpago == 9){
	    if(xentrega.value > usuarios[UsuarioSeleccionado].credito)
		xentrega.value = usuarios[UsuarioSeleccionado].credito;
	}
	
	if(id("peticionBono")){
	    if(id("peticionBono").value > usuarios[UsuarioSeleccionado].bono)
		id("peticionBono").value = usuarios[UsuarioSeleccionado].bono;
	}

	if(!modoMultipago){
            var entrega = parseFloat(CleanMoney(xentrega.value));
	} else {
            var entrega = 0;
            entrega += parseFloat(CleanMoney(id("peticionEfectivo").value));
            entrega += parseFloat(CleanMoney(id("peticionBono").value));
            entrega += parseFloat(CleanMoney(id("peticionTarjeta").value));
	}

	pendiente = parseFloat(entrega) - parseFloat( formatDinero(Global.totalbase) );
	abonopendiente = parseFloat( Global.totalbase ) - parseFloat( entrega );
        color     = ( parseInt(pendiente*100) >=0.01)? "green":"red";
        //pendiente = ( parseInt(pendiente*100) >=0.01)? pendiente:'0.00'; 
	
        id("peticionPendiente").setAttribute("label", formatDinero(pendiente));
	id("peticionPendiente").style.color = color;
	Local.AbonoClientePendiente = ( entrega < Global.totalbase )? abonopendiente:0;
    } 

    function validarPagoCliente(){
	var xpago  = id("modoDePagoTicket").value;
	var xmonto = 0;

	switch(xpago){
	    case '10' : xmonto = usuarios[UsuarioSeleccionado].bono;    break;
	    case '9'  : xmonto = usuarios[UsuarioSeleccionado].credito; break;
	}

	id("peticionEntrega").value = parseFloat(xmonto);
	ActualizaPeticion();
    }

    function AbrirPeticion(xval){

	RecalculoTotal();
	AjustarEtiquetaModo();

	//Stock && Pedido modificar
	var p_stock   = id("prevt-stock").getAttribute("checked");
	var r_pedid   = id("rPedido").getAttribute("selected");
	var base      = formatDinero( Global.totalbase );	
	var entregado = base;
	var pendiente = "0.00";
	var tcliente  = id("tCliente").label;

	if( p_stock != "true" && IdPresupuesto !=0 && r_pedid != "true")
	    return alert("gPOS: \n    Active check - [x] Stock - "+
			 "del Ticket Actual, para continuar.")

	if ( ticketlist.length == 0 )
	    return alert("gPOS: Ticket Actual Vacio"+
			 "\n\n Liste por lo menos un producto.");

	if ( checkPreTicket() ) return;

	if ( UsuarioSeleccionado == 1) { 
	    if(ModoDeTicket == "pedidos" || ModoDeTicket == "cesion"){
	    	setdefaulttipocomprobante(tcliente,'Ticket');
		return id("buscaCliente").focus();
	    }
	}
	
	switch( ModoDeTicket ){
	case 'cesion':
	    entregado = "0.00";
	    pendiente = formatDinero( Math.abs(Global.totalbase) );
	case 'venta':
	    AjustarEtiquetaDefault();
	    mostrarTicketPromocion();
	    cargarBonoUsuario();
	    break;
	case 'pedidos':
	    AjustarEtiquetaPedido();
	    break;
	}	    
	//Pago...
	id("peticionTotal").setAttribute("label", base);
	id("peticionEntrega").value = entregado;
	id("peticionPendiente").setAttribute("label", formatDinero(0));	
	id("NumeroDocumento").removeAttribute('readonly','readonly');	
	id("comprobante").setAttribute("collapsed", false);
	CambiarModoReserva(false);
	ActualizaPeticion();

	if(!esActivoServer) 
	    return alert('gPOS TPV :\n\n '+po_servidorocupado);

	habilitarMensajePrivado('e_pedidos');
	id("modoVisual").setAttribute("selectedIndex",6);

        id("panelDerecho").setAttribute("collapsed",true);
	resizelistboxticket(false);
	
	keyLoadTipoComprobante(xval);

	id("peticionEntrega").focus();
    }

    function cargarBonoUsuario(){

	if( usuarios[UsuarioSeleccionado].bono == 0 ) return;

	var userbono    = parseFloat( usuarios[UsuarioSeleccionado].bono );
	var ticketmonto = parseFloat( Global.totalbase );
	var loadbono    = ( ticketmonto > userbono )? userbono:ticketmonto;

	id("peticionBono").value = loadbono;
	modoMultipago            = false;	
	ModoMultipago();
    }

    function CerrarPeticion(){
	HabilitarImpresion();//si el boton imprimir fue desabilitado, se rehabilita
	id("modoVisual").setAttribute("selectedIndex",0);
	VerTPV();
	//CBFocus();
    }

    function VerGuiaRemision(){

	//if( !Local.esCajaTPV) return;
	var serie  = id("SerieNDocumento").value;
        var numero = id("NumeroDocumento").value;
        
        ImprimirTicket();
        
	id("panelDerecho").setAttribute("collapsed",true);
	id("modoVisual").setAttribute("selectedIndex",Vistas.guia);

        frameGuiaRemision.id("SerieGuia").setAttribute("value",serie);
        frameGuiaRemision.id("NumeroGuia").setAttribute("value",numero);
        frameGuiaRemision.id("txtTipoGuia").setAttribute("value",'Remitente');
        frameGuiaRemision.id("BotonCancelarImpresion").setAttribute("collapsed",'true');
        
	resizelistboxticket(false);
    }

    function CerrarImprimir(){
	HabilitarImpresion();//TODO: aqui tambien?
	id("modoVisual").setAttribute("selectedIndex",0);
	id("fichaProducto").setAttribute("src","about:blank");
	CBFocus();
    }

    function ImprimirTicket() {

	//  - TICKET     : 0
	//  - BOLETA     : 1
	//  - FACTURA    : 2
	//  - ALBARAN    : 4
	//  - PEDIDOS    : 5
	var nroDocumentoElemeto = id("NumeroDocumento");
	var sreDocumentoElemeto = id("SerieNDocumento");
	var modo                = id("rgModosTicket").value;
	var btnAcepImp          = id("BotonAceptarImpresion");
	var btnCancImp          = id("BotonCancelarImpresion");
	var idDocumento         = id("idDocTPV").value;
	var nroDocumento        = 0;
	var sreDocumento        = 0;
	var noticket            = 0;
	var textdoc             = 'Ticket';
	var comprobante         = 0;
	var adelanto            = 0;
	var vigencia            = 0;
        var nsmprod             = '';
	var mensaje             = '';

	//ValidaTicket...
	ValidaTicket(modo,'inicia',false);
	
 	//TipoDocumento
	switch( idDocumento )
	{
	case '0': textdoc = "Ticket"; comprobante  = 1; break;
	case '1': textdoc = "Boleta"; break;
	case '2': textdoc = "Factura"; break;
	case '4': textdoc = "Albaran"; break;
	case '5': textdoc = "Proforma"; break;
	default: return;
	}		
    
	if( comprobante == 0 )
	{
	    if( !validarNroDocumento() ) return; 
	    if( modo == "pedidos" ) habilitarMensajePrivado('e_pedidos');//Recarga NroProforma

	    nroDocumento              = parseInt(nroDocumentoElemeto.value,10);	
	    sreDocumento              = parseInt(sreDocumentoElemeto.value,10);	
            nroDocumentoElemeto.value = "";
	    sreDocumentoElemeto.value = "";
            noticket                  = 1;
	}
	
	//Cotizacion...
	if( modo == "pedidos" )
	{
	    //Adelanto...
	    adelanto = parseFloat(id("adelantoProforma").value);//Adelantos...
	    if( adelanto > 0 ) 
		ingresoAdelantoDineroCaja(adelanto,nroDocumento);//Ingresa Adelanto
	    else 
		adelanto = 0;
	    //Vigencia...
	    vigencia = parseInt(id("vigenciaProforma").value);//Vigencia Mensaje
 	    if( vigencia < 1 || isNaN(vigencia) ) 
		vigencia = 0;

	    //MetaProducto...
	    nsmprod  = validaMProductoTicket();//CB_MetaProductos...//id("serieMProducto").value;
            delMProductoToArrMP(nsmprod);//Elimina MProductos del ArrMP

	    //Mensaje...
	    mensaje  = AdjuntarObservacionesPedido(modo,vigencia,nsmprod,adelanto);//Registra...
	    if( mensaje == 0 ) mensaje='';
	}
	
	//Inicia...
	var data,firma,unidades, precio, descuento,codigo;
	var xrequest    = new XMLHttpRequest();
	var url         = "";
	var prod        = Array();
	var sr          = parseInt(Math.random()*999999999999999);
        var esCopia     = (modo == 'copia')? 1:0;//Si es una copia?		
	var pticket     = t_CrearTicket(esCopia,noticket);	//Inicia validbles Ticket
	var text_ticket = pticket.text_data;
	var post_ticket = pticket.post_data;
	var xres        = 1;

	if (!esCopia) {
            DesactivarImpresion();
	    //Ticket...
            url =
		"xcreaticket.php?modo=creaticket&"+
		"moticket="+escape(ModoDeTicket)+"&"+
		"tpv_serialrand="+sr+"&"+
		"nroDocumento="+nroDocumento+"&"+
		"sreDocumento="+sreDocumento+"&"+
		"idDocumento="+idDocumento+"&"+
		"Reservar="+Local.Reservar;
            xrequest.open("POST",url,false);
            xrequest.setRequestHeader('Content-Type',
				      'application/x-www-form-urlencoded; charset=UTF-8');
            xrequest.send(post_ticket+
			  "mensaje="+mensaje+"&"+
			  "nsmprod="+nsmprod+"&"+
			  "vigencia="+vigencia+"&"+
			  'DocumentoVenta=Venta'+'&'+
			  'IdPresupuesto='+IdPresupuesto);
	    
            xres = xrequest.responseText;	    
	    //alert(xres);
	    //Valida...
            //x~val~90001002:15:5:4:0;90003005:17:2:5:fff,ggg
	    prod = xres.split("~val~");    

	    //ValidaCaja....
	    if( prod[0] == 'noCAJA' ) 
	    {
		frameArqueo.esRecibidaListaArqueos = true; 
		frameArqueo.onLoadFormulario();
		id("botonImprimir").setAttribute("disabled",true);
		CerrarPeticion();
		Local.esCajaCerrada = 1;
		return alert("gPOS CAJA TPV:\n\n"+
			     "        **** CERRADA *** \n\n"+
			     "  - Abrir Caja, para continuar -");
	    }

	    //Problemas...
	    if(prod[1]) return ValidaTicket(modo,'valida',prod[1]);

	    //OK....
            xres = parseInt(xres);	    
	    ValidaTicket(modo,'termina',false);
	    DesactivarImpresion();

	    if( !xres )
		return alert('gPOS: '+po_ticketnoserver+" Comprobante \n "+xrequest.responseText);
	    //Sync
	    if( modo!="pedidos" )
	    {
		Local.esSyncStockPost    = true;
		Local.esSyncClientesPost = true;
		setTimeout("syncCoreTPV()",4200);//Lanza demonio sync core
	    }
	}
	
	//Impresion comprobante...
	switch( comprobante ){
	case 0://Facturas,Boleta,Proforma,guia
	    var c   = ( modo!="pedidos" )? "Venta":"Presupuesto";
	    var url = 
		"services.php?"+
		"modo=obtenerDatosComprobante"+c+"&"+
		"nroComprobante="+nroDocumento+"&"+
		"sreComprobante="+sreDocumento+"&"+
		    "esVenta=off"+"&"+
		    "tipoComprobante="+textdoc;
		
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    
	    var dtComprobante = xrequest.responseText.split("~");
	    var importe       = dtComprobante[0];
	    var codcliente    = dtComprobante[1];
	    var idcomprobante = dtComprobante[2];
	    var nroDocumento  = dtComprobante[3];
	    var nroSerie      = dtComprobante[4];
	    var importeletras = convertirNumLetras(importe,1);
	    importeletras     = importeletras.toUpperCase();
	    cIdComprobante    = idcomprobante;
            
	    //Liga
	    var url=
		"modulos/fpdf/imprimir_"+textdoc+"_tpv.php?"+
		"nro"+textdoc+"="+nroDocumento+"&"+
		"totaletras="+importeletras+"&"+
		"codcliente="+codcliente+"&"+
		"nroSerie="+sreDocumento+"&"+
		"nombreusuario="+Local.nombreDependiente+"&"+
		"idcomprobante="+idcomprobante;
            cURLPrint = url;
	    break;

	case 1://Ticket
	    if(Local.Imprimir)
		top.TicketFinal = window.open(EncapsrTextoParaImprimir(text_ticket),
					  "Consola Ticket",
					  "width=400,height=600,scrollbars=1,resizable=1",
					  "text/plain");
	    break;
	}
	
	//Status presupuesto 'confirmado'
	
	// Finaliza el proceso
        CerrarPeticion();
        habilitarControles(); 
	document.getElementById('busquedaVentas').removeAttribute('disabled');
        document.getElementById('NumeroDocumento').removeAttribute('disabled');
        CancelarVenta();
	
	//AseguramosCliente...
	//var radiofactura = id("radiofactura");
	//if(radiofactura.selected) tipocomprobante(1);

	//CargamosComboPreventa...
	if( modo == "pedidos" )  
	    generadorCargarPresupuesto('Proforma',xres,'id');

	//EliminarItemComboPreventa...
	//alert(IdTipoPresupuesto);
	//if( modo == "pedidos" && IdPresupuesto !=0 )
	//if(IdTipoPresupuesto==2)
	//generadorEliminaPresupuesto('Proforma',IdPresupuesto);

	//if( IdPresupuesto !=0 && modo != "pedidos" ) 
	//StatusPresupuentos...
	if( IdPresupuesto !=0 ) habilitarPresupuesto(0);

	//Set mensaje
	habilitarMensajePrivado();

	//LabelBotonAceptar
	btnAcepImp.setAttribute("label"," Aceptar ");
	btnAcepImp.setAttribute("image","img/gpos_imprimir.png");
	btnCancImp.setAttribute("collapsed","false");

	NuevoModo();

	//Lanzamos Impresion Comprobante
	if( comprobante == 0 && Local.Imprimir)
            if(textdoc != 'Albaran') imprimirComprobantePDF(url);//Imprime Comprobante

    }

    function imprimirComprobantePDF(url){
        location.href=url;
    }

    function CambiarModoImpresion(xvalue){
	Local.Imprimir = xvalue;
    }

    function CambiarModoReserva(xvalue){
	Local.Reservar = (xvalue)? 1:0;
        id("checkreservar").setAttribute("checked", xvalue);
	id("checkreservar").setAttribute("collapsed", ( ModoDeTicket == 'pedidos' ));
    }

    function ValidaTicket(modo,xcheck,xprod){

	switch( modo ){
	case 'venta': break;
	case 'cesion': break;
	case 'mproducto': break;
	default:
	    return;
	}

	var btnAcepImp = id("BotonAceptarImpresion");
	var btnCancImp = id("BotonCancelarImpresion");

	switch( xcheck ){

	case 'inicia': 

	    //Mensaje Boton...
	    btnAcepImp.setAttribute("label","  Validando Ticket  ");
	    btnAcepImp.setAttribute("image","");
	    btnCancImp.setAttribute("collapsed","true");
	    break;

	case 'valida':

	    //Ticket...
	    VerTPV();
	    btnAcepImp.setAttribute("label"," Aceptar ");
 	    btnAcepImp.setAttribute("image","img/gpos_imprimir.png");
	    btnCancImp.setAttribute("collapsed","false");

	case 'validamproducto':

	    //Sync...
	    syncProductosPostTicket();
	    //Lineas...
	    var aprod = xprod.split(";");
    	    var m_m   = '';
 	    for (var p in aprod )
	    {
		//cb:idpedidodet:unidad:unidadalma:seies,series
		var arr           = aprod[p].split(":");
		var xcb           = arr[0];
		var xpedidodet    = arr[1];
		var axpedidodet   = xpedidodet.split("-");//Valida pedidodet Kardex
		//var xvalpedidodet = ( axpedidodet[1] )? axpedidodet[1]:false;//Error Kardex
		var xunidades     = parseInt(arr[2]);
		var xunidadesalma = parseInt(arr[3]);
		var xseries       = arr[4];
		var xunidadesexd  = 0;
		var lineaid       = "tic_unid_"+xcb;
		var lineaunidades = id(lineaid);
		var i_m           = '';

		//Kardex estado pedidodet
		if( axpedidodet[1] )
		{
		    //Unidades...
		    xunidades     = 0;
		    xunidadesalma = 0;
		    xseries       = 0;

		    //Quita producto...
		    var t,codigo;	
		    var fila;
		    for (t=0;t<ticketlist.length;t++) 
		    {
			if (ticketlist[t]){

			    codigo = ticketlist[t];
			    
			    if ( codigo == xcb ){

				fila = id("tic_" + codigo);
				fila.parentNode.removeChild(fila);
				ticket[codigo] = null;
				ticketlist.splice(t,1);
			    }		 
			}
		    }	
		    //Consolida...
		    RecalculoTotal();

		    //Mensaje...
		    i_m    = "\n     -  ***ERROR en kardex, pedido detalle -"+axpedidodet[0]+"-";
		    i_m   += "\n     -  Mensaje **"+axpedidodet[1]+"**";
		    i_m   += "\n     [-] Producto removido de la lista.";
		    i_m   += "\n     [*] contactar con el administrador de la Base de Datos.";
		}		

		//Unidades...
		if( xunidades > xunidadesalma )
		{
		    //Unidades...
		    xunidadesexd         = parseFloat( xunidades ) - parseFloat( xunidadesalma );
		    ticket[xcb].unidades = parseFloat(ticket[xcb].unidades) - parseFloat(xunidadesexd);
 		    lineaunidades.value  = ticket[xcb].unidades;
		    //PedidoDet...
		    CargarPedidoDetFila(xcb,ticket[xcb].unidades );
		    //Consolida...
		    RecalculoTotal();
		    //Mensaje...
		    i_m    = "\n     - Existe "+xunidadesalma+" "+productos[xcb].unid+
			     "(s) del pedido - "+xpedidodet+" - en almacén.";
		    i_m   += "\n     - El pedido excede "+xunidadesexd+" "+productos[xcb].unid+"(s)";
		    i_m   += "\n     [-] Producto atendido descontando el exceso.";
		}		
		//Serie...
		if( xseries != 0)
		{
		    //Sincroniza...
		    quitaSeriesTicket(xcb,xseries);
		    //Mensaje...
		    i_m += "\n     NS:  "+xseries+" no disponible(s).";
		    i_m += "\n     [-] Producto atendido descontando el exceso.";
		} 		
		//Mensaje...
		if(i_m!='') m_m += "\n   Producto: "+productos[xcb].producto+" "+ i_m +"\n";
	    }
	    //Lanza Mensaje...
	    if(m_m!='')	alert ("gPOS: TPV TICKET\n "+m_m);

	    break;

	case 'termina': 
	    btnAcepImp.setAttribute("label","  Ticket OK  ");
	    break;
	}

    }


    function GuardarPreVentaTPV() {    
	//IdDocuemnto    : VALUE
	//  - VENTA      : 0
	//  - CESION     : 1

	id("NOM").focus();

	var modo     = id("rgModosTicket").value;
	var tcliente = id("tCliente").label;
	var noticket = 1;
	var p_stock  = id("prevt-stock").getAttribute("checked");

	//Stock Check...
	if( p_stock != "true" && IdPresupuesto !=0 )
	    return alert( c_gpos + "\n  - Active check - [x] Stock - del Ticket Actual")

	//Modo...
	var modotpv       = (modo=="venta"|| modo=="cesion" )?1:0;
	var alertpreventa = (modotpv == '0')?'\n - Selecione el modo - CONTADO ó CREDITO - en el TPV.':'';

	//Existencias...
	if(ticketlist.length == 0)
	    return alert( c_gpos + "\n - El listado del ticket actual esta vacio."+
			 "\n - Para Guardar la Pre-Venta liste un articulo."+alertpreventa);

	//Modo...
	if(modotpv == '0') 
	    return alert( c_preventa + alertpreventa);

	//Inicia variables Ajax 
	var data,firma,resultado,esCopia,unidades,precio,descuento,codigo;
	var xrequest    = new XMLHttpRequest();
	var url         = "";
	var sr          = parseInt(Math.random()*999999999999999);
	var preticket   = t_CrearTicket(esCopia,noticket);
	var text_ticket = preticket.text_data;
	var post_ticket = preticket.post_data;
	var modoticket  = 'preventa';

	DesactivarImpresion();

	//Cargar...
	url =
	    "xcreapreticket.php?modo="+modo+"&"+
	    "moticket="+modoticket+"&"+
	    "tpv_serialrand="+sr;
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type',
				  'application/x-www-form-urlencoded;'+
				  ' charset=UTF-8');
	xrequest.send(post_ticket+'&IdPresupuesto='+IdPresupuesto);
	
	resultado = xrequest.responseText;
	//alert(resultado);
	resultado = parseInt(resultado);

	if (!resultado )	
            return alert( c_gpos + po_error + "\n - Al guardar Pre-Venta");	

	//Numero Pre-Venta...
	alert("gPOS Pedido Nro:\n\n"+
	      "          - "+resultado+" - ");

	//Finaliza...
	CerrarPeticion();
	habilitarControles(); 
	document.getElementById('busquedaVentas').removeAttribute('disabled');
	VaciarDetallesVentas(); 
	document.getElementById('NumeroDocumento').removeAttribute('disabled');
	CancelarVenta();
	HabilitarImpresion();

	//Cliente...
	//var radiofactura = id("radiofactura");
	//if(radiofactura.selected)
	//    tipocomprobante(1); 

	//Item...
	generadorCargarPresupuesto('Preventa',resultado,'cd');

	//Status Preventa...
	if( IdPresupuesto !=0)
	    habilitarPresupuesto(2);

	mostrarMensajeTPV(1,'...pedido Nro - '+resultado+' -  registrado',5500);
    }

    function setTextDocumento(val,docTPV){
	id('TextoDocumentoTPV').label='Serie - Nro '+ val;
	id('idDocTPV').value=docTPV;
    }


    function tipocomprobante(opcion){

	//RADIO SELECT
	var comprobante   = opcion;
	var tcliente      = id("tCliente").label;
	var radioboleta   = id("radioboleta");
	var radioticket   = id("radioticket");
	var radiofactura  = id("radiofactura");
	var radioalbaran  = id("radioalbaran");
	var radioproforma = id("radioproforma");
	var elemento      = id("gruponb");
	var elemento2     = id("NumeroDocumento");
	var modo          = id("rgModosTicket").value;
        var esAlba = (opcion == 4)? false:true;

        id("BotonAceptarImpresion").setAttribute("collapsed",!esAlba);
        //id("BotonCancelarImpresion").setAttribute("collapsed",!esAlba);
        id("BotonGuiaRemision").setAttribute("collapsed",esAlba);
        
	//Albaran y Sesion
	/*if(modo!="cesion"){
	    radioalbaran.setAttribute("disabled", true);
	    radioalbaran.setAttribute("selected", false);
	}*/

	//Factura
	if( opcion == 2 ){
	    if ( UsuarioSeleccionado == 1 ){

		setdefaulttipocomprobante(tcliente,'Factura');
		return id("buscaCliente").focus();
	    } else {
		elemento.setAttribute("collapsed", "false");
		radioproforma.setAttribute("disabled", "true");
		setTextDocumento('Factura',opcion);
		CargarNroDocumentoVenta(opcion,0);
		elemento2.focus();
		return;
	    }
	}

	//Proforma
	if( opcion == 5 ){
	    var alertuser = '';
	    var modo = id("rgModosTicket").value;
	    if(modo=="pedidos"){
		if ( UsuarioSeleccionado == 1 ){
		    setdefaulttipocomprobante(tcliente,'Proforma');
		    return id("buscaCliente").focus();
		} 
		else {
		    elemento.setAttribute("collapsed", "false");
		    radiofactura.setAttribute("selected", "false");
		    radioboleta.setAttribute("selected", "false");
		    radioproforma.setAttribute("selected", "true");
		    setTextDocumento('Proforma',opcion);
		    CargarNroDocumentoVenta(opcion,0);
		    elemento2.focus();

		    return;
		}
	    }
 	    else{

		if ( UsuarioSeleccionado == 1 )
		    alertuser = '\n - Selecione un cliente diferente al - '+tcliente+' - ';
		alert('gPOS Proformas: \n - Selecione - PROFORMA - en el TPV '+alertuser);
  		setTextDocumento('',0);
		radioproforma.setAttribute("selected", "false");
		radioticket.setAttribute("selected", "true");
		return VerTPV();
	    }
	}


	//Boleta
	if( opcion == 1 ){
            elemento.setAttribute("collapsed", false);
	    radiofactura.setAttribute("selected", false);
	    radioticket.setAttribute("selected", false);
	    radioproforma.setAttribute("selected", false);
	    radioproforma.setAttribute("disabled", true);
	    radioboleta.setAttribute("selected", true);
	    setTextDocumento('Boleta',opcion);
	    CargarNroDocumentoVenta(opcion,0);
            elemento2.focus();
	    return;
	}

	//Albaran
	if( opcion == 4 ){
	    var alertuser = '';

	    if(modo=="cesion"){
		if ( UsuarioSeleccionado == 1 ){
		    setdefaulttipocomprobante(tcliente,'Albaran');
		    return id("buscaCliente").focus();
		} 
		else {
		    elemento.setAttribute("collapsed", "false");
		    radiofactura.setAttribute("selected", "false");
		    radioboleta.setAttribute("selected", "false");
		    radioalbaran.setAttribute("selected", "true");
		    setTextDocumento('Albaran',opcion);
		    CargarNroDocumentoVenta(opcion,0);
		    elemento2.focus();
		    return;
		}
	    }
 	    else{

		if ( UsuarioSeleccionado == 1 )
		    alertuser = '\n - Selecione un cliente diferente al - '+tcliente+' - ';
		alert('gPOS Albaran: \n - Selecione - CREDITO - en el TPV '+alertuser);
  		setTextDocumento('Boleta',1);
		radioalbaran.setAttribute("selected", "false");
		radioboleta.setAttribute("selected", "true");
		return VerTPV();
	    }
	}

	//Ticket
	if( opcion == 0 ){

            elemento.setAttribute("collapsed", "false");
	    radiofactura.setAttribute("selected", "false");
	    radioticket.setAttribute("selected", "true");
	    radioproforma.setAttribute("selected", "false");
	    radioboleta.setAttribute("selected", "false");
	    radioalbaran.setAttribute("selected", "false");
            elemento.setAttribute("collapsed", "true");
	    id("NumeroDocumento").value='';
	    setTextDocumento('',opcion);
	    return;
	}
    }


    function validarNroDocumento(){

	var IdDocumento   = id("idDocTPV").value;
	var NroDocumentoElemento = id("NumeroDocumento");
	var SerieElemento = id("SerieNDocumento");
	var textdoc       = "Ticket";
	//alert('ValidarNroDoc: '+IdDocumento+' '+NroDocumentoElemento.value);
	// Ticket : 0
	// Boleta : 1
	// Factura : 2
	// Albaran : 4
	// Proforma : 5
	switch( IdDocumento )
	{
	case '1': textdoc = "Boleta"; break;
	case '2': textdoc = "Factura"; break;
	case '4': textdoc = "Albaran"; break;
	case '5': textdoc = "Proforma"; break;
	default: return;
	}		    

	// VALIDAMOS TEXTO
	if(NroDocumentoElemento){

	    var NroDocumento= new String(NroDocumentoElemento.value);
	    var Serie= new String(SerieElemento.value);

	    if(isNaN(NroDocumento) || parseInt(NroDocumento) < 1 || NroDocumento=="" || NroDocumento.lastIndexOf(' ')>-1){
		//lanza mensaje
		
 		alert("gPOS: \n  - El Nro de "+textdoc+" "+NroDocumento+" es incorrecto.");
		CargarNroDocumentoVenta(IdDocumento,0);
		NroDocumentoElemento.focus();
		return false;
		
	    }else{

		NroDocumento = parseInt(NroDocumento,10);
		if(NroDocumento <= 0){
		    //lanza mensaje
		    alert("gPOS: \n  - El Nro de "+textdoc+" "+NroDocumento+" incorrecto.");
		    CargarNroDocumentoVenta(IdDocumento,0);
		    NroDocumentoElemento.focus();
		    return false;
		}
		
		//VALIDAMOS NRO COMPROBANTE 
		if( IdDocumento == 1 || IdDocumento == 2 || IdDocumento == 4 || IdDocumento == 5){

		    //COMPROBANTES
		    var url = 
			"services.php?"+
			"modo=validarNumeroComprobante&"+
			"textDoc="+textdoc+"&"+
			"Serie="+Serie+"&"+
			"nroComprobante="+NroDocumento; 

		    //PEDIDOS
		    if( IdDocumento == 5 ) 
			url = 
			"services.php?"+
			"modo=validarNumeroPresupuesto&"+
			"textDoc="+textdoc+"&"+
			"Serie="+Serie+"&"+
			"nroPresupuesto="+NroDocumento; 
		    
		    var xrequest = new XMLHttpRequest();
		    xrequest.open("GET",url,false);
		    xrequest.send(null);

		    var resultado = xrequest.responseText;
		    if(resultado){
			alert("gPOS:\n "+
			      "    - El Nro de "+textdoc+" "+NroDocumento+
			      " esta registrado en el sistema.\n"+
			      "    - Ingrese otro número");

			CargarNroDocumentoVenta(IdDocumento,0);
			NroDocumentoElemento.focus();
			return false;
		    }
		    if(!resultado){
			return true;
		    }
		}
	    }
	}
    }


    function setdefaulttipocomprobante(tcliente,comprobante){

	setTextDocumento('',0);
	id("radioboleta").setAttribute("selected", "false");//**** true
	id("radiofactura").setAttribute("selected", "false");
	id("radioproforma").setAttribute("selected", "false");
	id("radioalbaran").setAttribute("selected", "false");
	id("radioticket").setAttribute("selected", "true");//**** true
	alert('gPOS: '+comprobante+': \n - Selecione un cliente diferente al - '+tcliente+' - ');
	MostrarUsuariosForm();
    }

    function validaSerie(){
	var documento = id("idDocTPV");
	if(documento)
	    validaSerieDocumento(documento.value);
    }

    function validaSerieDocumento(idDocumento){
	var SerieElemento = id("SerieNDocumento");
	if(!SerieElemento) return;
	var Serie= new String(SerieElemento.value);
	if(isNaN(Serie) || Serie=="" || parseInt(Serie) < 1 || Serie.lastIndexOf(' ')>-1){
	    
	    alert("gPOS:\n - Serie - "+Serie+" - incorrecto.");
	    CargarNroDocumentoVenta(idDocumento,0);
	    
	} else { 
	    
	    //Proforma
	    //if(idDocumento == '5')
	    //return CargarNroDocumentoVenta(idDocumento,0);
	    
	    //Comprobantes
	    //Evalua si existe la serie ************
	    if(existeSerieDocumento(Serie,idDocumento)=='1')
		CargarNroDocumentoVenta(idDocumento,Serie);
	    else
		altaSerieDocumento(Serie,idDocumento);
	    
	}
    }

    function altaSerieDocumento(Serie,idDocumento){

	if(confirm("gPOS: Serie no registrada. "+
		   "\n\n     - Aceptar para crear nueva Serie.")){

	    if(idDocumento == '5')
		return CargarInicioSeriePreVenta(Serie); //Dejamos libre la serie de PreVenta

	    if(registraSerieDocumento(Serie,idDocumento)!='1'){

		alert("gPOS:\n - Error al  registrar la Serie - "+Serie+" - ");
		Serie = 0;//si sale algo mal

	    }
	    return CargarNroDocumentoVenta(idDocumento,Serie);	
	}
	CargarNroDocumentoVenta(idDocumento,0);	
    }

    function registraSerieDocumento(Serie,idDocumento){

	var url =
	    "services.php?"+"&"+
	    "modo=registraSerieDocumentoVenta"+"&"+
	    "idDocumento="+idDocumento+"&"+
	    "Serie="+Serie;

	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	//alert(xrequest.responseText);
	return xrequest.responseText;
    }

    function existeSerieDocumento(Serie,idDocumento){
	var url =
	    "services.php?"+"&"+
	    "modo=existeSerieDocumentoVenta"+"&"+
	    "idDocumento="+idDocumento+"&"+
	    "Serie="+Serie;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	return xrequest.responseText;
    }

    function CargarNroDocumentoVenta(idDocumento,Serie){
	var url =
	    "services.php?"+"&"+
	    "modo=cargaNroDocumentoVenta"+"&"+
	    "idDocumento="+idDocumento+"&"+
	    "Serie="+Serie;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	
	if(!xrequest.responseText)
	    return esActivoServer = false;//Servidor Activo?
	
	//Cargamos la serie y el numero
	var a_cod = xrequest.responseText.split('-');
	id("SerieNDocumento").value=a_cod[0];
	id("NumeroDocumento").value=a_cod[1];
    }

    function CargarInicioSeriePreVenta(Serie){
	id("SerieNDocumento").value=Serie;
	id("NumeroDocumento").value=1;
    }

    function AjustarEtiquetaPedido(){
	var radioboleta = id("radioboleta");
	var radiofactura = id("radiofactura");
	var radioalbaran = id("radioalbaran");
	var radioproforma = id("radioproforma");
	var radioticket = id("radioticket");
	var elemento = id("gruponb");

	//Default TEXT 
	elemento.setAttribute("collapsed", false);

	//Valores default pedido
	radioboleta.setAttribute("disabled", true);
	radiofactura.setAttribute("disabled", true);
	radioalbaran.setAttribute("disabled", true);
	radioticket.setAttribute("disabled", true);
	radioboleta.setAttribute("selected", false);
	radiofactura.setAttribute("selected", false);
	radioproforma.setAttribute("disabled", false);
	radioalbaran.setAttribute("selected", false);
	radioticket.setAttribute("selected", false);
	setTextDocumento('Proforma','5');
	radioproforma.setAttribute("selected", true);
	CargarNroDocumentoVenta('5',0);
    }

    function AjustarEtiquetaDefault(){

	id("radioboleta").setAttribute("disabled", false);
	id("radiofactura").setAttribute("disabled", false);
	id("radioalbaran").setAttribute("disabled", false);
	id("radioproforma").setAttribute("disabled", false);
	id("radioticket").setAttribute("disabled", false);
	tipocomprobante(0);
    }



    /* Ajusta signos en la vista del ticket */

    function TicketAjusta(){
        var codigo,unidades;
        var agnadidos = new Array();	

        for (var t=0;t<iticket;t++) {
            codigo = ticketlist[t];
            if ( !agnadidos[codigo] && id( "tic_" + codigo )  ) {	
                //txt += " " + id("tic_"+codigo)
                unidades = ticket[codigo].unidades;//id("tic_unid_" + codigo).value;	
                //unidades = ConvertirSignoApropiado( unidades );
		ticket[codigo].unidades = ConvertirSignoApropiado( unidades );
                id("tic_unid_" + codigo).value = ticket[codigo].unidades;

                agnadidos[codigo] = true;
            }		
        }		
    }

    /*++++++++ MULTIPAGOS +++++++++++*/	

    var modoMultipago = false;


    function LimpiarMultipagos(){
        id("peticionEfectivo").value = 0;
        id("peticionBono").value = 0;
        id("peticionTarjeta").value = 0;
        id("peticionTransferencia").value = 0;		

        modoMultipago = true;//forzamos modo activado
        ModoMultipago();//Cambiamos a apagado		
    }

    function ModoMultipago(){
        if(modoMultipago) {				
            id("Pagos_1").setAttribute("collapsed","true");
            id("Pagos_2").setAttribute("collapsed","true");
            id("Pagos_3").setAttribute("collapsed","true");
            //id("Pago_4").setAttribute("collapsed",false);	
            id("Pago_Modo").setAttribute("collapsed","false");	
            //id("peticionEntrega").setAttribute("readonly","true");
            //id("peticionEntrega").setAttribute("style","background-color: white!important");				
            id("Fila-peticionEntrega").setAttribute("collapsed","false");		
            modoMultipago  = false;
        } else {
            id("Pagos_1").setAttribute("collapsed","false");
            id("Pagos_2").setAttribute("collapsed","false");
            id("Pagos_3").setAttribute("collapsed","false");
            //id("Pago_4").setAttribute("collapsed",false);	
            id("Pago_Modo").setAttribute("collapsed","true");	
            //id("peticionEntrega").setAttribute("readonly","false");
            //id("peticionEntrega").setAttribute("style","background-color: -moz-dialog!important");		
            id("Fila-peticionEntrega").setAttribute("collapsed","true");				
            modoMultipago  = true;
        }
        ActualizaPeticion();
    }

    var modoPersonalizado=0;

    function ModoPersonalizado(){
        if(modoPersonalizado) {				
            //id("Admintic_0").setAttribute("collapsed","false");
            id("Admintic_1").setAttribute("collapsed","true");
            id("Admintic_2").setAttribute("collapsed","true");	
            id("Admintic_3").setAttribute("collapsed","true");
            modoPersonalizado  = false;
        } else {
            //id("Admintic_0").setAttribute("collapsed","true");
            id("Admintic_1").setAttribute("collapsed","false");
            id("Admintic_2").setAttribute("collapsed","false");			
            id("Admintic_3").setAttribute("collapsed","false");
            modoPersonalizado  = true;
        }
        ActualizaPeticion(); 
    }
 
    /*+++++++++++ Promocion ++++++++++++++*/
    function cargarPromocion(){
	/**
	   PromocionSeleccionado //Id Promocion
	   promocionesval  //Promociones Accedidas 
	   promocioneslist //Promociones Lista
	   
	   promociones[idpromocion].promocliente,.promocion,.modalidad,.prioridad,
                                   .monto,.tipo,.IdLocal,.cb0,.cb1,.descuento,.bono;     

 	- Modalidades : 'MontoCompra','HistorialCompra'
	- Tipo        : 'Descuento' ( descuento > % porcentaje aplicado al total ticket)
                        'Producto'  ( cb0, cb1 > un producto ó un alternativo dependiendo del stock)
                        'Bono'      ( bono > monto para proxima compra);
        - Disponible  : (IdLocal > 0=:todos 0<: local) 
	- Condiciones : 'HistorialCompra'
			   * Categoria Cliente  (promocliente)
			   * Preferencia        (prioridad > 0:Peguntar,1:Cargar,2:Ignorar)
			'MontoCompra'
			   * Monto Mayor Ticket (monto)
        **/

	if(ModoDeTicket == "pedidos") return;

	promocionesval      = new Array(); 
	promocionesmontoval = new Array(); 

	for(var j=0;j<promocioneslist.length;j++)
	{
            var xid         = promocioneslist[j];
	    var esPromocion = false;

	    switch(  promociones[xid].modalidad )
	    {
	    case 'MontoCompra':

		var xmonto = parseFloat( promociones[xid].monto );
		var xtotal = parseFloat( ticketTotalImporte );

		esPromocion = ( xtotal >= xmonto )? true:false;

		if(esPromocion) promocionesmontoval.push(xid);
		break;

	    case 'HistorialCompra':

		if ( UsuarioSeleccionado == 1 ) break;

		var ausuarioselpromo = getIdPromocion( usuarios[UsuarioSeleccionado].promo );
		var xusuariopromo    = parseInt( promociones[xid].promocliente );
		var xusuarioselpromo = 0;
		
		for( var g=0; g < ausuarioselpromo.length; g++)
		{
		    xusuarioselpromo = parseInt( ausuarioselpromo[g] );
		    if ( xusuarioselpromo == xusuariopromo ) esPromocion = true;    
		}
		break;
	    }
	    //Guarda...
	    if(esPromocion) promocionesval.push(xid);
	}

	//Limpia...
	validaPromocionesMonto();

	//Lanza...
	lanzarPromocion();
    }

    function lanzarPromocion(){

	if( !(promocionesval.length>0) ) return;//No hay promociones
	if( PromocionSeleccionado != 0 ) return s_lanzarPromocion();//Cargar promo...

	//txt...
	PromocionSeleccionado    = 0;

	//Pregunta
	if (xPromocionSeleccionado) 
	    return g_lanzarPromocion();

	var txtPromocionSeleccionado = txt_lanzaPromocion(false);

	//Una...
	if ( promocionesval.length == 1 ){

            if( !confirm( txtPromocionSeleccionado+"\n\n"+"     ¿Cargar promoción?") )
		return g_lanzarPromocion();

	    PromocionSeleccionado    = promocionesval[0];
	}
	//Varias...
	if( PromocionSeleccionado == 0 )
	{
	    //Elige...
	    var p = prompt( txtPromocionSeleccionado+"\n\n"+"     Elige el # de la promoción:",'');
	    //Cancela;
	    if( p == null) 
		return g_lanzarPromocion();

	    //Valida elegida
	    p = parseInt(p);
	    if( isNaN(p) || p=="" ||  p<=0 )
		return g_lanzarPromocion();

	    //Existe?
	    PromocionSeleccionado    =  ( promociones[p] )? p : 0;
	}
	//Lanza promo...
	if( PromocionSeleccionado != 0 ) s_lanzarPromocion();
    }

    function txt_lanzaPromocion( xsel ){

	var p_mm='',xpri='',xproducto='',xid='',xmodo='',xoferta='',xsrt='',esMonto=false,xcatego='',head_mm='',xcliente='',xtclient='',xvalida=0,xaccion='';

	//txt
	xaccion  = (xsel)? 'ón aplicada':'ones disponibles';
	xtclient = ( UsuarioSeleccionado != 1)? "Cliente: ":"";
	xvalida  = ( xsel )? 1:promocionesval.length;
	xsrt     = "     ";
	xcliente = "\n\n" + xsrt + xtclient + usuarios[UsuarioSeleccionado].nombre;

	for( var g=0; g < xvalida; g++)
	{
	    //id...
	    xid = ( xsel )? PromocionSeleccionado:promocionesval[g];
	    //cadenas...

	    txtpromo = usuarios[UsuarioSeleccionado].promo;
	    xpri     = ' Ninguna';
	    xpri     = ( promociones[xid].prioridad == 1 )? ' Baja' : xpri;
	    xpri     = ( promociones[xid].prioridad == 2 )? ' Media': xpri;
	    xpri     = ( promociones[xid].prioridad == 3 )? ' Alta' : xpri;
	    esMonto  = ( promociones[xid].modalidad == 'MontoCompra' )? true:false;

	    xmodo    = ( esMonto )? 'Importe Ticket Mayor a ' : 'Categoria Cliente ';
	    xcatego  = (!esMonto )? gettxtPromocion2id( txtpromo, promociones[xid].promocliente ):'';
	    xmododet = ( esMonto )? cMoneda[1]['S'] +" "+ promociones[xid].monto:xcatego;

	    switch( promociones[ xid ].tipo ){
	    case 'Descuento': xoferta = promociones[ xid ].descuento+'%';break;
	    case 'Bono'     : xoferta = cMoneda[1]['S']+" "+ promociones[ xid ].bono;break;
	    case 'Producto' :

		var xcb0     = false;
		var xcb1     = false;
		var xcb0Ext  = ( productos[ promociones[ xid ].cb0 ] )? promociones[ xid ].cb0:false;
		var xcb1Ext  = ( productos[ promociones[ xid ].cb1 ] )? promociones[ xid ].cb1:false;
		if( xcb0Ext ) xcb0 = ( productos[ xcb0Ext ].unidades > 0 )? xcb0Ext:false;
		if( xcb1Ext ) xcb1 = ( productos[ xcb1Ext ].unidades > 0 )? xcb1Ext:false;

		xoferta = ( !xcb0 && !xcb1 )? 'Producto en Oferta  - No Disponible - ':'';
		if ( !xcb0 && !xcb1 ) break;

		xoferta = ( xcb0             )? productos[ xcb0 ].producto:false;
		xoferta = ( !xoferta && xcb1 )? productos[ xcb1 ].producto:xoferta;
		xoferta += ' ( 1 und )'; 
		break;
	    }

	    //promocion...
	    p_mm += "      # "+xid+'     '+promociones[xid].promocion+"\n"+
  		"     --------------------------------------------"+
                "--------------------------------";
 	    p_mm += '\n       Modalidad : '+xmodo+' '+xmododet;
 	    p_mm += '\n       Tipo            : '+promociones[xid].tipo;
 	    p_mm += '\n       Oferta         : '+xoferta;
 	    p_mm += '\n       Prioridad    :'+xpri;
	    p_mm += ( promocionesval.length == g+1 )? '':'\n\n';
	}

	head_mm  = 
	    "gPOS: Promoci"+xaccion+" para el Ticket Actual "+xsrt+xsrt+xsrt+xsrt+xsrt+xsrt+xsrt+xsrt+xsrt+xsrt+xcliente+"\n"+p_mm;
	
	return head_mm;
    }

    function validaPromocionesMonto(){

	if( promocionesval.length == 1) return;

	for( var g=0; g < promocionesmontoval.length; g++){

	    //Ultimo..
	    if(promocionesmontoval.length-1 == g) break;
	    
	    idx = promocionesval.indexOf( promocionesmontoval[g] ); 
	    if(idx!=-1) promocionesval.splice(idx, 1);
	}

	if( promocionesval.length == 0 ){
	    id("ticketPromocionSeleccionado").setAttribute('collapsed',true);   
	    id("ticketPromocionSeleccionado").setAttribute('label','');
	}
    }

    function s_lanzarPromocion(){
	switch( promociones[ PromocionSeleccionado ].tipo )
	{
	case 'Descuento':cargarDescuentoPromocion();break;
	case 'Producto':cargarProductoPromocion();break;
	case 'Bono':cargarBonoPromocion();break;
	}

	if( PromocionSeleccionado == 0 ) return;

	id("ticketPromocionSeleccionado").setAttribute('collapsed',false);   
	if ( promociones[ PromocionSeleccionado ].tipo != 'Bono')
	    id("ticketPromocionSeleccionado").setAttribute('label','');
    }

    function g_lanzarPromocion(){

	var xpsrt = ( promocionesval.length > 1)? ' Pendientes':' Pendiente';
	xPromocionSeleccionado = true;

	id("ticketPromocionSeleccionado").setAttribute('collapsed',false);   
	id("ticketPromocionSeleccionado").setAttribute('label',promocionesval.length+xpsrt);
    }

    function n_lanzarPromocion(){

	xPromocionSeleccionado = true;
	PromocionSeleccionado  = 0;

	id("ticketPromocionSeleccionado").setAttribute('collapsed',true);   
	id("ticketPromocionSeleccionado").setAttribute('label','');
	alert("gPOS: PROMOCIONES \n\n           Producto en Oferta  - No Disponible - ")
    }

    function mostrarTicketPromocion(){

	if ( promocionesval.length == 0) return;

	//Pregunta
	if ( xPromocionSeleccionado )
	{
	    xPromocionSeleccionado = false; 
	    return cargarPromocion();
	}

	var txtPromocionSeleccionado = txt_lanzaPromocion(true);
	//promocion...
	alert( txtPromocionSeleccionado );
    }

    function cargarDescuentoPromocion(){
	
	var codigo;
	var dscto = parseFloat( promociones[ PromocionSeleccionado ].descuento );

	for (var h=0; h < ticketlist.length; h++) {

            codigo = ticketlist[h];	

            var ticdscto   = id("tic_descuento_"+ codigo);
            var ticprecio  = parseFloat( ticket[codigo].precio );
            var cantidad   = parseFloat( ticket[codigo].unidades );

	    ticket[codigo].descuento = parseMoney(dscto);
            ticdscto.setAttribute("value",FormateComoDescuento(ticket[codigo].descuento));	 
            Blink("tic_descuento_" + codigo, "label-descuento" );

	    lPromocionSeleccionado = true;
            RecalculoTotal();
	}
    }

    function cargarProductoPromocion(){

	var xcb0     = false;
	var xcb1     = false;
	var cbDispo  = false;	
	var xdscto   = 100;
	var xcant    = 1;
	var xid      = PromocionSeleccionado;
	var xcb0Ext  = ( productos[ promociones[ xid ].cb0 ] )? promociones[ xid ].cb0:false;
	var xcb1Ext  = ( productos[ promociones[ xid ].cb1 ] )? promociones[ xid ].cb1:false;
	
	if( xcb0Ext ) xcb0 = ( productos[ xcb0Ext ].unidades > 0 )? xcb0Ext:false;
	if( xcb1Ext ) xcb1 = ( productos[ xcb1Ext ].unidades > 0 )? xcb1Ext:false;

	if( !xcb0 && !xcb1 ) return n_lanzarPromocion();


	cbDispo = ( xcb0             )? xcb0:cbDispo;
	cbDispo = ( !cbDispo && xcb1 )? xcb1:cbDispo;

	if ( !cbDispo ) return;

	if ( ticket[ cbDispo ] ){
	    xcant  = parseInt( ticket[cbDispo].unidades );//id("tic_unid_"+cbDispo).value
	    xdscto = Math.round( (xdscto/xcant)*100 )/100;
	}

	//producto...
	lPromocionSeleccionado = true;

	if ( !ticket[ cbDispo ] ) 
	    tpv.AddCarrito( cbDispo ,ConvertirSignoApropiado(1) );
	
        id("tic_descuento_"+ cbDispo).value = FormateComoDescuento(xdscto);	 
        Blink("tic_descuento_" + cbDispo, "label-descuento" );
	
	//descuento...
	lPromocionSeleccionado = true;
        RecalculoTotal();
    }

    function cargarBonoPromocion(){

	if(UsuarioSeleccionado == 1){
	    id("ticketPromocionSeleccionado").setAttribute('collapsed',true);   
	    id("ticketPromocionSeleccionado").setAttribute('label','');
	    PromocionSeleccionado = 0;
	    return alert("gPOS: PROMOCIONES \n\n    Bono asociado a - Cliente Contado - no permitido")
	}
	
	id("ticketPromocionSeleccionado").setAttribute('label','Bono '+cMoneda[1]['S']+" "+formatDinero( promociones[ PromocionSeleccionado ].bono ));
    }

    function checkPreTicket(){

	if( ModoDeTicket == "pedidos" ) return false;

	var esPedidodet  = false;
	var esQuitar     = false;
	var esUnidades   = false;
	var xcodigo      = 0;
	var mm           = '';
	var mm_k         = '';
	var mm_s         = '';
	var xfila;

	for (var t=0;t<iticket;t++) {

	    xcodigo  = ticketlist[t];
	    xfila    = id("tic_" + xcodigo);

	    if(!xfila) continue;

	    esPedidodet = ( ticket[xcodigo].pedidodet == '')? 1:0;//id("tic_pedidodet_"+xcodigo).value
	    esUnidades  = ( ticket[xcodigo].unidades > 0 )? 0:1;//id("tic_unid_"+xcodigo).value
	    esQuitar    = ( esPedidodet == 0 && esUnidades == 0  )? false:true;

            if (!esQuitar) continue;
	    
            xfila.parentNode.removeChild(xfila);
            ticket[xcodigo] = null;
            ticketlist.splice(t,1);
	    iticket--;
	    mm_k  = ( esPedidodet )?  '\n  :::  ***RESUMEN KARDEX*** ' :'';
	    mm_s  = ( esUnidades  )?  '\n  :::  ***STOCK 0*** ':'';
	    mm   += "\n     - "+productos[xcodigo].producto+mm_k+mm_s; 
	    
	}

	if( mm=='' ) return false;
	
	alert('gPOS: TICKET ACTUAL \n\n  Productos con registros no validos:\n'+mm);
 	return true;
    }

/*+++++++++++++++++++++++++++++ TICKETS  ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ SERVICIOS  ++++++++++++++++++++++++++++++++++*/
   
    tpv.AddServicio = function ( codigobarras, codigo, nombre, referencia, precio,
				 impuesto, idsubsidiario ) {
        var talla      = "";
	var color      = "";
	var marca      = "";
        var servicio   = Nombre2Lex(nombre);
	var idproducto = productos[codigobarras].idproducto;

        //Creamos producto imaginario 
        if (!pool.Existe(codigo))
	    tA(idproducto,codigo,servicio,"",referencia,precio*100,precio*100,precio*100,precio*100,
	       impuesto,talla,color,0,0,0,0,idsubsidiario,nombre,"","","","",0,0,marca,0,0,0,
	       "unid","","",0,0,0,0,0,0,0,"" )

        pool.select(codigo); 	
        arreglotex = pool.get().nombre;
        referencia = pool.get().referencia;
        precio	   = pool.get().pvd;
        impuesto   = pool.get().impuesto;			
        descuento  = pool.get().descuento;		
        nombre2	   = pool.get().nombre2;	

        this.Compra( codigo,arreglotex,referencia,precio,impuesto,1, 
		     talla,color,descuento,idsubsidiario,nombre2);
    }


    tpv.getDatosFromCB	= function( cb ){
        var res = new Array();	
        if (!pool.Existe( cb ) ){
            return	res;
        }
	
        res["nombre"]	= pool.get().nombre;
        res["talla"]	= pool.get().talla;
        res["color"]	= pool.get().color;
        return res;
    }


        /*++++++++++++ ARREGLOS LISTA ++++++++++++++*/	

        function VerServicios(){

            id("panelDerecho").setAttribute("collapsed",true);
	    id("boxServicios").setAttribute("collapsed",false);	    
            var estado = 	document.getElementById("modoVisual").selectedIndex;	
            estado = (estado == 8)?0:8;

            id("modoVisual").setAttribute("selectedIndex",estado);	
	    setTimeout('BuscarOrdenServicio()',400);
	    setTimeout('checkServicios()',1000);
	    resizelistboxticket(false);
        }

        var indiceListadoSubsidiario = 0;

        //recibe YYYY-MM-DD, genera DD-MM-YYYY
        function toFormatoFecha(fecha){
            if (fecha== "0000-00-00" || !fecha){
                return "00-00-0000";			
            }
            if (fecha=="hoy"){
                var hoy = new Date();	
                return (hoy.getDate())+"-"+(hoy.getMonth()+1)+"-"+(hoy.getYear()+1900);
            }

            datosfecha = fecha.split("-");

            return datosfecha[2] + "-" + datosfecha[1] + "-"+datosfecha[0];  		
        }

        function ListadoSubsidiarios(){

            var idsubsidiario = id("SubsidiarioListaServicios").value;
            var statusServicio = id("StatusListaServicios").value;
            var ticket = id("TicketListaServicios").value;
	    var desde  = id("FechaBuscaServiciosTerceros").value;
	    var hasta  = id("FechaBuscaServiciosTercerosHasta").value;

            var url = "services.php?modo=mostrarServicios"
                + "&idsubsidiario=" + escape(idsubsidiario) 
                + "&status=" + escape(statusServicio) 
                + "&ticket=" + escape(ticket)
	        + "&desde=" + escape(desde)
	        + "&hasta=" + escape(hasta);

            var obj = new XMLHttpRequest();

            obj.open("POST",url,false);//POST en lugar de GET porque puede haber cambio de estado
            obj.send(null);

            var tex = "";
            var cr = "\n";

            var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante;
	    var estadopago,registro,observaciones,importe;
            var node,t,i;

            if (!obj.responseXML)
                return alert( c_gpos + po_error);		
            if (!obj.responseXML.documentElement)
                return alert( c_gpos + po_error);

            var xml = obj.responseXML.documentElement;

            VaciarListadoServicios();

            for (i=0; i<xml.childNodes.length; i++) {
                node = xml.childNodes[i];
                if (node){
                    t = 0;
                    subsidiario 	= node.getAttribute("NombreSubsidiario");
                    producto 		= node.getAttribute("DescripcionProducto");
                    arreglos 		= node.getAttribute("Servicios");
                    nticket		= node.getAttribute("NTicket");
                    statuscosica	= node.getAttribute("Status");
                    enviado		= node.getAttribute("FechaEnvio");
                    recibido		= node.getAttribute("FechaRecepcion");	
                    ident		= node.getAttribute("IdTbjoSubsidiario");	
                    registro		= node.getAttribute("FechaRegistro");
		    entregado           = node.getAttribute("FechaEntrega");
		    importe             = node.getAttribute("Coste");
		    importependiente    = node.getAttribute("CostePendiente");
		    observaciones       = node.getAttribute("Observaciones");
		    DocSubsidiario      = node.getAttribute("DocSubsidiario");
		    NDocSubsidiario      = node.getAttribute("NDocSubsidiario");

                    CrearFilaDeSubsidiariosListado(subsidiario,producto,arreglos,nticket,
						   statuscosica,enviado,recibido,ident,
						   registro,entregado,observaciones,importe,
						   importependiente,DocSubsidiario,
						   NDocSubsidiario);
                }					
            }

        }

        function CrearFilaDeSubsidiariosListado(subsidiario,producto,arreglos,nticket,
						statuscosica,enviado,recibido,ident,
						registro,entregado,observaciones,importe,
						importependiente,DocSubsidiario,
						NDocSubsidiario) {

            var xlistadoProductos = id("busquedaListaServicios");

            if (!xlistadoProductos)
                return alert( c_gpos + "Error de proceso, recargue la TPV");

	    var aenviado = (enviado != ' ')? enviado.split("~"):'';
	    var venviado = (enviado != ' ')? aenviado[0]:'';
	    var denviado = (enviado != ' ')? aenviado[1]:'';

	    var arecibido = (recibido != ' ')? recibido.split("~"):'';
	    var vrecibido = (recibido != ' ')? arecibido[0]:'';
	    var drecibido = (recibido != ' ')? arecibido[1]:'';

	    var aentregado = (entregado != ' ')? entregado.split("~"):'';
	    var ventregado = (entregado != ' ')? aentregado[0]:'';
	    var dentregado = (entregado != ' ')? aentregado[1]:'';

            var xsubsidiario = document.createElement("label");
            xsubsidiario.setAttribute("value",subsidiario);
	    xsubsidiario.setAttribute("id","arreglo_subsidiario_" + ident );

            var xproducto = document.createElement("label");
            xproducto.setAttribute("value",producto);
	    xproducto.setAttribute("id","arreglo_producto_" + ident );	

            var xarreglos = document.createElement("label");
            xarreglos.setAttribute("value",arreglos);	
            xarreglos.setAttribute("crop","end");
	    xarreglos.setAttribute("id","arreglo_servicio_" + ident );

            var xnticket = document.createElement("label");
            xnticket.setAttribute("value",nticket);
	    xnticket.setAttribute("id","arreglo_codigo_" + ident );

            var xstatuscosica = document.createElement("label");
            xstatuscosica.setAttribute("value",statuscosica);
            xstatuscosica.setAttribute("style","font-weight:bold; ");	
            xstatuscosica.setAttribute("id","arreglo_status_" + ident );			

            var xenviado = document.createElement("label");
            xenviado.setAttribute("value",venviado);	
            xenviado.setAttribute("label",denviado);	
            xenviado.setAttribute("id","arreglo_enviado_" + ident );				

            var xrecibido = document.createElement("label");
            xrecibido.setAttribute("value",vrecibido);
	    xrecibido.setAttribute("label",drecibido);
            xrecibido.setAttribute("id","arreglo_recibido_" + ident );

            var xentregado = document.createElement("label");
            xentregado.setAttribute("value",ventregado);
	    xentregado.setAttribute("label",dentregado);
	    xentregado.setAttribute("collapsed","true");
            xentregado.setAttribute("id","arreglo_entregado_" + ident );

            var xregistro = document.createElement("label");
            xregistro.setAttribute("value",registro);
            xregistro.setAttribute("id","arreglo_registro_" + ident );

            var ximporte = document.createElement("label");
            ximporte.setAttribute("value",formatDinero(importe,2));
            ximporte.setAttribute("style","text-align:right;font-weight:bold; ");
            ximporte.setAttribute("id","arreglo_importe_" + ident );

            var ximportependiente = document.createElement("label");
            ximportependiente.setAttribute("value",formatDinero(importependiente,2));
            ximportependiente.setAttribute("style","text-align:right;font-weight:bold; ");
            ximportependiente.setAttribute("id","arreglo_importependiente_" + ident );

            var xobservaciones = document.createElement("label");
            xobservaciones.setAttribute("value",observaciones);
	    xobservaciones.setAttribute("collapsed","true");
            xobservaciones.setAttribute("id","arreglo_observaciones_" + ident );

            var xdocsubsidiario = document.createElement("label");
            xdocsubsidiario.setAttribute("value",DocSubsidiario);
            xdocsubsidiario.setAttribute("label",NDocSubsidiario);
	    xdocsubsidiario.setAttribute("collapsed","true");
            xdocsubsidiario.setAttribute("id","arreglo_docsubsidiario_" + ident );

            var xlistitem = document.createElement("listitem");		
            xlistitem.setAttribute("id","listadomodarreglos_" + indiceListadoSubsidiario );

            indiceListadoSubsidiario++;	
            xlistitem.setAttribute("value",ident);		

            xlistitem.appendChild( xsubsidiario );
            xlistitem.appendChild( xproducto );
            xlistitem.appendChild( xarreglos );
            xlistitem.appendChild( xnticket );	
            xlistitem.appendChild( xstatuscosica );
	    xlistitem.appendChild( xregistro );	
            xlistitem.appendChild( xenviado );
            xlistitem.appendChild( xrecibido );	
            xlistitem.appendChild( ximporte );	
            xlistitem.appendChild( ximportependiente );	
            xlistitem.appendChild( xentregado );	
            xlistitem.appendChild( xobservaciones );	
            xlistitem.appendChild( xdocsubsidiario );	

            xlistadoProductos.appendChild( xlistitem );
        }

        function VaciarListadoServicios(){
            //alert("gPOS: vaciar: " + indiceListadoSubsidiario);

            var lista = id("busquedaListaServicios");

            for (var i = 0; i < indiceListadoSubsidiario; i++) { 
                kid = id("listadomodarreglos_"+i);					
                if (kid)	lista.removeChild( kid ); 
            }
            indiceListadoSubsidiario = 0;
        }

        //pregunta por el dia de hoy
        function hoyPrompt(mensaje){
            return prompt("Fecha?",toFormatoFecha("hoy"));
        }


        function ListadoServiciosSeleccionadoStatus(nuevoestado){
            //
            var xlista = id("busquedaListaServicios");

            if (!xlista) return alert( c_gpos + "e:0");
            if (!xlista.selectedItem) return;

            var ident = xlista.selectedItem.value;

            var xstatus = id("arreglo_status_"+ident);
            if (!xstatus) return alert( c_gpos + "error interno, reintente");

            xstatus.setAttribute("value",nuevoestado);

            var xenvia = id("arreglo_enviado_"+ident);
            var xrecibe = id("arreglo_recibido_"+ident);	

            var diacero = toFormatoFecha(0);
            var hoy		= toFormatoFecha("hoy");
            var newfecha ;

            switch(nuevoestado){
                case "Enviado"://fechaenvio hoy,. recepcion=0				
                    hoy = hoyPrompt();
                xenvia.setAttribute("value",hoy);		
                xrecibe.setAttribute("value",diacero);			
                break;
                case "Recibido":
                    case "Recogido"://Recepcion hoy		
                    hoy = hoyPrompt();
                xrecibe.setAttribute("value",hoy);
                break;
                case "Pdte Envio"://fechas a cero 
                    xrecibe.setAttribute("value",diacero);
                xenvia.setAttribute("value",diacero)
                    break;	
                default:
                //	prompt(nuevoestado);
                break;
            }

            var url = "services.php?modo=setStatusTrabajoSubsidiario"
                + "&idtrabajo=" + escape(ident) 
                + "&status=" + escape(nuevoestado) 

                var obj = new XMLHttpRequest();

            obj.open("GET",url,true); obj.send(null);					
        }

        function RevizarServicioSeleccionado(){
	    var idex = id("busquedaListaServicios").selectedItem;
	    
	    if(!idex) return;
	    
	    cIdTbjoSubsidiario = idex.value;
	    cSubsidiario       = id("arreglo_subsidiario_"+idex.value).getAttribute('value');
	    cProducto          = id("arreglo_producto_"+idex.value).getAttribute('value');
	    cServicio          = id("arreglo_servicio_"+idex.value).getAttribute('value');
	    cCodigo            = id("arreglo_codigo_"+idex.value).getAttribute('value');
	    cEntregado         = id("arreglo_entregado_"+idex.value).getAttribute('label');
	    cEstado            = id("arreglo_status_"+idex.value).getAttribute('value');
	    cImporte           = id("arreglo_importe_"+idex.value).getAttribute('value');
	    cEnviado           = id("arreglo_enviado_"+idex.value).getAttribute('label');
	    cRecibido          = id("arreglo_recibido_"+idex.value).getAttribute('label');
	    cPendiente         = id("arreglo_importependiente_"+idex.value).getAttribute('value');
	    cObservacion       = id("arreglo_observaciones_"+idex.value).getAttribute('value');
	    cCodigoDoc         = id("arreglo_docsubsidiario_"+idex.value).getAttribute('label');
	    cDocSubsidiario    = id("arreglo_docsubsidiario_"+idex.value).getAttribute('value');

	    id("vboxFormServicios").setAttribute('collapsed',false);

	    var docServicio = obtenerDocServicio();

	    cExisteMovServicio = (docServicio == '')? false:true;

	    gDocServicioSub = (docServicio !='')? docServicio[0]:'Ticket';
	    gCodDocServicio = (docServicio !='')? docServicio[1]:'';

	    cargarFormularioServivio();
	    changeEstadoServicio();

	}

        function obtenerDocServicio(){
	    var	url =
		"services.php?"
		+"modo=ObtenerDocServicio&"
		+"idex="+cIdTbjoSubsidiario;
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    var resultado = xrequest.responseText;

	    var res = xrequest.responseText;

	    return res.split('~');

	}

        function cargarFormularioServivio(){

	    //LimpiarFormaCoste();

	    var aenviado = (cEnviado)? cEnviado.split(" "):'';
	    var fenviado = (cEnviado)? aenviado[0]:'';
	    var henviado = (cEnviado)? aenviado[1]:'';
	    
	    var arecibido = (cRecibido)? cRecibido.split(" "):'';
	    var frecibido = (cRecibido)? arecibido[0]:'';
	    var hrecibido = (cRecibido)? arecibido[1]:'';
	    
	    var aentregado = (cEntregado)? cEntregado.split(" "):'';
	    var fentregado = (cEntregado)? aentregado[0]:'';
	    var hentregado = (cEntregado)? aentregado[1]:'';

	    id("titleServicios").label = cProducto+' '+cServicio+' '+cSubsidiario;

	    id("tbox_subsidiario").value = cSubsidiario;
	    id("tbox_importe").value = cImporte;
	    id("StatusListaServicios1").value = cEstado;
	    if(cEnviado) id("date_enviado").value = fenviado;
	    if(cEnviado) id("time_enviado").value = henviado;
	    if(cRecibido) id("date_recibido").value = frecibido;
	    if(cRecibido) id("time_recibido").value = hrecibido;
	    if(cEntregado) id("date_entregado").value = fentregado;
	    if(cEntregado) id("time_entregado").value = hentregado;
	    id("tbox_pendiente").value = cPendiente;
	    id("tbox_observacion").value = cObservacion;
	    id("SubsidiarioDocumento").value = gDocServicioSub;
	    id("tbox_coddocumento").value = gCodDocServicio;
	}

        function changeEstadoServicio(){
	    var pdteenvio = true;
	    var enviado   = true;
	    var recibido  = true;
	    var entregado = true;
	    var f_enviado = true;
	    var f_recibido= true;
	    var f_entregado= true;
	    var xabono     = true;
	    var xdocsubsid = true;

	    var statusr = id("StatusListaServicios1").label;
	    cEstado = (statusr == 'Pdte Envio')? cEstado:statusr;

	    switch(cEstado){
	    case 'Pdte Envio':
		enviado = false;
		break
	    case 'Enviado':
		recibido  = false;
		f_enviado = false;
		xabono    = (cPendiente != 0)?false:true;
		break;
	    case 'Recibido':
		entregado  = false;
		f_recibido = false;
		xabono     = (cPendiente != 0)?false:true;
		xdocsubsid = false;
		break;
	    case 'Entregado':
		f_entregado = false;
		xabono      = (cPendiente != 0)?false:true;
		xdocsubsid  = false;
		break;
	    }

	    id("itm_pdte_envio").setAttribute('collapsed',pdteenvio);
	    id("itm_enviado").setAttribute('collapsed',enviado);
	    id("itm_recibido").setAttribute('collapsed',recibido);
	    id("itm_entregado").setAttribute('collapsed',entregado);
	    id("row_enviado").setAttribute('collapsed',f_enviado);
	    id("row_recibido").setAttribute('collapsed',f_recibido);
	    id("row_entregado").setAttribute('collapsed',f_entregado);
	    id("row_documento").setAttribute('collapsed',xdocsubsid);
	    id("row_pendiente").setAttribute('collapsed',xabono);
	    id("row_abonar").setAttribute('collapsed',xabono);
	}

        function ModificarServicio(xval){
	    var concepto = false;
	    switch(xval){
	    case 1:
		//Importe
		if(cEstado == 'Recibido' || cEstado == 'Entregado'){
		    alert("gPOS: \n\n         No puede modificar el Importe en estado "+cEstado);
		    RevizarServicioSeleccionado();
		    return;
		}
		
		var data = id("tbox_importe").value;
		var npte = 0;

		if(data == cPendiente){
		    data = data;
		    npte = data;
		}

		if(data > cImporte){
		    data = data;
		    npte = parseFloat(cPendiente) + (parseFloat(data) - parseFloat(cImporte));
		}

		if(data < cImporte){
		    var res = parseFloat(cImporte) - parseFloat(data);
		    var nres  = parseFloat(cPendiente) - res;
		    if(nres == 0){
			data = data;
			npte = 0;
		    }
		    if(nres < 0){
			data = parseFloat(cImporte)-parseFloat(cPendiente);
			npte = 0;
		    }
		    if(nres > 0){
			data = data;
			npte = parseFloat(cPendiente)-(parseFloat(cImporte)-parseFloat(data));
		    }
		}

		if(data == cImporte){
		    data = cImporte;
		    npte = cPendiente;
		}

		id("tbox_importe").value = formatDinero(data);
		id("tbox_pendiente").value = formatDinero(npte);
		id("arreglo_importe_"+cIdTbjoSubsidiario).setAttribute('value',formatDinero(data));
		id("arreglo_importependiente_"+cIdTbjoSubsidiario).setAttribute('value',formatDinero(npte));
		break;
	    case 2:
		//Subsidiario
		if(cEstado == 'Recibido' || cEstado == 'Entregado'){
		    alert("gPOS: \n\n         No puede modificar el Subsidiario en estado -"+
			  cEstado+"-");
		    RevizarServicioSeleccionado();
		    return;
		}

		var subsid = id("tbox_subsidiario").value;
		if(cSubsidiario == subsid) {
		    id("tbox_subsidiario").value = cSubsidiario;
		    return;
		}

		var data = id("idsubsidiariohab").value;
		var subsid = id("tbox_subsidiario").value;

		id("arreglo_subsidiario_"+cIdTbjoSubsidiario).setAttribute('value',subsid);
		cSubsidiario = subsid;
		break;
	    case 3:
		//Estado
		LimpiarFormaCoste();
		var data = id("StatusListaServicios1").value;

		if(data == 'Enviado'){
		    var fecha   = id("date_enviado").value;
		    var hora    = id("time_enviado").value;
		    var xfecha  = fecha+' '+hora;

		    var aFecha  = fecha.split('-');
		    var aHora   = hora.split(':');
		    vFecha      = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0]+' '+aHora[0]+':'+aHora[1];

		    id("arreglo_enviado_"+cIdTbjoSubsidiario).setAttribute('value',vFecha);
		}

		if(data == 'Recibido'){
		    var fecha   = id("date_recibido").value;
		    var hora    = id("time_recibido").value;
		    var xfecha  = fecha+' '+hora;

		    var aFecha  = fecha.split('-');
		    var aHora   = hora.split(':');
		    vFecha      = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0]+' '+aHora[0]+':'+aHora[1];

		    id("arreglo_recibido_"+cIdTbjoSubsidiario).setAttribute('value',vFecha);
		}

		if(data == 'Entregado'){
		    var fecha   = id("date_entregado").value;
		    var hora    = id("time_entregado").value;
		    var xfecha  = fecha+' '+hora;

		    vFecha      = '';
		}

		id("arreglo_status_"+cIdTbjoSubsidiario).setAttribute('value',data);

		break;
	    case 4:
		//Fecha envio
		var fecha = id("date_enviado").value;
		var hora  = id("time_enviado").value;
		var data  = fecha+' '+hora;

		var aFecha  = fecha.split('-');
		var aHora   = hora.split(':');
		vFecha       = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0]+' '+aHora[0]+':'+aHora[1];

		id("arreglo_enviado_"+cIdTbjoSubsidiario).setAttribute('value',vFecha);

		break;
	    case 5:
		//Fecha recivido
		var fecha = id("date_recibido").value;
		var hora  = id("time_recibido").value;
		var data  = fecha+' '+hora;

		var aFecha  = fecha.split('-');
		var aHora   = hora.split(':');
		vFecha       = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0]+' '+aHora[0]+':'+aHora[1];

		id("arreglo_recibido_"+cIdTbjoSubsidiario).setAttribute('value',vFecha);

		break;
	    case 6:
		//Fecha entregado
		var fecha = id("date_entregado").value;
		var hora  = id("time_entregado").value;
		var data  = fecha+' '+hora;

		break;
	    case 7:
		//abonar
		var data    = id("tbox_abonar").value;
		var subsid  = id("tbox_subsidiario").value;
		var subdoc  = id("SubsidiarioDocumento").value;
		var doccod  = id("tbox_coddocumento").value;
		
		if(doccod == '') subdoc = '';

		if(data == '') return;
		
		var npendiente = parseFloat(cPendiente) - parseFloat(data);

		concepto = cServicio+' '+subdoc+' '+doccod+' '+subsid;

		id("arreglo_importependiente_"+cIdTbjoSubsidiario).setAttribute('value',formatDinero(npendiente));


		break;
	    case 8:
		//Observaciones
		//Carga dato
		var data = id("tbox_observacion").value;

		if( data == '' ) return;
		
		break;
	    case 9:
		//Observaciones
		var subdoc  = id("SubsidiarioDocumento").value;
		var doccod  = id("tbox_coddocumento").value;
		
		if(!cExisteMovServicio) return;

		break;
	    }

	    var url = "services.php?modo=ModificarServicios"+
		"&xids="+cIdTbjoSubsidiario+
		"&xdata="+data+
		"&concepto="+concepto+
		"&subdoc="+subdoc+
		"&doccod="+doccod+
		"&pdte="+npte+
		"&xfecha="+xfecha+
		"&xopserv="+xval;

	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);

	    var text = xrequest.responseText;

	    if (!text) return alert('gPOS: '+po_servidorocupado);

	    //LimpiarFormaCoste();
	}

        function ActualizarPeticionCoste(){
	    var abono = 0;
	    abono += parseFloat(CleanMoney(id("tbox_abonar").value));
	    var pendiente = parseFloat(cPendiente) - abono;

	    if(abono > cPendiente){
		pendiente = 0;
		abono = cPendiente;
		id("tbox_abonar").value = cPendiente;
	    }

	    id("tbox_pendiente").value = formatDinero(pendiente);
	}

        function LimpiarFormaCoste(){
	    id("tbox_abonar").value = "0";

	    var f = new Date();
	    var fecha = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
	    var hora  = f.getHours()+":"+f.getMinutes()+":"+f.getSeconds();

	    id("date_enviado").value = fecha;
	    id("date_recibido").value = fecha;
	    id("date_entregado").value = fecha;

	    id("time_enviado").value = hora;
	    id("time_recibido").value = hora;
	    id("time_entregado").value = hora;

	    ActualizarPeticionCoste();
	}

        function auxSubsidiarioHab(){
	    var url   = 'modulos/subsidiarios/selsubsidiario.php?modo=subsidiariopost';
	    var tipo  = 'proveedorhab';
	    popup(url,tipo);
	    
	}

        function auxAltaSubsidiario(){
           var url   = 'modsubsidiarios.php?modo=altapopup';
           var tipo  = 'altaproveedor';
           //var extra = "dialogWidth:" + "400" + "px;dialogHeight:" + "520" + "px"; 
           //window.showModalDialog(url,tipo,extra);
           popup(url,tipo);
        }

        function loadSubsidiarioHab(){
	    if(!SubsidiarioPost) return;
	    if(cSubsidiario == SubsidiarioPost) return;
	    id("tbox_subsidiario").value = SubsidiarioPost;
	    id("idsubsidiariohab").value = IdSubsidiarioPost;

            if(cIdSuscripcion == 0)
	        ModificarServicio(2);
            else
                ModificarSuscripcionCliente('10');
	}
/*+++++++++++++++++++++++++++++ SERVICIOS  ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ VENTAS  ++++++++++++++++++++++++++++++++++*/

/*+++++++++++++ REVISION VENTAS ++++++++++++++*/	

var idetallesVenta        = 0;
//var idfacturaseleccionada = 0;
var cCodigoComprobante    = '';
var cIdComprobante        = 0;
var cComprobante          = '';
var cSerieNroComprobante  = '';
var cClienteComprobante   = '';
var cIdClienteComprobante = 0;
var cMontoComprobante     = 0;
var cPendienteComprobante = 0;
var nrodocumentodevol     = 0;
var seriedocumentodevol   = 0;
var idcomprobantedevol    = 0;
var seriecomprobantedevol = "";
var cIdSuscripcionVenta   = 0;
var cIdComprobanteDet     = 0;
var esOrdenServicio       = 0;
var cReservado            = 0;
var cFechaEntrega         = "";
var cIdAlbaranes          = 0;
var cIdComprobantesNum    = 0;

// Busqueda abanzada
var vFormaVenta    = true;
var vMoneda        = true;
var vUsuario       = true;
var vOP            = true;
var vCodigo        = true;
var vFechaRegistro = true;

var aModificaFecha = Array();

var RevDet = 0;

function VaciarDetallesVentas(){
    var lista = id("busquedaDetallesVenta");
    
    for (var i = 0; i < idetallesVenta; i++) { 
        kid = id("detalleventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallesVenta = 0;
}

function VaciarBusquedaVentas(){
    var lista = id("busquedaVentas");
    
    for (var i = 0; i < ilineabuscaventas; i++) { 
        kid = id("lineabuscaventa_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineabuscaventas = 0;
}

function seleccionarlineaventatpv(linea,xval){
    var lista = (xval)? id("busquedaDetallesVenta"):id("busquedaVentas");
    var fila  = (xval)? id("detalleventa_"+linea):id("lineabuscaventa_"+linea);
    lista.selectItem(fila);
}

function RevisarVentaSeleccionada(){
    
    var idex = id("busquedaVentas").selectedItem;

    if(!idex) return;

    cIdComprobante        = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    cComprobante          = idex.childNodes[3].attributes.getNamedItem('value').nodeValue;
    cSerieNroComprobante  = idex.childNodes[5].attributes.getNamedItem('label').nodeValue;
    cClienteComprobante   = idex.childNodes[6].attributes.getNamedItem('label').nodeValue;
    cIdClienteComprobante = idex.childNodes[6].attributes.getNamedItem('value').nodeValue;
    cMontoComprobante     = idex.childNodes[10].attributes.getNamedItem('label').nodeValue;
    cPendienteComprobante = idex.childNodes[11].attributes.getNamedItem('label').nodeValue;
    //idfacturaseleccionada = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    cCodigoComprobante    = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    var nrodocumento      = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    nrodocumentodevol     = nrodocumento;
    var seriedocumento    = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    seriedocumentodevol   = seriedocumento;
    var cadena            = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
    posicion              = cadena.indexOf('-');
    idfac                 = cadena.substring(posicion+1);
    seriefac              = cadena.substring(0,posicion-1);
    idcomprobantedevol    = parseInt(idfac);
    seriecomprobantedevol = trim(seriefac);
    cIdSuscripcionVenta   = idex.childNodes[15].attributes.getNamedItem('value').nodeValue;
    cReservado            = id("venta_reservado_"+idex.value).getAttribute("value");
    cFechaEntrega         = id("venta_fechareserva_"+idex.value).getAttribute("value");
    cIdAlbaranes          = id("venta_albaranes_"+idex.value).getAttribute("value");
    cIdComprobantesNum    = id("venta_comprobantesnum_"+idex.value).getAttribute("value");

    menuContextualVentasRealizadas(cIdComprobante,false,false);

    if(RevDet == 0 || RevDet != idex.value)
        setTimeout("loadDetallesVentas("+idex.value+")",100);
    if(cEsCobroVenta) 
	setTimeout("BuscarDetallesCobroVenta("+cIdComprobante+")",100);

    RevDet = idex.value;
}

function loadDetallesVentas(xid){
    checkDevolucionDetalle(true);//Limpia...
    VaciarDetallesVentas();
    BuscarDetallesVenta(xid);
} 

function RevisarDetalleVentaSeleccionada(){
    
    var idex      = id("busquedaDetallesVenta").selectedItem;

    if(!idex) return;
    cIdComprobanteDet = idex.value;
    esOrdenServicio   = id("xdetalleventa_ordenservicio_"+idex.value).getAttribute("value");
    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
    var esSeries = ( mseries != 'false' )? true:false;

    var devuelto   = id("detalleventa_obs_"+idex.value).getAttribute("value");
    var esGarantia = ((esOrdenServicio == 0) && (devuelto == 0))? true:false;


    menuContextualVentasRealizadas(cIdComprobante,esSeries,esGarantia);
}

function cargarProducto2Devolver(){

    var idex      = id("busquedaVentas").selectedItem;
    if(!idex) return;
    if(cIdComprobante != idex.value) return;

    //Detalle
    var idexdet   = id("busquedaDetallesVenta").selectedItem;
    if(!idexdet) return;
    cIdComprobanteDet = idexdet.value;
    
    //Salvar Producto
    if( ! cDevolucion[ cIdComprobanteDet ] ){
	var devol       = new Object();
	devol.id        = cIdComprobante; 
	devol.iddet     = cIdComprobanteDet; 
	devol.producto  = idexdet.childNodes[3].attributes.getNamedItem('label').nodeValue;
	devol.codigo    = idexdet.childNodes[2].attributes.getNamedItem('label').nodeValue;
	devol.cantidad  = idexdet.childNodes[5].attributes.getNamedItem('value').nodeValue;
	devol.devolver  = idexdet.childNodes[5].attributes.getNamedItem('value').nodeValue;
	cDevolucion[ cIdComprobanteDet ] = devol;
	cDevolucionList.push( cIdComprobanteDet );
    }
    //Preguntar Cantidad a devolver
    var xcantidad = cDevolucion[ cIdComprobanteDet ].cantidad;
    
    if( xcantidad > 1 )
	xcantidad = parseInt( prompt("gPOS VENTAS DEVOLUCION:\n\n"+
				     "  -  PRODUCTO :  "+cDevolucion[ cIdComprobanteDet ].producto+
				     "\n  -  CANTIDAD :  "+cDevolucion[ cIdComprobanteDet ].cantidad+
				     "\n\n    Ingrese la cantidad a devolver: ",
				     cDevolucion[ cIdComprobanteDet ].devolver ) );
    if( isNaN( xcantidad ) ) return;
    if( !(xcantidad > 0) ) return;
    if( cDevolucion[ cIdComprobanteDet ].cantidad < xcantidad ) return;

    cDevolucion[ cIdComprobanteDet ].devolver = xcantidad;
    id("detalleventa_obs_"+cIdComprobanteDet).setAttribute("label", "*** Devolver  "+xcantidad);
}

function seleccionarALLDetalle2Devolucion(){

    var xlista = id("busquedaDetallesVenta");
    var n      = xlista.itemCount;
    var xiddet,xceldas,cadena,xtext;
    if(n==0) return; 

    for (var i = 0; i < n; i++) {
        xtext    = xlista.getItemAtIndex(i);
        xceldas  = xtext.getElementsByTagName('listcell');
        xiddet   = xtext.value;

	if( ! cDevolucion[ xiddet ] ){
	    var devol       = new Object();
	    devol.id        = cIdComprobante; 
	    devol.iddet     = xiddet; 
	    devol.producto  = xceldas[3].getAttribute('label');
	    devol.codigo    = xceldas[2].getAttribute('label');
	    devol.cantidad  = xceldas[5].getAttribute('value');
	    devol.devolver  = xceldas[5].getAttribute('value');
	    cDevolucion[ xiddet ] = devol;
	    cDevolucionList.push( xiddet );
	}
	else
	    cDevolucion[ xiddet ].devolver = xceldas[5].getAttribute('value');
	id("detalleventa_obs_"+xiddet).setAttribute("label", "*** Devolver  "+cDevolucion[ xiddet ].devolver);
    }
}

function checkDevolucionDetalle(xaccion){

    if ( !cEsDevolucionDetalle ) return;

    //Habilita menu detalle
    var xnoaccion=( xaccion )? false:true;

    id("menuDevolverProducto").setAttribute("collapsed",xaccion);
    id("btnsDevolucion").setAttribute("collapsed",xaccion);
    id("btnreturndetventa").setAttribute("collapsed",xnoaccion);
    id("VentaGarantiaComprobante").setAttribute("collapsed",xnoaccion);

    //Limpia 
    if ( cEsDevolucionDetalle ) 
	if( xaccion ) {
            setTimeout("loadDetallesVentas("+cIdComprobante+")",100);
	    cEsDevolucionDetalle = false;
	    cDevolucion     = new Array();
	    cDevolucionList = new Array();
	}
}

function obtenerDevolucionList(){
    var xout="";
    var xdiff="";

    for (var xkey in cDevolucionList) 
    {
	xid    = cDevolucionList[ xkey ];
	xout  += xdiff+cDevolucion[ xid ].iddet+":"+cDevolucion[ xid ].devolver;
	xdiff  = "~";
    }
    return xout;
}

function habilitaDevolucionVentaSeleccionada(modo){

    //TipoDocumento
    switch( cComprobante )
    {
	
    case 'Boleta' : 
    case 'Factura':
    case 'Ticket':
    case 'Albaran':
	
	var resultado = esCajaCerrada();
	var tcliente  = id("tCliente").label;

	//Caja...
	if(resultado == 1)
	    return alert("gPOS VENTAS DEVOLUCION:\n\n"+
			 "  ESTADO CAJA : CERRADO \n\n"+
			 "  Debe -Abrir Caja- para continuar.")

        //Habilitamos Devolucion Detalle
	cDevolucionModo      = modo;
	cEsDevolucionDetalle = true
	checkDevolucionDetalle(false);
    	break;

    default:
	return alert("gPOS VENTAS DEVOLUCION:\n\n"+
		     "  COMPROBANTE : "+cComprobante+"\n\n"+
		     "   Debe ser diferente a -"+cComprobante+" - para continuar.");
    }

}

function DevolverVentaSeleccionada(){

    //TipoDocumento
    switch( cComprobante )
    {
	
    case 'Boleta' : 
    case 'Factura':
    case 'Ticket':
    case 'Albaran':
	
	var resultado = esCajaCerrada();
	var tcliente  = id("tCliente").label;

	//Codigo Validacion
	if( !Local.esAdmin )
	    if( !validaCodigoAutorizacion(cIdComprobante,'Devolución' ) ) return;

	//Caja...
	if(resultado == 1)
	    return alert("gPOS VENTAS DEVOLUCION:\n\n"+
			 "  ESTADO CAJA : CERRADO \n\n"+
			 "  Debe -Abrir Caja- para continuar.")


	if(!( cDevolucionList.length > 0 )) 
	    return alert("gPOS VENTAS DEVOLUCION:\n\n "+
			 " - Elije por lo menos un producto a devolver.");

	if(!confirm("gPOS VENTAS DEVOLUCION:"+
		    "\n\n  COMPROBANTE : "+ cComprobante +" "+ cSerieNroComprobante +
		    "\n  CLIENTE            : "+ cClienteComprobante +
		    "\n  MONTO              : "+cMoneda[1]['S']+" "+cMontoComprobante +
		    "\n  PENDIENTE       : "+cMoneda[1]['S']+" "+cPendienteComprobante +
		    "\n\n Devolver detalle y Anular el comprobante, está seguro? ")) return;
	break;

    default:
	return alert("gPOS VENTAS DEVOLUCION:\n\n"+
		     "  COMPROBANTE : "+cComprobante+"\n\n"+
		     "   Debe ser diferente a -"+cComprobante+" - para continuar.");
    }
    
    var xdocumento = cComprobante +" Nro. "+ cSerieNroComprobante+ " Cliente "+cClienteComprobante; 
    var xres =false, xpediente=0;
    var xitems = obtenerDevolucionList();
    //Enviar los ID marcados
    
    var	url = 
	"services.php?modo=DevolverComprobanteTPV"+
	"&montocomprobante="+cMontoComprobante+
	"&pendientecomprobante="+cPendienteComprobante+
	"&dependiente="+Local.IdDependiente+
	"&concepto="+xdocumento+
	"&comprobante="+cIdComprobante+
	"&devolvermodo="+cDevolucionModo+
	"&items="+xitems;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = xrequest.responseText;
    var ares = xres.split('~');

    if ( !(parseInt(ares[0]) == 1) ) 
	alert('gPOS: '+po_servidorocupado+'\n\n'+ares[0]);		     

    
    //Productos...
    syncProductosPostTicket();
    //PreVentas...
    syncPresupuesto('Preventa');

    //Clientes por Nota de Credito...
    if( cDevolucionModo == 'credito' ) setTimeout('syncClientes()',600);

    //Limpia...
    cDevolucionModo = '';
    checkDevolucionDetalle(true);

    //Ticket...
    VerTPV();  
    //Confirmar...
    if(!confirm("gPOS VENTAS DEVOLUCION:"+
		"\n\n  COMPROBANTE : "+ cComprobante +" "+ cSerieNroComprobante +
		"\n  CLIENTE            : "+ cClienteComprobante +
		"\n  MONTO              : "+cMoneda[1]['S']+" "+cMontoComprobante +
		"\n  PENDIENTE       : "+cMoneda[1]['S']+" "+cPendienteComprobante +
		"\n\n Se creo un - TICKET PREVENTA - con el detalle, desea cargarlo? ")) 
	return;
    //Ticket PreVenta...
    selTipoPresupuesto(1);
    cargarDetPresupuestoACarrito(ares[1],cIdClienteComprobante,0);
}

function verNSVentaSeleccionada(){

    var idex      = id("busquedaVentas").selectedItem;
    if(!idex) return;
    var idcomp    = idex.childNodes[1].attributes.getNamedItem('id').nodeValue.replace('venta_serie_','');
    var idex      = id("busquedaDetallesVenta").selectedItem;
    var cod       = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    var producto  = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    var mseries   = idex.childNodes[10].attributes.getNamedItem('value').nodeValue;
    var reporte   = '';
    var bandera   = 0;

    if(!idex) return;
    var esSeries = ( mseries != 'false' )? mseries:false;

    if (esSeries ) {

	var arrseries = esSeries.split(';');

	for( var j=0;j<arrseries.length;j++)
	{
	    reporte   = reporte+'\n       - '+arrseries[j];
	    bandera=1;
	}

 	//lanza mesaje Resumen
	if(parseInt(reporte) != 0 ) 
	    alert ( 'gPOS VENTAS: \n\n Producto: '+ producto +'\n N/S:'+ reporte);
    }		
    //Si no es MProducto
    if(parseInt(bandera) != 1 )
	return alert ( " gPOS VENTAS:\n"+
		       "      "+
		       "- No es un Producto con N/S, elija otro.");
}

function verDetMPSeleccionada(){
    
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;
    var idcomp = idex.childNodes[1].attributes.getNamedItem('id').nodeValue.replace('venta_serie_','');
    var idex      = id("busquedaDetallesVenta").selectedItem;
    var cod       = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    var mproducto = idex.childNodes[3].attributes.getNamedItem('label').nodeValue;
    var mseries   = idex.childNodes[10].attributes.getNamedItem('value').nodeValue;
    var reporte   = '';
    var xdetalle  = '';
    var bandera   = 0;

    if(!idex) return;
    var esSeries = ( mseries != 'false' )? mseries:false;

    if (esSeries ) {

	var arrseries = esSeries.split(';');

	for( var j=0;j<arrseries.length;j++)
	{
	    xdetalle = row_mostrardetalleMProducto(arrseries[j],cod+' '+mproducto);
	    if(xdetalle) 
	    {
		reporte  = reporte+'\n\n '+xdetalle;
		bandera  += 1;
	    }
	}

 	//lanza mesaje Resumen
	if(parseInt(bandera) != 0 )  alert ( c_mproducto + reporte);
    }		
    //Si no es MProducto
    if(parseInt(bandera) != 1 )
	return alert ( c_mproducto + "\n"+
		       "      "+
		       "- No es un Meta Producto, elija otro.");
    
}

function obtenerTipoComprobante(num){
    
    var	url =
	"services.php?"
	+"modo=ObtenerTipoComprobante&"
	+"idex="+num+
	+"esVenta=off";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var resultado = xrequest.responseText;
    
    var res = xrequest.responseText;
    return res.split('-');
    
}

function obtenerIdComprobantesAlbaran(num){
    var	url =
	"services.php?"
	+"modo=ObtenerIdComprobantesAlbaran&"
	+"idex="+num+
	+"esVenta=off";
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var resultado = xrequest.responseText;
    
    var res = xrequest.responseText;
    return res.split(',');
}

function  ReimprimirVentaSeleccionada(){
    //VaciarDetallesVentas();
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    
    if (res[0]=='Ticket')
        var comprobante = 1;
    else
        var comprobante = 0;
    t_RecuperaTicket(num,res[0]);
}

function  imprimirImportePendienteVentaSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    //Local.AbonoPendiente = cPendienteComprobante;
    t_RecuperaTicketAbonos(num);
}

function  imprimirFormatoDetalladoTicketSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    Local.AbonoPendiente = cPendienteComprobante;
    Local.ImprimirFormatoTicket = true;
    t_RecuperaTicketAbonos(num);
}

function  imprimirFormatoImporteVentaSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    Local.AbonoPendiente = cPendienteComprobante;
    t_RecuperaTicketAbonos(num);
}

function  imprimirTicketAbonoClienteSeleccionado(){
    t_RecuperaTicketAbonosCliente();
}

function  imprimirTicketCreditoClienteSeleccionado(){
    t_RecuperaTicketCreditosCliente();
}

function imprimirGuiaRemisionVentaSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    //var res  = obtenerTipoComprobante(num);

    var aAlbaran = cIdAlbaranes.split(",");
    var idcomp   = obtenerIdComprobantesAlbaran(num);
    if(aAlbaran.length > 1)
        alert("gPOS:   Impresión Guías de Remisión \n\n    - El comprobante tiene "+aAlbaran.length+" Guía(s) de Remisión");

    if(trim(cIdAlbaranes) != ''){
        for(var i=0;i<idcomp.length;i++){
            t_RecuperaTicket(idcomp[i],'Albaran');
        }
    }
    else
        t_RecuperaTicket(num,'Albaran');    
}

function editarGuiaRemisionVentaSeleccionada(){
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    var idguia = id("venta_guiaremision_"+idex.value).getAttribute('value');
    if(trim(idguia) == '' || !trim(idguia))
        return;

    id("panelDerecho").setAttribute("collapsed",true);
    id("modoVisual").setAttribute("selectedIndex",Vistas.guia);

    frameGuiaRemision.id("BotonCancelarImpresion").setAttribute("oncommand",'parent.cerrarFormGuiaRemision()');
    frameGuiaRemision.id("BotonCancelarImpresion").setAttribute("collapsed",'false');
    frameGuiaRemision.id("txtTipoGuia").setAttribute("value",'Remitente');

    frameGuiaRemision.editarGuiaRemision(idguia);
        
    resizelistboxticket(false);    
}

function cerrarFormGuiaRemision(){
    id("panelDerecho").setAttribute("collapsed",true);
    id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);
    frameGuiaRemision.id("BotonCancelarImpresion").setAttribute("collapsed",'true');
}

var Abonar = new Object();
var modomultipagoabono = false;

function VentanaAbonos(){
    //Valida Alabaran
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    var xcod = false;
    if (res[0]=='Albaran'){
	alert("gPOS:\n "
	      +" - El Comprobante "+res[0]+" esta reservado."
	      +" \n  - Facture este comprobante para poder - Abonar - ")
	     return;
    }

    //Codigo Validacion
    if( !Local.esCajaTPV )
	if( !validaCodigoAutorizacion(num,'Abonar' ) ) return;

    //seteamos la fecha
    var f = new Date();
    var fecha = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
    var hora  = f.getHours()+":"+f.getMinutes()+":"+f.getSeconds();

    id("dateFechaPago").value = fecha;
    id("timeFechaPago").value = hora;
    id("dateFechaEntrega").value = fecha;
    id("timeFechaEntrega").value = hora;
    
	 //VaciarDetallesVentas();
    LimpiarFormaAbonos();
    
    var idex = id("busquedaVentas").selectedItem;
    
    if(!idex)	return;//no se selecciono nada
    
    var IdComprobante = idex.value;
	 
    if (!IdComprobante) return;//seleccion invalidad

    var doc      = id("venta_tipodocumento_"+IdComprobante).getAttribute("label");
    var numdoc   = id("venta_num_bol_"+IdComprobante).getAttribute("label");	
    var xpen = id("venta_pendiente_"+IdComprobante);
    var dineropendiente = xpen.getAttribute("label");
    var serie = id("venta_serie_" + IdComprobante).getAttribute("label");
    var num = id("venta_num_" + IdComprobante).getAttribute("label");
    var serienumfactura = serie+num;
    var cliente = id("venta_cliente_"+IdComprobante).getAttribute("label");
    cliente = cliente.split(":");
    id("titleAbonoVentana").setAttribute("label","Abonar: "+trim(doc)+" "+trim(numdoc));

    //resetea nuevo abono
    Abonar = new Object();	
    
    //fijamos la id actual
    Abonar.IdComprobante = IdComprobante;
    Abonar.Maximo = parseFloat(dineropendiente).toFixed(2);
    //Abonar.TotalImporte = cMontoComprobante;

    id("abono_cliente").setAttribute("value",trim(cliente[1]));
    id("abono_Debe").setAttribute("value",formatDinero(Abonar.Maximo));
    id("abono_Efectivo").setAttribute("value",formatDinero(Abonar.Maximo));
    id("abono_numTicket").setAttribute("value",serie);

    var xentrga = (cFechaEntrega == '0000-00-00 00:00:00' && cReservado == 1)? false:true;

    id("rowReservaEntregado").setAttribute("collapsed",xentrga);
    id("rowFechaReservaEntregado").setAttribute("collapsed",true);

    if(acuentasb){
	RegenEntidadFinanciera();
	RegenCuentas();
    }
    
    document.getElementById("modoVisual").setAttribute("selectedIndex",Vistas.abonar);	
    id("abono_Monto").focus();
}

var esDocCobro = false;
function verDetalleDocumentoCobros(){
    var iddoc = id("modoDeAbonoTicket").value;
    var esdoc = false;
    esDocCobro= false;
    //id("abono_Monto").value = 0;
    ActualizaPeticionAbono();

    switch(iddoc){
    case '1':
    case '9':
	id("abono_Monto").value = usuarios[cIdClienteComprobante].credito;
	id("DetallePagoDocumento").setAttribute("collapsed",!esdoc);
	ActualizaPeticionAbono();
	return;
    case '10':
	id("abono_Monto").value = usuarios[cIdClienteComprobante].bono;
	id("DetallePagoDocumento").setAttribute("collapsed",!esdoc);
	ActualizaPeticionAbono();
	return;
    case '2':
    case '3':
    case '4':
    case '5':
    case '6':
    case '7':
	//if(!acuentasb) alert("gPOS:    \n\n - La modalidad de pago eligido requiere cuentas bancarias");
	esdoc      = true;
	esDocCobro = true;
	break;
    }

    id("DetallePagoDocumento").setAttribute("collapsed",!esdoc);

 	    //Sincorniza Clientes ******
	    //if(!usuarios[IdCliente])
	//	syncClientes();
}

function ActualizaPeticionAbono() {
    var cr = "\n";
    var color ="black";
    var xentrega = id("abono_Monto");

    var xabono = id("modoDeAbonoTicket").value;
    if(xabono == 10){
	if(xentrega.value > usuarios[cIdClienteComprobante].bono)
	    xentrega.value = usuarios[cIdClienteComprobante].bono;
    }

    if(xabono == 9){
	if(xentrega.value > usuarios[cIdClienteComprobante].credito)
	    xentrega.value = usuarios[cIdClienteComprobante].credito;
    }

    if(id("abono_Bono")){
	if(id("abono_Bono").value > usuarios[cIdClienteComprobante].bono)
	    id("abono_Bono").value = usuarios[cIdClienteComprobante].bono;
    }

    if(!modomultipagoabono){
	var entrega = parseFloat(CleanMoney(xentrega.value));
    }else{
	var entrega = 0;
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Efectivo").value));
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Bono").value));
	entrega += parseFloat(CleanMoney(document.getElementById("abono_Tarjeta").value));
    }
    var pendiente = Abonar.Maximo - entrega;

    id("abono_Pendiente").setAttribute("value", formatDinero(pendiente));
    id("abono_nuevo").setAttribute("value", formatDinero(entrega));

        
    Local.AbonoImporte = parseFloat( entrega );
    Local.AbonoDebe    = parseFloat( Abonar.Maximo );
    Local.AbonoPendiente = parseFloat( pendiente );
}

function validarFormularioAbono(){
    if(!id("abono_Monto").value) id("abono_Monto").value       = 0;
    if(!id("abono_Efectivo").value) id("abono_Efectivo").value = 0;
    if(!id("abono_Bono").value) id("abono_Bono").value         = 0;
    if(!id("abono_Tarjeta").value) id("abono_Tarjeta").value   = 0;
}

function LimpiarFormaAbonos(){
    id("abono_Efectivo").value = "0";
    id("abono_Bono").value     = "0";
    id("abono_Tarjeta").value  = "0";
    id("abono_Monto").value    = "0";

    Abonar.Maximo = 0;
    modomultipagoabono = true;
    ModoMultipagoAbono();
    //ActualizaPeticionAbono();	
}

function LimpiarFormDocumentoCobro(){
    id("CodigoOperacion").value = "000000";
    id("NroDocumento").value    = "";
    id("ObservacionesDocCobro").value = "";
}

function ModoMultipagoAbono(){
    if(modomultipagoabono) {				
        id("Abonos_1").setAttribute("collapsed","true");
        id("Abonos_2").setAttribute("collapsed","true");
        id("Abonos_3").setAttribute("collapsed","true");
        id("Abono_Modo").setAttribute("collapsed","false");	
        id("Fila-AbonoEntrega").setAttribute("collapsed","false");
	id("abono_Bono").value = usuarios[cIdClienteComprobante].bono;		
        modomultipagoabono  = false;
    } else {
        id("Abonos_1").setAttribute("collapsed","false");
        id("Abonos_2").setAttribute("collapsed","false");
        id("Abonos_3").setAttribute("collapsed","false");
        id("Abono_Modo").setAttribute("collapsed","true");	
        id("Fila-AbonoEntrega").setAttribute("collapsed","true");
	id("modoDeAbonoTicket").value = 1;
	esDocCobro = false;
        modomultipagoabono  = true;
    }

    id("DetallePagoDocumento").setAttribute("collapsed",!modomultipagoabono);
    ActualizaPeticionAbono();
}


function RealizarAbono(){
    var IdComprobante  = Abonar.IdComprobante;
    var abono_efectivo = (modomultipagoabono)? CleanMoney(id("abono_Efectivo").value):0;
    var abono_tarjeta  = (modomultipagoabono)? CleanMoney(id("abono_Tarjeta").value):0;
    var abono_bono     = (modomultipagoabono)? CleanMoney(id("abono_Bono").value):0;
    var xconcepto      = cComprobante+" "+cSerieNroComprobante+" Cod. "+cCodigoComprobante;
    var entregado      = (id("checkReservaEntregago").checked)? 1:0;
    var fechapago      = id("dateFechaPago").value+" "+id("timeFechaPago").value;
    var fechaentrega   = (entregado)? id("dateFechaEntrega").value+" "+id("timeFechaEntrega").value:"";
    var fechahoy       = calcularFechaActual('fecha')+" "+calcularFechaActual('hora');
    var doc            = id("venta_tipodocumento_"+IdComprobante).getAttribute("label");
    var numdoc         = id("venta_num_bol_"+IdComprobante).getAttribute("label");
    var modalidadpago  = (!modomultipagoabono)? id("modoDeAbonoTicket").value:1;
    abono_efectivo     = (!modomultipagoabono)? CleanMoney(id("abono_Monto").value):abono_efectivo;

    if(abono_efectivo == 0 && abono_bono == 0 && abono_tarjeta == 0)
	return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n   - Ingrese monto del abono");

    if(!modomultipagoabono && abono_efectivo > Abonar.Maximo) 
	abono_efectivo = Abonar.Maximo;

    if(modomultipagoabono){
	var multiabono = parseFloat(abono_efectivo)+parseFloat(abono_bono)+parseFloat(abono_tarjeta);
	if(multiabono > Abonar.Maximo)
	    return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n   - Monto abonado es mayor al monto Debe");
    }

    if(fechahoy < fechapago) 
	return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n   - Fecha de pago es mayor a fecha actual");
    var DocCobro = "";

    if(!modomultipagoabono && abono_efectivo > Abonar.Maximo)
	return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n   - Monto ingresa es mayor a total pendiente");

    if(esDocCobro || (abono_tarjeta != 0 && modomultipagoabono)){

	var idnrocuenta  = id("NroCtaEmpresa").value;
	var codoperacion = trim(id("CodigoOperacion").value);
	var nrodocumento = trim(id("NroDocumento").value);
	var obscobro     = trim(id("ObservacionesDocCobro").value);
	var xrd = "&";

	codoperacion = (codoperacion == '' || codoperacion < '000000')? '000000':codoperacion;

	DocCobro += xrd + "xidnrocta="+idnrocuenta;
	DocCobro += xrd + "xcodop="+codoperacion;
	DocCobro += xrd + "xnrodoc="+nrodocumento;
	DocCobro += xrd + "xobs="+obscobro;

	if(!idnrocuenta)
	    return alert("gPOS:   \n\n - Elija Entidad Financiera");

	if(codoperacion == "")
	    return alert("gPOS:   \n\n - Ingrese Código de Opración del documento");

    }

    //Codigo Validacion
    if( !Local.esCajaTPV )
	if( !validaCodigoAutorizacion(IdComprobante,'Abonar' ) ) return;


    //Caja...
    if( esCajaCerrada() == 1)
	return alert("gPOS Caja :\n\n"+
		     "  ESTADO CAJA : CERRADO \n\n"+
		     "  Debe -Abrir Caja- para continuar.")
    
    var obj = new XMLHttpRequest();
    var url = "services.php?modo=realizarAbono&IdComprobante=" + escape(IdComprobante)
        + "&pago_efectivo=" + parseFloat(abono_efectivo)
        + "&pago_bono=" + parseFloat(abono_bono)
        + "&pago_tarjeta=" + parseFloat(abono_tarjeta)	
        + "&pago_concepto='" + xconcepto +"'"	
        + "&entregado=" + entregado
        + "&fechaentrega=" + fechaentrega
        + "&fechapago=" + fechapago
        + "&modalidadpago=" + modalidadpago
        + "&iduser=" + Local.IdDependiente
        + DocCobro
        + "&r=" + Math.random();		

    obj.open("POST",url,false);
    obj.send("");	
    
    var text = obj.responseText;
    
    var xpen   = id("venta_pendiente_"+IdComprobante);
    var xstatus= id("venta_status_"+IdComprobante);
    
    var ares = text.split("~");

    if(ares[0] != ""){
	alert('gPOS: '+po_servidorocupado+' \n\n    -Al Abonar el comprobante '+doc+' '+numdoc);
	LimpiarFormaAbonos();
	LimpiarFormDocumentoCobro();
	return VolverVentas();
    }
    
    if( ares[1] != '0' ){
	var xval = ares[1].split(" ");
	return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n  - Fecha de pago es menor a la fecha de apertura de caja \n  - Fecha de apertura de caja ["+ares[2]+"]");
    }
    
    if(ares[4] != '0'){
	return alert("gPOS:   Abonando "+doc+" "+numdoc+"\n\n  - Código de Operación "+ares[4]+" ya está registrado");
    }

    if( ares[3] == "" ){
	alert('gPOS: '+po_servidorocupado+' \n\n  -Al Abonar el comprobante '+doc+' '+numdoc);
	LimpiarFormaAbonos();
	LimpiarFormDocumentoCobro();
	return VolverVentas();
    }

    xpen.setAttribute("label",parseFloat( ares[3]).toFixed(2) );//Nuevo valor pendiente

    imprimirImportePendienteVentaSeleccionada();
    
    LimpiarFormaAbonos();
    LimpiarFormDocumentoCobro();
    VolverVentas();
    setTimeout('syncClientes()',2000);
}

function AbonarPorCliente(){

    if( preSeleccionadoCliente == 1 )
	return alert(  c_gpos + " ABONAR EFECTIVO"+
		      "\n\n -   Elija un cliente diferente a Cliente Contado. - ");

    var xid    = preSeleccionadoCliente;
    //Codigo Validacion
    if( !Local.esCajaTPV)
	if( !validaCodigoAutorizacionCliente(xid,'Abonar' ) ) return;


    var xmonto = prompt( c_gpos + " ABONAR EFECTIVO \n"+
			 "\n  CLIENTE        : "+ usuarios[ xid ].nombre +
			 "\n  MODO PAGO  : EFECTIVO "+
			 "\n  DEBE            : "+cMoneda[1]['S']+' '+formatDinero( usuarios[ xid ].debe )+
			 "\n\n Ingrese monto abonar:\n", usuarios[ xid ].debe );
    //cancelo?
    if( xmonto == null)
	return false;
    
    //monto vacio?
    if ( !( parseFloat( xmonto ) > 0) || parseFloat( xmonto ) > usuarios[ xid ].debe ) {
	alert( c_gpos + " ABONAR EFECTIVO \n"+
	       "\n  CLIENTE        : "+ usuarios[ xid ].nombre +
	       "\n  DEBE            : "+cMoneda[1]['S']+' '+formatDinero( usuarios[ xid ].debe )+
	       "\n\n     -   Ingrese correctamente el monto abonar  - ");
	return AbonarPorCliente();
    }


    if( !confirm( c_gpos + " ABONAR EFECTIVO \n"+
		 "\n  CLIENTE           : "+ usuarios[ xid ].nombre +
		  "\n  DEBE               : "+cMoneda[1]['S']+' '+formatDinero( usuarios[ xid ].debe )+
		  "\n  ABONA           : "+cMoneda[1]['S']+' '+formatDinero( xmonto )+
		  "\n  PENDIENTE      : "+cMoneda[1]['S']+' '+
		  formatDinero( parseFloat( usuarios[ xid ].debe - xmonto ).toFixed(2) )+
		  "\n\n se abonará el monto de "+cMoneda[1]['S']+' '+formatDinero( xmonto )+
		  " en los ticket pendientes, ¿está seguro?") )
	return;

    //Codigo Validacion
    if( !Local.esCajaTPV ) return;

    //Caja...
    if( esCajaCerrada() == 1)
	return alert("gPOS Caja :\n\n"+
		     "  ESTADO CAJA : CERRADO \n\n"+
		     "  Debe -Abrir Caja- para continuar.")

    //Mensaje de confirmacion
    var obj = new XMLHttpRequest();
    var url = "services.php?modo=realizarAbonoBrutal&IdCliente=" + escape(xid)
        + "&xmonto=" + parseFloat(xmonto)
        + "&r=" + Math.random();		

    obj.open("POST",url,false);
    obj.send(null);	
    
    var text = obj.responseText;
    var ares = text.split("~");
    
    if(ares[0] != 1)
	alert( c_gpos + " ABONAR EFECTIVO \n"+po_servidorocupado);
    else {
	var xpendiente              = parseFloat( ares[1] );
	Local.AbonoClienteDebe      = usuarios[ xid ].debe;
	Local.AbonoClienteImporte   = formatDinero( xmonto );
	Local.AbonoClientePendiente = formatDinero( parseFloat( ares[1] ).toFixed(2) );
	//parseFloat(usuarios[xid].debe-xmonto)
	imprimirTicketAbonoClienteSeleccionado();
    }
    setTimeout('syncClientes()',600);

}

function AsignarCreditoPorCliente(){
    if( preSeleccionadoCliente == 1 )
	return alert(  c_gpos + " ASIGNAR CREDITO"+
		      "\n\n -   Elija un cliente diferente a Cliente Contado. - ");

    var xid     = preSeleccionadoCliente;
    var cliente = usuarios[ xid ].nombre;
    var credit  = usuarios[ xid ].credito;

    //Codigo Validacion
    if( !Local.esCajaTPV ) return;

    popup("modulos/clientes/selcreditos.php?modo=credito&xidc="+xid+"&xcredit="+credit+"&xcliente="+cliente+"&xidu="+Local.IdDependiente,'credito');
}

function imprimirCreditoCliente(xmonto){
    var xid = preSeleccionadoCliente;
    Local.CreditoClienteImporte   = parseFloat( usuarios[ xid ].credito );
    Local.CreditoClienteEntregado = parseFloat( xmonto );
    Local.CreditoClienteTotal     = parseFloat( usuarios[ xid ].credito ) + parseFloat( xmonto );
    imprimirTicketCreditoClienteSeleccionado();
    setTimeout('syncClientes()',600);
}

var serialNum = (Math.random()*9000).toFixed();

function BuscarDetallesVenta(IdComprobante ){
    RawBuscarDetallesVenta(IdComprobante, AddLineaDetallesVenta);
}

function RawBuscarDetallesVenta(IdComprobante,FuncionRecogerDetalles){
    var obj = new XMLHttpRequest();
    var url = "services.php?modo=mostrarDetallesVenta&IdComprobante=" + escape(IdComprobante)
        + "&r=" + serialNum;		
    serialNum++;		
    obj.open("GET",url,false);
    obj.send(null);	

    var tex = "";
    var cr = "\n";
    var Referencia,Nombre,Talla,Color,Unidades,Precio,Descuento,PV,Codigo,CodBar,Descripcion,Lab,Marca,Serie,Lote,Vence,Servicio,MProducto,Menudeo,Cont,UnidxCont,Unid,Costo,IdPedidoDet,IdComprobanteDet,IdOrdenServicio,CantidadDevuelta,Concepto,IdAlbaran;
    var node,t,i;
    var numitem = 0;
    if (!obj.responseXML) return alert('gPOS: '+po_servidorocupado);		
    
    var xml = obj.responseXML.documentElement;
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node && node.childNodes && node.childNodes.length >0){
	    t = 0;
	    numitem++;
	    if (node.childNodes[t].firstChild){
                Referencia  = node.childNodes[t++].firstChild.nodeValue;
                Nombre      = node.childNodes[t++].firstChild.nodeValue;
                Talla       = node.childNodes[t++].firstChild.nodeValue;
                Color       = node.childNodes[t++].firstChild.nodeValue;
                Unidades    = node.childNodes[t++].firstChild.nodeValue;
		Precio      = node.childNodes[t++].firstChild.nodeValue;
                Descuento   = node.childNodes[t++].firstChild.nodeValue;
                PV          = node.childNodes[t++].firstChild.nodeValue;
                Codigo      = node.childNodes[t++].firstChild.nodeValue;
                CodBar      = node.childNodes[t++].firstChild.nodeValue;
                Lab         = node.childNodes[t++].firstChild.nodeValue;
                Marca       = node.childNodes[t++].firstChild.nodeValue;
                Serie       = node.childNodes[t++].firstChild.nodeValue;
                Lote        = node.childNodes[t++].firstChild.nodeValue;
                Vence       = node.childNodes[t++].firstChild.nodeValue;
                Servicio    = node.childNodes[t++].firstChild.nodeValue;
                MProducto   = node.childNodes[t++].firstChild.nodeValue;
                Menudeo     = node.childNodes[t++].firstChild.nodeValue;
		Cont        = node.childNodes[t++].firstChild.nodeValue;
                UnidxCont   = node.childNodes[t++].firstChild.nodeValue;
                Unid        = node.childNodes[t++].firstChild.nodeValue;
                IdComprobanteDet   = node.childNodes[t++].firstChild.nodeValue;
                IdPedidoDet = node.childNodes[t++].firstChild.nodeValue;
		Costo       = node.childNodes[t++].firstChild.nodeValue;
		Concepto    = node.childNodes[t++].firstChild.nodeValue;
		IdAlbaran   = node.childNodes[t++].firstChild.nodeValue;
		IdOrdenServicio  = node.childNodes[t++].firstChild.nodeValue;
		CantidadDevuelta = node.childNodes[t++].firstChild.nodeValue;

                FuncionRecogerDetalles(CodBar,Nombre,Talla,Color,Unidades,Descuento,PV,
				       Codigo,Lab,Marca,Serie,Lote,Vence,
				       Referencia,Precio,Servicio,MProducto,Menudeo,Cont,
				       UnidxCont,Unid,IdComprobanteDet,numitem,IdPedidoDet,
				       Costo,IdOrdenServicio,CantidadDevuelta);
	    }
        }
    }
}

function AddLineaDetallesVenta(CodBar, Nombre,Talla, Color, unidades, Descuento,
			       PV,Codigo,Lab,Marca,serie,lote,vence,
			       Referencia,Precio,servicio,mproducto,menudeo,cont,
			       unidxcont,unid,IdComprobanteDet,numitem,IdPedidoDet,Costo,
			       IdOrdenServicio,CantidadDevuelta){

    // cod = prodCod[Codigo-1];
    var lista = id("busquedaDetallesVenta");
    var xitem, xReferencia,xNombre,xTalla,xColor,xUnidades,xDescuento,xPV,xSerie,xLote,xVencimiento,xDetalle,xIdProducto,xIdPedidoDet,xCosto,xOrdenServicio,xObservaciones;

    var xresto    = ( menudeo == 1)? unidades%unidxcont                    : false;
    var xcant     = ( menudeo == 1)? ( unidades - xresto )/unidxcont       : false;
    var xcont     = ( menudeo == 1)? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
    var xmenudeo  = ( menudeo == 1)? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;


    var vdetalle  = ( mproducto == 1)? '**MIXPRODUCTO** '       : '';
    var vdetalle  = ( menudeo   == 1)? vdetalle+xmenudeo      : vdetalle;
    var vdetalle  = ( serie!='false')? vdetalle+'NS. '+serie.slice(0,120)+' ' : vdetalle;
    var vdetalle  = ( vence!='false')? vdetalle+'FV. '+vence + ' ' : vdetalle;
    var vdetalle  = ( lote !='false')? vdetalle+'LT. '+lote  + ' ' : vdetalle;
    var vdetalle  = ( servicio == 1 )? '**SERVICIO**' : vdetalle;
    var xobs      = ( CantidadDevuelta > 0 )? '*** '+CantidadDevuelta+' '+unid+' devuelta ***':'';

    xitem = document.createElement("listitem");
    xitem.value = IdComprobanteDet;
    xitem.setAttribute("id","detalleventa_" + idetallesVenta);
    xitem.setAttribute("oncontextmenu","seleccionarlineaventatpv("+idetallesVenta+",true)");
    idetallesVenta++;

    xObservaciones = document.createElement("listcell");
    xObservaciones.setAttribute("value",CantidadDevuelta);
    xObservaciones.setAttribute("id","detalleventa_obs_" + IdComprobanteDet);
    xObservaciones.setAttribute("style","text-align:center;color:#C91918;font-weight: bold;");
    xObservaciones.setAttribute("label",xobs);

    xReferencia = document.createElement("listcell");
    xReferencia.setAttribute("label", Referencia);
    xReferencia.setAttribute("id","xdetalleventa_referencia_"+IdComprobanteDet);

    xCodBar = document.createElement("listcell");
    xCodBar.setAttribute("label", CodBar);
    xCodBar.setAttribute("id","xdetalleventa_codigobarra_"+IdComprobanteDet);
    
    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");
    
    xNombre = document.createElement("listcell");
    xNombre.setAttribute("label", Nombre);
    xNombre.setAttribute("id","xdetalleventa_nombre_"+IdComprobanteDet);
    
    xTalla = document.createElement("listcell");
    xTalla.setAttribute("label", Nombre+" "+Marca+" "+Color+" "+Talla+" "+Lab);
    xTalla.setAttribute("id","xdetalleventa_concepto_"+IdComprobanteDet);
    
    xColor = document.createElement("listcell");
    xColor.setAttribute("label", Color);
    
    xUnidades = document.createElement("listcell");
    xUnidades.setAttribute("label", unidades+" "+unid);
    xUnidades.setAttribute("value",unidades);
    xUnidades.setAttribute("style","text-align:right");
    xUnidades.setAttribute("id","xdetalleventa_unidades_"+IdComprobanteDet);

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label", formatDinero(parseFloat(Precio).toFixed(2)));
    xPrecio.setAttribute("value",Precio);
    xPrecio.setAttribute("style","text-align:right");
    xPrecio.setAttribute("id","xdetalleventa_precio_"+IdComprobanteDet);

    xCosto = document.createElement("listcell");
    xCosto.setAttribute("value",Costo);
    xCosto.setAttribute("collapsed","true");
    xCosto.setAttribute("style","text-align:right");
    xCosto.setAttribute("id","xdetalleventa_costo_"+IdComprobanteDet);
    
    xDescuento = document.createElement("listcell");
    xDescuento.setAttribute("label", parseFloat(Descuento).toFixed(2));
    xDescuento.setAttribute("value", Descuento);
    xDescuento.setAttribute("style","text-align:right");
    xDescuento.setAttribute("id","xdetalleventa_descuento_"+IdComprobanteDet)
    
    xPV = document.createElement("listcell");
    xPV.setAttribute("label", formatDinero(parseFloat(PV).toFixed(2)));
    xPV.setAttribute("value",PV);
    xPV.setAttribute("style","text-align:right");
    xPV.setAttribute("id","xdetalleventa_pv_"+IdComprobanteDet)
    
    xDetalle = document.createElement("listcell");
    xDetalle.setAttribute("label",vdetalle);

    xSerie = document.createElement("listcell");
    xSerie.setAttribute("value",serie);
    xSerie.setAttribute("collapsed","true");
    xSerie.setAttribute("id","xdetalleventa_serie_"+IdComprobanteDet);

    xLote = document.createElement("listcell");
    xLote.setAttribute("value",lote);
    xLote.setAttribute("collapsed","true");
    xLote.setAttribute("id","xdetalleventa_lote_"+IdComprobanteDet);

    xVencimiento = document.createElement("listcell");
    xVencimiento.setAttribute("value",vence);
    xVencimiento.setAttribute("collapsed","true");
    xVencimiento.setAttribute("id","xdetalleventa_vencimiento_"+IdComprobanteDet);

    xIdPedidoDet = document.createElement("listcell");
    xIdPedidoDet.setAttribute("value",IdPedidoDet);
    xIdPedidoDet.setAttribute("collapsed","true");
    xIdPedidoDet.setAttribute("id","xdetalleventa_idpedidodet_"+IdComprobanteDet);

    xIdProducto = document.createElement("listcell");
    xIdProducto.setAttribute("value",Codigo);
    xIdProducto.setAttribute("collapsed","true");
    xIdProducto.setAttribute("id","xdetalleventa_idproducto_"+IdComprobanteDet);

    xOrdenServicio = document.createElement("listcell");
    xOrdenServicio.setAttribute("value",IdOrdenServicio);
    xOrdenServicio.setAttribute("collapsed","true");
    xOrdenServicio.setAttribute("id","xdetalleventa_ordenservicio_"+IdComprobanteDet);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xReferencia );
    xitem.appendChild( xCodBar );
    // xitem.appendChild( xNombre );
    xitem.appendChild( xTalla );
    xitem.appendChild( xDetalle );
    // xitem.appendChild( xColor );
    xitem.appendChild( xUnidades );
    xitem.appendChild( xPrecio );
    xitem.appendChild( xDescuento );	
    xitem.appendChild( xPV );
    xitem.appendChild( xObservaciones );
    xitem.appendChild( xSerie );
    xitem.appendChild( xLote );
    xitem.appendChild( xVencimiento );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xIdPedidoDet );
    xitem.appendChild( xCosto );
    xitem.appendChild( xOrdenServicio );
    lista.appendChild( xitem );
}


function buscarPorCodSerie(elemento){
    //var elemento = id("busquedaCodigoSerie");
    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("busquedaVentas");
    n = lista.itemCount;
    if(n==0) return; 
    busca = busca.toUpperCase();
    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[1].getAttribute('label');
        cadena = cadena.toUpperCase();
        if(cadena.indexOf(busca) != -1){
	    lista.selectItem(texto2);
	    RevisarVentaSeleccionada();
	    return;
        }
    }
    //alert(c_gpos + 'El codigo " '+elemento+' " no esta la lista.');
    id("busquedaCodigoSerie").value='';
}

function CambiarClienteDocumento(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    
    switch (res[0]) {
    case 'Factura':
    case 'Boleta': 
    case 'Albaran':
    case 'Ticket':
	return t_CambiarClienteDocumento(res[0],res[2],num);
	break;
    default:
	alert("gPOS:\n\n - "+po_servidorocupado+'. Acción Desconocida.');
    }
    
}

function t_CambiarClienteDocumento(TipoComprobante,iduser,num){
    
    var nroserie = id("venta_num_bol_" + num).getAttribute("label");

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Cambiar cliente' ) ) return;

    if(!confirm("gPOS:\n\n  Cambiar - "+
		usuarios[iduser].nombre+" - de documento "+
		TipoComprobante+" "+nroserie+"? "))
	return;//No quiere cambiar
    
    //Inicia proceso
    MostrarUsuariosForm();
    esCambiodeCliente = true;//Cambio de cliente de Comprobantes
    IdCompCambioCliente = num;
    //alert("gPOS:\n\n Escoja el Nuevo Cliente, para el Comprobante seleccionado.");
}

function CambiarIdClienteDocumento(iduser){
    
    if( IdCompCambioCliente == 0 ) 
	return alert("gPOS:\n\n"+
		     po_servidorocupado+
		     '\n - Al cambiar Cliente -');
    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Cambiar cliente' ) ) return;

    //Cambia cliente
    setIdClienteDocumento(iduser);
    //Reinica valores
    IdCompCambioCliente = 0;//reset
    esCambiodeCliente = false;//reset 
    //Regresa a ventas
    id("panelDerecho").setAttribute("collapsed",true);
    id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);	
    //Recarga Lista
    BuscarVentas();
}

function setIdClienteDocumento(iduser){
    
    var url =
	"services.php?"+"&"+
	"modo=setIdClienteDocumento"+"&"+
	"iduser="+iduser+"&"+
	"id="+IdCompCambioCliente;
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var xres = parseInt(xrequest.responseText);
    if(!xres) return alert("gPOS:\n\n"+po_servidorocupado+
			   '\n - Al cambiar Cliente -');
}

function ModificarNroDocumento(accion){

    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    var xaccion;

    switch(res[0]){
    case 'AlbaranInt':
	return alert("gPOS VENTAS:\n\n  El Nro. de "+res[0]+" es reservado.");
	break;
    case 'Ticket':
    case 'Factura':
    case 'Boleta':
    case 'Albaran':

	switch(accion){
	case "Modificar_FechaPago":
	    xaccion = 'fechapago';
	case "Modificar_FechaEmision":
	    xaccion = (accion == 'Modificar_FechaEmision')? 'fechaemision':xaccion;
	    //Codigo Validacion
	    if( !Local.esAdmin )
		if( !validaCodigoAutorizacion(cIdComprobante,'Modificar Fecha Emisión' ) ) return;
	    
	    aModificaFecha["idcomprobante"] = num;
	    aModificaFecha["tipocomprobante"] = res[0];
	    aModificaFecha["accion"] = accion;
	    aModificaFecha["serie"] = res[1];
	    mostrarPanelModificarFecha(xaccion);
	    break;
	case "Anular":
	case "Modificar":
	case "Modificar_y_Anular":
	    return t_ModificarNumeroComprobante(num,res[0],accion,res[1]);
	    break;

	default:
	    alert('gPOS VENTAS: '+po_servidorocupado+' \n - Acción Desconocida -');
	}
	break;
    }
}

function FacturarNroDocumento(accion){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);

    if(res[2] < 2)
	return alert("gPOS:   TPV Albaranes \n\n  "+
		     " - Opción reservada para "+
		     " Ticket, Albaran y Boleta relacionado a un - Cliente -.");
    
    if ( res[0]=='Albaran' || res[0]=='Boleta' || res[0]=='Ticket' )
	t_FacturarNumeroComprobante(num,res[0],accion,res[1]);
    else 
	return alert("gPOS TPV Albaranes: \n\n   - Opción reservada para Albaranes, Boletas y Ticket.");
}

function FacturarPorLote(){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var lnum = id("venta_num_bol_"+num).getAttribute('label');
    var res  = obtenerTipoComprobante(num);

    if ( res[0]=='Albaran' || res[0]=='Ticket')
	v_FacturarPorLote(num,lnum,res[1],res[2],res[0]);
    else
	return alert("gPOS:  TPV Albaranes \n\n   - Opción reservada para Albaranes y Ticket.");
}

function v_FacturarPorLote(num,lnum,Serie,idclient,cbte){

    var t_mm = "gPOS:  TPV FACTURAR POR LOTE ";
    if(idclient == 1)
	return alert(t_mm+'\n\n '+
		     ' El '+cbte+' '+lnum+' no está asociado a un Cliente');
    //Controla nro de albaranes por lote 
    if(nltPorFacturar==0){
	var p = prompt(t_mm+" \n\n "+
		       " Ingrese la cantidad de "+cbte+" por facturar:", '');
	//Cancelar pront?
	if( p == null) return;//Brutal termina proceso!!! 

	//Inicia proceso ****
	p = parseInt(p);
	//Valida
	if( p != ""){
	    if(isNaN(p) || p=="" ||  p<=0 )
		return v_FacturarPorLote(num,lnum,Serie,idclient,cbte);
	    if(p<2){
		alert('gPOS: '+t_mm+'\n\n '+
		      ' Seleccione mas de un '+cbte+', para facturar por lote.');
		return v_FacturarPorLote(num,lnum,Serie,idclient,cbte);
	    }
	    //set variable global
	    nltPorFacturar = p;
	    cliPorFacturar = idclient;
	}
    }
    
    //Controla Cliente
    if(idclient!=cliPorFacturar)
	return alert('gPOS: '+t_mm+'\n\n - Selecione '+cbte+' del cliente - '+
		     usuarios[cliPorFacturar].nombre+' -');
    
    //Controla duplicados albaran
    if(lotePorFacturar[num])
	return alert('gPOS: '+t_mm+nltPorFacturar+
		     ' albaranes \n\n   - '+cbte+' '+lnum+'está seleccionado.');
    
	 //Add item Albaran seleccionado 
    if ( parseInt( mltPorFacturar.length ) < parseInt( nltPorFacturar ) ) 
    {
	lotePorFacturar[num]=num; 
	mltPorFacturar.push(lnum);
    }
    
    //Mensaje recursivo
    var mm = '';
    var ps = parseInt(nltPorFacturar) - parseInt(mltPorFacturar.length);
    
    if ( ps != 0 ) 
	mm="\n\n  * Pendiente por seleccionar "+ps+" "+cbte+".";
    else 
	mm="\n\n  * Facturar los "+nltPorFacturar+" "+cbte+"?";
    
    if( !confirm(t_mm+""+nltPorFacturar+" "+cbte+" \n\n"+
		 "  Factuar por lote, los siguientes:\n\n"+
		 "     "+cbte+": "+mltPorFacturar.toString()+
		      "    "+mm ) )
    {
	//Reinicia variables globales, para salir
	return r_FacturarPorLote();//Termina proceso
    }
    else
    {
	if( ps == 0 )
	    return t_FacturarPorLote(Serie);//A facturar por lote 
	else
	    return;//Seguir proceso
    }
}

function r_FacturarPorLote(){
    lotePorFacturar = Array();
    mltPorFacturar  = Array();
    nltPorFacturar  = 0;
    cliPorFacturar  = 0;
}

function t_FacturarPorLote(Serie){

    var t_mm = "gPOS TPV FACTURAR POR LOTE: ";
    var p    = prompt(t_mm+"\n\n Ingrese Nro. Factura de la Serie -"+Serie+"-", '');
    
    //Cancelar...
    if( p == null) return r_FacturarPorLote();//Brutal termina proceso!!!

    //Filtra...
    //p = parseInt(p);

    if(isNaN(p) || p=="" || p.lastIndexOf(' ')>-1 || p<=0 )
    {
	alert("gPOS:\n     "+
	      "El Nro. de Factura es inválido, ingrese otra vez.");
	return t_FacturarPorLote(Serie);
    }
    
    //Existe?...
    var url = 
	"services.php?"
	+"modo=ValidarNumeroComprobante"
	+"&nroComprobante="+p
	+"&Serie="+Serie
	+"&textDoc=Factura";
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    if(xrequest.responseText){
	alert('gPOS: '+t_mm+"\n\n   El número de Factura "+p+
	      " esta registrado en el sistema.\n "+
	      " - Ingrese otro número.");
	//Otro numero...
	return t_FacturarPorLote(Serie);
    }

    //Nuevo...
    var ltAlbaran = '';
    var lt = true;
    for (var k in lotePorFacturar){
	if(lt)
	    ltAlbaran = lotePorFacturar[k]
	else
	    ltAlbaran += '-'+lotePorFacturar[k]
	lt=false;
    }
    
    var url = "services.php?modo=FacturarLoteComprobante&nro="+p
	+"&Serie="+Serie 
	+"&ltAlbaran="+ltAlbaran
	+"&cliAlbaran="+cliPorFacturar
	+"&cidcomprobante="+cIdComprobante
	+"&IdUser="+Local.IdDependiente;
    var xrequest = new XMLHttpRequest();
    var g_mm     = '';
    xrequest.open("POST",url,false);
    xrequest.send( null );

    var xres = xrequest.responseText;

    g_mm = po_servidorocupado+' \n - Al Facturar por Lote ';
    g_mm = (parseInt(xres))? t_mm+"\n\n  Facturación realizada con éxito,\n - Nro. Factura "+p:gmm;

    //Mensaje...
    alert('gPOS: '+g_mm);
    BuscarVentas();
    return r_FacturarPorLote();

}

function BoletarNroDocumento(accion){
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    
    if ( res[0]=='Albaran' || res[0]=='Ticket')
	t_BoletarNumeroComprobante(num,res[0],accion,res[1]);
    else
	return alert("gPOS TPV Albaranes: \n\n  - Opción reservada para Ticket, Albaranes.")
}

function t_BoletarNumeroComprobante(num,TipoComprobante,accion,Serie){

    var p = prompt(TipoComprobante+":\n Ingrese el Nro. Boleta de la Serie -"+Serie+"-", '');
    if(!p) return;
    if(isNaN(p) || p=="" || p.lastIndexOf(' ')>-1 || p<=0 ){
	alert("gPOS:\n  El Nro. de Boleta es inválido, ingrese otra vez.");
	p = "";
	t_BoletarNumeroComprobante(num,TipoComprobante,accion,Serie);
    }
    
    if( p != ""){
	var url = 
	    "services.php?"
	    +"modo=ValidarNumeroComprobante"
	    +"&nroComprobante="+p
	    +"&Serie="+Serie
	    +"&textDoc=Boleta";
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	if(xrequest.responseText){
	    alert("gPOS:\n  El número de Boleta "+p+
		       " esta registrado en el sistema.\n  - Ingrese otro número.");
	    t_BoletarNumeroComprobante(num,TipoComprobante,accion,Serie);
	}

	if(!xrequest.responseText){
	    var url = "services.php?modo=BoletarNumeroComprobante&nro="+p
		+"&tipocomprobante="+TipoComprobante
                +"&IdComprobante="+num
		+"&Serie="+Serie
		+"&IdUser="+Local.IdDependiente
                +'&accion='+accion;
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);

	    var resultado = xrequest.responseText;
	    if(resultado==1) {
		alert('gPOS: '+TipoComprobante+":\n  Boletar realizada con éxito,\n - Nro. Boleta "+p);
		BuscarVentas();
	    }else{
		return alert('gPOS: '+po_servidorocupado+' \n - Al Boletar Comprobante -');
	    }
	    
	}
    }
}

function t_FacturarNumeroComprobante(num,TipoComprobante,accion,Serie){
    
    var p = prompt("gPOS TPV FACTURAR:  "+TipoComprobante+"\n\n Ingrese Nro. Factura de la Serie -"+Serie+"-\n\n", '');
    if(!p) return;

    if(isNaN(p) || p=="" || p.lastIndexOf(' ')>-1 || p<=0 ){
	alert("gPOS:\n\n   El Nro. de Factura es inválido, ingrese otra vez.");
	p = "";
	t_FacturarNumeroComprobante(num,TipoComprobante,accion,Serie);
    }
    
    //Inicia proceso****
    p = parseInt(p);
    
    if( p != ""){
	
	//Esta registrado?
	var url = 
	    "services.php?"
	    +"modo=ValidarNumeroComprobante"
	    +"&nroComprobante="+p
	    +"&Serie="+Serie
	    +"&textDoc=Factura";
	
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	
	//Si esta registrado
	if(xrequest.responseText){
	    alert("gPOS:\n\n   El número de Factura "+p+
		  " esta registrado en el sistema.\n  - Ingrese otro número.");
	    //LLamada recursiva
	    t_FacturarNumeroComprobante(num,TipoComprobante,accion,Serie);
	}
	
	//No esta registrado, procede a facturación  
	if(!xrequest.responseText){
	    var url = "services.php?modo=FacturarNumeroComprobante&nro="+p
		+"&tipocomprobante="+TipoComprobante
                +"&IdComprobante="+num
		+"&Serie="+Serie
		+"&IdUser="+Local.IdDependiente
                +'&accion='+accion;
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    var resultado = xrequest.responseText;
	    
	    if(resultado==1) {
		alert("gPOS TPV FACTURAR:  "+TipoComprobante+"\n\n  Facturación realizada con éxito,\n - Nro. Factura "+p);
		BuscarVentas();
	    }else{
		return alert('gPOS: '+po_servidorocupado+' \n - Al Facturar Comprobante -');
	    }
		 
 	}
    }
}
 
function t_ModificarNumeroComprobante(num,TipoComprobante,accion,Serie){

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Modificar Nro. Comprobante' ) ) return;

    if(accion!='Anular'){
	var p = prompt("gPOS:\n\n"+
		       "   Ingrese el - Nuevo Nro  "+TipoComprobante+" - de la Serie -"+Serie+"-\n\n", '');
    }
    else{
	var snro = id('venta_num_bol_'+num).getAttribute('label');
	var ap= snro.split('-');
	var p = trim(ap[1]);
    }

    if(!p) return;
    
    if(isNaN(p) || p=="" || p.lastIndexOf(' ')>-1 || p<=0 ){
	alert("gPOS:\n\n  El Nro. de "+TipoComprobante+" es inválido, ingrese otra vez.");
	p = "";
	t_ModificarNumeroComprobante(num,TipoComprobante,accion,Serie);
    }
    
    //Inicia proceso****
    p = parseInt(p);
    
    if( p != ""){
	var url = 
	    "services.php?"
	    +"modo=ValidarNumeroComprobante"
	    +"&nroComprobante="+p
	    +"&Serie="+Serie
	    +"&textDoc="+TipoComprobante;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	var xres = xrequest.responseText;
	if(xres){
	    if(accion=='Anular')
	    {
		//Para Anular el Nro, debe estar registrado. 
		if(!confirm("gPOS:\n  - El número de "+TipoComprobante+"  - "+p+
			    " - de la serie  - "+Serie+" - sera anulado.") ) 
		    return;//Salir 
		
		//Para que siga el proceso
		xres = false;
		
		//Obtiene el Numero Ticket a Remplazar
		     p= id('venta_num_'+num).getAttribute('label');
		
	    }
	    else
	    {
		alert("gPOS:\n\n  - El número de "+
		      TipoComprobante+" "+
		      p+" esta registrado en el sistema.\n  - Ingrese otro número");
		t_ModificarNumeroComprobante(num,TipoComprobante,accion,Serie);
	    }
	}
	
	if(!xres){
	    var url = 
		"services.php?"
		+"modo=ModificarNumeroComprobante&nro="+p
		+"&tipocomprobante="+TipoComprobante
		+"&IdComprobante="+num
		+"&Serie="+Serie
		+"&IdUser="+Local.IdDependiente
		+'&accion='+accion;
	    
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    var xres = parseInt(xrequest.responseText);
	    if(xres == 1) {
		alert("gPOS:\n\n       "+accion+" el Nro. de  "
		      +TipoComprobante+"\n"+
		      "      - Acción ejecutada con éxito.\n"+
		      "      - Nuevo código : "+Serie+'-'+p+".");
		BuscarVentas();
	    }else{
		return alert('gPOS: '+po_servidorocupado+' \n Al modificar Numero Comprobante.');
	    }
	    
	}
    }
}

function mostrarPanelModificarFecha(xval){

    cargarDatosPanelModificaFecha(xval);
    var xtop  = parseInt( window.screen.width)/2 - 350;
    var xleft = parseInt( window.screen.height)/2 - 180;

    id("hboxReservado").setAttribute("collapsed",true);
    id("panelFechaEmision").openPopupAtScreen(xtop, xleft, false);

}

function cargarDatosPanelModificaFecha(tfecha){

    var num = aModificaFecha["idcomprobante"];
    var xfecha,wtitleFecha,xcommand;
    var xval = false;
    switch(tfecha){
    case 'fechaemision':
	xfecha = id("venta_fecha_emision_"+num).getAttribute("label");
	wtitleFecha = "    Modificar Fecha Emisión    ";
	xcommand = "t_ModificarFechaEmisionComprobante()";
	break;
    case 'fechareserva':
	xfecha = id("venta_fechareserva_"+num).getAttribute("label");
	wtitleFecha = "    Fecha Entrega    ";
	xcommand = "registrarEntregaReserva()";
	break;
    case 'fechapago':
	xfecha = id("venta_plazopago_"+num).getAttribute("label");
	wtitleFecha = "    Fecha Pago    ";
	xcommand = "ModificarFechaPago()";
	xval = true;
	break;
    }

    //var xfecha = id("venta_fecha_emision_"+num).getAttribute("label");
    if(trim(xfecha) != ''){
	var afecha = xfecha.split(" ");
	var dfecha = afecha[0].split("/");
	var fecha  = dfecha[2]+"-"+dfecha[1]+"-"+dfecha[0];
	var hora   = (afecha[1])? afecha[1]:'00:00:00';
    }else{
	var fecha = calcularFechaActual('fecha');
	var hora  = calcularFechaActual('hora');
    }

    id("dateFechaEmision").value = calcularFechaActual('fecha');
    id("timeFechaEmision").value = calcularFechaActual('hora');
    id("timeFechaEmision").setAttribute("collapsed",xval);
    id("dateFechaEmision").setAttribute("collapsed",false);

    var snro   = trim(id("venta_num_bol_"+num).getAttribute("label"));
    id("wtitleComprobanteVenta").setAttribute("label",aModificaFecha["tipocomprobante"]+" "+snro);
    id("wtitleFechaEmision").setAttribute("label",wtitleFecha);
    id("btnGuardarFechaEmision").setAttribute("oncommand",xcommand);    

}

function t_ModificarFechaEmisionComprobante(){
    var xfecha = id("venta_fecha_emision_"+aModificaFecha["idcomprobante"]).getAttribute("label");
    var afecha = xfecha.split(" ");
    var dfecha = afecha[0].split("/");
    var fechahora = dfecha[2]+"-"+dfecha[1]+"-"+dfecha[0]+" "+afecha[1]+":00";

    var fecha = id("dateFechaEmision").value;
    var hora  = id("timeFechaEmision").value;
    var pfecha = fecha+" "+hora;
    var fechahoy = calcularFechaActual('fecha')+" "+calcularFechaActual('hora');
    var doc      = id("venta_tipodocumento_"+aModificaFecha["idcomprobante"]).getAttribute("label");
    var numdoc   = id("venta_num_bol_"+aModificaFecha["idcomprobante"]).getAttribute("label");

    id("panelFechaEmision").hidePopup();

    if(fechahora == pfecha) 
	return alert("gPOS:   Modificar Fecha Emisión "+doc+" "+numdoc+" \n\n   - Ingrese nueva fecha de emisión");

    if(fechahoy < pfecha) 
	return alert("gPOS:   Modificar Fecha Emisión "+doc+" "+numdoc+"\n\n   - La nueva fecha de emisión es mayor a fecha actual");

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Modificar Fecha Emisión' ) ) return;

    var url = 
	"services.php?"
	+"modo=ModificarFechaEmicionComprobante&fecha="+pfecha
	+"&tipocomprobante="+aModificaFecha["tipocomprobante"]
	+"&IdComprobante="+aModificaFecha["idcomprobante"]
	+'&accion='+aModificaFecha["accion"];
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = xrequest.responseText;
    var ares = xres.split("~");
    if(ares[0] != "")
	return alert('gPOS: '+po_servidorocupado+' \n Al modificar Fecha Emisión.');

    if(parseInt(ares[1]) == 1) {
	alert("gPOS:   Modificar Fecha Emisión  "+doc+" "+numdoc+"\n\n"+
	      "      - Acción ejecutada con éxito.\n"+
	      "      - Nueva Fecha Emisión : "+pfecha+".");
	BuscarVentas();
    }else{
	if(ares[1] != 1){
	    alert("gPOS:  Modificar Fecha Emisión \n\n    - La nueva fecha es menor a la fecha de apertura de caja \n    - Fecha de apertura de caja ["+ares[2]+"]");
	}
    }
}

var ilineabuscaventas = 0;

function AddLineaVentas(item,vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,
			nombreCliente,NumeroDocumento,TipoDocumento,IdCliente,MotivoAlba,
			IdSuscripcion,FechaEmision,Reservado,FechaEntregaReserva,PlazoPago,Albaranes,IdComprobanteNum,IdGuiaRemision){
    var lista = id("busquedaVentas");
    var xitem,xnumitem,xvendedor,xserie,xnum,xfecha,xtotal,xpendiente,xestado,xtipodoc,xop,xsucripcion,xfechaemision,xreserva,xfechareserva,xplazopago,xalbaranes,xcomprobantesnum,xguiaremision;
    
    var vfecha = "0000-00-00 00:00:00";
    var lfecha = "";
    if(FechaEntregaReserva != ' '){
	var afecha = FechaEntregaReserva.split("~");
	vfecha = afecha[1];
	lfecha = afecha[0];
    } 

    var aPlazo = PlazoPago.split("-");
    FechaPlazo = aPlazo[2]+"/"+aPlazo[1]+"/"+aPlazo[0];
    FechaPlazo = (PlazoPago == '0000-00-00')? "":FechaPlazo;
 
    xitem = document.createElement("listitem");
    xitem.value = IdComprobante;
    xitem.setAttribute("id","lineabuscaventa_"+ilineabuscaventas);
    xitem.setAttribute("oncontextmenu","seleccionarlineaventatpv("+ilineabuscaventas+",false)");
    ilineabuscaventas++;
    
    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xop = document.createElement("listcell");
    xop.setAttribute("label",IdComprobante);
    xop.setAttribute("collapsed",vOP);
    xop.setAttribute("style","text-align:center");
    
    xtipodoc = document.createElement("listcell");
    xtipodoc.setAttribute("label",TipoDocumento+' '+MotivoAlba);
    xtipodoc.setAttribute("value",TipoDocumento);
    xtipodoc.setAttribute("style","text-align:left");
    xtipodoc.setAttribute("id","venta_tipodocumento_"+IdComprobante);
    
    xvendedor = document.createElement("listcell");
    xvendedor.setAttribute("label",vendedor);
    xvendedor.setAttribute("collapsed",vUsuario);
    xvendedor.setAttribute("style","text-align:left");
    xvendedor.setAttribute("crop", "end");	
    
    xserie = document.createElement("listcell");
    xserie.setAttribute("label", serie + "-"+num);
    xserie.setAttribute("collapsed",vCodigo);
    xserie.setAttribute("style","text-align:left");
    xserie.setAttribute("id","venta_serie_"+IdComprobante);
    
    xnum = document.createElement("listcell");
    xnum.setAttribute("label", num);
    xnum.setAttribute("id","venta_num_"+IdComprobante);
    
    xfecha = document.createElement("listcell");
    xfecha.setAttribute("style","text-align:right");
    xfecha.setAttribute("label", fecha);
    xfecha.setAttribute("collapsed",vFechaRegistro);

    xfechaemision = document.createElement("listcell");
    xfechaemision.setAttribute("style","text-align:right");
    xfechaemision.setAttribute("label", FechaEmision);	
    xfechaemision.setAttribute("id", "venta_fecha_emision_"+IdComprobante);	
    
    xtotal = document.createElement("listcell");
    xtotal.setAttribute("label", parseFloat(total).toFixed(2));
    xtotal.setAttribute("style","text-align:right");

    xpendiente = document.createElement("listcell");
    xpendiente.setAttribute("label", parseFloat(pendiente).toFixed(2));
    xpendiente.setAttribute("style","text-align:right");
    xpendiente.setAttribute("id","venta_pendiente_"+IdComprobante);

    xestado = document.createElement("listcell");
    xestado.setAttribute("label", estado);
    xestado.setAttribute("style","text-align:center","width: 8em");
    xestado.setAttribute("crop", "end");
    xestado.setAttribute("id","venta_status_"+IdComprobante);
    
    xnombre = document.createElement("listcell");
    xnombre.setAttribute("label", nombreCliente);
    xnombre.setAttribute("value", IdCliente);
    xnombre.setAttribute("crop", "end");
    xnombre.setAttribute("id","venta_cliente_"+IdComprobante);
    
    if(NumeroDocumento=='0')
	NumeroDocumento = num;
    
    xnumdoc = document.createElement("listcell");
    xnumdoc.setAttribute("label", NumeroDocumento+'  ');
    xnumdoc.setAttribute("style","text-align:left");
    xnumdoc.setAttribute("id","venta_num_bol_"+IdComprobante);

    xsuscripcion = document.createElement("listcell");
    xsuscripcion.setAttribute("value", IdSuscripcion);
    xsuscripcion.setAttribute("style","text-align:left");
    xsuscripcion.setAttribute("collapsed","true");
    xsuscripcion.setAttribute("id","venta_suscripcion_"+IdComprobante);

    xreservado = document.createElement("listcell");
    xreservado.setAttribute("value", Reservado);
    xreservado.setAttribute("collapsed", "true");
    xreservado.setAttribute("id","venta_reservado_"+IdComprobante);

    xfechareserva = document.createElement("listcell");
    xfechareserva.setAttribute("value", vfecha);
    xfechareserva.setAttribute("label", lfecha);
    xfechareserva.setAttribute("id","venta_fechareserva_"+IdComprobante);

    xplazopago = document.createElement("listcell");
    xplazopago.setAttribute("value", PlazoPago);
    xplazopago.setAttribute("label", FechaPlazo);
    xplazopago.setAttribute("style","text-align:right");
    xplazopago.setAttribute("id","venta_plazopago_"+IdComprobante);

    xalbaranes = document.createElement("listcell");
    xalbaranes.setAttribute("value", Albaranes);
    xalbaranes.setAttribute("style","text-align:left");
    xalbaranes.setAttribute("collapsed","true");
    xalbaranes.setAttribute("id","venta_albaranes_"+IdComprobante);

    xcomprobantesnum = document.createElement("listcell");
    xcomprobantesnum.setAttribute("value", IdComprobanteNum);
    xcomprobantesnum.setAttribute("style","text-align:left");
    xcomprobantesnum.setAttribute("collapsed","true");
    xcomprobantesnum.setAttribute("id","venta_comprobantesnum_"+IdComprobante);

    xguiaremision = document.createElement("listcell");
    xguiaremision.setAttribute("value", IdGuiaRemision);
    xguiaremision.setAttribute("style","text-align:left");
    xguiaremision.setAttribute("collapsed","true");
    xguiaremision.setAttribute("id","venta_guiaremision_"+IdComprobante);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xserie );
    xitem.appendChild( xop );
    xitem.appendChild( xtipodoc );
    xitem.appendChild( xnum );
    xitem.appendChild( xnumdoc );
    xitem.appendChild( xnombre );	
    xitem.appendChild( xfecha );
    xitem.appendChild( xfechaemision );
    xitem.appendChild( xplazopago );
    xitem.appendChild( xtotal );
    xitem.appendChild( xpendiente );	
    xitem.appendChild( xestado );
    xitem.appendChild( xfechareserva );
    xitem.appendChild( xvendedor );
    xitem.appendChild( xsuscripcion );
    xitem.appendChild( xreservado );
    xitem.appendChild( xalbaranes );
    xitem.appendChild( xcomprobantesnum );
    xitem.appendChild( xguiaremision );

    lista.appendChild( xitem );		
}


function BuscarVentas(){
    VaciarBusquedaVentas();
    VaciarDetallesCobrosVenta();
    checkDevolucionDetalle(true);//Limpia...

    var desde  = id("FechaBuscaVentas").value;
    var hasta  = id("FechaBuscaVentasHasta").value;
    var nombre = id("NombreClienteBusqueda").value;	
    var modoend         = (id("modoConsultaVentasFin").checked);
    var modopen         = (id("modoConsultaVentasPen").checked);
    var modotpventa     = id("modoConsultaTipoVenta").value;
    var modoserie       = "todos";
    var modocontado     = "todos";
    var modosuscripcion = "todos";
    var modoreserva     = "todos";

    var modo            = "todos";    
    //modo                = ( modoend && modopen )? "endypen":"todos";
    modo                = ( modoend && !modopen )? "end":modo;
    modo                = ( !modoend && modopen )? "pen":modo;

    //alert(modotpventa);
    switch( modotpventa ){
    case "contado":     modoserie = "contado"; break;
    case "credito":     modoserie = "cedidos"; break;
    case "suscripcion": modosuscripcion = "suscripcion"; break;
    case "reservas":    modoreserva = "reservados"; break;
    }

    //habilita tree
    BuscarReservados();
    BuscarPlazo();

    var filtrocodigo   = trim(id("busquedaCodigoSerie").value);
    var filtroventa    = id("FiltroVenta").value;
    var modofactura    = (filtroventa =="factura")?"factura":"todos";
    var modoboleta     = (filtroventa =="boleta")?"boleta":"todos";
    var modoticket     = (filtroventa == "ticket" )?"ticket":"todos";
    var modoalbaran    = (filtroventa =="albaran")?"albaran":"todos";
    var modoalbaranint = (filtroventa =="albaranint")?"albaranint":"todos";
    var modocaja       = (filtroventa =="caja" && Local.esCajaTPV == 1 )? "caja":"todos";

    var forzarid       = (filtrocodigo != '' )? filtrocodigo:false;
    var usuario        = id("IdUsuarioVentas").getAttribute("value");
    var tipoproducto   = id("TipoProducto").value;
    
    RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,modoboleta,
		    modoalbaran,modoalbaranint,modoticket,false,false,forzarid,usuario,
		    modoreserva,modocaja,tipoproducto,AddLineaVentas);
    
    var elemento = id("busquedaCodigoSerie").value;
    if( elemento != '' ){
	     //buscarPorCodSerie(elemento);
    }
}


function RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,
			 modoboleta,modoalbaran,modoalbaranint,modoticket,IdComprobante,
			 reimprimir,forzarid,usuario,modoreserva,modocaja,tipoproducto,
			 FuncionProcesaLinea){

    var url = "services.php?modo=mostrarVentas&desde=" + escape(desde) 
        + "&modoconsulta=" + escape(modo) 
        + "&hasta=" + escape(hasta) 
        + "&nombre=" + trim(nombre)
        + "&modoserie=" + escape(modoserie)
        + "&modosuscripcion=" + escape(modosuscripcion)
        + "&modoboleta=" + escape(modoboleta)
        + "&modoticket=" + escape(modoticket)
        + "&modoalbaran=" + escape(modoalbaran)
        + "&modoalbaranint=" + escape(modoalbaranint)
        + "&modofactura=" + escape(modofactura)
        + "&esventas=off"
        + "&modoventa=tpv" 
        + "&forzarfactura=" + IdComprobante
        + "&usuario=" + usuario
        + "&modoreserva=" + modoreserva
        + "&modocaja=" + modocaja
        + "&tipoprod=" + tipoproducto
        + "&forzarid=" + forzarid;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    
    var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,NumeroDocumento,TipoDocumento,IdCliente,IdSuscripcion,FechaEmision,Reservado,FechaEntregaReserva,esContable,PlazoPago,Albaranes,IdComprobanteNum,IdGuiaRemision;
    var node,t,i,codventa,xLocal; 
    var totalVenta = 0;
    var totalVentaPendiente = 0;
    var ImporteTotalVentas = 0;
    var nroboletas = 0;
    var nrofacturas = 0;
    var nrotickets = 0;
    var a_cv       = new Array();
    var a_cvdev    = new Array();
    var nrototalventas = 0;
    if (!obj.responseXML)
        return alert('gPOS: '+po_servidorocupado);
    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    t = 0;
	    vendedor 	    = node.childNodes[t++].firstChild.nodeValue;
	    serie 	    = node.childNodes[t++].firstChild.nodeValue;
	    num 	    = node.childNodes[t++].firstChild.nodeValue;
	    fecha 	    = node.childNodes[t++].firstChild.nodeValue;
	    total 	    = node.childNodes[t++].firstChild.nodeValue;
	    pendiente 	    = node.childNodes[t++].firstChild.nodeValue;
	    estado 	    = node.childNodes[t++].firstChild.nodeValue;
	    IdComprobante   = node.childNodes[t++].firstChild.nodeValue;
	    NumeroDocumento = node.childNodes[t++].firstChild.nodeValue;
	    TipoDocumento   = node.childNodes[t++].firstChild.nodeValue;
	    codventa        = serie+'-'+num;	    
	    esContable      = (!(TipoDocumento == 'AlbaranInt')); 

	    //alert(TipoDocumento+' es Contable -> ['+esContable+']'); 

	    if ( esContable )
	    {
		totalVenta          = parseFloat(totalVenta) + parseFloat(total);
		totalVentaPendiente =  parseFloat(totalVentaPendiente) + parseFloat(pendiente);   
	    }
	    if (TipoDocumento == 'Ticket') nrotickets++; 
	    if (TipoDocumento == 'Boleta') nroboletas++; 
	    if (TipoDocumento == 'Factura') nrofacturas++; 
	    if (a_cvdev[a_cvdev.indexOf(codventa)]==codventa)
		a_cv.push(codventa+':'+total);   
	    
	    if (node.childNodes[t].firstChild)
                nombreCliente = node.childNodes[t++].firstChild.nodeValue;
	    else 
                nombreCliente = "";

	    IdCliente     = node.childNodes[t++].firstChild.nodeValue;
	    xLocal        = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal       = node.childNodes[t++].firstChild.nodeValue;
	    MotivoAlba    = node.childNodes[t++].firstChild.nodeValue;
	    IdSuscripcion = node.childNodes[t++].firstChild.nodeValue;
	    FechaEmision  = node.childNodes[t++].firstChild.nodeValue;
	    PlazoPago     = node.childNodes[t++].firstChild.nodeValue;
	    Reservado     = node.childNodes[t+2].firstChild.nodeValue;
	    FechaEntregaReserva = node.childNodes[t+3].firstChild.nodeValue;
            Albaranes           = node.childNodes[t+4].firstChild.nodeValue;
            IdComprobanteNum    = node.childNodes[t+5].firstChild.nodeValue;
            IdGuiaRemision      = node.childNodes[t+6].firstChild.nodeValue;

	    FuncionProcesaLinea(item,vendedor,serie,num,fecha,total,pendiente,estado,
				IdComprobante,nombreCliente,NumeroDocumento,TipoDocumento,
				IdCliente,MotivoAlba,IdSuscripcion,FechaEmision,
				Reservado,FechaEntregaReserva,PlazoPago,Albaranes,
                                IdComprobanteNum,IdGuiaRemision);
	    
	    item--;
        }
    }
    
    //Sin Resumen...
    if( reimprimir ) return; 

    //CARGAMOS UN PEQUEnO REPORTE DE TOTALES EN EL HEADER
    var c_cvdev = String(unique(a_cvdev));
    var c_cv     = a_cv.toString();
    var a_cv     = c_cv.split(",");
    var a_cvdev  = c_cvdev.split(",");
    var a_cvres  = new Array();
    for (i=0; i<a_cvdev.length; i++) {
	var cvi = 0;
	for (j=0; j<a_cv.length; j++) {
	    var d_cv = a_cv[j].split(":");
	    if(a_cvdev[i]==d_cv[0])
		cvi = parseFloat(cvi) + parseFloat(d_cv[1]);
	}
	if(cvi>0)
	    a_cvres.push(a_cvdev[i]);
	
    }
    nrototalventas = parseFloat(nrofacturas+nroboletas+nrotickets) - parseFloat( a_cvres.length);

    ImporteTotalVentas = parseFloat(totalVenta) - parseFloat(totalVentaPendiente);
    id("TotalImporteVentas").value = " "+cMoneda[1]['S']+" "+formatDinero(totalVenta.toFixed(2));
    id("TotalImporteVentasPendiente").value = " "+cMoneda[1]['S']+" "+formatDinero(totalVentaPendiente.toFixed(2));
    id("ImporteTotalVentas").value    = " "+cMoneda[1]['S']+" "+formatDinero(ImporteTotalVentas);
    id("TotalVentasRealizadas").value = "  " + nrototalventas;
    id("TotalNroFacturas").value      = "  " + nrofacturas;
    id("TotalNroBoletas").value       = "  " + nroboletas;
    id("TotalNroTicket").value        = "  " + nrotickets;
    a_cvres  = new Array();
    a_cv     = new Array();
    a_cvdev  = new Array();
}

function mostrarBusquedaAvanzada(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");

    switch(xlabel){
    case "Forma_Venta":
	vFormaVenta    = xchecked;
	break;
    case "Moneda" : 
	vMoneda        = xchecked;
	break;
    case "Usuario":
	vUsuario       = xchecked;
	if(xchecked) id("IdUsuarioVentas").value = 'todos';
	break;
    case "OP" :
	vOP            = xchecked;
	break;
    case "Codigo":
	vCodigo        = xchecked;
	break;
    case "Fecha_Registro":
	vFechaRegistro = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);

    if( xlabel != 'Forma_Venta') BuscarVentas();
}

function menuContextualVentasRealizadas(xval,xvaldet,esGarantia){
    
    id("VentaRealizadaAbonar").setAttribute("collapsed",true);
    id("VentaRealizadaDevolver").setAttribute("collapsed",true);
    id("VentaRealizadaBoletar").setAttribute("collapsed",true);
    id("VentaRealizadaFacturar").setAttribute("collapsed",true);
    id("VentaRealizadaFacturarLote").setAttribute("collapsed",true);
    id("VentaRealizadaCambioCliente").setAttribute("collapsed",true);
    id("VentaRealizadaCambioNro").setAttribute("collapsed",true);
    id("VentaRealizadaAnularNro").setAttribute("collapsed",true);
    id("VentaRealizadaCambioAnularNro").setAttribute("collapsed",true);
    id("VentaRealizadaDetalleNS").setAttribute("collapsed",true);
    id("VentaRealizadaDetalleMProducto").setAttribute("collapsed",true);
    id("VentaGarantiaComprobante").setAttribute("collapsed",true);
    id("VentaRealizadaEntregarReserva").setAttribute("collapsed",true);
    id("VentaRealizadaFechaPago").setAttribute("collapsed",true);
    id("VentaEstadoReserva").setAttribute("collapsed",true);
    id("VentaGuiaRemision").setAttribute("collapsed",true);
    id("VentaGuiaRemisionEdit").setAttribute("collapsed",true);

    var esSuscripcionVenta = (cIdSuscripcionVenta == 0)? true:false;
    id("VentaSuscripcionImprimir").setAttribute("collapsed",esSuscripcionVenta);

    var esAbonar   =  ( id("venta_pendiente_"+xval).getAttribute("label") > 0 )? true:false
    var tipodoc    = id("venta_tipodocumento_"+xval).getAttribute("label");
    
    var esDevolver = false;
    var esBoletar  = false;
    var esFacturar = false;
    var esFacturarLote = false;
    var esCambioCliente = false;
    var esCambioNro = false;
    var esSeries    = xvaldet;
    var esEntregado = false;

    switch( cComprobante )
    {
    case 'Albaran':
	esFacturar = true;
	esFacturarLote = true;
    case 'Ticket':
	esBoletar = true;
	esFacturar = true;
	esFacturarLote = true;
    case 'Boleta' : 
    case 'Factura':
	esCambioNro     = ( cComprobante =='Ticket' )? false: true;
	esDevolver      = true;
	esCambioCliente = true;
	break;
    }

    if(cReservado == 1 && cFechaEntrega == '0000-00-00 00:00:00'){
	esEntregado = true;
    }

    var xpendiente = id("venta_pendiente_"+xval).getAttribute("label");
    var esPlazo = (xpendiente > 0)? true:false;
    var esreserva = (cReservado == 1 && cFechaEntrega != '0000-00-00 00:00:00')? false:true;

    if(tipodoc == 'AlbaranInt Devolución'){
	esAbonar = false;
	esEntregado = false;
	esreserva = false;
    }

    //Abono
    if ( esAbonar   ) id("VentaRealizadaAbonar").setAttribute("collapsed",false);
    if ( esDevolver ) id("VentaRealizadaDevolver").setAttribute("collapsed",false);
    if ( esBoletar  ) id("VentaRealizadaBoletar").setAttribute("collapsed",false);
    if ( esFacturar ) id("VentaRealizadaFacturar").setAttribute("collapsed",false);
    if ( esFacturarLote ) id("VentaRealizadaFacturarLote").setAttribute("collapsed",false);
    if ( esCambioCliente ) id("VentaRealizadaCambioCliente").setAttribute("collapsed",false);
    if ( esCambioNro ) id("VentaRealizadaCambioNro").setAttribute("collapsed",false);
    if ( esCambioNro ) id("VentaRealizadaAnularNro").setAttribute("collapsed",false);
    if ( esCambioNro ) id("VentaRealizadaCambioAnularNro").setAttribute("collapsed",false);
    if ( esSeries )    id("VentaRealizadaDetalleMProducto").setAttribute("collapsed",false);
    if ( esSeries )    id("VentaRealizadaDetalleNS").setAttribute("collapsed",false);
    if ( esGarantia ) id("VentaGarantiaComprobante").setAttribute("collapsed",false);
    if ( esEntregado ) id("VentaRealizadaEntregarReserva").setAttribute("collapsed",false);
    if ( esPlazo)     id("VentaRealizadaFechaPago").setAttribute("collapsed",false);
    if ( esreserva)   id("VentaEstadoReserva").setAttribute("collapsed",false);

    //Guia remision
    var xidguia = id("venta_guiaremision_"+xval).getAttribute('value');
    var esGuia = (cComprobante == 'Factura' || cComprobante == 'Albaran')? 'true':false;
    var esEditGuia = (trim(xidguia) != '')? true:false;

    if(esGuia) id("VentaGuiaRemision").setAttribute("collapsed",false);
    if(esEditGuia) id("VentaGuiaRemisionEdit").setAttribute("collapsed",false);

}

function verGarantiaComprobante(){
    var idex      = id("busquedaDetallesVenta").selectedItem;
    if(!idex) return;
    var esOrdenServicio = id("xdetalleventa_ordenservicio_"+idex.value).getAttribute("value");
    var devuelto   = id("detalleventa_obs_"+idex.value).getAttribute("value");
    var esGarantia = ((esOrdenServicio == 0) && (devuelto == 0))? true:false;
    var Producto   = id("xdetalleventa_concepto_"+idex.value).getAttribute("label");

    if(esGarantia)
	if(!confirm("gPOS:   Garantía Producto \n\n"+
		    " - Se va generar orden de servicio por garantía del Producto:\n"+
		    "   "+Producto+"\n ¿Desea Continuar?")) return;

    if(servicios.length == 0) 
	return alert("gPOS: Registre por lo menos un servicio para garantías en: \n\n "+
                     "      * Admin > Compras > Productos > Nuevo Producto" );

    VerServicios();
    RecibirGarantiaProducto();
}

function BuscarReservados(){
    var xval = (id("modoConsultaTipoVenta").value == "reservas")? false:true;

    id("vlistcolFechaEntrega").setAttribute("collapsed",xval);
    id("vlistFechaEntrega").setAttribute("collapsed",xval);

    //BuscarVentas();
}

function BuscarPlazo(){
    var xval = (id("modoConsultaVentasPen").checked)? false:true;
    var yval = ( id("modoConsultaTipoVenta").value == "credito" )? false:true;
    xval     = (!xval || !yval)? false:true;

    id("vlistcolPlazoPago").setAttribute("collapsed",xval);
    id("vlistPlazoPago").setAttribute("collapsed",xval);
    //BuscarVentas();
}

function EntregarReservas(){
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    if(cReservado == 1 && cFechaEntrega != '0000-00-00 00:00:00') return;
    aModificaFecha["idcomprobante"] = cIdComprobante;
    aModificaFecha["tipocomprobante"] = cComprobante;
    aModificaFecha["accion"] = "";
    aModificaFecha["serie"] = "";

    mostrarPanelModificarFecha("fechareserva");
}

function formModificarEstadoReserva(){
    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    if(cReservado == 1 && cFechaEntrega != '0000-00-00 00:00:00') return;

    id("wtitleFechaEmision").setAttribute("label",'Modificar Estado Reserva');
    id("dateFechaEmision").setAttribute("collapsed",true);
    id("timeFechaEmision").setAttribute("collapsed",true);
    id("hboxReservado").setAttribute("collapsed",false);
    id("btnGuardarFechaEmision").setAttribute("oncommand",'ModificarEstadoReserva()');
    var xreserva = (cReservado == 1)? true:false;
    id("checkReserva").setAttribute("checked",xreserva);
    id("wtitleComprobanteVenta").setAttribute("label",cComprobante+" "+trim(cSerieNroComprobante))

    var xtop  = parseInt( window.screen.width)/2 - 250;
    var xleft = parseInt( window.screen.height)/2 - 150;
    
    id("panelFechaEmision").openPopupAtScreen(xtop, xleft, false);
}

function ModificarEstadoReserva(){
    id("panelFechaEmision").hidePopup();
    id("hboxReservado").setAttribute("collapsed",true);

    var reservado = (id("checkReserva").checked);
    reservado = (reservado)? 1:0;

    var obj = new XMLHttpRequest();
    var url = "services.php?modo=ModificaEstadoReserva&IdComprobante=" + cIdComprobante
        + "&xreserva="+reservado
        + "&r=" + Math.random();		

    obj.open("POST",url,false);
    obj.send("");
    
    var text = obj.responseText;

    if(!parseInt(text)) alert("gPOS:  Reservas \n\n   - No se cambió el estado de reserva de "+cComprobante+" "+trim(cSerieNroComprobante));
    BuscarVentas();
}


function registrarEntregaReserva(){
    id("panelFechaEmision").hidePopup();
    
    var fecha = id("dateFechaEmision").value;
    var hora  = id("timeFechaEmision").value;
    var pfecha = fecha+" "+hora;

    var obj = new XMLHttpRequest();
    var url = "services.php?modo=EntregarReserva&IdComprobante=" + cIdComprobante
        + "&xfecha="+pfecha
        + "&r=" + Math.random();		

    obj.open("POST",url,false);
    obj.send("");
    
    var text = obj.responseText;

    if(!parseInt(text)) alert("gPOS:  Comprobantes Reservados \n\n   - No realizó la entrega del "+cComprobante+" "+trim(cSerieNroComprobante)+" reservado");
    BuscarVentas();
}

function ModificarFechaPago(){
    id("panelFechaEmision").hidePopup();
    
    var fecha = id("dateFechaEmision").value;
    var pfecha = fecha;

    var obj = new XMLHttpRequest();
    var url = "services.php?modo=ModificaFechaPago&IdComprobante=" + cIdComprobante
        + "&xfecha="+pfecha
        + "&r=" + Math.random();		

    obj.open("POST",url,false);
    obj.send("");
    
    var text = obj.responseText;

    if(!parseInt(text)) alert("gPOS:  Fecha de Pago \n\n   - No se modificó fecha de pago del "+cComprobante+" "+trim(cSerieNroComprobante));
    BuscarVentas();
}

function VerFechaReservaEntregado(xval){
    id("rowFechaReservaEntregado").setAttribute("collapsed",!xval);

    if(xval){
	id("dateFechaEntrega").value = id("dateFechaPago").value;
	id("timeFechaEntrega").value = id("timeFechaPago").value;
    }
}

var icuentas = 0;
var acuentasb = "";
var ientidadfinanciera = 0;

function VaciarCuentas(){
    var xlistitem = id("elementosCuenta");
    var iditem;
    var t = 0;
    
    while( el = id("cuenta_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    icuentas = 0;
    
    id("NroCtaEmpresa").setAttribute("label","");	
}

function RegenCuentasBancarias(){
    var idprov = 0;

    var xrequest = new XMLHttpRequest();
    var url = "services.php?modo=cuentasbancarias&idprov="+idprov+"&todo=0";
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res = xrequest.responseText;
    acuentasb = res;

}

function RegenCuentas() {
    VaciarCuentas();
    var entidad = id("EntidadFinanciera").getAttribute("label");

    var lines = acuentasb.split("\n");
    var actual,ent;
    var xent = "";
    var ln = lines.length-1;	
    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[2] == entidad)
	    AddCuentaLine('('+ent[0]+') '+ent[1],actual[1]);		
    }				
}

function AddCuentaLine(nombre, valor) {
    var xlistitem = id("elementosCuenta");	

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
    var lines = acuentasb.split("\n");
    var actual,ent;
    var xent = "";
    var ln = lines.length-1;	
    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	ent = actual[0].split("~");
	if(ent[2] != xent)
	    AddEntidadFinancieraLine(ent[2],actual[1]);
	xent = ent[2];
    }				   
}

function AddEntidadFinancieraLine(nombre, valor) {
    var xlistitem = id("elementosEntFinanciera");	
    
    var xentidad = document.createElement("menuitem");
    xentidad.setAttribute("id","entidad_def_" + ientidadfinanciera);
    
    xentidad.setAttribute("value",nombre);
    xentidad.setAttribute("label",nombre);
    
    xlistitem.appendChild( xentidad);
    if(ientidadfinanciera == 0) id("EntidadFinanciera").value = nombre;	
    ientidadfinanciera++;
}

function VaciarEntidadFinanciera(){
    var xlistitem = id("elementosEntFinanciera");
    var iditem;
    var t = 0;
    
    while( el = id("entidad_def_"+ t) ) {
	if (el)	xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    ientidadfinanciera = 0;
    
    id("EntidadFinanciera").setAttribute("label","");	
}

function CogeNroCuenta(){
    popup("modulos/pagoscobros/selcuentabancaria.php?modo=cuenta&xidprov=0&xcta=0",'cuenta');
}


function changeNroCuenta( quien, txtcuenta) {
    RegenEntidadFinanciera();
    id("EntidadFinanciera").value = txtcuenta;
    RegenCuentas();
    id("NroCtaEmpresa").value     = quien.value;
}


function validaCodigoAutorizacion(xid,xop){

    var xcod;
    
    if ( Local.CodigoAutorizacion[xid] )
        xcod = Local.CodigoAutorizacion[xid]; 
    else
	xcod = prompt( c_gpos + " Código de Autorización de Operaciones: \n"+
		       "\n  CLIENTE "+ cClienteComprobante +
		       "\n  COMPROBANTE       : "+ cComprobante +" "+ cSerieNroComprobante +
		       "\n  OPERACION             : "+ xop +
		       "\n\nIngrese el código de autorización de operaciones:\n\n", '');

    //cancelo?
    if( xcod == null)
	return false;

    //codigo vacio?
    if ( xcod == '') {
	alert( c_gpos + " Código de Autorización de Operaciones: \n"+
	       "\n     -   Ingrese correctamente el código de autorización  - ");
	return validaCodigoAutorizacion(xid,xop);
    }

    //servidor?
    var url,xres,xarrcod;

    url  = "services.php?modo=validaCodigoAutorizacionTPV"+
	"&xid="+xid+
	"&xcod="+xcod;
    xres = new XMLHttpRequest();
    xres.open("GET",url,false);
    try{
	xres.send(null);
    } catch(z){
	return;
    }
    
    //alert( xres.responseText );
    xarrcod = xres.responseText.split("~");

    //respuesta del servidor
    if(xarrcod[0] != 0 ){ 
	alert(po_servidorocupado); return false; 
    }
    
    Local.CodigoAutorizacion[xid] = ( xarrcod[1] == 1 )? xcod: false;

    if( !Local.CodigoAutorizacion[xid] )
	alert( c_gpos + " Código de Autorización de Operaciones: "+
	       "\n\n    -   El código de autorización es incorrecto ó expiró  -\n");

    return ( xarrcod[1] == 1 );
}

function validaCodigoAutorizacionCliente(xid,xop){

    var xcod;
    
    if ( Local.CodigoAutorizacionCliente[xid] )
        xcod = Local.CodigoAutorizacionCliente[xid]; 
    else
	xcod = prompt( c_gpos + " Código de Autorización de Operaciones: \n"+
		       "\n  CLIENTE       :"+ usuarios[ preSeleccionadoCliente ].nombre +
		       "\n  OPERACION  : "+ xop +
		       "\n\nIngrese el código de autorización de operaciones:\n\n", '');
    //cancelo?
    if( xcod == null)
	return false;

    //codigo vacio?
    if ( xcod == '') {
	alert( c_gpos + " Código de Autorización de Operaciones: \n"+
	       "\n     -   Ingrese correctamente el código de autorización  - ");
	return validaCodigoAutorizacionCliente(xid,xop);
    }

    //servidor?
    var url,xres,xarrcod;

    url  = "services.php?modo=validaCodigoAutorizacionClienteTPV"+
	"&xid="+xid+
	"&xcod="+xcod;
    xres = new XMLHttpRequest();
    xres.open("GET",url,false);
    try{
	xres.send(null);
    } catch(z){
	return;
    }
    
    //alert( xres.responseText );
    xarrcod = xres.responseText.split("~");

    //respuesta del servidor
    if(xarrcod[0] != 0 ){ 
	alert(po_servidorocupado); return false; 
    }
    
    Local.CodigoAutorizacionCliente[xid] = ( xarrcod[1] == 1 )? xcod: false;

    if( !Local.CodigoAutorizacionCliente[xid] )
	alert( c_gpos + " Código de Autorización de Operaciones: "+
	       "\n\n    -   El código de autorización es incorrecto ó expiró  -\n");

    return ( xarrcod[1] == 1 );
}

function ckCodigoAutorizacionCliente(xaccion,xck){
    if( preSeleccionadoCliente == 1 )
	return alert( c_gpos + " Código de Autorización de Operaciones: "+
		      "\n\n -   Elija un cliente diferente a Cliente Contado. - ");
    ckCodigoAutorizacion(xaccion,xck,preSeleccionadoCliente,false);
}


function ckCodigoAutorizacion(xaccion,xck,xid,xreset){
    var idex = id("busquedaVentas").selectedItem;
    var xid  = ( xaccion == 'ck' )? idex.value:xid;
    var url  = "services.php?modo=codigoAutorizacionTPV"+
	       "&xid="+xid+
	       "&xaccion="+xaccion;

    var xres = new XMLHttpRequest();

    xres.open("GET",url,false);
    try{
	xres.send(null);
    } catch(z){
	return;
    }
    //alert( xres.responseText );
    var newaccion = 'reset'+xaccion; 
    var xarrcod  = xres.responseText.split("~");
    var xtrmsj   = ( !xarrcod[2] )? '\n\n      *** Nuevo Código Generado  ***':'';
    var xmsj     = "";

    if(xarrcod[0] != 0 )
	return alert(po_servidorocupado);
    if ( xaccion == 'ck' || xaccion == 'resetck' )
	xmsj  = c_gpos + " Código de Autorización de Operaciones: \n"+
	"\n  CLIENTE "+ cClienteComprobante +
	"\n  COMPROBANTE        : "+ cComprobante +" "+ cSerieNroComprobante +
	"\n  CODIGO                   : ***"+xarrcod[1]+xtrmsj;

    if ( xaccion == 'ckcliente' || xaccion == 'resetckcliente' )
	xmsj  = c_gpos + " Código de Autorización de Operaciones: \n"+
	"\n  CLIENTE  : "+ usuarios[ preSeleccionadoCliente ].nombre +
	"\n  CODIGO    : ***"+xarrcod[1]+xtrmsj;

    //codigo nuevo
    if( xreset )
	return alert( xmsj+'\n' );

    //codigo guardado
    if( confirm( xmsj+"\n\n  desea generar un nuevo código de autorización?" ) )
	ckCodigoAutorizacion('reset'+xaccion,false,xid,true);
}


/*+++++++++++++++++++++++++++++ VENTAS  ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ VENTAS COBROS ++++++++++++++++++++++++++++++++++*/
var cEsCobroVenta = false;
var idetallescobroventa = 0;

function mostrarDetalleVenta(xval){

    switch(xval){
      case 'comprobante':
	var cbte = false;
	var cbro = true;
	cEsCobroVenta = false;
	break;
      case 'cobros':
	var cbte = true;
	var cbro = false;
	cEsCobroVenta = true;
	break;	
    }

    var xtitle = (cbte)? "Detalle Cobros":"Detalle Comprobantes";
    id("boxDetalleComprobantes").setAttribute('collapsed',cbte);
    id("t_detalle").setAttribute("checked",!cbte);
    id("onlistDetalle").setAttribute("label",xtitle);
    id("boxDetalleCobros").setAttribute('collapsed',cbro);
    id("t_cobros").setAttribute("checked",!cbro);
    if(cbro) id("btnsDevolucion").setAttribute('collapsed',true);

    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;
    
    if(!cbro) setTimeout("BuscarDetallesCobroVenta("+cIdComprobante+")",0);
}

function BuscarDetallesCobroVenta(IdComprobante){
    VaciarDetallesCobrosVenta();
    RawBuscarDetallesCobroVenta(IdComprobante, AddLineaDetallesCobro);
}

function VaciarDetallesCobrosVenta(){
    var lista = id("busquedaDetallesCobroVenta");
    
    for (var i = 0; i < idetallescobroventa; i++) { 
        kid = id("v_detallecobro_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    idetallescobroventa = 0;
}

function RawBuscarDetallesCobroVenta(IdComprobante,FuncionRecogerDetalles){

    var obj = new XMLHttpRequest();
    var z   = null;
    var url = "services.php?modo=mostrarDetallesCobro&IdComprobante="+IdComprobante;
    obj.open("GET",url,false);

    try {
	obj.send(null);
    } catch(z){
	return;
    }

    var tex = "";
    var cr = "\n";
    var item,ModoPago,FechaPago,ImportePago,IdComprobante,Usuario,Simbolo,Caja,Local,LocalPago,IdModalidad,TipoVenta;
    var node,t,i;
    var numitem = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);
    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC   = item;
    var numitem = 0;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
	    numitem++;
	    if(node.childNodes[t].firstChild){
		ModoPago     = node.childNodes[t++].firstChild.nodeValue;
		FechaPago    = node.childNodes[t++].firstChild.nodeValue;
		ImportePago  = node.childNodes[t++].firstChild.nodeValue;
		Usuario      = node.childNodes[t++].firstChild.nodeValue;
		IdOperacion  = node.childNodes[t++].firstChild.nodeValue;
		Local        = node.childNodes[t++].firstChild.nodeValue;
		IdModalidad  = node.childNodes[t++].firstChild.nodeValue;
		TipoVenta    = node.childNodes[t++].firstChild.nodeValue;
		LocalPago    = node.childNodes[t++].firstChild.nodeValue;
		
		FuncionRecogerDetalles(numitem,FechaPago,ImportePago,Usuario,ModoPago,
				       IdOperacion,Local,LocalPago,IdModalidad,TipoVenta);
            //item--;
	    }
        }
    }
}

function AddLineaDetallesCobro(numitem,FechaPago,ImportePago,Usuario,ModoPago,
			       IdOperacion,Local,LocalPago,IdModalidad,TipoVenta){

    var lista = id("busquedaDetallesCobroVenta");
    var xitem,xnumitem,xFechaPago,xModoPago,xUsuario,xIMportePago,xLocalPago,xTipoVenta;

    xitem = document.createElement("listitem");
    xitem.value = IdOperacion;
    xitem.setAttribute("id","v_detallecobro_" + idetallescobroventa);
    idetallescobroventa++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label", '  '+numitem+'. ');
    xnumitem.setAttribute("style","text-align:left");

    xModoPago = document.createElement("listcell");
    xModoPago.setAttribute("label", ModoPago);
    xModoPago.setAttribute("value", IdModalidad);
    xModoPago.setAttribute("id","c_modopago_"+IdOperacion);

    xFechaPago = document.createElement("listcell");
    xFechaPago.setAttribute("label", FechaPago);
    xFechaPago.setAttribute("id","c_fechapago_"+IdOperacion);

    xImportePago = document.createElement("listcell");
    xImportePago.setAttribute("label", formatDinero(ImportePago));
    xImportePago.setAttribute("value",ImportePago);
    xImportePago.setAttribute("style","font-weight:bold;text-align:right");
    xImportePago.setAttribute("id","c_importe_"+IdOperacion);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label", Usuario);
    xUsuario.setAttribute("style","text-align:center");
    xUsuario.setAttribute("id","c_usuario_"+IdOperacion);

    xLocalPago = document.createElement("listcell");
    xLocalPago.setAttribute("label", LocalPago+' - '+Local);
    xLocalPago.setAttribute("style","text-align:center");
    xLocalPago.setAttribute("id","c_localpago_"+IdOperacion);

    xTipoVenta = document.createElement("listcell");
    xTipoVenta.setAttribute("value", TipoVenta);
    xTipoVenta.setAttribute("collapsed","true");
    xTipoVenta.setAttribute("id","c_tipoventa_"+IdOperacion);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xFechaPago );
    xitem.appendChild( xModoPago );
    xitem.appendChild( xImportePago );
    xitem.appendChild( xLocalPago );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xTipoVenta );
    lista.appendChild( xitem );
}


function ImprimirCobroSeleccionadaVenta(){

    var idex = id("busquedaVentas").selectedItem;
    if(!idex) return;

    var idoc          = idex.value;
    var importe       = cMontoComprobante;
    var moneda        = 1;
    var importeletras = convertirNumLetras(importe,moneda);
    importeletras     = importeletras.toUpperCase();
    var url           = "modulos/fpdf/imprimir_cobros.php?idoc="+idoc+
                        "&totaletras="+importeletras;
    location.href=url;
}

function RevisarCobroSeleccionadaVenta(){
    var idex = id("busquedaDetallesCobroVenta").selectedItem;
    if(!idex) return;
    //var idpago = idex.value;

    ccIdOperacionCaja = idex.value;
    ccIdModalidadPago = id("c_modopago_"+idex.value).getAttribute("value");
    ccTipoVenta       = id("c_tipoventa_"+idex.value).getAttribute("value");
    ccImporteCobro    = id("c_importe_"+idex.value).getAttribute("value");
}

function ModificarCobrosVenta(xval){
    var idex = id("busquedaDetallesCobroVenta").selectedItem;
    if(!idex) return;

    //Codigo Validacion
    if( !Local.esAdmin )
	if( !validaCodigoAutorizacion(cIdComprobante,'Eliminar Abono' ) ) return;

    var msj = "- Cliente: "+cClienteComprobante+"\n- Monto : "+ccImporteCobro;
    if(!confirm('gPOS:  Eliminar Abonos Cliente \n\n'+msj+',\n'+'Va eliminar el abono.  ¿desea continuar?'))
	return;

    var obj = new XMLHttpRequest();
    var url = "modulos/pagoscobros/modpagoscobros.php?modo=ModificarCobros"
	+ "&idopcja=" + escape(ccIdOperacionCaja)
        + "&idcbte=" + escape(cIdComprobante)
        + "&idmod=" + escape(ccIdModalidadPago)
        + "&idc=" + escape(cIdClienteComprobante)
        + "&tv=" + escape(ccTipoVenta)
        + "&ximp=" + escape(ccImporteCobro)
        + "&xop=" + escape(xval);

    obj.open("POST",url,false);
    obj.send("");	
    
    var text = obj.responseText;    
    var ares = text.split("~");
    
    if(ares[0] != '')
	return alert('gPOS:  Eliminar Abono Cliente \n\n- Error al Eliminar \n'+po_servidorocupado+'\n'+ares[0]);

    if(ares[1] == 'cjacda')
	return alert("gPOS:  Eliminar Abono Cliente \n\n- No se eliminó el abono, la caja está cerrada");
    if(ares[2]){
	alert("gPOS:  Eliminar Abono Cliente \n\n- Se Eliminó el abono");
	BuscarVentas();
    }
}

/*+++++++++++++++++++++++++++++ VENTAS COBROS ++++++++++++++++++++++++++++++++++*/



/*+++++++++++++++++++++++++++++ TICKETS ++++++++++++++++++++++++++++++++++*/

/*++++++++++++ CREAR TICKET USO  +++++++++++*/

/* Crea un ticket desde el formulario de pantalla */
function t_CrearTicket(esCopia,noticket) {

    var ticket = new Ticket();
    
    /* Iniciamos un nuevo ticket, trae num del servidor, etc */
    ticket.IniciaNumeroDeSerie(ModoDeTicket);//trae del servidor etc
    
    /* Dinero entregado, y quien es el dependiente */
    if( modoMultipago )
    {
	ticket.multipago = true;		
	ticket.entregaCambio   = parseFloat(id("peticionPendiente").label);		
	//NOTA: suponemos que lo efectivo entregado es el dinero que queda en 
	// caja pasadas operaciones de cambio.
	ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEfectivo").value));
	ticket.entregaTarjeta  = parseFloat(CleanMoney(id("peticionTarjeta").value));
	ticket.entregaBono     = parseFloat(CleanMoney(id("peticionBono").value));
	ticket.setEntregado( ticket.entregaEfectivo + ticket.entregaTarjeta + ticket.entregaBono  );
    }
    else 
    {
	var modo =  id("modoDePagoTicket").value;
	ticket.multipago       = false;
	ticket.entregaEfectivo = 0;
	ticket.entregaTarjeta  = 0;
	ticket.entregaBono     = 0;
	ticket.entregaCambio   = parseFloat(id("peticionPendiente").label); 
	
	switch(parseInt(modo)){
	case 1://EFECTIVO
	    ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEntrega").value));
	    break;
	case 5://TARJERA
	    ticket.entregaTarjeta = parseFloat(CleanMoney(id("peticionEntrega").value));
	    //alert('entregado con tarjeta:'+ticket.entregaTarjeta);
	    break;
	case 10:///BONO
	    ticket.entregaBono    = parseFloat(CleanMoney(id("peticionEntrega").value));
	    break;
	default:
	    ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEntrega").value));
	    //alert('pago generico:'+modo);
	    break;			
	}
	ticket.setEntregado( ticket.entregaEfectivo + ticket.entregaTarjeta + ticket.entregaBono  );
    }

    /*++++++++ Promociones +++++++++*/
    ticket.promocionID   = PromocionSeleccionado; 
    ticket.promocionBono = (PromocionSeleccionado)? promociones[ PromocionSeleccionado ].bono:0;
    ticket.setDependiente(Local.nombreDependiente);
    ticket.SetModoPago( parseInt(id("modoDePagoTicket").value) );
    modospago[id("modoDePagoTicket").value] = id("modoDePagoTicket").getAttribute("label");
    //ATENCION: avanzamos numero de ticket		
    if (!esCopia) Local.numeroDeSerie = Local.numeroDeSerie + 1;
    
    var res = new Object();	

    res.text_data = (noticket == 0)? ticket.generaTextTicket() : ticket.generaTextTicketPreVenta();
    res.post_data = ticket.generaPostData();
    return res;
}


/* Recupera un ticket, desde datos XML. */

function t_RecuperaTicket(IdComprobante,TipoVenta){

    switch( TipoVenta )
    {
    case 'Ticket':
	imprimirFormatoDetalladoTicketSeleccionada();
	break;
	
    case 'Factura':
    case 'Boleta':
    case 'Albaran':
    case 'AlbaranInt':
	
	//obtenemos datos
	var url =
	    "services.php?modo=obtenerDatosComprobanteVenta"+"&"+
	    "IdComprobante="+IdComprobante+"&"+
	    "esVenta=off"+"&"+
	    "tipoComprobante="+TipoVenta;
	
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	var dtComprobante = xrequest.responseText.split("~");
	var importe       = dtComprobante[0];
	var codcliente    = dtComprobante[1];
	var idcomprobante = dtComprobante[2];
	var nroDocumento  = dtComprobante[3];
	var nroSerie      = dtComprobante[4];
	var importeletras = t_convertirNumLetras(importe);
	importeletras     = importeletras.toUpperCase();
	
	//imprime pdf
	var url=
	    "modulos/fpdf/imprimir_"+TipoVenta+"_tpv.php?"+
	    "nro"+TipoVenta+"="+nroDocumento+"&"+
	    "totaletras="+importeletras+"&"+
	    "codcliente="+codcliente+"&"+
	    "nroSerie="+nroSerie+"&"+
	    "nombreusuario="+Local.nombreDependiente+"&"+
	    "idcomprobante="+IdComprobante;
	
	location.href=url;
	break;

    default:
	return false;
    }		
}

function t_RecuperaTicketAbonos(IdComprobante){

    var res = Raw_t_RecuperaTicketAbonos(IdComprobante); 
    top.TicketFinal = window.open(EncapsrTextoParaImprimir(res.text_data),
				  "Consola Ticket",
				  "width=340,height=460,scrollbars=1,resizable=1,dependent=yes","text/plain");	
}

function t_RecuperaTicketAbonosCliente(){

    var res = Raw_t_RecuperaTicketAbonosCliente();    
    top.TicketFinal = window.open(EncapsrTextoParaImprimir(res.text_data),
				  "Consola Ticket",
				  "width=340,height=390,scrollbars=1,resizable=1,dependent=yes","text/plain");	
    
}

function t_RecuperaTicketCreditosCliente(){

    var res = Raw_t_RecuperaTicketCreditosCliente();    
    top.TicketFinal = window.open(EncapsrTextoParaImprimir(res.text_data),
				  "Consola Ticket",
				  "width=340,height=390,scrollbars=1,resizable=1,dependent=yes","text/plain");	
    
}

function ReimprimirVentaSuscripcion(){
    var idex          = id("busquedaVentas").selectedItem;
    var IdComprobante = idex.value;
    var res           = obtenerTipoComprobante(IdComprobante);
    var TipoVenta     = res[0];

    //obtenemos datos
    var url =
	"services.php?modo=obtenerDatosComprobanteVenta"+"&"+
	"IdComprobante="+IdComprobante+"&"+
	"esVenta=off"+"&"+
	"tipoComprobante="+TipoVenta;
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var dtComprobante = xrequest.responseText.split("~");
    var importe       = dtComprobante[0];
    var codcliente    = dtComprobante[1];
    var idcomprobante = dtComprobante[2];
    var nroDocumento  = dtComprobante[3];
    var nroSerie      = dtComprobante[4];
    var importeletras = t_convertirNumLetras(importe);
    importeletras     = importeletras.toUpperCase();

    //imprime pdf
    var url=
	"modulos/fpdf/imprimir_suscripcion_tpv.php?"+
	"nro="+nroDocumento+"&"+
	"totaletras="+importeletras+"&"+
	"codcliente="+codcliente+"&"+
	"nroSerie="+nroSerie+"&"+
	"nombreusuario="+Local.nombreDependiente+"&"+
	"idcomprobante="+IdComprobante;
    
    location.href=url;
}

function Raw_t_RecuperaTicket(IdComprobante,TipoVenta) {

    var ticket          = new Ticket();
    var newDetalles 	= t_BuscarDetallesVentaAntiguos(IdComprobante);
    var newGlobal 	= t_BuscaGlobalesFactura(IdComprobante);

    ticket.SetModoRemoto( IdComprobante, newGlobal, newDetalles );	
    ticket.setDependiente( newGlobal.Dependiente );
    ticket.setEntregado( newGlobal.DineroEntregado );	
    ticket.SetModoPago( newGlobal.ModoDePago );
    ticket.SetAlfaNumFactura( newGlobal.serie + "-" + newGlobal.num );

    var res = new Object();	
    if(TipoVenta=='Ticket'){
        res.text_data = ticket.generaTextTicket();
    }
    else{
        res.text_data = ticket.generaTextTicketPreVenta();
    }
    return res;
}

function Raw_t_RecuperaTicketAbonos(IdComprobante) {

    var ticket          = new Ticket();
    var newDetalles 	= t_BuscarDetallesVentaAntiguos(IdComprobante);
    var newGlobal 	= t_BuscaGlobalesFactura(IdComprobante);

    ticket.SetModoRemoto( IdComprobante, newGlobal, newDetalles );	
    ticket.setDependiente( newGlobal.Dependiente );
    ticket.setEntregado( newGlobal.DineroEntregado );	
    ticket.SetModoPago( newGlobal.ModoDePago );
    ticket.SetPendiente( newGlobal.pendiente );
    ticket.SetCliente( newGlobal.nombreCliente );
    ticket.SetComprobante( newGlobal.Comprobante );
    ticket.SetNroComprobante( newGlobal.NroComprobante );
    ticket.SetAlfaNumFactura( newGlobal.serie + "-" + newGlobal.num );

    var res = new Object();	
    res.text_data = ticket.generaTextTicketAbono();
    return res;
}


function Raw_t_RecuperaTicketAbonosCliente() {

    var ticket          = new Ticket();

    var res = new Object();	
    res.text_data = ticket.generaTextTicketAbonoCliente();
    return res;
}

function Raw_t_RecuperaTicketCreditosCliente() {

    var ticket          = new Ticket();

    var res = new Object();	
    res.text_data = ticket.generaTextTicketCreditoCliente();
    return res;
}


/* Carga de datos del ticket */

var GlobalNewTicket = new Object();

function CargaVenta(item,vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,nombreCliente,NumeroDocumento,TipoDocumento){

	GlobalNewTicket.Dependiente     = vendedor;
	GlobalNewTicket.serie 		= serie;
	GlobalNewTicket.num 		= num;
	GlobalNewTicket.fecha 		= fecha;
	GlobalNewTicket.total 		= total;
	GlobalNewTicket.pendiente 	= pendiente;
	GlobalNewTicket.estado 		= estado;
	GlobalNewTicket.IdComprobante	= IdComprobante;
	GlobalNewTicket.Comprobante	= TipoDocumento;
	GlobalNewTicket.NroComprobante	= NumeroDocumento;
	GlobalNewTicket.nombreCliente 	= nombreCliente;	
	GlobalNewTicket.DineroEntregado = 0;//TODO
	GlobalNewTicket.ModoDePago 	= 0;//TODO
}

function t_BuscaGlobalesFactura(IdComprobante) {
    GlobalNewTicket = new Object();
    RawBuscarVentas("","","","","","","","","","","",IdComprobante,true,false,"todos",'todos','todos','todos',CargaVenta);

    return GlobalNewTicket;
}


/* Cargamos los datos de lineas del ticket */

var lineasTicketSombra = new Array();

function ColeccionarDetallesTicket(CodBar,Nombre,Talla,Color,Unidades,Descuento,PV,
				   Codigo,Lab,Marca,Serie,Lote,Vence,
				   Referencia,Precio,Servicio,MProducto,Menudeo,Cont,
				   UnidxCont,Unid,IdComprobanteDet,numitem,IdPedidoDet,
				   Costo){
	var prod = new Object();
	prod.referencia = Referencia;
	prod.nombre 	= CodBar+'  '+Nombre+' '+' '+Marca+' '+Color+' '+Talla+' '+Lab;
	prod.talla 	= Talla;
	prod.color 	= Color;
	prod.marca 	= Marca;
	prod.lab 	= Lab;
	prod.unidades 	= Unidades;
	prod.unid 	= Unid;
	prod.descuento 	= Descuento;
	prod.pvd 	= Precio;
	prod.concepto 	= Nombre;
	prod.idsubsidiario = 0;
	prod.codigo 	 = Referencia;
	prod.codigobarra = CodBar;

	lineasTicketSombra[ lineasTicketSombra.length ] =  prod;	
}

function t_BuscarDetallesVentaAntiguos(IdComprobante){
	lineasTicketSombra = new Array();
	RawBuscarDetallesVenta(IdComprobante, ColeccionarDetallesTicket);
	return lineasTicketSombra;
}


/* IMPLEMENTACION TICKET */


/* Clase que representa un ticket*/

function Ticket(){
	/* si debe cargar los datos desde detallesSombra o desde tic */
	this.esTicketRemoto  = false;
	this.numeroserie     = 0;
        //this.aportacionimpuestos = 0;
	this.DineroEntregado = 0;
	this.dependiente     = "";
	this.TotalBase       = 0;
	
	/* Al registrar un producto, añadimos aqui los datos a enviar al server
	 relativos a una fila de ticket*/
	this.datos_post_productos = "";
	this.datos_text_productos = "";
	this.cr = "\n";
	this.tab = "\t";
	
	/* LocalSombra es una indireccion de los datos del local, de modo que pueda ser
	  ..o bien el local real en el que estamos, y en el que trabajamos en vivo.
	  O bien un local "fantasma", el del ticket que estamos imprimiendo que 
	  podria ser en el futuro un local distinto del actual, pero que en cualquier 
	  caso tendra algunos datos distintos.
	  
	  Esto facilita el imprimir tickets antiguos.	
	 */
	this.LocalSombra = Local;
	
	/* como en localsombra.. */	

	this.LocalGlobal = Global;	
}

Ticket.prototype.SetModoRemoto = function ( IdComprobante, newGlobal, newDetalles ){

         for (prop in newGlobal)
         {
	     this.LocalGlobal[prop] = newGlobal[prop];
	 }

	this.detallesSombra = newDetalles;
	this.esTicketRemoto = true;
}
	
Ticket.prototype.SetPendiente = function (newpendiente){
	this.pendiente = newpendiente;	
}

Ticket.prototype.getPendiente = function(){
	return this.pendiente;
}

Ticket.prototype.SetCliente = function (newcliente){
	this.cliente = newcliente;	
}

Ticket.prototype.getCliente = function (){
        return this.cliente;	
}


Ticket.prototype.SetComprobante = function (newcomprobante){
	this.comprobante = newcomprobante;	
}

Ticket.prototype.getComprobante = function (){
        return this.comprobante;	
}

Ticket.prototype.SetNroComprobante = function (newnrocomprobante){
	this.nroComprobante = newnrocomprobante;	
}

Ticket.prototype.getNroComprobante = function (){
        return this.nroComprobante;	
}
	
Ticket.prototype.SetModoPago = function (newmodopago){
	this.modopago = newmodopago;	
}

Ticket.prototype.getEntregado = function(){
	return this.DineroEntregado;
}

//NOTA: para forzar numero correcto de factura 
Ticket.prototype.SetAlfaNumFactura = function(alfanum){
	this.alfanumFactura = alfanum;
}

function getSpaces(num){
	var salida="";
	for(t=0;t<num;t++){
		salida += " ";
	}
	return salida;
}
function preparaNombreTicket(xnomb,xcb){
    return xnomb.replace(xcb, "");
}
function preparaCadena(cadena,tline,crl){
    var ncaract = cadena.length;
    var t_linea="";
    var nline   = 1;
    //var tline   = 32;
    var iline   = 0;
    var fline   = tline;
    if (ncaract > tline)
	nline = ((ncaract-(ncaract%tline))/tline)+1;

    for (var t=0;t<nline;t++) {    
	t_linea += trim( cadena.substring(iline,fline) ) +" " + crl;			
	iline   += tline;
	fline   += tline;
    }
    return t_linea;
}

function preparaCadenaNombre(cadena,tline,crl){
    var ncaract = cadena.length;
    var t_linea="";
    var nline   = 1;
    var iline   = 0;
    var fline   = tline;
    if (ncaract > tline)
	nline = ((ncaract-(ncaract%tline))/tline)+1;

    for (var t=0;t<nline;t++) {    
	t_linea += trim( cadena.substring(iline,fline) ) +" " + crl;			
	iline   += tline;
	fline   += tline;
    }
    return t_linea;
}

// Función modulo, regresa el residuo de una división 
function mod(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  parteEnt = Math.floor(resDiv);            // Obtiene la parte Entera de resDiv 
  parteFrac = resDiv - parteEnt ;      // Obtiene la parte Fraccionaria de la división
  //modulo = parteFrac * divisor;  // Regresa la parte fraccionaria * la división (modulo) 
  modulo = Math.round(parteFrac * divisor)
  return modulo; 
} 
// Fin de función mod

// Función ObtenerParteEntDiv, regresa la parte entera de una división
function ObtenerParteEntDiv(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  parteEntDiv = Math.floor(resDiv);
  return parteEntDiv; 
} 
// Fin de función ObtenerParteEntDiv

// function fraction_part, regresa la parte Fraccionaria de una cantidad
function fraction_part(dividendo , divisor) 
{ 
  resDiv = dividendo / divisor ;  
  f_part = Math.floor(resDiv); 
  return f_part; 
} 
// Fin de función fraction_part

// function string_literal conversion is the core of this program 
// converts numbers to spanish strings, handling the general special 
// cases in spanish language. 
function string_literal_conversion(number) 
{   
   // first, divide your number in hundreds, tens and units, cascadig 
   // trough subsequent divisions, using the modulus of each division 
   // for the next. 

   centenas = ObtenerParteEntDiv(number, 100); 
   number = mod(number, 100); 
   decenas = ObtenerParteEntDiv(number, 10); 
   number = mod(number, 10); 

   unidades = ObtenerParteEntDiv(number, 1); 
   number = mod(number, 1);  
   string_hundreds="";
   string_tens="";
   string_units="";
   
   // cascade trough hundreds. This will convert the hundreds part to 
   // their corresponding string in spanish.
   if(centenas == 1){
      string_hundreds = "ciento ";
   } 
   if(centenas == 2){
      string_hundreds = "doscientos ";
   }
   if(centenas == 3){
      string_hundreds = "trescientos ";
   } 
   if(centenas == 4){
      string_hundreds = "cuatrocientos ";
   } 
   if(centenas == 5){
      string_hundreds = "quinientos ";
   } 
   if(centenas == 6){
      string_hundreds = "seiscientos ";
   } 
   if(centenas == 7){
      string_hundreds = "setecientos ";
   } 
   if(centenas == 8){
      string_hundreds = "ochocientos ";
   } 
   if(centenas == 9){
      string_hundreds = "novecientos ";
   } 
 // end switch hundreds 

   // casgade trough tens. This will convert the tens part to corresponding 
   // strings in spanish. Note, however that the strings between 11 and 19 
   // are all special cases. Also 21-29 is a special case in spanish. 
   if(decenas == 1){
	   
      //Special case, depends on units for each conversion
      if(unidades == 1){
         string_tens = "once";
      }
      if(unidades == 2){
         string_tens = "doce";
      }
      if(unidades == 3){
         string_tens = "trece";
      }
      if(unidades == 4){
         string_tens = "catorce";
      }
      if(unidades == 5){
         string_tens = "quince";
      }
      if(unidades == 6){
         string_tens = "dieciseis";
      }
      if(unidades == 7){
         string_tens = "diecisiete";
      }
      if(unidades == 8){
         string_tens = "dieciocho";
      }
      if(unidades == 9){
         string_tens = "diecinueve";
      }
   } 
   //alert("STRING_TENS ="+string_tens);
   
   if(decenas == 2){
      string_tens = "veinti";
   }
   if(decenas == 3){
      string_tens = "treinta";
   }
   if(decenas == 4){
      string_tens = "cuarenta";
   }
   if(decenas == 5){
      string_tens = "cincuenta";
   }
   if(decenas == 6){
      string_tens = "sesenta";
   }
   if(decenas == 7){
      string_tens = "setenta";
   }
   if(decenas == 8){
      string_tens = "ochenta";
   }
   if(decenas == 9){
      string_tens = "noventa";
   }
    // Fin of swicth decenas

   // cascades trough units, This will convert the units part to corresponding 
   // strings in spanish. Note however that a check is being made to see wether 
   // the special cases 11-19 were used. In that case, the whole conversion of 
   // individual units is ignored since it was already made in the tens cascade. 
   if (decenas == 1) 
   { 
      string_units="";  
	  // empties the units check, since it has alredy been handled on the tens switch 
   } 
   else 
   { 
      if(unidades == 1){
         string_units = "un";
      }
      if(unidades == 2){
         string_units = "dos";
      }
      if(unidades == 3){
         string_units = "tres";
      }
      if(unidades == 4){
         string_units = "cuatro";
      }
      if(unidades == 5){
         string_units = "cinco";
      }
      if(unidades == 6){
         string_units = "seis";
      }
      if(unidades == 7){
         string_units = "siete";
      }
      if(unidades == 8){
         string_units = "ocho";
      }
      if(unidades == 9){
         string_units = "nueve";
      }
       // end switch units 
   } // end if-then-else 
//final special cases. This conditions will handle the special cases which 
//are not as general as the ones in the cascades. Basically four: 

// when you've got 100, you dont' say 'ciento' you say 'cien' 
// 'ciento' is used only for [101 >= number > 199] 
if (centenas == 1 && decenas == 0 && unidades == 0) 
{ 
   string_hundreds = "cien " ; 
}  
// when you've got 10, you don't say any of the 11-19 special 
// cases.. just say 'diez' 
if (decenas == 1 && unidades ==0) 
{ 
   string_tens = "diez " ; 
} 
// when you've got 20, you don't say 'veinti', which is used 
// only for [21 >= number > 29] 
if (decenas == 2 && unidades ==0) 
{ 
  string_tens = "veinte " ; 
} 
// for numbers >= 30, you don't use a single word such as veintiuno 
// (twenty one), you must add 'y' (and), and use two words. v.gr 31 
// 'treinta y uno' (thirty and one) 
if (decenas >=3 && unidades >=1) 
{ 
   string_tens = string_tens+" y "; 
} 
// this line gathers all the hundreds, tens and units into the final string 
// and returns it as the function value.
final_string = string_hundreds+string_tens+string_units;
return final_string ; 
} 
//end of function string_literal_conversion 
// handle some external special cases. Specially the millions, thousands 
// and hundreds descriptors. Since the same rules apply to all number triads 
// descriptions are handled outside the string conversion function, so it can 
// be re used for each triad. 


function t_convertirNumLetras(number)
{
    var cad, millions_final_string, thousands_final_string, centenas_final_string, descriptor;
    //number = number_format (number, 2);
    var number1=number.toString();
    //settype (number, "integer");
    var cent = number1.split(".");   
    var centavos = cent[1];
    //Mind Mod
    var number=cent[0];
    if (centavos == 0 || centavos == undefined)
    {
	centavos = "00";
    }
    if (number == 0 || number == "") 
    { // if amount = 0, then forget all about conversions, 
	centenas_final_string=" cero "; // amount is zero (cero). handle it externally, to 
	// function breakdown 
    } 
    else 
    { 
	var millions  = ObtenerParteEntDiv(number, 1000000); // first, send the millions to the string 
	number = mod(number, 1000000);           // conversion function 
	
	if (millions != 0)
	{                      
	    // This condition handles the plural case 
            if (millions == 1) 
            {              // if only 1, use 'millon' (million). if 
		descriptor= " millon ";  // > than 1, use 'millones' (millions) as 
            } 
            else 
            {                           // a descriptor for this triad. 
		descriptor = " millones "; 
            } 
	} 
	else 
	{    
            descriptor = " ";                 // if 0 million then use no descriptor. 
	} 
	millions_final_string = string_literal_conversion(millions)+descriptor; 
	thousands = ObtenerParteEntDiv(number, 1000);  // now, send the thousands to the string 
        number = mod(number, 1000);            // conversion function. 
	//print "Th:".thousands;
	if (thousands != 1) 
	{                   // This condition eliminates the descriptor 
            thousands_final_string =string_literal_conversion(thousands) + " mil "; 
	    //  descriptor = " mil ";          // if there are no thousands on the amount 
	} 
	if (thousands == 1)
	{
            thousands_final_string = " mil "; 
	}
	if (thousands < 1) 
	{ 
            thousands_final_string = " "; 
	} 
	// this will handle numbers between 1 and 999 which 
	// need no descriptor whatsoever. 
	centenas  = number;                     
	centenas_final_string = string_literal_conversion(centenas) ; 
    } //end if (number ==0) 

    /*if (ereg("un",centenas_final_string))
      {
      centenas_final_string = ereg_replace("","o",centenas_final_string); 
      }*/
    //finally, print the output. 

    /* Concatena los millones, miles y cientos*/
    cad = millions_final_string+thousands_final_string+centenas_final_string; 
    /* Convierte la cadena a Mayúsculas*/
    cad = cad.toUpperCase();       
    if (centavos.length>2)
    {  
	if(centavos.substring(2,3)>= 5){
            centavos = centavos.substring(0,1)+(parseInt(centavos.substring(1,2))+1).toString();
	}   else{
	    
            centavos = centavos.substring(0,1);
	}
    }

    /* Concatena a los centavos la cadena "/100" */
    if (centavos.length==1)
    {
	centavos = centavos+"0";
    }
    centavos = centavos+ "/100"; 


    /* Asigna el tipo de moneda, para 1 = PESO, para distinto de 1 = PESOS*/
    moneda = (number == 1)? cMoneda[1]['T']:cMoneda[1]['TP'];
/*
    if (number == 1)
    {
	moneda = " SOL ";  
    }
    else
    {
	moneda = " SOLES  ";  
    }
*/
    /* Regresa el número en cadena entre paréntesis y con tipo de moneda y la fase M.N.*/
    //Mind Mod, si se deja MIL pesos y se utiliza esta función para imprimir documentos
    //de caracter legal, dejar solo MIL es incorrecto, para evitar fraudes se debe de poner UM MIL pesos
    if(cad == '  MIL ')
    {
	cad=' UN MIL ';
    }
    // alert( "FINAL="+cad+moneda+centavos+" M.N.");
    return cad+" CON "+centavos+" "+moneda;
}




/* Preparamos la cadena de texto plano que representa el ticket */
Ticket.prototype.generaTextTicket = function(){
	var cambio;
	var salida = "";
	var cr = this.cr;

        var nombrecliente = usuarios[UsuarioSeleccionado].nombre;
        //var dnicliente    = usuarios[UsuarioSeleccionado].ruc;
        //var dnicliente    = ( dnicliente == '' )? '...': dnicliente;  
 
	//var len = new String(Local.Negocio).length;
        var len = new String(Local.promoMensaje).length;
	pad = len - 10;
	len = new String(Local.promoMensaje).length;
        pad = len - 10;

        salida += "****** " + Local.Negocio +  " ******" + cr;
	//salida += "" + Local.promoMensaje + "" + cr;
        salida += "" + this.Colum( new Array( Local.promoMensaje.slice(0,35) ));
        if(  Local.promoMensaje.length >35 )
	  salida += "" + this.Colum( new Array( Local.promoMensaje.slice(35,65) ));
        if(  Local.promoMensaje.length >65 )
	   salida += "" + this.Colum( new Array( Local.promoMensaje.slice(65,95) ));

        if(  Local.NegocioWeb != '')
	    salida += "" + Local.NegocioWeb+ "" + cr+ cr;
        else
	    salida += "" + cr;

        //salida += this.Linea();
	salida += "" + this.Colum( new Array( po_tienda , this.LocalSombra.nombretienda)) + "" ;
        if( Local.NegocioDireccion != '' ){
	    salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(0,35) ));
	    if(  Local.NegocioDireccion.length >35 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(35,65) ));
	    if(  Local.NegocioDireccion.length >65 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(65,95) ));
	}
	salida += "" + po_telf +' '+ Local.NegocioTelef + "" + cr+ cr ;
        salida += this.Linea();
	salida += "" + this.Colum( new Array( po_boucherde ));
        salida += this.Linea();
	salida += this.Colum( new Array( po_numtic , this.alfanumFactura ));	
        salida += po_ticketcliente+getSpaces(1)+nombrecliente+cr;
	salida += this.TexModoTicket(ModoDeTicket)+cr;	
        salida += this.Linea();	
        salida += this.Colum( new Array(po_unid,po_precio,po_descuento,
							       po_Total));	
	salida += this.Linea();
	salida += this.GenerarTextoProductos();
	salida += this.Linea();
	salida += this.Colum( new Array(po_TOTAL,"", formatDinero(this.TotalBase)) );
	salida += this.Colum( new Array(po_Entregado,"", formatDinero(this.getEntregado())) );
	salida += this.Colum( new Array(po_Cambio,"", formatDinero(this.genCambio())) );
        salida += this.Colum( new Array(po_Pendiente,"",formatDinero(Local.AbonoClientePendiente)));

	//salida += this.Linea();
        //salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );
	
	var modopago = this.modopago;
	if (modopago>1)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	var po_15diaslimite_resultante = new String( po_15diaslimite );
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace(/\\n/,cr);			
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace("%d",this.LocalSombra.diasLimiteDevolucion);	
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  this.dependiente) );
	salida += this.Fecha() 		+ cr;
	salida += po_15diaslimite_resultante 	+ cr;
	//salida += cr + this.LocalSombra.motd 		+ cr;
        salida += cr + this.Colum( new Array( this.LocalSombra.motd.slice(0,35) ));
        if(  this.LocalSombra.motd.length >35 )
	  salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(35,65) ));
        if(  this.LocalSombra.motd.length >65 )
	   salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(65,95) ));

	return salida;

}

/* Preparamos la cadena de texto plano que representa el ticket */
Ticket.prototype.generaTextTicketAbono = function(){
	var cambio;
	var salida = "";
	var cr = this.cr;

        var xnombrecliente = this.getCliente().split(" : ");//usuarios[UsuarioSeleccionado].nombre;
        var nombrecliente  = xnombrecliente[1];
        //var dnicliente    = usuarios[UsuarioSeleccionado].ruc;
        //var dnicliente    = ( dnicliente == '' )? '...': dnicliente;  
	//var len = new String(Local.Negocio).length;

        var len = new String(Local.promoMensaje).length;
	pad = len - 10;
	len = new String(Local.promoMensaje).length;
        pad = len - 20;


        salida += "****** " + Local.Negocio +  " ******" + cr;
	//salida += "" + Local.promoMensaje + "" + cr;
        salida += "" + this.Colum( new Array( Local.promoMensaje.slice(0,35) ));
        if(  Local.promoMensaje.length >35 )
	  salida += "" + this.Colum( new Array( Local.promoMensaje.slice(35,65) ));
        if(  Local.promoMensaje.length >65 )
	   salida += "" + this.Colum( new Array( Local.promoMensaje.slice(65,95) ));

        if(  Local.NegocioWeb != '')
	    salida += "" + Local.NegocioWeb+ "" + cr+ cr;
        else
	    salida += "" + cr;

        //salida += this.Linea();
	salida += "" + this.Colum( new Array( po_tienda , this.LocalSombra.nombretienda)) + "" ;
        if( Local.NegocioDireccion != '' ){
	    salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(0,35) ));
	    if(  Local.NegocioDireccion.length >35 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(35,65) ));
	    if(  Local.NegocioDireccion.length >65 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(65,95) ));
        }
	salida += "" + po_telf +' '+ Local.NegocioTelef + "" + cr+ cr ;
        salida += this.Linea();
	salida += "" + this.Colum( new Array( po_boucherde ));
        salida += this.Linea();
        salida += this.Colum( new Array( po_comprobante,this.getComprobante()+' '+this.getNroComprobante()));
	salida += this.Colum( new Array( po_numtic , this.alfanumFactura ));	
        salida += po_ticketcliente+getSpaces(1)+nombrecliente+cr;
	salida += this.TexModoTicket(ModoDeTicket)+cr;		

        //Formato Ticket
        if( Local.ImprimirFormatoTicket )
        {
            salida += this.Linea();
	    salida += this.Colum( new Array(po_unid,po_precio,po_descuento,
								   po_Total));	

	    salida += this.Linea();
            salida += this.GenerarTextoProductos();
	} else 
            var nosalida = this.GenerarTextoProductos();
	salida += this.Linea();
	salida += this.Colum( new Array(po_TOTAL,"", formatDinero( cMontoComprobante )) );

        if( Local.AbonoImporte >0 ){
	    salida += this.Colum( new Array(po_TOTALDEUDA,"", formatDinero( Local.AbonoDebe )) );
	    salida += this.Colum( new Array(po_Abonado,"", formatDinero( Local.AbonoImporte )) );
	}
	//salida += this.Colum( new Array(po_Cambio,"", formatDinero(this.genCambio())) );
        salida += this.Colum( new Array(po_Pendiente,"",formatDinero( Local.AbonoPendiente )));

	//salida += this.Linea();
        //salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );
	
	var modopago = this.modopago;
	if (modopago>1)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	var po_15diaslimite_resultante = new String( po_15diaslimite );
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace(/\\n/,cr);			
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace("%d",this.LocalSombra.diasLimiteDevolucion);	
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  this.dependiente) );
	salida += this.Fecha() + cr;
        //salida += po_15diaslimite_resultante 	+ cr;
	//salida += cr + this.LocalSombra.motd + cr;
        salida += cr + this.Colum( new Array( this.LocalSombra.motd.slice(0,35) ));
        if(  this.LocalSombra.motd.length >35 )
	  salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(35,65) ));
        if(  this.LocalSombra.motd.length >65 )
	   salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(65,95) ));


        //Formato Ticket
        Local.ImprimirFormatoTicket = false;
        Local.AbonoImporte          = 0;
        Local.AbonoDebe             = 0;
        Local.AbonoPendiente        = 0;
	return salida;

}

/* Preparamos la cadena de texto plano que representa el ticket */
Ticket.prototype.generaTextTicketAbonoCliente = function(){
	var cambio;
	var salida = "";
	var cr = this.cr;

        var nombrecliente  = usuarios[ preSeleccionadoCliente ].nombre; 
        //var dnicliente    = usuarios[UsuarioSeleccionado].ruc;
        //var dnicliente    = ( dnicliente == '' )? '...': dnicliente;  
 
	//var len = new String(Local.Negocio).length;
        var len = new String(Local.promoMensaje).length;
	pad = len - 10;
	len = new String(Local.promoMensaje).length;
        pad = len - 20;


        salida += "****** " + Local.Negocio +  " ******" + cr;
	//salida += "" + Local.promoMensaje + "" + cr;
        salida += "" + this.Colum( new Array( Local.promoMensaje.slice(0,35) ));
        if(  Local.promoMensaje.length >35 )
	  salida += "" + this.Colum( new Array( Local.promoMensaje.slice(35,65) ));
        if(  Local.promoMensaje.length >65 )
	   salida += "" + this.Colum( new Array( Local.promoMensaje.slice(65,95) ));

        if(  Local.NegocioWeb != '')
	    salida += "" + Local.NegocioWeb+ "" + cr+ cr;
        else
	    salida += "" + cr;

        //salida += this.Linea();
	salida += "" + this.Colum( new Array( po_tienda , this.LocalSombra.nombretienda)) + "" ;
        if( Local.NegocioDireccion != '' ){
	    salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(0,35) ));
	    if(  Local.NegocioDireccion.length >35 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(35,65) ));
	    if(  Local.NegocioDireccion.length >65 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(65,95) ));
	}
	salida += "" + po_telf +' '+ Local.NegocioTelef + "" + cr+ cr ;
        salida += this.Linea();
	salida += "" + this.Colum( new Array( po_boucherde ));
        salida += this.Linea();
        salida += po_ticketcliente+getSpaces(1)+nombrecliente+cr;
	salida += this.TexModoTicket(ModoDeTicket);		
	salida += this.Linea();
        salida += this.Colum( new Array(po_TOTALDEUDA,"", formatDinero( Local.AbonoClienteDebe )) );
        salida += this.Colum( new Array(po_Abonado,"", formatDinero( Local.AbonoClienteImporte )) );
        salida += this.Colum( new Array(po_Pendiente,"", formatDinero( Local.AbonoClientePendiente )) );

	//salida += this.Linea();
        //salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );
	
	var modopago = this.modopago;
	if (modopago>1)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  Local.nombreDependiente ) );
	salida += this.Fecha() + cr;
	//salida += cr + this.LocalSombra.motd + cr;
        salida += cr + this.Colum( new Array( this.LocalSombra.motd.slice(0,35) ));
        if(  this.LocalSombra.motd.length >35 )
	  salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(35,65) ));
        if(  this.LocalSombra.motd.length >65 )
	   salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(65,95) ));

        //Formato Ticket
        Local.AbonoClienteImporte   = 0;
        Local.AbonoClientePendiente = 0;
        Local.AbonoClienteDebe      = 0;


	return salida;

}

/* Preparamos la cadena de texto plano que representa el ticket */
Ticket.prototype.generaTextTicketCreditoCliente = function(){
	var cambio;
	var salida = "";
	var cr = this.cr;

        var nombrecliente  = usuarios[ preSeleccionadoCliente ].nombre; 
 
	//var len = new String(Local.Negocio).length;
        var len = new String(Local.promoMensaje).length;
	pad = len - 10;
	len = new String(Local.promoMensaje).length;
        pad = len - 20;


        salida += "****** " + Local.Negocio +  " ******" + cr;
	//salida += "" + Local.promoMensaje + "" + cr;
        salida += "" + this.Colum( new Array( Local.promoMensaje.slice(0,35) ));
        if(  Local.promoMensaje.length >35 )
	  salida += "" + this.Colum( new Array( Local.promoMensaje.slice(35,65) ));
        if(  Local.promoMensaje.length >65 )
	   salida += "" + this.Colum( new Array( Local.promoMensaje.slice(65,95) ));

        if(  Local.NegocioWeb != '')
	    salida += "" + Local.NegocioWeb+ "" + cr+ cr;
        else
	    salida += "" + cr;

        //salida += this.Linea();
	salida += "" + this.Colum( new Array( po_tienda , this.LocalSombra.nombretienda)) + "" ;
        if( Local.NegocioDireccion != '' ){
	    salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(0,35) ));
	    if(  Local.NegocioDireccion.length >35 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(35,65) ));
	    if(  Local.NegocioDireccion.length >65 )
		salida += "" + this.Colum( new Array( Local.NegocioDireccion.slice(65,95) ));
	}
	salida += "" + po_telf +' '+ Local.NegocioTelef + "" + cr+ cr ;
        salida += this.Linea();
	salida += "" + this.Colum( new Array( po_boucherdecredito ));
        salida += this.Linea();
        salida += po_ticketcliente+getSpaces(1)+nombrecliente+cr;
	salida += this.TexModoTicket(ModoDeTicket);		
        salida += this.Linea();
    salida += this.Colum( new Array( po_CreditoCliente,"",formatDinero( Local.CreditoClienteImporte )) );
        salida += this.Colum( new Array( po_Credito,"",formatDinero( Local.CreditoClienteEntregado )) );
        salida += this.Colum( new Array(po_TOTALCREDITO,"",formatDinero( Local.CreditoClienteTotal )) );


	//salida += this.Linea();
        //salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );
	
	var modopago = this.modopago;
	if (modopago>1)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  Local.nombreDependiente ) );
	salida += this.Fecha() + cr;
	//salida += cr + this.LocalSombra.motd + cr;
        salida += cr + this.Colum( new Array( this.LocalSombra.motd.slice(0,35) ));
        if(  this.LocalSombra.motd.length >35 )
	  salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(35,65) ));
        if(  this.LocalSombra.motd.length >65 )
	   salida += "" + this.Colum( new Array( this.LocalSombra.motd.slice(65,95) ));

        //Formato Ticket
        Local.CreditoClienteImporte   = 0;
        Local.CreditoClienteEntregado = 0;
        Local.CreditoClienteTotal     = 0;

        return salida;
}

Ticket.prototype.generaTextTicketPreVenta = function(){

        var fecha= new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth()+1;
        if (mes<10)
            mes = "0"+mes;
        var anio = fecha.getFullYear();
        var cadenafecha = dia +"/"+mes+"/"+anio;
        var nombrecliente = usuarios[UsuarioSeleccionado].nombre;
        var dnicliente    = usuarios[UsuarioSeleccionado].ruc;
        var dnicliente    = ( dnicliente == '' )? '...': dnicliente;  
        var cambio;
        var salida = "";
        var cr = this.cr;

        var len = new String(Local.Negocio).length;
        pad = len - 19;

        salida += cr+cr+cr+cr+cr+cr+cr+cr;
        salida += getSpaces(70)+this.alfanumFactura+cr+cr+cr;
        salida += getSpaces(8)+nombrecliente+cr+cr;
        salida += getSpaces(8)+preparaCadena(dnicliente,13)+getSpaces(9)+preparaCadena(dia+"     "+mes+"     "+anio,19)+cr+cr+cr;
        salida += this.GenerarTextoProductos()+cr+cr;
        salida += getSpaces(8)+t_convertirNumLetras(formatDinero(this.TotalBase))+getSpaces(11)+cr+cr+cr+cr;
        salida += getSpaces(100)+"****"+formatDinero(this.TotalBase);

        cambio = this.genCambio();


        var modopago = this.modopago;
        if (modopago>1)
            salida = salida + po_mododepago + " " + modospago[modopago] + cr;

        var po_15diaslimite_resultante = new String( po_15diaslimite );
        po_15diaslimite_resultante = po_15diaslimite_resultante.replace(/\\n/,cr);			
        po_15diaslimite_resultante = po_15diaslimite_resultante.replace("%d",this.LocalSombra.diasLimiteDevolucion);	
    return salida;
}


/* Construimos el numero de ticket, consultando la serie con el servidor */
Ticket.prototype.IniciaNumeroDeSerie = function (Modo){
	/* Actualiza el numero de serie de ticket desde el servidor, solo por si acaso */
	//NOTA: se usa para construir alfanumFactura
	//NOTA: se actualiza al siguiente numero con cada impresion
	this.LocalSombra.numeroDeSerie = this.TraerDelServidorNumeroDeSerie();

	//Local.numeroDeSerie: numero actual calculado por la tpv
	//this.numeroserie: dato que se enviara al servidor			
        //this.numeroserie  = Local.numeroDeSerie;

	/* NOTA:*/
	/* Construye "numero" de factura completa, como mezcla num y letras es 'alfanumfac' */
	/* Se tiene en cuenta el tipo de ticket, normal, cesion, etc.. */
	//NOTA: es el dato que aparecera impreso.
	this.alfanumFactura = this.AlfaNumFac( this.LocalSombra.numeroDeSerie ,Modo);
	
	if (modoPersonalizado){
		/*NOTA:
		En el modo personalizado se permite especificar el proximo numero de serie y 
		de factura. */
		var serieForzada = id("ajusteSerieTicket").value;
		var numeroForzado = id("ajusteNumeroTicket").value;
		
		//No se tiene en cuenta cesion, etc.. todo forzado.
		this.alfanumFactura = serieForzada + "-"+numeroForzado;
		//this.LocalSombra.numeroDeSerie = numeroForzado;	
		Global.fechahoy = id("ajusteFechaTicket").value;
	}
	
	
}

Ticket.prototype.setDependiente = function(nombre){
	this.dependiente = nombre;
}

Ticket.prototype.setEntregado = function (cantidadString){
	this.DineroEntregado = parseFloat(cantidadString);
}

/* Construimos la cadena post que servira para avisar al servidor de los datos del envio */
Ticket.prototype.generaPostData = function(){
	var data = "";
	var crd = "&";
	data += "entrega="          + escape( this.getEntregado()) + crd;
	data += "cambio="           + escape( this.entregaCambio ) + crd;
	data += "dependiente="      + encodeURIComponent( this.dependiente ) + crd;	
	data += "serieticket="      + escape( Local.prefixSerieActiva ) + crd;
	data += "numticket="        + escape( this.numeroserie ) + crd;		
	data += "entrega_efectivo=" + escape( this.entregaEfectivo ) + crd;		
	data += "entrega_bono="     + escape( this.entregaBono ) + crd;			
	data += "entrega_tarjeta="  + escape( this.entregaTarjeta ) + crd;
	data += "entrega_cambio="   + escape( this.entregaCambio ) + crd;
	data += "promocion_id="     + escape( this.promocionID ) + crd;	
	data += "promocion_bono="   + escape( this.promocionBono ) + crd;		
	data += this.datos_post_productos + crd;		
	data += "numlines="         + escape(iticket) + crd;		
	data += "UsuarioSeleccionado=" + UsuarioSeleccionado + crd;
	data += "modopago_id="      + escape( this.modopago ) + crd;	
	return data;
}

/* NOTA: ademas de recoger numero de serie, ajusta this.numeroserie 
*/
Ticket.prototype.TraerDelServidorNumeroDeSerie = function(){
	//numeroSiguienteDeFacturaParaNuestroLocal
	var moticket = ModoDeTicket;
	
	var	url = "services.php?modo=numeroSiguienteDeFacturaParaNuestroLocal&" + "&moticket=" + moticket + "&" + ApendRand();
	var xrequest = new XMLHttpRequest();
	
	xrequest.open("GET",url,false);
	xrequest.send(null);
	
	var resultado    = parseInt(xrequest.responseText);	
	this.numeroserie = resultado;
	return resultado;	
}

Ticket.prototype.sizeOfTab = function(){
	return 8;
}	

Ticket.prototype.Linea = function(){
    return "------------------------------------"  + this.cr;
}


Ticket.prototype.Colum = function(col) {
	var salida = "";
	
	if (!col)	return this.cr;
			
	for(var t=0;t<col.length;t++) {
		c = col[t];
		
		if (!c) c = this.tab;
		
		salida = salida + c;
		
		if ( c.length < this.sizeOfTab() ) {
			salida = salida + this.tab;
		} else {
			salida = salida + " ";
		}
	}	
	return salida + this.cr;		
}

Ticket.prototype.AlfaNumFac = function (Serie,Modo){
	switch( Modo ){
		case "interno":
			this.LocalSombra.prefixSerieActiva = this.LocalSombra.prefixSerieIN;break;
		case "cesion":
			this.LocalSombra.prefixSerieActiva = this.LocalSombra.prefixSerieCS;break;
		default:
		case "venta":		
			this.LocalSombra.prefixSerieActiva =  this.LocalSombra.prefixSerie;break;
	}
	
	return this.LocalSombra.prefixSerieActiva + "-" + Serie;			
}


Ticket.prototype.TexModoTicket = function(Modo){
	
	switch( Modo ){
	case "interno":
	    return po_ticketarreglointerno + this.cr;
	case "cesion":
	    if(Local.Reservar)
		return po_ticketcesionprendareserva + this.cr;
	    else
		return po_ticketcesionprenda + this.cr;
	    
	case "venta":		
	    if(Local.Reservar)
		return po_ticketcesionprendareserva + this.cr;
	    else
		return "";
	}
}

Ticket.prototype.Fecha = function (){
    return "Fecha:"+ this.LocalGlobal.fechahoy + this.cr + 
	   "Hora :"+ calcularFechaActual('hora') + this.cr;
}

Ticket.prototype.pgetIdSubsidiario = function (){
	return this.productoSombra["idsubsidiario"];
}

/* Carga datos desde el ticket presente en el formulario */

Ticket.prototype.ProductoDato = function (key){
	var xdato 	= id(key + this.CodigoProductoSeleccionado);
	if(xdato)
		return xdato.value;
	return false;
}



Ticket.prototype.pgetUnidades = function (){
	return this.productoSombra["unid"];
}

Ticket.prototype.pgetPedidoDet = function (){
	return this.productoSombra["pedidodet"];
}

Ticket.prototype.pgetStatus = function (){
	return this.productoSombra["status"];
}

Ticket.prototype.pgetOferta = function (){
	return this.productoSombra["oferta"];
}

Ticket.prototype.pgetIdProducto = function (){
	return this.productoSombra["idproducto"];
}

Ticket.prototype.pgetCosto = function (){
	return this.productoSombra["costo"];
}

Ticket.prototype.pgetImporte = function (){
	return this.productoSombra["importe"];
}

Ticket.prototype.pgetPrecio = function (){
	return this.productoSombra["precio"];
}

Ticket.prototype.pgetPrecioAlmacen = function (){
	return this.productoSombra["precioalmacen"];
}

Ticket.prototype.pgetDescuento = function (){
	return this.productoSombra["descuento"];
}

Ticket.prototype.pgetImpuesto = function (){
	return this.productoSombra["impuesto"];
}

Ticket.prototype.pgetReferencia = function (){
	return this.productoSombra["referencia"];
}

Ticket.prototype.pgetTalla = function (){
	return this.productoSombra["talla"];
}

Ticket.prototype.pgetColor = function (){
	return this.productoSombra["color"];
}

Ticket.prototype.pgetMarca = function (){
	return this.productoSombra["marca"];
}

Ticket.prototype.pgetLab = function (){
	return this.productoSombra["lab"];
}

Ticket.prototype.pgetUnid = function (){
	return this.productoSombra["unidmedida"];
}

Ticket.prototype.pgetNombre = function (){
	return this.productoSombra["nombre"];
}
Ticket.prototype.pgetConcepto = function (){
	return this.productoSombra["concepto"];
}


Ticket.prototype.genSombraDesdeTic = function(){

/*        datos["unid"] 		= parseInt(this.ProductoDato("tic_unid_"));
        datos["unidmedida"]     = productos[this.CodigoProductoSeleccionado].unid;
        datos["precio"] 	= normalFloat(CleanMoney(this.ProductoDato("tic_precio_")));
        datos["precioalmacen"]  = productos[this.CodigoProductoSeleccionado].pvd;
	datos["importe"] 	= normalFloat(CleanMoney(this.ProductoDato("tic_importe_")));
        datos["costo"]  	= normalFloat(CleanMoney(this.ProductoDato("tic_costo_")));
	datos["descuento"] 	= normalFloat(this.ProductoDato("tic_descuento_"));
	datos["impuesto"] 	= normalFloat(CleanInpuesto( this.ProductoDato("tic_impuesto_") )/100.0);
	datos["referencia"]     = this.ProductoDato("tic_referencia_");
	datos["talla"] 		= this.ProductoDato("tic_talla_");
	datos["color"] 		= this.ProductoDato("tic_color_");
        datos["nombre"] 	= preparaNombreTicket( this.ProductoDato("tic_nombre_"),
						       this.CodigoProductoSeleccionado );
	datos["concepto"] 	= this.ProductoDato("tic_concepto_");
	datos["pedidodet"] 	= this.ProductoDato("tic_pedidodet_");
	datos["idproducto"] 	= this.ProductoDato("tic_idproducto_");
	datos["idsubsidiario"]	= this.ProductoDato("tic_subsidiario_");
	datos["status"]	        = this.ProductoDato("tic_status_");
        datos["oferta"]	        = this.ProductoDato("tic_oferta_");
	datos["codigo"]		= this.CodigoProductoSeleccionado;
	this.productoSombra = datos;
*/


    var datos              = new Array(); 	
    var codigo             = this.CodigoProductoSeleccionado;
    var xnombreticket      = ( productos[codigo].menudeo )? ticket[codigo].producto+' '+
	                                                    ticket[codigo].vdetalle
                                                           :ticket[codigo].producto;
    datos["unid"]          = ticket[codigo].unidades;
    datos["unidmedida"]    = productos[codigo].unid;
    datos["precio"] 	   = normalFloat(CleanMoney(ticket[codigo].precio));
    datos["precioalmacen"] = productos[codigo].pvd;
    datos["importe"] 	   = normalFloat(CleanMoney(ticket[codigo].importe));
    datos["costo"]  	   = normalFloat(CleanMoney(ticket[codigo].costo));
    datos["descuento"] 	   = normalFloat(ticket[codigo].descuento);
    datos["impuesto"] 	   = normalFloat( ticket[codigo].impuesto/100.0 );
    datos["referencia"]    = ticket[codigo].referencia;
    datos["talla"] 	   = ticket[codigo].talla;
    datos["color"] 	   = ticket[codigo].color;
    datos["nombre"] 	   = preparaNombreTicket(xnombreticket ,codigo );
    datos["concepto"] 	   = ticket[codigo].concepto;
    datos["pedidodet"] 	   = ticket[codigo].pedidodet;
    datos["idproducto"]    = ticket[codigo].idproducto;
    datos["idsubsidiario"] = ticket[codigo].idsubsidiario;
    datos["status"]	   = ticket[codigo].cStatus;
    datos["oferta"]	   = ticket[codigo].oferta
    datos["codigo"]	   = codigo;

    this.productoSombra    = datos;
}

Ticket.prototype.genSombraDesdeRemota = function(){
 	var datos = new Array(); 	
  	var prod = this.detallesSombra[this.indiceProductoSombra];

        datos["unid"] 		= prod.unidades;
        datos["unidmedida"]	= prod.unid;
	datos["precio"] 	= prod.pvd;
	datos["descuento"] 	= prod.descuento;
	datos["impuesto"] 	= prod.impuesto;
	datos["referencia"]     = prod.referencia;
	datos["talla"] 		= prod.talla;
	datos["color"] 		= prod.color;
	datos["marca"] 		= prod.marca;
	datos["lab"] 		= prod.lab;
        datos["nombre"] 	= preparaNombreTicket( prod.nombre, prod.codigobarra);
	datos["concepto"] 	= prod.concepto;
	datos["idsubsidiario"]	= prod.idsubsidiario;
	datos["codigo"] 	= prod.codigobarra;//prod.codigobarra
	datos["codigobarra"] 	= prod.codigobarra;
	this.productoSombra     = datos;
}


Ticket.prototype.GenerarTextoProductos = function(){

    var codigo, prod;
    //maximo de productos que podrian encontrarse
    var maxproductos = ( this.esTicketRemoto )? this.detallesSombra.length : iticket;

    this.indiceProductoSombra = 0;
    var agnadidos = new Array();
    for (var t=0;t<maxproductos;t++) {
        if(!this.esTicketRemoto)
            codigo = ticketlist[t];	
        else { 
            this.genSombraDesdeRemota();
            codigo = this.productoSombra["codigo"];
        }

        if ( !agnadidos[codigo]   ) {	
            this.GeneraProducto(codigo,t);
            this.indiceProductoSombra = this.indiceProductoSombra + 1;
            agnadidos[codigo] = 1;		
        } 
	
    }
/**    if(comprobante==0){
        max=31-maxproductos;
        for (var t=0;t<max;t++) {
            this.datos_text_productos+=this.cr;
        }
    }**/

    return this.datos_text_productos;
}

Ticket.prototype.genCambio = function (){

	var cambio =  parseFloat(this.getEntregado()) -  parseFloat(this.TotalBase) ;	
	if (cambio<0)	cambio = 0;
	return cambio;
}
	
Ticket.prototype.GeneraProducto = function(codigo, indiceDeEntrada){
	this.CodigoProductoSeleccionado = codigo;
	
	//Numero de orden en productos ya enviados (0,1,2,3...)
	this.indiceProductoMetido = indiceDeEntrada;
	
	if( !this.esTicketRemoto ){
		var tic =  id( "tic_" + codigo );		
		if (!tic) return; //No esta en cesta de compra...

		//Prepopula la sombra con los datos del tic		
		this.genSombraDesdeTic();
	} else {
		this.genSombraDesdeRemota();
	}
	
	//Lee los datos desde la sombra
	var prod = new Object();
	
	prod.idsubsidiario = this.pgetIdSubsidiario()
	prod.unidades  	   = this.pgetUnidades();
	prod.costo  	   = this.pgetCosto();
        prod.importe  	   = this.pgetImporte();
	prod.pedidodet 	   = this.pgetPedidoDet();
	prod.status 	   = this.pgetStatus();
        prod.oferta 	   = this.pgetOferta();
	prod.idproducto	   = this.pgetIdProducto();
        prod.precio 	   = this.pgetPrecio();
	prod.precioalmacen = this.pgetPrecioAlmacen();		
	prod.descuento 	   = this.pgetDescuento();
	prod.impuesto  	   = this.pgetImpuesto();
	prod.referencia    = this.pgetReferencia();
	prod.talla 	   = this.pgetTalla();
	prod.color 	   = this.pgetColor();
	prod.marca 	   = this.pgetMarca();
	prod.lab 	   = this.pgetLab();
	prod.nombre 	   = this.pgetNombre();
	prod.concepto	   = this.pgetConcepto();
	prod.unid	   = this.pgetUnid();
	prod.codigo	   = codigo;
	
    	//alert("datos lee:\n con:"+prod.pedidodet+",nom:"+prod.nombre);
	this.RawGeneraProducto(prod);
}	

Ticket.prototype.RawGeneraProducto = function(prod){

    var cr             = this.cr;
    var nombreenticket = "";
    var unidmedticket  = "";
    var pvp            = 0;
    var total          = 0;
    nombreenticket     = ( prod.idsubsidiario>0 )? prod.concepto:prod.nombre;

    prod.concepto      = ( prod.concepto != "undefined")? prod.concepto:"";
    pvp  	       = parseFloat(prod.precio);//Impuesto incluido
    total 	       = formatDineroTotal( parseFloat(pvp) * parseFloat(prod.unidades) );

    if (prod.descuento>0) 
        total = parseFloat(parseFloat(total)-(parseFloat(total)*(parseFloat(prod.descuento)/100.0)));

    //Cuanto dinero del que paga el cliente es en concepto de impuestos
    //this.aportacionimpuestos  += parseFloat(total) * prod.impuesto;

    //Total del ticket
    total           = Math.round(total*10)/10;
    this.TotalBase  = parseFloat(this.TotalBase) + parseFloat(total);
    var salida      = "";
    var stringcore  = 36;

    //*** Listado de productos y detalles ticket
    //
    //*** Formato Basico Clasicc
    // [  Unid - PV - DCTO - Importe ]
    // [  Producto                   ]
    //
    nombreenticket  = nombreenticket.trim();
    nombreenticket  = nombreenticket.toLowerCase();
    nombreenticket  = nombreenticket.charAt(0).toUpperCase() + nombreenticket.slice(1);
    nombreenticket  = nombreenticket.replace(/\./g, '');//quita puntos
    nombreenticket  = nombreenticket.replace(/\s+/g, ' ');//quita spacios
    //nombreenticket  = nombreenticket.replace(/\s/g, '');//quita spacios
    unidmedticket   = prod.unid.substring(0,3);
    unidmedticket   = unidmedticket.toUpperCase()

    //formatos lines
    salida         += this.Colum( new Array(prod.unidades+unidmedticket,
					    formatDinero(pvp),
					    formatDescuento(prod.descuento),
					    formatDinero(total)));
    //formatos sublines
    salida         += ( nombreenticket != '')? preparaCadenaNombre(nombreenticket,stringcore,cr):'';

    this.datos_text_productos += salida;
		
    /* Añadimos estos datos a la informacion que habria que enviar al servidor */
						
	var	data_tickets2  = "";	
	var t = this.indiceProductoMetido;	
	var	firma = "line_" + t + "_"; 
	var crd = "&";	
    
	data_tickets2 += firma + "cod=" 	  + escape(prod.codigo) + crd;
	data_tickets2 += firma + "unid=" 	  + prod.unidades + crd;
        data_tickets2 += firma + "precio=" 	  + prod.precio + crd;
        data_tickets2 += firma + "precioalmacen=" + prod.precioalmacen + crd;
	data_tickets2 += firma + "importe=" 	  + prod.importe + crd;
	data_tickets2 += firma + "impuesto=" 	  + escape(prod.impuesto) + crd;
	data_tickets2 += firma + "descuento="	  + escape(prod.descuento) + crd;
	data_tickets2 += firma + "referencia="	  + escape(prod.referencia) + crd;
	data_tickets2 += firma + "cb="		  + escape(prod.codigo) + crd;
	data_tickets2 += firma + "nombre=" 	  + escape(prod.nombre) + crd;
	data_tickets2 += firma + "concepto=" 	  + encodeURIComponent(prod.concepto) + crd;
	data_tickets2 += firma + "talla=" 	  + encodeURIComponent(prod.talla) + crd;
	data_tickets2 += firma + "color=" 	  + encodeURIComponent(prod.color) + crd;	
	data_tickets2 += firma + "idsubsidiario=" + escape(prod.idsubsidiario) + crd;
	data_tickets2 += firma + "costo=" 	  + escape(prod.costo) + crd;
	data_tickets2 += firma + "pedidodet=" 	  + prod.pedidodet + crd;
	data_tickets2 += firma + "status=" 	  + prod.status + crd;
	data_tickets2 += firma + "oferta=" 	  + prod.oferta + crd;
	data_tickets2 += firma + "idproducto=" 	  + prod.idproducto + crd;
	
	this.datos_post_productos = this.datos_post_productos +  data_tickets2;
	this.indiceProductoMetido = this.indiceProductoMetido + 1;

}

/* HELPERS */
function normalFloat(cadena){
	var f = parseFloat(cadena);
	if (isNaN(f)) return 0.0;
	if (f<0.000000000001) return 0.0;
	return f;
}
/*+++++++++++++++++++++++++++++ TICKETS ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ MECAGRID  ++++++++++++++++++++++++++++++++++*/

/* DOM */


//Define id si no ha sido definida ya.
try {
	if (id);
} catch(e) {
	function id(nombrevictima){
		return document.getElementById(nombrevictima);
	}
}

var Dom = new Object();

Dom.create = function( tipoDeNodo, datosAjustar){
//	var fake = document.createElement("box");	
	var xnodo = document.createElement(tipoDeNodo);	
	var dato,paramdatos;
	var t=0;
	
	//xnodo.setAttribute("statustext",tipoDeNodo+":"+Math.random());
	
	if(datosAjustar){	
		paramdatos = datosAjustar.split(",");
		for (t=0;t<paramdatos.length;t++){
			dato = paramdatos[t].split("=");
			xnodo.setAttribute(dato[0],dato[1]);
		}	
	}
	return xnodo;
}

Dom.MatarTodosHijos = function (padreNodos){	
	var padre = id(padreNodos);
	while( padre.childNodes.length ){
		padre.removeChild( padre.childNodes[0] );
	}		
}

/* GRID */

var Meca = new Object();

Meca.genera = function (victima){

	var xtree = Dom.create("tree","flex=1");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	xtreecol = Dom.create("treecol","label=Filename,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtreecol = Dom.create("treecol","label=Location,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtreecol = Dom.create("treecol","label=Size,flex=1");
	xtreecols.appendChild(xtreecol);
	
	xtree.appendChild( xtreecols );
	
	var xtreechildren = Meca.create("treechildren");
	var xtreeitem = Meca.create("treeitem");
	var xtreerow = Meca.create("treerom");
	
	var xtreecell = Dom.create("treecell","Label=mozilla");
	xtreerow.appendChild(xtreecell);
	var xtreecell = Dom.create("treecell","Label=/data");
	xtreerow.appendChild(xtreecell);
	var xtreecell = Dom.create("treecell","Label=200 KB");
	xtreerow.appendChild(xtreecell);

	xtreeitem.appendChild( xtreerow);		
	xtreechildren.appendChild( xtreeitem );	
	xtree.appendChild( xtreechildren );
		
	id(victima).appendChild(xtree);
}


Meca.generaCruzadoProductos = function (victima, base){
	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true,seltype=single");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
    var nombre,flex;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
	    nombre   = heads["talla_"+t];
	    if (t==0) 
		flex = 0;
	    else 
		flex = 1;
	    
	    xtreecol = Dom.create("treecol","flex="+flex+",label="+nombre+",id=treecol_"+nombre+"_"+t);
	    xtreecol.setAttribute("style","font-weight: bold");
	    if (t==1){
		xtreecol.setAttribute("label"," ");
		//xtreecol.setAttribute("flex","1");
		xtreecol.setAttribute("style","min-width: 100px;font-weight: bold");			
	    }
		
	    if (t==0){
		xtreecol.setAttribute("style","min-width: 100px;font-weight: bold");		
	    }
	    
	    xtreecols.appendChild(xtreecol);							
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	
	var oldcero ="";
		
	
	for(var k=0;k<rows.length;k++){		
		var row = rows[k];								
		
		if (row[0]!=oldcero){				
			var firstLine = "firstLine";			
			oldcero = row[0];
		} else {
			var firstLine = "";
		}
		
		
		var	xtreeitem = Dom.create("treeitem","");
		var xtreerow = Dom.create("treerow");	
		
		for(j=0;j<row.length;j++){		
			
			if (j%2) {
				classpar = "esPar";
			} else 
				classpar = "noespar";													 
			
			xcell = Dom.create("treecell","label="+row[j]+",align=center,class="+classpar);			
			xcell.setAttribute("properties","colum_"+j+" celda " + firstLine );							
			
			xtreerow.appendChild(xcell);
		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
		

	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);
}




Meca.generaTable = function (victima, base){

	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true,seltype=single");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
	var nombre;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
		nombre = heads["talla_"+t];
		xtreecol = Dom.create("treecol","flex=0,label="+nombre+",id=treecol_"+nombre+"_"+t);
		xtreecols.appendChild(xtreecol);	
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	for(var k=0;k<rows.length;k++){
		var	xtreeitem = Dom.create("treeitem");
		var xtreerow = Dom.create("treerow");		
		var row = rows[k];						
		for(j=0;j<row.length;j++){		
			
			if (j%2) {
				classpar = "esPar";
			} else 
				classpar = "noespar";
							
			/*if (row[j]<1){
				addFix = ",collapse=true";
			} else addFix = "";*/							 
							
			xcell = Dom.create("treecell","label="+row[j]+",class="+classpar);
			xtreerow.appendChild(xcell);
		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);	
}


Meca.generaArbol = function (victima, base){

	var xtree = Dom.create("tree","flex=1,enableColumnDrag=true,hidecolumnpicker=true");
	var xtreecols = Dom.create("treecols");
	var xtreecol;
	
	var heads = base.heads;
	var numheads = base.numheads;
	var nombre;
	
	var xtarget = id(victima);
	
	
	for(var t=0;t<numheads;t++){	
		nombre = heads["talla_"+t];
		xtreecol = Dom.create("treecol","flex=1,label="+nombre+",id=treecol_"+nombre+"_"+t);
		xtreecols.appendChild(xtreecol);	
	}
		
	xtree.appendChild( xtreecols );	
	var xtreechildren = Dom.create("treechildren");
	
	var rows = base.rows;	
	var row,xtreecell,j,k;
	var xtreeitem;
	var xtreerow,rowheadtext;

	var rowhead = 0;
	for(var k=0;k<rows.length;k++){
		var	xtreeitem = Dom.create("treeitem");
		var xtreerow = Dom.create("treerow");		
		var row = rows[k];				
		for(j=0;j<row.length;j++){							
			xcell = Dom.create("treecell","label="+row[j]);
			xtreerow.appendChild(xcell);

		}	
		xtreeitem.appendChild( xtreerow);				
		xtreechildren.appendChild( xtreeitem );
	}
				
	xtree.appendChild( xtreechildren );			
	
	xtarget.setAttribute("flex",1);
	xtarget.appendChild(xtree);
	
}


Meca.cargarJSON = function (revisor,url,returned) {
//	var url = "testjson.php?CodigoBarras=90007006"
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
	var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante;
	var node,t,i;	
	
	if (!obj.responseText)
		return alert(po_error);	
		
	var objres = eval( "(" + obj.responseText+ ")" );	
	
	if(!returned)
		revisor(objres);
	
	return objres;
}



Meca.cargarXML = function (revisor) {
	var url = "service.php"
	
	var obj = new XMLHttpRequest();

	obj.open("GET",url,false);
	obj.send(null);
	
	var tex = "";
	var cr = "\n";
	
	var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante;
	var node,t,i;
	

	
	if (!obj.responseXML)
		return alert(po_error);		
	if (!obj.responseXML.documentElement)
		return alert(po_error);
	
	var xml = obj.responseXML.documentElement;		
	
//	alert( obj.responseText);
	
	for (i=0; i<xml.childNodes.length; i++) {
		node = xml.childNodes[i];
		if (node && node.getAttribute){
			nombre = node.getAttribute("nombre")
			//alert(nombre);
			data = new Array();			
			data["nombre"] = nombre;						
			revisor(data);
		}					
	}
}

/*+++++++++++++++++++++++++++++ MECAGRID  ++++++++++++++++++++++++++++++++++*/

/*++++++++++++++++++++++++ CADENAS  ++++++++++++++++++++++++++++*/

var po_numtic='Código :'; var po_dir='Dir.'; var po_web='Web.'; var po_telf='Telefono:'; var po_movil='Movil.';var po_comprobante='Doc.   :';var po_unid='Unid.';var po_prod='Prod.               ';var po_precio='Precio';var po_costo='Costo';var po_descuento='Desc.';var po_Total='Total';var po_TOTAL='TOTAL:';var po_TOTALDEUDA='Debe:';var po_TOTALCREDITO='TOTAL:';var po_Entregado='Entregado:';var po_Abonado='Abonado:';var po_Credito='Entregado:';var po_CreditoCliente='Credito:';var po_Cambio='Vuelto:';var po_Pendiente='Pendiente:';var po_desgloseiva='Desglose de IGV:';var po_leatendio='Le atendió:';var po_ticketarreglointerno='Ticket arreglo interno';var po_ticketcesionprenda='Ticket crédito de producto';var po_ticketcesionprendareserva='Ticket reserva de producto';var po_boucherde = 'Ticket de Pago '; var po_boucherdecredito = 'Ticket de asignación de crédito '; var po_tienda = 'Tienda : '; var po_ticketdevolucionprenda='Ticket devolución de prenda';var po_ticketnoserver='El servidor no ha podido autorizar la impresión de este ticket. Inténtelo mas tarde';var po_txtTicketVenta='Comprobante de venta';var po_txtTicketCesion='Ticket cesión';var po_txtTicketDevolucion='Ticket devolución';var po_txtTicketPedido='Presupuestos';var po_txtTicketMProducto='Meta Productos';var po_txtTicketServicioInterno='Ticket servicio';var po_imprimircopia='Impr. copia';var po_cerrar='Cerrar';var po_servidorocupado='Servidor ocupado, inténtelo más tarde';var po_nopuedeseliminarcontado='¡No puedes eliminar el cliente contado!';var po_seguroborrarcliente='¿Quieren borrar este cliente?';var po_clienteeliminado='Cliente eliminado del sistema';var po_noseborra='No se puede borrar ese cliente';var po_nuevocreado='Nuevo cliente creado';var po_clientemodificado='Cliente modificado';var po_operacionincompleta='Operacion con cliente incompleta, inténtelo mas tarde';var po_mensajeenviado='Mensaje enviado';var po_modopago='Modo de pago:';var po_nombreclientecontado='Cliente Contado';var po_ticketcliente='Cliente:';var po_Elige='Elije...';var po_15diaslimite='No se admiten devoluciones.\\nCambios dentro de las 24 horas.';var po_cuentascopias='¿Cuántas copias del código de barras necesita imprimir?';var po_cuantasunidadesquiere='¿Cuántas unidades del producto requiere?';var po_cuantasunidades='¿Cuántas unidades?';var po_faltadefcolor='Falta definir Modelo';var po_faltadeftalla='Falta definir Detalles';var po_faltadefcb='Falta definir el CB';var po_errorrepcod='Código de barras repetido';var po_tallacolrep='Detalle o Modelo repetidos';var po_unidadescompra='Debe especificar unidades de compra';var po_modnombreprod='Debe modificar el nombre del producto';var po_especificarref='Debe especificar una referencia';var po_especifiprecioventa='Debe especificar un precio de venta';var po_especificoste='Debe especificar un coste';var po_nuevoproducto='Nuevo producto';var po_nohayproductos='No hay productos';var po_sehandadodealtacodigos='Se han dado de alta %d códigos';var po_segurocancelar='¿Esta seguro que quiere cancelar?';var po_imprimircodigos='Imprimir CB';var po_borrar='Eliminar';var po_avisoborrar='¿Desea eliminar?';var po_nombre='Nombre';var po_talla='Concentración/Detalle';var po_color='Presentación/Modelo';var po_unidades='Unid.';var po_local='Local';var po_almacen='Almacén';var po_nombrecorto='Nombre de cliente demasiado corto';var po_quierecerrar='Seguro que quiere proceder al \'CIERRE DE CAJA\'?';var po_quiereabrir='Seguro que quiere proceder a \'ABRIR CAJA\'?';var po_sugerenciarecibida='Sugerencia recibida';var po_incidenciaanotada='Incidencia anotada';var po_notaenviada='Nota enviada';var po_confirmatraslado='¿Esta seguro?';var po_destino='Destino:';var po_mododepago='Modo de pago';var po_cuantascopias='¿Cuantas copias?';var po_moviendoa='Moviendo mercancía a: ';var po_importereal='Importe real de la caja:';var po_error=po_servidorocupado;var po_pagmas=">>";var po_pagmenos="<<";

/*++++++++++++++++++++++++ CADENAS  ++++++++++++++++++++++++++++*/


/*++++++++++++++++++++++++ SUSCRIPCION ++++++++++++++++++++++++++++*/
//

var cIdSuscripcionDet = 0;
var cSuscripcionDetalleLineas = 0;
var cIdSuscripcion    = 0;
var cIdTipoSuscripcion= 0;
var cSuscripcionContratosComboLineas = 0;
var cEstadoSuscripcion          = 0;
var cIdClienteSuscripcion       = 0;

function cargarSuscripcion(){

    var xid = id("buscaClienteSelect").value;
    var xml = '';
    if( xid == 1 ) return;
    if( !id("user_picker_"+xid) ) return;

    cIdClienteSuscripcion = preSeleccionadoCliente;
    
    id("tab-vistacliente").setAttribute("collapsed",true);
    id("tab-selcliente").setAttribute("visuallyselected",false);
    id("tab-suscripcion").setAttribute("collapsed",false);
    id("tab-suscripcion").setAttribute("visuallyselected",true);
    id("tab-suscripcion").setAttribute("label", " Suscripción: "+ usuarios[ cIdClienteSuscripcion ].nombre);
    id("tab-suscripcion").setAttribute("selected",true);
    id("tab-selcliente").setAttribute("selected",false);
    id("tab-boxclient").setAttribute("selectedIndex",3);

    vaciarSuscripcionDetalle();
    crearSuscripcion2XML( obtenerSuscripcionesCliente() );
}

function formularioDetalleLinea(xcod,xvalue,xop){

    var xtop         = parseInt( window.screen.width)/2 - 200;
    var xleft        = parseInt( window.screen.height)/2 - 150;
    var xtitlesusrip = "";
    var xcommand     = "";
    var xbtnlabel    = "";
    var xessuscrip   = false;
    var xnumserie    = true;

    id("rowMostrarInformacionExtra").setAttribute("collapsed",true);
    id("MostrarInformacionExtra").value = "";
    id("rowOrdenServicioNumeroSerie").setAttribute("collapsed",true);
    id("OrdenServicioNumeroSerie").value = "";

    id("btnSuscricpionLineaCancel").setAttribute("label",'Cancelar');
    id("btnSuscricpionLinea").setAttribute("collapsed",false);

    switch(xvalue){

    case 'NuevaSuscripcion':

	cIdSuscripcionDet = 0;

	id("suscripLineaConcepto").value    = productos[xcod].producto;
	id("suscripLineaCantidad").value    = 1;
	id("suscripLineaPrecio").value      = productos[xcod].pvd;
	id("suscripLineaImporte").value     = productos[xcod].pvd;
	id("suscripProductoServicio").value = productos[xcod].idproducto;
	id("suscripEstadoLinea").value      = 'Activo';
	id("suscripUnidadIntervalo").value  = 'Mes';
	id("suscripLineaIntervalo").value   = 1;
	id("suscripAdelantoPlazo").value    = 0;
	id("suscripAdelantoPlazoImporte").value = 0;
	id("suscripPlazoPago").value        = 1;

	xtitlesusrip = 'Nueva Suscripcion';
	xcommand     = "registrarSuscripcionLinea()";
	xbtnlabel    = "Aceptar";
	break;

    case 'EditarSuscripcion':

	id("suscripLineaConcepto").value    = slcConcepto;
	id("suscripProductoServicio").value = slcIdProducto;
	id("suscripLineaCantidad").value    = slcCantidad;
	id("suscripLineaPrecio").value      = slcPrecio;
	id("suscripLineaDescuento").value   = slcDescuento;
	id("suscripLineaImporte").value     = slcImporte;
	id("suscripLineaIntervalo").value   = slcIntervalo;
	id("suscripUnidadIntervalo").value  = slcUndIntervalo;
	id("suscripEstadoLinea").value      = slcEstado;
	id("suscripAdelantoPlazo").value    = slcAdelandoPlazo;
	id("suscripPlazoPago").value        = slcPlazoPago;

	id("suscripDiaFacturacion").value   = slcDiaFacturacion;

	xcommand     = "registrarSuscripcionLinea()";
	xbtnlabel    = "Modificar";
	xtitlesusrip = 'Modificando Suscripcion Servicio';

	calcularImporteSuscripcionAdelantoLinea();

	break;

    case 'NuevoOrdenServicio':
	id("suscripLineaConcepto").value = productos[xcod].producto;
	id("suscripLineaCantidad").value = 1;
	id("suscripLineaPrecio").value   = productos[xcod].pvd;
	id("suscripLineaImporte").value  = productos[xcod].pvd;
	id("suscripProductoServicio").value         = productos[xcod].idproducto;
	id("suscripProductoCodigoBarras").value     = productos[xcod].codigobarras;
	id("suscripProductoCodigoReferencia").value = productos[xcod].referencia;

	xnumserie    = (productos[xcod].serie)? false:true;
	xtitlesusrip = 'Nuevo Producto';
	xcommand     = "AgregarProductoOrdenServicio("+!xnumserie+")";
	xbtnlabel    = "Aceptar";
	xessuscrip   = true;
	break;

    case 'EditarOrdenServicio':
	var xiddet = cIdOrdenServicioDet;
	id("suscripLineaConcepto").value   = ordenserviciodet[ xiddet ].concepto;
	id("suscripProductoServicio").value= ordenserviciodet[ xiddet ].idproducto;
	id("suscripLineaCantidad").value   = ordenserviciodet[ xiddet ].unidades;
	id("suscripLineaPrecio").value     = ordenserviciodet[ xiddet ].precio;
	id("suscripLineaImporte").value    = ordenserviciodet[ xiddet ].importe;

        var xserie = (ordenserviciodet[ xiddet ].serie).split(",");
        var nseries = "";
        var xsep = "";
        for(var i= 0; i < xserie.length; i++){
           var aserie = xserie[i].split(":");
           nseries += xsep+aserie[1];
           xsep = ",";
        }
	id("OrdenServicioNumeroSerie").value = nseries;//ordenserviciodet[ xiddet ].serie;

	xcommand     = "ModificarServicioProducto()";
	xbtnlabel    = "Modificar";
	xtitlesusrip = 'Modificando Producto';

	xessuscrip   = true;
	xnumserie    = (trim(osdNumeroSerie) != '')? false:true;

        if(xop == 'Ver'){
          xtitlesusrip = 'Detalle Producto';
          id("btnSuscricpionLineaCancel").setAttribute("label",'Cerrar');
          id("btnSuscricpionLinea").setAttribute("collapsed",true);
        }
	break;
    }

    id("rowOrdenServicioNumeroSerie").setAttribute("collapsed",xnumserie);
    id("rowSuscripcionIntervalo").setAttribute('collapsed',xessuscrip);
    id("vboxSuscripcionLineaEstado").setAttribute('collapsed',xessuscrip);
    //id("vboxSuscripcionLineaAdelantoPlazoImporte").setAttribute('collapsed',xessuscrip);
    //id("vboxSuscripcionLineaAdelantoPlazo").setAttribute('collapsed',xessuscrip);
    id("vboxSuscripcionLineaDiaFacturacion").setAttribute('collapsed',xessuscrip);
    id("vboxSuscripcionLineaPlazoPago").setAttribute('collapsed',xessuscrip);
    id("rowSuscripcionDescuento").setAttribute('collapsed',xessuscrip);

    id("titleSuscripcionLinea").label = xtitlesusrip;
    id("btnSuscricpionLinea").setAttribute('label',xbtnlabel);
    id("btnSuscricpionLinea").setAttribute('oncommand',xcommand);
    id("panelSuscripcionLinea").openPopupAtScreen(xtop, xleft, false);
}

function calcularImporteSuscripcionLinea(){
    var xcantidad = id("suscripLineaCantidad").value;
    var xprecio   = id("suscripLineaPrecio").value;
    var xdescuento= id("suscripLineaDescuento").value;

    if(trim(xcantidad) == '' || trim(xcantidad) == 0)
	xcantidad = 1;

    if(trim(xprecio) == '')
	xprecio = 0;

    if(trim(xdescuento) == '')
	xdescuento = 0;

    var ximporte  = (parseFloat(xcantidad)*parseFloat(xprecio) - parseFloat(xdescuento));
    id("suscripLineaCantidad").value = xcantidad;
    id("suscripLineaPrecio").value = formatDinero(xprecio);
    id("suscripLineaDescuento").value = formatDinero(xdescuento);
    id("suscripLineaImporte").value = formatDinero(ximporte);
    calcularImporteSuscripcionAdelantoLinea();
}

function calcularImporteSuscripcionAdelantoLinea(){
    var xperiodos  = id("suscripAdelantoPlazo").value;
    var ximporte   = id("suscripLineaImporte").value;

    if(trim(xperiodos) == '') xperiodos = 0;

    id("suscripAdelantoPlazoImporte").value = formatDinero( parseFloat(xperiodos)*parseFloat(ximporte) );
    id("suscripAdelantoPlazo").value        = xperiodos;
}

function recargaComboSuscripcionContratos() {
 
    vaciarComboSuscripcionContratos();

    if( !suscripcionesclient[ cIdClienteSuscripcion ] )	return;

    var xid       = 0;
    var finit,ffin,fechainicio,fechafin,xsuscrip;

    for (var i = 0; i < suscripcionesclient[ cIdClienteSuscripcion ].length; i++){ 

	xid         = suscripcionesclient[ cIdClienteSuscripcion ][i];
	finit       = suscripciones[ xid ].fechainicio.split("-");
	ffin        = suscripciones[ xid ].fechafin.split("-");
	fechainicio = finit[2]+'/'+finit[1]+'/'+finit[0];
	fechafin    = ( suscripciones[ xid ].fechafin == '0000-00-00')? '':' al '+ffin[2]+'/'+ffin[1]+'/'+ffin[0];
	xsuscrip    = suscripciones[ xid ].tiposuscripcion +' - '+suscripciones[ xid ].estado+' - '+fechainicio+fechafin;

	addLineaComboSuscripcionContratos(xsuscrip,xid,false,false);
	
	if(i==0) cargarSuscripcionCliente(xid);
    } 
}

function addLineaComboSuscripcionContratos(xnombre, xvalor,xbase,xselect) {

    var xlistitem    = id("elementosSuscripcion");
    var xsuscripcion = document.createElement("menuitem");
    var xfunction    = (xbase)? "nuevoSuscripcionCliente();":"cargarSuscripcionCliente("+xvalor+")";

    xsuscripcion = document.createElement("menuitem");
    xsuscripcion.setAttribute("id","suscripcion_def_" + cSuscripcionContratosComboLineas);	
    xsuscripcion.setAttribute("value",xvalor);
    xsuscripcion.setAttribute("label",xnombre);
    xsuscripcion.setAttribute("oncommand",xfunction);

    if( xbase   ) xsuscripcion.setAttribute("style","font-weight:bold");
    if( xselect ) xsuscripcion.setAttribute("selected","true");

    xlistitem.appendChild( xsuscripcion );
    cSuscripcionContratosComboLineas++;
}

function vaciarComboSuscripcionContratos(){

    var xlistitem = id("elementosSuscripcion");
    var xmenulist = id("suscripComboContratos");

    for (var i = 0; i < cSuscripcionContratosComboLineas; i++) { 

        kid = id("suscripcion_def_"+i);					
        if (kid)
	    xlistitem.removeChild( kid ); 
    }

    cSuscripcionContratosComboLineas = 0;
    xmenulist.setAttribute("value",0 );	

    addLineaComboSuscripcionContratos('Nuevo Contrato', 0, true,true);
    nuevoSuscripcionCliente();
    id("suscripComboContratos").label = 'Nuevo Contrato';
    id("suscripComboContratos").value = 0;
}

function addLineaComboTipoSuscripcion(nombre, valor) {

    var xlistitem = id("elementosTipoSuscripcion");
    var xtiposuscripcion = document.createElement("menuitem");
    xtiposuscripcion.setAttribute("value",valor);
    xtiposuscripcion.setAttribute("label",nombre);
    xtiposuscripcion.setAttribute("oncommand","RegistrarSuscripcionCliente()");
    xlistitem.appendChild( xtiposuscripcion );

}

function salirSuscripcionLinea(){
    id("panelSuscripcionLinea").hidePopup();
}

function editarSuscripcionLinea(){

    var idx = id("listSuscripcionLinea").selectedItem;
    if(!idx) return;

    cIdSuscripcionDet = idx.value;
    slcConcepto       = id("sl_concepto_"+idx.value).getAttribute('label');
    slcIdProducto     = id("sl_concepto_"+idx.value).getAttribute('value');
    slcCantidad       = id("sl_cantidad_"+idx.value).getAttribute('label');
    slcPrecio         = id("sl_precio_"+idx.value).getAttribute('value');
    slcDescuento      = id("sl_descuento_"+idx.value).getAttribute('value');
    slcImporte        = id("sl_importe_"+idx.value).getAttribute('value');
    slcIntervalo      = id("sl_intervalo_"+idx.value).getAttribute('value');
    slcUndIntervalo   = id("sl_undintervalo_"+idx.value).getAttribute('value');
    slcEstado         = id("sl_estado_"+idx.value).getAttribute('label');      
    slcDiaFacturacion = id("sl_diafacturacion_"+idx.value).getAttribute('value');      
    slcAdelandoPlazo  = id("sl_adelantoplazo_"+idx.value).getAttribute('value');      
    slcPlazoPago      = id("sl_plazopago_"+idx.value).getAttribute('value');      

    if( cIdSuscripcionDet == idx.value )
	formularioDetalleLinea(false,'EditarSuscripcion',false);
}

function cleanSuscripcion(){
    var f = new Date();
    var fecha  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();

    id("suscripTipoSuscripcion").value  = '';
    id("suscripTipoPago").value         = 'Postpago';
    id("suscripProlongacion").value     = 'Ilimitado';
    id("suscripFechaInicio").value      = fecha;
    id("suscripFechaFin").value         = fecha;
    id("suscripEstado").value           = 'Pendiente';
    id("suscripComprobante").value      = 'Ticket';
    id("suscripSerieComprobante").value = '1';
    id("suscripComentarios").value      = '';
    id("suscripTipoSuscripcion").setAttribute('label', 'Elige...');

    id("rowFechaFinSuscripcion").setAttribute("collapsed",true);//Ilimitado
    id("rowSuscripcionSerieComprobante").setAttribute("collapsed",false);
    id("EmpresaTextGasto").value = '';
    id("IdSubsidiario").value = '';
}

function nuevoSuscripcionCliente(){
    cIdSuscripcion = 0;
    cleanSuscripcion();
    vaciarSuscripcionDetalle();
    xmenuSuscripcionLinea();
    
    verAccionesExtraSuscripcion();
}

function filtrarComboEstadoSuscripcion(){

    var xpendiente  = true;
    var xejecucion  = true;
    var xcancelado  = true;
    var xfinalizado = true;
    var xsuspendido = true;

    switch( cEstadoSuscripcion ){
    case 'Pendiente':
	xejecucion  = false;
	xcancelado  = false;
	xpendiente  = false;
	xfinalizado = true;
	break;
    case 'Ejecucion':
	xpendiente  = true;
	xfinalizado = false;
	xsuspendido = false;
	xejecucion  = false;
	break;
    case 'Finalizado':
	xejecucion  = true;
	xejecucion  = false;
	break;
    case 'Suspendido':
	xejecucion  = false;
        xfinalizado = false;
	break;
    case 'Cancelado':
	xpendiente  = false;
	break;
    }

    id("itemSuscripcionEjecucion").setAttribute('collapsed',xejecucion);
    id("itemSuscripcionPendiente").setAttribute('collapsed',xpendiente);
    id("itemSuscripcionCancelado").setAttribute('collapsed',xcancelado);
    id("itemSuscripcionFinalizado").setAttribute('collapsed',xfinalizado);
    id("itemSuscripcionSuspendido").setAttribute('collapsed',xsuspendido);
}

function mostrarNuevoTipoSuscripcion(xvalue){

    if ( suscripciones[ cIdSuscripcion ] )
	if(suscripciones[ cIdSuscripcion ].estado != 'Pendiente') 
	    return cargarSuscripcionCliente( cIdSuscripcion );

    id("rowTipoSuscripcion").setAttribute("collapsed",xvalue);
    id("rowNuevoTipoSuscripcion").setAttribute("collapsed",!xvalue);
    if(id("suscripComboContratos").value == 0 && !xvalue) 
	return nuevoSuscripcionCliente();
    if(id("suscripTipoSuscripcion").value == 0 && !xvalue) 
	return cargarSuscripcionCliente( cIdSuscripcion );
    if(xvalue) id("textNuevoTipoSuscripcion").focus();
}

function mostrarSuscripcionProlongacion(xprolongacion){
    var xprolong = (xprolongacion == 'Limitado')? false:true;
    id("rowFechaFinSuscripcion").setAttribute("collapsed",xprolong);
}

function mostrarSuscripcionComprobante(xcomprobante){
    var xcbte = (xcomprobante == 'Ticket')? true:false;
    id("rowSuscripcionSerieComprobante").setAttribute("collapsed",xcbte);
}


function ValidarSerieComprobanteSuscripcion(){
    var serie = id("suscripSerieComprobante").value;

    if(serie == 0 || trim(serie) == '')
	id("suscripSerieComprobante").value = 1;
}

function RegistrarTipoSuscripcion(xtiposuscrip){

    if( trim(xtiposuscrip) == "")
	return mostrarNuevoTipoSuscripcion(false);
    
    var url      = "modulos/suscripciones/modsuscripciones.php?modo=CreaTipoSuscripcion&xtiposuscrip="+xtiposuscrip;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres  = xrequest.responseText.split("~");

    if( xres[0] != '0')
        return alert('gPOS: '+po_servidorocupado+" "+xrequest.responseText);
    
    var xid    = parseInt(xres[1]);

    var text   = id("textNuevoTipoSuscripcion");

    addLineaComboTipoSuscripcion(xtiposuscrip.toUpperCase(), xid);     
    //elije nuevo elemento como seleted
    text.value         = "";
    //cIdTipoSuscripcion = xid;
    mostrarNuevoTipoSuscripcion(false);
    //RegistrarSuscripcionCliente();
}

function RegistrarSuscripcionCliente(){

    var IdTipoSuscripcion = id("suscripTipoSuscripcion").value;
    var TipoSuscripcion   = id("suscripTipoSuscripcion").label;
    var Serie             = id("suscripSerieComprobante").value;

    //Modificar...
    if(id("suscripComboContratos").value != 0)
	return ModificarSuscripcionCliente('1');

    //Nuevo...
    if(!confirm(c_gpos + " NUEVO CONTRATO DE SUSCRIPCION\n"+
	       "\n TIPO SUSCRIPCIÓN  : "+TipoSuscripcion+
	       "\n CLIENTE                    : "+usuarios[ cIdClienteSuscripcion ].nombre+
	       "\n\nregistrar nueva suscripción, ¿desea continuar?")) 
	return nuevoSuscripcionCliente();

    var url = "modulos/suscripciones/modsuscripciones.php?modo=CreaSuscripcion"+
	      "&xtiposusc="+IdTipoSuscripcion+
	      "&xclient="+cIdClienteSuscripcion+
	      "&xserie="+Serie;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res = xrequest.responseText;
    var xid = parseInt(res);

    setTimeout("mostrarMensajeTPVHead(1,'Nueva Suscripción')",50);
    crearSuscripcion2XML( obtenerSuscripcionesCliente() );
}

function cambiarSucripcionCliente(elemento,xsuscrip){
        var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("suscripComboContratos");
    var xvalue = 0;
    var xlabel = "";
    n = lista.itemCount;

    if(n==0) return; 

    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var cadena  = texto2.getAttribute('value');

	if ( busca == cadena)
	{
	    //xvalue = texto2.getAttribute("value");
	    texto2.setAttribute("label",xsuscrip);
	    texto2.label = xsuscrip;
	    break;
        }
    }

}

function mostrarMensajeTPVHead(xval,xmensaje){

    xval = (xval == 0)? true:false;

    id("txt-productoprogress").setAttribute("label", "Guardando cambios de "+xmensaje+"...");
    id("txt-productoprogress").setAttribute("collapsed", xval);
    if(xval) return;
    setTimeout("mostrarMensajeTPVHead(0,'')",2500);
}

function mostrarMensajeTPV(xval,xmensaje,xsettime){

    xval = (xval == 0)? true:false;

    id("txt-productoprogress").setAttribute("label", xmensaje);
    id("txt-productoprogress").setAttribute("collapsed", xval);

    if(xval) return;
    setTimeout("mostrarMensajeTPV(0,'',2500)",xsettime);
}

function ModificarSuscripcionCliente(xitem){
    if(id("suscripComboContratos").value == 0) return;

    if(id("suscripComboContratos").value == 0)
	return alert("gPOS: Seleccione un Contrato");

    //Control
    switch(xitem){
    case '1':
	if(suscripciones[ cIdSuscripcion ].estado != 'Pendiente') 
	    return cargarSuscripcionCliente( cIdSuscripcion );
    case '6':
	var xlinea = id("listSuscripcionLinea").itemCount;
	if(id("suscripEstado").value != 'Pendiente' && xlinea == 0)
	    return cargarSuscripcionCliente( cIdSuscripcion );
	xmensaje = 'Contrato de suscripción';
	var xconfirm = c_gpos + " MODIFICAR CONTRATO DE SUSCRIPCION\n"+
		    "\n TIPO SUSCRIPCIÓN  : "+ id("suscripTipoSuscripcion").label+
		    "\n ESTADO                    : "+ id("suscripEstado").value+
		    "\n CLIENTE                    : "+usuarios[ cIdClienteSuscripcion ].nombre+
		    "\n OBSERVACIÓN            : "+suscripciones[ cIdSuscripcion ].observaciones+
	            "\n\n salvar los cambios, ¿desea continuar?";

	break;
    case '9': xmensaje = 'observaciones'; break;
    case '2': xmensaje = 'tipo pago'; 
    case '3': xmensaje = 'prolongación'; 
    case '4': xmensaje = 'fecha inicio'; 
    case '5': xmensaje = 'fecha fin'; 
    case '7': xmensaje = 'combrobante'; 
    case '8': xmensaje = 'serie comprobante';
    case '10': xmensaje = 'administrador';
    default:
        var xestado = suscripciones[ cIdSuscripcion ].estado;
	if(xestado == 'Ejecucion' || xestado == 'Cancelado' || xestado == 'Finalizado') 
	    return cargarSuscripcionCliente( cIdSuscripcion );
    }
    //setTimeout("mostrarMensajeTPVHead(1,xmensaje)",100);
 
    var xdato;
    switch(xitem){
    case '1':
	//Tipo Suscripcion
	if(suscripciones[ cIdSuscripcion ].estado != 'Pendiente') 
	    return cargarSuscripcionCliente( cIdSuscripcion );

	xdato = id("suscripTipoSuscripcion").value;
        if(suscripciones[ cIdSuscripcion ].idtiposuscripcion == xdato) return;

	if(!confirm(xconfirm))
	    return cargarSuscripcionCliente( cIdSuscripcion );

	id("suscripComboContratos").value = cIdSuscripcion;
	break;
    case '2':
	//Tipo Pago
	xdato = id("suscripTipoPago").value;
        if(suscripciones[ cIdSuscripcion ].tipopago == xdato) return;

	break;
    case '3'://Prolongacion
	xdato = id("suscripProlongacion").value;
        if(suscripciones[ cIdSuscripcion ].prolongacion == xdato) return;
	var fechafin = id("suscripFechaFin").value;
	xdato = (xdato != 'Iliminatdo')? xdato+"~"+fechafin: xdato+"~0000-00-00";
	break;
    case '4'://Fecha Inicio
        if(id("suscripTipoSuscripcion").value == 0) return;
	xdato = id("suscripFechaInicio").value;
        if(suscripciones[ cIdSuscripcion ].fechainicio == xdato) return;
	break;
    case '5'://Fecha Fin
        if(id("suscripTipoSuscripcion").value == 0) return;
	var Prolongacion   = id("suscripProlongacion").value;
	xdato = id("suscripFechaFin").value;
        if(suscripciones[ cIdSuscripcion ].fechafin == xdato) return;
	xmensaje = 'fecha fin';
	xdato = (Prolongacion == 'Ilimitado')? '0000-00-00':xdato;
	break;
    case '6'://Estado
	xdato = id("suscripEstado").value;
        if(suscripciones[ cIdSuscripcion ].estado == xdato) return;

	if(!confirm(xconfirm))
	    return cargarSuscripcionCliente( cIdSuscripcion );

	break;
    case '7'://Comprobante
	xdato = id("suscripComprobante").value;
        if(suscripciones[ cIdSuscripcion ].comprobante == xdato) return;
	break;
    case '8'://Serie Comprobante
	var Comprobante    = id("suscripComprobante").value;
	xdato = id("suscripSerieComprobante").value;
        if(suscripciones[ cIdSuscripcion ].serie == xdato) return;
	xdato = (Comprobante == 'Ticket')? '0':xdato;
	break;
    case '9'://Comentarios
	xdato = trim(id("suscripComentarios").value);
        if(trim(suscripciones[ cIdSuscripcion ].observaciones) == xdato) return;
	break;
    case '10'://Administrador del suscriptor
	xdato = id("idsubsidiariohab").value;
        if(!xdato) return;
        if(trim(suscripciones[ cIdSuscripcion ].idsubsidiario) == xdato) return;
	break;        
    }
    setTimeout("mostrarMensajeTPVHead(1,xmensaje)",100);
    var xml = '';
    var xcontrato = id("suscripComboContratos").value;

    var opcion = (xcontrato != 0)? 'Editar':'Nuevo';
    var url = "modulos/suscripciones/modsuscripciones.php?modo=ModificaSuscripcion"+
	      "&xdato="+xdato+
	      "&xitem="+xitem+
	      "&xopcion="+opcion+
	      "&xclient="+cIdClienteSuscripcion+
	      "&xids="+xcontrato;
    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try{
	obj.send(null);
    } catch(z){
	return;
    }
    crearSuscripcion2XML( obj.responseXML.documentElement );
    cargarSuscripcionCliente( xcontrato );
}

function obtenerSuscripcionesCliente(){

    var z   = null;
    var url = "modulos/suscripciones/modsuscripciones.php?modo=ObtenerSuscripcionCliente"+
	           "&xclient="+cIdClienteSuscripcion;
    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try{
	obj.send(null);
    } catch(z){
	return;
    }
    return obj.responseXML.documentElement;
}

function crearSuscripcion2XML(xml){

    var idsuscipcion,idcliente,idtiposuscripcion,tiposuscripcion,fechainicio,fechafin,estado,prolongacion,comprobante,tipopago,observaciones,detalle,idsubsidiario,subsidiario;
    
    for (var i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    t = 0;
 	    idsuscipcion      = node.childNodes[t++].firstChild.nodeValue;
	    idcliente         = node.childNodes[t++].firstChild.nodeValue;
	    idtiposuscripcion = node.childNodes[t++].firstChild.nodeValue;
	    tiposuscripcion   = node.childNodes[t++].firstChild.nodeValue;
	    fechainicio	      = node.childNodes[t++].firstChild.nodeValue;
	    fechafin 	      = node.childNodes[t++].firstChild.nodeValue;
	    estado            = node.childNodes[t++].firstChild.nodeValue;
	    prolongacion      = node.childNodes[t++].firstChild.nodeValue;
	    comprobante       = node.childNodes[t++].firstChild.nodeValue;
	    serie             = node.childNodes[t++].firstChild.nodeValue;
	    tipopago  	      = node.childNodes[t++].firstChild.nodeValue;
	    observaciones     = node.childNodes[t++].firstChild.nodeValue;
            idsubsidiario     = node.childNodes[t++].firstChild.nodeValue;
	    detalle           = node.childNodes[t++].firstChild.nodeValue;
            subsidiario       = node.childNodes[t++].firstChild.nodeValue;

	    crearSuscripcion2Cliente(idsuscipcion,idcliente,idtiposuscripcion,tiposuscripcion,fechainicio,fechafin,estado,prolongacion,comprobante,tipopago,observaciones,serie,detalle,idsubsidiario,subsidiario);

        }
    }
    recargaComboSuscripcionContratos();
}

function crearSuscripcion2Cliente( xid,xidcliente,xidtiposuscripcion,xtiposuscripcion,xfechainicio,
				   xfechafin,xestado,xprolongacion,xcomprobante,xtipopago,
				   xobservaciones,xserie,xdetalle,xidsubsidiario,xsubsidiario){
    

    if( suscripciones[ xid ] )
	return updateSuscripcion2Cliente( xid,xidcliente,xidtiposuscripcion,xtiposuscripcion,xfechainicio,
					  xfechafin,xestado,xprolongacion,xcomprobante,xtipopago,
					  xobservaciones,xserie,xdetalle,xidsubsidiario,xsubsidiario );
    //Suscripcion
    suscripciones[ xid ]                   = new Object();	
    suscripciones[ xid ].idsuscripcion     = xid;
    suscripciones[ xid ].idcliente         = xidcliente;
    suscripciones[ xid ].idtiposuscripcion = xidtiposuscripcion;
    suscripciones[ xid ].tiposuscripcion   = xtiposuscripcion;
    suscripciones[ xid ].fechainicio       = xfechainicio;
    suscripciones[ xid ].fechafin          = xfechafin;
    suscripciones[ xid ].estado            = xestado;
    suscripciones[ xid ].prolongacion      = xprolongacion;
    suscripciones[ xid ].comprobante       = xcomprobante;
    suscripciones[ xid ].serie             = xserie;
    suscripciones[ xid ].tipopago          = xtipopago;
    suscripciones[ xid ].observaciones     = xobservaciones;
    suscripciones[ xid ].detalle           = xdetalle;
    suscripciones[ xid ].idsubsidiario     = xidsubsidiario;
    suscripciones[ xid ].subsidiario       = xsubsidiario;

    //Cliente
    if( !suscripcionesclient[ xidcliente ] ) 
	suscripcionesclient[ xidcliente ] = new Array();
    suscripcionesclient[ xidcliente ].push(xid);
    suscripcionesclient[ xidcliente ].sort(function(a,b){return a - b});
    suscripcionesclient[ xidcliente ].reverse();
}
function updateSuscripcion2Cliente( xid,xidcliente,xidtiposuscripcion,xtiposuscripcion,xfechainicio,
				    xfechafin,xestado,xprolongacion,xcomprobante,xtipopago,
				    xobservaciones,xserie,xdetalle,xidsubsidiario,xsubsidiario ){
    
    suscripciones[ xid ].idtiposuscripcion = xidtiposuscripcion;
    suscripciones[ xid ].tiposuscripcion   = xtiposuscripcion;
    suscripciones[ xid ].fechainicio       = xfechainicio;
    suscripciones[ xid ].fechafin          = xfechafin;
    suscripciones[ xid ].estado            = xestado;
    suscripciones[ xid ].prolongacion      = xprolongacion;
    suscripciones[ xid ].comprobante       = xcomprobante;
    suscripciones[ xid ].serie             = xserie;
    suscripciones[ xid ].tipopago          = xtipopago;
    suscripciones[ xid ].observaciones     = xobservaciones;
    suscripciones[ xid ].detalle           = xdetalle;
    suscripciones[ xid ].idsubsidiario     = xidsubsidiario;
    suscripciones[ xid ].subsidiario       = xsubsidiario;
}
    
function cargarSuscripcionCliente(xid){
    var f              = new Date();
    var fecha          = f.getFullYear() + "-" + (f.getMonth() +7) + "-" + f.getDate();
    var xprolong       = ( suscripciones[ xid ].prolongacion  == 'Limitado')? false:true;
    var xcbte          = ( suscripciones[ xid ].comprobante == 'Ticket')? true:false;

    cIdSuscripcion     = suscripciones[ xid ].idsuscripcion;
    cEstadoSuscripcion = suscripciones[ xid ].estado;

    id("suscripComboContratos").value  = xid;
    id("suscripTipoSuscripcion").value =  suscripciones[ xid ].idtiposuscripcion;
    id("suscripFechaInicio").value     =  suscripciones[ xid ].fechainicio;
    id("suscripFechaFin").value        = ( suscripciones[ xid ].fechafin == '0000-00-00' )? fecha:suscripciones[ xid ].fechafin;
    id("suscripEstado").value          = suscripciones[ xid ].estado;
    id("suscripProlongacion").value    = suscripciones[ xid ].prolongacion;
    id("suscripComprobante").value     = suscripciones[ xid ].comprobante;
    id("suscripSerieComprobante").value= suscripciones[ xid ].serie;
    id("suscripTipoPago").value        = suscripciones[ xid ].tipopago;
    id("suscripComentarios").value     = trim(suscripciones[ xid ].observaciones);

    id("EmpresaTextGasto").value       = suscripciones[ xid ].subsidiario;
    id("IdSubsidiario").value          = suscripciones[ xid ].idsubsidiario;

    id("rowFechaFinSuscripcion").setAttribute("collapsed",xprolong);
    id("rowSuscripcionSerieComprobante").setAttribute("collapsed",xcbte);

    filtrarComboEstadoSuscripcion();
    cargarSuscripcionDetalle();
    setTimeout('BuscarSuscripcionComprobante()',600);
    xmenuSuscripcionLinea();

    verAccionesExtraSuscripcion();
}

function cargarSuscripcionDetalle(){

    vaciarSuscripcionDetalle();
    if( !trim(suscripciones[cIdSuscripcion].detalle) ) return;

    var filas,celdas='';
    var slIdSuscripcionDet,slConcepto,slIdProducto,slCantidad,slPrecio,slDescuento,slImporte,slIntervalo,slUndIntervalo,slEstado,slDiaFacturacion,slAdelandoPlazo,slPlazoPago;

    filas = suscripciones[cIdSuscripcion].detalle.split(";");
    
    for(var i = 0; i<filas.length; i++){
	celdas    = filas[i].split("~");
 
	slIdSuscripcionDet= celdas[0];
	slIdProducto      = celdas[2];
	slConcepto        = celdas[3];
	slIntervalo       = celdas[4];
	slUndIntervalo    = celdas[5];
	slEstado          = celdas[6];
	slCantidad        = celdas[7];
	slPrecio          = celdas[8];
	slDescuento       = celdas[9];
	slImporte         = celdas[10];
	slDiaFacturacion  = celdas[11];
	slAdelandoPlazo   = celdas[12];
	slPlazoPago       = celdas[13];

	generarListaSuscripcionLinea(slConcepto,slIdProducto,slCantidad,slPrecio,slDescuento,
				     slImporte,slIntervalo,slUndIntervalo,slEstado,
				     slIdSuscripcionDet,slDiaFacturacion,slAdelandoPlazo,slPlazoPago);
    }

}

function generarListaSuscripcionLinea(slConcepto,slIdProducto,slCantidad,slPrecio,
				      slDescuento,slImporte,slIntervalo,slUndIntervalo,
				      slEstado,IdSuscripcionDet,slDiaFacturacion,
				      slAdelandoPlazo,slPlazoPago){

    var listaSuscripcionLinea = id("listSuscripcionLinea");
    var ldiaFacturar          = ( slDiaFacturacion == 0 )? 'Auto':'Día '+slDiaFacturacion;
    var xrow = document.createElement("listitem");
    xrow.setAttribute("value",IdSuscripcionDet);
    xrow.setAttribute("id","sl_lista_"+cSuscripcionDetalleLineas);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",cSuscripcionDetalleLineas+1 );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",slEstado );
    xcell.setAttribute("id",'sl_estado_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",slConcepto );
    xcell.setAttribute("value",slIdProducto );
    xcell.setAttribute("id",'sl_concepto_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",slCantidad );
    xcell.setAttribute("style","text-align:right" );
    xcell.setAttribute("id",'sl_cantidad_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",formatDinero(slPrecio) );
    xcell.setAttribute("value",slPrecio );
    xcell.setAttribute("style","text-align:right" );
    xcell.setAttribute("id",'sl_precio_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",formatDinero(slDescuento) );
    xcell.setAttribute("value",slDescuento );
    xcell.setAttribute("style","text-align:right" );
    xcell.setAttribute("id",'sl_descuento_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",formatDinero(slImporte) );
    xcell.setAttribute("value",slImporte );
    xcell.setAttribute("style","text-align:right" );
    xcell.setAttribute("id",'sl_importe_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("label",slIntervalo +' '+slUndIntervalo);
    xcell.setAttribute("value",slIntervalo);
    xcell.setAttribute("style","text-align:center" );
    xcell.setAttribute("id",'sl_intervalo_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("style","text-align:center" );
    xcell.setAttribute("label",ldiaFacturar);
    xcell.setAttribute("value",slDiaFacturacion);
    xcell.setAttribute("id",'sl_diafacturacion_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("value",slUndIntervalo);
    xcell.setAttribute("collapsed",true );
    xcell.setAttribute("id",'sl_undintervalo_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("value",slAdelandoPlazo);
    xcell.setAttribute("collapsed",true );
    xcell.setAttribute("id",'sl_adelantoplazo_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("value",slPlazoPago);
    xcell.setAttribute("collapsed",true );
    xcell.setAttribute("id",'sl_plazopago_'+IdSuscripcionDet );
    xrow.appendChild(xcell);

    listaSuscripcionLinea.appendChild(xrow);			
    cSuscripcionDetalleLineas++;
}

function vaciarSuscripcionDetalle(){	

    var lista = id("listSuscripcionLinea");

    for (var i = 0; i < cSuscripcionDetalleLineas; i++) { 
        kid = id("sl_lista_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }

    cSuscripcionDetalleLineas = 0;		
}

function validasuscripDiaFacturacion(xthis){
    if(parseInt(xthis.value) > 28) return xthis.value = 28;
    if(parseInt(xthis.value) == 0 || parseInt(xthis.value) == 0 ) return xthis.value = 1;
}

function registrarSuscripcionLinea(){
    var slConcepto       = id("suscripLineaConcepto").value;
    var slIdProducto     = id("suscripProductoServicio").value;
    var slCantidad       = id("suscripLineaCantidad").value;
    var slPrecio         = id("suscripLineaPrecio").value;
    var slDescuento      = id("suscripLineaDescuento").value;
    var slImporte        = id("suscripLineaImporte").value;
    var slIntervalo      = id("suscripLineaIntervalo").value;
    var slUndIntervalo   = id("suscripUnidadIntervalo").value;
    var slEstado         = id("suscripEstadoLinea").value;
    var slDiaFacturacion = id("suscripDiaFacturacion").value;
    var slAdelandoPlazo  = id("suscripAdelantoPlazo").value;
    var slPlazoPago      = id("suscripPlazoPago").value;
    var xids             = cIdSuscripcion;
    var xrequest = new XMLHttpRequest();
    var url = "modulos/suscripciones/modsuscripciones.php?modo=CreaSuscripcionLinea"+
	      "&xconcepto="+slConcepto+
	      "&xidprod="+slIdProducto+
	      "&xcant="+slCantidad+
	      "&xprecio="+slPrecio+
	      "&xdscto="+slDescuento+
	      "&ximpte="+slImporte+
	      "&xintervalo="+slIntervalo+
	      "&xundinter="+slUndIntervalo+
	      "&xestado="+slEstado+
	      "&xdiafacturar="+slDiaFacturacion+
	      "&xadelanto="+slAdelandoPlazo+
	      "&xplazopago="+slPlazoPago+
	      "&xidsd="+cIdSuscripcionDet+
	      "&xclient="+cIdClienteSuscripcion+
	      "&xids="+xids;
    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try{
	obj.send(null);
    } catch(z){
	return;
    }
    setTimeout("mostrarMensajeTPVHead(1,'lineas')",100);
    id("panelSuscripcionLinea").hidePopup();
    crearSuscripcion2XML( obj.responseXML.documentElement );
    cargarSuscripcionCliente( xids );
}

// Comprobantes de Suscripciones
var ilineaSuscripcionComprobante = 0

function vaciarSuscripcionComprobantes(){
    var lista = id("listComprobantesSuscripcion");

    for (var i = 0; i < ilineaSuscripcionComprobante; i++) { 
        kid = id("lineabuscaventasuscripcion_"+i);					
        if (kid)	lista.removeChild( kid ); 
    }
    ilineaSuscripcionComprobante = 0;    
}

function BuscarSuscripcionComprobante(){

    vaciarSuscripcionComprobantes();

    var desde           = id('FechaComprobanteSuscripcion').value;
    var hasta           = id("FechaComprobanteSuscripcionHasta").value;
    var filtrocodigo    = id("busquedaCodigoSerie").value;
    var filtroventa     = id("FiltroSuscripcionDocumento").value;
    var nombre          = "";
    var modo            = (id("FiltroEstadoComprobante").value=="Pendiente")? "pendientes":"todos";
    var modoserie       = "todos";
    var modosuscripcion = "suscripcion";
    var modofactura     = (filtroventa =="Factura")?"factura":"todos";
    var modoboleta      = (filtroventa =="Boleta")?"boleta":"todos";
    var modoticket      = (filtroventa =="Ticket" )?"ticket":"todos";
    var mododevolucion  = "todos";
    var modoalbaran     = "todos";
    var modoalbaranint  = "todos";
    var forzarid        = false;

    RawBuscarVentasSuscripcion(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,
			       modoboleta,mododevolucion,modoalbaran,modoalbaranint,modoticket,
			       false,false,forzarid,AddLineaVentasSuscripcion);
}

function RawBuscarVentasSuscripcion(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,
				    modoboleta,mododevolucion,modoalbaran,modoalbaranint,modoticket,
				    IdComprobante,reimprimir,forzarid,FuncionProcesaLineaSuscripcion){

    var url = "services.php?modo=mostrarVentas&desde=" + escape(desde) 
        + "&modoconsulta=" + escape(modo) 
        + "&hasta=" + escape(hasta) 
        + "&nombre=" + escape(nombre)
        + "&modoserie=" + escape(modoserie)
        + "&modosuscripcion=" + escape(modosuscripcion)
        + "&modoboleta=" + escape(modoboleta)
        + "&modoticket=" + escape(modoticket)
        + "&mododevolucion=" + escape(mododevolucion)
        + "&modoalbaran=" + escape(modoalbaran)
        + "&modoalbaranint=" + escape(modoalbaranint)
        + "&modofactura=" + escape(modofactura)
        + "&esventas=off"
        + "&modoventa=tpv" 
	+ "&idsuscripcion="+ cIdSuscripcion
        + "&forzarfactura=" + IdComprobante
        + "&forzarid=" + forzarid;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    
    var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,NumeroDocumento,TipoDocumento,IdCliente,sIdSuscripcion,FechaEmision;
    var node,t,i,codventa,xLocal; 
    var totalVenta = 0;
    var totalVentaPendiente = 0;
    var ImporteTotalVentas = 0;
    if (!obj.responseXML)
        return alert('gPOS: '+po_servidorocupado);

    var xml = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    t = 0;
	    vendedor 	    = node.childNodes[t++].firstChild.nodeValue;
	    serie 	    = node.childNodes[t++].firstChild.nodeValue;
	    num 	    = node.childNodes[t++].firstChild.nodeValue;
	    fecha 	    = node.childNodes[t++].firstChild.nodeValue;
	    total 	    = node.childNodes[t++].firstChild.nodeValue;
	    totalVenta      = parseFloat(totalVenta) + parseFloat(total);
	    pendiente 	    = node.childNodes[t++].firstChild.nodeValue;
	    totalVentaPendiente = parseFloat(totalVentaPendiente) + parseFloat(pendiente);
	    estado 	    = node.childNodes[t++].firstChild.nodeValue;
	    IdComprobante   = node.childNodes[t++].firstChild.nodeValue;
	    NumeroDocumento = node.childNodes[t++].firstChild.nodeValue;
	    TipoDocumento   = node.childNodes[t++].firstChild.nodeValue;
	    codventa        = serie+'-'+num;	    
	    
	    if (node.childNodes[t].firstChild)
                nombreCliente = node.childNodes[t++].firstChild.nodeValue;
	    else 
                nombreCliente = "";

	    IdCliente     = node.childNodes[t++].firstChild.nodeValue;
	    xLocal        = node.childNodes[t++].firstChild.nodeValue;
	    IdLocal       = node.childNodes[t++].firstChild.nodeValue;
	    MotivoAlba    = node.childNodes[t++].firstChild.nodeValue;
	    sIdSuscripcion= node.childNodes[t++].firstChild.nodeValue;
	    FechaEmision  = node.childNodes[t++].firstChild.nodeValue;

	    FuncionProcesaLineaSuscripcion(item,vendedor,serie,num,fecha,total,pendiente,estado,
					   IdComprobante,nombreCliente,NumeroDocumento,TipoDocumento,
					   IdCliente,MotivoAlba,FechaEmision);
	    item--;
        }
    }    
}

function AddLineaVentasSuscripcion(item,vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,
				   nombreCliente,NumeroDocumento,TipoDocumento,IdCliente,MotivoAlba,FechaEmision){

    var lista = id("listComprobantesSuscripcion");
    var xitem, xnumitem, xvendedor,xserie,xnum,xfecha,xtotal,xpendiente,xestado,xtipodoc,xop;
    
    xitem = document.createElement("listitem");
    xitem.value = IdComprobante;
    xitem.setAttribute("id","lineabuscaventasuscripcion_"+ilineaSuscripcionComprobante);
    ilineaSuscripcionComprobante++;
    
    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");
    
    xtipodoc = document.createElement("listcell");
    xtipodoc.setAttribute("label",TipoDocumento+' '+MotivoAlba);
    xtipodoc.setAttribute("value",TipoDocumento);
    xtipodoc.setAttribute("style","text-align:left");
    
    xserie = document.createElement("listcell");
    xserie.setAttribute("label", serie+"-"+num);
    xserie.setAttribute("style","text-align:left");
    xserie.setAttribute("id","ventasuscripcion_serie_"+IdComprobante);
    
    xfecha = document.createElement("listcell");
    xfecha.setAttribute("style","text-align:right");
    xfecha.setAttribute("label", FechaEmision);	
    
    xtotal = document.createElement("listcell");
    xtotal.setAttribute("label", parseFloat(total).toFixed(2));
    xtotal.setAttribute("style","text-align:right");

    xpendiente = document.createElement("listcell");
    xpendiente.setAttribute("label", parseFloat(pendiente).toFixed(2));
    xpendiente.setAttribute("style","text-align:right");
    xpendiente.setAttribute("id","ventasuscripcion_pendiente_"+IdComprobante);

    xestado = document.createElement("listcell");
    xestado.setAttribute("label", estado);
    xestado.setAttribute("style","text-align:center","width: 8em");
    xestado.setAttribute("crop", "end");
    xestado.setAttribute("id","ventasuscripcion_status_"+IdComprobante);

    if(NumeroDocumento=='0')
	NumeroDocumento = num;
     
    xnumdoc = document.createElement("listcell");
    xnumdoc.setAttribute("label", NumeroDocumento+'  ');
    xnumdoc.setAttribute("style","text-align:left");
    xnumdoc.setAttribute("id","ventasuscripcion_num_bol_"+IdComprobante);
    
    xitem.appendChild( xnumitem );
    xitem.appendChild( xtipodoc );
    xitem.appendChild( xserie );
    xitem.appendChild( xnumdoc );
    xitem.appendChild( xfecha );
    xitem.appendChild( xtotal );
    xitem.appendChild( xpendiente );	
    xitem.appendChild( xestado );
    lista.appendChild( xitem );		
}

function verComprobanteVentaSuscripcion(){

    var idx      = id("listComprobantesSuscripcion").selectedItem;

    if(!idx) return;

    var xcodigo  = idx.childNodes[2].attributes.getNamedItem('label').nodeValue;
    id("busquedaCodigoSerie").value = xcodigo;
    BuscarVentas();
    setTimeout("VerVentas()",200);
    buscarPorCodSerie(xcodigo);
}

function xmenuSuscripcionLinea(){
    var idx = id("listSuscripcionLinea").selectedItem;
    var agregar = (cIdSuscripcion == 0)? true:false;
    var editar  = (!idx )? true:false;

    switch( cEstadoSuscripcion ){
    case 'Finalizado' :
    case 'Ejecucion' :
    case 'Cancelado'  :
	agregar = true;
	editar  = true;
	break;
    }

    id("itemAgregarSuscripcionLinea").setAttribute('disabled',agregar);
    id("itemEditarSuscripcionLinea").setAttribute('disabled',editar);
}

function mostrarFormSuscripcionToOrdenServicio(){
    if(cIdSuscripcion == 0) return;
    id("SuscripToOrdenCliente").setAttribute("label",usuarios[cIdClienteSuscripcion].nombre);
    id("vboxAccionesExtraSuscripcion").setAttribute("collapsed",true);
    id("formSuscripcionToOrdenServicio").setAttribute("collapsed",false);
    id("suscripToOrdenObs").focus();
}

function registrarSuscripOrdenServicio(){
    if(cIdSuscripcion == 0) return;


    if(servicios.length == 0) 
	return alert("gPOS: Registre por lo menos un servicio para garantías en: \n\n "+
                     "      * Admin > Compras > Productos > Nuevo Producto" );

    var Obs       = id("suscripToOrdenObs").value;
    var Prioridad = id("suscripToOrdenPrioridad").value;

    salirSuscripToOrden();
    VerServicios();
    RecibirSuscripcionOrdenServicio(Obs,Prioridad);
}

function RecibirSuscripcionOrdenServicio(Obs,Prioridad){
    oscClon = false;

    id("idClienteOrdenServicio").value = cIdClienteSuscripcion;
    id("FiltroPrioridad").value        = '1';
    id("TipoOrdenServicio").value      = 'Regular';
    id("TipoOrdenServicio").setAttribute('value','Regular');
    id("FiltroPrioridad").value        = Prioridad;
    obtenerSerieNumeroOrdenServicio();
    RegistrarOrdenServicio(cIdSuscripcion);

    id("ObservacionServicio").value    = Obs;
    verProductoSat(false);
    var tiposerv = id("FiltroTipoServicio").label;
    id("ConceptoServicio").value = tiposerv;
    //if(ospDetalleSat == 1)
	//vertabProductoDetalleSat(true);
    mostrarPanelOrdenServicioDet(true,false);    
}

function salirSuscripToOrden(){
    id("formSuscripcionToOrdenServicio").setAttribute("collapsed",true);
    id("suscripToOrdenObs").value = "";
    id("suscripToOrdenPrioridad").value = 1;
    id("vboxAccionesExtraSuscripcion").setAttribute("collapsed",false);
}

function verAccionesExtraSuscripcion(){
    salirSuscripToOrden();
    var xval = (cIdSuscripcion == 0)? true:false;
    id("vboxAccionesExtraSuscripcion").setAttribute("collapsed",xval);
}

function mostrarSuscripcionFichaTecnica(xop){
    alert("línea:15422");
}
/*++++++++++++++++++++++++ SUSCRIPCION ++++++++++++++++++++++++++++*/

/*++++++++++++++++++++++++ PANEL PRODUCTO ++++++++++++++++++++++++++++*/

    function elijePanelProducto(xmodulo){
	var xtop  = parseInt( window.screen.width)/2 - 350;
	var xleft = parseInt( window.screen.height)/2 - 180;
	var xwidth = parseInt( window.screen.width)/1.6;
	cLoadModulo = xmodulo;

	id("panelElijeProducto").openPopupAtScreen(xtop, xleft, false);
	id("panelElijeProducto").style.width = xwidth+'px';
	id("panelNOM").focus();
    }

    function getPanelCodigoSelectedProd() {
        var t,codigo;	
        var fila;

        for (t=0;t<prodlist.length;t++) {
            if (prodlist[t]) {
                codigo = prodlist[t];
                if (codigo) {
                    fila = id("pnlprod_" + codigo);
                    if (fila && fila.selected) {
                        return codigo;
                    }
                }		 
            }
        }	
        return null;
    }

    function panelAgnadirProducto(){
        var cod     = getPanelCodigoSelectedProd();
	id("panelElijeProducto").hidePopup();

	switch( cLoadModulo ){
	case 'OrdenServicio':
            formularioDetalleLinea(cod,'NuevoOrdenServicio',false);   
	    break;
	case 'Suscripcion':
	    formularioDetalleLinea(cod,'NuevaSuscripcion',false);
	    break;
	default: 
	    return;
	}
    }

    function focusPanelListaProductos(){
	var xbosprodutos = id("listaPanelProductos"); 
	xbosprodutos.focus();
    }

    function VaciarPanelListadoProductos(){

        var oldListbox = id('listaPanelProductos');
        var newListbox = document.gClonedListboxPanel.cloneNode(true);
        oldListbox.parentNode.replaceChild( newListbox,oldListbox);     

        prodlist       = new Array();
        prodlist_cb    = new Array();
        prodlist_tag   = new Array();
    }

    function agnadirPanelPorReferencia()	{
        var referencia = id("panelREF").value.toUpperCase();
        if (!referencia) return;
        referencia  = new String(referencia);
	
        if (referencia.length <1) return;
	
        raw_agnadirPanelPorReferencia( CleanRef(referencia) );
    }

    function agnadirPanelPorCodigoBarras() {

            var cb  = id("panelCB");
            var vcb = CleanCB(cb.value);

            if (vcb.length < 1 ) return;

	    if(habilitarAddMProducto()) return;

            cb.value = "";
            cb.setAttribute("value","");

  	    VaciarPanelListadoProductos();	

	    //Tenemos este producto listado?
            if (!pool.Existe( vcb.toUpperCase() ))
	    {
		//Intenta anhadirlo...
		ExtraBuscarEnServidorXCB(vcb);
		//Existe...
		if(!productos[vcb]) return; 
	    }
            //Encuentra, 
	    if(vcb!='') raw_agnadirPanelPorCodigoBarras(vcb,true);
    } 

    function raw_agnadirPanelPorReferencia(referencia)	{
	var k,yaexiste;	
	var modo       = id("rgModosTicket").value;
	var precio     = 0;

	VaciarPanelListadoProductos();

	for(var t=0;t<iprodCod;t++) 
	{
	    cod = prodCod[t];
	    ref = productos[cod].referencia.toUpperCase(); 

	    if( ref.indexOf( referencia ) != -1 )  
		break;
	    else
		ref="";
	}
	
	if (ref2code[ref]) 
	{
	    var productosRef = ref2code[ref].split(",");
	    var p;
	    
	    for(var t=0;t<productosRef.length;t++) {
		p = productosRef[t];
		if (p)
		{
		    k      = productos[p];
		    precio = (Local.TPV=='VC')? k.pvc:k.pvd;

		    if (k) 
		    {		
			yaexiste = prodlist_cb[k.codigobarras];
			if (!yaexiste)
			    CrearPanelEntradaEnProductos(k.producto,k.codigobarras,k.referencia,
							 precio,k.impuesto,k.unidades,k.costo,
							 k.lote,k.vence,k.serie,k.menudeo,
							 k.unidxcont,k.unid,k.cont,
							 k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
							 k.pvo,k.condventa,k.mproducto);
		    }
		}
	    }
	}
	else
	    VaciarPanelListadoProductos();

	if (  esOnlineBusquedas()  ) 
	    ExtraBuscarEnServidorXRef(ref);
    }

    function raw_agnadirPanelPorCodigoBarras(vcb, reEntrar) {
	
        var vcb        = CleanCB(vcb);
        var codbar     = vcb.toUpperCase();
	var modo       = id("rgModosTicket").value;
	var encontrado = false;
        var estado     = false;


	//Servicio
	if( productos[vcb].lote  || 
	    productos[vcb].vence ||
	    productos[vcb].mproducto ) return;
	panelCEEP(vcb);
    }

    function panelCEEP(codigo){
        var k      = productos[codigo];		
	var precio = (Local.TPV=='VC')? k.pvc:k.pvd;
	
        if (k) 
	{					
	    CrearPanelEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
					 k.impuesto,k.unidades,k.costo,k.lote,k.vence,k.serie,
					 k.menudeo,k.unidxcont,k.unid,k.cont,
					 k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
					 k.pvo,k.condventa,k.mproducto);
	    focusPanelListaProductos();
        }	
	
    }

    function agnadirPanelPorNombre() {
	    //MostrarAjax();
	setTimeout("raw_agnadirPanelPorNombre()",100);
    }

    function raw_agnadirPanelPorNombre() {	
    
	var cod,text  = "",k;
	var nombre    = new String(id("panelNOM").value);
	var cadenas   = nombre.split("|");
	var precio    = 0;
	var cadena1   = trim(cadenas[0]).toUpperCase();
	var cadena2   = (cadenas[1])? trim(cadenas[1]).toUpperCase():"";
	
	if (nombre.length < 3) return;

	VaciarPanelListadoProductos();		

	nombre     = nombre.toUpperCase();

	//Busqueda Array
	for(var t=0;t<iprodCod;t++) 
	{
	    cod         = prodCod[t];
	    nom         = productos[cod].nombre.toUpperCase();
	    al1         = productos[cod].alias1.toUpperCase();
	    al2         = productos[cod].alias2.toUpperCase();	
	    marca       = productos[cod].marca.toUpperCase();	
	    modelo      = productos[cod].talla.toUpperCase();	
	    detalle     = productos[cod].color.toUpperCase();	
	    laboratorio = productos[cod].laboratorio.toUpperCase();	

	    if  ( (nom.indexOf( cadena1 ) != -1) || 
		  (al1.indexOf( cadena1 ) != -1) || 
		  (al2.indexOf( cadena1 ) != -1))  
	    {
		k      = productos[cod];
		precio = (Local.TPV=='VC')? k.pvc:k.pvd;
		/*
		if( k.lote  || 
		    k.vence ||
		    k.mproducto ) continue;
                */
		if(cadena2=="")
		{
		    CrearPanelEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
						 k.impuesto,k.unidades,k.costo,k.lote,k.vence,
						 k.serie,k.menudeo,k.unidxcont,k.unid,k.cont,
						 k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
						 k.pvo,k.condventa,k.mproducto);
 		}
		else{
		    if( (marca.indexOf(cadena2)!= -1   ) ||
			(modelo.indexOf(cadena2) != -1 ) ||
			(detalle.indexOf(cadena2) != -1) || 
			(laboratorio.indexOf(cadena2) != -1) )
			CrearPanelEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
						     k.impuesto,k.unidades,k.costo,k.lote,k.vence,
						     k.serie,k.menudeo,k.unidxcont,k.unid,k.cont,
						     k.servicio,k.ilimitado,k.oferta,k.ofertaunid,
						     k.pvo,k.condventa,k.mproducto);
		}
	    }
	}

	if (  esOnlineBusquedas()  )
	    ExtraBuscarEnServidor(nombre);  	  		
    }


    function CrearPanelEntradaEnProductos(producto,codigo,referencia,precio,
  					  impuesto,unidades,costo,lote,vence,serie,
					  menudeo,unidxcont,unid,cont,servicio,ilimitado,
					  oferta,ofertaunid,
					  pvo,condventa,mproducto){

	if( unidades == 0 )
	    if( !esOnlineBusquedas() )
		return;
	
	var modo      = id("rgModosTicket").value;//Mproducto
	var vprecio   = ( modo == "mproducto")? costo:precio;
	
        prodlist_cb[codigo] = 1;
	
	//Detalle
	var xvence     = ( vence  )? vence[0].split(":") :false;
	var xlote      = ( lote   )? lote[0].split(":")  :false;
	var xserie     = ( serie  )? serie[0].split(":") :false;
	var cssdetalle = ( oferta )? 'font-weight: bold;':'';
	
	//Menudeo
	var xresto    = ( menudeo )? unidades%unidxcont                    : false;
	var xcant     = ( menudeo )? ( unidades - xresto )/unidxcont       : false;
	var xcont     = ( menudeo )? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
	var xmenudeo  = ( menudeo )? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;
	var vdetalle  = '';
	
	switch(condventa){
	case 'CRM' : condventa = "C/RM.";	break;
	case 'CRMR': condventa = "C/RMR."; break;
	default    : condventa = false;
	}
	
	
	vdetalle  = ( mproducto )? '**MIXPRODUCTO** ' : vdetalle;
	vdetalle  = ( oferta    )? '**OFERTA '+ofertaunid+''+unid+' c/u '+formatDinero(pvo)+'** '+vdetalle : vdetalle;
	vdetalle  = ( menudeo   )? vdetalle+xmenudeo : vdetalle;
	vdetalle  = ( ilimitado )? vdetalle+'**STOCK ILIMITADO** ' : vdetalle;
	vdetalle  = ( serie     )? vdetalle+'NS. '+xserie[1].slice(0,30)+' ' : vdetalle;
	vdetalle  = ( vence     )? vdetalle+'FV. '+xvence[1] + ' ' : vdetalle;
	vdetalle  = ( lote      )? vdetalle+'LT. '+xlote[1]  + ' ' : vdetalle;
	vdetalle  = ( servicio  )? '**SERVICIO**' : vdetalle;
	vdetalle  = ( condventa )? vdetalle+' '+condventa : vdetalle;
	
        var xlistadoProductos = id("listaPanelProductos");	
	
        var xref         = document.createElement("label"); 
	xref.setAttribute("value",referencia);
        xref.setAttribute("id","pnlref_"+codigo);
	
        var xdescripcion = document.createElement("label");
	xdescripcion.setAttribute("value",producto);
        xdescripcion.setAttribute("id","pnldescripcion_"+codigo);
	
        var xexistencias = document.createElement("label");
	xexistencias.setAttribute("value",unidades+' '+unid );
        xexistencias.style.textAlign ="right";
        xexistencias.setAttribute("id","pnlstock_"+codigo);
	
        var xprecio      = document.createElement("label");
	xprecio.setAttribute("value",formatDinero(vprecio));	
        xprecio.style.align     ="right";
        xprecio.style.textAlign ="right";
        xprecio.setAttribute("id","pnlprecio_"+codigo);
	
        var xdetalle     = document.createElement("label");
	xdetalle.setAttribute("value",vdetalle);	
        xdetalle.setAttribute("id","pnldetalle_"+codigo);
	xdetalle.setAttribute("style",cssdetalle);
	
        var xlistitem    = document.createElement("listitem");
        xlistitem.setAttribute("id","pnlprod_"+codigo);
	
        xlistitem.appendChild( xref);
        xlistitem.appendChild( xdescripcion);
        xlistitem.appendChild( xdetalle);
        xlistitem.appendChild( xexistencias);
        xlistitem.appendChild( xprecio );
        xlistadoProductos.appendChild( xlistitem );
	xlistitem.setAttribute("tooltiptext",producto);	
        prodlist_tag[iprod] = xlistitem;
        prodlist[iprod++]   = codigo;	 	
    }

/*++++++++++++++++++++++++ PANEL PRODUCTO ++++++++++++++++++++++++++++*/

/*++++++++++++++++++++++++ PANEL CLIENTE  ++++++++++++++++++++++++++++*/
    function panelcargarCliente(xval){

        var theList = id('panelclientPickArea');
	var xid     = id("panelbuscaClienteSelect").value;
	var idpanel = id("panelElijeCliente");

	if(!(xid>1)) return;
	if(!id("panel_user_picker_"+xid))return;

	id("nombreClienteOrdenServicio").value = usuarios[xid].nombre;
	id("idClienteOrdenServicio").value     = xid;
	idpanel.hidePopup();
    }

    function panelbuscarCliente(){

        var busca    = id("panelbuscaCliente").value;
        var n        = usuarios.length;    
        var ns       = new String(busca);
        var theList  = id('panelclientPickArea');
	var xcliente = 0;

        ns    = ns.toUpperCase();
        filas = theList.itemCount;

        for(var i=0;i<filas;i++)
	{
	    theList.removeItemAt(0);
        }

        if(ns=="")
	{
	    for(var i=0;i<filas;i++)
	    {
                theList.removeItemAt(0);
	    }

	    for(var i=0;i<idusuarios.length;i++){
                var idcliente = idusuarios[i];
                var cliente   = theList.getItemAtIndex(0);
                paneladdXUser(usuarios[idcliente].nombre, 
			      usuarios[idcliente].id, 
			      usuarios[idcliente].debe, 
			      usuarios[idcliente].ruc, 
			      usuarios[idcliente].bono, 
			      usuarios[idcliente].credito, 
			      usuarios[idcliente].promo,
			      usuarios[idcliente].tipo,
                              usuarios[idcliente].suscrip);
	    }
        }else{
	    
	    for(var i=0;i<filas;i++)
	    {
                theList.removeItemAt(0);
	    }
	    
	    for(var i=0;i<idusuarios.length;i++)
	    {
                var idcliente = idusuarios[i];
                var ruc       = new String(usuarios[idcliente].ruc);
                var nombre    = new String(usuarios[idcliente].nombre);
                var cliente   = theList.getItemAtIndex(0);

		ruc    = ruc.toUpperCase();
                nombre = nombre.toUpperCase();

                if((nombre.indexOf(ns) != -1) || (ruc.indexOf(ns) != -1) )
		{
		    paneladdXUser(usuarios[idcliente].nombre,
				  usuarios[idcliente].id,
				  usuarios[idcliente].debe, 
				  usuarios[idcliente].ruc, 
				  usuarios[idcliente].bono, 
				  usuarios[idcliente].credito, 
				  usuarios[idcliente].promo,
				  usuarios[idcliente].tipo,
                                  usuarios[idcliente].suscrip);


		    xcliente = ( theList.itemCount == 1 )? usuarios[idcliente].id:0;
		    id("panelbuscaClienteSelect").value = xcliente;
                }
		theList.selectItem(cliente);
	    }
        }

	if( theList.itemCount == 1 && id("panel_user_picker_"+usuarios[idcliente].id) )
	{
	    id("panel_user_picker_"+idcliente).setAttribute("selected",true);
	    id("panelbuscaClienteSelect").value = idcliente;
	}
    }

function paneladdXUser(nombreUser,iduser,debe,ruc,bono,credito,promo,tipo,suscrip){

        var xroot    = id("panelclientPickArea");
        var xclient  =  document.createElement("listitem");
        var xnombre  = document.createElement("listcell");
        var xdebe    = document.createElement("listcell");
        var xicon    = document.createElement("listcell");
        var xbono    = document.createElement("listcell");
        var xpromo   = document.createElement("listcell");
        var xnf      = document.createElement("listcell");
	var txtdebe  = (debe)? cMoneda[1]['S']+" "+formatDinero(debe):"";
	var txtbono  = (bono)? cMoneda[1]['S']+" "+formatDinero(bono):"";
        var txtpromo = ( promo != '0' )? gettxtPromocion( promo ):"";
        var txtsuscrip = ( parseInt(suscrip) == 1)? '  **Suscrito.':'';
	var imgico   = 'gpos_clienteparticular.png';
	imgico = (tipo == 'Empresa')? 'gpos_clienteempresa.png':imgico;
	imgico = (tipo == 'Interno')? 'gpos_tpv_clientecontado.png':imgico;

        xdebe.setAttribute("label",txtdebe);	
        xdebe.setAttribute("value",iduser );
        xdebe.setAttribute("readonly","true");
        xdebe.setAttribute("id","panel_user_picker_debe_"+iduser);

        //xcell0.setAttribute("onclick","pickClient("+iduser+")");	
        xnf.setAttribute("id","panel_user_picker_ruc_"+iduser);
        xnf.setAttribute("label",ruc );	
        xnf.setAttribute("value",iduser );
        xnf.setAttribute("readonly","true");

        xnombre.setAttribute("id","panel_user_picker_nombre_"+iduser);
        xnombre.setAttribute("label",nombreUser+txtsuscrip);	
        xnombre.setAttribute("value",iduser );
        xnombre.setAttribute("readonly","true");


        xicon.setAttribute("label",tipo);	
        xicon.setAttribute("class","listitem-iconic");
        xicon.setAttribute("image","img/"+imgico);
        xicon.setAttribute("id","panel_user_picker_tipo_"+iduser);
        xbono.setAttribute("label",txtbono);	

        xpromo.setAttribute("label",txtpromo);	

        xclient.setAttribute("id","panel_user_picker_"+iduser);
        xclient.setAttribute("value",iduser );	
        xclient.setAttribute("onclick","panelselClient("+iduser+")");	

        xclient.appendChild( xicon );
        xclient.appendChild( xnf );
        xclient.appendChild( xnombre );
        xclient.appendChild( xdebe );
        xclient.appendChild( xbono );
        xclient.appendChild( xpromo );
        xroot.appendChild( xclient);	
    }

    function panelselClient(xdex){
	xdex = parseInt(xdex);
	id("panelbuscaClienteSelect").value = (xdex > 1 )? xdex:0;
    }
/*++++++++++++++++++++++++ PANEL CLIENTE  ++++++++++++++++++++++++++++*/

/*++++++++++++++++++++++++ ORDEN SERVICIO  ++++++++++++++++++++++++++++*/

var oscNewRegMarca   = false;
var ospNewMarca      = '';
var oscNewRegModelo  = false;
var ospNewModelo     = '';
var oscNewRegProdSat = false;
var ospNewProdSat    = '';
var oscNewRegMotivo  = false;
var ospNewMotivo     = '';

var oscEstado        = "";
var oscPrioridad     = "";
var oscTipo          = "";
var osciTipoServicio = 0;
var osciModelos      = 0;
var osciMarcas       = 1;
var osciProductos    = 1;
var osciMotivos      = 1;
var oscContador      = 0;
var oscLista         = "";
var oscDetalleSat    = false;
var cIdOrdenServicio = 0;
var oscIdCliente     = 0;
var oscCliente       = "Elije cliente...";
var oscEstado        = "";
var oscSerie         = 0;
var oscNumero        = 0;
var oscImpuesto      = 0;
var oscImporte       = 0;
var oscFacturacion   = 0;
var oscNuevo         = 'Nuevo';
var oscClon          = false;
var oscIdSuscripcion = 0;

var cIdOrdenServicioDet = 0;
var ilinealistaordenservicio = 0;

var osdEstado      = "";
var osdImporte     = 0;

var osdEstadoEntga = "";

var osdServicioSat = "";

var osdTipoProducto = "";
var osdNumeroSerie  = "";
var osdCodigoBarras = "";

var osdDireccion    = "";
var osdUbicacion    = "";



var cIdProductoSat = 0;
var ospIdMarca     = 0;
var ospIdModeloSat = 0;
var ospIdMotivoSat = 0;
var ospIdProducto  = 0;
var ospMarca       = "";
var ospModeloSat   = "";
var ospMotivoSat   = "";
var ospProducto    = "";
var ospNumeroSerie = "";
var ospDescripcion = "";
var ospSolucion    = "";
var ospDiagnostico = "";
var ospDetalleSat  = 0;
var ospUbicacion   = "";

var ilinealistaordenserviciodet = 0;
var ilinealistaproductodetallesat = 0;

var RevDet = 0;
var regOSDet = '';
var IdOrdenServicioSeleccionada = 0;
var ItemSeleccionada = 0;

// Busqueda avanzada orden servicio detalle
var vosFechaFin       = true;
var vosEstadoSolucion = true;
var vosNumeroSerie    = true;
var vosEstadoGarantia = true;
var vosEstadoEntrega  = true;
var vosFechaEntrega   = true;
var vosUsuarioRegistro= true;
var vosUsuarioEntrega = true;

var editaNumeroOS     = false;

var ordenserviciodet     = new Array();
var ordenserviciodetlist = new Array();
var ordenserviciodetserv = new Array();
var iordenserviciodet    = 0;

var cSubsidiario = '';

function mostrarServicios(xid){
    var xordenservicio = true,xoutsourcing = true;
    switch(xid){
    case "OrdenServicio":
	xordenservicio = false;
	break;
    case "Outsourcing":
	xoutsourcing = false;
	break;
    default :
	return;
    }

    id("boxOrdenservicio").setAttribute("collapsed",xordenservicio);
    id("boxOutsourcing").setAttribute("collapsed",xoutsourcing);

}

function mostrarFormOrdenServicio(xvalue){
    if(xvalue == 'Editar'){
	var idx = id("listadoOrdenServicio").selectedItem;
	if(!idx) return;
    }

    id("btnOrdenServicioDet").setAttribute("collapsed",true);
    id("vboxFormOrdenServicio").setAttribute('collapsed',false);

    var btnOSLabel   = " Aceptar";
    var btnOSCommand = "RegistrarOrdenServicio("+false+")";
    var xtitulo      = "Nueva Incidencia";
    var listboxOS    = false;
    var listboxOSDet = true;
    var btnCancel    = "volverOrdenServicio('"+xvalue+"')";
    var stadoserv    = true;

    switch(xvalue){
    case 'Nuevo':
	cleanFormOrdenServicio();
	cIdOrdenServicio = 0;

	var xlist   = id("listadoOrdenServicio");
	var rowlist = xlist.getRowCount(); 
	
	for (var i = 0; i < rowlist; i++) { 
            kid = id("linealistaordenservicio_"+i);					
            if (kid) kid.removeAttribute('selected');
	}
	id("nombreClienteOrdenServicio").value      = "Elije cliente...";
	obtenerSerieNumeroOrdenServicio();
	id("FiltroPrioridad").value = 1;
	id("numOrdenServicio").focus();
	break;
    case 'Editar':
	xtitulo      = " Modificando Incidencia";
	btnOSLabel   = " Modificar";
	btnOSCommand = "ModificarOrdenServicio()";
	listboxOS    = true;
	listboxOSDet = true;
	stadoserv    = false;
	editaNumeroOS= true;

	id("serieOrdenServicio").value = oscSerie;
	id("numOrdenServicio").value   = oscNumero;
	id("nombreClienteOrdenServicio").value      = oscCliente;
	id("idClienteOrdenServicio").value          = oscIdCliente;
	id("FiltroEstado").value       = oscEstado;
	id("FiltroPrioridad").value    = oscPrioridad;
	id("TipoOrdenServicio").value  = oscTipo;

	mostrarEstadoOrdenServicio();

	break;
    }

    id("btnOrdenServicioCancel").setAttribute("oncommand",btnCancel);
    id("btnOrdenServicioAceptar").setAttribute("label",btnOSLabel);
    id("btnOrdenServicioAceptar").setAttribute("oncommand",btnOSCommand);
    id("resumenOrdenServicio").setAttribute("collapsed",listboxOS);
    id("listadoOrdenServicio").setAttribute("collapsed",listboxOS);
    id("resumenOrdenServicioDetalle").setAttribute("collapsed",listboxOSDet);
    id("listadoOrdenServicioDetalle").setAttribute("collapsed",listboxOSDet);
    id("wtitleFormOrdenServicio").label = xtitulo;
    id("rowEstadoOrdenServicio").setAttribute('collapsed',stadoserv);
}

function obtenerSerieNumeroOrdenServicio(){
    var xrequest  = new XMLHttpRequest();
    var url       = "modulos/ordenservicio/modordenservicio.php?modo=ObtenerSerieNumeroOS";

    xrequest.open("GET",url,false);

    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    var xres    = res.split("~");

    id("serieOrdenServicio").value = xres[0];
    id("numOrdenServicio").value   = xres[1];
}

function mostrarEstadoOrdenServicio(){
    var xpendiente   = true;
    var xejecucion  = true;
    var xcancelado  = true;
    var xfinalizado = true;

    switch(oscEstado){

    case 'Pendiente':
	xcancelado = false;
	break;
    case 'Ejecucion':
	xcancelado = false;
    case 'Finalizado':
	break;
    case 'Cancelado':
	xpendiente = false;
	break;
    }

    id("itmEstadoPendiente").setAttribute('collapsed',xpendiente);
    id("itmEstadoCancelado").setAttribute('collapsed',xcancelado);
}

function cleanFormOrdenServicio(){
    id("nombreClienteOrdenServicio").value = "";
    id("idClienteOrdenServicio").value     = 0;
    id("FiltroEstado").value               = "Pendiente";

}

function calcularFechaActual(xvalue){
    var f = new Date();
    var fecha  = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
    var hora   = f.getHours() + ":" + f.getMinutes() + ":" + f.getSeconds();
    var actual = (xvalue == 'fecha')? fecha : hora;

    return actual;
}

function BuscarOrdenServicio(){
    VaciarBusquedaOrdenServicio();

    var osdesde       = id("FechaBuscaDesde").value;
    var oshasta       = id("FechaBuscaHasta").value;
    var oscliente     = id("NombreBusqueda").value;
    var osusuario     = id("UsuarioBusqueda").value;
    var osestado      = id("EstadoBusqueda").value;
    var oscodigo      = id("CodigoBusqueda").value;
    var osfacturado   = id("EstadoBusquedaFacturacion").value;
    var ostipo        = id("TipoBusqueda").value;

    RawBuscarOrdenServicio(osdesde,oshasta,oscliente,osestado,oscodigo,osfacturado,
			   osusuario,ostipo,AddLineaOrdenServicio);

    RevDet = 0;
}

function VaciarBusquedaOrdenServicio(){
    var oslista = id("listadoOrdenServicio");

    for (var i = 0; i < ilinealistaordenservicio; i++) { 
        kid = id("linealistaordenservicio_"+i);					
        if (kid)	oslista.removeChild( kid ); 
    }
    ilinealistaordenservicio = 0;
}

function RawBuscarOrdenServicio(osdesde,oshasta,oscliente,osestado,oscodigo,osfacturado,
				osusuario,ostipo,FuncionProcesaLineaOrdenServicio){

    var url = "modulos/ordenservicio/modordenservicio.php?modo=ObtenerOrdenServicio"
	+ "&xdesde=" + escape(osdesde)
        + "&xhasta=" + escape(oshasta)
        + "&xcliente=" + escape(oscliente)
        + "&xestado=" + escape(osestado)
        + "&xfact=" + escape(osfacturado)
        + "&xuser=" + escape(osusuario)
        + "&xtipo=" + escape(ostipo)
        + "&xcodigo=" + escape(oscodigo);

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false); 
    obj.send(null);
    
    var IdOrdenServicio,osIdCliente,osIdUsuario,osIdLocal,osIdUsuarioEntrega,osUsuarioEntrega,osUsuario,osCliente,osLocal,osFechaIngreso,osFechaEntrega,osEstado,osSerie,osCodigo,osNumeroOrden,osImpuesto,osImporte,osFacturacion,osPrioridad,osIdSuscripcion;

    var tex = "";
    var cr = "\n";
    var Cliente;

    var node,t,i;
    var totalOrdenServicio  = 0;
    var totalPendiente  = 0;
    var totalEjecucion  = 0;
    var totalFinalizado = 0;
    var totalCancelado  = 0;
    var totalFacturado  = 0;

    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var xml  = obj.responseXML.documentElement;
    var item = xml.childNodes.length;
    var tC   = item;
    var sldoc=false;

    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
	    osIdLocal          = node.childNodes[t++].firstChild.nodeValue;
	    osLocal            = node.childNodes[t++].firstChild.nodeValue;
	    osUsuario          = node.childNodes[t++].firstChild.nodeValue;
	    osIdCliente        = node.childNodes[t++].firstChild.nodeValue;
	    osCliente          = node.childNodes[t++].firstChild.nodeValue;
	    IdOrdenServicio    = node.childNodes[t++].firstChild.nodeValue;
	    osUsuarioEntrega   = node.childNodes[t++].firstChild.nodeValue;
	    osFechaIngreso     = node.childNodes[t++].firstChild.nodeValue;
	    osFechaEntrega     = node.childNodes[t++].firstChild.nodeValue;
	    osEstado           = node.childNodes[t++].firstChild.nodeValue;
	    osCodigo           = node.childNodes[t++].firstChild.nodeValue;
	    osSerie            = node.childNodes[t++].firstChild.nodeValue;
	    osNumeroOrden      = node.childNodes[t++].firstChild.nodeValue;
	    osImpuesto         = node.childNodes[t++].firstChild.nodeValue;
	    osImporte          = node.childNodes[t++].firstChild.nodeValue;  
	    osFacturacion      = node.childNodes[t++].firstChild.nodeValue;
	    osPrioridad        = node.childNodes[t++].firstChild.nodeValue;
	    osTipo             = node.childNodes[t++].firstChild.nodeValue;
	    osIdSuscripcion    = node.childNodes[t++].firstChild.nodeValue;

 	    totalOrdenServicio++;
	    var acomprobante = osFacturacion.split('~');

	    if(osEstado == 'Pendiente') totalPendiente++;
	    if(osEstado == 'Ejecucion') totalEjecucion++;
	    if(osEstado == 'Finalizado') totalFinalizado++;
	    if(osEstado == 'Cancelado') totalCancelado++;
	    if(acomprobante[0] == 1) totalFacturado++;

            FuncionProcesaLineaOrdenServicio(item,osIdLocal,osLocal,osUsuario,osIdCliente,
					     osCliente,IdOrdenServicio,osUsuarioEntrega,
					     osFechaIngreso,osFechaEntrega,osEstado,
					     osCodigo,osSerie,osNumeroOrden,osImpuesto,
					     osImporte,osFacturacion,osPrioridad,osTipo,
					     osIdSuscripcion);

	    item--;
        }
    }

    var srt = (totalOrdenServicio > 1 )? 's':'';
    id("TotalOrdenServicio").value  = totalOrdenServicio+' Incidencia'+srt;
    id("TotalFacturado").value  = totalFacturado;
}

function AddLineaOrdenServicio(item,osIdLocal,osLocal,osUsuario,osIdCliente,
			       osCliente,IdOrdenServicio,osUsuarioEntrega,
			       osFechaIngreso,osFechaEntrega,osEstado,
			       osCodigo,osSerie,osNumeroOrden,osImpuesto,
			       osImporte,osFacturacion,osPrioridad,osTipo,
			       osIdSuscripcion){
    var acomprobante = osFacturacion.split('~');
    var lista    = id("listadoOrdenServicio");

    var xUsuarioEntrega,xUsuario,xCliente,xLocal,xFechaIngreso,xFechaEntrega,xEstado,xSerie,xCodigo,xNumeroOrden,xImpuesto,xImporte,xIdOrdenServicio,xFacturacion,xPrioridad,xTipo,xIdSuscripcion;

    var FechaR,pFechaRegistro,iFechaRegistro,aFechaRegistro,oHoraRegistro,FechaE,pFechaEntrega,aFechaEntrega,oFechaEntrega,oHoraEntrega,vFacturacion;

    vFacturacion = (acomprobante[0] == 1)? acomprobante[2] : 'Pendiente';
    vFacturacion = (osEstado != 'Finalizado')? ' ' : vFacturacion;
    
    //Fecha Registro
    FechaR = osFechaIngreso.split('~');
    pFechaRegistro = FechaR[0];
    iFechaRegistro = FechaR[1];
    aFechaRegistro = iFechaRegistro.split(' ');
    oFechaRegistro = aFechaRegistro[0];
    oHoraRegistro  = aFechaRegistro[1];

    FechaE = osFechaEntrega.split('~');
    pFechaEntrega = FechaE[0];
    iFechaEntrega = FechaE[1];
    aFechaEntrega = iFechaEntrega.split(' ');
    oFechaEntrega = aFechaEntrega[0];
    oHoraEntrega  = aFechaEntrega[1];

    var fEntregra = (pFechaEntrega == '00/00/0000 00:00')? " ":pFechaEntrega
    var lPrioridad;

    if(osPrioridad == 1) lPrioridad = 'Normal';
    if(osPrioridad == 2) lPrioridad = 'Alta';
    if(osPrioridad == 3) lPrioridad = 'Muy Alta';

    xclass        = (item%2)?'parrow':'imparrow';      
    xitem         = document.createElement("listitem");
    xitem.value   = IdOrdenServicio;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistaordenservicio_"+ilinealistaordenservicio);
    xitem.setAttribute("oncontextmenu","seleccionarlineaordenservicio("+ilinealistaordenservicio+",false)");
    ilinealistaordenservicio++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xIdOrdenServicio = document.createElement("listcell");
    xIdOrdenServicio.setAttribute("value",IdOrdenServicio);
    xIdOrdenServicio.setAttribute("collapsed","true");
    xIdOrdenServicio.setAttribute("id","os_idordenservicio_"+IdOrdenServicio);
    

    xLocal = document.createElement("listcell");
    xLocal.setAttribute("label",osLocal);
    xLocal.setAttribute("value",osIdLocal);
    xLocal.setAttribute("style","text-align:left");
    xLocal.setAttribute("id","os_local_"+IdOrdenServicio);

    xCliente = document.createElement("listcell");
    xCliente.setAttribute("label",osCliente);
    xCliente.setAttribute("value",osIdCliente);
    xCliente.setAttribute("style","text-align:left");
    xCliente.setAttribute("id","os_cliente_"+IdOrdenServicio);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label",osUsuario);
    //xUsuario.setAttribute("value",osIdUsuario);
    xUsuario.setAttribute("style","text-align:center");
    xUsuario.setAttribute("collapsed",vosUsuarioRegistro);
    xUsuario.setAttribute("id","os_usuario_"+IdOrdenServicio);

    xUsuarioEntrega = document.createElement("listcell");
    xUsuarioEntrega.setAttribute("label",osUsuarioEntrega);
    //xUsuarioEntrega.setAttribute("value",IdUsuarioEntrega);
    xUsuarioEntrega.setAttribute("style","text-align:left");
    xUsuarioEntrega.setAttribute("collapsed",vosUsuarioEntrega);
    xUsuarioEntrega.setAttribute("id","os_usuarioentrega_"+IdOrdenServicio);

    xFIngreso = document.createElement("listcell");
    xFIngreso.setAttribute("label",pFechaRegistro);
    //xFIngreso.setAttribute("value",IdFIngreso);
    xFIngreso.setAttribute("style","text-align:left");
    xFIngreso.setAttribute("id","os_fingreso_"+IdOrdenServicio);

    xFEntrega = document.createElement("listcell");
    xFEntrega.setAttribute("label",fEntregra);
    //xFEntrega.setAttribute("value",IdFEntrega);
    xFEntrega.setAttribute("style","text-align:left");
    xFEntrega.setAttribute("collapsed",vosFechaEntrega);
    xFEntrega.setAttribute("id","os_fentrega_"+IdOrdenServicio);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label",osEstado);
    xEstado.setAttribute("style","text-align:left");
    xEstado.setAttribute("id","os_estado_"+IdOrdenServicio);

    xCodigo = document.createElement("listcell");
    xCodigo.setAttribute("label",osCodigo);
    xCodigo.setAttribute("style","text-align:left");
    xCodigo.setAttribute("id","os_codigo_"+IdOrdenServicio);

    xSerie = document.createElement("listcell");
    xSerie.setAttribute("label",osSerie);
    xSerie.setAttribute("style","text-align:left");
    xSerie.setAttribute("collapsed",true);
    xSerie.setAttribute("id","os_serie_"+IdOrdenServicio);

    xNumeroOrden = document.createElement("listcell");
    xNumeroOrden.setAttribute("label",osNumeroOrden);
    xNumeroOrden.setAttribute("style","text-align:left");
    xNumeroOrden.setAttribute("collapsed",true);
    xNumeroOrden.setAttribute("id","os_numeroorden_"+IdOrdenServicio);

    xImpuesto = document.createElement("listcell");
    xImpuesto.setAttribute("label",formatDinero(osImpuesto));
    xImpuesto.setAttribute("value",osImpuesto);
    xImpuesto.setAttribute("style","text-align:right");
    xImpuesto.setAttribute("id","os_impuesto_"+IdOrdenServicio);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label",formatDinero(osImporte));
    xImporte.setAttribute("value",osImporte);
    xImporte.setAttribute("style","text-align:right");
    xImporte.setAttribute("id","os_importe_"+IdOrdenServicio);

    xFacturacion = document.createElement("listcell");
    xFacturacion.setAttribute("label",vFacturacion);
    xFacturacion.setAttribute("value",acomprobante[1]);
    xFacturacion.setAttribute("style","text-align:left");
    xFacturacion.setAttribute("id","os_facturacion_"+IdOrdenServicio);

    xPrioridad = document.createElement("listcell");
    xPrioridad.setAttribute("label",lPrioridad);
    xPrioridad.setAttribute("value",osPrioridad);
    xPrioridad.setAttribute("style","text-align:left");
    xPrioridad.setAttribute("id","os_prioridad_"+IdOrdenServicio);

    xTipo = document.createElement("listcell");
    xTipo.setAttribute("label",osTipo);
    xTipo.setAttribute("value",osTipo);
    xTipo.setAttribute("style","text-align:left");
    xTipo.setAttribute("collapsed","true");
    xTipo.setAttribute("id","os_tipo_"+IdOrdenServicio);

    xIdSuscripcion = document.createElement("listcell");
    xIdSuscripcion.setAttribute("value",osIdSuscripcion);
    xIdSuscripcion.setAttribute("style","text-align:left");
    xIdSuscripcion.setAttribute("collapsed","true");
    xIdSuscripcion.setAttribute("id","os_idsuscripcion_"+IdOrdenServicio);


    xitem.appendChild( xnumitem );
    xitem.appendChild( xLocal );
    xitem.appendChild( xCodigo );
    xitem.appendChild( xEstado );
    xitem.appendChild( xPrioridad );
    xitem.appendChild( xFIngreso );
    xitem.appendChild( xFacturacion );
    xitem.appendChild( xCliente );
    xitem.appendChild( xFEntrega );
    xitem.appendChild( xImpuesto );
    xitem.appendChild( xImporte );
    xitem.appendChild( xUsuario );
    xitem.appendChild( xUsuarioEntrega );
    xitem.appendChild( xSerie );
    xitem.appendChild( xNumeroOrden );
    xitem.appendChild( xIdOrdenServicio );
    xitem.appendChild( xTipo );
    xitem.appendChild( xIdSuscripcion );
    lista.appendChild( xitem );	
}

function seleccionarlineaordenservicio(linea,xval){
    var lista = (xval)? id("listadoOrdenServicioDetalle"):id("listadoOrdenServicio");
    var fila  = (xval)? id("linealistaordenserviciodet_"+linea):id("linealistaordenservicio_"+linea);
    lista.selectItem(fila);
}

function RevisarOrdenServicioSeleccionada(){
    if (id("listadoOrdenServicio").getAttribute('disabled'))
	vboxOrdenServicioDisplay('unblock');

    var idex       = id("listadoOrdenServicio").selectedItem;
    
    if(!idex) return;

    cIdOrdenServicio = idex.value;

    oscIdCliente = id("os_cliente_"+idex.value).getAttribute("value");
    oscCliente   = id("os_cliente_"+idex.value).getAttribute("label");
    oscEstado    = id("os_estado_"+idex.value).getAttribute("label");
    oscSerie     = id("os_serie_"+idex.value).getAttribute("label");
    oscNumero    = id("os_numeroorden_"+idex.value).getAttribute("label");
    oscImpuesto  = id("os_impuesto_"+idex.value).getAttribute("value");
    oscImporte   = id("os_importe_"+idex.value).getAttribute("value");
    oscFacturacion = id("os_facturacion_"+idex.value).getAttribute("value");
    oscPrioridad   = id("os_prioridad_"+idex.value).getAttribute("value");
    oscCodigo      = id("os_codigo_"+idex.value).getAttribute("label");
    oscTipo        = id("os_tipo_"+idex.value).getAttribute("value");
    oscIdSuscripcion = id("os_idsuscripcion_"+idex.value).getAttribute("value");
    
    id("btnOrdenServicioDet").setAttribute("collapsed",false);
    id("resumenOrdenServicioDetalle").setAttribute("collapsed",false);
    id("listadoOrdenServicioDetalle").setAttribute("collapsed",false);
    id("vboxFormOrdenServicio").setAttribute("collapsed",true);
    id("btnOrdenServicio").setAttribute("collapsed",false);

    if(RevDet != idex.value)
	crearOrdenServicioDetalle2XML(obtenerOrdenServicioDetalle());

    BuscarDetalleOrdenServicio(idex.value);
    RevDet = idex.value;
    xmenuOrdenServicio();
    xmenuOrdenServicioDetalle();
}

/**** DETALLE ORDEN SERVICIO ******/

function mostrarFormOrdenServicioDet(xvalue,xset){
    if(osdTipoProducto == 'Producto' && xvalue != 'Nuevo'){
	formularioDetalleLinea(false,'EditarOrdenServicio',xvalue);
	return;
    }

    var xtitle              = "";
    var btnlabelcancel      = 'Cancelar';
    var btncollapsedaceptar = false;
    var btniconcancel       = 'img/gpos_cancelar.png';
    oscNuevoServicio        = 'Nuevo';

    var btnOSDetOncommand, btnOSDetLabel, btnOSDetCollapsed;
    regOSDet = xvalue;

    if(servicios.length == 0) 
	return alert("gPOS: Registre por lo menos un servicio en: \n\n "+
                     "      * Admin > Compras > Productos > Nuevo Producto" );

    switch(xvalue){
    case 'Nuevo':
	xtitle            = 'Nuevo Servicio';
	btnOSDetLabel     = 'Aceptar';
	btnOSDetOncommand = "RegistrarOrdenServicioDet()";
	btnOSDetCollapsed = false;
	osdEstado         = 'Pendiente';
	checkServicios();
	if(!oscClon){
	    if(oscEstado == 'Finalizado' || oscEstado == 'Cancelado') return;
	    if(xset) resetFormOrdenServicioDet();
	    ospDetalleSat = 0;
	}
	mostrarUbicacionServicio('Local');
	break;
    case 'Ver':
	var idx = id("listadoOrdenServicioDetalle").selectedItem;
	if(!idx) return;

	xtitle              = 'Detalle Servicio';
	btnOSDetCollapsed   = true;

	btnlabelcancel      = 'Cerrar';
	btncollapsedaceptar = true;

	CargarOrdenServicioDet();
	verOrdenServicioDetalle(false,xvalue);
	verProductoSat(false);

	id("btnCancelServicioDet").setAttribute('label','Cerrar');

	break;
    case 'Editar':
	var idx = id("listadoOrdenServicioDetalle").selectedItem;
	if(!idx) return;

	var estadoOSDet     = ordenserviciodet[ cIdOrdenServicioDet ].estado;
	oscNuevoServicio    = 'Modifica';
	btnOSDetCollapsed   = false;

	btnlabelcancel      = (estadoOSDet == 'Finalizado')? 'Cerrar':btnlabelcancel;
	btncollapsedaceptar = (estadoOSDet == 'Finalizado')? true:btncollapsedaceptar;
	btniconcancel       = (estadoOSDet == 'Finalizado')? 'img/gpos_cancelar.png':btniconcancel;

	btnOSDetLabel       = 'Modificar';
	btnOSDetOncommand   = "ModificarOrdenServicioDet()";
	xtitle              = 'Modificando Servicio';

	CargarOrdenServicioDet();
	mostrarUbicacionServicio(ordenserviciodet[ cIdOrdenServicioDet ].ubicacion);
	verOrdenServicioDetalle(true,xvalue);
	verificarEstadoOrdenServicioDet(estadoOSDet);
	verProductoSat(false);

	break;

    default:
	return;
    }

    id("wtitleFormOrdenServicioDet").setAttribute('label',xtitle);
    id("btnAceptarServicioDet").setAttribute('label',btnOSDetLabel);
    id("btnAceptarServicioDet").setAttribute('oncommand',btnOSDetOncommand);
    id("btnAceptarServicioDet").setAttribute('collapsed',btnOSDetCollapsed);


    id("MostrarInformacionExtraServicio").value = '';
    id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",true);

    mostrarPanelOrdenServicioDet(true,xvalue);
}

function verOrdenServicioDetalle(xver,xedit){
    var xval = (xedit != 'Nuevo')? true:!xver;

    id("rowTipoServicio").setAttribute('collapsed',xval);
    id("rowDescTipoServicio").setAttribute('collapsed',!xval);

    id("rowEstadoOrdenSericioDet").setAttribute('collapsed',!xver);
    id("rowdtnEstadoOrdenServicioDet").setAttribute('collapsed',xver);
    id("rowListaUsuario").setAttribute('collapsed',!xver);
    id("rowdtnListaUsuario").setAttribute('collapsed',xver);
    //id("rowFechaInicioServicio").setAttribute('collapsed',!xver);
    //id("rowdtnFechaInicioServicio").setAttribute('collapsed',xver);
    id("rowFechaFinServicio").setAttribute('collapsed',!xver);
    id("rowdtnFechaFinServicio").setAttribute('collapsed',xver);
    id("rowObservacionServicio").setAttribute('collapsed',!xver);
    id("rowdtnObservacionServicio").setAttribute('collapsed',xver);
    id("rowCantidadServivio").setAttribute('collapsed',!xver);
    id("rowdtnCantidadServivio").setAttribute('collapsed',xver);
    id("rowPrecioServicio").setAttribute('collapsed',!xver);
    id("rowdtnPrecioServicio").setAttribute('collapsed',xver);
    id("rowImporteServicio").setAttribute('collapsed',!xver);
    id("rowdtnImporteServicio").setAttribute('collapsed',xver);
    
    id("rowUbicacionServicio").setAttribute('collapsed',!xver);
    id("rowdtnUbicacionServicio").setAttribute('collapsed',xver);
    id("rowEstadoSolucion").setAttribute('collapsed',!xver);
    id("rowdtnEstadoSolucion").setAttribute('collapsed',xver);
    
    var xgtia = true;
    var ygtia = true;

    if(ordenserviciodet[cIdOrdenServicioDet]){
	xgtia = (trim(ordenserviciodet[cIdOrdenServicioDet].ordenanterior) == '')? true:!xver;
	ygtia = (trim(ordenserviciodet[cIdOrdenServicioDet].ordenanterior) == '')? true:xver;
    }

    id("rowGarantiaCondicion").setAttribute('collapsed',xgtia);
    id("rowdtnGarantiaCondicion").setAttribute('collapsed',ygtia);

    if(osdServicioSat == 0){
	id("rowConceptoServicio").setAttribute('collapsed',!xver);
	id("rowdtnConceptoServicio").setAttribute('collapsed',xver);
    }else
	id("rowConceptoServicio").setAttribute("collapsed",true);

    if(osdUbicacion == 'Externo'){
	id("rowDireccionServicio").setAttribute('collapsed',!xver);
	id("rowdtnDireccionServicio").setAttribute('collapsed',xver);
    }else{
	if(ordenserviciodet[cIdOrdenServicioDet])
	    mostrarUbicacionServicio(ordenserviciodet[ cIdOrdenServicioDet ].ubicacion);
    }

    var xx = ((xedit == 'Ver' || xedit == 'Editar') && osdServicioSat == 0)? true:!xver;
    var yy = ((xedit == 'Ver' || xedit == 'Editar') && osdServicioSat == 0)? true:xver;

    id("rowMotivoSat").setAttribute('collapsed',xx);
    id("rowdtnMotivoSat").setAttribute('collapsed',yy);
    id("rowUbicacionProducto").setAttribute('collapsed',xx);
    id("rowdtnUbicacionProducto").setAttribute('collapsed',yy);

    id("rowDiagnostico").setAttribute('collapsed',xx);
    id("rowdtnDiagnostico").setAttribute('collapsed',yy);
    id("rowResultado").setAttribute('collapsed',xx);
    id("rowdtnResultado").setAttribute('collapsed',yy);


}

function CargarOrdenServicioDet(xvalue){
    var fechaini = ordenserviciodet[ cIdOrdenServicioDet ].fechainicio.split("~");
    var fechafin = ordenserviciodet[ cIdOrdenServicioDet ].fechafin.split("~");
    var xdateini = fechaini[1].split(' ');
    var xdatefin = fechafin[1].split(' ');

    var condgarantia = '';

    if(ordenserviciodet[ cIdOrdenServicioDet ].condiciongarantia == 0) condgarantia = '';
    if(ordenserviciodet[ cIdOrdenServicioDet ].condiciongarantia == 1) condgarantia = 'Aplica';
    if(ordenserviciodet[ cIdOrdenServicioDet ].condiciongarantia == 2) condgarantia = 'No aplica';

    var tiposerv     = ordenserviciodet[ cIdOrdenServicioDet ].tiposervicio.split("~");
    var prodServicio = tiposerv[0];
    var fServicioSat = tiposerv[1];

    var vTipoServicio = ordenserviciodet[ cIdOrdenServicioDet ].idproducto+'~'+fServicioSat;
    var lTipoServicio = prodServicio+'-'+ordenserviciodet[ cIdOrdenServicioDet ].producto;

    var xdir        = trim(ordenserviciodet[ cIdOrdenServicioDet ].direccion);
    var dirServicio = (xdir)? xdir:usuarios[oscIdCliente].dir;

    id("FiltroTipoServicio").value      = vTipoServicio+':'+ordenserviciodet[ cIdOrdenServicioDet ].codigobarras;

    id("FiltroTipoServicio").label      = lTipoServicio;
    id("fInicioAtencionServicio").value = (xdateini[0] == '0000-00-00')? calcularFechaActual('fecha'):xdateini[0];
    id("hInicioAtencionServicio").value = xdateini[1];
    id("fFinAtencionServicio").value    = (xdatefin[0] == '0000-00-00')? calcularFechaActual('fecha'):xdatefin[0];
    id("hFinAtencionServicio").value    = xdatefin[1];
    id("FiltroEstadoOSDet").value       = ordenserviciodet[ cIdOrdenServicioDet ].estado;
    id("listIdUsuario").value           = ordenserviciodet[ cIdOrdenServicioDet ].iduserresponsable;
    id("listGarantiaCondicion").value   = ordenserviciodet[ cIdOrdenServicioDet ].condiciongarantia;
    id("listEstadoSolucion").value      = ordenserviciodet[ cIdOrdenServicioDet ].solucion;
    id("ConceptoServicio").value        = ordenserviciodet[ cIdOrdenServicioDet ].concepto;
    id("CantidadServicio").value        = ordenserviciodet[ cIdOrdenServicioDet ].unidades;
    id("PrecioServicio").value          = ordenserviciodet[ cIdOrdenServicioDet ].precio;
    id("ImporteServicio").value         = ordenserviciodet[ cIdOrdenServicioDet ].importe;
    id("UbicacionServicio").value       = ordenserviciodet[ cIdOrdenServicioDet ].ubicacion;
    id("DireccionServicio").value       = dirServicio;
    id("ObservacionServicio").value     = trim(ordenserviciodet[ cIdOrdenServicioDet ].observaciones);

    id("dctTipoServicio").value         = ordenserviciodet[ cIdOrdenServicioDet ].producto;
    id("dtnEstadoOrdenServicioDet").value = ordenserviciodet[ cIdOrdenServicioDet ].estado;
    id("dtnListaUsuario").value         = ordenserviciodet[ cIdOrdenServicioDet ].responsable;
    id("dtnFechaInicioServicio").value  = fechaini[1];
    id("dtnFechaFinServicio").value     = fechafin[1];
    id("dtnUbicacionServicio").value    = ordenserviciodet[ cIdOrdenServicioDet ].ubicacion;
    id("dtnGarantiaCondicion").value    = condgarantia;
    id("dtnEstadoSolucion").value       = ordenserviciodet[ cIdOrdenServicioDet ].solucion;
    id("dtnObservacionServicio").textContent = ordenserviciodet[ cIdOrdenServicioDet ].observaciones;
    id("dtnImporteServicio").value      = formatDinero(ordenserviciodet[ cIdOrdenServicioDet ].importe);
    id("dtnPrecioServicio").value       = formatDinero(ordenserviciodet[ cIdOrdenServicioDet ].precio);
    id("dtnCantidadServivio").value     = ordenserviciodet[ cIdOrdenServicioDet ].unidades;
    id("dtnDireccionServicio").textContent = ordenserviciodet[ cIdOrdenServicioDet ].direccion;
    id("dtnConceptoServicio").textContent = ordenserviciodet[ cIdOrdenServicioDet ].concepto;

    if(ordenserviciodet[ cIdOrdenServicioDet ].essat == 1){
	var xprod    = ordenserviciodet[ cIdOrdenServicioDet ].productosat.split(';;');
	var xprodsat = xprod[0].split('~');
	id("listMotivoSat").value  = xprodsat[3];
	id("DiagnosticoSat").value = trim(xprodsat[12]);
	id("ResultadoSat").value   = trim(xprodsat[11]);
	id("listUbicacionProducto").value = xprodsat[14];

	id("dtnUbicacionProducto").value    = xprodsat[14];
	id("dtnMotivoSat").value            = xprodsat[7];
	id("dtnDiagnosticoSat").textContent = trim(xprodsat[12]);
	id("dtnResultadoSat").textContent   = trim(xprodsat[11]);
	cIdProductoSat = xprodsat[0];

	CargarProductosSat();
    }
    verTabFormProductoSat(xvalue);
}

function verTabFormProductoSat(xvalue){
    var xprod       = id("FiltroTipoServicio").value.split(":");
    var prodtipo    = xprod[0].split("~");
    var esProdSat   = (prodtipo[1] == 1)? false:true;
    osdCodigoBarras = xprod[1];    

    id("tab-servicios-sat-oc").setAttribute("collapsed",esProdSat);
    id("rowConceptoServicio").setAttribute("collapsed",!esProdSat);
    id("rowUbicacionProducto").setAttribute("collapsed",esProdSat);
    id("titleEvaluacion").setAttribute("collapsed",esProdSat);
    id("rowMotivoSat").setAttribute("collapsed",esProdSat);
    id("rowDiagnostico").setAttribute("collapsed",esProdSat);
    id("rowResultado").setAttribute("collapsed",esProdSat);
    if(esProdSat) id("idDetalleSat").setAttribute("checked",false);
    verificarEstadoOrdenServicioDet('Pendiente');
}

function LoadProductosSat(){
    if(oscNuevoServicio != 'Nuevo')
	setTimeout("CargarProductosSat()",100);
}

function verProductoSat(xver){
    id("rowProducto").setAttribute('collapsed',!xver);
    id("rowdtnProducto").setAttribute('collapsed',xver);
    id("rowDescripcion").setAttribute('collapsed',!xver);
    id("rowdtnDescripcion").setAttribute('collapsed',xver);
    id("rowMarca").setAttribute('collapsed',!xver);
    id("rowdtnMarca").setAttribute('collapsed',xver);
    id("rowModeloSat").setAttribute('collapsed',!xver);
    id("rowdtnModeloSat").setAttribute('collapsed',xver);
    id("rowNumeroSerie").setAttribute('collapsed',!xver);
    id("rowdtnNumeroSerie").setAttribute('collapsed',xver);
}

function CargarProductosSat(){
    if(ordenserviciodet[ cIdOrdenServicioDet ].essat == 1){
	var xprod    = ordenserviciodet[ cIdOrdenServicioDet ].productosat.split(";;");
	var xprodsat = xprod[0].split('~');

	id("tab-servicios-sat-oc").setAttribute('collapsed',false);

	if(!oscClon)
	    id("idDetalleSat").setAttribute("disabled",true);

	id("dtnProducto").value    = xprodsat[8];
	id("dtnDescripcion").textContent = xprodsat[10];
	id("dtnMarca").value       = xprodsat[5];
	id("dtnModeloSat").value   = xprodsat[6];
	id("dtnNumeroSerie").value = xprodsat[9];

	id("listProductoSat").value= xprodsat[4];
	id("DescripcionSat").value = xprodsat[10];
	id("listMarca").value      = xprodsat[1];
	id("listModeloSat").value  = xprodsat[2];
	id("NumeroSerieSat").value = xprodsat[9];

	if(xprodsat[13] == 1) {
	    id("idDetalleSat").setAttribute("checked",true);
	    RevisarServicioProductoSatDet();
	    vertabProductoDetalleSat(true);
	    id("itemAgregarProductoSatDet").setAttribute('disabled',true);
	    id("itemModificarProductoSatDet").setAttribute('disabled',true);
	}
    }
}

function mostrarPanelOrdenServicioDet(xval=false,xedit=false){
    if((oscFacturacion != 0) && (xedit!='Ver') && !oscClon) return;
    vboxOrdenServicioDisplay('block');

    id("tab-servicios-sat-oc").setAttribute("selected",false);
    id("tab-servicios-sat-oc").setAttribute("visuallyselected",false);
    
    id("tab-servicios-oc").setAttribute("selected",true);
    id("tab-servicios-oc").setAttribute("visuallyselected",true);
    id("tab-boxservicios").setAttribute("selectedIndex",0);

    var xleft   = parseInt( window.screen.width)/2;
    var xtop    = parseInt( window.screen.height)/2;
    var idpanel = id("boxFormOrdenServicioDet");

    xleft  = xleft - 340;
    xtop   = xtop - 210;
    idpanel.openPopupAtScreen(xleft,xtop,false);
}

function mostrarPanelElijeCliente(){

    var xleft   = parseInt(window.screen.width)/2;
    var xtop    = parseInt(window.screen.height)/2;
    var idpanel = id("panelElijeCliente");

    xleft  = xleft - 380;
    xtop   = xtop - 160;

    id("panelbuscaCliente").value='';
    id("panelbuscaClienteSelect").value = 0;
    panelbuscarCliente();

    idpanel.openPopupAtScreen(xleft,xtop,false);
    id("panelbuscaCliente").focus();
}


function cleanFormOrdenServicioDet(){
    var Fecha = calcularFechaActual('fecha');

    id("fInicioAtencionServicio").value = Fecha;
    id("hInicioAtencionServicio").value = '00:00:00';
    id("fFinAtencionServicio").value    = Fecha;
    id("hFinAtencionServicio").value    = '00:00:00';
    id("FiltroEstadoOSDet").value       = 'Pendiente';
    id("listIdUsuario").value           = 0;
    id("ConceptoServicio").value        = '';
    id("CantidadServicio").value        = 1;
    id("wtitleFormOrdenServicioDet").label = 'Nuevo Servicio';
    id("UbicacionServicio").value       = 'Local';
    id("DireccionServicio").value       = (oscIdCliente != 0)? usuarios[oscIdCliente].dir:'';
    id("ObservacionServicio").value     = '';
    id("idDetalleSat").setAttribute('checked',false);
    mostrarUbicacionServicio('Local');
}

function cleanFormProductoSat(){
    id("idDetalleSat").setAttribute("checked",false);
    id("idDetalleSat").removeAttribute("disabled");
    id("ResultadoSat").value       = "";
    id("DiagnosticoSat").value     = "";
    id("NumeroSerieSat").value     = "";
    id("DescripcionSat").value     = "";
    id("listProductoSat").value    = (id("listProductoSat").itemCount > 1)? 1:0;
    id("listProductoDetSat").value = (id("listProductoDetSat").itemCount > 1)? 1:0;
    id("listMotivoSat").value      = (id("listMotivoSat").itemCount > 1)? 1:0;
    id("NumeroSerieDetSat").value  = "";
    VaciarDeHijosTag("listadoProductoDetalleSat","esMov");
}

function VaciarBuscarDetalleOrdenServicio(){
    var oslista = id("listadoOrdenServicioDetalle");

    for (var i = 0; i < ilinealistaordenserviciodet; i++) { 
        kid = id("linealistaordenserviciodet_"+i);					
        if (kid)	oslista.removeChild( kid ); 
    }
    ilinealistaordenserviciodet = 0;
}

function BuscarDetalleOrdenServicio(idordenservicio){
    VaciarBuscarDetalleOrdenServicio();
    RawBuscarDetalleOrdenServicio(idordenservicio,AddLineaOrdenServicioDetalle);
}

function RawBuscarDetalleOrdenServicio(idordenservicio,
				       FuncionProcesaLineaOrdenServicioDetalle){
    if(!ordenserviciodetserv[ cIdOrdenServicio ]) return;
    var IdOrdenServicioDet,osIdProducto,osProducto,osIdUsuarioResponsable,osIdComprobante,osFechaInicio,osFechaFin,osEstado,osGarantia,osEstadoGarantia,osGarantiaCondicion,osEstadoSolucion,osConcepto,osNumeroSerie,osUnidades,osPrecio,osImporte,osUsuarioResponsable,osTipoServicio,osTipoProducto,osCodigoBarras,osUbicacion,osDireccion,osObservacion,osProdSat;

    var item = 0,i,j;

    for (i=0; i<ordenserviciodetserv[ cIdOrdenServicio ].length; i++) {
	item++;
	j = ordenserviciodetserv[ cIdOrdenServicio ][i];

	IdOrdenServicioDet    = ordenserviciodet[ j ].idordenserviciodet;
	osIdProducto          = ordenserviciodet[ j ].idproducto;
	osIdUserResponsable   = ordenserviciodet[ j ].iduserresponsable;
	osIdComprobante       = ordenserviciodet[ j ].idcomprobante;
	osFechaInicio         = ordenserviciodet[ j ].fechainicio;
	osFechaFin            = ordenserviciodet[ j ].fechafin;
	osEstado              = ordenserviciodet[ j ].estado;
	osGarantia	      = ordenserviciodet[ j ].garantia;
	osEstadoGarantia      = ordenserviciodet[ j ].estadogarantia;
	osGarantiaCondicion   = ordenserviciodet[ j ].condiciongarantia;
	osEstadoSolucion      = ordenserviciodet[ j ].solucion;
	osConcepto            = ordenserviciodet[ j ].concepto;
	osNumeroSerie         = ordenserviciodet[ j ].serie;
	osUnidades            = ordenserviciodet[ j ].unidades;
	osPrecio              = ordenserviciodet[ j ].precio;
	osImporte             = ordenserviciodet[ j ].importe;
	osProducto            = ordenserviciodet[ j ].producto;
	osUsuarioResponsable  = ordenserviciodet[ j ].responsable;
	osTipoServicio        = ordenserviciodet[ j ].tiposervicio;
	osTipoProducto        = ordenserviciodet[ j ].tipoproducto;
	osCodigoBarras        = ordenserviciodet[ j ].codigobarras;
	osUbicacion           = ordenserviciodet[ j ].ubicacion;
	osDireccion           = ordenserviciodet[ j ].direccion;
	osObservacion         = ordenserviciodet[ j ].observaciones;
	osProdSat             = ordenserviciodet[ j ].essat;
	
        FuncionProcesaLineaOrdenServicioDetalle(item,IdOrdenServicioDet,osIdProducto,
						osIdUserResponsable,osIdComprobante,
						osFechaInicio,osFechaFin,osEstado,
						osGarantia,osEstadoGarantia,
						osGarantiaCondicion,osEstadoSolucion,
						osConcepto,osNumeroSerie,
						osUnidades,osPrecio,osImporte,osProducto,
						osUsuarioResponsable,osTipoServicio,
						osTipoProducto,osCodigoBarras,
						osUbicacion,osDireccion,osObservacion,
						osProdSat);

							         
    }						           
}

function AddLineaOrdenServicioDetalle(item,IdOrdenServicioDet,osIdProducto,
				      osIdUserResponsable,osIdComprobante,
				      osFechaInicio,osFechaFin,osEstado,
				      osGarantia,osEstadoGarantia,
				      osGarantiaCondicion,osEstadoSolucion,
				      osConcepto,osNumeroSerie,
				      osUnidades,osPrecio,osImporte,osProducto,
				      osUsuarioResponsable,osTipoServicio,
				      osTipoProducto,osCodigoBarras,osUbicacion,
				      osDireccion,osObservacion,osProdSat){
    
    var lista    = id("listadoOrdenServicioDetalle");

    var xProducto,xUsuarioResponsable,xComprobante,xFechaInicio,xFechaFin,xEstado,xGarantia,xEstadoGarantia,xGarantiaCondicion,xEstadoSolucion,xConcepto,xNumeroSerie,xUnidaes,xPrecio,xTipoServicio,xTipoProducto,xCodigoBarras,xUbicacion,xDireccion,xObservacion,xProdSat;
    osConcepto           = osConcepto.replace(/    /g, ' ');
    osConcepto           = osConcepto.replace(/   /g, ' ');
    osConcepto           = osConcepto.replace(/  /g, ' ');
    
    //filtro Producto
    if(osTipoProducto == 'Producto'){
	osEstado = "";
	osEstadoSolucion = "";
	osEstadoGarantia = "";
    }

    //Fecha Inicio
    var FechaI         = osFechaInicio.split('~');
    var vFechaInicio   = (osTipoProducto == 'Producto')? '':FechaI[0];
    var zFechaInicio   = vFechaInicio.split(' ');
    var yFechaInicio   = zFechaInicio[0];
    var iFechaInicio   = FechaI[1];
    var aFechaInicio   = iFechaInicio.split(' ');
    var oFechaInicio   = aFechaInicio[0];
    var oHoraInicio    = aFechaInicio[1];
    vFechaInicio       = (FechaI[1] != '0000-00-00 00:00:00')? vFechaInicio:'';

    //Fecha Fin
    var FechaF      = osFechaFin.split('~');
    var vFechaFin   = (osTipoProducto == 'Producto')? '':FechaF[0];
    var zFechaFin   = vFechaFin.split(' ');
    var yFechaFin   = zFechaFin[0];
    var iFechaFin   = FechaF[1];
    var aFechaFin   = iFechaFin.split(' ');
    var oFechaFin   = aFechaFin[0];
    var oHoraFin    = aFechaFin[1];
    vFechaFin       = (FechaF[1] != '0000-00-00 00:00:00')? vFechaFin:'';

    // Tipo servicio
    var tiposerv = osTipoServicio.split("~");
    var prodServicio = tiposerv[0];
    var fServicioSat = tiposerv[1];

    var vTipoServicio = osIdProducto+'~'+fServicioSat;
    var lTipoServicio = prodServicio+'-'+osProducto;

    xclass       = (item%2)?'imparrow':'parrow';  
    xitem        = document.createElement("listitem");
    xitem.value  = IdOrdenServicioDet;

    xitem.setAttribute('class',xclass);
    xitem.setAttribute("id","linealistaordenserviciodet_"+ilinealistaordenserviciodet);
    xitem.setAttribute("oncontextmenu","seleccionarlineaordenservicio("+ilinealistaordenserviciodet+",true)");
    ilinealistaordenserviciodet++;

    xnumitem = document.createElement("listcell");
    xnumitem.setAttribute("label",'  '+item+'.');
    xnumitem.setAttribute("style","text-align:left");

    xProducto = document.createElement("listcell");
    xProducto.setAttribute("label",osProducto);
    xProducto.setAttribute("value",osIdProducto);
    xProducto.setAttribute("collapsed",true);
    xProducto.setAttribute("id","osd_producto_"+IdOrdenServicioDet);

    xUsuario = document.createElement("listcell");
    xUsuario.setAttribute("label",osUsuarioResponsable);
    xUsuario.setAttribute("value",osIdUserResponsable);
    xUsuario.setAttribute("style","text-align:left");
    xUsuario.setAttribute("id","osd_usuariores_"+IdOrdenServicioDet);

    xFechaInicio = document.createElement("listcell");
    xFechaInicio.setAttribute("label",vFechaInicio);
    xFechaInicio.setAttribute("value",iFechaInicio);
    xFechaInicio.setAttribute("style","text-align:left");
    xFechaInicio.setAttribute("id","osd_fechainicio_"+IdOrdenServicioDet);

    xFechaFin = document.createElement("listcell");
    xFechaFin.setAttribute("label",vFechaFin);
    xFechaFin.setAttribute("value",iFechaFin);
    xFechaFin.setAttribute("style","text-align:left");
    xFechaFin.setAttribute("collapsed",vosFechaFin);
    xFechaFin.setAttribute("id","osd_fechafin_"+IdOrdenServicioDet);

    xConcepto = document.createElement("listcell");
    xConcepto.setAttribute("label",osConcepto);
    xConcepto.setAttribute("style","text-align:left");
    xConcepto.setAttribute("id","osd_concepto_"+IdOrdenServicioDet);

    xEstado = document.createElement("listcell");
    xEstado.setAttribute("label",osEstado);
    xEstado.setAttribute("style","text-align:center");
    xEstado.setAttribute("id","osd_estado_"+IdOrdenServicioDet);

    xGarantia = document.createElement("listcell");
    xGarantia.setAttribute("label",osGarantia);
    xGarantia.setAttribute("style","text-align:left");
    xGarantia.setAttribute("collapsed",true);
    xGarantia.setAttribute("id","osd_garantia_"+IdOrdenServicioDet);

    xEstadoGarantia = document.createElement("listcell");
    xEstadoGarantia.setAttribute("label",osEstadoGarantia);
    xEstadoGarantia.setAttribute("style","text-align:left");
    xEstadoGarantia.setAttribute("collapsed",vosEstadoGarantia);
    xEstadoGarantia.setAttribute("id","osd_estadogarantia_"+IdOrdenServicioDet);

    xGarantiaCondicion = document.createElement("listcell");
    xGarantiaCondicion.setAttribute("label",osGarantiaCondicion);
    xGarantiaCondicion.setAttribute("style","text-align:left");
    xGarantiaCondicion.setAttribute("collapsed",true);
    xGarantiaCondicion.setAttribute("id","osd_garantiacondicion_"+IdOrdenServicioDet);

    xEstadoSolucion = document.createElement("listcell");
    xEstadoSolucion.setAttribute("label",osEstadoSolucion);
    xEstadoSolucion.setAttribute("style","text-align:left");
    xEstadoSolucion.setAttribute("collapsed",vosEstadoSolucion);
    xEstadoSolucion.setAttribute("id","osd_estadosolucion_"+IdOrdenServicioDet);

    xNumeroSerie = document.createElement("listcell");
    xNumeroSerie.setAttribute("label",osNumeroSerie);
    xNumeroSerie.setAttribute("style","text-align:left");
    xNumeroSerie.setAttribute("collapsed",vosNumeroSerie);
    xNumeroSerie.setAttribute("id","osd_numeroserie_"+IdOrdenServicioDet);

    xUnidades = document.createElement("listcell");
    xUnidades.setAttribute("label",osUnidades);
    xUnidades.setAttribute("style","text-align:right");
    xUnidades.setAttribute("id","osd_unidades_"+IdOrdenServicioDet);

    xPrecio = document.createElement("listcell");
    xPrecio.setAttribute("label",formatDinero(osPrecio));
    xPrecio.setAttribute("value",osPrecio);
    xPrecio.setAttribute("style","text-align:right");
    xPrecio.setAttribute("id","osd_precio_"+IdOrdenServicioDet);

    xImporte = document.createElement("listcell");
    xImporte.setAttribute("label",formatDinero(osImporte));
    xImporte.setAttribute("value",osImporte);
    xImporte.setAttribute("style","text-align:right");
    xImporte.setAttribute("id","osd_importe_"+IdOrdenServicioDet);

    xUsuarioResponsable = document.createElement("listcell");
    xUsuarioResponsable.setAttribute("label",osUsuarioResponsable);
    xUsuarioResponsable.setAttribute("value",osIdUserResponsable);
    xUsuarioResponsable.setAttribute("style","text-align:center");
    xUsuarioResponsable.setAttribute("id","osd_usuarioresp_"+IdOrdenServicioDet);

    xComprobante = document.createElement("listcell");
    xComprobante.setAttribute("value",osIdComprobante);
    xComprobante.setAttribute("style","text-align:left");
    xComprobante.setAttribute("collapsed",true);
    xComprobante.setAttribute("id","osd_comprobante_"+IdOrdenServicioDet);

    xTipoServicio = document.createElement("listcell");
    xTipoServicio.setAttribute("value",vTipoServicio);
    xTipoServicio.setAttribute("label",lTipoServicio);
    xTipoServicio.setAttribute("style","text-align:left");
    xTipoServicio.setAttribute("collapsed",true);
    xTipoServicio.setAttribute("id","osd_tiposervicio_"+IdOrdenServicioDet);

    xServicioSat = document.createElement("listcell");
    xServicioSat.setAttribute("value",fServicioSat);
    xServicioSat.setAttribute("style","text-align:left");
    xServicioSat.setAttribute("collapsed",true);
    xServicioSat.setAttribute("id","osd_serviciosat_"+IdOrdenServicioDet);

    xTipoProducto = document.createElement("listcell");
    xTipoProducto.setAttribute("value",osTipoProducto);
    xTipoProducto.setAttribute("style","text-align:left");
    xTipoProducto.setAttribute("collapsed",true);
    xTipoProducto.setAttribute("id","osd_tipoproducto_"+IdOrdenServicioDet);

    xCodigoBarras = document.createElement("listcell");
    xCodigoBarras.setAttribute("value",osCodigoBarras);
    xCodigoBarras.setAttribute("style","text-align:left");
    xCodigoBarras.setAttribute("collapsed",true);
    xCodigoBarras.setAttribute("id","osd_codigobarras_"+IdOrdenServicioDet);

    xUbicacion = document.createElement("listcell");
    xUbicacion.setAttribute("value",osUbicacion);
    xUbicacion.setAttribute("style","text-align:left");
    xUbicacion.setAttribute("collapsed",true);
    xUbicacion.setAttribute("id","osd_ubicacion_"+IdOrdenServicioDet);

    xDireccion = document.createElement("listcell");
    xDireccion.setAttribute("value",trim(osDireccion));
    xDireccion.setAttribute("style","text-align:left");
    xDireccion.setAttribute("collapsed",true);
    xDireccion.setAttribute("id","osd_direccion_"+IdOrdenServicioDet);

    xObservacion = document.createElement("listcell");
    xObservacion.setAttribute("value",trim(osObservacion));
    xObservacion.setAttribute("style","text-align:left");
    xObservacion.setAttribute("collapsed",true);
    xObservacion.setAttribute("id","osd_observacion_"+IdOrdenServicioDet);

    xProdSat = document.createElement("listcell");
    xProdSat.setAttribute("label",osProdSat);
    xProdSat.setAttribute("collapsed",true);
    xProdSat.setAttribute("id","osd_prodsat_"+IdOrdenServicioDet);

    xitem.appendChild( xnumitem );
    xitem.appendChild( xConcepto );
    xitem.appendChild( xFechaInicio );
    xitem.appendChild( xFechaFin );
    xitem.appendChild( xEstado );
    xitem.appendChild( xEstadoGarantia );
    xitem.appendChild( xEstadoSolucion );
    xitem.appendChild( xNumeroSerie );
    xitem.appendChild( xUnidades );
    xitem.appendChild( xPrecio );
    xitem.appendChild( xImporte );
    xitem.appendChild( xUsuarioResponsable );
    xitem.appendChild( xGarantia );
    xitem.appendChild( xGarantiaCondicion );
    xitem.appendChild( xComprobante );
    xitem.appendChild( xTipoServicio );
    xitem.appendChild( xServicioSat );
    xitem.appendChild( xProducto );
    xitem.appendChild( xTipoProducto );
    xitem.appendChild( xCodigoBarras );
    xitem.appendChild( xUbicacion );
    xitem.appendChild( xDireccion );
    xitem.appendChild( xObservacion );
    xitem.appendChild( xProdSat );
    lista.appendChild( xitem );

}

function RevisarOrdenServicioDetSeleccionada(){
    if (id("listadoOrdenServicioDetalle").getAttribute('disabled'))
	vboxOrdenServicioDisplay('unblock');

    VaciarDeHijosTag("listadoProductoDetalleSat","esMov");

    var idex       = id("listadoOrdenServicioDetalle").selectedItem;
    if(!idex) return;

    cIdOrdenServicioDet = idex.value;

    osdEstado       = ordenserviciodet[ cIdOrdenServicioDet ].estado;
    osdImporte      = ordenserviciodet[ cIdOrdenServicioDet ].importe;
    osdServicioSat  = ordenserviciodet[ cIdOrdenServicioDet ].essat;
    osdTipoProducto = ordenserviciodet[ cIdOrdenServicioDet ].tipoproducto;
    osdNumeroSerie  = ordenserviciodet[ cIdOrdenServicioDet ].serie;
    osdCodigoBarras = ordenserviciodet[ cIdOrdenServicioDet ].codigobarras;
    osdDireccion    = ordenserviciodet[ cIdOrdenServicioDet ].direccion;
    osdUbicacion    = ordenserviciodet[ cIdOrdenServicioDet ].ubicacion;

    xmenuOrdenServicioDetalle();
}

function RevisarServicioProductoSatDet(){
    cleanListaOrdenServicioSatDet();
    var xprodsat = ordenserviciodet[ cIdOrdenServicioDet ].productosat.split(';;');
    var xproddet = xprodsat[1].split('::');

    for (i=0; i<xproddet.length; i++) {
	var detitem = xproddet[i].split('~');
	IdProductoSatDet  = detitem[0];
	psdIdMarcaSat     = detitem[1];
	psdIdModeloSat    = detitem[2];
	psdIdProductoSat  = detitem[3];
	psdMarcaSat       = detitem[4];
	psdModeloSat      = detitem[5];
	psdProductoSat    = detitem[6];
	psdNSSat          = detitem[7];

	GenerarListaProductoDetSat(psdIdMarcaSat,psdMarcaSat,psdIdModeloSat,psdModeloSat,
				   psdIdProductoSat,psdProductoSat,psdNSSat,
				   IdProductoSatDet);
    }
}

function cleanListaOrdenServicioSatDet(){
    var oslista = id("listadoProductoDetalleSat");
    
    for (var i = 0; i < oscContador; i++) { 
        kid = id("os_lista_"+i);					
        if (kid)	oslista.removeChild( kid ); 
    }
    
    oscContador = 0;
}

function mostrarProductoSat(){
    verTabFormProductoSat();
    id("PrecioServicio").value = productos[osdCodigoBarras].pvd;
    id("ConceptoServicio").value = id("FiltroTipoServicio").label;
    calcularImporteOSDet();
}

function vertabProductoDetalleSat(xvalue){
    id("listProductoSatDetalle").setAttribute('collapsed',!xvalue);
    id("formProductoSatDetalle").setAttribute('collapsed',true);
}

function cancelProductoDetSat(){
    vertabProductoDetalleSat(true);
}

function volverOrdenServicio(xvalue){
    cleanFormOrdenServicio();

    if(xvalue == 'Editar'){
	id("resumenOrdenServicio").setAttribute("collapsed",false);
	id("listadoOrdenServicio").setAttribute("collapsed",false);
	id("resumenOrdenServicioDetalle").setAttribute("collapsed",false);
	id("listadoOrdenServicioDetalle").setAttribute("collapsed",false);
	id("btnOrdenServicioDet").setAttribute("collapsed",false);
    }

    id("vboxFormOrdenServicio").setAttribute("collapsed",true);
    id("btnOrdenServicio").setAttribute("collapsed",false);

}

function AgregarProductoOrdenServicio(xesserie){
    var osConcepto     = id("suscripLineaConcepto").value;
    var osIdProducto   = id("suscripProductoServicio").value;
    var osCantidad     = id("suscripLineaCantidad").value;
    var osPrecio       = id("suscripLineaPrecio").value;
    var osDescuento    = id("suscripLineaDescuento").value;
    var osImporte      = id("suscripLineaImporte").value;
    var osvFechaInicio = "0000-00-00 00:00:00";
    var osvFechaFin    = "0000-00-00 00:00:00";
    var osIdUsuarioRes = 0;
    var osEstado       = 'Pendiente';
    var osUbicacion    = 'Local';
    var osNumeroSerie  = id("OrdenServicioNumeroSerie").value;
    var osCodigoBarras = id("suscripProductoCodigoBarras").value;
    var osCodReferencia= id("suscripProductoCodigoReferencia").value;

    var existeProducto = validarOSExisteProducto(osIdProducto);

    if(existeProducto != 0) {
	id("panelSuscripcionLinea").hidePopup();
	return;
    }

    osNumeroSerie = (xesserie)? validarOSNumeroSerie(osNumeroSerie,osCantidad,
						     osCodigoBarras):'';
    if(osNumeroSerie == '~' || cIdOrdenServicioDet == 0) return;

    var url       = "modulos/ordenservicio/modordenservicio.php?modo=CreaOrdenServicioDet"+
	            "&xidps="+osIdProducto+
                    "&xfinit="+osvFechaInicio+
                    "&xffin="+osvFechaFin+
                    "&xestado="+osEstado+
	            "&xidures="+osIdUsuarioRes+
	            "&xcpto="+osConcepto+
	            "&xns="+osNumeroSerie+
	            "&xcant="+osCantidad+
	            "&xprecio="+osPrecio+
 	            "&ximpte="+osImporte+
	            "&xtipoprod=Producto"+
	            "&xubi="+osUbicacion+
	            "&xcb="+osCodigoBarras+
	            "&xref="+osCodReferencia+
	            "&xidos="+cIdOrdenServicio+
	            "&xidosdet="+cIdOrdenServicioDet;

    var xrequest  = new XMLHttpRequest();
    xrequest.open("GET",url,false);

    id("panelSuscripcionLinea").hidePopup();

    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;
    }

    if(!(res))
	return alert(po_servidorocupado);

    var xosdet = res.split('~');
    cIdOrdenServicioDet = parseInt(xosdet[0]);

    var osImporteOS  = parseFloat(oscImporte) + parseFloat(osImporte);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);
    oscImpuesto = osImpuestoOS;
    oscImporte  = osImporteOS;

    actualizarListaOrdenServicio(oscSerie,oscNumero,oscCliente,oscIdCliente,oscEstado,
				 osImpuestoOS,osImporteOS,oscPrioridad);

    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    BuscarDetalleOrdenServicio(cIdOrdenServicio);
}

function validarOSExisteProducto(osIdProducto){
    var url = "modulos/ordenservicio/modordenservicio.php?modo=VerificaProductoOrdenServicioDet"+    
	            "&xidps="+osIdProducto+
	            "&xidosd="+cIdOrdenServicioDet;

    var xrequest  = new XMLHttpRequest();
    xrequest.open("GET",url,false);

    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;
    }
    
    return res;
}

function validarOSNumeroSerie(xseries,xcantidad,xcod){

    var aNumeroSerie    = xseries.split(',');
    var loadNumeroSerie = Array();

    aNumeroSerie = unique(aNumeroSerie);
    var t = '';
    var msj = '';

    if(xcantidad != aNumeroSerie.length || trim(xseries) == ''){
	msj = 'Nro Series no es igual a Cantidad';
	t = t+'~';
    }

    var aseries = Array();
    var pseries = Array();
    var totalseries = "";
    var osdSeries = productos[xcod].serie;

    for(var x=0; x<osdSeries.length; x++){
	pseries = osdSeries[x].split(":");
	aseries = pseries[1].split(",");
	totalseries = (!totalseries)? aseries:totalseries+','+aseries;
    }
    totalseries = totalseries.toString();
    totalseries = totalseries.split(',');

    for(var i = aNumeroSerie.length - 1 ;i>=0; i--){

	var index = totalseries.indexOf(aNumeroSerie[i])

	if(index == -1)
	{
	    var xindex = aNumeroSerie.indexOf(aNumeroSerie[i]);
	    aNumeroSerie.splice(xindex,1);
	    t = t+'~';
	    msj = 'Algunas series no coinciden';
	    
	} 
	else
	{
	    //Agrega IdpedidoDet por Serie
	    for(var x=0; x < osdSeries.length; x++)
	    {
		pseries = osdSeries[x].split(":");
		aseries = pseries[1].split(",");

		//busca serie
		if( !( loadNumeroSerie.indexOf( pseries[0]+':'+aNumeroSerie[i]) != -1 ) )
		    for(var z=0; z<aseries.length; z++){

			if( !( loadNumeroSerie.indexOf( pseries[0]+':'+aNumeroSerie[i]) != -1 ) )
			    
			    if ( aseries.indexOf( aNumeroSerie[i] ) != -1 )
				loadNumeroSerie.push( pseries[0]+':'+aNumeroSerie[i] );//add serie
		    }
	    }
	}
    }


    if(t != ''){
	id("rowMostrarInformacionExtra").setAttribute('collapsed',false);
	id("MostrarInformacionExtra").value = msj;
	id("OrdenServicioNumeroSerie").value = aNumeroSerie.toString();
	id("OrdenServicioNumeroSerie").focus();
	return '~';	
    }

    //return aNumeroSerie.toString();
    return loadNumeroSerie.toString()
}


function ModificarServicioProducto(){
    var osConcepto     = id("suscripLineaConcepto").value;
    var osCantidad     = id("suscripLineaCantidad").value;
    var osPrecio       = id("suscripLineaPrecio").value;
    var osDescuento    = id("suscripLineaDescuento").value;
    var osImporte      = id("suscripLineaImporte").value;
    var osvFechaInicio = "0000-00-00 00:00:00";
    var osvFechaFin    = "0000-00-00 00:00:00";
    var osIdUsuarioRes = 0;
    var osEstado       = 'Pendiente';
    var osNumeroSerie  = id("OrdenServicioNumeroSerie").value;
    var osEtdoEntrega  = 'Almacen';
    var osEtdoSolucion = 'Completa';
    var osGtiaCondicion= 0;

    osNumeroSerie = (trim(osdNumeroSerie) != '')? validarOSNumeroSerie(osNumeroSerie,
								       osCantidad,
								       ordenserviciodet[ cIdOrdenServicioDet ].codigobarras):'';
    if(osNumeroSerie == '~') return;

    var data  = "";
    var url   = "modulos/ordenservicio/modordenservicio.php?modo=ModificaOrdenServicioDet";
    data = data + "&xfinit="+osvFechaInicio;
    data = data + "&xffin="+osvFechaFin;
    data = data + "&xestado="+osEstado;
    data = data + "&xidures="+osIdUsuarioRes;
    data = data + "&xcpto="+osConcepto;
    data = data + "&xns="+osNumeroSerie;
    data = data + "&xcant="+osCantidad;
    data = data + "&xprecio="+osPrecio;
    data = data + "&ximpte="+osImporte;
    data = data + "&xstdoentrega="+osEtdoEntrega;
    data = data + "&xstdosol="+osEtdoSolucion;
    data = data + "&xgtiacond="+osGtiaCondicion;
    data = data + "&xtipoprod=Producto";
    data = data + "&xidos="+cIdOrdenServicio;
    data = data + "&xidosdet="+cIdOrdenServicioDet;
    data = data + "&xubi=Local";
    data = data + "&xtienesat=0";

    var xrequest  = new XMLHttpRequest();
    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    var res = '';
    id("panelSuscripcionLinea").hidePopup();

    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        res = false;
    }

    if(!(res))
	return alert(po_servidorocupado);

    if(ordenserviciodet[ cIdOrdenServicioDet ].essat == 1 && ordenserviciodet[ cIdOrdenServicioDet ].tipoproducto == 'Servicio') ModificarProductoSat();

    var osImporteOS  = parseFloat(oscImporte)-parseFloat(osdImporte) + parseFloat(osImporte);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);
    oscImpuesto = osImpuestoOS;
    oscImporte  = osImporteOS;


    actualizarListaOrdenServicio(oscSerie,oscNumero,oscCliente,oscIdCliente,oscEstado,
				 osImpuestoOS,osImporteOS,oscPrioridad);

    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    BuscarDetalleOrdenServicio(cIdOrdenServicio);
}

function quitarProductoOrdenServicioDet(){
    var idx = id("listadoOrdenServicioDetalle").selectedItem;
    if(!idx || cIdOrdenServicioDet == 0) return;

    var osImporteOS  = parseFloat(oscImporte)-parseFloat(ordenserviciodet[ cIdOrdenServicioDet ].importe);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);

    var url = "modulos/ordenservicio/modordenservicio.php?modo=QuitaProductoOrdenServicioDet"+
	      "&xidosd="+cIdOrdenServicioDet+
	      "&xidos="+cIdOrdenServicio+
	      "&ximporte="+osImporteOS+
	      "&ximpuesto="+osImpuestoOS;
    var xrequest  = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;
    }
    var osImporteOS  = parseFloat(oscImporte)-parseFloat(ordenserviciodet[ cIdOrdenServicioDet ].importe);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);
    oscImpuesto = osImpuestoOS;
    oscImporte  = osImporteOS;

    actualizarListaOrdenServicio(oscSerie,oscNumero,oscCliente,oscIdCliente,oscEstado,
				 osImpuestoOS,osImporteOS,oscPrioridad);

    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    BuscarDetalleOrdenServicio(cIdOrdenServicio);
}

function SeleccionarUsuario() {     
    selUsuarioAux();

    if(!UsuarioPost) return;

    id("nombreUsuario").value = UsuarioPost;
    id("idUsuario").value     = IdUsuarioPost;
}

function RegenTipoServicios() {
    VaciarServicios();
    
    var xmenulist = id("FiltroTipoServicio");
    var actual;
    var xvalue,xlabel,xsat;

    for(var t=0;t<servicios.length;t++){
	actual = servicios[t];
	actual = actual.split("~");
	xsat   = (actual[1] == 2)? 1:0;
	xvalue = productos[actual[0]].idproducto+'~'+xsat+':'+actual[0];
	xlabel = productos[actual[0]].nombre+' '+
	         productos[actual[0]].marca+' '+
	         productos[actual[0]].color+' '+
	         productos[actual[0]].talla+' '+
	         productos[actual[0]].laboratorio;
	AddTipoServicio(xlabel,xvalue);
	if(t==0){
	    xmenulist.value=xvalue;
	    mostrarProductoSat();
	}
    }
}

function checkServicios(){
    var cmbServicios = id("FiltroTipoServicio").itemCount;
    if(servicios.length != cmbServicios){
	RegenTipoServicios();
    }
}

function VaciarServicios(){
    var xlistitem = id("elementosServicios");
    var iditem;
    var t = 0;
    
    while( el = id("tiposerv_def_"+ t) ) {
	if (el) xlistitem.removeChild( el ) ;	
	t = t + 1;
    }
    
    osciTipoServicio = 0;
    
    id("FiltroTipoServicio").setAttribute("label","");
}


function AddTipoServicio(nombre, valor) {
	var xlistitem = id("elementosServicios");	
	
	var xtiposerv = document.createElement("menuitem");
	xtiposerv.setAttribute("id","tiposerv_def_" + osciTipoServicio);
			
	xtiposerv.setAttribute("value",valor);
	xtiposerv.setAttribute("label",nombre);
	
	xlistitem.appendChild( xtiposerv);
	osciTipoServicio++;
}

function verificarSerieNumOrdenServicio(xserienum,xvalue){
    if(!xvalue || xvalue == 0){ 
	obtenerSerieNumeroOrdenServicio();
	return;
    }
    
    switch(xserienum){
    case 'Serie':
	var xrequest  = new XMLHttpRequest(); 
	var url       = "modulos/ordenservicio/modordenservicio.php?modo=VerificaSerieNumeroOS"+
	                "&xserie="+xvalue+
	                "&xserienum="+xserienum;
	xrequest.open("GET",url,false);
        xrequest.send(null);
        res = xrequest.responseText;
	
	var xres = res.split("~");

	id("serieOrdenServicio").value = xres[0];
	id("numOrdenServicio").value   = xres[1];
	break;

    case 'Numero':
	var xserie = id("serieOrdenServicio").value;
	var xrequest  = new XMLHttpRequest(); 
	var url       = "modulos/ordenservicio/modordenservicio.php?modo=VerificaSerieNumeroOS"+
	                "&xserie="+xserie+
	                "&xnum="+xvalue+
	                "&xserienum="+xserienum;
	xrequest.open("GET",url,false);
        xrequest.send(null);
        res = xrequest.responseText;

	if(parseInt(res) == xvalue){
	    mostrarFormOrdenServicio('Editar');
	    alert("gPOS: \n\n           el Número de Orden  -"+xvalue+"-  de la Serie  -"+xserie+"-  ya esta registrado");
	    if(!editaNumeroOS) obtenerSerieNumeroOrdenServicio();
	    break;
	}
	var xnum = (res == 0)? xvalue:res;
	id("numOrdenServicio").value   = xnum;

	break;
    }
}

function RegistrarOrdenServicio(idsuscrip){

    var osSerie     = id("serieOrdenServicio").value;
    var osNumOrden  = id("numOrdenServicio").value;
    var osCliente   = id("nombreClienteOrdenServicio").value;
    var osIdCliente = id("idClienteOrdenServicio").value;
    var osPrioridad = id("FiltroPrioridad").value;
    var osTipo      = id("TipoOrdenServicio").value;
    var osIdSuscrip = (idsuscrip)? idsuscrip:0;

    var xrequest  = new XMLHttpRequest();
    var url       = "modulos/ordenservicio/modordenservicio.php?modo=CreaOrdenServicio"+
	            "&xclient=" + osIdCliente +
	            "&xserie=" +osSerie +
	            "&xnumorden=" +osNumOrden+
	            "&xtipo=" +osTipo+
	            "&xprioridad=" +osPrioridad+
	            "&xidsuscrip=" +osIdSuscrip+
	            "&xuser="+Local.IdDependiente;

    if(osIdCliente == 0) 
	return alert("gPOS:  \n\n         Seleccione un Cliente ");

    xrequest.open("GET",url,false);

    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    if(!parseInt(res)) 
	return alert("gPOS: \n\n"+po_servidorocupado+'\n\n -'+res+'-');
    
    var xid = parseInt(res);
    volverOrdenServicio('Editar');
    BuscarOrdenServicio();
    VaciarBuscarDetalleOrdenServicio();
    buscarIdOrdenServicio(xid);
}

function buscarIdOrdenServicio(elemento){

    var busca = parseInt(elemento);
    if(busca.length == 0) return;
    var lista = id("listadoOrdenServicio");
    n = lista.itemCount;
    if(n==0) return; 

    for (var i = 0; i < n; i++) {
	x=i+1;
        var texto2  = lista.getItemAtIndex(i);
        var celdas = texto2.getElementsByTagName('listcell');
        var cadena = celdas[15].getAttribute('value');

	if( busca == cadena )
	{
            lista.selectItem(texto2);
            RevisarOrdenServicioSeleccionada();
	    checkServicios();
	    var xitem = (oscClon)? 'Editar':'Nuevo';
	    if(!oscClon) mostrarFormOrdenServicioDet(xitem,true);
            return;
        }
    }
}

function ModificarOrdenServicio(){
    var datacb      = "";
    var osSerie     = id("serieOrdenServicio").value;
    var osNumOrden  = id("numOrdenServicio").value;
    var osCliente   = id("nombreClienteOrdenServicio").value;
    var osIdCliente = id("idClienteOrdenServicio").value;
    var osEstado    = id("FiltroEstado").value;
    var osPrioridad = id("FiltroPrioridad").value;
    var osTipo      = id("TipoOrdenServicio").value;

    datacb = (osIdCliente != oscIdCliente)? datacb + "&xclient="+osIdCliente:datacb+"";
    datacb = (osSerie != oscSerie )? datacb + "&xserie="+osSerie:datacb+"";
    datacb = (osNumOrden != oscNumero)? datacb + "&xnumorden="+osNumOrden:datacb+"";
    datacb = (osEstado != oscEstado)? datacb + "&xestado="+osEstado:datacb+"";
    datacb = (osPrioridad != oscPrioridad)? datacb + "&xprioridad="+osPrioridad:datacb+"";

    if(!datacb) return;

    var xrequest  = new XMLHttpRequest();
    var url       = "modulos/ordenservicio/modordenservicio.php?modo=ModificaOrdenServicio"+
	            "&xidos="+cIdOrdenServicio+
	            "&xserie="+osSerie+
	            "&xnumorden="+osNumOrden+
	            "&xestado="+osEstado+
	            "&xprioridad="+osPrioridad+
	            "&xtipo=" +osTipo+
	            "&xclient="+osIdCliente;

    xrequest.open("GET",url,false);
    xrequest.send(null);
    res = xrequest.responseText;

    if(parseInt(res) == 0 ){
	mostrarFormOrdenServicio('Editar');
	return alert("gPOS:\n\n        el Número Orden  -"+osNumOrden+
		     "-  de la Serie  -"+osSerie+"-  ya esta registrado");
    }

    if(oscEstado != osEstado)
	crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );

    oscEstado = osEstado;
    actualizarListaOrdenServicio(osSerie,osNumOrden,osCliente,osIdCliente,osEstado,
				 false,false,osPrioridad);

    BuscarDetalleOrdenServicio(cIdOrdenServicio);

    volverOrdenServicio('Editar');
}

function actualizarListaOrdenServicio(osSerie,osNumOrden,osCliente,osIdCliente,osEstado,
				      osImpuesto,osImporte,osPrioridad){
    var idx  = cIdOrdenServicio;
    var lPrioridad;
    if(osPrioridad == 1) lPrioridad = 'Normal';
    if(osPrioridad == 2) lPrioridad = 'Alta';
    if(osPrioridad == 3) lPrioridad = 'Muy Alta';


    id("os_codigo_"+idx).setAttribute("label",osSerie+"-"+osNumOrden);	   
    id("os_serie_"+idx).setAttribute("label",osSerie);	   
    id("os_numeroorden_"+idx).setAttribute("label",osNumOrden);	   
    id("os_cliente_"+idx).setAttribute("label",osCliente);
    id("os_cliente_"+idx).setAttribute("value",osIdCliente);
    id("os_estado_"+idx).setAttribute("label",osEstado); 
    id("os_impuesto_"+idx).setAttribute("value",osImpuesto); 
    id("os_impuesto_"+idx).setAttribute("label",formatDinero(osImpuesto)); 
    id("os_importe_"+idx).setAttribute("value",osImporte); 
    id("os_importe_"+idx).setAttribute("label",formatDinero(osImporte)); 
    id("os_prioridad_"+idx).setAttribute("value",osPrioridad); 
    id("os_prioridad_"+idx).setAttribute("label",lPrioridad); 
}

function ModificarOrdenServicioDet(){
    //Orden de servicio Detalle
    var osFechaInicio    = id("fInicioAtencionServicio").value;
    var osHoraInicio     = id("hInicioAtencionServicio").value;
    var osvFechaInicio   = osFechaInicio+' '+osHoraInicio;
    var osFechaFin       = id("fFinAtencionServicio").value;
    var osHoraFin        = id("hFinAtencionServicio").value;
    var osvFechaFin      = osFechaFin+' '+osHoraFin;
    var osEstado         = id("FiltroEstadoOSDet").value;
    var osIdUsuarioRes   = id("listIdUsuario").value;
    var osConcepto       = id("ConceptoServicio").value;
    var osCantidad       = id("CantidadServicio").value;
    var osPrecio         = id("PrecioServicio").value;
    var osImporte        = id("ImporteServicio").value;
    var osEtdoSolucion   = id("listEstadoSolucion").value;
    var osGtiaCondicion  = id("listGarantiaCondicion").value;
    var osServicio       = id("FiltroTipoServicio").label;
    var osUbicacion      = id("UbicacionServicio").value;
    var osDireccion      = trim(id("DireccionServicio").value);
    var osObservacion    = trim(id("ObservacionServicio").value);

    if(ordenserviciodet[ cIdOrdenServicioDet ]){
	var fechaini = ordenserviciodet[ cIdOrdenServicioDet ].fechainicio.split("~");
	var fechafin = ordenserviciodet[ cIdOrdenServicioDet ].fechafin.split("~");
	//osvFechaInicio = fechaini[1];
	//osvFechaFin    = fechafin[1];
    }


    //Producto Sat
    if(osdServicioSat == 1){
	var osDiagnostico = id("DiagnosticoSat").value;
	var osMotivo      = id("listMotivoSat").value;
	var osResultado   = id("ResultadoSat").value;
	var osUbicacionSat= id("listUbicacionProducto").value;
	var xMarca        = id("listMarca").label;
	var xModelo       = id("listModeloSat").label;
	var xProducto     = id("listProductoSat").label;
	osUbicacionSat       = (osEstado == 'Finalizado')? 'Almacen':osUbicacionSat;
    }

    var xmsj             = "" ;
    var yval             = true;

    if(osEstado == 'Finalizado' && osdServicioSat == 1){
	var diag = trim(id("DiagnosticoSat").value);
	var res  = trim(id("ResultadoSat").value);
	yval = false;
	xmsj = (diag == '')? xmsj+'diagnóstico ':xmsj;
	xmsj = (res == '')? xmsj+'resultado':xmsj;
	xmsj = trim(xmsj);
	xmsj = (xmsj != '')? 'Registre el '+xmsj.replace(/ /g, ' y ')+'.':'';
    }

    if(osEstado == 'Ejecucion' ){
	yval = false;
	xmsj = (osIdUsuarioRes == 0)? xmsj+' Asigne un responsable. ':xmsj;
    }

    if(xmsj != ''){
	id("MostrarInformacionExtraServicio").value = xmsj;
	id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",yval);
	return;
    }
    var data  = "";
    var url   = "modulos/ordenservicio/modordenservicio.php?modo=ModificaOrdenServicioDet";
    data = data + "&xfinit="+osvFechaInicio;
    data = data + "&xffin="+osvFechaFin;
    data = data + "&xestado="+osEstado;
    data = data + "&xidures="+osIdUsuarioRes;
    data = data + "&xcpto="+osConcepto;
    data = data + "&xcant="+osCantidad;
    data = data + "&xprecio="+osPrecio;
    data = data + "&ximpte="+osImporte;
    data = data + "&xstdosol="+osEtdoSolucion;
    data = data + "&xgtiacond="+osGtiaCondicion;
    data = data + "&xtipoprod=Servicio";
    data = data + "&xubi="+osUbicacion;
    data = data + "&xdir="+osDireccion;
    data = data + "&xobs="+osObservacion;
    data = data + "&xidos="+cIdOrdenServicio;
    data = data + "&xidosdet="+cIdOrdenServicioDet;
    data = data + "&xtienesat="+osdServicioSat;

    if(osdServicioSat == 1){
	data = data + "&xdiag="+ osDiagnostico;
	data = data + "&xmotivo="+ osMotivo;
	data = data + "&xresul="+ osResultado;
	data = data + "&xidpsat="+ cIdProductoSat;
	data = data + "&xubisat="+ osUbicacionSat;
    }

    var xrequest  = new XMLHttpRequest();

    ocultarpanelOrdenServicioDet();

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    var xosdet = res.split('~');
    
    if(xosdet[0] != '')
	return alert('gPOS: \n\n'+po_servidorocupado+'\n\n -'+xosdet[0]+'-');

    osEstado = xosdet[2];
    oscEstado = osEstado;

    var osImporteOS  = parseFloat(oscImporte)-parseFloat(osdImporte) + parseFloat(osImporte);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);
    oscImpuesto = osImpuestoOS;
    oscImporte  = osImporteOS;

    actualizarListaOrdenServicio(oscSerie,oscNumero,oscCliente,oscIdCliente,osEstado,
				 osImpuestoOS,osImporteOS,oscPrioridad);

    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    resetFormOrdenServicioDet();
    BuscarDetalleOrdenServicio(cIdOrdenServicio);
    ItemSeleccionada = 0;
}

function ModificarProductoSat(){
    var url       = "modulos/ordenservicio/modordenservicio.php?modo=ModificaProductoSat"
    var xrequest  = new XMLHttpRequest();
    var data      = "";

    var osEstado         = id("FiltroEstadoOSDet").value;
    
    var osMarca       = id("listMarca").value;
    var osModelo      = id("listModeloSat").value;
    var osProducto    = id("listProductoSat").value;
    var osDescripcion = id("DescripcionSat").value;
    var osNS          = id("NumeroSerieSat").value;
    var osDiagnostico = id("DiagnosticoSat").value;
    var osMotivo      = id("listMotivoSat").value;
    var osResultado   = id("ResultadoSat").value;
    var osDetalleSat  = id("idDetalleSat").checked;
    var osUbicacion   = id("listUbicacionProducto").value;
    var xMarca        = id("listMarca").label;
    var xModelo       = id("listModeloSat").label;
    var xProducto     = id("listProductoSat").label;
    osUbicacion       = (osEstado == 'Finalizado')? 'Almacen':osUbicacion;

    data = data + "&xmarca="+ osMarca;
    data = data + "&xmodelo="+ osModelo;
    data = data + "&xprod="+ osProducto;
    data = data + "&xdesc="+ osDescripcion;
    data = data + "&xns="+ osNS;
    data = data + "&xdiag="+ osDiagnostico;
    data = data + "&xmotivo="+ osMotivo;
    data = data + "&xresul="+ osResultado;
    data = data + "&xidpsat="+ cIdProductoSat;
    data = data + "&xubi="+ osUbicacion;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    if(!parseInt(res)) 
	return alert("gPOS: \n\n"+po_servidorocupado+'\n\n -'+res+'-');

    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    
}

function mostrarFormProductoSatDetalle(xvalue){
    id("formProductoSatDetalle").setAttribute('collapsed',!xvalue);
    id("listProductoSatDetalle").setAttribute('collapsed',xvalue);
}


function RegistrarOrdenServicioDet(){
    var xprod            = id("FiltroTipoServicio").value.split(":");
    var ostiposerv       = xprod[0].split("~");
    var osIdProducto     = ostiposerv[0];
    var osFechaInicio    = id("fInicioAtencionServicio").value;
    var osHoraInicio     = id("hInicioAtencionServicio").value;
    var osvFechaInicio   = osFechaInicio+' '+osHoraInicio;//'0000-00-00 00:00:00';//
    var osFechaFin       = id("fFinAtencionServicio").value;
    var osHoraFin        = id("hFinAtencionServicio").value;
    var osvFechaFin      = osFechaFin+' '+osHoraFin;//'0000-00-00 00:00:00';//
    var osEstado         = id("FiltroEstadoOSDet").value;
    var osIdUsuarioRes   = id("listIdUsuario").value;
    var osConcepto       = trim(id("ConceptoServicio").value);
    var osCantidad       = id("CantidadServicio").value;
    var osPrecio         = id("PrecioServicio").value;
    var osImporte        = id("ImporteServicio").value;
    var osServicio       = id("FiltroTipoServicio").label;
    var CodigoBarras     = xprod[1];
    var Referencia       = productos[xprod[1]].referencia;
    var osUbicacion      = id("UbicacionServicio").value;
    var osDireccion      = trim(id("DireccionServicio").value);
    var osObservacion    = trim(id("ObservacionServicio").value);
    osCantidad           = (ostiposerv[1] == 1)? 1:osCantidad;
    var CodigoAnterior   = (oscClon)? oscCodigo:"";

    

    osdServicioSat     = ostiposerv[1];

    if(osdServicioSat == 1){
	var xMarca    = trim(id("listMarca").label);
	var xModelo   = trim(id("listModeloSat").label);
	var xProducto = trim(id("listProductoSat").label);

	osConcepto    = osServicio+'- '+xProducto+' '+xMarca+' '+xModelo;
    }

    if(trim(ostiposerv) == ""){
	id("MostrarInformacionExtraServicio").value = 'Seleccione un Servicio';
	id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",false);
	return;
    }

    if(osEstado == 'Ejecucion'){
	if(osIdUsuarioRes == 0){
	    id("MostrarInformacionExtraServicio").value = 'Asigne un responsable';
	    id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",false);
	    return;
	}
    }

    if(ostiposerv[1] == 1){
	var osMarca       = trim(id("listMarca").value);
	var osModelo      = trim(id("listModeloSat").value);
	var osProducto    = trim(id("listProductoSat").value);
	var osMotivo      = trim(id("listMotivoSat").value);
	var msj = "";

	msj = (osProducto == 0)? msj+'producto ':msj;
	msj = (osMarca == 0)? msj+'marca ':msj;
	msj = (osModelo == 0 || osModelo == 'undefined')? msj+'modelo ':msj;
	msj = (osMotivo == 0)? msj+'motivo':msj;	
	msj = trim(msj);
	msj = msj.replace(/ /g, ', ');

	if(msj != ''){
	    id("MostrarInformacionExtraServicio").value = 'Seleccione '+msj;
	    id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",false);
	    return;
	}

    }

    if(cIdOrdenServicio == 0) return;

    var url       = "modulos/ordenservicio/modordenservicio.php?modo=CreaOrdenServicioDet"+
	            "&xidps="+osIdProducto+
                    "&xfinit="+osvFechaInicio+
                    "&xffin="+osvFechaFin+
                    "&xestado="+osEstado+
	            "&xidures="+osIdUsuarioRes+
	            "&xcpto="+osConcepto+
	            "&xcant="+osCantidad+
	            "&xprecio="+osPrecio+
 	            "&ximpte="+osImporte+
	            "&xtipoprod=Servicio"+
	            "&xidos="+cIdOrdenServicio+
	            "&xcb="+CodigoBarras+
	            "&xref="+Referencia+
	            "&xubi="+osUbicacion+
	            "&xdir="+osDireccion+
	            "&xobs="+osObservacion+
	            "&xcod="+CodigoAnterior;

    var xrequest  = new XMLHttpRequest();

    ocultarpanelOrdenServicioDet();

    xrequest.open("GET",url,false);

    try {
        xrequest.send(null);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    if(!(res))
	return alert(po_servidorocupado);

    var xosdet = res.split('~');
    osEstado = xosdet[1];
    oscEstado = (oscEstado == 'Ejecucion')? oscEstado:osEstado;

    cIdOrdenServicioDet = parseInt(xosdet[0]);

    if(ostiposerv[1] == 1) RegistrarProductoSat();

    var osImporteOS  = parseFloat(oscImporte) + parseFloat(osImporte);
    var osImpuestoOS = (parseFloat(osImporteOS)*parseFloat(Local.Impuesto)/100);
    oscImpuesto = osImpuestoOS;
    oscImporte  = osImporteOS;

    actualizarListaOrdenServicio(oscSerie,oscNumero,oscCliente,oscIdCliente,oscEstado,
				 osImpuestoOS,osImporteOS,oscPrioridad);

    //ordenserviciodetserv[ cIdOrdenServicio ].length = 0;
    crearOrdenServicioDetalle2XML( obtenerOrdenServicioDetalle() );
    resetFormOrdenServicioDet();
    BuscarDetalleOrdenServicio(cIdOrdenServicio);
}

function calcularImporteOSDet(){
    var xprod            = id("FiltroTipoServicio").value.split(":");
    var ostiposerv       = xprod[0].split("~");
    var osIdProducto     = ostiposerv[0];

    var osCantidad = trim(id("CantidadServicio").value);
    var osPrecio   = id("PrecioServicio").value;

    if(osCantidad == '' || osCantidad == 0 )
	osCantidad = 1;
    
    if(ostiposerv[1] == 1 && osCantidad > 1)
	osCantidad = 1;
    
    if(trim(osPrecio) == '')
	osPrecio = 0;
    
    var osImporte  = parseFloat(osCantidad)*parseFloat(osPrecio);
    id("PrecioServicio").value = formatDineroTotal(osPrecio);
    id("CantidadServicio").value = osCantidad;
    id("ImporteServicio").value = formatDineroTotal(osImporte);
}

function CancelarOrdenServicioDet(){
    ocultarpanelOrdenServicioDet();
    resetFormOrdenServicioDet();
}

function resetFormOrdenServicioDet(){
    cleanFormOrdenServicioDet();
    cleanFormProductoSat();
    id("btnCancelServicioDet").setAttribute('label',"Cancelar");
    id("btnCancelServicioDet").setAttribute('image',"img/gpos_cancelar.png");

    id("btnAceptarServicioDet").setAttribute('collapsed',false);
    id("btnAceptarServicioDet").setAttribute('label','Aceptar');
    id("btnAceptarServicioDet").setAttribute('oncommand','RegistrarOrdenServicioDet()');

    id("rowTipoServicio").setAttribute('collapsed',false);
    id("rowDescTipoServicio").setAttribute('collapsed',true);

    id("MostrarInformacionExtraServicio").value = '';
    id("rowMostrarInformacionExtraServicio").setAttribute("collapsed",true);

    id("rowDetalleProductoSat").setAttribute("collapsed",false);

    id("itemAgregarProductoSatDet").setAttribute('disabled',false);
    id("itemModificarProductoSatDet").setAttribute('disabled',false);
    
    cleanListaOrdenServicioSatDet();
    verOrdenServicioDetalle(true,'Nuevo');
    verProductoSat(true);
    vertabProductoDetalleSat(false);
    mostrarProductoSat();

    verificarEstadoOrdenServicioDet('Pendiente');
    osdEstado  = "Pendiente";

}

function ocultarpanelOrdenServicioDet(){
    id("boxFormOrdenServicioDet").hidePopup();
    vboxOrdenServicioDisplay('unblock');
}

//////////////////// Productos SAT 

function changeMarca(xdet){
    setTimeout("RegenModeloSat("+xdet+")",50);
}

function AddMarcaLine(nombre, valor, xdet) {
    
    var xlistitem = (xdet == 0)? id("elementosMarca") : id("elementosMarcaDetSat");

    var xmarca = document.createElement("menuitem");
    var idxmarca = (xdet == 0)? "marca_def_":"marcadet_def_";
    xmarca.setAttribute("id",idxmarca + osciMarcas);	
    xmarca.setAttribute("value",valor);
    xmarca.setAttribute("label",nombre);
    xlistitem.appendChild( xmarca );

    osciMarcas++;
    if(xdet == 0) AddMarcaLine(nombre, valor, 1);
}

function RegenModeloSat(xdet) {
    VaciarModeloSat(xdet);

    var osidmarca = (xdet == 0)? id("listMarca").value : id("listMarcaDetSat").value;
    var xmenulist = (xdet == 0)? id("listModeloSat") : id("listModeloDetSat");

    var xrequest = new XMLHttpRequest();
    var url = "modulos/ordenservicio/modordenservicio.php?modo=ObtnerModeloSat&xidmarca="+osidmarca;

    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res = xrequest.responseText;

    var lines = res.split("\n");
    var actual;
    var ln = lines.length-1;
    ln = (ln == 0)? 1:ln;
    for(var t=0;t<ln;t++){
	actual = lines[t];
	actual = actual.split("=");
	AddModeloLine(actual[0],actual[1],xdet);
	if(t==0){
	    xmenulist.value=actual[1];
	    xmenulist.label=actual[0];
	}		
    }				
}

function AddModeloLine(nombre, valor, xdet) {
    var xlistitem = (xdet == 0)? id("elementosModeloSat") : id("elementosModeloDetSat");
    
    if(osciModelos == 0){
	var btnmodelo = (xdet==0)? "mostrarNuevoModelo('true',0)":"mostrarNuevoModelo('true',1)";
	var xmodelo   = document.createElement("menuitem");
	var idxmodelo = (xdet == 0)? "modelo_def_":"modelodet_def_";
	xmodelo.setAttribute("id",idxmodelo + osciModelos);	
	xmodelo.setAttribute("value",0);
	xmodelo.setAttribute("label",'Nuevo Modelo');
	xmodelo.setAttribute("oncommand",btnmodelo);
	xmodelo.setAttribute("style","font-weight: bold;");
	xlistitem.appendChild( xmodelo );
	osciModelos++;

    }
 
    var xmodelo   = document.createElement("menuitem");
    var idxmodelo = (xdet == 0)? "modelo_def_":"modelodet_def_";
    xmodelo.setAttribute("id",idxmodelo + osciModelos);
    xmodelo.setAttribute("value",valor);
    xmodelo.setAttribute("label",nombre);
    xlistitem.appendChild( xmodelo);

    osciModelos++;
}

function VaciarModeloSat(xdet){
    var xlistitem = (xdet == 0)? id("elementosModeloSat") : id("elementosModeloDetSat");
    var xmenulist = (xdet == 0)? id("listModeloSat") : id("listModeloDetSat");
    var idxmodelo = (xdet == 0)? "modelo_def_":"modelodet_def_";

    var t = 0;
    while( el = id(idxmodelo + t ) ) {
	if (el) xlistitem.removeChild( el ) ;	
	t = t + 1;
    }

    osciModelos = 0;
    xmenulist.setAttribute("value",0 );
}

function AddProductoLine(nombre, valor, xdet) {
    var xlistitem   = (xdet == 0)? id("elementosProductoSat"):id("elementosProductoDetSat");
    var idxproducto = (xdet == 0)? "producto_def_":"productodet_def_";
    var xproducto = document.createElement("menuitem");
    xproducto.setAttribute("value",valor);
    xproducto.setAttribute("label",nombre);
    xlistitem.appendChild( xproducto );
    osciProductos++;
    if(xdet == 0) AddProductoLine(nombre, valor, 1);
}

function AddMotivoLine(nombre, valor) {
    var xlistitem = id("elementosMotivoSat");

    var xmotivo = document.createElement("menuitem");
    xmotivo.setAttribute("id","motivo_def_" + osciMotivos);
    xmotivo.setAttribute("value",valor);
    xmotivo.setAttribute("label",nombre);
    xlistitem.appendChild( xmotivo);
    osciMotivos++;
}

function SeleccionarMarcaRegistrada(elemento,xdet){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = (xdet == 0)? id("listMarca"):id("listMarcaDetSat");
    var xvalue = 0;
    var xlabel = "";
    n = lista.itemCount;

    if(n==0) return; 
    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var cadena  = texto2.getAttribute('label').toUpperCase();

	if(!elemento && i == 1){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
	}

	if ( busca == cadena)
	{
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
        }
    }
    
    lista.value = xvalue;
    lista.label = xlabel;

    changeMarca(xdet);
    return lista.label;
}

function SeleccionarModeloSatDeLista(elemento,xdet){

    var busca = trim(elemento);

    if(busca.length == 0) return;
    var lista = (xdet == 0)? id("listModeloSat"):id("listModeloDetSat");
    var xvalue = 0;
    var xlabel = "";
    n = lista.itemCount;

    if(n==0) return; 
    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var cadena  = texto2.getAttribute('label').toUpperCase();

	if(!elemento && i == 1){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
	}

	if ( busca == cadena){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
        }
    }
    
    lista.value = xvalue;
    lista.label = xlabel;

    return lista.label;
}

function SeleccionarProductoSatDeLista(elemento,xdet){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = (xdet == 0)? id("listProductoSat"):id("listProductoDetSat");
    var xvalue = 0;
    var xlabel = "";
    n = lista.itemCount;

    if(n==0) return; 
    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var cadena = texto2.getAttribute('label').toUpperCase();

	if(!elemento && i == 1){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
	}

	if ( busca == cadena){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    break;
        }
    }
    
    lista.value = xvalue;
    lista.label = xlabel;

    return lista.label;
}

function SeleccionarMotivoSatDeLista(elemento){

    var busca = trim(elemento);
    if(busca.length == 0) return;
    var lista = id("listMotivoSat");
    var xvalue = 0;
    var xlabel = "";
    n = lista.itemCount;

    if(n==0) return; 
    busca = busca.toUpperCase();

    for (var i = 0; i < n; i++) {
        var texto2  = lista.getItemAtIndex(i);
        var cadena = texto2.getAttribute('label').toUpperCase();

	if(!elemento && i == 1){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    continue;
	}

	if ( busca == cadena){
	    xvalue = texto2.getAttribute("value");
	    xlabel = texto2.getAttribute("label");
	    continue;
        }
    }
    
    lista.value = xvalue;
    lista.label = xlabel;

    return lista.label;
}


function RegistrarMarcaSat(marca,xdet){
    ospMarca = "";
    if(trim(marca) == 'Nueva Marca' || trim(marca) == '')
	return mostrarNuevoMarca(false,xdet,false);

    var part      = trim(marca).slice(0,5);
    part          = (part == "Nuevo" || trim(marca) == "")? true:false;
    if(part) return mostrarNuevoMarca(false,xdet,false);

    var existe = SeleccionarMarcaRegistrada(marca,xdet);
    existe     = existe.toUpperCase();
    marca      = marca.toUpperCase();

    if(existe == marca){
	oscNewRegMarca = true;
	ospNewMarca    = marca;
	return mostrarNuevoMarca(false,xdet);
    }

    var url      = "modulos/productos/selmarca.php?modo=salvamarcasat&marca="+marca.toUpperCase();
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var text   = (xdet == 1)? id("txtMarcaDetSat"):id("txtMarca");
    var xres = xrequest.responseText.split('~');

    if(xres[0] != '') 
	return alert('gPOS: '+po_servidorocupado+'\n\n'+xres[0]);

    AddMarcaLine(marca,parseInt(xres[1]),0);
    ospNewMarca = marca;
    oscNewRegMarca = true;
    mostrarNuevoMarca(false,xdet);
    text.value = "";
}

function RegistrarModeloSat(modelo,xdet){
    if(trim(modelo) == 'Nuevo Modelo') return mostrarNuevoModelo(false,xdet);

    var part     = trim(modelo).slice(0,5);
    part         = (part == "Nuevo" || trim(modelo) == "")? true:false;
    if(part) return mostrarNuevoModelo(false,xdet);

    var existe = SeleccionarModeloSatDeLista(modelo,xdet);
    existe     = existe.toUpperCase();
    modelo     = modelo.toUpperCase();

    if(existe == modelo){
	oscNewRegModelo = true;
	ospNewModelo    = modelo;
	return mostrarNuevoModelo(false,xdet);
    }

    var idmarca  = (xdet == 1)? id("listMarcaDetSat").value: id("listMarca").value;
    idmarca      = (idmarca == 0)? 1:idmarca;

    var url      = "modulos/ordenservicio/modordenservicio.php?modo=CreaModeloSat&modelo="+modelo+
	           "&xmarca="+idmarca;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res    = xrequest.responseText;
    var list   = (xdet == 1)? id("listModeloDetSat"):id("listModeloSat");
    var text   = (xdet == 1)? id("txtModeloDetSat"):id("txtModeloSat");

    AddModeloLine(modelo,parseInt(res),xdet);
    ospNewModelo    = modelo;
    oscNewRegModelo = true;
    mostrarNuevoModelo(false,xdet);
    text.value = "";
    list.value = parseInt(res);
}

function RegistrarProductoIdiomaSat(producto,xdet){
    if(trim(producto) == 'Nuevo Producto') return mostrarNuevoProductoSat(false,xdet);

    var part  = trim(producto).slice(0,5);
    part      = (part == "Nuevo" || trim(producto) == "")? true:false;
    if(part) return mostrarNuevoProductoSat(false,xdet);

    var list   = (xdet == 1)? id("listProductoDetSat"):id("listProductoSat");
    var text   = (xdet == 1)? id("txtProductoDetSat"):id("txtProductoSat");

    var existe = SeleccionarProductoSatDeLista(producto,xdet);
    existe     = existe.toUpperCase();
    producto   = producto.toUpperCase();

    if(existe == producto){
	oscNewRegProdSat = true;
	ospNewProdSat    = producto;
	text.value       = "";
	return mostrarNuevoProductoSat(false,xdet);
    }

    var url      = "modulos/ordenservicio/modordenservicio.php?modo=CreaProductoIdiomaSat"+
	           "&xprod="+producto;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var res  = xrequest.responseText;
    var xres = res.split('~');
    var xid  = (xres[0] == '')? xres[1]:xres[0];

    AddProductoLine(producto,parseInt(xid),0);
    list.value = parseInt(xid);
    ospNewProdSat    = producto;
    oscNewRegProdSat = true;
    mostrarNuevoProductoSat(false,xdet);
    text.value = "";
}

function RegistrarMotivoSat(motivo){
    if(trim(motivo) == 'Nuevo Motivo') return mostrarNuevoMotivoSat(false);

    var part     = trim(motivo).slice(0,5);
    part         = (part == "Nuevo" || trim(motivo) == "")? true:false;
    if(part) return mostrarNuevoMotivoSat(false);

    var existe = SeleccionarMotivoSatDeLista(motivo);
    existe     = existe.toUpperCase();
    motivo     = motivo.toUpperCase();

    if(existe == motivo){
	oscNewRegMotivo = true;
	ospNewMotivo    = motivo;
	return mostrarNuevoMotivoSat(false);
    }

    var url      = "modulos/ordenservicio/modordenservicio.php?modo=CreaMotivoSat&motivo="+motivo;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);
    var res    = xrequest.responseText;

    AddMotivoLine(motivo,parseInt(res));
    ospNewMotivo    = motivo;
    oscNewRegMotivo = true;
    mostrarNuevoMotivoSat(false);
    id("txtMotivoSat").value  = "";

}

function RegistrarProductoSat(){
    var url       = "modulos/ordenservicio/modordenservicio.php?modo=CreaProductoSat"
    var xrequest  = new XMLHttpRequest();
    var data      = "";
    
    var osMarca       = id("listMarca").value;
    var osModelo      = id("listModeloSat").value;
    var osProducto    = id("listProductoSat").value;
    var osDescripcion = id("DescripcionSat").value;
    var osNS          = id("NumeroSerieSat").value;
    var osDiagnostico = id("DiagnosticoSat").value;
    var osMotivo      = id("listMotivoSat").value;
    var osResultado   = id("ResultadoSat").value;
    var osUbicacion   = id("listUbicacionProducto").value;

    var xMarca        = id("listMarca").label;
    var xModelo       = id("listModeloSat").label;
    var xProducto     = id("listProductoSat").label;

    oscDetalleSat     = id("idDetalleSat").checked;
    var esDetalle     = (oscDetalleSat)? 1:0;
    esDetalle         = (oscContador > 0)? esDetalle:0;

    data = data + "&xmarca="+ osMarca;
    data = data + "&xmodelo="+ osModelo;
    data = data + "&xprod="+ osProducto;
    data = data + "&xdesc="+ osDescripcion;
    data = data + "&xns="+ osNS;
    data = data + "&xdiag="+ osDiagnostico;
    data = data + "&xmotivo="+ osMotivo;
    data = data + "&xresul="+ osResultado;
    data = data + "&xidosd="+ cIdOrdenServicioDet;
    data = data + "&xesdet="+ esDetalle;
    data = data + "&xubi="+ osUbicacion;

    xrequest.open("POST",url,false);
    xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    try {
        xrequest.send(data);
        res = xrequest.responseText;
    } catch(e) {
        res = false;	
    }

    if(!parseInt(res)) 
	return alert("gPOS: \n\n"+po_servidorocupado+'\n\n -'+res+'-');
    
    var IdProductoSat = parseInt(res);

    if(oscDetalleSat) RegistrarProductoDetSat(IdProductoSat);
}

function RegistrarProductoDetSat(IdProductoSat){
    var t = 0;
    firma = "os_lista_";
    
    while( t < oscContador ) { 

	var xrequest  = new XMLHttpRequest();
	
	var osMarcaSat       = id(firma+t+"_marcasat").getAttribute("value");
	var osModeloSat      = id(firma+t+"_modelosat").getAttribute("value");
	var osProductoSat    = id(firma+t+"_producto").getAttribute("value");
	var osNumeroSerieSat = id(firma+t+"_nssat").getAttribute("label");

	var url = "modulos/ordenservicio/modordenservicio.php?modo=CreaProductoDetSat"+
	          "&xidps="+IdProductoSat+
	          "&xmarca="+osMarcaSat+
	          "&xmodelo="+osModeloSat+
	          "&xprod="+osProductoSat+
 	          "&xnssat="+osNumeroSerieSat;

	xrequest.open("GET",url,false);

	try {
            xrequest.send(null);
            //res = xrequest.responseText;
	} catch(e) {
            //res = false;
	}
	
	t++;
    }

    VaciarDeHijosTag("listadoProductoDetalleSat","esMov");
}

function VaciarDeHijosTag(padreNombre,Tag){	
    var padre = id(padreNombre);
    while( padre.childNodes.length && padre.lastChild && padre.lastChild.getAttribute(Tag) ){
	padre.removeChild( padre.lastChild );
    }		
}


function AgregarProductoDetSat(){
    var psdIdMarcaSat    = id("listMarcaDetSat").value;
    var psdMarcaSat      = id("listMarcaDetSat").label;
    var psdIdModeloSat   = id("listModeloDetSat").value;
    var psdModeloSat     = id("listModeloDetSat").label;
    var psdIdProductoSat = id("listProductoDetSat").value;
    var psdProductoSat   = id("listProductoDetSat").label;
    var psdNSSat         = id("NumeroSerieDetSat").value;

    GenerarListaProductoDetSat(psdIdMarcaSat,psdMarcaSat,psdIdModeloSat,psdModeloSat,
			       psdIdProductoSat,psdProductoSat,psdNSSat,false);
    id("NumeroSerieDetSat").value = "";
    vertabProductoDetalleSat(true);
}

function GenerarListaProductoDetSat(psdIdMarcaSat,psdMarcaSat,psdIdModeloSat,psdModeloSat,
				    psdIdProductoSat,psdProductoSat,psdNSSat,
				    IdProductoSatDet){
    
    var listaProd = id("listadoProductoDetalleSat");

    var xrow = document.createElement("listitem");
    oscLista = "os_lista_"+oscContador;
    xrow.setAttribute("esMov",true);
    xrow.setAttribute("value",IdProductoSatDet);
    xrow.setAttribute("id",oscLista);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("label",oscContador+1 );
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("value",psdIdProductoSat);
    xcell.setAttribute("label", psdProductoSat+" "+psdMarcaSat+" "+psdModeloSat);
    xcell.setAttribute("id",oscLista+"_producto");
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("label",psdNSSat);
    xcell.setAttribute("id",oscLista+"_nssat");
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("value",psdIdMarcaSat);
    xcell.setAttribute("label",psdMarcaSat);
    xcell.setAttribute("id",oscLista+"_marcasat");
    xcell.setAttribute("collapsed",true);
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("value",psdIdModeloSat);
    xcell.setAttribute("label",psdModeloSat);
    xcell.setAttribute("id",oscLista+"_modelosat");
    xcell.setAttribute("collapsed",true);
    xrow.appendChild(xcell);

    xcell = document.createElement("listcell"); 
    xcell.setAttribute("esMov",true);
    xcell.setAttribute("value",IdProductoSatDet);
    xcell.setAttribute("id",oscLista+"_prodsatdet");
    xcell.setAttribute("collapsed",true);
    xrow.appendChild(xcell);

    listaProd.appendChild(xrow);			
    
    oscContador++;
}

function mostrarNuevoMarca(xvalue,xdet){
    var marca    = (xdet == 0)? id("rowMarca")    : id("rowMarcaDetSat");
    var newmarca = (xdet == 0)? id("rowNewMarca") : id("rowNewMarcaDetSat");
    var texmarca = (xdet == 0)? id("txtMarca")    : id("txtMarcaDetSat");
    marca.setAttribute("collapsed",xvalue);
    newmarca.setAttribute("collapsed",!xvalue);
    if(xvalue) texmarca.focus();
    if(!xvalue) SeleccionarMarcaRegistrada(false,xdet);
    if(oscNewRegMarca) 	SeleccionarMarcaRegistrada(ospNewMarca,xdet);
}

function mostrarNuevoModelo(xvalue,xdet){
    var modelo    = (xdet == 0)? id("rowModeloSat")    : id("rowModeloDetSat");
    var newmodelo = (xdet == 0)? id("rowNewModeloSat") : id("rowNewModeloDetSat");
    var texmodelo = (xdet == 0)? id("txtModeloSat")    : id("txtModeloDetSat");
    modelo.setAttribute("collapsed",xvalue);
    newmodelo.setAttribute("collapsed",!xvalue);
    if(xvalue) texmodelo.focus();
    if(!xvalue) SeleccionarModeloSatDeLista(false,xdet);
    if(oscNewRegModelo) SeleccionarModeloSatDeLista(ospNewModelo,xdet);
}

function mostrarNuevoProductoSat(xvalue,xdet){
    var prod        = (xdet == 0)? id("rowProducto")       : id("rowProductoDetSat");
    var newprod     = (xdet == 0)? id("rowNewProductoSat") : id("rowNewProductoDetSat");
    var texproducto = (xdet == 0)? id("txtProductoSat")    : id("txtProductoDetSat");
    prod.setAttribute("collapsed",xvalue);
    newprod.setAttribute("collapsed",!xvalue);
    if(xvalue) texproducto.focus();
    if(!xvalue) SeleccionarProductoSatDeLista(false,xdet);
    if(oscNewRegProdSat) SeleccionarProductoSatDeLista(ospNewProdSat,xdet);
}

function mostrarNuevoMotivoSat(xvalue){
    id("rowMotivoSat").setAttribute("collapsed",xvalue);
    id("rowNewMotivoSat").setAttribute("collapsed",!xvalue);
    if(xvalue) id("txtMotivoSat").focus();
    if(!xvalue) SeleccionarProductoSatDeLista(false);
    if(oscNewRegMotivo) SeleccionarMotivoSatDeLista(ospNewMotivo);
}

function verificarEstadoOrdenServicioDet(xvalue){
    var xpendiente  = true;
    var xejecucion  = true;
    var xfinalizado = true;
    var xcancelado  = true;

    var	xrowprecio = true;
    var	xrowcant   = true;
    var	xrowimport = true;
    var	xrowdiag   = true;
    var xrowsol    = true;

    var xgtiacond  = true;
    var xstdosol   = true;
    var xuserresp  = true;
    var xfechaservi= true;
    var xfechaservf= true;
    
    var xestado    = osdEstado;

    switch(xvalue){
    case 'Pendiente':
	xejecucion = false;
	xcancelado = (xestado == 'Pendiente')? false:true;
	xrowcant   = false;
	xuserresp  = false;
	xrowprecio = false;
	xrowimport = false;
	break;
    case 'Ejecucion':
	xpendiente = ((trim(xestado) == "") || (xestado == 'Pendiente'))? false:true;
	xfinalizado = ((xestado == 'Pendiente') || (trim(xestado) == ""))? true:false;
	xrowprecio = false;
	xrowcant   = false;
	xrowimport = false;
	xrowdiag   = (osdServicioSat == 0)? true:false;
	xgtiacond  = false;
	xuserresp  = false;
	xfechaservi= false;
	xcancelado = (xestado == 'Ejecucion')? false:true;
	break;
    case 'Cancelado':
	xpendiente = (xestado == 'Pendiente' || xestado == 'Cancelado')? false:true;
	xejecucion = (xestado == 'Ejecucion')? false:true;
	break;
    case 'Finalizado':
	xejecucion = (xestado == 'Ejecucion')? false:true;
	xrowprecio = false;
	xrowcant   = false;
	xrowimport = false;
	xrowdiag   = (osdServicioSat == 0)? true:false;
	xrowsol    = (osdServicioSat == 0)? true:false;
	xgtiacond  = false;
	xstdosol   = false;
	xuserresp  = false;
	xfechaservi= false;
	xfechaservf= false;
	id("fFinAtencionServicio").value = calcularFechaActual('fecha');
	id("listUbicacionProducto").value = 'Almacen';
	break;
    }
    var xorden = (cIdOrdenServicioDet != 0)? ordenserviciodet[cIdOrdenServicioDet].ordenanterior:false;
    xgtiacond = (trim(xorden))? xgtiacond:true;

    id("itmEstadoEjecucionDet").setAttribute('collapsed',xejecucion);
    id("itmEstadoPendienteDet").setAttribute('collapsed',xpendiente);
    id("itmEstadoCanceladoDet").setAttribute('collapsed',xcancelado);
    id("itmEstadoFinalizadoDet").setAttribute('collapsed',xfinalizado);

    id("rowCantidadServivio").setAttribute("collapsed",xrowcant);
    id("rowPrecioServicio").setAttribute("collapsed",xrowprecio);
    id("rowImporteServicio").setAttribute("collapsed",xrowimport);
    id("rowDiagnostico").setAttribute("collapsed",xrowdiag);
    id("rowResultado").setAttribute("collapsed",xrowsol);

    id("rowEstadoSolucion").setAttribute("collapsed",xstdosol);
    id("rowGarantiaCondicion").setAttribute("collapsed",xgtiacond);
    id("rowListaUsuario").setAttribute("collapsed",xuserresp);
    //id("rowFechaInicioServicio").setAttribute("collapsed",xfechaservi);
    //id("rowFechaFinServicio").setAttribute("collapsed",xfechaservf);
}

function mostrarBusquedaAvanzadaOrdenServicioDet(xthis){

    var xchecked = (xthis.getAttribute('checked'))? false:true;
    var xlabel   = xthis.label.replace(" ","_");
    var xdet     = true;

    switch(xlabel){
    case "Fecha_Fin": 
	vosFechaFin       = xchecked;
	break;
    case "Estado_Solucion":
	vosEstadoSolucion = xchecked;
	break;
    case "Numero_Serie" : 
	vosNumeroSerie    = xchecked;
	break;
    case "Estado_Garantia" :
	vosEstadoGarantia = xchecked;
	break;
    case "Fecha_Entrega":
	vosFechaEntrega   = xchecked;
	xdet              = false;
	break;
    case "Registrado_por":
	vosUsuarioRegistro= xchecked;
	xdet              = false;
	break;
    case "Entregado_por":
	vosUsuarioEntrega = xchecked;
	xdet              = false;
	break;
    case "Asignado_a":
	xdet              = false;
	break;
    case "Tipo_Servicio":
	xdet              = false;
	break;
    case "Facturacion":
	xdet              = false;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    
    (xdet)? BuscarDetalleOrdenServicio(cIdOrdenServicio):BuscarOrdenServicio();
}

function xmenuOrdenServicioDetalle(){
    var editar  = true;
    var agregar = true;
    var quitar  = true;
    var ver     = false;
    var hidx    = id("listadoOrdenServicio").selectedItem;
    var idx     = id("listadoOrdenServicioDetalle").selectedItem;
    var esselect = (!idx || id("listadoOrdenServicioDetalle").itemCount == 0)? true:false;

    var agregarserv = (oscEstado == 'Finalizado')? true:false;
    agregarserv     = (oscEstado == 'Cancelado')? true:agregarserv;
    agregarserv     = (oscTipo   == 'Garantia')? true:agregarserv;
    var agregarprod = (oscEstado == 'Cancelado' )? true:false;
    agregarprod = (osdTipoProducto == 'Producto')? true:agregarprod;
    agregarprod = (oscFacturacion != 0)? true:agregarprod;

    switch(osdEstado){
    case 'Pendiente':
	agregar   = (oscEstado != 'Cancelado')? false:true;
	editar    = false;
	quitar    = false;
	break
    case 'Ejecucion':
	agregar   = (oscEstado != 'Cancelado')? false:true;
	editar    = false;
	break;
    case 'Finalizado':
	editar    = false;
	break;
    case 'Cancelado':
	editar    = false;
	quitar    = false;
	agregarprod = true;
	break;
    }

    //editar = (osdTipoProducto == 'Producto')? false:editar;
    editar = (oscEstado == 'Cancelado')? true:editar;
    editar = (oscFacturacion != 0 )? true:editar;

    //var quitar = (osdTipoProducto != 'Producto')? true:false;
    quitar     = (!idx)? true:quitar;
    quitar     = (oscFacturacion != 0 || oscEstado == "Cancelado")? true:quitar;
    var clonar = (oscEstado == 'Finalizado')? false:true;
    clonar     = (osdTipoProducto == 'Servicio')? clonar:true;

    if(esselect){
	editar = true;
	ver    = true;
	clonar = true;
	agregar= true;
	agregarprod = true;
    }

    if(Local.esSAT == 1) id("itemEditarOrdenServicioDet").setAttribute('disabled',editar);
    id("itemVerOrdenServicioDet").setAttribute('disabled',ver);
    if(Local.esSAT == 1) id("itemAgregarServicio").setAttribute('disabled',agregarserv);
    if(Local.esSAT == 1) id("itemAgregarProducto").setAttribute('disabled',agregarprod);
    if(Local.esSAT == 1) id("itemQuitarProducto").setAttribute('disabled',quitar);
    if(Local.esSAT == 1) id("itemClonarServicio").setAttribute('disabled',clonar);
}

function imprimirOrdenServicio(){
    var idx = id("listadoOrdenServicio").selectedItem;
    if(!idx) return;
    var osImporte       = id("os_importe_"+idx.value).getAttribute("value");
    var moneda        = 1;
    var importeletras = convertirNumLetras(osImporte,moneda);
    importeletras     = (osImporte > 0)? importeletras.toUpperCase():"";

    var url= "modulos/fpdf/imprimir_ordenservicio.php?idoc="+idx.value+
	     "&totaletras="+importeletras;
    location.href=url;
}

function vboxOrdenServicioDisplay(xval){
    var listosdet = id("listadoOrdenServicioDetalle");
    var listos    = id("listadoOrdenServicio");

    switch(xval){
    case 'block':
	listosdet.setAttribute('disabled',true);
	listos.setAttribute('disabled',true);
	break;
    case 'unblock':
	listosdet.removeAttribute('disabled');
	listos.removeAttribute('disabled');
	break;
    }

}

function facturarOrdenServicio(){

    var idx = id("listadoOrdenServicio").selectedItem;

    if(!idx) return;

    if(oscEstado != 'Finalizado' || oscFacturacion != 0) return;

    var xrequest = new XMLHttpRequest();
    var url = "modulos/ordenservicio/modordenservicio.php?modo=FacturarOrdenServicio" 
	+ "&xid=" + parseInt(idx.value)
	+ "&xlocal=" + Local.IdLocalActivo
	+ "&xdependiente=" + Local.IdDependiente;

    xrequest.open("GET",url,false);
    xrequest.send(null);
    xres = xrequest.responseText;

    var xres = xres.split('~');

    if (xres[0] != '0')
        return alert(po_servidorocupado+
		     '\n\n  :::: '+xres[0])+' ::::';
    //Productos...
    syncProductosPostTicket();
    //PreVentas...
    syncPresupuesto('Preventa');  
    //Ticket...
    VerTPV();  
    //Confirmar...
    if(!confirm("gPOS TICKET PREVENTA  - "+xres[2]+" - \n"+
		"\n  ORDEN DE SERVICIO : " +oscSerie+' - '+oscNumero+
		"\n  CLIENTE                    : "+oscCliente+
		"\n  IMPORTE                     : "+cMoneda[1]['S']+" "+oscImporte+
		"\n\n Se creo un - TICKET PREVENTA - con el detalle, desea cargarlo? ")) 
	return;
    //Ticket PreVenta...
    selTipoPresupuesto(1);
    cargarDetPresupuestoACarrito(xres[1],oscIdCliente,0);
}

function xmenuOrdenServicio(){
    var editar   = true;
    var facturar = true;
    var hidx     = id("listadoOrdenServicio").selectedItem;
    var imprimir = (id("listadoOrdenServicioDetalle").itemCount != 0)? false:true;

    switch(oscEstado){
    case 'Pendiente':
	editar = false;
	break
    case 'Ejecucion':
	editar = false;
	break;
    case 'Finalizado':
	facturar = false;
	editar   = (oscFacturacion != 0)? true:false;
	break;
    case 'Cancelado':
	editar = false;
	break;
    }

    facturar = (oscFacturacion != 0)? true:facturar;

    if(Local.esSAT == 1) id("itemEditarOrdenServicio").setAttribute('disabled',editar);
    id("itemFacturarOrdenServicio").setAttribute('disabled',facturar);
    id("itemImprimirOrdenServicio").setAttribute('disabled',imprimir);
}

function mostrarUbicacionServicio(xvalue,xedit=false){
    var xval = (xvalue == 'Local')? true:false;
    id("rowDireccionServicio").setAttribute('collapsed',xval);
}

function clonarOrdenServicioDet(){
    oscClon = true;
    var idex   = id("listadoOrdenServicio").selectedItem;
    var codigo = id("os_codigo_"+idex.value).getAttribute('label');
    id("idClienteOrdenServicio").value = oscIdCliente;
    id("FiltroPrioridad").value = '1';
    
    obtenerSerieNumeroOrdenServicio();
    RegistrarOrdenServicio(false);
    verProductoSat(true);
    CargarProductosSat();
    if(ospDetalleSat == 1)
	vertabProductoDetalleSat(true);
    mostrarPanelOrdenServicioDet(true,false);
}

function obtenerOrdenServicioDetalle(){

    var z   = null;
    var url = "modulos/ordenservicio/modordenservicio.php?modo=ObtenerOrdenServicioDetalle"+
        "&xidos="+cIdOrdenServicio;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    try{
	obj.send(null);
    } catch(z){
	return;
    }
    return obj.responseXML.documentElement;
}

function crearOrdenServicioDetalle2XML(xml){

    var idsuscipcion,idcliente,idtiposuscripcion,tiposuscripcion,fechainicio,fechafin,estado,prolongacion,comprobante,tipopago,observaciones,detalle,osOrdenAnterior;
    if(ordenserviciodetserv[ cIdOrdenServicio ])
	ordenserviciodetserv[ cIdOrdenServicio ] = new Array();

    for (var i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
	    t = 0;

	    IdOrdenServicioDet    = node.childNodes[t++].firstChild.nodeValue;
	    osIdProducto          = node.childNodes[t++].firstChild.nodeValue;
	    osIdUserResponsable   = node.childNodes[t++].firstChild.nodeValue;
	    osIdComprobante       = node.childNodes[t++].firstChild.nodeValue;
	    osFechaInicio         = node.childNodes[t++].firstChild.nodeValue;
	    osFechaFin            = node.childNodes[t++].firstChild.nodeValue;
	    osEstado              = node.childNodes[t++].firstChild.nodeValue;
	    osGarantia	          = node.childNodes[t++].firstChild.nodeValue;
	    osEstadoGarantia      = node.childNodes[t++].firstChild.nodeValue;
	    osGarantiaCondicion   = node.childNodes[t++].firstChild.nodeValue;
	    osEstadoSolucion      = node.childNodes[t++].firstChild.nodeValue;
	    osConcepto            = node.childNodes[t++].firstChild.nodeValue;
	    osNumeroSerie         = node.childNodes[t++].firstChild.nodeValue;
	    osUnidades            = node.childNodes[t++].firstChild.nodeValue;
	    osPrecio              = node.childNodes[t++].firstChild.nodeValue;
	    osImporte             = node.childNodes[t++].firstChild.nodeValue;
	    osProducto            = node.childNodes[t++].firstChild.nodeValue;
	    osUsuarioResponsable  = node.childNodes[t++].firstChild.nodeValue;
	    osTipoServicio        = node.childNodes[t++].firstChild.nodeValue;
	    osTipoProducto        = node.childNodes[t++].firstChild.nodeValue;
	    osCodigoBarras        = node.childNodes[t++].firstChild.nodeValue;
	    osUbicacion           = node.childNodes[t++].firstChild.nodeValue;
	    osDireccion           = node.childNodes[t++].firstChild.nodeValue;
	    osObservacion         = node.childNodes[t++].firstChild.nodeValue;
	    osOrdenAnterior       = node.childNodes[t++].firstChild.nodeValue;
	    osEsProductoSat       = node.childNodes[t++].firstChild.nodeValue;
	    osProductoSat         = node.childNodes[t++].firstChild.nodeValue;


	    crearOrdenServicioDetalle2Servicio(IdOrdenServicioDet,osIdProducto,
					       osIdUserResponsable,osIdComprobante,
					       osFechaInicio,osFechaFin,osEstado,osGarantia,
					       osEstadoGarantia,osGarantiaCondicion,  
					       osEstadoSolucion,osConcepto,osNumeroSerie,
					       osUnidades,osPrecio,osImporte,osProducto,
					       osUsuarioResponsable,osTipoServicio,       
					       osTipoProducto,osCodigoBarras,osUbicacion,
					       osDireccion,osObservacion,osEsProductoSat,
					       osProductoSat,osOrdenAnterior); 
        }
    }
}

function crearOrdenServicioDetalle2Servicio(xiddet,xIdProducto,
					    xIdUserResponsable,xIdComprobante,
					    xFechaInicio,xFechaFin,xEstado,xGarantia,
					    xEstadoGarantia,xGarantiaCondicion,  
					    xEstadoSolucion,xConcepto,xNumeroSerie,
					    xUnidades,xPrecio,xImporte,xProducto,
					    xUsuarioResponsable,xTipoServicio,       
					    xTipoProducto,xCodigoBarras,xUbicacion,
					    xDireccion,xObservacion,xEsProductoSat,      
					    xProductoSat,xOrdenAnterior){

    if( !ordenserviciodetserv[ cIdOrdenServicio ] ) 
	ordenserviciodetserv[ cIdOrdenServicio ] = new Array();
    ordenserviciodetserv[ cIdOrdenServicio ].push(xiddet);


    if( ordenserviciodet[ xiddet ] )
	return updateOrdenServicioDetalle2Servicio(xiddet,xIdProducto,
						   xIdUserResponsable,xIdComprobante,
						   xFechaInicio,xFechaFin,xEstado,xGarantia,
						   xEstadoGarantia,xGarantiaCondicion,  
						   xEstadoSolucion,xConcepto,xNumeroSerie,
						   xUnidades,xPrecio,xImporte,xProducto,
						   xUsuarioResponsable,xTipoServicio,       
						   xTipoProducto,xCodigoBarras,xUbicacion,
						   xDireccion,xObservacion,xEsProductoSat,
						   xProductoSat,xOrdenAnterior);

    //Orden Servicio Detalle
    ordenserviciodetlist[ iordenserviciodet++ ]   = xiddet;		
    ordenserviciodet[ xiddet ]                    = new Object();	
    ordenserviciodet[ xiddet ].idordenserviciodet = xiddet;
    ordenserviciodet[ xiddet ].idproducto         = xIdProducto;
    ordenserviciodet[ xiddet ].iduserresponsable  = xIdUserResponsable;
    ordenserviciodet[ xiddet ].idcomprobante      = xIdComprobante;
    ordenserviciodet[ xiddet ].fechainicio        = xFechaInicio;
    ordenserviciodet[ xiddet ].fechafin           = xFechaFin;
    ordenserviciodet[ xiddet ].estado             = xEstado;
    ordenserviciodet[ xiddet ].garantia           = xGarantia;
    ordenserviciodet[ xiddet ].estadogarantia     = xEstadoGarantia;
    ordenserviciodet[ xiddet ].condiciongarantia  = xGarantiaCondicion;
    ordenserviciodet[ xiddet ].solucion           = xEstadoSolucion;
    ordenserviciodet[ xiddet ].concepto           = xConcepto;
    ordenserviciodet[ xiddet ].serie              = xNumeroSerie;
    ordenserviciodet[ xiddet ].unidades           = xUnidades;
    ordenserviciodet[ xiddet ].precio             = xPrecio;
    ordenserviciodet[ xiddet ].importe            = xImporte;
    ordenserviciodet[ xiddet ].producto           = xProducto;
    ordenserviciodet[ xiddet ].responsable        = xUsuarioResponsable;
    ordenserviciodet[ xiddet ].tiposervicio       = xTipoServicio; 
    ordenserviciodet[ xiddet ].tipoproducto       = xTipoProducto; 
    ordenserviciodet[ xiddet ].codigobarras       = xCodigoBarras; 
    ordenserviciodet[ xiddet ].ubicacion          = xUbicacion; 
    ordenserviciodet[ xiddet ].direccion          = xDireccion; 
    ordenserviciodet[ xiddet ].observaciones      = xObservacion;
    ordenserviciodet[ xiddet ].essat              = xEsProductoSat;
    ordenserviciodet[ xiddet ].productosat        = xProductoSat;
    ordenserviciodet[ xiddet ].ordenanterior      = xOrdenAnterior;


}
function updateOrdenServicioDetalle2Servicio(xiddet,xIdProducto,
					    xIdUserResponsable,xIdComprobante,
					    xFechaInicio,xFechaFin,xEstado,xGarantia,
					    xEstadoGarantia,xGarantiaCondicion,  
					    xEstadoSolucion,xConcepto,xNumeroSerie,
					    xUnidades,xPrecio,xImporte,xProducto,
					    xUsuarioResponsable,xTipoServicio,       
					    xTipoProducto,xCodigoBarras,xUbicacion,
					    xDireccion,xObservacion,xEsProductoSat,      
					     xProductoSat,xOrdenAnterior){

    ordenserviciodet[ xiddet ].idproducto         = xIdProducto;
    ordenserviciodet[ xiddet ].iduserresponsable  = xIdUserResponsable;
    ordenserviciodet[ xiddet ].idcomprobante      = xIdComprobante;
    ordenserviciodet[ xiddet ].fechainicio        = xFechaInicio;
    ordenserviciodet[ xiddet ].fechafin           = xFechaFin;
    ordenserviciodet[ xiddet ].estado             = xEstado;
    ordenserviciodet[ xiddet ].garantia           = xGarantia;
    ordenserviciodet[ xiddet ].estadogarantia     = xEstadoGarantia;
    ordenserviciodet[ xiddet ].condiciongarantia  = xGarantiaCondicion;
    ordenserviciodet[ xiddet ].solucion           = xEstadoSolucion;
    ordenserviciodet[ xiddet ].concepto           = xConcepto;
    ordenserviciodet[ xiddet ].serie              = xNumeroSerie;
    ordenserviciodet[ xiddet ].unidades           = xUnidades;
    ordenserviciodet[ xiddet ].precio             = xPrecio;
    ordenserviciodet[ xiddet ].importe            = xImporte;
    ordenserviciodet[ xiddet ].producto           = xProducto;
    ordenserviciodet[ xiddet ].responsable        = xUsuarioResponsable;
    ordenserviciodet[ xiddet ].tiposervicio       = xTipoServicio; 
    ordenserviciodet[ xiddet ].tipoproducto       = xTipoProducto; 
    ordenserviciodet[ xiddet ].codigobarras       = xCodigoBarras; 
    ordenserviciodet[ xiddet ].ubicacion          = xUbicacion; 
    ordenserviciodet[ xiddet ].direccion          = xDireccion; 
    ordenserviciodet[ xiddet ].observaciones      = xObservacion;
    ordenserviciodet[ xiddet ].essat              = xEsProductoSat;
    ordenserviciodet[ xiddet ].productosat        = xProductoSat;
    ordenserviciodet[ xiddet ].ordenanterior      = xOrdenAnterior;
}

function RecibirGarantiaProducto(){
    oscClon = false;

    id("idClienteOrdenServicio").value = cIdClienteComprobante;
    id("FiltroPrioridad").value        = '1';
    id("TipoOrdenServicio").value      = 'Garantia';
    id("TipoOrdenServicio").setAttribute('value','Garantia');
    obtenerSerieNumeroOrdenServicio();
    RegistrarOrdenServicio(false);
    actualizarGarantiaComprobante();

    id("ObservacionServicio").value    = 'Garantía, '+cComprobante+': '+cSerieNroComprobante;
    verProductoSat(true);

    if(ospDetalleSat == 1)
	vertabProductoDetalleSat(true);
    mostrarPanelOrdenServicioDet(true,false);
}

function actualizarGarantiaComprobante(){

    var url = "modulos/ordenservicio/modordenservicio.php?"+
              "modo=ActualizarGarantiaComprobanteDet"+
	      "&xidos="+cIdOrdenServicio+
 	      "&xidcd="+cIdComprobanteDet;

    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    id("xdetalleventa_ordenservicio_"+cIdComprobanteDet).setAttribute("value",cIdOrdenServicio);
    
}
/*++++++++++++++++++++++++ ORDEN SERVICIO ++++++++++++++++++++++++++++*/


/*++++++++++++++++++++++++ INIT XULVIEW LIST PRODUCTOs  ++++++++++++++++++++++++++*/
 if( Local.esB2B == 1 ) viewListaProductoPrecios('PVC');
 if( Local.esWESL  ) viewListaProductoPrecios('PVD');
 if( Local.esWESL  ) viewListaProductoPrecios('PVE');

