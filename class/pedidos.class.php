<?php

define("PEDIDO_PETICION",1);
define("PEDIDO_PEDIDO",2);
define("PEDIDO_RECIBIDO",3);

class pedido extends Cursor {
	
	var $filas;
	var $filascoste;
	var $filasproveedor;
	var $filaslaboratorio;
	
	var $IdPedido;
	
	function pedido(){
		return $this;	
	}
	
	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_pedidos", "IdPedido", $id);
		return $this->getResult();
	}
			
	function AgnadirProducto($id,$cantidad,$coste,$idproveedor,$idlaboratorio) {
		$this->filas[$id] = $cantidad;		
		$this->filascoste[$id] = $coste;
		$this->filasproveedor[$id] = $idproveedor;
		$this->filaslaboratorio[$id] = $idlaboratorio;
	}
	
	function Crea(){
		$this->IdPedido = false;
		$this->filas = array();
		$this->filascoste = array();		
		$this->filasproveedor 	= array();
		$this->filaslaboratorio 	= array();
		$this->set("Status",PEDIDO_PETICION,FORCE);		
	}
	
	function Alta(){

		global $UltimaInsercion;
		
		$data          = $this->export();
		$evitar        = array("FechaPeticion");
		$coma          = false;		

		$listaKeys     = "";
		$listaValues   = "";
		$listaocKeys   = "";
		$listaocValues = "";
		$listacpKeys   = "";
		$listacpValues = "";
		$listatKeys    = "";
		$listatValues  = "";
		
		foreach ($data as $key=>$value)
		  {
		    if (!in_array($key,$evitar))
		      {

			if ($coma) 
			  {
			    $listaKeys   .= ", ";
			    $listaValues .= ", ";
			  }
			
			$listaKeys     .= " $key";
			$value_s        = CleanRealMysql($value);
			$listaValues   .= " '$value_s'";
			$coma           = true;	

		      }
		  }

		$detadoc        = getSesionDato("detadoc");
		$IGV            = getSesionDato("IGV");
		$IPC            = getSesionDato("IPC");
		$IdProveedor    = $detadoc[1];
 		$IdUsuario      = getSesionDato("IdUsuario");
		$IdLocal        = getSesionDato("DestinoAlmacen");
		$Moneda         = getSesionDato("Moneda");
		$FechaEntrega   = $detadoc[4];
		$Codigo         = $detadoc[3];
		$FechaPago      = $detadoc[8];
		$IdMoneda       = $detadoc[5];
		$TCambio        = ($detadoc[6]!=0||$detadoc[6]!='')? $detadoc[6]:1;
		$FTCambio       = $detadoc[7];
		$Obs            = $detadoc[12];
		$IdOrdenCompra  = $detadoc[11];
		$ModoPago       = (getSesionDato("aCredito")=='true')?"Credito":"Contado";
	        $InclImpuesto   = (getSesionDato("incImpuestoDet"))?1:0;
		$incIGV         = getSesionDato("incImpuestoDet");
		$Comprobante    = getNombreDocumentoCompra($detadoc);
		$seldoc         = ($detadoc[0]=="O")? "O":"C"; 
		$this->IdPedido = false;

		switch($seldoc){

		case "O"://Orden Compra

		  $listaocKeys   .= "  IdLocal";
		  $listaocValues .= "  '".$IdLocal."'";
		  $listaocKeys   .= ",  IdMoneda";
		  $listaocValues .= ",  '".$IdMoneda."'";
		  $listaocKeys   .= ", IdUsuario";
		  $listaocValues .= ", '".$IdUsuario."'";
		  $listaocKeys   .= ", IdProveedor";
		  $listaocValues .= ", '".$IdProveedor."'";		
		  $listaocKeys   .= ", Impuesto";
		  $listaocValues .= ", '".$IGV."'";		
		  $listaocKeys   .= ", FechaPrevista";
		  $listaocValues .= ", '".explota($FechaEntrega)."'";		
		  $listaocKeys   .= ", FechaPago";
		  $listaocValues .= ", '".explota($FechaPago)."'";		
		  $listaocKeys   .= ", CambioMoneda";
		  $listaocValues .= ", '".$TCambio."'";		
		  $listaocKeys   .= ", Observaciones";
		  $listaocValues .= ", '".$Obs."'";		
		  $listaocKeys   .= ", FechaCambioMoneda";
		  $listaocValues .= ", '".explota($FTCambio)."'";		
		  $listaocKeys   .= ", ModoPago";
		  $listaocValues .= ", '".$ModoPago."'";		

		  $sql = "INSERT INTO ges_ordencompra ( $listaocKeys ) ".
		         "VALUES ( $listaocValues )";
		  $res = query($sql);
		  if (!$res) { $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		    return false;	}

		  break;

		case "C"://Pedido

		  $listaKeys   .= ", FechaRecepcion";
		  $listaValues .= ", '".explota($FechaEntrega)."'";		
		  $listaKeys   .= ", FechaPeticion";
		  $listaValues .= ", NOW()";		
		  $listaKeys   .= ", IdMoneda";
		  $listaValues .= ", '".$IdMoneda."'";
		  $listaKeys   .= ", IdLocal";
		  $listaValues .= ", '".$IdLocal."'";
		  $listaKeys   .= ", IdOrdenCompra";
		  $listaValues .= ", '".$IdOrdenCompra."'";
		  $listaKeys   .= ", CambioMoneda";
		  $listaValues .= ", '".$TCambio."'";
		  $listaKeys   .= ", FechaCambioMoneda";
		  $listaValues .= ", '".explota($FTCambio)."'";
		  $listaKeys   .= ", IdUsuario";
		  $listaValues .= ", '".$IdUsuario."'";
		  $listaKeys   .= ", Impuesto";
		  $listaValues .= ", '".$IGV."'";
		  $listaKeys   .= ", Percepcion";
		  $listaValues .= ", '".$IPC."'";
		  $listaKeys   .= ", IncluyeImpuesto";
		  $listaValues .= ", '".$InclImpuesto."'";
	  
		  $sql = "INSERT INTO ges_pedidos ( $listaKeys ) ".
		         "VALUES ( $listaValues )";
		  $res = query($sql);

		  if (!$res) { $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		    return false;	}

		  break;
		}

		$this->IdPedido = $UltimaInsercion;
		$IdPedido       = $this->IdPedido;
		$Importe        = 0;


		//Comprobante
		if($detadoc[0]!="O")
		  { 
		    $codigodoc       = ($detadoc[0]=='SD')?$IdLocal.'-'.$UltimaInsercion:$detadoc[3];
		    $listacpKeys    .= " IdUsuario";
		    $listacpValues  .= "'".$IdUsuario."'";
		    $listacpKeys    .= ",IdProveedor";
		    $listacpValues  .= ",'".$IdProveedor."'";
		    $listacpKeys    .= ",ModoPago";
		    $listacpValues  .= ",'".$ModoPago."'";
		    $listacpKeys    .= ",TipoComprobante";
		    $listacpValues  .= ",'".$Comprobante."'";
		    $listacpKeys    .= ",Codigo";
		    $listacpValues  .= ",'".$codigodoc."'";
		    $listacpKeys    .= ",FechaRegistro";
		    $listacpValues  .= ", NOW()";		
		    $listacpKeys    .= ",FechaFacturacion";
		    $listacpValues  .= ",'".explota($FechaEntrega)."'";		
		    $listacpKeys    .= ",FechaPago";
		    $listacpValues  .= ",'".explota($FechaPago)."'";
		    $listacpKeys    .= ",IdPedido";
		    $listacpValues  .= ",'".$IdPedido."'";
		    $listacpKeys    .= ",IdPedidosDetalle";
		    $listacpValues  .= ",'".$IdPedido."'";

		    $sql = "insert into ".
		      "ges_comprobantesprov ( ".$listacpKeys." ) ".
		      "values ( ".$listacpValues." )";

		    $res = query($sql);
		    if (!$res) { $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		      return false;	}

		  }

		//Detalle		
		foreach ($this->filas as $id=>$unidades) 
		  {

		    $preciounidad  = $this->filascoste[$id];
		    $Importe      += $unidades*$preciounidad;

		    $this->AltaFilaPedido($id,$unidades,$this->IdPedido,$preciounidad);

		  } 	


		//Orden Compra Importes
		if($detadoc[0]=="O") 
		  {
		    //Importe Total
		    $Importe   = round($Importe*100)/100;

		    //Elimina una vercion anterior, si existe?
		    $esCodOC   = ($IdOrdenCompra)?query("update ges_ordencompra ".
							"set Eliminado=1 ".
							"where IdOrdenCompra =".$IdOrdenCompra):false;
		    //Codigo Orden Compra
		    $CodOC  = ($esCodOC)?$detadoc[3]:$IdLocal.$IdPedido;

		    //Actualiza Importe 
		    $aset   = "update ges_ordencompra ";
		    $setval = "set Importe='".$Importe."', CodOrdenCompra=".$CodOC." ";
		    $where  = "where  IdOrdenCompra ='".$IdPedido."'";	
		    query($aset.$setval.$where); 
		  }


		//Orden Compra Estado "Recibido"
		if($IdOrdenCompra && $detadoc[0]!="O")
		   query("update ges_ordencompra ".
			 "set Estado='Recibido', ".
			 "FechaRecibido = NOW() ".
			 "where IdOrdenCompra =".$IdOrdenCompra);
		
		//Termina proceso
		return $this->IdPedido;
	}
		
        function AltaFilaPedido($id,$unidades,$IdPedido,$costeunidad){

		$detadoc  = getSesionDato("detadoc");
		$IdPedido = CleanID($IdPedido);
		$id 	  = CleanID($id);
		$unidades = intval($unidades);

		switch($detadoc[0]){

		case "O":

		  //ORDEN COMPRA
		  $tckey   = "IdOrdenCompra,IdProducto,Unidades,Costo";
		  $tcvalue = "'".$IdPedido."','".$id."','".$unidades."','".$costeunidad."'";      
		  $sql     = "insert into ges_ordencompradet ( ".$tckey." ) values ( ".$tcvalue." )";
		  $res     = query($sql);
		  if (!$res) { $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		    return false;	}


		  break;

		case "F":
		case "R":
		case "G":
		case "SD":

		  //COMPRA
		  global   $UltimaInsercion;

		  $ckey    = "IdPedido,IdProducto,Unidades";
 		  $cvalue  = "'".$IdPedido."','".$id."','".$unidades."'";
		  $sql     = "insert into ges_pedidosdet (".$ckey.") values (".$cvalue.")";
		  $res     = query($sql);
		  if (!$res) { $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		    return false;	}

		  //Series...
		  registrarNumeroSerie($id,$IdPedido,$UltimaInsercion);
		  
		break;

		}
 
		if (!$res) {
		  $this->Error(__FILE__ . __LINE__ , "E: no pudo guardar pedido");
		  return;
		}			

		//COSTE PRODUCTO
		$idprov  = ( $detadoc[1] > 0 )? $detadoc[1]: 1;
		$sql = 
		  " update ges_productos ".
		  " set    Costo ='".$costeunidad."', IdProvHab = ".$idprov.
		  " where  (IdProducto = '".$id."')";	
		query($sql,"Actualizando el coste");		
	}
	
}

