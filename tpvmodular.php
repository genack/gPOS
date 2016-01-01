<?php

	include("tool.php");

        #Valida Suscripciones
        #return validaSuscripcones2facturar(); 
        #checkSuscripciones();

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
        //Valida usuario tipo venta VC/VD
        if (esTipoVenta($_GET["t"]=='rc')){

	  if( !Admite("B2B") ){
	    session_write_close();
	    header("Location: xulentrar.php?modo=avisoUsuarioIncorrecto");
	    exit();
	  }

	}


        //xmodulos 
        //set global variable
        setTipoVenta($_GET["t"]); 	
        $TipoVenta   = getSesionDato("TipoVentaTPV");
        $GiroEmpresa = getSesionDato("GlobalGiroNegocio");
        $esPopup     = ( isset($_GET["espopup"]) )? CleanText($_GET["espopup"]):'off';
        $esPopup     = ( $esPopup == 'on' )? 'true':'false';
        
        //TPV corporativo
        $TipoVentaText = ( $TipoVenta=='VC' )? " CORPORATIVO":" PERSONAL";
        $esCheckVC     = ( $TipoVenta=='VC' )? 'checked="true"':'';
        $esCheckVD     = ( $TipoVenta=='VD' )? 'checked="true"':'';

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

	$statusServicios = array(
		'Pdte Envio' 	=> _("Pdte Envío"),
		'Enviado'		=> _("Enviado"),
		'Recibido' 		=> _("Recibido"),
		'Entregado' 	=> _("Entregado")		
		);

	$NombreEmpresa  = $_SESSION["GlobalNombreNegocio"];  
        $tNombreEmpresa = ($NombreEmpresa =='gPOS')?'': $NombreEmpresa;
        $MensajePromo   = ( $PROMActivo !='')? $PROMActivo : getParametro("MensajePromocion");

        echo str_replace("@","?",'<@xml version="1.0" encoding="UTF-8"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="chrome://global/skin/" type="text/css"@>');
	echo str_replace("@","?",'<@xml-stylesheet href="css/xultpv.css?v=2" type="text/css"@>');
        echo '<?xml-stylesheet href="'.$_BasePath.'css/xul.css?v=2" type="text/css"?>';
?>
	
<window id="window-tpv"  xml:lang="es" onload="accionInicioTPV();"
        title="<?php echo 'gPOS '.$tNombreEmpresa.' // TPV '.$TipoVentaText.' '.$NombreLocalActivo;?>"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">        

<!--  no-visuales -->  
<?php include("partes-tpv/tpvnovisuales.php"); ?>
<!--  no-visuales -->  

<!--  no-visuales -->  
<?php include("modulos/ordenservicio/ordenservicio.php"); ?>
<!--  no-visuales -->  

<!-- dependiente / cliente -->
<?php include("partes-tpv/tpvdependientecliente.php"); ?>
<!-- dependiente / cliente -->

<?php $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda);?>

