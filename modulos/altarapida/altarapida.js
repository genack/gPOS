
function id(nombre) { return document.getElementById(nombre) };

var noretryTC      = new Array();
var noretryCOD     = new Array();
var cTacolorSelect = false;

function ResetRetrys(){
	noretryTC = new Array();
	noretryCOD = new Array();
}

var itacolor = 0 ;//Indice de Talla/Color
var elAltaRapida = new Array();

function xNuevaTallaColor(){

	var firma;
	var xlistadoTacolor = id("listadoTacolor");				

        var unids         = enviar["UnidadMedida"];
        var condventa     = id("CondicionVenta").value;
	var actualCOD 	  = id("CB").value;
	var listacolor 	  = id("Colores");
	var elistacolor   = id("elementosColores");
	var vcolor 	  = listacolor.value;		
	var autenticolor;
	var listacolor 	  = id("Colores");
        var idaliasuno    = enviar["IdAlias0"];
        var idaliasdos    = enviar["IdAlias1"];
        var valiasuno     = id("IdAlias0").value;
        var valiasdos     = id("IdAlias1").value;
        var vfv           = id("vFechaVencimiento").value;
        var vlt           = id("vLote").value;
        var vunidxcont    = id("UnidadesxContenedor").value;
        var idcont        = id("Contenedores").value;
        var vcont         = id("Contenedores").label
        var vcantcont     = id("Empaques").value;
        var vcantcontunid = id("Unidades").value;
        var vcosto        = id("Costo").value;
	var unidadescompra = id("Cantidad").value;
        var vpvd          = id("xPVD").value;
        var vpvdd         = id("xPVDD").value;
        var vpvc          = id("xPVC").value;
        var vpvcd         = id("xPVCD").value;
        var vcostoop      = id("xCostoOP").value;
	//Buscando color seleccionado
	var idex = 0;
	var el;
	
	autenticolor 	= listacolor.label;
	idcolor 	= listacolor.value;

	//Buscando talla seleccionada		
	var autentitalla = "";
	
	var listatallas = id("Tallas");
	var vtalla 	= listatallas.value;		
	var elistatalla = id("elementosTallas");		

	autentitalla 	= listatallas.label;
	idtalla 	= listatallas.value;
	
	//Filtros que evitan entre monsergas		
	if (autenticolor.length < 1)
		return alert('gPOS: \n\n '+ po_faltadefcolor );

	if (autentitalla.length < 1)
		return alert('gPOS: \n\n '+ po_faltadeftalla );

	if (actualCOD.length < 1)
		return alert('gPOS: \n\n '+ po_faltadefcb );
	
	if (noretryCOD[actualCOD]) 
		return alert('gPOS: \n\n '+ po_errorrepcod );		

	if (noretryTC[autentitalla] == autenticolor) 
		return alert('gPOS: \n\n '+ po_tallacolrep );		

        if(id("FechaVencimiento").checked){ 
	    var fvence  = vfv.replace(/-/g,',');
	    var f       = new Date();
	    var hoy     = f.getFullYear()+","+(f.getMonth() + 1)+","+f.getDate();
	    var compara = comparaFechas(hoy,fvence);
    
	    if(compara >= 0) 
		return alert("gPOS: \n\n           Fecha de vencimiento incorrecto");
	}
    
        if( trim(id('Descripcion').value) == '' ) 
 	    return alert('gPOS: \n\n Ingrese correctamente - Nombre -');

        if(id("Lote").checked) 
            if ( vlt == '')
		return alert('gPOS: \n\n Ingrese correctamente - Lote de Producción -');

	if (id("Costo").value<0.01)
		return alert('gPOS: \n\n '+ po_especificoste );	

	if (id("Cantidad").value<0)
		return alert('gPOS: \n\n '+ po_unidadescompra );

	//Ha pasado filtros
        changeEditHeadDatos('true');//Inicia carrito

        noretryTC[autentitalla] = autenticolor;		
	noretryCOD[actualCOD] = 1;		

	var xlistitem = document.createElement("listitem");	
        document.createElement("listitem");	
	
	var xcod   = document.createElement("label");
	xcod.setAttribute("value",actualCOD);			
		
	var xtalla = document.createElement("label");
	xtalla.setAttribute("value",autentitalla);
	xtalla.setAttribute("tooltipText",idtalla);					
		
	var xcolor = document.createElement("label");
        xcolor.setAttribute("value",autenticolor);		
        xcolor.setAttribute("tooltipText",idcolor);					

	var xcosto = document.createElement("label");
        xcosto.setAttribute("value",vcosto);		

	var xcostoop = document.createElement("label");
        xcostoop.setAttribute("value",vcostoop);		

        //Precios Venta
	var xpvd = document.createElement("label");
        xpvd.setAttribute("value",vpvd);		

	var xpvdd = document.createElement("label");
        xpvdd.setAttribute("value",vpvdd);		

	var xpvc = document.createElement("label");
        xpvc.setAttribute("value",vpvc);		

	var xpvcd = document.createElement("label");
        xpvcd.setAttribute("value",vpvcd);		

        //Alias
	var xalias = document.createElement("label");
        xalias.setAttribute("value",valiasuno+' '+valiasdos);		
        //fv
	var xfv = document.createElement("label");
        xfv.setAttribute("value",(id("FechaVencimiento").checked)?vfv:'');		
        //lt     
	var xlt = document.createElement("label");
        xlt.setAttribute("value",(id("Lote").checked)?vlt:'');		
        //Menudeo
	var xmenudeo = document.createElement("label");
        xmenudeo.setAttribute("value",
			      (id("Menudeo").checked)?vcantcont+' '+
			      vcont+'+'+vcantcontunid+''+unids:'');

	var xidcont = document.createElement("label");
        xidcont.setAttribute("value",(id("Menudeo").checked)?idcont:'1');
        xidcont.setAttribute("collapsed","true");

	var xaliasuno = document.createElement("label");
        xaliasuno.setAttribute("value",idaliasuno);
        xaliasuno.setAttribute("collapsed","true");		

	var xaliasdos = document.createElement("label");
        xaliasdos.setAttribute("value",idaliasdos);
        xaliasdos.setAttribute("collapsed","true");		

	var xunidxcont = document.createElement("label");
        xunidxcont.setAttribute("value",(id("Menudeo").checked)?vunidxcont:'0');
        xunidxcont.setAttribute("collapsed","true");

	var xcantcont = document.createElement("label");
        xcantcont.setAttribute("value",(id("Menudeo").checked)?vcantcont:'0');
        xcantcont.setAttribute("collapsed","true");

	var xcantcontunid = document.createElement("label");
        xcantcontunid.setAttribute("value",(id("Menudeo").checked)?vcantcontunid:'0');
        xcantcontunid.setAttribute("collapsed","true");

        //Unidades
        var ounidadescompra = parseFloat(vunidxcont*vcantcont )+parseFloat(vcantcontunid);
        unidadescompra = ( id("Menudeo").checked )? ounidadescompra:unidadescompra;
				
	var xunid = document.createElement("label");
        xunid.setAttribute("value",unidadescompra);	

	var xcondventa = document.createElement("label");
        xcondventa.setAttribute("value",condventa);	
        xcondventa.setAttribute("collapsed","true");

		
	firma = "tacolor_"+itacolor;itacolor++;
        xlistitem.value = firma;
	xlistitem.setAttribute("id",firma); 
	xcod.setAttribute("id",firma+ "_cod");
	xtalla.setAttribute("id",firma+ "_talla");
	xcolor.setAttribute("id",firma+ "_color");
	xcosto.setAttribute("id",firma+ "_costo");
	xcostoop.setAttribute("id",firma+ "_costoop");
	xunid.setAttribute("id",firma+ "_unid");

	xpvd.setAttribute("id",firma+ "_pvd");
	xpvdd.setAttribute("id",firma+ "_pvdd");
        xpvc.setAttribute("id",firma+ "_pvc");
        xpvcd.setAttribute("id",firma+ "_pvcd");

	xalias.setAttribute("id",firma+ "_alias");
        xfv.setAttribute("id",firma+ "_fv");
        xlt.setAttribute("id",firma+ "_lt");
        xmenudeo.setAttribute("id",firma+ "_menudeo");
        xunidxcont.setAttribute("id",firma+ "_unidxcont");
        xidcont.setAttribute("id",firma+ "_idcont");
        xcantcont.setAttribute("id",firma+ "_cantcont");
        xcantcontunid.setAttribute("id",firma+ "_cantcontunid");
	xaliasuno.setAttribute("id",firma+ "_aliasuno");
	xaliasdos.setAttribute("id",firma+ "_aliasdos");
	xcondventa.setAttribute("id",firma+ "_condventa");

	xlistitem.appendChild( xcod );
	xlistitem.appendChild( xcolor );
	xlistitem.appendChild( xtalla );	
	xlistitem.appendChild( xalias );
    	xlistitem.appendChild( xunid );
	xlistitem.appendChild( xcosto );
	xlistitem.appendChild( xcostoop );
	xlistitem.appendChild( xpvd );
	xlistitem.appendChild( xpvdd );
	xlistitem.appendChild( xpvc );
	xlistitem.appendChild( xpvcd );

        xlistitem.appendChild( xmenudeo );

        xlistitem.appendChild( xfv );
	xlistitem.appendChild( xlt );

	xlistitem.appendChild( xunidxcont );
	xlistitem.appendChild( xidcont );
	xlistitem.appendChild( xaliasuno );
	xlistitem.appendChild( xaliasdos );
	xlistitem.appendChild( xcantcont );
	xlistitem.appendChild( xcantcontunid );
	xlistitem.appendChild( xcondventa );

	xlistadoTacolor.appendChild( xlistitem );		
	id("CB").value = parseInt(actualCOD) + 1;

        //setTimeout("ordenaListaTaColor()",200);
	
	setTimeout("RegenCB()",50);
}
function RegenCB() {
		var xrequest = new XMLHttpRequest();
		var url = "../productos/selcb.php?modo=cb";
		xrequest.open("GET",url,false);
		xrequest.send(null);
		var resultado = xrequest.responseText;
		if (resultado.length > 4){
			var oldCB = id("CB");
			var newvalue =  parseInt(resultado) + 1;
			//actualiza solo si "mejora" lo actual
			if ( newvalue > parseInt(oldCB.value))
				oldCB.value = newvalue;
		}
		//id("CB").style.color = "black"; 			
}



