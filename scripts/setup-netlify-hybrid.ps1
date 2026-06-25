# End-to-end Netlify + Railway hybrid setup helper.
param(
    [string]$RailwayBackendUrl = "",
    [string]$NetlifyDomain = ""
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
Set-Location $Root

Write-Host "=== School e-Café: Netlify + Railway hybrid setup ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Railway backend
Write-Host "[1/5] Railway backend" -ForegroundColor Yellow
if ((railway whoami 2>&1) -notmatch "Unauthorized") {
    & "$Root\scripts\setup-railway-live.ps1"
} else {
    Write-Host "  Skip: run 'railway login' and 'railway link', then re-run this script." -ForegroundColor Gray
}

# Step 2: Netlify proxy site
Write-Host "`n[2/5] Netlify proxy site" -ForegroundColor Yellow
Write-Host "  Deploy the netlify-proxy/ folder to Netlify:"
Write-Host "    - Connect GitHub repo, set Base directory: netlify-proxy"
Write-Host "    - Or: cd netlify-proxy && netlify deploy --prod"
if ($RailwayBackendUrl) {
    Write-Host "  Set Netlify env: RAILWAY_BACKEND_URL=$RailwayBackendUrl"
}

# Step 3: Custom domain DNS
Write-Host "`n[3/5] Custom domain (optional)" -ForegroundColor Yellow
if ($NetlifyDomain) {
    & "$Root\scripts\verify-netlify-dns.ps1" -Domain $NetlifyDomain
} else {
    Write-Host "  Add a domain in Netlify, then run:"
    Write-Host "    .\scripts\verify-netlify-dns.ps1 -Domain yourdomain.com"
}

# Step 4: Update Railway URLs
Write-Host "`n[4/5] Railway URL env vars" -ForegroundColor Yellow
if ($NetlifyDomain) {
    & "$Root\scripts\update-railway-netlify-urls.ps1" -NetlifyDomain $NetlifyDomain
} else {
    Write-Host "  After Netlify domain is live, run:"
    Write-Host "    .\scripts\update-railway-netlify-urls.ps1 -NetlifyDomain https://your-site.netlify.app"
}

# Step 5: Smoke test
Write-Host "`n[5/5] Smoke test" -ForegroundColor Yellow
if ($NetlifyDomain) {
    & "$Root\scripts\test-hybrid-deploy.ps1" -NetlifyUrl $NetlifyDomain -RailwayUrl $RailwayBackendUrl
} else {
    Write-Host "  After deploy, run:"
    Write-Host "    .\scripts\test-hybrid-deploy.ps1 -NetlifyUrl https://your-site.netlify.app -RailwayUrl $RailwayBackendUrl"
}

Write-Host "`nSetup guide: docs/NETLIFY.md" -ForegroundColor Gray