<hbox flex="1" class="box">

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
	  <caption class="boxtotal" orient="vertical">
	    <label value="Sub Total"/>
	    <label value="Descuento"/>
	  </caption>
	  <caption class="boxtotal" orient="vertical">
	    <label value=":"/>
	    <label value=":"/>
	  </caption>
	  <caption class="boxtotal" orient="vertical" >
	    <label  id="SubTotalLabel" value = " <?php echo $Moneda[1]['S']?> 0.00"/>
	    <label  id="DescuentoLabel" value = " <?php echo $Moneda[1]['S']?> 0.00"/>
	  </caption>
	  <toolbarseparator />
	  <caption  label="TOTAL :"  class="grande boxtotal" style="color: #0f0;background-color: black"/>
	  <caption  id="TotalLabel"  class="grande boxtotal" style="color: #0f0;background-color: black"  label=" <?php echo $Moneda[1]['S']?> 0.00"/>

	  <spacer flex="1"/>
	  <toolbarbutton id="ticketPromocionSeleccionado" image="img/gpos_tpvpromocion.png"
			 style="color: #fff; font-size: 13px"  collapsed="true"
			 oncommand="mostrarTicketPromocion()" label=""></toolbarbutton>
	  <toolbarbutton oncommand="MostrarUsuariosForm()">	
	    <caption id="tCliente" label="<?php echo $NombreClienteContado ?>" class="boxtotal cliente" />
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
<vbox style="padding:1em">
<iframe id="frameArqueo" name="frameArqueo" flex="1" src="<?php echo $_BasePath; ?>modulos/arqueo/arqueo.php"/>
<button class="media btn"  image="img/gpos_volver.png" label="Volver TPV" oncommand="VerTPV()" collapsed="false"/>
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
var cktextid                = "NOM,NombreClienteBusqueda,NombreBusqueda,buscaCliente,NombreComercial,NumeroFiscal,NombreLegal,buscapedido";

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
 Local.NegocioTipoVenta     = "<?php echo addslashes($tNombreEmpresa) ?>";
 Local.promoMensaje	    = "<?php echo addslashes($MensajePromo) ?>";
 Local.NegocioDireccion	    = "<?php echo addslashes($DIRActivo) ?>";
 Local.NegocioMovil	    = "<?php echo addslashes($MOVILActivo) ?>";
 Local.NegocioTelef	    = "<?php echo addslashes($TELFActivo) ?>";
 Local.NegocioWeb	    = "<?php echo addslashes($WEBActivo) ?>";
 Local.NegocioPoblacion     = "<?php echo addslashes($POBLActivo) ?>";
 Local.TPV                  = "<?php echo $TipoVenta; ?>";
 Local.Giro                 = "<?php echo $GiroEmpresa; ?>";
 Local.Imprimir             = true;
 Local.Reservar             = 0;
 Local.esPrecios            = "<?php echo ( Admite('Precios'))? 1:0; ?>";
 Local.esCajaTPV            = "<?php echo ( Admite('CajaTPV'))? 1:0; ?>";
 Local.esB2B                = "<?php echo ( Admite('B2B'))? 1:0; ?>";
 Local.esSuscripcion        = "<?php echo ( Admite('Suscripcion'))? 1:0; ?>";
 Local.esServicios          = "<?php echo ( Admite('Servicios'))? 1:0; ?>";
 Local.esSAT                = "<?php echo ( Admite('SAT'))? 1:0; ?>";
 Local.esStock              = "<?php echo ( Admite('Stocks'))? 1:0; ?>";
 Local.esAdmin              = "<?php echo ( Admite('Precios'))? true:false; ?>";
 Local.ocupado              = true;
 Local.esCajaCerrada        = "<?php echo cajaescerrado(); ?>";
 Local.esSyncPreventa       = false;
 Local.esSyncProforma       = false;
 Local.esSyncProOnline      = false;
 Local.esSyncMensajes       = false;
 Local.esSyncClientes       = false;
 Local.esSyncClientesPost   = false;
 Local.esSyncStock          = false;
 Local.esSyncStockPost      = false;
 Local.esSyncMProducto      = false;
 Local.esSyncCaja           = false;
 Local.esSyncPromociones    = false; 
 Local.textValue            = '~'; 
 Local.textActive           = false; 
 Local.textId               = cktextid.split(','); 
 Local.textOcupado          = false; 
 Local.diasLimiteDevolucion = 7;
 Local.productos            = 0;
 Local.Impuesto             = parseFloat( <?php echo getSesionDato("IGV") ?> );
 Local.Utilidad             = parseFloat( <?php echo getSesionDato("MargenUtilidad") ?> );
 Local.MPUtilidad           = Local.Utilidad;
 Local.CodigoAutorizacion   = new Array();
 Local.CodigoAutorizacionCliente   = new Array();
 Local.ImprimirFormatoTicket = false;
 Local.AbonoImporte          = 0;
 Local.AbonoBebe             = 0;
 Local.AbonoPendiente        = 0;
 Local.AbonoClienteImporte   = 0;
 Local.AbonoClientePendiente = 0;
 Local.AbonoClienteDebe      = 0;
 Local.CreditoClienteImporte = 0;
 Local.CreditoClienteEntregado = 0;
 Local.CreditoClienteTotal    = 0;

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
      if( !esPopup ) document.getElementById("botonsalirtpv").setAttribute("collapsed","true");
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

<script type="application/x-javascript" src="<?php echo $_BasePath; ?>js/tpv.js?ver=3.1/r<?php echo rand(0,99999999); ?>" async="async"/>

</window>

