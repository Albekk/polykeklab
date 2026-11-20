<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
function render(string $view,array $data=[]): void {
    extract($data,EXTR_SKIP);
    ob_start(); require __DIR__.'/Views/'.$view.'.php'; $content=ob_get_clean();
    require __DIR__.'/Templates/main.php';
}
require __DIR__.'/Router.php';
require __DIR__.'/Controllers/HelloController.php';
require __DIR__.'/Controllers/ArticlesController.php';
$router=new Router(); $hello=new HelloController(); $articles=new ArticlesController();
$router->get('~^/hello/([^/]+)$~',[$hello,'sayHello']);
$router->get('~^/bye/([^/]+)$~',[$hello,'sayBye']);
$router->get('~^/routing$~',static fn()=>render('routing',['title'=>'Лабораторная №3 — Роутинг']));
$router->get('~^/templates$~',static fn()=>render('templates'));
$router->get('~^/blog$~',static fn()=>render('message',['heading'=>'Мой блог','message'=>'Учебный MVC-проект на чистом PHP: маршруты, шаблоны и статьи.']));
$router->get('~^/about-me$~',static fn()=>render('message',['title'=>'Обо мне','heading'=>'Обо мне','message'=>'Беккер Александр Павлович, группа 253-321. Я изучаю PHP, PDO и архитектуру MVC. Этот сайт объединяет пять лабораторных работ.']));
$router->get('~^/articles$~',[$articles,'index']);
$router->get('~^/articles/([1-9][0-9]*)$~',[$articles,'show']);
try { $router->dispatch($path); }
catch(PDOException $ex) { http_response_code(503); render('message',['title'=>'База данных недоступна','heading'=>'База данных недоступна','message'=>'Настройте config.php и импортируйте SQL-файлы. Инструкция находится в README.']); }
