<?php
function EntregarOperacionCaja($IdLocal,$cantidad,$concepto,$IdPartida,$operacion=false,
			       $fechacaja,$IdArqueo,$TipoVenta,$DocSubsid=false,
			       $CodDocSub=false,$IdTbjoSub=false){
  
  $IdUsuario       = CleanID(getSesionDato("IdUsuario"));
  $IdLical         = ($IdLocal)? $IdLocal : CleanID(getSesionDato("IdTienda"));
  $modalidadpago   = "EFECTIVO";
  $IdModalidadPago = getIdFromMedio($modalidadpago);
  $date = date('Y-m-d H:i:s');

  $mov = new movimiento;
  $mov->set("IdLocal", $IdLocal, FORCE);
  $mov->set("IdUsuario", $IdUsuario, FORCE);
  $mov->set("IdArqueoCaja",$IdArqueo,FORCE);
  $mov->set("IdPartidaCaja",$IdPartida,FORCE);
  $mov->set("TipoVentaOperacion",$TipoVenta,FORCE);
  $mov->set("FechaCaja",$fechacaja,FORCE);
  $mov->set("TipoOperacion",$operacion,FORCE);
  $mov->set("Concepto",$concepto,FORCE);
  $mov->set("Importe",$cantidad,FORCE);
  $mov->set("IdModalidadPago",$IdModalidadPago,FORCE);
  if($DocSubsid) $mov->set("DocSubsidiario",$DocSubsid,FORCE);
  if($CodDocSub) $mov->set("NDocSubsidiario",$CodDocSub,FORCE);
  if($IdTbjoSub) $mov->set("IdTbjoSubsidiario",$IdTbjoSub,FORCE);
  $mov->set("FechaPago",$date,FORCE);
  

  if ($mov->Alta()) { 
    $id = $mov->get("IdOperacionCaja");			
    return $id;
  }
  else
    return false;
}

	
function EntregarCantidades($concepto, $IdLocal,$entregaEfectivo, $entregaBono, 
			    $entregaTarjeta,$IdComprobante,$TipoOperacion="Ingreso",
			    $fechapago=false,$modalidadpago=1,$doccobro=false,$IdUsuario){

        $IdUsuario = ($IdUsuario)? $IdUsuario : getSesionDato("IdUsuario");

	if($entregaEfectivo)
	  EntregarMetalico($IdLocal,$entregaEfectivo,$concepto,$IdComprobante,$TipoOperacion,
			   $fechapago,$modalidadpago,$doccobro,$IdUsuario);
	if($entregaBono)
	  EntregarBono($IdLocal,$entregaBono,$concepto,$IdComprobante,$fechapago);
	if($entregaTarjeta)
	  EntregarTarjeta($IdLocal,$entregaTarjeta,$concepto,$IdComprobante,$doccobro,
			  $fechapago,$IdUsuario);

}	

