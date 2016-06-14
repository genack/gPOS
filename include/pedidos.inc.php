<?php

function getIndexNocero($tarray){		
	foreach($tarray as $key=>$value){
			
		if (intval($key)>0)
			return $key;
	}		
}	

function CrearTraslado($idLocalDestino,$datosCompra) {
	$idOrden = "";
	//TODO: ¿Esta esto bien comentado?		
	//foreach ($compras as $id=>$unidades) {		//TODO: bug?
	foreach ($datosCompra as $id=>$unidades) {
		//$coste = getCosteDefectoProducto($id);				
		//$oPedido->AgnadirProducto($id,$unidades,$coste);
	}
		
	//$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}

class traslado {
	var $_IdComprobante;
	var $_IdPedido;
	var $_stockMover;
	var $_origen;
	var $_destino;
	var $userLog;	
	var $userLogCabecera;
	var $FechaPedido;
	var $FechaSalida;

	function OpenLog($titulo){		
		$comercio = $_SESSION["GlobalNombreNegocio"];	
		$this->userLogCabecera = "<center><u><b><font style='size: 14px'>".CleanParaWeb($comercio) . "</font></b></u></center><p>";
		$this->userLogCabecera .= "<table width='100%' style='border: 1px solid #999'><tr><td><b style='font-size: 110%;text-decoration:underline'>".
			 CleanParaWeb($titulo). "</B></td></tr>";
		$this->userLog = "";
	}
	function CloseLog(){
		$this->userLog .= "</table>";	
		$this->userLog = $this->userLogCabecera . $this->userLog;
	}
	
	function Log(){
		return $this->userLog;	
	}
	
	function LogAdd($html){
		$this->userLog .= $html;
	}
	
	function OperacionTraslado($Destino,$Origen,$Motivo) {

		$this->CrearAlbaran($Origen,$Destino,$Motivo);
		$this->TrasladoBrutal($Motivo);
		$this->RecepcionBrutal($Motivo,$Destino,$Origen);
		return true;
	}
	
	function CrearAlbaran($Origen,$Destino,$Motivo) {

		$this->_stockMover    = array();
		$this->_origen        = $Origen;
		$this->_destino       = $Destino;		
		$codigo               = getNextId('ges_comprobantes','NComprobante');

		if (!$Origen or !$Destino) 
		  return false;		  	

		//Compras
		$this->_IdPedido      = ( $Motivo != '4' )? 
		                        registrarAlbaranDestino($Destino,$Origen,$Motivo,
								$codigo,'TrasLocal'):0;
		//Ventas
		$this->_IdComprobante = registrarAlbaranOrigen($Destino,$Origen,$Motivo,
							       $codigo,$this->_IdPedido);
	}
	
	function TrasladoBrutal($Motivo) {		

		$IdComprobante = $this->_IdComprobante;	
		$IdPedido      = $this->_IdPedido;					
		$linea         = 0;
		$totalimporte  = 0;
		$Origen        = $this->_origen;
		$Destino       = $this->_destino;		
		$igv           = getSesionDato("IGV");
	        $marcadotrans  = getSesionDato("CarritoTrans");
		$Trans         = getSesionDato("CarritoMover");
		$aSeries       = getSesionDato("CarritoMoverSeries");		
		$almacenes     = new almacenes;
		$articulo      = new articulo;		

		foreach ($marcadotrans as $idarticulo ){

		  $oProducto     = new producto();

		  $articulo->Load($idarticulo);
		  $oProducto->Load($articulo->get("IdProducto"));

		  $cantidad      = 0;
		  $idproducto    = $articulo->get("IdProducto");
		  $precio        = round($Trans['Precio'.$idarticulo],2);
		  $costo         = round($Trans['Costo'.$idarticulo],2);
		  $mSeleccion    = $Trans[$idarticulo];
		  $aSeleccion    = explode("~", $mSeleccion);
		  $esSerie       = ( $aSeries[$idarticulo] )? true:false;

		  foreach ( $aSeleccion as $Pedido )
		    {
		      $aPedido       = explode(":", $Pedido);
		      $IdPedidoDet   = $aPedido[0];//Kardex
		      $cantidad      = $aPedido[1];
		      $LoteVence     = (isset($aPedido[3]))? $aPedido[3]:0;
		      $totalimporte += $precio*$cantidad;  
		      $existencias   = $almacenes->obtenerExistenciasKardex($idproducto,$Origen);

		      //Control
		      if ( $existencias < $cantidad ) return;

		      //Ventas
		      $IdComprobanteDet = registrarDetalleTrasladoSalida($idproducto,$cantidad,$costo,
									 $precio,$IdComprobante,
									 $IdPedidoDet,$esSerie,
									 $LoteVence);
		      //Compras
		      $nwIdPedidoDet    = registrarDetalleTrasladoEntrada($IdPedido,$idproducto,
									  $LoteVence,$cantidad,
									  $costo,$precio,$esSerie);
		      //Numeros de Series
		      registrarTrasladoSeries($Origen,$Destino,$IdPedido,$nwIdPedidoDet,
					      $IdPedidoDet,$idarticulo,$idproducto,
		                              $IdComprobante);
		      //Kardex
		      registrarTrasladoKardexFifo($Origen,$idproducto,$IdPedidoDet,
						  $IdComprobanteDet,$costo,$cantidad,
						  $existencias,$Motivo);
		      //Kadex costo almacen
		      $almacenes->actualizarCosto($idproducto,$Origen);

		      //Kadex resumen almacen
		      actualizaResumenKardex($idproducto,$Origen);

		    }
		}	
		//Importes Compras & Ventas
		registrarImportesTraslado($totalimporte,$IdComprobante,$IdPedido,$Motivo);
	}

	function RecepcionBrutal($Motivo,$Destino,$Origen) {	

	  if( $Motivo != 10 ) return;//Motivo:10 Trasladar y Recibir

	  $IdPedido     = $this->_IdPedido;//cIdPedido
	  $IdPedidoDets = $this->_IdPedido;//cIdPedidoDets
	  $IdLocal      = CleanID( $Destino );
	  $Operacion    = 3;//3:Traslado interno

	  //Valida
	  if(verificarEstadoDocumento($IdPedido)) {
	    echo gas("aviso",_("<center> <b>Recepción Albaran</b><br/> ¡Acción restringida! <br/>".
			       " error al validar Albarán </center>")); 	    
	    return;
	  }
	  
	  if(validaIntegridadSeries($IdPedido)) {
	    echo gas("aviso",_("<center> <b>Recepción de Albaran</b><br/> ¡Acción restringida! <br/>".
			       " error al validar Números de Series de los productos </center>")); 
	    return;
	  }

	  //Registra recepcion de Stock
	  registrarPedidoKardexFifo($IdPedido,$IdPedidoDets,$IdLocal,$Operacion,false,false,false);
	  actualizarStatusPedido($IdPedido,'2');
	  actualizarEstadoDocumentoPedido($IdPedido);
	  actualizarPreciosTrasladoDestino($IdPedido,$Destino,$Origen);
	  //precios 
	}
}

function actualizarPreciosTrasladoDestino($IdPedido,$Destino,$Origen){

    $res       = obtenerDetallePedidos($IdPedido);
    while( $row= Row($res) ) 
      {
	$idproducto = $row['IdProducto'];
	$oripv  = obtenerAllPreciosVentaAlmacen($idproducto,$Origen);
	$despv  = obtenerAllPreciosVentaAlmacen($idproducto,$Destino);

	$pvd    = ( $despv["PrecioVenta"]   == 0 )? $oripv["PrecioVenta"]:$despv["PrecioVenta"];
	$pvdd   = ( $despv["PVDDescontado"] == 0 )? $oripv["PVDDescontado"]:$despv["PVDDescontado"];
	$pvcd   = ( $despv["PVCDescontado"] == 0 )? $oripv["PVCDescontado"]:$despv["PVCDescontado"];
	$pvc    = ( $despv["PrecioVentaCorporativo"] == 0)? $oripv["PrecioVentaCorporativo"]: $despv["PrecioVentaCorporativo"];

	actualizarPreciosVentaAlmacen($idproducto,$pvd,$pvdd,$pvc,$pvcd,$Destino);
      }

}

function registrarCodigoComprobanteOrigen($IdComprobante,$Origen,$Motivo,$TipoVenta,
					  $IdUsuario){

	 $Tipo    = 'AlbaranInt';
         $Codigo  = NroComprobanteVentaMax($Origen,$Tipo,$Origen);
         $aCodigo = explode("-", $Codigo);
	 $Serie   = $aCodigo[0];
	 $Nro     = $aCodigo[1];

	 RegistrarNumeroComprobante($Nro,$IdComprobante,$Tipo,$Serie,$TipoVenta,$Origen,
				    $IdUsuario);

	 //Motivo
	 $sql= 
	   "update ges_comprobantesnum ".
	   "set    IdMotivoAlbaran = '".$Motivo."' ".
	   "where  IdComprobante   = '".$IdComprobante."'";
	 query($sql);
}

function registrarImportesTraslado($totalimporte,$IdComprobante,$IdPedido,$Motivo){

           $igv         = getSesionDato( "IGV");
	   $baseimporte = round(100*$totalimporte/(100+$igv),2);
	   $impuesto    = $totalimporte-$baseimporte;
	   $pendiente   = ( $Motivo != '2' )? 0:$totalimporte;
	   $status      = ( $Motivo != '2' )? 2:1;

	   //Ventas
	   $KeysValues  = " ImporteNeto      = $baseimporte,"; 
	   $KeysValues .= " ImporteImpuesto  = $impuesto,";
	   $KeysValues .= " TotalImporte     = $totalimporte,";
	   $KeysValues .= " Status           = $status,";
	   $KeysValues .= " ImportePendiente = $pendiente";
	   $sql         = " update ges_comprobantes set ".$KeysValues.
	                  " where  IdComprobante = '".$IdComprobante."'";
	   query($sql);

	   //Compras
	   if( !$IdPedido ) return;

	   $KeysValues  = " ImporteBase      = $baseimporte,"; 
	   $KeysValues .= " ImporteImpuesto  = $impuesto,";
	   $KeysValues .= " TotalImporte     = $totalimporte,";
	   $KeysValues .= " ImportePago      = $totalimporte,";
	   $KeysValues .= " ImportePendiente = $totalimporte";
	   $sql         = " update ges_comprobantesprov set ".$KeysValues.
	                  " where  IdPedido  = '".$IdPedido."'";
	   query($sql);
}

function registrarAlbaranOrigen($Destino,$Origen,$Motivo,$NComprobante,$IdPedido){

                global $UltimaInsercion;

		$Destinatario = ( $Motivo == '4' )? 'Proveedor':'Local';
		$IdUsuario    = getSesionDato("IdUsuario");
		$Serie        = "B" . $Origen;
		$igv          = getSesionDato( "IGV");

		$Keys      = "IdLocal,";
		$Values    = "'$Origen',";
		$Keys     .= "IdUsuario,";
		$Values   .= "'$IdUsuario',";
		$Keys     .= "SerieComprobante,";
		$Values   .= "'$Serie',";
		$Keys     .= "NComprobante,";
		$Values   .= "'$NComprobante',";
		$Keys     .= "TipoVentaOperacion,";
		$Values   .= "'VC',";
		$Keys     .= "FechaComprobante,";
		$Values   .= "NOW(),";
		$Keys     .= "ImporteNeto,";
		$Values   .= "' ',";
		$Keys     .= "ImporteImpuesto,";
		$Values   .= "'',";
		$Keys     .= "Impuesto,";
		$Values   .= "'$igv',";
		$Keys     .= "TotalImporte,";
		$Values   .= "'',";
		$Keys     .= "ImportePendiente,";
		$Values   .= "'',";
		$Keys     .= " Status,";
		$Values   .= "'1',";
		$Keys     .= " Destinatario,";
		$Values   .= "'$Destinatario',";
		$Keys     .= "IdCliente,";
		$Values   .= "'$Destino',";		
		$Keys     .= "Traslado";
		$Values   .= "'$IdPedido'";		
		$sql       = "insert into ges_comprobantes (".$Keys." ) values (".$Values.")";
		$res       = query($sql);

		if (!$res) return false;

		$IdComprobante = $UltimaInsercion;

		//Codigo Ventas
		registrarCodigoComprobanteOrigen($IdComprobante,$Origen,$Motivo,'VC',
						 $IdUsuario);

		return $IdComprobante;
}

function registrarAlbaranDestino($Destino,$Origen,$Motivo,$NComprobante,$TipoOperacion){
                  
                global $UltimaInsercion;

		$IdUsuario  = getSesionDato("IdUsuario");
		$igv        = getSesionDato("IGV");
		$EstadoPago = ( $Motivo == 2 )? "Pendiente":"Exonerado";//2:Consignación
		
		$Keys      = "IdLocal,";
		$Values    = "$Destino,";
		$Keys     .= "IdAlmacenRecepcion,";
		$Values   .= "$Destino,";
		$Keys     .= "IdUsuario,";
		$Values   .= "'$IdUsuario',";
		$Keys     .= "IdMoneda,";
		$Values   .= "'1',";
		$Keys     .= "IncluyeImpuesto,";
		$Values   .= "'0',";
		$Keys     .= "Impuesto,";
		$Values   .= "'$igv',";
		$Keys     .= "CambioMoneda,";
		$Values   .= "'1',";
		$Keys     .= "Status,";
		$Values   .= "'1',";
		$Keys     .= "FechaPeticion,";
		$Values   .= "NOW(),";
		$Keys     .= "TipoOperacion";
		$Values   .= "'$TipoOperacion'";
		$sql       = "insert into ges_pedidos (".$Keys." ) values (".$Values.")";
		$res       = query($sql);

		if (!$res) return false;

		$IdPedido  =  $UltimaInsercion;

		$Keys      = "ModoPago,";
		$Values    = "'Contado',";
		$Keys     .= "IdUsuario,";
		$Values   .= "'$IdUsuario',";
		$Keys     .= "IdPedido,";
		$Values   .= "'$IdPedido',";
		$Keys     .= "IdPedidosDetalle,";
		$Values   .= "'$IdPedido',";
		$Keys     .= "IdProveedor,";
		$Values   .= "'$Origen',";
		$Keys     .= "TipoComprobante,";
		$Values   .= "'AlbaranInt',";
		$Keys     .= "IdMotivoAlbaran,";
		$Values   .= "$Motivo,";
		$Keys     .= "EstadoPago,";
		$Values   .= "'$EstadoPago',";
		$Keys     .= "Codigo,";
		$Values   .= "'".$Origen."-".$NComprobante."',";
		$Keys     .= "FechaFacturacion,";
		$Values   .= "NOW(),";
		$Keys     .= "FechaPago,";
		$Values   .= "NOW(),";        
		$Keys     .= "EstadoDocumento";
		$Values   .= "'Borrador'";
		$sql       = "insert into ges_comprobantesprov (".$Keys." ) values (".$Values.")";
		$res       = query($sql);

		if (!$res) return false;

		return $IdPedido;
}


function getNextId($tabla,$columna){
    $num = 1; 
    $sql = "SELECT MAX($columna) as Total FROM $tabla";
    $row = queryrow($sql);
    if($row)
        $num = $row["Total"]+1;
     return $num;
}

function registrarDetalleTrasladoSalida($IdProducto,$Cantidad,$Costo,
					$Precio,$IdComprobante,
					$IdPedidoDet,$esSerie,
					$LoteVence){
                global $UltimaInsercion;

		$producto = getDatosProductosExtra($IdProducto,'id');
		$Importe  = round($Cantidad*$Precio*100)/100;
		$altfv    = explode("/", $LoteVence);
		$lt       = ( isset( $altfv[0] ) )? trim($altfv[0]):""; 
		$fv       = ( isset( $altfv[1] ) )? trim($altfv[1]):""; 
		$Lote     = ( $lt == "0" || $lt == "" )? 0:1;
		$Vence    = ( $fv == "0000-00-00" || $fv == "" )? 0:1;
		$Serie    = ( $esSerie )? 1:0;

		$Keys     = "IdComprobante,"; 
		$Values   = "'".$IdComprobante."',"; 
		$Keys    .= "IdProducto,"; 
		$Values  .= "'".$IdProducto."',"; 
		$Keys    .= "IdPedidoDet,"; 
		$Values  .= "'".$IdPedidoDet."',"; 
		$Keys    .= "Cantidad,"; 
		$Values  .= "'".$Cantidad."',";
		$Keys    .= "Precio,";
		$Values  .= "'".$Precio."',"; 
		$Keys    .= "CostoUnitario,";
		$Values  .= "'".$Costo."',"; 
		$Keys    .= "Descuento,"; 
		$Values  .= "'0',"; 
		$Keys    .= "Importe,"; 
		$Values  .= "'".$Importe."',"; 
		$Keys    .= "Talla,"; 
		$Values  .= "'".$producto['IdTalla']."',"; 
		$Keys    .= "Color,";
		$Values  .= "'".$producto['IdColor']."',"; 
		$Keys    .= "Serie,";
		$Values  .= "'".$Serie."',"; 
		$Keys    .= "Lote,";
		$Values  .= "'".$Lote."',"; 
		$Keys    .= "Vencimiento,";
		$Values  .= "'".$Vence."',"; 
		$Keys    .= "Referencia,"; 
		$Values  .= "'".$producto['Referencia']."',"; 
		$Keys    .= "CodigoBarras";
		$Values  .= "'".$producto['CodigoBarras']."'";
		$sql      = "insert into ges_comprobantesdet (".$Keys.") values (".$Values.")";
		query($sql);

		return $UltimaInsercion;
}

function registrarDetalleTrasladoEntrada($IdPedido,$IdProducto,$LoteVence,
					 $Cantidad,$Costo,$Precio,$xSerie){

                if( !$IdPedido ) return 0;

                global $UltimaInsercion;

		//Lote Vencimiento
		$altfv   = explode("/", $LoteVence);
		$lt      = ( isset( $altfv[0] ) )? trim($altfv[0]):""; 
		$fv      = ( isset( $altfv[1] ) )? trim($altfv[1]):""; 
		$lt      = ( $lt == "0" || $lt == "" )? "":$lt;
		$fv      = ( $fv == "0000-00-00" || $fv == "" )? "":$fv;
		$Serie   = ( $xSerie )? 1:0;

		$Importe = round($Cantidad*$Precio*100)/100;

		$Keys    = "IdPedido,"; 
		$Values  = "'".$IdPedido."',"; 
		$Keys   .= "IdProducto,"; 
		$Values .= "'".$IdProducto."',";
		$Keys   .= "Unidades,"; 
		$Values .= "'".$Cantidad."',"; 
		$Keys   .= "CostoUnidad,"; 
		$Values .= "'".$Costo."',"; 
		$Keys   .= "PrecioUnidad,"; 
		$Values .= "'".$Precio."',"; 
		$Keys   .= "Lote,"; 
		$Values .= "'".$lt."',"; 
		$Keys   .= "Serie,";
		$Values .= "'".$Serie."',"; 
		$Keys   .= "FechaVencimiento,"; 
		$Values .= "'".$fv."',"; 
		$Keys   .= "Importe"; 
		$Values .= "'".$Importe."'";
		$sql     = "insert into ges_pedidosdet (".$Keys.")  values (".$Values.")";
		$res = query($sql);

		return $UltimaInsercion;
}


function registrarTrasladoKardexFifo($Origen,$idproducto,$IdPedidoDet,
				     $IdComprobanteDet,$Costo,$Cantidad,
				     $existencias,$Motivo){
  
          $Operacion = ( $Motivo == 4 )? 4:3;//3:Traslado Interno 4:Traslado Externo

          registrarSalidaKardexFifo($idproducto,$Cantidad,$Costo,$Operacion,
				    $Origen,$IdPedidoDet,$IdComprobanteDet,
				    $existencias,false,false,false);
}

function ResetearCarritoCompras(){
	setSesionDato("CompraProveedor",false);
	setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		
	//Reseteamos carrito (no queremos mezclar productos de diferentes proveedores
	setSesionDato("CarritoCompras",false);
	setSesionDato("CarroCostesCompra",false);
	setSesionDato("PaginadorSeleccionCompras",0);	
	setSesionDato("PaginadorSeleccionCompras2",0);
	$ll=array();		
	setSesionDato("idprodserie",$ll);
	setSesionDato("series",$ll);
	setSesionDato("cantserie",$ll);
	setSesionDato("modoserie",$ll);
	setSesionDato("garantia",$ll);
        setSesionDato("descuentos",$ll);
        setSesionDato("fechavencimiento",$ll);
	setSesionDato("codigolote",$ll);
        setSesionDato("codigolocal",$ll);

        //Esto es para el nuevo carrito de numeros de serie
        $s1 = array();
        $s2 = array();
	setSesionDato("idprodseriebuy",$ll);
	setSesionDato("seriesbuy",$s1);
	setSesionDato("idprodseriecart",$ll);
	setSesionDato("seriescart",$s2);
	setSesionDato("fechagarantia",$s2);
	setSesionDato("xdtCarritoCompras",$s2);

        $detadoc=array();
        $detadoc[0]='SD';
        $detadoc[1]='1';
        $detadoc[2]='CASAS VARIAS';
        $detadoc[3]=false;
        $detadoc[4]=false;
        $detadoc[5]=1;
        $detadoc[6]=1;
        $detadoc[7]=false;
        $detadoc[8]=false;
        $detadoc[9]=false;
        $detadoc[10]=false;
        $detadoc[11]=false;
        $detadoc[12]=false;
        $detadoc[13]=0;
        $detadoc[14]=0;
        $detadoc[15]='';
        $detadoc[17]='';
        $detadoc[18]='';
        $detadoc[19]='';
        $detadoc[20]='';
        $detadoc[21]='';
        $detadoc[22]='';
        $detadoc[23]='';
        $detadoc[24]='';
        $detadoc[25]=false;
        setSesionDato("detadoc",$detadoc);
        setSesionDato("aCredito",false);
	setSesionDato('incImpuestoDet',false);
}

function CrearOrdenTraslado($idLocalDestino,$datosCompra) {
		
	$oPedido = new pedido;
	
	$oPedido->Crea();
	
	$oPedido->set("IdAlmacenRecepcion",$idLocalDestino,FORCE);
	
	
	//foreach ($compras as $id=>$unidades) {		//TODO: bug?
	foreach ($datosCompra as $id=>$unidades) {
		$coste = getCosteDefectoProducto($id);				
		$oPedido->AgnadirProducto($id,$unidades,$coste);
	}
		
	$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}

function validarOrdenDeCompra($idLocal){

        $compras = getSesionDato("CarritoCompras");
	if(empty($compras)) 
	  return false;
	else
	  return true;	
}

function CrearOrdenDeCompra($idLocal){

	$id = getSesionDato("DestinoAlmacen");
	
	//echo gas("Nota","Se ha enviado una orden de compra");
	//echo "Localid $id<br>";	
	
	$oPedido = new pedido;
	
	$oPedido->Crea();
	
	$oPedido->set("IdAlmacenRecepcion",$idLocal,FORCE);
	
	$compras = getSesionDato("CarritoCompras");
	$costes =  getSesionDato("CarroCostesCompra");
	
	foreach ($compras as $id=>$unidades) {		
		//TODO: el proveedor podria ser distinto del proveedor habitual
		// ..aqui asumimos que son iguales.				
		$idproveedor = getIdProveedorFromIdProducto($id);
		$idlaboratorio = getIdLaboratorioFromIdProducto($id);
		 
		//Añade una fila de orden de compra				
		$oPedido->AgnadirProducto($id,$unidades,$costes[$id],$idproveedor,$idlaboratorio);
	}
		
	$idOrden = $oPedido->Alta();
	
	return $idOrden;	
}

function VaciarTrasladados($trans){
	foreach($trans as $id){		
		//echo "Anulando ..$id<br>";
		$sql = "UPDATE ges_almacenes SET Unidades = 0 WHERE Id = '$id'";
		query($sql);	
	}	
}

//***** PASARELA SERIES
function getSeriesGetWaySerieAlma($idproducto,$idlocal){
  $seriesProducto = $_SESSION["JSGetWaySerieAlma"];
  $okns=0;
  $i=0;
  foreach ($seriesProducto as $arrayprod){
    if($arrayprod[0]==$idproducto && $arrayprod[1]==$idlocal && $okns == 0){
      $seriescadena = implode(";", $arrayprod[2]);
      $okns=1;
    }
    $i++;
  }
  return $seriescadena;
}

function obtenerIdCompraProducto($IdPedido,$IdProducto){	
  $sql = "SELECT IdPedidoDet ".
         "FROM   ges_pedidosdet  ".
         "WHERE  IdPedido   = '$IdPedido' ".
         "AND    IdProducto = '$IdProducto'";
  $res = query($sql);
  $row = Row($res);
  return $row['IdPedidoDet'];
}

function ObtenerIdAlmacenRecepcion($idpedidodet){	
  $sql = 
    "select IdAlmacenRecepcion ".
    "from   ges_pedidosdet ".
    "inner  join ges_pedidos on ".
    "       ges_pedidos.IdPedido =  ges_pedidosdet.IdPedido ".
    "where  IdPedidodet = ".$idpedidodet;

  $row = queryrow($sql);
  return $row["IdAlmacenRecepcion"];
}

function getGarantiaPedidoDet($IdPedidoDet,$IdProducto){

        $sql =
	  " select FechaGarantia ".
	  " from   ges_pedidosdet ".
	  " where  IdPedidoDet = '".$IdPedidoDet."'".
	  " and    IdProducto  = '".$IdProducto."'";
	$row = queryrow($sql);
	if (!$row)
 		return false;
	return $row["FechaGarantia"];
}

function registrarGarantia($IdProducto,$IdPedido){
    $arr = getSesionDato("fechagarantia");

    for($i=0;$i<count($arr);$i=$i+2)
      {
        $t = $i + 1;
	if($IdProducto==$arr[$i])
	  {
	    $sql = 
	      "update ges_pedidosdet ".
	      "set    FechaGarantia = '".CleanFechaES($arr[$t])."' ".
	      "where  IdProducto       = '".$arr[$i]."' ".
	      "and    IdPedido         = '".$IdPedido."'";
	    query($sql);
	    return;
	  }
      }
    //$ll=array();
    //setSesionDato("fechagarantia",$ll);
}

function registrarVencimiento($IdPedido){
    $arr = getSesionDato("fechavencimiento");
    for($i=0;$i<count($arr);$i=$i+2){
        $t = $i + 1;
	$sql = 
	  "update ges_pedidosdet ".
	  "set    FechaVencimiento = '".CleanFechaES($arr[$t])."' ".
	  "where  IdProducto       = '".$arr[$i]."' ".
	  "and    IdPedido         = '".$IdPedido."'";
        query($sql);
    }
    $ll=array();
    setSesionDato("fechavencimiento",$ll);
}

function registrarLote($IdPedido){
    $arr = getSesionDato("codigolote");
    for($i=0;$i<count($arr);$i=$i+2){
        $t = $i + 1;
	$sql = "update ges_pedidosdet ".
	       "set    Lote       = '".strtoupper($arr[$t])."' ".
	       "where  IdProducto = '".$arr[$i]."'".
	       "and    IdPedido   = '".$IdPedido."'"; 
	query($sql);
    }
    $ll=array();
    setSesionDato("codigolote",$ll);
}

function registraImportes($IdPedido){
  $detadoc    = getSesionDato("detadoc");
  $descuentos = getSesionDato("descuentos");
  $iIGV       = getSesionDato("incImpuestoDet");
  $Moneda     = getSesionDato("Moneda");
  $incIGV     = false;
  $IGV        = getSesionDato("IGV");
  $tImporte   = 0; 
  $sql        = "select IdProducto,Unidades ".
                "from   ges_pedidosdet ".
                "where  IdPedido = '".$IdPedido."'";
  $res = query($sql);
  while($row= Row($res)) {
    $id     = $row["IdProducto"];
    $unid   = $row["Unidades"];
    //Descuentos 
    if($descuentos[$id])
      {
	//Descuento
	$des = round($descuentos[$id][0],4);

	//Importe
	$imp = $descuentos[$id][1];
	$imp = ($iIGV=="true")?$imp:round($imp*($IGV+100)/100,4);

	//+TotalImporte
	$tImporte = $tImporte+$imp;

	//Precio
	$pre = round($imp/$unid,4);

	//Costo
	$cos = $descuentos[$id][1]/$unid;
	$cos = ($iIGV=="true")?$cos*100/($IGV+100):$cos;
	$cos = ($detadoc[5] == 2 )?$cos*$detadoc[6]:$cos;
	$cos = round($cos,4);

	//PedidosDetalle
	$key  = "CostoUnidad   = '".$cos."'";
	$key .= ",Descuento    = '".$des."'";
	$key .= ",Importe      = '".$imp."'";
	$key .= ",PrecioUnidad = '".$pre."'";
	$sql  = "update ges_pedidosdet ".
	        "set    ".$key.
	        "where  IdProducto = '".$id."' ".
	        "and    IdPedido   = '".$IdPedido."'";
	query($sql);
      }
  }

  //TotalImporte
  $TotalImporte    = round($tImporte,2);
  $ImporteBase     = round(($tImporte*100/($IGV+100)),2);
  $ImporteImpuesto = $TotalImporte-$ImporteBase;
  //$ImporteFlete    = ($detadoc[5] == 1)? $detadoc[13]:0;
  $ImportePago     = $TotalImporte+$detadoc[14];

  $listacpKeysVal  = " ImporteImpuesto  ='".$ImporteImpuesto."'";
  $listacpKeysVal .= ",ImporteBase      ='".$ImporteBase."'";
  $listacpKeysVal .= ",ImportePendiente ='".$ImportePago."'";
  $listacpKeysVal .= ",TotalImporte     ='".$TotalImporte."'";
  $listacpKeysVal .= ",ImporteFlete     ='".$detadoc[13]."'";
  $listacpKeysVal .= ",ImportePercepcion ='".$detadoc[14]."'";
  $listacpKeysVal .= ",ImportePago      ='".$ImportePago."'";
  $sql = "update ges_comprobantesprov set ".$listacpKeysVal.
         "where  IdPedido  = '".$IdPedido."' ".
         "and    Eliminado = '0'";
  query($sql);

}    

function actualizarStatusPedido($idpedido,$status){
         $sql = 
	   " update ges_pedidos ".
	   " set    Status   = '".$status."',FechaRecepcion=CURRENT_TIMESTAMP".
	   " where  IdPedido = '".$idpedido."'";
	 $res = query($sql,"Actualizar Status Pedido");
	 if (!$res){			
	   error(__FILE__ .  __LINE__ ,"E: no pudo actualizar costo del articulo");
	   return false;
	 }		
	 return true;
}

function obtenerDetallePedidos($IdPedidoDets){

         $sql = 
	   " select IdProducto,CostoUnidad,".
	   "        Unidades,IdPedidoDet".
	   " from   ges_pedidosdet ".
	   " where  IdPedido in (".$IdPedidoDets.") ";

	 return query($sql);
}

function obtenerDetallePedidoAjuste($IdPedidoDet){

         $sql = 
	   " select IdProducto,CostoUnidad,".
	   "        Unidades,IdPedidoDet".
	   " from   ges_pedidosdet ".
	   " where  IdPedidoDet = '".$IdPedidoDet."'";

	 return query($sql);
}

function obtenerDetalleVentaAjuste($IdComprobante,$ckeydet){

         $sql = 
	   " select IdProducto,IdComprobanteDet,CostoUnitario,".
	   "        IdPedidoDet,Cantidad ".
	   " from   ges_comprobantesdet ".
	   " where  IdComprobante = '".$IdComprobante."'".
	   " and    IdComprobanteDet in (".$ckeydet.") ";

	 return query($sql);
}

function obtenerPedidoDet($id){

         $sql =
	   " select Lote,IdPedidoDet,".
	   "        FechaVencimiento,".
	   "        Serie,IdPedido ".
	   " from   ges_pedidosdet ".
	   " where  IdPedidoDet = ".$id;
	 return query($sql);
}

function obtenerProveedorInterno($xid){

        $sql =
	  " select NombreComercial ".
	  " from   ges_locales ".
	  " where  IdLocal = '".$xid."' ";
	$row = queryrow($sql);
	if (!$row)
 		return '-';
	return $row["NombreComercial"];
}

function obtenerDocumentoComprobanteDet($idx,$kdxop){

	$esCliente = ($kdxop == 'Venta' || $kdxop == "Devolucion" );
	$esProv    = ($kdxop == "Traslado Externo");
	$esLocal   = ($kdxop == "Traslado Interno" || $kdxop == "Ajuste" || $kdxop == 'Inventario');
	$extra     = ($esLocal)?"inner  join ges_locales on ges_locales.IdLocal ":"";
        $extra     = ($esProv)?"inner join ges_proveedores on ges_proveedores.IdProveedor ":$extra;
	$extra     = ($esCliente)?"inner  join ges_clientes on ges_clientes.IdCliente ":$extra;
	
	$sql = 
	  "select concat( TipoComprobante,' Nro ',ges_comprobantestipo.Serie,'-',".
	  "       NumeroComprobante,' ',NombreComercial) as Documento, ".
	  "       FechaVencimiento as fv,".
	  "       ges_pedidosdet.Lote as lt, ".           
	  "       ges_pedidosdet.Serie as ns, ". 
	  "       IdMotivoAlbaran as mv ".       
	  "from   ges_comprobantesnum ".
	  "inner  join ges_comprobantesdet on ".
	  "       ges_comprobantesnum.IdComprobante = ges_comprobantesdet.IdComprobante ".
	  "inner  join ges_comprobantes on ".
	  "       ges_comprobantesdet.IdComprobante = ges_comprobantes.IdComprobante ".
	  "inner  join  ges_pedidosdet on ".
	  "       ges_pedidosdet.IdPedidoDet = ges_comprobantesdet.IdPedidoDet ".
	  "inner  join ges_comprobantestipo on ".
	  "       ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante ".
	  $extra." = ges_comprobantes.IdCliente ".
	  "where  ges_comprobantesdet.IdComprobanteDet = ".$idx;
	//echo "kkkk--><br>".$sql;
	$res = query($sql);
	return Row($res);
	
}

function obtenerDocumentoPedidoDet($idx,$kdxop){
  //echo "<br>======".$kdxop."=======";
	$esProv  = ($kdxop == "Compra");
	$esLocal = ($kdxop == "Traslado Interno" || $kdxop == "Ajuste" || $kdxop == 'Inventario');
	$extra   = ($esLocal)?"inner join ges_locales on ges_locales.IdLocal ":"";
        $extra   = ($esProv)?"inner join ges_proveedores on ges_proveedores.IdProveedor ":$extra;
	//	echo "<br>:::::".$extra."::::";
	$sql =
	   "select concat( TipoComprobante,' Nro ',Codigo,'  ',NombreComercial) as Documento,".
	   "       FechaVencimiento as fv,".
	   "       ges_pedidosdet.Lote as lt, ".           
	   "       ges_pedidosdet.Serie as ns, ".
	   "       IdMotivoAlbaran as mv ".
	   "from   ges_comprobantesprov ".
	   "inner  join ges_pedidosdet on ".
	   "       ges_comprobantesprov.IdPedido = ges_pedidosdet.IdPedido ".
	   $extra." = ges_comprobantesprov.IdProveedor ".
	   "where  ges_pedidosdet.IdPedidoDet = ".$idx;
	 $res    = query($sql);                         

	 return Row($res);  
}

function obtenerInventarioProductoFifo($id,$idlocal){
    
         $sql = 
	   "select CostoUnitarioMovimiento as Costo,".
	   "       sum(CantidadMovimiento) as Unidades,".
	   "       IdPedidoDet ".
	   "from   ges_kardex ".
	   "where  IdProducto = '$id' ".
	   "and    IdLocal    = '$idlocal' ".
	   "and    Eliminado  = 0 ".
	   //"group  by CostoUnitarioMovimiento  ".
	   "group  by IdPedidoDet ".
	   "order  by Idkardex ASC ";
	 $res = query($sql);
	 
	 if(!$res) return false;	
	 
	 $tabla = Array();
	 
	 while($row= Row($res)) 
	   {			
	     $fila           = Array();
	     $filalt         = Array();
	     $costo_unitario = $row["Costo"];
	     $unidades       = $row["Unidades"]; 
	     $idpedidodet    = $row["IdPedidoDet"]; 
	     
	     if($unidades!=0)
	       {
		 array_push($fila,$costo_unitario);
		 array_push($fila,$unidades);
		 array_push($fila,$idpedidodet);
		 array_push($tabla,$fila);
	       }
	   }  
	 
	 return $tabla;
}

function DetallesOrdenCompra($IdOrdenCompra){	
	$sql = 
	  "SELECT ges_productos.Referencia,".
	  "       ges_productos.IdProducto,".
	  "       ges_productos.CodigoBarras,".
	  "       CONCAT(ges_productos_idioma.Descripcion,' ',".
	  "       ges_marcas.Marca,' ',".
	  "       ges_modelos.Color,' ',".
	  "       ges_detalles.Talla,' ',".
	  "       ges_laboratorios.NombreComercial) as Producto,".
	  "       ges_ordencompradet.Unidades as Cantidad,".
	  "       ges_ordencompradet.Costo as Costo, ".
	  "       ges_ordencompradet.IdOrdenCompraDet, ".
	  "       ges_ordencompradet.IdOrdenCompra, ".
          "       ges_productos.VentaMenudeo, ".
          "       ges_contenedores.Contenedor, ".
	  "       ges_productos.UnidadesPorContenedor, ".
	  "       ges_productos.UnidadMedida ".
	  "FROM   ges_ordencompradet 
	        LEFT  JOIN ges_productos ON ges_ordencompradet.IdProducto = ges_productos.IdProducto 
                INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
                INNER JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla
                INNER JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor
                INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio
                INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca
	        INNER JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor
                WHERE ges_ordencompradet.IdOrdenCompra = '$IdOrdenCompra'
                AND   ges_productos_idioma.IdIdioma    = 1
                AND   ges_detalles.IdIdioma              = 1
                AND   ges_modelos.IdIdioma             = 1
                AND   ges_ordencompradet.Eliminado     = 0 ";
	
	$res = query($sql);
	if (!$res) return false;
	$ordencompra = array();
	$t = 0;
	while($row = Row($res)){
		$nombre = "detalles_" . $t++;
		$ordencompra[$nombre] = $row; 		
	}		
	return $ordencompra;
}

function DetallesCompra($IdPedido,$esSoloMoneda){ 
  $extraMoneda  = ($esSoloMoneda=='todo1')? "ges_pedidos.CambioMoneda":"1";
  $sql = 
	  "SELECT ges_productos.Referencia,".
	  "       ges_productos.IdProducto,".
	  "       ges_productos.CodigoBarras,".
	  "       CONCAT(ges_productos_idioma.Descripcion,' ',".
	  "       ges_marcas.Marca,' ',".
	  "       ges_modelos.Color,' ',".
	  "       ges_detalles.Talla,' ',".
	  "       ges_laboratorios.NombreComercial) as Producto,".
	  "       ges_pedidosdet.Unidades as Cantidad,".
	  "       (ges_pedidosdet.CostoUnidad*$extraMoneda) as Costo, ".
	  "       (ges_pedidosdet.PrecioUnidad*$extraMoneda) as Precio, ".
	  "       (ges_pedidosdet.Descuento*$extraMoneda) as Descuento, ".
	  "       (ges_pedidosdet.Importe*$extraMoneda) as Importe, ".
	  "       IF ( ges_pedidosdet.Lote like '', ' ',ges_pedidosdet.Lote) as LT, ". 
          "       IF ( DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%e %b %y~%Y-%m-%d') ) 
                    As FV,".
	  "       ges_pedidosdet.Serie as NS, ".
	  "       ges_pedidosdet.IdPedidoDet, ".
	  "       ges_pedidosdet.IdPedido, ".
          "       ges_productos.VentaMenudeo, ".
          "       ges_contenedores.Contenedor, ".
	  "       ges_productos.UnidadesPorContenedor, ".
	  "       ges_productos.UnidadMedida ".
	  "FROM   ges_pedidosdet ".
	  "LEFT  JOIN ges_productos ON ges_pedidosdet.IdProducto = ges_productos.IdProducto ".
	  "INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	  "INNER JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla ".
	  "INNER JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor ".
	  "INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	  "INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	  "INNER JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
          "INNER JOIN ges_pedidos    ON ges_pedidosdet.IdPedido = ges_pedidos.IdPedido ".
	  "WHERE ges_pedidosdet.IdPedido IN (".$IdPedido.") ".
	  "AND   ges_productos_idioma.IdIdioma = 1 ".
	  "AND   ges_detalles.IdIdioma           = 1 ".
	  "AND   ges_modelos.IdIdioma          = 1 ".
	  "AND   ges_pedidosdet.Eliminado      = 0 ";

	$res = query($sql);
	if (!$res) return false;
	$pedidos = array();
	$t = 0;
	while($row = Row($res)){
		$nombre = "detalles_" . $t++;
		$pedidos[$nombre] = $row; 		
	}		
	return $pedidos;
}

function DetallesCompraRecibir($IdPedido,$IdAlmacen,$IdProveedor){	

         $sql = 
	   "SELECT ges_productos.Referencia,".
	   "       ges_productos.IdProducto,".
	   "       ges_productos.CodigoBarras,".
	   "       CONCAT(ges_productos_idioma.Descripcion,' ',".
	   "       ges_marcas.Marca,' ',".
	   "       ges_modelos.Color,' ',".
	   "       ges_detalles.Talla,' ',".
	   "       ges_laboratorios.NombreComercial) as Producto,".
	   "       ges_pedidosdet.Unidades as Cantidad,".
	   "       ges_pedidosdet.CostoUnidad as Costo, ".
	   "       IF(ges_almacenes.Unidades > 0,ges_almacenes.CostoUnitario,0) as CostoPromedio,".
	   "       ges_almacenes.PrecioVenta   As PVD,".
	   "       ges_almacenes.PVDDescontado As PVDDcto,".
	   "       ges_almacenes.PrecioVentaCorporativo As PVC,".
	   "       ges_almacenes.PVCDescontado As PVCDcto,".
	   "       IF ( ges_pedidosdet.Lote like '', ' ',ges_pedidosdet.Lote) as LT,".
	   "       IF ( DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%e %b %y') ) 
                    As FV,".
	   "       ges_pedidosdet.Serie as NS, ".
	   "       ges_pedidosdet.IdPedidoDet,".
	   "       ges_almacenes.StockMin, ".
	   "       PrecioVentaSource,". 
	   "       PrecioVentaCorpSource,".  
	   "       PrecioVenta,".  
	   "       PrecioVentaCorporativo,".  
	   "       PVDDescontado,".  
	   "       PVCDescontado, ". 
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_contenedores.Contenedor, ".
	   "       ges_productos.UnidadesPorContenedor, ".
	   "       ges_productos.UnidadMedida, ".
	   "       ges_productos.IdFamilia as IdFamilia, ".
	   "       ges_productos.IdSubFamilia as IdSubFamilia, ".
	   "       ges_almacenes.CostoOperativo as CostoOperativo ".
	   
	   "FROM   ges_pedidosdet ".
	   "LEFT  JOIN ges_productos ON ges_pedidosdet.IdProducto = ges_productos.IdProducto ".
	   "INNER JOIN ges_almacenes ON ges_almacenes.IdProducto  = ges_productos.IdProducto ".
	   "INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "INNER JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla ".
	   "INNER JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor ".
	   "INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	   "INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	   "INNER JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
	   "WHERE ges_pedidosdet.IdPedido IN (".$IdPedido.") ".
	   "AND   ges_almacenes.IdLocal         = '".$IdAlmacen."' ".
	   "AND   ges_productos_idioma.IdIdioma = 1 ".
	   "AND   ges_detalles.IdIdioma           = 1 ".
	   "AND   ges_modelos.IdIdioma          = 1 ".
	   "AND   ges_pedidosdet.Eliminado      = 0 ";

	 $res = query($sql);
	 if (!$res) return false;
	 $pedidos = array();
	 $t = 0;
	 while($row = Row($res)){
	   $nombre = "detalles_" . $t++;
	   $row["MUSubFamilia"] = ObtenerMUSubFamilia($row["IdProducto"],$row["IdFamilia"],$row["IdSubFamilia"]);
	   $row["COPOrigen"] = ($IdProveedor != 0)? obtenerCostoOperativoOrigen($IdProveedor,$row["IdProducto"]):0;
	   $pedidos[$nombre] = $row; 		
	 }		
	 return $pedidos;
}

function OrdenCompraPeriodo($local,$desde,$hasta,$nombre=false,$esSoloContado=false,
			    $esSoloCredito=false,$esSoloMoneda=false,$esSoloLocal=false,
			    $esSoloCompra=false,$filtrocodigo,$entrega=false,$forzaid){
         /* Clean Datos */
         $desde        = CleanRealMysql($desde);
	 $hasta        = CleanRealMysql($hasta);
	 $entrega      = CleanRealMysql($entrega);
	 $nombre       = CleanRealMysql($nombre);

	 /* Proveedor */
	 $extraNombre  = ($nombre and $nombre != '')?" AND ges_proveedores.nombreComercial LIKE '%$nombre%' ":"";
	 /*Fechas: Desde,Hasta */
	 $extraFecha   = ($filtrocodigo != '')?" AND ges_ordencompra.CodOrdenCompra = '$filtrocodigo' ":" AND date(ges_ordencompra.FechaRegistro) >= '$desde' AND date(ges_ordencompra.FechaRegistro) <= '$hasta' ";

	 $extraFecha   = ($forzaid > 0 )?" AND ges_ordencompra.IdOrdenCompra = '$forzaid' ":$extraFecha;

	 $extraFecha   = ($entrega=='true')? " AND date(ges_ordencompra.FechaPrevista) >= '$desde' AND date(ges_ordencompra.FechaPrevista) <= '$hasta' ":$extraFecha;

	 /*Moneda value: Todos,Sol,Dolar */ 
	 $extraSol     = ($esSoloMoneda=='2')?" AND ges_ordencompra.IdMoneda = 2 ":"";
	 $extraDol     = ($esSoloMoneda=='1')?" AND ges_ordencompra.IdMoneda = 1 ":"";

	 /*Credito&Contado*/  
	 $extraContado = ($esSoloContado)?"":" AND ges_ordencompra.ModoPago = 'credito'";
	 $extraCredito = ($esSoloCredito)?"":"AND ges_ordencompra.ModoPago = 'contado'";

	 /*Local*/	 
	 $extraLocal   = ($esSoloLocal)?"   AND ges_ordencompra.IdLocal = '$esSoloLocal' ":"";

	 /*Estado*/	 
	 $extraCompra  = ($esSoloCompra!='Todos')?"   AND ges_ordencompra.Estado = '$esSoloCompra' ":"";

	 $sql = "SELECT
                ges_ordencompra.CodOrdenCompra As Codigo,
                ges_locales.NombreComercial As Local,
                ges_proveedores.nombreComercial As Proveedor,
                DATE_FORMAT(ges_ordencompra.FechaRegistro, '%e %b %Y - %k:%i') As Registro,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPedido, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPedido, '%e %b %y  %k:%i') ) 
                    As Pedido,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPrevista, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPrevista, '%e %b %y~%Y-%m-%d') ) 
                    As Entrega,
                IF ( DATE_FORMAT(ges_ordencompra.FechaRecibido, '%k:%i %e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaRecibido, '%e %b %y  %k:%i') ) 
                    As Recibido,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPago, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPago, '%e %b %y~%Y-%m-%d') ) 
                    As Pago,
                ges_moneda.Simbolo,
                ges_moneda.Moneda,
                ges_ordencompra.Importe,
                ges_ordencompra.ModoPago,   
                ges_ordencompra.Estado,  
                ges_usuarios.Nombre As Usuario, 
                ges_ordencompra.IdOrdenCompra,
                ges_ordencompra.CambioMoneda,
                ges_ordencompra.IdMoneda,
                ges_ordencompra.IdProveedor,
                DATE_FORMAT(ges_ordencompra.FechaCambioMoneda, '%d/%m/%Y') as FechaCambioMoneda,
                DATE_FORMAT(ges_ordencompra.FechaPrevista, '%d/%m/%Y') as FechaPrevista,
                DATE_FORMAT(ges_ordencompra.FechaPago, '%d/%m/%Y') as FechaPago,                
                IF ( ges_ordencompra.Observaciones like '', ' ',ges_ordencompra.Observaciones) 
                     as Observaciones,
                ges_ordencompra.IdLocal 

    	  FROM  ges_ordencompra
    		LEFT JOIN ges_proveedores ON ges_ordencompra.IdProveedor = ges_proveedores.IdProveedor
                INNER JOIN ges_moneda   ON ges_ordencompra.IdMoneda  = ges_moneda.IdMoneda
                INNER JOIN ges_locales  ON ges_ordencompra.IdLocal   = ges_locales.IdLocal
                INNER JOIN ges_usuarios ON ges_ordencompra.IdUsuario = ges_usuarios.IdUsuario                
          WHERE ges_ordencompra.Eliminado = 0 "."
          $extraNombre 
          $extraFecha 
          $extraSol 
          $extraDol 
          $extraContado 
          $extraCredito 
          $extraLocal 
          $extraCompra".
	   " ORDER BY ges_ordencompra.IdOrdenCompra DESC";  
	 $res = query($sql);

	 if (!$res) return false;

	 $OrdenCompra = array();
	 $t = 0;

	 while($row = Row($res)){
	   $nombre = "Orden_" . $t++;
	   $OCPago = "0";

	   if($row["Estado"] == 'Pedido' || $row["Estado"] == 'Recibido')
	     $OCPago = checkOrdenCompraPago($row["IdOrdenCompra"]);

	   $row["OrdenCompraPago"] = $OCPago;
	   $OrdenCompra[$nombre] = $row;

	 }	

	 return $OrdenCompra;
}

