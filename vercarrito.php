<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 30;

function dosdecimales($num){
    return round($num*100)/100;
}
function ListaFormaDeUnidades() {
	//FormaListaCompraCantidades	
	global $action;
	$jsOut          = "";
	$idprodseriebuy = getSesionDato("idprodseriebuy");
	$seriesbuy      = getSesionDato("seriesbuy");
	$Moneda         = getSesionDato("Moneda");

	setSesionDato("idprodseriecart",$idprodseriebuy);
	setSesionDato("seriescart",$seriesbuy);
	$oProducto      = new producto; 
	
	$ot = getTemplate("PopupCarritoCompra");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }

	$ot->resetSeries(array("IdProducto","Referencia","Nombre",
			       "tBorrar","tEditar","tSeleccion","vUnidades",
			       "vTalla","vColor","Serie"));
	
	$tamPagina     = $ot->getPagina();
	$indice        = getSesionDato("PaginadorSeleccionCompras2");			
	$carrito       = getSesionDato("CarritoCompras");
	//echo q($carrito,"Carrito Cantidades");
	$costescarrito = getSesionDato("CarroCostesCompra");
	$descuentos    = getSesionDato("descuentos");
	$quitar        = _("Quitar");

	$ot->fijar("tTitulo",_("Productos"));
	$ot->fijar("comboAlmacenes",getSesionDato("ComboAlmacenes"));
	$ot->fijar("comboAlmacenes",genComboAlmacenes(getParametro("AlmacenCentral")));
	

	$salta = 0;
	$num   = 0;
	$detadoc        = getSesionDato("detadoc");
	$incImpuestoDet = getSesionDato("incImpuestoDet");
	$incPercepcion  = getSesionDato("incPercepcion");
	$igv            = getSesionDato("IGV");
	$ipc            = getSesionDato("IPC");
	$TotalNeto      = 0;
	$TotalBruto     = 0;
	$ImporteFlete   = $detadoc[13];
	$ImportePercepcion = $detadoc[14];
	$ImportePago    = 0;
	$TotalDescuento = 0;
	$BrutoNeto      = 0;
	$TotalImpuesto  = 0;
	
	if ($carrito){

	  foreach ( $carrito as $key=>$value){	
            $TotalBruto     = $TotalBruto + $costescarrito[$key]*$value;
            //$costescarrito[$key];
	    $dscto          = (isset($descuentos[$key][0]))? $descuentos[$key][0]:0;
            $TotalDescuento = $TotalDescuento + $dscto;
            $BrutoNeto      = $BrutoNeto +  $descuentos[$key][1];
	  }	

	  $TotalImpuesto  = $BrutoNeto * $igv/100; 
	  $TotalNeto      = ($incImpuestoDet=="true")? $BrutoNeto : $BrutoNeto + $TotalImpuesto;
	  $BrutoNeto      = ($incImpuestoDet=="true")? $TotalNeto/($igv/100+1) : $BrutoNeto;
 	  $TotalBruto     = ($incImpuestoDet=="true")? $TotalBruto / ($igv/100+1) : $TotalBruto;
	  $TotalImpuesto  = $BrutoNeto * $igv/100; 
	  $TotalImpuesto  = round($TotalImpuesto * 100) / 100; 
	  $TotalDescuento = $TotalBruto - $BrutoNeto;

	  $ImportePercepcion = ( $incPercepcion == "true" && $ImportePercepcion == 0 )? round(( ($TotalNeto*$ipc/100) )*100)/100:$ImportePercepcion; 
	  //$ImporteFleteMoneda  = ($tipomoneda == 1)? $ImporteFlete * $tipocambio:$ImporteFlete;
	  //$ImportePago         = $TotalNeto + $ImporteFlete + $ImportePercepcion;
	  //$ImportePagoMoneda   = ($tipomoneda == 1)? $ImportePago * $tipocambio:$ImportePago;
	  
	  foreach ( $carrito as $key=>$value)
	    {		
	      $salta ++;
	      if ($num <= $tamPagina and $salta>=$indice)
		{		
		  $num++;			
		  
		  $oProducto->Load($key);
		  $precioventa= $descuentos[$key][1]*$igv/100;
		  $precioventa= $descuentos[$key][1]+$precioventa;
		  $vdescuento = (isset($descuentos[$key][0]))? $descuentos[$key][0]:0;
		  $pdescuento = (isset($descuentos[$key][2]))? $descuentos[$key][2]:0;

		  $item = $indice+$num;
		  if($indice==10||$indice==20) $item--;
		  $ot->fijarSerie("vItem",$item.".");
		  $ot->fijarSerie("vReferencia",$oProducto->getCB());		
		  $ot->fijarSerie("vNombre",getDatosProductosExtra($key,"nombre"));
		  $ot->fijarSerie("tBorrar",$quitar);
		  $ot->fijarSerie("vUnidades",$value);
		  //$ot->fijarSerie("vPrecio",dosdecimales($costescarrito[$key]));
		  $ot->fijarSerie("vPrecio",$costescarrito[$key]);
		  $ot->fijarSerie("IdProducto",$oProducto->getId());
		  $ot->fijarSerie("Serie",$oProducto->getSerie());
		  $ot->fijarSerie("vDescuento",dosdecimales($vdescuento));
		  $ot->fijarSerie("PorcentajeDescuento",dosdecimales($pdescuento));
		  $ot->fijarSerie("vImporte",dosdecimales($descuentos[$key][1]));
		  $ot->fijarSerie("vPrecioVenta",dosdecimales($precioventa));
		  $ot->fijarSerie("vVentaMenudeo",$oProducto->getVentaMenudeo());
		  $ot->fijarSerie("vUnidadesPorContenedor",$oProducto->getUnidadesPorContenedor());
		  $ot->fijarSerie("vUnidadMedida",$oProducto->getUnidadMedida());
		  $unimedida  = $oProducto->getUnidadMedida();
		  $menudeo    = $oProducto->getVentaMenudeo();
		  $lt         = $oProducto->getLote();
		  $fv         = $oProducto->getFechaVencimiento();
		  $ns         = $oProducto->getSerie();
		  $contenedor = $oProducto->getContenedor();
		  $vbtn       = ($menudeo)? true:false;
		  $vbtn       = ($lt)?      true:$vbtn;
		  $vbtn       = ($fv)?      true:$vbtn;
		  $contunid   = ($menudeo)? intval($value/$oProducto->getUnidadesPorContenedor()) : "";
		  $unid       = ($menudeo)? $value % $oProducto->getUnidadesPorContenedor() : "";
		  $cadena     = $contunid." ".$contenedor." + ".$unid." ".$unimedida;
		  $cadena     = ($menudeo)? $cadena : $contenedor;
		  $svalbtn    = (validaxdtCarritoProducto($key))? "+":"?";
		  $rbtnns     = ($ns)?          "readonly":"";
		  $rbtnns     = ($detadoc[0]=='O')? "":$rbtnns;
		  $sbtnns     = ($ns)?          "button":"hidden";
		  $sbtn       = ($vbtn)?        "button":"hidden";
		  $svalbtn    = ($detadoc[0]=='O')? "+":$svalbtn;
		  $sbtnns     = ($detadoc[0]=='O')? "hidden":$sbtnns;
		  $sbtn       = ($detadoc[0]=='O')? "hidden":$sbtn;
		  $sbtn       = ($menudeo)?     "button":$sbtn;
		  $sbtncsto   = ($menudeo)?     "button":"hidden";
		  
		  $ot->fijarSerie("vUnidMedida",$unimedida);
		  $ot->fijarSerie("vTotalContenedor",$cadena);
		  $ot->fijarSerie("vButton",$sbtn);
		  $ot->fijarSerie("vButtonCosto",$sbtncsto);
		  $ot->fijarSerie("vValButton",$svalbtn);
		  $ot->fijarSerie("vReadOnly",$rbtnns);
		  $ot->fijarSerie("vBotonSerie",$sbtnns);
		}
	    }
	}
    
	if (!$salta){
	  $ot->fijar("aviso",gas("aviso",_("Carrito vacío")));
	  $ot->eliminaSeccion("haydatos");			
	} else {
	  $ot->fijar("aviso");
	  $ot->confirmaSeccion("haydatos");
	}

	$tpfecha     = 'Fecha Emisión :';
	$tipodoc     = $detadoc[0];
	$nrodoc      = $detadoc[3];
	$anrodoc     = explode("-", $nrodoc);
	$sdoc        = $anrodoc[0];
	$ndoc        = ( isset($anrodoc[1]) )? $anrodoc[1]:false;
	$tnrodoc     = ($nrodoc)?'Nro. '.$nrodoc:'';
	$titulo      = ($tipodoc=='F')?'Factura '.$tnrodoc:'';
	$titulo      = ($tipodoc=='O')?'Pedido '.$tnrodoc:$titulo;
	$titulo      = ($tipodoc=='R')?'Boleta '.$tnrodoc:$titulo;
	$titulo      = ($tipodoc=='G')?'Albar&aacute;n '.$tnrodoc:$titulo;
	$titulo      = ($tipodoc=='SD')?'Ticket'.$tnrodoc:$titulo;
	$tpfecha     = ($tipodoc=='O')?'Fecha Entrega : ':$tpfecha;
	$albaranes   = ($tipodoc=='F')? $detadoc[15]:"";
	$stalbaranes = ($tipodoc=='F')? "block":"none";
	$idprov      = $detadoc[1];
	$nomprov     = $detadoc[2];
	$fecdoc      = $detadoc[4];
	$tipomoneda  = $detadoc[5];

	$checkF       = ($tipodoc == 'F')? 'selected':'';
	$checkO       = ($tipodoc == 'O')? 'selected':'';
	$checkR       = ($tipodoc == 'R')? 'selected':'';
	$checkG       = ($tipodoc == 'G')? 'selected':'';
	$checkSD      = ($tipodoc == 'SD')? 'selected':'';

	$checkedTS   = ($tipomoneda == 1)?'CHECKED':'';
	$checkedTD   = ($tipomoneda == 2)?'CHECKED':'';
	$tipocambio  = $detadoc[6];
	$fechacambio = $detadoc[7];
	$fechapago   = $detadoc[8];
	$idsubsid    = $detadoc[9];
	$nomsubsid   = $detadoc[10];
	$incluyeigv  = (getSesionDato("incImpuestoDet")=='true')?true:false;
	$incluyeipc  = (getSesionDato("incPercepcion")=='true')?true:false;
	$checkipc    = ($incluyeipc)?'CHECKED':'';
	$checkigv    = ($incluyeigv)?'CHECKED':'';
	$tpv         = 'PC';
	$xipc         = ($incluyeipc)?'':'display:none';
	$tvv         = ($incluyeigv)? $tpv:'VC';
	$tcp         = ($incluyeigv)?'Precio/Unid.':'Costo/Unid.';
	$pv          = ($incluyeigv)?'hidden':'text';
	$tdpv        = ($incluyeigv)?'1':'0';
	$colheadcart = ($incluyeigv)?'16':'17';
	$checkcredt  = (getSesionDato("aCredito")=='true')?'CHECKED':'';

	$ImporteFleteMoneda  = ($tipomoneda == 2)? $ImporteFlete / $tipocambio : $ImporteFlete;
	$ImportePago         = $TotalNeto + $ImporteFleteMoneda + $ImportePercepcion;
	$ImportePagoMonedaBase  = ($tipomoneda == 2)? $ImportePago * $tipocambio:$ImportePago;


	$ot->fijar("vTDoc",$tipodoc);
	$ot->fijar("vTxFecha",$tpfecha);
	$ot->fijar("vCheckIGV",$checkigv);
	$ot->fijar("vCheckPercepcion",$checkipc);
	$ot->fijar("vxPercepcion",$xipc);
	$ot->fijar("vMoneda1",$Moneda[1]['T']);
	$ot->fijar("vMoneda2",$Moneda[2]['T']);
	$ot->fijar("vSimboloMoneda1",$Moneda[1]['S']);
	$ot->fijar("vSimboloMoneda2",$Moneda[2]['S']);
	$ot->fijar("vCheckCredt",$checkcredt);
	$ot->fijar("vCheckO",$checkO);
	$ot->fijar("vCheckF",$checkF);
	$ot->fijar("vCheckR",$checkR);
	$ot->fijar("vCheckG",$checkG);
	$ot->fijar("vCheckSD",$checkSD);
	$ot->fijar("vTipoDoc",$titulo);
	$ot->fijar("vIdProvHab",$idprov);
	$ot->fijar("vIdSubsiHab",$idsubsid);
	$ot->fijar("vProveedorHab",$nomprov);
	$ot->fijar("vSubsiHab",$nomsubsid);
	$ot->fijar("vSDoc",$sdoc);
	$ot->fijar("vNDoc",$ndoc);
	$ot->fijar("vFechaDoc",$fecdoc);
	$ot->fijar("vFechaPago",$fechapago);
	$ot->fijar("vTipoMoneda",$tipomoneda);
	$ot->fijar("vCheckedTS",$checkedTS);
	$ot->fijar("vCheckedTD",$checkedTD);
	$ot->fijar("vTipoCambio",$tipocambio);
	$ot->fijar("vAlbaranes",$albaranes);
	$ot->fijar("stAlbaranes",$stalbaranes);
	$ot->fijar("vFechaCambio",$fechacambio);
	$ot->fijar("vIGV",$igv);
	$ot->fijar("vInputPV",$pv);
	$ot->fijar("vVV",$tvv);
	$ot->fijar("vPV",$tpv);
	$ot->fijar("vCP",$tcp);
	$ot->fijar("vTDPV",$tdpv);
	$ot->fijar("vColHeadCart",$colheadcart);
	$totallst=count($carrito);
	$ot->fijar("vTotalLst",$totallst);		

	$ot->fijar("vTotalNeto", dosdecimales($TotalNeto));
	$ot->fijar("vTotalDescuento",dosdecimales($TotalDescuento));
	$ot->fijar("vTotalImpuesto",dosdecimales($TotalImpuesto));
	$ot->fijar("vTotalBruto",dosdecimales($TotalBruto));
	$ot->fijar("vBrutoNeto",dosdecimales($BrutoNeto));
	$ot->fijar("vImporteFlete",dosdecimales($ImporteFlete));
	$ot->fijar("vImportePercepcion",dosdecimales($ImportePercepcion));
	$ot->fijar("vImportePago",dosdecimales($ImportePago));
	$ot->fijar("vImportePagoMonedaBase",dosdecimales($ImportePagoMonedaBase));

    //Guia Remision
        $undpeso = $detadoc[24];
        $vCheckMedidaKlg  = ($undpeso == 'TN')? 'selected':'';
        $vCheckMedidaTN   = ($undpeso == 'Klg')? 'selected':'';
        $stGuiaRemision   = ($tipodoc=='F')? "block":"none";
        $ot->fijar("vGuiaRemisionConcepto",$detadoc[17],FORCE);
        $ot->fijar("vGuiaRemisionPartida",$detadoc[18],FORCE);
        $ot->fijar("vGuiaRemisionLlegada",$detadoc[19],FORCE);
        $ot->fijar("vGuiaRemisionMarca",$detadoc[20],FORCE);
        $ot->fijar("vGuiaRemisionPlaca",$detadoc[21],FORCE);
        $ot->fijar("vGuiaRemisionLicencia",$detadoc[22],FORCE);
        $ot->fijar("vGuiaRemisionPeso",$detadoc[23],FORCE);
        $ot->fijar("vCheckMedidaKlg",$vCheckMedidaKlg,FORCE);
        $ot->fijar("vCheckMedidaTN",$vCheckMedidaKlg,FORCE);
        $ot->fijar("vFechaTraslado",$detadoc[25],FORCE);
        $ot->fijar("vIdSubsiHab",$detadoc[9],FORCE);
        $ot->fijar("vSubsidiarioHab",$detadoc[10],FORCE);
        $ot->fijar("stGuiaRemision",$stGuiaRemision);
	$jsOut .= jsPaginador($indice,$ot->getPagina(),$num);
	
	$ot->fijar("CLIST",$jsOut );
	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );

	$ot->terminaSerie();


	echo $ot->Output();	
}

