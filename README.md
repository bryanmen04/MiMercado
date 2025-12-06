# MiMercado

Repositorio del proyecto MiMercado — avance 50% API ASP.NET Core.

Repositorio: https://github.com/bryanmen04/MiMercado.git

Contenido principal:
- dotnet/                -> código fuente ASP.NET Core (API)
- db/                    -> scripts SQL (create/update + dump)
- docs/                  -> documentación y colección Postman
- README_API.md          -> instrucciones para ejecutar la API localmente

Pasos rápidos para ejecutar local:
1. Ajustar connection string en dotnet/appsettings.json (o usar variable de entorno).
2. cd dotnet && dotnet restore && dotnet build && dotnet run --urls "http://localhost:5000"
3. Abrir Swagger: http://localhost:5000

Entrega:
- ZIP con el dump SQL, PDF de avance, Postman collection y link al repo (ver scripts/create_submission.bat).

## Ramas requeridas
- master (entregas formales)
- develop (trabajo diario)

## Inicializar y subir (si partiste de una copia local)
1. Desde la raíz del proyecto (c:\AppServ\www\Mimercado):
   - Elimina historial anterior (si clonaste plantilla):
     - Windows PowerShell: `Remove-Item -Recurse -Force .git`
   - Ejecuta el script (automatiza init/commit/push):
     - `.\scripts\init_repo.bat`
   - O manual:
     - `git init`
     - `git add .`
     - `git commit -m "Initial commit: plantilla MiMercado"`
     - `git remote add origin git@github.com:bryanmen04/MiMercado.git`
     - `git branch -M master`
     - `git push -u origin master`
     - `git checkout -b develop`
     - `git push -u origin develop`

## Buenas prácticas
- Trabajar en `develop` o en ramas `feature/*` basadas en `develop`.
- Mensajes de commit claros (ej: `feat:`, `fix:`, `chore:`).
- No subir secrets (configuración en `application/config/*` o archivos .env). Añade valores placeholder.
- Mantener `README.md` y `docs/BD_Resumen.pdf` actualizados.

## Entrega BD
- Incluir `db_sys_universities_dump.sql` (dump final) y `BD_Resumen.pdf` en ZIP de entrega.

