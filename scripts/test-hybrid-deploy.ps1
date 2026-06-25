# Smoke-test hybrid Netlify proxy + Railway backend.
param(
    [Parameter(Mandatory = $true)]
    [string]$NetlifyUrl,
    [string]$RailwayUrl = ""
)

$ErrorActionPreference = "Stop"

function Test-Endpoint {
    param([string]$Base, [string]$Label)
    $url = $Base.TrimEnd('/') + '/'
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 30
        Write-Host "OK  $Label ($url) HTTP $($response.StatusCode)" -ForegroundColor Green
        return $response
    } catch {
        Write-Host "FAIL $Label ($url) $($_.Exception.Message)" -ForegroundColor Red
        return $null
    }
}

function Test-LoginPage {
    param([string]$Base, [string]$Label)
    $url = $Base.TrimEnd('/') + '/login'
    try {
        $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 30
        if ($response.Content -match 'login|password|STU') {
            Write-Host "OK  $Label login page ($url)" -ForegroundColor Green
            return $true
        }
        Write-Host "WARN $Label login page loaded but content unexpected" -ForegroundColor Yellow
        return $false
    } catch {
        Write-Host "FAIL $Label login ($url) $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

function Test-MpesaCallback {
    param([string]$Base, [string]$Label)
    $url = $Base.TrimEnd('/') + '/api/mpesa/callback'
    try {
        $response = Invoke-WebRequest -Uri $url -Method POST -UseBasicParsing -TimeoutSec 30 -Body '{}'
        Write-Host "OK  $Label M-Pesa callback ($url) HTTP $($response.StatusCode)" -ForegroundColor Green
        return $true
    } catch {
        $status = $null
        if ($_.Exception.Response) {
            $status = [int]$_.Exception.Response.StatusCode
        }
        if ($status -and $status -lt 500) {
            Write-Host "OK  $Label M-Pesa callback ($url) HTTP $status" -ForegroundColor Green
            return $true
        }
        Write-Host "FAIL $Label M-Pesa callback ($url) $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

Write-Host "=== Hybrid deployment smoke test ===" -ForegroundColor Cyan

$netlifyBase = $NetlifyUrl
if ($netlifyBase -notmatch '^https?://') { $netlifyBase = "https://$netlifyBase" }

$failed = $false

$netlifyHome = Test-Endpoint -Base $netlifyBase -Label "Netlify"
if (-not $netlifyHome) { $failed = $true }

if ($RailwayUrl) {
    $railwayBase = $RailwayUrl
    if ($railwayBase -notmatch '^https?://') { $railwayBase = "https://$railwayBase" }
    $railwayHome = Test-Endpoint -Base $railwayBase -Label "Railway"
    if (-not $railwayHome) { $failed = $true }
}

if (-not (Test-LoginPage -Base $netlifyBase -Label "Netlify")) { $failed = $true }
if (-not (Test-MpesaCallback -Base $netlifyBase -Label "Netlify")) { $failed = $true }

Write-Host ""
if ($failed) {
    Write-Host "Some checks failed. Verify RAILWAY_BACKEND_URL on Netlify and Railway env vars." -ForegroundColor Red
    exit 1
}

Write-Host "All automated checks passed." -ForegroundColor Green
Write-Host "Manual: log in as STU001, place an order, confirm QR code." -ForegroundColor Gray
