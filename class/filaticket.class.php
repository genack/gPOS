<?php


class filaTicket {
	var $unidades;
	var $precio;
	var $descuento;
	var $codigo;
	var $impuesto;
	var $idproducto;
	var $importe;
	var $concepto;
	var $talla;
	var $color;
	var $referencia;
	var $codigobarras;
	var $_esServicio;
	var $IdComprobante;
	var $idsubsidiario;
	var $nticket;
	var $CB;
	
	function esServicio(){
		return $this->_esServicio;	
	}
	
	function DeduceIdProducto($codigo,$idproducto){ //deduca el idproducto desde 
	  list($CB,$idsubsidiario,$Num) = explode(".",$codigo);
	  error(__FILE__,"DeduceIdProducto: $CB,$idsubsidiario,$Num");
	  $this->CB = $CB;
	  $this->codigojob  = $CB.$idsubsidiario.$Num;

	  $this->idproducto = $idproducto;	
	}
	
	/*
	 * Setter para los datos de la fila.
	 */	
	function Set($codigo,$unidades,$precio,$descuento,$impuesto,
		     $importe,$concepto,$talla,$color,$referencia,$cb,
		     $idsubsidiario,$nombre,$pedidodet,$status,$oferta,
		     $costo,$idproducto,$unidadimporte){
			
		$this->unidades 	= $unidades;
		$this->precio 		= $precio;
		$this->descuento 	= $descuento;
		$this->codigo 		= $codigo;//puede contener el codigo de arreglo
		$this->CB		= $codigo;//Codigo real		
		$this->impuesto 	= $impuesto * 100;//guardado en %
		$this->importe		= $importe;
		$this->unidadimporte	= $unidadimporte;
		$this->concepto 	= $concepto;
		$this->talla 		= $talla;
		$this->color		= $color;
		$this->referencia	= $referencia;
		$this->codigobarras     = $cb;
		$this->idsubsidiario	= $idsubsidiario;
		$this->nombre		= $nombre;
		$this->pedidodet	= $pedidodet;
		$this->status 	        = $status;
		$this->oferta 	        = $oferta;
		$this->costo	        = $costo;
		$this->codigojob	= "$codigo.$idsubsidiario";
		$this->descripcion      = "";


		if ($idsubsidiario>0)
		  {
		    $this->_esServicio = true;
		    $this->DeduceIdProducto($codigo,$idproducto);
		    $this->descripcion = "$nombre";		
		  }
		else 
		  {
		    $this->_esServicio = false;
		    $this->idproducto  = $idproducto;
		  }			
	}
	
	function AltaServicio($job){

	  Global $UltimaInsercion;

	  $id 	      = $job->getId();
	  $arreglo    = trim(CleanRealMysql($this->concepto));

	  $coste      = $this->importe;
	  $IdServicio = esServicioSubsidiario($arreglo);
	  if($IdServicio == 0)
	    {		
	      $sql = 
		"INSERT ".
		"INTO ges_subsidiariosserv ".
		"(Servicio)".
		"VALUES ".
		"('$arreglo')";
	      query($sql);		  
	      $IdServicio = $UltimaInsercion;
	    }

	  $this->descripcion = $IdServicio;		
	}	

