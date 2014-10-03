<?php


function getNombreLocalId($id){
	$id = CleanID($id);
	
	$keyname = "tLOCAL_".$id;
	
	if (isset($_SESSION[$keyname]) and $_SESSION[$keyname]) return $_SESSION[$keyname];	
	
	$sql = "SELECT Identificacion FROM ges_locales WHERE IdLocal = '$id'";
	
	$row = queryrow($sql);
	
	if (!$row) return false;
	
	$nombre = $row["Identificacion"];
	$_SESSION[$keyname] = $nombre;
	return $nombre; 	
}

function getNombreComercialLocal($id){
	$id = CleanID($id);
	$sql = "SELECT NombreComercial FROM ges_locales WHERE IdLocal = '$id'";
	$row = queryrow($sql);
	
	if (!$row) return false;
	
	$nombre = $row["NombreComercial"];

	return $nombre; 	
}


function getLocalesPrecios($IdLocal){
    $sql="SELECT IdLocal, NombreComercial
          FROM   ges_locales
          WHERE  Eliminado = '0' 
          AND    IdLocal <> '$IdLocal'
          ORDER  BY IdLocal ASC ";
    $res = query($sql);
    $arr = array();
    while( $row = Row($res) ){
               array_push($arr,$row['IdLocal'].",".$row['NombreComercial']);
    }
    return implode($arr,";");
}

function getLocalesPedidos(){
    $sql=
      "SELECT   ges_locales.IdLocal, NombreComercial ".
      "FROM     ges_locales, ges_pedidos ".
      "WHERE    ges_pedidos.TipoOperacion='TrasLocal' ".
      "AND      ges_locales.IdLocal = ges_pedidos.IdLocal ".
      "GROUP BY ges_locales.IdLocal";
    $res = query($sql);
    $arr = array();
    while( $row = Row($res) ){
               array_push($arr,$row['IdLocal'].",".$row['NombreComercial']);
    }
    return implode($arr,";");
}


function getNombreLegalLocalId($id){
	$id = CleanID($id);
	$sql = "SELECT NombreLegal FROM ges_locales WHERE IdLocal = '$id'";
	$row = queryrow($sql);
	if (!$row) return false;
	return  $row["NombreLegal"];
}

function getPoblacionLocalId($id){
	$id = CleanID($id);
	$sql = "SELECT Poblacion FROM ges_locales WHERE IdLocal = '$id'";
	$row = queryrow($sql);
	if (!$row) return false;
	return  $row["Poblacion"];
}

function getPaginaWebLocalId($id){
	$id = CleanID($id);
	$sql = "SELECT PaginaWeb FROM ges_locales WHERE IdLocal = '$id'";
	$row = queryrow($sql);
	if (!$row) return false;
	return $row["PaginaWeb"];
}

function getMesFromId($id){
  $id = CleanID($id);
  switch ($id) {
  case 1:
    return "enero";
    break;
  case 2:
    return "febrero";
    break;
  case 3:
    return "marzo";
    break;
  case 4:
    return "abril";
    break;
  case 5:
    return "mayo";
    break;
  case 6:
    return "junio";
    break;
  case 7:
    return "julio";
    break;
  case 8:
    return "agosto";
    break;
  case 9:
    return "septiembre";
    break;
  case 10:
    return "octubre";
    break;
  case 11:
    return "noviembre";
    break;
  case 12:
    return "diciembre";
    break;
  }     
}


function LocalFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdLocal"];
	
	$oLocal = new local;
		
	if ($oLocal->Load($id))
		return $oLocal;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class local extends Cursor {
      function local() {
    	return $this;
      }
    
      function Load($id) {
	$id = CleanID($id);
	$this->setId($id);
	$this->LoadTable("ges_locales", "IdLocal", $id);
	return $this->getResult();
      }
      

      function LoadCentral() {		
	$sql = "SELECT IdLocal FROM ges_locales WHERE AlmacenCentral = 1";
	
	$row = queryrow($sql);
	
	if (!is_array($row)){
	  return false;	
	}
	
	return $this->Load($row["IdLocal"]);			
      }
      
      
      // SET especializados    
      function setNombreComercial($nombre){    	
    	$this->set("NombreComercial",$nombre,FORCE);	
      }
      
      // GET especializados
      function getNombre(){
    	return $this->get("NombreComercial");	
      }
      
      function getPerfil(){
    	return $this->get("Perfil");
      }
      
      //Formulario de modificaciones y altas
      function old_formEntrada($action,$esModificar){
	if($esModificar)
	  $out = gas("titulo",_("Modificando local"));
	else
	  $out = gas("titulo",_("Nuevo local"));
	
	$out .=   "<table><tr>
		  <td>Nombre comercial</td><td>" . 
	          Input("NombreComercial",$this->getNombre()) . 
		  "</td><tr>".
		  "<tr><td></td><td>". Enviar(_("Guardar")) . "</td></tr>".
		  "</table>";							
	
	$modo = "newsave";
	if ($esModificar) {
	  $modo ="modsave";
	  $extra = Hidden("id",$this->getId());
	}
	
	return "<form action='$action?modo=$modo' method=post>$out $extra</form>";
      }
      
      //Formulario de modificaciones y altas
      function formEntrada($action,$esModificar){
	
	$ot = ($esModificar)? getTemplate("ModLocal") : getTemplate("AltaLocal");
	
	if (!$ot) return false;
	
	$modo            = ($esModificar)? "modsave":"newsave";
	$titulo          = ($esModificar)? _("Modificando local"):_("Nuevo local");
	$idlocal         = $this->get("IdLocal");
	
	
	$combonumeracion = getComboFormatoComprobante($this->get("IdTipoNumeracionFactura"));
	$comboidiomas    = genComboIdiomas($this->get("IdIdioma"));
	$incluido        = ( $this->is("ImpuestoIncluido") )? "checked":"";
	$esCentral       = ( $this->is("AlmacenCentral")   )? "checked":"";
	$esMoneda        = ( $this->is("AlmacenCentral")   )? "":"style='display:none'";
	$Moneda          = getMoneda();
	$cambios = array(
			 "tMensajeMes" => _("Mensaje Ticket"),
			 "vMensajeMes" => $this->get("MensajeMes"),
			 "tMensajePromo" => _("Mensaje Promoción"),
			 "vMensajePromo" => $this->get("MensajePromocion"),
			 "tVigenciaPresupuesto" => _("Vigencia Presupuesto"),
			 "vVigenciaPresupuesto" => $this->get("VigenciaPresupuesto"),
			 "tGarantiaComercial" => _("Garantía Comercial"),
			 "vGarantiaComercial" => $this->get("GarantiaComercial"),
			 "tMargenUtilidad" => _("Margen Utilidad "),
			 "vMargenUtilidad" => $this->get("MargenUtilidad"),
			 "vTipoMargenUtilidad" => $this->get("TipoMargenUtilidad"),
			 "vIGV" => $this->get("Impuesto"),
			 "vIPC" => $this->get("Percepcion"),
			 "tIdIdioma" => _("Idioma"),
			 "comboIdiomas" =>$comboidiomas,				
			 "tIdPais" => _("País"),
			 "vIdPais" => $this->get("IdPais"),
			 "comboIdPais" => genComboPaises($this->get("IdPais")),
			 "TITULO" => $titulo,
			 "tImpuestoIncluido"=> _("Impuesto incluido"),
			 "cImpuestoIncluido"=> $incluido,
			 "tTipoNumeracionFactura" => _("Tipo numeración fact."),
			 "comboTipoNumeracionFactura" => $combonumeracion,
			 "vNombreComercial" => $this->get("NombreComercial"),
			 "vNombreLegal" => $this->get("NombreLegal"),
			 "vAlmacenCentral" => $esCentral,
			 "vMoneda0" => $Moneda[1]['T'],
			 "vMonedaPlural0" => $Moneda[1]['TP'],
			 "vMonedaSimbolo0" => $Moneda[1]['S'],
			 "vMoneda1" => $Moneda[2]['T'],
			 "vMonedaPlural1" => $Moneda[2]['TP'],
			 "vMonedaSimbolo1" => $Moneda[2]['S'],
			 "vMoneda" => $esMoneda,
			 "vPoblacion" => $this->get("Poblacion"),
			 "vCodigoPostal" => $this->get("CodigoPostal"),
			 "vDireccionFactura" => $this->get("DireccionFactura"),
			 "DireccionFactura" => _("Dirección Factura"),
			 "vNFiscal" => $this->get("NFiscal"),
			 "NFiscal" => _("Número Fiscal"),
			 "vFax" => $this->get("Fax"),
			 "vEmail" => $this->get("Email"),
			 "vMovil" => $this->get("Movil"),
			 "vTelefono" => $this->get("Telefono"),
			 "vPaginaWeb" => $this->get("PaginaWeb"),
			 "vCuentaBancaria" => $this->get("CuentaBancaria"),
			 "Password" =>_("Contraseña"),			
			 "vPassword" => $this->get("Password"),
			 "Ver" => _("Ver"),
			 "Identificacion" => _("Identificación"),
			 "vIdentificacion" => $this->get("Identificacion"),
			 "NombreComercial" => _("Nombre comercial"),
			 "NombreLegal" => _("Nombre legal"),
			 "Poblacion" => _("Población"),
			 "CodigoPostal" => _("Codigo Postal"),
			 "Fax" => _("Fax"),
			 "Email" => _("Email"),
			 "Movil" => _("Móvil"),
			 "Telefono" => _("Teléfono"),
			 "PaginaWeb" => _("Pagina web"),
			 "CuentaBancaria" => _("Cuenta bancaria"),
			 "tAlmacenCentral" => _("El almacén central"),
			 "HIDDENDATA" => Hidden("id",$this->getId()),
			 "ACTION" => "$action?modo=$modo",

			 );

	return $ot->makear($cambios);

      }
      
      
      function Crea(){
	$this->set("NombreComercial",_("Nuevo local"),FORCE);		
	$this->set("Identificacion",genMakePass(),FORCE);
	$this->set("Password",genMakePass(),FORCE);					
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
	  
	  $listaKeys .= " $key";
	  $listaValues .= " '$value'";
	  $coma = true;
	}
	
	$sql = "INSERT INTO ges_locales ( $listaKeys ) VALUES ( $listaValues )";
	
	$res = query($sql,'Alta de local en locales');				
	$IdLocalCreado = $UltimaInsercion; 
	$this->set("IdLocal",$IdLocalCreado,FORCE);

	//Nels: Inicialización de ges_comprobantestipo
	setTipoComprobantesLocal($IdLocalCreado);	

	$sql = "SELECT IdLocal FROM ges_locales WHERE Eliminado=0 ORDER BY IdLocal ASC";
	$row = queryrow($sql);		
	$IdLocalUsable = $row["IdLocal"]; //Vamos a clonar los productos desde este almacen		
	//TODO: salir con error si no hay ningun local. Siempre deberia haber al menos un local,
	// el añadido durante el proceso de instalación.		
	
	
	if ($IdLocalCreado){			
	  
	  $sql = "SELECT * FROM ges_almacenes WHERE (IdLocal='$IdLocalUsable')";			
	  $res = query($sql);			
	  while( $row = Row($res) ){			
	    $IdProducto = $row["IdProducto"];
	    $PrecioVenta = $row["PrecioVenta"];
	    $TipoImpuesto = $row["TipoImpuesto"];
	    $Impuesto = $row["Impuesto"];
	    $Disponible = 1;//??
	    $Oferta = $row["Oferta"];
	    $Eliminado = $row["Eliminado"];
	    $Unidades = 0;//Empieza el almacen vacio
	    $StockMin = 0;//$row["StockMin"];
	    $StockIlimitado = 0;//$row["StockIlimitado"];
	    $DisponibleOnline = 0;//$row["DisponibleOnline"];
	    $DescuentoOnline  = 0;
	    $Oferta = 0;//$row["Oferta"];
	    
	    $newsql = "INSERT INTO `ges_almacenes` 
                                          (`IdLocal`, `IdProducto`, `Unidades`, `StockMin`, 
			                   `PrecioVenta`, `PVODescontado`, `TipoImpuesto`, 
                                           `Impuesto`, `StockIlimitado`, `Disponible`, 
			                   `DisponibleOnline`, `Eliminado`, `Oferta`)
                                          VALUES 
                                          ('$IdLocalCreado', '$IdProducto', '$Unidades', 
                                           '$StockMin', '$PrecioVenta', '$DescuentoOnline',
			                   '$TipoImpuesto', '$Impuesto', '$StockIlimitado', 
                                           '$Disponible', '$DisponibleOnline', '$Eliminado', 
                                           '$Oferta')";
	    
	    query($newsql);				
	  }
	}
	
	return true;		
	
      }

      function IniciarArqueos(){
	
	$IdLocal = $this->get("IdLocal");
	$TipoVenta = getSesionDato("TipoVentaTPV");
	$sql = 
	  " INSERT INTO `ges_arqueo_caja` ".
	  " (IdLocal,esCerrada,TipoVentaOperacion ) ".
	  " VALUES ".
	  " ('$IdLocal',1,'$TipoVenta')";
	query($sql,'Iniciando arqueos');	
      }

      
      function Modificacion(){
	return $this->Save();		
      }

}

