<?php
// CLI only. Intended for an isolated, disposable local MySQL instance on port 3307.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$pdo=new PDO('mysql:host=127.0.0.1;port=3307;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS phplabs_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE phplabs_test');
foreach (['01_schema.sql','02_seed.sql'] as $file) { $pdo->exec(file_get_contents(__DIR__.'/../sql/'.$file)); }
echo "Imported schema and seed into isolated phplabs_test.\n";
