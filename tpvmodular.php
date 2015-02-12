<?php

	include("tool.php");
//return validaSuscripcones2facturar();
        if (!getSesionDato("IdTienda")){
	  session_write_close();
	  header( "Location: ".$_BasePath."/config");
	  exit();
	}

	include("include/tpv.inc.php");

        //Valida tipo venta VC/VD
        if (!esTipoVenta($_GET["t"])){
	  session_write_close();
	  header("Location: xulentrar.php?modo=avisoUsuarioIncorrecto");
	  exit();
	}

        //xmodulos 
        //set global variable
        setTipoVenta($_GET["t"]); 	
        $TipoVenta   = getSesionDato("TipoVentaTPV");
        $GiroEmpresa = getSesionDato("GlobalGiroNegocio");
        $esPopup     = ( isset($_GET["espopup"]) )? CleanText($_GET["espopup"]):'off';
        $esPopup     = ( $esPopup == 'on' )? 'true':'false';
        
        //TPV corporativo
        $TipoVentaText = ( $TipoVenta=='VC' )? " B2B":" B2C";

	if (!$IdLocalActivo){
		session_write_close();
		header("Location: xulentrar.php?modo=tiendaDesconocida");
		exit();
	}		

	if ($usuarioActivoNoEsDependiente or !$NombreDependienteDefecto){
		session_write_close();
		header("Location: xulentrar.php?modo=avisoUsuarioIncorrecto");
		exit();
	}	

	SimpleAutentificacionAutomatica("visual-xul");
	
	header("Content-type: application/vnd.mozilla.xul+xml");
	header("Content-languaje: es");

	header("Pragma: no-cache");
	header("Cache-control: no-cache");

	$titulo = "TPV ".$TipoVentaText;	
	$cr = "<?";$crf = "?>";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	
	//Config: el impuesto no lo vamos a mostrar
	$esOcultoImpuesto = "true";

	$pvpUnidad = _("PV/U");

	$modosDePago = array( 
				0=> _("EFECTIVO"),
				1=> _("TARJETA"),
				2=> _("TRANSFERENCIA"),
				3=> _("GIRO"),
				4=> _("ENVIO"), 
				5=> _("BONO")
				);
	$statusServicios = array(
		'Pdte Envio' 	=> _("Pdte Envío"),
		'Enviado'		=> _("Enviado"),
		'Recibido' 		=> _("Recibido"),
		'Entregado' 	=> _("Entregado")		
		);

	$NombreEmpresa  = $_SESSION["GlobalNombreNegocio"];  
        $tNombreEmpresa = ($NombreEmpresa =='gPOS')?'': $NombreEmpresa;
        $MensajePromo   = ( $PROMActivo !='')? $PROMActivo:getParametro("MensajePromocion");

        echo str_replace("@","?",'<@xml version="1.0" encoding="UTF-8"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="chrome://global/skin/" type="text/css"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="css/xultpv.css" type="text/css"@>');
        echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css" type="text/css"?>';
?>
	
<window id="window-tpv"  xml:lang="es" 
        title="<?php echo 'gPOS '.$tNombreEmpresa.' // TPV '.$TipoVentaText;?>"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">        

<!--  no-visuales -->  
<?php include("partes-tpv/tpvnovisuales.php"); ?>
<!--  no-visuales -->  

<!-- dependiente / cliente -->
<?php include("partes-tpv/tpvdependientecliente.php"); ?>
<!-- dependiente / cliente -->

<?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda);?>

<hbox flex="1">

<deck id="modoVisual" flex="1">
   
