<?php

include("tool.php");
SimpleAutentificacionAutomatica("novisual-service");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

/*+++++++++++ VALIDA TICKET ++++++++++++++++*/

$modoTicket = CleanText($_GET["moticket"]);
$local      = getSesionDato("IdTienda");
$numlines   = CleanInt($_POST["numlines"]);
$numvalida  = ( $modoTicket=="venta" || $modoTicket=="cesion" )? $numlines:0;
$cbsrt      = array();
$srt        = 0;

//¿Cuantos datos hay para recoger?
for($j=0;$j<$numvalida;$j++)
  {
    $firma      = "line_".$j."_";
    $codigo     = CleanCB($_POST[$firma . "cod"]);
    $idproducto = CleanText($_POST[$firma . "idproducto"]);
    $pedidodet  = CleanText($_POST[$firma . "pedidodet"]);
    $apedidodet = explode(",", $pedidodet);
    $rkardex    = getResumenKardex2Producto($idproducto,$local);//13:4~15:5~13:4

    //idpedidodet:unidades:Serie;Serie,...
    foreach ($apedidodet as $xrow) 
      {
	$axrow       = explode(":", $xrow); 

	//salta > ilimitado:servicio:servicio-externo
	if(!isset($axrow[1])) continue;

	$idpedidodet = ( $axrow[1] )? $axrow[0] : false;//IdPedido
	$unidades    = ( $axrow[1] )? $axrow[1] : false;//Unidades
	$xnseries    = ( isset($axrow[2]) )? $axrow[2] : false;//Series...
	$srt         = existeUnidAlmacen($unidades,$idproducto,$idpedidodet,
					 $xnseries,$codigo,$local,$rkardex);
	if( $srt != 0 ) array_push($cbsrt,$srt);
      }
  }

//Termina...
if(count($cbsrt)>0){ echo "x~val~".implode(";", $cbsrt); return; };

/*+++++++++++ INCIALIZACIONES ++++++++++++*/

//$alm = new almacenes();

//Posibles estados de una factura
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",2);
define("FAC_IMPAGADA",3);
define("FAC_ANULADA",4);

$ImporteNeto 	= 0;       //lo que paga el cliente, menos los impuestos
$IvaImporte 	= 0;       //cuando de lo que hay que pagar es debido a impuestos
$TotalImporte 	= 0;       //Lo que tiene que pagar el cliente
$carrito 	= array(); //acumularemos aqui las lineas de ticket ticket
$icarrito 	= 0;
$trabajos 	= array(); //acumularemos aqui los trabajos a enviar al subsidiario


/* LEEMOS ALGUNOS DATOS GENERALES DEL TICKET */

//Guardamos numero de serie, para referencia posterior
$serie 	        = CleanText($_POST["serieticket"]);
$numticket      = CleanInt($_POST["numticket"]);
$documentoventa = CleanText($_POST["DocumentoVenta"]);
//$nseries      = $_POST["nseries"];
$mensaje        = CleanText($_POST["mensaje"]);

#Vigencia Presupuestos
$vigencia = CleanInt($_POST["vigencia"]);
$vigencia = ($vigencia == 0)? getSesionDato("VigenciaPresupuesto") :$vigencia;

#CB Meta productos
$nsmprod        = CleanText($_POST["nsmprod"]);
$IdPresupuesto  = $_POST["IdPresupuesto"];
$cambio		= CleanFloat($_POST["cambio"]);//dinero devuelto al cliente
$entregado      = CleanFloat($_POST["entrega"]);
$entregado 	= ($cambio>0)? $entregado - $cambio: $entregado;

# Sacamos dependiente
$dependiente 	= CleanTo($_POST["dependiente"]," ");
$idDependiente 	= getIdFromDependiente($dependiente); 

# Quien compra
$idClienteSeleccionado = CleanID($_POST["UsuarioSeleccionado"]);

#Promociones
$idPromocion    = CleanID($_POST["promocion_id"]);
$bonoPromocion  = CleanFloat($_POST["promocion_bono"]);
$entregaEfectivo = CleanFloat($_POST["entrega_efectivo"]);

# Dinero entregado en metalico
//No se llega a entregar la totalidad, sino solo la diferencia con el cambio
$entregaEfectivo = ($cambio>0)? $entregaEfectivo - $cambio : $entregaEfectivo;

# Dinero entregado mediante bono o tarjeta
$entregaBono 	= CleanFloat($_POST["entrega_bono"]);
$entregaTarjeta = CleanFloat($_POST["entrega_tarjeta"]);