function validaxdtCarritoDirecto(){

    $detadoc = getSesionDato('detadoc');
    $xdts    = getSesionDato("xdtCarritoCompras");
    $cart    = getSesionDato("CarritoCompras");
    $msg     = '';
    $msgtxt  = false;

    if($detadoc[0]=='O') return;
    if(!$cart)       return;

    foreach ($cart as $id=>$unid) 
      {	
	$msg = ($xdts[$id][1] && !$xdts[$id][2])? $msg."\n           - Número Serie":$msg;
	$msg = ($xdts[$id][5] && !$xdts[$id][6])? $msg."\n           - Fecha Vencimiento":$msg;
	$msg = ($xdts[$id][3] && !$xdts[$id][4])? $msg."\n           - Lote Producción":$msg;
	$msgtxt .= ($msg)? "\n      ".$xdts[$id][0].$msg."\n" : $msg;
	$msg = '';

      }
    $msg = ($msgtxt)? "gPOS: Carrito de Compra \n\n    Registre los datos pendientes de:\n".$msgtxt:$msg;
    return $msg;
}

function validaxdtCarritoProducto($id){

  $xdts  = getSesionDato("xdtCarritoCompras");
  $idval = true;

  if( $xdts[$id][1] ) $idval = ( $xdts[$id][2] )? $idval:false;
  if( $xdts[$id][5] ) $idval = ( $xdts[$id][6] )? $idval:false;
  if( $xdts[$id][3] ) $idval = ( $xdts[$id][4] )? $idval:false;

  return $idval;  
}

