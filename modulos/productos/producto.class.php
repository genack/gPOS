<?php

define("TALLAJE_VARIOS",5);
define("TALLAJE_VARIOS_TALLA",4);

function AccionesTrasAlta(){
	global $action;
	$ot = getTemplate("AccionesTrasAlta");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }
	
	$IdProducto = getSesionDato("UltimaAltaProducto");
	$oProducto = new producto;
	$oProducto->Load($IdProducto);
				
	$ot->fijar("IdProducto", $IdProducto);
	$ot->fijar("Serie",$oProducto->get("Serie"));
	$ot->fijar("Lote",$oProducto->get("Lote"));
	$ot->fijar("FechaVencimiento",$oProducto->get("FechaVencimiento"));
	
	//$ot->fijar("tEnviar" , _("Enviar"));
	$ot->fijar("action", $action);	
				
	echo $ot->Output();												
}

function AltaDesdePostProducto($esMudo=false) {
  
  $idalias0	= CleanText($_POST["IdProductoAlias0"]);
  $idalias1	= CleanText($_POST["IdProductoAlias1"]);
  $referencia 	= CleanReferencia($_POST["Referencia"]);
  $descripcion 	= CleanCadena($_POST["Descripcion"]);		
  $precioventa 	= ( isset( $_POST["PrecioVenta"]  ))? CleanDinero($_POST["PrecioVenta"]):false;
  $precioonline = ( isset( $_POST["PrecioOnline"] ))? CleanDinero($_POST["PrecioOnline"]):false;
  $coste 	= CleanDinero($_POST["CosteSinIVA"]);
  $idfamilia 	= CleanID($_POST["IdFamilia"]);
  $idsubfamilia = CleanID($_POST["IdSubFamilia"]);
  $condventa    = CleanText($_POST["CondicionVenta"]);
  $idprovhab 	= (!isset($_POST["IdProvHab"]))? CleanID($_POST["ProvHab"]) : CleanID($_POST["IdProvHab"]);
  $idprovhab    = CleanID($idprovhab);
  $idlabhab 	= (!isset($_POST["IdLabHab"]))? CleanID($_POST["LabHab"]) : CleanID($_POST["IdLabHab"]);
  $idlabhab 	= CleanID($idlabhab);
  $codigobarras = CleanCB($_POST["CodigoBarras"]);
  $refprovhab 	= (!isset($_POST["RefProv"]))? CleanReferencia($_POST["RefProvHab"]) : CleanReferencia($_POST["RefProv"]);
  $refprovhab   = CleanReferencia($refprovhab);
  $numeroserie  = (isset( $_POST["NumeroSerie"] ))?  CleanID($_POST["NumeroSerie"]=='on'):false;
  $metaproducto = (isset( $_POST["MetaProducto"] ))? CleanID($_POST["MetaProducto"]=='on'):false;
  $lote   	= (isset( $_POST["Lote"] ))? CleanID($_POST["Lote"]=='on'):false;
  $fv           = (isset( $_POST["FechaVencimiento"]))?CleanID($_POST["FechaVencimiento"]=='on'):false;
  $ventamenudeo = (isset( $_POST["VentaMenudeo"]))? CleanID($_POST["VentaMenudeo"]=='on'):false;
  $servicio     = (isset( $_POST["Servicio"]))? CleanID($_POST["IdTipoServicio"]):false;
  $undsxcont    = CleanID($_POST["UnidadesPorContenedor"]);
  $unidadmedida = CleanText($_POST["UnidadMedida"]);
  $idcolor 	= CleanID($_POST["IdColor"]);
  $idtalla      = CleanID($_POST["IdTalla"]);
  $idmarca      = (!isset($_POST["IdMarca"]))? CleanID($_POST["Marca"]) : CleanID($_POST["IdMarca"]);
  $idcontenedor = CleanID($_POST["IdContenedor"]);
  $idcontenedor = (!isset($_POST["IdContenedor"]))? $_POST["Contenedor"] : $_POST["IdContenedor"];
  $idcontenedor = CleanID($idcontenedor);

  if ($id = CrearProducto($esMudo,$referencia,$descripcion, $precioventa,
			  $precioonline,$coste,$idfamilia,$idsubfamilia,$idprovhab,
			  $codigobarras,$idtalla,$idcolor,$idmarca,$refprovhab,
			  $idalias0,$idalias1,$numeroserie,$undsxcont,$ventamenudeo,
			  $unidadmedida,$fv,$idlabhab,$lote,$idcontenedor,
			  $metaproducto,$servicio,$condventa)) 
    {
      if(!$esMudo)
	AccionesTrasAlta();
      return $id;
    } else {
    
    return false;
  } 
}

		
/* LISTADO COMBINADO */



function genListadoCruzado($IdProducto,$IdTallaje = false,$IdLang=false){	
    $IdProducto = CleanID($IdProducto);
    $IdTallaje 	= CleanID($IdTallaje);

    $out = "";//Cadena de salida

    if(!$IdLang)	$IdLang = getSesionDato("IdLenguajeDefecto");

    $sql = "SELECT Referencia, IdTallaje, IdProdBase,UnidadMedida FROM ges_productos WHERE IdProducto='$IdProducto' AND Eliminado='0'";
    $row = queryrow($sql);
    if (!$row)	return false;

    $tReferencia  = CleanRealMysql($row["Referencia"]);

    if(!$IdTallaje)	$IdTallaje = $row["IdTallaje"];
    if(!$IdTallaje) $IdTallaje = 2;//gracefull degradation
    $IdProdBase = $row["IdProdBase"];
    $UnidadMedida = $row["UnidadMedida"];

    $sql = "SELECT  ges_contenedores.Contenedor,ges_productos.VentaMenudeo,ges_productos.UnidadesPorContenedor,ges_locales.NombreComercial,ges_modelos.Color,
        ges_detalles.Talla, SUM(ges_almacenes.Unidades) as TotalUnidades FROM ges_almacenes INNER
        JOIN ges_locales ON ges_almacenes.IdLocal = ges_locales.IdLocal INNER
        JOIN ges_productos ON ges_almacenes.IdProducto =
        ges_productos.IdProducto INNER JOIN ges_modelos ON
        ges_productos.IdColor = ges_modelos.IdColor INNER JOIN ges_detalles ON
        ges_productos.IdTalla = ges_detalles.IdTalla   INNER JOIN ges_contenedores ON 
        ges_productos.IdContenedor = ges_contenedores.IdContenedor
        WHERE
        ges_productos.Referencia = '$tReferencia'
        AND
        ges_modelos.IdIdioma = 1
        AND ges_locales.Eliminado =0
        GROUP BY ges_almacenes.IdLocal, ges_productos.IdColor, ges_productos.IdTalla
        ORDER BY ges_almacenes.IdLocal, ges_productos.IdColor";

    $data 		= array();
    $colores 		= array();
    $tallas 		= array();
    $locales 		= array();
    $tallasTallaje 	= array();
    $listaColores 	= array();

    $res = query($sql,"Generando Listado Cruzado");

    while( $row = Row($res) ){
        $color 		  = $row["Color"];
        $talla 		  = NormalizaTalla($row["Talla"]);		
        $nombre 	  = $row["NombreComercial"];
        $unidades   	  = CleanInt($row["TotalUnidades"]);
        $ventamenudeo     = $row["VentaMenudeo"];
        $undsxcont        = $row["UnidadesPorContenedor"];
        $contenedor       = $row["Contenedor"];
        $colores[$color]  = 1;
        $tallas[$talla]   = 1;
        $locales[$nombre] = 1;
        //echo "Adding... c:$color,t:$talla,n:$nombre,u:$unidades<br>";
        $num     = 0;
        $enteros = 0;
        $puchos  = 0;

        if($ventamenudeo=="0" && $unidades != 0)
	  $data[$color][$talla][$nombre] = $unidades.$UnidadMedida;
        
        if($ventamenudeo=="1" && $unidades!=0){
	  if($undsxcont>$unidades){
                $puchos = "";
                $enteros = $unidades;
            }else{
                $enteros = intval($unidades/$undsxcont);
                $puchos = $unidades % $undsxcont;
            }
            if($puchos==0)
                $data[$color][$talla][$nombre] =  $enteros.$contenedor;
            else
                $data[$color][$talla][$nombre] =  $enteros.$contenedor."+".$puchos.$UnidadMedida;
        }
    }

    $sql = "SELECT Talla,SizeOrden FROM ges_detalles,ges_productos WHERE ges_detalles.IdTallaje= '$IdTallaje' AND ges_detalles.IdTalla = ges_productos.IdTalla AND ges_productos.IdProdBase = '$IdProdBase'  AND IdIdioma='$IdLang' AND ges_detalles.Eliminado='0'" .
        "	 ORDER BY SizeOrden ASC, Talla + 0 ASC";
    $res = query($sql);

    $numtallas =0;
    while($row = Row($res)){
        $orden = intval($row["SizeOrden"]);
        $talla = NormalizaTalla($row["Talla"]);
        $posicion = GetOrdenVacio($tallasTallaje,$orden,$talla); 
        $tallasTallaje[$posicion]  = $talla;
        $numtallas++; 
    }

    $out .= "<table class='forma'>";
    $num = 0;

    /*$out .= "<tr><td class='nombre'>".$tReferencia."</td>";

    foreach ($tallasTallaje as $k=>$v) {
        $out .= "<td class='lh' id='talla_$num'>".($v)."</td>";
        $num++;
    }
    $out .= "</tr>";*/

    foreach ($locales as $l=>$v2){


        $out .= "<tr class='f'><td></td><td class='lh' colspan='".($numtallas)."'>".($l)."</td></tr>";	


        $out .= "<tr><td class='nombre'>".$tReferencia."</td>";		
        foreach ($tallasTallaje as $k=>$v) {
            $out .= "<td class='lhz' id='talla_$num' style='width: 16px!important;background-color: #ccc'>".($v)."</td>";
            $num++;
        }
        $out .= "</tr>";

        foreach ($colores as $c=>$v1){	
            $out .= "<tr class='f'><td class='lh'>".($c)."</td>"; 	
            foreach ($tallasTallaje as $k2=>$t) {

                if (isset($data[$c][$t][$l])) {

                    $num= $data[$c][$t][$l];
                    $color = ($num<0)?"red":"black";

                    $u = "<b style='color: $color'>" . $data[$c][$t][$l] . "</b>";
                } else {
                    $u = "-"; 
                }

                $out .= "<td  align='center'>" . ($u) . "</td>";		
            }
            $out .= "</tr>";
        }


        $out .= "<tr><td><font color='white'>-</font></td></tr>\n";


    }
    $out .= "</table>";

    return $out;
}


function GetOrdenVacio($arreglo, $posicion=0,$talla=false){
	//Auxiliar.
	// Busca un slot vacio para colocar una talla.
	// Aunque las tallas tienen un orden 
	// este orden puede corromperse, y perderiamos tallas.
	
	if (!isset($arreglo[$posicion])){
		return $posicion;	
	}
	while( isset($arreglo[$posicion])){
		
		if ($arreglo[$posicion] == $talla){
			return $posicion;
		}

		$posicion = $posicion + 1;	 
	}
	
	return $posicion;	
}


function NormalizaTalla($talla){
	//$cad = substr ($cad, 0, -1);
	$talla = trim($talla);

	if (substr($talla, -1) =="-") {
		$talla = substr($talla,0,-1);
	}	

	if (substr($talla, 1) =="-") {
		$talla = substr($talla,1);
	}	

	//$talla = strtoupper($talla);
	$valnum = intval($talla);
/*	if ($valnum >0 and $valnum <10 ){
		$talla = " " . $valnum;
	} else {
    }		*/

	return $talla;
}


