
/*++++++++++++++++++++++ INIT +++++++++++++++++++++++++*/

    var id          = function(name) { return document.getElementById(name); }
    var comprobante = 0;
    var Vistas      = new Object(); 
    Vistas.ventas   = 7;
    Vistas.abonar   = 10;
    Vistas.tpv      = 0; 
    Vistas.caja     = 11; 

    //Ultimo articulo aÃ±adido al carrito.
    var xlastArticulo;

    /*++++ Conexion Estatus ++++++++*/

    //INFO: imagen de prohibido, para utilizar en seÃ±alizar conexion perdida
    var esGraficoConectado = true;
    var urlprohibido       = "data:image/gif;base64,R0lGODlhDAAMAMQAAPpbW/8AAPPz8/8zM/XFxfednf9aWveUlP4PD/iJifTl5flycv0iIvTV1f4KCv9mZvenp/4XF////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAUUABIALAAAAAAMAAwAAAVHoCAeQxAMhygqpekOisiYAAQB5iAcppOoEBMp4MgRRLhW4eF6KIIuiIDQYixyAYAqgQhETryAVNRopVq1W27Vcp1iqiFYFQIAOw==";

    /*+++++++ POOL  ++++++*/

    //var productos_series = new Array();
    var all_series      = new Array();
    var all_series_cb   = new Array();
    var iprodSerie      = 0;
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

    function Reload_demon_syncTPV(){
	//Sync
	esOffLineSyncTPV = false;
	esSyncBoton();
	pushSyncTPV();//lanza Sync Demon
    }

    function pushSyncTPV(){
	
	//Termina Brutal
	if(esSyncBoton('on')) return;

	syncProductosPostTicket();               //Sync Productos
        syncCargarPresupuesto('Proforma');       //Sync Proformas
	syncCargarPresupuesto('Preventa');       //Sync PreVentas
	setTimeout("syncCargarMProducto()",100); //Sync MetaProductos
        setTimeout("syncClientes()",300);        //Sync Clientes 
	setTimeout("syncPromociones()",300);     //Sync Promociones
	//Termina
        esSyncBoton('pause');
    }

    function Demon_syncClientes(){
    
	//esSyncTPV true?
	if(esSyncTPV)
	    return setTimeout("Demon_syncClientes()",2000);//esperar 5seg
	
	esSyncTPV = true;//Bloquea syncTPV

	esSyncBoton('on');//Boton Sync
	syncClientes();//Sync Clientes

	esSyncTPV = false;//Libera syncTPV
        esSyncBoton('pause');//Boton Sync Termina
	
	//Lanzar demonio
        setTimeout("Demon_syncClientes()",1500000);//33seg
    }

    function Demon_syncMProductos(){
	
	//esSyncTPV true?
	if(esSyncTPV)
	    return setTimeout("Demon_syncMProductos()",2000);//esperar 5seg
	
	//Boton Sync
	esSyncBoton('on');

	//Sync Productos
	syncCargarMProducto();//Sync MetaProductos

	//Boton Sync Termina
        esSyncBoton('pause');

	//Lanza demonio
        setTimeout("Demon_syncMProductos()",1700000);//27seg
    }

    function Demon_syncPreventas(){

	//esSyncTPV true?
	if(esSyncTPV)
	    return setTimeout("Demon_syncPreventas()",2000);//esperar 6seg
	
	//Bloq syncTPV*****
	esSyncTPV = true;

	//Boton Sync
	esSyncBoton('on');

	//Sync Proformas
        syncCargarPresupuesto('Proforma');
	
	//Sync Preventa
	syncCargarPresupuesto('Preventa');
        //setTimeout("syncCargarPresupuesto('Preventa')",2000);espera 4seg
	
	//Boton Sync Termina
        esSyncBoton('pause');

	//Lanzar 
        setTimeout("Demon_syncPreventas()",2000000);//21seg
    }

    function Demon_syncProductos(){

	//esSyncTPV true?
	if(esSyncTPV)
	    return setTimeout("Demon_syncProductos()",2000);//esperar 5seg

	//Boton Sync
	esSyncBoton('on');
	//alert("sync Productos");
	//Lanza sync productos
	syncProductosTPV();//Sync Productos TPV

	//Boton Sync Termina
        esSyncBoton('pause');
	
	//Lanza demonio
	setTimeout("Demon_syncProductos()",900000);//40seg
    }

    function Demon_CargarNuevosMensajes(){
        //alert("gPOS: cargando!1...");
        if (!AjaxMensajes)
            AjaxMensajes = new XMLHttpRequest();	

	//Boton Sync
	esSyncBoton('on');

        //Peticiones realizadas
        peticionesSinRespuesta = peticionesSinRespuesta +1;			

        var url = "modulos/mensajeria/modbuzon.php?modo=leernuevos&IdUltimo=" + ultimoLeido;
        url = url + "&desdelocal="+encodeURIComponent(  Local.nombretienda );	

        AjaxMensajes.open("POST",url,true);
        AjaxMensajes.onreadystatechange = RececepcionMensajes;
        AjaxMensajes.send(null)

	//Boton Sync Termina
        esSyncBoton('pause');

        setTimeout("Demon_CargarNuevosMensajes()",26000);//

        ActualizacionEstadoOnline();

    }

    function Demonio_Mensajes(){
        try {
            if (!AjaxMensajes)
                AjaxMensajes = new XMLHttpRequest();	
        } catch(e) {
            return;
        }
	//Boton Sync
	esSyncBoton('on');

        var url = "modulos/mensajeria/modbuzon.php?modo=hoy";

        AjaxMensajes.open("POST",url,true);
        AjaxMensajes.onreadystatechange = RececepcionMensajes;
        AjaxMensajes.send(null)	

	//Boton Sync Termina
        esSyncBoton('pause');

        setTimeout("Demon_CargarNuevosMensajes()",5000);
    }

 
    function Demonio_Productos(){
	
	syncProductosTPV();
        setTimeout("Demon_syncProductos()",64000);// 1.3 min
	
    }

    function Demonio_Promociones(){
	
	syncPromociones();
        setTimeout("Demonio_Promociones()",1200000);// 40 min 1800000
	
    }

    function Demonio_Preventas(){
	    
	//Combo Proformas
	CargarPresupuesto('Proforma');
	
	//Combo Preventa
        setTimeout("CargarPresupuesto('Preventa')",3000);//5seg
	
	//Lanzar demonio
        setTimeout("Demon_syncPreventas()",32000);//45seg
    }

    function Demonio_MProductos(){

	//Combo MProductos	     
	CargarComboMProducto();
	
	//Combo 
        setTimeout("CargarMProducto()",2000);//5seg
	    
	//Lanzar demonio
        setTimeout("Demon_syncMProductos()",36000);//27seg
	
    }

    function Demonio_Clientes(){
	 
	//Sync CLientes
	syncClientes();
	
	//Lanzar demonio
        setTimeout("Demon_syncClientes()",33000);//33seg
	
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
    function tA(idproducto,codigo,Lnombre,imagen,referencia,centimopvd,centimopvc,impuesto,LTalla, 
		LColor,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,Lalias1,Lalias2,
		refprovhab,unidades,serie,LMarca,costo,ventamenudeo,unidxcontenedor,unidmedida,
		Llaboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
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
        tAL(idproducto,codigo,nombre,imagen,referencia,centimopvd,centimopvc,impuesto,talla, color, 
	    Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,alias1,alias2,refprovhab,
	    unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,unidmedida,
	    laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
	    ilimitado,dosis);
    }

    function tAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,centimopvc,impuesto,Talla,
		 Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,alias1,alias2,
		 refprovhab,unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,unidmedida,
		 laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
		 ilimitado,dosis){
	
        if (!codigo) return;//No acepta lexers

        codigo   = new String(codigo);	

	//Ya tenemos este producto listado
        if (pool.Existe( codigo.toUpperCase() ))
            return stAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,centimopvc,impuesto,
			Talla,Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,
			alias1,alias2,refprovhab,unidades,serie,marca,costo,ventamenudeo,
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
    }

    function stAL(idproducto,codigo,Nombre,imagen,referencia,centimopvd,centimopvc,impuesto,Talla,
		  Color,Oferta,OfertaUnid,pvo,condventa,idsubsidiario,nombre2,rKardex,alias1,alias2,
		  refprovhab,unidades,serie,marca,costo,ventamenudeo,unidxcontenedor,unidmedida,
		  laboratorio,contenedor,pvdd,pvcd,vence,lote,servicio,mproducto,
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
		ModificarEntradaEnProductos(xsyn.producto,codigo,xsyn.referencia,precio,
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

        case 112 : 
	    VerTPV();
	    break;

        case 113 : 
	    MostrarUsuariosForm();
	    break;

        case 45 : 
	    pushSyncTPV();
	    break;

        case 115 : 
	    elijeComprobanteTPV();
	    break;

        case 118 : 
	    selTipoPresupuesto(2);
	    id("buscapedido").focus(); 
	    break;

        case 119 : 
	    selTipoPresupuesto(1);
	    id("buscapedido").focus(); 
	    break;

        case 120 : 
	    VerVentas();
	    break;

        case 121 : 
	    VerCaja();
	    break;
	}

	if(event.shiftKey) 

	    switch (event.keyCode) { 

            case 122 : 
		//Shift + F11  
		break;

            case 123 : 
		VerServicios();
		break;
            case 46 : 
		selTipoPresupuesto(0);
		id("NOM").focus(); 
		break;
	    } 

	if(event.ctrlKey) 

	    switch (event.keyCode) { 

            case 114 : 
		//Shift + F3 
		break;

	    } 
    }


