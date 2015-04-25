<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");
$IdLocal      = getSesionDato("IdTiendaDependiente");
if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$nroAlbaran     = $_GET["nroAlbaran"];
$nroSerie      = $_GET["nroSerie"];
$codcliente    = CleanID($_GET["codcliente"]);
$totaletras    = $_GET["totaletras"];
$IdComprobante = CleanID($_GET["idcomprobante"]);
$operador      = ($_GET["nombreusuario"])? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;

 $sql = "SELECT DireccionFactura
          FROM   ges_locales
          WHERE  IdLocal='$IdLocal'";
 $res       = query($sql);
 $row       = Row($res);
 $direccionlocal = utf8_decode($row["DireccionFactura"]);



//Comprobante
if ($codcliente==0){   
  $sql = "Select * 
               from    ges_comprobantes 
               where   IdComprobante = '$IdComprobante'
               and     IdLocal       = '$IdLocal'
               and     Eliminado     = '0'";
}else{
  $sql = "Select Destinatario  
               from  ges_comprobantes
               where ges_comprobantes.IdComprobante = '$IdComprobante' 
               and   ges_comprobantes.IdCliente     = '$codcliente' 
               and   ges_comprobantes.IdLocal       = '$IdLocal' 
               and   ges_comprobantes.Eliminado     = '0'";
  
  $rs    = query($sql);
  $comp  = Row($rs);
  $dest  = $comp["Destinatario"];

  $esmov = ($comp["Destinatario"] == 'Local')? " INNER JOIN ges_motivoalbaran ON ges_comprobantesnum.IdMotivoAlbaran = ges_motivoalbaran.IdMotivoAlbaran ":"";
  $esmovalba = ($comp["Destinatario"] == 'Local')? " ges_motivoalbaran.MotivoAlbaran, ":"";

  $xclien = ($dest == 'Local')? " (SELECT CONCAT(ges_locales.NombreLegal,'~~',ges_locales.DireccionFactura,'~~',ges_locales.NFiscal) FROM ges_locales WHERE ges_locales.IdLocal = ges_comprobantes.IdCliente) ":"";
  $xclien = ($dest == 'Proveedor')? " (SELECT CONCAT(ges_proveedores.NombreLegal,'~~',ges_proveedores.Direccion,'~~',ges_proveedores.NumeroFiscal) FROM ges_proveedores WHERE ges_proveedores.IdProveedor = ges_comprobantes.IdCliente) ":$xclien;
  $xclien = ($dest == 'Cliente')? " (SELECT CONCAT(IF(ges_clientes.TipoCliente = 'Empresa',ges_clientes.NombreLegal,ges_clientes.NombreComercial),'~~',ges_clientes.Direccion,'~~',ges_clientes.NumeroFiscal) FROM ges_clientes WHERE ges_clientes.IdCliente = ges_comprobantes.IdCliente) ":$xclien;

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
                IF(Destinatario='$dest',$xclien,'') as Cliente, 
                ges_comprobantes.IdCliente,
                ges_locales.NombreLegal as Local, ges_comprobantes.IdLocal,
                $esmovalba 
                ges_comprobantes.FechaComprobante 
    		FROM ges_comprobantes " .
    		"LEFT JOIN ges_clientes ON ges_comprobantes.IdCliente = ges_clientes.IdCliente
                INNER JOIN ges_comprobantesstatus ON ges_comprobantes.Status = ges_comprobantesstatus.IdStatus
                INNER JOIN ges_locales ON ges_comprobantes.IdLocal = ges_locales.IdLocal
                INNER JOIN ges_usuarios ON ges_comprobantes.IdUsuario = ges_usuarios.IdUsuario
                INNER JOIN ges_comprobantesnum ON ges_comprobantesnum.IdComprobante = ges_comprobantes.IdComprobante
                INNER JOIN ges_comprobantestipo ON  ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante 
                $esmov
                WHERE ges_comprobantes.Eliminado = 0
                AND  ges_comprobantesnum.Eliminado = 0
                AND  ges_comprobantesnum.Status = 'Emitido'
                AND  ges_comprobantes.IdComprobante = '$IdComprobante'
                AND  ges_comprobantes.IdLocal       = '$IdLocal'";
 //AND  ges_comprobantestipo.TipoComprobante = 'AlbaranInt'";
}
$res       = query($sql);
$lafila    = Row($res);