/* LISTADO COMBINADO */

/* CARRITO DE COMPRA */

function ActualizarCantidades() {
    $data = getSesionDato("CarritoCompras");
    $data2 = getSesionDato("CarroCostesCompra");
    $quitarproductos = ( isset($_POST["quitarproductos"]) )? $_POST["quitarproductos"]:false;
    $quitarproductos = explode(",",$quitarproductos);

    for($t=1;$t<200;$t++){
        if (isset($_POST["Id$t"])){
            $id = $_POST["Id$t"];
            $unid =$_POST["Cantidad$t"];
            $coste =$_POST["Precio$t"];

            if ($id) {
                $preval = $data[$id];
                $data[$id] = $unid;
                $data2[$id] = $coste;
            }

            //echo "Desde data[id]=$preval, para id=$id, cargando Cantidad$t=$unid<br>";
        }			
    }


    for($i=0;$i<count($quitarproductos);$i++){    
        unset($data[$quitarproductos[$i]]);
        unset($data2[$quitarproductos[$i]]);
    }

    setSesionDato("CarritoCompras",$data);		
    setSesionDato("CarroCostesCompra",$data2);


}

function ActualizarCantidadesDolar() {
		$data    = getSesionDato("CarritoCompras");
		$data2   = getSesionDato("CarroCostesCompra");
		$data3   = getSesionDato("descuentos");
		$detadoc = getSesionDato("detadoc");

		for($t=1;$t<200;$t++){
			if (isset($_POST["Id$t"])){
				$id = $_POST["Id$t"];
				$unid =$_POST["Cantidad$t"];
				$coste =$_POST["Precio$t"];
				
                if ($id) {
                    $preval    = $data[$id];
                    $data[$id] = $unid;

                    if ($detadoc[5]==1) {
                        $data2[$id]    = $coste*$detadoc[6];
                        $data3[$id][0] = $data3[$id][0]*$detadoc[6];
                        $data3[$id][1] = $data3[$id][1]*$detadoc[6];
                    }
                    if ($detadoc[5]==2) {
                        $data2[$id] = $coste;
                    }

                }
				
				//echo "Desde data[id]=$preval, para id=$id, cargando Cantidad$t=$valor<br>";
			}			
		}
		setSesionDato("CarritoCompras",$data);		
		setSesionDato("CarroCostesCompra",$data2);
		setSesionDato("descuentos",$data3);
}

function ActualizarDescuentosImportes() {

    $data = getSesionDato("descuentos");
	
    for($t=1;$t<200;$t++)
      {
	if (isset($_POST["Id$t"]))
	  {
	    $id         = $_POST["Id$t"];
	    $descuento  = $_POST["Descuento$t"];
	    $importe    = $_POST["Importe$t"];
	    $pdcto      = (isset($_POST["PorcentajeDescuento$t"]))?$_POST["PorcentajeDescuento$t"]:0;
	    if ($id) 
	      {
		$preval       = $data[$id];
		$data[$id][0] = $descuento;
		$data[$id][1] = $importe;
		$data[$id][2] = $pdcto;
	      }
	  }			
      }
    setSesionDato("descuentos",$data);		
}



/* CARRITO DE COMPRA */

/* BUSQUEDA DE DATOS*/

function getIdProductoFromIdArticulo($id){
	$id = CleanID($id);
	
	if ( isset($_SESSION["tIDALMACEN2IDPRODUCTO_$id"]) and intval($_SESSION["tIDALMACEN2IDPRODUCTO_$id"]) > 0 ) {
		return $_SESSION["tIDALMACEN2IDPRODUCTO_$id"];
	}
	

	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$id'";
	$row = queryrow($sql);
	
	if (!$row)	return false;
	
	$idprod = $row["IdProducto"];
	
	$_SESSION["tIDALMACEN2IDPRODUCTO_$id"] = $idprod;
	
	return $idprod;		
}

function getIdFromReferencia ($ref){
	if (!$ref)
		return false;
	
	$ref= CleanReferencia($ref);
	return genReferencia2IdProducto($ref);
}

function getProdBaseFromId($id){
	$id = CleanID($id);
	
	$key ="tPRODBASEFROMID_" . $id;
	
	if ( isset($_SESSION[$key]) and intval($_SESSION[$key]) > 0 ) {
		return $_SESSION[$key];
	}
	
	
	$sql = "SELECT IdProdBase FROM ges_productos WHERE IdProducto = '$id'";
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	$_SESSION[$key] = $row["IdProdBase"];
	
	return $row["IdProdBase"];
}

function getIdFromProdBase($id){
	$id = CleanID($id);
	
	$key ="tIDFROMPRODBASE_" . $id;
	
	if ( isset($_SESSION[$key]) and intval($_SESSION[$key]) > 0 ) {
		return $_SESSION[$key];
	}
	
	
	$sql = "SELECT IdProducto FROM ges_productos WHERE IdProdBase = '$id'";
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	$_SESSION[$key] = $row["IdProducto"];
	
	return $row["IdProducto"];
}

function getIdFromIdAlmacen($id){
       $id = CleanID($id);
	$key ="tIDFROMIDALMACEN_" . $id;
	
	if ( isset($_SESSION[$key]) and intval($_SESSION[$key]) > 0 ) {
		return $_SESSION[$key];
	}
	
	$sql = "SELECT IdProducto FROM ges_almacenes WHERE Id = '$id'";
	$row = queryrow($sql);
	if (!$row)
		return false;
	
	$_SESSION[$key] = $row["IdProducto"];
	
	return $row["IdProducto"];
}

function getCosteDefectoProducto($id) {
	$id = CleanID($id);
	$sql = "SELECT Costo FROM ges_productos WHERE IdProducto = '$id'"; 
	$row = queryrow($sql);
	if (!$row) return false;
	
	return $row["Costo"]; 	
}

function getIdProveedorFromIdProducto($id){	
	$sql = "SELECT IdProvHab FROM ges_productos WHERE IdProducto='$id' ";
	$row = queryrow($sql);
	
	return $row["IdProvHab"];	
}
function getIdLaboratorioFromIdProducto($id){	
	$sql = "SELECT IdLabHab FROM ges_productos WHERE IdProducto='$id' ";
	$row = queryrow($sql);
	
	return $row["IdLabHab"];	
}

function getIdMarcaFromIdProducto($id){	
	$sql = "SELECT IdMarca FROM ges_productos WHERE IdProducto='$id' ";
	$row = queryrow($sql);
	
	return $row["IdMarca"];	
}

function getIdSerieFromIdProducto($id){	
	$sql = "SELECT Serie FROM ges_productos WHERE IdProducto='$id' ";
	$row = queryrow($sql);
	
	return $row["Serie"];	
}

function ProductoFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdProducto"];
	
	$oProducto = new producto;
		
	if ($oProducto->Load($id))
		return $oProducto;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


function CrearProducto($mudo,$referencia,$descripcion, $precioventa,
		       $precioonline,$coste,$idfamilia,$idsubfamilia,$idprovhab,
		       $codigobarras,$idtalla,$idcolor,$idmarca,$refprovhab,
		       $idalias0,$idalias1,$numeroserie,$undsxcont,$ventamenudeo,
		       $unidadmedida,$fv,$idlabhab,$lote,$idcontenedor,
		       $metaproducto,$servicio,$condventa){
  global $action;
  $oProducto = new producto;

  $oProducto->Crea();
  
  if (!$idfamilia)	$idfamilia = getParametro("IdFamiliaDefecto");
  if (!$idsubfamilia)	$idfamilia = getParametro("IdFamiliaDefecto");
  
  //$oProducto->setNombre($nombre);
  $oProducto->setReferencia($referencia);	
  $oProducto->setDescripcion($descripcion);
  $oProducto->setLang(getSesionDato("IdLenguajeDefecto"));	
  $oProducto->setPrecioVenta(0);
  $oProducto->setPrecioOnline($precioonline);
  $oProducto->set("Costo",$coste,FORCE);
  $oProducto->set("IdFamilia",$idfamilia,FORCE);
  $oProducto->set("IdSubFamilia",$idsubfamilia,FORCE);
  $oProducto->set("IdProvHab",$idprovhab,FORCE);
  $oProducto->set("IdLabHab",$idlabhab,FORCE);
  $oProducto->set("CodigoBarras",$codigobarras,FORCE);
  $oProducto->set("RefProvHab",$refprovhab,FORCE);			
  $oProducto->set("Serie",$numeroserie,FORCE);
  $oProducto->set("Servicio",$servicio,FORCE);
  $oProducto->set("MetaProducto",$metaproducto,FORCE);
  $oProducto->set("Lote",$lote,FORCE);
  $oProducto->set("FechaVencimiento",$fv,FORCE);
  $oProducto->set("UnidadMedida",$unidadmedida,FORCE);
  $oProducto->set("VentaMenudeo",$ventamenudeo,FORCE);
  $oProducto->set("CondicionVenta",$condventa,FORCE);
  $oProducto->set("UnidadesPorContenedor",$undsxcont,FORCE);
  $oProducto->set("IdTalla",$idtalla,FORCE);
  $oProducto->set("IdColor",$idcolor,FORCE);
  $oProducto->set("IdMarca",$idmarca,FORCE);
  $oProducto->set("IdContenedor",$idcontenedor,FORCE);
  $oProducto->set("IdProductoAlias0",$idalias0,FORCE);
  $oProducto->set("IdProductoAlias1",$idalias1,FORCE);
  
  //		
  if ($oProducto->Alta()){
    
    //Guardamos el id de la ultima alta para procesos posteriores 
    // que quieran usarlo (encadenacion de acciones)
    setSesionDato("UltimaAltaProducto",$oProducto->getId());
    
    //TODO
    // una vez creado el producto, lo vamos a stockar en los almacenes
    // con cantidad cero
    
    $alm = getSesionDato("Almacen");
    
    error(__FILE__ . __LINE__ ,"Infor: Precio aqui es ". $oProducto->getPrecioVenta());
    
    $alm->ApilaProductoTodos($oProducto);
    return $oProducto->getId();
    
  } else {
    setSesionDato("UltimaAltaProducto",false);//por si acaso
    //setSesionDato("FetoProducto",$oProducto);
    if (!$mudo)
      echo $oProducto->formEntrada($action,false);	
    //echo gas("aviso",_("No se ha podido registrar el nuevo producto"));
    return false;
  }
}


function productoEnAlmacen($id) {
	global $FilasAfectadas;
	$sql = "SELECT Id FROM ges_almacenes WHERE Unidades>0 and IdProducto = '$id'";	
	$res = query($sql);
	$num = intval($FilasAfectadas);
	
	if (!$res){
		error(__FILE__ . __LINE__ ,"E: no se pudo contar en almacenes para $sql");
		return true;	
	}		
//	error(0,"Info: num es $num, con sql $sql"); 
	return ($num > 0);		
}

//eliminar uno de los dos

function getIdFromCodigoBarras($cb){
	$cb = CleanCB($cb);	
	if (!$cb or $cb=="")
		return false;
	
	$sql = 	"SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$cb')";
	$row = queryrow($sql);
	if (!$row){ 
		return false;
	}
	return $row["IdProducto"];
}

function getIdProductoSerieFromCB($cb){
	$cb = CleanCB($cb);	
	if (!$cb or $cb=="")
		return false;
	
	$sql = 	"SELECT IdProducto,Serie FROM ges_productos WHERE (CodigoBarras = '$cb')";
	$row = queryrow($sql);
	if (!$row){ 
		return false;
	}
	return $row;
}

