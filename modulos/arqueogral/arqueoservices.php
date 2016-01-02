<?php

include("../../tool.php");
require_once "../../class/json.class.php";

SimpleAutentificacionAutomatica("novisual-services");

$json = new Services_JSON();
$modo = $_REQUEST["modo"];
$Moneda = getSesionDato("Moneda"); 

switch($modo){
	
	case "getDatosActualizadosArqueo":
		$IdArqueo = intval($_REQUEST["IdArqueo"]);
		$IdLocal = intval($_REQUEST["IdLocal"]);	
		ActualizarArqueoDeLocal($IdArqueo,$IdLocal);
		$data = getDatosArqueo($IdArqueo);
		$output = $json->encode($data);
		echo $output;
		exit();					
		break;
	
	case "getListaUltimosDiez":
	        //error(__FILE__,"Info: modo: $modo");
	        $anio        = CleanText($_REQUEST["anio"]);
		$mes         = CleanText($_REQUEST["mes"]);
		$IdLocal     = intval($_REQUEST["IdLocal"]);
		$moneda      = CleanID($_REQUEST["mda"]);

		$ultimosDiez = getUltimasDiezAsArray($IdLocal,$anio,$mes,$moneda);
		$output = $json->encode($ultimosDiez);
		echo $output;
		exit();
		break;

	case "getMovimientos":
		$IdArqueo = intval($_REQUEST["IdArqueo"]);
		$data = getMovimientosArqueo($IdArqueo);
		$output = $json->encode($data);
		echo $output;
		exit();					
		break;		
		
	case "arquearYAbrirNuevaCaja":			
		$IdLocal  = intval($_REQUEST["IdLocal"]);
		$IdMoneda = CleanID($_REQUEST["xidm"]);
		$row = CalcularUltimoArqueo($IdLocal,$IdMoneda);
		ActualizarArqueo($row["IdArqueo"], $row);
		MarcarArqueoCerrado($row["IdArqueo"],$IdMoneda);
		$row["ImporteCierre"] = getImporteCierre($row["IdArqueo"]);
	//	echo InsertarNuevaCaja($row,$IdLocal);
		exit();			
		break;	

	case "soloAbrirCaja":
		$IdLocal  = intval($_REQUEST["xidl"]);
		$IdMoneda = CleanID($_REQUEST["xidm"]);

		if(!$IdLocal || !$IdMoneda) {
		  echo "~~Moneda o local no seleccionada, actualize su navegador~";
		  return;
		}
		
		$aArqueo  = CalcularUltimoArqueo2($IdLocal,$IdMoneda);

		$existeId = verificarAperturaCajaGral($IdLocal,$IdMoneda);

		if($existeId >= 1){
		  echo "~~~".$existeId;
		  return;
		}

		//if(!verificarArqueoCajaGral($IdLocal)) 
		  // echo "~".InicializaCajaGral($IdLocal,$aArqueo)."~~";
		//else
		echo "~".InsertarNuevaCaja($aArqueo,$IdLocal,$IdMoneda)."~~";

		exit();
		break;

	case "actualizarCantidadCaja":
		$IdLocal = intval($_REQUEST["IdLocal"]);
		$cantidad = CleanFloat($_REQUEST["cantidad"]);
		$IdMoneda = CleanID($_REQUEST["xidm"]);
		actualizarCantidadCaja($IdLocal,$cantidad,$IdMoneda);	
		exit();
		break;
        case "hacerOperacionDinero":
	        $IdLocal      = intval($_REQUEST["xidl"]);
		$cantidad     = CleanFloat($_REQUEST["cantidad"]);	
		$concepto     = $_REQUEST["concepto"];
		$IdMoneda     = CleanID($_REQUEST["xidm"]);
		$cambiomoneda = CleanFloat($_REQUEST["cm"]);
		$fechacaja    = CleanCadena($_REQUEST["fcaja"]);
		$operacion    = CleanText($_REQUEST["op"]);
		$xIdArqueo    = CleanID($_REQUEST["xidacg"]);
		$documento    = CleanText($_REQUEST["doc"]);
		$codigodoc    = CleanText($_REQUEST["coddoc"]);
		$proveedor    = CleanText($_REQUEST["prov"]);
 		$IdUsuario    = CleanID(getSesionDato("IdUsuario"));
		$Partida      = CleanText($_REQUEST["partida"]);
		$CodPartida   = CleanCadena($_REQUEST["codpartida"]);
		$IdCuenta     = CleanID($_REQUEST["idcuenta"]);

		$IdPartida    = obtenerIdPartidaCaja($CodPartida);

		// Control de arqueo
		$mov          = new movimientogral;
		$IdArqueo     = $mov->getIdArqueoEsCerrado($IdMoneda,$IdLocal);
		if($IdArqueo != $xIdArqueo){ 
		  echo "~"."01"; // Arqueo Diferente
		  return false;
		}

		// Control transferencia a almacen central
		$LocalActual  = getNombreComercialLocal($IdLocal);
		$oLocal = new local;
		$LocalDestino = $oLocal->getIdLocalCentral();
		$IdArqueoDest = $mov->getIdArqueoEsCerrado($IdMoneda,$LocalDestino);
		if(!$IdArqueoDest) {
		  echo "~"."02"; // Caja destino cerrada
		  return false;
		}
		if($CodPartida == 'S124'){
		  if($IdLocal == $LocalDestino){
		    echo "~"."03"; // Mismo local
		    return false;
		  }
		}

		//control cambio moneda
		if($CodPartida == 'S125'){
		  $IdMonedaCambio = ($IdMoneda == 1)? 2:1;
		  $IdArqueoM  = $mov->getIdArqueoEsCerrado($IdMonedaCambio,$IdLocal);
		  if(!$IdArqueoM){ 
		    echo "~"."04"; // Caja de moneda destino está cerrada;
		    return false;
		  }
		  $xcambiomoneda = $cambiomoneda;
		}
		$cambiomoneda = ($IdMoneda == 1)? 1:$cambiomoneda;
		  
		$idopcaja = EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,
						  $IdMoneda,$cambiomoneda,$operacion,
						  $fechacaja,$IdUsuario,$IdArqueo,$documento,
						  $codigodoc,$proveedor);

		if($IdCuenta){
		  $IdOperacionCaja     = 0;
		  $IdOperacionCajaGral = $idopcaja;
		  $TipoMovimiento      = ($operacion == 'Ingreso')? 'Salida':'Entrada';

		  RegistrarMovimientoBancario($IdLocal,$IdOperacionCaja,$IdOperacionCajaGral,
					    $IdUsuario,$IdCuenta,$TipoMovimiento,$concepto,
					    $cantidad);
		}

		if($CodPartida == 'S124'){
		  $operacion = 'Ingreso';
		  $concepto  = 'Transferecnia desde '.$LocalActual;
		  $IdLocal   = $LocalDestino;
		  $IdArqueo  = $IdArqueoDest;
		  
		  EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
					$cambiomoneda,$operacion,$fechacaja,$IdUsuario,
					$IdArqueo,$documento,$codigodoc,$proveedor);
		}

		if($CodPartida == "S125"){
		  $cambiomoneda = $xcambiomoneda;
		  if($IdMoneda == 1){
		    $cantidad =  round(($cantidad/$cambiomoneda),2);
		  }else{
		    $cantidad =  round(($cantidad*$cambiomoneda),2);
		  }

		  $IdMoneda  = $IdMonedaCambio;
		  $operacion = 'Ingreso';
		  $concepto  = 'Cambio moneda a '.$Moneda[$IdMoneda]['T'];
		  $IdArqueo  = $IdArqueoM;
		  $cambiomoneda = ($IdMoneda == 1)? 1:$xcambiomoneda;

		  EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
					$cambiomoneda,$operacion,$fechacaja,$IdUsuario,
					$IdArqueo,$documento,$codigodoc,$proveedor);  
		}

		echo "~"."00";
		return true;
		break;

	case "modificaOperacionCajaGral":				
		$IdLocal  = CleanID($_GET["xidl"]);
		$IdOperacionCaja = CleanID($_GET["xidoc"]);
		$concepto    = CleanText($_GET["concepto"]);	
		$codigodoc   = CleanText($_GET["codigodoc"]);
		$subsidiario = CleanID($_GET["proveedor"]);
		$documento   = CleanText($_GET["doc"]);

		echo "~".ModificarOperacionCajaGral($IdLocal,$IdOperacionCaja,$concepto,
						    $codigodoc,$subsidiario,$documento);
		exit();	
		break;

        case "obtenerAnios":
                $dato = obtenerAnios();
		echo $dato;
		exit();
		break;					

        case "obtenerDatosCuentaBancaria":
	        $idcta   = CleanID($_GET["xidc"]);
                $ingreso = obtenerDatosCuentaBancaria($idcta,'Ingreso');
                $salida  = obtenerDatosCuentaBancaria($idcta,'Salida');
		$saldo   = $ingreso-$salida;
		
		$dato    = $ingreso."~".$salida."~".$saldo;
		echo $dato;
		exit();
		break;

        case "obtenerCuentasBancarias":
	        $IdMoneda = CleanID($_GET["xid"]);
		$dato     = obtenerCuentasBancarias($IdMoneda);

		foreach ($dato as $key=>$value) {
		  echo "$value=$key\n";
		}		

		exit();
		break;

        case "registrarTransferenciaBancaria":
	        $CuentaOrigen  = CleanID($_GET["ctaorig"]);
		$CuentaDestino = CleanID($_GET["ctadest"]);
		$Concepto      = CleanText($_GET["concepto"]);
		$Importe       = CleanFloat($_GET["importe"]);
		$IdLocal       = getSesionDato("IdTienda");
		$IdUsuario     = getSesionDato("IdUsuario");

		$Saldo = obtenerUltimoSaldo($CuentaOrigen);

		if($Importe > $Saldo ){
		  echo "Error";
		  return;
		}

		$regOrigen  = RegistrarMovimientoBancario($IdLocal,0,0,$IdUsuario,
							 $CuentaOrigen,'Salida',$Concepto,
							 $Importe);

		$regDestino = RegistrarMovimientoBancario($IdLocal,0,0,$IdUsuario,
							  $CuentaDestino,'Ingreso',$Concepto,
							  $Importe);

		exit();
		break;

        case "obtenerUltimaFechaCajaGral":
		$IdLocal   = getSesionDato("IdTiendaDependiente");
		$IdMoneda  = CleanID($_GET["idmon"]);
                $dato      = obtenerUltimaFechaCajaGral($IdLocal,$IdMoneda);
		echo $dato;
		exit();
		break;

	default:
		break;	
}


