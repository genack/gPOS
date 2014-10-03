<?php

class Cursor {

	// DATOS de DATOS

	var $_datosFila; //Datos de fila
	var $_idFila; //id de fila
	var $_nameid; // nombre del id en la tabla
	var $_nombretabla; //Nombre de la tabla.
	var $_result; //Resultado de la ultima operacion.
	var $_error_msg; //mensaje de error en formato nice
 
	// DATOS de PAGINADOR
	
	var $_resPagina;
	
	function queryrow($sql){
		$res = query($sql);
		$this->_result = $res;
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"W: fallo en sql queryrow '$sql'");
			return false;	
		}				
		$row = Row($res);
		if (!is_array($row)){
			$this->Error(__FILE__ . __LINE__ ,"W: consulta no encuentra fila! '$sql'");
			return false;				
		}
		$this->import($row);			
		return true;		
	}
	
	
	function queryPagina($sql, $min = 0, $tam = 10 ) {
		
		$min = intval($min);
		$tam = intval($tam);		
		
		$res = query($sql . " LIMIT $min, $tam");
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"W: fallo en sql paginador '$sql'");	
		}
		$this->_resPagina = $res;	
		return $res;	
	}
	
	function LoadNext(){
		if (!$this->_resPagina)
			return false;
		
		$row = Row($this->_resPagina);
		if (!is_array($row)){			
			return false;  
		}			
		
		$this->_datosFila = $row;
		return true;	
	}	
	
	// Constructor

	function Cursor() {
		return $this;
	}

	// Cargando datos

	function LoadTable($tabla, $nameid, $id) {
		// Cada tabla se carga con un identificador y el nombre de tabla						
		if (!$id)
			return;

		$id = CleanID($id);

		$this->_idFila = $id;
		$this->_nameid = $nameid;
		$this->_nombretabla = $tabla;

		$sql = "SELECT * FROM ".$tabla." WHERE ($nameid='$id') ";
		$myresult = query($sql);

		if ($myresult) {
			$this->_datosFila = Row($myresult);

			if (!$this->_datosFila)
				$myresult = false;
			else {
				foreach ($this->_datosFila as $key => $value) {
					if (!isset ($value)) {
						$this->_datosFila[$key] = 0;
					}
				}
			}
		}

		$this->_result = $myresult;

		return $myresult;
	}


	// info

	function isKey($key) {
		if (!isset ($this->_datosFila))
			return false;

		if (!isset($key)) {
			$this->Error(__FILE__.__LINE__," No existe este campo $key");
			return false;
		}

		if (isset ($this->_datosFila[$key]))
			return true;

		return false;
	}

	function getResult() {	return $this->_result;	}

	function is($key){
		$val = $this->get($key);
		if ($val==1 or $val=="1" or $val =="on")
			return true;
		return false;	
	}

	// gets
	function getId(){
		return CleanID($this->_idFila);
	}

	function getLocal($campo){
		global $L;
		
		return $this->get($campo . $L);		
	}

	function get($campo) {
		$valor = false;
		if (!isset ($this->_datosFila)) {
			//$this->Error(__LINE__. __FILE__, "Fatal: no hay data en data leyendo $campo!");
			$this->_datosFila = array();
			return false;
		}
		if (isset($this->_datosFila[$campo]))
			return $this->_datosFila[$campo];
		return false;
	}

	function getNoNull($key) {
		$val = $this->get($key);
		if (!isset ($val))
			return "0";
		if (!$val or $val == "")
			return "0";
		return $val;
	}

	function getInt($campo){
		return intval($this->get($campo));
	}	

	function getClean($key){   //Evita campos con macros (empieza por %		
		$val = 	$this->get($key);
		if ($val[0]=='%')
			return "";		
		return $val;
	}

	function getSiNo($key){  //Lee como si o como no
		$is = $this->getNoNull($key);			
		//AddError(0,"Info: '$key' is es ".q($is));	
		if ($is=="1" or $is == 1){
			return _("Si");	
		} 
		
		return _("No");			
	}

	function getFichero($key){ //Guarda informacion de forma codificada			
		return base64_decode( $this->get($key) );					
	}

	// import/export
	function export() {
		return $this->_datosFila;
	}

	function import($mifila) {
		if (!is_array($mifila))
			$mifila = array ();
		if (!is_array($this->_datosFila))
			$this->_datosFila = array ();

		if (!isset($mifila))
			$this->Error(__FILE__.__LINE__, "E: No se acepta importar datos vacios.");

		$this->_datosFila = array_merge($this->_datosFila, $mifila);
	}

	function loadPOST($filter=false,$convert=false)	{
		if (!$filter or !is_array($filter)){
			foreach ($_POST as $key=>$value){
				$this->set($key,$value);	
			}
		} else {
			foreach ($filter as $val) {
				$value = $_POST[$val];
				if (!$convert) {									
					$this->set($val,$value,FORCE);
				}else {	
					if ($val){
						if ($value=="on")
							$this->set($val,"1",FORCE);
						else
							$this->set($val,"0",FORCE);
					}
				}					
				$this->Error(0,"Info: $val=$value");
			}					
		}		
	}	

	function QuickLoad($key,$id=false) {				

		//Carga forzada desde BD		
		if (!$this->_nombretabla) {			
			$this->Error(__FILE__ . __LINE__ , "E: uso sin inicializar!");
		}		
		
		if (!$id) {
			$id = $this->_idFila;		
		}
		
		$sql = "SELECT $key FROM " . $this->_nombretabla . " WHERE " . $this->_nameid . " = '$id'";
		$res = query($sql);
		
		if (!$res) { 
			$this->Error(__FILE__ . __LINE__ , "W: este $sql no pudo trabajar");
			return false;
		}	
		
		$row = Row($res);
		
		if (!is_array($row)){
			$this->Error(__FILE__ . __LINE__ , "W: no se pudo QL $key," . q($sql));
			return false;			
		}
		
		$value = $row[$key];
		
		$this->set($key,$value,FORCE); 
		
		return $value;						 		
	}
	

	function setIfData($key,$valor,$force=false){
		if($valor)
			$this->set($key,$valor,$force);	
	}

	// sets
	function set($key, $valor, $force = false) {
		if ($this->isKey($key))
			$this->_set($key, $valor);
		else {
			if ($force) {
				$this->_set($key, $valor);
				return;
			}
		}
	}

	function _set($campo, $valor) {
		if (!$this->_datosFila)
			$this->_datosFila  = array(); //sino un set force sin cargar el objeto falla
		
		$this->_datosFila[$campo] = $valor;
	}

	//Guarda datos en formato 1/0 desde formato checkbox
	function setCheck($key, $value, $force = false) {
		if ($value == "on")
			$this->set($key, 1, $force);
		else
			$this->set($key, 0, $force);
	}

	//Cambia el identificador de fila
	function setId($id){
		$this->_idFila = CleanID($id);	
	}

	//Ajusta dato localizado (soporte de idiomas)
	function setLocal($key,$valor,$force=false){		
		global $L;
		$this->set($key . $L,$valor,$force);				
	}

	//Guarda dato numerico haciendo conversiones
	function setNumeroFlotante($key,$valor,$force=false){
		$valor = str_replace(",",".",$valor);
		$this->set($key,$valor,$force);
	}
	
	//Guarda datos inseguros de forma cifrada
	function setFichero($key,$valor,$force=false){	
		$cod = base64_encode ($valor);//almacenamos la factura recodificada
		$this->set($key,$cod,$force);				
	}

	// arrays
	function resetArray($datos) {
		foreach ($datos as $key) {
			$this->_datosFila[$key] = false;
		}
	}

	function setArray($keys) {
		foreach ($keys as $key => $value) {
			$this->set($key, $value);
		}
	}

	function changeArray($datos, $filter = "") {
		if (!is_array($datos))
			$this->Error(__FILE__.__LINE__,"W: cambiando datos vacios");

		if ($filter == "")
			$filter = false; //no usar el filtro 

		foreach ($datos as $key => $value) {
			if ($filter) {
				if (in_array($key, $filter)) //es un valor valido
					$this->set($key, $value, true);
			} else {
				$this->set($key, $value);
			}
		}
	}

	// Salvando en BD
	function QuickSave($key,$value="0") {				
		
		if (!$this->_nombretabla) {			
			$this->Error(__FILE__ . __LINE__ , "E: uso sin inicializar!");
		}		
		
		$this->set($key,$value,FORCE);
		$sql = "UPDATE " . $this->_nombretabla . " SET $key = '$value' WHERE " . $this->_nameid . "= '" . $this->_idFila . "'";
		$res = query($sql);
		
		if (!$res) 
			$this->Error(__FILE__ . __LINE__ , "W: este $sql no pudo trabajar");				
		 
		return $res;		
	}
	

	function Save($hackCheckBox = false) {
		//Si no hay nada que salvar, echo! :I
		if (!$this->_datosFila)
			return false;

		$id = $this->_idFila;

		if (!$id)		return false;

		$coma = false;
		$str  = '';
		foreach ($this->_datosFila as $key => $value) {
			//TODO: optimizar este codigo
			//TODO: array_join(... por ejemplo
			if ( intval($key) == 0 and $key != "0" and $key != $this->_nameid) {
				if ($coma)
					$str .= ",";

				if ($hackCheckBox and $value == "on")
					$value = 1;

				$value = mysql_escape_string($value);

				$str .= " $key = '".$value."'";
				$coma = true;
			}
		}

		$sql = "UPDATE ".$this->_nombretabla." SET ". $str." WHERE ".$this->_nameid." = '$id'";

		$this->result = query($sql);
		if (!$this->result) {
			$this->Error(__LINE__, "E: no pudo salvar");
			return false;
		}

		return true;
	}

	// error
	function Error($line,$text){
		
		$id = $this->_idFila;
		if (!isset($this->_idFila))
			$id = "No fila";
			
		$tab = $this->_nombretabla;
		if (!$tab){
			$tab = "No tabla";				
		}
		
		error($line ."[$tab]($id)",$text);		
	}
	
	
	/* si no se le pasa parametros, devuelve mensaje de error sin formato. 
	 *  Si se pasan parametros, ajusta mensaje de error, y devuelve 
	 * mensaje de error formateado
	 */ 
	function nicerror($msg=false){
		if (!$msg)
			return $this->_error_msg;
		
		$this->_error_msg = $msg;	
		return gas("nota",$msg);
	}	
	
	function MarcarEliminado(){				
		return $this->QuickSave("Eliminado",1);	
	}

};
?>
