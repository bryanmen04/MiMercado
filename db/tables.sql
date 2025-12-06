USE db_sys_universities;

-- Desactivar restricciones de llave foránea temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar índices y llaves foráneas primero
ALTER TABLE TblVentas 
    DROP INDEX IF EXISTS idx_venta_fecha,
    DROP FOREIGN KEY IF EXISTS fk_venta_producto;

ALTER TABLE TblProductos
    DROP INDEX IF EXISTS idx_producto_estatus;

-- Eliminar tablas
DROP TABLE IF EXISTS TblVentas;
DROP TABLE IF EXISTS TblProductos;

-- Reactivar restricciones de llave foránea
SET FOREIGN_KEY_CHECKS = 1;

-- Crear tabla productos con nueva estructura
CREATE TABLE TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Crear tabla ventas con nueva estructura
CREATE TABLE TblVentas (
    eCodVenta INT AUTO_INCREMENT PRIMARY KEY,
    eCodProducto INT NOT NULL,
    eCantidad INT NOT NULL,
    dTotal DECIMAL(12,2) NOT NULL,
    dFecha DATE NOT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    CONSTRAINT fk_venta_producto
    FOREIGN KEY (eCodProducto) REFERENCES TblProductos(eCodProducto)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_producto_estatus ON TblProductos(tCodEstatus);
CREATE INDEX idx_venta_fecha ON TblVentas(dFecha);
