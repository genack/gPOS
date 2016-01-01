<?php

function CrearPago($IdComprobante,$IdPagoDoc,$importe,$mora,$documento,$obs,$IdUsuario=false,
		   $estadopago=false,$importeplan=false,$fechaplan=false,$IdMoneda,
		   $Desviacion=false,$exceso){

        if (!$IdUsuario)
	  $IdUsuario = getSesionDato("IdUsuario");

	$estado        = ($estadopago)? $estadopago:'Pendiente';
	$esPlanificado = (!$estadopago)? 1:0;

	$oPago = new pago;

	$oPago->set("IdComprobanteProv", $IdComprobante, FORCE);
	$oPago->set("Descripcion",$documento,FORCE);
	$oPago->set("Observaciones", $obs, FORCE);
	$oPago->set("IdUsuario", $IdUsuario, FORCE);
	$oPago->set("IdMoneda", $IdMoneda, FORCE);

	if($estadopago){
	  $oPago->set("IdPagoProvDoc", $IdPagoDoc, FORCE);
	  $oPago->set("Importe", $importe, FORCE);
	  $oPago->set("Mora", $mora, FORCE);
	  $oPago->set("Excedente", 0, FORCE);
	  $oPago->set("Estado", $estado, FORCE);
	  $oPago->set("ValuacionMoneda", $Desviacion, FORCE);
	}else{
	  $oPago->set("Importe",$importeplan,FORCE);
	  $oPago->set("FechaPago", $fechaplan, FORCE);
	  $oPago->set("esPlanificado",$esPlanificado,FORCE);
	}

	actualizarSaldoPagoDoc($exceso,$mora,$IdPagoDoc,false);

	if ($oPago->Alta()) {
		//if(isVerbose())		
		//	echo gas("aviso", _("Nuevo cliente registrado"));
		$id = $oPago->get("IdPagoProv");

		//$oPago->Update("Codigo",$IdLocal.$id,$id);
		return $id;

	} else {
		//echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}

}


function ModificaPago($idpagoprov,$idcomprobante,$IdUsuario,$xdoc,$Eliminar=false,
		      $IdMoneda,$Opcion,$IdPagoDoc=false,$Importe=false,$Mora=false,
		      $ImportePlan=false,$FechaPagoPlan=false,$Documento=false,
		      $Obs=false,$Desviacion=false,$EstadoPago=false,$exceso){

        if (!$IdUsuario)
	  $IdUsuario = getSesionDato("IdUsuario");

	switch($xdoc){
	    case "Eliminar":

	      $oPago = new pago;
	      $oPago->set("Eliminado", $Eliminar, FORCE);
	      $oPago->set("IdUsuario", $IdUsuario, FORCE);

	      actualizarSaldoPagoDoc($exceso,$Mora,$IdPagoDoc,$Importe);
	      break;

	    case "Modificar":

	      $oPago = new pago;
	      if($Opcion == 1){
		$oPago->set("IdPagoProvDoc", $IdPagoDoc, FORCE);
		$oPago->set("Importe", $Importe, FORCE);
		$oPago->set("Mora", $Mora, FORCE);
		$oPago->set("Excedente", 0, FORCE);
		$oPago->set("IdMoneda", $IdMoneda, FORCE);
		$oPago->set("ValuacionMoneda", $Desviacion, FORCE);
		$oPago->set("Estado", $EstadoPago, FORCE);

	      }

	      if($Opcion == 2){
		$oPago->set("FechaPago", $FechaPagoPlan, FORCE);
		$oPago->set("Importe", $ImportePlan, FORCE);
		$oPago->set("IdMoneda", $IdMoneda, FORCE);
	      }
	      $oPago->set("Descripcion", $Documento, FORCE);
	      $oPago->set("Observaciones", $Obs, FORCE);
	      break;
	}
	if ($oPago->Modificar($idpagoprov)) 
	  return $idpagoprov;
	else 
	  return false;
}

function ActualizaEstadoPagoDoc($idpagodoc,$estado){
  $sql = "UPDATE ges_pagosprovdoc SET Estado='$estado'".
    "WHERE  IdPagoProvDoc = $idpagodoc";
  $res = query($sql,"Documento Modificado");

  if (!$res){
    $this->Error(__FILE__ . __LINE__, "E: no pudo actualizar campo");
    return false;	
  }		
  return true;

}

class pago extends Cursor {
    function pago() {
    	return $this;
    }

    function Alta(){
	global $UltimaInsercion;
	$data = $this->export();
	//print $data;
	$coma = false;
	$listaKeys = "";
	$listaValues = "";
				
	foreach ($data as $key=>$value){
	  if ($coma) {
	    $listaKeys .= ", ";
	    $listaValues .= ", ";
	  }
	  $listaKeys .= " $key";
	  $listaValues .= "'".$value."'";
	  $coma = true;
	}
		
	$sql = "INSERT INTO ges_pagosprov ( $listaKeys ) VALUES ( $listaValues )";
	$res = query($sql,"Alta Pago Proveedor");
		
	if ($res) {		
	  $id = $UltimaInsercion;	
	  $this->set("IdPagoProv",$id,FORCE);
	  return $id;			
	}
						
	return false;				 		
    }

    function Modificar($idpagoprov){
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

	$sql = "UPDATE ges_pagosprov SET $str WHERE IdPagoProv = '$idpagoprov'";
	$res = query($sql,"Documento Modificado");

	if (!$res){
	  $this->Error(__FILE__ . __LINE__, "E: no pudo modificar Documento");
	  return false;	
	}		
	return true;				 		
    }
    function Update($key,$value,$id) {
	        $id  = CleanID($id);
		$key = CleanText($key);
		$value = CleanInt($value);

		$sql = "UPDATE ges_pagosprovdoc SET $key='$value' ".
		       "WHERE  IdPagoProvDoc = $id";
		$res = query($sql,"Documento Modificado");

		if (!$res){
		  $this->Error(__FILE__ . __LINE__, "E: no pudo actualizar campo");
		  return false;	
		}		
		return true;				 		
	}
    function getImporteComprobante($idcbte){
      $sql = "SELECT ImportePago ".
             "FROM   ges_comprobantesprov ".
	     "WHERE  IdComprobanteProv = '$idcbte' ".
	     "AND    Eliminado = 0";
      $row = queryrow($sql);
      return $row["ImportePago"];
    }

    function getImportePendiente($idcbte){
      $sql = "SELECT ImportePendiente ".
             "FROM   ges_comprobantesprov ".
	     "WHERE  IdComprobanteProv = '$idcbte' ".
	     "AND    Eliminado = 0";;
      $row = queryrow($sql);
      return $row["ImportePendiente"];
    }

    function getImportePagada($idcbte){
      $sql = "SELECT SUM(Importe) as ImportePagada ".
             "FROM ges_pagosprov ".
	     "WHERE IdComprobanteProv = '$idcbte' ".
             "AND Estado = 'Confirmado' ".
             "AND Eliminado = 0";
      $row = queryrow($sql);
      return $row["ImportePagada"];
    }

    function getImportePlan($idcbte){
      $sql = "SELECT SUM(Importe) as ImportePlan ".
             "FROM ges_pagosprov ".
	     "WHERE IdComprobanteProv = '$idcbte' ".
             "AND Estado = 'Pendiente' ".
	     "AND Eliminado = 0";
      $row = queryrow($sql);
      return $row["ImportePlan"];
    }

    function getIdPagoDoc($idcbte){
      $sql = "SELECT IdPagoProvDoc ".
             "FROM ges_pagosprov ".
	     "WHERE IdComprobanteProv = '$idcbte' ".
	     "AND Eliminado = 0";
      $row = queryrow($sql);
      return $row["IdPagoProvDoc"];
    }


}

function PagoPeriodo($desde,$hasta,$nombre=false,$esSoloMoneda=false,
		     $filtroestado=false,$filtromodalidad=false,$esSoloLocal=false){

  $Moneda    = getSesionDato("Moneda");

  // Clean Datos 
  $desde        = CleanRealMysql($desde);
  $hasta        = CleanRealMysql($hasta);
  $nombre       = CleanRealMysql($nombre);

  // Proveedor 
  $extraNombre  = ($nombre and $nombre != '')?" AND ges_proveedores.nombreComercial LIKE '%$nombre%' ":"";

  //Estado
  $extraEstado  = ($filtroestado!='Todos')?" AND ges_pagosprovdoc.Estado='$filtroestado' ":"";
  //Fechas: Desde,Hasta 

  $extraFecha   = " AND date(ges_pagosprovdoc.FechaRegistro) >= '$desde' AND date(ges_pagosprovdoc.FechaRegistro) <= '$hasta' ";

  //Moneda value: Todos,Sol,Dolar
  $extraSol     = ($esSoloMoneda=='2')?" AND ges_pagosprovdoc.IdMoneda = 2 ":"";
  $extraDol     = ($esSoloMoneda=='1')?" AND ges_pagosprovdoc.IdMoneda = 1 ":"";
  $extraMoneda  = ($esSoloMoneda=='todoSol')? "ges_pagosprovdoc.CambioMoneda":"1";
  $Simbolo      = ($esSoloMoneda=='todoSol')? "CONCAT('".$Moneda[1]['S']."') as Simbolo,":"ges_moneda.Simbolo,";


  //Local
  $extraLocal   = ($esSoloLocal)?" AND ges_pagosprovdoc.IdLocal = '$esSoloLocal' ":"";

  //Modalidad de pago
  $extraModalidad = ($filtromodalidad!='Todos')?" AND ges_pagosprovdoc.IdModalidadPago = '$filtromodalidad' ":"";


  $sql = "SELECT
                ges_locales.NombreComercial As Local,
                if(ges_pagosprovdoc.IdOrdenCompra = 0, ' ',(select ges_ordencompra.CodOrdenCompra from ges_ordencompra where ges_ordencompra.IdOrdenCompra = ges_pagosprovdoc.IdOrdenCompra)) as Pedido,
                ges_proveedores.nombreComercial As Proveedor,
                DATE_FORMAT(ges_pagosprovdoc.FechaRegistro, '%e %b %y  %H:%i') As Registro,
                IF ( DATE_FORMAT(ges_pagosprovdoc.FechaOperacion, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_pagosprovdoc.FechaOperacion, '%e %b %y %H:%i~%Y-%m-%d %H:%i:%S') ) 
                    As Operacion,
                ges_modalidadespago.ModalidadPago,
                ges_pagosprovdoc.Estado,
                $Simbolo
                (ges_pagosprovdoc.Importe*$extraMoneda)  as Importe,
                IF(ges_pagosprovdoc.EntidadFinanciera like '', ' ', ges_pagosprovdoc.EntidadFinanciera) as EntidadFinanciera,
                IF(ges_pagosprovdoc.CodOperacion like '', ' ', ges_pagosprovdoc.CodOperacion) as CodOperacion,
                IF(ges_pagosprovdoc.CtaEmpresa like '', ' ', ges_pagosprovdoc.CtaEmpresa) as CtaEmpresa,
                IF(ges_pagosprovdoc.CtaProveedor like '', ' ', ges_pagosprovdoc.CtaProveedor) as CtaProveedor,
                IF(ges_pagosprovdoc.NumDocumento like '', ' ', ges_pagosprovdoc.NumDocumento) as NumDocumento,
                ges_usuarios.Nombre As Usuario,
                ges_pagosprovdoc.CambioMoneda,
                ges_pagosprovdoc.IdPagoProvDoc,
                ges_pagosprovdoc.IdMoneda,
                ges_pagosprovdoc.IdOrdenCompra,
                ges_pagosprovdoc.IdModalidadPago,
                ges_pagosprovdoc.IdProveedor,
                ges_pagosprovdoc.Codigo, 
                IF ( ges_pagosprovdoc.Observaciones like '', ' ',ges_pagosprovdoc.Observaciones) as Observaciones
         FROM  ges_pagosprovdoc
         LEFT JOIN ges_proveedores ON ges_pagosprovdoc.IdProveedor = ges_proveedores.IdProveedor
         INNER JOIN ges_modalidadespago ON ges_pagosprovdoc.IdModalidadPago = ges_modalidadespago.IdModalidadPago
         INNER JOIN ges_moneda     ON ges_pagosprovdoc.IdMoneda  = ges_moneda.IdMoneda
         INNER JOIN ges_locales    ON ges_pagosprovdoc.IdLocal   = ges_locales.IdLocal
         INNER JOIN ges_usuarios   ON ges_pagosprovdoc.IdUsuario = ges_usuarios.IdUsuario
                
          WHERE ges_pagosprovdoc.Eliminado = 0 "."
          $extraNombre 
          $extraFecha 
          $extraModalidad
          $extraEstado
          $extraSol 
          $extraDol 
          $extraLocal".
          "ORDER BY ges_pagosprovdoc.IdPagoProvDoc DESC";  
  $res = query($sql);
  if (!$res) return false;
  $PagoDocumento = array();
  $t = 0;
  while($row = Row($res)){
    $nombre = "Documento_" . $t++;
    $PagoDocumento[$nombre] = $row; 		
  }	
  return $PagoDocumento;
}

function DetallesPago($IdComprobanteProv){
  $sql = "SELECT IF(Descripcion LIKE '',' ',Descripcion) as Documento, ".
         "ges_pagosprov.Estado, ".
         "IF(ges_pagosprov.Estado='Pendiente',' ',(SELECT ModalidadPago FROM ges_modalidadespago ".
           "INNER JOIN ges_pagosprovdoc ON ges_pagosprovdoc.IdModalidadPago ".
           "= ges_modalidadespago.IdModalidadPago WHERE ges_pagosprovdoc.IdPagoProvDoc ".
           "= ges_pagosprov.IdPagoProvDoc )) as ModoPago, ".
         "DATE_FORMAT(ges_pagosprov.FechaRegistro, '%e %b %y  %H:%i') AS FechaRegistro, ".
         "IF(DATE_FORMAT(ges_pagosprov.FechaPago, '%e %b %Y') IS NULL,' ',DATE_FORMAT(ges_pagosprov.FechaPago, '%e %b %y~%Y-%m-%d')) AS FechaPago, ".
         "ges_moneda.IdMoneda, ".
         "ges_moneda.Simbolo, ".
         "ges_pagosprov.Importe, ".
         "Mora, ".
         "Excedente, ".
         "ges_usuarios.Nombre As Usuario, ".
         "ges_pagosprov.IdPagoProv, ".
         "IF(ges_pagosprov.Estado='Pendiente',' ',ges_pagosprov.IdPagoProvDoc) AS IdPagoProvDoc, ".
         "ges_pagosprov.IdComprobanteProv, ".
         "IF(ges_pagosprov.IdPagoProvDoc = 0,' ',(SELECT CONCAT(DATE_FORMAT(ges_pagosprovdoc.FechaOperacion, '%e %b %Y'),'~',CambioMoneda) ".
           "FROM ges_pagosprovdoc WHERE ges_pagosprovdoc.IdPagoProvDoc = ges_pagosprov.IdPagoProvDoc)) AS FechaOperacion, ".
         "ges_pagosprov.ValuacionMoneda, ".
         "IF(ges_pagosprov.Observaciones like '', ' ',ges_pagosprov.Observaciones) as Observaciones, ".
         "ges_pagosprov.EstadoCuota, ".
         "ges_pagosprov.esPlanificado ".
         "FROM ges_pagosprov ".
         "INNER JOIN ges_usuarios ON ges_pagosprov.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_moneda ON ges_pagosprov.IdMoneda = ges_moneda.IdMoneda ".
         "WHERE ges_pagosprov.Eliminado = 0 ".
         "AND ges_pagosprov.IdComprobanteProv = '$IdComprobanteProv' ".
         "ORDER BY ges_pagosprov.IdPagoProv ASC ";

  $res = query($sql);
  if (!$res) return false;
  $PagoDocumento = array();
  $t = 0;
  while($row = Row($res)){
    $nombre = "DetPago_" . $t++;
    $PagoDocumento[$nombre] = $row;

    if($row["Estado"] == 'Pendiente' || $row["esPlanificado"] == 1)
      checkEstadoCuotaPago($row);
  }	
  return $PagoDocumento;
}

function checkFechaPago($row){
 
  $aFecha     = explode("~",$row["Pago"]);
  $Hoy        = strtotime('now');
  $Fecha      = ( isset($aFecha[1]) )? strtotime($aFecha[1]):'';
  $spago      = $row["EstadoPago"];
  $pdte       = $row["ImportePendiente"];
  $xid        = $row["IdPedido"];
  $campoxdato = "";

  if( $Hoy > ($Fecha+86400) && $pdte > 0)
    $spago = "Vencida";

  if( $Hoy < $Fecha && $pdte > 0)
    $spago = ($row["TotalImporte"] == $pdte)? "Pendiente":"Empezada";
 
  if( $spago != $row["EstadoPago"]) 
    sModificarCompra($xid,"EstadoPago = '$spago'",false,false);

  return $spago;
}

function setImporteCero2Estado($row){

  $xid        = $row["IdPedido"];
  $campoxdato = "EstadoPago = 'Pagada', EstadoDocumento='Confirmado'";

  sModificarCompra($xid,$campoxdato,false,false);  
}

function checkEstadoCuotaPago($row){
  $aFecha      = explode("~",$row["FechaPago"]);
  $Hoy         = strtotime('now');
  $Fecha       = strtotime($aFecha[1]);
  $esPlan      = $row["esPlanificado"];
  $estadopago  = $row["Estado"];
  $xid         = $row["IdPagoProv"];
  $estadocuota = "";

  if($estadopago == "Pendiente" && $Hoy > ($Fecha+86400) && $esPlan == 1)
    $estadocuota = 'Vencido';

  if($estadopago == "Pendiente" && $Hoy < $Fecha && $esPlan == 1){
    $estadocuota = 'Pendiente';
  }

  if($estadocuota != "")  {
    $sql = "UPDATE ges_pagosprov ".
           "SET    EstadoCuota = '$estadocuota' ".
           "WHERE  IdPagoProv = '$xid' ";
    query($sql);
  }
}

?>