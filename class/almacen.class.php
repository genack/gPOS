<?php

function GenEtiqueta($id,$precio=0) {
	global $action;
	$ot = getTemplate("Etiqueta");
	
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		echo "1!";
		return false; }

	$oProducto = new producto;
	
	if (!$oProducto->Load($id)){
		error(__FILE__ . __LINE__ ,"Info: producto no encontrado");
		echo "2!";		
		return false; 		
	}
	
	$bar = $oProducto->getCB();
	$nombre = $oProducto->getDescripcion();
	$marca = $oProducto->getMarcaTexto();
	$nombre = $nombre.' '.$marca;
	$cr = "&";
		
	$cad = "barcode=" . $bar . $cr;
	$cad .= "format=gif" . $cr;
	$cad .= "text=$bar" . $cr;
	$cad .= "text=".urlencode($nombre ." - " .$oProducto->get("Referencia")) . $cr;
	$cad .= "width=".getParametro("AnchoBarras")  .$cr; 
	$cad .= "height=".getParametro("AltoBarras") . $cr;		

	$urlbarcode = "modulos/barcode/barcode.php?" . $cad;
	
	$ot->fijar("urlbarcode", $urlbarcode);
	$ot->fijar("precio",FormatMoney($precio));	
	$ot->fijar("talla",$oProducto->getTextTalla());
	$ot->fijar("color",$oProducto->getTextColor());
	$ot->fijar("referencia",$oProducto->get("Referencia"));
	$ot->fijar("nombre",$nombre);

	echo $ot->Output();					
}


function getCosteDefectoArticulo($id) {
	$id = CleanID($id);
	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$id'  ";
	$row = queryrow($sql);	
	if (!$row) return false;
	$IdProducto = $row["IdProducto"];
	$sql = "SELECT Costo FROM ges_productos WHERE IdProducto = '$IdProducto'"; 
	$row = queryrow($sql);
	if (!$row) return false;
	
	return $row["Costo"]; 	
}


function AgnadirCarritoTraspaso($id,$u=1){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}
	
	$actual     = getSesionDato("CarritoTrans");
	$mover      = getSesionDato("CarritoMover");
	
	if (!in_array($id,$actual))
		array_push($actual,$id);
	
	$mover[$id] = $u;
		
	setSesionDato("CarritoMover",$mover);	
	setSesionDato("CarritoTrans",$actual);	
}

function AgnadirCarritoTraspasoSeries($id,$series){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}

	$actual     = getSesionDato("CarritoTransSeries");
	$mover      = getSesionDato("CarritoMoverSeries");

	if (!in_array($id,$actual)) 
	  array_push($actual,$id);

	$mover[$id] = $series;

	setSesionDato("CarritoMoverSeries",$mover);	
	setSesionDato("CarritoTransSeries",$actual);	
}

function QuitarDeCarritoTraspasoSeries($id){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}

	$actual = getSesionDato("CarritoTransSeries");
	$mover = getSesionDato("CarritoMoverSeries");

	if (in_array($id,$actual)) {

	  $clave = array_search($id, $actual); 
	  unset($actual[$clave]);
	  $actual = array_values($actual); 

	}
	unset($mover[$id]);	
	setSesionDato("CarritoMoverSeries",$mover);	
	setSesionDato("CarritoTransSeries",$actual);
	
}


function existeUnidAlmacenSeries($idpedidodet,$xseries,$id){

         $noseries = array();
	 $nseries  = explode(";", $xseries);

	 for($j=0; $j< count($nseries); $j++)
	   {
	     if(!esDisponibleVentaSerie($nseries[$j],$id,$idpedidodet)){
	       array_push($noseries,$nseries[$j]);
	     }
	   }

	 return ( count($noseries) > 0 )? implode(",",$noseries):0;
}

function existeUnidAlmacen($und,$id,$idpedidodet,$xseries,$codigo,
			   $local,$rkardex){

	 $akardex = explode("~", $rkardex);
	 
	 foreach ($akardex as $pedidodet) 
	   {
	     $apedidodet = explode(":", $pedidodet);

	     if( $apedidodet[0] == $idpedidodet ) 
	       {
		 $esValida    = validaKardexPedidoDet($idpedidodet);
		 $srtns       = ( $xseries )? existeUnidAlmacenSeries($idpedidodet,$xseries,$id):0;
		 $esSerie     = ( $srtns == "0" )? false:true;
		 $esCheck     = ( $esSerie || $apedidodet[1] < $und )? true:false;
		 $esCheck     = ( $esValida )? true:$esCheck;
		 $idpedidodet = ( $esValida )? $idpedidodet.'-'.$esValida : $idpedidodet;

		 return ($esCheck)? $codigo.":".$idpedidodet.":".$und.":".$apedidodet[1].":".$srtns:0;
	       }
	   }
	 //Problemas...
	 return $codigo.":".$idpedidodet.":".$und.":0:".$xseries;
}

function syncUnidAlmacen($time,$IdLocal){

         $IdLocal = CleanID($IdLocal);
	 $time    = CleanInt($time);
	 $a_prod  = getIdProductosSyncAlmacen($time,$IdLocal); 
	 $pjsOut  = 1;

	 if(count($a_prod)>0)
	   $pjsOut = getProductosSyncAlmacen($a_prod,$IdLocal,false,true);

	 return $pjsOut;
}

function getUnidAlmacen($IdLocal){

         $IdLocal = CleanID($IdLocal);
	 $pjsOut  = getProductosSyncAlmacen(array(),$IdLocal,false,false);
	 return $pjsOut;
}

