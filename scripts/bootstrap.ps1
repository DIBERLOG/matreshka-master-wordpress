$ErrorActionPreference = 'Stop'

function Invoke-DockerCompose {
  param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
  )

  & docker compose @Args
  if ($LASTEXITCODE -ne 0) {
    throw "docker compose $($Args -join ' ') failed with exit code $LASTEXITCODE."
  }
}

function Invoke-WpCli {
  param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
  )

  Invoke-DockerCompose run --rm wpcli @Args --url=http://localhost:8080 --allow-root
}

function Install-PublicPluginZip {
  param(
    [Parameter(Mandatory = $true)]
    [string]$Slug
  )

  $tempDir = Join-Path $PSScriptRoot '..\.tmp\plugins'
  $tempDir = [System.IO.Path]::GetFullPath($tempDir)
  New-Item -ItemType Directory -Force -Path $tempDir | Out-Null

  $zipPath = Join-Path $tempDir ($Slug + '.zip')
  $destination = Join-Path $PSScriptRoot '..\wp-content\plugins'
  $destination = [System.IO.Path]::GetFullPath($destination)
  $pluginPath = Join-Path $destination $Slug

  if (Test-Path $pluginPath) {
    Write-Host "Plugin $Slug already exists, skipping download."
    return
  }

  Write-Host "Downloading plugin $Slug..."
  Invoke-WebRequest -Uri "https://downloads.wordpress.org/plugin/$Slug.latest-stable.zip" -OutFile $zipPath

  Write-Host "Extracting plugin $Slug..."
  Expand-Archive -LiteralPath $zipPath -DestinationPath $destination -Force
}

Write-Host "Starting WordPress stack..."
Invoke-DockerCompose up -d db wordpress

Write-Host "Waiting for database readiness..."
$ready = $false
for ($attempt = 1; $attempt -le 24; $attempt++) {
  & docker compose exec -T db mysqladmin ping -h localhost -uroot -proot --silent *> $null
  if ($LASTEXITCODE -eq 0) {
    $ready = $true
    break
  }

  Start-Sleep -Seconds 5
}

if (-not $ready) {
  throw "WordPress stack did not become ready in time."
}

Write-Host "Installing WordPress core configuration..."
& docker compose run --rm wpcli core is-installed --url=http://localhost:8080 --allow-root *> $null
if ($LASTEXITCODE -ne 0) {
  Invoke-WpCli core install `
    --title="Matreshka Master" `
    --admin_user="admin" `
    --admin_password="admin" `
    --admin_email="admin@example.com" `
    --skip-email
}

Write-Host "Activating theme and core plugin..."
Invoke-WpCli theme activate matreshka-master
Invoke-WpCli plugin activate matreshka-master-core

Write-Host "Installing recommended public plugins..."
foreach ($plugin in @('woocommerce', 'polylang', 'seo-by-rank-math', 'wp-mail-smtp')) {
  Install-PublicPluginZip -Slug $plugin
}
Invoke-WpCli plugin activate woocommerce polylang seo-by-rank-math wp-mail-smtp

Write-Host "Assigning homepage and generating demo content..."
Invoke-WpCli option update show_on_front page
Invoke-WpCli eval-file /var/www/html/project/scripts/seed-demo.php

Write-Host "Bootstrap complete."
Write-Host "Frontend: http://localhost:8080"
Write-Host "Admin:    http://localhost:8080/wp-admin"