<groupbox flex="1">
    
	<!-- compra producto -->	
	<?php include("partes-tpv/tpvbuscaproducto.php") ?>
	<!-- compra producto -->	
	
	<vbox flex="1">
	<!-- listado productos -->
	 <?php include("partes-tpv/tpvlistaproductos.php") ?> 
	<!-- listado productos -->				

	<!-- listado compra tickets -->
 	<?php include("partes-tpv/tpvlistadoticket.php") ?>  
	<!-- listado compra tickets -->
	</vbox>	

	<!-- total y salir -->
	<hbox style="background-color: black" align="center">
	  <caption  orient="vertical" style="color: #0f0;background-color: black">
	    <label value="Sub Total"/>
	    <label value="Descuento"/>
	  </caption>
	  <caption  orient="vertical" style="color: #0f0;background-color: black">
	    <label value=":"/>
	    <label value=":"/>
	  </caption>
	  <caption orient="vertical"   style="color: #0f0;background-color: black">
	    <label  id="SubTotalLabel" value = " <?php echo $Moneda[1]['S']?> 0.00"/>
	    <label  id="DescuentoLabel" value = " <?php echo $Moneda[1]['S']?> 0.00"/>
	  </caption>
	  <toolbarseparator />
	  <caption  label="TOTAL :"  class="grande" style="color: #0f0;background-color: black"/>
	  <caption  id="TotalLabel"  class="grande" style="color: #0f0;background-color: black"  label=" <?php echo $Moneda[1]['S']?> 0.00"/>

	  <spacer flex="1"/>
	  <toolbarbutton id="ticketPromocionSeleccionado" image="img/gpos_tpvpromocion.png"
			 style="color: #fff; font-size: 13px"  collapsed="true"
			 oncommand="mostrarTicketPromocion()" label=""></toolbarbutton>
	  <toolbarbutton oncommand="MostrarUsuariosForm()">	
	    <caption  style="color: #0f0;background-color: black" id="tCliente"  
		      label="<?php echo $NombreClienteContado ?>" class="media" />
	  </toolbarbutton>
	</hbox>
	<!-- total y salir -->	
      </groupbox>


<!-- modificacion de linea de subsidiario -->
<?php include("partes-tpv/tpvmodificacionlineasubsidiario.php"); ?>
<!-- modificacion de linea de subsidiario -->

<!-- alta de cliente -->
<box align="center" pack="center">
</box>
<!-- alta de cliente -->

<!-- seleccion cliente -->
<?php include("partes-tpv/tpvseleccioncliente.php"); ?>
<!-- seleccion cliente -->

<!-- ficha producto -->
<?php include("partes-tpv/tpvfichaproductos.php"); ?>
<!-- ficha producto -->

<!-- ficha Imprimir -->
<?php include("partes-tpv/tpvimprimir.php"); ?>
<!-- ficha Imprimir  -->

<!-- ficha Imprimir -->
<?php include("partes-tpv/tpvfichaimprimir.php"); ?>
<!--  ficha Imprimir  -->

<!--  ficha Detalles Ventas  -->
<?php  include("partes-tpv/tpvdetallesventa.php"); ?>
<!--  ficha Detalles Ventas  -->

<!-- ficha Listado Subsidiarios -->
<?php include("partes-tpv/tpvfichalistadosubsidiarios.php"); ?>
<!-- ficha Listado Subsidiarios -->

<!-- ficha listados -->
<?php include("partes-tpv/tpvfichalistados.php"); ?>
<!-- ficha listados -->

<!-- ficha Query Abono -->
<?php include("partes-tpv/tpvqueryabono.snip.php"); ?>
<!-- ficha Query Abono -->

<!-- ficha Arqueo de caja -->
<vbox>
<iframe id="frameArqueo" flex="1" src="<?php echo $_BasePath; ?>modulos/arqueo/arqueo2.php"/>
<button class="media"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>
</vbox>
<!-- ficha Arqueo de caja -->

<!-- modificacion de linea de series -->
<?php include("partes-tpv/tpvmodificacionlineaseries.php"); ?>
<!-- modificacion de linea de serie -->

</deck>

<!-- panel botones derecho y mesajeria -->
<?php include("partes-tpv/tpvpanelderecho.php"); ?>
<!-- panel botones derecho y mesajeria -->


</hbox>

<box collapsed="true">
<iframe id="hiddenPrinter" src="about:blank"/>
</box>

<script>//<![CDATA[

