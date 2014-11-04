<?php
include("../tool.php");
header("Content-Type: text/javascript; charset=UTF-8");

$txtMoDet   = getModeloDetalle2txt();
$txtModelo  = $txtMoDet[1];
$txtDetalle = $txtMoDet[2];

$cadenas = array(	
        "po_numtic"=> _("Código :"),
	"po_unid"=>_("Unid."),
	"po_precio"=>_("Precio"),
	"po_costo"=>_("Costo"),
	"po_descuento"=>_("Desc."),
	"po_Total"=>_("Total"),
	"po_TOTAL"=>_("TOTAL:"),
	"po_Entregado"=>_("Entregado:"),
	"po_Cambio"=>_("Cambio:"),
	"po_desgloseiva"=>_("Desglose de IGV:"),
	"po_leatendio"=>_("Le atendió:"),
	"po_ticketarreglointerno"=>_("Ticket arreglo interno"),
	"po_ticketcesionprenda"=>_("Ticket cesión de prenda"),
	"po_ticketdevolucionprenda"=>_("Ticket devolución de prenda"),	
	"po_ticketnoserver"=>_("El servidor no ha podido autorizar la impresión de este ticket. Inténtelo mas tarde"),
	"po_txtTicketVenta"=>_("Comprobante de venta"),
	"po_txtTicketCesion"=>_("Ticket cesión"),
	"po_txtTicketDevolucion"=>_("Ticket devolución"),
	"po_txtTicketPedido"=>_("Presupuestos"),
	"po_txtTicketMProducto"=>_("Meta Productos"),
	"po_txtTicketServicioInterno"=>_("Ticket servicio"),
	"po_imprimircopia"=>_("Impr. copia"),
	"po_cerrar"=>_("Cerrar"),		
	"po_servidorocupado"=>_("Servidor ocupado, inténtelo más tarde"),
	"po_nopuedeseliminarcontado"=>_("¡No puedes eliminar el cliente contado!"),
	"po_seguroborrarcliente"=>_("¿Quieren borrar este cliente?"),
	"po_clienteeliminado"=>_("Cliente eliminado del sistema"),
	"po_noseborra"=>_("No se puede borrar ese cliente"),
	"po_nuevocreado "=>_("Nuevo cliente creado"),
	"po_clientemodificado"=>_("Cliente modificado"),
	"po_operacionincompleta"=>_("Operacion con cliente incompleta, inténtelo mas tarde"),
	"po_mensajeenviado"=>_("Mensaje enviado"),
	"po_modopago"=>_("Modo de pago:"),
	"po_nombreclientecontado"=>_("Cliente Contado"),
	"po_ticketcliente"=>_("Cliente:"),
	"po_Elige" =>_("Elije..."),	
	"po_15diaslimite"=>_("No se admiten devoluciones.\\nCambios dentro de las 24 horas."),
	"po_cuentascopias"=>_("¿Cuántas copias del código de barras necesita imprimir?"),
	"po_cuantasunidadesquiere"=>_("¿Cuántas unidades del producto requiere?"),
	"po_cuantasunidades"=>_("¿Cuántas unidades?"),		
	"po_faltadefcolor"=>_("Falta definir Modelo"),
	"po_faltadeftalla"=>_("Falta definir Detalles"),
	"po_faltadefcb"=>_("Falta definir el CB"),
	"po_errorrepcod"=>_("Código de barras repetido"),			
	"po_tallacolrep"=>_("Detalle o Modelo repetidos"),
	"po_unidadescompra"=>_("Debe especificar unidades de compra"),
	"po_modnombreprod"=>_("Debe modificar el nombre del producto"),
	"po_especificarref"=>_("Debe especificar una referencia"),
	"po_especifiprecioventa"=>_("Debe especificar un precio de venta"),
	"po_especificoste"=>_("Debe especificar un coste"),
	"po_nuevoproducto"=>_("Nuevo producto"),
	"po_nohayproductos"=>_("No hay productos"),
	"po_sehandadodealtacodigos"=>_("Se han dado de alta %d códigos"),
	"po_segurocancelar"=>_("¿Esta seguro que quiere cancelar?"),
	"po_imprimircodigos"=>_("Imprimir CB"),
	"po_borrar"=>_("Eliminar"),
	"po_avisoborrar"=>_("¿Desea eliminar?"),
	"po_nombre"=>_("Nombre"),
	"po_talla"=>$txtDetalle,
	"po_color"=>$txtModelo,
	"po_unidades"=>_("Unid."),
	"po_local"=>_("Local"),
	"po_almacen"=>_("Almacén"),
	"po_nombrecorto"=>_("Nombre de cliente demasiado corto"),
	"po_quierecerrar"=>_("Seguro que quiere proceder al 'CIERRE DE CAJA'?"),
	"po_quiereabrir"=>_("Seguro que quiere proceder a 'ABRIR CAJA'?"),
	"po_sugerenciarecibida"=>_("Sugerencia recibida"),
	"po_incidenciaanotada"=>_("Incidencia anotada"),
	"po_notaenviada"=>_("Nota enviada"),
	"po_confirmatraslado"=>_("¿Esta seguro?"),
	"po_destino"=>_("Destino:"),
	"po_mododepago"=>_("Modo de pago"),
	"po_cuantascopias" => _("¿Cuantas copias?"),
	"po_moviendoa"=>_("Moviendo mercancía a: "),
	"po_importereal"=>_("Importe real de la caja:"),
);

foreach ($cadenas as $variable=>$valor){	
	if ($variable)
		echo "var $variable='" . addslashes($valor). "';\n";  	
}
	
?>

var po_error = po_servidorocupado;

var po_pagmas = ">>";
var po_pagmenos = "<<";


