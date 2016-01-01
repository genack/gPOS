<?php

function OrdenServicioFactory($res) {
	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR en factory");
		return false;	
	}
	
	$row = Row($res);
	if (!is_array($row))
		return false;	
	$id = $row["IdOrdenServicio"];
	
	$oOrden = new ordenservicio;
		
	if ($oOrden->Load($id))
		return $oOrden;
		
	error(__FILE__ . __LINE__ ,"ERROR no pudo cargar id '$id'");
		
	return false;
}


class ordenservicio extends Cursor {
  
   function ordenservicio() {
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

   function getNumeroOS($xcampo, $id){
     $sql = "SELECT $xcampo as Campo FROM ges_ordenservicio WHERE IdOrdenServicio = '$id'";
     $row = queryrow($sql);
     $campo = ($row)? $row['Campo']:0;
     $this->set($xcampo,$campo,FORCE);
     
     return $this->get($xcampo);
   }

   function getEstado(){
     return $this->get("Estado");
   }

   function getConsultaSerieOS($Serie){
     $sql = "SELECT Serie FROM ges_ordenservicio WHERE Serie = '$Serie' ";
     $row = queryrow($sql);

     $serie = ($row)? $Serie:0;
     $this->set('Serie',$serie,FORCE);
     
     return $this->get('Serie');
   }

   function getConsultaNumeroOS($NumOrden,$Serie){
     $sql = "SELECT NumeroOrden FROM ges_ordenservicio WHERE NumeroOrden = '$NumOrden' 
             AND Serie = '$Serie' ";
     $row = queryrow($sql);

     $num = ($row)? $NumOrden:0;
     $this->set('NumeroOrden',$num,FORCE);
     
     return $this->get('NumeroOrden');
   }

   function getUltimoRegistroSerieOS(){
     $sql = "SELECT Serie FROM ges_ordenservicio ORDER BY IdOrdenServicio DESC LIMIT 0,1";
     $row = queryrow($sql);
     
     $serie = ($row["Serie"] == '' || !$row )? 1:$row["Serie"];
     $this->set('Serie',$serie,FORCE);
     return $this->get("Serie");
     
   }

   function getUltimoNumeroOS($Serie){
     $this->setUltimoNumeroOS($Serie);
     return $this->get("NumeroOrden");
   }

   function setUltimoNumeroOS($Serie){
     $sql = "SELECT MAX(NumeroOrden) as NumeroOrden FROM ges_ordenservicio 
                  WHERE Serie = '$Serie'";
     $row = queryrow($sql);

     $numorden = ($row["NumeroOrden"] != '')? ($row["NumeroOrden"]+1):1;
     $this->set('NumeroOrden',$numorden,FORCE);

   }

   function getUltimoNumListServicio($IdOSDet){
     $sql = "SELECT MAX(NumList) as NumList FROM ges_ordenserviciodet 
             WHERE IdOrdenServicioDet = '$IdOSDet' ";

     $row = queryrow($sql);
     $num = ($row["NumList"] != '')? $row["NumList"] : 0;

     $this->set("NumList",$num,FORCE);
     return $this->get("NumList");
   }

   function getNumListServicio($IdOSDet){
     $sql = "SELECT NumList FROM ges_ordenserviciodet 
             WHERE IdOrdenServicioDet = '$IdOSDet' ";

     $row = queryrow($sql);
     $num = ($row["NumList"] != '')? $row["NumList"] : 0;

     return $num;
   }

   function getNumListOS($IdOrdenServicio,$TipoProducto,$xNumlist,$IdOrdenServicioDet){
     $extraNum = ($xNumlist)? " AND NumList like '$xNumlist%' ":"";
     $sql = "SELECT MAX(NumList) as NumList FROM ges_ordenserviciodet 
             WHERE IdOrdenServicio = '$IdOrdenServicio' 
             AND TipoProducto = '$TipoProducto' ".
             " $extraNum ";

     $row = queryrow($sql);
     $idosdet = $this->getNumListServicio($IdOrdenServicioDet);
     $list= ($TipoProducto == 'Producto')? $idosdet.'01':'';
     $base= ($xNumlist)? $list:$IdOrdenServicio.$idosdet+1;
     $num = ($row["NumList"] != '')? ((int)$row["NumList"]+1) : $base;
     return $num;

   }

   function getImporteOS($IdOrden){
     $sql = "SELECT Importe FROM ges_ordenservicio WHERE IdOrdenServicio = '$IdOrden'";
     $row = queryrow($sql);
     return $row["Importe"];
   }

   function getImpuestoOS($IdOrden){
     $sql = "SELECT Impuesto FROM ges_ordenservicio WHERE IdOrdenServicio = '$IdOrden'";
     $row = queryrow($sql);
     return $row["Impuesto"];
   }

   function getImporteOSDet($IdOrdenDet){
     $sql = "SELECT Importe FROM ges_ordenserviciodet WHERE IdOrdenServicioDet = '$IdOrdenDet'";
     $row = queryrow($sql);
     return $row["Importe"];
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


function mostrarOrdenServicio($esSoloLocal,$Desde,$Hasta,$Cliente,
			      $Estado,$Codigo,$Facturacion,$Usuario,$Tipo){
  
  $desde        = CleanRealMysql($Desde);
  $hasta        = CleanRealMysql($Hasta);
  $nombre       = CleanRealMysql($Cliente);
  
  $extraUsuario = ($Usuario == 0)? "":" AND ges_ordenserviciodet.IdUsuarioResponsable = '$Usuario' ";
  $extrainneruser = ($Usuario == 0)? "":" INNER JOIN ges_ordenserviciodet ON ges_ordenservicio.IdOrdenServicio = ges_ordenserviciodet.IdOrdenServicio ";

  $extraNombre  = ($nombre and $nombre != '')?" AND ges_clientes.NombreComercial LIKE '%$nombre%' ":"";

  $extraLocal   = ($esSoloLocal != 0)? " AND ges_locales.IdLocal = '$esSoloLocal' ":"";
  $extraFecha   = " AND date(ges_ordenservicio.FechaIngreso) >= '$desde' AND date(ges_ordenservicio.FechaIngreso) <= '$hasta' ";
  $extraEstado  = ($Estado != 'Todos')? " AND ges_ordenservicio.Estado = '$Estado' " : "";
  $extraTipo    = ($Tipo != 'Todos')? " AND ges_ordenservicio.Tipo = '$Tipo' " : "";
  $extraFacturacion = ($Facturacion != 'Todos')? " AND ges_ordenservicio.Facturacion = '$Facturacion' AND ges_ordenservicio.Estado = 'Finalizado' ":"";

  $extraCodigo  = ($Codigo)? " AND ges_ordenservicio.Codigo = '$Codigo' ":$extraNombre.$extraLocal.$extraFecha.$extraEstado.$extraFacturacion.$extraTipo;

  $sql = "SELECT ges_ordenservicio.IdLocal, ".
         "       ges_locales.NombreComercial as Local, ".
         "       ges_usuarios.Nombre as Usuario, ".
         "       ges_ordenservicio.IdCliente, ".
         "       ges_clientes.NombreComercial as Cliente, ".
         "       ges_ordenservicio.IdOrdenServicio, ".
         "       IF(IdUsuarioEntrega = 0,' ',
                   (SELECT ges_usuarios.Nombre 
                    FROM   ges_usuarios 
                    WHERE  ges_usuarios.IdUsuario = IdUsuarioEntrega)) as UsuarioEntrega, ".
         "       CONCAT(DATE_FORMAT(FechaIngreso,'%d/%m/%Y %H:%i'),'~',FechaIngreso) as FechaIngreso, ".
         "       CONCAT(DATE_FORMAT(FechaEntrega,'%d/%m/%Y %H:%i'),'~',FechaEntrega) as FechaEntrega, ".
         "       ges_ordenservicio.Estado, ".
         "       ges_ordenservicio.Codigo, ".
         "       ges_ordenservicio.Serie, ".
         "       ges_ordenservicio.NumeroOrden, ".
         "       ges_ordenservicio.Impuesto, ".
         "       ges_ordenservicio.Importe, ".
         "       ges_ordenservicio.Facturacion, ".
         "       ges_ordenservicio.Prioridad, ".
         "       ges_ordenservicio.Tipo, ".
         "       ges_ordenservicio.IdSuscripcion ".
         "FROM   ges_ordenservicio ".
         "INNER JOIN ges_locales ON ges_ordenservicio.IdLocal = ges_locales.IdLocal ".
         "INNER JOIN ges_usuarios ON ges_ordenservicio.IdUsuario = ges_usuarios.IdUsuario ".
         "INNER JOIN ges_clientes ON ges_ordenservicio.IdCliente = ges_clientes.IdCliente ".
         "       $extrainneruser ".
         "WHERE  ges_ordenservicio.Eliminado = 0 ".
         "       $extraCodigo ".
         "       $extraUsuario ".
         "ORDER BY ges_ordenservicio.Facturacion ASC, ges_ordenservicio.Prioridad DESC, ges_ordenservicio.FechaIngreso ASC";

  $res = query($sql);
  
  if (!$res) return false;
  
  $OrdenServicio = array();
  $t = 0;

  while($row = Row($res)){
    $nombre = "Orden_" . $t++;
    $row["Facturacion"] = ($row["Facturacion"] == 1)? obtenerComprobanteOrdenServicio($row["IdOrdenServicio"]) : "0~0~";
    $OrdenServicio[$nombre] = $row;
  }	
  
  return $OrdenServicio;
    
}

function obtenerComprobanteOrdenServicio($IdOrdenServicio){
	$sql = "SELECT
                CONCAT (ges_comprobantes.SerieComprobante,'-',
                ges_comprobantes.NComprobante) as Codigo,
                CONCAT(ges_comprobantestipo.TipoComprobante,' ',ges_comprobantestipo.Serie,'-',ges_comprobantesnum.NumeroComprobante) as Comprobante
    		FROM ges_presupuestos " .
	        "INNER JOIN ges_comprobantes ON ges_presupuestos.IdPresupuesto = ges_comprobantes.IdPresupuesto ".
                "INNER JOIN ges_comprobantesnum ON ges_comprobantesnum.IdComprobante = ges_comprobantes.IdComprobante
                INNER JOIN ges_comprobantestipo ON  ges_comprobantestipo.IdTipoComprobante = ges_comprobantesnum.IdTipoComprobante 
                WHERE ges_comprobantes.Eliminado = 0
                AND  ges_comprobantesnum.Eliminado = 0
                AND  ges_comprobantesnum.Status = 'Emitido' 
                AND  ges_presupuestos.TipoPresupuesto = 'Preventa'
                AND  ges_presupuestos.Status = 'Confirmado' 
                AND  ges_presupuestos.Eliminado = 0
                AND  ges_presupuestos.IdOrdenServicio = '$IdOrdenServicio'";

	$row = queryrow($sql);

	return '1~'.$row["Codigo"].'~'.$row["Comprobante"];
}

function mostrarDetalleOrdenServicio($IdOrdenServicio){

  $sql = "SELECT IdOrdenServicioDet, ".
         "       ges_ordenserviciodet.IdProducto, ".
         "       IdUsuarioResponsable, ".
         "       IdComprobante, ".
         "       CONCAT(DATE_FORMAT(FechaInicio,'%d/%m/%Y %H:%i'),'~',FechaInicio) as FechaInicio, ".
         "       CONCAT(DATE_FORMAT(FechaFin,'%d/%m/%Y %H:%i'),'~',FechaFin) as FechaFin, ".
         "       ges_ordenserviciodet.Estado, ".
         "       Garantia, EstadoGarantia, GarantiaCondicion, EstadoSolucion, ".
         "       IF(Concepto like '',' ',Concepto) as Concepto, ".
         "       IF(NumeroSerie like '',' ',NumeroSerie) as NumeroSerie, ".
         "       Unidades, Precio, Importe, ".
	 "       IF(ges_productos.Servicio='0',CONCAT(ges_productos_idioma.Descripcion,' ',".
	 "       ges_marcas.Marca,' ',".
	 "       ges_modelos.Color,' ',".
	 "       ges_detalles.Talla,' ',".
	 "       ges_laboratorios.NombreComercial),ges_productos_idioma.Descripcion) as Producto,".
         "       IF(IdUsuarioResponsable = 0,' ',(SELECT ges_usuarios.Nombre 
                    FROM ges_usuarios WHERE ges_usuarios.IdUsuario = 
                    ges_ordenserviciodet.IdUsuarioResponsable)) as Usuario, ".
         "       IF(ges_productos.Servicio = '0',' ',(SELECT CONCAT(TipoServicio,'~',SAT) FROM ges_tiposervicio WHERE IdTipoServicio = ges_productos.Servicio )) as TipoServicio, ".
         "       ges_ordenserviciodet.TipoProducto, ".
         "       IF(ges_ordenserviciodet.CodigoBarras like '',' ',ges_ordenserviciodet.CodigoBarras) as CodigoBarras, ".
         "       ges_ordenserviciodet.Ubicacion, ".
         "       IF(ges_ordenserviciodet.Direccion like '',' ',ges_ordenserviciodet.Direccion) as Direccion, ".
         "       IF(ges_ordenserviciodet.Observaciones like '',' ',ges_ordenserviciodet.Observaciones) as Observaciones, ".
         "       IF(ges_ordenserviciodet.OrdenAnterior like '',' ',ges_ordenserviciodet.OrdenAnterior) as OrdenAnterior ".
         "FROM   ges_ordenserviciodet ".
	 "LEFT  JOIN ges_productos ON ges_ordenserviciodet.IdProducto = ges_productos.IdProducto ".
	 "INNER JOIN ges_productos_idioma ON ges_productos.IdProdBase = ges_productos_idioma.IdProdBase ".
	 "INNER JOIN ges_detalles       ON ges_productos.IdTalla  = ges_detalles.IdTalla ".
	 "INNER JOIN ges_modelos      ON ges_productos.IdColor  = ges_modelos.IdColor ".
	 "INNER JOIN ges_laboratorios ON ges_productos.IdLabHab = ges_laboratorios.IdLaboratorio ".
	 "INNER JOIN ges_marcas       ON ges_productos.IdMarca  = ges_marcas.IdMarca ".
         "WHERE  ges_ordenserviciodet.Eliminado = 0 ".
         "AND    ges_ordenserviciodet.IdOrdenServicio = '$IdOrdenServicio' ".
         "ORDER BY NumList ASC ";

