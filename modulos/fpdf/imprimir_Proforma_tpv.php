<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal      = getSesionDato("IdTiendaDependiente");
$Moneda    = getSesionDato("Moneda");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$nroProforma     = $_GET["nroProforma"];
//$nroSerie      = $_GET["nroSerie"];
$codcliente    = $_GET["codcliente"];
$totaletras    = $_GET["totaletras"];
$IdPresupuesto = $_GET["idcomprobante"];
$operador      = (isset($_GET["nombreusuario"]))? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$IGV	       = getSesionDato("IGV");
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;
 
//Cliente
if ($codcliente==0){
  $nombre    = "";
  $nif       = "";
  $direccion = "";
}else{
  $sql =
    "SELECT NombreComercial,".
    "       NumeroFiscal, ".
    "       Direccion ".
    "FROM   ges_clientes ".
    "WHERE  IdCliente='".$codcliente."'";
  $res       = query($sql);
  $row       = Row($res);
  $nombre    = utf8_decode($row["NombreComercial"]);
  $nif       = utf8_decode($row["NumeroFiscal"]);
  $direccion = utf8_decode($row["Direccion"]);
}

//Presupuesto
$sql =
  "Select *  ".
  "from ".
  "       ges_presupuestos ".
  "where  IdPresupuesto = '".$IdPresupuesto."' ".
  "and    IdLocal       = '".$IdLocal."' ".
  "and    Eliminado     = '0'";
$res    = query($sql);
$lafila = Row($res);

$TipoTPV = $lafila["TipoVentaOperacion"];

//FECHA
list($anho,$mes,$dia)=explode('-',$lafila["FechaPresupuesto"]);
list($dia,$hora)=explode(' ',$dia);
$mes = getMesFromId($mes);

//NROSERIE
$nroSerie = $lafila["Serie"];

//PDF ESTRUCTURA
$pdf=new PDF();
$pdf->Open();
$pdf->AddPage();

$pdf->Ln(28);

//PROFORMA
$pdf->SetX(86);
$pdf->SetFont('Courier','BU',16);	
$pdf->Cell(125,4,utf8_decode('PROFORMA Nº').$nroProforma );

$pdf->Ln(12);

//FECHA
$pdf->SetX(134);
$pdf->SetFont('Courier','',11);	
$pdf->Cell(130,4,$poblacion.", ".$dia." de ".$mes." ".$anho);
$pdf->Ln(8);
//." (".$hora.")"
// NOMBRE   
$pdf->SetX(18); 
$pdf->SetFont('Courier','B',11);	
$pdf->Cell(120,4,utf8_decode('Saludos: '));

$pdf->Ln(4);
$pdf->SetX(18); 
$pdf->SetFont('Courier','',11);	
$pdf->Cell(120,4,$nombre);

/*
//Presente
$pdf->Ln(6);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',11);	
$pdf->Cell(18,4,utf8_decode('Presente'));
$pdf->SetFont('Courier','B',11);	
$pdf->Cell(1,4,'.');
*/
//Argumento
$pdf->Ln(6);
$presente = "Mediante la presente es muy grato atenderle a nombre de nuestra empresa ".
            " $nombrelegal ".
            " y hacerle alcance detallado de nuestra propuesta:";

$cont = 1;
$pres = array();
$pres = getItemProducto($presente,75);

$pdf->SetX(16); 
$pdf->Cell(200,4,utf8_decode($pres[0]));
$pdf->Ln(4);

foreach ($pres as $key=>$line){
  if($key>0 && $key < 80 ){
    $pdf->SetX(16); 
    $pdf->Cell(200,4,utf8_decode($line));
    $pdf->Ln(4);
    $cont++;
    $presext = 0;
  }
}

//$igv=$lafila["IGV"]; 
//$igv=0;

$pdf->Ln(4);

// las lneas delos ARTICULOS 
$pdf->SetX(17); 
$pdf->Cell(1);

$pdf->SetFillColor(210,210,210);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',8);
	