function CompraPeriodo($local,$desde,$hasta,$emision=false,$nombre=false,
		       $esSoloMoneda=false,$esSoloLocal=false,
		       $esSoloCompra=false,$forzaid,
		       $esSoloDocumento=false,$esRecibir,$esSoloPagos=false,$esPagos=false,
		       $esFormaPago){

  $Moneda    = getSesionDato("Moneda");

  // Clean Datos 
  $desde        = CleanRealMysql($desde);
  $hasta        = CleanRealMysql($hasta);
  $nombre       = CleanRealMysql($nombre);
  $emision      = CleanRealMysql($emision);

  //Documentos
  $extraPagos   = ($esPagos)?" AND ges_comprobantesprov.TipoComprobante != 'Albaran' ":"";
  $extraEstDoc  = ($esPagos)?" AND ges_comprobantesprov.EstadoDocumento != 'Borrador' ".
                             " AND ges_comprobantesprov.EstadoDocumento != 'Cancelada' ":"";

  // Proveedor 
  $extraNombre  = ($nombre and $nombre != '')?" AND ges_proveedores.nombreComercial LIKE '%$nombre%' ":"";
  //Estado
  $extraEstado  = ($esRecibir)?" AND ges_comprobantesprov.EstadoDocumento ='Borrador' ":"";

  //Fechas: Desde,Hasta 
  $extraFecha   = ($forzaid != 'false')?" AND ges_comprobantesprov.Codigo LIKE '%$forzaid%' ":" AND date(ges_comprobantesprov.FechaRegistro) >= '$desde' AND date(ges_comprobantesprov.FechaRegistro) <= '$hasta' ";
  $extraFecha   = ($emision=='true')? " AND date(ges_comprobantesprov.FechaFacturacion) >= '$desde' AND date(ges_comprobantesprov.FechaFacturacion) <= '$hasta' ":$extraFecha;

  $extraFecha   = ($emision=='Pago')? " AND date(ges_comprobantesprov.FechaPago) >= '$desde' AND date(ges_comprobantesprov.FechaPago) <= '$hasta' ":$extraFecha;
  
  //Moneda value: Todos,Sol,Dolar
  $extraSol     = ($esSoloMoneda==2)?" AND ges_pedidos.IdMoneda = 2 ":"";
  $extraDol     = ($esSoloMoneda==1)?" AND ges_pedidos.IdMoneda = 1 ":"";
  $extraMoneda  = ($esSoloMoneda=='todo1')? "ges_pedidos.CambioMoneda":"1";
  $Simbolo      = ($esSoloMoneda=='todo1')? "CONCAT('".$Moneda[1]['S']."') as Simbolo,":"ges_moneda.Simbolo,";

  //Local
  $extraLocal   = ($esSoloLocal)?" AND ges_pedidos.IdLocal = '$esSoloLocal' ":"";

  //Estado
  $extraCompra  = ($esSoloCompra!='Todos')?" AND ges_comprobantesprov.EstadoDocumento = '$esSoloCompra' ":"";

  $extraEstPagos= ($esSoloPagos!='Todos')?" AND ges_comprobantesprov.EstadoPago = '$esSoloPagos' ":"";
  $extraFormaPago = ($esFormaPago!='Todos')?" AND ges_comprobantesprov.ModoPago = '$esFormaPago' ":"";

  //TipoComprobantes
  $extraDoc     = ($esSoloDocumento!='Todos')?" AND ges_comprobantesprov.TipoComprobante = '$esSoloDocumento' ":"";

  $sql = "SELECT
                ges_locales.NombreComercial As Almacen,
                ges_proveedores.nombreComercial As Proveedor,
                ges_comprobantesprov.Codigo,
                ges_comprobantesprov.TipoComprobante As Documento,                
                DATE_FORMAT(ges_comprobantesprov.FechaRegistro, '%e %b %y  %k:%i') As Registro,
                IF ( DATE_FORMAT(ges_comprobantesprov.FechaFacturacion, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_comprobantesprov.FechaFacturacion, '%e %b %y~%Y-%m-%d') ) 
                    As Emision,
                IF ( DATE_FORMAT(ges_comprobantesprov.FechaPago, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_comprobantesprov.FechaPago, '%e %b %y~%Y-%m-%d') ) 
                    As Pago,
                ges_pedidos.Impuesto,
                ROUND(ges_comprobantesprov.TotalImporte*ges_pedidos.Percepcion/100,2) as Percepcion,
                $Simbolo
                (ges_comprobantesprov.ImporteBase*$extraMoneda)       as ImporteBase,
                (ges_comprobantesprov.ImporteImpuesto*$extraMoneda)   as ImporteImpuesto,
                (ges_comprobantesprov.TotalImporte*$extraMoneda)      as TotalImporte,
                (ges_comprobantesprov.ImportePendiente*$extraMoneda)  as ImportePendiente,
                (ges_comprobantesprov.ImportePercepcion*$extraMoneda) as ImportePercepcion,
                ges_comprobantesprov.ModoPago,
                ges_comprobantesprov.EstadoDocumento,
                ges_usuarios.Nombre As Usuario,
                ges_pedidos.CambioMoneda,
                ges_pedidos.FechaCambioMoneda,
                ges_comprobantesprov.IdPedidosDetalle,
                ges_pedidos.IdPedido,
                ges_comprobantesprov.IdComprobanteProv,
                ges_pedidos.IdMoneda,
                ges_pedidos.IdOrdenCompra,
                IF ( ges_comprobantesprov.Observaciones like '', ' ',ges_comprobantesprov.Observaciones) as Observaciones,
                ges_pedidos.IdLocal,
                ges_pedidos.Impuesto As ImpuestoVenta,
	        ges_pedidos.IdAlmacenRecepcion,
                ges_comprobantesprov.IdProveedor,
                ges_comprobantesprov.EstadoPago,
                ges_comprobantesprov.IdMotivoAlbaran,
                (ges_comprobantesprov.ImporteFlete*$extraMoneda) as ImporteFlete,
                (ges_comprobantesprov.ImportePago*$extraMoneda)  as ImportePago
         FROM  ges_comprobantesprov
         LEFT JOIN ges_proveedores ON ges_comprobantesprov.IdProveedor = ges_proveedores.IdProveedor
         INNER JOIN ges_pedidos    ON ges_comprobantesprov.IdPedido = ges_pedidos.IdPedido
         INNER JOIN ges_moneda     ON ges_pedidos.IdMoneda  = ges_moneda.IdMoneda
         INNER JOIN ges_locales    ON ges_pedidos.IdAlmacenRecepcion   = ges_locales.IdLocal
         INNER JOIN ges_usuarios   ON ges_comprobantesprov.IdUsuario = ges_usuarios.IdUsuario
                
          WHERE ges_comprobantesprov.Eliminado = 0 "."
          $extraNombre 
          $extraPagos
          $extraEstDoc
          $extraEstPagos
          $extraFecha 
          $extraEstado
          $extraDoc
          $extraSol 
          $extraDol 
          $extraFormaPago
          $extraLocal 
          $extraCompra".
          "ORDER BY ges_comprobantesprov.IdComprobanteProv DESC";  
 	$res = query($sql);
	if (!$res) return false;
	$Compra = array();
	$t = 0;

	while($row = Row($res)){
	  $row["TipoDoc"]   = $row["Documento"];
	  $row["Proveedor"] = obtenerProveedorDocumento($row); 
	  $row["Documento"] = obtenerMotivoDocumento($row);
	  $row["EstadoPago"] = ($row["ModoPago"] == "Credito" && $row["EstadoDocumento"] != "Borrador")? checkFechaPago($row):$row["EstadoPago"];

	  $nombre           = "Orden_" . $t++;
	  $Compra[$nombre]  = $row; 	

	  if( $row["EstadoDocumento"] == "Pendiente" && $row["TotalImporte"] == 0) 
	    setImporteCero2Estado($row);
	}	
	return $Compra;
}

