<?php

require("../../tool.php");
SimpleAutentificacionAutomatica("visual-xulframe");

$esTPV    = ($_GET["area"]=="tpv");
$TipoCaja = ($esTPV)? getSesionDato("TipoVentaTPV"):"CG";
$IdLocal  = getSesionDato("IdTienda");
$esBTCA   = ( getSesionDato("GlobalGiroNegocio") == "BTCA" )? true:false;

$area = CleanRealMysql($_GET["area"]);

if($area){
	 $sqlarea = " AND (Area ='$area') ";	
}


$outItems = "";

$sql = "SELECT IdListado,NombrePantalla,CodigoSQL,Peso,Categoria FROM ges_listados WHERE (Eliminado=0) $sqlarea ORDER BY Categoria DESC, NombrePantalla ASC";

$res = query($sql);



// function to change german umlauts into ue, oe, etc.
function cv_input($str){
     $out = "";
     for ($i = 0; $i<strlen($str);$i++){
           $ch= ord($str{$i});
           switch($ch){
               case 241: $out .= "&241;"; break;           
               case 195: $out .= "";break;   
               case 164: $out .= "ae"; break;
               case 188: $out .= "ue"; break;
               case 182: $out .= "oe"; break;
               case 132: $out .= "Ae"; break;
               case 156: $out .= "Ue"; break;
               case 150: $out .= "Oe"; break;

               default : $out .= chr($ch) ;
           }
     }
     return $out;
}

function strictify ( $string ) {
       $fixed = htmlspecialchars( $string, ENT_QUOTES );

       $trans_array = array();
       for ($i=127; $i<255; $i++) {
           $trans_array[chr($i)] = "&#" . $i . ";";
       }

       $really_fixed = strtr($fixed, $trans_array);

       return $really_fixed;
}
$count = 0;
$Cat   = "";
$code  = "";
if ($res) {
	while ($row = Row($res)) {
	  $count++;
		$NombrePantalla = $row["NombrePantalla"];		
		$id = $row["IdListado"];		
		
		$activos = DetectaActivos( $row["CodigoSQL"]);
		$code .= $row["CodigoSQL"] . "\n----------------------------------\n";

		$NombrePantalla = cv_input($NombrePantalla);					
		$NombrePantalla = strictify($NombrePantalla);
		
		$peso      = $row["Peso"];
		$Categoria = $row["Categoria"];
		$style = "";

		if($Categoria != $Cat){
		  $Cat_label = (!$Categoria)? "Sin Categoría":$Categoria;
		  $style = "font-weight: bold;text-decoration: underline;font-size:13px!important";
		  $outItems = $outItems.
		              "<menuitem style='$style' label=' $Cat_label' value='0'/>\n";
		}
		
		if ($peso){
		  $style="font-weight: bold";	
		} else {
		  $style="";
		}

		if(!$esBTCA){
		  $Cat = $Categoria;
		  if($NombrePantalla == "CON REGISTRO SANITARIO" )  continue;
		  if($NombrePantalla == "CON CONDICION DE VENTA" )  continue;
		}

		$outItems = $outItems . "<menuitem style='$style' label='  $id. $NombrePantalla' value='$id' oncommand='SetActive(\"$activos\",\"$Categoria\")'/>\n";

		$Cat = $Categoria;
	}
}
		
		

