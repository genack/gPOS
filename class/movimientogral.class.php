<?php

function EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida=false,$IdMoneda,
			       $cambiomoneda=false,$operacion=false,$fechacaja,$IdUsuario,
			       $IdArqueo,$documento=false,$codigodoc=false,
			       $proveedor=false,$IdComprobante=false){
  
  $IdUsuario = ($IdUsuario)? $IdUsuario:CleanID(getSesionDato("IdUsuario"));
  $IdLical   = ($IdLocal)? $IdLocal : CleanID(getSesionDato("IdTienda"));

  $mov = new movimientogral;
  $mov->set("IdLocal", $IdLocal, FORCE);

  $mov->set("IdArqueoCajaGral",$IdArqueo,FORCE);
  $mov->set("IdUsuario",$IdUsuario,FORCE);
  $mov->set("IdPartidaCaja",$IdPartida,FORCE);
  $mov->set("IdMoneda",$IdMoneda,FORCE);
  $mov->set("CambioMoneda",$cambiomoneda,FORCE);
  $mov->set("FechaCaja",$fechacaja,FORCE);
  $mov->set("TipoOperacion",$operacion,FORCE);
  $mov->set("Concepto",$concepto,FORCE);
  $mov->set("Importe",$cantidad,FORCE);
  $mov->set("Documento",$documento,FORCE);
  $mov->set("CodigoDocumento",$codigodoc,FORCE);
  $mov->set("IdSubsidiario",$proveedor,FORCE);
  $mov->set("IdComprobante",$IdComprobante,FORCE);

  if ($mov->Alta()) { 
    $id = $mov->get("IdOperacionCaja");			
    return $id;
  }
  else
    return false;
}


class movimientogral extends Cursor {
  var $ingresos;
  var $gastos;
  var $localOperacion;
  var $IdComprobante;
  var $totalmovimiento;
  var $TipoOperacion;
  var $Concepto;
  var $Modalidad;

  function movimientogral() {
    return $this;
  }	
  
  
  function GetArqueoActivo($IdLocal){
    $sql = 
      "SELECT IdArqueo ".
      "FROM   ges_arqueo_caja ".
      "WHERE  IdLocal   = '$IdLocal' ".
      "AND    Eliminado = 0 ".
      "AND    esCerrada = 0 ".
      "AND    TipoVentaOperacion = '$TipoVenta' ".
      "ORDER BY FechaCierre DESC";
    $row = queryrow($sql,'Buscando arqueo abierto');
    $IdArqueo = $row["IdArqueo"];
    return intval($IdArqueo);		
  }
  
  
  function SetTipoOperacion($Tipo){
    $this->TipoOperacion = $Tipo;
  }
  
  function GetTipoOperacion(){
    //TipoOperacion  	enum('Ingreso', 'Gasto', 'Aportacion', 'Sustraccion')
    return $this->TipoOperacion;		 	
  }
  
  function GetImporteOperacion(){
    return $this->totalmovimiento;	
  }
  
  function movimiento() {
    $this->localOperacion = 0;//no local
    $this->ingresos = array();
    $this->gastos = array();
    $this->TipoOperacion = "Ingreso";
    return $this;
  }
  
  function Load($id) {
    $id = CleanID($id);
    $this->setId($id);
    $this->LoadTable("ges_librodiario_cajagral", "IdOperacionCaja ", $id);
    return $this->getResult();
  }
  
  function Crea(){
    //$this->setNombre(_("Nuevo movimiento"));
  }
  
  function Alta(){
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
      $listaKeys .= " $key";
      $listaValues .= "'".$value."'";
      $coma = true;
    }

    
    $sql = "INSERT INTO ges_librodiario_cajagral ( $listaKeys ) VALUES ( $listaValues )";
    
    $res = query($sql,"Alta movimiento");
    
    if ($res) {		
      $id = $UltimaInsercion;	
      $this->set("IdOperacionCaja",$id,FORCE);
      return $id;			
    }
    else
      return false;
  }

    function Update($key,$value,$id) {
	        $id  = CleanID($id);
		$key = CleanText($key);
		$value = CleanInt($value);

		$sql = "UPDATE ges_librodiario_cajagral SET $key='$value' ".
		       "WHERE  IdOperacionCaja = $id";
		$res = query($sql,"Documento Modificado");

		if (!$res){
		  $this->Error(__FILE__ . __LINE__, "E: no pudo actualizar campo");
		  return false;	
		}		
		return true;				 		
    }

  function getIdArqueoEsCerrado($IdMoneda,$IdLocal){
      $sql = 
      "select IdArqueoCajaGral ".
      "from   ges_arqueo_cajagral ".
      "where  IdLocal = '$IdLocal' ".
      "and    IdMoneda = '$IdMoneda' ".
      "and    esCerrada = 0 ";

    $row = queryrow($sql);
    
    return $row["IdArqueoCajaGral"];

  }

  function getAperturaCajaGral($IdMoneda,$IdLocal){
    $sql = 
      "select FechaApertura ".
      "from   ges_arqueo_cajagral ".
      "where  IdLocal = '$IdLocal' ".
      "and    IdMoneda = '$IdMoneda' ".
      "and    esCerrada = 0 ";

    $row = queryrow($sql);
    if (!$row)
      return 0;
    
    return $row["FechaApertura"];

  }
  
  
  function SiguienteMovimiento() {
    $res = $this->LoadNext();
    if (!$res) {
      return false;
    }
    $this->setId($this->get("IdOperacionCaja "));		
    return true;			
  }	
  
  function getIdArqueMovimientoGral($Id){
    $this->Load($Id);
    return $this->get("IdArqueoCajaGral");
  }
}

