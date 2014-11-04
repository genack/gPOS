<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-xulframe");

function OpcionesBusqueda($retorno) {
	global $action;
	
	
	$ot = getTemplate("xulBusquedaAvanzada");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	$idprov = getSesionDato("FiltraProv");
	$idmarca = getSesionDato("FiltraMarca");
	$idcolor = getSesionDato("FiltraColor");
	$idtalla = getSesionDato("FiltraTalla");
	$idfamilia = getSesionDato("FiltraFamilia");
		
	$ot->fijar("action",$action);
	$ot->fijar("pagRetorno",$retorno);
	$ot->fijar("comboProveedores",genXulComboProveedores($idprov));
	$ot->fijar("comboMarcas",genXulComboMarcas($idmarca));
	$ot->fijar("comboFamilias",genXulComboMarcas($idfamilia));
		
		//echo q($idcolor,"color a mostrar en template");
		//echo q(intval($idcolor),"intval color a mostrar en template");
			
	if (intval($idcolor) >=0)
			$ot->fijar("comboColores",genXulComboColores($idcolor));
	else
			$ot->fijar("comboColores",genXulComboColores("ninguno"));
			
	$ot->fijar("comboTallas",genXulComboTallas($idtalla));
		
	
	echo $ot->Output();	
}

function GeneraXul($retorno) {

 $txtMoDet   = getModeloDetalle2txt();
 $esBTCA     = (  $txtMoDet[0]  == "BTCA" );
 $btca       = ( $esBTCA )?'':'collapsed="true"';
 $txtalias   = $txtMoDet[3];
 $txtModelo  = $txtMoDet[1];
 $txtDetalle = $txtMoDet[2];

?>	
<grid> 
<columns> 
  <column flex="1"/><column flex="1"/>
</columns>
<rows>
  <row>
    <caption label="<?php echo _("Proveedor") ?>"/>				    
    <menulist id="idprov">
      <menupopup>
	<menuitem label="Elije Proveedor" style="font-weight: bold"/>
	<?php echo genXulComboProveedores(false,"menuitem") ?>
      </menupopup>
    </menulist>
  </row>

  <row <?php echo $btca ?>>
    <caption label="<?php echo _("Laborarotio") ?>"/>				    
    <menulist id="idlab">
      <menupopup>
	<menuitem label="Elije Laboratorio" style="font-weight: bold"/>
	<?php echo genXulComboLaboratorios(false,"menuitem") ?>
      </menupopup>
    </menulist>
  </row>

  <row>
    <caption label="<?php echo _("Marca") ?>"/>
    <menulist  id="idmarca">
      <menupopup>
	<menuitem label="Elije Marca" style="font-weight: bold"/>
	<?php echo genXulComboMarcas(false,"menuitem") ?>
      </menupopup>
    </menulist>
  </row>
  <row>
    <caption label="<?php echo _("Familia") ?>"/>
    <menulist  id="idfamilia" oncommand="changeFam(this.value)">
      <menupopup>
	<menuitem label="Elije Familia" style="font-weight: bold"/>
	<?php echo genXulComboFamilias(false,"menuitem") ?>
      </menupopup>
    </menulist>
  </row>
  <row>
    <caption label="<?php echo _("Sub Familia") ?>"/>
    <menulist  id="idsubfamilia">
      <menupopup id="elementosSubFamilias">
	<menuitem label="Elije Sub Familia" style="font-weight: bold"/>
        <?php echo genXulComboSubFamilias(false,1,"menuitem") ?>
      </menupopup>
    </menulist>
  </row>
  <row>
    <caption label="<?php echo $txtModelo ?>"/>
    <menulist  id="idcolor" >
      <menupopup id="elementosColores">
	<menuitem value="0" label="Elije Modelo" style="font-weight: bold" />
	<?php echo genXulComboColores(0, "menuitem", 1,"def");?>
      </menupopup>
    </menulist>
  </row>
  <row>
    <caption label="<?php echo $txtDetalle ?>"/>
    <menulist  id="idtalla">
      <menupopup id="elementosTallas">
	<menuitem value="0" label="Elije Detalle" style="font-weight: bold"/>
	<?php echo  genXulComboTallas(0, "menuitem",5,"def",1);?>  	
      </menupopup>
    </menulist>
  </row>

 <row>
    <caption label="<?php echo $txtalias ?>"/>				    
    <menulist id="idalias">
      <menupopup  id="elementosAlias">
	<menuitem value="0" label="Elije <?php echo $txtalias ?>" style="font-weight: bold"/>
        <?php echo genXulComboProductoAlias(0,'menuitem', 1,'def') ?>
      </menupopup>
    </menulist>
  </row>
 <row>
   <caption label="Nombre"></caption>
   <textbox id="p_Nombre" onkeypress="if (event.which == 13) { buscar() }" onfocus="select()"></textbox>
 </row>
</rows>
</grid>
<button  image="img/gpos_buscar.png"  label='<?php echo _("Buscar") ?>' oncommand="buscar()"/>

<script><![CDATA[
function id(nombre) { return document.getElementById(nombre) };

function buscar()
{
  var tc;

  var idprov  = id("idprov").value;
  var idlab   = id("idlab").value;
  var idalias = id("idalias").value;
  var idcolor = id("idcolor").value;
  var idmarca = id("idmarca").value;
  var idtalla = id("idtalla").value;
  var idfam   = id("idfamilia").value;
  var nombre  = id("p_Nombre").value;
  var idsubfam= id("idsubfamilia").value;

  window.parent.Productos_buscarextra(idprov,idcolor,idmarca,idtalla,idfam,idlab,idalias,tc,nombre,idsubfam);
     
}

var isubfamilias = 0;
var icolores     = 0;//Indice de color llenada
var itallas      = 0;//Indice de talla llenada
var ialias       = 0;//Indice de talla llenada

function changeFam(){
        setTimeout("RegenSubFamilias()",50);
    	setTimeout("RegenColores()",50);
    	setTimeout("RegenTallajes()",50);
    	setTimeout("RegenAlias()",50);
}


function RegenSubFamilias() {
	 VaciarSubFamilias();

	 var idfam = id("idfamilia").value;
	 var xrequest = new XMLHttpRequest();
	 var url = "selcb.php?modo=subfamilia&IdFamilia="+idfam;

	 xrequest.open("GET",url,false);
	 xrequest.send(null);
	 var res = xrequest.responseText;
	 
	 var lines = res.split("\n");
	 var actual;
	 var ln = lines.length-1;
	 for(var t=0;t<ln;t++){
	    actual = lines[t];
	    actual = actual.split("=");
	    AddSubFamiliaLine(actual[0],actual[1]);		
	 }				
}

function AddSubFamiliaLine(nombre, valor) {
	var xlistitem = id("elementosSubFamilias");	
	
	var xsubfamilia = document.createElement("menuitem");
	xsubfamilia.setAttribute("id","subfamilia_def_" + isubfamilias);
			
	xsubfamilia.setAttribute("value",valor);
	xsubfamilia.setAttribute("label",nombre);
	
	xlistitem.appendChild( xsubfamilia);var xlistitem = id("elementosSubFamilias");	
	isubfamilias++;
}

function VaciarSubFamilias(){

	var xlistitem = id("elementosSubFamilias");
	var iditem;
	var t = 0;
	while( el = id("subfamilia_def_"+ t ) ) 
	{
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}

	isubfamilias = 0;

	id("idsubfamilia").setAttribute("label","");	
}

function RegenColores() {
	VaciarColores();

	var idfam = id("idfamilia").value;
	var xrequest = new XMLHttpRequest();
	var url = "selcb.php?modo=colores&IdFamilia="+idfam;
	
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
	while( el = id("color_def_"+ t ) ) 
	{
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}

	icolores = 0;

	id("idcolor").setAttribute("label","");	
}

function RegenTallajes() {
	VaciarTallas();
		
	var mitallaje = 5;
	var idfam = id("idfamilia").value;			
	var xrequest = new XMLHttpRequest();
	var url = "selcb.php?modo=tallas&IdTallaje="+mitallaje+'&IdFamilia='+idfam;
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

	id("idtalla").setAttribute("label","");	
}

function AddTallaLine(nombre, valor) {
	var xlistitem = id("elementosTallas");	
	
	var xtalla = document.createElement("menuitem");
	xtalla.setAttribute("id","talla_def_" + itallas);
			
	xtalla.setAttribute("value",valor);
	xtalla.setAttribute("label",nombre);

	xlistitem.appendChild( xtalla);var xlistitem = id("elementosTallas");	
	itallas ++;
}


function RegenAlias() {
	VaciarAlias();
	var idfam = id("idfamilia").value;			
	var xrequest = new XMLHttpRequest();
	var url = "selcb.php?modo=alias&IdFamilia="+idfam;
	
	xrequest.open("GET",url,false);
	xrequest.send(null);
	var res = xrequest.responseText;
	
	var lines = res.split("\n");
	var actual;
	var ln = lines.length-1;	
	for(var t=0;t<ln;t++){
	   actual = lines[t];
	   actual = actual.split("=");
	   AddAliasLine(actual[0],actual[1]);		
	}				
}


function VaciarAlias(){
	var xlistitem = id("elementosAlias");
	var iditem;
	var t = 0;

	while( el = id("alias_def_"+ t) ) {
		if (el) {
			//alert('gPOS: \n\n '+ el.id );
			xlistitem.removeChild( el ) ;	
		}
		t = t + 1;
	}
	
	ialias = 0;

	id("idalias").setAttribute("label","");	
}

function AddAliasLine(nombre, valor) {

	var xlistitem = id("elementosAlias");	
	
	var xalias = document.createElement("menuitem");
	xalias.setAttribute("id","alias_def_" + ialias);
			
	xalias.setAttribute("value",valor);
	xalias.setAttribute("label",nombre);

	xlistitem.appendChild(xalias);var xlistitem = id("elementosAlias");	
	ialias ++;
}

]]></script>

<?php	
}


StartXul(_("Elije color"));


switch($modo){
	default:
	case "avanzada":
	  $retorno = (isset($_GET["vuelta"]))? $_GET["vuelta"]:'';
	  //OpcionesBusqueda($retorno);
	  GeneraXul($retorno);
	  break;	
}

EndXul();

?>