function obtenerProveedorDocumento($row){

        $Proveedor = ( $row["Documento"] == "AlbaranInt" )? 
	             obtenerProveedorInterno($row["IdProveedor"]): 
	             $row["Proveedor"];
	return $Proveedor;
}

function obtenerMotivoDocumento($row){

        $Documento = ( $row["IdMotivoAlbaran"] != "0" )?
	             $row["Documento"]." - ".getmotivoAlbaran($row["IdMotivoAlbaran"],false):
                     $row["Documento"];
	return $Documento;
}

function sModificarOrdenCompra($xid,$campoxdato,$xdet=false,$xhead=false){

        $Tb         = ($xhead)?'ges_ordencompradet':'ges_ordencompra';
	$IdKey      = ($xdet)?'IdOrdenCompraDet':'IdOrdenCompra';
	$Id         = CleanID($xid);
	$KeysValue  = $campoxdato;
	$sql   =
	  " update ".$Tb.
	  " set    ".$KeysValue." ".
	  " where  ".$IdKey." = ".$Id;	
	return query($sql); 
}

function sModificarCompra($xid,$campoxdato,$xdet=false,$xhead=false){
  
         $Tb         = ($xhead)?'ges_pedidosdet':'ges_comprobantesprov';
	 $IdKey      = ($xdet)?'IdPedidoDet':'IdPedido';
	 $Id         = CleanID($xid);
	 $KeysValue  = $campoxdato;
	 $sql   =
	   " update ".$Tb.
	   " set    ".$KeysValue." ".
	   " where  ".$IdKey." = ".$Id;	
	 return query($sql); 

}
function sModificarCompra2EliminarNS($xid){

        $sql = 
 	  " select IdPedidoDet,IdProducto ".
	  " from   ges_pedidosdet ".
	  " where  IdPedido  = '".$xid."'".
	  " and    Serie     IN (1,2)". 
	  " and    Eliminado = '0'"; 
	$res = query($sql);
	if (!$res) return false;

	while( $row = Row($res) ){
	  $sql= 
	    " update ges_productos_series ".
	    " set    Eliminado = '1' ".
	    " where  DocumentoEntrada   = '".$row["IdPedidoDet"]."'".
	    " and    IdProducto         = '".$row["IdProducto"]."'";
 	  query($sql);
	}
}
function sModificarVenta($xid,$campoxdato,$xdet=false,$xhead=false){
  
         $Tb         = ($xhead)?'ges_comprobantesdet':'ges_comprobantes';
	 $IdKey      = ($xdet)?'IdComprobanteDet':'IdComprobante';
	 $Id         = CleanID($xid);
	 $KeysValue  = $campoxdato;
	 $sql   =
	   " update ".$Tb.
	   " set    ".$KeysValue." ".
	   " where  ".$IdKey." = ".$Id;	
	 return query($sql); 
}

