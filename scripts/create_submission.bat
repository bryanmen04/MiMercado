@echo off
setlocal

REM Ajusta nombre y rutas según tu equipo
set ROOT=%~dp0..
set ZIPPATH=%ROOT%\submission\GradoyGrupo_MiMercado_API_ASPNetCore_Avance50.zip
mkdir "%ROOT%\submission" 2>nul

REM Archivos a incluir (ajusta si tus archivos están en otras rutas)
set FILES=%ROOT%\db\db_sys_universities_dump.sql %ROOT%\README_API.md %ROOT%\docs\Avance50.md %ROOT%\docs\postman_collection.json %ROOT%\repo_link.txt %ROOT%\dotnet

echo Generando ZIP en %ZIPPATH% ...
powershell -Command "Compress-Archive -LiteralPath %FILES% -DestinationPath '%ZIPPATH%' -Force"

if exist "%ZIPPATH%" (
  echo ZIP creado: %ZIPPATH%
) else (
  echo Error al crear el ZIP
)

endlocal
