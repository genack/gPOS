<?php

include("../../tool.php");
require_once "../../class/json.class.php";

SimpleAutentificacionAutomatica("novisual-services");

$json = new Services_JSON();
$modo = $_REQUEST["modo"];


switch($modo){
	
	case "getDatosActualizadosArqueo":
		$IdArqueo = intval($_REQUEST["IdArqueo"]);
		$IdLocal = intval($_REQUEST["IdLocal"]);	
		ActualizarArqueoDeLocal($IdArqueo,$IdLocal);
		$data = getDatosArqueo($IdArqueo);
		$output = $json->encode($data);
		$xsync  = setSyncTPV('Caja');
		echo $output;
		exit();					
		break;
	
	case "getListaUltimosDiez":
	  //error(__FILE__,"Info: modo: $modo");
		
		$IdLocal = intval($_REQUEST["IdLocal"]);
	        $anio        = CleanText($_REQUEST["anio"]);
		$mes         = CleanText($_REQUEST["mes"]);
		$ultimosDiez = getUltimasDiezAsArray($IdLocal,$anio,$mes);	
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
		$IdLocal = intval($_REQUEST["IdLocal"]);
		$row = CalcularUltimoArqueo($IdLocal);
		ActualizarArqueo($row["IdArqueo"], $row);
		MarcarArqueoCerrado($row["IdArqueo"]);
		$row["ImporteCierre"] = getImporteCierre($row["IdArqueo"]);
	       // echo InsertarNuevaCaja($row,$IdLocal);
		exit();			
		break;	

	case "soloAbrirCaja":
		$IdLocal = intval($_REQUEST["IdLocal"]);
		$row = CalcularUltimoArqueo2($IdLocal);

		$mov     = new movimiento;
		$esCajaAbierta = $mov->GetArqueoActivo($IdLocal);

		if($esCajaAbierta != 0) {
		  echo "-1";
		  return ;
		}

		echo InsertarNuevaCaja($row,$IdLocal);
		exit();
		break;

	case "actualizarCantidadCaja":
		$IdLocal = intval($_REQUEST["IdLocal"]);
		$cantidad = CleanFloat($_REQUEST["cantidad"]);
		actualizarCantidadCaja($IdLocal,$cantidad);	
		exit();
		break;

        case "hacerOperacionDinero":

	        $IdLocal      = intval($_REQUEST["xidl"]);
		$cantidad     = CleanFloat($_REQUEST["cantidad"]);	
		$concepto     = $_REQUEST["concepto"];
		$fechacaja    = CleanCadena($_REQUEST["fcaja"]);
		$operacion    = CleanText($_REQUEST["op"]);
		$xIdArqueo    = CleanID($_REQUEST["xidacg"]);
		$Partida      = CleanText($_REQUEST["partida"]);
		$TipoVenta    = getSesionDato("TipoVentaTPV");
		$CodPartida   = CleanCadena($_REQUEST["codpartida"]);
		$mov          = new movimiento;
		$IdArqueo     = $mov->getIdArqueoEsCerradoCaja($IdLocal,$TipoVenta);

		$IdPartida    = obtenerIdPartidaCaja($CodPartida);

		if($IdPartida == 0 || !$IdPartida){
		  echo 'errorpartida';
		  return;
		}

	        /*+++++++++++ VALIDA CAJA +++++++++++++++*/
	        if( cajaescerrado() == 1 ) { 
	            echo "cjacerrada";
	            return;
	        }
        
		if($IdArqueo != $xIdArqueo || !$IdArqueo) {
		  echo "cjacerrada";
		  return;
		}
        
        	$movgral        = new movimientogral;
        	$CajaCentral = getSesionDato("esCajaCentral");
                $oLocal = new local;
        	$LocalDestino = $oLocal->getIdLocalCentral();
        
		if($operacion == 'Sustraccion' && ($CodPartida == "S112" || $CodPartida == "S115")){
		    $IdMoneda       = 1;
		    $IdArqueoGral   = $movgral->getIdArqueoEsCerrado($IdMoneda,$IdLocal);
		    $fechacajagral  = $movgral->getAperturaCajaGral($IdMoneda,$IdLocal);
                    
		    if($fechacajagral == 0){
		        echo "cjagralcerrada";
		        return;
		    }

                    if($CajaCentral && ($LocalDestino != $IdLocal)){
	                $esCjaCentralAbierta = $movgral->getAperturaCajaGral($IdMoneda,$LocalDestino);
                        
	                if($esCjaCentralAbierta == 0){
		            echo "cjacentralcerrada";
		            return;
	                }
                    }
		}

	        EntregarOperacionCaja($IdLocal,$cantidad,$concepto,$IdPartida,$operacion,
			              $fechacaja,$IdArqueo,$TipoVenta);

	        if($operacion == 'Sustraccion' && ($CodPartida == 'S112' || $CodPartida == 'S115')){
	            $xtipo     = ($TipoVenta == 'VD')? 'TPV PERSONAL':'TPV CORPORATIVO';
	            $concepto  = "VENTAS ".$xtipo;
	            $IdPartida = 4;
	            $cambiomoneda = 1;
	            $operacion  = 'Ingreso';
	            $IdUsuario  = CleanID(getSesionDato("IdUsuario"));
	            
	            EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
				          $cambiomoneda,$operacion,$fechacajagral,$IdUsuario,
				          $IdArqueoGral,$documento=false,$codigodoc=false,
				          $proveedor=false,$IdComprobante=false);

	            // Trasladando a la caja central
	            $CajaCentral = getSesionDato("esCajaCentral");
	    
	            if($CajaCentral  && ($LocalDestino != $IdLocal)){

		    // salida caja general
		    $concepto  = "VENTAS ".$xtipo;
		    $IdPartida = obtenerIdPartidaCaja("S124");
		    $cambiomoneda = 1;
		    $operacion  = 'Sustraccion';

		    EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
					  $cambiomoneda,$operacion,$fechacajagral,$IdUsuario,
					  $IdArqueoGral,$documento=false,$codigodoc=false,
					  $proveedor=false,$IdComprobante=false);

		    // Ingreso a almacen central
		    $mov    = new movimientogral;
		    $oLocal = new local;
		    $LocalDestino = $oLocal->getIdLocalCentral();
		    $esCjaCentralAbierta = $movgral->getAperturaCajaGral($IdMoneda,$LocalDestino);

		    if($esCjaCentralAbierta == 0){
		      echo "cjacentralcerrada";
		      return;
		    }

		    $IdArqueoDest = $mov->getIdArqueoEsCerrado($IdMoneda,$LocalDestino);

		    $LocalActual  = getNombreComercialLocal($IdLocal);
		    $operacion = 'Ingreso';
		    $concepto  = 'Transferencia desde '.$LocalActual.' - '.$concepto;
		    $IdLocal   = $LocalDestino;
		    $IdArqueo  = $IdArqueoDest;
		  
		    EntregarOperacionGral($IdLocal,$cantidad,$concepto,$IdPartida,$IdMoneda,
					  $cambiomoneda,$operacion,$fechacaja,$IdUsuario,
					  $IdArqueo,$documento,$codigodoc,$proveedor);
		  }
		}
		  
		echo "exito";
		break;

	case "hacerIngresoAdelantoDinero":				
		$IdLocal   = getSesionDato("IdTiendaDependiente");
		$cantidad  = CleanFloat($_REQUEST["cantidad"]);	
		$concepto  = $_REQUEST["concepto"];
		$IdUsuario = CleanID($_REQUEST["xidu"]);

		global $UltimaInsercion;	
		EntregarMetalico($IdLocal,$cantidad,$concepto,false,"Ingreso",false,1,false,
				 $IdUsuario);

		setSesionDato("OperacionCajaPresupuesto",$UltimaInsercion);
		setSesionDato("OperacionCajaImportePresupuesto",$cantidad);
		exit();	
		break;			

	case "modificaOperacionCaja":				
		$IdLocal  = CleanID($_GET["xidl"]);
		$IdOperacionCaja = CleanID($_GET["xidoc"]);
		$concepto = CleanText($_GET["concepto"]);	

		echo "~".ModificarOperacionCaja($IdLocal,$IdOperacionCaja,$concepto);
		exit();	
		break;			

        case "obtenerAnios":
                $dato = obtenerAnios();
		echo $dato;
		exit();
		break;

        case "obtenerUltimaFechaCaja":
		$IdLocal   = getSesionDato("IdTiendaDependiente");
		$TipoVenta = getSesionDato("TipoVentaTPV");
                $dato      = obtenerUltimaFechaCaja($IdLocal,$TipoVenta);
		echo $dato;
		exit();
		break;

	default:
		break;	
}