function sModificarPedido($xid,$campoxdato){
	 $xid = CleanID($xid);
	 $sql =
	   " update ges_pedidos ".
	   " set    ".$campoxdato." ".
	   " where  IdPedido = ".$xid;	
	 return query($sql); 
}

function EditarOrdenCompra($xid,$tdoc,$esclon){

         //Inicio
         ResetearCarritoCompras();
	 
	 //Header Orden Compra
	 $estado      = ($tdoc=='O')?'Borrador':'Pedido';
	 $detadoc     = getSesionDato('detadoc');  
	 $datos       = OrdenCompraPeriodo('','','',false,true,true,false,false,'Todos','','',$xid);
	 $datostrj    = getTrabajosSubsidiario($xid);
	 $Moneda      = getSesionDato("Moneda");
	 $detadoc[0]  = $tdoc;//Documento Orden Compra
	 $detadoc[1]  = $datos["Orden_0"]["IdProveedor"];
	 $detadoc[2]  = $datos["Orden_0"]["Proveedor"];
	 $detadoc[3]  = ($tdoc=='O')? $datos["Orden_0"]["Codigo"]:'';
	 $detadoc[4]  = $datos["Orden_0"]["FechaPrevista"];
	 $detadoc[5]  = $datos["Orden_0"]["IdMoneda"];
	 $detadoc[6]  = $datos["Orden_0"]["CambioMoneda"];
	 $detadoc[7]  = $datos["Orden_0"]["FechaCambioMoneda"];
	 $detadoc[8]  = $datos["Orden_0"]["FechaPago"];
	 $detadoc[9]  = $datostrj["IdSubsidiario"];
	 $detadoc[10] = $datostrj["NombreComercial"];
	 $detadoc[11] = ( !$esclon )? $datos["Orden_0"]["IdOrdenCompra"]:0;
	 $detadoc[12] = ( $datos["Orden_0"]["Observaciones"]==' ')?'':$datos["Orden_0"]["Observaciones"];
	 $aCredito    = ($datos["Orden_0"]["ModoPago"]=='Credito')?true:false;

	 //Carga datos
	 setSesionDato('detadoc',$detadoc);
	 setSesionDato('aCredito',$aCredito);
	 setSesionDato('incImpuestoDet',true);

	 //Detalle Orden Compra
	 $detalle = DetallesOrdenCompra($xid);
	 foreach ( $detalle as $key => $values )
	   {
	     $detlinea = array();
	     if ($key and !is_numeric($key))
	       if (is_array($values))
		 {
		   //Filtra Datos
		   $ln=0;
		   foreach ( $values as $vkey => $val )
		     {
		       if ($vkey and !is_numeric($vkey)){
			 $detlinea[$ln]= $val;  
			 $ln++;
		       }
		     }

		   //Ordena Datos
		   $id          = $detlinea[1];
		   $costo       = $detlinea[5];
		   $unidades    = $detlinea[4];
		   //Carga datos
		   $costes      = getSesionDato("CarroCostesCompra");
		   $costes[$id] = $costo;
		   setSesionDato("CarroCostesCompra",$costes);
		   AgnadirCarritoCompras($id,$unidades);

		 }
	   }
}

