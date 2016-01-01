
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
var costoxcontenedor = 0;

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


        //treechildren
        //treeitem
        //treerow
        //treecell
        var treeChildren = document.getElementById("my_tree_children");
        var xlistitem    = document.createElement("treeitem");
        var xrow         = document.createElement("treerow");

        /* var xlistitem = document.createElement("listitem");	
        document.createElement("listitem");	 */
	
	var xcod   = document.createElement("treecell");
	xcod.setAttribute("label",actualCOD);			
		
	var xtalla = document.createElement("treecell");
	xtalla.setAttribute("label",autentitalla);
	xtalla.setAttribute("tooltipText",idtalla);					
		
	var xcolor = document.createElement("treecell");
        xcolor.setAttribute("label",autenticolor);		
        xcolor.setAttribute("tooltipText",idcolor);					

	var xcosto = document.createElement("treecell");
        xcosto.setAttribute("label",vcosto);		

	var xcostoop = document.createElement("treecell");
        xcostoop.setAttribute("label",vcostoop);		

        //Precios Venta
	var xpvd = document.createElement("treecell");
        xpvd.setAttribute("label",vpvd);		

	var xpvdd = document.createElement("treecell");
        xpvdd.setAttribute("label",vpvdd);		

	var xpvc = document.createElement("treecell");
        xpvc.setAttribute("label",vpvc);		

	var xpvcd = document.createElement("treecell");
        xpvcd.setAttribute("label",vpvcd);		

        //Alias
	var xalias = document.createElement("treecell");
        xalias.setAttribute("label",valiasuno+' '+valiasdos);		
        //fv
	var xfv = document.createElement("treecell");
        xfv.setAttribute("label",(id("FechaVencimiento").checked)?vfv:'');		
        //lt     
	var xlt = document.createElement("treecell");
        xlt.setAttribute("label",(id("Lote").checked)?vlt:'');		
        //Menudeo
	var xmenudeo = document.createElement("treecell");
        xmenudeo.setAttribute("label",
			      (id("Menudeo").checked)?vcantcont+' '+
			      vcont+'+'+vcantcontunid+''+unids:'');

	var xidcont = document.createElement("treecell");
        xidcont.setAttribute("label",(id("Menudeo").checked)?idcont:'1');
        xidcont.setAttribute("collapsed","true");

	var xaliasuno = document.createElement("treecell");
        xaliasuno.setAttribute("label",idaliasuno);
        xaliasuno.setAttribute("collapsed","true");		

	var xaliasdos = document.createElement("treecell");
        xaliasdos.setAttribute("label",idaliasdos);
        xaliasdos.setAttribute("collapsed","true");		

	var xunidxcont = document.createElement("treecell");
        xunidxcont.setAttribute("label",(id("Menudeo").checked)?vunidxcont:'0');
        xunidxcont.setAttribute("collapsed","true");

	var xcantcont = document.createElement("treecell");
        xcantcont.setAttribute("label",(id("Menudeo").checked)?vcantcont:'0');
        xcantcont.setAttribute("collapsed","true");

	var xcantcontunid = document.createElement("treecell");
        xcantcontunid.setAttribute("label",(id("Menudeo").checked)?vcantcontunid:'0');
        xcantcontunid.setAttribute("collapsed","true");

        //Unidades
        var ounidadescompra = parseFloat(vunidxcont*vcantcont )+parseFloat(vcantcontunid);
        unidadescompra = ( id("Menudeo").checked )? ounidadescompra:unidadescompra;
				
	var xunid = document.createElement("treecell");
        xunid.setAttribute("label",unidadescompra);	

	var xcondventa = document.createElement("treecell");
        xcondventa.setAttribute("label",condventa);	
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

	xrow.appendChild( xcod );
	xrow.appendChild( xcolor );
	xrow.appendChild( xtalla );	
	xrow.appendChild( xalias );
    	xrow.appendChild( xunid );
	xrow.appendChild( xcosto );
	xrow.appendChild( xcostoop );
	xrow.appendChild( xpvd );
	xrow.appendChild( xpvdd );
	xrow.appendChild( xpvc );
	xrow.appendChild( xpvcd );
        xrow.appendChild( xmenudeo );
        xrow.appendChild( xfv );
	xrow.appendChild( xlt );
	xrow.appendChild( xunidxcont );
	xrow.appendChild( xidcont );
	xrow.appendChild( xaliasuno );
	xrow.appendChild( xaliasdos );
	xrow.appendChild( xcantcont );
	xrow.appendChild( xcantcontunid );
	xrow.appendChild( xcondventa );

        xlistitem.appendChild( xrow );		
        treeChildren.appendChild( xlistitem );		
	//xlistadoTacolor.appendChild( xlistitem );		
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
    var t    = 0;
    var xArt = 0;
    var el, talla, color, cb, idtalla, idcolor, probhab, cantcont,cantcontu,vfv,vlt,costo; 

    if (id("Referencia").value.length <1)
	return alert('gPOS: \n\n '+ po_especificarref );	

    var fv = (id("FechaVencimiento").checked)? 'on' : '';
    var lt = (id("Lote").checked)? 'on' : '';
    var md = (id("Menudeo").checked)? 'on' : '';
    var ns = (id("NS").checked)? 'on' : '';
    var imm = ''; 
    var xAlmacen = ( parent.cIdLocal )? parent.cIdLocal:0;//Solo para inventario
    firma = "tacolor_";

    //Mensaje
    id("msjAltaRapida").setAttribute("label","...validando datos");
    id("msjAltaRapida").setAttribute("collapsed",false);
    id("btnAccionAltaRapida").setAttribute("collapsed",true);

    //Almacen


    while( el = id(firma + t) ) { 
	
	data = "";
	
	talla 	     = id( firma + t + "_talla" ).getAttribute("label");
	idtalla      = id( firma + t + "_talla" ).getAttribute("tooltipText");
	idcolor      = id( firma + t + "_color" ).getAttribute("tooltipText")
	costo        = id( firma + t + "_costo"  ).getAttribute("label");	
	costoop      = id( firma + t + "_costoop"  ).getAttribute("label");	
	unidades     = id( firma + t + "_unid"  ).getAttribute("label");	
	vfv          = id( firma + t + "_fv"  ).getAttribute("label");	
	vlt          = id( firma + t + "_lt"  ).getAttribute("label");	
	color 	     = id( firma + t + "_color" ).getAttribute("label");
	cb 	     = id( firma + t + "_cod" ).getAttribute("label");	
	idcont	     = id( firma + t + "_idcont" ).getAttribute("label");	
	idaliasuno   = id( firma + t + "_aliasuno" ).getAttribute("label");	
	idaliasdos   = id( firma + t + "_aliasdos" ).getAttribute("label");	
	condventa    = id( firma + t + "_condventa" ).getAttribute("label");	
	unidxcont    = id( firma + t + "_unidxcont" ).getAttribute("label");	
	cantcont     = id( firma + t + "_cantcont" ).getAttribute("label");	
	cantcontunid = id( firma + t + "_cantcontunid"  ).getAttribute("label");	
	pvd          = id( firma + t + "_pvd"  ).getAttribute("label");
	pvdd         = id( firma + t + "_pvdd"  ).getAttribute("label");
	pvc          = id( firma + t + "_pvc"  ).getAttribute("label");
	pvcd         = id( firma + t + "_pvcd"  ).getAttribute("label");
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
	data = data + "&Almacen="+ xAlmacen;
	data = data + "&CostoOP="+ escape(costoop);

	//Mensaje
	id("msjAltaRapida").setAttribute("label","...registrando "+
					 id( firma + t + "_talla" ).getAttribute("label")+' - '+
					 id( firma + t + "_color" ).getAttribute("label")+'.' );
	
	xrequest.open("POST",url,false);
	xrequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xrequest.send(data);

	var xres = xrequest.responseText.split(';');
	
	if(xres[0]!='')
	{
	    var existeClon = xrequest.responseText.split('~');
	    
	    if ( !parseInt( existeClon[0] ) ){
		if( !esInventario )
		    alert('gPOS Alta Rapida:   Modelo y detalle existente \n\n '+
			  id( firma + t + "_talla" ).getAttribute("label")+' - '+
			  id( firma + t + "_color" ).getAttribute("label")+'.\n\n'+
			  '         - Aceptar para continuar - ');
	    }
	    else
		alert(po_servidorocupado+'\n\n -'+xres[0]+'-');	
	}
	
	//Inventario	
	if(esInventario)
	    if( parseInt( xres[1] ) > 0){
		cb                             = xres[8];// nuevo cb;
		aProducto[ xArt ]              = xres[8];// cb;
		aProducto[cb+'_idproducto']    = xres[1];// id producto
		aProducto[cb+'_idarticulo']    = xres[2];// almacen - id -
		aProducto[cb+'_existencias']   = xres[3];// stock
		aProducto[cb+'_resumenkardex'] = xres[4];// kardex
		aProducto[cb+'_lt']            = xres[5];// es Lote?
		aProducto[cb+'_fv']            = xres[6];// es Vence?
		aProducto[cb+'_serie']         = xres[7];// es Serie?
		aProducto[cb+'_producto']      = xres[9];// nombre producto
		aProducto[cb+'_idalmacen']     = xAlmacen;// local
		aProducto[cb+'_cantidad']   = unidades;
		aProducto[cb+'_costo']      = costo;
		aProducto[cb+'_costoop']    = costoop;
		aProducto[cb+'_pvd']        = pvd;
		aProducto[cb+'_pvdd']       = pvdd;
		aProducto[cb+'_pvc']        = pvc;
		aProducto[cb+'_pvcd']       = pvcd;
		aProducto[cb+'_lote']       = vlt;// valor lote
		aProducto[cb+'_vence']      = vfv;// valor vencimiento
		xArt++;
	    } else
		imm =  '\n   - '+id( firma + t + "_color" ).getAttribute("label")
	               +' / '+id( firma + t + "_talla" ).getAttribute("label");
	
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

	if(esInventario){
	    if( imm != '')
		alert('gPOS Alta Rapida:   Modelos y detalles duplicados \n '+imm);

	    if( xArt > 0 ){ 
		//Mensaje
		id("msjAltaRapida").setAttribute("label","...cargando stock" );	
		parent.agregaStockAltaRapida(aProducto,xArt,0);
	    }
	        
	    else
		parent.volverStock()
	}
    } 
    else {

	//Oculta Mensaje
	alert('gPOS: \n\n '+po_nohayproductos);	
	id("msjAltaRapida").setAttribute("collapsed",true);
	id("btnAccionAltaRapida").setAttribute("collapsed",false);
    }
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
    var treeView = id("listadoTacolor").treeBoxObject.view;

    if(treeView.rowCount == 0 ) return; 

    var idex  = id("listadoTacolor");
    var xitem = idex.contentView.getItemAtIndex( idex.currentIndex );

    cTacolorSelect = xitem.value;
}
function quitarTacolorSelect(){

    if( !id( cTacolorSelect ) ) return;

    var xlistitem = id("my_tree_children");
    var el        = id( cTacolorSelect );
    var xtalla    = id( cTacolorSelect+"_talla" ).getAttribute('label');

    xlistitem.removeChild( el ); 
    
    noretryTC[xtalla] = '';
    
    if(itacolor==1)
	if(cAccion == 'alta')
	    changeEditHeadDatos('false');
    itacolor--;
    
    setTimeout("ordenaListaTaColor()",100);
}