function getProductosSyncAlmacen($aprod=array(),$IdLocalActivo,$filtro=false,$esSync=false){

         $allprod         = implode(",",$aprod);
	 $igv             = getSesionDato("IGV");
	 $out             = "";
	 $esExtra         = ( $filtro )? true:false;

	 $filtroProducto  = " AND ( ges_almacenes.Unidades >0 OR (ges_productos.Servicio=1 ".
	                    " OR ges_almacenes.StockIlimitado=1)) ";
	 $filtroProducto  = ( $esSync         )? '':$filtroProducto;
	 $filtroProducto .= ( count($aprod)>0 )?" AND ges_almacenes.IdProducto IN (".$allprod.") ":"";
	 $filtroProducto  = ( $esExtra        )? $filtro:$filtroProducto;
	 //$esBTCA          = ( getSesionDato("GlobalGiroNegocio") == "BTCA" )? true:false;

	 $sql = 
	   "SELECT ges_almacenes.IdProducto, ".
	   "       ges_productos.IdProdBase,  ".
	   "       ges_almacenes.Id, ".
	   "       ges_almacenes.IdLocal, ".
	   "       ges_almacenes.Unidades,  ".
	   "       ges_almacenes.DisponibleUnidades,  ".
	   "       ges_almacenes.Impuesto,  ".
	   "       ges_almacenes.Oferta, ".
	   "       ges_almacenes.OfertaUnidades, ".
	   "       ges_almacenes.PrecioVentaOferta, ".
	   "       ges_almacenes.StockMin, ".
	   "       ges_almacenes.StockIlimitado, ".
	   "       ges_productos.CodigoBarras, ".
	   "       ges_productos.RefProvHab, ".
	   "       ges_productos.Referencia, ".
	   "       ges_productos_idioma.Descripcion, ".
	   "       ges_marcas.Marca, ".
	   "       ges_tallas.Talla, ".
	   "       ges_colores.Color, ".
	   "       ges_laboratorios.NombreComercial as Laboratorio, ".
	   "       ges_productos.Serie, ".
	   "       ges_productos.Servicio, ".
	   "       ges_productos.Lote, ".
	   "       ges_productos.FechaVencimiento as Vence, ".
	   "       ges_productos.IdProductoAlias0, ".
	   "       ges_productos.IdProductoAlias1, ".
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_productos.UnidadesPorContenedor, ".
	   "       ges_productos.UnidadMedida, ".
	   "       ges_productos.CondicionVenta, ".
	   "       ges_productos.MetaProducto, ".
	   "       ges_contenedores.Contenedor, ".
	   "       ges_almacenes.ResumenKardex, ".
	   "       ges_almacenes.CostoUnitario, ".
	   "       ges_almacenes.PrecioVenta AS PVD,".
	   "       ges_almacenes.PVDDescontado AS PVDD, ".
	   "       ges_almacenes.PrecioVentaCorporativo AS PVC,".
	   "       ges_almacenes.PVCDescontado AS PVCD, ".
	   "       ges_almacenes.Disponible ".
	   "FROM   (((((((ges_almacenes   ".
	   "INNER  JOIN ges_productos        ON ".
	   "       ges_almacenes.IdProducto   = ges_productos.IdProducto) ".
	   "INNER  JOIN ges_productos_idioma ON ".
	   "       ges_productos.IdProdBase   = ges_productos_idioma.IdProdBase) ".
	   "INNER  JOIN ges_marcas           ON ".
	   "       ges_productos.IdMarca      = ges_marcas.IdMarca) ".
	   "INNER  JOIN ges_laboratorios     ON ".
	   "       ges_productos.IdLabHab     = ges_laboratorios.IdLaboratorio) ".
	   "INNER  JOIN ges_tallas           ON ".
	   "       ges_productos.IdTalla      = ges_tallas.IdTalla) ".
	   "INNER  JOIN ges_colores          ON ".
	   "       ges_productos.IdColor      = ges_colores.IdColor) ".
	   "INNER  JOIN ges_contenedores     ON ".
	   "       ges_productos.IdContenedor = ges_contenedores.IdContenedor) ".
	   "WHERE  ges_productos_idioma.IdIdioma = '1' ".
	   "       ".$filtroProducto.
	   //"AND    ges_almacenes.Disponible   ='1' ".
	   "AND    ges_productos.Eliminado    = 0 ".
	   "AND    ges_almacenes.IdLocal      = '$IdLocalActivo'".
	   "ORDER  BY ges_productos.IdProdBase ";

	 $jsOut = "";
	 $jsLex = new jsLextable();
	 $jsListar = "";

	 $res = query($sql);

	 while ($row = Row($res))
	   {
	     //INFO: ProductosLatin1 indica que la tabla productos esta codificado en 
	     // Latin1, y no en utf8
	     $xproducto    = $row["IdProducto"];
	     $xlocal       = $row["IdLocal"];
	     $PVD          = $row["PVD"];
	     $PVDD         = $row["PVDD"];
	     $PVC          = $row["PVC"];
	     $PVCD         = $row["PVCD"];
	     $UnidDisp     = $row["DisponibleUnidades"];
	     $Oferta       = $row["Oferta"];
	     $OfertaUnid   = $row["OfertaUnidades"];
	     $PVO          = $row["PrecioVentaOferta"];
	     //$esOferta     = ( $Oferta )? true:false;
	     $Disponible   = $row["Disponible"];

	     //$esServicio  = ($row["Servicio"])? true:false;
	     //$esMProducto = ($row["MetaProducto"])? true:false;
	     $rkdx        = $row["ResumenKardex"];
	     $Dosis       = getfichatecnica2Producto($xproducto);
	     $Serie       = ($row["Serie"] )? getPedidoDet2Kardex('Serie',$rkdx,$xproducto,$xlocal):"";
	     $Lote        = ($row["Lote"]  )? getPedidoDet2Kardex('Lote',$rkdx,$xproducto,$xlocal) :"";
	     $Vence       = ($row["Vence"] )? getPedidoDet2Kardex('Vence',$rkdx,$xproducto,$xlocal):"";
	     $alias1      = getIdProductoAlias2Texto( $row["IdProductoAlias0"] );
	     $alias2      = getIdProductoAlias2Texto( $row["IdProductoAlias1"] );

	     //Descripcion...
	     $lexNombre   = $jsLex->add($row["Descripcion"], getParametro("ProductosLatin1") );
	     $lexTalla    = $jsLex->add($row["Talla"]);
	     $lexColor    = $jsLex->add($row["Color"]);
	     $lexMarca    = qminimal($row["Marca"]);
	     $lexLab      = $jsLex->add($row["Laboratorio"], getParametro("ProductosLatin1"));
	     $lexAlias1   = $jsLex->add($alias1, getParametro("ProductosLatin1"));
	     $lexAlias2   = $jsLex->add($alias2, getParametro("ProductosLatin1"));

	     //Codigos...
	     $qmnCB       = qminimal($row["CodigoBarras"]);
	     $qmnRef      = qminimal($row["Referencia"]);
	     $qmnRefProv  = qminimal($row["RefProvHab"]);

	     //Stock...
	     $Stock       = qminimal($row["Unidades"]);
	     $xStock      = ( $UnidDisp > 0 && $Stock >= $UnidDisp )? $UnidDisp  :$Stock;//Reservado
	     //$qmnStock    = ( $esOferta && $Stock >= $OfertaUnid   )? $OfertaUnid:$xStock;//Ofertado
	     $qmnStock    = ( $Disponible )? $xStock:0;//Disponible
	     $qmnOfertaUnid = $OfertaUnid;
	     $qmnKardex   = qminimal($rkdx);
	     $qmnIlimitado= qminimal($row["StockIlimitado"]);

	     //Precios...
	     //$PVD         = ( $esOferta )? $OfertaPrecio:$PVD;
	     //$PVDD        = ( $esOferta )? $OfertaPrecio:$PVDD;
	     //$PVC         = ( $esOferta )? $OfertaPrecio:$PVC;
	     //$PVCD        = ( $esOferta )? $OfertaPrecio:$PVCD;
	     $qmnPVD      = qminimal($PVD*100);
	     $qmnPVDD     = qminimal($PVDD);
	     $qmnPVC      = qminimal($PVC*100);
	     $qmnPVCD     = qminimal($PVCD);
	     $qmnPVO      = qminimal($PVO);
	     $qmnCosto    = qminimal($row["CostoUnitario"]);
	     $qmnImpuesto = qminimal($row["Impuesto"]); 

	     //Detalles...
	     $qmnSerie    = qminimal($Serie); 
	     $qmnLote     = qminimal($Lote); 
	     $qmnOferta   = qminimal($Oferta); 
	     $qmnVence    = qminimal($Vence);
	     $qmnCondVenta= qminimal($row["CondicionVenta"]);
	     $qmnMenudeo  = qminimal($row["VentaMenudeo"]);
	     $qmnServicio = qminimal($row["Servicio"]);
	     $qmnMProducto= qminimal($row["MetaProducto"]);
	     $qmnUnd      = qminimal($row["UnidadMedida"]);
	     $qmnCont     = qminimal($row["Contenedor"]);
	     $qmnUndxCont = qminimal($row["UnidadesPorContenedor"]);
	     $qmnID       = qminimal($xproducto);
	     $qmnDosis    = qminimal($Dosis);//BTCA...
	     
	     $jsOut .= "tA(" .
	       $qmnID.",".
	       $qmnCB.",".
	       $lexNombre.",".
	       $qmnRef.",".
	       $qmnPVD.",".
	       $qmnPVC.",".
	       $qmnImpuesto.",".
	       $lexTalla.",".
	       $lexColor.",".		
	       $qmnOferta.",".
	       $qmnOfertaUnid.",".
	       $qmnPVO.",".
	       $qmnCondVenta.",null,null,".
	       $qmnKardex.",".
	       $lexAlias1.",".
	       $lexAlias2.",".
	       $qmnRefProv.",".
	       $qmnStock.",".
	       $qmnSerie.",".
	       $lexMarca.",".
	       $qmnCosto.",".
	       $qmnMenudeo.",".
	       $qmnUndxCont.",".
	       $qmnUnd.",".
	       $lexLab.",".
	       $qmnCont.",".
	       $qmnPVDD.",".
	       $qmnPVCD.",".
	       $qmnVence.",".
	       $qmnLote.",".
	       $qmnServicio.",".
	       $qmnMProducto.",".
	       $qmnIlimitado.",".
	       $qmnDosis.");\n";

	     if($esExtra)
		  $jsListar .= "CEEP(".qminimal($row["CodigoBarras"]).");\n";
	   }

	 $out .=  $jsLex->jsDump("L","xul",false);//vamos a defininir en fuera.
	 $out .=  $jsOut . $jsListar;

	 $generadorJSDeProductos = $out;
	 return $generadorJSDeProductos;

}





function getIdProductosSyncAlmacen($time,$IdLocal){

         $sql = 
	   " SELECT IdProducto ".
	   " FROM ges_almacenes ".
	   " WHERE ".
	   " UNIX_TIMESTAMP(FechaChange) > UNIX_TIMESTAMP() - ".$time.
	   " AND IdLocal = '".$IdLocal."'";
	 
	 $res = query($sql);
	 if (!$res) return 0;
	 $arr = array();
	 while($row = Row($res))
	   {
	     array_push($arr,$row["IdProducto"]);
	   }
	 return $arr;		
}

function QuitarDeCarritoTraspaso($id){
	
	if (!$id) {
		error(__FILE__.__LINE__,"no acepta nulo aqui");
		return;
	}
	
	$actual = getSesionDato("CarritoTrans");
	$mover = getSesionDato("CarritoMover");
	
	if (in_array($id,$actual)) {
	    //	  $actual = my_array_delete_by_value($actual,$id);
	    $clave = array_search($id, $actual); 
	    unset($actual[$clave]);
	    $actual = array_values($actual); 
	}
					
	unset($mover[$id]);	
		
	setSesionDato("CarritoMover",$mover);	
	setSesionDato("CarritoTrans",$actual);	
}

function GeneraNumDeTicket($IdLocal,$modoTicket){					
		switch($modoTicket){		
			case "interno":
				$serie = "IN";
				break;			
			case "cesion":		
				$serie = "CS";
				break;
			default: 			
				$serie = "B";
				break;
		}
										
		$miserie = $serie . $IdLocal;//Nos aseguramos de coger el valor correcto preguntando tambien por 		
			// ..la serie. Esto ayudara cuando un mismo local tenga mas de una serie, como va a ser el 
			// ..caso luego. 
		
		$sql = "SELECT Max(NComprobante) as NComprobanteMax FROM ges_comprobantes WHERE (IdLocal = '$IdLocal') AND (SerieComprobante='$miserie')";
		$row = queryrow($sql,"Numero actual de factura");
	
		if ($row){
			$numSerieTicketLocalActual =  intval($row["NComprobanteMax"]) + 1; 
		}	else {
			$numSerieTicketLocalActual = 0;
		}
		
		return $numSerieTicketLocalActual;	
}



class articulo extends Cursor {
	function SiguienteArticulo() {
		return $this->LoadNext();	
	}


	//Filtra repeticiones 
	function ListadoBase($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10){
		
		$IdProducto = CleanID($IdProducto);
		$indice = intval($indice);
		
		$Idioma = getSesionDato("IdLenguajeDefecto");
		
		if ($IdLocal)				
			$restriccion_local = "ges_almacenes.IdLocal = '$IdLocal' AND ";
		
		if ($IdProducto)
			$and_producto = "AND ges_almacenes.IdProducto = '$IdProducto'";
		
	
		
		$sql ="SELECT 
			DISTINCT(ges_productos.IdProdBase),
			ges_almacenes.IdProducto, 
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_almacenes.CostoUnitario,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,			
			ges_productos_idioma.Descripcion,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto $and_producto) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			$restriccion_local 
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0
			GROUP BY ges_productos.IdProdBase";
		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	


	function Listado($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10,$idbase=false){
		
		$IdProducto        = CleanID($IdProducto);
		$indice            = intval($indice);
		$Idioma            = getSesionDato("IdLenguajeDefecto");
		$restriccion_local = ($IdLocal)? "ges_almacenes.IdLocal = '$IdLocal' AND ":"";
		$and_producto      = ($IdProducto)? "AND ges_almacenes.IdProducto = '$IdProducto'":"";
		$restriccion_base  = ($idbase)? "ges_productos.IdProdBase = '$idbase' AND":"";
		
		$sql ="SELECT ges_almacenes.IdProducto, 
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.StockMin,
			ges_almacenes.CostoUnitario,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,
			ges_productos_idioma.Descripcion,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto $and_producto) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			$restriccion_local 
			$restriccion_base
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	
	