function sConsolidarOrdenesCompra($xid,$xdato){

           $sql = 
	     " select IdOrdenCompra ".
	     " from   ges_ordencompra ".
	     " where  CodOrdenCompra  IN (".$xdato.") ".
	     " and    Eliminado = '0'"; 
	   $res = query($sql);
	   if (!$res) return false;
	   while($row = Row($res)){

	     if($row["IdOrdenCompra"] != $xid )
	       {
		 sModificarOrdenCompra($row["IdOrdenCompra"],
				       'Eliminado = 1',
				       false,
				       false);
		 sModificarOrdenCompra($row["IdOrdenCompra"],
				       'IdOrdenCompra = '.$xid,
				       false,
				       true);
	       }
	   }
	   //Consolida detalle Importe
	   return ConsolidaDetalleOrdenCompra($xid);
}

function sConsolidaCompras($xid,$xdato){

           $sql = 
	     " select IdPedido ".
	     " from   ges_pedidos ".
	     " where  IdPedido IN (".$xdato.") ".
	     " and    Eliminado = '0'"; 
	   $res = query($sql);
	   if (!$res) return false;
	   while($row = Row($res)){

	     if($row["IdPedido"] != $xid )
	       {
		 sModificarCompra($row["IdPedido"],
				  'Eliminado = 1',
				  false,
				  false);
		 sModificarCompra($row["IdPedido"],
				  'IdPedido = '.$xid,
				  false,
				  true);
	       }
	   }
	   //Consolida detalle Importe
	   return ConsolidaDetalleCompra($xid,false);
}

