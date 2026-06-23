# School e-Café - Full local setup and launch
$ErrorActionPreference = "Stop"
$Root = $PSScriptRoot
Set-Location $Root

$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

Write-Host "`n=== School e-Cafe Setup ===" -ForegroundColor Cyan

# Composer dependencies
if (-not (Test-Path "$Root\vendor")) {
    if (-not (Test-Path "$Root\composer.phar")) {
        Invoke-WebRequest -Uri "https://getcomposer.org/download/latest-stable/composer.phar" -OutFile "$Root\composer.phar"
    }
    php composer.phar install --no-interaction
}

# Start MariaDB if not running
$mysql = "C:\Program Files\MariaDB 12.3\bin\mysql.exe"
$mysqld = "C:\Program Files\MariaDB 12.3\bin\mysqld.exe"
if (Test-Path $mysql) {
    $dbUp = $false
    try { & $mysql -u root -e "SELECT 1" 2>$null; $dbUp = $true } catch {}
    if (-not $dbUp -and (Test-Path $mysqld)) {
        Write-Host "Starting MariaDB..." -ForegroundColor Yellow
        Start-Process -FilePath $mysqld -ArgumentList "--console" -WindowStyle Hidden
        Start-Sleep 5
    }
    $count = & $mysql -u root -N -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='ecafe_db';" 2>$null
    if ($count -ne "1") {
        Write-Host "Importing database..." -ForegroundColor Yellow
        Get-Content "$Root\database\schema.sql" | & $mysql -u root
        Get-Content "$Root\database\seed.sql" | & $mysql -u root
    }
}

if (-not (Test-Path "$Root\.env")) {
    Copy-Item "$Root\.env.example" "$Root\.env"
}

Write-Host "`nLaunching at http://localhost:8000" -ForegroundColor Green
Write-Host "Demo login: STU001 / Password123!`n" -ForegroundColor Gray
php -S localhost:8000 -t public public/router.php
