<?php if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<h1>Лабораторная №5 — Статьи</h1><p>Откройте статью, чтобы увидеть текст и nickname автора из отдельного SQL-запроса.</p>
<?php foreach ($articles as $article): ?><article><h2><a href="<?=e(url('articles/'.$article['id']))?>"><?=e($article['title'])?></a></h2></article><?php endforeach ?>
<?php if (!$articles): ?><p>Статей пока нет. Импортируйте тестовые данные.</p><?php endif ?>
