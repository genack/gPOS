CREATE TABLE `ges_almacenes` (
  `Id` bigint(20) unsigned NOT NULL auto_increment,
  `IdLocal` smallint(5) unsigned NOT NULL default '0',
  `IdProducto` bigint(20) unsigned NOT NULL default '0',
  `Unidades` float NOT NULL DEFAULT '0',
  `StockMin` smallint(5) unsigned NOT NULL default '0',
  `StatusNS` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `PrecioVentaSource` char(30) NOT NULL DEFAULT '0',
  `PrecioVentaCorpSource` char(30) NOT NULL DEFAULT '0',
  `PrecioVenta` double unsigned NOT NULL default '0',
  `PrecioVentaCorporativo` double unsigned NOT NULL default '0',
  `PrecioVentaOferta` double unsigned NOT NULL DEFAULT '0',
  `PVDDescontado` double unsigned NOT NULL default '0',
  `PVCDescontado` double unsigned NOT NULL default '0',
  `CostoUnitario` double unsigned NOT NULL DEFAULT '0',
  `PVODescontado` double NOT NULL default '0',
  `TipoImpuesto` TINYTEXT NULL DEFAULT NULL,
  `Impuesto` DOUBLE NOT NULL DEFAULT '0',
  `StockIlimitado` tinyint(1) unsigned NOT NULL default '0',
  `Disponible` tinyint(1) unsigned NOT NULL default '0',
  `DisponibleUnidades` float NOT NULL DEFAULT '0',
  `DisponibleOnline` tinyint(1) unsigned NOT NULL default '0',
  `Oferta` tinyint(1) unsigned NOT NULL default '0',
  `OfertaUnidades` float NOT NULL DEFAULT '0',
  `FechaChange` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ResumenKardex` text NOT NULL,
  `EstadoInventario` tinyint(1) unsigned NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `IdLocal` (`IdLocal`,`IdProducto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_arqueo_caja` (
  `IdArqueo` bigint(20) unsigned NOT NULL auto_increment,
  `IdLocal` smallint(6) NOT NULL default '0',
  `TipoVentaOperacion` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `FechaApertura` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaCierre` datetime NOT NULL default '0000-00-00 00:00:00',
  `esCerrada` smallint(6) NOT NULL default '0',
  `ImporteApertura` double NOT NULL default '0',
  `ImporteIngresos` double NOT NULL default '0',
  `ImporteGastos` double NOT NULL default '0',
  `ImporteAportaciones` double NOT NULL default '0',
  `ImporteSustracciones` double NOT NULL default '0',
  `ImporteTeoricoCierre` double NOT NULL default '0',
  `ImporteCierre` double NOT NULL default '0',
  `ImporteDescuadre` double NOT NULL default '0',
  `UtilidadVenta` double NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdArqueo`),
  KEY `FechaArqueo` (`FechaApertura`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_arqueo_cajagral` (
  `IdArqueoCajaGral` bigint(20) unsigned NOT NULL auto_increment,
  `IdLocal` smallint(6) NOT NULL default '0',
  `IdUsuario` smallint(6) NOT NULL default '0',
  `IdMoneda` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `FechaApertura` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaCierre` datetime NOT NULL default '0000-00-00 00:00:00',
  `esCerrada` smallint(6) NOT NULL default '0',
  `ImporteApertura` double NOT NULL default '0',
  `ImporteIngresos` double NOT NULL default '0',
  `ImporteCompras` double NOT NULL default '0',
  `ImporteGastos` double NOT NULL default '0',
  `ImporteAportaciones` double NOT NULL default '0',
  `ImporteSustracciones` double NOT NULL default '0',
  `ImporteTeoricoCierre` double NOT NULL default '0',
  `ImporteCierre` double NOT NULL default '0',
  `ImporteDescuadre` double NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdArqueoCajaGral`),
  KEY `FechaArqueo` (`FechaApertura`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_librodiario_cajagral` (
  `IdOperacionCaja` bigint(20) NOT NULL auto_increment,
  `IdArqueoCajaGral` bigint(20) NOT NULL default '0',
  `IdLocal` smallint(6) NOT NULL default '0',
  `IdUsuario` int(2) NOT NULL,
  `IdPartidaCaja` INT  UNSIGNED NOT  NULL DEFAULT '0',
  `IdMoneda` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `IdPagoProvDoc` bigint(20) unsigned NOT NULL default '0',
  `IdComprobante` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Ventas',
  `IdSubsidiario` smallint(5) unsigned NOT NULL default '0',
  `CambioMoneda` DOUBLE NOT NULL DEFAULT '1',
  `FechaCaja` date NOT NULL default '0000-00-00',
  `FechaInsercion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `TipoOperacion` enum('Egreso','Ingreso','Gasto','Aportacion','Sustraccion') NOT NULL default 'Egreso',
  `Concepto` tinytext NOT NULL,
  `Documento` ENUM('Factura','Boleta','AlbaranInt','Ticket','Voucher') NOT NULL DEFAULT 'Ticket',
  `CodigoDocumento` varchar(20) NOT NULL,
  `Importe` double NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdOperacionCaja`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Movimientos diarios';

;;;;;;

CREATE TABLE `ges_bugs` (
  `IdBug` bigint(20) NOT NULL auto_increment,
  `LOC` tinytext,
  `Urgencia` tinyint(4) NOT NULL default '0',
  `Categoria` tinytext,
  `Titulo` tinytext NOT NULL,
  `QueDonde` text NOT NULL,
  `QueEsperaba` text NOT NULL,
  `QueOcurrio` text NOT NULL,
  `LogHistorico` text NOT NULL,
  `Status` tinyint(4) NOT NULL default '0',
  `Eliminado` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`IdBug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_clientes` (
  `IdCliente` smallint(5) unsigned NOT NULL auto_increment,
  `TipoCliente` enum('Particular','Empresa','Gobierno','Interno','Independiente') NOT NULL default 'Particular',
  `NombreComercial` tinytext NOT NULL,
  `NombreLegal` tinytext NOT NULL,
  `NumeroFiscal` tinytext NOT NULL,
  `Direccion` tinytext NOT NULL,
  `CP` tinytext NOT NULL,
  `Localidad` tinytext NOT NULL,
  `IdPais` smallint(5) unsigned NOT NULL default '0',
  `IdLocal` int(11) NOT NULL,
  `Descuento` double unsigned NOT NULL default '0',
  `CuentaBancaria` tinytext NOT NULL,
  `IdModPagoHabitual` smallint(5) unsigned NOT NULL default '0',
  `Telefono1` tinytext NOT NULL,
  `Telefono2` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `PaginaWeb` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Cargo` tinytext NOT NULL,
  `Comentarios` tinytext NOT NULL,
  `FechaRegistro` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FechaChange` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Bono` double NOT NULL Default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdCliente`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_colores` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `IdColor` smallint(5) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `Color` tinytext NOT NULL,
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  KEY `IdColor` (`IdColor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_productos_alias` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `IdProductoAlias` smallint(5) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `ProductoAlias` tinytext NOT NULL,
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  KEY `IdProductoAlias` (`IdProductoAlias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_pedidosdet` (
    `IdPedidoDet` bigint(20) unsigned NOT NULL auto_increment,
    `IdPedido` bigint(20) unsigned NOT NULL default '0',
    `IdProducto` bigint(20) unsigned NOT NULL default '0',
    `Unidades` float NOT NULL default '0',
    `CostoUnidad` double NOT NULL default '0',
    `PrecioUnidad` double NOT NULL default '0',
    `Importe` double NOT NULL default '0',
    `Descuento` double NOT NULL default '0',
    `Serie` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con serie(1), sin serie(0), serie malogrado(2)',
    `Lote` tinytext NOT NULL,
    `FechaVencimiento` date NOT NULL default '0000-00-00',
    `FechaGarantia` date NOT NULL,
    `Eliminado` tinyint(1) unsigned NOT NULL default '0',
    PRIMARY KEY  (`IdPedidoDet`),
    KEY `IdAlbaran` (`IdPedido`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_dinero_movimientos` (
  `IdOperacionCaja` bigint(20) NOT NULL auto_increment,
  `IdArqueoCaja` bigint(20) NOT NULL default '0',
  `IdLocal` smallint(6) NOT NULL default '0',
  `IdUsuario` INT(6)  UNSIGNED NOT NULL DEFAULT '0',
  `IdPartidaCaja` INT  UNSIGNED NOT  NULL DEFAULT '0',
  `TipoOperacion` enum('Ingreso','Gasto','Aportacion','Sustraccion') NOT NULL default 'Ingreso',
  `TipoVentaOperacion` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `FechaCaja` date NOT NULL default '0000-00-00',
  `Concepto` tinytext NOT NULL,
  `IdComprobante` bigint(20) UNSIGNED NOT NULL default '0',
  `IdAlbaran` bigint(20) NOT NULL default '0',
  `Importe` double NOT NULL default '0',
  `IdModalidadPago` tinyint(4) NOT NULL default '0',
  `IdTbjoSubsidiario` bigint(20) unsigned NOT NULL default '0',
  `CuentaBancaria` tinytext NOT NULL,
  `FechaInsercion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DocSubsidiario` enum('Factura','Boleta','Ticket') NOT NULL default 'Boleta',
  `NDocSubsidiario` varchar(15) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdOperacionCaja`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_comprobantes` (
  `IdComprobante` bigint(20) unsigned NOT NULL auto_increment,
  `IdLocal` smallint(6) NOT NULL default '0',
  `IdUsuario` smallint(6) NOT NULL default '0',
  `IdPromocion` INT unsigned NOT  NULL default '0',
  `SerieComprobante` tinytext NOT NULL,
  `NComprobante` bigint(20) unsigned NOT NULL default '0',
  `IdAlbaranes` text NOT NULL,
  `TipoVentaOperacion` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `FechaComprobante` date NOT NULL default '0000-00-00',
  `IdCliente` bigint(20) NOT NULL default '0',
  `ImporteNeto` double NOT NULL default '0',
  `ImporteImpuesto` double NOT NULL default '0',
  `TotalImporte` double NOT NULL default '0',
  `Impuesto` double NOT NULL default '0',
  `ImportePendiente` double NOT NULL default '0',
  `Status` tinyint(1) unsigned NOT NULL default '0',
  `Destinatario` ENUM('Cliente','Local','Proveedor') NOT NULL DEFAULT 'Cliente',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdComprobante`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_comprobantesdet` (
    `IdComprobanteDet` bigint(20) unsigned NOT NULL auto_increment,
    `IdComprobante` bigint(20) unsigned NOT NULL default '0',
    `IdAlbaran` bigint(20) unsigned NOT NULL default '0',
    `IdProducto` bigint(20) unsigned NOT NULL default '0',
    `IdPedidoDet` bigint(20) unsigned NOT NULL default '0',
    `Referencia` tinytext NOT NULL,
    `CodigoBarras` tinytext NOT NULL,
    `Concepto` tinytext NOT NULL,
    `Talla` tinytext NOT NULL,
    `Color` tinytext NOT NULL,
    `Cantidad` float NOT NULL DEFAULT '0',
    `CostoUnitario` double NOT NULL DEFAULT '0',
    `Precio` double NOT NULL default '0',
    `Descuento` double NOT NULL default '0',
    `Importe` double NOT NULL default '0',
    `Impuesto` double NOT NULL default '0',
    `Lote` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con Lote(1), sin Lote(0)',
    `Vencimiento` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con Fecha Vencimiento(1), sin Fecha Vencimiento(0)',
    `Serie` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con serie(1), sin serie(0)',
    `Oferta` tinytext NOT NULL COMMENT 'uni~ofertaunid~pv~pvo',
    `Eliminado` tinyint(1) unsigned NOT NULL default '0',
    PRIMARY KEY  (`IdComprobanteDet`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_familias` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `Familia` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  KEY `IdFamilia` (`IdFamilia`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_idiomas` (
  `IdIdioma` smallint(5) unsigned NOT NULL auto_increment,
  `Idioma` tinytext NOT NULL,
  `iso` tinytext NOT NULL,
  `Traducido` tinyint(1) unsigned NOT NULL default '0',
  `Datos` tinyint(1) unsigned NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdIdioma`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_kardexajusteoperacion` (
  `IdKardexAjusteOperacion` int unsigned NOT NULL auto_increment,
  `TipoMovimiento` ENUM('Entrada','Salida') NOT NULL DEFAULT 'Salida',
  `AjusteOperacion` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdKardexAjusteOperacion`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_listados` (
  `IdListado` bigint(20) unsigned NOT NULL auto_increment,
  `NombrePantalla` tinytext NOT NULL,
  `CodigoSQL` text NOT NULL,
  `Area` tinytext NOT NULL,
  `Peso` int(11) NOT NULL default '0',
  `Categoria` tinytext NOT NULL,
  `Eliminado` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`IdListado`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_locales` (
    `IdLocal` smallint(5) unsigned NOT NULL auto_increment,
    `IdIdioma` smallint(5) unsigned NOT NULL default '0',
    `IdPais` smallint(5) unsigned NOT NULL default '0',
    `AlmacenCentral` int unsigned NOT NULL default '0',
    `NombreComercial` tinytext NOT NULL,
    `Identificacion` tinytext NOT NULL,
    `Password` tinytext NOT NULL,
    `NombreLegal` tinytext NOT NULL,
    `IdFiscal` tinytext NOT NULL,
    `NFiscal` tinytext NOT NULL,
    `DireccionFactura` tinytext NOT NULL,
    `Poblacion` tinytext NOT NULL,
    `CodigoPostal` tinytext NOT NULL,
    `Telefono` tinytext NOT NULL,
    `Fax` tinytext NOT NULL,
    `Movil` tinytext NOT NULL,
    `Email` tinytext NOT NULL,
    `PaginaWeb` tinytext NOT NULL,
    `Impuesto` double NOT NULL default '0',
    `Percepcion` double NOT NULL default '0' comment 'Impuesto',
    `ImpuestoIncluido` tinyint(1) unsigned NOT NULL default '0',
    `IdTipoNumeracionFactura` smallint(5) unsigned NOT NULL default '0',
    `CuentaBancaria` tinytext NOT NULL,
    `Logotipo` tinytext NOT NULL,
    `MensajeMes` tinytext NOT NULL,
    `MensajePromocion` TINYTEXT NOT NULL,
    `MargenUtilidad` double NOT NULL,
    `TipoMargenUtilidad` tinyint(1) unsigned NOT NULL,
    `VigenciaPresupuesto` smallint(2) unsigned NOT NULL,
    `GarantiaComercial` smallint(2) unsigned NOT NULL, 
    `Eliminado` tinyint(1) unsigned NOT NULL default '0',
    PRIMARY KEY  (`IdLocal`),
    UNIQUE KEY `IdLocal` (`IdLocal`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_logsql` (
  `Idlogsql` bigint(20) NOT NULL auto_increment,
  `TipoProceso` tinytext NOT NULL,
  `IdProceso` int(11) NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Sql` text NOT NULL,
  `CreadoPor` tinytext NOT NULL,
  `IdCreador` int(11) NOT NULL,
  `Exito` tinyint(4) NOT NULL,
  `FechaCreacion` datetime NOT NULL,
  PRIMARY KEY  (`Idlogsql`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;


CREATE TABLE `ges_marcas` (
  `IdMarca` smallint(5) unsigned NOT NULL auto_increment,
  `Marca` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMarca`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE  `ges_contenedores` (
    `IdContenedor` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `Contenedor` tinytext NOT NULL,
    `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`IdContenedor`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_mensajes` (
  `IdMensaje` bigint(20) NOT NULL auto_increment,
  `Titulo` tinytext NOT NULL,
  `Texto` tinytext NOT NULL,
  `IdAutor` bigint(20) NOT NULL default '0',
  `IdOrigenLocal` bigint(20) NOT NULL default '0',
  `IdLocalRestriccion` bigint(20) NOT NULL default '0',
  `IdUsuarioRestriccion` bigint(20) NOT NULL default '0',
  `Status` enum('Normal','Urgente','Privado','Sistema') NOT NULL default 'Normal',
  `Fecha` datetime NOT NULL default '0000-00-00 00:00:00',
  `DiasCaduca` smallint(6) NOT NULL default '1',
  `Eliminado` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`IdMensaje`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_modalidadespago` (
  `IdModalidadPago` tinyint(2) unsigned NOT NULL auto_increment,
  `ModalidadPago` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdModalidadPago`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_subsidiarios` (
  `IdSubsidiario` smallint(5) unsigned NOT NULL auto_increment,
  `NombreComercial` tinytext NOT NULL,
  `NombreLegal` tinytext NOT NULL,
  `NumeroFiscal` tinytext NOT NULL,
  `Direccion` tinytext NOT NULL,
  `CP` tinytext NOT NULL,
  `Localidad` tinytext NOT NULL,
  `IdPais` smallint(5) unsigned NOT NULL default '0',
  `Descuento` double unsigned NOT NULL default '0',
  `CuentaBancaria` tinytext NOT NULL,
  `IdModPagoHabitual` smallint(5) unsigned NOT NULL default '0',
  `Telefono1` tinytext NOT NULL,
  `Telefono2` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `PaginaWeb` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Cargo` tinytext NOT NULL,
  `Comentarios` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSubsidiario`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_subsidiariosserv` (
  `IdServicio` bigint(20) unsigned NOT NULL auto_increment,
  `Servicio` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdServicio`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_subsidiariostbjos` (
  `IdTbjoSubsidiario` bigint(20) unsigned NOT NULL auto_increment,
  `IdSubsidiario` smallint(5) unsigned NOT NULL default '0',
  `IdProducto` bigint(20) NOT NULL default '0',
  `IdServicio` bigint(20) unsigned NOT NULL default '0',
  `IdOrdenCompra` bigint(20) NOT NULL DEFAULT '0',
  `IdPedido` bigint(20) NOT NULL DEFAULT '0',
  `DocSubsidiario` enum('Factura','Boleta','Ticket') NOT NULL default 'Boleta',
  `NDocSubsidiario` varchar(15) NOT NULL,
  `NTicket` tinytext NOT NULL,
  `Status` enum('Pdte Envio','Enviado','Recibido','Entregado') NOT NULL default 'Pdte Envio',
  `FechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FechaEnvio` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaRecepcion` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaEntrega` datetime NOT NULL default '0000-00-00 00:00:00',
  `Coste` double NOT NULL default '0',
  `CostePendiente` double NOT NULL default '0',
  `Observaciones` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdTbjoSubsidiario`),
  KEY `IdSubsidiario` (`IdSubsidiario`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_paises` (
  `IdPais` smallint(5) unsigned NOT NULL auto_increment,
  `NombrePais` tinytext NOT NULL,
  `IdIdiomaDefecto` smallint(5) unsigned NOT NULL default '0',
  `IdFiscal` tinytext NOT NULL,
  `TipoImpuestoDefecto` tinytext NOT NULL,
  `ImpuestoDefecto` double NOT NULL default '0',
  `SimboloMoneda` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPais`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_parametros` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `MultiLocal` tinyint(1) unsigned NOT NULL default '0',
  `MultiPais` tinyint(1) unsigned NOT NULL default '0',
  `MultiIdioma` tinyint(1) unsigned NOT NULL default '0',
  `AlmacenCentral` smallint(5) unsigned NOT NULL default '0',
  `VentaOnline` tinyint(1) unsigned NOT NULL default '0',
  `Inventario` tinyint(1) unsigned NOT NULL default '0',
  `Compras` tinyint(1) unsigned NOT NULL default '0',
  `GestionInventarios` enum('FIFO','LIFO','AVERAGE') NOT NULL default 'FIFO',
  `Tallas` tinyint(1) unsigned NOT NULL default '0',
  `Colores` tinyint(1) unsigned NOT NULL default '0',
  `Zapaterias` tinyint(1) unsigned NOT NULL default '0',
  `AltoBarras` tinyint(4) NOT NULL default '10',
  `AnchoBarras` int(4) NOT NULL default '300',
  `MaxCodBarras` tinyint(4) NOT NULL default '8',
  `AlmacenTransito` int(11) NOT NULL default '0',
  `IdFamiliaDefecto` tinyint(4) NOT NULL default '1',
  `IdSubFamiliaDefecto` tinyint(4) NOT NULL default '1',
  `MensajePromocion` tinytext NOT NULL,
  `ProductosLatin1` int(11) NOT NULL default '0',
  `PaisesLatin1` int(11) NOT NULL default '0',
  `IdiomasLatin1` int(11) NOT NULL default '0',
  `TallasLatin1` int(11) NOT NULL default '0',
  `ColoresLatin1` int(11) NOT NULL default '0',
  `SubFamiliaLatin1` int(11) NOT NULL default '0',
  `FamiliaLatin1` int(11) NOT NULL default '0',
  `TallajeLatin1` int(11) NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_pedidos` (
  `IdPedido` bigint(20) unsigned NOT NULL auto_increment,
  `IdLocal` smallint(6) NOT NULL DEFAULT '0',
  `IdAlmacenRecepcion` smallint(5) unsigned NOT NULL default '0',
  `IdUsuario` smallint(5) unsigned NOT NULL DEFAULT '0',
  `IdOrdenCompra` bigint(20) NOT NULL DEFAULT '0',
  `IdMoneda` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `IncluyeImpuesto` tinyint(1) unsigned NOT NULL default '0',
  `Impuesto` double NOT NULL default '0',
  `Percepcion` double NOT NULL default '0' comment 'Impuesto',
  `CambioMoneda` DOUBLE NOT NULL DEFAULT '1',
  `FechaCambioMoneda` DATE NOT NULL DEFAULT '0000-00-00',
  `Status` tinyint(3) unsigned NOT NULL default '0',
  `FechaPeticion` date NOT NULL default '0000-00-00',
  `FechaRecepcion` datetime NOT NULL default '0000-00-00',
  `TipoOperacion` enum('Compra','TrasLocal','MetaProducto') NOT NULL DEFAULT 'Compra',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPedido`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_perfiles_usuario` (
  `IdPerfil` smallint(5) unsigned NOT NULL auto_increment,
  `NombrePerfil` tinytext NOT NULL,
  `Administracion` tinyint(1) unsigned NOT NULL default '0',
  `InformeLocal` tinyint(1) unsigned NOT NULL default '0',
  `Informes` tinyint(1) unsigned NOT NULL default '0',
  `Productos` tinyint(1) unsigned NOT NULL default '0',
  `Proveedores` tinyint(1) unsigned NOT NULL default '0',
  `Compras` tinyint(1) unsigned NOT NULL default '0',
  `Stocks` tinyint(1) unsigned NOT NULL default '0',
  `VerStocks` tinyint(1) unsigned NOT NULL default '0',
  `Clientes` tinyint(1) unsigned NOT NULL default '0',
  `TPV` tinyint(1) unsigned NOT NULL default '0',
  `Precios` tinyint(1) NOT NULL DEFAULT '0',
  `Ventas` tinyint(1) NOT NULL DEFAULT '0',
  `Finanzas` tinyint(1) NOT NULL DEFAULT '0',
  `Cobros` tinyint(1) NOT NULL DEFAULT '0',
  `Pagos` tinyint(1) NOT NULL DEFAULT '0',
  `CajaGeneral` tinyint(1) NOT NULL DEFAULT '0',
  `Presupuestos` tinyint(1) NOT NULL DEFAULT '0',
  `ComprobantesCompra` tinyint(1) NOT NULL DEFAULT '0',
  `ComprobantesVenta` tinyint(1) NOT NULL DEFAULT '0',
  `Promociones` tinyint(1) NOT NULL DEFAULT '0',
  `Kardex` tinyint(1) NOT NULL DEFAULT '0',
  `Ajustes` tinyint(1) NOT NULL DEFAULT '0',
  `VerAjustes` tinyint(1) NOT NULL DEFAULT '0',
  `Almacen` tinyint(1) NOT NULL DEFAULT '0',
  `CajaTPV` tinyint(1) NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPerfil`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_productos` (
  `IdProducto` bigint(20) unsigned NOT NULL auto_increment,
  `IdMarca` smallint(5) unsigned NOT NULL default '0',
  `IdContenedor` smallint(5) unsigned NOT NULL default '0',
  `IdProdBase` bigint(20) unsigned NOT NULL default '0',
  `Referencia` tinytext NOT NULL,
  `Imagen` tinytext NOT NULL,
  `CodigoBarras` tinytext NOT NULL,
  `Costo` double NOT NULL default '0',
  `Impuesto` double NOT NULL default '0',
  `StockIlimitado` tinyint(1) unsigned NOT NULL default '0',
  `RefProvHab` tinytext NOT NULL COMMENT 'Registro Sanitario',
  `IdProvHab` smallint(5) unsigned NOT NULL default '0',
  `IdLabHab` smallint(5) unsigned NOT NULL DEFAULT '1',
  `IdTallaje` smallint(5) unsigned NOT NULL default '0',
  `IdTalla` smallint(5) unsigned NOT NULL default '0',
  `Servicio` tinyint(1) unsigned NOT NULL default '0',
  `IdColor` smallint(5) unsigned NOT NULL default '0',
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `IdSubFamilia` smallint(5) unsigned NOT NULL default '0',
  `IdProductoAlias0` smallint(5) unsigned NOT NULL default '0',
  `IdProductoAlias1` smallint(5) unsigned NOT NULL default '0',
  `Serie` tinyint(1) unsigned NOT NULL default '0',
  `MetaProducto` tinyint(1) unsigned NOT NULL default '0',
  `Lote` tinyint(1) unsigned NOT NULL default '0',
  `FechaVencimiento` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `VentaMenudeo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `UnidadesPorContenedor` bigint(20) unsigned NOT NULL DEFAULT '0',
  `UnidadMedida` ENUM('und','mts','lts','kls') NOT NULL DEFAULT 'und',
  `Obsoleto` tinyint(4) NOT NULL default '0',
  `CondicionVenta` enum('0','CRM','CRMR') NOT NULL DEFAULT '0' COMMENT '0:sin receta, CRM:con receta, CRMR:con reseta retenida',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdProducto`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_productos_idioma` (
  `IdProdIdioma` bigint(20) unsigned NOT NULL auto_increment,
  `IdProdBase` smallint(5) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `Nombre` tinytext NOT NULL,
  `Alias1` tinytext NOT NULL,
  `Alias2` tinytext NOT NULL,
  `Descripcion` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdProdIdioma`),
  KEY `IdProducto` (`IdProdBase`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_proveedores` (
  `IdProveedor` bigint(20) unsigned NOT NULL auto_increment,
  `NombreComercial` tinytext NOT NULL,
  `NombreLegal` tinytext NOT NULL,
  `NumeroFiscal` tinytext NOT NULL,
  `Direccion` tinytext NOT NULL,
  `CP` tinytext NOT NULL,
  `Localidad` tinytext NOT NULL,
  `IdPais` smallint(5) unsigned NOT NULL default '0',
  `Descuento` double unsigned NOT NULL default '0',
  `CuentaBancaria` tinytext NOT NULL,
  `IdModPagoHabitual` smallint(5) unsigned NOT NULL default '0',
  `Telefono1` tinytext NOT NULL,
  `Telefono2` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `PaginaWeb` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Cargo` tinytext NOT NULL,
  `Comentarios` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdProveedor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_laboratorios` (
  `IdLaboratorio` bigint(20) unsigned NOT NULL auto_increment,
  `NombreComercial` tinytext NOT NULL,
  `NombreLegal` tinytext NOT NULL,
  `NumeroFiscal` tinytext NOT NULL,
  `Direccion` tinytext NOT NULL,
  `CP` tinytext NOT NULL,
  `Localidad` tinytext NOT NULL,
  `IdPais` smallint(5) unsigned NOT NULL default '0',
  `Descuento` double unsigned NOT NULL default '0',
  `CuentaBancaria` tinytext NOT NULL,
  `IdModPagoHabitual` smallint(5) unsigned NOT NULL default '0',
  `Telefono1` tinytext NOT NULL,
  `Telefono2` tinytext NOT NULL,
  `Email` tinytext NOT NULL,
  `PaginaWeb` tinytext NOT NULL,
  `Contacto` tinytext NOT NULL,
  `Cargo` tinytext NOT NULL,
  `Comentarios` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdLaboratorio`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_comprobantesstatus` (
  `IdStatus` tinyint(3) unsigned NOT NULL auto_increment,
  `Status` tinytext NOT NULL,
  PRIMARY KEY  (`IdStatus`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_subfamilias` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `IdSubFamilia` smallint(5) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `SubFamilia` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  KEY `IdFamilia` (`IdFamilia`,`IdSubFamilia`)
) ENGINE=MyISAM   DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_tallajes` (
  `IdTallaje` int unsigned NOT NULL auto_increment,
  `Tallaje` tinytext NOT NULL,
  PRIMARY KEY  (`IdTallaje`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_tallas` (
  `Id` smallint(5) unsigned NOT NULL auto_increment,
  `IdTallaje` int unsigned NOT NULL default '0',
  `IdTalla` smallint(5) unsigned NOT NULL default '0',
  `SizeOrden` tinyint(3) unsigned NOT NULL default '0',
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `IdFamilia` smallint(5) unsigned NOT NULL default '0',
  `Talla` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  KEY `IdTalla` (`IdTalla`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_templates` (
  `IdTemplate` smallint(5) unsigned NOT NULL auto_increment,
  `Codigo` longtext NOT NULL,
  `Nombre` tinytext NOT NULL,
  `Comentario` tinytext NOT NULL,
  `Paginas` tinyint(4) NOT NULL default '0',
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdTemplate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE `ges_usuarios` (
  `IdUsuario` smallint(5) unsigned NOT NULL auto_increment,
  `Nombre` tinytext NOT NULL,
  `Identificacion` tinytext NOT NULL,
  `Password` tinytext NOT NULL,
  `IdIdioma` smallint(5) unsigned NOT NULL default '0',
  `Direccion` tinytext NOT NULL,
  `Telefono` tinytext NOT NULL,
  `Foto` tinytext NOT NULL,
  `FechaNacim` date NOT NULL default '0000-00-00',
  `Comision` double NOT NULL default '0',
  `IdPerfil` smallint(5) unsigned NOT NULL default '0',
  `IdLocal` smallint(5) unsigned NOT NULL default '0',
  `AdministradorWeb` tinyint(1) unsigned NOT NULL default '0',
  `AdministradorFacturas` int(11) NOT NULL,
  `CuentaBanco` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdUsuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE  `ges_productos_series` (
  `IdProductoSerie` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdProducto` bigint(20) unsigned NOT NULL DEFAULT '0',
  `NumeroSerie` varchar(30) NOT NULL,
  `DocumentoEntrada` bigint(20) NOT NULL COMMENT 'IdPedidoDet',
  `OperacionEntrada` enum('Compra','TrasLocal','AjusteExist','MetaProducto') NOT NULL DEFAULT 'Compra',
  `Estado` ENUM('Pedido','Almacen','Salida') NOT NULL DEFAULT 'Pedido',
  `Disponible` tinyint(1) unsigned NOT NULL default '1',
  `OperacionSalida` ENUM( 'Venta', 'TrasLocal', 'AjusteExist', 'MetaProducto' )  NOT NULL DEFAULT 'Venta',
  `DocumentoSalida` bigint(20) unsigned NOT NULL COMMENT 'IdComprobante',
  `Eliminado` int(11) NOT NULL,
  PRIMARY KEY (`IdProductoSerie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 

;;;;;;


CREATE TABLE  `ges_comprobantesprov` (
  `IdComprobanteProv` bigint(20) NOT NULL auto_increment,
  `IdUsuario` smallint(5) unsigned NOT NULL,
  `IdPedido` bigint(20) NOT NULL DEFAULT '0',
  `IdProveedor` smallint(5) unsigned NOT NULL default '0',
  `ModoPago` enum('Contado','Credito') NOT NULL DEFAULT 'Contado',
  `TipoComprobante` ENUM('Factura','Boleta','Ticket','Albaran','AlbaranInt') NOT NULL DEFAULT 'Factura' COMMENT 'Ticket es sin documento',
  `IdPedidosDetalle` text NOT NULL,
  `IdMotivoAlbaran` tinyint unsigned NOT NULL DEFAULT '0',
  `Codigo` varchar(20) NOT NULL default '',
  `FechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FechaFacturacion` date NOT NULL,
  `EstadoDocumento` ENUM('Borrador','Pendiente','Confirmado','Cancelada') NOT NULL default 'Borrador',
  `EstadoPago` ENUM('Pendiente','Empezada','Pagada','Vencida','Exonerado') NOT NULL default 'Pendiente',
  `ImporteBase` double unsigned NOT NULL,
  `ImporteImpuesto` double unsigned NOT NULL,
  `ImportePercepcion` double unsigned NOT NULL,
  `TotalImporte` double NOT NULL,
  `ImportePendiente` double NOT NULL,
  `FechaPago` date NOT NULL default '0000-00-00',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdComprobanteProv`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='facturas de compras a proveedores';

;;;;;;

CREATE TABLE `ges_pagosprov` (
  `IdPagoProv` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdComprobanteProv` bigint(20) unsigned NOT NULL default '0',
  `IdUsuario` int(2) unsigned NOT NULL default '0',
  `IdPagoProvDoc` bigint(20) unsigned NOT NULL default '0',
  `IdMoneda` int(3) unsigned NOT NULL default '0',
  `Descripcion` tinytext NOT NULL,
  `FechaRegistro` TIMESTAMP NOT  NULL  DEFAULT CURRENT_TIMESTAMP,
  `FechaPago` date NOT NULL DEFAULT '0000-00-00',
  `Importe` double NOT NULL default '0',
  `Mora` double NOT NULL DEFAULT '0',
  `Excedente` double NOT NULL DEFAULT '0',
  `ValuacionMoneda` double NOT NULL DEFAULT '0',
  `Estado` ENUM('Pendiente','Confirmado') NOT NULL DEFAULT 'Pendiente',
  `EstadoCuota` ENUM('Pendiente','Vencido') NOT NULL DEFAULT 'Pendiente',
  `esPlanificado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Observaciones` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`IdPagoProv`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Pagos de facturas a proveedores';

;;;;;;

CREATE TABLE `ges_kardex` (
      `IdKardex` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `IdProducto` bigint(20) unsigned NOT NULL,
      `FechaMovimiento` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `IdKardexOperacion` tinyint unsigned NOT NULL DEFAULT '1',
      `IdKardexAjusteOperacion` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
      `TipoMovimiento` ENUM('Entrada', 'Salida') NOT NULL DEFAULT 'Entrada',
      `CantidadMovimiento` float NOT NULL,
      `CostoUnitarioMovimiento` double NOT NULL,
      `CostoTotalMovimiento` double NOT NULL,
      `SaldoCantidad` float NOT NULL,
      `IdPedidoDet` bigint(20) unsigned NOT NULL,
      `IdLocal` smallint(5) unsigned NOT NULL,
      `IdUsuario` smallint(5) unsigned NOT NULL,
      `IdInventario` bigint(20) unsigned NOT NULL DEFAULT '0',
      `IdComprobanteDet` bigint(20) unsigned NOT NULL default '0',
      `IdInventarioAjuste` bigint(20) unsigned NOT NULL DEFAULT '0',
      `Observaciones` tinytext NOT NULL,
      `Estado` tinyint(1) unsigned NOT NULL DEFAULT '0',
      `EstadoDetalle` varchar(100) NOT NULL,
      `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
      PRIMARY KEY (`IdKardex`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_clientes_online` (
  `IdClienteOnline` bigint(10) unsigned NOT NULL auto_increment,
  `IdCliente` bigint(20) NOT NULL,
  `User` varchar(15) NOT NULL,
  `Pass` varchar(32) NOT NULL,
  `Alta` tinyint(4) NOT NULL,
  `CodigoAlta` varchar(8) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdClienteOnline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_pedidos_online` (
  `IdPedidoOnline` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdCliente` bigint(20) NOT NULL,
  `FechaPedido` datetime NOT NULL,
  `SinRegistro` tinyint(4) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPedidoOnline`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_pedidos_detalle_online` (
  `IdPedidoOnlineDet` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdPedidoOnline` bigint(20) NOT NULL,
  `IdProducto` bigint(20) NOT NULL,
  `Cantidad` float NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPedidoOnlineDet`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_pedidos_sinregistro_online` (
  `IdPedidoOnlineSinReg` bigint(20) NOT NULL AUTO_INCREMENT,
  `IdPedidoOnline` bigint(20) NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPedidoOnlineSinReg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_comprobantesnum` (
  `IdNumComprobante` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdComprobante` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IdTipoComprobante` smallint(3) NOT NULL DEFAULT '0',
  `IdMotivoAlbaran` tinyint unsigned NOT NULL DEFAULT '0',
  `NumeroComprobante` int(15) unsigned NOT NULL DEFAULT '0',
  `Fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `TipoVenta` enum('VD','VC','VL','VCM') NOT NULL DEFAULT 'VD',
  `Status` enum('Emitido','Reservado','Anulado','Facturado') NOT NULL DEFAULT 'Emitido',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdNumComprobante`),
  UNIQUE KEY `Codigo Comprobante` (`IdNumComprobante`,`IdTipoComprobante`,`NumeroComprobante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_comprobantestipo` (
  `IdTipoComprobante` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `TipoComprobante` enum('Factura','Boleta','Ticket','Albaran','AlbaranInt') NOT NULL DEFAULT 'Ticket',
  `Serie` int(3) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(5) unsigned NOT NULL,
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdTipoComprobante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_metaproductos` (
  `IdMetaProducto` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdProducto` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(5) unsigned NOT NULL DEFAULT '0',
  `IdCliente` int(11) NOT NULL DEFAULT '0',
  `IdComprobante` BIGINT(20) NOT NULL DEFAULT '0',
  `CBMetaProducto` tinytext NOT NULL,
  `TipoVentaOperacion` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `FechaRegistro` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FechaEnsamblaje` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FechaEntrega` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UsuarioAlmacen` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'IdUsuario',
  `UsuarioAAT` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'IdUsuario',
  `Costo` double NOT NULL DEFAULT '0',
  `Status` enum('Ensamblaje','Finalizado','Cancelado') NOT NULL DEFAULT 'Ensamblaje',
  `VigenciaMetaProducto` smallint(2) unsigned NOT NULL DEFAULT '0',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdMetaProducto`),
  UNIQUE KEY `Codigo Barra` (`CBMetaProducto`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_metaproductosdet` (
  `IdMetaproductoDet` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdMetaproducto` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IdProducto` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Referencia` tinytext NOT NULL,
  `CodigoBarras` tinytext NOT NULL,
  `Concepto` tinytext NOT NULL,
  `Talla` tinytext NOT NULL,
  `Color` tinytext NOT NULL,
  `Cantidad` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Costo` double NOT NULL DEFAULT '0',
  `Importe` double NOT NULL DEFAULT '0',
  `Lote` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con Lote(1), sin Lote(0)',
  `Vencimiento` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con Fecha Vencimiento(1), sin Fecha Vencimiento(0)',
  `Serie` tinyint(1) unsigned NOT NULL default '0' COMMENT 'con serie(1), sin serie(0)',
  `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdMetaproductoDet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_comprobantesformato` (
  `IdComprobanteFormato` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Formato` tinytext NOT NULL,
  `Medida` tinytext NOT NULL,
  `TipoComprobante` enum('Factura','Boleta','Ticket') NOT NULL DEFAULT 'Boleta',
  `Status` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdComprobanteFormato`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_presupuestos` (
  `IdPresupuesto` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdLocal` smallint(6) NOT NULL DEFAULT '0',
  `IdUsuario` smallint(6) NOT NULL DEFAULT '0',
  `Serie` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
  `NPresupuesto` bigint(20) unsigned NOT NULL DEFAULT '0',
  `TipoPresupuesto` enum('Proforma','Preventa') NOT NULL DEFAULT 'Preventa',
  `TipoVentaOperacion` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `FechaPresupuesto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FechaAtencion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IdCliente` int(11) NOT NULL DEFAULT '0',
  `ImporteNeto` double NOT NULL DEFAULT '0',
  `ImporteImpuesto` double NOT NULL DEFAULT '0',
  `TotalImporte` double NOT NULL DEFAULT '0',
  `Impuesto` double NOT NULL DEFAULT '0',
  `Status` enum('Pendiente','Confirmado','Modificado','Cancelado','Vencido') NOT NULL DEFAULT 'Pendiente',
  `IdCP` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'IdPresupuesto o IdComprobante por cambio de Status a Confirmado o Modificado',
  `ModoTPV` enum('venta','cesion','pedidos') NOT NULL DEFAULT 'pedidos',
  `VigenciaPresupuesto` smallint(2) unsigned NOT NULL DEFAULT '0',
  `CBMetaProducto` tinytext NOT NULL,
  `Observaciones` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPresupuesto`),
  UNIQUE KEY `Documento Presupuesto` (`IdLocal`,`Serie`,`NPresupuesto`,`TipoPresupuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_presupuestosdet` (
  `IdPresupuestoDet` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdPresupuesto` int(10) unsigned NOT NULL DEFAULT '0',
  `IdProducto` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Referencia` tinytext NOT NULL,
  `CodigoBarras` tinytext NOT NULL,
  `Concepto` tinytext NOT NULL,
  `Talla` tinytext NOT NULL,
  `Color` tinytext NOT NULL,
  `Cantidad` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Precio` double NOT NULL DEFAULT '0',
  `Descuento` double NOT NULL DEFAULT '0',
  `Importe` double NOT NULL DEFAULT '0',
  `Impuesto` double NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPresupuestoDet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_ordencompra` (
  `IdOrdenCompra` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdLocal` smallint(6) NOT NULL DEFAULT '0',
  `IdUsuario` smallint(6) NOT NULL DEFAULT '0',
  `IdProveedor` bigint(20) NOT NULL,
  `IdMoneda` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `CodOrdenCompra` bigint(20) NOT NULL DEFAULT '0',
  `Estado` enum('Borrador','Pendiente','Pedido','Recibido','Cancelado') NOT NULL DEFAULT 'Borrador',
  `FechaRegistro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `FechaPedido` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaPrevista` date NOT NULL default '0000-00-00',
  `FechaRecibido` datetime NOT NULL default '0000-00-00 00:00:00',
  `FechaPago` date NOT NULL default '0000-00-00',
  `CambioMoneda` DOUBLE NOT NULL DEFAULT '1',
  `FechaCambioMoneda` DATE NOT NULL DEFAULT '0000-00-00',
  `Impuesto` DOUBLE NOT NULL DEFAULT '0',
  `Importe` double NOT NULL DEFAULT '0',
  `ModoPago` enum('Contado','Credito') NOT NULL DEFAULT 'Contado',
  `Observaciones` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdOrdenCompra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_ordencompradet` (
  `IdOrdenCompraDet` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdOrdenCompra` bigint(20) NOT NULL DEFAULT '0',
  `IdProducto` bigint(20) unsigned NOT NULL default '0',
  `Unidades` float NOT NULL DEFAULT '0',  
  `Costo` double unsigned NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdOrdenCompraDet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_moneda` (
  `IdMoneda` int(3) NOT NULL AUTO_INCREMENT,
  `Moneda` tinytext NOT NULL,
  `MonedaEnPlural` tinytext NOT NULL,
  `Simbolo` char(5) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdMoneda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_pagosprovdoc` (
  `IdPagoProvDoc` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdModalidadPago` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `IdOrdenCompra` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IdProveedor` bigint(20) unsigned NOT NULL DEFAULT '0',
  `IdUsuario` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdMoneda` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `TipoProveedor` ENUM('Externo','Interno') NOT NULL DEFAULT 'Externo',
  `Codigo` varchar(20) NOT NULL,
  `FechaRegistro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `FechaOperacion` datetime NOT NULL default '0000-00-00 00:00:00',
  `Estado` ENUM('Borrador','Pendiente','Confirmado','Cancelado') NOT NULL DEFAULT 'Pendiente',
  `Importe` double NOT NULL Default '0',
  `CambioMoneda` DOUBLE NOT NULL DEFAULT '1',
  `EntidadFinanciera` Varchar(30) NOT NULL,
  `CodOperacion` tinytext NOT NULL,
  `CtaEmpresa` tinytext NOT NULL,
  `CtaProveedor` tinytext NOT NULL,
  `Documento` tinytext NOT NULL,
  `NumDocumento` tinytext NOT NULL,
  `Observaciones` text NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPagoProvDoc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_inventario` (
  `IdInventario` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IdUsuario` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `FechaInventarioInicio` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FechaInventarioFin` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Inventario` ENUM('Inicial','Periodico','Final','Intermitente') NOT NULL DEFAULT 'Periodico',
  `IdPedido` BIGINT(20) NOT NULL DEFAULT '0',
  `IdComprobante` BIGINT(20) NOT NULL DEFAULT '0',
  `Estado` ENUM('Pendiente','Finalizado') NOT NULL DEFAULT 'Pendiente',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdInventario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_kardexoperacion` (
  `IdKardexOperacion` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `KardexOperacion` tinytext NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdKardexOperacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_motivoalbaran` (
  `IdMotivoAlbaran` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `MotivoAlbaran` tinytext NOT NULL,
  `Almacen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Compras` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Ventas` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdMotivoAlbaran`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_partidascaja` (
  `IdPartidaCaja` int unsigned NOT NULL AUTO_INCREMENT,
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `TipoCaja` ENUM('VD','VC','CG') NOT NULL DEFAULT 'CG',
  `PartidaCaja` tinytext NOT NULL,
  `TipoOperacion` enum('Aportacion','Sustraccion','Ingreso','Gasto','Egreso') NOT NULL DEFAULT 'Egreso',
  `FechaRegistro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPartidaCaja`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_productosinformacion` (
  `IdProductoInformacion` bigint(20) unsigned NOT NULL auto_increment,
  `IdProducto` bigint(20) unsigned NOT NULL default '0',
  `Indicacion` varchar(300) NOT NULL,
  `ContraIndicacion` varchar(300) NOT NULL,
  `Interaccion` varchar(300) NOT NULL,
  `Dosificacion` varchar(300) NOT NULL,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdProductoInformacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_productosborrador` (
  `IdProductoBorrador` bigint(20) unsigned NOT NULL auto_increment,
  `IdUsuario` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `ProductoBorrador` varchar(300) NOT NULL,
  `FechaRegistro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdProductoBorrador`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_promociones`(
  `IdPromocion` INT unsigned NOT NULL auto_increment,
  `IdUsuario` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdPromocionCliente` smallint(6) unsigned NOT NULL DEFAULT '0',
  `Descripcion` varchar(300) NOT NULL,
  `FechaRegistro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FechaInicio` DATE NOT NULL DEFAULT '0000-00-00',
  `FechaFin` DATE NOT NULL DEFAULT '0000-00-00',
  `Estado` ENUM('Borrador','Ejecucion','Finalizado','Suspendido','Cancelado') NOT NULL DEFAULT 'Borrador',
  `Modalidad` ENUM('MontoCompra','HistorialCompra') NOT NULL DEFAULT 'MontoCompra',
  `MontoCompraActual` double NOT NULL Default '0',
  `Tipo` ENUM('Descuento','Producto','Bono') NOT NULL DEFAULT 'Descuento',
  `CBProducto0` bigint(20) unsigned NOT NULL default '0',
  `CBProducto1` bigint(20) unsigned NOT NULL default '0',
  `Descuento` double NOT NULL Default '0',
  `Bono` double NOT NULL Default '0',
  `Prioridad` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT  '0:Ninguno, 1:Baja, 2:Media, 3:Alta',
  `TipoVenta` enum('VD','VC') NOT NULL DEFAULT 'VD',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPromocion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_promocionclientes` (
  `IdPromocionCliente` INT unsigned NOT NULL auto_increment,
  `IdUsuario` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdLocal` smallint(6) unsigned NOT NULL DEFAULT '0',
  `IdHistorialVentaPeriodo` smallint unsigned NOT NULL DEFAULT '0',
  `FechaRegistro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CategoriaCliente` varchar(300) NOT NULL,
  `Descripcion` varchar(300) NOT NULL,
  `Estado` ENUM('Borrador','Ejecucion','Finalizado') NOT NULL DEFAULT 'Borrador',
  `DesdeMontoCompra` double NOT NULL Default '0',
  `HastaMontoCompra` double NOT NULL Default '0',
  `DesdeNumeroCompra` smallint(6) NOT NULL Default '0',
  `HastaNumeroCompra` smallint(6) NOT NULL Default '0',
  `MotivoPromocion` ENUM('MontoCompra','NumeroCompra','Ambos') NOT NULL DEFAULT 'Ambos',
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPromocionCliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_historialventaperiodo` (
  `IdHistorialVentaPeriodo` smallint unsigned NOT NULL auto_increment,
  `Periodo` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'En Meses, 0 es Inicio desde los tiempos',
  `HistorialVentaPeriodo` varchar(30) NOT NULL,
  `FechaRegistro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdHistorialVentaPeriodo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;

CREATE TABLE IF NOT EXISTS `ges_historialventas` (
  `IdHistorialVenta` bigint(20) unsigned NOT NULL auto_increment,
  `IdCliente` bigint(20) NOT NULL default '0',
  `IdHistorialVentaPeriodo` smallint unsigned NOT NULL DEFAULT '0',
  `MontoCompra` double NOT NULL Default '0',
  `NumeroCompra` smallint(6) NOT NULL Default '0',
  `FechaActualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Eliminado` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdHistorialVenta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

;;;;;;
