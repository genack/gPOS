<?php

function ClienteFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdCliente"];
	
	$oCliente = new cliente;
		
	if ($oCliente->Load($id))
		return $oCliente;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}



function CrearCliente($comercial, $legal, $direccion, $poblacion, $cp, $email, $telefono1, 
		      $telefono2, $contacto, $cargo, $cuentabancaria, $numero, $comentario,
		      $tipocliente, $IdModPagoHabitual, $idpais,$paginaweb,$IdLocal=false,
		      $FechaNacimiento) {

        $comercial = str_replace('&','&#038;',$comercial);
        $legal     = str_replace('&','&#038;',$legal);
        if (!$IdLocal)
	  $IdLocal = getSesionDato("IdTienda");

	$oCliente = new cliente;
	$oCliente->Crea();

	$oCliente->set("NombreComercial", $comercial, FORCE);
	$oCliente->set("NombreLegal", $legal, FORCE);
	$oCliente->set("Direccion", $direccion, FORCE);
	$oCliente->set("Localidad", $poblacion, FORCE);
	$oCliente->set("CP", $cp, FORCE);
	$oCliente->set("Email", $email, FORCE);
	$oCliente->set("Telefono1", $telefono1, FORCE);
	$oCliente->set("Telefono2", $telefono2, FORCE);
	$oCliente->set("Contacto", $contacto, FORCE);
	$oCliente->set("Cargo", $cargo, FORCE);	
	$oCliente->set("CuentaBancaria", $cuentabancaria, FORCE);
	$oCliente->set("NumeroFiscal", $numero, FORCE);
	$oCliente->set("Comentarios", $comentario, FORCE);
	$oCliente->set("TipoCliente", $tipocliente, FORCE);
	$oCliente->set("IdPais", $idpais, FORCE);
	$oCliente->set("PaginaWeb", $paginaweb, FORCE);
	$oCliente->set("FechaRegistro", "NOW()", FORCE);
	$oCliente->set("FechaChange", "NOW()", FORCE);
	$oCliente->set("IdLocal", $IdLocal, FORCE);
	$oCliente->set("FechaNacimiento", $FechaNacimiento, FORCE);
	$oCliente->set("IdModPagoHabitual", $IdModPagoHabitual, FORCE);
	
	if ($oCliente->Alta()) {
		//if(isVerbose())		
		//	echo gas("aviso", _("Nuevo cliente registrado"));
		return $oCliente->get("IdCliente");
	} else {
		//echo gas("aviso", _("No se ha podido registrar el nuevo producto"));
		return false;
	}

}


class cliente extends Cursor {
    function cliente() {
    	return $this;
    }
    
