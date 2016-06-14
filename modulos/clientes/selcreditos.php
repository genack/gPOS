<?php

include("../../tool.php");

$credito   = (isset($_GET["xcredit"]))? CleanText($_GET["xcredit"]):'0';
$cliente   = CleanText($_GET["xcliente"]);
$IdUsuario = CleanText($_GET["xidu"]);
$IdUsuario = ($IdUsuario)? $IdUsuario:CleanID(getSesionDato("IdUsuario"));
$IdLocal   = CleanID(getSesionDato("IdTienda"));
$IdCliente = (isset($_GET["xidc"]))? CleanID($_GET["xidc"]):0;
$xnro      = (isset($_GET["xnro"]))? CleanID($_GET["xnro"]):0;
$cuentabancaria = getSesionDato("CuentaBancaria");
$cuentabancaria = ($cuentabancaria || $cuentabancaria > 0)? $cuentabancaria:'false';

switch($modo){
  case "salvacredito":
  
    $Importe       = CleanFloat($_GET["importe"]);
    $Concepto      = CleanText($_GET["concepto"]);
    $fechaop       = CleanCadena($_GET["fechaop"]);
    $IdCuenta      = CleanID($_GET["idcuenta"]);
    $CodOperacion  = CleanText($_GET["codop"]);
    $Observaciones = CleanText($_GET["obs"]);
    $IdCliente     = CleanID($_GET["xidc"]);
    $IdUsuario     = CleanID($_GET["xidu"]);
    $IdLocal       = getSesionDato("IdTienda");
    $TipoCredito   = CleanText($_GET["xtipo"]);
    
    if($IdCliente <= 1 || !$IdCliente) break;
  
    if ($Importe <= 0.01) break;


    //Registra monto en cliente credito
    $xidccredito = registrarMovimientoCreditoCliente($IdCliente,$Importe,0,$IdLocal,
						     0,$IdUsuario,$Concepto,$fechaop);

    if($TipoCredito == 'Efectivo'){
      //Ingresar monto a caja
      $xconcepto = "Asignación de nota crédito cliente ".obtenerNombreCliente($IdCliente);
      $TipoVenta = getSesionDato("TipoVentaTPV");
      $arqueo    = new movimiento;
      $IdArqueo  = $arqueo->GetArqueoActivo($IdLocal);
      $FechaCaja = $arqueo->getAperturaCaja($IdLocal,$TipoVenta);
      
      EntregarOperacionCaja($IdLocal,$Importe,$xconcepto,0,'Ingreso',
			    $FechaCaja,$IdArqueo,$TipoVenta);

    }else{

     if(verificarCodigoOperacion($CodOperacion,$IdCuenta))
         break;
        
      // registro clientes cobros doc y movimientos bancario
      $doccobro["id"] = $IdCuenta;
      $doccobro["op"] = $CodOperacion;
      $doccobro["doc"] = '';
      $doccobro["obs"] = $Observaciones;

      GuardarDocumentosCobros(0,$doccobro,$IdLocal,$IdUsuario,$xidccredito,$fechaop);

      // movimiento bancario
      RegistrarMovimientoBancario($IdLocal,0,0,$IdUsuario,$IdCuenta,'Ingreso',$Concepto,
				  $Importe);
    }

    break;
      
  case "eliminacredito":
    $idcredito = CleanID($_GET["xid"]);
	
    break;
    
  case "modificacredito":

    break;
  default:
    break;	
}

SimpleAutentificacionAutomatica("visual-xulframe");
StartXul(_("Créditos Cliente"));
StartJs($js='modulos/clientes/creditos.js?v=4.2');

//SE EJECUTA SIEMPRE

echo "<vbox class='box' flex='1'>";
echo "<groupbox> <caption label='Cliente' class='box'/>";
echo "<hbox>";
echo "<description class='xbase'  flex='1' id='buscacuenta' value='".$cliente."' />";
echo "</hbox>";
echo "</groupbox>";

echo "<vbox align='center'>";
echo "<groupbox id='formCreditoCliente' collapsed='true' >";
echo "<caption id='titleCreditoCliente' label='Nuevo Crédito' class='box' />";
echo "<grid>";
echo "<rows>";
echo "<row id='rowTipoCredito' >";
echo "<caption label='Tipo Crédito'/>";
echo "<menulist id='listCredito' oncommand='changeTipoCredito(this.value)'>";
echo "<menupopup>";
echo "<menuitem value='Efectivo' label='Efectivo'/>". 
     "<menuitem value='Bancario' label='Bancario'/>";
