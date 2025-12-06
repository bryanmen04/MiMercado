# Avance 50% - MiMercado (Equipo [NombreEquipo])

## Equipo y proyecto
- Equipo: [NombreEquipo]
- Integrantes: [Alumno A], [Alumno B], [Alumno C]
- Proyecto: MiMercado — API para gestión de productos y ventas

## Endpoints implementados vs pendientes
Implementados:
- GET /api/products
- GET /api/products/{id}
- POST /api/products
- PUT /api/products/{id}
- DELETE /api/products/{id}

Pendientes (siguientes entregas):
- Endpoints para Usuarios (auth, CRUD)
- Endpoints para Ventas / Reportes
- Tests automatizados

## Capturas
(Incluir aquí capturas de Swagger y Postman; insertar imágenes antes de exportar a PDF)
- Captura 1: Swagger UI mostrando endpoints Products
- Captura 2: Postman - POST product (201)
- Captura 3: Postman - GET list (200)

## Pruebas realizadas
- Creación, obtención, actualización y eliminación de productos contra `db_sys_universities`.
- Verificación de integridad (FK) y formatos de respuesta.

## Problemas encontrados y soluciones
- Problema: Conexión DB en entornos locales (credenciales). Solución: usar placeholders y documentar variable de entorno.
- Problema: Dependencias de librerías (.NET/Dapper). Solución: documentar versión SDK y comandos dotnet restore/build.

## Link al repo
- https://github.com/bryanmen04/MiMercado.git

Firma: [NombreEquipo] — Fecha: [YYYY-MM-DD]