function getImporteCierre($IdArqueo){
        $TipoVenta = getSesionDato("TipoVentaTPV");

	$sql = "SELECT ImporteCierre FROM ges_arqueo_cajagral WHERE IdArqueoCajaGral='$IdArqueo' ";	
	$row = queryrow($sql);
	
	return $row["ImporteCierre"];	
}


function getUltimasDiezAsArray($IdLocal,$anio,$mes,$moneda){
        $TipoVenta = getSesionDato("TipoVentaTPV");
	$IdLocal   = getSesionDato("IdTienda");
	$inicio    = "$anio-$mes-01";
	$fin       = "$anio-$mes-31";
	$movgral   = new movimientogral;
	$fechacaja = $movgral->getAperturaCajaGral(1,$IdLocal);
	$afecha    = explode(" ",$fechacaja);
	$afecha    = explode("-",$afecha[0]);
	$datenow   = date('Y-m');
	$fcaja     = ($fechacaja)? $afecha[0]."-".$afecha[1]:$datenow;

	if($anio."-".$mes == $datenow ){
	  $finicio = " AND (DATE(FechaApertura) >= '$inicio' OR DATE(FechaCierre) = '0000-00-00') ";
	  $ffin = "";
	}else{
	  $finicio = " AND DATE(FechaApertura) >= '$inicio' ";
	  $ffin = " AND DATE(FechaApertura) <= '$fin' ";
	}

	$datos     = array();

	$sql = "SELECT IdArqueoCajaGral, IdLocal, IdUsuario, ".
               "ges_arqueo_cajagral.IdMoneda, FechaApertura, FechaCierre, ".
               "esCerrada, ImporteApertura, ImporteIngresos, ImporteCompras,ImporteGastos, ".
               "ImporteAportaciones, ImporteSustracciones, ImporteTeoricoCierre, ".
               "ImporteCierre, ImporteDescuadre, Simbolo, Moneda ".
               "FROM ges_arqueo_cajagral ".
               "INNER JOIN ges_moneda ON ges_arqueo_cajagral.IdMoneda = ges_moneda.IdMoneda ".
               "WHERE IdLocal='$IdLocal' ".
	       "AND ges_arqueo_cajagral.Eliminado = 0 ".
	       "AND ges_arqueo_cajagral.IdMoneda = $moneda ".
	       "$finicio $ffin ".
	       "ORDER BY FechaApertura DESC, ".
               "IdArqueoCajaGral DESC LIMIT 31";
	$res = query($sql,'Ultimas diez..');
	if (!$res) return $datos;
	
	$n = 0;
	while ($row = Row($res)){
		$datos["arqueo_$n"] = $row; 		
		$n++;
	}
	return $datos;
}

