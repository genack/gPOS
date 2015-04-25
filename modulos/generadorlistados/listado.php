<?php

require("../../tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$LocalActual  = getSesionDato("IdTienda");

$tabla     = "ges_listados";
$IdListado = CleanID($_GET["id"]);
$sql       = "SELECT * FROM $tabla WHERE (IdListado='$IdListado')";
$row       = queryrow($sql);
$arr       = array();
$CodigoSQL = '';
$NombrePantalla = '';
if ( $row) {
  $CodigoSQL 	  = $row["CodigoSQL"];
  $NombrePantalla = $row["NombrePantalla"];
  $Categoria      = $row["Categoria"];	
  $CodigoSQL      = PostProcesarSQL($CodigoSQL,$LocalActual);
  $esnombreclient = strpos($CodigoSQL,'ges_clientes.NombreLegal');
  $esnombreclient1= strpos($CodigoSQL,'ges_clientes.NombreComercial');
  $nombrereplace  = ($esnombreclient || $esnombreclient1)? true:false;
}
//echo $CodigoSQL."--------------";
$genCabecera = "";
$genListado  = "";
$genListCol  = "";

// calcular tiempo de respuesta de la consulta
timequery();
$res = query( $CodigoSQL,"Listado: " . CleanText($NombrePantalla) ); //Sirve para extraer headers
timequery();

$maximoPorcentaje = 0;
$nombrePorcentaje = "";

$totales       = array();
$totalesNombre = array();

$ponerBotonImprimir = false;

if ($res){
  $row = Row($res);

  if ($row){
    $genCabecera = "<tr class='head'>\n";
    $genCabecera .= "\t<td class='headitem'>#</td>\n";
    $genTotales  = "<tr class='head'>\n";
    
    foreach ( $row as $key=>$value){
      if (!esNumero2($key)){
	$totales[$key] = 0;				
	$titulo = ReformateaTitulo($key);//Cosa__Euro --> Cosa

	$genCabecera .= "\t<td class='headitem' key='$key'>$titulo</td>\n";

	if ( esPorcentaje($key) ) {
	  $nombrePorcentaje = $key;//Requiere total al final 
	}	
	
	if ( esAutoSuma($key) ){
	  $genTotales .= "\t<td class='headitem' autosuma='1' key='$key' align='right'>%TOTAL:".$key."% </td>\n";
	  $totalesNombre[$key] = $key;//requiere computo de totales								
	} else if ( esPorcentaje($key) ){
	  $nombrePorcentaje = $key;//donde se colocara el total de porcentaje
	  $genTotales .= "\t<td class='headitem' porcentaje='1' key='$key'>%PORCENTAJE%</td>\n";
	}  else {
	  $genTotales .= "\t<td class='headitem'></td>\n";
	}
      }
    }
    $genCabecera .= "</tr>\n";
    $genTotales  .= "</tr>\n";
    
    if ($nombrePorcentaje){
      $res = query( $CodigoSQL);
      
      while( $row = Row($res)){
	$maximoPorcentaje = $maximoPorcentaje + $row[$nombrePorcentaje] * 1;
      }
      
      $res = query( $CodigoSQL);
    }
    
    $listado  = array();
    $ilistado = 0;
    
    $genListado .= Datos2Codigo($row,false,$nombrereplace);
    $listado[$ilistado]= $row;		
    
    $ilistado = $ilistado + 1;		

    $odd = 1;
    
    //Genera listado
    while( $row= Row($res)){
      $genListado .= Datos2Codigo($row,$odd%2,$nombrereplace);
      $odd = $odd + 1;
      $listado[$ilistado]= $row;
      $ilistado = $ilistado + 1;
      $ponerBotonImprimir = true;
    }

    //Completa autosumas
    foreach ($totalesNombre as $key=>$texto){
      $subformat = str_replace("AutoSuma","",SubKey($key));//Noseque__AutoSumaEuro -> AutoSumaEuro-> Euro			
      $genTotales = str_replace( "%TOTAL:".$texto."%", "<!-- key:$key, sub:$subformat -->TOTAL: " . SubFormateo($subformat,$totales[$key]), $genTotales  );	
    }

    //Completa porcentaje
    if ($nombrePorcentaje){
      $maximoPorcentaje = SubFormateo(str_replace("Porcentaje","",SubKey($nombrePorcentaje)),$maximoPorcentaje);
      $genTotales = str_replace( "%PORCENTAJE%", "<!-- nP:$nombrePorcentaje -->TOTAL: $maximoPorcentaje", $genTotales  );				
    }
  } else {
    $genCabecera="<tr class='head'>
		  <td class='headitem'>Resultado</td></tr>";
    
    $genListado = "<tr class='lineadatos'>
		   <td class='dato'> <div style='text-align:center;font-weight: bold;'>".
                  "El listado result&oacute; vac&iacute;o"."</div> </td></tr>";	
    $genTotales = "<tr class='head'>
		   <td class='headitem'></td></tr>";
    
  }
}

?>
<html>
  <head>
  <style type='text/css'>
  .headitem {
     background-color: #eee;
  }

  .lineadatos, .dato {
     border-bottom: 1px solid #eee;
   }

  td {
    font-size: 12px;
  }
  </style>
  <style type='text/css' media='print'>
    
  input {
    visibility: hidden;
  }

</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>

<?php if ($ponerBotonImprimir) { ?>

<row>
  <caption label="<?php echo _("Local"); ?>"/>
  <menulist  id="a_idlocal" oncommand="">                              
    <menupopup>
      <menuitem label="<?php echo _("Elije local") ?>" style="font-weight: bold"/>
      <?php echo genXulComboAlmacenes(false,"menuitem") ?>
    </menupopup>
  </menulist>
</row>

   <input type="button" value="Imprimir" onclick="imprimirLista()"/>
   <input type="button" value="Exportar CSV" onclick="exportarcsv()"/>

<!--div style="padding: 3px 0pt 2px;">
  <b><font  color="black" size="2">Exportar:</font></b> 

  <button style="margin-right: 5px;" onclick="window.print()">
    <div >
      <img style="vertical-align: middle; height:24px;" 
         src="../../img/gpos_pdf_ico.png">
    </div>
  </button> 
  <button style="margin-right: 5px;" onclick="exportarcsv()">
    <div >
      <img style="vertical-align: middle; height:24px;" 
         src="../../img/gpos_csv_ico.png">
    </div>
  </button> 
</div-->
<?php } ?>

<table style="background-color: #fefefe" width='100%'>
<?php
	echo $genCabecera;
	echo $genListado;
	echo $genTotales;
	
	echo "</table>";

?>
<script>

function imprimirLista(){
  var nombre = "<?php echo $NombrePantalla?>";
  var cat    = "<?php echo $Categoria?>";
  var desde  = "<?php echo $_GET["Desde"]?>";
  var hasta  = "<?php echo $_GET["Hasta"]?>";
  
  var f      = new Date();
  var hoy    = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
  nombre     = nombre.replace(" ","_");
  
  var fecha  = (!desde || !hasta)? hoy : desde+"_AL_"+hasta;
  var title  = cat+"_"+nombre+"_"+fecha;
  document.title = title.toUpperCase();
  window.print();
 }

function exportarcsv(){
   var TipoArchivo = "csv";
   var tabla       = "<?php echo $tabla ?>";
   var IdListado   = "<?php echo $IdListado?>";
   var desde       = "<?php echo $_GET["Desde"]?>";
   var hasta       = "<?php echo $_GET["Hasta"]?>";
   var local       = "<?php echo $_GET["IdLocal"]?>";
   var familia     = "<?php echo $_GET["IdFamilia"]?>";
   var subsidiario = "<?php echo $_GET["IdSubsidiario"]?>";
   var stsubsid    = "<?php echo $_GET["StatusTrabajoSubsidiario"]?>";
   var proveedor   = "<?php echo $_GET["IdProveedor"]?>";
   var usuario     = "<?php echo $_GET["IdUsuario"]?>";
   var referencia  = "<?php echo $_GET["Referencia"]?>";
   var cb          = "<?php echo $_GET["cb"]?>";
   var NumeroSerie = "<?php echo $_GET["ns"]?>";
   var Lote        = "<?php echo $_GET["lote"]?>";
   var Partida     = "<?php echo $_GET["partida"]?>";
   var DNICliente  = "<?php echo $_GET["dnicliente"]?>";
   var TipoVenta   = "<?php echo $_GET["tipoventaop"]?>";
   var esTPVOP     = "<?php echo $_GET["estpvop"]?>";
   var tipocomprob = "<?php echo $_GET["tipocomprobante"]?>";
   var seriecomprob= "<?php echo $_GET["seriecomprobante"]?>";
   var estadocomprob= "<?php echo $_GET["estadocomprobante"]?>";
   var estadopago  = "<?php echo $_GET["estadopago"]?>";
   var modalidad   = "<?php echo $_GET["modalidad"]?>";
   var estadopromo = "<?php echo $_GET["estadopromo"]?>";
   var tipopromo   = "<?php echo $_GET["tipopromo"]?>";
   var tipooperacion= "<?php echo $_GET["tipooperacion"]?>";
   var tipoopcjagral= "<?php echo $_GET["tipoopcjagral"]?>";
   var periodoventa= "<?php echo $_GET["periodoventa"]?>";
   var nombrecliente= "<?php echo $_GET["nombrecliente"]?>";
   var tipocliente= "<?php echo $_GET["tipocliente"]?>";
   var idmarca= "<?php echo $_GET["idmarca"]?>";
   var LocalActual = "<?php echo $LocalActual?>";
   var CondVenta = "<?php echo $_GET["condicionventa"]?>";
   var EstadoOS  = "<?php echo $_GET["estadoos"]?>";
   var Prioridad = "<?php echo $_GET["prioridad"]?>";
   var Facturacion = "<?php echo $_GET["facturacion"]?>";
   var EstadoSuscripcion = "<?php echo $_GET["estadosucripcion"]?>";
   var TipoSuscripcion = "<?php echo $_GET["tiposuscripcion"]?>";
   var TipoPagoSuscripcion = "<?php echo $_GET["tipopagosuscripcion"]?>";
   var Prolongacion = "<?php echo $_GET["prolongacion"]?>";
   var IdCliente = "<?php echo $_GET["idcliente"]?>";
   var Codigo    = "<?php echo $_GET["codigo"]?>";
   var EstadoPagoVenta = "<?php echo $_GET["estadopagoventa"]?>";
   var Cobranza = "<?php echo $_GET["cobranza"]?>";
   var CodigoComprobante = "<?php echo $_GET["codcomprobante"]?>";

   var url  = 
     "exportarlistados.php?modo=ExportarDirectoCSV"+
     "&xfile="+TipoArchivo+
     "&xtab="+tabla+
     "&xidl="+IdListado+
     "&desde="+desde+
     "&hasta="+hasta+
     "&local="+local+
     "&familia="+familia+
     "&subsidiario="+subsidiario+
     "&stsubsid="+stsubsid+
     "&proveedor="+proveedor+
     "&usuario="+usuario+
     "&referencia="+referencia+
     "&ns="+NumeroSerie+
     "&lote="+Lote+
     "&partida="+Partida+
     "&tipoventa="+TipoVenta+
     "&estpvop="+esTPVOP+
     "&localactual="+LocalActual+
     "&dnicliente="+DNICliente+
     "&tipocomprobante="+tipocomprob+
     "&seriecomprobante="+seriecomprob+
     "&estadocomprobante="+estadocomprob+
     "&estadopago="+estadopago+
     "&modalidad="+modalidad+
     "&estadopromo="+estadopromo+
     "&tipopromo="+tipopromo+
     "&tipooperacion="+tipooperacion+
     "&tipoopcjagral="+tipoopcjagral+
     "&periodoventa="+periodoventa+
     "&nombrecliente="+nombrecliente+
     "&tipocliente="+tipocliente+
     "&idmarca="+idmarca+
     "&condicionventa="+CondVenta+
     "&estadoos="+EstadoOS+
     "&prioridad="+Prioridad+
     "&facturacion="+Facturacion+
     "&estadosucripcion="+EstadoSuscripcion+
     "&tiposuscripcion="+TipoSuscripcion+
     "&tipopagosuscripcion="+TipoPagoSuscripcion+
     "&prolongacion="+Prolongacion+
     "&idcliente="+IdCliente+
     "&codigo="+Codigo+
     "&estadopagoventa="+EstadoPagoVenta+
     "&cobranza="+Cobranza+
     "&codcomprobante="+CodigoComprobante+
     "&cb="+cb;

   document.location=url;
}
 
function RecargarPagina(){
  document.location='<?php echo $action . "?id=" . $_GET["id"] ?>';
}

<?php

echo "</script></body></html>";

// 
///////////////////////////////////////////////////////////////////////////////////
//  Funciones auxiliares

// Funcion que caulcula tiempo de respuesta de la consulta
function timequery(){
   static $querytime_begin;
   global $tr;
   list($usec, $sec) = explode(' ',microtime());
    
       if(!isset($querytime_begin))
      {   
         $querytime_begin= ((float)$usec + (float)$sec);
      }
      else
      {
         $querytime = (((float)$usec + (float)$sec)) - $querytime_begin;
	 $tr =  sprintf('%01.5f', $querytime);
         //echo sprintf('La consulta tardó %01.5f segundos. <br />', $querytime);
      }
}

function SubKey($clave){
  $variable = explode("__",$clave);
  $num	= count($variable);
  if ($num<2) {
    return false;
  }
  $modoformato = $variable[1];	
  return $modoformato;	
}

function AutoformatoSQL( $clave, $valor){
  global $maximoPorcentaje;
  $modoformato = SubKey($clave);

  if (!$modoformato) {
    //return htmlentities($valor) . "<!-- no formato -->";
    return htmlentities($valor,ENT_QUOTES,'UTF-8');
  }
  
  switch( $modoformato ){
    //Devuelven html
  case "AutoSumaPorcentaje":
  case "Porcentaje":
  case "ModUserButton":
  case "decode64":			
  case "FechaHora":
  case "Fecha":
  case "DiaSemana":
    $val = subFormateo($modoformato,$valor);
    return $val;
  //Devuelven un valor que se puede formatear en html			
  default:
    $val = subFormateo($modoformato,$valor);
    $val = CleanParaWeb($val);
    return "<div style='align:right;float:right'>$val</div>";
  }

  //return htmlentities($valor);
  return htmlentities($valor,ENT_QUOTES,'UTF-8');
}

function SubFormateo($modoformato,$valor){
  global $maximoPorcentaje;
  
  $val = $valor . "<!-- formato desconocido: $modoformato -->";
  $Moneda      = getSesionDato("Moneda");
  $modoformato = str_replace("__","",$modoformato);
  $submodo     = str_replace("AutoSuma","",$modoformato);

  switch($submodo){
  case "Entero":
    $val = intval($valor);
    break;
  default:
    $val = $valor;
    break;
  case "Dec2":
    $val = sprintf("%01.2f", $valor);
    break;
  case "Moneda":
  case "Euro":
    $val = $Moneda[1]['S'] . sprintf("%01.2f", $valor)  ;
  break;					
  case "Porcentaje":
    $val = ((($valor*1)/$maximoPorcentaje)*100);
    $val = (intval($val*100))/100;//recorta a solo dos digitos de precision
    return GenCol($val);		
  case "decode64":
    //error(0,"Info: decode base64");
    //$valor = str_replace("'","&#39;", base64_decode($valor));				
    $val = base64_decode($valor);				
    break;
  case "FechaHora":
    $fechahora = explode(" ",$valor);
    $val = $fechahora[1] . " " . $fechahora[0];
    break;					
  case "ModUserButton":
    $val = "<input type='button'value='Modificar' onclick='cmdPadre(\"IdUsuario\",".$valor.")'/>";
    return $valor;
    //GenCol
    
  case "Tarta":
    //$val = ((($valor*1)/$maximoPorcentaje)*100);
    //$val = (intval($val*100))/100;//recorta a solo dos digitos de precision
    //return GenCol($val);					
    break;
  case "Porcentaje":
    $val = ((($valor*1)/$maximoPorcentaje)*100);
    $val = (intval($val*100))/100;//recorta a solo dos digitos de precision
    return GenCol($val);		
  case "Fecha":
    $val = CleanFechaFromDB($valor);
    break;
  case "DiaSemana":
    $val = NumDia2DiaES($valor);
    break;
  case "Mes":
    $val = NumMes2MesES($valor);
    break;	
  }
  
  return $val;	
}

function ReformateaTitulo( $titulo ){
  $variable = explode("__",$titulo);
  $num	= count($variable);
  if($num<2){
    return $titulo;
  }
  return $variable[0];
}

function Datos2Codigo( $datos,$par=false,$nombrereplace ){
  global $totalesNombre,$totales;
  global $ilistado;
  $count =   $ilistado+1;
  if(!$datos or !is_array($datos))
    return;

  $out ="\n<tr class='lineadatos'>";		
  $out .= "\t<td class='headitem'>$count.</td>\n";    
  foreach ($datos as $key=>$value){
    if($nombrereplace)
      $value = str_replace('&#038;','&',$value);
    if (isset($totalesNombre[$key])){
      $original = $totales[$key];
      $suma = $original + $value;
      $totales[$key] = $suma;
      //echo "<p>org:$original, suma:$suma, value:$value";
    } else {
      //echo "<p>$key no esta en totalesNombre";	
    }

    if (!esNumero2($key)) {
      $value = AutoformatoSQL($key,$value);

      $out .= "\t<td class='dato'>$value &nbsp;</td>\n";
    }
  }

  return $out . "</tr>\n";

}

function esAutoSuma($key){ //contiene __AutoSuma
  return strpos($key,"__AutoSuma")>0;	
}

function esPorcentaje($key){	
  if (strpos($key,"__AutoSumaPorcentaje")>0) return true;
  return strpos($key,"__Porcentaje")>0; 
}


function esNumero2($cadena) {
  if ($cadena == "0")
    return true;
  
  return (( $cadena * 1 ) >0);
}

function GenCol($cent,$ponTex=false,$color='green'){
  //$centOpac = intval(($cent+100)/2);
  $centOpac = (($cent+100)/2)/100;
  $pix = "green_h.gif";
  //pixpaint2.gif
  return "<img src='$pix' style='width: ".$cent."%; height: 16px; -moz-opacity: ".$centOpac.";'/> " . $cent . "%";
}


function NumDia2DiaES($dia){
  $dia = intval($dia);
  //$dias = array('Monday', 'Tuesday', 'Wednesday','Thursday', 'Friday', 'Saturday','Sunday');
  $dias = array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
  
  return $dias[$dia];
}

function NumMes2MesES($mes){
  $meses = array('','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
  
  return $meses[$mes];
}

function RecortaPrecision($flotante){
  $flotante = sprintf("%01.2f", $flotante);
  return $flotante;
}											


function getProdBaseFromCB($cb){
  if (!$cb)
    return false;
  
  $cb_s = CleanCB($cb);
  $sql = "SELECT  IdProdBase FROM `ges_productos` WHERE CodigoBarras='$cb_s'";
  $row = queryrow($sql);
  return $row["IdProdBase"];
}

function PostProcesarSQL( $cod,$LocalActual ) {
 
  $Moneda = getSesionDato("Moneda");
  
  if( function_exists("getSesionDato"))
    $IdLang = getSesionDato("IdLenguajeDefecto");
  
  if (!$IdLang)
    $IdLang = 1;
  
  if (isset($_GET["cb"])) {
    $cod = str_replace("%IDPRODBASEDESDECB%",getProdBaseFromCB($_GET["cb"]),$cod);
    $cod = str_replace("%CODIGOBARRAS%",CleanRealMysql($_GET["cb"]),$cod);
  }

  $estadopagoventa = str_replace('%%',"'%%'",CleanText($_GET["estadopagoventa"]));

  $periodoventa = CleanText($_GET["periodoventa"]);
  if($periodoventa == 'DAY')
    $g_periodo = "$periodoventa(FechaComprobante)";
  if($periodoventa == 'WEEK')
    $g_periodo = "$periodoventa(FechaComprobante)";
  if($periodoventa == 'MONTH')
    $g_periodo = "$periodoventa(FechaComprobante)";
  if($periodoventa == 'YEAR')
    $g_periodo = "$periodoventa(FechaComprobante)";

  $TipoVenta = getSesionDato("TipoVentaTPV");
  $Precio    = ($TipoVenta == 'VD')? 'PrecioVenta':'PrecioVentaCorporativo';

  $cod = str_replace("%IDIDIOMA%",$IdLang,$cod);
  $cod = str_replace("%DESDE%",		CleanCadena($_GET["Desde"]),$cod);
  $cod = str_replace("%HASTA%",		CleanCadena($_GET["Hasta"]),$cod);
  $cod = str_replace("%IDTIENDA%",	CleanText($_GET["IdLocal"]),$cod);
  $cod = str_replace("%IDFAMILIA%",	CleanText($_GET["IdFamilia"]),$cod);	
  $cod = str_replace("%IDSUBSIDIARIO%",	CleanID($_GET["IdSubsidiario"]),$cod);
  $cod = str_replace("%STATUSTBJOSUBSIDIARIO%",	CleanText($_GET["StatusTrabajoSubsidiario"]),$cod);
  $cod = str_replace("%IDPROVEEDOR%",	CleanText($_GET["IdProveedor"]),$cod);
  $cod = str_replace("%IDUSUARIO%",	CleanText($_GET["IdUsuario"]),$cod);
  $cod = str_replace("%REFERENCIA%",	CleanRealMysql($_GET["Referencia"]),$cod);
  $cod = str_replace("%NUMEROSERIE%",	CleanText($_GET["ns"]),$cod);
  $cod = str_replace("%LOTE%",	        CleanText($_GET["lote"]),$cod);
  $cod = str_replace("%PARTIDA%",	CleanID($_GET["partida"]),$cod);
  $cod = str_replace("%TIPOVENTAOP%",	CleanText($_GET["tipoventaop"]),$cod);
  $cod = str_replace("%IDLOCAL%",	$LocalActual,$cod);
  $cod = str_replace("%DNICLIENTE%",	CleanText($_GET["dnicliente"]),$cod);
  $cod = str_replace("%TIPOCOMPROBANTE%",	CleanText($_GET["tipocomprobante"]),$cod);
  $cod = str_replace("%SERIECOMPROBANTE%",	CleanText($_GET["seriecomprobante"]),$cod);
  $cod = str_replace("%ESTADOCOMPROBANTE%",	CleanText($_GET["estadocomprobante"]),$cod);
  $cod = str_replace("%ESTADOPAGO%",	CleanText($_GET["estadopago"]),$cod);
  $cod = str_replace("%MODALIDAD%",	CleanText($_GET["modalidad"]),$cod);
  $cod = str_replace("%ESTADOPROMO%",	CleanText($_GET["estadopromo"]),$cod);
  $cod = str_replace("%TIPOPROMO%",	CleanText($_GET["tipopromo"]),$cod);
  $cod = str_replace("%TIPOOPERACION%",	CleanText($_GET["tipooperacion"]),$cod);
  $cod = str_replace("%TIPOOPCJAGRAL%",CleanText($_GET["tipoopcjagral"]),$cod);
  $cod = str_replace("%PERIODOVENTA%",CleanText($_GET["periodoventa"]),$cod);
  $cod = str_replace("%CLIENTE%",CleanText($_GET["nombrecliente"]),$cod);
  $cod = str_replace("%TIPOCLIENTE%",CleanText($_GET["tipocliente"]),$cod);
  $cod = str_replace("%IDMARCA%",CleanText($_GET["idmarca"]),$cod);
  $cod = str_replace("%CONDICIONVENTA%",CleanText($_GET["condicionventa"]),$cod);
  $cod = str_replace("%ESTADOOS%",CleanText($_GET["estadoos"]),$cod);
  $cod = str_replace("%PRIORIDAD%",CleanText($_GET["prioridad"]),$cod);
  $cod = str_replace("%FACTURACION%",CleanText($_GET["facturacion"]),$cod);
  $cod = str_replace("%ESTADOSUSCRIPCION%",CleanText($_GET["estadosucripcion"]),$cod);
  $cod = str_replace("%TIPOSUSCRIPCION%",CleanText($_GET["tiposuscripcion"]),$cod);
  $cod = str_replace("%TIPOPAGOSUSCRIPCION%",CleanText($_GET["tipopagosuscripcion"]),$cod);
  $cod = str_replace("%PROLONGACION%",CleanText($_GET["prolongacion"]),$cod);
  $cod = str_replace("%IDCLIENTE%",CleanText($_GET["idcliente"]),$cod);
  $cod = str_replace("%CODIGO%",CleanText($_GET["codigo"]),$cod);
  $cod = str_replace("%COBRANZA%",CleanText($_GET["cobranza"]),$cod);
  $cod = str_replace("%CODIGOCOMPROBANTE%",CleanText($_GET["codcomprobante"]),$cod);
  $cod = str_replace("'%IMPORTE%'",$estadopagoventa,$cod);
  $cod = str_replace("'%PERIODO_GROUP%'",$g_periodo,$cod);
  $cod = str_replace("%SML%",$Moneda[1]['S'],$cod);
  $cod = str_replace("'%TIPOVENTA%'",$Precio,$cod);

  return $cod;
}

?>
