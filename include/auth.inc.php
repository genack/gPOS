<?php
//include("class/usuario.class.php");
function SimpleAuth($module,$password){
	if (!$password){
		echo _("Este modulo esta desactivado");	
		exit();	
	}	
	
	if (!$_SESSION["Permitir_". $module]){
		$ok = false;
		if (isset($_POST["pass"])){
			if ($_POST["pass"]==$password){
				$ok = 1;
				$_SESSION["Permitir_". $module] = true;
			}
		}

	if (!$ok){	
			echo "<form method='post' name='form'>";
			echo "<input id='inputbox' type=password name='pass' onkeypress=\"if (event.which == 13) form.submit();\">" ;		
			echo "</form>" ;
			echo "<script> document.getElementById('inputbox').focus() </script>";
			exit();		
		} 	
	}
	
}

function Admite($noseque,$modulo=false){
	global $modulos;

	//Si exige modulo, pero este no esta disponible	
	if ($modulo and !$modulos[$modulo] )
		return false;	
	
	$val = getSesionDato("PerfilActivo");
	return 	$val[$noseque];
}

function xulAdmite($noseque,$modulo=false){
	if (Admite($noseque,$modulo)){
		return "";
	}else{
		return " disabled='true' ";		
	}

}

function selAdmite($noseque,$modulo=false){
	if (Admite($noseque,$modulo)){	       
		return false;
	}else{
		return true;		
	}

}

function jsAdmite($noseque,$modulo=false){
	if (Admite($noseque,$modulo)){	       
		echo 1;
	}else{
		echo 0;		
	}

}


function gulAdmite($noseque,$modulo=false){		
	echo xulAdmite($noseque,$modulo);
}

function RegistrarTiendaLogueada($id){
	$id = CleanID($id);
	setSesionDato("IdTienda",$id);	
	//Agregado para gestion de almacen central
	setSesionDato("IdTiendaDependiente",$id);
	//echo $id;

	//ControlExtra Listados 
	setSesionDato("postCompraListado",true);	
}
function RegistrarIGVTienda($id){
    $id = CleanID($id);
    $sql = "SELECT Impuesto,Percepcion,CuentaBancaria,CuentaBancaria2 FROM ges_locales WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo IGV del local");
    if($row)
      {
	setSesionDato("IGV", $row["Impuesto"]);
	setSesionDato("IPC", $row["Percepcion"]);
	setSesionDato("CuentaBancaria", $row["CuentaBancaria"]);
	setSesionDato("CuentaBancaria2", $row["CuentaBancaria2"]);
      }
}
function RegistrarGarantiaComercial($id){
    $id = CleanID($id);
    $sql = "SELECT GarantiaComercial FROM ges_locales WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo Garantia Comercial del local");
    if($row)
      setSesionDato("GarantiaComercial", $row["GarantiaComercial"]);	
}

function RegistrarVigenciaPresupuesto($id){
    $id = CleanID($id);
    $sql = "SELECT VigenciaPresupuesto FROM ges_locales WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo Vigencia Presupuesto del local");
    if($row)
      setSesionDato("VigenciaPresupuesto", $row["VigenciaPresupuesto"]);	
}

function RegistrarAlmacenCentral($id){
    $id = CleanID($id);
    $sql = "SELECT AlmacenCentral, CajaCentral FROM ges_locales WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo idAlmacenCentral del local");
    if($row)
      setSesionDato("esAlmacenCentral",$row["AlmacenCentral"]);

    RegistrarCajaCentral();
}

function RegistrarCajaCentral(){
    $sql = "SELECT CajaCentral FROM ges_locales WHERE AlmacenCentral = 1 ";
    $row = queryrow($sql,"Obteniendo caja central");
    if($row)
      setSesionDato("esCajaCentral",$row["CajaCentral"]);
}

function RegistrarMUTienda($id){
    $id = CleanID($id);
    $sql = "SELECT MargenUtilidad FROM ges_locales WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo MargenUtilidad del local");
    if($row)
        $mu = $row["MargenUtilidad"];
    setSesionDato("MargenUtilidad",$mu);	
}

