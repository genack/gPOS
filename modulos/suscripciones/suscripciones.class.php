<?php

function SuscripcionesFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdSuscripcion"];
	
	$oOrden = new ordenservicio;
		
	if ($oOrden->Load($id))
		return $oOrden;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class suscripciones extends Cursor {
  
   function suscripciones() {
     return $this;
   }
  
   function Load($id) {
     $id = CleanID($id);
     $this->setId($id);
     $this->LoadTable("ges_suscripciones", "IdSuscripcion", $id);
     return $this->getResult();
   }
   
   function Crea(){

   }

   function Alta($table,$idtable){
     global $UltimaInsercion;
     
     $data = $this->export();
     
     $coma = false;
     
     $listaKeys = "";
     $listaValues = "";
     
     foreach ($data as $key=>$value){
       if ($coma) {
	 $listaKeys .= ", ";
	 $listaValues .= ", ";
       }
       
       $listaKeys .= " " . $key;
       $listaValues .= " '".$value."'";
       $coma = true;
     }
     
     $sql = "INSERT INTO $table ( $listaKeys ) VALUES ( $listaValues )";
     $res = query($sql);
     
     if ($res) {
       $id = $UltimaInsercion;	
       $this->set($idtable,$id,FORCE);
       return $id;			
     }
						
     return false;
   }

   function Modificar($table,$idtable,$id){
     $data = $this->export();
     $coma = false;
     $str = "";
     
     foreach ($data as $key => $value) {
       if ($coma)
	 $str .= ",";
       $value = mysql_real_escape_string($value);
       $str .= " $key = '".$value."'";
       $coma = true;
     }
     
     $sql = "UPDATE $table SET $str ".
       "WHERE  $idtable = '$id'";
     
     $res = query($sql);
     
     if (!$res){
       $this->Error(__FILE__ . __LINE__, "E: no pudo modificar ");
       return false;	
     }		
     return true;				 		
   }
   
   
}

function mostrarSuscripcionLinea($IdSuscripcion){
  $sql = "SELECT * ".
         "FROM   ges_suscripcionesdet ".
         "WHERE Eliminado = 0 ".
         "AND   IdSuscripcion = '$IdSuscripcion' ".
         "ORDER BY IdSuscripcionDet ASC ";

  $res = query($sql);
  $arr = array();
  while( $row = Row($res) ){

    array_push($arr,$row['IdSuscripcionDet']."~"
	       .$row['IdSuscripcion']."~"
	       .$row['IdProducto']."~"
	       .$row['Concepto']."~"
	       .$row['Intervalo']."~"
	       .$row['UnidadIntervalo']."~"
	       .$row['Estado']."~"
	       .$row['Cantidad']."~"
	       .$row['Precio']."~"
	       .$row['Descuento']."~"	       
	       .$row['Importe']."~"
	       .$row['DiaFacturacion']."~"	       
	       .$row['AdelantoPeriodo']."~"	       
	       .$row['PlazoPago']."~"	       
	       );
  }
  return implode($arr,";"); 

}

function mostrarSuscripcionCliente($IdCliente){
  $IdLocal = getSesionDato("IdTienda");
  $sql = "SELECT IdSuscripcion,IdCliente,ges_suscripciones.IdTipoSuscripcion, ".
         "       TipoSuscripcion, ".
         "       date(FechaInicio) as FechaInicio, ".
         "       date(FechaFin) as FechaFin, ".
         "       Estado, ".
         "       Prolongacion, ".
         "       Comprobante, ".
         "       SerieComprobante, ".
         "       TipoPago, ".
         "       if( Observaciones like '', ' ', Observaciones ) as Observaciones, ".
         "       IdSubsidiario ".
         "FROM   ges_suscripciones ".
         "INNER JOIN ges_suscripciontipo ON ges_suscripciones.IdTipoSuscripcion = 
                 ges_suscripciontipo.IdTipoSuscripcion ".
         "WHERE ges_suscripciones.Eliminado = 0 ".
         "AND   IdCliente = '$IdCliente' ".
         "AND   IdLocal   = '$IdLocal' ".
         "ORDER BY IdSuscripcion DESC ";

  $res = query($sql);
  if (!$res) return false;
  $suscripciones = array();
  $t = 0;
  while($row = Row($res)){
    
    $nombre = "suscripcion_" . $t++;
    $xdetalle  =  mostrarSuscripcionLinea($row["IdSuscripcion"]);
    $row["Detalle"]  = ($xdetalle)? $xdetalle:' ';
    $row["Subsidiario"] = ($row["IdSubsidiario"] != 0)? getNombreSubsidiario($row["IdSubsidiario"]):' ';
    $suscripciones[$nombre] = $row;
    
  }

  return $suscripciones;
}

