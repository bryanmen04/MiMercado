-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS mimercado;
USE mimercado;

-- Crear tabla de productos si no existe
CREATE TABLE IF NOT EXISTS TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Crear tabla de ventas si no existe
CREATE TABLE IF NOT EXISTS TblVentas (
    eCodVenta INT AUTO_INCREMENT PRIMARY KEY,
    eCodProducto INT NOT NULL,
    eCantidad INT NOT NULL,
    dTotal DECIMAL(12,2) NOT NULL,
    dFecha DATE NOT NULL,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    FOREIGN KEY (eCodProducto) REFERENCES TblProductos(eCodProducto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