function sFacturarCompra($xid,$xdato){
               global     $UltimaInsercion;
	       $IdUsuario = getSesionDato("IdUsuario");  
	       $IdLocal   = getSesionDato("IdTienda");
	       $aidx      = explode(",", $xdato); 
	       $Codigo    = $aidx[0];unset($aidx[0]);
	       $xdato     = implode(",", $aidx);

	       $sql = 
		 " select ges_pedidos.*,ges_comprobantesprov.* ".
		 " from   ges_comprobantesprov ".
		 " inner  join ges_pedidos     ".
		 " on     ges_comprobantesprov.IdPedido  = ges_pedidos.IdPedido ".
		 " where  ges_comprobantesprov.IdPedido  = ".$xid.
		 " and    ges_comprobantesprov.Eliminado = '0'"; 
	       $res = query($sql);
	       if (!$res) return false;
	       $row = Row($res);

	       //Registra Pedido
	       $listpdKeys    = "IdLocal";
	       $listpdValues  = " '".$row["IdLocal"]."'";
	       $listpdKeys   .= ",IdAlmacenRecepcion";
	       $listpdValues .= ",'".$row["IdAlmacenRecepcion"]."'";
	       $listpdKeys   .= ",IdUsuario";
	       $listpdValues .= ",'".$IdUsuario."'";
	       $listpdKeys   .= ",IdMoneda";
	       $listpdValues .= ",'".$row["IdMoneda"]."'";
	       $listpdKeys   .= ",IncluyeImpuesto";
	       $listpdValues .= ",'".$row["IncluyeImpuesto"]."'";
	       $listpdKeys   .= ",Impuesto";
	       $listpdValues .= ",'".$row["Impuesto"]."'";
	       $listpdKeys   .= ",Percepcion";
	       $listpdValues .= ",'".$row["Percepcion"]."'";
	       $listpdKeys   .= ",CambioMoneda";
	       $listpdValues .= ",'".$row["CambioMoneda"]."'";
	       $listpdKeys   .= ",FechaCambioMoneda";
	       $listpdValues .= ",'".$row["FechaCambioMoneda"]."'";
	       $listpdKeys   .= ",Status";
	       $listpdValues .= ",'".$row["Status"]."'";
	       $listpdKeys   .= ",FechaPeticion";
	       $listpdValues .= ",'".$row["FechaPeticion"]."'";
	       $listpdKeys   .= ",FechaRecepcion";
	       $listpdValues .= ",'".$row["FechaRecepcion"]."'";
	       $listpdKeys   .= ",TipoOperacion";
	       $listpdValues .= ",'".$row["TipoOperacion"]."'";

	       $sql = "insert into ges_pedidos ( ".$listpdKeys." ) ".
		 "value ( ".$listpdValues." )";
	       $res = query($sql);

	       //Registra Factura  
	       $IdPedido      = $UltimaInsercion;
	       $listcpKeys    = "IdUsuario";
	       $listcpValues  = " '".$IdUsuario."'";
	       $listcpKeys   .= ",IdProveedor";
	       $listcpValues .= ",'".$row["IdProveedor"]."'";
	       $listcpKeys   .= ",IdPedido";
	       $listcpValues .= ",'".$IdPedido."'";
	       $listcpKeys   .= ",ModoPago";
	       $listcpValues .= ",'".$row["ModoPago"]."'";
	       $listcpKeys   .= ",TipoComprobante";
	       $listcpValues .= ",'Factura'";
	       $listcpKeys   .= ",IdPedidosDetalle";
	       $listcpValues .= ",'".$xdato."'";
	       $listcpKeys   .= ",Codigo";
	       $listcpValues .= ",'".$Codigo."'";
	       $listcpKeys   .= ",CodigoAlbaran";
	       $listcpValues .= ",'".getCodigosAlbaranes($xdato)."'";
	       $listcpKeys   .= ",EstadoDocumento";
	       $listcpValues .= ",'Pendiente'";

	       $sql = 
		 "insert into ges_comprobantesprov ( ".$listcpKeys." ) ".
		 "values ( ".$listcpValues." )";
	       query($sql);

	       //Actualiza Estado Albaranes
	       $aidx  = explode(",", $xdato); 
	       foreach ( $aidx as $key => $value ){
		 sModificarCompra($value,
				  "EstadoDocumento = 'Confirmado'",
				  false,
				  false);
	       }	
	       //Actualiza Importes
	       ConsolidaDetalleCompra($IdPedido,$xdato);
}