function validaSuscripcones2facturar(){
  $asuscrip  = buscarSuscripciones2facturar();
  if( $asuscrip ) crearSuscripciones2facturar($asuscrip);
}

function buscarSuscripciones2facturar(){
  $asuscrip = array();
  $sql = 
    " select IdSuscripcion,IdCliente,Comprobante,SerieComprobante,IdLocal,FechaInicio,FechaFin,TipoPago,DATE(NOW()) as FechaHoy,Prolongacion".
    " from   ges_suscripciones ".
    " where  Estado    = 'Ejecucion'".
    " and    Eliminado = '0' ";
  $res = query($sql);

  if (!$res) return false;

  while($row = Row($res)){
    $xid = $row["IdSuscripcion"] ;
    $asuscrip[ $xid ]['id']      = $xid;
    $asuscrip[ $xid ]['comprobante']      = $row["Comprobante"];
    $asuscrip[ $xid ]['local']            = $row["IdLocal"];
    $asuscrip[ $xid ]['seriecomprobante'] = $row["SerieComprobante"];
    $asuscrip[ $xid ]['cliente']          = $row["IdCliente"];
    $asuscrip[ $xid ]['inicio']           = $row["FechaInicio"];
    $asuscrip[ $xid ]['fin']              = $row["FechaFin"];
    $asuscrip[ $xid ]['hoy']              = $row["FechaHoy"];
    $asuscrip[ $xid ]['tipo']             = $row["TipoPago"];
    $asuscrip[ $xid ]['prolongacion']     = $row["Prolongacion"];
  }

  return buscarSuscripcionesDetalle2facturar($asuscrip);
}

function buscarSuscripcionesDetalle2facturar($asuscrip){

  foreach ($asuscrip as $k=>$suscrip) 
     {	
       $xid = $suscrip['id'];
       $asuscripdet = array();  
       $sql = 
	 " select IdSuscripcionDet,IdProducto,Concepto,Intervalo,".
	 "        UnidadIntervalo,Cantidad,Precio,Descuento,Importe,".
	 "        FechaFacturacion,DiaFacturacion,AdelantoPeriodo,AdelantoEstado,PlazoPago".
	 " from   ges_suscripcionesdet ".
	 " where  Estado        = 'Activo'".
	 " and    IdSuscripcion = '".$suscrip['id']."' ".
	 " and    Eliminado     = '0' ";
       $res = query($sql);

       while($row = Row($res)){

	 switch( $row["UnidadIntervalo"] ) {
	 case "Mes"   : $intervalo = 30 * $row["Intervalo"];  break;
	 case "Semana": $intervalo = 7  * $row["Intervalo"];   break;
	 case "Dia"   : $intervalo = $row["Intervalo"];   break;
	 }
	 $aproducto = getDatosProductosExtra($row["IdProducto"],'id');

	 $xiddet = $row['IdSuscripcionDet'];
	 $asuscripdet[ $xiddet ]['iddet']       = $xiddet;
	 $asuscripdet[ $xiddet ]['producto']    = $row["IdProducto"];
	 $asuscripdet[ $xiddet ]['concepto']    = $row["Concepto"];
	 $asuscripdet[ $xiddet ]['cantidad']    = $row["Cantidad"];
	 $asuscripdet[ $xiddet ]['precio']      = $row["Precio"];
	 $asuscripdet[ $xiddet ]['dcto']        = $row["Descuento"];
	 $asuscripdet[ $xiddet ]['importe']     = $row["Importe"];
	 $asuscripdet[ $xiddet ]['intervalobase']   = $row["Intervalo"];
	 $asuscripdet[ $xiddet ]['intervalo']   = $intervalo;
	 $asuscripdet[ $xiddet ]['facturacion'] = $row["FechaFacturacion"];
	 $asuscripdet[ $xiddet ]['diafacturacion'] = $row["DiaFacturacion"];
	 $asuscripdet[ $xiddet ]['adelanto'] = $row["AdelantoPeriodo"];
	 $asuscripdet[ $xiddet ]['adelantoestado'] = $row["AdelantoEstado"];
	 $asuscripdet[ $xiddet ]['plazopago']    = $row["PlazoPago"];
	 $asuscripdet[ $xiddet ]['codigobarras'] = $aproducto['CodigoBarras'];
	 $asuscripdet[ $xiddet ]['referencia']   = $aproducto['Referencia'];
	 $asuscripdet[ $xiddet ]['talla']        = $aproducto['IdTalla'];
	 $asuscripdet[ $xiddet ]['color']        = $aproducto['IdColor'];
	 //$asuscripdet[ $xiddet ]['vence']        = $aproducto['Vence'];
	 //$asuscripdet[ $xiddet ]['serie']        = $aproducto['Serie'];
	 //$asuscripdet[ $xiddet ]['lote']         = $aproducto['Lote'];

       }
       $asuscrip[ $xid ]['detalles'] = $asuscripdet;
     }
  return $asuscrip;
}

