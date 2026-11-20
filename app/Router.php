<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
final class Router {
    private array $routes=[];
    public function get(string $pattern, callable $handler): void { $this->routes[]=[$pattern,$handler]; }
    public function dispatch(string $path): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'HEAD') { header('Allow: GET, HEAD'); http_response_code(405); render('message',['heading'=>'Метод не разрешён','message'=>'Используйте GET.']); return; }
        foreach ($this->routes as [$pattern,$handler]) {
            if (preg_match($pattern,$path,$matches)) { array_shift($matches); $handler(...array_map('rawurldecode',$matches)); return; }
        }
        http_response_code(404); render('message',['heading'=>'Страница не найдена','message'=>'Проверьте адрес или воспользуйтесь меню.']);
    }
}
