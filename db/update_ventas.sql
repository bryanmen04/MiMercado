-- Actualizar la llave foránea en TblVentas para que coincida con TblProductos
ALTER TABLE TblVentas DROP FOREIGN KEY TblVentas_ibfk_1;
ALTER TABLE TblVentas ADD CONSTRAINT TblVentas_ibfk_1 
    FOREIGN KEY (eCodProducto) REFERENCES TblProductos(eCodProducto) ON DELETE CASCADE;