function actualizarFechaVencimientoCarrito($id,$fv){

    $xdts  = getSesionDato("xdtCarritoCompras");

    if(!$xdts[$id][3]) return;

    $arrfv           = getSesionDato("fechavencimiento");
    $xfv             = false;
    $fv              = trim($fv);
    $fvval           = ($fv!="")? true:false;

    //Modifica Registro
    for($i=0;$i<count($arrfv);$i=$i+2){
      $t = $i + 1;
      if($arrfv[$i] == $id)
	{
	  $arrfv[$t] = $fv;
	  $xfv       = true;
	}
    }

    //Nuevo Registro
    if(!$xfv)
      {
          array_push($arrfv,$id);
	  array_push($arrfv,$fv);
      }

    setSesionDato("fechavencimiento",$arrfv);
    actualizaxdtCarritoCompras($id,'fv',$fvval); 
}


function actualizarCodigoLoteCarrito($id,$lt){

    $xdts            = getSesionDato("xdtCarritoCompras");
    $idprodseriecart = getSesionDato("idprodseriecart");
    $arrlt           = getSesionDato("codigolote");
    $xlt             = false;
    $lt              = trim($lt);
    $ltval           = ($lt!="")? true:false;
    //Termina
    if(!$xdts[$id][5])
      return;

    //Modifica Registro
    for($i=0;$i<count($arrlt);$i=$i+2)
      {
	$t = $i + 1;
	if($arrlt[$i] == $id)
	  {
	    $arrlt[$t] = $lt;
	    $xlt       = true;
	  }
      }
    //Nuevo Registro
    if(!$xlt)
      {
	array_push($arrlt,$id);
	array_push($arrlt,$lt);
      }
    //Termina
    setSesionDato("codigolote",$arrlt);
    actualizaxdtCarritoCompras($id,'lt',$ltval);
}


