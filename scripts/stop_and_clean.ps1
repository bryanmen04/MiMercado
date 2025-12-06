```powershell
# Detener proceso, limpiar y rebuild para proyecto dotnet

$projectPath = Join-Path $PSScriptRoot "..\dotnet"
if (-not (Test-Path $projectPath)) {
    Write-Error "No se encontro la carpeta dotnet en $projectPath"
    exit 1
}

Write-Host "Buscando procesos MiMercado.Api..."
$procs = Get-Process -ErrorAction SilentlyContinue | Where-Object { $_.ProcessName -match "MiMercado.Api" -or $_.Name -match "MiMercado.Api" }
if ($procs) {
    Write-Host "Deteniendo procesos..."
    $procs | ForEach-Object { Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue }
    Start-Sleep -Seconds 1
} else {
    Write-Host "No se encontraron procesos corriendo."
}

Write-Host "dotnet clean ..."
dotnet clean $projectPath

Write-Host "Eliminando bin y obj ..."
Remove-Item -LiteralPath (Join-Path $projectPath "bin") -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -LiteralPath (Join-Path $projectPath "obj") -Recurse -Force -ErrorAction SilentlyContinue

Write-Host "dotnet build ..."
dotnet build $projectPath

Write-Host "Listo."
```