var Local                   = new Object();
var Global                  = new Object();
var modospago               = new Array();
var po_nombreclientecontado = "<?php echo addslashes($NombreClienteContado) ?>";
var po_ticketde             = "<?php echo addslashes(_("Ticket de "). $NombreEmpresa) ?>";

 Local.numeroDeSerie 	    = <?php echo CleanID($numSerieTicketLocalActual) ?>;
 Local.motd 		    = "<?php echo addslashes($MOTDActivo) ?>";
 Local.IdLocalActivo 	    = <?php echo CleanID(getSesionDato("IdTienda")) ?>;
 Local.IdLocalDependiente   = <?php echo CleanID(getSesionDato("IdTiendaDependiente")) ?>;
 Local.prefixSerie 	    = "B" + Local.IdLocalActivo  ;
 Local.prefixSerieCS 	    = "CS" + Local.IdLocalActivo  ;
 Local.prefixSerieIN 	    = "IN" + Local.IdLocalActivo  ;
 Local.max_dep 		    = <?php echo CleanID($numDependientes) ?>;
 Local.prefixSerieActiva    = Local.prefixSerie;
 Local.nombretienda 	    = "<?php echo addslashes($NombreLocalActivo) ?>";
 Local.nombreDependiente    = "<?php echo addslashes($NombreDependienteDefecto)?>";
 Local.IdDependiente        = "<?php echo CleanID($IdDependienteDefecto)?>";
 Local.Negocio 		    = "<?php echo addslashes($NombreEmpresa) ?>";
 Local.promoMensaje	    = "<?php echo addslashes($MensajePromo) ?>";
 Local.TPV                  = "<?php echo $TipoVenta; ?>";
 Local.Giro                 = "<?php echo $GiroEmpresa; ?>";
 Local.Imprimir             = true;

 Local.diasLimiteDevolucion = 7;

 Global.fechahoy = "<?php 
	$cad = "%A %d del %B, %Y";
	setlocale(LC_ALL,"es_ES");		
	$hoy = strftime($cad);
	if (function_exists("iconv"))
		echo iconv("ISO-8859-1","UTF-8",$hoy);
	else 
		echo $hoy;			
	
	?>";	

 Global.totalbase = 0;//Valor del ticket actual.
 
 //NOTA: activa funciones avanzadas 
 Global.AdministradorDeFacturasPresente = "<?php echo $_SESSION["EsAdministradorFacturas"] ?>";

<?php
$vEFECTIVO = 0;
	foreach( $modosDePago as $value=>$label ){
		echo "modospago[$value] = '$label';\n";
		if ($label=="BONO"){
			$vBONO = $value;	
		}else if ($label=="EFECTIVO"){
			$vEFECTIVO = $value;	
		}		
	}
	
?>

var vBONO     = parseInt(<?php echo intval($vBONO) ?>,10);
var vEFECTIVO = parseInt(<?php echo intval($vEFECTIVO) ?>,10);
var vIGV      = <?php echo getSesionDato("IGV") ?>;
var esPopup   = <?php echo $esPopup ?>;

function generadorCargado(){
	//Indica que funciones como aU() estan disponibles
	return typeof aU=="function";	
}

function generadorCargadoPromociones(){
	//Indica que funciones como tPL() estan disponibles
	return typeof tPL=="function";	
}

var L = new Array();

function CargarPromociones(){
	if (!generadorCargadoPromociones()) {
		setTimeout("CargarPromociones",100);
		return;
	}
	<?php 
	  echo $generadorJSDePromociones; 
	?>
}

function CargarCBFocus(){
	if (!(typeof CBFocus=="function")) {
		setTimeout("CargarCBFocus()",100);
		return;
	}
	setTimeout("CBFocus()",200);
}
function CargarbtnSalir(){
      if( esPopup ) document.getElementById("botonsalirtpv").setAttribute("oncommand","SalirTPV()");
}

//Cargara los productos cuando sea posible.
CargarPromociones();
CargarCBFocus();
CargarbtnSalir();
//setNombreLocalActivo(Local.IdLocalDependiente);

//Prepara listado de productos para limpieza rapida.
 document.gClonedListbox = document.getElementById('listaProductos').cloneNode(true);
 document.gClonedListboxPanel = document.getElementById('listaPanelProductos').cloneNode(true);

//]]></script>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tpv.js?ver=49/r<?php echo rand(0,99999999); ?>"/>
<script type="application/x-javascript" src="<?php echo $_BasePath; ?>modulos/ordenservicio/ordenservicio.js?ver=49/r<?php echo rand(0,99999999); ?>"/>
</window>

