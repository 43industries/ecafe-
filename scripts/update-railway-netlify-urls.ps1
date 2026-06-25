# Update Railway APP_URL and MPESA_CALLBACK_URL to the Netlify public domain.
param(
    [Parameter(Mandatory = $true)]
    [string]$NetlifyDomain
)

$ErrorActionPreference = "Stop"

if (-not (Get-Command railway -ErrorAction SilentlyContinue)) {
    Write-Host "Install Railway CLI: npm install -g @railway/cli" -ForegroundColor Red
    exit 1
}

if ((railway whoami 2>&1) -match "Unauthorized") {
    Write-Host "Run: railway login && railway link" -ForegroundColor Yellow
    exit 1
}

$domain = $NetlifyDomain.Trim().TrimEnd('/')
if ($domain -notmatch '^https?://') {
    $domain = "https://$domain"
}

$callback = "$domain/api/mpesa/callback"

Write-Host "Setting Railway variables:" -ForegroundColor Cyan
Write-Host "  APP_URL=$domain"
Write-Host "  MPESA_CALLBACK_URL=$callback"

railway variables --set "APP_URL=$domain"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

railway variables --set "MPESA_CALLBACK_URL=$callback"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Done. Redeploy Railway if the app does not pick up new env vars immediately." -ForegroundColor Green
