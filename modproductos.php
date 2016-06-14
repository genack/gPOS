<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

global $tamPagina,$txtMoDet;

$tamPagina = ( getSesionDato("GlobalGiroNegocio") == 'BTQE')? 200:100;
$txtMoDet  = getGiroNegocio2txt();

function UploadFoto() {        
        require ("modulos/fileupload/fileupload-class.php");

        $full = "productos_img/";
        //$mini = "productos_img/";

        $my_uploader = new uploader;
        $success = $my_uploader->upload("file", "", ".jpg");
        if ($success) {
                $modo = 2;
                $success = $my_uploader->save_file($full, $modo);
                if (!$success) {
					return false;                  
                }
                return  $my_uploader->file['name'];
        }
		return false;        
}
                            

function AutoOpen(){
         $mod = ( isset( $_SESSION["IdUltimoCambioProductos"] ))? $_SESSION["IdUltimoCambioProductos"]
	                                                          :false;
	 if (!$mod)	return "//no hay ultimod";
	
	 $id = $mod;	
	
	 //$_SESSION["IdUltimoCambioProductos"] = false;
	 return "\n MuestraBases($id);\n";	
}

function AccionesSeleccion(){
		global $action;
	$ot = getTemplate("AccionesSeleccionProd");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }

	$hayCarrito = getSesionDato("hayCarritoProd");
	
	if  ($hayCarrito){
		$ot->fijar("tOperaCarro",_("Trabajar con selección"));
		$ot->fijar("tBorraCarro",_("Deseleccionar"));
	} else {
		$ot->fijar("tOperaCarro", "");		
		$ot->fijar("tBorraCarro" , "");
		$ot->eliminaSeccion("carro");	
	}	
				
	
	$ot->fijar("tEnviar" , _("Buscar"));
	$ot->fijar("action", $action);	
				
	echo $ot->Output();												
}

function ListarProductosExtra(){	
  global $action,$tamPagina,$txtMoDet;
	$oProducto = new producto;

	$idprov 	= getSesionDato("FiltraProv");
	$idlab  	= getSesionDato("FiltraLab");
	$idalias  	= getSesionDato("FiltraAlias");
	$idmarca 	= getSesionDato("FiltraMarca");
	$idcolor 	= getSesionDato("FiltraColor");
	$idtalla 	= getSesionDato("FiltraTalla");
	$base 		= getSesionDato("FiltraBase");
	$idfamilia 	= getSesionDato("FiltraFamilia");
	$idsubfamilia 	= getSesionDato("FiltraSubFamilia");
	$ref 		= getSesionDato("FiltraReferencia");
	$cb 		= getSesionDato("FiltraCB");
	$nombre 	= getSesionDato("FiltraNombre");
	$idalias        = ($nombre) ? getLikeProductoAlias2Id($nombre, $IdIdioma=false):$idalias;
	$obsoletos      = getSesionDato("FiltraObsoletos");
	$indice 	= getSesionDato("PaginadorListaProd");
	$txtModelo      = $txtMoDet[1];
	$txtDetalle     = $txtMoDet[2];

	$hayProductos = $oProducto->ListadoFlexible($idprov,$idmarca,$idcolor,$idtalla,false,
						    $indice,$base,false,$idfamilia,$tamPagina,
						    $ref,$cb,$nombre,$obsoletos,$idalias,
						    $idlab,$idsubfamilia);

	$num = 0;
	
	$jsOut = "";
	$jsLex = new jsLextable;

	$jsOut .= jsLabel("color", $txtModelo);	
	$jsOut .= jsLabel("talla", $txtDetalle);
	$jsOut .= jsLabel("modificar",_("Modificar"));
	$jsOut .= jsLabel("referencia",_("Referencia"));
	$jsOut .= jsLabel("unid",_("Unid"));
	$jsOut .= jsLabel("pv",_("PV"));
	$jsOut .= jsLabel("nuevatallacolor",_("Nuevo $txtModelo / $txtDetalle"));
	
	$oldId = -1;
	$num =0;		
	while ($oProducto->SiguienteProducto()){
	  $num++;	
	  $id = $oProducto->getId();							
	  $nombre = $oProducto->getNombre();
	  $descripcion = $oProducto->get("Descripcion");
	  $marca = getIdMarca2Texto($oProducto->get("IdMarca"));
	  $ref = $oProducto->getReferencia();
	  $cb  = $oProducto->getCB();
	  $talla = getIdTalla2Texto( $oProducto->get("IdTalla"));
	  $color = getIdColor2Texto( $oProducto->get("IdColor"));
	  $lab = getNombreLaboratorio($oProducto->get("IdLabHab"));		
	  $lextalla = $jsLex->add($talla);
	  $lexcolor = $jsLex->add($color);

	  $fam = getIdFamilia2Texto( $oProducto->get("IdFamilia"));
	  $sub = getIdSubFamilia2Texto($oProducto->get("IdFamilia"), $oProducto->get("IdSubFamilia"));
	  
	  $manejaserie = $oProducto->get("Serie");
	  $manejalote  = $oProducto->get("Lote");
	  $manejafv    = $oProducto->get("FechaVencimiento");
	  $eservicio   = ( $oProducto->get("Servicio") > 0 )? 1:0;//Servicio
	  $esimagen    = "'".$oProducto->get("Imagen")."'";
	  $eservicio   = ( $oProducto->get("MetaProducto") > 0 )? 1:$eservicio;//MetaProducto
	  $lexfam      = $jsLex->add($fam);
	  $lexsub      = $jsLex->add($sub);
	  $idBase      = $oProducto->get("IdProdBase");
	  
	  if ($idBase != $oldId) {
	    $nombre = addslashes($nombre);
	    $ref    = addslashes($ref);

	    $jsOut .= "cPH($id,'$nombre','$ref',$lexfam,$lexsub,'$descripcion','$marca','$lab','$eservicio','$idBase');\n";
	  }
	  $jsOut .= "cP($id,$cb,$lextalla,$lexcolor,$idBase,$manejaserie,$manejalote,$manejafv,$eservicio,$esimagen);\n";
	  $oldId = $idBase;							
	}	
	
	$jsOut = $jsLex->jsDump() . $jsOut;
	
	$paginador = jsPaginador($indice,$tamPagina,$num);
	$jsOut .= $paginador;	
	$jsOut .= "cListProductos();";	
	$jsOut .= $paginador;
	$jsOut .= AutoOpen();
	echo jsBody($jsOut);
}

