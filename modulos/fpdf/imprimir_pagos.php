<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunesexp.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");
$IdLocal      = getSesionDato("IdTienda");

setlocale(LC_ALL,"es_ES");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$idcod         = $_GET["idoc"];
$totaletras    = $_GET["totaletras"];
$operador      = $_SESSION["NombreUsuario"];
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

$sql="SELECT			
                ges_locales.NombreComercial As Almacen,
                ges_proveedores.nombreLegal As Proveedor,
                ges_proveedores.CuentaBancaria As Cuenta,
                ges_proveedores.Telefono1 As Telefono,
                ges_proveedores.NumeroFiscal As RUC,
		ges_comprobantesprov.IdComprobanteProv,
                ges_comprobantesprov.Codigo,
                UPPER(ges_comprobantesprov.TipoComprobante) As Documento,
                DATE_FORMAT(ges_comprobantesprov.FechaRegistro, '%e %b %Y  %k:%i') As Registro,
                IF ( DATE_FORMAT(ges_comprobantesprov.FechaFacturacion, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_comprobantesprov.FechaFacturacion, '%e %b %Y ') ) 
                    As Emision,
                IF ( DATE_FORMAT(ges_comprobantesprov.FechaPago, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_comprobantesprov.FechaPago, '%e %b %Y') ) 
                    As Pago,
                ges_pedidos.Impuesto,
                ROUND(ges_comprobantesprov.TotalImporte*ges_pedidos.Percepcion/100,2) as Percepcion,
                ges_comprobantesprov.ImporteBase,
                ges_comprobantesprov.ImporteImpuesto,
                ges_comprobantesprov.TotalImporte,
                ges_comprobantesprov.ImportePendiente,
                ges_comprobantesprov.ImportePercepcion,
                ges_comprobantesprov.ImportePago,
                ges_comprobantesprov.ImporteFlete,
                ges_comprobantesprov.ModoPago,
                ges_comprobantesprov.EstadoDocumento,
                ges_comprobantesprov.EstadoPago,
                ges_usuarios.Nombre As Usuario,
                ges_pedidos.CambioMoneda,
                ges_pedidos.FechaCambioMoneda,
                ges_comprobantesprov.IdPedidosDetalle,
                ges_pedidos.IdPedido,
                ges_comprobantesprov.IdComprobanteProv,
                ges_pedidos.IdMoneda,
                IF ( ges_comprobantesprov.Observaciones like '', ' ',ges_comprobantesprov.Observaciones) as Observaciones,
                IF(ges_moneda.IdMoneda = 2, CONCAT(ges_moneda.Moneda,'/',ges_pedidos.CambioMoneda,'/', ges_pedidos.FechaCambioMoneda) ,ges_moneda.Moneda ) as MonedaDet,
                ges_pedidos.IdLocal,
                ges_pedidos.Impuesto,
                ges_pedidos.Percepcion,
	        ges_pedidos.IdAlmacenRecepcion,
 		ges_moneda.Simbolo As Moneda
         FROM  ges_comprobantesprov
         LEFT JOIN ges_proveedores ON ges_comprobantesprov.IdProveedor = ges_proveedores.IdProveedor
         INNER JOIN ges_pedidos    ON ges_comprobantesprov.IdPedido = ges_pedidos.IdPedido
         INNER JOIN ges_moneda     ON ges_pedidos.IdMoneda  = ges_moneda.IdMoneda
         INNER JOIN ges_locales    ON ges_pedidos.IdAlmacenRecepcion   = ges_locales.IdLocal
         INNER JOIN ges_usuarios   ON ges_comprobantesprov.IdUsuario = ges_usuarios.IdUsuario
         WHERE ges_comprobantesprov.IdComprobanteProv=".$idcod." AND ges_comprobantesprov.Eliminado = 0";

$res       = query($sql);
$row       = Row($res);
$Local     = utf8_decode($row["Almacen"]);
$Codigo    = utf8_decode($row["Codigo"]);
$Proveedor = utf8_decode($row["Proveedor"]);
$RUC       = utf8_decode($row["RUC"]);
$Documento = utf8_decode($row["Documento"]);
$Registro  = utf8_decode($row["Registro"]);
$Emision   = utf8_decode($row["Emision"]);
$Pago      = utf8_decode($row["Pago"]);
$Moneda    = utf8_decode($row["Moneda"]);
$Monedadet = utf8_decode($row["MonedaDet"]);
$ModoPago  = utf8_decode($row["ModoPago"]);
$ImportePago  = utf8_decode($row["ImportePago"]);
$ImporteFlete = utf8_decode($row["ImporteFlete"]);
$TotalImporte = utf8_decode($row["TotalImporte"]);
$ImporteImp   = utf8_decode($row["ImporteImpuesto"]);
$ImporteBase  = utf8_decode($row["ImporteBase"]);
$ImportePdte  = utf8_decode($row["ImportePendiente"]);
$ImportePerc  = utf8_decode($row["ImportePercepcion"]);
$Percepcion   = utf8_decode($row["Percepcion"]."%");
$IGV          = utf8_decode($row["Impuesto"]."%");
$EstadoDoc    = utf8_decode($row["EstadoDocumento"]);
$EstadoPago   = utf8_decode($row["EstadoPago"]);
$Usuario      = utf8_decode($row["Usuario"]);
$Observaciones   = utf8_decode($row["Observaciones"]);
$cuenta       = utf8_decode($row["Cuenta"]);
$telefono     = utf8_decode($row["Telefono"]);

