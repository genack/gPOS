<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

global $tamPagina;
$tamPagina = 100;

function AutoOpen(){

  $id = (isset($_SESSION["IdUltimoCambioAlmacen"]))? $_SESSION["IdUltimoCambioAlmacen"]:'';
  if (!$id) return;
  
  $idBase = getIdBase2IdAlmacen($id);	
  $_SESSION["IdUltimoCambioAlmacen"] = false;
  return "\n autoexpand[$idBase]=1;\n AutoFocusIdBase='$idBase';\n";	
}

function genOpcionesBusqueda(){
	global $action;	

	if (!Admite("Stocks"))	return false;

	$ot = getTemplate("FormBusquedaAlmacenProducto");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
	
	
	$local = $_SESSION["BusquedaLocal"];
//	if (!$local)
//		$combo = getSesionDato("ComboAlmacenes");
//	else
	$combo = genComboAlmacenes($local);
	$ot->fijar("vIdLocal",$local);

	//error(0,"leeyendo carrito");
	$hayCarrito = getSesionDato("hayCarritoTrans");
	
	if  ($hayCarrito){
		$ot->fijar("tOperaCarro",_("Trabajar con selección"));
		$ot->fijar("tBorraCarro",_("Deseleccionar"));
		$ot->fijar("hackCarro");
		$ot->confirmaSeccion("carro");
	} else {
		$ot->fijar("hackCarro","noactivo");	
		$ot->fijar("tOperaCarro");		
		$ot->fijar("tBorraCarro");
		$ot->eliminaSeccion("carro");	
	}	
	
	$ot->fijar("tTallaycolores",_("Tallas/Colores"));	
	
	$ot->fijar("Referencia" , _("Referencia"));
	$ot->fijar("bEnviar" , _("Buscar"));
	$ot->fijar("ACTION", $action);
	$ot->fijar("comboAlmacenes" , $combo);
	$ot->fijar("CB", _("CB"));
	
	if ($_SESSION["BusquedaLocal"])
		$ot->confirmaSeccion("haylocal");
	else
		$ot->eliminaSeccion("haylocal");
		
	return $ot->Output();												
}

function BusquedaBasica(){
  //	echo genOpcionesBusqueda();
}

function ListarAlmacen($referencia,$donde,$marcadotrans=false,$cb=false,$idbase=false,$soloLlenos=false,$obsoletos=false,$soloNS=false,$soloLote=false,$soloOferta=false,$reservados=false){	
  global $action,$tamPagina;

  $base = getSesionDato("BusquedaProdBase");
  $ot   = getTemplate("ListadoMonoProductoMultiAlmacen");
  
  if (!$ot){	
    error(__FILE__ . __LINE__ ,"Info: template no encontrado");
    return false; }
  
  //Extraemos datos
  $almacen    = getSesionDato("Articulos");
  $IdLocal    = ($donde)? $donde:"";

  
  if ($referencia){
    $id     = genReferencia2IdProducto($referencia);
    $idbase = getProdBaseFromId($id); 
  }		
  $IdProducto = (!$base)? getIdFromCodigoBarras($cb):"";	
   
  
  if (!$IdLocal and !$IdProducto) {
    echo gas("Aviso",_("Sin resultados"));
    
    if (!$IdProducto) {
      setSesionDato("BusquedaReferencia",false);
      if (!$base)
	setSesionDato("BusquedaCB",false);  
      //si no encontro nada, no se busca en esa ref 
    }
    
    return false;	
  }

  $indice  = getSesionDato("PaginadorAlmacen");
  $idalias = "";
  $nombre  = "";

  if (isset($_SESSION["BusquedaNombre"]) and $_SESSION["BusquedaNombre"])
    $nombre = $_SESSION["BusquedaNombre"];

  if($nombre)  
    $idalias = getLikeProductoAlias2Id($nombre, $IdIdioma=false);

  $res = $almacen->ListadoModular($IdLocal,$IdProducto,$indice,$tamPagina,$idbase,$nombre,$soloLlenos,$obsoletos,$soloNS,$soloLote,$soloOferta,$idalias,$reservados);

  $haytrans = is_array($marcadotrans) and count($marcadotrans);

  $num = 0;
  
  $jsOut = "";
  $jsLex = new jsLextable;
  
  $jsOut .= jsLabel("comprar",	_("Comprar"));
  $jsOut .= jsLabel("modificar",	_("Modificar"));
  $jsOut .= jsLabel("referencia",	_("Referencia"));
  $jsOut .= jsLabel("unid",		_("Unid"));
  $jsOut .= jsLabel("pv",			_("PV"));
  $jsOut .= jsLabel("seleccionar", _("Seleccionar"));
  $jsOut .= jsLabel("cuantasunidades",	_("¿Cuántas unidades?"),false);
  
  $oldId = -1;
  while($almacen->SiguienteArticulo() ){
    $num++;
    
    $transid      = $almacen->get("Id");
    $ref          = $almacen->get("Referencia");
    $cb           = $almacen->get("CodigoBarras");
    $nombre       = $almacen->get("Nombre");
    $nombre       = (getParametro("ProductosLatin1"))? iso2utf($nombre):$nombre;	
    $unidades     = $almacen->get("Unidades");
    $contenedor   = $almacen->get("Contenedor");
    $ucontenedor  = $almacen->get("UnidadesPorContenedor");
    $precio       = $almacen->get("CostoUnitario");
    $ident        = $almacen->get("Identificacion");
    $id           = $almacen->get("IdProducto");
    $iconos       = $almacen->Iconos();
    $talla        = getIdTalla2Texto($almacen->get("IdTalla"));
    $lextalla     = $jsLex->add($talla);
    $color        = getIdColor2Texto($almacen->get("IdColor"));
    $lexcolor     = $jsLex->add($color);
    $desc         = $almacen->get("Descripcion");
    $nombreLocal  = getNombreLocalId($almacen->get("IdLocal"));
    $lexlocal     = $jsLex->add($nombreLocal);
    $ManejaSerie  = $almacen->get("Serie");
    $statusns     = $almacen->get("StatusNS");
    $ventamenudeo = $almacen->get("VentaMenudeo");
    $UnidadMedida = $almacen->get("UnidadMedida");
    $fam          = getIdFamilia2Texto( $almacen->get("IdFamilia"));
    $sub          = getIdSubFamilia2Texto($almacen->get("IdFamilia"), $almacen->get("IdSubFamilia"));
    $lexfam       = $jsLex->add($fam);
    $lexsub       = $jsLex->add($sub);
    $sel          = ($haytrans and in_array($transid, $marcadotrans) )? 1:0;
    $idBase       = $almacen->get("IdProdBase");
    $marca        = getIdMarca2Texto($almacen->get("IdMarca"));
    $lab          = getNombreLaboratorio(getIdLaboratorioFromIdProducto($id));
    if ($idBase != $oldId) {
      $numlex = $jsLex->add($ident);
      $nombre = addslashes($nombre);
      $ref    = addslashes($ref);
      $jsOut .= "cAH($idBase,'$nombre','$ref','$desc',$numlex,$lexfam,$lexsub,$ManejaSerie,'$UnidadMedida','$contenedor',$ucontenedor,'$marca','$ventamenudeo','$lab');\n";
    }
    $jsOut .= "cA($id,'$iconos','$cb',$unidades,'$precio',$sel,$transid,$lextalla,$lexcolor,$lexlocal,$ManejaSerie,'$UnidadMedida','$contenedor',$ucontenedor,'$ventamenudeo','$statusns');\n";
    $oldId = $idBase;							
  }	
  
  $jsOut     = $jsLex->jsDump() . $jsOut;
  $jsOut    .= AutoOpen();	

  $paginador = $ot->jsPaginador($indice,$tamPagina,$num);
  $jsOut    .= $paginador;	
  $jsOut    .= "cListAlmacen();";	
  $jsOut    .= $paginador;
  
  echo "<center>";
  echo jsBody($jsOut);
  echo "</center>";					
  
}

