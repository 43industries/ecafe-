# One-time Railway database initialization.
# Requires: railway CLI logged in and linked to the project.
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Sql = Join-Path $Root "database\railway-init.sql"

if (-not (Get-Command railway -ErrorAction SilentlyContinue)) {
    Write-Host "Install Railway CLI: npm install -g @railway/cli" -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $Sql)) {
    Write-Host "Missing $Sql" -ForegroundColor Red
    exit 1
}

Write-Host "Importing schema and seed into Railway MySQL..." -ForegroundColor Cyan
cmd /c "railway connect mysql < `"$Sql`""
if ($LASTEXITCODE -ne 0) {
    Write-Host "Database import failed. Run: railway login && railway link" -ForegroundColor Red
    exit $LASTEXITCODE
}
Write-Host "Done. Test login: STU001 / Password123!" -ForegroundColor Green
