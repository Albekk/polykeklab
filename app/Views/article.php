<?php if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<article><h1><?=e($article['title'])?></h1><p class="author">Автор: <?=e($nickname)?></p><div><?=nl2br(e($article['body']))?></div></article><p><a href="<?=e(url('articles'))?>">← К списку статей</a></p>
