<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunesexp.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal   = getSesionDato("IdTiendaDependiente");
$Moneda    = getSesionDato("Moneda");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$nroAlbaran    = $_GET["nroAlbaranInt"];
$nroSerie      = $_GET["nroSerie"];
$codcliente    = $_GET["codcliente"];
$totaletras    = $_GET["totaletras"];
$IdComprobante = $_GET["idcomprobante"];
$operador      = ($_GET["nombreusuario"])? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;


 $sql = "SELECT
                ges_usuarios.Nombre As Vendedor, 
                ges_comprobantes.SerieComprobante,
                ges_comprobantes.NComprobante,
                DATE_FORMAT(ges_comprobantesnum.Fecha,'%d/%m/%Y %H:%i') as Fecha,
                ges_comprobantes.TotalImporte,
                ges_comprobantes.ImportePendiente,
                ges_comprobantesstatus.Status, 
                ges_comprobantes.IdComprobante,
                ges_comprobantes.Destinatario,
                ges_locales.NFiscal as RUC1,
                CONCAT(ges_comprobantestipo.Serie,'-',ges_comprobantesnum.NumeroComprobante) as NumeroComprobante,
                ges_comprobantestipo.TipoComprobante as TipoDocumento,
                IF(Destinatario='Local',(SELECT CONCAT('Interno - ',ges_locales.NombreLegal) FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantes.IdCliente),  (SELECT CONCAT('Externo - ',ges_proveedores.NombreLegal) FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantes.IdCliente)) as Cliente, 
                IF(Destinatario='Local',(SELECT ges_locales.NFiscal FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantes.IdCliente),  (SELECT ges_proveedores.NumeroFiscal FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantes.IdCliente)) as RUC2,
                ges_comprobantes.IdCliente,
                ges_locales.NombreLegal as Local, ges_comprobantes.IdLocal,
                ges_motivoalbaran.MotivoAlbaran
    		FROM ges_comprobantes " .
    		"LEFT JOIN ges_clientes ON ges_comprobantes.IdCliente = ges_clientes.IdCliente
                INNER JOIN ges_comprobantesstatus ON ges_comprobantes.Status = ges_comprobantesstatus.IdStatus
                INNER JOIN ges_locales ON ges_comprobantes.IdLocal = ges_locales.IdLocal
                INNER JOIN ges_usuarios ON ges_comprobantes.IdUsuario = ges_usuarios.IdUsuario
                INNER JOIN ges_comprobantesnum ON ges_comprobantesnum.IdComprobante = ges_comprobantes.IdComprobante
                INNER JOIN ges_comprobantestipo ON  ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante 
                INNER JOIN ges_motivoalbaran ON ges_comprobantesnum.IdMotivoAlbaran = ges_motivoalbaran.IdMotivoAlbaran
                WHERE ges_comprobantes.Eliminado = 0
                AND  ges_comprobantesnum.Eliminado = 0
                AND  ges_comprobantesnum.Status = 'Emitido'
                AND  ges_comprobantes.IdComprobante = '$IdComprobante'
                AND  ges_comprobantes.IdLocal       = '$IdLocal'
                AND  ges_comprobantestipo.TipoComprobante = 'AlbaranInt'";

  $res       = query($sql);
  $row       = Row($res);

$Documento = $row["TipoDocumento"];
$Codigo    = $row["NumeroComprobante"];
$Usuario   = utf8_decode($row["Vendedor"]);
$RUC1      = $row["RUC1"];
$RUC2      = $row["RUC2"];
$Local     = utf8_decode($row["Local"]); 
$Cliente   = utf8_decode($row["Cliente"]);
$Registro  = utf8_decode($row["Fecha"]);
$MotivAlba = utf8_decode($row["MotivoAlbaran"]);
$EstadoDoc = "Emitido";
$FechaEmi  = explode(" ",$Registro);
$FechaEmi  = $FechaEmi[0];
$ModoPago  = '';
$EstadoPago = '';
$Pago       = '';


//Imprime Comrpobante
//$pdf=new PDF();
$pdf = new PDF ( 'P' , 'mm' , array ( 210 , 297 ));

$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(90);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(125,4,strtoupper($Documento." ".$Codigo) );

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
$pdf->Cell(120,4,$Local);
 
$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC: '));

$pdf->SetX(148); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC1);

$pdf->Ln(4);

$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Cliente  : '));

$pdf->SetX(38); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Cliente);
 
$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC: '));

$pdf->SetX(148); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC2);

$pdf->Ln(4);

$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Motivo   :'));

$pdf->SetX(38); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$MotivAlba);

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
$pdf->Cell(120,4,$FechaEmi);

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
$pdf->Cell(20,4,"C.U. (".$Moneda[1]['S'].")",1,0,'C',1);
$pdf->Cell(25,4,"C.T. (".$Moneda[1]['S'].")",1,0,'C',1);


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


