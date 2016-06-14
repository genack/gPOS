<?php

class guiaremision extends Cursor {
    function guiaremision() {
    	return $this;
    }

    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_guiaremision", "IdGuiaRemision", $id);
		return $this->getResult();
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
		
	$sql = "INSERT INTO ges_guiaremision ( $listaKeys ) VALUES ( $listaValues )";
	$res = query($sql,"Alta Guia Remision");
		
	if ($res) {		
	  $id = $UltimaInsercion;	
	  $this->set("IdGuiaRemision",$id,FORCE);
	  return $id;			
	}
						
	return false;				 		
    }

    function Modificar($id){
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
        
        $sql = "UPDATE ges_guiaremision SET $str WHERE IdGuiaRemision = '$id'";
        $res = query($sql,"Guia Remision Modificado");
        
        if (!$res){
            $this->Error(__FILE__ . __LINE__, "E: no pudo modificar Guia de remision");
            return false;	
        }		
        return true;				 		
    }
    
}

?>