function esCerradaArqueoCaja($IdLocal){
  $TipoVenta = getSesionDato("TipoVentaTPV");
  
  $sql = 
    "SELECT esCerrada ".
    "FROM   ges_arqueo_caja ".
    "WHERE  IdLocal   = '$IdLocal' ".
    "AND    Eliminado = 0 ".
    "AND    esCerrada = 0 ".
    "AND    TipoVentaOperacion  = '$TipoVenta'".
    "ORDER  BY FechaCierre DESC";
  $row = queryrow($sql,'Buscando arqueo abierto');
  $esClose=$row["esCerrada"];
  if ($esClose=='0')  
    echo "false";
  else
    echo "true";
}	
function esTipoVenta($TipoVenta){
      	switch($TipoVenta){
		case "rd":
		  return true;
		case "rc":
		  return true;
		default:	
		  return false;
 	}
}

function getTipoVenta($TipoVenta){
      	switch($TipoVenta){
		case "rd":
		  return "VD";
		case "rc":
		  return "VC";
		default:	
		  return false;
 	}
}
function setTipoVenta($TipoVenta){
        $TipoVenta = getTipoVenta($TipoVenta); 	
        setSesionDato("TipoVentaTPV",$TipoVenta);
}

function getMoneda(){

        $sql = 
	  " select IdMoneda,Simbolo,Moneda,MonedaEnPlural ".
	  " from   ges_moneda ".
	  " where  (Eliminado=0)";			
	$res     = query($sql);			
	$aModeda = array();
	while( $row = Row($res) )
	  {			
	    $aModeda[ $row["IdMoneda"] ]['S'] = $row["Simbolo"];
	    $aModeda[ $row["IdMoneda"] ]['T'] = $row["Moneda"];
	    $aModeda[ $row["IdMoneda"] ]['TP'] = $row["MonedaEnPlural"];
	  }
	return $aModeda;
}