function getCodigosAlbaranes($xdato){
  
  $xsql = "SELECT Codigo FROM ges_comprobantesprov WHERE IdPedido in ( $xdato ) ";
  $xres = query($xsql);
  $srt      = "";
  $xcodigos = "";

  while( $xrow = Row($xres) ){
    $xcodigos .= $srt.$xrow["Codigo"];
    $srt       = ",";
  }
  return $xcodigos;
}


function ConsolidaDetalleOrdenCompra($xid){
               $sql = 
		 " select round(sum(Costo*Unidades),2) as Total".
		 " from   ges_ordencompradet ".
		 " where  IdOrdenCompra = ".$xid.
		 " and    Eliminado     = '0'";
	       
	       $res = query($sql);
	       $row = Row($res);
	       return sModificarOrdenCompra($xid,
					    'Importe = '.$row["Total"],false,false);
}

function ConsolidaDetalleCompra($xid,$xdato=false){
  
               $xdato = ($xdato)?$xdato:$xid;
	       //TotalImpuesto
	       $sql = 
		 " select round(sum(Importe),2) as TotalImporte".
		 " from   ges_pedidosdet ".
		 " where  IdPedido  in (".$xdato.")".
		 " and    Eliminado = '0'";
	       $res          = query($sql);
	       $row          = Row($res);
	       $TotalImporte = ($row["TotalImporte"]!='')?$row["TotalImporte"]:0;
	       //Impuesto
	       $sql = 
		 " select Impuesto".
		 " from   ges_pedidos ".
		 " where  IdPedido  = ".$xid.
		 " and    Eliminado = '0'";
	       $res             = query($sql);
	       $row             = Row($res);
	       $Impuesto        = $row["Impuesto"];

	       $ImporteBase     = round( ($TotalImporte*100/($Impuesto+100))*100 )/100;
	       $ImporteImpuesto = $TotalImporte-$ImporteBase;

	       //Actualiza importes
	       sModificarCompra($xid,
				'TotalImporte      = '.$TotalImporte.
				',ImportePendiente = '.$TotalImporte.
				',ImportePago      = '.$TotalImporte.
				',ImporteBase      = '.$ImporteBase.
				',ImporteImpuesto  = '.$ImporteImpuesto,
				false,
				false);
	       return 1;
}

function ConsolidaDetalleVenta($xid,$xdato=false){
  
               $xdato = ($xdato)?$xdato:$xid;
	       //TotalImpuesto
	       $sql = 
		 " select round(sum(Importe),2) as TotalImporte".
		 " from   ges_comprobantesdet ".
		 " where  IdComprobante  in (".$xdato.")".
		 " and    Eliminado = '0'";
	       $res          = query($sql);
	       $row          = Row($res);
	       $TotalImporte = ($row["TotalImporte"]!='')?$row["TotalImporte"]:0;
	       //Impuesto
	       $sql = 
		 " select Impuesto".
		 " from   ges_comprobantes ".
		 " where  IdComprobante  = ".$xid.
		 " and    Eliminado = '0'";
	       $res             = query($sql);
	       $row             = Row($res);
	       $Impuesto        = $row["Impuesto"];

	       $ImporteBase     = round( ($TotalImporte*100/($Impuesto+100))*100 )/100;
	       $ImporteImpuesto = $TotalImporte-$ImporteBase;

	       //Actualiza importes
	       sModificarVenta($xid,
				'TotalImporte     = '.$TotalImporte.
			 	',ImporteNeto     = '.$ImporteBase.
				',ImporteImpuesto = '.$ImporteImpuesto,
				false,
				false);
	       return 1;
}

function checkOrdenCompraPago($IdOrdenCompra){
  $sql = "SELECT group_concat(CONCAT(DATE_FORMAT(FechaOperacion,'%d/%m/%Y %H:%i'),'-',Importe)) as operacion, SUM(importe) as totalimporte ".
         "FROM   ges_pagosprovdoc ".
         "WHERE  IdOrdenCompra = '$IdOrdenCompra' ".
         "AND    Eliminado = 0 ";
  $row = queryrow($sql);
  if($row["operacion"] == '' || !$row) return "0";
  return $row["operacion"]."~~".$row["totalimporte"];
}

function getLoteFromIdProductoVenta($IdPedidoDet, $IdProducto){
  $sql = "SELECT Lote FROM ges_pedidosdet ".
         "WHERE  IdPedidoDet = '$IdPedidoDet' ".
         "AND    IdProducto  = '$IdProducto'";
  $row = queryrow($sql);
  return $row["Lote"];
}

function getVencimientoFromIdProductoVenta($IdPedidoDet, $IdProducto){
  $sql = "SELECT FechaVencimiento FROM ges_pedidosdet ".
         "WHERE  IdPedidoDet = '$IdPedidoDet' ".
         "AND    IdProducto  = '$IdProducto'";
  $row = queryrow($sql);
  return $row["FechaVencimiento"];
}

function actualizarEstadoDocumentoPedido($xid){

         $sql = 
	   " select IdMotivoAlbaran,TipoComprobante ".
	   " from   ges_comprobantesprov ".
	   " where  IdPedido = '$xid' ";
	 $row = queryrow($sql);
	 
	 $esAlbaranInt    = ( $row["TipoComprobante"] == 'AlbaranInt' )? true:false;
	 $esConsignacion  = ( $row["IdMotivoAlbaran"] == '2'          )? true:false;//2:Consignacion
	 $EstadoDocumento = ( $esAlbaranInt   )? "Confirmado" : "Pendiente";
	 $EstadoDocumento = ( $esConsignacion )? "Pendiente"  : $EstadoDocumento;

	 $campoxdato = " EstadoDocumento = '".$EstadoDocumento."'";
	 sModificarCompra( $xid,$campoxdato,false,false );
}