//PDF ESTRUCTURA
//$pdf=new PDF();
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->AddPage();

//TITULO
$pdf->SetX(10);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(0,0,$Documento." ".$Codigo,0,0,'C' );


$pdf->Ln(10);

// RESUMEN
$fila = 18;
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Proveedor: '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Proveedor);
 
$pdf->SetX($fila+21+121); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC: '));

$pdf->SetX($fila+21+121+9); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC);

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Teléfono :'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$telefono);

$pdf->SetX($fila+21+46); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Cuenta Bancaria :'));

$pdf->SetX($fila+21+46+33); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$cuenta);


$pdf->Ln(6);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Moneda   :'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Monedadet);

$pdf->SetX($fila+21+46); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado Documento:'));

$pdf->SetX($fila+21+46+34); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$EstadoDoc);

$pdf->SetX($fila+21+121); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Emisión    :'));

$pdf->SetX($fila+21+121+36); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Emision);

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Tipo Pago:'));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(120,4,$ModoPago);

$pdf->SetX($fila+21+46); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado Pago     :'));

$pdf->SetX($fila+21+46+34); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$EstadoPago);

$pdf->SetX($fila+21+121); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Vencimiento:'));
 
$pdf->SetX($fila+21+121+36); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Pago);
$pdf->Ln(2);

// resumen importe
$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Importe Neto: '));
  
$pdf->SetX($fila+27); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(120,4,$Moneda.number_format($TotalImporte,2));

if($ImportePerc > 0.0 ){

  //$pdf->Ln(4);
  $pdf->SetX($fila+21+46); 
  $pdf->SetFont('Courier','B',9);	
  $pdf->Cell(120,4,utf8_decode('Percepción:'));
  
  $pdf->SetX($fila+21+46+24); 
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(120,4,$Moneda.number_format($ImportePerc,2));
}

if($ImporteFlete > 0 ){

  //$pdf->Ln(4);
  $pdf->SetX($fila+21+121); 
  $pdf->SetFont('Courier','B',9);	
  $pdf->Cell(120,4,utf8_decode('Flete: '));
  
  $pdf->SetX($fila+21+121+14); 
  $pdf->SetFont('Courier','',9);
  $pdf->Cell(120,4,$Moneda.number_format($ImporteFlete,2));
}


$pdf->Ln(6);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(18,4,utf8_decode('IMPORTE  : '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,$Moneda.number_format($ImportePago,2));

$pdf->SetX($fila+58); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,"(".utf8_decode($totaletras)." )");

$pdf->Ln(4);
$pdf->SetX($fila); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(18,4,utf8_decode('PENDIENTE: '));

