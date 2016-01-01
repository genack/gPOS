<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$salida_funcion = "";
//$estilosalto = "page-break-after: always;";
$estilosalto = "";
$estilosaltotable= "page-break-before: always;";


function ListadoTiendasPrecios($IdProducto,$copias) {
	global $action,$salida_funcion;
	
	$ot = getTemplate("SeleccionEtiqueta");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		echo "3!";
		return false; }

	$almacen = getSesionDato("Articulos");
		
	$cr = "<br>";
	$res = $almacen->Listado(false,$IdProducto,false);
	$out = "";	
	
	$precioVarios = false;
	$precioold = "old";
	
	$numPreciosValidos = 0;
	
	while($almacen->SiguienteArticulo() ){
		$ajustar ="";
		$vPrecio =  $almacen->get("PrecioVenta");		
		
		if ($vPrecio<0.01)
			continue;
		
		$tPrecio =  FormatMoney( $vPrecio );
		
		if ($precioold == "old")
			$precioold = $vPrecio;
			
		//echo ("precioold $precioold, vprecio $vPrecio<br>");
		
		if ($precioold != $vPrecio){			
			$precioVarios = true;							
		} 	
			
			
		$out .= "<tr>";
		$out .= g("td",$almacen->get("Identificacion"));//Nombre del local
		
		$id = $almacen->get("IdProducto");
		$ajustar .= "AjustarCampo(\"precio\",\"$vPrecio\");"; 
		//$ajustar .= "AjustarCampo(\"precio\",\"$vPrecio\");";
		
		$out .= g("td","<input type=submit onclick='$ajustar' value='$tPrecio'>" );		
		$out .= "</tr>";
		
		$numPreciosValidos = $numPreciosValidos + 1;
		
	}
	
	if ($numPreciosValidos==0) {
	        echo gas("Aviso",_("Fije un Precio de Venta TPV, para imprimir etiquetas."));?>
         <center>
	   <form action="selimpresion.php?modo=codigobarrasProductoGet" method="post">
	     <table class='forma'>
	       <tr>
		 <td class="lh">
		   <?php echo _("Precio:") ?>
		 </td>
		 <td>
		   <input value="0" type="text" 
			  onkeypress="return soloNumerosBase(event,this.value)" name="precio">
		   </td>
		 </tr>
		 <tr>
		   <td class="lh"></td>
		   <td>
		     <input class="btn item" value="<?php echo _("Enviar") ?>" type=submit>
		   </td>
		 </tr>
	       </table>
	       <input type="hidden" name="copias" value="<?php echo $copias ?>">	
	       <input type="hidden" name="IdProducto" value="<?php echo $IdProducto ?>">	
	     </form>	 			
	   </center>
       <?php return false;	
	} else if (!$precioVarios){		
		return 	floatval($precioold);//quickprecio o cero
	}
	
	$ot->fijar("cuerpo",$out);
	$ot->fijar("action",$action ."?modo=codigobarrasProductoGet");
	$ot->fijar("IdProducto",$IdProducto);
	$ot->fijar("copias",$copias);	
	echo $ot->Output();
	//$salida_funcion =  $ot->Output();
		
	return false;//Si hay mas de uno, no devuelve quickprecio
}

define("PAGE_FONDOBLANCO",true);

PageStart(_("Impresión"),false,PAGE_FONDOBLANCO);

//echo CenterOpen();

//selimpresion.php?modo=codigobarrasProducto&id=154&copias=1


switch($modo){
	case "codigobarrasProducto":
		$id = CleanID($_GET["id"]);
		$copias  = intval($_GET["copias"]);
			
		$quickPrecio = ListadoTiendasPrecios($id,$copias);
		
		if ($quickPrecio) {		
			for($t=0;$t<$copias;$t++) {
				//?echo "<div style='$estilosalto'>";
				echo GenEtiqueta($id,$quickPrecio);				
				//?echo "</div>";
			}
						
			echo "<script>window.print()</script>";
			break;
		}
		error(__FILE__ . __LINE__ ,"Info: impreso listado");
		//$id = getIdFromCodigoBarras($bar);	
		//ListadoTiendasPrecios($id,$copias);			
		break;						
		
	case "codigobarrasProductoGet":
 		
		$id      = CleanID($_POST["IdProducto"]);
		$idlocal = getSesionDato("IdTienda");
		$copias  = intval($_POST["copias"]);				
		$precio  = CleanDinero($_POST["precio"]);	

		if (!isset($_POST["precio"]) and isset($_GET["Precio"]))
			$precio = CleanDinero($_GET["Precio"]);	
		if (!$id) break;

		actualizarAllPreciosVentaAlmacen($id,$precio,$idlocal);
		
		for($t=0;$t<$copias;$t++) {
			//?echo "<div style='$estilosalto'>";
			echo GenEtiqueta($id,$precio);
			//?echo "</div>";
		}
		
		echo "<script>window.print()</script>";
		break;		

		
	case "codigobarras":
		$bar = CleanID($_GET["id"]);
		$copias  = intval($_GET["copias"]);
		$id = getIdFromCodigoBarras($bar);	
		ListadoTiendasPrecios($id,$copias);	
		break;

	default:
		break;	
}



PageEnd(false);

?>
