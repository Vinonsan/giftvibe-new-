$ErrorActionPreference = "Stop"

$source = Split-Path -Parent $MyInvocation.MyCommand.Path
$tempDir = Join-Path $source "giftvibe_deploy"
$zipPath = Join-Path $source "giftvibelk-deployment.zip"

$includeRoot = @(
    "app",
    "public",
    "resources",
    "routes",
    "storage",
    "config.php",
    "schema.sql",
    ".htaccess",
    "DEPLOYMENT.md",
    "config.local.example.php"
)

Write-Host "Cleaning up..."
if (Test-Path $tempDir) { Remove-Item $tempDir -Recurse -Force }
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }
New-Item -ItemType Directory -Path $tempDir | Out-Null

Write-Host "Copying deployment files..."
foreach ($name in $includeRoot) {
    $from = Join-Path $source $name
    if (-not (Test-Path $from)) {
        Write-Warning "Skipped missing: $name"
        continue
    }
    Copy-Item -Path $from -Destination (Join-Path $tempDir $name) -Recurse -Force
}

$configLocal = Join-Path $tempDir "config.local.php"
if (Test-Path $configLocal) { Remove-Item $configLocal -Force }

Write-Host "Cleaning storage sessions and logs..."
$storagePath = Join-Path $tempDir "storage"
if (Test-Path $storagePath) {
    Get-ChildItem -Path $storagePath -Include "*.html", "*.txt", "*.log", "*.xml", "*.md" -File -Recurse -ErrorAction SilentlyContinue |
        Remove-Item -Force -ErrorAction SilentlyContinue
    $sessionsPath = Join-Path $storagePath "sessions"
    if (Test-Path $sessionsPath) {
        Get-ChildItem -Path $sessionsPath -File -ErrorAction SilentlyContinue | Remove-Item -Force
    }
}

Write-Host "Removing customer receipt uploads (not needed for deploy package)..."
$receiptsPath = Join-Path $tempDir "public\assets\uploads\receipts"
if (Test-Path $receiptsPath) {
    Get-ChildItem -Path $receiptsPath -File -ErrorAction SilentlyContinue | Remove-Item -Force
}

$adminCss = Join-Path $tempDir "public\assets\css\admin.css"
if (-not (Test-Path $adminCss)) {
    throw "Missing compiled admin CSS: public/assets/css/admin.css"
}

Write-Host "Creating zip (this may take a minute)..."
Push-Location $tempDir
try {
    tar.exe -a -c -f $zipPath *
} finally {
    Pop-Location
}

Remove-Item $tempDir -Recurse -Force

$sizeMb = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "Done: $zipPath ($sizeMb MB)"
