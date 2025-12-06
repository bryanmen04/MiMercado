USE db_sys_universities;

-- Crear tabla productos
CREATE TABLE TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Crear tabla ventas
CREATE TABLE TblVentas (
    eCodVenta INT AUTO_INCREMENT PRIMARY KEY,
    eCodProducto INT NOT NULL,
    eCantidad INT NOT NULL,
    dTotal DECIMAL(12,2) NOT NULL,
    dFecha DATE NOT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Agregar la llave foránea
ALTER TABLE TblVentas
ADD CONSTRAINT fk_venta_producto
FOREIGN KEY (eCodProducto) REFERENCES TblProductos(eCodProducto);
