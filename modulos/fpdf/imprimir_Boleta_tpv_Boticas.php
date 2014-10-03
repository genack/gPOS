<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');
//include("comunes_boleta_tpv.php");
include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal      = getSesionDato("IdTienda");
if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";
$nroBoleta     = $_GET["nroBoleta"];
$codcliente    = $_GET["codcliente"];
$totaletras    = $_GET["totaletras"];
$IdComprobante = $_GET["idcomprobante"];
$operador      = $_SESSION["NombreUsuario"];

//Comprobante
if (codcliente==0){   
  $sql = "Select * 
               from    ges_comprobantes 
               where   IdComprobante = '$IdComprobante'
               and     TipoVentaOperacion
               and     IdLocal       = '$IdLocal'
               and     Eliminado     = '0'";
}else{
  $sql = "Select *  
               from  ges_comprobantes,ges_clientes 
               where ges_comprobantes.IdComprobante = '$IdComprobante' 
               and   ges_comprobantes.TipoDocumento = '1' 
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
                 Direccion
          FROM   ges_clientes 
          WHERE  IdCliente='$codcliente'";
  $res       = query($sql);
  $row       = Row($res);
  $nombre    = utf8_decode($row["NombreComercial"]);
  $nif       = utf8_decode($row["NumeroFiscal"]);
  $direccion = utf8_decode($row["Direccion"]);
}

//$pdf=new PDF();
$pdf=new PDF (L,mm,array(168,100));


$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,1);
$pdf->Ln(12);



    $pdf->Cell(95);
    $pdf->Cell(80,4,"",'',0,'C');
	
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(.2);
    $pdf->SetFont('Arial','B',9);	

//colum 2
    $pdf->SetX( 130);
    $pdf->Ln(7);					
 
//#################################### Datos Cliente #########

    $pdf->SetFont('Arial','B',10);	
    $pdf->SetX(32); 
    //########## NOMBRE   
    $pdf->Cell(130,4,$nombre);
    $igv=0;
    $pdf->Ln(3);
    $pdf->SetX(180);
    //########## NUM GUIA REMIS.   
    $pdf->Cell(70,4,"");

    $pdf->SetX(32);	
    //########## DIRECCION				
    $pdf->Cell(130,4,$direccion);

    $pdf->SetX(146);
    //########## RUC
    $pdf->SetX(130);
    $pdf->Cell(70,4,"");

    $pdf->SetX(130);
    //########## FECHA BOLETA
    list($anho,$mes,$dia)=explode('-',$lafila["FechaComprobante"]);
    $pdf->Cell(70,4,$dia);
    $pdf->SetX(135);
    $pdf->Cell(70,4,$mes);
    $pdf->SetX(140);
    $pdf->Cell(70,4,$anho);
    $pdf->SetX(180);
    $pdf->Cell(70,4,"");
    $pdf->Ln(7);		
	
//####################### las lneas delos ARTICULOS ###################
$pdf->Cell(1);
	
$pdf->Ln(2);
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Arial','B',9);

