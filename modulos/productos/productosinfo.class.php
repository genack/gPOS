<?php

function CrearProductoInformacion($IdProducto,$Indicacion,$CtraIndicacion,$Interaccion,
				  $Dosificacion,$opcion,$IdProductoInfo=false){

	$oProdInfo = new productoinformacion;

	$oProdInfo->set("IdProducto",$IdProducto, FORCE);
	$oProdInfo->set("Indicacion",limpiarProductoInformacio($Indicacion),FORCE);
	$oProdInfo->set("ContraIndicacion",limpiarProductoInformacio($CtraIndicacion), FORCE);
	$oProdInfo->set("Interaccion",limpiarProductoInformacio($Interaccion), FORCE);
	$oProdInfo->set("Dosificacion",limpiarProductoInformacio($Dosificacion), FORCE);
	
	switch($opcion){
	case "Crear":
	  if ($oProdInfo->Alta()) {
	    $id = $oProdInfo->get("IdProductoInformacion");
	    return $id;
	  } else
	    return false;
	  break;
	case "Modificar":
	  if ($oProdInfo->Modificar($IdProductoInfo)) 
	    return $IdProductoInfo;
	  else 
	    return false;
	  break;
	}
	
}

function limpiarProductoInformacio($xCadena){
  $aCadena = explode(";",$xCadena);
  $str     = '';
  $xstr    = '';
  $xitem   = 5;

  for($i=0;$i<5;$i++)
    {

    if($aCadena[$i] != '')
      {
	$nCadena .= $str.$aCadena[$i];
	$str = ';';
	$xitem--; 
    }

  }

  for($j=0;$j<$xitem;$j++)
    {
      $xstr .= ';';
    }

  return $nCadena.$xstr;
}

class productoinformacion extends Cursor {
    function productoinformacion() {
    	return $this;
    }

    function Alta(){
	global $UltimaInsercion;
	$data = $this->export();
	//print $data;
	$coma = false;
	$listaKeys = "";
	$listaValues = "";
				
	foreach ($data as $key=>$value){
	  if ($coma) {
	    $listaKeys .= ", ";
	    $listaValues .= ", ";
	  }
	  $listaKeys .= " $key";
	  $listaValues .= "'".$value."'";
	  $coma = true;
	}
		
	$sql = "INSERT INTO ges_productosinformacion ( $listaKeys ) VALUES ( $listaValues )";
	$res = query($sql,"Alta Producto Informacion");
		
	if ($res) {		
	  $id = $UltimaInsercion;	
	  $this->set("IdProductoInformacion",$id,FORCE);
	  return $id;			
	}
						
	return false;				 		
    }

    function Modificar($idproductoinfo){
	$data = $this->export();
	$coma = false;
	$str = "";
	
	foreach ($data as $key => $value) {
	  if ($coma)
	    $str .= ",";
	  $value = mysql_real_escape_string($value);
	  $str .= " $key = '".$value."'";
	  $coma = true;
	}

	$sql = "UPDATE ges_productosinformacion SET $str ".
	       "WHERE  IdProductoInformacion = '$idproductoinfo'";

	$res = query($sql,"Documento Modificado");

	if (!$res){
	  $this->Error(__FILE__ . __LINE__, "E: no pudo modificar Documento");
	  return false;	
	}		
	return true;				 		
    }

    function getIdProductoInformacion($idprod){
      $sql = "SELECT IdProductoInformacion ".
             "FROM ges_productosinformacion ".
	     "WHERE IdProducto = '$idprod' ".
	     "AND Eliminado = 0";
      $row = queryrow($sql);
      return $row["IdProductoInformacion"];
    }


}


function mostrarProductoInformacion($IdProducto){
  $sql = "SELECT CONCAT(Indicacion,'~',ContraIndicacion,'~',Interaccion,'~', ".
         "       Dosificacion) as ProductoInformacion ".
         "FROM   ges_productosinformacion ".
         "WHERE  IdProducto = $IdProducto ".
         "AND    Eliminado  = 0";

  $res = query($sql);
  if (!$res) return false;
  $DocumentoInfo = array();
  $t = 0;
  while($row = Row($res)){
    $nombre = "DocumentoInfo_" . $t++;
    $DocumentoInfo[$nombre] = $row; 		
  }	
  return $DocumentoInfo;
}

function registraProductoBorrador($xlocal,$xproducto,$xusuario){

	 $Keys    = "ProductoBorrador,";
	 $Values  = "'".$xproducto."',";
	 $Keys   .= "IdLocal,";
	 $Values .= "'".$xlocal."',";
	 $Keys   .= "IdUsuario";
	 $Values .= "'".$xusuario."'";
	 $sql     = "insert into ges_productosborrador ( $Keys ) values ( $Values )";
	 return query($sql);
}
?>