	function Alta($IdComprobante,$SerialNum,$local,$documentoventa){ 

	  $this->IdComprobante = $IdComprobante;
	  $almacenes  = new almacenes;	  
	  $aPedidoDet = explode(",", $this->pedidodet);//IdPedidoDet:unidades:Serie;Serie,...
	  $aStatus    = explode("~", $this->status);//Serie~Lote~Vence...
	  //Filas...
	  foreach ($aPedidoDet as $xrow) 
	    {

	      Global $UltimaInsercion;
	      $axrow       = explode(":", $xrow); 
	      $idpedidodet = (isset($axrow[1]))? $axrow[0] : 0;
	      $unidades    = (isset($axrow[1]))? $axrow[1] : $this->unidades;
	      $xnseries    = ( isset( $axrow[2] ) )? $axrow[2] : false;
	      $xnseries    = ( isset( $axrow[2] ) )? str_replace(";",",",$xnseries) : false;
	      //$importe     = ($this->importe)/$unidades;
	      //$importe     = $unidades*$this->precio;
	      $importe     = $this->importe;
	      if( count( $aPedidoDet ) > 1 )
		{
		  $xprecio     = ($this->importe)/$this->unidades;
		  $importe     = $unidades*$xprecio;
		}

	      //ComprobanteDet...
	      $Keys    = "IdComprobante,";
	      $Values  = "'".$IdComprobante."',";	
	      $Keys   .= "IdProducto,";
	      $Values .= "'".$this->idproducto."',";
	      $Keys   .= "IdPedidoDet,";
	      $Values .= "'".$idpedidodet."',";
	      $Keys   .= "Cantidad,";
	      $Values .= "'".$unidades."',";
	      $Keys   .= "Precio,";
	      $Values .= "'".$this->precio."',";
	      $Keys   .= "CostoUnitario,";
	      $Values .= "'".$this->costo."',";
	      $Keys   .= "Descuento,";
	      $Values .= "'".$this->descuento."',";
	      $Keys   .= "Importe,";
	      $Values .= "'".$importe."',";
	      $Keys   .= "Impuesto,";
	      $Values .= "'".$this->impuesto."',";
	      $Keys   .= "Concepto,";
	      $Values .= "'".$this->concepto."',";
	      $Keys   .= "Talla,";
	      $Values .= "'".$this->talla."',";
	      $Keys   .= "Color,";
	      $Values .= "'".$this->color."',";
	      $Keys   .= "Referencia,";
	      $Values .= "'".$this->referencia."',";
	      $Keys   .= "CodigoBarras,";
	      $Values .= "'".$this->codigobarras."',";
	      $Keys   .= "Oferta,";
	      $Values .= "'".$this->oferta."',";
	      $Keys   .= "Serie,";
	      $Values .= "'".$aStatus[0]."',";
	      $Keys   .= "Lote,";
	      $Values .= "'".$aStatus[1]."',";
	      $Keys   .= "Vencimiento";
	      $Values .= "'".$aStatus[2]."'";

	      $sql     = "insert into ges_comprobantesdet (".$Keys.") values (".$Values.")";
	      query($sql,"Detalle ticket");
				
	      $this->nticket    = $SerialNum;
	      $idcomprobantedet = $UltimaInsercion;

	      //Existencias...
	      $existencias  = $almacenes->obtenerExistenciasKardex($this->idproducto,$local);

	      if ( $unidades > $existencias ) continue;

	      //Series...
	      if( isset( $axrow[2] ) ) 
		registraSalidaSeriesPedidoDet($this->idproducto,$IdComprobante,$xnseries,$idpedidodet);

	      //Kardex... 
	      $Operacion  = 2;//Venta

	      if( isset($axrow[1]) )
		registrarSalidaKardexFifo($this->idproducto,$unidades,$this->costo,
					  $Operacion,$local,$idpedidodet,$idcomprobantedet,
					  $existencias,false,false,false);
	      //Almacen Kardex...
	      $almacenes->actualizarCosto($this->idproducto,$local);
	      $almacenes->actualizaOfertaUnidades($this->idproducto,$local,$this->oferta); 
	      actualizaResumenKardex($this->idproducto,$local);

	      
	    }
	}

	function AltaPedidos($IdComprobante){ 


	  $this->IdComprobante = $IdComprobante;
	  //Preventa...
	  $Keys    = "IdPresupuesto,";
	  $Values  = "'".$IdComprobante."',";
	  $Keys   .= "IdProducto,";
	  $Values .= "'".$this->idproducto."',";
	  $Keys   .= "Cantidad,";
	  $Values .= "'".$this->unidades."',";
	  $Keys   .= "Precio,";
	  $Values .= "'".$this->precio."',";
	  $Keys   .= "Descuento,";
	  $Values .= "'".$this->descuento."',";
	  $Keys   .= "Importe,";
	  $Values .= "'".$this->importe."',";
	  $Keys   .= "Concepto,";
	  $Values .= "'".$this->concepto."',";
	  $Keys   .= "Talla,";
	  $Values .= "'".$this->talla."',";
	  $Keys   .= "Color,";
	  $Values .= "'".$this->color."',";
	  $Keys   .= "Referencia,";
	  $Values .= "'".$this->referencia."',";
	  $Keys   .= "CodigoBarras";
	  $Values .= "'".$this->codigobarras."'";
	  $sql     = "insert into ges_presupuestosdet (".$Keys.") values (".$Values.")";

	  
	  $res = query($sql,"Detalle ticket");				
	      
	  //Reserva...
	  //IdPedidoDet:unidades:Serie;Serie,...
	  $aPedidoDet = explode(",", $this->pedidodet);

	  foreach ($aPedidoDet as $xrow) 
	    {

	      $axrow       = explode(":", $xrow); 
	      $idpedidodet = (isset($axrow[1]))? $axrow[0] : 0;
	      $xnseries    = (isset($axrow[2]))? $axrow[2] : false;

	      //Series...
	      if($xnseries) 
		reservaSalidaSeriesPedidoDet($this->idproducto,$IdComprobante,
					     $xnseries,$idpedidodet);

	    }
	}