$IdComprobante=$lafila["IdComprobante"];
 	$sql = "
			SELECT 
			              ges_comprobantesdet.*,  
				      ges_productos.IdLabHab, 
				      ges_productos.IdProducto as codarticulo, 
				      ges_productos.Referencia as referencia, 
				      ges_productos_idioma.Descripcion as descripcion, 
				      ges_marcas.Marca,ges_colores.Color as presentacion,
				      ges_tallas.Talla as subpresentacion ,
                                      ges_laboratorios.NombreComercial as laboratorio,
				      ges_productos.IdContenedor, 
				      ges_productos.UnidadesPorContenedor,
				      ges_productos.UnidadMedida 
		         FROM 
			              ges_comprobantesdet,ges_productos,ges_productos_idioma, 
				      ges_tallas,ges_colores,ges_marcas,ges_laboratorios
			 WHERE   
				      ges_comprobantesdet.IdComprobante = '$IdComprobante' AND 
                                      ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio AND
				      ges_comprobantesdet.IdProducto = ges_productos.IdProducto AND 
				      ges_productos.IdColor = ges_colores.IdColor  AND 
				      ges_productos.IdTalla = ges_tallas.IdTalla AND 
				      ges_marcas.IdMarca = ges_productos.IdMarca  AND 
				      ges_productos.IdProdBase = ges_productos_idioma.IdProdBase AND 
				      ges_comprobantesdet.Eliminado = '0' ORDER BY ges_comprobantesdet.IdComprobanteDet ASC"; 
    	$contador=1;
        $totalbruto='';
        $totaldescuento=''; 
        $res=query($sql);
        while ( $row = Row($res) ) {
	  $pdf->Cell(9);
	  $codarticulo=$row["IdProducto"];
	  $cantidad=$row["Cantidad"];
          $cantidadunid=$row["UnidadMedida"];
	  switch ($cantidadunid) { 
	  case "0":  $cantunidmed="unid."; break;
	  case "1":  $cantunidmed="mtrs."; break;
	  case "2":  $cantunidmed="ltrs."; break;
	  case "3":  $cantunidmed="klos."; break;
	  }

	  //########## CANTIDAD
	  //$pdf->Cell(12,4,$cantidad." ".$cantunidmed,'LR',0,'C');	
	  $pdf->Cell(14,4,$cantidad,'LR',0,'C');	

	  //### TEXT DESCRIPCION
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

           $descripcion_0 = $descripcion." ".$marca." ".$modelo." ".$detalle." ".$laboratorio;
           $num_caract_0 = strlen($descripcion_0);
   	   $contadorTexto = 0;
	   $arrayTexto = split(' ',$descripcion_0);
           $descripcion = "";
           $descripcion2 = "";
	   $tamanoTexto = 48;
                  
	   while( $tamanoTexto >= strlen($descripcion)+ strlen($arrayTexto[$contadorTexto])){
                  $descripcion .= " ".$arrayTexto[$contadorTexto];
	          $contadorTexto++;
 	  }

	  $acotado= $descripcion;  

	  if ( $num_caract_0 > $tamanoTexto ){	
	     while( $tamanoTexto >= strlen($descripcion2)+ strlen($arrayTexto[$contadorTexto])){
               $descripcion2 .= " ".$arrayTexto[$contadorTexto];
	       $contadorTexto++;
 	     }
	     $acotado1= $descripcion2;
	     $acotadoext=1;
	  }


	  //################## META PRODUCTO
	  $IdMtProd='0';//Bandera Meta init
	  $ModoMtProd='9';

	  if($IdMtProd==1){
	    $descripcion=mysql_result($resultado2,$lineas,"DescripMtProd");
	    $longcadena = strlen($descripcion);
	    if($longcadena>65){
	      $acotado=substr($descripcion,0,strrpos(substr($descripcion,0,65)," "));
	      $longcadena = strlen($acotado);
	      $acotado1=substr($descripcion,$longcadena+1,120);
	      $longcadena = strlen($acotado1);
	      if ($longcadena>0){
		$acotadoext=1;
	      }
	    }else {
	      $acotado = substr(mysql_result($resultado2,$lineas,"DescripMtProd"), 0, 110);
	    }
	  }


	  //############## IMPRIME LINE
	  $pdf->Cell(90,4,$acotado,'LR',0,'L');
	  
	  //###  PRECIO
	  $precio=$row["Precio"];
	  $precio=round($precio * 100) / 100; 
	  $precio=number_format($precio,2);	  

	  //############## IMPRIME LINE
	  $pdf->Cell(15,4,$precio,'LR',0,'R');
	  
	  //###  IMPORTE
	  $importe=$row["Importe"];

	  //######## BRUTO NETO
	  $totalneto=$totalneto+$importe;
	  //$importe=$importe+$importe*$igv/100;
 	  $importe=round($importe * 100) / 100; 
	  $importe=number_format($importe,2);

	  $pdf->Cell(15,4,$importe,'LR',0,'R');
	  $pdf->Ln(3);	

	  //############ CONTADOR
	  $contador++;
	  //############  TEXT PRODUCT EXEDE NUM CARAT 90 PRINT NEW LINE
	  if( $acotadoext == 1){
	      $pdf->Cell(23);
	      $pdf->Cell(90,4,$acotado1,'LR',0,'L');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(3);
	      $contador++;
	  }
 	  //############  TEXT PRODUCT EXEDE NUM CARAT 90 PRINT NEW LINE


	  $lineas=$lineas + 1;
	  
	};
	
	while ($contador<12)
	{
	  $pdf->Cell(1);
          $pdf->Cell(20,4,"",'LR',0,'C');
          $pdf->Cell(15,4,"",'LR',0,'C');
	  $pdf->Cell(110,4,"",'LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Ln(3);	
	  $contador=$contador +1;
	}

//################### MENSAJE FOOTER 
          //############## LINEA 2
          //$pdf->Ln(1);	
	  $pdf->Cell(1);
          $pdf->Cell(22,4,"",'LR',0,'C');
          $fecha=implota($fechahoy=date("Y-m-d"));
          $hora=date("H:i");
          $codlist=$lafila["SerieComprobante"]." - ".$lafila["NComprobante"];
          $pdf->SetFont('Arial','B',8);
          $pdf->Cell(110,4,"NUEVA DIREECION: AV. ANDRES AVELINO CACERES NRO 842");
          $pdf->Ln(3);	
          $pdf->SetFont('Arial','B',9);
          $pdf->Cell(140,4,"TPV: ".getSesionDato("TipoVentaTPV")." / Nro Boleta: ".$nroBoleta." / CS: ".$codlist." / OP: ".$operador." / F.Imp.: ".$hora." ".$fecha,'LR',0,'C');
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
          //############## LINEA 3
          $pdf->Ln(1);	
	  $pdf->Cell(1);
          $pdf->Cell(30,4,"",'LRB',0,'C');
 	  $pdf->Cell(110,4,"",'LRB',0,'C');	
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
	  $pdf->Ln(3);	

//#######################  final de la Boleta
    $pdf->SetFont('Arial','B',9);	
    $pdf->SetX(26);	
    $pdf->Cell(300,4,$totaletras);
    $pdf->Ln(5);	
    $pdf->Cell(1);
    $totalneto=round($totalneto * 100) / 100; 
    $totalneto=number_format($totalneto,2);	
    $pdf->Cell(142,4,$totalneto,1,0,'R',1);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "BOLETA-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$nroBoleta.".pdf";
$pdf->Output($name,'');
?> 
