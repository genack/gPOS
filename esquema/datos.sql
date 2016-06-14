INSERT INTO `ges_arqueo_caja` 
VALUES ('', 1, 'VD', NOW(), '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
;;;;;;

;;;;;;
INSERT INTO  ges_arqueo_cajagral (IdArqueoCajaGral,IdLocal,IdUsuario,IdMoneda,FechaApertura,FechaCierre,esCerrada,ImporteApertura,ImporteIngresos,ImporteCompras,ImporteGastos,ImporteAportaciones,ImporteSustracciones,ImporteTeoricoCierre,ImporteCierre,ImporteDescuadre) 
VALUES ('', 1, 1,1, NOW(), '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
       ('', 1, 1,2, NOW(), '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
;;;;;;


INSERT INTO `ges_clientes` 
VALUES (1, 'Interno', 'Cliente Contado', '', '', '', '', '', 1, 0, 0, '', 0, '', '', '', '', '', '', '', '0000-00-00', NOW(), NOW(), 0, 0, 0, 0, '', '', 0, '', '', 0),
       (2, 'Interno', 'Administrador', 'Administrador', '', '', '', '', 1, 0, 0, '', 0, '', '', 'admin@admin', '', '', '', '', '0000-00-00', NOW(), NOW(), 0, 0, 0, 0, 'admin', md5('admin'), 1, '', '',0);

;;;;;;


INSERT INTO `ges_modelos` VALUES (1, 0, 1, '...', 2, 0);
;;;;;;

;;;;;;
INSERT INTO `ges_productos_alias` VALUES (1, 0, 1, '...', 2, 0);
;;;;;;

;;;;;;
INSERT INTO `ges_familias` VALUES (1, 1, 1, 'NORMAL', 1);
;;;;;;

;;;;;;
INSERT INTO `ges_idiomas` VALUES (1, 'ESPAÑOL', 'es', 1, 0, 0);
;;;;;;

;;;;;;
INSERT INTO `ges_locales` 
VALUES (1, 1, 1, 1, 'ALMACEN', 'almacen', md5('almacen'), 'LOCAL', '0', '', '', '', '', '', '', '', '', '',18,0, 1, 2, 0, 0, '', '','', 10, 0, 7, 12, 0, 'RDE', 0, 0, 0, 0);

;;;;;;

;;;;;;
INSERT INTO `ges_modalidadespago` 
VALUES (1, 'EFECTIVO', 0),
       (2, 'DEPOSITO BANCARIO', 0),
       (3, 'TRANSFERENCIA BANCARIA', 0),
       (4, 'TARJETA DE CREDITO', 0),
       (5, 'TARJETA DE DEBITO', 0),
       (6, 'CHEQUE', 0),
       (7, 'GIRO', 0),
       (8, 'ENVIO', 1),
       (9, 'NOTA DE CREDITO', 0),
       (10, 'BONO DE COMPRA', 0);
;;;;;;

;;;;;;
INSERT INTO `ges_paises` VALUES (1, 'PERU', 1, 'NIF/CIF', 'IGV', 18, 'S/.', 0);
;;;;;;

;;;;;;
INSERT INTO `ges_marcas` VALUES (NULL,'...', 0);
;;;;;;

;;;;;;
INSERT INTO `ges_parametros` VALUES (1, 1, 0, 0, 7, 0, 0, 0, 'FIFO', 0, 0, 0, 40, 210, 8, 0, 1, 1, 'Mensaje Promoción', 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00', 0, 0, 0);
;;;;;;

;;;;;;
INSERT INTO `ges_perfiles_usuario` 
VALUES (1, 'Administrador', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
       (2, 'Vendedor', 0, 1, 0, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0),
       (3, 'Jefe de Ventas', 0, 1, 0, 1, 0, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0),
       (4, 'Encargado Informes', 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
       (5, 'Encargado Compras', 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 0, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0),
       (6, 'Encargado Almacenes', 0, 0, 0, 1, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0),
       (7, 'Vendedor Web', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
;;;;;;

;;;;;;
INSERT INTO `ges_proveedores` VALUES (1, 'CASAS VARIAS', '', '', '', '', '', 1, 0, 0, 0, 1, '', '', '', '', '', '', '', 0);

;;;;;;
INSERT INTO `ges_laboratorios` VALUES (1, '.', '', '', '', '', '', 1, 0, '', 1, '', '', '', '', '', '', '', 0);
;;;;;;

INSERT INTO `ges_comprobantesstatus` 
VALUES (1, 'PENDIENTE PAGO'),
       (2, 'PAGADA'),
       (3, 'IMPAGADA'),
       (4, 'ANULADA'),
       (5, 'CEDIDO'),
       (6, 'RECUPERADO');
;;;;;;

INSERT INTO ges_detallescategoria 
VALUES (1, 'Numérico'),
       (2, 'Tamaño'),
       (3, 'Size'),
       (4, 'Talla única'),
       (5, 'Varios');
;;;;;;

INSERT INTO ges_detalles 
VALUES (1, 4, 0, 1, 1, 2, 'Única', 0),
       (2, 2, 1, 1, 1, 2, 'pequeña', 0),
       (3, 2, 2, 2, 1, 2, 'mediana', 0),
       (4, 2, 3, 3, 1, 2, 'grande', 0),
       (5, 5, 4, 0, 1, 2, '...', 0),
       (6, 3, 5, 4, 1, 2, 'L', 0),
       (7, 3, 6, 3, 1, 2, 'M', 0),
       (8, 3, 7, 2, 1, 2, 'S', 0),
       (9, 3, 8, 1, 1, 2, 'XS', 0),
       (10, 3, 9, 5, 1, 2, 'XL', 0),
       (11, 3, 10, 6, 1, 2, 'XXL', 0),
       (12, 1, 11, 0, 1, 2, '30', 0),
       (13, 1, 12, 0, 1, 2, '38', 0),
       (14, 1, 13, 0, 1, 2, '40', 0),
       (15, 1, 14, 0, 1, 2, '42', 0),
       (16, 1, 15, 0, 1, 2, '44', 0),
       (17, 1, 16, 0, 1, 2, '46', 0),
       (18, 1, 17, 0, 1, 2, '48', 0),
       (19, 1, 18, 0, 1, 2, '50', 0),
       (20, 4, 19, 0, 1, 2, 'mediana', 0),
       (21, 4, 20, 0, 1, 2, 'pequeña', 0),
       (22, 4, 21, 0, 1, 2, 'grande',  0),
       (23, 4, 22, 0, 1, 2, 'super', 0),
       (24, 4, 23, 0, 1, 2, '38', 0),
       (25, 4, 24, 0, 1, 2, '40', 0),
       (26, 4, 25, 0, 1, 2, '42', 0),
       (27, 4, 26, 0, 1, 2, '44', 0),
       (28, 4, 27, 0, 1, 2, '46', 0),
       (29, 4, 28, 0, 1, 2, '48', 0),
       (30, 4, 29, 0, 1, 2, '50', 0),
       (31, 4, 30, 0, 1, 2, '52', 0),
       (32, 4, 31, 0, 1, 2, '54', 0),
       (33, 1, 32, 0, 1, 2, '52', 0),
       (34, 1, 33, 0, 1, 2, '54', 0),
       (35, 1, 34, 0, 1, 2, '56', 0),
       (36, 1, 35, 0, 1, 2, '36', 0),
       (37, 1, 36, 0, 1, 2, '34', 0),
       (38, 1, 37, 0, 1, 2, '58', 0),
       (39, 1, 38, 0, 1, 2, '60', 0),
       (40, 1, 39, 0, 1, 2, '62', 0),
       (41, 2, 40, 0, 1, 2, 'super G', 0),
       (42, 5, 41, 0, 1, 2, 'UNICA', 0);

;;;;;;


INSERT INTO `ges_contenedores` 
VALUES (1,'...', 0),
       (2,'CAJA',0);
;;;;;;


;;;;;;
INSERT INTO `ges_usuarios` VALUES (1, 'Mantenimiento', 'soporte', md5('x0+admin13'), 1, 'Las labores de mantenimiento se realizan con esta cuenta. Por tanto es aconsejable no borrarla.', '', '', '0000-00-00', 0, 1, 0, 1, 0, '', '', 'Activo', 0);
;;;;;;

INSERT INTO `ges_usuarios` VALUES (2, 'Web', 'web', md5('web'), 1, '', '', '', '0000-00-00', 0, 7, 0, 0, 0, '', '', 'Inactivo', 0);
;;;;;;

INSERT INTO `ges_usuarios` VALUES (3, 'Administrador', 'admin', md5('admin'), 1, '', '', '', '0000-00-00', 0, 1, 0, 0, 0, '', '', 'Activo', 0);
;;;;;;


INSERT INTO `ges_familias` VALUES (2, 2, 1, 'VARIOS', 0);
;;;;;;

INSERT INTO `ges_subfamilias` VALUES (1, 2, 1, 1, '...', 0, 0,0 , 0);
;;;;;;

INSERT INTO `ges_comprobantestipo`  
VALUES (1, 'Factura', '1', '1', '0', '', '0'), 
       (2, 'Boleta', '1', '1', '0', '', '0'), 
       (3, 'Ticket', '1', '1', '0', '', '0'), 
       (4, 'Albaran', '1', '1', '0', '', '0'), 
       (5, 'AlbaranInt', '1', '1', '0', '', '0'), 
       (6, 'Factura', '2', '2', '0', '', '0'), 
       (7, 'Boleta', '2', '2', '0', '', '0'), 
       (8, 'Ticket', '2', '2', '0', '', '0'), 
       (9, 'Albaran', '2', '2', '0', '', '0'), 
       (10, 'AlbaranInt', '2', '2', '0', '', '0'), 
       (11, 'Factura', '3', '3', '0', '', '0'),  
       (12, 'Boleta', '3', '3', '0', '', '0'),
       (13, 'Ticket', '3', '3', '0', '', '0'),
       (14, 'Albaran', '3', '3', '0', '', '0'),
       (15, 'AlbaranInt', '3', '3', '0', '', '0');  
;;;;;;

INSERT INTO `ges_comprobantesformato` 
VALUES (1, 'CONTINUO', '168*100', 'Boleta', 1, '', 0),
       (2, 'DESGLOZABLE', '210*148', 'Boleta', 1, '', 0),
       (3, 'DESGLOZABLE', '210*297', 'Factura', 1, '', 0);
;;;;;;

INSERT INTO `ges_moneda`
VALUES ( 1, 'Nuevo Sol', 'Nuevos Soles', 'S/.', '0'), 
       ( 2, 'Dólar', 'Dólares', '$', '0');
;;;;;;

INSERT INTO `ges_subsidiarios`
VALUES ( 1, 'FLETADOR LOCAL', '', '', '', '', '', '0', '0', '', '0', '', '', '', '', '', '', 'Servicios de transporte local', '0');
;;;;;;

INSERT INTO `ges_subsidiariosserv` VALUES (1, 'TRANSPORTE', '0');

;;;;;;

INSERT INTO `ges_kardexoperacion`
VALUES (1,'Compra',0),
       (2,'Venta',0),
       (3,'Traslado Interno',0),
       (4,'Traslado Externo',0),
       (5,'Ajuste',0),
       (6,'Inventario',0);

;;;;;;

INSERT INTO `ges_kardexajusteoperacion`
VALUES (1,'Salida','Vencido',0),
       (2,'Salida','Malogrado',0),
       (3,'Salida','Robo',0),
       (4,'Salida','Observado',0),
       (5,'Entrada','Error registro compra',0),
       (6,'Entrada','Error registro venta',0),
       (7,'Entrada','Inicio de operaciones',0);

;;;;;;

INSERT INTO `ges_motivoalbaran`
VALUES (1,'Venta',0,0,1,0),
       (2,'Consignación',1,0,1,0),
       (3,'Compra',0,1,0,0),
       (4,'Devolución',1,1,1,0),
       (5,'Traslado',1,0,0,0),
       (6,'Inmovilización',1,0,0,0),
       (7,'Ajuste',0,0,0,0),
       (8,'Inventario',0,0,0,0),
       (9,'MetaProducto',0,0,0,0),
       (10,'Traslado y Recepción',1,0,0,0),
       (11,'Importacion', 0, 1, 0, 0),
       (12,'Exportacion', 0, 0, 0, 0);

;;;;;;

INSERT INTO `ges_partidascaja`
VALUES (1,0,'CG','Aporte Socios','Aportacion',CURRENT_TIMESTAMP,'S101',0),
       (2,0,'CG','Servicios Básicos','Gasto',CURRENT_TIMESTAMP,'S102',0),
       (3,0,'CG','Transporte','Gasto',CURRENT_TIMESTAMP,'S103',0),
       (4,0,'CG','Ventas','Ingreso',CURRENT_TIMESTAMP,'S104',0),
       (5,0,'CG','Depósito bancario','Sustraccion',CURRENT_TIMESTAMP,'S105',0),
       (6,0,'CG','Retiro bancario','Ingreso',CURRENT_TIMESTAMP,'S106',0),
       (7,0,'CG','Préstamo','Ingreso',CURRENT_TIMESTAMP,'S107',0),
       (8,0,'CG','Pago préstamo','Sustraccion',CURRENT_TIMESTAMP,'S108',0),
       (9,0,'CG','Compras','Egreso',CURRENT_TIMESTAMP,'S109',0),
       (10,0,'VD','Servicios Básicos','Gasto',CURRENT_TIMESTAMP,'S110',0),
       (11,0,'VD','Transporte','Gasto',CURRENT_TIMESTAMP,'S111',0),
       (12,0,'VD','Transferencia a caja general','Sustraccion',CURRENT_TIMESTAMP,'S112',0),
       (13,0,'VC','Servicios Básicos','Gasto',CURRENT_TIMESTAMP,'S113',0),
       (14,0,'VC','Transporte','Gasto',CURRENT_TIMESTAMP,'S114',0),
       (15,0,'VC','Transferencia a caja general','Sustraccion',CURRENT_TIMESTAMP,'S115',0),
       (16,0,'VC','Devolucion','Sustraccion',CURRENT_TIMESTAMP,'S116',0),
       (17,0,'VD','Devolucion','Sustraccion',CURRENT_TIMESTAMP,'S117',0),
       (18,0,'CG','Cobro Comprobantes','Ingreso',CURRENT_TIMESTAMP,'S118',0),
       (19,0,'CG','Servicio tercerizado','Gasto',CURRENT_TIMESTAMP,'S119',0),
       (20,0,'VD','Servicio tercerizado','Gasto',CURRENT_TIMESTAMP,'S120',0),
       (21,0,'VC','Servicio tercerizado','Gasto',CURRENT_TIMESTAMP,'S121',0),
       (22,0,'VC','Adelanto Comprobante','Sustraccion',CURRENT_TIMESTAMP,'S122',0),
       (23,0,'VD','Adelanto Comprobante','Sustraccion',CURRENT_TIMESTAMP,'S123',0),
       (24,0,'CG','Transferencia a almacén central','Sustraccion',CURRENT_TIMESTAMP,'S124',0),
       (25,0,'CG','Cambio moneda','Sustraccion',CURRENT_TIMESTAMP,'S125',0),
       (26,0,'CG','Transferencia entre almacenes','Sustraccion',CURRENT_TIMESTAMP,'S126',0);

;;;;;;

INSERT INTO `ges_historialventaperiodo`
VALUES (1,0,'Inicio de los tiempos',NOW(),0);

;;;;;;

INSERT INTO `ges_tiposervicio`
VALUES (NULL,'...',0,0);

;;;;;;

INSERT INTO `ges_modelosat`
VALUES (NULL,1,'...',0);

;;;;;;

INSERT INTO `ges_dwgroups`
VALUES (NULL,'admin', 0),
       (NULL,'user', 0);

;;;;;;

INSERT INTO `ges_dwusergroup`
VALUES (2,1, 0),
       (2,2, 0);

;;;;;;

INSERT INTO `ges_dashboard`
VALUES (NULL,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);


;;;;;;
