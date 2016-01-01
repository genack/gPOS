<?php
  include("../../tool.php");
  $Moneda = getSesionDato("Moneda"); getMonedaJS($Moneda);
  $IdUsuario = getSesionDato("IdUsuario");
  echo "<script> cLocalActivo = ".CleanID(getSesionDato("IdTienda"))."</script>";
?>

<html>
  <head>
   <link rel="stylesheet" type="text/css" href="dashboard.css?v=1.0" media="screen" />
   <script type="text/javascript" src="dashboard.js?v=1.1" ></script>
  </head>
    <body onload="lanzademonio()">

    <div class="bloque-up" id="gpos-dashboard-up">
    <!-- Compras -->
    <div class="xbox compras">
      <table class="box">
	<tbody>
	  <tr colspan ="2" >
	    <td class="title_monto"> COMPRAS </td>
	  </tr>
	  
	  <tr>
	    <td class="txt_monto"> Comprobantes pendientes </td>
	    <td class="unid_monto" id="vComprobantesPendientes"></td>
	  </tr>

          <tr>
	    <td class="txt_monto"> Comprobantes borrador </td>
	    <td class="unid_monto" id="vComprobantesBorrador"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Pedidos borrador </td>
	    <td class="unid_monto" id="vPedidosBorrador"></td>
	  </tr>
	
	  <tr>
	    <td class="txt_monto"> Pedidos pendientes </td>
	    <td class="unid_monto" id="vPedidosPendientes"></td>
	  </tr>

	  <tr>
	    <td></td>
	    <td></td>
	  </tr>

	</tbody>
      </table>
    </div>
	
    <div class="xbox almacen">
      <!-- Almacen -->
      <table class="box">
	<tbody>
	  <tr colspan ="2" >
	    <td class="title_monto"> ALMACEN </td>
	  </tr>

  	  <tr>
	    <td class="txt_monto"> Productos con Stock</td>
	    <td class="unid_monto" id="vProductosTotal"></td>
	  </tr>
	
    	  <tr>
	    <td class="txt_monto"> Productos sin stock </td>
	    <td class="unid_monto" id="vProductosSinStock"></td>
	  </tr>
	
	  <tr>
	    <td class="txt_monto"> Productos Stock Minimo </td>
	    <td class="unid_monto" id="vProductosStockMinimo"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Pronto vencimiento </td>
	    <td class="unid_monto" id="vProntoVencimiento"></td>
	  </tr>

	  <tr>
    	    <td class="txt_monto"> Pedidos por recibir </td>
	    <td class="unid_monto" id="vPedidosPorRecibir"></td>
	  </tr>

	</tbody>
      </table>
    </div>	

     <div class="xbox ventas">
      <!-- Ventas -->
      <table class="box">
	<tbody>
	  <tr colspan ="2" >
	    <td class="title_monto"> PRE VENTAS </td>
	  </tr>
	  
	  <tr>
	    <td class="txt_monto"> Preventas pendientes </td>
	    <td class="unid_monto" id="vPreventasPendientes"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Proformas pendientes </td>
	    <td class="unid_monto" id="vProformaPendientes"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Reservas pendientes </td>
	    <td class="unid_monto" id="vReservasPendientes"></td>
	  </tr>
	
	  <tr>
	    <td class="txt_monto"> Reservas por entregar </td>
	    <td class="unid_monto" id="vReservasEntregar"></td>
	  </tr>

  	  <tr>
	    <td></td>
	    <td></td>
	  </tr>

	</tbody>
       </table>
    </div>	

  
   </div>	
   <div class="bloque-down" id="gpos-dashboard-down">



        <div class="xbox almacen_resumen">	
       <!-- Resumen Almacen -->
       <table class="box">
	<tbody>
	  <tr rowspan="2">
          <td class="title_monto"  >CAPITAL ACTUAL</td>
          <td></td>
	  </tr>
    
          <tr>
	    <td class="txt_monto"> Total Costo</td>
	    <td class="unid_monto" id="vCostoTotal"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Total Margen Utilidad </td>
	    <td class="unid_monto" id="vUtilidadTotal"></td>
	  </tr>

  	  <tr>
	    <td class="txt_monto"> Total Impuesto </td>
	    <td class="unid_monto" id="vImpuestoTotal"></td>
	  </tr>

    	  <tr>
	    <td class="txt_monto"> Total Precio Venta</td>
	    <td class="unid_monto" id="vPrecioTotal"></td>
	  </tr>

  	  <tr>
	    <td class="txt_monto"></td>
	    <td class="unid_monto" ></td>
	  </tr>


    
	</tbody>
      </table>

   </div>


  
      <div class="xbox finanzas">
      <!-- Almacen -->
      <table class="box">
	<tbody>
	  <tr colspan ="2" >
	    <td class="title_monto"> FINANZAS</td>
	  </tr>
	
	  <tr>
	    <td class="txt_monto"> Pagos pendientes </td>
	    <td class="unid_monto" id="vPagosPendientes"></td>
	  </tr>
	  <tr>
	    <td class="txt_monto"> Pagos vencidos </td>
	    <td class="unid_monto" id="vPagosVencidos"></td>
	  </tr>
    
    	  <tr>
	    <td class="txt_monto"> Cobros pendientes </td>
	    <td class="unid_monto" id="vCobrosPendientes"></td>
	  </tr>
	  <tr>
	    <td class="txt_monto"> Cobros vencidos </td>
	    <td class="unid_monto" id="vCobrosVencidos"></td>
	  </tr>

          <tr>
	    <td class="txt_monto"></td>
	    <td class="unid_monto"></td>
	  </tr>


	</tbody>
      </table>
     </div>
    <div class="xbox ventas_resumen">	
       <!-- Resumen Ventas -->
       <table class="box">
	<tbody>
	  <tr colspan="2">
	    <td class="title_monto"> VENTAS </td>
	  </tr>
	  
	  <tr>
	    <td class="txt_monto"> Creditos pendientes </td>
	    <td class="unid_monto" id="vCreditosPendientes"></td>
	  </tr>

	  <tr>
	    <td class="txt_monto"> Servicios pendientes </td>
	    <td class="unid_monto" id="vServiciosPendientes"></td>
	  </tr>

    	  <tr>
	    <td class="txt_monto"> Promociones </td>
	    <td class="unid_monto" id="vPromociones"></td>
	  </tr>

	</tbody>
      </table>

   </div>

    <?php
    if($IdUsuario == 1){
    ?>
    <div class="bloque-btn" >
      <input class="btn" type="button" value="Actualizar DashBoard gPOS" onclick="actualizarDashBoard()"></input>
    </div>
    <?php }?>

  </body>
</html>
