-- Primero eliminamos los procedimientos almacenados
DROP PROCEDURE IF EXISTS stpConsultarProductos;
DROP PROCEDURE IF EXISTS stpConsultarVentas;
DROP PROCEDURE IF EXISTS stpInsertarProducto;
DROP PROCEDURE IF EXISTS stpInsertarVenta;

-- Luego eliminamos las tablas en orden correcto debido a las dependencias
DROP TABLE IF EXISTS BitVentas;
DROP TABLE IF EXISTS RelProductosCategorias;
DROP TABLE IF EXISTS CatProductos;
DROP TABLE IF EXISTS CatCategorias;

-- Ahora ejecuta el script db_structure.sql completo