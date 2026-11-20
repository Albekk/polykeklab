<?php
define('APP_ENTRY', true);
require __DIR__.'/includes/bootstrap.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$path = substr($path, strlen(BASE_PATH));
$path = '/'.trim($path, '/');
if ($path !== '/' && $path !== '/index.php') { require __DIR__.'/app/front.php'; exit; }
page_start('Лабораторные работы по PHP', true);
$number = 0;
?>
<div class="home-intro"><div><h2>Список лабораторных работ</h2><p>Практикум по PHP · пять учебных разделов</p></div><p class="student-tag">Беккер Александр Павлович<br>Группа 253-321</p></div><div class="lab-list">
<?php foreach (['lab1/index.php'=>'Форма обратной связи','lab2/index.php'=>'Записная книжка','routing'=>'Роутинг','templates'=>'Шаблоны','articles'=>'Статьи'] as $link=>$label): ?>
<a href="<?=e(url($link))?>"><span class="lab-number" aria-hidden="true"><?=sprintf('%02d', $number+1)?></span><span>Лабораторная №<?=++$number?> — <?=e($label)?></span></a>
<?php endforeach ?></div>
<?php page_end();
