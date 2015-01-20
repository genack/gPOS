<?php 
include ("../../tool.php");
$modo        = $_GET['modo'];
$Agregar     = false;
$pblistcheck = 'true';
$pblist      = 'false';	

switch ($modo) {

	     case "actualizarSeriesCarritoCompra" :
	       $id      = CleanID($_GET['id']);
	       $fg      = CleanFechaES($_GET['garantia']);
	       $nseries = CleanCadena($_POST['numerosdeserie']);

	       actualizarSeriesCarritoCompra($id,$nseries,$fg);
	       ClonarCarritoSeriesBuyTwoCart();

	       echo 1;
	       exit();
	       break;

	     case "actualizarSeriesCompra" :
	       $id          = CleanID($_GET['id']);
	       $idpedidodet = CleanID($_GET["idpedidodet"]);
	       $unidades    = CleanFloat($_GET["unidades"]);
	       $nseries     = CleanCadena($_POST['numerosdeserie']);
	       $OpEntrada   = CleanText($_GET["operacionentrada"]);
	       $fg          = false;//CleanFechaES($_GET['garantia']); 

	       actualizarSeriesCompra($id,$nseries,$idpedidodet,$unidades,$OpEntrada,$fg);
	       echo 1;
	       exit();
	       break;

	     case "postCompraListado" :
	       $check = CleanCadena($_GET["check"]);
	       $check = ($check=='true')? true:false;

	       setSesionDato("postCompraListado",$check);
	       echo (getSesionDato("postCompraListado"))? 'true':'false';
	       exit();
	       break;

	     case "visualizarseriebuy" :
	       $validarSeries = false;
	       $idpedidodet   = 0;
	       $idlocal       = getSesionDato("IdTienda");
	       $opentrada     = '';
	       $id            = CleanID($_GET["id"]);
	       $unidades      = CleanInt($_GET["u"]);
	       $trasAlta      = CleanID($_GET["trasAlta"]);
	       $textounidades = $unidades;
	       $tituloCart    = "Carrito de Compra - Número de Serie";
	       $btnexittxt    = "";
	       $btnexitcmd    = "";
	       $vtitulo       = false;
	       $prodbase      = false;
	       $fecharead     = false;
	       $escKBoxinit   = '';
	       $producto      = getDatosProductosExtra($id,'nombre');
	       $series        = obtenerSeriesCarritoBuy($id); 
	       $Comprar       = true;
	       $item          = 0;
	       $fila          = 0;
	       $valor         = "false";
	       $selAgregar    = "true";
	       $selBuscar     = "false";
	       $btnComprar    = 'Comprar...';
	       $btnCancelar   = "terminar('Limpiando datos S/N...')";
	       $Garantia      = date("Y-n-j",mktime(0, 0, 0, date("m"),
						    date("d"), 
						    date("Y")+1 ));
	       $escKBox       = false;
	       $esGarantia    = 'false';
	       include("xulseries.php");
	       break;

	     case "visualizarserieAgregaInventario" :
	       $validarSeries = false;
	       $idpedidodet   = 0;
	       $idlocal       = 0;
	       $item          = 0;
	       $opentrada     = '';
	       $fila          = 0;
	       $id            = CleanID($_GET["id"]);
	       $unidades      = CleanInt($_GET["u"]);
	       $trasAlta      = CleanID($_GET["trasAlta"]);
	       $btnexittxt    = "";
	       $btnexitcmd    = "";
	       $escKBoxinit   = '';
	       $vtitulo       = false;
	       $prodbase      = false;
	       $fecharead     = false;
	       $textounidades = $unidades;
	       $tituloCart    = "Ajustar Existencias - Número de Serie";
	       $producto      = getDatosProductosExtra($id,'nombrecb');
	       $series        = obtenerSeriesCarritoBuy($id); 
	       $Comprar       = true;
	       $valor         = "false";
	       $selAgregar    = "true";
	       $selBuscar     = "false";
	       $btnComprar    = ' Aceptar...';
	       $btnCancelar   = "parent.volverStock()";
	       $Garantia      = date("Y-n-j",mktime(0, 0, 0, date("m"),
						    date("d"), 
						    date("Y")+1 ));
	       $escKBox       = false;
	       $esGarantia    = 'false';
	       include("xulseries.php");
	       break;

	     case "visualizarseriescart" :
	       $validarSeries = false;
	       $idpedidodet   = 0;
	       $idlocal       = getSesionDato("IdTienda");
	       $opentrada     = '';
	       $id            = CleanID($_GET["id"]);
	       $unidades      = CleanInt($_GET["u"]);
	       $fila          = CleanInt($_GET["fila"]);
	       $item          = 0;
	       $trasAlta      = ( isset($_GET["trasAlta"]) )? CleanID($_GET["trasAlta"]):'';
	       $tituloCart    = "Carrito de Compra - Número de Serie ";
	       $textounidades = $unidades;
	       $producto      = getDatosProductosExtra($id,'nombre');
	       $series        = obtenerSeriesCarritoCompra($id); 
	       $valor         = "false";
	       $Garantia      = CleanFechaES( obtenerSeriesGarantiaCompra($id) );
	       $btnexittxt    = "";
	       $btnexitcmd    = "";
	       $Comprar       = true;
	       $Agregar       = true;
	       $vtitulo       = false;
	       $prodbase      = false;
	       $fecharead     = false;
	       $escKBoxinit   = '';
	       $btnComprar    = 'Comprar...';
	       $btnCancelar   = "terminar('Limpiando datos S/N...')";
	       $selAgregar    = "true";
	       $selBuscar     = "false";
	       $escKBox       = false;
	       $esGarantia    = 'false';
	       //print_r( getSesionDato("xdtCarritoCompras") );
	       include("xulseries.php");
	       break;
	     case "visualizarAgnadirProductoCompra" :
	       $id            = CleanID($_GET["id"]);
	       $manejalote    = (CleanID($_GET["manejalote"])==1 )? true:false;
	       $manejafv      = (CleanID($_GET["manejafv"])==1 )?   true:false;
	       $menudeo       = (CleanID($_GET["menudeo"])==1 )?    true:false;
	       $manejaserie   = CleanID($_GET["manejaserie"]);
	       $trasAlta      = CleanID($_GET["trasAlta"]);
	       $UContenedor   = CleanCadena($_GET["UContenedor"]);
	       $UMedida       = CleanCadena($_GET["UMedida"]);
	       $Contenedor    = CleanCadena($_GET["Contenedor"]);
	       $CostoUnitario = CleanDinero($_GET["CostoUnitario"]);
	       $producto      = getDatosProductosExtra($id,'nombre');
	       $series        = ($manejaserie == 1 )? obtenerSeriesCarritoCompra($id):''; 
	       $pblist        = (getSesionDato("postCompraListado"))? 'true':'false';	
	       $pblistcheck   = ($trasAlta=='1')? 'true':'false';
 	       $detadoc       = getSesionDato("detadoc");
	       $manejaserie   = ( $detadoc[0]=='O' )? 0:$manejaserie;
	       $manejafv      = ( $detadoc[0]=='O' )? false:$manejafv;
	       $manejalote    = ( $detadoc[0]=='O' )? false:$manejalote;
	       $UContenedor   = ( $UContenedor==0 )? 1:$UContenedor;
	       $manejansvl    = ($manejaserie == 1 )? true:false;
	       $fv            = ($manejafv)? CleanFechaES( obtenerFechaVencimiento($id) ):"";
	       $lt            = ($manejalote)?  obtenerLote($id):"";
	       $titulo        = "Carrito de Compra";
	       $cart          = getSesionDato("CarritoCompras");
	       $idcart        = getSesionDato("idprodseriecart");
	       $costes        = getSesionDato("CarroCostesCompra");
	       $descuentos    = getSesionDato("descuentos");
	       $esidcart      = (!isset($cart[$id]))? false:true;
	       $cantidadcart  = ($esidcart)? $cart[$id]:0;
	       $unidadescart  = ($menudeo)?  $cantidadcart%$UContenedor:0;
	       $cantidadcart  = ($menudeo)?  ($cantidadcart-$unidadescart)/$UContenedor:$cantidadcart;
	       $CostoUnitario = ($esidcart)? $costes[$id]:$CostoUnitario;
	       $dscto         = ($esidcart)? $descuentos[$id][0]:0;
	       $cantidad      = ($esidcart)? $cantidadcart: 0;
	       $unidades      = ($esidcart)? $unidadescart: 0;
	       $fila          = 0;
	       //print_r( getSesionDato("xdtCarritoCompras") );
	       include("xulcomprar.php");
	       break;
 
	     case "visualizarModificarProductoCarrito" :

	       $id            = CleanID($_GET["id"]);
	       $manejalote    = (CleanID($_GET["manejalote"]) == 1 )? true:false;
	       $manejafv      = (CleanID($_GET["manejafv"])   == 1 )? true:false;
	       $menudeo       = (CleanID($_GET["menudeo"])    == 1 )? true:false;
	       $manejaserie   = CleanID($_GET["manejaserie"]); 
	       $dscto         = CleanDinero($_GET["dscto"]);
	       $cantidad      = CleanFloat($_GET["Cntidad"]);
	       $unidades      = CleanFloat($_GET["unidades"]);
	       $fila          = CleanID($_GET["fila"]);
	       $trasAlta      = 0;
	       $UContenedor   = CleanCadena($_GET["UContenedor"]);
	       $UMedida       = CleanCadena($_GET["UMedida"]);
	       $Contenedor    = CleanCadena($_GET["Contenedor"]);
	       $CostoUnitario = CleanDinero($_GET["CostoUnitario"]);
	       $producto      = getDatosProductosExtra($id,'nombre');
	       $series        = ( $manejaserie == 1 )? obtenerSeriesCarritoCompra($id):''; 
	       $detadoc       = getSesionDato("detadoc");
	       $manejaserie   = ( $detadoc[0]=='O' )? 0:$manejaserie;
	       $manejafv      = ( $detadoc[0]=='O' )? false:$manejafv;
	       $manejalote    = ( $detadoc[0]=='O' )? false:$manejalote;
	       $fv            = ( $manejafv )? CleanFechaES( obtenerFechaVencimiento($id) ):"";
	       $lt            = ( $manejalote )?  obtenerLote($id):"";
	       $UContenedor   = ( $UContenedor==0 )? 1:$UContenedor;
	       $titulo        = "Modificando Carrito de Compra";
	       //print_r( getSesionDato("xdtCarritoCompras") );
	       include("xulcomprar.php");
	       break;

	     case "actualizarSeriesCarritoNS" :

	       $id      = CleanID($_GET['id']);
	       $nseries = $_POST['numerosdeserie']; 
	       $fg      = CleanFechaES($_GET['garantia']); 
	       actualizarSeriesCarritoNS($id,$nseries,$fg);
	       echo 1;
	       exit();
	       break;

	     case "mostrarSeriesAlmacen" :
	       $validarSeries  = false;
	       $idpedidodet    = 0;
	       $idbaseproducto = CleanID($_GET["id"]);
	       $id             = getIdFromProdBase($idbaseproducto);
	       $unidades       = 0;
	       $textounidades  = $unidades;
	       $costo          = '';
	       $idlocal        = $_SESSION["LocalMostrado"];
	       $series         = obtenerSeriesAlmacenProdBase($idbaseproducto);
	       $producto       = getDatosProductosExtra($id,'prodbaseref');
	       $tituloCart     = "Existencias";
	       $valor          = "true";
	       $prodbase       = true;
	       $Comprar        = false;
	       $vtitulo        = false;
	       $item           = 0;
	       $fecharead      = true;
	       $opentrada      = '';
	       $Garantia       = '';
	       $selAgregar     = "false";
	       $selBuscar      = "true";
	       $btnexittxt     = " Volver Stock ";
	       $btnexitcmd     = " SalirNStoAlmacen()";
	       $escKBox        = false;
	       $esGarantia     = 'false';
	       $escKBoxinit    = "";
	       include("xulseries.php");
	       break;

	     case "addJSGetWaySerie" :
	       $IdLocalMostrado = $_SESSION["LocalMostrado"];
	       $id = $_GET['id'];
	       $numerosdeserie = $_POST['numerosdeserie'];
	       $nseries = explode(";",$numerosdeserie);
	       //*** Control SERIES ARRAY  
	       if(is_array($_SESSION["JSGetWaySerieAlma"])){
		 //print_r($_SESSION["JSGetWaySerieAlma"]);
		 $i=0;
		 foreach ($_SESSION["JSGetWaySerieAlma"] as $arrayprod){
		   if($arrayprod[0]==$id && $arrayprod[1]==$IdLocalMostrado){
		     unset($_SESSION["JSGetWaySerieAlma"][$i]);
		   }
		   $i++;
		 }
		 $_SESSION["JSGetWaySerieAlma"] = array_merge($_SESSION["JSGetWaySerieAlma"]);
	       } else {
		 $_SESSION["JSGetWaySerieAlma"] = array();
	       }
	       //*** Push Series ARRAY     
	       $productoSerie = array($id,$IdLocalMostrado,$nseries);
	       array_push($_SESSION["JSGetWaySerieAlma"], $productoSerie);
	       //print_r($_SESSION["JSGetWaySerieAlma"]);
	       echo 1;
	       exit();
	       break;

	     case "mostrarSeriesAlmacenCarrito" :
	       $validarSeries = false;
	       $idpedidodet   = 0;
	       $idarticulo    = CleanID($_GET["xalmacen"]);
	       $id            = CleanID($_GET["xproducto"]);
	       $idlocal       = $_SESSION["LocalMostrado"];
	       $unidades      = CleanFloat($_GET["unid"]);
	       $textounidades = $unidades;
	       $costo         = (isset($_GET["costo"]))?CleanDinero($_GET["costo"]):'';
	       $tituloCart    = "Carrito Almacén";
 	       $producto      = getDatosProductosExtra($id,'nombrecb');

	       $aSeries       = getSesionDato("CarritoMoverSeries");
	       $esSerie       = ( $aSeries[$idarticulo] )? true:false;
	       $mSeries       = ( $esSerie )? $aSeries[$idarticulo]:'';
	       $seriesxPedido = explode("~", $mSeries);
	       $Series        = '';
	       $srt           = false;
	       $vernseries    = array();

	       foreach ($seriesxPedido as $nsPedido )
		 {
		   $aPedido = explode(":", $nsPedido);
		   $Pedido  = $aPedido[0];
		   $Series  = $Series.$srt.$aPedido[1];
		   $srt     = ',';
		 }
	       $series        =  str_replace(',', ';', $Series);
	       $valor         = "true";
	       $fecharead     = true;
	       $selAgregar    = "false";
	       $selBuscar     = "true";
	       $btnexittxt    = " Volver Carrito... ";
	       $btnexitcmd    = "SalirNStoAlmacenCarrito()";
	       $escKBox       = false;
	       $Garantia      = '';
	       $Comprar       = false;
	       $prodbase      = false;
	       $vtitulo       = false;
	       $item          = 0;
	       $fecharead     = true;
	       $opentrada     = '';
	       $esGarantia    = 'true';
	       $escKBoxinit   = "";
	       include("xulseries.php");
	       break;

	     case "mostrarSeriesAlmacenxProducto" :
	       $validarSeries = false;
	       $idpedidodet   = 0;
	       $idlocal       = $_SESSION["LocalMostrado"];
	       $id            = CleanID($_GET["id"]);
	       $unidades      = CleanFloat($_GET["unid"]);
	       $textounidades = $unidades;
	       $costo         = (isset($_GET["costo"]))?CleanDinero($_GET["costo"]):'';
	       $tituloCart    = "Existencias";
	       $producto      = getDatosProductosExtra($id,'nombrecb');
	       $series        = obtenerSeriesAlmacenProducto($id);
	       $valor         = "true";
	       $Garantia      = '';
	       $Comprar       = false;
	       $prodbase      = false;
	       $vtitulo       = false;
	       $item          = 0;
	       $fecharead     = true;
	       $selAgregar    = "false";
	       $selBuscar     = "true";
	       $btnexittxt    = " Volver Stock ";
	       $btnexitcmd    = " SalirNStoAlmacen()";
	       $escKBox       = false;
	       $esGarantia    = 'false';
	       $escKBoxinit   = "";
	       include("xulseries.php");
	       break;

	     case "validarSeriesCompraxProducto" :
	       $id            = CleanID($_GET["id"]);
	       $idpedidodet   = CleanID($_GET["idpedidodet"]);
	       $idlocal       = CleanID($_GET["idlocal"]);
	       $opentrada     = (isset($_GET["operacionentrada"]))? CleanText($_GET["operacionentrada"]):'';
	       $producto      = CleanCadena($_GET["producto"]);
	       $unidades      = CleanFloat($_GET["cantidad"]);
	       $costo         = (isset($_GET["costo"]))?CleanDinero($_GET["costo"]):'';
	       $tituloCart    = CleanCadena($_GET["titulo"]);
	       $textounidades = $unidades;
	       $series        = obtenerSeriesCompraProducto($id,$idpedidodet,false);
	       $cantidadNS    = obtenerCantidadSeriesCompra($id,$idpedidodet);
	       $Garantia      = getGarantiaPedidoDet($idpedidodet,$id);
	       $valor         = CleanCadena($_GET["valor"]);
	       $modificar     = (isset($_GET["modificar"]))?CleanCadena($_GET["modificar"]):false;
	       $btnComprar    = ($modificar)? "Modificar...":"Comprar...";
	       $btnCancelar   = ($modificar)? "SalirNStoAlmacenBorrador()":
		                              "terminar('Limpiando datos S/N...')";
	       $selBuscar     = "true";
	       $fecharead     = true;
	       $validarSeries = 1;
	       $escKBox       = false;
	       $escKBoxinit   = '';
	       $esGarantia    = 'false';
	       $selAgregar    = "false";
	       $Comprar       = false;
	       $btnexittxt    = "";
	       $btnexitcmd    = "";
	       $vtitulo       = false;
	       $prodbase      = false;
	       $fila          = 0;
	       $trasAlta      = 0;
	       include("xulseries.php");
	       break;

	     case "validarSeriesKardexProducto" :
	       $id            = CleanID($_GET["id"]);
	       $idpedidodet   = CleanID($_GET["idpedidodet"]);
	       $producto      = CleanCadena($_GET["producto"]);
	       $unidades      = CleanFloat($_GET["cantidad"]);
	       $costo         = (isset($_GET["costo"]))?CleanDinero($_GET["costo"]):0;
	       $tituloCart    = CleanCadena($_GET["titulo"]);
	       $textounidades = $unidades;
	       $series        = obtenerSeriesCompraProducto($id,$idpedidodet,true);
	       $cantidadNS    = obtenerCantidadSeriesCompra($id,$idpedidodet);
	       $Garantia      = getGarantiaPedidoDet($idpedidodet,$id);
	       $valor         = CleanCadena($_GET["valor"]);
	       $modificar     = (isset($_GET["modificar"]))? CleanCadena($_GET["modificar"]):false;
	       $btnComprar    = ($modificar)? "Modificar...":"Comprar...";
	       $btnCancelar   = ($modificar)? "SalirNStoAlmacenBorrador()":"terminar('Limpiando datos S/N...')";
	       $btnexittxt    = " Volver Kardex... ";
	       $btnexitcmd    = " SalirNStoKardex()";
	       $selBuscar     = "true";
	       $selAgregar    = "false";
	       $fecharead     = true;
	       $vtitulo       = true;
	       $validarSeries = 0;
	       $Comprar       = false;
	       $prodbase      = false;
	       $idlocal       = $_SESSION["LocalMostrado"];
	       $opentrada     = '';
	       $fila          = 0;
	       $trasAlta      = 0;
	       $escKBoxinit   = '';
	       $escKBox       = false;
	       $esGarantia    = 'false';
	       include("xulseries.php");
	       break;

	     case "selSeriesKardexProductoPedido" :
	       $id            = CleanID($_GET["id"]);
	       $idlocal       = (isset($_SESSION["LocalMostrado"]))? $_SESSION["LocalMostrado"]:0;
	       $idpedidodet   = CleanID($_GET["idpedidodet"]);
	       $producto      = CleanCadena($_GET["producto"]);
	       $unidades      = CleanFloat($_GET["cantidad"]);
	       $costo         = (isset($_GET["costo"]))? CleanDinero($_GET["costo"]):0;
	       $tituloCart    = CleanCadena($_GET["titulo"]);
	       $textounidades = $unidades;
	       $series        = obtenerSeriesCompraProducto($id,$idpedidodet,true);
	       $cantidadNS    = obtenerCantidadSeriesCompra($id,$idpedidodet);
	       $Garantia      = getGarantiaPedidoDet($idpedidodet,$id);
	       $valor         = CleanCadena($_GET["valor"]);
	       $modificar     = (isset($_GET["modificar"]))?CleanCadena($_GET["modificar"]):false;
	       $btnComprar    = ($modificar)? "Modificar...":"Comprar...";
	       $btnCancelar   = ($modificar)? "SalirNStoAlmacenBorrador()":"terminar('Limpiando datos S/N...')";
	       $btnexittxt    = " Volver Kardex... ";
	       $btnexitcmd    = " SalirNStoKardex()";
	       $selBuscar     = "true";
	       $selAgregar    = "false";
	       $Comprar       = false;
	       $fecharead     = true;
	       $vtitulo       = true;
	       $validarSeries = 0;
	       $opentrada     = '';
	       $prodbase      = false;
	       $escKBox       = true;
	       $esGarantia    = 'false';
	       $escKBoxinit   = "setTimeout('setcKBoxSerie()',100)";
	       include("xulseries.php");
	       break;

	     case "agnadirCarritoDirecto" :
	       $id         = CleanID($_GET["id"]);
	       $costo      = CleanDinero($_GET["costo"]);
	       $lt         = CleanCadena($_GET["lt"]);
	       $vlt        = ($lt=='')?false:$lt;
	       $fv         = CleanFechaES($_GET["fv"]);
	       $vfv        = ($fv=='')?false:$fv;
	       $unidades   = CleanFloat($_GET["unidades"]);
	       $dscto      = CleanDinero($_GET["dscto"]);
	       $importe    = CleanDinero($_GET["importe"]);
	       $pdscto     = CleanFloat($_GET["pdscto"]);
	       AgnadirCarritoComprasDirecto($id,$unidades,$costo,
					    $vfv,$vlt,$dscto,
					    $importe,$pdscto);
	       echo 1;
	       exit();
	       break;
	     }

?>