function getImporteCierre($IdArqueo){
        $TipoVenta = getSesionDato("TipoVentaTPV");

	$sql = "SELECT ImporteCierre From ges_arqueo_caja WHERE IdArqueo='$IdArqueo' AND  TipoVentaOperacion = '$TipoVenta'";
	$row = queryrow($sql);
	
	return $row["ImporteCierre"];	
}

function getUltimasDiezAsArray($IdLocal,$anio,$mes){
        $TipoVenta = getSesionDato("TipoVentaTPV");
	$inicio    = "$anio-$mes-01";
	$fin       = "$anio-$mes-31";
	$mov       = new movimiento;
	$fechacaja = $mov->getAperturaCaja($IdLocal,$TipoVenta);
	$afecha    = explode(" ",$fechacaja);
	$afecha    = explode("-",$afecha[0]);
	$datenow   = date('Y-m');
	$fcaja     = ($fechacaja)? $afecha[0]."-".$afecha[1]:$datenow;

	if($anio."-".$mes == $datenow){
	  $finicio = " AND (DATE(FechaApertura) >= '$inicio' OR DATE(FechaCierre) = '0000-00-00') ";
	  $ffin = "";
	}
	else{
	  $finicio = " AND DATE(FechaApertura) >= '$inicio' ";
	  $ffin    = " AND DATE(FechaApertura) <= '$fin' ";
	}

	$datos     = array();
	$sql = "SELECT IdArqueo, IdLocal, TipoVentaOperacion, ".
               "FechaApertura, FechaCierre, esCerrada, ".
               "ImporteApertura, ImporteIngresos, ImporteGastos, ".
               "ImporteAportaciones, ImporteSustracciones, ImporteTeoricoCierre, ".
               "ImporteCierre, ImporteDescuadre ".
               "FROM ges_arqueo_caja ".
               "WHERE IdLocal='$IdLocal' ".
	       "AND Eliminado = 0 ".
               "AND TipoVentaOperacion = '$TipoVenta' ".
               "$finicio $ffin ".
               "ORDER BY FechaApertura DESC, IdArqueo DESC LIMIT 31";

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
	$sql = "SELECT IdArqueo, IdLocal, TipoVentaOperacion, ".
               "FechaApertura, FechaCierre, esCerrada, ".
               "ImporteApertura, ImporteIngresos, ImporteGastos, ".
               "ImporteAportaciones, ImporteSustracciones, ImporteTeoricoCierre, ".
               "ImporteCierre, ImporteDescuadre, UtilidadVenta ".
               "FROM ges_arqueo_caja ".
               "WHERE IdArqueo='$IdArqueo' ";

	return queryrow($sql);	
}