var ven_normal = "dependent=yes,width=300,height=220,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_familiaplus = "dependent=yes,width=450,height=350,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_talla = "dependent=yes,width=300,height=260,screenX=200,screenY=300,titlebar=yes,status=0";
var ven_marca = "dependent=yes,width=300,height=360,screenX=200,screenY=300,titlebar=yes,status=0";

var ven = new Array();
ven["talla"]= ven_normal;
ven["marca"]= ven_marca;
ven["talla"]= ven_talla;
ven["tallaje"]= ven_talla;
ven["familiaplus"] = ven_familiaplus;




function popup(url,tipo) {
 if (ven[tipo])
   extra = ven[tipo];
 else 
   extra =  'dependent=yes,width=210,height=230,screenX=200,screenY=300,titlebar=yes,status=0';
   
  var nueva = window.open(url,tipo,extra);
}


//---------------------ALTA PRODUCTO--------------------------

function AltaProductoInventario(){
    aProducto = Array();
    AltaProducto();
}

function AltaProducto(){

    var firma;
    var xrequest = new XMLHttpRequest();
    var url = "../../services.php?modo=altaproducto";
    var data = "";
    
    var xlistitem = id("elementosTallas");
    var iditem;
    var t = 0;
    var el, talla, color, cb, idtalla, idcolor, probhab, cantcont,cantcontu,vfv,vlt,costo; 

    if (id("Referencia").value.length <1)
	return alert('gPOS: \n\n '+ po_especificarref );	

    var fv = (id("FechaVencimiento").checked)? 'on' : '';
    var lt = (id("Lote").checked)? 'on' : '';
    var md = (id("Menudeo").checked)? 'on' : '';
    var ns = (id("NS").checked)? 'on' : '';

    firma = "tacolor_";

    while( el = id(firma + t) ) { 
	
	data = "";
	
	talla 	     = id( firma + t + "_talla" ).value;
	idtalla      = id( firma + t + "_talla" ).getAttribute("tooltipText");
	idcolor      = id( firma + t + "_color" ).getAttribute("tooltipText")
	costo        = id( firma + t + "_costo"  ).value;	
	costoop      = id( firma + t + "_costoop"  ).value;	
	unidades     = id( firma + t + "_unid"  ).value;	
	vfv          = id( firma + t + "_fv"  ).value;	
	vlt          = id( firma + t + "_lt"  ).value;	
	color 	     = id( firma + t + "_color" ).value;
	cb 	     = id( firma + t + "_cod" ).value;	
	idcont	     = id( firma + t + "_idcont" ).value;	
	idaliasuno   = id( firma + t + "_aliasuno" ).value;	
	idaliasdos   = id( firma + t + "_aliasdos" ).value;	
	condventa    = id( firma + t + "_condventa" ).value;	
	unidxcont    = id( firma + t + "_unidxcont" ).value;	
	cantcont     = id( firma + t + "_cantcont" ).value;	
	cantcontunid = id( firma + t + "_cantcontunid"  ).value;	
	pvd          = id( firma + t + "_pvd"  ).value;
	pvdd         = id( firma + t + "_pvdd"  ).value;
	pvc          = id( firma + t + "_pvc"  ).value;
	pvcd         = id( firma + t + "_pvcd"  ).value;
        unids        = enviar["UnidadMedida"];

	data = data + "&Referencia=" + escape(id("Referencia").value);
	data = data + "&RefProv=" + escape(id("RefProv").value);
	data = data + "&Descripcion="+ encodeURIComponent(id("Descripcion").value);
	data = data + "&CosteSinIVA="+ escape(costo);	
	data = data + "&Marca="+ enviar["IdMarca"];	
	data = data + "&ProvHab="+ enviar["IdProvHab"];	
	data = data + "&LabHab="+ enviar["IdLabHab"];
	data = data + "&NumeroSerie="+ ns;
	data = data + "&Lote="+ lt;
	data = data + "&FechaVencimiento="+ fv;
	data = data + "&VentaMenudeo="+ md;
	data = data + "&UnidadesPorContenedor="+ unidxcont;
	data = data + "&IdProductoAlias0="+ idaliasuno;
	data = data + "&IdProductoAlias1="+ idaliasdos;
	data = data + "&CondicionVenta="+ condventa;
	data = data + "&IdContenedor="+ idcont;
	data = data + "&IdFamilia="+ enviar["IdFamilia"];
	data = data + "&IdSubFamilia="+ enviar["IdSubFamilia"];
	data = data + "&IdTalla="+ idtalla;
	data = data + "&IdColor="+ idcolor;
	data = data + "&CodigoBarras="+ cb;
	data = data + "&Unidades="+ unidades;				
	data = data + "&vFV="+ vfv;				
	data = data + "&vLT="+ vlt;
	data = data + "&vPVD="+ pvd;
	data = data + "&vPVDD="+ pvdd;
	data = data + "&vPVC="+ pvc;
	data = data + "&vPVCD="+ pvcd;				
	data = data + "&vModo="+ cModo;				
	data = data + "&UnidadMedida="+ unids;
	data = data + "&CostoOP="+ escape(costoop);

	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);
	
	var res = xrequest.responseText;
	if(!parseInt(res)) 
	    alert(po_servidorocupado+'\n\n -'+res+'-');	

	//Inventario	
	if(esInventario)
	{
	    aProducto[t]                = cb;
	    aProducto[cb+'_idproducto'] = res;
	    aProducto[cb+'_cantidad']   = unidades;
	    aProducto[cb+'_costo']      = costo;
	    aProducto[cb+'_costoop']    = costoop;
	    aProducto[cb+'_pvd']        = pvd;
	    aProducto[cb+'_pvdd']       = pvdd;
	    aProducto[cb+'_pvc']        = pvc;
	    aProducto[cb+'_pvcd']       = pvcd;
	    aProducto[cb+'_lt']         = vlt;
	    aProducto[cb+'_fv']         = vfv;

	}

	t++;
    }

    if (t>0) 
    {
	var aviso = new String( po_sehandadodealtacodigos );
	
	aviso = aviso.replace("%d",t);
	VaciarTacolores();

	if(!esInventario){
	    resetAllDatos('aAltaRapida');
	    parent.xwebcoreCollapsed(false,true);
	    parent.cSolapaLista = '';
	}

	if(esInventario)
	    parent.agregaStockAltaRapida(aProducto,t,0);
    } 
    else 
	alert('gPOS: \n\n '+po_nohayproductos);	

}


