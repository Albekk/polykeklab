<?php
if (!defined('APP_ENTRY')) { http_response_code(403); exit; }
final class ArticlesController {
    public function index(): void {
        $articles=db()->query('SELECT id,title FROM articles ORDER BY id ASC')->fetchAll();
        render('articles',['title'=>'Статьи','articles'=>$articles]);
    }
    public function show(string $id): void {
        $statement=db()->prepare('SELECT id,title,body,user_id FROM articles WHERE id=?');
        $statement->execute([$id]); $article=$statement->fetch();
        if (!$article) { http_response_code(404); render('message',['heading'=>'Статья не найдена','message'=>'Такой статьи нет в базе данных.']); return; }
        // Required separate query, deliberately not a JOIN.
        $statement=db()->prepare('SELECT nickname FROM users WHERE id=?');
        $statement->execute([$article['user_id']]); $author=$statement->fetch();
        render('article',['title'=>$article['title'],'article'=>$article,'nickname'=>$author ? $author['nickname'] : 'Автор не найден']);
    }
}
