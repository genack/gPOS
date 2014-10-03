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

$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

$producto = $_GET["xproduct"];
$idprod   = CleanID($_GET["xid"]);
$unidad   = CleanText($_GET["unidad"]);
$exist    = CleanText($_GET["exist"]);
$cprom    = CleanText($_GET["cpromedio"]);
$ctotal   = CleanText($_GET["ctotal"]);
$xlocal   = CleanID($_GET["xlocal"]);

$sql = "SELECT ges_almacenes.StockMin, ges_locales.NombreComercial as local ".
       "FROM ges_almacenes inner join ges_locales ON ges_almacenes.IdLocal = ".
       "ges_locales.IdLocal where ges_almacenes.IdProducto = '$idprod' ".
       "AND ges_almacenes.IdLocal = '$xlocal'";

$res = query($sql);
$row = Row($res);

$local   = utf8_decode($row["local"]);
$eminima = $row["StockMin"];

//PDF ESTRUCTURA
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(10);
$pdf->SetFont('Courier','B',13);	
$pdf->Cell(0,4,utf8_decode("TARJETA DE INFORMACIÓN"),0,0,'C' );

$pdf->Ln(12);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,"Producto:" );

$pdf->SetX(36);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,utf8_decode($producto));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Almacén :"));

$pdf->SetX(36);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode($local));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Existencia Mínima:"));

