CREATE FUNCTION obtenerStockProducto(idprod int, idlocal int, idped int) 
  RETURNS int 
DETERMINISTIC 
READS SQL DATA 
BEGIN
  DECLARE Cantidad int;
  SET Cantidad = (SELECT SUM(CantidadMovimiento) AS Unidad FROM ges_kardex
  WHERE IdProducto = idprod
  AND IdLocal = idlocal 
  AND IdPedidoDet = idped
  AND Eliminado = 0 );
  RETURN Cantidad; 
END;

;;;;;;

create trigger checkStatusSeriesPedidos after insert on ges_productos_series 
FOR EACH ROW 
begin
  declare ns int;
  declare np int;
  declare res int;
  declare esSerie INT;
  SELECT Serie INTO esSerie FROM ges_pedidosdet WHERE IdProducto = New.IdProducto AND IdPedidoDet = NEW.DocumentoEntrada;
  IF (esSerie <> 0) THEN
    set np = (select Unidades from ges_pedidosdet where IdPedidoDet = new.DocumentoEntrada AND IdProducto = new.IdProducto);
    set ns = (select count(*) from ges_productos_series where DocumentoEntrada = new.DocumentoEntrada AND IdProducto = new.IdProducto AND ges_productos_series.Eliminado = 0);
    IF (ns=np) THEN
      SET res = 1;
    ELSEIF (ns<>np) THEN
      SET res = 2;
    END IF;
    UPDATE ges_pedidosdet SET Serie = res WHERE IdPedidoDet = NEW.DocumentoEntrada AND IdProducto = NEW.IdProducto; 
  END IF;
END;

;;;;;;

CREATE TRIGGER checkEstadoKardex BEFORE INSERT ON ges_kardex 
FOR EACH ROW 
BEGIN 
  DECLARE ns INT; 
  DECLARE np INT; 
  DECLARE esSerie INT; 
  DECLARE IdComp BIGINT; 
  DECLARE Stock INT; 
  IF(NEW.TipoMovimiento = 'Entrada') THEN 
    SELECT Serie INTO esSerie FROM ges_pedidosdet WHERE IdProducto = NEW.IdProducto AND IdPedidoDet = NEW.IdPedidoDet; 
    IF(NEW.CantidadMovimiento = 0 AND NEW.IdInventario = 0) THEN 
       SET NEW.Estado = 4; 
       SET NEW.EstadoDetalle = 'Entrada Cantidad igual a 0'; 
    END IF; 
  ELSEIF(NEW.TipoMovimiento = 'Salida') THEN 
    SELECT Serie INTO esSerie FROM ges_comprobantesdet WHERE IdProducto = NEW.IdProducto AND IdPedidoDet = NEW.IdPedidoDet AND IdComprobanteDet = NEW.IdComprobanteDet; 
    SELECT obtenerStockProducto(NEW.IdProducto , NEW.IdLocal, NEW.IdPedidoDet) INTO Stock; 
    IF((NEW.CantidadMovimiento)*(-1) > Stock) THEN 
       SET NEW.Estado = 1; 
       SET NEW.EstadoDetalle = 'Salida Stock menor que la cantidad';
    END IF; 
    IF((Stock + NEW.CantidadMovimiento) < 0) THEN 
       SET NEW.Estado = 2; 
       SET NEW.EstadoDetalle = 'Salida Stock negativo'; 
    END IF; 
    IF(Stock = 0) THEN 
       SET NEW.Estado = 3; 
       SET NEW.EstadoDetalle = 'Salida Stock cero'; 
    END IF; 
  END IF; 
  IF (esSerie = 1) THEN 
    IF (NEW.TipoMovimiento = 'Entrada')  THEN 
      SET np = (SELECT Unidades FROM ges_pedidosdet WHERE ges_pedidosdet.IdPedidoDet = NEW.IdPedidoDet AND ges_pedidosdet.IdProducto = NEW.IdProducto); 
      SET ns = (SELECT COUNT(*) FROM ges_productos_series WHERE ges_productos_series.DocumentoEntrada = NEW.IdPedidoDet AND ges_productos_series.IdProducto = NEW.IdProducto AND ges_productos_series.Eliminado = 0); 
      IF(ns <> np) THEN 
      	SET NEW.Estado = 5; 
        SET NEW.EstadoDetalle = 'Entrada Cantidad y números de series diferentes'; 
      END IF; 
    ELSEIF(NEW.TipoMovimiento = 'Salida') THEN 
      SELECT IdComprobante INTO IdComp FROM ges_comprobantesdet WHERE ges_comprobantesdet.IdComprobanteDet = NEW.IdComprobanteDet; 
      SELECT Cantidad INTO np FROM ges_comprobantesdet WHERE ges_comprobantesdet.IdComprobanteDet = NEW.IdComprobanteDet; 
      SELECT COUNT(*) INTO ns FROM ges_productos_series WHERE ges_productos_series.IdProducto = NEW.IdProducto AND ges_productos_series.DocumentoSalida = IdComp AND Eliminado = 0 AND ges_productos_series.DocumentoEntrada = NEW.IdPedidoDet; 
      IF(ns <> np) THEN 
      	SET NEW.Estado = 6; 
        SET NEW.EstadoDetalle = 'Salida Cantidad y números de series diferentes'; 
      END IF; 
    END IF; 
  END IF; 
  CALL actualizar_kardex_costo (NEW.CostoTotalMovimiento,NEW.IdLocal,NEW.TipoMovimiento); 
END; 

;;;;;;

