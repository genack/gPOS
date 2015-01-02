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
$totaletras    = $_GET["totaletras"];
$operador      = $_SESSION["NombreUsuario"];
$IGV	       = getSesionDato("IGV");
$nombrelegal   = getNombreLegalLocalId($IdLocal);
$poblacion     = getPoblacionLocalId($IdLocal);

 $sql =
  "SELECT 
                ges_ordencompra.IdOrdenCompra As Codigo,
                ges_ordencompra.CodOrdenCompra,
                ges_locales.NombreComercial As Local,
                ges_proveedores.NombreLegal As Proveedor,
                ges_proveedores.NumeroFiscal As RUC,
                DATE_FORMAT(ges_ordencompra.FechaRegistro, '%e %M %Y') As Registro,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPedido, '%e %b %Y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPedido, '%e %b %y') ) 
                    As Pedido,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPrevista, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPrevista, '%e %b %y') ) 
                    As Entrega,
                IF ( DATE_FORMAT(ges_ordencompra.FechaRecibido, '%k:%i %e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaRecibido, '%e %b %y %k:%i') ) 
                    As Recibido,
                IF ( DATE_FORMAT(ges_ordencompra.FechaPago, '%e %b %y') IS NULL, 
                    ' ',
                    DATE_FORMAT(ges_ordencompra.FechaPago, '%e %b %y') ) 
                    As Pago, 
                IF(ges_moneda.IdMoneda = 2, CONCAT(ges_moneda.Moneda,'/',ges_ordencompra.CambioMoneda,'/', ges_ordencompra.FechaCambioMoneda) ,ges_moneda.Moneda ) as MonedaDet,
                ges_moneda.Simbolo As Moneda,
                ges_ordencompra.Importe,
                ges_ordencompra.ModoPago,   
                ges_ordencompra.Estado,  
                ges_ordencompra.FechaCambioMoneda,
                ges_ordencompra.CambioMoneda,
                ges_ordencompra.Observaciones,
                ges_usuarios.Nombre As Usuario 
    	  FROM  ges_ordencompra
    		LEFT JOIN ges_proveedores ON ges_ordencompra.IdProveedor = ges_proveedores.IdProveedor
                INNER JOIN ges_moneda   ON ges_ordencompra.IdMoneda  = ges_moneda.IdMoneda
                INNER JOIN ges_locales ON ges_ordencompra.IdLocal = ges_locales.IdLocal
                INNER JOIN ges_usuarios ON ges_ordencompra.IdUsuario = ges_usuarios.IdUsuario                
          WHERE ges_ordencompra.IdOrdenCompra=".$idcod." AND ges_ordencompra.Eliminado = 0  "; 

$res       = query($sql);
$row       = Row($res);
$Local     = utf8_decode($row["Local"]);
$Codigo    = utf8_decode($row["CodOrdenCompra"]);
$Proveedor = utf8_decode($row["Proveedor"]);
$RUC       = utf8_decode($row["RUC"]);
$Registro  = utf8_decode($row["Registro"]);
$Pedido    = utf8_decode($row["Pedido"]);
$Entrega   = utf8_decode($row["Entrega"]);
$Recibido  = utf8_decode($row["Recibido"]);
$Pago      = utf8_decode($row["Pago"]);
$Moneda    = utf8_decode($row["Moneda"]);
$Monedadet = utf8_decode($row["MonedaDet"]);
$ModoPago  = utf8_decode($row["ModoPago"]);
$Importe   = utf8_decode($row["Importe"]);
$Estado    = utf8_decode($row["Estado"]);
$Usuario   = utf8_decode($row["Usuario"]);
$Observaciones   = utf8_decode($row["Observaciones"]);

//Trabajos
$sql = 
  "SELECT ges_subsidiarios.NombreLegal AS Fletador, ".
  "       ges_subsidiariosserv.Servicio, ".
  "       ges_subsidiariostbjos.*".
  "FROM   ges_subsidiariostbjos 
   INNER JOIN ges_subsidiarios ON 
           ges_subsidiarios.IdSubsidiario  = ges_subsidiariostbjos.IdSubsidiario
   INNER JOIN ges_subsidiariosserv ON 
           ges_subsidiariosserv.IdServicio = ges_subsidiariostbjos.IdServicio
   WHERE IdOrdenCompra = ".$idcod." AND  ges_subsidiariostbjos.Eliminado = 0 ";
$res       = query($sql);
//$row       = Row($res);
while ( $row = Row($res) ) { 
  $Fletador     = utf8_decode($row["Fletador"]);
  $Flete        = utf8_decode($row["Costo"]);
  $Servicio     = utf8_decode($row["Servicio"]);
  $FleteDoc     = utf8_decode($row["DocSubsidiario"]);
  $FleteNDoc    = utf8_decode($row["NDocSubsidiario"]);
  $FechaEntrega = date('d/m/Y',strtotime($row["FechaRecepcion"]));
  $Observaciones .= " - ".$Servicio." ".$Fletador.", entrega ".$FechaEntrega.".";
}

//PDF ESTRUCTURA
$pdf=new PDF();
$pdf->Open();
$pdf->AddPage();

//PROFORMA
$pdf->SetX(75);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(125,4,utf8_decode('ORDEN DE COMPRA Nº ').$Codigo );

$pdf->Ln(12);

//FECHA
$pdf->SetX(145);
$pdf->SetFont('Courier','',9);	
$pdf->Cell(130,4,$poblacion.", ".$Registro);
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
$pdf->Cell(120,4,utf8_decode('Fecha Pago:'));
 
$pdf->SetX(107); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Pago);

$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Tipo Pago:'));

$pdf->SetX(160); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$ModoPago);