function crearSuscripciones2facturar($asuscrip){

  foreach ($asuscrip as $k=>$suscrip) 
     {	
       //echo '<br/><br/>***************************** '.$suscrip['id'].'  - '.$suscrip['comprobante'].' '.$suscrip['inicio'].' ~  '.$suscrip['fin'].'  :::  '.$suscrip['tipo'].' '.$suscrip['prolongacion'] .'********************* <br>';
       $detalleAfacturar  = array();
       $detalleporfecha   = array();
       $ndetalleAfacturar = 0;

       foreach ($suscrip['detalles'] as $kdet=>$suscripdet) 
	 {	

	   $esinicio          = ( $suscripdet['facturacion'] == '0000-00-00' );
	   $eslimitado        = ( $suscrip['prolongacion'] == 'Limitado' );
	   $esPrePago         = ( $suscrip['tipo'] == 'Prepago' );
	   $esvencido         = ( strtotime($suscrip['fin']) <= strtotime($suscrip['hoy']) );	   

	   $diafacturar       = $suscripdet['diafacturacion'];	   
	   $zintervalo        = $suscripdet['intervalo'];
	   $zintervalobase    = $suscripdet['intervalobase'];
	   $zimpintervalo     = intval($zintervalo-2);//Tolerancia 
	   $zimporte          = $suscripdet['importe'];
 	   $importediario     = $zimporte/$zintervalo;//30

	   $fechafin          = getFechafinSuscripcion($suscrip,$diafacturar,$esvencido,
						       $esPrePago,$eslimitado);
	   $fechainicio       = ( $esinicio )? $suscrip['inicio'] : $suscripdet['facturacion'];
	   $ultimafacturacion = $fechainicio;


	   //echo "<br> fecha  inicio [ ".$fechainicio." ]  - fin [ ".$fechafin." ] ";

	   $dateinicio        = new DateTime($fechainicio);
	   $datefin           = new DateTime($fechafin);

	   $diasintervalo     = intval( (  strtotime($fechafin) - strtotime($fechainicio) )/60/60/24);
	   $intervalo         = $datefin->diff($dateinicio);//diferecias
	   $intervaloDias     = ($intervalo->format("%d") > 0 )? 1:0;//dias
	   $intervaloMeses    = $intervalo->format("%m");//meses
	   $intervaloAnos     = $intervalo->format("%y")*12;//años
	   $xload             = false;

	   #Nuevas facturas
 	   $nuevasfacturas    = $intervaloMeses+$intervaloAnos+$intervaloDias;
	   $restonewfact    = $nuevasfacturas%$zintervalobase;
	   $newrestofact    = ($restonewfact>0)? 1:0;
	   $newfact2resto   = ($nuevasfacturas-$restonewfact)/$zintervalobase;
	   $nuevasfacturas  = $newfact2resto+$newrestofact;
	   
	   #fecha limite hoy
	   $nuevasfacturas = ( diff2fechas( $suscrip['hoy'],$fechainicio ) < 0 )? 0:$nuevasfacturas;
	   
	   //echo "<br> nuevas facturas [".$nuevasfacturas."]";

	   for($z=0; $z< $nuevasfacturas;$z++)
	     {

	       $xinicio  = ( $z == 0 )? $fechainicio:$xinicio; 
	       $arrxfin  = explode("-",$xinicio);
	       #echo "<br><br> xmesfin -> ".
	       $xmesfin  = ( $diafacturar > $arrxfin[2] )? $arrxfin[1] : $arrxfin[1]+$zintervalobase; //facturar
	       #echo "<br> xanofin -> ".
	       $xanofin  = ( $xmesfin < 13 )? $arrxfin[0] : $arrxfin[0]+1;    //Nuevo anio?
	       #echo "<br> xmesfin -> ".
	       $xmesfin  = ( $xmesfin < 13 )? $xmesfin : $zintervalobase;     //Nuevo anio mes?

	       $xfin     = $xanofin."-".$xmesfin."-".$diafacturar; //Nueva fecha final 
	       $xdate    = new DateTime($xfin);                    //formato
	       $xfin     = $xdate->format('Y-m-j');                //formato
	       $xcrstfin = diff2fechas( $fechafin, $xfin);         //resto core
	       $xfin     = ( $xcrstfin < 0 )? $fechafin:$xfin;     //fecha fin limitado
	       $xnrstfin = diff2fechas( $fechafin, $xfin);         //resto nuevo
	       $xperiodo = diff2fechas( $xfin, $xinicio);          //resto dias
	       $ckfin    = explode("-",$xfin);

	       //quita facturas 
	       if( $xcrstfin < 0 ) 
		 $nuevasfacturas--;

	       //add periodo 
	       if( $xnrstfin > 0 )
		 if( $xnrstfin < $zintervalo ) 
		   $nuevasfacturas++;

	       //cargar factura
	       $xload = ( !$eslimitado && $ckfin[2] == $diafacturar ); //es Ilimitado
	       $xload = ( $eslimitado )? true:$xload;                  //es limitado

  	       $importedetalle = ( $xperiodo < $zimpintervalo )? $importediario*$xperiodo : $zimporte;
	       $xfacturacion   = ( $esPrePago )? $xinicio:$xfin;
	       $xperiodofacturacion = " del ".$xinicio." al ".$xfin;

	       /**
	       echo "<br> ".$z."----------------------------------------------------------------------";
	       echo "<br> Perioso dias: ".$xperiodo;
	       echo "<br> Periodo :::::::::::::: Desde  ".$xinicio." Hasta  ".$xfin." ::::::";
	       echo "<br> Importe -> S/. ".FormatPreciosTPV($importedetalle);
	       echo "<br> Facturacion -> ".$xfacturacion;
	       echo "<br> Periodo Facturacion -> ".$xperiodofacturacion;
	       echo "<br> Cargar Factura ->[".$xload."]<br>";
	       **/

	       $xinicio = $xfin;   //Nueva fecha

	       if( $xload )
		 {
		   $suscripdet['importeAFacturar']  = FormatPreciosTPV($importedetalle);
		   $suscripdet['precio']  = FormatPreciosTPV($importedetalle/$suscripdet['cantidad']);
		   $suscripdet['FechaAFacturacion'] = $xfacturacion;
		   $suscripdet['FechaFinFactura']   = $xfin;
		   $suscripdet['conceptoperiodo']   = $xperiodofacturacion;

		   //echo "<br>".$z.'..................................................con fecha -> '.$suscripdet['FechaAFacturacion'].' Importe -> '.$suscripdet['importeAFacturar'].' '.$suscripdet['concepto'].' '.$suscripdet['conceptoperiodo'].'</br>';

		   $detalleporfecha[$xfacturacion]   = ( isset($detalleporfecha[$xfacturacion]) )? $detalleporfecha[$xfacturacion].','.$ndetalleAfacturar:$ndetalleAfacturar;
		   $detalleAfacturar[$ndetalleAfacturar] = $suscripdet;
		   
		   $ndetalleAfacturar++;
		 }
	     }
	 }

       foreach ( $detalleporfecha as $xfecha=>$xdetallefecha ) 
	 {
	   global $UltimaInsercion;

	   #Crear comprobante
	   $IdLocal          = $suscrip["local"]; //getSesionDato("IdTienda");    
	   $IdUsuario        = getSesionDato("IdUsuario");
	   $textDoc          = $suscrip["comprobante"];
	   $sreDocumento     = $suscrip["seriecomprobante"];
	   $Num              = GeneraNumDeTicket($IdLocal,"cesion");
	   $Codigo           = NroComprobanteVentaMax($IdLocal,$textDoc,$sreDocumento);
	   $aCodigo          = explode("-", $Codigo);
	   $Serie            = $aCodigo[0];
	   $Nro              = $aCodigo[1];

	   $SerieComprobante = "'CS$IdLocal'";
	   $NComprobante     = $Num;
	   $IdCliente        = $suscrip["cliente"];
	   $IdSuscripcion    = $suscrip["id"]; 
	   $keys    = "IdLocal,";
	   $values  = "$IdLocal,";
	   $keys   .= "IdUsuario,";
	   $values .= "$IdUsuario,";
	   $keys   .= "SerieComprobante,";
	   $values .= "$SerieComprobante,";
	   $keys   .= "NComprobante,";
	   $values .= "$NComprobante,";
	   $keys   .= "TipoVentaOperacion,";
	   $values .= "'VD',";
	   $keys   .= "FechaComprobante,";
	   $values .= "'$xfecha',";
	   $keys   .= " Status,";
	   $values .= " 1,";
	   $keys   .= "IdCliente,";
	   $values .= "$IdCliente,";
	   $keys   .= "Cobranza,";
	   $values .= "'Pendiente',";
	   $keys   .= "IdSuscripcion";
	   $values .= "$IdSuscripcion";
	   $sql     = "insert into ges_comprobantes (".$keys.") values (".$values.")";
	   query($sql);

	   $IdComprobante = $UltimaInsercion;

	   #Registra Numero Comprobante
	   if( $IdComprobante == 0 ) continue;
	   if( RegistrarNumeroComprobante($Nro,$IdComprobante,$textDoc,$Serie,false,$IdLocal,$IdUsuario) ) continue;

	   #Registrar detalle
	   $detalleporfactura = explode(',',$xdetallefecha);
	   $TotalImporte      = 0;
	   $Impuesto          = getSesionDato("IGV");    

	   //echo "<br/><br/> ########## ".$suscrip['comprobante']."  ".$Serie." - ".$Nro."  ".$xfecha."<br/> ";

	   foreach ( $detalleporfactura as $xitem ) 
	     {
	       //echo "<br/> DETALLE  - ".$xitem." - ID ".$detalleAfacturar[$xitem]['producto']."   ".$detalleAfacturar[$xitem]['concepto']." - ".$detalleAfacturar[$xitem]['conceptoperiodo']."  cant ".$detalleAfacturar[$xitem]['cantidad']."  S/.".$detalleAfacturar[$xitem]['importeAFacturar']." CB ".$detalleAfacturar[$xitem]['codigobarras'] ;
	      $TotalImporte += $detalleAfacturar[$xitem]['importeAFacturar'];
                 $xconcepto = str_replace($detalleAfacturar[$xitem]['codigobarras'],'',$detalleAfacturar[$xitem]['concepto']);
	      //ComprobanteDet...
	      $Keys    = "IdComprobante,";
	      $Values  = "'".$IdComprobante."',";	
	      $Keys   .= "IdProducto,";
	      $Values .= "'".$detalleAfacturar[$xitem]['producto']."',";
	      $Keys   .= "IdPedidoDet,";
	      $Values .= "'0',";
	      $Keys   .= "Cantidad,";
	      $Values .= "'".$detalleAfacturar[$xitem]['cantidad']."',";
	      $Keys   .= "Precio,";
	      $Values .= "'".$detalleAfacturar[$xitem]['precio']."',";
	      $Keys   .= "CostoUnitario,";
	      $Values .= "'0',";
	      $Keys   .= "Descuento,";
	      $Values .= "'0',";
	      $Keys   .= "Importe,";
	      $Values .= "'".$detalleAfacturar[$xitem]['importeAFacturar']."',";
	      $Keys   .= "Impuesto,";
	      $Values .= "'".$Impuesto."',";
	      $Keys   .= "Concepto,";
	         //$Values .= "'".$detalleAfacturar[$xitem]['concepto']." -".$detalleAfacturar[$xitem]['conceptoperiodo']."',";
                 $Values.= "'".$xconcepto." -".$detalleAfacturar[$xitem]['conceptoperiodo']."',";
	      $Keys   .= "Talla,";
	      $Values .= "'".$detalleAfacturar[$xitem]['talla']."',";
	      $Keys   .= "Color,";
	      $Values .= "'".$detalleAfacturar[$xitem]['color']."',";
	      $Keys   .= "Referencia,";
	      $Values .= "'".$detalleAfacturar[$xitem]['referencia']."',";
	      $Keys   .= "CodigoBarras,";
	      $Values .= "'".$detalleAfacturar[$xitem]['codigobarras']."',";
	      $Keys   .= "Oferta,";
	      $Values .= "0,";
	      $Keys   .= "Serie,";
	      $Values .= "0,";
	      $Keys   .= "Lote,";
	      $Values .= "0,";
	      $Keys   .= "Vencimiento";
	      $Values .= "0";
	      $sql     = "insert into ges_comprobantesdet (".$Keys.") values (".$Values.")";
	      query($sql);
	      #Actualiza fecha facturacion Detalle
	      $sql     = "update ges_suscripcionesdet set FechaFacturacion = '".$detalleAfacturar[$xitem]['FechaFinFactura']."' where IdSuscripcionDet = ".$detalleAfacturar[$xitem]['iddet'];	      
	      query($sql);
	      $plazopago = $detalleAfacturar[$xitem]['plazopago'];

	     }

	   #Actualiza Importe
	   $fechapago        = date('Y-m-d',strtotime('+'.$plazopago.' days', strtotime($xfecha)));
	   $ImporteImpuesto  = ($TotalImporte*100/100.0) - round( $TotalImporte*100/($Impuesto+100), 2);
	   $ImporteNeto      = $TotalImporte - $ImporteImpuesto;
       	   $keysvalues       = "ImporteNeto      = $ImporteNeto, ";
	   $keysvalues      .= "ImporteImpuesto  = $ImporteImpuesto, ";
	   $keysvalues      .= "Impuesto         = $Impuesto, ";
	   $keysvalues      .= "TotalImporte     = $TotalImporte, ";
	   $keysvalues      .= "PlazoPago        = '$fechapago', ";
	   $keysvalues      .= "ImportePendiente = $TotalImporte ";
	   $sql              = "update ges_comprobantes set ".$keysvalues." where IdComprobante = $IdComprobante";
	   query($sql);
	   actualizarImportePendienteCliente($IdCliente);
	 }

     } 
}