$acliente  = explode("~~",$lafila["Cliente"]);
$nombre    = $acliente[0];
$direccion = $acliente[1];
$nif       = $acliente[2];
$nombre    = str_replace('&#038;','&',$nombre);

//Imprime Comrpobante
//$pdf=new PDF();
$pdf = new PDF ( 'P' , 'mm' , array ( 210 , 297 ));

$pdf->Open();
$pdf->AddPage();

$pdf->Ln(11);

    $pdf->Cell(95);
    $pdf->Cell(80,4,"",'',0,'C');
    $pdf->Ln(5);
	
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.2);
    $pdf->SetFont('Arial','B',10);	

//colum 2
    $pdf->SetX( 130);
//    $pdf->Cell(70,1,"",'LRB',0,'L',1);
    $pdf->Ln(24);					

//#################################### Datos Cliente #########

//    $pdf->Cell(130,4,$lafila["Direccion"]);
//    $pdf->Cell(130,4,);
//    $pdf->MultiCell(70,4,"RUC"." " . ,'LR',0,'L',1);

    $pdf->SetX(37);
    //########## FECHA BOLETA

    list($anho,$mes,$dia)=explode('-',$lafila["FechaComprobante"]);
    $pdf->Cell(70,4,$dia);
    $pdf->SetX(47);
    $pdf->Cell(70,4,$mes);
    $pdf->SetX(57);
    $pdf->Cell(70,4,$anho);

    $pdf->SetX(93);
    //########## FECHA BOLETA
/*
    list($anho,$mes,$dia)=explode('-',$lafila["FechaComprobante"]);
    $pdf->Cell(70,4,$dia);
    $pdf->SetX(103);
    $pdf->Cell(70,4,$mes);
    $pdf->SetX(113);
    $pdf->Cell(70,4,$anho);
*/
    $pdf->Ln(12);

    //########## DIRECCION				
    $pdf->SetX(39);	
    $pdf->Cell(130,4,$direccionlocal);
    $pdf->SetX(138);	

    $pdf->Cell(130,4,$direccion);

    $pdf->Ln(22);

    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(22); 
    //########## NOMBRE   
    $pdf->Cell(130,4,$nombre);
    //$igv=$lafila["IGV"]; 
    $igv=0;
    $pdf->Ln(4);
    $pdf->SetX(180);
    //########## NUM GUIA REMIS.   
    $pdf->Cell(70,4,"");
 
 
    $pdf->SetX(96);
    //########## RUC
    $pdf->Cell(130,4, $nif);
//    $pdf->Cell(130,4,"42521542");
    $pdf->SetX(130);
//    $pdf->Cell(70,1,"",'LRB',0,'L',1);
    $pdf->Cell(70,4,"");

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
$pdf->SetFont('Arial','B',8);
	