$pdf->SetX(54);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,$eminima.' '.$unidad);

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
$pdf->Cell(70,4,utf8_decode("Operación"),1,0,'C',1);
$pdf->Cell(20,4,"Movimiento",1,0,'C',1);
$pdf->Cell(35,4,"Cantidad",1,0,'C',1);
$pdf->Cell(20,4,"C.U.(".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(20,4,"C.T.(".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(35,4,"Saldo",1,0,'C',1);
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
$pdf->Cell(70,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(35,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(35,4,"",'LR',0,'C');
$pdf->Cell(28,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;

$desde    = CleanCadena($_GET["desde"]);
$hasta    = CleanCadena($_GET["hasta"]);
$xope     = CleanID($_GET["xope"]);
$xmov     = CleanText($_GET["xmov"]);

$extra  = ( $xope )? "AND ges_kardex.IdKardexOperacion = '".$xope."' ":"";
$extra .= ( $xmov )? "AND ges_kardex.TipoMovimiento = '".$xmov."' ":"";

$sql    = 
    "SELECT DATE_FORMAT(FechaMovimiento, '%e/%m/%y %H:%i') as FechaMovimiento,".
    "       KardexOperacion,".
    "       IdPedidoDet,".
    "       IdComprobanteDet,".
    "       CantidadMovimiento,".
    "       ROUND(CostoUnitarioMovimiento,2) as CostoUnitarioMovimiento,".
    "       ROUND(CostoTotalMovimiento,2) as CostoTotalMovimiento,".
    "       ges_usuarios.Identificacion, ".
    "       SaldoCantidad, ".
    "       TipoMovimiento, ".
    "       IdKardexAjusteOperacion, ".
    "       ges_contenedores.Contenedor as Cont, ".
    "       ges_productos.UnidadMedida as Unid, ".
    "       ges_productos.UnidadesPorContenedor as UnidxCont, ".
    "       ges_productos.VentaMenudeo ".
    "FROM   ges_kardex,ges_usuarios,ges_kardexoperacion,".
    "       ges_productos,ges_contenedores ".
    "WHERE  ges_kardex.IdProducto ='$idprod' ".
    "AND    ges_usuarios.IdUsuario = ges_kardex.IdUsuario ".
    "AND    ges_productos.IdProducto = ges_kardex.IdProducto ".
    "AND    ges_contenedores.IdContenedor = ges_productos.IdContenedor ".
    "AND    ges_kardex.IdKardexOperacion = ges_kardexoperacion.IdKardexOperacion ".
    "AND    IdLocal='$xlocal' ".
    "AND    FechaMovimiento>= '$desde'  ".
    "AND    FechaMovimiento<= ADDDATE('$hasta',1) ".
    "AND    ges_kardex.Eliminado=0 ".
    $extra.
    "ORDER  BY IdKardex ASC";

$res   = query($sql);
$item  = 1;

while($row= Row($res)) {

  $detalle  = "";
  $fila     = array();
  $idped    = $row["IdPedidoDet"];
  $idcom    = $row["IdComprobanteDet"];
  $tmovi    = $row["TipoMovimiento"];
  $menudeo  = ($row["VentaMenudeo"])? $row["UnidxCont"].$row["Unid"]." x ".$row["Cont"]:false;
  $mkardex  = ($idped)? 'Pedido':'';
  $mkardex  = ($idcom)? 'Comprobante':$mkardex;

  $idx      = ($idped)? $idped:'';
  $idx      = ($idcom)? $idcom:$idx;

  //Menundeo
  $unidresto   = ($menudeo)? $row["CantidadMovimiento"]%$row["UnidxCont"]:0; 
  $unidempaque = ($menudeo)? ($row["CantidadMovimiento"]-$unidresto)/$row["UnidxCont"]:0;
  $unidmenudeo = ($menudeo)? $unidempaque." ".$row["Cont"]." + ".$unidresto:0;
  $existencias = ($menudeo)? $unidmenudeo:$row["CantidadMovimiento"]; 

  //Saldo
  $saldoresto  = ($menudeo)? $row["SaldoCantidad"]%$row["UnidxCont"]:0; 
  $unidempaque = ($menudeo)? ($row["SaldoCantidad"]-$saldoresto)/$row["UnidxCont"]:0;
  $unidmenudeo = ($menudeo)? $unidempaque." ".$row["Cont"]." + ".$saldoresto:0;
  $saldo       = ($menudeo)? $unidmenudeo:$row["SaldoCantidad"]; 

  $kdxop    = $row["KardexOperacion"];
  $idaju    = $row["IdKardexAjusteOperacion"];
  $mkardex  = obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju);


  $fmovimiento = utf8_decode($row["FechaMovimiento"]);
  $kardexop    = utf8_decode($row["KardexOperacion"]);
  $tipomov     = utf8_decode($row["TipoMovimiento"]);
  $cantidadmov = utf8_decode($row["CantidadMovimiento"]);
  $costounimov = utf8_decode($row["CostoUnitarioMovimiento"]);
  $costototal  = utf8_decode($row["CostoTotalMovimiento"]);
  //$saldo       = utf8_decode($row["SaldoCantidad"]);
  $user        = utf8_decode($row["Identificacion"]);
  $op          = $kardexop." ".$mkardex["Documento"];

  $pdf->SetX(17); 
  $pdf->Cell(1);

  //PRODUCTO ITEM
  $acotado = array();
  $acotado = getItemProducto($op,40);


  // IMPRIME LINE
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(6,4,$item,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(30,4,utf8_decode($fmovimiento),'LR',0,'L');
  $pdf->SetFont('Courier','',8);
  $pdf->Cell(70,4,utf8_decode($acotado[0]),0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(20,4,$tipomov,'LR',0,'C');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(35,4,$existencias.' '.$unidad,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(20,4,number_format($costounimov,2),'LR',0,'R');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(20,4,number_format($costototal,2),'LR',0,'R');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(35,4,$saldo.' '.$unidad,'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(28,4,$user,'LR',0,'R');
  $pdf->Ln(4);	

  //TEXT EXTRA LINE run
  $pdf->SetFont('Courier','',8);
  foreach ($acotado as $key=>$line){
    if($key>0 && $key < 27 ){
      $pdf->SetX(17); 
      $pdf->Cell(1);
      $pdf->Cell(6,4,'','LR',0,'R');
      $pdf->Cell(30,4,'','LR',0,'R');
      $pdf->Cell(70,4,utf8_decode($line),'LR',0,'L');
      $pdf->Cell(20,4,"",'LR',0,'R');
      $pdf->Cell(35,4,"",'LR',0,'R');
      $pdf->Cell(20,4,"",'LR',0,'C');
      $pdf->Cell(20,4,"",'LR',0,'C');
      $pdf->Cell(35,4,"",'LR',0,'C');
      $pdf->Cell(28,4,"",'LR',0,'C');
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
    $pdf->Cell(70,4,"",'LR',0,'C');
    $pdf->Cell(20,4,"",'LR',0,'C');
    $pdf->Cell(35,4,"",'LR',0,'C');
    $pdf->Cell(20,4,"",'LR',0,'C');
    $pdf->Cell(20,4,"",'LR',0,'C');
    $pdf->Cell(35,4,"",'LR',0,'C');
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
$pdf->Cell(70,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(35,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(35,4,"",'LRB',0,'C');
$pdf->Cell(28,4,"",'LRB',0,'C');

$pdf->Ln(5);	

$pdf->SetX(17); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(100,4,"Existencias:");

$pdf->SetX(42); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(6,4,$exist);

$pdf->SetX(105); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(110,4,"Costo Promedio:");

$pdf->SetX(137); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($cprom,2));

$pdf->SetX(170); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(100,4,"Costo Total:");

$pdf->SetX(195); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($ctotal,2));

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
$prod = explode(" ",$producto);
$cod  = $prod[0];
$name = "Producto_".$cod.".pdf";

$pdf->Output($name,'');
?>