function DetectaActivos($cod){
	global $esTPV;
	$a = "";
	
	if( strpos($cod,'%IDIDIOMA%') >0 ){
		$a .= "IdIdioma,";	
	}
	if( strpos($cod,'%DESDE%')  >0){
		$a .= "Desde,";	
	}
	if( strpos($cod,'%HASTA%') >0){
		$a .= "Hasta,";	
	}
	if( strpos($cod,'%IDTIENDA%')  >0 and !$esTPV){
		$a .= "IdTienda,";	
	}
	if( strpos($cod,'%IDFAMILIA%')  >0){
		$a .= "IdFamilia,";	
	}	
	if( strpos($cod,'%IDSUBFAMILIA%')  >0){
		$a .= "IdSubFamilia,";	
	}	
	if( strpos($cod,'%IDARTICULO%')  >0){
		$a .= "IdArticulo,";	
	}		
	if( strpos($cod,'%FAMILIA%')  >0){
		$a .= "Familia,";	
	}	
	if( strpos($cod,'%IDSUBSIDIARIO%')  >0){
		$a .= "IdSubsidiario,";	
	}
	if( strpos($cod,'%STATUSTBJOSUBSIDIARIO%')  >0){
		$a .= "StatusTrabajo,";	
	}
	if( strpos($cod,'%IDPROVEEDOR%')  >0){
		$a .= "IdProveedor,";	
	}
	if( strpos($cod,'%IDUSUARIO%')  >0){
		$a .= "IdUsuario,";	
	}						
	if( strpos($cod,'%REFERENCIA%')  >0){
		$a .= "Referencia,";	
	}						
	if( (strpos($cod,'%IDPRODBASEDESDECB%')>0) or (strpos($cod,'%CODIGOBARRAS%')>0) ){
		$a .= "CB,";	
	}
	if(strpos($cod,'%NUMEROSERIE%') > 0){
	        $a .= "NumeroSerie,";
	}
	if(strpos($cod,'%LOTE%') > 0){
	        $a .= "Lote, ";
	}
	if(strpos($cod,'%PARTIDA%') > 0){
	        $a .= "Partida,";
	}
	if(strpos($cod,'%TIPOVENTAOP%') > 0 and !$esTPV){
	        $a .= "TipoVentaOP,";
	}	
	if(strpos($cod,'%DNICLIENTE%') > 0){
	        $a .= "DNICliente,";
	}	
	if(strpos($cod,'%TIPOCOMPROBANTE%') > 0){
	        $a .= "TipoComprobante,";
	}
	if(strpos($cod,'%SERIECOMPROBANTE%') > 0){
	        $a .= "SerieComprobante,";
	}
	if(strpos($cod,'%ESTADOCOMPROBANTE%') > 0){
	        $a .= "EstadoComprobante,";
	}
	if(strpos($cod,'%ESTADOPAGO%') > 0){
	        $a .= "EstadoPago,";
	}	
	if(strpos($cod,'%MODALIDAD%') > 0){
	        $a .= "Modalidad,";
	}
	if(strpos($cod,'%ESTADOPROMO%') > 0){
	        $a .= "EstadoPromo,";
	}
	if(strpos($cod,'%TIPOPROMO%') > 0){
	        $a .= "TipoPromo,";
	}	
	if(strpos($cod,'%TIPOOPERACION%') > 0){
	        $a .= "TipoOperacion,";
	}	
	if(strpos($cod,'%TIPOOPCJAGRAL%') > 0){
	        $a .= "TipoOpCjaGral,";
	}	
	if(strpos($cod,'%PERIODOVENTA%') > 0){
	        $a .= "PeriodoVenta,";
	}	
	if(strpos($cod,'%CLIENTE%') > 0){
	        $a .= "NombreCliente,";
	}	
	if(strpos($cod,'%TIPOCLIENTE%') > 0){
	        $a .= "TipoCliente,";
	}	
	if(strpos($cod,'%IDMARCA%') > 0){
	        $a .= "IdMarca,";
	}	
	if(strpos($cod,'%CONDICIONVENTA%') > 0){
	        $a .= "CondicionVenta,";
	}	
	if(strpos($cod,'%ESTADOOS%') > 0){
	        $a .= "EstadoOS,";
	}	
	if(strpos($cod,'%PRIORIDAD%') > 0){
	        $a .= "Prioridad,";
	}	
	if(strpos($cod,'%FACTURACION%') > 0){
	        $a .= "Facturacion,";
	}	
	if(strpos($cod,'%ESTADOSUSCRIPCION%') > 0){
	        $a .= "EstadoSuscripcion,";
	}	
	if(strpos($cod,'%TIPOSUSCRIPCION%') > 0){
	        $a .= "TipoSuscripcion,";
	}	
	if(strpos($cod,'%TIPOPAGOSUSCRIPCION%') > 0){
	        $a .= "TipoPagoSuscripcion,";
	}	
	if(strpos($cod,'%PROLONGACION%') > 0){
	        $a .= "Prolongacion,";
	}	
	if(strpos($cod,'%IDCLIENTE%') > 0){
	        $a .= "IdCliente,";
	}	
	if(strpos($cod,'%CODIGO%') > 0){
	        $a .= "Codigo,";
	}	
	if(strpos($cod,'%IMPORTE%') > 0){
	        $a .= "EstadoPagoVenta,";
	}	
	if(strpos($cod,'%COBRANZA%') > 0){
	        $a .= "Cobranza,";
	}	
	if(strpos($cod,'%CODIGOCOMPROBANTE%') > 0){
	        $a .= "CodigoComprobante,";
	}	
	if(strpos($cod,'%PRESUPUESTOVENTA%') > 0){
	        $a .= "PresupuestoVenta,";
	}	
	if(strpos($cod,'%IDMONEDA%') > 0){
	        $a .= "IdMoneda,";
	}	
	if(strpos($cod,'%ESTADOPRESUPUESTO%') > 0){
	        $a .= "EstadoPresupuesto,";
	}	
	if(strpos($cod,'%VENTAESTADO%') > 0){
	        $a .= "VentaEstado,";
	}	
	if(strpos($cod,'%TIPOVENTATPV%') > 0){
	        $a .= "TipoVenta,";
	}	
	if(strpos($cod,'%PRODUCTOIDIOMA%') > 0){
	        $a .= "ProductoIdioma,";
	}	
	if(strpos($cod,'%MODALIDADPAGO%') > 0){
	        $a .= "ModalidadPago,";
	}	
	if(strpos($cod,'%CUENTABANCARIA%') > 0){
	        $a .= "CuentaBancaria,";
	}	

	return $a;
}

