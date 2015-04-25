<?php

function PerfilFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdPerfil"];
	
	$oPerfil = new perfil;
		
	if ($oPerfil->Load($id))
		return $oPerfil;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class perfil extends Cursor {

        function perfil() {
	  return $this;
	}
    
        function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("ges_perfiles_usuario", "IdPerfil", $id);
		return $this->getResult();
	}
    
        // SET especializados    
        function setNombrePerfil($nombre){    	
	  $this->set("NombrePerfil",$nombre,FORCE);	
	}
    
	 // GET especializados
	function getNombre(){
	  return $this->get("NombrePerfil");	
	}
	
	function getPerfil(){
	  return $this->get("Perfil");
	}

	function getCajaTPV(){
	  return $this->get("CajaTPV");
	}

	function getPrecios(){
	  return $this->get("Precios");
	}

	function getB2B(){
	  return $this->get("B2B");
	}
	
	function getServicios(){
	  return $this->get("Servicios");
	}

	function getSuscripciones(){
	  return $this->get("Suscripcion");
	}
	
	function getSAT(){
	  return $this->get("SAT");
	}

	//Formulario de modificaciones y altas
	function formEntrada($action,$esModificar){
				
		$ot      = getTemplate("AltaPerfil");
		if (!$ot) return false;

		$modo    = ($esModificar)? "modsave":"newsave";
		$titulo  = ($esModificar)? _("Modificando perfil"):_("Nuevo perfil");	

		$cambios = array(	
			"TITULO" => $titulo,	
			"VALUENOMBRE" => $this->getNombre(),
			"TEXTNOMBRE" => _("Nombre perfil"),
			"HIDDENDATA" => Hidden("id",$this->getId()),
			"ACTION" => "$action?modo=$modo",
			"CADMINISTRACION" => gCheck($this->is("Administracion")),
			"CINFORMELOCAL" => gCheck($this->is("InformeLocal")),
			"CINFORMES" => gCheck($this->is("Informes")),
			"CPRODUCTOS" => gCheck($this->is("Productos")),
			"CPROVEEDORES" => gCheck($this->is("Proveedores")),
			"CSTOCKS" => gCheck($this->is("Stocks")),
			"CCOMPRAS" => gCheck($this->is("Compras")),
			"CCLIENTES" => gCheck($this->is("Clientes")),
			"CTPV" => gCheck($this->is("TPV")),
			"CB2B" => gCheck($this->is("B2B")),
			"CPEDIDOSVENTA" => gCheck($this->is("PedidosVenta")),
			"CVERSTOCKS" => gCheck($this->is("VerStocks")),
			"CPRECIOS" => gCheck($this->is("Precios")),
			"CVENTAS" => gCheck($this->is("Ventas")), 	
			"CFINANZAS" => gCheck($this->is("Finanzas")), 	
			"CCOBROS" => gCheck($this->is("Cobros")), 	
			"CPAGOS" => gCheck($this->is("Pagos")), 	
			"CCAJAGENERAL" => gCheck($this->is("CajaGeneral")), 	
			"CCAJATPV" => gCheck($this->is("CajaTPV")), 	
			"CPRESUPUESTOS" => gCheck($this->is("Presupuestos")), 	
			"CCOMPROBANTESCOMPRA" => gCheck($this->is("ComprobantesCompra")),
			"CCOMPROBANTESVENTA" => gCheck($this->is("ComprobantesVenta")),
			"CPROMOCIONES" => gCheck($this->is("Promociones")),
			"CKARDEX" => gCheck($this->is("Kardex")),
			"CAJUSTES" => gCheck($this->is("Ajustes")), 	
			"CVERAJUSTES" => gCheck($this->is("VerAjustes")),
			"CALMACEN" => gCheck($this->is("Almacen")),
			"CSAT" => gCheck($this->is("SAT")),
			"CSUSCRIPCION" => gCheck($this->is("Suscripcion")),
			"CSERVICIOS" => gCheck($this->is("Servicios")),
			"TADMINISTRACION" => _("Administración"),
			"TINFORMELOCAL" => _("Informe local"),			
			"TINFORMES" =>  _("Informes"),
			"TPRODUCTOS" => _("Productos"),
			"TPROVEEDORES" => _("Proveedores"),
			"TSTOCKS" => _("Stocks"),
			"TCOMPRAS" => _("Compras"),
			"TCLIENTES" => _("Clientes"),
			"TTPV" => _("TPV"),	
			"TB2B" => _("B2B"),	
			"TPEDIDOSVENTA" => _("Pedidos"),
			"TVERSTOCKS" => _("Ver Stocks"),
			"TPRECIOS" => _("Precios"),	
			"TVENTAS" => _("Ventas"),	 	
			"TFINANZAS" => _("Finanzas"),
			"TCOBROS" => _("Cobros"),			
			"TPAGOS" => _("Pagos"), 	
			"TCAJAGENERAL"  => _("Caja General"),	
			"TCAJATPV"  => _("Caja TPV"),	
			"TPRESUPUESTOS"  => _("Presupuestos"),	
			"TCOMPROBANTESCOMPRA"  => _("Comprobantes Compra"),	
			"TCOMPROBANTESVENTA"  => _("Comprobantes Venta"),	
			"TPROMOCIONES"  => _("Promociones"),	
			"TKARDEX"  => _("Kardex"),	
			"TAJUSTES"  => _("Ajustes"),	
			"TALMACEN"  => _("Almacén"),	
			"TVERAJUSTES"  => _("Ver Ajustes"),
			"TSERVICIOS"  => _("Servicios"),
			"TSAT"  => _("SAT"),
			"TSUSCRIPCION"  => _("Suscripción")
		);
		return $ot->makear($cambios);
	}
	
	
	function Crea(){
		$this->setNombrePerfil(_("Nuevo perfil"));
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
			
			$listaKeys .= " " . $key;
			$listaValues .= " '".$value."'";
			$coma = true;
		}
	
		$sql = "INSERT INTO ges_perfiles_usuario ( $listaKeys ) VALUES ( $listaValues )";
		
		return query($sql);
						 	
	}
		
	function setNombre($nombre){
		$this->set("NombrePerfil",$nombre,FORCE);		
	}	
		
}

function getPerfilPrecios( $id ){

	$oUser    = new usuario;
	$oUser->Load( $id );
	$idperfil = $oUser->getIdPerfil();

	$oPerfil  = new perfil;	
	$oPerfil->Load( $idperfil );
	return $oPerfil->getPrecios()."~".
	       $oPerfil->getCajaTPV()."~".
	       $oPerfil->getB2B()."~".
	       $oPerfil->getServicios()."~".
	       $oPerfil->getSuscripciones()."~".
	       $oPerfil->getSAT();
}

?>
