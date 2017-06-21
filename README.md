# domosfera.kg
domosfera.kg /gallery.kg subservice 

Для з
Для установки, достаточно склонировать проект в локальную директорию

## Требования:
  #### БД(PostgreSQL >= 9.4)
  #### PHP >= 7.1 (драйвер php-pgsql, php-pdo, php-libxml, php-curl)
## Настройка подключения к БД и внесение изменений в нее:  

файл: /app/config/parameters.yml

установка (в командной строке, находясь в директории проекта):

php bin/console doctrine:database:create

php bin/console doctrine:schema:create.

## Запуск проекта и импорт данных:
в командной строке: php bin/console server:start

в окне браузера: http://localhost:8000/update-vendors, http://localhost:8000/update-complect

Верстка будет доступна по ссылке http://localhost:8000/new

Корневой шаблон - /app/Resources/views/smallBase.html.php

Дочерние в директории - /src/AppBundle/Resources/views

Файл стилей - /web/style.css

## Обычная html верстка
/web/dsphere_demo







  
