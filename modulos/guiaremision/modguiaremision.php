<?php
include("../../tool.php");

if (!getSesionDato("IdTienda")){
    session_write_close();
    //header("Location: #");
    exit();
}

$modo = CleanText($_GET["modo"]);

switch($modo){
        
    case "obtenerIdComprobanteNum":
        $id       = CleanID($_GET["id"]);
        $tipoguia = CleanText($_GET["xtipo"]);
        $data     = obtenerIdComprobanteNum($id,$tipoguia);
        echo $data;
        break;

    case "guardaGuiaRemsion":
        $idcnum    = CleanID($_POST["xidcnum"]);
        $serie     = CleanText($_POST["xserie"]);
        $numero    = CleanText($_POST["xnumero"]);
        $femision  = CleanCadena($_POST["xfemision"]);
        $motivo    = CleanText($_POST["xmotivo"]);
        $concepto  = CleanText($_POST["xconcepto"]);
        $ppartida  = CleanText($_POST["xppartida"]);
        $pllegaga  = CleanText($_POST["xpllegaga"]);
        $marca     = CleanText($_POST["xmarca"]);
        $placa     = CleanText($_POST["xplaca"]);
        $licencia  = CleanText($_POST["xlicencia"]);
        $pesocarga = CleanText($_POST["xpesocarga"]);
        $undpeso   = CleanText($_POST["xundpeso"]);
        $ftraslado = CleanCadena($_POST["xftraslado"]);
        $idguia    = CleanID($_POST["xidguia"]);
        $idusuario = CleanID($_POST["xiduser"]);
        $tipoguia  = CleanText($_POST["xtipoguia"]);
        $idcbteprob= CleanID($_POST["xidcbteprov"]);
        $idsubsid  = CleanID($_POST["xidsubsidiario"]);

        $existeguia = validarGuiaRemision($serie,$numero);

        if(!$idguia){
            if($existeguia)
                break;
            echo guardaGuiaRemsion($idcnum,$serie,$numero,$femision,$motivo,
                                   $concepto,$ppartida,$pllegaga,$marca,$placa,
                                   $licencia,$pesocarga,$undpeso,$ftraslado,
                                   $idusuario,$tipoguia,$idsubsid);
            }
        else{
            echo modificaGuiaRemsion($idcnum,$serie,$numero,$femision,$motivo,
                                     $concepto,$ppartida,$pllegaga,$marca,$placa,
                                     $licencia,$pesocarga,$undpeso,$ftraslado,
                                     $idguia,$idusuario,$tipoguia,$idcbteprob,
                                     $idsubsid);
            }
        break;
    case 'obtenerDataGuiaRemision':
        $IdGuiaRemision = CleanID($_GET["xid"]);
        $TipoGuia = CleanText($_GET["xtipo"]);
        $IdLocal  = getSesionDato("IdTienda");

        $dato = obtenerDatoGuiaRemisio($IdGuiaRemision);
        echo $dato;
        break;
}

function obtenerIdComprobanteNum($id,$tipoguia){
    switch($tipoguia){
        case 'Remitente':
            $sql = "SELECT IdAlbaranes ".
                   "FROM ges_comprobantes ".
                   "WHERE IdComprobante = $id ";
            $row = queryrow($sql);
            
            if($row["IdAlbaranes"] != "" || $row["IdAlbaranes"])
                return $row["IdAlbaranes"];
            
            
            $sql = "SELECT IdNumComprobante ".
                   "FROM ges_comprobantesnum ".
                   "WHERE IdComprobante = $id ";
            $row = queryrow($sql);
            return $row["IdNumComprobante"];
            break;
        case 'Transportista':
            break;
        case 'Proveedor':
            break;
    }
}

function guardaGuiaRemsion($idcnum,$serie,$numero,$femision,$motivo,
                           $concepto,$ppartida,$pllegaga,$marca,$placa,
                           $licencia,$pesocarga,$undpeso,$ftraslado,
                           $idusuario,$tipoguia,$idsubsid){

    $idlocal = getSesionDato("IdTienda");
    $idusuario = ($idusuario)? $idusuario:getSesionDato("IdUsuario");

    $guia = new guiaremision;

    $guia->set("IdComprobanteNum",$idcnum   ,FORCE); 
    $guia->set("NumeroSerie",$serie    ,FORCE); 
    $guia->set("NumeroGuia",$numero   ,FORCE); 
    $guia->set("FechaEmision",$femision ,FORCE); 
    $guia->set("IdMotivoAlbaran",$motivo   ,FORCE); 
    $guia->set("MotivoTraslado",$concepto ,FORCE); 
    $guia->set("PuntoPartida",$ppartida ,FORCE); 
    $guia->set("PuntoLlegada",$pllegaga ,FORCE); 
    $guia->set("MarcaUnidadTransp",$marca    ,FORCE); 
    $guia->set("PlacaUnidadTransp",$placa    ,FORCE); 
    $guia->set("LicenciaConductor",$licencia ,FORCE); 
    $guia->set("Peso",$pesocarga,FORCE); 
    $guia->set("UnidadPeso",$undpeso  ,FORCE); 
    $guia->set("FechaInicioTraslado",$ftraslado,FORCE); 
    $guia->set("IdLocal",$idlocal,FORCE);
    $guia->set("IdUsuario",$idusuario,FORCE);
    $guia->set("TipoGuia",$tipoguia,FORCE);
    $guia->set("IdSubsidiario",$idsubsid,FORCE);

    if($guia->Alta()){
	$id = $guia->get("IdGuiaRemision");
	return $id;
    }
}

