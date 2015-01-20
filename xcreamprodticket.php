<?php

include("tool.php");
SimpleAutentificacionAutomatica("novisual-service");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

/*+++++++++++ VALIDA TICKET ++++++++++++++++*/

$modoTicket = CleanText($_GET["moticket"]);
$modoTPV    = CleanText($_GET["modo"]);
$IdLocalActivo 	= getSesionDato("IdTienda");
$IdLocal        = CleanID($IdLocalActivo);
$Estado     = CleanText($_POST["Estado"]);
$numlines   = CleanInt($_POST["numlines"]);
$numvalida  = ( $Estado == 'Finalizado')? $numlines:0;
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
    $rkardex    = getResumenKardex2Producto($idproducto,$IdLocal);//13:4~15:5~13:4

    //idpedidodet:unidades:Serie;Serie,...
    foreach ($apedidodet as $xrow) 
      {
	$axrow       = explode(":", $xrow); 

	//salta > ilimitado:servicio:servicio-externo
	if(!$axrow[1]) continue;

	$idpedidodet = ($axrow[1])? $axrow[0] : false;//IdPedido
	$unidades    = ($axrow[1])? $axrow[1] : false;//Unidades
	$xnseries    = ( isset( $axrow[2]) )? $axrow[2] : false;//Series...
	$srt         = existeUnidAlmacen($unidades,$idproducto,$idpedidodet,
					 $xnseries,$codigo,$IdLocal,$rkardex);
	if( $srt != 0 ) array_push($cbsrt,$srt);
      }
  }

//Termina...
if(count($cbsrt)>0){ echo "x~val~".implode(";", $cbsrt); return; };

/* INCIALIZACIONES */

$alm = new almacenes();

//Posibles estados de una factura
define("FAC_PENDIENTE_PAGO",1);
define("FAC_PAGADA",		2);
define("FAC_IMPAGADA",		3);
define("FAC_ANULADA",		4);

$ImporteNeto 	= 0;//lo que paga el cliente, menos los impuestos
$IvaImporte 	= 0;//cuando de lo que hay que pagar es debido a impuestos
$TotalImporte 	= 0;//Lo que tiene que pagar el cliente
$carrito 	= array(); //acumularemos aqui las lineas de ticket ticket
$icarrito 	= 0;
$trabajos 	= array(); //acumularemos aqui los trabajos a enviar al subsidiario


/* LEEMOS ALGUNOS DATOS GENERALES DEL TICKET */

$IdMProducto    = CleanID($_POST["IdMProducto"]);
$IdMetaProducto = CleanID($_POST["IdMetaProducto"]);
//$Estado         = $_POST["Estado"];
$entregado	= CleanFloat($_POST["entrega"]);
$cambio		= CleanFloat($_POST["cambio"]);//dinero devuelto al cliente
if ($cambio>0)
  $entregado 	= $entregado - $cambio;//se elimina el cambio que no tiene sentido aqui
// Cambio: sera positivo si hay que devolverle al cliente
// y negativo si el cliente nos debe.

# Sacamos dependiente
$dependiente 	= CleanTo($_POST["dependiente"]," ");
$idDependiente 	= getIdFromDependiente($dependiente); 

# Quien compra
$idClienteSeleccionado = CleanID($_POST["UsuarioSeleccionado"]);

# Dinero entregado en metalico
$entregaEfectivo= CleanFloat($_POST["entrega_efectivo"]);
//No se llega a entregar la totalidad, sino solo la diferencia con el cambio
if ($cambio>0) $entregaEfectivo 	= $entregaEfectivo - $cambio;

# Dinero entregado mediante bono o tarjeta
$entregaBono 	= CleanFloat($_POST["entrega_bono"]);
$entregaTarjeta = CleanFloat($_POST["entrega_tarjeta"]);

/* VERIFICACIONES */

# Verificamos la fiabilidad del $numticket

/* VAMOS A LEER EL TICKET LINEA A LINEA */

//¿Cuantos datos hay para recoger?
$numlines = CleanInt($_POST["numlines"]);

for($t=0;$t<$numlines;$t++) 
  {
    $firma = "line_".$t."_";
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
	
	AgnadirTicket($codigo,$unidades,$precio,$descuento,$impuesto,
		      $importe,$concepto,$talla,$color,$referencia,$cb,
		      $idsubsidiario,$nombre,$pedidodet,$status,$oferta,
		      $costo,$idproducto);
  }
}