  $res = query($sql);
  
  if (!$res) return false;
  
  $OrdenServicioDet = array();
  $t = 0;

  while($row = Row($res)){
    $nombre = "OrdenDet_" . $t++;
    $row["Concepto"] = ($row["Concepto"] == ' ')? $row["Producto"]:$row["Concepto"];
    $TieneProductoSat = ($row["TipoServicio"] != ' ')? explode('~',$row["TipoServicio"]):0;
    $row["TieneProductoSat"] = ($TieneProductoSat[1] == 1)? 1:0;
    $row["ProductoSat"] = ($row["TieneProductoSat"] == 0)? ' ':mostrarServicioProductoSat($row["IdOrdenServicioDet"]);
    $OrdenServicioDet[$nombre] = $row;
    if($row["Estado"] == 'Finalizado' || $row["EstadoGarantia"] == 'Garantia')
      checkFechaGarantiaOrdenServicioDet($row["Garantia"],$row["IdOrdenServicioDet"]);
  }	
  
  return $OrdenServicioDet;
  
}

function revisarProductoSat($IdOrdenServicioDet){
  $sql = "SELECT ges_productossat.Diagnostico, ".
         "       ges_productossat.Solucion, ".
         "       ges_productossat.IdMotivoSat, ".
         "       ges_motivosat.Motivo, ".
         "       ges_productossat.Ubicacion ".
         "FROM   ges_productossat ".
         "INNER JOIN ges_motivosat ON ges_productossat.IdMotivoSat=ges_motivosat.IdMotivoSat ".
         "WHERE  ges_productossat.IdOrdenServicioDet = '$IdOrdenServicioDet'";
  $row = queryrow($sql);
  return $row["Diagnostico"].'~'.$row["Solucion"].'~'.$row["IdMotivoSat"].'~'.$row["Motivo"].'~'.$row["Ubicacion"];
}