function getDatosArqueo($IdArqueo){	
	$IdArqueo = intval($IdArqueo);
	$sql = "SELECT IdArqueoCajaGral, IdLocal, IdUsuario, ".
               "ges_arqueo_cajagral.IdMoneda, FechaApertura, FechaCierre, ".
               "esCerrada, ImporteApertura, ImporteIngresos, ImporteCompras,ImporteGastos, ".
               "ImporteAportaciones, ImporteSustracciones, ImporteTeoricoCierre, ".
               "ImporteCierre, ImporteDescuadre, Simbolo, Moneda ".
               "FROM ges_arqueo_cajagral ".
               "INNER JOIN ges_moneda ON ges_arqueo_cajagral.IdMoneda = ges_moneda.IdMoneda ".
               "WHERE IdArqueoCajaGral='$IdArqueo' ";

	return queryrow($sql);	
}


function ActualizarArqueoDeLocal($IdArqueo,$IdLocal){
    $row = CalcularUltimoArqueo($IdLocal,false,$IdArqueo);
    ActualizarArqueo($IdArqueo, $row);
    return $row;						
}

function ActualizarArqueo($IdArqueo, $Datos){
	
	$IdArqueo = intval($IdArqueo);

	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto");
	
	$sql = "UPDATE ges_arqueo_cajagral SET ".
		" ImporteTeoricoCierre= '". $Datos["TeoricoFinal"] ."', ".
		" ImporteIngresos= '". $Datos["Ingreso"] ."', ".
		" ImporteSustracciones= '". $Datos["Sustraccion"] ."', ".
		" ImporteAportaciones= '". $Datos["Aportacion"] ."', ".		
		" ImporteGastos= '". $Datos["Gasto"] ."', " .			
		" ImporteCompras= '". $Datos["Egreso"] ."' " .			
		" WHERE IdArqueoCajaGral='$IdArqueo' ";		 	
	query($sql,'Actualizar importes');
	
	$sql = "UPDATE ges_arqueo_cajagral SET ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones-ImporteCompras ".
	" WHERE IdArqueoCajaGral='$IdArqueo'";
		
	query($sql,'Actualizando teorico-cierre');	 		

	$sql = "UPDATE ges_arqueo_cajagral SET ".		
		" ImporteDescuadre = ImporteTeoricoCierre - ImporteCierre ".		
		" WHERE IdArqueoCajaGral='$IdArqueo' ";			
	query($sql,'Actualizar descuadre');		
}


