<?php
include ("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 10;

function ListarSubsidiarios() {
	//Creamos template
	global $action, $tamPagina;

	$ot = getTemplate("ListadoSubsidiarios");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}

	$marcado = getSesionDato("CarritoProv");

        //echo "ser: " . serialize($marcado). "<br>";

	$oSubsidiario = new Subsidiario;

	$indice = getSesionDato("PaginadorSubsidiarios");

	$haySubsidiarios = $oSubsidiario->Listado(false, $indice);
	$ot->fijar("tAviso", _("¿Esta seguro de que quiere eliminarlo?"));

	if (!$haySubsidiarios) {
		echo gas("aviso", "No hay Service disponibles");
	} else {
		$ot->fijar("tTitulo", _("Lista de Service"));
		$ot->fijar("action", $action);
		$ot->resetSeries(array ("IdSubsidiario", "Referencia", "Nombre", "tBorrar", "tEditar", "tSeleccion", "marca"
			,"vNombreComercial"));
		$num = 0;
		while ($oSubsidiario->SiguienteSubsidiario()) {
			$num ++;
			$id = $oSubsidiario->getId();
			$ot->fijarSerie("IdSubsidiario", $id);
			$ot->fijarSerie("tBorrar", _("Eliminar"));
			$ot->fijarSerie("tEditar", _("Modificar"));
			$ot->fijarSerie("tNombreComercial",_("Nombre comercial"));
			$ot->fijarSerie("vNombreComercial",$oSubsidiario->get("NombreComercial"));
			//if (in_array($id, $marcado)) {
			$ot->fijarSerie("marca", "<abbr title='Seleccion' style='color:red'>S</abbr>");
			$ot->fijarSerie("tSeleccion", "");
			$ot->eliminaSeccion("s$num");
			//} else {
			//$ot->fijarSerie("marca", "");
			//$ot->fijarSerie("tSeleccion", _("Selección"));
			//}
		}

		$ot->paginador($indice, false, $num);

		$ot->terminaSerie(false);
		echo $ot->Output();
	}
}

function MostrarSubsidiarioParaEdicion($id, $lang) {
	global $action;

	$oSubsidiario = new Subsidiario;
	if (!$oSubsidiario->Load($id, $lang)) {
		error(__FILE__.__LINE__, "W: no pudo mostrareditar '$id'");
		return false;
	}

	$ot = getTemplate("ModificarSubsidiario");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("action", $action);
	$ot->fijar("vIdSubsidiario", $id);
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oSubsidiario->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oSubsidiario->get("IdModPagoHabitual")));
	
	$ot->campo(_("Pagina web"), "PaginaWeb", $oSubsidiario);
	$ot->fijar("comboIdPais" ,genComboPaises($oSubsidiario->get("IdPais")));
			
	$ot->fijar("tIdPais", _("País"));			
	$ot->fijar("tTitulo", _("Modificando subsidiario"));
	$ot->campo(_("Nombre comercial"), "NombreComercial", $oSubsidiario);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oSubsidiario);
	$ot->campo(_("Dirección"), "Direccion", $oSubsidiario);
	$ot->campo(_("Localidad"), "Localidad", $oSubsidiario);
	$ot->campo(_("Código postal"), "CP", $oSubsidiario);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oSubsidiario);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oSubsidiario);
	$ot->campo(_("Contacto"), "Contacto", $oSubsidiario);
	$ot->campo(_("Cargo"), "Cargo", $oSubsidiario);
	$ot->campo(_("Email"), "Email", $oSubsidiario);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oSubsidiario);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oSubsidiario);
	$ot->campo(_("Comentarios"), "Comentarios", $oSubsidiario);	
	

	echo $ot->Output();
}