function checkFechaGarantiaOrdenServicioDet($Garantia,$id){
  $Hoy        = strtotime('now');
  $Fecha      = strtotime($Garantia);
  $table      = "ges_ordenserviciodet";
  $idtable    = "IdOrdenServicioDet";
  
  if($Hoy > ($Fecha+86400))
    {
      $oOrden = new ordenservicio;
      $oOrden->set("EstadoGarantia",'Vencida',FORCE);
      $oOrden->Modificar($table,$idtable,$id);
    }
}

function mostrarServicioProductoSat($IdOrdenServicioDet){
  $sql = "SELECT IdProductoSat, ges_productossat.IdMarca, ges_productossat.IdModeloSat, ".
         "       ges_productossat.IdMotivoSat, ges_productossat.IdProdBaseSat, ".
         "       ges_marcas.Marca, ges_modelosat.Modelo, ges_motivosat.Motivo, ".
         "       ges_productosidiomasat.Descripcion as Producto, ".
         "       IF(ges_productossat.NumeroSerie like '',' ',ges_productossat.NumeroSerie) as NumeroSerie, ".
         "       IF(ges_productossat.Descripcion like '',' ',ges_productossat.Descripcion) as Descripcion, ".
         "       IF(Solucion like '',' ',Solucion) as Solucion, ".
         "       IF(Diagnostico like '',' ',Diagnostico) as Diagnostico, ".
         "       Detalle, ".
         "       Ubicacion ".
         "FROM ges_productossat ".
         "INNER JOIN ges_marcas ON ges_productossat.IdMarca = ges_marcas.IdMarca ".
         "INNER JOIN ges_modelosat ON ges_productossat.IdModeloSat = ges_modelosat.IdModeloSat ".
         "INNER JOIN ges_motivosat ON ges_productossat.IdMotivoSat = ges_motivosat.IdMotivoSat ".
         "INNER JOIN ges_productosidiomasat ON ges_productossat.IdProdBaseSat = ges_productosidiomasat.IdProdBaseSat ".
         "WHERE ges_productossat.IdOrdenServicioDet = '$IdOrdenServicioDet' ".
         "AND ges_productossat.Eliminado = 0 ";

  $res = query($sql);

  if (!$res) return false;

  $prodsat     = '';

  while($row = Row($res)){
    $prodsat .= $row["IdProductoSat"].'~'.
                    $row["IdMarca"].'~'.
                    $row["IdModeloSat"].'~'.
                    $row["IdMotivoSat"].'~'.
                    $row["IdProdBaseSat"].'~'.
                    $row["Marca"].'~'.
                    $row["Modelo"].'~'.
                    $row["Motivo"].'~'.      
                    $row["Producto"].'~'.
                    $row["NumeroSerie"].'~'.      
                    $row["Descripcion"].'~'.
                    $row["Solucion"].'~'.
                    $row["Diagnostico"].'~'.
                    $row["Detalle"].'~'.
                    $row["Ubicacion"];

      $prodsat .= ($row["Detalle"] != 1)? ';;':';;'.revisarServicioProductoSatDet($row["IdProductoSat"]);
  }	
  return $prodsat;
}