function MarcarArqueoCerrado($IdArqueo,$IdMoneda){
		$IdArqueo = intval($IdArqueo);

		//Cierra caja 
		$sql = "UPDATE ges_arqueo_cajagral SET ".
		" FechaCierre= NOW(), ".
		" esCerrada = 1 ".
		" WHERE IdArqueoCajaGral='$IdArqueo' ";
		query($sql,"Marcando arqueo como cerrado");		

		//Cierra caja modo forzado

		$IdLocal  = getSesionDato("IdTiendaDependiente");

		$sql = "UPDATE ges_arqueo_cajagral SET ".
		  " FechaCierre = NOW(), ".
		  " esCerrada   = 1 ".
		  " WHERE esCerrada  = 0 ".
		  " AND   IdMoneda = '$IdMoneda'".
		  " AND   IdLocal = '$IdLocal'";
		query($sql,"Marcando arqueo como cerrado [modo forzado]");		
		
						
}


function CalcularUltimoArqueo($IdLocal,$IdMoneda=false,$IdArqueo=false){

	$datos     = array();
	$IdLocal   = CleanID($IdLocal);

	if (!$IdArqueo){
		$sql = "SELECT IdArqueoCajaGral ".
		  "FROM ges_arqueo_cajagral ".
		  "WHERE IdLocal   = '$IdLocal' ".
		  "AND   Eliminado = 0 ".
		  "AND   esCerrada = 0 ".
		  "AND   IdMoneda = '$IdMoneda' ".
		  "ORDER BY FechaCierre DESC";
		$row = queryrow($sql,'Buscando arqueo abierto');
		$IdArqueo = $row["IdArqueoCajaGral"];	
		if (!$IdArqueo)
		  return false;	
	}	
		
	$datos["IdArqueo"] = $IdArqueo;		
	
	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto","Egreso");

	foreach($modos as $tipo){	
	  $sql = "SELECT sum( Importe ) AS SumaImporte".
	    " FROM  ges_librodiario_cajagral ".
	    " WHERE Eliminado = 0 ".
	    " AND   IdLocal 	= '$IdLocal' ".
	    " AND   IdArqueoCajaGral 	= '$IdArqueo' ".
	    " AND   TipoOperacion = '$tipo' ";

	  //NOTA: investigar si modalidad de pago 1 es correcto o hay que contemplar otros tipos
	  $row = queryrow($sql);
	  $datos[$tipo] = $row["SumaImporte"];
	}	
	
	$datos["TeoricoFinal"]= $datos["Ingreso"]+$datos["Aportacion"]-$datos["Gasto"]-$datos["Sustraccion"]-$datos["Egreso"];
	
	//error(__FILE__ . __LINE__ , "If: final:". $datos["TeoricoFinal"]);
	
	return $datos;				
}


