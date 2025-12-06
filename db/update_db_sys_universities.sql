USE db_sys_universities;

-- 1) Catálogos
CREATE TABLE IF NOT EXISTS CatCategorias (
    eCodCategoria INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(100) NOT NULL,
    tDescripcion TEXT,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS CatEmpresas (
    eCodEmpresa INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tRazonSocial VARCHAR(200),
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS CatPerfiles (
    eCodPerfil INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(80) NOT NULL,
    tDescripcion VARCHAR(255),
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Entidades principales (no se borran datos existentes)
CREATE TABLE IF NOT EXISTS TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT DEFAULT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_producto_categoria (eCodCategoria),
    CONSTRAINT fk_producto_categoria FOREIGN KEY (eCodCategoria) REFERENCES CatCategorias(eCodCategoria) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS TblUsuarios (
    eCodUsuario INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tCorreo VARCHAR(150),
    tUsuario VARCHAR(80) UNIQUE,
    tPassword VARCHAR(255),
    eCodEmpresa INT DEFAULT NULL,
    eCodPerfil INT DEFAULT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_empresa (eCodEmpresa),
    CONSTRAINT fk_usuario_empresa FOREIGN KEY (eCodEmpresa) REFERENCES CatEmpresas(eCodEmpresa) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_usuario_perfil FOREIGN KEY (eCodPerfil) REFERENCES CatPerfiles(eCodPerfil) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS TblClientes (
    eCodCliente INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tCorreo VARCHAR(150),
    tTelefono VARCHAR(50),
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS TblVentas (
    eCodVenta INT AUTO_INCREMENT PRIMARY KEY,
    eCodCliente INT DEFAULT NULL,
    eCodUsuario INT DEFAULT NULL,
    dFecha DATE NOT NULL,
    dTotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_venta_fecha (dFecha),
    CONSTRAINT fk_venta_cliente FOREIGN KEY (eCodCliente) REFERENCES TblClientes(eCodCliente) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_venta_usuario FOREIGN KEY (eCodUsuario) REFERENCES TblUsuarios(eCodUsuario) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS TblVentaDetalle (
    eCodVentaDetalle INT AUTO_INCREMENT PRIMARY KEY,
    eCodVenta INT NOT NULL,
    eCodProducto INT NOT NULL,
    eCantidad INT NOT NULL DEFAULT 1,
    dPrecioUnitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    dTotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_vd_venta FOREIGN KEY (eCodVenta) REFERENCES TblVentas(eCodVenta) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vd_producto FOREIGN KEY (eCodProducto) REFERENCES TblProductos(eCodProducto) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Índices adicionales (no fallarán si ya existen)
-- MySQL < 8 no soporta CREATE INDEX IF NOT EXISTS, así que usamos DROP/CREATE con cuidado:
-- (Opcional) comprueba manualmente en phpMyAdmin si necesitas índices extra.

-- 4) Datos de ejemplo (solo si lo deseas): insertará si no existe ya registro con mismo nombre
INSERT INTO CatCategorias (tNombre, tDescripcion)
SELECT 'Lácteos','Productos lácteos' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM CatCategorias WHERE tNombre = 'Lácteos');

INSERT INTO CatCategorias (tNombre, tDescripcion)
SELECT 'Limpieza','Productos limpieza' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM CatCategorias WHERE tNombre = 'Limpieza');

INSERT INTO CatEmpresas (tNombre)
SELECT 'MiMercado S.A.' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM CatEmpresas WHERE tNombre = 'MiMercado S.A.');

INSERT INTO CatPerfiles (tNombre)
SELECT 'Admin' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM CatPerfiles WHERE tNombre = 'Admin');

INSERT INTO CatPerfiles (tNombre)
SELECT 'Vendedor' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM CatPerfiles WHERE tNombre = 'Vendedor');

-- FIN
