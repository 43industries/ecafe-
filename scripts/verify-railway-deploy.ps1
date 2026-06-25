# Verify Railway deployment artifacts are present and PHP config is valid.
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

$required = @(
    "railway.toml",
    "Caddyfile",
    "start-container.sh",
    "scripts/ensure-storage.sh",
    "scripts/init-railway-db.ps1",
    "database/railway-init.sql",
    "netlify-proxy/netlify.toml",
    "netlify-proxy/scripts/generate-redirects.js",
    "docs/NETLIFY.md"
)

$failed = $false
foreach ($file in $required) {
    if (-not (Test-Path $file)) {
        Write-Host "MISSING: $file" -ForegroundColor Red
        $failed = $true
    }
}

php -l "config/database.php" | Out-Null
php -l "bootstrap.php" | Out-Null
php -l "public/index.php" | Out-Null

if ($failed) {
    Write-Host "Deployment artifact check failed." -ForegroundColor Red
    exit 1
}

Write-Host "All Railway deployment files present. PHP syntax OK." -ForegroundColor Green
Write-Host ""
Write-Host "After deploying to Railway, verify manually:" -ForegroundColor Cyan
Write-Host "  [ ] https://<your-domain>/ loads"
Write-Host "  [ ] Login STU001 / Password123!"
Write-Host "  [ ] Test order generates QR code"
Write-Host "  [ ] QR codes persist after redeploy"
Write-Host "  [ ] M-Pesa callback URL reachable"
