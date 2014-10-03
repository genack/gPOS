<?php

define ("FORCE",1);

function ContarFilas($from,$where=""){
	global $FilasAfectadas;
	$res = Seleccion($from,$where);
	if (!$res)
		return false;
	
	return $FilasAfectadas;	
} 



 
function Seleccion($from,$where="",$order="", $limit=""){
	switch($from){
		case "AlbaranTraspaso":
			$from = "FROM ges_albaranes_traspaso";
			$id = "IdAlbaranTraspaso";			
			break;				
		
		case "Almacen":
			$from = "FROM ges_almacenes";
			$id = "Id";			
			break;				
		case "Producto":
			$from = "FROM ges_productos";
			$id = "IdProducto";			
			break;			
		case "Template":
			$from = "FROM ges_templates";
			$id = "IdTemplate";			
			break;	
		case "Usuario":
			$from = "FROM ges_usuarios";
			$id = "IdUsuario";			
			break;		
		case "Perfil":
			$from = "FROM ges_perfiles_usuario";
			$id = "IdPerfil";			
			break;		
		case "Local":
			$from = "FROM ges_locales";
			$id = "IdLocal";			
			break;
		default:
			$from = "FROM $from";
			$id = "Id";
			break;		
	}
	
	if ($where)
		$where = "WHERE $where AND Eliminado=0";
	else
		$where = "WHERE Eliminado = 0";

	if ($order)
		$order = "ORDER BY $order";
	
	if ($limit)
		$limit = "LIMIT $limit";
	
	$sql = "SELECT $id $from $where $order $limit ";
	
	$res = query($sql);
	return $res;	
}

function Row($res) {

	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR requiriendo datos");
		return false;	
	}
	
	$data = mysql_fetch_array($res);
	if (!is_array($data)) {
		$data = mysql_fetch_row($res);
	}

	return $data;
}

function LogSQLErroneo ($sql) {
	$sqlguardar = base64_encode($sql);			
	$llaves 	= "`Sql`, CreadoPor, IdCreador, Exito, FechaCreacion";
	$valores 	= "'$sqlguardar','web',0,0,NOW()"; 		
		
	$sqlSalvar = "INSERT ges_logsql ( $llaves ) VALUES ( $valores )";
	
	mysql_query($sqlSalvar);
}



function forceconnect(){
	//Solamente abre una conexion	
	global $link;	
	global $ges_database;	
	global $global_host_db;
	global $global_user_db ;
	global $global_pass_db;
	
	$database = $ges_database;	
		
	if (!$link) {
		//Si no se conecto antes, conecta ahora.
		$link = mysql_connect($global_host_db, $global_user_db, $global_pass_db);
		if (!$link)
			error(__FILE__. __LINE__, "Fatal: No puedo conectar a la base de datos");
		else
			mysql_select_db($database,$link);							
	}		
}          


function query($sql=false,$nick="") {
	global $link;
	global $UltimaInsercion,$FilasAfectadas, $debug_sesion;		
	global $ges_database;
	global $sqlTimeSuma;
	global $global_host_db;
	global $global_user_db ;
	global $global_pass_db;
	global $querysRealizadas;
	
	$lastime = microtime(true);
	
	if (!isset($sql)) {
		error(__FILE__ . __LINE__ , "Fatal: se paso un sql vacio!");
		return false;
	}
		
	$database = $ges_database;	
	$result = false;

	if (!$link) { forceconnect(); }
	//Set to UTF8
	mysql_query("SET NAMES utf8");
	
	//Set to es_ES
        mysql_query(" SET lc_time_names = 'es_ES' ");

	if ($link) 	$result = mysql_query($sql) or LogSQLErroneo($sql);
	
	if (!$result) {						
		die("Fallo de conexión en $sql o $link");
	}
	
	$ahora = microtime(true);
		
	$sqlTimeSuma = $sqlTimeSuma + ($ahora - $lastime);

	$UltimaInsercion  =	mysql_insert_id($link);
	$FilasAfectadas  = mysql_affected_rows($link);
	
	$querysRealizadas[] = $sql;		
	$_SESSION["QuerysRealizadas"] = $querysRealizadas;
	
	
	if ($nick == "CONVERSOR"){
		return $result;
	}
	
	if($result)	$fueExito = 1;
	else $fueExito = 0;
		
	$sqlguardar = base64_encode($sql);			
	$llaves 	= "`Sql`, CreadoPor, IdCreador, Exito, FechaCreacion,TipoProceso";
	$valores 	= "'$sqlguardar','web',0,'$fueExito',NOW(),'$nick'"; 		
		
	  //$sqlSalvar = "INSERT ges_logsql ( $llaves ) VALUES ( $valores )";
	
	  //if (!mysql_db_query($database, $sqlSalvar,$link)){
	  //error(__FILE__ . __LINE__ , "Fatal: fallo log '$sqlSalvar'");	
	  //}			
		 
	return $result;
}