function ListarProductos($idprov,$idmarca,$idcolor,$idtalla,$base,$idfamilia) {
		//Creamos template
  global $action,$txtMoDet,$tamPagina;
	
	$ot = getTemplate("ListadoProductos");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
		
	$marcado = getSesionDato("CarritoProd");  
	
	//echo "ser: " . serialize($marcado). "<br>";
	
	//$tamPagina  = $ot->getPagina();
	
	$oProducto  = new producto;
	$indice     = getSesionDato("PaginadorListaProd");
	$txtModelo  = $txtMoDet[1];
	$txtDetalle = $txtMoDet[2];
	
	$hayProductos = $oProducto->ListadoFlexible($idprov,$idmarca,$idcolor,$idtalla,false,$indice,$base,false,$idfamilia,$tamPagina);
			
	
	$ot->fijar("comboProveedores",genComboProveedores($idprov));
	$ot->fijar("comboMarcas",genComboMarcas($idmarca));
		
			
	if (intval($idcolor) >=0)
		$ot->fijar("comboColores",genComboColores($idcolor));
	else
		$ot->fijar("comboColores",genComboColores("ninguno"));
			
	$ot->fijar("comboTalla",genComboTallas($idtalla));
		
	$ot->fijar("tVerTallasColores", _("$txtModelo/$txtDetalle"));	
	$ot->fijar("tBuscaCodigoBarras", _("CB"));

	$jsOut = "";
	$ot->fijar("tTitulo",_("Lista de productos"));
	$ot->fijar("action",$action);

	$jsOut .= jsLabel("eliminar",_("Eliminar"));
	$jsOut .= jsLabel("modificar",_("Modificar"));
	$jsOut .= jsLabel("nuevatallacolor",_("$txtModelo / $txtDetalle"));
	
	$jsOut .= jsLabel("local",_("Local"));
	$jsOut .= jsLabel("nombre",_("Nombre"));
	$jsOut .= jsLabel("referencia",_("Referencia"));
	$jsOut .= jsLabel("unid",_("Unid"));
	$jsOut .= jsLabel("pv",_("PV"));
	$jsOut .= jsLabel("seleccionar",_("Seleccionar"));
		
	if (!$hayProductos){
		echo gas("aviso",_("No hay productos disponibles"));	
	} else {				
		$num =0;		
		while ($oProducto->SiguienteProducto()){
			$num++;	
			$id = $oProducto->getId();							
			$nombre = $oProducto->getNombre();
			$referencia = $oProducto->getReferencia();
			$jsOut .= "cP($id,'$nombre','$referencia');\n";						
		}				
		$ot->paginador($indice,false,$num);
		$jsOut .= "cListProductos();";		
	}	
	

	$ot->fijar("CLIST", $jsOut);
	 	 
	echo $ot->Output();	
}

