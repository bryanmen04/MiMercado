@echo off
REM Script para detener proceso MiMercado.Api, limpiar y rebuild

REM Ir a la raíz del repo
cd /d "%~dp0\.."

REM Ir a la carpeta del proyecto dotnet
if not exist "dotnet" (
  echo No se encontro la carpeta dotnet. Ajusta el script a la ubicacion correcta.
  pause
  exit /b 1
)
cd dotnet

REM Intentar detener por nombre de imagen
echo Buscando proceso MiMercado.Api.exe ...
tasklist /fi "imagename eq MiMercado.Api.exe" | findstr /I "MiMercado.Api.exe" >nul
if %errorlevel%==0 (
  echo Deteniendo MiMercado.Api.exe...
  taskkill /F /IM MiMercado.Api.exe
  timeout /t 1 /nobreak >nul
) else (
  echo No se encontro MiMercado.Api.exe corriendo.
)

REM Limpieza y rebuild
echo Ejecutando dotnet clean...
dotnet clean

echo Eliminando carpetas bin y obj (si existen)...
rmdir /s /q bin
rmdir /s /q obj

echo Reconstruyendo proyecto...
dotnet build

echo Operacion finalizada.
pause