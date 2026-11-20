<?php
define('APP_ENTRY', true);
require __DIR__.'/../includes/bootstrap.php';
$target = 'https://httpbin.org/get';
$error = null; $headers = false;
set_error_handler(static function(int $severity, string $message): never { throw new ErrorException($message, 0, $severity); });
try {
    $context = stream_context_create(['http'=>['method'=>'HEAD','timeout'=>8,'follow_location'=>0], 'ssl'=>['verify_peer'=>true,'verify_peer_name'=>true]]);
    $headers = get_headers($target, false, $context);
    if ($headers === false) { $error = 'Не удалось получить заголовки.'; }
} catch (Throwable $exception) { $error = 'Не удалось получить заголовки: проверьте HTTPS, allow_url_fopen и доступ к сети на хостинге.'; }
finally { restore_error_handler(); }
page_start('Лабораторная №1 — HTTP-заголовки');
?>
<h2>Результат get_headers()</h2><p>Публичный HTTPS-адрес: <?=e($target)?></p>
<?php if ($error): ?><p class="error" role="alert"><?=e($error)?></p><?php endif ?>
<label>Заголовки ответа<textarea rows="14" readonly><?=e($headers ? implode("\n", $headers) : 'Заголовки недоступны.')?></textarea></label>
<a href="<?=e(url('lab1/index.php'))?>">← К форме</a>
<?php page_end();
