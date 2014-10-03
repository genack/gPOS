<?php
 


include("../tool.php");

$sql = "DELETE FROM ges_logsql2";
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


	$sqllimpio = str_replace("\n", "", $sqloriginal);	
	
		$num = $num + 1;
	 	
	 	$coste = "";
	 	$ref = "";
	 	
	 	$datos = split("VALUES \(",$sqllimpio);
	 	if (count($datos)>0 and  $nick== "alta producto"){
	 		//echo "Fila: " . $datos[1] . "<br>";
	 		$values = split(",",$datos[1]);
	 		echo "D1: " . LimpiaComas($values[0]). "<br>D3: " . LimpiaComas($values[2]) . "<br>";
	 		$ref 	= 	LimpiaComas($values[1]);
	 		$coste 	= 	LimpiaComas($values[2]);
	 			 		
	 	}
	 
		$exeql = "INSERT INTO ges_logsql2 (TipoProceso,Sql,IdCreador,FechaCreacion,Referencia,Precio) VALUES ('$nick','$sql','$idcreador','$fecha','$ref','$coste')";
		query($exeql,"CONVERSOR");
	/*} else {
		//echo "<font size=-5>$sql</font><br>";	
	}*/
}

echo "Se encontraron $num sentencias<br>";
echo "Ya ha terminado";  
  
 
?>