/* OPERAMOS SOBRE LOS DATOS QUE HEMOS COLECCIONADO */

$CBMP = EjecutarTicket( $idDependiente, $entregado, $IdLocal, $idClienteSeleccionado,$modoTicket,
			$entregaEfectivo, $entregaBono, $entregaTarjeta,$cambio,
			$modoTPV,$IdMProducto,$Estado,$IdMetaProducto );


/* SALIMOS DEL PROCESO */
echo $CBMP;

////////////////////////////////////////////////////////////////////////////////7
//
// Funciones 


/*
 *  popula una fila de ticket con los datos que llegan de la terminal
 */

function AgnadirTicket($codigo,$unidades,$precio,$descuento,$impuesto,
		       $importe,$concepto,$talla,$color,$referencia,$cb,
		       $idsubsidiario,$nombre,$pedidodet,$status,$oferta,
		       $costo,$idproducto) {

	global $ImporteNeto, $IvaImporte, $TotalImporte;
	global $icarrito, $carrito;
	
	$costeneto    = $precio * $unidades;
	$coste        = $costeneto - ($costeneto * ($descuento/100.0) );	
	$iva          = $coste * ($impuesto);
	$IvaImporte   = $IvaImporte + $iva; //Se actualiza cuanto es debido a los impuestos 
	$TotalImporte = $TotalImporte + $coste; //Se actualiza cuanto debe pagar el clientes
	$fila         = new filaTicket;

	$fila->Set($codigo,$unidades,$precio,$descuento,$impuesto,
		   $importe,$concepto,$talla,$color,$referencia,$cb,
		   $idsubsidiario,$nombre,$pedidodet,$status,$oferta,$costo,
		   $idproducto,false);

	//Guardamos en carrito
	$carrito[$icarrito] = $fila;
	$icarrito = $icarrito + 1;	
}

/*
 * ** AGRUPA JOB **
 * cada prenda que se envia a un subsidiario representa un "trabajo" distinto que 
 * tendra uno o varios arreglos.
 */
  