function ModificaSuscripcion($xid,$campoxdato){
        $Tb         = 'ges_suscripciones';
	$IdKey      = 'IdSuscripcion';
	$Id         = CleanID($xid);
	$KeysValue  = $campoxdato;
	$sql   =
	  " update ".$Tb.
	  " set    ".$KeysValue." ".
	  " where  ".$IdKey." = ".$Id;
	return query($sql); 
}

function ModificaSuscripcionDet($xid,$campoxdato){
        $Tb         = 'ges_suscripcionesdet';
	$IdKey      = 'IdSuscripcion';
	$Id         = CleanID($xid);
	$KeysValue  = $campoxdato;
	$sql   =
	  " update ".$Tb.
	  " set    ".$KeysValue." ".
	  " where  ".$IdKey." = ".$Id;
	return query($sql); 
}

function RegistrarIncidenciasSuscripcion($IdSuscripcion,$xdato){
  $IdLocal   = getSesionDato("IdTienda");    
  $IdUsuario = getSesionDato("IdUsuario");

  $oSuscripcion = new suscripciones;
  $oSuscripcion->Load($IdSuscripcion);

  $sql = "INSERT INTO ges_suscripcionincidentes ".
         "(IdSuscripcion, IdTipoSuscripcion, IdUsuario, IdLocal, ".
         "Estado, Prolongacion, Comprobante, SerieComprobante, TipoPago, Observaciones) ".
         "values ( ".
         "'".$IdSuscripcion."', ".
         "'".$oSuscripcion->get("IdTipoSuscripcion")."', ".
         "'".$IdUsuario."', ".
         "'".$IdLocal."', ".
         "'".$xdato."', ".
         "'".$oSuscripcion->get("Prolongacion")."', ".
         "'".$oSuscripcion->get("Comprobante")."', ".
         "'".$oSuscripcion->get("SerieComprobante")."', ".
         "'".$oSuscripcion->get("TipoPago")."', ".
         "'".$oSuscripcion->get("Observaciones")."') ";

  query($sql); 
  
}