function ActualizarArqueoDeLocal($IdArqueo,$IdLocal){
		$row = CalcularUltimoArqueo($IdLocal,$IdArqueo);
		ActualizarArqueo($IdArqueo, $row);
		return $row;						
}

function ActualizarArqueo($IdArqueo, $Datos){
	
	$IdArqueo = intval($IdArqueo);
	
	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto");
	
	$sql = "UPDATE ges_arqueo_caja SET ".
		" ImporteTeoricoCierre= '". $Datos["TeoricoFinal"] ."', ".
		" ImporteIngresos= '". $Datos["Ingreso"] ."', ".
		" ImporteSustracciones= '". $Datos["Sustraccion"] ."', ".
		" ImporteAportaciones= '". $Datos["Aportacion"] ."', ".		
		" ImporteGastos= '". $Datos["Gasto"] ."' " .			
		" WHERE IdArqueo='$IdArqueo' ";		 	
	query($sql,'Actualizar importes');
	
	$sql = "UPDATE ges_arqueo_caja SET ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones ".
	" WHERE IdArqueo='$IdArqueo'";
		
	query($sql,'Actualizando teorico-cierre');	 		

	$sql = "UPDATE ges_arqueo_caja SET ".		
		" ImporteDescuadre = ImporteTeoricoCierre - ImporteCierre ".		
		" WHERE IdArqueo='$IdArqueo' ";			
	query($sql,'Actualizar descuadre');		
}


