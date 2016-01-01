<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");
$IdLocal      = getSesionDato("IdTiendaDependiente");
if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$nroBoleta     = $_GET["nro"];
$nroSerie      = $_GET["nroSerie"];
$codcliente    = CleanID($_GET["codcliente"]);
$totaletras    = $_GET["totaletras"];
$IdComprobante = CleanID($_GET["idcomprobante"]);
$operador      = ($_GET["nombreusuario"])? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;


//Comprobante
if ($codcliente==0){   
  $sql = "Select * 
               from    ges_comprobantes 
               where   IdComprobante = '$IdComprobante'
               and     IdLocal       = '$IdLocal'
               and     Eliminado     = '0'";
}else{
  $sql = "Select *  
               from  ges_comprobantes,ges_clientes 
               where ges_comprobantes.IdComprobante = '$IdComprobante' 
               and   ges_comprobantes.IdCliente     = '$codcliente' 
               and   ges_comprobantes.IdLocal       = '$IdLocal' 
               and   ges_comprobantes.IdCliente     = ges_clientes.IdCliente 
               and   ges_comprobantes.Eliminado     = '0'";
}
$res       = query($sql);
$lafila    = Row($res);

//Cliente
if ($codcliente==0){
  $nombre    = "";
  $nif       = "";
  $direccion = "";
}else{
  $sql = "SELECT NombreComercial,
                 NumeroFiscal,
                 Direccion,
                 NombreLegal,
                 TipoCliente
          FROM   ges_clientes 
          WHERE  IdCliente='$codcliente'";
  $res       = query($sql);
  $row       = Row($res);
  $nombre    = utf8_decode($row["NombreComercial"]);
  $nif       = utf8_decode($row["NumeroFiscal"]);
  $direccion = utf8_decode($row["Direccion"]);
  $cliente   = utf8_decode($row["TipoCliente"]);
  $nombre    = ($cliente == 'Empresa')? utf8_decode($row["NombreLegal"]):$nombre;
  $nombre    = str_replace('&#038;','&',$nombre);
}

$codlist=$lafila["SerieComprobante"]." - ".$lafila["NComprobante"];

//Imprime Comrpobante
//$pdf= new PDF (P,mm,array(211,211));
$pdf = new PDF ( 'L' , 'mm' , array ( 205 , 165 ));

$pdf->Open();
$pdf->AddPage();

$pdf->Ln(0);

    $pdf->Cell(95);
    $pdf->Cell(80,4,"",'',0,'C');
    $pdf->Ln(-10);
	
    $Image ='./logo/gpos_encabezado_pdf.png';
    $pdf->Cell( 40, 0, $pdf->Image($Image, $pdf->GetX(), $pdf->GetY(), 0.0), 0, 0, 'L', false );

    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.2);
    $pdf->SetFont('Arial','B',10);	
    $pdf->Ln(38);

    $pdf->Ln(-8);
    $pdf->SetX( 160);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(20,4,$codlist);

    $pdf->Ln(8);
    list($anho,$mes,$dia)=explode('-',$lafila["FechaComprobante"]);
    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(5); 
    $pdf->Cell(130,4,"Fecha       : ");
    $pdf->SetX(28); 
    $pdf->SetFont('Arial','B',8);	
    $pdf->Cell(130,4,$dia."/".$mes."/".$anho);

    //NOMBRE   
    $pdf->Ln(4);
    $pdf->SetX(5); 
    $pdf->SetFont('Arial','B',10);	
    $pdf->Cell(130,4,utf8_decode("Señor(es) : "));
    $pdf->SetX(28); 
    $pdf->SetFont('Arial','B',8);	
    $pdf->Cell(130,4,$nombre);

    //DIRECCION				
    $pdf->Ln(4);
    $pdf->SetX(5);
    $pdf->SetFont('Arial','B',10);	
    $pdf->Cell(70,4,utf8_decode("Dirección : "));
    $pdf->SetX(28);	
    $pdf->SetFont('Arial','B',8);	
    $pdf->Cell(130,4,$direccion);

    $pdf->Ln(6);
$pdf->SetX(5); 
$pdf->Cell(1);

$pdf->SetFillColor(210,210,210);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',8);
	

