<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
$row = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$row,$errors] = contact_data();
    if (!valid_csrf()) { $errors[] = 'Сессия формы истекла. Повторите отправку.'; }
    if (!$errors) {
        try { $statement = $pdo->prepare('INSERT INTO contacts (surname,name,patronymic,gender,birth_date,phone,address,email,comment) VALUES (:surname,:name,:patronymic,:gender,:birth_date,:phone,:address,:email,:comment)'); $statement->execute($row); echo '<p class="success">Запись добавлена</p>'; $row=[]; }
        catch (PDOException $ex) { $errors[] = 'Проверьте подключение и SQL-схему.'; }
    }
    if ($errors) { echo '<p class="error">Ошибка: запись не добавлена</p><p>'.e(implode(' ', $errors)).'</p>'; }
}
$formAction = 'add'; $formId = null; ?>
<form method="post" action="<?=e(url('lab2/index.php?action='.$formAction.($formId ? '&id='.$formId : '')))?>">
<?=csrf_field()?>
<?php foreach (['surname'=>'Фамилия','name'=>'Имя','patronymic'=>'Отчество','gender'=>'Пол','birth_date'=>'Дата рождения','phone'=>'Телефон','address'=>'Адрес','email'=>'Email','comment'=>'Комментарий'] as $key=>$label): ?>
<label><?=e($label)?>
<?php if ($key==='gender'): ?><select name="gender"><?php foreach (['не указан','мужской','женский'] as $g): ?><option <?=($row[$key]??'')===$g?'selected':''?>><?=e($g)?></option><?php endforeach ?></select>
<?php elseif ($key==='comment'): ?><textarea name="comment" maxlength="2000"><?=e($row[$key]??'')?></textarea>
<?php else: ?><input name="<?=e($key)?>" type="<?= match($key){'email'=>'email','birth_date'=>'date','phone'=>'tel',default=>'text'} ?>" value="<?=e($row[$key]??'')?>" maxlength="<?=match($key){'email'=>254,'address'=>255,'phone'=>40,default=>100}?>" <?=in_array($key,['surname','name'],true)?'required':''?> <?= $key==='birth_date' ? 'max="'.date('Y-m-d').'"' : ''?>><?php endif ?></label>
<?php endforeach ?><button type="submit">Сохранить</button></form>
