<?php
define('APP_ENTRY', true);
require __DIR__.'/../includes/bootstrap.php';
page_start('Лабораторная №1 — Форма обратной связи');
?>
<h2>Обратная связь</h2><p class="muted">Учебная форма: данные отправляются внешнему сервису httpbin.org. Используйте вымышленные данные.</p>
<form action="https://httpbin.org/post" method="post">
<label>Имя пользователя<input name="name" required maxlength="100" autocomplete="name"></label>
<label>Email<input type="email" name="email" required maxlength="254" autocomplete="email"></label>
<label>Тип обращения<select name="type"><option value="complaint">Жалоба</option><option value="suggestion">Предложение</option><option value="thanks">Благодарность</option></select></label>
<label>Текст обращения<textarea name="message" required maxlength="5000"></textarea></label>
<fieldset><legend>Варианты ответа</legend><label><input type="checkbox" name="reply[]" value="sms"> SMS</label><label><input type="checkbox" name="reply[]" value="email"> Email</label></fieldset>
<p><button type="submit">Отправить</button></p></form><a href="<?=e(url('lab1/headers.php'))?>">Перейти на вторую страницу →</a>
<?php page_end();