	function ListadoModular($IdLocal=false, $IdProducto=false,$indice=0,$tamPagina=10,$idbase=false,$nombre="",$soloLlenos =false,$obsoletos=false,$soloNS=false,$soloLote=false,$soloOferta=false,$idalias=false,$reservados=false){
		
		$IdProducto             = CleanID($IdProducto);
		$indice                 = intval($indice);
		$restriccion_local      = '';
		$restriccion_base       = '';
		$restriccion_stock      = '';
		$restriccion_ns         = '';
		$restriccion_lote       = '';
		$restriccion_oferta     = '';
		$restriccion_obsoletos  = '';			
		$restriccion_reservados = '';

		$Idioma = getSesionDato("IdLenguajeDefecto");
		
		$restriccion_local = "";
		if ($IdLocal)				
			$restriccion_local = "ges_almacenes.IdLocal = '$IdLocal' AND ";
		
		$and_producto = "";
		if ($IdProducto)
			$and_producto = "AND ges_almacenes.IdProducto = '$IdProducto'";

		if ($idalias)
		  $and_producto .= "AND (IdProductoAlias0 = '".$idalias."' OR IdProductoAlias1 ='".$idalias."')";
		else{
		  if ($nombre)
		    $and_producto .= "AND ges_productos_idioma.Descripcion  LIKE '%".$nombre."%' ";
		}    		
 
		$restriccion_base = "";
		if ($idbase)				
			$restriccion_base = "ges_productos.IdProdBase = '$idbase' AND";
		
		if($soloLlenos)
			$restriccion_stock = "ges_almacenes.Unidades != 0 AND ";

		if($soloNS)		        
			$restriccion_ns = "ges_productos.Serie != 0 AND ";

		if($soloLote)
			$restriccion_lote = "ges_productos.Lote != 0 AND ";

		if($soloOferta)
 			$restriccion_oferta = "ges_almacenes.Oferta != 0 AND ";

		if($reservados)
		        $restriccion_reservados = "ges_almacenes.DisponibleUnidades > 0 AND ";
	
		if (!$obsoletos) 
			$restriccion_obsoletos = " ges_productos.Obsoleto =0 AND ";
		
		
		$sql ="SELECT ges_almacenes.IdProducto,
			ges_productos.IdProdBase,  
			ges_almacenes.Id,
			ges_almacenes.Unidades,
			ges_almacenes.PrecioVenta,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
                        ges_almacenes.StatusNS,
			ges_almacenes.StockMin,
			ges_almacenes.CostoUnitario,
			ges_productos.Referencia,
			ges_productos.CodigoBarras,			
			ges_productos.VentaMenudeo,			
			ges_productos_idioma.Descripcion,
			ges_almacenes.Disponible,
			ges_locales.NombreComercial,
			ges_locales.Identificacion,
			ges_productos.IdTalla,
			ges_productos.IdColor,
            ges_contenedores.Contenedor,
            ges_productos.UnidadesPorContenedor,
			ges_productos.IdFamilia,
			ges_productos.IdMarca,
			ges_productos.IdSubFamilia,		
			ges_productos.Serie,
			ges_productos.UnidadMedida,
			ges_almacenes.IdLocal
			FROM (((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto ) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 	ges_productos_idioma.IdProdBase) INNER JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor			
			WHERE
			$restriccion_local 
			$restriccion_base
			$restriccion_stock
                        $restriccion_ns
                        $restriccion_lote
                        $restriccion_oferta
			$restriccion_obsoletos 			
			$restriccion_reservados
			ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0 $and_producto" .
			"ORDER BY ".
			" ges_productos_idioma.Descripcion ASC, " .
			" ges_productos.IdProdBase ASC, " .			
			" ges_locales.NombreComercial ASC \n\n";

		
		$res = $this->queryPagina($sql, $indice, $tamPagina+1);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
			return;		
		}								
	}	
	
	
	function Load($idArticulo){
		$Idioma = getSesionDato("IdLenguajeDefecto");
				
		$sql ="SELECT ges_almacenes.IdProducto, ges_almacenes.Id,
			ges_almacenes.IdLocal,
			ges_almacenes.Unidades,
			ges_almacenes.CostoUnitario,
			ges_almacenes.PrecioVenta,
			ges_almacenes.PVDDescontado,
			ges_almacenes.PrecioVentaCorporativo,
			ges_almacenes.PrecioVentaOferta,
			ges_almacenes.PVCDescontado,
			ges_almacenes.TipoImpuesto,
			ges_almacenes.Impuesto,
			ges_almacenes.Oferta,
			ges_almacenes.OfertaUnidades,
			ges_almacenes.StockMin,
			ges_almacenes.StockIlimitado,
			ges_almacenes.Disponible,
			ges_almacenes.DisponibleOnline,
			ges_almacenes.DisponibleUnidades,
			ges_productos.Referencia,
			ges_productos.IdLabHab,
			ges_productos.IdMarca,
			ges_productos.CodigoBarras,
			ges_productos_idioma.Descripcion,
			ges_locales.NombreComercial
		
			
			FROM ((ges_almacenes  INNER JOIN ges_locales ON ges_almacenes.IdLocal
			= ges_locales.IdLocal ) INNER JOIN ges_productos ON
			ges_almacenes.IdProducto = ges_productos.IdProducto ) INNER JOIN
			ges_productos_idioma ON ges_productos.IdProdBase = 
			ges_productos_idioma.IdProdBase
			
			WHERE
			ges_almacenes.Id = '$idArticulo'
			AND ges_productos_idioma.IdIdioma = '$Idioma'
			AND ges_productos.Eliminado = 0";
		$row = queryrow($sql);
		if (!$row){
			$this->Error(__FILE__ . __LINE__,"E: no puedo cargar '$idArticulo'");
			return false;			
		}
		$this->setId($idArticulo);
		$this->import($row);
		return true;
	}

	function Iconos(){ //Genera los iconos relativos al estado del articulo
		$out = "";
		if ($this->is("Oferta")) {
			$out .= "S";	
		} else
			$out .= "$";
			
		if ($this->get("Unidades")<=$this->get("StockMin")){
			$out .= "#";
		} else
			$out .= "+";
		
		if (!$this->is("Disponible")){
			$out .= "x";
		}else {
			$out .= "V";
		}
		
		return $out;		
	}

}



class almacenes {
	var $seleccionAlmacenes;

	

	function getSeleccion(){
		return 	$this->seleccionAlmacenes;
	}

	function fijarSeleccion($array){
		$this->seleccionAlmacenes = $array;
	}
	
	function crearSeleccionProductosDesdeRes($res){	
		if (!$res)
			return false;
			
		$out = array();
		while($row = Row($res)){
			array_push($out, $row["Id"]);
		}
		return $out;
	}
	
	function crearSeleccionAlmacenesDesdeRes($res){	
		if (!$res)
			return false;
			
		$out = array();
		while($row = Row($res)){
			array_push($out, $row["IdLocal"]);
		}
		return $out;
	}

	
	function crearSeleccionAlmacenes($IdProducto, $condicionWHERE="1") {
		$sql = "SELECT DISTINCT IdLocal FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		$out = $this->crearSeleccionAlmacenesDesdeRes($res);
		$this->seleccionAlmacenes = $out;	
		return $out;
	}

	function crearSeleccionProductos( $condicionWHERE = "1") {
		$sql = "SELECT Id FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		$out = $this->crearSeleccionProductosDesdeRes($res);
		$this->seleccionAlmacenes = $out;	
		return $out;
	}

	function crearListaProductos( $condicionWHERE = "1") {
		$sql = "SELECT * FROM ges_almacenes WHERE $condicionWHERE ORDER BY IdLocal ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		
		return $res;
	}
	
	function MeterProducto($oProducto,$extradatos){
		
	}
	
	
	function listaTodos(){						
		$out = $this->crearSeleccionAlmacenes("1");						
		return $out;			
	}

	function listaTodosConNombre(){						
		$sql = "SELECT IdLocal,NombreComercial FROM ges_locales  WHERE Eliminado=0 ORDER BY Identificacion ASC";
		
		$res = query($sql);
		if (!$res) {
			error (__FILE__ . __LINE__,"W: no se ha podido crear seleccion");
			return false;			
		}
		$out = array();
		
		while($row = Row($res)){
			$key = $row["IdLocal"];
			$value = $row["NombreComercial"];
			$out[$key] = $value;			
		}				
								
		return $out;			
	}

	function obtenerExistenciasKardex($id,$idlocal) {

	  $idlocal       = CleanID($idlocal);
	  $id 	         = CleanID($id);
	  $tabla         = obtenerInventarioProductoFifo($id,$idlocal);
	  $xunidades     = 0;

	  for($i = 0; $i< count($tabla);$i++)
	    {
	      $xunidades   += $tabla[$i][1];
	    }
	  return $xunidades;

	}

