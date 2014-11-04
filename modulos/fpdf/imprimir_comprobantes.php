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
$idcod     = $_GET["idoc"];
$totaletras    = utf8_decode($_GET["totaletras"]);
$operador      = utf8_decode($_SESSION["NombreUsuario"]);
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

$sql="SELECT			
                ges_locales.NombreComercial As Almacen,
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
 		ges_moneda.Simbolo As Moneda,
 		ges_comprobantesprov.TipoComprobante As TipoComprobante,
                IF(ges_comprobantesprov.TipoComprobante = 'AlbaranInt',(SELECT ges_locales.NombreLegal FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantesprov.IdProveedor),(SELECT ges_proveedores.NombreLegal FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantesprov.IdProveedor) ) AS Proveedor, 
                IF(ges_comprobantesprov.TipoComprobante = 'AlbaranInt',(SELECT ges_locales.NFiscal FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantesprov.IdProveedor),(SELECT ges_proveedores.NumeroFiscal FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantesprov.IdProveedor) ) AS RUC
         FROM  ges_comprobantesprov
         LEFT JOIN ges_proveedores ON ges_comprobantesprov.IdProveedor = ges_proveedores.IdProveedor
         INNER JOIN ges_pedidos    ON ges_comprobantesprov.IdPedido = ges_pedidos.IdPedido
         INNER JOIN ges_moneda     ON ges_pedidos.IdMoneda  = ges_moneda.IdMoneda
         INNER JOIN ges_locales    ON ges_pedidos.IdAlmacenRecepcion   = ges_locales.IdLocal
         INNER JOIN ges_usuarios   ON ges_comprobantesprov.IdUsuario = ges_usuarios.IdUsuario
         WHERE ges_comprobantesprov.IdComprobanteProv = ".$idcod." AND ges_comprobantesprov.Eliminado = 0";

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
$Importe   = utf8_decode($row["TotalImporte"]);
$ImporteImp   = utf8_decode($row["ImporteImpuesto"]);
$ImporteBase  = utf8_decode($row["ImporteBase"]);
$ImportePdte  = utf8_decode($row["ImportePendiente"]);
$ImportePerc  = utf8_decode($row["ImportePercepcion"]);
$Percepcion   = utf8_decode($row["Percepcion"]."%");
$IGV          = utf8_decode($row["Impuesto"]."%");
$EstadoDoc    = utf8_decode($row["EstadoDocumento"]);
$EstadoPago   = utf8_decode($row["EstadoPago"]);
$Usuario      = utf8_decode($row["Usuario"]);
$Observaciones= utf8_decode($row["Observaciones"]);
$ImportePago  = $row["ImportePago"];
$ImporteFlete = $row["ImporteFlete"];

//PDF ESTRUCTURA
$pdf=new PDF();
$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(90);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(125,4,$Documento." ".$Codigo );

$pdf->Ln(6);

//FECHA
$pdf->SetX(130);
$pdf->SetFont('Courier','',9);	
$pdf->Cell(130,4,"Registrado el ".$Registro);
$pdf->Ln(8);
//." (".$hora.")"
// NOMBRE   
$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Proveedor: '));

$pdf->SetX(38); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Proveedor);
 
$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC: '));

$pdf->SetX(148); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC);

$pdf->Ln(4);
$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Moneda:'));

$pdf->SetX(32); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Monedadet);

$pdf->SetX(85); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado Doc :'));

$pdf->SetX(108); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$EstadoDoc);

$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Emisión:'));

$pdf->SetX(168); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Emision);

$pdf->Ln(4);
$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Tipo Pago:'));

$pdf->SetX(40); 
$pdf->SetFont('Courier','',9);
$pdf->Cell(120,4,$ModoPago);

$pdf->SetX(85); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado Pago:'));

$pdf->SetX(108); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$EstadoPago);

$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Pago:'));
 
$pdf->SetX(168); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Pago);


//Detalle
$pdf->Ln(6);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(18,4,utf8_decode('Detalle'));
$pdf->SetX(32.5); 
$pdf->SetFont('Courier','B',10);	
$pdf->Cell(1,4,'.');

$pdf->Ln(6);

// las lneas delos ARTICULOS 
$pdf->SetX(17); 
$pdf->Cell(1);

$pdf->SetFillColor(210,210,210);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',8);
	
$pdf->Cell(115,4,utf8_decode("Descripción"),1,0,'D',1);
$pdf->Cell(18,4,"Cantidad",1,0,'C',1);
$pdf->Cell(20,4,"P.U. (".$Moneda.")",1,0,'C',1);
$pdf->Cell(25,4,"P.C. (".$Moneda.")",1,0,'C',1);