$pdf->Ln(8);
			
			
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','',8);
$IdComprobante=$lafila["IdComprobante"];
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

    	$contador=1;
        $totalbruto='';
        $totaldescuento=''; 
        $res=query($sql);
        while ( $row = Row($res) ) { 

	  $pdf->Cell(1);
	  $codarticulo=$row["IdProducto"];

	  // IMPRIME LINE
	  $cantidad=$row["Cantidad"];
	  // CANTIDAD
	  $pdf->Cell(20,4,$cantidad,'LR',0,'C');	
	  $pdf->SetFont('Arial','',9);
	  // UNID MEDIDA
          $cantunidmed=$row["UnidadMedida"];

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
	    $DocumentoSalida  = ($row["IdAlbaranes"])? $row["IdAlbaranes"]:$IdComprobante;
	    $series = getSeriesVenta2IdProducto($DocumentoSalida,$codarticulo);

	    if( count($series)< 90 )
	      $seriestext = "N/S: ".implode($series," ");
	  }

	  //META PRODUCTO ITEM
	  $itemmprod = array();
	  $acotmp    = array();
	  $acotmp    = getItemMetaProducto($row["MetaProducto"],$row["Serie"],$series,$codarticulo,71);

	  $descripcion_0 =
	    //$codigobarras." ".
	    $descripcion." ".
	    $marca." ".
	    $modelo." ".
	    $detalle." ".
	    $laboratorio." ".
	    $seriestext." ***";
	  
	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($descripcion_0,71);
	  
	  //  PRECIO
	  $precio=$row["Precio"];
	  $precio=round($precio * 100) / 100; 
	  $precio=number_format($precio,2);	  

	  // IMPRIME LINE
	  //$pdf->Cell(15,4,$precio,'LR',0,'R');
	  //  DESCUENTO
	  //$dcto=$row["Descuento");
	  //$totaldcto=$totaldcto+$dcto;

	  //  IMPORTE
	  $importe=$row["Importe"];
 	  $importe=round($importe * 100) / 100; 
	  $importe=number_format($importe,2);

	  // IMPRIME LINE
	  $pdf->Cell(140,4,$acotado[0],'LR',0,'L');
          $pdf->Cell(15,4, $cantunidmed ,'LR',0,'C');	
	  //$pdf->Cell(15,4,'','LR',0,'R');
 	  //$pdf->Cell(30,4,'','LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->Cell(1);
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      //$pdf->Cell(20,4,"",'LR',0,'C');	
	      $pdf->Cell(140,4,$line,'LR',0,'L');
	      $pdf->Cell(15,4,"",'LR',0,'C');
	      //$pdf->Cell(25,4,"",'LR',0,'C');
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
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      //$pdf->Cell(20,4,"",'LR',0,'C');	
	      $pdf->Cell(140,4,$linemp,'LR',0,'L');
	      $pdf->Cell(15,4,"",'LR',0,'C');
	      //$pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	      $pdf->SetFont('Arial','',8);
	    }
	  }

	  // CONTADOR
	  $contador++;

	};
	
	while ($contador<26)
	{
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          //$pdf->Cell(15,4,"",'LR',0,'C');
	  $pdf->Cell(140,4,"",'LR',0,'C');	
	  $pdf->Cell(15,4,"",'LR',0,'C');
	  //$pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

//################### MENSAJE FOOTER 
          $pdf->SetFont('Arial','',8);
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          //$pdf->Cell(15,4,"",'LR',0,'C');
	  $pdf->Cell(140,4,'____________________________________________________________________________________','LR',0,'C');	
	  $pdf->Cell(15,4,"",'LR',0,'C');
	  //$pdf->Cell(25,4,"",'LR',0,'C');
          $pdf->Ln(3);	
          //############## LINEA 2
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          //$pdf->Cell(15,4,"",'LR',0,'C');
          $fecha=implota($fechahoy=date("Y-m-d"));
          $hora=date("H:i");
          $codlist=$lafila["SerieComprobante"]." - ".$lafila["NComprobante"];
          $esTPV   = getSesionDato("TipoVentaTPV");
          $esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';


          $pdf->Cell(140,4,"TPV: ".$esTPV." / Guia Remision: ".$nroSerie."-".$nroAlbaran." / CS: ".$codlist." / OP: ".$operador." / F.Imp.: ".$fecha."  ".$hora,'LR',0,'C');
	  $pdf->Cell(15,4,"",'LR',0,'C');
	  //$pdf->Cell(25,4,"",'LR',0,'C');
          //############## LINEA 3
          $pdf->Ln(2);	
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LRB',0,'C');
          //$pdf->Cell(15,4,"",'LRB',0,'C');
 	  $pdf->Cell(140,4,"",'LRB',0,'C');	
	  $pdf->Cell(15,4,"",'LRB',0,'C');
	  //$pdf->Cell(25,4,"",'LRB',0,'C');
	  $pdf->Ln(6);	

//#######################  final de la Albaran
    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(20);
    //Total letras : $totaletras 
    $pdf->Cell(300,4,'');
//###################### Imprime Letras
$pdf->Ln(9);	

$pdf->Cell(1);

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
	
$pdf->Cell(1);

//########### HALLAMOS TOTALES
$pdf->Cell(20,4,"",1,0,'R',1);

$pdf->Cell(40,4,"",1,0,'R',1);

$pdf->Cell(40,4,"",1,0,'R',1);

$pdf->Cell(38,4,"",1,0,'R',1);	

$pdf->Cell(50,4,'',1,0,'R',1);
$pdf->Ln(4);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "ALBARAN-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroAlbaran.".pdf";
$pdf->Output($name,'');
?> 
