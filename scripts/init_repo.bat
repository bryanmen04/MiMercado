@echo off
setlocal

REM Remote por defecto (reemplaza si quieres otro)
set DEFAULT_REMOTE=https://github.com/bryanmen04/MiMercado.git

REM Parámetros: 1=remote(optional) 2=commit message(optional)
if "%~1"=="" (
  set REMOTE=%DEFAULT_REMOTE%
) else (
  set REMOTE=%~1
)
set CMMSG=%~2
if "%CMMSG%"=="" set CMMSG=Initial commit: plantilla

REM Ir a la raíz del proyecto (asume script en scripts\)
cd /d "%~dp0\.."
echo Working dir: %CD%

REM Eliminar historial anterior si existe (OPCIONAL)
if exist ".git" (
  echo Eliminando .git viejo...
  rmdir /s /q .git
)

echo Inicializando repo git...
git init
if errorlevel 1 (
  echo Error: git init falló.
  pause
  exit /b 1
)

echo Agregando archivos a staging...
git add .

echo Creando commit inicial...
git commit -m "%CMMSG%"

echo Configurando remote origin -> %REMOTE%
git remote add origin %REMOTE%

echo Estableciendo rama master y push...
git branch -M master
git push -u origin master

echo Creando y push de develop...
git checkout -b develop
git push -u origin develop

echo Operación finalizada. Revisa el repositorio remoto en %REMOTE%
endlocal
