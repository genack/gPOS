<?php

function CrearPagoDocumento($provhab,$ordencompra,$modalidadpago,$fechaoperacion,
			    $codigooperacion,$nrodocumento,$cuentaproveedor,$cuentaempresa,
			    $tipomoneda,$cambiomoneda,$importe,$obs,
			    $idlocal=false,$IdUsuario=false,$estado,$IdArqueo,
                            $tipoprov=false,$cambiodivisa){


        $IdLocal   = (!$idlocal)?   getSesionDato("IdTienda"):$idlocal;
        $IdUsuario = (!$IdUsuario)? getSesionDato("IdUsuario"):$IdUsuario;
	$modpago   = ($modalidadpago == 1 || $modalidadpago == 2 || $modalidadpago == 7)? 1:0;
	$oPagoDoc  = new pagodoc;
	$idreg     = "";

	$oPagoDoc->set("IdProveedor", $provhab, FORCE);
	$oPagoDoc->set("IdOrdenCompra", $ordencompra, FORCE);
	$oPagoDoc->set("IdModalidadPago", $modalidadpago, FORCE);
	$oPagoDoc->set("FechaOperacion", $fechaoperacion, FORCE);
	$oPagoDoc->set("CodOperacion", $codigooperacion, FORCE);
	$oPagoDoc->set("NumDocumento", $nrodocumento, FORCE);
	$oPagoDoc->set("IdCuentaProveedor", $cuentaproveedor, FORCE);
	$oPagoDoc->set("IdCuentaEmpresa", $cuentaempresa, FORCE);
	$oPagoDoc->set("IdMoneda", $tipomoneda, FORCE);
	$oPagoDoc->set("CambioMoneda", $cambiomoneda, FORCE);
	$oPagoDoc->set("Importe", $importe, FORCE);
	$oPagoDoc->set("Saldo", $importe, FORCE);
	$oPagoDoc->set("Observaciones", $obs, FORCE);
	$oPagoDoc->set("IdLocal", $IdLocal, FORCE);
	$oPagoDoc->set("IdUsuario", $IdUsuario, FORCE);
	$oPagoDoc->set("Estado", $estado, FORCE);
	$oPagoDoc->set("TipoProveedor", $tipoprov, FORCE);

	if($IdArqueo != 0){
	  if($estado == 'Pendiente' && $modpago == 1){
	    $idreg =  registrarLibroDiarioCajaGral($IdLocal,$IdUsuario,$tipomoneda,
                                               $cambiomoneda,$importe,$IdArqueo,
                                               $cambiodivisa);
	    if(!$idreg){
	      $estado = "Borrador";
	      $oPagoDoc->set("Estado", $estado, FORCE);
	    }
	  }
	}

	if ($oPagoDoc->Alta()) {
		//if(isVerbose())		
		//	echo gas("aviso", _("Nuevo cliente registrado"));
		$id = $oPagoDoc->get("IdPagoProvDoc");
		
		$oPagoDoc->Update("Codigo",$IdLocal.$id,$id);
		if($idreg)
		  updateLibrodiarioCajaGral($idreg,$id);
		return $id;

	} else {
		//echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}
	
}


function ModificaPagoDocumento($provhab,$modalidadpago,$fechaoperacion,$codigooperacion,
			       $nrodocumento,$cuentaproveedor,$cuentaempresa,
			       $tipomoneda,$cambiomoneda,$importe,
                               $obs,$idlocal,$IdUsuario,$estado,$idoc,$IdArqueo,$cEstado,
                               $cambiodivisa){

        $IdLocal   = (!$idlocal)? getSesionDato("IdTienda"):$idlocal;
	$IdUsuario = (!$IdUsuario)? getSesionDato("IdUsuario"):$IdUsuario;
	$entrar    = ($estado == 'Pendiente' && $cEstado == 'Borrador')? 1 : 0;
	$modpago   = ($modalidadpago == 1 || $modalidadpago == 2 || $modalidadpago == 7)? 1:0;
	$idreg     = "";

	switch($estado){
	    case "Cancelado":
	      $oPagoDoc = new pagodoc;
	      $oPagoDoc->set("Estado", $estado, FORCE);
	      if ($oPagoDoc->Modificar($idoc)) 
		return $idoc;
	      else
		return false;
	      break;

	    case "Borrador":
	      $oPagoDoc = new pagodoc;
	      $oPagoDoc->set("IdProveedor", $provhab, FORCE);
	      $oPagoDoc->set("IdModalidadPago", $modalidadpago, FORCE);
	      $oPagoDoc->set("FechaOperacion", $fechaoperacion, FORCE);
	      $oPagoDoc->set("CodOperacion", $codigooperacion, FORCE);
	      $oPagoDoc->set("NumDocumento", $nrodocumento, FORCE);
	      $oPagoDoc->set("IdCuentaProveedor", $cuentaproveedor, FORCE);
	      $oPagoDoc->set("IdCuentaEmpresa", $cuentaempresa, FORCE);
	      $oPagoDoc->set("IdMoneda", $tipomoneda, FORCE);
	      $oPagoDoc->set("CambioMoneda", $cambiomoneda, FORCE);
	      $oPagoDoc->set("Importe", $importe, FORCE);
	      $oPagoDoc->set("Saldo", $importe, FORCE);
	      $oPagoDoc->set("Observaciones", $obs, FORCE);
	      $oPagoDoc->set("IdLocal", $IdLocal, FORCE);
	      $oPagoDoc->set("IdUsuario", $IdUsuario, FORCE);
	      $oPagoDoc->set("Estado", $estado, FORCE);

	      if ($oPagoDoc->Modificar($idoc)) 
		return $idoc;
	      else
		return false;
	      break;

	    case "Pendiente":

	      $oPagoDoc = new pagodoc;
	      
	      $oPagoDoc->set("IdProveedor", $provhab, FORCE);
	      $oPagoDoc->set("IdModalidadPago", $modalidadpago, FORCE);
	      $oPagoDoc->set("FechaOperacion", $fechaoperacion, FORCE);
	      $oPagoDoc->set("CodOperacion", $codigooperacion, FORCE);
	      $oPagoDoc->set("NumDocumento", $nrodocumento, FORCE);
	      $oPagoDoc->set("IdCuentaProveedor", $cuentaproveedor, FORCE);
	      $oPagoDoc->set("IdCuentaEmpresa", $cuentaempresa, FORCE);
	      $oPagoDoc->set("IdMoneda", $tipomoneda, FORCE);
	      $oPagoDoc->set("CambioMoneda", $cambiomoneda, FORCE);
	      $oPagoDoc->set("Importe", $importe, FORCE);
	      $oPagoDoc->set("Observaciones", $obs, FORCE);
	      $oPagoDoc->set("IdLocal", $IdLocal, FORCE);
	      $oPagoDoc->set("IdUsuario", $IdUsuario, FORCE);
	      $oPagoDoc->set("Estado", $estado, FORCE);
	      if($entrar) $oPagoDoc->set("Saldo", $importe, FORCE);

	      if($IdArqueo != 0 && $entrar == 1){
		if($modpago == 1){
		  $idreg =  registrarLibroDiarioCajaGral($IdLocal,$IdUsuario,$tipomoneda,
                                                 $cambiomoneda,$importe,$IdArqueo,
                                                 $cambiodivisa);
		  if(!$idreg){
		    $estado = "Borrador";
		    $oPagoDoc->set("Estado", $estado, FORCE);
		  }
		}
	      }

	      if ($oPagoDoc->Modificar($idoc)){
		if($idreg)
		  updateLibrodiarioCajaGral($idreg,$idoc);
		return $idoc;
	      }
	      else 
		return false;

	      break;
	}
	
}

function EliminarPagoDocumento($IdLocal,$IdUsuario,$idoc,$Estado){
    $eliminado = 1;
    $oPagoDoc = new pagodoc;
	      
    $oPagoDoc->set("IdUsuario", $IdUsuario, FORCE);
    $oPagoDoc->set("Eliminado", $eliminado, FORCE);
    if ($oPagoDoc->Modificar($idoc)){
      if($Estado == 'Pendiente'){
	actualizarMovimientoCjaGral($idoc);

	$oPagoDoc->Load($idoc);
	$Importe     = $oPagoDoc->get("Importe");
	$IdCuenta    = $oPagoDoc->get("IdCuentaEmpresa");
	$IdModalidad = $oPagoDoc->get("IdModalidadPago");

	$concepto = "Cancelación de pago a proveedor ";
	
	if($IdCuenta > 0)
	  if($IdModalidad == 3 || $IdModalidad == 4 || $IdModalidad == 5 || $IdModalidad == 6)
	    RegistrarMovimientoBancario($IdLocal,0,0,$IdUsuario,$IdCuenta,'Ingreso',$concepto,
					$Importe);
      }
      return $idoc;
    }
    else
      return false;
}

class pagodoc extends Cursor {
    function pagodoc() {
    	return $this;
    }

    function Load($id) {
      $id = CleanID($id);
      $this->setId($id);
      $this->LoadTable("ges_pagosprovdoc", "IdPagoProvDoc ", $id);
      return $this->getResult();
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
		
	$sql = "INSERT INTO ges_pagosprovdoc ( $listaKeys ) VALUES ( $listaValues )";
	$res = query($sql,"Alta Pago Documento Proveedor");
		
	if ($res) {		
	  $id = $UltimaInsercion;	
	  $this->set("IdPagoProvDoc",$id,FORCE);
	  return $id;			
	}
						
	return false;				 		
    }

    function Modificar($idoc){
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

	$sql = "UPDATE ges_pagosprovdoc SET $str WHERE IdPAgoProvDoc = '$idoc'";
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

    function getImportePagoDoc($idpagodoc){
      $sql = "SELECT Importe ".
             "FROM   ges_pagosprovdoc ".
	     "WHERE  IdPagoProvDoc = '$idpagodoc'".
	     "AND    Eliminado = 0";
      $row = queryrow($sql);
      return $row["Importe"];
    }
}

function PagoDocumentoPeriodo($desde,$hasta,$nombre=false,$esSoloMoneda=false,
			      $filtroestado=false,$filtromodalidad=false,$esSoloLocal=false,
			      $iddoc=false){

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

  $extraTodo = (!$iddoc)? $extraNombre.$extraFecha.$extraModalidad.$extraEstado.$extraSol.$extraDol.$extraLocal : " AND ges_pagosprovdoc.IdPagoProvDoc = '$iddoc' ";

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
                IF(ges_pagosprovdoc.CodOperacion like '', ' ', ges_pagosprovdoc.CodOperacion) as CodOperacion,
                ges_pagosprovdoc.IdCuentaEmpresa as CtaEmpresa,
                ges_pagosprovdoc.IdCuentaProveedor as CtaProveedor,
                IF(ges_pagosprovdoc.NumDocumento like '', ' ', ges_pagosprovdoc.NumDocumento) as NumDocumento,
                ges_usuarios.Nombre As Usuario,
                ges_pagosprovdoc.CambioMoneda,
                ges_pagosprovdoc.IdPagoProvDoc,
                ges_pagosprovdoc.IdMoneda,
                ges_pagosprovdoc.IdOrdenCompra,
                ges_pagosprovdoc.IdModalidadPago,
                ges_pagosprovdoc.IdProveedor,
                ges_pagosprovdoc.Codigo,
                ges_pagosprovdoc.IdLocal,
                ges_pagosprovdoc.TipoProveedor,
                IF ( ges_pagosprovdoc.Observaciones like '', ' ',ges_pagosprovdoc.Observaciones) as Observaciones,
                ges_pagosprovdoc.Saldo
         FROM  ges_pagosprovdoc
         LEFT JOIN ges_proveedores ON ges_pagosprovdoc.IdProveedor = ges_proveedores.IdProveedor
         INNER JOIN ges_modalidadespago ON ges_pagosprovdoc.IdModalidadPago = ges_modalidadespago.IdModalidadPago
         INNER JOIN ges_moneda     ON ges_pagosprovdoc.IdMoneda  = ges_moneda.IdMoneda
         INNER JOIN ges_locales    ON ges_pagosprovdoc.IdLocal   = ges_locales.IdLocal
         INNER JOIN ges_usuarios   ON ges_pagosprovdoc.IdUsuario = ges_usuarios.IdUsuario
                
          WHERE ges_pagosprovdoc.Eliminado = 0 "."
          $extraTodo".
          "ORDER BY ges_pagosprovdoc.IdPagoProvDoc DESC";  
  $res = query($sql);
  if (!$res) return false;
  $PagoDocumento = array();
  $t = 0;
  while($row = Row($res)){
    $nombre   = "Documento_" . $t++;
    $ctaemp   = $row["CtaEmpresa"];
    $ctaprov  = $row["CtaProveedor"];

    if($row["TipoProveedor"] == 'Interno')
      $row["Proveedor"] = getNombreComercialLocal($row["IdProveedor"]);

    $row["CtaEmpresa"] = ($ctaemp != 0)? $ctaemp."~".obtenerCuentaBancaria($ctaemp):" ";
    $row["CtaProveedor"] = ($ctaprov != 0)? $ctaprov."~".obtenerCuentaBancaria($ctaprov):" ";

    $PagoDocumento[$nombre] = $row; 		
  }	
  return $PagoDocumento;
}

function registrarLibroDiarioCajaGral($IdLocal,$IdUsuario,$IdMoneda,$cambiomoneda,$importe,
                                      $IdArqueo,$cambiodivisa){

     $mov          = new movimientogral;
     $FechaApertura= $mov->getAperturaCajaGral($IdMoneda,$IdLocal);
     $IdLocal      = CleanID($IdLocal);
     $IdUsuario    = CleanID($IdUsuario);
     $cantidad     = CleanFloat($importe);
     $fechacaja    = CleanCadena($FechaApertura);
     $documento    = 'Ticket';
     $codigodoc    = 0;
     $proveedor    = "";
     $oIdMoneda    = $IdMoneda;
     $ocambiomoneda= $cambiomoneda;
     $CodPartida   = 'S125';
     $IdPartida    = obtenerIdPartidaCaja($CodPartida);
     $Moneda       = getSesionDato("Moneda");

     if($oIdMoneda != 1 && $cambiodivisa == '1'){
         //Registrar operacion sustraccion cambio moneda
         $IdMoneda  = 1;//$IdMonedaCambio;
         $operacion = 'Sustraccion';
         $concepto  = 'Cambio moneda a '.$Moneda[$oIdMoneda]['T'];
         $xIdArqueo  = $mov->getIdArqueoEsCerrado($IdMoneda,$IdLocal);//$IdArqueoM;
         $xcambiomoneda = 1;//($IdMoneda == 1)? 1:$xcambiomoneda;
         $cantidad =  round(($cantidad*$cambiomoneda),2);

         EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
                               $xcambiomoneda,$operacion,$fechacaja,$IdUsuario,
                               $xIdArqueo,$documento,$codigodoc,$proveedor);  

         // Registrar operacion ingreso cambio moneda
         $IdMoneda  = $oIdMoneda;
         $operacion = 'Ingreso';
         $concepto  = 'Cambio moneda desde '.$Moneda[1]['T'];
         $IdArqueoM = $mov->getIdArqueoEsCerrado($IdMoneda,$IdLocal);
         $xIdArqueo = $IdArqueoM;
         $xcambiomoneda = ($IdMoneda == 1)? 1:$cambiomoneda;
         $cantidad = $importe;

         EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
                               $xcambiomoneda,$operacion,$fechacaja,$IdUsuario,
                               $xIdArqueo,$documento,$codigodoc,$proveedor);       

     }
     
     // Registra operacion egreso por pago factura
     $concepto     = "Compra";
     $operacion    = "Egreso";
     $IdPartida    = obtenerPartidaCajaGral('Compras','CG',$operacion);
     $IdMoneda     = CleanID($IdMoneda);
     $cambiomoneda = CleanFloat($cambiomoneda);
     $cantidad     = $importe;

     $id =  EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
				  $cambiomoneda,$operacion,$fechacaja,$IdUsuario,
				  $IdArqueo,$documento,$codigodoc,$proveedor);
     return $id;
}

