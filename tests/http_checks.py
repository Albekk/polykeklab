"""HTTP integration checks against a running PHP server and fictional test DB."""
import sys, re, urllib.request, urllib.parse, urllib.error, http.cookiejar
base = sys.argv[1].rstrip('/') if len(sys.argv)>1 else 'http://127.0.0.1:8080'
no_db = '--no-db' in sys.argv
client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
passed=0
def get(path, data=None, expected=200):
    request=urllib.request.Request(base+path,data=urllib.parse.urlencode(data).encode() if data is not None else None)
    try: response=client.open(request,timeout=20)
    except urllib.error.HTTPError as ex: response=ex
    body=response.read().decode('utf-8')
    assert response.code==expected,(path,response.code,body[:200])
    assert not re.search(r'(?:Warning:|Fatal error:|Notice:|Deprecated:)',body),body
    return body
def check(condition, label):
    global passed
    assert condition,label
    passed+=1; print('PASS',label)
def token(body): return re.search(r'name="csrf" value="([a-f0-9]+)"',body)[1]
home=get('/')
check(home.count('class="lab-list"')==1 and home.count('Лабораторная №')==5,'five home links')
for path in ['/lab1/index.php','/routing','/templates']:
    check('На главную' in get(path) or 'Все лабораторные' in get(path),'navigation '+path)
form=get('/lab1/index.php')
check('action="https://httpbin.org/post" method="post"' in form and form.count('type="checkbox"')==2,'direct POST feedback and two checkboxes')
check('get_headers()' in get('/lab1/headers.php'),'real headers page handles network failure')
for path,text,title in [('/hello/username','Привет, username','Страница приветствия'),('/bye/username','Пока, username','Мой блог'),('/templates','Шаблоны','Мой блог')]:
    body=get(path); check(text in body and '<title>'+title+'</title>' in body,'route and title '+path)
check('&lt;script&gt;' in get('/hello/%3Cscript%3E'),'route output escaped')
get('/not-found',expected=404); get('/hello/name',{},405)
check(True,'404 and 405 responses')
for path in ['/lab2/menu.php','/lab2/viewer.php','/lab2/add.php','/lab2/edit.php','/lab2/delete.php','/lab2/config.php','/config.php','/sql/01_schema.sql','/app/Router.php']:
    get(path,expected=403)
check(True,'modules and sensitive files protected')
if not no_db:
    for page,rows in [(1,10),(2,10),(3,5)]:
        body=get('/lab2/index.php?page='+str(page)); check(body.count('<tr>')-1==rows,'pagination '+str(page))
    for sort in ['id','surname','birth_date']:
        body=get('/lab2/index.php?sort='+sort)
        check(body.count('class="active"')>=3,'active menus '+sort)
        values=re.findall(r'<tr>(.*?)</tr>',body,re.S)[1:]
        cells=[re.findall(r'<td>(.*?)</td>',row,re.S) for row in values]
        col={'id':0,'surname':1,'birth_date':5}[sort]
        items=[int(row[col]) if sort=='id' else row[col] for row in cells]
        check(items==sorted(items),'ascending sort '+sort)
    data=dict(surname='Тестовый',name='Интеграция',patronymic='Проверка',gender='не указан',birth_date='2000-01-01',phone='+7 000 123 4567',address='<script>alert(1)</script>',email='test@example.com',comment='HTTP test')
    body=get('/lab2/index.php?action=add')
    invalid=data|{'email':'bad','csrf':token(body)}
    check('Ошибка: запись не добавлена' in get('/lab2/index.php?action=add',invalid),'invalid email rejected')
    check('Ошибка: запись не добавлена' in get('/lab2/index.php?action=add',data|{'csrf':'invalid'}),'CSRF rejected')
    body=get('/lab2/index.php?action=add')
    check('Запись добавлена' in get('/lab2/index.php?action=add',data|{'csrf':token(body)}),'contact added')
    body=get('/lab2/index.php?action=edit')
    match=re.search(r'action=edit&amp;id=(\d+)">Тестовый Интеграция',body); assert match,body
    ident=match[1]; path='/lab2/index.php?action=edit&id='+ident
    body=get(path)
    check('&lt;script&gt;' in body and 'value="Тестовый"' in body,'edit prefilled and escaped')
    check('Запись сохранена' in get(path,data|{'name':'Обновлённый','csrf':token(body)}),'contact updated')
    body=get('/lab2/index.php?action=delete')
    href=re.search(r'href="([^"]*id='+ident+r'&amp;token=[^"]+)"',body)[1].replace('&amp;','&')
    relative=href[len(urllib.parse.urlparse(base).path):] if urllib.parse.urlparse(base).path else href
    check('Запись с фамилией Тестовый удалена' in get(relative),'contact deleted')
    get('/lab2/index.php?action=delete&id=1',expected=403)
    check(True,'delete without token rejected')
    body=get('/articles'); check(body.count('<article>')==3,'three seeded articles')
    for ident,nickname in [(1,'php_student'),(2,'mvc_explorer'),(3,'polytech_author')]:
        check('Автор: '+nickname in get('/articles/'+str(ident)),'author nickname '+str(ident))
    get('/articles/999999',expected=404); check(True,'missing article 404')
print(f'{passed} checks passed')
