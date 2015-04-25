<?php
 
include("../../tool.php"); 
 
SimpleAutentificacionAutomatica("novisual-services"); 
 
$modo = $_REQUEST["modo"]; 
 
switch($modo) { 
	case "modificarcliente":
		$idcliente      = CleanID($_POST["IdCliente"]);
		$comercial      = CleanText($_POST["NombreComercial"]);
		$legal          = CleanText($_POST["NombreLegal"]);
		$poblacion      = (isset($_POST["Localidad"]))? CleanText($_POST["Localidad"]):'';
		$direccion      = CleanText($_POST["Direccion"]);
		$cp             = (isset($_POST["CP"]))? CleanCP($_POST["CP"]):'';
		$email          = CleanEmail($_POST["Email"]);
		$telefono1      = CleanTelefono($_POST["Telefono1"]);
		$telefono2      = (isset($_POST["Telefono2"]))? CleanTelefono($_POST["Telefono2"]):'';
		$contacto       = (isset($_POST["Contacto"]))? CleanText($_POST["Contacto"]):'';
		$cargo          = (isset($_POST["Cargo"]))? CleanText($_POST["Cargo"]):'';
		$cuentabancaria = (isset($_POST["CuentaBancaria"]))? CleanCC($_POST["CuentaBancaria"]):'';
		$numero         = CleanText($_POST["NumeroFiscal"]);
		$comentario     = CleanText($_POST["Comentarios"]);
		$tipocliente    = CleanText($_POST["TipoCliente"]);
		$IdModPagoHabitual = (isset($_POST["IdModPagoHabitual"]))? CleanID($_POST["IdModPagoHabitual"]):1;
		$idpais 	= (isset($_POST["IdPais"]))? CleanID($_POST["IdPais"]):1; 
		$paginaweb      = (isset($_POST["PaginaWeb"]))? CleanUrl($_POST["PaginaWeb"]):'';
		$FechaNacimiento = CleanText($_POST["FechaNacimiento"]);
		$datehoy = date("Y-m-d");
		$FechaNacimiento = ($FechaNacimiento >= $datehoy)? '0000-00-00':$FechaNacimiento;
		if($tipocliente != 'Particular') $FechaNacimiento = '0000-00-00';
		if($FechaNacimiento == '1899-11-30') $FechaNacimiento = '0000-00-00';

		$oCliente       = new cliente;

		if(!$oCliente->Load($idcliente)) {
			echo 0;
			exit();	
		}
		$comercial = str_replace('&','&#038;',$comercial);
		$legal     = str_replace('&','&#038;',$legal);
		
		$oCliente->setIfData("NombreComercial", $comercial, FORCE);
		$oCliente->setIfData("NombreLegal", $legal, FORCE);
		$oCliente->setIfData("Direccion", $direccion, FORCE);
		$oCliente->setIfData("Localidad", $poblacion, FORCE);
		//$oCliente->setIfData("CP", $cp, FORCE);
		$oCliente->setIfData("Email", $email, FORCE);
		$oCliente->setIfData("Telefono1", $telefono1, FORCE);
		$oCliente->setIfData("Telefono2", $telefono2, FORCE);
		$oCliente->setIfData("Contacto", $contacto, FORCE);
		$oCliente->setIfData("Cargo", $cargo, FORCE);	
		$oCliente->setIfData("CuentaBancaria", $cuentabancaria, FORCE);
		$oCliente->setIfData("NumeroFiscal", $numero, FORCE);
		$oCliente->setIfData("Comentarios", $comentario, FORCE);
		$oCliente->setIfData("TipoCliente", $tipocliente, FORCE);
		$oCliente->setIfData("IdPais", $idpais, FORCE);
		$oCliente->setIfData("PaginaWeb", $paginaweb, FORCE);
		$oCliente->setIfData("FechaNacimiento", $FechaNacimiento, FORCE);
		//$oCliente->setIfData("IdLocal", CleanID(getSesionDato("IdTienda")), FORCE);
		
		if( $oCliente->Save()){
			echo $idcliente;
			$oCliente->setFechaSave($idcliente);
		} else {
			echo 0;
		}
		break;	
		
	case "altarapida":
	        $comercial = CleanText($_POST["NombreComercial"]);
		$legal     = CleanText($_POST["NombreLegal"]);
		$poblacion = (isset($_POST["Localidad"]))? CleanText($_POST["Localidad"]):'';
		$direccion = CleanText($_POST["Direccion"]);
		$cp        = CleanCP($_POST["CP"]);
		$email     = CleanEmail($_POST["Email"]);
		$telefono1 = CleanTelefono($_POST["Telefono1"]);
		$telefono2 = ( isset($_POST["Telefono2"]) )? CleanTelefono($_POST["Telefono2"]):'';
		$contacto  = ( isset($_POST["Contacto"]) )? CleanText($_POST["Contacto"]):'';
		$cargo     = ( isset($_POST["Pago"]) )? CleanText($_POST["Cargo"]):'';
		$cuentabancaria = (isset($_POST["CuentaBancaria"]))? CleanCC($_POST["CuentaBancaria"]):'';
		$numero     = CleanText($_POST["NumeroFiscal"]);
		$comentario = CleanText($_POST["Comentarios"]);
		$tipocliente = CleanText($_POST["TipoCliente"]);
		$IdModPagoHabitual = (isset($_POST["IdModPagoHabitual"]))? CleanID($_POST["IdModPagoHabitual"]):'';
		$idpais     = (isset($_POST["IdPais"]))? CleanID($_POST["IdPais"]):''; 
		$paginaweb  = (isset($_POST["PaginaWeb"]))? CleanUrl($_POST["PaginaWeb"]):'';
		$IdLocal    = CleanID(getSesionDato("IdTienda"));
		$FechaNacimiento = CleanText($_POST["FechaNacimiento"]);
		$datehoy = date("Y-m-d");
		$FechaNacimiento = ($FechaNacimiento >= $datehoy)? '0000-00-00':$FechaNacimiento;
		if($tipocliente != 'Particular') $FechaNacimiento = '0000-00-00';
		if($FechaNacimiento == '1899-11-30') $FechaNacimiento = '0000-00-00';
		
		$id = CrearCliente($comercial,$legal,$direccion,$poblacion,$cp,$email,
				   $telefono1,$telefono2,$contacto,$cargo,$cuentabancaria,
				   $numero,$comentario,$tipocliente,$idpais,$paginaweb,
				   $IdLocal,$FechaNacimiento);
		if ($id)		
		  echo "$id";
		else
		  echo "0";
		exit();			
	break;
	
} 
	
?>
