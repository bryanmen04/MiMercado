USE db_sys_universities;

-- Productos de ejemplo
INSERT INTO TblProductos (tNombre, tDescripcion, dPrecio, eStock, eCodCategoria)
SELECT 'Leche 1L','Leche entera 1 litro',20.00,100,(SELECT eCodCategoria FROM CatCategorias WHERE tNombre='Lácteos' LIMIT 1)
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM TblProductos WHERE tNombre='Leche 1L');

INSERT INTO TblProductos (tNombre, tDescripcion, dPrecio, eStock, eCodCategoria)
SELECT 'Detergente 1L','Detergente multiusos',35.00,50,(SELECT eCodCategoria FROM CatCategorias WHERE tNombre='Limpieza' LIMIT 1)
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM TblProductos WHERE tNombre='Detergente 1L');

-- Usuario ejemplo (password hashed: usar SHA1 o mejor bcrypt fuera de script)
INSERT INTO TblUsuarios (tNombre, tCorreo, tUsuario, tPassword, eCodPerfil)
SELECT 'Admin Demo', 'admin@demo.local', 'admin', SHA1('admin123'), (SELECT eCodPerfil FROM CatPerfiles WHERE tNombre='Admin' LIMIT 1)
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM TblUsuarios WHERE tUsuario='admin');

-- Cliente ejemplo
INSERT INTO TblClientes (tNombre, tCorreo, tTelefono)
SELECT 'Cliente Demo','cliente@demo.local','555-1234'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM TblClientes WHERE tCorreo='cliente@demo.local');

-- FIN
