<?php
define('FPDF_FONTPATH','font/');
require('mysql_table.php');

include("comunes.php");
include ("../funciones/fechas.php"); 
include("../../tool.php");

$IdLocal   = getSesionDato("IdTiendaDependiente");
$Moneda    = getSesionDato("Moneda");

if (!isset($IdLocal))
  echo "<script>parent.location.href='../logout.php';</script>";

$totaletras    = $_GET["totaletras"];
$IdOrdenServicio = $_GET["idoc"];
$operador      = isset($_GET["nombreusuario"])? $_GET["nombreusuario"]:$_SESSION["NombreUsuario"];
$LocalVenta    = (isset($_GET["idlocal"]))? CleanID($_GET["idlocal"]):0;
$IdLocal       = ($LocalVenta != 0)? $LocalVenta:$IdLocal;

  $sql = "SELECT ges_ordenservicio.IdLocal, ".
         "       ges_locales.NombreComercial as Local, ".
         "       ges_usuarios.Nombre as Usuario, ".
         "       ges_ordenservicio.IdCliente, ".
         "       ges_clientes.NombreComercial as Cliente, ".
         "       ges_clientes.NumeroFiscal RUC, ".
         "       ges_ordenservicio.IdOrdenServicio, ".
         "       IF(IdUsuarioEntrega = 0,' ',
                   (SELECT ges_usuarios.Nombre 
                    FROM   ges_usuarios 
                    WHERE  ges_usuarios.IdUsuario = IdUsuarioEntrega)) as UsuarioEntrega, ".
         "       DATE_FORMAT(FechaIngreso,'%d/%m/%Y %H:%i') as FechaIngreso, ".
         "       IF(FechaEntrega = '0000-00-00 00:00',' ',(DATE_FORMAT(FechaEntrega,'%d/%m/%Y %H:%i'))) as FechaEntrega, ".
         "       ges_ordenservicio.Estado, ".
         "       ges_ordenservicio.Codigo, ".
         "       ges_ordenservicio.Serie, ".
         "       ges_ordenservicio.NumeroOrden, ".
         "       ges_ordenservicio.Impuesto, ".
         "       ges_ordenservicio.Importe ".
         "FROM   ges_ordenservicio ".
         "INNER JOIN ges_locales ON ges_ordenservicio.IdLocal = ges_locales.IdLocal ".
         "INNER JOIN ges_usuarios ON ges_ordenservicio.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_clientes ON ges_ordenservicio.IdCliente = ges_clientes.IdCliente ".
         "WHERE  ges_ordenservicio.Eliminado = 0 ".
         "AND    ges_ordenservicio.IdOrdenServicio = '$IdOrdenServicio' ".
         "ORDER BY ges_ordenservicio.IdOrdenServicio DESC ";

$res       = query($sql);
$row       = Row($res);

$Codigo      = $row["Codigo"];
$Usuario     = utf8_decode($row["Usuario"]);
$UserEntrega = utf8_decode($row["UsuarioEntrega"]);
$Local       = utf8_decode($row["Local"]); 
$Cliente     = utf8_decode($row["Cliente"]);
$Registro    = utf8_decode($row["FechaIngreso"]);
$Entrega     = utf8_decode($row["FechaEntrega"]);
$Estado      = utf8_decode($row["Estado"]);
$Serie       = $row["Serie"];
$NumeroOrden = $row["NumeroOrden"];
$Impuesto    = $row["Impuesto"];
$Importe     = $row["Importe"];
$RUC         = $row["RUC"];

//Imprime Comrpobante
//$pdf=new PDF();
$pdf = new PDF ( 'P' , 'mm' , array ( 210 , 297 ));

$pdf->Open();
$pdf->AddPage();
$pdf->Ln(30);

//PROFORMA
$pdf->SetX(90);
$pdf->SetFont('Courier','BU',13);	
$pdf->Cell(125,4,strtoupper("Orden de Servicio ".$Codigo) );

$pdf->Ln(6);

//FECHA
$pdf->SetX(130);
$pdf->SetFont('Courier','',9);	
$pdf->Cell(130,4,"Registrado el ".$Registro);
$pdf->Ln(8);

// NOMBRE   

$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Cliente  : '));

$pdf->SetX(38); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Cliente);
 
$pdf->SetX(135); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('RUC: '));

$pdf->SetX(143); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$RUC);

$pdf->Ln(4);