function FormularioCompras($id){
	global $action;

	//Creamos template
	$ot = getTemplate("FormCompras");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	
	$producto = new producto;	
	
	if ($producto->Load($id)){		
		$ot->fijar("NombreProducto",$producto->getNombre());			
		$ot->fijar("Referencia",$producto->getReferencia());
		$ot->fijar("tTitulo",_("Petición de compra"));
		$ot->fijar("tCantidad",_("Cantidad:"));
		$ot->fijar("tEnviar",_("Enviar:"));
		$ot->fijar("tProveedorHabitual",_("Proveedor habitual:"));
	
		$ot->fijar("IdProducto",$producto->getId());			
		$ot->fijar("action",$action);
	
		echo $ot->Output();
	}	else {
		echo gas("Aviso",_("No se puede realizar la operación"));	
	}
}


function getOperacionesConSeleccion(){
	return;

}


function OperacionesConSeleccion(){
	return ;
}

function ListarSeleccion($marcadotrans){

 	global $action;
        echo '<center> 
              <table class="listado" border="0">
                <tbody>
                 <tr class="formaCabeza">
                   <td height="16" colspan="4">
                    <div class="formaTituloCarrito"> Carrito Almacén </div>
                   </td>
                 </tr> 
                </tbody>
              </table> 
              </center>';
	
	//Creamos template
	$ot       = getTemplate("ListadoMultiAlmacenSeleccion");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }	
		
	$articulo  = new articulo;		
	$tamPagina = $ot->getPagina();
	$indice    = getSesionDato("PaginadorSeleccionAlmacen");
	$igv       = getSesionDato("IGV");
	$vIGV      = ($igv)? '(IGV.'.$igv.'%)':'';
	$num       = 0;
	$salta     = 0;
	$Trans     = getSesionDato("CarritoMover");
	$TransNS   = getSesionDato("CarritoMoverSeries");
	$esCarrito = getSesionDato("ModoCarritoAlmacen");
	$tbPrecio  = ( $esCarrito == 't' )? 'block':'none';

	$ot->resetSeries(array("Unidades","PrecioVenta","IdProducto",
			       "Nombre","Referencia","NumTraspasar",
			       "NombreComercial","Comprar","marcatrans","iconos"));	

	foreach ($marcadotrans as $idarticulo ){

	  $salta ++;

	  if ($num <= $tamPagina and $salta>=$indice)
	    {		
	      $num++;			
	      $oProducto     = new producto();

	      $articulo->Load($idarticulo);
	      $oProducto->Load($articulo->get("IdProducto"));

	      $unid       = $oProducto->get("UnidadMedida");
	      $idproducto = $articulo->get("IdProducto");
	      $producto   = getDatosProductosExtra($idproducto,"nombre");
	      $esSerie    = ( $TransNS[$idarticulo] )? true:false;
	      $vSeries    = ( $esSerie )? 'inline':'none';
	      $mSeleccion = $Trans[$idarticulo];
	      $aSeleccion = explode("~", $mSeleccion);
	      $Seleccion  = 0;
	      $precio     = 0;
	      $npedido    = 0;
	      $xPrecio    = (isset($Trans['Precio'.$idarticulo]))?$Trans['Precio'.$idarticulo]:false;
	      $esPrecio   = ( $xPrecio )? true:false;
	      $LoteVence  = '';

	      foreach ( $aSeleccion as $Pedido )
		{
		  $aPedido    = explode(":", $Pedido);
		  $Seleccion += $aPedido[1];
		  $precio    += $aPedido[2];
		  $LoteVence  = (isset($aPedido[3]))? $LoteVence.' \n *  '.$aPedido[1].' '.$unid.' -  '.$aPedido[3]:false;
		}

	      $precio         = round(($precio/$Seleccion),2);
	      $vCosto         = round(100*$precio/(100+$igv),2);
	      $vPrecio        = ( $esPrecio  )? $xPrecio:$precio;
	      $vLoteVence     = ( $LoteVence )? 'inline':'none';
	      $LoteVence      = ( $LoteVence )? 'alert("gPOS: Carrito Almacén '.
		                              '\n\n'.$producto.'\n\n    Unid    '.
		                              '  Lote /  Vencimiento  \n'.$LoteVence.'")':'';

	      $ot->fijarSerie("Referencia",$articulo->get("CodigoBarras"));
	      $ot->fijarSerie("Nombre",$producto);
	      $ot->fijarSerie("Unidades",$articulo->get("Unidades").' '.$unid);
	      $ot->fijarSerie("Costo",$vCosto);
	      $ot->fijarSerie("PrecioVenta",$vPrecio);
	      $ot->fijarSerie("NombreComercial",$articulo->get("NombreComercial"));
	      $ot->fijarSerie("idproducto",$idproducto);
	      $ot->fijarSerie("Comprar","");		
	      $ot->fijarSerie("NumTraspasar",$Seleccion.' '.$unid);
	      $ot->fijarSerie("Series",$Seleccion.','.$idarticulo.','.$idproducto);
	      $ot->fijarSerie("vSeries",$vSeries);
	      $ot->fijarSerie("vLoteVence",$vLoteVence);
	      $ot->fijarSerie("LoteVence",$LoteVence);
	      $ot->fijarSerie("vProducto",$idarticulo.',this,'.$precio);
	      $ot->fijarSerie("vIGV",$vIGV);
	      //$ot->fijarSerie("transid",$idarticulo);
	      $ot->fijarSerie("iconos",$articulo->Iconos());

	      $Trans['Costo'.$idarticulo]  = $vCosto;
	      $Trans['Precio'.$idarticulo] = $vPrecio;
	      setSesionDato("CarritoMover",$Trans);	
	    }
	}	
	$ot->fijar("vPrecio",$tbPrecio);	
	$ot->paginador($indice,false,$num);	
	$ot->fijar("action",$action );
	
	$ot->terminaSerie();
	echo $ot->Output();
	//echo "hi! '$num'";		
}


