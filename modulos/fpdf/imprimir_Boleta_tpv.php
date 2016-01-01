<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");
$IdLocal      = getSesionDato("IdTiendaDependiente");
if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$nroBoleta     = $_GET["nroBoleta"];
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

//Imprime Comrpobante
//$pdf=new PDF();
//$pdf= new PDF (P,mm,array(211,211));
$pdf = new PDF ( 'P' , 'mm' , array ( 210 , 297 ));
$pdf->AddFont('Lucida','','lucida.php');
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
    $pdf->SetFont('Lucida','',8);	

//colum 2
    $pdf->SetX( 130);
//    $pdf->Cell(70,1,"",'LRB',0,'L',1);
    $pdf->Ln(38);

// Datos Cliente 
    $pdf->SetX(27); 
    //NOMBRE   
    $pdf->Cell(130,4,$nombre);
    //$igv=$lafila["IGV"]; 
    $igv=0;
    $pdf->Ln(6);
    $pdf->SetX(180);
    //NUM GUIA REMIS.   
    $pdf->Cell(70,4,"");

    $pdf->SetX(27);	
    //DIRECCION				
    $pdf->Cell(130,4,$direccion);

    $pdf->SetX(141);
    //RUC
    $pdf->Cell(130,4, $nif);
    //$pdf->Cell(130,4,"42521542");
    $pdf->SetX(130);
    //$pdf->Cell(70,1,"",'LRB',0,'L',1);
    $pdf->Cell(70,4,"");

    $pdf->SetX(174);
    //FECHA BOLETA
    $xdate = explode(" ",$lafila["FechaComprobante"]);
    list($anho,$mes,$dia)=explode('-',$xdate[0]);
    $pdf->Cell(70,4,$dia);
    $pdf->SetX(183);
    $pdf->Cell(70,4,$mes);
    $pdf->SetX(192);
    $pdf->Cell(70,4,$anho);
//    $pdf->Cell(35,1,"",'LRB',0,'L',1);
    $pdf->SetX(180);
    $pdf->Cell(70,4,"");
    $pdf->Ln(6);		
/*    $pdf->Cell(70,4,"Nro COMPR. DE PAGO: "." " . $lafila3["IdPais"]);
    $pdf->Ln(6);					
*/
	
//####################### las lneas delos ARTICULOS ###################
$pdf->Cell(1);
	
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
	
$pdf->Ln(6);
			
			
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Lucida','',8);
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

    $res=query($sql);
    while ( $row = Row($res) ) { 
      $codarticulo=$row["IdProducto"];

      //CANTIDAD
      $cantidad=$row["Cantidad"];

      //UNID MEDIDA
      $cantunidmed = $row["UnidadMedida"];

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

      //PRECIO
      $precio=$row["Precio"];
      $precio=round($precio * 100) / 100; 
      $precio=number_format($precio,2);	  
      //DESCUENTO
      $dcto="";
      if($row["Descuento"] !=0)
	$dcto="x".$row["Descuento"]."%";
      //$totaldcto=$totaldcto+$dcto;
      //IMPORTE
      $importe = $row["Importe"];
      $importe = round($importe * 100) / 100; 
      $importe = number_format($importe,2);
      $precio  = round((($importe/$cantidad))*100)/100;
      $precio  = number_format($precio,2);

      // IMPRIME LINE
      $pdf->Cell(1);
      $pdf->Cell(24,4,$cantidad,'LR',0,'C');	
      $pdf->SetFont('Lucida','',8);
      $pdf->Cell(16,4, $cantunidmed ,'LR',0,'C');	
      $pdf->Cell(96,4,utf8_decode($acotado[0]),'LR',0,'L');
      $pdf->Cell(16,4,$precio,'LR',0,'R');
      $pdf->Cell(26,4,$importe,'LR',0,'R');
      $pdf->Ln(4);	

      //TEXT EXTRA LINE run
      foreach ($acotado as $key=>$line){
	if($key>0 && $key < 27 ){
	  $pdf->Cell(1);
	  $pdf->Cell(24,4,"",'LR',0,'C');
	  $pdf->Cell(16,4,"",'LR',0,'C');	
	  $pdf->Cell(96,4,utf8_decode($line),'LR',0,'L');
	  $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(26,4,"",'LR',0,'C');
	  $pdf->Ln(4);
	  $contador++;
	  $acotadoext = 0;
	}
      }

      //TEXT META PRODUCTO
      foreach ($acotmp  as $key=>$linemp){
	if( $key < 20 ){
	  $pdf->SetFont('Lucida','',8);
	  $pdf->Cell(1);
	  $pdf->Cell(24,4,"",'LR',0,'C');
	  $pdf->Cell(16,4,"",'LR',0,'C');	
	  $pdf->Cell(96,4,utf8_decode($linemp),'LR',0,'L');
	  $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(26,4,"",'LR',0,'C');
	  $pdf->Ln(4);
	  $contador++;
	  $acotadoext = 0;
	  $pdf->SetFont('Lucida','',8);
	}
      }
      //CONTADOR
      $contador++;
    };

    while ($contador<24)
      {
	$pdf->Cell(1);
	$pdf->Cell(24,4,"",'LR',0,'C');
	$pdf->Cell(16,4,"",'LR',0,'C');
	$pdf->Cell(96,4,"",'LR',0,'C');	
	$pdf->Cell(16,4,"",'LR',0,'C');
	$pdf->Cell(26,4,"",'LR',0,'C');
	$pdf->Ln(4);	
	$contador=$contador +1;
      }

