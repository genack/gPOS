<?php

include("tool.php");

//Albaranes traspaso

SimpleAutentificacionAutomatica("visual-iframe");

$tamPagina = 10;

$action = "modalbaranes.php";

function ListarAlbaranes() {
	global $action;
	
	$res = Seleccion( "AlbaranTraspaso","","FechaSalida DESC, IdAlbaranTraspaso ASC","20");
	
	if (!$res){
		echo gas("aviso","No hay Albaranes disponibles");	
	} else{
		
		//echo gas("titulo",_("Lista de Albaranes"));
		echo "<center>";
		echo "<table border=0 class=forma>";
		echo "<tr><td class='lh'>Fecha</td><td class='lh'>Mo</td><td class='lh'></td><td class='lh'></td></tr>";
		while ($oAlbaran = AlbaranFactory($res) ){		
		
			$id = $oAlbaran->getId();
		
		/*
	IdAlbaranTraspaso  	bigint(20)  	 	UNSIGNED  	No  	 	auto_increment  	  Examinar   	  Cambiar   	  Eliminar   	  Primaria   	  Índice   	  Único   	 Texto completo
	IdAlmacenSalida 	smallint(5) 		UNSIGNED 	No 	0 		Examinar 	Cambiar 	Eliminar 	Primaria 	Índice 	Único 	Texto completo
	IdAlmacenRecepcion 	smallint(5) 		UNSIGNED 	No 	0 		Examinar 	Cambiar 	Eliminar 	Primaria 	Índice 	Único 	Texto completo
	FechaPedido 	date 			No 	0000-00-00 		Examinar 	Cambiar 	Eliminar 	Primaria 	Índice 	Único 	Texto completo
	FechaSalida 	date 			No 	0000-00-00 		Examinar 	Cambiar 	Eliminar 	Primaria 	Índice 	Único 	Texto completo
	Observaciones 	tinytext 	latin1_swedish_ci 		Sí 	NULL 		Examinar 	Cambiar 	Eliminar 	Primaria 	Índice 	Único 	Texto completo
	Eliminado
		*/
		
			$FechaSalida = CleanFechaFromDB($oAlbaran->get("FechaSalida"));
		
			$descripcion = getNombreLocalId($oAlbaran->get("IdAlmacenSalida")) . " - ";
			$descripcion .= getNombreLocalId($oAlbaran->get("IdAlmacenRecepcion"));
			
			$linkVer = gAccion("mostrar",_("Mostrar"),$id); 
			echo "<tr class='f'><td class='fecha'>".$FechaSalida."</td><td class='descripcion'>".$descripcion."</td><td>".$linkVer."</td></tr>";					
		}		
		echo "</table>";
	}

	echo "</center>";	
}

function OperacionesConAlbaranes(){
}

function FormularioAlta() {
	global $action;
	
	$oAlbaran = new Albaran;

	$oAlbaran->Crea();
	
	echo $oAlbaran->formEntrada($action,false);	
}


function MostrarAlbaran($id){
	
	global $action;
	
	$id = CleanID($id);
	
	$albaran = new albaran;
	$albaran->Load($id);	
			
	$ot = getTemplate("ModeloAlbaran");
			
	if (!$ot){	
		error(__FILE__ . __LINE__ ,"Info: template busqueda no encontrado");
		return false; }

 	//<tr><td>G18196 - BERMDAS.F.SKATE.KAPPA;AC</td><td>1</td></tr>
 
 	//$this->userLog .= "<tr><td>". $referencia . " - " . $nombre . "</td><td>". $unid . "</td></tr>";
 	//	$sql = "INSERT INTO ges_albtraspaso_det (IdAlbaranTraspaso,IdProducto,Unidades) 
	//			VALUES ('$IdAlbaran','$IdProducto','$Unidades')";
	
	$detallesString = "";
	$prod = new producto;
	
	$sql = "SELECT * FROM ges_albtraspaso_det WHERE IdAlbaranTraspaso=$id ORDER BY IdDetalle ASC";
	
	$res = query($sql,"Listando detalles de un albaran");
	
	while( $row = Row($res) ){
		$IdProducto = $row["IdProducto"];
		
		$prod->Load($IdProducto);
		$nombre_s = CleanParaWeb($prod->getNombre());
		$referencia_s = CleanParaWeb($prod->get("Referencia"));
		$unid = $row["Unidades"];		 
		
		$detallesString .= "<tr><td>". $referencia_s . " - " . $nombre_s . "</td><td>". $unid . "</td></tr>";
		
		
	}			
	
	$comercio 		= $_SESSION["GlobalNombreNegocio"];
	
	$local 			= new local;
	$local->Load( $albaran->get("IdAlmacenSalida") );
	$nombreorigen 	= CleanParaWeb($local->getNombre());
		
	$localdestino 	= new local;
	$localdestino->Load( $albaran->get("IdAlmacenRecepcion") );
	$nombredestino 	= CleanParaWeb($localdestino->getNombre());		
	
	$FechaSalida = CleanFechaFromDB( $albaran->get("FechaSalida") );
	
	$ot->fijar("FECHA", $FechaSalida);
	$ot->fijar("LINEAS", $detallesString);
	$ot->fijar("NOMBRECOMERCIO", CleanParaWeb($comercio));
	$ot->fijar("NUMEROALBARAN",$id);
	$ot->fijar("DESDETIENDA",$nombreorigen);
	$ot->fijar("HASTATIENDA",$nombredestino);
	
	echo $ot->Output();	
}



function PaginaBasica(){
	ListarAlbaranes();	
	OperacionesConAlbaranes();	
}

PageStart();

switch($modo){
	case "pagmas":
		$index = getSesionDato("PaginadorAlbaranes");		
		$index = $index + $tamPagina;		
		setSesionDato("PaginadorAlbaranes",$index);		
		break;
		
	case "pagmenos":
		$index = getSesionDato("PaginadorAlbaranes");		
		$index = $index - $tamPagina;
		if ($index<0)	$index = 0;		
		setSesionDato("PaginadorAlbaranes",$index);
		break;
		
	case "mostrar":
		$id = CleanID($_GET["id"]);	
		echo "<p style='margin: 64px'>";	
		MostrarAlbaran($id);			
		echo "<p><input class='noimprimir' type='button' onclick='print()' value='Imprimir'></p>";
		echo "</p>";		
			
		break;		
	default:
		ListarAlbaranes();
		OperacionesConAlbaranes();
		break;		
}

PageEnd();

?>