$pdf->Cell(6,4,utf8_decode("#"),1,0,'D',1);
$pdf->Cell(110,4,utf8_decode("Descripción"),1,0,'C',1);
$pdf->Cell(25,4,"Cantidad",1,0,'C',1);
$pdf->Cell(20,4,"Precio",1,0,'C',1);
$pdf->Cell(30,4,"Importe",1,0,'C',1);

$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(5); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LR',0,'c');
$pdf->Cell(110,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(30,4,"",'LR',0,'C');

	
//####################### las lneas delos ARTICULOS ###################

$pdf->Ln(1);
			
$IdComprobante=$lafila["IdComprobante"];

$contador=1;

$sql = 
      "SELECT  ges_comprobantesdet.IdProducto, ".
      "        group_CONCAT(ges_comprobantesdet.IdPedidoDet) as IdPedidoDet, ".
      "        IF(Concepto <> '',Concepto,'') as Concepto, ".
      "        SUM(ges_comprobantesdet.Cantidad) as Cantidad, ".
      "        AVG(ges_comprobantesdet.CostoUnitario) as CostoUnitario, ".
      "        AVG(ges_comprobantesdet.Precio) as Precio, ".
      "        AVG(ges_comprobantesdet.Descuento) as Descuento, ".
      "        SUM(ges_comprobantesdet.Importe) as Importe, ".
      "        ges_comprobantesdet.CodigoBarras, ".
      "        ges_productos.MetaProducto, ".
      "        ges_comprobantesdet.Serie, ".
      "        ges_productos.IdLabHab, ".
      "        ges_productos.IdProducto as codarticulo, ".
      "        ges_comprobantesdet.Referencia as referencia, ".
      "        ges_productos_idioma.Descripcion as descripcion, ".
      "        ges_marcas.Marca,ges_modelos.Color as presentacion,".
      "        ges_detalles.Talla as subpresentacion , ".
      "        ges_laboratorios.NombreComercial as laboratorio, ".
      "        ges_productos.IdContenedor, ".
      "        ges_productos.UnidadesPorContenedor, ".
      "        ges_productos.UnidadMedida ".
      "FROM    ges_comprobantesdet,ges_productos,ges_productos_idioma, ".
      "        ges_detalles,ges_modelos,ges_marcas,ges_laboratorios ".
      "WHERE   ges_comprobantesdet.IdComprobante = '$IdComprobante' ".
      "AND     ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
      "AND     ges_comprobantesdet.IdProducto = ges_productos.IdProducto ".
      "AND     ges_productos.IdColor = ges_modelos.IdColor ".
      "AND     ges_productos.IdTalla = ges_detalles.IdTalla ".
      "AND     ges_marcas.IdMarca = ges_productos.IdMarca ".
      "AND     ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
      "AND     ges_comprobantesdet.Eliminado = '0' ".
      "GROUP BY ges_comprobantesdet.CodigoBarras"; 

    $res  = query($sql);
    $item = 1;
    while ( $row = Row($res) ) { 
      $cantidad    = $row["Cantidad"]." ".$row["UnidadMedida"];
      $precio      = number_format(round($row["Precio"]),2);
      $codarticulo = $row["IdProducto"];
      
      //TEXT DESCRIPCION
      $codigobarras = $row["CodigoBarras"];
      $descripcion = utf8_decode($row["descripcion"]);
      $marca       = utf8_decode($row["Marca"]);
      $modelo      = utf8_decode($row["presentacion"]);
      $detalle     = utf8_decode($row["subpresentacion"]);
      $laboratorio = utf8_decode($row["laboratorio"]);

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

	if( count($series)< 21 )
	  $seriestext = "N/S: ".implode($series," ");
      }

      //META PRODUCTO
      $itemmprod = array();
      $acotmp    = array();
      $acotmp    = getItemMetaProducto($row["MetaProducto"],
				       $row["Serie"],
				       $series,
				       $codarticulo,
				       46);

      //PRODUCTO
      $descripcion_0 =
	//$codigobarras." ".
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
      $acotado = getItemProducto(utf8_decode($descripcion_0),46);

      $fechainicio = explode("-",$lafila["FacturacionAnterior"]);
      $fechafin    = explode("-",$lafila["FechaComprobante"]);

      // IMPRIME LINE
      $pdf->SetX(5); 
      $pdf->Cell(1);
      $pdf->SetFont('Courier','',8);
      $pdf->Cell(6,4,$item,'LR',0,'C');	
      $pdf->Cell(110,4,utf8_decode($acotado[0]),'LR',0,'L');
      $pdf->Cell(25,4,$cantidad,'LR',0,'R');
      $pdf->Cell(20,4,$precio,'LR',0,'R');
      $pdf->Cell(30,4,number_format($row["Importe"],2),'LR',0,'R');
      $pdf->Ln(4);	

      //TEXT EXTRA LINE run
      foreach ($acotado as $key=>$line){
	if($key>0 && $key < 27 ){
          $pdf->SetX(5); 
	  $pdf->Cell(1);
	  $pdf->Cell(6,4,"",'LR',0,'C');
	  $pdf->Cell(110,4,utf8_decode($line),'LR',0,'L');
	  $pdf->Cell(25,4,"",'LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');	
	  $pdf->Cell(30,4,"",'LR',0,'C');
	  $pdf->Ln(4);
	  $contador++;
	  $acotadoext = 0;
	}
      }

      //TEXT META PRODUCTO
      foreach ($acotmp  as $key=>$linemp){
	if( $key < 20 ){
          $pdf->SetFont('Courier','',8);
	  $pdf->Cell(1);
          $pdf->SetX(5); 
	  $pdf->Cell(6,4,"",'LR',0,'C');
	  $pdf->Cell(100,4,utf8_decode($linemp),'LR',0,'L');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(30,4,"",'LR',0,'C');
	  $pdf->Ln(4);
	  $contador++;
	  $acotadoext = 0;
	}
      }
      //CONTADOR
      $contador++;


    while ($contador<10)
      {
	$pdf->SetX(5); 
	$pdf->Cell(1);
	$pdf->Cell(6,4,"",'LR',0,'C');
	$pdf->Cell(110,4,"",'LR',0,'C');	
	$pdf->Cell(25,4,"",'LR',0,'C');
	$pdf->Cell(20,4,"",'LR',0,'C');
	$pdf->Cell(30,4,"",'LR',0,'C');
	$pdf->Ln(4);	
	$contador=$contador +1;
      }
    }