function FormTrasladoSeleccion(){
	global $action;
	
	$ot = getTemplate("FormTraslado");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }

	$combo = getSesionDato("ComboAlmacenes");

	$ot->fijar("tDestino" , _("Destino:"));
	$ot->fijar("bEnviar" , _("Enviar"));
	$ot->fijar("ACTION", $action);
	$ot->fijar("comboAlmacenes" , $combo);


	echo $ot->Output();
}

function FormularioEditarArticulo($id){
    global $action;

    $ot = getTemplate("FormEditarArticulo");

    if (!$ot){	
        error(__FILE__ . __LINE__ ,"Error: template busqueda no encontrado");
        return false; }

        $articulo = new articulo();
        $oProducto = new producto();
        if(!$articulo->Load($id)){
            error(__FILE__ . __LINE__ ,"Error: no puedo modificar ese producto");
            return false; }
            $oProducto->Load($articulo->get("IdProducto"));
            $igv            = getSesionDato("IGV");
            $local          = $_SESSION["LocalMostrado"];
            $idProducto     = $articulo->get("Id");
            $CostoUnitario  = $articulo->get("CostoUnitario");
            $PVD            = $articulo->get("PrecioVenta");
            $PVDD           = $articulo->get("PVDDescontado");
            $IGVD           = round(($PVD/(1+$igv/100))*($igv/100)*100)/100;
            $MUD            = round((($PVD - $IGVD) - $CostoUnitario)*100)/100;
            $PVC            = $articulo->get("PrecioVentaCorporativo");
            $PVCD           = $articulo->get("PVCDescontado");
            $IGVC           = round(($PVC/(1+$igv/100))*($igv/100)*100)/100;
            $MUC            = round((($PVC - $IGVC) - $CostoUnitario)*100)/100;
            $disponible     = ($articulo->is("Disponible"))? "checked":"";
            $dispOnline     = ($articulo->is("DisponibleOnline"))? "checked":"";
            $obsoleto       = ($oProducto->get("Obsoleto"))? "checked":"";
            $esLote         = $oProducto->get("Lote");
	    $esVence        = $oProducto->get("FechaVencimiento");
	    $esSerie        = $oProducto->get("Serie");
            $oferta         = ($articulo->is("Oferta"))? "checked":"";
            $stockilimitado = ($articulo->is("StockIlimitado"))? "checked":"";

	    $esIlimitado    = ( $esSerie )? 'none':'table-row';
	    $esIlimitado    = ( $esLote  )? 'none':$esIlimitado;
	    $esIlimitado    = ( $esVence )? 'none':$esIlimitado;

	    $existencias    = $articulo->get("Unidades");
	    $stockunidades  = $existencias;
	    $producto       = getDatosProductosExtra($articulo->get("IdProducto"),'nombretodos');
	    $nombre         = $producto["Nombre"]; 
	    $esMenudeo      = $producto["Menudeo"];
	    $unidad         = $producto["Und"];
	    $unidxemp       = $producto["UndxEmp"];
	    $empaque        = $producto["Empaque"];
	    $resmenudeo     = ($esMenudeo)? "  ( ".$stockunidades." ".$unidad." a ".
	                                    $unidxemp.$unidad."x".$empaque." )":"";
	    $disponibleunid = $articulo->get("DisponibleUnidades");

	    $reservadaunid  = ($disponibleunid > 0 )? $existencias - $disponibleunid:0;
	    $resto          = ($esMenudeo)? $existencias%$unidxemp:0;
	    $empaques       = ($esMenudeo)? ($existencias-$resto)/$unidxemp:0;
	    $existencias    = ($esMenudeo)? $empaques." ".$empaque." + ".$resto:$existencias;
	    $disponibleunid = ($disponibleunid>0)? $disponibleunid:$stockunidades;
	    $txtMoDet       = getModeloDetalle2txt();
	    $txtModelo      = $txtMoDet[1];
	    $txtDetalle     = $txtMoDet[2];
	    $txtalias       = $txtMoDet[3];
	    $txtref         = $txtMoDet[4];


            $ot->fijar("tTituloAux",_("Otras tiendas"));
            $ot->fijar("tIgualar",_("Todas las tiendas el mismo precio"));
            $ot->fijar("tMotivo",_("Motivo mod. existencias"));
            $ot->fijar("tTitulo",_("Modificar existencias"));
            $ot->fijar("vRefProvHab",$articulo->get("RefProvHab"));
            $ot->fijar("tRefProvHab",$txtref);
            $ot->fijar("vPresentacion", $oProducto->getTextColor());
            $ot->fijar("tPresentacion", $txtModelo);
            $ot->fijar("vProductoAlias0", getIdProductoAlias2Texto($articulo->get("IdProductoAlias0")));
            $ot->fijar("tProductoAlias0", $txtalias);
            $ot->fijar("vProductoAlias1", getIdProductoAlias2Texto($articulo->get("IdProductoAlias1")));
            $ot->fijar("tProductoAlias1", $txtalias);
            $ot->fijar("vSubPresentacion", $oProducto->getTextTalla());
            $ot->fijar("tSubPresentacion", $txtDetalle);
            $ot->fijar("vReferencia",$articulo->get("Referencia"));
            $ot->fijar("tReferencia",_("Referencia"));
            $ot->fijar("vDescripcion",$nombre);
            $ot->fijar("tDescripcion",_("Nombre"));
            $ot->fijar("vUnidades",$existencias);
            $ot->fijar("tUnidades",_("Stock"));
            $ot->fijar("mUnidad",$resmenudeo);
            $ot->fijar("vIGV",$igv);
            $ot->fijar("tIGV","IGV");
            $ot->fijar("vCostoUnitario",round($articulo->get("CostoUnitario")*100)/100 );
            $ot->fijar("tCostoUnitario",_("CU"));
            $ot->fijar("vCostoUnitarioC",round($articulo->get("CostoUnitario")*100)/100 );
            $ot->fijar("tCostoUnitarioC",_("CU"));
            $ot->fijar("vMUD",$MUD);
            $ot->fijar("tMUD",_("MU"));
            $ot->fijar("vIGVD",$IGVD);
            $ot->fijar("tIGVD",_("IGV"));
            $ot->fijar("vPVD",$PVD);
            $ot->fijar("tPVD",_("PV"));
            $ot->fijar("vPVDD",$PVDD);
            $ot->fijar("tPVDD",_("PVDD"));
            $ot->fijar("vMUC",$MUC);
            $ot->fijar("tMUC",_("MU"));
            $ot->fijar("vIGVC",$IGVC);
            $ot->fijar("tIGVC",_("IGV"));
            $ot->fijar("vPVC",$PVC);
            $ot->fijar("tPVC",_("PV"));
            $ot->fijar("vPVCD",$PVCD);
            $ot->fijar("tPVCD",_("PVCD"));
            $ot->fijar("tTipoImpuesto",_("Impuesto"));
            $ot->fijar("vTipoImpuesto",$articulo->get("TipoImpuesto"));
            $ot->fijar("vImpuesto",$articulo->get("Impuesto"));
            $ot->fijar("vStockMin",$articulo->get("StockMin"));
            $ot->fijar("vStockUnidades",$stockunidades);
            $ot->fijar("tOfertaUnidades",_("Unidades en Oferta "));
            $ot->fijar("vOfertaUnidades",$articulo->get("OfertaUnidades"));
            $ot->fijar("tPrecioVentaOferta",_("Precio de Oferta"));
            $ot->fijar("vPrecioVentaOferta",$articulo->get("PrecioVentaOferta"));
            $ot->fijar("tDisponibleUnidades",_("Unidades Disponibles"));
            $ot->fijar("vDisponibleUnidades",$disponibleunid);
            $ot->fijar("tUnidad",$unidad);
            $ot->fijar("tUnidadesReservadas",_("Unidades Reservadas"));
            $ot->fijar("vUnidadesReservadas",$reservadaunid);
            $ot->fijar("cDisponible",$disponible);
            $ot->fijar("cDisponibleOnline",$dispOnline);
	    $ot->fijar("cOferta",$oferta);
	    $ot->fijar("cStockIlimitado",$stockilimitado);
	    $ot->fijar("esStockIlimitado",$esIlimitado);
            $ot->fijar("tDisponible",_("Disponible"));
            $ot->fijar("tDisponibleOnline",_("Disponible Online"));
            $ot->fijar("tObsoleto",_("Obsoleto"));
            $ot->fijar("cObsoleto",$obsoleto);
            $ot->fijar("tOferta",_("En oferta"));						
            $ot->fijar("tStockIlimitado",_("Stock ilimitado"));				
            $ot->fijar("tStockMin",_("Stock minimo"));
            $ot->fijar("action",$action);
            $ot->fijar("vId",$articulo->get("Id"));
            echo $ot->Output();
}