function getCBfromIdProducto($IdProducto) {
	$IdProducto = CleanID($IdProducto);	
	$sql = 	"SELECT CodigoBarras FROM ges_productos WHERE IdProducto = '$IdProducto'";
	$row = queryrow($sql,"Busca CB de producto");
	if (!$row){ 
		return false;
	}
	return $row["CodigoBarras"];
}


function genReferencia2IdProducto($ref){
	
	$sql = 	"SELECT IdProducto FROM ges_productos WHERE (Referencia = '$ref')";
	$row = queryrow($sql);
	if (!$row){
		return false;
	}
	
	$id = $row["IdProducto"];
	
	return $id ;
}

function BuscaProductoPorReferencia($ref){	
	$sql = "SELECT IdProducto FROM ges_productos WHERE (Referencia='$ref')";
	$row = queryrow($sql);
	if ($row){
		return $row["IdProducto"];	
	}	else {
		return false;	
	}
}

/*
    * Tipo Impuesto - Obligatorio - 

    No se Almacena, es indicativo para dar de alta el producto en almacenes. 
    Por defecto se tomara valor "TipoImpuesto" de la tabla "ges_paises". 
    Un nuevo producto toma el tipo por defecto del pais en que esta el almacén central. 
	
	Producto->TipoImpuesto = AlmacenCentral->Pais->TipoImpuesto
	
	* Impuesto - Obligatorio - 

    Se almacena en "ges_productos_idioma". 
    Por defecto se tomara el valor "Impuesto" de la tabla "ges_productos_idioma". 
    Producto->Idioma->Impuesto = ??? Producto->Idioma->Impuesto
    
*/

function getTipoImpuesto($oProducto=false,$local=false) {
		$key = "tIMPUESTOCENTRALTIPO";

		if( isset($_SESSION[$key]))
			return $_SESSION[$key];


		$central = new local;
		if(!$central->LoadCentral())
			return false;
			
		
		$IdPais = CleanID($central->get("IdPais"));
		$sql = "SELECT TipoImpuestoDefecto FROM ges_paises WHERE IdPais='$IdPais'";
		$row = queryrow($sql,"Cargando TIPO impuesto de la central");
		
		if ($row) {
			$val = $row["TipoImpuestoDefecto"];
			$_SESSION[$key] = $val;
			return $val;
		}
			
		return "IVA";	
}
function ObtenerExistenciasTotalesAlmacen($id){

         $sql =
	   " select SUM(Unidades) as Existencias ".
           " from   ges_almacenes ".
           " where  IdProducto = '$id' ".
           " and    Eliminado = 0";
	 $res = query($sql);
	 if($row= Row($res)) 
	   return $row['Existencias'];
	 else 
	   return 0;
}
function ObtenerNumeroProductosPorReferencia($referencia){
    $num = 0;
    $sql = 
      " select COUNT(IdProducto) as 'TOTAL' ".
      " from   ges_productos ".
      " where  Referencia='$referencia' ".
      " and    Eliminado = '0'";
    $res = query($sql);
    $row = Row($res);
    return $row["TOTAL"];
}

function getValorImpuestoDefectoCentral() {
		$central = new local;
		$key = "tIMPUESTOCENTRAL";

		if( isset($_SESSION[$key]))
			return $_SESSION[$key];	
		
		if(!$central->LoadCentral())
			return false;
	
	
		
		$IdPais = CleanID($central->get("IdPais"));
		$sql = "SELECT ImpuestoDefecto FROM ges_paises WHERE IdPais='$IdPais'";
		$row = queryrow($sql,"Cargando VALOR impuesto de la central");
		
		if ($row) {
			$val = $row["ImpuestoDefecto"];
			$_SESSION[$key] = $val;
			return $val;
		}
			

		return "18";//Si algo falla, se ajusta a 18
}


function getIdColor2Texto($IdColor, $IdIdioma=false) {
	$IdColor = CleanID($IdColor);

	if (!$IdIdioma)
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
		
	$keyname = "tCOLOR_" . $IdColor;		
	//Cacheamos traduccion de talla en color	
	if ( isset( $_SESSION[$keyname] ) ) 	
             return $_SESSION[$keyname];
		
	
	$IdIdioma = CleanID($IdIdioma);		
	$sql = "SELECT Color  FROM ges_modelos  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' AND IdColor = '$IdColor'";
	$row = queryrow($sql);
	if (!$row)		return false;
	
	$_SESSION[$keyname] = $row["Color"];		
	return $row["Color"];	
}

function getIdProductoAlias2Texto($IdProductoAlias, $IdIdioma=false) {
	$IdProductoAlias = CleanID($IdProductoAlias);

	if (!$IdIdioma)
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
		
	$keyname = "tPRODUCTOALIAS_" . $IdProductoAlias;		
	//Cacheamos traduccion de talla en color	
	if ( isset( $_SESSION[$keyname] ) )
	  return $_SESSION[$keyname];
	
	$IdIdioma = CleanID($IdIdioma);		
	$sql = "SELECT ProductoAlias  FROM ges_productos_alias  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' AND IdProductoAlias = '$IdProductoAlias'";
	$row = queryrow($sql);
	if (!$row)		return false;
	
	$_SESSION[$keyname] = $row["ProductoAlias"];		
	return $row["ProductoAlias"];	
}

function setChangeColorbyIdColor($IdColor,$Color,$IdIdioma=false){
  if (!$IdIdioma)
    $IdIdioma = getSesionDato("IdLenguajeDefecto");
  $IdIdioma = CleanID($IdIdioma);	
  $sql = "UPDATE ges_modelos SET Color = '".$Color."'
          WHERE  ges_modelos.IdColor   = '".$IdColor."' 
          AND    ges_modelos.IdIdioma  = '".$IdIdioma."' 
          AND    Eliminado = 0";
  $res = query($sql);
  if (!$res) {
    $this->Error(__FILE__ . __LINE__, "E: no se pudo actualizar el  modelo en el producto");
    return false;	
  }		
  //Cacheamos traduccion color
  $_SESSION["tCOLOR_$IdColor"] = $Color;
  return true;
}

function setChangeTallabyIdTalla($IdTalla,$Talla,$IdIdioma=false){
  if (!$IdIdioma)
    $IdIdioma = getSesionDato("IdLenguajeDefecto");
  $IdIdioma = CleanID($IdIdioma);	
  $sql = "UPDATE ges_detalles SET Talla = '".$Talla."'
          WHERE  ges_detalles.IdTalla   = '".$IdTalla."' 
          AND    ges_detalles.IdIdioma  = '".$IdIdioma."' 
          AND    Eliminado = 0";
  $res = query($sql);
  if (!$res) {
    $this->Error(__FILE__ . __LINE__, "E: no se pudo actualizar el  detalle en el producto");
    return false;	
  }		
  //Cacheamos traduccion talla
  $_SESSION["tTALLA_$IdTalla"] = $Talla;

  return true;
}

function getLikeProductoAlias2Id($ProductoAlias, $IdIdioma=false) {
         $ProductoAlias = CleanText($ProductoAlias);
	if (strlen($ProductoAlias) < 4) return false;
	if(substr($ProductoAlias,0,1)=='*'){
	  $ProductoAlias = substr($ProductoAlias,1);
	  if (!$IdIdioma)
	    $IdIdioma = getSesionDato("IdLenguajeDefecto");
	  $IdIdioma = CleanID($IdIdioma);		
	  $sql = "SELECT IdProductoAlias 
                  FROM  `ges_productos_alias` 
                  WHERE  `IdIdioma` = '".$IdIdioma."'
                  AND  `ProductoAlias` LIKE  '%".$ProductoAlias."%'
                  AND  `Eliminado` =0
                  LIMIT 0 , 1";
	  $row = queryrow($sql);
	  if (!$row) return false;
	  return $row["IdProductoAlias"];	
	} else return false;
}


function getIdTalla2Texto($IdTalla, $IdIdioma=false) {
	$IdColor = CleanID($IdTalla);
	if (!$IdIdioma)
		$IdIdioma = getSesionDato("IdLenguajeDefecto");
	
	//Cacheamos traduccion de talla en color	
	if ( isset($_SESSION["tTALLA_$IdTalla"] )) 
	  return $_SESSION["tTALLA_$IdTalla"];

	$IdIdioma = CleanID($IdIdioma);				
	$sql = "SELECT Talla FROM ges_detalles  WHERE Eliminado=0 AND (IdIdioma = '$IdIdioma') AND (IdTalla = '$IdTalla')";
	$row = queryrow($sql);
	if (!$row)
	  return false;
	$_SESSION["tTALLA_$IdTalla"] = $row["Talla"];
	return $row["Talla"];	
}

function getIdMarca2Texto($IdMarca) {
	$IdMarca = CleanID($IdMarca);
		
	$sql = "SELECT Marca FROM ges_marcas WHERE Eliminado=0 AND (IdMarca = '$IdMarca')";
	$row = queryrow($sql);
	if (!$row)
		return false;
		
	return $row["Marca"];	
}
function getIdContenedor2Texto($IdContenedor) {
	$IdContenedor = CleanID($IdContenedor);
		
	$sql = "SELECT Contenedor FROM ges_contenedores WHERE Eliminado=0 AND (IdContenedor = '$IdContenedor')";
	$row = queryrow($sql);
	if (!$row)
		return false;
		
	return $row["Contenedor"];	
}

function getIdFamilia2Texto($IdFamilia) {
	$IdFamilia 	= CleanID($IdFamilia);
	$IdIdioma 	= getSesionDato("IdLenguajeDefecto");
	
	if (!$IdFamilia){
		return "";
	}		
	
	$keyname = "tFAMILIA_". $IdFamilia;
	
	if (isset(	$_SESSION[$keyname]) and $_SESSION[$keyname]){
		return $_SESSION[$keyname];
	}		
		
	//query("SELECT '$keyname buscando familia'");
			
	$sql = "SELECT Familia FROM ges_familias WHERE IdFamilia = '$IdFamilia' AND IdIdioma='$IdIdioma'";
	$row = queryrow($sql,"Cargando $keyname");
	if (!$row) {	
		$_SESSION[$keyname] = "";
		return "";
	}
	
	$familia = $row["Familia"];
	
	if (getParametro("FamiliaLatin1")){		
		$familia = iso2utf($familia);	
	}	
	
	$_SESSION[$keyname] = $familia;
	
	//query("SELECT '$keyname sera $familia'");
		
	return $familia;	
}

function getIdSubFamilia2Texto($IdFamilia,$IdSubFamilia) {
	$IdSubFamilia 	= CleanID($IdSubFamilia);
	$IdFamilia 		= CleanID($IdFamilia);
	
	if (!$IdFamilia){
		return "";
	}	
	
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	
	$keyname = "tSUBFAMILIA_".$IdFamilia."_".$IdSubFamilia;
	
	if (isset(	$_SESSION[$keyname]) and $_SESSION[$keyname]){
		return $_SESSION[$keyname];
	}			
	
		
	$sql = "SELECT SubFamilia FROM ges_subfamilias WHERE Eliminado=0 AND IdSubFamilia = '$IdSubFamilia' AND IdFamilia='$IdFamilia' AND IdIdioma='$IdIdioma'";
	$row = queryrow($sql);
	if (!$row)		return "";
	
	$subfamilia = $row["SubFamilia"];
	if (getParametro("SubFamiliaLatin1")){		
		$subfamilia = iso2utf($subfamilia);	
	}			
	
	$_SESSION[$keyname]  = $subfamilia;
			
	return $subfamilia;	
}