$pdf->Ln(4);
$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Pedido:'));

$pdf->SetX(44); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Pedido);

$pdf->SetX(85); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado:'));

$pdf->SetX(100); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Estado);

$pdf->SetX(140); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Entrega:'));
 
$pdf->SetX(168); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Entrega);


//Presente
$pdf->Ln(6);
$pdf->SetX(18); 
$pdf->SetFont('Courier','UB',10);	
$pdf->Cell(18,4,utf8_decode('Presente'));
$pdf->SetX(34.5); 
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
	

$pdf->Cell(10,4,"Cant.",1,0,'C',1);	
$pdf->Cell(10,4,"Unid.",1,0,'C',1);
$pdf->Cell(115,4,utf8_decode("Descripción"),1,0,'D',1);
$pdf->Cell(20,4,"Precio(".$Moneda.")",1,0,'C',1);
$pdf->Cell(25,4,"Importe(".$Moneda.")",1,0,'C',1);


$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(10,4,"",'LR',0,'C');
$pdf->Cell(10,4,"",'LR',0,'C');
$pdf->Cell(115,4,"",'LR',0,'C');	
$pdf->Cell(20,4,"",'LR',0,'C');
$pdf->Cell(25,4,"",'LR',0,'C');
$pdf->Ln(2);	