CREATE TRIGGER actualizaHistorialCliente_np AFTER INSERT ON ges_historialventaperiodo 
FOR EACH ROW 
BEGIN 
  DECLARE Periodo INT;
  DECLARE Monto DOUBLE;
  DECLARE Cantidad INT;
  DECLARE IdPeriodo INT;
  DECLARE Next INTEGER;
  DECLARE current_id integer;
  DECLARE tag_id integer;
  DECLARE tag_field varchar(255);
  DECLARE next_sep integer;
  DECLARE current_tag varchar(255);
  DECLARE right_tag varchar(255);

  SELECT GROUP_CONCAT(ges_clientes.IdCliente) INTO tag_field FROM ges_clientes where ges_clientes.Eliminado = 0 GROUP BY ges_clientes.Eliminado;
   IF (CHAR_LENGTH(tag_field) <> 0) THEN
      SET Next = 1; 
      WHILE (Next = 1) DO 
          SELECT INSTR(tag_field, ',') INTO next_sep;
          IF (next_sep > 0) THEN
            SELECT SUBSTR(tag_field, 1, next_sep - 1) INTO current_tag;
            SELECT SUBSTR(tag_field, next_sep + 1, CHAR_LENGTH(tag_field)) INTO right_tag;
            set tag_field = right_tag;
         ELSE
           set next = 0;
           set current_tag = tag_field;
         END IF;
         SET Periodo = NEW.Periodo;
         SET IdPeriodo = NEW.IdHistorialVentaPeriodo;
         IF(Periodo <> 0) THEN 
            SET Monto = obtenerMontoCompraCliente(Periodo, current_tag); 
            SET Cantidad = obtenerCantidadCompraCliente(Periodo, current_tag); 
            INSERT INTO ges_historialventas (IdCliente, IdHistorialVentaPeriodo, MontoCompra, NumeroCompra) 	 values  (current_tag,IdPeriodo,Monto, Cantidad);
          END IF; 
       END WHILE;
     END IF; 
END; 

;;;;;;

CREATE TRIGGER actualizaHistorialCliente_np_update BEFORE UPDATE ON ges_historialventaperiodo 
FOR EACH ROW 
BEGIN 
  DECLARE Periodo INT;
  DECLARE Monto DOUBLE;
  DECLARE Cantidad INT;
  DECLARE IdPeriodo INT;
  DECLARE Next INTEGER;
  DECLARE current_id integer;
  DECLARE tag_id integer;
  DECLARE tag_field varchar(255);
  DECLARE next_sep integer;
  DECLARE current_tag varchar(255);
  DECLARE right_tag varchar(255);

  SELECT GROUP_CONCAT(ges_clientes.IdCliente) INTO tag_field FROM ges_clientes where ges_clientes.Eliminado = 0 GROUP BY ges_clientes.Eliminado;
   IF (CHAR_LENGTH(tag_field) <> 0) THEN
      SET Next = 1; 
      WHILE (Next = 1) DO 
          SELECT INSTR(tag_field, ',') INTO next_sep;
          IF (next_sep > 0) THEN
            SELECT SUBSTR(tag_field, 1, next_sep - 1) INTO current_tag;
            SELECT SUBSTR(tag_field, next_sep + 1, CHAR_LENGTH(tag_field)) INTO right_tag;
            set tag_field = right_tag;
         ELSE
           set next = 0;
           set current_tag = tag_field;
         END IF;
         SET Periodo = NEW.Periodo;
         SET IdPeriodo = OLD.IdHistorialVentaPeriodo;
         IF(Periodo <> 0) THEN 
            SET Monto = obtenerMontoCompraCliente(Periodo, current_tag); 
            SET Cantidad = obtenerCantidadCompraCliente(Periodo,current_tag); 
            UPDATE ges_historialventas SET NumeroCompra = Cantidad, MontoCompra = Monto WHERE ges_historialventas.IdCliente = current_tag AND IdHistorialVentaPeriodo = IdPeriodo; 
          END IF; 
       END WHILE;
     END IF; 
END; 

;;;;;;

create function obtenerMontoCompraCliente(periodo int, idcliente bigint) 
  returns double 
DETERMINISTIC 
READS SQL DATA 
begin
  DECLARE Monto DOUBLE;
  SELECT SUM(TotalImporte) INTO Monto FROM ges_comprobantes 
  INNER JOIN ges_comprobantesnum ON ges_comprobantes.IdComprobante = ges_comprobantesnum.IdComprobante 
  INNER JOIN ges_comprobantestipo ON ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante
  WHERE ges_comprobantes.Eliminado = 0 
  AND ges_comprobantes.IdCliente = idcliente 
  AND ges_comprobantesnum.Status IN ('Emitido','Facturado') 
  AND ges_comprobantes.FechaComprobante BETWEEN DATE_SUB(CURDATE(), INTERVAL periodo*30 DAY) AND CURDATE() 
  AND ges_comprobantestipo.TipoComprobante IN ('Factura','Boleta','Ticket');
  return Monto;
end;

;;;;;;

create function obtenerCantidadCompraCliente(periodo int,idcliente bigint) 
  RETURNS INT 
DETERMINISTIC 
READS SQL DATA 
begin
  DECLARE Cantidad INT;
  SELECT COUNT(ges_comprobantes.IdComprobante) INTO Cantidad FROM ges_comprobantes 
  INNER JOIN ges_comprobantesnum ON ges_comprobantes.IdComprobante = ges_comprobantesnum.IdComprobante 
  INNER JOIN ges_comprobantestipo ON ges_comprobantesnum.IdTipoComprobante = ges_comprobantestipo.IdTipoComprobante
  WHERE ges_comprobantes.Eliminado = 0 
  AND ges_comprobantes.IdCliente = idcliente 
  AND ges_comprobantesnum.Status IN ('Emitido','Facturado') 
  AND ges_comprobantes.FechaComprobante BETWEEN DATE_SUB(CURDATE(), INTERVAL periodo*30 DAY) AND CURDATE() 
  AND ges_comprobantestipo.TipoComprobante IN ('Factura','Boleta','Ticket');
  return Cantidad;