function ValidarTrasladoDetalle($Origen){

	$marcadotrans  = getSesionDato("CarritoTrans");
	$Trans         = getSesionDato("CarritoMover");		
	$aSeries       = getSesionDato("CarritoMoverSeries");
	$articulo      = new articulo;
	$cbsrt         = Array();

	foreach ($marcadotrans as $idarticulo ){

	  $oProducto  = new producto();

	  $articulo->Load($idarticulo);
	  $oProducto->Load($articulo->get("IdProducto"));

	  $idproducto = $articulo->get("IdProducto");
	  $mSeleccion = $Trans[$idarticulo];
	  $aSeleccion = explode("~", $mSeleccion);
	  $esSerie    = ( $aSeries[$idarticulo] )? true:false;
	  $rkardex    = getResumenKardex2Producto($idproducto,$Origen);//13:4~15:5~13:4

	  foreach ( $aSeleccion as $Pedido )
	    {
	      $aPedido       = explode(":", $Pedido);
	      $idpedidodet   = $aPedido[0];//Kardex
	      $unidades      = $aPedido[1];
	      
	      //Serie...
              $mSeries       = ( $esSerie )? $aSeries[$idarticulo]:'';
	      $seriesxPedido = explode("~", $mSeries);

	      foreach ($seriesxPedido as $nsPedido )
		{
		  $aPedido = explode(":", $nsPedido);

		  if( $idpedidodet == $aPedido[0] )
		    $xnseries = str_replace(",",";",$aPedido[1]);		  
		}
	      $xnseries = ($esSerie)? $xnseries:false;
	      $srt      = existeUnidAlmacen($unidades,$idproducto,$idpedidodet,
					    $xnseries,$idproducto,$Origen,$rkardex);

	      if( $srt != 0 ) array_push($cbsrt,$srt);
	    }
	  
	}	

	//idproducto:idpedidodet-mensajebug:unidades:unidadesalmacen:series;

	if(count($cbsrt)>0)
	  {
	    $mm = '';

	    foreach ($cbsrt as $pedidodet)
	      {
		$apedidodet  = explode(":", $pedidodet);
		$idproducto  = $apedidodet[0];
		$idpedidodet = $apedidodet[1]; 
		$unidades    = $apedidodet[2]; 
		$unidadesalm = $apedidodet[3]; 
		$xseries     = $apedidodet[4]; 
		$producto    = getDatosProductosExtra($idproducto,'nombrecb');
		$id          = getIdFromAlmacen($idproducto,$Origen);
		
		$pedido   = 'Pedido Detalle: <br> ['.$idpedidodet.']<br>';
		$stock    = ( $unidades > $unidadesalm )? 'Unidades Seleccionadas:<br>Excede el stock actual en Almacén <br>':'';
		$series   = ( $xseries != "0" )? "N/S: ".$xseries.' no diponibles <br>':''; 
		$mm      .= "Producto:<br>".$producto."<br> ".$pedido.$stock.$series."<br>";
	
		QuitarDeCarritoTraspaso($id);
		QuitarDeCarritoTraspasoSeries($id);
	      }

	    echo gas("aviso",_("*** Error en Kardex ***<br>". $mm ));

	    return true;//Suspende...
	  }
	return false;//Continua... 
}

function ValidarAjusteExistenciasDetalle($Ajustes,$Series,$idproducto,$esSerie,$Origen){

	 $aAjustes = explode("~",$Ajustes);
	 $nAjustes = count($aAjustes);
	 $aSeries  = explode("~",$Series);
	 //$rkardex  = getResumenKardex2Producto($idproducto,$Origen);//13:4~15:5~13:4
	 $rkardex  = getResumenKardex4Ajuste($idproducto,$Origen);//13:4~15:5~13:4
	 $cbsrt    = Array();

	 for( $i=0; $i < $nAjustes ; $i++)
	   {
	     $Ajuste      = explode(":",$aAjustes[$i]);
	     $idpedidodet = $Ajuste[0];
	     $unidades    = $Ajuste[1];
	     $xnseries    = '';

	     if ($esSerie)
	       {
		 foreach ($aSeries as $nsPedido )
		   {
		     $aPedido = explode(":", $nsPedido);
		     
		     if( $idpedidodet == $aPedido[0] )
		       $xnseries = $aPedido[1];		  
		   }
	       }

	     $xnseries = ($esSerie)? false:$xnseries;		    
	     $srt      = existeUnidAlmacen($unidades,$idproducto,$idpedidodet,
					   $xnseries,$idproducto,$Origen,$rkardex);

	     if( $srt != 0 ) array_push($cbsrt,$srt);

	   }

	 if(count($cbsrt)>0)
	  {
	    $mm = '';

	    foreach ($cbsrt as $pedidodet)
	      {
		$apedidodet  = explode(":", $pedidodet);
		$idproducto  = $apedidodet[0];
		$idpedidodet = $apedidodet[1]; 
		$unidades    = $apedidodet[2]; 
		$unidadesalm = $apedidodet[3]; 
		$xseries     = $apedidodet[4]; 
		$producto    = getDatosProductosExtra($idproducto,'nombrecb');
		$id          = getIdFromAlmacen($idproducto,$Origen);
		$esStock     = ( $unidades > $unidadesalm )? true:false;
 
		$pedido   = 'Pedido Detalle :  '.$idpedidodet;
		$stock    = ( $esStock )? ' \n Unidades: Excede al stock actual en Almacén':'';
		$series   = ( $xseries != "0" )? " \n N/S: ".$xseries.' no diponibles ':''; 
		$mm      .= "\n\n Producto : ".$producto."\n ".$pedido.$stock.$series;
	
	      }
	    echo _(" *** Error en Kardex ***". $mm );
	    return true;
	  }
	 return false;
}

function ObtenerMUSubFamilia($IdProducto,$IdFamilia,$IdSubFamilia){

$sql = "SELECT MargenUtilidadVD, MargenUtilidadVC, Descuento ".
         "FROM   ges_subfamilias ".
         "WHERE ges_subfamilias.IdFamilia = '$IdFamilia' ".
         "AND   ges_subfamilias.IdSubFamilia = '$IdSubFamilia' ";
  
  $res = query($sql);

  $row = Row($res);
  return $row["MargenUtilidadVD"]."~~".$row["MargenUtilidadVC"]."~~".$row["Descuento"];
  
}

function obtenerCostoOperativoOrigen($IdProveedor,$IdProducto){
  $sql = "SELECT CostoOperativo 
          FROM ges_almacenes 
          WHERE IdLocal = $IdProveedor 
          AND IdProducto = $IdProducto ";

  $row = queryrow($sql);

  return $row["CostoOperativo"];
}

function obtenerCostoLocalCentral($IdProveedor,$IdProducto){
  $sql = "SELECT CostoUnitario 
          FROM ges_almacenes 
          WHERE IdLocal = $IdProveedor 
          AND IdProducto = $IdProducto ";

  $row = queryrow($sql);

  return $row["CostoUnitario"];
}

function verificarEstadoDocumento($IdPedido){
  $sql = "SELECT EstadoDocumento FROM ges_comprobantesprov WHERE IdPedido = '$IdPedido'";
  $row = queryrow($sql);

  if($row["EstadoDocumento"] != 'Borrador')
    return true;
  else
    return false;
}

function crearProforma($IdOrdenCompra,$IdCliente,$CodigoOC){
  global $UltimaInsercion;

  $IdLocal      = getSesionDato("IdTienda");
  $IdUsuario    = CleanID(getSesionDato("IdUsuario"));
  $TipoPresupuesto = 'Proforma';
  $TipoVenta    = (getSesionDato("TipoVentaTPV") != 1)? getSesionDato("TipoVentaTPV"):'VD';
  $Impuesto     = getSesionDato("IGV");

  $mensaje      = 'Orden Compra cod -'.$CodigoOC.'-';
  $vigencia     = getSesionDato("VigenciaPresupuesto");

  // Serie presupuesto
  $sql = "SELECT Serie FROM ges_presupuestos ".
         "WHERE IdLocal = $IdLocal AND Eliminado = 0 ORDER BY IdPresupuesto DESC LIMIT 1 ";
  $row = queryrow($sql);
  $sreDocumento = ($row["Serie"])? $row["Serie"]:1;

  // Nro Presupuesto
  $sql = "SELECT MAX(NPresupuesto) as NPresupuesto FROM ges_presupuestos ".
         "WHERE IdLocal = $IdLocal AND Serie = '$sreDocumento' AND Eliminado = 0 LIMIT 1 ";
  $row = queryrow($sql);
  $NroDocumento = ($row["NPresupuesto"])? $row["NPresupuesto"]+1:1;

  //Tipo Cliente
  $sql = "SELECT TipoCliente FROM ges_clientes ".
         "WHERE IdCliente = $IdCliente AND Eliminado = 0 LIMIT 1 ";
  $row = queryrow($sql);
  $TipoCliente = $row["TipoCliente"];
  //$TipoVenta = ($TipoCliente == 'Empresa' || $TipoCliente == 'Gobierno')? 'VC':$TipoVenta;

  $esquema = 
    "IdLocal, IdUsuario, NPresupuesto,TipoPresupuesto,".
    "TipoVentaOperacion,FechaPresupuesto,".
    "Impuesto, Status,IdCliente,".
    "Observaciones,VigenciaPresupuesto,Serie";
  
  $datos = 
    "'$IdLocal','$IdUsuario','$NroDocumento','$TipoPresupuesto',".
    "'$TipoVenta',NOW(),'$Impuesto','Pendiente','$IdCliente',".
    "'$mensaje','$vigencia','$sreDocumento'";
  
  $sql = "INSERT INTO ges_presupuestos (".$esquema.") VALUES (".$datos.")";
  query($sql);
  $IdPresupuesto = $UltimaInsercion;

  $sql = "SELECT IdProducto, Unidades, Costo FROM ges_ordencompradet WHERE IdOrdenCompra = $IdOrdenCompra ";
  $res = query($sql);
  $xvalue = "";
  $TotalImporte = 0;

  while($row = Row($res)){
    $IdProducto = $row["IdProducto"];
    $Unidades   = $row["Unidades"];

    $sql = "SELECT PrecioVenta, PrecioVentaCorporativo,CodigoBarras,Referencia ".
           " FROM ges_almacenes ".
           "INNER JOIN ges_productos ON ges_almacenes.IdProducto = ges_productos.IdProducto ".
           " WHERE ges_almacenes.IdProducto = $IdProducto AND IdLocal = $IdLocal";
    $row = queryrow($sql);


    $Referencia   = $row["Referencia"];
    $CodigoBarras = $row["CodigoBarras"];
    $Precio       = $row["PrecioVenta"];
    $PrecioCorp   = $row["PrecioVentaCorporativo"];
    $Importe      = ($TipoVenta == 'VD')? round($Unidades*$Precio,2):round($Unidades*$PrecioCorp,2);

    $TotalImporte = $TotalImporte + $Importe;

    $xvalue .= "(NULL, ".$IdPresupuesto.",".$IdProducto.", ".$Unidades.",'".$CodigoBarras."','".$Referencia."',".$Precio.",".$Importe." ),";
    
  }

  $xvalue = substr($xvalue,0,-1);
  $sql   = "INSERT INTO ges_presupuestosdet (idpresupuestodet, idpresupuesto, idproducto, cantidad,codigobarras,referencia,precio,importe) VALUES ".$xvalue;
  query($sql);

  $ImporteNeto = ROUND((100*$TotalImporte/(100+$Impuesto)),2);
  $ImporteImpuesto = $TotalImporte - $ImporteNeto;

  $sql = "UPDATE ges_presupuestos SET ImporteNeto = $ImporteNeto, ".
         "ImporteImpuesto = $ImporteImpuesto, TotalImporte = $TotalImporte ".
         "WHERE IdPresupuesto = $IdPresupuesto ";

  query($sql);

  return "~".$sreDocumento."-".$NroDocumento;
  
}
?>