function MarcarArqueoCerrado($IdArqueo){
		$IdArqueo = intval($IdArqueo);

		//Cierra caja 
		$sql = "UPDATE ges_arqueo_caja SET ".
		" FechaCierre= NOW(), ".
		" esCerrada = 1 ".
		" WHERE IdArqueo='$IdArqueo' ";
		query($sql,"Marcando arqueo como cerrado");		

		//Cierra caja modo forzado
		$TipoVenta = getSesionDato("TipoVentaTPV");
		$IdLocal  = getSesionDato("IdTiendaDependiente");

		$sql = "UPDATE ges_arqueo_caja SET ".
		  " FechaCierre = NOW(), ".
		  " esCerrada   = 1 ".
		  " WHERE esCerrada  = 0 ".
		  " AND   TipoVentaOperacion = '$TipoVenta'".
		  " AND   IdLocal = '$IdLocal'";
		query($sql,"Marcando arqueo como cerrado [modo forzado]");		
		
		//Actualiza utilidad venta
		ActualizarUtilidadVenta($IdArqueo);
}


function CalcularUltimoArqueo($IdLocal,$IdArqueo=false){

	$datos     = array();
	$IdLocal   = CleanID($IdLocal);
	$TipoVenta = getSesionDato("TipoVentaTPV");

	if (!$IdArqueo){
		$sql = "SELECT IdArqueo ".
		  "FROM ges_arqueo_caja ".
		  "WHERE IdLocal   = '$IdLocal' ".
		  "AND   Eliminado = 0 ".
		  "AND   esCerrada = 0 ".
		  "AND   TipoVentaOperacion = '$TipoVenta' ".
		  "ORDER BY FechaCierre DESC";
		$row = queryrow($sql,'Buscando arqueo abierto');
		$IdArqueo = $row["IdArqueo"];	
		if (!$IdArqueo)
		  return false;	
	}	
		
	$datos["IdArqueo"] = $IdArqueo;		
	$modos = array("Ingreso","Sustraccion","Aportacion","Gasto");

	foreach($modos as $tipo){	
	  $sql = "SELECT sum( Importe ) AS SumaImporte".
	    " FROM  ges_dinero_movimientos ".
	    " WHERE Eliminado = 0 ".
	    " AND   IdLocal 	= '$IdLocal' ".
	    " AND   IdArqueoCaja 	= '$IdArqueo' ".
	    " AND   TipoOperacion = '$tipo' ".
	    " AND   TipoVentaOperacion = '$TipoVenta' ".
	    " AND   IdModalidadPago = 1";
	  //NOTA: investigar si modalidad de pago 1 es correcto o hay que contemplar otros tipos
	  $row = queryrow($sql);
	  $datos[$tipo] = $row["SumaImporte"];
	}	

	$datos["TeoricoFinal"]= $datos["Ingreso"]+$datos["Aportacion"]-$datos["Gasto"]-$datos["Sustraccion"];
	
	//error(__FILE__ . __LINE__ , "If: final:". $datos["TeoricoFinal"]);
	
	return $datos;				
}


function CalcularUltimoArqueo2($IdLocal,$IdArqueo=false){

	$datos = array();
	
	$IdLocal = CleanID($IdLocal);
	$TipoVenta = getSesionDato("TipoVentaTPV");

	if (!$IdArqueo){
	  $sql = "SELECT IdArqueo, ImporteCierre ".
	    "FROM  ges_arqueo_caja ".
	    "WHERE IdLocal   ='$IdLocal' ".
	    "AND   Eliminado = 0 ".
	    "AND   esCerrada = 1 ".
	    "AND   TipoVentaOperacion = '$TipoVenta' ".
	    "ORDER BY FechaCierre DESC";
	  $row = queryrow($sql,'Buscando arqueo abierto');
	  $IdArqueo = $row["IdArqueo"];	
	  $ImporteCierre = $row["ImporteCierre"];
	  
	  if (!$IdArqueo)
		  return false;	
	}	
	$datos["IdArqueo"] = $IdArqueo;		
	$datos["ImporteCierre"] = $ImporteCierre;
	
	return $datos;				
}