/* VERIFICACIONES */

# Verificamos si la peticion ha sido duplicada
# Si es el caso, vendra con un serialrnd igual al anterior

$oldvalue_tpv_serialrand = ( isset($_SESSION["TPV_SerialRand"]))? $_SESSION["TPV_SerialRand"]:'';

if (isset($_POST["TPV_SerialRand"])){

  $newvalue = $_POST["tpv_serialrand"];				
  
  if ($newvalue and ($newvalue == $oldvalue_tpv_serialrand) ){
    //Tenemos un serial, y es el mismo que usamos la otra vez.
    // por tanto es una peticion repetida, y la evitamos saliendo.
    
    //TODO: salimos con 0, ó informamos del problema a la TPV de una manera mejor?.
									
    echo 0;
    exit();				
  }						
}

// Recordaremos el serial utilizado, para evitar repetirlo.
if(isset($_POST["tpv_serialrand"])) $_SESSION["TPV_SerialRand"] = $_POST["tpv_serialrand"];

# Verificamos la fiabilidad del $numticket
$numeroTeorico 	= CleanInt(GeneraNumDeTicket($local,$modoTicket));
$nroDocumento   = CleanInt($_GET["nroDocumento"]);
$sreDocumento   = ( isset( $_GET["sreDocumento"] ) )? CleanInt($_GET["sreDocumento"]):0;
$sreDocumento   = ( $sreDocumento == 0 )? $local : $sreDocumento;
$idDocumento    = CleanInt($_GET["idDocumento"]);

//Si el ticket es menor de lo que deberia
// ..asumimos ha habido algun error y abortamos.
//Si se ha perdido el login, tambien abortamos.
if ( ($numeroTeorico > $numticket) or !$local){ 
  echo 0;
  exit();		
}

setSesionDato( "numSerieTicketLocalActual", $numticket );

/* VAMOS A LEER EL TICKET LINEA A LINEA */

//¿Cuantos datos hay para recoger?
$numlines = CleanInt($_POST["numlines"]);


for($t=0;$t<$numlines;$t++) 
  {
    $firma  = "line_".$t."_";
    $codigo = $_POST[$firma . "cod"];

    if ($codigo) 
      {
	$unidades 	= CleanFloat($_POST[$firma . "unid"]);
	$precio 	= CleanFloat($_POST[$firma . "precio"]);
	$descuento 	= CleanFloat($_POST[$firma . "descuento"]);
	$impuesto 	= CleanFloat($_POST[$firma . "impuesto"]);
	$importe        = CleanFloat($_POST[$firma . "importe"]);
	$concepto 	= CleanText($_POST[$firma . "concepto"]);
	$nombre 	= CleanText($_POST[$firma . "nombre"]);
	$talla 	        = CleanText($_POST[$firma . "talla"]);
	$color     	= CleanText($_POST[$firma . "color"]);
	$referencia     = CleanRef($_POST[$firma . "referencia"]);
	$cb		= CleanCB($_POST[$firma . "cb"]);
	$idsubsidiario	= CleanID($_POST[$firma . "idsubsidiario"]);		
	$pedidodet	= CleanText($_POST[$firma . "pedidodet"]);
	$status   	= CleanText($_POST[$firma . "status"]);
	$oferta   	= CleanText($_POST[$firma . "oferta"]);
	$idproducto	= CleanText($_POST[$firma . "idproducto"]);
	$costo	        = CleanDinero($_POST[$firma . "costo"]);
	$unidadimporte  = ($importe/$unidades);

	AgnadirTicket($codigo,$unidades,$precio,$descuento,$impuesto,
		      $importe,$concepto,$talla,$color,$referencia,$cb,
		      $idsubsidiario,$nombre,$pedidodet,$status,$oferta,$costo,
		      $idproducto,$unidadimporte);
      }
  }

/* OPERAMOS SOBRE LOS DATOS QUE HEMOS COLECCIONADO */

$IdComprobante = EjecutarTicket( $idDependiente, $entregado, $local, $numticket,
				 $serie,$idClienteSeleccionado,$modoTicket,$entregaEfectivo,
				 $entregaBono,$entregaTarjeta,$cambio,$nroDocumento,
				 $idDocumento,$documentoventa,$IdPresupuesto,
				 $mensaje,$vigencia,$nsmprod,$sreDocumento,
				 $idPromocion,$bonoPromocion);

/* SALIMOS DEL PROCESO */
//echo 1;
echo $IdComprobante;