$contador=1;
$totalbruto=0;
$totalneto=0;
$lineas=0; 
$totalbrutounid=0;
$totaldescuento=0; 
$sql = 
  " SELECT ges_ordencompradet.*, ".
 	"        ges_productos.CodigoBarras, ".
	"        ges_productos.MetaProducto, ".
	"        ges_productos.Serie, ".
	"        ges_productos.IdLabHab, ".
	"        ges_productos.IdProducto as codarticulo, ".
	"        ges_productos.Referencia as referencia, ".
	"        ges_productos_idioma.Descripcion as descripcion, ".
	"        ges_marcas.Marca,ges_modelos.Color as presentacion, ".
	"        ges_detalles.Talla as subpresentacion , ".
	"        ges_laboratorios.NombreComercial as laboratorio,".
	"        ges_productos.IdContenedor, ".
        "        ges_productos.VentaMenudeo, ".
	"        ges_productos.UnidadesPorContenedor, ".
	"        ges_productos.UnidadMedida, ".
	"        ges_contenedores.Contenedor ".
	"FROM    ges_ordencompradet,ges_productos,ges_productos_idioma, ".
	"        ges_detalles,ges_modelos,ges_marcas,ges_laboratorios,ges_contenedores ".
	"WHERE   ges_ordencompradet.IdOrdenCompra = '".$idcod."' ".
	"AND     ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	"AND     ges_productos.IdContenedor = ges_contenedores.IdContenedor ".
	"AND     ges_ordencompradet.IdProducto = ges_productos.IdProducto ".
	"AND     ges_productos.IdColor = ges_modelos.IdColor  ".
	"AND     ges_productos.IdTalla = ges_detalles.IdTalla ".
	"AND     ges_marcas.IdMarca = ges_productos.IdMarca  ".
	"AND     ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	"AND     ges_ordencompradet.Eliminado = '0' ".
	"ORDER BY ges_ordencompradet.IdOrdenCompraDet ASC"; 
        
        $res = query($sql);
        while ( $row = Row($res) ) { 
	  $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $codarticulo = $row["IdProducto"];

	  // IMPRIME LINE
	  $cantidad=$row["Unidades"];
	  //UNID MEDIDA
	  $cantunidmed=$row["UnidadMedida"];

	  //IMPRIME CANTIDAD
	  $pdf->SetFont('Courier','B',8);
	  $pdf->Cell(10,4,$cantidad,'LR',0,'C');
	  $pdf->Cell(10,4,$cantunidmed,'LR',0,'C');		
	  $pdf->SetFont('Courier','',9);

	  //IMPRIME UNIDAD
	  //$pdf->SetFont('Courier','B',8);
	  //$pdf->Cell(10,4,$cantunidmed,'LR',0,'C');	
	  //$pdf->SetFont('Courier','',9);


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

	  if($row["VentaMenudeo"]){
	    $upc      = $row["UnidadesPorContenedor"];
	    $resto    = ( $cantidad>$upc && $upc )? $cantidad%$upc : $cantidad;
	    $cantcont = ( $cantidad>$upc && $upc )?  round(($cantidad-$resto)/$upc * 100) / 100 : 0;   
	    $detcant  = "Cant. ".$cantcont." ".$row["Contenedor"]."+".$resto." ".$cantunidmed;
	    $contund  =  $row["Contenedor"]."/".$upc.$cantunidmed;
	  }
	  else{
	    $detcant  = "";
	    $contund  = "";
	  }
	  //SERIES
	  $seriestext = '';
	  $series     = array();
	  $a_cbmtpd   = array();
	  $noSeries   = false;
	  if($row["Serie"]==1){

	    //cb de meta productos elegidos para el cliente
	    $cbmtpd = '';
	    
	    //cb de meta producto default, si es necesario
	    if($row["MetaProducto"]==1 && $cbmtpd == '')
	      {
		$a_cbmtpd = explode(";",getDetBaseMProducto($IdLocal,$codarticulo));
		$cbmtpd   = $a_cbmtpd[1];
		$noSeries = true;
	      }

	    $series =  getNSFromCBMPPresupuesto($IdLocal,$codarticulo,$cbmtpd);
	    //$series = explode(",",$series); 
	    if(!$noSeries)
	      $seriestext = "N/S: ".implode($series," ");
	    
	  }

	  //META PRODUCTO
	  $itemmprod = array();
	  $acotmp    = array();
	  $acotmp    = getItemMetaProducto($row["MetaProducto"],$row["Serie"],$series,$codarticulo,64);

	  $descripcion_0 =
	    $codigobarras." ".
	    $descripcion." ".
	    $marca." ".
	    $modelo." ".
	    $detalle." ".
	    $laboratorio." ".
	    $contund." ".
	    $seriestext." ".
	    $detcant;

	  //SERVICIO

	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($descripcion_0,60);

	  //PRECIO
	  $precio=$row["Costo"];
	  $precio=round($precio * 100) / 100; 	  
	  $totalbruto=$totalbruto+($precio*$cantidad);
	  $precio=number_format($precio,2);	  
	  $importe=round($precio*$cantidad*100)/100;
	  //BRUTO NETO
	  $totalneto=$totalneto+$importe;
	  //$importe=$importe+$importe*$igv/100;
 	  $importe=round($importe * 100) / 100; 
	  $importe=number_format($importe,2);

	  // IMPRIME LINE
	  $pdf->Cell(115,4,utf8_decode($acotado[0]),'LR',0,'L');
	  $pdf->SetFont('Courier','',9);
	  $pdf->Cell(20,4,$precio,'LR',0,'R');
	  $pdf->SetFont('Courier','B',9);
	  $pdf->Cell(25,4,$importe,'LR',0,'R');
	  $pdf->Ln(4);	

	  //TEXT EXTRA LINE run
	  $pdf->SetFont('Courier','',9);
	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(10,4,"",'LR',0,'C');
	      $pdf->Cell(10,4,"",'LR',0,'C');
	      $pdf->Cell(115,4,utf8_decode($line),'LR',0,'L');
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
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(10,4,"",'LR',0,'C');
	      $pdf->Cell(125,4,utf8_decode($linemp),'LR',0,'L');
	      $pdf->Cell(20,4,"",'LR',0,'C');
	      $pdf->Cell(25,4,"",'LR',0,'C');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }

	  //CONTADOR
	  $contador++;

	  $lineas=$lineas + 1;
	  
	};
	
	while ($contador<2)
	{
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(10,4,"",'LR',0,'C');
          $pdf->Cell(10,4,"",'LR',0,'C');
	  $pdf->Cell(115,4,"",'LR',0,'C');	
	  $pdf->Cell(20,4,"",'LR',0,'C');
	  $pdf->Cell(25,4,"",'LR',0,'C');
	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

          // LINEA 3
	  $pdf->Ln(-3);	
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(10,4,"",'LRB',0,'C');
          $pdf->Cell(10,4,"",'LRB',0,'C');
 	  $pdf->Cell(115,4,"",'LRB',0,'C');	
	  $pdf->Cell(20,4,"",'LRB',0,'C');
	  $pdf->Cell(25,4,"",'LRB',0,'C');
          $pdf->Ln(4);	
          //$pdf->Cell(1);

//  IMPORTE FINAL DESGLOZADO	
// TOTAL NETO
$totalneto=round($totalneto * 100) / 100; 
$totalneto=number_format($totalneto,2);	

$fecha=implota($fechahoy=date("Y-m-d"));
$hora=date("H:i");
$mensaje = 
  ":::".$operador.
  " ".$fecha." ".$hora.
  " ".$Local.":::";
$pdf->SetX(27);	
$pdf->SetFont('Courier','B',9);
$pdf->Cell(300,4,$mensaje);


// HALLAMOS TOTALES
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200,200,200);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','B',9);
$pdf->SetX(152);		
$pdf->Cell(1);
$pdf->Cell(20,4,"Total",1,0,'R',1);
$pdf->Cell(25,4,$totalneto,1,0,'R',1);
$pdf->Ln(6);