function getMovimientosArqueo($IdArqueo){
	
	$IdArqueo = intval($IdArqueo);
	$TipoVenta = getSesionDato("TipoVentaTPV");
	$datos = array();
	$sql =
	  " SELECT IdOperacionCaja, Identificacion, (IF(ges_dinero_movimientos.IdPartidaCaja <>0,(SELECT ges_partidascaja.PartidaCaja FROM ges_partidascaja WHERE ges_partidascaja.IdPartidaCaja = ges_dinero_movimientos.IdPartidaCaja AND ges_partidascaja.TipoCaja = '$TipoVenta'),'Venta')) as PartidaCaja, IdArqueoCaja, ".
          " ges_dinero_movimientos.TipoOperacion, ".
	  " TipoVentaOperacion, FechaCaja, Concepto, Importe, ".
	  " IdModalidadPago, FechaPago, IdComprobante,IdPartidaCaja ".
	  " FROM   ges_dinero_movimientos ".
	  " INNER JOIN ges_usuarios ON ".
	  " ges_dinero_movimientos.IdUsuario = ges_usuarios.IdUsuario ".
	  " WHERE  IdArqueoCaja       = '$IdArqueo' ".
	  " AND    IdModalidadPago    = 1 ".
	  " AND    TipoVentaOperacion = '$TipoVenta' ".
	  " AND    ges_dinero_movimientos.Eliminado = 0 ".
	  " ORDER  BY IdOperacionCaja DESC";
	$res = query($sql);
	if (!$res) return $datos;
	
	$n = 0;
	while( $row = Row($res)){
	        $row["Cliente"] = ($row["IdComprobante"] > 0)? obtenerClientexComprobante($row["IdComprobante"]):" ";
		$row["Cliente"] = str_replace('&#038;','&',$row["Cliente"]);
		
		$codpartida    = ($row["IdPartidaCaja"] == 0)? '0':obtenerCodigoPartidaCaja($row["IdPartidaCaja"]);
		$row["Codigo"] = $codpartida;

		$datos["mov_$n"] = $row;
		$n++;		
	} 			
	return $datos;					
}


function InsertarNuevaCaja($datosArqueo,$IdLocal){
	global $UltimaInsercion;	
	$IdLocal = CleanID($IdLocal);	
	
	$ImporteApertura = $datosArqueo["ImporteCierre"];
	$ImporteTeoricoCierre = $ImporteApertura;
	$TipoVenta = getSesionDato("TipoVentaTPV");
	$sql = "INSERT INTO ges_arqueo_caja ( 
		IdLocal, FechaApertura, FechaCierre, ImporteApertura, ImporteIngresos,
		ImporteGastos,ImporteAportaciones,ImporteSustracciones,ImporteTeoricoCierre,
		ImporteCierre,ImporteDescuadre,Eliminado,TipoVentaOperacion )
		VALUES (
		'$IdLocal', NOW(),'0000-00-00', '$ImporteApertura', '0',
		'0', '0', '0', '$ImporteTeoricoCierre',
		'0', '0', '0', '$TipoVenta' )";
	
	$res = query($sql,'Insertando nueva caja');
	
	if ($res)return $UltimaInsercion;
	return 0;	
}

function actualizarCantidadCaja($IdLocal,$cantidad){
	$IdLocal = CleanID($IdLocal);
	$cantidad = CleanFloat($cantidad);
	$TipoVenta = getSesionDato("TipoVentaTPV");
	$cantidad = CleanRealMysql($cantidad);
	$sql = "UPDATE ges_arqueo_caja SET ImporteCierre = '$cantidad' WHERE IdLocal='$IdLocal' AND esCerrada=0 AND  TipoVentaOperacion = '$TipoVenta' ";
	query($sql,'Actualizando cantidad de cierre');
	
	$sql = "UPDATE ges_arqueo_caja ".
	  "SET   ImporteTeoricoCierre = ImporteApertura+ImporteAportaciones-ImporteGastos+ImporteIngresos-ImporteSustracciones ".
	  "WHERE IdLocal   = '$IdLocal' ".
	  "AND   esCerrada = 0 ".
	  "AND   TipoVentaOperacion = '$TipoVenta' ";
	query($sql,'Actualizando teorico');	 		
	
	$sql = "UPDATE ges_arqueo_caja ".
	  "SET   ImporteDescuadre = ImporteCierre - ImporteTeoricoCierre ".
	  "WHERE IdLocal = '$IdLocal' ".
	  "AND   TipoVentaOperacion = '$TipoVenta' ";
	query($sql,'Actualizando descuadre');
}