function getFirstNotNull($tabla,$id){
	$sql = "SELECT $id as IdCosa FROM $tabla WHERE Eliminado=0";
	$row = queryrow($sql);
	if (!$row) return 0;
	return $row["IdCosa"];
}

function getSubFamiliaAleatoria($IdFamilia){

	$sql = "SELECT IdSubFamilia as IdCosa FROM ges_subfamilias WHERE IdFamilia='$IdFamilia' AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row) return 0;
	return $row["IdCosa"];
}


/* BUSQUEDA DE DATOS*/

/* CLASE */

class producto extends Cursor {
	
	var $lastLang;
	var $ges_productos;
	var $ges_productos_idioma;
	var $_fallodeintegridad;
	
    function producto() {
    	return $this;
    }
      
    function Init(){
    	$this->ges_productos = array("Referencia","CodigoBarras","RefProvHab",
				     "IdProdBase","IdProvHab","IdTalla","Servicio","IdColor",
				     "IdFamilia","Costo","IdSubFamilia","IdProvHab","IdMarca",
				     "IdTallaje","Serie","MetaProducto","UnidadesPorContenedor",
				     "VentaMenudeo","UnidadMedida","FechaVencimiento","IdLabHab",
				     "Lote","IdContenedor","IdProductoAlias0","IdProductoAlias1",
				     "CondicionVenta");	
	$this->ges_productos_idioma = array("IdProdBase","IdIdioma","Descripcion");			    	
    }  
      