/*+++++++++++++++++++++++++++++ PREVENTA ++++++++++++++++++++++++++++++++++*/


        /*++++++++++++ PREVENTA ++++++++++++*/

        //Array Presupuestos
        var aProforma = new Array();
        var aPreventa = new Array();
        var aPedido   = new Array();

        //Array Meta Productos
        var aMProductos = new Array();


        /*++++++++++++++++ Busquedas ++++++++++++++*/

        function buscarNroTicket(){
 	    var snr = id("buscapedido");
	    var stv = id("t_preventa").getAttribute('checked');
	    var stp = id("t_proforma").getAttribute('checked');
	    var stm = id("t_mproducto").getAttribute('checked');
	    var tpd;

	    //Numero buscado
	    sid = snr.value;

	    //Ticket 
	    if( stv == 'true') tpd = 'Preventa';
	    if( stp == 'true') tpd = 'Proforma';
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
			CrearEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
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
            if (unidades=="mayoreo")
	    {

		if(!productos[cod].menudeo) return;

		var xunidades = prompt('¿Cuántas '+productos[cod].cont+'+'+
				       productos[cod].unid+'?',0);
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
					cod);

	    //Carrito TPV
            tpv.AddCarrito( cod.toUpperCase() , unidades);
            //RecalculoTotal();***

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
		laboratorio = productos[cod].laboratorio.toUpperCase();	
		//var tienda1 = new String(tienda);

		//if (nom.indexOf( nombre ) != -1) {
		if  ( (nom.indexOf( cadena1 ) != -1) || 
		      (al1.indexOf( cadena1 ) != -1) || 
		      (al2.indexOf( cadena1 ) != -1))  
		{
		    k      = productos[cod];
		    precio = (Local.TPV=='VC')? k.pvc:k.pvd;

		    if(cadena2=="")
		    {
			CrearEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
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
			    CrearEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
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
					vcb);
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
				CrearEntradaEnProductos(k.producto,k.codigobarras,k.referencia,
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

		    aSeries  = ( celdas[10] != '0')? celdas[10]:0;
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

		precio    = parseFloat(celdas[4]);
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
		    
		    aSeries  = ( celdas[10] != '0')? celdas[10]:0;
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
            id("tic_precio_"+ vcb).setAttribute("value",formatDinero(precio));	
            Blink("tic_precio_" + vcb, "label-precio" );

	    //Descuento
            id("tic_descuento_"+ vcb ).setAttribute("value",FormateComoDescuento(descuento));
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
            id("tic_precio_"+ vcb).setAttribute("value",formatDinero(precio));	
            Blink("tic_precio_" + vcb, "label-precio" );
	    
	    //Actulaliza el consolidado carrito productos
            //RecalculoTotal();***
	    
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
					  'cargarIdMProducto('+celdas[0]+',"'+celdas[1]+'")');
		    combo.appendChild(elemento);
		}

	    }
	    //<menuitem label="Todos" selected="true"  />
	}

        function cargarIdMProducto(Id,label){

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


        function CargarMProducto(){

	    generadorCargarMProducto('MProducto','0');
	    return;
	}

        function CargarPresupuesto(tipopresupuesto){

	    generadorCargarPresupuesto(tipopresupuesto,'0','');
	    return;
	}

        /*++++++++++++++++++ SET/PON +++++++++++++++++++++*/

        function setNombreLocalActivo(dlocal){
	    //cambia nombre local activo
	    var d_local  = id("depLocalLista");
	    var i_dlocal = id("localdep_"+dlocal);
	    d_local.setAttribute('label',i_dlocal.label);
	    
	    if(Local.IdLocalDependiente!=Local.IdLocalActivo)
		setControlLocalDependiente(true);	
	    else
		setControlLocalDependiente(false);	
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

        function setControlLocalDependiente(op){
	    if(op)
	    {
		id("botonImprimir").setAttribute("disabled","true");
		id("botonsalirtpv").setAttribute("disabled","true");
		//id("VerCajaButton").setAttribute("disabled","true");
		id("VerListadosButton").setAttribute("disabled","true");
		id("VerServiciosButton").setAttribute("disabled","true");
		id("botonBorrar").setAttribute("disabled","true");
		id("botonGuardar").setAttribute("disabled","true");
	    }
	    else
	    {
		habilitabotonimprimir();
		id("botonsalirtpv").removeAttribute("disabled");
		//id("VerCajaButton").removeAttribute("disabled");
		id("VerListadosButton").removeAttribute("disabled");
		id("VerServiciosButton").removeAttribute("disabled");
		id("botonBorrar").removeAttribute("disabled");
		id("botonGuardar").removeAttribute("disabled");

	    }
	}

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
	    var i_mpro  = id("t_mproducto");
	    var s_prev  = id("SelPreventa");
	    var s_prof  = id("SelProforma");
	    var s_mpro  = id("SelMProducto");
	    var p_stock = id("prevt-stock");
	    var r_mprod = id("rMProducto");
	    var r_pedid = id("rPedido");
	    var r_cesio = id("rCesion");
	    var r_venta = id("rVenta");
	    var modo    = id("rgModosTicket");	   
 	    var t_bpedi = id("buscapedido");
	    var modotpv;

	    //CONTROL
	    switch( modo.value ){
	    case 'venta':
	    case 'cesion':
		modotpv = 1;
 		break;
	    case 'pedidos': 
		if( selticket == 2 )
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
		var m_alert = 'VENTA ó CESION';

		//PROFORMA
		if(selticket == 2)
		    m_alert = 'VENTA, CESION ó COTIZACION';

		//METAPRODUCTO
		if(selticket == 4)
		    m_alert = 'MPRODUCTO';

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
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'true');

		//Oculta listas
		s_prev.setAttribute("collapsed", "true");
		s_prof.setAttribute("collapsed", "true");
		s_mpro.setAttribute("collapsed", "true");
		t_bpedi.setAttribute("collapsed", "true");

		//Oculta check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "true");

		//Limpia Lista proformas
		s_prof.selectedItem=2;
		s_prof.setAttribute("label", "Elije ticket....");
		
		//Limpia Lista preventas
		s_prev.selectedItem=0;
		s_prev.setAttribute("label", "Elije ticket....");

		//Muestra Controles
		r_mprod.setAttribute("collapsed", "false");
		r_pedid.setAttribute("collapsed", "false");

		//Default Variables Globales
		IdTipoPresupuesto = 0;
		IdPresupuesto     = 0;  
		 IdMProducto       = 0;  
		IdMetaProducto    = 0;  
		StockMetaProducto = 0;
		//reset modo
		AjustarEtiquetaMetaproducto();
		resetPresupuestoCarrito();

		break;

	    case 1:
                //Ticket Preventa
		t_comb.label="TICKET PREVENTA";

		i_prof.setAttribute('checked', 'false');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'true');
		i_actu.setAttribute('checked', 'false');

		//Limpia Lista
		s_prev.selectedItem=0;
		s_prev.setAttribute("label", "Elije ticket....");
		
		//Oculta listas
		s_prof.setAttribute("collapsed", "true");
		s_prev.setAttribute("collapsed", "false");
		s_mpro.setAttribute("collapsed", "true");

		//Oculta Controles
		r_mprod.setAttribute("collapsed", "true");
		r_pedid.setAttribute("collapsed", "true");

		//Busqueda de nro pedidos
		t_bpedi.setAttribute("collapsed", "false");

		//Muestra check stock
		p_stock.setAttribute('checked', 'true');
		p_stock.setAttribute("collapsed", "false");

		//set tipo presupuesto
		IdTipoPresupuesto = 1;
		
		//reset modo
		resetPresupuestoCarrito();
		setTimeout("Demon_syncPreventas()",1500);//21seg
 		break;

	    case 2:
		//MENU Ticket Proforma
		t_comb.label="TICKET PROFORMA";
		i_prof.setAttribute('checked', 'true');
		i_mpro.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'false');

		s_prof.setAttribute("collapsed", "false");
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
	    case 4:
		//MENU Ticket MProducto
		t_comb.label="TICKET MPRODUCTO";
		i_mpro.setAttribute('checked', 'true');
		i_prof.setAttribute('checked', 'false');
		i_prev.setAttribute('checked', 'false');
		i_actu.setAttribute('checked', 'false');

		s_prof.setAttribute("collapsed", "true");
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
		if(confirm(c_gpos + " TPV MPRODUCTOS \n"+
			   "\n Cliente  : "+nuevoNombreUsuario+
			   "\n\n MProducto(s) : \n" +t_prod+
			   "\n                                    "+
			   " Cargar MProducto(s) a la Cotización? ") ){
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
			raw_agnadirPorCodigoBarras(vcb);//Agnade Item
			
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
	    id("tic_unid_" + vcb).value = xunidades;
	    ticket[vcb].unidades        = xunidades;

	    //Precio...
            id("tic_precio_"+ vcb).setAttribute("value",formatDinero(precio));	
            Blink("tic_precio_" + vcb, "label-precio" );

	    //Descuento...
	    id("tic_descuento_"+ vcb ).setAttribute("value",FormateComoDescuento(descuento));
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

	    if (!productos[cod].dosis ) return;
	    
	    var cfichaTecnica = productos[cod].dosis;
	    var esBTCA        = (Local.Giro=='BTCA')?true:false;
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
				   productos[cod].producto);

	    if( trim( xconcepto ) == '' ) return;
	    
	    //xconcepto = xconcepto.toUpperCase();
	    ticket[cod].concepto = xconcepto;

	    id("tic_nombre_"+cod).setAttribute('value',xconcepto); 
	    id("tic_concepto_"+cod).setAttribute('value',xconcepto); 
	} 


         function ConceptoParaFilaPreventa( xconcepto,vcb ){
	    xconcepto = xconcepto.toUpperCase();
	    ticket[vcb].concepto = xconcepto;

	    id("tic_nombre_"+vcb).setAttribute('value',xconcepto); 
	    id("tic_concepto_"+vcb).setAttribute('value',xconcepto); 
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

	    id("preventaFichaTecnica").setAttribute("disabled",true);
	    id("preventaMayoreo").setAttribute("disabled",true);
	    id("preventaDetalleMProducto").setAttribute("disabled",true);
	    id("preventaNumerosSeries").setAttribute("disabled",true);

	    var cod  = (xlisttpv)? getCodigoSelectedTicket():getCodigoSelectedProd();
	    if(cod == null) return;

	    if ( productos[cod].serie ) id("preventaNumerosSeries").removeAttribute("disabled");
	    if ( productos[cod].menudeo ) id("preventaMayoreo").removeAttribute("disabled")
	    if ( productos[cod].mproducto ) id("preventaDetalleMProducto").removeAttribute("disabled");
	    if ( productos[cod].dosis ) id("preventaFichaTecnica").removeAttribute("disabled");

            //Si lo ha encontrado, sera una buena idea mostrar el cb y su foto,si la hay..
	    //setImagenProducto( cod );
            //setTimeout("UpdateImageview()",50);
            setTimeout("setImagenProducto("+cod+")",200);

        }