////////////////////////////////////////////////////////////////////////////////7
//
// Funciones 
/*
 *  popula una fila de ticket con los datos que llegan de la terminal
 */

function AgnadirTicket($codigo,$unidades,$precio,$descuento,$impuesto,
		       $importe,$concepto,$talla,$color,$referencia,$cb,
		       $idsubsidiario,$nombre,$pedidodet,$status,$oferta,$costo,
		       $idproducto,$unidadimporte) {

	global $ImporteNeto,$IvaImporte,$TotalImporte;
	global $icarrito,$carrito;
	
	$TotalImporte += $importe; //Se actualiza cuanto debe pagar el clientes

	$fila         = new filaTicket;//Creamos registro de almacenaje
	$fila->Set($codigo,$unidades,$precio,$descuento,$impuesto,
		   $importe,$concepto,$talla,$color,$referencia,$cb,
		   $idsubsidiario,$nombre,$pedidodet,$status,$oferta,$costo,
		   $idproducto,$unidadimporte);

	//Guardamos en carrito
	$carrito[$icarrito] = $fila;
	$icarrito           = $icarrito + 1;	
}

/*
 * ** AGRUPA JOB **
 */
  
function AgrupaJob( &$arreglo ){

	global $trabajos;

	if (!$arreglo->esServicio()) return;	
	
	$codigojob = $arreglo->codigojob;
	error(0,"AgrupaJob: codjob:". $codigojob);
	
	if (!isset($trabajos[$codigojob])){
	  //Tenemos un job nuevo
	  $trabajos[$codigojob] = new job;
	  $trabajos[$codigojob]->CreaDesdeServicio($arreglo);
	  return;				
	}	
	$arreglo->AltaServicio($trabajos[$codigojob]);
}

/**** EJECUTAR TICKET **
 * crear recuerdo de ticket de compra
 */