function ActualizarUtilidadVenta($IdArqueo){
    $sql = "SELECT IdComprobante ".
           "FROM   ges_dinero_movimientos ".
           "WHERE  IdArqueoCaja = '$IdArqueo' ".
           "AND    Eliminado = 0 ";

    $res = query($sql);
    $utilidad = 0;

    while($row = Row($res)){
	$IdComprobante = $row["IdComprobante"];

	if($IdComprobante != 0){
	  
	  $status = verificarComprobante($IdComprobante);
	  if($status == 0){
	    $costocbte   = obtenerCostoComprobante($IdComprobante);
	    $importecbte = obtenerImporteNetoComprobante($IdComprobante);
	    $margen      = $importecbte - $costocbte;
	    $utilidad    = $utilidad + $margen;
	  }
	}
    }

    $sql = "UPDATE ges_arqueo_caja SET UtilidadVenta = '$utilidad' ".
           "WHERE  ges_arqueo_caja.IdArqueo = '$IdArqueo' ";

    query($sql,'Actualizacion Utilidad Venta');
}

function verificarComprobante($IdComprobante){
    $sql = "SELECT     ges_comprobantesnum.Status ".
           "FROM       ges_comprobantes ".
           "INNER JOIN ges_comprobantesnum ON ges_comprobantes.IdComprobante = ges_comprobantesnum.IdComprobante ".
           "INNER JOIN ges_comprobantestipo ON ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante ".
           "WHERE      ges_comprobantes.IdComprobante = '$IdComprobante' ".
           "AND        ges_comprobantestipo.TipoComprobante IN ('Factura','Boleta','Ticket','Albaran')";

    $row = queryrow($sql);
    
    if($row["Status"] == 'Anulado')
      return 1;
    else
      return 0;
}

function obtenerCostoComprobante($IdComprobante){
    $sql = "SELECT SUM(Cantidad*CostoUnitario) AS Costo ".
           "FROM   ges_comprobantesdet ".
           "WHERE  ges_comprobantesdet.IdComprobante = '$IdComprobante' ".
           "AND    ges_comprobantesdet.IdPedidoDet   <> 0 ".
           "AND    Eliminado = 0 ";

    $row = queryrow($sql);

    return $row["Costo"];
}
      
function obtenerImporteNetoComprobante($IdComprobante){
    $sql = "SELECT ImporteNeto ".
           "FROM   ges_comprobantes ".
           "WHERE  ges_comprobantes.IdComprobante = '$IdComprobante' ".
           "AND    Eliminado = 0 ";

    $row = queryrow($sql);

    return $row["ImporteNeto"];
}

function ModificarOperacionCaja($IdLocal,$IdOperacionCaja,$concepto){
  $sql = " UPDATE ges_dinero_movimientos SET Concepto = '$concepto'".
         " WHERE IdOperacionCaja='$IdOperacionCaja' ".
         " AND IdLocal = '$IdLocal' ";
  
  $res = query($sql,'Actualizando Concepto');
  if($res)
    return 1;
  else
    return 'false';
}

function obtenerAnios(){
  $IdLocal = getSesionDato("IdTienda");
  $sql = "SELECT DISTINCT(YEAR(FechaApertura)) as Anios ".
         "FROM ges_arqueo_caja ".
         "WHERE DATE(ges_arqueo_caja.FechaApertura) <> '0000-00-00' ".
         "AND Eliminado = 0 ".
         "AND IdLocal = $IdLocal ".
         "ORDER BY ges_arqueo_caja.FechaApertura DESC";

  $res = query($sql);
  $anios = '';
  $t = '';
  while($row = Row($res)){
    $anios .= $t.$row["Anios"];
    $t = ',';
  }

  return $anios;
}

function obtenerCodigoPartidaCaja($IdPartidaCaja){
  $sql = "SELECT Codigo ".
         "FROM   ges_partidascaja ".
         "WHERE  IdPartidaCaja = '$IdPartidaCaja'";
  $row = queryrow($sql);
  return $row["Codigo"];
}

function obtenerUltimaFechaCaja($IdLocal,$TipoVenta){
  $sql = "SELECT FechaApertura as Fecha ".
         "FROM   ges_arqueo_caja ".
         "WHERE  IdLocal = '$IdLocal' ".
         "AND TipoVentaOperacion = '$TipoVenta' ".
         "AND Eliminado = 0 ".
         "ORDER BY FechaApertura DESC ".
         "LIMIT 1 ";
  $row = queryrow($sql);
  return $row["Fecha"];
}


?>