StartXul('listados',$predata="",$css='');
?>
<vbox class="box">
  <groupbox>	
    <hbox align="center" pack="center" >
      <label id="CategoriaListado" value="" pack="center"/>
      <menulist    id="esListas" label="Listados" class="media">
	<menupopup style="text-align: left;">
	  <menuitem style="width: 300px;font-weight:bold;" label="ELIJA LISTADO"/>
	  <?php echo $outItems; ?>
	</menupopup>
      </menulist>
      <button id="btnListado" image="../../img/gpos_listados.png" label="Listar" oncommand="CambiaListado()" class="btn"/>
    </hbox>

    <hbox align="start" pack="center" class="box">
      <hbox id="getDesde" collapsed="true" align="center">
	<vbox>
	  <label value="Desde:"/>
	  <datepicker id="Desde" type="popup"/>
	</vbox>
      </hbox>

      <hbox id="getHasta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Hasta:"/>
	  <datepicker id="Hasta" type="popup"/>
	</vbox>
      </hbox>

      <hbox id="getIdTienda" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Local:"/>
	  <menulist  id="Local">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
	      <?php echo genXulComboAlmacenes(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoCaja" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Caja:"/>
	  <menulist  id="TipoCaja" oncommand="obtenerPartidasCaja(this.value)">
            <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem value="VD" label="TPV Personal"/>
	      <menuitem value="VC" label="TPV Corporativo"/>
	      <menuitem value="CG" label="General"/>
	    </menupopup>
	  </menulist>
        </vbox>	
      </hbox>		

      <hbox id="getPartida" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Partida:"/>
	  <menulist  id="Partida">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
	      <?php echo genXulComboPartidaCaja(false,"menuitem",$IdLocal,false,$TipoCaja) ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getIdProveedor" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Proveedor:"/>
	  <menulist  id="Proveedor">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <?php echo genXulComboProveedores(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>	

      <hbox id="getTipoVentaOP" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Venta Operación:"/>
	  <menulist  id="TipoVentaOP">						
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem value="VD" label="B2C"/>
	      <menuitem value="VC" label="B2B"/>
	    </menupopup>
	  </menulist>
        </vbox>	
      </hbox>		
	
      <hbox id="getIdFamilia" collapsed="true" align="center">
        <vbox>
          <label value="Familia:"/>
	  <menulist  id="IdFamilia">						
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <?php echo genXulComboFamilias(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>	

      <hbox id="getIdMarca" collapsed="true" align="center">
        <vbox>
          <label value="Marca:"/>
	  <menulist  id="IdMarca">						
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <?php echo genXulComboMarcas(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>	

      <hbox id="getIdSubsidiario" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Subsidiario:"/>
	  <menulist  id="Subsidiario">						
	    <menupopup>
	      <menuitem label="Elije Subsidiario"/>
	      <?php echo genXulComboSubsidiarios(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>		

      <hbox id="getStatusTrabajo" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Estado Trabajo:"/>
	  <menulist  id="StatusTrabajo">						
	    <menupopup>
	      <menuitem label="Elije estado"/>
	      <?php echo genXulComboStatusTrabajo(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>		
	
      <hbox id="getIdUsuario" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Usuario:"/>
	  <menulist  id="IdUsuario">						
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
              <?php echo genXulComboUsuarios(false,"menuitem",$IdLocal) ?>
	    </menupopup>
	  </menulist>	
        </vbox>
      </hbox>			

      <hbox id="getTipoComprobante" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Tipo Comprobante:"/>
	  <menulist  id="TipoComprobante">						
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Factura" value="Factura"/>
	      <menuitem label="Boleta" value="Boleta"/>
	      <menuitem label="Ticket" value="Ticket"/>
	      <menuitem label="Albaran" value="Albaran"/>
	    </menupopup>
	  </menulist>	
        </vbox>
      </hbox>			

      <hbox id="getTipoCliente" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Tipo Cliente:"/>
	  <menulist  id="TipoCliente">						
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Particular" value="Particular"/>
	      <menuitem label="Independiente" value="Independiente"/>
	      <menuitem label="Empresa" value="Empresa"/>
	      <menuitem label="Gobierno" value="Gobierno"/>
	    </menupopup>
	  </menulist>
        </vbox>
      </hbox>			

      <hbox id="getEstadoComprobante" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Estado Comprobante:"/>
	  <menulist  id="EstadoComprobante">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Confirmado" value="Confirmado"/>
	      <menuitem label="Pendiente" value="Pendiente"/>
	      <menuitem label="Cancelado" value="Cancelado"/>
	      <menuitem label="Borrador" value="Borrador"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoPago" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado Pago:"/>
	  <menulist  id="EstadoPago">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pagada" value="Pagada"/>
	      <menuitem label="Empezada" value="Empezada"/>
	      <menuitem label="Pendiente" value="Pendiente"/>
	      <menuitem label="Vencida" value="Vencida"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getModalidad" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Modalidad:"/>
	  <menulist  id="Modalidad">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Monto Compra" value="MontoCompra"/>
	      <menuitem label="Historial Compra" value="HistorialCompra"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoPromo" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado:"/>
	  <menulist  id="EstadoPromo">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Borrador" value="Borrador"/>
	      <menuitem label="Ejecución" value="Ejecucion"/>
	      <menuitem label="Finalizado" value="Finalizado"/>
	      <menuitem label="Suspendido" value="Suspendido"/>
	      <menuitem label="Cancelado" value="Cancelado"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoPromo" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo:"/>
	  <menulist  id="TipoPromo">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Descuento" value="Descuento"/>
	      <menuitem label="Producto" value="Producto"/>
	      <menuitem label="Bono" value="Bono"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoOperacion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo Operación:"/>
	  <menulist  id="TipoOperacion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Ingreso" value="Ingreso"/>
	      <menuitem label="Gasto" value="Gasto"/>
	      <menuitem label="Aportación" value="Aportacion"/>
	      <menuitem label="Sustracción" value="Sustraccion"/>  
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoOpCjaGral" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo Operación:"/>
	  <menulist  id="TipoOpCjaGral">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Ingreso" value="Ingreso"/>
	      <menuitem label="Egreso" value="Egreso"/>
	      <menuitem label="Gasto" value="Gasto"/>
	      <menuitem label="Aportación" value="Aportacion"/>
	      <menuitem label="Sustracción" value="Sustraccion"/>  
	 </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getPeriodoVenta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Periodo Venta:"/>
	  <menulist  id="PeriodoVenta">
	    <menupopup>
	      <menuitem label="Días" value="DAY" selected="true"/>
	      <menuitem label="Semanal" value="WEEK"/>
	      <menuitem label="Mensual" value="MONTH"/>
	      <menuitem label="Anual" value="YEAR"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getCondicionVenta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Condición Venta:"/>
	  <menulist  id="CondicionVenta">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Sin Receta" value="0" />
	      <menuitem label="Con Receta" value="CRM"/>
	      <menuitem label="Con Receta Retenida" value="CRMR"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoOS" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado:"/>
	  <menulist  id="EstadoOS">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pendiente" value="Pendiente" />
	      <menuitem label="Ejecución" value="Ejecucion"/>
	      <menuitem label="Finalizado" value="Finalizado"/>
	      <menuitem label="Cancelado" value="Cancelado"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getPrioridad" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Prioridad:"/>
	  <menulist  id="Prioridad">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Normal" value="1" />
	      <menuitem label="Alta" value="2"/>
	      <menuitem label="Muy Alta" value="3"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getFacturacion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Facturación:"/>
	  <menulist  id="Facturacion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pendiente" value="0"/>
	      <menuitem label="Facturado" value="1"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoSuscripcion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado:"/>
	  <menulist  id="EstadoSuscripcion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pendiente" value="Pendiente" />
	      <menuitem label="Ejecución" value="Ejecucion"/>
	      <menuitem label="Suspendido" value="Suspendido"/>
	      <menuitem label="Finalizado" value="Finalizado"/>
	      <menuitem label="Cancelado" value="Cancelado"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoSuscripcion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Suscripción:"/>
	  <menulist  id="TipoSuscripcion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <?php echo genXulComboTipoSuscripcion(false,"menuitem",false) ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoPagoSuscripcion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo Pago:"/>
	  <menulist  id="TipoPagoSuscripcion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pre-Pago" value="Prepago"/>
	      <menuitem label="Post-Pago" value="Postpago"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getProlongacion" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Prolongación:"/>
	  <menulist  id="Prolongacion">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Plazo Limitado" value="Limitado"/>
	      <menuitem label="Plazo Ilimitado" value="Ilimitado"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoPagoVenta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado Pago:"/>
	  <menulist  id="EstadoPagoVenta">
	    <menupopup>
	      <menuitem label="Todos" value="like '%%'"/>
	      <menuitem label="Pendiente" value="> 0"/>
	      <menuitem label="Pagado" value="= 0"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getIdCliente" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Cliente:"/>
	  <menulist  id="IdCliente">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
	      <?php echo genXulComboClientes(false,"menuitem",false) ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getCobranza" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado Cobranza:"/>
	  <menulist  id="Cobranza">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Pendiente" value="Pendiente" />
	      <menuitem label="Prórroga" value="Prorroga"/>
	      <menuitem label="Coactivo" value="Coactivo"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getPresupuestoVenta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo Pedido:"/>
	  <menulist  id="PresupuestoVenta">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Proforma" value="Proforma"/>
	      <menuitem label="Preventa" value="Preventa"/>
	      <menuitem label="Proforma Online" value="ProformaOnline"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getEstadoPresupuesto" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado:"/>
	  <menulist  id="EstadoPresupuesto">
	    <menupopup>
	      <menuitem label="Todos" value="%%"/>
	      <menuitem label="Confirmado" value="Confirmado" selected="true"/>
	      <menuitem label="Pendiente" value="Pendiente"/>
	      <menuitem label="Vencido" value="Vencido"/>
	      <menuitem label="Cancelado" value="Cancelado"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getIdMoneda" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Moneda:"/>
	  <menulist  id="IdMoneda">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
	      <?php echo genXulComboMoneda(false,"menuitem",false) ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getTipoVenta" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Tipo Venta:"/>
	  <menulist  id="TipoVenta">
	    <menupopup>
	      <menuitem label="Todos" value="%VentaTodos%"/>
	      <menuitem label="Contado" value="%VentaContado%"/>
	      <menuitem label="Crédito" value="%VentaCredito%"/>
	      <menuitem label="Reserva" value="%VentaReserva%"/>
	      <menuitem label="Suscripción" value="%VentaSuscripcion%"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getVentaEstado" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Estado:"/>
	  <menulist  id="VentaEstado">
	    <menupopup>
	      <menuitem label="Todos" value="%EstadoTodos%"/>
	      <menuitem label="Pendiente" value="%EstadoPendiente%"/>
	      <menuitem label="Finalizado" value="%EstadoFinalizado%"/>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getModalidadPago" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Modalidad Pago:"/>
	  <menulist  id="IdModalidadPago">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
	      <?php echo genXulComboModalidadPago(false,"menuitem") ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getCuentaBancaria" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Cuenta Bancaria:"/>
	  <menulist  id="CuentaBancaria">
	    <menupopup>
	      <menuitem label="Todos" value="%%" selected="true"/>
              <?php echo genXulComboCuentaBancaria2(false,"menuitem",false) ?>
	    </menupopup>
	  </menulist>
	</vbox>
      </hbox>

      <hbox id="getSerieComprobante" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
          <label value="Serie Comprobante:"/>
	  <textbox class="media" id="SerieComprobante" value=""/>
        </vbox>
      </hbox>

      <hbox id="getReferencia" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Referencia:"/>
	  <textbox class="media" id="Referencia" value=""/>
        </vbox>
      </hbox>			

      <hbox id="getCB" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Codigo barras:"/>
	  <textbox class="media" id="CB" value=""/>
        </vbox>
      </hbox>

      <hbox id="getNumeroSerie" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Numero Serie:"/>
	  <textbox class="media" id="NumeroSerie" value=""/>
        </vbox>
      </hbox>				
	
      <hbox id="getLote" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Lote:"/>
	  <textbox class="media" id="Lote" value=""/>
        </vbox>
      </hbox>	

      <hbox id="getDNICliente" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="DNI/RUC:"/>
	  <textbox class="media" id="DNICliente" value=""/>
        </vbox>
      </hbox>			

      <hbox id="getNombreCliente" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Cliente:"/>
	  <textbox class="media" id="NombreCliente" value=""/>
	</vbox>
      </hbox>

      <hbox id="getCodigo" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Código:"/>
	  <textbox class="media" id="Codigo" value=""/>
	</vbox>
      </hbox>

      <hbox id="getCodigoComprobante" collapsed="true" align="center">
	<spacer style="width: 5px"/>
	<vbox>
	  <label value="Código Factura:"/>
	  <textbox class="media" id="CodigoComprobante" value=""/>
	</vbox>
      </hbox>

      <hbox id="getProductoIdioma" collapsed="true" align="center">
	<spacer style="width: 5px"/>
        <vbox>
	  <label value="Producto:"/>
	  <textbox class="media" id="ProductoIdioma" value=""/>
        </vbox>
      </hbox>

    </hbox>
  </groupbox>
</vbox>
<vbox class="box" flex="1">
  <iframe id="webarea" src="about:blank" flex='1'/>
</vbox>
  <script><![CDATA[

var esTPV = <?php echo intval($esTPV); ?>;
var IdLocalActual = <?php echo intval(getSesionDato("IdTienda")); ?>;
var esTPVOP  = "<?php echo getSesionDato("TipoVentaTPV"); ?>";
function id(nombre) { return document.getElementById(nombre); };

function ActivarCambioListado(xval){
  id("btnListado").setAttribute("disabled",xval);
}

function CambiaListado() {
        ActivarCambioListado(true);
	setTimeout("ActivarCambioListado(false)",5000);
	var idlista 	= id("esListas").value;

	if(!idlista || idlista == 0) return;

	var web 	= id("webarea");
	var SerieComprobante = id("SerieComprobante").value;
	var NombreCliente    = id("NombreCliente").value;
	var DNICliente       = id("DNICliente").value;
        var Codigo           = id("Codigo").value;

	SerieComprobante = (SerieComprobante == "")? "%%":SerieComprobante;
	NombreCliente    = (NombreCliente == "")? "%%":NombreCliente;
	DNICliente       = (DNICliente == "")? "%%":DNICliente;
        Codigo           = (Codigo == "")? "%%":Codigo;

	var url = "listado.php?id="+idlista+
		"&Desde="+id("Desde").value +
		"&Hasta="+id("Hasta").value +		
		"&IdFamilia="+id("IdFamilia").value+
	        "&IdProveedor="+id("Proveedor").value+
		"&IdSubsidiario="+id("Subsidiario").value+
		"&StatusTrabajoSubsidiario="+id("StatusTrabajo").value+		
		"&IdUsuario="+id("IdUsuario").value+
		"&Referencia="+ escape(id("Referencia").value)+
		"&cb="+escape(id("CB").value)+
	        "&ns="+escape(id("NumeroSerie").value)+
	        "&lote="+escape(id("Lote").value)+
	        "&partida="+escape(id("Partida").value)+
	        "&dnicliente="+escape(DNICliente)+
	        "&tipocomprobante="+escape(id("TipoComprobante").value)+
	        "&seriecomprobante="+escape(SerieComprobante)+
	        "&estadocomprobante="+escape(id("EstadoComprobante").value)+
	        "&estadopago="+escape(id("EstadoPago").value)+
	        "&modalidad="+escape(id("Modalidad").value)+
	        "&estadopromo="+escape(id("EstadoPromo").value)+
	        "&tipopromo="+escape(id("TipoPromo").value)+
	        "&tipooperacion="+escape(id("TipoOperacion").value)+
	        "&tipoopcjagral="+escape(id("TipoOpCjaGral").value)+
	        "&periodoventa="+escape(id("PeriodoVenta").value)+
	        "&nombrecliente="+NombreCliente+
	        "&tipocliente="+escape(id("TipoCliente").value)+
	        "&idmarca="+escape(id("IdMarca").value)+
	        "&condicionventa="+escape(id("CondicionVenta").value)+
	        "&estadoos="+escape(id("EstadoOS").value)+
	        "&prioridad="+escape(id("Prioridad").value)+
	        "&facturacion="+escape(id("Facturacion").value)+
	        "&estadosucripcion="+escape(id("EstadoSuscripcion").value)+
	        "&tiposuscripcion="+escape(id("TipoSuscripcion").value)+
	        "&tipopagosuscripcion="+escape(id("TipoPagoSuscripcion").value)+
	        "&prolongacion="+escape(id("Prolongacion").value)+
	        "&idcliente="+escape(id("IdCliente").value)+
	        "&cobranza="+escape(id("Cobranza").value)+
	        "&codcomprobante="+escape(id("CodigoComprobante").value)+
	        "&codigo="+escape(Codigo)+
	        "&estadopagoventa="+escape(id("EstadoPagoVenta").value)+
	        "&presupuestoventa="+escape(id("PresupuestoVenta").value)+
	        "&estpvop="+escape(esTPVOP)+
 	        "&moneda="+escape(id("IdMoneda").value)+
 	        "&estadopresupuesto="+escape(id("EstadoPresupuesto").value)+
 	        "&tipoventa="+escape(id("TipoVenta").value)+
 	        "&ventaestado="+escape(id("VentaEstado").value)+
 	        "&productoidioma="+escape(id("ProductoIdioma").value)+
 	        "&modalidadpago="+escape(id("IdModalidadPago").value)+
 	        "&cuentabancaria="+escape(id("CuentaBancaria").value)+
		"&r=" + Math.random();

	if(!esTPV){
		url += "&IdLocal="+id("Local").value;
	        url += "&tipoventaop="+escape(id("TipoVentaOP").value);
	} else {
		url += "&IdLocal="+IdLocalActual;
	        url += "&tipoventaop="+esTPVOP;
	}

	web.setAttribute("src", url) ;
	

}

function Mostrar( idmostrar){
	var xthingie = id("get"+ idmostrar );

	if ( xthingie ){
		xthingie.setAttribute("collapsed","false");
	}
}

function SetActive( val, Categoria ){

        id("CategoriaListado").value = Categoria.toUpperCase()+': ';
	var dinterface = val.split(",");
	id("getDesde").setAttribute("collapsed","true");
	id("getHasta").setAttribute("collapsed","true");
	id("getIdTienda").setAttribute("collapsed","true");
	id("getIdProveedor").setAttribute("collapsed","true");
	id("getIdFamilia").setAttribute("collapsed","true");
	id("getIdSubsidiario").setAttribute("collapsed","true");	
	id("getStatusTrabajo").setAttribute("collapsed","true");
	id("getIdUsuario").setAttribute("collapsed","true");
	id("getReferencia").setAttribute("collapsed","true");
	id("getCB").setAttribute("collapsed","true");
	id("getNumeroSerie").setAttribute("collapsed","true");
	id("getLote").setAttribute("collapsed","true");
	id("getPartida").setAttribute("collapsed","true");
	id("getTipoVentaOP").setAttribute("collapsed","true");
	id("getDNICliente").setAttribute("collapsed","true");
	id("getTipoComprobante").setAttribute("collapsed","true");
	id("getSerieComprobante").setAttribute("collapsed","true");
	id("getEstadoComprobante").setAttribute("collapsed","true");
	id("getEstadoPago").setAttribute("collapsed","true");
	id("getModalidad").setAttribute("collapsed","true");
	id("getEstadoPromo").setAttribute("collapsed","true");
	id("getTipoPromo").setAttribute("collapsed","true");
	id("getTipoOperacion").setAttribute("collapsed","true");
	id("getTipoOpCjaGral").setAttribute("collapsed","true");
	id("getPeriodoVenta").setAttribute("collapsed","true");
	id("getNombreCliente").setAttribute("collapsed","true");
	id("getTipoCliente").setAttribute("collapsed","true");
	id("getIdMarca").setAttribute("collapsed","true");
	id("getCondicionVenta").setAttribute("collapsed","true");
	id("getEstadoOS").setAttribute("collapsed","true");
	id("getPrioridad").setAttribute("collapsed","true");
	id("getFacturacion").setAttribute("collapsed","true");
	id("getEstadoSuscripcion").setAttribute("collapsed","true");
	id("getTipoSuscripcion").setAttribute("collapsed","true");
	id("getTipoPagoSuscripcion").setAttribute("collapsed","true");
	id("getProlongacion").setAttribute("collapsed","true");
	id("getIdCliente").setAttribute("collapsed","true");
	id("getCodigo").setAttribute("collapsed","true");
	id("getEstadoPagoVenta").setAttribute("collapsed","true");
	id("getCobranza").setAttribute("collapsed","true");
	id("getCodigoComprobante").setAttribute("collapsed","true");
	id("getPresupuestoVenta").setAttribute("collapsed","true");
	id("getIdMoneda").setAttribute("collapsed","true");
	id("getEstadoPresupuesto").setAttribute("collapsed","true");
	id("getTipoVenta").setAttribute("collapsed","true");
	id("getVentaEstado").setAttribute("collapsed","true");
	id("getProductoIdioma").setAttribute("collapsed","true");
	id("getModalidadPago").setAttribute("collapsed","true");
	id("getCuentaBancaria").setAttribute("collapsed","true");

	for( t=0;t<dinterface.length;t++){
	        dinterface[t] = dinterface[t].replace(/^\s+/,'').replace(/\s+$/,'');
		Mostrar(dinterface[t]);
	}
	
}

/*
<?php

//echo $code;

?>

*/

//]]></script>
</window>
