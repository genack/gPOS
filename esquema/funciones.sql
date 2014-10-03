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
  IF (esSerie = 1) THEN
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
    IF(NEW.CantidadMovimiento = 0) THEN 
       SET NEW.Estado = 4; 
       SET NEW.EstadoDetalle = 'Entrada: Cantidad igual a 0'; 
    END IF; 
  ELSEIF(NEW.TipoMovimiento = 'Salida') THEN 
    SELECT Serie INTO esSerie FROM ges_comprobantesdet WHERE IdProducto = NEW.IdProducto AND IdPedidoDet = NEW.IdPedidoDet AND IdComprobanteDet = NEW.IdComprobanteDet; 
    SELECT obtenerStockProducto(NEW.IdProducto , NEW.IdLocal, NEW.IdPedidoDet) INTO Stock; 
    IF((NEW.CantidadMovimiento)*(-1) > Stock) THEN 
       SET NEW.Estado = 1; 
       SET NEW.EstadoDetalle = 'Salida: Stock menor que la cantidad';
    END IF; 
    IF((Stock + NEW.CantidadMovimiento) < 0) THEN 
       SET NEW.Estado = 2; 
       SET NEW.EstadoDetalle = 'Salida: Stock negativo'; 
    END IF; 
    IF(Stock = 0) THEN 
       SET NEW.Estado = 3; 
       SET NEW.EstadoDetalle = 'Salida: Stock cero'; 
    END IF; 
  END IF; 
  IF (esSerie = 1) THEN 
    IF (NEW.TipoMovimiento = 'Entrada')  THEN 
      SET np = (SELECT Unidades FROM ges_pedidosdet WHERE ges_pedidosdet.IdPedidoDet = NEW.IdPedidoDet AND ges_pedidosdet.IdProducto = NEW.IdProducto); 
      SET ns = (SELECT COUNT(*) FROM ges_productos_series WHERE ges_productos_series.DocumentoEntrada = NEW.IdPedidoDet AND ges_productos_series.IdProducto = NEW.IdProducto AND ges_productos_series.Eliminado = 0); 
      IF(ns <> np) THEN 
      	SET NEW.Estado = 5; 
        SET NEW.EstadoDetalle = 'Entrada: Cantidad y números de series diferentes.'; 
      END IF; 
    ELSEIF(NEW.TipoMovimiento = 'Salida') THEN 
      SELECT IdComprobante INTO IdComp FROM ges_comprobantesdet WHERE ges_comprobantesdet.IdComprobanteDet = NEW.IdComprobanteDet; 
      SELECT Cantidad INTO np FROM ges_comprobantesdet WHERE ges_comprobantesdet.IdComprobanteDet = NEW.IdComprobanteDet; 
      SELECT COUNT(*) INTO ns FROM ges_productos_series WHERE ges_productos_series.IdProducto = NEW.IdProducto AND ges_productos_series.DocumentoSalida = IDComp AND Eliminado = 0; 
      IF(ns <> np) THEN 
      	SET NEW.Estado = 6; 
        SET NEW.EstadoDetalle = 'Salida: Cantidad y números de series diferentes.'; 
      END IF; 
    END IF; 
  END IF; 
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