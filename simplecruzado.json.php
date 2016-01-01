<?php

include("tool.php");

SimpleAutentificacionAutomatica("novisual-services");

require_once "class/json.class.php";
$json = new Services_JSON();

//$idprod = CleanID($_GET["IdProducto"]);

//if (!$idprod) {
$cod = CleanCB($_GET["CodigoBarras"]);
$idprod = getIdFromCodigoBarras($cod);
//}

$value = genListadoCruzadoAsArray($idprod);

$output = $json->encode($value);

print($output);



function genListadoCruzadoAsArray($IdProducto,$IdTallaje = false,$IdLang=false){	
	$IdProducto = CleanID($IdProducto);
	$IdTallaje = CleanID($IdTallaje);
	
	$out = "";//Cadena de salida
	
	if(!$IdLang)	$IdLang = getSesionDato("IdLenguajeDefecto");
	
	$GiroEmpresa = getSesionDato("GlobalGiroNegocio");
	
	$sql = "SELECT CodigoBarras,Referencia, IdTallaje FROM ges_productos WHERE IdProducto='$IdProducto' AND Eliminado='0'";
	$row = queryrow($sql);
	if (!$row)	return false;
	
	$tReferencia   = CleanRealMysql($row["Referencia"]);
	$tCodigoBarras = CleanRealMysql($row["CodigoBarras"]);
	$filtro        = ($GiroEmpresa != 'BTQE')? " ges_productos.CodigoBarras='$tCodigoBarras' AND ":" ";
	$Moneda       = getSesionDato("Moneda");

	if(!$IdTallaje)	$IdTallaje = $row["IdTallaje"];
	if(!$IdTallaje) $IdTallaje = 2;//gracefull degradation
	
	$sql = "SELECT  ges_locales.NombreComercial,ges_modelos.Color,
		ges_detalles.Talla, ges_detalles.SizeOrden, SUM(ges_almacenes.Unidades) as 
                TotalUnidades, ges_productos.UnidadMedida, ges_almacenes.PrecioVenta  
                FROM ges_almacenes INNER
		JOIN ges_locales ON ges_almacenes.IdLocal = ges_locales.IdLocal INNER
		JOIN ges_productos ON ges_almacenes.IdProducto =
		ges_productos.IdProducto INNER JOIN ges_modelos ON
		ges_productos.IdColor = ges_modelos.IdColor INNER JOIN ges_detalles ON
		ges_productos.IdTalla = ges_detalles.IdTalla
		WHERE
                $filtro
                ges_productos.Referencia = '$tReferencia'
		AND ges_modelos.IdIdioma = 1
		AND ges_locales.Eliminado = 0 
                AND ges_almacenes.Unidades > 0 
		GROUP BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla
		ORDER BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla";
		
	$data = array();
	$colores = array();
	$tallas = array();
	$locales = array();
	$tallasTallaje = array();
	$listaColores = array();
	$numtallas =0;	
	$res = query($sql,"Generando Listado Cruzado");

	while( $row = Row($res) ){
		$color 		= $row["Color"];
		$talla 		= NormalizaTalla($row["Talla"]);		
		$nombre 	= $row["NombreComercial"];
		$unidades 	= CleanInt($row["TotalUnidades"]).' '.$row["UnidadMedida"].' - '.$Moneda[1]['S'].$row["PrecioVenta"];
		$colores[$color] = 1;
		$tallas[$talla] = 1;
		$locales[$nombre] = 1;
	
		$num = 0;
		

		//tallas
		$orden = intval($row["SizeOrden"]);
		$italla = NormalizaTalla($row["Talla"]);
		$posicion = GetOrdenVacio($tallasTallaje,$orden,$italla);  
		$tallasTallaje[$posicion]  = $italla;
		$numtallas++; 

		//echo "Adding... c:$color,t:$talla,n:$nombre,u:$unidades<br>";		
		$data[$color][$talla][$nombre] =$unidades;
		
	}
		
	/* $sql = "SELECT Talla,SizeOrden FROM ges_detalles WHERE IdTallaje= '$IdTallaje' AND IdIdioma='$IdLang' AND Eliminado='0'" . */
	/* 		"	 ORDER BY SizeOrden ASC, Talla + 0 ASC"; */
	/* $res = query($sql); */

	/* $numtallas =0; */
	/* while($row = Row($res)){ */
	/* 	$orden = intval($row["SizeOrden"]); */
	/* 	$talla = NormalizaTalla($row["Talla"]); */
	/* 	$posicion = GetOrdenVacio($tallasTallaje,$orden,$talla);   */
	/* 	$tallasTallaje[$posicion]  = $talla; */
	/* 	$numtallas++;  */
	/* } */
	
	//$out .= "<table class='forma'>";	
	//$out .= "<tr><td class='nombre'>".$tReferencia."</td>";
	$out_nombretabla = $tReferencia;
	
	
	
	$out_tallas = array();
	$out_tallas["talla_0"] = "LOCALES";
	$out_tallas["talla_1"] = "M o d e l o";  
	
	$num = 2;
	
	foreach ($tallasTallaje as $k=>$v) {
		$out_tallas["talla_$num"] = $v;
		$num++;
	}

	
	$out_base = array();	
	$out_rows = array();
	
	$numrow = 0;
	
	$out_filas = array();
	$out_bloques = array();
	
	foreach ($locales as $l=>$v2){	
		$out_base["nombre"] = $l;		
		$out_bloques[] = $l;				 
		foreach ($colores as $c=>$v1){	
	
			$row = array();
				
			$row[] = $l;
			$row[] = $c;
			 						
			foreach ($tallasTallaje as $k2=>$t) {
				
				if (isset($data[$c][$t][$l]))
					$u = $data[$c][$t][$l];
				else
					$u = "";
				//$out .= "<td class='unidades' align='center'>" . $u . "</td>";
				
				$row[] = $u;										
			}
			$out_rows[] = $row;	
			
			//$out .= "</tr>";
		}
					
	}
	//$out .= "</table>";
	
	
	$out_final = array();
	$out_final["heads"] = $out_tallas;
	$out_final["rows"] = $out_rows;
	$out_final["numheads"] = count($out_tallas);
	//$out_final["rowheads"] = $out_filas;	
	$out_final["nombretabla"] = $out_nombretabla;
	//$out_final["bloques"] = $out_bloques;
	return $out_final;
}




?>
