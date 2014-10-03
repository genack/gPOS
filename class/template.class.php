<?php

function getIdTemplateFromNombre($nombre){

	$sql = "SELECT IdTemplate FROM ges_templates WHERE Nombre='$nombre'";
	$row = queryrow($sql);
	
	if(!$row)	
		return;	
	
	return $row["IdTemplate"];
}

function jsPaginador($indice,$tamPagina,$num){
				
		$pag = intval($indice/$tamPagina) + 1;		
		$pagnext = $pag + 1;
		$pagprev = $pag - 1;		
	
		if ($pagprev<0)
			$pagprev =0;

		if ($indice>0)
			$pagmenos= "1";
		else 
			$pagmenos= "0";		
			
		if ($num>=$tamPagina)			
			$pagmas= "1";
		else 
			$pagmas= "0";
		
		return "genPaginador($pagmenos,$pagmas,$num);";							
}

function getTemplate($nombre){	
	//ATENCION: esta funcion cogera el template de la sesion!!
	//TODO: cargar todas al hacer login, y recuperarlas aqui desde la SESION
	
	$ot = getSesionDato("Template_$nombre");
	if ($ot){	
		//error(0,"retorna $ot");	
		return unserialize($ot);		
	}	
	
	
	$ot = new template;
	
	if ($ot->LoadByName($nombre)){		
		$_SESSION["Template_$nombre"] = serialize($ot);	
		return $ot;
	}
	
	echo "Error: No encontrado template '<a href='modtemplates.php?modo=alta'>$nombre<br>'";
	//http://www.casaruralvirtual.com/misc/9gestion/
	return false;	
}


function TemplateFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdTemplate"];
	
	$oTemplate = new template;
		
	if ($oTemplate->Load($id)){
		return $oTemplate;
	}
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}

class template extends Cursor {

	var $actual;
	var $series;

    function template() {
    }

	
	//Trabajar en vivo
	function fijar($key,$value=""){
		//error(0,"Info: '$key' to '$value'");
		$this->actual = str_replace("%$key%",$value,$this->actual);
	}
	
	function fijarSerie($key,$value) {
		$num = 0;
		if(isset($this->series[$key]))	$num = $this->series[$key];
		
		$num = intval($num + 1);
		$this->series[$key] = $num;
		$this->fijar($key.$num,$value);	
		$this->activaSerie($num);
	}
	
	function terminaSerie($num=false){
		if (!is_array($this->series))
			return;
		if (!$num)
			$num = $this->get("Paginas") + 1;	
			
		for($t=0;$t<$num;$t++)
			$this->eliminaSeccion($t);	
			
		foreach ($this->series as $key=>$value){
			//error(0,"Info: '$key' from value '$value'");
			$value = intval($value);
			for($t=$value;$t<$num;$t++){
				$this->fijarSerie($key,"");				
			}	
		}	
	}
	
	function activaSerie($num){
		$this->actual = str_replace("<$num>","",$this->actual);
		$this->actual = str_replace("</$num>","",$this->actual);	
	}
	
	function resetSeries($serie=false){
		$num = 0;
		if (!$serie)
			$this->series = array();
		else	{
			$ar = array();
			foreach ($serie as $key){
				$ar[$key] = 0;
				//echo "reseting $key<br>";
			}
			$this->series = $ar;
		}
		
	}

	function eliminaRestos($desde, $hasta) {
		//obsoleto
		for($t=$desde;$t<=$hasta;$t++)
			$this->eliminaSeccion("q". $t);
	}
	
	function Recarga(){
		$this->actual = $this->getCodigo();
	}
	
	function Output($ponFirma=true){
		if ($ponFirma)
			return $this->actual . $this->Firma();
		else		
			return $this->actual;
	}

	function Firma() {		
		
		if(!isUsuarioAdministradorWeb())
			return "";
		
		$name = $this->getNombre();
		return 	"<br><a onmouseover='window.status=\"Editar $name\";return true;' title='Editar $name' href='#' onclick='editatemplate(".$this->getId().")'>#</a>";
		//return "";
	}

	//Cargar desde identificadores

	function LoadByName($nombre){
		$sql = "SELECT IdTemplate FROM ges_templates WHERE Nombre = '$nombre'";
		$res = query($sql);
		if (!$res){
			return false;			
		}
		
		$row = Row($res);		
		if (!$row){
			return false;
		}
		
		$id = $row["IdTemplate"];
		if (!$this->Load($id)){
			return false;				
		}		
		return true;
	}


    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_templates", "IdTemplate", $id);
		
