<?php
// Development router for php -S. Emulates Apache rewrites and protected directories.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if (preg_match('~(?:^|/)\.|^/(?:app|includes|sql|tests|tools)(?:/|$)|^/(?:config(?:\.example)?\.php|README\.md|TESTING\.md)$~i', $path)) { http_response_code(403); exit('Доступ запрещён'); }
$file = realpath(__DIR__.$path);
if ($file && str_starts_with($file, __DIR__.DIRECTORY_SEPARATOR) && is_file($file)) { return false; }
if (is_dir(__DIR__.$path) && is_file(__DIR__.$path.'/index.php')) { require __DIR__.$path.'/index.php'; return; }
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__.'/index.php';