function revisarServicioProductoSatDet($IdProductoSat){
  $sql = "SELECT IdProductoSatDet, ges_productossatdet.IdMarca, ".
         "       ges_productossatdet.IdModeloSat, ".
         "       ges_productossatdet.IdProdBaseSat, ".
         "       ges_marcas.Marca, ges_modelosat.Modelo, ".
         "       ges_productosidiomasat.Descripcion, ".
         "       IF(ges_productossatdet.NumeroSerie like '',' ',ges_productossatdet.NumeroSerie) as NumeroSerie ".
         "FROM ges_productossatdet ".
         "INNER JOIN ges_marcas ON ges_productossatdet.IdMarca = ges_marcas.IdMarca ".
         "INNER JOIN ges_modelosat ON ges_productossatdet.IdModeloSat = ges_modelosat.IdModeloSat ".
         "INNER JOIN ges_productosidiomasat ON ges_productossatdet.IdProdBaseSat = ges_productosidiomasat.IdProdBaseSat ".
         "WHERE ges_productossatdet.IdProductoSat = '$IdProductoSat' ".
         "AND ges_productossatdet.Eliminado = 0 ";

  $res = query($sql);
  
  if (!$res) return false;
  
  $xdet = '';
  $ProductoSatDet = '';
  while($row = Row($res)){
    $ProductoSatDet .= $xdet.$row["IdProductoSatDet"].'~'.
                      $row["IdMarca"].'~'.
                      $row["IdModeloSat"].'~'.
                      $row["IdProdBaseSat"].'~'.
                      $row["Marca"].'~'.
                      $row["Modelo"].'~'.
                      $row["Descripcion"].'~'.
                      $row["NumeroSerie"];
    $xdet = '::';
  }	
  
  return $ProductoSatDet;
}