function CalcularUltimoArqueo2($IdLocal,$IdMoneda,$IdArqueo=false){

	$datos = array();
	$IdLocal = CleanID($IdLocal);

	if (!$IdArqueo){
	  $sql = "SELECT IdArqueoCajaGral, ImporteCierre ".
	    "FROM  ges_arqueo_cajagral ".
	    "WHERE IdLocal   ='$IdLocal' ".
	    "AND   Eliminado = 0 ".
	    "AND   esCerrada = 1 ".
	    "AND   Idmoneda = '$IdMoneda' ".
	    "ORDER BY FechaCierre DESC";
	  $row = queryrow($sql,'Buscando arqueo abierto');
	  $IdArqueo = $row["IdArqueoCajaGral"];	
	  $ImporteCierre = $row["ImporteCierre"];

	  if (!$IdArqueo)
		  return false;	
	}	

	$datos["IdArqueoCajaGral"] = $IdArqueo;		
	$datos["ImporteCierre"] = $ImporteCierre;

	return $datos;				
}



function getMovimientosArqueo($IdArqueo){
	
	$IdArqueo = intval($IdArqueo);
	$datos = array();
	$sql = "SELECT IdOperacionCaja, Identificacion, PartidaCaja, Moneda, Simbolo, ".
	       "ges_librodiario_cajagral.CambioMoneda, FechaCaja, ".
               "FechaInsercion, ges_librodiario_cajagral.TipoOperacion, Concepto, ".
	       "IF(ges_librodiario_cajagral.TipoOperacion = 'Gasto',Documento,'') as Documento, ".
	       "IF(ges_librodiario_cajagral.TipoOperacion = 'Gasto',CodigoDocumento,'') as CodigoDocumento, ".
	       "IF(ges_librodiario_cajagral.IdSubsidiario <> 0,(SELECT NombreComercial FROM ges_subsidiarios WHERE ges_subsidiarios.IdSubsidiario = ges_librodiario_cajagral.IdSubsidiario),'') as Proveedor, Importe, IdPagoProvDoc, ".
               "ges_partidascaja.Codigo, ".
               "ges_librodiario_cajagral.IdSubsidiario ".
	       "FROM ges_librodiario_cajagral ".
	       "INNER JOIN ges_usuarios ON ".
               " ges_librodiario_cajagral.IdUsuario = ges_usuarios.IdUsuario ".
	       "INNER JOIN ges_partidascaja ON ".
               " ges_librodiario_cajagral.IdPartidaCaja = ges_partidascaja.IdPartidaCaja ".
	       "INNER JOIN ges_moneda ON ".
               " ges_librodiario_cajagral.IdMoneda = ges_moneda.IdMoneda ".
	       "WHERE ges_librodiario_cajagral.IdArqueoCajaGral = '$IdArqueo' ".
	       "AND ges_librodiario_cajagral.Eliminado = 0 ".
	       "ORDER BY ges_librodiario_cajagral.FechaInsercion DESC ";

	$res = query($sql);
	if (!$res) return $datos;
	
	$n = 0;
	while( $row = Row($res)){
	        $row["Proveedor2"] = ($row["IdPagoProvDoc"] != 0)? obtenerMovimientoGralProv($row["IdPagoProvDoc"]):"";
		$row["Proveedor2"] = str_replace('&#038;','&',$row["Proveedor2"]);
		$row["Proveedor"] = str_replace('&#038;','&',$row["Proveedor"]);
		$DocProv = ($row["IdPagoProvDoc"] != 0)? obtenerDocGralProv($row["IdPagoProvDoc"]):"";
		$aDocProv = ($DocProv)? explode("~",$DocProv):"";
		$row["Documento"] = ($DocProv)? $aDocProv[0]:$row["Documento"];
		$row["CodigoDocumento"] = ($DocProv)? $aDocProv[1]:$row["CodigoDocumento"];

		$datos["mov_$n"] = $row;
		$n++;		
	} 			
	return $datos;
}

