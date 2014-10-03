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
$idcod         = $_GET["idoc"];
$totaletras    = $_GET["totaletras"];
$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

	$sql = "SELECT
                ges_usuarios.Nombre As Vendedor, 
                ges_locales.NombreComercial as Local,
                ges_comprobantes.SerieComprobante,
                ges_comprobantes.NComprobante,
                DATE_FORMAT(ges_comprobantesnum.Fecha,'%d/%m/%Y %H:%i') as Fecha,
                ges_comprobantes.TotalImporte,
                ges_comprobantes.ImportePendiente,
                ges_comprobantesstatus.Status, 
                ges_comprobantes.IdComprobante,
                CONCAT(ges_comprobantestipo.Serie,'-',ges_comprobantesnum.NumeroComprobante) as NumeroComprobante,
                ges_comprobantestipo.TipoComprobante as TipoDocumento,
                IF(Destinatario = 'Cliente',(SELECT CONCAT(ges_clientes.TipoCliente,' : ',ges_clientes.nombreComercial ) FROM ges_clientes WHERE ges_clientes.IdCliente = ges_comprobantes.IdCliente),(IF(Destinatario='Local',(SELECT CONCAT('Interno : ',ges_locales.nombreComercial) FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantes.IdCliente),  (SELECT CONCAT('Externo : ',ges_proveedores.NombreComercial) FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantes.IdCliente)))) as Cliente, 
                ges_comprobantestipo.TipoComprobante as TipoDocumento,
                IF(Destinatario = 'Cliente',(SELECT NumeroFiscal FROM ges_clientes WHERE ges_clientes.IdCliente = ges_comprobantes.IdCliente),(IF(Destinatario='Local',(SELECT NFiscal FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantes.IdCliente),  (SELECT NumeroFiscal FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantes.IdCliente)))) as RUC,
                ges_comprobantes.IdCliente 
    		FROM ges_comprobantes " .
    		"LEFT JOIN ges_clientes ON ges_comprobantes.IdCliente = ges_clientes.IdCliente
                INNER JOIN ges_comprobantesstatus ON ges_comprobantes.Status = ges_comprobantesstatus.IdStatus
                INNER JOIN ges_locales ON ges_comprobantes.IdLocal = ges_locales.IdLocal
                INNER JOIN ges_usuarios ON ges_comprobantes.IdUsuario = ges_usuarios.IdUsuario
                INNER JOIN ges_comprobantesnum ON ges_comprobantesnum.IdComprobante = ges_comprobantes.IdComprobante
                INNER JOIN ges_comprobantestipo ON  ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante 
                WHERE ges_comprobantes.Eliminado = 0
                AND  ges_comprobantes.IdComprobante = '$idcod'";  


$res       = query($sql);
$row       = Row($res);

$NComprobante = utf8_decode($row["NumeroComprobante"]);
$TotalImporte = utf8_decode($row["TotalImporte"]);
$ImportePdte  = utf8_decode($row["ImportePendiente"]);
$RUC       = utf8_decode($row["RUC"]);
$Documento = utf8_decode($row["TipoDocumento"]);
$Fecha     = utf8_decode($row["Fecha"]);
$Cliente   = utf8_decode($row["Cliente"]);
$Telefono  = '';//utf8_decode($row["Fecha"]);
$EstadoDoc = utf8_decode($row["Status"]);
$Local      = utf8_decode($row["Local"]);

//PDF ESTRUCTURA
//$pdf=new PDF();
$pdf=new PDF('P','mm','A4');
$pdf->Open();
$pdf->AddPage();

//TITULO
$pdf->SetX(10);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(0,0,strtoupper($Documento)." ".strtoupper($NComprobante),0,0,'C' );


$pdf->Ln(10);

// RESUMEN
$fila = 18;
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Cliente  : '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Cliente);
 
$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC      : '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC);

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Teléfono :'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Telefono);

$pdf->Ln(6);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Moneda   :'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Moneda[1]['T']);

$pdf->SetX($fila+21+46); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Emisión :'));

$pdf->SetX($fila+21+46+32); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Fecha);

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Tipo Pago:'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(120,4,'Contado');

$pdf->SetX($fila+21+46); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado Pago   :'));


$pdf->SetX($fila+21+46+32); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$EstadoDoc);