	function AgnadeCantidad($id,$agnadir,$idlocal) {

		$idlocal = CleanID($idlocal);
		$id    	 = CleanID($id);
		$agnadir = CleanFloat($agnadir);

		if (!($agnadir >= 0))  return false;

		$sql = "update ges_almacenes ".
		       "set    Unidades = $agnadir " .
		       "where  (IdLocal = '$idlocal') ".
		       "and    (IdProducto = '$id')";
		$res = query($sql,"Agnade unidades de articulo");

		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo agnadir cantidad");
			return false;
		}		
		return true;
	}

	function actualizaOfertaUnidades($id,$idlocal,$oferta) {

		if ( $oferta == 0 )  return false;

		$idlocal = CleanID($idlocal);
		$id    	 = CleanID($id);

		$sql     = 
		  " select OfertaUnidades".
		  " from   ges_almacenes ".
		  " where  IdProducto = '".$id."'".
		  " and    IdLocal    = '".$idlocal."'".
		  " limit 1";
		$row     = queryrow($sql);
		$ounid   = $row["OfertaUnidades"];
		
		$aoferta = explode("~", $oferta); //unidades~ofertaunid~pv~pvo
		$resto   = ( $ounid  > $aoferta[0] )? $ounid-$aoferta[0]:'0,Oferta=0';
 		//$soferta = ( $aoferta[0] > $aoferta[1] )? "0,Oferta=0":$resto;
		$soferta = "OfertaUnidades=".$resto;

		$sql = "update ges_almacenes ".
		       "set    $soferta " .
		       "where  (IdLocal = '$idlocal') ".
		       "and    (IdProducto = '$id')";
		$res = query($sql);

		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo agnadir cantidad");
			return false;
		}		
		return true;
	}
	

	function actualizarCosto($id,$idlocal){

	        $costounitario  = obtenerCostoUnitarioFifo($id,$idlocal); 
		$costounitario  = round($costounitario*100)/100;
		$igv            = getSesionDato("IGV");
		$idlocal 	= CleanID($idlocal);
		$id 		= CleanID($id);
		if (!$costounitario) return true;
		
		$sql = 
		  "UPDATE ges_almacenes ".
		  "SET    CostoUnitario = $costounitario, Impuesto= $igv " .
		  "WHERE  (IdLocal = '$idlocal') ".
		  "AND   (IdProducto = '$id') ";
		$res = query($sql,"Actualizar costo de articulo");

		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo actualizar costo del articulo");
			return false;
		}		
		return true;

	}

	function actualizaResumenKardex($resumenkardex,$idlocal,$id){

	  $resumenkardex = CleanText($resumenkardex); 
	  $id            = CleanID($id);
	  $idlocal       = CleanID($idlocal);
	  $sql 	         = "UPDATE ges_almacenes SET ResumenKardex = '$resumenkardex' " .
	                   "WHERE  (IdLocal = '$idlocal') AND (IdProducto = '$id') ";
	  $res 	         = query($sql,"Actualizar Resumen Kardex Almacen");

	  if (!$res){			
	    error(__FILE__ .  __LINE__ ,"E: no pudo agnadir Resumen");
	    return false;
	  }		
	  return true;
	}

	function actualizaEstadoInventario($idlocal,$id){

	  $id            = CleanID($id);
	  $idlocal       = CleanID($idlocal);
	  $sql 	         = "UPDATE ges_almacenes SET EstadoInventario = '1' " .
	                   "WHERE  (IdLocal = '$idlocal') AND (IdProducto = '$id') ";
	  $res 	         = query($sql,"Actualizar Estado Inventario Almacen");

	  if (!$res){			
	    error(__FILE__ .  __LINE__ ,"E: no pudo actualizar estado inventario");
	    return false;
	  }		
	  return true;
	}

	function actualizaEstadoInventarioLocal($idlocal){

	  $idlocal = CleanID($idlocal);
	  $sql 	   = "UPDATE ges_almacenes SET EstadoInventario = '0' " .
	             "WHERE  (IdLocal = '$idlocal')";
	  $res 	   = query($sql,"Actualizar Estado Inventario Local");

	  if (!$res){			
	    error(__FILE__ .  __LINE__ ,"E: no pudo actualizar estado inventario local");
	    return false;
	  }		
	  return true;
	}

	function RebajaCantidad($IdProducto,$unidadesQuitadas,$IdLocal) {
		$IdLocal 	  = CleanID($IdLocal);
		$IdProducto 	  = CleanID($IdProducto);
		$unidadesQuitadas = $unidadesQuitadas;
		$sql =
		  "UPDATE ges_almacenes SET Unidades = Unidades - $unidadesQuitadas " .
		  "WHERE (IdLocal = '$IdLocal') AND (IdProducto = '$IdProducto') ";		
		$res = query($sql,"Disminuir unidades de articulo");

		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo disminuir cantidad");
			return false;
		}		
		return true;
	}
		
	function ModificaCantidad($IdProducto,$unidadesModificar,$IdLocal) {

		$IdLocal 	   = CleanID($IdLocal);
		$IdProducto 	   = CleanID($IdProducto);
		$unidadesModificar = $unidadesModificar;
				
		$sql = 
		  "UPDATE ges_almacenes SET Unidades = (Unidades + ($unidadesModificar)) " .
		  "WHERE (IdLocal = '$IdLocal') AND (IdProducto = '$IdProducto') ";		

		$res = query($sql,"Modifica unidades de articulos");
		if (!$res){			
			error(__FILE__ .  __LINE__ ,"E: no pudo modificar cantidad");
			return false;
		}		
		return true;
	}



	function ApilaProducto($oProducto,$local,$unidades){

	        //CHON:Para no actualizar las unidades. las unidades los igualo a cero
	         $unidades =0;

		//Comprobar que no estaba
		$id = $oProducto->getId();
		
		//echo "Existe con anterioridad?<br>";
		$num = ContarFilas ("Almacen","(IdProducto='$id') AND (IdLocal ='$local')");
		if ($num) {
			//error(__FILE__ . __LINE__ ,"E: ya fue apilado");
			//return $this->AgnadeCantidad($id,$unidades,$local);
		} 		
		
		//TODO: no hay que negar esto?
		$esInventario = intval(getParametro("Inventario"));   
 		$tipoimpuesto = getTipoImpuesto($oProducto,$local);
 		
 		//error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
		
 		$datos = array(
				"IdLocal"=>$local,
				"IdProducto"=>$oProducto->getId(),
				"PrecioVenta"=>$oProducto->getPrecioVenta(),
				"Unidades"=>$unidades,
				"StockMin"=>0,
				"TipoImpuesto"=>$tipoimpuesto,
				"Impuesto" => $oProducto->get("Impuesto"),
				"StockIlimitado"=> $oProducto->get("Servicio"),
				"Disponible"=>1,
				"Oferta"=>0,
				);
				 				 		
 		$sql = CreaInsercion(false,$datos,"ges_almacenes"); 
		//"INSERT INTO dat_almacenes ($key) VALUES ($values)";
		
		query($sql,"Apilando producto en almacén");
			
	}
	
	function ApilaProductoTodos($oProducto,$unidades=0){
		
		$id = $oProducto->getId();
		
		//error(0,"Infodebug: id $id,".serialize($oProducto));		
		error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
		
		
		$listaTiendas = getSesionDato("ArrayTiendas");
		foreach ($listaTiendas as $tienda){
			$this->ApilaProducto($oProducto,$tienda,$unidades);
		}
	}

}

/*++++++++++++++ KARDEX ++++++++++++++++*/

function registrarPedidoKardexFifo($IdPedido,$IdPedidoDets,$IdAlmacenRecepcion,$Operacion=1,
				   $IdKardexAjusteOperacion=0,$IdInventario=0,$Obs=false){

    $productos = new producto;
    $almacenes = new almacenes;
    $res       = obtenerDetallePedidos($IdPedidoDets);
    
    while( $row= Row($res) ) 
      {

	$id          = $row['IdProducto'];
	$IdPedidoDet = $row['IdPedidoDet'];
	$costo       = $row['CostoUnidad'];
	$existencias = $almacenes->obtenerExistenciasKardex($id,$IdAlmacenRecepcion);

	registrarEntradaKardexFifo($id,$row['Unidades'],$costo,
				   $Operacion,$IdAlmacenRecepcion,
				   $IdPedidoDet,$existencias,
				   $IdKardexAjusteOperacion,$IdInventario,
				   0,$Obs);

	$almacenes->actualizarCosto($id,$IdAlmacenRecepcion);
	$productos->actualizarCosto($id,$costo);
	actualizaResumenKardex($id,$IdAlmacenRecepcion);
	actualizarSeries2PedidoDet($id,$IdPedidoDet," Estado = 'Almacen', Disponible = 1 ");
      } 
    //echo 1; 
}

function registrarAjusteEntradaKardex($IdPedido,$IdPedidoDet,$IdAlmacenRecepcion,$Operacion=1,
				     $IdKardexAjusteOperacion=0,$IdInventario=0,$Obs=false){

    $productos = new producto;
    $almacenes = new almacenes;
    $res       = obtenerDetallePedidoAjuste($IdPedidoDet);
    
    while( $row= Row($res) ) 
      {
	$id          = $row['IdProducto'];
	$costo       = $row['CostoUnidad'];
	$existencias = $almacenes->obtenerExistenciasKardex($id,$IdAlmacenRecepcion);

	registrarEntradaKardexFifo($id,$row['Unidades'],$costo,
				   $Operacion,$IdAlmacenRecepcion,
				   $row['IdPedidoDet'],$existencias,
				   $IdKardexAjusteOperacion,$IdInventario,
				   0,$Obs);

	$almacenes->actualizarCosto($id,$IdAlmacenRecepcion);
	$productos->actualizarCosto($id,$costo);
	actualizaResumenKardex($id,$IdAlmacenRecepcion);

	if($IdInventario)
	  $almacenes->actualizaEstadoInventario($IdAlmacenRecepcion,$id);
      } 
    //echo 1; 
}

function registrarAjusteSalidaKardex($IdComprobante,$Origen,$Operacion=1,
				     $IdKardexAjusteOperacion=0,
				     $IdInventario=0,$Obs=false){
    $productos = new producto;
    $almacenes = new almacenes;
    $res       = obtenerDetalleVentaAjuste($IdComprobante);
    
    while( $row= Row($res) ) 
      {

	$id               = $row['IdProducto'];
	$Costo            = $row['CostoUnitario'];
	$Cantidad         = $row['Cantidad'];
	$IdPedidoDet      = $row['IdPedidoDet'];
	$IdComprobanteDet = $row['IdComprobanteDet'];
	$existencias      = $almacenes->obtenerExistenciasKardex($id,$Origen);
	
	registrarSalidaKardexFifo($id,$Cantidad,$Costo,$Operacion,
				  $Origen,$IdPedidoDet,$IdComprobanteDet,
				  $existencias,$IdKardexAjusteOperacion,
				  $IdInventario,$Obs);

	$almacenes->actualizarCosto($id,$Origen);
	//$productos->actualizarCosto($id,$Costo);
	actualizaResumenKardex($id,$Origen);

	if($IdInventario)
	  $almacenes->actualizaEstadoInventario($Origen,$id);
      } 
    //echo 1; 
}