//################### MENSAJE FOOTER 
          $pdf->SetFont('Lucida','',8);
	  $pdf->Cell(1);
          $pdf->Cell(24,4,"",'LR',0,'C');
          $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(96,4,'____________________________________________________________________________________','LR',0,'C');	
	  $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(26,4,"",'LR',0,'C');
          $pdf->Ln(3);	
          //############## LINEA 2
	  $pdf->Cell(1);
          $pdf->Cell(24,4,"",'LR',0,'C');
          $pdf->Cell(16,4,"",'LR',0,'C');
          $fecha=implota($fechahoy=date("Y-m-d"));
          $hora=date("H:i");
          $codlist=$lafila["SerieComprobante"]." - ".$lafila["NComprobante"];
          $esTPV   = getSesionDato("TipoVentaTPV");
          $esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';

          $pdf->Cell(96,4,"TPV: ".$esTPV." / Boleta: ".$nroSerie."-".$nroBoleta." / CS: ".$codlist." / OP: ".$operador." / F.Imp.: ".$fecha."  ".$hora,'LR',0,'C');
	  $pdf->Cell(16,4,"",'LR',0,'C');
	  $pdf->Cell(26,4,"",'LR',0,'C');
          //############## LINEA 3
          $pdf->Ln(2);	
	  $pdf->Cell(1);
          $pdf->Cell(24,4,"",'LRB',0,'C');
          $pdf->Cell(16,4,"",'LRB',0,'C');
 	  $pdf->Cell(97,4,"",'LRB',0,'C');	
	  $pdf->Cell(16,4,"",'LRB',0,'C');
	  $pdf->Cell(26,4,"",'LRB',0,'C');
	  $pdf->Ln(6);	

//#######################  final de la Boleta
    $pdf->SetFont('Lucida','',9);	
    $pdf->SetX(27);	
    $pdf->Cell(300,4,utf8_decode($totaletras));
//###################### Imprime Letras
$pdf->Ln(7);	

$pdf->Cell(1);
$pdf->Ln(4);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Lucida','',9);
	
$pdf->Cell(1);

//########### HALLAMOS TOTALES
$pdf->Cell(20,4,"",1,0,'R',1);

$pdf->Cell(40,4,"",1,0,'R',1);

$pdf->Cell(40,4,"",1,0,'R',1);

$pdf->Cell(38,4,"",1,0,'R',1);	

$pdf->Cell(40,4,$lafila["TotalImporte"],1,0,'R',1);
$pdf->Ln(4);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "BOLETA-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroBoleta.".pdf";
$pdf->Output($name,'');
?> 
