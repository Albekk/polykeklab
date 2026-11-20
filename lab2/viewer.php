<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
function viewer(string $sort, int $page): string {
    global $pdo;
    $sort = in_array($sort, ['id','surname','birth_date'], true) ? $sort : 'id';
    $count = (int)$pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    $pages = max(1, (int)ceil($count / 10)); $page = max(1, min($page, $pages));
    $query = $pdo->prepare("SELECT * FROM contacts ORDER BY $sort ASC, id ASC LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', 10, PDO::PARAM_INT); $query->bindValue(':offset', ($page-1)*10, PDO::PARAM_INT); $query->execute();
    $html = '<div class="table-scroll"><table class="contacts"><caption>Контакты — страница '.$page.' из '.$pages.'</caption><thead><tr>';
    foreach (['№','Фамилия','Имя','Отчество','Пол','Дата рождения','Телефон','Адрес','Email','Комментарий'] as $text) { $html .= '<th scope="col">'.$text.'</th>'; }
    $html .= '</tr></thead><tbody>';
    foreach ($query as $row) { $html .= '<tr>'; foreach (['id','surname','name','patronymic','gender','birth_date','phone','address','email','comment'] as $key) { $html .= '<td>'.e($row[$key]).'</td>'; } $html .= '</tr>'; }
    $html .= '</tbody></table></div>';
    if (!$count) { $html .= '<p>Записная книжка пуста. Добавьте первый контакт.</p>'; }
    if ($pages > 1) { $html .= '<nav class="pagination" aria-label="Страницы">'; for ($i=1;$i<=$pages;$i++) { $html .= '<a '.($i===$page ? 'class="active" aria-current="page"' : '').' href="'.e(url("lab2/index.php?action=view&sort=$sort&page=$i")).'">'.$i.'</a>'; } $html .= '</nav>'; }
    return $html;
}
