<?php
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('code39.php');
include("../../tool.php");
require("qrcode/qrlib.php");

$codigo=$_GET["codigo"];
$IdLocal      = getSesionDato("IdTienda");
$descripcion  = getDescripcionFromCBMetaProducto($codigo);
$detalle      = getDetFromCBMetaProducto($codigo);

$codigobarras = $codigo;
$paginaweb    = getPaginaWebLocalId($IdLocal); 
$nombrelegal  = getNombreLegalLocalId($IdLocal);

$longcadena   = strlen($descripcion);
$descripcion1 = ( $longcadena>75 )? substr($descripcion,0,strrpos(substr($descripcion,0,75)," ")): substr($descripcion.".", 0, 75);
$descripcion2 = ( $longcadena>75 )? substr($descripcion.".",$longcadena+1,100):"";


class PDF extends FPDF
{
function EAN13($x,$y,$barcode,$h=40,$w=.82)
{
	$this->Barcode($x,$y,$barcode,$h,$w,8);
}

function UPC_A($x,$y,$barcode,$h=8,$w=.25)
{
	$this->Barcode($x,$y,$barcode,$h,$w,12);
}

function GetCheckDigit($barcode)
{
	//Compute the check digit
	$sum=0;
	for($i=1;$i<=11;$i+=2)
		$sum+=3*$barcode{$i};
	for($i=0;$i<=10;$i+=2)
		$sum+=$barcode{$i};
	$r=$sum%10;
	if($r>0)
		$r=10-$r;
	return $r;
}

function TestCheckDigit($barcode)
{
	//Test validity of check digit
	$sum=0;
	for($i=1;$i<=11;$i+=2)
		$sum+=3*$barcode{$i};
	for($i=0;$i<=10;$i+=2)
		$sum+=$barcode{$i};
	return ($sum+$barcode{12})%10==0;
}

function Barcode($x,$y,$barcode,$h,$w,$len)
{
	//Padding
	$barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
	if($len==7)
		$barcode='0'.$barcode;
	//Add or control the check digit
	if(strlen($barcode)==8) {}
	//$barcode.=$this->GetCheckDigit($barcode);
	elseif(!$this->TestCheckDigit($barcode))
		$this->Error('Error codigo incorrecto');
	//Convert digits to bars
	$codes=array(
		'A'=>array(
			'0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
			'5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
		'B'=>array(
			'0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
			'5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
		'C'=>array(
			'0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
			'5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
		);

	$parities=array(
		'0'=>array('A','A','A','A','A','A'),
		'1'=>array('A','A','B','A','B','B'),
		'2'=>array('A','A','B','B','A','B'),
		'3'=>array('A','A','B','B','B','A'),
		'4'=>array('A','B','A','A','B','B'),
		'5'=>array('A','B','B','A','A','B'),
		'6'=>array('A','B','B','B','A','A'),
		'7'=>array('A','B','A','B','A','B'),
		'8'=>array('A','B','A','B','B','A'),
		'9'=>array('A','B','B','A','B','A')
		);
	$code='101';
	$p=$parities[$barcode{0}];
	for($i=1;$i<=6;$i++)
		$code.=$codes[$p[$i-1]][$barcode{$i}];
	$code.='01010';

	for($i=7;$i<=12;$i++){
	  if( isset( $barcode{$i} ) )
	    if( isset( $codes['C'][$barcode{$i}] ) )
	      $code.= $codes['C'][$barcode{$i}];
	}
	$code.='101';
	//Draw bars
	for($i=0;$i<strlen($code);$i++)
	{
		if($code{$i}=='1')
			$this->Rect($x+$i*$w,$y,$w,$h,'F');
	}
	//Print text uder barcode
	$this->SetFont('Arial','',14);
	$this->Text($x+27,$y+$h+15/$this->k,substr($barcode,-$len));
}
}

$pdf=new PDF(); 

$pdf=new PDF ('P','mm',array(210,148.5) );

QRcode::png($detalle, '/tmp/qr.png', 'L', 3, 2);

$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('times','',5);
//$pdf->Text(0,1,"._ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ." );
$pdf->SetFont('times','',16);
$pdf->Text(60,6,$nombrelegal);
$pdf->SetFont('Arial','',9);
$pdf->Text(5,10,$descripcion1);
$pdf->Text(5,14,$descripcion2);
$pdf->Image('/tmp/qr.png',55,14,92,92,'PNG','');
$pdf->EAN13(4,16.5,$codigobarras);
$pdf->SetFont('helvetica','',14);
$pdf->Text(9,80,$paginaweb);
$pdf->SetFont('times','',5);
$pdf->Text(0,105,"._ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ __ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _." );
$pdf->SetFont('times','',16);
$pdf->Text(60,111,$nombrelegal);
$pdf->SetFont('Arial','',9);
$pdf->Text(5,115,$descripcion1);
$pdf->Text(5,119,$descripcion2);
$pdf->Image('/tmp/qr.png',55,119,92,92,'PNG','');
$pdf->EAN13(4,121.5,$codigobarras);
$pdf->SetFont('helvetica','',14);
$pdf->Text(9,185,$paginaweb);
$pdf->SetFont('times','',5);
//$pdf->Text(0,97,"._ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ ." );
//$pdf->Output();
//#### NOMBRE DEL FICHERO
$name = $codigobarras.".pdf";
$pdf->Output($name,'');

?>
