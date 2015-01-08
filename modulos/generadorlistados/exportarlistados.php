<?php
include ("../../tool.php");
$modo = CleanCadena($_GET["modo"]);

switch($modo){
  case "ExportarDirectoCSV":
    $TipoArchivo = CleanCadena($_GET["xfile"]);
    $Tabla       = CleanCadena($_GET["xtab"]);
    $Id          = CLeanID($_GET["xidl"]);
    $Desde       = CleanCadena($_GET["desde"]);
    $Hasta       = CleanCadena($_GET["hasta"]);
    $IdLocal     = CleanText($_GET["local"]);
    $IdFamilia   = CleanText($_GET["familia"]);
    $IdSubsidia  = CLeanID($_GET["subsidiario"]);
    $STSubsid    = CleanText($_GET["stsubsid"]);
    $IdProveedor = CleanText($_GET["proveedor"]);
    $IdUsuario   = CleanText($_GET["usuario"]);
    $Referencia  = CleanRealMysql($_GET["referencia"]);
    $CodigoBarra = CleanCadena($_GET["cb"]);
    $NumeroSerie = CleanText($_GET["ns"]);
    $Lote        = CleanText($_GET["lote"]);
    $Partida     = CleanID($_GET["partida"]);
    $DNICliente  = CleanText($_GET["dnicliente"]);
    $TipoVenta   = CleanText($_GET["tipoventa"]);
    $esTPVOP     = CleanText($_GET["estpvop"]);
    $LocalActual = CleanID($_GET["localactual"]);
    $TipoComprobante   = CleanText($_GET["tipocomprobante"]);
    $SerieComprobante  = CleanText($_GET["seriecomprobante"]);
    $EstadoComprobante = CleanText($_GET["estadocomprobante"]);
    $EstadoPago        = CleanText($_GET["estadopago"]);
    $Modalidad         = CleanText($_GET["modalidad"]);
    $EstadoPromo       = CleanText($_GET["estadopromo"]);
    $TipoPromo         = CleanText($_GET["tipopromo"]);
    $TipoOperacion     = CleanText($_GET["tipooperacion"]);
    $TipoOpCjaGral     = CleanText($_GET["tipoopcjagral"]);
    $PeriodoVenta      = CleanText($_GET["periodoventa"]);
    $NombreCliente     = CleanText($_GET["nombrecliente"]);
    $TipoCliente       = CleanText($_GET["tipocliente"]);
    $IdMarca           = CleanText($_GET["idmarca"]);
    $CondicionVenta    = CleanText($_GET["condicionventa"]);
    $EstadoOS          = CleanText($_GET["estadoos"]);
    $Prioridad         = CleanText($_GET["prioridad"]);
    $Facturacion       = CleanText($_GET["facturacion"]);
    $EstadoSuscripcion = CleanText($_GET["estadosucripcion"]);
    $TipoSuscripcion   = CleanText($_GET["tiposuscripcion"]);
    $TipoPagoSuscripcion = CleanText($_GET["tipopagosuscripcion"]);
    $Prolongacion      = CleanText($_GET["prolongacion"]);
    $IdCLiente         = CleanText($_GET["idcliente"]);
    $Codigo            = CleanText($_GET["codigo"]);
    $EstadoPagoVenta   = CleanText($_GET["estadopagoventa"]);
    $Cobranza          = CleanText($_GET["cobranza"]);

    $Consulta          = "SELECT * FROM $Tabla WHERE (IdListado = '$Id')";
    $row               = queryrow($Consulta);

    if($row){
      $CodigoSQL      = $row["CodigoSQL"];
      $NombreArchivo  = CleanCadena($row["Categoria"])."_".CleanCadena($row["NombrePantalla"]);
      $NombreArchivo  = strtoupper($NombreArchivo);
      $xNombreArchivo = str_replace(" ","_",$NombreArchivo);
      $CodigoSQL      = ProcesarSQL($CodigoSQL,$Desde, $Hasta, $IdLocal,
				    $IdFamilia, $IdSubsidia, $STSubsid,
				    $IdProveedor, $IdUsuario,$Referencia, $CodigoBarra,
				    $NumeroSerie, $Lote, $Partida, $TipoVenta,$esTPVOP,
				    $LocalActual,$DNICliente,$TipoComprobante,
				    $SerieComprobante,$EstadoComprobante,$EstadoPago,
				    $Modalidad,$EstadoPromo,$TipoPromo,$TipoOperacion,
				    $TipoOpCjaGral,$PeriodoVenta,$NombreCliente,
				    $TipoCliente,$IdMarca,$CondicionVenta,$EstadoOS,
				    $Prioridad,$Facturacion,$EstadoSuscripcion,
				    $TipoSuscripcion,$TipoPagoSuscripcion,
				    $Prolongacion,$IdCLiente,$Codigo,$EstadoPagoVenta,
				    $Cobranza);

    }
    $NombreArchivo = '"'.$NombreArchivo.'"';

    switch($TipoArchivo){
    case 'csv':
      $xNombreArchivo = $xNombreArchivo.".csv";
      exportMysqlToCsv($CodigoSQL,$xNombreArchivo,$NombreArchivo);
      break;
    }

    exit();				
    break;
  case 'inventaAjuste':
    $hasta    = CleanFechaES($_GET["hasta"]);
    $desde    = CleanFechaES($_GET["desde"]);
    $xfamilia = CleanID($_GET["familia"]);
    $xmarca   = CleanID($_GET["marca"]);
    $esInvent = ( $_GET["xinventario"] == "Inventario" )? true:false;
    $xinvent  = CleanID($_GET["xidinventario"]);
    $idlocal  = CleanID($_GET["xlocal"]);
    $xnombre  = CleanText($_GET["xnombre"]);
    $xcodigo  = CleanCB($_GET["xcodigo"]);
    $xope     = CleanID($_GET["xope"]);
    $xidope   = ($xope==7)?CleanID($_GET["xidope"]):false;//**Pendiente busqueda inventarios
    $xmov     = CleanText($_GET["xmov"]);
    $invent   = CleanText($_GET["xtitulo"]);
    $almacen  = CleanText($_GET["alma"]);
    $xLocal   = getNombreComercialLocal($idlocal);
    $almacen  = ($almacen)? $almacen:$xLocal;

    $xinvent = ($xinvent)? $xinvent:'none';

    $selcvs = 
      "       DATE_FORMAT(FechaMovimiento, '%e/%m/%y %H:%i') as FechaMovimiento, ".
      "       ges_usuarios.Identificacion as Usuario, ".
      "       ges_locales.NombreComercial as Almacen, ".
      "       CONCAT(ges_productos.CodigoBarras,' ',ges_productos_idioma.Descripcion,' ',".
      "       ges_marcas.Marca,' ', ".
      "       ges_modelos.Color,' ', ".
      "       ges_detalles.Talla,' ', ".
      "       ges_laboratorios.NombreComercial) as Producto, ".
      "       ges_contenedores.Contenedor, ".
      "       ges_productos.UnidadMedida, ".
      "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
      "       KardexOperacion, ".
      "       ges_kardex.TipoMovimiento, ".
      "       (SELECT ges_kardexajusteoperacion.AjusteOperacion from ges_kardexajusteoperacion where ges_kardexajusteoperacion.IdKardexAjusteOperacion = ges_kardex.IdKardexAjusteOperacion AND ges_kardex.IdKardexAjusteOperacion > 0) as 'AjusteOperacion', ".
      "       CantidadMovimiento, ".
      "       ROUND(CostoUnitarioMovimiento,2) as CostoUnitarioMovimiento, ".
      "       ROUND(CostoTotalMovimiento,2) as CostoTotalMovimiento, ".
      "       SaldoCantidad, ".
      "       IF ( ges_kardex.Observaciones like '', ' ',ges_kardex.Observaciones) ".
      "       as Observaciones ";
    

    $sql = obtenerKardexMovimientosInventario($idlocal,$desde,$hasta,$xfamilia,
					      $xmarca,$xope,$xmov,$xnombre,
					      $xcodigo,$xinvent,$esInvent,
					      false,$selcvs,false,false,false);


    $xinvent       = str_replace("- ","",$invent);
    $xinvent       = str_replace(" ","_",$xinvent);
    $xalmacen      = str_replace(" ","_",$almacen);
    $name          = "gPOS_".$xalmacen."_".$xinvent;
    $NombreArchivo = $name.".csv";
    $xtitulo       = '"'.$almacen.' '.$invent.'"';
    exportMysqlToCsv($sql,$NombreArchivo,$xtitulo); 
    break;
}

