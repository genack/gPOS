<?php

include("tool.php");
SimpleAutentificacionAutomatica("visual-iframe");

function ListarLocales() {
	global $action;
	
	$res = Seleccion( "Local","","IdLocal ASC","");
	
	if (!$res){
		echo gas("aviso","No hay locales disponibles");	
	} else{
		
		//echo gas("titulo",_("Lista de locales"));
		echo "<center>";
		echo "<table border=0 class=forma>";
		echo "<tr><td class='lh'>Local</td><td class='lh'></td><td class='lh'></td></tr>";
		while ($oLocal = LocalFactory($res) ){		
		
			$id = $oLocal->getId();
			//error("Info: id es '$id'");
		
			$nombre = $oLocal->getNombre();
			$linkEdicion = gAccion("editar",_("Modificar"),$id); 
			$linkborrado = gAccionConfirmada( "borrar", _("Eliminar") ,$id ,_("¿Seguro que quiere borrar?"));  
			echo "<tr class='f'><td class=nombre>$nombre</td><td>$linkEdicion</td><td>$linkborrado</td></tr>";					
		}
		echo "<tr class='f'><td></td><td></td><td></td></tr>";						       echo "<tr class='f'><td></td><td>".g('center',gAccion('alta',_('Nuevo local')))."</td><td></td></tr>";					
		echo "</table>";
		
		//TODO: debe ser relativo a un parametro: permitealtalocales
		//echo g("center",gAccion("alta",_("Nuevo local")));
	}
	
	userOperacionesConLocales();
	echo "</center>";	
}

function userOperacionesConLocales(){
	//OBSOLETO
}

