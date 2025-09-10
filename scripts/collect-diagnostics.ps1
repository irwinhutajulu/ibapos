<#
collect-diagnostics.ps1
Usage: run from project root or from anywhere; collects diagnostics for the IBAPOS project.
Example: powershell -ExecutionPolicy Bypass -File .\scripts\collect-diagnostics.ps1
Optional switches:
  -ProjectRoot <path>  : Project root (default: c:\xampp\htdocs\Data IBA POS\IBAPOS)
  -RunInstall           : Run composer install & npm ci (OFF by default)
  -IncludeTests         : Run phpunit / artisan tests (ON by default)
#>
param(
    [string]$ProjectRoot = 'c:\xampp\htdocs\Data IBA POS\IBAPOS',
    [switch]$RunInstall = $false,
    [switch]$IncludeTests = $true
)

try {
    Set-Location -Path $ProjectRoot -ErrorAction Stop
} catch {
    Write-Host "Cannot set location to $ProjectRoot : $_" -ForegroundColor Red
    exit 1
}

$timestamp = (Get-Date).ToString('yyyyMMdd_HHmmss')
$outDir = Join-Path -Path $ProjectRoot -ChildPath "diagnostics_$timestamp"
New-Item -Path $outDir -ItemType Directory -Force | Out-Null

function Run-CommandToFile {
    param($Command, $OutFile)
    try {
        Write-Host "Running: $Command"
        $output = Invoke-Expression $Command 2>&1
        $output | Out-File -FilePath $OutFile -Encoding utf8
    } catch {
        $_ | Out-File -FilePath $OutFile -Append -Encoding utf8
    }
}

# 1) Environment versions
Run-CommandToFile -Command "php -v" -OutFile (Join-Path $outDir 'php_version.txt')
Run-CommandToFile -Command "composer --version" -OutFile (Join-Path $outDir 'composer_version.txt')
Run-CommandToFile -Command "node -v" -OutFile (Join-Path $outDir 'node_version.txt')
Run-CommandToFile -Command "npm -v" -OutFile (Join-Path $outDir 'npm_version.txt')

# 2) Git status & branch
Run-CommandToFile -Command "git rev-parse --abbrev-ref HEAD" -OutFile (Join-Path $outDir 'git_branch.txt')
Run-CommandToFile -Command "git status --porcelain" -OutFile (Join-Path $outDir 'git_status.txt')
Run-CommandToFile -Command "git log -n 5 --pretty=format:'%h %ad %s' --date=short" -OutFile (Join-Path $outDir 'git_recent_commits.txt')

# 3) Important file lists
Get-ChildItem -Path (Join-Path $ProjectRoot 'Project Documentation') -File | Select-Object Name | Out-File (Join-Path $outDir 'catatan_project_files.txt') -Encoding utf8
Get-ChildItem -Path $ProjectRoot -Recurse -Include *.php,*.blade.php,*.md -File -ErrorAction SilentlyContinue | Select-Object FullName -First 200 | Out-File (Join-Path $outDir 'sample_files.txt') -Encoding utf8

# 4) Optional install (skipped by default)
if ($RunInstall) {
    Run-CommandToFile -Command "composer install --no-interaction --prefer-dist" -OutFile (Join-Path $outDir 'composer_install.txt')
    Run-CommandToFile -Command "npm ci" -OutFile (Join-Path $outDir 'npm_ci.txt')
}

# 5) Quick PHP lint on critical files (non-blocking)
$critical = @(
    'app\Http\Controllers\LocationController.php',
    'app\Http\Middleware\DeveloperPermissionMiddleware.php'
)
foreach ($f in $critical) {
    $p = Join-Path $ProjectRoot $f
    $out = Join-Path $outDir ("lint_" + ($f -replace '[\\/]', '_') + '.txt')
    if (Test-Path $p) {
        Run-CommandToFile -Command "php -l `"$p`"" -OutFile $out
    } else {
        "MISSING: $p" | Out-File -FilePath $out -Encoding utf8
    }
}

# 6) Run tests (if requested)
if ($IncludeTests) {
    Run-CommandToFile -Command "php artisan test --filter Location --stop-on-failure" -OutFile (Join-Path $outDir 'artisan_test_location.txt')
    Run-CommandToFile -Command "php artisan test --testsuite=Feature --stop-on-failure" -OutFile (Join-Path $outDir 'artisan_test_feature.txt')
}

# 7) Collect recent logs (last 500 lines)
$laravelLog = Join-Path $ProjectRoot 'storage\logs\laravel.log'
$apacheLog = 'C:\xampp\apache\logs\error.log'
$phpErrorLog = 'C:\xampp\php\logs\php_error_log'
if (Test-Path $laravelLog) { Get-Content $laravelLog -Tail 500 | Out-File (Join-Path $outDir 'laravel_log_tail.txt') -Encoding utf8 } else { 'NO_LARAVEL_LOG' | Out-File (Join-Path $outDir 'laravel_log_tail.txt') -Encoding utf8 }
if (Test-Path $apacheLog) { Get-Content $apacheLog -Tail 500 | Out-File (Join-Path $outDir 'apache_error_log_tail.txt') -Encoding utf8 } else { 'NO_APACHE_LOG' | Out-File (Join-Path $outDir 'apache_error_log_tail.txt') -Encoding utf8 }
if (Test-Path $phpErrorLog) { Get-Content $phpErrorLog -Tail 200 | Out-File (Join-Path $outDir 'php_error_log_tail.txt') -Encoding utf8 } else { 'NO_PHP_ERROR_LOG' | Out-File (Join-Path $outDir 'php_error_log_tail.txt') -Encoding utf8 }

# 8) Quick grep for key symbols
Select-String -Path "$ProjectRoot\**\*.php" -Pattern "DeveloperPermissionMiddleware" -SimpleMatch -List | Select-Object Path,LineNumber | Out-File (Join-Path $outDir 'grep_developer_permission.txt') -Encoding utf8
Select-String -Path "$ProjectRoot\**\*.php" -Pattern "LocationController" -SimpleMatch -List | Select-Object Path,LineNumber | Out-File (Join-Path $outDir 'grep_locationcontroller.txt') -Encoding utf8
Select-String -Path "$ProjectRoot\**\*.blade.php" -Pattern "confirmation-modal" -SimpleMatch -List | Select-Object Path,LineNumber | Out-File (Join-Path $outDir 'grep_confirmation_modal.txt') -Encoding utf8

# 9) Package the outputs
$zipPath = Join-Path $ProjectRoot ("diagnostics_$timestamp.zip")
Write-Host "Creating ZIP: $zipPath"
Compress-Archive -Path (Join-Path $outDir '*') -DestinationPath $zipPath -Force

# 10) Summary print
Write-Host "Diagnostics collected in: $outDir"
Write-Host "ZIP file: $zipPath"
Write-Host "Files included:"
Get-ChildItem -Path $outDir | Select-Object Name,Length | Format-Table

# exit with success
exit 0