function InsertarNuevaCaja($datosArqueo,$IdLocal,$IdMoneda){
	global $UltimaInsercion;	
	$IdLocal  = CleanID($IdLocal);
	$IdMoneda = CleanID($IdMoneda);

	$IdUsuario= CleanID(getSesionDato("IdUsuario"));

	$ImporteApertura = ($datosArqueo)? $datosArqueo["ImporteCierre"]:0;
	$ImporteTeoricoCierre = $ImporteApertura;

	$listkey = "IdLocal,IdUsuario,IdMoneda, FechaApertura, ImporteApertura, 
                    ImporteTeoricoCierre";

	$keyvalues = "'$IdLocal','$IdUsuario','$IdMoneda',NOW(),'$ImporteApertura',
                      '$ImporteTeoricoCierre'";

	$sql = "INSERT INTO ges_arqueo_cajagral ($listkey) values ($keyvalues)";
	
	$res = query($sql,'Insertando nueva caja');
	
	if ($res)return $UltimaInsercion;
	return 0;	
}

function actualizarCantidadCaja($IdLocal,$cantidad,$IdMoneda){
	$IdLocal  = CleanID($IdLocal);
	$IdMoneda = CleanID($IdMoneda);
	$cantidad = CleanFloat($cantidad);
	$cantidad = CleanRealMysql($cantidad);

	$sql = "UPDATE ges_arqueo_cajagral SET ImporteCierre = '$cantidad' WHERE IdLocal='$IdLocal' AND esCerrada=0 AND  IdMoneda = '$IdMoneda' ";
	query($sql,'Actualizando cantidad de cierre');
	
	$sql = "UPDATE ges_arqueo_cajagral ".
	  "SET   ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones-ImporteCompras ".
	  "WHERE IdLocal   = '$IdLocal'".
	  "AND   esCerrada = 0 ".
	  "AND   IdMoneda = '$IdMoneda' ";
	query($sql,'Actualizando teorico');	 		
	
	$sql = "UPDATE ges_arqueo_cajagral ".
	  "SET   ImporteDescuadre = ImporteCierre - ImporteTeoricoCierre ".
	  "WHERE IdLocal = '$IdLocal' ".
	  "AND   IdMoneda = '$IdMoneda' ";
	query($sql,'Actualizando descuadre');
}

function verificarArqueoCajaGral($IdLocal){
  $sql = "SELECT COUNT(IdArqueoCajaGral) as Id ".
         "FROM   ges_arqueo_cajagral ".
         "WHERE  IdLocal = '$IdLocal'";
  $row = queryrow($sql);
  return $row["Id"];
}

function InicializaCajaGral($IdLocal,$datosArqueo){
  $sql = "SELECT IdMoneda ".
         "FROM   ges_moneda ".
         "WHERE  Eliminado = 0";
 
  $res = query($sql);
  
  while( $row = Row($res)){
    InsertarNuevaCaja($datosArqueo,$IdLocal,$row["IdMoneda"]);
  } 			
 
}

