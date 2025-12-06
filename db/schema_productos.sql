-- Crear la tabla de productos con la nomenclatura correcta
CREATE TABLE IF NOT EXISTS TblProductos (
    eCodProducto INT AUTO_INCREMENT PRIMARY KEY,
    tNombre VARCHAR(150) NOT NULL,
    tDescripcion TEXT,
    dPrecio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    eStock INT NOT NULL DEFAULT 0,
    eCodCategoria INT,
    tCodEstatus CHAR(2) NOT NULL DEFAULT 'AC',
    FOREIGN KEY (eCodCategoria) REFERENCES CatCategorias(eCodCategoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
