<?php

function getIdMarcaFromMarca($marca){
	
	$marca = CleanRealMysql($marca);	
	$sql = "SELECT IdMarca FROM ges_marcas WHERE Marca='$marca'";
	$row = queryrow($sql);
	
	if ($row){
		return $row["IdMarca"];	
	} 
	
	return 0;
}

function getIdContenedorFromContenedor($contenedor){
	
	$contenedor = CleanRealMysql($contenedor);	
	$sql = "SELECT IdContenedor FROM ges_contenedores WHERE Contenedor='$contenedor'";
	$row = queryrow($sql);
	
	if ($row){
		return $row["IdContenedor"];	
	} 
	
	return 0;	
}
	
	
function getComboTipoImpuesto ($IdPais) {
	
	$IdPais = CleanID($IdPais);

	$sql = "SELECT TipoImpuestoDefecto, NombrePais FROM ges_paises		
	WHERE IdPais = '$IdPais'";
	
	$res = query($sql);
	
	if (!$res)
		return false;
		
	$out = "";
	while($row = Row($res)) {
		$key = $row["TipoImpuestoDefecto"];
		$value = $row["NombrePais"];
	 	$out .= "<option value='$key'>$value</option>";	
	}
	
	return $out;
}


function getComboFormatoComprobante($selected=false){

  $sql = 
    "SELECT IdComprobanteFormato, Formato ".
    "FROM   ges_comprobantesformato ".
    "ORDER  BY Formato ASC";		
  $res = query($sql);	
  if (!$res)	return false;
  
  $out = "";
  while($row = Row($res)) {
    $key 	= $row["IdComprobanteFormato"];
    $value 	= $row["Formato"];
    if ($key!=$selected)
      $out .= "<option value='$key'>$value</option>";
    else	
      $out .= "<option selected value='$key'>$value</option>";
  }
  
  return $out;
}

function genComboIdiomas($selected=false){
  $sql =
    "SELECT IdIdioma,Idioma ".
    "FROM   ges_idiomas ".
    "WHERE  Traducido = 1 ".
    "AND    Eliminado = 0 ".
    "ORDER  BY Idioma ASC";
  $res = query($sql);
  $out = '';
  if (!$res)
    return "";	
  while($row=Row($res)){
    $key 	= $row["IdIdioma"];
    $value 	= $row["Idioma"];
    
    if(getParametro("IdiomasLatin1")){
      $value = iso2utf($value);//Ha requerido una conversion, pues la tabla esta en Latin1.
    }
    
    $value_s = CleanParaWeb($value);		
    
    if ($key!=$selected)
      $out .= "<option value='$key'>$value_s</option>";
    else	
      $out .= "<option selected value='$key'>$value_s</option>";
  }
  return $out;		
}

function genComboLocales($selected=false){

  $sql = 
    "select IdLocal,NombreComercial ".
    "from   ges_locales  ".
    "where  Eliminado=0 ".
    "order  by NombreComercial ASC";
  $res = query($sql);
  $out = '';
  if (!$res) return "";	

  while($row=Row($res)){

    $key        = $row["IdLocal"];
    $value      = CleanXulLabel($row["NombreComercial"]);
    $value_s    = CleanParaWeb($value);		
    
    if ($key!=$selected)
      $out .= "<option value='$key'>$value_s</option>";
    else	
      $out .= "<option selected value='$key'>$value_s</option>";
  }
  $out = "<option value='0'>Todos</option>".$out;
  return $out;		
}