end;

;;;;;;

CREATE FUNCTION obtenerSyncTPV(xkeysinc char(32)) 
  RETURNS tinytext 
DETERMINISTIC 
READS SQL DATA 
BEGIN 
  DECLARE SyncUser tinytext; 
  SELECT CONCAT(Preventa,'~',Proforma,'~',ProformaOnline,'~',Stock,'~',Cliente,'~',Promocion,'~',Mensaje,'~',Caja,'~',MetaProducto) INTO SyncUser 
  FROM ges_synctpv 
  WHERE ges_synctpv.KeySync = xkeysinc AND ges_synctpv.Eliminado=0; 
  RETURN SyncUser; 
END; 

;;;;;;

create trigger actualizar_presupuestos_in after insert on ges_presupuestos 
FOR EACH ROW 
begin 
  IF(NEW.TipoPresupuesto = 'Preventa') THEN 
    UPDATE ges_synctpv SET ges_synctpv.Preventa = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  ELSEIF(NEW.TipoPresupuesto = 'Proforma') THEN 
    UPDATE ges_synctpv SET ges_synctpv.Proforma = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  ELSE  
    UPDATE ges_synctpv SET ges_synctpv.ProformaOnline = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  END IF; 
  CALL actualizar_presupuestosventas_in (NEW.TipoPresupuesto,NEW.IdLocal,NEW.TotalImporte); 
END; 

;;;;;;

create trigger actualizar_presupuestos_up after update on ges_presupuestos 
FOR EACH ROW 
begin 
  IF(NEW.TipoPresupuesto = 'Preventa') THEN 
    UPDATE ges_synctpv SET ges_synctpv.Preventa = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal;
  ELSEIF(NEW.TipoPresupuesto = 'Proforma') THEN 
    UPDATE ges_synctpv SET ges_synctpv.Proforma = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  ELSE  
    UPDATE ges_synctpv SET ges_synctpv.ProformaOnline = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  END IF; 
  CALL actualizar_presupuestosventas_up (NEW.TipoPresupuesto,NEW.Status,OLD.TotalImporte,NEW.IdLocal,OLD.Status,OLD.TipoPresupuesto); 
END; 

;;;;;;

create trigger actualizar_stock_in after insert on ges_almacenes 
FOR EACH ROW 
begin 
  DECLARE xservicio INT; 
  UPDATE ges_synctpv SET ges_synctpv.Stock = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal;  
  SELECT Servicio INTO xservicio FROM ges_productos WHERE ges_productos.IdProducto = NEW.IdProducto; 
  IF(xservicio = 0) THEN 
    CALL actualizar_almacenes_in (NEW.Unidades, NEW.IdLocal,NEW.StockMin); 
  END IF; 
END;

;;;;;;

create trigger actualizar_stock_up after update on ges_almacenes 
FOR EACH ROW 
begin 
  DECLARE xservicio INT; 
  UPDATE ges_synctpv SET ges_synctpv.Stock = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal;  
  SELECT Servicio INTO xservicio FROM ges_productos WHERE ges_productos.IdProducto = NEW.IdProducto; 
  IF(xservicio = 0) THEN 
    CALL actualizar_almacenes_up (OLD.StockMin,NEW.Unidades, OLD.Unidades, NEW.IdLocal,NEW.StockMin,NEW.PrecioVenta,OLD.PrecioVenta);
  END IF;
END; 

;;;;;;

create trigger actualizar_cliente_in after insert on ges_clientes 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Cliente = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger actualizar_cliente_up after update on ges_clientes 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Cliente = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger actualizar_promocion_in after insert on ges_promociones 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Promocion = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger actualizar_promocion_up after update on ges_promociones 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Promocion = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  CALL actualizar_promociones (NEW.Estado,OLD.Estado,NEW.IdLocal);
END; 

;;;;;;

create trigger actualizar_mensaje_in after insert on ges_mensajes 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.mensaje = 1 WHERE ges_synctpv.IdLocal = NEW.IdOrigenLocal; 
END; 

;;;;;;

create trigger actualizar_caja_in after insert on ges_arqueo_caja 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Caja = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END;

;;;;;;

create trigger actualizar_caja_up after update on ges_arqueo_caja 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.Caja = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger actualizar_metaproducto_in after insert on ges_metaproductos 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.MetaProducto = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger actualizar_metaproducto_up after update on ges_metaproductos 
FOR EACH ROW 
begin 
  UPDATE ges_synctpv SET ges_synctpv.MetaProducto = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
END; 

;;;;;;

create trigger crear_synctpv_usuario_in after insert on ges_usuarios 
FOR EACH ROW 
begin 
  DECLARE xiduser INT; 
  SELECT IdUsuario INTO xiduser FROM ges_synctpv WHERE ges_synctpv.IdUsuario = NEW.IdUsuario AND ges_synctpv.Eliminado = 0; 
  IF (xiduser IS NULL) THEN 
    INSERT INTO ges_synctpv (ges_synctpv.IdLocal,ges_synctpv.IdUsuario) values (NEW.IdLocal,NEW.IdUsuario); 
  END IF; 
END; 

;;;;;;