$pdf->Ln(6);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(18,4,utf8_decode('IMPORTE  : '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,$Moneda[1]['S'].number_format($TotalImporte,2));

$pdf->SetX($fila+58); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,"(".utf8_decode($totaletras)." )");

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(18,4,utf8_decode('PENDIENTE: '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,$Moneda[1]['S'].$ImportePdte);


//Detalle
$pdf->Ln(8);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(18,4,utf8_decode('Detalle Pago'));
$pdf->SetX(43); 
$pdf->SetFont('Courier','B',10);	
$pdf->Cell(1,4,':');

$pdf->Ln(6);

// las lneas delos Pagos
$pdf->SetX(17); 
$pdf->Cell(1);

$pdf->SetFillColor(210,210,210);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',8);
	

$pdf->Cell(6,4,"#",1,0,'C',1);
$pdf->Cell(35,4,"Fecha",1,0,'D',1);
$pdf->Cell(70,4,"Forma Pago",1,0,'C',1);
$pdf->Cell(30,4,"Importe (".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(25,4,"Usuario",1,0,'C',1);

$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LR',0,'C');
$pdf->Cell(35,4,"",'LR',0,'c');
$pdf->Cell(70,4,"",'LR',0,'C');
$pdf->Cell(30,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;

function obtnercobroscomprobantes1($idcod){


  $sql = "SELECT ModalidadPago, ".
         "DATE_FORMAT(ges_dinero_movimientos.FechaInsercion, '%e %b %y  %H:%i') AS Fecha, ".
         "Importe, ".
         "ges_usuarios.Identificacion As Usuario, ".
         "IdOperacionCaja, ".
         "ges_locales.NombreComercial as Local ".
         "FROM ges_dinero_movimientos ".
         "INNER JOIN ges_usuarios ON ges_dinero_movimientos.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_modalidadespago ON ges_dinero_movimientos.IdModalidadPago = ges_modalidadespago.IdModalidadPago ".
         "INNER JOIN ges_locales ON ges_dinero_movimientos.IdLocal = ges_locales.IdLocal ".
         "WHERE ges_dinero_movimientos.Eliminado = 0 ".
         "AND IdComprobante = '$idcod' ".
         "ORDER BY IdOperacionCaja ASC ";

  $res = query($sql);
  return $res;

}
function obtnercobroscomprobantes2($idcod){

  
  $sql = "SELECT 'EFECTICO' AS ModalidadPago, ".
         "DATE_FORMAT(ges_librodiario_cajagral.FechaInsercion, '%e %b %y  %H:%i') AS Fecha, ".
         "Importe, ".
         "ges_usuarios.Nombre As Usuario, ".
         "IdOperacionCaja, ".
         "ges_locales.NombreComercial as Local ".
         "FROM ges_librodiario_cajagral ".
         "INNER JOIN ges_usuarios ON ges_librodiario_cajagral.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_locales ON ges_librodiario_cajagral.IdLocal = ges_locales.IdLocal ".
         "WHERE ges_librodiario_cajagral.Eliminado = 0 ".
         "AND IdComprobante = '$idcod' ".
         "ORDER BY IdOperacionCaja ASC ";

  $res = query($sql);
  
  return $res;
}

$res1 = obtnercobroscomprobantes1($idcod);
$res2 = obtnercobroscomprobantes2($idcod);
$cant =  mysql_num_rows($res1);
if($cant == 0)
  $res1 = $res2;
$item = 1;
//$cant =  mysql_num_rows($res1);
$ImporteTotal = 0;

while ( $row =Row($res1) ) { 
  $pdf->SetX(17); 
  $pdf->Cell(1);
  //print_r($row);
  $Modalidad = $row["ModalidadPago"];
  $Fecha     = $row["Fecha"];
  $Importe   = $row["Importe"];
  $Usuario   = $row["Usuario"];
  
  $pdf->SetFont('Courier','',9);
  
  // IMPRIME LINE
  $pdf->Cell(6,4,$item,'LR',0,'R');
  $pdf->Cell(35,4,$Fecha,'LR',0,'L');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(70,4,$Modalidad,'LR',0,'L');
  $pdf->SetFont('Courier','B',9);
  $pdf->Cell(30,4,number_format($Importe,2),'LR',0,'R');
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(25,4,$Usuario,'LR',0,'C');

  
  $pdf->Ln(4);	
  
  //CONTADOR
  $contador++;
  $item++;
  $ImporteTotal = $ImporteTotal + $row["Importe"];
  
  if($cant < $contador)
    $res1 = $res2;
};

while ($contador<2)
  {
    $pdf->SetX(17); 
    $pdf->Cell(1);
    $pdf->Cell(6,4,"",'LR',0,'C');
    $pdf->Cell(35,4,"",'LR',0,'c');
    $pdf->Cell(70,4,"",'LR',0,'C');
    $pdf->Cell(30,4,"",'LR',0,'C');
    $pdf->Cell(25,4,"",'LR',0,'C');
    
    $pdf->Ln(4);	
    $contador = $contador + 1;
  }

// LINEA 3
$pdf->Ln(-3);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LRB',0,'C');
$pdf->Cell(35,4,"",'LRB',0,'c');
$pdf->Cell(70,4,"",'LRB',0,'C');
$pdf->Cell(30,4,"",'LRB',0,'C');
$pdf->Cell(25,4,"",'LRB',0,'C');

$pdf->Ln(4);	
//$pdf->Cell(1);

$fecha=implota($fechahoy=date("Y-m-d"));
$hora=date("H:i");
$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$Local.":::";
$pdf->SetX(17);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200,200,200);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',9);
$pdf->SetX(103);		
$pdf->Cell(1);
$pdf->Cell(25,4,"Total Aporte",1,0,'R',1);
$pdf->Cell(30,4,$Moneda[1]['S'].number_format($ImporteTotal,2),1,0,'R',1);


$pdf->Ln(6);



$pdf->Ln(6);


@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$name = $Documento."-".$NComprobante.".pdf";

$pdf->Output($name,'');
?> 
