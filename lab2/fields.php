<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
function contact_data(): array {
    $data = []; $errors = [];
    foreach (['surname'=>100,'name'=>100,'patronymic'=>100,'gender'=>20,'birth_date'=>10,'phone'=>40,'address'=>255,'email'=>254,'comment'=>2000] as $key=>$max) {
        $data[$key] = post_text($key);
        if (mb_strlen($data[$key]) > $max) { $errors[] = 'Слишком длинное значение: '.$key; }
    }
    if ($data['surname']==='' || $data['name']==='') { $errors[] = 'Укажите фамилию и имя.'; }
    if (!in_array($data['gender'], ['мужской','женский','не указан'], true)) { $errors[] = 'Выберите пол из списка.'; }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['birth_date']);
    if ($data['birth_date'] !== '' && (!$date || $date->format('Y-m-d') !== $data['birth_date'] || $date > new DateTimeImmutable('today'))) { $errors[] = 'Некорректная дата рождения.'; }
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Некорректный email.'; }
    if ($data['phone'] !== '' && !preg_match('/^[+\d\s()\-]{3,40}$/u', $data['phone'])) { $errors[] = 'Некорректный телефон.'; }
    $data['birth_date'] = $data['birth_date'] ?: null;
    return [$data, $errors];
}