function CheckEstadoFinalizadoOSDetalle($id){
  $sql = "SELECT Estado FROM ges_ordenserviciodet 
          WHERE IdOrdenServicio = '$id' AND TipoProducto = 'Servicio' AND Eliminado = 0";
  $res = query($sql);
  
  while($row = Row($res)){
    
    if($row["Estado"] == 'Finalizado')
      continue;
    return 'Ejecucion';
  }
  return 'Finalizado';
}

function ModificarEstadoOrdenServicioDetalle($Estado,$IdOrdenServicio){
  $sql = "UPDATE ges_ordenserviciodet SET Estado = '$Estado' ".
         "WHERE  IdOrdenServicio = '$IdOrdenServicio' ".
         "AND    TipoProducto = 'Servicio' ";
  query($sql);
}

function VerificarProductoOrdenServicio($IdOrdenServicioDet,$IdProducto){
  $oOrden  = new ordenservicio;
  $numlist = $oOrden->getNumListServicio($IdOrdenServicioDet);
  $sql = "SELECT IdOrdenServicioDet,Unidades,Precio,NumeroSerie,CodigoBarras 
          FROM ges_ordenserviciodet ".
         "WHERE  NumList like '$numlist%' ".
         "AND    IdProducto   = '$IdProducto' ".
         "AND    TipoProducto = 'Producto' ".
         "AND    Eliminado    = 0 ";

  $row = queryrow($sql);
  $id  = ($row["IdOrdenServicioDet"] != '')? $row["IdOrdenServicioDet"].'~'.
                                             $row["Unidades"].'~'.
                                             $row["Precio"].'~'.
                                             $row["NumeroSerie"].'~'.
                                             $row["CodigoBarras"]:0;
  return $id;
}

