<?php

include("tool.php");
SimpleAutentificacionAutomatica("novisual-service");

include_once("class/filaticket.class.php");
include_once("class/arreglos.class.php");

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

$IdPresupuesto  = CleanID($_POST["IdPresupuesto"]);
$entregado	= CleanFloat($_POST["entrega"]);
$cambio		= CleanFloat($_POST["cambio"]);//dinero devuelto al cliente
if ($cambio>0)
  $entregado 	= $entregado - $cambio;//se elimina el cambio que no tiene sentido aqui
// Cambio: sera positivo si hay que devolverle al cliente
// y negativo si el cliente nos debe.


# Sacamos local
$local 		= getSesionDato("IdTienda");

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
$IdLocalActivo 	= getSesionDato("IdTienda");
$modoTicket 	= $_GET["moticket"];
$modoTPV   	= $_GET["modo"];
$numeroTeorico 	= CleanInt(GeneraNumDeTicket($IdLocalActivo,$modoTicket));

/* VAMOS A LEER EL TICKET LINEA A LINEA */

//¿Cuantos datos hay para recoger?
$numlines = CleanInt($_POST["numlines"]);

for($t=0;$t<$numlines;$t++) {
  $firma = "line_".$t."_";

  $codigo = $_POST[$firma . "cod"];
  if ($codigo) {
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
	$idsubsidiario	= CleanCB($_POST[$firma . "idsubsidiario"]);		
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

$nroDocumento = EjecutarTicket( $idDependiente, $entregado, $local, $idClienteSeleccionado,$modoTicket,
				$entregaEfectivo, $entregaBono, $entregaTarjeta,$cambio,$modoTPV,$IdPresupuesto );

/* SALIMOS DEL PROCESO */
echo $nroDocumento;

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

	global $ImporteNeto,$IvaImporte,$TotalImporte;
	global $icarrito,$carrito;
	
	$costeneto    = $precio * $unidades;
	$coste        = $costeneto - ($costeneto * ($descuento/100.0) );	
	$iva          = $coste * ($impuesto);
	$IvaImporte   = $IvaImporte + $iva;     //Se actualiza cuanto es debido a los impuestos 
	$TotalImporte = $TotalImporte + $coste; //Se actualiza cuanto debe pagar el clientes
	$fila         = new filaTicket;         //Creamos registro de almacenaje

	$fila->Set($codigo,$unidades,$precio,$descuento,$impuesto,
		   $importe,$concepto,$talla,$color,$referencia,$cb,
		   $idsubsidiario,$nombre,$pedidodet,$status,$oferta,$costo,
		   $idproducto,false);

	//Guardamos en carrito
	$carrito[$icarrito] = $fila;
	$icarrito           = $icarrito + 1;	
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
			 $modoTPV,$IdPresupuesto ){

    global $TotalImporte;
    global $ImporteNeto;
    global $IvaImporte;
    global $carrito, $UltimaInsercion;
    global $trabajos;

    switch($modoTicket){
        case "preventa":
            //Lo que sea
            $ImportePendiente = intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0;
        if ($ImportePendiente<0)//Se entrego mas de lo que se dio
            $ImportePendiente = 0;
        break;

        case "mproducto":
            //Normalmente la totalidad del coste
            $ImportePendiente = abs(intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0) ;
        break;

        case "interno":
            $ImportePendiente = 0;
        break;

        default:
        $modoTicket = "tipoError:" + CleanRealMysql(CleanParaWeb($modoTicket));
        $ImportePendiente = abs(intval( (abs($TotalImporte) - abs($entregado)) *100 )/100.0) ;
        break;
    }		

    $IdLocal      = CleanID($IdLocal);
    $ImporteNeto  = $TotalImporte - $IvaImporte;
    $IGV          = getSesionDato("IGV");    
    $textDoc      = ($modoTicket=="preventa")? "Preventa":"";
    //Npresupuesto & SPresupuesto
    $IdArqueoCaja = GetArqueoActivoExtra($IdLocal);//Obtenemos la seie del id arqueo caja
    $codDocumento = explode("-",NroComprobantePreVentaMax($IdLocal,$textDoc,$IdArqueoCaja));
    $sreDocumento = ( $codDocumento[0] != $IdArqueoCaja )? $IdArqueoCaja:$codDocumento[0];
    $nroDocumento = ( $codDocumento[0] != $IdArqueoCaja )? 1:$codDocumento[1];
    $TipoVenta    = getSesionDato("TipoVentaTPV");
    $IdUsuarioRegistro  = ($IdPresupuesto != '0')? getIdUsuarioRegistroPresupuesto( $IdPresupuesto ):$idDependiente;
    //PreVenta...
    $esquema = 
      " IdLocal, IdUsuario, IdUsuarioRegistro,".
      " NPresupuesto, TipoPresupuesto,".
      " TipoVentaOperacion, FechaPresupuesto,".
      " ImporteNeto, ImporteImpuesto,".
      " Impuesto, TotalImporte, ".
      " Status, IdCliente, ModoTPV, Serie ";
    $datos   = 
      " '$IdLocal', '$idDependiente', '$IdUsuarioRegistro',".
      " '$nroDocumento', '$textDoc',".
      " '$TipoVenta', NOW(),".
      " '$ImporteNeto', '$IvaImporte',".
      " '$IGV', '$TotalImporte',".
      " 'Pendiente', '$IdCliente', '$modoTPV','$sreDocumento'";

    $sql = "INSERT INTO ges_presupuestos (".$esquema.")"."VALUES (".$datos.")";

    $res = query($sql,"Creando Pre venta ($nroDocumento)");

    if ($res) 
      {
        //Comprobantes...
        $IdComprobante = $UltimaInsercion;

	//Detalles...
        foreach ($carrito as $fila)
	  {			
	    $fila->AltaPedidos($IdComprobante);
	  }		
	
      }	

    //Presupuesto...
    if($IdPresupuesto != '0') setIdCPPresupuesto($IdPresupuesto,$IdComprobante);

    //Numero Pre-Venta...
    return $nroDocumento;
}
function getIdUsuarioRegistroPresupuesto($IdPresupuesto){

  $row = queryrow( " select IdUsuarioRegistro ".
		   " from   ges_presupuestos  ".
		   " where  IdPresupuesto = '".$IdPresupuesto."'" );
  return $row["IdUsuarioRegistro"];

}
function setIdCPPresupuesto($IdPresupuesto,$IdComprobante){
  
  $sql = 
    " UPDATE ges_presupuestos ".
    " SET    IdCP = '".$IdComprobante."' ".
    " WHERE  IdPresupuesto   = '".$IdPresupuesto."'";
  query($sql);	

}

?>
