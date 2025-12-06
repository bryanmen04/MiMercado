-- Script SQL de ejemplo para "Actividad 1" (BD actualizada)
-- IMPORTANTE: reemplaza `utma_equipoX` por el nombre de base de datos de tu equipo o usa la BD que prefieras.
-- Ejecución: crear la BD (si es necesario) y luego importar este script.

CREATE DATABASE IF NOT EXISTS `utma_equipoX` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `utma_equipoX`;

-- ------------------------------------------------------------------
-- Tablas de catálogos
-- ------------------------------------------------------------------

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
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS CatPerfiles (
    eCodPerfil INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(80) NOT NULL,
    tDescripcion VARCHAR(255),
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------------
-- Tablas principales
-- ------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT DEFAULT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
    eCodUsuario INT DEFAULT NULL, -- vendedor/usuario que registró la venta
    dFecha DATE NOT NULL,
    dTotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    fhCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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

-- ------------------------------------------------------------------
-- Índices útiles
-- ------------------------------------------------------------------
CREATE INDEX idx_producto_categoria ON TblProductos(eCodCategoria);
CREATE INDEX idx_venta_fecha ON TblVentas(dFecha);
CREATE INDEX idx_usuario_empresa ON TblUsuarios(eCodEmpresa);

-- ------------------------------------------------------------------
-- Datos de ejemplo (opcionales) - eliminar/ajustar para producción
-- ------------------------------------------------------------------
INSERT INTO CatCategorias (tNombre, tDescripcion) VALUES
('Lácteos', 'Productos lácteos'),
('Limpieza', 'Productos de limpieza');

INSERT INTO CatEmpresas (tNombre) VALUES
('MiMercado S.A.');

INSERT INTO CatPerfiles (tNombre) VALUES
('Admin'), ('Vendedor');

-- Ajusta o elimina los inserts de ejemplo según tu proyecto

-- Fin del script