$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LR',0,'C');	
$pdf->Cell(109,4,"",'LR',0,'C');
$pdf->Cell(18,4,"",'LR',0,'C');	
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(2);	

      $contador=1;
      $totalbruto=0;
      $totalbrutounid=0;
      $totaldescuento=0; 

      $sql = 
	  "SELECT ges_productos.Referencia,".
	  "       ges_productos.IdProducto,".
	  "       ges_productos.CodigoBarras,".
	  "       CONCAT(ges_productos_idioma.Descripcion,' ',".
	  "       if(ges_marcas.Marca='...','',ges_marcas.Marca),' ',".
	  "       if(ges_colores.Color='...','',ges_colores.Color),' ',".
	  "       if(ges_tallas.Talla='...','',ges_tallas.Talla),' ',".
	  "       if(ges_laboratorios.NombreComercial='.','',ges_laboratorios.NombreComercial)) as Producto,".
	  "       ges_pedidosdet.Unidades,".
	  "       ges_pedidosdet.CostoUnidad as Costo, ".
	  "       ges_pedidosdet.PrecioUnidad as Precio, ".
	  "       ges_pedidosdet.Descuento, ".
	  "       ges_pedidosdet.Importe as ImporteUnd, ".
	  "       IF ( ges_pedidosdet.Lote like '', '',ges_pedidosdet.Lote) as LT, ". 
          "       IF ( DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%e %b %Y') IS NULL, 
                    '',
                    DATE_FORMAT(ges_pedidosdet.FechaVencimiento, '%d/%m/%y') ) 
                    As FV,".
	  "       ges_pedidosdet.IdPedidoDet, ".
	  "       ges_pedidosdet.IdPedido, ".
	  "       ges_pedidosdet.Serie, ".
          "       ges_productos.VentaMenudeo, ".
          "       ges_productos.IdContenedor, ".
          "       ges_contenedores.Contenedor, ".
	  "       ges_productos.UnidadesPorContenedor, ".
	  "       ges_productos.UnidadMedida, ".
	  "       ges_pedidos.IdPedido ".

	  "FROM   ges_pedidosdet ".
	  "LEFT  JOIN ges_productos ON ges_pedidosdet.IdProducto = ges_productos.IdProducto ".
	  "INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	  "INNER JOIN ges_tallas       ON ges_productos.IdTalla  = ges_tallas.IdTalla ".
	  "INNER JOIN ges_colores      ON ges_productos.IdColor  = ges_colores.IdColor ".
	  "INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	  "INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
	  "INNER JOIN ges_contenedores ON ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
          "INNER JOIN ges_pedidos       ON ges_pedidosdet.IdPedido  = ges_pedidos.IdPedido ".
	  "WHERE ges_pedidosdet.IdPedido IN (".$idcod.") ".
	  "AND   ges_productos_idioma.IdIdioma = 1 ".
	  "AND   ges_tallas.IdIdioma           = 1 ".
	  "AND   ges_colores.IdIdioma          = 1 ".
	  "AND   ges_pedidosdet.Eliminado      = 0 ";

        $res = query($sql);
        $item = 1;
        while ( $row = Row($res) ) { 
	  $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $codarticulo = $row["IdProducto"];

	  // IMPRIME LINE
	  $cantidad=$row["Unidades"];
	  //UNID MEDIDA
	  $cantunidmed=$row["UnidadMedida"];
	  $pdf->SetFont('Courier','',9);
	  //### TEXT DESCRIPCION
          $codigobarras = $row["CodigoBarras"];
          $descripcion  = utf8_decode($row["Producto"]);

	  //Lote y Fecha de vencimiento
          $lote  = utf8_decode($row["LT"]);
	  $lote  = "Lt.".$lote;
	  $lote  = (utf8_decode($row["LT"])!='')? $lote : '';
          $fv    = utf8_decode($row["FV"]);
	  $fv    = "Fv.".$fv;
	  $fv    = (utf8_decode($row["FV"])!='')? $fv : '';

	  if($row["VentaMenudeo"]){
	    $upc      = $row["UnidadesPorContenedor"];
	    $resto    = ( $cantidad>$upc && $upc)? $cantidad%$upc : $cantidad;
	    $cantcont = ( $cantidad>$upc && $upc)?  round(($cantidad-$resto)/$upc * 100) / 100 : 0;   
	    $detcant  = "Cant.".$cantcont."".$row["Contenedor"]."+".$resto."".$cantunidmed;
	    $contund  =  $upc.$cantunidmed."/".$row["Contenedor"];
	  }else{
	    $detcant  = "";
	    $contund  = "";
	  }
	  //SERIES
	  $seriestext = '';
	  $series     = array();
	  if($row["Serie"]==1){

	    $series = getSeriesProductoPedidoDet($row["IdPedidoDet"],$codarticulo);

	    if( count($series)< 21 )
	      $seriestext = "NS. ".implode($series," ");
	  }

	  $descripcion_0 =
	    $codigobarras." ".
	    $descripcion."".
	    $contund." ".
	    $detcant." ".
	    $lote." ".
	    $fv." ".
	    $seriestext;

	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($descripcion_0,55);

	  //PRECIO
	  $PrecioUnd = number_format($row["Precio"],2);
	  $ImporteUnd=number_format($row["ImporteUnd"],2);
	  
	  //DECUENTO
	  $Dcto = $row["Descuento"];

	  // IMPRIME LINE
	  $pdf->Cell(6,4,$item,'LR',0,'R');
	  $pdf->Cell(109,4,utf8_decode($acotado[0]),'LR',0,'L');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(18,4,$cantidad.' '.$cantunidmed,'LR',0,'R');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(20,4,$PrecioUnd,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(25,4,$ImporteUnd,'LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  $pdf->SetFont('Courier','',9);
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(6,4,'','LR',0,'R');
	      $pdf->Cell(109,4,utf8_decode($line),'LR',0,'L');
	      $pdf->Cell(18,4,"",'LR',0,'R');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
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
	  $pdf->Cell(109,4,"",'LR',0,'C');
	  $pdf->Cell(18,4,"",'LR',0,'C');	
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
 	  $pdf->Cell(109,4,"",'LRB',0,'C');
          $pdf->Cell(18,4,"",'LRB',0,'C');
	  $pdf->Cell(20,4,"",'LRB',0,'C');
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

//  IMPORTE FINAL DESGLOZADO	

$ImporteBase = number_format($ImporteBase,2);
$ImporteImp  = number_format($ImporteImp,2);
$Importe     = number_format($Importe,2);

// HALLAMOS TOTALES
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200,200,200);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',9);
$pdf->SetX(150);		
$pdf->Cell(1);
$pdf->Cell(20,4,"Bruto Neto",1,0,'R',1);
$pdf->Cell(25,4,$ImporteBase,1,0,'R',1);
$pdf->Ln(4);
$pdf->SetX(150);		
$pdf->Cell(1);
$pdf->Cell(20,4,"Total IGV",1,0,'R',1);
$pdf->Cell(25,4,$ImporteImp,1,0,'R',1);
$pdf->Ln(4);
$pdf->SetX(150);		
$pdf->Cell(1);
$pdf->Cell(20,4,"Total Neto",1,0,'R',1);
$pdf->Cell(25,4,$Importe,1,0,'R',1);

if($ImportePerc > 0){
  $pdf->Ln(4);
  $pdf->SetX(150);		
  $pdf->Cell(1);
  $pdf->Cell(20,4,utf8_decode("Percepción"),1,0,'R',1);
  $pdf->Cell(25,4,number_format($ImportePerc,2),1,0,'R',1);
}

if($ImporteFlete > 0){
  $pdf->Ln(4);
  $pdf->SetX(150);		
  $pdf->Cell(1);
  $pdf->Cell(20,4,"Flete",1,0,'R',1);
  $pdf->Cell(25,4,number_format($ImporteFlete,2),1,0,'R',1);
}

$pdf->Ln(4);
$pdf->SetX(150);		
$pdf->Cell(1);
$pdf->Cell(20,4,"Total Pago",1,0,'R',1);
$pdf->Cell(25,4,number_format($ImportePago,2),1,0,'R',1);

$pdf->Ln(6);


//IMPORTE LETRAS
$pdf->SetFont('Courier','B',10);	
$pdf->SetX(17);	
$pdf->Cell(300,4,"SON:".utf8_decode($totaletras));
$pdf->Ln(4);
//Obervaciones
if($Observaciones != ' '){
  $pdf->Ln(6);
  $pdf->SetX(17); 
  $pdf->SetFont('Courier','UB',11);	
  $pdf->Cell(29.5,4,utf8_decode('Observaciones'));
  $pdf->SetFont('Courier','',11);	
  $pdf->Cell(1,4,'.');
  $pdf->Ln(6);

  $obsev = explode("-", $Observaciones);

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
$pdf->Cell(300,4,utf8_decode("- Los precios incluye IGV(".$IGV.")"));
$pdf->Ln(4);

if($Percepcion > 0 && $ImportePerc > 0.0 ){
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(300,4,utf8_decode("- ".$Documento." sujeto a percepción"));
$pdf->Ln(4);
}

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$name = $Documento."-".$Codigo.".pdf";

$pdf->Output($name,'');
?> 