$sql = 
	  " SELECT ges_comprobantesdet.IdProducto, ".
	  "        group_CONCAT(ges_comprobantesdet.IdPedidoDet) as IdPedidoDet, ".
	  "        IF(Concepto <> '',Concepto,'') as Concepto, ".
	  "        SUM(ges_comprobantesdet.Cantidad) as Cantidad, ".
	  "        AVG(ges_comprobantesdet.CostoUnitario) as CostoUnitario, ".
	  "        AVG(ges_comprobantesdet.Precio) as Precio, ".
	  "        AVG(ges_comprobantesdet.Descuento) as Descuento, ".
	  "        SUM(ges_comprobantesdet.Importe) as Importe, ".
	  "         ges_comprobantesdet.CodigoBarras, ".
	  "         ges_productos.MetaProducto, ".
	  "         ges_comprobantesdet.Serie, ".
	  "         ges_productos.IdLabHab, ".
	  "         ges_productos.IdProducto as codarticulo, ".
	  "         ges_comprobantesdet.Referencia as referencia, ".
	  "         ges_productos_idioma.Descripcion as descripcion, ".
	  "         ges_marcas.Marca,ges_modelos.Color as presentacion, ".
	  "         ges_detalles.Talla as subpresentacion , ".
	  "         ges_laboratorios.NombreComercial as laboratorio, ".
	  "         ges_productos.IdContenedor, ".
	  "         ges_productos.UnidadesPorContenedor, ".
	  "	    ges_productos.UnidadMedida ".
	  "FROM     ges_comprobantesdet,ges_productos,ges_productos_idioma, ".
	  "         ges_detalles,ges_modelos,ges_marcas,ges_laboratorios ".
	  "WHERE    ges_comprobantesdet.IdComprobante = '".$IdComprobante."' ".
	  "AND      ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	  "AND      ges_comprobantesdet.IdProducto = ges_productos.IdProducto ".
	  "AND      ges_productos.IdColor = ges_modelos.IdColor  ".
	  "AND      ges_productos.IdTalla = ges_detalles.IdTalla ".
	  "AND      ges_marcas.IdMarca = ges_productos.IdMarca ".
	  "AND      ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	  "AND      ges_comprobantesdet.Eliminado = '0' ".
	  "GROUP BY ges_comprobantesdet.CodigoBarras"; 

        $res=query($sql);

    	$contador=1;
        $totalbruto='';
        $totaldescuento=''; 
        $item = 1;

        while ( $row = Row($res) ) { 
	  $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $codarticulo=$row["IdProducto"];
	  $pdf->SetFont('Arial','',9);

	  // IMPRIME LINE
	  $cantidad=$row["Cantidad"];
	  // CANTIDAD


	  // UNID MEDIDA
          $cantunidmed = utf8_decode($row["UnidadMedida"]);

	  // IMPRIME LINE
	  //$pdf->Cell(20,4, $cantunidmed ,'LR',0,'C');	

	  $pdf->SetFont('Arial','',9);
	  // CADENA TEXT DESCRIPCION 

	  // TEXT DESCRIPCION
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
	  $seriestext   = '';
	  $series       = array();

	  if($row["Serie"]==1){

	    $series = getSeriesVenta2IdProducto($IdComprobante,$codarticulo);

	    if( count($series)< 90 )
	      $seriestext = "N/S: ".implode($series," ");
	  }

	  //META PRODUCTO ITEM
	  $itemmprod = array();
	  $acotmp    = array();
	  $acotmp    = getItemMetaProducto($row["MetaProducto"],$row["Serie"],$series,$codarticulo,74);

	  $descripcion_0 =
	    $codigobarras." ".
	    $descripcion." ".
	    $marca." ".
	    $modelo." ".
	    $detalle." ".
	    $laboratorio." ".
	    $seriestext." ***";
	  
	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($descripcion_0,74);
	  
	  //  PRECIO
	  $precio=$row["Precio"];
	  $precio=round($precio * 100) / 100; 
	  $precio=number_format($precio,2);	  


	  //  IMPORTE
	  $Costo   = number_format($row["CostoUnitario"],2);
	  $importe = round($cantidad*$Costo,2);

	  // BRUTO NETO
	  $importe=number_format($importe,2);

	  // IMPRIME LINE
	  $pdf->Cell(6,4,$item,'LR',0,'C');	
	  $pdf->Cell(109,4,$acotado[0],'LR',0,'L');
          $pdf->Cell(18,4,$cantidad.' '.$cantunidmed ,'LR',0,'C');	
	  $pdf->Cell(20,4,$Costo,'LR',0,'R');
 	  $pdf->Cell(25,4,$importe,'LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->Cell(1);
	      $pdf->Cell(6,4,"",'LR',0,'C');
	      $pdf->Cell(109,4,$line,'LR',0,'L');
	      $pdf->Cell(18,4,"",'LR',0,'C');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }

	  //TEXT META PRODUCTO
	  foreach ($acotmp  as $key=>$linemp){
	    if( $key < 20 ){
	      $pdf->SetFont('Arial','',7.5);
	      $pdf->Cell(1);
	      $pdf->Cell(6,4,"",'LR',0,'C');
	      $pdf->Cell(109,4,$linemp,'LR',0,'L');
	      $pdf->Cell(18,4,"",'LR',0,'C');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	      //$pdf->SetFont('Arial','',8);
	    }
	  }

	  // CONTADOR
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

//################### MENSAJE FOOTER 
          //############## LINEA 2
          $fecha   = implota($fechahoy=date("Y-m-d"));
          $hora    = date("H:i");
          $codlist = $Codigo; 
          $esTPV   = getSesionDato("TipoVentaTPV");
          $esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';

          //############## LINEA 3
          $pdf->Ln(-3);	
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(6,4,"",'LRB',0,'C');
          $pdf->Cell(109,4,"",'LRB',0,'C');
 	  $pdf->Cell(18,4,"",'LRB',0,'C');	
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
	  $pdf->Ln(6);	

          $pdf->SetX(18); 
          $pdf->SetFont('Arial','',9);
          $pdf->Cell(140,4,"TPV: ".$esTPV." / Guia Remision Traslado Interno: ".$nroSerie."-".$nroAlbaran." / CS: ".$codlist." / OP: ".$operador." / F.Imp.: ".$fecha."  ".$hora);
//#######################  final de la Albaran

//####################################  IMPORTE FINAL DESGLOZADO	
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(4);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','',10);
	

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "ALBARAN-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroAlbaran.".pdf";
$pdf->Output($name,'');
?> 
