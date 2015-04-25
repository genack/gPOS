ALTER TABLE `ges_almacenes` ADD `CostoOperativo` double NOT NULL default '0' AFTER `EstadoInventario`;


ALTER TABLE `ges_clientes` ADD `FechaNacimiento` DATE NOT NULL DEFAULT '0000-00-00' AFTER `Comentarios`;
ALTER TABLE `ges_clientes` ADD `Login` varchar(20) NOT NULL AFTER `Suscripcion`;
ALTER TABLE `ges_clientes` ADD `Pass` varchar(60) NOT NULL AFTER `Login`;
ALTER TABLE `ges_clientes` ADD `Alta` tinyint(4) NOT NULL AFTER `Pass`;
ALTER TABLE `ges_clientes` ADD `CodigoAlta` varchar(8) NOT NULL AFTER `Alta`;


ALTER TABLE `ges_comprobantes` ADD  `Traslado` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'IdPedido Traslado' AFTER `Observaciones`;


ALTER TABLE `ges_comprobantesdet` ADD `CantidadDevolucion` float NOT NULL DEFAULT '0' AFTER `Oferta`;
ALTER TABLE `ges_comprobantesdet` ADD `IdOrdenServicio` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `CantidadDevolucion`;

ALTER TABLE `ges_locales` ADD `CuentaBancaria2` tinytext NOT NULL AFTER `CuentaBancaria`;
ALTER TABLE `ges_locales` ADD `Descuento` double NOT NULL AFTER `GarantiaComercial`;
ALTER TABLE `ges_locales` ADD `MetodoRedondeo` enum('SR','RDE','RIE') NOT NULL default 'RDE' comment 'SR:Sin Redondeo,RIDE:Redondeo importe a decimal entero,RIE, Redondeo a ImporteEntero' AFTER `Descuento`;
ALTER TABLE `ges_locales` ADD `COPImpuesto` tinyint(1) unsigned NOT NULL default '0' AFTER `MetodoRedondeo`;
ALTER TABLE `ges_locales` ADD `AdmitePassword` tinyint(1) unsigned NOT NULL default '0' AFTER `COPImpuesto`;


ALTER TABLE `ges_perfiles_usuario` ADD `B2B` tinyint(1) unsigned NOT NULL default '0' AFTER `TPV`;
ALTER TABLE `ges_perfiles_usuario` ADD `PedidosVenta` tinyint(1) NOT NULL DEFAULT '0' AFTER `CajaTPV`;
ALTER TABLE `ges_perfiles_usuario` ADD `Servicios` tinyint(1) NOT NULL DEFAULT '0' AFTER `PedidosVenta`;
ALTER TABLE `ges_perfiles_usuario` ADD `SAT` tinyint(1) NOT NULL DEFAULT '0' AFTER `Servicios`;
ALTER TABLE `ges_perfiles_usuario` ADD `Suscripcion` tinyint(1) NOT NULL DEFAULT '0' AFTER `SAT`;

ALTER TABLE `ges_proveedores` ADD `CuentaBancaria2` tinytext NOT NULL AFTER `CuentaBancaria`;

ALTER TABLE `ges_subfamilias` ADD `MargenUtilidadVD` double unsigned NOT NULL default '0' AFTER `SubFamilia`;
ALTER TABLE `ges_subfamilias` ADD `MargenUtilidadVC` double unsigned NOT NULL default '0' AFTER `MargenUtilidadVD`;
ALTER TABLE `ges_subfamilias` ADD `Descuento` double unsigned NOT NULL default '0' AFTER `MargenUtilidadVC`;

ALTER TABLE `ges_usuarios` ADD `GrupoLocales` tinytext NOT NULL COMMENT 'IdLocales' AFTER `CuentaBanco`;

DROP TABLE `ges_clientes_online`;
DROP TABLE `ges_pedidos_online`;
DROP TABLE `ges_pedidos_detalle_online`;
DROP TABLE `ges_pedidos_sinregistro_online`;

ALTER TABLE `ges_presupuestos` CHANGE `TipoPresupuesto` `TipoPresupuesto` enum('Proforma','Preventa','ProformaOnline') NOT NULL DEFAULT 'Preventa';

ALTER TABLE `ges_presupuestos` ADD `IdOperacionCaja` bigint(20) NOT NULL DEFAULT '0' comment 'ges_dinero_movimientos' AFTER `IdOrdenServicio`;
ALTER TABLE `ges_presupuestos` ADD `ImporteAdelanto` double NOT NULL DEFAULT '0' AFTER `IdOperacionCaja`;

ALTER TABLE `ges_ordenservicio` ADD `Tipo` enum('Regular','Garantia')NOT NULL DEFAULT 'Regular' AFTER `Estado`;

ALTER TABLE `ges_ordenserviciodet` CHANGE `FechaChange` `FechaChange` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS `ges_synctpv` (
  `IdSync` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `IdLocal` int(10) unsigned NOT NULL DEFAULT '0',
  `IdUsuario` int(10) unsigned NOT NULL DEFAULT '0',
  `KeySync` char(32) NOT NULL,
  `Preventa` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Proforma` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ProformaOnline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Cliente` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Promocion` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Mensaje` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Caja` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `MetaProducto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`IdSync`)
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

TRUNCATE ges_listados;
source listados.sql; 
TRUNCATE ges_templates;
source template.sql;