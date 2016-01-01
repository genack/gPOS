<?php

function ProductoSatFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdProductoSat"];
	
	$oOrden = new ordenservicio;
		
	if ($oOrden->Load($id))
		return $oOrden;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class productosat extends Cursor {
  
   function productosat() {
     return $this;
   }
  
   function Load($id) {
     $id = CleanID($id);
     $this->setId($id);
     $this->LoadTable("ges_ordenservicio", "IdOrdenServicio", $id);
     return $this->getResult();
   }
   
   function Crea(){

   }

   function getIdProdSat($producto){
     $sql = "SELECT IdProdBaseSat FROM ges_productosidiomasat ".
            "WHERE  ges_productosidiomasat.Descripcion = '$producto' ";
     $row = queryrow($sql);
     if($row["IdProdBaseSat"] != ''){
       $this.set("Eliminado",0,FORCE);
       $this->Modificar('ges_productosidiomasat','IdProductoIdiomaSat',$row["IdProdBaseSat"]);
       return $row["IdProdBaseSat"];
     }
     else
       return '';
   }

   function getEstado(){
     return $this->get("Estado");
   }

   function getProdBaseSat(){
     $this->setProdBaseSat();
     return $this->get("IdProdBaseSat");
   }

   function setProdBaseSat(){
     $sql = "SELECT MAX(IdProdBaseSat) as IdProdBaseSat FROM ges_productosidiomasat";
     $row = queryrow($sql);

     $prodbase = ($row["IdProdBaseSat"] != '')? $row["IdProdBaseSat"]:1;
     $this->set('IdProdBaseSat',$prodbase,FORCE);
     
     if($row) 
       $this->set('IdProdBaseSat',$row["IdProdBaseSat"]+1,FORCE);
     else
       $this->set('IdProdBaseSat',1,FORCE);
   }
   
   function Alta($table,$idtable){
     global $UltimaInsercion;
     
     $data = $this->export();
     
     $coma = false;
     
     $listaKeys = "";
     $listaValues = "";
     
     foreach ($data as $key=>$value){
       if ($coma) {
	 $listaKeys .= ", ";
	 $listaValues .= ", ";
       }
       
       $listaKeys .= " " . $key;
       $listaValues .= " '".$value."'";
       $coma = true;
     }
     
     $sql = "INSERT INTO $table ( $listaKeys ) VALUES ( $listaValues )";
     $res = query($sql);
     
     if ($res) {		
       $id = $UltimaInsercion;	
       $this->set($idtable,$id,FORCE);
       return $id;			
     }
						
     return false;
   }

   function Modificar($table,$idtable,$id){
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
     
     $sql = "UPDATE $table SET $str ".
       "WHERE  $idtable = '$id'";
     
     $res = query($sql);
     
     if (!$res){
       $this->Error(__FILE__ . __LINE__, "E: no pudo modificar ");
       return false;	
     }		
     return true;				 		
   }
   
   
}


?>