function ListadoModificableImpresionPorLote(){
	$oProducto = new producto; 
	global  $action;

	$carrito 	= getSesionDato("CarritoCompras");
	$costescarrito 	= getSesionDato("CarroCostesCompra");
	$etiquetaIdProducto = array();
	$serie = 0;
	
	echo "<center><form method='post' action='$action?modo=impresionMultipleEjecutar'>";
	echo "<table class='listado' width='50%'><tbody>";
	echo "<tr>".
	  "<td class='lh'></td>".
	  "<td class='lh' width='5%'><nobr>". _("Número de etiquetas")."</nobr></td>".
	  "<td class='lh' style='min-width: 20em'><nobr>". _("Producto")."</nobr></td>".
	  "<td class='lh'>PV</td>".
	  "</tr>";							
	foreach ($carrito as $key=>$value){

	  $IdProducto  = $key;
	  $nombre     = getDatosProductosExtra($key,'nombrecb');	
	  $precio      = getPrecioGenerico($IdProducto);
	  if ($precio>0){
	    $serie++;							
	    echo "<tr class='f'>".
		"<td width='16'><img src='img/gpos_productos.png'></td>".
		"<td  width='10%'><input type='text' name='Unidades_$serie' value='$value'>".
		"<input type='hidden' name='Serie_IdProducto_$serie' value='".$IdProducto."'></td>".
		"<td class='nombre' style='min-width: 20em'><nobr>$nombre</nobr></td>".
		"<td class='precio'><nobr>".FormatMoney($precio)."</nobr></td>".
		"<input type='hidden' name='Serie_Precio_$serie' value='".($precio*1)."'>".
		"</td>".					
		"</tr>";	
	  } 
	}		

	if ($serie>0){
	  echo "<tr class='f'><td></td><td></td><td colspan='4'>".
	    "<input  class='btn item' type='submit' value='"._("Imprimir múltiple")."'>".
	    "</td></tr>";
	} else {
	  echo "<tr class='f'><td></td><td></td>".
	    "<td colspan='4'>"._("No se encontraron productos listos para etiquetar.").
	    "</td></tr>";
	}
	echo "</tbody></table>";	
	echo "<input type='hidden' name='numSeries' value='$serie'>";
	echo "</form></center>";
}


