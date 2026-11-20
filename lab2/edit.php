<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
$contacts = $pdo->query('SELECT id,surname,name FROM contacts ORDER BY surname ASC,name ASC,id ASC')->fetchAll();
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: (int)($contacts[0]['id']??0);
$statement = $pdo->prepare('SELECT * FROM contacts WHERE id=?'); $statement->execute([$id]); $row=$statement->fetch();
if (!$contacts) { echo '<p>Нет записей для редактирования.</p>'; return; }
if (!$row) { http_response_code(404); echo '<p class="error">Контакт не найден.</p>'; return; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    [$row,$errors]=contact_data();
    if (!valid_csrf()) { $errors[]='Сессия формы истекла.'; }
    if (!$errors) {
        try { $statement=$pdo->prepare('UPDATE contacts SET surname=:surname,name=:name,patronymic=:patronymic,gender=:gender,birth_date=:birth_date,phone=:phone,address=:address,email=:email,comment=:comment WHERE id=:id'); $statement->execute($row+['id'=>$id]); echo '<p class="success">Запись сохранена</p>'; $contacts=$pdo->query('SELECT id,surname,name FROM contacts ORDER BY surname,name,id')->fetchAll(); }
        catch(PDOException $ex) { $errors[]='Не удалось сохранить запись.'; }
    }
    if ($errors) { echo '<p class="error">'.e(implode(' ',$errors)).'</p>'; }
}
echo '<div class="choices">';
foreach ($contacts as $contact) { echo '<a class="'.((int)$contact['id']===$id?'active':'').'" href="'.e(url('lab2/index.php?action=edit&id='.$contact['id'])).'">'.e($contact['surname'].' '.$contact['name']).'</a>'; }
echo '</div>'; $formAction = 'edit'; $formId = $id; ?>
<form method="post" action="<?=e(url('lab2/index.php?action='.$formAction.($formId ? '&id='.$formId : '')))?>">
<?=csrf_field()?>
<?php foreach (['surname'=>'Фамилия','name'=>'Имя','patronymic'=>'Отчество','gender'=>'Пол','birth_date'=>'Дата рождения','phone'=>'Телефон','address'=>'Адрес','email'=>'Email','comment'=>'Комментарий'] as $key=>$label): ?>
<label><?=e($label)?>
<?php if ($key==='gender'): ?><select name="gender"><?php foreach (['не указан','мужской','женский'] as $g): ?><option <?=($row[$key]??'')===$g?'selected':''?>><?=e($g)?></option><?php endforeach ?></select>
<?php elseif ($key==='comment'): ?><textarea name="comment" maxlength="2000"><?=e($row[$key]??'')?></textarea>
<?php else: ?><input name="<?=e($key)?>" type="<?= match($key){'email'=>'email','birth_date'=>'date','phone'=>'tel',default=>'text'} ?>" value="<?=e($row[$key]??'')?>" maxlength="<?=match($key){'email'=>254,'address'=>255,'phone'=>40,default=>100}?>" <?=in_array($key,['surname','name'],true)?'required':''?> <?= $key==='birth_date' ? 'max="'.date('Y-m-d').'"' : ''?>><?php endif ?></label>
<?php endforeach ?><button type="submit">Сохранить</button></form>
