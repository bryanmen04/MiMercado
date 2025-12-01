@echo off
setlocal

REM Ajusta estas variables si lo necesitas
set MYSQLDUMP="C:\AppServ\MySQL\bin\mysqldump.exe"
set DBNAME=db_sys_universities
set OUTSQL="C:\AppServ\www\Mimercado\db\db_sys_universities_dump.sql"
set PDF="C:\AppServ\www\Mimercado\docs\BD_Resumen.pdf"
set ZIP="C:\AppServ\www\Mimercado\submission\GradoyGrupo_NombreProyecto_API_ASPNetCore_BD_Actualizada.zip"

mkdir "C:\AppServ\www\Mimercado\submission" 2>nul

echo Generando dump de la base de datos (%DBNAME%)...
%MYSQLDUMP% -u root -p %DBNAME% > %OUTSQL%
if errorlevel 1 (
    echo Error generando dump. Verifica usuario/contraseña y ruta de mysqldump.
    pause
    exit /b 1
)

echo Comprimiendo archivos...
powershell -Command "Compress-Archive -LiteralPath %OUTSQL%,%PDF% -DestinationPath %ZIP% -Force"

if exist %ZIP% (
    echo Archivo de entrega creado: %ZIP%
) else (
    echo Fallo al crear el ZIP.
)

endlocal