function RegistrarUsuarioLogueado($id){
			
	$sql = "SELECT Nombre,IdPerfil,AdministradorWeb,IdLocal FROM ges_usuarios WHERE IdUsuario='$id'";
	$row = queryrow($sql,"¿como se llama usuario?");
	if($row){
		$nombre     = $row["Nombre"];
		$userlocal  = $row["IdLocal"];
	}
	
	
	setSesionDato("NombreUsuario",$nombre);
	setSesionDato("IdUsuario",$id);
	setSesionDato("UsuarioLocal",$userlocal);
	
	if ($row["AdministradorWeb"])
		setSesionDato("UsuarioAdministradorWeb",1);
	else
	  	setSesionDato("UsuarioAdministradorWeb",false);
	
	$user = getUsuario($id);
	$_SESSION["EsAdministradorFacturas"] = $user->get("AdministradorFacturas");
	
	//Autentificacion para modulos novisuales																							
	$_SESSION["AutentificacionAutomatica"] = true;

	$idPerfil = $row["IdPerfil"];
	
	$sql = "SELECT * FROM ges_perfiles_usuario WHERE IdPerfil = '$idPerfil'";
	
	$row = queryrow($sql);
	if (!$row)
		return;
	
	setSesionDato("PerfilActivo",$row);	
}


function identificacionLocalValidaMd5($identificador,$passmd5){
	global $_motivoFallo;
	
	//$randString = $_SESSION["CadenaAleatoria"];
	
	$identificador = CleanLogin($identificador);	
	$datosValidos = strlen($identificador)>1 and strlen($passmd5)>1;
	
	if (!$datosValidos) {
		//$_motivoFallo = "datos'$identificador o $passmd5 nulos'";
		return false;	
	}		
	
	$sql = "SELECT IdLocal,Password FROM ges_locales WHERE Identificacion = '$identificador' AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row) {
		//$_motivoFallo = _("No encuentra local");			
		return false;
	}

	//$valido = md5($row["Password"]);// . $randString);
	$valido = $row["Password"];// . $randString);
	
	if ( $valido != $passmd5) {
		//$_motivoFallo = "DEBUG: datos'$valido != $passmd5', para " . $row["Password"];		
		return false;		
	}
		
	return $row["IdLocal"];	
}


function identificacionUsuarioValidoMd5($identificador,$passmd5){

	global $_motivoFallo;
	//$idlocal = getSesionDato("IdTienda");
	//$randString = $_SESSION["CadenaAleatoria"];
		
		
	$datosValidos = strlen($identificador)>1 and strlen($passmd5)>1;
	
	if (!$datosValidos)
		return false;		
	
	$sql = "SELECT IdUsuario,Password, concat( IdLocal,'',if( GrupoLocales like '', '', concat(',',GrupoLocales) )) as locales 
                FROM ges_usuarios 
                WHERE Identificacion = '$identificador' 
                AND Estado = 'Activo' 
                AND Eliminado=0";
	$row = queryrow($sql);
	if (!$row)
		return false;

	//$valido = md5($row["Password"]);// . $randString);
	$valido = $row["Password"];// . $randString);
	if ( $valido != $passmd5) {
		$_motivoFallo = "datos'$valido != $passmd5'";		
		return false;		
	}
	//Carga datos locales permitidos
	registraLocalesPermitidos($row["locales"]);
	//Retorma Id
	return $row["IdUsuario"];	
}


function identificacionLocalValida($identificador,$pass){
	
	$datosValidos = strlen($identificador)>1 and strlen($pass)>1;
	
	if (!$datosValidos)
		return false;	
		
	
	$sql = "SELECT IdLocal FROM ges_locales WHERE Identificacion = '$identificador' AND Password = '$pass'";
	$res = query($sql);
	if (!$res)
		return false;
		
	$row = Row($res);
	if (!is_array($row))
		return false;
		
	return $row["IdLocal"];	
}

function identificacionUsuarioValido($identificador,$pass){
	
	$datosValidos = strlen($identificador)>1 and strlen($pass)>1;
	
	if (!$datosValidos)
		return false;		
	
	$sql = "SELECT IdUsuario FROM ges_usuarios WHERE Identificacion = '$identificador' AND Password = '$pass'";
	$res = query($sql);
	if (!$res)
		return false;
		
	$row = Row($res);
	if (!is_array($row))
		return false;
		
	return $row["IdUsuario"];	
}

function SimpleAutentificacionAutomatica($subtipo=false,$redireccion=false){
	if(!isset($_SESSION["AutentificacionAutomatica"]) || !$_SESSION["AutentificacionAutomatica"]){
		//Si no esta autentificado, la pagina termina aqui mismo.
		// esto es valido para modulos sin parte visual,
		// y deberia solo ocurrir cuando se trata de acceder directamente		
		// por un cracker.
		
		if ($redireccion) {
			session_write_close();
			header("Location: $redireccion");
		}		
		exit;	
	}	
}