function CreaInsercion($soloEstos,$data,$nombreTabla) {
	$coma = false;
	
	$todos = true;
	if (is_array($soloEstos))
		$todos = false;
	
	$listaKeys = "";
	$listaValues = "";
				
	foreach ($data as $key=>$value){
		
		if ($todos)
			$vale = true;			
		else
			$vale = in_array($key,$soloEstos);
		
		if ($key =="0" or !$key)
			$vale = false;
		if (intval($key)>0)
			$vale = false;							

		
		//error(__LINE__ , "Info: key '$key' val '$value' vale '$vale' lkeys: '$listaKeys'");
							
		if ($vale) {
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$listaKeys .= " $key";
			$listaValues .= " '$value'";
			$coma = true;						
		}									
	}


	return "INSERT INTO $nombreTabla ( $listaKeys ) VALUES ( $listaValues )";
}
		

function CreaUpdate ($soloEstos, $data,$nombreTabla, $nombreID,$idvalue ) {
		$coma = false;
		$str = "";
	
		foreach ($data as $key => $value) {
			if ( in_array($key,$soloEstos) and $key != "0" ) {
				if ($coma)
					$str .= ",";

				$value = mysql_escape_string($value);

				$str .= " $key = '$value'";
				$coma = true;
			}
		}

		return "UPDATE $nombreTabla SET $str WHERE $nombreID = '$idvalue'";
}
	
function CreaUpdateSimple ($data,$nombreTabla, $nombreID,$idvalue ) {
		$coma = false;
		$str  = '';

		foreach ($data as $key => $value) {
			if (  $key != "0" and intval($key)==0 ) {
				if ($coma)
					$str .= ",";

				$value = mysql_escape_string($value);

				$str .= " $key = '$value'";
				$coma = true;
			}
		}

		return "UPDATE $nombreTabla SET $str WHERE $nombreID = '$idvalue'";
}
 
 
function queryrow($sql,$nick=false) {
	$res = query($sql,$nick);
	if (!$res){
		return false;	
	}
	$row = Row($res);
	if (!is_array($row)){
		return false;	
	}
	return $row;
} 

function getIdFromLang($iso) {
	$sql = "SELECT IdIdioma FROM ges_idiomas WHERE iso='$iso'";
    $row = queryrow($sql);
    if (!is_array($row)) {
    	error(__FILE__ . __LINE__ , "Info: no ha podido coger idioma desde iso");
    	return false;
    }
    
    //$id = $row["IdIdioma"];    
    //error(__FILE__ . __LINE__, "Info: id idioma es '$id'");
    
	return $row["IdIdioma"];    			
}



function nroRows($sql){
  global $link;
  $result = false;
  $rows   = false;
  if (!$link) { forceconnect(); }
  if ($link) $result = mysql_query($sql);
  if (!$result) {
    die("Fallo de conexión en $sql o $link");
  }else{
    return  mysql_num_rows($result);
  }
}



?>
