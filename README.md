# Student List
### Требования
1. Веб-сервер с поддержкой [PHP] *5.3* и выше.
2. База данных [PostgreSQL].
3. [Composer].

### Установка
1. Загрузите файлы приложения в корневой каталог вашего веб-сервера.
2. Сгенерируйте файлы автозагрузки используя composer (**composer dump-autoload**).
3. Импортируйте файл **dump.sql** в свою базу данных (PostgreSQL).
4. Отредактируйте необходимые параметры для подключения к БД (логин, пароль) в файле **config.ini**.

[PHP]: <https://secure.php.net/>
[PostgreSQL]: <http://www.postgresql.org/>
[Composer]: <https://getcomposer.org/>