$pdf->Ln(-3);	
$pdf->SetX(5); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'LRB',0,'c');
$pdf->Cell(110,4,"",'LRB',0,'C');
$pdf->Cell(25,4,"",'LRB',0,'C');
$pdf->Cell(20,4,"",'LRB',0,'C');
$pdf->Cell(30,4,"",'LRB',0,'C');
$pdf->Ln(4);

$fecha   = implota($fechahoy=date("Y-m-d"));
$hora    = date("H:i");
$codlist = $lafila["SerieComprobante"]." - ".$lafila["NComprobante"];
$esTPV   = getSesionDato("TipoVentaTPV");
$esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';
$pdf->SetX(11); 
$pdf->Cell(150,4,"TPV: ".$esTPV." / OP: ".$operador." / F.Imp.: ".$fecha."  ".$hora);
	

$pdf->SetX(147); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(20,4,"Total:",'LRB',0,'C');

$pdf->SetX(167); 
$pdf->SetFont('Courier','B',9);
$pdf->Cell(30,4,'S/.'.number_format($lafila["TotalImporte"],2),'LRB',0,'C');	


$pdf->Ln(10);	

//###################### Imprime Letras
$pdf->SetFont('Arial','B',8);	
$pdf->SetX(5);
$pdf->Cell(1);	
$pdf->Cell(200,4,"SON ".utf8_decode($totaletras));

$pdf->Ln(6);


$pdf->SetFont('Arial','B',10);	
$pdf->SetX(5);
$pdf->Cell(1);	
$pdf->Cell(200,4,"NOTA: ");



$pdf->Ln(4);
$pdf->SetX(5);
$pdf->Cell(1);	
$pdf->SetFont('Arial','B',6);	
$pdf->MultiCell(60,4,utf8_decode("* Después de camcelado el presente documento sírvase cambiar con la Boleta de Venta"));

$pdf->SetX(5);
$pdf->Cell(1);	
$pdf->MultiCell(60,4,utf8_decode("* Este Documento no es comprobante de pago"));

$pdf->Ln(-6);
$pdf->SetFont('Arial','B',10);	
$pdf->SetX(110);
$pdf->Cell(60,4,utf8_decode("-------------------"));
$pdf->Ln(3);
$pdf->SetX(110);
$pdf->Cell(60,4,utf8_decode("CANCELADO"));

	
//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "TICKET-SUSCRIPCION-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroBoleta.".pdf";
$pdf->Output($name,'');
?> 
