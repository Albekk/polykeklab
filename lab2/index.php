<?php
define('APP_ENTRY',true); define('NOTEBOOK_ENTRY',true);
session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','cookie_secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off']);
require __DIR__.'/../includes/bootstrap.php';
$action = is_string($_GET['action']??null) ? $_GET['action'] : 'view';
if (!in_array($action,['view','add','edit','delete'],true)) { $action='view'; }
$sort = is_string($_GET['sort']??null) && in_array($_GET['sort'],['id','surname','birth_date'],true) ? $_GET['sort'] : 'id';
require __DIR__.'/menu.php';
ob_start();
try {
    require __DIR__.'/config.php'; require __DIR__.'/fields.php';
    if ($action==='view') { require __DIR__.'/viewer.php'; echo viewer($sort,max(1,(int)($_GET['page']??1))); }
    else { require __DIR__.'/'.$action.'.php'; }
} catch (PDOException $ex) { http_response_code(503); echo '<p class="error">База данных недоступна. Настройте config.php и импортируйте SQL-файлы по README.</p>'; }
$content=ob_get_clean();
page_start('Лабораторная №2 — Записная книжка'); echo menu(); echo $content; page_end();
