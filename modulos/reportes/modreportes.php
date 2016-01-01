<?php 
require("../../tool.php");
SimpleAutentificacionAutomatica("novisual-services");

$modo = CleanText($_GET["modo"]);
$IdLocal = getSesionDato("IdTienda");
$locales = getLocalesPrecios($IdLocal);
//global $avencimientos;
$GLOBALS["avencimiento"] = "";//array();

switch($modo){		
  case "xReportes":
    $Title      = "Utilidad de Venta";
    $FechaDesde = 'false';
    $Periodo    = 'false';
    $btnBuscar  = 'buscarMovimientos()';
    $Vence      = 'true';
    $listvencidos = 'true';
    $listutilidad = 'false';
    $xreporte   = 'utilidad';
    $btnprint   = 'true';
    include("xulreportes.php");
    exit();
    break;
  case "xReportesVence":
    $Title      = "Fecha de Vencimiento";
    $FechaDesde = 'true';
    $Periodo    = 'true';
    $btnBuscar  = 'buscarVencimientos()';
    $Vence      = 'false';
    $listvencidos = 'false';
    $listutilidad = 'true';
    $xreporte   = 'vencimiento';
    $btnprint   = 'false';
    include("xulreportes.php");
    exit();
    break;

  case "obtenerAnios":
    $dato = obtenerAnios();
    echo $dato;
    exit();
    break;
    
  case "movimientos":
    $IdLocal = CleanID($_GET["local"]);
    $Desde   = CleanText($_GET["desde"]);
    $Hasta   = CleanText($_GET["hasta"]);
    $Periodo = CleanText($_GET["periodo"]);
    $Mes   = CleanText($_GET["mes"]);
    $Trimestre   = CleanText($_GET["trimestre"]);
    $Semestre   = CleanText($_GET["semestre"]);
    $Anio   = CleanText($_GET["anio"]);

    $datos   = obtenerMovimientosReporte($IdLocal,$Desde,$Hasta,$Periodo,$Mes,$Trimestre,
					 $Semestre,$Anio);

    VolcandoXML( Traducir2XML($datos),"Kardex");
    exit();
    break;

  case "vencimientos":
    $IdLocal = CleanID($_GET["local"]);
    $Desde   = CleanText($_GET["desde"]);
    $Hasta   = CleanText($_GET["hasta"]);
    $Estado = CleanText($_GET["opvence"]);

    $datos   = obtenerVencimientos($IdLocal,$Desde,$Hasta,$Estado);

    VolcandoXML( Traducir2XML($datos),"vence");
    exit();
    break;
    
  case "imprimir":
    $IdLocal = CleanID($_GET["xlocal"]);
    $Desde   = CleanCadena($_GET["desde"]);
    $Hasta   = CleanCadena($_GET["hasta"]);
    $OpVence = CleanText($_GET["opestado"]);

    include("../fpdf/imprimir_fechavencimiento.php");
    break;
}