function obtenerPartidaCajaGral($Partida,$TipoCaja,$TipoOperacion){
    $Partida = CleanText($Partida);
    $sql = "SELECT IdPartidaCaja FROM ges_partidascaja ".
           "WHERE  PartidaCaja LIKE '$Partida' ".
           "AND    TipoCaja = '$TipoCaja' ".
           "AND    TipoOperacion = '$TipoOperacion'";
    $row = queryrow($sql);
    return $row["IdPartidaCaja"];
}

function esCerradaCajaGral($IdArqueo){
  $sql = "SELECT  esCerrada   
          FROM ges_arqueo_cajagral 
          WHERE IdArqueoCajaGral = $IdArqueo ";
  
  $row = queryrow($sql);  

  return $row["esCerrada"];
}

function updateLibrodiarioCajaGral($idmov,$iddoc){
  $mov = new movimientogral;
  $mov->Update("IdPagoProvDoc",$iddoc,$idmov);
}

function obtenerMovimientoGralProv($IdPagoProvDoc){
  $sql = "SELECT IF(ges_pagosprovdoc.TipoProveedor = 'Externo', (ges_proveedores.NombreComercial),(SELECT ges_locales.NombreComercial FROM ges_locales WHERE ges_locales.IdLocal = ges_pagosprovdoc.IdProveedor)) as Proveedor, ges_pagosprovdoc.TipoProveedor ".
         "FROM ges_pagosprovdoc ".
         "INNER JOIN ges_proveedores ON ges_pagosprovdoc.IdProveedor = ges_proveedores.IdProveedor ".
         "WHERE ges_pagosprovdoc.IdPagoProvDoc = $IdPagoProvDoc ";

  $row = queryrow($sql);
  return $row["Proveedor"];
}

function obtenerDocGralProv($IdPagoProvDoc){
  $sql = "SELECT ges_pagosprov.IdComprobanteProv, ".
       "ges_pagosprovdoc.Estado ".
       "FROM ges_pagosprov ".
       "INNER JOIN ges_pagosprovdoc ON ges_pagosprovdoc.IdPagoProvDoc = ges_pagosprov.IdPagoProvDoc ".
       "WHERE ges_pagosprovdoc.IdPagoProvDoc = $IdPagoProvDoc ".
       "AND ges_pagosprov.Eliminado = 0 ";
  
  $res = query($sql);
  $ComprobanteDoc = "";
  $t = '';
  while($row = Row($res)){
      if($row["Estado"] == 'Confirmado'){
          $ComprobanteDoc .= $t.obtenerDocComprobanteProv($row["IdComprobanteProv"]);
          $t = '~~';
      }
  }

  return $ComprobanteDoc;
}

function obtenerDocComprobanteProv($IdComprobanteProv){
  $sql = "SELECT Codigo, TipoComprobante, ".
         "IF(TipoComprobante = 'AlbaranInt',(SELECT MotivoAlbaran FROM ges_motivoalbaran WHERE ges_motivoalbaran.IdMotivoAlbaran = ges_comprobantesprov.IdMotivoAlbaran),'') as MotivoAlbaran ".
         "FROM ges_comprobantesprov ".
         "WHERE ges_comprobantesprov.IdComprobanteProv = '$IdComprobanteProv' ";
  $row = queryrow($sql);
  return $row["TipoComprobante"]." ".$row["MotivoAlbaran"]."~".$row["Codigo"];
    
}

function actualizarMovimientoCjaGral($idoc){
  $sql = "UPDATE ges_librodiario_cajagral SET Eliminado='1' ".
         "WHERE  IdPagoProvDoc = $idoc";
  $res = query($sql,"Documento Modificado");
}

function RegistrarMovimientoBancario($IdLocal,$IdOperacionCaja,$IdOperacionCajaGral,
				     $IdUsuario,$IdCuenta,$TipoMovimiento,$concepto,
				     $cantidad){

  $listkey = "IdLocal,IdUsuario,IdCuentaBancaria,IdOperacionCaja,IdOperacionCajaGral, 
              TipoMovimiento,Concepto,Importe";
  
  $keyvalues = "'$IdLocal','$IdUsuario','$IdCuenta','$IdOperacionCaja','$IdOperacionCajaGral',
                '$TipoMovimiento','$concepto',$cantidad";
  
  $sql = "INSERT INTO ges_movimiento_bancario ($listkey) values ($keyvalues)";
  $res = query($sql,'Insertando nueva operacion cuenta bancaria');  
}

function obtenerIdPartidaCaja($CodPartida){
  $sql = "SELECT IdPartidaCaja as Id ".
         "FROM   ges_partidascaja ".
         "WHERE  Codigo = '$CodPartida'";
  $row = queryrow($sql);
  return $row["Id"];
}


?>
