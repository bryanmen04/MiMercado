# BD Actualizada — Resumen (1–2 páginas)

Equipo: [NombreEquipo]  
Integrantes:
- [Alumno A — Matrícula]
- [Alumno B — Matrícula]
- [Alumno C — Matrícula]

Grado y Grupo: [ej. 8° Semestre — Grupo X]  
Nombre del proyecto: Mimercado (o nombre asignado)

---

## Lista de tablas (nombre + descripción breve)

- CatCategorias  
  Guarda categorías de producto (eCodCategoria, tNombre, tDescripcion, tCodEstatus).

- CatEmpresas  
  Guarda empresas/tiendas o entidades relacionadas (eCodEmpresa, tNombre, tRazonSocial).

- CatPerfiles  
  Roles o perfiles de usuario (eCodPerfil, tNombre, tDescripcion).

- TblProductos  
  Productos del catálogo: identificador, nombre, descripción, precio, stock, FK a CatCategorias, estatus.

- TblUsuarios  
  Usuarios del sistema: datos personales, login, FK a CatEmpresas y CatPerfiles, estatus.

- TblClientes  
  Clientes registrados: nombre, contacto, correo, teléfono, estatus.

- TblVentas  
  Cabecera de ventas: referencia al cliente y usuario (vendedor), fecha, total, estatus.

- TblVentaDetalle  
  Líneas de venta: referencia a TblVentas y TblProductos, cantidad, precio unitario y total.

- ci_sessions (opcional)  
  Tabla para sesiones (si se usa sesión en BD): id, ip_address, timestamp, data.

---

## Diagrama (modelo físico / ER)
- Genera el diagrama en Navicat o MySQL Workbench (Reverse Engineer).  
- Captura la imagen del diagrama y guárdala en: `docs/images/er_diagram.png`.  
- Inserta la imagen aquí o inclúyela en el PDF final:
  ```markdown
  ![ER Diagram](./images/er_diagram.png)
  ```

---

## Notas de diseño (decisiones y justificación)

- Convención de nombres  
  - Prefijos: `e` para enteros (IDs), `t` para texto, `d` para decimales/fechas.  
  - Tablas en PascalCase sin espacios (TblProductos, CatCategorias). Esto facilita mapeo en .NET y evita conflictos.

- Integridad referencial  
  - FK con ON UPDATE CASCADE. Comportamiento ON DELETE:
    - SET NULL para relaciones opcionales (p. ej. eCodUsuario en TblVentas) para mantener histórico.
    - CASCADE para dependencias fuertes (TblVentaDetalle -> TblVentas) para mantener consistencia.

- Normalización  
  - Modelo normalizado (catálogos separados). Evitamos duplicar información repetitiva.

- Rendimiento  
  - Índices en columnas de búsqueda frecuente: idx_venta_fecha (TblVentas.dFecha), idx_producto_categoria (TblProductos.eCodCategoria), idx_usuario_empresa.

- Seguridad y datos sensibles  
  - No incluir secretos en scripts. Contraseñas se deben almacenar con hash fuerte en la app (bcrypt/argon2) — el script usa SHA1 solo como seed de ejemplo; reemplazar en producción.

- Reproducibilidad  
  - El script SQL entregado crea tablas si no existen e incluye inserts condicionales para semillas. Además se incluye la tabla `ci_sessions` para quien utilice sesiones en BD.

---

## Cómo restaurar / comprobar
1. Importar desde phpMyAdmin o consola:
   - `mysql -u root -p db_sys_universities < db_sys_universities_full.sql`
2. Verificar tablas y FK:
   - `SHOW TABLES;`
   - `SELECT COUNT(*) FROM TblProductos;`
3. (Opcional) Restaurar en instancia limpia para validar el script.

---

Firma: Equipo [NombreEquipo] — Fecha: [YYYY-MM-DD]