function registrarEntradaKardexFifo($id,$Cantidad,$Costo,$Operacion,
				    $IdLocal,$IdPedidoDet,$existencias,
				    $IdKardexAjusteOperacion=0,$IdInventario=0,
				    $IdComprobanteDet=0,$Obs=false){
 
        if( $Cantidad <= 0 && $IdInventario == 0 ) return;

	$User    = getSesionDato("IdUsuario");
	$CostAll = $Cantidad*$Costo;
	$Saldo   = $existencias+$Cantidad;
	
	$Keys    = " IdProducto ";
	$Values  = " '".$id."'";
	$Keys   .= ",FechaMovimiento";
	$Values .= ",NOW()";
	$Keys   .= ",IdKardexOperacion";
	$Values .= ",'".$Operacion."'";
	$Keys   .= ",TipoMovimiento";
	$Values .= ",'Entrada'";
	$Keys   .= ",CantidadMovimiento";
	$Values .= ",'".$Cantidad."'";
	$Keys   .= ",CostoUnitarioMovimiento";
	$Values .= ",'".$Costo."'";
	$Keys   .= ",CostoTotalMovimiento";
	$Values .= ",'".$CostAll."'";
	$Keys   .= ",IdLocal";
	$Values .= ",'".$IdLocal."'";
	$Keys   .= ",IdPedidoDet";
	$Values .= ",'".$IdPedidoDet."'";
	$Keys   .= ",IdComprobanteDet";
	$Values .= ",'".$IdComprobanteDet."'";
	$Keys   .= ",SaldoCantidad";
	$Values .= ",'".$Saldo."'";
	$Keys   .= ",Observaciones";
	$Values .= ",'".$Obs."'";
	$Keys   .= ",IdInventario";
	$Values .= ",'".$IdInventario."'";
	$Keys   .= ",IdKardexAjusteOperacion";
	$Values .= ",'".$IdKardexAjusteOperacion."'";
	$Keys   .= ",IdUsuario";
	$Values .= ",'".$User."'";
	
	$sql = "insert into ges_kardex (".$Keys.") values (".$Values.")";
	query($sql);
}

function actualizaResumenKardex($id,$idlocal) {

         $idlocal       = CleanID($idlocal);
	 $id 	        = CleanID($id);
	 $tabla         = obtenerInventarioProductoFifo($id,$idlocal);
	 $resumenkardex = '';  
	 $almacenes     = new almacenes;
	 $xunidades     = 0;

	 for($i = 0; $i< count($tabla);$i++)
	   {
	     //$costo       = $tabla[$i][0];
	     $unidades    = $tabla[$i][1];
	     $idpedidodet = $tabla[$i][2];
	     $dx          = ($i==0)? "":"~";
	     $resumenkardex .= $dx.$idpedidodet.":".$unidades;
	     $xunidades   += $unidades;
	   }

	 //Ordena pedidosdet por vencimiento
	 $resumenkardex = ordenarPedidosDet4Vencimiento($resumenkardex,$id,$idlocal);

	 $almacenes->actualizaResumenKardex($resumenkardex,$idlocal,$id);  
	 $almacenes->AgnadeCantidad($id,$xunidades,$idlocal);
}

function getResumenKardex4Ajuste($id,$idlocal) {

         $idlocal       = CleanID($idlocal);
	 $id 	        = CleanID($id);
	 $tabla         = obtenerInventarioProductoFifo($id,$idlocal);
	 $resumenkardex = '';  
	 $xunidades     = 0;

	 for($i = 0; $i< count($tabla);$i++)
	   {
	     //$costo       = $tabla[$i][0];
	     $unidades    = $tabla[$i][1];
	     $idpedidodet = $tabla[$i][2];
	     $dx          = ($i==0)? "":"~";
	     $resumenkardex .= $dx.$idpedidodet.":".$unidades;
	     $xunidades   += $unidades;
	   }

	 //Ordena pedidosdet por vencimiento
	 return $resumenkardex;
}

function ordenarPedidosDet4Vencimiento($resumen,$id,$idlocal){

  	$oProducto = new producto;

	if (!$oProducto->Load($id)) return $resumen;//Existe el Producto 		
	if (!$oProducto->get("FechaVencimiento")) return $resumen;//Meneja Vencimiento?

	//Obtiene resumen por fecha
	$Vence     = getPedidoDet2Kardex('VenceResumen',$resumen,$id,$idlocal);//~pedidodet:fecha
        $aResumen  = explode("~",  $resumen);
	$aVence    = explode("~", $Vence);
	$Fechas    = Array();
        $nResumen  = Array();
 
	//Resumen ordenado por fecha
	foreach ( $aVence as $pFecha )
	  {
	    $aFecha  = explode(":", $pFecha);
	    if( isset($aFecha[1]) )
	      array_push($Fechas,strtotime($aFecha[1]));
	  }
	$Fechas = search_merge_sort( $Fechas ); 
	
	//Resumen fechas ordenadas
	foreach ( $Fechas as $Fecha )
	  {
	    //Get IdPedido de Resumen fechas desordenadas
	    foreach ( $aVence as $pFecha )
	      {
		$aFecha  = explode(":", $pFecha);

		if( !isset($aFecha[1]) ) break;

		if( $Fecha==strtotime($aFecha[1]))
		  {
		    //Push Resumen Kardex
		    foreach ( $aResumen as $pPedido )
		      {
			$aPedido  = explode(":", $pPedido);
			
			if( $aPedido[0] == $aFecha[0] )
			  {
			    if ( !in_array($pPedido, $nResumen) )
			      array_push($nResumen,$pPedido);
			    break;
			  }
		      }
		    
		  }
	      }
	  }
	//Resumen ordenado por vencimiento 
	$resumen = implode("~", $nResumen);
	return  $resumen;
}

function obtenerCostoUnitarioFifo($IdProducto,$IdLocal){

         $sql =
	   " select CostoUnitarioMovimiento as Costo,".
           "        SUM(CantidadMovimiento) as Unidades".
           " from   ges_kardex".
           " where  IdProducto = '".$IdProducto."'".
           " and    IdLocal    = '".$IdLocal."'".
           " and    Eliminado  = 0".
           " group  by CostoUnitarioMovimiento ".
           " order  by Idkardex asc";
	 $res = query($sql);
	 if(!$res) return false;	

	 $costounitario = 0;
	 $existencias   = 0;
	 $costototal    = 0;
	 while($row= Row($res)) 
	   {			
	     $costo    = $row["Costo"];
	     $unidades = $row["Unidades"]; 
	     if($unidades!=0)
	       {
		 $existencias = $existencias + $unidades;
		 $costototal  = $costototal + $costo*$unidades;
	       }
	   }  
	 if($existencias!=0)
	   $costounitario = $costototal/$existencias;
	 
	 return $costounitario;
}

function actualizarPreciosVentaAlmacen($idproducto,$pvd,$idlocal){

         $sql = 
	   "UPDATE ges_almacenes ".
	   "SET    PrecioVenta   = '".$pvd."', ".
	   "       PVDDescontado = '".$pvd."', ".
	   "       PrecioVentaCorporativo = '".$pvd."',".
	   "       PVCDescontado = '".$pvd."' ".
	   "WHERE  IdProducto = '".$idproducto."' ".
	   "AND    IdLocal = '".$idlocal."'";
	 $res = query($sql);
}


function obtenerKardexMovimientosProducto($idproducto,$idlocal,$desde,
					  $hasta,$xope,$xmov){

         $desde  = CleanFechaES($desde);
	 $hasta  = CleanFechaES($hasta);
	 $extra  = ( $xope )? "AND ges_kardex.IdKardexOperacion = '".$xope."' ":"";
	 $extra .= ( $xmov )? "AND ges_kardex.TipoMovimiento = '".$xmov."' ":"";
	 $sql    = 
	   "SELECT DATE_FORMAT(FechaMovimiento, '%e %b %y  %H:%i') as FechaMovimiento,".
	   "       KardexOperacion,".
	   "       IdPedidoDet,".
	   "       IdComprobanteDet,".
	   "       CantidadMovimiento,".
	   "       ROUND(CostoUnitarioMovimiento,2) as CostoUnitarioMovimiento,".
	   "       ROUND(CostoTotalMovimiento,2) as CostoTotalMovimiento,".
	   "       ges_usuarios.Nombre, ".
	   "       SaldoCantidad, ".
	   "       TipoMovimiento, ".
	   "       ges_contenedores.Contenedor as Cont, ".
	   "       ges_productos.UnidadMedida as Unid, ".
	   "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_kardex.IdKardexAjusteOperacion,".
	   "       ges_kardex.IdInventario ".
	   "FROM   ges_kardex,ges_usuarios,ges_kardexoperacion,".
	   "       ges_productos,ges_contenedores ".
	   "WHERE  ges_kardex.IdProducto ='$idproducto' ".
	   "AND    ges_usuarios.IdUsuario = ges_kardex.IdUsuario ".
	   "AND    ges_productos.IdProducto = ges_kardex.IdProducto ".
	   "AND    ges_contenedores.IdContenedor = ges_productos.IdContenedor ".
	   "AND    ges_kardex.IdKardexOperacion = ges_kardexoperacion.IdKardexOperacion ".
	   "AND    IdLocal='$idlocal' ".
	   "AND    FechaMovimiento>= '$desde'  ".
	   "AND    FechaMovimiento<= ADDDATE('$hasta',1) ".
	   "AND    ges_kardex.Eliminado=0 ".
	   $extra.
	   "ORDER  BY IdKardex DESC";
	 $res   = query($sql);
	 $tabla = array();

	 while($row= Row($res)) {

	   $detalle     = "";
	   $fila        = array();
	   $idped       = $row["IdPedidoDet"];
	   $idcom       = $row["IdComprobanteDet"];
	   $tmovi       = $row["TipoMovimiento"];
	   $idaju       = $row["IdKardexAjusteOperacion"];
	   $menudeo     = ($row["VentaMenudeo"])? $row["UnidxCont"].$row["Unid"]." x ".$row["Cont"]:false;
	   $mkardex     = ($idped)? 'Pedido':'';
	   $mkardex     = ($idcom)? 'Comprobante':$mkardex;
	   $idx         = ($idped)? $idped:'';
	   $idx         = ($idcom)? $idcom:$idx;

	   //Menundeo
	   $unidresto   = ($menudeo)? $row["CantidadMovimiento"]%$row["UnidxCont"]:0; 
	   $unidempaque = ($menudeo)? ($row["CantidadMovimiento"]-$unidresto)/$row["UnidxCont"]:0;
	   $unidmenudeo = ($menudeo)? $unidempaque." ".$row["Cont"]." + ".$unidresto:0;
	   $existencias = ($menudeo)? $unidmenudeo:$row["CantidadMovimiento"]; 

	   //Saldo
	   $saldoresto  = ($menudeo)? $row["SaldoCantidad"]%$row["UnidxCont"]:0; 
	   $unidempaque = ($menudeo)? ($row["SaldoCantidad"]-$saldoresto)/$row["UnidxCont"]:0;
	   $unidmenudeo = ($menudeo)? $unidempaque." ".$row["Cont"]." + ".$saldoresto:0;
	   $saldo       = ($menudeo)? $unidmenudeo:$row["SaldoCantidad"]; 

	   $kdxop       = $row["KardexOperacion"];
	   $mkardex     = obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju);


	   array_push($fila, $row["FechaMovimiento"]);
	   array_push($fila, $row["KardexOperacion"].$mkardex["Motivo"]);
	   array_push($fila, $mkardex["Documento"]);
	   array_push($fila, $existencias);
	   array_push($fila, $row["CostoUnitarioMovimiento"]);
	   array_push($fila, $row["CostoTotalMovimiento"]);
	   array_push($fila, $row["Nombre"]);
	   array_push($fila, $mkardex["Detalle"]);
	   array_push($fila, $saldo);
	   array_push($fila, $tmovi);
	   //array_push($fila, $existencias);

	   array_push($tabla,implode(",",$fila));

	 }    
	 return implode(";",$tabla);
}



