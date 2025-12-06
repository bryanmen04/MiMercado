USE db_sys_universities;

-- Desactivar restricciones de llave foránea
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas directamente
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS TblVentas;
DROP TABLE IF EXISTS TblProductos;

-- Reactivar restricciones
SET FOREIGN_KEY_CHECKS = 1;
