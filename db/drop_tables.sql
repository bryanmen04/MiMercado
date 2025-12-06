USE db_sys_universities;

-- Desactivar restricciones de llave foránea
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas si existen (en orden correcto)
DROP TABLE IF EXISTS TblVentas;
DROP TABLE IF EXISTS TblProductos;

-- Reactivar restricciones
SET FOREIGN_KEY_CHECKS = 1;
