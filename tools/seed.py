"""Generate fictional classroom contacts; no personal data."""
from pathlib import Path
names = [('Иванов','Иван','Иванович'),('Петров','Пётр','Петрович'),('Сидорова','Анна','Алексеевна'),('Смирнов','Алексей','Олегович'),('Кузнецова','Мария','Игоревна'),('Попов','Дмитрий','Андреевич'),('Васильева','Елена','Петровна'),('Соколов','Максим','Иванович'),('Михайлова','Ольга','Сергеевна'),('Новиков','Артём','Денисович'),('Фёдорова','Ирина','Олеговна'),('Морозов','Кирилл','Ильич'),('Волкова','София','Алексеевна'),('Алексеев','Никита','Романович'),('Лебедева','Полина','Юрьевна'),('Семёнов','Андрей','Максимович'),('Егорова','Дарья','Павловна'),('Павлов','Роман','Дмитриевич'),('Козлова','Вера','Ильинична'),('Степанов','Илья','Олегович'),('Николаева','Алиса','Ивановна'),('Орлов','Денис','Петрович'),('Андреева','Ксения','Сергеевна'),('Макаров','Олег','Романович'),('Захарова','Валерия','Андреевна')]
rows=[]
for i,(surname,name,patronymic) in enumerate(names,1):
    gender='женский' if surname.endswith('а') else 'мужской'
    rows.append(f"('{surname}','{name}','{patronymic}','{gender}','{1995+i%10}-{1+i%12:02d}-{1+i%27:02d}','+7 (000) 000-{i:04d}','Учебная улица, дом {i}','student{i:02d}@example.com','Вымышленный контакт для проверки №{i}')")
sql="-- Fictional sample data. Import once after 01_schema.sql.\nSET NAMES utf8mb4;\nSTART TRANSACTION;\nINSERT INTO contacts (surname,name,patronymic,gender,birth_date,phone,address,email,comment) VALUES\n"+',\n'.join(rows)+';\n'
sql+="INSERT INTO users (nickname) VALUES ('php_student'),('mvc_explorer'),('polytech_author');\n"
for nick,title,body in [('php_student','Первый шаг в PHP','PHP формирует HTML на сервере. Этот блог объединяет маршруты, представления и MySQL.'),('mvc_explorer','Как работает MVC','Роутер выбирает контроллер. Контроллер получает данные и передаёт их представлению.'),('polytech_author','Безопасная работа с базой','PDO prepared statements отделяют SQL от пользовательских данных. Вывод в HTML экранируется.')]:
    sql+=f"INSERT INTO articles (user_id,title,body) SELECT id,'{title}','{body}' FROM users WHERE nickname='{nick}';\n"
sql+='COMMIT;\n'
Path('sql/02_seed.sql').write_text(sql,encoding='utf-8')
