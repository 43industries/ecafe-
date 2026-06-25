# Verify Netlify custom domain DNS configuration.
param(
    [Parameter(Mandatory = $true)]
    [string]$Domain
)

$ErrorActionPreference = "Stop"

$hostName = $Domain -replace '^https?://', '' -replace '/.*$', ''

Write-Host "=== DNS check for $hostName ===" -ForegroundColor Cyan

try {
    $cname = Resolve-DnsName -Name $hostName -Type CNAME -ErrorAction SilentlyContinue
    if ($cname) {
        Write-Host "CNAME: $($cname.NameHost)" -ForegroundColor Green
    }
} catch {}

try {
    $a = Resolve-DnsName -Name $hostName -Type A -ErrorAction SilentlyContinue
    foreach ($record in $a) {
        if ($record.IPAddress) {
            Write-Host "A: $($record.IPAddress)" -ForegroundColor Green
        }
    }
} catch {
    Write-Host "No A record found for $hostName" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Netlify custom domain setup:" -ForegroundColor Cyan
Write-Host "  1. Netlify → Site → Domain management → Add domain → $hostName"
Write-Host "  2. At your registrar, set DNS to Netlify's values (CNAME to <site>.netlify.app or Netlify DNS)"
Write-Host "  3. Wait for SSL provisioning (usually a few minutes)"
Write-Host "  4. Run: .\scripts\update-railway-netlify-urls.ps1 -NetlifyDomain https://$hostName"

if (Get-Command netlify -ErrorAction SilentlyContinue) {
    Write-Host "`nNetlify CLI domain status:" -ForegroundColor Cyan
    netlify status 2>&1
}