/*+++++++++++++++++++++++++++++ SERVICES ++++++++++++++++++++++++++++++++++*/


 
    var impuesto_normal = vIGV;//TODO: impuesto de subsidiarios
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

        id("tic_nombre_"+ arregloid ).setAttribute("value",arregloid+' '+servicio);
        id("tic_pedidodet_"+ arregloid ).setAttribute("value",'servicio-externo');
        id("tic_descuento_"+ arregloid ).setAttribute("value",FormateComoDescuento(descuento));
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

    function agnadirPorSeries(xseries,xproducto,xcantidad,xcod){

	id("selCB").value = xcod;
	limpiarlistaserie();
	listarseries(xseries);	

	id("nsProducto").setAttribute("label",xproducto);
	id("totalNS").setAttribute("label",xcantidad);
	id("nsTitulo").setAttribute("label","Carrito TPV - Elegir Stock");

        MostrarDialogoSeries();
	id("ckserie").focus();
    }

    function listarseries(xseries){

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
	id("tic_unid_" + xcod).value = xunidades;
	ticket[xcod].unidades        = xunidades;

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

	//Series
	if( modo != "pedidos" && productos[cod].serie)
	    return agnadirPorSeries(productos[cod].serie,productos[cod].producto,
				    productos[cod].unidades,cod);

        cuantas = ( cuantas<0 )? prompt(po_cuantasunidades,0):parseInt(cuantas);

	if (isNaN(cuantas)) return alert( c_gpos + 'Ingresar un valor numérico');
	
        if (cuantas<0) return alert( c_gpos + 'Ingresar un valor numérico positivo.');

	cuantas = parseInt(cuantas);//Control de Enteros	

        if (cuantas==0) return QuitarArticulo();

        unidadesenventa = unidcod.getAttribute("value");
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
		var unid      = ticket[cod].unid;

		oferta   = ( ounid >= tunid )? tunid:ounid;
		xdetalle = '**OFERTA '+oferta+''+unid+' c/u '+formatDinero(pvo)+'** ';
		oferta   = tunid+'~'+ounid+'~'+precio+'~'+pvo;//uni:ofertaunid:pv:pvo
		precio   = ( ounid >= tunid )? pvo:(pvo*ounid+(tunid-ounid)*precio)/tunid;

		id("tic_precio_"+cod).value  = precio.toFixed(2);
		id("tic_oferta_"+cod).value  = oferta;
		id("tic_detalle_"+cod).value = xdetalle;
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
					cod);
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
                        dato 	       = id("tic_unid_" + codigo).value;
                        impuesto       = id("tic_impuesto_" + codigo).value;
                        impuesto       = CleanInpuesto(impuesto);
                        filacantidad   = parseMoney( dato );
                        filaprecio     = parseMoney( id("tic_precio_" + codigo).value );
                        filadscto      = CleanDescuento( id("tic_descuento_" + codigo).value );
                        filasubtotal   = (parseFloat( filacantidad ) * parseFloat( filaprecio ));
                        filatotal      = parseFloat( filasubtotal ) - parseFloat( filasubtotal )*(parseFloat( filadscto )/100);
			filatotal       = formatDineroTotal( filatotal );
                        ticketsubtotal += parseFloat( filasubtotal);
			tickettotal    += parseFloat( filatotal );

                        id("tic_importe_" + codigo).value = formatDineroTotal( filatotal );
                    }
                }		 
            }
        }

	tickettotaldscto = ticketsubtotal - tickettotal;

        id("TotalLabel").setAttribute("label", cMoneda[1]['S'] +" "+ formatDineroTotal( tickettotal ) );
        id("SubTotalLabel").setAttribute("value", cMoneda[1]['S'] +" "+ formatDineroTotal( ticketsubtotal ) );
        id("DescuentoLabel").setAttribute("value", cMoneda[1]['S'] +" "+ formatDineroTotal( tickettotaldscto ) );

        Global.totalbase   = formatDineroTotal( tickettotal );
	ticketTotalImporte = formatDineroTotal( ticketsubtotal );
 

 	/*++++ Promocion +++++*/
	if( !lPromocionSeleccionado ) cargarPromocion();
	lPromocionSeleccionado = false;
    }

    function ModificarPrecio() {
        var ticketcodigo = getCodigoSelectedTicket();
        if (!ticketcodigo)	return;
        var ticprecio = id("tic_precio_"+ ticketcodigo);
        if (!ticprecio) return;

        p = parseMoney(prompt("Nuevo precio?", ticprecio.value ));
        if(p){
            ticprecio.setAttribute("value",formatDinero(p));	
            Blink("tic_precio_" + ticketcodigo, "label-precio" );
            RecalculoTotal();
        }
    }

    function ModificarDescuento(adm) {

	var modo = id("rgModosTicket").value;
        var ticketcodigo = getCodigoSelectedTicket();

        if (!ticketcodigo) return;

        var ticdscto   = id("tic_descuento_"+ ticketcodigo);
        var ticprecio  = parseFloat( id("tic_precio_"+ ticketcodigo).value );
        var cantidad   = parseFloat( id("tic_unid_" + ticketcodigo).value );
 	var precio     = (Local.TPV=='VC')? productos[ticketcodigo].pvc  : productos[ticketcodigo].pvd;
        var preciodcto = (Local.TPV=='VC')? productos[ticketcodigo].pvcd : productos[ticketcodigo].pvdd;
        var maxdes     = Math.round(parseFloat((precio - preciodcto)*cantidad)*100)/100;
        var dscto      = prompt("Descuento máximo permitido: "+maxdes+" "+cMoneda[1]['TP'], 0);
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

        ticdscto.setAttribute("value",FormateComoDescuento(dscto));	 
        Blink("tic_descuento_" + ticketcodigo, "label-descuento" );
        RecalculoTotal();
    }

    function CleanDescuento( valor ) {
        if (!valor) 	return 0.0;

        valor = valor.replace(/ /g,"");
        valor = valor.replace(/%/g,"");
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
            return "  ";//Especial para no hacer tan presente el descuento, dado que la mayor parte del 
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
	var ticketSync = false;

	//Valida modos TPV
	if( modo=="mproducto") var modotpv = 1;

	//esSyncTPV true?
	if(!esSyncTPV)
	{
	    esSyncTPV  = true;//Bloquea syncTPV
	    ticketSync = true;//Bloqueado por Ticket
	}
	
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
	    var url="modulos/fpdf/codigo.php?codigo="+IdMProducto;

	}
	var xEstado = ( Estado == 'Ensamblaje' )? 'Arreglo':Estado;
	//Regresamos el Numero Pre-Venta
	alert( c_gpos + ".                                 "+
	      "- TPV METAPRODUCTOS -"+
	      "                                 .\n\n"+
	      "Cliente     :  "+t_client+" \n"+	  
	      "Producto  :  "+t_mprod+" \n"+	  
	      "Estado     :  "+xEstado+" *** \n"+	  
	      "N/S          :  "+resultado+" *** ");

	// Finaliza el proceso
	CerrarPeticion();
	habilitarControles(); 
	document.getElementById('busquedaVentas').removeAttribute('disabled');
	VaciarDetallesVentas(); 
	document.getElementById('NumeroDocumento').removeAttribute('disabled');
	CancelarVenta();
	HabilitarImpresion();

	//ASEGURAMOS QUE LAS FACTURAS TENGAN UN CLIENTE SELECIONADO
	var radiofactura = id("radiofactura");
	if(radiofactura.selected)
	    tipocomprobante(1); 

	//ADD ITEM DEFAULT
	generadorCargarMProducto('MProducto',resultado);

	//RESET 
	selTipoPresupuesto(0);//Presupuestos

	//Guarda Detalle para pedido
	if(modoticket != 'endmproducto')
	    salvarMPparaPedido(srt_p);//IdUsuario~CBMP~IdProducto

	//SYNCTPV***
	//es ticketSync true?
	if(ticketSync) esSyncTPV = false;//Libera syncTPV


	//IMPRIME UN TICKET MPRODUCTO
	if( Estado == 'Finalizado' )//Imprime Comprobante
	    location.href=url;
    }

    function salvarMPparaPedido(srt){
    
	if(srt!='')
	    ArrMP.push(srt);
	//alert(ArrMP.toString());

    }

    function DesactivarImpresion(){
	id("BotonAceptarImpresion").setAttribute("disabled","true");
    }

    function HabilitarImpresion(){
	id("BotonAceptarImpresion").setAttribute("disabled","false");
    }



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
	    var l_pvpti  = id("pvpUnidadTicket");

	    //Default
	    btimpr.setAttribute("collapsed", "false");  //Boton Imprimir
	    id_modo.setAttribute("collapsed", "false");  //Boton Imprimir
	    s_bmprod.setAttribute("collapsed", "true"); //Combo MProducto
	    btguar.setAttribute("oncommand", "GuardarPreVentaTPV()"); //Boton Guardar
	    btborr.setAttribute("oncommand", "BorrarVentaTPV()"); //Boton Guardar
	    r_pedid.setAttribute("collapsed", "false");//Oculta Pedidos
	    r_cesio.setAttribute("collapsed", "false");//Oculta Cesion
	    s_bmprod.setAttribute("label","Elije MProducto...."); //Meta Producto
	    l_pvpti.setAttribute("label","PV/U"); 
	    //MetaProducto
	    if( modo == "mproducto" ){
		btimpr.setAttribute("collapsed", "true");  //Boton Imprimir
		s_bmprod.setAttribute("collapsed", "false"); //Combo MProducto
		btguar.setAttribute("oncommand", "GuardarMProductoTPV()"); //Boton Guardar
		btborr.setAttribute("oncommand", "BorrarMProductoTPV()"); //Boton Borrar
		r_pedid.setAttribute("collapsed", "true");//Oculta Pedidos
		r_cesio.setAttribute("collapsed", "true");//Oculta Cesion
		l_pvpti.setAttribute("label","Costo/U"); 

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
	    id("tic_pedidodet_"+xcod).value = xpedidodet;
	    id("tic_pedidodet_"+xcod).setAttribute('value',xpedidodet);

	    ticket[xcod].pedidodet          = xpedidodet;

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

	    id("tic_unid_" + xcod).value = xselunidad;
	    ticket[xcod].unidades        = xselunidad;

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
         var c_mproducto    = c_gpos+"TPV MPRODUCTO ";
         var c_preventa     = c_gpos+"TPV PREVENTA "
         var c_pedido       = c_gpos+"TPV PEDIDO "

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
             id(name).style.backgroundColor='white';
             id(name).style.color='black';
	     
             if (tipo=="listbox"){
		 id(name).style.cssText = " -moz-binding: url(\"chrome://global/content/bindings/listbox.xml#listitem\");";	
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
             id(name).style.backgroundColor='yellow';
             id(name).style.color='black';
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
	      xthis.setAttribute("label",xnombre);
	      
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

	      if( ares[1] == 1 ) id("ticketModificarPrecio").removeAttribute("disabled");
	      if( ares[1] == 0 ) id("ticketModificarPrecio").setAttribute("disabled",true);

	      if( ares[2] == 1 ) id("VerCajaButton").removeAttribute("disabled");
	      if( ares[2] == 0 ) id("VerCajaButton").setAttribute("disabled",true);

	      id("ticketModificarDescuento").setAttribute("oncommand",
							  "ModificarDescuento("+ares[1]+")");
	      //cambio usuario: mantiene usuario
	      xdato = ( res[0] == 1 )? 1:2;  
	      actualizaUsuarioTPV(xdato,xuser);
	  }



          function actualizaUsuarioTPV(xdato,xuser){

	      switch(xdato){
	      case 1:
		  Local.nombreDependiente =  id("depLista").getAttribute("label");
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

		  id("depLista").setAttribute("label",Local.nombreDependiente);
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

              id("panelDerecho").setAttribute("collapsed","false");
              id("modoVisual").setAttribute("selectedIndex",0);
              id("fichaProducto").setAttribute("src","about:blank");

	  }


          function ToggleFichaForm() {
              var code;

              if (esFichaVisible) {
		  id("panelDerecho").setAttribute("collapsed","false");
		  code = 0;//ocultar
              } else {
		  id("panelDerecho").setAttribute("collapsed","true");
		  code = 4;
              }

              var cod = getCodigoSelectedProd();
              var fichaProducto = id("fichaProducto");
	      id("fichaProductoNombre").setAttribute("label",CargarDescripcionFichaProducto(cod));
              id("modoVisual").setAttribute("selectedIndex",code);

              var url = "simplecruzado.json.php?CodigoBarras=" + cod;
              var obj  = Meca.cargarJSON( false,url,true);
              if(obj){
		  Dom.MatarTodosHijos("fichaProducto");		
		  Meca.generaCruzadoProductos( "fichaProducto", obj );	
              }
              esFichaVisible = code;
	  }


          /*+ FICHA SECCION +*/
          var esOffLineSyncTPV = false;//Control de Reload demon syncTPV

          function ActualizacionEstadoOnline(){
              if ( peticionesSinRespuesta >= 2 )
	      {
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
		  //id("botonImprimir").removeAttribute("disabled");
		  habilitabotonimprimir();

		  if ( esOffLineSyncTPV )
		      Reload_demon_syncTPV();
              }
              //var srcconectado = "img/gpos_network_on.png";
              //id("bolaMundo").src = urlprohibido;//Con 2 fallos o mas, asumimos sin conexion.
	      //id("bolaMundoOff").setAttribute("src","");
              //if ( id("bolaMundo").src != srcconectado) 
              //id("bolaMundo").src = srcconectado;
          } 


          function esSyncBoton(xval){

	      if(xval == 'pause')
		  return setTimeout("esSyncBoton()",3000);

	      if(!xval)
		  return id("syncTPV").setAttribute("image","img/gpos_tpvsynch_pause.png");

	      var pimg = "img/gpos_tpvsynch_pause.png";
	      var rimg = "img/gpos_tpvsynch_run.png";
	      var xbtn = id("syncTPV");
	      var ximg = xbtn.getAttribute("image");

	      if( ximg == rimg ) return true;
	      
	      xbtn.setAttribute("image",rimg);
	      return false;
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
		  
		  CrearEntradaEnProductos(k.producto,k.codigobarras,k.referencia,precio,
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
		alert( c_mproducto + "\n  - Elije un MProducto, para seguir.");
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

	    //alert("gPOS: habilita presupuesto ID: "+IdPresupuesto+" Opcion "+Opcion );
	    
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
	}

        function VerVentas(){

	    id("FechaBuscaVentas").value =id("FechaBuscaVentas").value;
	    id("FechaBuscaVentasHasta").value = id("FechaBuscaVentasHasta").value;	
	    id("panelDerecho").setAttribute("collapsed","true");
	    id("modoVisual").setAttribute("selectedIndex",Vistas.ventas);	
	    setTimeout('BuscarVentas()',400);
	    id("NombreClienteBusqueda").focus();
	}

        function VerCaja(){
	    id("panelDerecho").setAttribute("collapsed","true");
	    id("modoVisual").setAttribute("selectedIndex",Vistas.caja);	
	}

        function VerListados(){
            id("panelDerecho").setAttribute("collapsed","true");
            id("modoVisual").setAttribute("selectedIndex",9);	
	}

        function esCajaCerrada(){
	    var	url = "services.php?modo=cajaescerrado";
	    var xrequest = new XMLHttpRequest();
	    xrequest.open("GET",url,false);
	    xrequest.send(null);
	    return xrequest.responseText;
	}
           
        function habilitabotonimprimir(){

	    var resultado = esCajaCerrada();
	    var nodo = id("botonImprimir");
	    
	    //Retun 
	    if( Local.IdLocalActivo != Local.IdLocalDependiente ) return;
	    
	    if(resultado == 1)
		nodo.setAttribute("disabled",true);
	    
	    if(resultado == 0)
		nodo.removeAttribute("disabled");
	}

        function VerTPV(){
 	    //habilitabotonimprimir();
 	    id("panelDerecho").setAttribute("collapsed","false");
	    id("modoVisual").setAttribute("selectedIndex",Vistas.tpv);	
	    id("NOM").focus();
	}


        function CBFocus(){
	    id("CB").focus();
	}

        function SalirNice(){
	    window.document.location.href="logout.php";
	}

        function SalirTPV(){
	    window.close();
	}

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
		    id("tituloNuevoMensaje").setAttribute('readonly',true);
		    id("EnviarMensajePrivado").setAttribute('collapsed',true);
		    id("CancelarMensajePrivado").setAttribute('collapsed',true);
 		    id("adelantoProformabox").setAttribute('collapsed',false);
		    id("filaFechaEntregaProforma").setAttribute('collapsed',false);
		    id("vigenciaProformabox").setAttribute('collapsed',false);
		    id("btnEscribirMensajes").setAttribute('label',' Obervaciones');
		    id("tituloVisualMensaje").setAttribute('label','Obervaciones:');
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
		id("EnviarMensajePrivado").setAttribute('collapsed',false);
		id("CancelarMensajePrivado").setAttribute('collapsed',false);
		id("tituloNuevoMensaje").readOnly=false;
		id("vboxserieMProducto").setAttribute('collapsed',true);
 		id("adelantoProformabox").setAttribute('collapsed',true);
		id("filaFechaEntregaProforma").setAttribute('collapsed',true);
		id("vigenciaProformabox").setAttribute('collapsed',true);
		id("btnEscribirMensajes").setAttribute('label',' Escribir mensaje');
		id("tituloVisualMensaje").setAttribute('label','Mensaje:');
		//id("serieMProducto").setAttribute('value','');
		//id("serieMProducto").removeAttribute('readonly');
		break;
	    }
	    
	}


        function AjustarEtiquetaModo(){
	    var extatus = id("rgModosTicket").value;	
	    var MODOP   = "EFECTIVO";
	    var VMODOP  = vEFECTIVO;
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


	    id("modoDePagoTicket").setAttribute("label", MODOP);//no le gusta a F15

	    id("modoDePagoTicket").value = VMODOP;

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
            var data     = "&modo=hacerIngresoAdelantoDinero&cantidad="+escape(cantidad)+"&concepto="+encodeURIComponent(concepto)+"&r=" + Math.random();
	    
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

		if (AjaxMensajes)
                    if (AjaxMensajes.status=="200")
			peticionesSinRespuesta = 0;
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

            if ( resultado == "OK" ){
		if( modo != "pedidos" )
                    alert( c_gpos + po_mensajeenviado);
	    }
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
	    if( p_adelanto > 0)
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
	    if( p_vigencia > 0)
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

	    //Fecha entrega
	    if( p_enfecha != '' && m_envia )
	    {
		var ef_mensaje = '\n- Fecha Entrega: '+p_enfecha+' '+p_enhora;
	    }

	    //VALIDA ENVIO MENSAJE	
	    if( m_envia == 1 )
	    {
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
		id("cuerpoNuevoMensaje").value='';
		id("serieMProducto").value='';
 		id("adelantoProforma").value='';
		id("lugarEntregaProforma").value='';
		//id("fechaEntregaProforma").value='0000-00-00';
		//id("horaEntregaProforma").value='00:00:00';
		id("vigenciaProforma").value='';
 		//EL MENSAJE
		return all_mensaje;
	    }
	    else 
		return 0;
	}



        /*++++++++++++++++++ Local Dependiente +++++++++++++++++++++*/

        function cambiaLocalDependiente(dlocal){

	    //Cambia nombre local activo
	    //setNombreLocalActivo(dlocal);

	    //Limipa registros en carrito
	    //CancelarVenta();

	    //carga nuevo id local dependiente
	    setIdLocalDependiente(dlocal);
	    //Reinicia todo con nuevo id 
            setTimeout("location.reload()",50);	
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
 	    var pool_precio = ( modo == "mproducto")? pool.get().costo : precio;

            this.Compra( codigobarras, pool.get().nombre, pool.get().referencia, pool_precio,
			 pool.get().impuesto,unidades,pool.get().talla, pool.get().color, 
			 pool.get().descuento,0);

            //RecalculoTotal();***
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
		pool.CreaTicket(codigo);
		nuevo = 1;
	    }	

	    ticket[codigo].unidades += unidades;	

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
	    var vdetalle  = '';
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
	    var cssdetalle = ( oferta )? 'font-weight: bold;':'';
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

		if (!nuevo) id("tic_precio_"+codigo).value = precio.toFixed(2);
		if (!nuevo) id("tic_oferta_"+codigo).value = oferta;
	    }

	    //*+++ Nuevo +++++*//
	    if (nuevo) { //agnadimos	
		
		var xlistadoTicket = id("listadoTicket");				
		//var vprecio        = ( modo == "mproducto")? precio:ticket[codigo].precio;
		var vprecio        = precio;
		var xcod           = document.createElement("label");

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
		xprecio.setAttribute("value",formatDinero(vprecio));	

		var ximporte  = document.createElement("label");
		ximporte.setAttribute("value",formatDinero(vprecio* ticket[codigo].unidades));	

		var xtalla    = document.createElement("label");
		xtalla.setAttribute("value",talla);	
		xtalla.setAttribute("id","tic_talla_"+codigo);
		xtalla.setAttribute("collapsed","true");		
		xtalla.setAttribute("style","width:300px");
		
		var xcolor     = document.createElement("label");
		xcolor.setAttribute("value",color);		
		xcolor.setAttribute("id","tic_color_"+codigo);		
		xcolor.setAttribute("collapsed","true");		

		var ximpuesto  = document.createElement("label");
		ximpuesto.setAttribute("value",impuesto);		

		var xdescuento = document.createElement("label");
		xdescuento.setAttribute("value",FormateComoDescuento(descuento));		

		var xdetalle   = document.createElement("label");
		xdetalle.setAttribute("value",vdetalle);	
		xdetalle.setAttribute("id","tic_detalle_"+codigo);
		xdetalle.setAttribute("style",cssdetalle);

		var xpedidodet = document.createElement("label");
		xpedidodet.setAttribute("value",'');		
		xpedidodet.setAttribute("id","tic_pedidodet_"+codigo);		
		xpedidodet.setAttribute("collapsed","true");		

		var xstatus = document.createElement("label");
		xstatus.setAttribute("value",cStatus);		
		xstatus.setAttribute("id","tic_status_"+codigo);		
		xstatus.setAttribute("collapsed","true");		

		var xoferta = document.createElement("label");
		xoferta.setAttribute("value",oferta);		
		xoferta.setAttribute("id","tic_oferta_"+codigo);		
		xoferta.setAttribute("collapsed","true");		

		var xcosto = document.createElement("label");
		xcosto.setAttribute("value",ticket[codigo].costo);		
		xcosto.setAttribute("id","tic_costo_"+codigo);		
		xcosto.setAttribute("collapsed","true");		

		var xconcepto = document.createElement("label");
		xconcepto.setAttribute("value",nombre2);		
		xconcepto.setAttribute("id","tic_concepto_"+codigo);		
		xconcepto.setAttribute("collapsed","true");		

		if(idsubsidiario)
		{
		    var xsubsidiario = document.createElement("label");
		    xsubsidiario.setAttribute("value",idsubsidiario);
		    xsubsidiario.setAttribute("id","tic_subsidiario_"+codigo);
		}

		var xlistitem = document.createElement("listitem");	
		xlistitem.setAttribute("id","tic_"+codigo);
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

		    id("tic_detalle_"+codigo).value = vdetalle;
		    id("tic_detalle_"+codigo).setAttribute("style",cssdetalle);
		}
	    }

	    //Pedido Detalle...
	    CargarPedidoDetFila(codigo,ticket[codigo].unidades );

	    //Redibuja el nuevo TOTAL
	    RecalculoTotal();
	    //log("Comprando "+ticket[codigo].unidades +" de "+ticket[codigo].nombre);
	}


        function CrearEntradaEnProductos(producto,codigo,referencia,precio,
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
	    

	    vdetalle  = ( mproducto )? '**MPRODUCTO** ' : vdetalle;
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
	    xdescripcion.setAttribute("value",producto);
            xdescripcion.setAttribute("id","descripcion_"+codigo);

            var xexistencias = document.createElement("label");
	    xexistencias.setAttribute("value",unidades+' '+unid );
            xexistencias.style.textAlign ="right";
            xexistencias.setAttribute("id","stock_"+codigo);

            var xprecio      = document.createElement("label");
	    xprecio.setAttribute("value",formatDinero(vprecio));	
            xprecio.style.align     ="right";
            xprecio.style.textAlign ="right";
            xprecio.setAttribute("id","precio_"+codigo);

            var xdetalle     = document.createElement("label");
	    xdetalle.setAttribute("value",vdetalle);	
            xdetalle.setAttribute("id","detalle_"+codigo);
	    xdetalle.setAttribute("style",cssdetalle);

            var xlistitem    = document.createElement("listitem");
            xlistitem.setAttribute("id","prod_"+codigo);

            xlistitem.appendChild( xref);
            xlistitem.appendChild( xdescripcion);
            xlistitem.appendChild( xdetalle);
            xlistitem.appendChild( xexistencias);
            xlistitem.appendChild( xprecio );
            xlistadoProductos.appendChild( xlistitem );

            prodlist_tag[iprod] = xlistitem;
            prodlist[iprod++]   = codigo;	 	
	}


        function ModificarEntradaEnProductos(producto,codigo,referencia,precio,
  					     impuesto,unidades,costo,lote,vence,serie,
					     menudeo,unidxcont,unid,cont,servicio,ilimitado,
					     oferta,ofertaunid,pvo,condventa,
					     mproducto){

	    var modo         = id("rgModosTicket").value;//Mproducto
	    var vprecio      = ( modo == "mproducto")? costo:precio;
            var xref         = id("ref_"+codigo);
            var xdescripcion = id("descripcion_"+codigo);
            var xexistencias = id("stock_"+codigo);
            var xprecio      = id("precio_"+codigo);
            var xdetalle     = id("detalle_"+codigo);

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

	    vdetalle  = ( mproducto )? '**MPRODUCTO** ' : vdetalle;
	    vdetalle  = ( oferta    )? '**OFERTA '+ofertaunid+''+unid+' c/u '+formatDinero(pvo)+'** '+vdetalle : vdetalle;
	    vdetalle  = ( menudeo   )? vdetalle+xmenudeo      : vdetalle;
	    vdetalle  = ( ilimitado )? vdetalle+'**STOCK ILIMITADO** ' : vdetalle;
	    vdetalle  = ( serie     )? vdetalle+'NS. '+xserie[1].slice(0,30)+' ' : vdetalle;
	    vdetalle  = ( vence     )? vdetalle+'FV. '+xvence[1]+' ' : vdetalle;
	    vdetalle  = ( lote      )? vdetalle+'LT. '+xlote[1]+' '  : vdetalle;
	    vdetalle  = ( servicio  )? '**SERVICIO**' : vdetalle;
	    vdetalle  = ( condventa )? vdetalle+' '+condventa : vdetalle;

	    xref.setAttribute("value",referencia);
	    xdescripcion.setAttribute("value",producto);
	    xexistencias.setAttribute("value",unidades+' '+unid );
	    xprecio.setAttribute("value",formatDinero(vprecio));	
	    xdetalle.setAttribute("style",cssdetalle);	
	    xdetalle.setAttribute("value",vdetalle);
	}



        /*+++++++++++++++ SYNC ++++++++++++++++++++*/
        function syncCheckConnection() {

	    var z, AjaxDemon = null;

            try {
                AjaxDemon = new XMLHttpRequest();
		return false;

            } catch(z) {
		return true;
            }
	}

        function syncClientes(){

 	    //Check Conecction
	    if(syncCheckConnection()) return;

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
	    } catch(z){
		return;
	    }

	    esSyncTPV = false;//Desbloq syncTPVtrue;
            xjsOut    = AjaxDemon.responseText;

	    if(!xjsOut) return;//OK, detiene el proceso.

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }		
	}

        function getClientesTPV(){

	    esSyncBoton('on');//Boton Sync

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

	    esSyncTPV = false;//Desbloq syncTPVtrue;
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
	    if(syncCheckConnection()) return;
	    
	    //Trae Cambios en Almacen, si hay conexion
	    if ( peticionesSinRespuesta >= 3 ) return;

	    //esSyncTPV true?
	    if(esSyncTPV) return;

	    //Bloq syncTPV*****
	    esSyncTPV    = true;

	    //Cargamos tiempo de espera sg.
	    var ts       = Math.round((new Date()).getTime() / 1000);//Current time 
	    timeSyncTPV  = parseInt(ts) -  parseInt(ctimeSyncTPV);//Diferencia de tiempo
	    ctimeSyncTPV = Math.round((new Date()).getTime() / 1000); //Current time

	    //Variables globales
	    //esSyncTPV  llave 0/1
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
	    } catch(z){
		return;
	    }

	    esSyncTPV = false;//Desbloq syncTPVtrue;
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

        function getProductosTPV(){

	    esSyncBoton('on');//Boton Sync

 	    //Check Conecction
	    var xres,url,prod,xjsOut,z;
	    xProgress('false');
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

	    esSyncTPV = false;//Desbloq syncTPVtrue;
            xjsOut    = AjaxDemon.responseText;

	    xres      = parseInt(xjsOut);

	    if(xres == 1) return;//OK, detiene el proceso.

            try {	
		if (xjsOut) {
		    counttAL(xjsOut);
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }
	    setTimeout(function(){xProgress(true);},2000);//
            esSyncBoton('pause');//Boton Sync Termina
	}

        function syncPromociones(){

	    var xres,prod,xjsOut;
	    var z   = null;	    
	    var url = "modulos/promociones/modpromociones.php?modo=syncPromociones&xlocal="+Local.IdLocalActivo;

	    AjaxDemon.open("POST",url,false);
	    AjaxDemon.setRequestHeader('Content-Type',
				       'application/x-www-form-urlencoded; charset=UTF-8');
	    try {
		AjaxDemon.send(null);
	    } catch(z){
		return;
	    }

	    esSyncTPV = false;//Desbloq syncTPVtrue;
            xjsOut    = AjaxDemon.responseText;
	    xres      = parseInt(xjsOut);

	    if(xres == 1) return;//OK, detiene el proceso.

	    promocioneslist = new Array();

            try {	
		if (xjsOut) {
                    eval(xjsOut);//“eval es el mal"
		}	
            } catch(e){	
		return;
            }	
	}

        function syncProductosPostTicket(){

 	    //Check Conecction
	    //if(syncCheckConnection()) return;

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
	    } catch(z){
		return;
	    }
	    //alert(AjaxDemon.responseText);
	    esSyncTPV = false;//Desbloq syncTPVtrue;
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

        function  syncCargarPresupuesto(tp){

 	    //Check Conecction
	    if(syncCheckConnection()) return;

	    //add new item preventa o proforma
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
	    } catch(z){
		return;
	    }

	    cadena = xrequest.responseText;

	    //PREVENTA
	    if(tp=='Preventa')
	    {
		syncPreventa(cadena,tp);
		esSyncTPV = false;//Desbloq syncTPVtrue;
	    }
	    //PROFORMA
	    if(tp=='Proforma') syncProforma(cadena,tp);

	    //<menuitem label="Todos" selected="true"  />
	}

        function  syncCargarMProducto(){

 	    //Check Conecction
	    if(syncCheckConnection()) return;

	    //esSyncTPV true?
	    if(esSyncTPV) return;
	    
	    //syncTPV*****
	    esSyncTPV = true;//Bloquea syncTPV
	    
	    //METAPRODUCTO****************
	    //add new item preventa o proforma
	    var combo = id("itemsMProducto");
	    var cadena;
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
	    } catch(z){
		return;
	    }
	    cadena = xrequest.responseText;

	    //syncTPV*****
	    esSyncTPV = false;//Libera syncTPV

	    syncMProducto(cadena);
	    //<menuitem label="Todos" selected="true"  />
	}

        function syncMProducto(cadena){
	    
	    var aDelMProducto = new Array();
	    var filas;
	    
	    filas  = cadena.split(";");
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
			id("tic_unid_" + xcod).value = xunidades;
			ticket[xcod].unidades        = xunidades;

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
		  '     [ F1   ] -> Stock / Buscar \n'+
		  '     [ F2   ] -> Clientes / Buscar \n'+
		  '     [ F4   ] -> Imprimir Comprobante\n\n'+
		  '     [ F7   ] -> Proforma / Buscar \n'+
		  '     [ F8   ] -> Preventa / Buscar \n\n'+
		  '     [ F9   ] -> Ventas / Buscar \n'+
		  '     [ F10  ] -> Caja  \n\n'+
		  '     [ Shift + F12  ] -> Servicios \n\n'+
		  '     [ Insert ] -> Sincroniza TPV\n'+
		  '     [ Shift + Supr ] -> Limpia Ticket ');
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
 
        function xProgress(xval){
	    id("txt-productoprogress").setAttribute("collapsed",xval);
	    id("bar-productoprogress").setAttribute("collapsed",xval);
	}

        function counttAL( xcadena ){

	    var xtxt    = "tA(";
	    var itAL    = 0;
	    var ntAL    = 0;
	    
	    while (itAL != -1)
	    {
		var itAL = xcadena.indexOf(xtxt,itAL);
		if (itAL != -1)
		{
		    itAL++;
		    ntAL++;
		}
	    }
	    id("txt-productoprogress").label = 'Cargando '+ntAL+' productos...'   
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
       setTimeout("getProductosTPV()",100);//PRODUCTOS
       setTimeout("getClientesTPV()",3000);//CLIENTES

       //Inicia demonios
       setTimeout("Demonio_Mensajes()",5000);//MENSAJES
       setTimeout("Demonio_Productos()",8000);//PRODUCTOS
       setTimeout("Demonio_MProductos()",5000);//METAPRODUCTOS
       setTimeout("Demonio_Clientes()",10000);//CLIENTES
       setTimeout("Demonio_Preventas()",5000);//PEDIDOS
       setTimeout("Demonio_Promociones()",240000);//PROMOCIONES 240000

       setNombreLocalActivo(Local.IdLocalDependiente);

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
    var esSyncTPV         = false;// Usado para ejecutar sync TPV
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
    aU( "Cliente Contado",1,0,'',0,0,'Interno','','','','Cliente Contado','');

    // Agnadir Otros Clientes
    function aU(nombre,idcliente,debe,ruc,bono,promo,tipo,telf,email,dir,legal,obs) {
	
	if( usuarios[idcliente] )
	    return saU(nombre,idcliente,debe,ruc,bono,promo,tipo,telf,email,dir,legal,obs);

        idusuarios.push(idcliente);
        usuarios[idcliente] = new Object();
        usuarios[idcliente].nombre = nombre;
        usuarios[idcliente].ruc    = ruc;
        usuarios[idcliente].id     = idcliente;	
        usuarios[idcliente].debe   = debe;	
        usuarios[idcliente].bono   = bono;	
        usuarios[idcliente].promo  = promo;	
        usuarios[idcliente].tipo   = tipo;	
        usuarios[idcliente].telf   = telf;
        usuarios[idcliente].email  = email;
        usuarios[idcliente].dir    = dir;
        usuarios[idcliente].legal  = legal;
        usuarios[idcliente].obs    = obs;

        addXUser(nombre,idcliente,debe,ruc,bono,promo,tipo);
    }

    function saU(nombre,idcliente,debe,ruc,bono,promo,tipo,telf,email,dir,legal,obs) {

        usuarios[idcliente].nombre = nombre;
        usuarios[idcliente].ruc    = ruc;
	usuarios[idcliente].debe   = debe;
        usuarios[idcliente].bono   = bono;	
        usuarios[idcliente].promo  = promo;	
        usuarios[idcliente].tipo   = tipo;	
        usuarios[idcliente].telf   = telf;
        usuarios[idcliente].email  = email;
        usuarios[idcliente].dir    = dir;
        usuarios[idcliente].legal  = legal;
        usuarios[idcliente].obs    = obs;

        //Lista...
        if(!id("user_picker_"+idcliente))
            return addXUser(nombre,idcliente,debe,ruc,bono,promo,tipo);

        //Actualiza...
    	var xdebe = (debe>0)? cMoneda[1]['S']+" "+formatDinero(debe):"";
        id("user_picker_ruc_"+idcliente).setAttribute("label",ruc );
	id("user_picker_nombre_"+idcliente).setAttribute("label",nombre );
	id("user_picker_debe_"+idcliente).setAttribute("label",xdebe);	
    }

    function MostrarUsuariosForm() {

        id("modoVisual").setAttribute("selectedIndex",3);

        esListadoUsuariosVisible = true;
	id("buscaCliente").focus();
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
       
       id("buscaClienteSelect").value = idcliente;
       preSeleccionadoCliente = idcliente;
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
    }

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
    function addXUser(nombreUser,iduser,debe,ruc,bono,promo,tipo){

        var xroot    = id("clientPickArea");
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
        xnombre.setAttribute("label",nombreUser );	
        xnombre.setAttribute("value",iduser );
        xnombre.setAttribute("readonly","true");
        //xcell1.setAttribute("onclick","pickClient("+iduser+")");	

        xicon.setAttribute("label",tipo);	
        xicon.setAttribute("class","listitem-iconic");
        xicon.setAttribute("image","img/"+imgico);
        xicon.setAttribute("id","user_picker_tipo_"+iduser);
        xbono.setAttribute("label",txtbono);	

        xpromo.setAttribute("label",txtpromo);	

        xclient.setAttribute("id","user_picker_"+iduser);
        xclient.setAttribute("ondblclick","pickClient("+iduser+",this)");	
        xclient.setAttribute("onclick","SeleccionaCliente("+iduser+",this)");
        xclient.setAttribute("value",iduser );	

        xclient.appendChild( xicon );
        xclient.appendChild( xnf );
        xclient.appendChild( xnombre );
        xclient.appendChild( xdebe );
        xclient.appendChild( xbono );
        xclient.appendChild( xpromo );
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
			 usuarios[idcliente].promo,
			 usuarios[idcliente].tipo);
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
		    addXUser(usuarios[idcliente].nombre,
			     usuarios[idcliente].id,
			     usuarios[idcliente].debe, 
			     usuarios[idcliente].ruc, 
			     usuarios[idcliente].bono, 
			     usuarios[idcliente].promo,
			     usuarios[idcliente].tipo);


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

	if(vlDireccion=='')
	    xvalmsj += '\n       - Direccion';

	if( esNumeroFiscal )
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

	if(vlDireccion=='')
	    xvalmsj += '\n        - Direccion';

	if( esNumeroFiscal )
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
        data =  data + "Telefono1=" + getDatoCliente(modificar,"Telefono1") + cr;    
        data =  data + "NumeroFiscal=" + getDatoCliente(modificar,"NumeroFiscal") + cr;    
        data =  data + "Comentarios=" + getDatoCliente(modificar,"Comentarios") + cr;    
        data =  data + "TipoCliente=" + getDatoCliente(modificar,"TipoCliente") + cr;    
        data =  data + "Email=" + getDatoCliente(modificar,"Email");    

        var xrequest = new XMLHttpRequest();
        xrequest.open("POST",url,false);
        xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
        xrequest.send(data);
	//alert( xrequest.responseText )
        var respuesta = xrequest.responseText;//.split("=")[1];

        var idCliente = parseInt(respuesta);

        if (idCliente) {
            if(!modificar){

                aU(decodeURIComponent(nombrecliente),
		   parseInt(idCliente), 
		   0,ruc,0,0,
		   tipo,
		   getDatoCliente(modificar,"Telefono1"),
		   decodeURIComponent(getDatoCliente(modificar,"Email")),
		   decodeURIComponent(getDatoCliente(modificar,"Direccion")),
		   decodeURIComponent(nombrelegal),
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

    function getClienteId(idcliente){
	
        var url = "services.php?modo=mostrarCliente" + "&idcliente=" + escape(idcliente);
        var tex = "";
        var cr  = "\n";
	var z   = null;	    
        var node,t,i;
        var obj = new XMLHttpRequest();
        obj.open("GET",url,false);

	try {
	    obj.send(null);
	} catch(z){
	    return;
	}

        if (!obj.responseXML) return;
        if (!obj.responseXML.documentElement) return;

        var xml = obj.responseXML.documentElement;

        for (i=0; i<xml.childNodes.length; i++) {
            node = xml.childNodes[i];
            if (node){
                t = 0;
                id("visNombreComercial").value = node.getAttribute("NombreComercial");
                id("visLocalidad").value       = node.getAttribute("Localidad");
                //id("visCP").value            = node.getAttribute("CP");
                id("visEmail").value           = node.getAttribute("Email");
                id("visNumeroFiscal").value    = node.getAttribute("NumeroFiscal");
                id("visComentarios").value     = node.getAttribute("Comentarios");
                id("visPaginaWeb").value       = node.getAttribute("PaginaWeb");
                id("visDireccion").value       = node.getAttribute("Direccion");
                id("visCuentaBancaria").value  = node.getAttribute("CuentaBancaria");
                id("visTelefono1").value       = node.getAttribute("Telefono1");
                id("visTipoCliente").value     = node.getAttribute("TipoCliente");
                break;//sale del bucle, pues ya tenemos los datos
            }					
        }
    }

    function setTipoCliente(xtipo,xextra){

	var xnombre = ( xtipo == 'Particular')? 'Nombre':'Nombre Comercial';
	var eslegal = ( xtipo == 'Particular')? true:false;
	var esnif   = ( xtipo == 'Particular')? 'DNI':'RUC';
	var eslegal = ( xtipo == 'Independiente')? true:eslegal;
	var xnombre = ( xtipo == 'Independiente')? 'Nombre':xnombre;
	var xnumnif = ( xtipo == 'Particular')?  8:11; 

	id(xextra+"NumeroFiscal").setAttribute("maxlength",xnumnif);
	id(xextra+"txtNFiscal").setAttribute("label",esnif);
	id(xextra+"mtxtNombreComercial").setAttribute("label",xnombre);
	id(xextra+"mtxtNombreLegal").setAttribute("collapsed",eslegal);
    }

    function VerClienteId(){

	if( !preSeleccionadoCliente || preSeleccionadoCliente == 1)
            return id("tab-vistacliente").setAttribute("collapsed",true);

        cargarClienteId();

	id("tab-vistacliente").setAttribute("collapsed",false);
	id("tab-vistacliente").setAttribute("label", "Cliente: "+ id("visNombreComercial").value);
	id("tab-vistacliente").setAttribute("selected",true);
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

		if(!xthis.value ) return;

		for(var i=0;i<idusuarios.length;i++)
		{
                    var idcliente = idusuarios[i];
		    if( parseInt( usuarios[idcliente].ruc ) == parseInt( xthis.value ))
		    {
			if( extranif == '') LimpiarClienteForm();
			return alert( c_gpos + '\n - El cliente '+
				      usuarios[idcliente].nombre+
				      ' esta registrado.\n'+
				      ' - Busquelo por su Registro Fiscal: '+
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
	CancelarVenta(MANTENER_MODO);
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
	var xentrega   = id("peticionEntrega");
	//xentrega.value = (trim( xentrega.value ) == '')? 0:xentrega.value;

	if(!modoMultipago){
            var entrega = parseFloat(CleanMoney(xentrega.value));
	} else {
            var entrega = 0;
            entrega += parseFloat(CleanMoney(id("peticionEfectivo").value));
            entrega += parseFloat(CleanMoney(id("peticionBono").value));
            entrega += parseFloat(CleanMoney(id("peticionTarjeta").value));
	}

	pendiente = parseFloat(entrega) - parseFloat( formatDinero(Global.totalbase) );
        color     = ( parseInt(pendiente*100) >=0.01)? "green":"red";
        //pendiente = ( parseInt(pendiente*100) >=0.01)? pendiente:'0.00'; 

        id("peticionPendiente").setAttribute("label", formatDinero(pendiente));
	id("peticionPendiente").style.color = color;
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

	if( p_stock != "true" && IdPresupuesto !=0 && r_pedid != "true")
	    return alert("gPOS: \n    Active check - [x] Stock - "+
			 "del Ticket Actual, para continuar.")

	if ( ticketlist.length == 0 )
	    return alert("gPOS: Ticket Actual Vacio"+
			 "\n\n Liste por lo menos un producto.");

	if ( checkPreTicket() ) return;

	if ( UsuarioSeleccionado == 1 && ModoDeTicket == "pedidos")
	    return alert('gPOS: Cotización \n\n Selecione un cliente diferente al - '+
			 usuarios[UsuarioSeleccionado].nombre+' - ');
	
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
	id("comprobante").setAttribute("collapsed", "false");

	ActualizaPeticion();

	if(!esActivoServer) 
	    return alert('gPOS TPV :\n\n '+po_servidorocupado);

	habilitarMensajePrivado('e_pedidos');
	id("modoVisual").setAttribute("selectedIndex",6);

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
	CBFocus();
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
	var ticketSync          = false;
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
	//esSyncTPV...
	esSyncTPV  = (!esSyncTPV)? true:false;//Bloquea syncTPV

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
		"idDocumento="+idDocumento;
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

	    //Problemas...
	    if(prod[1]) return ValidaTicket(modo,'valida',prod[1]);

	    //OK....
            xres = parseInt(xres);	    
	    ValidaTicket(modo,'termina',false);
	    DesactivarImpresion();

	    if( !xres )
		return alert('gPOS: '+po_ticketnoserver+" Comprobante \n "+xrequest.responseText);
            if( modo!="pedidos" ) syncProductosPostTicket();//Sync...
	    if( modo!="pedidos" ) syncClientes();	    
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
	    
	    //Liga
	    var url=
		"modulos/fpdf/imprimir_"+textdoc+"_tpv.php?"+
		"nro"+textdoc+"="+nroDocumento+"&"+
		"totaletras="+importeletras+"&"+
		"codcliente="+codcliente+"&"+
		"nroSerie="+sreDocumento+"&"+
		"nombreusuario="+Local.nombreDependiente+"&"+
		"idcomprobante="+idcomprobante;
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
	esSyncTPV = (ticketSync)? false: esSyncTPV;//Libera syncTPV
	
	// Finaliza el proceso
        CerrarPeticion();
        habilitarControles(); 
	document.getElementById('busquedaVentas').removeAttribute('disabled');
        document.getElementById('NumeroDocumento').removeAttribute('disabled');
        CancelarVenta();
	
	//AseguramosCliente...
	var radiofactura = id("radiofactura");
	if(radiofactura.selected) tipocomprobante(1);

	//CargamosComboPreventa...
	if( modo == "pedidos" )  
	    generadorCargarPresupuesto('Proforma',xres,'id');

	//EliminarItemComboPreventa...
	if( modo == "pedidos" && IdPresupuesto !=0 )
	    generadorEliminaPresupuesto('Proforma',IdPresupuesto);

	//StatusPresupuentos...
	if( IdPresupuesto !=0 && modo != "pedidos" ) 
	    habilitarPresupuesto(0);

	//Set mensaje
	habilitarMensajePrivado();

	//LabelBotonAceptar
	btnAcepImp.setAttribute("label"," Aceptar ");
	btnAcepImp.setAttribute("image","img/gpos_imprimir.png");
	btnCancImp.setAttribute("collapsed","false");

	NuevoModo();

	//Lanzamos Impresion Comprobante
	if( comprobante == 0 && Local.Imprimir) location.href=url;//Imprime Comprobante
    }

    function CambiarModoImpresion(xvalue){
	Local.Imprimir = xvalue;
    }
 
    function ImprimirTicketOld() {    

	//IdDocuemnto    : VALUE
	//  - TICKET     : 0
	//  - BOLETA     : 1
	//  - FACTURA    : 2
	//  - DEVOLUCION : 3
	//  - ALBARAN    : 4
	//  - PEDIDOS    : 5
	var nroDocumentoElemeto = id("NumeroDocumento");
	var sreDocumentoElemeto = id("SerieNDocumento");
	var modo                = id("rgModosTicket").value;
	var btnAcepImp          = id("BotonAceptarImpresion");
	var btnCancImp          = id("BotonCancelarImpresion");
	var ticketSync          = false;
	var idDocumento         = id("idDocTPV").value;
	var nroDocumento        = 0;
	var sreDocumento        = 0;
	var noticket            = 0;
	var textdoc             = 'Ticket';
	var comprobante         = 1;

	//esSyncTPV...
	esSyncTPV  = (!esSyncTPV)? true:false;//Bloquea syncTPV

	//ValidaTicket...
	ValidaTicket(modo,'inicia',false);

 	//TipoDocumento
	switch( idDocumento )
	{
	case '0':
	    textdoc      = "Ticket";
	    comprobante  = 1; break;
	case '1':
	    textdoc = "Boleta"; break;
	case '2':
	    textdoc = "Factura"; break;
	case '4':
	    textdoc = "Albaran"; break;
	case '5':
	    textdoc = "Proforma"; break;
	default: return;
	}		
    
	switch( textdoc )
	{
	case 'Boleta':
	case 'Factura':
	case 'Albaran':
	case 'Proforma':
	    var t = validarNroDocumento();
	    if(!t) return; 
	    nroDocumento = parseInt(nroDocumentoElemeto.value,10);	
	    sreDocumento = parseInt(sreDocumentoElemeto.value,10);	
	    if( modo == "pedidos" ) habilitarMensajePrivado('e_pedidos');//Recarga NroProforma
            nroDocumentoElemeto.value = "";
	    sreDocumentoElemeto.value = "";
            noticket    = 1;
            comprobante = 0;
	    break;
	}
	
	//Cotizacion...
	if( modo == "pedidos" )
	{
	    var adelanto = parseFloat(id("adelantoProforma").value);//Adelantos...

	    if( adelanto > 0 ) ingresoAdelantoDineroCaja(adelanto,nroDocumento);//Ingresa Adelanto en Caja
	    else
		adelanto = 0;

	    var vigencia = parseInt(id("vigenciaProforma").value);//Vigencia Mensaje
	    if( vigencia < 1 || isNaN(vigencia) ) vigencia = 0;

	    var nsmprod  = validaMProductoTicket();//CB_MetaProductos...//id("serieMProducto").value;

            delMProductoToArrMP(nsmprod);//Elimina MProductos del ArrMP

	    var mensaje  = AdjuntarObservacionesPedido(modo,vigencia,nsmprod,adelanto);//Resitra...

	    if( mensaje == 0 ) mensaje='';
	}
	
	//Inicia...
	var data,firma,xres,unidades, precio, descuento,codigo;
	var xrequest    = new XMLHttpRequest();
	var url         = "";
	var prod        = Array();
	var sr          = parseInt(Math.random()*999999999999999);
        var esCopia     = (modo == 'copia')? 1:0;//Si es una copia?		
	var pticket     = t_CrearTicket(esCopia,noticket);	//Inicia validbles Ticket
	var text_ticket = pticket.text_data;
	var post_ticket = pticket.post_data;

	if (!esCopia) {
	    
            DesactivarImpresion();

	    //Ticket...
            url =
		"xcreaticket.php?modo=creaticket&"+
		"moticket="+escape(ModoDeTicket)+"&"+
		"tpv_serialrand="+sr+"&"+
		"nroDocumento="+nroDocumento+"&"+
		"sreDocumento="+sreDocumento+"&"+
		"idDocumento="+idDocumento;
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

	    //Problemas...
	    if(prod[1]) return ValidaTicket(modo,'valida',prod[1]);

	    //OK....
            xres = parseInt(xres);	    
	    ValidaTicket(modo,'termina',false);
	    DesactivarImpresion();

	    if( !xres ) alert('gPOS: '+po_ticketnoserver+" Comprobante");	
            if( modo!="pedidos" ) syncProductosPostTicket();//Sync...
	    if( modo!="pedidos" ) syncClientes();	    
	} else {
            xres = 1; 
	}		

	if (!xres ) {		
            alert('gPOS: '+po_ticketnoserver);	
	} else {		
	    
            var modo = id("rgModosTicket").value;
	    
	    //Imprimir
	    //Facturas,Boletas,Albaranes
	    if( comprobante==0 ){

		//Datos Impresion Comprobante
		var c = ( modo!="pedidos" )? "Venta":"Presupuesto";
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

		//Liga Impresion Comprobante
		var url=
		    "modulos/fpdf/imprimir_"+textdoc+"_tpv.php?"+
		    "nro"+textdoc+"="+nroDocumento+"&"+
		    "totaletras="+importeletras+"&"+
		    "codcliente="+codcliente+"&"+
		    "nroSerie="+sreDocumento+"&"+
		    "nombreusuario="+Local.nombreDependiente+"&"+
		    "idcomprobante="+idcomprobante;

	    }

	    //ticket
	    if( comprobante == 1)
                top.TicketFinal = window.open(EncapsrTextoParaImprimir(text_ticket),
					      "Consola Ticket",
					      "width=400,height=600,scrollbars=1,resizable=1",
					      "text/plain");		
	    //Status presupuesto 'confirmado'
	    //habilitarPresupuesto(0);
	    esSyncTPV = (ticketSync)? false: esSyncTPV;//Libera syncTPV

	    // Finaliza el proceso
            CerrarPeticion();
            habilitarControles(); 
	    document.getElementById('busquedaVentas').removeAttribute('disabled');
	    //VaciarDetallesVentas(); 
            document.getElementById('NumeroDocumento').removeAttribute('disabled');
            CancelarVenta();

	    //Lanzamos Impresion Comprobante
	    if( comprobante == 0 )
		location.href=url;//Imprime Comprobante
	}

	//AseguramosCliente...
	var radiofactura = id("radiofactura");
	if(radiofactura.selected) tipocomprobante(1);

	//CargamosComboPreventa...
	if( modo == "pedidos" )  generadorCargarPresupuesto('Proforma',xres,'id');

	//EliminarItemComboPreventa...
	if( modo == "pedidos" && IdPresupuesto !=0 ) generadorEliminaPresupuesto('Proforma',
										 IdPresupuesto);
	//StatusPresupuentos...
	if( IdPresupuesto !=0 && modo != "pedidos" ) habilitarPresupuesto(0);

	//Set mensaje
	habilitarMensajePrivado();

	//LabelBotonAceptar
	btnAcepImp.setAttribute("label"," Aceptar ");
	btnAcepImp.setAttribute("image","img/gpos_imprimir.png");
	btnCancImp.setAttribute("collapsed","false");

	NuevoModo();
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
		    i_m    = "\n     -  ***ERROR en kardex, pedido detalle -"+xpedidodet[0]+"-";
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
	var ticketSync = false;

	//esSyncTPV...
	if(!esSyncTPV)
	{
	    esSyncTPV  = true;//Bloquea syncTPV
	    ticketSync = true;//Bloqueado por Ticket
	}


	//Stock Check...
	if( p_stock != "true" && IdPresupuesto !=0 )
	    return alert( c_gpos + "\n  - Active check - [x] Stock - del Ticket Actual")

	//Usuario...
	//var alertuser = ( UsuarioSeleccionado == 1 )? '\n - Selecione un cliente diferente al - '+tcliente+' - ':'';

	//Modo...
	var modotpv       = (modo=="venta"|| modo=="cesion" )?1:0;
	var alertpreventa = (modotpv == '0')?'\n - Selecione el modo - VENTA ó CESION - en el TPV.':'';

	//Existencias...
	if(ticketlist.length == 0)
	    return alert( c_gpos + "\n - El listado del ticket actual esta vacio."+
			 "\n - Para Guardar la Pre-Venta liste un articulo."+alertpreventa);

	//Modo...
	if(modotpv == '0') 
	    return alert( c_preventa + alertpreventa);

	//Alert...
	//if ( UsuarioSeleccionado == 1 )
	    //return alert( c_preventa + alertuser);

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

	//ticketSync?...
	if(ticketSync) esSyncTPV = false;//Libera syncTPV

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
	var radiofactura = id("radiofactura");
	if(radiofactura.selected)
	    tipocomprobante(1); 

	//Item...
	generadorCargarPresupuesto('Preventa',resultado,'cd');

	//Status Preventa...
	if( IdPresupuesto !=0)
	    habilitarPresupuesto(2);

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
	
	//Albaran y Sesion
	if(modo!="cesion"){
	    radioalbaran.setAttribute("disabled", "true");
	    radioalbaran.setAttribute("selected", "false");
	}

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
		alert('gPOS Proformas: \n - Selecione - COTIZACION - en el TPV '+alertuser);
  		setTextDocumento('',0);
		radioproforma.setAttribute("selected", "false");
		radioticket.setAttribute("selected", "true");
		return VerTPV();
	    }
	}


	//Boleta
	if( opcion == 1 ){
            elemento.setAttribute("collapsed", "false");
	    radiofactura.setAttribute("selected", "false");
	    radioticket.setAttribute("selected", "false");
	    radioproforma.setAttribute("selected", "false");
	    radioproforma.setAttribute("disabled", "true");
	    radioboleta.setAttribute("selected", "true");
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
		alert('gPOS Albaran: \n - Selecione - CESION - en el TPV '+alertuser);
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

    function CargarDescripcionFichaProducto(cod){
	var url =
	    "services.php?&"+
	    "modo=cargarDescripcionFichaProductoTPV"+"&"+
	    "cb="+cod;
	var xrequest = new XMLHttpRequest();
	xrequest.open("GET",url,false);
	xrequest.send(null);
	return xrequest.responseText;
    }


    function AjustarEtiquetaPedido(){
	var radioboleta = id("radioboleta");
	var radiofactura = id("radiofactura");
	var radioalbaran = id("radioalbaran");
	var radioproforma = id("radioproforma");
	var radioticket = id("radioticket");
	var elemento = id("gruponb");

	//Default TEXT 
	elemento.setAttribute("collapsed", "false");

	//Valores default pedido
	radioboleta.setAttribute("disabled", "true");
	radiofactura.setAttribute("disabled", "true");
	radioalbaran.setAttribute("disabled", "true");
	radioticket.setAttribute("disabled", "true");
	radioboleta.setAttribute("selected", "false");
	radiofactura.setAttribute("selected", "false");
	radioproforma.setAttribute("disabled", "false");
	radioalbaran.setAttribute("selected", "false");
	radioticket.setAttribute("selected", "false");
	setTextDocumento('Proforma','5');
	radioproforma.setAttribute("selected", "true");
	CargarNroDocumentoVenta('5',0);
    }

    function AjustarEtiquetaDefault(){

	id("radioboleta").setAttribute("disabled", "false");
	id("radiofactura").setAttribute("disabled", "false");
	id("radioalbaran").setAttribute("disabled", "false");
	id("radioproforma").setAttribute("disabled", "false");
	id("radioticket").setAttribute("disabled", "false");
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
                unidades = id("tic_unid_" + codigo).value;	
                unidades = ConvertirSignoApropiado( unidades );

                id("tic_unid_" + codigo).value = unidades;

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
            var ticprecio  = parseFloat( id("tic_precio_"+ codigo).value );
            var cantidad   = parseFloat( id("tic_unid_" + codigo).value );

            dscto = parseMoney(dscto);
            ticdscto.setAttribute("value",FormateComoDescuento(dscto));	 
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
	    xcant  = parseInt( id("tic_unid_"+cbDispo).value );
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

	    esPedidodet = ( id("tic_pedidodet_"+xcodigo).value == '')? 1:0;
	    esUnidades  = ( id("tic_unid_"+xcodigo).value > 0       )? 0:1;
	    esQuitar    = ( esPedidodet == 0 && esUnidades == 0  )? false:true;

            if (!esQuitar) continue;
	    
            xfila.parentNode.removeChild(xfila);
            ticket[xcodigo] = null;
            ticketlist.splice(t,1);
	    iticket--;
	    mm_k  = ( esPedidodet )?  '  :::  ***RESUMEN KARDEX*** ' :'';
	    mm_s  = ( esUnidades  )?  '  :::  ***STOCK 0*** ':'';
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
	    tA(idproducto,codigo,servicio,"",referencia,precio*100,precio*100,impuesto,talla,color,
	       0,0,0,0,idsubsidiario,nombre,"","","","",0,0,marca,0,0,0,"unid","","",0,0,0,0,0,0,0,"" )

        pool.select(codigo); 	
        arreglotex = pool.get().nombre;
        referencia = pool.get().referencia;
        precio	   = pool.get().pvd;
        impuesto   = pool.get().impuesto;
        impuesto   = pool.get().impuesto;			
        descuento  = pool.get().descuento;		
        nombre2	   = pool.get().nombre2;	

        this.Compra( codigo,arreglotex,referencia,precio,impuesto,1, 
		     talla,color,descuento,idsubsidiario,nombre2);

        //RecalculoTotal();***
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
            //return;	
            id("panelDerecho").setAttribute("collapsed","true");

            var estado = 	document.getElementById("modoVisual").selectedIndex;	
            estado = (estado == 8)?0:8;

            id("modoVisual").setAttribute("selectedIndex",estado);	
	    setTimeout('ListadoSubsidiarios()',400);
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

		auxSubsidiarioHab();
		var subsid = id("tbox_subsidiario").value;
		if(cSubsidiario == subsid) return;

		var data = id("idsubsidiariohab").value;
		var subsid = id("tbox_subsidiario").value;

		id("arreglo_subsidiario_"+cIdTbjoSubsidiario).setAttribute('value',subsid);
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
	    var extra = "dialogWidth:" + "350" + "px;dialogHeight:" + "350" + "px";
	    window.showModalDialog(url,tipo,extra);

	    if(SubsidiarioPost) {
		id("tbox_subsidiario").value = SubsidiarioPost;
		id("idsubsidiariohab").value = IdSubsidiarioPost;
	    }
	}

/*+++++++++++++++++++++++++++++ SERVICIOS  ++++++++++++++++++++++++++++++++++*/


/*+++++++++++++++++++++++++++++ VENTAS  ++++++++++++++++++++++++++++++++++*/

/*+++++++++++++ REVISION VENTAS ++++++++++++++*/	

var idetallesVenta        = 0;
var idfacturaseleccionada = 0;
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

// Busqueda abanzada
var vFormaVenta    = true;
var vMoneda        = true;
var vUsuario       = true;
var vOP            = true;
var vCodigo        = true;

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

function RevisarVentaSeleccionada(){
    
    var idex = id("busquedaVentas").selectedItem;

    if(!idex) return;

    cIdComprobante        = idex.childNodes[2].attributes.getNamedItem('label').nodeValue;
    cComprobante          = idex.childNodes[3].attributes.getNamedItem('value').nodeValue;
    cSerieNroComprobante  = idex.childNodes[5].attributes.getNamedItem('label').nodeValue;
    cClienteComprobante   = idex.childNodes[6].attributes.getNamedItem('label').nodeValue;
    cIdClienteComprobante = idex.childNodes[6].attributes.getNamedItem('value').nodeValue;
    cMontoComprobante     = idex.childNodes[9].attributes.getNamedItem('label').nodeValue;
    cPendienteComprobante = idex.childNodes[10].attributes.getNamedItem('label').nodeValue;
    idfacturaseleccionada = idex.childNodes[1].attributes.getNamedItem('label').nodeValue;
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
    cIdSuscripcionVenta   = idex.childNodes[13].attributes.getNamedItem('value').nodeValue;

    menuContextualVentasRealizadas(cIdComprobante,false);

    if(RevDet == 0 || RevDet != idex.value)
        setTimeout("loadDetallesVentas("+idex.value+")",100);

    RevDet = idex.value;
}

function loadDetallesVentas(xid){
    VaciarDetallesVentas();
    BuscarDetallesVenta(xid);
} 

function RevisarDetalleVentaSeleccionada(){
    
    var idex      = id("busquedaDetallesVenta").selectedItem;

    if(!idex) return;

    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
    var esSeries = ( mseries != 'false' )? true:false;

    menuContextualVentasRealizadas(cIdComprobante,esSeries);
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

	//Caja...
	if(resultado == 1)
	    return alert("gPOS VENTAS DEVOLUCION:\n\n"+
			 "  ESTADO CAJA : CERRADO \n\n"+
			 "  Debe -Abrir Caja- para continuar.")
	//Ususario...
	//if( cClienteComprobante == 'Interno : Cliente Contado' ) 
	//    return alert("gPOS VENTAS DEVOLUCION:\n"+
	//		 "\n  COMPROBANTE : "+ cComprobante +" "+ cSerieNroComprobante +
	//		 "\n  CLIENTE            : "+ cClienteComprobante +
	//		 "\n\n  Debe tener un cliente diferente al - "+tcliente+" -");
	//Confirmar...
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
    var	url = 
	"services.php?modo=DevolverComprobanteTPV"+
	"&montocomprobante="+cMontoComprobante+
	"&pendientecomprobante="+cPendienteComprobante+
	"&dependiente="+Local.IdDependiente+
	"&concepto="+xdocumento+
	"&comprobante="+cIdComprobante;
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = xrequest.responseText;
    var ares = xres.split('~');
     
    //Productos...
    syncProductosPostTicket();
    //PreVentas...
    syncCargarPresupuesto('Preventa');  
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
    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
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
    var mseries   = idex.childNodes[9].attributes.getNamedItem('value').nodeValue;
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

var Abonar = new Object();

function VentanaAbonos(){
    
    //Valida Alabaran
    var idex = id("busquedaVentas").selectedItem;
    var num  = idex.value;
    var res  = obtenerTipoComprobante(num);
    
    if (res[0]=='Albaran'){
	alert("gPOS:\n "
	      +" - El Comprobante "+res[0]+" esta reservado."
	      +" \n  - Facture este comprobante para poder - Abonar - ")
	     return;
    }
    
    
	 //VaciarDetallesVentas();
    LimpiarFormaAbonos();
    
    var idex = id("busquedaVentas").selectedItem;
    
    if(!idex)	return;//no se selecciono nada
    
    var IdComprobante = idex.value;
	 
    if (!IdComprobante) return;//seleccion invalidad	
    
    var xpen = id("venta_pendiente_"+IdComprobante);
    var dineropendiente = xpen.getAttribute("label");
    var serie = id("venta_serie_" + IdComprobante).getAttribute("label");
    var num = id("venta_num_" + IdComprobante).getAttribute("label");
    var serienumfactura = serie+num;

    //resetea nuevo abono
    Abonar = new Object();	
    
    //fijamos la id actual
    Abonar.IdComprobante = IdComprobante;	
    Abonar.Maximo = parseFloat(dineropendiente).toFixed(2);
    
    id("abono_Debe").setAttribute("label",formatDinero(Abonar.Maximo));
    id("abono_Efectivo").setAttribute("value",formatDinero(Abonar.Maximo));
    id("abono_numTicket").setAttribute("label",serienumfactura);
    
    document.getElementById("modoVisual").setAttribute("selectedIndex",Vistas.abonar);	
}


function ActualizaPeticionAbono() {
    var cr = "\n";
    var color ="black";
    
    var entrega = 0;
    entrega += parseFloat(CleanMoney(document.getElementById("abono_Efectivo").value));	
    entrega += parseFloat(CleanMoney(document.getElementById("abono_Bono").value));
    entrega += parseFloat(CleanMoney(document.getElementById("abono_Tarjeta").value));
    var pendiente = Abonar.Maximo - entrega;
    id("abono_Pendiente").setAttribute("label", formatDinero(pendiente));
    id("abono_nuevo").setAttribute("label", formatDinero(entrega));	
}

function LimpiarFormaAbonos(){
    id("abono_Efectivo").value = "0";
    id("abono_Bono").value = "0";
    id("abono_Tarjeta").value = "0";	
    Abonar.Maximo = 0;
    ActualizaPeticionAbono();	
}


function RealizarAbono(){
    var IdComprobante = Abonar.IdComprobante;
    var abono_efectivo = CleanMoney(id("abono_Efectivo").value);
    var abono_tarjeta = CleanMoney(id("abono_Tarjeta").value);
    var abono_bono = CleanMoney(id("abono_Bono").value);	
    
    var obj = new XMLHttpRequest();
    var url = "services.php?modo=realizarAbono&IdComprobante=" + escape(IdComprobante)
        + "&pago_efectivo=" + parseFloat(abono_efectivo)
        + "&pago_bono=" + parseFloat(abono_bono)
        + "&pago_tarjeta=" + parseFloat(abono_tarjeta)	
        + "&r=" + Math.random();		
    
    obj.open("POST",url,false);
    obj.send("");	
    
    var text = obj.responseText;
    
    if (!text) return alert('gPOS: '+po_servidorocupado);
    
    
    var xpen = id("venta_pendiente_"+IdComprobante);
    var xstatus = id("venta_status_"+IdComprobante);
    
    text = parseFloat(text);		
    
    xpen.setAttribute("label",parseFloat(text).toFixed(2));//Nuevo valor pendiente
    
    if (text<0.01){
        if (xstatus)
	    xstatus.setAttribute("label","PAGADO");//Correspondiente nuevo estado	
    }
    
    LimpiarFormaAbonos();
    VolverVentas();
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
    var Referencia,Nombre,Talla,Color,Unidades,Precio,Descuento,PV,Codigo,CodBar,Descripcion,Lab,Marca,Serie,Lote,Vence,Servicio,MProducto,Menudeo,Cont,UnidxCont,Unid,Costo,IdPedidoDet,IdComprobanteDet;
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
		
                FuncionRecogerDetalles(CodBar,Nombre,Talla,Color,Unidades,Descuento,PV,
				       Codigo,Lab,Marca,Serie,Lote,Vence,
				       Referencia,Precio,Servicio,MProducto,Menudeo,Cont,
				       UnidxCont,Unid,IdComprobanteDet,numitem,IdPedidoDet,
				       Costo);
	    }
        }
    }
}

function AddLineaDetallesVenta(CodBar, Nombre,Talla, Color, unidades, Descuento,
			       PV,Codigo,Lab,Marca,serie,lote,vence,
			       Referencia,Precio,servicio,mproducto,menudeo,cont,
			       unidxcont,unid,IdComprobanteDet,numitem,IdPedidoDet,Costo){

    // cod = prodCod[Codigo-1];
    var lista = id("busquedaDetallesVenta");
    var xitem, xReferencia,xNombre,xTalla,xColor,xUnidades,xDescuento,xPV,xSerie,xLote,xVencimiento,xDetalle,xIdProducto,xIdPedidoDet,xCosto;

    var xresto    = ( menudeo == 1)? unidades%unidxcont                    : false;
    var xcant     = ( menudeo == 1)? ( unidades - xresto )/unidxcont       : false;
    var xcont     = ( menudeo == 1)? unid+' ('+unidxcont+unid+'/'+cont+')' : false;
    var xmenudeo  = ( menudeo == 1)? xcant+''+cont+'+'+xresto+''+xcont+' ' : false;


    var vdetalle  = ( mproducto == 1)? '**MPRODUCTO** '       : '';
    var vdetalle  = ( menudeo   == 1)? vdetalle+xmenudeo      : vdetalle;
    var vdetalle  = ( serie!='false')? vdetalle+'NS. '+serie.slice(0,120)+' ' : vdetalle;
    var vdetalle  = ( vence!='false')? vdetalle+'FV. '+vence + ' ' : vdetalle;
    var vdetalle  = ( lote !='false')? vdetalle+'LT. '+lote  + ' ' : vdetalle;
    var vdetalle  = ( servicio == 1 )? '**SERVICIO**' : vdetalle;
    
    xitem = document.createElement("listitem");
    xitem.value = IdComprobanteDet;
    xitem.setAttribute("id","detalleventa_" + idetallesVenta);
    idetallesVenta++;
    
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
    xitem.appendChild( xSerie );
    xitem.appendChild( xLote );
    xitem.appendChild( xVencimiento );
    xitem.appendChild( xIdProducto );
    xitem.appendChild( xIdPedidoDet );
    xitem.appendChild( xCosto );
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
    //Cambia cliente
    setIdClienteDocumento(iduser);
    //Reinica valores
    IdCompCambioCliente = 0;//reset
    esCambiodeCliente = false;//reset 
    //Regresa a ventas
    id("panelDerecho").setAttribute("collapsed","true");
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

    switch(res[0]){
    case 'Ticket':
    case 'AlbaranInt':
	return alert("gPOS VENTAS:\n\n  El Nro. de "+res[0]+" es reservado.");
	break;

    case 'Factura':
    case 'Boleta':
    case 'Albaran':

	switch(accion){
	case "Modificar_FechaEmision":
	    return t_ModificarFechaEmisionComprobante(num,res[0],accion,res[1]);
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
    
    if(res[2]==0)
	return alert("gPOS TPV Albaranes: \n\n  "+
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
    
    if ( res[0]!='Albaran'){
	alert("gPOS TPV Albaranes: \n\n   - Opción reservada para Albaranes.")
	return;
    }
    if ( res[0]=='Albaran')
	v_FacturarPorLote(num,lnum,res[1],res[2]);
}

function v_FacturarPorLote(num,lnum,Serie,idclient){
    
    var t_mm = "gPOS TPV FACTURAR POR LOTE: ";
    //Controla nro de albaranes por lote 
    if(nltPorFacturar==0){
	var p = prompt(t_mm+" \n\n "+
		       " Ingrese la cantidad de albaranes por facturar:", '');
	//Cancelar pront?
	if( p == null) return;//Brutal termina proceso!!! 

	//Inicia proceso ****
	p = parseInt(p);
	//Valida
	if( p != ""){
	    if(isNaN(p) || p=="" ||  p<=0 )
		return v_FacturarPorLote(num,lnum);
	    if(p<2){
		alert('gPOS: '+t_mm+'\n\n '+
		      ' Seleccione mas de un Albaran, para facturar por lote.');
		return v_FacturarPorLote(num,lnum);
	    }
	    //set variable global
	    nltPorFacturar = p;
	    cliPorFacturar = idclient;
	}
    }
    
    //Controla Cliente
    if(idclient!=cliPorFacturar)
	return alert('gPOS: '+t_mm+'\n\n - Selecione albaranes del cliente - '+
		     usuarios[cliPorFacturar].nombre+' -');
    
    //Controla duplicados albaran
    if(lotePorFacturar[num])
	return alert('gPOS: '+t_mm+nltPorFacturar+
		     ' albaranes \n\n   - Albaran '+lnum+'esta seleccionado.');
    
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
	mm="\n\n  * Pendiente por seleccionar "+ps+" albaran(es).";
    else 
	mm="\n\n  * Facturar los "+nltPorFacturar+" albaranes?";
    
    if( !confirm(t_mm+""+nltPorFacturar+" albaranes \n\n"+
		 "  Factuar por lote, los siguientes:\n\n"+
		 "     Albaran: "+mltPorFacturar.toString()+
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
    p = parseInt(p);

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
	+"&cidcomprobante="+cIdComprobante;
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
    
    if(accion!='Anular'){
	var p = prompt("gPOS:\n\n"+
		       "   Ingrese el - Nuevo Nro  "+TipoComprobante+" - de la Serie -"+Serie+"-\n\n", '');
    }
    else{
	var snro = id('venta_num_bol_'+num).getAttribute('label');
	var ap= snro.split('-');
	var p = trim(ap[1]);
    }
    
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

function t_ModificarFechaEmisionComprobante(num,TipoComprobante,accion,Serie){
    var xfecha = id("venta_fecha_emision_"+num).getAttribute("label");
    var afecha = xfecha.split("/");
    var p = prompt("gPOS:\n\n"+
		    "   Ingrese la - Fecha Emisión  "+TipoComprobante+" - de la Serie -"+Serie+"-\n\n", afecha[2]+"-"+afecha[1]+"-"+afecha[0]);    
    
    if(p==null) return;
    if(trim(p) ==  afecha[2]+"-"+afecha[1]+"-"+afecha[0]) return;

    if(!validaFechaTPV(trim(p)))
	return alert("gPOS: \n\n       Ingrese la fecha correctamente");
    
    var url = 
	"services.php?"
	+"modo=ModificarFechaEmicionComprobante&fecha="+p
	+"&tipocomprobante="+TipoComprobante
	+"&IdComprobante="+num
	+'&accion='+accion;
    
    var xrequest = new XMLHttpRequest();
    xrequest.open("GET",url,false);
    xrequest.send(null);

    var xres = parseInt(xrequest.responseText);
    if(xres == 1) {
	alert("gPOS:\n\n       "+accion+" el Nro. de  "
	      +TipoComprobante+"\n"+
	      "      - Acción ejecutada con éxito.\n"+
	      "      - Nueva Fecha Emisión : "+p+".");
	BuscarVentas();
    }else{
	return alert('gPOS: '+po_servidorocupado+' \n Al modificar Fecha Emisión.');
    }
    
}

var ilineabuscaventas = 0;

function AddLineaVentas(item,vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,
			nombreCliente,NumeroDocumento,TipoDocumento,IdCliente,MotivoAlba,
			IdSuscripcion,FechaEmision){
    var lista = id("busquedaVentas");
    var xitem,xnumitem,xvendedor,xserie,xnum,xfecha,xtotal,xpendiente,xestado,xtipodoc,xop,xsucripcion,xfechaemision;
    
    xitem = document.createElement("listitem");
    xitem.value = IdComprobante;
    xitem.setAttribute("id","lineabuscaventa_"+ilineabuscaventas);
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

    
    xitem.appendChild( xnumitem );
    xitem.appendChild( xserie );
    xitem.appendChild( xop );
    xitem.appendChild( xtipodoc );
    xitem.appendChild( xnum );
    xitem.appendChild( xnumdoc );
    xitem.appendChild( xnombre );	
    xitem.appendChild( xfecha );
    xitem.appendChild( xfechaemision );
    xitem.appendChild( xtotal );
    xitem.appendChild( xpendiente );	
    xitem.appendChild( xestado );
    xitem.appendChild( xvendedor );
    xitem.appendChild( xsuscripcion );

    lista.appendChild( xitem );		
}


function BuscarVentas(){
    VaciarBusquedaVentas();
    var desde  = id("FechaBuscaVentas").value;
    var hasta  = id("FechaBuscaVentasHasta").value;
    var nombre = id("NombreClienteBusqueda").value;	
    
    var modo      = (id("modoConsultaVentas").checked)?"pendientes":"todos";
    var modoserie = (id("modoConsultaVentasSerie").checked)?"cedidos":"todos";
    var modosuscripcion = (id("modoConsultaVentasSuscripcion").checked)?"suscripcion":"todos";

    var filtrocodigo   = trim(id("busquedaCodigoSerie").value);
    var filtroventa    = id("FiltroVenta").value;
    var modofactura    = (filtroventa =="factura")?"factura":"todos";
    var modoboleta     = (filtroventa =="boleta")?"boleta":"todos";
    var modoticket     = (filtroventa == "ticket" )?"ticket":"todos";
    var mododevolucion = (filtroventa =="devolucion")?"devolucion":"todos";
    var modoalbaran    = (filtroventa =="albaran")?"albaran":"todos";
    var modoalbaranint = (filtroventa =="albaranint")?"albaranint":"todos";
    var forzarid        = (filtrocodigo != '' )? filtrocodigo:false;
    
    RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,modoboleta,
		    mododevolucion,modoalbaran,modoalbaranint,modoticket,false,false,forzarid,
		    AddLineaVentas);
    
    var elemento = id("busquedaCodigoSerie").value;
    if( elemento != '' ){
	     //buscarPorCodSerie(elemento);
    }
}


function RawBuscarVentas(desde,hasta,nombre,modo,modoserie,modosuscripcion,modofactura,modoboleta,
			 mododevolucion,modoalbaran,modoalbaranint,modoticket,
			 IdComprobante,reimprimir,forzarid,FuncionProcesaLinea){

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
        + "&forzarfactura=" + IdComprobante
        + "&forzarid=" + forzarid;

    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    var tex = "";
    var cr = "\n";
    
    var vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,NumeroDocumento,TipoDocumento,IdCliente,IdSuscripcion,FechaEmision;
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
	    totalVenta      = parseFloat(totalVenta) + parseFloat(total);
	    pendiente 	    = node.childNodes[t++].firstChild.nodeValue;
	    totalVentaPendiente = parseFloat(totalVentaPendiente) + parseFloat(pendiente);
	    estado 	    = node.childNodes[t++].firstChild.nodeValue;
	    IdComprobante   = node.childNodes[t++].firstChild.nodeValue;
	    NumeroDocumento = node.childNodes[t++].firstChild.nodeValue;
	    TipoDocumento   = node.childNodes[t++].firstChild.nodeValue;
	    codventa        = serie+'-'+num;	    
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

	    FuncionProcesaLinea(item,vendedor,serie,num,fecha,total,pendiente,estado,
				IdComprobante,nombreCliente,NumeroDocumento,TipoDocumento,
				IdCliente,MotivoAlba,IdSuscripcion,FechaEmision);
	    
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
	break;
    case "OP" :
	vOP            = xchecked;
	break;
    case "Codigo":
	vCodigo        = xchecked;
	break;
    }

    if(id("vbox"+xlabel)) id("vbox"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlist"+xlabel)) id("vlist"+xlabel).setAttribute("collapsed",xchecked);
    if(id("vlistcol"+xlabel)) id("vlistcol"+xlabel).setAttribute("collapsed",xchecked);
    BuscarVentas();
}

function menuContextualVentasRealizadas(xval,xvaldet){
    
    id("VentaRealizadaAbonar").setAttribute("disabled",true);
    id("VentaRealizadaDevolver").setAttribute("disabled",true);
    id("VentaRealizadaBoletar").setAttribute("disabled",true);
    id("VentaRealizadaFacturar").setAttribute("disabled",true);
    id("VentaRealizadaFacturarLote").setAttribute("disabled",true);
    id("VentaRealizadaCambioCliente").setAttribute("disabled",true);
    id("VentaRealizadaCambioNro").setAttribute("disabled",true);
    id("VentaRealizadaAnularNro").setAttribute("disabled",true);
    id("VentaRealizadaCambioAnularNro").setAttribute("disabled",true);
    id("VentaRealizadaDetalleNS").setAttribute("disabled",true);
    id("VentaRealizadaDetalleMProducto").setAttribute("disabled",true);

    var esSuscripcionVenta = (cIdSuscripcionVenta == 0)? true:false;
    id("VentaSuscripcionImprimir").setAttribute("collapsed",esSuscripcionVenta);

    var esAbonar   =  ( id("venta_pendiente_"+xval).getAttribute("label") > 0 )? true:false
    var esDevolver = false;
    var esBoletar  = false;
    var esFacturar = false;
    var esFacturarLote = false;
    var esCambioCliente = false;
    var esCambioNro = false;
    var esSeries    = xvaldet;

    switch( cComprobante )
    {
    case 'Albaran':
	esFacturar = true;
	esFacturarLote = true;
    case 'Ticket':
	esBoletar = true;
    case 'Boleta' : 
    case 'Factura':
	esCambioNro     = ( cComprobante =='Ticket' )? false: true;
	esDevolver      = true;
	esCambioCliente = true;
	break;
    }

    //Abono
    if ( esAbonar   ) id("VentaRealizadaAbonar").removeAttribute("disabled");
    if ( esDevolver ) id("VentaRealizadaDevolver").removeAttribute("disabled");
    if ( esBoletar  ) id("VentaRealizadaBoletar").removeAttribute("disabled");
    if ( esFacturar ) id("VentaRealizadaFacturar").removeAttribute("disabled");
    if ( esFacturarLote ) id("VentaRealizadaFacturarLote").removeAttribute("disabled");
    if ( esCambioCliente ) id("VentaRealizadaCambioCliente").removeAttribute("disabled");
    if ( esCambioNro ) id("VentaRealizadaCambioNro").removeAttribute("disabled");
    if ( esCambioNro ) id("VentaRealizadaAnularNro").removeAttribute("disabled");
    if ( esCambioNro ) id("VentaRealizadaCambioAnularNro").removeAttribute("disabled");
    if ( esSeries )    id("VentaRealizadaDetalleMProducto").removeAttribute("disabled");
    if ( esSeries )    id("VentaRealizadaDetalleNS").removeAttribute("disabled");
}

/*+++++++++++++++++++++++++++++ VENTAS  ++++++++++++++++++++++++++++++++++*/



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
	case 0://EFECTIVO
	    ticket.entregaEfectivo = parseFloat(CleanMoney(id("peticionEntrega").value));
	    break;
	case 1://TARJERA
	    ticket.entregaTarjeta = parseFloat(CleanMoney(id("peticionEntrega").value));
	    //alert('entregado con tarjeta:'+ticket.entregaTarjeta);
	    break;
	case 5:///BONO
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
	var res = Raw_t_RecuperaTicket(IdComprobante,TipoVenta);
	top.TicketFinal = window.open(EncapsrTextoParaImprimir(res.text_data),
				      "Consola Ticket",
				      "width=400,height=600,scrollbars=1,resizable=1,dependent=yes","text/plain");	
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
	"nro"+TipoVenta+"="+nroDocumento+"&"+
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


/* Carga de datos del ticket */

var GlobalNewTicket = new Object();

function CargaVenta(item,vendedor,serie,num,fecha,total,pendiente,estado,IdComprobante,nombreCliente,NumeroDocumento,TipoDocumento){
	GlobalNewTicket.Dependiente = vendedor;
	GlobalNewTicket.serie 		= serie;
	GlobalNewTicket.num 		= num;
	GlobalNewTicket.fecha 		= fecha;
	GlobalNewTicket.total 		= total;
	GlobalNewTicket.pendiente 	= pendiente;
	GlobalNewTicket.estado 		= estado;
	GlobalNewTicket.IdComprobante	= IdComprobante;
	GlobalNewTicket.nombreCliente 	= nombreCliente;	
	GlobalNewTicket.DineroEntregado = 0;//TODO
	GlobalNewTicket.ModoDePago 	= 0;	//TODO
	
}

function t_BuscaGlobalesFactura(IdComprobante) {

    GlobalNewTicket = new Object();
    RawBuscarVentas("","","","","","","","","","","","",IdComprobante,true,false,CargaVenta);
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
	prod.pvd 	= PV;
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
	t_linea += cadena.substring(iline,fline) +" " + crl;			
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
        pad = len - 20;

	salida += "****** " + Local.Negocio +  " ******" + cr;
	salida += "" + Local.promoMensaje + ""+cr+cr;
	salida += "" + this.Colum( new Array( po_ticketde , this.LocalSombra.nombretienda)) + cr;
	salida += this.Colum( new Array( po_numtic , this.alfanumFactura ));	
        salida += po_ticketcliente+getSpaces(1)+nombrecliente+cr+cr;
	salida += this.TexModoTicket(ModoDeTicket);		
	salida += this.Colum( new Array(po_unid,po_precio,po_descuento,po_Total));	
	salida += this.Linea();
	salida += this.GenerarTextoProductos();
	salida += this.Linea();
	salida += this.Colum( new Array(po_TOTAL,"", formatDinero(this.TotalBase)) );
	salida += this.Colum( new Array(po_Entregado,"", formatDinero(this.getEntregado())) );
	salida += this.Colum( new Array(po_Cambio,"", formatDinero(this.genCambio())) );

	//salida += this.Linea();
        //salida += this.Colum( new Array(po_desgloseiva ,formatDinero(this.aportacionimpuestos)) );
	
	var modopago = this.modopago;
	if (modopago>0)
		salida = salida + po_mododepago + " " + modospago[modopago] + cr;
		
	var po_15diaslimite_resultante = new String( po_15diaslimite );
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace(/\\n/,cr);			
	po_15diaslimite_resultante = po_15diaslimite_resultante.replace("%d",this.LocalSombra.diasLimiteDevolucion);	
		
	salida += this.Linea();
	salida += this.Colum( new Array( po_leatendio + " ",  this.dependiente) );
	salida += this.Fecha() 		+ cr;
	salida += po_15diaslimite_resultante 	+ cr;
	salida += cr + this.LocalSombra.motd 		+ cr;
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
        if (modopago>0)
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

Ticket.prototype.Linea = function() {
	return "------------------------------"  + this.cr;
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
			return po_ticketcesionprenda + this.cr;
		case "venta":		
			return "";
	}
}

Ticket.prototype.Fecha = function (){
	return "Fecha:"+ this.cr + this.LocalGlobal.fechahoy + this.cr;
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
 	var datos = new Array(); 	
        datos["unid"] 		= parseInt(this.ProductoDato("tic_unid_"));
        datos["unidmedida"]     = productos[this.CodigoProductoSeleccionado].unid;
	datos["precio"] 	= normalFloat(CleanMoney(this.ProductoDato("tic_precio_")));
	datos["importe"] 	= normalFloat(CleanMoney(this.ProductoDato("tic_importe_")));
        datos["costo"]  	= normalFloat(CleanMoney(this.ProductoDato("tic_costo_")));
	datos["descuento"] 	= normalFloat(this.ProductoDato("tic_descuento_"));
	datos["impuesto"] 	= normalFloat(CleanInpuesto( this.ProductoDato("tic_impuesto_") )/100.0);
	datos["referencia"]     = this.ProductoDato("tic_referencia_");
	datos["talla"] 		= this.ProductoDato("tic_talla_");
	datos["color"] 		= this.ProductoDato("tic_color_");
	datos["nombre"] 	= this.ProductoDato("tic_nombre_");
	datos["concepto"] 	= this.ProductoDato("tic_concepto_");
	datos["pedidodet"] 	= this.ProductoDato("tic_pedidodet_");
	datos["idproducto"] 	= this.ProductoDato("tic_idproducto_");
	datos["idsubsidiario"]	= this.ProductoDato("tic_subsidiario_");
	datos["status"]	        = this.ProductoDato("tic_status_");
        datos["oferta"]	        = this.ProductoDato("tic_oferta_");
	datos["codigo"]		= this.CodigoProductoSeleccionado;
	this.productoSombra = datos;
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
	datos["nombre"] 	= prod.nombre;
	datos["concepto"] 	= prod.concepto;
	datos["idsubsidiario"]	= prod.idsubsidiario;
	datos["codigo"] 	= prod.codigo;
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
    var pvp            = 0;
    var total          = 0;
    nombreenticket     = ( prod.idsubsidiario>0 )? prod.concepto:prod.nombre;
    prod.concepto      = ( prod.concepto != "undefined")? prod.concepto:"";
    pvp  	       = parseFloat(prod.precio);//Impuesto incluido
    total 	       = parseFloat(pvp) * parseFloat(prod.unidades);

    if (prod.descuento>0) 
        total = parseFloat(parseFloat(total)-(parseFloat(total)*(parseFloat(prod.descuento)/100.0)));

    //Cuanto dinero del que paga el cliente es en concepto de impuestos
    //this.aportacionimpuestos  += parseFloat(total) * prod.impuesto;

    //Total del ticket
    this.TotalBase = parseFloat(this.TotalBase) + parseFloat(total);
    var salida = "";

//    if(comprobante==1){

    salida += cr + this.Colum( new Array(prod.unidades+" "+prod.unid,
					 formatDinero(pvp),
					 formatDescuento(prod.descuento),
					 formatDinero(total)));					
    //salida += "CB." + prod.codigo + " " + cr;			
    //nombreenticket = prod.codigo+' '+nombreenticket;
    nombreenticket = nombreenticket.trim();
    salida += preparaCadena(nombreenticket,35,cr);
    //salida += nombreenticket.substring(bline,tline) +" " + cr;			

    this.datos_text_productos += salida;
		
    /* Añadimos estos datos a la informacion que habria que enviar al servidor */
						
	var	data_tickets2  = "";	
	var t = this.indiceProductoMetido;	
	var	firma = "line_" + t + "_"; 
	var crd = "&";	
    
	data_tickets2 += firma + "cod=" 	  + escape(prod.codigo) + crd;
	data_tickets2 += firma + "unid=" 	  + prod.unidades + crd;
	data_tickets2 += firma + "precio=" 	  + prod.precio + crd;
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
		xtreecol.setAttribute("label","MODELO");
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

var po_numtic='Código :';var po_unid='Unid.';var po_precio='Precio';var po_costo='Costo';var po_descuento='Desc.';var po_Total='Total';var po_TOTAL='TOTAL:';var po_Entregado='Entregado:';var po_Cambio='Cambio:';var po_desgloseiva='Desglose de IGV:';var po_leatendio='Le atendió:';var po_ticketarreglointerno='Ticket arreglo interno';var po_ticketcesionprenda='Ticket cesión de prenda';var po_ticketdevolucionprenda='Ticket devolución de prenda';var po_ticketnoserver='El servidor no ha podido autorizar la impresión de este ticket. Inténtelo mas tarde';var po_txtTicketVenta='Comprobante de venta';var po_txtTicketCesion='Ticket cesión';var po_txtTicketDevolucion='Ticket devolución';var po_txtTicketPedido='Presupuestos';var po_txtTicketMProducto='Meta Productos';var po_txtTicketServicioInterno='Ticket servicio';var po_imprimircopia='Impr. copia';var po_cerrar='Cerrar';var po_servidorocupado='Servidor ocupado, inténtelo más tarde';var po_nopuedeseliminarcontado='¡No puedes eliminar el cliente contado!';var po_seguroborrarcliente='¿Quieren borrar este cliente?';var po_clienteeliminado='Cliente eliminado del sistema';var po_noseborra='No se puede borrar ese cliente';var po_nuevocreado='Nuevo cliente creado';var po_clientemodificado='Cliente modificado';var po_operacionincompleta='Operacion con cliente incompleta, inténtelo mas tarde';var po_mensajeenviado='Mensaje enviado';var po_modopago='Modo de pago:';var po_nombreclientecontado='Cliente Contado';var po_ticketcliente='Cliente:';var po_Elige='Elije...';var po_15diaslimite='No se admiten devoluciones.\\nCambios dentro de las 24 horas.';var po_cuentascopias='¿Cuántas copias del código de barras necesita imprimir?';var po_cuantasunidadesquiere='¿Cuántas unidades del producto requiere?';var po_cuantasunidades='¿Cuántas unidades?';var po_faltadefcolor='Falta definir Modelo';var po_faltadeftalla='Falta definir Detalles';var po_faltadefcb='Falta definir el CB';var po_errorrepcod='Código de barras repetido';var po_tallacolrep='Detalle o Modelo repetidos';var po_unidadescompra='Debe especificar unidades de compra';var po_modnombreprod='Debe modificar el nombre del producto';var po_especificarref='Debe especificar una referencia';var po_especifiprecioventa='Debe especificar un precio de venta';var po_especificoste='Debe especificar un coste';var po_nuevoproducto='Nuevo producto';var po_nohayproductos='No hay productos';var po_sehandadodealtacodigos='Se han dado de alta %d códigos';var po_segurocancelar='¿Esta seguro que quiere cancelar?';var po_imprimircodigos='Imprimir CB';var po_borrar='Eliminar';var po_avisoborrar='¿Desea eliminar?';var po_nombre='Nombre';var po_talla='Concentración/Detalle';var po_color='Presentación/Modelo';var po_unidades='Unid.';var po_local='Local';var po_almacen='Almacén';var po_nombrecorto='Nombre de cliente demasiado corto';var po_quierecerrar='Seguro que quiere proceder al \'CIERRE DE CAJA\'?';var po_quiereabrir='Seguro que quiere proceder a \'ABRIR CAJA\'?';var po_sugerenciarecibida='Sugerencia recibida';var po_incidenciaanotada='Incidencia anotada';var po_notaenviada='Nota enviada';var po_confirmatraslado='¿Esta seguro?';var po_destino='Destino:';var po_mododepago='Modo de pago';var po_cuantascopias='¿Cuantas copias?';var po_moviendoa='Moviendo mercancía a: ';var po_importereal='Importe real de la caja:';var po_error=po_servidorocupado;var po_pagmas=">>";var po_pagmenos="<<";

/*++++++++++++++++++++++++ CADENAS  ++++++++++++++++++++++++++++*/


/*++++++++++++++++++++++++ SUSCRIPCION ++++++++++++++++++++++++++++*/
/*++++++++++++++++++++++++ SUSCRIPCION ++++++++++++++++++++++++++++*/

/*++++++++++++++++++++++++ PANEL PRODUCTO ++++++++++++++++++++++++++++*/

    function elijePanelProducto(xmodulo){
	var xtop  = parseInt( window.screen.width)/2 - 350;
	var xleft = parseInt( window.screen.height)/2 - 180;

	cLoadModulo = xmodulo;
	id("panelElijeProducto").openPopupAtScreen(xtop, xleft, false);
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
            formularioDetalleLinea(cod,'NuevoOrdenServicio');   
	    break;
	case 'Suscripcion':
	    formularioDetalleLinea(cod,'NuevaSuscripcion');
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
		
		if( k.lote  || 
		    k.vence ||
		    k.mproducto ) continue;

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
	
	
	vdetalle  = ( mproducto )? '**MPRODUCTO** ' : vdetalle;
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
			      usuarios[idcliente].promo,
			      usuarios[idcliente].tipo);
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
				  usuarios[idcliente].promo,
				  usuarios[idcliente].tipo);


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

    function paneladdXUser(nombreUser,iduser,debe,ruc,bono,promo,tipo){

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
        xnombre.setAttribute("label",nombreUser );	
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