function obtenerKardexMovimientos($idlocal,$desde,$hasta,$xope,$xfamilia,
				  $xmarca,$xmov,$xnombre,$xcodigo,$listadesde,
				  $numerofilas,$mcount){

         $desde  = CleanFechaES($desde);
	 $hasta  = CleanFechaES($hasta);
	 $extra  = ( $xnombre )? " AND ges_productos_idioma.Descripcion  LIKE '%".$xnombre."%' ":"";
	 $extra .= ( $xmarca   != "0" )? " AND ges_marcas.IdMarca = '".$xmarca."' ":"";
	 $extra .= ( $xfamilia != "0" )? " AND ges_familias.IdFamilia = '".$xfamilia."' ":"";
	 $extra  = ( $xcodigo )? " AND ges_productos.CodigoBarras LIKE '".$xcodigo."' ":$extra;
	 $extra .= ( $xope )?    " AND ges_kardex.IdKardexOperacion = '".$xope."' ":"";
	 $extra .= ( $xmov )?    " AND ges_kardex.TipoMovimiento = '".$xmov."' ":"";
	 $extra .= ( $idlocal != "0")? " AND ges_kardex.IdLocal='".$idlocal."' ":"";

	 $extraLimit = ($listadesde >= 0 && $numerofilas>0)? " LIMIT ".$listadesde.",".$numerofilas:"";

 	 $sql    = 
	   "SELECT IdKardex,".
	   "       DATE_FORMAT(FechaMovimiento, '%e %b %y  %H:%i') as FechaMovimiento,".
	   "       KardexOperacion,".
	   "       CantidadMovimiento,".
	   "       ROUND(CostoUnitarioMovimiento,2) as CostoUnitarioMovimiento,".
	   "       ROUND(CostoTotalMovimiento,2) as CostoTotalMovimiento,".
	   "       ges_usuarios.Nombre as Usuario, ".
	   "       SaldoCantidad, ".
	   "       TipoMovimiento, ".
	   "       CONCAT(ges_productos.CodigoBarras,' ',ges_productos_idioma.Descripcion,' ',".
	   "       ges_marcas.Marca,' ',".
	   "       ges_colores.Color,' ',".
	   "       ges_tallas.Talla,' ',".
	   "       ges_laboratorios.NombreComercial) as Producto, ".
	   "       ges_locales.NombreComercial as Almacen, ".
	   "       IdPedidoDet,".
	   "       ges_kardex.IdProducto,".
	   "       ges_kardex.IdComprobanteDet,".
	   "       ges_kardex.IdLocal, ".
	   "       ges_contenedores.Contenedor as Cont, ".
	   "       ges_productos.UnidadMedida as Unid, ".
	   "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_kardex.IdKardexAjusteOperacion,".
	   "       ges_kardex.IdInventario ".
	   "FROM   ges_kardex ".
	   "LEFT   JOIN ges_productos ON ges_kardex.IdProducto = ges_productos.IdProducto ".
	   "INNER  JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "INNER  JOIN ges_tallas       ON ges_productos.IdTalla  = ges_tallas.IdTalla ".
	   "INNER  JOIN ges_colores      ON ges_productos.IdColor  = ges_colores.IdColor ".
	   "INNER  JOIN ges_familias     ON ges_productos.IdFamilia = ges_familias.IdFamilia ".
	   "INNER  JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	   "INNER  JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	   "INNER  JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
	   "INNER  JOIN ges_usuarios     ON ges_usuarios.IdUsuario = ges_kardex.IdUsuario ".
	   "INNER  JOIN ges_locales      ON ges_locales.IdLocal    = ges_kardex.IdLocal ".
	   "INNER  JOIN ges_kardexoperacion ON ".
	   "       ges_kardex.IdKardexOperacion = ges_kardexoperacion.IdKardexOperacion ".
	   "WHERE  FechaMovimiento>= '$desde'  ".
	   "AND    FechaMovimiento<= ADDDATE('$hasta',1) ".
	   "AND    ges_kardex.Eliminado=0 ".
	   $extra.
	   "ORDER  BY IdKardex DESC ".$extraLimit;

	 if ($mcount) return  nroRows($sql);

	 $res = query($sql);
	 if (!$res) return false;
	 $OrdenKardex = array();
	 $t = 0;

	 while($row = Row($res))
	   {
	     $detalle  = "";
	     $idped    = $row["IdPedidoDet"];
	     $kdxop    = $row["KardexOperacion"];
	     $idcom    = $row["IdComprobanteDet"];
	     $idaju    = $row["IdKardexAjusteOperacion"];
	     $menudeo  = ($row["VentaMenudeo"])? $row["UnidxCont"].$row["Unid"]." x ".$row["Cont"]:false;
	     $mkardex  = ($idped)? 'Pedido':false;
	     $mkardex  = ($idcom)? 'Comprobante':$mkardex;
	     $idx      = ($idped)? $idped:'';
	     $idx      = ($idcom)? $idcom:$idx;
	     $arkdx    = obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju);
	     
	     $row["KardexOperacion"] = $kdxop.$arkdx["Motivo"];
	     $row["Documento"]       = $arkdx["Documento"];
	     $row["Detalle"]         = $arkdx["Detalle"];
	     $nombre                 = "Operacion_" . $t++;
	     $OrdenKardex[$nombre]   = $row; 
	     
	   }	
	 return $OrdenKardex;

}


function obtenerKardexMovimientosInventario($idlocal,$desde,$hasta,$xfamilia,
					    $xmarca,$xope,$xmov,$xnombre,
					    $xcodigo,$xinvent,$esInvent,
					    $print=false,$selcvs=false,$numerofilas,
					    $listadesde,$mcount){

         $xinvent = ($xinvent)? $xinvent:'none';
         $desde   = CleanFechaES($desde);
	 $hasta   = CleanFechaES($hasta);
	 $fecha   = " AND  FechaMovimiento>= '$desde' AND  FechaMovimiento<= ADDDATE('$hasta',1) ";

	 $extra  = ( $esInvent )? " AND ges_kardex.IdInventario = '".$xinvent."' ":"";
	 $extra .= ( $esInvent )? "":$fecha;
	 $extra .= ( $xnombre  )? " AND ges_productos_idioma.Descripcion  LIKE '%".$xnombre."%' ":"";
	 $extra .= ( $xmarca   != "0" )? " AND ges_marcas.IdMarca = '".$xmarca."' ":"";
	 $extra .= ( $xfamilia != "0" )? " AND ges_familias.IdFamilia = '".$xfamilia."' ":"";
	 $extra  = ( $xcodigo  )? " AND ges_productos.CodigoBarras LIKE '".$xcodigo."' ":$extra;
	 $extra .= ( $xope     )? " AND ges_kardex.IdKardexOperacion = '".$xope."' ":"";
	 $extra .= ( $xmov     )? " AND ges_kardex.TipoMovimiento = '".$xmov."' ":"";
	 $extra .= ( $idlocal  != "0" )? " AND ges_kardex.IdLocal = '".$idlocal."' ":"";
	 $extraLimit = ($listadesde >= 0 && $numerofilas>0)? " LIMIT ".$listadesde.",".$numerofilas:"";

	 $selsql =
	   "       IdKardex,".
	   "       DATE_FORMAT(FechaMovimiento, '%e %b %y  %H:%i') as FechaMovimiento,".
	   "       KardexOperacion,".
	   "       CantidadMovimiento,".
	   "       ROUND(CostoUnitarioMovimiento,2) as CostoUnitarioMovimiento,".
	   "       ROUND(CostoTotalMovimiento,2) as CostoTotalMovimiento,".
	   "       ges_usuarios.Nombre as Usuario, ".
	   "       SaldoCantidad, ".
	   "       TipoMovimiento, ".
	   "       CONCAT(ges_productos.CodigoBarras,' ',ges_productos_idioma.Descripcion,' ',".
	   "       ges_marcas.Marca,' ',".
	   "       ges_colores.Color,' ',".
	   "       ges_tallas.Talla,' ',".
	   "       ges_laboratorios.NombreComercial) as Producto, ".
	   "       ges_locales.NombreComercial as Almacen, ".
	   "       IdPedidoDet,".
	   "       ges_kardex.IdProducto,".
	   "       IdComprobanteDet,".
	   "       ges_kardex.IdLocal, ".
	   "       ges_contenedores.Contenedor as Cont, ".
	   "       ges_productos.UnidadMedida as Unid, ".
	   "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_kardex.IdKardexAjusteOperacion,".
	   "       ges_kardex.IdInventario, ".
           "       IF ( ges_kardex.Observaciones like '', ' ',ges_kardex.Observaciones) ".
	   "       as Observaciones ";

	 $xsel = ($selcvs)? $selcvs:$selsql;
	 $sql  = 
	   "SELECT ".$xsel.
	   "FROM   ges_kardex ".
	   "LEFT   JOIN ges_productos ON ges_kardex.IdProducto = ges_productos.IdProducto ".
	   "INNER  JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "INNER  JOIN ges_tallas       ON ges_productos.IdTalla  = ges_tallas.IdTalla ".
	   "INNER  JOIN ges_colores      ON ges_productos.IdColor  = ges_colores.IdColor ".
	   "INNER  JOIN ges_familias     ON ges_productos.IdFamilia = ges_familias.IdFamilia ".
	   "INNER  JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	   "INNER  JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	   "INNER  JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
	   "INNER  JOIN ges_usuarios     ON ges_usuarios.IdUsuario = ges_kardex.IdUsuario ".
	   "INNER  JOIN ges_locales      ON ges_locales.IdLocal    = ges_kardex.IdLocal ".
	   "INNER  JOIN ges_kardexoperacion ON ".
	   "       ges_kardex.IdKardexOperacion = ges_kardexoperacion.IdKardexOperacion ".
	   "WHERE  ges_kardex.Eliminado=0 ".
	   $extra.
	   "ORDER  BY IdKardex DESC ".$extraLimit;

	 if ($selcvs) return $sql;    // Exportar CVS
	 if ($mcount) return  nroRows($sql); // Numero de filas

	 $res = query($sql);

	 if (!$res) return false;
	 if ($print) return $res;  // Exportar PDF

	 $OrdenKardex = array();
	 $t = 0;

	 while($row = Row($res))
	   {
	     $detalle  = "";
	     $idped    = $row["IdPedidoDet"];
	     $kdxop    = $row["KardexOperacion"];
	     $idcom    = $row["IdComprobanteDet"];
	     $idaju    = $row["IdKardexAjusteOperacion"];
	     $menudeo  = ($row["VentaMenudeo"])? $row["UnidxCont"].$row["Unid"]." x ".$row["Cont"]:false;
	     $mkardex  = ($idped)? 'Pedido':false;
	     $mkardex  = ($idcom)? 'Comprobante':$mkardex;
	     $idx      = ($idped)? $idped:'';
	     $idx      = ($idcom)? $idcom:$idx;
	     $arkdx    = obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju);
	     
	     $row["KardexOperacion"] = $kdxop.$arkdx["Motivo"];
	     $row["Documento"]       = $arkdx["Documento"];
	     $row["Detalle"]         = $arkdx["Detalle"];
	     $nombre                 = "Operacion_" . $t++;
	     $OrdenKardex[$nombre]   = $row; 
	     
	   }	
	 return $OrdenKardex;

}