    function SiguienteProducto() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdProducto"));		
		return true;			
	}

    function ListadoFlexible($idprov,$idmarca,$idcolor,$idtalla,$lang,
			     $min=0,$base=false,$idprod=false,$idfamilia=false,$tamPag=10,
			     $ref,$cb,$nombre,$obsoletos=false,$idalias=false,$idlab=false,
			     $idsubfamilia=false){
		
	//	error(__FILE__ . __LINE__ ,"($cb)($ref)($nombre)$idprov,$idmarca,$idcolor,$idtalla,$lang,$min=0,$base=false,$idprod=false,$idfamilia=false,$tamPag=10");
			
      $extra = "";
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    	if ($idprov)
    		$extra .= "AND ges_productos.IdProvHab  = '$idprov' ";
    	if ($idlab)
    		$extra .= "AND ges_productos.IdLabHab  = '$idlab' ";
    	if ($idmarca)
    		$extra .= "AND ges_productos.IdMarca  = '$idmarca' ";
    	if ($idcolor and $idcolor>0)
    		$extra .= "AND ges_productos.IdColor  = '$idcolor' ";
    	if ($idtalla)
    		$extra .= "AND ges_productos.IdTalla  = '$idtalla' ";
    	if ($base)
    		$extra .= "AND ges_productos.IdProdBase  = '$base' ";
    	if ($idprod)
    		$extra .= "AND ges_productos.IdProducto  = '$idprod' ";
    	if ($idfamilia)
    		$extra .= "AND ges_productos.IdFamilia  = '$idfamilia' ";
    	if ($idsubfamilia)
    		$extra .= "AND ges_productos.IdSubFamilia  = '$idsubfamilia' ";
    	if ($ref)
    		$extra .= "AND ges_productos.Referencia  = '$ref' ";
    	if ($cb)
    		$extra .= "AND ges_productos.CodigoBarras  = '$cb' ";
    	if ($idalias)
    		$extra .= "AND (IdProductoAlias0 = '".$idalias."' OR IdProductoAlias1 ='".$idalias."')";
	else{
	  if ($nombre)
    		$extra .= "AND ges_productos_idioma.Descripcion  LIKE '%".$nombre."%' ";
	}    		

    	if (!$obsoletos)
    		$extra .= "AND ges_productos.Obsoleto=0 ";
    		    		    
	$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Descripcion
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		$extra		".
		"ORDER BY ".		
		" ges_productos_idioma.Descripcion ASC, " .
		" ges_productos.IdProdBase ASC ";
		
		
		
		$res = $this->queryPagina($sql, $min, $tamPag);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	
	function ListadoFlexibleCompras($idprov,$idmarca,$idcolor,$idtalla,$lang,$min=0,
					$base=false,$idprod=false,$idfamilia=false,$tamPag,
					$ref,$cb,$nombre,$obsoletos=false,$idalias=false,
					$idlab=false,$porproveedor,$stockminimo){
	//	error(__FILE__ . __LINE__ ,"($cb)($ref)($nombre)$idprov,$idmarca,$idcolor,$idtalla,$lang,$min=0,$base=false,$idprod=false,$idfamilia=false,$tamPag=10");

	  $extra     = "";
	  $extrafrom = "";

	  if (!$lang)
	    $lang = getSesionDato("IdLenguajeDefecto");
	  if ($idprov and $porproveedor)
	     $extra .= "AND ges_productos.IdProvHab  = '$idprov' ";
	  if ($idlab)
	    $extra .= "AND ges_productos.IdProvHab  = '$idlab' ";
	  if ($idmarca)
	    $extra .= "AND ges_productos.IdMarca  = '$idmarca' ";
	  if ($idcolor and $idcolor>0)
	    $extra .= "AND ges_productos.IdColor  = '$idcolor' ";
	  if ($idtalla)
	    $extra .= "AND ges_productos.IdTalla  = '$idtalla' ";
	  if ($base)
	    $extra .= "AND ges_productos.IdProdBase  = '$base' ";
	  if ($idprod)
	    $extra .= "AND ges_productos.IdProducto  = '$idprod' ";
	  if ($idfamilia)
	    $extra .= "AND ges_productos.IdFamilia  = '$idfamilia' ";
	  if ($ref)
	    $extra .= "AND ges_productos.Referencia  = '$ref' ";
	  if ($cb)
	    $extra .= "AND ges_productos.CodigoBarras  = '$cb' ";
	  if ($idalias)
	    $extra .= "AND (IdProductoAlias0 = '".$idalias."' OR IdProductoAlias1 ='".$idalias."')";
	  else{
	    if ($nombre)
	      $extra .= "AND ges_productos_idioma.Descripcion  LIKE '%".$nombre."%' ";
	  }    	
	
	  if($stockminimo){

	    $idlocal   = getSesionDato("IdTienda");
	    $extra    .= " AND ges_almacenes.IdLocal = $idlocal AND ges_productos.Servicio = 0 AND ges_almacenes.StockMin > 0 AND ges_almacenes.StockMin >= ges_almacenes.Unidades ";
	    $extrafrom = " INNER JOIN ges_almacenes ON ges_almacenes.IdProducto = ges_productos.IdProducto ";
	  }

	  if (!$obsoletos)
	    $extra .= "AND ges_productos.Obsoleto=0 ";

	  $sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Descripcion

		FROM  ges_productos 
                INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
                $extrafrom
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
                AND ges_productos.Servicio = 0 
                AND ges_productos.MetaProducto = 0
		$extra		".
	    "ORDER BY ".		
	    " ges_productos_idioma.Descripcion ASC, " .
	    " ges_productos.IdProdBase ASC ";

	  $res = $this->queryPagina($sql, $min, $tamPag);
	  if (!$res) {
	    $this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
	  }	else {				
	    $this->setLang($lang);
	  }		
	  
	  return $res;				
	}	


	function ListadoProveedor($IdProvHab,$lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos.IdProvHab  = '$IdProvHab'
		AND ges_productos_idioma.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	
function ListadoLaboratorio($IdLabHab,$lang,$min=0){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos.IdLabHab  = '$IdLabHab'
		AND ges_productos_idioma.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	

	function Listado($lang,$min=0,$tamPagina=10){
			
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Descripcion
		
		FROM
		ges_productos INNER JOIN ges_productos_idioma ON
		ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos_idioma.Eliminado = 0";
		
		$res = $this->queryPagina($sql, $min, $tamPagina);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}	else {				
			$this->setLang($lang);
		}		
				
		return $res;				
	}	
	  
      
    function Load($id,$lang=false){
    	$this->Init();
    	$id = CleanID($id);
    	if (intval($id)==0){
    		error(__FILE__ . __LINE__ , "Info: cargando id, pero '$id' es cero");
    		return false;    		
    	}   
    	    	
    	if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
		$sql = "SELECT
		ges_productos.*,
		ges_productos_idioma.IdProdIdioma,
		ges_productos_idioma.Alias1,
		ges_productos_idioma.Alias2,
		ges_productos_idioma.Descripcion,
		ges_familias.Familia,
		ges_subfamilias.SubFamilia
		
		FROM
		ges_productos
		INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase =
		ges_productos_idioma.IdProdBase
		INNER JOIN ges_familias ON ges_productos.IdFamilia = ges_familias.IdFamilia
		INNER JOIN ges_subfamilias ON (ges_productos.IdSubFamilia =
		ges_subfamilias.IdSubFamilia AND ges_productos.IdFamilia =
		ges_subfamilias.IdFamilia)
		
		WHERE
		ges_productos_idioma.IdIdioma = '$lang'
		AND ges_familias.IdIdioma = '$lang'
		AND ges_subfamilias.IdIdioma = '$lang'
		AND ges_productos.Eliminado = 0
		AND ges_productos.IdProducto = '$id' ";

		
		
		
		$res = $this->queryrow($sql);//pregunta e importa fila
		if (!$res){
			$this->Error(__FILE__ . __LINE__ , "E: cargando producto");
			return false;			
		}				
		$this->setId($id);
		//$this->set("IdProducto",$id);
		$this->setLang($lang);				
    	return true;
    }
        
    function setLang($lang){    	    	
		$this->set("IdIdioma",$lang,FORCE);
		$this->lastLang = $lang;    	
    }           
    
    function getLang(){
    	return $this->lastLang;	
    }
    
    function getNombre(){
    	if (!getParametro("ProductosLatin1"))
    		return $this->get("Descripcion");//ya es UTF8
    	else
    		return iso2utf($this->get("Descripcion"));//requiere conversion a utf8
    }
    
	//Formulario de modificaciones y altas
    function formEntrada($action,$esModificar,$lang=false,$esPopup=false){


        if (!$esModificar)				
            $ot = getTemplate("AltaProductoOnlineMulti");
        else
            $ot = getTemplate("ModProductoOnlineMulti");

        if (!$ot){	return false; }

            if ($esModificar) {
                $modo = "modsave";
                $titulo = _("Modificando producto");	
            } else {
                $modo = "newsave";
                $titulo = _("Nuevo producto");			
            }

        if ($esPopup)
            $onClose = "window.close();";	
        else
            $onClose = "location.href='modproductos.php'";

        if ($esPopup or !$esModificar)
            $ListadoCombinado = "";
        else {
            if ($esModificar)
                $ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));
        }



	$incluido   = ($this->get("Serie")==1)? "checked":"";
	$incluido4  = ($this->get("Lote")==1)? "checked":"";
	$incluido3  = ($this->get("FechaVencimiento")==1)? "checked":"";
	$incluido2  = ($this->get("VentaMenudeo")==1)? "checked":"";
	$txtMoDet   = getModeloDetalle2txt();
	$txtModelo  = $txtMoDet[1];
	$txtDetalle = $txtMoDet[2];
	$txtalias   = $txtMoDet[3];
	$txtref     = $txtMoDet[4];
	$btca       = ( $txtMoDet[0]  == "BTCA" )?false:"style='display:none;'";
	$ismtposerv = ( $this->get("Servicio") || $this->get("MetaProducto") )?"style='display:none;'":false;
        $cambios = array(
            "tNuevaTallaOColor" => _("Nuevo $txtModelo / $txtDetalle"),
            "ListadoCombinado" => $ListadoCombinado,
            "vIdProducto" => $this->getId(),
            "onClose" => $onClose,
            "tImprimirCodigoBarras" => _("Imprimir código barras"),
            "vRefProvHab"=> $this->get("RefProvHab"),
            "tRefProvHab"=> $txtref,
            "vIdMarca" =>  $this->get("IdMarca"),
            "Imagen" =>  $this->get("Imagen"),
            "tMarca" => _("Marca"),
            "vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
            "vIdContenedor" =>  $this->get("IdContenedor"),
            "tContenedor" => _("Contenedor"),
            "vContenedor" => getIdContenedor2Texto($this->get("IdContenedor")),			
            "vIdTalla" =>  $this->get("IdTalla"),
            "tTalla" => _("Talla"),
            "tModelo" => $txtModelo,
            "vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),
            "vIdColor" =>  $this->get("IdColor"),
            "tColor" => _("Color"),
            "tDetalle" => $txtDetalle,
            "vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
            "tCodigoBarras" => _("Código barras"),
            "vCodigoBarras" => $this->get("CodigoBarras"),
            "tTitulo" => $titulo,	
            "HIDDENDATA" => Hidden("id",$this->getId()),
            "ACTION" => "$action?modo=$modo",
            "Referencia" => _("Referencia"),
            "vReferencia" =>  $this->getReferencia(),
            "isbtca" => $btca,
            "ismtposerv" => $ismtposerv,
            "vIdProductoAlias0" =>  $this->get("IdProductoAlias0"),
            "tProductoAlias0" => $txtalias,
            "vProductoAlias0" => getIdProductoAlias2Texto($this->get("IdProductoAlias0"),$this->get("IdIdioma")),

            "vIdProductoAlias1" =>  $this->get("IdProductoAlias1"),
            "tProductoAlias1" => $txtalias,
            "vProductoAlias1" => getIdProductoAlias2Texto($this->get("IdProductoAlias1"),$this->get("IdIdioma")),

            "tNumeroSerie"=> _("Maneja Numeros de Serie"),
            "cNumeroSerie" => $incluido,
            "tLote"=> _("Maneja Lote"),
            "cLote" => $incluido4,
            "tFechaVencimiento"=> _("Maneja Fecha Vencimiento"),
            "cFechaVencimiento" => $incluido3,
            "tVentaMenudeo"=> _("Venta al menudeo"),
            "cVentaMenudeo" => $incluido2,
            "tUnidadesPorContenedor" => _("Unidades Por Contenedor"),
            "vUnidadesPorContenedor" => $this->get("UnidadesPorContenedor"),
            "tUnidadMedida" => _("Unidad Medida"),
            "vUnidadMedida" => $this->get("UnidadMedida"),
            "vUM" => "Unidades ",

            "tCosteSinIVA" => _("Costo Ref."),
            "vCosteSinIVA" => $this->get("Costo")*1,
            "Descripcion" => _("Nombre"),
            "vDescripcion" => $this->getDescripcion(),			
            "PrecioVenta" => _("Precio venta"),
            "vPrecioVenta" => $this->getPrecioVenta(),
            "PrecioOnline" => _("Precio online"),
            "vPrecioOnline" => $this->getPrecioOnline(),
            //"comboFamilias" => genComboFamilias($this->get("IdFamilia")),

            "tFamilia" => _("Familia..."),
            "tSubFamilia" => _("Sub familia..."),

            "TipoImpuesto" => _("Impuesto"),

            "tIdProvHab" => _("Proveedor hab."),
            "vIdProvHab" => $this->get("IdProvHab"),
            "vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),

            "tIdLabHab" => _("Laboratorio hab."),
            "vIdLabHab" => $this->get("IdLabHab"),
            "vLaboratorioHab" => getNombreLaboratorio($this->get("IdLabHab")),

            "vTipoImpuesto" => $this->getTipoImpuesto(),
            "vIdFamilia" => $this->get("IdFamilia"),
            "vIdSubFamilia" => $this->get("IdSubFamilia"),						

            "vFamilia" => getIdFamilia2Texto($this->get("IdFamilia")),
            "vSubFamilia" =>getIdSubFamilia2Texto( $this->get("IdFamilia"),$this->get("IdSubFamilia") ),						
            "vImpuesto" => $this->getImpuesto()					
        );
	
        return $ot->makear($cambios);									
    }
  
  
    
	//Formulario de modificaciones y altas
    function formEntradaBar($action,$lang=false,$esPopup=false){

        $ot = getTemplate("ModBarFicha");

        if (!$ot){	return false; }

        $modo    = "modsavebar";		
        $titulo  = _("Modificando producto");	
        $onClose = "location.href='modproductos.php'";

        $ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));
	$incluidons  = ($this->get("Serie")==1)? "checked":"";
	$incluidolt  = ($this->get("Lote")==1)? "checked":"";
	$incluidofv  = ($this->get("FechaVencimiento")==1)? "checked":"";
	$incluidomd  = ($this->get("VentaMenudeo")==1)? "checked":"";
        $existencias = ObtenerExistenciasTotalesAlmacen($this->getId());
	$readonly    = ($existencias>0)? "disabled='true'":"";
	$readonlyUM  = "";
        $numreg      = ObtenerNumeroProductosPorReferencia($this->getReferencia());
        $um          = "";
        $ounidad     = ($numreg!=1)? "disabled":"";
        $ometro      = ($numreg!=1)? "disabled":"";
        $olitro      = ($numreg!=1)? "disabled":"";
        $okilo       = ($numreg!=1)? "disabled":"";
        switch ($this->get("UnidadMedida")) 
	  {
	  case 'und': $um = "Unidades"; $ounidad = "selected"; break;
	  case 'mts': $um = "Metros"; $ometro = "selected"; break;
	  case 'lts': $um = "Litros"; $olitro = "selected"; break;
	  case 'kls': $um = "Kilos";  $okilo = "selected"; break;
	  }
	
	$osrm  = "";
	$ocrm  = "";
	$ocrmr = "";
        switch ($this->get("CondicionVenta")) 
	  {
	  case '0'   : $osrm = "selected";  break;
	  case 'CRM' : $ocrm = "selected";  break;
	  case 'CRMR': $ocrmr = "selected"; break;
	  }

	$txtMoDet   = getModeloDetalle2txt();
	$esBTCA     = (  $txtMoDet[0]  == "BTCA" );
	$hidden     = "style='display:none;'";
	$btca       = ( $esBTCA )?false:$hidden;

	$ismtposerv = ( $this->get("Servicio") || $this->get("MetaProducto") )?$hidden:false;
	$isserie    = ( $this->get("Serie"))?$hidden:false;
	$txtalias   =  $txtMoDet[3];
	$txtref     =  $txtMoDet[4];
	$txtModelo  =  $txtMoDet[1];
	$txtDetalle =  $txtMoDet[2];
	$editTalla  = ( esEditable2Producto( $this->get("IdTalla"), 'Talla' ) )? "onfocus='setchangeid(1);this.select()'":"readonly='true'";
	$editColor  = ( esEditable2Producto( $this->get("IdColor"), 'Color' ) )? "onfocus='setchangeid(0);this.select()'":"readonly='true'";
        $cambios    = array(
            "tNuevaTallaOColor" => _("Nuevo $txtModelo / $txtDetalle"),
            "ListadoCombinado" => $ListadoCombinado,
            "vIdProducto" => $this->getId(),
            "onClose" => $onClose,
            "tImprimirCodigoBarras" => _("Imprimir código barras"),
            "vRefProvHab"=> $this->get("RefProvHab"),
            "tRefProvHab"=> $txtref,
            "vIdMarca" =>  $this->get("IdMarca"),
            "Imagen" =>  $this->get("Imagen"),
            "tMarca" => _("Marca"),
            "vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
            "vIdContenedor" =>  $this->get("IdContenedor"),
            "tContenedor" => _("Contenedor"),
            "vContenedor" => getIdContenedor2Texto($this->get("IdContenedor")),	
            "vIdTalla" =>  $this->get("IdTalla"),
            "tModelo" => $txtModelo,
            "vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),			
            "vIdColor" =>  $this->get("IdColor"),
            "tDetalle" => $txtDetalle,
            "vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
            "tCodigoBarras" => _("Código barras"),
            "vCodigoBarras" => $this->get("CodigoBarras"),
            "tTitulo" => $titulo,	
            "HIDDENDATA" => Hidden("id",$this->getId()),
            "ACTION" => "$action?modo=$modo",
            "Referencia" => _("Referencia"),
            "vReferencia" =>  $this->getReferencia(),
            //"Nombre" => _("Nombre"),
            //"vNombre" => $this->getNombre(),
            "vIdProductoAlias0" =>  $this->get("IdProductoAlias0"),
            "tProductoAlias0" => $txtalias,
            "vProductoAlias0" => getIdProductoAlias2Texto($this->get("IdProductoAlias0"),$this->get("IdIdioma")),

            "vIdProductoAlias1" =>  $this->get("IdProductoAlias1"),
            "tProductoAlias1" => $txtalias,
            "vProductoAlias1" => getIdProductoAlias2Texto($this->get("IdProductoAlias1"),$this->get("IdIdioma")),
            "tCosteSinIVA" => _("Costo Ref."),
            "vCosteSinIVA" => $this->get("Costo")*1,
            "Descripcion" => _("Nombre"),
            "vDescripcion" => $this->getDescripcion(),			
            "isbtca" => $btca,
            "ismtposerv" => $ismtposerv,
	    "isserie" => $isserie,
            "PrecioVenta" => _("Precio venta"),
            "vPrecioVenta" => $this->getPrecioVenta(),
            "PrecioOnline" => _("Precio online"),
            "vPrecioOnline" => $this->getPrecioOnline(),
            //"comboFamilias" => genComboFamilias($this->get("IdFamilia")),
            "tFamilia" => _("Familia..."),
            "tSubFamilia" => _("Sub familia..."),
            "TipoImpuesto" => _("Impuesto"),
            "tIdProvHab" => _("Proveedor hab."),
            "vIdProvHab" => $this->get("IdProvHab"),
            "vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),
            "tIdLabHab" => _("Laboratorio hab."),
            "vIdLabHab" => $this->get("IdLabHab"),
            "vLaboratorioHab" => getNombreLaboratorio($this->get("IdLabHab")),
            "tVentaMenudeo"=> _("Venta al menudeo"),
            "cVentaMenudeo" => $incluidomd,
            "cNumeroSerie" => $incluidons,
            "cLote" => $incluidolt,
            "cFechaVencimiento" => $incluidofv,
            "tUnidadesPorContenedor" => _("Unidades Por Contenedor"),
            "vUnidadesPorContenedor" => $this->get("UnidadesPorContenedor"),
            "tUnidadMedida" => _("Unidad Medida"),
            "vUnidadMedida" => $this->get("UnidadMedida"),
            "vUM" => $um,
            "ounidad" => $ounidad,
            "ometro" => $ometro,
            "olitro" => $olitro,
            "okilo" => $okilo,
            "oSRM" => $osrm,
            "oCRM" => $ocrm,
            "oCRMR" => $ocrmr,
            "oEditColor" => $editColor,
            "oEditTalla" => $editTalla,
            "vReadOnly" => $readonly,
            "vReadOnlyUM" => $readonlyUM,
            "vTipoImpuesto" => $this->getTipoImpuesto(),
            "vIdFamilia" => $this->get("IdFamilia"),
            "vIdSubFamilia" => $this->get("IdSubFamilia"),						
            "vFamilia" => getIdFamilia2Texto($this->get("IdFamilia")),
            "vSubFamilia" => $this->get("SubFamilia"),						
            "vImpuesto" => $this->getImpuesto()						
        );

        return $ot->makear($cambios);									
    }
	  
	//Formulario de modificaciones y altas
    function formClon($action, $lang=false,$volver=false){

		$ot = getTemplate("ClonProducto");
			
		if (!$ot){	return false; }
		
		$modo = "salvaclon";
			
		$ListadoCombinado = genListadoCruzado($this->getId(),$this->get("IdTallaje"));
		
		$tieneserie  = $this->get("Serie");
		$incluidons  = ($this->get("Serie")==1)? "checked":"";
		$incluidolt  = ($this->get("Lote")==1)? "checked":"";
		$incluidofv  = ($this->get("FechaVencimiento")==1)? "checked":"";
		$incluidomd  = ($this->get("VentaMenudeo")==1)? "checked":"";
		$motivomd    = ($this->get("VentaMenudeo")==1)? "visible":"hidden";
		$tienefechavencimiento = $this->get("FechaVencimiento");
		$volver = ($volver=="modcompras")?"modcompras.php":"modproductos.php";		
		$numreg = ObtenerNumeroProductosPorReferencia($this->getReferencia());
		$readonlyUM = "readonly";
		$readonly   = "readonly";
		$um = "";
		$ounidad = "";
		$ometro = "";
		$olitro = "";
		$okilo = "";
		switch ($this->get("UnidadMedida")) 
		  {
		  case 'und': $um = "Unidades"; $ounidad = "selected"; break;
		  case 'mts': $um = "Metros";   $ometro = "selected"; break;
		  case 'lts': $um = "Litros";   $olitro = "selected"; break;
		  case 'kls': $um = "Kilos";    $okilo = "selected"; break;
		  }

		$osrm  = "";
		$ocrm  = "";
		$ocrmr = "";
		switch ($this->get("CondicionVenta")) 
		  {
		  case '0'   : $osrm = "selected";  break;
		  case 'CRM' : $ocrm = "selected";  break;
		  case 'CRMR': $ocrmr = "selected"; break;
		  }
		$txtMoDet   = getModeloDetalle2txt();
		$esBTCA     = (  $txtMoDet[0]  == "BTCA" );
		$hidden     = "style='display:none;'";
		$btca       = ( $esBTCA )?false:$hidden;
		$txtref     = $txtMoDet[4];
		$ismtposerv = ( $this->get("Servicio") || $this->get("MetaProducto") )?$hidden:false;
		$isserie    = ( $this->get("Serie"))?$hidden:false;
		$txtalias   = $txtMoDet[3];
		$txtModelo  = $txtMoDet[1];
		$txtDetalle = $txtMoDet[2];
		$txtTitulo  = ( $esBTCA )?'Nueva':'Nuevo';
		$titulo     = _("$txtTitulo $txtModelo / $txtDetalle");
					
		$cambios = array(
			"tPrecioVenta" => _("Previo venta"),
			"vPrecioVenta" => $this->get("PrecioVenta"),
			//"phpPageVolver" => $volver,
			"vIdTallaje" => $this->get("IdTallaje"),
			"ListaCombinada" => $ListadoCombinado,
			"tImprimirCodigoBarras" => _("Imprimir código barras"),
			"vRefProvHab"=> $this->get("RefProvHab"),
			"tRefProvHab"=> $txtref,
			"vIdMarca" =>  $this->get("IdMarca"),
			"tMarca" => _("Marca"),
			"vMarca" => getIdMarca2Texto($this->get("IdMarca")),			
			"vIdContenedor" =>  $this->get("IdContenedor"),
			"tContenedor" => _("Contenedor"),
			"vContenedor" => getIdContenedor2Texto($this->get("IdContenedor")),
			"vIdTalla" =>  $this->get("IdTalla"),
			"tModelo" => $txtModelo,
			"vTalla" => getIdTalla2Texto($this->get("IdTalla"),$this->get("IdIdioma")),
			"vIdColor" =>  $this->get("IdColor"),
			"tDetalle" => $txtDetalle,
			"vColor" => getIdColor2Texto($this->get("IdColor"),$this->get("IdIdioma")),
			"tCodigoBarras" => _("Código barras"),
			"vCodigoBarras" => $this->get("CodigoBarras"),
			"tTitulo" => $titulo,	
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"action" => "$action?modo=$modo&idBase=",
			"Referencia" => _("Referencia"),
			"vReferencia" =>  $this->getReferencia(),
			//"Nombre" => _("Nombre"),
			//"vNombre" => $this->getNombre(),
			"tCosteSinIVA" => _("Costo Ref."),
			"vCosteSinIVA" => $this->get("Costo")*1,
			"Descripcion" => _("Nombre"),
			"vDescripcion" => $this->getDescripcion(),			
			"isbtca" => $btca,
			"ismtposerv" => $ismtposerv,
			"isserie" => $isserie,
			"vIdProductoAlias0" =>  $this->get("IdProductoAlias0"),
			"tProductoAlias0" => $txtalias,
			"vProductoAlias0" => getIdProductoAlias2Texto($this->get("IdProductoAlias0"),$this->get("IdIdioma")),

			"vIdProductoAlias1" =>  $this->get("IdProductoAlias1"),
			"tProductoAlias1" => $txtalias,
			"vProductoAlias1" => getIdProductoAlias2Texto($this->get("IdProductoAlias1"),$this->get("IdIdioma")),
			"PrecioVenta" => _("Precio venta"),
			"vPrecioVenta" => $this->getPrecioVenta(),
			"PrecioOnline" => _("Precio online"),
			"vPrecioOnline" => $this->getPrecioOnline(),
			//"comboFamilias" => genComboFamilias($this->get("IdFamilia")),
			"tVentaMenudeo"=> _("Venta al menudeo"),
			"cVentaMenudeo" => $incluidomd,
			"vMotivoMd" => $motivomd,
			"cNumeroSerie" => $incluidons,
			"cLote" => $incluidolt,
			"cFechaVencimiento" => $incluidofv,
			"tUnidadesPorContenedor" => _("Unidades Por Contenedor"),
			"vUnidadesPorContenedor" => $this->get("UnidadesPorContenedor"),
			"tUnidadMedida" => _("Unidad Medida"),
			"vUnidadMedida" => $this->get("UnidadMedida"),
			"vUM" => $um,
			"ounidad" => $ounidad,
			"ometro" => $ometro,
			"olitro" => $olitro,
			"okilo" => $okilo,
			"oSRM" => $osrm,
			"oCRM" => $ocrm,
			"oCRMR" => $ocrmr,
			"vReadOnly" => $readonly,
			"vReadOnlyUM" => $readonlyUM,
			"vSerie"=> $tieneserie,
			"vFechaVencimiento"=> $tienefechavencimiento,
			"tFamilia" => _("Familia..."),
			"tSubFamilia" => _("Sub familia..."),
			"TipoImpuesto" => _("Impuesto"),
			"tIdProvHab" => _("Proveedor hab."),
			"vIdProvHab" => $this->get("IdProvHab"),
			"vProveedorHab" => getNombreProveedor($this->get("IdProvHab")),
			"tIdLabHab" => _("Laboratorio hab."),
			"vIdLabHab" => $this->get("IdLabHab"),
			"vLaboratorioHab" => getNombreLaboratorio($this->get("IdLabHab")),
			"vTipoImpuesto" => $this->getTipoImpuesto(),
			"vIdFamilia" => $this->get("IdFamilia"),
			"vIdSubFamilia" => $this->get("IdSubFamilia"),
			"vFamilia" => $this->get("Familia"),
			"vSubFamilia" => $this->get("SubFamilia"),
			"vImpuesto" => $this->getImpuesto()	
					
		);
		return $ot->makear($cambios);
	}
	
	function getReferencia(){
		return $this->get("Referencia");	
	}
	function getSerie(){
		return $this->get("Serie");	
	}
	function getVentaMenudeo(){
		return $this->get("VentaMenudeo");	
	}

	function getLote(){
		return $this->get("Lote");	
	}

	function getFechaVencimiento(){
		return $this->get("FechaVencimiento");	
	}

	function getUnidadesPorContenedor(){
		return $this->get("UnidadesPorContenedor");	
	}
	function getUnidadMedida(){
		return $this->get("UnidadMedida");	
	}
	function getDescripcion(){
    	return $this->get("Descripcion");					
	}
	function getContenedor(){
	  return getIdContenedor2Texto($this->get("IdContenedor"));
	}
	function getPrecioVenta(){		
		return (float)$this->get("PrecioVenta");	
	}
	
	function getPrecio(){		
		return (float)$this->get("PrecioVenta");	
	}
	
	function getPrecioFormat(){
		return money_format('%!i Soles', $this->getPrecioVenta());	
	}

	function getPrecioOnline(){
		return $this->get("PrecioOnline");	
	}

	function getTipoImpuesto(){
		return $this->get("TipoImpuesto");		
	}
	
	function getImpuesto(){
		return $this->get("Impuesto");	
	}
	
	function getCB(){
		return $this->get("CodigoBarras");	
	}
	
	function getTextTalla() {
		$lang = $this->getLang();
		$IdTalla = $this->get("IdTalla");
		$sql = "SELECT Talla FROM ges_detalles WHERE IdIdioma='$lang' AND IdTalla='$IdTalla'";
		$row = queryrow($sql,"Lee texto talla");
		if (!$row)
			return false;
		
		if (getParametro("TallasLatin1")) //detecta si necesita conversión
			return iso2utf($row["Talla"]);
		else 	
			return $row["Talla"]; 		
	}
	
	function getTextColor() {
		$lang = $this->getLang();
		$IdColor = $this->get("IdColor");
		$sql = "SELECT Color FROM ges_modelos WHERE IdIdioma='$lang' AND IdColor='$IdColor'";
		$row = queryrow($sql,"Lee texto color");
		if (!$row)
			return false;
			
		if (getParametro("ColoresLatin1"))			
			return iso2utf($row["Color"]);
		else
			return $row["Color"]; 			
	}
					
	function Crea(){

		$this->Init();
		$this->regeneraCodigos();
						
		$this->setNombre(_("Nuevo producto"));
									
		$this->setPrecioVenta(0);
		$this->setPrecioOnline(0);
		$this->set("Costo",0,FORCE);
		$fam = getFirstNotNull("ges_familias","IdFamilia");
		$this->set("IdFamilia",$fam,FORCE);
		$this->set("IdSubFamilia",getSubFamiliaAleatoria($fam), FORCE);
		$this->set("IdProvHab",getFirstNotNull("ges_proveedores","IdProveedor"),FORCE);
		$this->set("IdLabHab",getFirstNotNull("ges_laboratorios","IdLaboratorio"),FORCE);
		$this->set("IdMarca",getFirstNotNull("ges_marcas","IdMarca"),FORCE);
		$this->set("IdContenedor",getFirstNotNull("ges_contenedores","IdContenedor"),FORCE);
		$this->set("IdProductoAlias0",getFirstNotNull("ges_productos_alias","IdProductoAlias"),FORCE);		
		$this->set("IdProductoAlias1",getFirstNotNull("ges_productos_alias","IdProductoAlias"),FORCE);		
		$this->set("IdTallaje",TALLAJE_VARIOS,FORCE);
		$this->set("IdTalla",TALLAJE_VARIOS_TALLA,FORCE);
		
		$oAlmacen = getSesionDato("AlmacenCentral");
		
		if ($oAlmacen){
		  //$this->set("");
		  $this->set("TipoImpuesto",getTipoImpuesto(),FORCE);	
		  $this->set("Impuesto",getValorImpuestoDefectoCentral(),FORCE);
		}
		//$this->set("IdProvHab",
		
	}
		
	function regeneraCodigos() {
		$minval = "0000";					
		$sql = "SELECT Max(IdProducto) as RefSugerido, Max(CodigoBarras) as MaxBarras FROM ges_productos";
		$row = queryrow($sql,"Imaginando referencia apropiada");
		if ($row) {
			$sugerido =  $row["RefSugerido"];
			$maxbarras = $row["MaxBarras"];		
			$minval = $sugerido + 1001;
		}							
		
		$letra = strtoupper(chr(ord('a')+rand()%25));
		$this->setReferencia($letra . $minval); 
		
		$this->regeneraCB();
	}
	
	function CBRepetido(){
	
		$cb = $this->get("CodigoBarras");
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras='$cb') AND Eliminado=0";
		$row = queryrow($sql,"¿Esta repetido?");
		if (!$row)
			return false;
			
		return (intval($row["IdProducto"])>0);		
	}
	
	
	function regeneraCB() {
		$minval = 0;					
		$sql = "SELECT Max(IdProducto) as RefSugerido, Max(CodigoBarras+1001) as MaxBarras FROM ges_productos";
		$row = queryrow($sql,"Sugiriendo CB Valido");
		if ($row) {
			$sugerido 	= intval($row["RefSugerido"]);
			$maxbarras 	= intval($row["MaxBarras"]);
			if (intval($maxbarras) > intval($sugerido))
				$minval = intval($maxbarras);
			else
				$minval = intval($sugerido) + 90000001;
											
		} else {
			$minval = 90000001+ rand()*10000;	
		}
				
		$extra = 1001;
		$cb = intval($minval)+intval($extra);
		$this->set("CodigoBarras", $cb,FORCE);
		
		while($this->CBRepetido()){
			$extra = $extra + 1001;		
			$cb = intval($minval) + intval($extra);
			$this->set("CodigoBarras", $cb ,FORCE);
		}  
	}
	
	function Alta(){
		global $UltimaInsercion;

		$this->Init();	//antibug squad		
		if (!$this->AutoIntegridad()){
		  $this->regeneraCB();
		}	
		if (!$this->AutoIntegridad()){
			$this->Error(__FILE__ . __LINE__, "Info: no pudo crear producto, fallo de integridad: [" . $this->getFallo() . "]");
			return false;
		}
		
		//$sql = "SELECT Max(IdProdBase) FROM ges_productos_idioma";
		
		$ref = CleanRef( $this->get("Referencia") );
		$sql = "SELECT IdProdBase FROM ges_productos WHERE Referencia='$ref'";	
		$row = queryrow($sql);
		
		if ($row) {
			//Ya conocemos esta referencia, luego le corresponde este prodbase
			$this->set("IdProdBase",$row["IdProdBase"],FORCE);
			error(0,"Info: prodbase fue " . $row["IdProdBase"] );			
			$existeIdioma = true;
		} else 	{
			//No conocemos esta referencia, luego es un nuevo prodbase		
			$sql = "SELECT Max(IdProdBase) as IdProdBase FROM ges_productos";
			$row = queryrow($sql);
			if ($row){
				$IdProdBase = intval($row["IdProdBase"]) + 1;	
			} else {
				error (__FILE__ . __LINE__ , "E: $sql no saco idprodbase adecuado");
				return false;	
			} 
			error(0,"Info: prodbase sera " . $IdProdBase );			
			$this->set("IdProdBase",$IdProdBase,FORCE);
			$existeIdioma = false;
		}

		//error(__FILE__ . __LINE__ , "Info: export sera .." . var_export($this->export(),true ) );						
		
		$sql = CreaInsercion($this->ges_productos,$this->export(),"ges_productos");

	
		//error(__FILE__ . __LINE__ ,"Info: va a ejecutar '$sql' para objeto" . var_export($this,true));
		$res = query($sql,"alta producto");
		$IdProducto = $UltimaInsercion;
		$this->setId($IdProducto);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}		
					
		if (!$existeIdioma) {
		    //Solo creamos idioma cuando es primera vez para este prodbase
			$sql = CreaInsercion($this->ges_productos_idioma,$this->export(),"ges_productos_idioma");
			$res = query($sql,"alta producto idioma");
			if (!$res) {
				$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
				return false;
			}		
		}
		return true;		 						 	
	}		
		
	function Clon(){
		global $UltimaInsercion;

		$this->Init();		
		
		if (!$this->AutoIntegridadClon()){
			//$this->Error(__FILE__ . __LINE__, "Info: no pudo crear producto, fallo de integridad");
			return false;
		}				
		
		$sql = CreaInsercion($this->ges_productos,$this->export(),"ges_productos");
		$res = query($sql,"clon producto");
		$IdProducto = $UltimaInsercion;
		$this->setId($IdProducto);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}		
		/*	
		 * Los datos de idioma no son necesarios de clonar
		 * 
		$sql = CreaInsercion($this->ges_productos_idioma,$this->export(),"ges_productos_idioma");
		$res = query($sql,"clon producto idioma");
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"E: no pudo insertar el producto");
			return false;
		}*/		
		
		return true;		 						 	
	}		
		
	function setFallo($fallo=false){
		$this->_fallodeintegridad = $fallo;
	}	
	
	function getFallo(){
		return $this->_fallodeintegridad;
	}			

	//INTEGRIDAD NORMAL

	function IntegridadNombre(){
		$nombre = $this->get("Nombre");
						
		if (!$nombre or strlen($nombre)<1) {
		  	$this->setFallo(_("Nombre demasiado corto"));
			return false;
		}
		
		if ($nombre=="Nuevo producto") {
			$this->setFallo(_("Nombre genérico no valido"));
			return false;	
		}
			
		return true;			
	}

	function IntegridadFamilia(){
		
		$lang = $this->getLang();
		
		$oFamilia = new Familia;
		
		$IdFamilia = intval($this->get("IdFamilia"));
		$IdSubFamilia = intval($this->get("IdSubFamilia"));
		if (!$IdFamilia or !$IdSubFamilia){
			$this->setFallo(_("Familia o subfamilia incorrecta"));
			return false;	
		}
		
		//Si la familia no existe, no tiene sentido utilizarla.
		$sql = "SELECT Id FROM ges_familias WHERE IdFamilia='$IdFamilia' ";
		$row = queryrow($sql,"Existe la familia?");		
		if (!$row){
			$this->setFallo(_("Familia incorrecta") );
			return false;						
		}		
				
		$sql = "SELECT Id FROM ges_subfamilias WHERE IdFamilia = '$IdFamilia' AND IdSubFamilia = '$IdSubFamilia'";
		$row = queryrow($sql,'Existe la subfamilia?');
				
		if (!is_array($row)){
			//
			//A peticion del cliente, se quiere que la gestion de subfamilias se autocorrija.
			//asi que haremos que el fallo aqui no sea fatal.
			
			$sql = "SELECT MIN(IdSubFamilia) as IdSubFamilia, SubFamilia
					FROM ges_subfamilias
					WHERE IdFamilia = '$IdFamilia'
					AND Eliminado = 0
					AND IdIdioma = '$lang'";
			$row = queryrow($sql,"Intentamos un arreglo de subfamilia");
			if(!$row or !$row["IdSubFamilia"]) {
				$this->setFallo(_("Subfamilia incorrectos"));
				return false;
			}
									
			$this->set("IdSubFamilia",$row["IdSubFamilia"],FORCE);
			return true;
		}  
						
		if (!$oFamilia->LoadSub($row["Id"])){
			$this->setFallo(_("Subfamilia incorrecta"));
			return false;			
		}				
		
		return true;		
	}
	
	function IntegridadReferencia() {
	
		return true;
		
		//No hacemos integridad de referencia para permitir que el usuario
		// asigne "prodbase" a mano mediante cambios en la referencia.
	
		$id = $this->getId();			
							
		$ref = $this->getReferencia();
		
		$sql = "SELECT IdProducto,IdProdBase FROM ges_productos WHERE (Referencia = '$ref') AND (IdProducto != '$id')";
		$res = query($sql);
		
		
		if (!$res){
			$this->Error(__FILE__ . __LINE__ , "E: $sql, error desconocido");
			return true;	
		}
	
		$row = Row($res);		
		if (!is_array($row)){
			return true;	
		}		
								
		$ViejoProdbase = $row["IdProdBase"];
		
		if ($ViejoProdbase != $this->get("IdProdBase")) {
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));		
			$this->Error(__FILE__ . __LINE__ , "Info: prodbase $ViejoProdbase colisiona con $id de ref $ref");
			return false;
		}				

