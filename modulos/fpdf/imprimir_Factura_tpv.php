<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");
$IdLocal      = getSesionDato("IdTiendaDependiente");
if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$nroFactura    = $_GET["nroFactura"];
$nroSerie      = $_GET["nroSerie"];
$codcliente    = $_GET["codcliente"];
$totaletras    = $_GET["totaletras"];
$IdComprobante = $_GET["idcomprobante"];
$operador      = ($_GET["nombreusuario"])? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$IGV	       = getSesionDato("IGV");
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;

if ($codcliente==0){
  $nombre="";
  $nif="";
  $direccion="";
}else{
  $sql=
    "SELECT NombreComercial as 'nombre', ".
    "       NumeroFiscal as 'nif', ".
    "       Direccion, ".
    "       NombreLegal, ".
    "       TipoCliente ".
    "FROM   ges_clientes ".
    "WHERE  IdCliente='$codcliente'";
  $res=query($sql);
  $row=Row($res);
  $nombre= utf8_decode($row["nombre"]);
  $nif=$row["nif"];
  $direccion = utf8_decode($row["Direccion"]); 
  $cliente   = utf8_decode($row["TipoCliente"]);
  $nombre    = ($cliente == 'Empresa')? utf8_decode($row["NombreLegal"]):$nombre;
}

$sql =
  "Select *  ".
  "       from ".
  "       ges_comprobantes ".
  "where  ges_comprobantes.IdComprobante='$IdComprobante' ".
  "and    ges_comprobantes.IdLocal='$IdLocal' ".
  "and    ges_comprobantes.Eliminado='0'";
$res = query($sql);
$lafila=Row($res);

//$pdf=new PDF();
$pdf = new PDF ( 'P' , 'mm' , array ( 210 , 297 ));

$pdf->Open();
$pdf->AddPage();
$pdf->Ln(2);

    $pdf->Cell(95);
    $pdf->Cell(80,4,"",'',0,'C');
    $pdf->Ln(5);
	
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.2);
    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX( 130);
    $pdf->Ln(40);					

// Datos Cliente 

    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(27); 
    // NOMBRE   
    $pdf->Cell(130,4,$nombre);
    //$igv=$lafila["IGV"]; 
    $igv=0;
    $pdf->Ln(6);
    $pdf->SetX(180);
    // NUM GUIA REMIS.   
    $pdf->Cell(70,4,"");

    $pdf->SetX(27);	
    // DIRECCION				
    $pdf->Cell(130,4,$direccion);

    $pdf->SetX(131);
    // RUC
    $pdf->Cell(130,4, $nif);
//    $pdf->Cell(130,4,"42521542");
    $pdf->SetX(130);
//    $pdf->Cell(70,1,"",'LRB',0,'L',1);
    $pdf->Cell(70,4,"");

    $pdf->SetX(171);
    // FECHA FACTURA
    list($anho,$mes,$dia)=explode('-',$lafila["FechaComprobante"]);
    $pdf->Cell(70,4,$dia);
    $pdf->SetX(182);
    $pdf->Cell(70,4,$mes);
    $pdf->SetX(192);
    $pdf->Cell(70,4,$anho);
//    $pdf->Cell(35,1,"",'LRB',0,'L',1);
    $pdf->SetX(177);
    $pdf->Cell(70,4,"");
    $pdf->Ln(2);		
/*    $pdf->Cell(70,4,"Nro COMPR. DE PAGO: "." " . $lafila3["IdPais"]);
    $pdf->Ln(6);					
*/
	
// las lneas delos ARTICULOS 
$pdf->Cell(1);
	
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);

$pdf->Ln(8);
			
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','',9);

