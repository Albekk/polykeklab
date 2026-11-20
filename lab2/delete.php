<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
// Explicit selection link carries a session token, as required by the assignment.
// A list view alone never deletes anything. No browser prefetch is requested.
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if ($id) {
    $token = $_GET['token']??'';
    if (!is_string($token) || !hash_equals(csrf(),$token)) { http_response_code(403); echo '<p class="error">Недействительная ссылка удаления.</p>'; }
    else {
        $statement=$pdo->prepare('SELECT surname FROM contacts WHERE id=?'); $statement->execute([$id]); $surname=$statement->fetchColumn();
        if ($surname!==false) { $statement=$pdo->prepare('DELETE FROM contacts WHERE id=?'); $statement->execute([$id]); echo '<p class="success">Запись с фамилией '.e($surname).' удалена</p>'; }
        else { echo '<p>Запись уже удалена или не существует.</p>'; }
    }
}
echo '<p>Выбор ссылки сразу удаляет соответствующий учебный контакт.</p><div class="choices">';
foreach ($pdo->query('SELECT id,surname,name,patronymic FROM contacts ORDER BY surname,name,id') as $row) {
    $initials=mb_substr($row['name'],0,1).'.'.($row['patronymic']!==''?' '.mb_substr($row['patronymic'],0,1).'.':'');
    echo '<a href="'.e(url('lab2/index.php?action=delete&id='.$row['id'].'&token='.csrf())).'">'.e($row['surname'].' '.$initials).'</a>';
}
echo '</div>';
