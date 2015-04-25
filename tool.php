<?php

ini_set("session.gc_maxlifetime",    "86400");
ini_alter("session.cookie_lifetime", "86400" );
ini_alter("session.entropy_file","/dev/urandom" );
ini_alter("session.entropy_length", "512" );

session_start();


include("include/legacy.inc.php");
include("include/debug.inc.php");


//SUPERGLOBALES
if (isset($_GET["modo"]))
	$modo = $_GET["modo"];

if (isset($_GET["cargarmodoget"]))
	$modo = $_POST["modo"];
	
if (!isset($modo))	$modo = false; //Evita algunos warnings	

//NOTA: para release esto en off
$debug_mode = false;	
	
//include("include/multidatabase.inc.php");
include("config/configuration.php");

//INCLUDES
include("include/db.inc.php");
include("include/clean.inc.php");
include("include/combos.inc.php");
include("include/supersesion.inc.php");
include("include/xul.inc.php");
include("include/xml.inc.php");
include("include/ventas.inc.php");
include("include/almacen.inc.php");
include("include/producto.inc.php");
include("include/auth.inc.php");
include("include/pedidos.inc.php");
include("include/js.ini.php");
include("include/series.inc.php");

//INCLUDE OTHER MODULES
include("modulos/pedidosventa/pedidosventa.inc.php");

//CLASES
include ("class/cursor.class.php");
include ("class/template.class.php");
include ("class/local.class.php");
include ("class/perfil.class.php");
include ("class/usuario.class.php");
include ("class/proveedor.class.php");
include ("class/laboratorio.class.php");
include ("class/pedidos.class.php");
include ("class/subsidiario.class.php");
include ("class/movimiento.class.php");
include ("class/albaran.class.php");

//CLASS OTHER MODULES
include ("modulos/promociones/promociones.class.php");
include ("modulos/clientes/cliente.class.php");
include ("modulos/productos/productosinfo.class.php");
include ("modulos/productos/familia.class.php");
include ("modulos/productos/producto.class.php");
include ("modulos/almacen/almacen.class.php");

//TOOLS
include ("tools/toolkit.php");

/////////////////////////////////
// Constantes

$link             = false;
$UltimaInsercion  = false;
$FilasAfectadas   = false;
$debug_sesion     = false;	
$modo_verbose     = false;
$querysRealizadas = array();


$enProcesoDeInstalacion = NULL;//Modificado 2012

if(!$enProcesoDeInstalacion){
	//Durante la instalacion, el dato de lenguaje no esta aun disponible
	$lang = getSesionDato("IdLenguajeDefecto");
}

//DEFINIR ZONA 
date_default_timezone_set('America/Lima');
//
////////////////////////////////

?>