    function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_clientes", "IdCliente", $id);
		return $this->getResult();
	}
    
    
    // SET especializados    
    function setNombre($nombre){    	
    	$this->set("NombreComercial",$nombre,FORCE);	
    }
    
    function esEmpresa() {    
    	return $this->get("TipoCliente")=="Empresa";	    	
    }

    function esParticular() {    
    	return $this->get("TipoCliente")=="Particular";	    	
    }
    
    // GET especializados
    function getNombre(){
    	return $this->get("NombreComercial");	
    }
    
    function getCliente(){
    	return $this->get("NombreComercial");
    }
	
	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
		$ot = getTemplate("ModificarCliente");
		if (!$ot){		return false;		}
								
		$comboidiomas = genComboIdiomas($this->get("IdIdioma"));
		$comboperfiles = genComboPerfiles($this->get("IdPerfil"));
									
		$cambios = array(	
			"tIdPais" => _("País"),
			"vIdPais" => $this->get("IdPais"),
			"comboIdPais" => genComboPaises($this->get("IdPais")),
			"TITULO" => _("Modificando cliente"),	
			"Direccion" => _("Dirección"),
			"Comision" => _("Comisión"),
			"Ver" => _("Ver"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"comboIdiomas" => $comboidiomas,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,	
			"vNombre" => $this->getNombre(),
			"vDireccion"=>$this->get("Direccion"),
			"vComision"=>$this->get("Comision"),
			"vTelefono"=>$this->get("Telefono"),		
			"ACTION" => "$action?modo=modsave",
			"HIDDENDATA" => Hidden("id",$this->getId())
		);

		return $ot->makear($cambios);		
				
	}
	
	function formAlta($action){
		$ot = getTemplate("AltaCliente");
		if (!$ot){		return false;		}
		
		$comboidiomas = genComboIdiomas();
		$comboperfiles = genComboPerfiles();
		
		$cambios = array(
			"tIdPais" => _("País"),
			"vIdPais" => $this->get("IdPais"),
			"comboIdPais" => genComboPaises($this->get("IdPais")),	
			"TITULO" => _("Alta cliente"),
			"Ver" => _("Ver"),	
			"Direccion" => _("Dirección"),
			"Poblacion" => _("Población"),
			"Comision" => _("Comisión"),
			"Telefono" => _("Teléfono"),
			"Nombre" => _("Nombre"),
			"Idioma" => _("Idioma"),
			"Ver" => _("Ver"),
			"comboIdiomas" => $comboidiomas,
			"Perfil" => _("Perfil"),
			"comboPerfiles" => $comboperfiles,			
			"vNombre" => $this->getNombre(),
			"TEXTNOMBRE" => _("Nombre perfil"),
			"ACTION" => "$action?modo=newsave",
		);

		return $ot->makear($cambios);
	}
	
	function Crea(){
		$this->setNombre(_("Nuevo cliente"));
		//$this->set("FechaNacim","1974-09-01",FORCE);
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

			if($key=='FechaRegistro' or $key=='FechaChange')
			  $listaValues .= " $value ";
			else
			  $listaValues .= " '$value'";
			
			$coma = true;															
		}

		
		$sql = "INSERT INTO ges_clientes ( $listaKeys ) VALUES ( $listaValues )";
		
		$res = query($sql,"Alta cliente");
		
		if ($res) {		
			$id = $UltimaInsercion;	
			$this->set("IdCliente",$id,FORCE);
			return $id;			
		}
						
		return false;				 		
	}

	function Listado($lang,$min=0,$nombre=false){
	  $extra = ($nombre)? " AND ges_clientes.NombreComercial like '%$nombre%' OR (NombreLegal like '%$nombre%' OR NumeroFiscal like '%$nombre%') ":"";
	  if (!$lang)
    		$lang = getSesionDato("IdLenguajeDefecto");
    
		$sql = "SELECT		
		ges_clientes.*		
		FROM
		ges_clientes 		
		WHERE
		ges_clientes.Eliminado = 0 
                AND ges_clientes.IdCliente > 2
                $extra
                ORDER BY ges_clientes.NombreComercial ASC
		";
		
		$res = $this->queryPagina($sql, $min, 10);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ ,"Info: fallo el listado");
		}		
				
		return $res;
	}
	
	function SiguienteCliente() {
		$res = $this->LoadNext();
		if (!$res) {
			return false;
		}
		$this->setId($this->get("IdCliente"));		
		return true;			
	}	
		
	function Modificacion() {
		
		$data = $this->export();				
		
 		$sql = CreaUpdateSimple($data,"ges_clientes","IdCliente",$this->get("IdCliente"));
		
		$res = query($sql,'Modificamos un cliente');
		if (!$res) {			
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo proveedor");
			return false;
		}		
		return true;
	}
	function setFechaSave($idcliente) {
	        //Current_stamp fail  on update
	        $sql = 
		  " UPDATE ges_clientes ".
		  " SET FechaChange = NOW() ".
		  " WHERE IdCliente = ".$idcliente;
		query($sql);
		return;
	}

}