function OperacionesConSubsidiarios() {
	if (!isUsuarioAdministradorWeb())
		return;
		
	echo gas("titulo", _("Operaciones sobre Subsidiario"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo Subsidiario")."</td><td>".gModo("alta", _("Alta"))."</td></tr>";
	echo "<tr><td style='color:red'>Debug: vaciar Subsidiario</td><td>".gModo("vaciarbasededatos", _("Eliminar todo"))."</td></tr>";
	echo "</table>";
}

function FormularioAlta($esPopup=false) {
	global $action;

	$oSubsidiario = new Subsidiario;

	$oSubsidiario->Crea();

	$ot = getTemplate("FormAltaSubsidiario");
	if (!$ot) {
		error(__FILE__.__LINE__, "Info: template no encontrado");
		return false;
	}
	$ot->fijar("action", $action);
	$ot->fijar("tTitulo", _("Alta Subsidiario"));	
	
	$ot->fijar("tModPagoHabitual", _("Modo pago hab."));
	$ot->fijar("vIdModPagoHabitual", $oSubsidiario->get("IdModPagoHabitual"));	
	$ot->fijar("comboModPagoHabitual", genComboModPagoHabitual( $oSubsidiario->get("IdModPagoHabitual")));
	
	$ot->campo(_("Pagina web"), "PaginaWeb", $oSubsidiario);
	
	$ot->fijar("tIdPais", _("País"));
	$ot->fijar("comboIdPais" ,genComboPaises($oSubsidiario->get("IdPais")));
		
	$ot->campo(_("Nombre comercial"), "NombreComercial", $oSubsidiario);
	$ot->campo(_("Nombre legal"), "NombreLegal", $oSubsidiario);
	$ot->campo(_("Dirección"), "Direccion", $oSubsidiario);
	$ot->campo(_("Localidad"), "Localidad", $oSubsidiario);
	$ot->campo(_("Código postal"), "CP", $oSubsidiario);
	$ot->campo(_("Telf.(1)"), "Telefono1", $oSubsidiario);
	$ot->campo(_("Telf.(2)"), "Telefono2", $oSubsidiario);
	$ot->campo(_("Contacto"), "Contacto", $oSubsidiario);
	$ot->campo(_("Cargo"), "Cargo", $oSubsidiario);
	$ot->campo(_("Email"), "Email", $oSubsidiario);
	$ot->campo(_("Cuenta bancaria"), "CuentaBancaria", $oSubsidiario);
	$ot->campo(_("Número fiscal"), "NumeroFiscal", $oSubsidiario);
	$ot->campo(_("Comentarios"), "Comentarios", $oSubsidiario);
	
	if ($esPopup) {
		$ot->fijar("vesPopup", 1);
		$ot->fijar("onClose", "window.close()");
	} else {
		$ot->fijar("vesPopup", 0);
		$ot->fijar("onClose", "location.href='modsubsidiarios.php'");	
	}

	echo $ot->Output();

}

function PaginaBasica() {
	//	AccionesSeleccion();
	ListarSubsidiarios();
	OperacionesConSubsidiarios();
}

function BorrarSubsidiario($id) {
	$oSubsidiario = new Subsidiario;

	if ($oSubsidiario->Load($id)) {
		//$nombre = $oSubsidiario->get("Nombre");
		echo gas("Aviso", _("Service borrado"));
		$oSubsidiario->MarcarEliminado();
	} else {
		echo gas("Aviso", _("No se ha podido borrar el Service"));
	}
}

function AgnadirCarritoSubsidiarios($id) {
	$actual = getSesionDato("CarritoProv");
	if (!is_array($actual)) {
		$actual = array ();
	}

	if (!in_array($id, $actual))
		array_push($actual, $id);

	$_SESSION["CarritoProv"] = $actual;
}

function ListarOpcionesSeleccion() {
	echo gas("titulo", _("Operaciones sobre la selección"));
	echo "<table border=1>";
	echo "<tr><td>"._("Hacer una compra a Service")."</td><td>".gModo("comprar", _("Comprar"))."</td></tr>";
	echo "<tr><td>"._("Buscar en el almacén")."</td><td>".gModo("transsel", _("Buscar"))."</td></tr>";
	//echo "<tr><td>"._("Cambio global de precio")."</td><td>".gModo("preciochange",_("Precios"))."</td></tr>";
	echo "</table>";
}

function ConvertirSelSubsidiarios2Articulos() {
}


function FormularioDeCambiodePrecio() {
}
function SubsidiarioEnAlmacen($id) {
}

function VaciarDatosSubsidiariosyAlmacen() {
	query("DELETE FROM ges_Subsidiarios");
}

function CrearSubsidiario($comercial, $legal, $direccion, $poblacion,
	 $cp, $email, $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero,
	 	 $comentario,$IdModPagoHabitual,$paginaweb,$idpais) {

	$oSubsidiario = new Subsidiario;
	$oSubsidiario->Crea();


	$oSubsidiario->set("IdPais", $idpais, FORCE);
	$oSubsidiario->set("PaginaWeb", $paginaweb, FORCE);

	$oSubsidiario->set("NombreComercial", $comercial, FORCE);
	$oSubsidiario->set("NombreLegal", $legal, FORCE);
	$oSubsidiario->set("Direccion", $direccion, FORCE);
	$oSubsidiario->set("Localidad", $poblacion, FORCE);
	$oSubsidiario->set("CP", $cp, FORCE);
	$oSubsidiario->set("Email", $email, FORCE);
	$oSubsidiario->set("Telefono1", $telefono1, FORCE);
	$oSubsidiario->set("Telefono2", $telefono2, FORCE);
	$oSubsidiario->set("Contacto", $contacto, FORCE);
	$oSubsidiario->set("Cargo", $cargo, FORCE);	
	$oSubsidiario->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oSubsidiario->set("NumeroFiscal", $numero, FORCE);
	$oSubsidiario->set("Comentarios", $comentario, FORCE);

	
	$oSubsidiario->set("IdModPagoHabitual", $IdModPagoHabitual, FORCE);

	if ($oSubsidiario->Alta()) {
		if(isVerbose())
			echo gas("aviso", _("Nuevo Service registrado"));
		return true;
	} else {
		if (isVerbose())
			echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}

}

function ModificarSubsidiario($id,$comercial, $legal, $direccion, $poblacion, $cp, $email, $telefono1, 
	$telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,	$IdModPagoHabitual,$paginaweb,$idpais	){
	$oSubsidiario = new Subsidiario;
	if (!$oSubsidiario->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	$oSubsidiario->set("IdPais", $idpais, FORCE);
	$oSubsidiario->set("PaginaWeb", $paginaweb, FORCE);
	
	$oSubsidiario->set("NombreComercial", $comercial, FORCE);
	$oSubsidiario->set("NombreLegal", $legal, FORCE);
	$oSubsidiario->set("Direccion", $direccion, FORCE);
	$oSubsidiario->set("Localidad", $poblacion, FORCE);
	$oSubsidiario->set("CP", $cp, FORCE);
	$oSubsidiario->set("Email", $email, FORCE);
	$oSubsidiario->set("Telefono1", $telefono1, FORCE);
	$oSubsidiario->set("Telefono2", $telefono2, FORCE);
	$oSubsidiario->set("Contacto", $contacto, FORCE);
	$oSubsidiario->set("Cargo", $cargo, FORCE);	
	$oSubsidiario->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oSubsidiario->set("NumeroFiscal", $numero, FORCE);
	$oSubsidiario->set("Comentarios", $comentario, FORCE);
	
	if($IdModPagoHabitual)
		$oSubsidiario->set("IdModPagoHabitual", $IdModPagoHabitual, FORCE);

	
	if ($oSubsidiario->Modificacion() ){
		if(isVerbose())
			echo gas("aviso",_("Service modificado"));	
	} else {
		echo gas("problema",_("No se puede cambiar datos de [$comercial]"));	
	}	
}


PageStart();

//echo gas("cabecera", _("Gestion de Service"));
$esPopup = false;

switch ($modo) {
	case "borrar":

		$Id = CleanID($_GET["Id"]);
		BorrarSubsidiario($Id);
		PaginaBasica();
		break;
	case "modsubsidiario":
		$id         = CleanID($_POST["IdSubsidiario"]);
		$comercial  = CleanText($_POST["NombreComercial"]);
		$legal      = CleanText($_POST["NombreLegal"]);
		$direccion  = CleanText($_POST["Direccion"]);
		$poblacion  = CleanText($_POST["Localidad"]);
		$cp         = CleanCP($_POST["CP"]);
		$email      = CleanEmail($_POST["Email"]);
		$telefono1  = CleanTelefono($_POST["Telefono1"]);
		$telefono2  = CleanTelefono($_POST["Telefono2"]);
		$contacto   = CleanText($_POST["Contacto"]);
		$cargo      = CleanText($_POST["Cargo"]);
		$cuentabancaria = CleanCC($_POST["CuentaBancaria"]);
		$numero     = CleanText($_POST["NumeroFiscal"]);
		$comentario = CleanText($_POST["Comentarios"]);
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$paginaweb  = CleanUrl($_POST["PaginaWeb"]);
		$idpais     = CleanID($_POST["IdPais"]);
		
		ModificarSubsidiario($id,$comercial, $legal, $direccion, $poblacion, $cp, $email,
			 $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,
			 	$IdModPagoHabitual,$paginaweb,$idpais);
		//Separador();
		PaginaBasica();
		break;
	case "editar":	
		$id   = CleanID($_GET["Id"]);
		$lang = getSesionDato("IdLenguajeDefecto");
		MostrarSubsidiarioParaEdicion($id,$lang);		
		break;
	case "newsubsidiario" :
	        $comercial  = CleanText($_POST["NombreComercial"]);
		$legal      = CleanText($_POST["NombreLegal"]);
		$direccion  = CleanText($_POST["Direccion"]);
		$poblacion  = CleanText($_POST["Localidad"]);
		$cp         = CleanCP($_POST["CP"]);
		$email      = CleanEmail($_POST["Email"]);
		$telefono1  = CleanTelefono($_POST["Telefono1"]);
		$telefono2  = CleanTelefono($_POST["Telefono2"]);
		$contacto   = CleanText($_POST["Contacto"]);
		$cargo      = CleanText($_POST["Cargo"]);
		$cuentabancaria = CleanText($_POST["CuentaBancaria"]);
		$numero     = CleanText($_POST["NumeroFiscal"]);
		$comentario = CleanText($_POST["Comentarios"]);
		$IdModPagoHabitual = CleanID($_POST["IdModPagoHabitual"]);
		$paginaweb  = (isset($_POST["PaginaWeb"]))? CleanUrl($_POST["PaginaWeb"]):'';
		$idpais     = (isset($_POST["IdPais"]))? CleanID($_POST["IdPais"]):1;		
		$espopup    = $_POST["esPopup"];
				
		if (CrearSubsidiario($comercial, $legal, $direccion, $poblacion, $cp, $email, $telefono1, $telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,$IdModPagoHabitual,$paginaweb,$idpais )) {
			if ($espopup){
				echo "<script>window.close()</script>";
				exit();				
			}
			//Separador();
			PaginaBasica();						
		} else {										
			FormularioAlta($espopup);
		}		
		break;
	case "altapopup":
	        $esPopup = true;
	case "alta" :
		FormularioAlta($esPopup);
		break;
	case "listar" :
		PaginaBasica();
		break;
	case "pagmenos":
		$indice = getSesionDato("PaginadorSubsidiarios");
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato("PaginadorSubsidiarios",$indice);
		PaginaBasica();
		break;	
	case "pagmas":
		$indice = getSesionDato("PaginadorSubsidiarios");
		$indice = $indice + $tamPagina;
		setSesionDato("PaginadorSubsidiarios",$indice);
		PaginaBasica();
		break;			
	default :
		PaginaBasica();
		break;
}

PageEnd();
?>
