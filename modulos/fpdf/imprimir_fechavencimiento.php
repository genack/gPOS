<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunesexp.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal   = getSesionDato("IdTienda");
$Moneda    = getSesionDato("Moneda");

$IdLocal = CleanID($_GET["xlocal"]);
$Desde   = CleanCadena($_GET["desde"]);
$Hasta   = CleanCadena($_GET["hasta"]);
$OpVence = CleanText($_GET["opestado"]);


setlocale(LC_ALL,"es_ES");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

/*
global $avencimientos;
$row = $GLOBALS["avencimiento"];//$avencimientos;
print_r(getSesionDato("Vencimientos"));
return;
*/
$Estado = ($OpVence == 'Vencido')? 'Vencidos':'Por Vencer';

//PDF ESTRUCTURA
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(10);
$pdf->SetFont('Courier','B',13);	
$pdf->Cell(0,4,utf8_decode("FECHA DE VENCIMIENTO DE PRODUCTOS"),0,0,'C' );

$pdf->Ln(12);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,"Estado:" );

$pdf->SetX(32);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,utf8_decode($Estado));

//Detalle
$pdf->Ln(8);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(30,4,utf8_decode('Lista Productos'));
$pdf->SetX(52); 
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
$pdf->Cell(30,4,utf8_decode("Almacén"),1,0,'L',1);
$pdf->Cell(20,4,utf8_decode("CB"),1,0,'L',1);
$pdf->Cell(100,4,"Producto",1,0,'L',1);
$pdf->Cell(20,4,"Cantidad",1,0,'C',1);
$pdf->Cell(30,4,"Fecha Vencimiento",1,0,'D',1);
$pdf->Cell(35,4,"Lote",1,0,'C',1);


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
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(100,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(30,4,"",'LR',0,'C');
$pdf->Cell(35,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;
$item  = 1;

/*
$alista = getSesionDato("Vencimientos");
if(!$alista) return;

foreach($alista as $key=>$value) {
*/

//------------------
  $IdLocal = ($IdLocal == 0)? "":" AND ges_locales.IdLocal = $IdLocal ";
  $Estado  = $OpVence;

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
         "$IdLocal ".
         "GROUP BY IdPedidoDet ".
         "HAVING Saldo > 0 ";

  $res2 = query($sql2);
  $iddets = array();
  $iddet = array();

while($row2 = Row($res2)){

    $yid = $row2["IdPedidoDet"];

    $iddet[$yid]["id"] = $yid;
    $iddet[$yid]["Saldo"] = $row2["Saldo"];

    array_push($iddets,$row2["IdPedidoDet"]);
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

  $res1 = obtenerDetVencimiento($iddets,$esVencido,$Desde,$Hasta,$IdLocal);

  while($row1 = Row($res1)){

    foreach($iddet as $key=>$value){

      if($row1["IdPedidoDet"] == $key){
	$row1["Saldo"] = $value["Saldo"];
      }
      continue;
    }

  $Local    = $row1["Local"];
  $CB       = $row1["CB"];
  $Producto = $row1["Producto"];
  $Stock    = $row1["Saldo"];
  $FechaVence = $row1["FechaVencimiento"];
  $Lote     = $row1["Lote"];
  //--------------
    /*
  $Local    = $value["Local"];
  $CB       = $value["CB"];
  $Producto = $value["Producto"];
  $Stock    = $value["Saldo"];
  $FechaVence = $value["FechaVencimiento"];
  $Lote     = $value["Lote"];
    */


  //PRODUCTO ITEM
  $acotado = array();
  $acotado = getItemProducto($Producto,40);

  // IMPRIME LINE
  $pdf->SetX(17); 
  $pdf->Cell(1);

  $pdf->SetFont('Courier','',9);
  $pdf->Cell(6,4,$item,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(30,4,utf8_decode($Local),'LR',0,'L');
  $pdf->SetFont('Courier','',8);
  $pdf->Cell(20,4,utf8_decode($CB),0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(100,4,utf8_decode($acotado[0]),'LR',0,'L');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(20,4,$Stock,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(30,4,$FechaVence,'LR',0,'R');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(35,4,$Lote,'LR',0,'R');
  $pdf->Ln(4);	

  //TEXT EXTRA LINE run
  $pdf->SetFont('Courier','',8);
  foreach ($acotado as $key=>$line){
    if($key>0 && $key < 27 ){
      $pdf->SetX(17); 
      $pdf->Cell(1);
      $pdf->Cell(6,4,'','LR',0,'R');
      $pdf->Cell(30,4,'','LR',0,'R');
      $pdf->Cell(20,4,'','LR',0,'R');
      $pdf->Cell(100,4,utf8_decode($line),'LR',0,'L');
      $pdf->Cell(20,4,"",'LR',0,'R');
      $pdf->Cell(30,4,"",'LR',0,'R');
      $pdf->Cell(35,4,"",'LR',0,'C');
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
    $pdf->Cell(20,4,"",'LR',0,'c');
    $pdf->Cell(100,4,"",'LR',0,'C');
    $pdf->Cell(20,4,"",'LR',0,'C');
    $pdf->Cell(30,4,"",'LR',0,'C');
    $pdf->Cell(35,4,"",'LR',0,'C');

    $pdf->Ln(4);	
    $contador=$contador +1;
  }

// LINEA 3
$pdf->Ln(-3);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LRB',0,'C');
$pdf->Cell(30,4,"",'LRB',0,'c');
$pdf->Cell(20,4,"",'LRB',0,'c');
$pdf->Cell(100,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(30,4,"",'LRB',0,'C');
$pdf->Cell(35,4,"",'LRB',0,'C');

$pdf->Ln(8);

$xLocal  = getNombreComercialLocal($IdLocal);

$fecha = implota($fechahoy=date("Y-m-d"));
$hora = date("H:i");

$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$xLocal.":::";
$pdf->SetX(17);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);


//#### NOMBRE DEL FICHERO

$name = "Productos_".$Estado.".pdf";

$pdf->Output($name,'');
//$_SESSION["Vencimientos"] = "";
?>