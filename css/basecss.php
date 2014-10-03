<?php

header("content-type: text/css");

?>

input.iPrecio { width: 6em;}
input.iUnidades { width: 5em; }
input.iImpuesto { width: 5em; }
input.iCodigoBarras { width: 9em; text-transform: uppercase}
input.iReferencia { width: 8 em;  }
input.iNombre, input.iPueblos { width: 12em; }
input.iDescripcion, input.iDireccion, input.iComentarios { width: 12em; }
input.iCodigoPostal , input.iCP{ width: 6em; }
input.iPaginaWeb, input.iEmail { width: 12em; }
input.iTelefono { width: 12em; }
input.iCC { width: 20em; text-transform: uppercase}
input.iMensajeMes { width: 20em;}
input.iMensajePromo { width: 20em;}
input.iDNI { width: 12em; text-transform: uppercase }


/* Caja extra que aparece en listados */
.subcaja {
 float:right;border: 1px solid lightgray;
}

.fullscreen {
 width: 100%; height: 100%;
}

.auxmenu {
 display: inline;
 list-style: none;
}

.item {
 width: 100%
}

/* iframes mensajeros */

.mensajero {
 visibility: hidden;
 width: 0px;
 height: 0px;
 border: none !important;
}


.debugmensajero {
 visibility: visible !important;
 width: 200px !important;
 height: 100px !important;
 border: 1px solid green !important;
}

/* cabecera, titulos de seccion */

#cabecera {
 position: absolute; left: 2px; top: -8px;
 width: 200px;
 height: 24px;
 font-size: 90%;
}


/* Filas de listados */
.f, input.sbtn {
 font-size: 14px;
}


/* Tras alta, elige que accion continuar */
.acciones {
 float: center;
}


/* td de estos datos van marcados */
.precio, .unidades, .boton, .referencia , .nombre, .nilhead, .talla, .color, .local, .familia{
 border-bottom: 1px solid #eee;
 min-width: 50px;
 text-align: left;
}


.familia {
 color: #666;
}


/* Datos centrados */
.talla, .color, .iconos {
 text-align: center;
}

/* Precios de algo*/
.precio, .unidades {
 text-align: right;
 margin-right: 4px;
 margin-left: 4px; 
}

/* areas de "modificar" "eliminar" y otros botones */
.boton {
 margin-right: 4px;
 margin-left: 4px; 
 text-align: center; 
}


/* para cuando no queremos artefactos como bottom lines de td */
.clear, clear {
 border: none !important;
 border-bottom: 0px !important;
}

/* Pie de pagina */
.piedepagina {
position: absolute; right: 10px; bottom: 10px;
}


/* titulo de Cabecera de seccion */
.cabecera {
 position: relative;
 z-index: 1;
}


/* Tabla del navegador */
.navtable 
{
 width: 150px;
}

/*Etiqueta aviso*/
.warning
{
 -moz-border-radius: 64px;
 font-size: 16px;
/* background-color: #ffc;*/
  background-color: #cfc;
/* deberia ser este, pero el amarillo queda mucho mejor*/
}

/* Panel de aviso*/
.aviso
{
 background-color: #f0f0f0;
 width: 300px;
 heigh: 80; 

 margin-right: 8px;
 margin-left: 8px;
 padding: 2px;
 font-size: 85%;

 -moz-border-radius: 16px;
 float: center;
}



/* cabeceras de listado */ 
.lh {   
 min-width: 80px; /* no soportado por ie excepto con hacks */
 color: black !important;
 text-weight: bold;
 text-align: center;
 background-color: #DFDFDF;
 color: #eee; 
}




/* HR lines de separacion */
.separador {
 width: 50%;
}

/* Un marco para dos ventanas de listado */
.metaframe {
 background-color: #eee;
 border: 2px;
 border-top: 2px solid gray;
 border-left: 4px solid  gray;
 border-right: 4px solid gray;
 border-bottom: 2px solid gray;
 padding: 4px;
 -moz-border-radius: 8px;

}

/* Fila de arriba, con el menu principal*/
.solapa {
 /*background-color: white !important;*/
 padding: 2px;
 color: white;
/* font-weight: bold;*/
 
}

.solapa:hover {
/* background-color: #0A6184;*/
 background-color: green;
 padding-bottom: 3px;
}

.centermenu2 {
/*background-color: gray; */
color: black;
text-align: right;
font-size: 14px;
}

.centermenu {
background-color: gray; 
color: #eee;
text-align: center;
height: 22px;
font-size: 13px;
}

/* forma dentro de otra forma */
.miniforma {
 background-color: white;
 -moz-border-radius: 8px;
 margin: 1px;
 padding: 1px;
 border: 1px solid green;
}

/* formulario (forma)*/
.forma, .listado {
 background-color: white;
  /*background-color: #eee;  */
  border: 2px;
 -moz-border-radius: 8px;
 margin: 4px;
 padding: 4px;
 border: 1px solid #ece8de;/* green */
}

.formaCabeza {
 background-color: green !important;
/*#ece8de #0A6184 background-color: green !important;*/
/* border-collapse: collapse;*/
/* background-color: #ffc !important;*/ 
/* border-bottom: 4px solid green;*/
/* background-color #ffa*/
 
}