/*		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe un producto con esa referencia"));
			return false;	
		}*/
		
		
		return true;					 
	}
		
	function IntegridadCodigoBarras() {
		$id = $this->getId();			
						
		$ref = $this->get("CodigoBarras");
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$ref') ";
		
		$row = queryrow($sql);
		
		if (!$row){
			return true;	
		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con ese código de barras"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe un producto con ese código de barras"));
			error(__FILE__ . __LINE__ ,"Info: Validacion: viejoid '$IdViejo' tiene '$ref'cb, luego '$id' no puede usarlo");
			return false;	
		}
		
		return true;					 
	}		

	function IntegridadTallasyColores() {
		$id = $this->getId();			
							
		$talla = $this->get("IdTalla");
		$color = $this->get("IdColor");
		$idprodbase = $this->get("IdProdBase");
		
		$sql  = "SELECT IdProducto FROM ges_productos WHERE (IdTalla = '$talla') AND (IdColor = '$color') AND (IdProdBase = '$idprodbase') ";
		
		$row = queryrow($sql,"..comprobando integridad de modelo y detalle");
		if(!$row) {			
			return true;
		}
		
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo and $id and $IdViejo != $id){
			// duplicado!
			$this->setFallo(_("Ya existe ese modelo y detalle para el producto"));
			return false;			
		} 				
		
		if (!$id and $IdViejo){
			//Ya existe uno!
			$this->setFallo(_("Ya existe el producto que quiere insertar con ese modelo y detalle"));
			return false;	
		}
		
		return true;	
		
	}	
			
	//INTEGRIDAD CLON

	function IntegridadReferenciaClon() {		
		
		//TODO: actualizar considerando que otro de la misma prodbase debe usar misma ref			
		$ref = $this->getReferencia();
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (Referencia = '$ref') ";
		$row = queryrow($sql);		
				
		if (!$row) {			return true;		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con esa referencia"));
			return false;			
		} 				
		
		
		return true;					 
	}
		
						
	function IntegridadFamiliaClon() {
		return $this->IntegridadFamilia();	
	}		
	
	function IntegridadCodigoBarrasClon() {
		//Bloquea productos con codigobarras repetido
				
		$ref = $this->get("CodigoBarras");	
		
		if(!$ref)	
			return false;
		
		$sql = "SELECT IdProducto FROM ges_productos WHERE (CodigoBarras = '$ref') ";
		
		$row = queryrow($sql);
		
		if (!$row){			return true;		}		
						
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Referencia duplicada: ya existe un producto con ese código de barras"));
			return false;			
		} 				
				
		return true;					 
	}		
		
			
	function IntegridadTallasyColoresClon() {						
		//Bloquea productos con igual triplete IdTalla+IdColor+IdProdBase	
		$talla = $this->get("IdTalla");
		$color = $this->get("IdColor");
		$idprodbase = $this->get("IdProdBase");
		
		$sql  = "SELECT IdProducto FROM ges_productos WHERE (IdTalla = '$talla') AND (IdColor = '$color') AND (IdProdBase = '$idprodbase') ";
		
		$row = queryrow($sql,"..comprobando integridad");
		if(!$row) {			
			return true;
		}
		
		$IdViejo = $row["IdProducto"];
		
		if ($IdViejo){
			// duplicado!
			$this->setFallo(_("Ya existe ese modelo y detalle para el producto"));
			return false;			
		} 				
			
		return true;			
	}	
					
	// AUTO INTEGRIDAD CLON
		
	function AutoIntegridadClon(){
		if (!$this->IntegridadTallasyColoresClon()){
			return false;	
		}		
		
		if (!$this->IntegridadCodigoBarrasClon()){
			return false;
		}		
		
		if (!$this->IntegridadFamiliaClon()){
			return false;	
		}		
		
		/*//TODO:
		if (!$this->IntegridadReferenciaClon()){
			return false;	
		}*/			
			
		return true;					
	}	

	// AUTO INTEGRIDAD
		
	function AutoIntegridad(){
		
		if (!$this->IntegridadTallasyColores()){
			return false;	
		}		
		
		if (!$this->IntegridadCodigoBarras()){
			return false;
		}		
		
		if (!$this->IntegridadFamilia()){
			return false;	
		}		
		
		if (!$this->IntegridadReferencia()){
			return false;	
		}			
			
		$this->AjustaTallaje();
			
		return true;			
	}	
				
	function AjustaTallaje() {
		//Detecta si es necesario un cambio de tallaje, y ajusta apropiadamente.
		
		$IdTalla = CleanID($this->get("IdTalla"));
		$sql = "SELECT IdTallaje FROM ges_detalles WHERE IdTalla = '$IdTalla'";
		$row = queryrow($sql,"¿Es tallaje correcto?");
		if (!$row) return true;//??.. no hay talla?
		$IdTallaje = $row["IdTallaje"];
		$this->set("IdTallaje",$IdTallaje,FORCE); 						
	}				
				