function obtenerKardexInventarioAlmacen($idlocal,$xfamilia,$xmarca,$xstock,
					$xnombre,$xcodigo,$esInvent){
	   
	 $extra  = ( $xmarca   != "0" )? " AND ges_marcas.IdMarca = '".$xmarca."' ":"";
	 $extra .= ( $xfamilia != "0" )? " AND ges_familias.IdFamilia = '".$xfamilia."' ":"";
	 $extra .= ( $xstock   == "1" )? " AND ges_almacenes.Unidades > 0 ":"";
	 $extra .= ( $xstock   == "2" )? " AND ges_almacenes.Unidades = 0 ":"";
	 $extra .= ( $esInvent)? " AND ges_almacenes.EstadoInventario = 0 ":"";
	 $extra .= ( $xnombre )? " AND ges_productos_idioma.Descripcion  LIKE '%".$xnombre."%' ":"";
	 $extra  = ( $xcodigo )? " AND ges_productos.CodigoBarras LIKE '".$xcodigo."' ":$extra;
	 $extra .= ( $idlocal  != "0" )? " AND ges_almacenes.IdLocal = '".$idlocal."' ":"";
	 $sql    = 
	   "SELECT ges_almacenes.Id,ges_almacenes.IdProducto,ges_almacenes.IdLocal,".
	   "       DATE_FORMAT(ges_almacenes.FechaChange, '%e %b %y  %H:%i') as FechaMovimiento,".
	   "       ges_almacenes.Unidades, ".
	   "       ROUND(CostoUnitario,2) as Costo,".
	   "       ROUND(PrecioVenta,2)   as PVD,".
	   "       ROUND(PVDDescontado,2) as PVDD,".
	   "       ROUND(PrecioVentaCorporativo,2) as PVC,".
	   "       ROUND(PVCDescontado,2) as PVCD,".
	   "       CONCAT(ges_productos.CodigoBarras,' ',ges_productos_idioma.Descripcion,' ',".
	   "       ges_marcas.Marca,' ',".
	   "       ges_colores.Color,' ',".
	   "       ges_tallas.Talla,' ',".
	   "       ges_laboratorios.NombreComercial) as Producto,".
	   "       ges_locales.NombreComercial as Almacen,".
	   "       IF( ges_almacenes.ResumenKardex like '', ' ', ges_almacenes.ResumenKardex) ".
	   "       as ResumenKardex,".
	   "       ges_contenedores.Contenedor as Cont, ".
	   "       ges_productos.UnidadMedida as Unid, ".
	   "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
	   "       ges_productos.VentaMenudeo, ".
	   "       ges_productos.Serie, ".
	   "       ges_productos.Lote, ".
	   "       ges_productos.FechaVencimiento ".
	   "FROM   ges_almacenes ".
	   "LEFT   JOIN ges_productos ON ges_almacenes.IdProducto = ges_productos.IdProducto ".
	   "INNER  JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	   "INNER  JOIN ges_tallas       ON ges_productos.IdTalla   = ges_tallas.IdTalla ".
	   "INNER  JOIN ges_colores      ON ges_productos.IdColor   = ges_colores.IdColor ".
	   "INNER  JOIN ges_laboratorios ON ges_productos.IdLabHab  = ges_laboratorios.IdLaboratorio ".
	   "INNER  JOIN ges_marcas       ON ges_productos.IdMarca   = ges_marcas.IdMarca ".
	   "INNER  JOIN ges_familias     ON ges_productos.IdFamilia = ges_familias.IdFamilia ".
	   "INNER  JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
	   "INNER  JOIN ges_locales      ON ges_locales.IdLocal    = ges_almacenes.IdLocal ".
	   "WHERE  ges_almacenes.Eliminado      = 0 ".
	   "AND    ges_productos.MetaProducto   = 0 ".
	   "AND    ges_productos.Servicio       = 0 ".
	   "AND    ges_almacenes.StockIlimitado = 0 ".
	   $extra.
	   "ORDER  BY ges_productos_idioma.Descripcion DESC";

	 $res = query($sql);
	 if (!$res) return false;
	 $OrdenKardex = array();
	 $t = 0;

	 while($row = Row($res))
	   {
	     $nombre                 = "Operacion_" . $t++;
	     $OrdenKardex[$nombre]   = $row; 
	     
	   }	
	 return $OrdenKardex;

}

function obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju){
         
         switch($mkardex) {
	 case "Pedido":
	   $mkdx = obtenerDocumentoPedidoDet($idx,$kdxop);
	   break;
	 case "Comprobante":
	   $mkdx = obtenerDocumentoComprobanteDet($idx,$kdxop);
	   break;
	 }
	 $detalle  = '';
	 $detalle .= ( $mkdx["fv"] != '0000-00-00' )? " FV. ".$mkdx["fv"]:"";
	 $detalle .= ( $mkdx["lt"] != '' )? " LT. ".$mkdx["lt"]:"";
	 $detalle .= ( $mkdx["ns"] != '0')? " NS.":"";
	 $detalle .= ( $menudeo )? "  ".$menudeo.".":" ";
	 $motivo   = ( $mkdx["mv"] != '0')? " - ".getmotivoAlbaran($mkdx["mv"],$idaju):"";
	 
	 $mkdx["Documento"] = $mkdx["Documento"];
	 $mkdx["Detalle"]   = $detalle;
	 $mkdx["Motivo"]    = $motivo;
	 return $mkdx;
}

//FALTA ARREGLAR
function obtenerCostoParaDevolucion($id,$idproducto){
    $sql = "SELECT CostoUnitarioMovimiento FROM ges_kardex WHERE IdProducto = '$idproducto' AND TipoDetalle = 9 AND DocumentoReferencia = '$id' AND TipoMovimiento = 1";
    $res = query($sql);
    $row = Row($res);
    return $row["CostoUnitarioMovimiento"];

}

function obtenerIdComprobante($ncomprobante,$seriecomprobante){

        $sql = 
	  "SELECT IdComprobante ".
	  "FROM   ges_comprobantes ".
	  "WHERE  SerieComprobante = '".$seriecomprobante."' ".
	  "AND    NComprobante     = '".$ncomprobante."'";
	$res = query($sql);
	$row = Row($res);
	return $row["IdComprobante"];
}

function registrarSalidaKardexFifo($id,$Cantidad,$Costo,$Operacion,$IdLocal,
				   $IdPedidoDet,$IdComprobanteDet,$existencias,
				   $IdKardexAjusteOperacion=0,$IdInventario=0,
				   $Obs=false){

        if( $Cantidad <= 0 ) return;

	 $User    = getSesionDato("IdUsuario");
	 $CostAll = $Cantidad*$Costo;
	 $Cant    = $Cantidad*(-1);
	 $Saldo   = $existencias-$Cantidad;

	 $Keys    = " IdProducto,"; 
	 $Values  = "'".$id."',";
	 $Keys   .= " FechaMovimiento,"; 
	 $Values .= " NOW(),";
	 $Keys   .= " IdKardexOperacion,";
	 $Values .= "'".$Operacion."',";
	 $Keys   .= " TipoMovimiento,";
	 $Values .= "'Salida',";
	 $Keys   .= " CantidadMovimiento,"; 
	 $Values .= "'".$Cant."',";
	 $Keys   .= " CostoUnitarioMovimiento,"; 
	 $Values .= "'".$Costo."',";
	 $Keys   .= " CostoTotalMovimiento,"; 
	 $Values .= "'".$CostAll."',";
	 $Keys   .= " IdLocal,";
	 $Values .= "'".$IdLocal."',";
	 $Keys   .= "IdPedidoDet,";
	 $Values .= " '".$IdPedidoDet."',";
	 $Keys   .= "IdComprobanteDet,";
	 $Values .= "'".$IdComprobanteDet."',";
	 $Keys   .= "SaldoCantidad,";
	 $Values .= "'".$Saldo."',";
	 $Keys   .= "Observaciones,";
	 $Values .= "'".$Obs."',";
	 $Keys   .= "IdInventario,";
	 $Values .= "'".$IdInventario."',";
	 $Keys   .= "IdKardexAjusteOperacion,";
	 $Values .= "'".$IdKardexAjusteOperacion."',";
	 $Keys   .= "IdUsuario"; 
	 $Values .= "'".$User."'";
	 $sql     = "insert into ges_kardex (".$Keys.") value (".$Values.")";
         query($sql);

}
function actualizaIdInventarioToKardex($IdLocal,$IdInventario,$IdProducto){

         //Idkardex
	 $sql = 
	   " select * ".
	   " from   ges_kardex ".
	   " where  IdProducto = '".$IdProducto."'".
	   " and    IdLocal    = '".$IdLocal."'".
	   " order  by IdKardex desc limit 1";
	 $row  = queryrow($sql);

	 if (!$row)
	   return false;

	 $IdProducto       = $row["IdProducto"];
	 $Operacion        = 6;//Inventario
	 $Costo            = $row["CostoUnitarioMovimiento"];
	 $existencias      = $row["SaldoCantidad"];
	 $IdPedidoDet      = $row["IdPedidoDet"];
	 $IdLocal          = $row["IdLocal"];
	 $IdComprobanteDet = $row["IdComprobanteDet"];
	 $AjusteOperacion  = $row["IdKardexAjusteOperacion"];

	 registrarEntradaKardexFifo($IdProducto,0,$Costo,$Operacion,
				    $IdLocal,$IdPedidoDet,$existencias,
				    $AjusteOperacion,$IdInventario,
				    0,false);
}