function getPrecioGenerico($IdProducto){
	$sql = 
	  "SELECT PrecioVenta ".
	  "FROM   ges_almacenes ".
	  "WHERE  IdProducto = '$IdProducto' ".
	  "AND    PrecioVenta>0";
	$row = queryrow($sql);
	if(!$row)
		return 0;
	return $row["PrecioVenta"];
}

function RecepcionarImpresionPorLote(){
	$unidadesSerie 	= array();
	$preciosSerie 	= array();
	$idProductoSerie = array();
	
	$numSeries = CleanInt($_POST["numSeries"]);
	for($t=0;$t<=$numSeries;$t++)
	  {
	    if (isset($_POST["Unidades_$t"])){
	      $IdProducto		  = CleanInt($_POST["Serie_IdProducto_$t"]);	
	      $Unidades 		  = CleanInt($_POST["Unidades_$t"]);
	      $unidadesSerie[$IdProducto] = $Unidades;			
	      $preciosSerie[$IdProducto]  = CleanFloat($_POST["Serie_Precio_$t"]);
	    }	
	  }
	
	foreach ($unidadesSerie as $IdProducto=>$unidades)
	  {
	    $precio = $preciosSerie[$IdProducto] ;
	    if ($precio>0)
	      {
		//echo "$IdProducto pedido imprimir con $unidades y precio $precio<br>";
		for($t=0;$t<$unidades;$t++){
		  GenEtiqueta($IdProducto,$precio);
		}
	      }
	  }
	echo "<script>window.print()</script>";
}