function EntregarMetalico($IdLocal,$entregado,$concepto,$IdComprobante=false,
			  $TipoOperacion="SinEspecificar",$fechapago=false,$modalidadpago=1,
			  $doccobro=false,$IdUsuario){
	$mov = new Movimiento();

	$mov->SetComprobante($IdComprobante);
	$mov->SetConcepto("Metalico: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,$modalidadpago);
	$mov->set("FechaPago",$fechapago,FORCE,$doccobro);
	
	if ($TipoOperacion!="SinEspecificar")
		$mov->SetTipoOperacion($TipoOperacion);
	$idopcaja = $mov->GuardaOperacion();

	//if($modalidadpago != 1 || $modalidadpago != 9 )

	switch($modalidadpago){
	case '1':
	case '9':
	case '10':
	  break;
	default:
	  if(!$fechapago){
	    $oLocal = new local;
	    $oLocal->Load($IdLocal);
	    $idc = $oLocal->get("CuentaBancaria");

	    $doccobro["id"]  = $idc;
	    $doccobro["op"]  = '';
	    $doccobro["doc"] = '';
	    $doccobro["obs"] = '';
	  }

	  GuardarDocumentosCobros($idopcaja,$doccobro,$IdLocal,$IdUsuario,0,false);
	  RegistrarMovimientoBancario($IdLocal,$idopcaja,0,$IdUsuario,$doccobro["id"],
				      'Ingreso',$concepto,$entregado);
	}
}


function EntregarBono($IdLocal,$entregado,$concepto,$IdComprobante=false,$fechapago){
	$mov = new Movimiento();
	
	$mov->SetComprobante($IdComprobante);
	$mov->SetConcepto("Bono: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,10);
	$mov->set("FechaPago",$fechapago,FORCE);
	$idopcaja = $mov->GuardaOperacion();			
}

function EntregarTarjeta($IdLocal,$entregado,$concepto,$IdComprobante=false,$doccobro=false,
			 $fechapago,$IdUsuario){
	$mov = new Movimiento();
	
	$mov->SetComprobante($IdComprobante);
	$mov->SetConcepto("Tarjeta: $concepto");
	$mov->EntregaEnTienda($IdLocal,$entregado,5);
	$mov->set("FechaPago",$fechapago,FORCE);
	$idopcaja = $mov->GuardaOperacion();
	
	if($doccobro) {
	  GuardarDocumentosCobros($idopcaja,$doccobro,$IdLocal,$IdUsuario,0,false);
	  RegistrarMovimientoBancario($IdLocal,$idopcaja,0,$IdUsuario,$doccobro["id"],
				      'Ingreso',$concepto,$entregado);
	}
}


function getIdFromMedio($medio){
	$medio = strtoupper(trim($medio));
	$sql = "SELECT IdModalidadPago FROM ges_modalidadespago WHERE ModalidadPago='$medio' ";
	$row =queryrow($sql);
	return $row["IdModalidadPago"];
}

function MovimientoFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdOperacionCaja "];
	
	$oMovimiento = new movimiento();
		
	if ($oMovimiento->Load($id))
		return $oMovimiento;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class movimiento extends Cursor {
	var $ingresos;
	var $gastos;
	var $localOperacion;
	var $IdComprobante;
	var $totalmovimiento;
	var $TipoOperacion;
	var $Concepto;
	var $Modalidad;
	
	function SetComprobante($IdComprobante){
		$this->IdComprobante = CleanID($IdComprobante);	
	}
	
	function SetConcepto($concepto){
		$this->Concepto = $concepto;
	}
	
	function EntregaEnTienda($IdLocal,$entregado,$mediodepago){
		if(!isset($this->ingresos[$mediodepago]))
			$this->ingresos[$mediodepago] = 0;
			
		//ModPago: efectivo, tarjeta, etc..

		//$IdModalidadPago = getIdFromMedio($mediodepago);
		$this->set("IdModalidadPago",$mediodepago,FORCE);
		error(__FILE__ . __LINE__ ,"Info: medio es '$mediodepago' ");
			
		//NOTA: era valor absoluto.
		$this->totalmovimiento += $entregado;				
		$this->ingresos[$mediodepago] += $entregado;			
		$this->localOperacion = $IdLocal;
	}
	
	function GuardaOperacion(){
	        global $UltimaInsercion;
	 	$IdComprobante 	 = $this->IdComprobante;
	 	$IdLocal	 = $this->localOperacion;
		$Concepto 	 = CleanRealMysql($this->Concepto);

	 	$TipoOperacion   = $this->GetTipoOperacion();
	 	$Importe	 = $this->GetImporteOperacion();
	 	$Concepto 	 = CleanRealMysql($this->Concepto);
	 	$IdModalidadPago = $this->get("IdModalidadPago");
	 	$IdArqueoCaja    = $this->GetArqueoActivo($IdLocal);
		$TipoVenta       = getSesionDato("TipoVentaTPV");
		$IdUsuario       = CleanID(getSesionDato("IdUsuario"));
		$FechaPago       = ($this->get("FechaPago"))? "'".$this->get("FechaPago")."'":"NOW()";
		
		if($IdModalidadPago != 1){
		  $fecha = ($this->get("FechaPago"))? $this->get("FechaPago"): date("Y-m-d");
		  $date  = date_create($fecha);
		  $fecha = date_format($date, 'Y-m-d');
		  $xIdArqueoCaja = $this->getIdArqueoxFecha($fecha,$IdLocal);
		  $IdArqueoCaja  = ($xIdArqueoCaja)? $xIdArqueoCaja : $IdArqueoCaja;
		}

	 	$values = "IdModalidadPago, Concepto, IdArqueoCaja, IdLocal,
                           TipoOperacion, FechaCaja, IdComprobante, Importe, 
                           FechaInsercion, TipoVentaOperacion, IdUsuario, FechaPago";
	 	$keys   = "'$IdModalidadPago','$Concepto','$IdArqueoCaja','$IdLocal',
                           '$TipoOperacion',CURDATE(),'$IdComprobante','$Importe', NOW(), 
                           '$TipoVenta','$IdUsuario',$FechaPago ";
	  
	 
	 
	 	$sql = "INSERT INTO ges_dinero_movimientos ( $values ) VALUES ( $keys )";
	 	$res = query($sql,"Creando un movimiento de dinero");
		return $UltimaInsercion;
	}
	
	function GetArqueoActivo($IdLocal){
	  $TipoVenta = getSesionDato("TipoVentaTPV");
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
	
	function getIdArqueoxFecha($fecha,$IdLocal){
	  $TipoVenta = getSesionDato("TipoVentaTPV");
	  $sql = 
	    "SELECT IdArqueo ".
	    "FROM   ges_arqueo_caja ".
	    "WHERE  IdLocal   = $IdLocal ".
	    "AND    Eliminado = 0 ".
	    "AND    TipoVentaOperacion = '$TipoVenta' ".
	    "AND    '$fecha' >= DATE(FechaApertura) ".
	    "AND    '$fecha' <= DATE(FechaCierre) ".
	    "ORDER BY IdArqueo DESC ".
	    "LIMIT 1 ";
	  $row = queryrow($sql,'Buscando arqueo de una fecha');
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
		$this->LoadTable("ges_dinero_movimientos", "IdOperacionCaja ", $id);
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
	
		$sql = "INSERT INTO ges_dinero_movimientos ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta movimiento");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdOperacionCaja ",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function getIdArqueoEsCerradoCaja($IdLocal,$TipoVenta){
	  $sql = 
	    "select IdArqueo ".
	    "from   ges_arqueo_caja ".
	    "where  IdLocal = '$IdLocal' ".
	    "and    TipoVentaOperacion = '$TipoVenta' ".
	    "and    esCerrada = 0 ";

	  $row = queryrow($sql);
    
	  return $row["IdArqueo"];

	}

        function getAperturaCaja($IdLocal,$TipoVenta){
	  $sql = 
	    "select FechaApertura ".
	    "from   ges_arqueo_caja ".
	    "where  IdLocal = '$IdLocal' ".
	    "and    TipoVentaOperacion = '$TipoVenta' ".
	    "and    esCerrada = 0 ";

	  $row = queryrow($sql);
	  if (!$row)
	    return 0;
	  
	  return $row["FechaApertura"];
	  
	}



	function Listado($lang,$min=0){

    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
	        $TipoVenta = getSesionDato("TipoVentaTPV");
		$sql = 
		  "SELEC *".
		  "FROM  ges_dinero_movimientos ".
		  "WHERE Eliminado = 0 ".
		  "AND   TipoVentaOperacion = '$TipoVenta'";
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteMovimiento() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdOperacionCaja "));		
		return true;			
	}	
		
	function Modificacion () {
		
		$data = $this->export();				
		
		$sql = CreaUpdateSimple($data,"ges_dinero_movimientos","IdOperacionCaja ",$this->get("IdOperacionCaja "));
		
		$res = query($sql);
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}

	function getIdArqueMovimiento($Id){
	  $this->Load($Id);
	  return $this->get("IdArqueoCaja");
	}
}


function GetArqueoActivoExtra($IdLocal){
  $TipoVenta = getSesionDato("TipoVentaTPV");
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


function getDatosComprobante($IdComprobante,$IdLocal){

  //Array codigo comprobate
  $cod = getCodigoComprobanteFromId($IdComprobante,$IdLocal);

  $sql = 
    "SELECT IdCliente,TotalImporte ".
    "FROM   ges_comprobantes ".
    "WHERE  IdComprobante = '".$IdComprobante."'"; 

  $row = queryrow($sql);

  if (!$row)
    return false;

  return $row["TotalImporte"]."~".$row["IdCliente"]."~".$IdComprobante."~".$cod[1]."~".$cod[0];

}

function getDatosPresupuesto($NPresupuesto,$TipoPresupuesto){
  //$IdLocal = getSesionDato("IdTienda"); 
  $IdLocal   = getSesionDato("IdTiendaDependiente");

  $sql = "SELECT IdCliente,TotalImporte,IdPresupuesto,Serie
            FROM   ges_presupuestos
            WHERE  NPresupuesto    = '$NPresupuesto'
            AND    IdLocal         = '$IdLocal'
            AND    TipoPresupuesto = '$TipoPresupuesto'
            AND    Eliminado       = '0'";

  $row = queryrow($sql);
  if (!$row)
    return false;
  return $row["TotalImporte"]."~".$row["IdCliente"]."~".$row["IdPresupuesto"]."~".$NPresupuesto."~".$row["Serie"];
}

function getIdFromComprobante($nroComprobante,$tipoComprobante,$sreComprobante){
  //$IdLocal = getSesionDato("IdTienda"); 
  $IdLocal   = getSesionDato("IdTiendaDependiente");
  $sql = "SELECT IdComprobante
                FROM ges_comprobantesnum, ges_comprobantestipo
                WHERE NumeroComprobante         =  '$nroComprobante'
                AND ges_comprobantesnum.Status  =  'Emitido'
                AND TipoComprobante             =  '$tipoComprobante'
                AND Serie                       =  '$sreComprobante'
                AND IdLocal                     =  '$IdLocal' 
                AND ges_comprobantestipo.Status =  '0'
                AND ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante
                AND ges_comprobantesnum.Eliminado       = '0'";

  $row = queryrow($sql);
  if (!$row)
    return false;
  return $row["IdComprobante"];
}


function getCodigoComprobanteFromId($IdComprobante,$IdLocal){
  //$IdLocal = getSesionDato("IdTienda"); 
  //$IdLocal   = getSesionDato("IdTiendaDependiente");
  $arr_cod = array();
  $sql = "SELECT NumeroComprobante,Serie
                FROM ges_comprobantesnum, ges_comprobantestipo
                WHERE IdComprobante         =  '$IdComprobante'
                AND ges_comprobantesnum.Status  =  'Emitido'
                AND IdLocal                     =  '$IdLocal' 
                AND ges_comprobantestipo.Status =  '0'
                AND ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante
                AND ges_comprobantesnum.Eliminado         = '0'";
  $row = queryrow($sql);
  if (!$row)
    return false;
  $cod     = $row["Serie"]."-".$row["NumeroComprobante"];
  $arr_cod = explode("-",$cod);
  return $arr_cod;
}
function getIdFromSerieNComprobante($serieticket,$numticket){
  $sql = "SELECT IdComprobante
              FROM  ges_comprobantes
              WHERE ges_comprobantes.SerieComprobante = '$serieticket' 
              AND   ges_comprobantes.NComprobante     = '$numticket' 
              AND    Eliminado        = 0";
  $row = queryrow($sql);
  if (!$row)
    return false;
  return $row["IdComprobante"];
}

function getIdFromCodigoComprobante($serienum){
  $cs = str_replace(" ","",$serienum);
  $sn = explode("-",$cs);
  if (!$cs or $cs=="")
    return false;
  return getIdFromSerieNComprobante($sn[0],$sn[1]);
}  

function getTipoComprobante($IdComprobante,$IdLocal){
  //$IdLocal = getSesionDato("IdTienda"); 
  //$IdLocal   = getSesionDato("IdTiendaDependiente");
  $sql =
    " SELECT TipoComprobante,Serie,IdCliente".
    " FROM  ges_comprobantes ".
    " INNER JOIN ges_comprobantesnum ".
    " ON    ges_comprobantesnum.IdComprobante = ges_comprobantes.IdComprobante ".
    " INNER JOIN ges_comprobantestipo  ".
    " ON    ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante ".
    " WHERE ges_comprobantesnum.IdComprobante = '".$IdComprobante."' ".
    " AND   ges_comprobantes.IdLocal          = '".$IdLocal."' ".
    " AND   ges_comprobantesnum.Status        = 'Emitido' ".
    " AND   ges_comprobantesnum.Eliminado     =  0 ".
    " AND   ges_comprobantes.Eliminado        =  0";
  $row = queryrow($sql);
   if (!$row) 
     return false; 
   return $row["TipoComprobante"].'-'.$row["Serie"].'-'.$row["IdCliente"]; 
}


function DevolverComprobanteTPV($IdComprobante,$Monto,$idDependiente,$Comprobante,
				$Items,$devolvermodo){

         $IdLocal         = getSesionDato("IdTiendaDependiente");
	 $TipoVenta       = getSesionDato("TipoVentaTPV");
	 $TipoComprobante = 'AlbaranInt';
	 $Motivo          = 4;//Devolución
	 $Serie           = $IdLocal;
	 $nroDocumento    = $IdComprobante;

	 //++++++ COMPROBANTE ++++++++++++

	 //Anula y Registra Devoluc Comprobante  
	 if( !AnularNumeroComprobante($IdComprobante,$IdLocal) )
	   registrarCodigoComprobanteOrigen($IdComprobante,$IdLocal,$Motivo,$TipoVenta,
					    $idDependiente);

	 //Trae encabezado comprobante...
	 $comprobante     = obtenerEncabezadoComprobanteVenta($IdComprobante);
 	 $IdCliente       = $comprobante["IdCliente"];
	 $ImporteNeto     = $comprobante["ImporteNeto"];
	 $ImporteImpuesto = $comprobante["ImporteImpuesto"];
	 $TotalImporte    = $comprobante["TotalImporte"];
	 $Impuesto        = $comprobante["Impuesto"];

	 //++++++ CAJA ++++++++++++

	 //registrar sustraccion por devolucion
	 $IdPartida = ( $TipoVenta =='VC')? 16:17; 
	 $concepto  = "Devolucion ".$Comprobante;
	 $arqueo    = new movimiento;
	 $IdArqueo  = $arqueo->GetArqueoActivo($IdLocal);
	 $FechaCaja = $arqueo->getAperturaCaja($IdLocal,$TipoVenta);
	 $MontoDevul= obtenerMontoDevolucion($IdComprobante,$IdLocal,1);
	 $MontoBono = obtenerMontoDevolucion($IdComprobante,$IdLocal,10);
	 $MontoCredito = obtenerMontoDevolucion($IdComprobante,$IdLocal,9);

         if( $MontoDevul > 0 )
	   switch($devolvermodo ){
	   case "credito":    
	     registrarMovimientoCreditoCliente($IdCliente,$MontoDevul,0,$IdLocal,
                                           $IdComprobante,$idDependiente,$concepto,false);
	     break;
	   case "efectivo":
	     EntregarOperacionCaja($IdLocal,$MontoDevul,$concepto,$IdPartida,'Sustraccion',
				   $FechaCaja,$IdArqueo,$TipoVenta);
	     break;
	     
	   }

  	 //++++++ PREVENTA ++++++++++++

	 $textDoc      = 'Preventa';
	 $codDocumento = explode("-",NroComprobantePreVentaMax($IdLocal,$textDoc,$IdArqueo));
	 $sreDocumento = ( $codDocumento[0] != $IdArqueo )? $IdArqueo:$codDocumento[0];
	 $nroDocumento = ( $codDocumento[0] != $IdArqueo )? 1:$codDocumento[1];

	 //Devuelve bono al cliente
	 if($MontoBono > 0)
	   registrarMovimientoBonoCliente($IdCliente,$MontoBono,0,$IdLocal,
					  $IdComprobante,$idDependiente);

	 //Devuelve credito al cliente
	 if($MontoCredito > 0)
	   registrarMovimientoCreditoCliente($IdCliente,$MontoCredito,0,$IdLocal,
                                         $IdComprobante,$idDependiente,$concepto,false);

	 //actualiza deuda cliente
	 actualizarImportePendienteCliente($IdCliente);

	 // crea preventa...
	 Global $UltimaInsercion;

	 $Keys    = "IdLocal,";
	 $Values  = "'".$IdLocal."',";
	 $Keys   .= "IdUsuario,";
	 $Values .= "'".$idDependiente."',";
	 $Keys   .= "IdUsuarioRegistro,";
	 $Values .= "'".$idDependiente."',";
	 $Keys   .= "NPresupuesto,";
	 $Values .= "'".$nroDocumento."',";
	 $Keys   .= "TipoPresupuesto,";
	 $Values .= "'".$textDoc."',";
	 $Keys   .= "TipoVentaOperacion,";
	 $Values .= "'".$TipoVenta."',";
	 $Keys   .= "FechaPresupuesto,";
	 $Values .= "NOW(),";
	 $Keys   .= "ImporteNeto,";
	 $Values .= "'".$ImporteNeto."',";
	 $Keys   .= "ImporteImpuesto,";
	 $Values .= "'".$ImporteImpuesto."',";
	 $Keys   .= "Impuesto,";
	 $Values .= "'".$Impuesto."',";
	 $Keys   .= "TotalImporte,";
	 $Values .= "'".$TotalImporte."',";
	 $Keys   .= "Status,";
	 $Values .= "'Pendiente',";
	 $Keys   .= "IdCliente,";
	 $Values .= "'".$IdCliente."',";
	 $Keys   .= "ModoTPV,";
	 $Values .= "'venta',";
	 $Keys   .= "Serie";
	 $Values .= "'".$sreDocumento."'";
	 $sql     = "insert into ges_presupuestos (".$Keys.") values (".$Values.")";
	 query($sql);

	 //Presupuesto
	 $IdPresupuesto = $UltimaInsercion;  
	 
	 //Historial Venta...
	 cargarVenta2HistorialVenta($IdCliente,$TotalImporte,false,false);

	 //++++++ DETALLE PREVENTA ++++++++++++

	 // trae detalle comprobante...
	 $sql= 
	   " select IdProducto, ".
	   " sum(Cantidad) as Cantidad, ".
	   " sum(Precio) as Precio, ".
	   " sum(Descuento) as Descuento, ".
	   " sum(Importe) as Importe, ".
	   " Concepto,Talla,Color,Referencia,".
	   " CodigoBarras,".
	   " group_concat(IdPedidoDet) as IdPedidoDet,".
	   " group_concat(IdAlbaran) as IdAlbaran, Serie, IdComprobanteDet ".
	   " from   ges_comprobantesdet ".
	   " where  IdComprobante = '".$IdComprobante."' ".
	   " and    Eliminado     = 0 ".
	   " and    IdPedidoDet   <> 0 ".
	   " group  by IdProducto ";
	 $res = query($sql);
	 if (!$res) return false;

	 while($row = Row($res))
	   {
	     $Keys    = "IdPresupuesto,";
	     $Values  = "'".$IdPresupuesto."',";
	     $Keys   .= "IdProducto,";
	     $Values .= "'".$row['IdProducto']."',";
	     $Keys   .= "Cantidad,";
	     $Values .= "'".$row['Cantidad']."',";
	     $Keys   .= "Precio,";
	     $Values .= "'".$row['Precio']."',";
	     $Keys   .= "Descuento,";
	     $Values .= "'".$row['Descuento']."',";
	     $Keys   .= "Importe,";
	     $Values .= "'".$row['Importe']."',";
	     $Keys   .= "Concepto,";
	     $Values .= "'".$row['Concepto']."',";
	     $Keys   .= "Talla,";
	     $Values .= "'".$row['Talla']."',";
	     $Keys   .= "Color,";
	     $Values .= "'".$row['Color']."',";
	     $Keys   .= "Referencia,";
	     $Values .= "'".$row['Referencia']."',";
	     $Keys   .= "CodigoBarras";
	     $Values .= "'".$row['CodigoBarras']."'";
	     $sql     = "insert into ges_presupuestosdet (".$Keys.") values (".$Values.")";
	     query($sql);   
	   }

         //++++++ LIBERA SERIES PRODUCTOS COMPROBANTE DETALLE +++++++    

	 // trae detalle comprobante...
	 $sql= 
	   " select IdProducto,Cantidad,IdPedidoDet,".
	   "        IdAlbaran,Serie,IdComprobanteDet ".
	   " from   ges_comprobantesdet ".
	   " where  IdComprobante = '".$IdComprobante."' ".
	   " and    Eliminado     = 0 ".
	   " and    Serie         = 1 ".
	   " and    IdPedidoDet  <> 0 ";
	 $res = query($sql);
	 if (!$res) return false;

	 while($row = Row($res))
	   {
	     $DocumentoSalida = ( $row["IdAlbaran"] )? $row["IdAlbaran"]:$IdComprobante;
	     $aSeries         = getSeries2IdProductoVentas($DocumentoSalida,$row["IdProducto"],$row["IdPedidoDet"]);
	     // retorna las series...
	     registraDevolucionSeriesVenta($row["IdProducto"],$IdComprobante,
					   $IdPresupuesto,$aSeries);
	     // libera series del detalle comprobante...
	     $sql = " update ges_comprobantesdet set Serie = 0 ".
	            " where  IdComprobanteDet=".$row['IdComprobanteDet'];
	     query($sql);   
	   }

         //++++++ MARCAR PRODUCTOS Y CANTIDAD COMPROBANTES DETALLE ++++++++++

	 $xitem = explode("~",$Items);
	 foreach ($xitem as $key=>$xdet){
	   	 $xdetalle = explode(":",$xdet);
		 $sql = 
		   " update ges_comprobantesdet set CantidadDevolucion = ".$xdetalle[1].
		   " where  IdComprobanteDet=".$xdetalle[0];
		 query($sql);   
	 }

	 //++++++ KARDEX ++++++++++++

	 $almacenes = new almacenes;
	 $Operacion = 2;//Venta - Devolucion
	 $sql= 
	   " select IdProducto,Cantidad,CostoUnitario,".
	   "        IdPedidoDet,IdComprobanteDet ".
	   " from   ges_comprobantesdet ".
	   " where  IdComprobante  = '$IdComprobante' ".
	   " and    IdPedidoDet   <> 0 ".
	   " and    Eliminado      = 0 ";
	 $res = query($sql);
	 if (!$res) return;

	 while($row = Row($res))
	   {
	     $id          = $row['IdProducto'];
	     $costo       = $row['CostoUnitario'];
	     $existencias = $almacenes->obtenerExistenciasKardex($id,$IdLocal);
	     $Obs         = $concepto;

	     // registra ingreso por pedidodet
	     registrarEntradaKardexFifo($id,$row['Cantidad'],$costo,
					$Operacion,$IdLocal,
					$row['IdPedidoDet'],$existencias,
					0,0,$row['IdComprobanteDet'],$Obs);	   

	     // actualiza registros kardex
	     $almacenes->actualizarCosto($id,$IdLocal);
	     actualizaResumenKardex($id,$IdLocal);
             actualizaDevolucionVitrina($row['Cantidad'],$IdLocal,$id);
	   }
	 
	 return " ~".$IdPresupuesto;
}

function  RegistrarNumeroComprobante($nroDocumento,$IdComprobante,$TipoComprobante,
				     $Serie,$tv=false,$Origen=false,$IdUsuario=false){

          $TipoVenta = ( $tv )?  $tv:getSesionDato("TipoVentaTPV");
	  $IdLocal   = getSesionDato("IdTiendaDependiente");
	  $IdLocal   = (!isset($IdLocal) )? getSesionDato("IdTienda"):$IdLocal;
	  $IdLocal   = ($Origen)? $Origen : $IdLocal;
	  $IdTipoComprobante = ObtenerIdTipoComprobante($IdLocal,
							$TipoComprobante,
							$Serie);
	  $IdUsuario = ($IdUsuario)? $IdUsuario:getSesionDato("IdUsuario");

	  $Keys    = " IdComprobante,"; 
	  $Values  = "'$IdComprobante',"; 
	  $Keys   .= " IdTipoComprobante,"; 
	  $Values .= "'$IdTipoComprobante',";
	  $Keys   .= " IdUsuario,"; 
	  $Values .= "'$IdUsuario',";
	  $Keys   .= " NumeroComprobante,"; 
	  $Values .= "'$nroDocumento',"; 
	  $Keys   .= " Status,";
	  $Values .= "'Emitido',";
	  $Keys   .= " TipoVenta,";
	  $Values .= "'$TipoVenta',";
	  $Keys   .= " Eliminado";
	  $Values .= "'0'";
	  $sql     = " insert into ges_comprobantesnum (".$Keys.") values (".$Values.")";
	  query($sql);

	  actualizarUsuarioComprobante($IdComprobante,$IdUsuario);
}

function ModificarNumeroComprobante($nroDocumento,$TipoComprobante,$IdComprobante,$accion,$Serie,$IdUsuario){

  $IdLocal   = getSesionDato("IdTiendaDependiente");
  $IdTipoComprobante = ObtenerIdTipoComprobante($IdLocal,$TipoComprobante,$Serie);
  $n_nro = false;
  //Set Elimnado 1
  if($accion=='Modificar')
    if(!EliminarNumeroComprobante($IdComprobante,$IdLocal))
      $n_nro=true;
  
  //Set Status Anulado
  if($accion=='Modificar_y_Anular'||$accion=='Anular')
    if(!AnularNumeroComprobante($IdComprobante,$IdLocal))
      $n_nro=true;

  //Anula el comprobante dejando solo el Ticket
  if($accion=='Anular')
    {
      $TipoComprobante = 'Ticket';
      $Serie = $IdLocal;
    }

  //Registra nuevo Nro
  if($n_nro)
    RegistrarNumeroComprobante($nroDocumento,$IdComprobante,$TipoComprobante,$Serie,false,false,$IdUsuario);

  $campoxdato = "Metalico: ".$TipoComprobante." Venta ".$Serie."-".$nroDocumento;
  ModificarMovDocComprobante($IdComprobante,$campoxdato);
}

function FacturarNumeroComprobante($nroDocumento,$TipoComprobante,$IdComprobante,$accion,$Serie,$IdUsuario){

         $IdLocal   = getSesionDato("IdTiendaDependiente");

	 if($accion=='Facturar' && $TipoComprobante=='Albaran')
	   {
	     //Liberamos el numero albaran comprobante & Registramos el nuevo numero comprobante
	     if(!LiberarAlbaranComprobante($IdComprobante,$IdLocal))
	       {
		 RegistrarNumeroComprobante($nroDocumento,$IdComprobante,'Factura',
					    $Serie,false,false,$IdUsuario);
		 ActualizarFechaEmisionComprobante($IdComprobante);
	       }
	 }
	 
	 if($accion=='Facturar' && $TipoComprobante=='Boleta')
	   {
	     //Liberamos el numero boleta comprobante
	     AnularNumeroComprobante($IdComprobante,$IdLocal);

	     //Registramos el nuevo numero comprobante
	     RegistrarNumeroComprobante($nroDocumento,$IdComprobante,'Factura',$Serie,false,false,$IdUsuario);
	     ActualizarFechaEmisionComprobante($IdComprobante);
	   }
	 
	 if($accion=='Facturar' && $TipoComprobante=='Ticket')
	   {
	     //Liberamos el numero boleta comprobante
	     AnularNumeroComprobante($IdComprobante,$IdLocal);
	     
	     //Registramos el nuevo numero comprobante
	     RegistrarNumeroComprobante($nroDocumento,$IdComprobante,'Factura',$Serie,false,false,$IdUsuario);
	     ActualizarFechaEmisionComprobante($IdComprobante);
	 }

	 $campoxdato = "Metalico: Factura Venta ".$Serie."-".$nroDocumento;
	 ModificarMovDocComprobante($IdComprobante,$campoxdato);
}

function FacturarLoteComprobante($nroDocumento,$ltAlbaran,$cliAlbaran,$Serie,$IdComprobante,$IdUsuario){
  
  $IdLocal    = getSesionDato("IdTiendaDependiente");
  $altAlbaran = explode("-",$ltAlbaran);
  //$IdTipoComprobante = ObtenerIdTipoComprobante($IdLocal,$TipoComprobante,$Serie);
  $listIdNum    = Array();
  $ImporteNeto  = 0;
  $IvaImporte   = 0;
  $TotalImporte = 0;
  $ImportePendiente = 0;
  $Importes     = Array();

  foreach ($altAlbaran as $key=>$Id){

    //Lista de IdNumComprobante
    array_push($listIdNum, getIdNumFromIdComprobante($Id.":".$IdComprobante));

    //Liberamos el numero albaran IdComprobante
    LiberarAlbaranComprobante($Id,$IdLocal);

    //Guardamos los importes de albaran IdComprobante
    $Importes      = getImporteFromComprobante($Id);//Faltaaa***
    $ImporteNeto  += $Importes[0];
    $IvaImporte   += $Importes[1];
    $TotalImporte += $Importes[2];
    $ImportePendiente += $Importes[3];

  }
  
  //Registar Factura en ges_comprobantes
  $IdComprobante = RegistrarComprobanteFactura($ImporteNeto,
					       $IvaImporte,
					       $TotalImporte,
					       $cliAlbaran,
					       $Serie,
					       $ImportePendiente,
					       $nroDocumento,$listIdNum);
  //Registramos el nuevo numero Factura IdComprobante en ges_comprobantesnum
  RegistrarNumeroComprobante($nroDocumento,$IdComprobante,'Factura',$Serie,false,false,$IdUsuario);

  //Registra nuevo IdComprobante   en ges_comprobantesdet 
  foreach ($altAlbaran as $key=>$Id){
    $IdAlbaran = $listIdNum[$key];
    RegistrarIdComprobanteDetalle($Id,$IdComprobante,$IdAlbaran);//Faltaaa******

  }

}

function RegistrarIdComprobanteDetalle($Id,$IdComprobante,$IdAlbaran){

  //Quita IdComprobante albaran por el nuevo IdComprobante,
  $sql = 
    " UPDATE ges_comprobantesdet".
    " SET    IdComprobante = '".$IdComprobante."' ".
    " WHERE  IdComprobante = '".$Id."' ".
    " AND    Eliminado     = '0'";
  query($sql);

}

function getImporteFromComprobante($Id){

  $imp = Array();//Lista importes
  $sql = 
    "SELECT ImporteNeto,ImporteImpuesto,TotalImporte,ImportePendiente ".
    "FROM   ges_comprobantes ".
    "WHERE  IdComprobante = '".$Id."'"; 
  $res = query($sql);

  while( $row = Row( $res )) {
    array_push($imp,
	       $row["ImporteNeto"],
	       $row["ImporteImpuesto"],
	       $row["TotalImporte"],
	       $row["ImportePendiente"]);//add importe
  }
  return $imp;
}

function RegistrarComprobanteFactura($ImporteNeto,$IvaImporte,$TotalImporte,$IdCliente,$Serie,
				     $ImportePendiente,$Num,$listIdNum){

  global $UltimaInsercion; 
  $IdLocal       = getSesionDato("IdTiendaDependiente");
  $idDependiente = getSesionDato("IdUsuario");
  $TipoVenta     = getSesionDato("TipoVentaTPV");
  $IGV           = getSesionDato("IGV");    
  $IdAlbaran     = implode(",",$listIdNum); 
  $Status        = ($ImportePendiente > 0.01)? 1:2;
  $Serie         = "CS".$Serie;  
  $Num           = getNumSerieTicket($Serie);
  $esquema =
    "IdLocal, IdUsuario, SerieComprobante,".
    "NComprobante,TipoVentaOperacion,FechaComprobante,".
    "ImporteNeto, ImporteImpuesto, Impuesto, TotalImporte,".
    "ImportePendiente, Status,IdCliente,IdAlbaranes";
  
  $datos = 
    "'$IdLocal','$idDependiente','$Serie',".
    "'$Num','$TipoVenta',NOW(),".
    "'$ImporteNeto','$IvaImporte','$IGV','$TotalImporte',".
    "'$ImportePendiente','$Status','$IdCliente','$IdAlbaran'";
  
  $sql = "INSERT INTO ges_comprobantes (".$esquema.") VALUES (".$datos.")";
  query($sql);
  return $UltimaInsercion;
}

function BoletarNumeroComprobante($nroDocumento,$TipoComprobante,$IdComprobante,$accion,$Serie,$IdUsuario){

         $IdLocal           = getSesionDato("IdTiendaDependiente");
	 $IdTipoComprobante = ObtenerIdTipoComprobante($IdLocal,$TipoComprobante,$Serie);
	 
	 if($accion=='Boletar' && $TipoComprobante=='Albaran'){
	   
	   //Liberamos el numero albaran comprobante
	   if(!LiberarAlbaranComprobante($IdComprobante,$IdLocal))

	     //Registramos el nuevo numero comprobante
	     RegistrarNumeroComprobante($nroDocumento,
					$IdComprobante,
					'Boleta',
					$Serie,false,false,$IdUsuario);
	 }
	 
	 if($accion=='Boletar' && $TipoComprobante=='Ticket'){
	   //Liberamos el numero boleta comprobante
	   AnularNumeroComprobante($IdComprobante,$IdLocal);
	   //Registramos el nuevo numero comprobante
	   RegistrarNumeroComprobante($nroDocumento,
				      $IdComprobante,
				      'Boleta',
				      $Serie,false,false,$IdUsuario);
	 }

	 $campoxdato = "Metalico: Boleta Venta ".$Serie."-".$nroDocumento;
	 ModificarMovDocComprobante($IdComprobante,$campoxdato);
}

function LiberarAlbaranComprobante($IdComprobante,$IdLocal){

         // optiene IdNumComprobante
         $IdNumComprobante = getIdNumFromIdComprobante($IdComprobante);

	 //Quita IdComprobante 
	 $sql = "UPDATE ges_comprobantesnum 
              SET    Status            = 'Facturado' ".
         //"        IdComprobante     = '' ".
            " WHERE  IdComprobante     = '".$IdComprobante."'
              AND    Eliminado         = '0'";
	 query($sql);

	 // registra IdNumComprobante 
	 $sql = "UPDATE ges_comprobantes 
              SET    IdAlbaranes       = '".$IdComprobante."'
              WHERE  IdComprobante     = '".$IdComprobante."'
              AND    Eliminado         = '0'";
	 query($sql);

	 //registra IdNumComprobante en Detalles 
	 $sql = "UPDATE ges_comprobantesdet
              SET    IdAlbaran     = '".$IdComprobante."'
              WHERE  IdComprobante = '".$IdComprobante."'";
	 echo query($sql);

}

function AnularNumeroComprobante($IdComprobante,$IdLocal){

         $sql = "update ges_comprobantesnum 
              set    Status        = 'Anulado'
              where  IdComprobante = '$IdComprobante'
              and    Eliminado     = '0'";
	 echo query($sql);
}

function EliminarNumeroComprobante($IdComprobante,$IdLocal){

         $IdComprobante = CleanID($IdComprobante);
	 $IdLocal = CleanID($IdLocal);
	 $sql = "UPDATE ges_comprobantesnum 
              SET    Eliminado          = '1'
              WHERE  IdComprobante     = '$IdComprobante'
              AND    Eliminado         = '0'";
	 echo query($sql);
}

function ActualizarFechaEmisionComprobante($IdComprobante){

         $IdComprobante = CleanID($IdComprobante);
	 $sql = "UPDATE ges_comprobantes 
                 SET    FechaComprobante  = Now()
                 WHERE  IdComprobante     = '$IdComprobante'
                 AND    Eliminado         = '0'";
	 query($sql);
}

function ObtenerIdTipoComprobante($IdLocal,$textDoc,$Serie){

         $sql =
	   "SELECT IdTipoComprobante FROM ges_comprobantestipo ".
	   "WHERE IdLocal = '".$IdLocal."' ".
	   "AND   TipoComprobante='".$textDoc."' ".
	   "AND   Serie   = '".$Serie."' ".
	   "AND   Status  = '0'";
	 $res=query($sql);
	 $row= Row($res);
	 return $row["IdTipoComprobante"];
}

function getIdProductoFromIdMetaProducto($IdMetaProducto){

         $sql =
	   "select IdProducto ". 
	   "from   ges_metaproductos ".
	   "where  IdMetaProducto='".$IdMetaProducto."'";
	 $res = query($sql);
	 $row = Row($res);
	 return $row["IdProducto"];
}

function getIdNumFromIdComprobante($IdComprobante){

         $sql = "SELECT IdNumComprobante 
          FROM   ges_comprobantesnum
          WHERE  IdComprobante='$IdComprobante'
          AND    Status = 'Emitido'
          AND    Eliminado = '0'";
	 $res=query($sql);
	 $row= Row($res);
	 return $row["IdNumComprobante"];
}

function getNumSerieTicket($Serie){
         $sql = 
	   " select NComprobante".
	   " from   ges_comprobantes ".
	   " where  SerieComprobante like '".$Serie."'".
	   " order  by NComprobante desc ".
	   " limit  0 , 1";
	 $res=query($sql);
	 if($row= Row($res)) 
	   return $row["NComprobante"]+1;
	 else
	   return 1;
}

function getDetFromCBMetaProducto($codigo){

         $res = getRowFromCBMetaProducto($codigo);
	 $arr = array();
	 $det = ' ';
	 while( $row = Row($res) )
	   {
	     //N/S
	     $ns = getNSFromMetaProductoDet($row['IdProducto'],$row['IdComprobante']);
	     //Producto
	     $det = $det." <br/> * ".
	       //$row['CodigoBarras']." ".
	       $row['Descripcion']."   CANT:".
	       $row['Cantidad']."   ".
	       $ns;
	   }
	 return $det;
}

function  getDetFromCBMetaProductoAlbaran($codigo=array(),$IdProducto,$conNS=true){

  $a_ns   = array();
  $a_prod = array();
  $allseries='';

  foreach ($codigo as $key=>$nserie){ 

    //Guardamos N/S
    if( $allseries!='' )
      $allseries = $allseries.",'".$nserie."'";
    else
      $allseries = "'".$nserie."'";

    //Obtiene detalle para cada series mproducto
    $res  = getRowFromCBMetaProducto($nserie,$IdProducto);

    while( $row = Row($res) ){

      //Obtiene NS
      $ns = getNSFromMetaProductoDet($row['IdProducto'],$row['IdComprobante']);
      $ns = str_replace('N/S: ','', $ns);

      //guardamos registros de series y cantidad en array
      if($ns=='')
	{
	  //Sin N/S
	  if(isset( $a_prod[$row['IdProducto']] ))
	    $a_prod[$row['IdProducto']] = $a_prod[$row['IdProducto']]." ".$row['IdProducto'];
	  else 
	    $a_prod[$row['IdProducto']] = $row['IdProducto'];
	}
      else
	{

	  //Con N/S
	  if(isset( $a_prod[$row['IdProducto']] ))
	    $a_prod[$row['IdProducto']] = $a_prod[$row['IdProducto']]." ". $ns;
	  else
	    $a_prod[$row['IdProducto']] = $ns;
	}
    }
  }

  $res  = getRowFromAllCBMetaProducto($allseries,$IdProducto);
  $det = '';
  while( $row = Row($res) ){
    //Producto
    $a_ns = array();
    $ns   = $a_prod[$row['IdProducto']];
    $a_ns = explode(' ',$ns);
    $cant = count($a_ns);
    $ns   = "N/S: ".$ns; 
    if($row['IdProducto']==$a_ns[0] || $conNS)
      $ns='';
    $det .= "<br/> * ".$row['Descripcion']." CANT:".$row['Cantidad']."  ".$ns; 
  }
  
  return $det;
}

function getItemProducto($descripcion_0,$NL=85){

  $num_caract_0  = strlen($descripcion_0);
  $contadorTexto = 0;
  $arrayTexto    = explode(' ',$descripcion_0);
  $acotadotext   = "";
  $acotado       = array();
  $tamanoTexto   = $NL;
  $tamanoControl = 0;
  $arrnum        = count($arrayTexto);
  //DESCRIPCION 
  //DISTRIBUYE ENTRE LINEAS 
  while( $num_caract_0 > $tamanoControl )
    {

      while( $tamanoTexto >= strlen($acotadotext)+ strlen($arrayTexto[$contadorTexto]) )
	{
	  $acotadotext .= " ".$arrayTexto[$contadorTexto];

	  $contadorTexto++;

	  if(!isset($arrayTexto[$contadorTexto]) ) break;
	}	     
      
      if(trim($acotadotext)!='') 
	array_push($acotado,$acotadotext);

      $tamanoControl = $tamanoControl + strlen($acotadotext);
      $acotadotext='';
    }
  return $acotado;
}

function getNSFromCBMPPresupuesto($IdLocal,$IdProducto,$CBMP){
  $series = explode(",", $CBMP);
  $esNS = array();
  foreach ($series as $ns=>$linens){

    if(getCBMetaFromMetaProducto($IdLocal,$linens,$IdProducto))
      array_push($esNS,$linens);

  }
  return $esNS;
}

function getItemMetaProducto($MP,$NS,$series,$IdProducto,$NL=64){
  //META PRODUCTO
  $itemmprod = array();
  $acotmp    = array();

  if( $MP == 1 && $NS == 1 ){

    $detmprod  = getDetFromCBMetaProductoAlbaran($series,$IdProducto,false);
    
    if ($detmprod != '')
      $itemmprod = explode("<br/>",$detmprod);

    foreach ($itemmprod as $key=>$linemp){

      if($key > 0){
	$numcaract  = strlen($linemp);
	$contTexto  = 0;
	$arrTexto   = explode(' ',$linemp);
	$acottext   = "";
	$tamTexto   = $NL;
	$tamControl = 0;

	while( $numcaract > $tamControl ){
	  while( $tamTexto >= strlen($acottext)+ strlen($arrTexto[$contTexto])){
	    $acottext .= " ".$arrTexto[$contTexto];
	    $contTexto++;

	    if(!isset($arrTexto[$contTexto]) ) break;
	  }	     
	  //add new line
	  if(trim($acottext)!='') 
	    array_push($acotmp,$acottext);
	  $tamControl = $tamControl + strlen($acottext);
	  $acottext='';		  
	}

      }
    }
  }
  return $acotmp;
}

function getRowFromCBMetaProducto($codigo,$IdProducto=0){

  $TipoVenta     = getSesionDato("TipoVentaTPV");
  $IdLocal       = getSesionDato("IdTiendaDependiente");
  $conIdProducto = ( $IdProducto != 0 )? " AND ges_metaproductos.IdProducto = '".$IdProducto."'":"";

  $sql= 
    " SELECT ges_metaproductosdet.IdProducto,".
    "        ges_metaproductosdet.IdMetaProducto,".
    "        ges_metaproductosdet.CodigoBarras,Cantidad,".
    "        CONCAT(Descripcion,' ',Marca,' ',Color,' ',Talla,' ',NombreComercial) as Descripcion,".
    "        IdComprobante".
    " FROM   ges_metaproductosdet ".
    " INNER  JOIN ges_metaproductos ON        ".
    "          ges_metaproductosdet.IdMetaProducto   = ges_metaproductos.IdMetaProducto".
    " INNER  JOIN ges_productos ON ".
    "        ges_productos.IdProducto = ges_metaproductosdet.IdProducto ".
    " INNER  JOIN ges_laboratorios ON ".
    "        ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
    " INNER  JOIN ges_marcas ON ".
    "        ges_productos.IdMarca = ges_marcas.IdMarca ".
    " INNER  JOIN ges_productos_idioma ON ".
    "        ges_productos_idioma.IdProdBase   =   ges_productos.IdProdBase ".
    " WHERE  ".
    //"ges_metaproductos.TipoVentaOperacion = '".$TipoVenta."' ".
    "        ges_metaproductos.CBMetaProducto     = '".$codigo."' ".
    $conIdProducto.
    " AND    ges_metaproductos.IdLocal            = '".$IdLocal."'  ".
    //" AND    ges_metaproductos.Status             = 'Finalizado' ".
    " AND    ges_metaproductos.Eliminado          = '0' ".
    " AND    ges_metaproductosdet.Eliminado       = '0' ";
 $res = query($sql);
 return $res;
}

function getRowFromAllCBMetaProducto($codigo,$IdProducto){

  $TipoVenta       = getSesionDato("TipoVentaTPV");
  //$IdLocal         = getSesionDato("IdTienda");
  $IdLocal   = getSesionDato("IdTiendaDependiente");
  $sql= 
    " SELECT ges_metaproductosdet.IdProducto,".
    "        ges_metaproductosdet.IdMetaProducto,".
    "        ges_metaproductosdet.CodigoBarras,SUM(Cantidad) as Cantidad,".
    "        CONCAT(Descripcion,' ',Marca,' ',Color,' ',Talla) as Descripcion".
    " FROM   ges_metaproductosdet ".
    " INNER  JOIN ges_metaproductos ON        ".
    "          ges_metaproductosdet.IdMetaProducto   = ges_metaproductos.IdMetaProducto".
    " INNER  JOIN ges_productos ON ".
    "        ges_productos.IdProducto = ges_metaproductosdet.IdProducto ".
    " INNER   JOIN ges_marcas ON ".
    "        ges_productos.IdMarca = ges_marcas.IdMarca ".
    " INNER  JOIN ges_productos_idioma ON ".
    "        ges_productos_idioma.IdProdBase   =   ges_productos.IdProdBase ".
    " WHERE  ".
    "        ges_metaproductos.CBMetaProducto  in (".$codigo.") ".
    " AND    ges_metaproductos.IdLocal         = '".$IdLocal."'  ".
    " AND    ges_metaproductos.IdProducto      = '".$IdProducto."'  ".
    " AND    ges_metaproductos.Eliminado       = '0' ".
    " AND    ges_metaproductosdet.Eliminado    = '0' ".
    " GROUP BY ges_metaproductosdet.IdProducto ";
 $res = query($sql);
 return $res;
}

function getCBMetaFromMetaProducto($IdLocal,$codigo,$IdProducto){

  $sql= 
    " SELECT *".
    " FROM  ges_metaproductos ".
    " WHERE CBMetaProducto  = '".$codigo."'".
    " AND   IdLocal         = '".$IdLocal."'  ".
    " AND   IdProducto      = '".$IdProducto."'  ".
    " AND   Eliminado       = '0' ";
    $res = query($sql);
    if($row= Row($res)) 
      return true;
    else
      return false;
}



function getDescripcionFromCBMetaProducto($codigo){

  $sql=
    "SELECT CONCAT(Descripcion,' ',Marca,' ',Color,' ',Talla) as MProducto ".
    "FROM   ges_metaproductos ".
    "INNER  JOIN ges_productos  ON ".
    "       ges_metaproductos.IdProducto  = ges_productos.IdProducto ".
    "INNER  JOIN ges_modelos ON ".
    "       ges_productos.IdColor = ges_modelos.IdColor ".
    "INNER  JOIN ges_detalles ON ".
    "       ges_productos.IdTalla = ges_detalles.IdTalla ".
    "INNER  JOIN ges_marcas ON ".
    "       ges_productos.IdMarca = ges_marcas.IdMarca ".
    "INNER  JOIN ges_productos_idioma ON ".
    "       ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
    "WHERE  ges_metaproductos.CBMetaProducto = '".$codigo."' ";

  $res=query($sql);
  $row= Row($res);
  return $row["MProducto"];
}

function ValidarNumeroComprobante($nroComprobante,$TipoComprobante,$Serie){

  $sql = 
    " SELECT NumeroComprobante ".
    " FROM   ges_comprobantesnum  ".
    " INNER  JOIN ges_comprobantestipo ".
    " ON     ges_comprobantestipo.IdTipoComprobante =  ges_comprobantesnum.IdTipoComprobante ".
    " WHERE  ges_comprobantestipo.Status            = '0' ".
    " AND    ges_comprobantestipo.Serie             = '".$Serie."' ".
    " AND    ges_comprobantestipo.TipoComprobante   = '".$TipoComprobante."'".
    " AND    ges_comprobantesnum.NumeroComprobante  = '".$nroComprobante."'".
    " AND    ges_comprobantesnum.Eliminado          = '0'";

  $row = queryrow($sql);
  if($row)
    echo true;
  else
    echo false;
}

function ValidarNumeroPresupuesto($nroPresupuesto,$TipoComprobante,$Serie){
  //$IdLocal = getSesionDato("IdTienda");
  $IdLocal   = getSesionDato("IdTiendaDependiente");
  $sql = 
    " SELECT NPresupuesto ".
    " FROM   ges_presupuestos ".
    " WHERE  IdLocal         = '".$IdLocal."' ".
    " AND    TipoPresupuesto = '".$TipoComprobante."' ".
    " AND    Serie           = '".$Serie."' ".
    " AND    NPresupuesto    = '".$nroPresupuesto."' ".
    " AND    Eliminado       = '0'";

  $row = queryrow($sql);
  if($row)
    echo true;
  else
    echo false;
}

function extSerieComprobante($IdLocal,$Serie,$TipoComprobante){

  if($TipoComprobante == 'Proforma'||$TipoComprobante == 'Preventa')
    {
      $tabla = " ges_presupuestos ";
      $where = " AND TipoPresupuesto = '".$TipoComprobante."' ";
    }
  else 
    {
      $tabla = " ges_comprobantestipo ";
      $where = " AND TipoComprobante = '".$TipoComprobante."' ";
    }

  $sql = 
    "SELECT Serie ".
    "FROM   ".$tabla." ".
    "WHERE  IdLocal          = '".$IdLocal."' ".
    $where.
    " AND   Serie           = '".$Serie."'".
    " AND   Eliminado       = '0'";
  $row = queryrow($sql);
  if($row)
    return true;
  else
    return false;
}

function regitraSerieComprobante($IdLocal,$Serie,$TipoComprobante){
  $esquema = "TipoComprobante,Serie,IdLocal";
  $datos   = "'".$TipoComprobante."','".$Serie."','".$IdLocal."'";
  $sql     = "INSERT INTO ges_comprobantestipo (".$esquema.") VALUES (".$datos.")";
  return query($sql);
}

function cargarSerieDefComprobante($IdLocal,$TipoComprobante){

  if($TipoComprobante == 'Proforma' || $TipoComprobante == 'Preventa')
    {
      $sql = 
	" SELECT Serie ".
	" FROM   ges_presupuestos ".
	" WHERE  TipoPresupuesto = '".$TipoComprobante."'".
	" AND    IdLocal = '".$IdLocal."'".
	" ORDER  BY IdPresupuesto DESC LIMIT 0,1 ";
    }
  else
    {
      $sql = 
	" SELECT Serie ".
	" FROM   ges_comprobantesnum ".
	" INNER JOIN ges_comprobantestipo ON ".
	" ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante ".
	" WHERE ges_comprobantestipo.Status = 0 ".
	" AND   ges_comprobantestipo.TipoComprobante = '".$TipoComprobante."'".
	" AND   ges_comprobantestipo.IdLocal = '".$IdLocal."'".
	" ORDER BY ges_comprobantesnum.IdNumComprobante DESC LIMIT 0,1 ";
    }

  $res=query($sql);
  $row= Row($res);
  if($row["Serie"])
    return $row["Serie"];
  else 
    return $IdLocal;
}

function NroComprobanteVentaMax($IdLocal,$TipoComprobante,$Serie){

  //Valida serie
  if(!extSerieComprobante($IdLocal,$Serie,$TipoComprobante))
    $Serie = cargarSerieDefComprobante($IdLocal,$TipoComprobante);

  $sql = "SELECT MAX( NumeroComprobante ) AS NroComprobante".
    " FROM   ges_comprobantesnum  ".
    " INNER JOIN ges_comprobantestipo ".
    " ON    ges_comprobantestipo.IdTipoComprobante =  ges_comprobantesnum.IdTipoComprobante ".
    " WHERE  ges_comprobantestipo.Status            = '0' ".
    " AND    ges_comprobantestipo.Serie             = '".$Serie."' ".
    " AND    ges_comprobantestipo.TipoComprobante   = '".$TipoComprobante."'".
    " AND    ges_comprobantesnum.Eliminado          = '0'";
  $res=query($sql);
  $row= Row($res);
 
  if($row["NroComprobante"])
    $nro = $row["NroComprobante"]+1;
  else
    $nro = 1;

  //codigo documento
  $cod = $Serie."-".$nro;

  return $cod;

}

function NroComprobantePreVentaMax($IdLocal,$TipoComprobante,$Serie){

  //echo $Serie;
  //Valida serie
  if(!extSerieComprobante($IdLocal,$Serie,$TipoComprobante))
    $Serie = cargarSerieDefComprobante($IdLocal,$TipoComprobante);
  
  $sql = "SELECT MAX( NPresupuesto ) AS NroComprobante".
    " FROM    ges_presupuestos ".
    " WHERE   IdLocal         = '$IdLocal' ".
    " AND     TipoPresupuesto = '$TipoComprobante' ".
    " AND     Serie = '$Serie' ".
    " AND     Eliminado       = '0'";
  $res=query($sql);
  $row= Row($res);

  if($row["NroComprobante"])
    $nro = $row["NroComprobante"]+1;
  else
    $nro = 1;
  //codigo documento
  $cod = $Serie."-".$nro;
  return $cod;
}
function getIdClienteInterno($IdClienteInterno){
  $sql = " SELECT IdCliente
           FROM  ges_clientes
           WHERE TipoCliente = 'Interno' 
           AND   IdLocal     = '".$IdClienteInterno."'
           AND   Eliminado   = '0'";
  $row = queryrow($sql);
  if ($row)
    return $row["IdCliente"];
  
  //Crea registro del local
  return CrearCliente(
		      getNombreLocalId($IdClienteInterno),
		      getNombreLocalId($IdClienteInterno),
		      '','', '', '', '','', '','','','','',
		      'Interno','','','',$IdClienteInterno);
}


function obtenerPartidaCaja($Partida,$TipoVenta){
    $Partida   = CleanText($Partida);
    $TipoVenta = CleanText($TipoVenta);
    $sql = "SELECT IdPartidaCaja FROM ges_partidascaja ".
           "WHERE PartidaCaja LIKE '$Partida' ".
           "AND TipoCaja = '$TipoVenta'";
    $row = queryrow($sql);
    return $row["IdPartidaCaja"];
}

function ObtenerDocumentoServicio($IdTbjoSub){
  $sql = "SELECT CONCAT(DocSubsidiario,'~',NDocSubsidiario) as DocServicio ".
         "FROM ges_dinero_movimientos ".
         "WHERE  IdTbjoSubsidiario      = '$IdTbjoSub' ".
         "ORDER BY IdOperacionCaja DESC ".
         "LIMIT 1";
  $row = queryrow($sql);
  return $row["DocServicio"];
}

function ModificarMovDocSubsidiario($xid,$campoxdato){
  $Tb         = 'ges_dinero_movimientos';
  $IdKey      = 'IdTbjoSubsidiario';
  $Id         = CleanID($xid);
  $KeysValue  = $campoxdato;
  $sql   =
    " update ".$Tb.
    " set    ".$KeysValue." ".
    " where  ".$IdKey." = ".$Id;	
  return query($sql); 
  
}

function obtenerSyncTPV(){
  $keysync = getSesionDato('KeySync');
  if(!$keysync) return '';
  $sql = " SELECT obtenerSyncTPV('$keysync') ";
  $row = queryrow($sql);
  return $row[0];
}

function setSyncTPV($xkey){
  $keysync = getSesionDato('KeySync');
  $sql = 
    " update ges_synctpv".
    " SET    $xkey = 0 ".
    " WHERE  KeySync   = '".$keysync."' ".
    " AND    Eliminado = '0'";
  query($sql);
}

function ModificarMovDocComprobante($xid,$campoxdato){
  $Id  = CleanID($xid);
  $sql = "SELECT CONCAT(SerieComprobante,'-',NComprobante) as Codigo  
          FROM ges_comprobantes 
          WHERE IdComprobante = $Id ";
  $row = queryrow($sql);
  $cod = $row["Codigo"];
  $campoxdato = "Concepto= '".$campoxdato." Cod ".$cod."'";

  $Tb         = 'ges_dinero_movimientos';
  $IdKey      = 'IdComprobante';
  $KeysValue  = $campoxdato;
  $sql   =
    " update ".$Tb.
    " set    ".$KeysValue." ".
    " where  ".$IdKey." = ".$Id;	
  query($sql); 
  
}

function ModificarFechaDineroMovimiento($IdComprobante,$fecha){
  $IdLocal  = getSesionDato("IdTienda");
  $arqueo   = new movimiento;
  $IdArqueo = $arqueo->GetArqueoActivo($IdLocal);

  $sql = "SELECT  IdOperacionCaja  
          FROM ges_dinero_movimientos 
          WHERE IdComprobante = $IdComprobante
          AND IdArqueoCaja = $IdArqueo ";

  if(nroRows($sql) != 1) return;

  $row = queryrow($sql);
  $IdOperacionCaja = $row["IdOperacionCaja"];

  ActualizarFechaDineroMovimiento($IdOperacionCaja,$fecha,$IdComprobante);
}

function ActualizarFechaDineroMovimiento($IdOperacionCaja,$fecha,$IdComprobante){
  $sql = "UPDATE ges_dinero_movimientos ".
         "SET FechaCaja = DATE('$fecha'), ".
         "FechaPago = '$fecha' ".
         "WHERE IdOperacionCaja = $IdOperacionCaja ".
         "AND IdComprobante = $IdComprobante ";
  query($sql);
}

function esCerradaCaja($IdArqueo){
  $sql = "SELECT  esCerrada, FechaApertura  
          FROM ges_arqueo_caja 
          WHERE IdArqueo = $IdArqueo ";
  
  $row = queryrow($sql);  

  return $row["esCerrada"]."~".$row["FechaApertura"];
}

function ValidarFechaAperturaCaja($Fecha){
  $IdLocal  = getSesionDato("IdTienda");
  $arqueo   = new movimiento;
  $IdArqueo = $arqueo->GetArqueoActivo($IdLocal);
  $xcaja    = explode("~",esCerradaCaja($IdArqueo));

  $datecaja = new DateTime($xcaja[1]);
  $datenew  = new DateTime($Fecha);

  if($datenew < $datecaja)
    return "~".$Fecha."~".$xcaja[1];
  else
    return "1";
}

function GuardarDocumentosCobros($idopcaja,$doccobro,$IdLocal,$IdUsuario,
				 $IdClienteCredito=0,$FechaOperacion=false){

  $FechaOperacion = ($FechaOperacion)? $FechaOperacion:date("Y-m-d H:i:s");

  $values = "IdLocal, IdUsuario, IdOperacionCaja, IdCuentaBancaria,
             CodOperacion, NumDocumento, Observaciones, IdClienteCredito,FechaOperacion";
  $keys   = "'$IdLocal','$IdUsuario','$idopcaja','".$doccobro["id"]."',
             '".$doccobro["op"]."','".$doccobro["doc"]."','".$doccobro["obs"]."',
             '".$IdClienteCredito."','".$FechaOperacion."'";
    
  $sql = "INSERT INTO ges_cobrosclientedoc ( $values ) VALUES ( $keys )";
  $res = query($sql);

}

function ckCodigoAutorizacionTPV($xid,$xaccion,$xcod=false){

  switch($xaccion) {
  case "ck":    $xcodauto = getCodAutorizacion($xid); break;
  case "ckcliente":    $xcodauto = getCodAutorizacionCliente($xid); break;
  case "val":   $xcodauto = valCodAutorizacion($xid,$xcod); break;
  case "valcliente":   $xcodauto = valCodAutorizacionCliente($xid,$xcod); break;
  case "resetck":	$xcodauto = getNewCodAutorizacion($xid); break;
  case "resetckcliente":    $xcodauto = getNewCodAutorizacionCliente($xid); break;
  }
  return "0~".$xcodauto;
}

function valCodAutorizacion($xid,$xcod){ 
  $xcod = $xcod.'~0';
  return ( getCodAutorizacion($xid) == $xcod ); 
}

function valCodAutorizacionCliente($xid,$xcod){ 
  $xcod = $xcod.'~0';
  return ( getCodAutorizacionCliente($xid) == $xcod ); 
}

function getCodAutorizacion($xid){
    $sql = " select CodigoValidacion ".
           " from   ges_comprobantes ".
           " where  IdComprobante = '".$xid."'"; 
    $row = queryrow($sql);     

    if (!$row)
      return false;

    if ( $row["CodigoValidacion"] == '')
      return getNewCodAutorizacion($xid);

    return $row["CodigoValidacion"].'~0';
}

function getCodAutorizacionCliente($xid){
    $sql = " select CodigoValidacion ".
           " from   ges_clientes ".
           " where  IdCliente = '".$xid."'"; 
    $row = queryrow($sql);     

    if (!$row)
      return false;

    if ( $row["CodigoValidacion"] == '')
      return getNewCodAutorizacionCliente($xid);

    return $row["CodigoValidacion"].'~0';
}

function setCodAutorizacionComprobante($xid,$xcodauto){
  $sql      = " update ges_comprobantes ".
              " set    CodigoValidacion = '$xcodauto'".
              " where  IdComprobante    = ".$xid;
  return query($sql);  
}

function setCodAutorizacionCliente($xid,$xcodauto){
  $sql      = " update ges_clientes ".
              " set    CodigoValidacion = '$xcodauto'".
              " where  IdCliente    = ".$xid;
  return query($sql);  
}

function setearCodAutorizacionComprobante(){
  $IdLocal    = CleanID(getSesionDato("IdTienda"));
  $TipoVenta  = getSesionDato("TipoVentaTPV");
  $sql        = " update ges_comprobantes ".
                " set    CodigoValidacion = ''".
                " where  IdLocal    = ".$IdLocal.
                " and    TipoVentaOperacion = '".$TipoVenta."'";
  return query($sql);  
}

function getNewCodAutorizacion($xid){
  
  $xcodauto = strtoupper( generarclaveAlfaNumeria(4) );

  if( setCodAutorizacionComprobante($xid,$xcodauto) ) 
    return $xcodauto;

}

function getNewCodAutorizacionCliente($xid){
  
  $xcodauto = strtoupper( generarclaveAlfaNumeria(4) );

  if( setCodAutorizacionCliente($xid,$xcodauto) ) 
    return $xcodauto;

}

function actualizarUsuarioComprobante($IdComprobante,$IdUsuario){
  $sql = "update ges_comprobantes SET IdUsuario = '$IdUsuario' 
          WHERE IdComprobante = '$IdComprobante'";
  query($sql);
}

function obtenerMontoDevolucion($IdComprobante,$IdLocal,$IdModalidad){
  $sql = "SELECT SUM(Importe) as Importe ".
         "FROM ges_dinero_movimientos ".
         "WHERE IdComprobante = '$IdComprobante' ".
         "AND IdLocal = '$IdLocal' ".
         "AND IdModalidadPago = '$IdModalidad' ";

  $row = queryrow($sql);
  
  if($row["Importe"] == '') return 0;
  return ($row["Importe"] <= 0)? 0:$row["Importe"];
}

function obtenerEncabezadoComprobanteVenta($IdComprobante){
	 $sql= 
	   " select IdCliente,ImporteNeto,ImporteImpuesto,".
	   "        TotalImporte,Impuesto ".
	   " from   ges_comprobantes ".
	   " where  IdComprobante  = '".$IdComprobante."' ".
	   " and    Eliminado      = 0 ";
	 $res = query($sql);
	 $row = Row($res);
	 return $row;
}

function verificarCodigoOperacion($codop,$idcuenta){
  $sql = "SELECT CodOperacion FROM ges_cobrosclientedoc ".
         "WHERE CodOperacion = '$codop' ".
         "AND IdCuentaBancaria = '$idcuenta' ".
         "AND Eliminado = 0 ".
         "LIMIT 1";

  $row = queryrow($sql);
  if(!$row || $row["CodOperacion"] == '000000')
    return false;
  return $row["CodOperacion"];
}

function getIdComprobanteAlbaran($IdComprobante,$IdLocal){
  //$IdLocal = getSesionDato("IdTienda"); 
  //$IdLocal   = getSesionDato("IdTiendaDependiente");
    $sql = "SELECT GROUP_CONCAT(DISTINCT(IdAlbaran)) as Albaranes ".
           " FROM  ges_comprobantesdet ".
           " WHERE IdComprobante = $IdComprobante ";

  $row = queryrow($sql);
   if (!$row) 
     return false; 
   return $row["Albaranes"];
}

?>