.formaTitulo {
 text-align: center;
 font-family: verdana;
 color: white;
 font-size: 14px;
 font-weight: bold;
 font: small-caps;
}




/* Navegador, simbolo de pagina actual */
.pagactual {
 color: gray;
}


/* Zona de un formulario de etiquetas, campo obligatorio */
.tbb {
 background-color: #eee;
 text-align: right !important;
 font-weight: bold;
 padding-right: 4px;
  
}

/* Zona de un formulario de etiquetas */
.t {
 background-color: #eee;
 /* background-color: #ddd;*/
 text-align: right !important; 
 padding-right: 4px;
}

/*??*/
.linkvolver , .linkextra {
 background-color: white;
 font-weight: bold;
 color: gray;
}

/*??*/
.linkvolver:hover , .linkextra:hover {
 text-decoration: none;
 color: blue;
}

/* Inputs en general*/
input {
 font-size: 95%;
 /*importante: mismo tamño de fuentes que tablas*/
}

textarea , input {
 background-color: #efe;
}

/* Selecciones en formularios */
textarea:focus, input:focus, select:focus {
  background-color: #cfc; /* color opcional #ffc amarillo*/
}

/* cabeceras de listado */ 
/* tablas de formulario */
.l {
	background-color: #eee;
	min-width: 80px; /* no soportado por ie excepto con hacks */
}


/*???*/
.td:focus {
	background-color: white;
}

/* butonizados redondeados */
.tb,input.btn, input.sbtn {
	font-size: 12px;
}


/* Botones tienen el mismo estilo que los linkbutonizados 
input.btn, input.sbtn,select.btn {
 background-color: #ece8de;
 border-top: thin solid white;
 border-left: thin solid white;
 border-right: thin solid gray;
 border-bottom: thin solid gray;
 color: blue;

}
*/

/* Enlaces butonizados */
.tb ,input.btn, input.sbtn,select.btn, input[type="button"] {
  border: 1px solid !important;
  -moz-border-top-colors: ThreeDLightShadow ThreeDHighlight;
  -moz-border-right-colors: ThreeDDarkShadow ThreeDShadow;
  -moz-border-bottom-colors: ThreeDDarkShadow ThreeDShadow;
  -moz-border-left-colors: ThreeDLightShadow ThreeDHighlight;
  padding-left: 4px;
 padding-right: 4px;

/* background-color: threedface;*/
 background-color: -moz-dialog;
 color: windowtext!important;
 -moz-appearance: button;
 text-transform: capitalize;
 cursor: pointer;
}

/* Hover de Enlaces butonizados y Botones reales */
.tb:hover, input.btn:hover, input.sbtn:hover, input[type="button"]:hover {
  -moz-border-bottom-colors: ThreeDLightShadow ThreeDHighlight;
  -moz-border-left-colors: ThreeDDarkShadow ThreeDShadow;
  -moz-border-top-colors: ThreeDDarkShadow ThreeDShadow;
  -moz-border-right-colors: ThreeDLightShadow ThreeDHighlight;
 text-decoration: none !important;
/* background-color: buttonHighlight;*/
  background-color: #cfc;
}




/* color de todos los butonizados */

a:visited, a:link, .tb,input.btn, input.sbtn {
 color: green;
}

/* Enlaces normales */
a:visited , a:link{
 text-decoration: none;
}

/* Hover de enlaces normales */
a:hover{
 text-decoration: underline;
}


a:active {
 color: green;
}


/* Textbox, un area que simula un input box readonly con un span */
.textbox {
 border: thin solid gray;
 padding-left: 2px;
 padding-right: 2px;
 /*width: 145px !important;*/
 border-top: thin solid black;
 border-left: thin solid black;
 border-right: thin solid gray;
 border-bottom: thin solid gray;




}

/* Hover de Etiqueta textbox */
.textbox:hover {
 background-color: #cfc;
}

/**/
.maximo {
	color: white;
	background-color: black;
	font-size: 18px;
	font: bold;
	padding: 10px;
}

/* Titulos de Pagina */
h1 {
/*	color: #0A6184;*/
 color: #666;
/* border-left: 4px solid #0A6184;*/
 border-left: 4px solid green;
/*	background-color: #0A6184;*/
	font-size: 18px;
	font: small-caps;
 padding-left: 4px;
}

/* Titulos de seccion */
h2 {
	font-size: 18px;
	font-weight: bold;
	color: gray;
	padding-left: 8px;
	/*background-color: white;		*/
}

/* Subtitulos de seccion*/
h3 {
 font-size: 16px;
 font-weight: bold;
 color: gray;
 padding-left: 16px;
}

/*??*/
.fact {
	color: gray;
	background-color: white;
}


/*Estilo por defecto de pagina*/
body {
/* background-image: url(mac1.gif);*/
/* background-image: url(img/bg2.png);
 background-repeat: repeat-x;*/
 margin: 0px;
 /*-moz-user-focus: ignore;*/
}

/*??*/
.menu
{
	font-weight: bold;
	color: black !important;
	background-color: white;
}

/* tablas en general */
td, th    {
 vertical-align: top;
 font-size: 83%;
}

