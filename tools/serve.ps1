param([int]$Port = 8080)
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$phpBinary = if ($phpCommand) { $phpCommand.Source } else { 'C:\OSPanel\modules\PHP-8.2\PHP\php.exe' }
if (!(Test-Path -LiteralPath $phpBinary)) { throw 'Install PHP 8.2+ or add php to PATH.' }
Set-Location (Split-Path $PSScriptRoot -Parent)
& $phpBinary -n -d "extension_dir=$([IO.Path]::GetDirectoryName($phpBinary))/ext" -d extension=pdo_mysql -d extension=mbstring -d extension=openssl -S "127.0.0.1:$Port" router.php
