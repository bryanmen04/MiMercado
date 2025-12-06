# MiMercado - API (avance 50%)

Resumen
- Avance: CRUD completo para entidad "Productos" y endpoints de consulta.
- DB usada: `db_sys_universities` (ver carpeta db/).

Requisitos
- .NET SDK 6/7 (si se implementa la API en .NET)
- MySQL (db_sys_universities)
- PHP/CodeIgniter (si usas partes del repo actual)
- Postman o Thunder Client para pruebas

Configuración y build (.NET)

IMPORTANTE: ejecuta los comandos dentro de la carpeta que contiene el proyecto .NET (la que tiene el archivo *.csproj). En este repositorio hemos usado la subcarpeta `dotnet/`.

1. Comprobar si existe el proyecto:
   - En Git Bash:
     ```
     ls dotnet/*.csproj
     ```
   - En CMD/PowerShell:
     ```
     dir dotnet\*.csproj
     ```
   - Si ves un archivo `.csproj`, ve al paso 3.

 
2. Si Nexiste un proyecto .NET aún, crea uno (opción rápida):
   - Crear un Web API nuevo en la subcarpeta `dotnet`:
     ```
     dotnet new webapi -o dotnet --no-https
     ```
   - Esto genera `dotnet/MiMercado.Api.csproj` y la estructura básica (Controllers/, Properties/, etc.). Luego copia/añade tus controladores, modelos y servicios dentro de `dotnet/`.

3. Restaurar, compilar y ejecutar (desde la raíz del repo):
   ```
   cd dotnet
   dotnet restore
   dotnet build
   dotnet run
   ```
   - Si prefieres, compilar sin ejecutar: `dotnet build`.
   - Si `dotnet build` devuelve MSB1003 nuevamente, confirma que estás dentro de la carpeta `dotnet` y que existe un archivo `.csproj`.

4. Ajustar connection string:
   - Edita `dotnet/appsettings.json` o define la variable de entorno:
     ```
     setx ASPNETCORE_ConnectionStrings__Default "Server=localhost;Database=db_sys_universities;User=root;Password=TU_PASS;"
     ```
   - O modifica `dotnet/appsettings.json` manualmente.

5. Abrir Swagger (cuando corra la app):
   - Por defecto: `http://localhost:5000/swagger` o la URL/puerto que muestre `dotnet run`.

Endpoints implementados (ejemplo mínimo entregado)
- GET /api/products
- GET /api/products/{id}
- POST /api/products
- PUT /api/products/{id}
- DELETE /api/products/{id}

Formato de respuestas
- JSON estructurado: { success: bool, data: object|array, message?: string }

Swagger
- Swagger UI documenta cada endpoint (summary, tags, ejemplos). Usa la UI para generar las capturas requeridas.

Pruebas
- Importar `docs/postman_collection.json` en Postman y ejecutar la colección contra la API corriendo localmente.

Notas finales
- No subir secrets reales. Usar placeholders o variables de entorno.
- Para entrega: generar ZIP con los archivos listados en `scripts/create_submission.bat`.