create trigger actualizar_movimientocaja_up after update on ges_dinero_movimientos 
FOR EACH ROW 
begin 
  IF(NEW.IdModalidadPago = 1) THEN 
    UPDATE ges_synctpv SET ges_synctpv.Caja = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  END IF; 
END; 

;;;;;;

create trigger actualizar_movimientocaja_in after insert on ges_dinero_movimientos 
FOR EACH ROW 
begin 
  IF(NEW.IdModalidadPago = 1) THEN 
    UPDATE ges_synctpv SET ges_synctpv.Caja = 1 WHERE ges_synctpv.IdLocal = NEW.IdLocal; 
  END IF; 
END; 

;;;;;;

create trigger actualizar_pendpedidosborrador_in after insert on ges_ordencompra 
FOR EACH ROW 
begin 
  IF(NEW.Estado = 'Borrador') THEN 
    UPDATE ges_dashboard SET c_PedidosBorrador = c_PedidosBorrador+1 WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_pendpedidosborrador_up after update on ges_ordencompra 
FOR EACH ROW 
begin 
  IF(OLD.Estado = 'Borrador' AND NEW.Estado = 'Pendiente') THEN 
    UPDATE ges_dashboard SET c_PedidosBorrador = c_PedidosBorrador-1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET c_PedidosPendiente = c_PedidosPendiente+1 WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(OLD.Estado = 'Pendiente' AND NEW.Estado <> 'Pendiente') THEN 
    UPDATE ges_dashboard SET c_PedidosPendiente = c_PedidosPendiente-1 WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create procedure actualizar_almacenes_up (OLDStockMin INT,NEWUnidades INT, OLDUnidades INT, NEWIdLocal INT,NEWStockMin INT,NEWPrecioVenta DOUBLE,OLDPrecioVenta DOUBLE) 

begin 
  IF(NEWStockMin <> OLDStockMin) THEN 
    IF(NEWStockMin >= NEWUnidades AND OLDStockMin < NEWUnidades) THEN 
      UPDATE ges_dashboard SET a_StockMinimo = a_StockMinimo+1 WHERE IdLocal = NEWIdLocal;
    END IF; 
    IF(NEWStockMin < NEWUnidades AND OLDStockMin > NEWUnidades) THEN 
      UPDATE ges_dashboard SET a_StockMinimo = a_StockMinimo-1 WHERE IdLocal = NEWIdLocal;
    END IF; 
  END IF; 
  IF(NEWUnidades <> OLDUnidades AND OLDStockMin > 0) THEN 
    IF(NEWUnidades <= NEWStockMin AND OLDUnidades > NEWStockMin) THEN 
      UPDATE ges_dashboard SET a_StockMinimo = a_StockMinimo+1 WHERE IdLocal = NEWIdLocal;
    END IF; 
    IF(NEWUnidades > NEWStockMin  AND OLDUnidades < NEWStockMin) THEN 
      UPDATE ges_dashboard SET a_StockMinimo = a_StockMinimo-1 WHERE IdLocal = NEWIdLocal;
    END IF; 
  END IF; 
  IF(NEWUnidades = 0 AND OLDUnidades <> 0) THEN 
    UPDATE ges_dashboard SET a_ProductosSinStock = a_ProductosSinStock+1 WHERE IdLocal = NEWIdLocal;
  END IF; 
  IF(NEWUnidades > 0 AND OLDUnidades = 0) THEN 
    UPDATE ges_dashboard SET a_ProductosSinStock = a_ProductosSinStock-1 WHERE IdLocal = NEWIdLocal;
  END IF; 
  IF((OLDUnidades <> NEWUnidades) OR (NEWPrecioVenta <> OLDPrecioVenta)) THEN 
    UPDATE ges_dashboard SET v_PrecioTotal = v_PrecioTotal + (NEWUnidades*NEWPrecioVenta - OLDUnidades*NEWPrecioVenta); 
  END IF; 
END; 

;;;;;;

create procedure actualizar_almacenes_in (NEWUnidades INT, NEWIdLocal INT,NEWStockMin INT) 

begin 
    UPDATE ges_dashboard SET a_ProductosSinStock = a_ProductosSinStock+1  WHERE IdLocal = NEWIdLocal; 
END; 

;;;;;;

create trigger actualizar_ordenservicio_in after insert on ges_ordenservicio 
FOR EACH ROW 
begin 
  IF(NEW.Estado = 'Pendiente') THEN 
    UPDATE ges_dashboard SET v_PendServicio = v_PendServicio+1 WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_ordenservicio_up after update on ges_ordenservicio 
FOR EACH ROW 
begin 
  DECLARE n_importe double;
  DECLARE o_importe double;
  SET n_importe = NEW.Importe;
  SET o_importe = OLD.Importe;

  IF(OLD.Estado = 'Cancelado' AND NEW.Estado = 'Pendiente') THEN 
    UPDATE ges_dashboard SET v_PendServicio = v_PendServicio+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET v_PendServicioMonto = v_PendServicioMonto+o_importe  WHERE IdLocal = NEW.IdLocal; 
  END IF; 
  IF(OLD.Estado = 'Pendiente' AND NEW.Estado = 'Ejecucion') THEN 
    UPDATE ges_dashboard SET v_PendServicio = v_PendServicio-1 WHERE IdLocal = NEW.IdLocal; 
    UPDATE ges_dashboard SET v_PendServicioMonto = v_PendServicioMonto-o_importe  WHERE IdLocal = NEW.IdLocal; 
  END IF; 
  IF(OLD.Estado = 'Pendiente' AND NEW.Estado = 'Cancelado') THEN 
    UPDATE ges_dashboard SET v_PendServicio = v_PendServicio-1 WHERE IdLocal = NEW.IdLocal; 
    UPDATE ges_dashboard SET v_PendServicioMonto = v_PendServicioMonto-o_importe  WHERE IdLocal = NEW.IdLocal; 
  END IF; 
  IF(NEW.Estado = 'Pendiente' OR NEW.Estado = 'Ejecucion') THEN 
    UPDATE ges_dashboard SET v_PendServicioMonto = v_PendServicioMonto+n_importe-o_importe  WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_cmbteventas_in after insert on ges_comprobantes 
