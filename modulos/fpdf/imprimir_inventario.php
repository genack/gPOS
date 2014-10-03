<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunesexp.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

setlocale(LC_ALL,"es_ES");

$IdLocal  = CleanID($_GET["xlocal"]);
$Moneda    = getSesionDato("Moneda");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

$hasta    = CleanFechaES($_GET["hasta"]);
$desde    = CleanFechaES($_GET["desde"]);
$xfamilia = CleanID($_GET["familia"]);
$xmarca   = CleanID($_GET["marca"]);
$esInvent = ( $_GET["xinventario"] == "Inventario" )? true:false;
$xinvent  = CleanID($_GET["xidinventario"]);
$xnombre  = CleanText($_GET["xnombre"]);
$xcodigo  = CleanCB($_GET["xcodigo"]);
$xope     = CleanID($_GET["xope"]);
$xidope   = ($xope==7)?CleanID($_GET["xidope"]):false;//**Pendiente busqueda inventarios
$xmov     = CleanText($_GET["xmov"]);
$invent   = CleanText($_GET["xtitulo"]);
$almacen  = CleanText($_GET["alma"]);
$idinv    = CleanID($_GET["idinv"]);

$xLocal   = getNombreComercialLocal($IdLocal);
$almacen  = ($almacen)? $almacen:$xLocal;

$sql = "SELECT DATE_FORMAT(FechaInventarioInicio, '%d/%m/%Y %H:%i') as FechaInicio, ".
       "       DATE_FORMAT(FechaInventarioFin, '%d/%m/%Y %H:%i') as FechaFin ".
       "FROM   ges_inventario ".
       "WHERE  IdInventario = '$idinv' "; 
$row = queryrow($sql);

$fechaini  = ($esInvent)? $row["FechaInicio"]:$desde;
$fechafin  = ($esInvent)? $row["FechaFin"]:$hasta;

//PDF ESTRUCTURA
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(10);
$pdf->SetFont('Courier','B',13);	
$pdf->Cell(0,4,utf8_decode($invent),0,0,'C' );

$pdf->Ln(12);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,utf8_decode("Almacén     :"));

$pdf->SetX(45);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(200,4,utf8_decode($almacen));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Fecha Inicio: "));

$pdf->SetX(45);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode($fechaini));

$pdf->Ln(4);

$pdf->SetX(17);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(50,4,utf8_decode("Fecha Fin   :"));

$pdf->SetX(45);
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(60,4,$fechafin);

//Detalle
$pdf->Ln(8);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(18,4,utf8_decode('Inventario Detalle'));
$pdf->SetX(57); 
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
$pdf->Cell(32,4,utf8_decode("Fecha"),1,0,'D',1);
$pdf->Cell(80,4,utf8_decode("Producto"),1,0,'C',1);
$pdf->Cell(70,4,utf8_decode("Operación"),1,0,'C',1);
$pdf->Cell(35,4,"Saldo",1,0,'C',1);
$pdf->Cell(20,4,"C. U.",1,0,'C',1);
$pdf->Cell(20,4,"C. T.",1,0,'C',1);