function getIdProducto2Articulo($id){
	$id  = CleanID($id);
	$row = queryrow(" SELECT IdProducto ".
			" FROM   ges_almacenes ".
			" WHERE  Id='$id'");
	return $row["IdProducto"];	
}

function getResumenKardex2Articulo($id){
	$id  = CleanID($id);
	$row = queryrow(" select ResumenKardex,CostoUnitario ".
			" from   ges_almacenes ".
			" where  Id='$id'");
	return $row["CostoUnitario"].'~'.$row["ResumenKardex"];	
}

function getResumenKardex2Producto($id,$local){

	$id    = CleanID($id);
	$local = CleanID($local);
	$sql   = 
	  " select ResumenKardex ".
	  " from   ges_almacenes ".
	  " where  IdLocal    = '".$local."'".
	  " and    IdProducto = '".$id."'".
	  " and    Disponible = '1'".
	  " and    Eliminado  = '0' ";

	$row = queryrow($sql);
	if (!$row)
	  return false;
	return $row["ResumenKardex"]; 	
}


function getfichatecnica2Producto($xproducto){

  $sql = 
    " select concat(Indicacion,'&',ContraIndicacion,".
    "        '&',Interaccion,'&',Dosificacion) as FichaTecnica ".
    " from  ges_productosinformacion ".
    " where IdProducto = '".$xproducto."'" .
    " and   Eliminado  = 0 ";

  $row = queryrow($sql);
  if (!$row)  return false;
  return $row["FichaTecnica"]; 	
}

function getPedidoDet2Kardex($xdato,$rkdx,$xproducto,$xlocal){

  //rkdx = IdPedidoDet:Unidades~IdPedidoDet:Unidades
  $akdx  = explode("~", $rkdx);
  $xres  = '';
  $srt   = '';
  foreach ( $akdx as $Pedido )
    {

      $aPedido = explode(":", $Pedido);
      $xres   .= $srt.getResumen2PedidoDet($xdato,$xproducto,$aPedido[0],$xlocal);
      $srt     = '~';//($xdato != 'Serie')?'~':'';

    }

  if($xres)
    return $xres;//retorna cadena

  //Brutal
  return false;
}


function getResumen2PedidoDet($xdato,$xproducto,$xpedidodet,$xlocal){

  switch($xdato){		
    
  case "Serie":
    $xcmd = getSerie2PedidoDet($xpedidodet,$xproducto); 
    break;
    
  case "Lote":
    $xcmd = getColumn2PedidoDet('Lote',$xpedidodet,$xproducto,'Lote'); 
    
    break;

  case "Vence":
    $xsql = " DATE_FORMAT(FechaVencimiento, '%e/%m/%y') as FechaVencimiento ";
    $xcmd = getColumn2PedidoDet($xsql,$xpedidodet,$xproducto,"FechaVencimiento"); 
    break;

  case "VenceResumen":
    $xsql = " FechaVencimiento ";
    $xcmd = getColumn2PedidoDet($xsql,$xpedidodet,$xproducto,"FechaVencimiento"); 
    break;

  }

  
  return $xcmd;

}

function getColumn2PedidoDet($xsql,$xpedidodet,$xproducto,$xcampo){

  $sql = 
    "select ".$xsql." ".
    "from   ges_pedidosdet ".
    "where  IdPedidoDet = '".$xpedidodet."' ".
    "and    IdProducto  = '".$xproducto."' ".
    "and    Eliminado   = 0";
  $row = queryrow($sql);
  if (!$row)
    return false;
  return $xpedidodet.":".$row[$xcampo];

}

function getIdBase2IdAlmacen($id){

         $pid = getIdProducto2Articulo($id);
	 $sql = " select IdProdBase ".
	        " from   ges_productos ".
	        " where  IdProducto = '$pid'";
	 $row = queryrow($sql);
	 
	 return $row["IdProdBase"];	
}

function getIdFromAlmacen($id,$local) {

	 $id    = CleanID($id);
	 $local = CleanID($local);
	 $sql   = " select Id from ges_almacenes ".
  	          " where  (IdLocal='$local') ".
	          " and    (IdProducto='$id')";	
	 $row = queryrow($sql);

	 if (!$row) return false;
	
	 return $row["Id"];	
}

function getmotivoAlbaran($xid,$idaju=false){

	 $xid = CleanID($xid);

	 if(!$idaju)
	   {
	     $sql = 
	       " select MotivoAlbaran ".
	       " from   ges_motivoalbaran ".
	       " where  IdMotivoAlbaran = '".$xid."'";
	     $row = queryrow($sql);
	     
	     if (!$row) 
	       return false;
	     return $row["MotivoAlbaran"];
	 }

	 if($idaju)
	   {
	     $sql =
	       " select AjusteOperacion ".
	       " from   ges_kardexajusteoperacion ".
	       " where  IdKardexAjusteOperacion = '".$idaju."'";
	     $row = queryrow($sql);
	     
	     if (!$row) 
	       return false;
	     return $row["AjusteOperacion"]; 
	   }

}

function registrarPreciosVentaAlmacen($PVD,$PVDD,$PVC,$PVCD,$IdArticulo){
         $sql = 
	   " update ges_almacenes".
	   " set    PrecioVenta            = ".$PVD.",".
	   "        PrecioVentaCorporativo = ".$PVC.",".
	   "        PVDDescontado          = ".$PVDD.",".
	   "        PVCDescontado          = ".$PVCD." ".
	   " where  (Id = '".$IdArticulo."')";
	 query($sql);
}

function registrarPreciosVentaAlmacenProducto($PVD,$PVDD,$PVC,$PVCD,$IdProducto){
         $sql = 
	   " update ges_almacenes".
	   " set    PrecioVenta            = ".$PVD.",".
	   "        PrecioVentaCorporativo = ".$PVC.",".
	   "        PVDDescontado          = ".$PVDD.",".
	   "        PVCDescontado          = ".$PVCD." ".
	   " where  (IdProducto = '".$IdProducto."')";
	 query($sql);
}

function registraCambiosInventario($IdInventario,$ValuexKey){

         $IdInventario = CleanID($IdInventario);

         $sql = 
	   " update ges_inventario ".
	   " set    ".$ValuexKey.
	   " where  (IdInventario = '".$IdInventario."')";
	 query($sql);
}
function getIdInventario($IdLocal){
         //Control
	 $sql =
	   " select IdInventario ".
	   " from   ges_inventario ".
	   " where  IdLocal = '".$IdLocal."'".
	   " and    Estado  = 'Pendiente'";
	 $row = queryrow($sql);
	 
	 if (!$row)
	   return 0;
	 return $row["IdInventario"]; 
}
function registraInventario($tipInvent,$IdLocal,$IdPedido,$IdComprobante){

         //Control         
         $IdInventario = getIdInventario($IdLocal);

	 if($IdInventario)  return $IdInventario;

         global $UltimaInsercion;

	 $IdUsuario = CleanID(getSesionDato("IdUsuario"));
	 $Keys    = "IdUsuario,"; 
	 $Values  = "'".$IdUsuario."',"; 
	 $Keys   .= "IdLocal,"; 
	 $Values .= "'".$IdLocal."',"; 
	 $Keys   .= "Inventario,"; 
	 $Values .= "'".$tipInvent."',"; 
	 $Keys   .= "IdPedido,"; 
	 $Values .= "'".$IdPedido."',"; 
	 $Keys   .= " IdComprobante,"; 
	 $Values .= "'".$IdComprobante."',"; 
	 $Keys   .= " Estado";
	 $Values .= "'Pendiente'"; 

	 $sql = "insert into ges_inventario (".$Keys.") values (".$Values.")";
	 query($sql);

	 $IdInventario = $UltimaInsercion;
	 if( $IdInventario == 1 )
	   registraCambiosInventario($IdInventario," Inventario = 'Inicial' ");

	 return $IdInventario;
}

function getIdAjusteOperacion($OpeAjuste,$TipoMovimiento){



         $sql =
	   " select IdKardexAjusteOperacion ".
	   " from   ges_kardexajusteoperacion ".
	   " where  AjusteOperacion like '".$OpeAjuste."' ".
	   " and    TipoMovimiento = '".$TipoMovimiento."'";

	 $row = queryrow($sql);

	 if (!$row) 
	   {
	     global $UltimaInsercion;
	     $Keys    = "AjusteOperacion,";
	     $Values  = "'".$OpeAjuste."',";
	     $Keys   .= "TipoMovimiento";
	     $Values .= "'".$TipoMovimiento."'";

	     $sql     = "insert into ges_kardexajusteoperacion (".$Keys.") values (".$Values.")";
	     query($sql);

	     return $UltimaInsercion;
	   }

	 return $row["IdKardexAjusteOperacion"];	

}

function validaKardexPedidoDet($idpedidodet){

         $sql = 
	   " select EstadoDetalle ".
	   " from   ges_kardex ".
	   " where  Estado <> 0 ".
	   " and    IdPedidoDet = '".$idpedidodet."'".
	   " limit 1";

	 $row = queryrow($sql);
	 
	 if (!$row) return false;
	 return $row["EstadoDetalle"]; 
	 
}
?>