function MostrarLocalParaEdicion($id) {
	global $action;
	
	$oLocal = new local;
	if (!$oLocal->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oLocal->formEntrada($action,true);	
}

function setAlmacenCentral($id){
	$id = CleanID($id);
	$sql = "UPDATE ges_locales SET AlmacenCentral = 0, CajaCentral = 0";
	query($sql);
	
	$sql = "UPDATE ges_locales SET AlmacenCentral = 1 WHERE IdLocal = '$id'";
	query($sql);
	
	$sql = "UPDATE ges_parametros SET AlmacenCentral = '$id'";
	query($sql);

	$sql = "UPDATE ges_locales SET CajaCentral = 0 WHERE IdLocal <> '$id'";
	query($sql);
	
	if(getSesionDato("IdTienda")==$id)
	  setSesionDato("esAlmacenCentral",1);
	else
	  setSesionDato("esAlmacenCentral",0);
}

function ModificarLocal($id,$nombre,
			$nombrelegal,$direccion,
			$poblacion,$codigopostal,
			$telefono,$fax,
			$movil, $email,
			$paginaweb,$cuentabancaria,
			$pass,$identificacion,
			$esCentral,$IdTipoNumeracionFactura,
			$ImpuestoIncluido,$idpais,
			$ididioma,$MensajeMes,
			$margen,$tipomargen,$igv,$ipc,
			$vigencia,$garantia,$nfiscal,$MensajePromo,
			$moneda0,$moneda0plural,$moneda0simbolo,
			$moneda1,$moneda1plural,$moneda1simbolo,$descuento,
			$metodoredondeo,$esCOPImpuesto,$cuentabancaria2,$esPass,
			$esCajaCentral){
	$oLocal = new local;
	if (!$oLocal->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}

	$oLocal->set("NombreComercial",$nombre,FORCE);
	$oLocal->set("NombreLegal",$nombrelegal,FORCE);
	$oLocal->set("DireccionFactura",$direccion,FORCE);
	$oLocal->set("NFiscal",$nfiscal,FORCE);
	$oLocal->set("Poblacion",$poblacion,FORCE);
	$oLocal->set("CodigoPostal",$codigopostal,FORCE);
	$oLocal->set("Telefono",$telefono,FORCE);
	$oLocal->set("Fax",$fax,FORCE);
	$oLocal->set("Movil",$movil,FORCE);
	$oLocal->set("Email",$email,FORCE);
	$oLocal->set("PaginaWeb",$paginaweb,FORCE);
	$oLocal->set("CuentaBancaria",$cuentabancaria,FORCE);
	if($pass != 'localess')
	  $oLocal->set("Password",md5($pass),FORCE);
	$oLocal->set("Identificacion",$identificacion,FORCE);
	$oLocal->set("IdTipoNumeracionFactura",$IdTipoNumeracionFactura,FORCE);
	$oLocal->set("ImpuestoIncluido",$ImpuestoIncluido,FORCE);
	$oLocal->set("IdPais",$idpais,FORCE);
	$oLocal->set("IdIdioma",$ididioma,FORCE);
	$oLocal->set("MensajeMes",$MensajeMes,FORCE);
	$oLocal->set("MensajePromocion",$MensajePromo,FORCE);
	$oLocal->set("VigenciaPresupuesto",$vigencia,FORCE);
	$oLocal->set("GarantiaComercial",$garantia,FORCE);
	$oLocal->set("MargenUtilidad",$margen,FORCE);
	$oLocal->set("TipoMargenUtilidad",$tipomargen,FORCE);
	$oLocal->set("Impuesto",$igv,FORCE);
	$oLocal->set("Percepcion",$ipc,FORCE);
	$oLocal->set("Descuento",$descuento,FORCE);
	$oLocal->set("MetodoRedondeo",$metodoredondeo,FORCE);
	$oLocal->set("COPImpuesto",$esCOPImpuesto,FORCE);
	$oLocal->set("CuentaBancaria2",$cuentabancaria2,FORCE);
	$oLocal->set("AdmitePassword",$esPass,FORCE);
	$oLocal->set("CajaCentral",$esCajaCentral,FORCE);

	if ($esCentral){
		setAlmacenCentral($id);	
		$oLocal->set("AlmacenCentral",1,FORCE);
		setMoneda($moneda0,$moneda0plural,$moneda0simbolo,
			  $moneda1,$moneda1plural,$moneda1simbolo);
	}
			
	if ($oLocal->Modificacion()){
	  //if(isVerbose())
	  echo gas("aviso",_("Local modificado, Reinicie sesión para aplicar cambios"));

	  //invalidarSesion("ListaTiendas");
	  //unset($_SESSION["tLOCAL_$id"]);
	  
	  $idlocalactivo = getSesionDato("IdTienda");

	  if($id==$idlocalactivo){
	    RegistrarIGVTienda($id);
	    RegistrarValuacionPrecioTPV($id);
	  }
	  return true;

	} else {

	  echo gas("problema",_("No se puedo cambiar dato"));	
	  return false;

	}	
}

function OperacionesConLocales(){
	if (!isUsuarioAdministradorWeb())
		return;
	
	
	echo gas("titulo",_("Operaciones sobre Locales"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear una nueva tienda")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta() {
	global $action;
	
	$oLocal = new local;

	$oLocal->Crea();
	
	echo $oLocal->formEntrada($action,false);	
}

function CrearLocal($nombre,$nombrelegal,$direccion,$poblacion,$codigopostal,
		    $telefono,$fax,$movil,$email,$paginaweb,$cuentabancaria,
		    $pass,$identificacion,$idpais,$idioma,$margen,$tipomargen,
		    $igv,$ipc,$esPass){
	$oLocal = new local;

	$oLocal->Crea();

	$oLocal->set("NombreComercial",$nombre,FORCE);
	$oLocal->set("NombreLegal",$nombrelegal,FORCE);
	$oLocal->set("DireccionFactura",$direccion,FORCE);
	$oLocal->set("Poblacion",$poblacion,FORCE);
	$oLocal->set("CodigoPostal",$codigopostal,FORCE);
	$oLocal->set("Telefono",$telefono,FORCE);
	$oLocal->set("Fax",$fax,FORCE);
	$oLocal->set("Movil",$movil,FORCE);
	$oLocal->set("Email",$email,FORCE);
	$oLocal->set("PaginaWeb",$paginaweb,FORCE);
	$oLocal->set("CuentaBancaria",$cuentabancaria,FORCE);
	$oLocal->set("Password",md5($pass),FORCE);
	$oLocal->set("Identificacion",$identificacion,FORCE);
	$oLocal->set("IdPais",$idpais,FORCE);
	$oLocal->set("IdIdioma",getIdFromLang("es"),FORCE);
	$oLocal->set("MargenUtilidad",$margen,FORCE);
	$oLocal->set("TipoMargenUtilidad",$tipomargen,FORCE);
	$oLocal->set("Impuesto",$igv,FORCE);
	$oLocal->set("Percepcion",$ipc,FORCE);
	$oLocal->set("AdmitePassword",$esPass,FORCE);

	$IdLocalCreado = $oLocal->Alta();
	if ($IdLocalCreado){
		invalidarSesion("ListaTiendas");
		$alm = new almacenes;
		$arrayTodos = array_keys($alm->listaTodosConNombre());
		
		$_SESSION["ArrayTiendas"] = $arrayTodos;
		
		//TODO: aqui tenemos una ligadura fuerte entre un modulo y la aplicación.
		// esto se debe automatizar para que la ligadura sea debil.		
		$oLocal->IniciarArqueos();		
		updateDashBoard($IdLocalCreado);
		return true;
	} else {
		//echo gas("aviso",_("No se ha podido registrar el nuevo local"));
		return false;
	}
}

function PaginaBasica(){
	ListarLocales();	
	OperacionesConLocales();	
}

function BorrarTienda($id){
	$oLocal = new local;	
	
	if ($oLocal->Load($id)) {		
		$nombre = $oLocal->getNombre();
		//echo gas("Aviso",_("Local eliminado"));
		
		$oLocal->MarcarEliminado();			
		invalidarSesion("ListaTiendas");	
		return true;
	}	else {
		//echo gas("Aviso",_("No se ha podido borrar el local"));	
		return false;
	}
}

PageStart();

//echo gas("cabecera",_("Gestion de Locales"));


switch($modo){
	case "newsave":
	        $nombre         = CleanText($_POST["NombreComercial"]);
		$nombrelegal    = CleanText($_POST["NombreLegal"]);
		$direccion      = CleanCadena($_POST["DireccionFactura"]);
		$poblacion      = CleanText($_POST["Poblacion"]);
		$codigopostal   = CleanCP($_POST["CodigoPostal"]);
		$telefono       = CleanText($_POST["Telefono"]);
		$fax            = CleanText($_POST["Fax"]);
		$movil          = CleanText($_POST["Movil"]);
		$email          = CleanEmail($_POST["Email"]);
		$paginaweb      = CleanUrl($_POST["PaginaWeb"]);
		$cuentabancaria = CleanCC($_POST["CuentaBancaria"]);		
		$identificacion = CleanText($_POST["Identificacion"]);		
		$idpais 	= CleanID($_POST["IdPais"]);	
		$ididioma 	= (isset($_POST["IdIdioma"]))? CleanID($_POST["IdIdioma"]):1;
		$pass           = CleanText($_POST["Password"]);
		$margen         = CleanText($_POST["MargenUtilidad"]);
		$tipomargen     = CleanText($_POST["TipoMargenUtilidad"]);
		$igv            = CleanText($_POST["IGV"]);
		$ipc            = CleanText($_POST["Percepcion"]);
		$esPass 	= (isset($_POST["ConContrasenia"]))? ($_POST["ConContrasenia"] =='on'):false;
		$esPass         = ($esPass)? 1:0;
		$existe         = verficarExistenciaLocal($identificacion,0);
		if($existe) return FormularioAlta();
		
		if($esPass)
		  if(strlen($pass) < 8) return FormularioAlta();

		$localpermitidos = obtenerLocalesPermitidos();
		$xlocales        = obtenrLocalesActivos();
		
		if($xlocales >= $localpermitidos && $localpermitidos != 0){
		  echo gas("aviso","A excedido cantidad de locales permitidos");	
		  return PaginaBasica();
		}
		
		CrearLocal($nombre,$nombrelegal,$direccion,$poblacion,$codigopostal,
			   $telefono,$fax,$movil,$email,$paginaweb,$cuentabancaria,
			   $pass,$identificacion,$idpais,$ididioma,$margen,$tipomargen,
			   $igv,$ipc,$esPass);
			
		PaginaBasica();	
		break;	
	case "alta":
		FormularioAlta();	
		break;
	case "modsave":
		$id 				= CleanID($_POST["id"]);
		$nombre 			= CleanText($_POST["NombreComercial"]);
		$nombrelegal 		        = CleanText($_POST["NombreLegal"]);
		$direccion 			= CleanCadena($_POST["DireccionFactura"]);
		$nfiscal                        = CleanText($_POST["NFiscal"]);
		$poblacion 			= CleanText($_POST["Poblacion"]);
		$codigopostal 		        = CleanCP($_POST["CodigoPostal"]);
		$telefono 			= CleanText($_POST["Telefono"]);
		$fax 				= CleanText($_POST["Fax"]);
		$movil 				= CleanText($_POST["Movil"]);
		$email 				= CleanEmail($_POST["Email"]);
		$paginaweb 			= CleanUrl($_POST["PaginaWeb"]);
		$cuentabancaria 	        = CleanText($_POST["IdCuentaBancaria"]);
		$pass 				= CleanTo($_POST["Password"]," ");
		$identificacion 	        = CleanText($_POST["Identificacion"]);
		$esCentral 			= (isset($_POST["esCentral"]))? ($_POST["esCentral"] =='on'):false;
		$IdTipoNumeracionFactura        = CleanID($_POST["IdTipoNumeracionFactura"]);
		$ImpuestoIncluido 	        = (isset($_POST["ImpuestoIncluido"]))? CleanID($_POST["ImpuestoIncluido"]=='on'):false;
		$idpais 			= CleanID($_POST["IdPais"]);
		$ididioma 			= (isset($_POST["IdIdioma"]))? CleanID($_POST["IdIdioma"]):1;
		$mensaje 			= CleanText($_POST["MensajeMes"]);
		$promocion 			= CleanText($_POST["MensajePromo"]);
		$vigencia 			= CleanInt($_POST["VigenciaPresupuesto"]);
		$garantia 			= CleanInt($_POST["GarantiaComercial"]);
		$margen				= CleanText($_POST["MargenUtilidad"]);
		$tipomargen			= CleanText($_POST["TipoMargenUtilidad"]);
		$igv				= CleanText($_POST["IGV"]);
		$ipc				= CleanText($_POST["Percepcion"]);
		$moneda0 			= CleanText($_POST["Moneda0"]);
		$moneda0simbolo			= CleanText($_POST["MonedaSimbolo0"]);
		$moneda0plural 			= CleanText($_POST["MonedaPlural0"]);	
		$moneda1 			= CleanText($_POST["Moneda1"]);
		$moneda1simbolo			= CleanText($_POST["MonedaSimbolo1"]);
		$moneda1plural 			= CleanText($_POST["MonedaPlural1"]);
		$descuento 			= CleanText($_POST["Descuento"]);
		$metodoredondeo			= CleanText($_POST["MetodoRedondeo"]);
		$esCOPImpuesto 			= (isset($_POST["esCOPImpuesto"]))? ($_POST["esCOPImpuesto"] =='on'):false;
		$esCOPImpuesto                  = ($esCOPImpuesto)? 1:0;
		$cuentabancaria2                = (isset($_POST["IdCuentaBancaria2"]))? CleanText($_POST["IdCuentaBancaria2"]):0;
		$esPass 	                = (isset($_POST["ConContrasenia"]))? ($_POST["ConContrasenia"] =='on'):false;
		$esPass                         = ($esPass)? 1:0;
		$existe = verficarExistenciaLocal($identificacion,$id);
		if($existe) return MostrarLocalParaEdicion($id);
		if($esPass)
		  if(strlen($pass) < 8) return MostrarLocalParaEdicion($id);

		$esCajaCentral 	                = (isset($_POST["CentralizaCaja"]))? ($_POST["CentralizaCaja"] =='on'):false;
		$esCajaCentral                  = ($esCajaCentral)? 1:0;

		if(!$esCentral)
		  $esCajaCentral = 0;

		ModificarLocal($id,$nombre,
			       $nombrelegal,$direccion,
			       $poblacion,$codigopostal,
			       $telefono,$fax,
			       $movil, $email,
			       $paginaweb,$cuentabancaria,
			       $pass,$identificacion,
			       $esCentral,$IdTipoNumeracionFactura,
			       $ImpuestoIncluido,$idpais,
			       $ididioma,$mensaje,
			       $margen,$tipomargen,$igv,$ipc,
			       $vigencia,$garantia,$nfiscal,$promocion,
			       $moneda0,$moneda0plural,$moneda0simbolo,
			       $moneda1,$moneda1plural,$moneda1simbolo,$descuento,
			       $metodoredondeo,$esCOPImpuesto,$cuentabancaria2,$esPass,
			       $esCajaCentral);
		PaginaBasica();	

		break;
	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarLocalParaEdicion($id);
		break;
	case "borrar":
		$id = CleanID($_GET["id"]);
		BorrarTienda($id);
		PaginaBasica();			
		break;
	default:
		ListarLocales();
		OperacionesConLocales();
		break;		
}

function getTiposComprobantes(){
  $sql ="SHOW COLUMNS FROM ges_comprobantestipo LIKE 'TipoComprobante'";
  $res=query($sql);
  $row = Row($res);
  return explode("','",preg_replace("/(enum|set)\('(.+?)'\)/", "$2", $row["Type"]));
}
function setTipoComprobantesLocal($IdLocal){
  $row=getTiposComprobantes();
  foreach ($row as $tc) {
    $sql="INSERT INTO `ges_comprobantestipo`  
          VALUES (NULL, '$tc', '$IdLocal', '$IdLocal', '0', '', '0')";
    query($sql,"Set Tipos Comprobantes");
  }
}

PageEnd();

?>