function genComboPerfiles($selected=false){
	$sql = "SELECT  IdPerfil,NombrePerfil FROM ges_perfiles_usuario  WHERE Eliminado=0 AND IdPerfil <> 7 ORDER BY NombrePerfil ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdPerfil"];
		$value = $row["NombrePerfil"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function getFamiliasProductos(){

         $sql = "SELECT IdFamilia,Familia FROM ges_familias WHERE Eliminado=0 ORDER BY Familia ASC";
	 $res = query($sql);
	 $arr = array();
	 while( $row = Row($res) ){
	   array_push($arr,$row['IdFamilia'].",".$row['Familia']);
	 }
	 return implode($arr,";");
}

function genComboFamilias($selected=false){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = $row["Familia"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboSubFamilias($selected=false, $IdFamilia=0){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = $row["SubFamilia"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}



function genArraySubFamilias($IdFamilia){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = $row["SubFamilia"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayFamilias(){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = $row["Familia"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayProveedores(){
	$sql = "SELECT IdProveedor,NombreComercial  FROM ges_proveedores WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdProveedor"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayClientes(){
	$sql = "SELECT IdCliente,NombreComercial  FROM ges_clientes WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdCliente"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayLocales(){
	$sql = "SELECT IdLocal,NombreComercial  FROM ges_locales WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdLocal"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}


function genArraySubsidiarios(){
	$sql = "SELECT IdSubsidiario,NombreComercial  FROM ges_subsidiarios WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdSubsidiario"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}
function genArrayLaboratorios(){
	$sql = "SELECT IdLaboratorio,NombreComercial  FROM ges_laboratorios WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdLaboratorio"];
		$value = $row["NombreComercial"];
		$out[$key]=$value;
	}
	return $out;		
}


function genXulComboColores($selected=false,$xul="listitem", $idfamilia=false, $autoid=false){
  $idfamilia_text= "";
  $familia  = ($idfamilia)? "AND IdFamilia='".$idfamilia."'":'';
  $IdIdioma = getSesionDato("IdLenguajeDefecto");
  $sql = "SELECT IdColor,Color  FROM ges_modelos  WHERE Eliminado=0 ".$familia." AND IdIdioma = '".$IdIdioma."' ORDER BY Color ASC";
  $res = query($sql);
  if (!$res)
		return false;
		
	$serie = array();
	$out   = "";	
	$num   = 0;		
	$ident = "";
	while($row=Row($res)){
		$color = $row["Color"];
		$serie[$color]=$row["IdColor"];
	}
			
	//while($row=Row($res)){
	foreach($serie as $value=>$idcolor){

		if ($autoid) {		
			$ident = " id='color_".$autoid."_".$num."'";
			$num ++;
		}	

		$key = $idcolor;//$row["IdColor"];
		$value = CleanXulLabel( $value );
	

		if ($key!=$selected)
			$out .= "<$xul ".$ident." label='$value' value='$key' />\n";
		else	
			$out .= "<$xul ".$ident." label='$value' value='$key' selected='true'/>\n";
	}
	return $out;		
}

function genXulComboProductoAlias($selected=false,$xul="listitem", $idfamilia=1, $autoid=false){

  $IdIdioma = getSesionDato("IdLenguajeDefecto");
  $sql = "SELECT IdProductoAlias,ProductoAlias  FROM ges_productos_alias WHERE Eliminado=0 AND IdFamilia='".$idfamilia."' AND IdIdioma = '".$IdIdioma."' ORDER BY ProductoAlias ASC";
  $res = query($sql);
  if (!$res)
		return false;
		
	$serie = array();
	$out = "";	
	$num = 0;		

	while($row=Row($res)){
		$productoalias = $row["ProductoAlias"];
		$serie[$productoalias]=$row["IdProductoAlias"];
	}
			
	//while($row=Row($res)){
	foreach($serie as $value=>$idproductoalias){

		if ($autoid) {		
			$ident = " id='alias_".$autoid."_".$num."'";
			$num ++;
		}	

		$key = $idproductoalias;//$row["IdProductoalias"];
		$value = CleanXulLabel( $value );
	

		if ($key!=$selected)
			$out .= "<$xul label='$value' value='$key' />\n";
		else	
			$out .= "<$xul label='$value' value='$key' selected='true'/>\n";
	}
	return $out;		
}


function genComboColores($selected=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdColor,Color  FROM ges_modelos  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Color ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdColor"];
		$value = NormalizaTalla($row["Color"]);
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected='true' value='$key'>$value</option>";
	}
	return $out;		
}


function genArrayColores($idfamilia){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdColor,Color  FROM ges_modelos  WHERE Eliminado=0 AND IdFamilia='$idfamilia' AND IdIdioma = '$IdIdioma' ORDER BY Color ASC";
	$res = query($sql);

	if (!$res)
		return false;
		
	$out =array();	

	while($row=Row($res)){
		$key = $row["IdColor"];
		$value = $row["Color"];
		$out[$key]=$value;
	}
        
	return $out;		
}

function genArrayProductoAlias($idfamilia){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdProductoAlias,ProductoAlias  FROM ges_productos_alias WHERE Eliminado=0 AND IdFamilia='$idfamilia' AND IdIdioma = '$IdIdioma' ORDER BY ProductoAlias ASC";
	$res = query($sql);

	if (!$res)
		return false;
		
	$out =array();	

	while($row=Row($res)){
		$key = $row["IdProductoAlias"];
		$value = $row["ProductoAlias"];
		$out[$key]=$value;
	}
        
	return $out;		
}

function getMarcasProductos(){

        $sql ="SELECT IdMarca,Marca FROM ges_marcas WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	$arr = array();
	while( $row = Row($res) ){
	  array_push($arr,$row['IdMarca'].",".$row['Marca']);
	}
    return implode($arr,";");
}

function genArrayMarcas(){
	
	$sql = "SELECT IdMarca,Marca  FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = $row["Marca"];
		$out[$key]=$value;
	}
	return $out;		
}
function genArrayTipoServicios(){
	
	$sql = "SELECT IdTipoServicio,TipoServicio,SAT  FROM ges_tiposervicio  WHERE Eliminado=0 ORDER BY TipoServicio ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$sat = ($row["SAT"])? ' - SAT':'';
		$key = $row["IdTipoServicio"];
		$value = $row["TipoServicio"].$sat;
		$out[$key]=$value;
	}
	return $out;		
}
 
function genArrayContenedores(){
	
	$sql = "SELECT IdContenedor,Contenedor  FROM ges_contenedores  WHERE Eliminado=0 ORDER BY Contenedor ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
			
	while($row=Row($res)){
		$key = $row["IdContenedor"];
		$value = $row["Contenedor"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayTallas($IdTallaje=5,$idfamilia=1){
        $idfamilia_text= "";
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdTalla,Talla  FROM ges_detalles WHERE Eliminado=0 AND IdFamilia='".$idfamilia."' AND IdTallaje='".$IdTallaje."' AND IdIdioma = '".$IdIdioma."' ORDER BY Talla ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out =array();	
	$preprocess = array();
			
	while($row=Row($res)){
		$key = $row["IdTalla"];
		$value = NormalizaTalla($row["Talla"]);
		
		if (getParametro("TallasLatin1")){
			$value = iso2utf($value);
		}	
		//$out[$key]=$value;
		$preprocess[$value] = $key;
	}
	
	foreach($preprocess as $key=>$value){
		$out[$value] = $key;			
	}	
	
	return $out;		
}

function genComboAlmacenes($selected=false) {
	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "<option value='nada'></option>";	
	foreach($arrayTodos as $key=>$value){
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;
}

function genXulComboAlmacenes($selected=false,$xul="menuitem",$callback=false) {
	$alm = new almacenes;
	$arrayTodos = $alm->listaTodosConNombre();
		
	$out = "";	
	$call = "";
	foreach($arrayTodos as $key=>$value){
		if ($callback) 
			$call = "oncommand=\"$callback('$key')\"";
			
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value' $call/>";
		else	
			$out .= "<$xul value='$key' label='$value' selected='true' $call/>";

			
	}
	return $out;
}
	
//genComboProveedores
function genComboProveedores($selected=false) {
	$sql = "SELECT IdProveedor,NombreComercial  FROM ges_proveedores  WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdProveedor"];
		$value = $row["NombreComercial"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}


function genXulComboFamilias($selected=false,$xul="listitem"){
	$sql = "SELECT IdFamilia,Familia  FROM ges_familias  WHERE Eliminado=0 ORDER BY Familia ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdFamilia"];
		$value = CleanXulLabel($row["Familia"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboAjusteOperacion($selected=false,$xul="listitem",$tipo="Salida"){
	$sql = 
	  "SELECT IdKardexAjusteOperacion,AjusteOperacion ".
	  "FROM   ges_kardexajusteoperacion ".
	  "WHERE  Eliminado      = '0' ".
	  "AND    TipoMovimiento = '".$tipo."' ".
	  "ORDER  BY AjusteOperacion ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdKardexAjusteOperacion"];
		$value = CleanXulLabel($row["AjusteOperacion"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboMotivoAlbaran($selected=false,$xul="listitem",$modulo){
	$sql = "SELECT IdMotivoAlbaran,MotivoAlbaran ".
	       "FROM   ges_motivoalbaran ".
               "WHERE  Eliminado = 0 ".
	       "AND    ".$modulo." = '1'". 
	       "ORDER  BY MotivoAlbaran ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdMotivoAlbaran"];
		$value = CleanXulLabel($row["MotivoAlbaran"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboSubsidiarios($selected=false,$xul="listitem"){
	$sql = "SELECT IdSubsidiario, NombreComercial as Subsidiario  FROM ges_subsidiarios  WHERE Eliminado=0 ORDER BY  NombreComercial ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdSubsidiario"];
		$value = CleanXulLabel($row["Subsidiario"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}
	return $out;		
}

function genXulComboStatusTrabajo($selected=false,$xul="listitem"){

	$estados = array('Pdte Envio', 'Enviado', 'Recibido', 'Entregado');

	$key = 0;
	$out = "";
	foreach ($estados as $value){		
		$value = CleanXulLabel($value);
		if ($key!=$selected)
			$out .= "<$xul value='$value' label='$value'/>\n";
		else	
			$out .= "<$xul value='$value' label='$value'/>\n";
		
		$key = $key + 1;
	}
	return $out;		
}

function genXulComboSubFamilias($selected=false, $IdFamilia=0,$xul="listitem"){
	$sql = "SELECT IdSubFamilia,SubFamilia  FROM ges_subfamilias  WHERE Eliminado=0 AND IdFamilia = '$IdFamilia' ORDER BY SubFamilia ASC";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdSubFamilia"];
		$value = CleanXulLabel($row["SubFamilia"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<listitem selected value='$key' label='$value'/>\n";
	}
	return $out;		
}


function genXulComboProveedores($selected=false,$xul="listitem",$callback=false) {

	$sql = 
	  "select IdProveedor,NombreComercial ".
	  "from   ges_proveedores  ".
	  "where  Eliminado=0 ".
	  "order  by NombreComercial ASC";

	$res = query($sql);
	if (!$res)
		return "";

	$out  = "";
	$call = "";				

	while( $row = Row( $res ) )
	  {
	    $key   = $row["IdProveedor"];
	    $value = CleanXulLabel($row["NombreComercial"]);
	    
	    if ($callback) 
	      $call = "oncommand=\"$callback('$key')\"";

	    if ($key!=$selected)
	      $out .= "<$xul value='$key' label='$value' $call/>";
	    else
	      $out .= "<$xul value='$key' label='$value' selected='true' $call/>";
	  }
	
	return $out;		
}

function genXulComboClientes($selected=false,$xul="listitem",$callback=false) {

	$sql = 
	  "select IdCliente,NombreComercial ".
	  "from   ges_clientes  ".
	  "where  Eliminado=0 and IdCliente <> 2 ".
	  "order  by NombreComercial ASC";

	$res = query($sql);
	if (!$res)
		return "";
			
	$out  = "";
	$call = "";				

	while( $row = Row( $res ) )
	  {
	    $key   = $row["IdCliente"];
	    $value = CleanXulLabel($row["NombreComercial"]);
	    
	    if ($callback) 
	      $call = "oncommand=\"$callback('$key')\"";

	    if ($key!=$selected)
	      $out .= "<$xul value='$key' label='$value' $call/>";
	    else	
	      $out .= "<$xul selected='true' value='$key' label='$value' $call/>";
	  }
	
	return $out;		
}

function genXulComboLocales($selected=false,$xul="listitem",$callback=false) {

	$sql = 
	  "select IdLocal,NombreComercial ".
	  "from   ges_locales  ".
	  "where  Eliminado=0 ".
	  "order  by NombreComercial ASC";

	$res = query($sql);
	if (!$res)
		return "";
			
	$out  = "";
	$call = "";				

	while( $row = Row( $res ) )
	  {
	    $key   = $row["IdLocal"];
	    $value = CleanXulLabel($row["NombreComercial"]);
	    
	    if ($callback) 
	      $call = "oncommand=\"$callback('$key')\"";

	    if ($key!=$selected)
	      $out .= "<$xul value='$key' label='$value' $call/>";
	    else	
	      $out .= "<$xul selected='true' value='$key' label='$value' $call/>";
	  }
	
	return $out;		
}

function genXulComboLaboratorios($selected=false,$xul="listitem") {
	$sql = "SELECT IdLaboratorio,NombreComercial  FROM ges_laboratorios  WHERE Eliminado=0 ORDER BY NombreComercial ASC";
	$res = query($sql);
	if (!$res)
		return "";
			
	$out = "";
				
	while($row=Row($res)){
		$key = $row["IdLaboratorio"];
		$value = CleanXulLabel($row["NombreComercial"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}


function genXulComboTallas($selected=false,$xul="listitem",$IdTallaje=5, $autoid=false, $idfamilia=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$familia  = ($idfamilia)? "AND IdFamilia='".$idfamilia."'":'';
	$sql = "SELECT IdTalla,Talla FROM ges_detalles   WHERE Eliminado=0 ".$familia." AND IdTallaje='".$IdTallaje."' AND IdIdioma = '".$IdIdioma."' ORDER BY Talla + 0 ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out   = "";	
	$num   = 0;		
	$id    = "";
	$ident = "";
	while($row=Row($res)){
		if ($autoid) {		
			$ident = " id='talla_".$autoid."_".$num."'";
			$num ++;
		}	
	
		$key = $row["IdTalla"];
		$value = NormalizaTalla($row["Talla"]);
		if (getParametro("TallasLatin1")){
			$value = iso2utf($value);
		}	
		
		$value = CleanXulLabel($value);
		if ($key!=$selected)
			$out .= "<$xul".$ident." label='$value' value='$key'/>\n";
		else	
			$out .= "<$xul".$ident." selected='true' value='$key' label='$value'/>\n";
	}
	return $out;		
}




function genXulComboMarcas($selected=false,$xul="listitem"){

	$sql = "SELECT IdMarca,Marca FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = CleanXulLabel($row["Marca"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboTipoServicios($selected=false,$xul="listitem"){

	$sql = "SELECT IdTipoServicio,TipoServicio,SAT FROM ges_tiposervicio  WHERE Eliminado=0 ORDER BY TipoServicio ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdTipoServicio"];
		$value = CleanXulLabel($row["TipoServicio"]);
		$value = ($row["SAT"])? $value.' - SAT':$value;

		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboTipoSuscripcion($selected=false,$xul="listitem",$xfunction=false){

	$sql = "SELECT IdTipoSuscripcion,TipoSuscripcion 
                FROM ges_suscripciontipo 
                WHERE Eliminado=0 
                ORDER BY TipoSuscripcion ASC ";

	$xfunction = ( $xfunction )? "oncommand='".$xfunction."'":"";

	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdTipoSuscripcion"];
		$value = CleanXulLabel($row["TipoSuscripcion"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value' $xfunction/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value' $xfunction />";
	}
	return $out;		
}

function genXulComboModalidadPago($selected=false,$xul="listitem"){

	$sql = "SELECT IdModalidadPago,ModalidadPago FROM ges_modalidadespago  WHERE Eliminado=0 ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	

	while($row=Row($res)){
		$key = $row["IdModalidadPago"];
		$value = CleanXulLabel($row["ModalidadPago"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else{	
			$out .= "<$xul value='$key' label='$value' selected='true'/>";
		}
	}
	return $out;		
}

function genXulComboMoneda($selected=false,$xul="listitem"){

	$sql = "SELECT IdMoneda,UPPER(Moneda) as Moneda FROM ges_moneda  WHERE Eliminado=0 ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdMoneda"];
		$value = CleanXulLabel($row["Moneda"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboContenedores($selected=false,$xul="listitem",$autoid=false){

	$sql = "SELECT IdContenedor,Contenedor FROM ges_contenedores  WHERE Eliminado=0 ORDER BY Contenedor ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out   = "";
	$num   = 0;
	$ident = "";
	while($row=Row($res)){
		if ($autoid) {		
			$ident = " id='contenedor_".$autoid."_".$num."'";
			$num ++;
		}	

		$key = $row["IdContenedor"];
		$value = CleanXulLabel($row["Contenedor"]);
		if ($key!=$selected)
			$out .= "<$xul ".$ident." value='$key' label='$value'/>";
		else	
			$out .= "<$xul ".$ident." selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genComboTallas($selected=false){
	$IdIdioma = getSesionDato("IdLenguajeDefecto");
	$sql = "SELECT IdTalla,Talla FROM ges_detalles  WHERE Eliminado=0 AND IdIdioma = '$IdIdioma' ORDER BY Talla ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = '';
			
	while($row=Row($res)){
		$key = $row["IdTalla"];
		$value = $row["Talla"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboMarcas($selected=false){

	$sql = "SELECT IdMarca,Marca FROM ges_marcas  WHERE Eliminado=0 ORDER BY Marca ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = '';
			
	while($row=Row($res)){
		$key = $row["IdMarca"];
		$value = $row["Marca"];
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}


function genComboModPagoHabitual($selected=false) {
	
	$datos = array(_("Tarjeta"),_("Transferencia"),_("Giro"),_("Envio"));	
	$out   = '';
	$key   = 0;

	foreach ($datos as $value){
		$key++;
		if ($key!=$selected)
			$out .= "<option value='$key'>$value</option>";
		else	
			$out .= "<option selected value='$key'>$value</option>";
	}
	return $out;		
}

function genComboPaises($selected=false){
	$sql = "SELECT IdPais,NombrePais  FROM ges_paises  WHERE Eliminado=0 ORDER BY NombrePais ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = '';
			
	while($row=Row($res)){
		$key = $row["IdPais"];
		$value = $row["NombrePais"];
		
		if (getParametro("PaisesLatin1")){
			$value = iso2utf($value);//Puede necesitar una conversion, si la tabla de paises esta en Latin1
		}					
		
		$value_s = CleanParaWeb($value);
		
		if ($key!=$selected)
			$out .= "<option value='$key'>$value_s</option>";
		else	
			$out .= "<option selected value='$key'>$value_s</option>";
	}
	return $out;		
}

function genXulComboUsuarios($selected=false,$xul="listitem",$idlocal=false){

	$sql = "SELECT IdUsuario,Nombre FROM ges_usuarios  WHERE Eliminado = 0 
                AND IdLocal IN ('0','$idlocal') AND IdUsuario > 2 ORDER BY Nombre ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["IdUsuario"];
		$value = CleanXulLabel($row["Nombre"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}



function genXulKardexOperaciones($selected=false,$xul="listitem"){

	$sql = 
	  "SELECT IdKardexOperacion,KardexOperacion ".
	  "FROM   ges_kardexoperacion ".
	  "WHERE  Eliminado=0 ".
	  "ORDER  BY IdKardexOperacion ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key   = $row["IdKardexOperacion"];
		$value = CleanXulLabel($row["KardexOperacion"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulKardexInventario($selected=false,$xul="listitem",$IdLocal){

   	$sql =
	  "SELECT CONCAT(IdInventario,':',IdPedido,':',IdComprobante,':',".
	  "       DATE_FORMAT(FechaInventarioInicio,'>    Fecha inventario     >   %e/%m/%y %k~%i'),".
	  "       DATE_FORMAT(FechaInventarioFin,'  a  %e/%m/%y %k~%i')) as IdInventario, ".
	  "       Estado, CONCAT(Inventario,' ',".
	  "       DATE_FORMAT(FechaInventarioInicio,'%M %Y'),' - ',Estado) as Inventario ".
	  "FROM   ges_inventario ".
	  "WHERE  Eliminado = 0 ".
	  "AND    IdLocal   = ".$IdLocal." ".
	  "ORDER   BY FechaInventarioInicio DESC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key   = $row["IdInventario"];
		$value = CleanXulLabel($row["Inventario"]);
		$id    = "inventario_".$row["Estado"];
		if ($key!=$selected)
			$out .= "<$xul id='$id' value='$key' label='$value'/>";
		else	
			$out .= "<$xul id='$id' selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboPagoDoc($selected=false,$xul="listitem",$idproveedor=false,$tipoprov){
        $Proveedor = ($idproveedor)?$idproveedor:'';
	$sql = "SELECT CONCAT(IdPagoProvDoc,'~',ges_moneda.IdMoneda,'~',".
               "Moneda,'~',CambioMoneda,'~',Saldo,'~',TipoProveedor) AS PagoDocumento,".
               "CONCAT(Codigo,' ',(SELECT ModalidadPago FROM ges_modalidadespago ".
	       "WHERE ges_modalidadespago.IdModalidadPago=ges_pagosprovdoc.IdModalidadPago),".
	       "' ',DATE_FORMAT(FechaOperacion, '%d/%m/%y %H:%i'),'   ', ".
               "Simbolo,Saldo) AS ComboDocumento ".
	       "FROM ges_pagosprovdoc  ".
	       "INNER JOIN ges_moneda ON ges_pagosprovdoc.IdMoneda = ges_moneda.IdMoneda ".
	       "WHERE ges_pagosprovdoc.Eliminado = 0 ".
	       "AND Saldo > 0 ".
	       "AND IdProveedor = '$Proveedor' ".
	       "AND TipoProveedor = '$tipoprov' ".
	       "ORDER BY FechaRegistro DESC ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	
			
	while($row=Row($res)){
		$key = $row["PagoDocumento"];
		$value = CleanXulLabel($row["ComboDocumento"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboPartidaCaja($selected=false,$xul="listitem",$idlocal=false,
				$op=false,$TipoCaja){
        $xop = ($op)? " AND TipoOperacion = '$op' ":"";
	$sql = " SELECT Codigo,PartidaCaja ".
               " FROM ges_partidascaja ".
               " WHERE Eliminado = 0 ".
               " AND IdLocal IN (0,'$idlocal') ".
	       " $xop ".
               " AND TipoCaja = '$TipoCaja' ".
               " ORDER BY IdPartidaCaja ASC";
	$res = query($sql);
	if (!$res)
		return false;

	$t = 0;
	if($op == 'Aportacion') $xitem = "partida_aportacion_def_";
	if($op == 'Sustraccion') $xitem = "partida_sustraccion_def_";
	if($op == 'Ingreso') $xitem = "partida_ingreso_def_";
	if($op == 'Gasto') $xitem = "partida_gasto_def_";
	$xiditem = $xitem.$t;
		
	$out = "<$xul value='0' label='Elige...' style='font-weight: bold' id='$xiditem'/>";
			
	while($row=Row($res)){
	        $t++;
		$key = $row["Codigo"];
		$value = CleanXulLabel($row["PartidaCaja"]);
		$xid = $xitem.$t;
		if ($key!=$selected)
		  $out .= "<$xul value='$key' label='$value' id='$xid'/>";
		else	
		  $out .= "<$xul selected='true' value='$key' label='$value' id='$xid'/>";
	}
	return $out;		
}

function genXulComboPromocionCliente($idlocal){

	$sql = " SELECT IdPromocionCliente, CategoriaCliente ".
               " FROM ges_promocionclientes ".
               " WHERE Eliminado = 0 ".
	       " AND Estado = 'Ejecucion' ".
               " AND IdLocal IN (0,'$idlocal') ".
               " ORDER BY IdPromocionCliente ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";
	$str = "";
			
	while($row=Row($res)){
		$key = $row["IdPromocionCliente"];
		$value = CleanXulLabel($row["CategoriaCliente"]);
	
		$out .= $str.$key.':'.$value;
		$str = '~';
	}
	return $out;		
}

function genXulComboHistorialVentaPeriodo(){

	$sql = " SELECT IdHistorialVentaPeriodo, Periodo ".
               " FROM ges_historialventaperiodo ".
               " WHERE Eliminado = 0 ".
               " ORDER BY IdHistorialVentaPeriodo ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";
	$str = "";
			
	while($row=Row($res)){
		$key = $row["IdHistorialVentaPeriodo"];
		$value = CleanXulLabel($row["Periodo"]);
	
		$out .= $str.$key.':'.$value;
		$str = '~';
	}
	return $out;		
}

function genArrayTipoServicio(){
	$sql = "SELECT CONCAT(IdProducto,'~',SAT) as IdProducto,
                Descripcion as Servicio 
                FROM ges_productos 
                INNER JOIN ges_tiposervicio ON ges_productos.Servicio = ges_tiposervicio.IdTipoServicio 
                INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase
                WHERE ges_productos.Eliminado=0  ORDER BY TipoServicio ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out        = array();	
	$preprocess = array();
			
	while($row=Row($res)){
		$key = $row["IdProducto"];
		$value = NormalizaTalla($row["Servicio"]);
		
		//$out[$key]=$value;
		$preprocess[$value] = $key;
	}
	
	foreach($preprocess as $key=>$value){
		$out[$value] = $key;			
	}	
	
	return $out;		
}

function genArrayModeloSat($IdMarca){
	$sql = "SELECT IdModeloSat,Modelo FROM ges_modelosat WHERE Eliminado=0 
                AND IdMarca = '$IdMarca' ORDER BY Modelo ASC";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out        = array();	
	$preprocess = array();
			
	while($row=Row($res)){
		$key = $row["IdModeloSat"];
		$value = NormalizaTalla($row["Modelo"]);
		
		//$out[$key]=$value;
		$preprocess[$value] = $key;
	}
	
	foreach($preprocess as $key=>$value){
		$out[$value] = $key;			
	}	
	
	return $out;		
}

function genXulComboMarcasSat($selected=false,$xul="listitem",$xdet){

  $out  = "<$xul value='0' label='Nuevo Marca' style='font-weight: bold' oncommand='mostrarNuevoMarca(true,$xdet)'/>";
  
  $out .= genXulComboMarcas($selected,$xul);	
  
  return $out;
}

function genXulComboProductoSat($selected=false,$xul="listitem",$xdet){
        $sql = "SELECT IdProdBaseSat as Producto, Descripcion FROM ges_productosidiomasat 
                WHERE Eliminado = 0 order by Descripcion ASC ";

	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "<$xul value='0' label='Nuevo Producto' style='font-weight: bold' oncommand='mostrarNuevoProductoSat(true,$xdet)'/>";

	while($row=Row($res)){

	  $key   = $row["Producto"];
	  $value = CleanXulLabel($row["Descripcion"]);
	  if ($key!=$selected)
	    $out .= "<$xul value='$key' label='$value'/>";
	  else	
	    $out .= "<$xul selected='true' value='$key' label='$value'/>";
	}

	return $out;
}

function genXulComboMotivoSat($selected=false,$xul="listitem"){
	$sql = "SELECT IdMotivoSat,Motivo FROM ges_motivosat WHERE Eliminado=0 
                ORDER BY Motivo ASC ";

	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "<$xul value='0' label='Nuevo Motivo' style='font-weight: bold' oncommand='mostrarNuevoMotivoSat(true)'/>";

			
	while($row=Row($res)){
		$key = $row["IdMotivoSat"];
		$value = CleanXulLabel($row["Motivo"]);
		if ($key != $selected)
			$out .= "<$xul value='$key' label='$value'/>";
		else	
			$out .= "<$xul selected='true' value='$key' label='$value'/>";
	}
	return $out;		
}

function genXulComboModeloSat($selected=false,$xul="listitem",$IdMarca,$xdet){
	$sql = "SELECT IdModeloSat,Modelo FROM ges_modelosat WHERE Eliminado=0 
                AND IdMarca = '$IdMarca' ORDER BY Modelo ASC";

	$res = query($sql);
	if (!$res)
		return false;
	$t = 0;
	$xitem   = ($xdet == 0)? "modelo_def_":"modelodet_def_";
	$xiditem = $xitem.$t;
	
	$out = "<$xul value='0' label='Nuevo Modelo' style='font-weight: bold' oncommand='mostrarNuevoModelo(true,$xdet)' id='$xiditem'/>";
			
	while($row=Row($res)){
	  $t++;
	  $key = $row["IdModeloSat"];
	  $value = CleanXulLabel($row["Modelo"]);
	  $xid = $xitem.$t;
	  if ($key!=$selected)
	    $out .= "<$xul value='$key' label='$value' id='$xid'/>";
	  else	
	    $out .= "<$xul selected='true' value='$key' label='$value' id='$xid'/>";
	}
	return $out;		
}

function genComboMetodoRedondeo($selected='RDE') {
	
	$values = array(_("SR"),_("RDE"),_("RIE"));	

	$opciones = Array();
	$opciones['SR']= 'Sin Redondeo';
	$opciones['RDE']= 'Decimal Entero';
	$opciones['RIE']= 'Decimal Entero Media';

	$out   = '';
	$key   = 0;

	foreach ($values as $value){
		
		if ($value!=$selected)
			$out .= "<option value='$value'>$opciones[$value]</option>";
		else	
			$out .= "<option selected value='$value'>$opciones[$value]</option>";
	}
	return $out;		
}

function genXulComboCuentaBancaria($selected=false,$xul="listitem",$idprov,$entfincra=""){
	$sql = "SELECT IdCuentaBancaria, CONCAT('(',Simbolo,') ',NumeroCuenta) as Cuenta  FROM ges_cuentasbancarias INNER JOIN ges_moneda ON ges_cuentasbancarias.IdMoneda = ges_moneda.IdMoneda  WHERE ges_cuentasbancarias.Eliminado=0 AND IdProveedorProv = $idprov AND EntidadFinanciera like '%$entfincra%'";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdCuentaBancaria"];
		$value = CleanXulLabel($row["Cuenta"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul value='$key' label='$value' selected='true'/>\n";
	}
	return $out;		
}

function genXulComboEntidadFinanciera($selected=false,$xul="listitem",$idprov){
	$sql = "SELECT IdCuentaBancaria, EntidadFinanciera FROM ges_cuentasbancarias WHERE ges_cuentasbancarias.Eliminado=0 AND IdProveedorProv = $idprov GROUP BY EntidadFinanciera";
	$res = query($sql);
	$out = "";
	if (!$res)
		return "";	
	while($row=Row($res)){
		$key = $row["IdCuentaBancaria"];
		$value = CleanXulLabel($row["EntidadFinanciera"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul value='$key' label='$value' selected='true'/>\n";
	}
	return $out;		
}

function genArrayCuentaBancaria($idprov=0,$xval){
        $todo = ($xval)? "":" AND ges_cuentasbancarias.IdProveedorProv = '$idprov' ";
	$sql = "SELECT IdCuentaBancaria, CONCAT(Simbolo,'~',NumeroCuenta,'~',EntidadFinanciera,'~',ges_cuentasbancarias.IdMoneda,'~',IdProveedorProv,'~',Estado,'~',Observaciones) as Cuenta ".
	       " FROM ges_cuentasbancarias ".
	       " INNER JOIN ges_moneda ON ges_cuentasbancarias.IdMoneda = ges_moneda.IdMoneda ".
	       " WHERE ges_cuentasbancarias.Eliminado=0 ".
	       " $todo ".
	       " ORDER BY EntidadFinanciera ASC ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = array();	
			
	while($row=Row($res)){
		$key = $row["IdCuentaBancaria"];
		$value = $row["Cuenta"];
		$out[$key]=$value;
	}
	return $out;		
}

function genArrayPartidas($operacion,$tipocaja,$IdLocal){
	$sql = "SELECT Codigo, PartidaCaja as Partida ".
	       " FROM ges_partidascaja ".
	       " WHERE ges_partidascaja.Eliminado = 0 ".
	       " AND ges_partidascaja.IdLocal IN (0,$IdLocal) ".
	       " AND ges_partidascaja.TipoCaja = '$tipocaja' ".
	       " AND ges_partidascaja.TipoOperacion = '$operacion' ".
	       " ORDER BY PartidaCaja ASC ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = array();	
			
	while($row=Row($res)){
		$key       = $row["Codigo"];
		$value     = $row["Partida"];
		$out[$key] = $value;
	}
	return $out;		
}

function genComboModalidadPago($selected=false,$xul="listitem"){

	$sql = "SELECT IdModalidadPago,ModalidadPago FROM ges_modalidadespago  WHERE Eliminado=0 ";
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	

	while($row=Row($res)){
		$key = $row["IdModalidadPago"];
		$value = CleanXulLabel($row["ModalidadPago"]);
		if ($key!=$selected)
			$out .= "<option value='$key'> $value</option>";
		else{	
			$out .= "<option value='$key' selected>$value</option>";
		}
	}
	return $out;		
}

function genXulComboCuentaBancaria2($selected=false,$xul="listitem",$xidm){
        $xmda = ($xidm)? " AND ges_moneda.IdMoneda = '$xidm' ":'';
	$sql = "SELECT IdCuentaBancaria,CONCAT(Simbolo,' ',EntidadFinanciera,' ',NumeroCuenta) as Cuenta FROM ges_cuentasbancarias INNER JOIN ges_moneda ON ges_cuentasbancarias.IdMoneda = ges_moneda.IdMoneda  WHERE ges_cuentasbancarias.Eliminado=0 AND IdProveedorProv = 0 ".$xmda;
	$res = query($sql);
	if (!$res)
		return false;
		
	$out = "";	

	while($row=Row($res)){
		$key = $row["IdCuentaBancaria"];
		$value = CleanXulLabel($row["Cuenta"]);
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else	
			$out .= "<$xul value='$key' label='$value' selected='true'/>\n";
	}
	return $out;		
}


?>