$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LR',0,'C');
$pdf->Cell(32,4,"",'LR',0,'c');
$pdf->Cell(80,4,"",'LR',0,'C');
$pdf->Cell(70,4,"",'LR',0,'C');
$pdf->Cell(35,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');


$pdf->Ln(2);	

$contador=1;

$item  = 1;

$xinvent = ($xinvent)? $xinvent:'none';

$res = obtenerKardexMovimientosInventario($IdLocal,$desde,$hasta,$xfamilia,
					  $xmarca,$xope,$xmov,$xnombre,
					  $xcodigo,$xinvent,$esInvent,
					  true,false,false,false,false);

if (!$res) return false;
$OrdenKardex = array();
$t = 0;
$cont = 0;
$aux  = 0;
$totalprod = 0;
$totalimp  = 0;
while($row = Row($res))
  {
	$detalle  = "";
	$idped    = $row["IdPedidoDet"];
	$kdxop    = $row["KardexOperacion"];
	$idcom    = $row["IdComprobanteDet"];
	$idaju    = $row["IdKardexAjusteOperacion"];
	$menudeo  = ($row["VentaMenudeo"])? $row["UnidxCont"].$row["Unid"]." x ".$row["Cont"]:false;
	$mkardex  = ($idped)? 'Pedido':false;
	$mkardex  = ($idcom)? 'Comprobante':$mkardex;
	$idx      = ($idped)? $idped:'';
	$idx      = ($idcom)? $idcom:$idx;
	$arkdx    = obtenerKardexDocumento($mkardex,$idx,$menudeo,$kdxop,$idaju);
	
	$row["KardexOperacion"] = $kdxop.$arkdx["Motivo"];
	$row["Documento"]       = $arkdx["Documento"];
	$row["Detalle"]         = $arkdx["Detalle"];

	
	
	$fila     = array();
	$tmovi    = $row["TipoMovimiento"];
	
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
	
	
	$fmovimiento = utf8_decode($row["FechaMovimiento"]);
	$kardexop    = utf8_decode($row["KardexOperacion"]);
	$tipomov     = utf8_decode($row["TipoMovimiento"]);
	$cantidadmov = utf8_decode($row["CantidadMovimiento"]);
	$costounimov = utf8_decode($row["CostoUnitarioMovimiento"]);
	$costototal  = utf8_decode($row["CostoTotalMovimiento"]);
	$saldocant   = utf8_decode($row["SaldoCantidad"]);
	$user        = utf8_decode($row["Usuario"]);
	$producto    = utf8_decode($row["Producto"]);
	$op          = $kardexop." ".$arkdx["Documento"];
	$kardexop    = str_replace("-","",$kardexop);
	$unidmedida  = utf8_decode($row["Unid"]);
	$saldototal  = $costounimov*$saldocant;

	$pdf->SetX(17); 
	$pdf->Cell(1);
	
	//PRODUCTO ITEM
	$producto = ($row["VentaMenudeo"] == 1)?$producto." (".$row["UnidxCont"].$row["Unid"]."/".$row["Cont"].")":$producto;
	$acotado  = array();
	$acotado  = getItemProducto($producto,45);

	// IMPRIME LINE
	$pdf->SetFont('Courier','',9);
	$pdf->Cell(6,4,$item,'LR',0,'R');
	$pdf->SetFont('Courier','',9);
	$pdf->Cell(32,4,utf8_decode($fmovimiento),'LR',0,'L');
	$pdf->SetFont('Courier','',8);
	$pdf->Cell(80,4,$acotado[0],'LR',0,'L');
	$pdf->SetFont('Courier','',9);
	$pdf->Cell(70,4,utf8_decode($kardexop),'LR',0,'L');
	$pdf->SetFont('Courier','B',9);
	$pdf->Cell(35,4,$saldo.' '.$unidmedida,'LR',0,'R');
	$pdf->SetFont('Courier','',9);
	$pdf->Cell(20,4,number_format($costounimov,2),'LR',0,'R');
	$pdf->SetFont('Courier','B',9);
	$pdf->Cell(20,4,number_format($saldototal,2),'LR',0,'R');
	$pdf->Ln(4);	
    
	//TEXT EXTRA LINE run
	$pdf->SetFont('Courier','',8);
	foreach ($acotado as $key=>$line){

	    if(($key>0 && $key < 27) ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(6,4,'','LR',0,'R');
	      $pdf->Cell(32,4,'','LR',0,'R');
	      $pdf->Cell(80,4,utf8_decode($line),'LR',0,'L');
	      $pdf->Cell(70,4,'','LR',0,'L');
	      $pdf->Cell(35,4,"",'LR',0,'R');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      
	      $contador++;
	      $acotadoext = 0;
	    
	  }
	}


	//CONTADOR
	$contador++;
	$item++;

	while ($contador<2)
	  {
	    $pdf->SetX(17); 
	    $pdf->Cell(1);
	    $pdf->Cell(6,4,"",'LR',0,'C');
	    $pdf->Cell(32,4,"",'LR',0,'c');
	    $pdf->Cell(80,4,"",'LR',0,'C');
	    $pdf->Cell(65,4,"",'LR',0,'C');
	    $pdf->Cell(70,4,"",'LR',0,'C');
	    $pdf->Cell(20,4,"",'LR',0,'C');
	    $pdf->Cell(20,4,"",'LR',0,'C');
	    
	    $pdf->Ln(4);	
	    $contador=$contador +1;
	  }
	$totalprod++;
	$totalimp = $totalimp + $saldototal;

      
  }

// LINEA 3
$pdf->Ln(-3);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LRB',0,'C');
$pdf->Cell(32,4,"",'LRB',0,'c');
$pdf->Cell(80,4,"",'LRB',0,'C');
$pdf->Cell(70,4,"",'LRB',0,'C');
$pdf->Cell(35,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');

$pdf->Ln(5);	

$pdf->SetX(17); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(100,4,"Total Productos:");

$pdf->SetX(50); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(6,4,$totalprod);

$pdf->SetX(75); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(110,4,"Costo Total:");

$pdf->SetX(100); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(10,4,$Moneda[1]['S'].number_format($totalimp,2));

$pdf->Ln(8);

$fecha=implota($fechahoy=date("Y-m-d"));
$hora=date("H:i");

$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$xLocal.":::";
$pdf->SetX(17);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);



@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$invent  = str_replace("- ","",$invent);
$invent  = str_replace(" ","_",$invent);
$almacen = str_replace(" ","_",$almacen);
$name    = "gPOS_".$almacen."_".$invent.".pdf";

$pdf->Output($name,'');
?>