function checkSuscripciones(){

  $fechaactual = Date("Y-m-d");

  if( getParametro("Suscripcion") == $fechaactual ) return;

  #Verifica
  validaSuscripcones2facturar();
  setParametroSuscripcion();

}
 
function diff2fechas($xfin,$xinicio){     return intval( (  strtotime($xfin) - strtotime($xinicio) )/60/60/24);}
function addmonth2fecha($xmonth,$xfecha){  return date ( 'Y-m-j' , strtotime ( $xmonth , strtotime ( $xfecha ) ) );}

function getFechafinSuscripcion($xsuscrip,$xdiafacturar,$xesvencido,
				$xesPrePago,$xeslimitado){

  $zfechafin     = ( $xesvencido && $xeslimitado )? $xsuscrip['fin'] : $xsuscrip['hoy'];

  //Pospago
  if( !$xesPrePago && $xeslimitado ){
    //no vencido
    if( !$xesvencido ){
      $arfechafin  = explode('-',$zfechafin); 
      $xmesfin     = ( $xdiafacturar > $arfechafin[2] )? $arfechafin[1]-1 : $arfechafin[1]; //facturar
      $xanofin     = ( $xmesfin == 0 )? $arfechafin[0]-1:$arfechafin[0];
      $xmesfin     = ( $xmesfin == 0 )? 12 : $xmesfin;
      $zfechafin  = $xanofin."-".$xmesfin."-".$xdiafacturar;         //Nueva fecha final 
    }
  }

  //Prepago
  if( $xesPrePago ){

    $zfechafincore = ( $xeslimitado )? $xsuscrip['fin']:$zfechafin;
    $arfechafin    = explode('-',$zfechafin); 
    $xmesfin       = ( $xdiafacturar > $arfechafin[2] )? $arfechafin[1] : $arfechafin[1]+1; //facturar
    $xanofin       = ( $xmesfin < 13 )? $arfechafin[0] : $arfechafin[0]+1;      //Nuevo anio?
    $xmesfin       = ( $xmesfin < 13 )? $xmesfin : 1;                           //Nuevo anio mes?

    $xfechafin = $xanofin."-".$xmesfin."-".$xdiafacturar;                       //Nueva fecha final 
    $xcrstfin  = diff2fechas( $xfechafin,$zfechafincore);                       //resto core
    $zfechafin = ( $xcrstfin > 0 && $xeslimitado )? $zfechafincore:$xfechafin;  //fecha fin limitado
    
  }
  return $zfechafin;
}

function verficarEstadoSuscripcionCliente($id){
    $sql = "SELECT Estado ".
           "FROM ges_suscripciones ".
           "WHERE IdCliente = '$id' ";
    $res = query($sql);

    if(!$res) return false;

    while($row = Row($res)){
        if($row["Estado"] == 'Finalizado' || $row["Estado"] == 'Cancelado')
            return false;
        else
            return true;
    }
}

?>
