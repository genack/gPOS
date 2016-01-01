<?php

include("../../tool.php");

$cuenta    = (isset($_GET["xcta"]))? CleanText($_GET["xcta"]):'0';
$cuenta    = ($cuenta == '0')? "":$cuenta;
$IdUsuario = CleanID(getSesionDato("IdUsuario"));
$IdLocal   = CleanID(getSesionDato("IdTienda"));
$xidprov   = (isset($_GET["xidprov"]))? CleanID($_GET["xidprov"]):0;
$xnro      = (isset($_GET["xnro"]))? CleanID($_GET["xnro"]):0;
//echo "...".$cuenta."..";
switch($modo){
  case "salvacuenta":
  
    $EntidadFinanciera = CleanText($_GET["financiera"]);
    $NumeroCuenta      = CleanText($_GET["nrocuenta"]);
    $IdMoneda          = CleanID($_GET["idmon"]);
    $IdProveedor       = CleanID($_GET["idprov"]);
    $Estado            = CleanText($_GET["estado"]);
    $Observaciones     = CleanText($_GET["obs"]);
  
    if ($EntidadFinanciera == "" || $NumeroCuenta== "") break;
    
    $sql = "SELECT IdCuentaBancaria FROM ges_cuentasbancarias 
            WHERE EntidadFinanciera='$NumeroCuenta'";
    $row = queryrow($sql);
  
    if ($row and $row["IdCuentaBancaria"]) 
      {
	$idold = $row["IdCuentaBancaria"];
	$sql = "UPDATE ges_cuentasbancarias SET Eliminado=0 WHERE IdCuentaBancaria='$idold'";
	query($sql);
	break;		
      }
    
    global $UltimaInsercion;
    query("INSERT INTO ges_cuentasbancarias 
           (IdLocal,IdUsuario,IdProveedorProv,IdMoneda,EntidadFinanciera,
            NumeroCuenta,Observaciones) 
           VALUES ('$IdLocal','$IdUsuario','$IdProveedor','$IdMoneda','$EntidadFinanciera',
                   '$NumeroCuenta','$Observaciones')");
    break;
      
  case "eliminacuenta":
    $idcuenta = CleanID($_GET["xid"]);
    $sql = "UPDATE ges_cuentasbancarias SET Eliminado=1 WHERE IdCuentaBancaria='$idcuenta'";
    query($sql);	
    break;
    
  case "modificacuenta":
    $IdProveedor       = CleanID($_GET["idprov"]);
    $IdMoneda          = CleanID($_GET["idmon"]);
    $idcuenta          = CleanID($_GET["xid"]);
    $NumeroCuenta      = CleanText($_GET["nrocuenta"]);
    $Observaciones     = CleanText($_GET["obs"]);
    $EntidadFinanciera = CleanText($_GET["financiera"]);
    $Estado            = CleanText($_GET["estado"]);
    
    $sql = "UPDATE ges_cuentasbancarias ".
           " SET IdProveedorProv = '$IdProveedor', ".
	   "     IdMoneda        = '$IdMoneda', ".
	   "     NUmeroCuenta    = '$NumeroCuenta', ".
	   "     EntidadFinanciera = '$EntidadFinanciera', ".
	   "     Observaciones   = '$Observaciones', ".
 	   "     Estado          = '$Estado' ".
           " WHERE IdCuentaBancaria='$idcuenta'";
    query($sql);
    break;
  default:
    break;	
}

SimpleAutentificacionAutomatica("visual-xulframe");
StartXul(_("Elija Cuenta"));
StartJs($js='modulos/pagoscobros/cuentabancaria.js?v=3.1');

//SE EJECUTA SIEMPRE

echo "<vbox class='box' flex='1'><groupbox> <caption label='Buscar Cuenta' class='box'/>";
echo "<hbox>";
echo "<textbox  flex='2' id='buscacuenta' onkeyup='BuscarCuenta();   if (event.which == 13) agnadirDirecto();' onkeypress='return soloAlfaNumerico(event)' value='".$cuenta."' />";
echo "<button id='btnNuevaCuenta' flex='1' label='"._("Nuevo")."' oncommand='VerFormCuenta(false)' collapsed='false' class='btn'/>";
echo "</hbox>";

echo "<groupbox id='formCuenta' collapsed='true'>";
echo "<caption id='titleCuentaBancaria' label='Nueva Cuenta Bancaria' class='box'/>";
echo "<grid>";
echo "<rows>";
echo "<row>";
echo "<caption label='Ent. Financiera'/>";
echo "<textbox id='txtEntFinanciera' style='width:20em'/>";
echo "</row>";
echo "<row>";
echo "<caption label='Nro Cuenta'/>";
echo "<textbox id='txtNroCuenta' style='width:20em'/>";
echo "</row>";
echo "<row id='rowMoneda'>";
echo "<caption label='Moneda'/>";
echo "<menulist id='listIdMoneda'>";
echo "<menupopup>".genXulComboMoneda('1','menuitem')."</menupopup>";
echo "</menulist>";
echo "</row>";
echo "<row id='rowProveedor' collapsed='true'>";
echo "<caption label='Proveedor'/>";
echo "<menulist id='listIdProveedor'>";
echo "<menupopup>".genXulComboProveedores($xidprov,'menuitem',false)."</menupopup>";
echo "</menulist>";
echo "</row>";
echo "<row id='rowEstadoCuenta' collapsed='true'>";
echo "<caption label='Estado'/>";
echo "<menulist id='listEstado'>";
echo "<menupopup>";
echo "<menuitem value='Activo' label='Activo'/>". 
     "<menuitem value='Inactivo' label='Inactivo'/>";
echo "</menupopup>";
echo "</menulist>";
echo "</row>";
echo "<row>";
echo "<caption label='Observaciones'/>";
echo "<textbox id='txtObservaciones' multiline='true' rows='1'/>";
echo "</row>";
echo "</rows>";
echo "</grid>";
echo "<hbox>";
echo "<button image="."'".$_BasePath."img/gpos_aceptar.png'"." id='btnGuardaCuenta' flex='1' label='"._("Guardar")."' oncommand='GuardarCreaCuenta()' collapsed='false' class='btn'/>";
echo "<button image="."'".$_BasePath."img/gpos_cancelar.png'"."  id='btnCancelaCuenta' flex='1' label='"._("Cancelar")."' oncommand='CancelarCreaCuenta()' collapsed='false' class='btn'/>";
echo "</hbox>";
echo "</groupbox>";

echo "</groupbox>";

echo "<groupbox id='ListaCuentas'><caption label='" . _("Cuentas") . "' class='box'/>";

$acuentas = genArrayCuentaBancaria($xidprov,false);
$combo = "";
echo "<script>\n";
echo " cta = new Object();\n";
echo " txtcta = new Object();\n";
echo " stcta = new Object();\n";
echo " nro   = '$xnro';\n";
foreach ($acuentas as $key=>$value){
  $valor = explode("~",$value);
  $xvalor = "(".$valor[0].") ".$valor[1]." ".$valor[2];
  echo "cta[$key] = '$xvalor';\n";
  if($xnro == 0)
    echo "txtcta[$key] = '$valor[2]';\n";
  else
    echo "txtcta[$key] = '$valor[1] $valor[2]';\n";
  echo "stcta[$key] = '$value';\n";
}
echo "xidprov='$xidprov';\n";
echo "\n</script>";						
echo "<listbox id='listboxCuenta' ondblclick='parent.changeNroCuenta(this,txtcta[this.value],xidprov,nro);parent.closepopup();return true;' onkeypress='if (event.which == 13) { parent.changeNroCuenta(this,txtcta[this.value],xidprov,nro);parent.closepopup();return true;}' contextmenu='accionesListaCuentas' >\n";
echo "<listcols flex='1'>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "</listcols>";
echo "</listbox>";

echo "<popupset>
       <popup id='accionesListaCuentas'> 
        <menuitem  label='Modificar' oncommand='ModificarDatoCuenta()'/>
        <menuitem  label='Eliminar' oncommand='EliminarCuenta()'/>
       </popup>
      </popupset>";
echo "</groupbox></vbox>";

EndXul();

?>
