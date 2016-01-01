<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunesexp.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal   = getSesionDato("IdTienda");
$Moneda    = getSesionDato("Moneda");

setlocale(LC_ALL,"es_ES");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$xidlocal      = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):$IdLocal;
$IdLocal       = $xidlocal;
$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

$idarqueo      = CleanID($_GET["idarqueo"]);
$TipoVenta     = getSesionDato("TipoVentaTPV");

$sql = "SELECT IdArqueo, ges_arqueo_caja.IdLocal, TipoVentaOperacion, ".
       "DATE_FORMAT(FechaApertura,'%d/%m/%Y %H:%i') as FechaApertura, ".
       "IF(DATE(FechaCierre) = '0000-00-00', ' ',DATE_FORMAT(FechaCierre,'%d/%m/%Y %H:%i')) as FechaCierre, esCerrada, ".
       "ImporteApertura, ImporteIngresos, ImporteGastos, ".
       "ImporteAportaciones, ImporteSustracciones, ImporteTeoricoCierre, ".
       "ImporteCierre, ImporteDescuadre, UtilidadVenta, ".
       "ges_locales.NombreComercial as Local ".
       "FROM ges_arqueo_caja ".
       "INNER JOIN ges_locales ON ges_arqueo_caja.IdLocal = ges_locales.IdLocal ".
       "WHERE IdArqueo='$idarqueo' ";

$res  = query($sql);
$row1 = Row($res);

//PDF ESTRUCTURA
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->AddPage();


$pdf->SetX(10);
$pdf->SetFont('Courier','B',13);	
$pdf->Cell(0,4,utf8_decode("ARQUEO CAJA - ".$row1["IdArqueo"]),0,0,'C' );

$pdf->Ln(12);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Local         :"));

$pdf->SetX(50);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode($row1["Local"]));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,"Fecha Apertura:" );

$pdf->SetX(50);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,utf8_decode($row1["FechaApertura"]));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Fecha Cierre  :"));

$pdf->SetX(50);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,$row1["FechaCierre"]);

//Detalle
$pdf->Ln(8);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(18,4,utf8_decode('Movimientos'));
$pdf->SetX(41); 
$pdf->SetFont('Courier','B',10);	
$pdf->Cell(1,4,':');

$pdf->Ln(6);


// las lineas de los productos
$pdf->SetX(17); 
$pdf->Cell(1);

$pdf->SetFillColor(210,210,210);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',8);
	

