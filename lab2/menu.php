<?php
if (!defined('NOTEBOOK_ENTRY')) { http_response_code(403); exit; }
function menu(): string {
    global $action, $sort;
    $html = '<nav class="menu" aria-label="Записная книжка">';
    foreach (['view'=>'Просмотр','add'=>'Добавление записи','edit'=>'Редактирование записи','delete'=>'Удаление записи'] as $key=>$text) {
        $html .= '<a class="'.($action === $key ? 'active' : '').'" href="'.e(url('lab2/index.php?action='.$key)).'">'.e($text).'</a>';
    }
    $html .= '</nav>';
    if ($action === 'view') {
        $html .= '<nav class="menu submenu" aria-label="Сортировка">';
        foreach (['id'=>'По порядку добавления','surname'=>'По фамилии','birth_date'=>'По дате рождения'] as $key=>$text) {
            $html .= '<a class="'.($sort === $key ? 'active' : '').'" href="'.e(url('lab2/index.php?action=view&sort='.$key)).'">'.e($text).'</a>';
        }
        $html .= '</nav>';
    }
    return $html;
}