function ProcesarSQL($cod,$Desde,$Hasta,$IdLocal,$IdFamilia,
		     $IdSubsidia,$STSubsid,$IdProveedor,$IdUsuario,$Referencia,
		     $CodigoBarra,$NumeroSerie,$Lote,$Partida,$TipoVenta,$esTPVOP,
		     $LocalActual,$DNICliente,$TipoComprobante,$SerieComprobante,
		     $EstadoComprobante,$EstadoPago,$Modalidad,$EstadoPromo,$TipoPromo,
		     $TipoOperacion,$TipoOpCjaGral,$PeriodoVenta,$NombreCliente,
		     $TipoCliente,$IdMarca,$CondicionVenta,$EstadoOS,$Prioridad,
		     $Facturacion,$EstadoSuscripcion,$TipoSuscripcion,$TipoPagoSuscripcion,
		     $Prolongacion,$IdCLiente,$Codigo,$EstadoPagoVenta,$Cobranza) {

  $Moneda = getSesionDato("Moneda");
  
  if( function_exists("getSesionDato"))
    $IdLang = getSesionDato("IdLenguajeDefecto");
  
  if (!$IdLang)
    $IdLang = 1;

  if($PeriodoVenta == 'DAY')
    $g_periodo = "$PeriodoVenta(FechaComprobante)";
  if($PeriodoVenta == 'WEEK')
    $g_periodo = "$PeriodoVenta(FechaComprobante)";
  if($PeriodoVenta == 'MONTH')
    $g_periodo = "$PeriodoVenta(FechaComprobante)";
  if($PeriodoVenta == 'YEAR')
    $g_periodo = "$PeriodoVenta(FechaComprobante)";

  $EstadoPagoVenta = str_replace('%%',"'%%'",$EstadoPagoVenta);

  $cod = str_replace("%IDIDIOMA%",$IdLang,$cod);
  $cod = str_replace("%DESDE%",		$Desde,$cod);
  $cod = str_replace("%HASTA%",		$Hasta,$cod);
  $cod = str_replace("%IDTIENDA%",	$IdLocal,$cod);
  $cod = str_replace("%IDFAMILIA%",	$IdFamilia,$cod);	
  $cod = str_replace("%IDSUBSIDIARIO%",	$IdSubsidia,$cod);
  $cod = str_replace("%STATUSTBJOSUBSIDIARIO%",	$STSubsid,$cod);
  $cod = str_replace("%IDPROVEEDOR%",	$IdProveedor,$cod);
  $cod = str_replace("%IDUSUARIO%",	$IdUsuario,$cod);
  $cod = str_replace("%REFERENCIA%",	$Referencia,$cod);
  $cod = str_replace("%CODIGOBARRAS%",  $CodigoBarra,$cod);
  $cod = str_replace("%NUMEROSERIE%",   $NumeroSerie,$cod);
  $cod = str_replace("%LOTE%",          $Lote,$cod);
  $cod = str_replace("%PARTIDA%",	$Partida,$cod);
  $cod = str_replace("%TIPOVENTAOP%",	$TipoVenta,$cod);
  $cod = str_replace("%IDLOCAL%",	$LocalActual,$cod);
  $cod = str_replace("%DNICLIENTE%",	$DNICliente,$cod);
  $cod = str_replace("%TIPOCOMPROBANTE%",	$TipoComprobante,$cod);
  $cod = str_replace("%SERIECOMPROBANTE%",	$TipoComprobante,$cod);
  $cod = str_replace("%ESTADOCOMPROBANTE%",	$EstadoComprobante,$cod);
  $cod = str_replace("%ESTADOPAGO%",	$EstadoPago,$cod);
  $cod = str_replace("%MODALIDAD%",	$Modalidad,$cod);
  $cod = str_replace("%ESTADOPROMO%",	$EstadoPromo,$cod);
  $cod = str_replace("%TIPOPROMO%",	$TipoPromo,$cod);
  $cod = str_replace("%TIPOOPERACION%",	$TipoOperacion,$cod);
  $cod = str_replace("%TIPOOPCJAGRAL%", $TipoOpCjaGral,$cod);
  $cod = str_replace("%PERIODOVENTA%",  $PeriodoVenta,$cod);
  $cod = str_replace("%CLIENTE%",       $NombreCliente,$cod);
  $cod = str_replace("%TIPOCLIENTE%",   $TipoCliente,$cod);
  $cod = str_replace("'%PERIODO_GROUP%'",$g_periodo,$cod);
  $cod = str_replace("%IDMARCA%",       $IdMarca,$cod);
  $cod = str_replace("%CONDICIONVENTA%",$CondicionVenta,$cod);
  $cod = str_replace("%ESTADOOS%",$EstadoOS,$cod);
  $cod = str_replace("%PRIORIDAD%",$Prioridad,$cod);
  $cod = str_replace("%FACTURACION%",$Facturacion,$cod);
  $cod = str_replace("%ESTADOSUSCRIPCION%",$EstadoSuscripcion,$cod);
  $cod = str_replace("%TIPOSUSCRIPCION%",$TipoSuscripcion,$cod);
  $cod = str_replace("%TIPOPAGOSUSCRIPCION%",$TipoPagoSuscripcion,$cod);
  $cod = str_replace("%PROLONGACION%",$Prolongacion,$cod);
  $cod = str_replace("%IDCLIENTE%",$IdCLiente,$cod);
  $cod = str_replace("%CODIGO%",$Codigo,$cod);
  $cod = str_replace("'%IMPORTE%'",$EstadoPagoVenta,$cod);
  $cod = str_replace("%COBRANZA%",$Cobranza,$cod);
  $cod = str_replace("%SML%",$Moneda[1]['S'],$cod);

  if($esTPVOP)
    $cod = str_replace("%TIPOVENTA%",	$esTPVOP,$cod);

  return $cod;
}

?>