function obtenerMovimientosReporte($IdLocal,$Desde,$Hasta,$Periodo,$Mes,$Trimestre,
				   $Semestre,$Anio){

  $IdLocal = ($IdLocal == 0)? "%%":$IdLocal;
  $xgroup = "";
  $xfecha = "";
  switch($Periodo){
    case 'Dia':
      $inicio = "$Desde";
      $fin    = "$Desde";
      break;

    case 'Mes':
      $inicio = "$Anio-$Mes-1";
      $fin    = "$Anio-$Mes-31";
      break;

    case 'Trimestre':
      if($Trimestre == '1'){
	$inicio = "$Anio-1-1";
	$fin    = "$Anio-3-31";
      }
      if($Trimestre == '2'){
	$inicio = "$Anio-4-1";
	$fin    = "$Anio-6-30";
      }
      if($Trimestre == '3'){
	$inicio = "$Anio-7-1";
	$fin    = "$Anio-9-30";
      }
      if($Trimestre == '4'){
	$inicio = "$Anio-8-1";
	$fin    = "$Anio-12-31";
      }

      break;
    case 'Semestre':
      if($Semestre == '1'){
	$inicio = "$Anio-1-1";
	$fin    = "$Anio-6-31";
      }
      if($Semestre == '2'){
	$inicio = "$Anio-7-1";
	$fin    = "$Anio-12-31";
      }
      break;

    case 'Anio':
      $inicio = "$Anio-1-1";
      $fin    = "$Anio-12-31";
      break;

    case 'EntreFecha':
      $inicio = "$Desde";
      $fin    = "$Hasta";
      break;
  }

  if($Periodo != 'Todo')
    $xfecha = "AND DATE(FechaInsercion) >= '$inicio' 
               AND DATE(FechaInsercion) <= '$fin' ";
  else 
    $xfecha = "";
  
  $sql = "SELECT ges_locales.NombreComercial as Local,ges_locales.IdLocal,
          GROUP_CONCAT(DISTINCT(ges_dinero_movimientos.IdComprobante)) as IdComprobante,
          '0' as Impuesto,
          SUM(ges_dinero_movimientos.Importe) as Importe ".
         "FROM ges_dinero_movimientos ".
         "INNER JOIN ges_comprobantesnum ON ges_dinero_movimientos.IdComprobante = ges_comprobantesnum.IdComprobante
          INNER JOIN ges_comprobantestipo ON ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante
          INNER JOIN ges_locales ON ges_dinero_movimientos.IdLocal = ges_locales.IdLocal ".
         "WHERE ges_comprobantestipo.TipoComprobante IN ('Factura','Boleta','Ticket','Albaran')
          AND ges_comprobantesnum.Status <> 'Anulado'
          AND ges_dinero_movimientos.Eliminado = 0
          AND ges_dinero_movimientos.IdLocal like '$IdLocal' ".
         "AND ges_dinero_movimientos.IdComprobante > 0 ".
          "$xfecha".
         "GROUP BY ges_dinero_movimientos.IdLocal, ges_dinero_movimientos.IdComprobante,'$xgroup'
          ORDER BY ges_locales.IdLocal,ges_dinero_movimientos.FechaInsercion ASC";


  $res = query($sql);
  $utilidad = Array();
  $impuesto = getSesionDato("IGV");
  $tcosto   = 0;
  $timporte = 0;
  $tutilidad= 0;
  $t = 0;
  $xlocal = 0;
  while($row = Row($res)){
    $nombre = "utilidad_".$t++;
    $tcosto = ontenerCostoComprobante($row["IdComprobante"]);
    $ximpte = obtenerImporteComprobante($row["IdComprobante"]);

    if($ximpte > $row["Importe"]){
      $x = (100*$row["Importe"])/$ximpte;
      $xcosto = ($x*$row["Importe"])/100;
      $tcosto = $xcosto;
    }

    $timporte  = $row["Importe"];//round(($timporte + $row["Importe"]),2);
    $tcosto    = round($tcosto,2);
    $timpuesto = round(($tcosto*$impuesto/100),2);
    $tutilidad = $timporte - $tcosto - $timpuesto;
    
    $row["Impuesto"]    = $timpuesto;
    $row["Costo"]    = $tcosto;
    $row["Importe"]  = $timporte;
    $row["Utilidad"] = $tutilidad;
    
    $utilidad[$nombre] = $row;
  }

  return $utilidad;
}

function ontenerCostoComprobante($idc){
  $sql = "SELECT ROUND(SUM(CostoUnitario*Cantidad),2) as Costo ".
         "FROM ges_comprobantesdet ".
         "WHERE ges_comprobantesdet.IdComprobante IN ($idc) ";

  $row = queryrow($sql);
  return $row["Costo"];
}

function obtenerAnios(){
  $sql = "SELECT GROUP_CONCAT(DISTINCT(YEAR(FechaComprobante))) as Anios ".
         "FROM ges_comprobantes ".
         "ORDER BY ges_comprobantes.FechaComprobante DESC";

  $row = queryrow($sql);
  return $row["Anios"];
}

