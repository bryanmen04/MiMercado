ALTER TABLE productos RENAME TO TblProductos;
ALTER TABLE TblProductos 
    CHANGE COLUMN id eCodProducto INT AUTO_INCREMENT,
    CHANGE COLUMN nombre tNombre VARCHAR(150),
    CHANGE COLUMN descripcion tDescripcion TEXT,
    CHANGE COLUMN precio dPrecio DECIMAL(10,2),
    CHANGE COLUMN stock eStock INT;
