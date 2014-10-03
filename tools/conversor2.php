<?php


include("../tool.php");


query($sql);

$sql = "SELECT * FROM ges_logsql ORDER BY FechaCreacion DESC, Idlogsql DESC";	
$res = query($sql);

	 
function LimpiaComas($cadena){
	$cad = str_replace("'","",$cadena);
	$cad = trim($cad);
	return $cad;	
} 
	 
$num = 0;	 
while ( $row = Row($res) ) {
	$sqloriginal = base64_decode($row["Sql"]);
	$sql 		= CleanRealMysql($sqloriginal,false); 
	$nick 		= $row["TipoProceso"];
	$fecha 		= CleanRealMysql($row["FechaCreacion"]);
	$idcreador 	= $row["IdCreador"];	
	
	$sqlcopia = $sqloriginal;
	$contiene = 0;
	
	$firma = "INSERT INTO ges_productos ( Referencia, CodigoBarras, Costo, IdFamilia, IdSubFamilia, IdProvHab, IdMarca, IdTallaje, IdTalla, RefProvHab, IdColor, IdProdBase";
	$firma = "INSERT INTO ges_productos (";
//	$sqlcopia = str_replace("\n", "", $sqlcopia);
	$sqllimpio = str_replace("\n", "", $sqloriginal);
	$sqlcopia = str_replace($firma, "", $sqlcopia);
//	$sqlcopia = str_replace("Servicio", "", $sqlcopia);
	
	
	//IdMarca, IdProdBase, Referencia,
	
	
	
	
	if ($sqlcopia != $sqloriginal){
		$num = $num + 1;
	 	
	 	
	 	$coste = "";
	 	$ref = "";
	 	
	 	$datos = split("VALUES \(",$sqllimpio);
	 	if (count($datos)>0 and (str_replace("clon","",$nick)!=$nick)){
	 		echo "<hr><font size=-5>$sqloriginal</font><br>";
	 		
	 		//echo "Fila: " . $datos[1] . "<br>";
	 		$values = split(",",$datos[1]);
	 		echo "Info: $nick --<br> D1: " . LimpiaComas($values[0]). "<br>D3: " . LimpiaComas($values[2]) . "<br>";
	 		$ref 	= 	LimpiaComas($values[0]);
	 		$coste 	= 	LimpiaComas($values[2]);
	 			 		
	 	}
	 
		//$exeql = "INSERT INTO ges_logsql2 (TipoProceso,Sql,IdCreador,FechaCreacion,Referencia,Precio) VALUES ('$nick','$sql','$idcreador','$fecha','$ref','$coste')";
		//query($exeql,"CONVERSOR");
	} else {
		//echo "<font size=-5>$sql</font><br>";	
	}
}

echo "Se encontraron $num inserciones<br>";
echo "Ya ha terminado";  
  
 
?>