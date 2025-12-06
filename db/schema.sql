-- Tabla de categorías
CREATE TABLE IF NOT EXISTS `CatCategorias` (
  `eCodCategoria` INT AUTO_INCREMENT PRIMARY KEY,
  `tNombre` VARCHAR(100) NOT NULL,
  `tDescripcion` TEXT,
  `tCodEstatus` CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tNombre` VARCHAR(150) NOT NULL,
  `tDescripcion` TEXT,
  `dPrecio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `eStock` INT NOT NULL DEFAULT 0,
  `eCodCategoria` INT DEFAULT NULL,
  `tCodEstatus` CHAR(2) NOT NULL DEFAULT 'AC',
  FOREIGN KEY (eCodCategoria) REFERENCES CatCategorias(eCodCategoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `producto_id` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `fecha` DATE NOT NULL,
  `tCodEstatus` CHAR(2) NOT NULL DEFAULT 'AC',
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla para sesiones si usas sess_use_database = TRUE
CREATE TABLE IF NOT EXISTS `app_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (id),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
