<?php

include("tool.php");

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 20;

function AccionesSeleccion(){
									
}

function ListarFamilias() {
		//Creamos template
	global $action,$tamPagina;
	
	$ot = getTemplate("ListadoFamilias");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
	$tamPagina = $ot->getPagina();		
	$marcado = getSesionDato("CarritoFam");  
	
	//echo "ser: " . serialize($marcado). "<br>";
	
	$oFamilia = new familia;
	
	$indice = getSesionDato("PaginadorListaFam");
	
	$hayFamilias = $oFamilia->Listado(false,$indice);
	
	if (!$hayFamilias){
		echo gas("aviso","No hay familias disponibles");	
	} else{
		$ot->fijar("tTitulo",_("Lista de familias"));
		$ot->fijar("action",$action);
				
		$ot->resetSeries(array("IdFamilia","Referencia","Nombre","tBorrar","tEditar",
			"tSeleccion","marca","tListaSub","tCreaSub"));
		$num = 0;		
		while ($oFamilia->SiguienteFamilia()){	
			$id = $oFamilia->getId();
			$num ++;					
			$ot->fijarSerie("Id",$oFamilia->get("IdFamilia"));			
			$ot->fijarSerie("tBorrar",_("Eliminar"));
			$ot->fijarSerie("tEditar",_("Modificar"));
			$ot->fijarSerie("tCreaSub",_("Crear subfamilia"));
			$ot->fijarSerie("tListaSub",_("Subfamilia"));
		//	$ot->fijarSerie("IdPadre",$oFamilia->get("IdPadre"));
			$ot->fijarSerie("Nombre",$oFamilia->getNombre());		
							
			if (is_array($marcado) and in_array($id,$marcado)){
				$ot->fijarSerie("marca","<abbr title='Seleccion' style='color:red'>&lArr;</abbr>");
				$ot->fijarSerie("tSeleccion","");	
			} else {
				$ot->fijarSerie("marca","");
				$ot->fijarSerie("tSeleccion",_("Selección"));
			}						
		}		
		
		$ot->paginador($indice,false,$num);
		
		$ot->terminaSerie(false);
		echo $ot->Output();				
	}
}


function ListarSubFamilias($IdPadre = 0) {
		//Creamos template
	global $action,$tamPagina;
	
	$ot = getTemplate("ListadoSubFamilias");
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template no encontrado");
		return false; }
		
	//$marcado = getSesionDato("CarritoFam");  
		
	$oFamilia = new familia;
	
	$indice = getSesionDato("PaginadorListaSubFam");
	
	$hayFamilias = $oFamilia->ListadoSub(false,$indice,$IdPadre);

	$oFam = new familia;
	$oFam->Load($IdPadre);
	$NomFamilia = $oFam->get("Familia");

	if (!$hayFamilias){
		echo gas("aviso","No hay familias disponibles");	
	} else{
		$ot->fijar("tTitulo",_("Lista de subfamilias"));
		$ot->fijar("action",$action);
		$ot->fijar("vFamilia",$NomFamilia);
		
		$ot->fijar("id",$IdPadre);
		$ot->resetSeries(array("IdFamilia","Referencia","Nombre","tBorrar","tEditar",
			"tSeleccion","marca","tListaSub","tCreaSub"));
		$num = 0;		
		while ($oFamilia->SiguienteFamilia()){	
			$id = $oFamilia->getId();
			$num ++;				
			$ot->fijarSerie("Id",$id);	
			$ot->fijarSerie("tBorrar",_("Eliminar"));
			$ot->fijarSerie("tEditar",_("Modificar"));
			$ot->fijarSerie("tCreaSub",_("Crear subfamilia"));
			$ot->fijarSerie("tListaSub",_("Subfamilia"));
			$ot->fijarSerie("IdFamilia",$oFamilia->get("IdFamilia"));
			$ot->fijarSerie("Nombre",$oFamilia->get("SubFamilia"));
		}		
		
		$ot->paginador($indice,false,$num);
		
		$ot->terminaSerie(false);
		echo $ot->Output();				
	}
}