function AgrupaJob( &$arreglo ){
	global $trabajos;
	
	if (!$arreglo->esServicio())
		return;	
	
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
function EjecutarTicket( $idDependiente, $entregado ,$IdLocal,$IdCliente,$modoTicket, 
			 $entregaEfectivo, $entregaBono, $entregaTarjeta, $cambio,
			 $modoTPV,$IdMProducto,$Estado,$IdMetaProducto ){
    global $TotalImporte;
    global $ImporteNeto;
    global $IvaImporte;
    global $carrito, $UltimaInsercion;
    global $trabajos;

    $ImporteNeto = $TotalImporte - $IvaImporte;
    $IGV         = getSesionDato("IGV");    
    $CBMP        = ($modoTicket=="endmproducto")? $IdMProducto : generaCBMP();
    $TipoVenta   = getSesionDato("TipoVentaTPV");
    $vigencia    = getSesionDato("VigenciaPresupuesto");
    $SerialNum   = "";
    //Ensamblaje
    if($modoTicket == "mproducto")
      {
	$esquema = 
	  " IdProducto, IdLocal, FechaRegistro, TipoVentaOperacion,".
	  " UsuarioAlmacen, Costo , Status, CBMetaProducto, IdCliente,VigenciaMetaProducto";
	$datos   = 
	  " '$IdMProducto', '$IdLocal', NOW(),'$TipoVenta',".
	  " '$idDependiente', '$TotalImporte','$Estado','$CBMP','$IdCliente','$vigencia' ";

	//IdProducto IdLocal Fecha UsuarioAlmacen Costo Estado
	$sql = "INSERT INTO ges_metaproductos (".$esquema.")"."VALUES (".$datos.")";
	$res = query($sql,"Inserta Metaproducto ($CBMP)");
      }
     
    //Finaliza... 
    if($modoTicket=="endmproducto")
      {
	//Costo...
	$sql = 
	  " update ges_metaproductos ".
	  " set    Costo           = '".$TotalImporte."',".
	  "        IdCliente       = '".$IdCliente."'".
	  " where  IdMetaProducto  = '".$IdMetaProducto."'";
	$res = query($sql,"Actualiza TotalCosto MetaProducto ($IdMProducto)");	

	//Finaliza MetaProducto...
	if( $Estado == "Finalizado" )
	  {
	    //Registros...
	    $IdProducto = getIdProductoFromIdMetaProducto($IdMetaProducto);
	    $Destino    = $IdLocal;
	    $Origen     = $IdLocal;
	    $Motivo     = '9';
	    $Codigo     = getNextId('ges_comprobantesprov','IdComprobanteProv');

	    //Ventas
	    $IdComprobante = registrarAlbaranOrigen($Destino,$Origen,$Motivo,$Codigo);

	    //Compras...
	    $IdPedido   = registrarAlbaranDestino($Destino,$Origen,$Motivo,$Codigo,
						  'MetaProducto');
	    $Costo      = $TotalImporte;
	    $Precio     = abs(intval((abs($TotalImporte)+abs($TotalImporte*$IGV/100))*100)/100.0);
	    $LoteVence  = 0;
	    $Cantidad   = 1;

	    //Detale Mproducto...
	    $IdPedidoDet = registrarDetalleTrasladoEntrada($IdPedido,$IdProducto,
							   $LoteVence,$Cantidad,
							   $Costo,$Precio,true);
	    //Series Mproducto...
	    registrarNumeroSerieExtra($IdProducto,$IdPedidoDet,
				      $CBMP,false,'Pedido',
				      'MetaProducto','0');
	    //Fecha ensamblaje...
	    $sql = 
	      " update ges_metaproductos ".
	      " set    FechaEnsamblaje = NOW(),IdComprobante = ".$IdComprobante.
	      " where  FechaEnsamblaje = '0000-00-00 00:00:00'".
	      " and    IdMetaProducto  = '".$IdMetaProducto."'";
	    query($sql);

	    //Importes Compras & Ventas
	    registrarImportesTraslado($TotalImporte,$IdComprobante,$IdPedido,'9');
	
	  }
      }

    if (!$res) return false;

    //Detalles...
    if($modoTicket !="endmproducto")  $IdMetaProducto = $UltimaInsercion;
    if($modoTicket =="endmproducto" )	setDelDetMetaProducto($IdMetaProducto);
    
    foreach ($carrito as $fila) 
      {
	//Detalle Mproducto...
	$fila->AltaMProductos($IdMetaProducto);
	
	//Detalle Albaran...
	if( $Estado == "Finalizado" && $modoTicket =="endmproducto" ) 
	  $fila->Alta($IdComprobante,$SerialNum,$IdLocal,"venta");
      }		
    
    //Numero Pre-Venta
    return $CBMP;
}


function setDelDetMetaProducto($IdMetaProducto){
  $sql=
    " UPDATE ges_metaproductosdet ".
    " SET    Eliminado = 1 ".
    " WHERE  IdMetaProducto  = ".$IdMetaProducto;
  query($sql);
}

function generaCBMP() {
  $minval = 0;					
  $sql =
    " SELECT Max(IdMetaProducto) as RefSugerido,".
    "        Max(CBMetaProducto+1) as MaxBarras".
    " FROM   ges_metaproductos";
  $row = queryrow($sql,"Sugiriendo CB Metaproducto Valido");
  if ($row) {
    $sugerido 	= intval($row["RefSugerido"]);
    $maxbarras 	= intval($row["MaxBarras"]);
    if (intval($maxbarras) > intval($sugerido))
      $minval = intval($maxbarras);
    else
      $minval = intval($sugerido) + 30000001;
    
  } else {
    $minval = 30000001+ rand()*10000;	
  }
  
  $extra = 0;
  $CBMP = intval($minval)+intval($extra);
  
  while(CBMPRepetido($CBMP)){
    $extra = $extra + 1;		
    $CBMP = intval($minval) + intval($extra);
  }  
  return $CBMP;
}

function CBMPRepetido($CB){
  $sql = 
    " SELECT IdMetaProducto ".
    " FROM   ges_metaproductos ".
    " WHERE  (CBMetaProducto='$CB') ".
    " AND    Eliminado=0";
  $row = queryrow($sql,"¿Esta repetido CB Meta producto?");
  if (!$row)
    return false;
  return (intval($row["IdMetaProducto"])>0);
		
}



?>