function getClientesTPV($time=false){

          $out          = "";
          $jsLex        = new jsLextable();
	  $clientes     = Array();
	  $clientesruc  = Array();
	  $clientebono  = Array();
	  $clientedebe  = Array();
          $clientepromo = Array();
	  $extraChange  = ($time)? " AND UNIX_TIMESTAMP(FechaChange) > UNIX_TIMESTAMP() - ".$time:"";
          $sql = 
	    " select IdCliente,TipoCliente,Telefono1,Direccion,Email, ".
	    "        NombreLegal as legal,NombreComercial as comercial,Bono,Credito,".
	    "        NumeroFiscal as NFiscal,Comentarios,FechaNacimiento,Debe ".
	    " from   ges_clientes ".
	    " where  TipoCliente <> 'Interno' ".
	    " $extraChange ".
	    " and    Eliminado = 0 ".
	    " order  by NombreComercial asc";
           $res = query($sql);

           while( $row = Row( $res ) )
	     {
	       $promo  = cargarPromocionCliente( $row["IdCliente"] );	
	       $bono   = ( $row["Bono"] )? $row["Bono"]:0;
	       $credito   = ( $row["Credito"] )? $row["Credito"]:0;
	       $promo  = ( $promo )? $promo:0;
	       $debe   = ( $row["Debe"] * 1.0 );
	       $comercial = str_replace('&#038;','&',$row["comercial"]);
	       $legal     = str_replace('&#038;','&',$row["legal"]);
	       $out .= "aU( ".qq($comercial).",".
                              $row["IdCliente"].",". 
                              $debe.","."'".
                              $row["NFiscal"]."'".",".
		              $bono.",".
		              $credito.",'".
		              $promo."','".
		              $row["TipoCliente"]."',".
		              qq($row["Telefono1"]).",".
		              qq($row["Email"]).",".
		              qq($row["Direccion"]).",".
		              qq($legal).",".
		              qq($row["FechaNacimiento"]).",".
		              qq($row["Comentarios"])." );\n";
	     }

	 return $out;
}
function setIdClienteDocumento($iduser,$id){
	   $sql = 
	   " UPDATE ges_comprobantes ".
	   " SET    IdCliente = '".$iduser."'".
	   " WHERE  IdComprobante = '".$id."'";
	   return query($sql);	
}

function updateVenta2Clientes($idcliente,$extra=false){

         $extra = ($extra)? ",".CleanText($extra):"";
	 $sql   = 
	   " update ges_clientes ".
	   " set    FechaChange = NOW() ".$extra.
	   " where  IdCliente = ".$idcliente;
	 query($sql);
}
function updateBonoPromocion2Clientes( $xid ){
	 $sql   = 
	   " select IdCliente ".
	   " from   ges_comprobantes ".
	   " where  IdPromocion = ".$xid.
	   " group  by IdCliente";
	 $xres = query($sql);
	 
	 if (!$xres)
	   return false;

	 while($xrow = Row($xres))
	   {
	     updateVenta2Clientes($xrow["IdCliente"]," Bono = 0 ");
	   }
}

function actualizarImportePendienteCliente($IdCliente){
  $totalDebe =  getImportePendienteCliente( $IdCliente );
  updateVenta2Clientes( $IdCliente, " Debe = ".$totalDebe );
}

function cargarImportePendienteCliente($IdCliente,$ImportePendiente){
	$totalDebe =  getImportePendienteCliente( $IdCliente ) + $ImportePendiente;
	updateVenta2Clientes( $IdCliente, " Debe = ".$totalDebe );
}

function getImportePendienteCliente( $IdCliente ){

  $sql = " select sum(ges_comprobantes.ImportePendiente) as Debe
	              from   ges_comprobantes 
	              inner  join ges_comprobantesnum  
	              on     ges_comprobantes.IdComprobante = ges_comprobantesnum.IdComprobante      
	              inner  join ges_comprobantestipo 
	              on     ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante 
	              where  ges_comprobantes.ImportePendiente > 0 
	              and    ges_comprobantes.Status IN(1,3) 
	              and    ges_comprobantes.Destinatario = 'Cliente' 
	              and    ges_comprobantes.IdCliente = ".$IdCliente." 
	              and    ges_comprobantestipo.TipoComprobante in ('Ticket','Factura','Boleta','Albaran') 
	              and    ges_comprobantesnum.Status in ('Emitido','Facturado')
	              group  by ges_comprobantes.IdCliente ";

  $row = queryrow($sql);
  
  if ($row["Debe"] == '') return 0;
  
  return ( $row["Debe"] >= 0 )? $row["Debe"] : 0;
}

function getImporteBonoCliente($IdCliente){
  $sql = "SELECT SUM(Importe) as TotalBono FROM ges_clientesbono ".
         "WHERE IdCliente = $IdCliente ";

  $row = queryrow($sql);

  if ( $row["TotalBono"] == "" ) return 0;
  else
    return ( $row["TotalBono"] >= 0 )? $row["TotalBono"] : 0;
}


function getImporteCreditoCliente($IdCliente){
  $sql = "SELECT SUM(Importe) as TotalCredito FROM ges_clientescredito ".
         "WHERE IdCliente = $IdCliente ";

  $row = queryrow($sql);

  if ( $row["TotalCredito"] == "" ) return 0;
  else
    return ( $row["TotalCredito"] >= 0 )? $row["TotalCredito"] : 0;
}