FOR EACH ROW 
begin 
  DECLARE pendiente double;
  DECLARE importe double;
  DECLARE xcred char(2);
  SET pendiente = NEW.ImportePendiente;
  SET importe = NEW.TotalImporte;
  SELECT SUBSTR(NEW.SerieComprobante,1,2) INTO xcred;

  IF(NEW.Reservado = 1 AND pendiente <> 0) THEN 
    UPDATE ges_dashboard SET v_PendReservas = v_PendReservas+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET v_PendReservasMonto = v_PendReservasMonto+pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(NEW.Reservado = 1 AND pendiente = 0) THEN 
    UPDATE ges_dashboard SET v_ReservasEntregar = v_ReservasEntregar+1 WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(NEW.Status = 1 AND pendiente <> 0 AND xcred = 'CS') THEN 
    UPDATE ges_dashboard SET v_PendCreditos = v_PendCreditos+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET v_PendCreditosMonto = v_PendCreditosMonto+pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(pendiente > 0) THEN 
    UPDATE ges_dashboard SET f_PendPorCobrar = f_PendPorCobrar+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto+pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_cmbteventas_up after update on ges_comprobantes 
FOR EACH ROW 
begin 
  DECLARE n_pendiente double;
  DECLARE o_pendiente double;
  DECLARE xcred char(2);
  SELECT SUBSTR(NEW.SerieComprobante,1,2) INTO xcred;
  SET n_pendiente = NEW.ImportePendiente;
  SET o_pendiente = OLD.ImportePendiente;

  IF(NEW.Reservado = 1) THEN 
    IF(o_pendiente <> 0 AND n_pendiente = 0) THEN 
      UPDATE ges_dashboard SET v_PendReservas = v_PendReservas-1 WHERE IdLocal = NEW.IdLocal;
      UPDATE ges_dashboard SET v_PendReservasMonto = v_PendReservasMonto-o_pendiente WHERE IdLocal = NEW.IdLocal;
    END IF; 
    IF(o_pendiente <> 0 AND n_pendiente <> 0) THEN 
      UPDATE ges_dashboard SET v_PendReservasMonto = v_PendReservasMonto-o_pendiente+n_pendiente WHERE IdLocal = NEW.IdLocal;
    END IF; 
    IF(o_pendiente = 0 AND n_pendiente <> 0) THEN 
      UPDATE ges_dashboard SET v_PendReservas = v_PendReservas+1 WHERE IdLocal = NEW.IdLocal;
      UPDATE ges_dashboard SET v_PendReservasMonto = v_PendReservasMonto+n_pendiente WHERE IdLocal = NEW.IdLocal;
    END IF; 
    IF(NEW.FechaEntregaReserva <> OLD.FechaEntregaReserva AND n_pendiente = 0 AND o_pendiente = 0) THEN 
      UPDATE ges_dashboard SET v_ReservasEntregar = v_ReservasEntregar-1 WHERE IdLocal = NEW.IdLocal;
    END IF; 
    IF(NEW.FechaEntregaReserva = '0000-00-00 00:00:00' AND n_pendiente = 0 AND o_pendiente <> 0) THEN 
      UPDATE ges_dashboard SET v_ReservasEntregar = v_ReservasEntregar+1 WHERE IdLocal = NEW.IdLocal;
    END IF; 
  END IF;
  IF(xcred = 'CS') THEN 
    IF(n_pendiente = 0 AND o_pendiente <> 0) THEN 
      UPDATE ges_dashboard SET v_PendCreditos = v_PendCreditos-1 WHERE IdLocal = NEW.IdLocal;
      UPDATE ges_dashboard SET v_PendCreditosMonto = v_PendCreditosMonto-o_pendiente WHERE IdLocal = NEW.IdLocal;
    END IF; 
    IF(n_pendiente <> o_pendiente AND n_pendiente > 0 AND o_pendiente > 0) THEN 
      UPDATE ges_dashboard SET v_PendCreditosMonto = v_PendCreditosMonto-o_pendiente+n_pendiente WHERE IdLocal = NEW.IdLocal; 
    END IF; 
    IF(o_pendiente = 0 AND n_pendiente <> 0) THEN 
      UPDATE ges_dashboard SET v_PendCreditos = v_PendCreditos+1 WHERE IdLocal = NEW.IdLocal;
      UPDATE ges_dashboard SET v_PendCreditosMonto = v_PendCreditosMonto+n_pendiente WHERE IdLocal = NEW.IdLocal;
    END IF; 
  END IF;
  IF(o_pendiente <> 0 AND n_pendiente = 0 ) THEN 
    UPDATE ges_dashboard SET f_PendPorCobrar = f_PendPorCobrar-1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto-o_pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(o_pendiente > n_pendiente AND n_pendiente <> 0 ) THEN 
    UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto-o_pendiente+n_pendiente WHERE IdLocal = NEW.IdLocal; 
  END IF; 
  IF(o_pendiente < n_pendiente AND o_pendiente = 0 ) THEN 
    UPDATE ges_dashboard SET f_PendPorCobrar = f_PendPorCobrar+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto+n_pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(o_pendiente < n_pendiente AND o_pendiente <> 0 ) THEN 
    UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto-o_pendiente+n_pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(NEW.Cobranza = 'Coactivo' AND OLD.Cobranza <> 'Coactivo') THEN 
    UPDATE ges_dashboard SET f_VencPorCobrar = f_VencPorCobrar+1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET f_VencPorCobrarMonto = f_VencPorCobrarMonto+o_pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
  IF(NEW.Cobranza = 'Coactivo' AND n_pendiente = 0 AND o_pendiente <> 0) THEN 
    UPDATE ges_dashboard SET f_VencPorCobrar = f_VencPorCobrar-1 WHERE IdLocal = NEW.IdLocal;
    UPDATE ges_dashboard SET f_VencPorCobrarMonto = f_VencPorCobrarMonto-o_pendiente WHERE IdLocal = NEW.IdLocal;
  END IF; 