function EjecutarTicket( $idDependiente, $entregado ,$IdLocal, $Num, 
			 $Serie,$IdCliente,$modoTicket ,$entregaEfectivo, 
			 $entregaBono, $entregaTarjeta, $cambio,$nroDocumento,
			 $idDocumento,$documentoventa,$IdPresupuesto,
			 $mensaje,$vigencia,$nsmprod,$sreDocumento,
			 $idPromocion,$bonoPromocion){
    global $TotalImporte;
    global $ImporteNeto;
    global $IvaImporte;
    global $carrito, $UltimaInsercion;
    global $trabajos;

    switch($modoTicket)
      {
      case "cesion":
      case "venta":
	$ImportePendiente = abs( intval( ( abs($TotalImporte) - abs($entregado) ) * 100 ) / 100.0 );
        $esVenta          = true;
	$esPedido         = false;
      break;
      case "pedidos":
	$ImportePendiente = 0;
	$esVenta          = false;
	$esPedido         = true;
        break;
      }		

    switch($idDocumento)
      {
      case 0: $textDoc = "Ticket"; $nroDocumento = $Num; break;
      case 1: $textDoc = "Boleta";  break;
      case 2: $textDoc = "Factura"; break;
      case 4: $textDoc = "Albaran"; break;
      case 5: $textDoc = "Proforma"; break;
      case 6: $textDoc = "Preventa"; break;
      }

    $IdLocal      = CleanID($IdLocal);
    $Status       = ( abs($ImportePendiente) > 0.009 )? FAC_PENDIENTE_PAGO : FAC_PAGADA;
    $IGV          = getSesionDato("IGV");    
    $IvaImporte   = ($TotalImporte*100/100.0) - round( $TotalImporte*100/($IGV+100), 2);
    $ImporteNeto  = $TotalImporte - $IvaImporte;
    $TipoVenta    = getSesionDato("TipoVentaTPV");
    $t_Doc        = ( $modoTicket=="interno"    )? $textDoc." Servicio :" : $textDoc." Venta :";
    $textticket   = $t_Doc." ".$sreDocumento." - ".$nroDocumento;
    $SerialNum    = "$Serie-$Num";
    $IdCliente    = ($IdCliente)? $IdCliente:1;   
    $res          = false;
    
    //Comprobantes...
    if($esVenta){

      $esquema =
	"IdLocal, IdUsuario, SerieComprobante,".
	"NComprobante,TipoVentaOperacion,FechaComprobante,".
	"ImporteNeto, ImporteImpuesto, Impuesto, TotalImporte,".
	"ImportePendiente, Status,IdCliente,IdPromocion";

      $datos = 
	"'$IdLocal','$idDependiente','$Serie',".
	"'$Num','$TipoVenta',NOW(),".
	"'$ImporteNeto','$IvaImporte','$IGV','$TotalImporte',".
	"'$ImportePendiente','$Status','$IdCliente','$idPromocion'";

      $sql = "INSERT INTO ges_comprobantes (".$esquema.") VALUES (".$datos.")";
      $res = query($sql,"Creando Ticket ($modoTicket)");
      $IdComprobante = $UltimaInsercion;

      //Bono && Historial Venta...
      $xbono = ( $entregaBono > 0 )? ' Bono = 0 ':false;
      $xbono = ( $bonoPromocion > 0 )? " Bono = '$bonoPromocion' ": $xbono;
      cargarVenta2HistorialVenta($IdCliente,$TotalImporte,true,$xbono);
    }

    //Proformas...
    if( $esPedido ){

      $esquema = 
	"IdLocal, IdUsuario, NPresupuesto,TipoPresupuesto,".
	"TipoVentaOperacion,FechaPresupuesto,ImporteNeto,".
	"ImporteImpuesto, Impuesto, TotalImporte, Status,IdCliente,".
	"Observaciones,CBMetaProducto,VigenciaPresupuesto,Serie";

      $datos = 
	"'$IdLocal','$idDependiente','$nroDocumento','$textDoc',".
	"'$TipoVenta',NOW(),'$ImporteNeto',".
	"'$IvaImporte','$IGV','$TotalImporte','Pendiente','$IdCliente',".
	"'$mensaje','$nsmprod','$vigencia','$sreDocumento'";
	
      $sql = "INSERT INTO ges_presupuestos (".$esquema.") VALUES (".$datos.")";
      $res = query($sql,"Creando Ticket ($modoTicket)");
  
      $IdComprobante = $UltimaInsercion;
      setVigenciaMProductos($nsmprod,$vigencia);//VIgencia meta Productos
    }

    if (!$res) return 0;

    //ges_comprobantes...

    $TipoOperacion = "Ingreso";//venta, otros    

    //NumeroComprobante...
    if($esVenta)
      if(RegistrarNumeroComprobante($nroDocumento,$IdComprobante,$textDoc,$sreDocumento,false,false))
	return;

    //Dinero...
    if($esVenta)
      EntregarCantidades($textticket,$IdLocal,$entregaEfectivo,$entregaBono,$entregaTarjeta,
			 $IdComprobante,$TipoOperacion);

    //Procesar Lineas...
    foreach ($carrito as $fila) 
      {
	if( $esVenta  ) $fila->Alta( $IdComprobante, $SerialNum, $IdLocal, $documentoventa);
	if( $esPedido ) $fila->AltaPedidos( $IdComprobante, $SerialNum, $modoTicket);
	AgrupaJob( $fila );		
      }		
    
    if($esVenta)
      {
	
	//Trabajos
	foreach ($carrito as $fila) 
	  {
	    $codigojob	= $fila->codigojob;
	    $IdServicio	= $fila->descripcion;
	    
	    if (isset ( $trabajos[$codigojob] ) )
	      {
		$trabajos[$codigojob]->AgnadeId($IdServicio);
	      }
	    else
	      {
		error(__LINE__.__FILE__ , "Error: no acepto $codigojob ");
	      }
	  }	
	
	foreach( $trabajos as $job)
	  {
	    $job->SaveIdServicio();	
	  } 		
      }
    
    if($IdPresupuesto != '0') 
      setIdCPPresupuesto($IdPresupuesto,$IdComprobante,$modoTicket);//Presupuestos
    
    return $IdComprobante;//IdComprobante
}

function EjecutarRetiradaDeAlmacen( $IdLocal ){

	global $carrito, $UltimaInsercion;
		
	foreach ($carrito as $fila) 
	  {
	    if (!$fila->esServicio())
	      $fila->RetiradaDeAlmacen($IdLocal);		
	  }		
}

function setIdCPPresupuesto($IdPresupuesto,$IdComprobante,$modoTicket){

  $sStatus = ($modoTicket=="pedidos")? ", Status ='Modificado'": '';

  $sql     = 
    " UPDATE ges_presupuestos ".
    " SET    IdCP            = '".$IdComprobante."'".$sStatus.",FechaAtencion = NOW() ".
    " WHERE  IdPresupuesto   = '".$IdPresupuesto."'";

  query($sql);	

}

?>
