<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
final class HelloController {
    public function sayHello(string $name): void { render('message',['title'=>'Страница приветствия','heading'=>'Привет, '.$name,'message'=>'Маршрут /hello/name передал имя в контроллер и представление.']); }
    public function sayBye(string $name): void { render('message',['heading'=>'Пока, '.$name,'message'=>'Маршрут /bye/name вызывает экшн sayBye(string $name).']); }
}