END; 

;;;;;;

create procedure actualizar_presupuestosventas_in (NEWTipoPresupuesto char(14),NEWIdLocal INT,NEWTotalImporte double) 

begin 
  IF(NEWTipoPresupuesto = 'Preventa') THEN 
    UPDATE ges_dashboard SET v_PendPreventas = v_PendPreventas+1 WHERE IdLocal = NEWIdLocal;
    UPDATE ges_dashboard SET v_PendPreventasMonto = v_PendPreventasMonto+NEWTotalImporte WHERE IdLocal = NEWIdLocal;
  END IF; 
  IF(NEWTipoPresupuesto = 'Proforma') THEN 
    UPDATE ges_dashboard SET v_PendProformas = v_PendProformas+1 WHERE IdLocal = NEWIdLocal;
    UPDATE ges_dashboard SET v_PendProformasMonto = v_PendProformasMonto+NEWTotalImporte WHERE IdLocal = NEWIdLocal;
  END IF; 
END; 

;;;;;;

create procedure actualizar_presupuestosventas_up (NEWTipoPresupuesto char(14),NEWStatus char(10),OLDTotalImporte double,NEWIdLocal int,OLDStatus char(10),OLDTipoPresupuesto char(14)) 

begin 
  IF(NEWTipoPresupuesto = 'Preventa' AND OLDTipoPresupuesto = 'Preventa') THEN
    IF(NEWStatus <> 'Pendiente' AND OLDStatus = 'Pendiente') THEN 
      UPDATE ges_dashboard SET v_PendPreventas = v_PendPreventas-1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendPreventasMonto = v_PendPreventasMonto-OLDTotalImporte WHERE IdLocal = NEWIdLocal;
    END IF;
    IF(NEWStatus = 'Pendiente' AND OLDStatus <> 'Pendiente') THEN
      UPDATE ges_dashboard SET v_PendPreventas = v_PendPreventas+1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendPreventasMonto = v_PendPreventasMonto+OLDTotalImporte WHERE IdLocal = NEWIdLocal;
    END IF;      
  END IF; 
  IF(NEWTipoPresupuesto = 'Proforma' AND OLDTipoPresupuesto = 'Proforma') THEN
    IF(NEWStatus <> 'Pendiente' AND OLDStatus = 'Pendiente') THEN 
      UPDATE ges_dashboard SET v_PendProformas = v_PendProformas-1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendProformasMonto = v_PendProformasMonto-OLDTotalImporte WHERE IdLocal = NEWIdLocal;
    END IF;
    IF(NEWStatus = 'Pendiente' AND OLDStatus <> 'Pendiente') THEN 
      UPDATE ges_dashboard SET v_PendProformas = v_PendProformas+1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendProformasMonto = v_PendProformasMonto+OLDTotalImporte WHERE IdLocal = NEWIdLocal;
    END IF;    
  END IF;
  IF(OLDTipoPresupuesto = 'Preventa' AND NEWTipoPresupuesto = 'Proforma') THEN
      UPDATE ges_dashboard SET v_PendProformas = v_PendProformas+1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendProformasMonto = v_PendProformasMonto+OLDTotalImporte WHERE IdLocal = NEWIdLocal;

      UPDATE ges_dashboard SET v_PendPreventas = v_PendPreventas-1 WHERE IdLocal = NEWIdLocal;
      UPDATE ges_dashboard SET v_PendPreventasMonto = v_PendPreventasMonto-OLDTotalImporte WHERE IdLocal = NEWIdLocal;
  END IF;
END; 

;;;;;;

create trigger actualizar_cmbtecompras_in after insert on ges_comprobantesprov 
FOR EACH ROW 
begin 
  DECLARE xidlocal int;
  DECLARE importe double; 
  SELECT IdLocal INTO xidlocal FROM ges_pedidos WHERE IdPedido = NEW.IdPedido;  
  SET importe = NEW.TotalImporte; 
  IF(NEW.EstadoDocumento = 'Borrador' AND NEW.TipoComprobante <> 'AlbaranInt') THEN 
    UPDATE ges_dashboard SET c_CmbteBorrador = c_CmbteBorrador+1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET a_PedidosPorRecibir = a_PedidosPorRecibir+1 WHERE IdLocal = xidlocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_cmbtecompras_up after update on ges_comprobantesprov 