function registrarMovimientoBonoCliente($IdCliente,$Bono,$Tipo,$IdLocal,$IdComprobante,
					$IdUsuario){
  $IdLocal   = (!$IdLocal)? CleanID(getSesionDato("IdTienda")):$IdLocal;
  $IdUsuario = (!$IdUsuario)? CleanID(getSesionDato("IdUsuario")):$IdUsuario;
  $xcampo    = "";
  $xvalues   = "";

  $xcampo .= 'IdLocal, ';
  $xvalues.= "'".$IdLocal."'".', ';
  $xcampo .= 'IdUsuario, ';
  $xvalues.= "'".$IdUsuario."'".', ';
  $xcampo .= 'IdCliente, ';
  $xvalues.= "'".$IdCliente."'".', ';
  $xcampo .= 'IdComprobante, ';
  $xvalues.= "'".$IdComprobante."'".', ';
  $xcampo .= 'Importe, ';
  $xvalues.= "'".$Bono."'".', ';
  $xcampo .= 'Tipo ';
  $xvalues.= "'".$Tipo."'".' ';

  $sql = "INSERT INTO ges_clientesbono (".$xcampo.") VALUES (".$xvalues.")";
  query($sql);

  // actuaiza bono del cliente
  actualizarBonoCliente($IdCliente);
}


function registrarMovimientoCreditoCliente($IdCliente,$Credito,$Tipo,$IdLocal,$IdComprobante,
					   $IdUsuario,$xconcepto){
  
  $IdLocal   = (!$IdLocal)? CleanID(getSesionDato("IdTienda")):$IdLocal;
  $IdUsuario = (!$IdUsuario)? CleanID(getSesionDato("IdUsuario")):$IdUsuario;
  $xcampo    = "";
  $xvalues   = "";

  $xcampo .= 'IdLocal, ';
  $xvalues.= "'".$IdLocal."'".', ';
  $xcampo .= 'IdUsuario, ';
  $xvalues.= "'".$IdUsuario."'".', ';
  $xcampo .= 'IdCliente, ';
  $xvalues.= "'".$IdCliente."'".', ';
  $xcampo .= 'IdComprobante, ';
  $xvalues.= "'".$IdComprobante."'".', ';
  $xcampo .= 'Importe, ';
  $xvalues.= "'".$Credito."'".', ';
  $xcampo .= 'Concepto, ';
  $xvalues.= "'".$xconcepto."'".', ';
  $xcampo .= 'Tipo ';
  $xvalues.= "'".$Tipo."'".' ';

  $sql = "INSERT INTO ges_clientescredito (".$xcampo.") VALUES (".$xvalues.")";
  query($sql);

  // actuaiza credito del cliente
  actualizarCreditoCliente($IdCliente);
}

function actualizarBonoCliente($IdCliente){
  $totalBono =  getImporteBonoCliente( $IdCliente );
  updateVenta2Clientes( $IdCliente, " Bono = ".$totalBono );
}

function actualizarCreditoCliente($IdCliente){
  $totalCredito =  getImporteCreditoCliente( $IdCliente );
  updateVenta2Clientes( $IdCliente, " Credito = ".$totalCredito );
}

function obtenerClientexComprobante($IdComprobante){
  $sql = "SELECT IF(ges_clientes.TipoCliente IN ('Empresa','Gobierno'),ges_clientes.NombreLegal,ges_clientes.NombreComercial) as Cliente ".
         "FROM ges_clientes ".
         "INNER JOIN ges_comprobantes ON ges_clientes.IdCliente = ges_comprobantes.IdCliente ".
         "WHERE ges_comprobantes.IdComprobante = '$IdComprobante' ";
  $row = queryrow($sql);
  return $row["Cliente"];
}

function buscarNumeroFiscal($nfiscal,$idclient){
  $xwhere = ($idclient)? " AND IdCliente = '$idclient'":" AND NumeroFiscal = '$nfiscal'";
  $sql = "SELECT NumeroFiscal FROM ges_clientes ".
         "WHERE Eliminado = 0".
         "$xwhere";
  $row = queryrow($sql);
  return $row["NumeroFiscal"];
}

function obtenerNombreCliente($IdCliente){
  $sql = "SELECT IF(ges_clientes.TipoCliente IN ('Empresa','Gobierno'),ges_clientes.NombreLegal,ges_clientes.NombreComercial) as Cliente ".
         "FROM ges_clientes ".
         "WHERE ges_clientes.IdCliente = '$IdCliente' ";
  $row = queryrow($sql);
  return $row["Cliente"];
}