function MostrarProductoParaEdicion($id,$lang,$esPopup=false) {
	global $action;
	
	$oProducto = new producto;
	if (!$oProducto->Load($id,$lang)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oProducto->formEntrada($action,true,false,$esPopup);	
}

function MostrarProductoBarParaEdicion($id,$lang,$esPopup=false) {
	global $action;
	
	$oProducto = new producto;
	if (!$oProducto->Load($id,$lang)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oProducto->formEntradaBar($action,true,$esPopup);	
}

function MostrarProductoParaClonado($id,$idBase,$lang,$volver=false) {
	global $action;
	$_SESSION["IdUltimoCambioProductos"] = $idBase;			

	$oProducto = new producto;
	if (!$oProducto->Load($id,$lang)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	$oProducto->regeneraCB();

	echo $oProducto->formClon($action,true,$volver);	
}


function ModificarProductoFoto($id,$nuevaFoto,$ref=false){
	
	$nuevaFoto = CleanRealMysql($nuevaFoto);
	
	if($id){
		$id = CleanID($id);			
		$where = "IdProducto='$id'";
	} else {
		$ref = CleanRealMysql($ref);
		$where = " Referencia='$ref' ";	
	}
	$sql = "UPDATE ges_productos SET Imagen='$nuevaFoto' WHERE $where";
	query($sql);
}

function ModificarProductoBar($id,$newcodigobarras,$refprovhab,$idcontenedor,
			      $ventamenudeo,$unidxcont,$unidadmedida,$idcolor,
			      $idtalla,$nombre,$tieneserie,$tienelote,$fechav,
			      $idalias0,$idalias1,$condventa){

	$oProducto = new producto;
	if (!$oProducto->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	$unidxcont = ($ventamenudeo==1)?$unidxcont:0;
	
	$oProducto->set("CodigoBarras",$newcodigobarras,FORCE);
	$oProducto->set("RefProvHab",$refprovhab,FORCE);
	$oProducto->set("IdContenedor",$idcontenedor,FORCE);
	$oProducto->set("IdColor",$idcolor,FORCE);
	$oProducto->set("IdTalla",$idtalla,FORCE);
	$oProducto->set("VentaMenudeo",$ventamenudeo,FORCE);
        $oProducto->set("UnidadMedida",$unidadmedida,FORCE);
	$oProducto->set("CondicionVenta",$condventa,FORCE);
	$oProducto->set("UnidadesPorContenedor",$unidxcont,FORCE);
	$oProducto->set("Serie",$tieneserie,FORCE);
	$oProducto->set("Lote",$tienelote,FORCE);
	$oProducto->set("FechaVencimiento",$fechav);
	$oProducto->set("IdProductoAlias0",$idalias0,FORCE);
	$oProducto->set("IdProductoAlias1",$idalias1,FORCE);
	
	if (!$oProducto->AutoIntegridad()){
	  $error = $oProducto->getFallo();
	  
	  echo gas("aviso",_("[$error], intentelo de nuevo<!-- id:$id, color:$idcolor, talla:$idtalla -->"));
	  return false;
	}
	
	if ($oProducto->Modificacion() ){
	  $talla = resetIdTalla2Texto( $idtalla );
	  $color = resetIdColor2Texto( $idcolor );

	  echo gas("aviso",_("$newcodigobarras - $nombre $color $talla modificado"));	
	  return true;
	} else {
		echo gas("problema",_("No se puedo cambiar datos - $newcodigobarras"));
		return false;	
	}	
}

function ModificarProducto($id,$nombre,$referencia,$descripcion, $precioventa,
			   $precioonline, $idfamilia,$idsubfamilia,$coste,
			   $idprovhab,$idcolor,$idtalla,$codigobarras,$idmarca,
			   $refprovhab,$idalias0,$idalias1,$ns,$fv,$idlabhab,$mlote){

         $oProducto = new producto;
	 if (!$oProducto->Load($id)){
	   error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
	   return false;	
	 }
	 $oProducto->setDescripcion($descripcion);	 
	 $IdProdBase = $oProducto->get("IdProdBase");


	 if ($oProducto->Modificacion()){
	   echo gas("aviso",_(" $referencia - $descripcion modificado"));	

	   $xdato  = "IdProvHab     = ".$idprovhab;
	   $xdato .= ",IdLabHab     = ".$idlabhab;
	   $xdato .= ",IdMarca      = ".$idmarca;
	   $xdato .= ",IdFamilia    = ".$idfamilia;
	   $xdato .= ",IdSubFamilia = ".$idsubfamilia;
	   
	   $sql = 
	     " update ges_productos ".
	     " set   ".$xdato.
	     " where IdProdBase = ".$IdProdBase;
	   query($sql);
	   return true;	   

	 } else {
	   echo gas("problema",_("No se puedo cambiar datos de [$referencia]"));
	   return false;	
	 }	
}

function OperacionesConProductos(){
	
	if (!isUsuarioAdministradorWeb())
		return;
	
	echo gas("titulo",_("Operaciones sobre Productos"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo producto")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "<tr><td style='color:red'>Debug: vaciar productos y almacenes</td><td>".gModo("vaciarbasededatos",_("Eliminar todo"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;

	$oProducto = new producto;

	$oProducto->Crea();
	
	echo $oProducto->formEntrada($action,false);	
}



function PaginaBasica(){
	//AccionesSeleccion();
	/*
	$idprov = getSesionDato("FiltraProv");
	$idmarca = getSesionDato("FiltraMarca");
	$idcolor = getSesionDato("FiltraColor");
	$idtalla = getSesionDato("FiltraTalla");
	$base = getSesionDato("FiltraBase");
	$idfamilia = getSesionDato("FiltraFamilia");*/
	
	//ListarProductos($idprov,$idmarca,$idcolor,$idtalla,$base,$idfamilia);	
	ListarProductosExtra();
	//OperacionesConProductos();	
}

function BorrarProducto($id){
	$oProducto = new producto;	
	
	if ($oProducto->Load($id)) {		
		$nombre = $oProducto->getNombre();
		
		//if (isVerbose())
			echo gas("Aviso",_("Producto borrado"));
		
		$oProducto->EliminarProducto();		
		return true;
	}	else {
		echo gas("Aviso",_("No se ha podido borrar el producto"));	
		return false;
	}
}

function AgnadirCarritoProductos($id){
	$actual = getSesionDato("CarritoProd");
	if (!is_array($actual)) {		$actual = array();		}
	
	if (!in_array($id,$actual))	array_push($actual,$id);
		
	$_SESSION["CarritoProd"] = $actual;	
}

function ListarOpcionesSeleccion(){
	echo gas("titulo",_("Operaciones sobre la selección"));
	echo "<table border=1>";
	echo "<tr><td>"._("Hacer una compra a proveedores")."</td><td>".gModo("comprar",_("Comprar"))."</td></tr>";
	echo "<tr><td>"._("Buscar en el almacén")."</td><td>".gModo("transsel",_("Buscar"))."</td></tr>";
	//echo "<tr><td>"._("Cambio global de precio")."</td><td>".gModo("preciochange",_("Precios"))."</td></tr>";
	echo "</table>";	
}

function ConvertirSelProductos2Articulos(){
	//Busca estos productos en el almacen y los selecciona
	
	
	$carroprod = getSesionDato("CarritoProd");
	
	//Vamos a agnadir a la seleccion actual del carro de articulos
	$carroarticulos = getSesionDato("CarritoTrans");
	if (!is_array($carroarticulos))
		$carroarticulos = array();
						
	foreach ($carroprod as $IdProducto){		
		$res = Seleccion("Almacen","IdProducto='$IdProducto'");		
		if ($res){
			while($row=Row($res)){
				$id = $row["Id"];
				array_push($carroarticulos,$id);
			}	
		}
	}	
	setSesionDato("CarritoTrans",$carroarticulos);		
}



function FormularioDeCambiodePrecio(){
	global $action;
	
	$ot = getTemplate("CambioPreciosSeleccion");
	if (!$ot){	error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }	
	//TODO: el cambiar precios a un grupo de productos
	// puede ser opcional
}


//function VaciarDatosProductosyAlmacen(){
  //query("DELETE FROM ges_almacenes");
  //query("DELETE FROM ges_productos");
  //query("DELETE FROM ges_productos_idioma");
//}


function ClonarProducto($id,$idcolor,$idtalla,$referencia=false,$codigobarras,
			$refprovhab,$coste,$precioventa,$ventamenudeo,$unidadesxcontenedor,
			$unidadmedida,$idcontenedor,$referencia,$idalias0,$idalias1,
			$condventa,$tieneserie,$tienelote,$tienevence) {
	global $action;
			
	$oProducto = new producto;
	if (!$oProducto->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	$oProducto->set("IdColor",$idcolor,FORCE);
	$oProducto->set("IdTalla",$idtalla,FORCE);
	$oProducto->set("Referencia",$referencia,FORCE);
	$oProducto->set("CodigoBarras",$codigobarras,FORCE);
	$oProducto->set("RefProvHab",$refprovhab,FORCE);
	$oProducto->set("Costo",$coste,FORCE);
	$oProducto->setPrecioVenta($precioventa);
	$oProducto->set("IdContenedor",$idcontenedor,FORCE);
	$oProducto->set("UnidadMedida",$unidadmedida,FORCE);
	$oProducto->set("VentaMenudeo",$ventamenudeo,FORCE);
	$oProducto->set("CondicionVenta",$condventa,FORCE);
	$oProducto->set("UnidadesPorContenedor",$unidadesxcontenedor,FORCE);
	$oProducto->set("IdProductoAlias0",$idalias0,FORCE);
	$oProducto->set("IdProductoAlias1",$idalias1,FORCE);
	$oProducto->set("Lote",$tienelote,FORCE);
	$oProducto->set("FechaVencimiento",$tienevence,FORCE);
	$oProducto->set("Serie",$tieneserie,FORCE);
	//setSesionDato("ClonProd",var_export($oProducto,true));
	
	if ($oProducto->Clon()) {
		$alm = getSesionDato("Almacen");		
		$alm->ApilaProductoTodos($oProducto);
		return true;
	} else {
	  echo gas("aviso",_("No se pudo realizar la copia. <br/> - Reintente con diferentes caracteristicas Modelo ó Detalle -"));
	  $oProducto->regeneraCB();
	  echo $oProducto->formClon($action,true);
	}
		
	return false;			
}

//Validacion AJAX.
function ValidacionNuevoProducto($campo,$valor,&$fallo){
	$oProducto = new producto;
	if(!$campo)
		return false;
	
	switch($campo) {
		case "CodigoBarras":
			if (!$campo or strlen($campo)<3)
				return false;
		
			$oProducto->set("CodigoBarras",$valor,FORCE);
			$res =  $oProducto->IntegridadCodigoBarrasClon();
			if (!$res) {
				//$fallo = $oProducto->getFallo();
				return false;
			}								
			break;
		case "Nombre":
			if (!$campo or strlen($campo)<3)
				return false;

			$oProducto->set("Nombre",$valor,FORCE);
			$res = $oProducto->IntegridadNombre();															
			if (!$res) {
				//$fallo = $oProducto->getFallo();
				return false;
			}								
			break;
	}
	
	return true;
}


switch($modo) {

	case "valida":
		$campo 		= CleanTo($_GET["campo"]," ");
		$valor 		= CleanTo($_GET["valor"]," ");
		$idcampo 	= CleanTo($_GET["idcampo"]," ");
		$fallo = "";
		if (!ValidacionNuevoProducto($campo,$valor,$fallo)){			
			echo "document.getElementById('$idcampo').style.color='red';";	
		}	else 
			echo "document.getElementById('$idcampo').style.color='black';";
		exit();
		break;		
}

function QuitarFiltrosAvanzados() {
			setSesionDato("FiltraProv",false);
			setSesionDato("FiltraMarca",false);
			setSesionDato("FiltraColor",false);
			setSesionDato("FiltraTalla",false);
			setSesionDato("FiltraBase",false);
			setSesionDato("FiltraFamilia",false);		
			setSesionDato("FiltraReferencia",false);
			setSesionDato("FiltraLocal",false);
			setSesionDato("FiltraCB",false);
			setSesionDato("FiltraNombre",false);	
}

PageStart("Modproductos");

echo gas("cabecera",_("Gestión de Productos"));

switch($modo){
		case "buscarproductos":
		QuitarFiltrosAvanzados();
		
		setSesionDato("PaginadorListaProd",0);
	
		$referencia = CleanReferencia(GET("Referencia"));		
		$donde = CleanID(GET("IdLocal"));
		$cb = CleanCB(GET("CodigoBarras"));		
		$completas = (GET("verCompletas")=="on");		
		$nombre = CleanText(GET("Nombre"));
		$obsoletos = CleanID(GET("Obsoletos"));
		
		if (strlen($referencia)<1)
			$referencia = false;
		
		if (strlen($cb)<1)
			$cb = false;	
					
		setSesionDato("FiltraReferencia",$referencia);
		setSesionDato("FiltraCB",$cb);
		setSesionDato("FiltraNombre",$nombre);
		setSesionDato("FiltraObsoletos",$obsoletos);
		PaginaBasica();
		break;
		
		case "nomostrar":
			QuitarFiltrosAvanzados();	
			//PaginaBasica();
			break;
			
		case "mostrar":
		
		$reset = false;
		$id =  CleanID($_GET["IdProveedor"]);
		if ($id != getSesionDato("FiltraProv") ) {
			setSesionDato("FiltraProv",$id);
			$reset = true;
		}

		$id =  CleanID($_GET["IdLaboratorio"]);
		if ($id != getSesionDato("FiltraLab") ) {
			setSesionDato("FiltraLab",$id);
			$reset = true;
		}

		$id =  CleanID($_GET["IdAlias"]);			
		if ($id != getSesionDato("FiltraAlias")) {
			setSesionDato("FiltraAlias",$id);
			$reset = true;
		}
			
		$id =  CleanID($_GET["IdTalla"]);			
		if ($id != getSesionDato("FiltraTalla")) {
			setSesionDato("FiltraTalla",$id);
			$reset = true;
		}

		$id =  intval($_GET["IdColor"]);
		//echo q($id,"color leido");								
		if ($id != getSesionDato("FiltraColor")) {
			setSesionDato("FiltraColor",$id);
			$reset = true;			
			//echo q($id,"nuevo color");
		}
		
		$id =  CleanID($_GET["IdMarca"]);
		if ($id != getSesionDato("FiltraMarca")) {
			setSesionDato("FiltraMarca",$id);
			$reset = true;			
		}		

		$nombre =  CleanText($_GET["Nombre"]);
		if ($nombre != getSesionDato("FiltraNombre")) {
			setSesionDato("FiltraNombre",$nombre);
			$reset = true;			
		}				

		
		$id =  CleanID($_GET["IdFamilia"]);
		if ($id != getSesionDato("FiltraFamilia")) {
			setSesionDato("FiltraFamilia",$id);
			$reset = true;			
		}				

		$id =  CleanID($_GET["IdSubFamilia"]);
		if ($id != getSesionDato("FiltraSubFamilia")) {
			setSesionDato("FiltraSubFamilia",$id);
			$reset = true;			
		}				
				
		setSesionDato("FiltraBase",false);				
				
		if ($reset) {
			setSesionDato("PaginadorCompras",0);//Puede haber ahora muchos menos
		}						
				
		PaginaBasica();
		break;
		
	case "buscaporcb":
		$cb = CleanCB($_POST["CodigoBarras"]);
		if (!$cb)
			$cb = CleanCB($_GET["CodigoBarras"]);
		
		$completas = ($_POST["verCompletas"]=="on");
		
		if (!$completas)
			$completas = ($_GET["verCompletas"]=="on");
		
		$id = getIdFromCodigoBarras($cb);					
		
		if ($id) {
			if ($completas) {
				$base = getProdBaseFromId($id);		
				setSesionDato("FiltraBase",$base);	
				PaginaBasica();								
			} else {								
				setSesionDato("FiltraBase",false);		
				MostrarProductoParaEdicion($id,$lang);
			}
		} else {
			setSesionDato("FiltraBase",false);	
			PaginaBasica();
		}
		break;
	case "salvaclon":

		$id 	       = CleanID($_POST["id"]);
		$idalias0      = CleanID($_POST["IdProductoAlias0"]);
		$idalias1      = CleanID($_POST["IdProductoAlias1"]);
		$idcolor       = CleanID($_POST["IdColor"]);
		$idtalla       = CleanID($_POST["IdTalla"]);
		$condventa     = CleanText($_POST["CondicionVenta"]);
		$codigobarras  = CleanCB($_POST["CodigoBarras"]);
		$refprovhab    = CleanCB($_POST["RefProvHab"]);
		$referencia    = CleanReferencia($_POST["Referencia"]);
		$coste         = CleanFloat($_POST["Coste"]);
		$precioventa   = CleanFloat($_POST["PrecioVenta"]);
		$idcontenedor  = CleanID($_POST["IdContenedor"]);
		$tieneserie    = (isset($_POST["NumeroSerie"]))? CleanID($_POST["NumeroSerie"]=='on'):false;
		$tienelote     = (isset($_POST["Lote"]))? CleanID($_POST["Lote"]=='on'):false;
		$tienevence    = (isset($_POST["FechaVencimiento"]))? CleanID($_POST["FechaVencimiento"]== 'on'):false;
		$ventamenudeo  = (isset($_POST["VentaMenudeo"]))?CleanID($_POST["VentaMenudeo"]=='on'):false;
		$unidxcont     = CleanID($_POST["UnidadesPorContenedor"]);
		$unidadmedida  = CleanText($_POST["UnidadMedida"]);
		$txtModelo    = $txtMoDet[1];
		$txtDetalle   = $txtMoDet[2];

		if (ClonarProducto($id,$idcolor,$idtalla,false,$codigobarras,
				   $refprovhab,$coste,$precioventa,$ventamenudeo,
				   $unidxcont,$unidadmedida,$idcontenedor,
				   $referencia,$idalias0,$idalias1,$condventa,
				   $tieneserie,$tienelote,$tienevence)){
		  echo gas("aviso",_("Creado nuevo $txtModelo/$txtDetalle"));	
		  //Separador();
		  //$_SESSION["IdUltimoCambioProductos"] = $id;			
		  PaginaBasica();				
		}
		break;	
	case "clonar":
		$id     = CleanID( $_GET["id"] );
		$idBase = (isset($_SESSION["IdUltimoCambioProductos"]))? CleanID( $_SESSION["IdUltimoCambioProductos"] ):0;
		$idBase = (isset($_GET["idBase"]))? CleanID( $_GET["idBase"] ):$idBase;
		$volver = (isset($_GET["volver"]))? Clean($_GET["volver"]):"";
		MostrarProductoParaClonado($id,$idBase,false,$volver);
		break;
	case "vaciarbasededatos":
	        //VaciarDatosProductosyAlmacen();	
		echo gas("nota","Tablas de productos y almacen vaciadas");
		break;		
	case "preciochange":
		FormularioDeCambiodePrecio();	
		break;
	case "transsel": //Busca estos productos en el almacen y los selecciona
		ConvertirSelProductos2Articulos();
		echo "<script>\nlocation.href='modalmacenes.php?modo=seleccion';\n</script>"; 	
		break;		
	case "operaseleccion":			
		if ($_POST["borraseleccion"]){
			$_SESSION["CarritoProd"]=false;
			AccionesSeleccion();
			//echo gas("nota",_("Ya no hay seleccion"));	
			ListarProductos();		
			OperacionesConProductos();										
		} else {
			//la otra accion			
			ListarOpcionesSeleccion();			
			ListarProductos();		
			OperacionesConProductos();																	
		} 				
			
		break;		
	case "selec":
		$id = CleanID($_GET["id"]);
		AgnadirCarritoProductos($id);
		
		$marcado = getSesionDato("CarritoProd");  					
		//echo gas("nota",_("Producto seleccionado"));		

		PaginaBasica();							
			
		break;
	case "borrar":
		$id     = CleanID($_GET["id"]);
		$idBase = CleanID($_GET["idBase"]);
		$_SESSION["IdUltimoCambioProductos"] = $idBase;
		if (!productoEnAlmacen($id)){		
			BorrarProducto($id);
		} else {
			echo gas("aviso",_("No se puede borrar porque aun hay existencias. <br/> - Primero libere el stock en Almacen > Ajustes -") );
		} 				
		//Separador();
		PaginaBasica();	
		break;	

	case "newsave":		
		AltaDesdePostProducto();
		break;	
	case "alta":
		FormularioAlta();	
		break;
		
	case "modsavebar":
		$id   	        = CleanID($_POST["id"]);
		$idalias0       = CleanID($_POST["IdProductoAlias0"]);
		$idalias1       = CleanID($_POST["IdProductoAlias1"]);
		$codigobarras   = CleanCB($_POST["CodigoBarras"]);
		$refprovhab     = CleanCB($_POST["RefProvHab"]);
		$idcontenedor 	= CleanID($_POST["IdContenedor"]);
		$idcolor 	= CleanID($_POST["IdColor"]);
		$idtalla 	= CleanID($_POST["IdTalla"]);
		$condventa   	= CleanText($_POST["CondicionVenta"]);
		$nombre   	= (isset($_POST["Nombre"]))? CleanText($_POST["Nombre"]):'';
		$IdIdioma       = 1;

		$ventamenudeo = (isset($_POST["VentaMenudeo"]))? CleanID($_POST["VentaMenudeo"]=='on'):false;
		$tieneserie   = (isset($_POST["NumeroSerie"]))? CleanID($_POST["NumeroSerie"]=='on'):false;
		$tienelote    = (isset($_POST["Lote"]))? CleanID($_POST["Lote"]=='on'):false;
		$fechav       = (isset($_POST["FechaVencimiento"]))? CleanID($_POST["FechaVencimiento"]== 'on'):false;
		$unidxcont    = (isset($_POST["UnidadesPorContenedor"]))? CleanID($_POST["UnidadesPorContenedor"]):0;
		$unidadmedida = (isset($_POST["UnidadMedida"]))? CleanText($_POST["UnidadMedida"]):"und";

		//$_SESSION["IdUltimoCambioProductos"] = $id;

		if (function_exists("UploadFoto")) $nuevaFoto = UploadFoto();

		if ($nuevaFoto)	ModificarProductoFoto($id,$nuevaFoto);	

		if ( ModificarProductoBar($id,$codigobarras,$refprovhab,$idcontenedor,
					  $ventamenudeo,$unidxcont,$unidadmedida,$idcolor,
					  $idtalla,$nombre,$tieneserie,$tienelote,$fechav,
                                  $idalias0,$idalias1,$condventa) ) {

            if(getSesionDato('esPopup')){
                echo "<script>parent.cerrarVentanaEditProducto();</script>";
                return;
            }
            PaginaBasica();
        }
		else 
		  MostrarProductoBarParaEdicion($id,$lang);

	
		break;
	case "modsave":
		$id	= CleanID($_POST["id"]);
		
		$nombre   = CleanText($_POST["Nombre"]);
		$idalias0 = CleanID($_POST["IdProductoAlias0"]);
		$idalias1 = CleanID($_POST["IdProductoAlias1"]);

		if (getParametro("ProductosLatin1")){			
			//NOTA: si tenemos la tabla en latin1, la recepcion de nombre como utf8
			// requiere una conversion. Asi en memoria estara igual que terminara en la base de datos
			// y hay que hacer menos suposiciones en el codigo.
			//Desde luego seria mejor si no hubiera tablas en latin1, pero lamentablemente 
			// algunas tablas heredadas estan asi.
			
			$nombre = utf8iso($nombre);
		}				

		$tieneserie   = (isset($_POST["NumeroSerie"]))? CleanText($_POST["NumeroSerie"]):'off';
		$ns           = ($tieneserie == "on")? 1:0;
		$tienelote    = (isset($_POST["Lote"]))? CleanText($_POST["Lote"]):'off';
		$mlote        = ( $tienelote == "on" )? 1:0;
		$fechav       = (isset($_POST["FechaVencimiento"]))? CleanText($_POST["FechaVencimiento"]):false;
		$fv           = ( $fechav == "on" )? 1: 0;
		$referencia   = CleanReferencia($_POST["Referencia"]);
		$descripcion  = CleanCadena($_POST["Descripcion"]);
		$precioventa  = (isset($_POST["PrecioVenta"]))? CleanDinero($_POST["PrecioVenta"]):0;
		$precioonline = (isset($_POST["PrecioOnline"]))? CleanDinero($_POST["PrecioOnline"]):0;
		$idfamilia    = CleanID($_POST["IdFamilia"]);
		$idsubfamilia = CleanID($_POST["IdSubFamilia"]);
		$coste        = CleanDinero($_POST["CosteSinIVA"]);
		$idprovhab    = CleanID($_POST["IdProvHab"]);
		$idlabhab     = CleanID($_POST["IdLabHab"]);
		$idcolor      = (isset($_POST["IdColor"]))? CleanID($_POST["IdColor"]):1;
		$idtalla      = (isset($_POST["IdTalla"]))? CleanID($_POST["IdTalla"]):1;
		$codigobarras = CleanCB($_POST["CodigoBarras"]);
		$idmarca      = CleanID($_POST["IdMarca"]);
		$refprovhab   = CleanText($_POST["RefProvHab"]);
		$_SESSION["IdUltimoCambioProductos"] = $id;
		//$nuevaFoto    = UploadFoto();
		//if ($nuevaFoto)
		//ModificarProductoFoto(false,$nuevaFoto,$referencia);	

		
		if (ModificarProducto($id,$nombre,$referencia,$descripcion,$precioventa,
				      $precioonline, $idfamilia,$idsubfamilia,$coste,$idprovhab,
				      $idcolor,$idtalla,$codigobarras,$idmarca,$refprovhab,
				      $idalias0,$idalias1,$ns,$fv,$idlabhab,$mlote)){
		  //if(isVerbose())
		  //     echo gas("aviso","Producto modificado");
		  //Separador();
            
            if(getSesionDato('esPopup')){
                echo "<script>parent.cerrarVentanaEditProducto();</script>";
                return;
            }
		  
		  PaginaBasica();						
		} else {
		  MostrarProductoParaEdicion($id,$lang);
		}
		break;
		
	case "modsavebar2":
		$id	= CleanID($_POST["id"]);
		
		$nuevaFoto = UploadFoto();
		
		if ($nuevaFoto){
			ModificarProductoFoto($id,$nuevaFoto);	
			//echo gas("nota",_t("Foto modificada"));
				PaginaBasica();	
		}	else
			MostrarProductoBarParaEdicion($id,$lang);
			
		break;		
	case "editar":
		$id = CleanID($_GET["id"]);
		$idBase = CleanID($_GET["idBase"]);
        $esPopup = isset($_GET["espopup"])? true:false;
		$_SESSION["IdUltimoCambioProductos"] = $idBase;
        setSesionDato("esPopup",$esPopup);
        
		MostrarProductoParaEdicion($id,$lang,$esPopup);
		break;
	case "editarbar":
		$id     = CleanID($_GET["id"]);
		$idBase = CleanID($_GET["idBase"]);
        $esPopup = isset($_GET["espopup"])? true:false;
		$_SESSION["IdUltimoCambioProductos"] = $idBase;
        setSesionDato("esPopup",$esPopup);

		MostrarProductoBarParaEdicion($id,$lang,$esPopup);
		break;		
	case "pagmenos":
		$indice = getSesionDato("PaginadorListaProd");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorListaProd",$indice);
		PaginaBasica();
		break;	
	case "pagmas":
		$indice = getSesionDato("PaginadorListaProd");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorListaProd",$indice);
		PaginaBasica();
		break;			
	default:
		PaginaBasica();
		break;		
}

PageEnd();


?>