FOR EACH ROW 
begin 
  DECLARE xidlocal int;
  DECLARE n_importe double; 
  DECLARE n_pendiente double; 
  DECLARE o_importe double; 
  DECLARE o_pendiente double; 
  SET n_importe = NEW.TotalImporte; 
  SET n_pendiente = NEW.ImportePendiente; 
  SET o_importe = OLD.TotalImporte; 
  SET o_pendiente = OLD.ImportePendiente; 
  SELECT IdLocal INTO xidlocal FROM ges_pedidos WHERE IdPedido = NEW.IdPedido; 
  IF(NEW.TipoComprobante <> 'AlbaranInt') THEN 
  IF(OLD.EstadoDocumento = 'Borrador' AND NEW.EstadoDocumento = 'Pendiente') THEN 
    UPDATE ges_dashboard SET c_CmbteBorrador = c_CmbteBorrador-1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET c_CmbtePendiente = c_CmbtePendiente+1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET f_PendPorPagar = f_PendPorPagar+1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET f_PendPorPagarMonto = f_PendPorPagarMonto+n_importe WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET a_PedidosPorRecibir = a_PedidosPorRecibir-1 WHERE IdLocal = xidlocal;
  END IF; 
  IF(OLD.EstadoDocumento = 'Pendiente' AND NEW.EstadoDocumento = 'Confirmado') THEN 
    UPDATE ges_dashboard SET c_CmbtePendiente = c_CmbtePendiente-1 WHERE IdLocal = xidlocal;
  END IF; 
  IF(NEW.EstadoDocumento = 'Cancelada') THEN 
    UPDATE ges_dashboard SET c_CmbteBorrador = c_CmbteBorrador-1 WHERE IdLocal = xidlocal;
  END IF; 
  IF(OLD.EstadoDocumento <> 'Borrador') THEN 
  IF(n_pendiente = 0 ) THEN 
    UPDATE ges_dashboard SET f_PendPorPagar = f_PendPorPagar-1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET f_PendPorPagarMonto = f_PendPorPagarMonto-o_pendiente WHERE IdLocal = xidlocal;
  END IF; 
  IF(n_pendiente <> 0 AND o_pendiente > n_pendiente) THEN 
    UPDATE ges_dashboard SET f_PendPorPagarMonto = f_PendPorPagarMonto-o_pendiente+n_pendiente WHERE IdLocal = xidlocal;
  END IF; 
  IF(n_pendiente <> 0 AND o_pendiente < n_pendiente AND o_pendiente <> 0) THEN 
    UPDATE ges_dashboard SET f_PendPorPagarMonto = f_PendPorPagarMonto-o_pendiente+n_pendiente WHERE IdLocal = xidlocal;
  END IF; 
  IF(n_pendiente <> 0 AND o_pendiente < n_pendiente AND o_pendiente = 0) THEN 
    UPDATE ges_dashboard SET f_PendPorPagar = f_PendPorPagar+1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET f_PendPorPagarMonto = f_PendPorPagarMonto+n_pendiente WHERE IdLocal = xidlocal;
  END IF; 
  IF((OLD.EstadoPago = 'Pendiente' OR OLD.EstadoPago = 'Empezada') AND NEW.EstadoPago = 'Vencida') THEN 
    UPDATE ges_dashboard SET f_VencPorPagar = f_VencPorPagar+1 WHERE IdLocal = xidlocal; 
    UPDATE ges_dashboard SET f_VencPorPagarMonto = f_VencPorPagarMonto+n_pendiente WHERE IdLocal = xidlocal; 
  END IF; 
  IF(OLD.EstadoPago = 'Vencida' AND NEW.EstadoPago <> 'Vencida') THEN 
    UPDATE ges_dashboard SET f_VencPorPagar = f_VencPorPagar-1 WHERE IdLocal = xidlocal;
    UPDATE ges_dashboard SET f_VencPorPagarMonto = f_VencPorPagarMonto-o_pendiente WHERE IdLocal = xidlocal; 
  END IF; 
  END IF; 
  END IF; 
END; 

;;;;;;

create trigger actualizar_productos_in after insert on ges_productos  
FOR EACH ROW 
begin 
  IF(NEW.Servicio = 0) THEN 
    UPDATE ges_dashboard SET p_Productos = p_Productos+1;
  END IF; 
  IF(NEW.Servicio = 1) THEN 
    UPDATE ges_dashboard SET p_Servicios = p_Servicios+1;
  END IF; 
END; 

;;;;;;

create trigger actualizar_productos_up after update on ges_productos  
FOR EACH ROW 
begin 
  IF(NEW.Servicio = 0 AND OLD.Servicio = 1) THEN 
    UPDATE ges_dashboard SET p_Servicios = p_Servicios-1;
    UPDATE ges_dashboard SET p_Productos = p_Productos+1;
  END IF; 
  IF(NEW.Servicio = 1 AND OLD.Servicio = 0 ) THEN 
    UPDATE ges_dashboard SET p_Servicios = p_Servicios+1;
    UPDATE ges_dashboard SET p_Productos = p_Productos-1;
  END IF; 
  IF(NEW.Servicio = 0) THEN 
    IF(NEW.Eliminado = 1 AND OLD.Eliminado = 0 ) THEN 
      UPDATE ges_dashboard SET p_Productos = p_Productos-1;
      UPDATE ges_dashboard SET a_ProductosSinStock = a_ProductosSinStock-1; 
    END IF; 
    IF(NEW.Eliminado = 0 AND OLD.Eliminado = 1 ) THEN 
      UPDATE ges_dashboard SET p_Productos = p_Productos+1;
      UPDATE ges_dashboard SET a_ProductosSinStock = a_ProductosSinStock+1; 
    END IF; 
  END IF; 
  IF(NEW.Servicio = 1) THEN 
    IF(NEW.Eliminado = 1 AND OLD.Eliminado = 0 ) THEN 
      UPDATE ges_dashboard SET p_Servicios = p_Servicios-1;
    END IF; 
    IF(NEW.Eliminado = 0 AND OLD.Eliminado = 1 ) THEN 
      UPDATE ges_dashboard SET p_Servicios = p_Servicios+1;
    END IF; 
  END IF; 