function obtenerVencimientos($IdLocal,$Desde,$Hasta,$Estado){
  $xIdLocal = ($IdLocal == 0)? "":" AND ges_locales.IdLocal = $IdLocal ";

  switch($Estado){
    case 'Vencido':
      $esVencido = true;
      break;
    case 'PorVencer':
      $esVencido = false;
      break;
  }

  $sql2 = "SELECT  IdPedidoDet, SUM(CantidadMovimiento) as Saldo ".
         "FROM ges_kardex ".
         "INNER JOIN ges_locales ON ges_kardex.IdLocal = ges_locales.IdLocal ".
         "WHERE ges_kardex.Eliminado = 0 ".
         "x$IdLocal ".
         "GROUP BY IdPedidoDet ".
         "HAVING Saldo > 0 ";

  $res2 = query($sql2);
  $iddets = array();
  $iddet = array();
  $vencimiento = Array();
  $t=0;
  
  while($row2 = Row($res2)){

    $yid = $row2["IdPedidoDet"];

    $iddet[$yid]["id"] = $yid;
    $iddet[$yid]["Saldo"] = $row2["Saldo"];

    array_push($iddets,$row2["IdPedidoDet"]);
  }

  if(sizeof($iddets) == 0) return $vencimiento;

  $res1 = obtenerDetVencimiento($iddets,$esVencido,$Desde,$Hasta,$IdLocal);

  while($row1 = Row($res1)){

    foreach($iddet as $key=>$value){

      if($row1["IdPedidoDet"] == $key){
	$row1["Saldo"] = $value["Saldo"];
      }
      continue;
    }
    $nombre = "vence_".$t++;
    $vencimiento[$nombre] = $row1;
  }
  //$_SESSION["Vencimientos"] = $vencimiento;
  return $vencimiento;
}

function obtenerDetVencimiento($iddets,$esVencido,$Desde,$Hasta,$IdLocal){
  $IdLocal = ($IdLocal == 0)? "":" AND ges_pedidos.IdLocal = $IdLocal ";
  $Hoy = date("Y-m-d");
  $xid = "";
  $xc  = "";

  foreach($iddets as $key=>$value){
    $xid = $xid.$xc.$value;
    $xc = ",";
  }

  $extrafecha = ($esVencido)? " AND ges_pedidosdet.FechaVencimiento < '$Hoy'":" AND ges_pedidosdet.FechaVencimiento >= '$Desde' AND ges_pedidosdet.FechaVencimiento <= '$Hasta' ";

  $sql = " SELECT ges_pedidos.IdLocal, ges_locales.NombreComercial as Local, ".
         " IdPedidoDet,ges_pedidosdet.IdProducto,CodigoBarras as CB,".
         " CONCAT(ges_productos_idioma.Descripcion,' ',ges_marcas.Marca,' ',ges_modelos.Color,' ',ges_detalles.Talla,' ',ges_laboratorios.NombreComercial) as Producto,".
         " DATE_FORMAT(ges_pedidosdet.FechaVencimiento,'%d/%m/%Y') as FechaVencimiento, ".
         " IF(ges_pedidosdet.Lote = '',' ',ges_pedidosdet.Lote) as Lote".
         " FROM ges_pedidosdet".
         " INNER JOIN ges_productos ON ges_pedidosdet.IdProducto = ges_productos.IdProducto".
         " INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
         " INNER JOIN ges_marcas ON ges_productos.IdMarca = ges_marcas.IdMarca ".
         " INNER JOIN ges_modelos ON ges_productos.IdColor = ges_modelos.IdColor ".
         " INNER JOIN ges_detalles ON ges_productos.IdTalla = ges_detalles.IdTalla ".
         " INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
         " INNER JOIN ges_pedidos ON ges_pedidosdet.IdPedido = ges_pedidos.IdPedido ".
         " INNER JOIN ges_locales ON ges_pedidos.IdLocal = ges_locales.IdLocal ".
         " WHERE IdPedidoDet IN ($xid) ".
         " AND ges_pedidosdet.FechaVencimiento <> '0000-00-00'".
         " $IdLocal ".
         " $extrafecha ".
         " ORDER BY ges_pedidosdet.FechaVencimiento ASC";

  $res     = query($sql);

  return  $res;
}
?>