function modificaGuiaRemsion($idcnum,$serie,$numero,$femision,$motivo,
                             $concepto,$ppartida,$pllegaga,$marca,$placa,
                             $licencia,$pesocarga,$undpeso,$ftraslado,
                             $idguia,$idusuario,$tipoguia,$idcbteprob,
                             $idsubsid){

    $idlocal = getSesionDato("IdTienda");
    $idusuario = ($idusuario)? $idusuario:getSesionDato("IdUsuario");
    $guia = new guiaremision;

    $guia->set("IdComprobanteNum",$idcnum   ,FORCE); 
    $guia->set("NumeroSerie",$serie    ,FORCE); 
    $guia->set("NumeroGuia",$numero   ,FORCE); 
    $guia->set("FechaEmision",$femision ,FORCE); 
    $guia->set("IdMotivoAlbaran",$motivo   ,FORCE); 
    $guia->set("MotivoTraslado",$concepto ,FORCE); 
    $guia->set("PuntoPartida",$ppartida ,FORCE); 
    $guia->set("PuntoLlegada",$pllegaga ,FORCE); 
    $guia->set("MarcaUnidadTransp",$marca    ,FORCE); 
    $guia->set("PlacaUnidadTransp",$placa    ,FORCE); 
    $guia->set("LicenciaConductor",$licencia ,FORCE); 
    $guia->set("Peso",$pesocarga,FORCE); 
    $guia->set("UnidadPeso",$undpeso  ,FORCE); 
    $guia->set("FechaInicioTraslado",$ftraslado,FORCE); 
    $guia->set("IdLocal",$idlocal,FORCE);
    $guia->set("IdUsuario",$idusuario,FORCE);
    $guia->set("TipoGuia",$tipoguia,FORCE);
    $guia->set("IdComprobanteProv",$idcbteprob,FORCE);
    $guia->set("IdSubsidiario",$idsubsid,FORCE);
    
    if($guia->Modificar($idguia)){
	return $idguia;
    }
    
}

function validarGuiaRemision($serie,$numero){
    $sql = "SELECT NumeroSerie, NumeroGuia ".
           "FROM ges_guiaremision ".
           "WHERE NumeroSerie = $serie ".
           "AND NumeroGuia = $numero ".
           "AND Eliminado = 0 ";
    $row = queryrow($sql);

    if(!$row)
        return false;
    else
        return $row["NumeroSerie"]."-".$row["NumeroGuia"];
}

function obtenerDatoGuiaRemisio($IdGuiaRemision){
    $guia = new guiaremision;
    $guia->Load($IdGuiaRemision);

    $idsubs = $guia->get("IdSubsidiario");
    $subs = new Subsidiario;
    if($idsubs > 0)
        $subs->Load($idsubs);
    $subsidiario = ($idsubs > 0)? $subs->get("NombreComercial"):"";

    $txt = "";
    $txt .= $guia->get("IdComprobanteNum")."~~".
        $guia->get("IdLocal")."~~".
        $guia->get("IdUsuario")."~~".
        $guia->get("IdComprobanteProv")."~~".
        $guia->get("IdSubsidiario")."~~".
        $guia->get("IdMotivoAlbaran")."~~".
        $guia->get("NumeroSerie")."~~".
        $guia->get("NumeroGuia")."~~".
        $guia->get("TipoGuia")."~~".
        $guia->get("FechaEmision")."~~".
        $guia->get("FechaInicioTraslado")."~~".
        $guia->get("MotivoTraslado")."~~".
        $guia->get("PuntoPartida")."~~".
        $guia->get("PuntoLlegada")."~~".
        $guia->get("MarcaUnidadTransp")."~~".
        $guia->get("PlacaUnidadTransp")."~~".
        $guia->get("LicenciaConductor")."~~".
        $guia->get("Peso")."~~".
        $guia->get("UnidadPeso")."~~".
        $subsidiario;

    return $txt;
}

?>