function verificarPassUser($id,$passmd5){
  global $_motivoFallo;
  
  //$randString = $_SESSION["CadenaAleatoria"];
  
  $identificador = CleanID($id);	
  $datosValidos = strlen($identificador)>0 and strlen($passmd5)>1;
  
  if (!$datosValidos) {
    //$_motivoFallo = "datos'$identificador o $passmd5 nulos'";
    return false;	
  }		
  
  $sql = "SELECT IdUsuario,Password FROM ges_usuarios WHERE IdUsuario = '$identificador' AND Eliminado=0";
  $row = queryrow($sql);
  if (!$row) {
    //$_motivoFallo = _("No encuentra local");			
    return false;
  }
  
  $valido = $row["Password"];// . $randString);
  
  if ( $valido != $passmd5) {
    //$_motivoFallo = "DEBUG: datos'$valido != $passmd5', para " . $row["Password"];		
    return false;		
  }
		
  return $row["IdUsuario"];	
  

}

function RegistrarMoneda(){
  $Moneda = getMoneda();
  setSesionDato("Moneda",$Moneda);
}

function RegistrarValuacionPrecioTPV($id){
    $id = CleanID($id);
    $sql = "SELECT MetodoRedondeo,Descuento,COPImpuesto FROM ges_locales 
            WHERE IdLocal = '$id'";
    $row = queryrow($sql,"Obteniendo Redondeo Precio del local");

    if($row){
      setSesionDato("MetodoRedondeo",$row["MetodoRedondeo"]);
      setSesionDato("DescuentoTienda",$row["Descuento"]);
      setSesionDato("COPImpuesto",$row["COPImpuesto"]);
    }
}

function RegistrarKeySyncTPV(){
  $idlocal   = getSesionDato("IdTienda");
  $idusuario = getSesionDato("IdUsuario");
  $keysync   = MD5(date("Y-m-d H:i:s"));

  $xset   = '';
  $xset  .= "IdLocal = '$idlocal', ";
  $xset  .= "KeySync = '$keysync', ";
  $xset  .= "Preventa = 0, ";
  $xset  .= "Proforma = 0, ";
  $xset  .= "ProformaOnline = 0, ";
  $xset  .= "Stock = 0, ";
  $xset  .= "Cliente = 0, ";
  $xset  .= "Promocion = 0, ";
  $xset  .= "Mensaje = 0, ";
  $xset  .= "Caja = 0, ";
  $xset  .= "MetaProducto = 0 ";
    
  $sql = "UPDATE ges_synctpv SET $xset ".
         "WHERE IdUsuario = $idusuario ";
  query($sql);

  setSesionDato("KeySync",$keysync);
}

function registraLocalesPermitidos( $xlocales ){
  $xsqllocal = ( $xlocales == '0')? '':' idlocal in ( '.$xlocales.' ) and';

  $sql = 
    " select IdLocal,AdmitePassword,Identificacion ".
    " from  ges_locales ".
    " where $xsqllocal eliminado = 0";
  $res = query($sql);
  $ckAccess = Array();
  $xsrt = '';

  while($row= Row($res)) {
    $ckAccess[ $row['Identificacion'] ] = $row['IdLocal'].":".$row['AdmitePassword'];
    $ckAccess[ 'js' ] .= $xsrt.$row['Identificacion'].":".$row['AdmitePassword'];
    $xsrt              = '~';
  }
  setSesionDato("LocalAccess",$ckAccess);
}

function RegistrarLoginLog(){
  $idlocal   = getSesionDato("IdTienda");
  $idusuario = getSesionDato("IdUsuario");
  $IP        = getRealIP();
  global $UltimaInsercion;

  $sql = "INSERT INTO ges_loginlog (IdLocal, IdUsuario, IpAcceso) VALUES($idlocal,$idusuario,'$IP') ";
  query($sql);
  setSesionDato("IdLoginLog",$UltimaInsercion);
  $_SESSION["IdLoginLog"] = $UltimaInsercion;
}

function getRealIP(){
  if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
    {
        return $_SERVER["HTTP_X_FORWARDED"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED"]))
    {
        return $_SERVER["HTTP_FORWARDED"];
    }
    else
    {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function actualizarLoginLog(){
  $idlog = getSesionDato("IdLoginLog");
  if(!$idlog) return;
  $sql = "UPDATE ges_loginlog SET FechaFin = NOW() ".
         "WHERE IdLoginLog = $idlog ";
  query($sql);
}
?>
