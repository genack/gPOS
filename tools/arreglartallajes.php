<?php

include("../tool.php");


//Normalizar familias estropeadas
query("UPDATE ges_productos SET IdFamilia=3 , IdSubFamilia=2 WHERE IdFamilia=1");


$sql = "SELECT * FROM ges_productos";

$resprod = query($sql);

while( $row = Row($resprod) ){		
	$IdProducto = $row["IdProducto"]; 
	$IdTalla = $row["IdTalla"];
	$sql = "SELECT IdTallaje FROM ges_detalles WHERE IdTalla = '$IdTalla'";
	$restalla = query($sql);
	if ($restalla){
		if ($rowtallaje = Row($restalla) ){
			$IdTallaje = $rowtallaje["IdTallaje"];
			$sql = "UPDATE ges_productos SET IdTallaje = '$IdTallaje' WHERE IdProducto='$IdProducto' ";
			echo ($sql. "<br>");
			query($sql);			
		}	
	}	
}




?>
