<?php
/*
 * Created on 10-feb-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
include("../tool.php");


$sql = "SELECT Referencia, Precio FROM ges_logsql2";

$res = query($sql);


while($row = Row($res)){

	$coste	= $row["Precio"];
	$ref	= $row["Referencia"]; 

	$sql = "UPDATE ges_productos SET Costo = '$coste' WHERE CodigoBarras='$ref'"; 
	echo "<font size=-1>$sql</font><br>";
	query($sql);
}
 
 
?>