//-----------------------------------------------

//---------------------MARCA--------------------------

function CogerMarca(){    popup('../productos/selmarca.php?modo=marca','marca'); }


function changeMarca( quien, txtmarca) {
	enviar["IdMarca"] = quien.value;
	id("Marca").value = txtmarca;

}

//-----------------------------------------------

//---------------------PROVEEDOR--------------------------

function CogeProvHab() {     popup('../proveedores/selproveedor.php?modo=proveedorhab','proveedorhab');  }

function changeProvHab( quien, txtprov ) {
	id("ProvHab").value = txtprov;
	enviar["IdProvHab"] = quien.value;
}	

//-----------------------------------------------

//---------------------LABORATORIO--------------------------

function CogeLabHab() {     popup('../laboratorios/sellaboratorio.php?modo=laboratoriohab','laboratoriohab');  }

function changeLabHab( quien, txtlab ) {
	id("LabHab").value = txtlab;
	enviar["IdLabHab"] = quien.value;
}	

//-----------------------------------------------

//---------------------ALIAS--------------------------

function CogeAlias(xid,txtalias) {

    var idfamilia = enviar["IdFamilia"];
    popup('../productos/selproductoalias.php?modo=alias&idfamilia='+idfamilia+'&id='+xid+'&txtalias='+txtalias,'color');  

}

