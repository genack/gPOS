<?php
include("../../tool.php");

SimpleAuth("logactivo",$module_password);

$num = intval($_GET["num"]);

if (!$num)
	$num = 4000;


////////////////////////////////////////////////////////
// COSTANTES


	PageStart();
	
?>
<style>

*, td {	
 font-size: 96%;	
}

</style>
<?php	
	
	
	
	$rnd = rand();
	
	echo "<a href='$action?$rnd#Log'>Reload</a> - <a href='#Log'>Log</a>";

	/*
	echo "<a name='Sesion'>";
	if ($_GET["sesion"]!="no"){
		echo "<table border=1 width=100%>";
		foreach($_SESSION as $key=>$value){
				
			if (is_array($value)){
				$value = var_export($value,true);
			}					
			
			$code = htmlentities($value,ENT_QUOTES,'UTF-8');			
			if ($key == "QuerysRealizadas" or $key=="Errores_Session"){				
				$code = "<pre>$code</pre>";	
			}					
			echo g("tr",g("td class='fact' width=10%",$key).g("td",$code));			
		}			
		echo "</table>";
	}
	*/
			
	$sql = "SELECT * FROM ges_logsql ORDER BY FechaCreacion DESC, Idlogsql DESC  LIMIT $num";
	
	$res = query($sql,"------");
	
	echo "<a name='Log'></a><a href='$action?$rnd#Log'>Reload</a> - <a href='#Sesion'>Sesion</a>";
	echo "<table border=1 width=100%>";
	if ($res){
		while($row = Row($res)){
			$sql = base64_decode($row["Sql"]); 

			if ( 
				!(strpos( $sql,"IdCliente" )===false) 				


				){
				if ($row["Exito"]==0)
					$sql = gColor("red",$sql);
				$nick = $row["TipoProceso"];
							
				echo g("tr",g("td class=fact",$row["FechaCreacion"]).g("td",$nick).g("td",htmlentities($sql,ENT_QUOTES,'UTF-8')));		
			}	
		}			
	} else{
		echo g(br,q($sql) . " no mola");	
	}	
		
	echo "</table>";
	
	echo "<a href='#Sesion'>Sesion</a><br>";
	
	PageEnd();
	
?>