function setMoneda($moneda0,$moneda0plural,$moneda0simbolo,
		   $moneda1,$moneda1plural,$moneda1simbolo){

  $sql =
    " update ges_moneda ".
    " set    Moneda='$moneda0',MonedaEnPlural='$moneda0plural',Simbolo='$moneda0simbolo' ".
    " where  IdMoneda=1";
  query($sql);
  $sql =
    " update ges_moneda ".
    " set    Moneda='$moneda1',MonedaEnPlural='$moneda1plural',Simbolo='$moneda1simbolo' ".
    " where  IdMoneda=2";
  query($sql);
 
}

function getMonedaJS($Moneda){

echo "
       <script>//<![CDATA[
       var cMoneda = new Array();
       cMoneda[1] = new Array();
       cMoneda[1]['S'] = '".$Moneda[1]['S']."';
       cMoneda[1]['T'] = '".$Moneda[1]['T']."';
       cMoneda[1]['TP'] = '".$Moneda[1]['TP']."';

       cMoneda[2] = new Array();
       cMoneda[2]['S'] = '".$Moneda[2]['S']."';
       cMoneda[2]['T'] = '".$Moneda[2]['T']."';
       cMoneda[2]['TP'] = '".$Moneda[2]['TP']."';
       //]]></script>
";
  
}

?>
