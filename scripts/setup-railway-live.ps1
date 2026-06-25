# Complete Railway backend setup: link project, init DB, print public URL.
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

if (-not (Get-Command railway -ErrorAction SilentlyContinue)) {
    Write-Host "Install Railway CLI: npm install -g @railway/cli" -ForegroundColor Red
    exit 1
}

$whoami = railway whoami 2>&1 | Out-String
if ($whoami -match "Unauthorized") {
    Write-Host "Not logged in to Railway. Run: railway login" -ForegroundColor Yellow
    exit 1
}

$status = railway status 2>&1
if ($LASTEXITCODE -ne 0 -or $status -match "No linked project") {
    Write-Host "Link this repo to your Railway project: railway link" -ForegroundColor Yellow
    exit 1
}

Write-Host "=== Railway backend setup ===" -ForegroundColor Cyan
powershell -ExecutionPolicy Bypass -File "$Root\scripts\verify-railway-deploy.ps1"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "`nImporting database (one-time)..." -ForegroundColor Cyan
powershell -ExecutionPolicy Bypass -File "$Root\scripts\init-railway-db.ps1"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "`nRailway domains:" -ForegroundColor Cyan
railway domain 2>&1

Write-Host "`nBackend is ready. Copy your Railway URL for Netlify RAILWAY_BACKEND_URL." -ForegroundColor Green
Write-Host "Test login: STU001 / Password123!" -ForegroundColor Gray