function changeProductoAlias(o,label,xid) {
    var valor               = o.value;
    var esDuplicado         = false;

    enviar["IdAlias"+xid]   = valor;
    esDuplicado             = (enviar["IdAlias0"] == enviar["IdAlias1"])?true:false;
    enviar["IdAlias"+xid]   = (esDuplicado)?0:valor;
    id("IdAlias"+xid).value = (esDuplicado)?'':label;
}	

function changeNewProductoAlias(IdProductoAlias,label,xid){
    var valor               = IdProductoAlias;
    var esDuplicado         = false;

    enviar["IdAlias"+xid]   = valor;
    esDuplicado             = (enviar["IdAlias0"] == enviar["IdAlias1"])?true:false;
    enviar["IdAlias"+xid]   = (esDuplicado)?0:valor;
    id("IdAlias"+xid).value = (esDuplicado)?'':label;
}

//-----------------------------------------------

//-------------------FAMILIAS----------------------------


function changeFamYSub(idsubfamilia,idfamilia,texsubfamilia, texfamilia ){
	if (!texsubfamilia || texsubfamilia == "undefined" )
 		texsubfamilia = "";

        texsubfamilia = (trim(texsubfamilia) == '...')? "":texsubfamilia;
	var famsub = "" + texfamilia + " - " + texsubfamilia;
	id("FamSub").value = famsub;
	id("Descripcion").value = texfamilia + " " + texsubfamilia;
	enviar["IdSubFamilia"] = idsubfamilia;
	enviar["IdFamilia"] = idfamilia;
    	setTimeout("RegenColores()",50);
    	setTimeout("RegenTallajes()",50);
}

function CogeFamilia(){
    var vfamilia = enviar["IdFamilia"];
    popup('../productos/selsubfamilia.php?modo=familia&IdFamilia='+vfamilia,'familiaplus');
}

//-----------------------------------------------

//----------------Unidad-Medida------------------
function changeUnidMedida(xund){

    enviar["UnidadMedida"] = xund;

    if( xund != 'und' && id("NS").checked ) 
	id("NS").checked  = false ;
}
//-----------------------------------------------

