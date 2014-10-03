<?php

class PDF extends FPDF
{
//Cabecera de pgina
function Header()
{
    //Logo
    $this->Image('./logo/gpos_encabezado_pdf.png',12,8,150);
    $this->Ln(24);	

}

//Pie de pgina
function Footer()
{

  $this->SetFont('Arial','',6);
  $this->SetY(-21);
  $this->Cell(0,10,'',0,0,'C');
  $this->SetY(-18);
  $this->Cell(0,10,html_entity_decode(''),0,0,'C');
  $this->SetY(-15);
  $this->Cell(0,10,html_entity_decode(''),0,0,'C');
  $this->SetY(-12);

  /** $this->Cell(0,10,'___________________',0,0,'C');	
  $this->Ln(4);	
  $this->Cell(0,10,"Administrador",0,0,'C');*/	
  //$this->Ln(4);	
  $this->SetFont('Arial','',8);
  $fecha  =implota($fechahoy=date("Y-m-d"));
  $fecha .= " ".date("H:i");
  $this->Cell(0,10,utf8_decode('Fecha de impresión '). $fecha. '.',0,0,'C');	
  //$this->Cell(0);
  $this->Cell(-5,10,utf8_decode('Página ').$this->PageNo(),0,0,'R');
}

}
?>