$IdComprobante=$lafila["IdComprobante"];
        $contador       = 1;
        $totalbruto     = 0;
        $totalneto      = 0;
        $totalbrutounid = 0;
        $totaldescuento = 0; 
	
       $sql = 
	" SELECT ges_comprobantesdet.IdProducto, ".
        "        group_CONCAT(ges_comprobantesdet.IdAlbaran) as IdAlbaranes, ".
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
	"        ges_marcas.Marca,ges_colores.Color as presentacion, ".
	"        ges_tallas.Talla as subpresentacion , ".
	"        ges_laboratorios.NombreComercial as laboratorio,".
	"        ges_productos.IdContenedor, ".
	"        ges_productos.UnidadesPorContenedor, ".
	"        ges_productos.UnidadMedida ".
	"FROM    ges_comprobantesdet,ges_productos,ges_productos_idioma, ".
	"        ges_tallas,ges_colores,ges_marcas,ges_laboratorios ".
	"WHERE   ges_comprobantesdet.IdComprobante = '".$IdComprobante."' ".
	"AND     ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	"AND     ges_comprobantesdet.IdProducto = ges_productos.IdProducto ".
	"AND     ges_productos.IdColor = ges_colores.IdColor  ".
	"AND     ges_productos.IdTalla = ges_tallas.IdTalla ".
	"AND     ges_marcas.IdMarca = ges_productos.IdMarca  ".
	"AND     ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	"AND     ges_comprobantesdet.Eliminado = '0' ".
	"GROUP BY ges_comprobantesdet.CodigoBarras"; 

        $res = query($sql);
        while ( $row = Row($res) ) { 
	  $pdf->Cell(1);
	  $codarticulo = $row["IdProducto"];

	  // IMPRIME LINE
	  $cantidad = $row["Cantidad"];
	  // CANTIDAD
	  $pdf->Cell(20,4,$cantidad,'LR',0,'C');	
	  $pdf->SetFont('Arial','',9);
	  //UNID MEDIDA
          $cantunidmed = $row["UnidadMedida"];
	  
	  $pdf->Cell(16,4, $cantunidmed ,'LR',0,'C');	

	  $pdf->SetFont('Arial','B',9);
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
	  $seriestext   = '';
	  $series       = array();

	  if($row["Serie"]==1){
	    $DocumentoSalida  = ($row["IdAlbaranes"])? $row["IdAlbaranes"]:$IdComprobante;
	    $series = getSeriesVenta2IdProducto($DocumentoSalida,$codarticulo);

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
					   60);

	  //PRODUCTO
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
	  $acotado = getItemProducto($descripcion_0,56);

	  //PRECIO
	  $precio=$row["Precio"];
	  $precio=round($precio * 100) / 100; 	  
	  $totalbruto=$totalbruto+($precio*$cantidad);
	  $precio=number_format($precio,2);	  
	  //DESCUENTO
	  $dcto="";
	  if($row["Descuento"] !=0)
	    $dcto="x".$row["Descuento"]."%";
	  //$totaldcto=$totaldcto+$dcto;
	  //IMPORTE
	  $importe=$row["Importe"];
	  //BRUTO NETO
	  $totalneto=$totalneto+$importe;
	  //$importe=$importe+$importe*$igv/100;
 	  $importe=round($importe * 100) / 100; 
	  $importe=number_format($importe,2);

	  // IMPRIME LINE
	  $pdf->Cell(109,4,$acotado[0],'LR',0,'L');
	  $pdf->Cell(20,4,$precio."".$dcto,'LR',0,'R');
	  $pdf->Cell(25,4,$importe,'LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->Cell(1);
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(16,4,"",'LR',0,'C');	
	      $pdf->Cell(109,4,$line,'LR',0,'L');
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
	      $pdf->Cell(1);
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(16,4,"",'LR',0,'C');	
	      $pdf->Cell(109,4,$linemp,'LR',0,'L');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }

	  //CONTADOR
	  $contador++;

	};
	
	while ($contador<25)
	{
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(109,4,"",'LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

//################### MENSAJE FOOTER 
          $pdf->SetFont('Arial','',8);
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(109,4,'____________________________________________________________________________________','LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
          $pdf->Ln(3);	
          //############## LINEA 2
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          $pdf->Cell(16,4,"",'LR',0,'C');
          $fecha   = implota($fechahoy=date("Y-m-d"));
          $hora    = date("H:i");
          $codlist = $lafila["SerieComprobante"]." - ".$lafila["NComprobante"];
          $esTPV   = getSesionDato("TipoVentaTPV");
          $esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';

          $pdf->Cell(109,4,"TPV: ".$esTPV." / Factura: ".$nroSerie."-".$nroFactura." / CS: ".$codlist."/ IGV:".$IGV." / OP: ".$operador." / F.Imp: ".$fecha."  ".$hora,'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
          //############## LINEA 3
          $pdf->Ln(2);	
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LRB',0,'C');
          $pdf->Cell(16,4,"",'LRB',0,'C');
 	  $pdf->Cell(109,4,"",'LRB',0,'C');	
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
	  $pdf->Ln(9);	

//#######################  final de la Factura
    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(27);	
    $pdf->Cell(300,4,utf8_decode($totaletras));
//###################### Imprime Letras
$pdf->Ln(9);	

$pdf->Cell(1);

//####################################  IMPORTE FINAL DESGLOZADO	
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','B',8);

$pdf->Ln(2);

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','',10);
	
$pdf->Cell(1);

//########### HALLAMOS TOTALES

//TOTAL NETO SIN IGV
$totalneto=round($totalneto * 100) / 100; 
$totalneto_sinigv=($totalneto*100)/($IGV+100);
$totalneto_sinigv=round($totalneto_sinigv * 100) / 100; 

//TOTAL BRUTO
$totalbruto=($totalbruto*100)/($IGV+100);
//$totalbruto=round($totalbruto * 100) / 100; 

//TOTALIGV
$totaligv=$totalneto-$totalneto_sinigv;
$totaligv=round($totaligv * 100) / 100; 

//TOTAL DESCUENTO
$totaldescuento=$totalbruto-$totalneto_sinigv;
$totaldescuento=round($totaldescuento * 100) / 100; 

//FORMAT
$totalbruto=number_format($totalbruto,2);	
$totaldescuento=number_format($totaldescuento,2);	
$totalneto_sinigv=number_format($totalneto_sinigv,2);	
$totaligv=number_format($totaligv,2);	
$totalneto=number_format($totalneto,2);	

$pdf->Cell(30,4,$totalbruto,1,0,'R',1);
$pdf->Cell(36,4,$totaldescuento,1,0,'R',1);
$pdf->Cell(36,4,$totalneto_sinigv,1,0,'R',1);
$pdf->Cell(36,4,$totaligv,1,0,'R',1);	

//### TOTAL NETO

$pdf->Cell(50,4,number_format($lafila["TotalImporte"],2),1,0,'R',1);
$pdf->Ln(4);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
$name = "FACTURA-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroFactura.".pdf";

$pdf->Output($name,'');
?> 