///////////////desde aqui
    function ActualizarProveedoresSerieProductos($IdProdBase,$IdProvHab,$Serie){
        $sql = "UPDATE ges_productos SET IdProvHab = '$IdProvHab', Serie = '$Serie' WHERE ges_productos.IdProdBase = '$IdProdBase' AND Eliminado = 0";
	$res = query($sql);
		if (!$res) {
            $this->Error(__FILE__ . __LINE__, "E: no se pudo actualizar el  proveedor en los productos");
			return false;	
		}		
        return true;
    }
///////////////////hasta aqui MADE IN CHON				
	function Modificacion(){

		$this->Init();						

		if (!$this->AutoIntegridad()){
			$this->Error(__FILE__ . __LINE__, "Info: no pudo modificar producto, fallo de integridad");
			return false;
		}
		
		$sql = CreaUpdate($this->ges_productos,$this->export(),"ges_productos","IdProducto",$this->getId());
		$res = query($sql);
		
		if (!$res) {
			$this->Error(__FILE__ . __LINE__, "E: no pudo modificar producto");
			return false;	
		}				
		
		$sql = CreaUpdate($this->ges_productos_idioma,$this->export(),"ges_productos_idioma","IdProdIdioma",$this->get("IdProdIdioma"));
		$res = query($sql);
		if (!$res){
			$this->Error(__FILE__ . __LINE__, "E: no pudo modificar producto, datos idioma");
			return false;	
		}		
        $this->ActualizarProveedoresSerieProductos($this->get("IdProdBase"),$this->get("IdProvHab"),$this->get("Serie"));
		return true;		
	}		
				
	function setNombre($nombre){
		$this->set("Nombre",$nombre,FORCE);						
	}	

	function setReferencia($ref){
		$this->set("Referencia",$ref, FORCE);						
	}	
	
	function setDescripcion($Descripcion){
		$this->set("Descripcion",$Descripcion,FORCE);
	}

	function actualizarCosto($id,$costo){
	  $sql = "UPDATE ges_productos SET Costo = $costo WHERE IdProducto = '$id'";
	  $res = query($sql);
	  if (!$res)
	    return error(__FILE__ . __LINE__ , "W: no pudo actualizar costo articulo");
	  return true;
	}
	
	function EliminarProducto(){
		$id = $this->getId();
		
		$sql = "UPDATE ges_productos SET Eliminado = 1 WHERE IdProducto = '$id'";
		$res = query($sql);
		if (!$res)
			error(__FILE__ . __LINE__ , "W: no pudo borrar registro");
			
		$idbase = $this->get("IdProdBase"); 
					
		$sql = "SELECT IdProducto FROM ges_productos WHERE (IdProdBase='$idbase') AND Eliminado=0";
		$row = queryrow($sql);
		
		$existe = false;
		if ($row)
			$existe = $row["IdProducto"];
		
		if (!$existe) {
			//Ya no quedan prodictos para este prodbase				
			$sql = "UPDATE ges_productos_idioma SET Eliminado = 1 WHERE IdProdBase = '$id'";
			$res = query($sql);
			if (!$res)
				error(__FILE__ . __LINE__ , "W: no pudo borrar registro en idioma");
		}
			
		$sql = "UPDATE ges_almacenes SET Eliminado = 1 WHERE IdProducto = '$id'";
		$res = query($sql);
		if (!$res)
			error(__FILE__ . __LINE__ , "W: no pudo borrar registros de almacen");			
									
	}	
		
	function setPrecioVenta($value){
	         $igv = getSesionDato("IGV");
		 if($igv>0) 
		   $value = $value * (1+($igv/100));
		 $this->set("PrecioVenta",$value,FORCE);	
	}

	function setPrecioOnline($value){
		$this->set("PrecioOnline",$value,FORCE);	
	}
	
	function getTallaTexto(){
		return getIdTalla2Texto($this->get("IdTalla"));
	}
	
	function getColorTexto(){
		return getIdColor2Texto($this->get("IdColor"));
	}
	
	function getMarcaTexto(){
		return getIdMarca2Texto($this->get("IdMarca"));
	}

}