//--------------------TALLAJES---------------------------


function changeTallaje(idtallaje, txttallaje) {
	id("Tallaje").value = txttallaje;
	enviar["IdTallaje"] = idtallaje;	
	VaciarTacolores();
	setTimeout("RegenTallajes()",50);
}

function CogeTallaje(){
    var vfamilia = enviar["IdFamilia"];    
    popup('../productos/selmodelo.php?modo=xtallaje&idfamilia='+vfamilia,'tallaje');
}

var itallas = 0;//Indice de talla llenada

function AddTallaLine(nombre, valor) {
	var xlistitem = id("elementosTallas");	
	
	var xtalla = document.createElement("menuitem");
	xtalla.setAttribute("id","talla_def_" + itallas);
			
	xtalla.setAttribute("value",valor);
	xtalla.setAttribute("label",nombre);
	
	xlistitem.appendChild( xtalla);var xlistitem = id("elementosTallas");	
	itallas ++;
}


function VaciarTallas(){
	var xlistitem = id("elementosTallas");
	var iditem;
	var t = 0;

	while( el = id("talla_def_"+ t) ) {
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	itallas = 0;

	id("Tallas").setAttribute("label","");	
}

function RevisarProductoSeleccionado(){

    var idex          = id("listadoTacolor").selectedItem;

    if(!idex) return;
      cTacolorSelect = idex.value;
}
function quitarTacolorSelect(){

    var xlistitem = id("listadoTacolor");
    var el        = id( cTacolorSelect );
    var xtalla    = id(cTacolorSelect+"_talla").value;

    xlistitem.removeChild( el ) ;	
    noretryTC[xtalla] = '';

    if(itacolor==1)
	changeEditHeadDatos('false');
    itacolor--;

    setTimeout("ordenaListaTaColor()",100);
}

function VaciarTacolores(){
	var xlistitem = id("listadoTacolor");
	var iditem;
	var t = 0;

	while( el = id("tacolor_"+ t) ) {
		if (el) {
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	itacolor = 0;

	ResetRetrys();
	
}


function RegenTallajes() {
		VaciarTallas();
		
		var mitallaje = enviar["IdTallaje"];
                var mifamilia = enviar["IdFamilia"];
		if(!mitallaje)
			mitallaje = MITALLAJEDEFECTO;
			
		var xrequest = new XMLHttpRequest();
		var url = "../productos/selcb.php?modo=tallas&IdTallaje="+mitallaje+'&IdFamilia='+mifamilia;
		xrequest.open("GET",url,false);
		xrequest.send(null);
		var res = xrequest.responseText;
	
		var lines = res.split("\n");
		var actual;
                var ln = lines.length-1;	
		for(var t=0;t<ln;t++){
			actual = lines[t];
			actual = actual.split("=");
			AddTallaLine(actual[0],actual[1]);		
		}				
}



//-----------------------------------------------


//--------------------TALLAS---------------------------


function changeTalla(idtalla, txttalla) {
    RegenTallajes();
    enviar["IdTalla"] = idtalla.value;
    id("Tallas").setAttribute("label", txttalla);  
}

function changeNewTalla(idtalla,label){
    RegenTallajes();
    enviar["IdTalla"] = idtalla;
    id("Tallas").value = idtalla;
    id("Tallas").setAttribute("label", label);  
}
function CogeTalla(){    
    var vtallaje = enviar["IdTallaje"];
    var vfamilia = enviar["IdFamilia"];
    popup('../productos/selmodelo.php?modo=talla&IdTallaje='+vtallaje+'&idfamilia='+vfamilia,'talla');      
}

//-----------------------------------------------


//--------------------COLORES---------------------------


var icolores = 0;//Indice de color llenada

function AddColorLine(nombre, valor) {
	var xlistitem = id("elementosColores");	
	
	var xcolor = document.createElement("menuitem");
	xcolor.setAttribute("id","color_def_" + icolores);
			
	xcolor.setAttribute("value",valor);
	xcolor.setAttribute("label",nombre);
	
	xlistitem.appendChild( xcolor);var xlistitem = id("elementosColores");	
	icolores++;
}

function VaciarColores(){
	var xlistitem = id("elementosColores");
	var iditem;
	var t = 0;

	while( el = id("color_def_"+ t) ) {
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	icolores = 0;

	id("Colores").setAttribute("label","");	
}

function isObject(a) {
    return (a && typeof a == 'object') || isFunction(a);
}


function changeColor(idcolor, txtcolor) {
    RegenColores();      
    enviar["IdColor"] = idcolor.value;
    id("Colores").value = (isObject(idcolor))? idcolor.value : idcolor;
    id("Colores").setAttribute("label",txtcolor);
}

function changeNewColor(idcolor,label){
    RegenColores();      
    enviar["IdColor"] = idcolor;
    id("Colores").value = idcolor ;
    id("Colores").setAttribute("label",label);

}
function CogeColor(){  
    var vfamilia = enviar["IdFamilia"];
    popup('../productos/selmodelo.php?modo=color&idfamilia='+vfamilia,'color');      
}

function RegenColores() {
		VaciarColores();
		
                var mifamilia = enviar["IdFamilia"];
		var xrequest = new XMLHttpRequest();
		var url = "../productos/selcb.php?modo=colores&IdFamilia="+mifamilia;

		xrequest.open("GET",url,false);
		xrequest.send(null);
		var res = xrequest.responseText;

		var lines = res.split("\n");
		var actual;
                var ln = lines.length-1;
		for(var t=0;t<ln;t++){
			actual = lines[t];
			actual = actual.split("=");
			AddColorLine(actual[0],actual[1]);		
		}				
}


//-----------------------------------------------
//-----------------Contenedor--------------------
var icontenedores = 0;//Indice de color llenada


function AddContenedorLine(nombre, valor) {
	var xlistitem = id("elementosContenedores");	
	
	var xcontenedor = document.createElement("menuitem");
	xcontenedor.setAttribute("id","contenedor_def_" + icontenedores);
			
	xcontenedor.setAttribute("value",valor);
	xcontenedor.setAttribute("label",nombre);
	
	xlistitem.appendChild( xcontenedor);var xlistitem = id("elementosContenedores");	
	icontenedores ++;
}

function changeContenedor(o,label){
    RegenContenedores();      
    id("Contenedores").value = o.value;
    id("Contenedores").setAttribute("label",label);
}


function CogeContenedor(){  
    VaciarContenedores();
    popup('../productos/selcontenedor.php?modo=contenedor','contenedor');      
}

function RegenContenedores() {
                VaciarContenedores();
                var mifamilia = enviar["IdFamilia"];
		var xrequest = new XMLHttpRequest();
		var url = "../productos/selcb.php?modo=contenedores";
		xrequest.open("GET",url,false);
		xrequest.send(null);
		var res = xrequest.responseText;
		var lines = res.split("\n");
		var actual;
                var ln = lines.length-1;
		for(var t=0;t<ln;t++){
			actual = lines[t];
			actual = actual.split("=");
		        AddContenedorLine(actual[0],actual[1]);		
		}				
}

function VaciarContenedores(){
	var xlistitem = id("elementosContenedores");
	var iditem;
	var t = 0;

	while( el = id("contenedor_def_"+ t) ) {
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	icontenedores = 0;

	id("Contenedores").setAttribute("label","");	
}

//-----------------------------------------------

function changeEditHeadDatos(val){

    id('lProvHab').setAttribute("collapsed",val);
    id('lLabHab').setAttribute("collapsed",val);
    id('lMarca').setAttribute("collapsed",val);
    id('lTallaje').setAttribute("collapsed",val);
    id('lFamSub').setAttribute("collapsed",val);
    id('Referencia').setAttribute("readonly",val);
    id('Descripcion').setAttribute("readonly",val);
    //id('UnidadMedida').setAttribute("disable",val);
    id('NS').setAttribute("disabled",val);
    id('Lote').setAttribute("disabled",val);
    id('FechaVencimiento').setAttribute("disabled",val);
    id('Menudeo').setAttribute("disabled",val);

}


function resetAllDatos(modo){
    var main  = parent.getWebForm();
    var aviso = 'Limpiando formulario Alta Rapida...';
    main.setAttribute("src",'modulos/compras/progress.php?modo='+modo+'&aviso='+aviso);
}

function verDatosExtra(xsw,xval){   
    
    var sval = (xval)?false:true; 
    var nval = (!xval)?false:true; 
    var vval = '';

    switch (xsw) {
    case 'fv':
	vval = (sval)?'':'FV';
	id("rowDatoFechaVencimiento").setAttribute('collapsed',sval);
	id("colFV").setAttribute('label',vval);
	id("NS").checked = false;
	break;

    case 'lt':
	vval = (sval)?'':'LT';
	id("rowDatoLote").setAttribute('collapsed',sval);
	id("colLT").setAttribute('label',vval);
	id("NS").checked = false;
	break;

    case 'ct':
	vval = (sval)?'':'Menudeo';
	id("rowDatoContenedor").setAttribute('collapsed',sval);
	id("rowCantidad").setAttribute('collapsed',nval);
	id("rowContenedor").setAttribute('collapsed',sval);
	id("colMenudeo").setAttribute('label',vval);
	id("NS").checked = false;
	break;

    case 'ns':

	if( enviar["UnidadMedida"] != 'und' ) 
	    return id("NS").checked = false ;
	nval=true;
	sval=true;
	id("FechaVencimiento").checked = false;
	id("Lote").checked = false;
	id("Menudeo").checked = false;
	id("rowDatoContenedor").setAttribute('collapsed',nval);
	id("rowCantidad").setAttribute('collapsed',false);
	id("rowContenedor").setAttribute('collapsed',nval);
	id("colMenudeo").setAttribute('label',vval);

	id("rowDatoLote").setAttribute('collapsed',nval);
	id("colLT").setAttribute('label',vval);

	id("rowDatoFechaVencimiento").setAttribute('collapsed',nval);
	id("colFV").setAttribute('label',vval);

	//enviar["manejaNS"] = sval;
	break;
    }


}

function validaDato(xo,xtext){

    switch (xtext) {

    case 'nCont':
	if(xo.value < 2) xo.value = 2;
	break;

    case 'nCant':
	if(xo.value == 0) xo.value = 1;
	break;

    case 'nUnidEmp':
	var xox = id("UnidadesxContenedor");

	if(trim(xo.value) =='') xo.value = 0;
	xo.value = ( parseFloat(xo.value) >= parseFloat(xox.value) )? xox.value-1 : xo.value;
	break;

    case 'nFV':
	//Fecha Vencimiento
        var ifh   = id("vFechaVencimiento");
	ifh.value = trim(ifh.value);

	if( ifh.value == 'AAAA-MM-DD' ||  ifh.value == '' )
	    return ifh.value ='AAAA-MM-DD';

	var xfh = ifh.value;
	var afh = xfh.split('-'); //AAAA-MM-DD
	var nfh = afh[0]+'-'+afh[1]+'-'+afh[2];
	
	if( validaFecha(nfh) ) 
	    return ifh.value = nfh;
	return ifh.value ='AAAA-MM-DD';

	break;
	}
}

function volverPresupuestos(){
    if(confirm('gPOS:\n\n ¿Esta seguro que quiere vaciar el Carrito Alta Rapida?')){
		VaciarTacolores();

	resetAllDatos('cAltaRapida');
	parent.xwebcoreCollapsed(false,true);
    }
}

var xPVD  = 0;
var xPVDD = 0;
var sPVD  = 0;
var xPVC  = 0;
var xPVCD = 0;
var sPVC  = 0;

function setCostoPreciosAltaRapida(xval,xdato){

    var MUD,MUC,IMP,COP,xDSTO,xMUD,xMUC;
    
    if(!xdato.value)	return xdato.value=0;
    var sdato   = parseFloat(xdato.value);
    var xCosto  = parseFloat(id("Costo").value);
    var xPrecio = parseFloat(id("xCostoOP").value);

    var xkey    = enviar["IdFamilia"]+"-"+enviar["IdSubFamilia"];

    if(!aSubFamilia[xkey]){
	CargarDataSubFamilias();
	if(!aSubFamilia[xkey])	return;
    }

    xDSTO       = (aSubFamilia[xkey].dsto != 0)? aSubFamilia[xkey].dsto:DSTOGR;
    xMUD        = (aSubFamilia[xkey].mud != 0)? aSubFamilia[xkey].mud:MUGR;
    xMUC        = (aSubFamilia[xkey].muc != 0)? aSubFamilia[xkey].muc:MUGR;
    xDSTO       = parseFloat(xDSTO);
    xMUD        = parseFloat(xMUD);
    xMUC        = parseFloat(xMUC);

    COP         = (COPImpuesto)? 0:xPrecio;
    MUD         = (xCosto + COP)*(xMUD/100);
    MUC         = (xCosto + COP)*(xMUC/100);

    switch (xval) {

    case 'costo':
	id("Costo").value = formatDineroTotal(xCosto);
    case 'precio':
	id("xCostoOP").value = formatDineroTotal(xPrecio);

	xPVD     = xCosto + MUD + COP;
	IMP      = (xPVD*cImpuesto/100).round(2);
	xPVD     = (COPImpuesto)? (xPVD + IMP + xPrecio):(xPVD + IMP);
	xPVD     = xPVD.round(2);
	xPVDD    = (xPVD-(MUD*xDSTO/100)).round(2);
	sPVD     = xPVD;
	sPVDD    = xPVDD;

	xPVC     = xCosto + MUC + COP;
	IMP      = (xPVC*cImpuesto/100).round(2);
	xPVC     = (COPImpuesto)? (xPVC + IMP + xPrecio):(xPVC + IMP);
	xPVC     = xPVC.round(2);
	xPVCD    = (xPVC-(MUC*xDSTO/100)).round(2);
	sPVC     = xPVC;
	sPVCD    = xPVCD;

	break;

    case 'pvd':
        var PVD  = parseFloat(id("xPVD").value);
	PVD      = (PVD <= FormatPreciosTPV(sPVD))? sPVD:PVD;
	xPVD     = PVD;
	xPVDD    = (xPVD-(MUD*xDSTO/100)).round(2);

	break;

    case 'pvdd':
	var PVDD = parseFloat(id("xPVDD").value);
	PVDD     = (PVDD < FormatPreciosTPV(sPVDD) || PVDD > xPVD)? sPVD-(MUD*xDSTO/100):PVDD;
	xPVDD    = PVDD;
	break;

    case 'pvc':
	var PVC  = parseFloat(id("xPVC").value);
	PVC      = (PVC <= FormatPreciosTPV(sPVC))? sPVC:PVC;
	xPVC     = PVC;
	xPVCD    = (xPVC-(MUC*xDSTO/100)).round(2);
	break;

    case 'pvcd':
	var PVCD = parseFloat(id("xPVCD").value);
	PVCD     = (PVCD < FormatPreciosTPV(sPVCD) || PVCD > xPVC)? sPVC-(MUC*xDSTO/100):PVCD;
	xPVCD    = PVCD;
	break;
    }
    
    id("xPVD").value          = FormatPreciosTPV(xPVD);  
    id("xPVDD").value         = FormatPreciosTPV(xPVDD);  
    id("xPVC").value          = FormatPreciosTPV(xPVC); 
    id("xPVCD").value         = FormatPreciosTPV(xPVCD);

}

function CargarDataSubFamilias(){

    var url = "../../services.php?modo=obtenerdatasubfamilia";
    var xIdFamilia,xIdSubFamilia,xMUD,xMUC,xDSTO,node,t,i;
    var obj = new XMLHttpRequest();
    obj.open("GET",url,false);
    obj.send(null);

    if (!obj.responseXML)
        return alert(po_servidorocupado);

    var xml = obj.responseXML.documentElement;
    for (i=0; i<xml.childNodes.length; i++) {
        node = xml.childNodes[i];
        if (node){
            t = 0;
            xIdFamilia 	     = node.childNodes[t++].firstChild.nodeValue;
            xIdSubFamilia    = node.childNodes[t++].firstChild.nodeValue;
	    xMUD             = node.childNodes[t++].firstChild.nodeValue;
	    xMUC             = node.childNodes[t++].firstChild.nodeValue;
	    xDSTO            = node.childNodes[t++].firstChild.nodeValue;
	    xKey             = xIdFamilia+"-"+xIdSubFamilia;
	    sSF(xKey,xMUD,xMUC,xDSTO);
	}
    }
}

function sSF(xKey,xMUD,xMUC,xDSTO){
    

  if( !aSubFamilia[xKey] ){
	var subfam      = new Object();
	subfam.mud        = xMUD; 
	subfam.muc        = xMUC; 
	subfam.dsto       = xDSTO; 

      aSubFamilia[xKey] = subfam;
  }else{
      	aSubFamilia[xKey].mud   = xMUD; 
	aSubFamilia[xKey].muc   = xMUC; 
	aSubFamilia[xKey].dsto  = xDSTO; 
  }

}

function ordenaListaTaColor(){

    var lista = id("listadoTacolor");
    n = lista.itemCount;
    if(n==0) return; 
    for (var i = 0; i < n; i++) {
	var zlistitem  = lista.getItemAtIndex(i);
	var zfirma    = zlistitem.getAttribute('id');
	var zcod     = id(zfirma+"_cod");
	var ztalla   = id(zfirma+"_talla");
	var zcolor   = id(zfirma+"_color");
	var zcosto   = id(zfirma+"_costo");
	var zcostoop = id(zfirma+"_costoop");
	var zunid    = id(zfirma+"_unid");

	var zpvd = id(zfirma+ "_pvd");
	var zpvdd = id(zfirma+ "_pvdd");
	var zpvc = id(zfirma+ "_pvc");
	var zpvcd = id(zfirma+ "_pvcd");
    
	var zalias = id(zfirma+ "_alias");
	var zfv = id(zfirma+ "_fv");
	var zlt = id(zfirma+ "_lt");
	var zmenudeo = id(zfirma+ "_menudeo");
	var zunidxcont = id(zfirma+ "_unidxcont");
	var zidcont = id(zfirma+ "_idcont");
	var zcantcont = id(zfirma+ "_cantcont");
	var zcantcontunid = id(zfirma+ "_cantcontunid");
	var zaliasuno = id(zfirma+ "_aliasuno");
	var zaliasdos = id(zfirma+ "_aliasdos");
	var zcondventa = id(zfirma+ "_condventa");
	
	zfirma = "tacolor_"+i;
	zlistitem.setAttribute("id",zfirma); 
	zcod.setAttribute("id",zfirma+ "_cod");
	ztalla.setAttribute("id",zfirma+ "_talla");
	zcolor.setAttribute("id",zfirma+ "_color");
	zcosto.setAttribute("id",zfirma+ "_costo");
	zcostoop.setAttribute("id",zfirma+ "_costoop");
	zunid.setAttribute("id",zfirma+ "_unid");

	zpvd.setAttribute("id",zfirma+ "_pvd");
	zpvdd.setAttribute("id",zfirma+ "_pvdd");
        zpvc.setAttribute("id",zfirma+ "_pvc");
        zpvcd.setAttribute("id",zfirma+ "_pvcd");

	zalias.setAttribute("id",zfirma+ "_alias");
        zfv.setAttribute("id",zfirma+ "_fv");
        zlt.setAttribute("id",zfirma+ "_lt");
        zmenudeo.setAttribute("id",zfirma+ "_menudeo");
        zunidxcont.setAttribute("id",zfirma+ "_unidxcont");
        zidcont.setAttribute("id",zfirma+ "_idcont");
        zcantcont.setAttribute("id",zfirma+ "_cantcont");
        zcantcontunid.setAttribute("id",zfirma+ "_cantcontunid");
	zaliasuno.setAttribute("id",zfirma+ "_aliasuno");
	zaliasdos.setAttribute("id",zfirma+ "_aliasdos");
	zcondventa.setAttribute("id",zfirma+ "_condventa");

    }
}