//IMPORTE LETRAS
$pdf->SetFont('Courier','B',10);	
$pdf->SetX(17);	
$pdf->Cell(300,4,"SON:".utf8_decode($totaletras));

//Obervaciones
if($Observaciones!==''){
  $pdf->Ln(6);
  $pdf->SetX(17); 
  $pdf->SetFont('Courier','UB',11);	
  $pdf->Cell(29.5,4,utf8_decode('Observaciones'));
  $pdf->SetFont('Courier','',9);	
  $pdf->Cell(1,4,'.');
  $pdf->Ln(6);

  $obsev = explode("-", $Observaciones);

  foreach ($obsev  as $key=>$line){
    if( $key > 0 && $line!=''){
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
$pdf->SetFont('Courier','',10);	
$pdf->Cell(1,4,'.');
$pdf->Ln(6);
//IGV
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(300,4,utf8_decode("- Los precios incluye IGV(".$IGV."%)"));
$pdf->Ln(4);
//Vigencia
//$pdf->SetX(17); 
//$pdf->Cell(1);
//$pdf->Cell(300,4,utf8_decode("- Vigencia de oferta ".$lafila["VigenciaPresupuesto"]." día(s)."));
//$pdf->Ln(4);
//Garantia Comercial
//$pdf->SetX(17); 
//$pdf->Cell(1);
//$pdf->Cell(300,4,utf8_decode("- Garantía comercial ".$garantiacomercial." meses."));
//$pdf->Ln(4);

//$impo=sprintf("%01.2f", $impo); 

@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);

//#### NOMBRE DEL FICHERO
$name = "ORDENCOMPRA-".$Codigo.".pdf";

$pdf->Output($name,'');
?> 