$pdf->SetX($fila+21); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(120,4,$Moneda.number_format($ImportePdte,2));


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
$pdf->Cell(35,4,utf8_decode("Documento"),1,0,'D',1);
$pdf->Cell(25,4,"Estado",1,0,'C',1);
$pdf->Cell(70,4,"Forma Pago",1,0,'C',1);
$pdf->Cell(20,4,"Fecha Venc.",1,0,'C',1);
$pdf->Cell(20,4,"Fecha Pago",1,0,'C',1);
$pdf->Cell(30,4,"Importe",1,0,'C',1);
$pdf->Cell(20,4,"Mora",1,0,'C',1);
$pdf->Cell(25,4,"Estado Cuota",1,0,'C',1);

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
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Cell(70,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(30,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;

  $sql = "SELECT IF(Descripcion LIKE '',' ',Descripcion) as Documento, ".
         "ges_pagosprov.Estado, ".
         "IF(ges_pagosprov.Estado='Pendiente',' ',(SELECT ModalidadPago FROM ges_modalidadespago ".
           "INNER JOIN ges_pagosprovdoc ON ges_pagosprovdoc.IdModalidadPago ".
           "= ges_modalidadespago.IdModalidadPago WHERE ges_pagosprovdoc.IdPagoProvDoc ".
           "= ges_pagosprov.IdPagoProvDoc )) as ModoPago, ".
         "DATE_FORMAT(ges_pagosprov.FechaRegistro, '%e %b %y  %H:%i') AS FechaRegistro, ".
         "IF(DATE_FORMAT(ges_pagosprov.FechaPago, '%e %b %Y') IS NULL,' ',DATE_FORMAT(ges_pagosprov.FechaPago, '%e %b %y~%Y-%m-%d')) AS FechaPago, ".
         "ges_moneda.IdMoneda, ".
         "ges_moneda.Simbolo, ".
         "ges_pagosprov.Importe, ".
         "IF(Mora = 0,' ',Mora) AS Mora, ".
         "ges_usuarios.Nombre As Usuario, ".
         "ges_usuarios.Identificacion As User, ".
         "ges_pagosprov.IdPagoProv, ".
         "IF(ges_pagosprov.Estado='Pendiente',' ',ges_pagosprov.IdPagoProvDoc) AS IdPagoProvDoc, ".
         "ges_pagosprov.IdComprobanteProv, ".
         "IF(ges_pagosprov.IdPagoProvDoc = 0,' ',(SELECT CONCAT(DATE_FORMAT(ges_pagosprovdoc.FechaOperacion, '%e %b %y'),'~',CambioMoneda) ".
           "FROM ges_pagosprovdoc WHERE ges_pagosprovdoc.IdPagoProvDoc = ges_pagosprov.IdPagoProvDoc)) AS FechaOperacion, ".
         "ges_pagosprov.ValuacionMoneda, ".
         "IF(ges_pagosprov.Observaciones like '', ' ',ges_pagosprov.Observaciones) as Observaciones, ".
         "ges_pagosprov.EstadoCuota, ".
         "ges_pagosprov.esPlanificado ".
         "FROM ges_pagosprov ".
         "INNER JOIN ges_usuarios ON ges_pagosprov.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_moneda ON ges_pagosprov.IdMoneda = ges_moneda.IdMoneda ".
         "WHERE ges_pagosprov.Eliminado = 0 ".
         "AND ges_pagosprov.IdComprobanteProv IN (".$idcod.") ".
         "ORDER BY ges_pagosprov.IdPagoProv ASC ";


        $res = query($sql);
        $item = 1;
        $observ = "";
        while ( $row = Row($res) ) { 
	  $pdf->SetX(17); 
	  $pdf->Cell(1);

	  $documento = $row["Documento"];
	  $estado    = $row["Estado"];
	  $formapago = $row["ModoPago"];
	  $importe   = number_format($row["Importe"],2);
	  $simbolo   = $row["Simbolo"];
	  $mora      = $row["Mora"];
	  $mora      = ($mora == ' ')? ' ':number_format($mora,2);
	  $user      = $row["User"];

	  $fpagopl   = $row["FechaPago"];
	  $fpago     = ($fpagopl != ' ')? explode("~",$fpagopl):"";
	  $fpago     = ($fpagopl != ' ')? $fpago[0]:"";
	  
	  $fechaOp   = $row["FechaOperacion"];
	  $foperacion= ($fechaOp != ' ')? explode("~",$fechaOp):"";
	  $foperacion= ($fechaOp != ' ')? $foperacion[0]:"";

	  $EstadoCuota = ($row["EstadoCuota"] == 'Vencido')? $row["EstadoCuota"]:'';

	  $pdf->SetFont('Courier','',9);

	  $obs = utf8_decode($row["Observaciones"]);

	  $observ .= ($obs != ' ')? " - ".$documento.": ".$obs:"";

	  // IMPRIME LINE
	  $pdf->Cell(6,4,$item,'LR',0,'R');
	  $pdf->Cell(35,4,utf8_decode($documento),'LR',0,'L');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(25,4,$estado,'LR',0,'L');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(70,4,$formapago,'LR',0,'L');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(20,4,$fpago,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(20,4,$foperacion,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(30,4,$simbolo.$importe,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(20,4,$mora,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(25,4,$EstadoCuota,'LR',0,'C');


	  $pdf->Ln(4);	

	  //CONTADOR
	  $contador++;
	  $item++;
	  
	};
	
	while ($contador<2)
	{
          $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $pdf->Cell(6,4,"",'LR',0,'C');
	  $pdf->Cell(35,4,"",'LR',0,'c');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Cell(70,4,"",'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(30,4,"",'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');

	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

          // LINEA 3
	  $pdf->Ln(-3);	
          $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $pdf->Cell(6,4,"",'LRB',0,'C');
	  $pdf->Cell(35,4,"",'LRB',0,'c');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
	  $pdf->Cell(70,4,"",'LRB',0,'C');
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(30,4,"",'LRB',0,'C');
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');

          $pdf->Ln(4);	
          //$pdf->Cell(1);

//  IMPORTE FINAL DESGLOZADO	
// TOTAL NETO

$fecha=implota($fechahoy=date("Y-m-d"));
$hora=date("H:i");

$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$Local.":::";
$pdf->SetX(17);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);

$pdf->Ln(6);


//Obervaciones
if($observ != ''){
  $pdf->Ln(6);
  $pdf->SetX(17); 
  $pdf->SetFont('Courier','UB',11);	
  $pdf->Cell(29.5,4,utf8_decode('Observaciones'));
  $pdf->SetFont('Courier','',11);	
  $pdf->Cell(1,4,'.');
  $pdf->Ln(6);

  $obsev = explode("-", $observ);

  foreach ($obsev  as $key=>$line){
    if( $key > 0 ){
      $pdf->SetX(17); 
      $pdf->Cell(1);
      $pdf->Cell(300,4,utf8_decode("-".$line));
      $pdf->Ln(4);
    }
  }
}


$pdf->Ln(6);


@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$name = $Documento."-".$Codigo.".pdf";

$pdf->Output($name,'');
?> 