function actualizarCarritoSeriePrincipal(){
    $idprodseriecart = getSesionDato("idprodseriecart");
    $seriescart      = getSesionDato("seriescart");
    $quitarproductos = (isset($_POST["quitarproductos"]))? $_POST["quitarproductos"]:false;
    $quitarproductos = explode(",",$quitarproductos);

    for($i=0;$i<count($quitarproductos);$i++){    
        if(in_array($quitarproductos[$i],$idprodseriecart)){
            unset($idprodseriecart[$quitarproductos[$i]]);
            unset($seriescart[$quitarproductos[$i]]);
        }
    }

    setSesionDato("idprodseriebuy",$idprodseriecart);
    setSesionDato("seriesbuy",$seriescart);
}
PageStart();

switch($modo){
	case "impresionMultipleEjecutar":		
		RecepcionarImpresionPorLote();
	
		break;
	case "imprimirtodas":
		ListadoModificableImpresionPorLote();
	
	
		break;
	case "noseleccion":	
	case "noselecion":		
	        echo gas("cabecera",_("Presupuestos"));
		//Reseteamos carrito y su paginador
		ResetearCarritoCompras();

		ListaFormaDeUnidades();
		break;
		
	case "pagmenos":
		ActualizarCantidades();
		$indice = getSesionDato("PaginadorSeleccionCompras2");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorSeleccionCompras2",$indice);
		ListaFormaDeUnidades();
		break;	
	case "pagmas":
		ActualizarCantidades();
		$indice = getSesionDato("PaginadorSeleccionCompras2");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorSeleccionCompras2",$indice);
		ListaFormaDeUnidades();
		break;		
	case "guardarcambios":
	        echo gas("cabecera",_("Presupuestos"));
		ActualizarCantidades();
		ActualizarDescuentosImportes();
		actualizarCarritoSeriePrincipal();
		ListaFormaDeUnidades();	
		break;	
	default:
	case "check":
	  echo gas("cabecera",_("Presupuestos"));
	  ListaFormaDeUnidades();
	break;	
}


PageEnd();


?>