function obtenerCuentaBancaria($idcta){
  $sql = "SELECT NumeroCuenta, EntidadFinanciera, Simbolo ".
         "FROM ges_cuentasbancarias ".
         "INNER JOIN ges_moneda ON ges_cuentasbancarias.IdMoneda = ges_moneda.IdMoneda ".
         "WHERE IdCuentaBancaria = '$idcta' ".
         "AND ges_cuentasbancarias.Eliminado = 0 ";
  
  $row = queryrow($sql);
  return $row["Simbolo"]."~".$row["NumeroCuenta"]."~".$row["EntidadFinanciera"];
}

function actualizarSaldoPagoDoc($exceso,$mora,$IdPagoDoc,$Importe){
  $exceso = ($mora > 0)? 0:$exceso;
  $dato   = ($Importe)? "Saldo+$Importe":$exceso;
  $sql = "UPDATE ges_pagosprovdoc SET Saldo = $dato ".
         "WHERE IdPagoProvDoc = $IdPagoDoc";
  query($sql);
}

function ModificarSaldoPago($IdPagoDoc,$Saldo){
  $sql = "UPDATE ges_pagosprovdoc SET Saldo = '$Saldo' ".
         "WHERE IdPagoProvDoc = '$IdPagoDoc'";
  return query($sql);
}
?>