function ckeckOrdenServicio2Preventa($xid){

  $sql = 
     " select concat(IdPresupuesto,'~',NPresupuesto) as id ".
     " from   ges_presupuestos ".
     " where  IdOrdenServicio = '$xid' ".
     " and    TipoPresupuesto='Preventa' ".
     " and    Status  = 'Pendiente' ".
     " and    Eliminado = 0 ";
   
   $res = query($sql);
   return ( $row = Row($res) )? $row['id']:0;
}

function ckeckPreventa2OrdenServicio($xid){

  $sql = 
     " select IdOrdenServicio as id ".
     " from   ges_presupuestos ".
     " where  IdPresupuesto = '$xid' ".
     " and    TipoPresupuesto='Preventa' ".
     " and    Status  = 'Pendiente' ".
     " and    Eliminado = 0 ";
   
   $res = query($sql);
   return ( $row = Row($res) )? $row['id']:0;
}

function setStatusOrdenServicio($idOrdenServicio,$idDependiente){

  $sql     = 
    " UPDATE ges_ordenservicio ".
    " SET    IdUsuarioEntrega = '".$idDependiente."',".
    "        FechaEntrega = NOW(),".
    "        Facturacion= 1".
    " WHERE  IdOrdenServicio = '".$idOrdenServicio."'";

  query($sql);

}

function ActualizarGarantiaComprobanteDet($IdOrdenServicio,$IdComprobanteDet){
  $xid     = ($IdComprobanteDet == 0)? 'IdOrdenServicio':'IdComprobanteDet';
  $xdato   = ($IdComprobanteDet == 0)? $IdOrdenServicio:$IdComprobanteDet;
  $IdOS    = ($IdComprobanteDet == 0)? 0:$IdOrdenServicio;
  $sql     = 
    " UPDATE ges_comprobantesdet ".
    " SET    IdOrdenServicio = '".$IdOS."' ".
    " WHERE  $xid = '".$xdato."' ";

  query($sql);  
}

?>