$pdf->Cell(10,4,"Cant.",1,0,'C',1);	
//$pdf->Cell(15,4,"Unid.",1,0,'C',1);
$pdf->Cell(110,4,utf8_decode("Descripción"),1,0,'D',1);
$pdf->Cell(20,4,"Precio(".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(15,4,"Dsto(".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(25,4,"Importe(".$Moneda[1]['S'].")",1,0,'C',1);


$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

//$pdf->Cell(1);
//$pdf->Cell(20,4,"",'LR',0,'C');
//$pdf->Cell(125,4,"",'LR',0,'C');	
//$pdf->Cell(20,4,"",'LR',0,'C');
//$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(10,4,"",'LR',0,'C');
$pdf->Cell(110,4,"",'LR',0,'C');	
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(15,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(2);	



$IdPresupuesto=$lafila["IdPresupuesto"];
$contador=1;
$totalbruto=0;
$totalneto=0;
$totalbrutounid=0;
$totaldescuento=0; 
$sql = 
  " SELECT ges_presupuestosdet.*, ".
 	"        ges_presupuestosdet.CodigoBarras, ".
	"        ges_productos.MetaProducto, ".
	"        ges_productos.Serie, ".
	"        ges_productos.IdLabHab, ".
	"        ges_productos.IdProducto as codarticulo, ".
	"        ges_presupuestosdet.Referencia as referencia, ".
	"        ges_productos_idioma.Descripcion as descripcion, ".
	"        ges_marcas.Marca,ges_colores.Color as presentacion, ".
	"        ges_tallas.Talla as subpresentacion , ".
	"        ges_laboratorios.NombreComercial as laboratorio,".
	"        ges_productos.IdContenedor, ".
	"        ges_productos.UnidadesPorContenedor, ".
	"        ges_productos.UnidadMedida ".
	"FROM    ges_presupuestosdet,ges_productos,ges_productos_idioma, ".
	"        ges_tallas,ges_colores,ges_marcas,ges_laboratorios ".
	"WHERE   ges_presupuestosdet.IdPresupuesto = '".$IdPresupuesto."' ".
	"AND     ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	"AND     ges_presupuestosdet.IdProducto = ges_productos.IdProducto ".
	"AND     ges_productos.IdColor = ges_colores.IdColor  ".
	"AND     ges_productos.IdTalla = ges_tallas.IdTalla ".
	"AND     ges_marcas.IdMarca = ges_productos.IdMarca  ".
	"AND     ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	"AND     ges_presupuestosdet.Eliminado = '0' ".
	"ORDER BY ges_presupuestosdet.IdPresupuestoDet ASC"; 

        $res = query($sql);
        while ( $row = Row($res) ) { 
	  $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $codarticulo = $row["IdProducto"];

	  // IMPRIME LINE
	  $cantidad=$row["Cantidad"];
	  //UNID MEDIDA
          $cantidadunid = $row["UnidadMedida"];

	  //IMPRIME CANTIDAD
	  $pdf->SetFont('Courier','B',8);
	  $pdf->Cell(10,4,$cantidad,'LR',0,'C');	
	  $pdf->SetFont('Courier','',9);
	  //CADENA TEXT DESCRIPCION 

	  //### TEXT DESCRIPCION
          $codigobarras = $row["CodigoBarras"];
          $descripcion  = utf8_decode($row["descripcion"]);
	  $marca        = utf8_decode($row["Marca"]);
	  $modelo       = utf8_decode($row["presentacion"]);
	  $detalle      = utf8_decode($row["subpresentacion"]);
	  $laboratorio  = utf8_decode($row["laboratorio"]);

	  if($marca=="...")
	     $marca="";
	  if($modelo=="...")
	     $modelo="";
	  if($detalle=="...")
	     $detalle="";
	  if($laboratorio==".")
	    $laboratorio="";

	  //SERIES
	  $seriestext = '';
	  $series     = array();
	  $a_cbmtpd   = array();
	  $noSeries   = false;
	  if($row["Serie"]==1){

	    //cb de meta productos elegidos para el cliente
	    $cbmtpd = $lafila["CBMetaProducto"];
	    
	    //cb de meta producto default, si es necesario
	    if($row["MetaProducto"]==1 && $cbmtpd == '')
	      {
		$a_cbmtpd = explode(";",getDetBaseMProducto($IdLocal,$codarticulo));
		$cbmtpd   = $a_cbmtpd[1];
		$noSeries = true;
	      }

	    $series =  getNSFromCBMPPresupuesto($IdLocal,$codarticulo,$cbmtpd);
	    //$series = explode(",",$series); 
	    if(!$noSeries)
	      $seriestext = "N/S: ".implode($series," ");
	    
	  }

	  //META PRODUCTO
	  $itemmprod = array();
	  $acotmp    = array();
	  $acotmp    = getItemMetaProducto($row["MetaProducto"],$row["Serie"],$series,$codarticulo,64);

	  $descripcion_0 =
	    $codigobarras." ".
	    $descripcion." ".
	    $marca." ".
	    $modelo." ".
	    $detalle." ".
	    $laboratorio." ".
	    $seriestext;

	  //SERVICIO
	  if($row["Concepto"]!=''){
	    $descripcion_0  = $codigobarras." ".utf8_decode($row["Concepto"]);
	    $acotmp = array();
	  }


	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($descripcion_0,55);

	  //PRECIO
	  $precio=$row["Precio"];
	  $precio=round($precio * 100) / 100; 	  
	  $totalbruto=$totalbruto+($precio*$cantidad);
	  $precio=number_format($precio,2);	  
	  //DESCUENTO
	  $dcto="";
	  if($row["Descuento"] !=0)
	    $dcto = $row["Descuento"];
	  //$totaldcto=$totaldcto+$dcto;
	  //IMPORTE
	  $importe=$row["Importe"];
	  //BRUTO NETO
	  $totalneto=$totalneto+$importe;
	  //$importe=$importe+$importe*$igv/100;
 	  $importe=round($importe * 100) / 100; 
	  $importe=number_format($importe,2);

	  // IMPRIME LINE
	  $pdf->Cell(110,4,utf8_decode($acotado[0]),'LR',0,'L');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(20,4,$precio,'LR',0,'R');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(15,4,$dcto,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(25,4,$importe,'LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  $pdf->SetFont('Courier','',9);
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(10,4,"",'LR',0,'C');
	      $pdf->Cell(110,4,utf8_decode($line),'LR',0,'L');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(15,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }

	  //TEXT META PRODUCTO
	  foreach ($acotmp  as $key=>$linemp){
	    if( $key < 20 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(10,4,"",'LR',0,'C');
	      $pdf->Cell(110,4,utf8_decode($linemp),'LR',0,'L');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(15,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }

	  //CONTADOR
	  $contador++;

	};
	
	while ($contador<2)
	{
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(10,4,"",'LR',0,'C');
	  $pdf->Cell(110,4,"",'LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(15,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

          // LINEA 3
	  $pdf->Ln(-3);	
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(10,4,"",'LRB',0,'C');
 	  $pdf->Cell(110,4,"",'LRB',0,'C');	
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(15,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
          $pdf->Ln(4);	
          //$pdf->Cell(1);

//  IMPORTE FINAL DESGLOZADO	
// TOTAL NETO
$totalneto=round($totalneto * 100) / 100; 
$totalneto=number_format($totalneto,2);	

$fecha   = implota($fechahoy=date("Y-m-d"));
$hora    = date("H:i");
$codlist = $lafila["NPresupuesto"];
$TipoTPV = ($TipoTPV == 'VD')? 'B2C':'B2B';
$mensaje = 
  "** ".$TipoTPV.
  " ** ".$operador.
  " ** ".$fecha." ".$hora.
  " **".$IdLocal."-".$nroSerie."-".$nroProforma."**";
$pdf->SetX(27);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);


// HALLAMOS TOTALES
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200,200,200);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',9);
$pdf->SetX(157);		
$pdf->Cell(1);
$pdf->Cell(15,4,"Total",1,0,'R',1);
$pdf->Cell(25,4,$totalneto,1,0,'R',1);
$pdf->Ln(8);

//IMPORTE LETRAS
$pdf->SetFont('Courier','B',10);	
$pdf->SetX(17);	
$pdf->Cell(300,4,"SON:".utf8_decode($totaletras));

//Obervaciones
if($lafila["Observaciones"]!==''){
  $pdf->Ln(8);
  $pdf->SetX(17); 
  $pdf->SetFont('Courier','UB',11);	
  $pdf->Cell(29.5,4,utf8_decode('Observaciones'));
  $pdf->SetFont('Courier','',11);	
  $pdf->Cell(1,4,'.');
  $pdf->Ln(6);

  $obsev = explode("-", $lafila["Observaciones"]);

  foreach ($obsev  as $key=>$line){
    if( $key > 0 ){
      $pdf->SetX(17); 
      $pdf->Cell(1);
      $pdf->Cell(300,4,utf8_decode("-".$line));
      $pdf->Ln(4);
    }
  }
}

//Condiciones Generales
$garantiacomercial = getSesionDato("GarantiaComercial");
$pdf->Ln(6);
$pdf->SetX(17); 
$pdf->SetFont('Courier','UB',11);	
$pdf->Cell(48,4,utf8_decode('Condiciones Generales'));
$pdf->SetFont('Courier','',11);	
$pdf->Cell(1,4,'.');
$pdf->Ln(6);
//IGV
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(300,4,utf8_decode("- Los precios incluye IGV(".$IGV."%)"));
$pdf->Ln(4);
//Vigencia
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(300,4,utf8_decode("- Vigencia de oferta ".$lafila["VigenciaPresupuesto"]." día(s)."));
$pdf->Ln(4);
//Garantia Comercial
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(300,4,utf8_decode("- Garantía comercial ".$garantiacomercial." meses."));
$pdf->Ln(4);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$name = "PROFORMA-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroProforma.".pdf";

$pdf->Output($name,'');
?> 
