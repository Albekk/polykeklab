<?php
declare(strict_types=1);
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
$settings = is_file(__DIR__.'/../config.php') ? require __DIR__.'/../config.php' : require __DIR__.'/../config.example.php';
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$detected = preg_replace('~/(?:lab[12]/)?[^/]+\.php$~', '', $script);
define('BASE_PATH', rtrim($settings['base_path'] ?: ($detected ?? ''), '/'));
function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return BASE_PATH.'/'.ltrim($path, '/'); }
function db(): PDO {
    global $settings;
    static $pdo;
    if (!$pdo) {
        $d = $settings['db'];
        $pdo = new PDO("mysql:host={$d['host']};port={$d['port']};dbname={$d['name']};charset=utf8mb4", $d['user'], $d['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $pdo;
}
function university_header(string $title, bool $home = false): void { ?>
<header class="university <?= $home ? 'university-home' : 'university-compact' ?>"><a class="logo" href="<?=e(url())?>"><img src="<?=e(url('assets/logo.svg'))?>" alt="Московский Политех"></a><div><h1><?=e($title)?></h1><?php if ($home): ?><p>Московский Политехнический университет</p><?php endif ?></div></header>
<?php }
function site_footer(): void { ?><footer class="site-footer"><p>Задание для самостоятельной работы</p><p>Выполнил: Беккер Александр Павлович, группа 253-321</p></footer><?php }
function page_start(string $title, bool $home = false): void { ?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=e($title)?></title><link rel="stylesheet" href="<?=e(url('assets/site.css'))?>"></head><body class="<?= $home ? 'home-page' : (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/lab1/') ? 'lab-page feedback-page' : 'lab-page') ?>">
<?php university_header($title, $home); ?>
<main class="card"><nav class="breadcrumb"><a href="<?=e(url())?>">← На главную</a></nav>
<?php }
function page_end(): void { ?></main><?php site_footer(); ?></body></html><?php }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function valid_csrf(): bool { return is_string($_POST['csrf'] ?? null) && hash_equals(csrf(), $_POST['csrf']); }
function post_text(string $key): string { return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ''; }