function VaciarTacolores(){

    var xlist = id("my_tree_children");
    var el    = ""
    var t     = 0;
    while( el = id("tacolor_"+ t) ) {
	if (el) {
	    xlist.removeChild( el ) ;	
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
    id("Tallas").value = (isObject(idtalla))? idtalla.value : idtalla;
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
	//id("colFV").setAttribute('label',vval);
	id("NS").checked = false;
	id("FechaVencimiento").checked = xval;
	break;

    case 'lt':
	vval = (sval)?'':'LT';
	id("rowDatoLote").setAttribute('collapsed',sval);
	//id("colLT").setAttribute('label',vval);
	id("NS").checked = false;
	id("Lote").checked = xval;
	break;

    case 'ct':
	vval = (sval)?'':'Menudeo';
	id("rowDatoContenedor").setAttribute('collapsed',sval);
	id("rowCantidad").setAttribute('collapsed',nval);
	id("rowContenedor").setAttribute('collapsed',sval);
	//id("colMenudeo").setAttribute('label',vval);
	id("NS").checked = false;
	id("Menudeo").checked = xval;
	id("xEmpaqueProductoAlta").setAttribute("collapsed",sval);
	id("xEmpaqueProductoAlta").setAttribute("label","xEmpaque");
	break;

    case 'ns':

	if( enviar["UnidadMedida"] != 'und' ) 
	    return id("NS").checked = false ;
	nval=true;
	sval=true;
	id("FechaVencimiento").checked = false;
	id("Lote").checked = false;
	id("Menudeo").checked = false;
	id("NS").checked = xval;
	id("rowDatoContenedor").setAttribute('collapsed',nval);
	id("rowCantidad").setAttribute('collapsed',false);
	id("rowContenedor").setAttribute('collapsed',nval);
	//id("colMenudeo").setAttribute('label',vval);

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
        VaciarTacolores();
	resetAllDatos('aCompras');
	parent.xwebcoreCollapsed(false,true);
	parent.cSolapaLista = '';
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
    MUD         = ((xCosto*cCambio) + COP)*(xMUD/100);
    MUC         = ((xCosto*cCambio) + COP)*(xMUC/100);

    switch (xval) {

    case 'costo':
	id("Costo").value = xCosto;
    case 'precio':
	id("xCostoOP").value = xPrecio.toFixed(3);

	xPVD     = (xCosto*cCambio) + MUD + COP;
	IMP      = (xPVD*cImpuesto/100).round(2);
	xPVD     = (COPImpuesto)? (xPVD + IMP + xPrecio):(xPVD + IMP);
	xPVD     = xPVD.round(2);
	xPVDD    = (xPVD-(MUD*xDSTO/100)).round(2);
	sPVD     = xPVD;
	sPVDD    = xPVDD;

	xPVC     = (xCosto*cCambio) + MUC + COP;
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

    var treeView = id("listadoTacolor").treeBoxObject.view;
    var n        = treeView.rowCount;

    if(n==0) return; 
    for (var i = 0; i < n; i++) {
	var zlistitem  = treeView.getItemAtIndex(i);
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
        zlistitem.value = zfirma;
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

function mostrarCostoTotalAlta(){

    var p = prompt("gPOS:\n\n Costo Total", costoxcontenedor);
    if(!p) return;

    if(isNaN(p)||p==""||p.lastIndexOf(' ')>-1||parseFloat(p)<0)
    {
	alert("gPOS: \n\n Ingrese correctamente el valor del campo");
	return mostrarCostoTotalAlta();
    }

    costoxcontenedor = p;
    var UnidxCont = id("UnidadesxContenedor").value;
    var costo = document.getElementById("Costo");
    var nuevocosto = p/parseFloat(UnidxCont);

    //costo.value = nuevocosto.toFixed(3);
    costo.value = Math.round( nuevocosto*100000 )/100000;
    setCostoPreciosAltaRapida('costo',costo);
}