		if ($this->getResult()){
			$this->Recarga();	
		}		
		return $this->getResult();
	}
	
	//Trabajar en lotes	
	function makear($cambios){
		$codigo = $this->getFichero("Codigo");
		foreach ($cambios as $key=>$value){
			$codigo = str_replace("%$key%",$value,$codigo);	
		}
		$codigo .= $this->Firma();
		return $codigo;
	}
	
	function cambiacodigo($newcodigo){		
		$this->setCodigo($newcodigo);
		$this->QuickSave("Codigo",$this->get("Codigo"));	
	}	
	
	function setNombre($nombre){
		$this->set("Nombre",$nombre,FORCE);	
	}
	
	function getNombre(){
		return $this->get("Nombre");
	}
	
	function getCodigo(){
		return $this->getFichero("Codigo");
	}
	
	
		//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
		
		if($esModificar) {
			//$out = gas("titulo",_("Modificando template ") ."'". $this->get("Nombre") ."'");
			$cambiarnombre = "<input name='Nombre' value='".$this->getNombre()."'>";
		} else {
			//$out = gas("titulo",_("Nueva Template"));
			$cambiarnombre = "<input name='Nombre' value='NuevaTemplate'>";
		}
		 
		
		$codigo =  $this->getCodigo();
		
		$codigo = str_replace("<textarea","<#textarea",$codigo);
		$codigo = str_replace("/textarea>","/#textarea>",$codigo);
		$out    = "";
		$out .= "<table><tr><td><input  type=submit> - $cambiarnombre</td></tr>".
		"<tr><td><textarea ROWS='30' cols='115'  class='fullscreen'  name='Codigo'>" . $codigo . "</textarea></td>".		
		"</table>";							
		
		$modo = "newsave";
		if ($esModificar) {
			$modo ="modsave";
			$extra = "<input type='hidden' onblur='this.rewindFocus()' name='id' style='-moz-user-focus: ignore' value='".$this->getId()."'>";
		}
		
		return "<form action='$action?modo=$modo' method=post>$out $extra</form>";					
	}

	function formExtra($action){
		

		$out = gas("titulo",_("Datos extra"));
		
		$cambiarnombre = "<input SIZE=80 name='Comentario' value='".$this->get("Comentario")."'>";
		$cambiarpaginas = "<input name='Paginas' value='".$this->get("Paginas")."'>";		 		
		
		$out .= "<table><tr><td>". Enviar(_("Guardar")) . "</td></tr>".
		"<tr><td>$cambiarnombre - Comentario</td></tr>" .
		"<tr><td>$cambiarpaginas - Paginas</td></tr>" .
		"<tr><td>". Enviar(_("Guardar")) . "</td></tr>".
		"</table>";							
		
		$modo = "newsave";
		$extra = Hidden("id",$this->getId());		
		return "<form action='$action?modo=salvarextra' method=post>$out $extra</form>";					
	}
		
	function Crea(){
		$this->set("Nombre","Nueva template",FORCE);	
	}
	
	function setCodigo($newcodigo){
		$this->setFichero("Codigo",$newcodigo);
	}
	
	
	function ejecutar() {
		$codigo = $this->actual;
		eval("?>".$codigo."<?");
	}
   
   
	function Alta(){
	
		$data = $this->export();
		
		$coma = false;
		$listaKeys = "";
		$listaValues = "";
		
		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}
			
			$listaKeys .= " $key";
			$listaValues .= " '$value'";
			$coma = true;															
		}
	
		$sql = "INSERT INTO ges_templates ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}
	
	function eliminaSeccion($key){
		//$this->actual  = preg_replace("/\<".$key."\>(.|\n)*\<\/".$key."\>/" , "" , $this->actual);
		$this->actual = str_replace("<$key>","<!--",$this->actual);
		$this->actual = str_replace("</$key>","-->",$this->actual);		
	}
	
	function confirmaSeccion($key){
		$this->actual = str_replace("<$key>","",$this->actual);
		$this->actual = str_replace("</$key>","",$this->actual);		
	}

	
	function jsPaginador($indice,$tamPagina=false,$num){
		
		if (!$tamPagina)
			$tamPagina= $this->get("Paginas");
		
		$pag = intval($indice/$tamPagina) + 1;		
		$pagnext = $pag + 1;
		$pagprev = $pag - 1;		
	
		if ($pagprev<0)
			$pagprev =0;

		if ($indice>0)
			$pagmenos= "1";
		else 
			$pagmenos= "0";		
			
		if ($num>=$tamPagina)			
			$pagmas= "1";
		else 
			$pagmas= "0";
		
		return "genPaginador($pagmenos,$pagmas,$num);";							
	}
	
	
	function paginador($indice,$tamPagina=false,$num){
		
		if (!$tamPagina)
			$tamPagina= $this->get("Paginas");
		
		$pag = intval($indice/$tamPagina) + 1;
		$out =  "";//era "<span class=pagactual><b> [ <i>".  $pag . "</i> ] </b></span>"
		$pagnext =$pag + 1;
		$pagprev =$pag - 1;
		
	
		if ($pagprev<0)
			$pagprev =0;

		if ($indice>0)
			$this->fijar("tPaginaMenos","&lt;&lt;");
		else {
			$this->fijar("tPaginaMenos","");
			$this->eliminaSeccion("paginamenos");
		}
			
		if ($num>=$tamPagina)
			$this->fijar("tPaginaMas","&gt;&gt;");
		else {
			$this->fijar("tPaginaMas","");
			$this->eliminaSeccion("paginamas");
		}
		
		if ($indice>0 or $num>=$tamPagina)
			$this->fijar("tPagina",$out);
		else {
			$this->fijar("tPagina","");
			$this->eliminaSeccion("enpagina");
		}
			
		$this->fijar("Encontrados",_("Encontrados :") .$num);		
	}

	function campo($texto, $campo,$valor=false){
		$this->fijar("t$campo",$texto);
		if ($valor)	{
			if (is_array($valor))
				$this->fijar("v$campo",$valor);
			else
			if (is_object($valor))
				$this->fijar("v$campo",$valor->get($campo));
			else
				$this->Error(__FILE__ . __LINE__ ,"E: tipo desconocido");
		}
		else
			$this->fijar("v$campo","");						
	}
	
	function getPagina(){
		return $this->get("Paginas");	
	}

	function PupulaMensajeros(){	
		$out = "";
		for($t=0;$t<10;$t++){
			$out .= "<iframe class=mensajero style='visibility: hidden' src='' id='mensajero$t' name='mensajero$t'></iframe>";	
		}
		$this->fijar("Mensajeros",$out);		
	}
	
}


?>