/* CLASE */

function setVigenciaMProductos($nsmprod,$vigencia){
 
  if($nsmprod!=''){
      $nsmp = explode(',',$nsmprod);
      foreach ($nsmp as $k=>$ns) {	
	$sql = 
	  " UPDATE ges_metaproductos ".
	  " SET   VigenciaMetaProducto	= '".$vigencia."'".
	  " WHERE  CBMetaProducto = '".$ns."'";
	query($sql);	
      }
  }

}

function getDatosProductosExtra($id,$extra=false){

	 $oPd = new producto;
	 $oPd->Load($id);

         switch ($extra) {

	 case "nombre" :
	   return $oPd->get("Descripcion")." ".
	     getIdMarca2Texto($oPd->get("IdMarca"))." ".
	     getIdColor2Texto($oPd->get("IdColor"))." ".
	     getIdTalla2Texto($oPd->get("IdTalla"))." ".
	     getNombreLaboratorio($oPd->get("IdLabHab"));

	   break;

	 case "nombreref" :
	   return $oPd->get("Referencia")." ".
	          $oPd->get("Descripcion")." ".
	          getIdMarca2Texto($oPd->get("IdMarca"))." ".
	          getIdColor2Texto($oPd->get("IdColor"))." ".
	          getIdTalla2Texto($oPd->get("IdTalla"))." ".
	          getNombreLaboratorio($oPd->get("IdLabHab"));

	   break;

	 case "prodbaseref" :
	   return $oPd->get("Referencia")." ".
	          $oPd->get("Descripcion")." ".
	          getIdMarca2Texto($oPd->get("IdMarca"))." ".
	          getNombreLaboratorio($oPd->get("IdLabHab"));

	   break;

	 case "nombrecb" :
	   return $oPd->get("CodigoBarras")." ".
	          $oPd->get("Descripcion")." ".
	          getIdMarca2Texto($oPd->get("IdMarca"))." ".
	          getIdColor2Texto($oPd->get("IdColor"))." ".
	          getIdTalla2Texto($oPd->get("IdTalla"))." ".
	          getNombreLaboratorio($oPd->get("IdLabHab"));

	   break;

	 case "id" :
	   $arreglo = array(); 
	   $arreglo["Referencia"]   = $oPd->get("Referencia");
	   $arreglo["CodigoBarras"] = $oPd->get("CodigoBarras");
	   $arreglo["IdTalla"]      = $oPd->get("IdTalla");
	   $arreglo["IdMarca"]      = $oPd->get("IdMarca");
	   $arreglo["IdColor"]      = $oPd->get("IdColor");
	   $arreglo["IdLabHab"]     = $oPd->get("IdLabHab");
	   $arreglo["Serie"]        = $oPd->get("Serie");
	   $arreglo["Lote"]         = $oPd->get("Lote");
	   $arreglo["Vence"]        = $oPd->get("FechaVencimiento");
	   return $arreglo;

	   break;

	 case 'todos':
	   $arreglo  = array(); 
	   $Servicio = ( $oPd->get("Servicio") > 0 )? 1:0;
	   array_push($arreglo, $oPd->get("VentaMenudeo"));
	   array_push($arreglo, $oPd->get("UnidadesPorContenedor"));
	   array_push($arreglo, $oPd->get("UnidadMedida"));
	   array_push($arreglo, $oPd->getContenedor("Contenedor"));
	   array_push($arreglo, $oPd->get("Costo"));
	   array_push($arreglo, $oPd->get("Serie"));
	   array_push($arreglo, $oPd->get("Lote"));
	   array_push($arreglo, $oPd->get("FechaVencimiento"));
	   array_push($arreglo, $oPd->get("MetaProducto"));
	   array_push($arreglo, $Servicio);
	   return $arreglo;

	   break;

	 case 'nombretodos':
	   $nombre =  $oPd->get("CodigoBarras")." ".
                      $oPd->get("Descripcion")." ".
	              getIdMarca2Texto($oPd->get("IdMarca"))." ".
	              getIdColor2Texto($oPd->get("IdColor"))." ".
	              getIdTalla2Texto($oPd->get("IdTalla"))." ".
	              getNombreLaboratorio($oPd->get("IdLabHab"));
	   $arreglo = array(); 

	   $arreglo["Nombre"]   = $nombre;
	   $arreglo["Menudeo"]  = $oPd->get("VentaMenudeo");
	   $arreglo["UndxEmp"]  = $oPd->get("UnidadesPorContenedor");
	   $arreglo["Und"]      = $oPd->get("UnidadMedida");
	   $arreglo["Empaque"]  = $oPd->getContenedor("Contenedor");
	   $arreglo["Costo"]    = $oPd->get("Costo");
	   $arreglo["Serie"]    = $oPd->get("Serie");
	   $arreglo["Lote"]     = $oPd->get("Lote");
	   $arreglo["Vence"]    = $oPd->get("FechaVencimiento");
	   $arreglo["Meta"]     = $oPd->get("MetaProducto");
	   $arreglo["Servicio"] = ( $oPd->get("Servicio") > 0 )? 1:0;
	   return $arreglo;
	   break;
	 }
}

function esEditable2Producto( $id,$tcampo ){

	$id = CleanID($id);
	
	$sql = 
	  " select count(Id".$tcampo.") as total ".
	  " from   ges_productos ".
	  " where  Id".$tcampo."   = '".$id."' ".
	  " and    Eliminado = 0";
	$row = queryrow($sql);

	if ( $row["total"] > 1 )
		return false;
	return true;
}

function getTipoServicio($xid){
  $sql = 
    " select SAT ".
    " from   ges_tiposervicio ".
    " where  IdTipoServicio='$xid'";
  $row = queryrow($sql);
  return ( $row["SAT"] == "0")? 1:2;  
}

?>