function MostrarFamiliaParaEdicion($id,$lang=false) {
	global $action;
	
	if (!$lang)
		$lang = getSesionDato("IdLenguajeDefecto");
	
	$oFamilia = new familia;
	if (!$oFamilia->Load($id,$lang)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oFamilia->formEntrada($action,true);	
}

function MostrarSubFamiliaParaEdicion($id,$mud,$muc,$dsto,$tipocosto,$lang=false) {
	global $action;
	
	if (!$lang)
		$lang = getSesionDato("IdLenguajeDefecto");
	
	$oFamilia = new familia;
	
	if (!$oFamilia->LoadSub($id,$lang)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	echo $oFamilia->formModificarSubfamilia($action,$mud,$muc,$dsto,$tipocosto);	
}

function ModificarFamilia($id,$nombre){
	$oFamilia = new familia;
	if (!$oFamilia->Load($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	//error( __FILE__ . __LINE__ ,"Info: s1 ". serialize($oFamilia));
	
	$oFamilia->setNombre($nombre);

	
	if ($oFamilia->Modificacion() ){
		return false;
	} else {
		//echo gas("problema",_("No se puede cambiar datos de [$referencia]"));	
		return false;
	}	
}

function ModificarSubFamilia($id,$nombre,$margenvd,$margenvc,$descuento){
	$oFamilia = new familia;
	if (!$oFamilia->LoadSub($id)){
		error(__FILE__ . __LINE__ ,"W: no pudo mostrareditar '$id'");
		return false;	
	}
	
	//error( __FILE__ . __LINE__ ,"Info: s1 ". serialize($oFamilia));
	
	$oFamilia->set("SubFamilia",$nombre);
	$oFamilia->set("MargenUtilidadVD",$margenvd);
	$oFamilia->set("MargenUtilidadVC",$margenvc);
	$oFamilia->set("Descuento",$descuento);
	
	if ($oFamilia->ModificacionSubfamilia() ){
		return true;
	} else {
		//echo gas("problema",_("No se puede cambiar datos de [$nombre]"));	
		return false;
	}	
}


function OperacionesConFamilias(){
	if (!isUsuarioAdministradorWeb())
		return;
	
	
	echo gas("titulo",_("Operaciones sobre Familias"));
	echo "<table border=1>";
	echo "<tr><td>"._("Crear un nuevo familia")."</td><td>".gModo("alta",_("Alta"))."</td></tr>";
	echo "<tr><td style='color:red'>Debug: vaciar familias</td><td>".gModo("vaciarbasededatos",_("Eliminar todo"))."</td></tr>";
		
	echo "</table>";
}


//FormularioSub

function FormularioAlta() {
	global $action;

	$oFamilia = new familia;
	$oFamilia->Crea();
			
	echo $oFamilia->formAlta($action,false);	
}

function FormularioAltaSubfamilia($padre=0) {
	global $action;

	$oFamilia = new familia;
	$oFamilia->Crea();
	$oFamilia->set("IdFamilia",$padre,FORCE);
			
	echo $oFamilia->formAltaSubfamilia($action,$padre);	
}




function PaginaBasica(){
		
        $padre = (isset($_SESSION["PaginaPadre"]))? intval($_SESSION["PaginaPadre"]):'';
	AccionesSeleccion();
	ListarFamilias();	
	OperacionesConFamilias();	
}

function PaginaBasicaSubfamilia($padre=0){
			
	AccionesSeleccion();
	ListarSubFamilias($padre);	
	OperacionesConFamilias();	
}


function BorrarFamilia($id){
	$oFamilia = new familia;	
	
	$id = CleanID($id);
	
	if ($oFamilia->Load($id)) {		
		$nombre = $oFamilia->getNombre();
		echo gas("Aviso",_("Familia $nombre borrado"));
		
		$oFamilia->EliminarFamilia();		
	}	else {
		echo gas("Aviso",_("No se ha podido borrar la familia"));	
	}
}

function BorrarSubFamilia($id){
	$oFamilia = new familia;	
	
	if ($oFamilia->LoadSub($id)) {		
		$nombre = $oFamilia->get("SubFamilia");
		//echo gas("Aviso",_("Sub familia $nombre borrado"));
				
		$oFamilia->MarcarEliminado();		
		return true;
	}	else {
		//	echo gas("Aviso",_("No se ha podido borrar el familia"));	
		return false;
	}
}

function AgnadirCarritoFamilias($id){
	return;
	/*
	$actual = getSesionDato("CarritoFam");
	if (!is_array($actual)){
		$actual = array();	
	}
	
	if (!in_array($id,$actual))
		array_push($actual,$id);
		
	$_SESSION["CarritoFam"] = $actual;	
	*/
}

function ListarOpcionesSeleccion(){
	echo "obsoleto";		
}

function ConvertirSelFamilias2Articulos(){
	//Busca estos familias en el almacen y los selecciona
	
	
	$carroprod = getSesionDato("CarritoFam");
	
	//Vamos a agnadir a la seleccion actual del carro de articulos
	$carroarticulos = getSesionDato("CarritoTrans");
	if (!is_array($carroarticulos))
		$carroarticulos = array();
						
	foreach ($carroprod as $IdFamilia){		
		$res = Seleccion("Almacen","IdFamilia='$IdFamilia'");		
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
	echo "obsoleto";
}

function familiaEnAlmacen($id) {
	return false;		
}

function VaciarDatosFamiliasyAlmacen(){
	query("DELETE FROM ges_familias");
	query("DELETE FROM ges_subfamilias");
}

function ListaProductosxSubFamilia($IdFamilia,$IdSubFamilia,$mud,$muc,$dsto,$tipocosto){

	//echo "::".$IdFamilia."::".$IdSubFamilia."::";
        $COPImpuesto = getSesionDato("COPImpuesto");
	$res = obtenerProductoxSubFamilia($IdFamilia,$IdSubFamilia);
	$xout = "<table class='forma' width='750'>
                 <tbody><tr><td><table></table></td></tr>
                 <tr class='formaCabezaLista'>
                 <td colspan=4>Productos</td>
                 <td colspan=4 >Precios Anteriores</td>
                 <td colspan=4 >Nuevos Precios</td>
                 </tr>
                 <tr class='formaCabezaLista' style='font-size:1em;'>
                 <td ></td>
                 <td >Ref</td>
                 <td >CB</td>
                 <td style='min-width:8em'>Nombre</td>
                 <td >PVP</td>
                 <td >PVP/D</td>
                 <td >PVC</td>
                 <td >PVC/D</td>
                 <td >PVP</td>
                 <td >PVP/D</td>
                 <td >PVC</td>
                 <td >PVC/D</td>
                 </tr>";
	
	while($row=Row($res)){

	  $Costo = ($tipocosto == 'CP')? $row["CostoPromedio"]:$row["UltimoCosto"];
	  $COP       = ($COPImpuesto == 1)? 0:$row["CostoOperativo"];

	  $MUD_Nuevo   = ($Costo+$COP)*$mud/100;
	  $PD_Nuevo    = $Costo+$COP+$MUD_Nuevo;
	  $IGV_Directo = $PD_Nuevo*$row["Impuesto"]/100;
	  $PVD_Nuevo   = ($COPImpuesto == 1)? $PD_Nuevo+$IGV_Directo+$row["CostoOperativo"]:$PD_Nuevo+$IGV_Directo;
	  $PVD_Nuevo  = ($mud != 0)? $PVD_Nuevo : $row["PrecioVenta"];
	  $PVD_Nuevo  = FormatPreciosTPV($PVD_Nuevo);
	  $PVDD_Nuevo = $PVD_Nuevo-($MUD_Nuevo*$dsto/100);
	  $PVDD_Nuevo = ($mud != 0)? $PVDD_Nuevo:$row["PVDDescontado"];
	  $PVDD_Nuevo = FormatPreciosTPV($PVDD_Nuevo);

	  $MUC_Nuevo = ($Costo+$COP)*$muc/100;
	  $PC_Nuevo  = $Costo+$COP+$MUC_Nuevo;
	  $IGV_Corporativo = $PC_Nuevo*$row["Impuesto"]/100;
	  $PVC_Nuevo = ($COPImpuesto == 1)? $PC_Nuevo+$IGV_Corporativo+$row["CostoOperativo"]:$PC_Nuevo+$IGV_Corporativo;

	  $PVC_Nuevo  = ($muc != 0)? $PVC_Nuevo:$row["PrecioVentaCorporativo"];
	  $PVC_Nuevo  = FormatPreciosTPV($PVC_Nuevo);
	  $PVCD_Nuevo = $PVC_Nuevo-$MUC_Nuevo*$dsto/100;
	  $PVCD_Nuevo = ($muc != 0)? $PVCD_Nuevo:$row["PVCDescontado"];
	  $PVCD_Nuevo = FormatPreciosTPV($PVCD_Nuevo);

	  $xout .= "<tr class='t f'><td width='16' class='iconproducto'><img src='img/gpos_productos.png'></td>".
	    "<td class='referencia'>".$row["Referencia"]."</td>".
	    "<td class='codigobarras'>".$row["CodigoBarras"]."</td>".
	    "<td class='nombre'>".$row["Producto"]."</td>".
	    "<td >".$row["PrecioVenta"]."</td>".
	    "<td >".$row["PVDDescontado"]."</td>".
	    "<td >".$row["PrecioVentaCorporativo"]."</td>".
	    "<td >".$row["PVCDescontado"]."</td>".
	    "<td >".$PVD_Nuevo."</td>".
	    "<td >".$PVDD_Nuevo."</td>".
	    "<td >".$PVC_Nuevo."</td>".
	    "<td >".$PVCD_Nuevo."</td></tr>";

	  //$xout .=  "<br>".$Costo;
	}

	$xout .= "</tbody></table>";
	return $xout;
}


PageStart();

//echo gas("cabecera",_("Gestion de Familias"));


switch($modo){
	
	case "vaciarbasededatos":
		VaciarDatosFamiliasyAlmacen();	
		echo gas("nota","Tablas de familias y almacen vaciadas");
		break;		
	case "borrar":
		$id = CleanID($_GET["id"]);			
		BorrarFamilia($id);
		PaginaBasica();	
		break;	
	case "borrasubfamiliar":
	case "borrarsubfamilia":
		$id = CleanID($_GET["id"]);			
		BorrarSubFamilia($id);
		$padre = getSesionDato("SubFamiliaDeFamilia");
		PaginaBasicaSubfamilia($padre);		
		break;	
	case "newfamilia":		
		$nombre = CleanTo($_POST["Nombre"]," ");					
		CrearFamilia($nombre);								
		//Separador();
		PaginaBasica();	
		break;	
	case "newsubfamilia":		
		//$padre = CleanID($_POST["id"]);
		$padre     = getSesionDato("SubFamiliaDeFamilia");
		$nombre    = CleanText($_POST["Nombre"]);
		$margenvd  = CleanFloat($_POST["MargenUtilidadVD"]);
		$margenvc  = CleanFloat($_POST["MargenUtilidadVC"]);
		$descuento = CleanFloat($_POST["Descuento"]);

		CrearSubFamilia($nombre,$margenvd,$margenvc,$descuento,$padre);
		//Separador();
		
		setSesionDato("PaginadoActivoFamilia","PaginadorListaSubFam");
		//setSesionDato("PaginadorListaSubFam",0);//inicio de pagina de subfamilias
		$padre = getSesionDato("SubFamiliaDeFamilia");
		PaginaBasicaSubfamilia($padre);
		
		break;	
	case "alta":
		FormularioAlta();	
		break;
	case "altasubfamilia":
		$padre  = CleanID($_GET["IdFamilia"]);
		FormularioAltaSubfamilia($padre);	
		break;		
	case "modfam":
		$id 	= CleanID($_POST["id"]);
		$nombre = CleanText($_POST["Familia"]);			
		ModificarFamilia($id,$nombre	);
		PaginaBasica();	
		break;
	case "modsubfamilia":
		$id 	   = CleanID($_POST["id"]);
		$nombre    = CleanText($_POST["SubFamilia"]);			
		$margenvd  = CleanFloat($_POST["MargenUtilidadVD"]);
		$margenvc  = CleanFloat($_POST["MargenUtilidadVC"]);
		$descuento = CleanFloat($_POST["Descuento"]);
		ModificarSubFamilia($id,$nombre,$margenvd,$margenvc,$descuento);

		// Recula precio
		$nuevo = isset($_POST["MargenUtilidad"]);
		$padre = getSesionDato("SubFamiliaDeFamilia");

		if($nuevo){
		  $tipocosto = CleanText($_POST["menu"]);
		  if($margenvd != 0 || $margenvc != 0 || $descuento != 0)
		    actualizarPreciosVentaAlmacenxSubFamilia($id,$margenvd,$margenvc,$tipocosto,$descuento);
		}


		PaginaBasicaSubfamilia($padre);		
		break;
	case "editar":
		$id = CleanID($_GET["id"]);
		MostrarFamiliaParaEdicion($id);
		break;
	case "editarsubfamilia":
	        $MUD   = isset($_GET["mud"])? CleanText($_GET["mud"]):'MUD';
		$MUC   = isset($_GET["muc"])? CleanText($_GET["muc"]):'MUC';
		$DSTO  = isset($_GET["dsto"])? CleanText($_GET["dsto"]):'DSTO';
		$TipoCosto = isset($_GET["tipocosto"])? CleanText($_GET["tipocosto"]):'CP';

		$id = CleanID($_GET["id"]);
		setSesionDato("IdSubFamilia",$id);
		MostrarSubFamiliaParaEdicion($id,$MUD,$MUC,$DSTO,$TipoCosto);
		break;
	case "pagmenos":
		$paginador = getSesionDato("PaginadoActivoFamilia");
		$indice = getSesionDato($paginador);
		$indice = $indice - $tamPagina;
		if ($indice<0)
			$indice = 0;
		setSesionDato($paginador,$indice);
		if ($paginador !="PaginadorListaSubFam")
			PaginaBasica();			//Navegando familia, 
		else {
			$padre = getSesionDato("SubFamiliaDeFamilia");
			PaginaBasicaSubfamilia($padre); //Navegando subfamilia
		}
			
		//echo "usando paginador $paginador<br>";
		break;	
	case "pagmas":
		$paginador = getSesionDato("PaginadoActivoFamilia");
		$indice = getSesionDato($paginador);
		$indice = $indice + $tamPagina;
		setSesionDato($paginador,$indice);
				if ($paginador !="PaginadorListaSubFam")
			PaginaBasica();			//Navegando familia, 
		else {
			$padre = getSesionDato("SubFamiliaDeFamilia");
			PaginaBasicaSubfamilia($padre); //Navegando subfamilia
		}
		//echo "usando paginador $paginador<br>";
		break;	
	default:	
	case "lista":
		setSesionDato("PaginadoActivoFamilia","PaginadorListaFam");
		PaginaBasica();
		//echo "usando paginado " . getSesionDato("PaginadoActivoFamilia") . "<br>";
		break;
	case "iniciarListaSubfamilia":		
		setSesionDato("PaginadorListaSubFam",0);
		$padre = CleanID($_GET["IdFamilia"]);
		setSesionDato("SubFamiliaDeFamilia",$padre);
	case "listasubfamilia":
		setSesionDato("PaginadoActivoFamilia","PaginadorListaSubFam");
		$padre = getSesionDato("SubFamiliaDeFamilia");		
		PaginaBasicaSubfamilia($padre);
		//echo "usando paginado " . getSesionDato("PaginadoActivoFamilia") . "<br>";
		break;


}

PageEnd();

?>