$pdf->Cell(6,4,"#",1,0,'C',1);
$pdf->Cell(30,4,utf8_decode("Fecha"),1,0,'D',1);
$pdf->Cell(60,4,utf8_decode("Operación"),1,0,'C',1);
$pdf->Cell(45,4,"Cliente",1,0,'C',1);
$pdf->Cell(70,4,"Concepto",1,0,'C',1);
$pdf->Cell(20,4,"Importe (".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(28,4,"Usuario",1,0,'C',1);


$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LR',0,'C');
$pdf->Cell(30,4,"",'LR',0,'c');
$pdf->Cell(60,4,"",'LR',0,'C');
$pdf->Cell(45,4,"",'LR',0,'C');
$pdf->Cell(70,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(28,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;

$sql =
	  " SELECT IdOperacionCaja, Identificacion, (IF(ges_dinero_movimientos.IdPartidaCaja <>0,(SELECT ges_partidascaja.PartidaCaja FROM ges_partidascaja WHERE ges_partidascaja.IdPartidaCaja = ges_dinero_movimientos.IdPartidaCaja AND ges_partidascaja.TipoCaja = '$TipoVenta'),'Venta')) as PartidaCaja, IdArqueoCaja, ".
          " ges_dinero_movimientos.TipoOperacion, ".
	  " TipoVentaOperacion, FechaCaja, Concepto, Importe, ".
	  " IdModalidadPago, DATE_FORMAT(FechaPago,'%d/%m/%y %H:%i') as FechaPago, ".
          " IdComprobante ".
	  " FROM   ges_dinero_movimientos ".
	  " INNER JOIN ges_usuarios ON ".
	  " ges_dinero_movimientos.IdUsuario = ges_usuarios.IdUsuario ".
	  " WHERE  IdArqueoCaja       = '$idarqueo' ".
	  " AND    IdModalidadPago    = 1 ".
	  " AND    TipoVentaOperacion = '$TipoVenta' ".
	  " AND    ges_dinero_movimientos.Eliminado = 0 ".
	  " ORDER  BY IdOperacionCaja DESC";

$res   = query($sql);
$item  = 1;

while($row= Row($res)) {
  $Cliente = ($row["IdComprobante"] > 0)? obtenerClientexComprobante($row["IdComprobante"]):"";

  $Fecha    = $row["FechaPago"];
  $Concepto = $row["Concepto"];
  $Importe  = $row["Importe"];
  $Usuario  = $row["Identificacion"];
  $TipoOperacion = $row["TipoOperacion"];
  $PartidaCaja   = $row["PartidaCaja"];


  $pdf->SetX(17); 
  $pdf->Cell(1);

  //PRODUCTO ITEM
  $acotado = array();
  $acotado = getItemProducto($Concepto,35);

  $client = Array();
  $client = ($Cliente)? getItemProducto($Cliente,25):"";
  $client[0] = ($Cliente)? $client[0]:" ";
  // IMPRIME LINE
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(6,4,$item,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(30,4,utf8_decode($Fecha),'LR',0,'L');
  $pdf->SetFont('Courier','',8);
  $pdf->Cell(60,4,utf8_decode($TipoOperacion." - ".$PartidaCaja),0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(45,4,utf8_decode($client[0]),'LR',0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(70,4,utf8_decode($acotado[0]), 'LR',0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(20,4,number_format($Importe,2),'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(28,4,$Usuario,'LR',0,'R');
  $pdf->Ln(4);	

  //TEXT EXTRA LINE run
  $pdf->SetFont('Courier','',8);
  foreach ($acotado as $key=>$line){
    if($key>0 && $key < 27 ){
      $pdf->SetX(17); 
      $pdf->Cell(1);
      $pdf->Cell(6,4,'','LR',0,'R');
      $pdf->Cell(30,4,'','LR',0,'R');
      $pdf->Cell(60,4,"",'LR',0,'L');
      $pdf->Cell(45,4,"",'LR',0,'L');
      $pdf->Cell(70,4,utf8_decode($line),'LR',0,'L');
      $pdf->Cell(20,4,"",'LR',0,'R');
      $pdf->Cell(28,4,"",'LR',0,'R');
      $pdf->Ln(4);

      $contador++;
      $acotadoext = 0;
    }
  }


  //CONTADOR
  $contador++;
  $item++;
	  
};
	
while ($contador<2)
  {
    $pdf->SetX(17); 
    $pdf->Cell(1);
    $pdf->Cell(6,4,"",'LR',0,'C');
    $pdf->Cell(30,4,"",'LR',0,'c');
    $pdf->Cell(60,4,"",'LR',0,'C');
    $pdf->Cell(45,4,"",'LR',0,'C');
    $pdf->Cell(70,4,"",'LR',0,'C');
    $pdf->Cell(20,4,"",'LR',0,'C');
    $pdf->Cell(28,4,"",'LR',0,'C');

    $pdf->Ln(4);	
    $contador=$contador +1;
  }

// LINEA 3
$pdf->Ln(-3);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LRB',0,'C');
$pdf->Cell(30,4,"",'LRB',0,'c');
$pdf->Cell(60,4,"",'LRB',0,'C');
$pdf->Cell(45,4,"",'LRB',0,'C');
$pdf->Cell(70,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(28,4,"",'LRB',0,'C');

$pdf->Ln(6);	
$pdf->SetX(17); 
$pdf->SetFont('Courier','UB',10);
$pdf->Cell(100,4,"RESUMEN:");
$pdf->Ln(5);	


$pdf->SetX(17); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,"SALDO INICIAL:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(6,4,$Moneda[1]['S'].number_format($row1["ImporteApertura"],2));

$pdf->Ln(4);	

$pdf->SetX(17); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(110,4,"+ INGRESOS:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteIngresos"],2));

$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,"- GASTOS:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteGastos"],2));

$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,"+ APORTACIONES:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteAportaciones"],2));

$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','',8);
$pdf->Cell(100,4,"- SUSTRACCIONES:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteSustracciones"],2));

$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','B',8);
$pdf->Cell(100,4,"= TEORICO CIERRE:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteTeoricoCierre"],2));

$pdf->Ln(6);

$pdf->SetX(17); 
$pdf->SetFont('Courier','B',8);
$pdf->Cell(100,4,"CIERRE CAJA:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteCierre"],2));

$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','B',8);
$pdf->Cell(100,4,"DESCUADRE CAJA:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["ImporteDescuadre"],2));
/*
$pdf->Ln(4);

$pdf->SetX(17); 
$pdf->SetFont('Courier','B',8);
$pdf->Cell(100,4,"UTILIDAD VENTA:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($row1["UtilidadVenta"],2));
*/
$pdf->Ln(8);

$xLocal  = getNombreComercialLocal($IdLocal);

$fecha=implota($fechahoy=date("Y-m-d"));
$hora=date("H:i");

$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$xLocal.":::";
$pdf->SetX(17);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);


//#### NOMBRE DEL FICHERO
//$prod = explode(" ",$producto);
//$cod  = $prod[0];
$name = "Arqueo_Caja_".$idarqueo.".pdf";

$pdf->Output($name,'');
?>