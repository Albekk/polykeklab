<?php if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=e($title??'Мой блог')?></title><link rel="stylesheet" href="<?=e(url('assets/site.css'))?>"><link rel="stylesheet" href="<?=e(url('assets/blog.css'))?>"></head><body class="blog-page">
<?php university_header('Мой блог'); ?><div class="blog-card">
<nav class="blog-toolbar"><a href="<?=e(url())?>">← Все лабораторные работы</a></nav>
<table class="layout"><tr><td colspan="2" class="header">Мой блог</td></tr><tr><td class="sidebar"><nav><div class="sidebarHeader">Меню</div><ul>
<?php foreach (['blog'=>'Главная страница','about-me'=>'Обо мне','hello/'.rawurlencode('Александр')=>'Приветствие','bye/'.rawurlencode('Александр')=>'Прощание','routing'=>'ЛР3: Роутинг','templates'=>'ЛР4: Шаблоны','articles'=>'ЛР5: Статьи'] as $link=>$label): ?><li><a href="<?=e(url($link))?>"><?=e($label)?></a></li><?php endforeach ?>
</ul></nav></td><td class="content"><main><?=$content?></main></td></tr></table></div><?php site_footer(); ?></body></html>