function ModificarOperacionCajaGral($IdLocal,$IdOperacionCaja,$concepto,
				    $codigodoc,$subsidiario,$documento){
  $sql = " UPDATE ges_librodiario_cajagral SET Concepto = '$concepto', ".
         " IdSubsidiario = '$subsidiario', ".
         " Documento = '$documento', ".
         " CodigoDocumento = '$codigodoc' ".
         " WHERE IdOperacionCaja='$IdOperacionCaja' ".
         " AND IdLocal = '$IdLocal' ";
  
  $res = query($sql,'Actualizando Concepto');
  if($res)
    return 1;
  else
    return 'false';
}

function obtenerAnios(){
  $sql = "SELECT DISTINCT(YEAR(FechaApertura)) as Anios ".
         "FROM ges_arqueo_cajagral ".
         "WHERE Eliminado = 0 ".
         "ORDER BY ges_arqueo_cajagral.FechaApertura DESC";

  $res = query($sql);
  $anios = '';
  $t = '';
  while($row = Row($res)){
    $anios .= $t.$row["Anios"];
    $t = ',';
  }

  return $anios;
}

function verificarAperturaCajaGral($IdLocal,$IdMoneda){
  $sql = "SELECT COUNT(IdArqueoCajaGral) as Id ".
         "FROM   ges_arqueo_cajagral ".
         "WHERE  IdLocal = '$IdLocal'".
         "AND IdMoneda = $IdMoneda ".
         "AND Eliminado = 0 ".
         "AND esCerrada = 0 ";
  $row = queryrow($sql);
  return $row["Id"];

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

function obtenerDatosCuentaBancaria($idcta,$TipoMovimiento){
  $sql = "SELECT SUM(Importe) as Importe ".
         "FROM   ges_movimiento_bancario ".
         "WHERE  Eliminado = 0 ".
         "AND IdCuentaBancaria = $idcta ".
         "AND TipoMovimiento = '$TipoMovimiento' ".
         "GROUP BY IdCuentaBancaria ";

  $row = queryrow($sql);
  return $row["Importe"];
}

function obtenerCuentasBancarias($IdMoneda){
  $xmda = ($IdMoneda)? " AND ges_moneda.IdMoneda = '$IdMoneda' ":'';

  $sql = "SELECT IdCuentaBancaria,CONCAT(Simbolo,' ',EntidadFinanciera,' ',NumeroCuenta) as Cuenta FROM ges_cuentasbancarias INNER JOIN ges_moneda ON ges_cuentasbancarias.IdMoneda = ges_moneda.IdMoneda  WHERE ges_cuentasbancarias.Eliminado=0 AND IdProveedorProv = 0 ".$xmda;

  $res = query($sql);
  if (!$res) return false;

  $out        = array();	
  $preprocess = array();
  
  while($row=Row($res)){
    $key = $row["IdCuentaBancaria"];
    $value = NormalizaTalla($row["Cuenta"]);
    
    //$out[$key]=$value;
    $preprocess[$value] = $key;
  }
  
  foreach($preprocess as $key=>$value){
    $out[$value] = $key;			
  }	

  return $out;		
}

function obtenerUltimoSaldo($CuentaOrigen){
  $ingreso = obtenerDatosCuentaBancaria($CuentaOrigen,'Ingreso');
  $salida  = obtenerDatosCuentaBancaria($CuentaOrigen,'Salida');

  $saldo = $ingreso - $salida;
  return $saldo;
}

function obtenerIdPartidaCaja($CodPartida){
  $sql = "SELECT IdPartidaCaja as Id ".
         "FROM   ges_partidascaja ".
         "WHERE  Codigo = '$CodPartida'";
  $row = queryrow($sql);
  return $row["Id"];
}

function obtenerUltimaFechaCajaGral($IdLocal,$IdMoneda){
  $sql = "SELECT FechaApertura as Fecha ".
         "FROM   ges_arqueo_cajagral ".
         "WHERE  IdLocal = '$IdLocal' ".
         "AND IdMoneda = '$IdMoneda' ".
         "AND Eliminado = 0 ".
         "ORDER BY FechaApertura DESC ".
         "LIMIT 1 ";
  $row = queryrow($sql);
  return $row["Fecha"];
}


?>