	function AltaMProductos($IdComprobante){ 
	  
	         $this->IdComprobante = $IdComprobante;

		 //Inserta ó Actualiza ... 
		 
		 //Existe? ...
		 $sql =
		   " select IdMetaproductoDet as Id ".
		   " from   ges_metaproductosdet ".
		   " where  IdMetaproducto = '".$IdComprobante."'".
		   " and    IdProducto     = '".$this->idproducto."'";
		 $res = query($sql);

		 if($row= Row($res)) 
		   {
		     //Actualiza...
		     $sql=
		       " update ges_metaproductosdet ".
		       " set    Cantidad  = '".$this->unidades."',".
		       "        Costo     = '".$this->precio."',".
		       "        Importe   = '".$this->importe."',".
		       "        Eliminado = '0'".
		       " where  IdMetaProductoDet  = '".$row["Id"]."'";		
		   }
		 else
		   {
		     $aStatus = explode("~", $this->status);//Serie~Lote~Vence...

		     //Inserta...
		     $Keys    = "IdMetaproducto,";
		     $Values  = "'".$IdComprobante."',";
		     $Keys   .= "IdProducto,";
		     $Values .= "'".$this->idproducto."',";
		     $Keys   .= "Cantidad,";
		     $Values .= "'".$this->unidades."',";
		     $Keys   .= "Costo,";
		     $Values .= "'".$this->precio."',";
		     $Keys   .= "Importe,";
		     $Values .= "'".$this->importe."',";
		     $Keys   .= "Concepto,";
		     $Values .= "'".$this->descripcion."',";
		     $Keys   .= "Talla,";
		     $Values .= "'".$this->talla."',";
		     $Keys   .= "Color,";
		     $Values .= "'".$this->color."',";
		     $Keys   .= "Referencia,";
		     $Values .= "'".$this->referencia."',";
		     $Keys   .= "CodigoBarras,";
		     $Values .= "'".$this->codigobarras."',";
		     $Keys   .= "Serie,";
		     $Values .= "'".$aStatus[0]."',";
		     $Keys   .= "Lote,";
		     $Values .= "'".$aStatus[1]."',";
		     $Keys   .= "Vencimiento";
		     $Values .= "'".$aStatus[2]."'";
		     
		     $sql = "insert into ges_metaproductosdet (".$Keys.") values (".$Values.")";     
		   }
		 
		 $res = query($sql,"Detalle ticket");


		 //Series Reserva...
		 //IdPedidoDet:unidades:Serie;Serie,...
		 $aPedidoDet = explode(",", $this->pedidodet);

		 foreach ($aPedidoDet as $xrow) 
		   {

		     $axrow       = explode(":", $xrow); 
		     $idpedidodet = (isset($axrow[1]) )? $axrow[0] : 0;
		     $xnseries    = (isset($axrow[2]))? $axrow[2] : false;

		     //Series...
		     if($xnseries) reservaSalidaSeriesMProductoDet($this->idproducto,$IdComprobante,
								   $xnseries,$idpedidodet);

		   }
		 
	}

	function RetiradaDeAlmacen($local){			
		global $alm;
			
		$alm->ModificaCantidad($this->idproducto,0 - $this->unidades,$local);		
	}

}

function esServicioSubsidiario($arreglo){

  $a_srt = str_replace(" ","",$arreglo);

  $sql= 
    "SELECT IdServicio,Servicio ".
    "FROM ges_subsidiariosserv ";
  $res = query($sql);

  if ($res){

    while( $row = Row($res)){

      $s_srt = str_replace(" ","",$row["Servicio"]);
      if($s_srt == $a_srt) 
	return $row["IdServicio"];
    }
    return 0;
  }
}

function esNuevo2CrearServicio($concepto){



	  $arreglo    = trim(CleanRealMysql($concepto));
	  $IdServicio = esServicioSubsidiario($arreglo);
	  $esNuevo    = 0;
	  if($IdServicio == 0)
	    {		
	      Global $UltimaInsercion;

	      $sql = 
		"INSERT ".
		"INTO ges_subsidiariosserv ".
		"(Servicio)".
		"VALUES ".
		"('$concepto')";
	      query($sql);		  

	      $IdServicio = $UltimaInsercion;
	      $esNuevo    = 1;
	    }
	  echo $esNuevo.":".$IdServicio;		
}
?>