//Acciones mudas
switch($modo){
	case "trans": //Agadir un producto al carrito de la 
		$id     = CleanID($_GET["id"]);
		$u      = CleanCadena($_GET["u"]);
		$series = CleanCadena($_GET["series"]);

		AgnadirCarritoTraspaso($id,$u);
		AgnadirCarritoTraspasoSeries($id,$series);

		exit();
		break;
	case "notrans": //desAgadir un producto al carrito de la 
		$id     = CleanID($_GET["id"]);	

		QuitarDeCarritoTraspaso($id);
		QuitarDeCarritoTraspasoSeries($id);
		exit();
		break;		
}		
		
PageStart();

echo gas("cabecera",_("Stock"));

switch($modo) {
	case "bases":	
		setSesionDato("ListaBases",true);	
		break;
		
	case "nobases":
		setSesionDato("ListaBases",false);	
		break;		
	
	case "pagmas":
		$index = getSesionDato("PaginadorAlmacen");		
		$index = $index + $tamPagina;		
		setSesionDato("PaginadorAlmacen",$index);		
		break;
		
	case "pagmenos":
		$index = getSesionDato("PaginadorAlmacen");		
		$index = $index - $tamPagina;
		if ($index<0)
			$index = 0;		
		setSesionDato("PaginadorAlmacen",$index);
		break;
	case "selpagmas":
		$index = getSesionDato("PaginadorSeleccionAlmacen");		
		$index = $index + $tamPagina;		
		setSesionDato("PaginadorSeleccionAlmacen",$index);
		break;		
	case "selpagmenos":	
		$index = getSesionDato("PaginadorSeleccionAlmacen");
		
		$index = $index - $tamPagina;
		if ($index<0)
			$index = 0;				
		setSesionDato("PaginadorSeleccionAlmacen",$index);
		break;		

	default:
		break;
	
}

