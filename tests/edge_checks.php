<?php
// CLI only; changes are rolled back even when an assertion fails.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('APP_ENTRY',true);
$_SERVER['SCRIPT_NAME']='/class/php-labs/index.php';
$_SERVER['REQUEST_METHOD']='GET';
require __DIR__.'/../includes/bootstrap.php';
function check(bool $value,string $message): void { if (!$value) { throw new RuntimeException($message); } echo 'PASS '.$message."\n"; }
check(BASE_PATH==='/class/php-labs','automatic subfolder prefix');
check(url('hello/name')==='/class/php-labs/hello/name','prefixed route URL');
ob_start(); page_start('Test'); page_end(); $html=ob_get_clean();
check(str_contains($html,'/class/php-labs/assets/logo.svg'),'prefixed logo and assets');
$path='/unused'; ob_start(); require __DIR__.'/../app/front.php'; ob_end_clean();
$pdo=db(); $pdo->beginTransaction();
try {
    $pdo->exec("INSERT INTO users (nickname) VALUES ('edge_author')"); $userId=$pdo->lastInsertId();
    $statement=$pdo->prepare('INSERT INTO articles (user_id,title,body) VALUES (?,?,?)'); $statement->execute([$userId,'Edge article','Text']); $id=$pdo->lastInsertId();
    $statement=$pdo->prepare('DELETE FROM users WHERE id=?'); $statement->execute([$userId]);
    ob_start(); (new ArticlesController())->show($id); $html=ob_get_clean();
    check(str_contains($html,'Автор не найден'),'missing author fallback after FK SET NULL');
    check(str_contains($html,'/class/php-labs/articles'),'prefixed MVC links');
} finally { $pdo->rollBack(); }
define('NOTEBOOK_ENTRY',true); require __DIR__.'/../lab2/fields.php';
$_POST=['surname'=>'Test','name'=>'Student','gender'=>'не указан','birth_date'=>'2000-02-31'];
[, $errors]=contact_data(); check(count($errors)>0,'invalid calendar date rejected');
$_POST=['surname'=>'Test','name'=>'Student','gender'=>'не указан','birth_date'=>'2999-01-01'];
[, $errors]=contact_data(); check(count($errors)>0,'future birth date rejected');
require __DIR__.'/../lab2/viewer.php';
check(str_contains(viewer('id',999),'страница 3 из 3'),'out of range pagination clamped');
check(str_contains(viewer('id',-5),'страница 1 из 3'),'negative pagination clamped');
echo "9 edge checks passed\n";