function actualizarSeriesCarritoNS($id,$nseries,$fg){

    $xdts            = getSesionDato("xdtCarritoCompras");    
    $idprodseriecart = getSesionDato("idprodseriecart");
    $seriescart      = getSesionDato("seriescart");
    $arrfg           = getSesionDato("fechagarantia");
    $unidades        = getSesionDato("CarritoCompras");
    $nseries         = trim($nseries);
    $arrns           = explode(";", $nseries); 
    $nsval           = ( $unidades[$id] == count($arrns) )? 1:0;
    $xfg             = false;

    if(!$xdts[$id][1] )return;

    if(!in_array($id,$idprodseriecart))
      array_push($idprodseriecart,$id);

    $seriescart[$id] = $nseries;

    //Modifica Registro
    for($i=0;$i<count($arrfg);$i=$i+2)
      {
	$t   = $i + 1;
	if( $arrfg[$i] == $id ) 
	  {
	    $arrfg[$t] = $fg;
	    $xfg = true;
	  }
      }

    //Nuevo Registro
    if(!$xfg)
      {
          array_push($arrfg,$id);
	  array_push($arrfg,$fg);
      }

    setSesionDato("idprodseriecart",$idprodseriecart);
    setSesionDato("seriescart",$seriescart);
    setSesionDato("fechagarantia",$arrfg);
    actualizaxdtCarritoCompras($id,'ns',$nsval);
}

function actualizarSeriesCarritoCompra($id,$nseries,$fg){
    //print_r( getSesionDato("xdtCarritoCompras") );
    $xdts           = getSesionDato("xdtCarritoCompras");    
    $idprodseriebuy = getSesionDato("idprodseriebuy");
    $seriesbuy      = getSesionDato("seriesbuy");
    $arrfg          = getSesionDato("fechagarantia");
    $nseries        = trim($nseries);
    $nsval          = ($nseries!="")? true:false;
    
    if( !$xdts[$id][1] )return;
    
    if(!in_array($id,$idprodseriebuy))
      array_push($idprodseriebuy,$id);

    $seriesbuy[$id]=$nseries;

    array_push($arrfg,$id);
    array_push($arrfg,$fg);

    setSesionDato("idprodseriebuy",$idprodseriebuy);
    setSesionDato("seriesbuy",$seriesbuy);

    setSesionDato("fechagarantia",$arrfg);
    actualizaxdtCarritoCompras($id,'ns',$nsval);
}

function obtenerSeriesCarritoBuy($idproducto){
    $seriesbuy = getSesionDato("seriesbuy");
    if ( isset($seriesbuy[$idproducto] ) ) 
      return $seriesbuy[$idproducto];
}

function obtenerSeriesCarritoCompra($idproducto){
    $seriescart = getSesionDato("seriescart");
    if ( isset($seriescart[$idproducto]) ) 
	return $seriescart[$idproducto];
}

function obtenerSeriesGarantiaCompra($idproducto){
    $arr = getSesionDato("fechagarantia");
    for($i=0;$i<count($arr);$i=$i+2){
        $t = $i + 1;
	if($arr[$i] == $idproducto)
	  return $arr[$t];
    }
}

function obtenerLote($idproducto){
    $arr = getSesionDato("codigolote");
    for($i=0;$i<count($arr);$i=$i+2){
        $t = $i + 1;
	if($arr[$i] == $idproducto)
	  return $arr[$t];
    }
}

function obtenerFechaVencimiento($idproducto){
    $arr = getSesionDato("fechavencimiento");
    for($i=0;$i<count($arr);$i=$i+2){
        $t = $i + 1;
	if($arr[$i] == $idproducto)
	  return $arr[$t];
    }
}