switch($modo){

	case "modificar":
	
		if (!Admite("Stocks"))	return false;
		
		$id              = CleanID($_POST["Id"]);
		$disponible      = (isset($_POST["Disponible"]))?($_POST["Disponible"]=="on"):false;
		$dispOnline      = (isset($_POST["DisponibleOnline"]))? ($_POST["DisponibleOnline"]=="on"):false;
		$oferta          = (isset($_POST["Oferta"]))? ($_POST["Oferta"]=="on"):false;
		$stockilimitado  = (isset($_POST["StockIlimitado"]))? ($_POST["StockIlimitado"]=="on"):false;
		$Obsoleto        = (isset($_POST["Obsoleto"]))? ($_POST["Obsoleto"]=="on"):false;
		$Stock           = CleanInt($_POST["AntiguoUnidades"]);
		$UnidDisponible	 = CleanInt($_POST["DisponibleUnidades"]);
		$UnidReservadas	 = CleanInt($_POST["UnidadesReservadas"]);
		$UnidOferta	 = CleanDinero($_POST["OfertaUnidades"]);
		$PrecioOferta	 = CleanDinero($_POST["PrecioVentaOferta"]);
		$stockmin	 = CleanInt($_POST["StockMin"]);
		$Producto        = CleanCadena($_POST["Producto"]);
		$local           = CleanID($_SESSION["LocalMostrado"]);	
		$local           = (!$local)? getSesionDato("IdTienda"):$local;

		ModificarArticulo($id,$disponible,$dispOnline,$oferta,$stockilimitado,$stockmin,
				  $Producto,$Obsoleto,$UnidDisponible,$UnidReservadas,
				  $UnidOferta,$PrecioOferta,$Stock);

		$_SESSION["IdUltimoCambioAlmacen"] = $id;

		$ref             = $_SESSION["BusquedaReferencia"];
		$referencia      = $_SESSION["BusquedaReferencia"];
		$donde           = $_SESSION["BusquedaLocal"];
		$cb              = $_SESSION["BusquedaCB"];
		$nombre          = $_SESSION["BusquedaNombre"];
		$soloLlenos      = $_SESSION["BusquedaSoloLlenos"];
		$soloNS          = $_SESSION["BusquedaSoloNS"];
		$soloLote        = $_SESSION["BusquedaSoloLote"];
		$soloOferta      = $_SESSION["BusquedaSoloOferta"];
		$obsoletos       = $_SESSION["BusquedaObsoletos"];
		$reservados      = $_SESSION["BusquedaReservados"];
		$soloLlenos      = $_SESSION["BusquedaSoloConStock"];
		$marcadotrans    = getSesionDato("CarritoTrans");  
		ListarAlmacen($ref,$local,$marcadotrans,$cb,false,$soloLlenos,
			      $obsoletos,$soloNS,$soloLote,$soloOferta,$reservados);
		break;	

	case "editar":

	        $id = CleanID($_GET["id"]);		
		FormularioEditarArticulo($id);
		break;

	case "nosonoferta":
		if (!Admite("Stocks"))	return false;

 		$IdAlmacen    = $_SESSION["LocalMostrado"];
		$marcadotrans = getSesionDato("CarritoTrans");
		$cantidad     = getSesionDato("CarritoMover");
		$num          = MarcarGenericoUnidades($marcadotrans,"Oferta=0, OfertaUnidades = ",
						       $IdAlmacen,$cantidad,false);

		$aviso = (count($marcadotrans))? " $num Producto(s) marcados sin oferta.":"Carrito vacio ";
		echo gas("aviso",_($aviso));

		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;	
	
	case "nosondisponibles":

		if (!Admite("Stocks"))	return false;

		$IdAlmacen    = $_SESSION["LocalMostrado"];
		$marcadotrans = getSesionDato("CarritoTrans");
		$cantidad     = getSesionDato("CarritoMover");
		$num          = MarcarGenericoUnidades($marcadotrans,
						       "Disponible=1,DisponibleUnidades = Unidades - ",
						       $IdAlmacen,$cantidad,true);

		$aviso = (count($marcadotrans))? " $num Producto(s) marcados como no disponibles":"Carrito vacio ";
		echo gas("aviso",_($aviso));

		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;	
	
	case "sondisponibles":

		$marcadotrans = getSesionDato("CarritoTrans");
		$cantidad     = getSesionDato("CarritoMover");
		$IdAlmacen    = $_SESSION["LocalMostrado"];
		$num          = MarcarGenericoUnidades($marcadotrans,"Disponible=1,DisponibleUnidades = ",
						       $IdAlmacen,$cantidad,true);

		$aviso = (count($marcadotrans))? " $num Producto(s) marcados como disponibles":"Carrito vacio ";
		echo gas("aviso",_($aviso));

		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;	

	case "versonoferta":

	        setSesionDato("ModoCarritoAlmacen","t");
		$marcadotrans = getSesionDato("CarritoTrans");
		$cantidad     = getSesionDato("CarritoMover");

		$aviso = (count($marcadotrans))? " Ingrese el Precio de Oferta ":"Carrito vacio";

		echo gas("aviso",_($aviso));
		ListarSeleccion($marcadotrans);
		break;	
	case "sonoferta":

		if (!Admite("Stocks"))	return false;

		$marcadotrans = getSesionDato("CarritoTrans");
		$cantidad     = getSesionDato("CarritoMover");
		$IdAlmacen    = $_SESSION["LocalMostrado"];

		$num          = MarcarGenericoUnidades($marcadotrans,"Oferta=1, OfertaUnidades = ",
						       $IdAlmacen,$cantidad,true);

		$aviso = (count($marcadotrans))? " $num Producto(s) marcados en oferta.":"Carrito vacio";
		echo gas("aviso",_($aviso));
		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		setSesionDato("ModoCarritoAlmacen",'g');
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;	
		
	case "esobsoleto":

		$marcadotrans = getSesionDato("CarritoTrans");
		$num          = MarcarGenericoProducto($marcadotrans,"Obsoleto=1");
	
		$aviso = " $num productos marcados como obsoletos,<br/>".
		         " (*) Esta acción afecta a todos los locales.";

		$aviso = (count($marcadotrans))? $aviso:"Carrito vacio";

		echo gas("aviso",_($aviso));

		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;	
		
	case "noobsoleto":
		$marcadotrans = getSesionDato("CarritoTrans");
		$num          = MarcarGenericoProducto($marcadotrans,"Obsoleto=0");

		$aviso = " $num productos marcados como no obsoletos,<br/>".
  		         " (*) Esta acción afecta a todos los locales.";

		$aviso = (count($marcadotrans))? $aviso:"Carrito vacio";

		echo gas("aviso",_($aviso));

		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$marcadotrans = getSesionDato("CarritoTrans");
		ListarSeleccion($marcadotrans);
		break;			
				
	case "albaran":

	        $marcadotrans = getSesionDato("CarritoTrans");

		if(!count($marcadotrans))
		  {
		    echo gas("aviso",_("Carrito vacio"));

		    OperacionesConSeleccion();		 		
		    ListarSeleccion($marcadotrans);	
		    break;
		  }
		//$marcadotrans = getSesionDato("CarritoTrans");
	        $Origen       = CleanID($_SESSION["LocalMostrado"]);
		$Origen       = (!$Origen)? getSesionDato("IdTienda"):$Origen;
		$Destino      = CleanID(GET("IdLocalDestino"));
		$Destino      = (!$Destino)? CleanID($_POST["IdLocalDestino"]):$Destino;
		$Motivo       = CleanID($_GET["motivo"]);
		$tMotivo      = CleanCadena($_GET["tmotivo"]);
		$nomdes       = CleanCadena($_GET["tdestino"]);

		switch($Motivo){		

		case "4"://Devolucion
		  break;

		case "6"://inmovilizacion
		  $Destino = $Origen;
		  break;

		case "2"://Consignacion
		case "5"://Traslado

		  if($Origen == $Destino)
		    echo gas("aviso",_("<center> ¡Acción restringida! ".
				       " Elije otro local</center>")); 
		  else
		    break;

		default:
		  //FormTrasladoSeleccion();
 		  OperacionesConSeleccion();
		  ListarSeleccion($marcadotrans);
		  return;
		}

		//Valida kardex...
		if( ValidarTrasladoDetalle($Origen) ) return;

		//Traslado....
		OperacionTrasladoResumida($Destino,$Origen,$Motivo);

		echo _("<center>
                          <div class='forma' style='width: 200px'>
                            
                            <ul class='auxmenu'>
                             <li class='lh' style='font-weight: bold;padding:.5em;font-size:13px'>
                                 Se ha realizado su alta</li>
                             <li class='lh' style='font-size:14px;'>Albaran - ".$tMotivo."</li>
                             <li class='lh' style='font-size:13px;'>Local ".$nomdes."</li>
                             <li class='auxitem'>
                                <input class='btn item' type='button' value='Ver Albaran' 
                                       onclick='parent.lanzarVentasGeneral()'>
                             </li>
                             <li class='auxitem'>
                             <hr width='100%'>
                             </li>
                             <li class='auxitem'>
                             <input class='btn item' type='button' value='Volver a Stock' 
                                    onclick='parent.almacen_buscar(1)'>
                             </li>
                            </ul>
                            </div>
                           </center>");


		$_SESSION["CarritoTrans"]       = array();		
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();
		setSesionDato("PaginadorSeleccionAlmacen",false);
		break;	

	case "transsel":
		//Elige destino	
		FormTrasladoSeleccion();		
		break;		
	case "selpagmas": //navegando en la seleccion
	case "selpagmenos":		
		$marcadotrans = getSesionDato("CarritoTrans");
	        //OperacionesConSeleccion();		 		
		ListarSeleccion($marcadotrans);	
		break;
	case "seleccion": //operar seleccion

	        $marcadotrans = getSesionDato("CarritoTrans");

		if (!count($marcadotrans))
		  echo gas("aviso",_("Carrito vacio"));

		if (isset($_POST["borraseleccion"]) and $_POST["borraseleccion"]){
			$_SESSION["CarritoTrans"]=array();
			BusquedaBasica();							
			$ref          = $_SESSION["BusquedaReferencia"];
			$local        = $_SESSION["BusquedaLocal"];
			$cb           = $_SESSION["BusquedaCB"];
			ListarAlmacen($ref,$local,$marcadotrans,$cb);		
		} else {
			//BusquedaBasica();
			//OperacionesConSeleccion();		 		
			ListarSeleccion($marcadotrans);
		}							
		break;		
	case "obsoleto_trans": //Agadir un producto al carrito de la 
		$id = CleanID($_GET["id"]);
		$u  = CleanInt($_GET["u"]);
		AgnadirCarritoTraspaso($id,$u);
		BusquedaBasica();
		
		echo gas("nota",_("Producto seleccionado"));		
		$marcadotrans = getSesionDato("CarritoTrans");
		$ref          = $_SESSION["BusquedaReferencia"];
		$local        = $_SESSION["BusquedaLocal"];
		$cb           = $_SESSION["BusquedaCB"];

		if ($local or $ref or $cb)
			ListarAlmacen($ref,$local,$marcadotrans,$cb);		
		break;	

	case "pagmas": //navegando en el listado almacen
	case "pagmenos":
	        //BusquedaBasica();
		$ref = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb = $_SESSION["BusquedaCB"];
		$soloLlenos   = $_SESSION["BusquedaSoloConStock"];
		$nombre       = $_SESSION["BusquedaNombre"];
		$soloNS       = $_SESSION["BusquedaSoloNS"];
		$soloLote     = $_SESSION["BusquedaSoloLote"];
		$soloOferta   = $_SESSION["BusquedaSoloOferta"];
		$obsoletos    = $_SESSION["BusquedaObsoletos"];
		$reservados   = $_SESSION["BusquedaReservados"];
		$soloLlenos   = $_SESSION["BusquedaSoloConStock"];
		$marcadotrans = getSesionDato("CarritoTrans");  

		if (!$local)
			$local = getSesionDato("IdTienda");		
							
		if (($local or $ref) or $cb)
		  ListarAlmacen($ref,$local,$marcadotrans,$cb,false,$soloLlenos,
				$obsoletos,$soloNS,$soloLote,$soloOferta,$reservados);

					
		break;	
	case "hacercompra":
		$IdProducto = $_POST["IdProducto"];
		$Cantidad = $_POST["Cantidad"];
		$esHabitual = $_POST["habitual"] == "on";
		
		//RealizarPedido();
		//echo gas("TODO","Cuando trabajemos los albaranes, se continuara por aqui");
		break;	
	case "comprar":
		$IdProducto = $_GET["id"];
		FormularioCompras($IdProducto);
		break;		
	case "buscarproductos":
		setSesionDato("PaginadorAlmacen",0);
	
		$referencia = CleanReferencia(GET("Referencia"));		
		$donde      = CleanID(GET("IdLocal"));
		$cb         = CleanCB(GET("CodigoBarras"));		
		$completas  = (GET("verCompletas")=="on");		
		$nombre     = CleanText(GET("Nombre"));
		$soloLlenos = CleanID(GET("soloConStock"));
		$soloNS     = CleanID(GET("soloConNS"));
		$soloLote   = CleanID(GET("soloConLote"));
		$soloOferta = CleanID(GET("soloConOferta"));
		$obsoletos  = CleanID(GET("mostrarObsoletos"));
		$reservados = CleanID(GET("mostrarReservados"));

		if (intval($donde)<1)
			$donde = false;
			
		if (strlen($referencia)<1)
			$referencia = false;
		
		if (strlen($cb)<1)
			$cb = false;	
			
		if ($referencia) { //buscara para este código de barras.
		
			if ($cb)			
				$id = getIdFromCodigoBarras($cb);
			else {
				$id = getIdFromReferencia($referencia);
			}		
								
			$IdBase = getProdBaseFromId($id); 
			$_SESSION["BusquedaProdBase"] = $IdBase;
									
		} 		else {
			$_SESSION["BusquedaProdBase"] = false;
		}				
		
		$_SESSION["BusquedaReferencia"] = $referencia;
		$_SESSION["BusquedaLocal"] = $donde;
		$_SESSION["BusquedaCB"] = $cb;
		$_SESSION["BusquedaNombre"] = $nombre;
		
		$_SESSION["BusquedaSoloLlenos"] = $soloLlenos;
		$_SESSION["BusquedaSoloNS"] = $soloNS;
		$_SESSION["BusquedaSoloLote"] = $soloLote;
		$_SESSION["BusquedaSoloOferta"] = $soloOferta;
		$_SESSION["BusquedaObsoletos"] = $obsoletos;
		$_SESSION["BusquedaReservados"] = $reservados;
		$_SESSION["BusquedaSoloConStock"] = $soloLlenos;
		
		$marcadotrans = getSesionDato("CarritoTrans");  


		//Si no se dice dodne buscar, se busca por defecto en el local actual		
		if (!$donde)
			$donde = getSesionDato("IdTienda");


		BusquedaBasica();	

		$_SESSION["LocalMostrado"] = $donde;
		
		if (($referencia or $donde) or ($cb or $nombre))
		  ListarAlmacen($referencia,$donde,$marcadotrans,$cb,false,
				$soloLlenos,$obsoletos,$soloNS,$soloLote,$soloOferta,$reservados);
		else
			echo gas("Aviso",_("No especifico opciones de búsqueda"));		
		break;
	case "borrarseleccion":
		setSesionDato("PaginadorSeleccionAlmacen",false);
		$_SESSION["CarritoTrans"]       = array();
		$_SESSION["CarritoMover"]       = array();
		$_SESSION["CarritoTransSeries"] = array();		
		$_SESSION["CarritoMoverSeries"] = array();

		$ref   = $_SESSION["BusquedaReferencia"];
		$local = $_SESSION["BusquedaLocal"];
		$cb    = $_SESSION["BusquedaCB"];
		
		if ($local or $ref or $cb)
			ListarAlmacen($ref,$local,false, $cb);	
		break;	
	default:

	        $id           = (isset($_GET["Id"]))? CleanID($_GET["Id"]):0;
		$_SESSION["IdUltimoCambioAlmacen"] = $id;
		$ref          = $_SESSION["BusquedaReferencia"];
		$local        = $_SESSION["BusquedaLocal"];
		$cb           = $_SESSION["BusquedaCB"];
		$soloLlenos   = $_SESSION["BusquedaSoloConStock"];
		$nombre       = $_SESSION["BusquedaNombre"];
		$soloNS       = $_SESSION["BusquedaSoloNS"];
		$soloLote     = $_SESSION["BusquedaSoloLote"];
		$soloOferta   = $_SESSION["BusquedaSoloOferta"];
		$obsoletos    = $_SESSION["BusquedaObsoletos"];
		$reservados   = $_SESSION["BusquedaReservados"];
		$soloLlenos   = $_SESSION["BusquedaSoloConStock"];
		$marcadotrans = getSesionDato("CarritoTrans");  
		$local        = (!$local)? getSesionDato("IdTienda"):$local;
		if ($local or $ref or $cb)
		  ListarAlmacen($ref,$local,$marcadotrans,$cb,false,$soloLlenos,
				$obsoletos,$soloNS,$soloLote,$soloOferta,$reservados);
		
		echo "<!-- id:".getSesionDato("IdTienda")."  local:".$local."  -->";
		
		break;		
}


PageEnd();

?>
