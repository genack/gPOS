<?php


class bug extends Cursor {
	 function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_bugs", "IdBug", $id);
		return $this->getResult();
	}
	
	function esSugerencia(){
		return ($this->get("Categoria")=="SUGERENCIA");	
	}
	
	function GenID(){		
		$num = rand(90000000,99999999);
	
		$this->set("LOC",strtoupper(sprintf("%x",$num)), FORCE);	
	}
	
	
	function Reportar(){
		$categoria 	= $this->get("Categoria");
		$titulo 	= str_replace("\n"," ",$this->get("Titulo"));
		$crline = "----------------------\n";
		$crend = "------------------------------------------------\n";
		$cr = "\n";
					
		if ($this->esSugerencia()){
			$sugerencia = $this->get("QueEsperaba");
			$loc = $this->get("LOC");
			$titulo = "[9Gestion-sugerencia][$loc]: $titulo";
			
			
			$res = mail(CORREO_ADMIN, $titulo,"Sugerencia $loc:\n".$crline .$cr .$sugerencia.$crend );
			if (!$res){				
				mail(CORREO_ADMIN,"fallo envio correo",$titulo);	
			}			
			
			
		} else {
			$prioridad = $this->get("Urgencia");
			$loc = $this->get("LOC");

			
			$titulo = "[9Gestion-bug-$prioridad][$loc] $categoria : $titulo";			
			$cuerpo = "Bug $loc, de categoria $categoria:\n".
				$crline .			
				"Donde ocurrio el error:\n". $this->get("QueDonde"). $cr .
				$crline . 
				"Que esperaba:\n". $this->get("QueEsperaba"). $cr .
				$crline .
				"Que ocurrió:\n". $this->get("QueOcurrio"). $cr .
				$crline .
				"Historico:\n".  $this->get("LogHistorico"). $cr.
				$crend;
																
				
			$res = mail(CORREO_ADMIN, $titulo , $cuerpo);
			if (!$res){				
				mail(CORREO_ADMIN,"fallo envio correo",$titulo);	
			}			

		}					
	}
	
	function Fallo($titulo, $quedonde, $queesperaba, $categoria, $queocurrio, $urgencia	){
		$this->GenID();
		$this->set("Titulo",$titulo,FORCE);
		$this->set("QueEsperaba",$queesperaba,FORCE);
		$this->set("QueOcurrio",$queocurrio,FORCE);
		$this->set("QueDonde",$quedonde,FORCE);
		$this->set("Categoria",$categoria,FORCE);				
		$this->set("Urgencia",$urgencia,FORCE);
	}		
	
	function Sugerencia($titulo,$cuerpo){
		$this->GenID();
		$this->set("Titulo",$titulo,FORCE);
		$this->set("QueEsperaba",$cuerpo,FORCE);
		$this->set("Categoria","SUGERENCIA",FORCE);		
		$this->set("Urgencia",5,FORCE);//menor urgente que un bug
	}
	
	function Alta(){
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
			
			$value_s = CleanRealMysql($value);
			$listaKeys .= " $key";
			$listaValues .= " '$value_s'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_bugs ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta bug");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdBug",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}
	
}







?>