END; 

;;;;;;

create trigger actualizar_cmbtenum_up after update on ges_comprobantesnum  
FOR EACH ROW 
begin 
  DECLARE pendiente double; 
  DECLARE xidlocal smallint; 
  DECLARE status tinyint;
  DECLARE cobranza VARCHAR(10);
  DECLARE reservado tinyint;
  DECLARE fecha varchar(16);
  DECLARE importe DOUBLE;

  SELECT c.Status,c.Cobranza,c.Reservado,c.FechaEntregaReserva,c.IdLocal,c.TotalImporte,c.ImportePendiente INTO @status,@cobranza,@reservado,@fecha,@xidlocal,@importe,@pendiente FROM ges_comprobantes c WHERE c.IdComprobante = OLD.IdComprobante;

  IF(NEW.Status = 'Anulado' AND OLD.Status <> 'Anulado') THEN 
    IF(@pendiente > 0 AND @status = 1) THEN 
      UPDATE ges_dashboard SET v_PendCreditos = v_PendCreditos-1 WHERE IdLocal = @xidlocal;
      UPDATE ges_dashboard SET v_PendCreditosMonto = v_PendCreditosMonto-@pendiente WHERE IdLocal = @xidlocal;
    END IF; 
    IF(@pendiente > 0 ) THEN 
      UPDATE ges_dashboard SET f_PendPorCobrar = f_PendPorCobrar-1 WHERE IdLocal = @xidlocal;
      UPDATE ges_dashboard SET f_PendPorCobrarMonto = f_PendPorCobrarMonto-@pendiente WHERE IdLocal = @xidlocal;
    END IF; 
    IF(@cobranza = 'Coactivo' ) THEN 
      UPDATE ges_dashboard SET f_VencPorCobrar = f_VencPorCobrar-1 WHERE IdLocal = @xidlocal;
      UPDATE ges_dashboard SET f_VencPorCobrarMonto = f_VencPorCobrarMonto-@pendiente WHERE IdLocal = @xidlocal;
    END IF; 
    IF(@fecha = '0000-00-00 00:00' AND @reservado = 1) THEN 
      UPDATE ges_dashboard SET v_ReservasEntregar = v_ReservasEntregar-1 WHERE IdLocal = @xidlocal;
    END IF; 
    IF(@reservado = 1 AND @pendiente > 0 ) THEN 
      UPDATE ges_dashboard SET v_PendReservas = v_PendReservas-1 WHERE IdLocal = @xidlocal;
      UPDATE ges_dashboard SET v_PendReservasMonto = v_PendReservasMonto-@pendiente WHERE IdLocal = @xidlocal;
    END IF; 
  END IF; 
END; 

;;;;;;

create trigger actualizar_movbancario_in before insert on ges_movimiento_bancario  
FOR EACH ROW 
begin 
  DECLARE xSaldo DOUBLE; 
  SELECT IF(SUM(Saldo) IS NULL,0,Saldo) INTO xSaldo FROM ges_movimiento_bancario WHERE IdCuentaBancaria = NEW.IdCuentaBancaria AND Eliminado = 0 ORDER BY IdMovimientoBancario DESC LIMIT 1;  
  IF(NEW.TipoMovimiento = 'Ingreso' ) THEN 
    SET NEW.Saldo = xSaldo + NEW.Importe; 
  END IF; 
  IF(NEW.TipoMovimiento = 'Salida' ) THEN 
    SET NEW.Saldo = xSaldo - NEW.Importe; 
  END IF; 
END; 

;;;;;;

create procedure actualizar_kardex_costo (NEWCostoTotalMovimiento DOUBLE,NEWIdLocal INT,NEWTipoMovimiento CHAR(7)) 
begin 
    IF(NEWTipoMovimiento = 'Entrada') THEN 
    UPDATE ges_dashboard SET a_CostoTotal = a_CostoTotal+NEWCostoTotalMovimiento WHERE IdLocal = NEWIdLocal; 
    END IF; 
    IF(NEWTipoMovimiento = 'Salida') THEN 
    UPDATE ges_dashboard SET a_CostoTotal = a_CostoTotal-NEWCostoTotalMovimiento WHERE IdLocal = NEWIdLocal; 
    END IF; 
END; 

;;;;;;

create procedure actualizar_promociones (NEWEstado char(15),OLDEstado char(15),NEWIdLocal INT) 

begin 

  IF(NEWEstado = 'Ejecucion' AND OLDEstado <> 'Ejecucion') THEN 
    UPDATE ges_dashboard SET v_EjecPromociones = v_EjecPromociones+1 WHERE IdLocal = NEWIdLocal;
  END IF; 
  IF(NEWEstado <> 'Ejecucion' AND OLDEstado = 'Ejecucion') THEN 
    UPDATE ges_dashboard SET v_EjecPromociones = v_EjecPromociones-1 WHERE IdLocal = NEWIdLocal;
  END IF; 
END; 

;;;;;;

create trigger actualizar_unidadesvitrina before update on ges_almacenes 
FOR EACH ROW 
begin
  DECLARE Resto DOUBLE; 
  IF(OLD.Unidades > NEW.Unidades) THEN 
    SET Resto = (OLD.Unidades - NEW.Unidades); 
    IF((NEW.UnidadesVitrina-Resto) > 0 ) THEN 
      SET NEW.UnidadesVitrina = NEW.UnidadesVitrina-Resto;
    ELSE
      SET NEW.UnidadesVitrina = 0;    
    END IF; 
  END IF; 
END; 

;;;;;;