$pdf->SetX(18); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Estado   :'));

$pdf->SetX(38); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Estado);

$pdf->SetX(70); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Ingreso:'));

$pdf->SetX(97); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Registro);

$pdf->SetX(135); 
$pdf->SetFont('Courier','B',9);	
$pdf->Cell(120,4,utf8_decode('Fecha Entrega:'));

$pdf->SetX(163); 
$pdf->SetFont('Courier','',9);	
$pdf->Cell(120,4,$Entrega);


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
$pdf->Cell(6,4,"#",1,0,'C',1);	
$pdf->Cell(172,4,utf8_decode("Descripción"),1,0,'D',1);

$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210,210,210);
$pdf->SetLineWidth(.2);
$pdf->SetFont('Courier','',8);

$pdf->Ln(4);	
$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->Cell(6,4,"",'',0,'C');	
$pdf->Cell(172,4,"",'',0,'C');
$pdf->Ln(2);	


  $sql = "SELECT IdOrdenServicioDet, ".
         "       ges_ordenserviciodet.IdProducto, ".
         "       IdUsuarioResponsable, ".
         "       IdComprobante, ".
         "       DATE_FORMAT(FechaInicio,'%d/%m/%Y %H:%i') as FechaInicio, ".
         "       DATE_FORMAT(FechaFin,'%d/%m/%Y %H:%i') as FechaFin, ".
         "       ges_ordenserviciodet.Estado, ".
         "       Garantia, EstadoGarantia, GarantiaCondicion, EstadoSolucion, ".
         "       IF(Concepto like '',' ',Concepto) as Concepto, ".
         "       IF(NumeroSerie like '',' ',NumeroSerie) as NumeroSerie, ".
         "       Unidades, Precio, Importe, ".
	 "       IF(ges_productos.Servicio='0',CONCAT(ges_productos_idioma.Descripcion,' ',".
	 "       ges_marcas.Marca,' ',".
	 "       ges_modelos.Color,' ',".
	 "       ges_detalles.Talla,' ',".
	 "       ges_laboratorios.NombreComercial),ges_productos_idioma.Descripcion) as Producto,".
         "       IF(IdUsuarioResponsable = 0,' ',(SELECT ges_usuarios.Nombre 
                    FROM ges_usuarios WHERE ges_usuarios.IdUsuario = 
                    ges_ordenserviciodet.IdUsuarioResponsable)) as Usuario, ".
         "       IF(ges_productos.Servicio = '0',' ',(SELECT CONCAT(TipoServicio,'~',SAT) FROM ges_tiposervicio WHERE IdTipoServicio = ges_productos.Servicio )) as TipoServicio, ".
         "       ges_productos.UnidadMedida, ".
         "       ges_ordenserviciodet.TipoProducto ".
         "FROM   ges_ordenserviciodet ".
	 "LEFT  JOIN ges_productos ON ges_ordenserviciodet.IdProducto = ges_productos.IdProducto ".
	 "INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	 "INNER JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla ".
	 "INNER JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor ".
	 "INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	 "INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
         "WHERE  ges_ordenserviciodet.Eliminado = 0 ".
         "AND    ges_ordenserviciodet.IdOrdenServicio = '$IdOrdenServicio' ".
         "ORDER BY NumList ASC ";

        $res=query($sql);

    	$contador=1;
        $totalbruto='';
        $item = 1;
        $osdConcepto = '';

        while ( $row = Row($res) ) {
	  $IdOrdenServicioDet = $row["IdOrdenServicioDet"];
	  $osdCantidad  = $row["Unidades"];
	  $osdImporte   = $row["Importe"];
	  $osdPrecio    = $row["Precio"];
	  $osdConcepto  = ($row["Concepto"] == ' ')? $row["Producto"]:$row["Concepto"];
          $osdUndMedida = utf8_decode($row["UnidadMedida"]);
	  $osdTipoProducto = $row["TipoProducto"];
	  $osdNumeroSerie = $row["NumeroSerie"];

	  if($osdNumeroSerie != ' ')
	    $osdConcepto = $osdConcepto.' '.$osdNumeroSerie;

	  $aMotivo      = array();
	  $aDiagnostico = array();
	  $aSolucion    = array();

	  $xtiposerv    = explode('~',utf8_decode($row["TipoServicio"]));

	  if(utf8_decode($row["TipoServicio"]) == ' ')
	    $xtiposerv[1] = '';

	  if($xtiposerv[1] == 1){

	    $xsql = "SELECT ges_motivosat.Motivo, ".
	            "       ges_productossat.Diagnostico, ".
	            "       ges_productossat.Solucion, ".
	            "       ges_productossat.IdProductoSat, ".
	            "       ges_productossat.Detalle, ".
	            "       ges_productossat.NumeroSerie ".
	            "FROM   ges_productossat ".
	            "INNER JOIN ges_motivosat ON ges_productossat.IdMotivoSat = ges_motivosat.IdMotivoSat ".
	            "WHERE  ges_productossat.IdOrdenServicioDet = '$IdOrdenServicioDet' ";
	    $xrow = queryrow($xsql);

	    $ospMotivo      = trim(utf8_decode($xrow["Motivo"]));
	    $ospDiagnostico = trim(utf8_decode($xrow["Diagnostico"]));
	    $ospSolucion    = trim(utf8_decode($xrow["Solucion"]));
	    $ospIdProdSat   = $xrow["IdProductoSat"];
	    

	    if($ospMotivo != '')
	      $aMotivo   =  getItemProducto($ospMotivo,105);
	    else
	      $aMotivo[0] = '';

	    if($ospDiagnostico != '')
	      $aDiagnostico = getItemProducto($ospDiagnostico,105);
	    else
	      $aDiagnostico[0] = "";

	    if($ospSolucion != '')
	      $aSolucion    = getItemProducto($ospSolucion,105);
	    else
	       $aSolucion[0]  = '';

	    if(trim($xrow["NumeroSerie"]) != '')
	      $osdConcepto  = $osdConcepto." ".$xrow["NumeroSerie"];
	    
	    if($xrow["Detalle"] == 1){
	      $ysql = "SELECT ges_productosidiomasat.Descripcion, ".
		      "       ges_marcas.Marca, ".
		      "       ges_modelosat.Modelo, ".
		      "       NumeroSerie ".
		      "FROM   ges_productossatdet ".
		      "INNER JOIN ges_productosidiomasat ON ges_productossatdet.IdProdBaseSat = ges_productosidiomasat.IdProdBaseSat ".
		      "INNER JOIN ges_marcas ON ges_productossatdet.IdMarca = ges_marcas.IdMarca ".
		      "INNER JOIN ges_modelosat ON ges_productossatdet.IdModeloSat = ges_modelosat.IdModeloSat ".
		      "WHERE  ges_productossatdet.IdProductoSat = '$ospIdProdSat' ";

	      $dres = query($ysql);
	      $ospdDetalle = '';
	      $xsep = '';
	      while($yrow = Row($dres)){
		$ospdDetalle .= $xsep.$yrow["Descripcion"]." ".
		                $yrow["Marca"]." ".
		                $yrow["Modelo"]." ".
		                $yrow["NumeroSerie"];
		$xsep = ' - ';
	      }

	      $osdConcepto = $osdConcepto.", ".$ospdDetalle;
	    }
	  }

	  if($osdTipoProducto == 'Producto')
	    $osdConcepto = $osdConcepto.' ('.$osdCantidad.' '.$osdUndMedida.')';

	  $pdf->SetX(17); 
	  $pdf->Cell(1);
	  $pdf->SetFont('Arial','',9);
	  
	  //PRODUCTO ITEM
	  $acotado = array();
	  $acotado = getItemProducto($osdConcepto,105);
	  

	  // IMPRIME LINE
	  $pdf->SetFont('','B',8);
	  $pdf->Cell(6,4,$item,'',0,'C');	
	  $pdf->Cell(172,4,$acotado[0],'',0,'L');
	  $pdf->Ln(4); 

	  //TEXT EXTRA LINE run

	  foreach ($acotado as $key=>$line){
	    if($key>0 && $key < 27 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->Cell(6,4,"",'',0,'C');
	      $pdf->Cell(172,4,$line,'',0,'L');
	      $pdf->Ln(4);
	      $contador++;
	      $acotadoext = 0;
	    }
	  }


	  if($xtiposerv[1] == 1 ){
	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->SetFont('','B');	
	      $pdf->Cell(6,4,"",'',0,'C');
	      $pdf->Cell(172,4,utf8_decode(" Motivo: "),'',0,'L');

	      $pdf->SetX(37); 
	      $pdf->SetFont('','');	
	      $pdf->Cell(172,4,$aMotivo[0],'',0,'L');
	      $pdf->Ln(4);

	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->SetFont('','B');
	      $pdf->Cell(6,4,"",'',0,'C');
	      $pdf->Cell(130,4,utf8_decode(" Diagnóstico: "),'',0,'L');

	      $pdf->SetX(45); 
	      $pdf->SetFont('','');	
	      $pdf->Cell(172,4,$aDiagnostico[0],'',0,'L');
	      $pdf->Ln(4);

	      foreach ($aDiagnostico as $key=>$line){
		if($key>0 && $key < 27 ){
		  $pdf->SetX(17); 
		  $pdf->Cell(1);
		  $pdf->Cell(6,4,"",'',0,'C');
		  $pdf->Cell(172,4,$line,'',0,'L');
		  $pdf->Ln(4);
		  //$contador++;
		  //$acotadoext = 0;
		}
	      }

	      //lineaExtra($aDiagnostico);

	      $pdf->SetX(17); 
	      $pdf->Cell(1);
	      $pdf->SetFont('','B');
	      $pdf->Cell(6,4,"",'',0,'C');
	      $pdf->Cell(130,4,utf8_decode(" Resultado: "),'',0,'L');

	      $pdf->SetX(43); 
	      $pdf->SetFont('','');
	      $pdf->Cell(120,4,$aSolucion[0],'',0,'L');
	      $pdf->Ln(4);

	      foreach ($aSolucion as $key=>$line){
		if($key>0 && $key < 27 ){
		  $pdf->SetX(17); 
		  $pdf->Cell(1);
		  $pdf->Cell(6,4,"",'',0,'C');
		  $pdf->Cell(172,4,$line,'',0,'L');
		  $pdf->Ln(4);
		  //$contador++;
		  //$acotadoext = 0;
		}
	      }


	      //lineaExtra($aDiagnostico);

	      //$contador++;

	  }



	  // CONTADOR
	  $contador++;
	  $item++;
	  
	};
	
	while ($contador<2)
	{
          $pdf->SetX(17);
	  $pdf->Cell(1);
          $pdf->Cell(6,4,"",'',0,'C');
	  $pdf->Cell(172,4,"",'',0,'C');	
	  $pdf->Ln(4);	
	  $contador=$contador +1;
	}

