<?php if (!defined('APP_ENTRY')) { http_response_code(403); exit; } ?>
<h1>Лабораторная №3 — Роутинг</h1><p>Front Controller передаёт путь роутеру, а роутер вызывает метод контроллера с именем из URL.</p>
<ul><li><a href="<?=e(url('hello/username'))?>">/hello/username — приветствие</a></li><li><a href="<?=e(url('bye/username'))?>">/bye/username — прощание</a></li><li><a href="<?=e(url('hello/'.rawurlencode('Алексей')))?>">Приветствие с русским именем</a></li></ul>
<form method="get" action="<?=e(url('routing'))?>"><label>Имя для примеров <input name="name" maxlength="80" value="<?=e(is_string($_GET['name']??null)?$_GET['name']:'Студент')?>" required></label> <button>Показать ссылки</button></form>
<?php $example=is_string($_GET['name']??null)?trim($_GET['name']):'Студент'; if ($example!=='' && !str_contains($example,'/')): ?>
<p><a href="<?=e(url('hello/'.rawurlencode($example)))?>">Привет, <?=e($example)?></a> · <a href="<?=e(url('bye/'.rawurlencode($example)))?>">Пока, <?=e($example)?></a></p><?php endif ?>