echo "</menupopup>";
echo "</menulist>";
echo "</row>";
echo "<row>";
echo "<caption label='Monto'/>";
echo "<textbox id='txtImporte' onkeypress='return soloNumeros(event,this.value)' style='width:25em'/>";
echo "</row>";
echo "<row>";
echo "<caption label='Concepto'/>";
echo "<textbox id='txtConcepto' onkeypress='return soloAlfaNumerico(event)' style='width:20em'/>";
echo "</row>";
echo "<row>";
echo "<caption label='Fecha Operación'/>";
echo "<hbox>";
echo "<datepicker id='txtFechaOperacion' />";
echo "<timepicker id='txtHoraOperacion' />";
echo "</hbox>";
echo "</row>";
echo "<row id='rowCuenta' collapsed='true'>";
echo "<caption label='Nro Cuenta'/>";
echo "<menulist id='listIdCuentaBancaria'>";
echo "<menupopup>".genXulComboCuentaBancaria2($cuentabancaria,'menuitem','1')."</menupopup>";
echo "</menulist>";
echo "</row>";
echo "<row id='rowCodOperacion' collapsed='true'>";
echo "<caption label='Código Operación'/>";
echo "<textbox id='txtCodigoOperacion' onkeypress='return soloAlfaNumerico(event)' style='width:20em' value='000000'/>";
echo "</row>";
echo "<row id='rowObservaciones' collapsed='true'>";
echo "<caption label='Observaciones'/>";
echo "<textbox id='txtObservaciones' onkeypress='return soloAlfaNumerico(event)' multiline='true' rows='1'/>";
echo "</row>";
echo "</rows>";
echo "</grid>";
echo "<hbox>";
echo "<button image="."'".$_BasePath."img/gpos_aceptar.png'"." id='btnGuardaCreditoCliente' flex='1' label='"._("Guardar")."' oncommand='GuardarCreditoCliente()' collapsed='false' class='btn'/>";
echo "<button image="."'".$_BasePath."img/gpos_cancelar.png'"."  id='btnCancelaCuenta' flex='1' label='"._("Cancelar")."' oncommand='CancelarCreaCuenta()' collapsed='false' class='btn'/>";
echo "</hbox>";
echo "</groupbox>";
echo "</vbox>";

echo "<hbox pack='center' id='boxbusqueda'>";
echo "<vbox>";
echo "<description>Desde:</description>";
echo "<datepicker id='fechaDesde' type='popup' align='center'/>";
echo "</vbox>";
echo "<vbox>";
echo "<description>Hasta:</description>";
echo "<datepicker id='fechaHasta' type='popup' align='center'/>";
echo "</vbox>";
echo "<vbox>";
echo "<description>Movimiento:</description>";
echo "<menulist id='listTipoMovimiento' >";
echo "<menupopup>";
echo "<menuitem value='todos' label='Todos'/>". 
     "<menuitem value='0' label='Entrada'/>". 
     "<menuitem value='1' label='Salida'/>";
echo "</menupopup>";
echo "</menulist>";
echo "</vbox>";
echo "<vbox style='margin-top:1.1em'>";
echo "<button id='btnbuscar' class='btn' label=' Buscar ' image='../../img/gpos_buscar.png' oncommand='BuscarCreditos()'></button>";
echo "</vbox>";
echo "</hbox>";

echo "<groupbox id='ListaCreditoCliente' flex='1'>";
echo "<caption label='" . _("Movimientos créditos") . "' class='box'/>";

echo "<script>\n";
echo "xidc='$IdCliente';\n";
echo "xcliente='$cliente';\n";
echo "xidu='$IdUsuario';\n";
echo "xcredito='$credito';\n";
echo "\n</script>";						
echo "<listbox id='listboxCredito' ondblclick='ModificarDatoCredito();' onkeypress='if (event.which == 13) { ModificarDatoCredito();}' contextmenu='accionesListaCreditos' flex='1'>\n";
echo "<listcols flex='1'>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "<listcol></listcol>";
echo "<splitter class='tree-splitter'></splitter>";
echo "</listcols>";
echo "<listhead>";
echo "<listheader label='#' style='font-style:italic;'/>";
echo "<listheader label='Fecha Operación'/>";
echo "<listheader label='Movimiento'/>";
echo "<listheader label='Concepto'/>";
echo "<listheader label='Importe'/>";
echo "<listheader label='Cuenta'/>";
echo "<listheader label='Cod Operación'/>";
echo "</listhead>";
echo "</listbox>";

echo "<vbox class='box' id='boxResumenCredito'>";
echo "  <caption class='box' label='Resumen Pedidos' />";
echo "  <hbox  class='resumen' pack='center' align='left'>";
echo "    <label value='Total Entrada'/>";
echo "    <description id='TotalEntrada' value='' />";
echo "    <label value='Total Salida'/>";
echo "    <description id='TotalSalida' value='' />";
echo "    <label value='Total Crédito'/>";
echo "    <description id='TotalCredito' value='' />";
echo "  </hbox>";
echo "</vbox>";

echo "<popupset>
       <popup id='accionesListaCreditos'> ".
//        <menuitem  label='Modificar' oncommand='ModificarDatoCredito()'/>
//        <menuitem  label='Eliminar' oncommand='EliminarCredito()'/>
       "</popup>
      </popupset>";
echo "<button id='btnNuevoCredto' flex='0' label='"._("Nuevo")."' oncommand='VerFormCreditoCliente(false)' collapsed='false' class='btn'/>";
echo "</groupbox></vbox>";

EndXul();

?>