//################### MENSAJE FOOTER 
          //############## LINEA 2
          $fecha   = implota($fechahoy=date("Y-m-d"));
          $hora    = date("H:i");
          $esTPV   = getSesionDato("TipoVentaTPV");
          $esTPV   = ($esTPV == 'VD')? 'B2C':'B2B';

          //############## LINEA 3
          $pdf->Ln(-3);	
          $pdf->SetX(17); 
	  $pdf->Cell(1);
          $pdf->Cell(6,4,"",'B',0,'C');
          $pdf->Cell(172,4,"",'B',0,'C');
	  $pdf->Ln(6);	

          $pdf->SetX(18); 
          $pdf->SetFont('Arial','',8);
          $pdf->Cell(140,4,"TPV: ".$esTPV." / OP: ".$operador." / F.Imp.: ".$fecha."  ".$hora);
//#######################  final orden servicio

//####################################  	
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

$pdf->Ln(6);
$pdf->SetX(17); 
$pdf->SetFont('Courier','UB',9);	
$pdf->Cell(48,4,utf8_decode('IMPORTANTE:'));
$pdf->SetFont('Courier','',11);	
$pdf->Cell(1,4,'.');
$pdf->Ln(6);	

$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->SetFont('','',8);	
$pdf->MultiCell(178,4,utf8_decode("- Debe presentar este documento o referir el N° de Orden para recoger el equipo"));

$pdf->SetX(17); 
$pdf->Cell(1);
$pdf->MultiCell(178,4,utf8_decode("- La empresa no se responsabiliza por los equipos que sean dejado por más de 06 meses sin recoger."));
$pdf->Ln(4);


@mysql_free_result($resultado); 
@mysql_free_result($query);
@mysql_free_result($resultado2); 
@mysql_free_result($query3);
//#### NOMBRE DEL FICHERO
$name = "Orden Servicio-".getSesionDato("TipoVentaTPV")."-LOCAL-".$IdLocal."-NRO-".$Codigo.".pdf";
$pdf->Output($name,'');

?> 