function agnadirxdtCarritoCompras($id){

  $oPd = new producto;
  $oPd->Load($id);

  actualizaxdtCarritoCompras($id,'prod', getDatosProductosExtra($id,'nombrecb') ); 

  if( $oPd->get("Lote") )             actualizaxdtCarritoCompras($id,'lt',false);
  if( $oPd->get("FechaVencimiento") ) actualizaxdtCarritoCompras($id,'fv',false);
  if( $oPd->get("Serie") )            actualizaxdtCarritoCompras($id,'ns',false);
}

function actualizaxdtCarritoCompras($id,$modo,$value){

    $xdts  = getSesionDato("xdtCarritoCompras");
    $esxdt = false;

    if( !isset( $xdts[$id] ) ){
      $xdts[$id][1]  = 0;
      $xdts[$id][2]  = false;
      $xdts[$id][3]  = 0;
      $xdts[$id][4]  = false;
      $xdts[$id][5]  = 0;
      $xdts[$id][6]  = false;
    }

    switch ($modo) 
      {
      case 'ns' :
	$xdts[$id][1]  = 1;
	$xdts[$id][2]  = $value;
	$esxdt         = true;

	break;
      case 'fv' :
	$xdts[$id][3]  = 1;
	$xdts[$id][4]  = $value;
	$esxdt         = true;
	break;
      case 'lt' :
	$xdts[$id][5]  = 1;
	$xdts[$id][6]  = $value;
	$esxdt         = true;
	break;
      case 'prod' :
	$xdts[$id][0] = $value;//Producto
	break;
      }
    setSesionDato("xdtCarritoCompras",$xdts);
    //print_r( getSesionDato("xdtCarritoCompras") );
    return $esxdt;
}

function AgnadirCarritoComprasDirecto($id,$unidades,$costounitario,
				      $fv=false,$lt=false,$dscto,
				      $importe,$pdscto){
    if(!$id) return;

    agnadirxdtCarritoCompras($id);

    $actual             = getSesionDato("CarritoCompras");
    $costes             = getSesionDato("CarroCostesCompra");
    $descuentos         = getSesionDato("descuentos");

    $actual[$id]        = ( isset( $actual[$id] ) )? $actual[$id] + $unidades : $unidades;	
    $costes[$id]        = $costounitario;//getCosteDefectoProducto($id);
    $descuentos[$id][0] = $dscto;
    $descuentos[$id][1] = $importe;
    $descuentos[$id][2] = $pdscto;

    setSesionDato("CarritoCompras",$actual);
    setSesionDato("CarroCostesCompra",$costes);
    setSesionDato("descuentos",$descuentos);

    if($lt) actualizarCodigoLoteCarrito($id,$lt);
    if($fv) actualizarFechaVencimientoCarrito($id,$fv);
}

function AgnadirCarritoCompras($id,$unidades=1) {
	
	if(!$id) return;
	agnadirxdtCarritoCompras($id);

	$actual         = getSesionDato("CarritoCompras");
	$costes         = getSesionDato("CarroCostesCompra");
	$dsctos         = getSesionDato("descuentos");
	$actual[$id]    = ( isset( $actual[$id] ))? $actual[$id] + $unidades:$unidades;	
	$costes[$id]    = ( isset( $costes[$id] ))? $costes[$id]:getCosteDefectoProducto($id);
	$descuento      = ( isset( $dsctos[$id][0]) )? $dsctos[$id][0]:0;
	$dsctos[$id][1] = $actual[$id]*$costes[$id]-$descuento;

	setSesionDato("CarritoCompras",$actual);
	setSesionDato("CarroCostesCompra",$costes);
	setSesionDato("descuentos",$dsctos);
}

function getNombreDocumentoCompra($dtdc){
  
  $Comprobante = '';

  switch($dtdc[0]){
  case "O": 
    $Comprobante = "Pedido"; 
    break;
  case "F":
    $Comprobante = "Factura";
    break;
  case "R":
    $Comprobante = "Boleta";
    break;
  case "G":
    $Comprobante = "Albaran";
    break;
  case "SD":
    $Comprobante = "Ticket";
    break;
  } 
  return $Comprobante;
}


function ClonarCarritoSeriesBuyTwoCart(){

    $idprodseriebuy = getSesionDato("idprodseriebuy");
    $seriesbuy      = getSesionDato("seriesbuy");

    setSesionDato("idprodseriecart",$idprodseriebuy);
    setSesionDato("seriescart",$seriesbuy);
}

?>
